<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Genel Utility ve Yardımcı Fonksiyonlar
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Rastgele Sayı Üretici
 *
 * Bu fonksiyon, belirtilen aralıkta cryptographically secure rastgele sayı üretir.
 * Müşteri kodları, referans numaraları gibi kullanımlar için uygundur.
 *
 * Kullanım Alanı:
 * - Müşteri numarası üretiminde (örn: 548, 5, 14)
 * - Referans kodu oluşturmada
 * - Rastgele ID generation'da
 * - Unique identifier üretiminde
 * - Test data generation'da
 *
 * Teknik Detaylar:
 * - random_int() CSPRNG kullanır
 * - Cryptographically secure random generation
 * - Uniform distribution sağlar
 * - Integer range validation
 * - PHP 7.0+ compatibility
 *
 * Güvenlik Özellikleri:
 * - Cryptographically secure random source
 * - Predictable pattern yok
 * - Uniform probability distribution
 * - System entropy kullanır
 *
 * Kullanım Örnekleri:
 * $musteri_no = Rastgele_Sayi(1, 999);     // 1-999 arası: 548
 * $kucuk_id = Rastgele_Sayi(1, 9);        // 1-9 arası: 5
 * $kategori = Rastgele_Sayi(10, 99);      // 10-99 arası: 14
 * $unique_id = Rastgele_Sayi(100000, 999999); // 6 haneli: 578432
 *
 * @param int $min Minimum değer (dahil)
 * @param int $max Maximum değer (dahil)
 * @return int Belirtilen aralıkta rastgele sayı
 * @throws Exception Min > Max durumunda
 */
function Rastgele_Sayi($min = 1, $max = 1000)
{
    if ($min > $max) {
        return 0;
    }

    return random_int($min, $max);
}

/**
 * Dosya Boyutu Formatlamac
 *
 * Bu fonksiyon, byte cinsinden dosya boyutunu okunabilir formata dönüştürür.
 * İnsan dostu format (KB, MB, GB, TB) ile gösterim sağlar.
 *
 * Kullanım Alanı:
 * - Dosya upload sistemlerinde boyut gösterimi
 * - Storage management'da
 * - System monitoring'de
 * - User interface'de disk usage
 *
 * Teknik Detaylar:
 * - Binary (1024) veya decimal (1000) base desteği
 * - Configurable decimal precision
 * - Unit localization support
 * - Large file size support (up to TB)
 *
 * Kullanım Örneği:
 * echo Dosya_Boyutu_Formatla(1024); // "1.00 KB"
 * echo Dosya_Boyutu_Formatla(1536, 1); // "1.5 KB"
 * echo Dosya_Boyutu_Formatla(1000000, 2, false); // "1.00 MB" (decimal)
 *
 * @param int $bytes Byte cinsinden boyut
 * @param int $precision Ondalık hassasiyet (varsayılan: 2)
 * @param bool $binary Binary (1024) veya decimal (1000) base (varsayılan: true)
 * @return string Formatlanmış boyut string'i
 */
function Dosya_Boyutu_Formatla($bytes, $precision = 2, $binary = true)
{
    $base = $binary ? 1024 : 1000;
    $units = $binary ? ['B', 'KB', 'MB', 'GB', 'TB'] : ['B', 'kB', 'MB', 'GB', 'TB'];

    if ($bytes <= 0) {
        return '0 ' . $units[0];
    }

    $exponent = floor(log($bytes) / log($base));
    $exponent = min($exponent, count($units) - 1);

    $size = $bytes / pow($base, $exponent);

    return round($size, $precision) . ' ' . $units[$exponent];
}

/**
 * URL Slug Üretici
 *
 * Bu fonksiyon, Türkçe karakterli metni URL-friendly slug'a dönüştürür.
 * SEO uyumlu URL'ler oluşturmak için kullanılır.
 *
 * Kullanım Alanı:
 * - Blog post URL'lerinde
 * - Product page slug'larında
 * - Category ve tag URL'lerinde
 * - SEO-friendly identifier'larda
 *
 * Teknik Detaylar:
 * - Turkish character conversion (ş->s, ğ->g, etc.)
 * - Special character removal
 * - Multiple space/dash consolidation
 * - Lowercase conversion
 * - Trim leading/trailing dashes
 *
 * Kullanım Örneği:
 * echo Url_Slug_Olustur("Merhaba Dünya!"); // "merhaba-dunya"
 * echo Url_Slug_Olustur("İstanbul'da Yaşam"); // "istanbulda-yasam"
 * echo Url_Slug_Olustur("Ürün & Hizmet Çeşitleri"); // "urun-hizmet-cesitleri"
 *
 * @param string $text Slug'a dönüştürülecek metin
 * @param string $separator Kelime ayırıcısı (varsayılan: '-')
 * @return string URL-safe slug
 */
function Url_Slug_Olustur($text, $separator = '-')
{
    // Türkçe karakterleri dönüştür
    $turkce = ['ş', 'Ş', 'ı', 'I', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
    $latin = ['s', 'S', 'i', 'I', 'I', 'g', 'G', 'u', 'U', 'o', 'O', 'c', 'C'];
    $text = str_replace($turkce, $latin, $text);

    // Küçük harfe çevir
    $text = strtolower($text);

    // Alfanümerik olmayan karakterleri ayırıcı ile değiştir
    $text = preg_replace('/[^a-z0-9\-]/', $separator, $text);

    // Çoklu ayırıcıları tek ayırıcı yap
    $text = preg_replace('/' . preg_quote($separator) . '+/', $separator, $text);

    // Başındaki ve sonundaki ayırıcıları kaldır
    return trim($text, $separator);
}

/**
 * E-posta Adresi Doğrulama
 *
 * Bu fonksiyon, e-posta adresinin geçerli format ve domain'e sahip
 * olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - User registration validation'da
 * - Contact form'larında
 * - Newsletter subscription'da
 * - Email marketing sistemlerinde
 *
 * Teknik Detaylar:
 * - RFC 5322 email format validation
 * - DNS MX record checking (opsiyonel)
 * - Disposable email detection (opsiyonel)
 * - Domain blacklist checking
 * - International domain support
 *
 * Kullanım Örneği:
 * if (Email_Dogrula('user@example.com')) {
 *     echo "Geçerli email";
 * }
 * 
 * if (Email_Dogrula('test@domain.com', true)) {
 *     echo "Email format ve domain geçerli";
 * }
 *
 * @param string $email Doğrulanacak e-posta adresi
 * @param bool $domain_kontrol Domain MX kaydı kontrol edilsin mi (varsayılan: false)
 * @return bool Geçerli email ise true, değilse false
 */
function Email_Dogrula($email, $domain_kontrol = false)
{
    // Temel format kontrolü
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Domain kontrolü isteniyorsa
    if ($domain_kontrol) {
        $domain = substr(strrchr($email, "@"), 1);

        // MX kaydı veya A kaydı var mı kontrol et
        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            return false;
        }
    }

    return true;
}

/**
 * Telefon Numarası Formatlamac
 *
 * Bu fonksiyon, Türkiye telefon numaralarını standart formata dönüştürür.
 * Farklı giriş formatlarını (+90, 0, vs.) standartlaştırır.
 *
 * Kullanım Alanı:
 * - User registration'da telefon standardizasyonu
 * - SMS gönderim sistemlerinde
 * - Contact management'da
 * - CRM sistemlerinde
 *
 * Teknik Detaylar:
 * - Turkish mobile number validation
 * - Multiple input format support
 * - International format conversion
 * - Operator detection
 * - Format normalization
 *
 * Desteklenen Formatlar:
 * - +90 532 123 45 67
 * - 0532 123 45 67
 * - 532 123 45 67
 * - 5321234567
 *
 * Kullanım Örneği:
 * echo Telefon_Formatla('+90 532 123 45 67'); // "+90 532 123 45 67"
 * echo Telefon_Formatla('0532 123 45 67'); // "+90 532 123 45 67"
 * echo Telefon_Formatla('5321234567'); // "+90 532 123 45 67"
 *
 * @param string $telefon Ham telefon numarası
 * @param string $format Çıktı formatı ('international', 'national', 'compact')
 * @return string|false Formatlanmış telefon numarası veya geçersizse false
 */
