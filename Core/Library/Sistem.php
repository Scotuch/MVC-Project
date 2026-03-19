<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Sistem Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Sistem
{
    private static $instance = null;

    private function __construct()
    {
        self::$instance = $this;
    }

    public static function Calistir()
    {
        if (self::$instance === null) {
            new self();
        }
        Log::Bilgi(0, "Sistem başlatıldı.", GELISTIRICI);
        return self::$instance;
    }

    public static function Dosya($konum = null, $dosya = null, $veri = null)
    {
        if ($dosya === null || $konum === null || $dosya === '' || $konum === '') {
            return false;
        }

        $dosyaYolu = rtrim($konum, DS) . DS . $dosya . '.php';
        $dosyaKonum = realpath($dosyaYolu);
        if ($dosyaKonum && file_exists($dosyaKonum)) {
            if (is_object($veri)) {
                $veri = Object_Array_Cevir($veri);
            }
            if (is_array($veri)) {
                extract($veri, EXTR_SKIP);
            }

            require_once $dosyaKonum;
            Log::Bilgi(0, "Dosya dahil edildi: {$dosyaYolu}", GELISTIRICI);
            return $dosya;
        } else {
            $Mesaj = Mesaj("hata", "Dosya bulunamadı: {$dosyaYolu}", "", true);
            Log::Hata(0, $Mesaj->mesaj);
            if (GELISTIRICI) {
                throw new Exception($Mesaj->mesaj);
            } else {
                http_response_code($Mesaj->kod);
                die("{$Mesaj->kod} Not Authorized");
                exit;
            }
            return false;
        }
    }
}
