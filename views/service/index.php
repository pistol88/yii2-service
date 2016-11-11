<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use pistol88\service\modules\Service;

$this->title = 'Услуги';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);

if(yii::$app->has('organization')) {
    $organizations = yii::$app->organization->getList();
    $organizations = ArrayHelper::map($organizations, 'id', 'name');
} else {
    $organizations = [];
}

?>
<div class="service-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <p>
        <?php echo Html::a('Добавить услугу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 49px;']],
            'name',
            [
                'attribute' => 'organization_id',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'organization_id',
                    $organizations,
                    ['class' => 'form-control', 'prompt' => 'Организация']
                ),
                'content' => function($model) use ($organizations) {
                    foreach($organizations as $id => $name) {
                        if($id == $model->organization_id) {
                            return $name;
                        }
                    }
                    
                    return '';
                }
            ],
            ['attribute' => 'service.name', 'label' => 'Материнская услуга'],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 100px;']],
        ],
    ]); ?>

</div>
