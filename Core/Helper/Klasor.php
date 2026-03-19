<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Klasör fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Klasör Varlık Kontrol Fonksiyonu
 *
 * Bu fonksiyon, belirtilen yolda bir klasörün mevcut olup olmadığını kontrol eder.
 * Basit boolean dönüş ile klasör varlığını doğrular.
 *
 * Kullanım Alanı:
 * - Dosya sistemi operasyonlarından önce kontrol
 * - Upload directory validasyonunda
 * - Backup location verification'da
 * - Path existence checking'de
 *
 * Teknik Detaylar:
 * - is_dir() PHP function'unu wrap eder
 * - Boolean return type
 * - Path validation yapar
 * - Cross-platform uyumluluğu
 *
 * Kullanım Örneği:
 * if (Klasor_Kontrol('/uploads/temp')) {
 *     echo "Temp klasörü mevcut";
 * } else {
 *     echo "Klasör bulunamadı";
 * }
 *
 * @param string $klasor Kontrol edilecek klasör yolu
 * @return bool Klasör varsa true, yoksa false
 */
function Klasor_Kontrol($klasor)
{
    return is_dir($klasor);
}

/**
 * Klasör İçeriği Okuma Fonksiyonu
 *
 * Bu fonksiyon, belirtilen klasördeki tüm dosya ve alt klasörlerin
 * listesini döndürür. Sistem dosyalarını (. ve ..) otomatik filtreler.
 *
 * Kullanım Alanı:
 * - Dosya listeleme işlemlerinde
 * - Directory browsing sistemlerinde
 * - File management uygulamalarında
 * - Backup işlemlerinde inventory almada
 *
 * Teknik Detaylar:
 * - opendir/readdir/closedir kullanır
 * - '.' ve '..' gizli dosyalarını filtreler
 * - Safe directory handling
 * - Array formatında sonuç döndürür
 *
 * Kullanım Örneği:
 * $dosyalar = Klasor_Oku('/uploads');
 * foreach ($dosyalar as $dosya) {
 *     echo "Dosya: " . $dosya . "\n";
 * }
 *
 * @param string $klasor Okunacak klasör yolu
 * @return array Klasördeki dosya ve klasör adları array'i
 */
function Klasor_Oku($klasor)
{
    $dosyalar = [];
    if (is_dir($klasor)) {
        $dizin = opendir($klasor);
        while (($dosya = readdir($dizin)) !== false) {
            if ($dosya != '.' && $dosya != '..') {
                $dosyalar[] = $dosya;
            }
        }
        closedir($dizin);
    }
    return $dosyalar;
}

/**
 * Klasör Oluşturma Fonksiyonu
 *
 * Bu fonksiyon, belirtilen yolda klasör oluşturur. Gerekirse parent
 * directory'leri de otomatik olarak oluşturur (recursive creation).
 *
 * Kullanım Alanı:
 * - Upload directory'lerinin hazırlanmasında
 * - Log dosyası dizinlerinin oluşturulmasında
 * - Cache folder'larının kurulumunda
 * - Dynamic directory structure'larında
 *
 * Teknik Detaylar:
 * - mkdir() with recursive flag (true)
 * - 0777 permissions (system dependent)
 * - Existence check before creation
 * - Boolean return for success/failure
 *
 * Kullanım Örneği:
 * if (Klasor_Olustur('/var/www/uploads/2025/01')) {
 *     echo "Klasör başarıyla oluşturuldu";
 * } else {
 *     echo "Klasör oluşturulamadı";
 * }
 *
 * @param string $id Oluşturulacak klasörün tam yolu
 * @return bool Başarılı ise true, hata durumunda false
 */
function Klasor_Olustur($id)
{
    if (!file_exists($id)) {
        if (mkdir($id, 0777, true)) {
            return true;
        } else {
            return false;
        }
    }
    return true;
}

/**
 * Recursive Klasör Silme Fonksiyonu
 *
 * Bu fonksiyon, belirtilen klasörü ve içeriğini tamamen siler.
 * Alt klasörler ve tüm dosyaları recursive olarak temizler.
 *
 * Kullanım Alanı:
 * - Temp directory cleanup işlemlerinde
 * - Cache temizleme operasyonlarında
 * - Upload folder reset'lerinde
 * - System maintenance'da
 *
 * Teknik Detaylar:
 * - Recursive directory traversal
 * - scandir() ile içerik listeleme
 * - unlink() dosyalar için, rmdir() klasörler için
 * - '.' ve '..' filtering
 *
 * UYARI: Bu fonksiyon kalkalıcı silme yapar, dikkatli kullanın!
 *
 * Kullanım Örneği:
 * if (Klasor_Sil('/temp/cache')) {
 *     echo "Cache klasörü temizlendi";
 * } else {
 *     echo "Silme işlemi başarısız";
 * }
 *
 * @param string $klasor Silinecek klasörün tam yolu
 * @return bool Başarılı ise true, hata durumunda false
 */
