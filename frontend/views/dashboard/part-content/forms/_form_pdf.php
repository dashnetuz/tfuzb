<?php

use yii\helpers\Html;

/** @var common\models\ContentPdf $model */
?>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'PDF fayl'), 'file_path', ['class' => 'form-label']) ?>
    <?= Html::activeFileInput($model, 'file_path', ['class' => 'form-control']) ?>
</div>

<hr>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Sarlavha (UZ)'), 'title_uz', ['class' => 'form-label']) ?>
    <?= Html::activeTextInput($model, 'title_uz', ['class' => 'form-control']) ?>
</div>
<div class="form-group">
    <?= Html::label(Yii::t('app', 'Sarlavha (RU)'), 'title_ru', ['class' => 'form-label']) ?>
    <?= Html::activeTextInput($model, 'title_ru', ['class' => 'form-control']) ?>
</div>
<div class="form-group">
    <?= Html::label(Yii::t('app', 'Sarlavha (EN)'), 'title_en', ['class' => 'form-label']) ?>
    <?= Html::activeTextInput($model, 'title_en', ['class' => 'form-control']) ?>
</div>

<hr>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Tavsif (UZ)'), 'description_uz', ['class' => 'form-label']) ?>
    <?= Html::activeTextarea($model, 'description_uz', ['class' => 'form-control', 'rows' => 3]) ?>
</div>
<div class="form-group">
    <?= Html::label(Yii::t('app', 'Tavsif (RU)'), 'description_ru', ['class' => 'form-label']) ?>
    <?= Html::activeTextarea($model, 'description_ru', ['class' => 'form-control', 'rows' => 3]) ?>
</div>
<div class="form-group">
    <?= Html::label(Yii::t('app', 'Tavsif (EN)'), 'description_en', ['class' => 'form-label']) ?>
    <?= Html::activeTextarea($model, 'description_en', ['class' => 'form-control', 'rows' => 3]) ?>
</div>
