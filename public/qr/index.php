<?php

/*
 * /qr köprüsü — public/qr fiziksel bir dizin olduğundan (assets burada)
 * Apache "GET /qr/" isteğini CI4'ün ön denetleyicisine hiç sokmaz
 * (RewriteCond !-d dizinde atlar) ve DirectoryIndex olmadan 403 verirdi.
 * Bu dosya, isteği uygulamanın QR giriş rotasına devreder.
 * (public/teklif/index.php ile aynı desen.)
 */

declare(strict_types=1);

header('Location: /qr/login', true, 302);
exit;
