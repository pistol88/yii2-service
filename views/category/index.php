<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use pistol88\service\models\Category;

$this->title = 'Категории услуг';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);

$categories = Category::find()->where("id != :id AND (parent_id = 0 OR parent_id IS NULL)", [':id' => (int)$model->id])->all();
$categories = ArrayHelper::map($categories, 'id', 'name');

if(yii::$app->has('organisation')) {
    $organisations = yii::$app->organisation->getList();
    $organisations = ArrayHelper::map($organisations, 'id', 'name');
} else {
    $organisations = [];
}

?>
<div class="category-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <p>
        <?php echo Html::a('Добавить категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 49px;']],
            'name',
            [
                'attribute' => 'organisation_id',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'organisation_id',
                    $organisations,
                    ['class' => 'form-control', 'prompt' => 'Организация']
                ),
                'content' => function($model) use ($organisations) {
                    foreach($organisations as $id => $name) {
                        if($id == $model->organisation_id) {
                            return $name;
                        }
                    }
                    
                    return '';
                }
            ],
            [
                'attribute' => 'parent.name',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'parent_id',
                    $categories,
                    ['class' => 'form-control', 'prompt' => 'Материнская категория']
                ),
            ],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 100px;']],
        ],
    ]); ?>

</div>
