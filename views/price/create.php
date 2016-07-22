<?php
use yii\helpers\Html;

$this->title = 'Добавление цены';
$this->params['breadcrumbs'][] = ['label' => 'Цены', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
