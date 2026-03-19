<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Zaman fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Mikrosaniye Düzeyinde Süre Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, belirli bir başlangıç zamanından itibaren geçen süreyi
 * mikrosaniye hassasiyetinde hesaplar ve saniye cinsinden döndürür.
 *
 * Kullanım Alanı:
 * - Performance monitoring
 * - Kod bloklarının execution time ölçümü
 * - Benchmark testleri
 * - API response time hesaplama
 *
 * Teknik Detaylar:
 * - microtime(true) kullanarak Unix timestamp + microseconds
 * - BASLANGIC sabiti otomatik fallback
 * - Float precision ile hassas hesaplama
 * - 2 decimal point formatting
 *
 * Precision Level:
 * - Microsecond accuracy (0.000001 second)
 * - Number format: XX.XX seconds
 * - Memory efficient calculation
 * - No external dependency
 *
 * Kullanım Örneği:
 * // Script başında
 * define('BASLANGIC', microtime(true));
 * // İşlem sonunda
 * $gecen_sure = Mikro_Zaman();
 * echo "İşlem süresi: {$gecen_sure} saniye";
 *
 * @param float|null $data Başlangıç zamanı (microtime true formatında, null ise BASLANGIC sabiti kullanılır)
 * @return string Geçen süre saniye cinsinden (XX.XX formatında)
 */
function Mikro_Zaman($data = null)
{
    $data = $data ?? (defined('BASLANGIC') ? BASLANGIC : microtime(true));

    $duration = microtime(true) - $data;
    $hours = (int)($duration / 60 / 60);
    $minutes = (int)($duration / 60) - $hours * 60;
    $seconds = $duration - $hours * 60 * 60 - $minutes * 60;
    return number_format((float)$seconds, 2, '.', '');
}

/**
 * Mikrosaniye Düzeyinde Süre Hesaplama Fonksiyonu (Milisaniye Çıktı)
 *
 * Bu fonksiyon, belirli bir başlangıç zamanından itibaren geçen süreyi
 * mikrosaniye hassasiyetinde hesaplar ve milisaniye cinsinden döndürür.
 *
 * Kullanım Alanı:
 * - Performance monitoring (milisaniye hassasiyeti)
 * - API response time ölçümü
 * - Database query execution time
 * - JavaScript uyumlu timing display
 *
 * Teknik Detaylar:
 * - microtime(true) kullanarak Unix timestamp + microseconds
 * - BASLANGIC sabiti otomatik fallback
 * - Milisaniye dönüşümü (* 1000)
 * - 2 decimal point formatting with 'ms' unit
 *
 * Precision Level:
 * - Microsecond accuracy (0.000001 second)
 * - Output format: XX.XXms
 * - Memory efficient calculation
 * - No external dependency
 *
 * Kullanım Örneği:
 * // Script başında
 * define('BASLANGIC', microtime(true));
 * // İşlem sonunda
 * $gecen_sure = Mikro_Saniye();
 * echo "İşlem süresi: {$gecen_sure}"; // "İşlem süresi: 145.67ms"
 *
 * @param float|null $data Başlangıç zamanı (microtime true formatında, null ise BASLANGIC sabiti kullanılır)
 * @return string Geçen süre milisaniye cinsinden (XX.XXms formatında)
 */
function Mikro_Saniye($data = null)
{
    $data = $data ?? (defined('BASLANGIC') ? BASLANGIC : microtime(true));

    $duration = microtime(true) - $data;
    $milliseconds = $duration * 1000;
    return number_format($milliseconds, 2, '.', '') . 'ms';
}

/**
 * Güncel Tarih Alma Fonksiyonu
 *
 * Bu fonksiyon, sistemin mevcut tarihini ISO 8601 standardında
 * (YYYY-MM-DD) formatında döndürür.
 *
 * Kullanım Alanı:
 * - Database kayıtlarında tarih stamping
 * - Log dosyalarında tarih etiketi
 * - Form default değerleri
 * - Tarih karşılaştırmaları
 *
 * Teknik Detaylar:
 * - PHP date() fonksiyonu kullanımı
 * - Server timezone'ına göre hesaplama
 * - ISO 8601 standard format
 * - MySQL DATE field uyumlu
 *
 * Format Özellikleri:
 * - YYYY: 4 haneli yıl (2025)
 * - MM: 2 haneli ay (01-12)
 * - DD: 2 haneli gün (01-31)
 * - Separator: Dash (-)
 *
 * Kullanım Örneği:
 * $bugun = Tarih();
 * echo $bugun; // '2025-10-01'
 * // SQL Query'de kullanım
 * $sql = "INSERT INTO logs (tarih) VALUES ('" . Tarih() . "')";
 *
 * @return string Güncel tarih YYYY-MM-DD formatında
 */
function Tarih()
{
    return date('Y-m-d');
}

/**
 * Güncel Saat Alma Fonksiyonu
 *
 * Bu fonksiyon, sistemin mevcut saatini 24-saat formatında
 * (HH:MM:SS) şeklinde döndürür.
 *
 * Kullanım Alanı:
 * - Log kayıtlarında time stamping
 * - Sistem monitoring
 * - Real-time clock display
 * - Time-based operations
 *
 * Teknik Detaylar:
 * - PHP date() fonksiyonu kullanımı
 * - Server timezone'ına göre hesaplama
 * - 24-hour format (00:00:00 - 23:59:59)
 * - MySQL TIME field uyumlu
 *
 * Format Özellikleri:
 * - HH: 2 haneli saat (00-23)
 * - MM: 2 haneli dakika (00-59)
 * - SS: 2 haneli saniye (00-59)
 * - Separator: Colon (:)
 *
 * Kullanım Örneği:
 * $simdi = Saat();
 * echo $simdi; // '14:30:25'
 * // Log entry'de kullanım
 * $log = Saat() . ' - İşlem tamamlandı';
 *
 * @return string Güncel saat HH:MM:SS formatında
 */
function Saat()
{
    return date('H:i:s');
}

/**
 * Güncel Tarih ve Saat Alma Fonksiyonu
 *
 * Bu fonksiyon, sistemin mevcut tarih ve saatini ISO 8601 standardında
 * (YYYY-MM-DD HH:MM:SS) formatında döndürür.
 *
 * Kullanım Alanı:
 * - Database timestamp kayıtları
 * - Audit log entries
 * - Created/Updated datetime fields
 * - Full datetime comparisons
 *
 * Teknik Detaylar:
 * - PHP date() fonksiyonu kullanımı
 * - Server timezone'ına göre hesaplama
 * - MySQL DATETIME field uyumlu
 * - ISO 8601 partial compliance
 *
 * Format Özellikleri:
 * - YYYY-MM-DD: Tarih kısmı
 * - HH:MM:SS: Saat kısmı
 * - Space separator between date and time
 * - 24-hour time format
 *
 * Kullanım Örneği:
 * $timestamp = Tarih_Saat();
 * echo $timestamp; // '2025-10-01 14:30:25'
 * // Database insert'te kullanım
 * $sql = "INSERT INTO users (created_at) VALUES ('" . Tarih_Saat() . "')";
 *
 * @return string Güncel tarih ve saat YYYY-MM-DD HH:MM:SS formatında
 */
function Tarih_Saat()
{
    return date('Y-m-d H:i:s');
}

