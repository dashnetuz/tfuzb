<?php
/** @var $content */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\models\Setting;

$setting = Setting::findOne(1);

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="<?= $setting && $setting->favicon ? Html::encode($setting->favicon) : '/template/assets/images/logos/favicon.png' ?>" type="image/png">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- HEADER -->
<header class="header">
    <nav class="navbar navbar-expand-lg py-0">
        <div class="container">
            <a class="navbar-brand me-0 py-0" href="/">
                <img src="<?= $setting && $setting->logo ? Html::encode($setting->logo) : '/template/assets/images/logos/logo.png' ?>" alt="Logo" />
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Bosh sahifa</a>
                    </li>
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="/site/contact">Bog'lanish</a>-->
<!--                    </li>-->
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- CONTENT -->
<main>
    <?= $content ?>
</main>

<!-- FOOTER -->
<footer class="footer-part pt-5 pb-4 text-center">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. Barcha huquqlar himoyalangan.</p>
    </div>
</footer>

<!-- Iconify CDN -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