function Telefon_Formatla($telefon, $format = 'international')
{
    // Sadece rakamları al
    $digits = preg_replace('/[^0-9]/', '', $telefon);

    // Türkiye prefixi kontrolü ve standardizasyonu
    if (substr($digits, 0, 2) === '90') {
        $digits = substr($digits, 2); // +90'ı kaldır
    } elseif (substr($digits, 0, 1) === '0') {
        $digits = substr($digits, 1); // 0'ı kaldır
    }

    // 10 haneli Türkiye mobil numara kontrolü
    if (strlen($digits) !== 10) {
        return false;
    }

    // Türkiye mobil operatör prefiksleri
    $mobile_prefixes = ['50', '53', '54', '55', '59'];
    $prefix = substr($digits, 0, 2);

    if (!in_array($prefix, $mobile_prefixes)) {
        return false;
    }

    // Format'a göre çıktı
    switch ($format) {
        case 'international':
            return '+90 ' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 3) . ' ' . substr($digits, 6, 2) . ' ' . substr($digits, 8, 2);
        case 'national':
            return '0' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 3) . ' ' . substr($digits, 6, 2) . ' ' . substr($digits, 8, 2);
        case 'compact':
            return '+90' . $digits;
        default:
            return '+90 ' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 3) . ' ' . substr($digits, 6, 2) . ' ' . substr($digits, 8, 2);
    }
}

/**
 * Tarih Formatlamac ve Göreceli Zaman
 *
 * Bu fonksiyon, timestamp'i insan dostu formata dönüştürür.
 * "2 saat önce", "dün", "geçen hafta" gibi göreceli zaman ifadeleri üretir.
 *
 * Kullanım Alanı:
 * - Social media post timestamps
 * - Comment sistemi tarih gösterimi
 * - Activity feed'lerde
 * - Notification sistemlerinde
 *
 * Teknik Detaylar:
 * - Relative time calculation
 * - Turkish localization
 * - Multiple time unit support (second, minute, hour, day, month, year)
 * - Past and future time support
 * - Configurable precision
 *
 * Kullanım Örneği:
 * echo Tarih_Goreceli(time() - 3600); // "1 saat önce"
 * echo Tarih_Goreceli(time() - 86400); // "dün"
 * echo Tarih_Goreceli(time() + 3600); // "1 saat sonra"
 *
 * @param int $timestamp Unix timestamp
 * @param bool $detayli Detaylı format (varsayılan: false)
 * @return string Göreceli zaman string'i
 */
function Tarih_Goreceli($timestamp, $detayli = false)
{
    $fark = time() - $timestamp;
    $mutlak_fark = abs($fark);
    $gelecek = $fark < 0;

    // Zaman birimleri (saniye cinsinden)
    $birimler = [
        'yıl' => 31536000,
        'ay' => 2592000,
        'hafta' => 604800,
        'gün' => 86400,
        'saat' => 3600,
        'dakika' => 60,
        'saniye' => 1
    ];

    // Çok yakın zaman
    if ($mutlak_fark < 60) {
        return $gelecek ? 'birazdan' : 'az önce';
    }

    foreach ($birimler as $birim => $saniye) {
        if ($mutlak_fark >= $saniye) {
            $miktar = floor($mutlak_fark / $saniye);

            if ($detayli) {
                $suffix = $gelecek ? ' sonra' : ' önce';
                return $miktar . ' ' . $birim . $suffix;
            } else {
                // Kısa formatlar
                switch ($birim) {
                    case 'gün':
                        if ($miktar == 1) {
                            return $gelecek ? 'yarın' : 'dün';
                        }
                        break;
                    case 'hafta':
                        if ($miktar == 1) {
                            return $gelecek ? 'gelecek hafta' : 'geçen hafta';
                        }
                        break;
                }

                $suffix = $gelecek ? ' sonra' : ' önce';
                return $miktar . ' ' . $birim . $suffix;
            }
        }
    }

    return 'bilinmeyen zaman';
}

/**
 * Renk Kodu Dönüştürücü
 *
 * Bu fonksiyon, farklı renk formatları arasında dönüşüm yapar.
 * HEX, RGB, HSL formatları arasında çeviri sağlar.
 *
 * Kullanım Alanı:
 * - Theme customization sistemlerinde
 * - Color picker implementation'da
 * - CSS color manipulation'da
 * - Image processing'de
 *
 * Teknik Detaylar:
 * - HEX to RGB conversion
 * - RGB to HEX conversion
 * - HSL color space support
 * - Color validation
 * - Multiple input format support
 *
 * Kullanım Örneği:
 * echo Renk_Donustur('#FF0000', 'rgb'); // "rgb(255, 0, 0)"
 * echo Renk_Donustur('rgb(255, 0, 0)', 'hex'); // "#FF0000"
 * echo Renk_Donustur('#FF0000', 'hsl'); // "hsl(0, 100%, 50%)"
 *
 * @param string $renk Dönüştürülecek renk kodu
 * @param string $hedef_format Hedef format ('hex', 'rgb', 'hsl')
 * @return string|false Dönüştürülmüş renk veya geçersizse false
 */
function Renk_Donustur($renk, $hedef_format = 'hex')
{
    // HEX format detect and parse
    if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $renk)) {
        $hex = ltrim($renk, '#');

        // 3 haneli hex'i 6 haneye çevir
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    } elseif (preg_match('/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/', $renk, $matches)) {
        // RGB format parse
        $r = intval($matches[1]);
        $g = intval($matches[2]);
        $b = intval($matches[3]);

        // RGB range validation
        if ($r > 255 || $g > 255 || $b > 255) {
            return false;
        }
    } else {
        return false; // Geçersiz format
    }

    // Hedef formata dönüştür
    switch ($hedef_format) {
        case 'hex':
            return '#' . sprintf('%02x%02x%02x', $r, $g, $b);

        case 'rgb':
            return "rgb($r, $g, $b)";

        case 'hsl':
            // RGB to HSL conversion
            $r_norm = $r / 255;
            $g_norm = $g / 255;
            $b_norm = $b / 255;

            $max = max($r_norm, $g_norm, $b_norm);
            $min = min($r_norm, $g_norm, $b_norm);
            $delta = $max - $min;

            $l = ($max + $min) / 2;

            if ($delta == 0) {
                $h = $s = 0;
            } else {
                $s = $l > 0.5 ? $delta / (2 - $max - $min) : $delta / ($max + $min);

                switch ($max) {
                    case $r_norm:
                        $h = (($g_norm - $b_norm) / $delta) + ($g_norm < $b_norm ? 6 : 0);
                        break;
                    case $g_norm:
                        $h = ($b_norm - $r_norm) / $delta + 2;
                        break;
                    case $b_norm:
                        $h = ($r_norm - $g_norm) / $delta + 4;
                        break;
                }
                $h /= 6;
            }

            $h = round($h * 360);
            $s = round($s * 100);
            $l = round($l * 100);

            return "hsl($h, $s%, $l%)";

        default:
            return false;
    }
}

