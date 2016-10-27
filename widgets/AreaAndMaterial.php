<?php

namespace pistol88\service\widgets;

use pistol88\service\models\Service;
use pistol88\service\models\CustomService;
use yii;

class AreaAndMaterial extends \yii\base\Widget
{
    public $serviceName = null;
    public $settings = null;
    public function init()
    {
        \pistol88\service\assets\AreaAndMaterialAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        $customServiceModel = new CustomService;

        return $this->render('area_and_material', [
            'module' => yii::$app->getModule('service'),
            'name' => $this->serviceName,
            'settings' => $this->settings,
            'customServiceModel' => $customServiceModel,
        ]);
    }
}