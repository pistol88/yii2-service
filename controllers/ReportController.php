<?php
namespace pistol88\service\controllers;
use yii;
use pistol88\service\events\Earnings;
use pistol88\service\events\Element;
use pistol88\service\models\Cost;
use pistol88\staffer\models\Payment;
use pistol88\staffer\models\Fine;
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
        $workerStat = [];
        $workers = [];
        $shopStat = [];
        $sessionId = 0;
        $costs = [];
        $totalEarning = 0;
        
        if($session) {
            $shopStat = yii::$app->order->getStatByModelAndDatePeriod('pistol88\shop\models\Product', $session->start, $session->stop);
            $stat = yii::$app->order->getStatByModelAndDatePeriod(['pistol88\service\models\CustomService', 'pistol88\service\models\Price'], $session->start, $session->stop);
            $shopStatPromocode = yii::$app->order->getStatByModelAndDatePeriod('pistol88\shop\models\Product', $session->start, $session->stop, "`promocode` != ''");
            $statPromocode = yii::$app->order->getStatByModelAndDatePeriod(['pistol88\service\models\CustomService', 'pistol88\service\models\Price'], $session->start, $session->stop, "`promocode` != ''");
            $costs = Cost::findAll(['session_id' => $session->id]);
            $sessionId = $session->id;
            $workers = $session->users;
            $workersCount = yii::$app->worksess->getWorkersCount($session);
            $orders = yii::$app->order->getOrdersByDatePeriod($session->start, $session->stop);
            foreach($workers as $worker) {
                if(!isset($workerStat[$worker->id]['fines'])) {
                    //Задан ли индивидуальный процент
                    if(empty($worker->persent)) {
                        if($worker->pay_type == 'base') {
                            $basePersent = $this->module->getWorkerPersent($session);
                        } else {
                            $basePersent = 0;
                        }
                    } else {
                        $basePersent = $worker->persent;
                    }
                    $workerStat[$worker->id]['fix'] = (int)$worker->fix;
                    $workerStat[$worker->id]['time'] = yii::$app->worksess->getUserWorkTimeBySession($worker, $session);
                    $workerStat[$worker->id]['earnings'] = (int)$worker->fix;
                    $workerStat[$worker->id]['fines'] = 0; //штрафы
                    $workerStat[$worker->id]['payment'] = Payment::findOne(['session_id' => $session->id, 'worker_id' => $worker->id]);
                    $workerStat[$worker->id]['payments'] = Payment::find()->where(['session_id' => $session->id, 'worker_id' => $worker->id])->all();
                    $workerStat[$worker->id]['persent'] = $basePersent;
                    $workerStat[$worker->id]['sessions'] = $worker->getSessionsBySessions($session);
                    $workerStat[$worker->id]['service_count'] = 0; //Выполнено услуг
                    $workerStat[$worker->id]['order_count'] = 0; //Кол-во заказов
                    $workerStat[$worker->id]['service_total'] = 0; //Общая сумма выручки
                    $workerStat[$worker->id]['service_base_total'] = 0; //Общая сумма выручки без учета скидок
                    $workerStat[$worker->id]['bonus'] = 0;
                }
            }
            //Распределяем деньги от каждого заказа между сотрудниками
            foreach($orders as $order) {
                $orderWorkers = [];
                $orderCustomerCount = 0;
                foreach($workers as $worker) {
                    if($worker->hasWork(strtotime($order->date))) {
                        $orderWorkers[] = $worker;
                        if(empty($this->module->workerCategoryIds) | in_array($worker->category_id, $this->module->workerCategoryIds)) {
                            $orderCustomerCount++;
                        }
                    }
                }
                
                $hasServiceElements = [];
                
                $elements = [];
                
                foreach($order->elements as $element) {
                    $elementModel = $element->getModel();
                    
                    if(in_array($elementModel::className(), ['pistol88\service\models\CustomService', 'pistol88\service\models\Price'])) {
                        if(!isset($elements[$elementModel::className()])) {
                            $elements[$elementModel::className()]['service_count'] = 0;
                            $elements[$elementModel::className()]['service_total'] = 0;
                            $elements[$elementModel::className()]['service_base_total'] = 0;
                        }
                        
                        $total = $element->price*$element->count;
                        
                        $elementEvent = new Element(['cost' => $total]);

                        $module = $this->module;
                        $this->module->trigger($module::EARNING_ELEMENT_CALCULATE, $elementEvent);
                        $total = $elementEvent->cost;

                        $totalToEarning += $total;
                        
                        $elements[$elementModel::className()]['service_count'] += $element->count;
                        $elements[$elementModel::className()]['service_total'] += $total;
                        $elements[$elementModel::className()]['service_base_total'] += $element->base_price*$element->count;
                    }
                }
                
                foreach($elements as $element) {
                    foreach($orderWorkers as $worker) {
                        if(!isset($hasServiceElements[$worker->id])) {
                            $workerStat[$worker->id]['order_count'] += 1;
                        }
                        $hasServiceElements[$worker->id] = true;
                        $workerStat[$worker->id]['service_count'] += $element['service_count'];
                        $workerStat[$worker->id]['service_total'] += $element['service_total'];
                        $workerStat[$worker->id]['service_base_total'] += $element['service_total'];
                        
                        if($workerStat[$worker->id]['persent']) {
                            $persent = round(($workerStat[$worker->id]['persent']/100), 2);
                            $elementCost = $element['service_total'];
                            //Процент, выдааемый сотруднику в случае применения скидки
                            if($promoDivision = $this->module->promoDivision) {
                                $dif = $element['service_base_total']-$element['service_total'];
                                if($dif > 0) {
                                    $promoPersent = ($dif*100)/$element['service_base_total'];
                                    foreach($promoDivision as $model => $params) {
                                        if($elementModel::className() == $model) {
                                            foreach($params as $k => $v) {
                                                if($promoPersent > $k) {
                                                    $elementCost = $element['service_base_total'];
                                                    $elementCost = $elementCost*($v/100);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if((empty($this->module->workerCategoryIds) | in_array($worker->category_id, $this->module->workerCategoryIds))) {
                                $earning = ($elementCost*$persent)/$orderCustomerCount;
                            } else {
                                $earning = ($elementCost*$persent);
                            }
                            $workerStat[$worker->id]['earnings'] += $earning;
                        }
                    }
                }
            }
            //Костомные начисления через триггер
            foreach($workers as $worker) {
                $earning = $workerStat[$worker->id]['earnings'];
                $earningsEvent = new Earnings(
                    [
                        'worker' => $worker,
                        'total' => $stat['total'],
                        'userTotal' => $workerStat[$worker->id]['service_total'],
                        'workersCount' => $workersCount,
                        'earning' => $earning,
                    ]
                );

                $module = $this->module;
                $this->module->trigger($module::EVENT_EARNINGS, $earningsEvent);
                $earning = $earningsEvent->earning;
                
                $fines = $worker->getFinesByDatePeriod($session->start, $session->stop)->sum('sum');
                $workerStat[$worker->id]['fines'] += $fines;
                $workerStat[$worker->id]['earnings'] = $earning;
                $workerStat[$worker->id]['earnings'] -= $fines;
                if($earningsEvent->bonus) {
                    $workerStat[$worker->id]['bonus'] = $earningsEvent->bonus;
                }
                if($earningsEvent->fine) {
                    $workerStat[$worker->id]['fine'] = $earningsEvent->fine;
                }
                $paymentSum = Payment::find()->where(['session_id' => $session->id, 'worker_id' => $worker->id])->sum('sum');
                $workerStat[$worker->id]['earnings'] -= $paymentSum;
            }
            $stop = $session->stop;
            if(!$stop) {
                $stop = date('Y-m-d H:i:s');
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
            'totalEarning' => $totalEarning,
            'shopStat' => $shopStat,
            'date' => $date,
            'costs' => $costs,
            'session' => $session,
            'sessions' => $sessions,
            'sessionId' => $sessionId,
            'stat' => $stat,
            'workerPersent' => $workerPersent,
            'workers' => $workers,
            'workerStat' => $workerStat,
            'module' => $this->module,
        ]);
    }
    
    public function actionGetSessions()
    {
        $date = date('Y-m-d', strtotime(yii::$app->request->post('date')));
        $session = yii::$app->worksess->getSessions(null, $date);
        $json = [];
        if(empty($session)) {
            $json['HtmlList'] = '<ul><li>Сессии не были открыты.</li></ul>';
        } else {
            $json['HtmlList'] = Html::ul($session, ['item' => function($item, $index) {
                return html::tag('li', Html::a(date('d.m.Y H:i:s', $item->start_timestamp) . ' ' . $item->shiftName , ['/service/report/index', 'sessionId' => $item->id]));
            }]);
        }
        die(json_encode($json));
    }
}
