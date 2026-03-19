<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * API Dokümantasyon Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

class Api_Dokuman
{
    /**
     * Tüm API endpoint'lerinin dokümantasyonunu döndür
     * 
     * @param array $servisler Endpoint listesi
     * @param string $format Çıktı formatı: 'json', 'html', 'array', 'xml', 'markdown', 'yaml', 'openapi', 'postman', 'text'
     * @return mixed
     */
    public static function Olustur($servisler, $format = 'array')
    {
        // Base URL'i doğru hesapla
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Proje klasörünü bul (REQUEST_URI'den dokuman kısmını çıkar)
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $baseDir = '';
        if (!empty($requestUri)) {
            // URL'den /dokuman veya benzeri endpoint ismini çıkar
            $baseDir = preg_replace('/\/(dokuman|api\.php).*$/', '', $requestUri);
        }

        $baseUrl = $protocol . '://' . $host . $baseDir;

        // Cache son güncelleme zamanını al
        $cacheKonum = (defined('KLASOR_CACHE')) ? KLASOR_CACHE : UYGULAMA . 'Cache' . DS;
        $cacheDosya = $cacheKonum . 'api_router.cache';
        $tarih = ($cacheDosya && file_exists($cacheDosya))
            ? date('Y-m-d H:i:s', filemtime($cacheDosya))
            : date('Y-m-d H:i:s');

        $dokuman = [
            'baslik' => (defined('TEMA_BASLIK') && TEMA_BASLIK ? TEMA_BASLIK . ' ' : '') . 'API Dokümantasyonu',
            'versiyon' => (defined('TEMA_VERSIYON') ? TEMA_VERSIYON : '1.0.0'),
            'son_guncelleme' => $tarih,
            'toplam_endpoint' => count($servisler),
            'base_url' => $baseUrl,

            // Tema bilgileri (App/Config/Tema.php)
            'aciklama' => (defined('TEMA_ACIKLAMA') ? TEMA_ACIKLAMA : ''),
            'organizasyon' => (defined('TEMA_SAHIP_AD') ? TEMA_SAHIP_AD : 'Scotuch'),
            'destek_email' => (defined('TEMA_SAHIP_EMAIL') ? TEMA_SAHIP_EMAIL : 'samedcimen@hotmail.com'),
            'destek_url' => (defined('TEMA_SAHIP_URL') ? TEMA_SAHIP_URL : 'https://github.com/Scotuch'),
            'lisans' => 'MIT',

            // API ayarları (Core/Config/Api.php)
            'api_rate_limit' => (defined('API_RATE_LIMIT_SAYISI') ? API_RATE_LIMIT_SAYISI : 60),
            'api_rate_limit_saniye' => (defined('API_RATE_LIMIT_SANIYE') ? API_RATE_LIMIT_SANIYE : 60),
            'api_token_timeout_dakika' => (defined('API_TOKEN_TIMEOUT_DAKIKA') ? API_TOKEN_TIMEOUT_DAKIKA : 30),

            'endpoints' => []
        ];

        // Endpoint'leri kategorilere göre ayır
        $kategoriler = [];
        foreach ($servisler as $endpoint => $detay) {
            $kategori = explode('_', $endpoint)[0];
            $kategoriler[$kategori][] = [
                'id' => $endpoint,
                'url' => $endpoint,
                'dosya' => $detay['dosya'],
                'sinif' => $detay['sinif'],
                'metod' => $detay['metod'],
                'isim' => $detay['bilgi']['isim'],
                'versiyon' => $detay['bilgi']['versiyon'],
                'aciklama' => $detay['bilgi']['aciklama'],
                'endpoint_aciklama' => $detay['bilgi']['endpoint_aciklama'],
                'kategori' => ucfirst($kategori),
                'ozellikler' => $detay['ozellikler'] ?? []
            ];
        }

        $dokuman['endpoints'] = $kategoriler;

        switch ($format) {
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
                return json_encode($dokuman, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            case 'html':
                return self::HTML($dokuman);

            case 'xml':
                header('Content-Type: application/xml; charset=utf-8');
                return self::XML($dokuman);

            case 'markdown':
            case 'md':
                header('Content-Type: text/markdown; charset=utf-8');
                return self::Markdown($dokuman);

            case 'yaml':
            case 'yml':
                header('Content-Type: application/x-yaml; charset=utf-8');
                return self::YAML($dokuman);

            case 'openapi':
            case 'swagger':
                header('Content-Type: application/json; charset=utf-8');
                return self::OpenAPI($dokuman);

            case 'postman':
                header('Content-Type: application/json; charset=utf-8');
                return self::Postman($dokuman);

            case 'text':
            case 'txt':
                header('Content-Type: text/plain; charset=utf-8');
                return self::Text($dokuman);

            case 'csv':
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="api-endpoints.csv"');
                return self::CSV($dokuman);

            case 'curl':
                header('Content-Type: text/plain; charset=utf-8');
                return self::cURL($dokuman);

            case 'httpie':
                header('Content-Type: text/plain; charset=utf-8');
                return self::HTTPie($dokuman);

            case 'php':
                header('Content-Type: text/plain; charset=utf-8');
                return self::PHP($dokuman);

            case 'javascript':
            case 'js':
                header('Content-Type: text/javascript; charset=utf-8');
                return self::JavaScript($dokuman);

            case 'python':
            case 'py':
                header('Content-Type: text/x-python; charset=utf-8');
                return self::Python($dokuman);

            case 'bash':
            case 'sh':
                header('Content-Type: text/x-shellscript; charset=utf-8');
                return self::Bash($dokuman);

            case 'ruby':
            case 'rb':
                header('Content-Type: text/x-ruby; charset=utf-8');
                return self::Ruby($dokuman);

            case 'go':
            case 'golang':
                header('Content-Type: text/x-go; charset=utf-8');
                return self::Go($dokuman);

            case 'java':
                header('Content-Type: text/x-java; charset=utf-8');
                return self::Java($dokuman);

            case 'csharp':
            case 'cs':
                header('Content-Type: text/x-csharp; charset=utf-8');
                return self::CSharp($dokuman);

            case 'typescript':
            case 'ts':
                header('Content-Type: text/typescript; charset=utf-8');
                return self::TypeScript($dokuman);

            case 'powershell':
            case 'ps1':
                header('Content-Type: text/x-powershell; charset=utf-8');
                return self::PowerShell($dokuman);

            case 'wget':
                header('Content-Type: text/plain; charset=utf-8');
                return self::Wget($dokuman);

            case 'axios':
                header('Content-Type: text/javascript; charset=utf-8');
                return self::Axios($dokuman);

            case 'insomnia':
                header('Content-Type: application/json; charset=utf-8');
                return self::Insomnia($dokuman);

            case 'swagger2':
                header('Content-Type: application/json; charset=utf-8');
                return self::Swagger2($dokuman);
            case 'test':
                return self::Test($dokuman);
            default:
                return $dokuman;
        }
    }

    private static function HTML($dokuman)
    {
        // Dökümantasyon için HTML dosyasını belirle.
        $Dosya = (defined('SISTEM_KLASOR_VIEW')) ? SISTEM_KLASOR_VIEW . 'Api_Dokuman_HTML.php' : SISTEM . 'View' . DS . 'Api_Dokuman_HTML.php';
        //include __DIR__ . '/Api_Dokuman_HTML.php';
        // HTML çıktı için buffer başlat.
        ob_start();

        if (file_exists($Dosya)) {
            try {
                include $Dosya;
                Log::Bilgi(1, "API dokümantasyon HTML dosyası yüklendi: {$Dosya}");
            } catch (Error | Exception $hata) {
                $Mesaj = Mesaj("hata", "Dosya bulunamadı: {$Dosya}", "", true);
                Log::Hata(1, $Mesaj->mesaj);
                if (GELISTIRICI) {
                    throw new Exception($Mesaj->mesaj);
                } else {
                    http_response_code($Mesaj->kod);
                    die("{$Mesaj->kod} Not Authorized");
                    exit;
                }
                return false;
            }
        } else {
            $klasor = (defined(KLASOR_VIEW)) ? KLASOR_VIEW : UYGULAMA . 'View' . DS;
            Sistem::Dosya($klasor, 'Hata');
        }

        // HTML içeriği burada oluşturulur.
        return ob_get_clean();
    }

    private static function Test($dokuman)
    {
        $baseUrl = $dokuman['base_url'];
        $html = "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$dokuman['baslik']}</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --success: #059669;
            --success-light: #10b981;
            --danger: #dc2626;
            --danger-light: #ef4444;
            --warning: #d97706;
            --warning-light: #f59e0b;
            --dark: #0f172a;
            --dark-2: #1e293b;
            --dark-3: #334155;
            --light: #f8fafc;
            --light-2: #f1f5f9;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-light: #64748b;
            --text-lighter: #94a3b8;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .main-wrapper {
            max-width: 1920px;
            margin: 0 auto;
            background: var(--light);
        }
        
        .container {
            display: grid;
            grid-template-columns: 300px 1fr;
            min-height: 100vh;
            position: relative;
        }
        
        /* Sidebar */
        .sidebar {
            background: white;
            border-right: 1px solid var(--border);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            box-shadow: var(--shadow);
        }
        
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: var(--light-2); }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: var(--text-lighter); }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .logo i {
            font-size: 1.75rem;
        }
        
        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
            font-size: 0.813rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .sidebar-content {
            padding: 1.5rem;
        }
        
        .nav-category {
            margin-bottom: 1.5rem;
        }
        
