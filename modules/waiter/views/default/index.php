<?php

use yii\bootstrap5\Html;

?>

<h3>Панель управления официанта</h3>

<div class="p-2 d-flex flex-wrap gap-2 justify-content-start" style="max-width:100%;">
    <?= Html::a('Создать заказ', ['/waiter/order'], ['class' => 'btn btn-outline-primary']) ?>
</div>
<style>
@media (max-width: 576px) {
    .d-flex.flex-wrap.gap-2 > a {
        flex-basis: 100% !important;
        min-width: 0 !important;
    }
}
</style>

