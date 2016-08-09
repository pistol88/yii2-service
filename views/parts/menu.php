<?php
use yii\bootstrap\Nav;
?>
<div class="menu-container">
    <?= Nav::widget([
        'items' => [
            [
                'label' => 'Заказ',
                'url' => ['/service/price/order'],
            ],
            [
                'label' => 'Отчеты',
                'url' => ['/service/report/index'],
            ],
            [
                'label' => 'Услуги',
                'url' => ['/service/service/index'],
            ],
            [
                'label' => 'Тарифы',
                'url' => ['/service/price/index'],
            ],
            [
                'label' => 'Категории потребителей',
                'url' => ['/service/category/index'],
            ],
            [
                'label' => 'Комплексы',
                'url' => ['/service/complex/index'],
            ],
            [
                'label' => 'Затраты',
                'url' => ['/service/cost/index'],
            ],
        ],
        'options' => ['class' =>'nav-pills'],
    ]); ?>
</div>