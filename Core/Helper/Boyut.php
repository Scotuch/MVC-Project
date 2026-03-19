<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Boyut fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Dosya Boyutu Formatı Dönüştürücü
 *
 * Bu fonksiyon, byte cinsinden dosya boyutunu insan tarafından okunabilir formata dönüştürür.
 * Otomatik olarak en uygun birimi (B, KB, MB, GB, TB, PB, EB) seçer ve formatlar.
 *
 * Kullanım Alanı:
 * - Dosya yükleme sistemlerinde boyut gösterimi
 * - Disk kullanım rapotlarında
 * - Log dosyalarında boyut bilgisi
 * - Sistem izleme araçlarında
 *
 * Teknik Detaylar:
 * - Binary (1024) tabanlı hesaplama kullanır
 * - Floating point precision destekler
 * - Uzun ve kısa format seçenekleri
 * - Negatif değerleri sıfıra çevirir
 *
 * Kullanım Örneği:
 * $boyut = Boyut(1024); // "1 KB"
 * $boyut = Boyut(1048576, 2, true); // "1.00 Megabyte"
 * $boyut = Boyut(0); // "0 B"
 *
 * @param mixed $filesize Dönüştürülecek boyut (byte cinsinden)
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @param bool $longFormat Uzun format kullanılsın mı (varsayılan: false)
 * @return string Formatlanmış boyut string'i
 */
function Boyut($filesize, $precision = 2, $longFormat = false)
{
    if (!is_numeric($filesize)) {
        return '0 B';
    }
    $filesize = floatval($filesize);
    $filesize = $filesize > 0 ? $filesize : 0;
    $units = $longFormat ?
        ['Byte', 'Kilobyte', 'Megabyte', 'Gigabyte', 'Terabyte', 'Petabyte', 'Exabyte'] :
        ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
    $base = 1024;
    $unitIndex = 0;
    while ($filesize >= $base && $unitIndex < count($units) - 1) {
        $filesize /= $base;
        $unitIndex++;
    }
    if ($unitIndex === 0) {
        $precision = 0;
    }
    $formattedSize = number_format($filesize, $precision, '.', '');
    $unit = $units[$unitIndex];
    if ($longFormat && $unitIndex > 0 && $formattedSize == 1) {
        $unit = rtrim($unit, 's');
    }
    return $formattedSize . ' ' . $unit;
}

/**
 * Birimli Boyut String'ini Byte'a Dönüştürücü
 *
 * Bu fonksiyon, "10 MB", "5.5 GB" gibi birimli boyut ifadelerini byte cinsine dönüştürür.
 * Çeşitli birim formatlarını (B, KB, MB, GB, TB, PB, EB) ve uzun adlarını destekler.
 *
 * Kullanım Alanı:
 * - Yapılandırma dosyalarından boyut okuma
 * - Kullanıcı input'larının işlenmesi
 * - API'lerde boyut parametrelerinin çevrilmesi
 * - Form validasyonlarında
 *
 * Teknik Detaylar:
 * - Regex ile boyut ve birim ayrıştırması
 * - Case-insensitive birim eşleştirme
 * - Binary (1024) tabanlı çarpım
 * - Hatalı format durumunda 0 döndürür
 *
 * Kullanım Örneği:
 * $byte = Boyut_Size("10 MB"); // 10485760
 * $byte = Boyut_Size("1.5 GB"); // 1610612736
 * $byte = Boyut_Size("invalid"); // 0
 *
 * @param string $boyut Dönüştürülecek boyut string'i (örn: "10 MB")
 * @return float|int Byte cinsinden boyut değeri
 */
