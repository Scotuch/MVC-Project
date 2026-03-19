<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Çekirdek Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

spl_autoload_register(function ($class) {
    try {
        static $ilk = true;
        if ($ilk) {
            require_once SISTEM . 'Library/Uygulama.php';
            require_once SISTEM . 'Library/Sistem.php';
            $ilk = false;
        }
        $file1 = SISTEM . 'Library/' . $class . '.php';
        $file2 = UYGULAMA . 'Library/' . $class . '.php';
        if (@file_exists($file1)) {
            require_once $file1;
        } elseif (@file_exists($file2)) {
            require_once $file2;
        } else {
            if (class_exists('Log')) {
                Log::Dikkat(0, "Autoload: Sınıf bulunamadı: {$class}", GELISTIRICI);
            }
        }
    } catch (Error | Exception $e) {
        Hata($e);
    }
});

/**
 * Hata Mesajı Gösterici
 *
 * Bu fonksiyon, sistemde oluşan hataları kullanıcıya detaylı ve biçimli şekilde gösterir.
 * Hata mesajı, dosya, satır, fonksiyon ve sunucu bilgilerini içerir.
 *
 * Kullanım Alanı:
 * - Geliştirme ve debug sürecinde hata takibi
 * - Üretim ortamında kullanıcıya hata bildirimi
 *
 * Teknik Detaylar:
 * - HTML tabanlı detaylı hata çıktısı üretir
 * - Sunucu ve kullanıcı bilgilerini de gösterir
 * - Çalışmayı durdurur (die)
 *
 * Kullanım Örneği:
 * try {
 *     // ... kod ...
 * } catch (Exception $e) {
 *     Hata($e);
 * }
 *
 * @param Exception|Error $a Yakalanan hata nesnesi
 * @return void
 */
function Hata($sistem_hatasi = null)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $hataSayfasiDosya = "Hata";
    $hataSayfasiKonum = defined('SISTEM_KLASOR_VIEW') ? SISTEM_KLASOR_VIEW : SISTEM . 'View';

    try {
        require_once $hataSayfasiKonum . DS . $hataSayfasiDosya . '.php';
    } catch (Error | Exception $sistem_hatasi) {
        $a = $sistem_hatasi;
        $b = $a->getTrace();
        $c = '';
        $c .= '<style>@import url(https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap);body,html,main{height:100%}*{padding:0;margin:0;border:none;text-decoration:none;list-style:none}*,::after,::before{box-sizing:border-box}:focus,main .hata details summary:focus{outline:0}html{width:100%}body{background:#efefef;color:#333;font-family:"Josefin Sans",sans-serif}main{position:fixed;top:0;left:0;width:100%;display:flex;align-items:center;flex-direction:column;z-index:99999999999;background-color:#303030;overflow:auto}main .hata-baslik{font-size:3rem;padding:2rem;color:#ca4c3f}main .hata{display:flex;width:900px;padding:1rem;background-color:#2a2a2a;flex-direction:column;border-radius:10px}main .hata details{font-size:1rem;margin:auto auto 1rem;background:#2d2d2d;border-radius:8px;position:relative;width:100%;color:#dd779c}main .hata details:last-of-type{margin-bottom:0}main .hata details span{user-select:none}main .hata details:hover{cursor:pointer}main .hata details .hata-panel{border-top:1px solid #2d2d2d;cursor:default;padding:1em;font-weight:300;line-height:1.5}main .hata details .hata-panel dl{display:grid;grid-column-gap:1.5rem;grid-row-gap:0.5rem;grid-template-columns:4.5rem 1fr;width:100%}main .hata details .hata-panel .hata-1{color:#81e1dd;word-wrap:break-word;text-align:right}main .hata details .hata-panel .hata-2{color:#f9f668;word-break:break-all;margin-bottom:0}main .hata details summary{list-style:none;padding:1em}main .hata details summary::-webkit-details-marker{display:none}main .hata details .ikon{pointer-events:none;position:absolute;top:.75em;right:1em;background:#2d2d2d}@media (max-width:1200px){main .hata-baslik{font-size:1rem}main .hata{width:90%}}</style>';
        $c .= '<body><main><span class="hata-baslik">Sistemde Hata Meydana Geldi!</span><div class="hata"><details open><summary><span>Açıklama</span><div class="ikon">▼</div></summary><div class="hata-panel"><dl><dt class="hata-1">Hata<dd class="hata-2">' . $a->getMessage() . '<dt class="hata-1">Dosya<dd class="hata-2">' . $a->getFile() . '<dt class="hata-1">Satır<dd class="hata-2">' . $a->getLine() . '</dl></div><div class="ikon">▲</div></details><details open><summary><span>Çalıştırıldığı Yer</span><div class="ikon">▼</div></summary><div class="hata-panel"><dl><dt class=hata-1>Dosya<dd class=hata-2>' . $b[0]['file'] . '<dt class=hata-1>Satır<dd class=hata-2>' . $b[0]['line'] . '<dt class=hata-1>Fonksiyon<dd class=hata-2>' . $b[0]['function'] . '</dl></div><div class="ikon">▲</div></details><details><summary><span>Kullanıcı</span><div class="ikon">▼</div></summary><div class="hata-panel"><dl><dt class=hata-1>Platform<dd class=hata-2>' . @$_SERVER['HTTP_SEC_CH_UA_PLATFORM'] . '<dt class=hata-1>Tarayıcı<dd class=hata-2>' . $_SERVER['HTTP_USER_AGENT'] . '<dt class=hata-1>Accept<dd class=hata-2>' . $_SERVER['HTTP_ACCEPT'] . '<dt class=hata-1>Kodlama<dd class=hata-2>' . $_SERVER['HTTP_ACCEPT_ENCODING'] . '<dt class=hata-1>Dil<dd class=hata-2>' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '<dt class=hata-1>Host<dd class=hata-2>' . $_SERVER['HTTP_HOST'] . '<dt class=hata-1>Bağlantı<dd class=hata-2>' . $_SERVER['HTTP_CONNECTION'] . '<dt class=hata-1>Server<dd class=hata-2>' . $_SERVER['SERVER_SOFTWARE'] . '<dt class=hata-1>Server Adı<dd class=hata-2>' . $_SERVER['SERVER_NAME'] . '<dt class=hata-1>Server Port<dd class=hata-2>' . $_SERVER['SERVER_PORT'] . '<dt class=hata-1>Kullanıcı<dd class=hata-2>' . $_SERVER['SERVER_ADMIN'] . '<dt class=hata-1>Ağ Geçidi<dd class=hata-2>' . $_SERVER['GATEWAY_INTERFACE'] . '<dt class=hata-1>Protokol<dd class=hata-2>' . $_SERVER['SERVER_PROTOCOL'] . '<dt class=hata-1>Method<dd class=hata-2>' . $_SERVER['REQUEST_METHOD'] . '<dt class=hata-1>Veri<dd class=hata-2>' . $_SERVER['QUERY_STRING'] . '<dt class=hata-1>Zaman<dd class=hata-2>' . $_SERVER['REQUEST_TIME'] . '</dl></div><div class="ikon">▲</div></details><details><summary><span>Server Bilgileri</span><div class="ikon">▼</div></summary><div class="hata-panel"><dl><dt class=hata-1>Versiyon<dd class=hata-2>' . phpversion() . '<dt class=hata-1>Zend<dd class=hata-2>' . zend_version() . '</dl></div><div class="ikon">▲</div></details></div></main></body>';
        die($c);
    }
}

