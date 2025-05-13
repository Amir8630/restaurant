<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Booking $model */

YiiAsset::register($this);
$this->registerCss(<<<CSS
    body {
      background-color: #f7f7f7;
          background-color:rgb(130, 133, 136);

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
    
    /* Стили для всплывающей подсказки */
    .tooltip-custom {
      position: relative;
      display: inline-block;
      cursor: pointer;
    }

    .tooltip-custom i {
      font-size: 14px;
    }

    .tooltip-custom .tooltip-text {
      visibility: hidden;
      width: 260px;
    background-color: #343a40;
    color: #ffffff;
    text-align: left;
    border-radius: 10px;
    padding: 12px 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    position: absolute;
    z-index: 10;
    top: 120%;
    left: 120%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.4s, visibility 0.4s;
    font-size: 14px;
    }

    .tooltip-custom:hover .tooltip-text {
    visibility: visible;
    background-color: #343a40;
    color: #ffffff;
    opacity: 1;
    }
    
    .tooltip-custom {
        margin: 0; /* Убираем внешние отступы */
        padding: 0; /* Убираем внутренние отступы */
        display: inline-flex;
        align-items: flex-start; /* Выравниваем содержимое по верхнему краю */
    }
CSS
);
?>

<div class="booking-container">

    <?= Html::a(
        Html::img('@web/img/arrow-left.svg', [
            'alt' => 'Назад',
            'style' => 'width: 20px; height: 20px; margin-right: 8px;'
        ]),
        ['/account/booking'],
        ['class' => 'btn btn-link d-inline-flex align-items-center']
    ) ?>

    <h1>Бронирование</h1>
    <div class="booking-form">

        <!-- <php Pjax::begin([
            'id' => 'pjax-booking',
            'enablePushState' => false,
            'timeout' => 5000,
            // 'clientOptions' => ['method' => 'POST']
        ]); ?> -->
        <!-- <php $form = Pjax::begin(['id' => 'pjax-c', 'enablePushState' => false, 'timeout' => 5000]); ?> -->

        <?php $form = ActiveForm::begin([
            'id'                   => 'form-create',
            'enableAjaxValidation' => true,
            'validateOnSubmit'     => true, // вот эта строчка отменит AJAX‑валидацию при отправке
            'validateOnChange'     => true, // оставим проверку при изменении
            'validateOnBlur'       => true,
            'validateOnType'       => false,
            'options'              => [
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
                <?php  
                $label = '<span class="tooltip-custom" style="display: inline-flex; align-items: center;">    
                    Окончание
                    <img src="/web/img/circle-question.svg" alt="tooltip-icon" style="width: 16px; height: 16px; vertical-align: middle; margin-left: 8px;">
                    <span class="tooltip-text">Интервал по умолчанию — 2 часа от начала. Можно изменить вручную.</span>
                </span>';
                ?>
                <?= $form->field($model, 'booking_time_end', ['enableAjaxValidation' => true])->textInput(['type' => 'time', 'max' => '23:00'])->label($label) ?>
            </div>
            
        </div>

        <div class="alert alert-primary d-none text-center" role="alert">
            <strong>Обратите внимание:</strong> Мы работаем ежедневно с <strong>7:00</strong> до <strong>23:00</strong>.
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'count_guest', ['enableAjaxValidation' => true])->input('number', ['min' => 1]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'phone', ['enableAjaxValidation' => true])->textInput(['maxlength' => true])->widget(\yii\widgets\MaskedInput::class, ['mask' => '+7 (999)-999-9999']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <?= $form->field($model, 'selected_tables', ['enableAjaxValidation' => true])->hiddenInput()->label(false) ?>


        <h3 class="mt-4">Схема зала</h3>
        <div id="hall-container" class="w-100">
            <?= file_get_contents(Yii::getAlias('img/tables.svg')) ?>
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

        <?= $this->registerJsFile('/js/booking.js', ['depends' => JqueryAsset::class]); ?>

    </div>
</div>

<?php
$this->registerJs(<<<JS
    $('.tooltip-custom').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
JS
);
?>
