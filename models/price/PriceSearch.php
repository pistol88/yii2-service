<?php
namespace pistol88\service\models\price;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use pistol88\service\models\Price;

class PriceSearch extends Price
{
    public function rules()
    {
        return [
            [['id', 'service_id', 'category_id'], 'integer'],
            [['price'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Price::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'service_id' => $this->service_id,
            'category_id' => $this->category_id,
            'price' => $this->price,
        ]);

        return $dataProvider;
    }
}