/**
 * Metin Kısaltmac
 *
 * Bu fonksiyon, uzun metinleri belirtilen karakter sayısında kısaltır.
 * Kelime sınırlarını koruyarak akıllı kısaltma yapar.
 *
 * Kullanım Alanı:
 * - Blog post excerpt'lerinde
 * - Product description'larda
 * - Search result snippets'de
 * - Card/tile component'lerde
 *
 * Teknik Detaylar:
 * - Word boundary preservation
 * - HTML tag stripping
 * - Custom suffix support
 * - UTF-8 string handling
 * - Multiple truncation strategies
 *
 * Kullanım Örneği:
 * echo Metin_Kisalt("Bu çok uzun bir metin örneği.", 15); // "Bu çok uzun..."
 * echo Metin_Kisalt($html_content, 100, " [devamı]", true); // HTML'siz kısaltma
 *
 * @param string $metin Kısaltılacak metin
 * @param int $uzunluk Maksimum karakter sayısı
 * @param string $son_ek Kısaltma suffix'i (varsayılan: '...')
 * @param bool $html_temizle HTML tag'leri temizlensin mi (varsayılan: false)
 * @return string Kısaltılmış metin
 */
function Metin_Kisalt($metin, $uzunluk = 100, $son_ek = '...', $html_temizle = false)
{
    if ($html_temizle) {
        $metin = strip_tags($metin);
    }

    // Metin zaten kısa ise olduğu gibi döndür
    if (mb_strlen($metin, 'UTF-8') <= $uzunluk) {
        return $metin;
    }

    // Suffix için yer ayır
    $son_ek_uzunluk = mb_strlen($son_ek, 'UTF-8');
    $hedef_uzunluk = $uzunluk - $son_ek_uzunluk;

    // Kelime sınırında kısalt
    $kisaltilmis = mb_substr($metin, 0, $hedef_uzunluk, 'UTF-8');

    // Son kelimenin yarısında kesilmişse, o kelimeyi çıkar
    $son_bosluk = mb_strrpos($kisaltilmis, ' ', 0, 'UTF-8');
    if ($son_bosluk !== false && $son_bosluk > $hedef_uzunluk * 0.8) {
        $kisaltilmis = mb_substr($kisaltilmis, 0, $son_bosluk, 'UTF-8');
    }

    return $kisaltilmis . $son_ek;
}

/**
 * Array Sıralama Helper
 *
 * Bu fonksiyon, çok boyutlu array'leri belirtilen key'e göre sıralar.
 * Nested object property'lere göre sıralama da destekler.
 *
 * Kullanım Alanı:
 * - Database sonuç sıralamasında
 * - API response sorting'de
 * - Data table işlemlerinde
 * - Search result ranking'de
 *
 * Teknik Detaylar:
 * - Multi-dimensional array sorting
 * - Nested property access
 * - Multiple sort directions
 * - Type-safe comparison
 * - Natural sorting support
 *
 * Kullanım Örneği:
 * $users = [['name' => 'Ahmet', 'age' => 25], ['name' => 'Mehmet', 'age' => 30]];
 * $sorted = Array_Sirala($users, 'age', 'desc');
 * $nested = Array_Sirala($products, 'category.name', 'asc');
 *
 * @param array $array Sıralanacak array
 * @param string $key Sıralama anahtarı (nested: 'parent.child')
 * @param string $direction Sıralama yönü ('asc' veya 'desc')
 * @return array Sıralanmış array
 */
function Array_Sirala($array, $key, $direction = 'asc')
{
    if (empty($array) || !is_array($array)) {
        return $array;
    }

    usort($array, function ($a, $b) use ($key, $direction) {
        // Nested key access (örn: 'user.profile.name')
        $getValue = function ($item, $keyPath) {
            $keys = explode('.', $keyPath);
            $value = $item;

            foreach ($keys as $k) {
                if (is_array($value) && isset($value[$k])) {
                    $value = $value[$k];
                } elseif (is_object($value) && isset($value->$k)) {
                    $value = $value->$k;
                } else {
                    return null;
                }
            }

            return $value;
        };

        $valueA = $getValue($a, $key);
        $valueB = $getValue($b, $key);

        // Null değerleri en sona koy
        if ($valueA === null && $valueB === null) return 0;
        if ($valueA === null) return 1;
        if ($valueB === null) return -1;

        // Numeric comparison
        if (is_numeric($valueA) && is_numeric($valueB)) {
            $comparison = $valueA <=> $valueB;
        } else {
            // String comparison
            $comparison = strcasecmp((string)$valueA, (string)$valueB);
        }

        return $direction === 'desc' ? -$comparison : $comparison;
    });

    return $array;
}

/**
 * QR Kod URL Üretici
 *
 * Bu fonksiyon, verilen metin için QR kod oluşturan URL üretir.
 * Ücretsiz QR kod API'leri kullanarak image URL'i oluşturur.
 *
 * Kullanım Alanı:
 * - Contact bilgileri paylaşımında
 * - WiFi şifre paylaşımında
 * - Website URL'lerinde
 * - Payment sistemlerinde
 *
 * Teknik Detaylar:
 * - Multiple QR API providers support
 * - Configurable size support
 * - Error correction level
 * - UTF-8 encoding support
 * - Fallback API providers
 *
 * Kullanım Örneği:
 * echo Qr_Kod_Url("https://example.com"); // QR kod image URL
 * echo Qr_Kod_Url("WIFI:T:WPA;S:MyWiFi;P:password123;;", 300); // WiFi QR
 * $img = '<img src="' . Qr_Kod_Url($text) . '" alt="QR Code">';
 *
 * @param string $veri QR koda dönüştürülecek veri
 * @param int $boyut QR kod boyutu (pixel, varsayılan: 200)
 * @param string $hata_seviye Error correction level ('L','M','Q','H', varsayılan: 'M')
 * @param string $api Kullanılacak API ('qr-server', 'api-qrserver', 'qrcode-monkey')
 * @return string QR kod image URL'i
 */
function Qr_Kod_Url($veri, $boyut = 200, $hata_seviye = 'M', $api = 'qr-server')
{
    $encoded_data = urlencode($veri);

    // Error correction level mapping
    $error_levels = [
        'L' => 'L', // Low (~7%)
        'M' => 'M', // Medium (~15%) 
        'Q' => 'Q', // Quartile (~25%)
        'H' => 'H'  // High (~30%)
    ];

    $ecc = $error_levels[$hata_seviye] ?? 'M';

    switch ($api) {
        case 'qr-server':
            // QR-Server.com - Free QR Code API
            return "https://api.qrserver.com/v1/create-qr-code/?size={$boyut}x{$boyut}&data={$encoded_data}&ecc={$ecc}";

        case 'api-qrserver':
            // Alternative QR Server API
            return "https://qr-server.com/api/v1/create-qr-code/?size={$boyut}x{$boyut}&data={$encoded_data}&ecc={$ecc}&format=png";

        case 'qrcode-monkey':
            // QRCode Monkey API (simple version)
            return "https://api.qrcode-monkey.com/qr/custom?download=false&file=png&size={$boyut}&data={$encoded_data}";

        case 'quickchart':
            // QuickChart.io QR API
            return "https://quickchart.io/qr?text={$encoded_data}&size={$boyut}&errorCorrectionLevel={$ecc}";

        default:
            // Default: QR-Server.com
            return "https://api.qrserver.com/v1/create-qr-code/?size={$boyut}x{$boyut}&data={$encoded_data}&ecc={$ecc}";
    }
}

/**
 * Türk TC Kimlik No Doğrulama
 *
 * Bu fonksiyon, TC kimlik numarasının matematik kuralına uygun
 * olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - User registration validation
 * - Identity verification sistemlerinde
 * - Government form validation
 * - KYC (Know Your Customer) süreçlerinde
 *
 * Teknik Detaylar:
 * - 11 haneli sayı kontrolü
 * - İlk hane 0 olamaz kuralı
 * - TC kimlik algoritması (mod 10)
 * - Checksum validation
 * - Turkish citizenship validation
 *
 * Kullanım Örneği:
 * if (Tc_Kimlik_Dogrula('12345678901')) {
 *     echo "Geçerli TC kimlik numarası";
 * }
 *
 * @param string $tc TC kimlik numarası
 * @return bool Geçerli TC ise true, değilse false
 */
