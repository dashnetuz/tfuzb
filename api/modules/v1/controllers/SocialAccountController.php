<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\httpclient\Client;
use common\models\User;
use common\models\UserProfile;
use common\models\UserRole;
use common\models\Role;
use common\models\RolePermission;
use common\models\Permission;
use common\models\SocialAccount;
use yii\authclient\clients\Google;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Social",
 *     description="Google orqali autentifikatsiya"
 * )
 *
 * @OA\PathItem(path="/v1/social")
 */
class SocialAccountController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    private function getGoogleTokenData($code)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://oauth2.googleapis.com/token')
            ->setData([
                'code' => $code,
                'client_id' => getenv('GOOGLE_CLIENT_ID'),
                'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => getenv('FRONT_URL_LOGIN'),
                'grant_type' => 'authorization_code',
            ])
            ->send();

        return $response->isOk ? $response->data : null;
    }

    private function assignDefaultPermissionsToUserRole($user)
    {
        $userRole = Role::findOne(['name' => 'user']);
        if (!$userRole) {
            Yii::error("'user' roli topilmadi.", __METHOD__);
            return;
        }

        $permissions = [
            'can_view_profile_me',
            'can_reset_password_me',
            'can_update_profile_me',
            'can_upload_avatar_me',
        ];

        foreach ($permissions as $permName) {
            $permission = Permission::findOne(['name' => $permName]);
            if (!$permission) {
                // Permission mavjud bo'lmasa, uni yaratamiz
                $permission = new Permission([
                    'name' => $permName,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                $permission->save(false);
            }

            // RolePermission mavjudligini tekshirib keyin qo‘shamiz
            if (!RolePermission::find()->where([
                'role_id' => $userRole->id,
                'permission_id' => $permission->id,
            ])->exists()) {
                (new RolePermission([
                    'role_id' => $userRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]))->save(false);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/social/login",
     *     summary="Google orqali login",
     *     tags={"Social"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"provider", "code"},
     *             @OA\Property(property="provider", type="string", example="google"),
     *             @OA\Property(property="code", type="string", example="4/0AX4XfWh...")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Muvaffaqiyatli JWT token", @OA\JsonContent(@OA\Property(property="token", type="string"))),
     *     @OA\Response(response=400, description="Token yoki provider noto‘g‘ri"),
     *     @OA\Response(response=500, description="Serverda xatolik")
     * )
     */
    public function actionLogin()
    {
        $body = Yii::$app->request->bodyParams;
        $provider = $body['provider'] ?? null;
        $code = $body['code'] ?? null;

        if ($provider !== 'google' || !$code) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Noto‘g‘ri provider yoki code yuborilmagan'];
        }

        try {
            $tokenData = $this->getGoogleTokenData($code);

            if (!$tokenData || empty($tokenData['access_token'])) {
                Yii::$app->response->statusCode = 400;
                return ['error' => 'Google token olishda xatolik'];
            }

            $client = new Google([
                'clientId' => getenv('GOOGLE_CLIENT_ID'),
                'clientSecret' => getenv('GOOGLE_CLIENT_SECRET'),
            ]);

            $client->setAccessToken([
                'token' => $tokenData['access_token'],
                'params' => $tokenData,
            ]);

            $userAttributes = $client->getUserAttributes();

            $email = $userAttributes['email'] ?? null;
            $clientId = $userAttributes['id'] ?? null;

            if (!$email || !$clientId) {
                Yii::$app->response->statusCode = 400;
                return ['error' => 'Email yoki ID topilmadi'];
            }

            $account = SocialAccount::findOne(['provider' => 'google', 'client_id' => $clientId]);
            if (!$account) {
                $user = User::findOne(['email' => $email]) ?? new User();
                if ($user->isNewRecord) {
                    $user->username = explode('@', $email)[0] . '_' . time();
                    $user->email = $email;
                    $user->setPassword(Yii::$app->security->generateRandomString(16));
                    $user->generateAuthKey();
                    $user->status = User::STATUS_ACTIVE;
                    $user->save(false);

                    (new UserProfile([
                        'user_id' => $user->id,
                        'firstname' => $userAttributes['given_name'] ?? '',
                        'lastname' => $userAttributes['family_name'] ?? '',
                        'avatar' => '',
                    ]))->save(false);

                    // Rollarni tayinlash
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

                    // --- Faqat yangi foydalanuvchiga permission tayinlash ---
                    $this->assignDefaultPermissionsToUserRole($user);
                }

                (new SocialAccount([
                    'user_id' => $user->id,
                    'provider' => 'google',
                    'client_id' => $clientId,
                    'data' => json_encode($userAttributes),
                    'created_at' => time(),
                ]))->save(false);
            } else {
                $user = $account->user;
            }

            return ['token' => $user->generateJwt()];

        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Serverda xatolik: ' . $e->getMessage()];
        }
    }
}
