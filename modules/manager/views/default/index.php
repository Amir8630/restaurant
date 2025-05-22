<?php

use yii\bootstrap5\Html;

?>

<h3>Панель управления менеджера</h3>

<div>
    <?= Html::a('Загрузить схему зала', ['svg/upload'], ['class' => 'btn btn-outline-primary']) ?>
    <?= Html::a('Пользователи', ['user/index'], ['class' => 'btn btn-outline-info text1 ']) ?>
