<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Setting;

/** @var yii\web\View $this */
/** @var frontend\models\LoginForm $model */
/** @var string|null $error */
/** @var string|null $success */

$setting = Setting::findOne(1);
$this->title = Yii::t('app', 'Sign In');
?>
<div class="col-xl-6 border-end">
    <div class="row justify-content-center py-4">
        <div class="col-lg-11">
            <div class="card-body">
                <a href="/" class="text-nowrap logo-img d-block mb-4 w-100">
                    <img src="<?= $setting && $setting->logo_bottom ? Html::encode($setting->logo_bottom) : '/template/assets/images/logos/logo.svg' ?>" class="dark-logo" alt="Logo-img" />
                </a>
                <h2 class="lh-base mb-4"><?= Yii::t('app', "Let's get you signed in") ?></h2>

                <div class="d-grid mb-4">
                    <button id="google-login-btn" type="button"
                            class="btn btn-white shadow-sm text-dark link-primary border fw-semibold d-flex align-items-center justify-content-center rounded-1 py-6 w-100">
                        <img src="/template/assets/images/svgs/google-icon.svg" alt="google" class="img-fluid me-2" width="18" height="18">
                        <span class="d-none d-xxl-inline-flex"><?= Yii::t('app', "Sign in with") ?></span>&nbsp; Google
                    </button>
                </div>

                <div class="position-relative text-center my-4">
                    <p class="mb-0 fs-12 px-3 d-inline-block bg-body z-index-5 position-relative"><?= Yii::t('app', "Or sign in with email") ?></p>
                    <span class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
                </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'action' => ['auth/login'],
                    'method' => 'post',
                    'enableClientValidation' => true,
                ]); ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= Html::encode($success) ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= Html::encode($error) ?></div>
                <?php endif; ?>

                <?= $form->field($model, 'username')->textInput([
                    'class' => 'form-control',
                    'placeholder' => Yii::t('app', 'Enter your username'),
                ]) ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'class' => 'form-control',
                    'placeholder' => Yii::t('app', 'Enter your password'),
                ]) ?>

                <div class="d-grid mt-2">
                    <?= Html::submitButton(Yii::t('app', 'Sign In'), ['class' => 'btn btn-dark w-100 py-8 mb-4 rounded-1']) ?>
                </div>

                <div class="d-flex flex-column align-items-center text-center mt-2">
                    <a class="text-primary fw-bolder fs-6" href="<?= Yii::$app->urlManager->createUrl(['auth/request-password-reset']) ?>">
                        <?= Yii::t('app', 'Forgot your password?') ?>
                    </a>
                    <p class="fs-12 mb-0 fw-medium mt-2"><?= Yii::t('app', "Don't have an account yet?") ?></p>
                    <a class="text-primary fw-bolder ms-2" href="<?= Yii::$app->urlManager->createUrl(['auth/register']) ?>">
                        <?= Yii::t('app', 'Sign Up Now') ?>
                    </a>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<!-- GOOGLE LOGIN SDK -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const googleLoginButton = document.getElementById('google-login-btn');

        if (googleLoginButton) {
            googleLoginButton.addEventListener('click', function () {
                const client = google.accounts.oauth2.initCodeClient({
                    client_id: '<?= Yii::$app->params['googleClientId'] ?>',
                    scope: 'openid email profile',
                    ux_mode: 'popup',
                    callback: (response) => {
                        if (response.code) {
                            window.location.href = '<?= Yii::$app->urlManager->createUrl(['auth/social-callback']) ?>?provider=google&code=' + response.code;
                        } else {
                            alert('<?= Yii::t('app', 'Google login muvaffaqiyatsiz. Iltimos qayta urinib ko‘ring.') ?>');
                        }
                    }
                });
                client.requestCode();
            });
        }
    });
</script>
