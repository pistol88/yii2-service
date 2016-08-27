<?php
use yii\helpers\Html;

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => $module->propertyName, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-create">

    <?= $this->render('_form', [
        'model' => $model,
        'module' => $module,
    ]) ?>

</div>
