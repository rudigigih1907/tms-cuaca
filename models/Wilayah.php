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

    /**
     * Mengambil detail nama Provinsi, Kabupaten, Kecamatan, Kelurahan berdasarkan kode kelurahan (kode_adm4)
     * 
     * @param string $kodeKelurahan (Contoh: '31.72.01.1001' atau '3172011001')
     * @return array
     */
    public static function getDetailWilayahByKode($kodeKelurahan)
    {
        if (empty($kodeKelurahan)) {
            return [
                'provinsi'  => '-',
                'kabupaten' => '-',
                'kecamatan' => '-',
                'kelurahan' => '-',
            ];
        }

        $kelurahan = static::findOne(['kode' => $kodeKelurahan]);
        if (!$kelurahan) {
            return [
                'provinsi'  => '-',
                'kabupaten' => '-',
                'kecamatan' => '-',
                'kelurahan' => '-',
            ];
        }

        // Ambil data induk (Kecamatan, Kabupaten, Provinsi) berdasarkan relasi parent atau potongan kode
        // Contoh jika menggunakan potongan kode standar BPS/Kemendagri (misal: 31.72.01.1001):
        $kodeKecamatan = substr($kodeKelurahan, 0, 8); // 31.72.01
        $kodeKabupaten = substr($kodeKelurahan, 0, 5); // 31.72
        $kodeProvinsi  = substr($kodeKelurahan, 0, 2); // 31

        $kecamatan = static::findOne(['kode' => $kodeKecamatan]);
        $kabupaten = static::findOne(['kode' => $kodeKabupaten]);
        $provinsi  = static::findOne(['kode' => $kodeProvinsi]);

        return [
            'provinsi'  => $provinsi->nama ?? '-',
            'kabupaten' => $kabupaten->nama ?? '-',
            'kecamatan' => $kecamatan->nama ?? '-',
            'kelurahan' => $kelurahan->nama ?? '-',
        ];
    }
}
