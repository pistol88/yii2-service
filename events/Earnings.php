<?php
namespace pistol88\service\events;

use yii\base\Event;

class Earnings extends Event
{
    public $worker;
    public $persent;
    public $total;
    public $userTotal;
    public $earning;
    public $workersCount;
    public $bonus = 0;
    public $fine = 0;
}