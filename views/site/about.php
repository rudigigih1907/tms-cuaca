<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $developerName */
/** @var string $organization */
/** @var string $apiSource */
/** @var string $apiVersion */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

$wilayahSource = 'https://github.com/cahyadsn/wilayah';
?>

<div class="site-about">
    <!-- Header Banner -->
    <div class="p-4 p-md-5 mb-4 bg-primary text-white rounded-3 shadow-sm">
        <div class="container-fluid py-2">
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-info-circle me-2"></i>Tentang Aplikasi
            </h1>
            <p class="col-md-9 fs-6 text-white-50 mb-0">
                Sistem Informasi Integrasi Layanan Data Terbuka Cuaca Indonesia.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card Pengembang / Developer -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold m-0 text-primary">
                        <i class="bi bi-code-slash me-2"></i>Pengembang
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; min-width: 60px;">
                            <i class="bi bi-person-badge fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1"><?= Html::encode($developerName) ?></h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold">
                                <?= Html::encode($organization) ?>
                            </span>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush border-top pt-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                            <span class="text-muted"><i class="bi bi-building me-2"></i>Organisasi</span>
                            <span class="fw-semibold"><?= Html::encode($organization) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                            <span class="text-muted"><i class="bi bi-person-gear me-2"></i>Role</span>
                            <span class="fw-semibold">Staff IT Software Development</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Card Integrasi API BMKG & Data Wilayah -->
        <div class="col-12 col-md-6 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold m-0 text-primary">
                        <i class="bi bi-cloud-sun me-2"></i>Sumber Data & API
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        Aplikasi ini terintegrasi secara langsung dengan layanan API Data Terbuka BMKG (Badan Meteorologi, Klimatologi, dan Geofisika) untuk menyajikan prakiraan cuaca secara akurat hingga tingkat wilayah terkecil.
                    </p>

                    <!-- Block 1: BMKG API -->
                    <div class="p-3 border rounded-3 mb-3 bg-light-subtle">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-diagram-3 fs-4 text-warning me-2"></i>
                            <h6 class="fw-bold mb-0">Cakupan Wilayah Cuaca</h6>
                        </div>
                        <p class="small text-muted mb-0">
                            <strong><?= Html::encode($apiVersion) ?></strong> — Menyediakan estimasi parameter cuaca spesifik hingga level Kelurahan/Desa di seluruh wilayah Indonesia.
                        </p>
                    </div>

                    <!-- Block 2: Data Wilayah Tingkat 4 (cahyadsn/wilayah) -->
                    <div class="p-3 border rounded-3 mb-4 bg-light-subtle">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt fs-4 text-danger me-2"></i>
                            <h6 class="fw-bold mb-0">Master Data Wilayah Indonesia</h6>
                        </div>
                        <p class="small text-muted mb-2">
                            Pemetaan kode dan struktur hirarki administratif (Provinsi, Kabupaten/Kota, Kecamatan, hingga Desa/Kelurahan) bersumber dari repository terbuka data wilayah Indonesia.
                        </p>
                        <a href="<?= Html::encode($wilayahSource) ?>" target="_blank" rel="noopener noreferrer" class="small text-decoration-none fw-semibold">
                            <i class="bi bi-github me-1"></i> Repository cahyadsn/wilayah <i class="bi bi-arrow-up-right small"></i>
                        </a>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 border-top pt-3">
                        <span class="small text-muted">
                            <i class="bi bi-link-45deg me-1"></i>Dokumentasi API BMKG:
                        </span>
                        <a href="<?= Html::encode($apiSource) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary fw-semibold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka data.bmkg.go.id
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>