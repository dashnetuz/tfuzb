<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Quiz $model */
/** @var common\models\Part $part */

$this->title = $model->isNewRecord ? Yii::t('app', 'Test qo‘shish') : Yii::t('app', 'Testni tahrirlash');
?>

<div class="container py-4">
    <h4 class="mb-4"><?= Html::encode($this->title) ?>: <?= Html::encode($part->title_uz) ?></h4>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

    <?= $form->field($model, 'type')->dropDownList([
        1 => Yii::t('app', 'Test (variantli)'),
        2 => Yii::t('app', 'Insho (AI baholaydi)'),
    ]) ?>

    <?= $form->field($model, 'time_limit')->textInput()->hint(Yii::t('app', 'Daqiqalarda kiriting')) ?>

    <?= $form->field($model, 'pass_percent')->textInput() ?>

    <?= $form->field($model, 'max_attempt')->textInput()->hint(Yii::t('app', 'Bo‘sh qoldirilsa – cheksiz')) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Bekor qilish'), ['quiz-index', 'part_id' => $part->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
