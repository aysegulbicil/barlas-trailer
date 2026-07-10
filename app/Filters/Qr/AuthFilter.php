<?php

namespace App\Filters\Qr;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Requires a signed-in QR user. Remembers the intended URL so the user
 * is returned there after logging in (used by the QR scan flow).
 * Filters run before controllers, so QR paths are built with site_url('qr/...')
 * instead of the qr helper.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_id')) {
            helper('url');
            session()->set('redirect_url', current_url());

            return redirect()->to(site_url('qr/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
