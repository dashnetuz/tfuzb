<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'] ?? 8],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            echo "<pre>";
            print_r($this->getErrors()); // Xatolarni ekranga chiqarish
            echo "</pre>";
            exit;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->auth_type = 'local';
        $user->status = User::STATUS_INACTIVE; // Statusni qo'shish
        $user->created_at = time();
        $user->updated_at = time();

        if (!$user->save()) {
            echo "<pre>";
            print_r($user->getErrors()); // Saqlashdagi xatolarni ekranga chiqarish
            echo "</pre>";
            exit;
        }

        if (!$this->sendEmail($user)) {
            die('Email yuborishda xatolik yuz berdi.');
        }

        return true;
    }


    /**
     * Sends confirmation email to user
     * @param User $user user model to which email should be sent
     * @return bool whether the email was sent
     */
//    protected function sendEmail($user)
//    {
//        return Yii::$app->mailer->compose(
//            ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
//            ['user' => $user]
//        )
//            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
//            ->setTo($this->email)
//            ->setSubject('Account registration at ' . Yii::$app->name)
//            ->send();
//    }
    protected function sendEmail($user)
    {
        Yii::info("Email yuborildi: " . $this->email, 'signup');
        return true; // Email jo‘natish jarayoni o‘tkazib yuboriladi
    }

}
