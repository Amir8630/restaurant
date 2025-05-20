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
.booking-container {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 2rem;
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
}

/* Заголовок */
.booking-container h1 {
  text-align: center;
  color: var(--navbar-bg);
  margin-bottom: 2rem;
}

/* ===== Стили формы ===== */
.booking-form .form-group label,
.booking-form .form-label {
  font-weight: 600;
  color: var(--navbar-bg);
}

.booking-form .form-control {
  border: 1px solid var(--navbar-bg);
  border-radius: .5rem;
  transition: border-color .3s, box-shadow .3s;
}

.booking-form .form-control:focus {
  border-color: var(--navbar-link-active);
  box-shadow: 0 0 0 .2rem rgba(32, 201, 151, .25);
}

/* ===== Дата и время ===== */
.booking-form input[type="date"],
.booking-form input[type="time"] {
  background: #fff;
}

/* ===== Кнопка отправки ===== */
.btn-outline-success {
  display: block;
  width: 100%;
  font-weight: 600;
  border: 2px solid var(--navbar-link-active);
  border-radius: 50px;
  padding: .75rem;
  color: var(--navbar-link-active);
  transition: background-color .4s, color .4s;
}

.btn-outline-success:hover {
  background-color: var(--navbar-link-active);
  color: #fff;
}

/* ===== Схема зала ===== */
#hall-container {
  border: 1px solid var(--navbar-bg);
  border-radius: .75rem;
  background: #fafafa;
  margin-bottom: 2rem;
}

/* ===== Всплывающая подсказка ===== */
.tooltip-custom {
  position: relative;
  display: inline-flex;
  align-items: center;
  cursor: default;
}

.tooltip-custom .tooltip-text {
  visibility: hidden;
  width: 260px;
  background-color: var(--navbar-bg);
  color: var(--navbar-link);
  text-align: left;
  border-radius: .5rem;
  padding: .75rem 1rem;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translate(-50%, .5rem);
  opacity: 0;
  transition: opacity .3s, visibility .3s;
  font-size: .875rem;
  z-index: 10;
}

.tooltip-custom:hover .tooltip-text {
  visibility: visible;
  opacity: 1;
}

.btn-book {
  padding: 0.5rem 1.5rem;       /* уменьшили вертикальные отступы */
  font-size: 1rem;             /* чуть меньше шрифт */
  border-width: 2px;
  border-radius: 2rem;         /* более обтекаемый вид */
  display: inline-block;       /* ширина по содержимому */
}
.btn-book:hover {
  background-color: var(--navbar-link-active);
  color: #fff;
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
<?= Html::submitButton('Забронировать', [
    'class' => 'btn btn-outline-success btn-book mt-3'
]) ?>
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
