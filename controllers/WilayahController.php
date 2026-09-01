<?php

namespace app\controllers;

use app\models\Wilayah;
use app\models\WilayahSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WilayahController implements the CRUD actions for Wilayah model.
 */
class WilayahController extends Controller
{
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

    /**
     * Lists all Wilayah models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;

        $provinsiId  = $request->get('provinsi_id');
        $kabupatenId = $request->get('kabupaten_id');
        $kecamatanId = $request->get('kecamatan_id');
        $kelurahanId = $request->get('kelurahan_id');

        // Mengambil data untuk Dropdown Cascade
        $listProvinsi  = Wilayah::getProvinsi();
        $listKabupaten = Wilayah::getChildList($provinsiId, 5);
        $listKecamatan = Wilayah::getChildList($kabupatenId, 8);
        $listKelurahan = Wilayah::getChildList($kecamatanId, 13);

        // Query untuk Data GridView
        $query = Wilayah::find();

        if ($kelurahanId) {
            $query->where(['kode' => $kelurahanId]);
        } elseif ($kecamatanId) {
            $query->where(['like', 'kode', $kecamatanId . '%', false])
                ->andWhere(['CHAR_LENGTH(kode)' => 13]); // Tampilkan Kelurahan di Kecamatan ini
        } elseif ($kabupatenId) {
            $query->where(['like', 'kode', $kabupatenId . '%', false])
                ->andWhere(['CHAR_LENGTH(kode)' => 8]);  // Tampilkan Kecamatan di Kabupaten ini
        } elseif ($provinsiId) {
            $query->where(['like', 'kode', $provinsiId . '%', false])
                ->andWhere(['CHAR_LENGTH(kode)' => 5]);  // Tampilkan Kabupaten di Provinsi ini
        } else {
            $query->where(['CHAR_LENGTH(kode)' => 2]);    // Default: Tampilkan daftar Provinsi
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'nama' => SORT_ASC, // Urutan default
                ],
                'attributes' => [
                    'kode' => [
                        'asc' => ['kode' => SORT_ASC],
                        'desc' => ['kode' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => 'Kode Wilayah',
                    ],
                    'nama' => [
                        'asc' => ['nama' => SORT_ASC],
                        'desc' => ['nama' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => 'Nama Wilayah',
                    ],
                ],
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
        ]);
    }

    /**
     * Displays a single Wilayah model.
     * @param string $kode Kode
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($kode)
    {
        return $this->render('view', [
            'model' => $this->findModel($kode),
        ]);
    }

    /**
     * Creates a new Wilayah model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Wilayah();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'kode' => $model->kode]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Wilayah model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $kode Kode
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($kode)
    {
        $model = $this->findModel($kode);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'kode' => $model->kode]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Wilayah model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $kode Kode
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($kode)
    {
        $this->findModel($kode)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Wilayah model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $kode Kode
     * @return Wilayah the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($kode)
    {
        if (($model = Wilayah::findOne(['kode' => $kode])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
