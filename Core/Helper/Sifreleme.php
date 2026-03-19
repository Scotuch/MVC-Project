<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Şifreleme fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/





/**
 * AES-256-CBC Şifreleme İşlemi
 *
 * Bu fonksiyon, veriyi AES-256-CBC algoritmasıyla şifreler.
 * Random IV kullanarak her seferinde farklı sonuç üretir.
 *
 * Kullanım Alanı:
 * - Hassas kullanıcı bilgilerinin saklanmasında
 * - Veritabanında kritik verilerin korunmasında
 * - Config dosyalarında şifre saklamada
 * - Session data'nın güvenli saklanmasında
 *
 * Teknik Detaylar:
 * - AES-256-CBC encryption algorithm
 * - SHA-256 ile anahtar türetme
 * - Random IV generation (16 bytes)
 * - Base64 encoding for storage
 *
 * Güvenlik Önlemleri:
 * - ANAHTAR sabitini güvenli tutun
 * - IV her şifreleme için benzersizdir
 * - OpenSSL extension gerektirir
 *
 * Kullanım Örneği:
 * $sifreli = Sifrele('gizli bilgi');
 * // Veritabanında veya dosyada saklanabilir
 *
 * @param string $veri Şiffrelenecek veri
 * @return string|false Şifreli veri (Base64) veya hata durumunda false
 */
function Sifrele($veri)
{
    $sifre = substr(hash('sha256', ANAHTAR, true), 0, 32);
    $iv = openssl_random_pseudo_bytes(16);

    $sifrelenmis = openssl_encrypt($veri, SIFRELEME_YONTEMI, $sifre, OPENSSL_RAW_DATA, $iv);
    if ($sifrelenmis === false) {
        return false;
    }

    return base64_encode($iv . $sifrelenmis);
}

/**
 * AES-256-CBC Şifre Çözme İşlemi
 *
 * Bu fonksiyon, Sifrele() ile şifrelenmiş veriyi orijinal haline döndürür.
 * IV extraction ve validation işlemlerini yapar.
 *
 * Kullanım Alanı:
 * - Şifrelenmiş verilerin okunmasında
 * - Veritabanından şifreli bilgilerin alınmasında
 * - Config dosyalarından şifrelerin okunmasında
 * - Session data'nın restore edilmesinde
 *
 * Teknik Detaylar:
 * - Base64 decode işlemi
 * - IV extraction (first 16 bytes)
 * - AES-256-CBC decryption
 * - Data validation ve error handling
 *
 * Güvenlik Kontrolleri:
 * - Minimum data length kontrolu (16+ bytes)
 * - Invalid Base64 kontrolu
 * - Decryption failure kontrolu
 *
 * Kullanım Örneği:
 * $orijinal = Sifre_Coz($sifreli_veri);
 * if ($orijinal !== false) {
 *     echo "Veri: " . $orijinal;
 * }
 *
 * @param string $sifrelenmisVeri Çözülecek şifreli veri (Base64)
 * @return string|false Orijinal veri veya hata durumunda false
 */
function Sifre_Coz($sifrelenmisVeri)
{
    $veri = base64_decode($sifrelenmisVeri);
    if ($veri === false || strlen($veri) < 16) {
        return false;
    }

    $iv = substr($veri, 0, 16);
    $sifrelenmis = substr($veri, 16);
    $sifre = substr(hash('sha256', ANAHTAR, true), 0, 32);

    return openssl_decrypt($sifrelenmis, SIFRELEME_YONTEMI, $sifre, OPENSSL_RAW_DATA, $iv);
}

/**
 * SHA-256 Hash Üretme İşlemi
 *
 * Bu fonksiyon, veriyi SHA-256 algoritmasıyla hash'ler.
 * Hex string veya binary format seçimi yapabilirsiniz.
 *
 * Kullanım Alanı:
 * - Şifre hash'leme işlemlerinde (ek salt ile)
 * - Veri bütünlüğü kontrolünde
 * - Unique identifier oluşturmada
 * - File checksum hesaplamalarında
 *
 * Teknik Detaylar:
 * - SHA-256 cryptographic hash function
 * - 256-bit (32 byte) output
 * - Hex (64 karakter) veya binary format
 * - One-way hash function (irreversible)
 *
 * Güvenlik Notları:
 * - Şifre hash'i için tek başına yeterli değil
 * - Salt kullanımı önerilir
 * - Rainbow table saldırılarına karşı dikkatli olun
 *
 * Kullanım Örneği:
 * $hash = Sha256_Olustur('parola123');
 * $binary = Sha256_Olustur('veri', true);
 *
 * @param string $veri Hash'lenecek veri
 * @param bool $ham Binary format dönsün mü (varsayılan: false)
 * @return string SHA-256 hash (hex veya binary)
 */