function Boyut_Size($boyut)
{
    if (!is_string($boyut)) {
        return 0;
    }
    $boyut = str_replace(['(', ')', ','], '', trim($boyut));
    if (preg_match('/^([0-9]*\.?[0-9]+)\s*([A-Za-z]+)$/i', $boyut, $matches)) {
        $sayi = floatval($matches[1]);
        $birim = strtoupper($matches[2]);
        $multipliers = [
            'B' => 1,
            'BYTE' => 1,
            'BYTES' => 1,
            'KB' => 1024,
            'KILOBYTE' => 1024,
            'KILOBYTES' => 1024,
            'MB' => 1024 * 1024,
            'MEGABYTE' => 1024 * 1024,
            'MEGABYTES' => 1024 * 1024,
            'GB' => 1024 * 1024 * 1024,
            'GIGABYTE' => 1024 * 1024 * 1024,
            'GIGABYTES' => 1024 * 1024 * 1024,
            'TB' => 1024 * 1024 * 1024 * 1024,
            'TERABYTE' => 1024 * 1024 * 1024 * 1024,
            'TERABYTES' => 1024 * 1024 * 1024 * 1024,
            'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
            'PETABYTE' => 1024 * 1024 * 1024 * 1024 * 1024,
            'PETABYTES' => 1024 * 1024 * 1024 * 1024 * 1024,
            'EB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
            'EXABYTE' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
            'EXABYTES' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024
        ];
        if (isset($multipliers[$birim])) {
            return $sayi * $multipliers[$birim];
        }
    }
    if (is_numeric($boyut)) {
        return floatval($boyut);
    }
    return 0;
}

/**
 * Otomatik Boyut Analizi ve Detaylı Bilgi
 *
 * Bu fonksiyon, boyut dönüşümünü yapar ve ayrıntılı bilgi array'i döndürür.
 * Sayısal değer, birim ve formatlanmış string'i ayrı ayrı sağlar.
 *
 * Kullanım Alanı:
 * - API response'larında detaylı boyut bilgisi
 * - Dashboard widget'larında boyut gösterimi
 * - Rapor sistemlerinde veri analizi
 * - İstatistik hesaplamalarında
 *
 * Teknik Detaylar:
 * - Structured array döndürür
 * - Otomatik birim seçimi (1024 tabanlı)
 * - Precision kontrolü (byte için 0, diğerleri için ayarlanabilir)
 * - Round işlemi uygular
 *
 * Kullanım Örneği:
 * $info = Boyut_Otomatik(1048576);
 * // ['value' => 1.0, 'unit' => 'MB', 'formatted' => '1.00 MB']
 *
 * @param mixed $bytes Analiz edilecek byte değeri
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @return array Detaylı boyut bilgi array'i
 */
function Boyut_Otomatik($bytes, $precision = 2)
{
    if (!is_numeric($bytes)) {
        return ['value' => 0, 'unit' => 'B', 'formatted' => '0 B'];
    }

    $bytes = floatval($bytes);
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
    $base = 1024;
    $unitIndex = 0;

    while ($bytes >= $base && $unitIndex < count($units) - 1) {
        $bytes /= $base;
        $unitIndex++;
    }

    $precision = $unitIndex === 0 ? 0 : $precision;
    $value = round($bytes, $precision);
    $unit = $units[$unitIndex];
    $formatted = number_format($value, $precision, '.', '') . ' ' . $unit;

    return [
        'value' => $value,
        'unit' => $unit,
        'formatted' => $formatted
    ];
}

/**
 * Bandwidth Hesaplama ve Formatı
 *
 * Bu fonksiyon, transfer edilen veri miktarını ve süreyi kullanarak bandwidth hesaplar.
 * Sonucu "/s" (per second) formatında döndürür.
 *
 * Kullanım Alanı:
 * - Network performans ölçümlerinde
 * - Dosya transfer hız gösteriminde
 * - API rate limiting hesaplamalarında
 * - İnternet hız testlerinde
 *
 * Teknik Detaylar:
 * - Saniye başına byte hesabı yapar
 * - Sıfır veya negatif süre kontrolü
 * - Otomatik birim seçimi (B/s, KB/s, MB/s, vb.)
 * - Floating point division kullanır
 *
 * Kullanım Örneği:
 * $hiz = Boyut_Bandwidth(1048576, 10); // "104.86 KB/s"
 * $hiz = Boyut_Bandwidth(0, 5); // "0 B/s"
 *
 * @param mixed $bytes Transfer edilen byte miktarı
 * @param mixed $seconds Transfer süresi (saniye)
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @return string Formatlanmış bandwidth string'i
 */
