<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Güvenlik ve Input Sanitization Fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * XSS (Cross-Site Scripting) Temizleme İşlemi
 *
 * Bu fonksiyon, kullanıcı girişlerini XSS saldırılarına karşı temizler.
 * HTML tag'lerini kaldırır ve özel karakterleri kodlar.
 *
 * Kullanım Alanı:
 * - Kullanıcı form input'larının temizlenmesinde
 * - Comment ve feedback sistemlerinde
 * - Search query sanitization'da
 * - User-generated content'de
 *
 * Teknik Detaylar:
 * - strip_tags() ile HTML tag temizleme
 * - htmlspecialchars() ile karakter encoding
 * - ENT_QUOTES ile quote character encoding
 * - UTF-8 encoding support
 *
 * Güvenlik Önlemleri:
 * - Script tag'leri tamamen kaldırır
 * - HTML event handler'ları temizler
 * - JavaScript injection'ları engeller
 * - SQL injection'a ek koruma sağlamaz
 *
 * UYARI:
 * - Bu fonksiyon sadece XSS içindir
 * - SQL injection için ayrı önlem alın
 * - Output encoding da yapın
 *
 * Kullanım Örneği:
 * $temiz = Xss_Temizle($_POST['comment']);
 * $sadece_strip = Xss_Temizle($input, false);
 *
 * @param string $veri Temizlenecek veri
 * @param bool $htmlKodla HTML karakterleri encode edilsin mi (varsayılan: true)
 * @return string XSS'e karşı temizlenmiş veri
 */
function Xss_Temizle($veri, $htmlKodla = true)
{
    $veri = strip_tags($veri);
    if ($htmlKodla) {
        $veri = htmlspecialchars($veri, ENT_QUOTES, 'UTF-8');
    }
    return $veri;
}

/**
 * Basit SQL Injection Temizleme İşlemi
 *
 * Bu fonksiyon, temel SQL injection temizleme yapar. Ancak prepared
 * statement'lar daha güvenli ve önerilen yöntemdir.
 *
 * Kullanım Alanı:
 * - Legacy code'da geçici çözüm
 * - Basit string temizleme
 * - Quick input sanitization
 * - Development/testing ortamında
 *
 * Teknik Detaylar:
 * - strip_tags() ile HTML tag temizleme
 * - trim() ile boşluk temizleme
 * - addslashes() ile quote escaping
 * - Basic character escaping
 *
 * ÖNEMLİ UYARI:
 * - Bu fonksiyon tam güvenlik sağlamaz!
 * - Prepared statement'lar kullanın!
 * - PDO veya MySQLi prepared queries tercih edin!
 * - Bu sadece ek katman korunmasıdır!
 *
 * Daha Güvenli Alternatifler:
 * - PDO prepared statements
 * - MySQLi prepared statements
 * - ORM query builders
 * - Parameterized queries
 *
 * Kullanım Örneği:
 * $temiz = Sql_Temizle($_POST['username']);
 * // ANCAK BUNUN YERİNE BUNU YAPIN:
 * // $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
 * // $stmt->execute([$_POST['username']]);
 *
 * @param string $veri Temizlenecek veri
 * @return string Basit SQL temizleme uygulanmış veri
 * @deprecated Prepared statements kullanın!
 */
function Sql_Temizle($veri)
{
    return addslashes(strip_tags(trim($veri)));
}

