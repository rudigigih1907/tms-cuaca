<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Cuaca;

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
}
