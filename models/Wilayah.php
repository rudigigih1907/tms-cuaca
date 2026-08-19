<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wilayah".
 *
 * @property string $kode
 * @property string|null $nama
 */
class Wilayah extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wilayah';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama'], 'default', 'value' => null],
            [['kode'], 'required'],
            [['kode'], 'string', 'max' => 13],
            [['nama'], 'string', 'max' => 100],
            [['kode'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kode' => 'Kode',
            'nama' => 'Nama',
        ];
    }

    public static function getProvinsi(): array
    {
        return static::getDb()->cache(function () {
            return static::find()
                ->select(['nama', 'kode'])
                ->where(['CHAR_LENGTH(kode)' => 2])
                ->orderBy(['nama' => SORT_ASC])
                ->indexBy('kode')
                ->column();
        }, 86400); // Cache selama 24 jam
    }

    public static function getChildList(?string $parentId, int $length): array
    {
        if (!$parentId) {
            return [];
        }

        return static::find()
            ->select(['nama', 'kode'])
            ->where(['like', 'kode', $parentId . '%', false])
            ->andWhere(['CHAR_LENGTH(kode)' => $length])
            ->orderBy(['nama' => SORT_ASC])
            ->indexBy('kode')
            ->column();
    }
}
