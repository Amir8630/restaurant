<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\account\models\BookingSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="booking-search booking-search-form">
    <style>
        .booking-search-form .row > div {
            margin-bottom: 10px; /* уменьшить вертикальные отступы между полями */
        }
        .booking-search-form .form-group {
            margin-top: 0; /* убрать лишний отступ сверху у кнопок */
        }
    </style>
    <?php $form = ActiveForm::begin([
        'id' => 'form_search',
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => 1],
    ]); ?>

    <div class="d-flex flex-wrap justify-content-between">
        <div class="col-md-2">
            <?= $form->field($model, 'id')->textInput(['placeholder' => 'ID']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fio_guest')->textInput(['placeholder' => 'ФИО гостя']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'user_id')->textInput(['placeholder' => 'ID пользователя']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'booking_date')->textInput([
                'type' => 'date',
                ]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'booking_time_start')->textInput(['type' => 'time']) ?>
        </div>
        <?= $form->field($model, 'title_search')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'id_search')->hiddenInput()->label(false) ?>
    </div>

    <div class="form-group">
        <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
