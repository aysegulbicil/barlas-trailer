<?php

namespace App\Filters\Qr;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Requires a signed-in QR user with the "admin" role.
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_id')) {
            return redirect()->to(site_url('qr/login'));
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to(site_url('qr/dashboard'))->with('error', 'Admin access is required.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