/**
 * Türkçe Tarih Formatı Fonksiyonu
 *
 * Bu fonksiyon, güncel tarihi Türkçe gün adı ile birlikte
 * okunabilir formatta döndürür.
 *
 * Kullanım Alanı:
 * - Kullanıcı arayüzlerinde tarih gösterimi
 * - Türkçe raporlarda tarih formatı
 * - Dashboard'larda güncel tarih
 * - Email template'lerinde tarih
 *
 * Teknik Detaylar:
 * - İngilizce gün adlarını Türkçe'ye çevirir
 * - PHP date('l') ile gün adı alımı
 * - Array mapping ile dil dönüşümü
 * - Fallback mechanism ile güvenlik
 *
 * Format Özellikleri:
 * - Gün Adı + Virgül + Boşluk + YYYY-MM-DD
 * - Örnek: 'Pazartesi, 2025-10-01'
 * - UTF-8 compatible Türkçe karakterler
 * - Human-readable format
 *
 * Kullanım Örneği:
 * $tarih = Turkce_Tarih();
 * echo $tarih; // 'Salı, 2025-10-01'
 * // HTML'de kullanım
 * echo "<h2>Bugün: {$tarih}</h2>";
 *
 * @return string Türkçe gün adı ile birlikte tarih (Gün, YYYY-MM-DD formatında)
 */
function Turkce_Tarih()
{
    $gunler = [
        'Sunday' => 'Pazar',
        'Monday' => 'Pazartesi',
        'Tuesday' => 'Salı',
        'Wednesday' => 'Çarşamba',
        'Thursday' => 'Perşembe',
        'Friday' => 'Cuma',
        'Saturday' => 'Cumartesi'
    ];
    $gunIngilizce = date('l');
    $gunTurkce = $gunler[$gunIngilizce] ?? $gunIngilizce;
    return $gunTurkce . ', ' . date('Y-m-d');
}

/**
 * Özelleştirilebilir Tarih Formatlama Fonksiyonu
 *
 * Bu fonksiyon, verilen tarih/saat değerini istenilen formatta
 * döndürür. PHP date() format kodlarını destekler.
 *
 * Kullanım Alanı:
 * - Custom tarih formatları
 * - Lokalizasyon gereksinimleri
 * - Rapor çıktılarında özel formatlar
 * - API response formatting
 *
 * Teknik Detaylar:
 * - PHP date() format characters desteği
 * - Unix timestamp input/output
 * - Null safety ile current time fallback
 * - Flexible formatting system
 *
 * Format Örnekleri:
 * - 'd/m/Y': 01/10/2025
 * - 'l, F j, Y': Tuesday, October 1, 2025
 * - 'H:i:s': 14:30:25
 * - 'c': 2025-10-01T14:30:25+03:00
 *
 * Kullanım Örneği:
 * $formatted = Tarih_Formatla('d/m/Y H:i');
 * echo $formatted; // '01/10/2025 14:30'
 * // Belirli timestamp için
 * $custom = Tarih_Formatla('l, j F Y', 1696176000);
 *
 * @param string $format PHP date() format string (d, m, Y, H, i, s vb.)
 * @param int|null $timestamp Unix timestamp (null ise current time kullanılır)
 * @return string Formatlanmış tarih/saat string'i
 */
function Tarih_Formatla($format, $timestamp = null)
{
    $timestamp = $timestamp ?? time();
    return date($format, $timestamp);
}

/**
 * İki Tarih Arası Fark Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, iki tarih arasındaki farkı gün, saat, dakika ve
 * saniye cinsinden detaylı şekilde hesaplar ve döndürür.
 *
 * Kullanım Alanı:
 * - Yaş hesaplaması
 * - Süre hesaplamaları
 * - Deadline tracking
 * - Duration analytics
 *
 * Teknik Detaylar:
 * - strtotime() ile string-to-timestamp conversion
 * - abs() ile mutlak değer hesabı
 * - Mathematical breakdown (modulo operations)
 * - Associative array return format
 *
 * Hesaplama Mantığı:
 * - Saniye → Dakika → Saat → Gün dönüşümü
 * - Kalan hesaplamaları (modulo)
 * - Precision: saniye düzeyinde
 * - Always positive result (absolute)
 *
 * Kullanım Örneği:
 * $fark = Tarih_Farki('2025-10-01', '2025-10-05');
 * // Result: ['gun' => 4, 'saat' => 0, 'dakika' => 0, 'saniye' => 0]
 * echo "{$fark['gun']} gün, {$fark['saat']} saat fark var";
 *
 * @param string $tarih1 İlk tarih (PHP strtotime() uyumlu format)
 * @param string $tarih2 İkinci tarih (PHP strtotime() uyumlu format)
 * @return array Fark bilgileri ['gun' => int, 'saat' => int, 'dakika' => int, 'saniye' => int]
 */
function Tarih_Farki($tarih1, $tarih2)
{
    $zaman1 = strtotime($tarih1);
    $zaman2 = strtotime($tarih2);
    $fark = abs($zaman1 - $zaman2);

    $gun = floor($fark / (60 * 60 * 24));
    $saat = floor(($fark % (60 * 60 * 24)) / (60 * 60));
    $dakika = floor(($fark % (60 * 60)) / 60);
    $saniye = $fark % 60;

    return [
        'gun' => $gun,
        'saat' => $saat,
        'dakika' => $dakika,
        'saniye' => $saniye
    ];
}

/**
 * Tarih Manipülasyon Fonksiyonu
 *
 * Bu fonksiyon, verilen bir tarihe belirli bir zaman dilimi ekleyerek
 * veya çıkararak yeni bir tarih hesaplar.
 *
 * Kullanım Alanı:
 * - Tarih aritmetiği işlemleri
 * - Deadline hesaplamaları
 * - Subscription expiry dates
 * - Schedule planning
 *
 * Teknik Detaylar:
 * - PHP strtotime() relative format desteği
 * - Two-step conversion (string→timestamp→string)
 * - Flexible modification strings
 * - MySQL DATETIME compatible output
 *
 * Modification Örnekleri:
 * - '+1 day': Bir gün ekle
 * - '-2 weeks': İki hafta çıkar
 * - '+3 months': Üç ay ekle
 * - 'next Monday': Gelecek Pazartesi
 *
 * Kullanım Örneği:
 * $yeni_tarih = Tarih_Degistir('2025-10-01', '+7 days');
 * echo $yeni_tarih; // '2025-10-08 00:00:00'
 * // Subscription renewal
 * $renewal = Tarih_Degistir($start_date, '+1 year');
 *
 * @param string $tarih Başlangıç tarihi (PHP strtotime() uyumlu)
 * @param string $degisim Değişiklik string'i ('+1 day', '-2 weeks' vb.)
 * @return string Yeni tarih YYYY-MM-DD HH:MM:SS formatında
 */
function Tarih_Degistir($tarih, $degisim)
{
    $zaman = strtotime($degisim, strtotime($tarih));
    return date('Y-m-d H:i:s', $zaman);
}

/**
 * Tarih/Saat Karşılaştırma Fonksiyonu
 *
 * Bu fonksiyon, iki tarih/saat değerini karşılaştırarak
 * hangisinin daha önce olduğunu kullanıcı dostu mesajla belirtir.
 *
 * Kullanım Alanı:
 * - Tarih validasyon kontrolleri
 * - Chronological ordering
 * - Timeline verification
 * - Date range validation
 *
 * Teknik Detaylar:
 * - strtotime() ile timestamp conversion
 * - Three-way comparison logic
 * - Human-readable Turkish messages
 * - String concatenation for output
 *
 * Comparison Results:
 * - Eğer birinci < ikinci: 'birinci daha önce'
 * - Eğer birinci > ikinci: 'ikinci daha önce'
 * - Eğer birinci = ikinci: 'İki tarih/saat eşit'
 * - Invalid input handling
 *
 * Kullanım Örneği:
 * $sonuc = Tarih_Saat_Karsilastir('2025-10-01', '2025-10-05');
 * echo $sonuc; // '2025-10-01 daha önce'
 * // Validation'da kullanım
 * $check = Tarih_Saat_Karsilastir($start_date, $end_date);
 *
 * @param string $birinci İlk tarih/saat (PHP strtotime() uyumlu)
 * @param string $ikinci İkinci tarih/saat (PHP strtotime() uyumlu)
 * @return string Karşılaştırma sonucu Türkçe açıklama ile
 */
