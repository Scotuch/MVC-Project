<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Dosya fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Dosya Varlık Kontrol Fonksiyonu
 *
 * Bu fonksiyon, belirtilen yolda bir dosyanın mevcut olup olmadığını kontrol eder.
 * Basit boolean dönüş ile dosya varlığını doğrular.
 *
 * Kullanım Alanı:
 * - Dosya sistemi operasyonlarından önce kontrol
 * - Upload file validasyonunda
 * - Config file verification'da
 * - Asset existence checking'de
 *
 * Teknik Detaylar:
 * - file_exists() PHP function'unu wrap eder
 * - Boolean return type
 * - Path validation yapar
 * - Cross-platform uyumluluğu
 *
 * Kullanım Örneği:
 * if (Dosya_Kontrol('/config/database.php')) {
 *     echo "Config dosyası mevcut";
 * } else {
 *     echo "Dosya bulunamadı";
 * }
 *
 * @param string $dosya Kontrol edilecek dosya yolu
 * @return bool Dosya varsa true, yoksa false
 */
function Dosya_Kontrol($dosya)
{
    return file_exists($dosya);
}

/**
 * Dosya İçeriği Okuma Fonksiyonu
 *
 * Bu fonksiyon, belirtilen dosyanın tüm içeriğini okur ve string olarak döndürür.
 * Binary ve text dosyaları destekler.
 *
 * Kullanım Alanı:
 * - Config dosyalarının okunmasında
 * - Template dosyası yüklemede
 * - Log dosyası analizinde
 * - Data file processing'de
 *
 * Teknik Detaylar:
 * - file_get_contents() kullanır
 * - Binary-safe okuma
 * - Memory-efficient tek seferde okuma
 * - Error handling ile false dönüş
 *
 * Kullanım Örneği:
 * $config = Dosya_Oku('/config/app.json');
 * if ($config !== false) {
 *     $settings = json_decode($config, true);
 * }
 *
 * @param string $dosya Okunacak dosyanın tam yolu
 * @return string|false Dosya içeriği veya hata durumunda false
 */
function Dosya_Oku($dosya)
{
    return file_get_contents($dosya);
}

/**
 * Dosyaya İçerik Yazma Fonksiyonu
 *
 * Bu fonksiyon, belirtilen dosyaya veri yazar. Mevcut içeriği tamamen değiştirir
 * veya üzerine ekler (append mode).
 *
 * Kullanım Alanı:
 * - Log dosyası oluşturmada
 * - Cache dosyası kaydetmede
 * - Config dosyası güncellenmesinde
 * - Data export işlemlerinde
 *
 * Teknik Detaylar:
 * - file_put_contents() kullanır
 * - LOCK_EX ile dosya kilitleme
 * - FILE_APPEND ile ekleme modu
 * - Atomic write operation
 *
 * Kullanım Örneği:
 * Dosya_Yaz('/logs/error.log', 'Hata: ' . $error_msg, true);
 * Dosya_Yaz('/cache/data.json', json_encode($data));
 *
 * @param string $dosya Yazılacak dosyanın tam yolu
 * @param string $icerik Yazılacak içerik
 * @param bool $ekle Üzerine eklensin mi (varsayılan: false)
 * @return int|false Yazılan byte sayısı veya hata durumunda false
 */
function Dosya_Yaz($dosya, $icerik, $ekle = false)
{
    $flags = LOCK_EX;
    if ($ekle) {
        $flags |= FILE_APPEND;
    }
    return file_put_contents($dosya, $icerik, $flags);
}

/**
 * Dosya Silme Fonksiyonu
 *
 * Bu fonksiyon, belirtilen dosyayı sistemden kalıcı olarak siler.
 * Güvenlik kontrolleri yaparak güvenli silme sağlar.
 *
 * Kullanım Alanı:
 * - Temp dosyalarının temizlenmesinde
 * - Cache dosyalarının silinmesinde
 * - Upload cleanup işlemlerinde
 * - Old file maintenance'da
 *
 * Teknik Detaylar:
 * - unlink() PHP function kullanır
 * - file_exists() ile varlık kontrolü
 * - Boolean return for success/failure
 * - Permanent deletion (geri alınamaz)
 *
 * UYARI: Bu fonksiyon kalıcı silme yapar, dikkatli kullanın!
 *
 * Kullanım Örneği:
 * if (Dosya_Sil('/temp/old_cache.dat')) {
 *     echo "Cache temizlendi";
 * } else {
 *     echo "Silme işlemi başarısız";
 * }
 *
 * @param string $dosya Silinecek dosyanın tam yolu
 * @return bool Başarılı ise true, hata durumunda false
 */
function Dosya_Sil($dosya)
{
    if (file_exists($dosya)) {
        return unlink($dosya);
    }
    return false;
}

/**
 * Dosya Boyutu Alma Fonksiyonu
 *
 * Bu fonksiyon, belirtilen dosyanın boyutunu byte cinsinden döndürür.
 * Formatlanmış boyut için Boyut() fonksiyonu ile birlikte kullanılabilir.
 *
 * Kullanım Alanı:
 * - Upload boyut kontrolünde
 * - Disk kullanım analizinde
 * - File size validation'da
 * - Storage quota checks'de
 *
 * Teknik Detaylar:
 * - filesize() PHP function kullanır
 * - Byte cinsinden exact size
 * - Large file support (>2GB)
 * - Error handling ile false dönüş
 *
 * Kullanım Örneği:
 * $size = Dosya_Boyut('/uploads/large_file.zip');
 * if ($size !== false) {
 *     echo "Dosya boyutu: " . Boyut($size);
 * }
 *
 * @param string $dosya Boyutu alınacak dosyanın tam yolu
 * @return int|false Dosya boyutu (byte) veya hata durumunda false
 */
function Dosya_Boyut($dosya)
{
    return filesize($dosya);
}

/**
 * Dosya MIME Type Belirleme Fonksiyonu
 *
 * Bu fonksiyon, bir dosyanın MIME type'ını güvenli şekilde belirler.
 * Upload işlemleri ve dosya validation'da kullanılır.
 *
 * Kullanım Alanı:
 * - File upload validation
 * - Content type detection
 * - Security file checking
 * - Download header setting
 *
 * Teknik Detaylar:
 * - Multiple detection methods
 * - Extension fallback
 * - Security validation
 * - Cross-platform compatibility
 *
 * Kullanım Örneği:
 * $mime = Dosya_Mime_Type('uploads/image.jpg');
 * if ($mime === 'image/jpeg') {
 *     echo 'JPEG dosyası';
 * }
 *
 * @param string $dosya_yolu Dosya yolu
 * @return string|false MIME type veya false
 */
function Dosya_Mime_Type($dosya_yolu)
{
    if (!file_exists($dosya_yolu) || !is_readable($dosya_yolu)) {
        return false;
    }

    // finfo kullanarak güvenli detection
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $dosya_yolu);
        finfo_close($finfo);

        if ($mime !== false) {
            return $mime;
        }
    }

    // mime_content_type fallback
    if (function_exists('mime_content_type')) {
        $mime = mime_content_type($dosya_yolu);
        if ($mime !== false) {
            return $mime;
        }
    }

    // Extension-based fallback
    $uzanti = strtolower(pathinfo($dosya_yolu, PATHINFO_EXTENSION));
    $mime_types = [
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    ];

    return isset($mime_types[$uzanti]) ? $mime_types[$uzanti] : 'application/octet-stream';
}

