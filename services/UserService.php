<?php

namespace app\services;

use Yii;
use app\models\User;
use yii\web\NotFoundHttpException;

class UserService
{
    public function getAllUsers(): array
    {
        return User::find()->orderBy(['id' => SORT_DESC])->all();
    }

    public function getUserById(int $id): ?User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Pengguna tidak ditemukan.');
    }

    public function createUser(array $data, string $roleName = 'user'): array
    {
        $user = new User();
        $user->username = $data['username'] ?? '';
        $user->email = $data['email'] ?? '';
        $user->status = $data['status'] ?? 10; // Default: 10 (STATUS_ACTIVE)

        if (!empty($data['password'])) {
            $user->setPassword($data['password']);
            $user->generateAuthKey();
        }

        if ($user->save()) {
            // Assign Role Pilihan
            $this->assignRoleToUser($user->id, $roleName);

            return ['success' => true, 'message' => "User '{$user->username}' berhasil dibuat."];
        }

        return ['success' => false, 'errors' => $user->getErrors()];
    }

    public function updateUser(int $id, array $data): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        $user->username = $data['username'] ?? $user->username;
        $user->email = $data['email'] ?? $user->email;
        $user->status = $data['status'] ?? $user->status;

        if (!empty($data['password'])) {
            $user->setPassword($data['password']);
        }

        if ($user->save()) {
            return ['success' => true, 'message' => "Data user '{$user->username}' berhasil diperbarui."];
        }

        return ['success' => false, 'errors' => $user->getErrors()];
    }

    public function toggleStatus(int $id): array
    {
        if (Yii::$app->user->id == $id) {
            return [
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri!'
            ];
        }

        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        $user->status = ($user->status == 10) ? 0 : 10;

        if ($user->save(false)) {
            $statusLabel = ($user->status == 10) ? 'diaktifkan' : 'dinonaktifkan';
            return [
                'success' => true,
                'status' => $user->status,
                'message' => "User '{$user->username}' berhasil {$statusLabel}."
            ];
        }

        return ['success' => false, 'message' => 'Gagal mengubah status user.'];
    }

    public function updateRole(int $userId, string $newRoleName): array
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($newRoleName);

        if (!$role) {
            return ['success' => false, 'message' => 'Role tidak ditemukan.'];
        }

        $auth->revokeAll($userId);
        $auth->assign($role, $userId);

        return ['success' => true, 'message' => 'Role berhasil diperbarui.'];
    }

    private function assignRoleToUser(int $userId, string $roleName): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        if ($role) {
            $auth->revokeAll($userId);
            $auth->assign($role, $userId);
        }
    }

    public function deleteUser(int $id): array
    {
        if (Yii::$app->user->id == $id) {
            return [
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri!'
            ];
        }

        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        Yii::$app->authManager->revokeAll($user->id);
        $user->delete();

        return ['success' => true, 'message' => 'User berhasil dihapus.'];
    }

    public function getUserDetail(int $id): array
    {
        $user = $this->getUserById($id);

        $userRoles = Yii::$app->authManager->getRolesByUser($user->id);
        $roleName = !empty($userRoles) ? reset($userRoles)->name : '-';

        return [
            'user' => $user,
            'roleName' => $roleName,
        ];
    }

    public function resetPassword(int $id, string $newPassword = '123456'): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        $user->setPassword($newPassword);
        $user->generateAuthKey();

        if ($user->save(false)) {
            return [
                'success' => true,
                'message' => "Password untuk user '{$user->username}' berhasil di-reset menjadi '{$newPassword}'."
            ];
        }

        return ['success' => false, 'message' => 'Gagal mereset password user.'];
    }
}