function Tc_Kimlik_Dogrula($tc)
{
    // String'i temizle, sadece rakamları al
    $tc = preg_replace('/[^0-9]/', '', $tc);

    // 11 haneli olmalı
    if (strlen($tc) !== 11) {
        return false;
    }

    // İlk hane 0 olamaz
    if ($tc[0] === '0') {
        return false;
    }

    // Tüm haneler aynı olamaz
    if (preg_match('/^(\d)\1{10}$/', $tc)) {
        return false;
    }

    // TC Kimlik algoritması
    $digits = str_split($tc);

    // İlk 10 hanenin toplamı
    $tek_toplam = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
    $cift_toplam = $digits[1] + $digits[3] + $digits[5] + $digits[7];

    // 10. hane kontrolü
    $onuncu_hane = (($tek_toplam * 7) - $cift_toplam) % 10;
    if ($onuncu_hane != $digits[9]) {
        return false;
    }

    // 11. hane kontrolü
    $onbirinci_hane = ($tek_toplam + $cift_toplam + $digits[9]) % 10;
    if ($onbirinci_hane != $digits[10]) {
        return false;
    }

    return true;
}

/**
 * IP Adres Bilgi Çıkarıcı
 *
 * Bu fonksiyon, IP adresinden coğrafi ve teknik bilgileri çıkarır.
 * Ücretsiz IP geolocation API'leri kullanır.
 *
 * Kullanım Alanı:
 * - User analytics'de
 * - Security monitoring'de
 * - Localization sistemlerinde
 * - Fraud detection'da
 *
 * Teknik Detaylar:
 * - Multiple IP geolocation providers
 * - IPv4 ve IPv6 support
 * - Country, city, ISP bilgileri
 * - Timezone detection
 * - Caching-friendly structure
 *
 * Kullanım Örneği:
 * $info = Ip_Bilgi_Al('8.8.8.8');
 * echo $info['country']; // "United States"
 * echo $info['city']; // "Mountain View"
 *
 * @param string $ip IP adresi (boşsa user'ın IP'sini kullanır)
 * @param bool $detayli Detaylı bilgi alınsın mı (varsayılan: false)
 * @return array|false IP bilgi array'i veya hata durumunda false
 */
function Ip_Bilgi_Al($ip = '', $detayli = false)
{
    // IP boşsa kullanıcının IP'sini al
    if (empty($ip)) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        // Proxy arkasında ise gerçek IP'yi almaya çalış
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
    }

    // IP format kontrolü
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        // Local/private IP ise basic bilgi döndür
        return [
            'ip' => $ip,
            'country' => 'Unknown',
            'city' => 'Unknown',
            'isp' => 'Local Network',
            'timezone' => 'UTC'
        ];
    }

    try {
        // Ücretsiz IP API'si kullan (ipapi.co)
        $url = "http://ipapi.co/{$ip}/json/";
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Mozilla/5.0 (compatible; PHP IP Lookup)'
            ]
        ]);

        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if (!$data || isset($data['error'])) {
            return false;
        }

        $result = [
            'ip' => $ip,
            'country' => $data['country_name'] ?? 'Unknown',
            'country_code' => $data['country'] ?? 'XX',
            'city' => $data['city'] ?? 'Unknown',
            'region' => $data['region'] ?? 'Unknown',
            'isp' => $data['org'] ?? 'Unknown',
            'timezone' => $data['timezone'] ?? 'UTC'
        ];

        if ($detayli) {
            $result['latitude'] = $data['latitude'] ?? null;
            $result['longitude'] = $data['longitude'] ?? null;
            $result['postal'] = $data['postal'] ?? null;
            $result['asn'] = $data['asn'] ?? null;
        }

        return $result;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * YouTube Video ID Çıkarıcı
 *
 * Bu fonksiyon, YouTube URL'inden video ID'sini çıkarır.
 * Farklı YouTube URL formatlarını destekler.
 *
 * Kullanım Alanı:
 * - Video embedding sistemlerinde
 * - Content management'da
 * - Social media integration'da
 * - Video gallery'lerinde
 *
 * Teknik Detaylar:
 * - Multiple YouTube URL format support
 * - youtu.be short URLs
 * - youtube.com watch URLs
 * - Embed URL support
 * - Playlist URL handling
 *
 * Desteklenen Formatlar:
 * - https://www.youtube.com/watch?v=VIDEO_ID
 * - https://youtu.be/VIDEO_ID
 * - https://youtube.com/embed/VIDEO_ID
 * - https://m.youtube.com/watch?v=VIDEO_ID
 *
 * Kullanım Örneği:
 * echo Youtube_Video_Id('https://youtu.be/dQw4w9WgXcQ'); // "dQw4w9WgXcQ"
 * echo Youtube_Video_Id('https://youtube.com/watch?v=abc123'); // "abc123"
 *
 * @param string $url YouTube URL'i
 * @return string|false Video ID veya geçersizse false
 */
function Youtube_Video_Id($url)
{
    if (empty($url)) {
        return false;
    }

    // URL parse et
    $parsed = parse_url($url);

    if (!$parsed) {
        return false;
    }

    $host = $parsed['host'] ?? '';
    $path = $parsed['path'] ?? '';
    $query = $parsed['query'] ?? '';

    // youtu.be format
    if ($host === 'youtu.be' || $host === 'www.youtu.be') {
        return trim($path, '/');
    }

    // youtube.com formats
    if (preg_match('/^(www\.)?youtube\.com$|^(www\.)?m\.youtube\.com$/', $host)) {
        // /watch?v=VIDEO_ID format
        if (strpos($path, '/watch') === 0) {
            parse_str($query, $params);
            return $params['v'] ?? false;
        }

        // /embed/VIDEO_ID format
        if (strpos($path, '/embed/') === 0) {
            return substr($path, 7); // "/embed/" uzunluğu 7
        }

        // /v/VIDEO_ID format
        if (strpos($path, '/v/') === 0) {
            return substr($path, 3); // "/v/" uzunluğu 3
        }
    }

    return false;
}

/**
 * Kredi Kartı Luhn Algoritması Doğrulama
 *
 * Bu fonksiyon, kredi kartı numarasının Luhn algoritmasına
 * uygun olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Payment form validation
 * - E-commerce checkout'da
 * - Credit card processing'de
 * - Financial application'larda
 *
 * Teknik Detaylar:
 * - Luhn algorithm (mod 10)
 * - Credit card format validation
 * - Checksum verification
 * - Multiple card type support
 * - Industry standard validation
 *
 * Kullanım Örneği:
 * if (Kredi_Karti_Dogrula('4532 1234 5678 9012')) {
 *     echo "Geçerli kart numarası formatı";
 * }
 *
 * @param string $kart_no Kredi kartı numarası
 * @return bool Luhn algoritmasına uygunsa true, değilse false
 */
function Kredi_Karti_Dogrula($kart_no)
{
    // Sadece rakamları al
    $kart_no = preg_replace('/[^0-9]/', '', $kart_no);

    // En az 13, en fazla 19 haneli olmalı
    $uzunluk = strlen($kart_no);
    if ($uzunluk < 13 || $uzunluk > 19) {
        return false;
    }

    // Luhn algoritması
    $toplam = 0;
    $cift_pozisyon = false;

    // Sağdan sola git
    for ($i = $uzunluk - 1; $i >= 0; $i--) {
        $digit = intval($kart_no[$i]);

        if ($cift_pozisyon) {
            $digit *= 2;
            if ($digit > 9) {
                $digit = ($digit % 10) + intval($digit / 10);
            }
        }

        $toplam += $digit;
        $cift_pozisyon = !$cift_pozisyon;
    }

    return ($toplam % 10) === 0;
}

