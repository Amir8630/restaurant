<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\widgets\MaskedInput;

?>

<div class="row">
            <div class="col-lg-5">

            <h3>Регистрация</h3>
                <?php $form = ActiveForm::begin(['id' => 'register-form']); ?>

                    <?= $form->field($model, 'fio')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'email') ?>
                    <?= $form->field($model, 'gender')->dropDownList(['Мужской' => 'Мужской', 'Женский' => 'Женский']) ?>
                    <?= $form->field($model, 'phone')->widget(MaskedInput::class, ['mask' => '+7 (999)-999-99-99']) ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'rules')->checkbox() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-outline-primary', 'name' => 'register-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>