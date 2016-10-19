<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="service-order-net">
    <div class="header">
        <a href="<?=Url::toRoute(['/service/price/get-categories']);?>" class="back">Выбор категории</a> /
    </div>
    <div class="body">
        <div class="categories">
            <?php foreach($categories as $category) { ?>
                <div class="col-md-4 col-sm-6 col-lg-3 category">
                    <a href="<?=Url::toRoute(['/service/price/get-prices']);?>" data-title="<?=Html::encode($category->name);?>" data-id="<?=$category->id;?>">
                        <?=$category->name;?>
                        <img src="<?=$category->image->url;?>" width="100%" />
                    </a>
                    <?php if($childs = $category->childs) { ?>
                        <div class="childs" style="display: none;">
                            <?php foreach($childs as $category) { ?>
                                <div class="col-md-4 col-sm-6 col-lg-3 category">
                                    <a href="<?=Url::toRoute(['/service/price/get-prices']);?>" data-id="<?=$category->id;?>">
                                        <span><?=$category->name;?></span>
                                        <img src="<?=$category->image->url;?>" width="100%" />
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
