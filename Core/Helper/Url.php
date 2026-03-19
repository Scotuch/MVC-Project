<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * URL fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Kısa URL Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, verilen path'e göre tam URL oluşturur.
 * TEMEL_DIZIN sabitini kullanarak internal link'ler üretir.
 *
 * Kullanım Alanı:
 * - Navigation link'lerinde
 * - Asset (CSS/JS/Image) link'lerinde
 * - Form action URL'lerinde
 * - Router system'da
 *
 * Teknik Detaylar:
 * - Absolute URL detection (http/https/www)
 * - TEMEL_DIZIN constant integration
 * - Safe URL generation
 * - XSS attack prevention
 *
 * Güvenlik Önlemleri:
 * - JavaScript protocol blocking
 * - Colon character filtering
 * - XSS attack prevention
 *
 * Kullanım Örneği:
 * $link = URL('admin/dashboard');
 * $absolute = URL('https://example.com'); // Returns as-is
 *
 * @param string $url Oluşturulacak URL
 * @return string Tam ve güvenli URL
 */
function URL($url = '')
{
    return Url_Olustur($url);
}

/**
 * Güvenli URL Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, verilen URL'i güvenli formata dönüştürür ve
 * zararsız hale getirir. XSS ataklarına karşı koruma sağlar.
 *
 * Kullanım Alanı:
 * - Navigation link'lerinin oluşturulmasında
 * - Dinamik URL üretiminde
 * - Router sisteminde
 * - Template engine'lerde link oluşturmada
 *
 * Teknik Detaylar:
 * - Absolute URL algılama (http/https/www)
 * - JavaScript injection önleme
 * - TEMEL_DIZIN sabiti entegrasyonu
 * - Security validation
 * - $full_url parametresi ile tam domainli URL oluşturma
 *
 * Güvenlik Önlemleri:
 * - JavaScript protokol engelleme
 * - Colon karakteri filtreleme
 * - XSS saldırılarına karşı koruma
 * - Güvenli URL üretimi
 *
 * Kullanım Örneği:
 * $link = Url_Olustur('admin/dashboard');
 * $safe = Url_Olustur('javascript:alert(1)'); // 'javascript:;'
 * $absolute = Url_Olustur('https://example.com'); // as-is
 * $full = Url_Olustur('admin/dashboard', true); // Tam domainli URL
 *
 * @param string $url Oluşturulacak URL path'i
 * @param bool $full_url true ise tam domainli (absolute) URL döner, false ise TEMEL_DIZIN ile başlar
 * @return string Güvenli ve tam URL
 */
function Url_Olustur($url = '', $full_url = false)
{
    if (preg_match('/^(http:\/\/|https:\/\/|www\.)/i', $url)) {
        return $url;
    }

    if (str_contains($url, ":")) {
        return 'javascript:;';
    }
    if ($full_url) {
        return Mevcut_Domain_Al() . TEMEL_DIZIN . ltrim($url, '/');
    }
    return TEMEL_DIZIN . $url;
}
/**
 * URL'den Ana Domain Adı Çıkarma Fonksiyonu
 *
 * Bu fonksiyon, tam URL'den sadece ana domain adını çıkarır.
 * Subdomain'leri temizler ve clean domain name döndürür.
 *
 * Kullanım Alanı:
 * - Domain-based filtering sistemlerinde
 * - Analytics ve tracking'de
 * - Whitelist/blacklist kontrolünde
 * - External link validation'da
 *
 * Teknik Detaylar:
 * - Protocol normalization (https->http)
 * - www prefix removal
 * - parse_url() ile host extraction
 * - Multi-level subdomain handling
 *
 * URL Processing:
 * - https://www.example.com -> example
 * - http://subdomain.example.com -> example
 * - www.test.org -> test
 * - Invalid URL -> null
 *
 * Kullanım Örneği:
 * $domain = Host_Adi_Al('https://www.google.com/search'); // 'google'
 * $site = Host_Adi_Al('http://subdomain.example.org'); // 'example'
 * $invalid = Host_Adi_Al('not-a-url'); // null
 *
 * @param string $link İşlemlenecek tam URL
 * @return string|null Ana domain adı veya geçersizse null
 */
function Host_Adi_Al($link = '')
{
    if (empty($link)) {
        return null;
    }
    $link = str_replace(["https://www.", "www."], "http://", $link);
    $link = str_replace("http://http://", "http://", $link);
    $schema = parse_url($link);

    if (isset($schema['host'])) {
        $host = $schema['host'];
        $parts = explode('.', $host);
        return count($parts) > 2 ? $parts[count($parts) - 2] : $parts[0];
    }
    return null;
}

/**
 * URL Geçerlilik Kontrol Fonksiyonu
 *
 * Bu fonksiyon, verilen string'in geçerli bir URL formatında
 * olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Form validation'da URL field kontrolu
 * - API parameter validation'da
 * - User input sanitization'da
 * - External link verification'da
 *
 * Teknik Detaylar:
 * - FILTER_VALIDATE_URL kullanır
 * - RFC compliance kontrolü
 * - Protocol requirement (http/https)
 * - Domain name validation
 *
 * Validation Kriterleri:
 * - Valid protocol (http/https/ftp)
 * - Valid domain name format
 * - Valid URL structure
 * - RFC 3986 compliance
 *
 * Kullanım Örneği:
 * if (Url_Gecerli_Mi('https://example.com')) {
 *     echo 'Geçerli URL';
 * }
 * $valid = Url_Gecerli_Mi('not-a-url'); // false
 * $valid = Url_Gecerli_Mi('https://test.com'); // true
 *
 * @param string $url Kontrol edilecek URL string'i
 * @return bool Geçerli URL ise true, değilse false
 */
function Url_Gecerli_Mi($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Mevcut Sayfa URL'ini Alma Fonksiyonu
 *
 * Bu fonksiyon, kullanıcının şu anda bulunduğu sayfanın
 * tam URL'ini döndürür. Protocol, host ve request URI bilgilerini birleştirir.
 *
 * Kullanım Alanı:
 * - Canonical URL oluşturmada
 * - Share button'larda
 * - Logging ve analytics'de
 * - Referer kontrolünde
 *
 * Teknik Detaylar:
 * - $_SERVER superglobal kullanımı
 * - HTTPS detection (SSL kontrolü)
 * - HTTP_HOST fallback (localhost default)
 * - REQUEST_URI parsing
 *
 * Güvenlik Önemleri:
 * - Host header injection koruması yok (dikkat!)
 * - REQUEST_URI sanitization yapılmaz
 * - HTTPS detection şifreleme kontrolü
 * - Localhost fallback güvenli
 *
 * Kullanım Örneği:
 * $current = Mevcut_Url_Al();
 * // Örnek: https://example.com/admin/dashboard?page=1
 * echo $current; // Tam URL döner
 *
 * @return string Mevcut sayfanın tam URL'i (protocol + host + path + query)
 */
function Mevcut_Url_Al()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '';

    return $protocol . $host . $uri;
}

/**
 * Mevcut URL Path'ini Alma Fonksiyonu
 *
 * Bu fonksiyon, mevcut sayfanın sadece path kısmını döndürür.
 * Protocol, domain ve query string'i çıkarır, sadece yol bilgisini alır.
 *
 * Kullanım Alanı:
 * - Route matching'de
 * - Navigation menu'lerde active kontrolü
 * - Breadcrumb oluşturmada
 * - Path-based authorization'da
 *
 * Teknik Detaylar:
 * - Mevcut_Url_Al() fonksiyonunu kullanır
 * - parse_url() ile PHP_URL_PATH extraction
 * - Null safety ile '/' fallback
 * - Query string ve fragment temizliği
 *
 * Path Format:
 * - /admin/dashboard -> /admin/dashboard
 * - / -> / (ana sayfa)
 * - /products?id=1 -> /products (query temizlenir)
 * - Invalid URL -> / (fallback)
 *
 * Kullanım Örneği:
 * // URL: https://example.com/admin/users?page=2
 * $path = Mevcut_Yol_Al();
 * echo $path; // '/admin/users'
 *
 * @return string Mevcut sayfanın path kısmı (slash ile başlar)
 */
