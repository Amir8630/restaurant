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

$this->title = 'Пользватели';
$this->params['breadcrumbs'][] = $this->title;
// $this->registerJsFile('@web/js/1.js', [
//     'depends' => [\yii\web\JqueryAsset::class],
// ]);
?>
<?php
// ТОЛЬКО ДЛЯ ТЕСТИРОВАНИЯ были добавлены стили для отображения карточек, стили бета
$this->registerCss(<<<CSS
.booking-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    width: 320px;
    margin: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: box-shadow 0.3s ease;
    border: 1px solid #eaeaea;
}
.booking-card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}
.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.booking-id {
    font-weight: 600;
    font-size: 1rem;
}
.booking-card-body p {
    margin: 4px 0;
    font-size: 0.95rem;
}
.booking-card-body i {
    margin-right: 6px;
    color: #6c757d;
}
.booking-card-footer {
    margin-top: 12px;
}
.pagination {
    margin-top: 20px;
    gap: 6px;
    justify-content: center;
    text-align: center;
}

.page-item {
    display: inline-block;
}

.page-link {
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 8px;
}

@media (max-width: 576px) {
    .page-link {
        padding: 4px 8px; /* Reduced padding for smaller screens */
        font-size: 12px; /* Smaller font size for better fit */
    }
}

@media (max-width: 576px) {
    .responsive-pager {
        text-align: center;
    }

    .responsive-pager .page-item {
        display: none;
    }

    .responsive-pager .page-item:first-child,
    .responsive-pager .page-item.active,
    .responsive-pager .page-item:last-child,
    .responsive-pager .page-item:nth-child(n+2):nth-child(-n+4),
    .responsive-pager .page-item:nth-last-child(n+2):nth-last-child(-n+4) {
        display: inline-block;
    }
}


CSS);

?>
<div class="booking-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Зарегистрировать пользователя', ['create'], ['class' => 'btn btn-outline-success']) ?>
    </p>

    <?php Pjax::begin([
        'id' => 'pjax-booking-index',
        // 'enablePushState' => false,
        'timeout' => 5000
          ]); ?> 
    
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

<?php $this->registerJsFile('js/userFilter.js', ['depends' => YiiAsset::class]) ?>