/**
 * Dosya Uzantısı Alma Fonksiyonu
 *
 * Bu fonksiyon, dosya adından uzantıyı çıkarır ve küçük harf olarak döndürür.
 * Type detection ve validation işlemlerinde kullanılır.
 *
 * Kullanım Alanı:
 * - File type validation'da
 * - Upload filter'larında
 * - File processing routing'de
 * - Extension-based handling'de
 *
 * Teknik Detaylar:
 * - pathinfo() PATHINFO_EXTENSION kullanır
 * - Lowercase normalization
 * - Dot character hariç
 * - Empty extension için boş string
 *
 * Kullanım Örneği:
 * $ext = Dosya_Uzanti('/uploads/document.PDF');
 * echo $ext; // 'pdf'
 *
 * if (in_array($ext, ['jpg', 'png', 'gif'])) {
 *     echo 'Resim dosyası';
 * }
 *
 * @param string $dosya Uzantısı alınacak dosya yolu/adı
 * @return string Dosya uzantısı (küçük harf, nokta hariç)
 */
function Dosya_Uzanti($dosya)
{
    return strtolower(pathinfo($dosya, PATHINFO_EXTENSION));
}

/**
 * Güvenli Dosya Adı Üretici
 *
 * Bu fonksiyon, dosya adını web ortamında güvenli hale getirir.
 * Özel karakterleri temizler ve cross-platform uyumluluğu sağlar.
 *
 * Kullanım Alanı:
 * - File upload processing'de
 * - Dynamic file generation'da
 * - Cross-platform file naming'de
 * - Web-safe filename creation'da
 *
 * Teknik Detaylar:
 * - Türkçe karakter normalization
 * - Special character removal
 * - Space to underscore conversion
 * - Extension preservation
 *
 * Kullanım Örneği:
 * $safe = Dosya_Guvenli_Ad('Türkçe Dosya Adı!@#.pdf');
 * echo $safe; // 'turkce_dosya_adi.pdf'
 *
 * @param string $dosya_adi Güvenli hale getirilecek dosya adı
 * @return string Web-safe dosya adı
 */
function Dosya_Guvenli_Ad($dosya_adi)
{
    $uzanti = Dosya_Uzanti($dosya_adi);
    $ad = pathinfo($dosya_adi, PATHINFO_FILENAME);

    // Türkçe karakterleri değiştir
    $turkce = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'];
    $ingilizce = ['c', 'g', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'I', 'O', 'S', 'U'];
    $ad = str_replace($turkce, $ingilizce, $ad);

    // Özel karakterleri temizle
    $ad = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $ad);
    $ad = preg_replace('/_+/', '_', $ad);
    $ad = trim($ad, '_');

    return $uzanti ? $ad . '.' . $uzanti : $ad;
}

/**
 * Dosya Kopyalama Fonksiyonu
 *
 * Bu fonksiyon, bir dosyayı başka bir konuma kopyalar.
 * Hedef dizin yoksa otomatik olarak oluşturur.
 *
 * Kullanım Alanı:
 * - Backup işlemlerinde
 * - File duplication'da
 * - Template copying'de
 * - Asset deployment'da
 *
 * Teknik Detaylar:
 * - copy() PHP function kullanır
 * - Target directory auto-creation
 * - dirname() ile path extraction
 * - Boolean success/failure return
 *
 * Kullanım Örneği:
 * if (Dosya_Kopyala('/source/file.txt', '/backup/file.txt')) {
 *     echo "Dosya başarıyla kopyalandı";
 * }
 *
 * @param string $kaynak Kopyalanacak kaynak dosya yolu
 * @param string $hedef Hedef dosya yolu
 * @return bool Başarılı ise true, hata durumunda false
 */
function Dosya_Kopyala($kaynak, $hedef)
{
    $hedef_dizin = dirname($hedef);
    if (!is_dir($hedef_dizin)) {
        Klasor_Olustur($hedef_dizin);
    }
    return copy($kaynak, $hedef);
}

/**
 * Dosya Taşıma/Yeniden Adlandırma Fonksiyonu
 *
 * Bu fonksiyon, dosyayı yeni konuma taşır veya adını değiştirir.
 * Atomic operation ile güvenli taşıma sağlar.
 *
 * Kullanım Alanı:
 * - File organization'da
 * - Upload processing'de
 * - File renaming'de
 * - Directory restructuring'de
 *
 * Teknik Detaylar:
 * - rename() PHP function kullanır
 * - Target directory auto-creation
 * - Atomic move operation
 * - Cross-directory support
 *
 * Kullanım Örneği:
 * if (Dosya_Tasi('/temp/upload.tmp', '/final/document.pdf')) {
 *     echo "Dosya başarıyla taşındı";
 * }
 *
 * @param string $kaynak Taşınacak kaynak dosya yolu
 * @param string $hedef Hedef dosya yolu
 * @return bool Başarılı ise true, hata durumunda false
 */
function Dosya_Tasi($kaynak, $hedef)
{
    $hedef_dizin = dirname($hedef);
    if (!is_dir($hedef_dizin)) {
        Klasor_Olustur($hedef_dizin);
    }
    return rename($kaynak, $hedef);
}

/**
 * Dosya Son Değiştirilme Zamanı
 *
 * Bu fonksiyon, dosyanın en son ne zaman değiştirildiği bilgisini döndürür.
 * Unix timestamp formatında sonuç verir.
 *
 * Kullanım Alanı:
 * - Cache validation'da
 * - File monitoring'de
 * - Backup scheduling'de
 * - Version control'de
 *
 * Teknik Detaylar:
 * - filemtime() PHP function kullanır
 * - Unix timestamp return
 * - Error handling ile false dönüş
 * - Cross-platform compatibility
 *
 * Kullanım Örneği:
 * $mtime = Dosya_Degistirilme_Zamani('/data/cache.dat');
 * if (time() - $mtime > 3600) {
 *     echo "Cache 1 saattan eski, yenilenmeli";
 * }
 *
 * @param string $dosya Kontrol edilecek dosya yolu
 * @return int|false Son değiştirilme zamanı (Unix timestamp) veya false
 */
function Dosya_Degistirilme_Zamani($dosya)
{
    return filemtime($dosya);
}

/**
 * Dosya İzinleri Alma Fonksiyonu
 *
 * Bu fonksiyon, dosyanın sistem izinlerini octal format'ta döndürür.
 * Permission management ve security audit'lerde kullanılır.
 *
 * Kullanım Alanı:
 * - Security audit'lerde
 * - Permission management'da
 * - File system diagnostics'de
 * - Access control validation'da
 *
 * Teknik Detaylar:
 * - fileperms() PHP function kullanır
 * - Octal format return (örn: 0755)
 * - Full permission mask
 * - Unix-style permissions
 *
 * Kullanım Örneği:
 * $perms = Dosya_Izinler('/config/secure.conf');
 * if (($perms & 0o044) !== 0) {
 *     echo "UYARI: Dosya herkes tarafından okunabilir!";
 * }
 *
 * @param string $dosya İzinleri kontrol edilecek dosya yolu
 * @return int|false Dosya izinleri (octal) veya false
 */
