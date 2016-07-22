<?php
namespace pistol88\service\models\price;

use yii;
use pistol88\service\models\Category;
use pistol88\service\models\Service;

class PriceQuery extends \yii\db\ActiveQuery
{
    public function tariff($categoryId, $serviceId)
    {
        return $this->andWhere(['category_id' => $categoryId, 'service_id' => $serviceId]);
    }
}