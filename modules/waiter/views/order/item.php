<?php
use yii\helpers\Html;

/** @var app\models\Order $model */

$this->registerCss(<<<CSS
.order-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 16px;
    padding: 20px;
    width: 340px;
    max-width: 100%;
    transition: box-shadow 0.2s ease;
    margin-bottom: 20px;
}

.order-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.order-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 16px;
    color: #2c3e50;
}

.order-card-body p {
    margin: 6px 0;
    font-size: 14px;
    color: #555;
}

.order-card-footer {
    margin-top: 15px;
    text-align: right;
}

.order-status {
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    color: white;
    display: inline-block;
}

.status-new { background-color: #17a2b8; }
.status-in-progress { background-color: #ffc107; color: #212529; }
.status-completed { background-color: #28a745; }
.status-canceled { background-color: #dc3545; }
CSS
);
?>

<div class="order-card shadow-sm">
    <div class="order-card-header">
        <div>Заказ №<?= Html::encode($model->id) ?></div>
        <div>
            <?php
                $statusClassMap = [
                    '9' => 'Новый',
                    'Новый' => '9',
                    'В работе' => 'status-in-progress',
                    'Завершён' => 'status-completed',
                    'Отменён' => 'status-canceled',
                ];
                $statusTitle = $model->status->title ?? '—';
                $statusClass = $statusClassMap[$statusTitle] ?? 'status-new';
            ?>
            <span class="order-status <?= $statusClass ?>"><?= Html::encode($statusTitle) ?></span>
        </div>
    </div>
    <div class="order-card-body">
        <p><strong>Стол:</strong> <?= $model->table_id ? Html::encode($model->table_id) : '—' ?></p>
        <p><strong>Тип заказа:</strong> <?= $model->order_type == 10 ? 'На месте' : 'С собой' ?></p>
        <p><strong>Время создания:</strong> <?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
        <p><strong>Официант:</strong> <?= Html::encode($model->waiter->fio ?? '—') ?></p>
        <p><strong>Блюда:</strong></p>
        <ul>
            <?php foreach ($model->orderDishes as $dish): ?>
                <li><?= Html::encode($dish->dish->title ?? '(неизвестно)') ?> — <?= Html::encode($dish->count) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="order-card-footer">
        <?= Html::a('Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm ms-2']) ?>
    </div>
</div>
