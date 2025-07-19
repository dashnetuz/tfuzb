<?php

namespace frontend\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class BreadcrumbWidget extends Widget
{
    public $homeUrl = ['dashboard/index'];
    public $icon = 'solar:home-2-line-duotone';

    public function run()
    {
        $breadcrumbs = Yii::$app->view->params['breadcrumbs'] ?? [];
        $title = Html::encode($this->view->title ?? 'Title');

        $items = [];

        // Bosh sahifa (Home) icon bilan
        $items[] = Html::tag('li',
            Html::a(
                Html::tag('iconify-icon', '', [
                    'icon' => $this->icon,
                    'class' => 'fs-6'
                ]),
                Yii::$app->urlManager->createUrl($this->homeUrl),
                ['class' => 'text-muted text-decoration-none d-flex']
            ),
            ['class' => 'breadcrumb-item d-flex align-items-center']
        );

        // Har bir breadcrumb elementi
        foreach ($breadcrumbs as $i => $crumb) {
            if (is_array($crumb)) {
                $items[] = Html::tag('li',
                    Html::a(Html::encode($crumb['label']), $crumb['url']),
                    ['class' => 'breadcrumb-item']
                );
            } else {
                // Oxirgi element — bu sahifa sarlavhasi
                $items[] = Html::tag('li',
                    Html::tag('span', Html::encode($crumb), [
                        'class' => 'badge fw-medium fs-2 bg-primary-subtle text-primary'
                    ]),
                    ['class' => 'breadcrumb-item active', 'aria-current' => 'page']
                );
            }
        }

        // To‘liq html structure
        return Html::tag('div',
            Html::tag('div',
                Html::tag('div',
                    Html::tag('div',
                        Html::tag('div',
                            Html::tag('div',
                                Html::tag('h4', $title, ['class' => 'mb-4 mb-sm-0 card-title']) .
                                Html::tag('nav',
                                    Html::tag('ol', implode('', $items), ['class' => 'breadcrumb']),
                                    ['aria-label' => 'breadcrumb', 'class' => 'ms-auto']
                                ),
                                ['class' => 'd-sm-flex align-items-center justify-space-between']
                            ),
                            ['class' => 'col-12']
                        ),
                        ['class' => 'row align-items-center']
                    ),
                    ['class' => 'card card-body py-3']
                )
            )
        );
    }
}
