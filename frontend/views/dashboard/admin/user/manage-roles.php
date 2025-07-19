<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var int $userId */
/** @var string $username */
/** @var string $email */
/** @var array $assignedRoles */
/** @var array $availableRoles */

$this->title = Yii::t('app', "Foydalanuvchiga rol berish:  {username} ({email})", [
    'username' => $username,
    'email' => $email,
]);

// 🔥 PHP tarafda URL larni avval oling
$assignUrl = Url::to(['dashboard/assign-role-api']);
$removeUrl = Url::to(['dashboard/remove-role-api']);
$backUrl = Url::to(['dashboard/admin-user']);
?>

    <div class="container py-4">
        <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5><?= Yii::t('app', 'Foydalanuvchiga biriktirilgan rollar') ?></h5>
                <ul id="assigned-roles" class="list-group min-vh-50 border p-2">
                    <?php foreach ($assignedRoles as $role): ?>
                        <li class="list-group-item" data-role="<?= Html::encode($role) ?>">
                            <?= Html::encode(ucfirst($role)) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-6">
                <h5><?= Yii::t('app', 'Barcha mavjud rollar') ?></h5>
                <ul id="available-roles" class="list-group min-vh-50 border p-2">
                    <?php foreach ($availableRoles as $role): ?>
                        <li class="list-group-item" data-role="<?= Html::encode($role) ?>">
                            <?= Html::encode(ucfirst($role)) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="mt-4">
            <?= Html::a(Yii::t('app', 'Orqaga qaytish'), $backUrl, [
                'class' => 'btn btn-success',
            ]) ?>
        </div>
    </div>

<?php
// 🔥 SortableJS kutubxonasini yuklaymiz
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js', ['depends' => \yii\web\JqueryAsset::class]);

// 🔥 JavaScript kodni yuklaymiz
$this->registerJs("
var assignUrl = '{$assignUrl}';
var removeUrl = '{$removeUrl}';

const assigned = document.getElementById('assigned-roles');
const available = document.getElementById('available-roles');

Sortable.create(assigned, {
    group: 'roles',
    animation: 150,
    onAdd: function (evt) {
        let role = evt.item.getAttribute('data-role');
        commonAjaxRequest(assignUrl, role);
    }
});

Sortable.create(available, {
    group: 'roles',
    animation: 150,
    onAdd: function (evt) {
        let role = evt.item.getAttribute('data-role');
        commonAjaxRequest(removeUrl, role);
    }
});

function commonAjaxRequest(url, role) {
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            user_id: {$userId},
            role: role
        },
        success: function(res) {
            showAjaxAlert(res); // ✅ Backend success bo'lsa toastr ko'rsat
        },
        error: function(xhr) {
            if (xhr.status === 401 || xhr.status === 403) {
                toastr.error('Sessiya tugagan. Iltimos qayta login qiling.');
                setTimeout(function() {
                    window.location.href = '/auth/login'; // 🔥 login pagega yuboramiz
                }, 2000);
            } else {
                toastr.error('Server bilan aloqa xatosi.');
            }
        }
    });
}


");
?>