<?php

use yii\bootstrap5\Html;
use yii\web\YiiAsset;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Booking $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="booking-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fio_guest')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'booking_date')->textInput(['type' => 'date']) ?>

    <?= $form->field($model, 'booking_time_start', ['enableAjaxValidation' => true])->textInput(['type' => 'time']) ?>

    <?= $form->field($model, 'booking_time_end', ['enableAjaxValidation' => true])->textInput(['type' => 'time']) ?>

    <?= $form->field($model, 'count_guest')->input('number', ['min' => 1]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'selected_tables')->hiddenInput(['id' => 'selected-tables'])->label(false) ?>

    <div id="hall-container">
        <?= file_get_contents(Yii::getAlias('@webroot/img/test_tabels.svg')) ?>
    </div>

    <?= $this->registerJsFile('/js/booking.js', ['depends' => YiiAsset::class]); ?>

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

    <div class="form-group">
        <?= Html::submitButton('Забронировать', ['class' => 'btn btn-outline-success mt-2']) ?>
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
</div>