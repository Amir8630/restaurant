<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\manager\models\UserSearch $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss(<<<CSS
.user-search-form {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

.user-search-form .card {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 0.25rem 0.5rem;
    overflow: hidden;
    width: auto;
    max-width: 1100px;
}

.user-search-form .card-header {
    background: transparent;
    border-bottom: none;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    text-align: center;
}

.user-search-form .card-body {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: flex-start;
    padding: 0;
}

.user-search-form .form-group {
    flex: 1 1 150px;
    min-width: 120px;
    margin-bottom: 0.5rem !important;
}

.user-search-form .btn-reset {
    align-self: flex-start;
}

@media (max-width: 576px) {
    .user-search-form .form-group {
        flex: 1 1 100%;
    }

    .user-search-form .btn-reset {
        width: 100%;
    }
}
CSS);
?>

<div class="user-search user-search-form">
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

        <div class="card-body">

            <?= $form->field($model, 'id')->textInput(['placeholder' => 'ID']) ?>
            <?= $form->field($model, 'created_by_id')->textInput([ 'placeholder' => 'ID создателя']) ?>
            <?= $form->field($model, 'fio')->textInput(['placeholder' => 'ФИО']) ?>
            <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email']) ?>
            <?= $form->field($model, 'gender')->dropDownList([
                'Мужской' => 'Мужской',
                'Женский' => 'Женский'
            ], ['prompt' => 'Пол']) ?>
            <?= $form->field($model, 'role_id')->dropDownList(  Yii::$app->user->identity->role_id == 2
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
            ], ['prompt' => 'Роль']) ?>

            <?= Html::a('Сброс', ['index'], ['class' => 'btn btn-outline-light btn-reset']) ?>

            <?= $form->field($model, 'title_search')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'id_created_by')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<JS
$('#form_search').on('change', 'input, select', function(){
    $(this).closest('form').yiiActiveForm('submitForm');
});
JS);
?>
