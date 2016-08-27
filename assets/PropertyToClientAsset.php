<?php
namespace pistol88\service\assets;

use yii\web\AssetBundle;

class PropertyToClientAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/property_to_client.js',
    ];
    
    public $css = [
        'css/property_to_client.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }
}
