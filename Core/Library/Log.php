<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Log Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Log
{
    public const SEVIYELER = [
        0 => 'HATA',
        1 => 'DIKKAT',
        2 => 'BILGI',
        3 => 'BASARI',
        4 => 'DEBUG'
    ];

    /**
     * Log kaydı oluşturur.
     *
     * Kullanılabilir log seviyeleri:
     *
     * 0 => HATA    : Sistemsel veya kritik hatalar
     * 1 => DIKKAT   : Uyarılar (önemsiz ama dikkat edilmesi gereken durumlar)
     * 2 => BILGI   : Genel bilgi mesajları (ör. kullanıcı girişi, API çağrısı)
     * 3 => BASARI  : Başarıyla tamamlanan işlemler
     * 4 => DEBUG   : Geliştirme sürecinde kullanılacak hata ayıklama verileri
     *
     * Log konumu seviyeleri:
     * 0 => Core    : Core klasörüne log yazar
     * 1 => App     : App klasörüne log yazar
     *
     * @param int $seviye Log seviyesi (0-4 arası)
     * @param int $konum Log konumu (0: Core, 1: App)
     * @param string $mesaj Kaydedilecek log mesajı
     * @param string $durum Dev modu için (true ise -dev.log dosyası oluşturur)
     *
     * @return bool Log yazma işleminin başarı durumu (true: başarılı, false: başarısız)
     */
    public static function Yaz($seviye = '', $konum = 1, $mesaj = '', $durum = '')
    {
        if (
            $durum === false ||
            $seviye === '' ||
            $mesaj === '' ||
            !is_int($seviye) ||
            !isset(self::SEVIYELER[$seviye]) ||
            !is_string($mesaj) ||
            !is_int($konum) ||
            ($konum !== 0 && $konum !== 1)
        ) {
            return false;
        }

        $seviyeAdi = self::SEVIYELER[$seviye];
        $tarih = date('H:i:s');
        $kokDizin = defined('ROOT') ? ROOT : './';

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $gercekcaller = null;

        foreach ($backtrace as $trace) {
            if (!isset($trace['class']) || $trace['class'] !== 'Log') {
                $gercekcaller = $trace;
                break;
            }
        }

        if ($gercekcaller === null) {
            $gercekcaller = end($backtrace);
        }

        $callerFile = $gercekcaller['file'] ?? '';
        $satirNumarasi = $gercekcaller['line'] ?? 0;
        $tamDosyaYolu = str_replace($kokDizin, '', $callerFile);
        $fonksiyonAdi = $gercekcaller['function'] ?? '';

        if ($konum === 0) {
            $baseKlasor = $kokDizin . 'Core/Log/';
        } else {
            $baseKlasor = $kokDizin . 'App/Log/';
        }

        if (!is_dir($baseKlasor) && !@mkdir($baseKlasor, 0777, true)) {
            return false;
        }

        $yil = date('Y');
        $ay = date('m');
        $gun = date('d');

        $tarihKlasoru = $baseKlasor . $yil . DS . $ay . DS;
        if (!is_dir($tarihKlasoru) && !@mkdir($tarihKlasoru, 0777, true)) {
            return false;
        }

        $dosyaYolu = $tarihKlasoru . $gun . '.log';

        if ($durum === true) {
            $dosyaYolu = $tarihKlasoru . $gun . '-dev.log';
        }

        $kısaLogSatiri = "[$tarih] [$seviyeAdi] $mesaj" . PHP_EOL;

        $detaylar = [];
        $detaylar[] = "$tamDosyaYolu:$satirNumarasi";

        if ($fonksiyonAdi) {
            $detaylar[] = "Func:$fonksiyonAdi";
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $detaylar[] = "IP:" . (self::IP_Adresi() ?? 'Unknown');
            $detaylar[] = "Method:" . $_SERVER['REQUEST_METHOD'];
            if (!empty($_SERVER['REQUEST_URI'])) {
                $detaylar[] = "URI:" . substr($_SERVER['REQUEST_URI'], 0, 100);
            }
        }

        if ($seviye === 4) {
            $detaylar[] = "Mem:" . round(memory_get_usage() / 1024, 1) . "KB";
        }

        $detaylıLogSatiri = "[$tarih] [$seviyeAdi] [" . implode('] [', $detaylar) . "]" . PHP_EOL;
        $logSatiri = $kısaLogSatiri . $detaylıLogSatiri;

        if (@file_put_contents($dosyaYolu, $logSatiri, FILE_APPEND | LOCK_EX) === false) {
            return false;
        }

        self::LogRotasyonu($dosyaYolu);
        return true;
    }

    /**
     * Log dosya rotasyonu gerçekleştirir.
     * Dosya boyutu maksimum değeri aşarsa eski dosyaları arşivler.
     * Tüm hatalar sessizce işlenir.
     *
     * @param string $dosyaYolu Log dosyasının tam yolu
     * @return bool Rotasyon işleminin başarı durumu
     */
    private static function LogRotasyonu($dosyaYolu)
    {
        $logBoyutu = (defined('LOG_BOYUTU') ? LOG_BOYUTU : 2) * 1024 * 1024;

        if (!file_exists($dosyaYolu) || @filesize($dosyaYolu) < $logBoyutu) {
            return true;
        }

        $dosyaInfo = @pathinfo($dosyaYolu);
        if (!$dosyaInfo) {
            return false;
        }

        $baseAd = $dosyaInfo['filename'];
        $uzanti = $dosyaInfo['extension'];
        $dizin = $dosyaInfo['dirname'];
        $maxDosyaSayisi = defined('LOG_SINIR') ? LOG_SINIR : 10;

        for ($i = $maxDosyaSayisi - 1; $i >= 1; $i--) {
            $eskiDosya = $dizin . DS . $baseAd . '.' . $i . '.' . $uzanti;
            $yeniDosya = $dizin . DS . $baseAd . '.' . ($i + 1) . '.' . $uzanti;

            if (file_exists($eskiDosya)) {
                if ($i == $maxDosyaSayisi - 1) {
                    @unlink($eskiDosya);
                } else {
                    @rename($eskiDosya, $yeniDosya);
                }
            }
        }

        $arşivDosya = $dizin . DS . $baseAd . '.1.' . $uzanti;
        return @rename($dosyaYolu, $arşivDosya);
    }

    /**
     * HATA seviyesinde log kaydı oluşturur.
     *
     * @param int $konum Log konumu (0: Core, 1: App)
     * @param string $mesaj Kaydedilecek hata mesajı
     * @param string $durum Dev modu için (true ise -dev.log dosyası oluşturur)
     *
     * @return bool Log yazma işleminin başarı durumu
     */
    public static function Hata($konum = 1, $mesaj, $durum = '')
    {
        return self::Yaz(0, $konum, $mesaj, $durum);
    }

    /**
     * DIKKAT seviyesinde log kaydı oluşturur.
     *
     * @param int $konum Log konumu (0: Core, 1: App)
     * @param string $mesaj Kaydedilecek dikkat mesajı
     * @param string $durum Dev modu için (true ise -dev.log dosyası oluşturur)
     *
     * @return bool Log yazma işleminin başarı durumu
     */
    public static function Dikkat($konum = 1, $mesaj, $durum = '')
    {
        return self::Yaz(1, $konum, $mesaj, $durum);
    }

    /**
     * BILGI seviyesinde log kaydı oluşturur.
     *
     * @param int $konum Log konumu (0: Core, 1: App)
     * @param string $mesaj Kaydedilecek bilgi mesajı
     * @param string $durum Dev modu için (true ise -dev.log dosyası oluşturur)
     *
     * @return bool Log yazma işleminin başarı durumu
     */
    public static function Bilgi($konum = 1, $mesaj, $durum = '')
    {
        return self::Yaz(2, $konum, $mesaj, $durum);
    }

    /**
     * BASARI seviyesinde log kaydı oluşturur.
     *
     * @param int $konum Log konumu (0: Core, 1: App)
     * @param string $mesaj Kaydedilecek başarı mesajı
     * @param string $durum Dev modu için (true ise -dev.log dosyası oluşturur)
     *
     * @return bool Log yazma işleminin başarı durumu
     */
    public static function Basari($konum = 1, $mesaj, $durum = '')
    {
        return self::Yaz(3, $konum, $mesaj, $durum);
    }

    /**
     * DEBUG seviyesinde log kaydı oluşturur.
     *
     * @param int $konum Log konumu (0: Core, 1: App)
     * @param string $mesaj Kaydedilecek debug mesajı
     * @param string $durum Dev modu için (true ise -dev.log dosyası oluşturur)
     *
     * @return bool Log yazma işleminin başarı durumu
     */
    public static function Debug($konum = 1, $mesaj, $durum = '')
    {
        return self::Yaz(4, $konum, $mesaj, $durum);
    }

    /**
     * Kullanıcının gerçek IP adresini güvenli bir şekilde tespit eder.
     *
     * Bu method, proxy, CDN ve load balancer arkasındaki gerçek IP'yi bulur.
     * Birden fazla HTTP header'ı kontrol ederek en güvenilir IP adresini döndürür.
     *
     * Kontrol edilen header'lar:
     * - HTTP_CLIENT_IP: Client IP (genellikle proxy'ler tarafından set edilir)
     * - HTTP_X_FORWARDED_FOR: X-Forwarded-For header (en yaygın)
     * - HTTP_X_FORWARDED: X-Forwarded header
     * - HTTP_X_CLUSTER_CLIENT_IP: Cluster client IP
     * - HTTP_FORWARDED_FOR: Forwarded-For header
     * - HTTP_FORWARDED: Forwarded header (RFC 7239)
     * - REMOTE_ADDR: Doğrudan bağlanan IP (fallback)
     *
     * @return string Tespit edilen IP adresi, localhost için 'localhost', bulunamazsa 'unknown'
     */
    private static function IP_Adresi()
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);

                if ($ip === "::1" || $ip === "127.0.0.1") {
                    return "localhost";
                }

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
