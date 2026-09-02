<?php

namespace app\controllers;

use app\models\Cuaca;
use app\models\CuacaGambar;
use app\models\Wilayah;
use app\services\BmkgSyncService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use app\services\CuacaExportPdfService;

class CuacaController extends \yii\web\Controller
{
    private BmkgSyncService $bmkgService;
    private CuacaExportPdfService $exportPdfService;

    // Inject Service via constructor / inisialisasi
    public function init()
    {
        parent::init();
        $this->bmkgService = new BmkgSyncService();
        $this->exportPdfService = new CuacaExportPdfService();
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        // SET KODE DEFAULT JAKARTA UTARA (Sesuaikan dengan kode di DB Anda)
        // DKI Jakarta = 31, Jakarta Utara = 31.72 / 3172, dst.
        $defaultProvinsiId  = '31';        // DKI Jakarta
        $defaultKabupatenId = '31.72';     // Kota Jakarta Utara
        $defaultKecamatanId = '31.72.02';  // Tanjung Priok
        $defaultKelurahanId = '31.72.02.1001'; // Tanjung Priok

        $provinsiId  = $request->get('provinsi_id', $defaultProvinsiId);
        $kabupatenId = $request->get('kabupaten_id', $defaultKabupatenId);
        $kecamatanId = $request->get('kecamatan_id', $defaultKecamatanId);
        $kelurahanId = $request->get('kelurahan_id', $defaultKelurahanId);
        $tanggal     = $request->get('tanggal');

        $listProvinsi  = Wilayah::getProvinsi();
        $listKabupaten = Wilayah::getChildList($provinsiId, 5);
        $listKecamatan = Wilayah::getChildList($kabupatenId, 8);
        $listKelurahan = Wilayah::getChildList($kecamatanId, 13);

        // Ambil daftar tanggal unik + jumlah records per tanggal
        $groupedDates = [];
        if (!empty($kelurahanId)) {
            $query = Cuaca::find()
                ->select([
                    "DATE(local_datetime) AS tgl",
                    "COUNT(id) AS total_jam"
                ])
                ->where(['kode_adm4' => $kelurahanId]);

            if (!empty($tanggal)) {
                $query->andWhere(['DATE(local_datetime)' => $tanggal]);
            }

            $groupedDates = $query->groupBy(["DATE(local_datetime)"])
                ->orderBy(['tgl' => SORT_DESC])
                ->asArray()
                ->all();
        }

        return $this->render('index', [
            'groupedDates' => $groupedDates,
            'provinsiId'   => $provinsiId,
            'kabupatenId'  => $kabupatenId,
            'kecamatanId'  => $kecamatanId,
            'kelurahanId'  => $kelurahanId,
            'listProvinsi' => $listProvinsi,
            'listKabupaten' => $listKabupaten,
            'listKecamatan' => $listKecamatan,
            'listKelurahan' => $listKelurahan,
        ]);
    }

    /**
     * Mengambil & menyimpan data langsung dari API BMKG berdasarkan adm4.
     */
    public function actionSync(): Response
    {
        $adm4 = Yii::$app->request->post('adm4');

        // Panggil Service
        $result = $this->bmkgService->syncByAdm4($adm4 ?? '');

        if ($result['success']) {
            Yii::$app->session->setFlash('success', $result['message']);
        } else {
            Yii::$app->session->setFlash('error', $result['message']);
        }

        return $this->redirect(['index', 'adm4' => $adm4]);
    }

    public function actionExportPdf(string $kelurahan_id, string $tanggal)
{
    // Cukup pass 2 parameter utama
    $pdf = $this->exportPdfService->generatePdf($kelurahan_id, $tanggal);

    if (!$pdf) {
        Yii::$app->session->setFlash('error', 'Tidak ada data cuaca untuk tanggal tersebut.');
        return $this->redirect(['index', 'kelurahan_id' => $kelurahan_id, 'tanggal' => $tanggal]);
    }

    return $pdf->render();
}

    /**
     * Halaman View Terpisah untuk Menampilkan Galeri Gambar
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);
        $detailWilayah = Wilayah::getDetailWilayahByKode($model->kode_adm4);
        return $this->render('view', [
            'model'         => $model,
            'detailWilayah' => $detailWilayah,
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

    /**
     * Action AJAX untuk Load Data Cuaca per Jam berdasarkan Tanggal & Kelurahan
     */
    public function actionGetDetailByDate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $kelurahanId = Yii::$app->request->get('kelurahan_id');
        $tanggal     = Yii::$app->request->get('tanggal');

        if (empty($kelurahanId) || empty($tanggal)) {
            return ['status' => 'error', 'data' => []];
        }

        $models = Cuaca::find()
            ->where(['kode_adm4' => $kelurahanId])
            ->andWhere(['DATE(local_datetime)' => $tanggal])
            ->orderBy(['local_datetime' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($models as $idx => $model) {
            $totalFoto = count($model->galeri);
            $badgeFoto = $totalFoto > 0
                ? '<span class="badge bg-success">' . $totalFoto . ' Foto</span>'
                : '<span class="badge bg-secondary">Kosong</span>';

            $btnGaleri = \yii\helpers\Html::a('<i class="bi bi-images"></i> Lihat Galeri ' . $badgeFoto, ['view', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-outline-primary',
                'data-pjax' => '0',
            ]);

            $data[] =
                [
                    'no'              => $idx + 1,
                    'local_datetime'  => Yii::$app->formatter->asDate($model->local_datetime, 'php:H:i'),
                    'kondisi_cuaca'   => $model->kondisi_cuaca ?? '-',
                    'suhu'            => $model->suhu !== null ? $model->suhu . ' °C' : '-',
                    'kelembapan'      => $model->kelembapan !== null ? $model->kelembapan . ' %' : '-',
                    'kecepatan_angin' => $model->kecepatan_angin !== null ? $model->kecepatan_angin . ' km/j' : '-',
                    'arah_angin'      => $model->arah_angin ?? '-',
                    'action'          => $btnGaleri
                ];
        }

        return [
            'status' => 'success',
            'data'   => $data
        ];
    }
}
