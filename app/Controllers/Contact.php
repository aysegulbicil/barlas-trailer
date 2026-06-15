<?php

namespace App\Controllers;

/**
 * Contact controller
 *
 * index()  — renders the localized contact page (uses layouts/yeni).
 * submit() — validates the enquiry, ALWAYS persists it as a lead (so a
 *            message is never lost even before SMTP is configured), then
 *            sends a notification e-mail to the sales inbox as a best-effort
 *            layer. Responds with JSON for the AJAX (fetch) flow and falls
 *            back to a redirect + flashdata for no-JS submissions.
 *
 * NOTE — e-posta teslimi için: app/Config/Email.php (veya .env) içinde
 * protocol = 'smtp' ve SMTP bilgilerini doldurun. Doldurulana kadar her
 * başvuru writable/leads/YYYY-MM.jsonl dosyasına yazılır; form yine çalışır.
 */
class Contact extends BaseController
{
    /** Satış kutusu — bildirimler buraya gider / yanıtlar buradan döner. */
    private const INBOX = 'info@barlastrailer.com';

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
            'consent' => 'required',
        ];
        $messages = [
            'name'    => ['required' => lang('Contact.err_name'),    'min_length' => lang('Contact.err_name')],
            'email'   => ['required' => lang('Contact.err_email'),   'valid_email' => lang('Contact.err_email')],
            'message' => ['required' => lang('Contact.err_message'), 'min_length' => lang('Contact.err_message')],
            'consent' => ['required' => lang('Contact.err_consent')],
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
        $this->notify($lead);        // en iyi çaba: e-posta (başarısızlık ölümcül değil)

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

            $email = service('email');
            $email->setFrom(self::INBOX, lang('Common.site_name'));
            $email->setTo(self::INBOX);
            if (filter_var($lead['email'], FILTER_VALIDATE_EMAIL)) {
                $email->setReplyTo($lead['email'], $lead['name']);
            }
            $email->setSubject('[Web] ' . $subject . ' — ' . $lead['name']);
            $email->setMessage($body);

            if (! $email->send(false)) {
                log_message('error', '[contact] e-posta gönderilemedi: ' . $email->printDebugger(['headers']));
            }
        } catch (\Throwable $e) {
            log_message('error', '[contact] e-posta istisnası: ' . $e->getMessage());
        }
    }
}
