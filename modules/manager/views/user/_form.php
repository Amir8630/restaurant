<?php

use app\models\Role;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\widgets\MaskedInput;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fio', [
        'template' => "{label}\n<div class=\"input-group mb-3\">\n" .
                      "<span class=\"input-group-text\"><i class=\"bi bi-person-fill\"></i></span>\n" .
                      "{input}\n</div>\n{error}",
    ])->textInput(['maxlength' => true, 'placeholder' => 'ФИО']) ?>

    <?= $form->field($model, 'email', [
        'template' => "{label}\n<div class=\"input-group mb-3\">\n" .
                      "<span class=\"input-group-text\"><i class=\"bi bi-envelope-fill\"></i></span>\n" .
                      "{input}\n</div>\n{error}",
    ])->textInput(['maxlength' => true, 'type' => 'email', 'placeholder' => 'Email']) ?>

    <?= $form->field($model, 'gender', [
        'template' => "{label}\n<div class=\"input-group mb-3\">\n" .
                    "<span class=\"input-group-text\"><i class=\"bi bi-gender-ambiguous\"></i></span>\n" .
                    "{input}\n</div>\n{error}",
    ])->dropDownList(
        [
            'Мужской' => 'Мужской',
            'Женский' => 'Женский',
        ],
        [
            'prompt' => 'Выберите пол',
            'class'  => 'form-select'
        ]
    ) ?>

    <?= $form->field($model, 'phone', [
        'template' => "{label}\n<div class=\"input-group mb-3\">\n" .
                      "<span class=\"input-group-text\"><i class=\"bi bi-telephone-fill\"></i></span>\n" .
                      "{input}\n</div>\n{error}",
    ])->textInput(['maxlength' => true, 'placeholder' => 'Phone'])->widget(MaskedInput::class, ['mask' => '+7 (999)-999-99-99']) ?>

<?= $form->field($model, 'password', [
    'template' => "{label}\n" .
                  "<div class=\"input-group mb-3\">\n" .
                  "  <span class=\"input-group-text\"><i class=\"bi bi-lock-fill\"></i></span>\n" .
                  "  {input}\n" .
                  "  <button class=\"btn btn-outline-secondary\" type=\"button\" id=\"togglePassword\">\n" .
                  // по умолчанию — перечёркнутый глаз
                  "    <i class=\"bi bi-eye-slash\" id=\"togglePasswordIcon\"></i>\n" .
                  "  </button>\n" .
                  "</div>\n" .
                  "{error}",
])
->passwordInput([
    'maxlength'   => true,
    'placeholder' => 'Password',
    'id'          => 'user-password',
    'class'       => 'form-control',
]) ?>



   <?= $form->field($model, 'role_id', [
    'template' => "{label}\n<div class=\"input-group mb-3\">\n" .
                  "<span class=\"input-group-text\"><i class=\"bi bi-shield-lock-fill\"></i></span>\n" .
                  "{input}\n</div>\n{error}",
    ])->dropDownList(
        Yii::$app->user->identity->role_id == 2
            ? [
            '5' => 'Официант',
            '4' => 'Повар',
            '3' => 'Менеджер',
            '2' => 'Администратор',
            ]
            : [
            '5' => 'Официант',
            '4' => 'Повар',
            '3' => 'Менеджер',
            ],
        [
            'prompt' => 'Выберите роль',
            'class'  => 'form-select'
        ]
    ) ?>

    <?= $form->field($model, 'created_by_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
    <div class="form-group">
        <?= Html::a('Назад',['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Зарегистрировать' : 'Сохранить', ['class' => 'btn btn-outline-primary2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$('#togglePassword').on('click', function() {
    const input = $('#user-password');
    const icon  = $('#togglePasswordIcon');
    const type  = input.attr('type') === 'password' ? 'text' : 'password';
    input.attr('type', type);
    // переключаем иконку
    icon.toggleClass('bi-eye bi-eye-slash');
});
JS;
$this->registerJs($js);
?>
