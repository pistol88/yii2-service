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
    
    public static function buldTree($parent_id = null)
    {
        $return = [];
        
        if(empty($parent_id)) {
            $categories = Category::find()->where('parent_id = 0 OR parent_id is null')->orderBy('sort DESC')->asArray()->all();
        } else {
            $categories = Category::find()->where(['parent_id' => $parent_id])->orderBy('sort DESC')->asArray()->all();
        }
        
        foreach($categories as $level1) {
            $return[$level1['id']] = $level1;
            $return[$level1['id']]['childs'] = self::buldTree($level1['id']);
        }
        
        return $return;
    }
    
    public static function buildTextTree($id = null, $level = 1, $ban = [])
    {
        $return = [];
        
        $prefix = str_repeat('--', $level);
        $level++;
        
        if(empty($id)) {
            $categories = Category::find()->where('parent_id = 0 OR parent_id is null')->orderBy('sort DESC')->asArray()->all();
        } else {
            $categories = Category::find()->where(['parent_id' => $id])->orderBy('sort DESC')->asArray()->all();
        }
        
        foreach($categories as $category) {
            if(!in_array($category['id'], $ban)) {
                $return[$category['id']] = "$prefix {$category['name']}";
                $return = $return + self::buildTextTree($category['id'], $level, $ban);
            }
        }
        
        return $return;
    }
}