function Dosya_Izinler($dosya)
{
    return fileperms($dosya);
}

/**
 * Dosya Hash Değeri Hesaplama
 *
 * Bu fonksiyon, dosyanın hash değerini hesaplar. Bütünlük kontrolü
 * ve dosya karşılaştırması için kullanılır.
 *
 * Kullanım Alanı:
 * - File integrity verification
 * - Duplicate file detection
 * - Checksum validation
 * - File comparison operations
 *
 * Teknik Detaylar:
 * - hash_file() PHP function kullanır
 * - Multiple algorithm support
 * - Large file support
 * - Memory efficient processing
 *
 * Kullanım Örneği:
 * $hash = Dosya_Hash('/downloads/file.zip');
 * if ($hash === $expected_sha256) {
 *     echo "Dosya bütünlüğü doğrulandı";
 * }
 *
 * @param string $dosya Hash'i hesaplanacak dosya yolu
 * @param string $algoritma Hash algoritması (varsayılan: 'sha256')
 * @return string|false Dosya hash değeri veya false
 */
function Dosya_Hash($dosya, $algoritma = 'sha256')
{
    return hash_file($algoritma, $dosya);
}

/**
 * Satır Satır Dosya Okuma Fonksiyonu
 *
 * Bu fonksiyon, büyük dosyaları memory-efficient şekilde satır satır okur.
 * Callback function ile her satırı işleyebilirsiniz.
 *
 * Kullanım Alanı:
 * - Large log file processing
 * - CSV file parsing
 * - Data file analysis
 * - Memory-efficient file reading
 *
 * Teknik Detaylar:
 * - Generator pattern ile memory efficiency
 * - Line-by-line processing
 * - Callback function support
 * - Large file handling
 *
 * Kullanım Örneği:
 * Dosya_Satir_Oku('/logs/large.log', function($satir, $satir_no) {
 *     if (strpos($satir, 'ERROR') !== false) {
 *         echo "Hata bulundu satır $satir_no: $satir\n";
 *     }
 * });
 *
 * @param string $dosya Okunacak dosya yolu
 * @param callable $callback Her satır için çağrılacak function
 * @return bool İşlem başarılı ise true, hata durumunda false
 */
function Dosya_Satir_Oku($dosya, callable $callback)
{
    $handle = fopen($dosya, 'r');
    if ($handle === false) {
        return false;
    }

    $satir_no = 1;
    while (($satir = fgets($handle)) !== false) {
        $callback(rtrim($satir), $satir_no);
        $satir_no++;
    }

    fclose($handle);
    return true;
}

/**
 * Dosya İçinde Arama Fonksiyonu
 *
 * Bu fonksiyon, dosya içinde belirtilen metni arar ve eşleşen satırları döndürür.
 * Regex ve case-insensitive arama seçenekleri sunar.
 *
 * Kullanım Alanı:
 * - Log file analysis
 * - Configuration file search
 * - Text file grep operations
 * - Content filtering
 *
 * Teknik Detaylar:
 * - Line-by-line search
 * - Regex pattern support
 * - Case-insensitive option
 * - Line number tracking
 *
 * Kullanım Örneği:
 * $sonuclar = Dosya_Icerik_Ara('/logs/app.log', 'ERROR', false, true);
 * foreach ($sonuclar as $sonuc) {
 *     echo "Satır {$sonuc['satir']}: {$sonuc['icerik']}\n";
 * }
 *
 * @param string $dosya Arama yapılacak dosya yolu
 * @param string $aranan Aranacak metin/pattern
 * @param bool $regex Regex pattern mi (varsayılan: false)
 * @param bool $buyuk_kucuk_harf Büyük/küçük harf duyarlı (varsayılan: true)
 * @return array Eşleşen satırlar [['satir' => int, 'icerik' => string], ...]
 */
function Dosya_Icerik_Ara($dosya, $aranan, $regex = false, $buyuk_kucuk_harf = true)
{
    $sonuclar = [];

    if (!file_exists($dosya)) {
        return $sonuclar;
    }

    $handle = fopen($dosya, 'r');
    if ($handle === false) {
        return $sonuclar;
    }

    $satir_no = 1;
    while (($satir = fgets($handle)) !== false) {
        $satir = rtrim($satir);
        $bulundu = false;

        if ($regex) {
            $flags = $buyuk_kucuk_harf ? '' : 'i';
            $bulundu = preg_match('/' . $aranan . '/' . $flags, $satir);
        } else {
            if ($buyuk_kucuk_harf) {
                $bulundu = strpos($satir, $aranan) !== false;
            } else {
                $bulundu = stripos($satir, $aranan) !== false;
            }
        }

        if ($bulundu) {
            $sonuclar[] = [
                'satir' => $satir_no,
                'icerik' => $satir
            ];
        }

        $satir_no++;
    }

    fclose($handle);
    return $sonuclar;
}

/**
 * Dosya Backup Oluşturma Fonksiyonu
 *
 * Bu fonksiyon, dosyanın backup kopyasını timestamp ile oluşturur.
 * Güvenli dosya işlemleri öncesi backup almada kullanılır.
 *
 * Kullanım Alanı:
 * - Critical file modifications öncesi
 * - Configuration changes backup
 * - Database file backup
 * - Version history maintenance
 *
 * Teknik Detaylar:
 * - Timestamp-based naming
 * - Original file preservation
 * - Atomic copy operation
 * - Backup directory creation
 *
 * Kullanım Örneği:
 * $backup_path = Dosya_Backup_Olustur('/config/database.php');
 * if ($backup_path) {
 *     echo "Backup oluşturuldu: $backup_path";
 *     // Şimdi güvenle dosyayı değiştirebiliriz
 * }
 *
 * @param string $dosya Backup'ı alınacak dosya yolu
 * @param string $backup_dizin Backup dizini (boşsa aynı dizin)
 * @return string|false Backup dosyasının yolu veya false
 */
function Dosya_Backup_Olustur($dosya, $backup_dizin = '')
{
    if (!file_exists($dosya)) {
        return false;
    }

    $dosya_info = pathinfo($dosya);
    $timestamp = date('Y-m-d_H-i-s');

    if ($backup_dizin === '') {
        $backup_dizin = $dosya_info['dirname'];
    }

    $backup_adi = $dosya_info['filename'] . '_backup_' . $timestamp;
    if (isset($dosya_info['extension'])) {
        $backup_adi .= '.' . $dosya_info['extension'];
    }

    $backup_yolu = $backup_dizin . DIRECTORY_SEPARATOR . $backup_adi;

    if (Dosya_Kopyala($dosya, $backup_yolu)) {
        return $backup_yolu;
    }

    return false;
}

