<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Cuaca $model */

$this->title = 'Galeri Foto Cuaca - ' . Yii::$app->formatter->asDatetime($model->local_datetime, 'php:d M Y H:i');
$this->params['breadcrumbs'][] = ['label' => 'Data Cuaca', 'url' => ['index', 'adm4' => $model->kode_adm4]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cuaca-view">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('&laquo; Kembali ke Daftar', ['index', 'adm4' => $model->kode_adm4], ['class' => 'btn btn-secondary']) ?>
    </div>

    <!-- DETAIL INFORMASI CUACA -->
    <div class="row mb-4">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'kode_adm4',
                    [
                        'attribute' => 'local_datetime',
                        'value' => Yii::$app->formatter->asDatetime($model->local_datetime, 'php:d F Y - H:i WIB'),
                    ],
                    'kondisi_cuaca',
                    [
                        'attribute' => 'suhu',
                        'value' => $model->suhu ? $model->suhu . ' °C' : '-',
                    ],
                    [
                        'attribute' => 'kelembapan',
                        'value' => $model->kelembapan ? $model->kelembapan . ' %' : '-',
                    ],
                ],
            ]) ?>
        </div>

        <!-- FORM UPLOAD MULTIPLE GAMBAR -->
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <strong>Tambah Foto Baru</strong>
                </div>
                <div class="card-body">
                    <?= Html::beginForm(['cuaca/upload-detail', 'id' => $model->id], 'post', [
                        'enctype' => 'multipart/form-data',
                    ]) ?>
                    <div class="mb-3">
                        <label class="form-label">Pilih Satu atau Beberapa Gambar:</label>
                        <?= Html::activeFileInput($model, 'imageFiles[]', [
                            'multiple' => true,
                            'accept' => 'image/*',
                            'class' => 'form-control',
                            'required' => true,
                        ]) ?>
                        <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB per file (Max 5 file).</small>
                    </div>
                    <?= Html::submitButton('<i class="bi bi-cloud-upload"></i> Unggah Gambar', [
                        'class' => 'btn btn-success w-100',
                    ]) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>

    <!-- GALERI FOTO GRID -->
    <?php Pjax::begin(['id' => 'galeri-pjax']); ?>
    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title mb-0">Daftar Foto Tersimpan (<?= count($model->galeri) ?>)</h3>
        </div>
        <div class="card-body">
            <?php if (empty($model->galeri)): ?>
                <div class="alert alert-warning mb-0">Belum ada foto yang diunggah untuk data cuaca ini.</div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                    <?php foreach ($model->galeri as $foto): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <a href="<?= $foto->getImageUrl() ?>" target="_blank">
                                    <img src="<?= $foto->getImageUrl() ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Foto Cuaca">
                                </a>
                                <div class="card-body p-2 text-center bg-light">
                                    <small class="text-muted d-block mb-2"><?= Yii::$app->formatter->asDatetime($foto->created_at, 'php:d/m/Y H:i') ?></small>
                                    <?= Html::a('<i class="bi bi-trash"></i> Hapus Foto', ['delete-gambar', 'id' => $foto->id], [
                                        'class' => 'btn btn-sm btn-outline-danger w-100',
                                        'data-confirm' => 'Yakin ingin menghapus foto ini?',
                                        'data-method' => 'post',
                                        'data-pjax' => '1',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php Pjax::end(); ?>

</div>