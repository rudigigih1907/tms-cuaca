<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserForm $model */
/** @var array $roles */

$this->title = 'Tambah Pengguna Baru';
$this->params['breadcrumbs'][] = ['label' => 'Manajemen Pengguna', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-create">
    <div class="mb-3">
        <h3 class="fw-bold m-0"><?= Html::encode($this->title) ?></h3>
        <p class="text-muted small m-0">Isi formulir untuk menambahkan akun baru.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
    ]) ?>
</div>