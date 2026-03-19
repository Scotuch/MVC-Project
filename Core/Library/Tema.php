<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Tema Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/


class Tema
{
    public $baslik;
    public $aciklama;
    public $anahtar_kelimeler;
    public $sahip_ad;
    public $sahip_email;
    public $sahip_url;
    public $renk;
    public $ikon;

    public $css_dosyalar = [];
    public $js_dosyalar = [];
    public $otomatik_yukleme;

    public $meta_etiketler = [];
    public $js_degiskenler = [];
    public $manifest;
    public $sw;

    public $google;
    public $og_etiketler = [];
    public $twitter_etiketler = [];

    public $onbellek;

    public function __construct($baslik = '', $css = [], $js = [], $oto_yukleme = true)
    {
        $this->otomatik_yukleme = $oto_yukleme;
        $this->onbellek = defined('TEMA_ONBELLEK') ? TEMA_ONBELLEK : false;
        $this->Bilgiler($baslik);
        $this->Diger_Bilgiler();

        $this->Assets($css, 'css');
        $this->Assets($js, 'js');

        // JavaScript değişkenlerini yükle
        $this->JS_Degiskenler_Yukle();

        $this->SEO_Meta_Etiketleri();
    }

    private function Bilgiler($baslik = '')
    {
        $this->aciklama = defined('TEMA_ACIKLAMA') ? TEMA_ACIKLAMA : 'MVC [Model-View-Controller] mimarisi kullanılarak geliştirilmiştir.';
        $this->anahtar_kelimeler = defined('TEMA_ANAHTAR_KELIMELER') ? TEMA_ANAHTAR_KELIMELER : 'mvc, framework, php';
        $this->sahip_ad = defined('TEMA_SAHIP_AD') ? TEMA_SAHIP_AD : 'Scotuch';
        $this->sahip_email = defined('TEMA_SAHIP_EMAIL') ? TEMA_SAHIP_EMAIL : 'samedcimen@hotmail.com';
        $this->sahip_url = defined('TEMA_SAHIP_URL') ? TEMA_SAHIP_URL : 'https://github.com/Scotuch';
        $this->renk = defined('TEMA_RENK') ? TEMA_RENK : '#168060ff';
        $this->ikon = defined('TEMA_IKON') ? TEMA_IKON : ASSETS . 'img/favicon.png';

        $this->js_degiskenler = TEMA_DEGISKENLER;

        $this->Baslik_Olustur($baslik);

        $this->Temel_Etiketler();
    }

    private function Diger_Bilgiler()
    {
        $this->google['recaptcha_v3'] = defined('TEMA_GOOGLE_RECAPTCHA') ? TEMA_GOOGLE_RECAPTCHA : '';
        $this->google['tag'] = defined('TEMA_GOOGLE_TAG') ? TEMA_GOOGLE_TAG : '';
        $this->google['site_dogrulama'] = defined('TEMA_GOOGLE_SITE_DOGRULAMA') ? TEMA_GOOGLE_SITE_DOGRULAMA : '';

        $this->manifest = defined('TEMA_SCRIPT_MANIFEST') ? TEMA_SCRIPT_MANIFEST : '';
        $this->sw = defined('TEMA_SCRIPT_SW') ? TEMA_SCRIPT_SW : '';
    }

    private function Baslik_Olustur($baslik)
    {
        if (empty($baslik)) {
            $this->baslik = defined('TEMA_BASLIK') ? TEMA_BASLIK : 'MVC Framework';
        } else {
            $ayrac = defined('TEMA_BASLIK_AYRAC') ? " " . TEMA_BASLIK_AYRAC . " " : ' | ';
            $site_baslik = defined('TEMA_BASLIK') ? TEMA_BASLIK : 'Ana Sayfa';
            $this->baslik = $baslik . $ayrac . $site_baslik;
        }
    }

