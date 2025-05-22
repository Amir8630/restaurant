<?php

use yii\bootstrap5\Html;

?>

<h3>Панель управления Повара</h3>

<div class="p-2 d-flex flex-wrap gap-2 justify-content-start" style="max-width:100%;">
    <?= Html::a('Работа с заказами', ['/cook/order'], ['class' => 'btn btn-outline-primary']) ?>
</div>
<style>
@media (max-width: 576px) {
    .d-flex.flex-wrap.gap-2 > a {
        flex-basis: 100% !important;
        min-width: 0 !important;
    }
}
</style>

