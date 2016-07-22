<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use pistol88\cart\widgets\BuyButton;
use pistol88\cart\widgets\ElementsList;
use pistol88\cart\widgets\ChangeCount;
use pistol88\cart\widgets\CartInformer;
use pistol88\order\models\FieldValue;

$this->title = 'Заказ услуги';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="price-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <br class="clear" />
    
    <div class="row">
        <div class="col-lg-8">
            <table class="table table-hover table-responsive service-prices-table">
                <tr>
                    <td width="200">Вид услуги</td>
                    <?php foreach($categories as $category) { ?>
                        <td><strong><?=$category->name;?></strong></td>
                    <?php } ?>
                </tr>
                <?php foreach($services as $service) { ?>
                    <tr>
                        <td><?=$service->name;?></td>
                        <?php foreach($categories as $category) { ?>
                            <?php if($price = $priceModel::find()->tariff($category->id, $service->id)->one()) { ?>
                                <td class="price">
                                    <input data-base-price="<?=$price->price;?>" type="text" name="text" value="<?=$price->price;?>" />
                                    <?= BuyButton::widget([
                                        'model' => $price,
                                        'text' => '<i class="glyphicon glyphicon-shopping-cart"></i>',
                                        'htmlTag' => 'a',
                                        'cssClass' => 'btn btn-default'
                                    ]) ?>
                                </td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr>
                    <td width="200">Вид услуги</td>
                    <?php foreach($categories as $category) { ?>
                        <td><strong><?=$category->name;?></strong></td>
                    <?php } ?>
                </tr>
            </table>
        </div>
        <div class="col-lg-4">
            <div class="service-order">
                <h2>Корзина</h2>
                <?=ElementsList::widget(['type' => ElementsList::TYPE_FULL]);?>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="total">
                            <?= CartInformer::widget(['htmlTag' => 'span', 'offerUrl' => '/?r=cart', 'text' => '{c} на {p}']); ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="promocode">
                            <p>Скидочный код</p>
                            <?=\pistol88\promocode\widgets\Enter::widget();?>
                        </div>
                    </div>
                </div>

                <h2>Заказ</h2>
                
                <?php $form = ActiveForm::begin(['action' => ['/order/order/create'], 'id' => 'orderForm']); ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <?= $form->field($orderModel, 'payment_type_id')->dropDownList($paymentTypes) ?>
                        </div>
                        <div class="col-lg-6">
                            <?= $form->field($orderModel, 'status')->dropDownList(Yii::$app->getModule('order')->orderStatuses) ?>
                        </div>
                    </div>

                    <?php if($fields = $orderModel->allfields) { ?>
                        <div class="row">
                            <?php foreach($fields as $fieldModel) { ?>
                                <div class="col-lg-12 col-xs-12">
                                    <?php
                                    if($widget = $fieldModel->type->widget) {
                                        echo $widget::widget(['form' => $form, 'fieldModel' => $fieldModel]);
                                    }
                                    else {
                                        echo $form->field(new FieldValue, 'value['.$fieldModel->id.']')->label($fieldModel->name)->textInput(['required' => ($fieldModel->required == 'yes')]);
                                    }
                                    ?>
                                    <?php if($fieldModel->description) { ?>
                                        <p><small><?=$fieldModel->description;?></small></p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?= $form->field($orderModel, 'comment')->textArea(['maxlength' => true]) ?>
                
                    <div class="form-group offer">
                        <?= Html::submitButton($orderModel->isNewRecord ? Yii::t('order', 'Create order') : Yii::t('order', 'Update'), ['class' => $orderModel->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>