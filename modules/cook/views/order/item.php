<?php
use yii\helpers\Html;
use app\models\Status;

$this->registerCss(<<<'CSS'
.order-card { display:flex; width:100%; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.08); margin-bottom:1.5rem; }
.order-card-section { padding:1.5rem; box-sizing:border-box; }
.order-card-section:first-child { flex:1 1 30%; border-right:2px solid #ddd; }
.order-card-section:last-child  { flex:1 1 70%; }
.order-card-header { font-size:1.3rem; margin-bottom:1rem; position:relative; padding-bottom:.5rem; }
.order-card-section:first-child .order-card-header::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; background:#20c997; }
.order-card-section:last-child  .order-card-header::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; background:#ff6b6b; }
.order-status-badge { padding:.3rem .8rem; border-radius:10px; font-size:.85rem;  color:#fff; text-transform:uppercase; margin-left:.5rem; }
.status-new          { background:#17a2b8; }
.status-in-progress  { background:#ffc107; color:#212529; }
.status-completed    { background:#28a745; }
.status-canceled     { background:#dc3545; }
.order-card-body p   { margin:.6rem 0; font-size:.95rem; color:#4a4a4a; }
.order-card-body ul  { list-style:none; padding:0; margin:0; }
.order-card-body li  { display:flex; justify-content:space-between; align-items:center; padding:1rem; border-bottom:2px solid #bbb; border-radius:8px; margin-bottom:.5rem; }
.order-card-body li:nth-child(odd)  { background:#f0f0f0; }
.order-card-body li:nth-child(even) { background:#d0d0d0; }
.dish-count          { ; font-size:1.1rem; color:#e74c3c; margin-left:1rem; }
.order-card-footer   { margin-top:1rem; display:flex; gap:.5rem; flex-wrap:wrap; }
.order-card-footer .btn { font-size:.9rem; padding:.45rem .9rem; }
@media(max-width:768px){
  .order-card{flex-direction:column}
  .order-card-section:first-child{border-right:none;border-bottom:2px solid #ddd}
}
CSS
);
?>

<div class="order-card">
  <!-- Левый блок — данные заказа -->
  <div class="order-card-section">
    <div class="order-card-header">
      Заказ №<?= Html::encode($model->id) ?>
      <?php
        $map = [
          Status::getStatusId('Новый')           => 'status-new',
          Status::getStatusId('готовится')       => 'status-in-progress',
          Status::getStatusId('готов к выдаче') => 'status-completed',
          Status::getStatusId('Завершено')       => 'status-completed',
          Status::getStatusId('Отменено')        => 'status-canceled',
        ];
        $oCls = $map[$model->order_status] ?? '';
      ?>
      <span class="order-status-badge <?= $oCls ?>">
        <?= Html::encode($model->status->title) ?>
      </span>
    </div>
    <div class="order-card-body">
            <!-- <p><strong>Стол:</strong> <= $model->order_type == 11 ? '—' : ($model->table_id ?: '—') ?></p>
      <p><strong>Тип:</strong> <= $model->order_type == 10 ? 'На месте' : 'С собой' ?></p> -->
    <p>
      <strong>Стол:</strong>
      <span style="display:inline-block; padding:0.2em 0.8em; border-radius:8px; background:<?= $model->order_type == 11 ? '#adb5bd' : '#20c997' ?>; color:#fff; font-weight:bold;">
        <?= $model->order_type == 11 ? '—' : ($model->table_id ?: '—') ?>
      </span>
    </p>
    <p>
      <strong>Тип:</strong>
      <span style="display:inline-block; padding:0.2em 0.8em; border-radius:8px; background:<?= $model->order_type == 10 ? '#007bff' : '#fd7e14' ?>; color:#fff; font-weight:bold;">
        <?= $model->order_type == 10 ? 'На месте' : 'С собой' ?>
      </span>
    </p>
    <p><strong>Создан:</strong> <?= Yii::$app->formatter->asTime($model->created_at) ?></p>
    </div>
    <div class="order-card-footer">
      <?php
        $cur  = $model->order_status;
        $opts = [];
        
          $opts = [
            Status::getStatusId('готовится')       => 'Готовится',
            Status::getStatusId('готов к выдаче') => 'К выдаче',
            Status::getStatusId('Отменено')        => 'Отменено',
          ];
       
       foreach ($opts as $stId => $stTitle) {
            $colorMap = [
                Status::getStatusId('готовится')       => '#ffc107', // жёлтый
                Status::getStatusId('готов к выдаче') => '#28a745', // зелёный
                Status::getStatusId('Отменено')        => '#dc3545', // красный
            ];
            echo Html::button($stTitle, [
                'class' => 'btn btn-sm change-order-status-btn',
                'data-id' => $model->id,
                'data-status' => $stId,
                'style' => 'background-color: ' . ($colorMap[$stId] ?? '#007bff') . '; color: white;',
            ]);
        }
        ?>
    </div>
  </div>

  <!-- Правый блок — список блюд -->
  <div class="order-card-section">
    <div class="order-card-header">Блюда</div>
    <div class="order-card-body">
      <ul>
      <?php foreach ($model->orderDishes as $dish):
        $dCls  = $map[$dish->status_id] ?? '';
        $curD  = $dish->status_id;
        $dOpts = [];
        if ($curD == Status::getStatusId('Новый')) {
          $dOpts = [
            Status::getStatusId('готовится')       => 'Готовится',
            Status::getStatusId('готов к выдаче') => 'К выдаче',
          ];
        } elseif ($curD == Status::getStatusId('готовится')) {
          $dOpts = [
            Status::getStatusId('готов к выдаче') => 'К выдаче',
          ];
        }
      ?>
        <li>
          <div>
            <?= Html::encode($dish->dish->title) ?>
            <span class="dish-count" style="color:<?= 
                $dish->status_id == Status::getStatusId('готовится') ? '#b8860b' : (
                $dish->status_id == Status::getStatusId('готов к выдаче') ? '#28a745' : (
                $dish->status_id == Status::getStatusId('Новый') ? '#17a2b8' : (
                $dish->status_id == Status::getStatusId('Отменено') ? '#dc3545' : '#e74c3c'
            ))) ?>;">× <?= Html::encode($dish->count) ?></span>
            <span class="order-status-badge <?= $dCls ?>">
              <?= Html::encode($dish->status->title) ?>
            </span>
          </div>
          <div>
<?php foreach ($dOpts as $stId => $stTitle): 
        $colorMap = [
            Status::getStatusId('готовится')       => '#ffc107',
            Status::getStatusId('готов к выдаче') => '#28a745',
        ];
        echo Html::button($stTitle, [
            'class' => 'btn btn-sm change-dish-status-btn',
            'data-id' => $dish->id,
            'data-status' => $stId,
            'style' => 'background-color: ' . ($colorMap[$stId] ?? '#6c757d') . '; color: white;',
        ]);
    ?>
<?php endforeach; ?>
          </div>
        </li>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