function Boyut_Bandwidth($bytes, $seconds, $precision = 2)
{
    if (!is_numeric($bytes) || !is_numeric($seconds) || $seconds <= 0) {
        return '0 B/s';
    }

    $bytesPerSecond = $bytes / $seconds;
    $result = Boyut_Otomatik($bytesPerSecond, $precision);

    return $result['formatted'] . '/s';
}

/**
 * Belirli Birime Boyut Dönüştürücü
 *
 * Bu fonksiyon, byte cinsinden boyutu belirtilen hedef birime dönüştürür.
 * Kullanıcı hangi birimi istediğini açıkça belirtebilir.
 *
 * Kullanım Alanı:
 * - Birim standardizasyonu gereken durumlarda
 * - Karşılaştırma işlemlerinde
 * - Hesaplamalar için sayısal değer gerektiğinde
 * - API'lerde belirli birim gereksinimleri
 *
 * Teknik Detaylar:
 * - Desteklenen birimler: B, KB, MB, GB, TB, PB, EB
 * - Case-insensitive birim kontrolü
 * - Division işlemi ile dönüşüm
 * - Round işlemi uygular
 *
 * Kullanım Örneği:
 * $mb = Boyut_Donustur(1048576, 'MB'); // 1.0
 * $gb = Boyut_Donustur(2147483648, 'GB'); // 2.0
 * $invalid = Boyut_Donustur(1024, 'XX'); // 0
 *
 * @param mixed $bytes Dönüştürülecek byte değeri
 * @param string $targetUnit Hedef birim (B, KB, MB, GB, TB, PB, EB)
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @return float Hedef birimdeki sayısal değer
 */
function Boyut_Donustur($bytes, $targetUnit, $precision = 2)
{
    if (!is_numeric($bytes)) {
        return 0;
    }

    $multipliers = [
        'B' => 1,
        'KB' => 1024,
        'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024,
        'TB' => 1024 * 1024 * 1024 * 1024,
        'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
        'EB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024
    ];

    $targetUnit = strtoupper($targetUnit);

    if (!isset($multipliers[$targetUnit])) {
        return 0;
    }

    $result = $bytes / $multipliers[$targetUnit];
    return round($result, $precision);
}

/**
 * Boyut Yüzde Hesaplayıcısı
 *
 * Bu fonksiyon, mevcut boyutun toplam boyuta olan yüzdelik oranını hesaplar.
 * Progress bar'lar, disk kullanım göstergesi gibi durumlarda kullanılır.
 *
 * Kullanım Alanı:
 * - Dosya yükleme progress bar'larında
 * - Disk kullanım göstergelerinde
 * - Depolama analizlerinde
 * - İstatistik hesaplamalarında
 *
 * Teknik Detaylar:
 * - Division by zero kontrolü
 * - Percentage calculation (currentSize / totalSize * 100)
 * - Round işlemi uygular
 * - Negatif değer kontrolü
 *
 * Kullanım Örneği:
 * $oran = Boyut_Yuzde(512, 1024); // 50.0
 * $oran = Boyut_Yuzde(0, 100); // 0.0
 * $oran = Boyut_Yuzde(100, 0); // 0 (hata durumu)
 *
 * @param mixed $currentSize Mevcut boyut değeri
 * @param mixed $totalSize Toplam boyut değeri
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @return float Yüzdelik oran değeri
 */
function Boyut_Yuzde($currentSize, $totalSize, $precision = 2)
{
    if (!is_numeric($currentSize) || !is_numeric($totalSize) || $totalSize <= 0) {
        return 0;
    }

    $percentage = ($currentSize / $totalSize) * 100;
    return round($percentage, $precision);
}