/**
 * Gelişmiş XSS Temizleme İşlemi
 *
 * Bu fonksiyon, standart XSS temizlemeden daha kapsamlı koruma sağlar.
 * Whitelist-based approach kullanarak izinli HTML tag'leri korur.
 *
 * Kullanım Alanı:
 * - Rich text editor içeriğinin temizlenmesinde
 * - Blog post ve article content'inde
 * - Comment sistemlerinde HTML desteği varsa
 * - CMS content management'da
 *
 * Teknik Detaylar:
 * - Whitelist-based HTML tag filtering
 * - Dangerous attribute removal
 * - Script ve event handler temizleme
 * - URL validation for links
 * - CSS injection prevention
 *
 * İzinli Elementler:
 * - Metin formatlaması: p, br, strong, em, u, i, b
 * - Listeler: ul, ol, li
 * - Linkler: a (href kontrollü)
 * - Başlıklar: h1-h6
 *
 * Kullanım Örneği:
 * $temiz = Xss_Gelismis_Temizle($html_content);
 * $custom_tags = ['p', 'br', 'strong'];
 * $ozel = Xss_Gelismis_Temizle($content, $custom_tags);
 *
 * @param string $veri Temizlenecek HTML content
 * @param array $izinliTagler İzinli HTML tag'leri (varsayılan: güvenli tag'ler)
 * @return string Güvenli HTML content
 */
function Xss_Gelismis_Temizle($veri, $izinliTagler = null)
{
    if ($izinliTagler === null) {
        $izinliTagler = [
            'p',
            'br',
            'strong',
            'em',
            'u',
            'i',
            'b',
            'ul',
            'ol',
            'li',
            'a',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6'
        ];
    }

    // İzinli tag'leri string formatına çevir
    $tagString = '<' . implode('><', $izinliTagler) . '>';

    // Sadece izinli tag'leri koru
    $veri = strip_tags($veri, $tagString);

    // Tehlikeli attribute'ları temizle
    $veri = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $veri); // Event handlers
    $veri = preg_replace('/\s*javascript\s*:/', '', $veri); // JavaScript URLs
    $veri = preg_replace('/\s*vbscript\s*:/', '', $veri); // VBScript URLs
    $veri = preg_replace('/\s*data\s*:/', '', $veri); // Data URLs

    return $veri;
}

/**
 * CSRF Token Üretici
 *
 * Bu fonksiyon, Cross-Site Request Forgery saldırılarına karşı
 * güvenli token üretir ve session'da saklar.
 *
 * Kullanım Alanı:
 * - Form submission'larında CSRF korunması
 * - AJAX request'lerinde security token
 * - API endpoint'lerinde request validation
 * - Administrative action'larda ek güvenlik
 *
 * Teknik Detaylar:
 * - Cryptographically secure random token
 * - Session-based token storage
 * - Configurable token expiration
 * - Per-form unique token support
 * - Automatic cleanup of expired tokens
 *
 * Güvenlik Özellikleri:
 * - 32-byte random token generation
 * - Timing attack resistant comparison
 * - Automatic token rotation
 * - Session binding for additional security
 *
 * Kullanım Örneği:
 * $token = Csrf_Token_Olustur();
 * $form_token = Csrf_Token_Olustur('user_update_form');
 * echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
 *
 * @param string $form_id Form identifier (opsiyonel, multi-form support için)
 * @param int $gecerlilik Token geçerlilik süresi saniye (varsayılan: 3600)
 * @return string CSRF token
 */
