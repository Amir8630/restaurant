
<?php
/** @var yii\web\View $this */
$this->title = 'Условия использования';
?>
<div class="container-lg py-5">
  <div class="text-center mb-5">
    <h1 class="display-5 fw-semibold">Условия использования</h1>
    <p class="text-muted">Обновлено 15 мая 2025 г.</p>
  </div>

  <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
    <section class="mb-5">
      <h2 class="h5 fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>1. Общие положения</h2>
      <p>Используя ИС ресторана, вы соглашаетесь с данными Условиями. Если вы не согласны — пожалуйста, не используйте сервис.</p>
    </section>

    <section class="mb-5">
      <h2 class="h5 fw-bold mb-3"><i class="bi bi-check2-circle me-2"></i>2. Права пользователя</h2>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="p-4 rounded-4 h-100 text-center feature-box">
            <i class="bi bi-calendar2-check fs-1 mb-2"></i>
            <p class="mb-0 fw-semibold">Онлайн‑бронирование</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 rounded-4 h-100 text-center feature-box">
            <i class="bi bi-receipt fs-1 mb-2"></i>
            <p class="mb-0 fw-semibold">Просмотр статуса бронирования</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 rounded-4 h-100 text-center feature-box">
            <i class="bi bi-x-circle fs-1 mb-2"></i>
            <p class="mb-0 fw-semibold">Отмена брони за 2 ч.</p>
          </div>
        </div>
      </div>
    </section>

    <style>
    .feature-box {
      background: #f8f9fa;
      border: 2px solid #495057; /* Более заметная окантовка */
      box-shadow: 0 2px 12px rgba(0,0,0,.10); /* Добавлена базовая тень */
      transition: transform .3s, box-shadow .3s, border-color .3s;
    }
    .feature-box:hover {
      transform: translateY(-6px) scale(1.03);
      box-shadow: 0 1.5rem 2rem rgba(0,0,0,.20); /* Более выраженная тень при наведении */
      border-color: #212529; /* Еще более заметная окантовка при наведении */
    }
    </style>

    <section class="mb-5">
      <h2 class="h5 fw-bold mb-3"><i class="bi bi-list-check me-2"></i>3. Обязанности пользователя</h2>
      <p>Предоставлять актуальные контактные данные и соблюдать правила поведения в ресторане.</p>
    </section>

    <section class="mb-4">
      <h2 class="h5 fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2"></i>4. Ограничение ответственности</h2>
      <p>Мы стремимся обеспечивать бесперебойную работу сервиса, но не несём ответственность за сбои, вызванные провайдерами связи.</p>
    </section>

    <p class="text-muted small">Полная версия условий будет опубликована после юридической проверки.</p>
  </div>
</div>
