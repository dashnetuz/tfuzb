<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class AuthAsset extends AssetBundle
{
    public $basePath = '@webroot/template/assets';
    public $baseUrl = '@web/template/assets';

    public $css = [
        'css/styles.css',
        'https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css',
    ];

    public $js = [
        'libs/bootstrap/dist/js/bootstrap.bundle.min.js',
        'libs/simplebar/dist/simplebar.min.js',
//        'js/theme/app.init.js',
        'js/theme/theme.js',
        'js/theme/app.min.js',
        'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
        'https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
