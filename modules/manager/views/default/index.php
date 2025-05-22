<?php

use yii\bootstrap5\Html;

?>

<h3>Панель управления менеджера</h3>

<div class="p-2 d-flex flex-wrap gap-2 justify-content-start" style="max-width:100%;">
    <?= Html::a('Загрузить схему зала', ['/manager/svg/upload'], ['class' => 'btn btn-outline-primary flex-fill', 'style' => 'min-width:180px;']) ?>
    <?= Html::a('Работа с пользователями', ['/manager/user'], ['class' => 'btn btn-outline-info flex-fill', 'style' => 'min-width:180px;']) ?>  
</div>
<style>
@media (max-width: 576px) {
    .d-flex.flex-wrap.gap-2 > a {
        flex-basis: 100% !important;
        min-width: 0 !important;
    }
}
</style>

