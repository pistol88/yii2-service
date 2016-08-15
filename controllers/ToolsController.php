<?php
namespace pistol88\service\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use pistol88\cart\widgets\ElementsList;
use pistol88\cart\widgets\CartInformer;

class ToolsController  extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
        ];
    }
    
    public function actionCartInfo()
    {
        die(json_encode([
            'cart' => ElementsList::widget(['columns' => '3', 'showCountArrows' => false, 'type' => ElementsList::TYPE_FULL]),
            'total' => CartInformer::widget(['htmlTag' => 'div', 'text' => '{c} на {p}']),
            'count' => yii::$app->cart->count,
        ]));
    }
}