/**
 * Gelişmiş Konsol Çıktısı ve Debug Fonksiyonu
 *
 * Modern ve kullanışlı bir debug aracı. Verileri güzel formatlanmış, renkli ve detaylı şekilde görüntüler.
 * Değişken tipini, dosya ve satır bilgisini, backtrace bilgilerini gösterir.
 *
 * Özellikler:
 * - Otomatik tip algılama ve renkli gösterim
 * - Çağrıldığı dosya ve satır bilgisi
 * - Hafıza kullanımı ve çalışma süresi takibi
 * - Otomatik dark/light tema uyumu (sistem temasına göre renk değişimi)
 * - Transparan arka plan (sayfa teması gözükür)
 * - Çoklu parametre desteği
 * - JSON, Array, Object ve diğer tüm türler için optimize edilmiş görünüm
 * - Browser Console ve CLI desteği
 * - Tek tıkla veri kopyalama
 *
 * Kullanım Örnekleri:
 * Konsol($degisken);                          // Basit kullanım
 * Konsol($data, $user, $config);              // Çoklu değişken
 * Konsol($arr, ['die' => true]);              // Çalışmayı durdur
 * Konsol($obj, ['tip' => 'json']);            // JSON formatında
 * Konsol($val, ['zaman' => true]);            // Zaman damgası ile
 * Konsol($var, ['etiket' => 'Debug Point']); // Özel etiket
 *
 * @param mixed ...$args Yazdırılacak değişkenler ve seçenekler
 * @return void
 */
