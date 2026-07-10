<?php

/*
 * Barlas Teklif Sistemi — root bounce to the SPA shell.
 *
 * Why this file exists: public/teklif/ is a real directory, so Apache never
 * rewrites GET /teklif into CodeIgniter (the .htaccess front-controller rule
 * skips existing dirs; mod_dir would otherwise answer 403). DirectoryIndex
 * picks this file up instead and it mirrors apps/teklif/index.php 1:1.
 * /teklif/index.php resolves here as a real file too — same bounce.
 */
header('Location: ui/index.html');
exit;
