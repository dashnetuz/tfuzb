<?php

/** @var yii\web\View $this */

$this->title = 'Ruxsat yo‘q';

?>

<div class="text-center py-5">
    <h1 class="display-4 text-danger">403</h1>
    <h2 class="mb-4"><?= Yii::t('app', 'Sahifaga ruxsat berilmagan') ?></h2>
    <p class="mb-4"><?= Yii::t('app', 'Kechirasiz, sizda bu sahifaga kirish uchun ruxsat yo‘q.') ?></p>
    <a href="/" class="btn btn-primary"><?= Yii::t('app', 'Bosh sahifaga qaytish') ?></a>
</div>