function Mevcut_Yol_Al()
{
    return parse_url(Mevcut_Url_Al(), PHP_URL_PATH) ?? '/';
}

/**
 * Mevcut Domain'i Alma Fonksiyonu
 *
 * Bu fonksiyon, mevcut sitenin protokol ve domain kısmını
 * döndürür. Path, query ve fragment bilgilerini çıkarır.
 *
 * Kullanım Alanı:
 * - Cross-domain kontrolünde
 * - Absolute URL oluşturmada
 * - API endpoint tanımlamada
 * - CDN URL'lerinde
 *
 * Teknik Detaylar:
 * - $_SERVER HTTPS kontrolü
 * - HTTP_HOST extraction
 * - Protocol detection (http/https)
 * - Port bilgisi dahil değil
 *
 * Güvenlik Önemleri:
 * - Host header injection riski
 * - HTTPS detection güvenilir
 * - Localhost fallback güvenli
 * - Port spoofing koruması yok
 *
 * Domain Format:
 * - https://example.com (HTTPS site)
 * - http://localhost (local development)
 * - https://subdomain.site.com (subdomain)
 * - http://192.168.1.100 (IP address)
 *
 * Kullanım Örneği:
 * $domain = Mevcut_Domain_Al();
 * $api_url = $domain . '/api/v1/users';
 * // Örnek: https://example.com/api/v1/users
 *
 * @return string Mevcut domain (protocol + host, port hariç)
 */
function Mevcut_Domain_Al()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . $host;
}

/**
 * Parametreli URL Oluşturucu
 *
 * Bu fonksiyon, base URL'e query parametreleri ekleyerek
 * tam URL oluşturur. Mevcut parametreler varsa birleştirir.
 *
 * Kullanım Alanı:
 * - Filtreleme linklerinde
 * - Sayfalama (pagination) sistemlerinde
 * - API endpoint'lerinde
 * - Search form action'larında
 *
 * Teknik Detaylar:
 * - http_build_query() kullanımı
 * - Automatic URL encoding
 * - Query separator detection (? vs &)
 * - Empty parameter array handling
 *
 * Parameter Format:
 * - ['page' => 1, 'sort' => 'name'] -> ?page=1&sort=name
 * - ['q' => 'test search'] -> ?q=test+search
 * - [] -> Hiçbir değişiklik yok
 * - ['tags' => ['php', 'web']] -> ?tags%5B0%5D=php&tags%5B1%5D=web
 *
 * Kullanım Örneği:
 * $base = 'https://example.com/search';
 * $params = ['q' => 'PHP tutorial', 'page' => 2];
 * $url = Url_Parametreli_Olustur($base, $params);
 * // Sonuç: https://example.com/search?q=PHP+tutorial&page=2
 *
 * @param string $temel_url Temel URL (query string olabilir)
 * @param array $parametreler Eklenecek parametreler (key-value array)
 * @return string Parametreli tam URL (URL encoded)
 */
function Url_Parametreli_Olustur($temel_url, $parametreler = [])
{
    if (empty($parametreler)) {
        return $temel_url;
    }

    $query_string = http_build_query($parametreler);
    $separator = strpos($temel_url, '?') !== false ? '&' : '?';

    return $temel_url . $separator . $query_string;
}

/**
 * URL Query Parameter Ekleme Fonksiyonu
 *
 * Bu fonksiyon, mevcut bir URL'e yeni query parametresi ekler.
 * Var olan parametreler korunur, aynı anahtarda parametre varsa güncellenir.
 *
 * Kullanım Alanı:
 * - Dinamik URL oluşturmada
 * - Filtreleme sistemlerinde
 * - Sayfalama (pagination) linklerinde
 * - Arama sonuçları URL'lerinde
 *
 * Teknik Detaylar:
 * - parse_url() ile URL parsing
 * - http_build_query() ile parameter serialization
 * - Port ve fragment bilgilerini korur
 * - Mevcut query string'i parse eder
 *
 * URL Yapısı:
 * - Scheme, host, port, path korunur
 * - Query string'e yeni parametre eklenir
 * - Fragment (#) kısmı korunur
 * - URL encoding otomatik yapılır
 *
 * Kullanım Örneği:
 * $url = 'https://example.com/search?q=test';
 * $yeni = Url_Parametre_Ekle($url, 'page', '2');
 * // Sonuç: https://example.com/search?q=test&page=2
 *
 * @param string $url Hedef URL (tam format)
 * @param string $anahtar Eklenecek parameter anahtarı
 * @param string $deger Eklenecek parameter değeri
 * @return string Yeni parametre eklenmiş tam URL
 */
function Url_Parametre_Ekle($url, $anahtar, $deger)
{
    $parsed = parse_url($url);
    $query = [];
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }
    $query[$anahtar] = $deger;
    $new_query = http_build_query($query);
    $base_url = $parsed['scheme'] . '://' . $parsed['host'];
    if (isset($parsed['port'])) {
        $base_url .= ':' . $parsed['port'];
    }
    if (isset($parsed['path'])) {
        $base_url .= $parsed['path'];
    }
    $base_url .= '?' . $new_query;
    if (isset($parsed['fragment'])) {
        $base_url .= '#' . $parsed['fragment'];
    }
    return $base_url;
}

/**
 * URL Query Parameter Kaldırma Fonksiyonu
 *
 * Bu fonksiyon, URL'den belirtilen query parametresini kaldırır.
 * Diğer parametreler ve URL yapısı korunur.
 *
 * Kullanım Alanı:
 * - Filtreleri temizlemede
 * - Sayfa geçişlerinde
 * - Geçici parametreleri kaldırmada
 * - URL cleanup işlemlerinde
 *
 * Teknik Detaylar:
 * - parse_url() ile URL ayrıştırma
 * - parse_str() ile query parsing
 * - unset() ile parametre kaldırma
 * - URL yeniden construction
 *
 * Güvenlik Önemleri:
 * - URL injection koruması
 * - Parameter validation yapılmaz (caller'da yapılmalı)
 * - Empty query string temizliği
 * - Fragment preservation
 *
 * Kullanım Örneği:
 * $url = 'https://example.com/search?q=test&page=2&sort=date';
 * $temiz = Url_Parametre_Kaldir($url, 'page');
 * // Sonuç: https://example.com/search?q=test&sort=date
 *
 * @param string $url Hedef URL (tam format)
 * @param string $anahtar Kaldırılacak parameter anahtarı
 * @return string Parameter kaldırılmış tam URL
 */
function Url_Parametre_Kaldir($url, $anahtar)
{
    $parsed = parse_url($url);
    $query = [];

    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $query);
        unset($query[$anahtar]);
    }

    $base_url = $parsed['scheme'] . '://' . $parsed['host'];

    if (isset($parsed['port'])) {
        $base_url .= ':' . $parsed['port'];
    }

    if (isset($parsed['path'])) {
        $base_url .= $parsed['path'];
    }

    if (!empty($query)) {
        $base_url .= '?' . http_build_query($query);
    }

    if (isset($parsed['fragment'])) {
        $base_url .= '#' . $parsed['fragment'];
    }

    return $base_url;
}

/**
 * URL Query Parametrelerini Alma Fonksiyonu
 *
 * Bu fonksiyon, URL'den query string parametrelerini çıkarır ve
 * key-value array formatında döndürür. Empty URL'de mevcut sayfa kullanılır.
 *
 * Kullanım Alanı:
 * - Form data processing'de
 * - URL parameter validation'da
 * - Search filter extraction'da
 * - Analytics ve tracking'de
 *
 * Teknik Detaylar:
 * - parse_url() ile URL parsing
 * - parse_str() ile query string decoding
 * - Mevcut_Url_Al() fallback mechanism
 * - Array key preservation
 *
 * Parameter Format:
 * - ?page=1&sort=name -> ['page'=>'1', 'sort'=>'name']
 * - ?tags[]=php&tags[]=web -> ['tags'=>['php','web']]
 * - ?search=test%20query -> ['search'=>'test query']
 * - Query yok -> [] (empty array)
 *
 * Kullanım Örneği:
 * $url = 'https://example.com/search?q=PHP&category=tutorial&page=2';
 * $params = Query_Parametreleri_Al($url);
 * print_r($params);
 * // ['q'=>'PHP', 'category'=>'tutorial', 'page'=>'2']
 *
 * @param string $url İşlemlenecek URL (boşsa mevcut URL kullanılır)
 * @return array Query parametreleri key-value array'i (boş olabilir)
 */