/**
 * Boyut Karşılaştırma Fonksiyonu
 *
 * Bu fonksiyon, iki boyut değerini karşılaştırır ve comparison result döndürür.
 * String formatlı boyutları otomatik olarak byte'a dönüştürür.
 *
 * Kullanım Alanı:
 * - Dosya boyutlarının sıralanmasında
 * - En büyük/küçük dosya bulma işlemlerinde
 * - Boyut-tabanlı filtreleme işlemlerinde
 * - Array sorting işlemlerinde
 *
 * Teknik Detaylar:
 * - String boyutları otomatik dönüşüm
 * - Three-way comparison (-1, 0, 1)
 * - Type validation kontrolü
 * - Floating point karşılaştırma
 *
 * Kullanım Örneği:
 * $sonuc = Boyut_Karsilastir("1 MB", "2 MB"); // -1
 * $sonuc = Boyut_Karsilastir(1024, 1024); // 0
 * $sonuc = Boyut_Karsilastir(2048, 1024); // 1
 *
 * @param mixed $size1 İlk boyut değeri (byte veya string)
 * @param mixed $size2 İkinci boyut değeri (byte veya string)
 * @return int Karşılaştırma sonucu (-1: küçük, 0: eşit, 1: büyük)
 */
function Boyut_Karsilastir($size1, $size2)
{
    if (is_string($size1)) {
        $size1 = Boyut_Size($size1);
    }
    if (is_string($size2)) {
        $size2 = Boyut_Size($size2);
    }
    if (!is_numeric($size1) || !is_numeric($size2)) {
        return 0;
    }
    $size1 = floatval($size1);
    $size2 = floatval($size2);
    if ($size1 < $size2) {
        return -1;
    }
    if ($size1 > $size2) {
        return 1;
    }
    return 0;
}

/**
 * Boyut Aralık Kontrol Fonksiyonu
 *
 * Bu fonksiyon, bir boyut değerinin belirtilen minimum ve maksimum aralıkta
 * olup olmadığını kontrol eder. Validasyon işlemlerinde kullanılır.
 *
 * Kullanım Alanı:
 * - Dosya yükleme validasyonlarında
 * - Form input kontrolünde
 * - API parameter validasyonunda
 * - Sistem kaynak kontrolünde
 *
 * Teknik Detaylar:
 * - String boyutları otomatik dönüşüm
 * - Inclusive range check (>= min && <= max)
 * - Type validation kontrolü
 * - Boolean return type
 *
 * Kullanım Örneği:
 * $gecerli = Boyut_Aralik_Kontrol("5 MB", "1 MB", "10 MB"); // true
 * $gecerli = Boyut_Aralik_Kontrol(2048, 1024, 4096); // true
 * $gecerli = Boyut_Aralik_Kontrol("15 MB", "1 MB", "10 MB"); // false
 *
 * @param mixed $size Kontrol edilecek boyut değeri
 * @param mixed $minSize Minimum boyut sınırı
 * @param mixed $maxSize Maksimum boyut sınırı
 * @return bool Aralıkta ise true, değilse false
 */
function Boyut_Aralik_Kontrol($size, $minSize, $maxSize)
{
    if (is_string($size)) {
        $size = Boyut_Size($size);
    }
    if (is_string($minSize)) {
        $minSize = Boyut_Size($minSize);
    }
    if (is_string($maxSize)) {
        $maxSize = Boyut_Size($maxSize);
    }
    if (!is_numeric($size) || !is_numeric($minSize) || !is_numeric($maxSize)) {
        return false;
    }
    return ($size >= $minSize && $size <= $maxSize);
}
/**
 * Maksimum Boyut Kontrol Fonksiyonu
 *
 * Bu fonksiyon, bir boyut değerinin belirtilen maksimum değeri aşıp aşmadığını
 * kontrol eder. Dosya boyutu sınırlamalarında sıkça kullanılır.
 *
 * Kullanım Alanı:
 * - Dosya yükleme boyut sınırlamalarında
 * - Disk kota kontrolünde
 * - Memory usage validasyonunda
 * - Upload limit kontrolünde
 *
 * Teknik Detaylar:
 * - String boyutları otomatik dönüşüm
 * - Less than or equal check (<=)
 * - Type validation kontrolü
 * - Boolean return type
 *
 * Kullanım Örneği:
 * $gecerli = Boyut_Max_Kontrol("5 MB", "10 MB"); // true
 * $gecerli = Boyut_Max_Kontrol(1024, 2048); // true
 * $gecerli = Boyut_Max_Kontrol("15 MB", "10 MB"); // false
 *
 * @param mixed $size Kontrol edilecek boyut değeri
 * @param mixed $maxSize Maksimum boyut sınırı
 * @return bool Sınır içinde ise true, aşıyorsa false
 */
