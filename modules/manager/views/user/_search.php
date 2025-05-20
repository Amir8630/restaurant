<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\manager\models\UserSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'id' => 'form_search',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'created_by_id') ?>

    <?= $form->field($model, 'fio')?>
    <?= $form->field($model, 'title_search')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'gender')->dropDownList(['Мужской' => 'Мужской', 'Женский' => 'Женский'],['prompt' => 'Выберите пол']) ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php  echo $form->field($model, 'role_id')->dropDownList(
        [
            '5' => 'официант',
            '4' => 'повар',
            '3' => 'менеджер',
        ],['prompt' => 'Выберите роль']) ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <div class="form-group">
        <!-- <= Html::submitButton('Поиск', ['class' => 'btn btn-outline-primary']) ?> -->
        <?= Html::a('Сбросить',['index'] , ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
