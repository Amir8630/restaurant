<?php

namespace app\modules\manager;

use Yii;
use yii\filters\AccessControl;

/**
 * manager module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\manager\controllers';


    public function behaviors() 
    {
        return [ 
            'access' => [ 
                'class' => AccessControl::class, 
                'denyCallback' => fn() => Yii::$app->response->redirect('/'), 
                'rules' => [ 
                    [ 
                        'allow' => true, 
                        'controllers' => ['manager/svg', 'manager/user'],
                        'actions' => ['upload', 'index', 'create', 'update', 'delete', 'view'], 
                        'matchCallback' => fn() => Yii::$app->user->identity->userRole == 'manager' || Yii::$app->user->identity->userRole == 'admin', 
                    ],
                    [ 
                        'allow' => true, 
                        'roles' => ['@'], 
                        'matchCallback' => fn() => Yii::$app->user->identity->userRole == 'manager',
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
