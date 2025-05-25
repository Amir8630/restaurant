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

$this->title = 'Бронь №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Брони', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


$this->registerCss(<<<CSS
body {
    background-color: #1e2227;
}

.booking-card {
    background-color: #232b38;
    border: 1px solid #3e4a55;
    border-radius: 16px;
    padding: 25px;
    max-width: 800px;
    margin: 30px auto;
    color: #f8f9fa;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    font-family: 'Segoe UI', sans-serif;
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 20px;
}

.status-new {
    color: #9b59b6;
    padding: 4px 10px;
    border-radius: 6px;
}

.status-completed {
    color: #7bed9f;
    padding: 4px 10px;
    border-radius: 6px;
}

.status-canceled {
    color: #ff6b81;
    padding: 4px 10px;
    border-radius: 6px;
}

.booking-card-body p {
    margin: 8px 0;
    font-size: 15px;
    color: #d3d3d3;
}

.booking-card-tables {
    margin-top: 20px;
    text-align: center;
}

.divTable {
    display: inline-block;
    margin: 10px;
    padding: 10px 20px;
    border: 1px solid #70a1ff;
    border-radius: 10px;
    background-color: #1e1e1e;
    position: relative;
    color: #a8ffb2;
}

.divTable.disabledTable {
    opacity: 0.5;
    pointer-events: none;
}
.table.table-darks th ,td{
    background-color: #232b38 !important;
    color: #d3d3d3;
    border-color: #3e4a55 !important; /* можно оставить или убрать */
}
td {
    color: #d3d3d3 !important;
}


.booking-card-footer {
    margin-top: 20px;
    display: flex;
    justify-content: flex-start;
    gap: 10px;
}

.btn-sm {
    font-size: .85rem;
    padding: .4rem .8rem;
}

.btn-outline-light {
    border-color: #70a1ff;
    color: #70a1ff;
}

.btn-outline-light:hover {
    background: #70a1ff;
    color: #232b38;
}


.table-status {
    font-weight: bold;
    font-size: 1.2rem;
    display: inline-block;
}
CSS
);
?>

<div class="booking-view booking-card" data-booking-id="<?= $model->id ?>">
    <div class="booking-card-header">
        <span><?= Html::encode($this->title) ?></span>
<span>
    <?php
    // Сопоставляем id статуса к классам с простым цветом текста, как во втором примере
    $statusMap = [
        Status::getStatusId('Отменён')      => 'status-canceled',
        Status::getStatusId('Забронировано') => 'status-completed',
        // Если есть другие статусы - добавь сюда
    ];
    $cls = $statusMap[$model->status_id] ?? 'status-new';

    // Просто выводим цветной текст без фона и отступов
    echo '<span class="' . $cls . '">' . Html::encode($model->status->title) . '</span>';
    ?>
</span>
    </div>

    <div class="booking-card-body">

        <div class="btn-group mb-3" id="booking-view">  
           
                <?= Html::a('<i class="bi bi-arrow-left"></i> Назад', ['index'], ['class' => 'btn btn-outline-primary btn-sm']) ?>  
    
                <?= $model->status_id == Status::getStatusId('Забронировано') 
                ? Html::a('Отменить', ['cancel', 'id' => $model->id], ['class' => 'btn btn-outline-danger btn-cancel-modal btn-sm', 'data-number' => $model->id,])
                :'' ?>
         
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-darks'], // убрал table-striped
            'attributes' => [
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
                'booking_time_end',
                'count_guest',
                'phone',
                [
                    'attribute' => 'email',
                    'format' => 'raw',
                    'value' => Html::encode($model->email),
                ],
              [
    'attribute' => 'номер столика',
    'value' => function ($model) {
        $tables = BookingTable::findAll(['booking_id' => $model->id]);
        $divsHtml = '<div style="display: flex; justify-content: center; flex-wrap: wrap;">';
        $totalTime = 20; // Время "заполнения" в секундах

        foreach ($tables as $table) {
            $classes = 'btn btn-outline-info m-2 selectedDiv divTable';
            $inlineFiller = '';
                if ($model->status_id == 15) {
                    $classes .= ' disabledTable';
                }
            if ($table->delete_started_at) {
                $startTime = strtotime($table->delete_started_at);
                $now = time();
                $elapsed = $now - $startTime;

                if ($elapsed >= $totalTime) {
                    // Удаление завершено — заливка полная, столик отключён
                    $classes .= ' disabledTable';
                    $inlineFiller = '<div class="red-filler" style="position:absolute; bottom:0; left:0; width:100%; height:100%; background-color:red; z-index:-1;"></div>';
                } else {
                    // В процессе удаления — показать текущий прогресс
                    $percent = ($elapsed / $totalTime) * 100;
                    $remaining = $totalTime - $elapsed;

                    $classes .= ' pendingDelete';
                    
                    $inlineFiller = '<div class="red-filler" style="position:absolute; bottom:0; left:0; width:100%; height:' . $percent . '%; background-color:red; z-index:-1;';
                    
                    // Только если пользователь открыл страницу впервые — включаем анимацию
                    $inlineFiller .= ' animation: fillRedAnimation ' . $remaining . 's linear forwards;';
                    $inlineFiller .= '" onanimationend="this.parentNode.classList.add(\'disabledTable\');"></div>';
                }
            }

            $divsHtml .= '<div id="table' . $table->table_id . '" class="' . $classes . '" style="width:38%; height:60px; text-align:center; line-height:60px; position:relative;">' . $inlineFiller . 'стол ' . $table->table_id . '</div>';
        }

        $divsHtml .= '</div>';
        return $divsHtml;
    },
    'format' => 'raw'
],

            ],
        ]) ?>
    </div>
</div>

<?= $this->registerJsFile('/js/cancelTable.js', ['depends' => YiiAsset::class]); ?>
<?= $this->render('modal', ['number' => $model->id]) ?>

<?php if (Yii::$app->request->get('sendMail')): ?>
<script>
    fetch('<?= Url::to(['booking/mail', 'id' => $model->id]) ?>').then(() => console.log('Mail sent'));
</script>
<?php endif; ?>

<?php if (Yii::$app->request->get('sendMailCancel')): ?>
<script>
    fetch('<?= Url::to(['booking/mail-cancel', 'id' => $model->id]) ?>').then(() => console.log('Mail sent'));
</script>
<?php endif; ?>


<?php
\yii\bootstrap5\Modal::begin([
    'id' => 'tableDeleteModal',
    'title' => '<span id="tableDeleteModalTitle">Удалить стол?</span>',
    'footer' => '
        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-danger" id="confirmTableDeleteBtn">Удалить</button>
    ',
]);
?>

<p id="tableDeleteModalText">Вы точно хотите удалить этот стол из брони?</p>

<?php \yii\bootstrap5\Modal::end(); ?>