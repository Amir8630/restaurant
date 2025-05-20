<?php
/** @var yii\web\View $this */
$this->title = 'Контакты';
?>
<div class="container-lg py-5">
  <div class="text-center mb-5">
    <h1 class="display-5 fw-semibold">Свяжитесь с нами</h1>
    <p class="text-muted">Любой способ — мы всегда отвечаем</p>
  </div>

  <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
    <div class="row gy-4 justify-content-center">
      <div class="col-md-6 col-lg-4 text-center">
        <i class="bi bi-telephone fs-1 text-primary"></i>
        <h5 class="fw-bold mt-3 mb-1">Телефон</h5>
        <p class="mb-0 fs-5">+7 (812) 555‑12‑34</p>
        <small class="text-muted">10:00 – 22:00 ежедневно</small>
      </div>
      <div class="col-md-6 col-lg-4 text-center">
        <i class="bi bi-envelope fs-1 text-primary"></i>
        <h5 class="fw-bold mt-3 mb-1">Email</h5>
        <p class="mb-0 fs-5">support@restomanager.ru</p>
        <small class="text-muted">Ответ в течение 24 часов</small>
      </div>
      <div class="col-md-6 col-lg-4 text-center">
        <i class="bi bi-geo-alt fs-1 text-primary"></i>
        <h5 class="fw-bold mt-3 mb-1">Адрес</h5>
        <p class="mb-0 fs-5">Невский проспект 1</p>
        <small class="text-muted">м. Адмиралтейская</small>
      </div>
    </div>

    <hr class="my-5">

        <div class="text-center">
            <h2 class="h5 fw-bold mb-3">Мы в социальных сетях</h2>

            <a href="#" class="btn btn-link fs-2 mx-2 social-link"><i class="bi bi-telegram"></i></a>
            <a href="#" class="btn btn-link fs-2 mx-2 social-link"><i class="bi bi-whatsapp"></i></a>
            <a href="#" class="btn btn-link fs-2 mx-2 align-middle social-link" style="vertical-align: middle;"><i class="bi bi-youtube" style="vertical-align: middle;"></i></a>
        </div>
      </div>
    </div>
    
    <style>
    .social-link i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f1f3f5;
        font-size: 1.25rem;
        transition: transform .3s, box-shadow .3s, background .3s, color .3s;
    }

    .social-link:hover i {
        transform: translateY(-6px) scale(1.2);
        background: #e0e7ff;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }
    </style>