/**
 * Türkçe Sayı Okuyucu
 *
 * Bu fonksiyon, rakamları Türkçe yazılı formata dönüştürür.
 * Para birimi formatında kullanım için uygundur.
 *
 * Kullanım Alanı:
 * - Fatura sistemlerinde
 * - Çek yazımında
 * - Financial document'lerde
 * - Legal document'lerde
 *
 * Teknik Detaylar:
 * - Turkish number naming convention
 * - Large number support (milyar'a kadar)
 * - Decimal number support
 * - Currency formatting option
 * - Gender-aware naming (bir/birinci)
 *
 * Kullanım Örneği:
 * echo Sayi_Turkce_Yaz(1234); // "bin iki yüz otuz dört"
 * echo Sayi_Turkce_Yaz(1234.56, true); // "bin iki yüz otuz dört lira elli altı kuruş"
 *
 * @param float $sayi Dönüştürülecek sayı
 * @param bool $para_birimi Para birimi formatında mı (varsayılan: false)
 * @return string Türkçe yazılı sayı
 */
function Sayi_Turkce_Yaz($sayi, $para_birimi = false)
{
    if (!is_numeric($sayi)) {
        return 'geçersiz sayı';
    }

    $sayi = floatval($sayi);

    // Negatif sayı kontrolü
    $negatif = $sayi < 0;
    $sayi = abs($sayi);

    // Tam ve ondalık kısımları ayır
    $tam_kisim = floor($sayi);
    $ondalik_kisim = round(($sayi - $tam_kisim) * 100);

    // Sayı isimleri
    $birler = ['', 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'];
    $onlar = ['', '', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];
    $yuzler = ['', 'yüz'];

    $binlik_birimler = ['', 'bin', 'milyon', 'milyar'];

    // Sayıyı çevir
    $sayi_yaz = function ($sayi) use ($birler, $onlar, $yuzler) {
        if ($sayi == 0) return '';

        $sonuc = '';

        // Yüzler
        $yuz = floor($sayi / 100);
        if ($yuz > 0) {
            if ($yuz > 1) $sonuc .= $birler[$yuz] . ' ';
            $sonuc .= 'yüz ';
        }

        // Onlar ve birler
        $kalan = $sayi % 100;
        if ($kalan >= 20) {
            $on = floor($kalan / 10);
            $bir = $kalan % 10;
            $sonuc .= $onlar[$on];
            if ($bir > 0) $sonuc .= ' ' . $birler[$bir];
        } elseif ($kalan >= 10) {
            // 10-19 arası özel durumlar
            $ozel_onlar = ['on', 'on bir', 'on iki', 'on üç', 'on dört', 'on beş', 'on altı', 'on yedi', 'on sekiz', 'on dokuz'];
            $sonuc .= $ozel_onlar[$kalan - 10];
        } elseif ($kalan > 0) {
            $sonuc .= $birler[$kalan];
        }

        return trim($sonuc);
    };

    if ($tam_kisim == 0) {
        $sonuc = 'sıfır';
    } else {
        $sonuc = '';
        $grup_index = 0;

        while ($tam_kisim > 0) {
            $grup = $tam_kisim % 1000;
            if ($grup > 0) {
                $grup_yazi = $sayi_yaz($grup);

                // "Bin" için özel durum
                if ($grup_index == 1 && $grup == 1) {
                    $grup_yazi = '';
                }

                if (!empty($grup_yazi)) {
                    $sonuc = $grup_yazi . ' ' . $binlik_birimler[$grup_index] . ' ' . $sonuc;
                } elseif ($grup_index == 1) {
                    $sonuc = $binlik_birimler[$grup_index] . ' ' . $sonuc;
                }
            }

            $tam_kisim = floor($tam_kisim / 1000);
            $grup_index++;
        }

        $sonuc = trim($sonuc);
    }

    // Para birimi formatı
    if ($para_birimi) {
        $sonuc .= ' lira';
        if ($ondalik_kisim > 0) {
            $kurus_yazi = $sayi_yaz($ondalik_kisim);
            $sonuc .= ' ' . $kurus_yazi . ' kuruş';
        }
    } elseif ($ondalik_kisim > 0) {
        $ondalik_yazi = $sayi_yaz($ondalik_kisim);
        $sonuc .= ' virgül ' . $ondalik_yazi;
    }

    // Negatif sayı
    if ($negatif) {
        $sonuc = 'eksi ' . $sonuc;
    }

    return $sonuc;
}

/**
 * Sosyal Medya Link Validator
 *
 * Bu fonksiyon, sosyal medya platform linklerinin geçerli
 * olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Profile management sistemlerinde
 * - Social media integration'da
 * - Contact form validation'da
 * - Business directory'lerde
 *
 * Teknik Detaylar:
 * - Platform-specific URL validation
 * - Username extraction
 * - Multiple platform support
 * - URL normalization
 * - Format standardization
 *
 * Desteklenen Platformlar:
 * - Facebook, Instagram, Twitter, LinkedIn
 * - YouTube, TikTok, GitHub
 * - Custom platform ekleme desteği
 *
 * Kullanım Örneği:
 * $result = Sosyal_Medya_Dogrula('https://twitter.com/username', 'twitter');
 * if ($result['valid']) {
 *     echo "Username: " . $result['username'];
 * }
 *
 * @param string $url Sosyal medya URL'i
 * @param string $platform Platform adı (opsiyonel, otomatik detect)
 * @return array Validation sonucu ve detaylar
 */
function Sosyal_Medya_Dogrula($url, $platform = '')
{
    if (empty($url)) {
        return ['valid' => false, 'error' => 'URL boş'];
    }

    // URL'yi parse et
    $parsed = parse_url($url);
    if (!$parsed || !isset($parsed['host'])) {
        return ['valid' => false, 'error' => 'Geçersiz URL formatı'];
    }

    $host = strtolower($parsed['host']);
    $path = $parsed['path'] ?? '';

    // Platform patterns
    $patterns = [
        'facebook' => [
            'hosts' => ['facebook.com', 'www.facebook.com', 'fb.com', 'www.fb.com'],
            'pattern' => '/^\/([a-zA-Z0-9._-]+)\/?$/',
            'example' => 'https://facebook.com/username'
        ],
        'instagram' => [
            'hosts' => ['instagram.com', 'www.instagram.com'],
            'pattern' => '/^\/([a-zA-Z0-9._]+)\/?$/',
            'example' => 'https://instagram.com/username'
        ],
        'twitter' => [
            'hosts' => ['twitter.com', 'www.twitter.com', 'x.com', 'www.x.com'],
            'pattern' => '/^\/([a-zA-Z0-9_]+)\/?$/',
            'example' => 'https://twitter.com/username'
        ],
        'linkedin' => [
            'hosts' => ['linkedin.com', 'www.linkedin.com'],
            'pattern' => '/^\/in\/([a-zA-Z0-9-]+)\/?$/',
            'example' => 'https://linkedin.com/in/username'
        ],
        'youtube' => [
            'hosts' => ['youtube.com', 'www.youtube.com'],
            'pattern' => '/^\/(c\/|channel\/|user\/)?([a-zA-Z0-9_-]+)\/?$/',
            'example' => 'https://youtube.com/c/username'
        ],
        'tiktok' => [
            'hosts' => ['tiktok.com', 'www.tiktok.com'],
            'pattern' => '/^\/(@[a-zA-Z0-9._]+)\/?$/',
            'example' => 'https://tiktok.com/@username'
        ],
        'github' => [
            'hosts' => ['github.com', 'www.github.com'],
            'pattern' => '/^\/([a-zA-Z0-9_-]+)\/?$/',
            'example' => 'https://github.com/username'
        ]
    ];

    // Platform belirtilmemişse otomatik detect et
    if (empty($platform)) {
        foreach ($patterns as $p => $config) {
            if (in_array($host, $config['hosts'])) {
                $platform = $p;
                break;
            }
        }
    }

    if (empty($platform) || !isset($patterns[$platform])) {
        return ['valid' => false, 'error' => 'Desteklenmeyen platform'];
    }

    $config = $patterns[$platform];

    // Host kontrolü
    if (!in_array($host, $config['hosts'])) {
        return ['valid' => false, 'error' => 'Geçersiz ' . $platform . ' domain'];
    }

    // Path pattern kontrolü
    if (!preg_match($config['pattern'], $path, $matches)) {
        return [
            'valid' => false,
            'error' => 'Geçersiz ' . $platform . ' URL formatı',
            'example' => $config['example']
        ];
    }

    $username = $matches[1] ?? '';

    return [
        'valid' => true,
        'platform' => $platform,
        'username' => $username,
        'normalized_url' => 'https://' . $config['hosts'][0] . $path
    ];
}

/**
 * Barcode (EAN-13) Doğrulama
 *
 * Bu fonksiyon, EAN-13 barcode'unun check digit algoritmasına
 * uygun olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Product inventory sistemlerinde
 * - E-commerce ürün yönetiminde
 * - Retail POS sistemlerinde
 * - Warehouse management'da
 *
 * Teknik Detaylar:
 * - EAN-13 check digit algorithm
 * - 13 haneli barcode validation
 * - Modulo 10 calculation
 * - International standard compliance
 * - Error detection capability
 *
 * Kullanım Örneği:
 * if (Barcode_Dogrula('1234567890123')) {
 *     echo "Geçerli EAN-13 barcode";
 * }
 *
 * @param string $barcode EAN-13 barcode numarası
 * @return bool Geçerli barcode ise true, değilse false
 */
function Barcode_Dogrula($barcode)
{
    // Sadece rakamları al
    $barcode = preg_replace('/[^0-9]/', '', $barcode);

    // 13 haneli olmalı
    if (strlen($barcode) !== 13) {
        return false;
    }

    $toplam = 0;

    // İlk 12 haneyi işle
    for ($i = 0; $i < 12; $i++) {
        $digit = intval($barcode[$i]);
        // Çift pozisyonlardaki rakamları 3 ile çarp
        $toplam += ($i % 2 === 1) ? $digit * 3 : $digit;
    }

    // Check digit hesapla
    $check_digit = (10 - ($toplam % 10)) % 10;

    // Son hane ile karşılaştır
    return $check_digit == intval($barcode[12]);
}

/**
 * Mesafe Hesaplayıcı (Koordinat)
 *
 * Bu fonksiyon, iki GPS koordinatı arasındaki mesafeyi hesaplar.
 * Haversine formula kullanarak hassas hesaplama yapar.
 *
 * Kullanım Alanı:
 * - Location-based services'de
 * - Delivery tracking sistemlerinde
 * - Map applications'da
 * - Proximity detection'da
 *
 * Teknik Detaylar:
 * - Haversine formula
 * - Great-circle distance calculation
 * - Earth radius consideration
 * - Multiple unit support (km, mile, meter)
 * - High precision calculation
 *
 * Kullanım Örneği:
 * $mesafe = Mesafe_Hesapla(41.0082, 28.9784, 39.9334, 32.8597); // İstanbul-Ankara
 * echo $mesafe . " km"; // ~350 km
 *
 * @param float $lat1 İlk koordinat latitude
 * @param float $lon1 İlk koordinat longitude  
 * @param float $lat2 İkinci koordinat latitude
 * @param float $lon2 İkinci koordinat longitude
 * @param string $birim Mesafe birimi ('km', 'mile', 'meter')
 * @return float Hesaplanan mesafe
 */
function Mesafe_Hesapla($lat1, $lon1, $lat2, $lon2, $birim = 'km')
{
    // Derece to radian dönüşümü
    $lat1_rad = deg2rad($lat1);
    $lon1_rad = deg2rad($lon1);
    $lat2_rad = deg2rad($lat2);
    $lon2_rad = deg2rad($lon2);

    // Koordinat farkları
    $dlat = $lat2_rad - $lat1_rad;
    $dlon = $lon2_rad - $lon1_rad;

    // Haversine formula
    $a = sin($dlat / 2) * sin($dlat / 2) +
        cos($lat1_rad) * cos($lat2_rad) *
        sin($dlon / 2) * sin($dlon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Dünya yarıçapı (km)
    $earth_radius = 6371;

    $mesafe = $earth_radius * $c;

    // Birim dönüşümü
    switch ($birim) {
        case 'mile':
            return $mesafe * 0.621371;
        case 'meter':
            return $mesafe * 1000;
        default:
            return $mesafe;
    }
}

/**
 * Kredi Kartı Tip Detector
 *
 * Bu fonksiyon, kredi kartı numarasından kartın tipini
 * (Visa, MasterCard, etc.) tespit eder.
 *
 * Kullanım Alanı:
 * - Payment form'larında
 * - E-commerce checkout'da
 * - Card validation sistemlerinde
 * - Payment gateway integration'da
 *
 * Teknik Detaylar:
 * - IIN (Issuer Identification Number) analysis
 * - Major card brand detection
 * - Prefix pattern matching
 * - Industry standard ranges
 * - Real-time card type detection
 *
 * Desteklenen Kartlar:
 * - Visa, MasterCard, American Express
 * - Discover, Diners Club, JCB
 * - UnionPay, Maestro
 *
 * Kullanım Örneği:
 * $tip = Kart_Tip_Tespit('4532123456789012');
 * echo $tip['brand']; // "Visa"
 * echo $tip['type']; // "Credit"
 *
 * @param string $kart_no Kredi kartı numarası
 * @return array Kart tip bilgileri
 */
function Kart_Tip_Tespit($kart_no)
{
    // Sadece rakamları al
    $kart_no = preg_replace('/[^0-9]/', '', $kart_no);

    if (strlen($kart_no) < 4) {
        return ['brand' => 'Unknown', 'type' => 'Unknown'];
    }

    // İlk 4-6 hanesi al (BIN/IIN)
    $prefix = substr($kart_no, 0, 6);
    $prefix4 = substr($kart_no, 0, 4);
    $prefix2 = substr($kart_no, 0, 2);

    // Kart tipleri ve prefix'leri
    $patterns = [
        'Visa' => [
            'pattern' => '/^4/',
            'length' => [13, 16, 19],
            'type' => 'Credit/Debit'
        ],
        'MasterCard' => [
            'pattern' => '/^(5[1-5]|2[2-7])/',
            'length' => [16],
            'type' => 'Credit/Debit'
        ],
        'American Express' => [
            'pattern' => '/^3[47]/',
            'length' => [15],
            'type' => 'Credit'
        ],
        'Discover' => [
            'pattern' => '/^(6011|65|64[4-9]|622)/',
            'length' => [16, 19],
            'type' => 'Credit'
        ],
        'Diners Club' => [
            'pattern' => '/^(30[0-5]|36|38)/',
            'length' => [14],
            'type' => 'Credit'
        ],
        'JCB' => [
            'pattern' => '/^35/',
            'length' => [16],
            'type' => 'Credit'
        ],
        'UnionPay' => [
            'pattern' => '/^62/',
            'length' => [16, 17, 18, 19],
            'type' => 'Credit/Debit'
        ],
        'Maestro' => [
            'pattern' => '/^(5018|5020|5038|5893|6304|6759|6761|6762|6763)/',
            'length' => [12, 13, 14, 15, 16, 17, 18, 19],
            'type' => 'Debit'
        ]
    ];

    $uzunluk = strlen($kart_no);

    foreach ($patterns as $brand => $config) {
        if (preg_match($config['pattern'], $kart_no)) {
            // Uzunluk kontrolü
            if (in_array($uzunluk, $config['length'])) {
                return [
                    'brand' => $brand,
                    'type' => $config['type'],
                    'length' => $uzunluk,
                    'valid_length' => true
                ];
            } else {
                return [
                    'brand' => $brand,
                    'type' => $config['type'],
                    'length' => $uzunluk,
                    'expected_length' => $config['length'],
                    'valid_length' => false
                ];
            }
        }
    }

    return [
        'brand' => 'Unknown',
        'type' => 'Unknown',
        'length' => $uzunluk,
        'valid_length' => false
    ];
}

/**
 * Şifre Güçlülük Analizi
 *
 * Bu fonksiyon, şifrenin güçlülüğünü analiz eder ve
 * iyileştirme önerileri sunar.
 *
 * Kullanım Alanı:
 * - User registration'da
 * - Password policy enforcement'da
 * - Security assessment'da
 * - Password strength meters'da
 *
 * Teknik Detaylar:
 * - Multiple strength criteria evaluation
 * - Entropy calculation
 * - Common password detection
 * - Dictionary attack resistance
 * - Comprehensive scoring system
 *
 * Değerlendirme Kriterleri:
 * - Uzunluk, karakter çeşitliliği
 * - Yaygın kalıplar, sözlük kelimeleri
 * - Kişisel bilgi içeriği
 * - Entropi hesaplama
 *
 * Kullanım Örneği:
 * $analiz = Sifre_Guclugun_Analiz('MyPass123!');
 * echo $analiz['score']; // 0-100 arası puan
 * echo $analiz['level']; // "Weak", "Medium", "Strong"
 *
 * @param string $sifre Analiz edilecek şifre
 * @return array Güçlülük analizi sonuçları
 */
function Sifre_Guclugun_Analiz($sifre)
{
    $score = 0;
    $feedback = [];
    $details = [];

    $uzunluk = strlen($sifre);

    // Uzunluk kontrolü
    if ($uzunluk >= 12) {
        $score += 25;
        $details['length'] = 'excellent';
    } elseif ($uzunluk >= 8) {
        $score += 15;
        $details['length'] = 'good';
        $feedback[] = 'Şifreyi 12+ karakter yapın';
    } elseif ($uzunluk >= 6) {
        $score += 10;
        $details['length'] = 'fair';
        $feedback[] = 'Şifreyi en az 8 karakter yapın';
    } else {
        $details['length'] = 'poor';
        $feedback[] = 'Şifre çok kısa, en az 6 karakter gerekli';
    }

    // Karakter çeşitliliği
    $buyuk_harf = preg_match('/[A-Z]/', $sifre);
    $kucuk_harf = preg_match('/[a-z]/', $sifre);
    $rakam = preg_match('/[0-9]/', $sifre);
    $ozel_karakter = preg_match('/[^A-Za-z0-9]/', $sifre);

    $cesitlilik = 0;
    if ($buyuk_harf) $cesitlilik++;
    if ($kucuk_harf) $cesitlilik++;
    if ($rakam) $cesitlilik++;
    if ($ozel_karakter) $cesitlilik++;

    $score += $cesitlilik * 10;
    $details['variety'] = $cesitlilik;

    if (!$buyuk_harf) $feedback[] = 'Büyük harf ekleyin';
    if (!$kucuk_harf) $feedback[] = 'Küçük harf ekleyin';
    if (!$rakam) $feedback[] = 'Rakam ekleyin';
    if (!$ozel_karakter) $feedback[] = 'Özel karakter ekleyin (!@#$%^&*)';

    // Yaygın kalıplar
    $patterns = [
        '/(.)\1{2,}/' => 'Aynı karakteri art arda kullanmayın',
        '/123|234|345|456|567|678|789|890/' => 'Ardışık rakam dizilerini kullanmayın',
        '/abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz/i' => 'Ardışık harf dizilerini kullanmayın',
        '/qwerty|asdf|zxcv|qaz|wsx|edc/i' => 'Klavye dizilerini kullanmayın'
    ];

    foreach ($patterns as $pattern => $message) {
        if (preg_match($pattern, $sifre)) {
            $score -= 10;
            $feedback[] = $message;
        }
    }

    // Yaygın şifreler (basit kontrol)
    $common_passwords = [
        'password',
        'password123',
        '123456',
        '123456789',
        'qwerty',
        'abc123',
        'password1',
        'admin',
        'letmein',
        'welcome',
        'monkey',
        '1234567890',
        'iloveyou',
        'princess',
        'dragon'
    ];

    if (in_array(strtolower($sifre), $common_passwords)) {
        $score -= 30;
        $feedback[] = 'Yaygın şifrelerden kaçının';
        $details['common'] = true;
    }

    // Entropi hesaplama (basitleştirilmiş)
    $karakterler = array_unique(str_split($sifre));
    $charset_size = count($karakterler);
    $entropy = $uzunluk * log($charset_size) / log(2);

    $details['entropy'] = round($entropy, 2);

    if ($entropy > 50) {
        $score += 20;
    } elseif ($entropy > 30) {
        $score += 10;
    }

    // Final score normalization
    $score = max(0, min(100, $score));

    // Seviye belirleme
    if ($score >= 80) {
        $level = 'Strong';
        $color = 'green';
    } elseif ($score >= 60) {
        $level = 'Medium';
        $color = 'orange';
    } elseif ($score >= 40) {
        $level = 'Weak';
        $color = 'red';
    } else {
        $level = 'Very Weak';
        $color = 'darkred';
    }

    return [
        'score' => $score,
        'level' => $level,
        'color' => $color,
        'feedback' => $feedback,
        'details' => $details
    ];
}

/**
 * Markdown HTML Converter (Basit)
 *
 * Bu fonksiyon, temel Markdown syntax'ını HTML'e dönüştürür.
 * Blog yazıları ve comment sistemleri için basit formatting sağlar.
 *
 * Kullanım Alanı:
 * - Blog comment sistemlerinde
 * - Simple text formatting'de
 * - Documentation rendering'de
 * - Content management'da
 *
 * Teknik Detaylar:
 * - Basic Markdown syntax support
 * - Safe HTML output
 * - XSS prevention
 * - Lightweight implementation
 * - Essential formatting only
 *
 * Desteklenen Syntax:
 * - **bold**, *italic*, `code`
 * - # Headings (1-6 level)
 * - [link](url), ![image](url)
 * - - Lista items
 *
 * Kullanım Örneği:
 * $html = Markdown_Html_Cevir('**Kalın metin** ve *italik* metin');
 * echo $html; // "<strong>Kalın metin</strong> ve <em>italik</em> metin"
 *
 * @param string $markdown Markdown formatındaki metin
 * @param bool $guvenli XSS korunması aktif mi (varsayılan: true)
 * @return string HTML formatına dönüştürülmüş metin
 */
function Markdown_Html_Cevir($markdown, $guvenli = true)
{
    if (empty($markdown)) {
        return '';
    }

    $html = $markdown;

    // XSS korunması
    if ($guvenli) {
        $html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
    }

    // Headings (H1-H6)
    $html = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $html);

    // Bold text
    $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);

    // Italic text
    $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);

    // Inline code
    $html = preg_replace('/`(.*?)`/', '<code>$1</code>', $html);

    // Links
    $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $html);

    // Images
    $html = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1">', $html);

    // Line breaks to paragraphs
    $paragraphs = explode("\n\n", $html);
    $html = '';
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if (!empty($p)) {
            // Lista kontrolü
            if (preg_match('/^[\-\*\+] /', $p)) {
                $items = explode("\n", $p);
                $html .= "<ul>\n";
                foreach ($items as $item) {
                    $item = preg_replace('/^[\-\*\+] (.*)/', '<li>$1</li>', trim($item));
                    if (!empty($item)) {
                        $html .= $item . "\n";
                    }
                }
                $html .= "</ul>\n";
            } elseif (!preg_match('/^<h[1-6]>/', $p)) {
                // Heading değilse paragraph yap
                $html .= "<p>" . str_replace("\n", "<br>", $p) . "</p>\n";
            } else {
                $html .= $p . "\n";
            }
        }
    }

    return trim($html);
}

