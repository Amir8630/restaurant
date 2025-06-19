<?php
use yii\bootstrap5\Html;
?>

<div class="container py-4">
    <h3 class="mb-4">Панель управления администратора</h3>
    <div class="row gy-3">

        <!-- Карточка загрузки схемы зала -->
        <div class="col-12 col-sm-6 col-md-4">
            <?= Html::a(
                '<div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-upload display-4 mb-3"></i>
                    <h5 class="card-title">Загрузить схему зала</h5>
                </div>',
                ['/manager/svg/upload'],
                [
                    'class' => 'card h-100 text-decoration-none text-center text-light border-0 shadow-sm p-3',
                    'style' => 'transition: transform .2s;',
                    'encode' => false,
                ]
            ) ?>
        </div>

        <!-- Карточка статистики бронирований -->
        <div class="col-12 col-sm-6 col-md-4">
            <?= Html::a(
                '<div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-bar-chart-line display-4 mb-3"></i>
                    <h5 class="card-title">Диаграмма бронирований</h5>
                </div>',
                ['/admin/booking/stats'],
                [
                    'class' => 'card h-100 text-decoration-none text-center text-light border-0 shadow-sm p-3',
                    'style' => 'transition: transform .2s;',
                    'encode' => false,
                ]
            ) ?>
        </div>

        <!-- Карточка управления пользователями -->
        <div class="col-12 col-sm-6 col-md-4">
            <?= Html::a(
                '<div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-people display-4 mb-3"></i>
                    <h5 class="card-title">Работа с пользователями</h5>
                </div>',
                ['/manager/user'],
                [
                    'class' => 'card h-100 text-decoration-none text-center text-light border-0 shadow-sm p-3',
                    'style' => 'transition: transform .2s;',
                    'encode' => false,
                ]
            ) ?>
        </div>

                <!-- Новая карточка загрузки меню -->
        <div class="col-12 col-sm-6 col-md-4">
            <?= Html::a(
                '<div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-file-earmark-text display-4 mb-3"></i>
                    <h5 class="card-title">Загрузить меню</h5>
                </div>',
                ['/menu/index3'],
                [
                    'class' => 'card h-100 text-decoration-none text-center text-light border-0 shadow-sm p-3',
                    'style' => 'transition: transform .2s;',
                    'encode' => false,
                ]
            ) ?>
        </div>
        
    </div>
</div>

<style>
    /* Hover-эффект: чуть увеличиваем карточку */
    .card:hover {
        transform: translateY(-4px) scale(1.02);
    }
    /* Адаптивный фон и текст */
    body {
        background: rgb(44, 62, 80);
    }
    .card {
        background: rgb(44, 62, 80);
    }
    .card-title {
        font-size: 1.1rem;
        margin: 0;
    }
</style>