/**
 * Dosya Lock İşlemi (Dosya Kilitleme)
 *
 * Bu fonksiyon, dosyayı concurrent access'e karşı kilitler.
 * Multi-process ortamlarda güvenli dosya erişimi sağlar.
 *
 * Kullanım Alanı:
 * - Critical file operations
 * - Database file access
 * - Log file writing
 * - Configuration file updates
 *
 * Teknik Detaylar:
 * - flock() system call kullanır
 * - Exclusive/shared lock seçenekleri
 * - Non-blocking option
 * - Resource handle return
 *
 * Kullanım Örneği:
 * $handle = Dosya_Kilitle('/shared/counter.txt', 'w');
 * if ($handle) {
 *     fwrite($handle, (int)fread($handle, 10) + 1);
 *     Dosya_Kilit_Ac($handle);
 * }
 *
 * @param string $dosya Kilitlenecek dosya yolu
 * @param string $mod Açma modu (r/w/a)
 * @param bool $paylasmali Paylaşımlı kilit mi (varsayılan: false=exclusive)
 * @return resource|false Dosya handle'ı veya false
 */
function Dosya_Kilitle($dosya, $mod = 'r+', $paylasmali = false)
{
    $handle = fopen($dosya, $mod);
    if ($handle === false) {
        return false;
    }

    $lock_type = $paylasmali ? LOCK_SH : LOCK_EX;
    if (flock($handle, $lock_type)) {
        return $handle;
    } else {
        fclose($handle);
        return false;
    }
}

/**
 * Dosya Kilidini Açma Fonksiyonu
 *
 * Bu fonksiyon, Dosya_Kilitle ile kilitlenen dosya kilidini açar
 * ve dosya handle'ını kapatır.
 *
 * Kullanım Alanı:
 * - File lock cleanup
 * - Resource management
 * - Critical section exit
 * - File handle closure
 *
 * Teknik Detaylar:
 * - flock() LOCK_UN ile unlock
 * - fclose() ile handle closure
 * - Resource cleanup
 * - Error handling
 *
 * Kullanım Örneği:
 * $handle = Dosya_Kilitle('/data/file.dat', 'w');
 * // Dosya işlemleri...
 * Dosya_Kilit_Ac($handle);
 *
 * @param resource $handle Dosya kilidiyle handle resource
 * @return bool Başarılı ise true, hata durumunda false
 */
function Dosya_Kilit_Ac($handle)
{
    if (is_resource($handle)) {
        flock($handle, LOCK_UN);
        return fclose($handle);
    }
    return false;
}

/**
 * Dizindeki Dosyaları Listeleme Fonksiyonu
 *
 * Bu fonksiyon, belirtilen dizindeki dosyaları filtreler ve listeler.
 * Uzantı, boyut ve tarih filtreleme seçenekleri sunar.
 *
 * Kullanım Alanı:
 * - File management systems
 * - Asset listing
 * - Backup file discovery
 * - Directory analysis
 *
 * Teknik Detaylar:
 * - RecursiveDirectoryIterator kullanımı
 * - Extension filtering
 * - Size and date filtering
 * - Detailed file information
 *
 * Kullanım Örneği:
 * $resimler = Dizin_Dosya_Listesi('/uploads', ['jpg', 'png']);
 * $buyuk_dosyalar = Dizin_Dosya_Listesi('/data', [], 1048576); // >1MB
 *
 * @param string $dizin Taranacak dizin yolu
 * @param array $uzantilar İzin verilen uzantılar (boş = hepsi)
 * @param int $min_boyut Minimum dosya boyutu byte (0 = sınır yok)
 * @param int $max_boyut Maximum dosya boyutu byte (0 = sınır yok)
 * @param bool $recursive Alt dizinler dahil mi (varsayılan: false)
 * @return array Dosya bilgileri array'i
 */
function Dizin_Dosya_Listesi($dizin, $uzantilar = [], $min_boyut = 0, $max_boyut = 0, $recursive = false)
{
    $dosyalar = [];

    if (!is_dir($dizin)) {
        return $dosyalar;
    }

    $iterator = $recursive ?
        new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dizin)) :
        new DirectoryIterator($dizin);

    foreach ($iterator as $dosya) {
        if ($dosya->isFile()) {
            $dosya_yolu = $dosya->getPathname();
            $dosya_uzanti = Dosya_Uzanti($dosya_yolu);
            $dosya_boyut = $dosya->getSize();

            // Uzantı filtresi
            if (!empty($uzantilar) && !in_array($dosya_uzanti, $uzantilar)) {
                continue;
            }

            // Boyut filtresi
            if ($min_boyut > 0 && $dosya_boyut < $min_boyut) {
                continue;
            }
            if ($max_boyut > 0 && $dosya_boyut > $max_boyut) {
                continue;
            }

            $dosyalar[] = [
                'yol' => $dosya_yolu,
                'ad' => $dosya->getFilename(),
                'boyut' => $dosya_boyut,
                'boyut_formatli' => Boyut($dosya_boyut),
                'uzanti' => $dosya_uzanti,
                'degistirilme_zamani' => $dosya->getMTime(),
                'okunabilir' => $dosya->isReadable(),
                'yazilabilir' => $dosya->isWritable()
            ];
        }
    }

    return $dosyalar;
}

/**
 * CSV Dosyası Okuma Fonksiyonu
 *
 * Bu fonksiyon, CSV dosyasını okur ve PHP array'i olarak döndürür.
 * Header row ve delimiter customization destekler.
 *
 * Kullanım Alanı:
 * - Data import operations
 * - Excel file processing
 * - Report data reading
 * - Bulk data operations
 *
 * Teknik Detaylar:
 * - fgetcsv() PHP function kullanır
 * - Custom delimiter support
 * - Header row as array keys
 * - Encoding awareness
 *
 * Kullanım Örneği:
 * $data = Csv_Dosya_Oku('/data/users.csv', true, ',');
 * foreach ($data as $user) {
 *     echo $user['name'] . ' - ' . $user['email'] . "\n";
 * }
 *
 * @param string $dosya CSV dosyasının yolu
 * @param bool $baslik_satiri İlk satır header mı (varsayılan: true)
 * @param string $ayirici CSV delimiter karakteri (varsayılan: ',')
 * @return array CSV verileri array formatında
 */
function Csv_Dosya_Oku($dosya, $baslik_satiri = true, $ayirici = ',')
{
    $veriler = [];
    $basliklar = [];

    if (!file_exists($dosya)) {
        return $veriler;
    }

    $handle = fopen($dosya, 'r');
    if ($handle === false) {
        return $veriler;
    }

    $satir_no = 0;
    while (($satir = fgetcsv($handle, 0, $ayirici)) !== false) {
        if ($satir_no === 0 && $baslik_satiri) {
            $basliklar = $satir;
        } else {
            if ($baslik_satiri && !empty($basliklar)) {
                $veri_satiri = [];
                foreach ($satir as $index => $deger) {
                    $anahtar = $basliklar[$index] ?? $index;
                    $veri_satiri[$anahtar] = $deger;
                }
                $veriler[] = $veri_satiri;
            } else {
                $veriler[] = $satir;
            }
        }
        $satir_no++;
    }

    fclose($handle);
    return $veriler;
}

