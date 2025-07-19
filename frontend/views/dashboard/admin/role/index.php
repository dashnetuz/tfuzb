<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var array $roles */

$this->title = Yii::t('app', 'Rollar boshqaruvi');
?>

<div class="container py-4">
    <div class="mb-4">
        <!-- Yangi rol qo‘shish formasi -->
        <div class="card p-3 mb-4">
            <h5 class="mb-3"><?= Yii::t('app', 'Yangi rol qo‘shish') ?></h5>
            <div class="input-group">
                <input type="text" id="new-role-name" class="form-control" placeholder="<?= Yii::t('app', 'Rol nomini kiriting') ?>">
                <button class="btn btn-success" id="save-new-role"><?= Yii::t('app', 'Qo‘shish') ?></button>
            </div>
        </div>
    </div>

    <?php if (!empty($roles)): ?>
        <div class="row">
            <?php foreach ($roles as $roleName): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center position-relative">
                            <h5 class="card-title"><?= Html::encode(ucfirst($roleName)) ?></h5>
                            <?= Html::a(
                                Yii::t('app', 'Permissionlarni boshqarish'),
                                Url::to(['/dashboard/manage-role-permissions', 'role' => $roleName]),
                                ['class' => 'btn btn-primary mt-3']
                            ) ?>
                            <button
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-role-btn"
                                    data-role="<?= Html::encode($roleName) ?>"
                                    title="<?= Yii::t('app', 'Rolni o‘chirish') ?>">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning"><?= Yii::t('app', 'Hozircha hech qanday rol mavjud emas.') ?></div>
    <?php endif; ?>
</div>

<?php
$createRoleUrl = Url::to(['/dashboard/create-role-api']);
$deleteRoleUrl = Url::to(['/dashboard/delete-role-api']);
$this->registerJs(<<<JS
$('#save-new-role').on('click', function() {
    var roleName = $('#new-role-name').val().trim();
    if (!roleName) {
        toastr.error('Rol nomini kiriting.');
        return;
    }

    $.ajax({
        url: '{$createRoleUrl}',
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({ name: roleName }),
        success: function(res) {
            if (res.success) {
                toastr.success(res.success);
                setTimeout(() => location.reload(), 1000);
            } else if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.error('Nomaʼlum xatolik yuz berdi.');
            }
        },
        error: function() {
            toastr.error('Server bilan aloqa xatoligi.');
        }
    });
});

// 🔥 ENTER tugmasi bosilganda ham Qo‘shishni chaqiramiz
$('#new-role-name').on('keypress', function(e) {
    if (e.which === 13) { // 13 - Enter
        $('#save-new-role').click();
    }
});

// 🗑️ Rolni o'chirish
$('.delete-role-btn').on('click', function() {
    var roleName = $(this).data('role');
    if (!confirm(roleName + ' rolini o‘chirishni xohlaysizmi?')) {
        return;
    }

    $.ajax({
        url: '{$deleteRoleUrl}',
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({ name: roleName }),
        success: function(res) {
            if (res.success) {
                toastr.success(res.success);
                setTimeout(() => location.reload(), 1000);
            } else if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.error('Nomaʼlum xatolik yuz berdi.');
            }
        },
        error: function() {
            toastr.error('Server bilan aloqa xatoligi.');
        }
    });
});
JS);
?>
