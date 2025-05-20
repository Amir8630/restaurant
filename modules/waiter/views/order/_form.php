<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderForm */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Url;
use yii\web\JsExpression;



$this->registerCssFile('@web/js/1.css');
$this->registerJsFile('@web/js/1.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);

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

    <table class="table" id="dishes-table">
        <thead>
            <tr>
                <th>Блюдо</th><th>Кол-во</th><th>—</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($model->dishes as $i => $dish): ?>
            <tr data-index="<?= $i ?>">
                <td>
                    <?= Html::activeHiddenInput($dish, "[$i]dish_id") ?>
                    <?= Html::textInput("OrderDishForm[$i][dish_name]", $dish->dish_name ?? '', [
                        'class' => 'form-control dish-name',
                        'data-index' => $i,
                        'placeholder' => 'Начните вводить...',
                    ]) ?>
                </td>
                <td>
                    <?= Html::activeTextInput($dish, "[$i]count", ['type'=>'number','min'=>1,'class'=>'form-control']) ?>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">&times;</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?= Html::button('Добавить блюдо', ['class'=>'btn btn-success', 'id'=>'add-row']) ?>

    <div class="form-group" style="margin-top:20px;">
        <?= Html::submitButton('Создать заказ', ['class'=>'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<!-- Шаблон строки -->
<table style="display:none;">
<tr id="row-template" data-index="INDEX">
    <td>
        <input type="hidden" name="OrderDishForm[INDEX][dish_id]" value="">
        <input class="form-control dish-name" name="OrderDishForm[INDEX][dish_name]" data-index="INDEX" placeholder="Начните вводить..." />
    </td>
    <td>
        <input type="number" min="1" value="1"
               name="OrderDishForm[INDEX][count]" class="form-control" />
    </td>
    <td>
        <button type="button" class="btn btn-danger remove-row">&times;</button>
    </td>
</tr>
</table>

<?php
$initialIndex = isset($model->dishes) && is_array($model->dishes) ? count($model->dishes) : 0;

$js = <<<JS
let idx = $initialIndex;

function initDishAutocomplete(selector) {
    \$(selector).autocomplete({
        minLength: 2,
        source: function(request, response) {
            \$.getJSON("dish-list", { q: request.term }, function(data){
                response(data);
            });
        },
        select: function(event, ui) {
            const idx = \$(this).data('index');
            \$('tr[data-index='+idx+']')
                .find('input[name="OrderDishForm['+idx+'][dish_id]"]')
                .val(ui.item.id);
            \$(this).val(ui.item.label);
            return false;
        }
    });
}

// инициализируем на всех текущих
\$('.dish-name').each(function(){
    initDishAutocomplete(this);
});

// добавить строку
\$('#add-row').on('click', function(){
    let tpl = \$('#row-template').prop('outerHTML').replace(/INDEX/g, idx);
    \$('#dishes-table tbody').append(tpl);
    initDishAutocomplete(\$('#dishes-table tbody tr').last().find('.dish-name'));
    idx++;
});

// удалить строку
\$('#dishes-table').on('click', '.remove-row', function(){
    \$(this).closest('tr').remove();
});
JS;

$this->registerJs($js);
?>