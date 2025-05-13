<?php

namespace app\modules\waiter;

use Yii;
use yii\filters\AccessControl;

/**
 * waiter module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\waiter\controllers';

        public function behaviors() 
    { 
        return [ 
            'access' => [ 
                'class' => AccessControl::class, 
                'denyCallback' => fn() => Yii::$app->response->redirect('/'), 
                'rules' => [ 
                    [ 
                        'allow' => true, 
                        'roles' => ['@'],
                        'matchCallback' => fn() => Yii::$app->user->identity->userRole == 'waiter',
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