    private function Temel_Etiketler()
    {

        $this->meta_etiketler[] = '<meta charset="utf-8">';
        $this->meta_etiketler[] = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        $this->meta_etiketler[] = '<title>' . $this->Meta_Guvenlik($this->baslik) . '</title>';

        if (!empty($this->aciklama)) {
            $this->meta_etiketler[] = '<meta name="description" content="' . $this->Meta_Guvenlik($this->aciklama) . '">';
        }
        if (!empty($this->anahtar_kelimeler)) {
            $this->meta_etiketler[] = '<meta name="keywords" content="' . $this->Meta_Guvenlik($this->anahtar_kelimeler) . '">';
        }
        if (!empty($this->sahip_ad)) {
            $this->meta_etiketler[] = '<meta name="author" content="' . $this->Meta_Guvenlik($this->sahip_ad) . '">';
        }
        if (!empty($this->renk)) {
            $this->meta_etiketler[] = '<meta name="theme-color" content="' . $this->Meta_Guvenlik($this->renk) . '">';
        }
        if (!empty($this->ikon)) {
            $this->meta_etiketler[] = '<link rel="icon" type="image/png" href="' . $this->Meta_Guvenlik($this->ikon) . '">';
        }
    }

    private function Assets($dosyalar = [], $tip = '')
    {
        $gecerli_tipler = ['css', 'js'];
        if (!in_array(strtolower($tip), $gecerli_tipler)) {
            return;
        }

        $liste = [];
        $tip_upper = strtoupper($tip);
        $tema_sabit = 'TEMA_' . $tip_upper;

        if ($this->otomatik_yukleme && defined($tema_sabit)) {
            $liste = constant($tema_sabit);
        }

        if (is_array($dosyalar)) {
            $liste = array_merge($liste, $dosyalar);
        }
        foreach ($liste as $dosya) {
            $this->Assets_Yukle($dosya, $tip);
        }
    }

    private function Assets_Yukle($dosya, $tip)
    {
        try {
            // Boş alan ve güvenlik kontrolü
            if (empty($dosya) || !$this->Guvenlik($dosya)) {
                return false;
            }

            $tip_kucuk = strtolower($tip);
            $dosya_listesi = $tip_kucuk . '_dosyalar';

            // URL kontrolü ve yükleme
            if (function_exists('Url_Gecerli_Mi') && Url_Gecerli_Mi($dosya)) {
                return $this->Assets_Yukle_Harici($dosya, $tip, $dosya_listesi);
            }

            // Yerel dosya işlemleri
            return $this->Assets_Yukle_Yerel($dosya, $tip, $dosya_listesi);
        } catch (Exception $e) {
            if (defined('GELISTIRICI') && GELISTIRICI) {
                Log::Dikkat(0, 'Tema Hatası: ' . $e->getMessage());
                throw new Exception($e->getMessage());
            } else {
                return false;
            }
        }
    }

    private function Assets_Yukle_Harici($dosya, $tip, $dosya_listesi)
    {
        $this->{$dosya_listesi}[] = $dosya;

        $meta_etiketi = $this->Meta_Etiketi_Olustur($dosya, $tip);
        if (!empty($meta_etiketi)) {
            $this->meta_etiketler[] = $meta_etiketi;
            return true;
        }

        return false;
    }

    private function Assets_Yukle_Yerel($dosya, $tip, $dosya_listesi)
    {
        $tip_upper = strtoupper($tip);
        $tip_kucuk = strtolower($tip);

        $klasor_sabiti = 'KLASOR_' . $tip_upper;
        $url_sabiti = $tip_upper;
        $uzanti = '.' . $tip_kucuk;

        if (!defined($klasor_sabiti) || !defined($url_sabiti)) {
            if (defined('GELISTIRICI') && GELISTIRICI) {
                $Mesaj = Mesaj('dikkat', "Sabit tanımlı değil: {$klasor_sabiti} veya {$url_sabiti}", '', true);
                Log::Dikkat(0, $Mesaj->mesaj);
                throw new Exception($Mesaj->mesaj);
            } else {
                return false;
            }
        }

        $konum = constant($klasor_sabiti) . $dosya . $uzanti;
        $yol = constant($url_sabiti) . $dosya . $uzanti;

        // Dosya kontrolü
        if (!file_exists($konum)) {
            if (defined('GELISTIRICI') && GELISTIRICI) {
                $Mesaj = Mesaj('dikkat', "Dosya bulunamadı: {$konum}", '', true);
                Log::Dikkat(0, $Mesaj->mesaj);
                throw new Exception($Mesaj->mesaj);
            } else {
                return false;
            }
        }

        $this->{$dosya_listesi}[] = $dosya;

        $final_yol = $this->Onbellek($yol);
        $meta_etiketi = $this->Meta_Etiketi_Olustur($final_yol, $tip);

        if (!empty($meta_etiketi)) {
            $this->meta_etiketler[] = $meta_etiketi;
            return true;
        }

        return false;
    }

