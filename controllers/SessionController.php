<?php
namespace pistol88\service\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use pistol88\worksess\widgets\ControlButton;
use pistol88\worksess\widgets\Info;

class SessionController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
				'only' => ['current', 'start', 'stop'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
        ];
    }

    public function actionCurrent()
    {
        $workers = $this->module->getWorkersList();
        
        return $this->render('current', [
            'workers' => $workers,
        ]);
    }
}
