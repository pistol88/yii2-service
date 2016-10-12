<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<div class="worker-payments-widget">
    <div class="summary">
        Всего:
        <?=number_format($dataProvider->query->sum('sum'), 2, ',', '.');?>
    </div>
    <?php Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 49px;']],
            ['attribute' => 'sum', 'filter' => false],
            'date',
            [
                'attribute' => 'session_id',
                'filter' => false,
                'content' => function($model) {
                    if($model) Html::a($model->session->start.' ('.$model->session->user->name.')', ['/service/report/index', 'sessionId' => $model->session->id]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
