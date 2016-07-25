<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Тарифы';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="price-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <?php $form = ActiveForm::begin(); ?>
        <p>
            <input type="submit" name="submit" value="Сохранить" class="btn btn-success" />
        </p>

        <table class="table table-hover table-responsive service-prices-table">
            <tr>
                <th width="40">ID</th>
                <th width="200">Вид услуги</th>
                <?php foreach($categories as $category) { ?>
                    <th><?=$category->name;?></th>
                <?php } ?>
            </tr>
            <?php foreach($complexes as $complex) { ?>
                <tr>
                    <td>K<?=$complex->id;?></td>
                    <td><strong>Комплекс «<?=$complex->name;?>»</strong></td>
                    <?php foreach($categories as $category) { ?>
                        <?php $priceModel = $priceModel->getTariff($category, $complex); ?>
                        <td>
                            <input style="width: 70%;" type="text" name="price[<?=$category->id;?>][<?=$complex::className();?>][<?=$complex->id;?>]" value="<?=$priceModel->price;?>" />
                            <?php if($priceModel->id) { ?>
                                <a href="<?=Url::toRoute(['update', 'id' => $priceModel->id]);?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <?php foreach($services as $service) { ?>
                <tr>
                    <td><?=$service->id;?></td>
                    <td><?=$service->name;?></td>
                    <?php foreach($categories as $category) { ?>
                        <?php $priceModel = $priceModel->getTariff($category, $service); ?>
                        <td>
                            <input style="width: 70%;" type="text" name="price[<?=$category->id;?>][<?=$service::className();?>][<?=$service->id;?>]" value="<?=$priceModel->price;?>" />
                            <?php if($priceModel->id) { ?>
                                <a href="<?=Url::toRoute(['update', 'id' => $priceModel->id]);?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        
        <p>
            <input type="submit" name="submit" value="Сохранить" class="btn btn-success" />
        </p>
    <?php ActiveForm::end(); ?>
</div>