<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var array $users */
$this->title = Yii::t('app', 'Foydalanuvchilar ro‘yxati');
?>

<div class="container py-4">
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Rollar</th> <!-- Yangi ustun -->
                    <th>Status</th>
                    <th>Amallar</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= Html::encode($user['id']) ?></td>
                            <td><?= Html::encode($user['username']) ?></td>
                            <td><?= Html::encode($user['email']) ?></td>
                            <td>
                                <?php if (!empty($user['roles'])): ?>
                                    <?php foreach ($user['roles'] as $role): ?>
                                        <span class="badge bg-primary"><?= Html::encode($role) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted"><?= Yii::t('app', 'Roli yo‘q') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($user['status']) && $user['status'] == 10): ?>
                                    <span class="badge bg-success"><?= Yii::t('app', 'Active') ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= Yii::t('app', 'Inactive') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= Html::a(
                                    Yii::t('app', 'Rol boshqarish'),
                                    ['dashboard/admin-user-manage-roles', 'id' => $user['id']],
                                    ['class' => 'btn btn-sm btn-warning']
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center"><?= Yii::t('app', 'Foydalanuvchilar topilmadi.') ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
