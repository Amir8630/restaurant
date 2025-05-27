<?php

namespace app\modules\waiter\controllers;

use app\models\Order;
use app\models\OrderDish;
use app\models\Status;
use app\modules\waiter\models\OrderSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\OrderForm;
use Exception;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\jui\AutoComplete;
use yii\web\BadRequestHttpException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */


public function actionCreate()
{
    $model = new Order();
    $dishes = [new OrderDish()];
    // При первом заходе — одна пустая строка блюда
    if (empty($model->dishes)) {
        $model->dishes = [new OrderDish()];
    }

    // Если не POST — отрисовываем форму
    if (!Yii::$app->request->isPost) {
        return $this->render('create', [
            'model'  => $model,
            'dishes' => $model->dishes,
        ]);
    }

    // 1) Загружаем данные самого заказа
    $model->load(Yii::$app->request->post());
    $model->waiter_id    = Yii::$app->user->id;
    $model->order_status = Status::getStatusId('Новый');

    // 2) Читаем блюда из POST['OrderDish']
    $postDishes = Yii::$app->request->post('OrderDish', []);
    $dishes     = [];
    foreach ($postDishes as $i => $data) {
        $dish = new OrderDish();
        // Присваиваем поля вручную
        $dish->dish_id = (int)($data['dish_id'] ?? 0);
        $dish->count   = (int)($data['count']   ?? 0);
        $dish->status_id   = Status::getStatusId('Новый');
        $dishes[] = $dish;
    }

    // 3) Валидация: сам Order и все OrderDish
    $valid = $model->validate();
    foreach ($dishes as $i => $dish) {
        // VarDumper::dump($dish->attributes, 10, true); die();
        if (!$dish->validate()) {
            Yii::$app->session->setFlash('error', 'Ошибка в строке #'.($i+1).': '.implode(', ', $dish->getFirstErrors()));
            return $this->render('create', [
                'model'  => $model,
                'dishes' => $dishes,
            ]);
        }
        $valid = true;
    }

    if (!$valid) {
        Yii::$app->session->setFlash('error', 'Ошибка валидации заказа');
        return $this->render('create', [
            'model'  => $model,
            'dishes' => $dishes,
        ]);
    }

    // 4) Сохраняем в транзакции
    $tx = Yii::$app->db->beginTransaction();
    try {
        $model->save(false);
        foreach ($dishes as $dish) {
            $dish->order_id = $model->id;
            $dish->save(false);
        }
        $tx->commit();
        Yii::$app->session->setFlash('success', 'Заказ успешно создан');
        return $this->redirect(['index']);
    } catch (\Throwable $e) {
        $tx->rollBack();
        Yii::$app->session->setFlash('error', 'Ошибка сохранения: ' . $e->getMessage());
    }

    // 5) При неудаче — возвращаемся к форме
    return $this->render('create', [
        'model'  => $model,
        'dishes' => $dishes,
    ]);
}




    // AJAX-экшен для динамического поиска блюд
public function actionDishList($q = '')
{
    $q = trim(mb_strtolower($q));
    
    $dishes = \app\models\Dish::find()
        ->select(['id', 'title'])
        ->asArray()
        ->all();

    // фильтрация и сортировка вручную
    $filtered = array_filter($dishes, function ($dish) use ($q) {
        return mb_stripos($dish['title'], $q) !== false;
    });

    // сортировка: сначала те, где вхождение в начале
    usort($filtered, function ($a, $b) use ($q) {
        $posA = mb_stripos($a['title'], $q);
        $posB = mb_stripos($b['title'], $q);
        return $posA <=> $posB;
    });

    $result = array_map(fn($dish) => [
        'id' => $dish['id'],
        'label' => $dish['title'],
    ], $filtered);

    return $this->asJson($result);
}




    public function actionCreateOriginal()
    {
        $model = new Order();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
public function actionUpdate($id)
{
    $model = $this->findModel($id);
    $dishes = OrderDish::findAll(['order_id' => $model->id]);

    if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post();

        // Загрузка и сохранение основного заказа
        if ($model->load($post) && $model->save()) {

            // Удалим старые блюда
            OrderDish::deleteAll(['order_id' => $model->id]);

            // Сохраняем новые блюда
            if (!empty($post['OrderDish'])) {
                foreach ($post['OrderDish'] as $dishData) {
                    $orderDish = new \app\models\OrderDish();
                    $orderDish->order_id = $model->id;
                    $orderDish->dish_id = $dishData['dish_id'];
                    $orderDish->status_id   = Status::getStatusId('Новый');
                    $orderDish->count = $dishData['count'];
                    $orderDish->save(false);
                }
            }

        return $this->redirect(['index']);
        }
    }

    return $this->render('update', [
        'model' => $model,
        'dishes' => $dishes,
    ]);
}


    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    
public function actionUpdateStatus(int $id, int $status)
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    if (!Yii::$app->request->isAjax) {
        throw new BadRequestHttpException("Ожидался AJAX-запрос");
    }

    $order = $this->findModel($id);
    $order->order_status = $status;
    $order->save(false);

    OrderDish::updateAll(['status_id' => $status], ['order_id' => $order->id]);

    return ['success' => true];
}

public function actionUpdateDishStatus(int $id, int $status)
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    if (!Yii::$app->request->isAjax) {
        throw new BadRequestHttpException("Ожидался AJAX-запрос");
    }

    $dish = OrderDish::findOne($id);
    if (!$dish) {
        throw new NotFoundHttpException("Блюдо не найдено");
    }

    $dish->status_id = $status;
    $dish->save(false);

    $order = $dish->order;
    $all = OrderDish::find()
        ->select('status_id')
        ->where(['order_id' => $order->id])
        ->column();

    $cookingId = Status::getStatusId('готовится');
    $readyId   = Status::getStatusId('готов к выдаче');
    $issuedId = Status::getStatusId('Выдано');

    if (in_array($cookingId, $all, true)) {
        $order->order_status = $cookingId;
    } elseif (!in_array($cookingId, $all, true)
        && count(array_unique($all)) === 1
        && current($all) === $readyId
    ) {
        $order->order_status = $readyId;
    } elseif (count(array_unique($all)) === 1 && current($all) === $issuedId) {
        $order->order_status = $issuedId; // или другой статус, например "Завершён"
    }

    $order->save(false);

    return ['success' => true];
}
}
