<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $provinsiId */
/** @var string|null $kabupatenId */
/** @var string|null $kecamatanId */
/** @var string|null $kelurahanId */
/** @var array $listProvinsi */
/** @var array $listKabupaten */
/** @var array $listKecamatan */
/** @var array $listKelurahan */

$this->title = 'Prakiraan Cuaca Berdasarkan Wilayah';

$provinsiId  = $provinsiId ?? null;
$kabupatenId = $kabupatenId ?? null;
$kecamatanId = $kecamatanId ?? null;
$kelurahanId = $kelurahanId ?? null;
?>

<div class="cuaca-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'cuaca-wilayah-pjax',
        'enablePushState' => true,
        'timeout' => 5000,
    ]); ?>

    <!-- FORM SELEKSI DROPDOWN NAMA WILAYAH -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Pilih Wilayah</strong>
        </div>
        <div class="card-body">
            <?= Html::beginForm(['cuaca/index'], 'get', ['data-pjax' => true, 'id' => 'form-filter-wilayah']) ?>

            <div class="row g-3">
                <!-- Dropdown Provinsi -->
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Provinsi</label>
                    <?= Html::dropDownList('provinsi_id', $provinsiId, $listProvinsi ?? [], [
                        'prompt' => '-- Pilih Provinsi --',
                        'class' => 'form-select form-control',
                        'onchange' => '$(this).closest("form").submit();'
                    ]) ?>
                </div>

                <!-- Dropdown Kabupaten/Kota -->
                <?php if (!empty($provinsiId)): ?>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Kota / Kabupaten</label>
                        <?= Html::dropDownList('kabupaten_id', $kabupatenId, $listKabupaten ?? [], [
                            'prompt' => '-- Pilih Kota/Kabupaten --',
                            'class' => 'form-select form-control',
                            'onchange' => '$(this).closest("form").submit();'
                        ]) ?>
                    </div>
                <?php endif; ?>

                <!-- Dropdown Kecamatan -->
                <?php if (!empty($kabupatenId)): ?>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Kecamatan</label>
                        <?= Html::dropDownList('kecamatan_id', $kecamatanId, $listKecamatan ?? [], [
                            'prompt' => '-- Pilih Kecamatan --',
                            'class' => 'form-select form-control',
                            'onchange' => '$(this).closest("form").submit();'
                        ]) ?>
                    </div>
                <?php endif; ?>

                <!-- Dropdown Kelurahan/Desa -->
                <?php if (!empty($kecamatanId)): ?>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Kelurahan / Desa</label>
                        <?= Html::dropDownList('kelurahan_id', $kelurahanId, $listKelurahan ?? [], [
                            'prompt' => '-- Pilih Kelurahan --',
                            'class' => 'form-select form-control',
                            'onchange' => '$(this).closest("form").submit();'
                        ]) ?>
                    </div>
                <?php endif; ?>

                <!-- DROPDOWN FILTER TANGGAL (Hanya muncul jika kelurahan sudah dipilih) -->
                <?php if (!empty($kelurahanId)): ?>
                    <div class="col-md-2">
                        <label class="form-label font-weight-bold text-success">Pilih Tanggal</label>
                        <?= Html::dropDownList('tanggal', $tanggal, $listTanggal ?? [], [
                            'prompt' => '-- Semua Tanggal --',
                            'class' => 'form-select form-control border-success',
                            'onchange' => '$(this).closest("form").submit();'
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>

            <?= Html::endForm() ?>

            <!-- INFORMASI & TOMBOL SYNC / CETAK PDF -->
            <?php if (!empty($kelurahanId)): ?>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Kode Wilayah Terpilih (adm4):</strong>
                        <span class="badge bg-info text-dark fs-6"><?= Html::encode($kelurahanId) ?></span>
                        <span class="ms-2 text-muted">(<?= Html::encode($listKelurahan[$kelurahanId] ?? '') ?>)</span>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- TOMBOL CETAK PDF (Hanya aktif jika tanggal spesifik dipilih) -->
                        <?php if (!empty($tanggal)): ?>
                            <?= Html::a('Cetak Laporan PDF', [
                                'cuaca/export-pdf',
                                'kelurahan_id' => $kelurahanId,
                                'tanggal' => $tanggal
                            ], [
                                'class' => 'btn btn-danger',
                                'target' => '_blank',
                                'data-pjax' => '0', // PERINGATAN: Harus '0' agar Pjax tidak mencegat proses download PDF
                            ]) ?>
                        <?php endif; ?>

                        <!-- TOMBOL TARIK DATA BMKG -->
                        <?= Html::beginForm(['cuaca/sync'], 'post', ['data-pjax' => true, 'class' => 'd-inline']) ?>
                        <?= Html::hiddenInput('adm4', $kelurahanId) ?>
                        <?= Html::submitButton('Tarik Data BMKG', [
                            'class' => 'btn btn-success',
                        ]) ?>
                        <?= Html::endForm() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TABEL GRIDVIEW HASIL PRAKIRAAN CUACA -->
    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title mb-0">Prakiraan Cuaca Tersimpan</h3>
        </div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
                'emptyText' => empty($kelurahanId)
                    ? 'Silakan pilih Wilayah sampai tingkat Kelurahan/Desa di atas.'
                    : 'Belum ada data cuaca untuk wilayah ini. Silakan klik tombol "Tarik / Update Data BMKG".',
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
                        'header' => 'No',
                        'headerOptions' => ['style' => 'width: 50px;', 'class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'local_datetime',
                        'label' => 'Waktu Prakiraan',
                        'value' => fn($model) => Yii::$app->formatter->asDatetime($model->local_datetime),
                    ],
                    [
                        'attribute' => 'analysis_date',
                        'label' => 'Analysis Date',
                        'value' => fn($model) => Yii::$app->formatter->asDatetime($model->analysis_date),
                    ],
                    [
                        'attribute' => 'kondisi_cuaca',
                        'value' => fn($model) => $model->kondisi_cuaca ?? '-',
                    ],
                    [
                        'attribute' => 'suhu',
                        'value' => fn($model) => $model->suhu !== null ? $model->suhu . ' °C' : '-',
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'kelembapan',
                        'value' => fn($model) => $model->kelembapan !== null ? $model->kelembapan . ' %' : '-',
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'kecepatan_angin',
                        'value' => fn($model) => $model->kecepatan_angin !== null ? $model->kecepatan_angin . ' km/j' : '-',
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'arah_angin',
                        'value' => fn($model) => $model->arah_angin ?? '-',
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <?php Pjax::end(); ?>
</div>