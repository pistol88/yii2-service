<?php
namespace pistol88\service;

use pistol88\service\events\Salary;
use pistol88\service\events\Element;
use pistol88\staffer\models\Payment;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use pistol88\service\models\StafferToService;
use yii;

class Service extends Component
{
    public $workerCategoryIds = [];
    public $workerPersent = 30;
    public $workers = null;
    public $promoDivision = []; //'model' => ['Скидка >' => 'процент от стоимости']
    public $splitOrderPerfome = false; // возможность исполнения заказа отдельными работниками
    
    const EVENT_SALARY = 'salary';
    const EVENT_SALARY_ELEMENT_CALCULATE = 'salary_element_calculate';
    
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

    public function getStafferByServiceId($serviceId)
    {
        return StafferToService::find()->where(['service_id' => $serviceId])->all();
    }

    public function getReportBySession($session)
    {
        $workers = $session->getUsers()->orderBy('category_id')->all();
        
        if(yii::$app->has('organization') && $organization = yii::$app->organization->get()) {
            $orders = yii::$app->order->setOrganization($organization->id)->getOrdersByDatePeriod($session->start, $session->stop);
        } else {
            $orders = yii::$app->order->getOrdersByDatePeriod($session->start, $session->stop);
        }

        $data = []; //Данные по заказам
        $salary = []; //Зарплаты
        $balances = []; //Остаток по выплатам
        $fines = []; //Штрафы
        $baseSalary = []; //Базовая зарплата без штрафов и бонусов

        $sessionTotal = 0; //Сумарный оборот сегодня
        
        $prevOrderWorkers = 0;
        $prevGroup = false;
        
        //Формируем группы, исходя из кол-ва сотрудников
        foreach($orders as $order) {
            $groupWorkers = [];
            $workersCount = 0;
            
            //Присваиваем сотрудников заказу
            if ($this->splitOrderPerfome && $staffersToService = $this->getStafferByServiceId($order->id)) {
                $stafferModel = $staffer->staffer_model;
                $stafferModel = new $stafferModel();
                $orderWorkers = $stafferModel::find()->where(['id' => ArrayHelper::getColumn($staffersToService, 'id')])->all();
            } else {
                $orderWorkers = $workers;
            }
            
            foreach($orderWorkers as $worker) {
                if($worker->hasWork($order->timestamp)) {
                    if($worker->category) {
                        $workerCategoryName = $worker->category->name;
                    } else {
                        $workerCategoryName = '';
                    }
                    
                    $worker =  ArrayHelper::toArray($worker);
                    $worker['categoryName'] = $workerCategoryName;
                    $worker['salary'] = 0;

                    $groupWorkers[] = $worker;
                    
                    if(empty($this->workerCategoryIds) | in_array($worker['category_id'], $this->workerCategoryIds)) {
                        $workersCount++;
                    }
                }
            }

            if(!$prevGroup | $prevOrderWorkers != $workersCount) {                
                $data[$order->timestamp] = [
                    'workers' => $groupWorkers,
                    'workersCount' => $workersCount,
                    'orders' => [$order],
                ];
                
                if(!$prevGroup) {
                    $data[$order->timestamp]['name'] = $workersCount;
                } elseif($prevOrderWorkers > $workersCount) {
                    $data[$order->timestamp]['name'] = '+1';
                } else {
                    $data[$order->timestamp]['name'] = '-1';
                }
                
                $prevGroup = $order->timestamp;
            } else {
                $data[$prevGroup]['orders'][] = $order;
            }
            
            $prevOrderWorkers = $workersCount;
        }
        
        //Проходимся по группам, делаем расчеты ЗП
        foreach($data as &$group) {
            if($group['orders']) {
                $group['sum'] = 0;
                foreach($group['orders'] as $key => &$order) {
                    $elements = $order->getElementsRelation()->where(['model' => ['pistol88\service\models\CustomService', 'pistol88\service\models\Price']]);
                    if($elements->all()) {
                        $order = ArrayHelper::toArray($order);
                        $order['elements'] = [];
                        $order['base_price'] = 0;
                        $order['to_base'] = 0;
                        $order['price'] = 0;

                        $basePrice = 0;
                        $price = 0;
                        
                        foreach($elements->all() as $element) {
                            $serviceName = $element->getModel()->name;
                            $element = ArrayHelper::toArray($element);
                            $element['serviceName'] = $serviceName;
                            $order['elements'][] = $element;
                            
                            $basePrice += $element['base_price']*$element['count'];
                            $price += $element['price']*$element['count'];
                        }
                        
                        $elementEvent = new Element(['cost' => $price, 'group' => $group]);
                        $this->trigger(self::EVENT_SALARY_ELEMENT_CALCULATE, $elementEvent);
                        $price = $elementEvent->cost;
                        
                        $customToBase = false;
                        //Процент, выдааемый сотруднику в случае применения скидки
                        if($promoDivision = $this->promoDivision) {
                            $dif = $basePrice-$price;
                            if($dif > 0) {
                                $promoPersent = ($dif*100)/$basePrice;
                                foreach($promoDivision as $model => $params) {
                                    foreach($params as $k => $v) {
                                        if($promoPersent >= $k) {
                                            $customToBase = $basePrice*($v/100);
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        if(!$customToBase) {
                            $order['to_base'] += $price;
                        } else {
                            $order['to_base'] += $customToBase;
                        }
                        
                        $order['base_price'] += $basePrice;
                        $order['price'] += $price;
                        $sessionTotal += $price;
                        $group['sum'] += $order['to_base'];
                    } else {
                        unset($group['orders'][$key]);
                    }
                }
                
                //Назначаем процент и базу
                $group['persent'] = $this->getWorkerPersent($group);
                $group['base'] = $group['sum']*($group['persent']/100);
                
                //Начисляем ЗП сотрудникам с индивидуальным процентом
                foreach($group['workers'] as $key => $worker) {
                    if($worker['persent']) {
                        //Базовый тип
                        if($worker['pay_type'] == 'base') {
                            //Исполнитель
                            if(in_array($worker['category_id'], $this->workerCategoryIds)) {
                                $group['persent'] = $group['persent']-$worker['persent'];
                            }
                            $workerSalary = $group['base']*($worker['persent']/100);
                            $group['workers'][$key]['salary'] = $workerSalary;
                        }
                        //С выручки
                        else {
                            $workerSalary = $group['sum']*($worker['persent']/100);
                            $group['workers'][$key]['salary'] = $workerSalary;
                            $salary[$worker['id']] += $workerSalary;
                        }
                    }
                }
                
                //Начисляем ЗП обычным сотрудникам
                foreach($group['workers'] as $key => $worker) {
                    //Процент для мойщиков
                    if(!$worker['persent']) {
                        if($worker['pay_type'] == 'base' && in_array($worker['category_id'], $this->workerCategoryIds)) {
                            $workerSalary = $group['base']/$group['workersCount'];
                            $group['workers'][$key]['salary'] = $workerSalary;
                            $salary[$worker['id']] += $workerSalary;
                        }
                    }
                }
            }
        }

        $baseSalary = $salary;
        
        //Вычитаем штрафы
        foreach($workers as $worker) {
            if($fineSum = $worker->getFinesByDatePeriod($session->start, $session->stop)->sum('sum')) {
                $fines[$worker->id] = $fineSum;
                $salary[$worker->id] -= $fineSum;
            }
        }
        
        //Начисляем фиксы
        foreach($workers as $worker) {
            if($fix = $worker->fix) {
                $salary[$worker->id] += $fix;
            }
        }

        //Узнаем, сколько уже выплатили
        foreach($salary as $workerId => $workerSalary) {
            $paymentSum = Payment::find()->where(['session_id' => $session->id, 'worker_id' => $workerId])->sum('sum');
            $balances[$workerId] = $workerSalary-$paymentSum;
        }
        
        $dataSalary = [];
        foreach($workers as $worker) {
            $dataSalary[$worker->id] = [];
            $dataSalary[$worker->id]['staffer'] = $worker;
            $dataSalary[$worker->id]['base_salary'] = round($baseSalary[$worker['id']], 2); //Грязная ЗП
            $dataSalary[$worker->id]['balance'] = round($balances[$worker['id']], 2); //Остаток по выплате ЗП
            $dataSalary[$worker->id]['fines'] = round($fines[$worker['id']], 2); //Штрафы
            $dataSalary[$worker->id]['bonuses'] = 0; //Бонусы

            $workerSalary = $salary[$worker['id']]; //Чистая ЗП без изменчивости
            
            //Добавляем изменчивости
            $salaryEvent = new Salary(
                [
                    'session' => $session,
                    'worker' => $worker,
                    'total' => $sessionTotal,
                    'salary' => $workerSalary,
                ]
            );

            $this->trigger(self::EVENT_SALARY, $salaryEvent);
            $workerSalary = $salaryEvent->salary;
            
            $dataSalary[$worker->id]['fines'] += $salaryEvent->fine;
            $dataSalary[$worker->id]['bonuses'] += $salaryEvent->bonus;
            
            $dataSalary[$worker->id]['salary'] = round($workerSalary, 2); //Чистая ЗП (со штрафами и бонусами)
        }
        
        return ['orders' => $data, 'salary' => $dataSalary];
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
