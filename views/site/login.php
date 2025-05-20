<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Авторизация';
?>
<div class="site-login">
    <!-- <h1><= Html::encode($this->title) ?></h1> -->
    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    // общий шаблон для полей:
                    'template' => "{label}\n" .
                                  "<div class=\"input-group mb-3\">\n" .
                                  "  <span class=\"input-group-text\">{icon}</span>\n" .
                                  "  {input}\n" .
                                  "</div>\n" .
                                  "{error}",
                    'labelOptions' => ['class' => 'form-label'], // или любая нужная вам обёртка
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'email', [
                'parts' => [
                    '{icon}' => '<i class="bi bi-envelope-fill"></i>',
                ],
            ])->textInput([
                'autofocus'   => true,
                'placeholder' => 'Email',
                'class'       => 'form-control',
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

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"form-check mb-3\">{input} {label}</div>\n{error}",
                'labelOptions' => ['class' => 'form-check-label'],
                'inputOptions' => ['class' => 'form-check-input'],
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Вход', ['class' => 'btn btn-outline-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div style="color:#999;">
                В системе доступны тестовые аккаунты:
                <br> <strong>Пользователь</strong> user@u.ru / User123
                <br> <strong>Повар</strong> cook@u.ru / Cook123
                <br> <strong>Официант</strong> waiter@u.ru / Waiter123
                <br> <strong>Менеджер</strong> manager@u.ru / Manager123
                <br> <strong>Администратор</strong> admin@u.ru / Admin123
            </div>

        </div>
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