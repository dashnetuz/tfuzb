<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class DashboardAsset extends AssetBundle
{
    public $basePath = '@webroot/template/assets';
    public $baseUrl = '@web/template/assets';

    public $css = [
        'css/styles.css',
        'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
        'libs/quill/dist/quill.snow.css',
        'https://unpkg.com/@tabler/icons-webfont@latest/tabler-icons.min.css',
    ];

    public $js = [
        'libs/bootstrap/dist/js/bootstrap.bundle.min.js',
        'libs/simplebar/dist/simplebar.min.js',
        'js/theme/app.init.js',
        'js/theme/theme.js',
        'js/theme/app.min.js',
        'js/theme/sidebarmenu.js',
        'libs/apexcharts/dist/apexcharts.min.js',
        'js/dashboards/dashboard1.js',
        'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
        'libs/fullcalendar/index.global.min.js',
        'js/plugins/toastr-init.js',
        'libs/quill/dist/quill.min.js',
        'js/forms/quill-init.js',
        'libs/tinymce/tinymce.min.js',
        'js/forms/tinymce-init.js',
        'js/slugify.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];
}
