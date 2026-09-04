<?php

namespace app\models;

use yii\base\Model;

class UserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $status = 10;
    public $role;
    public $isNewRecord = true;

    public function rules()
    {
        return [
            [['username', 'email'], 'required', 'message' => '{attribute} tidak boleh kosong.'],
            [['username', 'email', 'role'], 'trim'],
            ['email', 'email', 'message' => 'Format email tidak valid.'],
            ['status', 'in', 'range' => [10, 0]],
            ['role', 'string'],

            // Validasi Unik Username & Email (Mengecualikan ID user yang sedang di-edit)
            ['username', 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                if ($this->id) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
            }],
            ['email', 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                if ($this->id) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
            }],

            // Password wajib diisi saat Create, opsional saat Update
            ['password', 'required', 'when' => function ($model) {
                return empty($model->id);
            }, 'whenClient' => "function (attribute, value) {
                return $('#userform-id').val() === '';
            }"],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email'    => 'Email',
            'password' => $this->isNewRecord ? 'Password' : 'Password Baru (Kosongkan jika tidak diubah)',
            'status'   => 'Status Akun',
            'role'     => 'Role Pengguna',
        ];
    }
}