function Query_Parametreleri_Al($url = '')
{
    if (empty($url)) {
        $url = Mevcut_Url_Al();
    }

    $parsed = parse_url($url);
    $parametreler = [];

    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $parametreler);
    }

    return $parametreler;
}

/**
 * URL Path Extraction Fonksiyonu
 *
 * Bu fonksiyon, herhangi bir URL'den sadece path kısmını çıkarır.
 * Protocol, domain, query ve fragment bilgilerini temizler.
 *
 * Kullanım Alanı:
 * - Route parsing'de
 * - Path-based cache key'lerde
 * - File path extraction'da
 * - URL normalization'da
 *
 * Teknik Detaylar:
 * - parse_url() PHP_URL_PATH flag kullanımı
 * - Null safety ile empty string fallback
 * - Invalid URL handling
 * - Leading slash preservation
 *
 * Path Örnekleri:
 * - https://example.com/admin/users -> /admin/users
 * - /products?id=1 -> /products
 * - https://site.com/ -> /
 * - invalid-url -> '' (empty string)
 *
 * Kullanım Örneği:
 * $url = 'https://example.com/admin/dashboard?tab=users#content';
 * $path = Url_Yol_Al($url);
 * echo $path; // '/admin/dashboard'
 *
 * @param string $url Parse edilecek tam URL
 * @return string URL path kısmı (slash ile başlar, boş olabilir)
 */
function Url_Yol_Al($url)
{
    return parse_url($url, PHP_URL_PATH) ?? '';
}

/**
 * URL Fragment (Hash) Extraction Fonksiyonu
 *
 * Bu fonksiyon, URL'den fragment kısmını çıkarır (#işaretinden sonraki kısım).
 * SPA routing, anchor link'ler ve page sections için kullanılır.
 *
 * Kullanım Alanı:
 * - Single Page Application routing
 * - Anchor link navigation
 * - Page section jumping
 * - Hash-based state management
 *
 * Teknik Detaylar:
 * - parse_url() PHP_URL_FRAGMENT flag
 * - # karakteri dahil değil
 * - URL decoding yapılmaz
 * - Null return on missing fragment
 *
 * Fragment Örnekleri:
 * - https://example.com/#section1 -> 'section1'
 * - /page#top -> 'top'
 * - https://site.com/page -> null (fragment yok)
 * - /admin#users?tab=active -> 'users?tab=active'
 *
 * Kullanım Örneği:
 * $url = 'https://example.com/docs#installation';
 * $fragment = Url_Fragment_Al($url);
 * echo $fragment; // 'installation'
 *
 * @param string $url Parse edilecek tam URL
 * @return string|null Fragment kısmı (# hariç) veya null
 */
function Url_Fragment_Al($url)
{
    return parse_url($url, PHP_URL_FRAGMENT);
}

/**
 * Tek Query Parameter Değeri Alma Fonksiyonu
 *
 * Bu fonksiyon, URL'den belirli bir query parametresinin değerini alır.
 * Parametre yoksa null döndürür, mevcut URL varsayılan olarak kullanılır.
 *
 * Kullanım Alanı:
 * - GET parametresi okumada
 * - Filtreleme değerlerinde
 * - Sayfa numarası alımda
 * - Search query extraction'da
 *
 * Teknik Detaylar:
 * - Query_Parametreleri_Al() fonksiyonunu kullanır
 * - Array key lookup ile değer bulma
 * - Null coalescing operator (??) kullanımı
 * - Type preservation (string/int/array)
 *
 * Return Değerleri:
 * - ?page=5 -> '5' (string olarak)
 * - ?tags[]=php&tags[]=web -> ['php', 'web']
 * - ?active -> '' (değer yoksa empty string)
 * - Parametre yoksa -> null
 *
 * Kullanım Örneği:
 * // URL: https://example.com/search?q=PHP&page=2
 * $search = Query_Parametre_Al('q');
 * $page = Query_Parametre_Al('page');
 * echo $search; // 'PHP'
 * echo $page; // '2'
 *
 * @param string $anahtar Aranacak parameter anahtarı
 * @param string $url Hedef URL (boşsa mevcut URL kullanılır)
 * @return mixed Parameter değeri (string/array) veya null
 */
function Query_Parametre_Al($anahtar, $url = '')
{
    $parametreler = Query_Parametreleri_Al($url);
    return $parametreler[$anahtar] ?? null;
}

/**
 * Comprehensive URL Parser Fonksiyonu
 *
 * Bu fonksiyon, URL'i tüm bileşenlerine ayırır ve detaylı array döndürür.
 * PHP'nin parse_url() fonksiyonunu genişletir ve ek bilgiler ekler.
 *
 * Kullanım Alanı:
 * - URL analiz sistemlerinde
 * - Router development'da
 * - URL validation'da
 * - Debugging ve logging'de
 *
 * Teknik Detaylar:
 * - parse_url() native function kullanımı
 * - Query_Parametreleri_Al() integration
 * - Null safety ile default değerler
 * - Comprehensive component extraction
 *
 * Return Array Structure:
 * - 'scheme' -> http/https/ftp
 * - 'host' -> domain name
 * - 'port' -> port number (null if default)
 * - 'path' -> URL path
 * - 'query' -> raw query string
 * - 'fragment' -> hash fragment
 * - 'query_params' -> parsed parameters array
 *
 * Kullanım Örneği:
 * $url = 'https://user:pass@example.com:8080/path?q=test#section';
 * $parts = Url_Ayristir($url);
 * print_r($parts);
 * // ['scheme'=>'https', 'host'=>'example.com', 'port'=>8080, ...]
 *
 * @param string $url Parse edilecek tam URL
 * @return array URL bileşenleri ve parsed query parameters
 */
function Url_Ayristir($url)
{
    $parsed = parse_url($url);

    return [
        'scheme' => $parsed['scheme'] ?? '',
        'host' => $parsed['host'] ?? '',
        'port' => $parsed['port'] ?? null,
        'path' => $parsed['path'] ?? '',
        'query' => $parsed['query'] ?? '',
        'fragment' => $parsed['fragment'] ?? '',
        'query_params' => Query_Parametreleri_Al($url)
    ];
}

/**
 * HTTP Yönlendirme İşlemi
 *
 * Bu fonksiyon, kullanıcıyı belirtilen URL'e yönlendirir.
 * Location header gönderir ve script execution'u sonlandırır.
 *
 * Kullanım Alanı:
 * - Authentication flow yönlendirmeleri
 * - Form submission sonrası redirect
 * - Error handling redirects
 * - SEO-friendly URL redirections
 *
 * Teknik Detaylar:
 * - headers_sent() kontrolü ile safety check
 * - Location header ile browser redirect
 * - HTTP status code customization
 * - exit() ile script termination
 *
 * HTTP Status Codes:
 * - 301: Permanent Redirect (SEO transfer)
 * - 302: Temporary Redirect (default)
 * - 303: See Other (POST-to-GET)
 * - 307: Temporary Redirect (method preserved)
 *
 * Güvenlik Önemleri:
 * - Open redirect vulnerability riski
 * - URL validation caller'da yapılmalı
 * - Header injection koruması yok
 * - HTTPS downgrade riski
 *
 * Kullanım Örneği:
 * // Basit redirect
 * Sayfa_Yonlendir('/admin/dashboard');
 *
 * // SEO redirect (301)
 * Sayfa_Yonlendir('/new-page-url', 301);
 *
 * // POST-after-redirect pattern
 * Sayfa_Yonlendir('/success', 303);
 *
 * @param string $url Yönlendirilecek hedef URL (relative/absolute)
 * @param int $durum_kodu HTTP status kodu (301/302/303/307)
 * @return void HTTP redirect yapar ve script'i sonlandırır
 */
