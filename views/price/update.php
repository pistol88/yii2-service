<?php
use yii\helpers\Html;

$this->title = 'Обновление цены: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Цены', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновление';
?>
<div class="price-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
