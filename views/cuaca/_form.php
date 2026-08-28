<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string|null $provinsiId */
/** @var string|null $kabupatenId */
/** @var string|null $kecamatanId */
/** @var string|null $kelurahanId */
/** @var string|null $tanggal */
/** @var array $listProvinsi */
/** @var array $listKabupaten */
/** @var array $listKecamatan */
/** @var array $listKelurahan */

$provinsiId  = $provinsiId ?? null;
$kabupatenId = $kabupatenId ?? null;
$kecamatanId = $kecamatanId ?? null;
$kelurahanId = $kelurahanId ?? null;
$tanggal      = $tanggal ?? null;

$listProvinsi  = $listProvinsi ?? [];
$listKabupaten = $listKabupaten ?? [];
$listKecamatan = $listKecamatan ?? [];
$listKelurahan = $listKelurahan ?? [];
?>
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
                        <!-- TOMBOL TARIK DATA BMKG -->
                        <?= Html::beginForm(['cuaca/sync'], 'post', ['data-pjax' => true, 'class' => 'd-inline']) ?>
                        <?= Html::hiddenInput('adm4', $kelurahanId) ?>
                        <?= Html::submitButton('<i class="bi bi-cloud-download"></i> Tarik Data BMKG', [
                            'class' => 'btn btn-success',
                        ]) ?>
                        <?= Html::endForm() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>