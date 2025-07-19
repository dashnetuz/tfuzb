<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;
use yii\filters\auth\HttpBearerAuth;
use OpenApi\Annotations as OA;
use common\models\User;
use common\models\UserProfile;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Foydalanuvchi profili bilan ishlash"
 * )
 */
class UserController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/v1/user/profile",
     *     summary="Foydalanuvchi profilini olish",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Foydalanuvchi maʼlumotlari",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function actionProfile()
    {
        $this->requirePermission('can_view_profile_me');

        $user = Yii::$app->user->identity;
        $profile = $user->profile;

        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'firstname' => $profile->firstname ?? '',
            'lastname' => $profile->lastname ?? '',
            'avatar' => $profile->avatar ?? '',
            'roles' => array_map(fn($r) => $r->role->name, $user->roles)
        ];
    }

    /**
     * @OA\Post(
     *     path="/v1/user/reset-password",
     *     summary="Parolni almashtirish",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password"},
     *             @OA\Property(property="current_password", type="string"),
     *             @OA\Property(property="new_password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parol muvaffaqiyatli yangilandi",
     *         @OA\JsonContent(@OA\Property(property="message", type="string"))
     *     )
     * )
     */
    public function actionResetPassword()
    {
        $this->requirePermission('can_reset_password_me');

        $body = Yii::$app->request->bodyParams;
        $user = Yii::$app->user->identity;

        if (!$user->validatePassword($body['current_password'] ?? '')) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Eski parol noto‘g‘ri'];
        }

        $user->setPassword($body['new_password']);
        $user->generateAuthKey();

        if ($user->save()) {
            return ['message' => 'Parol muvaffaqiyatli yangilandi'];
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $user->errors];
    }

    /**
     * @OA\Put(
     *     path="/v1/user/profile/update",
     *     summary="Profil maʼlumotlarini yangilash (firstname, lastname)",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil yangilandi",
     *         @OA\JsonContent(@OA\Property(property="message", type="string"))
     *     )
     * )
     */
    public function actionUpdateProfile()
    {
        $this->requirePermission('can_update_profile_me');

        $user = Yii::$app->user->identity;
        $profile = $user->profile;

        if (!$profile) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Profil topilmadi'];
        }

        $data = Yii::$app->request->bodyParams;

        $profile->firstname = $data['firstname'] ?? $profile->firstname;
        $profile->lastname = $data['lastname'] ?? $profile->lastname;

        if (!$profile->validate()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $profile->getErrors()];
        }

        if ($profile->save(false)) {
            return ['message' => 'Profil yangilandi'];
        }

        throw new ServerErrorHttpException('Saqlashda xatolik yuz berdi');
    }

    /**
     * @OA\Post(
     *     path="/v1/user/avatar-upload",
     *     summary="Avatar rasm yuklash",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"avatar"},
     *                 @OA\Property(property="avatar", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar yuklandi",
     *         @OA\JsonContent(@OA\Property(property="url", type="string"))
     *     )
     * )
     */
    public function actionAvatarUpload()
    {
        $this->requirePermission('can_upload_avatar_me');

        $user = Yii::$app->user->identity;
        $profile = $user->profile;

        if (!$profile) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Profil topilmadi'];
        }

        $uploadedFile = UploadedFile::getInstanceByName('avatar');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('Avatar fayli yuborilmadi');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'heic'];
        if (!in_array(strtolower($uploadedFile->extension), $allowedExtensions)) {
            throw new BadRequestHttpException('Faqat rasm formatlari: jpg, jpeg, png, webp, heic');
        }

        if ($uploadedFile->size > 20 * 1024 * 1024) {
            throw new BadRequestHttpException('Rasm hajmi 20MB dan oshmasligi kerak');
        }

        $dirPath = Yii::getAlias('@app/web/uploads/avatars/');
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0775, true);
        }

        if ($profile->avatar && file_exists(Yii::getAlias('@app/web') . parse_url($profile->avatar, PHP_URL_PATH))) {
            @unlink(Yii::getAlias('@app/web') . parse_url($profile->avatar, PHP_URL_PATH));
        }

        $fileName = 'avatar_' . $user->id . '_' . date('Ymd_His') . '.' . $uploadedFile->extension;
        $filePath = $dirPath . $fileName;

        if ($uploadedFile->saveAs($filePath)) {
            $baseUrl = Yii::$app->params['baseUrl'];
            $profile->avatar = $baseUrl . '/uploads/avatars/' . $fileName;
            $profile->save(false);
            return ['url' => $profile->avatar];
        }

        throw new ServerErrorHttpException('Faylni saqlab bo‘lmadi');
    }
}
