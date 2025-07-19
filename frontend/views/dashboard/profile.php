<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var array $user */
/** @var array $resetPasswordForm */


$this->title = Yii::t('app', 'Account Settings');

?>
<div class="card overflow-hidden">
    <div class="card-body p-0">
        <img src="/template/assets/images/backgrounds/profilebg.jpg" alt="matdash-img" class="img-fluid">
        <div class="row align-items-center">
            <div class="col-lg-4 order-lg-1 order-2">
                <div class="d-flex align-items-center justify-content-around m-4">
                    <div class="text-center">
                        <i class="ti ti-file-description fs-6 d-block mb-2"></i>
                        <h4 class="mb-0 fw-semibold lh-1">0</h4>
                        <p class="mb-0 ">Posts</p>
                    </div>
                    <div class="text-center">
                        <i class="ti ti-user-circle fs-6 d-block mb-2"></i>
                        <h4 class="mb-0 fw-semibold lh-1">0</h4>
                        <p class="mb-0 ">Followers</p>
                    </div>
                    <div class="text-center">
                        <i class="ti ti-user-check fs-6 d-block mb-2"></i>
                        <h4 class="mb-0 fw-semibold lh-1">0</h4>
                        <p class="mb-0 ">Following</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-n3 order-lg-2 order-1">
                <div class="mt-n5">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="d-flex align-items-center justify-content-center round-110">
                            <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden round-100">
                                <img src="<?= Html::encode($user->avatar ?? '/template/assets/images/profile/user-1.jpg') ?>" alt="matdash-img" class="w-100 h-100">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h5 class="mb-0"><?= $user->firstname?> <?= $user->lastname?></h5>
                        <p class="mb-0"><?= $user->email?></p>
<!--                        <p class="mb-0">--><?php //= implode(', ', $user['roles']) ?><!--</p>-->
                    </div>
                </div>
            </div>
            <div class="col-lg-4 order-last">
                <ul class="list-unstyled d-flex align-items-center justify-content-center justify-content-lg-end my-3 mx-4 pe-4 gap-3">
                    <li>
                        <a class="d-flex align-items-center justify-content-center btn btn-primary p-2 fs-4 rounded-circle" href="javascript:void(0)" width="30" height="30">
                            <i class="ti ti-brand-facebook"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-secondary d-flex align-items-center justify-content-center p-2 fs-4 rounded-circle" href="javascript:void(0)">
                            <i class="ti ti-brand-dribbble"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-danger d-flex align-items-center justify-content-center p-2 fs-4 rounded-circle" href="javascript:void(0)">
                            <i class="ti ti-brand-youtube"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <!-- Tabs -->
    <ul class="nav nav-pills user-profile-tab" id="pills-tab" role="tablist">
        <!-- Tablar (Account, Notifications, Bills, Security) -->
        <!-- Sen bergan koddagi tablar shu yerda -->
    </ul>

    <div class="card-body">
        <div class="tab-content" id="pills-tabContent">
            <!-- ACCOUNT TAB -->
            <div class="tab-pane fade show active" id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab">

                <div class="row">
                    <!-- Avatar o'zgartirish qismi -->
                    <div class="col-lg-6">
                        <div class="card w-100 border">
                            <div class="card-body p-4">
                                <h4 class="card-title"><?= Yii::t('app', 'Change Profile Picture') ?></h4>
                                <div class="text-center">

                                    <img src="<?= Html::encode($user->avatar ?? '/template/assets/images/profile/user-1.jpg') ?>" alt="avatar" class="img-fluid rounded-circle mb-3" width="120" height="120">

                                    <div class="d-flex justify-content-center gap-3 mt-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#avatarUploadModal">
                                            <?= Yii::t('app', 'Upload New') ?>
                                        </button>
                                    </div>

                                    <p class="mt-3"><?= Yii::t('app', 'Allowed JPG, GIF or PNG. Max size of 20 MB.') ?></p>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="avatarUploadModal" tabindex="-1" aria-labelledby="avatarUploadModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header d-flex align-items-center">
                                    <h4 class="modal-title" id="avatarUploadModalLabel"><?= Yii::t('app', 'Upload Avatar') ?></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card">

                                        <div class="card-body">
                                            <?php $form = ActiveForm::begin([
                                                'id' => 'avatar-upload-form',
                                                'action' => ['dashboard/avatar-upload'],
                                                'options' => ['enctype' => 'multipart/form-data'],
                                            ]); ?>

                                            <div class="text-center">
                                                <input type="file" name="avatar" accept="image/*" class="form-control mb-3" required>

                                                <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
                                            </div>

                                            <?php ActiveForm::end(); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shaxsiy ma'lumotlarni yangilash -->
                    <div class="col-lg-6">
                        <div class="card w-100 border">
                            <div class="card-body p-4">
                                <h4 class="card-title"><?= Yii::t('app', 'Personal Details') ?></h4>
                                <p class="card-subtitle mb-4"><?= Yii::t('app', 'Update your personal details') ?></p>

                                <?php $form = ActiveForm::begin([
                                    'id' => 'profile-update-form',
                                    'action' => ['dashboard/profile-update'],
                                    'enableAjaxValidation' => false,
                                ]); ?>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <?= $form->field($user, 'firstname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Enter firstname')]) ?>
                                    </div>
                                    <div class="col-lg-12">
                                        <?= $form->field($user, 'lastname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Enter lastname')]) ?>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 d-flex justify-content-end gap-3">
                                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                                </div>

                                <?php ActiveForm::end(); ?>


                            </div>
                        </div>
                    </div>

                    <!-- Parolni o'zgartirish -->
                    <div class="col-lg-12">
                        <div class="card w-100 border">
                            <div class="card-body p-4">
                                <h4 class="card-title"><?= Yii::t('app', 'Change Password') ?></h4>
                                <p class="card-subtitle mb-4"><?= Yii::t('app', 'To change your password please confirm here') ?></p>

                                <?php $form = ActiveForm::begin([
                                    'id' => 'reset-password-form',
                                    'action' => ['dashboard/reset-password'],
                                ]); ?>

                                <?= $form->field($resetPasswordForm, 'current_password')->passwordInput(['placeholder' => Yii::t('app', 'Enter current password')]) ?>

                                <?= $form->field($resetPasswordForm, 'new_password')->passwordInput(['placeholder' => Yii::t('app', 'Enter new password')]) ?>

                                <?= $form->field($resetPasswordForm, 'confirm_password')->passwordInput(['placeholder' => Yii::t('app', 'Confirm new password')]) ?>

                                <div class="col-12 mt-4 d-flex justify-content-end gap-3">
                                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                                </div>

                                <?php ActiveForm::end(); ?>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Notifications / Bills / Security tab-content ni keyinchalik o'zgartiramiz -->

        </div>
    </div>
</div>




