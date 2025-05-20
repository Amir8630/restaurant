<?php

use app\models\Role;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\YiiAsset;
use yii\widgets\Pjax;

/** @var \app\modules\manager\models\ModelsSvgUploadForm $model */
?>
    <?= Html::a(
        Html::img('@web/img/arrow-left.svg', [
            'alt' => 'Назад',
            'style' => 'width: 20px; height: 20px; margin-right: 8px;'
        ]),
        Yii::$app->user->identity->role_id == Role::getRoleId('admin') ? ['/admin'] : ['/manager'],
        ['class' => 'btn btn-link d-inline-flex align-items-center']
    ) ?>
<?php Pjax::begin(['id' => 'scheme', 'enablePushState' => false, 'timeout' => 5000]); ?>
<div class="scheme-container position-relative mx-auto" style="justify-content: center;">

    <!-- Схема -->
    <div class="current-scheme position-relative">
        <?= file_exists(Yii::getAlias('@webroot/img/tables.svg')) ? file_get_contents(Yii::getAlias('@webroot/img/tables.svg')) : '<p class="text-danger">Файл схемы не найден.</p>' ?>
    </div>

    <!-- Оверлей -->
    <div class="upload-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center"
         style="cursor: pointer; background: rgba(0, 0, 0, 0.5); margin: auto;">
        <button type="button" class="btn btn-light btn-lg mb-3 shadow-sm">
            <i class="bi bi-cloud-upload me-2"></i>Загрузить новую схему
        </button>
        <p class="text-white mb-0 text-center">Нажмите для выбора SVG файла</p>
    </div>

    <!-- Форма -->
    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'position-absolute',
            'data-pjax' => true,
        ]
    ]) ?>
        <?= $form->field($model, 'svgFile')->fileInput([
            'class' => 'd-none',
            'id' => 'svgUploadInput',
            'accept' => 'image/svg+xml'
        ])->label(false) ?>
    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

<?php
$this->registerJsFile('@web/js/scheme.js', ['depends' => YiiAsset::class]);
?>

<style>
.current-scheme svg {
    display: block;
    border-radius: 10px; /* Закругление углов оверлея */

}
.upload-overlay {
    transition: opacity 0.3s ease;
    border-radius: 10px; /* Закругление углов оверлея */
}
.upload-overlay button {
    transition: transform 0.2s ease;
}
.upload-overlay button:hover {
    transform: translateY(-2px);
}
.scheme-container:hover .upload-overlay {
    background: rgba(0, 0, 0, 0.6) !important;
}
</style>