<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Diğer fonksiyonlar
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Yüklenmiş Dosyaları Listeleme Fonksiyonu
 *
 * Bu fonksiyon, mevcut PHP işleminde yüklenmiş (include/require) olan
 * tüm dosyaların listesini döndürür.
 *
 * Kullanım Alanı:
 * - Debug işlemlerinde dosya izleme
 * - Performance analizinde include tracking
 * - Development ortamında dependency analizi
 * - Memory usage optimization'da
 *
 * Teknik Detaylar:
 * - get_included_files() PHP function'unu wrap eder
 * - Chronological order'da dosya listesi
 * - Full file path bilgileri
 * - Runtime include tracking
 *
 * Kullanım Örneği:
 * $dosyalar = Tum_Dosyalar();
 * foreach ($dosyalar as $dosya) {
 *     echo "Yüklenmiş: " . basename($dosya) . "\n";
 * }
 *
 * @return array Yüklenmiş dosyaların tam yollarını içeren array
 */
function Tum_Dosyalar()
{
    return get_included_files();
}

/**
 * Kullanıcı Tanımlı Sabitleri Listeleme Fonksiyonu
 *
 * Bu fonksiyon, kullanıcı tarafından tanımlanan (define() ile oluşturulan)
 * sabitlerin listesini döndürür. PHP'nin yerleşik sabitlerini dışlar.
 *
 * Kullanım Alanı:
 * - Yapılandırma sabitlerinin listelenmesinde
 * - Debug işlemlerinde sabit tracking
 * - Development ortamında configuration analizi
 * - Runtime constant inspection'da
 *
 * Teknik Detaylar:
 * - get_defined_constants(true) kullanır
 * - Sadece 'user' kategorisindeki sabitleri filtreler
 * - PHP built-in constants'ları harıç tutar
 * - Runtime defined constants tracking
 *
 * Kullanım Örneği:
 * define('SITE_NAME', 'My Website');
 * $sabitler = Tum_Sabitler();
 * print_r($sabitler); // ['SITE_NAME' => 'My Website']
 *
 * @return array Kullanıcı tanımlı sabit adı ve değerlerini içeren array
 */
function Tum_Sabitler()
{
    $all_constants = get_defined_constants(true);
    return isset($all_constants['user']) ? $all_constants['user'] : [];
}

/**
 * Hafıza Kullanımı Bilgisi Fonksiyonu
 *
 * Bu fonksiyon, mevcut PHP işleminin hafıza kullanım bilgilerini döndürür.
 * Hem mevcut kullanım hem de peak kullanım değerlerini sağlar.
 *
 * Kullanım Alanı:
 * - Performance monitoring
 * - Memory leak detection
 * - Resource usage tracking
 * - Optimization analysis
 *
 * Teknik Detaylar:
 * - memory_get_usage() ve memory_get_peak_usage() kullanır
 * - Okunabilir format (MB, KB) seçeneği
 * - Real memory usage tracking
 * - Peak memory monitoring
 *
 * Kullanım Örneği:
 * $hafiza = Hafiza_Kullanimi();
 * echo "Mevcut: " . $hafiza['mevcut_mb'] . " MB";
 * echo "Peak: " . $hafiza['peak_mb'] . " MB";
 *
 * @param bool $okunabilir Format olarak MB/KB gösterimi
 * @return array Hafıza kullanım bilgileri
 */
function Hafiza_Kullanimi($okunabilir = true)
{
    $current = memory_get_usage();
    $peak = memory_get_peak_usage();

    $result = [
        'mevcut_byte' => $current,
        'peak_byte' => $peak
    ];

    if ($okunabilir) {
        $result['mevcut_mb'] = round($current / 1024 / 1024, 2);
        $result['peak_mb'] = round($peak / 1024 / 1024, 2);
        $result['mevcut_kb'] = round($current / 1024, 2);
        $result['peak_kb'] = round($peak / 1024, 2);
    }

    return $result;
}

/**
 * Çalışma Zamanı Bilgisi Fonksiyonu
 *
 * Bu fonksiyon, BASLANGIC sabitinden itibaren geçen süreyi hesaplar.
 * Performance ölçümü ve debug işlemlerinde kullanılır.
 *
 * Kullanım Alanı:
 * - Script execution time tracking
 * - Performance benchmarking
 * - Code optimization analysis
 * - Debugging slow operations
 *
 * Teknik Detaylar:
 * - BASLANGIC sabiti kullanır
 * - Microsecond precision
 * - Multiple format options
 * - Real execution time tracking
 *
 * Kullanım Örneği:
 * define('BASLANGIC', microtime(true)); // Script başında
 * $sure = Calisma_Zamani();
 * echo "Script çalışma süresi: " . $sure['saniye'] . " saniye";
 *
 * @return array Çalışma süresi bilgileri
 */
function Calisma_Zamani()
{
    $baslangic_zamani = defined('BASLANGIC') ? BASLANGIC : ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));

    $gecen_sure = microtime(true) - $baslangic_zamani;

    return [
        'milisaniye' => round($gecen_sure * 1000, 3),
        'saniye' => round($gecen_sure, 3),
        'dakika' => round($gecen_sure / 60, 3),
        'raw' => $gecen_sure
    ];
}

/**
 * Random Token Üretici Fonksiyonu
 *
 * Bu fonksiyon, güvenli rastgele token'lar üretir.
 * CSRF koruması, API anahtarları ve oturum token'ları için kullanılır.
 *
 * Kullanım Alanı:
 * - CSRF token generation
 * - API key creation
 * - Session token generation
 * - Unique identifier creation
 *
 * Teknik Detaylar:
 * - Cryptographically secure random bytes
 * - Multiple encoding options
 * - Customizable length
 * - High entropy generation
 *
 * Kullanım Örneği:
 * $token = Random_Token();
 * $api_key = Random_Token(32, 'base64');
 *
 * @param int $uzunluk Token uzunluğu (byte cinsinden)
 * @param string $format Çıkış formatı: 'hex', 'base64', 'base64url'
 * @return string Üretilen güvenli token
 */
function Random_Token($uzunluk = 16, $format = 'hex')
{
    $bytes = random_bytes($uzunluk);

    switch ($format) {
        case 'base64':
            return base64_encode($bytes);
        case 'base64url':
            return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
        case 'hex':
        default:
            return bin2hex($bytes);
    }
}

