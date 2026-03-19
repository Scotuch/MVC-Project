<?php

defined('ERISIM') or exit('401 Unauthorized');

/*************************************************
 * API Endpoint Modülü
 * -> Token API
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Token_Api
{
    public static function Ozellikler()
    {
        return [
            'token' => [
                'GET' => true,
                'POST' => true,
                'TOKEN' => false
            ],
            'token_post' => [
                'GET' => false,
                'POST' => true,
                'TOKEN' => false
            ]
        ];
    }

    public static function Calistir($istekler)
    {
        $token = Sifrele(ANAHTAR . "-" . time());
        return Mesaj(
            'basarili',
            'Token oluşturuldu',
            '',
            true,
            [
                'gecerlilik_suresi_dakika' => API_TOKEN_TIMEOUT_DAKIKA,
                'gecerlilik_suresi_saniye' => API_TOKEN_TIMEOUT_DAKIKA * 60,
                'olusturulma_zamani' => time(),
                'olusturulma_tarihi' => Tarih_Saat(),
                'token' => $token
            ]
        );
    }

    public static function Post($istekler)
    {
        return self::Calistir($istekler);
    }


    public static function Servis_Bilgi()
    {
        return [
            'isim' => 'Token API',
            'versiyon' => '1.0',
            'aciklama' => 'Token oluştur.',
            'servisler' => [
                'token' => 'API istekleri için hızlı bir sekilde token oluşturur.',
            ]
        ];
    }
}
