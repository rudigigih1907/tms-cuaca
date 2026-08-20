<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cuaca".
 *
 * @property int $id
 * @property string $kode_adm4
 * @property string $local_datetime
 * @property string $analysis_date
 * @property int|null $suhu
 * @property int|null $kelembapan
 * @property string|null $kondisi_cuaca
 * @property float|null $kecepatan_angin
 * @property string|null $arah_angin
 * @property string|null $created_at
 */
class Cuaca extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cuaca';
    }

    public function behaviors(): array
{
    return [
        [
            'class' => TimestampBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
            ],
            // Gunakan format DATETIME Asia/Jakarta
            'value' => date('Y-m-d H:i:s'),
        ],
    ];
}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['suhu', 'kelembapan', 'kondisi_cuaca', 'kecepatan_angin', 'arah_angin'], 'default', 'value' => null],
            [['kode_adm4', 'local_datetime', 'analysis_date'], 'required'],
            [['local_datetime', 'analysis_date', 'created_at'], 'safe'],
            [['suhu', 'kelembapan'], 'integer'],
            [['kecepatan_angin'], 'number'],
            [['kode_adm4'], 'string', 'max' => 20],
            [['kondisi_cuaca'], 'string', 'max' => 100],
            [['arah_angin'], 'string', 'max' => 50],
            [['kode_adm4', 'local_datetime'], 'unique', 'targetAttribute' => ['kode_adm4', 'local_datetime']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode_adm4' => 'Kode Wilayah (adm4)',
            'local_datetime' => 'Local Datetime',
            'analysis_date' => 'Analysis Date',
            'suhu' => 'Suhu (°C)',
            'kelembapan' => 'Kelembapan (%)',
            'kondisi_cuaca' => 'Kondisi Cuaca',
            'kecepatan_angin' => 'Kecepatan Angin (km/j)',
            'arah_angin' => 'Arah Angin',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Mengambil data dari API BMKG dan meng-upsert ke Database.
     */
    public static function syncFromBmkg(string $adm4): array
    {
        $url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4={$adm4}";

        try {
            $client = new \yii\httpclient\Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                ->send();

            if (!$response->isOk) {
                return ['success' => false, 'message' => 'Gagal terhubung ke API BMKG.'];
            }

            $data = $response->data;

            // Penanganan respons API BMKG
            $cuacaList = $data['data'][0]['cuaca'] ?? $data['cuaca'] ?? [];

            if (empty($cuacaList)) {
                return ['success' => false, 'message' => 'Data cuaca dari BMKG kosong atau kode adm4 tidak valid.'];
            }

            $savedCount = 0;

            foreach ($cuacaList as $itemGroup) {
                // BMKG menyajikan data per kelompok rentang waktu
                $forecasts = is_array($itemGroup) && isset($itemGroup[0]) ? $itemGroup : [$itemGroup];

                foreach ($forecasts as $item) {
                    if (!isset($item['local_datetime'])) {
                        continue;
                    }

                    // 1. CARI DATA LAMA (Mencegah duplikasi & penghapusan)
                    $model = static::findOne([
                        'kode_adm4' => $adm4,
                        'local_datetime' => $item['local_datetime']
                    ]);

                    // 2. JIKA BELUM ADA, BUAT RECORD BARU (Mencegah terhapus/hilang)
                    if (!$model) {
                        $model = new static();
                        $model->kode_adm4 = $adm4;
                        $model->local_datetime = $item['local_datetime'];
                    }

                    $model->kode_adm4 = $adm4;
                    $model->local_datetime = $item['local_datetime'];
                    $model->analysis_date = $item['analysis_date'] ?? null;
                    $model->suhu = $item['t'] ?? null;
                    $model->kelembapan = $item['hu'] ?? null;
                    $model->kondisi_cuaca = $item['weather_desc'] ?? null;
                    $model->kecepatan_angin = $item['ws'] ?? null;
                    $model->arah_angin = $item['wd'] ?? null;

                    if ($model->save()) {
                        $savedCount++;
                    }
                }
            }

            return [
                'success' => true,
                'message' => "Berhasil menyinkronkan {$savedCount} data cuaca untuk wilayah {$adm4}."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
