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
        <!-- <php $form = Pjax::begin(['id' => 'pjax-c', 'enablePushState' => false, 'timeout' => 5000]); ?> -->
        <?php $form = ActiveForm::begin(['id' => 'form-create', 'options' => ['data-pjax' => true]]); ?>

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



<!-- <svg width="90" height="72" viewBox="0 0 90 72" fill="none" xmlns="http://www.w3.org/2000/svg" class="new-svg-table"> -->
<!-- стулья -->
<!-- <rect x="17.5" y="1.5" width="25" height="4" rx="2" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
<path d="M24.4447 5.5H35.5553C37.2877 5.5 38.7595 6.76749 39.0165 8.48081L40.8165 20.4808C41.1339 22.5969 39.495 24.5 37.3553 24.5H22.6447C20.505 24.5 18.8661 22.5969 19.1835 20.4808L20.9835 8.48081C21.2405 6.76749 22.7123 5.5 24.4447 5.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
<rect x="48.5" y="1.5" width="25" height="4" rx="2" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
<path d="M55.4447 5.5H66.5553C68.2877 5.5 69.7595 6.76749 70.0165 8.48081L71.8165 20.4808C72.1339 22.5969 70.495 24.5 68.3553 24.5H53.6447C51.505 24.5 49.8661 22.5969 50.1835 20.4808L51.9835 8.48081C52.2405 6.76749 53.7123 5.5 55.4447 5.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
<rect x="73.5" y="70.5" width="25" height="4" rx="2" transform="rotate(-180 73.5 70.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
<path d="M66.5553 66.5L55.4447 66.5C53.7123 66.5 52.2405 65.2325 51.9835 63.5192L50.1835 51.5192C49.8661 49.4031 51.505 47.5 53.6448 47.5L68.3553 47.5C70.495 47.5 72.1339 49.4031 71.8165 51.5192L70.0165 63.5192C69.7595 65.2325 68.2877 66.5 66.5553 66.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
<rect x="42.5" y="70.5" width="25" height="4" rx="2" transform="rotate(-180 42.5 70.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
<path d="M35.5553 66.5L24.4447 66.5C22.7123 66.5 21.2405 65.2325 20.9835 63.5192L19.1835 51.5192C18.8661 49.4031 20.505 47.5 22.6448 47.5L37.3553 47.5C39.495 47.5 41.1339 49.4031 40.8165 51.5192L39.0165 63.5192C38.7595 65.2325 37.2877 66.5 35.5553 66.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
<rect x="2.5" y="48.5" width="25" height="4" rx="2" transform="rotate(-90 2.5 48.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
<path d="M6.5 41.5553L6.5 30.4447C6.5 28.7123 7.76749 27.2405 9.48081 26.9835L21.4808 25.1835C23.5969 24.8661 25.5 26.505 25.5 28.6447L25.5 43.3552C25.5 45.495 23.5969 47.1339 21.4808 46.8165L9.48081 45.0165C7.76749 44.7595 6.5 43.2877 6.5 41.5553Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path> -->
<!-- строчка снизу не понятно что делает, вроде тень для стульев -->
<!-- <rect x="11" y="62" width="52" height="69" rx="4" transform="rotate(-90 11 62)" fill="#29C770" class="v-center v-fill desk-one__svg-table"></rect> -->
 
<!-- нужна ли тут маска и для чего? -->
    <!-- <mask class="v-mask" id="c_mask_1702480619226" maskUnits="userSpaceOnUse" x="0" y="0" width="90" height="71.9792" style="mask-type: alpha;">
        <rect x="17.5" y="1.5" width="25" height="4" rx="2" fill="#29C770" fill-opacity="0.6" stroke="#29C770"></rect>
        <path d="M24.4447 5.5H35.5553C37.2877 5.5 38.7595 6.76749 39.0165 8.48081L40.8165 20.4808C41.1339 22.5969 39.495 24.5 37.3553 24.5H22.6447C20.505 24.5 18.8661 22.5969 19.1835 20.4808L20.9835 8.48081C21.2405 6.76749 22.7123 5.5 24.4447 5.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770"></path>
        <rect x="48.5" y="1.5" width="25" height="4" rx="2" fill="#29C770" fill-opacity="0.6" stroke="#29C770"></rect>
        <path d="M55.4447 5.5H66.5553C68.2877 5.5 69.7595 6.76749 70.0165 8.48081L71.8165 20.4808C72.1339 22.5969 70.495 24.5 68.3553 24.5H53.6447C51.505 24.5 49.8661 22.5969 50.1835 20.4808L51.9835 8.48081C52.2405 6.76749 53.7123 5.5 55.4447 5.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770"></path>
        <rect x="73.5" y="70.5" width="25" height="4" rx="2" transform="rotate(-180 73.5 70.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770"></rect>
        <path d="M66.5553 66.5L55.4447 66.5C53.7123 66.5 52.2405 65.2325 51.9835 63.5192L50.1835 51.5192C49.8661 49.4031 51.505 47.5 53.6448 47.5L68.3553 47.5C70.495 47.5 72.1339 49.4031 71.8165 51.5192L70.0165 63.5192C69.7595 65.2325 68.2877 66.5 66.5553 66.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770"></path>
        <rect x="42.5" y="70.5" width="25" height="4" rx="2" transform="rotate(-180 42.5 70.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770"></rect>
        <path d="M35.5553 66.5L24.4447 66.5C22.7123 66.5 21.2405 65.2325 20.9835 63.5192L19.1835 51.5192C18.8661 49.4031 20.505 47.5 22.6448 47.5L37.3553 47.5C39.495 47.5 41.1339 49.4031 40.8165 51.5192L39.0165 63.5192C38.7595 65.2325 37.2877 66.5 35.5553 66.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770"></path>
        <rect x="2.5" y="48.5" width="25" height="4" rx="2" transform="rotate(-90 2.5 48.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770"></rect>
        <path d="M6.5 41.5553L6.5 30.4447C6.5 28.7123 7.76749 27.2405 9.48081 26.9835L21.4808 25.1835C23.5969 24.8661 25.5 26.505 25.5 28.6447L25.5 43.3552C25.5 45.495 23.5969 47.1339 21.4808 46.8165L9.48081 45.0165C7.76749 44.7595 6.5 43.2877 6.5 41.5553Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770"></path>
        <rect x="11" y="62" width="52" height="69" rx="4" transform="rotate(-90 11 62)" fill="#29C770"></rect>
    </mask> -->

    <!-- строчка с низу не понятно что делает, вроде тень для стола -->
    <!-- <g class="v-g-mask" mask="url(#c_mask_1702480619226)"> -->
        <!--  <rect x="-28" y="-44" width="90" height="100" transform="rotate(-35)" fill="black" fill-opacity="0.5"></rect> -->
    <!-- </g> -->

