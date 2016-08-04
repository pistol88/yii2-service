<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use pistol88\worksess\widgets\ControlButton;
use pistol88\worksess\widgets\Info;
use pistol88\worksess\widgets\SessionGraph;

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
    <div class="administrator-place">
        <?=Info::widget();?>

        <?=ControlButton::widget();?>
    </div>

    <h2>Сотрудники</h2>
    <?=SessionGraph::widget(['hoursCount' => $module->shiftDuration]);?>
</div>
