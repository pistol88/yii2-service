<?php
namespace pistol88\service\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use pistol88\worksess\widgets\ControlButton;
use pistol88\worksess\widgets\Info;
use yii\filters\AccessControl;

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

    public function actionStart($userId = null)
    {
        $for = null;
        
        if($userId) {
            $for = $this->module->getUserModel($userId);
        }
        
        if(!yii::$app->request->post('ajax')) {
            if(yii::$app->worksess->soon($for)) {
                yii::$app->session->setFlash('fail', 'Необходимо завершить текущую сессию');
            } else {
                if (yii::$app->worksess->start($for)) {
                    yii::$app->session->setFlash('success', 'Сессия успешно стартовала');
                } else {
                    yii::$app->session->setFlash('fail', 'Не удалось начать сессию');
                }
            }
        } else {
            $result = 'fail';
            $button = null;
            $error = null;

            if(yii::$app->worksess->soon($for)) {
                $error = 'Необходимо завершить текущую сессию';
            } else {
                if (yii::$app->worksess->start($for)) {
                    $result = 'success';
                    $button = ControlButton::widget(['for' => $for]);
                } else {
                    $error = 'Не удалось начать сессию';
                }
            }
            
            return json_encode(['result' => $result, 'button' => $button, 'info' => Info::widget(['for' => $for]), 'error' => $error]);
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionStop($userId = null)
    {
        $for = null;
        
        if($userId) {
            $for = $this->module->getUserModel($userId);
        }
        
        if(!yii::$app->request->post('ajax')) {
            if(!yii::$app->worksess->soon($for)) {
                yii::$app->session->setFlash('fail', 'Нет текущей сессии');
            } else {
                if (yii::$app->worksess->stop($for)) {
                    yii::$app->session->setFlash('success', 'Сессия успешно завершена');
                } else {
                    yii::$app->session->setFlash('fail', 'Не удалось завершить сессию');
                }
            }
        } else {
            $result = 'fail';
            $button = null;
            $error = null;

            if(!yii::$app->worksess->soon($for)) {
                $error = 'Нет текущей сессии';
            } else {
                if (yii::$app->worksess->stop($for)) {
                    $result = 'success';
                    $button = ControlButton::widget(['for' => $for]);
                } else {
                    $error = 'Не удалось завершить сессию';
                }
            }
            
            return json_encode(['result' => $result, 'button' => $button, 'info' => Info::widget(['for' => $for]), 'error' => $error]);
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }
    
    protected function findModel($id)
    {
        $model = new Session;
        
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested session does not exist.');
        }
    }
}
