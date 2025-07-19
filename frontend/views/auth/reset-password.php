<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\ResetPasswordForm $model */
/** @var string|null $error */
/** @var string|null $success */

$this->title = Yii::t('app', 'Reset Password');
?>
<div class="col-xl-6 border-end">
    <div class="row justify-content-center py-4">
        <div class="col-lg-11">
            <div class="card-body">
                <a href="/" class="text-nowrap logo-img d-block mb-4 w-100">
                    <img src="/template/assets/images/logos/logo.svg" class="dark-logo" alt="<?= Yii::t('app', 'Logo-Dark') ?>" />
                </a>
                <h2 class="lh-base mb-4"><?= Yii::t('app', 'Create new password') ?></h2>

                <?php $form = ActiveForm::begin([
                    'id' => 'reset-password-form',
                    'action' => ['auth/reset-password'],
                    'method' => 'post',
                    'enableClientValidation' => true,
                    'enableAjaxValidation' => false,
                ]); ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= Html::encode($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= Html::encode($success) ?></div>
                <?php endif; ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'class' => 'form-control',
                    'placeholder' => Yii::t('app', 'Enter your new password'),
                ]) ?>

                <div class="d-grid mt-2">
                    <?= Html::submitButton(Yii::t('app', 'Reset Password'), [
                        'class' => 'btn btn-dark w-100 py-8 mb-4 rounded-1'
                    ]) ?>
                </div>

                <div class="d-flex align-items-center">
                    <p class="fs-12 mb-0 fw-medium"><?= Yii::t('app', 'Go back to login?') ?></p>
                    <a class="text-primary fw-bolder ms-2" href="<?= Yii::$app->urlManager->createUrl(['auth/login']) ?>">
                        <?= Yii::t('app', 'Sign In') ?>
                    </a>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