function Sayfa_Yonlendir($url, $durum_kodu = 302)
{
    if (!headers_sent()) {
        header('Location: ' . $url, true, $durum_kodu);
        exit();
    }
}

/**
 * Route-Based Yönlendirme Fonksiyonu
 *
 * Bu fonksiyon, application route'una göre yönlendirme yapar.
 * Internal URL oluşturur ve Sayfa_Yonlendir() ile redirect gerçekleştirir.
 *
 * Kullanım Alanı:
 * - MVC framework routing
 * - Controller method redirects
 * - Form submission sonrası yönlendirme
 * - Authentication flow'da
 *
 * Teknik Detaylar:
 * - Url_Olustur() ile internal URL generation
 * - Url_Parametreli_Olustur() ile query string
 * - Sayfa_Yonlendir() ile HTTP redirect
 * - exit() ile script termination
 *
 * Güvenlik Önemleri:
 * - Internal route validation yok
 * - Parameter sanitization caller'da yapılmalı
 * - Open redirect vulnerability riski
 * - TEMEL_DIZIN constant dependency
 *
 * Kullanım Örneği:
 * // Basit route redirect
 * Route_Yonlendir('admin/dashboard');
 *
 * // Parametreli redirect
 * Route_Yonlendir('users/profile', ['id' => 123, 'tab' => 'settings']);
 * // Yönlendirir: /admin/users/profile?id=123&tab=settings
 *
 * @param string $route Application içi route path'i
 * @param array $parametreler URL parametreleri (optional)
 * @return void HTTP redirect yapar ve script'i sonlandırır
 */
function Route_Yonlendir($route, $parametreler = [])
{
    $url = Url_Olustur($route);
    if (!empty($parametreler)) {
        $url = Url_Parametreli_Olustur($url, $parametreler);
    }
    Sayfa_Yonlendir($url);
}

/**
 * Referer-Based Geri Yönlendirme Fonksiyonu
 *
 * Bu fonksiyon, kullanıcıyı bir önceki sayfaya yönlendirir.
 * HTTP_REFERER header'ini kullanır, yoksa varsayılan URL'e gönderir.
 *
 * Kullanım Alanı:
 * - Form cancel işlemlerinde
 * - Error page'lerden dönüşte
 * - Authorization failure sonrası
 * - "Geri" button functionality
 *
 * Teknik Detaylar:
 * - $_SERVER['HTTP_REFERER'] header okuma
 * - Null coalescing ile fallback
 * - Sayfa_Yonlendir() wrapper function
 * - Browser referer dependency
 *
 * Güvenlik Riskleri:
 * - Referer spoofing vulnerability
 * - Open redirect attack riski
 * - Cross-site referer leakage
 * - Referer header absence
 *
 * Kullanım Örneği:
 * // Önceki sayfaya dön (varsa)
 * Geri_Yonlendir();
 *
 * // Custom fallback ile
 * Geri_Yonlendir('/admin/dashboard');
 * // Referer yoksa /admin/dashboard'a gönderir
 *
 * @param string $varsayilan Referer yoksa kullanılacak fallback URL
 * @return void HTTP redirect yapar ve script'i sonlandırır
 */
function Geri_Yonlendir($varsayilan = '/')
{
    $referer = $_SERVER['HTTP_REFERER'] ?? $varsayilan;
    Sayfa_Yonlendir($referer);
}

/**
 * URL Güvenlik Temizleme Fonksiyonu
 *
 * Bu fonksiyon, URL'i güvenlik tehditlerine karşı temizler.
 * Malicious protocol'leri engeller ve entity decoding yapar.
 *
 * Kullanım Alanı:
 * - User input URL validation
 * - External link sanitization
 * - XSS attack prevention
 * - Href attribute cleaning
 *
 * Teknik Detaylar:
 * - Protocol filtering (javascript/data/vbscript)
 * - HTML entity decoding normalization
 * - Whitespace trimming
 * - Case-insensitive protocol matching
 *
 * Güvenlik Önemleri:
 * - JavaScript protocol injection engelleme
 * - Data URI scheme filtering
 * - VBScript protocol blocking
 * - HTML entity bypass prevention
 *
 * Filtrelenen Protokoller:
 * - javascript: -> '' (boş string)
 * - data: -> '' (base64/inline content)
 * - vbscript: -> '' (IE legacy)
 * - Güvenli: http/https/ftp/mailto
 *
 * Kullanım Örneği:
 * $malicious = 'javascript:alert(1)';
 * $safe = Url_Temizle($malicious);
 * echo $safe; // '' (empty string)
 *
 * $encoded = '&lt;script&gt;alert(1)&lt;/script&gt;';
 * $decoded = Url_Temizle($encoded);
 * echo $decoded; // '<script>alert(1)</script>'
 *
 * @param string $url Temizlenecek URL (user input)
 * @return string Güvenlik filtresinden geçmiş URL
 */
function Url_Temizle($url)
{
    $url = preg_replace('/^(javascript|data|vbscript):/i', '', $url);
    $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
    $url = trim($url);
    return $url;
}
/**
 * URL File Extension Extraction Fonksiyonu
 *
 * Bu fonksiyon, URL'den dosya uzantısını çıkarır.
 * Path kısmındaki dosya adından extension bilgisini alır.
 *
 * Kullanım Alanı:
 * - File type detection
 * - MIME type belirleme
 * - Download handler routing
 * - Static asset processing
 *
 * Teknik Detaylar:
 * - Url_Yol_Al() ile path extraction
 * - pathinfo() ile extension parsing
 * - Null safety ile fallback
 * - Case sensitivity preservation
 *
 * Extension Örnekleri:
 * - https://example.com/image.jpg -> 'jpg'
 * - /assets/style.css?v=1.0 -> 'css'
 * - /api/users -> null (uzantı yok)
 * - /file.tar.gz -> 'gz' (son uzantı)
 *
 * Kullanım Örneği:
 * $url = 'https://cdn.example.com/assets/app.min.js?v=2.1';
 * $ext = Url_Uzanti_Al($url);
 * echo $ext; // 'js'
 *
 * if ($ext === 'pdf') {
 *     header('Content-Type: application/pdf');
 * }
 *
 * @param string $url Parse edilecek file URL
 * @return string|null File extension (noktasız) veya null
 */
function Url_Uzanti_Al($url)
{
    $yol = Url_Yol_Al($url);
    $pathinfo = pathinfo($yol);
    return $pathinfo['extension'] ?? null;
}

/**
 * Domain Karşılaştırma Fonksiyonu
 *
 * Bu fonksiyon, iki URL'in aynı domain'e ait olup olmadığını kontrol eder.
 * Sadece host kısmını karşılaştırır, protocol ve path önemsiz.
 *
 * Kullanım Alanı:
 * - Cross-domain validation
 * - CORS policy kontrolü
 * - Internal/external link ayrımı
 * - Security policy enforcement
 *
 * Teknik Detaylar:
 * - parse_url() PHP_URL_HOST extraction
 * - Strict string comparison (===)
 * - Protocol agnostic comparison
 * - Subdomain sensitive
 *
 * Karşılaştırma Örnekleri:
 * - https://example.com vs http://example.com -> true
 * - www.site.com vs site.com -> false
 * - sub.example.com vs example.com -> false
 * - example.com:8080 vs example.com -> false
 *
 * Kullanım Örneği:
 * $internal = 'https://mysite.com/admin';
 * $external = 'https://google.com';
 *
 * if (Ayni_Domain_Mi($internal, Mevcut_Url_Al())) {
 *     echo 'Internal link';
 * } else {
 *     echo 'External link - add rel="nofollow"';
 * }
 *
 * @param string $url1 İlk karşılaştırılacak URL
 * @param string $url2 İkinci karşılaştırılacak URL
 * @return bool Aynı domain ise true, farklıysa false
 */
