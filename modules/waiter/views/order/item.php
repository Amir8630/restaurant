<?php
use yii\helpers\Html;
use app\models\Status;

/** @var app\models\Order $model */

$this->registerCss(<<<'CSS'
.order-card {
  background: #2d363f;
  border: 2px solid #4a535e;
  border-radius: 16px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.05);
  margin-bottom: 2rem;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  width: 100%;
}
.order-card-section { padding: 1.5rem; padding-bottom: 0; }
.order-card-header {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 1.4rem; font-weight: 600; color: #eef1f5;
  border-bottom: 1px solid #4a535e; margin-bottom: 1rem; padding-bottom: .5rem;
}
.order-status-badge { font-size:.9rem; text-transform:uppercase; font-weight:bold; }
.status-new         { color: #70a1ff; }
.status-in-progress { color: #fffa65; }
.status-completed   { color: #7bed9f; }
.status-canceled    { color: #ff6b81; }
.status-issued      { color: #9b59b6; } /* статус «Выдано» */
.order-card-body p { margin:.4rem 0; font-size:.95rem; color:#dcdfe3; }
.order-card-body ul { list-style:none; padding:0; margin:0; }
.order-card-body li {
  background:#3b434c; border:2px solid #5a6672; border-radius:12px;
  padding:1rem; margin-bottom:.75rem; display:flex; justify-content:space-between;
  align-items:center;
}
.order-card-body li:nth-child(odd)  { background:#3e4750; }
.order-card-body li:nth-child(even) { background:#353c45; }
.dish-info { display:flex; align-items:center; gap:.75rem; color:#eef1f5; font-weight:600; }
.dish-count { font-size:1.1rem; font-weight:bold; }
.order-card-footer {
  margin-top:1rem; display:flex; justify-content:flex-end; gap:.5rem; flex-wrap:wrap; padding-bottom: 1.5rem;
}
.btn-light-action {
  text-decoration:none; font-size:.9rem; padding:.4rem 1rem;
  border:2px solid #eef1f5; background:transparent; color:#eef1f5;
  border-radius:8px; font-weight:600; transition:background .2s, color .2s;
}
.btn-light-action:hover { background:#eef1f5; color:#2d363f; }
@media(max-width:768px){ .order-card{flex-direction:column} }
CSS
);
?>

<div class="order-card">
  <!-- Заголовок -->
  <div class="order-card-section">
    <div class="order-card-header">
      <span>Заказ №<?= Html::encode($model->id) ?></span>
      <?php
        $map = [
          Status::getStatusId('Новый')           => 'status-new',
          Status::getStatusId('готовится')       => 'status-in-progress',
          Status::getStatusId('готов к выдаче')  => 'status-completed',
          Status::getStatusId('Отменён')         => 'status-canceled',
          Status::getStatusId('Выдано')          => 'status-issued',
        ];
        $oCls = $map[$model->order_status] ?? '';
      ?>
      <span class="order-status-badge <?= $oCls ?>">
        <?= Html::encode($model->status->title ?? '—') ?>
      </span>
    </div>
    <div class="order-card-body">
      <p><strong>Стол:</strong> <?= $model->order_type==11?'—':($model->table_id?:'—') ?></p>
      <p><strong>Тип:</strong> <?= $model->order_type==10?'На месте':'С собой' ?></p>
      <p><strong>Создан:</strong> <?= Yii::$app->formatter->asTime($model->created_at) ?></p>
      <p><strong>Официант:</strong> <?= Html::encode($model->waiter->fio ?? '—') ?></p>
    </div>
  </div>

  <!-- Список блюд -->
  <div class="order-card-section">
    <div class="order-card-header" style="border-bottom:none; padding:0; margin-bottom:1rem;"></div>
    <div class="order-card-body">
      <ul>
  <?php foreach ($model->orderDishes as $dish):
    $dCls = $map[$dish->status_id] ?? '';
  ?>
  <li>
    <div class="dish-info">
      <?= Html::encode($dish->dish->title ?? '(неизвестно)') ?>
      <span class="dish-count <?= $dCls ?>">× <?= Html::encode($dish->count) ?></span>
      <span class="order-status-badge <?= $dCls ?>">
        <?= Html::encode($dish->status->title ?? '—') ?>
      </span>
    </div>
    <div>
      <?php if ($dish->status_id == Status::getStatusId('Готов к выдаче')): ?>
        <?= Html::button(
            'Выдать',
            [
              'class' => 'btn-light-action change-dish-status-btn',
              'data-id' => $dish->id,
              'data-status' => Status::getStatusId('Выдано'),
              'data-url' => \yii\helpers\Url::to(['update-dish-status', 'id' => $dish->id, 'status' => Status::getStatusId('Выдано')]),
            ]
        ) ?>
      <?php endif; ?>
    </div>
  </li>
  <?php endforeach; ?>
</ul>
    </div>
  </div>

<div class="order-card-section order-card-footer">
    <!-- <= Html::button(
        '<i class="bi bi-eye"></i> Просмотр',
        ['class' => 'btn-light-action', 'onclick' => "window.location.href='".\yii\helpers\Url::to(['view','id'=>$model->id])."';"]
    ) ?> -->

    <?php if ($model->order_status == Status::getStatusId('готов к выдаче')): ?>
        <?= Html::button(
            '<i class="bi bi-check2-square"></i> Выдать',
            [
                'class' => 'btn-light-action change-order-status-btn',
                'data-id' => $model->id,
                'data-status' => Status::getStatusId('Выдано'),
                // строим полный URL в data-url
                'data-url' => \yii\helpers\Url::to(['update-status','id'=>$model->id,'status'=>Status::getStatusId('Выдано')]),
            ]
        ) ?>
    <?php endif; ?>
    <?php if ($model->order_status == Status::getStatusId('Новый')): ?>
        <?= Html::button(
            '<i class="bi bi-trash"></i> Удалить',
            [
                'class' => 'btn-light-action',
                'onclick' => "if(confirm('Вы уверены?')) window.location.href='".\yii\helpers\Url::to(['delete','id'=>$model->id])."?_method=post';",
            ]
        ) ?>
    <?php endif; ?>
</div>
</div>
