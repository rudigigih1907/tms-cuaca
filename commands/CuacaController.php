<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Cuaca;
use app\models\Wilayah;

class CuacaController extends Controller
{
    /**
     * Perintah untuk menyinkronkan data cuaca BMKG.
     * Contoh penggunaan CLI: php yii cuaca/sync 31.72.02.1001
     * Untuk wilayah tanjung priok
     */
    public function actionSync(string $adm4 = '31.72.02.1001'): int
    {
        $this->stdout("Mulai menarik data cuaca BMKG untuk adm4: {$adm4}...\n");

        $result = Cuaca::syncFromBmkg($adm4);

        if ($result['success']) {
            $this->stdout("[SUKSES] " . $result['message'] . "\n");
            return ExitCode::OK;
        }

        $this->stderr("[ERROR] " . $result['message'] . "\n");
        return ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * Perintah untuk menyinkronkan data cuaca BMKG
     * Hanya beberapa wilayah saja
     */
    public function actionSyncAll(): int
    {
        $daftarAdm4 = [
            '31.72.05.1003', // Kelurahan 1
            '31.72.05.1004', // Kelurahan 2
            '32.73.01.1001', // Kelurahan 3
        ];

        foreach ($daftarAdm4 as $adm4) {
            $result = Cuaca::syncFromBmkg($adm4);
            $this->stdout($result['message'] . "\n");
        }

        return ExitCode::OK;
    }

    /**
     * Otomatisasi Sinkronisasi BMKG Jakarta Utara dengan Rate Limiting (Max 60 req/menit)
     * CLI command: php yii cuaca/sync-jakut
     */
    public function actionSyncJakut(): int
    {
        $this->stdout("[" . date('Y-m-d H:i:s') . "] Memulai sinkronisasi BMKG Jakarta Utara...\n");

        // Ambil semua kelurahan Jakarta Utara (Kode Prefix 31.72)
        $daftarKelurahanJakut = Wilayah::find()
            ->select(['kode'])
            ->where(['CHAR_LENGTH(kode)' => 13])
            ->andWhere(['like', 'kode', '31.72.%', false])
            ->column();

        if (empty($daftarKelurahanJakut)) {
            $this->stderr("Tidak ada kode kelurahan Jakarta Utara yang ditemukan.\n");
            return ExitCode::DATAERR;
        }

        $sukses = 0;
        $gagal = 0;

        foreach ($daftarKelurahanJakut as $adm4) {
            $result = Cuaca::syncFromBmkg($adm4);

            if ($result['success']) {
                $sukses++;
                $this->stdout("[OK] Adm4 {$adm4}: {$result['message']}\n");
            } else {
                $gagal++;
                $this->stderr("[ERROR] Adm4 {$adm4}: {$result['message']}\n");
            }
            usleep(5000000);
        }
        $this->stdout("Sinkronisasi Selesai. Total Kelurahan: " . count($daftarKelurahanJakut) . " (Sukses: {$sukses}, Gagal: {$gagal})\n");
        return ExitCode::OK;
    }
}
