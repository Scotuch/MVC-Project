<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Controller Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Controller extends Sistem
{
    protected $yukle;
    protected $onceMiddleware = [];
    protected $sonraMiddleware = [];
    protected $data = []; // View'a gönderilecek veriler

    public function __construct()
    {
        $this->yukle = $this;
        $this->middlewareYukle();
    }

    // Middleware'leri burada tanımlayabilirsiniz
    protected function middlewareYukle() {}

    /**
     * Parametre doğrulama ve filtreleme (override edilebilir)
     */
    public function dogrula($method, &$params)
    {
        // Varsayılan olarak parametreleri değiştirmez, true döner
        // Controller'da override edilerek özel doğrulama yapılabilir
        return true;
    }

    /**
     * Controller veya method bazlı erişim/yetki kontrolü
     */
    public function yetkiKontrol($method)
    {
        // Varsayılan olarak tüm methodlara izin ver
        // Özel controllerlarda override edilerek yetki kontrolü yapılabilir
        return true;
    }

    // Before middleware çalıştır
    public function onceCalistir($method)
    {
        foreach ($this->onceMiddleware as $middleware) {
            if (is_callable($middleware)) {
                call_user_func($middleware, $method);
            }
        }
    }

    // After middleware çalıştır
    public function sonraCalistir($method)
    {
        foreach ($this->sonraMiddleware as $middleware) {
            if (is_callable($middleware)) {
                call_user_func($middleware, $method);
            }
        }
    }

    public function View($dosya, $veri = [])
    {
        $dosya = mb_convert_case($dosya, MB_CASE_TITLE, "UTF-8");
        $konum = UYGULAMA . 'View/' . $dosya . '.php';

        if (!file_exists($konum)) {
            $Mesaj = Mesaj('hata', "View dosyası bulunamadı: {$konum}", "", true);
            Log::Hata(0, $Mesaj->mesaj);
            if (defined('GELISTIRICI') && GELISTIRICI) {
                throw new Exception($Mesaj->mesaj);
            } else {
                $this->Dosya(KLASOR_VIEW, 'Hata', $Mesaj);
            }
        }
        $Mesaj = Mesaj('bilgi', "View dosyası yükklendi: {$konum}", "", true);
        Log::Bilgi(0, $Mesaj->mesaj, GELISTIRICI);
        if (!empty($veri)) {
            extract($veri, EXTR_SKIP);
        }
        $this->Dosya(KLASOR_VIEW, $dosya, $veri);
    }
}
