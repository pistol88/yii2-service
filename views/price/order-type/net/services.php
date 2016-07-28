<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use pistol88\cart\widgets\BuyButton;
use pistol88\cart\widgets\ChangeCount;
?>
<div class="service-order-net">
    <div class="header">
        <a href="<?=Url::toRoute(['/service/price/order']);?>">Выбор категории</a> / <?=$categoryModel->name;?> / Выбор услуги
    </div>
    <div class="body">
        <div class="services">
            <?php if($complexes) { ?>
                <h3>Комплексы</h3>
                <div class="row">
                    <?php foreach($complexes as $complex) { ?>
                        <?php $price = $priceModel::find()->tariff($categoryId, $complex)->one(); ?>
                        <div class="col-md-4 col-sm-6 col-lg-3 price" <?php if(!empty($price->description)) echo ' title="'.$price->description.'"'; ?>>
                            <input class="service-price"  <?php if(!empty($price->description)) echo ' style="border: 1px solid yellow;"'; ?> data-base-price="<?=$price->price;?>" type="text" name="text" value="<?=$price->price;?>" />
                            <strong>Комплекс «<?=$complex->name;?>»</strong> <small>(<?=implode(', ', ArrayHelper::map($complex->services, 'id', 'name'));?>)</small>
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
                </div>
                <hr />
            <?php } ?>
            <h3>Услуги</h3>
            <div class="row">
                <?php foreach($services as $service) { ?>
                    <?php $price = $priceModel::find()->tariff($categoryId, $service)->one(); ?>
                    <div class="col-md-4 col-sm-6 col-lg-3 price" <?php if(!empty($price->description)) echo ' title="'.$price->description.'"'; ?>>
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
                <?php } ?>
            </div>
        </div>
    </div>
</div>
