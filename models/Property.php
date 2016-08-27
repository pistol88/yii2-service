<?php
namespace pistol88\service\models;

use Yii;
use pistol88\client\models\Client;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

class Property extends \yii\db\ActiveRecord
{
    function behaviors()
    {
        return [
            'field' => [
                'class' => 'pistol88\field\behaviors\AttachFields',
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    public static function tableName()
    {
        return '{{%service_property}}';
    }
    
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category_id', 'client_id'], 'integer'],
            [['status', 'comment'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => yii::$app->getModule('service')->propertyName,
            'category_id' => 'Категория',
            'client_id' => 'Клиент',
            'status' => 'Статус',
            'name' => yii::$app->getModule('service')->identName,
            'comment' => 'Комментарий',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата редактирования',
        ];
    }
    
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
    
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
