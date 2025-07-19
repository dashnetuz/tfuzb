<?php

namespace frontend\components;

use Yii;
use yii\base\Widget;

class AlertWidget extends Widget
{
    public function run()
    {
        $session = Yii::$app->session;

        // Har doim toastr konfiguratsiyasini o'rnatamiz
        $this->registerToastrOptions();

        foreach (['success', 'error', 'warning', 'info'] as $type) {
            if ($session->hasFlash($type)) {
                $message = $session->getFlash($type);
                $this->registerToastr($type, $message);
            }
        }
    }

    protected function registerToastrOptions()
    {
        $js = <<<JS
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "3000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "swing",
    "showMethod": "slideDown",
    "hideMethod": "slideUp"
};
JS;
        Yii::$app->view->registerJs($js, \yii\web\View::POS_READY);
    }

    protected function registerToastr($type, $message)
    {
        $title = ucfirst($type);

        $js = <<<JS
toastr["{$type}"]("{$message}", "{$title}");
JS;
        Yii::$app->view->registerJs($js, \yii\web\View::POS_READY);
    }
}
