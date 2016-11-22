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

            if($session) {
                $date = date('Y-m-d', $session->start_timestamp);
            } else {
                $date = date('Y-m-d');
            }
            
            return $this->render('index', [
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
