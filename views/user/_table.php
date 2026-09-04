<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\UserSearch $searchModel */
/** @var array $roles */

$authManager = Yii::$app->authManager;
// Konversi list role ke array key-value untuk filter header GridView
$roleList = array_combine(array_keys($roles), array_keys($roles));
$currentUserId = Yii::$app->user->id; // Ambil ID user yang sedang login
?>

<?php Pjax::begin(['id' => 'user-pjax-grid', 'timeout' => 5000]); ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'summary' => '<div class="p-3 text-muted small">Menampilkan <b>{begin}-{end}</b> dari <b>{totalCount}</b> pengguna.</div>',
            'pager' => [
                'options' => ['class' => 'pagination pagination-sm justify-content-left my-3'],
                'linkContainerOptions' => ['class' => 'page-item'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                'prevPageLabel' => '&laquo; Prev',
                'nextPageLabel' => 'Next &raquo;',
                'firstPageLabel' => 'First',
                'lastPageLabel' => 'Last',
                'maxButtonCount' => 5,
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => '#',
                    'headerOptions' => ['width' => '50', 'class' => 'text-center']
                ],
                [
                    'attribute' => 'username',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<i class="bi bi-person-circle me-1 text-secondary"></i> ' . Html::encode($model->username);
                    },
                ],
                'email:email',
                [
                    'attribute' => 'role',
                    'label' => 'Role RBAC',
                    'filter' => $roleList, // Filter Dropdown di Header
                    'format' => 'raw',
                    'value' => function ($model) use ($authManager, $roles) {
                        $userRoles = $authManager->getRolesByUser($model->id);
                        $currentRole = !empty($userRoles) ? reset($userRoles)->name : '';

                        $options = ['' => '-- Tanpa Role --'] + array_combine(array_keys($roles), array_keys($roles));

                        return Html::dropDownList('role_select', $currentRole, $options, [
                            'class' => 'form-select form-select-sm role-dropdown',
                            'data-user-id' => $model->id,
                        ]);
                    },
                ],
                [
                    'attribute' => 'status',
                    'filter' => [10 => 'Aktif', 0 => 'Nonaktif'], // Filter Dropdown di Header
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-center', 'width' => '160'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function ($model) use ($currentUserId) {
                        $isActive = ($model->status == 10);
                        $isSelf = ($model->id == $currentUserId); // Cek apakah baris ini adalah akun sendiri

                        $badge = $isActive
                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>'
                            : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';

                        return '
                        <div class="form-check form-switch d-flex justify-content-center m-0">
                            <input class="form-check-input status-toggle" type="checkbox" role="switch" 
                                data-user-id="' . $model->id . '" ' . ($isActive ? 'checked' : '') . ' '
                            . ($isSelf ? 'disabled title="Anda tidak dapat menonaktifkan akun sendiri"' : '')
                            . 'style="cursor: ' . ($isSelf ? 'not-allowed' : 'pointer') . '; width: 2.5em; height: 1.25em;">
                        </div>
                        <small class="status-label d-block text-muted mt-1" id="status-label-' . $model->id . '">' . $badge . '</small>';
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {reset-password} {delete}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        return \yii\helpers\Url::to([$action, 'id' => $model->id]);
                    },
                    'visibleButtons' => [
                        'delete' => function ($model, $key, $index) use ($currentUserId) {
                            return $model->id !== $currentUserId;
                        },
                    ],
                    'buttons' => [
                        'reset-password' => function ($url, $model) {
                            return \yii\helpers\Html::a('<i class="bi bi-key-fill"></i>', ['reset-password', 'id' => $model->id], [
                                'class' => 'btn-reset-password me-1',
                                'title' => 'Reset Password ke 123456',
                                'data-user-id' => $model->id,
                                'data-username' => $model->username,
                                'data' => [
                                    'confirm' => "Apakah Anda yakin ingin mereset password user '{$model->username}' menjadi '123456'?",
                                    'method' => 'post',
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

<?php Pjax::end(); ?>