<!-- стол -->
    <!-- <rect x="10" y="61" width="54" height="71" rx="4" transform="rotate(-90 11 62)" fill="#29C770" class="desk-one__svg-stroke"></rect> -->
            
<!-- </svg> -->



<!-- <svg width="90" height="72" viewBox="0 0 90 72" fill="none" xmlns="http://www.w3.org/2000/svg" class="new-svg-table"> -->
    <!-- стулья -->
    <!-- <rect x="17.5" y="1.5" width="25" height="4" rx="2" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
    <path d="M24.4447 5.5H35.5553C37.2877 5.5 38.7595 6.76749 39.0165 8.48081L40.8165 20.4808C41.1339 22.5969 39.495 24.5 37.3553 24.5H22.6447C20.505 24.5 18.8661 22.5969 19.1835 20.4808L20.9835 8.48081C21.2405 6.76749 22.7123 5.5 24.4447 5.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
    <rect x="48.5" y="1.5" width="25" height="4" rx="2" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
    <path d="M55.4447 5.5H66.5553C68.2877 5.5 69.7595 6.76749 70.0165 8.48081L71.8165 20.4808C72.1339 22.5969 70.495 24.5 68.3553 24.5H53.6447C51.505 24.5 49.8661 22.5969 50.1835 20.4808L51.9835 8.48081C52.2405 6.76749 53.7123 5.5 55.4447 5.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
    <rect x="73.5" y="70.5" width="25" height="4" rx="2" transform="rotate(-180 73.5 70.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
    <path d="M66.5553 66.5L55.4447 66.5C53.7123 66.5 52.2405 65.2325 51.9835 63.5192L50.1835 51.5192C49.8661 49.4031 51.505 47.5 53.6448 47.5L68.3553 47.5C70.495 47.5 72.1339 49.4031 71.8165 51.5192L70.0165 63.5192C69.7595 65.2325 68.2877 66.5 66.5553 66.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
    <rect x="42.5" y="70.5" width="25" height="4" rx="2" transform="rotate(-180 42.5 70.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
    <path d="M35.5553 66.5L24.4447 66.5C22.7123 66.5 21.2405 65.2325 20.9835 63.5192L19.1835 51.5192C18.8661 49.4031 20.505 47.5 22.6448 47.5L37.3553 47.5C39.495 47.5 41.1339 49.4031 40.8165 51.5192L39.0165 63.5192C38.7595 65.2325 37.2877 66.5 35.5553 66.5Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
    <rect x="2.5" y="48.5" width="25" height="4" rx="2" transform="rotate(-90 2.5 48.5)" fill="#29C770" fill-opacity="0.6" stroke="#29C770" class="v-fill v-stroke"></rect>
    <path d="M6.5 41.5553L6.5 30.4447C6.5 28.7123 7.76749 27.2405 9.48081 26.9835L21.4808 25.1835C23.5969 24.8661 25.5 26.505 25.5 28.6447L25.5 43.3552C25.5 45.495 23.5969 47.1339 21.4808 46.8165L9.48081 45.0165C7.76749 44.7595 6.5 43.2877 6.5 41.5553Z" fill="#29C770" fill-opacity="0.4" stroke="#29C770" class="v-fill-d v-stroke"></path>
     -->
    <!-- стол -->
    <!-- <rect x="10" y="61" width="54" height="71" rx="4" transform="rotate(-90 11 62)" fill="#29C770" class="desk-one__svg-stroke"></rect>
</svg> -->
