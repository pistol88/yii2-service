<?php

namespace pistol88\service\models;

use Yii;

/**
 * This is the model class for table "service_staffer_to_service".
 *
 * @property integer $id
 * @property string $staffer_model
 * @property integer $staffer_id
 * @property string $service_model
 * @property integer $service_id
 * @property integer $session_id
 * @property string $datetime
 */
class StafferToService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_staffer_to_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['staffer_id', 'service_id'], 'required'],
            [['staffer_id', 'service_id', 'session_id'], 'integer'],
            [['datetime'], 'safe'],
            [['staffer_model', 'service_model'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'staffer_model' => 'Модель исполнителя',
            'staffer_id' => 'ID исполнителя',
            'service_model' => 'Модель сервиса',
            'service_id' => 'ID сервиса',
            'session_id' => 'ID сессии',
            'datetime' => 'Дата',
        ];
    }

}
