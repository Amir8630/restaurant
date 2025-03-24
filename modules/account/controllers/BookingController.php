<?php

namespace app\modules\account\controllers;

use app\models\Booking;
use app\models\BookingTable;
use app\models\Status;
use app\modules\account\models\BookingSearch;
use Yii;
use yii\bootstrap5\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Response;

/**
 * BookingController implements the CRUD actions for Booking model.
 */
class BookingController extends Controller
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
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Booking models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookingSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Booking model.
     * @param int $id Бронь №
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionMailView($token)
    {
        if($model = Booking::findOne(['token' => $token])) {
            return $this->render('view', ['model' => $model]);
        } else {
            echo 'error'; die;
        }
    }

    /**
     * Creates a new Booking model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    

     public function actionMail(
            $fio_guest,
            $booking_date,
            $booking_time_start,
            $booking_time_end,
            $count_guest,
            $IdTables,
            $email,
            $token
        )
     {
        Yii::$app->urlManager->baseUrl = '';
        Yii::$app->urlManager->scriptUrl = '';
        Yii::$app->urlManager->hostInfo = '';
        $restaurant_link = 'http://localhost/account/booking/mail-view?token=' . $token;

        Yii::$app->mailer->htmlLayout = '@app/mail/layouts/html';
        if(Yii::$app->mailer
            ->compose('mail', [
            'fio_guest' => $fio_guest,
            'booking_date' => $booking_date,
            'booking_time_start' => $booking_time_start,
            'booking_time_end' => $booking_time_end,
            'count_guest' => $count_guest,
            'IdTables' => $IdTables,
            'email' => $email,
            'restaurant_link' => $restaurant_link,
            // 'restaurant_link' => 'http://' . pathinfo($_SERVER['PWD'])['filename'] . '.wsr.ru/account/booking/view?id='. $id .'',

            ])
            ->setFrom('restaurant.project@mail.ru')
            ->setTo($email)
            ->setSubject('Подтверждение бронирования')
            ->send()
        ) {
            Yii::$app->session->setFlash('success', 'Вы успешно отправили письмо');
        } else {
            Yii::$app->session->setFlash('warning', 'Ошибка!');
        }
         
     }

    public function actionCreate()
    {
        $model = new Booking();
        $model->user_id = Yii::$app->user->id;
        $model->status_id = Status::getStatusId('Забронировано');   

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }

                //ген уникальный токен
                while(true) {
                    $token = bin2hex(random_bytes(32));
                    if (!Booking::findOne(['token' => $token])) {
                        break;
                    } 
                }
                $model->token = $token; 
                
                if($model->save()) {
                    $IdTables = explode(',', $model->selected_tables);
                    $this->runAction('mail', [
                        'fio_guest' => $model->fio_guest,
                        'booking_date' => $model->booking_date,
                        'booking_time_start' => $model->booking_time_start,
                        'booking_time_end' => $model->booking_time_end,
                        'count_guest' => $model->count_guest,
                        'email' => $model->email,
                        'IdTables' => $model->selected_tables,
                        'token' => $model->token,
                    ]);
                    foreach ($IdTables as $key => $IdTable) {

                        $booking_table = new BookingTable();
                        $booking_table->status_id = Status::getStatusId('Забронировано');
                        $booking_table->table_id = $IdTable;
                        $booking_table->booking_id = $model->id;
                        
                        if($booking_table->save()) {
                           
                        } //else {
                        //     VarDumper::dump($booking_table->errors, 10, true); die;
                        // }
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                } //else {
                //     VarDumper::dump($model->errors, 10, true); die;
                // }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

//поиск забронированных столов
public function actionGetBookedTables()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $bookingDate = Yii::$app->request->post('booking_date');
    $bookingTimeStart = Yii::$app->request->post('booking_time_start');
    $bookingTimeEnd = Yii::$app->request->post('booking_time_end');

    $bookedTables = [];
    // поиск всех броней
    $bookings = Booking::find()
        ->where(['booking_date' => $bookingDate, 'status_id' => Status::getStatusId('Забронировано')])
        ->andWhere(['<', 'booking_time_start', $bookingTimeEnd])
        ->andWhere(['>', 'booking_time_end', $bookingTimeStart])
        ->all();

     // поиск всех столов у брони
    if ($bookings) {
        foreach ($bookings as $booking) {
            $bookingTables = BookingTable::find()
            ->where([
                'booking_id' => $booking->id,
                'status_id' => [Status::getStatusId('Забронировано'), Status::getStatusId('Свободно')]
            ])
            ->andWhere([
                'or',
                ['>', 'delete_started_at', date('Y-m-d H:i:s', time() - 50)],
                ['delete_started_at' => null]
            ])
            ->all();
            // var_dump($bookingTables->createCommand()->rawSql); die;        

            foreach ($bookingTables as $bookingTable) {
                $bookedTables[] = $bookingTable->table_id;
            }
        }
        
        
    }
    return $bookedTables;
}

    /**
     * Updates an existing Booking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id Бронь №
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCancel($id)
    {
        if($model = $this->findModel($id)) {
            $model->status_id = Status::getStatusId('Отменено');
            if(!$model->save(false)) {
                VarDumper::dump($model->errors, 10, true); die;
            }

            BookingTable::updateAll(
                [
                    'delete_started_at' => date('Y-m-d H:i:s', time() - 50),
                    'status_id' => Status::getStatusId('Свободно')
                ],
                ['booking_id' => $model->id]
            );

            Yii::$app->session->setFlash('success', 'Вы успешно отменили бронь');

            $tabels = BookingTable::findAll(['booking_id' => $model->id]);
            $tabels = implode(',', array_map(fn($t) => $t->table_id, $tabels));

            Yii::$app->mailer->htmlLayout = '@app/mail/layouts/html';
            Yii::$app->mailer
                ->compose('cancel', [
                'fio_guest' => $model->fio_guest,
                'booking_date' => $model->booking_date,
                'booking_time_start' => $model->booking_time_start,
                'booking_time_end' => $model->booking_time_end,
                'count_guest' => $model->count_guest,
                'IdTables' => $tabels,
                'email' => $model->email,
                'restaurant_link' => Yii::$app->urlManager->createAbsoluteUrl(['/site/index']),
                ])
                ->setFrom('restaurant.project@mail.ru')
                ->setTo($model->email)
                ->setSubject('Отмена бронирования')
                ->send();

            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }

    // отмена стола
    // public function actionToggleDelete()
    // {
    //     Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $tableId = Yii::$app->request->post('table_id');
    //     $bookingId = Yii::$app->request->post('booking_id');

    //     if ($bookingTable = BookingTable::findOne(['table_id' => $tableId, 'booking_id' => $bookingId])) {
    //         $bookingTable->status_id = Status::getStatusId('Свободно');
    //         $bookingTable->delete_started_at = date('Y-m-d H:i:s');
    //         $bookingTable->save(false);
    //         return ['success' => true];
    //     }     

    //     return ['success' => false, 'message' => 'Стол не найден'];
    // }

    // отмена стола
    public function actionToggleDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $tableId = Yii::$app->request->post('table_id');
        $bookingId = Yii::$app->request->post('booking_id');

        if ($bookingTable = BookingTable::findOne(['table_id' => $tableId, 'booking_id' => $bookingId])) {
            $bookingTable->status_id = Status::getStatusId('Свободно');
            $bookingTable->delete_started_at = date('Y-m-d H:i:s');
            $bookingTable->save(false);

            return ['success' => true];
        }     

        return ['success' => false, 'message' => 'Стол не найден'];
    }

    // отмена отмены стола
    public function actionReturnTable()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $tableId   = Yii::$app->request->post('table_id');
        $bookingId = Yii::$app->request->post('booking_id');

        if($bookingTable = BookingTable::findOne(['table_id' => $tableId, 'booking_id' => $bookingId])) {
            $bookingTable->status_id = Status::getStatusId('Забронировано');
            $bookingTable->delete_started_at = null;
            if(! $bookingTable->save(false)) {
                VarDumper::dump($bookingTable->errors, 10, true); die;
            }
            // $bookingTable->save(false);

            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Стол не найден'];
    }

public function actionUpdate($id)
{
    $model = $this->findModel($id);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}



    /**
     * Finds the Booking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id Бронь №
     * @return Booking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Booking::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
