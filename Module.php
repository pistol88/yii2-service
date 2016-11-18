<?php
namespace pistol88\service;

use yii;

class Module extends \yii\base\Module
{
    public $adminRoles = ['admin', 'superadmin'];
    public $workers = null;

    public $persentOdPromocode = 100; //Процент выплат сотрудникам от заказов с промокодом
    public $currency = 'руб.';
    public $mainIdent = 'Номер автомобиля с буквами';
    public $mainIdentFieldSelector = '#fieldvalue-value-2';
    public $cache = true;
    public $cachePeriod = 7000;
    public $shiftDuration = 20; //in hours
    public $stafferProfileUrl = '/staffer/staffer/view';
    public $propertyStatuses = ['active' => 'Активно', 'unactive' => 'Неактивно'];
    public $propertyName = 'Автомобили';
    public $identName = 'Номер авто';
    public $hideEmptyPrice = false;
    public $stafferModel = 'pistol88\staffer\models\Staffer'; // модель работников для назначения на исполнение заказа
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
        ];
	
    public function init()
    {
        parent::init();
    }
}
