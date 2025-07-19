<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use common\models\User;
use common\models\UserProfile;
use common\models\UserRole;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Ro'yxatdan o'tish, login va parolni tiklash"
 * )
 *
 * @OA\PathItem(path="/v1/auth")
 */
class AuthController extends BaseController
{
      private function translate($message)
    {
        return Yii::t('app', $message);
    }

    private function missing($field)
    {
        return $this->translate("Maydon to‘ldirilmagan: {field}", ['field' => $field]);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/register",
     *     summary="Foydalanuvchini ro'yxatdan o'tkazish",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "email", "password"},
     *             @OA\Property(property="username", type="string", example="testuser"),
     *             @OA\Property(property="email", type="string", example="test@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Foydalanuvchi yaratildi", @OA\JsonContent(@OA\Property(property="token", type="string"))),
     *     @OA\Response(response=400, description="Majburiy maydonlar to‘ldirilmagan"),
     *     @OA\Response(response=409, description="Foydalanuvchi allaqachon mavjud"),
     *     @OA\Response(response=422, description="Saqlashda xatolik")
     * )
     */
    public function actionRegister()
    {
        $body = Yii::$app->request->bodyParams;

        foreach (['username', 'email', 'password'] as $field) {
            if (empty($body[$field])) {
                Yii::$app->response->statusCode = 400;
                return ['error' => $this->missing($field)];
            }
        }

        $existingUser = User::find()
            ->where(['or', ['username' => $body['username']], ['email' => $body['email']]])
            ->one();

        if ($existingUser) {
            Yii::$app->response->statusCode = 409;
            return [
                'error' => $this->translate('Foydalanuvchi allaqachon mavjud.'),
                'details' => [
                    'username' => $existingUser->username,
                    'email' => $existingUser->email
                ]
            ];
        }

        $user = new User();
        $user->username = $body['username'];
        $user->email = $body['email'];
        $user->setPassword($body['password']);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if ($user->save()) {
            $profile = new UserProfile([
                'user_id' => $user->id,
                'firstname' => '',
                'lastname' => '',
                'avatar' => ''
            ]);
            $profile->save(false);

            $roles = ['user'];
            if ($user->id == 1) $roles[] = 'creator';

            foreach ($roles as $roleName) {
                $roleId = UserRole::getRoleIdByName($roleName);
                if ($roleId) {
                    (new UserRole([
                        'user_id' => $user->id,
                        'role_id' => $roleId,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]))->save(false);
                }
            }

            Yii::$app->response->statusCode = 201;
            return ['token' => $user->generateJwt()];
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $user->errors];
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     summary="Foydalanuvchini login qilish",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login muvaffaqiyatli", @OA\JsonContent(
     *         @OA\Property(property="token", type="string"),
     *         @OA\Property(property="role", type="string")
     *     )),
     *     @OA\Response(response=400, description="Majburiy maydonlar to‘ldirilmagan"),
     *     @OA\Response(response=401, description="Login yoki parol noto‘g‘ri")
     * )
     */
    public function actionLogin()
    {
        $body = Yii::$app->request->bodyParams;

        if (empty($body['username']) || empty($body['password'])) {
            Yii::$app->response->statusCode = 400;
            return ['error' => $this->translate('Username va parol to‘ldirilishi shart.')];
        }

        $user = User::findByUsername($body['username']);
        if ($user && $user->validatePassword($body['password'])) {
            return [
                'token' => $user->generateJwt(),
                'roles' => $user->getRoleNames()
            ];

        }

        Yii::$app->response->statusCode = 401;
        return ['error' => $this->translate('Login yoki parol noto‘g‘ri')];
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/request-password-reset",
     *     summary="Parolni tiklash uchun token olish",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="test@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Token yaratildi", @OA\JsonContent(@OA\Property(property="token", type="string"))),
     *     @OA\Response(response=400, description="Email kiritilmagan"),
     *     @OA\Response(response=404, description="Email topilmadi"),
     *     @OA\Response(response=500, description="Tokenni yaratib bo‘lmadi")
     * )
     */
    public function actionRequestPasswordReset()
    {
        $body = Yii::$app->request->bodyParams;

        if (empty($body['email'])) {
            Yii::$app->response->statusCode = 400;
            return ['error' => $this->missing('email')];
        }

        $user = User::findOne(['email' => $body['email'], 'status' => User::STATUS_ACTIVE]);

        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return ['error' => $this->translate('Bunday email topilmadi')];
        }

        $user->generatePasswordResetToken();
        if ($user->save(false)) {
            return ['token' => $user->password_reset_token];
        }

        Yii::$app->response->statusCode = 500;
        return ['error' => $this->translate('Tokenni yaratib bo‘lmadi')];
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/reset-password",
     *     summary="Token orqali parolni almashtirish",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "password"},
     *             @OA\Property(property="token", type="string", example="reset_xxx_12345678"),
     *             @OA\Property(property="password", type="string", example="newsecurepass123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Parol yangilandi", @OA\JsonContent(@OA\Property(property="message", type="string"))),
     *     @OA\Response(response=400, description="Token noto‘g‘ri yoki foydalanuvchi topilmadi"),
     *     @OA\Response(response=500, description="Parolni saqlashda xatolik yuz berdi")
     * )
     */
    public function actionResetPassword()
    {
        $body = Yii::$app->request->bodyParams;

        if (empty($body['token']) || empty($body['password'])) {
            Yii::$app->response->statusCode = 400;
            return ['error' => $this->translate('Token va yangi parol to‘ldirilishi kerak')];
        }

        $user = User::findByPasswordResetToken($body['token']);
        if (!$user) {
            Yii::$app->response->statusCode = 400;
            return ['error' => $this->translate('Token noto‘g‘ri yoki eskirgan')];
        }

        $user->setPassword($body['password']);
        $user->removePasswordResetToken();

        if ($user->save(false)) {
            return ['message' => $this->translate('Parol muvaffaqiyatli yangilandi')];
        }

        Yii::$app->response->statusCode = 500;
        return ['error' => $this->translate('Parolni saqlashda xatolik yuz berdi')];
    }
}
