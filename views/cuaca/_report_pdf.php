<?php

use yii\helpers\Html;

/** @var array $dataCuaca */
/** @var string $namaKelurahan */
/** @var string $kodeAdm4 */
/** @var string $tanggal */

?>

<div class="header-title">Laporan Prakiraan Cuaca</div>
<div class="header-sub">Sumber Data: Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)</div>

<!-- METADATA LAPORAN -->
<table class="meta-table">
    <tr>
        <td width="22%"><strong>Wilayah / Kelurahan</strong></td>
        <td width="3%">:</td>
        <td width="75%"><strong><?= Html::encode($namaKelurahan) ?></strong> (Kode ADM4: <?= Html::encode($kodeAdm4) ?>)</td>
    </tr>
    <tr>
        <td><strong>Tanggal Prakiraan</strong></td>
        <td>:</td>
        <td><?= Yii::$app->formatter->asDate($tanggal) ?></td>
    </tr>
    <tr>
        <td><strong>Total Data Jam</strong></td>
        <td>:</td>
        <td><?= count($dataCuaca) ?> Periode Waktu</td>
    </tr>
</table>

<!-- TABEL DATA PRAKIRAAN CUACA -->
<table class="table-data">
    <thead>
        <tr>
            <th width="8%">No</th>
            <th width="20%">Jam / Waktu</th>
            <th>Kondisi Cuaca</th>
            <th width="15%">Suhu (°C)</th>
            <th width="15%">Kelembapan (%)</th>
            <th width="20%">Kecepatan Angin</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataCuaca as $index => $row): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= Yii::$app->formatter->asTime($row->local_datetime) . ' WIB' ?></td>
                <td style="text-align: left; padding-left: 12px;"><?= Html::encode($row->kondisi_cuaca ?? '-') ?></td>
                <td><?= $row->suhu !== null ? $row->suhu . ' °C' : '-' ?></td>
                <td><?= $row->kelembapan !== null ? $row->kelembapan . ' %' : '-' ?></td>
                <td><?= $row->kecepatan_angin !== null ? $row->kecepatan_angin . ' km/j' : '-' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="footer-text">
    <p>Dicetak secara otomatis melalui Sistem Informasi Cuaca BMKG pada <?= date('d F Y, H:i') ?> WIB.</p>
</div>