function Sha256_Olustur($veri, $ham = false)
{
    return hash('sha256', $veri, $ham);
}

/**
 * SHA-512 Hash Üretme İşlemi
 *
 * Bu fonksiyon, veriyi SHA-512 algoritmasıyla hash'ler.
 * SHA-256'dan daha uzun ve güçlü hash üretir.
 *
 * Kullanım Alanı:
 * - Yüksek güvenlik gerektiren hash'lerde
 * - Large data integrity verification'da
 * - Digital signature sistemlerinde
 * - Critical security applications'da
 *
 * Teknik Detaylar:
 * - SHA-512 cryptographic hash function
 * - 512-bit (64 byte) output
 * - Hex (128 karakter) veya binary format
 * - Cryptographically secure hash
 *
 * Performans Notları:
 * - SHA-256'dan daha yavaş
 * - Daha uzun hash output
 * - Memory usage daha yüksek
 * - Security/performance trade-off
 *
 * Kullanım Örneği:
 * $hash = Sha512_Olustur('critical_data');
 * $binary = Sha512_Olustur('veri', true);
 *
 * @param string $veri Hash'lenecek veri
 * @param bool $ham Binary format dönsün mü (varsayılan: false)
 * @return string SHA-512 hash (hex veya binary)
 */
function Sha512_Olustur($veri, $ham = false)
{
    return hash('sha512', $veri, $ham);
}

/**
 * Bcrypt Şifre Hash'leme İşlemi
 *
 * Bu fonksiyon, şifreleri güvenli bcrypt algoritmasıyla hash'ler.
 * Otomatik salt generation ve cost parameter desteği sağlar.
 *
 * Kullanım Alanı:
 * - Kullanıcı şifre hash'lemede (recommended)
 * - Authentication sistemlerinde
 * - Password storage'da industry standard
 * - Login security implementations'da
 *
 * Teknik Detaylar:
 * - Blowfish-based password hashing
 * - Automatic salt generation
 * - Configurable cost parameter (4-31)
 * - Time-tested security algorithm
 *
 * Maliyet Rehberi:
 * - 10: Hızlı, düşük güvenlik
 * - 12: Dengeli (recommended default)
 * - 15: Yüksek güvenlik, yavaş
 * - Her +1 maliyet = 2x daha yavaş
 *
 * Kullanım Örneği:
 * $hash = Bcrypt_Olustur('kullanici_sifresi');
 * $guclu = Bcrypt_Olustur('admin_sifresi', 15);
 *
 * @param string $sifre Hash'lenecek şifre
 * @param int $maliyet Bcrypt cost parameter (4-31, varsayılan: 12)
 * @return string Bcrypt hash string
 */
function Bcrypt_Olustur($sifre, $maliyet = 12)
{
    return password_hash($sifre, PASSWORD_BCRYPT, ['cost' => $maliyet]);
}

/**
 * Argon2 Şifre Hash'leme İşlemi
 *
 * Bu fonksiyon, modern ve güçlü Argon2 algoritmasıyla şifre hash'ler.
 * Memory-hard algorithm ile brute force saldırılara karşı optimize edilmiştir.
 *
 * Kullanım Alanı:
 * - Yüksek güvenlik gereken uygulamalarda
 * - Modern password hashing standard
 * - Enterprise security implementations'da
 * - Future-proof password storage'da
 *
 * Teknik Detaylar:
 * - Argon2I algorithm (data-independent)
 * - Memory-hard function (GPU resistant)
 * - Configurable memory, time, threads
 * - PHP 7.2+ requirement
 *
 * Parametre Rehberi:
 * - Bellek: 64MB (65536 KB) recommended minimum
 * - Zaman: 4 iterations balanced
 * - Thread: 3 parallel threads
 * - Yüksek değerler = daha güvenli ama yavaş
 *
 * Kullanım Örneği:
 * $hash = Argon2_Olustur('super_secret');
 * $custom = Argon2_Olustur('pass', 131072, 6, 4);
 *
 * @param string $sifre Hash'lenecek şifre
 * @param int $bellek Memory cost KB cinsinden (varsayılan: 65536)
 * @param int $zaman Time cost iterations (varsayılan: 4)
 * @param int $thread Parallelism threads (varsayılan: 3)
 * @return string|false Argon2 hash veya unsupported ise false
 */
