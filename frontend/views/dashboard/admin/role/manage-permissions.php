<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var string $role */

$this->title = Yii::t('app', 'Permissionlarni boshqarish: {role}', ['role' => ucfirst($role)]);

$updatePermissionsUrl = Url::to(['dashboard/update-role-permissions-api']);
$allPermissionsUrl = Url::to(['dashboard/all-permissions']);
$rolePermissionsUrl = Url::to(['dashboard/role-permissions', 'role' => $role]);
$backUrl = Url::to(['dashboard/admin-roles']);

$saveLoadingText = Yii::t('app', 'Saqlanmoqda...');
$saveButtonText = Yii::t('app', 'Saqlash');
?>
<div class="container py-4">
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <h5><?= Yii::t('app', 'Rolga biriktirilgan permissionlar') ?></h5>
            <ul id="assigned-permissions" class="list-group min-vh-50 border p-2"></ul>
        </div>
        <div class="col-md-6">
            <h5><?= Yii::t('app', 'Barcha mavjud permissionlar') ?></h5>
            <ul id="available-permissions" class="list-group min-vh-50 border p-2"></ul>
        </div>
    </div>

    <div class="mt-4 d-flex gap-3">
        <?= Html::button(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-primary', 'id' => 'save-permissions']) ?>
        <?= Html::a(Yii::t('app', 'Orqaga qaytish'), $backUrl, ['class' => 'btn btn-success']) ?>
    </div>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js', ['depends' => \yii\web\JqueryAsset::class]);
?>

<?php
$this->registerJs(<<<JS
var updatePermissionsUrl = '{$updatePermissionsUrl}';
var allPermissionsUrl = '{$allPermissionsUrl}';
var rolePermissionsUrl = '{$rolePermissionsUrl}';
var role = '{$role}';

let assigned = document.getElementById('assigned-permissions');
let available = document.getElementById('available-permissions');

function loadPermissions() {
    $('#assigned-permissions').html('<li class="list-group-item text-muted">Yuklanmoqda...</li>');
    $('#available-permissions').html('<li class="list-group-item text-muted">Yuklanmoqda...</li>');

    Promise.all([
        $.get(allPermissionsUrl),
        $.get(rolePermissionsUrl)
    ]).then(function([all, assignedPerms]) {
        $('#assigned-permissions').empty();
        $('#available-permissions').empty();

        if (Array.isArray(all)) {
            all.forEach(function(p) {
                $('#available-permissions').append(
                    `<li class="list-group-item d-flex align-items-center" data-permission="\${p.name}">
                        <span style="cursor: grab; margin-right: 8px;">&#9776;</span> \${p.name}
                    </li>`
                );
            });
        }

        if (Array.isArray(assignedPerms)) {
            assignedPerms.forEach(function(name) {
                const item = $(`[data-permission="\${name}"]`, '#available-permissions');
                if (item.length) {
                    $('#assigned-permissions').append(item.clone());
                    item.remove();
                }
            });
        }
    });
}

loadPermissions();

Sortable.create(assigned, { group: 'permissions', animation: 150 });
Sortable.create(available, { group: 'permissions', animation: 150 });

$('#save-permissions').on('click', function() {
    const permissions = [];
    $('#assigned-permissions .list-group-item').each(function() {
        permissions.push($(this).data('permission'));
    });

    const \$btn = $(this); // <-- JAVASCRIPT o'zgaruvchi \$ bilan qochirilgan

    \$btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> {$saveLoadingText}');

    $.ajax({
        url: updatePermissionsUrl,
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify({ role: role, permissions: permissions }),
        success: function(res) {
            if (res && res.message) {
                toastr.success(res.message);
            } else {
                toastr.success('Permissionlar muvaffaqiyatli yangilandi.');
            }
            loadPermissions();
        },
        error: function(xhr) {
            let errorMessage = 'Xatolik yuz berdi.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            toastr.error(errorMessage);
        },
        complete: function() {
            \$btn.prop('disabled', false).html('{$saveButtonText}');
        }
    });
});


JS);
?>
