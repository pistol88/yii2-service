<?php
namespace pistol88\service\models;

use yii;

class Price extends \yii\db\ActiveRecord implements \pistol88\cart\interfaces\CartElement, \pistol88\order\interfaces\Product
{
    public static function tableName()
    {
        return '{{%service_price}}';
    }

    public function rules()
    {
        return [
            [['service_id', 'category_id', 'service_type'], 'required'],
            [['service_id', 'category_id'], 'integer'],
            [['service_type', 'description'], 'string'],
            [['price'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Услуга',
            'price' => 'Цена',
            'description' => 'Описание',
            'category_id' => 'Категории',
        ];
    }
    
    public function getCartId()
    {
        return $this->id;
    }

    public function getCartName()
    {
        return '<strong>'.$this->service->name.'</strong> <br /><small>'.$this->category->name.'</small>';
    }

    public function getShortName()
    {
        return $this->service->name;
    }
    
    public function getName()
    {
        return $this->service->name.' - '.$this->category->name;
    }
    
    public function getCartPrice()
    {
        return $this->price;
    }

    public function getCartOptions()
    {
        return [];
    }

    public function minusAmount($count)
    {
        return true;
    }
    
    public function plusAmount($count)
    {
        return true;
    }
    
    function getCode()
    {
        return $this->id;
    }

    function getPrice()
    {
        return $this->price;
    }
    
    function getAmount()
    {
        return 1;
    }

    function getSellModel()
    {
        return $this;
    }

    
    public function getService()
    {
        $service_type = $this->service_type;
        
        return $this->hasOne($service_type::className(), ['id' => 'service_id']);
    }
    
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    
    public static function find()
    {
        return new price\PriceQuery(get_called_class());
    }
    
    public function getTariff(Category $category, \pistol88\service\interfaces\Service $service)
    {
        if($price = static::find()->tariff($category->id, $service)->one()) {
            return $price;
        } else {
            return new Price;
        }
    }
}