function Tarih_Saat_Karsilastir($birinci, $ikinci)
{
    $zaman1 = strtotime($birinci);
    $zaman2 = strtotime($ikinci);

    if ($zaman1 < $zaman2) {
        return "$birinci daha önce";
    } elseif ($zaman1 > $zaman2) {
        return "$ikinci daha önce";
    } else {
        return "İki tarih/saat eşit";
    }
}

/**
 * Haftanın Günü Numarası Fonksiyonu
 *
 * Bu fonksiyon, bugünün haftanın kaçıncı günü olduğunu
 * ISO 8601 standardına göre (1=Pazartesi) döndürür.
 *
 * Kullanım Alanı:
 * - İş günü kontrolleri
 * - Hafta sonu/hafta içi ayırımı
 * - Calendar applications
 * - Work schedule planning
 *
 * Teknik Detaylar:
 * - PHP date('N') format character
 * - ISO 8601 week date standard
 * - Monday-based week start
 * - Integer return type (1-7)
 *
 * Gün Kodları:
 * - 1: Pazartesi (Monday)
 * - 2: Salı (Tuesday)
 * - 3: Çarşamba (Wednesday)
 * - 4: Perşembe (Thursday)
 * - 5: Cuma (Friday)
 * - 6: Cumartesi (Saturday)
 * - 7: Pazar (Sunday)
 *
 * Kullanım Örneği:
 * $gun_no = Haftanin_Gunu();
 * if ($gun_no <= 5) {
 *     echo 'İş günü';
 * } else {
 *     echo 'Hafta sonu';
 * }
 *
 * @return string Gün numarası (1=Pazartesi, 7=Pazar)
 */
function Haftanin_Gunu()
{
    return date('N');
}

/**
 * Ayın Günü Numarası Fonksiyonu
 *
 * Bu fonksiyon, bugünün ay içerisinde kaçıncı günü
 * olduğunu leading zero olmadan döndürür.
 *
 * Kullanım Alanı:
 * - Monthly reports
 * - Payment due date calculations
 * - Calendar grid positioning
 * - Day-based conditionals
 *
 * Teknik Detaylar:
 * - PHP date('j') format character
 * - No leading zeros (1-31)
 * - Month-relative positioning
 * - Integer string return
 *
 * Değer Aralığı:
 * - Minimum: 1 (ayın ilk günü)
 * - Maximum: 31 (aya bağlı olarak 28-31)
 * - Format: Single/double digit
 * - Type: String representation
 *
 * Kullanım Örneği:
 * $gun = Ayin_Gunu();
 * echo "Ayın {$gun}. günü"; // 'Ayın 1. günü'
 * // Özel gün kontrolü
 * if ($gun == '15') {
 *     echo 'Ayın ortası';
 * }
 *
 * @return string Ay içindeki gün numarası (1-31, leading zero yok)
 */
function Ayin_Gunu()
{
    return date('j');
}

/**
 * Yılın Günü Numarası Fonksiyonu
 *
 * Bu fonksiyon, bugünün yıl içerisinde kaçıncı günü
 * olduğunu 1-based indexing ile döndürür.
 *
 * Kullanım Alanı:
 * - Yıllık progress tracking
 * - Julian day calculations
 * - Seasonal analysis
 * - Annual statistics
 *
 * Teknik Detaylar:
 * - PHP date('z') + 1 (0-based'den 1-based'e)
 * - Leap year consideration
 * - January 1st = Day 1
 * - December 31st = Day 365/366
 *
 * Değer Aralığı:
 * - Normal yıl: 1-365
 * - Artık yıl: 1-366
 * - Örnek: 1 Ocak = 1, 31 Aralık = 365/366
 * - Automatic leap year adjustment
 *
 * Kullanım Örneği:
 * $gun = Yilin_Gunu();
 * echo "Yılın {$gun}. günü"; // 'Yılın 274. günü'
 * // Progress calculation
 * $progress = ($gun / 365) * 100;
 * echo "Yılın %{$progress}'i geçti";
 *
 * @return int Yıl içindeki gün numarası (1-365/366)
 */
function Yilin_Gunu()
{
    return date('z') + 1;
}

/**
 * Unix Timestamp Alma Fonksiyonu
 *
 * Bu fonksiyon, belirli bir tarih için Unix timestamp değeri döndürür.
 * Mikrosaniye desteği ve esnek input options sağlar.
 *
 * Kullanım Alanı:
 * - Database timestamp storage
 * - API epoch time generation
 * - Performance benchmarking
 * - Cache expiry calculations
 *
 * Teknik Detaylar:
 * - Standard Unix timestamp (seconds since 1970-01-01)
 * - Microtime option (float with microseconds)
 * - strtotime() parsing for date strings
 * - Current time fallback mechanism
 *
 * Timestamp Types:
 * - Normal: Integer seconds (time())
 * - Micro: Float with microseconds (microtime(true))
 * - Custom: From date string (strtotime())
 * - Current: Default behavior
 *
 * Kullanım Örneği:
 * $stamp = Zaman_Damgasi();
 * echo $stamp; // 1696176000
 * // Mikrosaniye ile
 * $micro = Zaman_Damgasi(null, true);
 * // Belirli tarih için
 * $custom = Zaman_Damgasi('2025-10-01');
 *
 * @param string|null $tarih Tarih string'i (null ise current time)
 * @param bool $mikro Mikrosaniye desteği (true ise float döner)
 * @return int|float Unix timestamp (mikro=false: int, mikro=true: float)
 */
function Zaman_Damgasi($tarih = null, $mikro = false)
{
    if ($mikro) {
        return microtime(true);
    }
    return $tarih ? strtotime($tarih) : time();
}

/**
 * Kronometre Başlatma Fonksiyonu (Deprecated)
 *
 * Bu fonksiyon, performans ölçümü için başlangıç zamanını
 * mikrosaniye hassasiyetinde alır. Geriye uyumluluk için korunmuştur.
 *
 * Kullanım Alanı:
 * - Legacy code support
 * - Performance measurement başlangıcı
 * - Execution time tracking
 * - Benchmark test setup
 *
 * Teknik Detaylar:
 * - Zaman_Damgasi(null, true) wrapper'i
 * - Microtime(true) indirect call
 * - Float precision timestamp
 * - Backward compatibility layer
 *
 * Deprecation Note:
 * - Bu fonksiyon gelecek versiyonlarda kaldırılabilir
 * - Zaman_Damgasi(null, true) kullanımı önerilir
 * - Legacy support only
 * - New code'da kullanılmamalı
 *
 * Kullanım Örneği:
 * $start = Cronometer_Baslat();
 * // İşlemler...
 * $end = microtime(true);
 * $duration = $end - $start;
 *
 * @deprecated Zaman_Damgasi(null, true) kullanın
 * @return float Mikrosaniye hassasiyetinde timestamp
 */
// Geriye uyumluluk için
function Cronometer_Baslat()
{
    return Zaman_Damgasi(null, true);
}

/**
 * UTC Zamanı Alma Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihi veya mevcut zamanı UTC (Greenwich Mean Time)
 * timezone'ında YYYY-MM-DD HH:MM:SS formatında döndürür.
 *
 * Kullanım Alanı:
 * - International applications
 * - Database normalization
 * - API standardization
 * - Cross-timezone compatibility
 *
 * Teknik Detaylar:
 * - gmdate() kullanımı (GMT/UTC)
 * - Local timezone'dan bağımsız
 * - ISO 8601 partial compliance
 * - Server timezone etkisiz
 *
 * UTC Özellikleri:
 * - Coordinated Universal Time
 * - Timezone offset: +00:00
 * - No daylight saving effects
 * - International standard
 *
 * Kullanım Örneği:
 * $utc_now = UTC_Zamani();
 * echo $utc_now; // '2025-10-01 11:30:25' (UTC)
 * // Belirli tarih için
 * $utc_date = UTC_Zamani('2025-10-01 14:30:25');
 * // API response'da kullanım
 * $api_data['timestamp'] = UTC_Zamani();
 *
 * @param string|null $tarih Dönüştürülecek tarih (null ise current time)
 * @return string UTC timezone'ında YYYY-MM-DD HH:MM:SS formatında tarih
 */
