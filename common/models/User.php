<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string|null $password_hash
 * @property string $auth_key
 * @property string|null $password_reset_token
 * @property int $status
 * @property string $auth_type
 * @property string|null $auth_provider_id
 * @property string|null $verification_token
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Profile $profile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['username', 'email', 'auth_key', 'status', 'auth_type'], 'required'],
            [['status'], 'integer'],
            [['username', 'email', 'password_hash', 'password_reset_token', 'auth_provider_id', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username', 'email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['auth_provider_id', 'auth_type'], 'unique', 'targetAttribute' => ['auth_provider_id', 'auth_type']],

            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'safe'],
        ];
    }


    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    // IdentityInterface metodlari
//    public static function findIdentity($id)
//    {
//        return static::findOne($id);
//    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Agar token orqali kirish kerak bo‘lsa, bu metodni yozish kerak
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
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

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function verifyEmail()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->verification_token = null; // Tokenni yo‘q qilish
        return $this->save(false);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }


}
