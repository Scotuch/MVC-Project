<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * API Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Api
{
    private $ip;
    private $istekKlasor;
    private $istekDosya;
    private $istekSayisi;
    private $istekTuru;
    private $istekler;
    private $istekSorunlari = [];
    private $guvenlikSorunlari = [];
    private $hataMesajlari = [];
    private $token;

    public function __construct()
    {
        $this->ip = IP_Adresi();
        $this->istekKlasor = UYGULAMA . 'Cache' . DS . 'Istekler' . DS;
        Klasor_Olustur($this->istekKlasor);
        $this->istekDosya = $this->istekKlasor . Sha256_Olustur($this->ip) . '.cache';
        $this->istekSayisi = API_RATE_LIMIT_SAYISI;
    }

    public function Calistir()
    {
        try {


            // İstek işlemlerini başlat.
            $this->Istek();

            // CORS başlıklarını ayarla.
            $this->Cors();

            // Güvenlik kontrolerini yap.
            $this->Guvenlik();

            // Hız limitleme kontrollerini yap.
            $this->Hiz();

            // Verileri hazırla.
            //$this->Veri_Hazirla();

            // İstek verilerini işle.
            //$this->Veriler();

            // Kimlik doğrulamasını yap.
            $this->Kimlik();

            // Endpoint işlemini yürüt.
            $this->Yurut();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function Istek()
    {
        // İstek bilgilerini al ve doğrula.
        if (empty($_SERVER['REQUEST_METHOD'])) {
            $Mesaj = Mesaj('yetki', 'Geçersiz istek türü.', '', true);
            Log::Dikkat(1, 'Geçersiz istek türü. IP: ' . $this->ip);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return;
        } else {
            $this->istekTuru = $_SERVER['REQUEST_METHOD'] ?? '';
        }

        // Verileri hazırla.
        $this->Veri_Hazirla();

        // ID kontrolü yap.
        if (!isset($this->istekler->id) || empty($this->istekler->id)) {
            $Mesaj = Mesaj(
                'servis',
                'API isteğinizde "id" parametresi eksik. Daha fazla bilgi için API dokümantasyonuna bakınız.',
                '',
                true,
                [
                    'hata' => 'id parametresi zorunludur',
                    'ornekler' => [
                        'GET /api.php?id=token',
                        'POST /api.php {"id": "test"}'
                    ],
                    'dokuman' => TRUE,
                    'dokuman_link' => (function_exists('Url_Olustur') ? Url_Olustur('dokuman', true) : null),
                    'istekTuru' => $this->istekTuru,
                    'token' => $this->istekler->token ?? '',
                    'zaman' => (function_exists('Tarih_Saat') ? Tarih_Saat() : date('Y-m-d H:i:s')),
                ]
            );
            http_response_code($Mesaj->kod);
            die(Json_Olustur($Mesaj));
        }
    }

    private function Cors()
    {
        // Cors bilgileri eklenecek.

        $this->istekTuru = $_SERVER['REQUEST_METHOD'] ?? '';
    }

    private function Guvenlik()
    {
        // Güvenlik başlıklarını kontrol et.
        $this->guvenlikSorunlari = $this->Guvenlik_Baslik();
        if (!empty($this->guvenlikSorunlari)) {
            $Mesaj = Mesaj('yetki', 'Güvenlik ihlali tespit edildi.', '', true);
            Log::Dikkat(1, 'Güvenlik uyarıları: ' . implode(', ', $this->guvenlikSorunlari));
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return;
        }

        // Request size kontrolü.
        $maxSize = (defined('API_MAX_REQUEST_SIZE') ? API_MAX_REQUEST_SIZE : 1) * 1024 * 1024;
        if (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > $maxSize) {
            $Mesaj = Mesaj('fazlaBuyuk', 'İstek boyutu çok büyük.', '', true);
            Log::Hata(1, 'İstek boyutu çok büyük: ' . $_SERVER['CONTENT_LENGTH']);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return;
        }
    }

    private function Guvenlik_Baslik()
    {
        $sorunlar = [];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        if (empty($userAgent)) {
            $sorunlar[] = 'User-Agent başlığı eksik.';
        } elseif (strlen($userAgent) < 10) {
            $sorunlar[] = 'Çok kısa User-Agent başlığı.';
        } elseif (preg_match('/(sqlmap|nikto|acunetix|havij|fimap|dirbuster|nessus)/i', $userAgent)) {
            $sorunlar[] = "Şüpheli User-Agent tespiti. => {$userAgent}";
        } elseif (!preg_match('/^[a-zA-Z0-9 ._\/\-\(\);:]+$/', $userAgent) === 0) {
            $sorunlar[] = "Geçersiz karakterler içeren User-Agent başlığı. => {$userAgent}";
        } elseif (!defined('API_IZINLI_METHODLAR') || !in_array($requestMethod, API_IZINLI_METHODLAR)) {
            $sorunlar[] = 'İzin verilmeyen HTTP metodu: ' . $requestMethod;
        }
        return $sorunlar;
    }

    private function Hiz()
    {
        $now = time();
        $istekler = [];
        $fp = null;

        try {
            // Atomik okuma için dosya kilidi
            $fp = fopen($this->istekDosya, 'c+');
            if ($fp === false) {
                Log::Dikkat(1, 'Rate limit dosyası açılamadı, geçiliyor. IP: ' . $this->ip);
                return;
            }

            // Özel (exclusive) kilit - diğer istekleri beklet
            if (!flock($fp, LOCK_EX)) {
                Log::Dikkat(1, 'Rate limit dosyası kilitlenemedi. IP: ' . $this->ip);
                fclose($fp);
                return;
            }

            // Dosya içeriğini oku
            $fileSize = filesize($this->istekDosya);
            if ($fileSize > 0) {
                $content = fread($fp, $fileSize);
                if ($content !== false) {
                    $decoded = json_decode($content, true);
                    if (is_array($decoded)) {
                        $istekler = $decoded;
                    }
                }
            }

            // Zaman penceresi (60 saniye)
            $zamanPenceresi = (defined('API_RATE_LIMIT_SANIYE') ? API_RATE_LIMIT_SANIYE : 60);
            $sinirZamani = $now - $zamanPenceresi;

            // Eski istekleri temizle (sadece geçerli olanları tut)
            // array_filter yerine foreach ile daha hızlı
            $gecerliIstekler = [];
            foreach ($istekler as $timestamp) {
                if ($timestamp > $sinirZamani) {
                    $gecerliIstekler[] = $timestamp;
                }
            }

            // Erken kontrol - limit kontrolü yeni istek eklenmeden önce
            $mevcutIstek = count($gecerliIstekler);
            if ($mevcutIstek >= $this->istekSayisi) {
                // En eski isteğe kadar beklemesi gereken süreyi hesapla
                $enEskiIstek = min($gecerliIstekler);
                $kalanSure = $zamanPenceresi - ($now - $enEskiIstek);
                Log::Hata(1, sprintf(
                    'Rate limit aşıldı. IP: %s, İstek: %d/%d, Kalan süre: %ds',
                    $this->ip,
                    $mevcutIstek,
                    $this->istekSayisi,
                    $kalanSure
                ));
                $Mesaj = Mesaj('limit', "Çok fazla istek yapıldı. {$kalanSure} saniye sonra tekrar deneyin.", '', true);
                $this->hataMesajlari[] = $Mesaj->mesaj;

                // Rate limit başlığı ekle (RFC 6585)
                header('X-RateLimit-Limit: ' . $this->istekSayisi);
                header('X-RateLimit-Remaining: 0');
                header('X-RateLimit-Reset: ' . ($now + $kalanSure));
                header('X-RateLimit-Reset-Second: ' . $kalanSure);
                header('X-RateLimit-Window: ' . $zamanPenceresi);
                header('Retry-After: ' . $kalanSure);

                http_response_code($Mesaj->kod);

                // Kilit serbest bırakılacak (finally bloğunda)
                return;
            }

            // Yeni isteği ekle
            $gecerliIstekler[] = $now;

            // Dosyayı başa sar ve yaz
            rewind($fp);
            ftruncate($fp, 0);
            fwrite($fp, json_encode($gecerliIstekler, JSON_UNESCAPED_UNICODE));

            // Rate limit başlıklarını ekle
            $kalanIstek = $this->istekSayisi - count($gecerliIstekler);
            $resetZamani = $now + $zamanPenceresi;
            $resetSaniye = $zamanPenceresi;

            header('X-RateLimit-Limit: ' . $this->istekSayisi);
            header('X-RateLimit-Remaining: ' . $kalanIstek);
            header('X-RateLimit-Reset: ' . $resetZamani);
            header('X-RateLimit-Reset-Second: ' . $resetSaniye);
            header('X-RateLimit-Window: ' . $zamanPenceresi);
        } finally {
            // Her durumda kilidi serbest bırak ve dosyayı kapat
            if ($fp) {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
        }
    }

    private function Veri_Hazirla()
    {
        $rawData = [];

        // GET parametrelerini al
        if (!empty($_GET)) {
            $rawData = array_merge($rawData, $_GET);
        }

        // POST parametrelerini al
        if (!empty($_POST)) {
            $rawData = array_merge($rawData, $_POST);
        }

        // JSON veri kontrolü (php://input)
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $jsonData = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                $rawData = array_merge($rawData, $jsonData);
            }
        }
        $this->istekler = (object) $this->Veri_Temizle($rawData);
    }

    private function Veri_Temizle($veri, $tur = 'default')
    {
        if (is_array($veri)) {
            return array_map(function ($a) use ($tur) {
                return $this->Veri_Temizle($a, $tur);
            }, $veri);
        }

        if (is_object($veri)) {
            $temizVeri = new stdClass();
            foreach ($veri as $key => $value) {
                $temizVeri->{$key} = $this->Veri_Temizle($value, $tur);
            }
            return $temizVeri;
        }

        if (is_null($veri) || $veri === '') {
            return $veri;
        }

        // Boolean ve numeric değerler için doğrudan döndür
        if (is_bool($veri) || is_int($veri) || is_float($veri)) {
            return $veri;
        }

        // String değil ise string'e çevir
        if (!is_string($veri)) {
            $veri = (string) $veri;
        }

        // Tehlikeli karakterleri kaldır
        $veri = str_ireplace([
            '<script',
            '</script>',
            'javascript:',
            'onclick=',
            'onload=',
            'onerror=',
            'onmouseover=',
            'onfocus=',
            'onblur=',
            'onchange=',
            'onsubmit=',
            'eval(',
            'expression(',
            'vbscript:',
            'data:'
        ], '', $veri);

        // Regex ile event handler'ları temizle
        $veri = preg_replace('/on\w+\s*=/i', '', $veri);
        $veri = preg_replace('/style\s*=.*?expression/i', '', $veri);
        $veri = preg_replace('/src\s*=.*?javascript/i', '', $veri);

        switch ($tur) {
            case 'email':
                return filter_var($veri, FILTER_SANITIZE_EMAIL);
            case 'url':
                return function_exists('Url_Temizle') ? Url_Temizle($veri) : filter_var($veri, FILTER_SANITIZE_URL);
            case 'xss_clean':
                return function_exists('Xss_Temizle') ? Xss_Temizle($veri, true) : htmlspecialchars($veri, ENT_QUOTES, 'UTF-8');
            case 'numeric':
                return filter_var($veri, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'integer':
                return filter_var($veri, FILTER_SANITIZE_NUMBER_INT);
            default:
                $veri = trim($veri);
                $veri = strip_tags($veri);
                $veri = htmlspecialchars($veri, ENT_QUOTES, 'UTF-8');
                return $veri;
        }
    }

    public function Veriler()
    {
        // İstek verilerini işleme kodları eklenecek.
    }

    /** OTO EKLENDI AYIKLANACAK */
    private function Kimlik()
    {
        // Endpoint ID'yi kontrol et
        $endpointId = $this->istekler->id ?? null;

        // Api_Router sınıfını yükle
        if (!class_exists('Api_Router')) {
            $apiRouterDosya = SISTEM_KLASOR_LIBRARY . 'Api_Router.php';
            if (file_exists($apiRouterDosya)) {
                require_once $apiRouterDosya;
            }
        }


        // Endpoint'in özelliklerini al
        if ($endpointId && class_exists('Api_Router')) {
            $ozellikler = Api_Router::Endpoint_Ozelliklerini_Al($endpointId);

            // Endpoint yoksa uygun hata ile dön
            if ($ozellikler === null || empty($ozellikler)) {


                $Mesaj = Mesaj(
                    'cakisma',
                    'Geçersiz API isteği: ' . $endpointId,
                    '',
                    true,
                    [
                        'dokuman' => TRUE,
                        'dokuman_link' => (function_exists('Url_Olustur') ? Url_Olustur('dokuman', true) : null),
                        'istekId' => $endpointId ?? '',
                        'istekTuru' => $this->istekTuru,
                        'zaman' => (function_exists('Tarih_Saat') ? Tarih_Saat() : date('Y-m-d H:i:s')),
                    ]
                );
                http_response_code($Mesaj->kod);
                die(Json_Olustur($Mesaj));
            }

            // Debug: Özellikleri logla
            if (defined('GELISTIRICI') && GELISTIRICI) {
                Log::Debug(1, "Endpoint: {$endpointId} - Özellikler: " . json_encode($ozellikler), true);
            }

            // TOKEN = false ise token kontrolünü atla
            if (isset($ozellikler['TOKEN']) && $ozellikler['TOKEN'] === false) {
                Log::Bilgi(1, "Endpoint: {$endpointId} - Token kontrolü atlandı (TOKEN=false)");

                // API verilerini doğrula (token kontrolü hariç)
                $this->istekSorunlari = $this->Istek_Kontrol($this->istekler, false); // false = token kontrolü yapma
                if (!$this->istekSorunlari['gecerli']) {
                    $Mesaj = Mesaj('gecersizIstek', 'Geçersiz API istek verileri: ' . implode(', ', $this->istekSorunlari['hatalar']), '', true);
                    Log::Hata(1, $Mesaj->mesaj);
                    $this->hataMesajlari = array_merge($this->hataMesajlari, $this->istekSorunlari['hatalar']);
                    http_response_code($Mesaj->kod);
                    return;
                }

                // Uyarılar varsa logla.
                if (!empty($this->istekSorunlari['uyarilar'])) {
                    Log::Dikkat(1, 'API istek uyarıları: ' . implode(', ', $this->istekSorunlari['uyarilar']) . ' - IP: ' . $this->ip);
                }

                return; // Token kontrolü atlanıyor
            }
        }

        // TOKEN = true için: URL'den veya body'den token al (Authorization header opsiyonel)
        $headers = getallheaders();

        // Önce isteklerden token'ı al
        if (!empty($this->istekler->token)) {
            $this->token = $this->istekler->token;
            Log::Bilgi(1, "Token query string/body'den alındı");
        }
        // Authorization header varsa onu kullan (backward compatibility)
        elseif (!empty($headers['Authorization']) && preg_match('/Bearer\s+(\S+)/', $headers['Authorization'], $matches)) {
            $this->token = $matches[1];
            Log::Bilgi(1, "Token Authorization header'dan alındı");
        } else {
            // Token bulunamadı
            $Mesaj = Mesaj('oturum', 'Token parametresi gereklidir. (&token=... veya Authorization header)', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return;
        }

        // Token uzunluk kontrolü
        if (strlen($this->token) < 32 || strlen($this->token) > 512) {
            $Mesaj = Mesaj('oturum', 'Geçersiz token uzunluğu.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return;
        }

        // Token doğrulama
        $this->Token_Kontrol();

        // API verilerini doğrula
        $this->istekSorunlari = $this->Istek_Kontrol($this->istekler);
        if (!$this->istekSorunlari['gecerli']) {
            $Mesaj = Mesaj('gecersizIstek', 'Geçersiz API istek verileri: ' . implode(', ', $this->istekSorunlari['hatalar']), '', true);
            Log::Hata(1, $Mesaj->mesaj);
            $this->hataMesajlari = array_merge($this->hataMesajlari, $this->istekSorunlari['hatalar']);
            http_response_code($Mesaj->kod);
            return;
        }

        // Uyarılar varsa logla.
        if (!empty($this->istekSorunlari['uyarilar'])) {
            Log::Dikkat(1, 'API istek uyarıları: ' . implode(', ', $this->istekSorunlari['uyarilar']) . ' - IP: ' . $this->ip);
        }
    }

    /** OTO EKLENDI AYIKLANACAK */
    private function Istek_Kontrol($data, $tokenKontrol = true)
    {
        $hatalar = [];
        $uyarilar = [];

        if (empty($data->id)) {
            $hatalar[] = 'API ID gereklidir';
        } else {
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $data->id)) {
                $hatalar[] = 'Geçersiz API ID formatı';
            }
            if (strlen($data->id) > 50) {
                $hatalar[] = 'API ID 50 karakterden uzun olamaz';
            }
        }

        // Token kontrolü sadece $tokenKontrol true ise yapılsın
        if ($tokenKontrol) {
            if (empty($data->token)) {
                $hatalar[] = 'Token gereklidir';
            } else {
                $tokenLen = strlen($data->token);
                if ($tokenLen < 32) {
                    $hatalar[] = 'Token minimum 32 karakter olmalıdır';
                } elseif ($tokenLen > 1024) {
                    $hatalar[] = 'Token maksimum 1024 karakter olabilir';
                }
            }
        }

        return [
            'hatalar' => $hatalar,
            'uyarilar' => $uyarilar,
            'gecerli' => empty($hatalar)
        ];
    }

    /** OTO EKLENDI AYIKLANACAK */
    private function Token_Kontrol()
    {
        $this->token = str_replace(' ', '+', $this->token);
        // Token şifresini çöz.
        $cozulenToken = Sifre_Coz($this->token);
        if (!$cozulenToken || !str_contains($cozulenToken, '-')) {
            $Mesaj = Mesaj('oturum', 'Token şifresi çözülemedi veya geçersiz format.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return false;
        }

        // Token parçalarını ayır.
        list($anahtar, $zaman) = explode('-', $cozulenToken, 2);

        // Token eşleştirme kontrolü.
        if ($anahtar !== ANAHTAR) {
            $Mesaj = Mesaj('oturum', 'Token anahtarı geçersiz.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return false;
        }

        // Token zaman aşımı kontrolü (15 dakika - güvenlik artırıldı).
        $tokenTimeout = (defined('API_TOKEN_TIMEOUT_DAKIKA') ? API_TOKEN_TIMEOUT_DAKIKA : 15) * 60;
        if (time() - (int)$zaman > $tokenTimeout) {
            $Mesaj = Mesaj('oturum', 'Token süresi dolmuş.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            $this->hataMesajlari[] = $Mesaj->mesaj;
            http_response_code($Mesaj->kod);
            return false;
        }
    }

    private function Yurut()
    {
        // Hata varsa işlemi durdur.
        if (!empty($this->hataMesajlari)) {
            $mesajMetni = implode(' ', $this->hataMesajlari);
            throw new Exception($mesajMetni);
        }
        // İstek başarılı, logla. token ve zaman hariç tüm verileri JSON olarak kaydet.
        $logVeri = (array) $this->istekler;
        unset($logVeri['token'], $logVeri['zaman']);
        $logString = implode(', ', array_map(fn($k, $v) => $k . '="' . (is_scalar($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE)) . '"', array_keys($logVeri), $logVeri));
        Log::Bilgi(1, 'API isteği alındı. Gelen veriler: [ ' . $logString . ' ] IP: ' . $this->ip);

        // API Router sistemini kullanarak endpoint işlemini yürüt.
        if (!isset($this->istekler->id) || empty($this->istekler->id)) {
            $Mesaj = Mesaj('gecersizIstek', 'API ID belirtilmemiş.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            echo Json_Olustur($Mesaj);
            return;
        }
        if (!file_exists(SISTEM_KLASOR_LIBRARY . 'Api_Router.php')) {
            $Mesaj = Mesaj('servis', 'API Router dosyası bulunamadı.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
            return;
        }
        if (!class_exists('Api_Router')) {
            $Mesaj = Mesaj('servis', 'API Router sınıfı bulunamadı.', '', true);
            Log::Hata(1, $Mesaj->mesaj);
            http_response_code($Mesaj->kod);
            throw new Exception($Mesaj->mesaj);
            return;
        }

        $Router = Api_Router::Calistir($this->istekler->id, $this->istekler, $this->token);
        $Json_Kontrol = Json_Kontrol($Router);
        if ($Json_Kontrol === true) {
            echo $Router;
        } else {
            if (is_object($Router) || is_array($Router)) {
                echo Json_Olustur($Router);
            } else {
                echo $Router;
            }
        }
    }
}
