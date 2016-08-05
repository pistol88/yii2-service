<?php
namespace pistol88\service\controllers;

use Yii;
use pistol88\service\models\Payment;
use pistol88\service\models\category\PaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class PaymentController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'set' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
				'only' => ['set', 'index', 'unset'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSet()
    {
        $data = yii::$app->request->post();
        
        $json = ['result' => null, 'error' => null];
        
        if(!Payment::find()->where(['worker_id' => $data['worker_id'], 'session_id' => $data['session_id']])->one()) {
            $payment = new Payment;
            $payment->worker_id = $data['worker_id'];
            $payment->session_id = $data['session_id'];
            $payment->sum = $data['sum'];
            if($payment->validate() && $payment->save()) {
                $json['result'] = 'success';
            } else {
                $json['result'] = 'fail';
                $json['error'] = $payment->errors;
            }
        }
        
        return json_encode($json);
    }

    public function actionUnset()
    {
        $data = yii::$app->request->post();
        
        $json = ['result' => null, 'error' => null];
        
        if($payment = Payment::find()->where(['worker_id' => $data['worker_id'], 'session_id' => $data['session_id']])->one()) {
            if($payment->delete()) {
                $json['result'] = 'success';
            } else {
                $json['result'] = 'fail';
                $json['error'] = $payment->errors;
            }
        }
        
        return json_encode($json);
    }
}
