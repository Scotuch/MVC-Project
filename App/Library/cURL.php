<?php
defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Bileşenleri
 * Özel Curl Sınıfı
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class cURL
{

    public $curl;
    public $url;
    public $url_full;
    public $url_veri;
    public $post_veri;
    public $put_veri;
    public $cerez;
    public $ayarlar;
    public $header;
    public $useragent;
    public $referer;
    public $bilgi;
    public $zaman;
    public $proxy;
    public $yonlendir;

    public function __construct()
    {

        $this->url = '';
        $this->url_full = "";
        $this->url_veri = [];
        $this->post_veri = [];
        $this->put_veri = '';
        $this->cerez = '';
        $this->ayarlar = [];
        $this->header = $this->Header_Varsayılan();
        $this->useragent = $this->UserAgent();
        $this->referer = 'https://www.google.com.tr';
        $this->bilgi = 'false';
        $this->zaman = 30;
        $this->proxy = '';
        $this->yonlendir = false;
    }

    public function __destruct()
    {
        $this->curl = NULL;
    }

    public function calistir($id = '')
    {
        if (empty($this->url)) {
            return 'URL Belirlenmedi';
        }
        $this->curl = curl_init();
        curl_setopt_array($this->curl, $this->ayarlari_oku());
        $curl_icerik = curl_exec($this->curl);
        //$info=curl_getinfo( $curl );
        if ($curl_icerik === false) {
            $hata = [];
            $hata['kod'] = curl_errno($this->curl);
            $hata['mesaj'] = curl_error($this->curl);
            $curl_icerik =  $hata;
            //$curl_icerik = 'Hata Kodu: ' . curl_errno($this->curl) . ' ~  Hata Bilgisi: '. curl_error($this->curl);
        }
        curl_close($this->curl);
        if ($id == 'json') {
            return json_decode($curl_icerik);
        }
        return $curl_icerik;
    }

    private function ayarlari_oku()
    {
        $ayarlar = $this->ayarlar;
        $ayarlar[CURLOPT_RETURNTRANSFER] = 1;
        $ayarlar[CURLOPT_URL] = $this->url . $this->Url_Veri_Topla();
        $ayarlar[CURLOPT_ENCODING] = 'gzip, deflate';

        $this->url_full = $this->url . $this->Url_Veri_Topla();

        if (!empty($this->cerez)) {
            $ayarlar[CURLOPT_COOKIE] = $this->cerez;
        }

        if (!empty($this->useragent)) {
            $ayarlar[CURLOPT_USERAGENT] = $this->useragent;
        }
        if (!empty($this->referer)) {
            $ayarlar[CURLOPT_REFERER] = $this->referer;
        }
        if (!empty($this->header)) {
            $ayarlar[CURLOPT_HTTPHEADER] = $this->header;
        }
        $postData = $this->Post_Veri_Topla();
        $putData = $this->Put_Veri_Topla();
        if (!empty($putData)) {
            $ayarlar[CURLOPT_CUSTOMREQUEST] = "PUT";
            $ayarlar[CURLOPT_POSTFIELDS] = $putData;
        } elseif (!empty($postData)) {
            $ayarlar[CURLOPT_POST] = 1;
            $ayarlar[CURLOPT_POSTFIELDS] = $postData;
        }

        if (!empty($this->proxy)) {
            $proxy_parcala = explode(':', $this->proxy);
            if (isset($proxy_parcala[0]) && isset($proxy_parcala[1])) {
                $ayarlar[CURLOPT_PROXY] = $proxy_parcala[0];
                $ayarlar[CURLOPT_PROXYPORT] = $proxy_parcala[1];
                $ayarlar[CURLOPT_PROXYTYPE] = 'HTTP';
            }
        }

        $ayarlar[CURLOPT_FOLLOWLOCATION] = $this->yonlendir;
        $ayarlar[CURLOPT_HEADER] = $this->bilgi;
        $ayarlar[CURLOPT_TIMEOUT] = $this->zaman;
        return $ayarlar;
    }

    public function Url($url)
    {
        $this->url = $url;
    }

    public function Url_Veri($name, $value = 0)
    {
        if (is_array($name)) {
            foreach ($name as $dataName => $dataValue) :
                $this->url_veri[$dataName] = $dataValue;
            endforeach;
        } else {
            $this->url_veri[$name] = $value;
        }
    }

    public function Url_Veri_Topla()
    {
        $return_data = '';
        if (!empty($this->url_veri)) {
            $stringGet = '?';
            $stringGet .= http_build_query($this->url_veri);
            $return_data = $stringGet;
        }
        return $return_data;
    }

    public function Post_Veri($name, $value = 0)
    {
        if (is_array($name)) {
            foreach ($name as $dataName => $dataValue) :
                $this->post_veri[$dataName] = $dataValue;
            endforeach;
        } else {
            $this->post_veri = $name;
        }
    }

    public function Post_Veri_Topla()
    {
        $return_data = '';
        if (!empty($this->post_veri)) {
            $return_data = $this->post_veri;
        }
        return $return_data;
    }

    public function Put_Veri($data)
    {
        if (!empty($data)) {
            $this->put_veri = $data;
        }
    }

    public function Put_Veri_Topla()
    {
        $return_data = '';
        if (!empty($this->put_veri)) {
            $return_data = $this->put_veri;
        }
        return $return_data;
    }

    public function Bilgi($var)
    {
        $this->bilgi = $var;
    }

    public function Cerez($icerik)
    {
        $this->cerez = $icerik;
    }

    public function Header($name, $value = 0)
    {
        if (is_array($name)) {
            foreach ($name as $dataName => $dataValue) :
                $this->header[] = $dataName . ': ' . $dataValue;
            endforeach;
        } else {
            $this->header[] = $name . ': ' . $value;
        }
    }

    public function Header_Varsayılan()
    {
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: tr-tr,tr;q=0.5";
        $header[] = "cache-control: max-age=0";
        $header[] = "Keep-Alive: 300";
        return $header;
    }

    public function Proxy($ip)
    {
        $this->proxy = $ip;
    }

    public function Referer($refer_url)
    {
        $this->referer = $refer_url;
    }

    public function UserAgent($tur = '', $veri = '')
    {
        switch ($tur) {
            case 'android':
                $veri = 'Mozilla/5.0 (Linux; Android 10; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3396.81 Mobile Safari/537.36';
                break;
            case 'apple':
                $veri = 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML%2C like Gecko) Mobile/15E148';
                break;
            case 'free_bsd':
                $veri = 'Mozilla/5.0 (X11; FreeBSD amd64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36';
                break;
            case 'mac':
                $veri = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.1.2 Safari/605.1.15';
                break;
            case 'nokia':
                $veri = 'Nokia5250/10.0.011 (SymbianOS/9.4; U; Series60/5.0 Mozilla/5.0; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Safari/525 3gpp-gba';
                break;
            case 'xiaomi':
                $veri = 'Mozilla/5.0 (Linux; Android 10; Redmi Note 9 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.91 Mobile Safari/537.36';
                break;
            case 'ozel':
                $veri = $veri;
                break;
            default:
                //$veri = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36';
                $veri = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36 Edg/115.0.1901.188';
                break;
        }
        $this->useragent = $veri;
        return  $veri;
    }

    public function Yonlendir($var)
    {
        $this->yonlendir = $var;
    }

    public function Zaman($bool)
    {
        if (is_numeric($bool)) {
            $this->zaman = $bool;
        }
    }
}