function Boyut_Max_Kontrol($size, $maxSize)
{
    if (is_string($size)) {
        $size = Boyut_Size($size);
    }
    if (is_string($maxSize)) {
        $maxSize = Boyut_Size($maxSize);
    }
    if (!is_numeric($size) || !is_numeric($maxSize)) {
        return false;
    }
    return $size <= $maxSize;
}

/**
 * Minimum Boyut Kontrol Fonksiyonu
 *
 * Bu fonksiyon, bir boyut değerinin belirtilen minimum değeri sağlayıp sağlamadığını
 * kontrol eder. Dosya boyutu alt sınır kontrolünde kullanılır.
 *
 * Kullanım Alanı:
 * - Dosya yükleme minimum boyut kontroldünde
 * - İçerik boyutu validasyonunda
 * - Resim dosyaları boyut kontroldünde
 * - Veri dosyaları içerik kontroldünde
 *
 * Teknik Detaylar:
 * - String boyutları otomatik dönüşüm
 * - Greater than or equal check (>=)
 * - Type validation kontrolü
 * - Boolean return type
 *
 * Kullanım Örneği:
 * $gecerli = Boyut_Min_Kontrol("5 MB", "1 MB"); // true
 * $gecerli = Boyut_Min_Kontrol(2048, 1024); // true
 * $gecerli = Boyut_Min_Kontrol("500 KB", "1 MB"); // false
 *
 * @param mixed $size Kontrol edilecek boyut değeri
 * @param mixed $minSize Minimum boyut sınırı
 * @return bool Sınırı sağlıyorsa true, altında ise false
 */
function Boyut_Min_Kontrol($size, $minSize)
{
    if (is_string($size)) {
        $size = Boyut_Size($size);
    }
    if (is_string($minSize)) {
        $minSize = Boyut_Size($minSize);
    }
    if (!is_numeric($size) || !is_numeric($minSize)) {
        return false;
    }
    return $size >= $minSize;
}

/**
 * Array'deki En Büyük Boyutu Bulma Fonksiyonu
 *
 * Bu fonksiyon, boyut değerleri array'inden en büyük değeri bulur ve döndürür.
 * String formatlı boyutları da destekler ve otomatik dönüşüm yapar.
 *
 * Kullanım Alanı:
 * - Dosya listelerinde en büyük dosyayı bulma
 * - Depolama analizlerinde maksimum değer tespiti
 * - İstatistik hesaplamalarında
 * - Performance monitoring'de peak değer bulma
 *
 * Teknik Detaylar:
 * - Array iteration ile maksimum bulma
 * - String boyutları otomatik dönüşüm
 * - Empty array kontrolü
 * - Type validation kontrolü
 *
 * Kullanım Örneği:
 * $max = Boyut_Max_Bul(["1 MB", "5 MB", "2 MB"]); // 5242880 (5 MB in bytes)
 * $max = Boyut_Max_Bul([1024, 2048, 512]); // 2048
 * $max = Boyut_Max_Bul([]); // 0
 *
 * @param array $sizes Boyut değerleri array'i
 * @return float|int En büyük boyut değeri (byte cinsinden)
 */
function Boyut_Max_Bul($sizes)
{
    if (!is_array($sizes) || empty($sizes)) {
        return 0;
    }

    $maxSize = 0;

    foreach ($sizes as $size) {
        if (is_string($size)) {
            $size = Boyut_Size($size);
        }

        if (is_numeric($size) && $size > $maxSize) {
            $maxSize = $size;
        }
    }

    return $maxSize;
}