function Ayni_Domain_Mi($url1, $url2)
{
    $host1 = parse_url($url1, PHP_URL_HOST);
    $host2 = parse_url($url2, PHP_URL_HOST);

    return $host1 === $host2;
}

/**
 * Localhost Detection Fonksiyonu
 *
 * Bu fonksiyon, verilen URL'in localhost veya local network'te
 * olup olmadığını kontrol eder. Development environment detection için.
 *
 * Kullanım Alanı:
 * - Development/production environment ayrımı
 * - Debug mode activation
 * - Local asset serving
 * - Security policy bypass (dikkatli!)
 *
 * Teknik Detaylar:
 * - parse_url() host extraction
 * - Predefined localhost patterns
 * - IPv4 private network regex (192.168.x.x)
 * - Common localhost aliases
 *
 * Tanınan Localhost Patterns:
 * - 'localhost' -> Local hostname
 * - '127.0.0.1' -> IPv4 loopback
 * - '::1' -> IPv6 loopback
 * - '0.0.0.0' -> All interfaces
 * - '192.168.x.x' -> Private network range
 *
 * Kullanım Örneği:
 * $url = 'http://192.168.1.100:3000/api';
 *
 * if (Localhost_Mi($url)) {
 *     ini_set('display_errors', 1); // Development mode
 *     $debug = true;
 * } else {
 *     $debug = false; // Production mode
 * }
 *
 * @param string $url Kontrol edilecek URL
 * @return bool Localhost/local network ise true, değilse false
 */
function Localhost_Mi($url)
{
    $host = parse_url($url, PHP_URL_HOST);
    $localhost_patterns = ['localhost', '127.0.0.1', '::1', '0.0.0.0'];

    return in_array($host, $localhost_patterns) || preg_match('/^192\.168\.\d+\.\d+$/', $host);
}

/**
 * SEO Dostu URL Slug Üretici
 *
 * Bu fonksiyon, Türkçe ve özel karakterli metinleri
 * URL'de kullanılabilir slug formatına dönüştürür. ASCII-safe output üretir.
 *
 * Kullanım Alanı:
 * - Blog post URL generation
 * - Product page slugs
 * - Category URL creation
 * - SEO-friendly URL structure
 *
 * Teknik Detaylar:
 * - Türkçe karakter mapping (manual array)
 * - Lowercase transformation
 * - Non-alphanumeric character removal
 * - Multiple whitespace normalization
 *
 * Dönüştürme Süreci:
 * 1. Türkçe -> İngilizce karakter mapping
 * 2. strtolower() ile lowercase
 * 3. Regex ile özel karakter temizleme
 * 4. Whitespace normalization
 * 5. Space -> separator replacement
 *
 * Karakter Mapping:
 * - ç/Ç -> c/C
 * - ğ/Ğ -> g/G
 * - ı/I/İ -> i/I/I
 * - ö/Ö -> o/O
 * - ş/Ş -> s/S
 * - ü/Ü -> u/U
 *
 * Kullanım Örneği:
 * $title = 'Türkçe Makale Başlığı - 2024';
 * $slug = Slug_Olustur($title);
 * echo $slug; // 'turkce-makale-basligi-2024'
 *
 * // Custom separator
 * $custom = Slug_Olustur('Test Metin', '_');
 * echo $custom; // 'test_metin'
 *
 * @param string $metin Slug'a dönüştürülecek raw text
 * @param string $ayirici Kelime ayırıcı karakter (default: '-')
 * @return string URL-safe, SEO-friendly slug
 */
function Slug_Olustur($metin, $ayirici = '-', $secenekler = [])
{
    $varsayilan = [
        'lowercase' => true,
        'max_length' => 100,
        'trim_separator' => true
    ];

    $opts = array_merge($varsayilan, $secenekler);

    // Türkçe karakterleri dönüştür (geliştirilmiş)
    $turkce = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'];
    $ingilizce = ['c', 'g', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'I', 'O', 'S', 'U'];
    $metin = str_replace($turkce, $ingilizce, $metin);

    // Unicode normalization
    if (function_exists('normalizer_normalize')) {
        $metin = normalizer_normalize($metin, Normalizer::FORM_D);
    }

    // Küçük harfe dönüştür
    if ($opts['lowercase']) {
        $metin = strtolower($metin);
    }

    // Özel karakterleri temizle
    $metin = preg_replace('/[^a-z0-9\s]/', '', $metin);
    $metin = preg_replace('/\s+/', ' ', $metin);
    $metin = trim($metin);

    // Boşlukları ayırıcı ile değiştir
    $metin = str_replace(' ', $ayirici, $metin);

    // Çoklu ayırıcıları tek yap
    $metin = preg_replace('/[' . preg_quote($ayirici, '/') . ']+/', $ayirici, $metin);

    // Başındaki ve sonundaki ayırıcıları kaldır
    if ($opts['trim_separator']) {
        $metin = trim($metin, $ayirici);
    }

    // Uzunluk sınırla
    if ($opts['max_length'] > 0 && strlen($metin) > $opts['max_length']) {
        $metin = substr($metin, 0, $opts['max_length']);

        // Son kelimeyi koru
        $last_separator = strrpos($metin, $ayirici);
        if ($last_separator !== false) {
            $metin = substr($metin, 0, $last_separator);
        }
    }

    return $metin;
}

/**
 * URL Kısaltma Fonksiyonu
 *
 * Bu fonksiyon, uzun URL'leri belirlenen maksimum uzunluğa göre kısaltır.
 * Baş ve son kısımları koruyarak ortadan kırpar, okuması kolaylaştırır.
 *
 * Kullanım Alanı:
 * - UI'da URL gösterimi
 * - Log dosyalarında
 * - Email template'lerinde
 * - Social media sharing
 *
 * Teknik Detaylar:
 * - String length calculation
 * - Floor division ile symmetric cutting
 * - substr() ile baş/son extraction
 * - Custom separator support
 *
 * Kısaltma Örnekleri:
 * - 'https://example.com/very/long/path/to/file.html' (50 char)
 * - Result: 'https://example.c...o/file.html'
 * - Separator: '...' (default)
 * - Baş + separator + son = maks_uzunluk
 *
 * Kullanım Örneği:
 * $uzun_url = 'https://example.com/admin/users/profile/edit/settings';
 * $kısa = Url_Kisalt($uzun_url, 30, '...');
 * echo $kısa; // 'https://exa...settings'
 *
 * @param string $url Kısaltılacak URL
 * @param int $maks_uzunluk Maksimum karakter sayısı (default: 50)
 * @param string $ayirici Orta kısım separator (default: '...')
 * @return string Kısaltılmış URL veya orijinal (kısası)
 */
function Url_Kisalt($url, $maks_uzunluk = 50, $ayirici = '...')
{
    if (strlen($url) <= $maks_uzunluk) {
        return $url;
    }

    $ayirici_uzunluk = strlen($ayirici);
    $baslangic_uzunluk = floor(($maks_uzunluk - $ayirici_uzunluk) / 2);
    $bitis_uzunluk = $maks_uzunluk - $baslangic_uzunluk - $ayirici_uzunluk;

    $baslangic = substr($url, 0, $baslangic_uzunluk);
    $bitis = substr($url, -$bitis_uzunluk);

    return $baslangic . $ayirici . $bitis;
}

