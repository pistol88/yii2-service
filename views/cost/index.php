<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Траты';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="category-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <p>
        <?php echo Html::a('Добавить трату', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 49px;']],
            'name',
            'sum',
            'date',
            [
                'attribute' => 'session_id',
                'filter' => false,
                'content' => function($model) {
                    return Html::a($model->session->start.' ('.$model->session->user->name.')', ['/service/report/index', 'sessionId' => $model->session->id]);
                }
            ],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 120px;']],
        ],
    ]); ?>

</div>