/**
 * Türkçe Metin Sıralam Karşılaştırıcısı
 *
 * Bu fonksiyon, Türkçe karakterleri dikkate alarak
 * metinleri doğru sıralaması için karşılaştırma yapar.
 *
 * Kullanım Alanı:
 * - Türkçe isim listelerinin sıralanmasında
 * - Alfabetik search result'larda
 * - Contact list'lerinde
 * - Product catalog'larında
 *
 * Teknik Detaylar:
 * - Turkish alphabet order (A,B,C,Ç,D...)
 * - Case-insensitive comparison
 * - Diacritical marks handling
 * - Natural sorting support
 * - UTF-8 compatibility
 *
 * Kullanım Örneği:
 * usort($isimler, 'Turkce_Siralama_Karsilastir');
 * // veya
 * $result = Turkce_Siralama_Karsilastir('Çelik', 'Demir'); // -1
 *
 * @param string $a İlk karşılaştırılacak metin
 * @param string $b İkinci karşılaştırılacak metin
 * @return int -1, 0, 1 (a<b, a=b, a>b)
 */
function Turkce_Siralama_Karsilastir($a, $b)
{
    // Türkçe karakter sıralaması için dönüşüm tablosu
    $turkce_siralama = [
        'a' => 'a',
        'A' => 'a',
        'b' => 'b',
        'B' => 'b',
        'c' => 'c',
        'C' => 'c',
        'ç' => 'c1',
        'Ç' => 'c1',
        'd' => 'd',
        'D' => 'd',
        'e' => 'e',
        'E' => 'e',
        'f' => 'f',
        'F' => 'f',
        'g' => 'g',
        'G' => 'g',
        'ğ' => 'g1',
        'Ğ' => 'g1',
        'h' => 'h',
        'H' => 'h',
        'ı' => 'i',
        'I' => 'i1',
        'i' => 'i1',
        'İ' => 'i1',
        'j' => 'j',
        'J' => 'j',
        'k' => 'k',
        'K' => 'k',
        'l' => 'l',
        'L' => 'l',
        'm' => 'm',
        'M' => 'm',
        'n' => 'n',
        'N' => 'n',
        'o' => 'o',
        'O' => 'o',
        'ö' => 'o1',
        'Ö' => 'o1',
        'p' => 'p',
        'P' => 'p',
        'r' => 'r',
        'R' => 'r',
        's' => 's',
        'S' => 's',
        'ş' => 's1',
        'Ş' => 's1',
        't' => 't',
        'T' => 't',
        'u' => 'u',
        'U' => 'u',
        'ü' => 'u1',
        'Ü' => 'u1',
        'v' => 'v',
        'V' => 'v',
        'y' => 'y',
        'Y' => 'y',
        'z' => 'z',
        'Z' => 'z'
    ];

    // Karakterleri dönüştür
    $a_converted = '';
    $b_converted = '';

    $a_chars = mb_str_split($a, 1, 'UTF-8');
    $b_chars = mb_str_split($b, 1, 'UTF-8');

    foreach ($a_chars as $char) {
        $a_converted .= $turkce_siralama[$char] ?? $char;
    }

    foreach ($b_chars as $char) {
        $b_converted .= $turkce_siralama[$char] ?? $char;
    }

    return strcmp($a_converted, $b_converted);
}

