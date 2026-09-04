<?php

use yii\bootstrap5\ActiveForm; // Atau yii\widgets\ActiveForm jika menggunakan Bootstrap 4/3
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ChangePasswordForm $model */

$this->title = 'Ganti Password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-change-password row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white py-3">
                <h5 class="card-title m-0 fw-bold">
                    <i class="bi bi-shield-lock me-2"></i><?= Html::encode($this->title) ?>
                </h5>
            </div>
            <div class="card-body p-4">

                <?php $form = ActiveForm::begin([
                    'id' => 'change-password-form',
                    'enableClientValidation' => true,
                ]); ?>

                <?= $form->field($model, 'oldPassword')->passwordInput([
                    'placeholder' => 'Masukkan password saat ini',
                    'autofocus' => true,
                ]) ?>

                <?= $form->field($model, 'newPassword')->passwordInput([
                    'placeholder' => 'Masukkan password baru (min. 6 karakter)',
                ]) ?>

                <?= $form->field($model, 'confirmPassword')->passwordInput([
                    'placeholder' => 'Ulangi password baru',
                ]) ?>

                <div class="d-grid gap-2 mt-4">
                    <?= Html::submitButton('<i class="bi bi-save me-1"></i> Simpan Password Baru', [
                        'class' => 'btn btn-primary fw-semibold',
                    ]) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>