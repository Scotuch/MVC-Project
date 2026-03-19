<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Önbellek Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Onbellek
{
    /**
     * Önbellek türleri
     */
    public const TURLER = [
        0 => 'Core',
        1 => 'App'
    ];

    /**
     * Önbellek klasörü oluşturur ve dosya yolunu hazırlar.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return array|false Önbellek bilgileri veya hata durumunda false
     */
    public static function Hazirla($anahtar, $konum = 1, $saat = 1)
    {
        if (
            empty($anahtar) ||
            !is_string($anahtar) ||
            !is_int($konum) ||
            ($konum !== 0 && $konum !== 1) ||
            !is_numeric($saat) ||
            $saat <= 0
        ) {
            return false;
        }

        $kokDizin = defined('ROOT') ? ROOT : './';

        if ($konum === 0) {
            $baseKlasor = $kokDizin . 'Core/Cache/';
        } else {
            $baseKlasor = $kokDizin . 'App/Cache/';
        }

        if (!is_dir($baseKlasor) && !@mkdir($baseKlasor, 0777, true)) {
            Log::Hata($konum, "Önbellek klasörü oluşturulamadı: $baseKlasor");
            return false;
        }

        $dosyaAdi = hash('sha256', $anahtar) . '.cache';
        $dosyaYolu = $baseKlasor . $dosyaAdi;
        $sure = $saat * 3600; // Saat cinsinden saniyeye çevir

        return [
            'anahtar' => $anahtar,
            'dosya_yolu' => $dosyaYolu,
            'sure' => $sure,
            'konum' => $konum
        ];
    }

    /**
     * Önbellekten veri okur.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return string|false Önbellekteki veri veya geçersizse false
     */
    public static function Oku($anahtar, $konum = 1, $saat = 1)
    {
        $hazirlik = self::Hazirla($anahtar, $konum, $saat);
        if (!$hazirlik) {
            return false;
        }

        $dosyaYolu = $hazirlik['dosya_yolu'];

        if (!file_exists($dosyaYolu)) {
            return false;
        }

        // Dosya süre kontrolü
        $olusturmaZamani = @filemtime($dosyaYolu);
        if (!$olusturmaZamani || (time() - $olusturmaZamani) > $hazirlik['sure']) {
            self::Sil($anahtar, $konum);
            return false;
        }

        $icerik = @file_get_contents($dosyaYolu);
        if ($icerik === false) {
            Log::Dikkat($konum, "Önbellek dosyası okunamadı: $anahtar");
            return false;
        }

        Log::Debug($konum, "Önbellek okundu: $anahtar");
        return $icerik;
    }

    /**
     * Önbelleğe veri yazar.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param string $veri Kaydedilecek veri
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return bool Yazma işleminin başarı durumu
     */
    public static function Yaz($anahtar, $veri, $konum = 1, $saat = 1)
    {
        if (!is_string($veri)) {
            return false;
        }

        $hazirlik = self::Hazirla($anahtar, $konum, $saat);
        if (!$hazirlik) {
            return false;
        }

        $dosyaYolu = $hazirlik['dosya_yolu'];

        if (@file_put_contents($dosyaYolu, $veri, LOCK_EX) === false) {
            Log::Hata($konum, "Önbellek yazılamadı: $anahtar");
            return false;
        }

        Log::Debug($konum, "Önbellek yazıldı: $anahtar (" . strlen($veri) . " bayt)");
        self::OtomatikTemizlik($konum);
        return true;
    }

    /**
     * Önbellekten veri alır, yoksa callback fonksiyonunu çalıştırıp sonucu önbelleğe kaydeder.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param callable $callback Veri üretecek fonksiyon
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return string|false Önbellekteki veya üretilen veri
     */
    public static function Hatirlat($anahtar, $callback, $konum = 1, $saat = 1)
    {
        if (!is_callable($callback)) {
            return false;
        }

        // Önce önbellekten kontrol et (aynı süre ile)
        $veri = self::Oku($anahtar, $konum, $saat);
        if ($veri !== false) {
            return $veri;
        }

        // Önbellekte yoksa callback'i çalıştır
        try {
            $yeniVeri = call_user_func($callback);
            if ($yeniVeri !== false && is_string($yeniVeri)) {
                self::Yaz($anahtar, $yeniVeri, $konum, $saat);
                return $yeniVeri;
            }
        } catch (Exception $e) {
            Log::Hata($konum, "Önbellek callback hatası: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Belirli bir önbellek dosyasını siler.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @return bool Silme işleminin başarı durumu
     */
    public static function Sil($anahtar, $konum = 1, $saat = 1)
    {
        $hazirlik = self::Hazirla($anahtar, $konum, $saat);
        if (!$hazirlik) {
            return false;
        }

        $dosyaYolu = $hazirlik['dosya_yolu'];

        if (file_exists($dosyaYolu)) {
            $sonuc = @unlink($dosyaYolu);
            if ($sonuc) {
                Log::Debug($konum, "Önbellek silindi: $anahtar");
            }
            return $sonuc;
        }

        return true; // Dosya zaten yok, başarılı kabul et
    }

    /**
     * Önbellek dosyasının durumunu kontrol eder.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return array|false Durum bilgileri veya dosya yoksa false
     */
    public static function Durum($anahtar, $konum = 1, $saat = 1)
    {
        $hazirlik = self::Hazirla($anahtar, $konum, $saat);
        if (!$hazirlik) {
            return false;
        }

        $dosyaYolu = $hazirlik['dosya_yolu'];

        if (!file_exists($dosyaYolu)) {
            return false;
        }

        $olusturmaZamani = @filemtime($dosyaYolu);
        $dosyaBoyutu = @filesize($dosyaYolu);
        $simdi = time();
        $gecenSure = $simdi - $olusturmaZamani;
        $kalanSure = $hazirlik['sure'] - $gecenSure;

        if ($kalanSure < 0) {
            $kalanSure = 0;
        }

        // Süreleri formatla
        $kalanSaat = floor($kalanSure / 3600);
        $kalanDakika = floor(($kalanSure % 3600) / 60);
        $kalanSaniye = $kalanSure % 60;

        $toplamSaat = floor($hazirlik['sure'] / 3600);
        $toplamDakika = floor(($hazirlik['sure'] % 3600) / 60);
        $toplamSn = $hazirlik['sure'] % 60;

        // Klasör yolunu sansürle
        $klasor = dirname($dosyaYolu);
        $dosyaAdi = basename($dosyaYolu);
        $klasorUzunluk = strlen($klasor);
        if ($klasorUzunluk > 10) {
            $sansurluKlasor = substr($klasor, 0, 3) . '...' . substr($klasor, -4);
        } else {
            $sansurluKlasor = str_repeat('X', $klasorUzunluk);
        }
        $sansurluYol = $sansurluKlasor . DIRECTORY_SEPARATOR . $dosyaAdi;

        return [
            'anahtar' => $anahtar,
            'dosya_yolu' => $sansurluYol,
            'olusturma_zamani' => date('Y-m-d H:i:s', $olusturmaZamani),
            'gecen_sure_saniye' => $gecenSure,
            'kalan_sure' => sprintf('%d saat %d dakika %d saniye', $kalanSaat, $kalanDakika, $kalanSaniye),
            'kalan_sure_saniye' => $kalanSure,
            'toplam_sure' => sprintf('%d saat %d dakika %d saniye', $toplamSaat, $toplamDakika, $toplamSn),
            'dosya_boyutu' => $dosyaBoyutu . ' bayt',
            'gecerli_mi' => ($kalanSure > 0),
            'sure_doldu_mu' => ($kalanSure === 0) ? 'Evet' : 'Hayır'
        ];
    }

    /**
     * Tüm önbellek dosyalarını temizler.
     *
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @return bool Temizleme işleminin başarı durumu
     */
    public static function Temizle($konum = 1)
    {
        $kokDizin = defined('ROOT') ? ROOT : './';

        if ($konum === 0) {
            $baseKlasor = $kokDizin . 'Core/Cache/';
        } else {
            $baseKlasor = $kokDizin . 'App/Cache/';
        }

        if (!is_dir($baseKlasor)) {
            return true;
        }

        $dosyalar = @glob($baseKlasor . '*.cache');
        if (!$dosyalar) {
            return true;
        }

        $silinenSayisi = 0;
        foreach ($dosyalar as $dosya) {
            if (@unlink($dosya)) {
                $silinenSayisi++;
            }
        }

        Log::Bilgi($konum, "Önbellek temizlendi: $silinenSayisi dosya silindi");
        return true;
    }

    /**
     * Süresi dolmuş önbellek dosyalarını otomatik temizler.
     *
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @return int Silinen dosya sayısı
     */
    private static function OtomatikTemizlik($konum = 1)
    {
        $kokDizin = defined('ROOT') ? ROOT : './';

        if ($konum === 0) {
            $baseKlasor = $kokDizin . 'Core/Cache/';
        } else {
            $baseKlasor = $kokDizin . 'App/Cache/';
        }

        if (!is_dir($baseKlasor)) {
            return 0;
        }

        $dosyalar = @glob($baseKlasor . '*.cache');
        if (!$dosyalar) {
            return 0;
        }

        $simdi = time();
        $silinenSayisi = 0;
        $maxOnbellekSuresi = (defined('ONBELLEK_OTO_TEMIZLEME_SAAT') ? ONBELLEK_OTO_TEMIZLEME_SAAT : 24) * 3600; // Saat cinsinden saniyeye çevir

        foreach ($dosyalar as $dosya) {
            $olusturmaZamani = @filemtime($dosya);
            if ($olusturmaZamani && ($simdi - $olusturmaZamani) > $maxOnbellekSuresi) {
                if (@unlink($dosya)) {
                    $silinenSayisi++;
                }
            }
        }

        if ($silinenSayisi > 0) {
            Log::Debug($konum, "Otomatik önbellek temizliği: $silinenSayisi eski dosya silindi");
        }

        return $silinenSayisi;
    }

    /**
     * Önbellek istatistiklerini döndürür.
     *
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @return array Önbellek istatistikleri
     */
    public static function Istatistikler($konum = 1)
    {
        $kokDizin = defined('ROOT') ? ROOT : './';

        if ($konum === 0) {
            $baseKlasor = $kokDizin . 'Core/Cache/';
        } else {
            $baseKlasor = $kokDizin . 'App/Cache/';
        }

        if (!is_dir($baseKlasor)) {
            return [
                'toplam_dosya' => 0,
                'toplam_boyut' => 0,
                'gecerli_dosya' => 0,
                'suresi_dolmus_dosya' => 0
            ];
        }

        $dosyalar = @glob($baseKlasor . '*.cache');
        if (!$dosyalar) {
            return [
                'toplam_dosya' => 0,
                'toplam_boyut' => 0,
                'gecerli_dosya' => 0,
                'suresi_dolmus_dosya' => 0
            ];
        }

        $toplamDosya = count($dosyalar);
        $toplamBoyut = 0;
        $gecerliDosya = 0;
        $suresiDolmusDosya = 0;
        $simdi = time();
        $maxOnbellekSuresi = 86400;

        foreach ($dosyalar as $dosya) {
            $boyut = @filesize($dosya);
            $olusturmaZamani = @filemtime($dosya);

            if ($boyut !== false) {
                $toplamBoyut += $boyut;
            }

            if ($olusturmaZamani && ($simdi - $olusturmaZamani) <= $maxOnbellekSuresi) {
                $gecerliDosya++;
            } else {
                $suresiDolmusDosya++;
            }
        }

        return [
            'toplam_dosya' => $toplamDosya,
            'toplam_boyut' => $toplamBoyut,
            'toplam_boyut_formatli' => self::BoyutFormatla($toplamBoyut),
            'gecerli_dosya' => $gecerliDosya,
            'suresi_dolmus_dosya' => $suresiDolmusDosya,
            'klasor_yolu' => $baseKlasor
        ];
    }

    /**
     * Array veya object verisini JSON olarak önbelleğe yazar.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param mixed $veri Array veya object veri
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return bool Yazma işleminin başarı durumu
     */
    public static function Json_Yaz($anahtar, $veri, $konum = 1, $saat = 1)
    {
        if (!is_array($veri) && !is_object($veri)) {
            Log::Dikkat($konum, "Json_Yaz: Sadece array ve object veri türleri destekleniyor: $anahtar");
            return false;
        }

        $jsonVeri = @json_encode($veri, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($jsonVeri === false) {
            Log::Hata($konum, "Json_Yaz: JSON encode hatası: $anahtar");
            return false;
        }

        return self::Yaz($anahtar, $jsonVeri, $konum, $saat);
    }

    /**
     * JSON formatında kaydedilmiş veriyi okur ve decode eder.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param bool $assoc True ise array, false ise object döndürür
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return mixed|false Decode edilmiş veri veya hata durumunda false
     */
    public static function Json_Oku($anahtar, $konum = 1, $assoc = true, $saat = 1)
    {
        $jsonVeri = self::Oku($anahtar, $konum, $saat);
        if ($jsonVeri === false) {
            return false;
        }

        $veri = @json_decode($jsonVeri, $assoc);
        if ($veri === null && json_last_error() !== JSON_ERROR_NONE) {
            Log::Hata($konum, "Json_Oku: JSON decode hatası: $anahtar - " . json_last_error_msg());
            return false;
        }

        return $veri;
    }

    /**
     * Çoklu anahtarları toplu olarak okur.
     *
     * @param array $anahtarlar Okunacak önbellek anahtarları
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return array Anahtar => veri çiftleri (bulunamayan anahtarlar null)
     */
    public static function TopluOku($anahtarlar, $konum = 1, $saat = 1)
    {
        if (!is_array($anahtarlar) || empty($anahtarlar)) {
            return [];
        }

        $sonuclar = [];
        foreach ($anahtarlar as $anahtar) {
            $sonuclar[$anahtar] = self::Oku($anahtar, $konum, $saat);
        }

        Log::Debug($konum, "Toplu okuma: " . count($anahtarlar) . " anahtar işlendi");
        return $sonuclar;
    }

    /**
     * Çoklu veriyi toplu olarak yazar.
     *
     * @param array $veriler Anahtar => veri çiftleri
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return array Başarılı/başarısız işlem sonuçları
     */
    public static function TopluYaz($veriler, $konum = 1, $saat = 1)
    {
        if (!is_array($veriler) || empty($veriler)) {
            return [];
        }

        $sonuclar = [];
        $basarili = 0;
        foreach ($veriler as $anahtar => $veri) {
            $sonuc = self::Yaz($anahtar, $veri, $konum, $saat);
            $sonuclar[$anahtar] = $sonuc;
            if ($sonuc) {
                $basarili++;
            }
        }

        Log::Debug($konum, "Toplu yazma: $basarili/" . count($veriler) . " anahtar başarılı");
        return $sonuclar;
    }

    /**
     * Çoklu anahtarları toplu olarak siler.
     *
     * @param array $anahtarlar Silinecek önbellek anahtarları
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @return array Başarılı/başarısız silme sonuçları
     */
    public static function TopluSil($anahtarlar, $konum = 1)
    {
        if (!is_array($anahtarlar) || empty($anahtarlar)) {
            return [];
        }

        $sonuclar = [];
        $basarili = 0;
        foreach ($anahtarlar as $anahtar) {
            $sonuc = self::Sil($anahtar, $konum);
            $sonuclar[$anahtar] = $sonuc;
            if ($sonuc) {
                $basarili++;
            }
        }

        Log::Debug($konum, "Toplu silme: $basarili/" . count($anahtarlar) . " anahtar başarılı");
        return $sonuclar;
    }

    /**
     * Önbellek anahtarının var olup olmadığını kontrol eder.
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return bool Anahtar varsa true, yoksa false
     */
    public static function Kontrol($anahtar, $konum = 1, $saat = 1)
    {
        $hazirlik = self::Hazirla($anahtar, $konum, $saat);
        if (!$hazirlik) {
            return false;
        }

        $dosyaYolu = $hazirlik['dosya_yolu'];

        if (!file_exists($dosyaYolu)) {
            return false;
        }

        // Süre kontrolü
        $olusturmaZamani = @filemtime($dosyaYolu);
        if (!$olusturmaZamani || (time() - $olusturmaZamani) > $hazirlik['sure']) {
            self::Sil($anahtar, $konum);
            return false;
        }

        return true;
    }

    /**
     * Önbellek süresini uzatır (touch işlemi).
     *
     * @param string $anahtar Önbellek anahtarı
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $yeniSaat Yeni süre (saat, boşsa dosyayı şu anki zamana günceller)
     * @return bool Güncelleme işleminin başarı durumu
     */
    public static function Uzat($anahtar, $konum = 1, $yeniSaat = null)
    {
        $mevcutSaat = ($yeniSaat !== null && is_int($yeniSaat) && $yeniSaat > 0) ? $yeniSaat : 1;
        $hazirlik = self::Hazirla($anahtar, $konum, $mevcutSaat);
        if (!$hazirlik) {
            return false;
        }

        $dosyaYolu = $hazirlik['dosya_yolu'];

        if (!file_exists($dosyaYolu)) {
            return false;
        }

        $yeniZaman = time();

        // Eğer yeni süre verilmişse, içeriği okuyup yeniden yaz
        if ($yeniSaat !== null && is_int($yeniSaat) && $yeniSaat > 0) {
            $icerik = @file_get_contents($dosyaYolu);
            if ($icerik !== false) {
                return self::Yaz($anahtar, $icerik, $konum, $yeniSaat);
            } else {
                return false;
            }
        }

        // Sadece dosya zamanını güncelle
        $sonuc = @touch($dosyaYolu, $yeniZaman);
        if ($sonuc) {
            Log::Debug($konum, "Önbellek süresi uzatıldı: $anahtar");
        }

        return $sonuc;
    }

    /**
     * Önbellek verilerini günceller (mevcut veriyi okur, callback ile işler, geri yazar).
     *
     * @param string $anahtar Önbellek anahtarı
     * @param callable $callback Veriyi işleyecek fonksiyon
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: mevcut süre korunur)
     * @return mixed|false Güncellenmiş veri veya hata durumunda false
     */
    public static function Guncelle($anahtar, $callback, $konum = 1, $saat = null)
    {
        if (!is_callable($callback)) {
            return false;
        }

        $mevcutVeri = self::Oku($anahtar, $konum);
        if ($mevcutVeri === false) {
            return false;
        }

        try {
            $yeniVeri = call_user_func($callback, $mevcutVeri);
            if ($yeniVeri !== false && is_string($yeniVeri)) {
                // Süre belirtilmemişse mevcut süreyi koru
                if ($saat === null) {
                    $durum = self::Durum($anahtar, $konum);
                    $saat = $durum ? ceil($durum['kalan_sure_saniye'] / 3600) : 1;
                }

                if (self::Yaz($anahtar, $yeniVeri, $konum, $saat)) {
                    Log::Debug($konum, "Önbellek güncellendi: $anahtar");
                    return $yeniVeri;
                }
            }
        } catch (Exception $e) {
            Log::Hata($konum, "Önbellek güncelleme hatası: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Belirtilen prefix ile başlayan tüm önbellek anahtarlarını listeler.
     *
     * @param string $prefix Aranacak prefix
     * @param int $konum Önbellek konumu (0: Core, 1: App)
     * @param int $saat Saat cinsinden önbellek süresi (varsayılan: 1 saat)
     * @return array Bulunan anahtar listesi
     */
    public static function Listele($prefix, $konum = 1, $saat = 1)
    {
        if (empty($prefix) || !is_string($prefix)) {
            return [];
        }

        $kokDizin = defined('ROOT') ? ROOT : './';

        if ($konum === 0) {
            $baseKlasor = $kokDizin . 'Core/Cache/';
        } else {
            $baseKlasor = $kokDizin . 'App/Cache/';
        }

        if (!is_dir($baseKlasor)) {
            return [];
        }

        $dosyalar = @glob($baseKlasor . '*.cache');
        if (!$dosyalar) {
            return [];
        }

        $bulunanlar = [];
        $simdi = time();

        foreach ($dosyalar as $dosyaYolu) {
            $olusturmaZamani = @filemtime($dosyaYolu);
            if (!$olusturmaZamani) {
                continue;
            }

            // Geçici olarak farklı anahtarları test et
            for ($i = 0; $i < 1000; $i++) {
                $testAnahtar = $prefix . $i;
                $testHash = hash('sha256', $testAnahtar) . '.cache';

                if (basename($dosyaYolu) === $testHash) {
                    // Süre kontrolü
                    $hazirlik = self::Hazirla($testAnahtar, $konum, $saat);
                    if ($hazirlik && ($simdi - $olusturmaZamani) <= $hazirlik['sure']) {
                        $bulunanlar[] = $testAnahtar;
                    }
                    break;
                }
            }
        }

        return $bulunanlar;
    }

    /**
     * Bayt cinsinden boyutu okunabilir formata çevirir.
     *
     * @param int $bayt Bayt cinsinden boyut
     * @return string Formatlanmış boyut
     */
    private static function BoyutFormatla($bayt)
    {
        $birimler = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bayt >= 1024 && $i < count($birimler) - 1) {
            $bayt /= 1024;
            $i++;
        }

        return round($bayt, 2) . ' ' . $birimler[$i];
    }
}
