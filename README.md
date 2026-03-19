# PHP MVC Framework

Sıfırdan geliştirilmiş, güçlü ve esnek bir **PHP MVC (Model-View-Controller)** çerçevesidir. Sınıf tabanlı mimari, gelişmiş API sistemi, kapsamlı güvenlik katmanı ve zengin yardımcı araçlarıyla modern web uygulamaları geliştirmeye hazır bir altyapı sunar.

- **Yazar:** Samed CİMEN (Scotuch) — [samedcimen@hotmail.com](mailto:samedcimen@hotmail.com)
- **GitHub:** [github.com/Scotuch](https://github.com/Scotuch)
- **Versiyon:** 1.0.0
- **Lisans:** [MIT](https://opensource.org/licenses/MIT)
- **Telif:** © 2026 Samed CİMEN

---

## İçindekiler

- [Özellikler](#özellikler)
- [Gereksinimler](#gereksinimler)
- [Kurulum](#kurulum)
- [Dizin Yapısı](#dizin-yapısı)
- [Temel Kullanım](#temel-kullanım)
    - [Controller Oluşturma](#controller-oluşturma)
    - [View Oluşturma](#view-oluşturma)
    - [API Endpoint Oluşturma](#api-endpoint-oluşturma)
- [API Sistemi](#api-sistemi)
    - [Token Alma](#token-alma)
    - [API Endpoint Çağrısı](#api-endpoint-çağrısı)
    - [Endpoint Özellikleri](#endpoint-özellikleri)
- [Güvenlik](#güvenlik)
- [Log Sistemi](#log-sistemi)
- [Yardımcı Fonksiyonlar](#yardımcı-fonksiyonlar)
- [Yapılandırma](#yapılandırma)

---

## Özellikler

| Özellik                       | Açıklama                                                 |
| ----------------------------- | -------------------------------------------------------- |
| 🏗️ **MVC Mimarisi**           | Controller, View katmanları ile temiz kod yapısı         |
| 🔌 **REST API Sistemi**       | Sınıf tabanlı, modüler ve self-documenting API           |
| 🔒 **Token Kimlik Doğrulama** | AES-256-CBC ile şifrelenmiş token sistemi                |
| 🚦 **Rate Limiting**          | Sliding Window algoritması ile IP bazlı hız sınırlama    |
| 🛡️ **Güvenlik Katmanı**       | XSS, SQL Injection, CSRF koruması; güvenlik başlıkları   |
| 📦 **Önbellek**               | API router ve istek önbelleği                            |
| 📝 **Loglama**                | 5 seviyeli log sistemi, otomatik rotasyon, dev/prod modu |
| 🔐 **Şifreleme**              | AES-256-CBC, AES-256-GCM, Bcrypt, Argon2, HMAC desteği   |
| 🗄️ **Veritabanı**             | PDO tabanlı, fluent interface, prepared statements       |
| 📄 **API Dokümantasyonu**     | Otomatik oluşturulan, 30+ formatta dokümantasyon         |
| 🌍 **CORS Desteği**           | Yapılandırılabilir izinli domain ve metot listesi        |
| ⚙️ **Ortam Değişkenleri**     | `.env` dosyası ile güvenli yapılandırma                  |
| 🔍 **Debug Araçları**         | `Konsol()` ile gelişmiş web/CLI debug çıktısı            |
| 🔄 **Autoloader**             | SPL tabanlı otomatik sınıf yükleme                       |
| 🛤️ **URL Yönlendirme**        | `.htaccess` tabanlı temiz URL yapısı                     |

---

## Gereksinimler

- **PHP** >= 7.4 (PHP 8.x önerilir)
- **Apache** with `mod_rewrite`
- **MySQL** / MariaDB
- **OpenSSL** PHP eklentisi
- **PDO** & `pdo_mysql` PHP eklentisi

---

## Kurulum

**1. Projeyi web sunucusu kök dizinine kopyalayın**

**2. `.env` dosyasını oluşturun:**

```bash
cp .env.example .ENV
```

**3. `.ENV` dosyasını düzenleyin:**

```env
# Veritabanı Ayarları
VERITABANI_HOST=localhost
VERITABANI_PORT=3306
VERITABANI_KULLANICI=root
VERITABANI_SIFRE=gizli_sifre
VERITABANI_AD=veritabani_adi

# Şifreleme Ayarları
ANAHTAR=guclu_rastgele_anahtar_buraya
SIFRELEME_YONTEMI=AES-256-CBC
JSON_OPTIONS=
```

**4. Apache `mod_rewrite` aktif olmalıdır.** `.htaccess` dosyası otomatik olarak tüm istekleri `index.php`'ye yönlendirir.

**5. `App/Cache`, `App/Log`, `App/Temp`, `App/Backup` klasörlerine yazma izni verin:**

```bash
chmod -R 755 App/Cache App/Log App/Temp App/Backup
```

---

## Dizin Yapısı

```
MVC/
├── index.php               # Uygulama giriş noktası
├── api.php                 # API endpoint giriş noktası
├── .htaccess               # URL yönlendirme & güvenlik kuralları
├── .ENV                    # Ortam değişkenleri (gizli)
├── .env.example            # Ortam değişkenleri şablonu
│
├── Core/                   # ⚙️ Çekirdek sistem (değiştirmeyin)
│   ├── Core.php            # SPL Autoloader & Hata fonksiyonları
│   ├── Config/
│   │   ├── Ayarlar.php     # Klasör sabitlerini tanımlar
│   │   ├── Api.php         # API güvenlik & rate limit ayarları
│   │   ├── Log.php         # Log boyutu & rotasyon ayarları
│   │   ├── Sifreleme.php   # Şifreleme sabitleri (.env'den)
│   │   └── Veritabani.php  # Veritabanı sabitleri (.env'den)
│   ├── Helper/
│   │   ├── Sifreleme.php   # AES-CBC, AES-GCM, Bcrypt, Argon2, HMAC
│   │   ├── Mesaj.php       # Standart API/sistem mesaj oluşturucu
│   │   ├── Url.php         # URL oluşturma & temizleme
│   │   ├── Cerez.php       # Cookie yönetimi
│   │   ├── Dosya.php       # Dosya işlemleri
│   │   ├── Klasor.php      # Klasör işlemleri
│   │   ├── Guvenlik.php    # Güvenlik ve doğrulama araçları
│   │   ├── Veri.php        # Veri işleme & sanitizasyon
│   │   ├── Kelimeler.php   # Metin & string yardımcıları
│   │   ├── Zaman.php       # Tarih & saat yardımcıları
│   │   ├── Boyut.php       # Boyut & format dönüştürücüler
│   │   ├── Diger.php       # Genel yardımcı fonksiyonlar
│   │   ├── Yardimci.php    # Ek yardımcı araçlar
│   │   └── Sinif.php       # Sınıf yardımcıları
│   ├── Library/
│   │   ├── Uygulama.php    # MVC çekirdeği (routing, dispatch)
│   │   ├── Controller.php  # Temel Controller sınıfı
│   │   ├── Sistem.php      # Singleton sistem sınıfı
│   │   ├── Api.php         # API işlem motoru
│   │   ├── Api_Router.php  # API endpoint router & cache
│   │   ├── Api_Dokuman.php # Otomatik API dokümantasyonu
│   │   ├── Veritabani.php  # PDO tabanlı veritabanı sınıfı
│   │   ├── Log.php         # Log sınıfı (5 seviye)
│   │   ├── Onbellek.php    # Önbellek yöneticisi
│   │   ├── Tema.php        # Tema & varlık yöneticisi
│   │   └── Env.php         # .env dosyası okuyucu
│   ├── View/
│   │   ├── Hata.php        # Sistem hata sayfası
│   │   └── Api_Dokuman_HTML.php  # API dokümantasyon HTML şablonu
│   └── Log/                # Core log dosyaları
│
├── App/                    # 📦 Uygulama kodları (burada geliştirin)
│   ├── Config/
│   │   ├── Ayarlar.php     # Assets yol sabitleri
│   │   ├── Tema.php        # Tema yapılandırması
│   │   └── Veritabani.php  # Uygulama tablo adı sabitleri (VT_TEST vb.)
│   ├── Controller/
│   │   ├── Anasayfa.php    # Anasayfa controller örneği
│   │   ├── Api.php         # API bilgi controller
│   │   └── Dokuman.php     # API dokümantasyon controller
│   ├── View/
│   │   ├── Anasayfa.php    # Anasayfa görünümü
│   │   └── Hata.php        # Uygulama hata sayfası
│   ├── Api/
│   │   ├── Token.php       # Token üretme endpoint'i
│   │   ├── Test.php        # Test endpoint'i
│   │   └── Html/           # API HTML yanıt şablonları
│   ├── Helper/
│   │   └── Sinif.php       # Uygulama özel yardımcılar (cURL, Veritabani wrappers)
│   ├── Library/
│   │   └── cURL.php        # cURL istek kütüphanesi
│   ├── Cache/              # API router & istek önbelleği (otomatik oluşur)
│   ├── Log/                # Uygulama log dosyaları (otomatik oluşur)
│   ├── Backup/             # Yedek dosyaları
│   ├── Temp/               # Geçici dosyalar
│   ├── Partial/            # Tekrar kullanılabilir view parçaları (header, footer vb.)
│   ├── Service/            # İş mantığı servis sınıfları
│   ├── Database/           # Migration, seed ve sorgu dosyaları
│   ├── Content/            # Statik içerik dosyaları
│   └── Other/              # Sınıflandırılmamış diğer dosyalar
│
└── assets/                 # 🎨 Frontend varlıkları
    ├── css/                # Stil dosyaları
    ├── js/                 # JavaScript dosyaları (script.js, script.bundle.js)
    ├── sass/ & scss/       # SASS/SCSS kaynak dosyaları
    ├── fonts/              # Font dosyaları
    └── img/                # Görseller
```

---

## Temel Kullanım

### Controller Oluşturma

`App/Controller/` dizininde `SayfaAdi.php` adıyla dosya oluşturun. Sınıf adı `SayfaAdi_Controller` olmalı ve `Controller` sınıfından türetilmelidir:

```php
<?php
defined('ERISIM') or exit('401 Unauthorized');

class Blog_Controller extends Controller
{
    public function index()
    {
        // /blog URL'si bu metodu çağırır
        $this->View('blog');
    }

    public function detay($id)
    {
        // /blog/detay/123 URL'si bu metodu çağırır
        $this->View('blog_detay', ['id' => $id]);
    }
}
```

**URL Yapısı:** `/{controller}/{method}/{param1}/{param2}/...`

### View Oluşturma

`App/View/` dizininde `SayfaAdi.php` adıyla dosya oluşturun:

```php
<?php defined('ERISIM') or exit('401 Unauthorized'); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?= CSS ?>style.css">
</head>
<body>
    <h1>Merhaba!</h1>
    <script src="<?= JS ?>script.js"></script>
</body>
</html>
```

### API Endpoint Oluşturma

`App/Api/` dizininde `EndpointAdi.php` adıyla dosya oluşturun. Sınıf adı `EndpointAdi_Api` olmalıdır:

```php
<?php
defined('ERISIM') or exit('401 Unauthorized');

class Kullanici_Api
{
    // Endpoint özellikleri (HTTP metodları & token zorunluluğu)
    public static function Ozellikler()
    {
        return [
            'kullanici'        => ['GET' => true, 'POST' => false, 'TOKEN' => true],
            'kullanici_ekle'   => ['GET' => false, 'POST' => true,  'TOKEN' => true],
        ];
    }

    // GET /api.php?id=kullanici&token=...
    public static function Calistir($istekler, $token = null)
    {
        return Mesaj('basarili', 'Kullanıcı listesi', '', true, [
            'kullanicilar' => []
        ]);
    }

    // POST /api.php  {"id":"kullanici_ekle","token":"...","ad":"Ali"}
    public static function Ekle($istekler, $token = null)
    {
        $ad = $istekler->ad ?? '';
        // işlem...
        return Mesaj('basarili', 'Kullanıcı eklendi.', '', true);
    }

    // Dokümantasyon için servis bilgisi
    public static function Servis_Bilgi()
    {
        return [
            'isim'      => 'Kullanıcı API',
            'versiyon'  => '1.0',
            'aciklama'  => 'Kullanıcı yönetim servisi',
            'servisler' => [
                'kullanici'      => 'Kullanıcı listesini döndürür.',
                'kullanici_ekle' => 'Yeni kullanıcı ekler.',
            ]
        ];
    }
}
```

> **Endpoint Adlandırma Kuralı:** `dosya_adi` → `Calistir()` metodu, `dosya_adi_metodadi` → diğer `public static` metodlar.

---

## API Sistemi

### Token Alma

Token kimlik doğrulaması gerektiren endpointleri çağırmadan önce token alın:

```http
GET /api?id=token
```

**Yanıt:**

```json
{
    "kod": 200,
    "baslik": "Başarılı",
    "mesaj": "Token oluşturuldu",
    "gecerlilik_suresi_dakika": 30,
    "token": "eyJhbGciO..."
}
```

### API Endpoint Çağrısı

```http
# GET isteği
GET /api?id=test&token=TOKEN_DEGERI

# POST isteği (JSON)
POST /api
Content-Type: application/json
Authorization: Bearer TOKEN_DEGERI

{"id": "test", "token": "TOKEN_DEGERI"}
```

### Endpoint Özellikleri

Her endpoint için `Ozellikler()` metodu ile HTTP metodunu ve token zorunluluğunu tanımlayın:

| Özellik  | Değer        | Açıklama           |
| -------- | ------------ | ------------------ |
| `GET`    | `true/false` | GET metodu izni    |
| `POST`   | `true/false` | POST metodu izni   |
| `PUT`    | `true/false` | PUT metodu izni    |
| `DELETE` | `true/false` | DELETE metodu izni |
| `TOKEN`  | `true/false` | Token zorunluluğu  |

### Yerleşik Endpointler

| Endpoint ID | Metod     | Token | Açıklama           |
| ----------- | --------- | ----- | ------------------ |
| `token`     | GET, POST | ❌    | API token üret     |
| `test`      | GET, POST | ✅    | API bağlantı testi |
| `test_html` | GET, POST | ❌    | HTML yanıt testi   |

### API Dokümantasyonu

Tüm API endpointleri için otomatik dokümantasyon `/dokuman` adresinde mevcuttur:

```
/dokuman          → HTML format (varsayılan)
/dokuman/json     → JSON format
/dokuman/markdown → Markdown format
/dokuman/openapi  → OpenAPI / Swagger
/dokuman/postman  → Postman koleksiyonu
/dokuman/python   → Python kod örneği
/dokuman/js       → JavaScript kod örneği
/dokuman/php      → PHP kod örneği
```

---

## Güvenlik

### Güvenlik Başlıkları (`.htaccess`)

Apache otomatik olarak aşağıdaki güvenlik başlıklarını ekler:

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

### Rate Limiting

Sliding Window algoritması ile IP başına istek sınırlaması:

```php
// Core/Config/Api.php
define('API_RATE_LIMIT_SAYISI', 60);  // Dakikada maksimum istek
define('API_RATE_LIMIT_SANIYE', 60);  // Pencere süresi (saniye)
```

Rate limit aşıldığında yanıt başlıkları:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1234567890
Retry-After: 45
```

### Şifreleme Fonksiyonları

```php
// AES-256-CBC şifreleme
$sifreli = Sifrele('gizli_veri');
$orijinal = Sifre_Coz($sifreli);

// AES-256-GCM (kimlik doğrulamalı şifreleme)
$gcm = Aes_Gcm_Sifrele('kritik_veri');
$orijinal = Aes_Gcm_Coz($gcm);

// Şifre hash'leme
$hash = Bcrypt_Olustur('kullanici_sifresi');        // bcrypt
$hash = Argon2_Olustur('kullanici_sifresi');        // Argon2
$gecerli = Sifre_Dogrula('kullanici_sifresi', $hash);

// HMAC imzalama
$imza = Hmac_Olustur($veri, ANAHTAR);
$gecerli = Hash_Dogrula($beklenen, $gercek);

// Zaman damgalı token
$token = Zaman_Damgali_Token(3600, 'user:123');
$gecerli = Zaman_Damgali_Token_Dogrula($token, 'user:123');

// Rastgele üreticiler
$token = Rastgele_Token(32);          // URL-safe token
$sifre = Rastgele_Sifre(12);          // Güçlü şifre
$uuid = Rastgele_Uuid();              // UUID v4
```

### XSS & Input Koruması

API sistemi tüm gelen verileri `Veri_Temizle()` ile otomatik olarak temizler. Controller URL parametreleri de `htmlspecialchars` ile filtrelenir.

---

## Log Sistemi

### Log Seviyeleri

```php
Log::Hata(0, 'Kritik hata mesajı');            // Seviye 0 - HATA
Log::Dikkat(0, 'Uyarı mesajı');                // Seviye 1 - DİKKAT
Log::Bilgi(1, 'Bilgi mesajı');                 // Seviye 2 - BİLGİ
Log::Basari(1, 'Başarı mesajı');               // Seviye 3 - BAŞARI
Log::Debug(1, 'Debug mesajı', GELISTIRICI);    // Seviye 4 - DEBUG
```

**Konum parametresi:**

- `0` → `Core/Log/` klasörüne yazar
- `1` → `App/Log/` klasörüne yazar

**Dev modu:** Son parametre `true` ise `{tarih}-dev.log` dosyasına yazar; üretim logları ile karışmaz.

### Log Dosya Yapısı

```
App/Log/
└── 2026/
    └── 03/
        ├── 11.log        # Üretim logları
        └── 11-dev.log    # Geliştirici logları
```

### Log Yapılandırması

```php
// Core/Config/Log.php
define('LOG_BOYUTU', 2);   // MB - rotasyon başlama boyutu
define('LOG_SINIR', 10);   // Maksimum rotasyon dosya sayısı
```

---

## Yardımcı Fonksiyonlar

### Mesaj Oluşturucu

```php
// Standart API/AJAX yanıt objesi
$mesaj = Mesaj('basarili', 'İşlem tamamlandı', '', true);
$mesaj = Mesaj('hata', 'Kayıt bulunamadı', '', true);
$mesaj = Mesaj('oturum', 'Giriş yapmanız gerekiyor', 'giris', true);
$mesaj = Mesaj('basarili', 'Veri eklendi', '', true, ['id' => 42]);

// JSON string olarak
$json = Mesaj('basarili', 'Tamam');
```

**HTTP Durum Kodu Eşleşmeleri:**

| Durum           | HTTP Kodu | Kullanım              |
| --------------- | --------- | --------------------- |
| `basarili`      | 200       | Başarılı işlemler     |
| `bilgi`         | 202       | Bilgilendirme         |
| `gecersizIstek` | 400       | Hatalı istek formatı  |
| `oturum`        | 401       | Token/oturum hatası   |
| `yetki`         | 403       | Yetkisiz erişim       |
| `hata`          | 404       | Kaynak bulunamadı     |
| `izinYok`       | 405       | HTTP metodu izinsiz   |
| `cakisma`       | 409       | Veri çakışması        |
| `limit`         | 429       | Rate limit aşıldı     |
| `dikkat`        | 500       | Sunucu hatası         |
| `servis`        | 503       | Servis kullanılamıyor |

### Debug Aracı

```php
// Tek değişken
Konsol($degisken);

// Çoklu değişken
Konsol($data, $user, $config);

// Seçeneklerle
Konsol($arr, ['die' => true]);               // Çalışmayı durdur
Konsol($obj, ['tip' => 'json']);             // JSON formatı
Konsol($val, ['etiket' => 'API Yanıtı']);   // Etiketli
Konsol($var, ['backtrace' => true]);         // Stack trace ile
```

---

## Yapılandırma

### Geliştirici Modu

`index.php` içindeki `GELISTIRICI` sabiti:

```php
define('GELISTIRICI', true);   // Geliştirme (detaylı hatalar, dev loglar)
define('GELISTIRICI', false);  // Üretim (hatalar gizlenir)
```

### Varsayılan Route

```php
// App/Config/Ayarlar.php
define('VARSAYILAN_CONTROLLER', 'Anasayfa');
define('VARSAYILAN_METHOD', 'index');
```

### API Yapılandırması

```php
// Core/Config/Api.php
define('API_MAX_REQUEST_SIZE', 1);           // MB - Maksimum istek boyutu
define('API_RATE_LIMIT_SAYISI', 60);         // Dakikadaki istek limiti
define('API_RATE_LIMIT_SANIYE', 60);         // Pencere süresi
define('API_TOKEN_TIMEOUT_DAKIKA', 30);      // Token geçerlilik süresi
define('API_IZINLI_METHODLAR', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
define('API_IZINLI_DOMAINLER', [
    'https://yourdomain.com',
]);
```

### Önbellek Yapılandırması

```php
// App/Config/Ayarlar.php
define('ONBELLEK_OTO_TEMIZLEME_SAAT', 168);  // 168 saat = 7 gün
```

---

## Veritabanı Kullanımı

PDO tabanlı, fluent interface desteğiyle:

```php
$db = new Veritabani();

// SELECT
$kullanici = $db->sec('kullanicilar')
    ->nerede('id', 1)
    ->getir();

// INSERT
$db->ekle('kullanicilar', [
    'ad'    => 'Ali',
    'email' => 'ali@ornek.com'
]);

// UPDATE
$db->guncelle('kullanicilar')
    ->ayarla(['ad' => 'Veli'])
    ->nerede('id', 1)
    ->calistir();

// DELETE
$db->sil('kullanicilar')
    ->nerede('id', 1)
    ->calistir();
```

---

## Lisans

Bu proje [MIT Lisansı](https://opensource.org/licenses/MIT) altında lisanslanmıştır.

```
MIT License © 2026 Samed CİMEN
```
