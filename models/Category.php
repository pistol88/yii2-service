<?php
namespace pistol88\service\models;

use yii;

class Category extends \yii\db\ActiveRecord 
{
    function behaviors()
    {
        return [
            'images' => [
                'class' => 'pistol88\gallery\behaviors\AttachImages',
                'mode' => 'gallery',
            ],
        ];
    }
    
    public static function tableName()
    {
        return '{{%service_category}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'sort' => 'Приоритет',
            'parent_id' => 'Материнская категория',
        ];
    }
    
    public function getCategory()
    {
        return $this->hasOne(self::className(), ['parent_id' => 'id']);
    }
}