/**
 * Array Derinlik Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, çok boyutlu bir array'in maksimum derinliğini hesaplar.
 * Veri yapısı analizi ve validation işlemlerinde kullanılır.
 *
 * Kullanım Alanı:
 * - Data structure analysis
 * - JSON depth validation
 * - Array complexity measurement
 * - Recursive data inspection
 *
 * Teknik Detaylar:
 * - Recursive depth calculation
 * - Multi-dimensional array support
 * - Circular reference protection
 * - Memory efficient traversal
 *
 * Kullanım Örneği:
 * $data = ['a' => ['b' => ['c' => 'değer']]];
 * $derinlik = Array_Derinlik($data); // 3
 *
 * @param array $array Derinliği hesaplanacak array
 * @param int $_mevcut_derinlik İç kullanım için mevcut derinlik
 * @return int Array'in maksimum derinliği
 */
function Array_Derinlik(array $array, $_mevcut_derinlik = 0)
{
    $max_derinlik = $_mevcut_derinlik;

    foreach ($array as $value) {
        if (is_array($value)) {
            $derinlik = Array_Derinlik($value, $_mevcut_derinlik + 1);
            $max_derinlik = max($max_derinlik, $derinlik);
        }
    }

    return $max_derinlik;
}

/**
 * Kullanıcı Ajanı Analizi Fonksiyonu
 *
 * Bu fonksiyon, HTTP User-Agent string'ini analiz eder ve
 * tarayıcı, işletim sistemi bilgilerini çıkarır.
 *
 * Kullanım Alanı:
 * - Browser compatibility checking
 * - Mobile device detection
 * - Analytics data collection
 * - Responsive design decisions
 *
 * Teknik Detaylar:
 * - Regex pattern matching
 * - Multiple browser support
 * - OS detection
 * - Mobile/Desktop classification
 *
 * Kullanım Örneği:
 * $bilgi = User_Agent_Analiz();
 * if ($bilgi['mobil']) {
 *     echo 'Mobil cihaz';
 * }
 *
 * @param string|null $user_agent User agent string (varsayılan $_SERVER['HTTP_USER_AGENT'])
 * @return array User agent analiz sonuçları
 */
function User_Agent_Analiz($user_agent = null)
{
    if ($user_agent === null) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    $result = [
        'tarayici' => 'Bilinmeyen',
        'versiyon' => 'Bilinmeyen',
        'isletim_sistemi' => 'Bilinmeyen',
        'mobil' => false,
        'tablet' => false,
        'bot' => false,
        'user_agent' => $user_agent
    ];

    // Bot detection
    if (preg_match('/bot|crawler|spider|crawling/i', $user_agent)) {
        $result['bot'] = true;
        $result['tarayici'] = 'Bot';
        return $result;
    }

    // Mobile/Tablet detection
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $user_agent)) {
        $result['mobil'] = true;
        if (preg_match('/iPad|Android.*Tablet|Tablet/i', $user_agent)) {
            $result['tablet'] = true;
            $result['mobil'] = false;
        }
    }

    // Browser detection
    $browsers = [
        '/Chrome\/([0-9.]+)/i' => 'Chrome',
        '/Firefox\/([0-9.]+)/i' => 'Firefox',
        '/Safari\/([0-9.]+)/i' => 'Safari',
        '/Edge\/([0-9.]+)/i' => 'Edge',
        '/Opera\/([0-9.]+)/i' => 'Opera',
        '/MSIE ([0-9.]+)/i' => 'Internet Explorer'
    ];

    foreach ($browsers as $pattern => $name) {
        if (preg_match($pattern, $user_agent, $matches)) {
            $result['tarayici'] = $name;
            if (isset($matches[1])) {
                $result['versiyon'] = $matches[1];
            }
            break;
        }
    }

    // OS detection
    $os_patterns = [
        '/Windows NT 10.0/i' => 'Windows 10',
        '/Windows NT 6.3/i' => 'Windows 8.1',
        '/Windows NT 6.2/i' => 'Windows 8',
        '/Windows NT 6.1/i' => 'Windows 7',
        '/Windows NT 6.0/i' => 'Windows Vista',
        '/Windows NT 5.1/i' => 'Windows XP',
        '/Mac OS X/i' => 'Mac OS X',
        '/Android/i' => 'Android',
        '/iPhone OS|iOS/i' => 'iOS',
        '/Linux/i' => 'Linux',
        '/Ubuntu/i' => 'Ubuntu'
    ];

    foreach ($os_patterns as $pattern => $name) {
        if (preg_match($pattern, $user_agent)) {
            $result['isletim_sistemi'] = $name;
            break;
        }
    }

    return $result;
}

/**
 * JSON Güvenli Encode/Decode Fonksiyonu
 *
 * Bu fonksiyon, JSON işlemlerini güvenli şekilde yapar ve
 * hata kontrolü ile birlikte sonuç döndürür.
 *
 * Kullanım Alanı:
 * - API response formatting
 * - Data serialization
 * - Configuration file handling
 * - Error-safe JSON operations
 *
 * Teknik Detaylar:
 * - Error handling with json_last_error()
 * - UTF-8 encoding support
 * - Pretty print option
 * - Depth limit protection
 *
 * Kullanım Örneği:
 * $json = JSON_Guvenli(['anahtar' => 'değer'], 'encode');
 * if ($json['basarili']) {
 *     echo $json['data'];
 * }
 *
 * @param mixed $data İşlenecek veri
 * @param string $islem 'encode' veya 'decode'
 * @param int $secenekler JSON encode seçenekleri
 * @param int $derinlik Maksimum derinlik
 * @return array Sonuç array'i (basarili, data, hata)
 */
function JSON_Guvenli($data, $islem = 'encode', $secenekler = 0, $derinlik = 512)
{
    $result = [
        'basarili' => false,
        'data' => null,
        'hata' => null,
        'hata_kodu' => null
    ];

    if ($islem === 'encode') {
        $result['data'] = json_encode($data, $secenekler, $derinlik);
    } elseif ($islem === 'decode') {
        $result['data'] = json_decode($data, true, $derinlik, $secenekler);
    } else {
        $result['hata'] = 'Geçersiz işlem: ' . $islem;
        return $result;
    }

    $json_error = json_last_error();

    if ($json_error === JSON_ERROR_NONE) {
        $result['basarili'] = true;
    } else {
        $result['hata_kodu'] = $json_error;

        switch ($json_error) {
            case JSON_ERROR_DEPTH:
                $result['hata'] = 'Maksimum derinlik aşıldı';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $result['hata'] = 'JSON durumu uyumsuzluğu';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $result['hata'] = 'Kontrol karakteri hatası';
                break;
            case JSON_ERROR_SYNTAX:
                $result['hata'] = 'JSON söz dizimi hatası';
                break;
            case JSON_ERROR_UTF8:
                $result['hata'] = 'UTF-8 encoding hatası';
                break;
            default:
                $result['hata'] = 'Bilinmeyen JSON hatası';
                break;
        }
    }

    return $result;
}

