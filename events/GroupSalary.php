<?php
namespace pistol88\service\events;

use yii\base\Event;

class GroupSalary extends Event
{
    public $session;
    public $worker;
    public $salary;
    public $group;
}
