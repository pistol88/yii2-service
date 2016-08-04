<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use pistol88\worksess\widgets\SessionGraph;

$this->title = 'Отчеты по услугам';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="report-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <br style="clear: both;" />
    
    <div class="row session-finder">
        <div class="col-md-6">
            <form action="" method="get">
                <p>Выберите сессию:</p>
                <input href="<?=Url::toRoute(['/service/report/get-sessions']);?>" class="get-sessions-by-date form-control" type="date" name="date" value="<?=$date;?>" />
                <ul>
                    <?php foreach($sessions as $sessionList) { ?>
                    <li><a <?php if($session && $sessionList->id == $session->id) echo 'style="font-weight: bold;"'; ?> href="<?=Url::toRoute(['/service/report/index', 'sessionId' => $sessionList->id]);?>"><?=$sessionList->start;?> <?php if(isset($sessionList->user)) { ?> (<?=$sessionList->user->name;?>)<?php } ?></a></li>
                    <?php } ?>
                </ul>
            </form>

        </div>
        <div class="col-md-6">

        </div>
    </div>
    
    <?php if(!$session) { ?>
        <p>Выберите сессию.</p>
    <?php } else { ?>

        <h1><?=$session->start;?> <?php if(isset($session->user)) { ?>(<?=$session->user->name;?>)<?php } ?> </h1>

        <hr style="clear: both;" />

        <h2>Выплаты</h2>
        <table class="table table-hover table-responsive">
            <tr>
                <td><strong>Сотрудник</strong></td>
                <td><strong>Заказов/Услуг</strong></td>
                <td><strong>Выручка</strong></td>
                <td><strong>Время работы</strong></td>
                <td><strong>Зарплата</strong></td>
            </tr>
            <?php
            $sum = ['orders' => 0, 'services' => 0, 'total' => 0, 'earnings' => '0'];
            foreach($workers as $worker) {
                $sum['orders'] += $workerStat[$worker->id]['order_count'];
                $sum['services'] += $workerStat[$worker->id]['service_count'];
                $sum['total'] += $workerStat[$worker->id]['service_total'];
                $sum['earnings'] += $workerStat[$worker->id]['earnings'];
                ?>
                <tr>
                    <td class="worker-name">
                        <p class="staffername">
                            <strong><?=$worker->name;?></strong>
                        </p>
                        <?php if($cat = $worker->category) { ?>
                            <p>
                                <small>(<?=$cat->name;?>)</small>
                            </p>
                        <?php } ?>
                        <?php
                        if($workerSessions = $workerStat[$worker->id]['sessions']) {
                            echo '<ul>';
                            foreach($workerSessions as $workSession) {
                                if($workSession->stop_timestamp) {
                                    $dateStop = date('H:i', $workSession->stop_timestamp);
                                } else {
                                    $dateStop = '...';
                                }
                                echo Html::tag(
                                    'li',
                                    Html::a(
                                        date('H:i', $workSession->start_timestamp).' - '.$dateStop,
                                        ['/order/order/index', 'date_start' => $workSession->start, 'date_stop' => $workSession->stop]
                                    )
                                );
                            }
                            echo '</ul>';
                        }
                        ?>
                    </td>
                    <td>
                        <?=$workerStat[$worker->id]['order_count'];?>/<?=$workerStat[$worker->id]['service_count'];?>
                    </td>
                    <td>
                        <?=$workerStat[$worker->id]['service_total'];?>
                        <?=$module->currency;?>
                    </td>
                    <td class="worker-session-time">
                        <?=$worker->getSessionTime();?>
                    </td>
                    <td>
                        <?=$workerStat[$worker->id]['earnings'];?>
                        <?=$module->currency;?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td align="right">Итого:</td>
                <td><strong><?=$sum['orders'];?>/<?=$sum['services'];?></strong></td>
                <td><strong><?=$sum['total'];?> <?=$module->currency;?></strong></td>
                <td><strong>-</strong></td>
                <td><strong><?=$sum['earnings'];?> <?=$module->currency;?></strong></td>
            </tr>
        </table>

        <h2>Рабочий день</h2>
        
        <?=SessionGraph::widget(['workers' => $workers, 'control' => false, 'session' => $session, 'hoursCount' => $module->shiftDuration]);?>
    <?php } ?>
</div>