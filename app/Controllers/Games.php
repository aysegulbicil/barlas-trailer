<?php

namespace App\Controllers;

/**
 * Games controller — Bekleme Salonu (ekosistem planı §8).
 *
 * Tamamı istemci tarafı çalışan oyunlar: hub (kahve molası ekranı),
 * Treyler Tetris ve Günün Sorusu. Metinler ve soru bankası
 * app/Language/{locale}/Games.php dosyalarından gelir; skorlar yalnız
 * ziyaretçinin cihazında (localStorage) tutulur — sunucuda kayıt yok.
 * Sadakat puanına dönüşüm, hesap sistemi (§4.3) geldiğinde bağlanacak.
 */
class Games extends BaseController
{
    public function index(): string
    {
        $this->cachePage(3600);

        return view('pages/games/index', [
            'metaTitle'       => lang('Games.meta_title'),
            'metaDescription' => lang('Games.meta_description'),
        ]);
    }

    public function tetris(): string
    {
        $this->cachePage(3600);

        return view('pages/games/tetris', [
            'metaTitle'       => lang('Games.t_meta_title'),
            'metaDescription' => lang('Games.meta_description'),
        ]);
    }

    public function daily(): string
    {
        $this->cachePage(3600);

        $bank = lang('Games.bank');

        return view('pages/games/daily', [
            'metaTitle'       => lang('Games.d_meta_title'),
            'metaDescription' => lang('Games.meta_description'),
            'bank'            => is_array($bank) ? $bank : [],
        ]);
    }
}
