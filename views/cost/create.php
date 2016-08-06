<?php
use yii\helpers\Html;

$this->title = 'Добавление траты';
$this->params['breadcrumbs'][] = ['label' => 'Траты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
