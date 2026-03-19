<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Kelime fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Türkçe Büyük Harf Başlatıcı
 *
 * Bu fonksiyon, verilen string içindeki her kelimenin ilk harfini Türkçe karakter uyumlu şekilde büyük harfe çevirir.
 * Standart ucwords fonksiyonunun Türkçe karakterler için eksik kaldığı durumlarda kullanılır.
 *
 * Kullanım Alanı:
 * - Başlık, isim, adres gibi metinlerin düzgün formatlanması
 * - Form verilerinin işlenmesi
 * - Arayüzde düzgün metin gösterimi
 *
 * Teknik Detaylar:
 * - UTF-8 desteği ile çalışır
 * - Türkçe karakterlerin ilk harfini doğru şekilde büyük yapar (Ç, Ş, Ü, Ö, Ğ, I)
 * - Diğer harfleri değiştirmez
 *
 * Kullanım Örneği:
 * $metin = ucwords_tr("istanbul üsküdar çengelköy"); // "Istanbul Üsküdar Çengelköy"
 *
 * @param string $gelen Dönüştürülecek metin
 * @return string Baş harfleri büyük metin
 */
function ucwords_tr($gelen)
{

    $sonuc = '';
    $kelimeler = explode(" ", $gelen);

    foreach ($kelimeler as $kelime_duz) {

        $kelime_uzunluk = strlen($kelime_duz);
        $ilk_karakter = mb_substr($kelime_duz, 0, 1, 'UTF-8');

        if ($ilk_karakter == 'Ç' or $ilk_karakter == 'ç') {
            $ilk_karakter = 'Ç';
        } elseif ($ilk_karakter == 'Ğ' or $ilk_karakter == 'ğ') {
            $ilk_karakter = 'Ğ';
        } elseif ($ilk_karakter == 'I' or $ilk_karakter == 'ı') {
            $ilk_karakter = 'I';
        } elseif ($ilk_karakter == 'I' or $ilk_karakter == 'i') {
            $ilk_karakter = 'I';
        } elseif ($ilk_karakter == 'Ö' or $ilk_karakter == 'ö') {
            $ilk_karakter = 'Ö';
        } elseif ($ilk_karakter == 'Ş' or $ilk_karakter == 'ş') {
            $ilk_karakter = 'Ş';
        } elseif ($ilk_karakter == 'Ü' or $ilk_karakter == 'ü') {
            $ilk_karakter = 'Ü';
        } else {
            $ilk_karakter = strtoupper($ilk_karakter);
        }

        $digerleri = mb_substr($kelime_duz, 1, $kelime_uzunluk, 'UTF-8');
        $sonuc .= $ilk_karakter . $digerleri . ' ';
    }

    $son = trim(str_replace(' ', ' ', $sonuc));
    return $son;
}