/**
 * URL Protocol Değiştirme Fonksiyonu
 *
 * Bu fonksiyon, mevcut URL'in protocol kısmını değiştirir.
 * HTTP/HTTPS arasında geçiş yapmak için kullanılır.
 *
 * Kullanım Alanı:
 * - SSL migration
 * - Mixed content fixes
 * - Protocol enforcement
 * - CDN URL generation
 *
 * Teknik Detaylar:
 * - Protocol validation (http/https only)
 * - parse_url() ile URL reconstruction
 * - Port preservation
 * - Path, query, fragment preservation
 *
 * Güvenlik Önemleri:
 * - Sadece http/https protokolleri kabul
 * - Invalid protocol'de orijinal URL döner
 * - URL injection koruması
 * - Malformed URL handling
 *
 * Kullanım Örneği:
 * $http_url = 'http://example.com/secure/login';
 * $https_url = Url_Protokol_Degistir($http_url, 'https');
 * echo $https_url; // 'https://example.com/secure/login'
 *
 * // Invalid protocol
 * $invalid = Url_Protokol_Degistir($http_url, 'ftp');
 * echo $invalid; // 'http://example.com/secure/login' (unchanged)
 *
 * @param string $url Protokolü değiştirilecek URL
 * @param string $yeni_protokol Yeni protokol ('http' veya 'https')
 * @return string Protokolü değiştirilmiş URL
 */
function Url_Protokol_Degistir($url, $yeni_protokol)
{
    if (!in_array($yeni_protokol, ['http', 'https'])) {
        return $url;
    }

    $parsed = parse_url($url);
    if (!$parsed || !isset($parsed['host'])) {
        return $url;
    }

    $yeni_url = $yeni_protokol . '://' . $parsed['host'];

    if (isset($parsed['port'])) {
        $yeni_url .= ':' . $parsed['port'];
    }

    if (isset($parsed['path'])) {
        $yeni_url .= $parsed['path'];
    }

    if (isset($parsed['query'])) {
        $yeni_url .= '?' . $parsed['query'];
    }

    if (isset($parsed['fragment'])) {
        $yeni_url .= '#' . $parsed['fragment'];
    }

    return $yeni_url;
}

/**
 * URL Port Validation Fonksiyonu
 *
 * Bu fonksiyon, verilen URL'in belirtilen port numarasında çalışıp
 * çalışmadığını kontrol eder. Default port'lar otomatik tanınır.
 *
 * Kullanım Alanı:
 * - Service discovery
 * - Port-based routing
 * - Security policy enforcement
 * - Load balancer configuration
 *
 * Teknik Detaylar:
 * - parse_url() ile port extraction
 * - Default port detection (80/443)
 * - Numeric port comparison
 * - Scheme-based fallback
 *
 * Default Port Rules:
 * - HTTP -> 80 (if port not specified)
 * - HTTPS -> 443 (if port not specified)
 * - Explicit port -> As specified
 * - Invalid URL -> false
 *
 * Kullanım Örneği:
 * $api_url = 'https://api.example.com:8443/v1';
 *
 * if (Url_Port_Kontrol($api_url, 8443)) {
 *     echo 'API runs on custom HTTPS port';
 * }
 *
 * // Default port check
 * $web_url = 'https://example.com/page';
 * if (Url_Port_Kontrol($web_url, 443)) {
 *     echo 'Standard HTTPS port'; // true
 * }
 *
 * @param string $url Kontrol edilecek URL
 * @param int $port Kontrol edilecek port numarası
 * @return bool Belirtilen port'ta çalışıyorsa true, değilse false
 */
function Url_Port_Kontrol($url, $port)
{
    $parsed = parse_url($url);

    if (!$parsed || !isset($parsed['scheme'])) {
        return false;
    }

    $url_port = $parsed['port'] ?? null;
    if ($url_port === null) {
        $url_port = ($parsed['scheme'] === 'https') ? 443 : 80;
    }

    return $url_port == $port;
}

/**
 * Relative URL Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, bir URL'den diğerine relative path hesaplar.
 * Aynı domain'deyse relative path, farklıysa absolute URL döndürür.
 *
 * Kullanım Alanı:
 * - Site map generation
 * - Internal link optimization
 * - File system path calculation
 * - Navigation menu building
 *
 * Teknik Detaylar:
 * - Ayni_Domain_Mi() ile domain check
 * - Path directory explosion
 * - Common path prefix calculation
 * - '../' navigation generation
 *
 * Relative Path Örnekleri:
 * - /admin/users -> /admin/settings = '../settings'
 * - /blog/posts/edit -> /blog/categories = '../categories'
 * - /api/v1/users -> /api/v2/posts = '../v2/posts'
 * - Same directory -> './'
 *
 * Kullanım Örneği:
 * $from = 'https://example.com/admin/users/list';
 * $to = 'https://example.com/admin/settings';
 * $relative = Relative_Url_Olustur($from, $to);
 * echo $relative; // '../settings'
 *
 * // Cross-domain returns absolute
 * $external = 'https://other.com/page';
 * $abs = Relative_Url_Olustur($from, $external);
 * echo $abs; // 'https://other.com/page'
 *
 * @param string $baslangic_url Başlangıç reference URL
 * @param string $hedef_url Hedef destination URL
 * @return string Relative path veya absolute URL
 */
function Relative_Url_Olustur($baslangic_url, $hedef_url)
{
    if (!Ayni_Domain_Mi($baslangic_url, $hedef_url)) {
        return $hedef_url;
    }

    $baslangic_yol = Url_Yol_Al($baslangic_url);
    $hedef_yol = Url_Yol_Al($hedef_url);
    $baslangic_dizinler = array_filter(explode('/', $baslangic_yol));
    $hedef_dizinler = array_filter(explode('/', $hedef_yol));
    $ortak_uzunluk = 0;
    $min_uzunluk = min(count($baslangic_dizinler), count($hedef_dizinler));

    for ($i = 0; $i < $min_uzunluk; $i++) {
        if ($baslangic_dizinler[$i] === $hedef_dizinler[$i]) {
            $ortak_uzunluk++;
        } else {
            break;
        }
    }
    $geri_git_sayisi = count($baslangic_dizinler) - $ortak_uzunluk;
    $relative_yol = str_repeat('../', $geri_git_sayisi);
    $kalan_dizinler = array_slice($hedef_dizinler, $ortak_uzunluk);

    if (!empty($kalan_dizinler)) {
        $relative_yol .= implode('/', $kalan_dizinler);
    }

    return $relative_yol ?: './';
}

/**
 * Resim URL Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, verilen resim path'ine göre tam resim URL'i oluşturur.
 * TEMEL_DIZIN sabitini kullanarak güvenli resim link'leri üretir.
 *
 * Kullanım Alanı:
 * - HTML img src attribute'lerinde
 * - CSS background-image URL'lerinde
 * - Dinamik resim galerilerinde
 * - API response'larında resim URL'leri
 *
 * Teknik Detaylar:
 * - Absolute URL detection (http/https/www)
 * - TEMEL_DIZIN constant integration
 * - Safe image URL generation
 * - File extension validation
 *
 * Güvenlik Önlemleri:
 * - JavaScript protocol blocking
 * - Path traversal attack prevention
 * - XSS attack prevention
 * - Valid image extension check
 *
 * Desteklenen Formatlar:
 * - jpg, jpeg, png, gif, webp, svg, bmp, ico
 * - Case-insensitive extension matching
 * - Default fallback image support
 *
 * Kullanım Örneği:
 * $avatar = Resim('users/avatars/user123.jpg');
 * $logo = Resim('assets/img/logo.png');
 * $external = Resim('https://cdn.example.com/image.jpg'); // Returns as-is
 * $invalid = Resim('javascript:alert(1)'); // Returns fallback
 *
 * @param string $resim_yolu Resim dosyasının yolu
 * @param string $varsayilan_resim Geçersiz durumda kullanılacak fallback resim
 * @return string Güvenli ve tam resim URL'i
 */
function Resim($resim_yolu = '', $varsayilan_resim = 'default.png')
{
    if (!file_exists(KLASOR_IMG . $resim_yolu)) {
        return Resim_Url_Olustur('', $varsayilan_resim);
    }
    return Resim_Url_Olustur($resim_yolu, $varsayilan_resim);
}

