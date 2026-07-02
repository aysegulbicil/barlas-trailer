<?php

namespace App\Commands\Agents;

/**
 * Sahte Site Avcısı (§9) — markayı taklit eden alan adlarını tarar, saf kod.
 *
 * barlastrailer.com'un yazım-hatası (typosquat) varyantlarını üretir ve
 * DNS'te kayıtlı olup olmadıklarına bakar (checkdnsrr A/MX). Yeni kayıtlı
 * bir taklit bulunursa dikkat kuyruğuna alarm düşer; karar (UDRP, avukat,
 * izleme) Ana Admin'indir — ajan yalnız istihbarat üretir (onay ilkesi).
 *
 * Durum dosyası bilinen kayıtlıları tutar: aynı alan için alarm bir kez
 * üretilir. DNS sorgusu dışında ağ trafiği yok; API maliyeti sıfır.
 *
 * cron önerisi (her Pazar 05:00):
 *   0 5 * * 0  cd /var/www/html && php spark agents:domain-watch
 */
class DomainWatchAgent extends AgentCommand
{
    protected $name        = 'agents:domain-watch';
    protected $description = 'Markayı taklit eden (typosquat) alan adlarını DNS üzerinden tarar.';

    private const BRAND       = 'barlastrailer';
    private const HOME_DOMAIN = 'barlastrailer.com';

    /** Marka adının aynen denendiği alternatif uzantılar. */
    private const ALT_TLDS = ['net', 'org', 'co', 'info', 'com.tr'];

    /** Yazım-hatası varyantlarının denendiği uzantılar. */
    private const TYPO_TLDS = ['com', 'com.tr'];

    /** Tek koşuda en fazla DNS sorgusu (cron süresi sınırlı kalsın). */
    private const MAX_LOOKUPS = 150;

    protected function agentName(): string
    {
        return 'domain-watch';
    }

    protected function work(): array
    {
        $state = $this->loadState();
        $known = $state['registered'] ?? [];

        $checked    = 0;
        $registered = 0;
        $new        = 0;

        foreach ($this->candidates() as $domain) {
            if ($checked >= self::MAX_LOOKUPS) {
                break;
            }
            $checked++;

            if (! $this->isRegistered($domain)) {
                continue;
            }
            $registered++;

            if (! isset($known[$domain])) {
                $known[$domain] = ['first_seen' => date('c')];
                $new++;
                $this->alert('typosquat-found', sprintf(
                    'Markaya benzer KAYITLI alan adı bulundu: %s — içeriğini kontrol edin; taklitse UDRP/avukat seçeneğini değerlendirin.',
                    $domain
                ));
            }
        }

        $state['registered'] = $known;
        $state['last_run']   = date('c');
        $this->saveState($state);

        return [
            'checked'     => $checked,
            'registered'  => $registered,
            'new_found'   => $new,
            'known_total' => count($known),
        ];
    }

    /**
     * Aday alan adları: marka adı alternatif uzantılarda + yazım-hatası
     * varyantları ana uzantılarda. Meşru ana alan adı listeye girmez.
     *
     * @return list<string>
     */
    private function candidates(): array
    {
        $domains = [];

        foreach (self::ALT_TLDS as $tld) {
            $domains[] = self::BRAND . '.' . $tld;
        }

        foreach ($this->typoVariants(self::BRAND) as $variant) {
            foreach (self::TYPO_TLDS as $tld) {
                $domains[] = $variant . '.' . $tld;
            }
        }

        $domains = array_values(array_unique($domains));

        return array_values(array_filter(
            $domains,
            static fn (string $d): bool => $d !== self::HOME_DOMAIN
        ));
    }

    /**
     * Klasik typosquat üreteci: harf atlama, komşu takas, harf çiftleme,
     * görsel benzer karakter ve tireleme.
     *
     * @return list<string>
     */
    private function typoVariants(string $name): array
    {
        $variants = ['barlas-trailer', 'barlastrailers', 'barlastreyler'];
        $len      = strlen($name);

        for ($i = 0; $i < $len; $i++) {
            // Harf atlama: "barlastailer"
            $variants[] = substr($name, 0, $i) . substr($name, $i + 1);

            // Harf çiftleme: "barllastrailer"
            $variants[] = substr($name, 0, $i + 1) . $name[$i] . substr($name, $i + 1);

            // Komşu takas: "barlastariler"
            if ($i < $len - 1) {
                $swapped          = $name;
                $swapped[$i]      = $name[$i + 1];
                $swapped[$i + 1]  = $name[$i];
                $variants[]       = $swapped;
            }
        }

        // Görsel benzer karakterler: l→1, i→1, o→0, s→5, a→4
        foreach (['l' => '1', 'i' => '1', 'o' => '0', 's' => '5', 'a' => '4'] as $from => $to) {
            if (str_contains($name, $from)) {
                $variants[] = str_replace($from, $to, $name);
            }
        }

        // Geçersiz/aynı olanları ele.
        return array_values(array_unique(array_filter(
            $variants,
            static fn (string $v): bool => $v !== $name && preg_match('/^[a-z0-9-]{4,}$/', $v) === 1
        )));
    }

    /** A veya MX kaydı olan alan "kayıtlı" sayılır. */
    private function isRegistered(string $domain): bool
    {
        // Sondaki nokta: yerel arama alanı (search domain) eklenmesini önler.
        return @checkdnsrr($domain . '.', 'A') || @checkdnsrr($domain . '.', 'MX');
    }

    private function statePath(): string
    {
        return WRITEPATH . 'data/agents/domain-watch/state.json';
    }

    private function loadState(): array
    {
        $state = json_decode((string) @file_get_contents($this->statePath()), true);

        return is_array($state) ? $state : [];
    }

    private function saveState(array $state): void
    {
        $dir = dirname($this->statePath());
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($this->statePath(), json_encode($state, JSON_PRETTY_PRINT) . "\n", LOCK_EX);
    }
}
