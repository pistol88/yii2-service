<?php
use yii\helpers\Html;

$this->title = 'Редактирование комплекса: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Комплексы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="complex-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
