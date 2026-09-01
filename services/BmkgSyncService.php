<?php

namespace app\services;

use Yii;
use app\models\Cuaca;
use app\models\Wilayah;

class BmkgSyncService
{
    /**
     * Sync data BMKG untuk 1 kode adm4 (kelurahan)
     */
    public function syncByAdm4(string $adm4): array
    {
        if (empty($adm4)) {
            return [
                'success' => false,
                'message' => 'Kode Wilayah (adm4) tidak boleh kosong!'
            ];
        }

        // Panggil logic sync dari Model atau pindahkan logika fetch API langsung ke sini
        return Cuaca::syncFromBmkg($adm4);
    }

    /**
     * Sync data BMKG untuk banyak kode adm4 (Array)
     */
    public function syncMultiple(array $daftarAdm4): array
    {
        $results = [];
        foreach ($daftarAdm4 as $adm4) {
            $results[$adm4] = $this->syncByAdm4($adm4);
        }
        return $results;
    }

    /**
     * Sync seluruh kelurahan di Jakarta Utara (Kode Prefix 31.72.%)
     * Dilengkapi dengan rate limiting & callback untuk logging progress
     * 
     * @param callable|null $logger Callback fungsi untuk output log (misal CLI stdout)
     */
    public function syncJakartaUtara(?callable $logger = null): array
    {
        $daftarKelurahanJakut = Wilayah::find()
            ->select(['kode'])
            ->where(['CHAR_LENGTH(kode)' => 13])
            ->andWhere(['like', 'kode', '31.72.%', false])
            ->column();

        if (empty($daftarKelurahanJakut)) {
            return [
                'success' => false,
                'message' => 'Tidak ada kode kelurahan Jakarta Utara yang ditemukan.',
                'total' => 0,
                'sukses' => 0,
                'gagal' => 0
            ];
        }

        $sukses = 0;
        $gagal = 0;

        foreach ($daftarKelurahanJakut as $adm4) {
            $result = $this->syncByAdm4($adm4);

            if ($result['success']) {
                $sukses++;
                if ($logger) {
                    $logger("[OK] Adm4 {$adm4}: {$result['message']}\n", 'info');
                }
            } else {
                $gagal++;
                if ($logger) {
                    $logger("[ERROR] Adm4 {$adm4}: {$result['message']}\n", 'error');
                }
            }

            // Rate limiting: Jeda 5 detik antar request agar tidak di-block BMKG
            usleep(5000000);
        }

        return [
            'success' => true,
            'message' => "Sinkronisasi selesai. Total: " . count($daftarKelurahanJakut) . " (Sukses: {$sukses}, Gagal: {$gagal})",
            'total'   => count($daftarKelurahanJakut),
            'sukses'  => $sukses,
            'gagal'   => $gagal
        ];
    }
}
