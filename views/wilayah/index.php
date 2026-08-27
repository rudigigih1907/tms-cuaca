<?php

use yii\helpers\Html;
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
    <?= $this->render('_dropdown', [
        'provinsiId'    => $provinsiId ?? null,
        'kabupatenId'   => $kabupatenId ?? null,
        'kecamatanId'   => $kecamatanId ?? null,
        'kelurahanId'   => $kelurahanId ?? null,
        'listProvinsi'  => $listProvinsi ?? [],
        'listKabupaten' => $listKabupaten ?? [],
        'listKecamatan' => $listKecamatan ?? [],
        'listKelurahan' => $listKelurahan ?? [],
    ]) ?>

    <!-- TABEL GRIDVIEW -->
    <?= $this->render('_table', [
        'dataProvider' => $dataProvider,
    ]) ?>
    <?php Pjax::end(); ?>

</div>