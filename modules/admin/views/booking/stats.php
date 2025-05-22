<?php
/** @var string[] $labels */

use yii\bootstrap5\Html;

/** @var int[]    $realFull */
/** @var int[]    $fittedFull */
/** @var int[]    $forecastFull */
/** @var int      $maxCapacity */

\yii\web\JqueryAsset::register($this);
$this->registerJsFile('@web/js/diagram.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
    <?= Html::a(
        Html::img('@web/img/arrow-left.svg', [
            'alt' => 'Назад',
            'style' => 'width: 20px; height: 20px; margin-right: 8px;'
        ]),
        ['/admin'],
        ['class' => 'btn btn-link d-inline-flex align-items-center']
    ) ?>

<div style="max-width:900px; margin:20px auto;">
  <h3 class="text-center">История и прогноз бронирований</h3>
  <canvas id="bookingChart"></canvas>
</div>

<?php
$jsLabels    = json_encode($labels);
$jsReal      = json_encode($realFull);
$jsFitted    = json_encode($fittedFull);
$jsForecast  = json_encode($forecastFull);
$maxY        = $maxCapacity;

$js = <<<JS
const ctx = document.getElementById('bookingChart').getContext('2d');
const isMobile = window.innerWidth < 480;

const chart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: $jsLabels,
    datasets: [
      {
        label: 'Реальные брони',
        data: $jsReal,
        borderColor: 'blue',
        fill: false,
        spanGaps: true,
      },
      {
        label: 'Прогноз (прошлое)',
        data: $jsFitted,
        borderColor: 'green',
        borderDash: [4, 4],
        fill: false,
        spanGaps: true,
      },
      {
        label: 'Прогноз',
        data: $jsForecast,
        borderColor: 'orange',
        borderDash: [5, 5],
        fill: '-1',
        backgroundColor: 'rgba(255,165,0,0.2)',
        spanGaps: true,
      }
    ]
  },
  options: {
    scales: {
      x: {
        title: { display: true, text: 'Дата (день недели)' },
        ticks: {
          autoSkip: true,
          maxRotation: isMobile ? 45 : 0,
          minRotation: isMobile ? 45 : 0,
          display: !isMobile
        }
      },
      y: {
        title: { display: true, text: 'Броней в день' },
        suggestedMax: $maxY
      }
    },
    plugins: {
      legend: { position: 'top' },
      tooltip: { mode: 'index', intersect: false }
    },
    interaction: { mode: 'nearest', axis: 'x', intersect: false }
  }
});
JS;

$this->registerJs($js);

?>
<style>
    /* Контейнер для графика и формы */
.chart-container {
  max-width: 900px;
  margin: 20px auto;
  padding: 0 10px; /* небольшой отступ слева и справа */
  box-sizing: border-box;
}

/* Канвас графика — ширина 100%, высота автоматическая */
#bookingChart {
  width: 100% !important;
  height: auto !important;
  max-height: 400px; /* можно подстроить */
}

/* Форма — flex с переносом */
.form-flex {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
  max-width: 900px;
  margin: 0 auto 20px;
  padding: 0 10px;
  box-sizing: border-box;
}

/* Поля ввода и кнопка адаптивны */
.form-flex input[type=number] {
  flex-shrink: 0;
  width: 100px;
  max-width: 100%;
}

.form-flex label, .form-flex span {
  white-space: nowrap;
}

.form-flex button {
  flex-shrink: 0;
  white-space: nowrap;
}

/* Для очень узких экранов — делаем форму вертикальной */
@media (max-width: 480px) {
  .form-flex {
    flex-direction: column;
    align-items: stretch;
  }
  .form-flex label,
  .form-flex span,
  .form-flex input,
  .form-flex button {
    width: 100%;
    white-space: normal;
  }
}

</style>

<?php
use yii\helpers\Url;
?>

<div class="form-flex">
  <form method="get" action="<?= Url::to(['/admin/booking/stats']) ?>" class="d-flex align-items-center gap-2" style="flex-wrap: wrap;">
    <label for="days">Показать статистику за:</label>
    <input type="number" id="days" name="days" value="<?= Html::encode($daysBack) ?>" min="13" max="60" class="form-control" style="width: 100px;">
    <span>дней</span>
    <button type="submit" class="btn btn-outline-primary">Обновить</button>
  </form>
</div>
