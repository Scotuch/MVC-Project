<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Uygulama Temel Ayarları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/*
|--------------------------------------------------------------------------
| Route ayarları
|--------------------------------------------------------------------------
|
| Varsayılan controller, method ve bulunamayan controller ayarları.
|
*/
define('VARSAYILAN_CONTROLLER', 'Anasayfa');
define('VARSAYILAN_METHOD', 'index');

/*
|--------------------------------------------------------------------------
| Sistem Klasör Ayarları
|--------------------------------------------------------------------------
|
| Sistem klasör yapısı için gerekli sabit değişkenler."
|
*/
define('SISTEM_KLASOR_CONFIG', SISTEM . 'Config' . DS);
define('SISTEM_KLASOR_HELPER', SISTEM . 'Helper' . DS);
define('SISTEM_KLASOR_LIBRARY', SISTEM . 'Library' . DS);
define('SISTEM_KLASOR_VIEW', SISTEM . 'View' . DS);

/*
|--------------------------------------------------------------------------
| Uygulama Klasör Ayarları
|--------------------------------------------------------------------------
|
| Sistem klasör yapısı için gerekli sabit değişkenler."
|
*/
define('KLASOR_API', UYGULAMA . 'Api' . DS);
define('KLASOR_BACKUP', UYGULAMA . 'Backup' . DS);
define('KLASOR_CACHE', UYGULAMA . 'Cache' . DS);
define('KLASOR_CONFIG', UYGULAMA . 'Config' . DS);
define('KLASOR_CONTROLLER', UYGULAMA . 'Controller' . DS);
define('KLASOR_DATABASE', UYGULAMA . 'Database' . DS);
define('KLASOR_HELPER', UYGULAMA . 'Helper' . DS);
define('KLASOR_LIBRARY', UYGULAMA . 'Library' . DS);
define('KLASOR_LOG', UYGULAMA . 'Log' . DS);
define('KLASOR_MODEL', UYGULAMA . 'Model' . DS);
define('KLASOR_OTHER', UYGULAMA . 'Other' . DS);
define('KLASOR_PARTIAL', UYGULAMA . 'Partial' . DS);
define('KLASOR_SERVICE', UYGULAMA . 'Service' . DS);
define('KLASOR_TEMP', UYGULAMA . 'Temp' . DS);
define('KLASOR_VIEW', UYGULAMA . 'View' . DS);

/*
|--------------------------------------------------------------------------
| Önbellek ayarları
|--------------------------------------------------------------------------
|
| Önbellek dosyalarının geçerlilik süresi ve temizleme ayarları.
|
*/
define('ONBELLEK_OTO_TEMIZLEME_SAAT', 168); // Saat cinsinden - Önbellek geçerlilik süresi
