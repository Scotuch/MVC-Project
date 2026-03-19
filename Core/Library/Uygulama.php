<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Uygulama Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Uygulama
{
    protected $yukleyici = [];
    protected $sistem;
    protected $controller;
    protected $method;
    protected $parametre = [];
    protected $url;
    protected $sayfa;
    protected $kontrolNesnesi;

    public function __construct()
    {
        // Yapılandırma dosyalarını ve fonksiyonlarını dahil et.
        $this->Yukleyici('');

        // Sistem modülünü başlat.
        $this->sistem = Sistem::Calistir();

        // Varsayılan controller metod belirle.
        $this->controller = defined('VARSAYILAN_CONTROLLER') ? VARSAYILAN_CONTROLLER : 'Anasayfa';
        $this->method = defined('VARSAYILAN_METHOD') ? VARSAYILAN_METHOD : 'index';

        try {
            // Sayfa türünü önce belirle
            $this->sayfa = $this->Gecerli_Sayfa();

            // API çağrılarında hiçbir şey yapma
            if ($this->sayfa == 'api' || $this->sayfa == 'endpoint') return;

            // URL'yi ayrıştır
            $this->url = $this->URL();

            // Controller'ı yükle ve çalıştır.
            $this->Calistir();
        } catch (Error | Exception $a) {
            Log::Hata(0, $a->getMessage());
            Hata($a, true);
        }
    }

    private function Yukleyici()
    {
        $toplamDosya = 0;
        $baslangicZamani = microtime(true);
        foreach ([SISTEM, UYGULAMA] as $directory) {
            $folders = ($directory === UYGULAMA) ? array('Config', 'Helper') : array('Config', 'Helper', 'Library');
            foreach ($folders as $folder) {
                $path = $directory . $folder;
                if (!is_dir($path)) {
                    Log::Dikkat(0, "Klasör bulunamadı: $path");
                    continue;
                }
                $files = glob($path . '/*.php');
                sort($files, SORT_STRING);
                foreach ($files as $file) {
                    if (basename($file) !== 'index.php') {
                        try {
                            require_once $file;
                            $this->yukleyici[] = $file;
                            $toplamDosya++;
                        } catch (Throwable $e) {
                            Log::Hata(0, "Dosya yükleme hatası: $file - " . $e->getMessage());
                        }
                    }
                }
            }
        }
        Log::Bilgi(0, "Yükleyici tamamlandı. Toplam $toplamDosya dosya " . Mikro_Saniye($baslangicZamani) . " sürede yüklendi.", GELISTIRICI);
    }

    private function Calistir()
    {
        $url = $this->url;
        if ($url && isset($url[0]) && $url[0] != '') {
            $controllerRaw = $url[0];
            if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $controllerRaw)) {
                $Mesaj = Mesaj('dikkat', 'Geçersiz karakterler içeren controller adı: ' . htmlspecialchars($controllerRaw, ENT_QUOTES, 'UTF-8'), '', true);
                Log::Dikkat(0, $Mesaj->mesaj, GELISTIRICI);
                $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
                exit;
            }
            $controllerAdi = $this->URL_Duzenle($controllerRaw);
        } else {
            $controllerAdi = $this->controller;
        }

        // Controller dosyasını dahil et.
        $controllerKonum = KLASOR_CONTROLLER . $controllerAdi . '.php';
        if (file_exists($controllerKonum)) {
            require_once $controllerKonum;
        } else {
            $Mesaj = Mesaj('hata', "Controller dosyası bulunamadı: {$controllerAdi}", "", true);
            Log::Hata(0, $Mesaj->mesaj, GELISTIRICI);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        // Controller sınıfının varlığını kontrol et.
        $sinifAdi = $controllerAdi . "_Controller";
        if (!class_exists($sinifAdi)) {
            $Mesaj = Mesaj('hata', "Controller sınıfı bulunamadı: {$sinifAdi}", "", true);
            Log::Hata(0, $Mesaj->mesaj);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        // Controller sınıfını başlat.
        $this->kontrolNesnesi = new $sinifAdi();

        // Controller sınıfının geçerli olup olmadığını kontrol et.
        if (!is_subclass_of($this->kontrolNesnesi, 'Controller')) {
            $Mesaj = Mesaj('hata', "Geçersiz controller sınıfı: {$sinifAdi}", "", true);
            Log::Hata(0, $Mesaj->mesaj);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        unset($this->url[0]);
        $method = (isset($this->url[1]) && $this->url[1] != '') ? $this->url[1] : $this->method;
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $method)) {
            $Mesaj = Mesaj('dikkat', 'Geçersiz karakterler içeren method adı: ' . htmlspecialchars($method, ENT_QUOTES, 'UTF-8'), '', true);
            Log::Dikkat(0, $Mesaj->mesaj, GELISTIRICI);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        if (!method_exists($this->kontrolNesnesi, $method)) {
            Log::Bilgi(0, "Controller method bulunamadı: $method, index deneniyor.", GELISTIRICI);
            if (method_exists($this->kontrolNesnesi, 'index')) {
                $method = 'index';
            } else {
                // Hiçbir şey yapmadan devam et
                return;
            }
        }

        // ReflectionMethod ile method kontrolü
        $ref = new ReflectionMethod($this->kontrolNesnesi, $method);
        if (!$ref->isPublic() || $ref->isConstructor() || $ref->isDestructor()) {
            $Mesaj = Mesaj('dikkat', 'Method erişilemez: ' . htmlspecialchars($method, ENT_QUOTES, 'UTF-8'), '', true);
            Log::Dikkat(0, $Mesaj->mesaj, GELISTIRICI);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        // Gerekli parametre sayısını kontrol et
        $paramCount = $ref->getNumberOfRequiredParameters();
        unset($this->url[1]);
        $this->parametre = $this->url ? array_values($this->url) : array();

        if (count($this->parametre) < $paramCount) {
            $Mesaj = Mesaj('dikkat', 'Eksik parametre! Gerekli: ' . $paramCount . ', Verilen: ' . count($this->parametre), '', true);
            Log::Dikkat(0, $Mesaj->mesaj, GELISTIRICI);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        $this->xssFiltrele($this->parametre);
        $this->method = $method;
        Log::Bilgi(0, '[CALL] Controller: ' . $controllerAdi . ' | Method: ' . $method . ' | Params: ' . json_encode($this->parametre), GELISTIRICI);

        // Yetkilendirme kontrolü
        if (!$this->kontrolNesnesi->yetkiKontrol($this->method)) {
            $Mesaj = Mesaj('dikkat', 'Bu işleme yetkiniz yok!', '', true);
            Log::Dikkat(0, $Mesaj->mesaj, GELISTIRICI);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        // Parametre doğrulama
        if (!$this->kontrolNesnesi->dogrula($this->method, $this->parametre)) {
            $Mesaj = Mesaj('dikkat', 'Parametre doğrulama hatası!', '', true);
            Log::Dikkat(0, $Mesaj->mesaj, GELISTIRICI);
            $this->sistem->dosya(defined('KLASOR_VIEW') && KLASOR_VIEW ? UYGULAMA . '/View/' : KLASOR_VIEW, 'Hata', $Mesaj);
            exit;
        }

        $this->kontrolNesnesi->onceCalistir($this->method);
        call_user_func_array([$this->kontrolNesnesi, $this->method], $this->parametre);
        $this->kontrolNesnesi->sonraCalistir($this->method);
    }

    public static function Gecerli_Sayfa()
    {
        $a = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
        $b = explode('.', $a);
        return $b[0];
    }

    public function URL()
    {
        if (!isset($_GET['url']) || empty($_GET['url'])) {
            return null;
        }
        $url = trim($_GET['url'], '/');
        if (
            strlen($url) > 200 || strpos($url, '..') !== false || strpos($url, '//') !== false ||
            preg_match('/<|javascript:|data:|onload|onerror/i', $url) || !preg_match('/^[a-zA-Z0-9\/_.-]+$/', $url)
        ) {
            Log::Dikkat(0, "Güvenlik: Geçersiz URL tespit edildi: " . htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));
            return null;
        }
        return explode('/', $url);
    }

    public function URL_Duzenle($q)
    {
        $q = ucwords(strtolower(str_replace(['-', '_', '%20'], ' ', $q)));
        return trim(str_replace(' ', '_', preg_replace('/\s+/', ' ', $q)), '_');
    }

    private function xssFiltrele(&$params)
    {
        $onceki = $params;
        foreach ($params as &$p) {
            $p = htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
        }
        if ($onceki !== $params) {
            Log::Dikkat(0, "XSS Filtre: Parametreler temizlendi");
        }
    }
}