function Konsol(...$args)
{
    static $cagriSayisi = 0;
    $cagriSayisi++;

    // Varsayılan ayarlar
    $varsayilan = [
        'die' => false,
        'tip' => 'auto',
        'zaman' => true,
        'dosya' => true,
        'hafiza' => true,
        'backtrace' => false,
        'etiket' => null
    ];

    // Son argüman ayarlar array'i mi kontrol et
    $ayarlar = $varsayilan;
    if (count($args) > 0 && is_array(end($args)) && !isset(end($args)[0])) {
        $sonArg = array_pop($args);
        if (
            isset($sonArg['die']) || isset($sonArg['tip']) || isset($sonArg['zaman']) ||
            isset($sonArg['dosya']) || isset($sonArg['hafiza']) || isset($sonArg['backtrace']) ||
            isset($sonArg['etiket'])
        ) {
            $ayarlar = array_merge($varsayilan, $sonArg);
        } else {
            array_push($args, $sonArg);
        }
    }

    // Hiç veri yoksa çık
    if (empty($args)) {
        return;
    }

    // Çağrı bilgilerini al
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $caller = $backtrace[0] ?? [];
    $dosyaYolu = $caller['file'] ?? 'Bilinmiyor';
    $satirNo = $caller['line'] ?? 0;
    $dosyaAdi = basename($dosyaYolu);

    // CLI kontrolü
    $isCLI = php_sapi_name() === 'cli';

    if ($isCLI) {
        // CLI Çıktısı
        echo "\n" . str_repeat('=', 80) . "\n";
        echo "KONSOL DEBUG #$cagriSayisi\n";
        echo str_repeat('=', 80) . "\n";

        if ($ayarlar['zaman']) {
            echo "Zaman: " . date('Y-m-d H:i:s') . "\n";
        }
        if ($ayarlar['dosya']) {
            echo "Dosya: $dosyaYolu:$satirNo\n";
        }
        if ($ayarlar['hafiza']) {
            echo "Hafıza: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB\n";
        }
        if ($ayarlar['etiket']) {
            echo "Etiket: " . $ayarlar['etiket'] . "\n";
        }

        echo str_repeat('-', 80) . "\n";

        foreach ($args as $index => $veri) {
            if (count($args) > 1) {
                echo "Parametre #" . ($index + 1) . ":\n";
            }

            switch ($ayarlar['tip']) {
                case 'json':
                    echo json_encode($veri, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
                    break;
                case 'export':
                    echo var_export($veri, true) . "\n";
                    break;
                case 'serialize':
                    echo serialize($veri) . "\n";
                    break;
                default:
                    print_r($veri);
                    echo "\n";
            }
        }

        echo str_repeat('=', 80) . "\n";
    } else {
        // Web Çıktısı
        echo '<div style="margin: 10px 0; padding: 15px; font-family: \'Consolas\', \'Monaco\', monospace; font-size: 13px; line-height: 1.5;">';

        // Başlık
        echo '<div style="margin-bottom: 12px; padding-bottom: 5px; border-bottom: 1px solid rgba(128,128,128,0.3);">';
        echo '<strong style="font-size: 14px;">🐛 KONSOL DEBUG #' . $cagriSayisi . '</strong>';

        // Etiket
        if ($ayarlar['etiket']) {
            echo ' <span style="border: 1px solid rgba(128,128,128,0.3); padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 8px;">' . htmlspecialchars($ayarlar['etiket']) . '</span>';
        }

        echo '</div>';

        // Bilgi satırı
        $bilgiler = [];
        if ($ayarlar['zaman']) {
            $bilgiler[] = '<span>⏱ ' . date('H:i:s') . '</span>';
        }
        if ($ayarlar['dosya']) {
            $bilgiler[] = '<span>📄 ' . htmlspecialchars($dosyaAdi) . ':' . $satirNo . '</span>';
        }
        if ($ayarlar['hafiza']) {
            $bilgiler[] = '<span>💾 ' . round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB</span>';
        }

        if (!empty($bilgiler)) {
            echo '<div style="font-size: 11px; margin-bottom: 10px; opacity: 0.8;">';
            echo implode(' | ', $bilgiler);
            echo ' | <button onclick="konsolKopyala(\'konsol-' . $cagriSayisi . '\', event)" style="border: 1px solid rgba(128,128,128,0.5); padding: 2px 8px; border-radius: 3px; cursor: pointer; font-size: 10px; font-family: inherit; vertical-align: middle; background: transparent;">📋 Kopyala</button>';
            echo '</div>';
        }

        // Veriler
        foreach ($args as $index => $veri) {
            if (count($args) > 1) {
                echo '<div style="margin-top: 8px; padding: 5px; border-left: 3px solid rgba(128,128,128,0.5);">';
                echo '<strong>Parametre #' . ($index + 1) . '</strong> <span style="opacity: 0.6;">(' . gettype($veri) . ')</span>';
                echo '</div>';
            }

            echo '<pre id="konsol-' . $cagriSayisi . '-' . $index . '" style="margin: 8px 0; padding: 10px; border: 1px solid rgba(128,128,128,0.2); border-radius: 4px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word;">';

            switch ($ayarlar['tip']) {
                case 'json':
                    echo '<code>';
                    echo htmlspecialchars(json_encode($veri, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    echo '</code>';
                    break;
                case 'export':
                    echo '<code>';
                    echo htmlspecialchars(var_export($veri, true));
                    echo '</code>';
                    break;
                case 'serialize':
                    echo '<code>';
                    echo htmlspecialchars(serialize($veri));
                    echo '</code>';
                    break;
                case 'dump':
                    ob_start();
                    var_dump($veri);
                    echo '<code>';
                    echo htmlspecialchars(ob_get_clean());
                    echo '</code>';
                    break;
                default:
                    // Auto tip - akıllı formatlama
                    if (is_null($veri)) {
                        echo '<code style="font-style: italic; opacity: 0.6;">NULL</code>';
                    } elseif (is_bool($veri)) {
                        echo '<code style="font-weight: bold;">' . ($veri ? 'TRUE' : 'FALSE') . '</code>';
                    } elseif (is_string($veri)) {
                        echo '<code>"' . htmlspecialchars($veri) . '"</code>';
                    } elseif (is_numeric($veri)) {
                        echo '<code>' . $veri . '</code>';
                    } elseif (is_array($veri) || is_object($veri)) {
                        echo '<code>';
                        print_r($veri);
                        echo '</code>';
                    } else {
                        echo '<code>';
                        print_r($veri);
                        echo '</code>';
                    }
            }

            echo '</pre>';
        }

        // Backtrace
        if ($ayarlar['backtrace']) {
            echo '<details style="margin-top: 10px; padding: 8px; border: 1px solid rgba(128,128,128,0.2); border-radius: 4px; cursor: pointer;">';
            echo '<summary style="font-weight: bold; user-select: none;">📋 Stack Trace</summary>';
            echo '<pre style="margin: 8px 0; padding: 8px; font-size: 11px; overflow-x: auto;"><code>';

            $fullBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($fullBacktrace); // Konsol fonksiyonunu atla

            foreach ($fullBacktrace as $i => $trace) {
                $dosya = isset($trace['file']) ? basename($trace['file']) : 'internal';
                $satir = $trace['line'] ?? '?';
                $func = $trace['function'] ?? '';
                $class = $trace['class'] ?? '';
                $type = $trace['type'] ?? '';

                echo "#$i $class$type$func() $dosya:$satir\n";
            }

            echo '</code></pre></details>';
        }

        echo '</div>';

        // Kopyalama JavaScript fonksiyonu
        echo '<script>';
        echo 'function konsolKopyala(prefiksi, e) {';
        echo '  try {';
        echo '    let icerik = "";';
        echo '    let index = 0;';
        echo '    let element;';
        echo '    while (element = document.getElementById(prefiksi + "-" + index)) {';
        echo '      if (icerik) icerik += "\\n\\n";';
        echo '      icerik += element.textContent || element.innerText;';
        echo '      index++;';
        echo '    }';
        echo '    if (!icerik && (element = document.getElementById(prefiksi + "-0"))) {';
        echo '      icerik = element.textContent || element.innerText;';
        echo '    }';
        echo '    let btn = e.currentTarget || e.target;';
        echo '    if (navigator.clipboard && navigator.clipboard.writeText) {';
        echo '      navigator.clipboard.writeText(icerik).then(function() {';
        echo '        let eskiMetin = btn.textContent;';
        echo '        btn.textContent = "✓ Kopyalandı";';
        echo '        btn.style.background = "#4ec9b0";';
        echo '        setTimeout(function() {';
        echo '          btn.textContent = eskiMetin;';
        echo '          btn.style.background = "";';
        echo '        }, 2000);';
        echo '      });';
        echo '    } else {';
        echo '      let temp = document.createElement("textarea");';
        echo '      temp.value = icerik;';
        echo '      temp.style.position = "fixed";';
        echo '      temp.style.opacity = "0";';
        echo '      document.body.appendChild(temp);';
        echo '      temp.select();';
        echo '      document.execCommand("copy");';
        echo '      document.body.removeChild(temp);';
        echo '      let eskiMetin = btn.textContent;';
        echo '      btn.textContent = "✓ Kopyalandı";';
        echo '      btn.style.background = "#4ec9b0";';
        echo '      setTimeout(function() {';
        echo '        btn.textContent = eskiMetin;';
        echo '        btn.style.background = "";';
        echo '      }, 2000);';
        echo '    }';
        echo '  } catch(err) {';
        echo '    console.error("Kopyalama hatası:", err);';
        echo '    alert("Kopyalama başarısız: " + err.message);';
        echo '  }';
        echo '}';
        echo '</script>';
    }

    if ($ayarlar['die']) {
        die();
    }
}
