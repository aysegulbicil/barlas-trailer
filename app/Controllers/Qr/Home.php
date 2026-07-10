<?php

namespace App\Controllers\Qr;

class Home extends BaseController
{
    public function index(): string
    {
        return view('qr/welcome_message');
    }
}
