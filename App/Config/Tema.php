<?php

defined('ERISIM') or exit('401 Unauthorized');
/*
|--------------------------------------------------------------------------
| Tema ayarları
|--------------------------------------------------------------------------
|
| Uygulamanızın tema ayarları.
|
*/

define('TEMA_BASLIK', 'Proje Başlığı');
define('TEMA_BASLIK_AYRAC', '|');
define('TEMA_ACIKLAMA', 'Proje Açıklaması');
define('TEMA_ANAHTAR_KELIMELER', 'proje, anahtar, kelimeler, mvc, php framework, ');
define('TEMA_SAHIP_AD', 'Scotuch');
define('TEMA_SAHIP_EMAIL', 'samedcimen@hotmail.com');
define('TEMA_SAHIP_URL', 'https://github.com/Scotuch');
define('TEMA_RENK', '#018786');
define('TEMA_IKON', 'https://www.php.net/favicon.ico');
define('TEMA_YILI', '2026');
define('TEMA_VERSIYON', '1.0.0');


define('TEMA_ONBELLEK', true);
define('TEMA_CSS', [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
]);
define('TEMA_JS', [
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11',
    'script.bundle',
    'script'
]);

define('TEMA_DEGISKENLER', [
    'DIR' => TEMEL_DIZIN,
    'API' => true,
    'API_URL' => Url_Olustur('api'),
    'DOKUMAN_URL' => Url_Olustur('dokuman'),
    'GELISTIRICI' => GELISTIRICI,
    'TOKEN' => Sifrele(ANAHTAR . "-" . time()),
    'ZAMAN' => time()
]);

define('TEMA_SCRIPT_MANIFEST', '');
define('TEMA_SCRIPT_SW', 'sw.js');

define('TEMA_GOOGLE_RECAPTCHA', '');
define('TEMA_GOOGLE_SITE_DOGRULAMA', '');
define('TEMA_GOOGLE_TAG', '');