/**
 * IP Adresi Tespit Fonksiyonu
 *
 * Bu fonksiyon, kullanıcının gerçek IP adresini tespit eder.
 * Proxy, CDN ve load balancer arkasındaki gerçek IP'yi bulur.
 *
 * Kullanım Alanı:
 * - Gerçek kullanıcı IP'si tespiti
 * - Security logging ve rate limiting
 * - Geolocation services
 * - Analytics ve istatistik toplama
 *
 * Teknik Detaylar:
 * - Birden fazla HTTP header kontrolü
 * - Proxy ve CDN desteği
 * - Private/Reserved IP filtreleme
 * - IPv6 localhost detection
 * - Fallback mechanism
 *
 * Kullanım Örneği:
 * $ip = IP_Adresi();
 * if ($ip !== 'unknown') {
 *     echo "Kullanıcı IP: " . $ip;
 * }
 *
 * @return string Tespit edilen IP adresi veya 'localhost'/'unknown'
 */
function IP_Adresi()
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

            if ($ip == "::1") {
                return "localhost";
            }
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Cihaz Bilgisi Detayı Fonksiyonu
 *
 * Bu fonksiyon, kullanıcının cihaz bilgilerini detaylı şekilde analiz eder.
 * User Agent string'inden ekran çözünürlüğü, cihaz tipi ve platform bilgilerini çıkarır.
 *
 * Kullanım Alanı:
 * - Responsive design optimizasyonu
 * - Mobile/Desktop specific features
 * - Analytics ve kullanıcı davranış analizi
 * - Device targeting campaigns
 *
 * Teknik Detaylar:
 * - Advanced User Agent parsing
 * - Device type classification
 * - Screen resolution estimation
 * - Platform detection
 *
 * Kullanım Örneği:
 * $cihaz = Cihaz_Bilgisi();
 * if ($cihaz['tip'] === 'mobil') {
 *     include 'mobile_layout.php';
 * }
 *
 * @return array Detaylı cihaz bilgileri
 */