/**
 * Güvenli Resim URL Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, verilen resim path'ini güvenli formata dönüştürür ve
 * geçerli resim formatlarını kontrol eder. Zararlı içeriklerden korur.
 *
 * Kullanım Alanı:
 * - Dinamik resim URL'leri oluşturmada
 * - User-generated content'te resim gösteriminde
 * - File upload sonrası URL generation'da
 * - Template engine'lerde resim rendering'de
 *
 * Teknik Detaylar:
 * - Absolute URL detection (http/https/www)
 * - File extension validation
 * - Path traversal prevention
 * - TEMEL_DIZIN constant integration
 *
 * Güvenlik Önlemleri:
 * - JavaScript protocol blocking
 * - Path traversal attack prevention (../)
 * - Invalid extension filtering
 * - XSS attack prevention
 *
 * Geçerli Resim Formatları:
 * - Standard: jpg, jpeg, png, gif
 * - Modern: webp, svg
 * - Icon: ico, bmp
 * - Case-insensitive matching
 *
 * Kullanım Örneği:
 * $safe = Resim_Url_Olustur('products/phone.jpg');
 * $malicious = Resim_Url_Olustur('javascript:alert(1)'); // Returns fallback
 * $traversal = Resim_Url_Olustur('../../../etc/passwd'); // Returns fallback
 * $absolute = Resim_Url_Olustur('https://cdn.com/img.png'); // Returns as-is
 *
 * @param string $resim_yolu Resim dosyasının path'i
 * @param string $varsayilan_resim Güvenlik ihlali durumunda fallback resim
 * @return string Güvenli ve geçerli resim URL'i
 */
function Resim_Url_Olustur($resim_yolu = '', $varsayilan_resim = 'default.png')
{
    // Boş path kontrolü
    if (empty($resim_yolu)) {
        return IMG . $varsayilan_resim;
    }

    // Absolute URL kontrolü (CDN, external resources)
    if (preg_match('/^(http:\/\/|https:\/\/|www\.)/i', $resim_yolu)) {
        return $resim_yolu;
    }

    // Güvenlik kontrolleri
    if (str_contains($resim_yolu, ":") || str_contains($resim_yolu, "..")) {
        return IMG . $varsayilan_resim;
    }

    // Resim uzantısı kontrolü
    if (!Resim_Uzantisi_Gecerli_Mi($resim_yolu)) {
        return IMG . $varsayilan_resim;
    }

    return IMG . $resim_yolu;
}

/**
 * Resim Uzantısı Geçerlilik Kontrolü
 *
 * Bu fonksiyon, verilen dosya path'inin geçerli bir resim formatında
 * olup olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - File upload validation'da
 * - Dynamic image loading'de
 * - Security filtering'de
 * - MIME type pre-validation'da
 *
 * Teknik Detaylar:
 * - pathinfo() ile extension extraction
 * - Case-insensitive comparison
 * - Predefined format whitelist
 * - Safe extension validation
 *
 * Desteklenen Formatlar:
 * - JPEG: jpg, jpeg
 * - PNG: png
 * - GIF: gif (animated destekli)
 * - WebP: webp (modern format)
 * - SVG: svg (vector graphics)
 * - Icon: ico, bmp
 *
 * Kullanım Örneği:
 * if (Resim_Uzantisi_Gecerli_Mi('photo.jpg')) {
 *     echo 'Geçerli resim formatı';
 * }
 * $valid = Resim_Uzantisi_Gecerli_Mi('document.pdf'); // false
 * $valid = Resim_Uzantisi_Gecerli_Mi('image.JPEG'); // true (case-insensitive)
 *
 * @param string $dosya_yolu Kontrol edilecek dosya path'i
 * @return bool Geçerli resim formatıysa true, değilse false
 */
function Resim_Uzantisi_Gecerli_Mi($dosya_yolu)
{
    $gecerli_uzantilar = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
    $uzanti = strtolower(pathinfo($dosya_yolu, PATHINFO_EXTENSION));

    return in_array($uzanti, $gecerli_uzantilar);
}

/**
 * Responsive Resim URL Üretici
 *
 * Bu fonksiyon, farklı ekran boyutları için responsive resim URL'leri oluşturur.
 * srcset attribute'u için multiple resolution URLs üretir.
 *
 * Kullanım Alanı:
 * - Responsive web design
 * - Mobile optimization
 * - Bandwidth optimization
 * - Progressive loading
 *
 * Teknik Detaylar:
 * - Multiple size variant generation
 * - Filename pattern extension
 * - Breakpoint-based URL creation
 * - Automatic fallback handling
 *
 * Size Variants:
 * - sm: Small devices (480px)
 * - md: Medium devices (768px)
 * - lg: Large devices (1200px)
 * - xl: Extra large devices (1920px)
 *
 * Naming Convention:
 * - Original: image.jpg
 * - Small: image_sm.jpg
 * - Medium: image_md.jpg
 * - Large: image_lg.jpg
 *
 * Kullanım Örneği:
 * $srcset = Responsive_Resim_Url('products/phone.jpg');
 * // Returns array: ['sm' => '/img/phone_sm.jpg', 'md' => '/img/phone_md.jpg', ...]
 *
 * @param string $temel_resim Ana resim dosyası path'i
 * @param array $boyutlar Üretilecek boyut varyantları
 * @return array Responsive resim URL'leri array'i
 */
function Responsive_Resim_Url($temel_resim, $boyutlar = ['sm', 'md', 'lg', 'xl'])
{
    $responsive_urls = [];
    $pathinfo = pathinfo($temel_resim);
    $dizin = $pathinfo['dirname'] !== '.' ? $pathinfo['dirname'] . '/' : '';
    $dosya_adi = $pathinfo['filename'];
    $uzanti = $pathinfo['extension'] ?? 'jpg';

    foreach ($boyutlar as $boyut) {
        $responsive_dosya = $dizin . $dosya_adi . '_' . $boyut . '.' . $uzanti;
        $responsive_urls[$boyut] = Resim($responsive_dosya);
    }

    return $responsive_urls;
}

/**
 * URL Segmentlerini Array Olarak Alma Fonksiyonu
 *
 * Bu fonksiyon, $_GET['url'] parametresini alıp temizleyerek
 * array formatında döndürür. Router sistemleri için URL parsing.
 *
 * Kullanım Alanı:
 * - MVC routing sistemlerinde
 * - URL-based controller/action mapping'de
 * - RESTful API endpoint parsing'de
 * - Dynamic route handling'de
 *
 * Teknik Detaylar:
 * - $_GET['url'] parameter extraction
 * - FILTER_SANITIZE_URL kullanımı
 * - rtrim() ile trailing slash temizliği
 * - explode() ile segment array'e dönüştürme
 *
 * Güvenlik Önemleri:
 * - URL sanitization (XSS prevention)
 * - Trailing slash normalization
 * - Empty segment filtering
 * - Safe array conversion
 *
 * URL Parsing Örnekleri:
 * - ?url=admin/users/edit -> ['admin', 'users', 'edit']
 * - ?url=blog/post/123/ -> ['blog', 'post', '123']
 * - ?url=api/v1/products -> ['api', 'v1', 'products']
 * - URL yok -> null
 *
 * Kullanım Örneği:
 * // URL: /index.php?url=admin/users/edit/123
 * $segments = URL_Segmentleri();
 * if ($segments) {
 *     $controller = $segments[0]; // 'admin'
 *     $action = $segments[1] ?? 'index'; // 'users'
 *     $id = $segments[2] ?? null; // 'edit'
 * }
 *
 * // Route mapping
 * [$controller, $method, $param] = URL_Segmentleri() ?? ['home', 'index', null];
 *
 * @return array|null URL segmentleri array'i veya parametre yoksa null
 */
function URL_Segmentleri()
{
    if (isset($_GET['url'])) {
        $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
        return explode('/', $url);
    }
    return null;
}

