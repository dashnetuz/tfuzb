<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\EssayCriteria $model */
/** @var common\models\Quiz $quiz */

$this->title = $model->isNewRecord
    ? Yii::t('app', 'Yangi mezon')
    : Yii::t('app', 'Mezonni tahrirlash');
?>

<div class="container py-4">
    <h4 class="mb-4"><?= Html::encode($this->title) ?>: <?= Html::encode($quiz->title) ?></h4>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'min' => 0, 'max' => 100]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Bekor qilish'), ['essay-criteria-manage', 'quiz_id' => $quiz->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
