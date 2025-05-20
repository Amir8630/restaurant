<?php
use yii\helpers\Html;
// Removed unused 'use yii\helpers\Url;'

/** @var app\models\Booking $model */

// $statusColors = [
//     'Ожидает' => 'warning',
//     'Подтверждена' => 'success',
//     'Отменена' => 'danger',
// ];

// $statusName = ucfirst(strtolower($model->status->title ?? 'Ожидает'));
// $color = $statusColors[$statusName] ?? 'secondary';
?>


<?php
$this->registerCss(<<<CSS
body {
    background-color: #f1f3f6;
}

.booking-card {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 16px;
    padding: 20px;
    width: 340px;
    max-width: 100%;
    transition: box-shadow 0.2s ease;
}

.booking-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 16px;
    color: #2c3e50;
}

.booking-card-body p {
    margin: 6px 0;
    font-size: 14px;
    color: #555;
}

.booking-card-footer {
    margin-top: 15px;
}
CSS);
?>

<div class="booking-card shadow-sm">
    <div class="booking-card-header">
        <div class="booking-id">Пользователь №<?= Html::encode($model->id) ?></div>
    </div>
    <div class="booking-card-body">
        <p><i class="bi bi-person-circle"></i> ФИО: <?= Html::encode($model->fio) ?></p>
        <p><i class="bi bi-envelope"></i> Email: <?= Html::encode($model->email) ?></p>
        <p><i class="bi bi-people-fill"></i> Пол: <?= Html::encode($model->gender) ?></p>
        <p><i class="bi bi-telephone"></i> Телефон: <?= Html::encode($model->phone) ?></p>
        <p><i class="bi bi-person-badge"></i> Роль: <?= Html::encode($model->getRoleTitle()) ?></p>
    </div>
    <div class="booking-card-footer d-flex justify-content-end gap-2">
        <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm', 'title' => 'Просмотр']) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i>', ['cancel', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger btn-sm btn-cancel-modal',
            'data-number' => $model->id,
            'title' => 'Удалить'
        ]) ?>
    </div>
</div>


<!-- старый вариант, сверху стили бета -->
<!-- <php

use app\models\Status;
use yii\bootstrap5\Html;

?>


<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title"> <= Html::encode('Бронь №' . $model->id) ?> </h5>
    <p class="card-text"> <= Html::encode('Бронь на ' . $model->booking_date) ?> </p>
    <p class="card-text"> <= Html::encode('Бронь с ' . $model->booking_time_start) ?> </p>
    <p class="card-text"> <= Html::encode('Бронь до ' . $model->booking_time_end) ?> </p>
    <p class="card-text"> <= Html::encode('Email ' . $model->email) ?> </p>
  </div>
  <= Html::a('Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-info w-100'])?>
  
  <= $model->status_id == Status::getStatusId('Забронировано') 
  ? Html::a('Отменить', ['cancel-modal', 'id' => $model->id], ['class' => 'btn btn-outline-warning mt-2 w-100 btn-cancel-modal', 'data-number' => $model->id]) 
  : ''?>

</div> -->

<!-- проблема в том что я убрал модели из передачи и теперь я прочто renfer делаю без передачи  -->