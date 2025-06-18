<?php

use yii\helpers\Html;

$this->title = 'Добро пожаловать';

$this->registerCss(<<<CSS
body, html {
    margin: 0!important;
    padding: 0!important;
    height: 100%;
}

body{
     overflow-x: hidden;
}

.hero-section {
    position: relative;
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: url('/img/hero-bg.png') center/cover no-repeat;
    color: white;
    overflow: hidden;
    margin-left: calc(-50vw + 50%);
    padding-top: 5vh;
    padding-bottom: 5vh;
}

.hero-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(3px);
    z-index: 0;
}

.hero-content {
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.1);
    padding: 2rem;
    border-radius: 16px;
    backdrop-filter: blur(6px);
    max-width: 600px;
    margin-bottom: 10vh;
    text-align: center;
}

.hero-content h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
}

.hero-cards-container {
    position: relative; /* было: absolute */
    margin-top: 2rem; /* добавим отступ после .hero-content */
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    max-width: 90vw;
    width: auto;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(6px);
    padding: 2rem;
    box-sizing: border-box;
    justify-content: center;
    z-index: 2;
}


.card-link {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 16px;
    padding: 1rem 2rem;
    box-shadow: none;
    transition: all 0.3s ease;
    flex: 1 1 calc(33.333% - 2rem); /* 3 карточки в ряд */
    min-width: 270px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
    border: 1px solid rgba(255, 255, 255, 0.3);
    min-height: 180px;
    height: 100%; добавим
}

.card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}
.card-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-5px);
}

.card-link .card-title {
    color: white;
    font-weight: bold;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.card-link .card-text {
    color: white;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}

.card-link .btn {
    align-self: flex-start;
    border-color: white;
    color: white;
}

.card-link .btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    border-color: white;
}

/* 2 карточки в ряд для планшетов */
@media (max-width: 1100px) {
    .card-link {
        flex: 1 1 calc(50% - 2rem);
    }
}

/* 1 карточка в ряд для телефонов */
@media (max-width: 768px) {
    .hero-section {
        height: auto;
        min-height: 100vh;
        padding-top: 3rem;
        padding-bottom: 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        overflow-y: auto;
        background-size: cover;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 90vw;
        padding: 1.5rem;
        margin-top: 3rem;
        margin-bottom: 3rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        backdrop-filter: blur(6px);
        text-align: center;
        box-sizing: border-box;
    }

    .hero-cards-container {
        position: relative;
        bottom: auto;
        left: auto;
        transform: none;
        width: 100%;
        min-width: 65vw;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
        padding: 1rem;
        box-sizing: border-box;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        backdrop-filter: blur(6px);
        z-index: 1;
    }

    .card-link {
        flex: none;
        width: 100%;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.1);
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
        transition: all 0.3s ease;
        margin-left: 0 !important;
        margin-right: 0 !important;
        min-height: 180px; /* Добавлено для одинаковой высоты */
    }

    .card-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .card-link .card-title {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 0.75rem;
        color: white;
    }

    .card-link .card-text {
        font-size: 1rem;
        margin-bottom: 1rem;
        flex-grow: 1;
        color: white;
    }

    .card-link .btn {
        align-self: flex-start;
        border-color: white;
        color: white;
    }

    .card-link .btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border-color: white;
    }
}
CSS);
?>

<div class="hero-section">
  <div class="hero-content">
    <h1>Добро пожаловать в наш ресторан</h1>
    <p>Где вкус сочетается с уютом и современным сервисом</p>
    <?= Html::a('Посмотреть меню', ['/menu/index3'], ['class' => 'btn btn-outline-light']) ?>
  </div>

  <div class="hero-cards-container">
    <div class="card card-link">
      <div class="card-body">
        <h5 class="card-title">Меню</h5>
        <p class="card-text">Ознакомьтесь с нашими блюдами</p>
        <?= Html::a('Перейти', ['/menu/index3'], ['class' => 'btn btn-outline-light']) ?>
      </div>
    </div>

    <div class="card card-link">
      <div class="card-body">
        <h5 class="card-title">Бронирование</h5>
        <p class="card-text">Забронируйте столик онлайн</p>
        <?= Html::a('Бронировать', ['/account/booking'], ['class' => 'btn btn-outline-light']) ?>
      </div>
    </div>

    <div class="card card-link">
      <div class="card-body">
        <h5 class="card-title">Контакты</h5>
        <p class="card-text">Как нас найти и связаться</p>
        <?= Html::a('Контакты', ['/site/contact'], ['class' => 'btn btn-outline-light']) ?>
      </div>
    </div>
  </div>
</div>
