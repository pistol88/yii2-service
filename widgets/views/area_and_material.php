<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


$array = explode(PHP_EOL,$settings);
$result = [];
foreach ($array as $arrayItem) {
    $temp = explode(':',$arrayItem);
    $temp[1] = (int) $temp[1];
    $type[] = [ 'value' => $temp[1],'title'=>$temp[0]];
}

foreach($type as $value) {
    $result = $result + [$value['value'] => $value['title']];
}
$param = [
    'id' => 'input-type',
    'class' => 'calculate-service-material form-control'
];
?>
<div class="row calculate-service-data-form form">
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::dropDownList('set-type','null',$result,$param); ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Длина</label>
            <input type="text" id="input-width" class="calculate-service-width form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Ширина</label>
            <input type="text" id="input-height" class="calculate-service-height form-control">
        </div>
    </div>

    <div class="col-md-6 col-md-offset-3">
        <div class="form-group">
            <div class="calculate-service-price"></div>
        </div>
    </div>

    <?php $form = ActiveForm::begin(['action' => Url::to(['/service/price/order']),'options' => ['enctype' => 'multipart/form-data', 'data-service-name' => $name],'id' => 'add-custom-service-form']); ?>
        <div class="row">
            <div class="col-md-8 hidden"><?php echo $form->field($customServiceModel, 'name')->textInput(['value'=> $name]) ?></div>
            <div class="col-md-4 hidden"><?php echo $form->field($customServiceModel, 'price')->textInput() ?></div>
        </div>
        <?php echo Html::submitButton('В корзину', ['class' => 'col-md-12 put-calculate-service-btn btn btn-success', 'disabled' => 'disabled']) ?>
    <?php ActiveForm::end(); ?>
</div>