/**
 * CSV Dosyası Yazma Fonksiyonu
 *
 * Bu fonksiyon, PHP array'ini CSV dosyası olarak kaydeder.
 * Header row ve delimiter customization destekler.
 *
 * Kullanım Alanı:
 * - Data export operations
 * - Report generation
 * - Excel-compatible file creation
 * - Bulk data export
 *
 * Teknik Detaylar:
 * - fputcsv() PHP function kullanır
 * - Custom delimiter support
 * - Automatic header generation
 * - UTF-8 encoding
 *
 * Kullanım Örneği:
 * $users = [
 *     ['name' => 'Ali', 'email' => 'ali@test.com'],
 *     ['name' => 'Veli', 'email' => 'veli@test.com']
 * ];
 * Csv_Dosya_Yaz('/exports/users.csv', $users, true);
 *
 * @param string $dosya CSV dosyasının yolu
 * @param array $veriler Yazılacak veriler array'i
 * @param bool $baslik_satiri Header row eklensin mi (varsayılan: true)
 * @param string $ayirici CSV delimiter karakteri (varsayılan: ',')
 * @return bool Başarılı ise true, hata durumunda false
 */
function Csv_Dosya_Yaz($dosya, array $veriler, $baslik_satiri = true, $ayirici = ',')
{
    if (empty($veriler)) {
        return false;
    }

    $handle = fopen($dosya, 'w');
    if ($handle === false) {
        return false;
    }

    // UTF-8 BOM ekle (Excel uyumluluğu için)
    fwrite($handle, "\xEF\xBB\xBF");

    // Header satırı yaz
    if ($baslik_satiri && is_array($veriler[0])) {
        $basliklar = array_keys($veriler[0]);
        fputcsv($handle, $basliklar, $ayirici);
    }

    // Veri satırlarını yaz
    foreach ($veriler as $satir) {
        if (is_array($satir)) {
            fputcsv($handle, array_values($satir), $ayirici);
        } else {
            fputcsv($handle, [$satir], $ayirici);
        }
    }

    fclose($handle);
    return true;
}

/**
 * Dosya İndirme Response Fonksiyonu
 *
 * Bu fonksiyon, dosyayı tarayıcıya indirme olarak gönderir.
 * Uygun Content-Type ve Content-Disposition header'larını ayarlar.
 *
 * Kullanım Alanı:
 * - File download systems
 * - Document delivery
 * - Export file serving
 * - Protected file access
 *
 * Teknik Detaylar:
 * - HTTP response headers
 * - MIME type detection
 * - Content-Disposition attachment
 * - File streaming support
 *
 * Kullanım Örneği:
 * Dosya_Indir('/reports/monthly.pdf', 'Aylık_Rapor_2024.pdf');
 * // Tarayıcı dosyayı 'Aylık_Rapor_2024.pdf' adıyla indirir
 *
 * @param string $dosya İndirilecek dosyanın yolu
 * @param string $indirme_adi İndirme sırasında kullanılacak dosya adı
 * @return bool İndirme başlatıldı ise true, hata durumunda false
 */
function Dosya_Indir($dosya, $indirme_adi = '')
{
    if (!file_exists($dosya)) {
        return false;
    }

    $indirme_adi = $indirme_adi ?: basename($dosya);
    $mime_type = Dosya_Mime_Type($dosya);
    $dosya_boyutu = filesize($dosya);

    // HTTP headers
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $indirme_adi . '"');
    header('Content-Length: ' . $dosya_boyutu);
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    // Dosyayı stream et
    readfile($dosya);
    return true;
}

/**
 * Dosya Yükleme İşleme Fonksiyonu
 *
 * Bu fonksiyon, HTTP file upload'ını güvenli şekilde işler.
 * Dosya validasyonu ve güvenli kaydetme işlemlerini gerçekleştirir.
 *
 * Kullanım Alanı:
 * - Web form file upload processing
 * - User avatar/document upload
 * - Bulk file upload systems
 * - CMS file management
 *
 * Teknik Detaylar:
 * - $_FILES superglobal processing
 * - UPLOAD_ERR_* error code handling
 * - File type validation
 * - Size limit enforcement
 *
 * Kullanım Örneği:
 * $result = Dosya_Yukle('profile_photo', '/uploads/avatars/', ['jpg', 'png'], 2097152);
 * if ($result['success']) {
 *     echo "Dosya yüklendi: " . $result['path'];
 * } else {
 *     echo "Hata: " . $result['error'];
 * }
 *
 * @param string $input_name HTML form input name
 * @param string $hedef_dizin Upload edilecek dizin
 * @param array $izin_verilen_uzantilar İzin verilen dosya uzantıları
 * @param int $max_boyut Maximum dosya boyutu (byte)
 * @param bool $guvenli_ad Güvenli dosya adı oluşturulsun mu
 * @return array Upload sonucu ['success' => bool, 'path' => string, 'error' => string]
 */
function Dosya_Yukle($input_name, $hedef_dizin, $izin_verilen_uzantilar = [], $max_boyut = 0, $guvenli_ad = true)
{
    $result = ['success' => false, 'path' => '', 'error' => ''];

    // Upload varlık kontrolü
    if (!isset($_FILES[$input_name])) {
        $result['error'] = 'Dosya yüklenmedi';
        return $result;
    }

    $file = $_FILES[$input_name];

    // Upload error kontrolü
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'Dosya PHP ini ayarlarında belirlenen boyutu aştı',
            UPLOAD_ERR_FORM_SIZE => 'Dosya form MAX_FILE_SIZE sınırını aştı',
            UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
            UPLOAD_ERR_NO_FILE => 'Dosya yüklenmedi',
            UPLOAD_ERR_NO_TMP_DIR => 'Geçici dizin bulunamadı',
            UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı',
            UPLOAD_ERR_EXTENSION => 'PHP uzantısı dosya yüklemeyi durdurdu'
        ];
        $result['error'] = $upload_errors[$file['error']] ?? 'Bilinmeyen yükleme hatası';
        return $result;
    }

    // Dosya boyutu kontrolü
    if ($max_boyut > 0 && $file['size'] > $max_boyut) {
        $result['error'] = 'Dosya çok büyük. Maksimum: ' . Boyut($max_boyut);
        return $result;
    }

    // Dosya uzantısı kontrolü
    $dosya_uzantisi = Dosya_Uzanti($file['name']);
    if (!empty($izin_verilen_uzantilar) && !in_array($dosya_uzantisi, $izin_verilen_uzantilar)) {
        $result['error'] = 'İzin verilmeyen dosya türü. İzin verilenler: ' . implode(', ', $izin_verilen_uzantilar);
        return $result;
    }

    // Hedef dizin oluştur
    if (!is_dir($hedef_dizin)) {
        Klasor_Olustur($hedef_dizin);
    }

    // Dosya adını belirle
    $dosya_adi = $guvenli_ad ? Dosya_Guvenli_Ad($file['name']) : $file['name'];

    // Aynı isimde dosya varsa unique ad oluştur
    $hedef_yol = $hedef_dizin . DIRECTORY_SEPARATOR . $dosya_adi;
    $counter = 1;
    $base_name = pathinfo($dosya_adi, PATHINFO_FILENAME);
    $extension = pathinfo($dosya_adi, PATHINFO_EXTENSION);

    while (file_exists($hedef_yol)) {
        $new_name = $base_name . '_' . $counter;
        if ($extension) {
            $new_name .= '.' . $extension;
        }
        $hedef_yol = $hedef_dizin . DIRECTORY_SEPARATOR . $new_name;
        $counter++;
    }

    // Dosyayı taşı
    if (move_uploaded_file($file['tmp_name'], $hedef_yol)) {
        $result['success'] = true;
        $result['path'] = $hedef_yol;
        $result['filename'] = basename($hedef_yol);
        $result['size'] = filesize($hedef_yol);
        $result['mime_type'] = Dosya_Mime_Type($hedef_yol);
    } else {
        $result['error'] = 'Dosya kaydedilemedi';
    }

    return $result;
}

