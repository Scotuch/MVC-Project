<?php
$a = $sistem_hatasi;
$b = $a->getTrace();
$stackLimit = GELISTIRICI ? 50 : 5;
http_response_code(500);
ob_start();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Hatası - Geliştirici: <?php echo (GELISTIRICI ? 'Açık' : 'Kapalı') ?></title>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

        :root {
            --primary: #ef4444;
            --primary-dark: #dc2626;
            --primary-glow: rgba(239, 68, 68, 0.3);
            --bg-main: #1e293b;
            --bg-card: #2d3748;
            --bg-hover: #374151;
            --border: #3f4856;
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.2);
            --success: #10b981;
            --warning: #f59e0b;
        }

        html,
        body {
            margin: 0;
            height: 100%;
            background: var(--bg-main);
        }

        * {
            box-sizing: border-box;
        }

        /* === OVERLAY === */
        main {
            position: fixed;
            inset: 0;
            z-index: 999999999999999999;
            background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(239, 68, 68, 0.1) 0%, transparent 50%),
                linear-gradient(135deg, #1e293b 0%, #334155 100%);
            display: flex;
            justify-content: center;
            overflow: hidden;
            padding: 32px 16px;
            font-family: Inter, system-ui, sans-serif;
            animation: bgPulse 10s ease-in-out infinite;
        }

        @keyframes bgPulse {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        /* === WRAPPER === */
        .wrapper {
            width: 100%;
            max-width: 980px;
            background: rgba(45, 55, 72, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
            max-height: calc(100vh - 64px);
            display: flex;
            flex-direction: column;
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* === HEADER === */
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #991b1b 100%);
            padding: 32px 36px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px var(--primary-glow), inset 0 -1px 0 rgba(255, 255, 255, 0.1);
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r="1" fill="rgba(255,255,255,0.08)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)" /></svg>');
            opacity: 0.6;
            animation: headerFloat 20s linear infinite;
        }

        @keyframes headerFloat {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(60px, 60px);
            }
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .header h1::before {
            content: '⚠';
            font-size: 32px;
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.5));
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .header .error-type {
            margin-top: 14px;
            font-size: 15px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.98);
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            padding: 10px 16px;
            border-radius: 10px;
            border-left: 3px solid rgba(255, 255, 255, 0.6);
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* === BODY (SCROLL) === */
        .body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: var(--bg-main);
        }

        .body::-webkit-scrollbar {
            width: 10px;
        }

        .body::-webkit-scrollbar-track {
            background: var(--bg-card);
        }

        .body::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 5px;
        }

        .body::-webkit-scrollbar-thumb:hover {
            background: var(--bg-hover);
        }

        /* === PANELLER === */
        details {
            background: rgba(45, 55, 72, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            margin-bottom: 14px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        details:hover {
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-1px);
        }

        details[open] {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(59, 130, 246, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            border-color: rgba(59, 130, 246, 0.2);
        }

        details summary {
            list-style: none;
            cursor: pointer;
            padding: 18px 24px 18px 48px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            position: relative;
            user-select: none;
            transition: all 0.2s ease;
            background: linear-gradient(90deg, transparent 0%, rgba(59, 130, 246, 0.05) 50%, transparent 100%);
            background-size: 200% 100%;
            background-position: 0% 0%;
        }

        details summary:hover {
            background-position: 100% 0%;
        }

        details summary::-webkit-details-marker {
            display: none;
        }

        details summary::before {
            content: "▶";
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            font-size: 11px;
            transition: transform 0.25s ease, color 0.2s ease;
        }

        details summary:hover::before {
            color: #60a5fa;
        }

        details[open] summary::before {
            transform: translateY(-50%) rotate(90deg);
        }

        /* === PANEL === */
        .panel {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 24px;
            font-size: 14px;
            color: var(--text-secondary);
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.25) 100%);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* === GRID === */
        dl {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 12px 20px;
        }

        dt {
            text-align: right;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        dd {
            margin: 0;
            color: var(--text-primary);
            word-break: break-all;
            font-family: ui-monospace, monospace;
            font-size: 13px;
            padding: 8px 14px;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.4) 100%);
            border-radius: 8px;
            border-left: 3px solid var(--accent);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(59, 130, 246, 0.1);
            transition: all 0.2s ease;
        }

        dd:hover {
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(59, 130, 246, 0.3), 0 0 12px var(--accent-glow);
        }

        /* === STACK === */
        .stack {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12.5px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .stack-item {
            background: rgba(0, 0, 0, 0.3);
            border-left: 3px solid var(--accent);
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .stack-item:hover {
            background: rgba(0, 0, 0, 0.4);
            border-left-color: var(--primary);
            transform: translateX(2px);
        }

        .stack-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .stack-number {
            background: var(--accent);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            min-width: 32px;
            text-align: center;
        }

        .stack-function {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 13px;
            flex: 1;
        }

        .stack-function .class {
            color: #fbbf24;
        }

        .stack-function .method {
            color: #60a5fa;
        }

        .stack-location {
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stack-location .file {
            color: var(--text-secondary);
            word-break: break-all;
        }

        .stack-location .line {
            color: var(--primary);
            font-weight: 600;
            background: rgba(220, 38, 38, 0.1);
            padding: 2px 6px;
            border-radius: 3px;
        }

        .stack-args {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid var(--border);
            font-size: 11px;
            color: var(--text-muted);
        }

        .stack-args summary {
            padding: 0;
            font-size: 11px;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .stack-args summary::before {
            display: none;
        }

        .stack-args summary:hover {
            background: none;
            color: var(--text-secondary);
        }

        .stack-args pre {
            margin: 6px 0 0 0;
            padding: 8px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 4px;
            overflow-x: auto;
            font-size: 11px;
            color: var(--text-secondary);
        }

        /* İç içe paneller için */
        .panel details summary::before {
            display: none;
        }

        .panel details summary {
            padding-left: 20px;
        }

        .panel details summary:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* === CODE PREVIEW === */
        .code-preview {
            background: #0d1117;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .code-lines {
            font-family: ui-monospace, monospace;
            font-size: 13px;
            line-height: 1.5;
            overflow-x: auto;
        }

        .code-line {
            display: flex;
            padding: 4px 0;
            transition: background 0.1s;
        }

        .code-line:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .code-line.error-line {
            background: rgba(220, 38, 38, 0.15);
            border-left: 3px solid var(--primary);
        }

        .code-line.error-line:hover {
            background: rgba(220, 38, 38, 0.2);
        }

        .line-number {
            color: var(--text-muted);
            text-align: right;
            padding: 0 12px;
            user-select: none;
            min-width: 50px;
            flex-shrink: 0;
        }

        .error-line .line-number {
            color: var(--primary);
            font-weight: 700;
        }

        .line-content {
            color: var(--text-secondary);
            padding-right: 16px;
            white-space: pre;
        }

        .error-line .line-content {
            color: var(--text-primary);
        }

        /* === REQUEST DATA === */
        .data-badge {
            display: inline-block;
            background: var(--accent);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
            padding: 20px;
            font-style: italic;
        }

        /* === ENV GRID === */
        .env-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }

        .env-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 12px;
            border-radius: 6px;
            border-left: 2px solid var(--accent);
        }

        .env-item-label {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .env-item-value {
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 600;
        }

        .env-item-value.success {
            color: #10b981;
        }

        .env-item-value.warning {
            color: #f59e0b;
        }

        .env-item-value.error {
            color: var(--primary);
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            main {
                padding: 16px 12px;
            }

            .wrapper {
                border-radius: 12px;
            }

            .header {
                padding: 20px 20px;
            }

            .header h1 {
                font-size: 20px;
            }

            .header h1::before {
                font-size: 24px;
            }

            .body {
                padding: 16px;
            }

            dl {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            dt {
                text-align: left;
                font-size: 11px;
                margin-bottom: -4px;
            }

            details summary {
                padding: 14px 18px 14px 40px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <main>
        <div class="wrapper">

            <!-- HEADER -->
            <div class="header">
                <h1>Sistemde Hata Meydana Geldi!</h1>
                <div class="error-type"><?= htmlspecialchars($a->getMessage()) ?></div>
            </div>

            <!-- BODY -->
            <div class="body">

                <!-- AÇIKLAMA -->
                <details open>
                    <summary>Açıklama</summary>
                    <div class="panel">
                        <dl>
                            <dt>Hata</dt>
                            <dd><?= htmlspecialchars($a->getMessage()) ?></dd>
                            <dt>Dosya</dt>
                            <dd><?= GELISTIRICI ? htmlspecialchars($a->getFile()) : ($a->getFile() ? basename($a->getFile()) : '-') ?></dd>
                            <dt>Satır</dt>
                            <dd><?= $a->getLine() ?></dd>
                        </dl>
                    </div>
                </details>

                <!-- KOD ÖNİZLEME -->
                <?php if (GELISTIRICI && $a->getFile() && file_exists($a->getFile())): ?>
                    <details open>
                        <summary>Kod Önizleme</summary>
                        <div class="panel">
                            <?php
                            $errorFile = $a->getFile();
                            $errorLine = $a->getLine();
                            $lines = file($errorFile);
                            $start = max(0, $errorLine - 6);
                            $end = min(count($lines), $errorLine + 5);
                            ?>
                            <div class="code-preview">
                                <div class="code-lines">
                                    <?php for ($i = $start; $i < $end; $i++): ?>
                                        <div class="code-line <?= ($i + 1 === $errorLine) ? 'error-line' : '' ?>">
                                            <span class="line-number"><?= $i + 1 ?></span>
                                            <span class="line-content"><?= htmlspecialchars($lines[$i]) ?></span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </details>
                <?php endif; ?>

                <!-- ÇALIŞTIRILDIĞI YER -->
                <details open>
                    <summary>Çalıştırıldığı Yer</summary>
                    <div class="panel">
                        <dl>
                            <dt>Dosya</dt>
                            <dd><?= GELISTIRICI ? htmlspecialchars($b[0]['file']) : ($b[0]['file'] ? basename($b[0]['file']) : '-') ?></dd>
                            <dt>Satır</dt>
                            <dd><?= $b[0]['line'] ?? '-' ?></dd>
                            <dt>Fonksiyon</dt>
                            <dd><?= $b[0]['function'] ?? '-' ?></dd>
                        </dl>
                    </div>
                </details>

                <!-- KULLANICI -->
                <?php if (GELISTIRICI): ?>
                    <?php
                    // Platform ve tarayıcı tespiti
                    function tespit_platform($ua)
                    {
                        $platform = '-';
                        if (preg_match('/Windows NT/i', $ua)) $platform = 'Windows';
                        elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) $platform = 'Mac OS';
                        elseif (preg_match('/Linux/i', $ua)) $platform = 'Linux';
                        elseif (preg_match('/Android/i', $ua)) $platform = 'Android';
                        elseif (preg_match('/iPhone|iPad|iPod/i', $ua)) $platform = 'iOS';
                        elseif (preg_match('/CrOS/i', $ua)) $platform = 'Chrome OS';
                        return $platform;
                    }
                    function tespit_tarayici($ua)
                    {
                        if (preg_match('/Edg\//i', $ua)) return 'Microsoft Edge';
                        if (preg_match('/OPR\//i', $ua)) return 'Opera';
                        if (preg_match('/Chrome\//i', $ua)) return 'Google Chrome';
                        if (preg_match('/Firefox\//i', $ua)) return 'Mozilla Firefox';
                        if (preg_match('/Safari\//i', $ua) && !preg_match('/Chrome\//i', $ua)) return 'Safari';
                        if (preg_match('/MSIE|Trident/i', $ua)) return 'Internet Explorer';
                        return '-';
                    }
                    function tespit_cihaz($ua)
                    {
                        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $ua)) return 'Mobil';
                        if (preg_match('/Tablet|iPad/i', $ua)) return 'Tablet';
                        return 'Masaüstü';
                    }
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $platform = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? tespit_platform($ua);
                    $tarayici = tespit_tarayici($ua);
                    $cihaz = tespit_cihaz($ua);
                    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '-';
                    $referer = $_SERVER['HTTP_REFERER'] ?? '-';
                    $protocol = $_SERVER['SERVER_PROTOCOL'] ?? '-';
                    $port = $_SERVER['REMOTE_PORT'] ?? '-';
                    $accept = $_SERVER['HTTP_ACCEPT'] ?? '-';
                    $encoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '-';
                    $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '-';
                    $host = $_SERVER['HTTP_HOST'] ?? '-';
                    $conn = $_SERVER['HTTP_CONNECTION'] ?? '-';
                    $method = $_SERVER['REQUEST_METHOD'] ?? '-';
                    $query = $_SERVER['QUERY_STRING'] ?? '-';
                    $time = isset($_SERVER['REQUEST_TIME']) ? date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) : '-';
                    ?>
                    <details>
                        <summary>Kullanıcı</summary>
                        <div class="panel">
                            <dl>
                                <dt>Platform</dt>
                                <dd><?= htmlspecialchars($platform) ?></dd>
                                <dt>Tarayıcı</dt>
                                <dd><?= htmlspecialchars($tarayici) ?></dd>
                                <dt>Cihaz Türü</dt>
                                <dd><?= htmlspecialchars($cihaz) ?></dd>
                                <dt>User-Agent</dt>
                                <dd><?= htmlspecialchars($ua) ?></dd>
                                <dt>IP</dt>
                                <dd><?= htmlspecialchars($ip) ?></dd>
                                <dt>Port</dt>
                                <dd><?= htmlspecialchars($port) ?></dd>
                                <dt>Referer</dt>
                                <dd><?= htmlspecialchars($referer) ?></dd>
                                <dt>Protokol</dt>
                                <dd><?= htmlspecialchars($protocol) ?></dd>
                                <dt>Accept</dt>
                                <dd><?= htmlspecialchars($accept) ?></dd>
                                <dt>Kodlama</dt>
                                <dd><?= htmlspecialchars($encoding) ?></dd>
                                <dt>Dil</dt>
                                <dd><?= htmlspecialchars($lang) ?></dd>
                                <dt>Host</dt>
                                <dd><?= htmlspecialchars($host) ?></dd>
                                <dt>Bağlantı</dt>
                                <dd><?= htmlspecialchars($conn) ?></dd>
                                <dt>Method</dt>
                                <dd><?= htmlspecialchars($method) ?></dd>
                                <dt>Veri</dt>
                                <dd><?= htmlspecialchars($query) ?></dd>
                                <dt>Zaman</dt>
                                <dd><?= htmlspecialchars($time) ?></dd>
                            </dl>
                        </div>
                    </details>
                <?php endif; ?>

                <!-- REQUEST BİLGİLERİ -->
                <?php if (GELISTIRICI): ?>
                    <details>
                        <summary>Request Bilgileri
                            <?php
                            $totalItems = 0;
                            $totalItems += !empty($_GET) ? count($_GET) : 0;
                            $totalItems += !empty($_POST) ? count($_POST) : 0;
                            $totalItems += !empty($_COOKIE) ? count($_COOKIE) : 0;
                            $totalItems += !empty($_SESSION) ? count($_SESSION) : 0;
                            if ($totalItems > 0):
                            ?>
                                <span class="data-badge"><?= $totalItems ?></span>
                            <?php endif; ?>
                        </summary>
                        <div class="panel">
                            <?php if (!empty($_GET)): ?>
                                <details>
                                    <summary style="padding: 8px 0; font-size: 13px; color: var(--text-primary);">GET <span class="data-badge" style="margin-left: 6px;"><?= count($_GET) ?></span></summary>
                                    <div style="margin-top: 8px;">
                                        <dl>
                                            <?php foreach ($_GET as $key => $value): ?>
                                                <dt><?= htmlspecialchars($key) ?></dt>
                                                <dd><?= htmlspecialchars(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd>
                                            <?php endforeach; ?>
                                        </dl>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <?php if (!empty($_POST)): ?>
                                <details>
                                    <summary style="padding: 8px 0; font-size: 13px; color: var(--text-primary);">POST <span class="data-badge" style="margin-left: 6px;"><?= count($_POST) ?></span></summary>
                                    <div style="margin-top: 8px;">
                                        <dl>
                                            <?php foreach ($_POST as $key => $value): ?>
                                                <dt><?= htmlspecialchars($key) ?></dt>
                                                <dd><?= htmlspecialchars(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd>
                                            <?php endforeach; ?>
                                        </dl>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <?php if (!empty($_COOKIE)): ?>
                                <details>
                                    <summary style="padding: 8px 0; font-size: 13px; color: var(--text-primary);">COOKIE <span class="data-badge" style="margin-left: 6px;"><?= count($_COOKIE) ?></span></summary>
                                    <div style="margin-top: 8px;">
                                        <dl>
                                            <?php foreach ($_COOKIE as $key => $value): ?>
                                                <dt><?= htmlspecialchars($key) ?></dt>
                                                <dd><?= htmlspecialchars(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd>
                                            <?php endforeach; ?>
                                        </dl>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <?php if (!empty($_SESSION)): ?>
                                <details>
                                    <summary style="padding: 8px 0; font-size: 13px; color: var(--text-primary);">SESSION <span class="data-badge" style="margin-left: 6px;"><?= count($_SESSION) ?></span></summary>
                                    <div style="margin-top: 8px;">
                                        <dl>
                                            <?php foreach ($_SESSION as $key => $value): ?>
                                                <dt><?= htmlspecialchars($key) ?></dt>
                                                <dd><?= htmlspecialchars(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd>
                                            <?php endforeach; ?>
                                        </dl>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <?php if (empty($_GET) && empty($_POST) && empty($_COOKIE) && empty($_SESSION)): ?>
                                <div class="empty-state">Hiçbir request verisi bulunamadı</div>
                            <?php endif; ?>
                        </div>
                    </details>
                <?php endif; ?>

                <!-- ENVIRONMENT -->
                <?php if (GELISTIRICI): ?>
                    <details>
                        <summary>Environment & PHP Ayarları</summary>
                        <div class="panel">
                            <div class="env-grid">
                                <div class="env-item">
                                    <div class="env-item-label">PHP Version</div>
                                    <div class="env-item-value success"><?= phpversion() ?></div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Memory Limit</div>
                                    <div class="env-item-value"><?= ini_get('memory_limit') ?></div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Max Execution Time</div>
                                    <div class="env-item-value"><?= ini_get('max_execution_time') ?>s</div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Upload Max Filesize</div>
                                    <div class="env-item-value"><?= ini_get('upload_max_filesize') ?></div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Post Max Size</div>
                                    <div class="env-item-value"><?= ini_get('post_max_size') ?></div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Display Errors</div>
                                    <div class="env-item-value <?= ini_get('display_errors') ? 'warning' : 'success' ?>">
                                        <?= ini_get('display_errors') ? 'On' : 'Off' ?>
                                    </div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Error Reporting</div>
                                    <div class="env-item-value"><?= error_reporting() ?></div>
                                </div>
                                <div class="env-item">
                                    <div class="env-item-label">Timezone</div>
                                    <div class="env-item-value"><?= date_default_timezone_get() ?></div>
                                </div>
                            </div>
                            <details style="margin-top: 16px;">
                                <summary style="padding: 8px 0; font-size: 13px; color: var(--text-primary);">Yüklü Extension'lar (<?= count(get_loaded_extensions()) ?>)</summary>
                                <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px;">
                                    <?php foreach (get_loaded_extensions() as $ext): ?>
                                        <span style="background: rgba(59, 130, 246, 0.2); color: var(--accent); padding: 4px 10px; border-radius: 12px; font-size: 11px;"><?= $ext ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </details>
                        </div>
                    </details>
                <?php endif; ?>

                <!-- STACK TRACE -->
                <?php if (GELISTIRICI): ?>
                    <details>
                        <summary>Stack Trace (<?= min(count($b), $stackLimit) ?> seviye)</summary>
                        <div class="panel">
                            <?php
                            $traceCount = 0;
                            foreach (array_slice($b, 0, $stackLimit) as $trace):
                                $traceCount++;
                                $class = $trace['class'] ?? '';
                                $type = $trace['type'] ?? '';
                                $function = $trace['function'] ?? '';
                                $file = $trace['file'] ?? '';
                                $line = $trace['line'] ?? 0;
                                $args = $trace['args'] ?? [];
                            ?>
                                <div class="stack-item">
                                    <div class="stack-header">
                                        <span class="stack-number">#<?= $traceCount ?></span>
                                        <span class="stack-function">
                                            <?php if ($class): ?>
                                                <span class="class"><?= htmlspecialchars($class) ?></span><span style="color: var(--text-muted);"><?= htmlspecialchars($type) ?></span>
                                            <?php endif; ?>
                                            <span class="method"><?= htmlspecialchars($function) ?>()</span>
                                        </span>
                                    </div>
                                    <?php if ($file): ?>
                                        <div class="stack-location">
                                            <span class="file"><?= htmlspecialchars($file) ?></span>
                                            <?php if ($line): ?>
                                                <span class="line">satır <?= $line ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($args)): ?>
                                        <div class="stack-args">
                                            <details>
                                                <summary>↳ Parametreler (<?= count($args) ?>)</summary>
                                                <pre><?= htmlspecialchars(print_r($args, true)) ?></pre>
                                            </details>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($b) > $stackLimit): ?>
                                <div style="text-align: center; color: var(--text-muted); font-size: 12px; padding: 10px;">
                                    ... ve <?= count($b) - $stackLimit ?> seviye daha
                                </div>
                            <?php endif; ?>
                        </div>
                    </details>
                <?php endif; ?>

                <!-- SERVER -->
                <?php if (GELISTIRICI): ?>
                    <details>
                        <summary>Server Bilgileri</summary>
                        <div class="panel">
                            <dl>
                                <dt>Server</dt>
                                <dd><?= $_SERVER['SERVER_SOFTWARE'] ?? '-' ?></dd>
                                <dt>Server Adı</dt>
                                <dd><?= $_SERVER['SERVER_NAME'] ?? '-' ?></dd>
                                <dt>Server Port</dt>
                                <dd><?= $_SERVER['SERVER_PORT'] ?? '-' ?></dd>
                                <dt>PHP</dt>
                                <dd><?= phpversion() ?></dd>
                                <dt>Zend</dt>
                                <dd><?= zend_version() ?></dd>
                            </dl>
                        </div>
                    </details>
                <?php endif; ?>

            </div>
        </div>
    </main>

</body>

</html>
<?php
die(ob_get_clean());