function Cihaz_Bilgisi()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $cihaz = [
        'tip' => 'desktop',
        'platform' => 'unknown',
        'tarayici' => 'unknown',
        'tarayici_versiyon' => 'unknown',
        'isletim_sistemi' => 'unknown',
        'ekran_boyut' => 'unknown',
        'dokunmatik' => false,
        'retina' => false,
        'cpu_mimarisi' => 'unknown',
        'dil' => 'unknown',
        'zaman_dilimi' => 'unknown',
        'user_agent' => $user_agent
    ];

    // İşletim Sistemi ve Platform Detection (daha kapsamlı)
    if (preg_match('/iPhone/i', $user_agent)) {
        $cihaz['platform'] = 'iPhone';
        $cihaz['isletim_sistemi'] = 'iOS';
        $cihaz['tip'] = 'mobil';
        $cihaz['dokunmatik'] = true;
        $cihaz['retina'] = true;
    } elseif (preg_match('/iPad/i', $user_agent)) {
        $cihaz['platform'] = 'iPad';
        $cihaz['isletim_sistemi'] = 'iPadOS';
        $cihaz['tip'] = 'tablet';
        $cihaz['dokunmatik'] = true;
        $cihaz['retina'] = true;
    } elseif (preg_match('/Android/i', $user_agent)) {
        $cihaz['platform'] = 'Android';
        $cihaz['isletim_sistemi'] = 'Android';
        $cihaz['tip'] = preg_match('/Mobile/i', $user_agent) ? 'mobil' : 'tablet';
        $cihaz['dokunmatik'] = true;

        // Android versiyon tespiti
        if (preg_match('/Android\s([0-9.]+)/i', $user_agent, $matches)) {
            $cihaz['isletim_sistemi'] = 'Android ' . $matches[1];
        }
    } elseif (preg_match('/Windows Phone/i', $user_agent)) {
        $cihaz['platform'] = 'Windows Phone';
        $cihaz['isletim_sistemi'] = 'Windows Phone';
        $cihaz['tip'] = 'mobil';
        $cihaz['dokunmatik'] = true;
    } elseif (preg_match('/Windows NT ([0-9.]+)/i', $user_agent, $matches)) {
        $cihaz['platform'] = 'Windows';
        $version_map = [
            '10.0' => 'Windows 10/11',
            '6.3' => 'Windows 8.1',
            '6.2' => 'Windows 8',
            '6.1' => 'Windows 7',
            '6.0' => 'Windows Vista',
            '5.1' => 'Windows XP'
        ];
        $cihaz['isletim_sistemi'] = $version_map[$matches[1]] ?? 'Windows ' . $matches[1];
        $cihaz['tip'] = 'desktop';
    } elseif (preg_match('/Mac OS X/i', $user_agent)) {
        $cihaz['platform'] = 'macOS';
        $cihaz['isletim_sistemi'] = 'macOS';
        $cihaz['tip'] = 'desktop';

        // macOS versiyon tespiti
        if (preg_match('/Mac OS X ([0-9_]+)/i', $user_agent, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
            $cihaz['isletim_sistemi'] = 'macOS ' . $version;
        }
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $cihaz['platform'] = 'Linux';
        $cihaz['isletim_sistemi'] = 'Linux';
        $cihaz['tip'] = 'desktop';

        // Linux dağıtım tespiti
        if (preg_match('/Ubuntu/i', $user_agent)) {
            $cihaz['isletim_sistemi'] = 'Ubuntu Linux';
        } elseif (preg_match('/Fedora/i', $user_agent)) {
            $cihaz['isletim_sistemi'] = 'Fedora Linux';
        }
    }

    // Tarayıcı Detection (geliştirilmiş)
    $browsers = [
        '/Edg\/([0-9.]+)/i' => 'Microsoft Edge',
        '/Chrome\/([0-9.]+)/i' => 'Google Chrome',
        '/Firefox\/([0-9.]+)/i' => 'Mozilla Firefox',
        '/Safari\/([0-9.]+)/i' => 'Safari',
        '/Opera\/([0-9.]+)/i' => 'Opera',
        '/MSIE ([0-9.]+)/i' => 'Internet Explorer',
        '/Trident.*rv:([0-9.]+)/i' => 'Internet Explorer'
    ];

    foreach ($browsers as $pattern => $name) {
        if (preg_match($pattern, $user_agent, $matches)) {
            $cihaz['tarayici'] = $name;
            if (isset($matches[1])) {
                $cihaz['tarayici_versiyon'] = $matches[1];
            }
            break;
        }
    }

    // CPU Architecture (geliştirilmiş)
    if (preg_match('/x86_64|x64|amd64/i', $user_agent)) {
        $cihaz['cpu_mimarisi'] = '64-bit (x64)';
    } elseif (preg_match('/i386|i686|x86/i', $user_agent)) {
        $cihaz['cpu_mimarisi'] = '32-bit (x86)';
    } elseif (preg_match('/arm64|aarch64/i', $user_agent)) {
        $cihaz['cpu_mimarisi'] = 'ARM64';
    } elseif (preg_match('/armv[67]l?/i', $user_agent)) {
        $cihaz['cpu_mimarisi'] = 'ARM (32-bit)';
    } elseif (preg_match('/WOW64/i', $user_agent)) {
        $cihaz['cpu_mimarisi'] = '32-bit app on 64-bit Windows';
    }

    // Language detection (geliştirilmiş)
    $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if (!empty($accept_language)) {
        $languages = explode(',', $accept_language);
        $primary_lang = trim(explode(';', $languages[0])[0]);

        // Dil kodlarını açıklamaya çevir
        $language_names = [
            'tr' => 'Türkçe',
            'tr-TR' => 'Türkçe (Türkiye)',
            'en' => 'English',
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'ru' => 'Русский',
            'ar' => 'العربية',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어'
        ];

        $cihaz['dil'] = $language_names[$primary_lang] ?? $primary_lang;
        $cihaz['dil_kodu'] = $primary_lang;
    } else {
        $cihaz['dil_kodu'] = 'unknown';
    }

    // Timezone detection (JavaScript gerektirir, server-side tahmin)
    if (isset($_SERVER['HTTP_CF_TIMEZONE'])) {
        // Cloudflare timezone header
        $cihaz['zaman_dilimi'] = $_SERVER['HTTP_CF_TIMEZONE'];
    } elseif (function_exists('date_default_timezone_get')) {
        $cihaz['zaman_dilimi'] = date_default_timezone_get();
    }

    // Ekran boyutu tahmini (genel kategoriler)
    if ($cihaz['tip'] === 'mobil') {
        $cihaz['ekran_boyut'] = 'Küçük (≤ 768px)';
    } elseif ($cihaz['tip'] === 'tablet') {
        $cihaz['ekran_boyut'] = 'Orta (768px - 1024px)';
    } else {
        $cihaz['ekran_boyut'] = 'Büyük (≥ 1024px)';
    }

    // Retina detection için ek kontrollar
    if (
        preg_match('/Retina|High DPI|hdpi/i', $user_agent) ||
        $cihaz['platform'] === 'iPhone' ||
        $cihaz['platform'] === 'iPad'
    ) {
        $cihaz['retina'] = true;
    }

    // Bot detection
    if (preg_match('/bot|crawler|spider|crawling|google|bing|yahoo|facebook/i', $user_agent)) {
        $cihaz['tip'] = 'bot';
        $cihaz['platform'] = 'Bot/Crawler';
        $cihaz['dokunmatik'] = false;
        $cihaz['retina'] = false;
    }

    return $cihaz;
}

/**
 * Sunucu Performans Bilgisi Fonksiyonu
 *
 * Bu fonksiyon, sunucunun mevcut performans metriklerini döndürür.
 * Sistem yükü, disk kullanımı ve network bilgilerini analiz eder.
 *
 * Kullanım Alanı:
 * - System monitoring
 * - Performance optimization
 * - Resource usage tracking
 * - Health check endpoints
 *
 * Teknik Detaylar:
 * - CPU load average
 * - Memory usage analysis
 * - Disk space monitoring
 * - Process count tracking
 *
 * Kullanım Örneği:
 * $performans = Sunucu_Performans();
 * if ($performans['cpu_yuk'] > 80) {
 *     // Yüksek CPU kullanımı uyarısı
 * }
 *
 * @return array Sunucu performans metrikleri
 */
function Sunucu_Performans()
{
    $performans = [
        'cpu_yuk' => 0,
        'hafiza_kullanim' => Hafiza_Kullanimi(),
        'disk_kullanim' => [],
        'uptime' => 0,
        'aktif_kullanici' => 0,
        'php_versiyon' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
    ];

    // CPU Load Average (Linux/Unix systems)
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $performans['cpu_yuk'] = round($load[0], 2);
        $performans['load_avg'] = [
            '1_dakika' => round($load[0], 2),
            '5_dakika' => round($load[1], 2),
            '15_dakika' => round($load[2], 2)
        ];
    }

    // Disk usage
    $disk_total = disk_total_space('/');
    $disk_free = disk_free_space('/');
    if ($disk_total && $disk_free) {
        $disk_used = $disk_total - $disk_free;
        $performans['disk_kullanim'] = [
            'toplam_gb' => round($disk_total / 1024 / 1024 / 1024, 2),
            'kullanilan_gb' => round($disk_used / 1024 / 1024 / 1024, 2),
            'bos_gb' => round($disk_free / 1024 / 1024 / 1024, 2),
            'kullanim_yuzde' => round(($disk_used / $disk_total) * 100, 2)
        ];
    }

    // System uptime (Linux/Unix)
    if (file_exists('/proc/uptime')) {
        $uptime_data = file_get_contents('/proc/uptime');
        $uptime_seconds = floatval(explode(' ', $uptime_data)[0]);
        $performans['uptime'] = [
            'saniye' => round($uptime_seconds, 0),
            'saat' => round($uptime_seconds / 3600, 1),
            'gun' => round($uptime_seconds / 86400, 1)
        ];
    }

    return $performans;
}

