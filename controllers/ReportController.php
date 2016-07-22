<?php
namespace pistol88\service\controllers;

use yii;
use pistol88\order\models\Order;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ReportController extends Controller
{
    public function actionIndex()
    {
        $total = Order::getStatByDate(date('Y-m-d'))['total'];
        
        $workers = $this->module->getWorkersList();

        $workerStat = [];
        
        //Считаем количество заказов и выручку за каждую смену каждого сотрудника
        $washersCount = yii::$app->worksess->getWorkersCount();

        foreach($workers as $worker) {
            if(!isset($workerStat[$worker->id]['service_count'])) {
                $workerStat[$worker->id]['service_count'] = 0; //Выполнено услуг
                $workerStat[$worker->id]['order_count'] = 0; //Кол-во заказов
                $workerStat[$worker->id]['service_total'] = 0; //Общая сумма выручки
                $workerStat[$worker->id]['earnings'] = 0;
            }
            
            $persent = '0.'.$this->module->workerPersent;
            
            foreach($worker->getSessions() as $session) {
                $stat = Order::getStatByDatePeriod($session->start, $session->stop);
                $workerStat[$worker->id]['service_count'] += $stat['count_elements'];
                $workerStat[$worker->id]['order_count'] += $stat['count_order'];
                $workerStat[$worker->id]['service_total'] += $stat['total'];
                //Заработок равен общей выручке за смену / кол-во работников в эту смену
                $workerStat[$worker->id]['earnings'] += ($stat['total']*$persent)/$washersCount;
                
            }
        }
        

        $workerPersent = $this->module->workerPersent;
        
        return $this->render('index', [
            'totalToday' => $total,
            'workerPersent' => $workerPersent,
            'workers' => $workers,
            'workerStat' => $workerStat,
            'secondCost' => $total/86400,
            'module' => $this->module,
        ]);
    }
}
