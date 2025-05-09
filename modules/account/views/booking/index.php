<?php

use app\models\Booking;
use app\widgets\Alert;
use yii\bootstrap5\LinkPager;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\web\YiiAsset;
use yii\widgets\ListView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\account\models\BookingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Брони';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Забронировать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-booking-index', 'enablePushState' => false, 'timeout' => 5000]); ?> 
    
    <?= Alert::widget() ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => 'item',
        'layout' => "{pager}\n{summary}\n<div class = 'd-flex flex-wrap justify-content-center gap-3'>{items}</div>\n<div class = 'mt-2'></div>{pager}",
        'pager' => ['class' => LinkPager::class],

    ]) ?>

    <?php Pjax::end(); ?>

</div>

<?= $this->render('modal', ['pjax' => '#pjax-booking-index']) ?>

