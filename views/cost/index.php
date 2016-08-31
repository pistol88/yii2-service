<?php
use yii\helpers\Html;
use yii\grid\GridView;
use nex\datepicker\DatePicker;

$this->title = 'Траты';
$this->params['breadcrumbs'][] = $this->title;

if($dateStart = yii::$app->request->get('date_start')) {
    $dateStart = date('Y-m-d', strtotime($dateStart));
}

if($dateStop = yii::$app->request->get('date_stop')) {
    $dateStop = date('Y-m-d', strtotime($dateStop));
}

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="category-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Новая трата</h3>
        </div>
        <div class="panel-body">
            <div class="col-lg-4">
                <?php echo $this->render('_form', [
                    'model' => $model,
                    'module' => $module,
                ]) ?>
            </div>
        </div>
    </div>
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?=yii::t('order', 'Search');?></h3>
        </div>
        <div class="panel-body">
            <form action="" class="row search">
                <input type="hidden" name="CostSearch[name]" value="" />
                 <div class="col-md-4">
                     <div class="row">
                         <div class="col-md-6">
                             <?= DatePicker::widget([
                                 'name' => 'date_start',
                                 'addon' => false,
                                 'value' => $dateStart,
                                 'size' => 'sm',
                                 'language' => 'ru',
                                 'placeholder' => yii::t('order', 'Date from'),
                                 'clientOptions' => [
                                     'format' => 'L',
                                     'minDate' => '2015-01-01',
                                     'maxDate' => date('Y-m-d'),
                                 ],
                                 'dropdownItems' => [
                                     ['label' => 'Yesterday', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('-1 day')],
                                     ['label' => 'Tomorrow', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('+1 day')],
                                     ['label' => 'Some value', 'url' => '#', 'value' => 'Special value'],
                                 ],
                             ]);?>
                         </div>
                         <div class="col-md-6">
                             <?= DatePicker::widget([
                                 'name' => 'date_stop',
                                 'addon' => false,
                                 'value' => $dateStop,
                                 'size' => 'sm',
                                 'placeholder' => yii::t('order', 'Date to'),
                                 'language' => 'ru',
                                 'clientOptions' => [
                                     'format' => 'L',
                                     'minDate' => '2015-01-01',
                                     'maxDate' => date('Y-m-d'),
                                 ],
                                 'dropdownItems' => [
                                     ['label' => yii::t('order', 'Yesterday'), 'url' => '#', 'value' => \Yii::$app->formatter->asDate('-1 day')],
                                     ['label' => yii::t('order', 'Tomorrow'), 'url' => '#', 'value' => \Yii::$app->formatter->asDate('+1 day')],
                                     ['label' => yii::t('order', 'Some value'), 'url' => '#', 'value' => 'Special value'],
                                 ],
                             ]);?>
                         </div>
                     </div>
                 </div>

                 <div class="col-md-2">
                     <input class="form-control" type="submit" value="<?=Yii::t('order', 'Search');?>" class="btn btn-success" />
                 </div>
             </form>
        </div>
    </div>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 49px;']],
            'name',
            'sum',
            [
                'attribute' => 'date',
                'filter' => false,
            ],
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
