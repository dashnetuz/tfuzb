<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/template/assets';
    public $baseUrl = '@web/template/assets';

    public $css = [
        'css/style.min.css',
        'libs/owl.carousel/dist/assets/owl.carousel.min.css',
        'libs/owl.carousel/dist/assets/owl.theme.default.min.css',
        'libs/aos/dist/aos.css',
        'libs/simplebar/dist/simplebar.min.css',
        'libs/bootstrap-icons/font/bootstrap-icons.css',
        'libs/feather-icons/dist/feather.css',
    ];

    public $js = [
        'libs/jquery/dist/jquery.min.js',
        'libs/bootstrap/dist/js/bootstrap.bundle.min.js',
        'libs/simplebar/dist/simplebar.min.js',
        'libs/aos/dist/aos.js',
        'libs/owl.carousel/dist/owl.carousel.min.js',
        'js/app.min.js',
        'js/app.init.js',
        'js/theme.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
