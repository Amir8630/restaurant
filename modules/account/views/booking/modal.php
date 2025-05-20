<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\web\YiiAsset;
use yii\helpers\Url;

$pjax = $pjax ?? false;
?>
<?php
Modal::begin([
    'id' => 'cancel-modal',
    'title' => 'Отмена брони',
    'options' => [
        'data-mail-url' => Url::to(['booking/mail-cancel']), // Вот сюда
    ],
    
]);
?>
 <!-- тут будеть текст от джса -->
    <div id="text"></div>
    <div class="d-flex justify-content-end gap-3">
        <?= Html::a('Назад', '', ['class' => 'btn btn-outline-primary btn-close-modal']) ?>

        <!-- <=$pjax
            ? Html::a('Отменить', '', ['class' => 'btn btn-outline-danger btn-cancel',
            'data-pjx' => $pjax]) 
            : Html::a('Отменить', '', ['class' => 'btn btn-outline-danger btn-cancel',
            'data-method' => 'post', 'data-pjx' => 0])?> -->

        <?= Html::a('Отменить', '', [
            'class' => 'btn btn-outline-danger btn-cancel',
            'data-method' => 'post',
            'data-pjx' => $pjax ?: 0,
            'data-id' => '', // сюда потом через JS вставим ID брони
        ]) ?>
    </div>

    

<?php
    Modal::end();
    $this->registerJsFile('/js/cancelModal.js', ['depends' => YiiAsset::class]);
?>