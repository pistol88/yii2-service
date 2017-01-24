<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use pistol88\cart\widgets\BuyButton;
use pistol88\cart\widgets\ChangeCount;
?>
<div class="service-order-net">
    <div class="header">
        <a href="<?=Url::toRoute(['/service/price/get-categories']);?>" class="back"> <i class="glyphicon glyphicon-chevron-left"></i> Выбор категории</a> /
        <?php if($parent = $categoryModel->parent) { ?>
            <strong><?=$parent->name;?></strong> /
        <?php } ?>
        <strong><?=$categoryModel->name;?></strong> / Выбор услуги
    </div>
    <div class="body">
        <div class="services">
            <?php if($complexes) { ?>
                <h3>Комплексы</h3>
                <div class="row">
                    <?php foreach($complexes as $complex) { ?>
                        <?php if($price = $priceModel::find()->tariff($categoryId, $complex)->one()) { ?>
                            <?php if(yii::$app->getModule('service')->hideEmptyPrice && $price->price <= 0) { ?>
                            
                            <?php } else { ?>
                                <div class="col-md-4 col-sm-6 col-lg-3 price" <?php if(!empty($price->description)) echo ' title="'.$price->description.'"'; ?>>
                                    <input class="service-price"  <?php if(!empty($price->description)) echo ' style="border: 1px solid yellow;"'; ?> data-base-price="<?=$price->price;?>" type="text" name="text" value="<?=$price->price;?>" />
                                    <strong>«<?=$complex->name;?>»</strong> <small>(<?=implode(', ', ArrayHelper::map($complex->services, 'id', 'name'));?>)</small>
                                    <?php if($price) { ?>
                                        <?= BuyButton::widget([
                                            'model' => $price,
                                            'text' => '<i class="glyphicon glyphicon-shopping-cart"></i>',
                                            'htmlTag' => 'a',
                                            'cssClass' => 'btn btn-default'
                                        ]) ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>

            
            <h3>Услуги</h3>
            <div class="row">
                <?php foreach($services as $service) { ?>
                    <?php if($price = $priceModel::find()->tariff($categoryId, $service)->one()) { ?>
                        <?php if(yii::$app->getModule('service')->hideEmptyPrice && $price->price <= 0) { ?>
                        
                        <?php } else { ?>
                            <div class="col-md-4 col-sm-6 col-lg-3 price" <?php if(!empty($price->description)) echo ' title="'.$price->description.'"'; ?>>
                                <div class="row">
                                    <input class="service-price"  <?php if(!empty($price->description)) echo ' style="border: 1px solid yellow;"'; ?> data-base-price="<?=$price->price;?>" type="text" name="text" value="<?=$price->price;?>" />
                                        <strong><?=$service->name;?></strong>
                                        <?php if($price) { ?>
                                            <?= BuyButton::widget([
                                                'model' => $price,
                                                'text' => '<i class="glyphicon glyphicon-shopping-cart"></i>',
                                                'htmlTag' => 'a',
                                                'cssClass' => 'btn btn-default'
                                                ]) ?>
                                                <?php } ?>
                                </div>
                                <?php if(!empty($price->description)) { ?>
                                    <div class="row text-right">
                                        <?php $tooltip = '<p>'.$price->description.'</p>';

                                            echo Html::tag('span', '', [
                                                'data-template' => '<div class="popover" style="max-width: 165px; max-height: 75px;" role="tooltip"><div class="popover-arrow"></div><div class="popover-content"></div></div>',
                                                'data-title' => 'Заработано',
                                                'data-html' => 'true',
                                                'data-content' => $tooltip,
                                                'data-toggle' => 'popover',
                                                'data-placement' => 'left',
                                                'tabindex' => '0',
                                                'data-trigger' => 'hover',
                                                'style' => 'cursor:pointer;',
                                                'class' => 'glyphicon glyphicon-info-sign href',
                                            ]);
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    console.log('test');
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
    $(function () {
        $("[data-toggle='popover']").popover();
    });
</script>
