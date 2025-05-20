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
        $dishes[] = $dish;
    }

    // 3) Валидация: сам Order и все OrderDish
    $valid = $model->validate();
    foreach ($dishes as $i => $dish) {
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
        return $this->redirect(['view', 'id' => $model->id]);
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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
}
