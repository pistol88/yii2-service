<?php
namespace pistol88\service\controllers;

use yii;
use pistol88\order\models\Order;
use pistol88\worksess\models\Session;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ReportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
				'only' => ['index', 'get-sessions'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
        ];
    }
    
    public function actionIndex($sessionId = null)
    {
        if(!$sessionId) {
            $session = yii::$app->worksess->soon();
        } else {
            $session = Session::findOne($sessionId);
        }

        $stat = null;
        
        $workers = $this->module->getWorkersList();

        $workerStat = [];

        $workers = [];
        $workerStat = [];
        
        if($session) {
            $workers = $session->users;
            $stat = Order::getStatByDatePeriod($session->start, $session->stop);

            $workersCount = 0;
            
            foreach($workers as $worker) {
                if($worker->pay_type == 'base' | empty($worker->pay_type)) {
                    $workersCount++;
                }
            }

            foreach($workers as $worker) {
                if(empty($worker->persent)) {
                    $basePersent = $this->module->workerPersent;
                    $persent = '0.'.$this->module->workerPersent;
                } else {
                    $basePersent = $worker->persent;
                    $persent = '0.'.$worker->persent;
                }
                
                if(!isset($workerStat[$worker->id]['service_count'])) {
                    $workerStat[$worker->id]['service_count'] = 0; //Выполнено услуг
                    $workerStat[$worker->id]['order_count'] = 0; //Кол-во заказов
                    $workerStat[$worker->id]['service_total'] = 0; //Общая сумма выручки
                    $workerStat[$worker->id]['earnings'] = 0;
                    $workerStat[$worker->id]['persent'] = $basePersent;
                }

                $workerSessions = $worker->getSessionsBySessions($session);

                $workerStat[$worker->id]['sessions'] = $workerSessions;

                foreach($workerSessions as $workSession) {
                    
                    $stat = Order::getStatByDatePeriod($workSession->start, $workSession->stop);
                    $workerStat[$worker->id]['service_count'] += $stat['count_elements'];
                    $workerStat[$worker->id]['order_count'] += $stat['count_order'];
                    $workerStat[$worker->id]['service_total'] += $stat['total'];

                    //Заработок равен общей выручке за смену / кол-во работников в эту смену
                    if($workersCount) {
                        $workerStat[$worker->id]['earnings'] += ($stat['total']*$persent)/$workersCount;
                    } else {
                        $workerStat[$worker->id]['earnings'] += ($stat['total']*$persent);
                    }
                }
            }
        }

        $workerPersent = $this->module->workerPersent;
        
        if($session) {
            $date = date('Y-m-d', $session->start_timestamp);
        } else {
            $date = date('Y-m-d');
        }
        
        $sessions = yii::$app->worksess->getSessions(null, $date);
        
        return $this->render('index', [
            'date' => $date,
            'session' => $session,
            'sessions' => $sessions,
            'stat' => $stat,
            'workerPersent' => $workerPersent,
            'workers' => $workers,
            'workerStat' => $workerStat,
            'module' => $this->module,
        ]);
    }
    
    public function actionGetSessions()
    {
        $session = yii::$app->worksess->getSessions(null, yii::$app->request->post('date'));
        
        $json = [];
        
        if(empty($session)) {
            $json['HtmlList'] = '<ul><li>Сессии не были открыты.</li></ul>';
        } else {
        
            $json['HtmlList'] = Html::ul($session, ['item' => function($item, $index) {
                return html::tag('li', Html::a($item->start.' ('.$item->user->name.')', ['/service/report/index', 'sessionId' => $item->id]));
            }]);
        }
        
        die(json_encode($json));
    }
}