        .nav-category-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            margin-bottom: 0.75rem;
            font-weight: 700;
            padding: 0 0.5rem;
        }
        
        .nav-category-title i {
            font-size: 0.875rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0.75rem;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: var(--light-2);
            color: var(--primary);
            padding-left: 1rem;
        }
        
        .nav-link i {
            font-size: 0.75rem;
            color: var(--primary);
        }
        
        /* Main Content */
        .main-content {
            padding: 0;
            background: var(--light);
            overflow-y: auto;
            height: 100vh;
        }
        
        .main-content::-webkit-scrollbar { width: 8px; }
        .main-content::-webkit-scrollbar-track { background: var(--light-2); }
        .main-content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        .main-content::-webkit-scrollbar-thumb:hover { background: var(--text-lighter); }
        
        .content-header {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 2.5rem;
            box-shadow: var(--shadow-sm);
        }
        
        .page-title {
            font-size: 1.875rem;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .page-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            font-size: 0.875rem;
            color: var(--text-light);
        }
        
        .page-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .page-meta-item i {
            color: var(--primary);
        }
        
        .badge {
            padding: 0.375rem 0.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 6px;
            font-size: 0.813rem;
            font-weight: 700;
        }
        
        .content-body {
            padding: 2.5rem;
        }
        
        .section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
        }
        
        .section-title i {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .export-section {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(30, 64, 175, 0.05) 100%);
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px dashed var(--primary-light);
        }
        
        .export-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.75rem;
        }
        
        .export-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1rem;
            background: white;
            color: var(--text);
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .export-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .export-btn i {
            font-size: 1.125rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
        }
        
        .stat-box {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1.75rem;
            border-radius: 12px;
            color: white;
            box-shadow: var(--shadow-lg);
            transition: transform 0.2s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .stat-box:hover {
            transform: translateY(-4px);
        }
        
        .stat-box h3 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .stat-box p {
            font-size: 0.875rem;
            opacity: 0.95;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .stat-box.success {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-light) 100%);
        }
        
        .stat-box.warning {
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-light) 100%);
        }
        
        .stat-box.danger {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-light) 100%);
        }
        
        .info-card {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(217, 119, 6, 0.05) 100%);
            border: 2px solid rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            padding: 1.75rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
            font-size: 0.875rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .info-item i {
            color: var(--warning);
            font-size: 1.125rem;
        }
        
        .info-item strong {
            color: var(--text);
        }
        
        .info-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .info-item a:hover {
            text-decoration: underline;
        }
        
        .category-block {
            margin-bottom: 2.5rem;
        }
        
        .category-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary);
        }
        
        .category-title i {
            color: var(--primary);
        }
        
        .endpoint-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .endpoint-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .endpoint-card-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.03) 0%, rgba(30, 64, 175, 0.03) 100%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
        }
        
        .endpoint-card-header:hover {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(30, 64, 175, 0.08) 100%);
        }
        
        .endpoint-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }
        
        .endpoint-card-body {
            padding: 1.5rem;
            display: none;
            background: white;
            border-top: 2px solid var(--border);
        }
        
        .endpoint-card.active .endpoint-card-body {
            display: block;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .method-tag {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-sm);
        }
        
        .method-post { background: #2563eb; color: white; }
        .method-get { background: #059669; color: white; }
        .method-put { background: #d97706; color: white; }
        .method-delete { background: #dc2626; color: white; }
        .method-patch { background: #7c3aed; color: white; }
        
        .endpoint-name {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .endpoint-desc {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-top: 0.375rem;
        }
        
        .endpoint-details {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .detail-label {
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .detail-label i {
            color: var(--primary);
        }
        
        .detail-value {
            color: var(--text-light);
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.813rem;
        }
        
        .test-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: var(--shadow);
        }
        
        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .test-btn i {
            font-size: 1rem;
        }
        
        .toggle-icon {
            transition: transform 0.3s;
            color: var(--primary);
            font-size: 1.25rem;
        }
        
        .endpoint-card.active .toggle-icon {
            transform: rotate(180deg);
        }
        
        /* Mobile Test Panel */
        .mobile-test-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-xl);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .mobile-test-toggle:hover {
            transform: scale(1.1);
        }
        
        .mobile-test-toggle:active {
            transform: scale(0.95);
        }
        
        .test-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .test-modal.active {
            display: flex;
        }
        
        .test-panel {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.3s;
        }
        
        @keyframes slideUp {
            from { transform: translateY(100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .test-panel::-webkit-scrollbar { width: 6px; }
        .test-panel::-webkit-scrollbar-track { background: var(--light-2); }
        .test-panel::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        
        .test-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem;
            border-bottom: 2px solid var(--border);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }
        
        .test-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 800;
        }
        
        .test-header i {
            font-size: 1.5rem;
        }
        
        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .test-panel-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        
        .form-label i {
            color: var(--primary);
        }
        
        .form-input, .form-textarea {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            transition: all 0.2s;
            background: var(--light-2);
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .send-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: var(--shadow);
        }
        
        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .response-box {
            background: var(--dark);
            color: #e2e8f0;
            padding: 1.25rem;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.813rem;
            max-height: 300px;
            overflow: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
        }
        
        .response-box::-webkit-scrollbar { width: 6px; }
        .response-box::-webkit-scrollbar-track { background: var(--dark-2); }
        .response-box::-webkit-scrollbar-thumb { background: var(--dark-3); border-radius: 3px; }
        
        .status-success { color: var(--success-light); font-weight: 700; }
        .status-error { color: var(--danger-light); font-weight: 700; }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                width: 300px;
                height: 100vh;
                z-index: 999;
                transition: left 0.3s;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .mobile-menu-toggle {
                position: fixed;
                top: 1rem;
                left: 1rem;
                width: 48px;
                height: 48px;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1.25rem;
                cursor: pointer;
                box-shadow: var(--shadow-lg);
                z-index: 998;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .mobile-test-toggle {
                display: flex;
            }
            
            .content-header {
                padding: 1.25rem 1.5rem;
                padding-left: 5rem;
            }
            
            .content-body {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 640px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .export-buttons {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .endpoint-details {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .detail-label {
                padding-bottom: 0.25rem;
                border-bottom: 1px solid var(--border);
            }
            
            .content-header {
                padding: 1rem 1rem;
                padding-left: 4.5rem;
            }
            
            .content-body {
                padding: 1rem;
            }
            
            .section {
                padding: 1.25rem;
            }
            
            /* Token input ve buton mobilde tek sütun */
            #tokenInput {
                font-size: 0.75rem !important;
            }
        }
    </style>
</head>
<body>
    <div class='main-wrapper'>
        <button class='mobile-menu-toggle' onclick='toggleSidebar()' style='display:none;'>
            <i class='fas fa-bars'></i>
        </button>
        
        <div class='container'>
            <div class='sidebar' id='sidebar'>
                <div class='sidebar-header'>
                    <div class='logo'>
                        <i class='fas fa-rocket'></i>
                        <span>API Doküman</span>
                    </div>
                    <div class='version-badge'>
                        <i class='fas fa-tag'></i>
                        <span>v{$dokuman['versiyon']}</span>
                    </div>
                </div>
                
                <div class='sidebar-content'>";

        // Sidebar menu
        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $html .= "<div class='nav-category'>";
            $html .= "<div class='nav-category-title'>";
            $html .= "<i class='fas fa-folder'></i>";
            $html .= "<span>" . ucfirst($kategori) . "</span>";
            $html .= "</div>";
            foreach ($endpoints as $endpoint) {
                $html .= "<a href='#" . htmlspecialchars($endpoint['id']) . "' class='nav-link' onclick='closeSidebar()'>";
                $html .= "<i class='fas fa-circle'></i>";
                $html .= "<span>" . htmlspecialchars($endpoint['url']) . "</span>";
                $html .= "</a>";
            }
            $html .= "</div>";
        }

        $html .= "</div>
            </div>
            
            <div class='main-content'>
                <div class='content-header'>
                    <h1 class='page-title'>{$dokuman['baslik']}</h1>
                    <div class='page-meta'>
                        <span class='badge'>
                            <i class='fas fa-code-branch'></i>
                            v{$dokuman['versiyon']}
                        </span>
                        <div class='page-meta-item'>
                            <i class='fas fa-calendar-alt'></i>
                            <span>{$dokuman['son_guncelleme']}</span>
                        </div>
                        <div class='page-meta-item'>
                            <i class='fas fa-plug'></i>
                            <span>{$dokuman['toplam_endpoint']} endpoint</span>
                        </div>
                    </div>";

        // API açıklaması varsa ekle
        if (!empty($dokuman['aciklama'])) {
            $html .= "
                    <p style='margin-top:1rem;color:var(--text-light);font-size:0.938rem;'><i class='fas fa-info-circle'></i> {$dokuman['aciklama']}</p>";
        }

        $html .= "
                </div>
                
                <div class='content-body'>
                    <div class='section'>
                        <div class='section-title'>
                            <i class='fas fa-download'></i>
                            <span>Dökümanı İndir</span>
                        </div>
                        <div class='export-section'>
                            <div class='export-buttons'>";

        $exportFormats = [
            ['url' => Url_Olustur('dokuman/json'), 'icon' => 'fas fa-file-code', 'label' => 'JSON'],
            ['url' => Url_Olustur('dokuman/xml'), 'icon' => 'fas fa-code', 'label' => 'XML'],
            ['url' => Url_Olustur('dokuman/markdown'), 'icon' => 'fas fa-file-lines', 'label' => 'Markdown'],
            ['url' => Url_Olustur('dokuman/yaml'), 'icon' => 'fas fa-file-code', 'label' => 'YAML'],
            ['url' => Url_Olustur('dokuman/openapi'), 'icon' => 'fas fa-file-contract', 'label' => 'OpenAPI'],
            ['url' => Url_Olustur('dokuman/postman'), 'icon' => 'fas fa-paper-plane', 'label' => 'Postman'],
            ['url' => Url_Olustur('dokuman/text'), 'icon' => 'fas fa-file-lines', 'label' => 'Text'],
            ['url' => Url_Olustur('dokuman/csv'), 'icon' => 'fas fa-table', 'label' => 'CSV'],
            ['url' => Url_Olustur('dokuman/curl'), 'icon' => 'fas fa-terminal', 'label' => 'cURL'],
            ['url' => Url_Olustur('dokuman/php'), 'icon' => 'fab fa-php', 'label' => 'PHP'],
            ['url' => Url_Olustur('dokuman/javascript'), 'icon' => 'fab fa-js', 'label' => 'JavaScript'],
            ['url' => Url_Olustur('dokuman/python'), 'icon' => 'fab fa-python', 'label' => 'Python']
        ];

        foreach ($exportFormats as $format) {
            $html .= "<a href='{$format['url']}' class='export-btn'>
                            <i class='{$format['icon']}'></i>
                            <span>{$format['label']}</span>
                        </a>";
        }

        $html .= "</div>
                        </div>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>
                            <i class='fas fa-chart-bar'></i>
                            <span>İstatistikler</span>
                        </div>
                        <div class='stats-grid'>
                            <div class='stat-box'>
                                <h3>{$dokuman['toplam_endpoint']}</h3>
                                <p><i class='fas fa-plug'></i> Toplam Endpoint</p>
                            </div>
                            <div class='stat-box success'>
                                <h3>" . count($dokuman['endpoints']) . "</h3>
                                <p><i class='fas fa-folder'></i> Kategori</p>
                            </div>
                            <div class='stat-box warning'>
                                <h3>{$dokuman['api_rate_limit']}</h3>
                                <p><i class='fas fa-tachometer-alt'></i> Rate Limit ({$dokuman['api_rate_limit_saniye']}sn)</p>
                            </div>
                            <div class='stat-box danger'>
                                <h3>{$dokuman['api_token_timeout_dakika']}</h3>
                                <p><i class='fas fa-clock'></i> Token Süresi (dk)</p>
                            </div>
                        </div>
                    </div>";

        // API bilgi ve iletişim kartı
        if (!empty($dokuman['organizasyon']) || !empty($dokuman['destek_email'])) {
            $html .= "<div class='section info-card'>
                        <div class='section-title'>
                            <i class='fas fa-address-card'></i>
                            <span>İletişim ve Destek</span>
                        </div>
                        <div class='info-grid'>";

            if (!empty($dokuman['organizasyon'])) {
                $html .= "<div class='info-item'>
                            <i class='fas fa-building'></i>
                            <div><strong>Organizasyon:</strong> {$dokuman['organizasyon']}</div>
                        </div>";
            }
            if (!empty($dokuman['destek_email'])) {
                $html .= "<div class='info-item'>
                            <i class='fas fa-envelope'></i>
                            <div><strong>E-posta:</strong> <a href='mailto:{$dokuman['destek_email']}'>{$dokuman['destek_email']}</a></div>
                        </div>";
            }
            if (!empty($dokuman['destek_url'])) {
                $html .= "<div class='info-item'>
                            <i class='fas fa-globe'></i>
                            <div><strong>Web:</strong> <a href='{$dokuman['destek_url']}' target='_blank'>{$dokuman['destek_url']}</a></div>
                        </div>";
            }
            if (!empty($dokuman['lisans'])) {
                $html .= "<div class='info-item'>
                            <i class='fas fa-balance-scale'></i>
                            <div><strong>Lisans:</strong> {$dokuman['lisans']}</div>
                        </div>";
            }

            $html .= "</div>
                    </div>";
        }

        // Token Üretme Bölümü
        $html .= "<div class='section'>
                    <div class='section-title'>
                        <i class='fas fa-key'></i>
                        <span>Token Üret</span>
                    </div>
                    <div style='background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(30, 64, 175, 0.05) 100%); padding: 1.5rem; border-radius: 10px; border: 2px dashed var(--primary-light);'>
                        <p style='margin-bottom: 1rem; color: var(--text-light); font-size: 0.875rem;'>
                            <i class='fas fa-info-circle'></i> API'ye erişim için token oluşturun. Token {$dokuman['api_token_timeout_dakika']} dakika geçerlidir.
                        </p>
                        <div style='display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; align-items: center;'>
                            <input type='text' id='tokenInput' readonly placeholder='Token burada görünecek...' onclick='copyTokenFromInput()' style='padding: 1rem 1.25rem; border: 2px solid var(--border); border-radius: 8px; font-family: Monaco, monospace; font-size: 0.875rem; background: white; color: var(--text); cursor: pointer; transition: all 0.2s;' title='Tıklayarak kopyala'>
                            <button onclick='generateToken()' style='padding: 1rem 1.5rem; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow); white-space: nowrap;' onmouseover='this.style.transform=\"translateY(-2px)\"; this.style.boxShadow=\"var(--shadow-lg)\"' onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"var(--shadow)\"'>
                                <i class='fas fa-key'></i>
                                <span>Token Oluştur</span>
                            </button>
                        </div>
                        <div id='tokenMessage' style='display: none; margin-top: 0.75rem; padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.813rem; font-weight: 600;'></div>
                    </div>
                </div>";

        // Endpoint'leri kategorilere göre listele
        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $html .= "<div class='section category-block'>";
            $html .= "<div class='category-title'>";
            $html .= "<i class='fas fa-folder-open'></i>";
            $html .= "<span>" . ucfirst($kategori) . "</span>";
            $html .= "</div>";

            foreach ($endpoints as $endpoint) {
                $html .= "<div class='endpoint-card' id='" . htmlspecialchars($endpoint['id']) . "' data-endpoint='" . htmlspecialchars(json_encode($endpoint)) . "'>";
                $html .= "<div class='endpoint-card-header' onclick='this.parentElement.classList.toggle(\"active\")'>";
                $html .= "<div class='endpoint-info'>";
                $html .= "<span class='method-tag method-post'>POST</span>";
                $html .= "<div>";
                $html .= "<div class='endpoint-name'>" . htmlspecialchars($endpoint['url']) . "</div>";

                if ($endpoint['endpoint_aciklama'] || $endpoint['aciklama']) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $html .= "<div class='endpoint-desc'>" . htmlspecialchars($aciklama) . "</div>";
                }

                $html .= "</div></div>";
                $html .= "<i class='fas fa-chevron-down toggle-icon'></i>";
                $html .= "</div>";

                $html .= "<div class='endpoint-card-body'>";
                $html .= "<div class='endpoint-details'>";

                if ($endpoint['isim']) {
                    $html .= "<div class='detail-label'><i class='fas fa-tag'></i> Servis</div>";
                    $html .= "<div class='detail-value'>" . htmlspecialchars($endpoint['isim']) . "</div>";
                }

                $html .= "<div class='detail-label'><i class='fas fa-cube'></i> Sınıf</div>";
                $html .= "<div class='detail-value'>" . htmlspecialchars($endpoint['sinif']) . "::" . htmlspecialchars($endpoint['metod']) . "()</div>";

                $html .= "<div class='detail-label'><i class='fas fa-file-code'></i> Dosya</div>";
                $html .= "<div class='detail-value'>" . htmlspecialchars($endpoint['dosya']) . "</div>";

                // Endpoint özellikleri
                if (!empty($endpoint['ozellikler'])) {
                    $ozellikler = $endpoint['ozellikler'];

                    // HTTP metodları
                    $izinliMetodlar = [];
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'] as $method) {
                        if (isset($ozellikler[$method]) && $ozellikler[$method] === true) {
                            $izinliMetodlar[] = $method;
                        }
                    }

                    if (!empty($izinliMetodlar)) {
                        $html .= "<div class='detail-label'><i class='fas fa-exchange-alt'></i> Metodlar</div>";
                        $html .= "<div class='detail-value'>";
                        foreach ($izinliMetodlar as $method) {
                            $tagClass = 'method-' . strtolower($method);
                            $html .= "<span class='method-tag {$tagClass}' style='margin-right:0.5rem;'>{$method}</span>";
                        }
                        $html .= "</div>";
                    }

                    // Token
                    $tokenGerekli = isset($ozellikler['TOKEN']) ? $ozellikler['TOKEN'] : true;
                    $html .= "<div class='detail-label'><i class='fas fa-key'></i> Token</div>";
                    $html .= "<div class='detail-value'>";
                    if ($tokenGerekli === false) {
                        $html .= "<span style='color:var(--success);font-weight:700;'><i class='fas fa-check-circle'></i> Gerekmiyor</span>";
                    } else {
                        $html .= "<span style='color:var(--danger);font-weight:700;'><i class='fas fa-exclamation-circle'></i> Gerekli</span>";
                    }
                    $html .= "</div>";
                }

                $html .= "</div>";

                $html .= "<button class='test-btn' onclick='loadEndpointToTest(this)'>
                            <i class='fas fa-flask'></i>
                            <span>Test Et</span>
                        </button>";
                $html .= "</div></div>";
            }

            $html .= "</div>";
        }

        $html .= "</div>
            </div>
        </div>
        
        <!-- Mobile Test Button -->
        <button class='mobile-test-toggle' onclick='openTestModal()'>
            <i class='fas fa-flask'></i>
        </button>
        
        <!-- Test Modal -->
        <div class='test-modal' id='testModal'>
            <div class='test-panel'>
                <div class='test-panel-header'>
                    <div class='test-header'>
                        <i class='fas fa-flask'></i>
                        <span>API Test</span>
                    </div>
                    <button class='close-modal' onclick='closeTestModal()'>
                        <i class='fas fa-times'></i>
                    </button>
                </div>
                
                <div class='test-panel-body'>
                    <div class='form-group'>
                        <label class='form-label'>
                            <i class='fas fa-plug'></i>
                            <span>Endpoint</span>
                        </label>
                        <input type='text' class='form-input' id='testEndpoint' placeholder='endpoint_adi' readonly>
                    </div>
                    
                    <div class='form-group'>
                        <label class='form-label'>
                            <i class='fas fa-key'></i>
                            <span>Token (Opsiyonel)</span>
                        </label>
                        <input type='text' class='form-input' id='testToken' placeholder='Bearer token...'>
                    </div>
                    
                    <div class='form-group'>
                        <label class='form-label'>
                            <i class='fas fa-code'></i>
                            <span>İstek Parametreleri (JSON)</span>
                        </label>
                        <textarea class='form-textarea' id='testParams' placeholder='{&#10;  \"param1\": \"value1\",&#10;  \"param2\": \"value2\"&#10;}'></textarea>
                    </div>
                    
                    <button class='send-btn' onclick='sendTestRequest()' id='sendBtn'>
                        <i class='fas fa-paper-plane'></i>
                        <span>İstek Gönder</span>
                    </button>
                    
                    <div class='form-group' style='margin-top:1.5rem;'>
                        <label class='form-label'>
                            <i class='fas fa-terminal'></i>
                            <span>Yanıt</span>
                        </label>
                        <div class='response-box' id='responseBox'>Henüz istek gönderilmedi...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const baseUrl = '{$baseUrl}';
        let currentEndpoint = null;
        
        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('active');
            }
        }
        
        // Test modal functions
        function openTestModal() {
            document.getElementById('testModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeTestModal() {
            document.getElementById('testModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.getElementById('testModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTestModal();
            }
        });
        
        // Smooth scroll to endpoint
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const yOffset = -20;
                    const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    const mainContent = document.querySelector('.main-content');
                    if (mainContent) {
                        mainContent.scrollTo({top: y, behavior: 'smooth'});
                    }
                    setTimeout(() => target.classList.add('active'), 300);
                }
            });
        });
        
        // Load endpoint to test panel
        function loadEndpointToTest(btn) {
            const card = btn.closest('.endpoint-card');
            const endpointData = JSON.parse(card.getAttribute('data-endpoint'));
            currentEndpoint = endpointData;
            
            document.getElementById('testEndpoint').value = endpointData.url;
            document.getElementById('testParams').value = JSON.stringify({
                \"param1\": \"value1\"
            }, null, 2);
            document.getElementById('responseBox').textContent = 'Endpoint yüklendi. Parametreleri girin ve istek gönderin.';
            
            openTestModal();
        }
        
        // Send test request
        async function sendTestRequest() {
            const endpoint = document.getElementById('testEndpoint').value;
            const token = document.getElementById('testToken').value;
            const paramsText = document.getElementById('testParams').value;
            const responseBox = document.getElementById('responseBox');
            const sendBtn = document.getElementById('sendBtn');
            
            if (!endpoint) {
                responseBox.innerHTML = '<span class=\"status-error\"><i class=\"fas fa-exclamation-triangle\"></i> Lütfen bir endpoint seçin!</span>';
                return;
            }
            
            let params = {};
            try {
                params = paramsText.trim() ? JSON.parse(paramsText) : {};
            } catch (e) {
                responseBox.innerHTML = '<span class=\"status-error\"><i class=\"fas fa-times-circle\"></i> Geçersiz JSON formatı: ' + e.message + '</span>';
                return;
            }
            
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i><span>Gönderiliyor...</span>';
            responseBox.textContent = 'İstek gönderiliyor...';
            
            try {
                const requestBody = { id: endpoint, istekler: params };
                if (token) requestBody.token = token;
                
                const response = await fetch(baseUrl + '/api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestBody)
                });
                
                const responseText = await response.text();
                let data;
                
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    const statusIcon = response.ok ? '<i class=\"fas fa-check-circle\"></i>' : '<i class=\"fas fa-times-circle\"></i>';
                    responseBox.innerHTML = `<span class=\"status-error\">\${statusIcon} HTTP \${response.status} - JSON Parse Hatası</span>\\n\\n<strong>Sunucu Yanıtı:</strong>\\n` + responseText;
                    return;
                }
                
                const statusClass = response.ok ? 'status-success' : 'status-error';
                const statusIcon = response.ok ? '<i class=\"fas fa-check-circle\"></i>' : '<i class=\"fas fa-times-circle\"></i>';
                responseBox.innerHTML = `<span class=\"\${statusClass}\">\${statusIcon} HTTP \${response.status}</span>\\n\\n` + JSON.stringify(data, null, 2);
                
            } catch (error) {
                responseBox.innerHTML = '<span class=\"status-error\"><i class=\"fas fa-exclamation-triangle\"></i> İstek Hatası: ' + error.message + '</span>\\n\\n' +
                    'Kontrol edin:\\n• API: ' + baseUrl + '/api\\n• Sunucu çalışıyor mu?\\n• CORS ayarları';
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class=\"fas fa-paper-plane\"></i><span>İstek Gönder</span>';
            }
        }
        
        // Keyboard shortcuts
        document.getElementById('testParams')?.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                sendTestRequest();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTestModal();
            }
        });
        
        // Responsive menu toggle visibility
        function updateMenuToggle() {
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            if (window.innerWidth <= 1024) {
                if (!menuToggle) {
                    const btn = document.createElement('button');
                    btn.className = 'mobile-menu-toggle';
                    btn.onclick = toggleSidebar;
                    btn.innerHTML = '<i class=\"fas fa-bars\"></i>';
                    document.body.appendChild(btn);
                }
            }
        }
        
        updateMenuToggle();
        window.addEventListener('resize', updateMenuToggle);
        
        // Token generation function
        async function generateToken() {
            const tokenInput = document.getElementById(\"tokenInput\");
            const tokenMessage = document.getElementById(\"tokenMessage\");
            const btn = event.target.closest('button');
            const originalBtnContent = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = \"<i class='fas fa-spinner fa-spin'></i><span>Oluşturuluyor...</span>\";
            tokenInput.value = \"Token oluşturuluyor...\";
            tokenMessage.style.display = \"none\";
            
            try {
                const response = await fetch(baseUrl + \"/api?id=token\", {
                    method: \"POST\",
                    headers: {
                        \"Content-Type\": \"application/json\",
                        \"Accept\": \"application/json\"
                    },
                    body: JSON.stringify({})
                });
                
                const data = await response.json();
                
                if (data.kod === 200 && data.token) {
                    tokenInput.value = data.token;
                    const sure = data.gecerlilik_suresi || \"\";
                    const mesaj = data.mesaj || data.baslik || \"Token başarıyla oluşturuldu\";
                    
                    tokenMessage.innerHTML = \"<i class='fas fa-check-circle'></i> \" + mesaj + (sure ? \" (\" + sure + \" dk)\" : \"\");
                    tokenMessage.style.display = \"block\";
                    tokenMessage.style.background = \"rgba(5, 150, 105, 0.1)\";
                    tokenMessage.style.color = \"var(--success)\";
                    tokenMessage.style.border = \"1px solid rgba(5, 150, 105, 0.3)\";
                } else {
                    const hata = data.mesaj || data.baslik || \"Token oluşturulamadı\";
                    tokenInput.value = \"\";
                    tokenInput.placeholder = \"Hata: \" + hata;
                    
                    tokenMessage.innerHTML = \"<i class='fas fa-times-circle'></i> \" + hata;
                    tokenMessage.style.display = \"block\";
                    tokenMessage.style.background = \"rgba(220, 38, 38, 0.1)\";
                    tokenMessage.style.color = \"var(--danger)\";
                    tokenMessage.style.border = \"1px solid rgba(220, 38, 38, 0.3)\";
                }
            } catch (error) {
                tokenInput.value = \"\";
                tokenInput.placeholder = \"Hata oluştu\";
                
                tokenMessage.innerHTML = \"<i class='fas fa-exclamation-triangle'></i> İstek Hatası: \" + error.message;
                tokenMessage.style.display = \"block\";
                tokenMessage.style.background = \"rgba(220, 38, 38, 0.1)\";
                tokenMessage.style.color = \"var(--danger)\";
                tokenMessage.style.border = \"1px solid rgba(220, 38, 38, 0.3)\";
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
            }
        }
        
        function copyTokenFromInput() {
            const tokenInput = document.getElementById('tokenInput');
            const token = tokenInput.value;
            const tokenMessage = document.getElementById('tokenMessage');
            
            if (!token || token === 'Token oluşturuluyor...' || token.startsWith('Hata:')) {
                return;
            }
            
            tokenInput.select();
            tokenInput.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(token).then(() => {
                const originalBorder = tokenInput.style.borderColor;
                tokenInput.style.borderColor = 'var(--success)';
                tokenInput.style.boxShadow = '0 0 0 4px rgba(5, 150, 105, 0.2)';
                
                tokenMessage.innerHTML = \"<i class='fas fa-check'></i> Token kopyalandı!\";
                tokenMessage.style.display = \"block\";
                tokenMessage.style.background = \"rgba(5, 150, 105, 0.1)\";
                tokenMessage.style.color = \"var(--success)\";
                tokenMessage.style.border = \"1px solid rgba(5, 150, 105, 0.3)\";
                
                setTimeout(() => {
                    tokenInput.style.borderColor = originalBorder;
                    tokenInput.style.boxShadow = 'none';
                }, 2000);
            }).catch(err => {
                tokenMessage.innerHTML = \"<i class='fas fa-times'></i> Kopyalama başarısız\";
                tokenMessage.style.display = \"block\";
                tokenMessage.style.background = \"rgba(220, 38, 38, 0.1)\";
                tokenMessage.style.color = \"var(--danger)\";
                tokenMessage.style.border = \"1px solid rgba(220, 38, 38, 0.3)\";
            });
        }
    </script>
</body>
</html>";
        return $html;
    }

    /**
     * XML formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function XML($dokuman)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<api>' . "\n";

        // Metadata alanlarını otomatik yazdır
        foreach ($dokuman as $key => $value) {
            if ($key !== 'endpoints' && !is_array($value)) {
                $xml .= "  <{$key}>{$value}</{$key}>\n";
            }
        }

        $xml .= "  <endpoints>\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $xml .= "    <kategori name=\"{$kategori}\">\n";
            foreach ($endpoints as $endpoint) {
                $xml .= "      <endpoint>\n";
                $xml .= "        <id>{$endpoint['id']}</id>\n";
                $xml .= "        <url>{$endpoint['url']}</url>\n";
                $xml .= "        <kategori>{$endpoint['kategori']}</kategori>\n";
                $xml .= "        <sinif>{$endpoint['sinif']}</sinif>\n";
                $xml .= "        <metod>{$endpoint['metod']}</metod>\n";
                $xml .= "        <dosya>{$endpoint['dosya']}</dosya>\n";

                if (!empty($endpoint['isim'])) {
                    $xml .= "        <isim><![CDATA[{$endpoint['isim']}]]></isim>\n";
                }
                if (!empty($endpoint['aciklama'])) {
                    $xml .= "        <aciklama><![CDATA[{$endpoint['aciklama']}]]></aciklama>\n";
                }
                if (!empty($endpoint['endpoint_aciklama'])) {
                    $xml .= "        <endpoint_aciklama><![CDATA[{$endpoint['endpoint_aciklama']}]]></endpoint_aciklama>\n";
                }

                if (!empty($endpoint['ozellikler'])) {
                    $xml .= "        <ozellikler>\n";
                    foreach ($endpoint['ozellikler'] as $key => $value) {
                        $val = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                        $xml .= "          <{$key}>{$val}</{$key}>\n";
                    }
                    $xml .= "        </ozellikler>\n";
                }

                $xml .= "      </endpoint>\n";
            }
            $xml .= "    </kategori>\n";
        }

        $xml .= "  </endpoints>\n";
        $xml .= '</api>';

        return $xml;
    }

    /**
     * Markdown formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function Markdown($dokuman)
    {
        $md = "# {$dokuman['baslik']}\n\n";

        // Metadata alanlarını otomatik yazdır
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $formatValue = ($key === 'base_url') ? "`{$value}`" : $value;
                $md .= "**{$label}:** {$formatValue}  \n";
            }
        }
        $md .= "\n";

        $md .= "---\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $md .= "## 📁 " . ucfirst($kategori) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $md .= "### `{$endpoint['url']}`\n\n";

                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $md .= "{$aciklama}\n\n";
                }

                $md .= "**Detaylar:**\n";
                $md .= "- **Sınıf:** `{$endpoint['sinif']}::{$endpoint['metod']}()`\n";
                $md .= "- **Dosya:** `{$endpoint['dosya']}`\n";

                if (!empty($endpoint['isim'])) {
                    $md .= "- **Servis:** {$endpoint['isim']}\n";
                }

                if (!empty($endpoint['ozellikler'])) {
                    $ozellikler = $endpoint['ozellikler'];

                    // HTTP Metodları
                    $metodlar = [];
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'] as $method) {
                        if (isset($ozellikler[$method]) && $ozellikler[$method] === true) {
                            $metodlar[] = "`{$method}`";
                        }
                    }
                    if (!empty($metodlar)) {
                        $md .= "- **İzin Verilen Metodlar:** " . implode(', ', $metodlar) . "\n";
                    }

                    // Token
                    $tokenGerekli = isset($ozellikler['TOKEN']) ? $ozellikler['TOKEN'] : true;
                    $md .= "- **Token:** " . ($tokenGerekli ? '✅ Gerekli' : '❌ Gerekmiyor') . "\n";
                }

                $md .= "\n";
            }

            $md .= "---\n\n";
        }

        return $md;
    }

    /**
     * YAML formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function YAML($dokuman)
    {
        $yaml = "api:\n";

        // Metadata alanlarını otomatik yazdır
        foreach ($dokuman as $key => $value) {
            if ($key !== 'endpoints' && !is_array($value)) {
                $formatValue = is_numeric($value) ? $value : "\"{$value}\"";
                $yaml .= "  {$key}: {$formatValue}\n";
            }
        }
        $yaml .= "\n";

        $yaml .= "endpoints:\n";
        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $yaml .= "  {$kategori}:\n";
            foreach ($endpoints as $endpoint) {
                $yaml .= "    - id: \"{$endpoint['id']}\"\n";
                $yaml .= "      url: \"{$endpoint['url']}\"\n";
                $yaml .= "      kategori: \"{$endpoint['kategori']}\"\n";
                $yaml .= "      sinif: \"{$endpoint['sinif']}\"\n";
                $yaml .= "      metod: \"{$endpoint['metod']}\"\n";
                $yaml .= "      dosya: \"{$endpoint['dosya']}\"\n";

                if (!empty($endpoint['isim'])) {
                    $yaml .= "      isim: \"{$endpoint['isim']}\"\n";
                }
                if (!empty($endpoint['aciklama'])) {
                    $yaml .= "      aciklama: \"{$endpoint['aciklama']}\"\n";
                }
                if (!empty($endpoint['endpoint_aciklama'])) {
                    $yaml .= "      endpoint_aciklama: \"{$endpoint['endpoint_aciklama']}\"\n";
                }

                if (!empty($endpoint['ozellikler'])) {
                    $yaml .= "      ozellikler:\n";
                    foreach ($endpoint['ozellikler'] as $key => $value) {
                        $val = is_bool($value) ? ($value ? 'true' : 'false') : "\"{$value}\"";
                        $yaml .= "        {$key}: {$val}\n";
                    }
                }
            }
        }

        return $yaml;
    }

    /**
     * OpenAPI 3.0 formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function OpenAPI($dokuman)
    {
        // Info alanını otomatik oluştur
        $info = [
            'title' => $dokuman['baslik'],
            'version' => $dokuman['versiyon'] ?? '1.0.0'
        ];

        // Description oluştur
        $descParts = [];
        foreach ($dokuman as $key => $value) {
            if (!in_array($key, ['baslik', 'versiyon', 'base_url', 'endpoints']) && !is_array($value)) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $descParts[] = "{$label}: {$value}";
            }
        }
        if (!empty($descParts)) {
            $info['description'] = implode(' | ', $descParts);
        }

        // Contact bilgisi varsa ekle
        if (isset($dokuman['sahibi'])) {
            $info['contact'] = ['name' => $dokuman['sahibi']];
        }

        $openapi = [
            'openapi' => '3.0.0',
            'info' => $info,
            'servers' => [
                ['url' => $dokuman['base_url']]
            ],
            'paths' => []
        ];

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            foreach ($endpoints as $endpoint) {
                $path = '/api';
                $openapi['paths'][$path][$path] = [];

                // HTTP metodlarını belirle
                $metodlar = ['post']; // Varsayılan
                if (!empty($endpoint['ozellikler'])) {
                    $metodlar = [];
                    foreach (['get', 'post', 'put', 'delete', 'patch'] as $method) {
                        if (
                            isset($endpoint['ozellikler'][strtoupper($method)]) &&
                            $endpoint['ozellikler'][strtoupper($method)] === true
                        ) {
                            $metodlar[] = $method;
                        }
                    }
                    if (empty($metodlar)) $metodlar = ['post'];
                }

                foreach ($metodlar as $method) {
                    $openapi['paths'][$path][$method] = [
                        'summary' => $endpoint['url'],
                        'description' => $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'] ?: '',
                        'tags' => [ucfirst($kategori)],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => [
                                                'type' => 'string',
                                                'example' => $endpoint['id']
                                            ],
                                            'istekler' => [
                                                'type' => 'object',
                                                'description' => 'Endpoint parametreleri'
                                            ]
                                        ],
                                        'required' => ['id']
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Başarılı yanıt',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];

                    // Token kontrolü
                    $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                        $endpoint['ozellikler']['TOKEN'] : true;

                    if ($tokenGerekli) {
                        $openapi['paths'][$path][$method]['security'] = [
                            ['bearerAuth' => []]
                        ];
                    }
                }
            }
        }

        // Security scheme ekle
        $openapi['components'] = [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT'
                ]
            ]
        ];

        return json_encode($openapi, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Postman Collection formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function Postman($dokuman)
    {
        // Description'ı otomatik oluştur
        $descParts = [];
        foreach ($dokuman as $key => $value) {
            if (!in_array($key, ['baslik', 'base_url', 'endpoints']) && !is_array($value)) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $descParts[] = "{$label}: {$value}";
            }
        }

        $collection = [
            'info' => [
                'name' => $dokuman['baslik'],
                'description' => 'API Koleksiyonu - ' . implode(' | ', $descParts),
                'version' => $dokuman['versiyon'] ?? '1.0.0',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            ],
            'item' => []
        ];

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $folder = [
                'name' => ucfirst($kategori),
                'item' => []
            ];

            foreach ($endpoints as $endpoint) {
                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;

                $request = [
                    'name' => $endpoint['url'],
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            [
                                'key' => 'Content-Type',
                                'value' => 'application/json'
                            ]
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'id' => $endpoint['id'],
                                'istekler' => new stdClass()
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        ],
                        'url' => [
                            'raw' => $dokuman['base_url'] . '/api',
                            'host' => [parse_url($dokuman['base_url'], PHP_URL_HOST)],
                            'path' => ['api']
                        ],
                        'description' => $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'] ?: ''
                    ]
                ];

                if ($tokenGerekli) {
                    $request['request']['header'][] = [
                        'key' => 'Authorization',
                        'value' => 'Bearer {{token}}',
                        'type' => 'text'
                    ];
                }

                $folder['item'][] = $request;
            }

            $collection['item'][] = $folder;
        }

        return json_encode($collection, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Düz metin formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function Text($dokuman)
    {
        $text = str_repeat('=', 60) . "\n";
        $text .= "  {$dokuman['baslik']}\n";
        $text .= str_repeat('=', 60) . "\n\n";

        // Metadata alanlarını otomatik yazdır
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        $maxLen = 15;
        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $text .= str_pad($label, $maxLen) . ": {$value}\n";
            }
        }
        $text .= "\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $text .= str_repeat('-', 60) . "\n";
            $text .= "KATEGORI: " . strtoupper($kategori) . "\n";
            $text .= str_repeat('-', 60) . "\n\n";

            foreach ($endpoints as $i => $endpoint) {
                $text .= "[" . ($i + 1) . "] {$endpoint['url']}\n";
                $text .= str_repeat('.', 60) . "\n";

                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $text .= "Açıklama : {$aciklama}\n";
                }

                $text .= "Sınıf    : {$endpoint['sinif']}::{$endpoint['metod']}()\n";
                $text .= "Dosya    : {$endpoint['dosya']}\n";

                if (!empty($endpoint['isim'])) {
                    $text .= "Servis   : {$endpoint['isim']}\n";
                }

                if (!empty($endpoint['ozellikler'])) {
                    $ozellikler = $endpoint['ozellikler'];

                    // HTTP Metodları
                    $metodlar = [];
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'] as $method) {
                        if (isset($ozellikler[$method]) && $ozellikler[$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                    if (!empty($metodlar)) {
                        $text .= "Metodlar : " . implode(', ', $metodlar) . "\n";
                    }

                    // Token
                    $tokenGerekli = isset($ozellikler['TOKEN']) ? $ozellikler['TOKEN'] : true;
                    $text .= "Token    : " . ($tokenGerekli ? 'Gerekli' : 'Gerekmiyor') . "\n";
                }

                $text .= "\n";
            }
        }

        $text .= str_repeat('=', 60) . "\n";
        $text .= "Son - {$dokuman['son_guncelleme']}\n";
        $text .= str_repeat('=', 60) . "\n";

        return $text;
    }

    /**
     * CSV formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function CSV($dokuman)
    {
        // Metadata başlığını otomatik oluştur
        $metaParts = [];
        foreach ($dokuman as $key => $value) {
            if ($key !== 'endpoints' && !is_array($value)) {
                if ($key === 'baslik') {
                    $metaParts[] = $value;
                } elseif ($key === 'versiyon') {
                    $metaParts[] = "v{$value}";
                } else {
                    $label = ucfirst(str_replace('_', ' ', $key));
                    $metaParts[] = "{$label}: {$value}";
                }
            }
        }
        $csv = "# " . implode(' - ', $metaParts) . "\n";
        $csv .= "ID,URL,Kategori,Sinif,Metod,Dosya,Servis,Aciklama,HTTP_Metodlar,Token_Gerekli\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            foreach ($endpoints as $endpoint) {
                $csv .= '"' . $endpoint['id'] . '",';
                $csv .= '"' . $endpoint['url'] . '",';
                $csv .= '"' . $endpoint['kategori'] . '",';
                $csv .= '"' . $endpoint['sinif'] . '",';
                $csv .= '"' . $endpoint['metod'] . '",';
                $csv .= '"' . $endpoint['dosya'] . '",';
                $csv .= '"' . ($endpoint['isim'] ?: '') . '",';

                $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'] ?: '';
                $csv .= '"' . str_replace('"', '""', $aciklama) . '",';

                // HTTP Metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                $csv .= '"' . implode('|', $metodlar) . '",';

                // Token
                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $csv .= '"' . ($tokenGerekli ? 'Evet' : 'Hayir') . '"';

                $csv .= "\n";
            }
        }

        return $csv;
    }

    /**
     * cURL komutları formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function cURL($dokuman)
    {
        $curl = "#!/bin/bash\n";
        $curl .= "# {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $curl .= "# {$label}: {$value}\n";
            }
        }
        $curl .= "\n";

        $curl .= "BASE_URL=\"{$dokuman['base_url']}\"\n";
        $curl .= "TOKEN=\"your_token_here\"\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $curl .= "# " . str_repeat('=', 50) . "\n";
            $curl .= "# Kategori: " . ucfirst($kategori) . "\n";
            $curl .= "# " . str_repeat('=', 50) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $curl .= "# {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $curl .= "# {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $curl .= "# İzinli Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $curl .= "# Yetkilendirme Gerekli: " . ($tokenGerekli ? 'Evet' : 'Hayır') . "\n";

                $curl .= "curl -X POST \"\$BASE_URL/api\" \\\n";
                $curl .= "  -H \"Content-Type: application/json\" \\\n";

                if ($tokenGerekli) {
                    $curl .= "  -H \"Authorization: Bearer \$TOKEN\" \\\n";
                }

                $curl .= "  -d '{\n";
                $curl .= "    \"id\": \"{$endpoint['id']}\",\n";
                $curl .= "    \"istekler\": {\n";
                $curl .= "      \"param1\": \"value1\"\n";
                $curl .= "    }\n";
                $curl .= "  }'\n\n";
            }
        }

        return $curl;
    }

    /**
     * HTTPie komutları formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function HTTPie($dokuman)
    {
        $http = "# {$dokuman['baslik']}\n";
        $http .= "# HTTPie Commands\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $http .= "# {$label}: {$value}\n";
            }
        }
        $http .= "\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $http .= "# " . str_repeat('=', 50) . "\n";
            $http .= "# Kategori: " . ucfirst($kategori) . "\n";
            $http .= "# " . str_repeat('=', 50) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $http .= "# {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $http .= "# {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $http .= "# İzinli Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $http .= "# Yetkilendirme Gerekli: " . ($tokenGerekli ? 'Evet' : 'Hayır') . "\n";

                $http .= "http POST {$dokuman['base_url']}/api \\\n";
                $http .= "  Content-Type:application/json \\\n";

                if ($tokenGerekli) {
                    $http .= "  Authorization:\"Bearer YOUR_TOKEN\" \\\n";
                }

                $http .= "  id={$endpoint['id']} \\\n";
                $http .= "  istekler:='{\"param1\":\"value1\"}'\n\n";
            }
        }

        return $http;
    }

    /**
     * PHP kodu formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function PHP($dokuman)
    {
        $php = "<?php\n";
        $php .= "/**\n";
        $php .= " * {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $php .= " * {$label}: {$value}\n";
            }
        }
        $php .= " */\n\n";

        $php .= "class ApiClient\n";
        $php .= "{\n";
        $php .= "    private \$baseUrl = '{$dokuman['base_url']}';\n";
        $php .= "    private \$token = null;\n\n";

        $php .= "    public function __construct(\$token = null)\n";
        $php .= "    {\n";
        $php .= "        \$this->token = \$token;\n";
        $php .= "    }\n\n";

        $php .= "    private function request(\$endpoint, \$params = [])\n";
        $php .= "    {\n";
        $php .= "        \$data = [\n";
        $php .= "            'id' => \$endpoint,\n";
        $php .= "            'istekler' => \$params\n";
        $php .= "        ];\n\n";

        $php .= "        \$headers = ['Content-Type: application/json'];\n";
        $php .= "        if (\$this->token) {\n";
        $php .= "            \$headers[] = 'Authorization: Bearer ' . \$this->token;\n";
        $php .= "        }\n\n";

        $php .= "        \$ch = curl_init(\$this->baseUrl . '/api');\n";
        $php .= "        curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);\n";
        $php .= "        curl_setopt(\$ch, CURLOPT_POST, true);\n";
        $php .= "        curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode(\$data));\n";
        $php .= "        curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n\n";

        $php .= "        \$response = curl_exec(\$ch);\n";
        $php .= "        curl_close(\$ch);\n\n";

        $php .= "        return json_decode(\$response, true);\n";
        $php .= "    }\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $php .= "    // " . str_repeat('-', 50) . "\n";
            $php .= "    // Kategori: " . ucfirst($kategori) . "\n";
            $php .= "    // " . str_repeat('-', 50) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['_', '-'], '', lcfirst(ucwords($endpoint['id'], '_-')));

                $php .= "    /**\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $php .= "     * {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $php .= "     * @method " . implode('|', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $php .= "     * @token " . ($tokenGerekli ? 'required' : 'optional') . "\n";

                $php .= "     * @param array \$params\n";
                $php .= "     * @return array\n";
                $php .= "     */\n";

                $php .= "    public function {$methodName}(\$params = [])\n";
                $php .= "    {\n";
                $php .= "        return \$this->request('{$endpoint['id']}', \$params);\n";
                $php .= "    }\n\n";
            }
        }

        $php .= "}\n\n";
        $php .= "// Kullanım örneği:\n";
        $php .= "// \$client = new ApiClient('your_token_here');\n";
        $php .= "// \$result = \$client->methodName(['param' => 'value']);\n";

        return $php;
    }

    /**
     * JavaScript kodu formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function JavaScript($dokuman)
    {
        $js = "/**\n";
        $js .= " * {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $js .= " * {$label}: {$value}\n";
            }
        }
        $js .= " */\n\n";

        $js .= "class ApiClient {\n";
        $js .= "    constructor(token = null) {\n";
        $js .= "        this.baseUrl = '{$dokuman['base_url']}';\n";
        $js .= "        this.token = token;\n";
        $js .= "    }\n\n";

        $js .= "    async request(endpoint, params = {}) {\n";
        $js .= "        const headers = {\n";
        $js .= "            'Content-Type': 'application/json'\n";
        $js .= "        };\n\n";

        $js .= "        if (this.token) {\n";
        $js .= "            headers['Authorization'] = `Bearer \${this.token}`;\n";
        $js .= "        }\n\n";

        $js .= "        const response = await fetch(`\${this.baseUrl}/api`, {\n";
        $js .= "            method: 'POST',\n";
        $js .= "            headers: headers,\n";
        $js .= "            body: JSON.stringify({\n";
        $js .= "                id: endpoint,\n";
        $js .= "                istekler: params\n";
        $js .= "            })\n";
        $js .= "        });\n\n";

        $js .= "        return await response.json();\n";
        $js .= "    }\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $js .= "    // " . str_repeat('-', 50) . "\n";
            $js .= "    // Kategori: " . ucfirst($kategori) . "\n";
            $js .= "    // " . str_repeat('-', 50) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['_', '-'], '', lcfirst(ucwords($endpoint['id'], '_-')));

                $js .= "    /**\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $js .= "     * {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $js .= "     * @method " . implode('|', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $js .= "     * @token " . ($tokenGerekli ? 'required' : 'optional') . "\n";

                $js .= "     * @param {Object} params\n";
                $js .= "     * @returns {Promise<Object>}\n";
                $js .= "     */\n";

                $js .= "    async {$methodName}(params = {}) {\n";
                $js .= "        return await this.request('{$endpoint['id']}', params);\n";
                $js .= "    }\n\n";
            }
        }

        $js .= "}\n\n";
        $js .= "// Kullanım örneği:\n";
        $js .= "// const client = new ApiClient('your_token_here');\n";
        $js .= "// const result = await client.methodName({param: 'value'});\n";

        return $js;
    }

    /**
     * Python kodu formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function Python($dokuman)
    {
        $py = "\"\"\"!\n";
        $py .= "{$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $py .= "{$label}: {$value}\n";
            }
        }
        $py .= "\"\"\"\n\n";

        $py .= "import requests\n";
        $py .= "from typing import Dict, Optional\n\n\n";

        $py .= "class ApiClient:\n";
        $py .= "    def __init__(self, token: Optional[str] = None):\n";
        $py .= "        self.base_url = '{$dokuman['base_url']}'\n";
        $py .= "        self.token = token\n\n";

        $py .= "    def request(self, endpoint: str, params: Dict = None) -> Dict:\n";
        $py .= "        \"\"\"API isteği gönder\"\"\"\n";
        $py .= "        if params is None:\n";
        $py .= "            params = {}\n\n";

        $py .= "        headers = {'Content-Type': 'application/json'}\n";
        $py .= "        if self.token:\n";
        $py .= "            headers['Authorization'] = f'Bearer {self.token}'\n\n";

        $py .= "        data = {\n";
        $py .= "            'id': endpoint,\n";
        $py .= "            'istekler': params\n";
        $py .= "        }\n\n";

        $py .= "        response = requests.post(\n";
        $py .= "            f'{self.base_url}/api',\n";
        $py .= "            json=data,\n";
        $py .= "            headers=headers\n";
        $py .= "        )\n\n";

        $py .= "        return response.json()\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $py .= "    # " . str_repeat('-', 50) . "\n";
            $py .= "    # Kategori: " . ucfirst($kategori) . "\n";
            $py .= "    # " . str_repeat('-', 50) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['-'], '_', strtolower($endpoint['id']));

                $py .= "    def {$methodName}(self, params: Dict = None) -> Dict:\n";
                $py .= "        \"\"\"!\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $py .= "        {$aciklama}\n\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $py .= "        Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $py .= "        Token: " . ($tokenGerekli ? 'Gerekli' : 'Opsiyonel') . "\n\n";

                $py .= "        Args:\n";
                $py .= "            params: İstek parametreleri\n\n";
                $py .= "        Returns:\n";
                $py .= "            API yanıtı\n";
                $py .= "        \"\"\"\n";
                $py .= "        return self.request('{$endpoint['id']}', params)\n\n";
            }
        }

        $py .= "\n# Kullanım örneği:\n";
        $py .= "# client = ApiClient('your_token_here')\n";
        $py .= "# result = client.method_name({'param': 'value'})\n";

        return $py;
    }

    /**
     * Bash script formatında API dokümantasyonu
     * @param array $dokuman
     * @return string
     */
    private static function Bash($dokuman)
    {
        $bash = "#!/bin/bash\n";
        $bash .= "# {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $bash .= "# {$label}: {$value}\n";
            }
        }
        $bash .= "\n";

        $bash .= "BASE_URL=\"{$dokuman['base_url']}\"\n";
        $bash .= "TOKEN=\"\${API_TOKEN:-your_token_here}\"\n\n";

        $bash .= "# API istek fonksiyonu\n";
        $bash .= "api_request() {\n";
        $bash .= "    local endpoint=\"\$1\"\n";
        $bash .= "    local params=\"\$2\"\n";
        $bash .= "    local use_token=\"\${3:-true}\"\n\n";

        $bash .= "    local headers=\"-H 'Content-Type: application/json'\"\n";
        $bash .= "    if [ \"\$use_token\" = \"true\" ]; then\n";
        $bash .= "        headers=\"\$headers -H 'Authorization: Bearer \$TOKEN'\"\n";
        $bash .= "    fi\n\n";

        $bash .= "    local data='{\"id\":\"'\$endpoint'\",\"istekler\":'\$params'}'\n\n";

        $bash .= "    eval curl -s -X POST \"\$BASE_URL/api\" \$headers -d \"'\$data'\"\n";
        $bash .= "}\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $bash .= "# " . str_repeat('=', 50) . "\n";
            $bash .= "# Kategori: " . ucfirst($kategori) . "\n";
            $bash .= "# " . str_repeat('=', 50) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $functionName = str_replace(['-', '_'], '_', strtolower($endpoint['id']));

                $bash .= "# {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $bash .= "# {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $bash .= "# İzinli Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $bash .= "# Yetkilendirme Gerekli: " . ($tokenGerekli ? 'Evet' : 'Hayır') . "\n";

                $bash .= "{$functionName}() {\n";
                $bash .= "    local params=\"\${1:-'{}'}\"\n";
                $bash .= "    api_request \"{$endpoint['id']}\" \"\$params\" \"" .
                    ($tokenGerekli ? 'true' : 'false') . "\"\n";
                $bash .= "}\n\n";
            }
        }

        $bash .= "# Kullanım örnekleri:\n";
        $bash .= "# function_name '{\"param1\":\"value1\"}'\n";
        $bash .= "# function_name # Parametresiz çağrı\n";

        return $bash;
    }

    /**
     * Ruby format - Ruby HTTP client sınıfı
     */
    private static function Ruby($dokuman)
    {
        $ruby = "# frozen_string_literal: true\n\n";
        $ruby .= "# API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $ruby .= "# {$label}: {$value}\n";
            }
        }
        $ruby .= "\n";
        $ruby .= "require 'net/http'\n";
        $ruby .= "require 'json'\n";
        $ruby .= "require 'uri'\n\n";

        $ruby .= "class ApiClient\n";
        $ruby .= "  BASE_URL = '{$dokuman['base_url']}'\n";
        $ruby .= "  API_ENDPOINT = \"#{BASE_URL}/api\"\n\n";

        $ruby .= "  def initialize(token = nil)\n";
        $ruby .= "    @token = token\n";
        $ruby .= "  end\n\n";

        $ruby .= "  def request(endpoint, params = {})\n";
        $ruby .= "    uri = URI.parse(API_ENDPOINT)\n";
        $ruby .= "    http = Net::HTTP.new(uri.host, uri.port)\n";
        $ruby .= "    http.use_ssl = (uri.scheme == 'https')\n\n";

        $ruby .= "    request = Net::HTTP::Post.new(uri.request_uri)\n";
        $ruby .= "    request['Content-Type'] = 'application/json'\n";
        $ruby .= "    request['Authorization'] = \"Bearer #{@token}\" if @token\n\n";

        $ruby .= "    body = { endpoint: endpoint }\n";
        $ruby .= "    body.merge!(params) unless params.empty?\n";
        $ruby .= "    request.body = body.to_json\n\n";

        $ruby .= "    response = http.request(request)\n";
        $ruby .= "    JSON.parse(response.body)\n";
        $ruby .= "  rescue StandardError => e\n";
        $ruby .= "    { error: e.message }\n";
        $ruby .= "  end\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $ruby .= "  # " . str_repeat('-', 60) . "\n";
            $ruby .= "  # Kategori: " . ucfirst($kategori) . "\n";
            $ruby .= "  # " . str_repeat('-', 60) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = strtolower(str_replace(['-', '_'], '_', $endpoint['id']));

                $ruby .= "  # {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $ruby .= "  # {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $ruby .= "  # @method " . implode('|', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $ruby .= "  # @token " . ($tokenGerekli ? 'required' : 'optional') . "\n";

                $ruby .= "  # @param params [Hash] İstek parametreleri\n";
                $ruby .= "  # @return [Hash] API yanıtı\n";
                $ruby .= "  def {$methodName}(params = {})\n";
                $ruby .= "    request('{$endpoint['id']}', params)\n";
                $ruby .= "  end\n\n";
            }
        }

        $ruby .= "end\n\n";
        $ruby .= "# Kullanım:\n";
        $ruby .= "# client = ApiClient.new('YOUR_TOKEN')\n";
        $ruby .= "# result = client.method_name(param1: 'value1')\n";
        $ruby .= "# puts result\n";

        return $ruby;
    }

    /**
     * Go format - Go HTTP client paketi
     */
    private static function Go($dokuman)
    {
        $go = "package main\n\n";
        $go .= "// API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $go .= "// {$label}: {$value}\n";
            }
        }
        $go .= "\n";
        $go .= "import (\n";
        $go .= "    \"bytes\"\n";
        $go .= "    \"encoding/json\"\n";
        $go .= "    \"fmt\"\n";
        $go .= "    \"io\"\n";
        $go .= "    \"net/http\"\n";
        $go .= ")\n\n";

        $go .= "const (\n";
        $go .= "    BaseURL     = \"{$dokuman['base_url']}\"\n";
        $go .= "    APIEndpoint = BaseURL + \"/api\"\n";
        $go .= ")\n\n";

        $go .= "type ApiClient struct {\n";
        $go .= "    Token  string\n";
        $go .= "    Client *http.Client\n";
        $go .= "}\n\n";

        $go .= "func NewApiClient(token string) *ApiClient {\n";
        $go .= "    return &ApiClient{\n";
        $go .= "        Token:  token,\n";
        $go .= "        Client: &http.Client{},\n";
        $go .= "    }\n";
        $go .= "}\n\n";

        $go .= "func (c *ApiClient) Request(endpoint string, params map[string]interface{}) (map[string]interface{}, error) {\n";
        $go .= "    if params == nil {\n";
        $go .= "        params = make(map[string]interface{})\n";
        $go .= "    }\n";
        $go .= "    params[\"endpoint\"] = endpoint\n\n";

        $go .= "    jsonData, err := json.Marshal(params)\n";
        $go .= "    if err != nil {\n";
        $go .= "        return nil, err\n";
        $go .= "    }\n\n";

        $go .= "    req, err := http.NewRequest(\"POST\", APIEndpoint, bytes.NewBuffer(jsonData))\n";
        $go .= "    if err != nil {\n";
        $go .= "        return nil, err\n";
        $go .= "    }\n\n";

        $go .= "    req.Header.Set(\"Content-Type\", \"application/json\")\n";
        $go .= "    if c.Token != \"\" {\n";
        $go .= "        req.Header.Set(\"Authorization\", \"Bearer \"+c.Token)\n";
        $go .= "    }\n\n";

        $go .= "    resp, err := c.Client.Do(req)\n";
        $go .= "    if err != nil {\n";
        $go .= "        return nil, err\n";
        $go .= "    }\n";
        $go .= "    defer resp.Body.Close()\n\n";

        $go .= "    body, err := io.ReadAll(resp.Body)\n";
        $go .= "    if err != nil {\n";
        $go .= "        return nil, err\n";
        $go .= "    }\n\n";

        $go .= "    var result map[string]interface{}\n";
        $go .= "    err = json.Unmarshal(body, &result)\n";
        $go .= "    return result, err\n";
        $go .= "}\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $go .= "// " . str_repeat('=', 70) . "\n";
            $go .= "// Kategori: " . ucfirst($kategori) . "\n";
            $go .= "// " . str_repeat('=', 70) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['-', '_'], '', ucwords($endpoint['id'], '-_'));

                $go .= "// {$methodName} - {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $go .= "// {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $go .= "// Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $go .= "// Token: " . ($tokenGerekli ? 'Gerekli' : 'Opsiyonel') . "\n";

                $go .= "func (c *ApiClient) {$methodName}(params map[string]interface{}) (map[string]interface{}, error) {\n";
                $go .= "    return c.Request(\"{$endpoint['id']}\", params)\n";
                $go .= "}\n\n";
            }
        }

        $go .= "func main() {\n";
        $go .= "    client := NewApiClient(\"YOUR_TOKEN\")\n";
        $go .= "    params := map[string]interface{}{\n";
        $go .= "        \"param1\": \"value1\",\n";
        $go .= "    }\n";
        $go .= "    result, err := client.MethodName(params)\n";
        $go .= "    if err != nil {\n";
        $go .= "        fmt.Println(\"Error:\", err)\n";
        $go .= "        return\n";
        $go .= "    }\n";
        $go .= "    fmt.Println(result)\n";
        $go .= "}\n";

        return $go;
    }

    /**
     * Java format - Java HTTP client sınıfı
     */
    private static function Java($dokuman)
    {
        $java = "// API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $java .= "// {$label}: {$value}\n";
            }
        }
        $java .= "\n";
        $java .= "import java.net.URI;\n";
        $java .= "import java.net.http.HttpClient;\n";
        $java .= "import java.net.http.HttpRequest;\n";
        $java .= "import java.net.http.HttpResponse;\n";
        $java .= "import org.json.JSONObject;\n";
        $java .= "import java.time.Duration;\n\n";

        $java .= "public class ApiClient {\n";
        $java .= "    private static final String BASE_URL = \"{$dokuman['base_url']}\";\n";
        $java .= "    private static final String API_ENDPOINT = BASE_URL + \"/api\";\n";
        $java .= "    private final String token;\n";
        $java .= "    private final HttpClient client;\n\n";

        $java .= "    public ApiClient(String token) {\n";
        $java .= "        this.token = token;\n";
        $java .= "        this.client = HttpClient.newBuilder()\n";
        $java .= "            .connectTimeout(Duration.ofSeconds(30))\n";
        $java .= "            .build();\n";
        $java .= "    }\n\n";

        $java .= "    private JSONObject request(String endpoint, JSONObject params) throws Exception {\n";
        $java .= "        if (params == null) {\n";
        $java .= "            params = new JSONObject();\n";
        $java .= "        }\n";
        $java .= "        params.put(\"endpoint\", endpoint);\n\n";

        $java .= "        HttpRequest.Builder builder = HttpRequest.newBuilder()\n";
        $java .= "            .uri(URI.create(API_ENDPOINT))\n";
        $java .= "            .header(\"Content-Type\", \"application/json\")\n";
        $java .= "            .POST(HttpRequest.BodyPublishers.ofString(params.toString()));\n\n";

        $java .= "        if (token != null && !token.isEmpty()) {\n";
        $java .= "            builder.header(\"Authorization\", \"Bearer \" + token);\n";
        $java .= "        }\n\n";

        $java .= "        HttpRequest request = builder.build();\n";
        $java .= "        HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());\n";
        $java .= "        return new JSONObject(response.body());\n";
        $java .= "    }\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $java .= "    // " . str_repeat('=', 60) . "\n";
            $java .= "    // Kategori: " . ucfirst($kategori) . "\n";
            $java .= "    // " . str_repeat('=', 60) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = lcfirst(str_replace(['-', '_'], '', ucwords($endpoint['id'], '-_')));

                $java .= "    /**\n";
                $java .= "     * {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $java .= "     * {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $java .= "     * @method " . implode('|', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $java .= "     * @token " . ($tokenGerekli ? 'required' : 'optional') . "\n";

                $java .= "     * @param params Request parametreleri\n";
                $java .= "     * @return API yanıtı\n";
                $java .= "     */\n";
                $java .= "    public JSONObject {$methodName}(JSONObject params) throws Exception {\n";
                $java .= "        return request(\"{$endpoint['id']}\", params);\n";
                $java .= "    }\n\n";
            }
        }

        $java .= "    public static void main(String[] args) {\n";
        $java .= "        try {\n";
        $java .= "            ApiClient client = new ApiClient(\"YOUR_TOKEN\");\n";
        $java .= "            JSONObject params = new JSONObject();\n";
        $java .= "            params.put(\"param1\", \"value1\");\n";
        $java .= "            JSONObject result = client.methodName(params);\n";
        $java .= "            System.out.println(result.toString(2));\n";
        $java .= "        } catch (Exception e) {\n";
        $java .= "            e.printStackTrace();\n";
        $java .= "        }\n";
        $java .= "    }\n";
        $java .= "}\n";

        return $java;
    }

    /**
     * C# format - C# HttpClient sınıfı
     */
    private static function CSharp($dokuman)
    {
        $cs = "// API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $cs .= "// {$label}: {$value}\n";
            }
        }
        $cs .= "\n";
        $cs .= "using System;\n";
        $cs .= "using System.Net.Http;\n";
        $cs .= "using System.Text;\n";
        $cs .= "using System.Text.Json;\n";
        $cs .= "using System.Threading.Tasks;\n";
        $cs .= "using System.Collections.Generic;\n\n";

        $cs .= "namespace ApiClient\n";
        $cs .= "{\n";
        $cs .= "    public class ApiClient\n";
        $cs .= "    {\n";
        $cs .= "        private const string BASE_URL = \"{$dokuman['base_url']}\";\n";
        $cs .= "        private const string API_ENDPOINT = BASE_URL + \"/api\";\n";
        $cs .= "        private readonly HttpClient _client;\n";
        $cs .= "        private readonly string _token;\n\n";

        $cs .= "        public ApiClient(string token = null)\n";
        $cs .= "        {\n";
        $cs .= "            _token = token;\n";
        $cs .= "            _client = new HttpClient();\n";
        $cs .= "            _client.Timeout = TimeSpan.FromSeconds(30);\n";
        $cs .= "        }\n\n";

        $cs .= "        private async Task<Dictionary<string, object>> Request(string endpoint, Dictionary<string, object> parameters = null)\n";
        $cs .= "        {\n";
        $cs .= "            if (parameters == null)\n";
        $cs .= "                parameters = new Dictionary<string, object>();\n\n";

        $cs .= "            parameters[\"endpoint\"] = endpoint;\n\n";

        $cs .= "            var json = JsonSerializer.Serialize(parameters);\n";
        $cs .= "            var content = new StringContent(json, Encoding.UTF8, \"application/json\");\n\n";

        $cs .= "            var request = new HttpRequestMessage(HttpMethod.Post, API_ENDPOINT)\n";
        $cs .= "            {\n";
        $cs .= "                Content = content\n";
        $cs .= "            };\n\n";

        $cs .= "            if (!string.IsNullOrEmpty(_token))\n";
        $cs .= "            {\n";
        $cs .= "                request.Headers.Add(\"Authorization\", $\"Bearer {_token}\");\n";
        $cs .= "            }\n\n";

        $cs .= "            var response = await _client.SendAsync(request);\n";
        $cs .= "            var responseContent = await response.Content.ReadAsStringAsync();\n";
        $cs .= "            return JsonSerializer.Deserialize<Dictionary<string, object>>(responseContent);\n";
        $cs .= "        }\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $cs .= "        // " . str_repeat('=', 60) . "\n";
            $cs .= "        // Kategori: " . ucfirst($kategori) . "\n";
            $cs .= "        // " . str_repeat('=', 60) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['-', '_'], '', ucwords($endpoint['id'], '-_'));

                $cs .= "        /// <summary>\n";
                $cs .= "        /// {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $cs .= "        /// {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $cs .= "        /// Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $cs .= "        /// Token: " . ($tokenGerekli ? 'Gerekli' : 'Opsiyonel') . "\n";

                $cs .= "        /// </summary>\n";
                $cs .= "        /// <param name=\"parameters\">İstek parametreleri</param>\n";
                $cs .= "        /// <returns>API yanıtı</returns>\n";
                $cs .= "        public async Task<Dictionary<string, object>> {$methodName}Async(Dictionary<string, object> parameters = null)\n";
                $cs .= "        {\n";
                $cs .= "            return await Request(\"{$endpoint['id']}\", parameters);\n";
                $cs .= "        }\n\n";
            }
        }

        $cs .= "        public static async Task Main(string[] args)\n";
        $cs .= "        {\n";
        $cs .= "            var client = new ApiClient(\"YOUR_TOKEN\");\n";
        $cs .= "            var parameters = new Dictionary<string, object>\n";
        $cs .= "            {\n";
        $cs .= "                { \"param1\", \"value1\" }\n";
        $cs .= "            };\n";
        $cs .= "            var result = await client.MethodNameAsync(parameters);\n";
        $cs .= "            Console.WriteLine(JsonSerializer.Serialize(result, new JsonSerializerOptions { WriteIndented = true }));\n";
        $cs .= "        }\n";
        $cs .= "    }\n";
        $cs .= "}\n";

        return $cs;
    }

    /**
     * TypeScript format - TypeScript/Axios client sınıfı
     */
    private static function TypeScript($dokuman)
    {
        $ts = "// API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $ts .= "// {$label}: {$value}\n";
            }
        }
        $ts .= "\n";
        $ts .= "import axios, { AxiosInstance, AxiosResponse } from 'axios';\n\n";

        $ts .= "interface ApiResponse {\n";
        $ts .= "    [key: string]: any;\n";
        $ts .= "}\n\n";

        $ts .= "interface RequestParams {\n";
        $ts .= "    [key: string]: any;\n";
        $ts .= "}\n\n";

        $ts .= "class ApiClient {\n";
        $ts .= "    private static readonly BASE_URL: string = '{$dokuman['base_url']}';\n";
        $ts .= "    private static readonly API_ENDPOINT: string = `\${ApiClient.BASE_URL}/api`;\n";
        $ts .= "    private readonly client: AxiosInstance;\n";
        $ts .= "    private readonly token: string | null;\n\n";

        $ts .= "    constructor(token: string | null = null) {\n";
        $ts .= "        this.token = token;\n";
        $ts .= "        this.client = axios.create({\n";
        $ts .= "            baseURL: ApiClient.BASE_URL,\n";
        $ts .= "            timeout: 30000,\n";
        $ts .= "            headers: {\n";
        $ts .= "                'Content-Type': 'application/json'\n";
        $ts .= "            }\n";
        $ts .= "        });\n\n";

        $ts .= "        if (this.token) {\n";
        $ts .= "            this.client.defaults.headers.common['Authorization'] = `Bearer \${this.token}`;\n";
        $ts .= "        }\n";
        $ts .= "    }\n\n";

        $ts .= "    private async request(endpoint: string, params: RequestParams = {}): Promise<ApiResponse> {\n";
        $ts .= "        try {\n";
        $ts .= "            const response: AxiosResponse<ApiResponse> = await this.client.post(ApiClient.API_ENDPOINT, {\n";
        $ts .= "                endpoint,\n";
        $ts .= "                ...params\n";
        $ts .= "            });\n";
        $ts .= "            return response.data;\n";
        $ts .= "        } catch (error: any) {\n";
        $ts .= "            throw new Error(error.response?.data?.message || error.message);\n";
        $ts .= "        }\n";
        $ts .= "    }\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $ts .= "    // " . str_repeat('=', 70) . "\n";
            $ts .= "    // Kategori: " . ucfirst($kategori) . "\n";
            $ts .= "    // " . str_repeat('=', 70) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['_', '-'], '', lcfirst(ucwords($endpoint['id'], '_-')));

                $ts .= "    /**\n";
                $ts .= "     * {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $ts .= "     * {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $ts .= "     * @method " . implode('|', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $ts .= "     * @token " . ($tokenGerekli ? 'required' : 'optional') . "\n";

                $ts .= "     * @param params - Request parametreleri\n";
                $ts .= "     * @returns API yanıtı\n";
                $ts .= "     */\n";
                $ts .= "    async {$methodName}(params: RequestParams = {}): Promise<ApiResponse> {\n";
                $ts .= "        return await this.request('{$endpoint['id']}', params);\n";
                $ts .= "    }\n\n";
            }
        }

        $ts .= "}\n\n";
        $ts .= "// Kullanım:\n";
        $ts .= "// const client = new ApiClient('YOUR_TOKEN');\n";
        $ts .= "// const result = await client.methodName({ param1: 'value1' });\n";
        $ts .= "// console.log(result);\n\n";
        $ts .= "export default ApiClient;\n";

        return $ts;
    }

    /**
     * PowerShell format - PowerShell scriptleri
     */
    private static function PowerShell($dokuman)
    {
        $ps = "# API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $ps .= "# {$label}: {$value}\n";
            }
        }
        $ps .= "\n";

        $ps .= "\$BaseUrl = '{$dokuman['base_url']}'\n";
        $ps .= "\$ApiEndpoint = \"\$BaseUrl/api\"\n";
        $ps .= "\$Token = 'YOUR_TOKEN_HERE'\n\n";

        $ps .= "function Invoke-ApiRequest {\n";
        $ps .= "    param(\n";
        $ps .= "        [Parameter(Mandatory=\$true)]\n";
        $ps .= "        [string]\$Endpoint,\n\n";

        $ps .= "        [Parameter(Mandatory=\$false)]\n";
        $ps .= "        [hashtable]\$Params = @{},\n\n";

        $ps .= "        [Parameter(Mandatory=\$false)]\n";
        $ps .= "        [bool]\$UseToken = \$true\n";
        $ps .= "    )\n\n";

        $ps .= "    \$body = @{ endpoint = \$Endpoint }\n";
        $ps .= "    foreach (\$key in \$Params.Keys) {\n";
        $ps .= "        \$body[\$key] = \$Params[\$key]\n";
        $ps .= "    }\n\n";

        $ps .= "    \$headers = @{\n";
        $ps .= "        'Content-Type' = 'application/json'\n";
        $ps .= "    }\n\n";

        $ps .= "    if (\$UseToken -and \$Token) {\n";
        $ps .= "        \$headers['Authorization'] = \"Bearer \$Token\"\n";
        $ps .= "    }\n\n";

        $ps .= "    try {\n";
        $ps .= "        \$response = Invoke-RestMethod -Uri \$ApiEndpoint -Method Post -Headers \$headers -Body (\$body | ConvertTo-Json) -ErrorAction Stop\n";
        $ps .= "        return \$response\n";
        $ps .= "    }\n";
        $ps .= "    catch {\n";
        $ps .= "        Write-Error \"API Error: \$_\"\n";
        $ps .= "        return \$null\n";
        $ps .= "    }\n";
        $ps .= "}\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $ps .= "# " . str_repeat('=', 70) . "\n";
            $ps .= "# Kategori: " . ucfirst($kategori) . "\n";
            $ps .= "# " . str_repeat('=', 70) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $functionName = str_replace(['-', '_'], '', ucwords($endpoint['id'], '-_'));

                $ps .= "# {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $ps .= "# {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $ps .= "# İzinli Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $ps .= "# Yetkilendirme Gerekli: " . ($tokenGerekli ? 'Evet' : 'Hayır') . "\n";

                $ps .= "function Invoke-{$functionName} {\n";
                $ps .= "    param([hashtable]\$Params = @{})\n";
                $ps .= "    return Invoke-ApiRequest -Endpoint '{$endpoint['id']}' -Params \$Params -UseToken \$" . ($tokenGerekli ? 'true' : 'false') . "\n";
                $ps .= "}\n\n";
            }
        }

        $ps .= "# Kullanım örneği:\n";
        $ps .= "# \$result = Invoke-FunctionName -Params @{ param1 = 'value1' }\n";
        $ps .= "# \$result | ConvertTo-Json -Depth 10\n";

        return $ps;
    }

    /**
     * Wget format - Wget komutları
     */
    private static function Wget($dokuman)
    {
        $wget = "#!/bin/bash\n";
        $wget .= "# API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $wget .= "# {$label}: {$value}\n";
            }
        }
        $wget .= "\n";

        $wget .= "BASE_URL=\"{$dokuman['base_url']}\"\n";
        $wget .= "API_ENDPOINT=\"\$BASE_URL/api\"\n";
        $wget .= "TOKEN=\"YOUR_TOKEN_HERE\"\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $wget .= "# " . str_repeat('=', 70) . "\n";
            $wget .= "# Kategori: " . ucfirst($kategori) . "\n";
            $wget .= "# " . str_repeat('=', 70) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $wget .= "# {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $wget .= "# {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $wget .= "# İzinli Metodlar: " . implode(', ', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $wget .= "# Yetkilendirme Gerekli: " . ($tokenGerekli ? 'Evet' : 'Hayır') . "\n";

                $wget .= "wget --quiet \\\n";
                $wget .= "  --method=POST \\\n";
                $wget .= "  --header=\"Content-Type: application/json\" \\\n";

                if ($tokenGerekli) {
                    $wget .= "  --header=\"Authorization: Bearer \$TOKEN\" \\\n";
                }

                $wget .= "  --body-data='{\"endpoint\":\"{$endpoint['id']}\",\"param1\":\"value1\"}' \\\n";
                $wget .= "  --output-document=- \\\n";
                $wget .= "  \"\$API_ENDPOINT\"\n\n";
            }
        }

        $wget .= "# Not: --quiet sessiz mod, --output-document=- stdout'a yazar\n";

        return $wget;
    }

    /**
     * Axios format - JavaScript Axios kütüphanesi
     */
    private static function Axios($dokuman)
    {
        $axios = "// API Client - {$dokuman['baslik']}\n";

        // Metadata yorumlarını otomatik oluştur
        $labels = [
            'versiyon' => 'Versiyon',
            'sahibi' => 'Sahibi',
            'son_guncelleme' => 'Son Güncelleme',
            'base_url' => 'Base URL',
            'toplam_endpoint' => 'Toplam Endpoint'
        ];

        foreach ($dokuman as $key => $value) {
            if ($key !== 'baslik' && $key !== 'endpoints' && !is_array($value)) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $axios .= "// {$label}: {$value}\n";
            }
        }
        $axios .= "\n";
        $axios .= "const axios = require('axios');\n\n";

        $axios .= "class ApiClient {\n";
        $axios .= "    constructor(token = null) {\n";
        $axios .= "        this.baseURL = '{$dokuman['base_url']}';\n";
        $axios .= "        this.apiEndpoint = `\${this.baseURL}/api`;\n";
        $axios .= "        this.token = token;\n\n";

        $axios .= "        this.client = axios.create({\n";
        $axios .= "            baseURL: this.baseURL,\n";
        $axios .= "            timeout: 30000,\n";
        $axios .= "            headers: {\n";
        $axios .= "                'Content-Type': 'application/json'\n";
        $axios .= "            }\n";
        $axios .= "        });\n\n";

        $axios .= "        if (this.token) {\n";
        $axios .= "            this.client.defaults.headers.common['Authorization'] = `Bearer \${this.token}`;\n";
        $axios .= "        }\n";
        $axios .= "    }\n\n";

        $axios .= "    async request(endpoint, params = {}) {\n";
        $axios .= "        try {\n";
        $axios .= "            const response = await this.client.post(this.apiEndpoint, {\n";
        $axios .= "                endpoint,\n";
        $axios .= "                ...params\n";
        $axios .= "            });\n";
        $axios .= "            return response.data;\n";
        $axios .= "        } catch (error) {\n";
        $axios .= "            throw new Error(error.response?.data?.message || error.message);\n";
        $axios .= "        }\n";
        $axios .= "    }\n\n";

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $axios .= "    // " . str_repeat('=', 70) . "\n";
            $axios .= "    // Kategori: " . ucfirst($kategori) . "\n";
            $axios .= "    // " . str_repeat('=', 70) . "\n\n";

            foreach ($endpoints as $endpoint) {
                $methodName = str_replace(['_', '-'], '', lcfirst(ucwords($endpoint['id'], '_-')));

                $axios .= "    /**\n";
                $axios .= "     * {$endpoint['url']}\n";
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $aciklama = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                    $axios .= "     * {$aciklama}\n";
                }

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                if (!empty($metodlar)) {
                    $axios .= "     * @method " . implode('|', $metodlar) . "\n";
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;
                $axios .= "     * @token " . ($tokenGerekli ? 'required' : 'optional') . "\n";

                $axios .= "     * @param {Object} params - Request parametreleri\n";
                $axios .= "     * @returns {Promise<Object>} API yanıtı\n";
                $axios .= "     */\n";
                $axios .= "    async {$methodName}(params = {}) {\n";
                $axios .= "        return await this.request('{$endpoint['id']}', params);\n";
                $axios .= "    }\n\n";
            }
        }

        $axios .= "}\n\n";
        $axios .= "// Kullanım:\n";
        $axios .= "// const client = new ApiClient('YOUR_TOKEN');\n";
        $axios .= "// client.methodName({ param1: 'value1' })\n";
        $axios .= "//     .then(result => console.log(result))\n";
        $axios .= "//     .catch(error => console.error(error));\n\n";
        $axios .= "module.exports = ApiClient;\n";

        return $axios;
    }

    /**
     * Insomnia format - Insomnia REST client export
     */
    private static function Insomnia($dokuman)
    {
        $insomnia = [
            '_type' => 'export',
            '__export_format' => 4,
            '__export_date' => date('Y-m-d\TH:i:s.000\Z'),
            '__export_source' => 'api_dokuman',
            'resources' => []
        ];

        // Workspace description'ı otomatik oluştur
        $descParts = [];
        foreach ($dokuman as $key => $value) {
            if (!in_array($key, ['baslik', 'base_url', 'endpoints']) && !is_array($value)) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $descParts[] = "{$label}: {$value}";
            }
        }

        // Workspace
        $insomnia['resources'][] = [
            '_id' => 'wrk_' . md5($dokuman['base_url']),
            'parentId' => null,
            'modified' => time() * 1000,
            'created' => time() * 1000,
            'name' => $dokuman['baslik'],
            'description' => implode(' | ', $descParts),
            'scope' => 'collection',
            '_type' => 'workspace'
        ];

        // Base environment
        $insomnia['resources'][] = [
            '_id' => 'env_' . md5($dokuman['base_url']),
            'parentId' => 'wrk_' . md5($dokuman['base_url']),
            'modified' => time() * 1000,
            'created' => time() * 1000,
            'name' => 'Base Environment',
            'data' => [
                'base_url' => $dokuman['base_url'],
                'token' => 'YOUR_TOKEN_HERE'
            ],
            'dataPropertyOrder' => ['base_url', 'token'],
            'color' => null,
            'isPrivate' => false,
            'metaSortKey' => 1,
            '_type' => 'environment'
        ];

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            // Kategori için folder
            $folderId = 'fld_' . md5($kategori);
            $insomnia['resources'][] = [
                '_id' => $folderId,
                'parentId' => 'wrk_' . md5($dokuman['base_url']),
                'modified' => time() * 1000,
                'created' => time() * 1000,
                'name' => ucfirst($kategori),
                'description' => '',
                'environment' => new stdClass(),
                'environmentPropertyOrder' => null,
                'metaSortKey' => -time(),
                '_type' => 'request_group'
            ];

            foreach ($endpoints as $endpoint) {
                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;

                // HTTP metodları
                $metodlar = [];
                if (!empty($endpoint['ozellikler'])) {
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $metodlar[] = $method;
                        }
                    }
                }
                $allowedMethods = !empty($metodlar) ? implode(', ', $metodlar) : 'POST';

                $description = '';
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $description = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                }
                $description .= "\n\nİzinli Metodlar: {$allowedMethods}";
                $description .= "\nYetkilendirme Gerekli: " . ($tokenGerekli ? 'Evet' : 'Hayır');

                $insomnia['resources'][] = [
                    '_id' => 'req_' . md5($endpoint['id']),
                    'parentId' => $folderId,
                    'modified' => time() * 1000,
                    'created' => time() * 1000,
                    'url' => '{{ _.base_url }}/api',
                    'name' => $endpoint['url'],
                    'description' => trim($description),
                    'method' => 'POST',
                    'body' => [
                        'mimeType' => 'application/json',
                        'text' => json_encode([
                            'endpoint' => $endpoint['id'],
                            'param1' => 'value1'
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    ],
                    'parameters' => [],
                    'headers' => [
                        [
                            'name' => 'Content-Type',
                            'value' => 'application/json'
                        ]
                    ],
                    'authentication' => $tokenGerekli ? [
                        'type' => 'bearer',
                        'token' => '{{ _.token }}',
                        'prefix' => 'Bearer'
                    ] : ['type' => 'none'],
                    'metaSortKey' => -time(),
                    'isPrivate' => false,
                    'settingStoreCookies' => true,
                    'settingSendCookies' => true,
                    'settingDisableRenderRequestBody' => false,
                    'settingEncodeUrl' => true,
                    'settingRebuildPath' => true,
                    'settingFollowRedirects' => 'global',
                    '_type' => 'request'
                ];
            }
        }

        return json_encode($insomnia, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Swagger 2.0 format
     */
    private static function Swagger2($dokuman)
    {
        // Info description'ı otomatik oluştur
        $descParts = [];
        foreach ($dokuman as $key => $value) {
            if (!in_array($key, ['baslik', 'versiyon', 'base_url', 'endpoints']) && !is_array($value)) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $descParts[] = "{$label}: {$value}";
            }
        }

        $swagger = [
            'swagger' => '2.0',
            'info' => [
                'title' => $dokuman['baslik'],
                'version' => $dokuman['versiyon'] ?? '1.0.0',
                'description' => implode("\n", $descParts)
            ],
            'host' => parse_url($dokuman['base_url'], PHP_URL_HOST),
            'basePath' => parse_url($dokuman['base_url'], PHP_URL_PATH) ?: '/',
            'schemes' => [
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http'
            ],
            'consumes' => ['application/json'],
            'produces' => ['application/json'],
            'securityDefinitions' => [
                'Bearer' => [
                    'type' => 'apiKey',
                    'name' => 'Authorization',
                    'in' => 'header',
                    'description' => 'Bearer token authorization. Örnek: Bearer YOUR_TOKEN'
                ]
            ],
            'paths' => [],
            'definitions' => [
                'ApiRequest' => [
                    'type' => 'object',
                    'required' => ['endpoint'],
                    'properties' => [
                        'endpoint' => [
                            'type' => 'string',
                            'description' => 'API endpoint adı'
                        ]
                    ]
                ],
                'ApiResponse' => [
                    'type' => 'object',
                    'properties' => [
                        'success' => [
                            'type' => 'boolean'
                        ],
                        'data' => [
                            'type' => 'object'
                        ],
                        'message' => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ],
            'tags' => []
        ];

        // Kategorileri tag olarak ekle
        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            $swagger['tags'][] = [
                'name' => ucfirst($kategori),
                'description' => ucfirst($kategori) . ' endpoints'
            ];
        }

        // API endpoint'i
        $swagger['paths']['/api'] = [];

        foreach ($dokuman['endpoints'] as $kategori => $endpoints) {
            foreach ($endpoints as $endpoint) {
                // HTTP metodları
                $metodlar = ['post']; // Default
                if (!empty($endpoint['ozellikler'])) {
                    $temp = [];
                    foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                        if (isset($endpoint['ozellikler'][$method]) && $endpoint['ozellikler'][$method] === true) {
                            $temp[] = strtolower($method);
                        }
                    }
                    if (!empty($temp)) {
                        $metodlar = $temp;
                    }
                }

                $tokenGerekli = isset($endpoint['ozellikler']['TOKEN']) ?
                    $endpoint['ozellikler']['TOKEN'] : true;

                $description = '';
                if (!empty($endpoint['endpoint_aciklama']) || !empty($endpoint['aciklama'])) {
                    $description = $endpoint['endpoint_aciklama'] ?: $endpoint['aciklama'];
                }

                $metodInfo = "**İzinli Metodlar:** " . implode(', ', array_map('strtoupper', $metodlar)) . "  \n";
                $tokenInfo = "**Token:** " . ($tokenGerekli ? 'Gerekli' : 'Opsiyonel');

                $operation = [
                    'tags' => [ucfirst($kategori)],
                    'summary' => $endpoint['url'],
                    'description' => trim($description . "\n\n" . $metodInfo . $tokenInfo),
                    'operationId' => $endpoint['id'],
                    'parameters' => [
                        [
                            'in' => 'body',
                            'name' => 'body',
                            'required' => true,
                            'schema' => [
                                'allOf' => [
                                    ['$ref' => '#/definitions/ApiRequest'],
                                    [
                                        'type' => 'object',
                                        'properties' => [
                                            'endpoint' => [
                                                'type' => 'string',
                                                'example' => $endpoint['id']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Başarılı',
                            'schema' => [
                                '$ref' => '#/definitions/ApiResponse'
                            ]
                        ],
                        '400' => [
                            'description' => 'Hatalı istek'
                        ],
                        '401' => [
                            'description' => 'Yetkilendirme hatası'
                        ]
                    ]
                ];

                if ($tokenGerekli) {
                    $operation['security'] = [
                        ['Bearer' => []]
                    ];
                }

                // POST metodu her zaman var (API POST ile çalışıyor)
                $swagger['paths']['/api']['post'] = $operation;
            }
        }

        return json_encode($swagger, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
