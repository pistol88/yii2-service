<?php
namespace pistol88\service\models;

use yii;
use pistol88\worksess\models\Session;

class Cost extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%service_cost}}';
    }

    public function rules()
    {
        return [
            [['name', 'sum'], 'required'],
            [['user_id', 'session_id'], 'integer'],
            [['date', 'name'], 'string'],
            [['sum'], 'double'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Администратор',
            'name' => 'На что',
            'session_id' => 'Сессия',
            'date' => 'Дата',
            'sum' => 'Сумма',
        ];
    }
    
    public function getSession()
    {
        return $this->hasOne(Session::className(), ['id' => 'session_id']);
    }
    
    public function beforeSave($insert)
    {
        if(empty($this->user_id)) {
            $this->user_id = yii::$app->user->id;
        }

        if(empty($this->date)) {
            $this->date = date('Y-m-d H:i:s');
        }

        if(empty($this->session_id) && $session = yii::$app->worksess->soon()) {
            $this->session_id = $session->id;
        }
        
        return parent::beforeSave($insert);
    }
}
