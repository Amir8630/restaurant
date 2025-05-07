<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\YiiAsset;
use yii\widgets\Pjax;

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

        <!-- <php Pjax::begin([
            'id' => 'pjax-booking',
            'timeout' => 10000,
            'enablePushState' => false,
        ]); ?> -->
        <!-- <php $form = Pjax::begin(['id' => 'pjax-c', 'enablePushState' => false, 'timeout' => 5000]); ?> -->

        <?php $form = ActiveForm::begin([
            'id'                   => 'form-create',
            'enableAjaxValidation' => true,
            'validateOnSubmit'     => false, // вот эта строчка отменит AJAX‑валидацию при отправке
            'validateOnChange'     => true, // оставим проверку при изменении
            'validateOnBlur'       => true,
            'validateOnType'       => false,
            'options'              => [
                'data-pjax' => true,
                'class'     => 'booking-form',
            ],
        ]); ?>

        <?= $form->field($model, 'fio_guest')->textInput(['maxlength' => true]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'booking_date', ['enableAjaxValidation' => true])->textInput(['type' => 'date', 'min' => date('Y-m-d')]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'booking_time_start', ['enableAjaxValidation' => true])->textInput(['type' => 'time', 'min' => '07:00', 'max' => '22:00']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'booking_time_end', ['enableAjaxValidation' => true])->textInput(['type' => 'time', 'max' => '23:00']) ?>
            </div>
        </div>
        <div class="alert alert-primary" role="alert">
  A simple primary alert—check it out!
</div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'count_guest', ['enableAjaxValidation' => true])->input('number', ['min' => 1]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'phone', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <?= $form->field($model, 'selected_tables', ['enableAjaxValidation' => true])->hiddenInput()->label(false) ?>


        <h3 class="mt-4">Схема зала</h3>
        <div id="hall-container" class="w-100">
            <?= file_get_contents(Yii::getAlias('img/Frame12.svg')) ?>
        </div>

        <div class="form-group">
            <!-- , 'data-pjax' => 0 -->
            <?= Html::submitButton('Забронировать', ['class' => 'btn btn-outline-success mt-2 w-100']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <!-- <php Pjax::end(); ?> -->

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


