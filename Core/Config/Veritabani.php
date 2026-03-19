<?php

defined('ERISIM') or exit('401 Unauthorized');
/*
|--------------------------------------------------------------------------
| Veritabanı ayarları
|--------------------------------------------------------------------------
|
| Bağlantı için gerekli bilgiler.
|
*/
define('VT_HOST', Env::Sec('VERITABANI_HOST'));
define('VT_PORT', Env::Sec('VERITABANI_PORT'));
define('VT_ADI', Env::Sec('VERITABANI_AD'));
define('VT_KULLANICI', Env::Sec('VERITABANI_KULLANICI'));
define('VT_SIFRE', Env::Sec('VERITABANI_SIFRE'));
