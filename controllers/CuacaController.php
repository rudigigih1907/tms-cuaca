<?php

namespace app\controllers;

use app\models\Cuaca;
use app\models\Wilayah;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CuacaController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request;

        $provinsiId  = $request->get('provinsi_id');
        $kabupatenId = $request->get('kabupaten_id');
        $kecamatanId = $request->get('kecamatan_id');
        $kelurahanId = $request->get('kelurahan_id'); // Ini adalah kode adm4
        $tanggal     = $request->get('tanggal');     // Filter Tanggal Terpilih (YYYY-MM-DD)

        // Ambil daftar nama wilayah untuk dropdown
        $listProvinsi  = Wilayah::getProvinsi();
        $listKabupaten = Wilayah::getChildList($provinsiId, 5);
        $listKecamatan = Wilayah::getChildList($kabupatenId, 8);
        $listKelurahan = Wilayah::getChildList($kecamatanId, 13);

        // Ambil daftar Tanggal Unik yang tersedia di DB khusus kelurahan terpilih
        $listTanggal = [];
        if (!empty($kelurahanId)) {
            $dates = Cuaca::find()
                ->select(["DATE(local_datetime) AS tgl"])
                ->where(['kode_adm4' => $kelurahanId])
                ->groupBy(["DATE(local_datetime)"])
                ->orderBy(['tgl' => SORT_ASC])
                ->asArray()
                ->all();

            foreach ($dates as $row) {
                $tglRaw = $row['tgl'];
                // Format label dropdown (Contoh: 06 Agustus 2026)
                $listTanggal[$tglRaw] = Yii::$app->formatter->asDate($tglRaw, 'php:d F Y');
            }
        }

        // GridView cuaca berdasarkan kelurahan yang dipilih (adm4)
        $query = Cuaca::find();
        if (!empty($kelurahanId)) {
            $query->andWhere(['kode_adm4' => $kelurahanId]);
            // Tambahkan filter jika tanggal dipilih
            if (!empty($tanggal)) {
                $query->andWhere(['DATE(local_datetime)' => $tanggal]);
            }
        } else {
            // Jika belum pilih kelurahan, tampilkan kosong/default
            $query->where('1=0');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 24],
            'sort' => [
                'defaultOrder' => ['local_datetime' => SORT_ASC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'provinsiId'   => $provinsiId,
            'kabupatenId'  => $kabupatenId,
            'kecamatanId'  => $kecamatanId,
            'kelurahanId'  => $kelurahanId,
            'listProvinsi' => $listProvinsi,
            'listKabupaten' => $listKabupaten,
            'listKecamatan' => $listKecamatan,
            'listKelurahan' => $listKelurahan,
            'listTanggal'  => $listTanggal,
            'tanggal'      => $tanggal,
        ]);
    }

    /**
     * Mengambil & menyimpan data langsung dari API BMKG berdasarkan adm4.
     */
    public function actionSync(): \yii\web\Response
    {
        $adm4 = Yii::$app->request->post('adm4');

        if (empty($adm4)) {
            Yii::$app->session->setFlash('error', 'Kode Wilayah (adm4) harus diisi!');
            return $this->redirect(['index']);
        }

        $result = Cuaca::syncFromBmkg($adm4);

        if ($result['success']) {
            Yii::$app->session->setFlash('success', $result['message']);
        } else {
            Yii::$app->session->setFlash('error', $result['message']);
        }

        return $this->redirect(['index', 'adm4' => $adm4]);
    }
}
