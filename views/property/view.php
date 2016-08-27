<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => $module->propertyName, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр';
?>
<div class="client-view">
    <p><a href="<?=Url::toRoute(['update', 'id' => $model->id]);?>" class="btn btn-success">Редактировать</a></p>
    
    <?=DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'category.name',
            'client.name',
            'status',
            'comment:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]);?>

    <?php if($fieldPanel = \pistol88\field\widgets\Show::widget(['model' => $model])) { ?>
        <div class="block">
            <h2>Прочее</h2>
            <?=$fieldPanel;?>
        </div>
    <?php } ?>
</div>
