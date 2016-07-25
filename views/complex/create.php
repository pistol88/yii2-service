<?php
use yii\helpers\Html;

$this->title = 'Добавление комплекса';
$this->params['breadcrumbs'][] = ['label' => 'Комплексы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="complex-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
