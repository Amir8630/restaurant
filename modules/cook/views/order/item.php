<?php
use yii\helpers\Html;
use app\models\Status;

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

.order-card-section {
  padding: 1.5rem;
  box-sizing: border-box;
}

.order-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.4rem;
  font-weight: 600;
  color: #eef1f5;
  border-bottom: 1px solid #4a535e;
  padding-bottom: .5rem;
  margin-bottom: 1rem;
}

.order-status-badge {
  font-size: .9rem;
  text-transform: uppercase;
  font-weight: bold;
  color: inherit;
}

.status-completed   { color: #7bed9f; }
.status-in-progress { color: #fffa65; }
.status-new         { color: #70a1ff; }
.status-canceled    { color: #ff6b81; }

.order-card-body p {
  margin: .4rem 0;
  font-size: .95rem;
  color: #dcdfe3;
}

.order-card-body ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.order-card-body li {
  background: #3b434c;
  border: 2px solid #5a6672;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: .75rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-card-body li:nth-child(odd)  { background: #3e4750; }
.order-card-body li:nth-child(even) { background: #353c45; }

/* Название и количество */
.order-card-body li div:first-child {
  display: flex;
  align-items: center;
  gap: .75rem;
  color: #eef1f5;            /* светлый текст */
  font-weight: 600;
}
.dish-count {
  font-size: 1.1rem;
  font-weight: bold;
}

/* Кнопки */
.btn-outline-action {
  font-size: .9rem;
  padding: .4rem 1rem;
  border: 2px solid currentColor;
  background: transparent;
  color: currentColor;
  border-radius: 8px;
  font-weight: 600;
  transition: background .2s, color .2s;
}
.btn-outline-action:hover {
  background: currentColor;
  color: #2d363f;            /* темный фон на контраст */
}

/* Футер */
.order-card-footer {
  margin-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
}

@media(max-width:768px){
  .order-card{flex-direction:column}
}

/* Базовый стиль для всех кнопок */
.btn-outline-action {
  font-size: .9rem;
  padding: .4rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  transition: background .2s, color .2s;
  background: transparent;
  color: #f1f1f1;
  border: 2px solid #f1f1f1;
}

/* При наведении – заливка цветом статуса */
.btn-outline-action.status-completed:hover   { background-color: #7bed9f; color: #1b262c; border-color: #7bed9f; }
.btn-outline-action.status-in-progress:hover { background-color: #fffa65; color: #1b262c; border-color: #fffa65; }
.btn-outline-action.status-new:hover         { background-color: #70a1ff; color: #1b262c; border-color: #70a1ff; }
.btn-outline-action.status-canceled:hover    { background-color: #ff6b81; color: #1b262c; border-color: #ff6b81; }

/* Цвет текста и рамки по умолчанию (без наведения) */
.btn-outline-action.status-completed      { color: #7bed9f; border-color: #7bed9f; }
.btn-outline-action.status-in-progress { color: #fffa65; border-color: #fffa65; }
.btn-outline-action.status-new      { color: #70a1ff; border-color: #70a1ff; }
.btn-outline-action.status-canceled    { color: #ff6b81; border-color: #ff6b81; }
/* .order-status-badge {
  font-size: .85rem;
  text-transform: uppercase;
  font-weight: bold;
  padding: .2rem .6rem;
  border-radius: 12px;
  border: 2px solid currentColor;
  background-color: rgba(255, 255, 255, 0.05); /* легкий фон */


CSS
);
?>

<div class="order-card">
  <div class="order-card-section">
    <div class="order-card-header">
      <span>Заказ №<?= Html::encode($model->id) ?></span>
      <?php
        $map = [
          Status::getStatusId('Новый')           => 'status-new',
          Status::getStatusId('готовится')       => 'status-in-progress',
          Status::getStatusId('готов к выдаче')  => 'status-completed',
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
      <p><strong>Стол:</strong> <?= $model->order_type == 11 ? '—' : ($model->table_id ?: '—') ?></p>
      <p><strong>Тип:</strong> <?= $model->order_type == 10 ? 'На месте' : 'С собой' ?></p>
      <p><strong>Создан:</strong> <?= Yii::$app->formatter->asTime($model->created_at) ?></p>
    </div>
    <div class="order-card-footer">
      <?php
        $opts = [
          Status::getStatusId('готовится')       => 'Готовится',
          Status::getStatusId('готов к выдаче')  => 'Готов к выдаче',
          Status::getStatusId('Отменено')        => 'Отменить',
        ];
        foreach ($opts as $stId => $stTitle):
          $cls = $map[$stId] ?? '';
      ?>
        <?= Html::button($stTitle, [
            'class' => "btn-outline-action {$cls} change-order-status-btn",
            'data-id' => $model->id,
            'data-status'=> $stId,
        ]) ?>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="order-card-section">
    <div class="order-card-header" style="border-bottom:none; padding:0; margin-bottom:1rem;">
      <!-- разделитель без текста -->
    </div>
    <div class="order-card-body">
      <ul>
        <?php foreach ($model->orderDishes as $dish):
          $dCls = $map[$dish->status_id] ?? '';
          $dOpts = [];
          if ($dish->status_id == Status::getStatusId('Новый')) {
            $dOpts = [
              Status::getStatusId('готовится')       => 'Готовится',
              Status::getStatusId('готов к выдаче')  => 'К выдаче',
            ];
          } elseif ($dish->status_id == Status::getStatusId('готовится')) {
            $dOpts = [ Status::getStatusId('готов к выдаче') => 'К выдаче' ];
          }
        ?>
        <li>
          <div>
            <?= Html::encode($dish->dish->title) ?>
            <span class="dish-count <?= $dCls ?>">× <?= Html::encode($dish->count) ?></span>

            <span class="order-status-badge <?= $dCls ?>">
              <?= Html::encode($dish->status->title) ?>
            </span>
          </div>
          <div>
            <?php foreach ($dOpts as $stId => $stTitle): ?>
              <?php $cls = $map[$stId] ?? ''; ?>
              <?= Html::button($stTitle, [
                  'class' => "btn-outline-action {$cls} change-dish-status-btn",
                  'data-id'    => $dish->id,
                  'data-status'=> $stId,
              ]) ?>
            <?php endforeach; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
