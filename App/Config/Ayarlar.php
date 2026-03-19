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
| Assets Klasörleri
|--------------------------------------------------------------------------
|
| Assets klasörlerinin yolları.
*/
define('ASSETS', TEMEL_DIZIN . 'assets/');
define('CSS', ASSETS . 'css/');
define('JS', ASSETS . 'js/');
define('FONTS', ASSETS . 'fonts/');
define('IMG', ASSETS . 'img/');
define('SVG', IMG . 'svg/');
define('UPLOAD', IMG . 'upload/');

define('KLASOR_ASSETS', ROOT . 'assets' . DS);
define('KLASOR_CSS', KLASOR_ASSETS . 'css' . DS);
define('KLASOR_JS', KLASOR_ASSETS . 'js' . DS);
define('KLASOR_FONTS', KLASOR_ASSETS . 'fonts' . DS);
define('KLASOR_IMG', KLASOR_ASSETS . 'img' . DS);
define('KLASOR_SVG', KLASOR_IMG . 'svg' . DS);
define('KLASOR_UPLOAD', KLASOR_IMG . 'upload' . DS);