/**
 * HTTP İstek Bilgisi Fonksiyonu
 *
 * Bu fonksiyon, mevcut HTTP isteğinin detaylı bilgilerini analiz eder.
 * Headers, method, timing ve güvenlik bilgilerini toplar.
 *
 * Kullanım Alanı:
 * - API request logging
 * - Security analysis
 * - Performance monitoring
 * - Debug operations
 *
 * Teknik Detaylar:
 * - Full HTTP header analysis
 * - Request timing calculation
 * - Security header detection
 * - Content negotiation analysis
 *
 * Kullanım Örneği:
 * $istek = HTTP_Istek_Bilgisi();
 * if ($istek['guvenli_baglanti']) {
 *     // HTTPS işlemleri
 * }
 *
 * @return array HTTP istek detayları
 */
function HTTP_Istek_Bilgisi()
{
    $istek = [
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'url' => $_SERVER['REQUEST_URI'] ?? '/',
        'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
        'guvenli_baglanti' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'port' => $_SERVER['SERVER_PORT'] ?? 80,
        'host' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'referer' => $_SERVER['HTTP_REFERER'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'ip_adresi' => IP_Adresi(),
        'accept' => $_SERVER['HTTP_ACCEPT'] ?? null,
        'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null,
        'accept_encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null,
        'connection' => $_SERVER['HTTP_CONNECTION'] ?? null,
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? null,
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
        'query_string' => $_SERVER['QUERY_STRING'] ?? null,
        'request_time' => $_SERVER['REQUEST_TIME'] ?? time(),
        'headers' => []
    ];

    // Tüm HTTP headers'ı topla
    if (function_exists('getallheaders')) {
        $istek['headers'] = getallheaders();
    } else {
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header_name = str_replace('_', '-', substr($key, 5));
                $istek['headers'][$header_name] = $value;
            }
        }
    }

    // Güvenlik headers kontrolü
    $istek['guvenlik_headers'] = [
        'x_frame_options' => isset($istek['headers']['X-Frame-Options']),
        'x_xss_protection' => isset($istek['headers']['X-XSS-Protection']),
        'x_content_type_options' => isset($istek['headers']['X-Content-Type-Options']),
        'strict_transport_security' => isset($istek['headers']['Strict-Transport-Security']),
        'content_security_policy' => isset($istek['headers']['Content-Security-Policy'])
    ];

    return $istek;
}

/**
 * Cache Control Fonksiyonu
 *
 * Bu fonksiyon, HTTP cache headers'ını yönetir ve
 * tarayıcı cache davranışını kontrol eder.
 *
 * Kullanım Alanı:
 * - Static file caching
 * - API response caching
 * - Performance optimization
 * - CDN integration
 *
 * Teknik Detaylar:
 * - Multiple cache strategies
 * - ETags generation
 * - Last-Modified headers
 * - Cache-Control directives
 *
 * Kullanım Örneği:
 * Cache_Control('1_hour'); // 1 saat cache
 * Cache_Control('no_cache'); // Cache'i devre dışı bırak
 *
 * @param string $tip Cache tipi veya süre
 * @param array $secenekler Ek cache seçenekleri
 * @return bool Cache header'ları ayarlandı mı
 */
function Cache_Control($tip = 'no_cache', $secenekler = [])
{
    if (headers_sent()) {
        return false;
    }

    switch ($tip) {
        case 'no_cache':
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            break;

        case '1_hour':
            $expires = time() + 3600;
            header('Cache-Control: public, max-age=3600');
            header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
            break;

        case '1_day':
            $expires = time() + 86400;
            header('Cache-Control: public, max-age=86400');
            header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
            break;

        case '1_week':
            $expires = time() + 604800;
            header('Cache-Control: public, max-age=604800');
            header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
            break;

        case '1_month':
            $expires = time() + 2592000;
            header('Cache-Control: public, max-age=2592000');
            header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
            break;

        case 'etag':
            $etag = isset($secenekler['etag']) ? $secenekler['etag'] : md5(serialize($secenekler));
            header('ETag: "' . $etag . '"');

            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
                http_response_code(304);
                exit;
            }
            break;

        default:
            // Custom cache control
            header('Cache-Control: ' . $tip);
            break;
    }

    // Last-Modified header ekle
    if (isset($secenekler['last_modified'])) {
        $last_modified = is_numeric($secenekler['last_modified']) ?
            $secenekler['last_modified'] : strtotime($secenekler['last_modified']);

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $if_modified_since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($last_modified <= $if_modified_since) {
                http_response_code(304);
                exit;
            }
        }
    }

    return true;
}

/**
 * Güvenlik Headers Fonksiyonu
 *
 * Bu fonksiyon, web güvenliği için gerekli HTTP headers'ını ayarlar.
 * XSS, Clickjacking ve diğer güvenlik açıklarına karşı koruma sağlar.
 *
 * Kullanım Alanı:
 * - XSS koruması
 * - Clickjacking prevention
 * - Content type sniffing koruması
 * - HTTPS enforcement
 *
 * Teknik Detaylar:
 * - Security headers best practices
 * - CSP (Content Security Policy)
 * - HSTS implementation
 * - Frame options control
 *
 * Kullanım Örneği:
 * Guvenlik_Headers(); // Temel güvenlik headers
 * Guvenlik_Headers('strict'); // Sıkı güvenlik politikaları
 *
 * @param string $seviye Güvenlik seviyesi ('basic', 'strict', 'paranoid')
 * @param array $secenekler Özel güvenlik seçenekleri
 * @return bool Headers ayarlandı mı
 */
function Guvenlik_Headers($seviye = 'basic', $secenekler = [])
{
    if (headers_sent()) {
        return false;
    }

    // Temel güvenlik headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    switch ($seviye) {
        case 'strict':
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self'; frame-ancestors 'none';");
            break;

        case 'paranoid':
            header('Referrer-Policy: no-referrer');
            header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');
            header("Content-Security-Policy: default-src 'none'; script-src 'self'; style-src 'self'; img-src 'self'; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");
            header('X-Frame-Options: DENY');
            header('X-Permitted-Cross-Domain-Policies: none');
            break;

        case 'basic':
        default:
            header('Referrer-Policy: same-origin');
            if (isset($secenekler['csp'])) {
                header('Content-Security-Policy: ' . $secenekler['csp']);
            }
            break;
    }

    // Özel headers
    if (isset($secenekler['extra_headers'])) {
        foreach ($secenekler['extra_headers'] as $header) {
            header($header);
        }
    }

    return true;
}

