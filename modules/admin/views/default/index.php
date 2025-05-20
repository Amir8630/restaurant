<?php

use yii\bootstrap5\Html;

?>

<h3>Панель управления администратора</h3>

<div>
    <?= Html::a('Загрузить схему зала', ['/manager/svg/upload'], ['class' => 'btn btn-outline-primary']) ?>
    <?= Html::a('Диаграмма бронирований', ['/admin/booking/stats'], ['class' => 'btn btn-outline-success']) ?>  
</div>

