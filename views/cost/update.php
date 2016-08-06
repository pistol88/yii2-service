<?php
use yii\helpers\Html;

$this->title = 'Редактирование  траты: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Траты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновление';
?>
<div class="category-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
