<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View          $this */
/** @var app\models\Order       $model */
/** @var app\models\OrderDish[] $dishes  */

?>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'table_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(\app\models\Table::find()->all(), 'id', 'id'),
        ['prompt' => 'Выберите стол']
    ) ?>

    <?= $form->field($model, 'order_type')->radioList([
        10 => 'На месте',
        11 => 'С собой',
    ]) ?>

    <hr>
    <h4>Блюда</h4>

    <table id="dishes-table" class="table">
        <thead>
          <tr><th>Блюдо</th><th>Кол-во</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($dishes)) {
            $dishes = [new \app\models\OrderDish()];
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
                <td>
                    <?= Html::activeTextInput($dish, "[$i]count", [
                        'type'=>'number', 'min'=>1,
                        'class'=>'form-control',
                        'value'=>$dish->count ?: 1
                    ]) ?>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">&times;</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?= Html::button('Добавить блюдо', ['class'=>'btn btn-success','id'=>'add-row']) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать заказ' : 'Сохранить заказ',
            ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>


<?php
// Подключаем модалку — она одна на всей странице, её тоже выносим сюда
use yii\bootstrap5\Modal;
Modal::begin([
    'id'            => 'dishModal',
    'title'         => 'Выберите блюдо',
    'toggleButton'  => false,
    'dialogOptions' => ['class'=>'modal-dialog modal-dialog-centered modal-lg'],
]); ?>
  <div class="modal-search-wrapper">
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
// Подключаем стили и JS, которые ты уже написал (1.css и 1.js)
$this->registerCssFile('@web/js/1.css');
$this->registerJsFile('@web/js/1.js', ['depends'=>[\yii\web\JqueryAsset::class]]);
?>
