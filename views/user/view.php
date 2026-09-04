<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var string $roleName */

$this->title = 'Detail Pengguna: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Manajemen Pengguna', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>

<div class="user-view">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold m-0"><?= Html::encode($this->title) ?></h3>
            <p class="text-muted small m-0">Informasi lengkap profil dan akun pengguna.</p>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-pencil me-1"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-warning text-white']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus pengguna ini?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="bi bi-key-fill me-1"></i> Reset Password', ['reset-password', 'id' => $model->id], [
                'class' => 'btn btn-warning text-white fw-semibold',
                'data' => [
                    'confirm' => "Reset password user ini menjadi '123456'?",
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-striped table-bordered detail-view m-0'],
                'attributes' => [
                    'id',
                    'username',
                    'email:email',
                    [
                        'attribute' => 'role',
                        'label' => 'Role RBAC',
                        'format' => 'raw',
                        'value' => function () use ($roleName) {
                            return '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">'
                                . Html::encode($roleName) .
                                '</span>';
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Status Akun',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->status === User::STATUS_ACTIVE
                                ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>'
                                : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Nonaktif</span>';
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Dibuat Pada',
                        'value' => function ($model) {
                            return date('d M Y, H:i', $model->created_at);
                        },
                    ],
                    [
                        'attribute' => 'updated_at',
                        'label' => 'Diperbarui Pada',
                        'value' => function ($model) {
                            return date('d M Y, H:i', $model->updated_at);
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>