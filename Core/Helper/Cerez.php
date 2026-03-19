<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Çerez fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * HTTP Çerez Belirleme Fonksiyonu
 *F
 * Bu fonksiyon, tarayıcıda güvenli çerez oluşturur. Modern gümenlik önlemleri
 * ve ayarları destekler. Header gönderilmeden önce çağrılmalıdır.
 *
 * Kullanım Alanı:
 * - Kullanıcı oturum yönetiminde
 * - Tercihler ve ayarların saklanmasında
 * - Almayet ve pazarlama çerezlerinde
 * - Geçici veri depolamada
 *
 * Teknik Detaylar:
 * - Headers sent kontrolü yapar
 * - Modern cookie attributes destekler
 * - SameSite policy desteği
 * - Error logging ve warning sistemi
 *
 * Kullanım Örneği:
 * Cerez_Belirle('kullanici_adi', 'admin', 7, true);
 * Cerez_Belirle('tema', 'dark', 30);
 * Cerez_Belirle('dil', 'tr');
 *
 * @param string $adi Çerez adı
 * @param string $value Çerez değeri
 * @param int $gun Geçerlilik süresi (gün cinsinden, varsayılan: 1)
 * @param bool $secure HTTPS zorunlu mu (varsayılan: false)
 * @param bool $httponly JavaScript erişim engeli (varsayılan: true)
 * @param string $samesite SameSite policy (varsayılan: 'Lax')
 * @return void
 */
function Cerez_Belirle($adi = '', $value = '', $gun = 1, $secure = false, $httponly = true, $samesite = 'Lax')
{
    if (!headers_sent()) {
        $options = [
            'expires' => time() + (60 * 60 * 24 * $gun),
            'path' => '/',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ];
        setcookie($adi, $value, $options);
    } else {
        $file = '';
        $line = 0;
        headers_sent($file, $line);
        trigger_error("Headers already sent in $file on line $line. Cannot set cookie: $adi", E_USER_WARNING);
    }
}

/**
 * HTTP Response'dan Çerez Çıkarma Fonksiyonu
 *
 * Bu fonksiyon, HTTP response content'inden Set-Cookie header'larını parse eder
 * ve cookie string formatında döndürür. cURL işlemlerinde kullanılır.
 *
 * Kullanım Alanı:
 * - cURL ile HTTP isteklerinde
 * - API client'larında session management
 * - Web scraping işlemlerinde
 * - Proxy ve gateway uygulamalarında
 *
 * Teknik Detaylar:
 * - Regex ile Set-Cookie header parsing
 * - Multiple cookie desteği
 * - Cookie string formatında birleştirme
 * - Semicolon separatör kullanımı
 *
 * Kullanım Örneği:
 * $response = "Set-Cookie: session=abc123; path=/";
 * $cookies = Cerez_Oku($response); // "session=abc123"
 *
 * @param string $content HTTP response content
 * @return string Cookie string formatında çerezler
 */
function Cerez_Oku($content)
{
    preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]+);/i', $content, $matches, PREG_SET_ORDER);
    $a = array();
    foreach ($matches as $m) {
        $a[trim($m[1])] = trim($m[2]);
    }
    $cookies = '';
    foreach ($a as $b => $c) {
        $cookies .= "{$b}={$c}; ";
    }
    return rtrim($cookies, '; ');
}

/**
 * Tarayıcıdan Çerez Okuma Fonksiyonu
 *
 * Bu fonksiyon, tarayıcı tarafından gönderilen çerezi okur ve değerini döndürür.
 * $_COOKIE superglobal'ini güvenli şekilde wrap eder.
 *
 * Kullanım Alanı:
 * - Kullanıcı oturum bilgilerini okuma
 * - Tercih ve ayarları alma
 * - Authentication token'larını okuma
 * - Geçici veri erişiminde
 *
 * Teknik Detaylar:
 * - $_COOKIE array kontrolü
 * - Null safety sağlar
 * - Direct superglobal erişimini engeller
 * - Type-safe return
 *
 * Kullanım Örneği:
 * $kullanici = Cerez_Tarayıcıdan_Oku('kullanici_adi');
 * $tema = Cerez_Tarayıcıdan_Oku('tema') ?? 'light';
 *
 * @param string $adi Okunacak çerez adı
 * @return string|null Çerez değeri veya null
 */
