<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use pistol88\cart\widgets\ElementsList;
use pistol88\cart\widgets\CartInformer;

$this->title = 'Заказ услуги';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="price-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <br class="clear" />
    
    <p align="center"><small>Enter - отправить заказ</small></p>
    
    <div class="control row">
        <div class="col-md-9 ident">
            <div class="service-ident">
                <input type="text" name="service-ident" value="" id="service-ident" autocomplete="off" data-field-selector="<?=yii::$app->getModule('service')->mainIdentFieldSelector;?>" placeholder="<?=yii::$app->getModule('service')->mainIdent;?>" />
            </div>
        </div>
        <div class="col-md-3 types">
            <div class="order-type">
                <form action="" method="get">
                    <select class="form-control" name="service-order-type" onchange="$(this).parent('form').submit();">
                        <option value="table">Таблицей</option>
                        <option value="net" <?php if($type == 'net') { echo ' selected="selected"'; }?>>Сеткой</option>
                    </select>
                    
                </form>
            </div>
            
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-9 col-md-8  col-sm-12">
            <?=$this->render('order-type/'.$type, ['categories' => $categories, 'services' => $services, 'complexes' => $complexes, 'prices' => $prices]);?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="service-order">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Чек <span class="pistol88-cart-count"><?=yii::$app->cart->count;?></span></h3>
                    </div>
                    <div class="col-md-6">
                        <div class="promocode">
                            <?=\pistol88\promocode\widgets\Enter::widget();?>
                        </div>
                    </div>
                </div>
                
                <?=ElementsList::widget(['columns' => '3', 'showCountArrows' => false, 'type' => ElementsList::TYPE_FULL]);?>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="total">
                            <?= CartInformer::widget(['htmlTag' => 'span', 'offerUrl' => '/?r=cart', 'text' => '{c} на {p}']); ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        
                    </div>
                </div>

                <h3>Заказ</h3>
                <iframe src="about:blank" id="orderSubmitter" name="orderSubmitter" style="display:none;"></iframe>
                <?php $form = ActiveForm::begin(['options' => ['target' => 'orderSubmitter'], 'action' => ['/order/order/create'], 'id' => 'orderForm']); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div style="display: none;">
                                <?= $form->field($orderModel, 'status')->label(false)->textInput(['value' => 'new', 'type' => 'hidden', 'maxlength' => true]) ?>
                            </div>
                            <?= $form->field($orderModel, 'payment_type_id')->dropDownList($paymentTypes) ?>
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
                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <?= $form->field($orderModel, 'comment')->textArea(['maxlength' => true]) ?>
                        </div>
                    </div>
                
                    <div class="form-group offer">
                        <?= Html::submitButton($orderModel->isNewRecord ? Yii::t('order', 'Create order') : Yii::t('order', 'Update'), ['class' => $orderModel->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>