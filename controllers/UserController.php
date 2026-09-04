<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\UserForm;
use app\models\UserSearch;
use app\services\UserService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
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
                            'roles' => ['admin'],
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $roles = Yii::$app->authManager->getRoles();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'roles' => $roles,
        ]);
    }

    public function actionCreate()
    {
        $model = new UserForm();
        $roles = Yii::$app->authManager->getRoles();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $data = $model->attributes;
            $res = $this->userService->createUser($data, $model->role);

            if ($res['success']) {
                Yii::$app->session->setFlash('success', $res['message']);
                return $this->redirect(['index']);
            }

            if (isset($res['errors'])) {
                $model->addErrors($res['errors']);
            } else {
                Yii::$app->session->setFlash('error', $res['message'] ?? 'Gagal membuat user.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

    public function actionUpdate($id)
    {
        $user = $this->userService->getUserById((int)$id);
        $roles = Yii::$app->authManager->getRoles();

        $model = new UserForm();
        $model->id = $user->id;
        $model->username = $user->username;
        $model->email = $user->email;
        $model->status = $user->status;

        $userRoles = Yii::$app->authManager->getRolesByUser($user->id);
        $model->role = !empty($userRoles) ? reset($userRoles)->name : '';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $data = array_filter($model->attributes); // Filter input kosong (seperti password)

            $res = $this->userService->updateUser((int)$id, $data);
            if ($res['success']) {
                if (!empty($model->role)) {
                    $this->userService->updateRole((int)$id, $model->role);
                }

                Yii::$app->session->setFlash('success', $res['message']);
                return $this->redirect(['index']);
            }

            if (isset($res['errors'])) {
                $model->addErrors($res['errors']);
            } else {
                Yii::$app->session->setFlash('error', $res['message'] ?? 'Gagal memperbarui user.');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function actionDelete($id)
    {
        $res = $this->userService->deleteUser((int)$id);

        if ($res['success']) {
            Yii::$app->session->setFlash('success', $res['message']);
        } else {
            Yii::$app->session->setFlash('error', $res['message']);
        }

        return $this->redirect(['index']);
    }

    public function actionToggleStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');

        return $this->userService->toggleStatus((int)$id);
    }

    public function actionChangeRole()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->request->post('userId');
        $roleName = Yii::$app->request->post('roleName');

        return $this->userService->updateRole((int)$userId, (string)$roleName);
    }

    public function actionView($id)
    {
        $detail = $this->userService->getUserDetail((int)$id);

        return $this->render('view', [
            'model' => $detail['user'],
            'roleName' => $detail['roleName'],
        ]);
    }

    public function actionResetPassword($id)
    {
        $res = $this->userService->resetPassword((int)$id, '123456');

        if ($res['success']) {
            Yii::$app->session->setFlash('success', $res['message']);
        } else {
            Yii::$app->session->setFlash('error', $res['message']);
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }
}
