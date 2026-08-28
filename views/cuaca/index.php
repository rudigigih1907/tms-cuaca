<?php

use app\assets\DataTableAsset;
use yii\helpers\Html;
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

DataTableAsset::register($this);
?>

<div class="cuaca-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'cuaca-wilayah-pjax',
        'enablePushState' => true,
        'timeout' => 5000,
    ]); ?>

    <!-- FORM SELEKSI DROPDOWN NAMA WILAYAH -->
    <?= $this->render('_form', [
        'provinsiId'    => $provinsiId ?? null,
        'kabupatenId'   => $kabupatenId ?? null,
        'kecamatanId'   => $kecamatanId ?? null,
        'kelurahanId'   => $kelurahanId ?? null,
        'listProvinsi'  => $listProvinsi ?? [],
        'listKabupaten' => $listKabupaten ?? [],
        'listKecamatan' => $listKecamatan ?? [],
        'listKelurahan' => $listKelurahan ?? [],
    ]) ?>

    <!-- TABEL GRIDVIEW HASIL PRAKIRAAN CUACA -->
    <?= $this->render('_table', [
        'groupedDates' => $groupedDates,
        'kelurahanId'  => $kelurahanId ?? null,
    ]) ?>

    <?php Pjax::end(); ?>
</div>