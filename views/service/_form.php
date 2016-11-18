<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use pistol88\service\models\Service;
$script = <<< EOD
        if ($('#service-calculator').val()) { $('#calculate').attr("checked","checked"); $('#calculateBlock').show().fadeIn();}
        $('#calculate').on('click',function () {
            if ($('#calculate').is(':checked')) $('#calculateBlock').show().fadeIn();
            else $('#calculateBlock').hide().fadeOut();
    });
EOD;
$this->registerJs($script);
$services = Service::find()->where("id != :id AND (parent_id = 0 OR parent_id IS NULL)", [':id' => (int)$model->id])->all();
$services = ArrayHelper::map($services, 'id', 'name');
$parentServices = $services;
$parentServices[0] = 'Нет';

if(!$model->parent_id) {
    $model->parent_id = 0;
}
?>

<div class="service-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if(yii::$app->has('organization') && $organization = yii::$app->get('organization')) { ?>
        <?php echo $form->field($model, 'organization_id')->dropDownList(array_merge(['' => 'Нет'], ArrayHelper::map($organization->getList(), 'id', 'name'))) ?>
    <?php } ?>
    
    <?php echo $form->field($model, 'sort')->textInput(['maxlength' => true])->hint('Чем выше приоритет, тем выше элемент среди других в общем списке.'); ?>

    <?= $form->field($model, 'parent_id')->dropdownList($parentServices);?>

    <label><input type="checkbox" name="calc" id="calculate"> Вычисляемая услуга</label>
    <div id="calculateBlock" class="form-group" style="display: none;">
        <?= $form->field($model, 'calculator')->dropdownList(Yii::$app->service->getCalculateWidgets(), ['prompt' => 'Выберите виджет']); ?>

        <?= $form->field($model, 'settings')->textarea(['rows' => 3, 'cols' => 7, 'placeholder' => 'Опция: значение']); ?>
    </div>
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
