<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test Platform Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        <?php
        // HTTP Metod renklerini config'den al
        $methodColors = defined('API_METHOD_COLORS') ? API_METHOD_COLORS : [
            'GET' => '#1cc88a',
            'POST' => '#4e73df',
            'PUT' => '#f6c23e',
            'DELETE' => '#e74a3b',
            'PATCH' => '#36b9cc',
            'OPTIONS' => '#858796',
            'HEAD' => '#5a5c69'
        ];
        ?> :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --sidebar-bg: #f8f9fc;
            --sidebar-border: #e3e6f0;
            --header-bg: #ffffff;
            --accent-primary: #4e73df;
            --accent-success: #1cc88a;
            --accent-warning: #f6c23e;
            --accent-danger: #e74a3b;
            --accent-info: #36b9cc;
            --border-color: #e3e6f0;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            --bg-primary: #1a1d23;
            --bg-secondary: #23272e;
            --text-primary: #e0e6ed;
            --text-secondary: #a0a8b5;
            --sidebar-bg: #1e2228;
            --sidebar-border: #2d3139;
            --header-bg: #23272e;
            --accent-primary: #5a8dee;
            --accent-success: #39da8a;
            --accent-warning: #fdac41;
            --accent-danger: #ff5b5c;
            --accent-info: #00cfdd;
            --border-color: #2d3139;
            --shadow: rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 25px;
            transition: background-color 0.3s;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 22px;
            cursor: pointer;
            padding: 8px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-info));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .header-brand h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }

        .favorite-count-badge,
        .recent-count-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: var(--bg-primary);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .favorite-count-badge {
            color: var(--accent-warning);
        }

        .favorite-count-badge:hover {
            background: linear-gradient(135deg, var(--accent-warning) 0%, #d4a017 100%);
            color: white;
            border-color: var(--accent-warning);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(246, 194, 62, 0.3);
        }

        .recent-count-badge {
            color: var(--accent-info);
        }

        .recent-count-badge:hover {
            background: linear-gradient(135deg, var(--accent-info) 0%, #2c9faf 100%);
            color: white;
            border-color: var(--accent-info);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(54, 185, 204, 0.3);
        }

        .favorite-count-badge i,
        .recent-count-badge i {
            font-size: 14px;
        }

        .stats-badge {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 8px 16px;
            background: var(--bg-primary);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .stat-item i {
            color: var(--accent-primary);
        }

        .theme-toggle,
        .history-toggle,
        .clear-storage-toggle {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .theme-toggle:hover,
        .history-toggle:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        .clear-storage-toggle:hover {
            background: var(--accent-danger);
            color: white;
            border-color: var(--accent-danger);
        }

        .history-toggle {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .history-toggle:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        .history-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-danger);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        .sidebar {
            position: fixed;
            top: 65px;
            left: 0;
            width: 280px;
            height: calc(100vh - 65px);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 999;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-section {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding: 10px 14px;
            background: var(--accent-primary);
            border-radius: 10px;
        }

        .sidebar-title {
            font-size: 13px;
            font-weight: 600;
            color: white;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-title::before {
            content: '';
            width: 2px;
            height: 14px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 1px;
        }

        .filter-button {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .filter-button:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .favorite-filter-button,
        .recent-filter-button {
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            background: var(--bg-primary);
            color: var(--text-primary);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            width: 100%;
            margin-bottom: 6px;
        }

        .favorite-filter-button:hover {
            background: var(--bg-secondary);
            border-color: var(--border-color);
        }

        .favorite-filter-button:hover i {
            color: var(--accent-warning);
        }

        .recent-filter-button:hover {
            background: var(--bg-secondary);
            border-color: var(--border-color);
        }

        .recent-filter-button:hover i {
            color: var(--accent-info);
        }

        .favorite-filter-button i {
            color: var(--accent-warning);
            transition: color 0.2s ease;
        }

        .recent-filter-button i {
            color: var(--accent-info);
            transition: color 0.2s ease;
        }

        .filter-group {
            margin-bottom: 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px;
            transition: all 0.2s ease;
        }

        .filter-group:hover {
            border-color: var(--border-color);
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .filter-label i {
            color: var(--accent-primary);
            font-size: 13px;
        }

        .filter-select,
        .env-input {
            width: 100%;
            padding: 8px 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .env-input {
            font-family: 'Courier New', monospace;
            cursor: text;
        }

        .filter-select:hover,
        .env-input:hover {
            border-color: var(--border-color);
        }

        .filter-select:focus,
        .env-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        /* ============================================
           LIMITLESS ADMIN TEMPLATE STYLE MENÜ
           Minimal, temiz ve profesyonel
           ============================================ */

        .accordion-category {
            margin-bottom: 0;
        }

        .accordion-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: transparent;
            border: none;
            border-radius: 0;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-bottom: 0;
            color: var(--text-primary);
        }

        .accordion-header:hover {
            background: var(--bg-secondary);
        }

        .accordion-header.active {
            background: var(--bg-secondary);
        }

        .accordion-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            font-size: 13px;
            flex: 1;
        }

        .accordion-title i {
            font-size: 14px;
            width: 18px;
            text-align: center;
            color: var(--text-secondary);
        }

        .accordion-header.active .accordion-title i {
            color: var(--accent-primary);
        }

        .accordion-icon {
            transition: transform 0.2s ease;
            font-size: 10px;
            color: var(--text-secondary);
            margin-left: auto;
        }

        .accordion-header.active .accordion-icon {
            transform: rotate(90deg);
        }

        .accordion-count {
            background: var(--accent-primary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            min-width: 22px;
            text-align: center;
            margin-left: 8px;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: 0;
        }

        .accordion-content.active {
            max-height: 2000px;
        }

        .endpoint-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .endpoint-item {
            padding: 8px 12px 8px 40px;
            margin-bottom: 0;
            border-radius: 0;
            cursor: pointer;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-primary);
            background: transparent;
            border: none;
        }

        .endpoint-item:hover {
            background: var(--bg-secondary);
        }

        .endpoint-item.active {
            background: var(--bg-secondary);
            color: var(--accent-primary);
            font-weight: 500;
        }

        .endpoint-item.active .method-badge {
            background: rgba(255, 255, 255, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 600;
        }

        /* Aktif menüde metod badge'i metod rengini korur */
        .endpoint-item.active .method-badge.method-get {
            background-color: <?php echo htmlspecialchars($methodColors['GET'] ?? '#1cc88a', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .endpoint-item.active .method-badge.method-post {
            background-color: <?php echo htmlspecialchars($methodColors['POST'] ?? '#4e73df', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .endpoint-item.active .method-badge.method-put {
            background-color: <?php echo htmlspecialchars($methodColors['PUT'] ?? '#f6c23e', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .endpoint-item.active .method-badge.method-delete {
            background-color: <?php echo htmlspecialchars($methodColors['DELETE'] ?? '#e74a3b', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .endpoint-item.active .method-badge.method-patch {
            background-color: <?php echo htmlspecialchars($methodColors['PATCH'] ?? '#36b9cc', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .endpoint-item.active .method-badge.method-options {
            background-color: <?php echo htmlspecialchars($methodColors['OPTIONS'] ?? '#858796', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .endpoint-item.active .method-badge.method-head {
            background-color: <?php echo htmlspecialchars($methodColors['HEAD'] ?? '#5a5c69', ENT_QUOTES, 'UTF-8'); ?> !important;
            color: white;
            opacity: 0.9;
        }

        .method-badge {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        /* HTTP Metod Renkleri - Config'den alınıyor */
        .method-get {
            background-color: <?php echo htmlspecialchars($methodColors['GET'] ?? '#1cc88a', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .method-post {
            background-color: <?php echo htmlspecialchars($methodColors['POST'] ?? '#4e73df', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .method-put {
            background-color: <?php echo htmlspecialchars($methodColors['PUT'] ?? '#f6c23e', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .method-delete {
            background-color: <?php echo htmlspecialchars($methodColors['DELETE'] ?? '#e74a3b', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .method-patch {
            background-color: <?php echo htmlspecialchars($methodColors['PATCH'] ?? '#36b9cc', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .method-options {
            background-color: <?php echo htmlspecialchars($methodColors['OPTIONS'] ?? '#858796', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .method-head {
            background-color: <?php echo htmlspecialchars($methodColors['HEAD'] ?? '#5a5c69', ENT_QUOTES, 'UTF-8'); ?>;
            color: white;
        }

        .main-content {
            margin-left: 280px;
            margin-top: 65px;
            padding: 25px;
            transition: margin-left 0.3s ease;
        }

        .main-content.full-width {
            margin-left: 0;
        }

        .page-header {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        /* Error Codes Grid (Modal içinde kullanılıyor) */
        .error-codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .error-code-item {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.3s ease;
        }

        .error-code-item:hover {
            border-color: var(--accent-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .error-code-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .error-code-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 14px;
        }

        .error-code-number {
            background: var(--accent-danger);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .error-code-title {
            color: var(--text-primary);
            font-size: 14px;
        }

        .error-code-desc {
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.6;
            margin-top: 8px;
        }

        /* Rate Limit Info (Tab içinde kullanılıyor) */
        .rate-limit-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 0;
        }

        .rate-limit-item {
            background: var(--bg-primary);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .rate-limit-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .rate-limit-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Tab içindeki rate limit için */
        #ratelimit-tab .rate-limit-value {
            font-size: 18px;
        }

        /* Export Panel */
        .export-panel {
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .export-panel:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .export-panel-header {
            background: linear-gradient(135deg, var(--accent-info) 0%, #2c9faf 100%);
            color: white;
            padding: 20px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-weight: 700;
            font-size: 17px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            user-select: none;
        }

        .export-panel-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
        }

        .export-panel-header:hover {
            opacity: 0.95;
        }

        .export-panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .export-panel-icon {
            transition: transform 0.3s ease;
            font-size: 14px;
        }

        .export-panel.active .export-panel-icon {
            transform: rotate(180deg);
        }

        .export-panel-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            padding: 0 28px;
        }

        .export-panel.active .export-panel-body {
            max-height: 2000px;
            padding: 28px;
        }

        .export-formats-compact {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .export-category-compact {
            background: transparent;
            border: none;
            padding: 0;
            margin-bottom: 20px;
        }

        .export-category-compact:last-child {
            margin-bottom: 0;
        }

        .export-category-title-compact {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .export-formats-list-compact {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .export-format-btn-compact {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .export-format-btn-compact:hover {
            border-color: var(--accent-primary);
            background: var(--bg-secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .export-format-icon-compact {
            width: 24px;
            height: 24px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            flex-shrink: 0;
        }

        .rate-limit-progress {
            margin-top: 12px;
            height: 8px;
            background: var(--bg-secondary);
            border-radius: 4px;
            overflow: hidden;
        }

        .rate-limit-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-success), var(--accent-warning));
            transition: width 0.3s ease;
        }


        .api-info-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .api-info-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .api-info-header {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #3a5dd9 100%);
            color: white;
            padding: 32px 36px;
            position: relative;
            overflow: hidden;
        }

        .api-info-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 20s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .api-info-header-content {
            position: relative;
            z-index: 1;
        }

        .api-info-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .api-info-title-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .api-info-title-text {
            flex: 1;
        }

        .api-info-title-text h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .api-info-title-text p {
            font-size: 14px;
            margin: 0;
            opacity: 0.95;
            color: white;
            line-height: 1.6;
        }

        .api-info-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .api-info-stat {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .api-info-stat:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .api-info-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .api-info-stat-value {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .api-info-stat-value i {
            font-size: 18px;
            opacity: 0.9;
        }

        .api-info-body {
            padding: 0;
        }

        .api-info-main {
            padding: 28px 36px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .api-info-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px;
            background: var(--bg-primary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .api-info-item:hover {
            background: var(--bg-secondary);
            border-color: var(--accent-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .api-info-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .api-info-item-icon.primary {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #4a6fd8 100%);
            color: white;
        }

        .api-info-item-icon.info {
            background: linear-gradient(135deg, var(--accent-info) 0%, #00b8c4 100%);
            color: white;
        }

        .api-info-item-icon.success {
            background: linear-gradient(135deg, var(--accent-success) 0%, #2db870 100%);
            color: white;
        }

        .api-info-item-icon.warning {
            background: linear-gradient(135deg, var(--accent-warning) 0%, #e89a2e 100%);
            color: white;
        }

        .api-info-item-icon.danger {
            background: linear-gradient(135deg, var(--accent-danger) 0%, #e63946 100%);
            color: white;
        }

        .api-info-item-content {
            flex: 1;
            min-width: 0;
        }

        .api-info-item-label {
            font-size: 11px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .api-info-item-value {
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 600;
            word-break: break-word;
        }

        .api-info-item-value a {
            color: inherit;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .api-info-item-value a {
            color: var(--accent-primary);
            font-weight: 600;
        }

        .api-info-item-value a:hover {
            color: var(--accent-info);
            text-decoration: underline;
        }

        /* Accordion for Details */
        .api-info-accordion {
            border-top: 1px solid var(--border-color);
        }

        .api-info-accordion-header {
            padding: 20px 36px;
            background: var(--bg-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            user-select: none;
        }

        .api-info-accordion-header:hover {
            background: var(--bg-secondary);
        }

        .api-info-accordion-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .api-info-accordion-title i {
            color: var(--accent-primary);
            font-size: 16px;
        }

        .api-info-accordion-icon {
            transition: transform 0.3s ease;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .api-info-accordion-header.active .api-info-accordion-icon {
            transform: rotate(180deg);
        }

        .api-info-accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            background: var(--bg-primary);
        }

        .api-info-accordion-content.active {
            max-height: 2000px;
        }

        .api-info-accordion-body {
            padding: 0 36px 24px 36px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .stats-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, var(--accent-success), #17a673);
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, var(--accent-danger), #c92b2b);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--accent-warning), #d4a017);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, var(--accent-info), #2c9faf);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .token-generator-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .token-generator-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .token-generator-header {
            background: linear-gradient(135deg, var(--accent-warning) 0%, #d4a017 100%);
            color: white;
            padding: 20px 28px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 17px;
            position: relative;
            overflow: hidden;
        }

        .token-generator-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
        }

        .token-generator-body {
            padding: 28px;
        }

        .token-display-group {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .token-display {
            flex: 1;
            padding: 14px 18px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .token-display:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .token-display.empty {
            color: var(--text-secondary);
            font-style: italic;
        }

        .btn-generate-token {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            background: linear-gradient(135deg, var(--accent-warning) 0%, #d4a017 100%);
            box-shadow: 0 4px 12px rgba(246, 194, 62, 0.3);
        }

        .btn-generate-token:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(246, 194, 62, 0.4);
        }

        .btn-copy-token {
            padding: 12px 20px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-copy-token:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        /* ============================================
           MODERN TEST CARD TASARIMI
           Önceki tasarım + modern iyileştirmeler
           ============================================ */
        .test-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
            scroll-margin-top: 80px;
            transition: all 0.3s ease;
        }

        .test-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .test-card-header {
            background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-info) 100%);
            color: white;
            padding: 20px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: 17px;
            position: relative;
            overflow: hidden;
        }

        .test-card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
        }

        .test-card-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(10px);
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .test-card-body {
            padding: 28px;
        }

        .live-test-area {
            margin-bottom: 28px;
        }

        .params-builder {
            margin-bottom: 24px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .params-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 13px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .params-header-title {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .params-rows {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .param-row {
            display: grid;
            grid-template-columns: 30px 1fr 1fr 40px;
            gap: 10px;
            align-items: center;
        }

        .param-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .param-input {
            width: 100%;
            padding: 10px 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Courier New', monospace;
            transition: all 0.2s;
        }

        .param-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .param-remove-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .param-remove-btn:hover {
            background: var(--accent-danger);
            color: white;
            border-color: var(--accent-danger);
            transform: scale(1.05);
        }

        .params-add-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--accent-primary);
            color: var(--accent-primary);
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 600;
        }

        .params-add-btn:hover {
            background: var(--accent-primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        .url-input-group {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .url-input-wrapper {
            flex: 1;
            position: relative;
        }

        .url-input-wrapper label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: block;
        }

        .url-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Courier New', monospace;
            transition: all 0.2s;
        }

        .url-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .method-select {
            padding: 12px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            min-width: 110px;
            height: 44px;
            align-self: flex-end;
            transition: all 0.2s;
        }

        .method-select:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .btn-test {
            background: linear-gradient(135deg, var(--accent-success) 0%, #17a673 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            height: 44px;
            align-self: flex-end;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(28, 200, 138, 0.3);
        }

        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(28, 200, 138, 0.4);
        }

        .response-action-btn {
            flex: 1;
            padding: 7px 14px;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 34px;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }

        .response-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .response-action-btn:hover::before {
            left: 100%;
        }

        .response-action-btn:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25) !important;
            filter: brightness(1.05);
        }

        .response-action-btn:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.1s ease;
        }

        .response-action-btn i {
            transition: transform 0.2s ease;
        }

        .response-action-btn:hover i {
            transform: scale(1.1);
        }

        .request-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 12px;
            background: transparent;
            padding: 0;
            border-bottom: 2px solid var(--border-color);
        }

        .tab-button {
            padding: 10px 16px;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 0;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }

        .tab-button i {
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .tab-button:hover {
            color: var(--text-primary);
            background: transparent;
        }

        .tab-button.active {
            color: var(--accent-primary);
            background: transparent;
            border-bottom-color: var(--accent-primary);
            font-weight: 600;
        }

        .tab-button.active i {
            color: var(--accent-primary);
        }

        .tab-content {
            display: none;
            margin-bottom: 16px;
        }

        .tab-content.active {
            display: block;
        }

        .tab-content-card {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-textarea {
            width: 100%;
            padding: 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-family: 'Courier New', monospace;
            font-size: 13px;
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
            transition: all 0.2s ease;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.1);
        }

        .form-textarea::placeholder {
            color: var(--text-secondary);
            opacity: 0.5;
        }

        /* Panel Toolbar */
        .panel-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            gap: 8px;
            flex-wrap: wrap;
        }

        .panel-toolbar-left {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .panel-toolbar-right {
            display: flex;
            gap: 6px;
        }

        .toolbar-btn {
            padding: 6px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .toolbar-btn:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        .toolbar-btn i {
            font-size: 11px;
        }

        /* Headers Key-Value Form */
        .headers-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .header-row {
            display: grid;
            grid-template-columns: 1fr 2fr auto;
            gap: 8px;
            align-items: center;
        }

        .header-input {
            padding: 8px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Courier New', monospace;
        }

        .header-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.1);
        }

        .header-remove-btn {
            padding: 8px 12px;
            background: var(--accent-danger);
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .header-remove-btn:hover {
            background: #c0392b;
        }

        .add-header-btn {
            padding: 8px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .add-header-btn:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        /* Auth Type Selector */
        .auth-type-selector {
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .auth-type-btn {
            padding: 6px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .auth-type-btn.active {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        .auth-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .auth-input {
            padding: 10px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Courier New', monospace;
        }

        .auth-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.1);
        }

        /* JSON Status */
        .json-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            margin-top: 6px;
        }

        .json-status.valid {
            color: var(--accent-success);
        }

        .json-status.invalid {
            color: var(--accent-danger);
        }

        .response-section {
            margin-top: 28px;
            border-top: 2px solid var(--border-color);
            padding-top: 24px;
        }

        .analytics-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            margin-top: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .analytics-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .analytics-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            margin-top: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .stat-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .stat-card-icon.success {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        }

        .stat-card-icon.info {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .stat-card-icon.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        .stat-card-icon.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
        }

        .stat-card-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .stat-card-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .recent-requests-table {
            margin-top: 24px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .table-header {
            background: var(--bg-secondary);
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-header h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        .requests-table {
            width: 100%;
            border-collapse: collapse;
        }

        .requests-table th {
            background: var(--bg-secondary);
            padding: 12px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .requests-table td {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
            color: var(--text-primary);
        }

        .requests-table tr:last-child td {
            border-bottom: none;
        }

        .requests-table tr:hover {
            background: var(--bg-secondary);
        }

        .method-badge-table {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-badge.success {
            background: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .status-badge.error {
            background: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }

        .status-badge.warning {
            background: rgba(255, 193, 7, 0.1);
            color: #f6c23e;
        }

        .url-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .response-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .response-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .response-label {
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .response-controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .response-btn {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .response-btn:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .status-success {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
            color: white;
        }

        .status-error {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            color: white;
        }

        .response-time {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .response-area {
            background: #f5f5f5;
            color: var(--text-primary);
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 12px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
            min-height: 100px;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
            position: relative;
        }

        #responseContent {
            margin: 0;
            padding: 0;
            display: block;
        }

        /* Dark mode için response area */
        [data-theme="dark"] .response-area {
            background: #2a2a2a;
            border-color: #3a3a3a;
            color: #e0e0e0;
        }

        /* Response area scrollbar */
        .response-area::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .response-area::-webkit-scrollbar-track {
            background: #f5f5f5;
            border-radius: 4px;
        }

        .response-area::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 4px;
        }

        .response-area::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        [data-theme="dark"] .response-area::-webkit-scrollbar-track {
            background: #2a2a2a;
        }

        [data-theme="dark"] .response-area::-webkit-scrollbar-thumb {
            background: #555;
        }

        [data-theme="dark"] .response-area::-webkit-scrollbar-thumb:hover {
            background: #666;
        }

        .search-box {
            position: sticky;
            top: 0;
            background: #2d2d2d;
            padding: 10px;
            margin: -20px -20px 10px -20px;
            border-bottom: 1px solid #444;
            display: none;
        }

        .search-box.active {
            display: block;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px;
            background: #1e1e1e;
            border: 1px solid #444;
            border-radius: 4px;
            color: #d4d4d4;
            font-size: 12px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .highlight {
            background-color: yellow;
            color: black;
            padding: 2px 4px;
            border-radius: 2px;
        }

        .notification-toast {
            position: fixed;
            top: 80px;
            right: 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--accent-success);
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 5px 20px var(--shadow);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 10000;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }

        .notification-toast.show {
            display: flex;
        }

        .notification-toast.error {
            border-left-color: var(--accent-danger);
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--accent-success);
            color: white;
            font-size: 14px;
        }

        .notification-toast.error .notification-icon {
            background: var(--accent-danger);
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .notification-message {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px var(--shadow);
        }

        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .modal-body {
            padding: 25px;
        }

        .favorite-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
            font-size: 14px;
            margin-left: auto;
        }

        .favorite-btn:hover {
            background: var(--bg-primary);
            color: var(--accent-warning);
        }

        .favorite-btn.active {
            color: var(--accent-warning);
        }

        .favorite-btn.active i {
            font-weight: 900;
        }

        .endpoint-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .template-item,
        .history-item {
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .template-item:hover,
        .history-item:hover {
            background: var(--bg-primary);
            border-color: var(--accent-primary);
        }

        .template-header,
        .history-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .template-name,
        .history-url {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        .template-actions,
        .history-actions {
            display: flex;
            gap: 8px;
        }

        .template-btn,
        .history-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
            font-size: 14px;
        }

        .template-btn:hover,
        .history-btn:hover {
            background: var(--bg-primary);
            color: var(--accent-primary);
        }

        .template-btn.delete:hover,
        .history-btn.delete:hover {
            color: var(--accent-danger);
        }

        .template-desc,
        .history-time {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .template-meta,
        .history-meta {
            display: flex;
            gap: 10px;
            font-size: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .history-status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .history-status.success {
            background: rgba(28, 200, 138, 0.15);
            color: var(--accent-success);
        }

        .history-status.error {
            background: rgba(231, 74, 59, 0.15);
            color: var(--accent-danger);
        }

        /* ============================================
           MODERN ENDPOINT CARD TASARIMI
           Önceki tasarım + modern iyileştirmeler
           ============================================ */
        .endpoint-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: none;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .endpoint-card.active {
            display: block;
            animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .endpoint-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
            border-color: var(--accent-primary);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .endpoint-card-header-modern {
            padding: 24px 28px;
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.05) 0%, rgba(78, 115, 223, 0.02) 100%);
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .endpoint-title-group {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }

        .endpoint-title-group h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .endpoint-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .endpoint-description {
            padding: 20px 28px;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.7;
            border-bottom: 1px solid var(--border-color);
        }

        .endpoint-details {
            padding: 24px 28px;
        }

        .detail-section {
            margin-bottom: 24px;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-label i {
            font-size: 10px;
            opacity: 0.7;
        }

        .url-display {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Courier New', monospace;
            font-size: 13px;
            color: var(--text-primary);
            background: var(--bg-primary);
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            word-break: break-all;
            line-height: 1.6;
            position: relative;
        }

        .request-example {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Courier New', monospace;
            font-size: 12px;
            background: var(--bg-primary);
            padding: 16px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            line-height: 1.8;
            overflow-x: auto;
        }

        .request-example .method-tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            margin-right: 8px;
        }

        .request-body-code {
            margin-top: 12px;
            padding: 12px;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 6px;
            font-size: 11px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .endpoint-actions {
            padding: 20px 28px;
            background: var(--bg-primary);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .endpoint-card details summary {
            cursor: pointer;
            user-select: none;
            list-style: none;
            padding: 10px 14px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
        }

        .endpoint-card details summary:hover {
            background: var(--bg-secondary);
            border-color: var(--accent-primary);
            color: var(--text-primary);
        }

        .endpoint-card details summary::-webkit-details-marker {
            display: none;
        }

        .endpoint-card details[open] summary i.fa-chevron-right {
            transform: rotate(90deg);
        }

        .endpoint-card details summary i.fa-chevron-right {
            transition: transform 0.2s;
            font-size: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .endpoint-card-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-primary);
            position: relative;
            overflow: hidden;
        }

        .endpoint-card-title {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .endpoint-card-title h4 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            display: block;
            visibility: visible;
            opacity: 1;
            flex: 1;
        }

        .auth-badge {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .auth-required {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            color: white;
        }

        .auth-not-required {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
            color: white;
        }

        .endpoint-desc {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.7;
            margin: 0;
        }

        .endpoint-card-body {
            padding: 28px;
        }

        .endpoint-url-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            padding: 18px 20px;
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .url-label {
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .endpoint-url-display {
            background: var(--bg-secondary);
            padding: 12px 16px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 500;
            word-break: break-all;
            border: 1px solid var(--border-color);
        }

        .example-response-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            padding: 18px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .example-response-label {
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .example-response-content {
            font-family: 'Courier New', monospace;
            color: var(--text-primary);
            font-size: 13px;
            line-height: 1.6;
            white-space: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            word-break: normal;
            background: #f5f5f5;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            max-height: 200px;
            scrollbar-width: thin;
            scrollbar-color: #bbb #f5f5f5;
        }

        .example-response-content::-webkit-scrollbar {
            height: 8px;
        }

        .example-response-content::-webkit-scrollbar-track {
            background: #f5f5f5;
            border-radius: 4px;
        }

        .example-response-content::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 4px;
        }

        .example-response-content::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        /* Dark mode için koyu */
        [data-theme="dark"] .example-response-content {
            background: #2a2a2a;
            border-color: #3a3a3a;
            color: #e0e0e0;
        }

        [data-theme="dark"] .example-response-content::-webkit-scrollbar-track {
            background: #2a2a2a;
        }

        [data-theme="dark"] .example-response-content::-webkit-scrollbar-thumb {
            background: #555;
        }

        [data-theme="dark"] .example-response-content::-webkit-scrollbar-thumb:hover {
            background: #666;
        }

        [data-theme="dark"] .example-response-content {
            scrollbar-color: #555 #2a2a2a;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin: 0;
        }

        .action-button {
            padding: 12px 18px;
            border: 1px solid var(--border-color);
            background: var(--bg-primary);
            color: var(--text-primary);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .action-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .action-button:hover::before {
            left: 100%;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-button:active {
            transform: translateY(0);
        }

        .action-button.primary {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #3a5dd9 100%);
            color: white;
            border-color: var(--accent-primary);
        }

        .action-button.primary:hover {
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.3);
        }

        .action-button.success {
            background: linear-gradient(135deg, var(--accent-success) 0%, #17a673 100%);
            color: white;
            border-color: var(--accent-success);
        }

        .action-button.success:hover {
            box-shadow: 0 6px 20px rgba(28, 200, 138, 0.3);
        }

        .action-button.warning {
            background: linear-gradient(135deg, var(--accent-warning) 0%, #d4a017 100%);
            color: white;
            border-color: var(--accent-warning);
        }

        .action-button.warning:hover {
            box-shadow: 0 6px 20px rgba(246, 194, 62, 0.3);
        }

        .action-button.info {
            background: linear-gradient(135deg, var(--accent-info) 0%, #2c9faf 100%);
            color: white;
            border-color: var(--accent-info);
        }

        .action-button.info:hover {
            box-shadow: 0 6px 20px rgba(54, 185, 204, 0.3);
        }

        .action-button.secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .action-button.secondary:hover {
            background: var(--bg-primary);
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .action-button.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            color: white;
            border-color: #e74a3b;
        }

        .action-button.danger:hover {
            box-shadow: 0 6px 20px rgba(231, 74, 59, 0.3);
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .stats-badge {
                display: none;
            }

            .favorite-count-badge span,
            .recent-count-badge span {
                display: none;
            }

            .favorite-count-badge,
            .recent-count-badge {
                padding: 8px;
                min-width: 40px;
                justify-content: center;
            }

            .url-input-group {
                flex-direction: column;
            }

            .method-select {
                width: 100%;
            }

            .notification-toast {
                right: 10px;
                left: 10px;
                min-width: auto;
            }

            .response-controls {
                width: 100%;
            }

            .response-btn {
                flex: 1;
                justify-content: center;
            }

            .token-display-group {
                flex-direction: column;
            }

            .stats-dashboard {
                grid-template-columns: 1fr;
            }

            .test-card-actions {
                flex-direction: column;
                width: 100%;
            }

            .action-btn {
                justify-content: center;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .stats-grid-responsive {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .request-tabs {
                gap: 2px;
            }

            .tab-button {
                padding: 8px 12px;
                font-size: 12px;
            }

            .form-textarea {
                min-height: 100px;
                font-size: 12px;
                padding: 10px;
            }

            .api-info-header {
                padding: 24px 20px;
            }

            .api-info-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .api-info-title-text h3 {
                font-size: 22px;
            }

            .api-info-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .api-info-stat-value {
                font-size: 16px;
            }

            .api-info-main {
                padding: 20px;
                grid-template-columns: 1fr;
            }

            .api-info-accordion-header {
                padding: 16px 20px;
            }

            .api-info-accordion-body {
                padding: 0 20px 20px 20px;
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid-responsive {
                grid-template-columns: 1fr !important;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 65px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }

        .sidebar-overlay.show {
            display: block;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-primary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
</head>

<body>
    <div class="notification-toast" id="notificationToast">
        <div class="notification-icon"><i class="fas fa-check"></i></div>
        <div class="notification-content">
            <div class="notification-title" id="notificationTitle">Başarılı</div>
            <div class="notification-message" id="notificationMessage">İşlem tamamlandı</div>
        </div>
    </div>

    <div class="header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <div class="header-brand">
                <div class="brand-icon"><i class="fas fa-bolt"></i></div>
                <h4>API Test Platform</h4>
            </div>
        </div>
        <div class="header-info">
            <div class="stats-badge">
                <div class="stat-item"><i class="fas fa-link"></i><span><strong id="totalEndpoints">0</strong> Endpoint</span></div>
                <div class="stat-item"><i class="fas fa-folder"></i><span><strong id="totalCategories">0</strong> Kategori</span></div>
            </div>
            <div class="favorite-count-badge" onclick="showFavorites()" title="Favorileri göster">
                <i class="fas fa-star"></i>
                <span id="favoriteCount">0</span>
            </div>
            <div class="recent-count-badge" onclick="showRecent()" title="Son kullanılanları göster">
                <i class="fas fa-clock"></i>
                <span id="recentCount">0</span>
            </div>
            <button class="history-toggle" onclick="openHistory()" title="İstek Geçmişi">
                <i class="fas fa-history"></i>
                <span class="history-badge" id="historyCount">0</span>
            </button>
            <button class="clear-storage-toggle" onclick="clearAllStorage()" title="Tüm Verileri Temizle">
                <i class="fas fa-trash-alt"></i>
            </button>
            <button class="theme-toggle" onclick="toggleTheme()" title="Tema Değiştir">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-header">
                <span class="sidebar-title">Arama ve Filtre</span>
                <button class="filter-button" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Sıfırla
                </button>
            </div>
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-search"></i> Endpoint Ara
                </label>
                <input type="text"
                    class="filter-select"
                    id="endpointSearch"
                    placeholder="Endpoint adı veya açıklama ara..."
                    oninput="searchEndpoints()">
            </div>
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-folder"></i> Kategori
                </label>
                <select class="filter-select" id="categoryFilter" onchange="filterEndpoints()">
                    <option value="">Tüm Kategoriler</option>
                    <?php
                    $endpoints = $dokuman['endpoints'] ?? [];
                    foreach ($endpoints as $kategoriAdi => $kategoriEndpoints):
                        if (empty($kategoriEndpoints)) continue;
                    ?>
                        <option value="<?php echo htmlspecialchars(strtolower($kategoriAdi), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars(ucfirst($kategoriAdi), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-code"></i> Method
                </label>
                <select class="filter-select" id="methodFilter" onchange="filterEndpoints()">
                    <option value="">Tüm Methodlar</option>
                    <option value="get">GET</option>
                    <option value="post">POST</option>
                    <option value="put">PUT</option>
                    <option value="delete">DELETE</option>
                    <option value="patch">PATCH</option>
                </select>
            </div>
            <div class="filter-group" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-color);">
                <button class="favorite-filter-button" onclick="showFavorites()">
                    <i class="fas fa-star"></i> Favoriler
                </button>
                <button class="recent-filter-button" onclick="showRecent()" style="margin-bottom: 0;">
                    <i class="fas fa-clock"></i> Son Kullanılanlar
                </button>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-header">
                <span class="sidebar-title">Endpoint Kategorileri</span>
            </div>

            <?php
            // Endpoint'leri kategorilere göre göster
            $endpoints = $dokuman['endpoints'] ?? [];
            $kategoriIndex = 0;
            foreach ($endpoints as $kategoriAdi => $kategoriEndpoints):
                if (empty($kategoriEndpoints) || !is_array($kategoriEndpoints)) continue;

                $isActive = $kategoriIndex === 0 ? 'active' : '';
                $kategoriIndex++;
            ?>
                <div class="accordion-category">
                    <div class="accordion-header <?php echo $isActive; ?>" onclick="toggleAccordion(this)">
                        <div class="accordion-title">
                            <i class="fas fa-folder"></i>
                            <span><?php echo htmlspecialchars(ucfirst($kategoriAdi), ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="accordion-count"><?php echo count($kategoriEndpoints); ?></span>
                            <i class="fas fa-chevron-right accordion-icon"></i>
                        </div>
                    </div>
                    <div class="accordion-content <?php echo $isActive; ?>">
                        <ul class="endpoint-list">
                            <?php foreach ($kategoriEndpoints as $endpoint):
                                $endpointId = $endpoint['id'] ?? '';
                                $ozellikler = $endpoint['ozellikler'] ?? [];

                                // İlk izinli metodu bul (öncelik: GET > POST > PUT > DELETE)
                                $izinliMetodlar = [];
                                foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $metod) {
                                    if (isset($ozellikler[$metod]) && $ozellikler[$metod] === true) {
                                        $izinliMetodlar[] = $metod;
                                    }
                                }
                                $ilkMetod = !empty($izinliMetodlar) ? strtolower($izinliMetodlar[0]) : 'get';
                            ?>
                                <li class="endpoint-item"
                                    data-endpoint="<?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-endpoint-desc="<?php echo htmlspecialchars($endpoint['endpoint_aciklama'] ?? $endpoint['aciklama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    onclick="selectEndpoint(this, '<?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?>')">
                                    <span class="method-badge method-<?php echo htmlspecialchars($ilkMetod, ENT_QUOTES, 'UTF-8'); ?>"><?php echo !empty($izinliMetodlar) ? htmlspecialchars($izinliMetodlar[0], ENT_QUOTES, 'UTF-8') : 'GET'; ?></span>
                                    <span><?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <button class="favorite-btn"
                                        onclick="event.stopPropagation(); toggleFavorite('<?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?>', this)"
                                        title="Favorilere ekle/çıkar">
                                        <i class="far fa-star"></i>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($endpoints)): ?>
                <div style="padding: 20px; text-align: center; color: var(--text-secondary);">
                    <i class="fas fa-inbox" style="font-size: 32px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="font-size: 13px;">Henüz endpoint bulunamadı</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <!-- API Bilgileri Kartı -->
        <div class="api-info-card">
            <div class="api-info-header">
                <div class="api-info-header-content">
                    <div class="api-info-title">
                        <div class="api-info-title-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="api-info-title-text">
                            <h3><?php echo htmlspecialchars($dokuman['baslik'] ?? 'API Dokümantasyonu', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <?php if (!empty($dokuman['aciklama'])): ?>
                                <p><?php echo htmlspecialchars($dokuman['aciklama'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php else: ?>
                                <p>API Dokümantasyon ve Test Platformu</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="api-info-stats">
                        <div class="api-info-stat">
                            <div class="api-info-stat-label">Versiyon</div>
                            <div class="api-info-stat-value">
                                <i class="fas fa-tag"></i>
                                <span>v<?php echo htmlspecialchars($dokuman['versiyon'] ?? '1.0.0', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                        <div class="api-info-stat">
                            <div class="api-info-stat-label">Toplam Endpoint</div>
                            <div class="api-info-stat-value">
                                <i class="fas fa-plug"></i>
                                <span><?php echo htmlspecialchars($dokuman['toplam_endpoint'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                        <?php if (!empty($dokuman['base_url'])): ?>
                            <div class="api-info-stat">
                                <div class="api-info-stat-label">Base URL</div>
                                <div class="api-info-stat-value" style="font-size: 14px; font-family: 'Courier New', monospace;">
                                    <i class="fas fa-server"></i>
                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($dokuman['base_url'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($dokuman['son_guncelleme'])): ?>
                            <div class="api-info-stat">
                                <div class="api-info-stat-label">Son Güncelleme</div>
                                <div class="api-info-stat-value">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo date('d.m.Y', strtotime($dokuman['son_guncelleme'])); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="api-info-body">
                <div class="api-info-main">
                    <?php if (!empty($dokuman['organizasyon'])): ?>
                        <div class="api-info-item">
                            <div class="api-info-item-icon primary">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="api-info-item-content">
                                <div class="api-info-item-label">Organizasyon</div>
                                <div class="api-info-item-value"><?php echo htmlspecialchars($dokuman['organizasyon'], ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($dokuman['destek_email'])): ?>
                        <div class="api-info-item">
                            <div class="api-info-item-icon info">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="api-info-item-content">
                                <div class="api-info-item-label">Destek E-posta</div>
                                <div class="api-info-item-value">
                                    <a href="mailto:<?php echo htmlspecialchars($dokuman['destek_email'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($dokuman['destek_email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($dokuman['destek_url'])): ?>
                        <div class="api-info-item">
                            <div class="api-info-item-icon success">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="api-info-item-content">
                                <div class="api-info-item-label">Destek URL</div>
                                <div class="api-info-item-value">
                                    <a href="<?php echo htmlspecialchars($dokuman['destek_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                                        <?php echo htmlspecialchars($dokuman['destek_url'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Detaylar Accordion -->
            <div class="api-info-accordion">
                <div class="api-info-accordion-header" onclick="toggleApiInfoAccordion(this)">
                    <div class="api-info-accordion-title">
                        <i class="fas fa-info-circle"></i>
                        <span>Detaylı Bilgiler</span>
                    </div>
                    <i class="fas fa-chevron-down api-info-accordion-icon"></i>
                </div>
                <div class="api-info-accordion-content">
                    <div class="api-info-accordion-body">
                        <?php if (!empty($dokuman['organizasyon'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon primary">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Organizasyon</div>
                                    <div class="api-info-item-value"><?php echo htmlspecialchars($dokuman['organizasyon'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($dokuman['lisans'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon warning">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Lisans</div>
                                    <div class="api-info-item-value"><?php echo htmlspecialchars($dokuman['lisans'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($dokuman['destek_email'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon info">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Destek E-posta</div>
                                    <div class="api-info-item-value">
                                        <a href="mailto:<?php echo htmlspecialchars($dokuman['destek_email'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($dokuman['destek_email'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($dokuman['destek_url'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon success">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Destek URL</div>
                                    <div class="api-info-item-value">
                                        <a href="<?php echo htmlspecialchars($dokuman['destek_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                                            <?php echo htmlspecialchars($dokuman['destek_url'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($dokuman['api_rate_limit'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon danger">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Rate Limit</div>
                                    <div class="api-info-item-value">
                                        <?php echo htmlspecialchars($dokuman['api_rate_limit'], ENT_QUOTES, 'UTF-8'); ?> istek /
                                        <?php echo htmlspecialchars($dokuman['api_rate_limit_saniye'] ?? 60, ENT_QUOTES, 'UTF-8'); ?> saniye
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($dokuman['api_token_timeout_dakika'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Token Timeout</div>
                                    <div class="api-info-item-value"><?php echo htmlspecialchars($dokuman['api_token_timeout_dakika'], ENT_QUOTES, 'UTF-8'); ?> dakika</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        // İzinli metodlar (Api.php'den)
                        $izinliMethodlar = defined('API_IZINLI_METHODLAR') ? API_IZINLI_METHODLAR : ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
                        if (!empty($izinliMethodlar)):
                        ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">İzinli HTTP Metodları</div>
                                    <div class="api-info-item-value">
                                        <?php echo implode(', ', $izinliMethodlar); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($dokuman['son_guncelleme'])): ?>
                            <div class="api-info-item">
                                <div class="api-info-item-icon primary">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="api-info-item-content">
                                    <div class="api-info-item-label">Son Güncelleme (Detaylı)</div>
                                    <div class="api-info-item-value"><?php echo date('d.m.Y H:i:s', strtotime($dokuman['son_guncelleme'])); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Görüntüle & İndir Panel -->
        <div class="export-panel" id="exportPanel">
            <div class="export-panel-header" onclick="toggleExportPanel()">
                <div class="export-panel-title">
                    <i class="fas fa-download"></i>
                    <span>Görüntüle & İndir</span>
                </div>
                <i class="fas fa-chevron-down export-panel-icon"></i>
            </div>
            <div class="export-panel-body">
                <?php
                $exportFormats = [
                    'Data Formats' => [
                        ['key' => 'json', 'name' => 'JSON', 'icon' => 'fa-code', 'color' => '#f6c23e'],
                        ['key' => 'xml', 'name' => 'XML', 'icon' => 'fa-file-code', 'color' => '#36b9cc'],
                        ['key' => 'yaml', 'name' => 'YAML', 'icon' => 'fa-file-alt', 'color' => '#858796'],
                        ['key' => 'markdown', 'name' => 'Markdown', 'icon' => 'fa-markdown', 'color' => '#1cc88a'],
                        ['key' => 'csv', 'name' => 'CSV', 'icon' => 'fa-file-csv', 'color' => '#4e73df'],
                        ['key' => 'text', 'name' => 'Text', 'icon' => 'fa-file-alt', 'color' => '#6c757d'],
                    ],
                    'API Specs' => [
                        ['key' => 'openapi', 'name' => 'OpenAPI', 'icon' => 'fa-file-contract', 'color' => '#1cc88a'],
                        ['key' => 'swagger', 'name' => 'Swagger', 'icon' => 'fa-file-contract', 'color' => '#85ea2d'],
                        ['key' => 'swagger2', 'name' => 'Swagger 2.0', 'icon' => 'fa-file-contract', 'color' => '#85ea2d'],
                    ],
                    'API Clients' => [
                        ['key' => 'postman', 'name' => 'Postman', 'icon' => 'fa-rocket', 'color' => '#ff6c37'],
                        ['key' => 'insomnia', 'name' => 'Insomnia', 'icon' => 'fa-moon', 'color' => '#5849be'],
                    ],
                    'Command Line' => [
                        ['key' => 'curl', 'name' => 'cURL', 'icon' => 'fa-terminal', 'color' => '#1cc88a'],
                        ['key' => 'httpie', 'name' => 'HTTPie', 'icon' => 'fa-terminal', 'color' => '#0e9aa7'],
                        ['key' => 'wget', 'name' => 'Wget', 'icon' => 'fa-download', 'color' => '#858796'],
                    ],
                    'Languages' => [
                        ['key' => 'php', 'name' => 'PHP', 'icon' => 'fa-code', 'color' => '#777bb4'],
                        ['key' => 'javascript', 'name' => 'JavaScript', 'icon' => 'fa-code', 'color' => '#f7df1e'],
                        ['key' => 'typescript', 'name' => 'TypeScript', 'icon' => 'fa-code', 'color' => '#3178c6'],
                        ['key' => 'python', 'name' => 'Python', 'icon' => 'fa-code', 'color' => '#3776ab'],
                        ['key' => 'java', 'name' => 'Java', 'icon' => 'fa-code', 'color' => '#ed8b00'],
                        ['key' => 'csharp', 'name' => 'C#', 'icon' => 'fa-code', 'color' => '#239120'],
                        ['key' => 'go', 'name' => 'Go', 'icon' => 'fa-code', 'color' => '#00add8'],
                        ['key' => 'ruby', 'name' => 'Ruby', 'icon' => 'fa-gem', 'color' => '#cc342d'],
                        ['key' => 'bash', 'name' => 'Bash', 'icon' => 'fa-terminal', 'color' => '#4eaa25'],
                        ['key' => 'powershell', 'name' => 'PowerShell', 'icon' => 'fa-windows', 'color' => '#012456'],
                    ],
                    'Libraries' => [
                        ['key' => 'axios', 'name' => 'Axios', 'icon' => 'fa-code', 'color' => '#5a29e4'],
                    ],
                ];

                $baseUrl = $dokuman['base_url'] ?? '';
                $exportBaseUrl = rtrim($baseUrl, '/') . '/dokuman';
                ?>
                <div class="export-formats-compact">
                    <?php foreach ($exportFormats as $category => $formats): ?>
                        <div class="export-category-compact">
                            <div class="export-category-title-compact"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="export-formats-list-compact">
                                <?php foreach ($formats as $format): ?>
                                    <button class="export-format-btn-compact" onclick="exportFormat('<?php echo $format['key']; ?>', '<?php echo $exportBaseUrl; ?>')" title="<?php echo htmlspecialchars($format['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="export-format-icon-compact" style="background: <?php echo $format['color']; ?>;">
                                            <i class="fas <?php echo $format['icon']; ?>"></i>
                                        </div>
                                        <span><?php echo htmlspecialchars($format['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>



        <div class="token-generator-card">
            <div class="token-generator-header">
                <i class="fas fa-key"></i><span>Token Üretici</span>
            </div>
            <div class="token-generator-body">
                <div class="token-display-group">
                    <div class="token-display empty" id="tokenDisplay">Token henüz oluşturulmadı...</div>
                    <button class="btn-copy-token" onclick="copyToken()" id="copyTokenBtn" style="display: none;">
                        <i class="fas fa-copy"></i><span>Kopyala</span>
                    </button>
                    <button class="btn-generate-token" onclick="generateToken()">
                        <i class="fas fa-magic"></i><span>Token Üret</span>
                    </button>
                </div>
                <div style="font-size: 12px; color: var(--text-secondary);">
                    <i class="fas fa-info-circle"></i> Token otomatik oluşturulacak ve panoya kopyalanacak
                </div>
            </div>
        </div>

        <div class="test-card" id="testCard">
            <div class="test-card-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-flask"></i><span>Canlı Test (API Client)</span>
                </div>
                <div class="test-card-actions">
                    <button class="action-btn" onclick="openTemplates()">
                        <i class="fas fa-bookmark"></i> Şablonlar
                    </button>
                    <button class="action-btn" onclick="saveAsTemplate()">
                        <i class="fas fa-save"></i> Şablon Kaydet
                    </button>
                </div>
            </div>
            <div class="test-card-body">
                <div class="live-test-area">
                    <!-- Base URL Input (Readonly, Üstte) -->
                    <div class="url-input-group" style="margin-bottom: 10px;">
                        <div class="url-input-wrapper" style="flex: 1;">
                            <label style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; display: block; font-weight: 600;">
                                <i class="fas fa-link"></i> Base URL
                            </label>
                            <input type="text"
                                class="url-input"
                                id="baseUrlInput"
                                readonly
                                style="background: var(--bg-primary); cursor: not-allowed; opacity: 0.8;">
                        </div>
                    </div>

                    <!-- Endpoint/Query Input (Altta) -->
                    <div class="url-input-group">
                        <div class="url-input-wrapper" style="flex: 1;">
                            <label style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; display: block; font-weight: 600;">
                                <i class="fas fa-code"></i> Endpoint & Query Parameters
                            </label>
                            <input type="text"
                                class="url-input"
                                id="apiUrl"
                                placeholder="?id=test&email=example@mail.com">
                        </div>
                        <select class="method-select" id="requestMethod">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        <button class="btn-test" onclick="testAPI()">
                            <i class="fas fa-play"></i><span>Test Et</span>
                        </button>
                    </div>

                    <div class="params-builder">
                        <div class="params-header">
                            <div class="params-header-title">
                                <i class="fas fa-sliders-h"></i>
                                <span>Query Parametreleri (Thunder Client tarzı)</span>
                            </div>
                            <button type="button" class="params-add-btn" onclick="addQueryParamRow()">
                                <i class="fas fa-plus"></i> Satır Ekle
                            </button>
                        </div>
                        <div class="params-rows" id="queryParamsContainer">
                            <!-- Satırlar JS ile eklenecek -->
                        </div>
                    </div>

                    <div class="request-tabs">
                        <button class="tab-button active" onclick="switchTab(event, 'headers')">
                            <i class="fas fa-list"></i> Headers
                        </button>
                        <button class="tab-button" onclick="switchTab(event, 'body')">
                            <i class="fas fa-code"></i> Body
                        </button>
                        <button class="tab-button" onclick="switchTab(event, 'auth')">
                            <i class="fas fa-lock"></i> Auth
                        </button>
                        <button class="tab-button" onclick="switchTab(event, 'ratelimit')">
                            <i class="fas fa-tachometer-alt"></i> Rate Limit
                        </button>
                    </div>
                    <div class="tab-content active" id="headers-tab">
                        <div class="tab-content-card">
                            <div class="panel-toolbar">
                                <div class="panel-toolbar-left">
                                    <button class="toolbar-btn" onclick="addQuickHeader('Content-Type', 'application/json')" title="Content-Type ekle">
                                        <i class="fas fa-plus"></i> Content-Type
                                    </button>
                                    <button class="toolbar-btn" onclick="addQuickHeader('Accept', 'application/json')" title="Accept ekle">
                                        <i class="fas fa-plus"></i> Accept
                                    </button>
                                    <button class="toolbar-btn" onclick="addQuickHeader('Authorization', 'Bearer')" title="Authorization ekle">
                                        <i class="fas fa-plus"></i> Authorization
                                    </button>
                                    <button class="toolbar-btn" onclick="autoSetContentType()" title="Body'ye göre otomatik Content-Type">
                                        <i class="fas fa-magic"></i> Auto Content-Type
                                    </button>
                                </div>
                                <div class="panel-toolbar-right">
                                    <button class="toolbar-btn" onclick="toggleHeadersView()" title="Görünüm değiştir">
                                        <i class="fas fa-exchange-alt"></i> <span id="headersViewMode">JSON</span>
                                    </button>
                                    <button class="toolbar-btn" onclick="clearHeaders()" title="Temizle">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="headersFormView" style="display: none;">
                                <div class="headers-form" id="headersFormContainer">
                                    <!-- Headers form buraya eklenecek -->
                                </div>
                                <button class="add-header-btn" onclick="addHeaderRow()">
                                    <i class="fas fa-plus"></i> Header Ekle
                                </button>
                            </div>
                            <div id="headersJsonView">
                                <label class="form-label">Headers (JSON)</label>
                                <textarea class="form-textarea" id="requestHeaders" placeholder='{"Content-Type": "application/json"}' oninput="validateHeadersJSON()"></textarea>
                                <div class="json-status" id="headersJSONStatus" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content" id="body-tab">
                        <div class="tab-content-card">
                            <div class="panel-toolbar">
                                <div class="panel-toolbar-left">
                                    <button class="toolbar-btn" onclick="formatJSON('requestBody')" title="JSON formatla">
                                        <i class="fas fa-indent"></i> Format
                                    </button>
                                    <button class="toolbar-btn" onclick="validateJSON('requestBody')" title="JSON doğrula">
                                        <i class="fas fa-check-circle"></i> Validate
                                    </button>
                                    <button class="toolbar-btn" onclick="insertBodyTemplate()" title="Şablon ekle">
                                        <i class="fas fa-file-code"></i> Template
                                    </button>
                                </div>
                                <div class="panel-toolbar-right">
                                    <button class="toolbar-btn" onclick="clearBody()" title="Temizle">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <label class="form-label">Request Body (JSON)</label>
                            <textarea class="form-textarea" id="requestBody" placeholder='{"key": "value"}' oninput="validateBodyJSON()"></textarea>
                            <div class="json-status" id="bodyJSONStatus" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="tab-content" id="auth-tab">
                        <div class="tab-content-card">
                            <div class="panel-toolbar">
                                <div class="panel-toolbar-left">
                                    <button class="toolbar-btn" onclick="useGeneratedToken()" title="Üretilen token'ı kullan">
                                        <i class="fas fa-key"></i> Use Generated Token
                                    </button>
                                    <button class="toolbar-btn" onclick="copyAuthToHeader()" title="Header'a kopyala">
                                        <i class="fas fa-copy"></i> Copy to Header
                                    </button>
                                </div>
                                <div class="panel-toolbar-right">
                                    <button class="toolbar-btn" onclick="clearAuth()" title="Temizle">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="auth-type-selector">
                                <button class="auth-type-btn active" onclick="setAuthType('bearer')" data-auth-type="bearer">Bearer Token</button>
                                <button class="auth-type-btn" onclick="setAuthType('basic')" data-auth-type="basic">Basic Auth</button>
                                <button class="auth-type-btn" onclick="setAuthType('apikey')" data-auth-type="apikey">API Key</button>
                            </div>
                            <div id="authBearerView" class="auth-input-group">
                                <label class="form-label">Bearer Token</label>
                                <textarea class="form-textarea" id="authToken" placeholder="Bearer token_here veya sadece token" style="min-height: 80px;"></textarea>
                            </div>
                            <div id="authBasicView" class="auth-input-group" style="display: none;">
                                <label class="form-label">Username</label>
                                <input type="text" class="auth-input" id="authBasicUser" placeholder="username">
                                <label class="form-label">Password</label>
                                <input type="password" class="auth-input" id="authBasicPass" placeholder="password">
                            </div>
                            <div id="authApikeyView" class="auth-input-group" style="display: none;">
                                <label class="form-label">API Key</label>
                                <input type="text" class="auth-input" id="authApikeyKey" placeholder="X-API-Key veya benzeri">
                                <label class="form-label">Value</label>
                                <input type="text" class="auth-input" id="authApikeyValue" placeholder="api_key_value">
                            </div>
                        </div>
                    </div>
                    <div class="tab-content" id="ratelimit-tab">
                        <div class="tab-content-card">
                            <div class="panel-toolbar">
                                <div class="panel-toolbar-left">
                                    <button class="toolbar-btn" onclick="updateRateLimitInfo()" title="Yenile">
                                        <i class="fas fa-sync-alt"></i> Yenile
                                    </button>
                                </div>
                            </div>
                            <div class="rate-limit-info" style="grid-template-columns: 1fr; gap: 16px;">
                                <div class="rate-limit-item">
                                    <div class="rate-limit-label">İstek Limiti</div>
                                    <div class="rate-limit-value">
                                        <i class="fas fa-bolt"></i>
                                        <span><?php echo htmlspecialchars($dokuman['api_rate_limit'] ?? 60, ENT_QUOTES, 'UTF-8'); ?> istek</span>
                                    </div>
                                </div>
                                <div class="rate-limit-item">
                                    <div class="rate-limit-label">Zaman Dilimi</div>
                                    <div class="rate-limit-value">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo htmlspecialchars($dokuman['api_rate_limit_saniye'] ?? 60, ENT_QUOTES, 'UTF-8'); ?> saniye</span>
                                    </div>
                                </div>
                                <div class="rate-limit-item">
                                    <div class="rate-limit-label">Kalan İstek</div>
                                    <div class="rate-limit-value">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span id="remainingRequests">-</span>
                                    </div>
                                    <div class="rate-limit-progress">
                                        <div class="rate-limit-progress-bar" id="rateLimitProgress" style="width: 100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="response-section">
                    <div class="response-box">
                        <div class="response-header-inner">
                            <div class="response-label">
                                <i class="fas fa-code"></i> RESPONSE
                            </div>
                            <div class="response-controls">
                                <div class="status-info" id="statusInfo"></div>
                                <button class="response-btn" onclick="openErrorCodesModal()" title="Hata Kodları">
                                    <i class="fas fa-exclamation-triangle"></i> Hata Kodları
                                </button>
                                <button class="response-btn" onclick="formatResponse()" title="Format JSON">
                                    <i class="fas fa-indent"></i> Format
                                </button>
                                <button class="response-btn" onclick="toggleSearch()" title="Ara">
                                    <i class="fas fa-search"></i> Ara
                                </button>
                                <button class="response-btn" onclick="copyResponse()" title="Kopyala">
                                    <i class="fas fa-copy"></i> Kopyala
                                </button>
                                <button class="response-btn" onclick="downloadResponse()" title="İndir">
                                    <i class="fas fa-download"></i> İndir
                                </button>
                            </div>
                        </div>
                        <div class="response-area" id="responseArea">
                            <div class="search-box" id="searchBox">
                                <input type="text" class="search-input" id="searchInput" placeholder="Response içinde ara..." oninput="searchInResponse()">
                            </div>
                            <div id="responseContent">Henüz test yapılmadı...</div>
                        </div>
                        <div style="padding: 10px 14px; border-top: 1px solid var(--border-color); display: flex; gap: 10px; justify-content: center; background: linear-gradient(to bottom, rgba(255,255,255,0.02), rgba(255,255,255,0));">
                            <button class="response-action-btn" onclick="document.getElementById('testCard').scrollIntoView({behavior: 'smooth', block: 'start'});" style="background: linear-gradient(135deg, var(--accent-info) 0%, #4e73df 100%); box-shadow: 0 3px 8px rgba(78, 115, 223, 0.3);">
                                <i class="fas fa-arrow-up" style="font-size: 11px;"></i><span>Canlı Test'e Git</span>
                            </button>
                            <button class="response-action-btn" onclick="testAPI()" style="background: linear-gradient(135deg, var(--accent-success) 0%, #17a673 100%); box-shadow: 0 3px 8px rgba(28, 200, 138, 0.3);">
                                <i class="fas fa-play" style="font-size: 11px;"></i><span>Test Et</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- İstatistikler Bölümü -->
                <div class="analytics-section" id="analyticsSection" style="margin-top: 30px;">
                    <div class="analytics-header">
                        <h3><i class="fas fa-chart-line"></i> İstatistikler ve Analitik</h3>
                        <button class="response-btn" onclick="loadAnalytics()" title="Yenile">
                            <i class="fas fa-sync-alt"></i> Yenile
                        </button>
                    </div>
                    <div id="analyticsContent">
                        <!-- İstatistikler buraya yüklenecek -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Endpoint Cards - Dinamik Oluşturuluyor -->
        <?php
        $endpoints = $dokuman['endpoints'] ?? [];
        $baseApiUrl = ($dokuman['base_url'] ?? '') . '/api.php';
        $firstCard = false; // Başlangıçta hiçbir kart aktif değil, sadece seçilen gösterilir

        foreach ($endpoints as $kategoriAdi => $kategoriEndpoints):
            if (empty($kategoriEndpoints) || !is_array($kategoriEndpoints)) continue;

            foreach ($kategoriEndpoints as $endpoint):
                $endpointId = $endpoint['id'] ?? '';
                // Endpoint açıklaması (servisler array'inden gelen)
                $endpointAciklama = $endpoint['endpoint_aciklama'] ?? $endpoint['aciklama'] ?? $endpointId;
                // Endpoint başlığı (öncelik: endpoint_aciklama > endpointId formatlanmış)
                $endpointIsim = !empty($endpoint['endpoint_aciklama'])
                    ? $endpoint['endpoint_aciklama']
                    : ucwords(str_replace('_', ' ', $endpointId));
                $ozellikler = $endpoint['ozellikler'] ?? [];

                // Ozellikler() yapısı: ['endpoint_id' => ['GET' => true, 'POST' => true, ...]]
                // Eğer endpoint ID'sine göre özellikler varsa onu kullan, yoksa direkt özellikleri kullan
                $endpointOzellikleri = [];
                if (isset($ozellikler[$endpointId]) && is_array($ozellikler[$endpointId])) {
                    // Yeni yapı: endpoint ID'sine göre özellikler
                    $endpointOzellikleri = $ozellikler[$endpointId];
                } else {
                    // Eski yapı: direkt özellikler (backward compatibility)
                    $endpointOzellikleri = $ozellikler;
                }

                // İzinli metodları bul
                $izinliMetodlar = [];
                foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $metod) {
                    if (isset($endpointOzellikleri[$metod]) && $endpointOzellikleri[$metod] === true) {
                        $izinliMetodlar[] = $metod;
                    }
                }
                $ilkMetod = !empty($izinliMetodlar) ? $izinliMetodlar[0] : 'GET';
                $ilkMetodLower = strtolower($ilkMetod);

                // Token gereksinimi
                $tokenGerekli = isset($endpointOzellikleri['TOKEN']) ? $endpointOzellikleri['TOKEN'] : true;

                // API URL'i oluştur (GET için query string, POST için body)
                $apiUrl = $baseApiUrl . '?id=' . urlencode($endpointId);

                // Örnek request body (POST/PUT için)
                $ornekBody = json_encode(['id' => $endpointId], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                if ($tokenGerekli) {
                    $ornekBodyObj = json_decode($ornekBody, true);
                    $ornekBodyObj['token'] = 'YOUR_TOKEN_HERE';
                    $ornekBody = json_encode($ornekBodyObj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }

                // Örnek response
                $ornekResponse = json_encode([
                    'durum' => 'basarili',
                    'mesaj' => 'İşlem başarılı',
                    'veri' => ['endpoint' => $endpointId, 'zaman' => date('Y-m-d H:i:s')]
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        ?>
                <?php
                // Test body'yi base64 encode et (JSON özel karakterlerinden kaçınmak için)
                $testBody = ($ilkMetod === 'GET') ? '' : $ornekBody;
                $testBodyEncoded = base64_encode($testBody);
                ?>
                <div class="endpoint-card <?php echo $firstCard ? 'active' : ''; ?>"
                    id="card-<?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?>"
                    data-endpoint-id="<?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?>"
                    data-test-query="<?php echo htmlspecialchars('?id=' . urlencode($endpointId), ENT_QUOTES, 'UTF-8'); ?>"
                    data-test-method="<?php echo htmlspecialchars($ilkMetod, ENT_QUOTES, 'UTF-8'); ?>"
                    data-test-body="<?php echo htmlspecialchars($testBodyEncoded, ENT_QUOTES, 'UTF-8'); ?>">

                    <!-- Modern Header -->
                    <div class="endpoint-card-header-modern">
                        <div class="endpoint-title-group">
                            <span class="method-badge method-<?php echo htmlspecialchars($ilkMetodLower, ENT_QUOTES, 'UTF-8'); ?>" style="flex-shrink: 0;"><?php echo htmlspecialchars($ilkMetod, ENT_QUOTES, 'UTF-8'); ?></span>
                            <h3><?php echo htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8'); ?></h3>
                        </div>
                        <div class="endpoint-meta">
                            <?php if (count($izinliMetodlar) > 1): ?>
                                <?php foreach (array_slice($izinliMetodlar, 1) as $metod): ?>
                                    <span class="method-badge method-<?php echo htmlspecialchars(strtolower($metod), ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 10px; padding: 4px 10px; opacity: 0.85;"><?php echo htmlspecialchars($metod, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if ($tokenGerekli): ?>
                                <span style="padding: 6px 14px; background: linear-gradient(135deg, #f6c23e 0%, #f39c12 100%); color: white; border-radius: 6px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 6px rgba(243, 156, 18, 0.3); display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-shield-alt" style="font-size: 10px;"></i> Token Gerekli
                                </span>
                            <?php else: ?>
                                <span style="padding: 6px 14px; background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: white; border-radius: 6px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 6px rgba(28, 200, 138, 0.3); display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-unlock" style="font-size: 10px;"></i> Token'e Gerek Yok
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="endpoint-description">
                        <?php echo htmlspecialchars($endpointAciklama, ENT_QUOTES, 'UTF-8'); ?>
                    </div>

                    <!-- Details Section -->
                    <div class="endpoint-details">
                        <!-- URL -->
                        <div class="detail-section">
                            <div class="detail-label">
                                <i class="fas fa-link"></i> Endpoint URL
                            </div>
                            <div class="url-display">
                                <?php echo htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>

                        <!-- Request Example -->
                        <div class="detail-section">
                            <div class="detail-label">
                                <i class="fas fa-code"></i> Request Example
                            </div>
                            <div class="request-example">
                                <?php if ($ilkMetod === 'GET'): ?>
                                    <div>
                                        <span class="method-tag" style="background: rgba(28, 200, 138, 0.15); color: var(--accent-success);">GET</span>
                                        <span style="color: var(--text-primary);"><?php echo htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php if ($tokenGerekli): ?>
                                            <br><span style="color: var(--accent-warning); font-size: 11px; margin-top: 8px; display: block;">Authorization: Bearer YOUR_TOKEN_HERE</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div>
                                        <span class="method-tag" style="background: rgba(78, 115, 223, 0.15); color: var(--accent-primary);"><?php echo htmlspecialchars($ilkMetod, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span style="color: var(--text-primary);"><?php echo htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <br><span style="color: var(--accent-info); font-size: 11px; margin-top: 8px; display: block;">Content-Type: application/json</span>
                                        <?php if ($tokenGerekli): ?>
                                            <br><span style="color: var(--accent-warning); font-size: 11px; margin-top: 4px; display: block;">Authorization: Bearer YOUR_TOKEN_HERE</span>
                                        <?php endif; ?>
                                        <details style="margin-top: 12px;">
                                            <summary style="cursor: pointer; font-size: 11px; color: var(--text-secondary); font-weight: 600; padding: 8px 12px; background: rgba(0,0,0,0.02); border-radius: 4px; border: 1px solid var(--border-color); list-style: none; display: flex; align-items: center; justify-content: space-between;">
                                                <span><i class="fas fa-chevron-right" style="font-size: 9px; margin-right: 6px; transition: transform 0.2s;"></i> Request Body</span>
                                            </summary>
                                            <div class="request-body-code"><?php echo htmlspecialchars($ornekBody, ENT_QUOTES, 'UTF-8'); ?></div>
                                        </details>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="endpoint-actions">
                        <?php
                        $testQuery = '?id=' . urlencode($endpointId);
                        $testBody = ($ilkMetod === 'GET') ? '' : $ornekBody;
                        $testBodyEncoded = base64_encode($testBody);
                        $apiUrlEscaped = htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8');
                        $endpointIdEscaped = htmlspecialchars($endpointId, ENT_QUOTES, 'UTF-8');
                        $methodEscaped = htmlspecialchars($ilkMetod, ENT_QUOTES, 'UTF-8');
                        ?>
                        <button class="action-button primary" onclick="fillTestAreaFromBase64('<?php echo htmlspecialchars($testQuery, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $methodEscaped; ?>', '<?php echo htmlspecialchars($testBodyEncoded, ENT_QUOTES, 'UTF-8'); ?>')" title="Test alanına yükle">
                            <i class="fas fa-upload"></i> Test Alanına Yükle
                        </button>
                        <button class="action-button success" onclick="quickTestFromBase64('<?php echo htmlspecialchars($testQuery, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $methodEscaped; ?>', '<?php echo htmlspecialchars($testBodyEncoded, ENT_QUOTES, 'UTF-8'); ?>')" title="Hızlı test et">
                            <i class="fas fa-bolt"></i> Anlık Test
                        </button>
                        <button class="action-button info" onclick="copyEndpointUrl('<?php echo $apiUrlEscaped; ?>')" title="URL'yi kopyala">
                            <i class="fas fa-link"></i> URL Kopyala
                        </button>
                        <button class="action-button secondary" onclick="copyCurlCommand('<?php echo $apiUrlEscaped; ?>', '<?php echo $methodEscaped; ?>', '<?php echo htmlspecialchars($testBodyEncoded, ENT_QUOTES, 'UTF-8'); ?>')" title="cURL komutunu kopyala">
                            <i class="fas fa-terminal"></i> cURL Kopyala
                        </button>
                        <button class="action-button warning" onclick="copyEndpointJson('<?php echo $endpointIdEscaped; ?>', '<?php echo $apiUrlEscaped; ?>', '<?php echo $methodEscaped; ?>')" title="Endpoint JSON'unu kopyala">
                            <i class="fas fa-code"></i> JSON Kopyala
                        </button>
                        <button class="action-button secondary" onclick="saveEndpointAsTemplate('<?php echo htmlspecialchars(addslashes($endpointIsim), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($testQuery, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $methodEscaped; ?>', <?php echo !empty($testBodyEscaped) ? "'" . addslashes($testBodyEscaped) . "'" : "''"; ?>)" title="Şablon olarak kaydet">
                            <i class="fas fa-bookmark"></i> Şablon Kaydet
                        </button>
                    </div>
                </div>
            <?php
                $firstCard = false; // İlk kartı işaretledikten sonra false yap
            endforeach;
        endforeach;

        if (empty($endpoints)):
            ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
                <h3 style="margin-bottom: 10px; color: var(--text-primary);">Henüz endpoint bulunamadı</h3>
                <p style="font-size: 14px;">API endpoint'leri App/Api klasörüne ekleyerek dokümantasyona dahil edebilirsiniz.</p>
            </div>
        <?php endif; ?>

        <!-- Request Chart Section -->
        <div class="chart-section">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> Son 10 İstek Grafiği</h3>
                <button class="response-btn" onclick="refreshChart()" title="Yenile">
                    <i class="fas fa-sync-alt"></i> Yenile
                </button>
            </div>
            <div class="chart-container">
                <canvas id="requestChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Templates Modal -->
    <div class="modal-overlay" id="templatesModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-bookmark"></i> Request Şablonları</h3>
                <button class="modal-close" onclick="closeTemplates()">×</button>
            </div>
            <div class="modal-body" id="templatesBody">
                <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p>Henüz şablon yok. İlk şablonunuzu kaydedin!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div class="modal-overlay" id="historyModal">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-history"></i> İstek Geçmişi</h3>
                <button class="modal-close" onclick="closeHistory()">×</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text"
                            id="historySearch"
                            placeholder="Geçmişte ara..."
                            style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px; width: 200px;"
                            oninput="filterHistory()">
                        <select id="historyMethodFilter" onchange="filterHistory()" style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px;">
                            <option value="">Tüm Methodlar</option>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="response-btn" onclick="exportHistory()" style="margin: 0;">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="response-btn" onclick="clearHistory()" style="margin: 0;">
                            <i class="fas fa-trash"></i> Temizle
                        </button>
                    </div>
                </div>
                <div id="historyBody">
                    <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3; margin-bottom: 10px;"></i>
                        <p>Henüz test yapılmadı. İlk testinizi yapın!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Detail Modal -->
    <div class="modal-overlay" id="historyDetailModal">
        <div class="modal-content" style="max-width: 1000px; max-height: 90vh;">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-info-circle"></i> İstek Detayları</h3>
                <button class="modal-close" onclick="closeHistoryDetail()">×</button>
            </div>
            <div class="modal-body" id="historyDetailBody">
                <!-- Detaylar buraya yüklenecek -->
            </div>
        </div>
    </div>

    <!-- Error Codes Modal -->
    <div class="modal-overlay" id="errorCodesModal" onclick="if(event.target === this) closeErrorCodesModal()">
        <div class="modal-content" style="max-width: 1200px; max-height: 90vh;" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Hata Kodları</h3>
                <button class="modal-close" onclick="closeErrorCodesModal()">×</button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="color: var(--text-secondary); margin-bottom: 20px;">API'den dönebilecek tüm hata kodları ve açıklamaları</p>
                <div class="error-codes-grid">
                    <?php
                    // Mesaj.php'den hata kodlarını al
                    $durumlar = [
                        'basarili'      => [200, 'Başarılı'],
                        'bilgi'         => [202, 'Bilgi'],
                        'sessiz'        => [204, ''],
                        'gecersizIstek' => [400, 'Geçersiz İstek'],
                        'oturum'        => [401, 'Oturum Hatası'],
                        'yetki'         => [403, 'Yetki Hatası'],
                        'hata'          => [404, 'Hata'],
                        'izinYok'       => [405, 'İzin Verilmeyen Metot'],
                        'zamanAsimi'    => [408, 'İstek Zaman Aşımı'],
                        'cakisma'       => [409, 'Veri Çakışması'],
                        'kayipKaynak'   => [410, 'Kaynak Kaldırılmış'],
                        'fazlaBuyuk'    => [413, 'Çok Büyük Veri'],
                        'debug'         => [418, 'Debug Bilgisi'],
                        'validasyon'    => [422, 'Geçersiz Veri'],
                        'limit'         => [429, 'Çok Fazla İstek'],
                        'dikkat'        => [500, 'Dikkat'],
                        'agGecidi'      => [502, 'Ağ Geçidi Hatası'],
                        'servis'        => [503, 'Servis Kullanılamıyor'],
                        'agZamanAsimi'  => [504, 'Ağ Geçidi Zaman Aşımı']
                    ];

                    // Açıklamalar (Mesaj.php'deki yorumlardan)
                    $aciklamalar = [
                        'basarili'      => 'İşlem başarıyla tamamlandı',
                        'bilgi'         => 'Bilgilendirme mesajı',
                        'sessiz'        => 'İşlem başarılı ancak içerik döndürülmedi',
                        'gecersizIstek' => 'Gönderilen istek formatı geçersiz veya eksik parametreler var',
                        'oturum'        => 'Token eksik, geçersiz veya süresi dolmuş',
                        'yetki'         => 'Bu işlem için yetkiniz bulunmuyor',
                        'hata'          => 'İstenen kaynak bulunamadı veya genel hata',
                        'izinYok'       => 'Bu endpoint için kullanılan HTTP metodu izin verilmiyor',
                        'zamanAsimi'    => 'İstek zaman aşımına uğradı',
                        'cakisma'       => 'Gönderilen veri mevcut verilerle çakışıyor',
                        'kayipKaynak'   => 'İstenen kaynak kalıcı olarak kaldırılmış',
                        'fazlaBuyuk'    => 'Gönderilen veri boyutu limiti aşıyor',
                        'debug'         => 'Geliştirici modunda debug bilgisi',
                        'validasyon'    => 'Gönderilen veri validasyon kurallarını geçemedi',
                        'limit'         => 'Rate limit aşıldı, lütfen bekleyin',
                        'dikkat'        => 'Sunucu tarafında bir hata oluştu',
                        'agGecidi'      => 'Ağ geçidi veya proxy sunucusu hata verdi',
                        'servis'        => 'Servis geçici olarak kullanılamıyor',
                        'agZamanAsimi'  => 'Ağ geçidi zaman aşımına uğradı'
                    ];

                    foreach ($durumlar as $key => $durum):
                        [$code, $title] = $durum;
                        $desc = $aciklamalar[$key] ?? '';
                        // Sessiz için özel başlık
                        if ($key === 'sessiz' && empty($title)) {
                            $title = 'Sessiz Başarı';
                        }
                    ?>
                        <div class="error-code-item">
                            <div class="error-code-header">
                                <div class="error-code-badge">
                                    <span class="error-code-number"><?php echo $code; ?></span>
                                    <span class="error-code-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                            <div class="error-code-desc"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>



    <script>
        // PHP tarafında üretilen API dokümantasyon verisini JS içinde kullan
        const apiDoc = <?php echo json_encode($dokuman, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;

        let currentResponse = '';
        let generatedToken = '';
        let templates = JSON.parse(localStorage.getItem('apiTemplates') || '[]');
        let history = JSON.parse(localStorage.getItem('apiHistory') || '[]');
        let stats = JSON.parse(localStorage.getItem('apiStats') || '{"success": 0, "error": 0, "total": 0, "times": []}');
        let filteredHistory = [];
        let favorites = JSON.parse(localStorage.getItem('apiFavorites') || '[]');
        let recentEndpoints = JSON.parse(localStorage.getItem('apiRecent') || '[]');
        // Base URL: doküman URL'sinden hesaplanan proje kökü + /api
        let baseUrl = (apiDoc?.base_url ? apiDoc.base_url + '/api' : '');

        // Environment Variables sistemi
        function replaceVariables(text) {
            const baseUrlInput = document.getElementById('baseUrlInput');
            const baseUrlValue = baseUrlInput ? baseUrlInput.value : baseUrl;
            return text.replace(/{{baseUrl}}/g, baseUrlValue);
        }

        // Base URL sabit, kullanıcı tarafından değiştirilemez

        // Load base URL & doküman istatistikleri
        document.addEventListener('DOMContentLoaded', function() {
            // Base URL'i üstteki readonly input'a doldur
            const baseUrlInput = document.getElementById('baseUrlInput');
            if (baseUrlInput) {
                baseUrlInput.value = baseUrl;
            }

            // Sidebar'daki baseUrl'i de doldur (eski kod uyumluluğu için)
            const sidebarBaseUrl = document.getElementById('baseUrl');
            if (sidebarBaseUrl) sidebarBaseUrl.value = baseUrl;

            // Doküman istatistikleri (endpoint & kategori sayısı)
            try {
                if (typeof apiDoc === 'object' && apiDoc !== null) {
                    const endpointsByCat = apiDoc.endpoints || {};
                    const categories = Object.keys(endpointsByCat);
                    const totalEndpoints = categories.reduce((sum, cat) => {
                        const list = endpointsByCat[cat] || [];
                        return sum + (Array.isArray(list) ? list.length : 0);
                    }, 0);

                    const catEl = document.getElementById('totalCategories');
                    const epEl = document.getElementById('totalEndpoints');
                    if (catEl) catEl.textContent = categories.length;
                    if (epEl) epEl.textContent = totalEndpoints;
                }
            } catch (e) {
                console.error('API doküman istatistikleri hesaplanırken hata:', e);
            }


            updateHistoryCount();
            updateFavoriteCount();
            updateRecentCount();

            // Headers form'u başlat
            if (document.getElementById('headersFormContainer')) {
                addHeaderRow();
            }

            // Rate limit güncelleme
            updateRateLimitInfo();

            // Query parametre satırlarını başlat (en az bir boş satır)
            try {
                const container = document.getElementById('queryParamsContainer');
                if (container && container.children.length === 0) {
                    addQueryParamRow('', '', true);
                }
                // Eğer URL'de query string varsa senkronize et
                const apiUrl = document.getElementById('apiUrl').value;
                if (apiUrl && apiUrl.includes('?')) {
                    syncQueryParamsFromUrl();
                }
            } catch (e) {
                console.error('Query params başlatma hatası:', e);
            }

            // ESC tuşu ile modal'ları kapat
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const errorModal = document.getElementById('errorCodesModal');
                    if (errorModal && errorModal.classList.contains('show')) {
                        closeErrorCodesModal();
                    }
                }
            });
        });


        // ========== RATE LIMIT INFO ==========
        function updateRateLimitInfo() {
            const remainingEl = document.getElementById('remainingRequests');
            const progressBar = document.getElementById('rateLimitProgress');

            if (!remainingEl || !progressBar) return;

            // Rate limit bilgilerini al
            const rateLimit = <?php echo htmlspecialchars($dokuman['api_rate_limit'] ?? 60, ENT_QUOTES, 'UTF-8'); ?>;
            const rateLimitSeconds = <?php echo htmlspecialchars($dokuman['api_rate_limit_saniye'] ?? 60, ENT_QUOTES, 'UTF-8'); ?>;

            // Son 1 dakikadaki istek sayısını hesapla (localStorage'dan)
            const now = Date.now();
            const oneMinuteAgo = now - (rateLimitSeconds * 1000);
            const recentRequests = history.filter(h => {
                if (h.timestampMs) {
                    return h.timestampMs > oneMinuteAgo;
                }
                try {
                    const timestamp = new Date(h.timestamp).getTime();
                    return !isNaN(timestamp) && timestamp > oneMinuteAgo;
                } catch (e) {
                    return false;
                }
            });

            const usedRequests = recentRequests.length;
            const remaining = Math.max(0, rateLimit - usedRequests);
            const percentage = (usedRequests / rateLimit) * 100;

            remainingEl.textContent = remaining;
            progressBar.style.width = percentage + '%';

            // Renk değiştir (yeşil -> sarı -> kırmızı)
            if (percentage < 50) {
                progressBar.style.background = 'linear-gradient(90deg, var(--accent-success), #2db870)';
            } else if (percentage < 80) {
                progressBar.style.background = 'linear-gradient(90deg, var(--accent-warning), #e89a2e)';
            } else {
                progressBar.style.background = 'linear-gradient(90deg, var(--accent-danger), #e63946)';
            }
        }

        // Rate limit'i her istek sonrası güncelle
        function addToHistory(url, method, status, duration, body) {
            const now = Date.now();
            const historyItem = {
                id: now,
                url: url,
                method: method,
                status: status,
                duration: duration,
                body: body,
                timestamp: new Date().toLocaleString('tr-TR'),
                timestampMs: now // Hesaplamalar için epoch time
            };

            history.unshift(historyItem);

            // Son 50 kaydı tut
            if (history.length > 50) {
                history = history.slice(0, 50);
            }

            localStorage.setItem('apiHistory', JSON.stringify(history));
            updateHistoryCount();
            updateRateLimitInfo(); // Rate limit bilgisini güncelle

            // Grafiği ve istatistikleri güncelle
            setTimeout(() => {
                initChart();
                loadAnalytics();
            }, 100);
        }

        function updateHistoryCount() {
            document.getElementById('historyCount').textContent = history.length;
        }

        function updateFavoriteCount() {
            const count = favorites.length;
            const el = document.getElementById('favoriteCount');
            if (el) el.textContent = count;
        }

        function updateRecentCount() {
            const count = recentEndpoints.length;
            const el = document.getElementById('recentCount');
            if (el) el.textContent = count;
        }


        function openHistory() {
            loadHistory();
            document.getElementById('historyModal').classList.add('show');
        }

        function closeHistory() {
            document.getElementById('historyModal').classList.remove('show');
        }

        function loadHistory() {
            filteredHistory = [...history];
            renderHistory();
        }

        function renderHistory() {
            const body = document.getElementById('historyBody');

            if (filteredHistory.length === 0) {
                body.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3; margin-bottom: 10px;"></i>
                        <p>Henüz test yapılmadı. İlk testinizi yapın!</p>
                    </div>
                `;
                return;
            }

            body.innerHTML = filteredHistory.map(h => `
                <div class="history-item" onclick="showHistoryDetail(${h.id})">
                    <div class="history-header">
                        <div class="history-url" style="flex: 1; word-break: break-all;">${h.url}</div>
                        <div class="history-actions">
                            <button class="history-btn" onclick="event.stopPropagation(); loadFromHistory(${h.id})" title="Tekrar Test Et">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button class="history-btn" onclick="event.stopPropagation(); showHistoryDetail(${h.id})" title="Detayları Gör">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button class="history-btn delete" onclick="event.stopPropagation(); deleteHistory(${h.id})" title="Sil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="history-time">${h.timestamp}</div>
                    <div class="history-meta">
                        <span class="method-badge method-${h.method.toLowerCase()}">${h.method}</span>
                        <span class="history-status ${h.status >= 200 && h.status < 300 ? 'success' : 'error'}">${h.status}</span>
                        <span style="color: var(--text-secondary);">${h.duration}ms</span>
                    </div>
                </div>
            `).join('');
        }

        function filterHistory() {
            const searchTerm = document.getElementById('historySearch').value.toLowerCase();
            const methodFilter = document.getElementById('historyMethodFilter').value;

            filteredHistory = history.filter(h => {
                const matchesSearch = !searchTerm ||
                    h.url.toLowerCase().includes(searchTerm) ||
                    h.method.toLowerCase().includes(searchTerm) ||
                    (h.body && h.body.toLowerCase().includes(searchTerm));

                const matchesMethod = !methodFilter || h.method === methodFilter;

                return matchesSearch && matchesMethod;
            });

            renderHistory();
        }

        function showHistoryDetail(id) {
            const item = history.find(h => h.id === id);
            if (!item) return;

            const detailBody = document.getElementById('historyDetailBody');
            detailBody.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: var(--text-primary);">İstek Bilgileri</h4>
                    <div style="background: var(--bg-primary); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="margin-bottom: 10px;">
                            <strong>URL:</strong> <code style="background: var(--bg-secondary); padding: 4px 8px; border-radius: 4px;">${item.url}</code>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Method:</strong> <span class="method-badge method-${item.method.toLowerCase()}">${item.method}</span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Status:</strong> <span class="history-status ${item.status >= 200 && item.status < 300 ? 'success' : 'error'}">${item.status}</span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Süre:</strong> ${item.duration}ms
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Tarih:</strong> ${item.timestamp}
                        </div>
                        ${item.body ? `
                        <div style="margin-top: 15px;">
                            <strong>Request Body:</strong>
                            <pre style="background: var(--bg-secondary); padding: 12px; border-radius: 6px; overflow-x: auto; margin-top: 8px; font-size: 12px;">${JSON.stringify(JSON.parse(item.body || '{}'), null, 2)}</pre>
                        </div>
                        ` : ''}
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="action-button primary" onclick="loadFromHistory(${item.id}); closeHistoryDetail();">
                        <i class="fas fa-redo"></i> Tekrar Test Et
                    </button>
                    <button class="action-button secondary" onclick="copyHistoryItem(${item.id})">
                        <i class="fas fa-copy"></i> Kopyala
                    </button>
                </div>
            `;

            document.getElementById('historyDetailModal').classList.add('show');
        }

        function closeHistoryDetail() {
            document.getElementById('historyDetailModal').classList.remove('show');
        }

        function openErrorCodesModal() {
            document.getElementById('errorCodesModal').classList.add('show');
        }

        function closeErrorCodesModal() {
            document.getElementById('errorCodesModal').classList.remove('show');
        }

        function copyHistoryItem(id) {
            const item = history.find(h => h.id === id);
            if (!item) return;

            const data = {
                url: item.url,
                method: item.method,
                body: item.body,
                timestamp: item.timestamp
            };

            navigator.clipboard.writeText(JSON.stringify(data, null, 2)).then(() => {
                showNotification('Kopyalandı!', 'İstek bilgileri panoya kopyalandı', 'success');
            });
        }

        function exportHistory() {
            const dataStr = JSON.stringify(history, null, 2);
            const blob = new Blob([dataStr], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `api_history_${Date.now()}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            showNotification('İndirildi!', 'Geçmiş JSON olarak indirildi', 'success');
        }

        function loadFromHistory(id) {
            const item = history.find(h => h.id === id);
            if (!item) return;

            document.getElementById('apiUrl').value = item.url;
            document.getElementById('requestMethod').value = item.method;
            document.getElementById('requestBody').value = item.body || '';

            closeHistory();

            const testCard = document.getElementById('testCard');
            testCard.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            showNotification('Geçmişten Yüklendi!', 'İsteği tekrar test edebilirsiniz', 'success');
        }

        function deleteHistory(id) {
            if (!confirm('Bu kaydı silmek istediğinizden emin misiniz?')) return;

            history = history.filter(h => h.id !== id);
            localStorage.setItem('apiHistory', JSON.stringify(history));
            loadHistory();
            updateHistoryCount();
            showNotification('Silindi', 'Geçmiş kaydı silindi', 'success');
        }

        function clearHistory() {
            if (!confirm('Tüm geçmişi silmek istediğinizden emin misiniz?')) return;

            history = [];
            localStorage.setItem('apiHistory', JSON.stringify(history));
            loadHistory();
            updateHistoryCount();
            showNotification('Temizlendi', 'Tüm geçmiş silindi', 'success');
        }

        function toggleApiInfoAccordion(element) {
            const content = element.nextElementSibling;
            const isActive = element.classList.contains('active');

            if (isActive) {
                element.classList.remove('active');
                content.classList.remove('active');
            } else {
                element.classList.add('active');
                content.classList.add('active');
            }
        }

        function toggleExportPanel() {
            const panel = document.getElementById('exportPanel');
            if (panel) {
                panel.classList.toggle('active');
            }
        }

        function toggleAccordion(element) {
            const content = element.nextElementSibling;
            const isActive = element.classList.contains('active');

            // Tüm accordion'ları kapat
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.classList.remove('active');
            });
            document.querySelectorAll('.accordion-content').forEach(content => {
                content.classList.remove('active');
            });

            // Eğer tıklanan menü kapalıysa, sadece onu aç
            if (!isActive) {
                element.classList.add('active');
                content.classList.add('active');
            }
        }

        function selectEndpoint(element, cardId) {
            if (window.innerWidth <= 768) toggleSidebar();

            // Son kullanılanlara ekle
            addToRecent(cardId);

            document.querySelectorAll('.endpoint-item').forEach(i => i.classList.remove('active'));
            element.classList.add('active');

            document.querySelectorAll('.endpoint-card').forEach(card => card.classList.remove('active'));

            const targetCard = document.getElementById('card-' + cardId);
            if (targetCard) {
                targetCard.classList.add('active');

                // Scroll pozisyonunu ayarla (üstten boşluk bırak)
                const yOffset = -100; // Üstten 100px boşluk
                const y = targetCard.getBoundingClientRect().top + window.pageYOffset + yOffset;

                window.scrollTo({
                    top: y,
                    behavior: 'smooth'
                });
            }
        }

        function fillTestArea(url, method, body) {
            url = replaceVariables(url);

            // URL'den sadece query string kısmını çıkar
            let queryString = '';
            if (url.includes('?')) {
                queryString = '?' + url.split('?')[1];
            } else if (url.startsWith('?')) {
                queryString = url;
            }

            document.getElementById('apiUrl').value = queryString;
            document.getElementById('requestMethod').value = method;
            document.getElementById('requestBody').value = body;

            const testCard = document.getElementById('testCard');
            testCard.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // URL'den Thunder Client tarzı query parametrelerini güncelle
            try {
                syncQueryParamsFromUrl();
            } catch (e) {
                console.error('Query params senkronizasyon hatası:', e);
            }

            showNotification('Test Alanı Hazır!', 'Parametreleri düzenleyip test edebilirsiniz', 'success');
        }

        function fillTestAreaFromBase64(url, method, bodyBase64) {
            let body = '';
            if (bodyBase64) {
                try {
                    body = atob(bodyBase64);
                } catch (e) {
                    console.error('Body decode hatası:', e);
                    body = '';
                }
            }
            fillTestArea(url, method, body);
        }

        function quickTest(url, method, body) {
            url = replaceVariables(url);

            // URL'den sadece query string kısmını çıkar
            let queryString = '';
            if (url.includes('?')) {
                queryString = '?' + url.split('?')[1];
            } else if (url.startsWith('?')) {
                queryString = url;
            }

            document.getElementById('apiUrl').value = queryString;
            document.getElementById('requestMethod').value = method;
            document.getElementById('requestBody').value = body;
        }

        function quickTestFromBase64(url, method, bodyBase64) {
            let body = '';
            if (bodyBase64) {
                try {
                    body = atob(bodyBase64);
                } catch (e) {
                    console.error('Body decode hatası:', e);
                    body = '';
                }
            }
            quickTest(url, method, body);

            const testCard = document.getElementById('testCard');
            testCard.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Query parametrelerini senkronize et
            try {
                syncQueryParamsFromUrl();
            } catch (e) {
                console.error('Query params senkronizasyon hatası:', e);
            }

            showNotification('Test Başlatılıyor...', 'İstek otomatik gönderiliyor', 'success');

            setTimeout(() => testAPI(), 800);
        }

        // Thunder Client tarzı Query Param builder
        function addQueryParamRow(key = '', value = '', enabled = true) {
            const container = document.getElementById('queryParamsContainer');
            if (!container) return;

            const row = document.createElement('div');
            row.className = 'param-row';

            row.innerHTML = `
                <div class="param-checkbox">
                    <input type="checkbox" class="param-enable" ${enabled ? 'checked' : ''}>
                </div>
                <input type="text" class="param-input param-key" placeholder="id" value="${key ? String(key).replace(/"/g, '&quot;') : ''}">
                <input type="text" class="param-input param-value" placeholder="value" value="${value ? String(value).replace(/"/g, '&quot;') : ''}">
                <button type="button" class="param-remove-btn" title="Satırı Sil">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Eventler
            const checkbox = row.querySelector('.param-enable');
            const keyInput = row.querySelector('.param-key');
            const valueInput = row.querySelector('.param-value');
            const removeBtn = row.querySelector('.param-remove-btn');

            const onChange = () => {
                updateUrlFromQueryParams();

                // Eğer key ve value doldurulmuşsa, otomatik yeni satır ekle
                const key = keyInput.value.trim();
                const value = valueInput.value.trim();

                // Son satır mı kontrol et
                const allRows = container.querySelectorAll('.param-row');
                const isLastRow = row === allRows[allRows.length - 1];

                if (isLastRow && key && value) {
                    // Kısa bir gecikme ile yeni satır ekle (kullanıcı yazmayı bitirsin)
                    setTimeout(() => {
                        // Hala son satırsa ve key/value doluysa
                        const currentRows = container.querySelectorAll('.param-row');
                        if (row === currentRows[currentRows.length - 1] &&
                            keyInput.value.trim() && valueInput.value.trim()) {
                            addQueryParamRow('', '', true);
                        }
                    }, 300);
                }
            };

            checkbox.addEventListener('change', onChange);
            keyInput.addEventListener('input', onChange);
            valueInput.addEventListener('input', onChange);

            // Enter tuşu ile yeni satıra geç
            keyInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    valueInput.focus();
                }
            });
            valueInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Eğer key ve value doluysa yeni satır ekle
                    if (keyInput.value.trim() && valueInput.value.trim()) {
                        addQueryParamRow('', '', true);
                        const newRow = container.querySelectorAll('.param-row');
                        if (newRow.length > 0) {
                            const lastRow = newRow[newRow.length - 1];
                            lastRow.querySelector('.param-key')?.focus();
                        }
                    }
                }
            });

            removeBtn.addEventListener('click', () => {
                row.remove();
                updateUrlFromQueryParams();
            });

            container.appendChild(row);
        }

        function updateUrlFromQueryParams() {
            const urlInput = document.getElementById('apiUrl');
            if (!urlInput) return;

            const container = document.getElementById('queryParamsContainer');
            if (!container) return;

            const rows = container.querySelectorAll('.param-row');
            const params = [];

            rows.forEach(row => {
                const enabled = row.querySelector('.param-enable')?.checked;
                const key = row.querySelector('.param-key')?.value?.trim();
                const value = row.querySelector('.param-value')?.value ?? '';

                if (enabled && key) {
                    params.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
                }
            });

            // Sadece query string kısmını oluştur (base URL'i dahil etme)
            const qs = params.join('&');
            urlInput.value = qs ? `?${qs}` : '';
        }

        function syncQueryParamsFromUrl() {
            const urlInput = document.getElementById('apiUrl');
            const container = document.getElementById('queryParamsContainer');
            if (!urlInput || !container) return;

            // Eski satırları temizle
            container.innerHTML = '';

            const url = urlInput.value || '';

            // URL'den query string kısmını al (başında ? varsa kaldır)
            let qs = url.startsWith('?') ? url.substring(1) : url.split('?')[1] || '';

            if (!qs) {
                // En az bir boş satır aç
                addQueryParamRow('', '', true);
                return;
            }

            const searchParams = new URLSearchParams(qs);

            let hasRows = false;
            for (const [key, value] of searchParams.entries()) {
                addQueryParamRow(key, value, true);
                hasRows = true;
            }

            if (!hasRows) {
                addQueryParamRow('', '', true);
            }
        }

        function copyEndpointUrl(url) {
            url = replaceVariables(url);
            navigator.clipboard.writeText(url).then(() => {
                showNotification('URL Kopyalandı!', 'Endpoint URL panoya kopyalandı', 'success');
            });
        }

        function saveEndpointAsTemplate(name, url, method, body = '') {
            const template = {
                id: Date.now(),
                name: name,
                url: url,
                method: method,
                body: body,
                headers: '{"Content-Type": "application/json"}',
                date: new Date().toLocaleString('tr-TR')
            };

            templates.push(template);
            localStorage.setItem('apiTemplates', JSON.stringify(templates));
            showNotification('Şablon Kaydedildi!', `"${name}" şablonu başarıyla oluşturuldu`, 'success');
        }

        async function generateToken() {
            const tokenDisplay = document.getElementById('tokenDisplay');
            tokenDisplay.textContent = 'Token oluşturuluyor...';
            tokenDisplay.classList.remove('empty');

            try {
                // Gerçek token endpoint'ine istek at
                const baseUrlInput = document.getElementById('baseUrlInput');
                const baseUrlValue = baseUrlInput ? baseUrlInput.value : baseUrl;
                const tokenUrl = baseUrlValue + '?id=token';

                const response = await fetch(tokenUrl, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                // Token'ı response'dan al
                if (data && data.veri && data.veri.token) {
                    generatedToken = data.veri.token;
                } else if (data && data.token) {
                    generatedToken = data.token;
                } else {
                    throw new Error('Token response formatı beklenmiyor');
                }

                tokenDisplay.textContent = generatedToken;
                document.getElementById('copyTokenBtn').style.display = 'flex';

                // Token'ı panoya kopyala
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(generatedToken).then(() => {
                        showNotification('Token Oluşturuldu!', 'Token panoya kopyalandı', 'success');
                    }).catch(() => {
                        // Fallback yöntemi dene
                        fallbackCopyToken(generatedToken);
                    });
                } else {
                    // Fallback yöntemi kullan
                    fallbackCopyToken(generatedToken);
                }
            } catch (error) {
                tokenDisplay.textContent = 'Token oluşturulamadı: ' + error.message;
                tokenDisplay.classList.add('empty');
                showNotification('Hata', 'Token oluşturulamadı: ' + error.message, 'error');
                console.error('Token oluşturma hatası:', error);
            }
        }

        function fallbackCopyToken(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textarea);
                if (successful) {
                    showNotification('Token Oluşturuldu!', 'Token panoya kopyalandı', 'success');
                } else {
                    showNotification('Token Oluşturuldu', 'Ancak panoya kopyalanamadı', 'error');
                }
            } catch (err) {
                document.body.removeChild(textarea);
                showNotification('Token Oluşturuldu', 'Ancak panoya kopyalanamadı', 'error');
            }
        }

        function copyToken() {
            if (generatedToken) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(generatedToken).then(() => {
                        showNotification('Kopyalandı!', 'Token panoya kopyalandı', 'success');
                    }).catch(() => {
                        fallbackCopyToken(generatedToken);
                    });
                } else {
                    fallbackCopyToken(generatedToken);
                }
            }
        }

        function showNotification(title, message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const titleEl = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');
            const icon = toast.querySelector('.notification-icon i');

            titleEl.textContent = title;
            messageEl.textContent = message;

            if (type === 'error') {
                toast.classList.add('error');
                icon.className = 'fas fa-times';
            } else {
                toast.classList.remove('error');
                icon.className = 'fas fa-check';
            }

            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function clearAllStorage() {
            if (confirm('Tüm verileri temizlemek istediğinizden emin misiniz?\n\nBu işlem şunları silecek:\n- İstek geçmişi\n- Kaydedilmiş şablonlar\n- Favoriler\n- Son kullanılanlar\n- İstatistikler\n\nBu işlem geri alınamaz!')) {
                // Tüm localStorage verilerini temizle
                localStorage.removeItem('apiHistory');
                localStorage.removeItem('apiTemplates');
                localStorage.removeItem('apiFavorites');
                localStorage.removeItem('apiRecent');
                localStorage.removeItem('apiStats');

                // Tema ayarını koru (kullanıcı tercihi)
                // localStorage.removeItem('theme'); // İsterseniz bunu da ekleyebilirsiniz

                showNotification('Temizlendi', 'Tüm veriler başarıyla temizlendi', 'success');

                // Sayfayı yenile
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }

        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = body.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                body.removeAttribute('data-theme');
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                document.getElementById('themeIcon').className = 'fas fa-sun';
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function switchTab(event, tabName) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            event.target.closest('.tab-button').classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        // ========== EXPORT FORMATS ==========
        function exportFormat(format, baseUrl) {
            const url = baseUrl + '/' + format;
            window.open(url, '_blank');
        }

        // ========== HEADERS PANEL FUNCTIONS ==========
        let headersViewMode = 'json'; // 'json' or 'form'

        function toggleHeadersView() {
            headersViewMode = headersViewMode === 'json' ? 'form' : 'json';
            const jsonView = document.getElementById('headersJsonView');
            const formView = document.getElementById('headersFormView');
            const modeLabel = document.getElementById('headersViewMode');

            if (headersViewMode === 'form') {
                jsonView.style.display = 'none';
                formView.style.display = 'block';
                modeLabel.textContent = 'FORM';
                loadHeadersToForm();
            } else {
                jsonView.style.display = 'block';
                formView.style.display = 'none';
                modeLabel.textContent = 'JSON';
                saveHeadersFromForm();
            }
        }

        function loadHeadersToForm() {
            const container = document.getElementById('headersFormContainer');
            const headersText = document.getElementById('requestHeaders').value;
            container.innerHTML = '';

            try {
                const headers = headersText ? JSON.parse(headersText) : {};
                Object.keys(headers).forEach(key => {
                    addHeaderRow(key, headers[key]);
                });
            } catch (e) {
                // JSON parse hatası, boş form göster
            }

            if (container.children.length === 0) {
                addHeaderRow();
            }
        }

        function saveHeadersFromForm() {
            const rows = document.querySelectorAll('.header-row');
            const headers = {};
            rows.forEach(row => {
                const key = row.querySelector('.header-key')?.value?.trim();
                const value = row.querySelector('.header-value')?.value?.trim();
                if (key && value) {
                    headers[key] = value;
                }
            });
            document.getElementById('requestHeaders').value = JSON.stringify(headers, null, 2);
            validateHeadersJSON();
        }

        function addHeaderRow(key = '', value = '') {
            const container = document.getElementById('headersFormContainer');
            const row = document.createElement('div');
            row.className = 'header-row';
            row.innerHTML = `
                <input type="text" class="header-input header-key" placeholder="Header Key" value="${key}" onchange="saveHeadersFromForm()">
                <input type="text" class="header-input header-value" placeholder="Header Value" value="${value}" onchange="saveHeadersFromForm()">
                <button class="header-remove-btn" onclick="this.closest('.header-row').remove(); saveHeadersFromForm();">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(row);
        }

        function addQuickHeader(key, value) {
            if (headersViewMode === 'json') {
                const textarea = document.getElementById('requestHeaders');
                let headers = {};
                try {
                    headers = JSON.parse(textarea.value || '{}');
                } catch (e) {
                    headers = {};
                }
                headers[key] = value;
                textarea.value = JSON.stringify(headers, null, 2);
                validateHeadersJSON();
            } else {
                addHeaderRow(key, value);
                saveHeadersFromForm();
            }
        }

        function autoSetContentType() {
            const body = document.getElementById('requestBody').value;
            if (body.trim()) {
                try {
                    JSON.parse(body);
                    addQuickHeader('Content-Type', 'application/json');
                } catch (e) {
                    addQuickHeader('Content-Type', 'text/plain');
                }
            } else {
                addQuickHeader('Content-Type', 'application/json');
            }
        }

        function clearHeaders() {
            document.getElementById('requestHeaders').value = '';
            document.getElementById('headersFormContainer').innerHTML = '';
            addHeaderRow();
            validateHeadersJSON();
        }

        function validateHeadersJSON() {
            const textarea = document.getElementById('requestHeaders');
            const status = document.getElementById('headersJSONStatus');
            const value = textarea.value.trim();

            if (!value) {
                status.style.display = 'none';
                return;
            }

            try {
                JSON.parse(value);
                status.style.display = 'flex';
                status.className = 'json-status valid';
                status.innerHTML = '<i class="fas fa-check-circle"></i> Geçerli JSON';
            } catch (e) {
                status.style.display = 'flex';
                status.className = 'json-status invalid';
                status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Geçersiz JSON: ' + e.message;
            }
        }

        // ========== BODY PANEL FUNCTIONS ==========
        function formatJSON(textareaId) {
            const textarea = document.getElementById(textareaId);
            try {
                const parsed = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(parsed, null, 2);
                validateBodyJSON();
                showNotification('Başarılı', 'JSON formatlandı', 'success');
            } catch (e) {
                showNotification('Hata', 'Geçersiz JSON: ' + e.message, 'error');
            }
        }

        function validateJSON(textareaId) {
            const textarea = document.getElementById(textareaId);
            try {
                JSON.parse(textarea.value);
                showNotification('Başarılı', 'Geçerli JSON', 'success');
            } catch (e) {
                showNotification('Hata', 'Geçersiz JSON: ' + e.message, 'error');
            }
        }

        function validateBodyJSON() {
            const textarea = document.getElementById('requestBody');
            const status = document.getElementById('bodyJSONStatus');
            const value = textarea.value.trim();

            if (!value) {
                status.style.display = 'none';
                return;
            }

            try {
                JSON.parse(value);
                status.style.display = 'flex';
                status.className = 'json-status valid';
                status.innerHTML = '<i class="fas fa-check-circle"></i> Geçerli JSON';
            } catch (e) {
                status.style.display = 'flex';
                status.className = 'json-status invalid';
                status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Geçersiz JSON: ' + e.message;
            }
        }

        function insertBodyTemplate() {
            const templates = [{
                    name: 'Empty Object',
                    value: '{}'
                },
                {
                    name: 'User Object',
                    value: '{"name": "John", "email": "john@example.com", "age": 30}'
                },
                {
                    name: 'Product Object',
                    value: '{"id": 1, "name": "Product", "price": 99.99, "stock": 100}'
                },
                {
                    name: 'Array Example',
                    value: '[{"id": 1, "name": "Item 1"}, {"id": 2, "name": "Item 2"}]'
                }
            ];

            const selected = prompt('Şablon seçin:\n' + templates.map((t, i) => `${i + 1}. ${t.name}`).join('\n'));
            if (selected) {
                const index = parseInt(selected) - 1;
                if (templates[index]) {
                    document.getElementById('requestBody').value = JSON.stringify(JSON.parse(templates[index].value), null, 2);
                    validateBodyJSON();
                }
            }
        }

        function clearBody() {
            document.getElementById('requestBody').value = '';
            validateBodyJSON();
        }

        // ========== AUTH PANEL FUNCTIONS ==========
        let currentAuthType = 'bearer';

        function setAuthType(type) {
            currentAuthType = type;
            document.querySelectorAll('.auth-type-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[data-auth-type="${type}"]`).classList.add('active');

            document.getElementById('authBearerView').style.display = type === 'bearer' ? 'block' : 'none';
            document.getElementById('authBasicView').style.display = type === 'basic' ? 'block' : 'none';
            document.getElementById('authApikeyView').style.display = type === 'apikey' ? 'block' : 'none';
        }

        function useGeneratedToken() {
            if (generatedToken) {
                document.getElementById('authToken').value = 'Bearer ' + generatedToken;
                showNotification('Başarılı', 'Token eklendi', 'success');
            } else {
                showNotification('Uyarı', 'Önce token üretin', 'error');
            }
        }

        function copyAuthToHeader() {
            const authToken = document.getElementById('authToken').value.trim();
            if (authToken) {
                addQuickHeader('Authorization', authToken.startsWith('Bearer') ? authToken : 'Bearer ' + authToken);
                showNotification('Başarılı', 'Header\'a kopyalandı', 'success');
            }
        }

        function clearAuth() {
            document.getElementById('authToken').value = '';
            document.getElementById('authBasicUser').value = '';
            document.getElementById('authBasicPass').value = '';
            document.getElementById('authApikeyKey').value = '';
            document.getElementById('authApikeyValue').value = '';
        }

        // Auth değerlerini al (testAPI için)
        function getAuthValue() {
            if (currentAuthType === 'bearer') {
                return document.getElementById('authToken').value.trim();
            } else if (currentAuthType === 'basic') {
                const user = document.getElementById('authBasicUser').value.trim();
                const pass = document.getElementById('authBasicPass').value.trim();
                if (user && pass) {
                    return 'Basic ' + btoa(user + ':' + pass);
                }
            } else if (currentAuthType === 'apikey') {
                const key = document.getElementById('authApikeyKey').value.trim();
                const value = document.getElementById('authApikeyValue').value.trim();
                if (key && value) {
                    return {
                        header: key,
                        value: value
                    };
                }
            }
            return '';
        }

        async function testAPI() {
            // Base URL'i al (readonly input'tan)
            const baseUrlInput = document.getElementById('baseUrlInput');
            const baseUrlValue = baseUrlInput ? baseUrlInput.value : baseUrl;

            // Endpoint/Query string'i al
            let endpointQuery = document.getElementById('apiUrl').value || '';

            // Base URL + Endpoint/Query birleştir
            let url = baseUrlValue;
            if (endpointQuery) {
                // Eğer endpointQuery ? ile başlamıyorsa ekle
                if (!endpointQuery.startsWith('?')) {
                    endpointQuery = '?' + endpointQuery;
                }
                url = baseUrlValue + endpointQuery;
            }

            const method = document.getElementById('requestMethod').value;
            let body = document.getElementById('requestBody').value;
            const headersText = document.getElementById('requestHeaders').value;

            // Headers form view'den veri al (eğer aktifse)
            if (headersViewMode === 'form') {
                saveHeadersFromForm();
            }

            // Auth değerini al (yeni sistem)
            const authValue = getAuthValue();
            const responseContent = document.getElementById('responseContent');
            const statusInfo = document.getElementById('statusInfo');

            responseContent.textContent = 'İstek gönderiliyor...';
            statusInfo.innerHTML = '';

            // İstek başlatıldı bildirimi
            showNotification('Test Başlatılıyor...', `${method} isteği gönderiliyor`, 'success');

            try {
                // Headers'ı parse et
                let headers = {
                    'Content-Type': 'application/json'
                };

                if (headersText) {
                    try {
                        const parsedHeaders = JSON.parse(headersText);
                        headers = {
                            ...headers,
                            ...parsedHeaders
                        };
                    } catch (e) {
                        console.warn('Headers JSON parse hatası:', e);
                    }
                }

                // Authorization ekle (yeni sistem)
                if (authValue) {
                    if (typeof authValue === 'object' && authValue.header) {
                        // API Key
                        headers[authValue.header] = authValue.value;
                    } else if (authValue.startsWith('Basic ')) {
                        // Basic Auth
                        headers['Authorization'] = authValue;
                    } else {
                        // Bearer Token
                        const tokenValue = authValue.replace(/^Bearer\s+/i, '');
                        headers['Authorization'] = 'Bearer ' + tokenValue;
                    }
                }

                const options = {
                    method: method,
                    headers: headers
                };

                // POST/PUT/PATCH için body hazırla
                if (['POST', 'PUT', 'PATCH'].includes(method)) {
                    if (body) {
                        // Body JSON ise parse et ve token ekle
                        try {
                            let bodyObj = JSON.parse(body);

                            // Eğer body'de id yoksa URL'den çıkar
                            if (!bodyObj.id && url.includes('?id=')) {
                                const urlParams = new URLSearchParams(url.split('?')[1]);
                                bodyObj.id = urlParams.get('id');
                            }

                            // Token ekle (eğer yoksa ve Bearer token ise)
                            if (authValue && typeof authValue === 'string' && !authValue.startsWith('Basic ') && !bodyObj.token) {
                                const tokenValue = authValue.replace(/^Bearer\s+/i, '');
                                bodyObj.token = tokenValue;
                            }

                            options.body = JSON.stringify(bodyObj);
                        } catch (e) {
                            // Body JSON değilse olduğu gibi gönder
                            options.body = body;
                        }
                    } else {
                        // Body boşsa URL'den id'yi al ve body oluştur
                        if (url.includes('?id=')) {
                            const urlParams = new URLSearchParams(url.split('?')[1]);
                            const endpointId = urlParams.get('id');
                            const bodyObj = {
                                id: endpointId
                            };

                            if (authValue && typeof authValue === 'string' && !authValue.startsWith('Basic ')) {
                                const tokenValue = authValue.replace(/^Bearer\s+/i, '');
                                bodyObj.token = tokenValue;
                            }

                            options.body = JSON.stringify(bodyObj);
                        }
                    }
                } else {
                    // GET için URL'e token ekle (query string olarak - sadece Bearer token)
                    if (authValue && typeof authValue === 'string' && !authValue.startsWith('Basic ') && !url.includes('token=')) {
                        const tokenValue = authValue.replace(/^Bearer\s+/i, '');
                        const separator = url.includes('?') ? '&' : '?';
                        url += separator + 'token=' + encodeURIComponent(tokenValue);
                    }
                }

                const startTime = Date.now();
                const response = await fetch(url, options);
                const endTime = Date.now();
                const duration = endTime - startTime;

                let responseData;
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    responseData = await response.json();
                    currentResponse = JSON.stringify(responseData, null, 2);
                } else {
                    const text = await response.text();
                    currentResponse = text;
                    try {
                        responseData = JSON.parse(text);
                    } catch (e) {
                        responseData = {
                            raw: text
                        };
                    }
                }

                statusInfo.innerHTML = `
                    <span class="status-badge ${response.ok ? 'status-success' : 'status-error'}">
                        ${response.status} ${response.statusText}
                    </span>
                    <span class="response-time">${duration}ms</span>
                `;

                responseContent.textContent = currentResponse;
                addToHistory(url, method, response.status, duration, options.body || body);

                // Duruma göre bildirim göster
                let notificationTitle = '';
                let notificationMessage = '';
                let notificationType = 'success';

                if (response.ok) {
                    // Başarılı yanıtlar (200-299)
                    if (response.status === 200) {
                        notificationTitle = 'Test Başarılı!';
                        notificationMessage = `İstek başarıyla tamamlandı (${duration}ms)`;
                    } else if (response.status === 201) {
                        notificationTitle = 'Oluşturuldu!';
                        notificationMessage = `Kaynak başarıyla oluşturuldu (${duration}ms)`;
                    } else if (response.status === 204) {
                        notificationTitle = 'Başarılı!';
                        notificationMessage = `İşlem tamamlandı (${duration}ms)`;
                    } else {
                        notificationTitle = 'Başarılı!';
                        notificationMessage = `HTTP ${response.status} - ${response.statusText} (${duration}ms)`;
                    }

                    // Response içindeki durum mesajını kontrol et
                    if (responseData && responseData.durum) {
                        const durumMesajlari = {
                            'basarili': 'İşlem başarıyla tamamlandı',
                            'bilgi': 'Bilgilendirme mesajı',
                            'sessiz': 'İşlem başarılı',
                            'gecersizIstek': 'Geçersiz İstek',
                            'oturum': 'Oturum Hatası',
                            'yetki': 'Yetki Hatası',
                            'hata': 'Hata oluştu',
                            'validasyon': 'Geçersiz Veri'
                        };

                        if (durumMesajlari[responseData.durum]) {
                            notificationMessage = `${durumMesajlari[responseData.durum]} (${duration}ms)`;
                        }

                        // Hata durumları için bildirim tipini değiştir
                        if (['gecersizIstek', 'oturum', 'yetki', 'hata', 'validasyon'].includes(responseData.durum)) {
                            notificationType = 'error';
                            notificationTitle = 'Hata!';
                        }
                    }
                } else {
                    // HTTP hata kodları (400-599)
                    notificationType = 'error';

                    if (response.status >= 400 && response.status < 500) {
                        notificationTitle = 'İstemci Hatası!';
                        notificationMessage = `HTTP ${response.status} - ${response.statusText}`;

                        if (response.status === 401) {
                            notificationMessage = 'Oturum hatası - Token geçersiz veya süresi dolmuş';
                        } else if (response.status === 403) {
                            notificationMessage = 'Yetki hatası - Bu işlem için yetkiniz yok';
                        } else if (response.status === 404) {
                            notificationMessage = 'Kaynak bulunamadı';
                        } else if (response.status === 422) {
                            notificationMessage = 'Geçersiz veri - Lütfen parametreleri kontrol edin';
                        }
                    } else if (response.status >= 500) {
                        notificationTitle = 'Sunucu Hatası!';
                        notificationMessage = `HTTP ${response.status} - Sunucu hatası oluştu`;
                    }

                    // Response içindeki mesajı kontrol et
                    if (responseData && responseData.mesaj) {
                        notificationMessage = responseData.mesaj;
                    }
                }

                showNotification(notificationTitle, notificationMessage, notificationType);

                // Response paneline kaydır
                setTimeout(() => {
                    const responseSection = document.querySelector('.response-section');
                    if (responseSection) {
                        const header = document.querySelector('.header');
                        const headerHeight = header ? header.offsetHeight : 65;
                        const elementPosition = responseSection.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerHeight + 15;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }, 100);
            } catch (error) {
                statusInfo.innerHTML = '<span class="status-badge status-error">ERROR</span>';
                responseContent.textContent = `Hata: ${error.message}`;
                currentResponse = `Hata: ${error.message}`;
                addToHistory(url, method, 0, 0, body);

                // Network/Connection hatası bildirimi
                let errorMessage = 'Bağlantı hatası oluştu';
                if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                    errorMessage = 'Ağ hatası - Sunucuya bağlanılamadı';
                } else if (error.message.includes('timeout')) {
                    errorMessage = 'Zaman aşımı - İstek çok uzun sürdü';
                } else if (error.message.includes('CORS')) {
                    errorMessage = 'CORS hatası - Sunucu isteğe izin vermiyor';
                } else {
                    errorMessage = error.message;
                }

                showNotification('Bağlantı Hatası!', errorMessage, 'error');

                // Response paneline kaydır (hata durumunda da)
                setTimeout(() => {
                    const responseSection = document.querySelector('.response-section');
                    if (responseSection) {
                        const header = document.querySelector('.header');
                        const headerHeight = header ? header.offsetHeight : 65;
                        const elementPosition = responseSection.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerHeight + 15;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }, 100);
            }
        }

        function formatResponse() {
            try {
                const responseContent = document.getElementById('responseContent');
                const parsed = JSON.parse(currentResponse);
                currentResponse = JSON.stringify(parsed, null, 2);
                responseContent.textContent = currentResponse;
                showNotification('Formatlandı', 'JSON başarıyla formatlandı', 'success');
            } catch (e) {
                showNotification('Hata', 'Geçerli bir JSON değil', 'error');
            }
        }

        function copyResponse() {
            if (!currentResponse) {
                showNotification('Hata', 'Kopyalanacak response yok', 'error');
                return;
            }
            navigator.clipboard.writeText(currentResponse).then(() => {
                showNotification('Kopyalandı!', 'Response panoya kopyalandı', 'success');
            });
        }

        function downloadResponse() {
            if (!currentResponse) {
                showNotification('Hata', 'İndirilecek response yok', 'error');
                return;
            }
            const blob = new Blob([currentResponse], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `response_${Date.now()}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            showNotification('İndirildi!', 'Response dosya olarak indirildi', 'success');
        }

        function toggleSearch() {
            const searchBox = document.getElementById('searchBox');
            searchBox.classList.toggle('active');
            if (searchBox.classList.contains('active')) {
                document.getElementById('searchInput').focus();
            } else {
                document.getElementById('searchInput').value = '';
                searchInResponse();
            }
        }

        function searchInResponse() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const responseContent = document.getElementById('responseContent');

            if (!searchTerm) {
                responseContent.innerHTML = currentResponse;
                return;
            }

            let highlighted = currentResponse;
            const regex = new RegExp(searchTerm, 'gi');
            highlighted = highlighted.replace(regex, match => `<span class="highlight">${match}</span>`);
            responseContent.innerHTML = highlighted;
        }



        function saveAsTemplate() {
            const url = document.getElementById('apiUrl').value;
            const method = document.getElementById('requestMethod').value;
            const body = document.getElementById('requestBody').value;
            const headers = document.getElementById('requestHeaders').value;

            if (!url) {
                showNotification('Hata', 'Lütfen bir URL girin', 'error');
                return;
            }

            const name = prompt('Şablon adı:');
            if (!name) return;

            const template = {
                id: Date.now(),
                name: name,
                url: url,
                method: method,
                body: body,
                headers: headers,
                date: new Date().toLocaleString('tr-TR')
            };

            templates.push(template);
            localStorage.setItem('apiTemplates', JSON.stringify(templates));
            showNotification('Kaydedildi!', 'Şablon başarıyla kaydedildi', 'success');
        }

        function openTemplates() {
            loadTemplates();
            document.getElementById('templatesModal').classList.add('show');
        }

        function closeTemplates() {
            document.getElementById('templatesModal').classList.remove('show');
        }

        function loadTemplates() {
            const body = document.getElementById('templatesBody');

            if (templates.length === 0) {
                body.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3; margin-bottom: 10px;"></i>
                        <p>Henüz şablon yok. İlk şablonunuzu kaydedin!</p>
                    </div>
                `;
                return;
            }

            body.innerHTML = templates.map(t => `
                <div class="template-item" onclick="loadTemplate(${t.id})">
                    <div class="template-header">
                        <div class="template-name"><i class="fas fa-bookmark"></i> ${t.name}</div>
                        <div class="template-actions">
                            <button class="template-btn" onclick="event.stopPropagation(); loadTemplate(${t.id})">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="template-btn delete" onclick="event.stopPropagation(); deleteTemplate(${t.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="template-desc">${t.url}</div>
                    <div class="template-meta">
                        <span class="method-badge method-${t.method.toLowerCase()}">${t.method}</span>
                        <span>${t.date}</span>
                    </div>
                </div>
            `).join('');
        }

        function loadTemplate(id) {
            const template = templates.find(t => t.id === id);
            if (!template) return;

            document.getElementById('apiUrl').value = template.url;
            document.getElementById('requestMethod').value = template.method;
            document.getElementById('requestBody').value = template.body;
            document.getElementById('requestHeaders').value = template.headers;

            closeTemplates();
            showNotification('Yüklendi!', 'Şablon başarıyla yüklendi', 'success');
        }

        function deleteTemplate(id) {
            if (!confirm('Bu şablonu silmek istediğinizden emin misiniz?')) return;

            templates = templates.filter(t => t.id !== id);
            localStorage.setItem('apiTemplates', JSON.stringify(templates));
            loadTemplates();
            showNotification('Silindi', 'Şablon silindi', 'success');
        }

        document.getElementById('apiUrl').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') testAPI();
        });

        // Filtreleme fonksiyonları
        function filterEndpoints() {
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            const methodFilter = document.getElementById('methodFilter').value.toLowerCase();

            // Sadece sidebar'daki endpoint item'larını filtrele
            // Kartlar zaten sadece seçilen gösteriliyor
            const sidebarItems = document.querySelectorAll('.endpoint-item');
            let visibleCount = 0;

            sidebarItems.forEach(item => {
                const endpointId = item.getAttribute('data-endpoint');
                let show = true;

                // Kategori filtresi
                if (categoryFilter) {
                    const endpointData = findEndpointData(endpointId);
                    if (endpointData) {
                        const endpointKategori = (endpointData.kategori || '').toLowerCase();
                        if (endpointKategori !== categoryFilter) {
                            show = false;
                        }
                    }
                }

                // Method filtresi
                if (methodFilter && show) {
                    const methodBadge = item.querySelector('.method-badge');
                    if (methodBadge) {
                        const method = methodBadge.textContent.trim().toLowerCase();
                        if (method !== methodFilter) {
                            show = false;
                        }
                    }
                }

                if (show) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function findEndpointData(endpointId) {
            if (typeof apiDoc === 'object' && apiDoc !== null && apiDoc.endpoints) {
                for (const kategori in apiDoc.endpoints) {
                    const endpoints = apiDoc.endpoints[kategori];
                    if (Array.isArray(endpoints)) {
                        const found = endpoints.find(ep => ep.id === endpointId);
                        if (found) return found;
                    }
                }
            }
            return null;
        }

        function resetFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('methodFilter').value = '';
            document.getElementById('endpointSearch').value = '';

            // Kartlar zaten sadece seçilen gösteriliyor, filtreleme gerekmez

            // Tüm sidebar item'larını göster
            const sidebarItems = document.querySelectorAll('.endpoint-item');
            sidebarItems.forEach(item => {
                item.style.display = 'flex';
            });

            showNotification('Filtreler Sıfırlandı', 'Tüm endpoint\'ler gösteriliyor', 'success');
        }

        // Gelişmiş Arama ve Filtreleme
        function searchEndpoints() {
            const searchTerm = document.getElementById('endpointSearch').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            const methodFilter = document.getElementById('methodFilter').value.toLowerCase();

            const sidebarItems = document.querySelectorAll('.endpoint-item');
            let visibleCount = 0;

            sidebarItems.forEach(item => {
                const endpointId = item.getAttribute('data-endpoint') || '';
                const endpointDesc = item.getAttribute('data-endpoint-desc') || '';
                let show = true;

                // Arama filtresi
                if (searchTerm) {
                    const matchesId = endpointId.toLowerCase().includes(searchTerm);
                    const matchesDesc = endpointDesc.toLowerCase().includes(searchTerm);
                    if (!matchesId && !matchesDesc) {
                        show = false;
                    }
                }

                // Kategori filtresi
                if (categoryFilter && show) {
                    const endpointData = findEndpointData(endpointId);
                    if (endpointData) {
                        const endpointKategori = (endpointData.kategori || '').toLowerCase();
                        if (endpointKategori !== categoryFilter) {
                            show = false;
                        }
                    }
                }

                // Method filtresi
                if (methodFilter && show) {
                    const methodBadge = item.querySelector('.method-badge');
                    if (methodBadge) {
                        const method = methodBadge.textContent.trim().toLowerCase();
                        if (method !== methodFilter) {
                            show = false;
                        }
                    }
                }

                if (show) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function toggleFavorite(endpointId, btn) {
            const index = favorites.indexOf(endpointId);
            if (index > -1) {
                favorites.splice(index, 1);
                btn.classList.remove('active');
                btn.querySelector('i').className = 'far fa-star';
                showNotification('Favorilerden Çıkarıldı', endpointId + ' favorilerden çıkarıldı', 'success');
            } else {
                favorites.push(endpointId);
                btn.classList.add('active');
                btn.querySelector('i').className = 'fas fa-star';
                showNotification('Favorilere Eklendi', endpointId + ' favorilere eklendi', 'success');
            }
            localStorage.setItem('apiFavorites', JSON.stringify(favorites));
            updateFavoriteButtons();
            updateFavoriteCount();
        }

        function updateFavoriteButtons() {
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                const item = btn.closest('.endpoint-item');
                if (item) {
                    const endpointId = item.getAttribute('data-endpoint');
                    if (favorites.includes(endpointId)) {
                        btn.classList.add('active');
                        btn.querySelector('i').className = 'fas fa-star';
                    } else {
                        btn.classList.remove('active');
                        btn.querySelector('i').className = 'far fa-star';
                    }
                }
            });
        }

        function showFavorites() {
            const sidebarItems = document.querySelectorAll('.endpoint-item');
            sidebarItems.forEach(item => {
                const endpointId = item.getAttribute('data-endpoint');
                if (favorites.includes(endpointId)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
            showNotification('Favoriler', 'Sadece favori endpoint\'ler gösteriliyor', 'info');
        }

        function showRecent() {
            const sidebarItems = document.querySelectorAll('.endpoint-item');
            sidebarItems.forEach(item => {
                const endpointId = item.getAttribute('data-endpoint');
                if (recentEndpoints.includes(endpointId)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
            showNotification('Son Kullanılanlar', 'Sadece son kullanılan endpoint\'ler gösteriliyor', 'info');
        }

        function addToRecent(endpointId) {
            const index = recentEndpoints.indexOf(endpointId);
            if (index > -1) {
                recentEndpoints.splice(index, 1);
            }
            recentEndpoints.unshift(endpointId);
            if (recentEndpoints.length > 10) {
                recentEndpoints = recentEndpoints.slice(0, 10);
            }
            localStorage.setItem('apiRecent', JSON.stringify(recentEndpoints));
            updateRecentCount();
        }

        // İstatistikler ve Analitik
        function loadAnalytics() {
            const body = document.getElementById('analyticsContent');
            if (!body) {
                console.error('analyticsContent bulunamadı');
                return;
            }

            // History'den istatistikleri hesapla
            const totalRequests = history.length || 0;
            const successRequests = history.filter(h => h.status >= 200 && h.status < 300).length;
            const successRate = totalRequests > 0 ? ((successRequests / totalRequests) * 100).toFixed(1) : 0;

            const times = history.map(h => parseFloat(h.duration) || 0).filter(t => t > 0);
            const avgTime = times.length > 0 ? (times.reduce((a, b) => a + b, 0) / times.length).toFixed(0) : 0;
            const minTime = times.length > 0 ? Math.min(...times) : 0;
            const maxTime = times.length > 0 ? Math.max(...times) : 0;

            // Method dağılımı
            const methodStats = {};
            history.forEach(h => {
                methodStats[h.method] = (methodStats[h.method] || 0) + 1;
            });

            // Status kod dağılımı
            const statusStats = {};
            history.forEach(h => {
                const statusGroup = Math.floor(h.status / 100) * 100;
                statusStats[statusGroup] = (statusStats[statusGroup] || 0) + 1;
            });

            // Başarısız istekler
            const failedRequests = totalRequests - successRequests;

            // Status kod detayları
            const statusDetails = {};
            history.forEach(h => {
                statusDetails[h.status] = (statusDetails[h.status] || 0) + 1;
            });
            const statusDetailsList = Object.entries(statusDetails)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);

            // Son 24 saat içindeki istekler
            const now = Date.now();
            const last24h = history.filter(h => {
                try {
                    // Önce timestampMs varsa onu kullan (yeni format)
                    if (h.timestampMs) {
                        return (now - h.timestampMs) < (24 * 60 * 60 * 1000);
                    }
                    // Eski format için timestamp string'ini parse et
                    const timestamp = new Date(h.timestamp).getTime();
                    // NaN kontrolü
                    if (isNaN(timestamp)) {
                        return false;
                    }
                    return (now - timestamp) < (24 * 60 * 60 * 1000);
                } catch (e) {
                    return false;
                }
            }).length;

            // Son 7 gün içindeki istekler
            const last7d = history.filter(h => {
                try {
                    // Önce timestampMs varsa onu kullan (yeni format)
                    if (h.timestampMs) {
                        return (now - h.timestampMs) < (7 * 24 * 60 * 60 * 1000);
                    }
                    // Eski format için timestamp string'ini parse et
                    const timestamp = new Date(h.timestamp).getTime();
                    // NaN kontrolü
                    if (isNaN(timestamp)) {
                        return false;
                    }
                    return (now - timestamp) < (7 * 24 * 60 * 60 * 1000);
                } catch (e) {
                    return false;
                }
            }).length;

            if (totalRequests === 0) {
                body.innerHTML = `
                    <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                        <i class="fas fa-chart-line" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
                        <p style="font-size: 14px;">Henüz yeterli veri yok. API istekleri yaptıkça istatistikler oluşacak.</p>
                    </div>
                `;
                return;
            }

            body.innerHTML = `
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 30px;" class="stats-grid-responsive">
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-server" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Toplam İstek</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent-primary); line-height: 1.2;">${totalRequests}</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-check-circle" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Başarılı</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent-success); line-height: 1.2;">${successRequests}</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-times-circle" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Başarısız</div>
                                <div style="font-size: 24px; font-weight: 700; color: #e74a3b; line-height: 1.2;">${failedRequests}</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-percentage" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Başarı Oranı</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent-success); line-height: 1.2;">${successRate}%</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-clock" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Ort. Süre</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent-warning); line-height: 1.2;">${avgTime}ms</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-tachometer-alt" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Min/Max</div>
                                <div style="font-size: 18px; font-weight: 700; color: var(--accent-info); line-height: 1.2;">${minTime}ms / ${maxTime}ms</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-calendar-day" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Son 24 Saat</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent-primary); line-height: 1.2;">${last24h}</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #858796 0%, #5a5c69 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-calendar-week" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Son 7 Gün</div>
                                <div style="font-size: 24px; font-weight: 700; color: #858796; line-height: 1.2;">${last7d}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div>
                        <h4 style="margin-bottom: 15px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-list" style="color: var(--accent-primary);"></i> Method Dağılımı
                        </h4>
                        <div style="background: var(--bg-primary); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                            ${Object.keys(methodStats).length > 0 ? Object.entries(methodStats).map(([method, count]) => `
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 8px; background: var(--bg-secondary); border-radius: 6px;">
                                    <span class="method-badge method-${method.toLowerCase()}">${method}</span>
                                    <span style="font-weight: 600; color: var(--text-primary);">${count} istek</span>
                                </div>
                            `).join('') : '<p style="color: var(--text-secondary); text-align: center; padding: 10px;">Veri yok</p>'}
                        </div>
                    </div>

                    <div>
                        <h4 style="margin-bottom: 15px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-code" style="color: var(--accent-info);"></i> Status Kod Dağılımı
                        </h4>
                        <div style="background: var(--bg-primary); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                            ${Object.keys(statusStats).length > 0 ? Object.entries(statusStats).map(([status, count]) => `
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 8px; background: var(--bg-secondary); border-radius: 6px;">
                                    <span style="font-weight: 600; color: var(--text-primary);">${status}xx</span>
                                    <span style="font-weight: 600; color: var(--text-primary);">${count} istek</span>
                                </div>
                            `).join('') : '<p style="color: var(--text-secondary); text-align: center; padding: 10px;">Veri yok</p>'}
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <h4 style="margin-bottom: 15px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle" style="color: var(--accent-warning);"></i> Status Kod Detayları
                    </h4>
                    <div style="background: var(--bg-primary); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                        ${statusDetailsList.length > 0 ? statusDetailsList.map(([status, count]) => {
                            let statusColor = '#858796';
                            if (status >= 200 && status < 300) statusColor = '#1cc88a';
                            else if (status >= 300 && status < 400) statusColor = '#f6c23e';
                            else if (status >= 400) statusColor = '#e74a3b';
                            return `
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; background: var(--bg-secondary); border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span style="width: 60px; padding: 4px 8px; background: ${statusColor}; color: white; border-radius: 4px; font-weight: 700; text-align: center; font-size: 12px;">${status}</span>
                                        <span style="font-size: 12px; color: var(--text-secondary);">${getStatusText(status)}</span>
                                    </div>
                                    <span style="font-weight: 600; color: var(--text-primary);">${count} kez</span>
                                </div>
                            `;
                        }).join('') : '<p style="color: var(--text-secondary); text-align: center; padding: 20px;">Henüz yeterli veri yok</p>'}
                    </div>
                </div>
            `;
        }

        function getStatusText(status) {
            const statusTexts = {
                200: 'OK',
                201: 'Created',
                204: 'No Content',
                400: 'Bad Request',
                401: 'Unauthorized',
                403: 'Forbidden',
                404: 'Not Found',
                500: 'Internal Server Error',
                502: 'Bad Gateway',
                503: 'Service Unavailable'
            };
            return statusTexts[status] || 'Unknown';
        }

        // Endpoint kartı buton fonksiyonları
        function copyEndpointUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                showNotification('Kopyalandı!', 'URL panoya kopyalandı', 'success');
            }).catch(() => {
                showNotification('Hata', 'URL kopyalanamadı', 'error');
            });
        }

        function copyCurlCommand(url, method, bodyBase64) {
            const baseUrlInput = document.getElementById('baseUrlInput');
            const baseUrlValue = baseUrlInput ? baseUrlInput.value : baseUrl;
            const fullUrl = baseUrlValue + (url.startsWith('?') ? url : '?id=' + url);

            let curlCommand = `curl -X ${method} "${fullUrl}"`;

            if (bodyBase64 && method !== 'GET') {
                try {
                    const body = atob(bodyBase64);
                    curlCommand += ` \\\n  -H "Content-Type: application/json" \\\n  -d '${body}'`;
                } catch (e) {
                    console.error('Body decode error:', e);
                }
            }

            navigator.clipboard.writeText(curlCommand).then(() => {
                showNotification('Kopyalandı!', 'cURL komutu panoya kopyalandı', 'success');
            }).catch(() => {
                showNotification('Hata', 'cURL komutu kopyalanamadı', 'error');
            });
        }

        function copyEndpointJson(endpointId, url, method) {
            const baseUrlInput = document.getElementById('baseUrlInput');
            const baseUrlValue = baseUrlInput ? baseUrlInput.value : baseUrl;
            const fullUrl = baseUrlValue + (url.includes('?') ? url.split('?')[1] : '?id=' + endpointId);

            const endpointJson = {
                id: endpointId,
                url: fullUrl,
                method: method,
                baseUrl: baseUrlValue
            };

            const jsonString = JSON.stringify(endpointJson, null, 2);

            navigator.clipboard.writeText(jsonString).then(() => {
                showNotification('Kopyalandı!', 'Endpoint JSON panoya kopyalandı', 'success');
            }).catch(() => {
                showNotification('Hata', 'JSON kopyalanamadı', 'error');
            });
        }

        function saveEndpointAsTemplate(name, query, method, body) {
            const baseUrlInput = document.getElementById('baseUrlInput');
            const baseUrlValue = baseUrlInput ? baseUrlInput.value : baseUrl;
            const fullUrl = baseUrlValue + query;

            const template = {
                id: Date.now(),
                name: name || `Endpoint: ${query}`,
                url: query,
                method: method,
                body: body || '',
                headers: '{"Content-Type": "application/json"}',
                date: new Date().toLocaleString('tr-TR')
            };

            templates.push(template);
            localStorage.setItem('apiTemplates', JSON.stringify(templates));
            showNotification('Kaydedildi!', 'Endpoint şablon olarak kaydedildi', 'success');
        }

        // Grafik sistemi
        let requestChart = null;

        function parseTimestamp(timestamp) {
            if (!timestamp) return new Date();

            // Eğer timestamp bir sayı ise (milliseconds)
            if (typeof timestamp === 'number') {
                return new Date(timestamp);
            }

            // Eğer timestamp bir string ise
            if (typeof timestamp === 'string') {
                // Türkçe tarih formatını parse et (örn: "30.12.2025 20:13:58")
                const turkishDate = timestamp.match(/(\d{2})\.(\d{2})\.(\d{4})\s+(\d{2}):(\d{2}):(\d{2})/);
                if (turkishDate) {
                    const [, day, month, year, hour, minute, second] = turkishDate;
                    return new Date(year, month - 1, day, hour, minute, second);
                }

                // ISO formatını dene
                const isoDate = new Date(timestamp);
                if (!isNaN(isoDate.getTime())) {
                    return isoDate;
                }
            }

            return new Date();
        }

        function initChart() {
            const ctx = document.getElementById('requestChart');
            if (!ctx) return;

            const last10Requests = history.slice(0, 10).reverse();

            if (last10Requests.length === 0) {
                ctx.parentElement.innerHTML = `
                    <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                        <i class="fas fa-chart-line" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
                        <p style="font-size: 14px;">Henüz yeterli veri yok. İstek yaptıkça grafik oluşacak.</p>
                    </div>
                `;
                return;
            }

            // Timestamp'leri düzelt ve label'ları oluştur
            const labels = last10Requests.map((req, index) => {
                const date = parseTimestamp(req.timestamp || req.id);
                if (isNaN(date.getTime())) {
                    return `İstek ${index + 1}`;
                }
                return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
            });

            const responseTimes = last10Requests.map(req => parseFloat(req.duration) || 0);
            const statusCodes = last10Requests.map(req => parseInt(req.status) || 0);
            const methods = last10Requests.map(req => req.method || 'GET');

            // Status code renklerini belirle
            const statusColors = statusCodes.map(code => {
                if (code >= 200 && code < 300) return 'rgb(28, 200, 138)'; // Yeşil - Başarılı
                if (code >= 300 && code < 400) return 'rgb(255, 193, 7)'; // Sarı - Yönlendirme
                if (code >= 400) return 'rgb(231, 74, 59)'; // Kırmızı - Hata
                return 'rgb(108, 117, 125)'; // Gri - Bilinmeyen
            });

            if (requestChart) {
                requestChart.destroy();
            }

            requestChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Response Time (ms)',
                        data: responseTimes,
                        borderColor: 'rgb(78, 115, 223)',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y',
                        fill: true,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: statusColors,
                        pointBorderColor: statusColors,
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    return `${methods[index]} - ${labels[index]}`;
                                },
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const req = last10Requests[index];
                                    const status = statusCodes[index] || 0;
                                    const statusText = status >= 200 && status < 300 ? ' (Başarılı)' :
                                        status >= 400 ? ' (Hata)' :
                                        status >= 300 && status < 400 ? ' (Yönlendirme)' : ' (Bilinmeyen)';

                                    return [
                                        `Response Time: ${responseTimes[index] || 0}ms`,
                                        `Status Code: ${status}${statusText}`
                                    ];
                                },
                                afterLabel: function(context) {
                                    const index = context.dataIndex;
                                    const req = last10Requests[index];
                                    const url = req.url || 'N/A';
                                    return `URL: ${url.length > 50 ? url.substring(0, 50) + '...' : url}`;
                                },
                                labelColor: function(context) {
                                    const index = context.dataIndex;
                                    return {
                                        borderColor: statusColors[index],
                                        backgroundColor: statusColors[index]
                                    };
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Response Time (ms)',
                                color: 'rgb(78, 115, 223)',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + 'ms';
                                }
                            },
                            beginAtZero: true
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        function refreshChart() {
            initChart();
            showNotification('Yenilendi', 'Grafik güncellendi', 'success');
        }

        // Sayfa yüklendiğinde grafiği ve istatistikleri başlat
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                initChart();
                loadAnalytics();
            }, 500);
        });
    </script>
</body>

</html>