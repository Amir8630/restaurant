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
<div class="booking-view" data-booking-id="<?= $model->id ?>">

    <h3><?= Html::encode('Бронь №' . $this->title) ?></h3>

    <p id="booking-view">
        <?= Html::a('Назад', ['index'], ['class' => 'btn btn-outline-primary']) ?>
        <?= $model->status_id == Status::getStatusId('Забронировано') 
        ? Html::a('Отменить', ['cancel', 'id' => $model->id], ['class' => 'btn btn-outline-danger btn-cancel-modal'])
        :'' ?>
    </p>

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
                'value' => $model->status->title,
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
</script>
<?php endif; ?>