function UTC_Zamani($tarih = null)
{
    return $tarih ? gmdate('Y-m-d H:i:s', strtotime($tarih)) : gmdate('Y-m-d H:i:s');
}

/**
 * Saat Dilimi Dönüştürme Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihi belirtilen saat dilimine çevirerek
 * o timezone'daki karşılığını döndürür.
 *
 * Kullanım Alanı:
 * - Multi-timezone applications
 * - Global user interfaces
 * - Meeting scheduling systems
 * - International data conversion
 *
 * Teknik Detaylar:
 * - DateTime ve DateTimeZone class'ları
 * - PHP timezone identifier support
 * - Automatic DST handling
 * - Precise timezone conversion
 *
 * Desteklenen Timezone'lar:
 * - Europe/Istanbul (Türkiye, UTC+3)
 * - America/New_York (UTC-5/-4)
 * - Asia/Tokyo (UTC+9)
 * - UTC (UTC+0)
 *
 * Kullanım Örneği:
 * $istanbul = Saat_Dilimine_Cevir('2025-10-01 12:00:00 UTC');
 * echo $istanbul; // '2025-10-01 15:00:00'
 * // Farklı timezone
 * $tokyo = Saat_Dilimine_Cevir('2025-10-01 12:00:00', 'Asia/Tokyo');
 * // User localization
 * $user_time = Saat_Dilimine_Cevir($utc_time, $user_timezone);
 *
 * @param string $tarih Dönüştürülecek tarih (timezone bilgisi ile)
 * @param string $hedef_saat_dilimi Hedef timezone identifier (default: 'Europe/Istanbul')
 * @return string Hedef timezone'daki YYYY-MM-DD HH:MM:SS formatında tarih
 */
function Saat_Dilimine_Cevir($tarih, $hedef_saat_dilimi = 'Europe/Istanbul')
{
    $dt = new DateTime($tarih);
    $dt->setTimezone(new DateTimeZone($hedef_saat_dilimi));
    return $dt->format('Y-m-d H:i:s');
}

/**
 * Geçen Zaman Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihten bu yana geçen zamanı
 * human-readable Türkçe formatında döndürür.
 *
 * Kullanım Alanı:
 * - Social media post timestamps
 * - Comment/activity feeds
 * - Relative time display
 * - User-friendly time formatting
 *
 * Teknik Detaylar:
 * - Current time vs given time comparison
 * - Hierarchical time unit selection
 * - Turkish language output
 * - Smart unit switching logic
 *
 * Time Units & Thresholds:
 * - < 60 saniye: 'X saniye önce'
 * - < 3600 (1 saat): 'X dakika önce'
 * - < 86400 (1 gün): 'X saat önce'
 * - < 2592000 (30 gün): 'X gün önce'
 * - < 31536000 (1 yıl): 'X ay önce'
 * - >= 1 yıl: 'X yıl önce'
 *
 * Kullanım Örneği:
 * $gecen = Gecen_Zaman('2025-09-30 14:25:00');
 * echo $gecen; // '5 dakika önce'
 * // Blog post'ta kullanım
 * echo "Yayınlanma: " . Gecen_Zaman($post_date);
 *
 * @param string $tarih Geçmiş tarihi (PHP strtotime() uyumlu)
 * @return string Human-readable geçen zaman Türkçe formatında
 */
function Gecen_Zaman($tarih)
{
    $simdi = time();
    $gecmis = strtotime($tarih);
    $fark = $simdi - $gecmis;

    if ($fark < 60) {
        return $fark . ' saniye önce';
    } elseif ($fark < 3600) {
        return floor($fark / 60) . ' dakika önce';
    } elseif ($fark < 86400) {
        return floor($fark / 3600) . ' saat önce';
    } elseif ($fark < 2592000) {
        return floor($fark / 86400) . ' gün önce';
    } elseif ($fark < 31536000) {
        return floor($fark / 2592000) . ' ay önce';
    } else {
        return floor($fark / 31536000) . ' yıl önce';
    }
}

/**
 * Kalan Zaman Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, belirtilen hedef tarihe kadar kalan zamanı
 * detaylı şekilde (gün, saat, dakika, saniye) hesaplar.
 *
 * Kullanım Alanı:
 * - Countdown timers
 * - Event deadline tracking
 * - Promotion expiry displays
 * - Project milestone monitoring
 *
 * Teknik Detaylar:
 * - Future time vs current time comparison
 * - Mathematical breakdown calculation
 * - Zero/negative time handling
 * - Formatted string output
 *
 * Output Format:
 * - Positive: 'X gün, Y saat, Z dakika, W saniye kaldı'
 * - Zero/Negative: 'Süre doldu'
 * - Components: Days, Hours, Minutes, Seconds
 * - Turkish language formatting
 *
 * Kullanım Örneği:
 * $kalan = Kalan_Zaman('2025-12-31 23:59:59');
 * echo $kalan; // '91 gün, 9 saat, 29 dakika, 34 saniye kaldı'
 * // Expired event
 * $expired = Kalan_Zaman('2025-09-01');
 * echo $expired; // 'Süre doldu'
 *
 * @param string $hedef_tarih Hedef tarih (PHP strtotime() uyumlu)
 * @return string Kalan zaman açıklaması veya 'Süre doldu'
 */
function Kalan_Zaman($hedef_tarih)
{
    $simdi = time();
    $hedef = strtotime($hedef_tarih);
    $fark = $hedef - $simdi;

    if ($fark <= 0) {
        return 'Süre doldu';
    }

    $gun = floor($fark / 86400);
    $saat = floor(($fark % 86400) / 3600);
    $dakika = floor(($fark % 3600) / 60);
    $saniye = $fark % 60;

    return "{$gun} gün, {$saat} saat, {$dakika} dakika, {$saniye} saniye kaldı";
}

/**
 * Yaş Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, doğum tarihinden bugüne kadar geçen
 * yıl sayısını (yaşı) hassas şekilde hesaplar.
 *
 * Kullanım Alanı:
 * - User profile systems
 * - Age verification
 * - Demographic analysis
 * - Birthday reminders
 *
 * Teknik Detaylar:
 * - DateTime class ile hassas hesaplama
 * - DateInterval diff() method
 * - Leap year consideration
 * - Day-level precision
 *
 * Calculation Logic:
 * - Doğum tarihi vs bugün karşılaştırma
 * - Tam yıl hesaplama (birthday gereksinimi)
 * - Otomatik leap year adjustment
 * - Integer yaş return
 *
 * Kullanım Örneği:
 * $yas = Yas_Hesapla('1990-05-15');
 * echo $yas; // 35 (eğer bugün 2025-10-01 ise)
 * // Age validation
 * if (Yas_Hesapla($birth_date) >= 18) {
 *     echo 'Yetişkin kullanıcı';
 * }
 *
 * @param string $dogum_tarihi Doğum tarihi (YYYY-MM-DD formatında)
 * @return int Hesaplanan yaş (tam yıl)
 */
function Yas_Hesapla($dogum_tarihi)
{
    $dogum = new DateTime($dogum_tarihi);
    $bugun = new DateTime();
    $fark = $bugun->diff($dogum);
    return $fark->y;
}