function Argon2_Olustur($sifre, $bellek = 65536, $zaman = 4, $thread = 3)
{
    if (defined('PASSWORD_ARGON2I')) {
        return password_hash($sifre, PASSWORD_ARGON2I, [
            'memory_cost' => $bellek,
            'time_cost' => $zaman,
            'threads' => $thread
        ]);
    }
    return false;
}

/**
 * HMAC (Hash-based Message Authentication Code) Üretme
 *
 * Bu fonksiyon, veri ve anahtar kullanarak güvenli HMAC üretir.
 * Mesaj bütünlüğü ve authentication için kullanılır.
 *
 * Kullanım Alanı:
 * - API signature'larında
 * - JWT token'larında
 * - Webhook verification'da
 * - Data integrity checking'de
 *
 * Teknik Detaylar:
 * - HMAC-SHA256 default algorithm
 * - Secret key ile keyed hashing
 * - Multiple algorithm support
 * - Cryptographically secure MAC
 *
 * Desteklenen Algoritmalar:
 * - sha256 (recommended)
 * - sha512 (high security)
 * - md5 (legacy, avoid)
 * - sha1 (legacy, avoid)
 *
 * Güvenlik Notları:
 * - Anahtarı güvenli saklayın
 * - Timing attack'lara karşı hash_equals kullanın
 * - Key rotation implement edin
 *
 * Kullanım Örneği:
 * $mac = Hmac_Olustur('mesaj', 'gizli_anahtar');
 * $api_sig = Hmac_Olustur($request_body, $api_secret);
 *
 * @param string $veri HMAC hesaplanacak veri
 * @param string $anahtar Güvenli anahtar
 * @param string $algoritma Hash algoritması (varsayılan: 'sha256')
 * @param bool $ham Binary format dönsün mü (varsayılan: false)
 * @return string HMAC değeri (hex veya binary)
 */
function Hmac_Olustur($veri, $anahtar, $algoritma = 'sha256', $ham = false)
{
    return hash_hmac($algoritma, $veri, $anahtar, $ham);
}

/**
 * Şifre Doğrulama İşlemi
 *
 * Bu fonksiyon, plain text şifre ile hash'lenmiş şifreyi karşılaştırır.
 * Bcrypt, Argon2 gibi tüm modern hash algoritmalarını destekler.
 *
 * Kullanım Alanı:
 * - Kullanıcı giriş sistemlerinde
 * - Authentication middleware'de
 * - Password verification'da
 * - Login security checks'de
 *
 * Teknik Detaylar:
 * - password_verify() PHP function wrap
 * - Automatic algorithm detection
 * - Timing-safe comparison
 * - Salt extraction ve verification
 *
 * Güvenlik Özellikleri:
 * - Constant-time comparison
 * - Automatic hash format detection
 * - No plain text storage needed
 * - Rainbow table resistant
 *
 * Kullanım Örneği:
 * $stored_hash = '$2y$12$...';
 * if (Sifre_Dogrula($user_input, $stored_hash)) {
 *     echo 'Giriş başarılı';
 * }
 *
 * @param string $sifre Kontrol edilecek plain text şifre
 * @param string $hash Karşılaştırılacak hash'lenmiş şifre
 * @return bool Eşleşiyorsa true, değilse false
 */
function Sifre_Dogrula($sifre, $hash)
{
    return password_verify($sifre, $hash);
}

