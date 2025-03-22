<?php

use yii\bootstrap5\Html;

?>


<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title"><?= 'Бронь №' . $model->id?></h5>
    <p class="card-text">Some quickf the card's content.</p>
  </div>
  <?= Html::a('Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-info w-100'])?>
  <?= Html::a('Отменить', ['cancel', 'id' => $model->id], ['class' => 'btn btn-outline-warning mt-2 w-100'])?>
</div>