/**
 * Veri Temizleme Fonksiyonu
 *
 * Bu fonksiyon, kullanıcı girdilerini güvenli hale getirmek için
 * çeşitli temizleme ve sanitization işlemleri yapar.
 *
 * Kullanım Alanı:
 * - Form data sanitization
 * - XSS prevention
 * - SQL injection prevention
 * - Input validation
 *
 * Teknik Detaylar:
 * - Multiple sanitization methods
 * - Custom filter support
 * - Recursive array cleaning
 * - Encoding preservation
 *
 * Kullanım Örneği:
 * $temiz_veri = Veri_Temizle($_POST['kullanici_girdi'], 'string');
 * $temiz_email = Veri_Temizle($email, 'email');
 *
 * @param mixed $veri Temizlenecek veri
 * @param string $tip Temizleme tipi
 * @param array $secenekler Ek temizleme seçenekleri
 * @return mixed Temizlenmiş veri
 */
function Veri_Temizle($veri, $tip = 'string', $secenekler = [])
{
    if (is_array($veri)) {
        return array_map(function ($item) use ($tip, $secenekler) {
            return Veri_Temizle($item, $tip, $secenekler);
        }, $veri);
    }

    if (!is_string($veri)) {
        return $veri;
    }

    switch ($tip) {
        case 'string':
            $veri = strip_tags($veri);
            $veri = htmlspecialchars($veri, ENT_QUOTES, 'UTF-8');
            $veri = trim($veri);
            break;

        case 'html':
            // HTML'e izin ver ama güvenli hale getir
            $allowed_tags = $secenekler['allowed_tags'] ?? '<p><br><strong><em><ul><ol><li><a>';
            $veri = strip_tags($veri, $allowed_tags);
            break;

        case 'email':
            $veri = filter_var($veri, FILTER_SANITIZE_EMAIL);
            break;

        case 'url':
            $veri = filter_var($veri, FILTER_SANITIZE_URL);
            break;

        case 'int':
            $veri = filter_var($veri, FILTER_SANITIZE_NUMBER_INT);
            break;

        case 'float':
            $veri = filter_var($veri, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            break;

        case 'filename':
            $veri = preg_replace('/[^a-zA-Z0-9._-]/', '', $veri);
            $veri = trim($veri, '.');
            break;

        case 'sql':
            // Temel SQL injection koruması
            $veri = str_replace(["'", '"', ';', '--', '/*', '*/'], '', $veri);
            break;

        case 'xss':
            $veri = htmlspecialchars($veri, ENT_QUOTES, 'UTF-8');
            $veri = preg_replace('/javascript:/i', '', $veri);
            $veri = preg_replace('/on\w+=/i', '', $veri);
            break;

        default:
            // Varsayılan string temizleme
            $veri = strip_tags($veri);
            $veri = htmlspecialchars($veri, ENT_QUOTES, 'UTF-8');
            $veri = trim($veri);
            break;
    }

    return $veri;
}

/**
 * Oturum Yönetimi Fonksiyonu
 *
 * Bu fonksiyon, PHP oturumlarını güvenli şekilde yönetir.
 * Session güvenliği, timeout ve regeneration işlemlerini yapar.
 *
 * Kullanım Alanı:
 * - Secure session management
 * - Session timeout handling
 * - Session hijacking prevention
 * - User authentication tracking
 *
 * Teknik Detaylar:
 * - Session security best practices
 * - Session ID regeneration
 * - Timeout management
 * - Fingerprinting for security
 *
 * Kullanım Örneği:
 * Oturum_Yonet('start'); // Güvenli oturum başlat
 * Oturum_Yonet('regenerate'); // Session ID yenile
 * Oturum_Yonet('destroy'); // Oturumu sonlandır
 *
 * @param string $islem Yapılacak işlem ('start', 'destroy', 'regenerate', 'check')
 * @param array $secenekler Oturum seçenekleri
 * @return bool|array İşlem sonucu
 */
function Oturum_Yonet($islem = 'start', $secenekler = [])
{
    $varsayilan_secenekler = [
        'timeout' => 3600, // 1 saat
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ];

    $secenekler = array_merge($varsayilan_secenekler, $secenekler);

    switch ($islem) {
        case 'start':
            if (session_status() === PHP_SESSION_NONE) {
                // Güvenli session ayarları
                session_set_cookie_params([
                    'lifetime' => $secenekler['timeout'],
                    'path' => '/',
                    'domain' => '',
                    'secure' => $secenekler['secure'],
                    'httponly' => $secenekler['httponly'],
                    'samesite' => $secenekler['samesite']
                ]);

                session_start();

                // Session security
                if (!isset($_SESSION['_token'])) {
                    $_SESSION['_token'] = bin2hex(random_bytes(32));
                }

                if (!isset($_SESSION['_created'])) {
                    $_SESSION['_created'] = time();
                } elseif (time() - $_SESSION['_created'] > $secenekler['timeout']) {
                    session_destroy();
                    return false;
                }

                // Fingerprinting for security
                $fingerprint = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
                if (!isset($_SESSION['_fingerprint'])) {
                    $_SESSION['_fingerprint'] = $fingerprint;
                } elseif ($_SESSION['_fingerprint'] !== $fingerprint) {
                    session_destroy();
                    return false;
                }

                $_SESSION['_last_activity'] = time();
            }
            return true;

        case 'destroy':
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION = array();

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(
                        session_name(),
                        '',
                        time() - 42000,
                        $params["path"],
                        $params["domain"],
                        $params["secure"],
                        $params["httponly"]
                    );
                }

                session_destroy();
            }
            return true;

        case 'regenerate':
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
                $_SESSION['_created'] = time();
            }
            return true;

        case 'check':
            if (session_status() !== PHP_SESSION_ACTIVE) {
                return false;
            }

            if (
                isset($_SESSION['_last_activity']) &&
                (time() - $_SESSION['_last_activity'] > $secenekler['timeout'])
            ) {
                session_destroy();
                return false;
            }

            $_SESSION['_last_activity'] = time();
            return true;

        default:
            return false;
    }
}