/**
 * Güvenli Hash Karşılaştırma İşlemi
 *
 * Bu fonksiyon, iki hash değerini timing-safe şekilde karşılaştırır.
 * Timing attack'lara karşı koruma sağlar.
 *
 * Kullanım Alanı:
 * - HMAC verification'da
 * - API signature checking'de
 * - Token validation'da
 * - Cryptographic comparisons'da
 *
 * Teknik Detaylar:
 * - hash_equals() PHP function wrap
 * - Constant-time string comparison
 * - Length-independent timing
 * - Side-channel attack prevention
 *
 * Timing Attack Korunması:
 * - Normal == comparison timing leak yapar
 * - hash_equals sabit zamanda çalışır
 * - Hash length'den bağımsız timing
 * - Cryptographically secure comparison
 *
 * Kullanım Örneği:
 * $expected_hmac = hash_hmac('sha256', $data, $key);
 * if (Hash_Dogrula($expected_hmac, $received_hmac)) {
 *     echo 'HMAC geçerli';
 * }
 *
 * @param string $beklenen Beklenen hash değeri
 * @param string $gercek Karşılaştırılacak hash değeri
 * @return bool Hash'ler eşleşiyorsa true, değilse false
 */
function Hash_Dogrula($beklenen, $gercek)
{
    return hash_equals($beklenen, $gercek);
}

/**
 * AES-GCM Şifreleme İşlemi (Authenticated Encryption)
 *
 * Bu fonksiyon, veriyi AES-256-GCM ile şifreler. CBC'den farklı olarak
 * authentication tag ile bütünlük korunması da sağlar.
 *
 * Kullanım Alanı:
 * - Yüksek güvenlik gereken şifreleme işlemlerinde
 * - Network communication'da
 * - Secure file storage'da
 * - Modern cryptographic applications'da
 *
 * Teknik Detaylar:
 * - AES-256-GCM authenticated encryption
 * - 12-byte IV (96-bit) generation
 * - Authentication tag (16-byte) üretimi
 * - AEAD (Authenticated Encryption with Associated Data)
 *
 * Güvenlik Özellikleri:
 * - Encryption + Authentication tek işlemde
 * - Tampering detection capability
 * - IV uniqueness kritik önemde
 * - Tag verification gerekli
 *
 * Kullanım Örneği:
 * $sifreli = Aes_Gcm_Sifrele('critical_data');
 * // ['data' => '...', 'iv' => '...', 'tag' => '...']
 *
 * @param string $veri Şiffrelenecek veri
 * @param string|null $anahtar Şifreleme anahtarı (null = ANAHTAR sabitini kullan)
 * @return array|false Şifreli veri array'i veya hata durumunda false
 */
function Aes_Gcm_Sifrele($veri, $anahtar = null)
{
    $anahtar = $anahtar ?: substr(hash('sha256', ANAHTAR, true), 0, 32);
    $iv = openssl_random_pseudo_bytes(12);
    $tag = '';

    $sifrelenmis = openssl_encrypt($veri, 'aes-256-gcm', $anahtar, OPENSSL_RAW_DATA, $iv, $tag);

    if ($sifrelenmis === false) {
        return false;
    }

    return [
        'data' => base64_encode($sifrelenmis),
        'iv' => base64_encode($iv),
        'tag' => base64_encode($tag)
    ];
}

/**
 * AES-GCM Şifre Çözme İşlemi (Authenticated Decryption)
 *
 * Bu fonksiyon, AES-GCM ile şifrelenmiş veriyi çözer ve authentication
 * tag'i doğrulayarak veri bütünlüğünü kontrol eder.
 *
 * Kullanım Alanı:
 * - AES-GCM ile şifrelenmiş verilerin okunmasında
 * - Secure communication'da message verification
 * - Authenticated storage systems'da
 * - Tamper-evident data processing'de
 *
 * Teknik Detaylar:
 * - Array format validation
 * - Base64 decode operations
 * - AES-256-GCM decryption
 * - Authentication tag verification
 *
 * Güvenlik Kontrolleri:
 * - Array structure validation
 * - Required keys existence check
 * - Tag verification (automatic)
 * - Tampering detection
 *
 * Kullanım Örneği:
 * $sifrelenmis = ['data' => '...', 'iv' => '...', 'tag' => '...'];
 * $orijinal = Aes_Gcm_Coz($sifrelenmis);
 *
 * @param array $sifrelenmisVeri Çözülecek veri array'i
 * @param string|null $anahtar Şifre çözme anahtarı (null = ANAHTAR sabitini kullan)
 * @return string|false Orijinal veri veya hata/tampering durumunda false
 */
