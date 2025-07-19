<?php

use yii\helpers\Html;

$this->title = 'Xatolik yuz berdi';
?>
<div class="position-relative overflow-hidden min-vh-100 w-100 d-flex align-items-center justify-content-center">
    <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
            <div class="col-lg-6 text-center">
                <img src="<?= Yii::getAlias('@web/template/assets/images/backgrounds/errorimg.svg') ?>" alt="error-img" class="img-fluid" width="400">
                <h1 class="fw-semibold mb-4 fs-1">Oops!!!</h1>
                <h4 class="fw-semibold mb-4">
                    <?= Html::encode($message ?? 'Nomaʼlum xatolik yuz berdi.') ?>
                </h4>
                <a class="btn btn-primary" href="<?= Yii::$app->homeUrl ?>">Bosh sahifaga qaytish</a>
            </div>
        </div>
    </div>
</div>
