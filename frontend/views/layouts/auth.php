<?php

use frontend\assets\AuthAsset;
use yii\helpers\Html;

AuthAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
</head>

<body>
<?php $this->beginBody(); ?>
<div class="preloader">
    <img src="/template/assets/images/logos/favicon.png" alt="<?= Yii::t('app', 'Loading') ?>" class="lds-ripple img-fluid" />
</div>
<div id="main-wrapper">
    <div class="position-relative overflow-hidden auth-bg min-vh-100 w-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100 my-5 my-xl-0">
                <div class="col-md-9 d-flex flex-column justify-content-center">
                    <div class="card mb-0 bg-body auth-login m-auto w-100">
                        <div class="row gx-0">
                            <!-- Part 1 -->
                            <?= $content ?>
                            <!-- Part 2 -->
                            <div class="col-xl-6 d-none d-xl-block">
                                <div class="row justify-content-center align-items-start h-100">
                                    <div class="col-lg-9">
                                        <div id="auth-login" class="carousel slide auth-carousel mt-5 pt-4" data-bs-ride="carousel">
                                            <div class="carousel-indicators">
                                                <button type="button" data-bs-target="#auth-login" data-bs-slide-to="0" class="active" aria-current="true" aria-label="<?= Yii::t('app', 'Slide') ?> 1"></button>
                                                <button type="button" data-bs-target="#auth-login" data-bs-slide-to="1" aria-label="<?= Yii::t('app', 'Slide') ?> 2"></button>
                                                <button type="button" data-bs-target="#auth-login" data-bs-slide-to="2" aria-label="<?= Yii::t('app', 'Slide') ?> 3"></button>
                                            </div>
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                    <div class="d-flex align-items-center justify-content-center w-100 h-100 flex-column gap-9 text-center">
                                                        <img src="/template/assets/images/backgrounds/login-side.png" alt="<?= Yii::t('app', 'Login side image') ?>" width="300" class="img-fluid" />
                                                        <h4 class="mb-0"><?= Yii::t('app', 'Feature Rich 3D Charts') ?></h4>
                                                        <p class="fs-12 mb-0"><?= Yii::t('app', 'Lorem ipsum 3D charts') ?></p>
                                                        <a href="javascript:void(0)" class="btn btn-primary rounded-1"><?= Yii::t('app', 'Learn More') ?></a>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <div class="d-flex align-items-center justify-content-center w-100 h-100 flex-column gap-9 text-center">
                                                        <img src="/template/assets/images/backgrounds/login-side.png" alt="<?= Yii::t('app', 'Login side image') ?>" width="300" class="img-fluid" />
                                                        <h4 class="mb-0"><?= Yii::t('app', 'Feature Rich 2D Charts') ?></h4>
                                                        <p class="fs-12 mb-0"><?= Yii::t('app', 'Lorem ipsum 2D charts') ?></p>
                                                        <a href="javascript:void(0)" class="btn btn-primary rounded-1"><?= Yii::t('app', 'Learn More') ?></a>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <div class="d-flex align-items-center justify-content-center w-100 h-100 flex-column gap-9 text-center">
                                                        <img src="/template/assets/images/backgrounds/login-side.png" alt="<?= Yii::t('app', 'Login side image') ?>" width="300" class="img-fluid" />
                                                        <h4 class="mb-0"><?= Yii::t('app', 'Feature Rich 1D Charts') ?></h4>
                                                        <p class="fs-12 mb-0"><?= Yii::t('app', 'Lorem ipsum 1D charts') ?></p>
                                                        <a href="javascript:void(0)" class="btn btn-primary rounded-1"><?= Yii::t('app', 'Learn More') ?></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
