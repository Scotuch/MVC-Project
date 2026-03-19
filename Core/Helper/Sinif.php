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
 * Tema oluşturmak için kullanılır.
 *
 * Bu fonksiyon, Tema sınıfını oluşturur ve Tema nesnesini döndürür.
 *
 * @param string $a Tema ismi
 * @param array $b Tema dizini
 * @param array $c Tema yapılandırma dizisi
 * @param boolean $d Tema onbellekli oluşturulur mu
 * @return Tema
 */
function Tema($baslik = '', $css = [], $js = [], $oto_yukleme = true)
{
    return new Tema($baslik, $css, $js, $oto_yukleme);
}


/**
 * Veritabanı bağlantısı sağlar.
 *
 * Bu fonksiyon, veritabanı bağlantısını başlatır ve bir Veritabani nesnesi döndürür.
 * 
 * @return Veritabani Veritabanı bağlantı nesnesi
 */
function Veritabani()
{
    return new Veritabani();
}
