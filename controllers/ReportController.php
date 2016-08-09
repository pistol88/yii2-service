<?php
namespace pistol88\service\controllers;

use yii;
use pistol88\service\events\Earnings;
use pistol88\service\models\Cost;
use pistol88\service\models\Payment;
use pistol88\order\models\Order;
use pistol88\order\models\PaymentType;
use pistol88\worksess\models\Session;
use yii\db\Query;
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
        
        $paymentsInfo = [];
        
        $paymentTypes = PaymentType::find()->all();
        
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
                    $workerStat[$worker->id]['earnings'] = 0; //
                    $workerStat[$worker->id]['payment'] = Payment::findOne(['session_id' => $session->id, 'worker_id' => $worker->id]);
                    $workerStat[$worker->id]['bonus'] = 0;
                    $workerStat[$worker->id]['fine'] = 0;
                    $workerStat[$worker->id]['persent'] = $basePersent;
                }

                $workerSessions = $worker->getSessionsBySessions($session);

                $workerStat[$worker->id]['sessions'] = $workerSessions;

                foreach($workerSessions as $workSession) {
                    
                    $userStat = Order::getStatByDatePeriod($workSession->start, $workSession->stop);
                    $workerStat[$worker->id]['service_count'] += $stat['count_elements'];
                    $workerStat[$worker->id]['order_count'] += $stat['count_order'];
                    $workerStat[$worker->id]['service_total'] += $stat['total'];

                    if($workersCount) {
                        $earning = ($stat['total']*$persent)/$workersCount;
                    } else {
                        $earning = ($stat['total']*$persent);
                    }

                    $earningsEvent = new Earnings(
                        [
                            'worker' => $worker,
                            'persent' => $persent,
                            'total' => $stat['total'],
                            'userTotal' => $userStat['total'],
                            'workersCount' => $workersCount,
                            'earning' => $earning,
                        ]
                    );
                    
                    $module = $this->module;
                    $module->trigger($module::EVENT_EARNINGS, $earningsEvent);
                    
                    $earning = $earningsEvent->earning;
                    
                    $workerStat[$worker->id]['earnings'] += $earning;
                    
                    if($earningsEvent->bonus) {
                        $workerStat[$worker->id]['bonus'] = $earningsEvent->bonus;
                    }
                    
                    if($earningsEvent->fine) {
                        $workerStat[$worker->id]['fine'] = $earningsEvent->fine;
                    }
                }
            }
            
            $stop = $session->stop;
            if(!$stop) {
                $stop = date('Y-m-d H:i:s');
            }
            
            foreach($paymentTypes as $pt) {
                $query = new Query();
                $sum = $query->from([Order::tableName()])
                        ->where('date >= :dateStart', [':dateStart' => $session->start])
                        ->andWhere('date <= :dateStop', [':dateStop' => $stop])
                        ->andWhere(['payment_type_id' => $pt->id])
                        ->sum('cost');

                $paymentsInfo[$pt->name] = (int)$sum;
            }
        }

        $workerPersent = $this->module->workerPersent;
        
        if($session) {
            $date = date('Y-m-d', $session->start_timestamp);
        } else {
            $date = date('Y-m-d');
        }
        
        $sessions = yii::$app->worksess->getSessions(null, $date);

        $costs = Cost::findAll(['session_id' => $session->id]);
        
        return $this->render('index', [
            'date' => $date,
            'costs' => $costs,
            'session' => $session,
            'sessions' => $sessions,
            'stat' => $stat,
            'workerPersent' => $workerPersent,
            'paymentTypes' => $paymentTypes,
            'paymentsInfo' => $paymentsInfo,
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
                return html::tag('li', Html::a(date('d.m.Y H:i:s', $item->start_timestamp) . ' ' . $item->shiftName . ' ('.$item->user->name.')', ['/service/report/index', 'sessionId' => $item->id]));
            }]);
        }

        die(json_encode($json));
    }
}
