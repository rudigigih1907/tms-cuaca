<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\services\BmkgSyncService;

class CuacaController extends Controller
{
    private BmkgSyncService $bmkgService;

    public function init()
    {
        parent::init();
        $this->bmkgService = new BmkgSyncService();
    }
    /**
     * Perintah untuk menyinkronkan data cuaca BMKG.
     * Contoh penggunaan CLI: php yii cuaca/sync 31.72.02.1001
     * Untuk wilayah tanjung priok
     */
    public function actionSync(string $adm4 = '31.72.02.1001'): int
    {
        $this->stdout("Mulai menarik data cuaca BMKG untuk adm4: {$adm4}...\n");

        $result = $this->bmkgService->syncByAdm4($adm4);

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

        $results = $this->bmkgService->syncMultiple($daftarAdm4);
        foreach ($results as $adm4 => $res) {
            $this->stdout("Adm4 {$adm4}: " . $res['message'] . "\n");
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

        // Callback logger untuk menampilkan output real-time di console
        $logger = function ($msg, $type) {
            if ($type === 'error') {
                $this->stderr($msg);
            } else {
                $this->stdout($msg);
            }
        };

        $summary = $this->bmkgService->syncJakartaUtara($logger);

        $this->stdout($summary['message'] . "\n");
        return $summary['success'] ? ExitCode::OK : ExitCode::DATAERR;
    }
}