function Klasor_Sil($klasor)
{
    if (!is_dir($klasor)) {
        return false;
    }
    $dosyalar = array_diff(scandir($klasor), ['.', '..']);
    foreach ($dosyalar as $dosya) {
        $yol = $klasor . DIRECTORY_SEPARATOR . $dosya;
        is_dir($yol) ? Klasor_Sil($yol) : unlink($yol);
    }
    return rmdir($klasor);
}

/**
 * Klasör Boyutu Hesaplama Fonksiyonu
 *
 * Bu fonksiyon, belirtilen klasördeki tüm dosyaların toplam boyutunu
 * hesaplar ve insan okunabilir formatta döndürür.
 *
 * Kullanım Alanı:
 * - Disk kullanım analizlerinde
 * - Storage quota kontrollerinde
 * - Backup boyutu hesaplamalarında
 * - System monitoring'de
 *
 * Teknik Detaylar:
 * - RecursiveIteratorIterator kullanır
 * - RecursiveDirectoryIterator ile traversal
 * - getSize() method'u ile boyut alma
 * - Byte_Cevirici() ile format dönüşümü
 *
 * Kullanım Örneği:
 * $boyut = Klasor_Boyut('/var/www/uploads');
 * echo "Upload klasörü boyutu: " . $boyut;
 *
 * @param string $klasor Boyutu hesaplanacak klasör yolu
 * @return string Formatlanmış boyut bilgisi (MB, GB, vb.)
 */
function Klasor_Boyut($klasor)
{
    $boyut = 0;
    if (is_dir($klasor)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($klasor, FilesystemIterator::SKIP_DOTS)) as $dosya) {
            $boyut += $dosya->getSize();
        }
    }
    return Boyut($boyut);
}

/**
 * Klasör Adı Değiştirme Fonksiyonu
 *
 * Bu fonksiyon, mevcut bir klasörün adını değiştirir (rename operation).
 * Hedef adın mevcut olmadığını kontrol eder.
 *
 * Kullanım Alanı:
 * - Dosya yönetim sistemlerinde
 * - Backup folder organizasyonunda
 * - Dynamic directory management'da
 * - User folder customization'da
 *
 * Teknik Detaylar:
 * - rename() PHP function kullanır
 * - Source directory existence check
 * - Target name collision check
 * - Atomic rename operation
 *
 * Kullanım Örneği:
 * if (Klasor_Adi_Degistir('/old_folder', '/new_folder')) {
 *     echo "Klasör adı başarıyla değiştirildi";
 * } else {
 *     echo "Ad değiştirme başarısız";
 * }
 *
 * @param string $klasorYolu Mevcut klasörün tam yolu
 * @param string $yeniAd Yeni klasör adı/yolu
 * @return bool Başarılı ise true, hata durumunda false
 */
function Klasor_Adi_Degistir($klasorYolu, $yeniAd)
{
    if (is_dir($klasorYolu) && !file_exists($yeniAd)) {
        return rename($klasorYolu, $yeniAd);
    }
    return false;
}

/**
 * Klasör Boşluk Kontrol Fonksiyonu
 *
 * Bu fonksiyon, belirtilen klasörün boş olup olmadığını kontrol eder.
 * Gizli dosyalar (örn: .htaccess) hariç hiçbir dosya içermez ise boş sayılır.
 *
 * Kullanım Alanı:
 * - Klasör silme işlemlerinden önce kontrol
 * - Upload directory hazir hale getirmede
 * - Cleanup operasyonlarında validation
 * - Directory state checking'de
 *
 * Teknik Detaylar:
 * - scandir() ile içerik listeleme
 * - '.' ve '..' system entries filtering
 * - Count-based emptiness check
 * - Boolean return type
 *
 * Kullanım Örneği:
 * if (Klasor_Bos_Mu('/temp/uploads')) {
 *     echo "Klasör boş, güvenle silinebilir";
 * } else {
 *     echo "Klasörde dosyalar var";
 * }
 *
 * @param string $klasor Kontrol edilecek klasör yolu
 * @return bool Boş ise true, dosya varsa false
 */
function Klasor_Bos_Mu($klasor)
{
    if (is_dir($klasor)) {
        return count(array_diff(scandir($klasor), ['.', '..'])) === 0;
    }
    return false;
}
