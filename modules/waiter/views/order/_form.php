<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;

/* @var yii\web\View $this */
/* @var app\models\Order $model */
/* @var app\models\OrderDish[] $dishes */

$this->title = $model->isNewRecord ? 'Создание заказа' : 'Редактирование заказа №' . $model->id;

// Общие стили для формы
$this->registerCss(<<<'CSS'
.form-container {
  max-width: 800px;
  margin: 2rem auto;
  background: #fff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.form-container h1 {
  margin-bottom: 1.5rem;
  font-size: 1.8rem;
  text-align: center;
}
.form-group {
  margin-bottom: 1.25rem;
}
.table-responsive {
  overflow-x: auto;
}
#dishes-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 0.75rem;
}
#dishes-table th {
  background: #f0f0f0;
  padding: 0.75rem;
  padding-top: 0;
  text-align: left;
}
#dishes-table td {
  background: #fafafa;
  vertical-align: middle;
}
#dishes-table .form-control {
  margin-bottom: 0;
}
.remove-row {
  line-height: 1;
  padding: 0.4rem 0.8rem;
}
#add-row {
  margin-bottom: 1.5rem;
}


.btn-outline-primary {
  border-color: #007bff;
  color: #007bff;
}
.btn-outline-primary:hover {
  background: #007bff;
  color: #fff;
}
/* Скрываем form-group стол при «С собой» */
CSS
);
?>

<div class="form-container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <?= Html::a(
                Html::img('@web/img/arrow-left.svg', [
                    'alt' => 'Назад',
                    'style' => 'width: 20px; height: 20px;'
                ]),
                ['/waiter/order'],
                ['class' => 'btn btn-link d-inline-flex align-items-center p-0']
            ) ?>
            <div class="flex-grow-1 d-flex justify-content-center">
                <h1 class="mb-0 text-center"><?= Html::encode($this->title) ?></h1>
            </div>
            <div style="width:20px"></div>
        </div>

  <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'table_id', [
          'options' => ['class' => 'form-group'],
      ])->dropDownList(
          \yii\helpers\ArrayHelper::map(\app\models\Table::find()->all(), 'id', 'id'),
          [
            'prompt' => 'Выберите стол',
            'id'     => 'order-table',
          ]
    ) ?>

    <?= $form->field($model, 'order_type', [
          'template' => "{label}\n<div id=\"order-type\">{input}</div>\n{error}"
      ])->radioList([
          10 => 'На месте',
          11 => 'С собой',
      ], ['itemOptions' => ['class' => 'form-check-input ms-1']])
    ?>

    <hr>

    <div class="table-responsive">
      <table id="dishes-table" class="table">
        <thead>
          <tr><th>Блюдо</th><th style="width:100px">Кол-во</th><th></th></tr>
        </thead>
        <tbody>
          <?php
          if (empty($dishes)) {
              $dishes = [ new \app\models\OrderDish() ];
          }
          foreach ($dishes as $i => $dish): ?>
          <tr data-index="<?= $i ?>">
            <td>
              <?= Html::activeHiddenInput($dish, "[$i]dish_id") ?>
              <?= Html::textInput("OrderDish[$i][dish_name]",
                  $dish->dish->title ?? '',
                  [
                    'class'=>'form-control dish-picker',
                    'data-index'=>$i,
                    'readonly'=>true,
                    'placeholder'=>'Кликните для выбора'
                  ]
              ) ?>
            </td>
            <td style="width:1%; white-space:nowrap;">
              <?= Html::activeTextInput($dish, "[$i]count", [
                  'type'=>'number','min'=>1,'class'=>'form-control text-center',
                  'value'=>$dish->count ?: 1,
                  'style'=>'width:4em; display:inline-block;'
              ]) ?>
            </td>
            <td class="text-center" style="width: 3em;">
              <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="width:2em; height:2em; padding:0;">&times;</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

        <?= Html::button(
        '<i class="bi bi-plus" style="font-size: 1rem;"></i> Добавить блюдо',
        [
            'class' => 'btn btn-outline-success btn-sm',
            'id' => 'add-row',
            'type' => 'button',
            'style' => 'padding: 0.2rem 0.5rem;'
        ]
        ) ?>

    <div class="form-group text-center mt-4">
      <?= Html::submitButton($model->isNewRecord ? 'Создать заказ' : 'Сохранить заказ',
          ['class' => 'btn btn-outline-primary2 btn-lg']) ?>
    </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
// Модалка выбора блюда
Modal::begin([
    'id'            => 'dishModal',
    'title'         => 'Выберите блюдо',
    'toggleButton'  => false,
    'dialogOptions' => ['class'=>'modal-dialog modal-dialog-centered modal-lg'],
]); ?>
  <div class="modal-search-wrapper mb-2">
    <input type="text" id="modalSearch" class="form-control" placeholder="Фильтр по названию...">
  </div>
  <div class="modal-scroll-area">
    <table class="table table-hover mb-0" id="modalList">
      <thead><tr><th>Название блюда</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
<?php Modal::end(); ?>

<?php
// JS для динамики формы
$js = <<<'JS'
;(function(){
  // Скрыть / показать поле "Стол"
  function toggleTableField() {
    var type = $('#order-type input:checked').val();
    $('#order-table').closest('.form-group').toggle(type === '10');
  }

  $(function(){
    $('#order-type input').on('change', toggleTableField);

    var modal     = $('#dishModal'),
        modalList = $('#modalList tbody'),
        search    = $('#modalSearch'),
        idx       = $('#dishes-table tbody tr').length,
        currentRow;

    function addRow(i) {
      $('#dishes-table tbody').append(
        '<tr data-index="'+i+'">'+
          '<td>'+
            '<input type="hidden" name="OrderDish['+i+'][dish_id]" value="">'+
            '<input type="text" class="form-control dish-picker" '+
              'name="OrderDish['+i+'][dish_name]" readonly placeholder="Кликните для выбора">'+
          '</td>'+
          '<td style="width:1%; white-space:nowrap;">'+
            '<input type="number" min="1" value="1" class="form-control text-center" '+
              'name="OrderDish['+i+'][count]" style="width:4em; display:inline-block;">'+
          '</td>'+
          '<td class="text-center" style="width: 3em;">'+
            '<button type="button" class="btn btn-sm btn-outline-danger remove-row" style="width:2em; height:2em; padding:0;">&times;</button>'+
          '</td>'+
        '</tr>'
      );
    }

    if (idx === 0) addRow(idx++);
    $('#add-row').on('click', function(){ addRow(idx++); });

    $('#dishes-table').on('click', '.remove-row', function(){
      $(this).closest('tr').remove();
    });

    $('#dishes-table').on('click', '.dish-picker', function(){
      currentRow = $(this).closest('tr');
      modal.modal('show');
      search.val('');
      loadDishes('');
    });

    function loadDishes(q) {
      $.getJSON('dish-list', { q: q }, function(data){
        modalList.empty();
        data.forEach(function(item){
          modalList.append(
            '<tr><td data-id="'+item.id+'">' + item.label + '</td></tr>'
          );
        });
      });
    }

    search.on('input', function(){
      loadDishes($(this).val());
    });

    modalList.on('click', 'tr', function(){
      var cell = $(this).find('td'),
          name = cell.text(),
          id   = cell.data('id');
      currentRow.find('input[name$="[dish_name]"]').val(name);
      currentRow.find('input[name$="[dish_id]"]').val(id);
      modal.modal('hide');
    });
  });
})();
JS;
$this->registerJs($js);
$this->registerCssFile('@web/js/1.css');