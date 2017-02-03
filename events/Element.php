<?php
namespace pistol88\service\events;

use yii\base\Event;

class Element extends Event
{
    public $group;
    public $cost;
    public $element;
    public $order;
}