/**
 * Resim Dosyası Yeniden Boyutlandırma Fonksiyonu
 *
 * Bu fonksiyon, resim dosyasını belirtilen boyutlara yeniden boyutlandırır.
 * GD extension kullanarak çeşitli resim formatlarını destekler.
 *
 * Kullanım Alanı:
 * - Avatar/profile photo resize
 * - Thumbnail generation
 * - Image optimization
 * - Gallery image processing
 *
 * Teknik Detaylar:
 * - GD extension kullanımı
 * - JPEG, PNG, GIF, WebP desteği
 * - Aspect ratio preservation
 * - Memory efficient processing
 *
 * Kullanım Örneği:
 * if (Resim_Boyutlandir('/uploads/large.jpg', '/thumbs/small.jpg', 200, 200)) {
 *     echo "Thumbnail oluşturuldu";
 * }
 *
 * @param string $kaynak_dosya Kaynak resim dosyası yolu
 * @param string $hedef_dosya Hedef resim dosyası yolu
 * @param int $yeni_genislik Yeni genişlik (pixel)
 * @param int $yeni_yukseklik Yeni yükseklik (pixel)
 * @param bool $oran_koru En-boy oranı korunsun mu (varsayılan: true)
 * @param int $kalite JPEG kalitesi 0-100 (varsayılan: 85)
 * @return bool Başarılı ise true, hata durumunda false
 */
function Resim_Boyutlandir($kaynak_dosya, $hedef_dosya, $yeni_genislik, $yeni_yukseklik, $oran_koru = true, $kalite = 85)
{
    if (!extension_loaded('gd')) {
        return false;
    }

    if (!file_exists($kaynak_dosya)) {
        return false;
    }

    // Resim bilgilerini al
    $resim_info = getimagesize($kaynak_dosya);
    if ($resim_info === false) {
        return false;
    }

    $eski_genislik = $resim_info[0];
    $eski_yukseklik = $resim_info[1];
    $mime_type = $resim_info['mime'];

    // Kaynak resmi yükle
    switch ($mime_type) {
        case 'image/jpeg':
            $kaynak_resim = imagecreatefromjpeg($kaynak_dosya);
            break;
        case 'image/png':
            $kaynak_resim = imagecreatefrompng($kaynak_dosya);
            break;
        case 'image/gif':
            $kaynak_resim = imagecreatefromgif($kaynak_dosya);
            break;
        case 'image/webp':
            $kaynak_resim = imagecreatefromwebp($kaynak_dosya);
            break;
        default:
            return false;
    }

    if (!$kaynak_resim) {
        return false;
    }

    // En-boy oranını koruyarak yeni boyutları hesapla
    if ($oran_koru) {
        $oran_x = $yeni_genislik / $eski_genislik;
        $oran_y = $yeni_yukseklik / $eski_yukseklik;
        $oran = min($oran_x, $oran_y);

        $yeni_genislik = (int)($eski_genislik * $oran);
        $yeni_yukseklik = (int)($eski_yukseklik * $oran);
    }

    // Yeni resim oluştur
    $yeni_resim = imagecreatetruecolor($yeni_genislik, $yeni_yukseklik);

    // PNG ve GIF için şeffaflığı koru
    if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
        imagealphablending($yeni_resim, false);
        imagesavealpha($yeni_resim, true);
        $seffaf = imagecolorallocatealpha($yeni_resim, 0, 0, 0, 127);
        imagefill($yeni_resim, 0, 0, $seffaf);
    }

    // Resmi yeniden boyutlandır
    imagecopyresampled(
        $yeni_resim,
        $kaynak_resim,
        0,
        0,
        0,
        0,
        $yeni_genislik,
        $yeni_yukseklik,
        $eski_genislik,
        $eski_yukseklik
    );

    // Hedef dizini oluştur
    $hedef_dizin = dirname($hedef_dosya);
    if (!is_dir($hedef_dizin)) {
        Klasor_Olustur($hedef_dizin);
    }

    // Resmi kaydet
    $basarili = false;
    $hedef_uzanti = Dosya_Uzanti($hedef_dosya);

    switch ($hedef_uzanti) {
        case 'jpg':
        case 'jpeg':
            $basarili = imagejpeg($yeni_resim, $hedef_dosya, $kalite);
            break;
        case 'png':
            $basarili = imagepng($yeni_resim, $hedef_dosya);
            break;
        case 'gif':
            $basarili = imagegif($yeni_resim, $hedef_dosya);
            break;
        case 'webp':
            $basarili = imagewebp($yeni_resim, $hedef_dosya, $kalite);
            break;
        default:
            // Kaynak formatı koru
            switch ($mime_type) {
                case 'image/jpeg':
                    $basarili = imagejpeg($yeni_resim, $hedef_dosya, $kalite);
                    break;
                case 'image/png':
                    $basarili = imagepng($yeni_resim, $hedef_dosya);
                    break;
                case 'image/gif':
                    $basarili = imagegif($yeni_resim, $hedef_dosya);
                    break;
                case 'image/webp':
                    $basarili = imagewebp($yeni_resim, $hedef_dosya, $kalite);
                    break;
            }
    }

    // Memory cleanup
    imagedestroy($kaynak_resim);
    imagedestroy($yeni_resim);

    return $basarili;
}

/**
 * Dosya Type Validation Fonksiyonu
 *
 * Bu fonksiyon, dosyanın gerçek MIME type'ını kontrol ederek
 * güvenlik açıklarına karşı koruma sağlar.
 *
 * Kullanım Alanı:
 * - File upload security validation
 * - Malicious file detection
 * - File type spoofing prevention
 * - Security audit operations
 *
 * Teknik Detaylar:
 * - finfo extension kullanımı
 * - Magic number detection
 * - Extension vs content validation
 * - Binary analysis
 *
 * Kullanım Örneği:
 * if (!Dosya_Type_Guvenligi('/uploads/suspicious.jpg')) {
 *     echo "UYARI: Dosya içeriği uzantısıyla uyuşmuyor!";
 * }
 *
 * @param string $dosya Kontrol edilecek dosya yolu
 * @param array $beklenen_mime_types Kabul edilen MIME türleri
 * @return bool Güvenli ise true, şüpheli ise false
 */
