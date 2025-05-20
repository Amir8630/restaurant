<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

?>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'table_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(\app\models\Table::find()->all(), 'id', 'id'),
        ['prompt' => 'Выберите стол']
    ) ?>

    <?= $form->field($model, 'order_type')->radioList([
        '10' => 'На месте',
        '11' => 'С собой',
    ]) ?>

    <table id="dishes-table" class="table">
        <thead><tr><th>Блюдо</th><th>Кол-во</th><th>—</th></tr></thead>
        <tbody>
        <?php
        if (empty($model->dishes)) {
            $model->dishes = [ new \app\models\OrderDish() ];
        }
        foreach ($model->dishes as $i => $dish): ?>
            <tr data-index="<?= $i ?>">
                <td>
                    <?= Html::activeHiddenInput($dish, "[$i]dish_id") ?>
                    <?= Html::textInput("OrderDishForm[$i][dish_name]",
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
                        'type'=>'number','min'=>1,'class'=>'form-control',
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
        <?= Html::submitButton('Создать заказ', ['class'=>'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php Modal::begin([
    'id'            => 'dishModal',
    'title'         => 'Выберите блюдо',
    'toggleButton'  => false,
    'clientOptions' => [
        'backdrop' => false,
        'keyboard' => true,
    ],
    'dialogOptions' => [
        'class' => 'modal-dialog modal-dialog-centered modal-lg',
    ],
]); ?>

<!-- поле поиска фиксированное -->
<div class="modal-search-wrapper">
  <input type="text" id="modalSearch" class="form-control" placeholder="Фильтр по названию...">
</div>

<!-- прокручиваемый список -->
<div class="modal-scroll-area">
  <table class="table table-hover mb-0" id="modalList">
    <thead><tr><th>Название блюда</th></tr></thead>
    <tbody></tbody>
  </table>
</div>

<?php Modal::end(); ?>


<?php
$this->registerCssFile('@web/js/1.css');
$this->registerJsFile('@web/js/1.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
