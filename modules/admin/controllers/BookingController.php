<?php
namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use DateTime;
use DateInterval;
use DatePeriod;
use Exception;

class BookingController extends Controller
{
    /**
     * Holt–Winters аддитивный: возвращает fitted + forecast (нормированные доли)
     */
    private function hwAdditive(array $dataHist, int $seasonLen, int $fLen, float $alpha, float $beta, float $gamma): array
    {
        $n = count($dataHist);
        if ($n < 2 * $seasonLen) {
            throw new Exception("Нужно минимум " . (2 * $seasonLen) . " точек");
        }

        // инициализация
        $avg1  = array_sum(array_slice($dataHist, 0, $seasonLen)) / $seasonLen;
        $avg2  = array_sum(array_slice($dataHist, $seasonLen, $seasonLen)) / $seasonLen;
        $level = $avg1;
        $trend = ($avg2 - $avg1) / $seasonLen;
        $season = [];
        for ($i = 0; $i < $seasonLen; $i++) {
            $season[$i] = $dataHist[$i] - $avg1;
        }

        // fitted
        $fitted = [];
        for ($t = 0; $t < $n; $t++) {
            if ($t >= $seasonLen) {
                $fitted[] = $level + $trend + $season[$t % $seasonLen];
            } else {
                $fitted[] = $dataHist[$t];
            }
            $prevLevel  = $level;
            $prevSeason = $season[$t % $seasonLen];
            $value      = $dataHist[$t];

            $level = $alpha * ($value - $prevSeason) + (1 - $alpha) * ($prevLevel + $trend);
            $trend = $beta  * ($level - $prevLevel)  + (1 - $beta)  * $trend;
            $season[$t % $seasonLen] = $gamma * ($value - $level) + (1 - $gamma) * $prevSeason;
        }

        // forecast
        $forecast = [];
        for ($m = 1; $m <= $fLen; $m++) {
            $idx = ($n + $m - 1) % $seasonLen;
            $forecast[] = $level + $m * $trend + $season[$idx];
        }

        return ['fitted'=>$fitted, 'forecast'=>$forecast];
    }

    public function actionStats()
    {
        $request = Yii::$app->request;
        $daysBack = (int)$request->get('days', 15); // по умолчанию 15, если не передано

        // Ограничим диапазон, например, 7–60 дней
        if ($daysBack < 7) $daysBack = 7;
        if ($daysBack > 60) $daysBack = 60;

        $hoursOpen   = 23 - 7;
        $slotLen     = 2;
        $slotsPerDay = $hoursOpen / $slotLen;
        $tablesCount = 15;
        $maxCapacity = $slotsPerDay * $tablesCount;

        $dateFrom = (new DateTime())->modify("-{$daysBack} days")->format('Y-m-d');

        // 3. Загружаем брони по дням
        $rows = (new Query())
            ->select(['date'=>'booking_date','cnt'=>'COUNT(*)'])
            ->from('booking')
            ->where(['>=','booking_date',$dateFrom])
            ->groupBy('booking_date')
            ->orderBy('booking_date')
            ->all();

        // 4. Карта дата→кол-во
        $map = [];
        foreach ($rows as $r) {
            $map[$r['date']] = (int)$r['cnt'];
        }

        // 5. Генерим истории дат и значений
        $start  = new DateTime($dateFrom);
        $today  = new DateTime();

        $period = new DatePeriod($start, new DateInterval('P1D'), (clone $today));

        $historicalDates = [];
        $historicalData  = [];
        foreach ($period as $dt) {
            $d = $dt->format('Y-m-d');
            $historicalDates[] = $d;
            $historicalData[]  = $map[$d] ?? 0;
        }

        // 6. Нормируем в доли
        $loadHist = array_map(fn($c) => $c / $maxCapacity, $historicalData);

        // 7. Holt–Winters
        $hw = $this->hwAdditive($loadHist, 7, 7, 0.3, 0.1, 0.1);
        // денормируем и округляем
        $fittedCnt   = array_map(fn($v) => max(0, min($maxCapacity, (int)round($v * $maxCapacity))), $hw['fitted']);
        $forecastCnt = array_map(fn($v) => max(0, min($maxCapacity, (int)round($v * $maxCapacity))), $hw['forecast']);



        // 8. Собираем все метки (30 дней + сегодня + 7 дней вперед)
        $weekDays = [1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб',7=>'Вс'];


$allDates = $historicalDates;
        for ($i=1; $i<=7; $i++) {
            $dt = (new DateTime())->modify("+{$i} days");
            $allDates[] = $dt->format('Y-m-d');
        }
        $labels = array_map(function($d) use ($weekDays) {
            $dt = new DateTime($d);
            return $dt->format('d.m.Y') . ' (' . $weekDays[(int)$dt->format('N')] . ')';
        }, $allDates);

        // // 9. Выравниваем массивы под одну длину
        // $histCount = count($historicalData);
        // $fLen      = count($forecastCnt);

        // $realFull     = array_merge($historicalData, array_fill(0, $fLen, null));
        // $fittedFull   = array_merge($fittedCnt,      array_fill(0, $fLen, null));
        // $forecastFull = array_merge(array_fill(0, $histCount, $fittedCnt), $forecastCnt);

        // 9. Выравниваем массивы под одну длину
        $histCount = count($historicalData);
        $fLen      = count($forecastCnt);

        // Заполняем массивы данными для всех меток
        $realFull     = array_merge($historicalData, array_fill(0, $fLen, 0)); // Для отсутствующих данных ставим 0
        $fittedFull   = array_merge($fittedCnt,      array_fill(0, $fLen, null));
                $realFull     = array_merge($historicalData, array_fill(0, $fLen, 0)); // Для отсутствующих данных ставим 0

        $forecastFull = array_merge(array_fill(0, $histCount, null), $forecastCnt);

        // 10. Рендерим
        return $this->render('stats', [
            'labels'        => $labels,
            'realFull'      => $realFull,
            'fittedFull'    => $fittedFull,
            'forecastFull'  => $forecastFull,
            'maxCapacity'   => $maxCapacity,
            'daysBack'      => $daysBack, // передаём в представление
        ]);
    }
}
