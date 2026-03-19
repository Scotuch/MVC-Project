<?php

defined('ERISIM') or exit('401 Unauthorized');

/*************************************************
 * API Endpoint Modülü
 * -> Test API
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Test_Api
{
    public static function Ozellikler()
    {
        return [
            'test' => [
                'GET' => true,
                'POST' => true,
                'TOKEN' => true
            ],
            'test_html' => [
                'GET' => true,
                'POST' => true,
                'TOKEN' => false
            ]
        ];
    }

    public static function Calistir($istekler)
    {
        $return = Mesaj('basarili', 'Test API çalıştı', '', true);
        return $return;
    }

    public static function Html($istekler)
    {
        return Api_Router::HTML(
            'test',
            Mesaj(
                'basarili',
                'HTML yanıtı başarıyla oluşturuldu.',
                '',
                true,
                ['istekler' => $istekler, 'zaman' => date('Y-m-d H:i:s'), 'random' => rand(1000, 9999)]
            )
        );
    }

    public static function Servis_Bilgi()
    {
        return [
            'isim' => 'Test API',
            'versiyon' => '1.0',
            'aciklama' => 'API sistemini test etmek için kullanılır',
            'servisler' => [
                'test' => 'API sisteminin çalıştığını test eder.',
                'test_html' => 'Basit bir HTML içeriğini işler.'
            ]
        ];
    }
}
