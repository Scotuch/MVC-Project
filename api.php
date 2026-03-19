<?php

/*
|--------------------------------------------------------------------------
| API Endpoint | Gelişmiş Sınıf Tabanlı API Sistemi
|--------------------------------------------------------------------------
|
| Bu dosya API isteklerini karşılar ve Core/Library/Api sınıfını kullanarak
| tüm işlemleri modüler bir şekilde yönetir.
|
| Özellikler:
| - Sınıf tabanlı mimari (OOP)
| - Gelişmiş güvenlik kontrolleri
| - Rate limiting (Sliding Window algoritması)
| - Comprehensive logging
| - Performance monitoring
| - CORS desteği
| - Input sanitization & validation
| - Token-based authentication
| - Health check sistemi
| - Debug mode desteği
|
| Kullanım:
| POST /api.php
| Headers: Authorization: Bearer <token>
| Content-Type: application/json
| Body: {"id": "endpoint_name", "token": "<token>"}
|
| Available Endpoints:
| - test_api: Basit API testi
| - system_info: Sistem bilgileri ve performans metrikleri
| - health_check: Kapsamlı sistem sağlık kontrolü
| - debug_info: Geliştirici debug bilgileri (dev mode only)
|
| Author: Scotuch
| Version: 1.0.0
| License: MIT
|
*/

require 'index.php';

/*
|--------------------------------------------------------------------------
| API İşlemi Başlat
|--------------------------------------------------------------------------
|
| Core/Library/Api sınıfını kullanarak tüm API işlemlerini yönet.
| Tüm işlemler sınıf içinde kapsüllenmiş ve modüler yapıda organize edilmiştir.
|
*/
try {
    $Api = new Api();
    $Api->Calistir();
} catch (Error | Exception $e) {
    if (defined('GELISTIRICI') && GELISTIRICI) {
        $Mesaj = Mesaj('dikkat', $e->getMessage(), '', true);
    } else {
        $Mesaj = Mesaj('dikkat', 'Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.', '', true);
    }
    //http_response_code($Mesaj->kod);
    Log::Hata(1, 'API kritik hatası: ' . $e->getMessage() . ' - Dosya: ' . $e->getFile() . ' - Satır: ' . $e->getLine());
    echo Json_Olustur($Mesaj);
    exit;
}
