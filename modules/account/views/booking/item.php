<?php

use app\models\Status;
use yii\bootstrap5\Html;

?>


<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title"><?= 'Бронь №' . $model->id?></h5>
    <p class="card-text">Some quickf the card's content.</p>
  </div>
  <?= Html::a('Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-info w-100'])?>
  <?= $model->status_id == Status::getStatusId('Забронировано') ? Html::a('Отменить', ['cancel-modal', 'id' => $model->id], ['class' => 'btn btn-outline-warning mt-2 w-100 btn-cancel-modal']) : ''?>
</div>

<!-- проблема в том что я убрал модели из передачи и теперь я прочто renfer делаю без передачи  -->