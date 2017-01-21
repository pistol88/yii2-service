<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use pistol88\worksess\widgets\SessionGraph;
use nex\datepicker\DatePicker;

$this->title = 'Отчеты по услугам';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);

$totalServices = 0;
?>
<div class="report-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <br style="clear: both;" />
    
    <div class="row session-finder">
        <div class="col-md-6">
            <form action="" method="get">
                <p>Выберите период:</p>
                <div class="row">
                    <div class="col-md-4">
                        <?= DatePicker::widget([
                            'name' => 'dateStart',
                            'addon' => false,
                            'value' => date('d.m.Y', strtotime($dateStart)),
                            'size' => 'sm',
                            'language' => 'ru',
                            'options' => [
                                'onchange' => '',
                            ],
                            'placeholder' => 'На дату...',
                            'clientOptions' => [
                                'format' => 'L',
                                'minDate' => '2015-01-01',
                                'maxDate' => date('Y-m-d'),
                            ],
                            'dropdownItems' => [
                                ['label' => 'Yesterday', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('-1 day')],
                                ['label' => 'Tomorrow', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('+1 day')],
                                ['label' => 'Some value', 'url' => '#', 'value' => 'Special value'],
                            ],
                        ]);?>
                    </div>
                    <div class="col-md-4">
                        <?= DatePicker::widget([
                            'name' => 'dateStop',
                            'addon' => false,
                            'value' => date('d.m.Y', strtotime($dateStop)),
                            'size' => 'sm',
                            'language' => 'ru',
                            'options' => [
                                'onchange' => '',
                            ],
                            'placeholder' => 'На дату...',
                            'clientOptions' => [
                                'format' => 'L',
                                'minDate' => '2015-01-01',
                                'maxDate' => date('Y-m-d'),
                            ],
                            'dropdownItems' => [
                                ['label' => 'Yesterday', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('-1 day')],
                                ['label' => 'Tomorrow', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('+1 day')],
                                ['label' => 'Some value', 'url' => '#', 'value' => 'Special value'],
                            ],
                        ]);?>
                    </div>
                    <div class="col-md-4">
                        <input type="submit" value="Применить" class="btn btn=submit" />
                    </div>
                </div>
            </form>

        </div>
        <div class="col-md-6">

        </div>
    </div>
    
    <div class="report" id="report-to-print">
        <h1><?=date('d.m.Y', strtotime($dateStart));?> - <?=date('d.m.Y', strtotime($dateStop));?></h1>
        <a href="#" class="btn btn-submit" onclick="pistol88.service.callPrint('report-to-print'); return false;" style="float: right;"><i class="glyphicon glyphicon-print"></i></a>
        
        <p>Основные услуги: <?=$serviceStat['total'];?> <?=$module->currency;?> *</p>
        <p>Прочие услуги: <?=$customStat['total'];?> <?=$module->currency;?></p>
        <p>Витрина: <?=$shopStat['total'];?> <?=$module->currency;?></p>
        <p>* <small>Сумма оказанных услуг может не совпадать с денежными поступлениями. Сюда входит заказы, выполненные в долг и бесплатно.</small></p>
        
        <h2>Движения денежных средств</h2>
        
        <?= \halumein\cashbox\widgets\ReportBalanceByPeriod::widget([
                'dateStart' => $dateStart,
                'dateStop' => $dateStop
                 ])
        ?>
        
        <h2>Расход</h2>
        <table class="table">
            <tr>
                <th>Наименование</th>
                <th>Расход</th>
                <th>Примечание</th>
            </tr>
            <?php foreach(yii::$app->spending->getCategories() as $category) { ?>
                <tr>
                    <td><?=$category->name;?></td>
                    <td><?=yii::$app->spending->getSumByCategory($category, $dateStart, $dateStop);?></td>
                    <td>&nbsp;</td>
                </tr>
            <?php } ?>
        </table>
        
    </div>
</div>
