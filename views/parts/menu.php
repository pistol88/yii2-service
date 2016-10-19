<?php
use yii\bootstrap\Nav;
?>
<div class="menu-container">
    <?= Nav::widget([
        'items' => yii::$app->getModule('service')->menu,
        'options' => ['class' =>'nav-pills'],
    ]); ?>
</div>