/**
 * Türkçe Ay Adı Fonksiyonu
 *
 * Bu fonksiyon, verilen ay numarasına karşılık gelen
 * Türkçe ay adını döndürür.
 *
 * Kullanım Alanı:
 * - Tarih formatlarında localization
 * - Rapor başlıklarında ay isimleri
 * - Calendar applications
 * - Date picker Turkish labels
 *
 * Teknik Detaylar:
 * - Associative array mapping
 * - 1-based month indexing (1=Ocak)
 * - Current month fallback
 * - Error handling with 'Geçersiz ay'
 *
 * Ay Listesi:
 * - 1: Ocak, 2: Şubat, 3: Mart, 4: Nisan
 * - 5: Mayıs, 6: Haziran, 7: Temmuz, 8: Ağustos
 * - 9: Eylül, 10: Ekim, 11: Kasım, 12: Aralık
 * - Invalid: 'Geçersiz ay'
 *
 * Kullanım Örneği:
 * $ay = Ay_Adi(10);
 * echo $ay; // 'Ekim'
 * // Mevcut ay
 * $bu_ay = Ay_Adi();
 * // Tarih formatında
 * echo date('j') . ' ' . Ay_Adi() . ' ' . date('Y');
 *
 * @param int|null $ay_numarasi Ay numarası (1-12, null ise mevcut ay)
 * @return string Türkçe ay adı veya 'Geçersiz ay'
 */
function Ay_Adi($ay_numarasi = null)
{
    $aylar = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
    ];
    $ay = $ay_numarasi ?? date('n');
    return $aylar[$ay] ?? 'Geçersiz ay';
}

/**
 * Türkçe Gün Adı Fonksiyonu
 *
 * Bu fonksiyon, verilen gün numarasına karşılık gelen
 * Türkçe gün adını döndürür.
 *
 * Kullanım Alanı:
 * - Calendar widget localization
 * - Weekly schedule displays
 * - Date formatting in Turkish
 * - Day-based conditional messages
 *
 * Teknik Detaylar:
 * - ISO 8601 week standard (1=Pazartesi)
 * - Associative array mapping
 * - Current day fallback
 * - Error handling with 'Geçersiz gün'
 *
 * Gün Listesi:
 * - 1: Pazartesi (Monday)
 * - 2: Salı (Tuesday)
 * - 3: Çarşamba (Wednesday)
 * - 4: Perşembe (Thursday)
 * - 5: Cuma (Friday)
 * - 6: Cumartesi (Saturday)
 * - 7: Pazar (Sunday)
 *
 * Kullanım Örneği:
 * $gun = Gun_Adi(1);
 * echo $gun; // 'Pazartesi'
 * // Bugünün adı
 * $bugun = Gun_Adi();
 * // Haftalık program
 * for ($i = 1; $i <= 7; $i++) {
 *     echo Gun_Adi($i) . "\n";
 * }
 *
 * @param int|null $gun_numarasi Gün numarası (1-7, null ise bugün)
 * @return string Türkçe gün adı veya 'Geçersiz gün'
 */
function Gun_Adi($gun_numarasi = null)
{
    $gunler = [
        1 => 'Pazartesi', 2 => 'Salı', 3 => 'Çarşamba', 4 => 'Perşembe',
        5 => 'Cuma', 6 => 'Cumartesi', 7 => 'Pazar'
    ];
    $gun = $gun_numarasi ?? date('N');
    return $gunler[$gun] ?? 'Geçersiz gün';
}

/**
 * Gün Tipi Belirleme Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihin iş günü mü yoksa hafta sonu mu
 * olduğunu belirleyerek kategorize eder.
 *
 * Kullanım Alanı:
 * - İş günü hesaplamaları
 * - Working hours validation
 * - Business logic decisions
 * - Schedule management
 *
 * Teknik Detaylar:
 * - ISO 8601 week day standard
 * - Range-based categorization
 * - String identifier return
 * - Flexible date input
 *
 * Return Categories:
 * - 'is_gunu': Pazartesi-Cuma (1-5)
 * - 'hafta_sonu': Cumartesi-Pazar (6-7)
 * - 'bilinmeyen': Invalid input fallback
 * - Case-sensitive string identifiers
 *
 * Kullanım Örneği:
 * $tip = Gun_Tipi();
 * if ($tip === 'is_gunu') {
 *     echo 'Ofis açık';
 * } elseif ($tip === 'hafta_sonu') {
 *     echo 'Hafta sonu';
 * }
 * // Belirli tarih için
 * $tip = Gun_Tipi('2025-10-05'); // Pazar
 *
 * @param string|null $tarih Kontrol edilecek tarih (null ise bugün)
 * @return string Gün tipi ('is_gunu', 'hafta_sonu', 'bilinmeyen')
 */
function Gun_Tipi($tarih = null)
{
    $gun = $tarih ? date('N', strtotime($tarih)) : date('N');

    if ($gun >= 1 && $gun <= 5) {
        return 'is_gunu';
    } elseif ($gun == 6 || $gun == 7) {
        return 'hafta_sonu';
    }
    return 'bilinmeyen';
}

/**
 * Hafta Sonu Kontrol Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihin hafta sonu (Cumartesi veya Pazar)
 * olup olmadığını boolean değer olarak döndürür.
 *
 * Kullanım Alanı:
 * - Weekend-specific operations
 * - Business hours validation
 * - Conditional content display
 * - Schedule filtering
 *
 * Teknik Detaylar:
 * - Gun_Tipi() wrapper function
 * - Boolean return type
 * - Simple true/false logic
 * - Backward compatibility layer
 *
 * Logic:
 * - Cumartesi (6) → true
 * - Pazar (7) → true
 * - Pazartesi-Cuma (1-5) → false
 * - Invalid input → false
 *
 * Kullanım Örneği:
 * if (Hafta_Sonu_mu()) {
 *     echo 'Hafta sonu indirimi aktif!';
 * }
 * // Belirli tarih kontrolü
 * $weekend = Hafta_Sonu_mu('2025-10-05');
 * var_dump($weekend); // true (Pazar)
 *
 * @param string|null $tarih Kontrol edilecek tarih (null ise bugün)
 * @return bool Hafta sonu ise true, değilse false
 */

function Hafta_Sonu_mu($tarih = null)
{
    return Gun_Tipi($tarih) === 'hafta_sonu';
}
/**
 * İş Günü Kontrol Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihin iş günü (Pazartesi-Cuma)
 * olup olmadığını boolean değer olarak döndürür.
 *
 * Kullanım Alanı:
 * - Business day calculations
 * - Working hours validation
 * - Office operations scheduling
 * - Workday-specific features
 *
 * Teknik Detaylar:
 * - Gun_Tipi() wrapper function
 * - Boolean return type
 * - Inverse of weekend check
 * - Backward compatibility support
 *
 * Logic:
 * - Pazartesi-Cuma (1-5) → true
 * - Cumartesi-Pazar (6-7) → false
 * - Invalid input → false
 * - Standard business week
 *
 * Kullanım Örneği:
 * if (Is_Gunu_mu()) {
 *     echo 'Destek hattı açık';
 * } else {
 *     echo 'Hafta sonu - destek kapalı';
 * }
 * // Tarih validasyon
 * $is_workday = Is_Gunu_mu('2025-10-01');
 *
 * @param string|null $tarih Kontrol edilecek tarih (null ise bugün)
 * @return bool İş günü ise true, değilse false
 */
function Is_Gunu_mu($tarih = null)
{
    return Gun_Tipi($tarih) === 'is_gunu';
}

/**
 * Dönem Tarih Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, belirli dönem tipine göre (ay başı, ay sonu vb.)
 * özel tarihleri hesaplayarak döndürür.
 *
 * Kullanım Alanı:
 * - Financial period calculations
 * - Report date ranges
 * - Billing cycle management
 * - Archive date planning
 *
 * Teknik Detaylar:
 * - Switch-case logic for period types
 * - PHP date() format manipulation
 * - Flexible input date support
 * - Standard date format output
 *
 * Supported Period Types:
 * - 'ay_bas': Ayın ilk günü (YYYY-MM-01)
 * - 'ay_son': Ayın son günü (YYYY-MM-XX)
 * - 'yil_bas': Yılın ilk günü (YYYY-01-01)
 * - 'yil_son': Yılın son günü (YYYY-12-31)
 * - Default: Verilen tarihin kendisi
 *
 * Kullanım Örneği:
 * $ay_basi = Donem_Tarih('ay_bas', '2025-10-15');
 * echo $ay_basi; // '2025-10-01'
 * $yil_sonu = Donem_Tarih('yil_son');
 * // Rapor dönemi
 * $start = Donem_Tarih('ay_bas', $report_date);
 * $end = Donem_Tarih('ay_son', $report_date);
 *
 * @param string $tip Dönem tipi ('ay_bas', 'ay_son', 'yil_bas', 'yil_son')
 * @param string|null $tarih Referans tarih (null ise bugün)
 * @return string Hesaplanan dönem tarihi YYYY-MM-DD formatında
 */