function Cerez_Tarayıcıdan_Oku($adi)
{
    return isset($_COOKIE[$adi]) ? $_COOKIE[$adi] : null;
}

/**
 * Çerez Silme Fonksiyonu
 *
 * Bu fonksiyon, tarayıcıdaki belirtilen çerezi siler. Expires zamanını
 * geçmişe ayarlayarak tarayıcının çerezi kaldırmasını sağlar.
 *
 * Kullanım Alanı:
 * - Kullanıcı çıkış işlemlerinde
 * - Session sonlandırma işlemlerinde
 * - Ayar sıfırlama işlemlerinde
 * - Güvenlik temizleme işlemlerinde
 *
 * Teknik Detaylar:
 * - Expires'i geçmişe (time() - 3600) ayarlar
 * - Aynı path ve domain ayarlarını kullanır
 * - HttpOnly ve SameSite ayarları korur
 * - Browser-side deletion tetikler
 *
 * Kullanım Örneği:
 * Cerez_Sil('kullanici_adi');
 * Cerez_Sil('session_token');
 *
 * @param string $adi Silinecek çerez adı
 * @return void
 */
function Cerez_Sil($adi)
{
    setcookie($adi, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Çerez Varlık Kontrol Fonksiyonu
 *
 * Bu fonksiyon, belirtilen ad ile bir çerezin tarayıcıda mevcut
 * olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Kullanıcı giriş durumu kontroldün
 * - Ayar varlığı kontroldünde
 * - Conditional logic işlemlerinde
 * - Validation işlemlerinde
 *
 * Teknik Detaylar:
 * - $_COOKIE array key kontrolü
 * - Boolean return type
 * - Null-safe çalışma
 * - Fast existence check
 *
 * Kullanım Örneği:
 * if (Cerez_Var_Mi('kullanici_adi')) {
 *     // Kullanıcı giriş yapmış
 * }
 *
 * @param string $adi Kontrol edilecek çerez adı
 * @return bool Çerez varsa true, yoksa false
 */
function Cerez_Var_Mi($adi)
{
    return isset($_COOKIE[$adi]);
}

/**
 * Tüm Çerezleri Listeleme Fonksiyonu
 *
 * Bu fonksiyon, tarayıcı tarafından gönderilen tüm çerezleri
 * array formatında döndürür.
 *
 * Kullanım Alanı:
 * - Debug işlemlerinde çerez analizi
 * - Admin panellerinde çerez yönetimi
 * - Sistem izleme ve logging'de
 * - Cookie audit işlemlerinde
 *
 * Teknik Detaylar:
 * - $_COOKIE superglobal'i direkt döndürür
 * - Reference değil copy döndürür
 * - Array formatında key-value pairs
 * - Read-only operation
 *
 * Kullanım Örneği:
 * $cerezler = Cerez_Hepsini_Listele();
 * foreach ($cerezler as $ad => $deger) {
 *     echo "$ad: $deger";
 * }
 *
 * @return array Tüm çerez adı ve değerleri array'i
 */
function Cerez_Hepsini_Listele()
{
    return $_COOKIE;
}

/**
 * Şifreli Çerez Belirleme Fonksiyonu
 *
 * Bu fonksiyon, çerez değerini AES-256-CBC ile şifreleyerek güvenli bir şekilde
 * saklar. Hassas bilgiler için çok daha güvenli bir çözümdür.
 *
 * Kullanım Alanı:
 * - Hassas kullanıcı bilgilerinin saklanmasında
 * - Authentication token'larında
 * - Kişisel veri depolamada
 * - Güvenlik-kritik ayarlarda
 *
 * Teknik Detaylar:
 * - AES-256-CBC şifreleme kullanır
 * - Random IV oluşturur
 * - Base64 encoding uygular
 * - OpenSSL extension gerektirir
 *
 * Kullanım Örneği:
 * Cerez_Sifreli_Belirle('user_data', 'hassas_bilgi', 7, 'gizli_anahtar');
 * Cerez_Sifreli_Belirle('token', 'abc123', 1);
 *
 * @param string $adi Çerez adı
 * @param string $value Şifrelenecek değer
 * @param int $gun Geçerlilik süresi (gün, varsayılan: 1)
 * @param string $anahtar Şifreleme anahtarı (boşsa varsayılan kullanılır)
 * @param bool $secure HTTPS zorunlu mu (varsayılan: false)
 * @param bool $httponly JavaScript erişim engeli (varsayılan: true)
 * @param string $samesite SameSite policy (varsayılan: 'Lax')
 * @return bool Başarılı ise true, hata durumunda false
 */
function Cerez_Sifreli_Belirle($adi = '', $value = '', $gun = 1, $anahtar = '', $secure = false, $httponly = true, $samesite = 'Lax')
{
    if (empty($adi) || $value === '') {
        return false;
    }

    if (!extension_loaded('openssl')) {
        trigger_error("OpenSSL eklentisi bulunamadı. Şifreli çerez oluşturulamaz.", E_USER_WARNING);
        return false;
    }

    if (empty($anahtar)) {
        $anahtar = 'varsayilan_anahtar_2025';
    }

    $iv = openssl_random_pseudo_bytes(16, $iv_strong);
    if ($iv === false || !$iv_strong) {
        trigger_error("Güvenli IV oluşturulamadı.", E_USER_WARNING);
        return false;
    }

    $encrypted = openssl_encrypt($value, 'AES-256-CBC', $anahtar, 0, $iv);
    if ($encrypted === false) {
        trigger_error("Şifreleme işlemi başarısız: " . openssl_error_string(), E_USER_WARNING);
        return false;
    }

    $data = base64_encode($iv . $encrypted);
    if ($data === false) {
        trigger_error("Base64 encoding başarısız.", E_USER_WARNING);
        return false;
    }

    Cerez_Belirle($adi, $data, $gun, $secure, $httponly, $samesite);
    return true;
}

/**
 * Şifreli Çerez Okuma Fonksiyonu
 *
 * Bu fonksiyon, Cerez_Sifreli_Belirle ile oluşturulan şifreli çerezi
 * çözer ve orijinal değeri döndürür.
 *
 * Kullanım Alanı:
 * - Şifreli kullanıcı bilgilerini okuma
 * - Güvenli token değerlerin erişimi
 * - Hassas ayarların alınmasında
 * - Encrypted session data'nın çözülmesinde
 *
 * Teknik Detaylar:
 * - AES-256-CBC şifre çözme kullanır
 * - Base64 decode yapar
 * - IV extraction ve validation
 * - Error handling ve logging
 *
 * Kullanım Örneği:
 * $veri = Cerez_Sifreli_Oku('user_data', 'gizli_anahtar');
 * $token = Cerez_Sifreli_Oku('token');
 *
 * @param string $adi Okunacak çerez adı
 * @param string $anahtar Şifre çözme anahtarı (boşsa varsayılan kullanılır)
 * @return string|null Çözülen değer veya hata durumunda null
 */
function Cerez_Sifreli_Oku($adi, $anahtar = '')
{
    if (!extension_loaded('openssl')) {
        trigger_error("OpenSSL eklentisi bulunamadı. Şifreli çerez okunamaz.", E_USER_WARNING);
        return null;
    }
    $data = Cerez_Tarayıcıdan_Oku($adi);
    if (!$data) {
        return null;
    }
    if (empty($anahtar)) {
        $anahtar = 'varsayilan_anahtar_2025';
    }
    $decoded_data = base64_decode($data, true);
    if ($decoded_data === false) {
        trigger_error("Base64 decode başarısız. Çerez bozuk olabilir.", E_USER_WARNING);
        return null;
    }
    if (strlen($decoded_data) < 16) {
        trigger_error("Çerez verisi çok kısa. Bozuk veri.", E_USER_WARNING);
        return null;
    }

    $iv = substr($decoded_data, 0, 16);
    $encrypted = substr($decoded_data, 16);
    $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $anahtar, 0, $iv);
    if ($decrypted === false) {
        trigger_error("Şifre çözme başarısız: " . openssl_error_string(), E_USER_WARNING);
        return null;
    }

    return $decrypted;
}

/**
 * JSON Çerez Belirleme Fonksiyonu
 *
 * Bu fonksiyon, PHP array'ini JSON formatına dönüştürerek çerez olarak saklar.
 * Kompleks veri yapılarını çerez olarak saklamak için kullanılır.
 *
 * Kullanım Alanı:
 * - Kullanıcı tercihlerini saklamada
 * - Almayet sepeti verilerinde
 * - Form state bilgilerinde
 * - Multi-value çerezlerde
 *
 * Teknik Detaylar:
 * - JSON_UNESCAPED_UNICODE flag kullanır
 * - Array to JSON conversion
 * - UTF-8 karakter desteği
 * - Error handling sağlar
 *
 * Kullanım Örneği:
 * $tercihler = ['tema' => 'dark', 'dil' => 'tr'];
 * Cerez_Json_Belirle('kullanici_tercihleri', $tercihler, 30);
 *
 * @param string $adi Çerez adı
 * @param array $dizi JSON'a dönüştürülecek array
 * @param int $gun Geçerlilik süresi (gün, varsayılan: 1)
 * @param bool $secure HTTPS zorunlu mu (varsayılan: false)
 * @param bool $httponly JavaScript erişim engeli (varsayılan: true)
 * @param string $samesite SameSite policy (varsayılan: 'Lax')
 * @return bool Başarılı ise true, hata durumunda false
 */
function Cerez_Json_Belirle($adi = '', $dizi = [], $gun = 1, $secure = false, $httponly = true, $samesite = 'Lax')
{
    $json_data = json_encode($dizi, JSON_UNESCAPED_UNICODE);
    if ($json_data === false) {
        return false;
    }

    Cerez_Belirle($adi, $json_data, $gun, $secure, $httponly, $samesite);
    return true;
}

/**
 * JSON Çerez Okuma Fonksiyonu
 *
 * Bu fonksiyon, Cerez_Json_Belirle ile oluşturulan JSON çerezi okur
 * ve PHP array/object formatına dönüştürür.
 *
 * Kullanım Alanı:
 * - Kullanıcı tercihlerini okumada
 * - Kompleks veri yapılarının erişiminde
 * - Multi-value çerez verilerinde
 * - State restoration işlemlerinde
 *
 * Teknik Detaylar:
 * - JSON decode operation
 * - Associative array / object seçimi
 * - UTF-8 karakter desteği
 * - Null return on error
 *
 * Kullanım Örneği:
 * $tercihler = Cerez_Json_Oku('kullanici_tercihleri');
 * $tema = $tercihler['tema'] ?? 'light';
 *
 * @param string $adi Okunacak çerez adı
 * @param bool $assoc Associative array döndürsün mü (varsayılan: true)
 * @return array|object|null Çözülen veri veya hata durumunda null
 */
function Cerez_Json_Oku($adi, $assoc = true)
{
    $data = Cerez_Tarayıcıdan_Oku($adi);
    if (!$data) {
        return null;
    }

    return json_decode($data, $assoc);
}

/**
 * Çerez Boyut Kontrol ve Analiz Fonksiyonu
 *
 * Bu fonksiyon, çerez değerinin boyutunu kontrol eder ve 4KB sınırına
 * göre detaylı analiz bilgisi döndürür.
 *
 * Kullanım Alanı:
 * - Çerez boyutu validasyonunda
 * - Storage limit kontrolünde
 * - Performance optimization'da
 * - Debug işlemlerinde boyut analizi
 *
 * Teknik Detaylar:
 * - 4KB (4096 byte) sınır kontrolü
 * - Byte-level size calculation
 * - Percentage usage hesabı
 * - Remaining space calculation
 *
 * Kullanım Örneği:
 * $analiz = Cerez_Boyut_Kontrol('uzun_cerez_verisi');
 * if (!$analiz['gecerli']) {
 *     echo "Çerez çok büyük: " . $analiz['boyut'];
 * }
 *
 * @param string $value Kontrol edilecek çerez değeri
 * @return array Boyut analiz bilgileri array'i
 */
function Cerez_Boyut_Kontrol($value)
{
    $boyut = strlen($value);
    $limit = 4096; // 4KB
    $gecerli = $boyut <= $limit;

    return [
        'boyut' => $boyut,
        'limit' => $limit,
        'gecerli' => $gecerli,
        'kalan' => $limit - $boyut,
        'yuzde' => round(($boyut / $limit) * 100, 2)
    ];
}
/**
 * Toplu Çerez Belirleme Fonksiyonu
 *
 * Bu fonksiyon, birden fazla çerezi aynı anda belirler ve işlem sonuçlarını
 * detaylı rapor halinde döndürür.
 *
 * Kullanım Alanı:
 * - Çoklu ayar kayıtlarında
 * - Bulk user preference updates'de
 * - Migration işlemlerinde
 * - Setup wizard'larda
 *
 * Teknik Detaylar:
 * - Batch processing yapabilir
 * - Success/failure tracking
 * - Exception handling per item
 * - Detailed result reporting
 *
 * Kullanım Örneği:
 * $cerezler = ['tema' => 'dark', 'dil' => 'tr', 'tz' => 'Europe/Istanbul'];
 * $sonuc = Cerez_Toplu_Belirle($cerezler, 30);
 * echo count($sonuc['basarili']) . " çerez başarıyla kaydedildi";
 *
 * @param array $cerezler Çerez adı ve değerleri array'i
 * @param int $gun Geçerlilik süresi (gün, varsayılan: 1)
 * @param bool $secure HTTPS zorunlu mu (varsayılan: false)
 * @param bool $httponly JavaScript erişim engeli (varsayılan: true)
 * @param string $samesite SameSite policy (varsayılan: 'Lax')
 * @return array Başarılı ve başarısız işlemler raporu
 */
function Cerez_Toplu_Belirle($cerezler = [], $gun = 1, $secure = false, $httponly = true, $samesite = 'Lax')
{
    $sonuclar = [
        'basarili' => [],
        'basarisiz' => []
    ];

    foreach ($cerezler as $ad => $deger) {
        try {
            Cerez_Belirle($ad, $deger, $gun, $secure, $httponly, $samesite);
            $sonuclar['basarili'][] = $ad;
        } catch (Exception $e) {
            $sonuclar['basarisiz'][$ad] = $e->getMessage();
        }
    }

    return $sonuclar;
}

/**
 * Toplu Çerez Silme Fonksiyonu
 *
 * Bu fonksiyon, birden fazla çerezi aynı anda siler ve işlem sonuçlarını
 * detaylı rapor halinde döndürür.
 *
 * Kullanım Alanı:
 * - Kullanıcı çıkış işlemlerinde
 * - Privacy-related cleanup'larda
 * - Session termination'da
 * - Bulk data deletion'da
 *
 * Teknik Detaylar:
 * - Batch deletion processing
 * - Success/failure tracking
 * - Exception handling per item
 * - Detailed result reporting
 *
 * Kullanım Örneği:
 * $silinecekler = ['session', 'user_token', 'temp_data'];
 * $sonuc = Cerez_Toplu_Sil($silinecekler);
 * echo count($sonuc['basarili']) . " çerez silindi";
 *
 * @param array $cerez_adlari Silinecek çerez adları array'i
 * @return array Başarılı ve başarısız işlemler raporu
 */
function Cerez_Toplu_Sil($cerez_adlari = [])
{
    $sonuclar = [
        'basarili' => [],
        'basarisiz' => []
    ];

    foreach ($cerez_adlari as $ad) {
        try {
            Cerez_Sil($ad);
            $sonuclar['basarili'][] = $ad;
        } catch (Exception $e) {
            $sonuclar['basarisiz'][$ad] = $e->getMessage();
        }
    }

    return $sonuclar;
}

/**
 * Çerez Kalan Süre Bilgi Fonksiyonu
 *
 * Bu fonksiyon, çerezin kalan süresi hakkında bilgi döndürür.
 * Teknik sınırlamalar nedeniyle tam süre hesaplaması yapamaz.
 *
 * Kullanım Alanı:
 * - Debug işlemlerinde
 * - Çerez audit sistemlerinde
 * - Development ortamında monitoring
 * - General information purposes'da
 *
 * Teknik Detaylar:
 * - Browser limitation nedeniyle tam süre bilinmez
 * - Existence check yapar
 * - Informational message döndürür
 * - Client-side expiration management
 *
 * Kullanım Örneği:
 * $sure_bilgi = Cerez_Sure_Kaldi('session_token');
 * echo $sure_bilgi; // Açıklama mesajı
 *
 * @param string $adi Kontrol edilecek çerez adı
 * @return string|null Bilgi mesajı veya null
 */
function Cerez_Sure_Kaldi($adi)
{
    if (!Cerez_Var_Mi($adi)) {
        return null;
    }

    // Not: Tarayıcı çerezlerinin gerçek bitiş süresini PHP'den tam olarak bilemeyiz
    // Bu fonksiyon genel bir açıklama döndürür
    return "Çerez mevcut ancak kalan süre tarayıcı tarafından yönetilmektedir.";
}

/**
 * Çerez Güvenlik Kontrol ve Hash Validasyon Fonksiyonu
 *
 * Bu fonksiyon, çerez değerinin hash'ini kontrol ederek tamper detection
 * yapar. Çerezin değiştirilip değiştirilmediğini belirler.
 *
 * Kullanım Alanı:
 * - Çerez bütünlüğü kontrolünde
 * - Security validation'da
 * - Anti-tampering sistemlerinde
 * - Integrity verification'da
 *
 * Teknik Detaylar:
 * - Configurable hash algorithm
 * - Hash_equals timing-safe comparison
 * - Multiple algorithm support
 * - Tamper detection capability
 *
 * Kullanım Örneği:
 * $beklenen = hash('sha256', 'orijinal_deger');
 * $guven = Cerez_Guvenlik_Kontrol('test_cerez', $beklenen);
 *
 * @param string $adi Kontrol edilecek çerez adı
 * @param string $beklenen_hash Beklenen hash değeri
 * @param string $algoritma Hash algoritması (varsayılan: 'sha256')
 * @return bool Hash eşleşiyorsa true, değilse false
 */
function Cerez_Guvenlik_Kontrol($adi, $beklenen_hash, $algoritma = 'sha256')
{
    $deger = Cerez_Tarayıcıdan_Oku($adi);
    if (!$deger) {
        return false;
    }

    $mevcut_hash = hash($algoritma, $deger);
    return hash_equals($beklenen_hash, $mevcut_hash);
}

/**
 * Domain-Spesifik Çerez Belirleme Fonksiyonu
 *
 * Bu fonksiyon, belirli domain ve path için çerez belirler. Cross-domain
 * çerez yönetimi ve subdomain paylanma işlemlerinde kullanılır.
 *
 * Kullanım Alanı:
 * - Subdomain çerez paylaşımında
 * - Multi-domain uygulamalarda
 * - Path-specific cookie management'da
 * - Cross-subdomain authentication'da
 *
 * Teknik Detaylar:
 * - Custom domain ve path ayarı
 * - Subdomain wildcard support
 * - Path-based cookie scope
 * - Full cookie attribute control
 *
 * Kullanım Örneği:
 * Cerez_Domain_Belirle('shared_data', 'value', '.example.com', '/', 7);
 * Cerez_Domain_Belirle('admin_token', 'token', 'admin.site.com', '/panel/');
 *
 * @param string $adi Çerez adı
 * @param string $value Çerez değeri
 * @param string $domain Hedef domain (boş = current domain)
 * @param string $path Hedef path (varsayılan: '/')
 * @param int $gun Geçerlilik süresi (gün, varsayılan: 1)
 * @param bool $secure HTTPS zorunlu mu (varsayılan: false)
 * @param bool $httponly JavaScript erişim engeli (varsayılan: true)
 * @param string $samesite SameSite policy (varsayılan: 'Lax')
 * @return void
 */
function Cerez_Domain_Belirle($adi = '', $value = '', $domain = '', $path = '/', $gun = 1, $secure = false, $httponly = true, $samesite = 'Lax')
{
    if (!headers_sent()) {
        $options = [
            'expires' => time() + (60 * 60 * 24 * $gun),
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ];
        setcookie($adi, $value, $options);
    }
}

/**
 * Tüm Çerezleri Temizleme Fonksiyonu
 *
 * Bu fonksiyon, belirtilen hariç tutulacaklar dışındaki tüm
 * çerezleri siler ve silinen çerez listesini döndürür.
 *
 * Kullanım Alanı:
 * - Logout işlemlerinde tam temizlik
 * - Privacy compliance işlemlerinde
 * - Session cleanup operasyonlarında
 * - Development ortamında reset işlemlerinde
 *
 * Teknik Detaylar:
 * - Selective deletion with exclusions
 * - Batch deletion processing
 * - Deleted cookies tracking
 * - Safe exclusion list handling
 *
 * Kullanım Örneği:
 * $korunacaklar = ['dil', 'tema'];
 * $silinenler = Cerez_Hepsini_Temizle($korunacaklar);
 * echo count($silinenler) . " çerez silindi";
 *
 * @param array $hariç_tutulacaklar Silinmeyecek çerez adları array'i
 * @return array Silinen çerez adları array'i
 */
function Cerez_Hepsini_Temizle($hariç_tutulacaklar = [])
{
    $silinen_cerezler = [];

    foreach ($_COOKIE as $ad => $deger) {
        if (!in_array($ad, $hariç_tutulacaklar)) {
            Cerez_Sil($ad);
            $silinen_cerezler[] = $ad;
        }
    }

    return $silinen_cerezler;
}
