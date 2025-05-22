<?php

use yii\helpers\Html;

$this->title = 'Добро пожаловать';

$this->registerCss(<<<CSS
.hero-section {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: url('/img/hero-bg.png') center/cover no-repeat;
    position: relative;
    text-align: center;
    color: white;
}

.hero-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(3px);
}

.hero-content {
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.1);
    padding: 2rem;
    border-radius: 16px;
    backdrop-filter: blur(6px);
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

.card-link {
    transition: all 0.3s;
}
.card-link:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(255,255,255,0.1);
}
CSS);
?>

<div class="hero-section">
  <div class="hero-content">
    <h1>Добро пожаловать в наш ресторан</h1>
    <p>Где вкус сочетается с уютом и современным сервисом</p>
    <?= Html::a('Посмотреть меню', ['/menu/index3'], ['class' => 'btn btn-outline-light']) ?>
  </div>
</div>

<div class="container py-5">
  <div class="row g-4 text-center">
    <div class="col-md-4">
      <div class="card bg-dark text-white h-100 card-link">
        <div class="card-body">
          <h5 class="card-title">Меню</h5>
          <p class="card-text">Ознакомьтесь с нашими блюдами</p>
          <?= Html::a('Перейти', ['/menu/index3'], ['class' => 'btn btn-outline-light']) ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card bg-dark text-white h-100 card-link">
        <div class="card-body">
          <h5 class="card-title">Бронирование</h5>
          <p class="card-text">Забронируйте столик онлайн</p>
          <?= Html::a('Бронировать', ['/account/booking'], ['class' => 'btn btn-outline-light']) ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card bg-dark text-white h-100 card-link">
        <div class="card-body">
          <h5 class="card-title">Контакты</h5>
          <p class="card-text">Как нас найти и связаться</p>
          <?= Html::a('Контакты', ['/site/contact'], ['class' => 'btn btn-outline-light']) ?>
        </div>
      </div>
    </div>
  </div>
</div>
