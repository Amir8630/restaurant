<?php

use app\models\Status;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var yii\web\View                            $this */
/* @var app\modules\account\models\BookingSearch $model */

/* Регистрируем компактные стили и анимацию */
$this->registerCss(<<<CSS

.booking-search-form {
    display: flex;
    justify-content: center;    /* центрируем контейнер */
    margin-bottom: 1rem;
}

.booking-search-form .card {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 0.25rem 0.5rem;
    overflow: hidden;
    width: auto;                /* авто-ширина по содержимому */
    max-width: 970px;           /* не больше этого */
}

.booking-search-form .card-header {
    background: transparent;
    border-bottom: none;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    text-align: center;
}

.booking-search-form .card-body {
    display: flex;
    align-self: flex-start; 
    gap: 0.5rem;
    align-items: center;
    flex-wrap: nowrap;
    padding: 0;
}

.booking-search-form .form-group {
    /* margin-bottom: 0 !important; */
    margin-bottom: 0.5rem !important;
}

.booking-search-form .btn-reset {
    align-self: flex-start;
}
.booking-search-form .card-body {
    display: flex;
    flex-wrap: wrap;              /* разрешаем перенос строк */
    gap: 0.5rem;
    align-items: flex-start;
    padding: 0;
}

/* Чтобы поля занимали всю ширину на телефоне */
.booking-search-form .form-group {
    flex: 1 1 150px;               /* минимальная ширина 150px, адаптируется */
    min-width: 120px;
    margin-bottom: 0.5rem !important;
}

/* Мобильная адаптация */
@media (max-width: 576px) {
    .booking-search-form .form-group {
        flex: 1 1 100%;
    }

    .booking-search-form .btn-reset {
        width: 100%;
    }
}

CSS
);
?>

<div class="booking-search booking-search-form">
  <?php $form = ActiveForm::begin([
      'id'      => 'form_search',
      'action'  => ['index'],
      'method'  => 'get',
      'options' => ['data-pjax' => 1],
            'fieldConfig' => [
            'options'   => ['class' => 'form-group'], 
            'template'  => "{input}\n{error}",
            'labelOptions' => ['class' => 'd-none'],
        ],
  ]); ?>

    <div class="card">
      <div class="card-header">
        <strong>Фильтры</strong>
      </div>

      <div class="d-flex g-2 card-body">

     
          <?= $form->field($model, 'id')->textInput(['placeholder' => 'Бронь №'])->label(false) ?>


        <?= $form->field($model, 'fio_guest')->textInput(['placeholder' => 'На имя'])->label(false) ?>
        <?= $form->field($model, 'status_id')->dropDownList(
            [
                '1' => 'Забронировано',
                '4' => 'Отменён',
            ],['prompt' => 'Статус'])->label(false) ?>

        <?= $form->field($model, 'booking_date')->input('date')->label(false) ?>

        <?= $form->field($model, 'booking_time_start')->input('time')->label(false) ?>

        <?= Html::a('Сброс', ['index'], ['class' => 'btn btn-outline-light btn-reset']) ?>

        <?= $form->field($model, 'title_search')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'id_search')->hiddenInput()->label(false) ?>

      </div>
    </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<JS
$('#form_search').on('change', 'input, select', function(){
    $(this).closest('form').yiiActiveForm('submitForm');
});
JS
);