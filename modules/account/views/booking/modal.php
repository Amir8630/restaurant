<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\web\YiiAsset;

$pjax = $pjax ?? false;
?>
<?php
    Modal::begin([
        'id' => 'cancel-modal',
        'title' => 'Удалить бронь?',
    ]);
?>
 
    <div> Удалить бронь №100111?</div>
    <div class="d-flex justify-content-end gap-3">
        <?= Html::a('Назад', '', ['class' => 'btn btn-outline-primary btn-close-modal']) ?>

        <?=$pjax
            ? Html::a('Отменить', '', ['class' => 'btn btn-outline-danger btn-cancel',
            'data-pjx' => $pjax]) 
            : Html::a('Отменить', '', ['class' => 'btn btn-outline-danger btn-cancel',
            'data-method' => 'post', 'data-pjx' => 0])?>
    </div>

<?php
    Modal::end();
    $this->registerJsFile('/js/cancelModal.js', ['depends' => YiiAsset::class]);
?>