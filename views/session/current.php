<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use pistol88\worksess\widgets\ControlButton;
use pistol88\worksess\widgets\Info;

$this->title = 'Рабочая смена';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="session-index">
    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <?php if(Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php } ?>
    <?php if(Yii::$app->session->hasFlash('fail')) { ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('fail') ?>
        </div>
    <?php } ?>
    
    <h2>Смена</h2>
    <?=Info::widget();?>

    <p><?=ControlButton::widget();?></p>
    
    <h2>Рабочие</h2>
    
    <ul class="session-workers">
        <?php foreach($workers as $worker) { ?>
            <li class="row">
                <div class="col-md-4">
                    <p><strong><?=$worker->username;?></strong> <?php if($name = $worker->name) { ?>(<?=$name;?>)<?php } ?></p>
                    <?=Info::widget(['for' => $worker]);?>
                </div>
                <div class="col-md-2"><?=ControlButton::widget(['for' => $worker]);?></div>
            </li>
        <?php } ?>
    </ul>
    
</div>
