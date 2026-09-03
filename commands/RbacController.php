<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

class RbacController extends Controller
{

    public function actionInit(): int
    {
        $auth = Yii::$app->authManager;

        // Reset semua data RBAC lama
        $auth->removeAll();

        // Buat Permissions (Hak Akses Spesifik)
        $accessCuaca = $auth->createPermission('accessCuaca');
        $accessCuaca->description = 'Akses Modul Cuaca (Lihat & Sync Data)';
        $auth->add($accessCuaca);

        $accessWilayah = $auth->createPermission('accessWilayah');
        $accessWilayah->description = 'Akses Modul Wilayah (Kelola Data Kelurahan/Kecamatan)';
        $auth->add($accessWilayah);

        // Buat Role 'user'
        $userRole = $auth->createRole('user');
        $userRole->description = 'Pengguna Biasa';
        $auth->add($userRole);
        
        // Role 'user' HANYA punya akses Cuaca
        $auth->addChild($userRole, $accessCuaca);

        // Buat Role 'admin'
        $adminRole = $auth->createRole('admin');
        $adminRole->description = 'Administrator System';
        $auth->add($adminRole);

        // Role 'admin' punya akses Cuaca DAN Wilayah
        $auth->addChild($adminRole, $accessCuaca);
        $auth->addChild($adminRole, $accessWilayah);

        $this->stdout("Inisialisasi RBAC Berhasil!\n");
        return ExitCode::OK;
    }
}
