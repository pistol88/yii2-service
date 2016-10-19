<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use pistol88\service\models\Service;

$services = Service::find()->where("id != :id AND (parent_id = 0 OR parent_id IS NULL)", [':id' => (int)$model->id])->all();
$services = ArrayHelper::map($services, 'id', 'name');
$parentServices = array_merge(['0' => 'Нет'], $services);
?>

<div class="service-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
    <p><small>Чем выше приоритет, тем выше элемент среди других в общем списке.</small></p>
    
    <?= $form->field($model, 'parent_id')->dropdownList($parentServices);?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
