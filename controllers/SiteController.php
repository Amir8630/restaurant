<?php

namespace app\controllers;

use app\models\Booking;
use app\models\BookingTable;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RegisterForm;
use app\models\User;
use PhpParser\Node\Stmt\Else_;
use yii\helpers\VarDumper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // echo Yii::$app->security->generatePasswordHash('User123'); die;
        // var_dump(time('H:i:s')); die;
        // var_dump(date('H:i:s')); 
        // var_dump(date('H:i:s') - '20'); die;

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Вы успешно вошли в систему');
            
            // не готово
            switch (Yii::$app->user->identity->userRole) {
                case 'admin':
                    return $this->redirect('/admin');
                    break;
                case 'user':
                    return $this->redirect('/admin');
                    break;
                case 'manager':
                    return $this->redirect('/manager');
                case 'waiter':
                    return $this->redirect('/waiter');
                case 'cook':
                    return $this->redirect('/cook');
                    break;
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('info', 'Вы успешно вышли из системы');

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionTest()
    {
        return $this->render('test');
    }

    public function actionMail()
    {
        Yii::$app->mailer->htmlLayout = '@app/mail/layouts/html';
        if(Yii::$app->mailer
            ->compose('mail', [
                'fio_guest' => 'Amir',
                'booking_date' => '02.05.2025',
                'booking_time_start' => '12:00',
                'booking_time_end' => 3,
                'count_guest' => '2,4,5',
                'IdTables' => '2,4,5',
                'restaurant_link' => Yii::$app->urlManager->createAbsoluteUrl(['/site/index']),
            ])
            ->setFrom('restaurant.project@mail.ru')
            // ->setTo('restaurant.project@mail.ru')
            ->setTo('barakatamir2005@gmail.com')
            ->setSubject('test')
            ->send()
        ) {
            Yii::$app->session->setFlash('success', 'Вы успешно отправили письмо');
        } else {
            Yii::$app->session->setFlash('warning', 'Ошибка!');
        }
        
        return $this->render('index');
    }

  
    public function actionRegister()
    {
        $model = new RegisterForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->register()) {
                if (Yii::$app->user->login($user, 60*60)) {
                    Yii::$app->session->setFlash('success', 'Вы успешно вошли в систему');
                    $this->redirect('/account');
                    // нету авто адресаци от роли 
                }
            }
        }
        return $this->render('register', compact('model'));
    }
}
