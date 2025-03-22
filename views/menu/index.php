<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\YiiAsset;

$this->title = 'Электронное меню';
$this->registerJsFile('@web/js/turn.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/test.js', ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<div class="menu-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <?= $form->field($model, 'file')->fileInput() ?>
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>

    <?php if (!empty($menuItems)): ?>
        <div id="menu-book">
            <?php foreach (array_chunk($menuItems, 2) as $page): // Каждая страница содержит 2 блюда ?>
                <div class="menu-page">
                    <?php foreach ($page as $item): ?>
                        <div class="menu-item">
                            <h3><?= Html::encode($item['name']) ?></h3>
                            <p><?= Html::encode($item['description']) ?></p>
                            <p class="price"><?= Html::encode($item['price']) ?> ₽</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.menu-container {
    text-align: center;
    margin: 20px auto;
    max-width: 800px;
}

#menu-book {
    width: 600px;
    height: 400px;
    margin: auto;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.menu-page {
    width: 100%;
    height: 100%;
    background: #fff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border: 1px solid #ddd;
}

.menu-item {
    margin-bottom: 20px;
    text-align: center;
}

.price {
    font-weight: bold;
    color: #d9534f;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var book = $('#menu-book');
    if (book.length) {
        book.turn({
            width: 600,
            height: 400,
            autoCenter: true,
            gradients: true,
            acceleration: true,
            duration: 1000,  // Время анимации
            easing: 'easeInOutQuad'  // Плавное ускорение и замедление
        });
    }
});
</script>
