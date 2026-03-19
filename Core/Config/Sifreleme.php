<?php

defined('ERISIM') or exit('401 Unauthorized');
/*
|--------------------------------------------------------------------------
| Şifreleme ayarları
|--------------------------------------------------------------------------
|
| Varsayılan şifreleme ayarları.
|
*/

define('ANAHTAR', Env::Sec('ANAHTAR'));
define('SIFRELEME_YONTEMI', Env::Sec('SIFRELEME_YONTEMI'));
define('JSON_OPTIONS', Env::Sec('JSON_OPTIONS'));
