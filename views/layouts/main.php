<?php
use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/site.css') ?>">
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img(
        Yii::getAlias('@web') . '/img/nice_logo2nn.svg',
        ['alt' => Yii::$app->name, 'height' => '40']  // подбери высоту по дизайну
    ),
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => ['class' => 'navbar navbar-expand-md navbar-dark custom-navbar fixed-top'],
    ]);
    echo Nav::widget([
        'options'         => ['class' => 'navbar-nav title'],
        'activateItems'   => true,
        'items'           => [
            ['label' => 'Меню', 'url' => ['/menu/index3']],
            // ['label' => 'книга2', 'url' => ['/menu/index2']],
            // ['label' => 'книга',  'url' => ['/menu/index']],
            !Yii::$app->user->isGuest && Yii::$app->user->identity->userRole == 'cook'
                ? ['label'  => 'Панель Повара','url' => ['/cook/order'],'active' => Yii::$app->controller->module?->id === 'cook']
                : '',
            !Yii::$app->user->isGuest && Yii::$app->user->identity->userRole == 'manager'
                ? ['label'  => 'Панель менеджера','url' => ['/manager/default/index'],'active' => Yii::$app->controller->module?->id === 'manager']
                : '',
            !Yii::$app->user->isGuest && Yii::$app->user->identity->userRole == 'waiter'
                ? ['label' => 'ЛК официанта', 'url' => ['/waiter/order'],'active' => Yii::$app->controller->module?->id === 'waiter']
                : '',
            !Yii::$app->user->isGuest && Yii::$app->user->identity->userRole == 'user'
                ? ['label' => 'Брони', 'url' => ['/account/booking'],'active' => Yii::$app->controller->module?->id === 'account']
                : '',
            !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
                ? ['label' => 'Панель админа', 'url' => ['/admin'],'active' => Yii::$app->controller->module?->id === 'admin' || Yii::$app->controller->module?->id === 'manager']
                : '',
            // ['label' => 'test', 'url' => ['/site/test']],
            Yii::$app->user->isGuest
                ? ['label' => 'Регистрация', 'url' => ['/site/register']]
                : '',
            Yii::$app->user->isGuest
                ? ['label' => 'Авторизация', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Выход (' . Yii::$app->user->identity->email . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
        ],
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <?php if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'index'): ?>
        <div class="container pt-5 mt-4">
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>
</main>

<footer id="footer" class="mt-auto custom-footer py-4 title">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-3 mb-md-0 text-center text-md-start">
            &copy; <?= date('Y') ?> Diplom. Все права защищены.
        </div>
        <div class="text-center text-md-end">
        <?= Html::a(
    'Политика конфиденциальности',
    ['/site/privacy'],
    [
        'class' => 'text-link me-3' . (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'privacy' ? ' active' : ''),
    ]
) ?>
<?= Html::a(
    'Условия использования',
    ['/site/terms'],
    [
        'class' => 'text-link me-3' . (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'terms' ? ' active' : ''),
    ]
) ?>
<?= Html::a(
    'Контакты',
    ['/site/contact'],
    [
        'class' => 'text-link' . (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'contact' ? ' active' : ''),
    ]
) ?>

        </div>
    </div>
</footer>
<div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?php $this->endBody() ?>
</body>
</html>
<?php

$this->registerJs("
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 3000);
");
?>
<?php $this->endPage() ?>