    private function Guvenlik($dosya)
    {
        $tehlikeli_karakterler = ['../', '../', '\\', '<', '>', '|', '&'];
        foreach ($tehlikeli_karakterler as $karakter) {
            if (strpos($dosya, $karakter) !== false) {
                if (defined('GELISTIRICI') && GELISTIRICI) {
                    $Mesaj = Mesaj('dikkat', "Tehlikeli karakter tespit edildi: {$karakter} dosya adı içinde.", '', true);
                    Log::Dikkat(0, $Mesaj->mesaj);
                    throw new Exception($Mesaj->mesaj);
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    private function Meta_Guvenlik($veri)
    {
        if (is_array($veri) || is_object($veri)) {
            if (defined('GELISTIRICI') && GELISTIRICI) {
                $Mesaj = Mesaj('dikkat', "Meta etiketleri için dizi veya nesne kullanılamaz.", '', true);
                Log::Dikkat(0, $Mesaj->mesaj);
                throw new Exception($Mesaj->mesaj);
            } else {
                return '';
            }
        }
        if ($veri === null || $veri === '') {
            return '';
        }
        $veri = preg_replace('/[\x00-\x1F\x7F]/u', '', $veri);
        return htmlspecialchars($veri, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function Meta_Etiketi_Olustur($kaynak, $tip)
    {
        if (strtolower($tip) === 'css') {
            return '<link rel="stylesheet" type="text/css" href="' . $kaynak . '">';
        } else {
            return '<script type="text/javascript" src="' . $kaynak . '"></script>';
        }
    }

    public function Meta_Etiketi_Ekle($name, $content)
    {
        if (is_array($name)) {
            foreach ($name as $etiket) {
                if (is_array($etiket) && isset($etiket['name'], $etiket['content'])) {
                    $this->meta_etiketler[] = '<meta name="' . $this->Meta_Guvenlik($etiket['name']) . '" content="' . $this->Meta_Guvenlik($etiket['content']) . '">';
                } elseif (is_string($etiket)) {
                    $this->meta_etiketler[] = $etiket;
                }
            }
        } else {
            $this->meta_etiketler[] = '<meta name="' . $this->Meta_Guvenlik($name) . '" content="' . $this->Meta_Guvenlik($content) . '">';
        }
        return $this;
    }

    private function SEO_Meta_Etiketleri()
    {
        // Robots
        $this->meta_etiketler[] = '<meta name="robots" content="index, follow">';

        // Google etiketleri
        $this->Meta_Etiket_Google();

        // Open Graph etiketleri
        $this->Meta_Etiket_OpenGraph();

        // Twitter Card etiketleri
        $this->Meta_Etiket_Twitter();
    }

    private function Meta_Etiket_Google()
    {
        if (!empty($this->google['recaptcha_v3'])) {
            $this->meta_etiketler[] = '<script src="https://www.google.com/recaptcha/api.js?render=' . $this->Meta_Guvenlik($this->google['recaptcha_v3']) . '"></script>';
        }

        if (!empty($this->google['tag'])) {
            $this->meta_etiketler[] = '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $this->Meta_Guvenlik($this->google['tag']) . '"></script>';
            $this->meta_etiketler[] = "<script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '" . $this->Meta_Guvenlik($this->google['tag']) . "');
            </script>";
        }

        if (!empty($this->google['site_dogrulama'])) {
            $this->meta_etiketler[] = '<meta name="google-site-verification" content="' . $this->Meta_Guvenlik($this->google['site_dogrulama']) . '">';
        }
    }

    private function Meta_Etiket_OpenGraph()
    {
        $og_veriler = [
            'og:title' => $this->baslik,
            'og:description' => $this->aciklama,
            'og:type' => 'website',
            'og:url' => function_exists('Mevcut_Url_Al') ? Mevcut_Url_Al() : '',
            'og:site_name' => defined('TEMA_BASLIK') ? TEMA_BASLIK : 'MVC Framework'
        ];

        foreach ($og_veriler as $property => $content) {
            if (!empty($content)) {
                $this->og_etiketler[] = '<meta property="' . $this->Meta_Guvenlik($property) . '" content="' . $this->Meta_Guvenlik($content) . '">';
            }
        }
        $this->meta_etiketler = array_merge($this->meta_etiketler, $this->og_etiketler);
    }

    private function Meta_Etiket_Twitter()
    {
        $twitter_veriler = [
            'twitter:card' => 'summary',
            'twitter:title' => $this->baslik,
            'twitter:description' => $this->aciklama
        ];

        foreach ($twitter_veriler as $name => $content) {
            if (!empty($content)) {
                $this->twitter_etiketler[] = '<meta name="' . $this->Meta_Guvenlik($name) . '" content="' . $this->Meta_Guvenlik($content) . '">';
            }
        }
        $this->meta_etiketler = array_merge($this->meta_etiketler, $this->twitter_etiketler);
    }

    private function JS_Degiskenler_Yukle()
    {
        if (defined('TEMA_DEGISKENLER') && is_array(TEMA_DEGISKENLER)) {
            $this->js_degiskenler = array_merge($this->js_degiskenler, TEMA_DEGISKENLER);
        }
    }

    private function Onbellek($dosya)
    {
        if (empty($dosya)) {
            return false;
        }

        if ($this->onbellek == true && !Url_Gecerli_Mi($dosya)) {
            return $dosya . '?' . time();
        }

        return $dosya;
    }

    public function calistir()
    {
        $cikti = PHP_EOL;

        // Tüm meta etiketleri
        foreach ($this->meta_etiketler as $meta) {
            $cikti .= "\t" . $meta . PHP_EOL;
        }

        // JavaScript değişkenleri
        if (!empty($this->js_degiskenler)) {
            $cikti .= "\t<script>" . PHP_EOL;
            foreach ($this->js_degiskenler as $ad => $deger) {
                if (is_array($deger) || is_object($deger)) {
                    $cikti .= "\t\tconst {$ad} = " . json_encode($deger, JSON_UNESCAPED_UNICODE) . ";" . PHP_EOL;
                } elseif (is_bool($deger)) {
                    $cikti .= "\t\tconst {$ad} = " . ($deger ? 'true' : 'false') . ";" . PHP_EOL;
                } elseif (is_numeric($deger)) {
                    $cikti .= "\t\tconst {$ad} = {$deger};" . PHP_EOL;
                } else {
                    $cikti .= "\t\tconst {$ad} = '" . addslashes($deger) . "';" . PHP_EOL;
                }
            }
            $cikti .= "\t</script>" . PHP_EOL;
        }

        // Manifest dosyası.
        if (!empty($this->manifest)) {
            $manifest = ltrim($this->manifest, '/');
            $web_yol = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            $manifest_web = $web_yol . '/' . $manifest;
            if (file_exists(ROOT . $manifest)) {
                $cikti .= "\t<link rel=\"manifest\" href=\"" . addslashes($manifest_web) . "\">" . PHP_EOL;
            }
        }

        // Service Worker.
        if (!empty($this->sw)) {
            $sw = ltrim($this->sw, '/');
            $web_yol = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            $sw_web = $web_yol . '/' . $sw;
            if (file_exists(ROOT . $sw)) {
                $cikti .= "\t<script>if('serviceWorker' in navigator)navigator.serviceWorker.register('" . addslashes($sw_web) . "');</script>\n";
            }
        }
        return $cikti . PHP_EOL;
    }
}
