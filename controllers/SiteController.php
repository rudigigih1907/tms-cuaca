<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ChangePasswordForm;
use Yii;
use app\models\ContactForm;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index', 'register', 'change-password'],
                'rules' => [
                    [
                        'actions' => ['register'],
                        'allow' => true,
                        'roles' => ['?'], // Guest (belum login)
                    ],
                    [
                        'actions' => ['logout', 'index', 'change-password', 'dashboard'],
                        'allow' => true,
                        'roles' => ['@'], // User yang sudah login
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionChangePassword()
    {
        $user = Yii::$app->user->identity;

        $model = new ChangePasswordForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->session->setFlash('success', 'Password Anda berhasil diperbarui.');
            return $this->refresh();
        }

        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    /**
     * Halaman Dashboard User
     */
    public function actionDashboard()
    {
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;

        // Ambil role user aktif
        $userRoles = Yii::$app->authManager->getRolesByUser($user->id);
        $roleName = !empty($userRoles) ? reset($userRoles)->name : 'User';

        return $this->render('dashboard', [
            'user' => $user,
            'roleName' => $roleName,
        ]);
    }
}
