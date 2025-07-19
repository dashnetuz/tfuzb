<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


/**
 * User model with JWT support
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['password_reset_token'], 'string'],
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        try {
            $decoded = JWT::decode($token, new Key(Yii::$app->params['jwtSecret'], 'HS256'));
            return static::findOne(['id' => $decoded->uid, 'status' => self::STATUS_ACTIVE]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function generateJwt()
    {
        $roles = array_map(fn($r) => $r->role->name, $this->roles);

        $payload = [
            'iss' => Yii::$app->params['jwtIssuer'],
            'aud' => Yii::$app->params['jwtAudience'],
            'iat' => time(),
            'exp' => time() + Yii::$app->params['jwtExpire'],
            'uid' => $this->id,
            'roles' => $roles,
        ];

        return JWT::encode($payload, Yii::$app->params['jwtSecret'], 'HS256');
    }




    // User.php ichida
    public function getProfile()
    {
        return $this->hasOne(\common\models\UserProfile::class, ['user_id' => 'id']);
    }

    public function getRoles()
    {
        return $this->hasMany(\common\models\UserRole::class, ['user_id' => 'id']);
    }

    public function hasRole($roleName)
    {
        return in_array($roleName, array_column($this->roles, 'role'));
    }

    public function hasPermission($permissionName)
    {
        foreach ($this->roles as $userRole) {
            if ($userRole->role && $userRole->role->permissions) {
                foreach ($userRole->role->permissions as $perm) {
                    if ($perm->name === $permissionName) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getPrimaryRoleName(): ?string
    {
        $role = $this->getRoles()
            ->with('role')
            ->orderBy(['id' => SORT_ASC]) // birinchi rol qaytadi
            ->one();

        return $role && $role->role ? $role->role->name : null;
    }
    public function getRoleNames(): array
    {
        return array_filter(array_map(
            fn($r) => $r->role->name ?? null,
            $this->roles
        ));
    }



}