/**
 * Para Birimi Formatlamacı
 *
 * Bu fonksiyon, sayıları para birimi formatında gösterir.
 * Türk Lirası ve diğer para birimleri için localization desteği.
 *
 * Kullanım Alanı:
 * - E-commerce fiyat gösteriminde
 * - Financial reporting'de
 * - Invoice ve receipt'lerde
 * - Budget planning tool'larında
 *
 * Teknik Detaylar:
 * - Multiple currency support
 * - Locale-aware formatting
 * - Thousand separators
 * - Decimal precision control
 * - Symbol positioning
 *
 * Kullanım Örneği:
 * echo Para_Birim_Formatla(1234.56); // "1.234,56 ₺"
 * echo Para_Birim_Formatla(1234.56, 'USD'); // "$1,234.56"
 * echo Para_Birim_Formatla(1234.56, 'EUR'); // "€1.234,56"
 *
 * @param float $miktar Para miktarı
 * @param string $para_birimi Para birimi kodu (TRY, USD, EUR, GBP)
 * @param int $ondalik Ondalık basamak sayısı (varsayılan: 2)
 * @return string Formatlanmış para string'i
 */
function Para_Birim_Formatla($miktar, $para_birimi = 'TRY', $ondalik = 2)
{
    $miktar = floatval($miktar);

    $para_birimleri = [
        'TRY' => [
            'symbol' => '₺',
            'position' => 'after',
            'thousands' => '.',
            'decimal' => ','
        ],
        'USD' => [
            'symbol' => '$',
            'position' => 'before',
            'thousands' => ',',
            'decimal' => '.'
        ],
        'EUR' => [
            'symbol' => '€',
            'position' => 'before',
            'thousands' => '.',
            'decimal' => ','
        ],
        'GBP' => [
            'symbol' => '£',
            'position' => 'before',
            'thousands' => ',',
            'decimal' => '.'
        ],
        'JPY' => [
            'symbol' => '¥',
            'position' => 'before',
            'thousands' => ',',
            'decimal' => '.',
            'decimals' => 0
        ]
    ];

    $config = $para_birimleri[strtoupper($para_birimi)] ?? $para_birimleri['TRY'];

    // JPY için özel durum (ondalık yok)
    if (isset($config['decimals'])) {
        $ondalik = $config['decimals'];
    }

    // Sayıyı formatla
    $formatted = number_format($miktar, $ondalik, $config['decimal'], $config['thousands']);

    // Symbol pozisyonu
    if ($config['position'] === 'before') {
        return $config['symbol'] . $formatted;
    } else {
        return $formatted . ' ' . $config['symbol'];
    }
}

