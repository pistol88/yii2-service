<?php
use yii\helpers\Html;
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
                <th width="40">#</th>
                <th width="200">Вид услуги</th>
                <?php foreach($categories as $category) { ?>
                    <th><?=$category->name;?></th>
                <?php } ?>
            </tr>
            <?php foreach($services as $service) { ?>
                <tr>
                    <td><?=$service->id;?></td>
                    <td><?=$service->name;?></td>
                    <?php foreach($categories as $category) { ?>
                        <td><input type="text" name="price[<?=$category->id;?>][<?=$service->id;?>]" value="<?=$priceModel->getTafiff($category, $service);?>" /></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        
        <p>
            <input type="submit" name="submit" value="Сохранить" class="btn btn-success" />
        </p>
    <?php ActiveForm::end(); ?>
</div>