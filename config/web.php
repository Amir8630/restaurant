<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'name' => 'Diplom',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
         'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'locale' => 'ru-RU',
        'dateFormat' => 'php:d.m.Y',
        'datetimeFormat' => 'php:d.m.Y H:i',
        'timeFormat' => 'php:H:i',
        'defaultTimeZone' => 'Europe/Moscow',
    ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'zxczxc',
            'baseUrl' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'scheme' => 'smtps',
                'host' => 'smtp.mail.ru',
                'username' => 'restaurant.project@mail.ru',
                'password' => 'Zy1uCqmGB2pkBdr35JE6',
                'port' => 465,
                // 'dsn' => 'native://default',
                // 'encryption' => 'tls',
                'options' => [
                    'ssl' => true
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'account' => [
            'class' => 'app\modules\account\Module',
            'defaultRoute' => 'booking/index',
        ],
        'waiter' => [
            'class' => 'app\modules\waiter\Module',
        ],
        'cook' => [
            'class' => 'app\modules\cook\Module',
            'defaultRoute' => 'order/index',
        ],
        'manager' => [
            'class' => 'app\modules\manager\Module',
        ],
    ],
    'params' => $params,
    'timeZone' => 'Europe/Moscow', // Замените на вашу временную зону

];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
