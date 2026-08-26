<?php

namespace app\controllers;

use app\models\Cuaca;
use app\models\CuacaGambar;
use app\models\Wilayah;
use kartik\mpdf\Pdf;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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

    public function actionExportPdf(string $kelurahan_id, string $tanggal)
    {
        // Query data cuaca berdasarkan kelurahan dan tanggal terpilih
        $dataCuaca = Cuaca::find()
            ->where(['kode_adm4' => $kelurahan_id])
            ->andWhere(['DATE(local_datetime)' => $tanggal])
            ->orderBy(['local_datetime' => SORT_ASC])
            ->all();

        if (empty($dataCuaca)) {
            Yii::$app->session->setFlash('error', 'Tidak ada data cuaca untuk tanggal tersebut.');
            return $this->redirect(['index', 'kelurahan_id' => $kelurahan_id, 'tanggal' => $tanggal]);
        }

        // Ambil informasi nama wilayah kelurahan (Opsional)
        $namaKelurahan = $kelurahan_id;
        $kelurahanModel = Wilayah::findOne(['kode' => $kelurahan_id]);
        if ($kelurahanModel) {
            $namaKelurahan = $kelurahanModel->nama;
        }

        // Render HTML khusus tampilan PDF
        $content = $this->renderPartial('_report_pdf', [
            'dataCuaca' => $dataCuaca,
            'namaKelurahan' => $namaKelurahan,
            'kodeAdm4' => $kelurahan_id,
            'tanggal' => $tanggal,
        ]);

        // Setup Konfigurasi PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
                body { font-family: sans-serif; font-size: 10pt; color: #333; }
                .header-title { text-align: center; font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
                .header-sub { text-align: center; font-size: 10pt; color: #666; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .meta-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
                .meta-table td { padding: 4px 8px; vertical-align: top; }
                .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .table-data th { background-color: #007bff; color: #ffffff; border: 1px solid #0056b3; padding: 8px; text-align: center; font-weight: bold; }
                .table-data td { border: 1px solid #cccccc; padding: 7px; text-align: center; }
                .table-data tr:nth-child(even) { background-color: #f9f9f9; }
                .footer-text { margin-top: 30px; text-align: right; font-size: 9pt; color: #777; }
            ',
            'options' => ['title' => "Laporan Cuaca - {$namaKelurahan} ({$tanggal})"],
            'methods' => [
                'SetHeader' => ['LAPORAN PRAKIRAAN CUACA BMKG||Tgl Cetak: ' . date('d/m/Y H:i')],
                'SetFooter' => ['|Halaman {PAGENO} dari {nbpg}|'],
            ]
        ]);

        return $pdf->render();
    }

    /**
     * Halaman View Terpisah untuk Menampilkan Galeri Gambar
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Upload Gambar dari Halaman View Detail
     */
    public function actionUploadDetail(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            if ($model->imageFiles && $model->uploadMultiple()) {
                Yii::$app->session->setFlash('success', 'Gambar berhasil ditambahkan.');
            } else {
                Yii::$app->session->setFlash('error', 'Gagal mengunggah gambar.');
            }
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionUpload(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            // Ambil semua file yang diupload (multiple)
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            if ($model->imageFiles && $model->uploadMultiple()) {
                Yii::$app->session->setFlash('success', 'Beberapa gambar berhasil diunggah.');
            } else {
                Yii::$app->session->setFlash('error', 'Gagal mengunggah gambar.');
            }
        }

        return $this->redirect(['index', 'adm4' => $model->kode_adm4]);
    }

    // Action untuk menghapus 1 foto spesifik dari galeri
    public function actionDeleteGambar(int $id): \yii\web\Response
    {
        $gambar = CuacaGambar::findOne($id);
        if ($gambar) {
            $cuaca = Cuaca::findOne($gambar->cuaca_id);
            $filePath = Yii::getAlias('@webroot/uploads/cuaca/') . $gambar->file_name;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $gambar->delete();
            Yii::$app->session->setFlash('success', 'Gambar berhasil dihapus.');
            return $this->redirect(['index', 'adm4' => $cuaca->kode_adm4 ?? null]);
        }

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Cuaca
    {
        if (($model = Cuaca::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Data tidak ditemukan.');
    }
}
