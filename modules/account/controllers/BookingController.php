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

    public function actionMailView($id)
    {
        $secretKey = 'secret_key'; 

        $decoded = base64_decode($id);
        if (!$decoded) {
            throw new \yii\web\ForbiddenHttpException('Неверный токен');
        }

        // Разбираем ID и хеш
        [$id, $hash] = explode(':', $decoded, 2);

        // Проверяем хеш
        if (hash_hmac('sha256', $id, $secretKey) !== $hash) {
            throw new \yii\web\ForbiddenHttpException('Ошибка аутентификации');
        }

        // Ищем бронь по ID
        $booking = Booking::findOne($id);
        if (!$booking) {
            throw new \yii\web\NotFoundHttpException('Бронь не найдена');
        }

        // Показываем страницу с бронью
        return $this->render('view', ['model' => $booking]);
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
        $email
        )
     {
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
                 'restaurant_link' => Yii::$app->urlManager->createAbsoluteUrl(['/site/index']),
             ])
             ->setFrom('restaurant.project@mail.ru')
             ->setTo($email)
             ->setSubject('test')
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
                if($model->save()) {
                    $IdTables = explode(',', $model->selected_tables);
                    // VarDumper::dump($model->selected_tables, 10, true); die;
                    $this->runAction('mail', [
                        'fio_guest' => $model->fio_guest,
                        'booking_date' => $model->booking_date,
                        'booking_time_start' => $model->booking_time_start,
                        'booking_time_end' => $model->booking_time_end,
                        'count_guest' => $model->count_guest,
                        'email' => $model->email,
                        'IdTables' => $model->selected_tables,
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


public function actionGetBookedTables()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $bookingDate = Yii::$app->request->post('booking_date');
    $bookingTimeStart = Yii::$app->request->post('booking_time_start');
    $bookingTimeEnd = Yii::$app->request->post('booking_time_end');

    $bookedTables = [];

    $bookings = Booking::find()
        ->where(['booking_date' => $bookingDate, 'status_id' => Status::getStatusId('Забронировано')])
        ->andWhere(['<', 'booking_time_start', $bookingTimeEnd])
        ->andWhere(['>', 'booking_time_end', $bookingTimeStart])
        ->all();

    if ($bookings) {
        foreach ($bookings as $booking) {

                $bookingTables = BookingTable::find()
                ->where([
                    'booking_id' => $booking->id,
                    'status_id' => Status::getStatusId('Забронировано') || Status::getStatusId('Свободно')
                ])
                ->andWhere([
                    'or',
                    ['<=', 'delete_started_at', date('Y-m-d H:i:s', time() - 20)],
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
    public function actionCancelBooking($id)
    {
        $model = $this->findModel($id);
                            VarDumper::dump($bookingTables, 10, true); die;

        $bookedTables = [];
        $bookings = Booking::findOne(['id' => $model->id]);
        $bookings = Status::getStatusId('Отменено');

    

        if ($bookings) {
                // $bookingTables = BookingTable::find()->where(['booking_id' => $bookings->id])->all();
                $bookingTables = BookingTable::findAll(['booking_id' => $bookings->id]);
                // VarDumper::dump($bookingTables, 10, true); die;
                foreach ($bookingTables as $bookingTable) {
                    $bookedTables[] = $bookingTable->table_id;
                }
                // VarDumper::dump($bookedTables, 10, true); die;
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'selected_tables' => $model->selected_tables]);
        }

        return $this->render('cancel', [
            'model' => $model,
        ]);
    }


public function actionToggleDelete()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $tableId = Yii::$app->request->post('table_id');
    $bookingId = Yii::$app->request->post('booking_id');
    // $pending = filter_var(Yii::$app->request->post('pending'), FILTER_VALIDATE_BOOLEAN);

    if ($bookingTable = BookingTable::findOne(['table_id' => $tableId, 'booking_id' => $bookingId])) {
        $bookingTable->status_id = Status::getStatusId('Свободно');
        $bookingTable->delete_started_at = date('Y-m-d H:i:s');
        // $bookingTable->is_blocked = 1;
        $bookingTable->save(false);
        return ['success' => true];
    }     

    return ['success' => false, 'message' => 'Стол не найден'];

    // if ($pending) {
    //     // Стол отмечаем как ожидающий удаления
    //     // $bookingTable->is_pending_delete = 1;
    //     $bookingTable->delete_started_at = date('Y-m-d H:i:s');
    // } else {
    //     // Если отменяем удаление
    //     $bookingTable->is_pending_delete = 0;
    //     $bookingTable->delete_started_at = null;
    // }

    $bookingTable->save(false);
    // return ['success' => true, 'pending' => $pending];
}


public function actionReturnTable()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $tableId   = Yii::$app->request->post('table_id');
    $bookingId = Yii::$app->request->post('booking_id');

    if($bookingTable = BookingTable::findOne(['table_id' => $tableId, 'booking_id' => $bookingId])) {
        $bookingTable->status_id = Status::getStatusId('Забронировано');
        $bookingTable->delete_started_at = null;
        $bookingTable->save(false);

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
