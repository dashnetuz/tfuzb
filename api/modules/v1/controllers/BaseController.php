<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use common\models\RolePermission;
use common\models\Role;
use common\models\Permission;

class BaseController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // CORS uchun
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'], // agar faqat frontend bo‘lsa: ['http://localhost:8181']
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type', 'Accept-Language'],
            ],
        ];

        // JSON javoblar uchun
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        // Tilni header yoki query'dan o'qish
        $lang = Yii::$app->request->get('lang')
            ?? Yii::$app->request->getHeaders()->get('Accept-Language')
            ?? 'uz'; // default

        // Faqat mavjud tillarni qo‘llab-quvvatlash
        if (in_array($lang, ['uz', 'ru', 'en'])) {
            Yii::$app->language = $lang;
        }

        //file_put_contents('/tmp/lang.log', "Lang: " . Yii::$app->language . "\n", FILE_APPEND);

        // OPTIONS so‘rovlar uchun CORS
        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', '*');
            Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
            Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept-Language');
            Yii::$app->end();
        }

        return parent::beforeAction($action);
    }


    /**
     * JWT dan rollarni olish
     * @return string[]
     */
    protected function getRolesFromToken(): array
    {
        $decoded = $this->decodeJwtToken();
        return isset($decoded->roles) && is_array($decoded->roles) ? $decoded->roles : [];
    }

    /**
     * JWT token ichida berilgan rol bormi?
     * @param string $roleName
     * @return bool
     */
    protected function hasRoleInToken(string $roleName): bool
    {
        return in_array($roleName, $this->getRolesFromToken());
    }

    /**
     * JWT dan user ID ni olish
     * @return int|null
     */
    protected function getUserIdFromToken(): ?int
    {
        $decoded = $this->decodeJwtToken();
        return isset($decoded->uid) ? (int)$decoded->uid : null;
    }

    /**
     * JWT ni dekodlash — umumiy metod
     * @return object|null
     * @throws UnauthorizedHttpException agar JWT noto‘g‘ri bo‘lsa
     */
    private function decodeJwtToken(): ?object
    {
        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            throw new UnauthorizedHttpException("Avtorizatsiya tokeni topilmadi yoki noto‘g‘ri formatda.");
        }

        try {
            return JWT::decode($matches[1], new Key(Yii::$app->params['jwtSecret'], 'HS256'));
        } catch (\Throwable $e) {
            throw new UnauthorizedHttpException("Yaroqsiz yoki muddati o‘tgan token.");
        }
    }

    /**
     * Foydalanuvchining permission huquqini tekshirish
     * @param string $permissionName
     * @return bool
     */
    protected function hasPermission(string $permissionName): bool
    {
        $roles = $this->getRolesFromToken();

        if (empty($roles)) {
            return false;
        }

        return RolePermission::find()
            ->alias('rp')
            ->innerJoin(['r' => Role::tableName()], 'r.id = rp.role_id')
            ->innerJoin(['p' => '{{%permission}}'], 'p.id = rp.permission_id')
            ->where(['r.name' => $roles, 'p.name' => $permissionName])
            ->exists();
    }

    /**
     * Permission talab qilinadi, bo‘lmasa 403 chiqaradi
     * @param string $permissionName
     * @throws ForbiddenHttpException
     */
    protected function requirePermission(string $permissionName): void
    {
        if (!Permission::findOne(['name' => $permissionName])) {
            // Avtomatik permission yaratish
            $perm = new Permission();
            $perm->name = $permissionName;
            $perm->created_at = time();
            $perm->updated_at = time();
            $perm->save();
        }

        if (!$this->hasPermission($permissionName)) {
            throw new ForbiddenHttpException("Sizda '{$permissionName}' huquqi mavjud emas.");
        }
    }
}
