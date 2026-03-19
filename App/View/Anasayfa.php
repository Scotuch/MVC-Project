<!DOCTYPE html>
<html lang="tr">

<head>
    <?php $Tema = Tema('Ana Sayfa', [], [], true);
    echo $Tema->calistir() ?>
</head>

<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mt-4">Hoşgeldiniz!</h1>
                    <p class="lead">Bu, varsayılan ana sayfa görünümüdür. Kendi içeriğinizi eklemek için bu dosyayı düzenleyebilirsiniz.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
