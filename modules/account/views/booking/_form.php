<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var app\models\Booking $model */

YiiAsset::register($this);
$this->registerCss(<<<CSS
    body {
      background-color: #f7f7f7;
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    .booking-container {
      max-width: 1000px;
      margin: 20px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .booking-container h1 {
      text-align: center;
      color: #343a40;
      margin-bottom: 30px;
    }
    /* Стили для формы */
    .booking-form .form-group label {
      font-weight: 600;
    }
    .booking-form .form-control {
      border-radius: 5px;
      border: 1px solid #ced4da;
    }
    .booking-form .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    /* Стили для контейнера схемы зала */
    #hall-container {
      border: 1px solid #ddd;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 20px;
      background: #fafafa;
    }
    #hall-container svg {
      width: 100%;
      height: auto;
      display: block;
    }
    /* Кнопка отправки */
    .btn-outline-success {
      font-weight: 600;
      border-radius: 50px;
      padding: 12px 20px;
      border: 2px solid #28a745;
      transition: background-color 0.3s, color 0.3s;
    }
    .btn-outline-success:hover {
      background-color: #28a745;
      color: #fff;
    }
CSS
);
?>

<div class="booking-container">
    <h1>Бронирование</h1>
    <div class="booking-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fio_guest')->textInput(['maxlength' => true]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'booking_date')->textInput(['type' => 'date']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'booking_time_start', ['enableAjaxValidation' => true])->textInput(['type' => 'time']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'booking_time_end', ['enableAjaxValidation' => true])->textInput(['type' => 'time']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'count_guest')->input('number', ['min' => 1]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <?= $form->field($model, 'selected_tables')->hiddenInput(['id' => 'selected-tables'])->label(false) ?>

        <h3 class="mt-4">Схема зала</h3>
        <div id="hall-container" class="w-100">
            <?= file_get_contents(Yii::getAlias('img/test_tabels.svg')) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Забронировать', ['class' => 'btn btn-outline-success mt-2 w-100']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php
        if (Yii::$app->session->hasFlash('error')): ?>
            <?php
            $this->registerJs(<<<JS
                $(document).ready(function(){
                    validateAndFetchBookedTables();
                });
            JS);
            ?>
        <?php endif; ?>

        <?php
        if (!empty($model->selected_tables)) {
            $js = <<<JS
                let selectedTables = '{$model->selected_tables}'.split(',');
                selectedTables.forEach(function(id) {
                    $('#table' + id).addClass('selected');
                });
            JS;
            $this->registerJs($js);
        }
        ?>

        <?= $this->registerJsFile('/js/booking.js', ['depends' => YiiAsset::class]); ?>

    </div>
</div>


