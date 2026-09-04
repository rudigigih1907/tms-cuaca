<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\UserSearch $searchModel */
/** @var array $roles */

$this->title = 'Manajemen Pengguna';
$this->params['breadcrumbs'][] = $this->title;

// Daftarkan file JS eksternal
$this->registerJsFile('@web/js/user-index.js', [
    'depends' => [\yii\web\JqueryAsset::class],
    'position' => View::POS_END,
]);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="fw-bold m-0"><?= Html::encode($this->title) ?></h3>
    </div>
    <?= Html::a('<i class="bi bi-person-plus me-1"></i> Tambah User Baru', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<!-- Alert Notifikasi Toast / Banner -->
<div id="ajax-alert" class="alert alert-dismissible fade show d-none mb-3" role="alert">
    <span id="ajax-alert-message"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Container dengan data-attribute untuk passing URL ke JS -->
<div id="user-grid-container"
    data-toggle-status-url="<?= Url::to(['user/toggle-status']) ?>"
    data-change-role-url="<?= Url::to(['user/change-role']) ?>">

    <!-- PANGGIL FILE PARTIAL TABEL -->
    <?= $this->render('_table', [
        'dataProvider' => $dataProvider,
        'searchModel'  => $searchModel,
        'roles'        => $roles,
    ]) ?>
</div>