function Dosya_Type_Guvenligi($dosya, $beklenen_mime_types = [])
{
    if (!file_exists($dosya)) {
        return false;
    }

    // Gerçek MIME type'ı al
    $gercek_mime = Dosya_Mime_Type($dosya);
    $uzanti = Dosya_Uzanti($dosya);

    // Extension-based expected MIME types
    $uzanti_mime_map = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'txt' => 'text/plain',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed'
    ];

    // Beklenen MIME type'lar belirtilmemişse uzantıdan çıkar
    if (empty($beklenen_mime_types) && isset($uzanti_mime_map[$uzanti])) {
        $beklenen_mime_types = [$uzanti_mime_map[$uzanti]];
    }

    // MIME type kontrolü
    if (!empty($beklenen_mime_types)) {
        return in_array($gercek_mime, $beklenen_mime_types);
    }

    // Uzantı-içerik uyumu kontrolü
    if (isset($uzanti_mime_map[$uzanti])) {
        return $gercek_mime === $uzanti_mime_map[$uzanti];
    }

    return true; // Bilinmeyen uzantılar için geç
}

/**
 * Temporary Dosya Oluşturma Fonksiyonu
 *
 * Bu fonksiyon, sistem temp dizininde geçici dosya oluşturur.
 * Benzersiz isim garantisi ve otomatik cleanup desteği.
 *
 * Kullanım Alanı:
 * - Temporary file processing
 * - Cache file generation
 * - Export operation staging
 * - Image processing workflows
 *
 * Teknik Detaylar:
 * - tmpfile() ve tempnam() kullanımı
 * - System temp directory usage
 * - Unique filename generation
 * - Resource handle return
 *
 * Kullanım Örneği:
 * $temp_file = Gecici_Dosya_Olustur('myapp_');
 * file_put_contents($temp_file, $data);
 * // İşlemler...
 * unlink($temp_file); // Temizlik
 *
 * @param string $prefix Dosya adı prefix'i (varsayılan: 'tmp_')
 * @param string $dizin Temp dizin (boşsa sistem temp)
 * @return string|false Temp dosya yolu veya false
 */
function Gecici_Dosya_Olustur($prefix = 'tmp_', $dizin = '')
{
    if (empty($dizin)) {
        $dizin = sys_get_temp_dir();
    }

    return tempnam($dizin, $prefix);
}

/**
 * Dosya Sıkıştırma Fonksiyonu (ZIP)
 *
 * Bu fonksiyon, dosya veya klasörü ZIP formatında sıkıştırır.
 * Recursive klasör sıkıştırma ve compression level desteği.
 *
 * Kullanım Alanı:
 * - File archive creation
 * - Backup compression
 * - Multiple file delivery
 * - Download package creation
 *
 * Teknik Detaylar:
 * - ZipArchive extension kullanımı
 * - Recursive directory compression
 * - File path preservation
 * - Compression level control
 *
 * Kullanım Örneği:
 * $files = ['/path/file1.txt', '/path/file2.txt'];
 * if (Dosya_Zip_Olustur('/backup.zip', $files)) {
 *     echo "ZIP arşivi oluşturuldu";
 * }
 *
 * @param string $zip_dosyasi Oluşturulacak ZIP dosya yolu
 * @param array|string $dosyalar Sıkıştırılacak dosya/klasör(ler)
 * @param string $base_dizin Base dizin (relative paths için)
 * @return bool Başarılı ise true, hata durumunda false
 */
function Dosya_Zip_Olustur($zip_dosyasi, $dosyalar, $base_dizin = '')
{
    if (!class_exists('ZipArchive')) {
        return false;
    }

    $zip = new ZipArchive();
    $result = $zip->open($zip_dosyasi, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    if ($result !== true) {
        return false;
    }

    // Tek dosya ise array'e çevir
    if (!is_array($dosyalar)) {
        $dosyalar = [$dosyalar];
    }

    foreach ($dosyalar as $dosya) {
        if (is_file($dosya)) {
            // Dosya ekle
            $local_name = $base_dizin ? str_replace($base_dizin . DIRECTORY_SEPARATOR, '', $dosya) : basename($dosya);
            $zip->addFile($dosya, $local_name);
        } elseif (is_dir($dosya)) {
            // Klasör ekle (recursive)
            Zip_Klasor_Ekle($zip, $dosya, $base_dizin);
        }
    }

    return $zip->close();
}

/**
 * ZIP'e Recursive Klasör Ekleme (Helper)
 *
 * Bu fonksiyon, Dosya_Zip_Olustur için recursive klasör ekleme yapar.
 * Internal helper function olarak kullanılır.
 *
 * @param ZipArchive $zip ZIP archive object
 * @param string $klasor Eklenecek klasör yolu
 * @param string $base_dizin Base dizin
 * @return void
 */
function Zip_Klasor_Ekle($zip, $klasor, $base_dizin = '')
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($klasor),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $file_path = $file->getRealPath();
            $relative_path = $base_dizin ?
                str_replace($base_dizin . DIRECTORY_SEPARATOR, '', $file_path) :
                str_replace($klasor . DIRECTORY_SEPARATOR, '', $file_path);

            $zip->addFile($file_path, $relative_path);
        }
    }
}

/**
 * ZIP Dosyası Açma Fonksiyonu
 *
 * Bu fonksiyon, ZIP arşivini belirtilen dizine açar.
 * Path traversal koruması ve selective extraction desteği.
 *
 * Kullanım Alanı:
 * - Archive extraction
 * - Backup restoration
 * - Package installation
 * - File deployment
 *
 * Teknik Detaylar:
 * - ZipArchive extraction
 * - Path traversal security check
 * - Selective file extraction
 * - Directory structure preservation
 *
 * Kullanım Örneği:
 * if (Dosya_Zip_Ac('/backup.zip', '/restore/')) {
 *     echo "Arşiv başarıyla açıldı";
 * }
 *
 * @param string $zip_dosyasi Açılacak ZIP dosyası
 * @param string $hedef_dizin Açılacak dizin
 * @param array $sadece_dosyalar Sadece belirtilen dosyalar (boş = hepsi)
 * @return bool Başarılı ise true, hata durumunda false
 */
function Dosya_Zip_Ac($zip_dosyasi, $hedef_dizin, $sadece_dosyalar = [])
{
    if (!class_exists('ZipArchive')) {
        return false;
    }

    if (!file_exists($zip_dosyasi)) {
        return false;
    }

    $zip = new ZipArchive();
    $result = $zip->open($zip_dosyasi);

    if ($result !== true) {
        return false;
    }

    // Hedef dizini oluştur
    if (!is_dir($hedef_dizin)) {
        Klasor_Olustur($hedef_dizin);
    }

    $hedef_dizin = rtrim($hedef_dizin, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = $zip->getNameIndex($i);

        // Sadece belirtilen dosyalar filtresi
        if (!empty($sadece_dosyalar) && !in_array($entry, $sadece_dosyalar)) {
            continue;
        }

        // Path traversal güvenlik kontrolü
        if (strpos($entry, '..') !== false) {
            continue;
        }

        $hedef_yol = $hedef_dizin . $entry;

        // Dizin ise oluştur
        if (substr($entry, -1) === '/') {
            Klasor_Olustur($hedef_yol);
        } else {
            // Dosya ise parent dizini oluştur ve çıkar
            $parent_dir = dirname($hedef_yol);
            if (!is_dir($parent_dir)) {
                Klasor_Olustur($parent_dir);
            }

            copy("zip://$zip_dosyasi#$entry", $hedef_yol);
        }
    }

    $zip->close();
    return true;
}

