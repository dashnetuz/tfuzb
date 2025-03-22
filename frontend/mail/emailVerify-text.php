Hello <?= $user->username ?>,

Click the link below to verify your email:

<?= Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]) ?>
