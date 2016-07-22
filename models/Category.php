<?php
namespace pistol88\service\models;

use yii;

class Category extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%service_category}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
        ];
    }
}