function Donem_Tarih($tip, $tarih = null)
{
    $zaman = $tarih ? strtotime($tarih) : time();

    switch ($tip) {
        case 'ay_bas':
            return date('Y-m-01', $zaman);
        case 'ay_son':
            return date('Y-m-t', $zaman);
        case 'yil_bas':
            return date('Y-01-01', $zaman);
        case 'yil_son':
            return date('Y-12-31', $zaman);
        default:
            return date('Y-m-d', $zaman);
    }
}

/**
 * Ay Başlangıcı Tarihi Fonksiyonu (Deprecated)
 *
 * Bu fonksiyon, verilen tarihin bulunduğu ayın ilk gününü
 * döndürür. Geriye uyumluluk için korunmuştur.
 *
 * Kullanım Alanı:
 * - Legacy code support
 * - Monthly period calculations
 * - Billing cycle start dates
 * - Report period beginning
 *
 * Teknik Detaylar:
 * - Donem_Tarih('ay_bas') wrapper
 * - Backward compatibility layer
 * - Simple month first day calculation
 * - Standard date format output
 *
 * Deprecation Note:
 * - Donem_Tarih('ay_bas', $tarih) kullanımı önerilir
 * - Gelecek versiyonlarda kaldırılabilir
 * - Legacy support only
 * - New development'ta kullanılmamalı
 *
 * Kullanım Örneği:
 * $ay_bas = Ay_Baslangici('2025-10-15');
 * echo $ay_bas; // '2025-10-01'
 * // Mevcut ay başı
 * $bu_ay_bas = Ay_Baslangici();
 *
 * @deprecated Donem_Tarih('ay_bas', $tarih) kullanın
 * @param string|null $tarih Referans tarih (null ise bugün)
 * @return string Ayın ilk günü YYYY-MM-01 formatında
 */

function Ay_Baslangici($tarih = null)
{
    return Donem_Tarih('ay_bas', $tarih);
}
/**
 * Ay Sonu Tarihi Fonksiyonu (Deprecated)
 *
 * Bu fonksiyon, verilen tarihin bulunduğu ayın son gününü
 * döndürür. Geriye uyumluluk için korunmuştur.
 *
 * Kullanım Alanı:
 * - Legacy code support
 * - Monthly period end calculations
 * - Billing cycle end dates
 * - Report period closing
 *
 * Teknik Detaylar:
 * - Donem_Tarih('ay_son') wrapper
 * - Automatic month length calculation
 * - Leap year February handling (28/29)
 * - Standard date format output
 *
 * Deprecation Note:
 * - Donem_Tarih('ay_son', $tarih) kullanımı önerilir
 * - Gelecek versiyonlarda kaldırılabilir
 * - Legacy support only
 * - New development'ta kullanılmamalı
 *
 * Kullanım Örneği:
 * $ay_son = Ay_Sonu('2025-02-15');
 * echo $ay_son; // '2025-02-28'
 * // Mevcut ay sonu
 * $bu_ay_son = Ay_Sonu();
 *
 * @deprecated Donem_Tarih('ay_son', $tarih) kullanın
 * @param string|null $tarih Referans tarih (null ise bugün)
 * @return string Ayın son günü YYYY-MM-DD formatında
 */
function Ay_Sonu($tarih = null)
{
    return Donem_Tarih('ay_son', $tarih);
}

/**
 * Yıl Başlangıcı Tarihi Fonksiyonu (Deprecated)
 *
 * Bu fonksiyon, verilen tarihin bulunduğu yılın ilk gününü
 * (1 Ocak) döndürür. Geriye uyumluluk için korunmuştur.
 *
 * Kullanım Alanı:
 * - Legacy code support
 * - Annual period calculations
 * - Fiscal year start dates
 * - Yearly report periods
 *
 * Teknik Detaylar:
 * - Donem_Tarih('yil_bas') wrapper
 * - Fixed January 1st calculation
 * - Year extraction from input
 * - Standard date format output
 *
 * Deprecation Note:
 * - Donem_Tarih('yil_bas', $tarih) kullanımı önerilir
 * - Gelecek versiyonlarda kaldırılabilir
 * - Legacy support only
 * - New development'ta kullanılmamalı
 *
 * Kullanım Örneği:
 * $yil_bas = Yil_Baslangici('2025-10-15');
 * echo $yil_bas; // '2025-01-01'
 * // Mevcut yıl başı
 * $bu_yil_bas = Yil_Baslangici();
 *
 * @deprecated Donem_Tarih('yil_bas', $tarih) kullanın
 * @param string|null $tarih Referans tarih (null ise bugün)
 * @return string Yılın ilk günü YYYY-01-01 formatında
 */
function Yil_Baslangici($tarih = null)
{
    return Donem_Tarih('yil_bas', $tarih);
}

/**
 * Yıl Sonu Tarihi Fonksiyonu (Deprecated)
 *
 * Bu fonksiyon, verilen tarihin bulunduğu yılın son gününü
 * (31 Aralık) döndürür. Geriye uyumluluk için korunmuştur.
 *
 * Kullanım Alanı:
 * - Legacy code support
 * - Annual period end calculations
 * - Fiscal year closing dates
 * - Year-end report periods
 *
 * Teknik Detaylar:
 * - Donem_Tarih('yil_son') wrapper
 * - Fixed December 31st calculation
 * - Year extraction from input
 * - Standard date format output
 *
 * Deprecation Note:
 * - Donem_Tarih('yil_son', $tarih) kullanımı önerilir
 * - Gelecek versiyonlarda kaldırılabilir
 * - Legacy support only
 * - New development'ta kullanılmamalı
 *
 * Kullanım Örneği:
 * $yil_son = Yil_Sonu('2025-10-15');
 * echo $yil_son; // '2025-12-31'
 * // Mevcut yıl sonu
 * $bu_yil_son = Yil_Sonu();
 *
 * @deprecated Donem_Tarih('yil_son', $tarih) kullanın
 * @param string|null $tarih Referans tarih (null ise bugün)
 * @return string Yılın son günü YYYY-12-31 formatında
 */
function Yil_Sonu($tarih = null)
{
    return Donem_Tarih('yil_son', $tarih);
}

/**
 * Artık Yıl Kontrol Fonksiyonu
 *
 * Bu fonksiyon, verilen yılın artık yıl olup olmadığını
 * Gregorian calendar kurallaına göre hesaplar.
 *
 * Kullanım Alanı:
 * - Calendar applications
 * - Date validation systems
 * - February day count calculations
 * - Historical date analysis
 *
 * Teknik Detaylar:
 * - Gregorian calendar leap year rules
 * - Mathematical modulo operations
 * - Compound boolean logic
 * - Current year fallback
 *
 * Leap Year Rules:
 * - 4'e bölünüyor VE 100'e bölünmüyor → Artık
 * - VEYA 400'e bölünüyor → Artık
 * - Diğer durumlar → Artık değil
 * - Örnek: 2024=Artık, 1900=Değil, 2000=Artık
 *
 * Kullanım Örneği:
 * $leap = Artik_Yil_mu(2024);
 * var_dump($leap); // true
 * // Şubat gün sayısı hesabı
 * $subat_gunleri = Artik_Yil_mu($year) ? 29 : 28;
 * // Mevcut yıl kontrolü
 * if (Artik_Yil_mu()) {
 *     echo 'Bu yıl artık yıl';
 * }
 *
 * @param int|null $yil Kontrol edilecek yıl (null ise mevcut yıl)
 * @return bool Artık yıl ise true, değilse false
 */