/**
 * Array'deki En Küçük Boyutu Bulma Fonksiyonu
 *
 * Bu fonksiyon, boyut değerleri array'inden en küçük değeri bulur ve döndürür.
 * String formatlı boyutları da destekler ve otomatik dönüşüm yapar.
 *
 * Kullanım Alanı:
 * - Dosya listelerinde en küçük dosyayı bulma
 * - Depolama analizlerinde minimum değer tespiti
 * - İstatistik hesaplamalarında
 * - Performance monitoring'de minimum threshold tespiti
 *
 * Teknik Detaylar:
 * - Array iteration ile minimum bulma
 * - String boyutları otomatik dönüşüm
 * - Empty array kontrolü
 * - PHP_INT_MAX ile initialization
 *
 * Kullanım Örneği:
 * $min = Boyut_Min_Bul(["1 MB", "5 MB", "2 MB"]); // 1048576 (1 MB in bytes)
 * $min = Boyut_Min_Bul([1024, 2048, 512]); // 512
 * $min = Boyut_Min_Bul([]); // 0
 *
 * @param array $sizes Boyut değerleri array'i
 * @return float|int En küçük boyut değeri (byte cinsinden)
 */
function Boyut_Min_Bul($sizes)
{
    if (!is_array($sizes) || empty($sizes)) {
        return 0;
    }

    $minSize = PHP_INT_MAX;

    foreach ($sizes as $size) {
        if (is_string($size)) {
            $size = Boyut_Size($size);
        }

        if (is_numeric($size) && $size < $minSize) {
            $minSize = $size;
        }
    }

    return $minSize === PHP_INT_MAX ? 0 : $minSize;
}

/**
 * Boyut Format Validasyon Fonksiyonu
 *
 * Bu fonksiyon, string olarak verilen boyut ifadesinin geçerli bir formatta
 * olup olmadığını kontrol eder. Regex ile format kontrolü yapar.
 *
 * Kullanım Alanı:
 * - Form input validasyonunda
 * - API parameter kontrolünde
 * - Yapılandırma dosyası validasyonunda
 * - User input sanitization'da
 *
 * Teknik Detaylar:
 * - Regex pattern matching kullanır
 * - Case-insensitive birim kontrolü
 * - Decimal ve integer number destekler
 * - Çeşitli birim formatlarını destekler
 *
 * Kullanım Örneği:
 * $gecerli = Boyut_Format_Kontrol("10 MB"); // true
 * $gecerli = Boyut_Format_Kontrol("5.5 GB"); // true
 * $gecerli = Boyut_Format_Kontrol("invalid format"); // false
 *
 * @param string $boyut Kontrol edilecek boyut string'i
 * @return bool Geçerli format ise true, değilse false
 */
function Boyut_Format_Kontrol($boyut)
{
    if (!is_string($boyut)) {
        return false;
    }

    $pattern = '/^[0-9]*\.?[0-9]+\s*(B|KB|MB|GB|TB|PB|EB|BYTE|BYTES|KILOBYTE|KILOBYTES|MEGABYTE|MEGABYTES|GIGABYTE|GIGABYTES|TERABYTE|TERABYTES|PETABYTE|PETABYTES|EXABYTE|EXABYTES)$/i';

    return preg_match($pattern, trim($boyut)) === 1;
}

/**
 * Türkçe Boyut Formatı Dönüştürücü
 *
 * Bu fonksiyon, boyut değerlerini Türkçe birimler ve sayı formatı ile döndürür.
 * Ondalik ayraç olarak virgül, binlik ayraç olarak nokta kullanır.
 *
 * Kullanım Alanı:
 * - Türkçe arayüzlerde boyut gösterimi
 * - Yerel kullanıcı deneyiminde
 * - Türkçe raporlarda
 * - Lokalizasyon gereksinimlerinde
 *
 * Teknik Detaylar:
 * - Türkçe birim isimleri (Bayt, Kilobayt, vb.)
 * - Türkçe sayı formatı (virgül ve nokta)
 * - Uzun ve kısa format seçenekleri
 * - UTF-8 uyumlu çıktı
 *
 * Kullanım Örneği:
 * $boyut = Boyut_Turkce(1024); // "1 KB"
 * $boyut = Boyut_Turkce(1048576, 2, true); // "1,00 Megabayt"
 * $boyut = Boyut_Turkce(1536, 1); // "1,5 KB"
 *
 * @param mixed $filesize Dönüştürülecek boyut (byte cinsinden)
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @param bool $longFormat Uzun format kullanılsın mı (varsayılan: false)
 * @return string Türkçe formatlanmış boyut string'i
 */
