<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use pistol88\cart\widgets\BuyButton;
use pistol88\cart\widgets\ChangeCount;
?>
<table class="table table-hover table-responsive service-prices-table">
    <tr>
        <td width="200">Вид услуги</td>
        <?php foreach($categories as $category) { ?>
            <td><strong><?=$category->name;?></strong></td>
        <?php } ?>
    </tr>
    <?php foreach($complexes as $complex) { ?>
        <tr>
            <td><strong>Комплекс «<?=$complex->name;?>»</strong> <small>(<?=implode(', ', ArrayHelper::map($complex->services, 'id', 'name'));?>)</small></td>
            <?php foreach($categories as $category) { ?>
                <?php if($price = $prices[$complex::className()][$category->id][$complex->id]) { ?>
                    <td class="price" title="<?=$price->description;?>">
                        <input class="service-price" <?php if(!empty($price->description)) echo ' style="border: 1px solid yellow;"'; ?> data-base-price="<?=$price->price;?>" type="text" name="text" value="<?=$price->price;?>" />
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
    <?php foreach($services as $service) { ?>
        <tr>
            <td><?=$service->name;?></td>
            <?php foreach($categories as $category) { ?>
                <?php if($price = $prices[$service::className()][$category->id][$service->id]) { ?>
                    <td class="price" title="<?=$price->description;?>">
                        <input class="service-price" <?php if(!empty($price->description)) echo ' style="border: 1px solid yellow;"'; ?> data-base-price="<?=$price->price;?>" type="text" name="text" value="<?=$price->price;?>" />
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