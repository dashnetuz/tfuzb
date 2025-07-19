<?php

namespace frontend\components;

use Yii;
use yii\base\Widget;

class AlertAjaxWidget extends Widget
{
    public function run()
    {
        $js = <<<JS
function showAjaxAlert(response) {
    if (!response || typeof response !== 'object') {
        return;
    }

    if (response.success) {
        toastr.success(response.success, "Success");
    } else if (response.error) {
        toastr.error(response.error, "Error");
    } else if (response.warning) {
        toastr.warning(response.warning, "Warning");
    } else if (response.info) {
        toastr.info(response.info, "Info");
    }
}

JS;

        Yii::$app->view->registerJs($js, \yii\web\View::POS_HEAD);
    }
}
