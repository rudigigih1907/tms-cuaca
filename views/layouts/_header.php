<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
$username = Html::encode(Yii::$app->user->identity?->username ?? '');

$items = [
    [
        'label' => 'Dashboard',
        'url' => ['/site/dashboard'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    [
        'label' => 'Manajemen User',
        'url' => ['/user/index'],
        'visible' => !Yii::$app->user->isGuest && Yii::$app->user->can('admin'),
    ],
    [
        'label' => 'Cuaca',
        'url' => ['/cuaca/index'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    [
        'label' => 'About',
        'url' => ['/site/about'],
        'visible' => !Yii::$app->user->isGuest,
    ],
];

// Menu Autentikasi & Akun (Posisi Kanan Navbar)
$rightItems = [];

if (Yii::$app->user->isGuest) {
    $rightItems[] = [
        'label' => '<i class="bi bi-box-arrow-in-right me-1"></i> Login',
        'url' => ['/auth/login'],
    ];
} else {
    $rightItems[] = [
        'label' => '<i class="bi bi-person-circle me-1"></i> ' . $username,
        'items' => [
            [
                'label' => '<i class="bi bi-speedometer2 me-2"></i> Dashboard',
                'url' => ['/site/dashboard'],
            ],
            [
                'label' => '<i class="bi bi-key me-2"></i> Ganti Password',
                'url' => ['/site/change-password'],
            ],
            '<div class="dropdown-divider"></div>',
            [
                'label' => '<i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout',
                'url' => ['/auth/logout'],
                'linkOptions' => [
                    'data-method' => 'post',
                    'class' => 'dropdown-item text-danger',
                ],
            ],
        ],
    ];
}

?>
<header id="header">
    <?php NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => ['site/dashboard'],
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'],
    ]) ?>

    <!-- Menu Utama (Kiri) -->
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto'],
        'encodeLabels' => false,
        'items' => $items,
    ]) ?>

    <!-- Menu User & Profil (Kanan) -->
    <div class="d-flex align-items-center gap-2">
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'encodeLabels' => false,
            'items' => $rightItems,
        ]) ?>

        <!-- Tombol Theme Toggle (Dark/Light) -->
        <?= Html::button('&#127769;', [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5 px-2',
            'aria-label' => 'Switch to dark mode',
        ]) ?>
    </div>

    <?php NavBar::end() ?>
</header>