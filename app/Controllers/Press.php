<?php

namespace App\Controllers;

/**
 * Press controller — Basın Odası (trafik motorları planının §7.10 maddesi).
 *
 * Marka varlıkları public/assets/brand/ klasöründen OTOMATİK derlenir
 * (dosya-bırak-çalışsın): yeni logo/görsel eklemek = dosyayı klasöre
 * atmak, kod değişikliği gerekmez.
 */
class Press extends BaseController
{
    public function index(): string
    {
        $this->cachePage(3600);

        $assets = [];

        foreach (glob(FCPATH . 'assets/brand/*.{png,svg,jpg,jpeg,webp}', GLOB_BRACE) ?: [] as $file) {
            $name = basename($file);
            $size = @getimagesize($file);

            $assets[] = [
                'file'  => $name,
                'url'   => base_url('assets/brand/' . $name),
                'label' => ucwords(str_replace(['-', '_'], ' ', pathinfo($name, PATHINFO_FILENAME))),
                'dim'   => is_array($size) ? $size[0] . ' × ' . $size[1] : strtoupper(pathinfo($name, PATHINFO_EXTENSION)),
                'kb'    => (int) round(filesize($file) / 1024),
            ];
        }

        usort($assets, static fn (array $a, array $b): int => strcmp($a['file'], $b['file']));

        return view('pages/press/index', [
            'metaTitle'       => lang('Press.meta_title'),
            'metaDescription' => lang('Press.meta_description'),
            'assets'          => $assets,
        ]);
    }
}