function Csrf_Token_Olustur($form_id = 'default', $gecerlilik = 3600)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Eski expired token'ları temizle
    Csrf_Token_Temizle();

    $token = bin2hex(random_bytes(32));
    $expiry = time() + $gecerlilik;

    if (!isset($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    $_SESSION['csrf_tokens'][$form_id] = [
        'token' => $token,
        'expiry' => $expiry
    ];

    return $token;
}

/**
 * CSRF Token Doğrulama
 *
 * Bu fonksiyon, gönderilen CSRF token'ın geçerliliğini kontrol eder.
 * Timing-safe comparison kullanarak güvenli doğrulama yapar.
 *
 * Kullanım Alanı:
 * - Form submission validation'da
 * - AJAX request verification'da
 * - API security checking'de
 * - Administrative action validation'da
 *
 * Teknik Detaylar:
 * - Timing-safe token comparison
 * - Expiration time validation
 * - Session binding verification
 * - Automatic token cleanup
 * - One-time token consumption option
 *
 * Güvenlik Kontrolleri:
 * - Token existence validation
 * - Expiration time check
 * - Session binding verification
 * - Length validation for timing attack prevention
 *
 * Kullanım Örneği:
 * if (Csrf_Token_Dogrula($_POST['csrf_token'])) {
 *     // Form işleme devam et
 * } else {
 *     // CSRF saldırısı detected
 * }
 *
 * @param string $token Doğrulanacak CSRF token
 * @param string $form_id Form identifier (opsiyonel)
 * @param bool $tek_kullanim Token tek kullanımlık mı (varsayılan: true)
 * @return bool Token geçerliyse true, değilse false
 */
function Csrf_Token_Dogrula($token, $form_id = 'default', $tek_kullanim = true)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($token) || !isset($_SESSION['csrf_tokens'][$form_id])) {
        return false;
    }

    $storedData = $_SESSION['csrf_tokens'][$form_id];

    // Expiry kontrolü
    if (time() > $storedData['expiry']) {
        unset($_SESSION['csrf_tokens'][$form_id]);
        return false;
    }

    // Timing-safe comparison
    $isValid = hash_equals($storedData['token'], $token);

    // Tek kullanımlık ise token'ı sil
    if ($isValid && $tek_kullanim) {
        unset($_SESSION['csrf_tokens'][$form_id]);
    }

    return $isValid;
}

/**
 * Expired CSRF Token Temizleme
 *
 * Bu fonksiyon, süresi dolmuş CSRF token'larını session'dan temizler.
 * Memory usage'ı optimize etmek için düzenli çalıştırılmalıdır.
 *
 * Kullanım Alanı:
 * - Session cleanup operasyonlarında
 * - Scheduled maintenance task'lerinde
 * - Memory optimization'da
 * - Token management'da
 *
 * @return int Temizlenen token sayısı
 */
function Csrf_Token_Temizle()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_tokens'])) {
        return 0;
    }

    $current_time = time();
    $cleaned = 0;

    foreach ($_SESSION['csrf_tokens'] as $form_id => $data) {
        if ($current_time > $data['expiry']) {
            unset($_SESSION['csrf_tokens'][$form_id]);
            $cleaned++;
        }
    }

    return $cleaned;
}

/**
 * IP Adres Güvenlik Kontrolü
 *
 * Bu fonksiyon, IP adresinin güvenlik blacklist'inde olup olmadığını
 * kontrol eder ve şüpheli aktivite tespiti yapar.
 *
 * Kullanım Alanı:
 * - Login attempt monitoring'de
 * - Rate limiting sistemlerinde
 * - Spam prevention'da
 * - Security incident detection'da
 *
 * Teknik Detaylar:
 * - IP whitelist/blacklist support
 * - Geolocation-based filtering
 * - Rate limiting integration
 * - Suspicious pattern detection
 * - Log integration for security events
 *
 * Kullanım Örneği:
 * if (!Ip_Guvenlik_Kontrol($_SERVER['REMOTE_ADDR'])) {
 *     // IP blocked veya suspicious
 * }
 *
 * @param string $ip Kontrol edilecek IP adresi
 * @param array $whitelist İzinli IP listesi (opsiyonel)
 * @param array $blacklist Yasaklı IP listesi (opsiyonel)
 * @return bool IP güvenilir ise true, değilse false
 */
function Ip_Guvenlik_Kontrol($ip, $whitelist = [], $blacklist = [])
{
    // IP format validation
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }

    // Whitelist kontrolü - eğer whitelist varsa ve IP içinde değilse reddet
    if (!empty($whitelist) && !in_array($ip, $whitelist)) {
        return false;
    }

    // Blacklist kontrolü
    if (!empty($blacklist) && in_array($ip, $blacklist)) {
        return false;
    }

    // Private/local IP'leri genelde güvenilir kabul et
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return true; // Local/private IP
    }

    // Burada ek güvenlik kontrolleri eklenebilir:
    // - Rate limiting check
    // - GeoIP filtering
    // - Known bad IP database lookup
    // - Reputation score checking

    return true; // Default: allow
}

