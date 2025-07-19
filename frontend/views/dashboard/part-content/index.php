<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use frontend\helpers\VideoHelper;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\Part $part */
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <?= Html::a('<i class="ti ti-plus me-1"></i>' . Yii::t('app', 'Kontent qo‘shish'), ['part-content-create', 'part_id' => $part->id], ['class' => 'btn btn-primary']) ?>

        <!-- 🔽 Test qo‘shish dropdown -->
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-list-check me-1"></i> <?= Yii::t('app', 'Test qo‘shish') ?>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <?= Html::a(Yii::t('app', 'Oddiy test (variantli)'), ['dashboard/quiz-create', 'part_id' => $part->id, 'type' => 1], ['class' => 'dropdown-item']) ?>
                </li>
                <li>
                    <?= Html::a(Yii::t('app', 'Insho (AI baholaydi)'), ['dashboard/quiz-create', 'part_id' => $part->id, 'type' => 2], ['class' => 'dropdown-item']) ?>
                </li>
            </ul>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'part-content-grid']); ?>

    <div class="row" id="sortable-body">

        <?php foreach ($dataProvider->models as $model): ?>
            <div class="col-lg-12 sortable-row mb-3" data-id="<?= $model->id ?>">
                <div class="card border shadow-sm rounded">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1 text-primary"><?= Html::encode($model->type->label) ?></h5>
                                <span class="badge bg-info">Pozitsiya: <?= $model->position ?></span>
                            </div>
                            <div class="dropdown">
                                <a class="text-muted" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical fs-5"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><?= Html::a('<i class="ti ti-list-details me-1"></i> Ko‘rish', ['part-content-preview', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                                    <li><?= Html::a('<i class="ti ti-eye me-1"></i> Tahrirlash', ['part-content-update', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                                    <li><?= Html::a('<i class="ti ti-trash me-1"></i> O‘chirish', ['part-content-delete', 'id' => $model->id], [
                                            'class' => 'dropdown-item text-danger',
                                            'data-confirm' => Yii::t('app', 'Ishonchingiz komilmi?'),
                                            'data-method' => 'post',
                                        ]) ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-3">
                            <?php
                            $content = $model->getActiveContent();
                            $typeName = $model->type->name;

                            switch ($typeName) {
                                case 'text':
                                    echo Html::tag(
                                        'p',
                                        $content && $content->getText()
                                            ? $content->getText()
                                            : '[Matn yo‘q]',
                                        ['class' => 'text-muted']
                                    );
                                    break;

                                case 'picture':
                                    echo $content && $content->file_path
                                        ? Html::img(Url::to('@web' . $content->file_path), [
                                            'class' => 'img-fluid rounded',
                                            'style' => 'width:100%',
                                        ])
                                        : Html::tag('div', '[Rasm yo‘q]', ['class' => 'text-danger']);
                                    break;

                                case 'pdf':
                                    $pdfName = $content && $content->file_path ? basename($content->file_path) : null;
                                    echo '<i class="ti ti-file-text me-2 text-danger"></i>' . Html::encode($content->title ?: '[PDF yo‘q]');
                                    break;

                                case 'video':
                                    $videoLink = $content && $content->youtube_url ? $content->youtube_url : '';
                                    $videoId = \frontend\helpers\VideoHelper::getYoutubeId($videoLink);
                                    echo $videoId
                                        ? Html::tag('div', '<iframe width="100%" height="200" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>', ['class' => 'ratio ratio-16x9'])
                                        : Html::tag('div', '[Video ID aniqlanmadi]', ['class' => 'text-warning']);
                                    break;

                                default:
                                    echo Html::tag('div', '[Nomaʼlum tur]', ['class' => 'text-warning']);
                                    break;
                            }
                            ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                <i class="ti ti-calendar me-1"></i>
                                <?= date('d.m.Y', $model->created_at) ?>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox" role="switch"
                                       data-id="<?= $model->id ?>" <?= $model->status ? 'checked' : '' ?>>
                                <label class="form-check-label"><?= Yii::t('app', 'Holat') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
    <?php Pjax::end(); ?>
</div>

<?php
$this->registerCss('.sortable-row { cursor: move; }');
$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => \yii\web\JqueryAsset::class]);

$sortUrl = Url::to(['dashboard/part-content-sort']);
$toggleUrl = Url::to(['dashboard/part-content-toggle-status']);
$csrf = Yii::$app->request->csrfToken;

$this->registerJs(<<<JS
$("#sortable-body").sortable({
    update: function() {
        let ids = $("#sortable-body .sortable-row").map(function(){ return $(this).data("id"); }).get();
        $.post("$sortUrl", {
            ids: ids,
            _csrf: "$csrf"
        });
    }
});

$(document).on("change", ".toggle-status", function() {
    let id = $(this).data('id');
    let status = $(this).is(':checked') ? 1 : 0;
    $.post("$toggleUrl", {
        id: id,
        status: status,
        _csrf: "$csrf"
    });
});
JS);
?>
