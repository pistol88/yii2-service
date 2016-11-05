<?php
namespace pistol88\service;

use yii\base\BootstrapInterface;
use yii;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if(empty($app->modules['gridview'])) {
            $app->setModule('gridview', [
                'class' => '\kartik\grid\Module',
            ]);
        }
        
        if(!$app->has('service')) {
            $app->set('service', [
                'class' => '\pistol88\service\Service',
            ]);
        }
    }
}