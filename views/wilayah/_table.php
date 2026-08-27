<?php

use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

?>
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Daftar Data Wilayah</h3>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'summary' => 'Menampilkan <b>{begin}-{end}</b> dari <b>{totalCount}</b> data.',
            'emptyText' => 'Data wilayah tidak ditemukan.',
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
                    'headerOptions' => ['style' => 'width: 60px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                ],
                [
                    'attribute' => 'kode',
                    'label' => 'Kode Wilayah',
                    'headerOptions' => ['style' => 'width: 150px;'],
                ],
                [
                    'attribute' => 'nama',
                    'label' => 'Nama Wilayah',
                ],
                [
                    'label' => 'Tingkat',
                    'value' => function ($model) {
                        $len = strlen($model->kode);
                        return match ($len) {
                            2 => 'Provinsi',
                            5 => 'Kota / Kabupaten',
                            8 => 'Kecamatan',
                            13 => 'Kelurahan / Desa',
                            default => 'Unknown',
                        };
                    },
                    'headerOptions' => ['style' => 'width: 180px;'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        return \yii\helpers\Url::to([$action, 'kode' => $model->kode]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>