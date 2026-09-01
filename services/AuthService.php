<?php

namespace app\services;

use Yii;
use app\models\User;
use app\models\RegisterForm;
use app\models\LoginForm;

class AuthService
{
    /**
     * Memproses registrasi user baru
     */
    public function register(RegisterForm $form): ?User
    {
        if (!$form->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $form->username;
        $user->email = $form->email;
        $user->password_hash = Yii::$app->security->generatePasswordHash($form->password);
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->status = User::STATUS_ACTIVE;
        $user->created_at = time();
        $user->updated_at = time();

        if ($user->save()) {
            return $user;
        }
        return null;
    }

    /**
     * Memproses otentikasi login
     */
    public function login(LoginForm $form): bool
    {
        if (!$form->validate()) {
            return false;
        }

        $user = User::findByUsername($form->username);
        if (!$user || !$user->validatePassword($form->password)) {
            $form->addError('password', 'Username atau password salah.');
            return false;
        }

        $duration = $form->rememberMe ? 3600 * 24 * 30 : 0;
        return Yii::$app->user->login($user, $duration);
    }

    /**
     * Memproses logout
     */
    public function logout(): bool
    {
        return Yii::$app->user->logout();
    }
}
