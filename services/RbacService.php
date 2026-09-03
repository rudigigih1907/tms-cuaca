<?php

namespace app\services;

use Yii;
use app\models\User;

class RbacService
{
    /**
     * Assign Role ke User
     */
    public function assignRole(int $userId, string $roleName): bool
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        
        if (!$role) {
            return false;
        }

        // Revoke role lama jika ada
        $auth->revokeAll($userId);
        
        // Assign role baru
        return $auth->assign($role, $userId) !== null;
    }

    /**
     * Ambil Role Utama dari User
     */
    public function getUserRole(int $userId): ?string
    {
        $roles = Yii::$app->authManager->getRolesByUser($userId);
        return !empty($roles) ? array_key_first($roles) : null;
    }
}