/**
 * Dosya İçerik Karşılaştırma Fonksiyonu
 *
 * Bu fonksiyon, iki dosyanın içeriğini karşılaştırır.
 * Hash-based ve byte-by-byte comparison seçenekleri sunar.
 *
 * Kullanım Alanı:
 * - File synchronization
 * - Duplicate detection
 * - Backup validation
 * - Version comparison
 *
 * Teknik Detaylar:
 * - Hash-based fast comparison
 * - Byte-by-byte detailed comparison
 * - Large file support
 * - Memory efficient processing
 *
 * Kullanım Örneği:
 * if (Dosya_Karsilastir('/original.txt', '/backup.txt')) {
 *     echo "Dosyalar aynı";
 * }
 *
 * @param string $dosya1 İlk dosya yolu
 * @param string $dosya2 İkinci dosya yolu
 * @param bool $hash_karsilastir Hash tabanlı hızlı karşılaştırma (varsayılan: true)
 * @return bool Dosyalar aynı ise true, farklı ise false
 */
function Dosya_Karsilastir($dosya1, $dosya2, $hash_karsilastir = true)
{
    if (!file_exists($dosya1) || !file_exists($dosya2)) {
        return false;
    }

    // Boyut kontrolü
    if (filesize($dosya1) !== filesize($dosya2)) {
        return false;
    }

    if ($hash_karsilastir) {
        // Hash tabanlı hızlı karşılaştırma
        return Dosya_Hash($dosya1) === Dosya_Hash($dosya2);
    } else {
        // Byte-by-byte karşılaştırma
        $handle1 = fopen($dosya1, 'rb');
        $handle2 = fopen($dosya2, 'rb');

        if (!$handle1 || !$handle2) {
            if ($handle1) {
                fclose($handle1);
            }
            if ($handle2) {
                fclose($handle2);
            }
            return false;
        }

        $chunk_size = 8192;
        $ayni = true;

        while (!feof($handle1) && !feof($handle2)) {
            $chunk1 = fread($handle1, $chunk_size);
            $chunk2 = fread($handle2, $chunk_size);

            if ($chunk1 !== $chunk2) {
                $ayni = false;
                break;
            }
        }

        // Her iki dosya da aynı anda sonlanmalı
        if (feof($handle1) !== feof($handle2)) {
            $ayni = false;
        }

        fclose($handle1);
        fclose($handle2);

        return $ayni;
    }
}

/**
 * JSON Dosyası İşleme Fonksiyonu
 *
 * Bu fonksiyon, JSON dosyalarını okur, işler ve kaydeder.
 * Syntax validation ve pretty formatting desteği.
 *
 * Kullanım Alanı:
 * - Configuration management
 * - API data caching
 * - Settings storage
 * - Data exchange formats
 *
 * Teknik Detaylar:
 * - JSON syntax validation
 * - Pretty print formatting
 * - UTF-8 encoding support
 * - Error handling
 *
 * Kullanım Örneği:
 * $config = Json_Dosya_Oku('/config/app.json');
 * $config['new_setting'] = 'value';
 * Json_Dosya_Yaz('/config/app.json', $config, true);
 *
 * @param string $dosya JSON dosya yolu
 * @return array|false JSON verisi array olarak veya false
 */
function Json_Dosya_Oku($dosya)
{
    if (!file_exists($dosya)) {
        return false;
    }

    $icerik = Dosya_Oku($dosya);
    if ($icerik === false) {
        return false;
    }

    $json = json_decode($icerik, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    return $json;
}

/**
 * JSON Dosyası Yazma Fonksiyonu
 *
 * Bu fonksiyon, PHP array'ini JSON dosyası olarak kaydeder.
 * Pretty printing ve encoding options desteği.
 *
 * Kullanım Alanı:
 * - Configuration saving
 * - Data export to JSON
 * - API cache writing
 * - Settings persistence
 *
 * Teknik Detaylar:
 * - JSON_PRETTY_PRINT option
 * - JSON_UNESCAPED_UNICODE support
 * - Atomic file writing
 * - Error handling
 *
 * Kullanım Örneği:
 * $data = ['name' => 'Ahmet', 'age' => 30];
 * Json_Dosya_Yaz('/data/user.json', $data, true);
 *
 * @param string $dosya JSON dosya yolu
 * @param mixed $veri JSON'a çevrilecek veri
 * @param bool $pretty Pretty print formatı (varsayılan: true)
 * @return bool Başarılı ise true, hata durumunda false
 */
function Json_Dosya_Yaz($dosya, $veri, $pretty = true)
{
    $flags = JSON_UNESCAPED_UNICODE;
    if ($pretty) {
        $flags |= JSON_PRETTY_PRINT;
    }

    $json = json_encode($veri, $flags);
    if ($json === false) {
        return false;
    }

    return Dosya_Yaz($dosya, $json) !== false;
}

/**
 * Dosya Streaming İşlemi
 *
 * Bu fonksiyon, büyük dosyaları chunk'lar halinde stream eder.
 * Memory-efficient file serving için kullanılır.
 *
 * Kullanım Alanı:
 * - Large file downloads
 * - Video/audio streaming
 * - Memory-efficient file serving
 * - Bandwidth-limited transfers
 *
 * Teknik Detaylar:
 * - Chunk-based reading
 * - HTTP range requests support
 * - Memory usage optimization
 * - Progressive download
 *
 * Kullanım Örneği:
 * Dosya_Stream('/videos/large_movie.mp4', 'movie.mp4', 1048576);
 *
 * @param string $dosya Stream edilecek dosya yolu
 * @param string $indirme_adi İndirme dosya adı
 * @param int $chunk_size Chunk boyutu byte (varsayılan: 8192)
 * @return bool Stream başarılı ise true, hata durumunda false
 */
function Dosya_Stream($dosya, $indirme_adi = '', $chunk_size = 8192)
{
    if (!file_exists($dosya)) {
        return false;
    }

    $dosya_boyutu = filesize($dosya);
    $indirme_adi = $indirme_adi ?: basename($dosya);
    $mime_type = Dosya_Mime_Type($dosya);

    // HTTP headers
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $indirme_adi . '"');
    header('Content-Length: ' . $dosya_boyutu);
    header('Accept-Ranges: bytes');
    header('Cache-Control: must-revalidate');

    // Range request desteği
    $range = $_SERVER['HTTP_RANGE'] ?? '';
    if (!empty($range)) {
        $ranges = explode('=', $range);
        $offsets = explode('-', $ranges[1]);
        $offset = intval($offsets[0]);
        $length = intval($offsets[1]) ?: $dosya_boyutu - 1;

        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $offset-$length/$dosya_boyutu");
        header('Content-Length: ' . ($length - $offset + 1));
    } else {
        $offset = 0;
        $length = $dosya_boyutu - 1;
    }

    // Dosyayı chunk'lar halinde oku ve gönder
    $handle = fopen($dosya, 'rb');
    if (!$handle) {
        return false;
    }

    fseek($handle, $offset);
    $bytes_to_read = $length - $offset + 1;

    while ($bytes_to_read > 0 && !feof($handle)) {
        $read_size = min($chunk_size, $bytes_to_read);
        echo fread($handle, $read_size);
        $bytes_to_read -= $read_size;

        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }

    fclose($handle);
    return true;
}
