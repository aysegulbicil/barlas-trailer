<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * İç araç bekçisi (/fatura, /teklif, ileride /qr) — eski Apache
 * auto_prepend_file bekçisinin (apps/_shared/auth/guard.php) CI4 karşılığı.
 *
 * Kimlik: /patron girişi CI4 oturumuna `apps_user` yazar (App\Controllers\Patron).
 * Girişsiz istek login'e yönlenir; API çağrıları yönlendirme izleyemeyeceği
 * için JSON 401 alır.
 */
class AppsAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('apps_user')) {
            return null; // girişli — istenen rotaya devam
        }

        $uri = '/' . ltrim($request->getUri()->getPath(), '/');

        $wantsJson = str_contains($uri, '/server/api/')
            || str_contains($request->getHeaderLine('Accept'), 'application/json');

        if ($wantsJson) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['ok' => false, 'error' => 'auth_required']);
        }

        return redirect()->to(site_url('patron/login') . '?next=' . rawurlencode($uri));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // sonrası yok
    }
}
