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
use yii\web\YiiAsset;
use yii\helpers\Url;
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
        // Получаем текущую дату и время
        $today = date('Y-m-d');
        $now = date('H:i:s');

        // 1. Все брони, где дата меньше сегодня, переводим в статус 15
        \app\models\Booking::updateAll(
            ['status_id' => 15],
            [
            'and',
            ['status_id' => 1],
            ['<', 'booking_date', $today]
            ]
        );

        // 2. Все брони на сегодня, где время окончания меньше или равно текущему времени, переводим в статус 15
        \app\models\Booking::updateAll(
            ['status_id' => 15],
            [
            'and',
            ['status_id' => 1],
            ['booking_date' => $today],
            ['<=', 'booking_time_end', $now]
            ]
        );

        // Далее идёт стандартная логика index для рендеринга списка броней
        $searchModel = new BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

    public function actionMail($id)
    {
        $Booking = Booking::findOne($id);
        if ($Booking === null) {
            throw new NotFoundHttpException('Бронирование не найдено.');
        }

        $BookingTable = BookingTable::find()
            ->where(['booking_id' => $Booking->id])
            ->all();

        $tableIds = implode(',', array_map(fn($table) => $table->table_id, $BookingTable));

        // вроде можно использовать url to для создания ссылки, надо будеть попробовать
        $restaurant_link = Yii::$app->urlManager->createAbsoluteUrl(['account/booking/mail-view', 'token' => $Booking->token]);
        // для localhost
        // $restaurant_link = 'http://localhost/account/booking/mail-view?token=' . $Booking->token;

        // для сервера
        // $restaurant_link = 'http://avcsvty-m2.wsr.ru/account/booking/mail-view?token=' . $Booking->token

        Yii::$app->mailer->htmlLayout = '@app/mail/layouts/html';
        if (Yii::$app->mailer
            ->compose('mail', [
                'id' => $Booking->id,
                'fio_guest' => $Booking->fio_guest,
                'booking_date' => $Booking->booking_date,
                'booking_time_start' => $Booking->booking_time_start,
                'booking_time_end' => $Booking->booking_time_end,
                'count_guest' => $Booking->count_guest,
                'IdTables' => $tableIds,
                'email' => $Booking->email,
                'restaurant_link' => $restaurant_link,
            ])
            ->setFrom('restaurant.project@mail.ru')
            ->setTo($Booking->email)
            ->setSubject('Подтверждение бронирования')
            ->send()
        ) {
            // Yii::$app->session->setFlash('success', 'Вы успешно отправили письмо');
        } else {
            // Yii::$app->session->setFlash('warning', 'Ошибка при отправке письма!');
        }

        return $this->asJson(['status' => 'success']);
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

                if($model->validate()) {
                
                    // Генерация уникального токена
                    while (true) {
                        $token = bin2hex(random_bytes(32));
                        if (!Booking::findOne(['token' => $token])) {
                            break;
                        }
                    }
                    $model->token = $token;

                    $selectedTables = explode(',', $model->selected_tables);

                    // Поиск существующих броней, которые пересекаются по времени
                    $bookings = Booking::find()
                        ->where(['booking_date' => $model->booking_date, 'status_id' => Status::getStatusId('Забронировано')])
                        ->andWhere(['<', 'booking_time_start', $model->booking_time_end])
                        ->andWhere(['>', 'booking_time_end', $model->booking_time_start])
                        ->all();

                    $bookedTables = [];

                    // Получение уже забронированных столов
                    if ($bookings) {
                        foreach ($bookings as $booking) {
                            $bookingTables = BookingTable::find()
                                ->where(['booking_id' => $booking->id, 'status_id' => Status::getStatusId('Забронировано')])
                                ->all();

                            foreach ($bookingTables as $bookingTable) {
                                $bookedTables[] = $bookingTable->table_id;
                            }
                        }
                    }

                    // Проверка, есть ли пересечения
                    $conflictingTables = array_intersect($selectedTables, $bookedTables);

                    if (!empty($conflictingTables)) {                    
                        if(count($conflictingTables) > 1) {
                            $tables = implode(',', $conflictingTables);
                            $model->addError('selected_tables', "$tables столы уже забронированы.");
                            Yii::$app->session->setFlash('error', "$tables столы уже забронированы."); 
                        } else {
                            $table = implode(',', $conflictingTables);
                            $model->addError('selected_tables', "$table стол уже забронирован.");
                            Yii::$app->session->setFlash('error', "$table стол уже забронирован."); 
                        }

                        // Удаление забронированных столов из скрытого поля
                        $selectedTables = array_diff($selectedTables, $bookedTables);
                        $model->selected_tables = implode(',', $selectedTables);
                    

                        return $this->render('create', [
                            'model' => $model,
                        ]);

                    }

                    // Если столы свободны, сохраняем бронь
                    if ($model->save()) {
                        foreach ($selectedTables as $tableId) {
                            $booking_table = new BookingTable();
                            $booking_table->status_id = Status::getStatusId('Забронировано');
                            $booking_table->table_id = $tableId;
                            $booking_table->booking_id = $model->id;
                            $booking_table->save();
                        }

                        return $this->redirect(['view', 'id' => $model->id, 'sendMail' => 1]);
                    }
                }
            }
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

    public function actionCancel($id)
    {
        if($model = $this->findModel($id)) {
            $model->status_id = Status::getStatusId('Отменён');
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

            // redirect + отправка письма 
            return $this->redirect(['view', 'id' => $model->id, 'sendMailCancel' => 1]);

        }
    }

    public function actionCancelModal($id)
    {
        if($model = $this->findModel($id)) {
            $model->status_id = Status::getStatusId('Отменён');
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

            return $this->asJson(true);
            // ждеть dataProvider   а что если не деоатьб ренлер а промто через джс сделать  reload
        }
    }

        public function actionMailCancel($id = null)
    {
        $id = $id ?: Yii::$app->request->post('id');
        
        $Booking = Booking::findOne($id);
        if ($Booking === null) {
            throw new NotFoundHttpException('Бронирование не найдено.');
        }

        $BookingTable = BookingTable::find()
            ->where(['booking_id' => $Booking->id])
            ->all();

        $tableIds = implode(',', array_map(fn($table) => $table->table_id, $BookingTable));

        Yii::$app->mailer->htmlLayout = '@app/mail/layouts/html';
        if (Yii::$app->mailer
            ->compose('cancel', [
                'id' => $Booking->id,
                'fio_guest' => $Booking->fio_guest,
                'booking_date' => $Booking->booking_date,
                'booking_time_start' => $Booking->booking_time_start,
                'booking_time_end' => $Booking->booking_time_end,
                'count_guest' => $Booking->count_guest,
                'IdTables' => $tableIds,
                'email' => $Booking->email,
                'restaurant_link' => Yii::$app->urlManager->createAbsoluteUrl(['/site/index']),
            ])
            ->setFrom('restaurant.project@mail.ru')
            ->setTo($Booking->email)
            ->setSubject('Отмена бронирования')
            ->send()
        ) {
            // return $this->asJson(['status' => 'ok']);
            // Yii::$app->session->setFlash('success', 'Вы успешно отправили письмо');
        } else {
            // Yii::$app->session->setFlash('warning', 'Ошибка при отправке письма!');
        }

    }

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
