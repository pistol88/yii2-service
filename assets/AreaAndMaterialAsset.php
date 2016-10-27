<?php
namespace pistol88\service\assets;

use yii\web\AssetBundle;

class AreaAndMaterialAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/area_and_material.js',
    ];

    public $css = [
        'css/area_and_material.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }
}