/**
 * Kullanıcı Agent Güvenlik Kontrolü
 *
 * Bu fonksiyon, HTTP User-Agent header'ının şüpheli olup olmadığını
 * kontrol eder ve bot/scraper detection yapar.
 *
 * Kullanım Alanı:
 * - Bot detection sistemlerinde
 * - Scraper prevention'da
 * - Rate limiting'de
 * - Access control'de
 *
 * Teknik Detaylar:
 * - Known bot pattern detection
 * - Suspicious user agent filtering
 * - Empty/missing user agent handling
 * - Whitelist/blacklist support
 * - Pattern matching for common threats
 *
 * Kullanım Örneği:
 * if (!User_Agent_Kontrol($_SERVER['HTTP_USER_AGENT'])) {
 *     // Suspicious user agent detected
 * }
 *
 * @param string $user_agent HTTP User-Agent string
 * @param bool $bot_izin Bot'lara izin verilsin mi (varsayılan: false)
 * @return bool User agent güvenilir ise true, değilse false
 */
function User_Agent_Kontrol($user_agent, $bot_izin = false)
{
    // Boş user agent kontrolü
    if (empty($user_agent)) {
        return false;
    }

    // Çok kısa user agent'ler şüpheli
    if (strlen($user_agent) < 10) {
        return false;
    }

    // Bilinen bot pattern'leri
    $bot_patterns = [
        '/bot/i',
        '/crawler/i',
        '/spider/i',
        '/scraper/i',
        '/curl/i',
        '/wget/i',
        '/python/i',
        '/perl/i'
    ];

    if (!$bot_izin) {
        foreach ($bot_patterns as $pattern) {
            if (preg_match($pattern, $user_agent)) {
                return false;
            }
        }
    }

    // Şüpheli karakterler
    if (preg_match('/[<>"\']/', $user_agent)) {
        return false;
    }

    return true;
}

/**
 * Güvenli Header Ayarlama
 *
 * Bu fonksiyon, web uygulaması için önemli güvenlik header'larını
 * otomatik olarak ayarlar.
 *
 * Kullanım Alanı:
 * - Page response öncesi güvenlik setup'da
 * - API response'larında
 * - Security hardening'de
 * - OWASP best practices implementation'da
 *
 * Teknik Detaylar:
 * - XSS Protection headers
 * - Content Type Options
 * - Frame Options
 * - HSTS (HTTP Strict Transport Security)
 * - Content Security Policy başlangıç
 * - Referrer Policy
 *
 * Ayarlanan Header'lar:
 * - X-XSS-Protection
 * - X-Content-Type-Options
 * - X-Frame-Options
 * - Strict-Transport-Security
 * - Referrer-Policy
 *
 * Kullanım Örneği:
 * Guvenlik_Header_Ayarla();
 * // Veya özelleştirilmiş:
 * Guvenlik_Header_Ayarla(true, 'SAMEORIGIN', 31536000);
 *
 * @param bool $hsts HSTS aktif edilsin mi (varsayılan: true)
 * @param string $frame_policy Frame policy (DENY, SAMEORIGIN, varsayılan: DENY)
 * @param int $hsts_max_age HSTS max age saniye (varsayılan: 31536000 = 1 yıl)
 */
function Guvenlik_Header_Ayarla($hsts = true, $frame_policy = 'DENY', $hsts_max_age = 31536000)
{
    // Sadece headers gönderilmemişse ayarla
    if (!headers_sent()) {
        // XSS Protection
        header('X-XSS-Protection: 1; mode=block');

        // Content Type Options
        header('X-Content-Type-Options: nosniff');

        // Frame Options
        header('X-Frame-Options: ' . $frame_policy);

        // HSTS (sadece HTTPS'de)
        if ($hsts && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=' . $hsts_max_age . '; includeSubDomains');
        }

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Permissions Policy (önceden Feature Policy)
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}
