<?php

use yii\helpers\Html;

/** @var common\models\ContentPicture $model */
?>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Rasm fayl'), 'file_path', ['class' => 'form-label']) ?>
    <?= Html::activeFileInput($model, 'file_path', ['class' => 'form-control']) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Alt matn'), 'alt', ['class' => 'form-label']) ?>
    <?= Html::activeTextInput($model, 'alt', ['class' => 'form-control']) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Sarlavha (Caption)'), 'caption', ['class' => 'form-label']) ?>
    <?= Html::activeTextInput($model, 'caption', ['class' => 'form-control']) ?>
</div>
