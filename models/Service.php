<?php
namespace pistol88\service\models;

use yii;
use \pistol88\service\interfaces\Service as ServiceInterface;

class Service extends \yii\db\ActiveRecord implements ServiceInterface
{
    public static function tableName()
    {
        return '{{%service_service}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id', 'parent_id', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'parent_id' => 'Материнская услуга',
            'sort' => 'Приоритет',
        ];
    }
    
    public function getService()
    {
        return $this->hasOne(self::className(), ['parent_id' => 'id']);
    }
    
    public function getId()
    {
        return $this->id;
    }
}
