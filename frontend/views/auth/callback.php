<?php

use yii\web\View;

$this->registerJs("
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    if (token) {
        localStorage.setItem('jwt_token', token);
        window.location.href = '/dashboard';
    }
", View::POS_READY);
?>
<p>Tizimga kirish jarayoni...</p>