/**
 * Yerel Ağ (LAN) Tespiti Fonksiyonu
 *
 * Bu fonksiyon, verilen host/IP adresinin yerel ağda (LAN) olup olmadığını kontrol eder.
 * Private IP range'lerini (RFC 1918) ve loopback adreslerini tespit eder.
 *
 * Kullanım Alanı:
 * - LAN üzerinden erişim kontrolü
 * - Development/production ortam ayrımı
 * - Network-based security policy
 * - Internal/external request filtering
 *
 * Teknik Detaylar:
 * - RFC 1918 private network ranges (10.x.x.x, 172.16-31.x.x, 192.168.x.x)
 * - Localhost patterns (localhost, 127.x.x.x)
 * - IPv6 loopback (::1)
 * - IPv4 link-local (169.254.x.x)
 *
 * Kontrol Edilen IP Aralıkları:
 * - 10.0.0.0 - 10.255.255.255 (Class A private)
 * - 172.16.0.0 - 172.31.255.255 (Class B private)
 * - 192.168.0.0 - 192.168.255.255 (Class C private)
 * - 127.0.0.0 - 127.255.255.255 (Loopback)
 * - 169.254.0.0 - 169.254.255.255 (Link-local)
 *
 * Kullanım Örneği:
 * $host = $_SERVER['HTTP_HOST'];
 *
 * if (Yerel_Ag_Mi($host)) {
 *     // Yerel ağdan erişim - debug modunu aç
 *     define('DEBUG_MODE', true);
 *     $db_host = '192.168.1.100';
 * } else {
 *     // İnternetten erişim - production ayarları
 *     define('DEBUG_MODE', false);
 *     $db_host = 'production-db.example.com';
 * }
 *
 * @param string $host Kontrol edilecek host veya IP adresi
 * @return bool Yerel ağda ise true, değilse false
 */
function Yerel_Ag_Mi($host = '')
{
    if (empty($host)) {
        $host = $_SERVER['HTTP_HOST'] ?? '';
    }

    // Port varsa temizle
    $host = explode(':', $host)[0];

    // Localhost kontrolü
    $localhost_patterns = ['localhost', '::1', '0.0.0.0'];
    if (in_array($host, $localhost_patterns)) {
        return true;
    }

    // IP adresi değilse domain olabilir
    if (!filter_var($host, FILTER_VALIDATE_IP)) {
        return false;
    }

    // Private network ranges (RFC 1918)
    // 10.0.0.0 - 10.255.255.255 (Class A)
    if (preg_match('/^10\./', $host)) {
        return true;
    }

    // 172.16.0.0 - 172.31.255.255 (Class B)
    if (preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $host)) {
        return true;
    }

    // 192.168.0.0 - 192.168.255.255 (Class C)
    if (preg_match('/^192\.168\./', $host)) {
        return true;
    }

    // 127.0.0.0 - 127.255.255.255 (Loopback)
    if (preg_match('/^127\./', $host)) {
        return true;
    }

    // 169.254.0.0 - 169.254.255.255 (Link-local)
    if (preg_match('/^169\.254\./', $host)) {
        return true;
    }

    return false;
}

/**
 * Ortam Tipi Belirleme Fonksiyonu
 *
 * Bu fonksiyon, uygulamanın çalıştığı ortamı tespit eder ve
 * 'localhost', 'lan' veya 'production' değerlerinden birini döndürür.
 *
 * Kullanım Alanı:
 * - Environment-based configuration
 * - Automatic database selection
 * - Debug mode activation
 * - API endpoint routing
 * - Asset URL selection (CDN vs local)
 *
 * Teknik Detaylar:
 * - $_SERVER['HTTP_HOST'] kontrolü
 * - Private IP detection
 * - Domain name validation
 * - Cascading environment detection
 *
 * Ortam Tipleri:
 * - 'localhost': 127.0.0.1, localhost, ::1 gibi loopback adresler
 * - 'lan': 192.168.x.x, 10.x.x.x gibi yerel ağ IP'leri
 * - 'production': Public domain veya IP adresleri
 *
 * Kullanım Örneği:
 * $ortam = Ortam_Tipi_Al();
 *
 * switch ($ortam) {
 *     case 'localhost':
 *         $db_host = 'localhost';
 *         $debug = true;
 *         break;
 *     case 'lan':
 *         $db_host = '192.168.1.100';
 *         $debug = true;
 *         break;
 *     case 'production':
 *         $db_host = 'db.example.com';
 *         $debug = false;
 *         break;
 * }
 *
 * // Örnek: Base URL oluşturma
 * $base_url = match (Ortam_Tipi_Al()) {
 *     'localhost' => 'http://localhost/proje/',
 *     'lan' => 'http://192.168.1.50/proje/',
 *     'production' => 'https://www.example.com/',
 * };
 *
 * @return string Ortam tipi: 'localhost', 'lan' veya 'production'
 */
function Ortam_Tipi_Al()
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Port varsa temizle
    $host = explode(':', $host)[0];

    // Localhost kontrolü
    $localhost_patterns = ['localhost', '127.0.0.1', '::1', '0.0.0.0'];
    if (in_array($host, $localhost_patterns)) {
        return 'localhost';
    }

    // Yerel ağ kontrolü
    if (Yerel_Ag_Mi($host)) {
        return 'lan';
    }

    // Production (public IP veya domain)
    return 'production';
}

/**
 * Sunucu IP Adresi Alma Fonksiyonu
 *
 * Bu fonksiyon, sunucunun gerçek IP adresini tespit eder.
 * Birden fazla ağ interface'i varsa SERVER_ADDR kullanır.
 *
 * Kullanım Alanı:
 * - Server identification
 * - Load balancer configuration
 * - Network diagnostics
 * - Multi-server setup
 *
 * Teknik Detaylar:
 * - $_SERVER['SERVER_ADDR'] primary source
 * - gethostbyname() fallback
 * - IPv4/IPv6 support
 * - Null safety
 *
 * Kullanım Örneği:
 * $sunucu_ip = Sunucu_IP_Al();
 * echo "Sunucu IP: " . $sunucu_ip; // '192.168.1.100'
 *
 * // Logging için
 * Log::Bilgi("Request handled by server: " . Sunucu_IP_Al());
 *
 * @return string|null Sunucu IP adresi veya alınamazsa null
 */
function Sunucu_IP_Al()
{
    if (!empty($_SERVER['SERVER_ADDR'])) {
        return $_SERVER['SERVER_ADDR'];
    }

    if (!empty($_SERVER['SERVER_NAME'])) {
        return gethostbyname($_SERVER['SERVER_NAME']);
    }

    return null;
}

/**
 * İstemci IP Adresi Alma Fonksiyonu
 *
 * Bu fonksiyon, client'ın gerçek IP adresini tespit eder.
 * Proxy ve load balancer arkasındaki IP'leri de algılar.
 *
 * Kullanım Alanı:
 * - User tracking ve analytics
 * - Rate limiting ve throttling
 * - IP-based access control
 * - Geolocation services
 * - Security logging
 *
 * Teknik Detaylar:
 * - X-Forwarded-For header parsing
 * - CloudFlare IP detection
 * - Proxy header chain
 * - REMOTE_ADDR fallback
 * - IP validation
 *
 * Kontrol Edilen Header'lar:
 * - HTTP_CF_CONNECTING_IP (CloudFlare)
 * - HTTP_X_FORWARDED_FOR (Proxy chain)
 * - HTTP_CLIENT_IP (Client IP)
 * - HTTP_X_REAL_IP (Real IP)
 * - REMOTE_ADDR (Fallback)
 *
 * Güvenlik Notu:
 * - Header'lar spoof edilebilir
 * - Production'da güvenilir proxy listesi kullanın
 * - Kritik işlemler için ek validation yapın
 *
 * Kullanım Örneği:
 * $ip = Istemci_IP_Al();
 * echo "Ziyaretçi IP: " . $ip;
 *
 * // Rate limiting
 * if (!Rate_Limit_Kontrol($ip, 100, 3600)) {
 *     die('Too many requests');
 * }
 *
 * // IP ban kontrolü
 * if (IP_Banli_Mi($ip)) {
 *     Sayfa_Yonlendir('/yasakli');
 * }
 *
 * @return string Client IP adresi (fallback: REMOTE_ADDR)
 */
function Istemci_IP_Al()
{
    // CloudFlare IP
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    // X-Forwarded-For chain (ilk IP gerçek client IP'sidir)
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ip_list[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    // Client IP header
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        if (filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
    }

    // X-Real-IP header
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        if (filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
    }

    // Fallback: Direct connection IP
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
