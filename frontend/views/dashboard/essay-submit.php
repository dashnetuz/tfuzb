<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \yii\web\View $this */
/** @var \common\models\Part $part */
/** @var \common\models\QuizEssaySubmission $model */

$this->title = Yii::t('app', 'Essayni topshirish');
$this->params['breadcrumbs'][] = ['label' => $part->lesson->title, 'url' => ['lesson/view', 'id' => $part->lesson_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-4">
    <h3><?= Html::encode($part->title) ?></h3>

    <div class="card mt-4">
        <div class="card-body">
<!--            <p>--><?php //= Yii::t('app', 'Quyidagi maydonga o‘z fikrlaringizni yozing (500-800 so‘z):') ?><!--</p>-->

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'essay_text')->textarea(['rows' => 12]) ?>

            <div class="form-group mt-3">
                <?= Html::submitButton(Yii::t('app', 'Essayni topshirish'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
