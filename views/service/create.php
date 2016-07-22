<?php
use yii\helpers\Html;

$this->title = 'Добавление услуги';
$this->params['breadcrumbs'][] = ['label' => 'Услуги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