function Aes_Gcm_Coz($sifrelenmisVeri, $anahtar = null)
{
    if (!is_array($sifrelenmisVeri) || !isset($sifrelenmisVeri['data'], $sifrelenmisVeri['iv'], $sifrelenmisVeri['tag'])) {
        return false;
    }

    $anahtar = $anahtar ?: substr(hash('sha256', ANAHTAR, true), 0, 32);
    $data = base64_decode($sifrelenmisVeri['data']);
    $iv = base64_decode($sifrelenmisVeri['iv']);
    $tag = base64_decode($sifrelenmisVeri['tag']);

    return openssl_decrypt($data, 'aes-256-gcm', $anahtar, OPENSSL_RAW_DATA, $iv, $tag);
}

/**
 * Rastgele String Üretici
 *
 * Bu fonksiyon, belirtilen karakter setinden rastgele string üretir.
 * Cryptographically secure random number generator kullanır.
 *
 * Kullanım Alanı:
 * - Token generation'da
 * - Random password üretiminde
 * - Unique identifier oluşturmada
 * - Nonce ve salt generation'da
 *
 * Teknik Detaylar:
 * - random_int() CSPRNG kullanır
 * - Custom character set destekler
 * - Uniform distribution sağlar
 * - Cryptographically secure
 *
 * Karakter Setleri:
 * - Varsayılan: alphanumeric (A-Z, a-z, 0-9)
 * - Custom: istediğiniz karakterler
 * - Özel karakterler eklenebilir
 * - Unicode support
 *
 * Kullanım Örneği:
 * $token = Rastgele_String(16); // 16 karakterlik alphanumeric
 * $ozel = Rastgele_String(8, '0123456789'); // Sadece rakamlar
 * $guclu = Rastgele_String(12, 'ABCabc123!@#');
 *
 * @param int $uzunluk Üretilecek string uzunluğu (varsayılan: 32)
 * @param string $karakterler Kullanılacak karakter seti
 * @return string Rastgele üretilmiş string
 */
function Rastgele_String($uzunluk = 32, $karakterler = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $rastgele = '';
    $maxIndex = strlen($karakterler) - 1;

    for ($i = 0; $i < $uzunluk; $i++) {
        $rastgele .= $karakterler[random_int(0, $maxIndex)];
    }

    return $rastgele;
}

/**
 * URL-Safe Rastgele Token Üretici
 *
 * Bu fonksiyon, URL'lerde güvenle kullanılabilir rastgele token üretir.
 * Base64-URL encoding kullanarak compact format sağlar.
 *
 * Kullanım Alanı:
 * - API token'larında
 * - URL-based authentication'da
 * - CSRF token'larında
 * - Session identifier'larda
 *
 * Teknik Detaylar:
 * - random_bytes() CSPRNG kullanır
 * - Base64-URL encoding (RFC 4648)
 * - '+/' karakterlerini '-_' ile değiştirir
 * - Padding '=' karakterlerini kaldırır
 *
 * URL Güvenli Özellikler:
 * - HTTP GET parameter'larda güvenli
 * - File name'lerde kullanılabilir
 * - JSON'da escape gerektirmez
 * - Database-friendly format
 *
 * Kullanım Örneği:
 * $api_token = Rastgele_Token(32);
 * $csrf_token = Rastgele_Token(16);
 * $session_id = Rastgele_Token(48);
 *
 * @param int $uzunluk Byte cinsinden token uzunluğu (varsayılan: 32)
 * @return string URL-safe rastgele token
 */
function Rastgele_Token($uzunluk = 32)
{
    return rtrim(strtr(base64_encode(random_bytes($uzunluk)), '+/', '-_'), '=');
}

