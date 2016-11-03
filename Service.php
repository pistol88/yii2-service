<?php
namespace pistol88\service;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use pistol88\service\models\StafferToService;
use yii;

class Service extends Component
{
    public function getCalculateWidgets()
    {
        return [
            'pistol88\service\widgets\AreaAndMaterial' => 'Калькулятор площади',
        ];
    }

    /* Добавляет запись в таблицу исполнителей заказа
    * staffer_model и service_model - classname сооствествующих моделей
    */

    public function addStafferToService($stafferId, $serviceId, $params = null)
    {
        $model = new StafferToService();

        $model->staffer_id = $stafferId;
        $model->service_id = $serviceId;
        $model->date = date('Y-m-d H:i:s');

        if (isset($params['stafferModel'])) {
            $model->staffer_model = $params['stafferModel'];
        }

        if (isset($params['serviceModel'])) {
            $model->service_model = $params['serviceModel'];
        }

        if (isset($params['sessionId'])) {
            $model->session_id = $params['sessionId'];
        }

        return $model->save();
    }

    public function getStafferIdsByServiceId($serviceId)
    {
        return StafferToService::find()->where(['service_id' => $serviceId])->all();
    }

}