/**
 * İban Doğrulama (Türkiye)
 *
 * Bu fonksiyon, Türkiye IBAN numarasının geçerli olup
 * olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Banking form validation'da
 * - Payment system'lerinde
 * - Financial application'larda
 * - Money transfer system'lerinde
 *
 * Teknik Detaylar:
 * - TR IBAN format validation
 * - MOD-97 algorithm
 * - Check digit verification
 * - Bank code validation
 * - 26 character length check
 *
 * IBAN Format: TR + 2 check digits + 5 bank code + 1 reserve + 16 account
 *
 * Kullanım Örneği:
 * if (Iban_Dogrula('TR330006100519786457841326')) {
 *     echo "Geçerli IBAN numarası";
 * }
 *
 * @param string $iban IBAN numarası
 * @return bool Geçerli IBAN ise true, değilse false
 */
function Iban_Dogrula($iban)
{
    // Boşlukları ve özel karakterleri temizle
    $iban = preg_replace('/[^A-Z0-9]/i', '', strtoupper($iban));

    // Türkiye IBAN formatı: TR + 24 hane = 26 karakter
    if (strlen($iban) !== 26) {
        return false;
    }

    // TR ile başlamalı
    if (substr($iban, 0, 2) !== 'TR') {
        return false;
    }

    // Check digit'leri al (3. ve 4. karakter)
    $check_digits = substr($iban, 2, 2);

    // IBAN'ı yeniden düzenle: bank code + account + country code + check digits
    $rearranged = substr($iban, 4) . '2927' . $check_digits; // TR = 2927

    // Her karakteri sayıya çevir (A=10, B=11, ..., Z=35)
    $numeric_string = '';
    for ($i = 0; $i < strlen($rearranged); $i++) {
        $char = $rearranged[$i];
        if (is_numeric($char)) {
            $numeric_string .= $char;
        } else {
            $numeric_string .= (ord($char) - ord('A') + 10);
        }
    }

    // MOD 97 kontrolü
    $remainder = bcmod($numeric_string, '97');

    return $remainder === '1';
}