function Boyut_Turkce($filesize, $precision = 2, $longFormat = false)
{
    if (!is_numeric($filesize)) {
        return '0 B';
    }

    $filesize = floatval($filesize);
    $filesize = $filesize > 0 ? $filesize : 0;

    $units = $longFormat ?
        ['Bayt', 'Kilobayt', 'Megabayt', 'Gigabayt', 'Terabayt', 'Petabayt', 'Eksabayt'] :
        ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];

    $base = 1024;
    $unitIndex = 0;

    while ($filesize >= $base && $unitIndex < count($units) - 1) {
        $filesize /= $base;
        $unitIndex++;
    }

    if ($unitIndex === 0) {
        $precision = 0;
    }

    $formattedSize = number_format($filesize, $precision, ',', '.');
    return $formattedSize . ' ' . $units[$unitIndex];
}

/**
 * Özel Boyut Formatı Dönüştürücü
 *
 * Bu fonksiyon, boyut dönüşümünü kullanıcı tarafından tanımlanabilen
 * format seçenekleri ile yapar. Tam kontrol sağlar.
 *
 * Kullanım Alanı:
 * - Özel format gereksinimlerinde
 * - Marka-spesifik gösterim standartlarında
 * - API'lerde esnek format seçeneklerinde
 * - Multi-language desteknde
 *
 * Teknik Detaylar:
 * - Options array ile yapılandırma
 * - Varsayılan değerler ile merge
 * - Custom birim listesi destekler
 * - Custom sayı formatı seçenekleri
 *
 * Kullanım Örneği:
 * $opts = ['precision' => 3, 'decimal_separator' => ','];
 * $boyut = Boyut_Ozel_Format(1048576, $opts); // "1,000 MB"
 *
 * @param mixed $filesize Dönüştürülecek boyut (byte cinsinden)
 * @param array $options Format seçenekleri array'i
 * @return string Özel formatlanmış boyut string'i
 */
function Boyut_Ozel_Format($filesize, $options = [])
{
    if (!is_numeric($filesize)) {
        return '0 B';
    }

    // Varsayılan seçenekler
    $defaults = [
        'precision' => 2,
        'decimal_separator' => '.',
        'thousands_separator' => '',
        'space_between' => true,
        'units' => ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'],
        'base' => 1024
    ];

    $options = array_merge($defaults, $options);

    $filesize = floatval($filesize);
    $filesize = $filesize > 0 ? $filesize : 0;

    $base = $options['base'];
    $unitIndex = 0;

    while ($filesize >= $base && $unitIndex < count($options['units']) - 1) {
        $filesize /= $base;
        $unitIndex++;
    }

    $precision = $unitIndex === 0 ? 0 : $options['precision'];

    $formattedSize = number_format(
        $filesize,
        $precision,
        $options['decimal_separator'],
        $options['thousands_separator']
    );

    $separator = $options['space_between'] ? ' ' : '';

    return $formattedSize . $separator . $options['units'][$unitIndex];
}

