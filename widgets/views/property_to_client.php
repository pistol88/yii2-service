<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use pistol88\service\models\Category;
use pistol88\client\models\Client;
use kartik\select2\Select2;
?>
<div class="property-to-client-widget">
    
    <?php Modal::begin([
        'header' => '<h2>Добавить</h2>',
        'toggleButton' => ['class' => 'btn btn-success', 'label' => 'Добавить'],
    ]);
    ?>

    <?php $form = ActiveForm::begin(['action' => ['/service/property/ajax-create'], 'options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-xs-6">
                <?= $form->field($model, 'name')->textInput() ?>
            </div>
            <?php if($module->propertyStatuses) { ?>
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <?php
                    if(!$model->status) {
                        $model->status = 'active';
                    }
                    ?>
                    <?= $form->field($model, 'status')
                        ->widget(Select2::classname(), [
                        'data' => $module->propertyStatuses,
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Выберите статус ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
            <?php } ?>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= $form->field($model, 'category_id')
                    ->widget(Select2::classname(), [
                    'data' => Category::buildTextTree(),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите категорию ...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= $form->field($model, 'comment')->textArea() ?>
            </div>
        </div>

        <?= $form->field($model, 'client_id')->label(false)->textInput(['value' => $client->id, 'type' => 'hidden']) ?>

        <?php /* Gallery::widget(['model' => $model]); */ ?>

        <div class="form-group client-control">
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
    
    <?php Modal::end(); ?>
    
    <?php Pjax::begin(); ?>

    <a href="" class="service-property-update"> </a>

    <?php
    echo \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export' => false,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 55px;']],
            'name',
            'client.name',
            [
                'attribute' => 'category_id',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'category_id',
                    Category::buildTextTree(),
                    ['class' => 'form-control', 'prompt' => 'Категория']
                ),
                'value' => 'category.name'
            ],
            [
                'attribute' => 'status',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    $module->propertyStatuses,
                    ['class' => 'form-control', 'prompt' => 'Статус']
                ),
                'content' => function($model) use ($module) {
                    return @$module->propertyStatuses[$model->status];
                }
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 155px;']],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>