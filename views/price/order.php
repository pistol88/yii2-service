<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Alert;
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
                <?php if($order) { ?>
                    <?=Alert::widget([
                        'options' => [
                            'class' => 'alert-success',
                        ],
                        'body' => 'Внимание! Вы добавляете новые элементы к существующему заказа №'.$order->id,
                    ]);?>
                <?php } else { ?>
                    <div class="service-ident">
                        <input type="text" name="service-ident" value="" id="service-ident" autocomplete="off" data-field-selector="<?=yii::$app->getModule('service')->mainIdentFieldSelector;?>" placeholder="<?=$ident;?>" />
                    </div>
                <?php } ?>
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

    <div class="summary-slide" style="display: none;">
        <div class="total col-md-4">
            <?= CartInformer::widget(['htmlTag' => 'span', 'text' => '{c} на {p}']); ?>
        </div>
        <div class="total col-md-4">
            <button class="btn btn-success">Создать заказ</button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-9 col-md-8  col-sm-12 service-list">
            <div class="row">
                <div class="col-md-12 other-services">
                    <?php //BuyByCode::widget();?>
                    <small>
                        <!--a href="#productsModal" data-toggle="modal" data-target="#productsModal" class="btn btn-default choice-product  ">Витрина <span class="glyphicon glyphicon-plus add-option"></span></a-->
                         <a data-toggle="modal" data-target="#custom-service" href="#custom-service" class="btn btn-default choice-service" title="Другое">Другое <i class="glyphicon glyphicon-plus"></i> </a>
                        <a data-toggle="modal" data-target="#calculate-service" href="#calculate-service" class="btn btn-default choice-service" title="Вычисляемые услуги">Калькулятор <i class="glyphicon glyphicon-plus"></i> </a>
                       
                    </small>
                </div>
            </div>
            <?=$this->render('order-type/'.$type, ['categories' => $categories, 'services' => $services, 'complexes' => $complexes, 'prices' => $prices]);?>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12">
            <div class="arm-right-column">
            <div class="service-order">
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
                            <?= CartInformer::widget(['htmlTag' => 'span', 'text' => '{c} на {p}']); ?>
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
                    <?php if($order) { ?>
                        <a href="<?=Url::toRoute(['/order/order/push-elements', 'id' => $order->id, 'push_cart' => true]);?>" data-method="post" class="btn btn-success">Добавить к заказу №<?=$order->id;?></a>
                    <?php } else { ?>
                        <?php if (Yii::$app->service->splitOrderPerfome) {
                                if (isset(yii::$app->worksess->soon()->users)) {
                                    $staffers = yii::$app->worksess->soon()->getUsers();
                                    $staffers = $staffers->all();
                                } else {
                                    $staffers = null;
                                }
                            } else {
                                $staffers = null;
                            }
                        ?>

                        <?= \pistol88\order\widgets\OrderFormLight::widget([
                                'useAjax' => true,
                                'staffer' => $staffers
                            ]);
                        ?>
                    <?php } ?>
                </div>
            </div>
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

<!--div class="modal fade" id="productsModal" tabindex="-1" role="dialog">
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
</div-->


<div style="display: none;">
    <?php foreach($categories as $category) { ?>
        <div class="category">
            <a href="<?=Url::toRoute(['/service/price/get-prices']);?>" class="service-category service-category-<?=$category->id;?>" data-id="<?=$category->id;?>">
               <?=$category->name;?>

            </a>
        </div>
    <?php } ?>
</div>
