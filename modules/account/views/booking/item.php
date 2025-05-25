<?php

use app\models\Status;
use yii\helpers\Html;

/** @var app\models\Booking $model */

?>

<?php
$this->registerCss(<<<'CSS'
body {
    background-color: #1e2227;
}

.booking-card {
    background-color: #2d363f;
    border: 1px solid #3e4a55;
    border-radius: 16px;
    padding: 20px;
    width: 320px;
    max-width: 100%;
    transition: box-shadow 0.2s ease;
    color: #f8f9fa;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.booking-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 16px;
    color: #ffffff;
}

/* Простой цветной текст статуса без обводки */
.status-new         { color: #9b59b6; }
.status-in-progress { color: #fffa65; }
.status-completed   { color: #7bed9f; }
.status-canceled    { color: #ff6b81; }

.booking-card-body p {
    margin: 6px 0;
    font-size: 14px;
    color: #d3d3d3;
}

.booking-card-footer {
    margin-top: 15px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.btn-sm { font-size: .85rem; padding: .4rem .8rem; }
.btn-outline-light { border-color: #70a1ff; color: #70a1ff; }
.btn-outline-light:hover { background: #70a1ff; color: #2d363f; }
.btn-outline-danger { border-color: #ff6b81; color: #ff6b81; }
CSS
);
?>

<div class="booking-card">
    <div class="booking-card-header">
        <div class="booking-id">Бронь №<?= Html::encode($model->id) ?></div>
        <?php
            // отображаем простой цветной текст статуса
            $map = [

                Status::getStatusId('Отменён')         => 'status-canceled',

                Status::getStatusId('Забронировано')    => 'status-completed',
            ];
            $cls = $map[$model->status_id] ?? 'status-new';
        ?>
        <span class="<?= $cls ?>"><?= Html::encode($model->status->title) ?></span>
    </div>
    <div class="booking-card-body">
        <p><i class="bi bi-person-circle"></i> На имя: <?= Html::encode($model->fio_guest) ?></p>
        <p><i class="bi bi-calendar-check"></i> Дата: <?= Yii::$app->formatter->asDate($model->booking_date) ?></p>
        <p><i class="bi bi-clock"></i> Время: <?= Html::encode($model->booking_time_start) ?> - <?= Html::encode($model->booking_time_end) ?></p>
        <p><i class="bi bi-people-fill"></i> Персон: <?= Html::encode($model->count_guest) ?></p>
        <p><i class="bi bi-telephone"></i> <?= Html::encode($model->phone) ?></p>
        <p><i class="bi bi-envelope"></i> <?= Html::encode($model->email) ?></p>
        <p><i class="bi bi-calendar-plus"></i> Создана: <?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
    </div>
    <div class="booking-card-footer">
        <div class="btn-group">
            <?= Html::a('<i class="bi bi-eye"></i> Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
            <?= $model->status_id == Status::getStatusId('Забронировано') 
                ? Html::a('<i class="bi bi-x-circle"></i> Отменить', ['cancel', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger btn-sm btn-cancel-modal',
                    'data-number' => $model->id,
                    'title' => 'Отменить'
                ])
                : '' ?>
        </div>
    </div>
</div>
