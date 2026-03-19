<!DOCTYPE html>
<html lang="tr">

<head>
    <?php
    $kod = (isset($veri['kod']) && defined('GELISTIRICI') && GELISTIRICI) ? $veri['kod'] : 404;
    $kod2 = (isset($veri['kod']) && defined('GELISTIRICI') && GELISTIRICI) ? $veri['kod'] : 'Hata';
    $baslik = (isset($veri['baslik']) && defined('GELISTIRICI') && GELISTIRICI) ? $veri['baslik'] : 'Sayfa Bulunamadı';
    $mesaj = (isset($veri['mesaj']) && defined('GELISTIRICI') && GELISTIRICI) ? $veri['mesaj'] : 'Üzgünüz, bir hata oluştu. İstediğiniz hizmet şu anda kullanılamıyor. <br> <small>Lütfen daha sonra tekrar deneyin.</small>    ';
    $Tema = Tema($kod2, [], [], true);
    echo $Tema->calistir();
    http_response_code($kod) ?>
</head>

<body class="bg-secondary-subtle">
    <div class="container-fluid pt-xl-5 pt-sm-4 d-flex align-items-center justify-content-center">
        <div class="card shadow border-0 bg-white" style="max-width: 600px; width: 100%;">
            <div class="card-body text-center p-5">
                <h1 class="display-1 text-danger fw-bold mb-3"><?= $kod ?></h1>
                <h2 class="h3 text-black fw-semibold mb-3"><?= $baslik ?></h2>
                <p class="text-dark mb-4"><?= $mesaj ?></p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <button class="btn btn-danger px-4" onclick="location.reload()">Sayfayı Yenile</button>
                    <button class="btn btn-outline-danger px-4" onclick="location.href='<?= Url_Olustur('anasayfa') ?>'">Anasayfaya Dön</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
