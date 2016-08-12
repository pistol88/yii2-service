<?php
namespace pistol88\service\widgets;

use yii\helpers\Html;
use pistol88\service\models\Payment;
use pistol88\service\models\payment\PaymentSearch;
use yii;

class ClientPayments extends \yii\base\Widget
{
    public $client_id = null;
    
    public function init()
    {
        \pistol88\service\assets\WidgetAsset::register($this->getView());
        
        return parent::init();
    }

    public function run()
    {
        $searchModel = new PaymentSearch();
        
        $params = Yii::$app->request->queryParams;
        
        if($this->client_id && empty($params['PaymentSearch'])) {
            $params['PaymentSearch']['client_id'] = $this->client_id;
        }
        
        $dataProvider = $searchModel->search($params);

        return $this->render('client_payments', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
