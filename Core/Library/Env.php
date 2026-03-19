<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Env Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Env
{
    private static $degiskenler = [];
    private static $yuklendi = false;
    private static $dosya = '.env';

    public static function Yukle($dosya = null)
    {
        if ($dosya === null) {
            $dosya = self::$dosya;
        }

        if (empty($dosya)) {
            return false;
        }

        if (!self::$yuklendi) {
            if (file_exists($dosya)) {
                $satirlar = file($dosya, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                if ($satirlar === false) {
                    Log::Hata(0, "Env dosyası okunamadı: {$dosya}");
                    return false;
                }

                foreach ($satirlar as $satir) {
                    $satir = trim($satir);

                    if (empty($satir) || strpos($satir, '#') === 0) {
                        continue;
                    }

                    if (strpos($satir, '=') !== false) {
                        $parcalar = explode('=', $satir, 2);
                        if (count($parcalar) === 2) {
                            $temizAnahtar = trim($parcalar[0]);
                            $temizDeger = trim($parcalar[1], '"\'');

                            if (!empty($temizAnahtar)) {
                                self::$degiskenler[$temizAnahtar] = $temizDeger;
                            }
                        }
                    }
                }
            } else {
                Log::Dikkat(0, "Env dosyası bulunamadı: {$dosya}", GELISTIRICI);
            }
            self::$yuklendi = true;
        }
        return true;
    }

    public static function Sec($anahtar, $varsayilan = null)
    {
        if (empty($anahtar)) {
            return null;
        }

        self::Yukle();

        if ($varsayilan === null && !isset(self::$degiskenler[$anahtar])) {
            return null;
        }

        if (isset(self::$degiskenler[$anahtar])) {
            return self::$degiskenler[$anahtar];
        } else {
            return $varsayilan;
        }
    }

    public static function Belirle($anahtar, $deger)
    {
        if (empty($anahtar)) {
            return false;
        }

        self::Yukle();
        self::$degiskenler[$anahtar] = $deger;

        return true;
    }

    public static function Sil($anahtar)
    {
        if (empty($anahtar)) {
            return false;
        }

        self::Yukle();

        if (!isset(self::$degiskenler[$anahtar])) {
            return false;
        }

        unset(self::$degiskenler[$anahtar]);

        return true;
    }

    public static function Tumunu()
    {
        self::Yukle();
        return self::$degiskenler;
    }

    public static function Dosya_Yaz($anahtar, $deger)
    {
        if (empty($anahtar)) {
            return false;
        }

        self::Yukle();
        self::$degiskenler[$anahtar] = $deger;
        $sonuc = self::Kaydet();

        return $sonuc;
    }

    public static function Dosya_Sil($anahtar)
    {
        if (empty($anahtar)) {
            return false;
        }

        self::Yukle();

        if (!isset(self::$degiskenler[$anahtar])) {
            return false;
        }

        unset(self::$degiskenler[$anahtar]);
        $sonuc = self::Kaydet();

        return $sonuc;
    }

    private static function Kaydet()
    {
        if (empty(self::$dosya)) {
            return false;
        }

        $icerik = '';

        foreach (self::$degiskenler as $anahtar => $deger) {
            if (empty($anahtar)) {
                continue;
            }

            $degerStr = (string) $deger;

            if (strpos($degerStr, ' ') !== false || strpos($degerStr, '#') !== false || strpos($degerStr, '"') !== false) {
                $degerStr = '"' . str_replace('"', '\"', $degerStr) . '"';
            }
            $icerik .= $anahtar . '=' . $degerStr . PHP_EOL;
        }

        $dosyaYolu = dirname(self::$dosya);
        if (!empty($dosyaYolu) && !is_dir($dosyaYolu)) {
            if (!mkdir($dosyaYolu, 0755, true)) {
                Log::Hata(0, "Env klasörü oluşturulamadı: {$dosyaYolu}");
                return false;
            }
        }

        $sonuc = file_put_contents(self::$dosya, $icerik, LOCK_EX) !== false;
        if (!$sonuc) {
            Log::Hata(0, "Env dosyası yazılamadı: " . self::$dosya);
        } else {
            Log::Bilgi(0, "Env dosyası güncellendi: " . self::$dosya, GELISTIRICI);
        }
        return $sonuc;
    }
}
