<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model pistol88\service\models\Category */

$this->title = 'Редактирование категории: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновление';
?>
<div class="category-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
