<p>Hello <?= $user->username ?>,</p>

<p>Click the link below to verify your email:</p>

<p><a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]) ?>">
        Verify Email
    </a></p>
