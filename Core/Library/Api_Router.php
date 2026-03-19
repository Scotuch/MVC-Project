<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * API Router Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Api_Router
{
    private static $instance = null;
    private static $servisler = null;
    private static $apiKonum = null;
    private static $htmlKonum = null;
    private static $cacheKonum = null;

    public function __construct()
    {
        // Singleton örneğini ayarla.
        self::$instance = $this;

        // API klasörünü belirle.
        self::$apiKonum = (defined('KLASOR_API')) ? KLASOR_API : UYGULAMA . 'Api' . DS;
        self::$htmlKonum = self::$apiKonum . 'Html' . DS;
        self::$cacheKonum = (defined('KLASOR_CACHE')) ? KLASOR_CACHE : UYGULAMA . 'Cache' . DS;

        // Klasörleri oluştur.
        !is_dir(self::$apiKonum) && mkdir(self::$apiKonum, 0755, true);
        !is_dir(self::$htmlKonum) && mkdir(self::$htmlKonum, 0755, true);
        function_exists('Klasor_Olustur') && Klasor_Olustur(self::$apiKonum);
        function_exists('Klasor_Olustur') && Klasor_Olustur(self::$htmlKonum);
    }

    public static function Calistir($id = '', $istekler = '', $token = null)
    {
        if (self::$instance === null) {
            new self();
        }
        // API endpoint'lerini yükle.
        self::Liste();
        $servisler = self::$servisler;

        // İstenen endpoint mevcut mu?
        if (!isset($servisler[$id])) {
            $Mesaj = Mesaj('cakisma', 'Geçersiz API isteği: ' . $id, '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
            return;
        }

        $servislerAyar = $servisler[$id];

        // Endpoint özelliklerini kontrol et
        $ozellikKontrol = self::Ozellik_Kontrol($servislerAyar, $id);
        if ($ozellikKontrol !== true) {
            return $ozellikKontrol; // Hata mesajını döndür
        }

        // Sınıf dosyasını yükle
        $dosyaYolu = self::$apiKonum . basename($servislerAyar['dosya']);
        if (file_exists($dosyaYolu)) {
            require_once $dosyaYolu;
        }

        if (!class_exists($servislerAyar['sinif'])) {
            $Mesaj = Mesaj('cakisma', 'API sınıfı bulunamadı: ' . $servislerAyar['sinif'], '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
            return;
        }

        if (!method_exists($servislerAyar['sinif'], $servislerAyar['metod'])) {
            $Mesaj = Mesaj('cakisma', 'API metodu bulunamadı: ' . $servislerAyar['metod'], '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
            return;
        }

        // API metodunu çağır.
        return call_user_func_array(
            [$servislerAyar['sinif'], $servislerAyar['metod']],
            [$istekler, $token]
        );
    }

    /**
     * Endpoint özelliklerini kontrol et (GET, POST, TOKEN vb.)
     * @param array $servisAyar
     * @param string $endpointId
     * @return bool|object true veya hata mesajı
     */
    private static function Ozellik_Kontrol($servisAyar, $endpointId)
    {
        // Sınıfın özellikleri var mı kontrol et
        if (!isset($servisAyar['ozellikler']) || empty($servisAyar['ozellikler'])) {
            // Özellik tanımlanmamışsa, varsayılan olarak tüm metodlara ve token kontrolüne izin ver
            return true;
        }

        $ozellikler = $servisAyar['ozellikler'];
        $aktifMetod = $_SERVER['REQUEST_METHOD'];

        // 1. HTTP Method kontrolü
        $metodIzni = false;
        $izinliMetodlar = [];

        foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'] as $method) {
            if (isset($ozellikler[$method]) && $ozellikler[$method] === true) {
                $izinliMetodlar[] = $method;
                if ($method === $aktifMetod) {
                    $metodIzni = true;
                }
            }
        }

        if (!$metodIzni && !empty($izinliMetodlar)) {
            $Mesaj = Mesaj(
                'izinYok',
                "Bu endpoint için {$aktifMetod} metoduna izin verilmiyor.",
                '',
                true,
                [
                    'endpoint' => $endpointId,
                    'kullanilan_metod' => $aktifMetod,
                    'izinli_metodlar' => implode(', ', $izinliMetodlar)
                ]
            );
            Log::Dikkat(1, "Endpoint: {$endpointId} - İzinli olmayan metod kullanıldı: {$aktifMetod}");
            http_response_code($Mesaj->kod);
            return $Mesaj;
        }

        // 2. TOKEN kontrolü - Eğer TOKEN = false ise, token kontrolünü atla
        $tokenGerekli = isset($ozellikler['TOKEN']) ? $ozellikler['TOKEN'] : true; // Varsayılan: token gerekli

        if ($tokenGerekli === false) {
            // Token gerekmiyor, başarılı
            Log::Debug(1, "Endpoint: {$endpointId} - Token kontrolü atlandı (TOKEN=false)", defined('GELISTIRICI') && GELISTIRICI);
            return true;
        }

        return true;
    }

    public static function Liste()
    {
        // Cache kontrolü
        $cachedData = self::cacheOku();
        if ($cachedData !== null) {
            self::$servisler = $cachedData;
            return self::$servisler;
        }

        $dosyalar = glob(self::$apiKonum . '*.php');

        foreach ($dosyalar as $dosya) {
            if (!file_exists($dosya)) continue;

            $dosyaAdi = basename($dosya, '.php');
            $sinifAdi = $dosyaAdi . '_Api';

            require_once $dosya;

            if (!class_exists($sinifAdi)) continue;

            // Servis bilgilerini al
            $servisBilgi = method_exists($sinifAdi, 'Servis_Bilgi')
                ? call_user_func([$sinifAdi, 'Servis_Bilgi'])
                : [];

            // Endpoint özelliklerini al
            $endpointOzellikleri = method_exists($sinifAdi, 'Ozellikler')
                ? call_user_func([$sinifAdi, 'Ozellikler'])
                : [];

            // Sınıf metodlarını işle
            try {
                $yansitma = new ReflectionClass($sinifAdi);
                $metodlar = $yansitma->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);

                foreach ($metodlar as $metod) {
                    $metodAdi = $metod->getName();

                    if (in_array($metodAdi, ['Servis_Bilgi', 'Ozellikler'])) continue;

                    $endpointAdi = self::servisAdi($dosyaAdi, $metodAdi);

                    // Bu endpoint için özellikler var mı kontrol et
                    $ozellikler = isset($endpointOzellikleri[$endpointAdi])
                        ? $endpointOzellikleri[$endpointAdi]
                        : [];

                    self::$servisler[$endpointAdi] = [
                        'dosya' => $dosyaAdi . '.php',
                        'sinif' => $sinifAdi,
                        'metod' => $metodAdi,
                        'ozellikler' => $ozellikler,  // Özellikleri ekle
                        'bilgi' => [
                            'isim' => $servisBilgi['isim'] ?? '',
                            'versiyon' => $servisBilgi['versiyon'] ?? '',
                            'aciklama' => $servisBilgi['aciklama'] ?? '',
                            'endpoint_aciklama' => $servisBilgi['servisler'][$endpointAdi] ?? ''
                        ]
                    ];
                }
            } catch (ReflectionException $e) {
                continue;
            }
        }

        // Cache'e kaydet
        self::cacheYaz(self::$servisler);

        return self::$servisler;
    }

    private static function servisAdi($dosyaAdi, $metodAdi)
    {
        $dosyaAdi = strtolower($dosyaAdi);
        $metodAdi = strtolower($metodAdi);
        switch ($metodAdi) {
            case 'calistir':
                $c = $dosyaAdi;
                break;
            default:
                $c = "{$dosyaAdi}_{$metodAdi}";
        }
        return $c;
    }

    /**
     * Belirli bir endpoint'in özelliklerini al
     * @param string $endpointId
     * @return array|null
     */
    public static function Endpoint_Ozelliklerini_Al($endpointId)
    {
        if (self::$instance === null) {
            new self();
        }

        // Servisleri yükle
        if (self::$servisler === null) {
            self::Liste();
        }

        // Endpoint var mı kontrol et
        if (!isset(self::$servisler[$endpointId])) {
            return null;
        }

        // Özellikleri döndür
        return self::$servisler[$endpointId]['ozellikler'] ?? [];
    }

    /**
     * Cache'den API listesini oku
     * @return array|null
     */
    private static function cacheOku()
    {
        $cacheDosya = self::$cacheKonum . 'api_router.cache';

        if (!file_exists($cacheDosya)) {
            return null;
        }

        // API dosyalarında değişiklik kontrolü
        if (self::apiDosyalariDegisti($cacheDosya)) {
            @unlink($cacheDosya);
            return null;
        }

        $data = @file_get_contents($cacheDosya);
        return $data ? unserialize($data) : null;
    }

    /**
     * API listesini cache'e yaz
     * @param array $data
     */
    private static function cacheYaz($data)
    {
        $cacheDosya = self::$cacheKonum . 'api_router.cache';
        !is_dir(self::$cacheKonum) && mkdir(self::$cacheKonum, 0755, true);
        @file_put_contents($cacheDosya, serialize($data));
    }

    /**
     * API dosyalarında değişiklik olup olmadığını kontrol et
     * @param string $cacheDosya
     * @return bool
     */
    private static function apiDosyalariDegisti($cacheDosya)
    {
        $cacheZamani = filemtime($cacheDosya);
        $dosyalar = glob(self::$apiKonum . '*.php');

        foreach ($dosyalar as $dosya) {
            if (filemtime($dosya) > $cacheZamani) {
                return true; // Dosya değişmiş
            }
        }

        // Dosya sayısı değişti mi kontrol et
        $cachedData = @unserialize(@file_get_contents($cacheDosya));
        if ($cachedData && count($dosyalar) !== count(array_unique(array_column($cachedData, 'dosya')))) {
            return true; // Yeni dosya eklendi veya silindi
        }

        return false;
    }

    /**
     * Request parametrelerini doğrula
     * 
     * @param array $istekler Gelen parametreler
     * @param array $kurallar Doğrulama kuralları
     * @return array ['gecerli' => bool, 'hatalar' => array]
     */
    public static function Validate($istekler, $kurallar)
    {
        $hatalar = [];

        foreach ($kurallar as $alan => $kural) {
            $deger = $istekler[$alan] ?? null;
            $kurallar_array = is_array($kural) ? $kural : explode('|', $kural);

            foreach ($kurallar_array as $tek_kural) {
                [$kural_adi, $parametre] = array_pad(explode(':', $tek_kural, 2), 2, null);

                switch ($kural_adi) {
                    case 'required':
                        if (empty($deger) && $deger !== '0') {
                            $hatalar[$alan][] = "{$alan} alanı zorunludur.";
                        }
                        break;

                    case 'numeric':
                        if (!empty($deger) && !is_numeric($deger)) {
                            $hatalar[$alan][] = "{$alan} sayısal olmalıdır.";
                        }
                        break;

                    case 'string':
                        if (!empty($deger) && !is_string($deger)) {
                            $hatalar[$alan][] = "{$alan} metin olmalıdır.";
                        }
                        break;

                    case 'email':
                        if (!empty($deger) && !filter_var($deger, FILTER_VALIDATE_EMAIL)) {
                            $hatalar[$alan][] = "{$alan} geçerli bir e-posta olmalıdır.";
                        }
                        break;

                    case 'min':
                        if (!empty($deger) && strlen($deger) < $parametre) {
                            $hatalar[$alan][] = "{$alan} en az {$parametre} karakter olmalıdır.";
                        }
                        break;

                    case 'max':
                        if (!empty($deger) && strlen($deger) > $parametre) {
                            $hatalar[$alan][] = "{$alan} en fazla {$parametre} karakter olmalıdır.";
                        }
                        break;

                    case 'array':
                        if (!empty($deger) && !is_array($deger)) {
                            $hatalar[$alan][] = "{$alan} dizi olmalıdır.";
                        }
                        break;

                    case 'in':
                        $izin_verilenler = explode(',', $parametre);
                        if (!empty($deger) && !in_array($deger, $izin_verilenler)) {
                            $hatalar[$alan][] = "{$alan} şu değerlerden biri olmalıdır: {$parametre}";
                        }
                        break;
                }
            }
        }

        return [
            'gecerli' => empty($hatalar),
            'hatalar' => $hatalar
        ];
    }

    /**
     * Tüm API endpoint'lerinin dokümantasyonunu döndür
     * 
     * @param string $format Çıktı formatı: 'json', 'html', 'array'
     * @return mixed
     */
    public static function Dokumanlar($format = 'array')
    {
        self::$instance ??= new self();
        $servisler = self::Liste();
        return Api_Dokuman::Olustur($servisler, $format);
    }

    /**
     * HTML dosyası yükle ve içeriğini döndür
     * 
     * @param string $dosya HTML dosya adı (örn: Anasayfa veya Kullanici_Bilgi)
     * @param array|null $veri HTML'e aktarılacak değişkenler
     * @return string HTML içeriği
     * @throws Exception Dosya bulunamazsa veya okunamazsa
     */
    public static function HTML($dosya, $veri = null)
    {
        self::$instance ??= new self();

        // Dosya adını formatlama: Aaaa_Bbbb_Cccc formatına çevir
        $dosya = basename($dosya, '.php');
        $dosya = ucwords(strtolower($dosya), '_');
        $dosya .= '.php';
        $yol = self::$htmlKonum . $dosya;

        if (!file_exists($yol)) {
            $Mesaj = Mesaj('hata', "HTML dosyası bulunamadı: {$yol}", '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
        }

        if (!is_readable($yol)) {
            $Mesaj = Mesaj('yetki', "HTML dosyası okunamıyor: {$dosya}", '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
        }

        ob_start();
        is_array($veri) && extract($veri, EXTR_SKIP);
        include $yol;
        Log::Bilgi(1, "HTML yüklendi: {$dosya}");
        return ob_get_clean();
    }
}
