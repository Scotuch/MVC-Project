<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Sınıf fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * cURL işlemleri için kullanılır.
 *
 * Bu fonksiyon, cURL sınıfını oluşturur ve cURL nesnesini döndürür.
 *
 * @return cURL cURL nesnesi
 */
function cURL()
{
    Sistem::Dosya(KLASOR_LIBRARY, 'cURL');
    return new cURL();
}
