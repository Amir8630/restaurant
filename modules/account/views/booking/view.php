<?php

use app\models\BookingTable;
use app\models\Status;
use yii\bootstrap5\Html;
use yii\helpers\VarDumper;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Booking $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Брони', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="booking-view" data-booking-id="<?= $model->id ?>">

    <h3><?= Html::encode('Бронь №' . $this->title) ?></h3>

    <p>
        <?= Html::a('Назад', ['index'], ['class' => 'btn btn-outline-primary']) ?>
        <?= $model->status_id == Status::getStatusId('Забронировано') ? Html::a('Отменить', ['cancel', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger',
            'data' => [
                'confirm' => 'Вы уверены?',
                'method' => 'post',
            ],
        ])
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
            [
                'attribute' => 'booking_time_start',
                'format' => ['datetime', 'php:H:i:s'],
            ],
            [
                'attribute' => 'booking_time_end',
                'format' => ['datetime', 'php:H:i:s'],
            ],
            [
                'attribute' => 'status_id',
                'value' => $model->status->title,
            ],
            'count_guest',
            'phone',
            'email:email',

            //нужно сделать проверку статуса, видны столы только со статусом актив
            //в идеале сделать чтобы поля обновлялис как в корзине с + и -
            // [
            //     'attribute' => 'номер столика',
            //     'value' => function ($model) {
            //         $tables = BookingTable::findAll(['booking_id' => $model->id]);                    

            //         $idTables = array_map(function($table) {
            //             return $table->table_id;
            //         }, $tables);

            //         // Генерируем HTML-код для div-элементов
            //         $divsHtml = '<div style="display: flex; justify-content: center; flex-wrap: wrap;">'; 

            //         foreach ($idTables as $idTable) {
            //             // Создаем div для каждого столика
            //             $divsHtml .= '<div id = "table'. $idTable .'"class="btn btn-outline-info m-2 selectedDiv divTable" style="width: 38%; height: 10%;border: 1px solid #ccc; display: inline-block; margin-right: 5px; text-align: center; line-height: 50px;">стол ' . $idTable . '</div>';
            //         }
            //         return $divsHtml .= '</div>';
            //     },
            //     'format' => 'raw' // Убедитесь, что формат установлен на 'raw', чтобы HTML-код отображался правильно
            // ],

            [
                'attribute' => 'номер столика',
                'value' => function ($model) {
                    $tables = BookingTable::findAll(['booking_id' => $model->id]);
                    $divsHtml = '<div style="display: flex; justify-content: center; flex-wrap: wrap;">';
                    $totalTime = 10; // время в секундах

                    foreach ($tables as $table) {
                        $classes = 'btn btn-outline-info m-2 selectedDiv divTable';
                        $inlineFiller = '';

                        if ($table->delete_started_at) {
                            $classes .= ' disabledTable';
                            $startTime = strtotime($table->delete_started_at);
                            $now = time();
                            $elapsed = $now - $startTime;

                            // var_dump($table->delete_started_at);
                            // var_dump($startTime);
                            // var_dump($elapsed);
                            // var_dump($elapsed >= $totalTime);
                            // die;

                            if ($elapsed >= $totalTime) {

                                // Фикс: обновляем is_deleted = 1 в БД, если время истекло
                                // $table->is_deleted = 1;
                                $table->save(false);
                                $classes .= ' disabledTable';
                                $inlineFiller = '<div class="red-filler" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; background-color: red; z-index: -1;"></div>';
                            } else {

                                $classes .= ' pendingDelete';
                                $currentPercent = ($elapsed / $totalTime) * 100;
                                $remainingTime = $totalTime - $elapsed;

                                $animationStyle = $remainingTime > 0
                                    ? "animation: fillRedAnimation {$remainingTime}s linear forwards;"
                                    : '';

                                $inlineFiller = '<div class="red-filler" style="position: absolute; bottom: 0; left: 0; width: 100%; height: '
                                    . $currentPercent . '%; background-color: red; z-index: -1; ' . $animationStyle . '"></div>';
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