/**
 * Boyut Progress Bar Üretici Fonksiyonu
 *
 * Bu fonksiyon, mevcut ve toplam boyut değerlerini kullanarak progress bar
 * görselizasyonu ve ilgili bilgileri üretir.
 *
 * Kullanım Alanı:
 * - Dosya yükleme progress gösteriminde
 * - Disk kullanım göstergelerinde
 * - Transfer durumu gösteriminde
 * - Terminal-based progress bar'larda
 *
 * Teknik Detaylar:
 * - ASCII progress bar üretir
 * - Formatlanmış boyut bilgileri sağlar
 * - Yüzdelik hesaplama yapar
 * - Completion status kontrolü
 *
 * Kullanım Örneği:
 * $progress = Boyut_Progress(512000, 1024000);
 * // ['current_formatted' => '500 KB', 'percentage' => 50.0, ...]
 *
 * @param mixed $current Mevcut boyut değeri
 * @param mixed $total Toplam boyut değeri
 * @param int $barLength Progress bar uzunluğu (varsayılan: 30)
 * @return array Progress bilgileri array'i
 */
function Boyut_Progress($current, $total, $barLength = 30)
{
    if (!is_numeric($current) || !is_numeric($total) || $total <= 0) {
        return [
            'current_formatted' => '0 B',
            'total_formatted' => '0 B',
            'percentage' => 0,
            'bar' => str_repeat('-', $barLength),
            'completed' => false
        ];
    }

    $current = floatval($current);
    $total = floatval($total);

    // Mevcut boyut toplamdan büyükse sınırla
    $current = min($current, $total);

    $percentage = Boyut_Yuzde($current, $total, 1);
    $filledLength = round(($current / $total) * $barLength);

    $bar = str_repeat('█', $filledLength) . str_repeat('-', $barLength - $filledLength);

    return [
        'current_formatted' => Boyut($current),
        'total_formatted' => Boyut($total),
        'percentage' => $percentage,
        'bar' => $bar,
        'completed' => $current >= $total
    ];
}

/**
 * Boyut İstatistik Analiz Fonksiyonu
 *
 * Bu fonksiyon, boyut değerleri array'ini analiz eder ve detaylı istatistik
 * bilgileri döndürür. Hem raw hem de formatlanmış değerler sağlar.
 *
 * Kullanım Alanı:
 * - Dosya sistemi analizlerinde
 * - Depolama raporlamalarında
 * - Performance monitoring'de
 * - Kapasitie planlama işlemlerinde
 *
 * Teknik Detaylar:
 * - Count, total, average, min, max hesaplar
 * - String boyutları otomatik dönüşüm
 * - Hem byte hem formatlı değerler
 * - Empty array kontrolü
 *
 * Kullanım Örneği:
 * $stats = Boyut_Istatistik(["1 MB", "2 MB", "3 MB"]);
 * // ['count' => 3, 'total' => 6291456, 'average' => 2097152, ...]
 *
 * @param array $sizes Analiz edilecek boyut değerleri array'i
 * @return array Detaylı istatistik bilgileri array'i
 */
function Boyut_Istatistik($sizes)
{
    if (!is_array($sizes) || empty($sizes)) {
        return [
            'count' => 0,
            'total' => 0,
            'average' => 0,
            'min' => 0,
            'max' => 0,
            'total_formatted' => '0 B',
            'average_formatted' => '0 B',
            'min_formatted' => '0 B',
            'max_formatted' => '0 B'
        ];
    }

    $byteSizes = [];

    foreach ($sizes as $size) {
        if (is_string($size)) {
            $size = Boyut_Size($size);
        }

        if (is_numeric($size)) {
            $byteSizes[] = floatval($size);
        }
    }

    if (empty($byteSizes)) {
        return [
            'count' => 0,
            'total' => 0,
            'average' => 0,
            'min' => 0,
            'max' => 0,
            'total_formatted' => '0 B',
            'average_formatted' => '0 B',
            'min_formatted' => '0 B',
            'max_formatted' => '0 B'
        ];
    }

    $count = count($byteSizes);
    $total = array_sum($byteSizes);
    $average = $total / $count;
    $min = min($byteSizes);
    $max = max($byteSizes);

    return [
        'count' => $count,
        'total' => $total,
        'average' => $average,
        'min' => $min,
        'max' => $max,
        'total_formatted' => Boyut($total),
        'average_formatted' => Boyut($average),
        'min_formatted' => Boyut($min),
        'max_formatted' => Boyut($max)
    ];
}
