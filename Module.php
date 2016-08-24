<?php
namespace pistol88\service;

use yii;

class Module extends \yii\base\Module
{
    public $adminRoles = ['admin', 'superadmin'];
    public $workers = null;
    public $workerPersent = 30;
    public $currency = 'руб.';
    public $mainIdent = 'Номер и марка автомобиля';
    public $mainIdentFieldSelector = '#fieldvalue-value-2';
    public $cache = true;
    public $cachePeriod = 7000;
    public $shiftDuration = 20; //in hours
    public $stafferProfileUrl = '/staffer/staffer/view';
    
    const EVENT_EARNINGS = 'earnings';
    
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

    public function getWorkerPersent($session)
    {
        if ( is_callable($this->workerPersent)) {
            $workerPercent = $this->workerPersent;
            return  $workerPercent($session);
        } else {
            return $this->workerPersent;
        } 
    }
}