<?php
namespace pistol88\service;

use yii;

class Module extends \yii\base\Module
{
    public $adminRoles = ['admin', 'superadmin'];
    public $workers = null;
    public $workerPersent = 100;
    public $currency = 'руб.';
    public $mainIdent = 'Номер и марка автомобиля';
    public $mainIdentFieldSelector = '#fieldvalue-value-2';
    
    public function init()
    {
        parent::init();
    }
    
    public function getWorkersList()
    {
        if(is_callable($this->workers)) {
            $values = $this->workers;
            
            return $values();
        }
        
        return [];
    }
}