<?php

use yii\helpers\Html;

/** @var array $dataCuaca */
/** @var array $detailWilayah */
/** @var string $kodeAdm4 */
/** @var string $tanggal */
/** @var string $logoPath */
/** @var string $sourceUrl */
/** @var string $namaPerusahaan */
?>

<table style="width: 100%; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px;">
    <tr>
        <td style="width: 15%; text-align: center; vertical-align: middle;">
            <?php if (file_exists($logoPath)): ?>
                <img src="<?= $logoPath ?>" style="height: 65px; width: auto;" alt="Logo BMKG">
            <?php else: ?>
                <div style="font-weight: bold; color: #999;">[LOGO]</div>
            <?php endif; ?>
        </td>
        <td style="width: 85%; text-align: center; vertical-align: middle;">
            <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px;">
                <?= Html::encode($namaPerusahaan) ?>
            </div>
            <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">Laporan Prakiraan Cuaca</div>
            <div style="font-size: 9.5pt; color: #444; margin-top: 4px;">
                Sumber Data: <strong>Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)</strong>
            </div>
            <div style="font-size: 8.5pt; color: #0056b3; margin-top: 2px;">
            </div>
        </td>
    </tr>
</table>

<!-- METADATA WILAYAH LENGKAP -->
<table class="meta-table" style="width: 100%; margin-bottom: 15px;">
    <tr>
        <td width="18%"><strong>Provinsi</strong></td>
        <td width="2%">:</td>
        <td width="30%"><?= Html::encode($detailWilayah['provinsi'] ?? '-') ?></td>

        <td width="18%"><strong>Kecamatan</strong></td>
        <td width="2%">:</td>
        <td width="30%"><?= Html::encode($detailWilayah['kecamatan'] ?? '-') ?></td>
    </tr>
    <tr>
        <td><strong>Kabupaten / Kota</strong></td>
        <td>:</td>
        <td><?= Html::encode($detailWilayah['kabupaten'] ?? '-') ?></td>

        <td><strong>Kelurahan / Desa</strong></td>
        <td>:</td>
        <td><strong><?= Html::encode($detailWilayah['kelurahan'] ?? '-') ?></strong></td>
    </tr>
    <tr>
        <td><strong>Kode Wilayah (ADM4)</strong></td>
        <td>:</td>
        <td><code><?= Html::encode($kodeAdm4) ?></code></td>

        <td><strong>Tanggal Prakiraan</strong></td>
        <td>:</td>
        <td><?= Yii::$app->formatter->asDate($tanggal, 'php:d F Y') ?></td>
    </tr>
</table>

<!-- TABEL DATA PRAKIRAAN CUACA -->
<table class="table-data">
    <thead>
        <tr>
            <th width="18%">Jam / Waktu</th>
            <th>Kondisi Cuaca</th>
            <th width="14%">Suhu</th>
            <th width="16%">Kelembapan</th>
            <th width="18%">Kecepatan Angin</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($dataCuaca)): ?>
            <?php foreach ($dataCuaca as $index => $row): ?>
                <tr>
                    <td style="text-align: center;"><?= Yii::$app->formatter->asTime($row->local_datetime, 'short') ?> WIB</td>
                    <td style="text-align: left; padding-left: 10px;"><?= Html::encode($row->kondisi_cuaca ?? '-') ?></td>
                    <td style="text-align: center;"><?= $row->suhu !== null ? $row->suhu . ' °C' : '-' ?></td>
                    <td style="text-align: center;"><?= $row->kelembapan !== null ? $row->kelembapan . ' %' : '-' ?></td>
                    <td style="text-align: center;"><?= $row->kecepatan_angin !== null ? $row->kecepatan_angin . ' km/jam' : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #777;">Data prakiraan cuaca tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer-text">
    <p>Data bersumber dari API Resmi <strong>BMKG Open Data</strong> (<a href="<?= Html::encode($sourceUrl) ?>" style="color: #666;"><?= Html::encode($sourceUrl) ?></a>). <br>
        Dicetak secara otomatis pada <?= date('d/m/Y H:i') ?> WIB.</p>
</div>