<?php

namespace App\Controllers;

/**
 * Contact controller
 *
 * index()    — renders the localized contact page (uses layouts/yeni).
 * submit()   — validates the enquiry, ALWAYS persists it as a lead (so a
 *              message is never lost even before SMTP is configured), then
 *              (1) notifies the sales inbox and (2) sends an automatic
 *              confirmation ("auto-reply") to the visitor — both best-effort.
 *              Responds with JSON for the AJAX (fetch) flow and falls back to
 *              a redirect + flashdata for no-JS submissions.
 *
 * NOTE — e-posta teslimi için: .env (veya app/Config/Email.php) içinde
 * email.protocol = smtp ve SMTP bilgilerini doldurun. Doldurulana kadar her
 * başvuru writable/leads/YYYY-MM.jsonl dosyasına yazılır; form yine çalışır.
 */
class Contact extends BaseController
{
    /**
     * Satış kutusu — bildirimler buraya gider / yanıtlar buradan döner.
     * ŞİMDİLİK: gerçek bir alan adı postası (info@barlastrailer.com) henüz
     * olmadığı için bildirimler test Gmail'ine yönlendirildi. Alan adı maili
     * hazır olunca bu satırı tek başına değiştirmen yeterli.
     */
    private const INBOX = 'aysegullbicill@gmail.com';

    public function index(): string
    {
        $data = [
            'metaTitle'       => lang('Contact.meta_title'),
            'metaDescription' => lang('Contact.meta_description'),
        ];

        return view('pages/contact', $data);
    }

    public function submit()
    {
        $wantsJson = $this->request->isAJAX()
            || str_contains((string) $this->request->getHeaderLine('Accept'), 'application/json');

        // Bal kabı: botlar gizli "website" alanını doldurur; insanlar görmez.
        if (trim((string) $this->request->getPost('website')) !== '') {
            return $this->respondOk($wantsJson); // sessizce başarılı görün, at
        }

        $rules = [
            'name'    => 'required|min_length[2]|max_length[120]',
            'email'   => 'required|valid_email|max_length[180]',
            'phone'   => 'permit_empty|max_length[40]',
            'company' => 'permit_empty|max_length[160]',
            'subject' => 'permit_empty|max_length[140]',
            'message' => 'required|min_length[10]|max_length[4000]',
        ];
        $messages = [
            'name'    => ['required' => lang('Contact.err_name'),    'min_length' => lang('Contact.err_name')],
            'email'   => ['required' => lang('Contact.err_email'),   'valid_email' => lang('Contact.err_email')],
            'message' => ['required' => lang('Contact.err_message'), 'min_length' => lang('Contact.err_message')],
        ];

        if (! $this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();

            if ($wantsJson) {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok'      => false,
                    'errors'  => $errors,
                    'message' => lang('Contact.form_error'),
                ]);
            }

