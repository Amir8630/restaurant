<?php

namespace app\modules\cook\controllers;

use app\models\Order;
use app\models\OrderDish;
use app\models\Status;
use app\modules\cook\models\OrderSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Response;

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
     * @param int $id Заказ №
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
     * @param int $id Заказ №
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
     * @param int $id Заказ №
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
     * @param int $id Заказ №
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

    if (in_array($cookingId, $all, true)) {
        $order->order_status = $cookingId;
    } elseif (!in_array($cookingId, $all, true)
        && count(array_unique($all)) === 1
        && current($all) === $readyId
    ) {
        $order->order_status = $readyId;
    }

    $order->save(false);

    return ['success' => true];
}
}
