<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use nex\datepicker\DatePicker;
use pistol88\service\models\Category;
use pistol88\client\models\Client;
use pistol88\gallery\widgets\Gallery;
use kartik\select2\Select2;

\pistol88\client\assets\BackendAsset::register($this);
?>

<div class="model-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-lg-2 col-md-3 col-xs-6">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
        <?php if($module->propertyStatuses) { ?>
            <div class="col-lg-2 col-md-4 col-xs-6">
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
        <div class="col-lg-2 col-md-3 col-xs-6">
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
        <div class="col-lg-2 col-md-3 col-xs-6">
            <?= $form->field($model, 'client_id')->textInput() ?>
            <?php if($model->client_id) { ?>
                <a href="<?=Url::toRoute(['/client/client/view', 'id' => $model->client_id]);?>"><i class="glyphicon glyphicon-eye-open"></i> <?=$model->client->name;?></a>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'comment')->textArea() ?>
        </div>
    </div>

    <?php /* Gallery::widget(['model' => $model]); */ ?>
    
    <div class="form-group client-control">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>