            return redirect()->back()->withInput()
                ->with('errors', $errors)
                ->with('form_error', lang('Contact.form_error'));
        }

        $lead = [
            'time'    => date('c'),
            'locale'  => current_locale(),
            'name'    => trim((string) $this->request->getPost('name')),
            'email'   => trim((string) $this->request->getPost('email')),
            'phone'   => trim((string) $this->request->getPost('phone')),
            'company' => trim((string) $this->request->getPost('company')),
            'subject' => trim((string) $this->request->getPost('subject')),
            'message' => trim((string) $this->request->getPost('message')),
            'ip'      => $this->request->getIPAddress(),
            'agent'   => $this->request->getUserAgent()->getAgentString(),
        ];

        $this->persistLead($lead);   // her zaman: kayıt kaynağı
        $this->notify($lead);        // en iyi çaba: satış kutusuna bildirim
        $this->autoReply($lead);     // en iyi çaba: kullanıcıya otomatik onay

        return $this->respondOk($wantsJson);
    }

    /** Başarılı yanıt — AJAX'ta JSON, aksi halde flashdata ile yönlendirme. */
    private function respondOk(bool $wantsJson)
    {
        if ($wantsJson) {
            return $this->response->setJSON([
                'ok'      => true,
                'message' => lang('Contact.form_success'),
                // CSRF açıkken bir sonraki gönderim için tokenı tazele
                'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        return redirect()->to(locale_url('contact'))
            ->with('form_success', lang('Contact.form_success'));
    }

    /** Başvuruyu aylık JSONL dosyasına ekler (writable/leads). */
    private function persistLead(array $lead): void
    {
        $dir = WRITEPATH . 'leads';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $line = json_encode($lead, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($line !== false) {
            @file_put_contents($dir . '/' . date('Y-m') . '.jsonl', $line . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    /** Satış kutusuna bildirim e-postası (yapılandırılmamışsa loglanır). */
    private function notify(array $lead): void
    {
        try {
            $subject = $lead['subject'] !== '' ? $lead['subject'] : lang('Contact.email_subject');

            $body = lang('Contact.email_intro') . "\n"
                . str_repeat('-', 48) . "\n"
                . lang('Contact.f_name')    . ': ' . $lead['name']    . "\n"
                . lang('Contact.f_email')   . ': ' . $lead['email']   . "\n"
                . lang('Contact.f_phone')   . ': ' . ($lead['phone']   ?: '—') . "\n"
                . lang('Contact.f_company') . ': ' . ($lead['company'] ?: '—') . "\n"
                . lang('Contact.f_subject') . ': ' . ($lead['subject'] ?: '—') . "\n"
                . str_repeat('-', 48) . "\n"
                . lang('Contact.f_message') . ":\n" . $lead['message'] . "\n"
                . str_repeat('-', 48) . "\n"
                . 'Locale: ' . $lead['locale'] . '  |  IP: ' . $lead['ip'] . '  |  ' . $lead['time'] . "\n";

            $email = service('email', null, false);
            $email->setFrom(self::INBOX, lang('Common.site_name'));
            $email->setTo(self::INBOX);
            if (filter_var($lead['email'], FILTER_VALIDATE_EMAIL)) {
                $email->setReplyTo($lead['email'], $lead['name']);
            }
            $email->setSubject('[Web] ' . $subject . ' — ' . $lead['name']);
            $email->setMessage($body);

            if (! $email->send()) {
                log_message('error', '[contact] bildirim e-postası gönderilemedi: ' . $email->printDebugger(['headers']));
            }
        } catch (\Throwable $e) {
            log_message('error', '[contact] bildirim e-postası istisnası: ' . $e->getMessage());
        }
    }

    /**
     * Kullanıcıya otomatik onay (auto-reply) e-postası — en iyi çaba.
     *
     * Ziyaretçinin formda girdiği e-posta adresine, gezindiği dilde
     * (lead['locale']) "bilgileriniz alınmıştır, en kısa sürede dönüş
     * yapılacaktır" mesajını gönderir. SMTP yapılandırılmamışsa sessizce
     * loglanır; form akışı yine başarıyla tamamlanır.
     */
    private function autoReply(array $lead): void
    {
        // Geçersiz adrese gönderme deneme.
        if (! filter_var($lead['email'], FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            $locale   = $lead['locale'] ?: 'tr';
            $siteName = lang('Common.site_name', [], $locale);

            // Özet satırları — alan etiketleri kullanıcının diliyle.
            $rows = [
                lang('Contact.f_name',    [], $locale) => $lead['name'],
                lang('Contact.f_email',   [], $locale) => $lead['email'],
                lang('Contact.f_phone',   [], $locale) => $lead['phone']   ?: '—',
                lang('Contact.f_company', [], $locale) => $lead['company'] ?: '—',
                lang('Contact.f_subject', [], $locale) => $lead['subject'] ?: '—',
                lang('Contact.f_message', [], $locale) => $lead['message'],
            ];

            $html = view('emails/autoreply', [
                'locale'       => $locale,
                'siteName'     => $siteName,
                'preheader'    => lang('Contact.autoreply_preheader', [], $locale),
                'greeting'     => lang('Contact.autoreply_greeting', [$lead['name']], $locale),
                'intro'        => lang('Contact.autoreply_intro', [], $locale),
                'summaryTitle' => lang('Contact.autoreply_summary_title', [], $locale),
                'rows'         => $rows,
                'closing'      => lang('Contact.autoreply_closing', [], $locale),
                'regards'      => lang('Contact.autoreply_regards', [], $locale),
                'team'         => lang('Contact.autoreply_team', [], $locale),
                'autoNote'     => lang('Contact.autoreply_auto_note', [], $locale),
            ]);

            // HTML görüntülenemeyen istemciler için düz metin alternatifi.
            $alt = lang('Contact.autoreply_greeting', [$lead['name']], $locale) . "\n\n"
                . lang('Contact.autoreply_intro', [], $locale) . "\n\n"
                . lang('Contact.autoreply_summary_title', [], $locale) . ":\n";
            foreach ($rows as $label => $value) {
                $alt .= '• ' . $label . ': ' . $value . "\n";
            }
            $alt .= "\n" . lang('Contact.autoreply_closing', [], $locale) . "\n\n"
                . lang('Contact.autoreply_regards', [], $locale) . "\n"
                . lang('Contact.autoreply_team', [], $locale);

            // İzole (paylaşılmayan) örnek: notify() durumundan etkilenmez.
            $email = service('email', null, false);
            $email->setFrom(self::INBOX, $siteName);
            $email->setTo($lead['email']);
            $email->setReplyTo(self::INBOX, $siteName);
            $email->setSubject(lang('Contact.autoreply_subject', [], $locale));
            $email->setMailType('html');
            $email->setMessage($html);
            $email->setAltMessage($alt);

            if (! $email->send()) {
                log_message('error', '[contact] onay e-postası gönderilemedi: ' . $email->printDebugger(['headers']));
            }
        } catch (\Throwable $e) {
            log_message('error', '[contact] onay e-postası istisnası: ' . $e->getMessage());
        }
    }
}
