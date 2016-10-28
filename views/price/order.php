<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use pistol88\cart\widgets\ElementsList;
use pistol88\cart\widgets\CartInformer;
use pistol88\order\widgets\ChooseClient;
use pistol88\order\widgets\BuyByCode;
use pistol88\service\widgets\AreaAndMaterial;

$this->title = 'Заказ услуги';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
\pistol88\order\assets\CreateOrderAsset::register($this);

$this->registerJs("pistol88.createorder.updateCartUrl = '".Url::toRoute(['tools/cart-info'])."';");
?>
<div class="price-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <br class="clear" />

    <p align="center"><small>Enter - отправить заказ</small></p>

    <div class="control row">
        <div class="col-md-9 ident">
            <?php if($ident = yii::$app->getModule('service')->mainIdent) { ?>
                <div class="service-ident">
                    <input type="text" name="service-ident" value="" id="service-ident" autocomplete="off" data-field-selector="<?=yii::$app->getModule('service')->mainIdentFieldSelector;?>" placeholder="<?=$ident;?>" />
                </div>
            <?php } ?>
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
        <div class="col-lg-3 col-md-4 col-sm-12">
            <div class="service-order">
                <div class="row">
                    <div class="col-md-6">
                        <?php //BuyByCode::widget();?>
                    </div>
                    <div class="col-md-7">
                        <small>
                            <a data-toggle="modal" data-target="#calculate-service" href="#calculate-service" class="choice-service" title="Вычисляемые услуги">Вычисляемые услуги <i class="glyphicon glyphicon-plus"></i> </a>
                            <a href="#productsModal" data-toggle="modal" data-target="#productsModal" class="choice-product  ">Товар <span class="glyphicon glyphicon-plus add-option"></span></a>
                            <a data-toggle="modal" data-target="#custom-service" href="#custom-service" class="choice-service" title="Другое">Другое <i class="glyphicon glyphicon-plus"></i> </a>
                        </small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h3>Чек <span class="pistol88-cart-count"><?=yii::$app->cart->count;?></span></h3>
                    </div>
                    <div class="col-md-6 custom-service">
                    </div>
                </div>

                <?=ElementsList::widget(['columns' => '3', 'showCountArrows' => false, 'type' => ElementsList::TYPE_FULL]);?>

                <div class="row">
                    <div class="col-md-7">
                        <div class="total">
                            <?= CartInformer::widget(['htmlTag' => 'span', 'offerUrl' => '/?r=cart', 'text' => '{c} на {p}']); ?>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <?php
                        if(yii::$app->has('promocode')) { ?>
                            <div class="promocode">
                                <?=\pistol88\promocode\widgets\Enter::widget();?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <iframe src="about:blank" id="orderSubmitter" name="orderSubmitter" style="display:none;"></iframe>
                <!-- <iframe src="<?php // echo Url::to(['/order/order/create-from-iframe']); ?>" width="335" height="800" frameborder="0"></iframe> -->

                <div class="order-create-container" style="width: 279px;" id="order-creation-container">
                    <?= \pistol88\order\widgets\OrderFormLight::widget([
                            'useAjax' => true,
                        ]);
                    ?>
                </div>

                <?php // $form = ActiveForm::begin(['options' => ['target' => 'orderSubmitter', 'class' => 'panel-group-none'], 'action' => ['/order/order/create'], 'id' => 'orderForm']); ?>
                    <!-- <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a class="heading collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseTwo">
                                    Клиент
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false">
                            <div class="panel-body">
                                <?php // echo ChooseClient::widget(['form' => $form, 'model' => $orderModel]);?>
                                <select class="form-control service-choose-property">
                                    <option>Автомобиль...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="heading" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Заказ
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false">
                            <div class="row panel-body">
                                <div class="col-lg-12">
                                    <div style="display: none;">
                                        <?php // echo $form->field($orderModel, 'status')->label(false)->textInput(['value' => 'new', 'type' => 'hidden', 'maxlength' => true]) ?>
                                    </div>
                                    <?php // echo $form->field($orderModel, 'payment_type_id')->dropDownList($paymentTypes) ?>
                                </div>
                                <?php /* if($fields = $orderModel->allfields) { ?>
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
                                <?php } */ ?>
                                <div class="row">
                                    <div class="col-lg-12 col-xs-12">
                                        <?php // echo $form->field($orderModel, 'comment')->textArea(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                    <div class="form-group offer">
                        <?php // echo Html::submitButton($orderModel->isNewRecord ? Yii::t('order', 'Create order') : Yii::t('order', 'Update'), ['class' => $orderModel->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'test']); ?>
                    </div> -->

                <?php // ActiveForm::end(); ?>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="custom-service" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Уникальная услуга</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'],'id' => 'add-custom-service-form']); ?>
                    <?php if(Yii::$app->session->hasFlash('customServiceBuy')) { ?>
                        <div class="alert alert-success" role="alert">
                            <?= Yii::$app->session->getFlash('customServiceBuy') ?>
                        </div>
                        <script type="text/javascript">if (typeof pistol88 != "undefined" && pistol88) { pistol88.createorder.updateCart(); }</script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-8"><?php echo $form->field($customServiceModel, 'name')->textInput() ?></div>
                        <div class="col-md-4"><?php echo $form->field($customServiceModel, 'price')->textInput() ?></div>
                    </div>
                    <?php echo Html::submitButton('В корзину', ['class' => 'btn btn-success']) ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=yii::t('order', 'Close');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="calculate-service" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Вычисляемая услуга</h4>
            </div>
            <div class="modal-body">
                <div class="modal-rows">
                    <?php foreach($calculateServiceModel as $calculateService) { ?>
                        <div class="col-md-3 calculate-service-model btn btn-default" data-url="<?= Url::to(['price/get-calculate-service-form-ajax', 'id' => $calculateService->id]); ?>">
                                <strong><?=$calculateService->name;?></strong>
                        </div>
                    <?php } ?>
                    <div data-role="CalculateServiceForm"></div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=yii::t('order', 'Close');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=yii::t('order', 'Products');?></h4>
            </div>
            <div class="modal-body">
                <iframe src="<?=Url::toRoute(['/order/tools/find-products-window']);?>" id="products-list-window"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=yii::t('order', 'Close');?></button>
            </div>
        </div>
    </div>
</div>


<div style="display: none;">
    <?php foreach($categories as $category) { ?>
        <div class="category">
            <a href="<?=Url::toRoute(['/service/price/get-prices']);?>" class="service-category service-category-<?=$category->id;?>" data-id="<?=$category->id;?>">
               <?=$category->name;?>

            </a>
        </div>
    <?php } ?>
</div>
