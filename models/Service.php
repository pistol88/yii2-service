<?php
namespace pistol88\service\models;

use yii;

class Service extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%service_service}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id'], 'integer'],
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
