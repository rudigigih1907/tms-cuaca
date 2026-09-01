<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['username', 'email', 'password', 'password_repeat'], 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Username ini sudah digunakan.'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Email ini sudah terdaftar.'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Password tidak cocok.'],
        ];
    }

    public function register()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
