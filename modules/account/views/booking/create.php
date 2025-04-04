<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Booking $model */

$this->title = 'Бронирование';
$this->params['breadcrumbs'][] = ['label' => 'Брони', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-create">

    <!-- <h1><= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
