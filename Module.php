<?php
namespace pistol88\service;

use yii;

class Module extends \yii\base\Module
{
    public $adminRoles = ['admin', 'superadmin'];
    public $workers = null;
    public $workerPersent = 30;
    public $workerCategoryIds = [];
    public $persentOdPromocode = 100; //Процент выплат сотрудникам от заказов с промокодом
    public $currency = 'руб.';
    public $mainIdent = 'Номер и марка автомобиля';
    public $mainIdentFieldSelector = '#fieldvalue-value-2';
    public $cache = true;
    public $cachePeriod = 7000;
    public $shiftDuration = 20; //in hours
    public $stafferProfileUrl = '/staffer/staffer/view';
    public $propertyStatuses = ['active' => 'Активно', 'unactive' => 'Неактивно'];
    public $propertyName = 'Автомобили';
    public $identName = 'Номер авто';
    public $promoDivision = []; //'model' => ['Скидка >' => 'процент от стоимости'] 
    public $menu = [
            [
                'label' => 'Заказ',
                'url' => ['/service/price/order'],
            ],
            [
                'label' => 'Отчеты',
                'url' => ['/service/report/index'],
            ],
            [
                'label' => 'Услуги',
                'url' => ['/service/service/index'],
            ],
            /* [
                'label' => yii::$app->getModule('service')->propertyName,
                'url' => ['/service/property/index'],
            ], */
            [
                'label' => 'Тарифы',
                'url' => ['/service/price/index'],
            ],
            [
                'label' => 'Категории',
                'url' => ['/service/category/index'],
            ],
            [
                'label' => 'Комплексы',
                'url' => ['/service/complex/index'],
            ],
            [
                'label' => 'Затраты',
                'url' => ['/service/cost/index'],
            ],
        ];
    
    const EVENT_EARNINGS = 'earnings';
    const EARNING_ELEMENT_CALCULATE = 'earning_element_calculate';
	
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
