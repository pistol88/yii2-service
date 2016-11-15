<?php
namespace pistol88\service\events;

use yii\base\Event;

class Salary extends Event
{
    public $session;
    public $worker;
    public $total;
    public $salary;
    public $bonus = 0;
    public $fine = 0;
}