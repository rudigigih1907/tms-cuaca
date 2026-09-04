<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UserForm $model */
/** @var array $roles */

$roleList = array_combine(array_keys($roles), array_keys($roles));
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <?php $form = ActiveForm::begin(['id' => 'user-form']); ?>

        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'username')->textInput(['placeholder' => 'Masukkan username']) ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'email')->textInput(['placeholder' => 'nama@domain.com']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => $model->id ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan kata sandi'
                ]) ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'role')->dropDownList($roleList, ['prompt' => '-- Pilih Role RBAC --']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'status')->dropDownList([
                    10 => 'Aktif',
                    0 => 'Nonaktif',
                ]) ?>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::submitButton($model->id ? 'Simpan Perubahan' : 'Tambah User', ['class' => 'btn btn-primary fw-semibold']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>