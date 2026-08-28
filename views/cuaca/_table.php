<?php

use app\assets\DataTableAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var yii\web\View $this */
/** @var array $groupedDates */
/** @var string|null $kelurahanId */

$kelurahanId = $kelurahanId ?? null;
$groupedDates = $groupedDates ?? [];
?>
<div class="card card-default">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0 fs-6">Prakiraan Cuaca</h3>
        <?php if (!empty($groupedDates)): ?>
            <span class="badge bg-light text-dark"><?= count($groupedDates) ?> Hari Terdata</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($kelurahanId)): ?>
            <div class="p-4 text-center text-muted">
                Silakan pilih Wilayah sampai tingkat Kelurahan/Desa di atas.
            </div>
        <?php elseif (empty($groupedDates)): ?>
            <div class="p-4 text-center text-muted">
                Belum ada data cuaca untuk wilayah ini. Silakan klik tombol <strong>"Tarik / Update Data BMKG"</strong>.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="table-cuaca-group" class="table table-bordered table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
                            <th>Tanggal Prakiraan</th>
                            <th class="text-center">Jumlah Jam Terdata</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupedDates as $idx => $row):
                            $formattedDate = Yii::$app->formatter->asDate($row['tgl'], 'php:l, d F Y');
                        ?>
                            <tr data-tgl="<?= Html::encode($row['tgl']) ?>">
                                <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-bold">
                                    <i class="bi bi-calendar-event me-2 text-primary"></i><?= $formattedDate ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark"><?= $row['total_jam'] ?> Data Jam</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-expand-detail">
                                        <i class="bi bi-plus-square-fill me-1"></i> Buka Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Register DataTables Asset
DataTableAsset::register($this);

// Parameter PHP yang dibutuhkan JS
$ajaxDetailUrl = Url::to(['cuaca/get-detail-by-date']);
$exportPdfBaseUrl = Url::to(['cuaca/export-pdf', 'kelurahan_id' => $kelurahanId]);

$configJson = json_encode([
    'ajaxDetailUrl' => $ajaxDetailUrl,
    'kelurahanId' => $kelurahanId,
    'exportPdfBaseUrl' => $exportPdfBaseUrl,
]);

// Daftarkan config di posisi POS_READY atau POS_HEAD agar selalu ter-update saat PJAX me-load _table.php
$this->registerJs("window.cuacaConfig = {$configJson};", View::POS_READY);

$this->registerJsFile(
    '@web/js/cuaca-table.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>