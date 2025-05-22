<?php
use yii\helpers\Html;

/** @var app\models\Order $model */

$this->registerCss(<<<CSS
.order-card {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 16px;
    padding: 20px;
    width: 340px;
    max-width: 100%;
    transition: box-shadow 0.2s ease;
    margin-bottom: 20px;
}

.order-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
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

.order-status-badge {
    font-weight: 500;
    font-size: 13px;
    border-radius: 12px;
    padding: 4px 10px;
    display: inline-block;
    color: #fff;
    background-color: #6c757d;
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
        <?php
        $statusClassMap = [
            9 => 'status-new',
            5 => 'status-in-progress',
            6 => 'status-in-progress',
            3 => 'status-completed',
            4 => 'status-canceled',
        ];
        $statusId = $model->order_status;
        $statusTitle = $model->status->title ?? '—';
        $statusClass = $statusClassMap[$statusId] ?? '';
        ?>
        <div>
            <span class="order-status-badge <?= $statusClass ?>"><?= Html::encode($statusTitle) ?></span>
        </div>
    </div>
    <div class="order-card-body">
        <p><i class="bi bi-grid-3x3-gap"></i> Стол: <?= $model->table_id ? Html::encode($model->table_id) : '—' ?></p>
        <p><i class="bi bi-box"></i> Тип: <?= $model->order_type == 10 ? 'На месте' : 'С собой' ?></p>
        <p><i class="bi bi-clock"></i> Время создания: <?= Yii::$app->formatter->asTime($model->created_at) ?></p>
        <p><strong>Блюда:</strong></p>
        <ul style="padding-left: 20px; margin: 0;">
        <?php foreach ($model->orderDishes as $dish): ?>
            <li>
                <?= Html::encode($dish->dish->title ?? '(неизвестно)') ?> — 
                <?= Html::encode($dish->count) ?> шт.
                <span class="order-status-badge <?= $statusClass ?>"><?= Html::encode($dish->status->title ?? '—') ?></span>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
        <div class="btn-group mb-2 mt-1">
            <?= Html::a(
            '<i class="bi bi-eye"></i> Просмотр',
            ['view', 'id' => $model->id],
            [
                'class' => 'btn btn-outline-primary btn-sm'
            ]
            ) ?>
            <?= Html::a('<i class="bi bi-pencil"></i> Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-success btn-sm']) ?>
            <?= Html::a(
            '<i class="bi bi-trash"></i> Удалить',
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-outline-danger btn-sm',
                'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                'method'  => 'post',
                ],
            ]
            ) ?>
        </div>
</div>
