<?php

use app\models\BookingTable;
use app\models\Status;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\helpers\VarDumper;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Booking $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Брони', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<!-- стили бета + ссылка надо убрать -->
<?php
$this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<?php
$this->registerCss(<<<CSS
.booking-view {
    background-color: #ffffff;
    border-radius: 16px;
    padding: 30px;
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    font-family: 'Segoe UI', sans-serif;
    color: #2c3e50;
}

.booking-view h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 25px;
    text-align: center;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

#booking-view {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.table-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9em;
}

.status-booked { background-color: #3498db; color: white; }
.status-cancelled { background-color: #e74c3c; color: white; }
.status-completed { background-color: #2ecc71; color: white; }

.divTable.disabledTable {
    opacity: 0.5;
    pointer-events: none;
}
/* мусор из за которого и не работает щаполнение квадрата столика для его отмены */
/* 
.table-wrapper {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.divTable {
    width: 130px;
    height: 60px;
    background-color: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    text-align: center;
    line-height: 60px;
    font-weight: 600;
    color: #34495e;
    position: relative;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.divTable:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.divTable.disabledTable {
    background-color: #f1f1f1;
    color: #aaa;
    border-style: dashed;
}

.red-filler {
    border-radius: 12px;
    opacity: 0.4;
}

@keyframes fillRedAnimation {
    0% { height: 0%; }
    100% { height: 100%; }
} */
CSS);
?>


<div class="booking-view" data-booking-id="<?= $model->id ?>">

    <h3><?= Html::encode('Бронь №' . $this->title) ?></h3>
     <div class="btn-group mb-2">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Назад', ['index'], ['class' => 'btn btn-outline-primary']) ?>
            <?= $model->status_id == Status::getStatusId('Забронировано') 
                ? Html::a('Отменить', ['cancel', 'id' => $model->id], ['class' => 'btn btn-outline-danger btn-cancel-modal']) 
                : ''?>
        </div>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fio_guest',
            [
                'attribute' => 'user_id',
                'value' => $model->user->fio,
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i:s'],
            ],
            [
                'attribute' => 'booking_date',
                'format' => ['datetime', 'php:d.m.Y'],
            ],
            'booking_time_start',

            //В таком формат вреия не праильное оно посто другое надо проверить часовой пояс 
            // [
            //     'attribute' => 'booking_time_start',
            //     'format' => ['datetime', 'php:H:i:s'],
            // ],
            'booking_time_end',

            // [
            //     'attribute' => 'booking_time_end',
            //     'format' => ['datetime', 'php:H:i:s'],
            // ],
          [
    'attribute' => 'status_id',
    'format' => 'raw',
    'value' => function ($model) {
        $status = $model->status->title;
        $class = match ($status) {
            'Забронировано' => 'status-booked',
            'Отменено' => 'status-cancelled',
            'Завершено' => 'status-completed',
            default => '',
        };
        return "<span class='table-status {$class}'>" . Html::encode($status) . "</span>";
    },
],
            'count_guest',
            'phone',
            'email:email',
            [
                'attribute' => 'номер столика',
                'value' => function ($model) {
                    $tables = BookingTable::findAll(['booking_id' => $model->id]);
                    $divsHtml = '<div style="display: flex; justify-content: center; flex-wrap: wrap;">';
                    $totalTime = 50; // время в секундах
            
                    foreach ($tables as $table) {
                        $classes = 'btn btn-outline-info m-2 selectedDiv divTable';
                        $inlineFiller = '';
            
                        if ($table->delete_started_at) {
                            $startTime = strtotime($table->delete_started_at);
                            $now = time();
                            $elapsed = $now - $startTime;
            
                            if ($elapsed >= $totalTime) {
                                // Если прошло 50 секунд или более – сразу добавляем класс disabledTable
                                $classes .= ' disabledTable';
                                $inlineFiller = '<div class="red-filler" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; background-color: red; z-index: -1;"></div>';
                            } else {
                                // Если прошло меньше 50 секунд – добавляем pendingDelete и анимируем заполнение
                                $classes .= ' pendingDelete';
                                $currentPercent = ($elapsed / $totalTime) * 100;
                                $remainingTime = $totalTime - $elapsed;
                                $animationStyle = "animation: fillRedAnimation {$remainingTime}s linear forwards;";
                                // onanimationend добавит класс disabledTable родительскому элементу (т.е. самому диву столика)
                                $inlineFiller = '<div class="red-filler" style="position: absolute; bottom: 0; left: 0; width: 100%; height: '
                                    . $currentPercent . '%; background-color: red; z-index: -1; ' . $animationStyle . '" onanimationend="this.parentNode.classList.add(\'disabledTable\');"></div>';
                            }
                        }
            
                        $divsHtml .= '<div id="table' . $table->table_id . '" class="' . $classes . '" 
                                      style="width: 38%; height: 60px; border: 1px solid #ccc; display: inline-block; margin: 5px; text-align: center; line-height: 60px; position: relative;">'
                            . $inlineFiller
                            . 'стол ' . $table->table_id . '</div>';
                    }
            
                    return $divsHtml . '</div>';
                },
                'format' => 'raw'
            ],


        ],
    ]) ?>

</div>

<?= $this->registerJsFile('/js/cancelTable.js', ['depends' => YiiAsset::class]); ?>

<?= $this->render('modal', ['number' => $model->id]) ?>


<!-- Отправка почты в фоне после actionCreate -->
<?php if (Yii::$app->request->get('sendMail')): ?>
    
<script>
    // Отправка почты в фоне (не блокирует отображение страницы)
    fetch('<?= Url::to(['booking/mail', 'id' => $model->id]) ?>')
      .then(res => console.log('Mail sent'));
    // Или: navigator.sendBeacon('<= Url::to(['booking/mail', 'id' => $model->id]) ?>');

// вариант с ajax, работает асинхронно но при изминении url ошибка
// <php $mailUrl = Url::to(['/account/booking/mail', 'id' => $model->id]); ?>
// $.ajax({
//     url: '<= $mailUrl ?>',
//     async: true,
//     type: 'POST',
//     dataType: 'json',
//     data: {
//         _csrf: '<= Yii::$app->request->getCsrfToken() ?>'
//     },
//     success: function(res) {
//         if (res.status === 'success') {
//             console.log('Письмо отправлено');
//         } else {
//             console.warn('Ошибка при отправке');
//         }
//     },
//     error: function() {
//         console.error('Не удалось вызвать actionMail');
//     }
// });
</script>
<?php endif; ?>


<!-- Отправка почты в фоне после actionCancel -->
<?php if (Yii::$app->request->get('sendMailCancel')): ?>
    
<script>
    // Отправка почты в фоне (не блокирует отображение страницы)
    fetch('<?= Url::to(['booking/mail-cancel', 'id' => $model->id]) ?>')
      .then(res => console.log('Mail sent'));
    // Или: navigator.sendBeacon('<= Url::to(['booking/mail', 'id' => $model->id]) ?>');
</script>
<?php endif; ?>


