<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string|null $provinsiId */
/** @var string|null $kabupatenId */
/** @var string|null $kecamatanId */
/** @var string|null $kelurahanId */
/** @var array $listProvinsi */
/** @var array $listKabupaten */
/** @var array $listKecamatan */
/** @var array $listKelurahan */

$provinsiId  = $provinsiId ?? null;
$kabupatenId = $kabupatenId ?? null;
$kecamatanId = $kecamatanId ?? null;
$kelurahanId = $kelurahanId ?? null;

$listProvinsi  = $listProvinsi ?? [];
$listKabupaten = $listKabupaten ?? [];
$listKecamatan = $listKecamatan ?? [];
$listKelurahan = $listKelurahan ?? [];
?>

<?= Html::beginForm(['wilayah/index'], 'get', ['data-pjax' => true, 'id' => 'form-wilayah']) ?>

<table class="table table-bordered mb-4">
    <tr>
        <td style="width: 150px;"><label for="provinsi">Provinsi</label></td>
        <td>
            <?= Html::dropDownList('provinsi_id', $provinsiId, $listProvinsi, [
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
                <?= Html::dropDownList('kabupaten_id', $kabupatenId, $listKabupaten, [
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
                <?= Html::dropDownList('kecamatan_id', $kecamatanId, $listKecamatan, [
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
                <?= Html::dropDownList('kelurahan_id', $kelurahanId, $listKelurahan, [
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