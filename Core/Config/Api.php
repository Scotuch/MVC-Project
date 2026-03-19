<?php

defined('ERISIM') or exit('401 Unauthorized');
/*
|--------------------------------------------------------------------------
| API ayarları
|--------------------------------------------------------------------------
|
| API token süreleri ve güvenlik ayarları.
|
*/
define('API_HSTS_MAKSIMIM_GUN', 365); // HSTS maksimum gün sayısı
define('API_MAX_REQUEST_SIZE', 1); // MB cinsinden
define('API_RATE_LIMIT_SAYISI', 60); // Dakika başına izin verilen istek sayısı
define('API_RATE_LIMIT_SANIYE', 60); // Süre (saniye) - Rate limit zaman dilimi
define('API_TEMIZLIK_SAAT', 2); // Rate limit veritabanı temizliği için saat
define('API_TOKEN_TIMEOUT_DAKIKA', 30); // Token geçerlilik süresi (dakika)
define('API_IZINLI_DOMAINLER', [
    'https://yourdomain.com',
    'https://api.yourdomain.com'
]);
define('API_IZINLI_METHODLAR', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
//define('API_IZINLI_METHODLAR', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);

/*
|--------------------------------------------------------------------------
| Endpoint ayarları
|--------------------------------------------------------------------------
|
| Mevcut API endpoint'leri ve açıklamaları.
|
*/
define('ENDPOINT', true);

/*
||--------------------------------------------------------------------------
|| HTTP Metod Renkleri
||--------------------------------------------------------------------------
||
|| API dokümantasyonunda kullanılacak HTTP metod renkleri.
|| Her metod için arka plan rengi tanımlanır.
||
*/
define('API_METHOD_COLORS', [
    'GET' => '#1cc88a',      // Yeşil
    'POST' => '#4e73df',     // Mavi
    'PUT' => '#f6c23e',      // Sarı/Turuncu
    'DELETE' => '#e74a3b',   // Kırmızı
    'PATCH' => '#36b9cc',    // Açık Mavi/Cyan
    'OPTIONS' => '#858796',  // Gri
    'HEAD' => '#5a5c69'      // Koyu Gri
]);
