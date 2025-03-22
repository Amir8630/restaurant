<?php

namespace app\modules\account;

use Yii;
use yii\filters\AccessControl;

/**
 * account module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\account\controllers';

    public function behaviors() 
    { 
        return [ 
            'access' => [ 
                'class' => AccessControl::class, 
                'denyCallback' => fn() => Yii::$app->response->redirect('/'), 
                'rules' => [ 
                    [ 
                        'allow' => true, 
                        'controllers' => ['account/booking'],
                        'actions' => ['mail-view'], 
                        'roles' => ['@'], 
                        'matchCallback' => fn() => Yii::$app->user->isGuest || Yii::$app->user->identity->userRole == 'user', 
                    ],
                    [ 
                        'allow' => true, 
                        'roles' => ['@'], 
                        'matchCallback' => fn() => Yii::$app->user->identity->userRole == 'user',
                    ], 
                ], 
            ], 
        ]; 
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
