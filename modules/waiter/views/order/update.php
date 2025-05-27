<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Order $model */

$this->title = 'Редактирование заказа №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование заказа №' . $model->id;
?>
<div class="order-update">


    <?= $this->render('_form', [
        'model' => $model,
        'dishes' => $dishes,
    ]) ?>

</div>
