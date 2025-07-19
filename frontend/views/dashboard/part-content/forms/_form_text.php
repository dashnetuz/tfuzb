<?php
use yii\helpers\Html;

/** @var common\models\ContentText $model */
?>

<div class="form-group mb-3">
    <?= Html::label($model->getAttributeLabel('text_uz'), null, ['class' => 'form-label']) ?>
    <?= Html::textarea('ContentText[text_uz]', $model->text_uz, ['class' => 'form-control tinymce']) ?>
</div>

<div class="form-group mb-3 mt-3">
    <?= Html::label($model->getAttributeLabel('text_ru'), null, ['class' => 'form-label']) ?>
    <?= Html::textarea('ContentText[text_ru]', $model->text_ru, ['class' => 'form-control tinymce']) ?>
</div>

<div class="form-group mb-3 mt-3">
    <?= Html::label($model->getAttributeLabel('text_en'), null, ['class' => 'form-label']) ?>
    <?= Html::textarea('ContentText[text_en]', $model->text_en, ['class' => 'form-control tinymce']) ?>
</div>
