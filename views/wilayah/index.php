<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\WilayahSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Data Daerah';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="wilayah-index">
    <p>
        <?= Html::a('Create Wilayah', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin([
        'id' => 'wilayah-pjax',
        'enablePushState' => false,
        'timeout' => 5000,
    ]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <!-- FORM FILTER DROPDOWN -->
    <?= Html::beginForm(['wilayah/index'], 'get', ['data-pjax' => true, 'id' => 'form-wilayah']) ?>

    <table class="table table-bordered mb-4">
        <tr>
            <td style="width: 150px;"><label for="provinsi">Provinsi</label></td>
            <td>
                <?= Html::dropDownList('provinsi_id', $provinsiId, $listProvinsi ?? [], [
                    'id' => 'provinsi',
                    'prompt' => 'Pilih Provinsi',
                    'class' => 'form-control',
                    'onchange' => '$(this).closest("form").submit();'
                ]) ?>
            </td>
        </tr>

        <?php if (!empty($provinsiId)): ?>
            <tr>
                <td><label for="kabupaten">Kota/Kabupaten</label></td>
                <td>
                    <?= Html::dropDownList('kabupaten_id', $kabupatenId, $listKabupaten ?? [], [
                        'id' => 'kabupaten',
                        'prompt' => 'Pilih Kota/Kabupaten',
                        'class' => 'form-control',
                        'onchange' => '$(this).closest("form").submit();'
                    ]) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if (!empty($kabupatenId)): ?>
            <tr>
                <td><label for="kecamatan">Kecamatan</label></td>
                <td>
                    <?= Html::dropDownList('kecamatan_id', $kecamatanId, $listKecamatan ?? [], [
                        'id' => 'kecamatan',
                        'prompt' => 'Pilih Kecamatan',
                        'class' => 'form-control',
                        'onchange' => '$(this).closest("form").submit();'
                    ]) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if (!empty($kecamatanId)): ?>
            <tr>
                <td><label for="kelurahan">Kelurahan</label></td>
                <td>
                    <?= Html::dropDownList('kelurahan_id', $kelurahanId, $listKelurahan ?? [], [
                        'id' => 'kelurahan',
                        'prompt' => 'Pilih Kelurahan',
                        'class' => 'form-control',
                        'onchange' => '$(this).closest("form").submit();'
                    ]) ?>
                </td>
            </tr>
        <?php endif; ?>
    </table>
    <?= Html::endForm() ?>

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
    <?php Pjax::end(); ?>

</div>