<?php

use yii\helpers\Html;

$this->title = 'Добро пожаловать';

$this->registerCss(<<<CSS
body, html {
    margin: 0!important;
    padding: 0!important;
    height: 100%;
}

.hero-section {
    position: relative;
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center; /* Центрируем hero-content */
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
    margin-bottom: 21vh; /* Чтобы не сдвигался вниз */
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
    position: absolute;
    bottom: 10rem; /* Прижать к низу с небольшим отступом */
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 1.5rem;
    max-width: 65vw;
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(6px);
    padding: 2rem;
    box-sizing: border-box;
    justify-content: center;
    z-index: 2;
    flex-wrap: nowrap; /* Можно поменять на wrap, если нужно */
}

.card-link {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 16px;
    padding: 1rem 2rem;          /* Меньше по вертикали, больше по горизонтали */
    box-shadow: none;
    transition: all 0.3s ease;
    flex: 0 1 500px;             /* Больше ширина, фиксируем */
    min-width: 500px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
    border: 1px solid rgba(255, 255, 255, 0.3);
    min-height: 180px;           /* Минимальная высота для равномерности */
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

@media (max-width: 768px) {
    .hero-section {
        height: auto; /* Убираем фиксированную высоту */
        min-height: 100vh; /* Минимум экран */
        padding-top: 3rem;
        padding-bottom: 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start; /* Контент сверху */
        overflow-y: auto; /* Если будет больше — прокрутка */
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
        position: relative; /* Чтобы не перекрывалось */
        bottom: auto;
        left: auto;
        transform: none;
        max-width: 90vw;
        width: 100%;
        display: flex;
        flex-direction: column; /* вертикально */
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
        min-width: 50px;
        margin-left: 0 !important;
        margin-right: 0 !important;
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