function Artik_Yil_mu($yil = null)
{
    $yil = $yil ?? date('Y');
    return (($yil % 4 == 0 && $yil % 100 != 0) || ($yil % 400 == 0));
}

/**
 * Aydaki Gün Sayısı Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, belirtilen ay ve yıl için o ayın kaç gün
 * olduğunu Gregorian calendar'a göre hesaplar.
 *
 * Kullanım Alanı:
 * - Calendar grid generation
 * - Date range validation
 * - Monthly report calculations
 * - Billing period planning
 *
 * Teknik Detaylar:
 * - PHP cal_days_in_month() function
 * - CAL_GREGORIAN calendar system
 * - Automatic leap year consideration
 * - Current month/year fallback
 *
 * Monthly Day Counts:
 * - Ocak, Mart, Mayıs, Temmuz, Ağustos, Ekim, Aralık: 31
 * - Nisan, Haziran, Eylül, Kasım: 30
 * - Şubat: 28 (normal), 29 (artık yıl)
 * - Automatic leap year adjustment
 *
 * Kullanım Örneği:
 * $gun_sayisi = Aydaki_Gun_Sayisi(2, 2024);
 * echo $gun_sayisi; // 29 (Şubat 2024 - artık yıl)
 * // Mevcut ay
 * $bu_ay_gunleri = Aydaki_Gun_Sayisi();
 * // Calendar loop
 * for ($i = 1; $i <= Aydaki_Gun_Sayisi($month, $year); $i++) {
 *     echo $i . " ";
 * }
 *
 * @param int|null $ay Ay numarası (1-12, null ise mevcut ay)
 * @param int|null $yil Yıl (null ise mevcut yıl)
 * @return int Aydaki toplam gün sayısı (28-31)
 */
function Aydaki_Gun_Sayisi($ay = null, $yil = null)
{
    $ay = $ay ?? date('n');
    $yil = $yil ?? date('Y');
    return cal_days_in_month(CAL_GREGORIAN, $ay, $yil);
}

/**
 * Tarih Geçerlilik Kontrol Fonksiyonu
 *
 * Bu fonksiyon, verilen tarih string'inin geçerli bir tarih formatı
 * olup olmadığını kontrol eder ve boolean değer döndürür.
 *
 * Kullanım Alanı:
 * - Form input validation
 * - User data sanitization
 * - API parameter checking
 * - Date format verification
 *
 * Teknik Detaylar:
 * - strtotime() parsing ability test
 * - False return value detection
 * - Format-agnostic validation
 * - Multiple date format support
 *
 * Supported Formats:
 * - YYYY-MM-DD (ISO format)
 * - MM/DD/YYYY (US format)
 * - DD-MM-YYYY (European format)
 * - Natural language ('tomorrow', 'last week')
 *
 * Kullanım Örneği:
 * $valid = Tarih_Gecerli_mi('2025-10-01');
 * var_dump($valid); // true
 * $invalid = Tarih_Gecerli_mi('2025-13-45');
 * var_dump($invalid); // false
 * // Form validation
 * if (!Tarih_Gecerli_mi($_POST['birth_date'])) {
 *     echo 'Geçersiz tarih formatı';
 * }
 *
 * @param string $tarih Kontrol edilecek tarih string'i
 * @return bool Geçerli tarih ise true, değilse false
 */
function Tarih_Gecerli_mi($tarih)
{
    $zaman = strtotime($tarih);
    return ($zaman !== false);
}

/**
 * Mevsim Belirleme Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihin hangi mevsime ait olduğunu
 * astronomik mevsim geçişleri baz alınarak belirler.
 *
 * Kullanım Alanı:
 * - Seasonal content management
 * - Weather-based applications
 * - Marketing campaign timing
 * - Theme/design switching
 *
 * Teknik Detaylar:
 * - Astronomical season transitions
 * - Day-level precision
 * - Northern hemisphere seasons
 * - Turkish season names
 *
 * Season Boundaries (Approximate):
 * - Kış: 21 Aralık - 19 Mart
 * - İlkbahar: 20 Mart - 20 Haziran
 * - Yaz: 21 Haziran - 22 Eylül
 * - Sonbahar: 23 Eylül - 20 Aralık
 *
 * Kullanım Örneği:
 * $mevsim = Mevsim('2025-07-15');
 * echo $mevsim; // 'Yaz'
 * // Seasonal styling
 * $season_class = strtolower(Mevsim());
 * echo "<body class='season-{$season_class}'>";
 * // Conditional content
 * if (Mevsim() === 'Kış') {
 *     echo 'Kış indirimleri başladı!';
 * }
 *
 * @param string|null $tarih Kontrol edilecek tarih (null ise bugün)
 * @return string Mevsim adı ('Kış', 'İlkbahar', 'Yaz', 'Sonbahar')
 */
function Mevsim($tarih = null)
{
    $ay = $tarih ? date('n', strtotime($tarih)) : date('n');
    $gun = $tarih ? date('j', strtotime($tarih)) : date('j');

    if (($ay == 12 && $gun >= 21) || $ay == 1 || $ay == 2 || ($ay == 3 && $gun < 20)) {
        return 'Kış';
    } elseif (($ay == 3 && $gun >= 20) || $ay == 4 || $ay == 5 || ($ay == 6 && $gun < 21)) {
        return 'İlkbahar';
    } elseif (($ay == 6 && $gun >= 21) || $ay == 7 || $ay == 8 || ($ay == 9 && $gun < 23)) {
        return 'Yaz';
    } else {
        return 'Sonbahar';
    }
}

/**
 * ISO Hafta Numarası Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihin yıl içerisindeki ISO 8601
 * standardına göre hafta numarasını döndürür.
 *
 * Kullanım Alanı:
 * - Weekly report generation
 * - Project timeline management
 * - Calendar week display
 * - ISO standard compliance
 *
 * Teknik Detaylar:
 * - ISO 8601 week date system
 * - Monday-based week start
 * - Week 1 contains January 4th
 * - Range: 1-53 (rarely 53)
 *
 * ISO Week Rules:
 * - Hafta Pazartesi günü başlar
 * - 1. hafta 4 Ocak'ı içerir
 * - 1. hafta yılın en az 4 gününü içerir
 * - Son hafta 52 veya 53 olabilir
 *
 * Kullanım Örneği:
 * $hafta = Hafta_Numarasi('2025-10-01');
 * echo $hafta; // '40' (yaklaşık)
 * // Haftalık rapor
 * $current_week = Hafta_Numarasi();
 * echo "Bu hafta: {$current_week}. hafta";
 * // Week range calculation
 * $week_start = Hafta_Numarasi($period_start);
 *
 * @param string|null $tarih Kontrol edilecek tarih (null ise bugün)
 * @return string ISO hafta numarası (1-53)
 */
function Hafta_Numarasi($tarih = null)
{
    return $tarih ? date('W', strtotime($tarih)) : date('W');
}

/**
 * ISO 8601 Tarih Formatı Fonksiyonu
 *
 * Bu fonksiyon, verilen tarihi ISO 8601 standardında
 * (YYYY-MM-DDTHH:MM:SS+TZ) tam format ile döndürür.
 *
 * Kullanım Alanı:
 * - API response formatting
 * - International data exchange
 * - XML/JSON timestamp fields
 * - Cross-system compatibility
 *
 * Teknik Detaylar:
 * - PHP date('c') format character
 * - Complete ISO 8601 compliance
 * - Timezone information included
 * - Machine-readable format
 *
 * ISO 8601 Format:
 * - Date: YYYY-MM-DD
 * - Time: THH:MM:SS
 * - Timezone: +HH:MM veya Z (UTC)
 * - Örnek: 2025-10-01T14:30:25+03:00
 *
 * Kullanım Örneği:
 * $iso_date = ISO_Tarihi();
 * echo $iso_date; // '2025-10-01T14:30:25+03:00'
 * // API response
 * $response['timestamp'] = ISO_Tarihi();
 * // XML attribute
 * echo "<created>" . ISO_Tarihi($created_at) . "</created>";
 *
 * @param string|null $tarih Dönüştürülecek tarih (null ise bugün)
 * @return string ISO 8601 formatında tam tarih string'i
 */
