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
new Chart(ctx, {
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
        borderDash: [4,4],
        fill: false,
        spanGaps: true,
      },
      {
        label: 'Прогноз',
        data: $jsForecast,
        borderColor: 'orange',
        borderDash: [5,5],
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
        ticks: { autoSkip: false }
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