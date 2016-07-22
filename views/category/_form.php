<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>
        <?php echo $form->errorSummary($model); ?>

        <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
