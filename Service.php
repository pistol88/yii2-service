<?php
namespace pistol88\service;

use pistol88\service\events\Salary;
use pistol88\service\events\GroupSalary;
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
    public $orderCustomFields = [2];

    const EVENT_SALARY = 'salary';
    const EVENT_GROUP_CALCULATE = 'group_calculate';
    const EVENT_GROUP_SALARY = 'salary_group';

    public function getCalculateWidgets()
    {
        return [
            'pistol88\service\widgets\AreaAndMaterial' => 'Калькулятор площади',
        ];
    }

    /* Добавляет запись в таблицу исполнителей заказа
    * staffer_model и service_model - classname сооствествующих моделей
    */

    public function addStafferToService($stafferId, $serviceId, $stafferModel, $serviceModel, $sessionId = null)
    {
        $model = new StafferToService();

        $model->staffer_id = $stafferId;
        $model->service_id = $serviceId;
        $model->staffer_model = $stafferModel;
        $model->service_model = $serviceModel;
        $model->datetime = date('Y-m-d H:i:s');

        if($sessionId) {
            $model->session_id = $sessionId;
        }

        return $model->save();
    }

    public function getStafferByServiceId($serviceId)
    {
        return StafferToService::find()->where(['service_id' => $serviceId])->all();
    }

    public function groupSalaryVariablity($salary, $group, $session, $worker)
    {
        //Добавляем изменчивости
        $salaryEvent = new GroupSalary(
            [
                'session' => $session,
                'worker' => $worker,
                'salary' => $salary,
            ]
        );

        $this->trigger(self::EVENT_GROUP_SALARY, $salaryEvent);

        return $salaryEvent->salary;
    }

    public function getReportBySession($session)
    {
        if($session->report) {
            return unserialize($session->report);
        }
        
        $workers = $session->getUsers()->orderBy('category_id')->all();

        if(yii::$app->has('organization') && $organization = yii::$app->organization->get()) {
            $orders = yii::$app->order->setOrganization($organization->id)->getOrdersByDatePeriod($session->start, $session->stop);
        } else {
            $orders = yii::$app->order->getOrdersByDatePeriod($session->start, $session->stop);
        }

        $data = []; //Данные по заказам
        $salary = []; //Зарплаты
        $fines = []; //Штрафы
        $bonuses = []; // бонусы
        $baseSalary = []; //Базовая зарплата без штрафов и бонусов
        $sessionTotal = 0; //Сумарный оборот сегодня
        $summary = [
            'servicesTotal' => 0,
            'baseServicesTotal' => 0,
            'elementsCount' => 0,
            'ordersCount' => 0,
            'totalSalary' => 0,
        ];

        $prevOrderWorkers = 0;
        $prevOrdersWorkersList = [];
        $prevGroup = false;

        //Формируем группы, исходя из кол-ва сотрудников
        foreach($orders as $order) {
            $groupWorkers = [];
            $orderWorkers = [];
            $workersCount = 0;
            $workersList = [];

            $summary['ordersCount']++;
            
            //Присваиваем сотрудников заказу
            if ($this->splitOrderPerfome && $staffersToService = $this->getStafferByServiceId($order->id)) {
                // так как модель workera у staffer'а может быть любой - придётся пробежаться по всем
                foreach ($staffersToService as $key => $staffer) {
                    $stafferModel = $staffer->staffer_model;
                    $stafferModel = new $stafferModel();
                    $worker =  $stafferModel::findOne($staffer->staffer_id);
                    $orderWorkers[] = $worker;
                }
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

                    if($worker['pay_type'] != 'overbase' && (empty($this->workerCategoryIds) | in_array($worker['category_id'], $this->workerCategoryIds))) {
                        $workersCount++;
                        $workersList[] = $worker['id'];
                    }
                }
            }

            if(!$prevGroup | $prevOrderWorkers != $workersCount | count(array_intersect($workersList, $prevOrdersWorkersList)) != count($workersList)) {
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
            $prevOrdersWorkersList = $workersList;
        }
        
        //Проходимся по группам, делаем расчеты ЗП
        foreach($data as &$group) {
            if($group['orders']) {
                $group['sum'] = 0;
                foreach($group['orders'] as $key => &$order) {
                    $elements = $order->getElementsRelation()->where(['model' => ['pistol88\service\models\CustomService', 'pistol88\service\models\Price']]);
                    if($elements->all()) {
                        $orderModel = $order;
                        $order = ArrayHelper::toArray($order);
                        $order['elements'] = [];
                        $order['base_price'] = 0;
                        $order['to_base'] = 0;
                        if($paymentType = $orderModel->paymentType) {
                            $order['payment_type_name'] = $paymentType->name;
                        } else {
                            $order['payment_type_name'] = '';
                        }

                        $order['price'] = 0;
                        foreach($this->orderCustomFields as $field) {
                            $order['client_name'] .= " ".$orderModel->getField($field);
                        }

                        $basePrice = 0;
                        $price = 0;

                        foreach($elements->all() as $element) {
                            $serviceName = $element->getModel()->name;
                            $element = ArrayHelper::toArray($element);
                            $element['serviceName'] = $serviceName;
                            $order['elements'][] = $element;

                            $basePrice += $element['base_price']*$element['count'];
                            $price += $element['price']*$element['count'];
                            
                            $summary['baseServicesTotal'] += $basePrice;
                            $summary['servicesTotal'] += $price;
                            
                            $summary['elementsCount'] = $summary['elementsCount']+$element['count'];
                        }

                        $elementEvent = new Element(['cost' => $price, 'group' => $group]);
                        $this->trigger(self::EVENT_GROUP_CALCULATE, $elementEvent);
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
                            $elementEvent = new Element(['cost' => $customToBase, 'group' => $group]);
                            $this->trigger(self::EVENT_GROUP_CALCULATE, $elementEvent);
                            $customToBase = $elementEvent->cost;
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
                        if($worker['pay_type'] == 'base' | $worker['pay_type'] == 'overbase') {
                            //Исполнитель
                            if(in_array($worker['category_id'], $this->workerCategoryIds)) {
                                $group['persent'] = $group['persent']-$worker['persent'];
                                $group['workersCount']--;
                            }
                            $workerSalary = $this->groupSalaryVariablity($group['sum']*($worker['persent']/100), $group, $session, $worker);
                            $group['workers'][$key]['salary'] = $workerSalary;
                        }
                        //С выручки
                        else {
                            $workerSalary = $this->groupSalaryVariablity($group['sum']*($worker['persent']/100), $group, $session, $worker);
                            $group['workers'][$key]['salary'] = $workerSalary;
                        }

                        $salary[$worker['id']] += $workerSalary;
                    }
                }

                //Перерасчитываем базу
                $group['base'] = $group['sum']*($group['persent']/100);

                //Начисляем ЗП обычным сотрудникам
                foreach($group['workers'] as $key => $worker) {
                    //Процент для мойщиков
                    if(!$worker['persent']) {
                        if(($worker['pay_type'] == 'base' | $worker['pay_type'] == 'overbase') && in_array($worker['category_id'], $this->workerCategoryIds)) {
                            $workerSalary = $this->groupSalaryVariablity($group['base']/$group['workersCount'], $group, $session, $worker);
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

        // Премии
        foreach($workers as $worker) {
            $bonuses[$worker->id] = 0;
            // при начислении зарплаты (см. ивент on stop в конфиге), помечаем премию как начисленную
            $bonusQuery = \Yii::$app->staffer->getStafferBonuses($worker->id);
            // TODO решить как считать. Либо собирать все
            $bonusQuery->andWhere(['canceled' => null]); // НЕ отменённые премии
            $bonusQuery->andWhere(['payed' => null]); // и НЕ начисленные в предыдущие зарплаты

            // либо считать по времени сессии, но тогда выпадают премии начисленные не в смену сотрудника
            // $bonusQuery->andWhere(['>=', 'created', $session->start]);
            // $bonusQuery->andWhere(['<=', 'created', $session->stop]);

            $bonusSum = $bonusQuery->sum('sum');
            if($bonusSum > 0) {
                $bonuses[$worker->id] = $bonusSum;
                $salary[$worker->id] += $bonusSum;
            }
        }

        //Начисляем фиксы
        foreach($workers as $worker) {
            if($fix = $worker->fix) {
                $salary[$worker->id] += $fix;
            }
        }

        $dataSalary = [];
        foreach($workers as $worker) {
            $dataSalary[$worker->id] = [];
            $dataSalary[$worker->id]['staffer'] = $worker;
            $dataSalary[$worker->id]['base_salary'] = round($baseSalary[$worker['id']], 2); //Грязная ЗП
            $dataSalary[$worker->id]['fines'] = round($fines[$worker['id']], 2); //Штрафы
            $dataSalary[$worker->id]['bonuses'] = $bonuses[$worker['id']]; //Бонусы

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

            $workerSalary = round($workerSalary, 1, PHP_ROUND_HALF_UP);

            $dataSalary[$worker->id]['salary'] = $workerSalary; //Чистая ЗП (со штрафами и бонусами)

            $summary['totalSalary'] += $workerSalary;

            $paymentSum = Payment::find()->where(['session_id' => $session->id, 'worker_id' => $worker['id']])->sum('sum');
            $dataSalary[$worker->id]['balance'] = round($workerSalary-$paymentSum, 0, PHP_ROUND_HALF_UP);
        }

        return ['orders' => $data, 'salary' => $dataSalary, 'summary' => $summary];
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