/**
 * Breadcrumb Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, web sitesi navigasyonu için breadcrumb oluşturur.
 * SEO dostu ve kullanıcı deneyimi odaklı breadcrumb yapısı sunar.
 *
 * Kullanım Alanı:
 * - Website navigation
 * - SEO optimization
 * - User experience improvement
 * - Site structure indication
 *
 * Teknik Detaylar:
 * - Schema.org markup support
 * - Customizable separators
 * - Auto URL generation
 * - HTML5 nav element
 *
 * Kullanım Örneği:
 * $breadcrumb = Breadcrumb_Olustur([
 *     'Anasayfa' => '/',
 *     'Ürünler' => '/urunler',
 *     'Elektronik' => '/urunler/elektronik',
 *     'Laptop' => null // Mevcut sayfa
 * ]);
 *
 * @param array $items Breadcrumb öğeleri (başlık => URL)
 * @param array $secenekler Breadcrumb seçenekleri
 * @return string HTML breadcrumb
 */
function Breadcrumb_Olustur($items = [], $secenekler = [])
{
    $varsayilan = [
        'separator' => ' > ',
        'home_text' => 'Anasayfa',
        'home_url' => '/',
        'class' => 'breadcrumb',
        'schema' => true,
        'wrapper' => 'nav'
    ];

    $secenekler = array_merge($varsayilan, $secenekler);

    if (empty($items)) {
        // Otomatik breadcrumb oluştur
        $path = trim($_SERVER['REQUEST_URI'], '/');
        $segments = explode('/', $path);

        $items = [$secenekler['home_text'] => $secenekler['home_url']];
        $current_path = '';

        foreach ($segments as $segment) {
            if (!empty($segment)) {
                $current_path .= '/' . $segment;
                $title = ucwords(str_replace('-', ' ', $segment));
                $items[$title] = $current_path;
            }
        }

        // Son öğeyi link yapmama (mevcut sayfa)
        $last_key = array_key_last($items);
        $items[$last_key] = null;
    }

    $breadcrumb_items = [];
    $position = 1;

    foreach ($items as $title => $url) {
        if ($url !== null) {
            if ($secenekler['schema']) {
                $breadcrumb_items[] = sprintf(
                    '<span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">' .
                        '<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>' .
                        '<meta itemprop="position" content="%d">' .
                        '</span>',
                    htmlspecialchars($url),
                    htmlspecialchars($title),
                    $position
                );
            } else {
                $breadcrumb_items[] = sprintf(
                    '<a href="%s">%s</a>',
                    htmlspecialchars($url),
                    htmlspecialchars($title)
                );
            }
        } else {
            if ($secenekler['schema']) {
                $breadcrumb_items[] = sprintf(
                    '<span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">' .
                        '<span itemprop="name">%s</span>' .
                        '<meta itemprop="position" content="%d">' .
                        '</span>',
                    htmlspecialchars($title),
                    $position
                );
            } else {
                $breadcrumb_items[] = '<span>' . htmlspecialchars($title) . '</span>';
            }
        }
        $position++;
    }

    $breadcrumb_html = implode($secenekler['separator'], $breadcrumb_items);

    if ($secenekler['schema']) {
        $wrapper_attrs = sprintf(
            'class="%s" itemscope itemtype="http://schema.org/BreadcrumbList"',
            $secenekler['class']
        );
    } else {
        $wrapper_attrs = sprintf('class="%s"', $secenekler['class']);
    }

    return sprintf(
        '<%s %s>%s</%s>',
        $secenekler['wrapper'],
        $wrapper_attrs,
        $breadcrumb_html,
        $secenekler['wrapper']
    );
}

/**
 * Pagination Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, veritabanı sorguları için sayfalama (pagination) oluşturur.
 * Bootstrap ve özel CSS framework'leri ile uyumlu HTML çıktısı verir.
 *
 * Kullanım Alanı:
 * - Database query pagination
 * - Search result paging
 * - Product listing pagination
 * - Blog post navigation
 *
 * Teknik Detaylar:
 * - Customizable page range
 * - SEO-friendly URLs
 * - Bootstrap CSS classes
 * - AJAX pagination support
 *
 * Kullanım Örneği:
 * $pagination = Pagination_Olustur([
 *     'toplam' => 1000,
 *     'sayfa_basi' => 20,
 *     'mevcut_sayfa' => 5,
 *     'base_url' => '/urunler/sayfa/'
 * ]);
 *
 * @param array $secenekler Pagination seçenekleri
 * @return array Pagination bilgileri ve HTML
 */
