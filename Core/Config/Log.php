<?php
defined('ERISIM') or exit('401 Unauthorized');
/*
|--------------------------------------------------------------------------
| Log ayarları
|--------------------------------------------------------------------------
|
| Log dosyalarının boyutu ve arşiv limitleri.
|
*/
define('LOG_BOYUTU', 2);  // MB cinsinden - Log dosyası maksimum boyutu
define('LOG_SINIR', 25);  // Maksimum arşiv dosya sayısı