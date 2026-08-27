<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $kelurahanId */

$kelurahanId = $kelurahanId ?? null;
?>
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
                // KOLOM INDIKATOR JUMLAH GAMBAR & TOMBOL VIEW
                [
                    'label' => 'Galeri Foto',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 160px;', 'class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        $total = count($model->galeri);
                        $badge = $total > 0
                            ? '<span class="badge bg-success">' . $total . ' Foto</span>'
                            : '<span class="badge bg-secondary">Kosong</span>';

                        return Html::a('<i class="bi bi-images"></i> Lihat Galeri ' . $badge, ['view', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'data-pjax' => '0', // Buka halaman baru tanpa Pjax grid
                        ]);
                    },
                ],
            ],
        ]); ?>
    </div>
</div>