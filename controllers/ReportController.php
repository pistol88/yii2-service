<?php
namespace pistol88\service\controllers;

use yii;
use pistol88\service\events\Earnings;
use pistol88\service\models\Cost;
use pistol88\service\models\Payment;
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
        
        $paymentsInfo = [];
        
        $paymentTypes = PaymentType::find()->all();
        
        $shopStat = [];
        
        $sessionId = 0;
        
        $costs = [];
        
		$shopStat = yii::$app->order->getStatByModelAndDatePeriod('pistol88\shop\models\Product', $session->start, $session->stop);
		$stat = yii::$app->order->getStatByModelAndDatePeriod(['pistol88\service\models\CustomService', 'pistol88\service\models\Price'], $session->start, $session->stop);

		$shopStatPromocode = yii::$app->order->getStatByModelAndDatePeriod('pistol88\shop\models\Product', $session->start, $session->stop, "`promocode` != ''");
		$statPromocode = yii::$app->order->getStatByModelAndDatePeriod(['pistol88\service\models\CustomService', 'pistol88\service\models\Price'], $session->start, $session->stop, "`promocode` != ''");
		
		foreach($workers as $worker) {
			if(!isset($workerStat[$worker->id]['service_count'])) {
				$workerStat[$worker->id]['service_count'] = 0; //Выполнено услуг
				$workerStat[$worker->id]['order_count'] = 0; //Кол-во заказов
				$workerStat[$worker->id]['service_total'] = 0; //Общая сумма выручки
				$workerStat[$worker->id]['earnings'] = (int)$worker->fix; //
				$workerStat[$worker->id]['fines'] = 0; //штрафы
				$workerStat[$worker->id]['payment'] = Payment::findOne(['session_id' => $session->id, 'worker_id' => $worker->id]);
				$workerStat[$worker->id]['bonus'] = 0;
				$workerStat[$worker->id]['fine'] = 0;
				$workerStat[$worker->id]['persent'] = $basePersent;
			}
		}

        if($session) {
            $costs = Cost::findAll(['session_id' => $session->id]);
            
            $sessionId = $session->id;
            
            $workers = $session->users;
            
			$workersCount = yii::$app->worksess->getWorkersCount($session);
			
			$orders = yii::$app->order->getOrdersByDatePeriod($session->start, $session->stop);

			//Распределяем деньги от каждого заказа между сотрудниками
			foreach($orders as $order) {
				foreach($order->elements as $element) {
					$elementModel = $element->getModel(); 
					if(in_array($elementModel::className(), ['pistol88\service\models\CustomService', 'pistol88\service\models\Price'])) {
						$orderWorkers = [];
						$orderCustomerCount = 0;
						foreach($workers as $worker) {
							if($worker->hasWork(strtotime($order->date))) {
								$orderWorkers[] = $worker;

								if(empty($this->module->workerCategoryIds) | in_array($worker->category_id, $this->module->workerCategoryIds)) {
									$orderCustomerCount++;
								}

								//Задан ли индивидуальный процент
								if(empty($worker->persent)) {
									$basePersent = $this->module->getWorkerPersent($session);
								} else {
									$basePersent = $worker->persent;
								}

								if(!isset($workerStat[$worker->id]['fines'])) {
									$workerStat[$worker->id]['earnings'] = 0;
									$workerStat[$worker->id]['fines'] = 0; //штрафы
									$workerStat[$worker->id]['payment'] = Payment::findOne(['session_id' => $session->id, 'worker_id' => $worker->id]);
									$workerStat[$worker->id]['persent'] = $basePersent;
									$workerStat[$worker->id]['sessions'] = $worker->getSessionsBySessions($session);
									$workerStat[$worker->id]['service_count'] = 0; //Выполнено услуг
									$workerStat[$worker->id]['order_count'] = 0; //Кол-во заказов
									$workerStat[$worker->id]['service_total'] = 0; //Общая сумма выручки
								}
								
								$workerStat[$worker->id]['service_count'] += $element->count; //Выполнено услуг
								$workerStat[$worker->id]['order_count'] += 1; //Кол-во заказов
								$workerStat[$worker->id]['service_total'] += $element->price*$element->count; //Общая сумма выручки
							}
						}

						foreach($orderWorkers as $worker) {
							$persent = round(($workerStat[$worker->id]['persent']/100), 2);

							if((empty($this->module->workerCategoryIds) | in_array($worker->category_id, $this->module->workerCategoryIds))) {
								$earning = (($element->price*$element->count)*$persent)/$orderCustomerCount;
							} else {
								$earning = (($element->price*$element->count)*$persent);
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
				$module->trigger($module::EVENT_EARNINGS, $earningsEvent);
				
				$earning = $earningsEvent->earning;
				
				$fines = $worker->getFinesByDatePeriod($workSession->start, $workSession->stop)->sum('sum');
				
				$workerStat[$worker->id]['fines'] += $fines;
				$workerStat[$worker->id]['earnings'] = $earning;
				$workerStat[$worker->id]['earnings'] -= $fines;
				
				if($earningsEvent->bonus) {
					$workerStat[$worker->id]['bonus'] = $earningsEvent->bonus;
				}
				
				if($earningsEvent->fine) {
					$workerStat[$worker->id]['fine'] = $earningsEvent->fine;
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

        return $this->render('index', [
            'shopStat' => $shopStat,
            'date' => $date,
            'costs' => $costs,
            'session' => $session,
            'sessions' => $sessions,
            'sessionId' => $sessionId,
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
        $date = date('Y-m-d', strtotime(yii::$app->request->post('date')));
        
        $session = yii::$app->worksess->getSessions(null, $date);

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
