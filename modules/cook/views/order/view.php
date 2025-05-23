<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Заказ #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этот заказ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'table_id',
                'value' => $model->table_id,
                'visible' => (bool)$model->table_id,
            ],
            [
                'attribute' => 'created_at',
                'format' => ['time', 'php:H:i'],
            ],
            [
                'attribute' => 'order_type',
                'value' => function ($model) {
                    return $model->order_type == 10 ? 'На месте' : 'С собой';
                },
            ],
[
    'label' => 'Статус заказа',
    'format' => 'raw',
    'value' => function ($model) {
        $current = $model->order_status;
        $options = [];

        // Логика смены статусов
        if ($current === 'new') {
            $options = [
                'cooking' => 'Готовится',
                'ready' => 'Готово к выдаче',
                'canceled' => 'Отменено',
            ];
        } elseif ($current === 'cooking') {
            $options = [
                'ready' => 'Готово к выдаче',
                'canceled' => 'Отменено',
            ];
        }

        if (empty($options)) {
            return isset($model->status) && isset($model->status->title) ? $model->status->title : '—';
        }

        $form = \yii\helpers\Html::beginForm(['order/update-status', 'id' => $model->id], 'post', ['style' => 'display:inline']) .
            \yii\helpers\Html::dropDownList('status', null, $options, ['prompt' => 'Выбрать', 'class' => 'form-control d-inline', 'style' => 'width:auto; display:inline-block;']) .
            \yii\helpers\Html::submitButton('Сменить', ['class' => 'btn btn-sm btn-success ml-2']) .
            \yii\helpers\Html::endForm();

        return $form;
    },
],

            [
                'attribute' => 'waiter_id',
                'value' => $model->waiter->fio ?? '—',
            ],
        ],
    ]) ?>

    <h2>Блюда заказа</h2>
    <?= GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->orderDishes,
            'pagination' => false,
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'dish_id',
                'label' => 'Блюдо',
                'value' => function ($dish) {
                    return $dish->dish->title ?? '(неизвестно)';
                },
            ],
            [
                'attribute' => 'count',
                'label' => 'Количество',
            ],
        ],
    ]) ?>
</div>
