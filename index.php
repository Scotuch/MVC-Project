<?php

/**
 * Proje Adı | Proje Açıklaması
 *
 * MVC [Model-View-Controller] mimarisi kullanılarak geliştirilmiştir.
 *
 * @author Scotuch <samedcimen@hotmail.com>
 * @copyright 2026 Samed CIMEN
 * @license MIT License https://opensource.org/licenses/MIT
 * @link https://github.com/Scotuch/
 * @version 1.0.0
 * @package PHP
 * @subpackage MVC
 */

/*
|--------------------------------------------------------------------------
| Sabit Değişkenler
|--------------------------------------------------------------------------
|
| Öncelikle projemiz için gerekli tüm değişkenleri belirleyelim.
|
| 1 - Bilinen tüm hataları gizle.
| 2 - Zaman dilimini belirle.
| 3 - Azami çalıştırma süresini belirle.
| 4 - Gerekli global değişkenler.
|
*/
error_reporting(0);
date_default_timezone_set('Europe/Istanbul');
set_time_limit(30);
ob_start();

define('DS', DIRECTORY_SEPARATOR);
define('BASLANGIC', microtime(true));
define('ERISIM', true);
define('GELISTIRICI', true);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('TEMEL_DIZIN', rtrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__)), '/') . '/');
define('SISTEM', ROOT . 'Core' . DS);
define('UYGULAMA', ROOT . 'App' . DS);

/*
|--------------------------------------------------------------------------
| Çalıştır
|--------------------------------------------------------------------------
|
| Sistem dosyalarını dahil et ve uygulamayı başlat.
|
*/
try {
    require_once SISTEM . 'Core.php';
    $Uygulama = new Uygulama();
} catch (Error | Exception $sistem_hata) {
    (class_exists('Log')) ? Log::Hata(0, 'Index.php kritik hatası: ' . $sistem_hata->getMessage() . ' - File: ' . $sistem_hata->getFile() . ' - Line: ' . $sistem_hata->getLine(),) : null;
    (function_exists('Hata')) ? Hata($sistem_hata) : exit('401 Not Authorized');
}
