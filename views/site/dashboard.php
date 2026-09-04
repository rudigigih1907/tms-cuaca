<?php

use yii\helpers\Html;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var string $roleName */

$this->title = 'Dashboard Pengguna';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-dashboard">
    <!-- Header Banner -->
    <div class="p-4 mb-4 bg-primary text-white rounded-3 shadow-sm">
        <div class="container-fluid py-2">
            <h1 class="display-6 fw-bold">Selamat Datang, <?= Html::encode($user->username) ?>! 👋</h1>
            <p class="col-md-8 fs-6 mb-0 text-white-50">
                Anda masuk sebagai role <span class="badge bg-light text-primary fw-semibold fs-6 ms-1"><?= Html::encode(strtoupper($roleName)) ?></span>.
                Gunakan panel ini untuk mengelola akun Anda.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card Profil Ringkas -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-circle fs-1"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?= Html::encode($user->username) ?></h5>
                    <p class="text-muted small mb-3"><?= Html::encode($user->email) ?></p>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i> Status: Aktif
                        </span>
                    </div>

                    <div class="border-top pt-3 text-start small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Terdaftar Sejak:</span>
                            <span class="fw-semibold"><?= Yii::$app->formatter->asDate($user->created_at, 'php:d M Y') ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Terakhir Diubah:</span>
                            <span class="fw-semibold"><?= Yii::$app->formatter->asDate($user->updated_at, 'php:d M Y, H:i') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Quick Actions / Pintasan -->
        <div class="col-md-6 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold m-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Aksi Cepat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Tombol Ganti Password -->
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="text-primary mb-2">
                                        <i class="bi bi-shield-lock fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold">Keamanan Akun</h6>
                                    <p class="small text-muted mb-3">Perbarui password akun Anda secara berkala untuk menjaga keamanan.</p>
                                </div>
                                <?= Html::a('Ganti Password', ['/site/change-password'], ['class' => 'btn btn-sm btn-outline-primary fw-semibold w-100']) ?>
                            </div>
                        </div>

                        <!-- Tombol Khusus Admin (Kondisional) -->
                        <?php if (Yii::$app->user->can('admin')): ?>
                            <div class="col-sm-6">
                                <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-danger mb-2">
                                            <i class="bi bi-people fs-3"></i>
                                        </div>
                                        <h6 class="fw-bold">Manajemen User</h6>
                                        <p class="small text-muted mb-3">Kelola pengguna, tambah akun baru, dan atur hak akses RBAC.</p>
                                    </div>
                                    <?= Html::a('Buka User Management', ['/user/index'], ['class' => 'btn btn-sm btn-outline-danger fw-semibold w-100']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>