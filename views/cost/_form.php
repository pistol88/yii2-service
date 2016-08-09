<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(['action' => ['/service/cost/create'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
        <?php echo $form->errorSummary($model); ?>

        <?php echo $form->field($model, 'name')->textInput() ?>
    
        <?php echo $form->field($model, 'sum')->textInput() ?>

        <div class="form-group">
            <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