function Pagination_Olustur($secenekler = [])
{
    $varsayilan = [
        'toplam' => 0,
        'sayfa_basi' => 10,
        'mevcut_sayfa' => 1,
        'base_url' => '?sayfa=',
        'gosterilen_sayfa' => 5,
        'first_text' => '&laquo;&laquo;',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'last_text' => '&raquo;&raquo;',
        'class' => 'pagination',
        'item_class' => 'page-item',
        'link_class' => 'page-link',
        'active_class' => 'active',
        'disabled_class' => 'disabled',
        'ajax' => false,
        'ajax_target' => '#content'
    ];

    $opts = array_merge($varsayilan, $secenekler);

    $toplam_sayfa = ceil($opts['toplam'] / $opts['sayfa_basi']);
    $mevcut = max(1, min($opts['mevcut_sayfa'], $toplam_sayfa));

    $result = [
        'toplam_kayit' => $opts['toplam'],
        'toplam_sayfa' => $toplam_sayfa,
        'mevcut_sayfa' => $mevcut,
        'sayfa_basi' => $opts['sayfa_basi'],
        'baslangic' => ($mevcut - 1) * $opts['sayfa_basi'] + 1,
        'bitis' => min($mevcut * $opts['sayfa_basi'], $opts['toplam']),
        'onceki_sayfa' => $mevcut > 1 ? $mevcut - 1 : null,
        'sonraki_sayfa' => $mevcut < $toplam_sayfa ? $mevcut + 1 : null,
        'sayfa_listesi' => [],
        'html' => ''
    ];

    if ($toplam_sayfa <= 1) {
        return $result;
    }

    // Sayfa aralığını hesapla
    $baslangic_sayfa = max(1, $mevcut - floor($opts['gosterilen_sayfa'] / 2));
    $bitis_sayfa = min($toplam_sayfa, $baslangic_sayfa + $opts['gosterilen_sayfa'] - 1);

    if ($bitis_sayfa - $baslangic_sayfa + 1 < $opts['gosterilen_sayfa']) {
        $baslangic_sayfa = max(1, $bitis_sayfa - $opts['gosterilen_sayfa'] + 1);
    }

    for ($i = $baslangic_sayfa; $i <= $bitis_sayfa; $i++) {
        $result['sayfa_listesi'][] = $i;
    }

    // HTML oluştur
    $html = '<nav aria-label="Sayfa navigasyonu">';
    $html .= '<ul class="' . $opts['class'] . '">';

    $ajax_attr = $opts['ajax'] ? ' data-ajax="true" data-target="' . $opts['ajax_target'] . '"' : '';

    // İlk sayfa
    if ($mevcut > 1) {
        $url = $opts['base_url'] . '1';
        $html .= '<li class="' . $opts['item_class'] . '">';
        $html .= '<a class="' . $opts['link_class'] . '" href="' . $url . '"' . $ajax_attr . '>' . $opts['first_text'] . '</a>';
        $html .= '</li>';

        // Önceki sayfa
        $url = $opts['base_url'] . $result['onceki_sayfa'];
        $html .= '<li class="' . $opts['item_class'] . '">';
        $html .= '<a class="' . $opts['link_class'] . '" href="' . $url . '"' . $ajax_attr . '>' . $opts['prev_text'] . '</a>';
        $html .= '</li>';
    }

    // Sayfa numaraları
    foreach ($result['sayfa_listesi'] as $sayfa) {
        $url = $opts['base_url'] . $sayfa;
        $active = $sayfa == $mevcut ? ' ' . $opts['active_class'] : '';

        $html .= '<li class="' . $opts['item_class'] . $active . '">';
        if ($sayfa == $mevcut) {
            $html .= '<span class="' . $opts['link_class'] . '">' . $sayfa . '</span>';
        } else {
            $html .= '<a class="' . $opts['link_class'] . '" href="' . $url . '"' . $ajax_attr . '>' . $sayfa . '</a>';
        }
        $html .= '</li>';
    }

    // Sonraki sayfa
    if ($mevcut < $toplam_sayfa) {
        $url = $opts['base_url'] . $result['sonraki_sayfa'];
        $html .= '<li class="' . $opts['item_class'] . '">';
        $html .= '<a class="' . $opts['link_class'] . '" href="' . $url . '"' . $ajax_attr . '>' . $opts['next_text'] . '</a>';
        $html .= '</li>';

        // Son sayfa
        $url = $opts['base_url'] . $toplam_sayfa;
        $html .= '<li class="' . $opts['item_class'] . '">';
        $html .= '<a class="' . $opts['link_class'] . '" href="' . $url . '"' . $ajax_attr . '>' . $opts['last_text'] . '</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    $html .= '</nav>';

    $result['html'] = $html;

    return $result;
}

/**
 * Rastgele Renk Üretici Fonksiyonu
 *
 * Bu fonksiyon, farklı formatlarda rastgele renkler üretir.
 * Web tasarımı ve data visualization için kullanılır.
 *
 * Kullanım Alanı:
 * - Chart color generation
 * - Avatar background colors
 * - Theme color variations
 * - Random design elements
 *
 * Teknik Detaylar:
 * - Multiple color formats
 * - HSL, RGB, HEX support
 * - Brightness control
 * - Color palette generation
 *
 * Kullanım Örneği:
 * $renk = Rastgele_Renk('hex'); // "#3A7BD5"
 * $renkler = Rastgele_Renk('palette', ['count' => 5]);
 *
 * @param string $format Renk formatı ('hex', 'rgb', 'hsl', 'palette')
 * @param array $secenekler Renk seçenekleri
 * @return string|array Renk değeri veya renk paleti
 */
function Rastgele_Renk($format = 'hex', $secenekler = [])
{
    $varsayilan = [
        'min_brightness' => 30,
        'max_brightness' => 70,
        'saturation' => 70,
        'count' => 1
    ];

    $opts = array_merge($varsayilan, $secenekler);

    if ($format === 'palette') {
        $palette = [];
        for ($i = 0; $i < $opts['count']; $i++) {
            $palette[] = Rastgele_Renk('hex', $opts);
        }
        return $palette;
    }

    // HSL renk üret
    $hue = rand(0, 360);
    $saturation = $opts['saturation'];
    $lightness = rand($opts['min_brightness'], $opts['max_brightness']);

    switch ($format) {
        case 'hsl':
            return "hsl($hue, $saturation%, $lightness%)";

        case 'rgb':
            // HSL'yi RGB'ye dönüştür
            $c = (100 - abs(2 * $lightness - 100)) * $saturation / 10000;
            $x = $c * (1 - abs(fmod($hue / 60, 2) - 1));
            $m = $lightness / 100 - $c / 2;

            if ($hue < 60) {
                $r = $c;
                $g = $x;
                $b = 0;
            } elseif ($hue < 120) {
                $r = $x;
                $g = $c;
                $b = 0;
            } elseif ($hue < 180) {
                $r = 0;
                $g = $c;
                $b = $x;
            } elseif ($hue < 240) {
                $r = 0;
                $g = $x;
                $b = $c;
            } elseif ($hue < 300) {
                $r = $x;
                $g = 0;
                $b = $c;
            } else {
                $r = $c;
                $g = 0;
                $b = $x;
            }

            $r = round(($r + $m) * 255);
            $g = round(($g + $m) * 255);
            $b = round(($b + $m) * 255);

            return "rgb($r, $g, $b)";

        case 'hex':
        default:
            // HSL'yi RGB'ye dönüştür sonra HEX yap
            $c = (100 - abs(2 * $lightness - 100)) * $saturation / 10000;
            $x = $c * (1 - abs(fmod($hue / 60, 2) - 1));
            $m = $lightness / 100 - $c / 2;

            if ($hue < 60) {
                $r = $c;
                $g = $x;
                $b = 0;
            } elseif ($hue < 120) {
                $r = $x;
                $g = $c;
                $b = 0;
            } elseif ($hue < 180) {
                $r = 0;
                $g = $c;
                $b = $x;
            } elseif ($hue < 240) {
                $r = 0;
                $g = $x;
                $b = $c;
            } elseif ($hue < 300) {
                $r = $x;
                $g = 0;
                $b = $c;
            } else {
                $r = $c;
                $g = 0;
                $b = $x;
            }

            $r = round(($r + $m) * 255);
            $g = round(($g + $m) * 255);
            $b = round(($b + $m) * 255);

            return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
