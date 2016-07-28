<?php
use yii\helpers\Url;
?>
<div class="service-order-net">
    <div class="header">
        <a href="<?=Url::toRoute(['/service/price/get-categories']);?>" class="back">Выбор категории</a> /
    </div>
    <div class="body">
        <div class="categories">
            <?php foreach($categories as $category) { ?>
                <div class="col-md-4 col-sm-6 col-lg-3 category">
                    <a href="<?=Url::toRoute(['/service/price/get-prices']);?>" data-id="<?=$category->id;?>">
                        <?=$category->name;?>
                        <img src="<?=$category->image->url;?>" width="100%" />
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