function ISO_Tarihi($tarih = null)
{
    return $tarih ? date('c', strtotime($tarih)) : date('c');
}

/**
 * Milisaniye Zaman Damgası Fonksiyonu
 *
 * Bu fonksiyon, mevcut zamanı milisaniye cinsinden
 * Unix timestamp olarak döndürür.
 *
 * Kullanım Alanı:
 * - JavaScript timestamp compatibility
 * - High-precision timing
 * - Unique identifier generation
 * - Performance measurement
 *
 * Teknik Detaylar:
 * - microtime(true) * 1000 calculation
 * - Round() ile integer conversion
 * - Millisecond precision (0.001 second)
 * - JavaScript Date.now() equivalent
 *
 * Precision & Range:
 * - Unit: Milliseconds since Unix epoch
 * - Precision: 1 millisecond
 * - Örnek: 1696176025123
 * - JavaScript compatible format
 *
 * Kullanım Örneği:
 * $ms_timestamp = Milisaniye_Zaman_Damgasi();
 * echo $ms_timestamp; // 1696176025123
 * // JavaScript ile karşılaştırma
 * $js_time = "<script>console.log({$ms_timestamp});</script>";
 * // Unique ID generation
 * $unique_id = Milisaniye_Zaman_Damgasi() . rand(100, 999);
 *
 * @return int Milisaniye cinsinden Unix timestamp
 */
function Milisaniye_Zaman_Damgasi()
{
    return round(microtime(true) * 1000);
}

/**
 * Kronometre Bitirme Fonksiyonu
 *
 * Bu fonksiyon, başlangıç zamanından itibaren geçen süreyi
 * milisaniye cinsinden hesaplar ve formatlar.
 *
 * Kullanım Alanı:
 * - Performance benchmarking
 * - Execution time measurement
 * - Code optimization analysis
 * - Response time tracking
 *
 * Teknik Detaylar:
 * - microtime(true) precision timing
 * - Millisecond conversion (* 1000)
 * - 2 decimal places formatting
 * - Human-readable output with unit
 *
 * Output Format:
 * - 'XX.XX ms' string format
 * - 2 decimal precision
 * - Includes unit label
 * - Ready for display/logging
 *
 * Kullanım Örneği:
 * $start = microtime(true);
 * // ... işlemler ...
 * $duration = Cronometer_Bitir($start);
 * echo $duration; // '145.67 ms'
 * // Benchmark logging
 * Log_Yaz("Query execution: " . Cronometer_Bitir($query_start));
 *
 * @param float $baslangic_zamani Başlangıç zamanı (microtime(true) formatında)
 * @return string Geçen süre 'XX.XX ms' formatında
 */
function Cronometer_Bitir($baslangic_zamani)
{
    $bitis = microtime(true);
    $gecen_sure = ($bitis - $baslangic_zamani) * 1000;
    return round($gecen_sure, 2) . ' ms';
}

/**
 * Tarih Karşılaştırma Fonksiyonu
 *
 * Bu fonksiyon, iki tarihi karşılaştırarak aralarındaki
 * sıralama ilişkisini integer değer olarak döndürür.
 *
 * Kullanım Alanı:
 * - Array sorting operations
 * - Date range ordering
 * - Chronological sorting
 * - Comparison algorithms
 *
 * Teknik Detaylar:
 * - strtotime() timestamp conversion
 * - Three-way comparison logic
 * - Standard comparison return values
 * - Sorting algorithm compatible
 *
 * Return Values:
 * - -1: tarih1 < tarih2 (tarih1 daha önce)
 * - 0: tarih1 = tarih2 (eşit)
 * - 1: tarih1 > tarih2 (tarih1 daha sonra)
 * - Compatible with usort() callback
 *
 * Kullanım Örneği:
 * $result = Tarih_Karsilastir('2025-10-01', '2025-10-05');
 * echo $result; // -1
 * // Array sorting
 * usort($dates, function($a, $b) {
 *     return Tarih_Karsilastir($a['date'], $b['date']);
 * });
 *
 * @param string $tarih1 İlk tarih (PHP strtotime() uyumlu)
 * @param string $tarih2 İkinci tarih (PHP strtotime() uyumlu)
 * @return int Karşılaştırma sonucu (-1, 0, 1)
 */
function Tarih_Karsilastir($tarih1, $tarih2)
{
    $zaman1 = strtotime($tarih1);
    $zaman2 = strtotime($tarih2);

    if ($zaman1 < $zaman2) {
        return -1;
    } elseif ($zaman1 > $zaman2) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * Dakika:Saniye Formatı Dönüştürücü Fonksiyonu
 *
 * Bu fonksiyon, saniye cinsinden verilen süreyi
 * MM:SS formatında (dakika:saniye) döndürür.
 *
 * Kullanım Alanı:
 * - Video/audio player duration display
 * - Timer applications
 * - Exercise/workout tracking
 * - Short duration formatting
 *
 * Teknik Detaylar:
 * - Mathematical division and modulo
 * - sprintf() ile zero-padding
 * - 2-digit format guarantee
 * - No hours component
 *
 * Format Özellikleri:
 * - MM:SS format (dakika:saniye)
 * - Leading zeros (05:09)
 * - Maximum: 99:59 (practical limit)
 * - Minimum: 00:00
 *
 * Kullanım Örneği:
 * $formatted = Dakika_Saniye(125);
 * echo $formatted; // '02:05'
 * // Video player
 * echo "Duration: " . Dakika_Saniye($video_length);
 * // Timer display
 * $remaining = Dakika_Saniye($countdown_seconds);
 *
 * @param int $saniye Toplam saniye sayısı
 * @return string MM:SS formatında süre string'i
 */
function Dakika_Saniye($saniye)
{
    $dakika = floor($saniye / 60);
    $kalan_saniye = $saniye % 60;
    return sprintf('%02d:%02d', $dakika, $kalan_saniye);
}

/**
 * Saat:Dakika:Saniye Formatı Dönüştürücü Fonksiyonu
 *
 * Bu fonksiyon, saniye cinsinden verilen süreyi
 * HH:MM:SS formatında (saat:dakika:saniye) döndürür.
 *
 * Kullanım Alanı:
 * - Long duration video/audio players
 * - Stopwatch applications
 * - Work time tracking
 * - Extended timer displays
 *
 * Teknik Detaylar:
 * - Hierarchical time unit breakdown
 * - Mathematical division with remainders
 * - sprintf() ile consistent formatting
 * - 2-digit zero-padding for all units
 *
 * Format Özellikleri:
 * - HH:MM:SS format (saat:dakika:saniye)
 * - Leading zeros (01:05:09)
 * - 24+ hours support (25:30:15)
 * - Full precision time display
 *
 * Kullanım Örneği:
 * $formatted = Saat_Dakika_Saniye(3725);
 * echo $formatted; // '01:02:05'
 * // Movie duration
 * echo "Film süresi: " . Saat_Dakika_Saniye($movie_seconds);
 * // Work session tracking
 * $session_time = Saat_Dakika_Saniye($work_seconds);
 *
 * @param int $saniye Toplam saniye sayısı
 * @return string HH:MM:SS formatında süre string'i
 */
function Saat_Dakika_Saniye($saniye)
{
    $saat = floor($saniye / 3600);
    $dakika = floor(($saniye % 3600) / 60);
    $kalan_saniye = $saniye % 60;
    return sprintf('%02d:%02d:%02d', $saat, $dakika, $kalan_saniye);
}
