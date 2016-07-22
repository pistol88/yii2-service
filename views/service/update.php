<?php
use yii\helpers\Html;

$this->title = 'Редактирование услуги: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Услуги', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="service-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
