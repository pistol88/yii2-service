<?php
namespace pistol88\service\widgets;

use yii\helpers\Html;
use pistol88\service\models\Property;
use pistol88\service\models\property\PropertySearch;
use kartik\select2\Select2;
use yii;

class PropertyToClient extends \yii\base\Widget
{
    public $client = null;
    
    public function init()
    {
        \pistol88\service\assets\PropertyToClientAsset::register($this->getView());
        
        return parent::init();
    }

    public function run()
    {
        $searchModel = new PropertySearch();
        
        $params = Yii::$app->request->queryParams;
        
        if($this->client && empty($params['PropertySearch'])) {
            $params['PropertySearch']['client_id'] = $this->client->id;
        }
        
        $dataProvider = $searchModel->search($params);

        $model = new Property;
        
        return $this->render('property_to_client', [
            'module' => yii::$app->getModule('service'),
            'model' => $model,
            'client' => $this->client,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
