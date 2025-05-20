<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = "Пользователь №{$model->id}";
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>



    <div class="d-flex justify-content-between align-items-center mb-2">


        <div class="btn-group">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Назад', ['index'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="bi bi-pencil"></i> Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-success']) ?>
            <?= Html::a(
                '<i class="bi bi-trash"></i> Удалить',
                ['delete', 'id' => $model->id],
                [
                    'class' => 'btn btn-outline-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                        'method'  => 'post',
                    ],
                ]
            ) ?>
        </div>
    </div>

    <div class="booking-card shadow-sm">
        <div class="booking-card-header">
            <div class="booking-id"><?= Html::encode($this->title) ?></div>
        </div>

        <div class="booking-card-body">
            <p><i class="bi bi-person-circle"></i> ID: <?= Html::encode($model->id) ?></p>
            <p><i class="bi bi-person-circle"></i> ФИО: <?= Html::encode($model->fio) ?></p>
            <p><i class="bi bi-envelope"></i> Email: <?= Html::encode($model->email) ?></p>
            <p><i class="bi bi-people-fill"></i> Пол: <?= Html::encode($model->gender) ?></p>
            <p><i class="bi bi-telephone"></i> Телефон: <?= Html::encode($model->phone) ?></p>
            <p><i class="bi bi-person-badge"></i> Роль: <?= Html::encode($model->getRoleTitle()) ?></p>
            <p><i class="bi bi-key-fill"></i> Auth Key: <?= Html::encode($model->auth_key) ?></p>
        </div>


  
    </div>



</div>
