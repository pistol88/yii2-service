<?php
namespace pistol88\service\models;

use Yii;

class Complex extends \yii\db\ActiveRecord implements \pistol88\service\interfaces\Service
{
    public function behaviors()
    {
        return [
            'category' => [
                'class' => \voskobovich\manytomany\ManyToManyBehavior::className(),
                'relations' => [
                    'service_ids' => 'services',
                ],
            ],
        ];
    }
    
    public static function tableName()
    {
        return '{{%service_complex%}}';
    }

    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['sort'], 'integer'],
            [['service_ids'], 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'sort' => 'Приоритет',
            'service_ids' => 'Услуги',
        ];
    }
    
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
                    ->viaTable('service_to_complex', ['complex_id' => 'id']);
    }
    
    public function getId()
    {
        return $this->id;
    }
}
