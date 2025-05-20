<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\widgets\MaskedInput;

?>

<div class="row">
    <div class="col-lg-5">

        <h3>Регистрация</h3>
        <!-- <h1>Регистрация</h1> -->


        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
        ]); ?>

        <?= $form->field($model, 'fio', [
            'template' => "{label}\n" .
                          "<div class=\"input-group mb-3\">\n" .
                          "  <span class=\"input-group-text\"><i class=\"bi bi-person-fill\"></i></span>\n" .
                          "  {input}\n" .
                          "</div>\n" .
                          "{error}",
        ])->textInput([
            'autofocus'   => true,
            'placeholder' => 'ФИО',
            'class'       => 'form-control',
        ]) ?>

        <?= $form->field($model, 'email', [
            'template' => "{label}\n" .
                          "<div class=\"input-group mb-3\">\n" .
                          "  <span class=\"input-group-text\"><i class=\"bi bi-envelope-fill\"></i></span>\n" .
                          "  {input}\n" .
                          "</div>\n" .
                          "{error}",'enableAjaxValidation' => true
        ])->textInput([
            'type'        => 'email',
            'placeholder' => 'Email',
            'class'       => 'form-control',
        ]) ?>

        <?= $form->field($model, 'gender', [
            'template' => "{label}\n" .
                          "<div class=\"input-group mb-3\">\n" .
                          "  <span class=\"input-group-text\"><i class=\"bi bi-gender-ambiguous\"></i></span>\n" .
                          "  {input}\n" .
                          "</div>\n" .
                          "{error}",
        ])->dropDownList(
            ['Мужской' => 'Мужской', 'Женский' => 'Женский'],
            [
                'prompt' => 'Выберите пол',
                'class'  => 'form-select',
            ]
        ) ?>

        <?= $form->field($model, 'phone', [
            'template' => "{label}\n" .
                          "<div class=\"input-group mb-3\">\n" .
                          "  <span class=\"input-group-text\"><i class=\"bi bi-telephone-fill\"></i></span>\n" .
                          "  {input}\n" .
                          "</div>\n" .
                          "{error}",
        ])->widget(MaskedInput::class, [
            'mask' => '+7 (999)-999-99-99',
            'options' => [
                'placeholder' => 'Телефон',
                'class'       => 'form-control',
            ],
        ]) ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n" .
                          "<div class=\"input-group mb-3\">\n" .
                          "  <span class=\"input-group-text\"><i class=\"bi bi-lock-fill\"></i></span>\n" .
                          "  {input}\n" .
                          "  <button class=\"btn btn-outline-secondary\" type=\"button\" id=\"togglePassword\">\n" .
                          "    <i class=\"bi bi-eye-slash\" id=\"togglePasswordIcon\"></i>\n" .
                          "  </button>\n" .
                          "</div>\n" .
                          "{error}",
        ])->passwordInput([
            'placeholder' => 'Пароль',
            'id'          => 'register-password',
            'class'       => 'form-control',
        ]) ?>

        <?= $form->field($model, 'rules')->checkbox([
            'template' => "<div class=\"form-check mb-3\">\n" .
                          "  {input}\n" .
                          "  {label}\n" .
                          "</div>\n" .
                          "{error}",
            'labelOptions' => ['class' => 'form-check-label'],
            'inputOptions' => ['class' => 'form-check-input'],
        ]) ?>

        <div class="form-group d-flex gap-2">
            <?= Html::a('Назад', ['site/index'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-outline-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
// JS для переключения видимости пароля
$js = <<<JS
$('#togglePassword').on('click', function() {
    const input = $('#register-password');
    const icon  = $('#togglePasswordIcon');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    } else {
        input.attr('type', 'password');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    }
});
JS;
$this->registerJs($js);
?>
