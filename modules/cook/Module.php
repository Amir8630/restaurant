<?php

namespace app\modules\cook;

use Yii;
use yii\filters\AccessControl;

/**
 * cook module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\cook\controllers';

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
                        'matchCallback' => fn() => Yii::$app->user->identity->userRole == 'cook',
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