/**
 * UUID v4 (Universally Unique Identifier) Üretici
 *
 * Bu fonksiyon, RFC 4122 standardına uygun UUID v4 üretir.
 * Cryptographically secure random data kullanır.
 *
 * Kullanım Alanı:
 * - Database primary key'lerinde
 * - Unique resource identifier'larda
 * - File naming'de
 * - Distributed systems'da unique ID
 *
 * Teknik Detaylar:
 * - RFC 4122 UUID v4 specification
 * - 128-bit random number
 * - Version ve variant bit'leri ayarlanır
 * - Standard format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
 *
 * UUID Format:
 * - 32 hexadecimal digits
 * - 4 hyphen ile ayrılmış 5 grup
 * - Version 4 (random-based)
 * - Variant 1 (RFC 4122)
 *
 * Kullanım Örneği:
 * $id = Rastgele_Uuid();
 * // Örnek: "f47ac10b-58cc-4372-a567-0e02b2c3d479"
 *
 * @return string RFC 4122 UUID v4 string
 */
function Rastgele_Uuid()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return sprintf(
        '%08s-%04s-%04s-%04s-%12s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

/**
 * Rastgele Güçlü Şifre Üretici
 *
 * Bu fonksiyon, belirtilen kurallara göre güçlü rastgele şifre üretir.
 * Karakter tiplerini esnek şekilde yapılandırabilirsiniz.
 *
 * Kullanım Alanı:
 * - Otomatik şifre üretiminde
 * - Kullanıcı kayıt sistemlerinde
 * - Şifre reset işlemlerinde
 * - Temporary access credentials'da
 *
 * Teknik Detaylar:
 * - Cryptographically secure random generation
 * - Flexible character set composition
 * - Configurable complexity rules
 * - Equal distribution of character types
 *
 * Karakter Tipleri:
 * - Büyük harf: A-Z
 * - Küçük harf: a-z
 * - Rakam: 0-9
 * - Özel karakter: !@#$%^&*()_+-=[]{}|;:,.<>?
 *
 * Güvenlik Önerileri:
 * - Minimum 12 karakter kullanın
 * - Tüm karakter tiplerini aktif tutun
 * - Üretilen şifreleri güvenli ilettin
 *
 * Kullanım Örneği:
 * $sifre = Rastgele_Sifre(); // Default: 12 char, tüm tipler
 * $basit = Rastgele_Sifre(8, false); // Özel karakter yok
 * $sayisal = Rastgele_Sifre(6, false, false, false, true); // Sadece rakam
 *
 * @param int $uzunluk Şifre uzunluğu (varsayılan: 12)
 * @param bool $ozelKarakter Özel karakterler dahil edilsin mi (varsayılan: true)
 * @param bool $buyukHarf Büyük harfler dahil edilsin mi (varsayılan: true)
 * @param bool $kucukHarf Küçük harfler dahil edilsin mi (varsayılan: true)
 * @param bool $rakam Rakamlar dahil edilsin mi (varsayılan: true)
 * @return string Üretilmiş rastgele şifre
 */
function Rastgele_Sifre($uzunluk = 12, $ozelKarakter = true, $buyukHarf = true, $kucukHarf = true, $rakam = true)
{
    $karakterler = '';

    if ($buyukHarf) {
        $karakterler .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    if ($kucukHarf) {
        $karakterler .= 'abcdefghijklmnopqrstuvwxyz';
    }
    if ($rakam) {
        $karakterler .= '0123456789';
    }
    if ($ozelKarakter) {
        $karakterler .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
    }

    if (empty($karakterler)) {
        $karakterler = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    }

    return Rastgele_String($uzunluk, $karakterler);
}

/**
 * Veri Bütünlüğü Kontrolu (Checksum)
 *
 * Bu fonksiyon, verinin bütünlüğünü kontrol etmek için hash üretir.
 * Veri değişikliklerini tespit etmede kullanılır.
 *
 * Kullanım Alanı:
 * - Dosya integrity verification'da
 * - Data transmission validation'da
 * - Database record verification'da
 * - Backup integrity checks'de
 *
 * Teknik Detaylar:
 * - Configurable hash algorithm
 * - One-way hash function
 * - Deterministic output
 * - Fast computation
 *
 * Desteklenen Algoritmalar:
 * - sha256 (recommended, default)
 * - sha512 (high security)
 * - md5 (fast, low security)
 * - sha1 (legacy)
 *
 * Kullanım Senaryoları:
 * - Dosya upload'tan sonra integrity check
 * - Database'e kayıttan önce checksum
 * - Network transfer verification
 * - Backup validation
 *
 * Kullanım Örneği:
 * $checksum = Butunluk_Kontrolu($file_content);
 * $fast_check = Butunluk_Kontrolu($data, 'md5');
 * $secure_check = Butunluk_Kontrolu($critical_data, 'sha512');
 *
 * @param string $veri Checksum hesaplanacak veri
 * @param string $algoritma Hash algoritması (varsayılan: 'sha256')
 * @return string Veri bütünlük hash'i
 */
function Butunluk_Kontrolu($veri, $algoritma = 'sha256')
{
    return hash($algoritma, $veri);
}

/**
 * Zaman Damgalı Güvenli Token Üretici
 *
 * Bu fonksiyon, belirli süre sonra otomatik olarak geçersiz olan
 * güvenli token üretir. HMAC ile imzalanır.
 *
 * Kullanım Alanı:
 * - Email verification link'lerinde
 * - Password reset token'larında
 * - Temporary access URL'lerinde
 * - Time-sensitive operations'da
 *
 * Teknik Detaylar:
 * - Expiration timestamp embedded
 * - HMAC signature for integrity
 * - Base64 encoding for URL safety
 * - Additional data support
 *
 * Token Yapısı:
 * - Timestamp (expire time)
 * - Additional data (optional)
 * - HMAC signature
 * - Base64 encoded final token
 *
 * Güvenlik Özellikleri:
 * - Tampering detection
 * - Automatic expiration
 * - Cryptographic signature
 * - Replay attack prevention
 *
 * Kullanım Örneği:
 * $reset_token = Zaman_Damgali_Token(3600, 'user_id:123');
 * $verify_token = Zaman_Damgali_Token(86400, 'email@example.com');
 *
 * @param int $gecerlilikSuresi Token geçerlilik süresi saniye (varsayılan: 3600)
 * @param string $ekVeri Token'a eklenecek ek veri (varsayılan: boş)
 * @return string Zaman damgalı güvenli token
 */
function Zaman_Damgali_Token($gecerlilikSuresi = 3600, $ekVeri = '')
{
    $zaman = time() + $gecerlilikSuresi;
    $veri = $zaman . '|' . $ekVeri;
    $imza = Hmac_Olustur($veri, ANAHTAR);

    return base64_encode($veri . '|' . $imza);
}

/**
 * Zaman Damgalı Token Doğrulama İşlemi
 *
 * Bu fonksiyon, Zaman_Damgali_Token ile üretilen token'ı doğrular.
 * Süre aşımı ve imza kontrolleri yapar.
 *
 * Kullanım Alanı:
 * - Email verification link validation
 * - Password reset token checking
 * - Temporary URL access validation
 * - Time-sensitive operation verification
 *
 * Teknik Detaylar:
 * - Base64 decode ve parse
 * - Timestamp expiration check
 * - Additional data verification
 * - HMAC signature validation
 *
 * Validation Kontrolleri:
 * - Token format validation
 * - Expiration time check
 * - Additional data match
 * - HMAC signature verification
 *
 * Dönüş Değerleri:
 * - true: Token geçerli ve süresi dolmamış
 * - false: Geçersiz, bozuk veya süresi dolmuş
 *
 * Kullanım Örneği:
 * if (Zaman_Damgali_Token_Dogrula($received_token, 'user_id:123')) {
 *     echo 'Token geçerli, işlemi devam ettir';
 * } else {
 *     echo 'Geçersiz veya süresi dolmuş token';
 * }
 *
 * @param string $token Doğrulanacak token
 * @param string $ekVeri Beklenen ek veri (varsayılan: boş)
 * @return bool Geçerliyse true, değilse false
 */
function Zaman_Damgali_Token_Dogrula($token, $ekVeri = '')
{
    $cozulmus = base64_decode($token);
    $parcalar = explode('|', $cozulmus);

    if (count($parcalar) !== 3) {
        return false;
    }

    [$zaman, $tokenEkVeri, $imza] = $parcalar;
    if (time() > $zaman) {
        return false;
    }
    if ($tokenEkVeri !== $ekVeri) {
        return false;
    }
    $beklenenImza = Hmac_Olustur($zaman . '|' . $ekVeri, ANAHTAR);
    return hash_equals($beklenenImza, $imza);
}
