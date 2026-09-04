<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserForm $model */
/** @var app\models\User $user */
/** @var array $roles */

$this->title = 'Edit Pengguna: ' . $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Manajemen Pengguna', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['update', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="user-update">
    <div class="mb-3">
        <h3 class="fw-bold m-0"><?= Html::encode($this->title) ?></h3>
        <p class="text-muted small m-0">Perbarui data pengguna.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
    ]) ?>
</div>