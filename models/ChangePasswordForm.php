<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $confirmPassword;

    private $_user;

    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'confirmPassword'], 'required', 'message' => '{attribute} tidak boleh kosong.'],
            [['newPassword'], 'string', 'min' => 6, 'message' => 'Password baru minimal 6 karakter.'],
            ['oldPassword', 'validateOldPassword'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Konfirmasi password tidak cocok dengan password baru.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword' => 'Password Saat Ini',
            'newPassword' => 'Password Baru',
            'confirmPassword' => 'Konfirmasi Password Baru',
        ];
    }

    /**
     * Validasi apakah password lama yang dimasukkan benar
     */
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->_user->validatePassword($this->$attribute)) {
                $this->addError($attribute, 'Password saat ini salah.');
            }
        }
    }

    /**
     * Simpan password baru ke basis data
     */
    public function changePassword(): bool
    {
        if ($this->validate()) {
            $this->_user->setPassword($this->newPassword);
            $this->_user->generateAuthKey();
            return $this->_user->save(false);
        }

        return false;
    }
}
