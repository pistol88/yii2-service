<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use pistol88\worksess\widgets\SessionGraph;
use nex\datepicker\DatePicker;

$this->title = 'Отчеты по услугам';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);

$incomeSum = yii::$app->cashbox->getIncomeSumByPeriod(date('Y-m-d H:i:s', $session->start_timestamp), $session->stop_timestamp ? date('Y-m-d H:i:s', $session->stop_timestamp) : date('Y-m-d H:i:s'));

?>
<div class="report-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <br style="clear: both;" />

<?php if($session) { ?>
    <div class="tabs row">
        <div class="col-md-6">
            <ul class="nav nav-tabs" role="tablist">
                <li><a href="<?=Url::toRoute(['/service/report/index', 'sessionId' => $session->id]);?>">Расширенный отчет</a></li>
                <li class="active"><a href="<?=Url::toRoute(['/service/report/mini', 'sessionId' => $session->id]);?>">Короткий отчет</a></li>
            </ul>
        </div>
    </div>

    <div id="report-to-print">
        <h3>
            <?php if(isset($session->user)) { ?>Администратор <?=$session->user->name;?><?php } ?>
            
            <?php if(yii::$app->has('organization') && $organization = yii::$app->organization->get()) { ?>
                (<?=$organization->name;?>)
            <?php } ?>
        </h3>
        <a href="#" class="btn btn-submit" onclick="pistol88.service.callPrint('report-to-print'); return false;" style="float: right;"><i class="glyphicon glyphicon-print"></i></a>
        
        <p><strong>Смена</strong>: <?=$session->shiftName;?> (<?=date('d.m.Y H:i:s', $session->start_timestamp);?> - <?php if($session->stop_timestamp) echo date('d.m.Y H:i:s', $session->stop_timestamp); else echo '...';?>), <?=$session->getDuration();?></p>
        <p><strong>Количество заказов по услугам</strong>: <?=$data['summary']['ordersCount'];?></p>

        <?= \halumein\cashbox\widgets\ReportBalanceByPeriod::widget([
                'dateStart' => date('Y-m-d H:i:s', $session->start_timestamp),
                'dateStop' => $session->stop_timestamp ? date('Y-m-d H:i:s', $session->stop_timestamp) : null
                 ])
        ?>
        
        <h3>Зарплата</h3>
        <table class="table" width="100%">
            <tr>
                <th>Сотрудник</th>
                <th width="30">ЗП</th>
                <th width="30">Выдано</th>
            </tr>
            <?php $sumSalary = 0; $sumBalance = 0;?>
            <?php foreach($data['salary'] as $workerId => $workerData) { ?>
                <?php
                $sumSalary += $workerData['salary'];
                $sumBalance += ($workerData['salary']-$workerData['balance']);
                ?>
                <tr>
                    <td>
                        <?=$workerData['staffer']->name;?> -
                        <?php if($cat = $workerData['staffer']->category) { ?><small><?=$cat->name;?></small><?php } ?>
                    </td>
                    <td><?=$workerData['salary'];?></td>
                    <td><?=($workerData['salary']-$workerData['balance']);?></td>
                </tr>
            <?php } ?>
            <tr>
                <td align="right">Итого:</td>
                <th><?=$sumSalary;?></th>
                <td><?=$sumBalance;?></td>
            </tr>
        </table>
        
        <h3><?=$module->profitFieldName;?>: <?=$incomeSum-$sumSalary;?></h3>
        
        <?php if($paymentTypeReport = \pistol88\order\widgets\ReportPaymentTypes::widget([
                'types' => $module->paymentTypeIdsReport,
                'dateStart' => date('Y-m-d H:i:s', $session->start_timestamp),
                'dateStop' => $session->stop_timestamp ? date('Y-m-d H:i:s', $session->stop_timestamp) : null
             ])) { ?>
            <h3>Отчет по способам оплаты</h3>
            <?= $paymentTypeReport?> 
        <?php } ?>
        
        <h3>Отчёт по расходам</h3>

        <?= \halumein\spending\widgets\ReportSpendingsByPeriod::widget([
                'dateStart' => date('Y-m-d H:i:s', $session->start_timestamp),
                'dateStop' => $session->stop_timestamp ? date('Y-m-d H:i:s', $session->stop_timestamp) : null,
                'simple' => true,
                 ])
        ?>
        
        <h3>Витрина</h3>
        <table class="table">
            <tr>
                <th>Сумма</th>
                <th>Заказов</th>
                <th>Элементов</th>
            </tr>
            <tr>
                <td><?=$shopStat['total'];?> <?=$module->currency;?></td>
                <td><?=$shopStat['count_orders'];?></td>
                <td><?=$shopStat['count_elements'];?></td>
            </tr>
        </table>
    </div>
<?php } else { ?>
    <p>Сессия не была начата.</p>
<?php } ?>
</div>
