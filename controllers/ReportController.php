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
use yii\helpers\ArrayHelper;
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
    
    public function actionPeriod($dateStart = null, $dateStop = null)
    {
        if(!$dateStart) {
            $dateStart = date('Y-m-d H:i:s', (time()-(86400*30)));
        } else {
            $dateStart = date('Y-m-d H:i:s', strtotime($dateStart));
        }
        
        if(!$dateStop) {
            $dateStop = date('Y-m-d H:i:s');
        } else {
            $dateStop = date('Y-m-d H:i:s', strtotime($dateStop));
        }

        $serviceStat = yii::$app->order->getStatByModelAndDatePeriod('pistol88\service\models\Price', $dateStart, $dateStop);
        $customStat = yii::$app->order->getStatByModelAndDatePeriod('pistol88\service\models\CustomService', $dateStart, $dateStop);
        $shopStat = yii::$app->order->getStatByModelAndDatePeriod(['pistol88\microshop\models\Product', 'pistol88\shop\models\Product'], $dateStart, $dateStop);
        
        return $this->render('period', [
            'module' => $this->module,
            'serviceStat' => $serviceStat,
            'customStat' => $customStat,
            'shopStat' => $shopStat,
            'dateStart' => Html::encode($dateStart),
            'dateStop' => Html::encode($dateStop),
        ]);
    }
    
    public function actionMini($sessionId = null)
    {
        if($sessionId) {
            $session = Session::findOne($sessionId);
        } else {
            if(!$session = yii::$app->worksess->soon()) {
                $session = yii::$app->worksess->last();
            }
        }

        if($session) {
            $data = yii::$app->service->getReportBySession($session);
        } else {
            $data = null;
        }

        $shopStat = yii::$app->order->getStatByModelAndDatePeriod(['pistol88\microshop\models\Product', 'pistol88\shop\models\Product'], $session->start, $session->stop);
        
        return $this->render('mini', [
            'session' => $session,
            'data' => $data,
            'module' => $this->module,
            'currency' => $this->module->currency,
            'shopStat' => $shopStat,
        ]);
    }
    
    public function actionIndex($sessionId = null)
    {
        if(!$sessionId) {
            $session = yii::$app->worksess->soon();
        } else {
            $session = Session::findOne($sessionId);
        }
        
        $sessions = yii::$app->worksess->getSessions(null, $date);
        
        if($session) {
            $data = yii::$app->service->getReportBySession($session);

            $shopStat = ['total' => 0, 'count_elements' => 0, 'count_orders' => 0];

            if($session) {
                $date = date('Y-m-d', $session->start_timestamp);
            } else {
                $date = date('Y-m-d');
            }
            
            $orders = yii::$app->order->getOrdersByDatePeriod($session->start, $session->stop);
            $shopOrders = [];
            
            foreach($orders as $order) {
                $elements = $order->getElementsRelation()->where(['model' => ['pistol88\microshop\models\Product', 'pistol88\shop\models\Product']]);
                if($elements->all()) {
                    $shopStat['count_orders'] += 1;
                    foreach($elements->all() as $element) {
                        $elementModel = $element->getModel();
                        
                        $shopStat['count_elements'] += $element->count;
                        $shopStat['total'] += ($elementModel->getCartPrice()*$element->count);
                        
                        $clientName = '';
                        
                        foreach(yii::$app->service->orderCustomFields as $field) {
                            $clientName .= " ".$order->getField($field);
                        }
                        
                        $shopOrders[$order->id . '_' . $element->id] = [
                            'name' => $elementModel->getCartName(),
                            'price' => $elementModel->getCartPrice(),
                            'timestamp' => $order->timestamp,
                            'count' => $element->count,
                            'order_id' => $order->id,
                            'client_name' => $clientName,
                        ];
                    }
                }
            }
            
            return $this->render('index', [
                'shopOrders' => $shopOrders,
                'shopStat' => $shopStat,
                'data' => $data,
                'date' => $date,
                'session' => $session,
                'sessions' => $sessions,
                'sessionId' => $sessionId,
                'module' => $this->module,
                'currency' => $this->module->currency,
            ]);
        } else {
            return $this->render('index', [
                'sessions' => $sessions,
                'session' => false,
                'module' => $this->module,
            ]);
        }
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
