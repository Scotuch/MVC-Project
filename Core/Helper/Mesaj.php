<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Mesaj fonksiyonu
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Sistem Mesaj Oluşturucu Fonksiyonu
 *
 * Bu fonksiyon, sistem mesajları için standart format oluşturur. Başarı, bilgi,
 * dikkat, hata, yetki ve debug durumları için uygun response objesi/JSON üretir.
 *
 * Kullanım Alanı:
 * - API response'larında standart mesaj formatı
 * - AJAX isteklerinde uniform cevap yapısı
 * - Form işlem sonuçlarının kullanıcıya iletilmesinde
 * - System notification'larda
 * - Yetki kontrol mesajlarında
 * - Debug ve geliştirici bildirimlerinde
 *
 * Teknik Detaylar:
 * - HTTP status code ile uyumlu kod atama (200, 202, 403, 404, 418, 500)
 * - Her durum için Font Awesome ikon değeri (response'ta ikon alanı)
 * - JSON/Object dual return mode
 * - URL sanitization ile güvenlik
 * - Url_Olustur() function integration
 * - Ek veri desteği ile genişletilebilir yapı
 *
 * Durum Kodları ve Karşılıkları:
 * - 'basarili'      => 200 (HTTP OK - Başarılı işlem)
 * - 'bilgi'         => 202 (HTTP Accepted - Bilgilendirme)
 * - 'sessiz'        => 204 (HTTP No Content - Sessiz başarı)
 * - 'gecersizIstek' => 400 (HTTP Bad Request - Geçersiz istek)
 * - 'oturum'        => 401 (HTTP Unauthorized - Oturum hatası)
 * - 'yetki'         => 403 (HTTP Forbidden - Yetki hatası)
 * - 'hata'          => 404 (HTTP Not Found - Genel hata)
 * - 'izinYok'       => 405 (HTTP Method Not Allowed - İzin verilmeyen metot)
 * - 'zamanAsimi'    => 408 (HTTP Request Timeout - İstek zaman aşımı)
 * - 'cakisma'       => 409 (HTTP Conflict - Veri çakışması)
 * - 'kayipKaynak'   => 410 (HTTP Gone - Kaynak kalıcı olarak kaldırılmış)
 * - 'fazlaBuyuk'    => 413 (HTTP Payload Too Large - Çok büyük veri)
 * - 'debug'         => 418 (I'm a teapot - Debug bilgisi)
 * - 'validasyon'    => 422 (HTTP Unprocessable Entity - Validasyon hatası)
 * - 'limit'         => 429 (HTTP Too Many Requests - Rate limit)
 * - 'dikkat'        => 500 (HTTP Internal Server Error - Dikkat/Uyarı)
 * - 'agGecidi'      => 502 (HTTP Bad Gateway - Kötü ağ geçidi)
 * - 'servis'        => 503 (HTTP Service Unavailable - Servis hatası)
 * - 'agZamanAsimi'  => 504 (HTTP Gateway Timeout - Ağ geçidi zaman aşımı)
 *
 * Kullanım Örnekleri:
 * $response = Mesaj('basarili', 'Kayıt tamamlandı', 'dashboard', true);
 * $json = Mesaj('hata', 'Geçersiz veri');
 * $refresh = Mesaj('bilgi', 'Sayfa yenileniyor', 'yenile');
 * $sessiz = Mesaj('sessiz', 'İşlem tamam'); // Bildirim gösterilmez
 * $gecersizIstek = Mesaj('gecersizIstek', 'Gönderilen veri formatı geçersiz');
 * $oturum = Mesaj('oturum', 'Oturumunuz sonlanmış', 'giris');
 * $yetki = Mesaj('yetki', 'Bu işlem için yetkiniz bulunmuyor');
 * $izinYok = Mesaj('izinYok', 'Bu metot izin verilmiyor');
 * $zamanAsimi = Mesaj('zamanAsimi', 'İstek zaman aşımına uğradı');
 * $cakisma = Mesaj('cakisma', 'Bu email adresi zaten kayıtlı');
 * $kayipKaynak = Mesaj('kayipKaynak', 'Bu içerik artık mevcut değil');
 * $fazlaBuyuk = Mesaj('fazlaBuyuk', 'Yüklenen dosya çok büyük');
 * $debug = Mesaj('debug', 'Değişken değeri: test', '', true, ['data' => $test]);
 * $validasyon = Mesaj('validasyon', 'Email formatı hatalı');
 * $limit = Mesaj('limit', 'Çok fazla deneme yaptınız, lütfen bekleyin');
 * $agGecidi = Mesaj('agGecidi', 'Ağ geçidi hatası oluştu');
 * $servis = Mesaj('servis', 'Veritabanı bağlantısı kurulamadı');
 * $agZamanAsimi = Mesaj('agZamanAsimi', 'Ağ geçidi zaman aşımı');
 * $extra = Mesaj('basarili', 'İşlem tamam', '', false, ['id' => 123, 'tip' => 'kayit']);
 *
 * @param string $durum Mesaj durumu ('basarili', 'bilgi', 'sessiz', 'gecersizIstek', 'oturum', 'yetki', 'hata', 'izinYok', 'zamanAsimi', 'cakisma', 'kayipKaynak', 'fazlaBuyuk', 'debug', 'validasyon', 'limit', 'dikkat', 'agGecidi', 'servis', 'agZamanAsimi')
 * @param string $mesaj Gösterilecek mesaj metni
 * @param string $url Yönlendirilecek URL ('yenile' = sayfayı yenile, boş = yönlendirme yok)
 * @param bool $object Object dön mü (true) yoksa JSON string mi (false) - varsayılan: false
 * @param array $ekVeri Mesaja eklenecek ek veri dizisi - varsayılan: []
 * @return stdClass|string|false Mesaj objesi, JSON string veya false
 */
function Mesaj($durum = '', $mesaj = '', $url = '', $object = false, $ekVeri = [])
{
    if (!$durum) {
        return false;
    }

    // Durum kodları, başlıkları ve Font Awesome ikonları (daha kolay bakım için array kullanımı)
    $durumlar = [
        'basarili'      => [200, 'Başarılı', 'fas fa-check-circle'],
        'bilgi'         => [202, 'Bilgi', 'fas fa-info-circle'],
        'sessiz'        => [204, '', 'fas fa-check'],
        'gecersizIstek' => [400, 'Geçersiz İstek', 'fas fa-exclamation-triangle'],
        'oturum'        => [401, 'Oturum Hatası', 'fas fa-user-clock'],
        'yetki'         => [403, 'Yetki Hatası', 'fas fa-lock'],
        'hata'          => [404, 'Hata', 'fas fa-times-circle'],
        'izinYok'       => [405, 'İzin Verilmeyen Metot', 'fas fa-ban'],
        'zamanAsimi'    => [408, 'İstek Zaman Aşımı', 'fas fa-clock'],
        'cakisma'       => [409, 'Veri Çakışması', 'fas fa-exclamation-triangle'],
        'kayipKaynak'   => [410, 'Kaynak Kaldırılmış', 'fas fa-unlink'],
        'fazlaBuyuk'    => [413, 'Çok Büyük Veri', 'fas fa-file-exclamation'],
        'debug'         => [418, 'Debug Bilgisi', 'fas fa-bug'],
        'validasyon'    => [422, 'Geçersiz Veri', 'fas fa-exclamation-circle'],
        'limit'         => [429, 'Çok Fazla İstek', 'fas fa-hourglass-half'],
        'dikkat'        => [500, 'Dikkat', 'fas fa-exclamation-triangle'],
        'agGecidi'      => [502, 'Ağ Geçidi Hatası', 'fas fa-network-wired'],
        'servis'        => [503, 'Servis Kullanılamıyor', 'fas fa-server'],
        'agZamanAsimi'  => [504, 'Ağ Geçidi Zaman Aşımı', 'fas fa-clock']
    ];

    // Durum kontrolü ve atama
    if (isset($durumlar[$durum])) {
        [$kod, $baslik, $ikon] = $durumlar[$durum];
    } else {
        $kod = 400;
        $baslik = '';
        $ikon = 'fas fa-question-circle';
    }

    $a = new stdClass();
    $a->kod = $kod;
    $a->baslik = $baslik;
    $a->ikon = $ikon;
    if ($mesaj) {
        $a->mesaj = $mesaj;
    }

    $url = filter_var($url, FILTER_SANITIZE_URL);

    if ($url == 'yenile') {
        $a->url = 'yenile';
    } elseif ($url != '') {
        $a->url = Url_Olustur($url);
    }

    if (!empty($ekVeri) && is_array($ekVeri)) {
        foreach ($ekVeri as $anahtar => $deger) {
            $a->$anahtar = $deger;
        }
    }

    if ($object == true) {
        return $a;
    }
    return Json_Olustur($a);
}
