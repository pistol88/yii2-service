<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Отчеты по услугам';
$this->params['breadcrumbs'][] = $this->title;

\pistol88\service\assets\BackendAsset::register($this);
?>
<div class="report-index">

    <div class="service-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    
    <br style="clear: both;" />
    
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
                    <p>
                        <strong><?=$worker->username;?></strong>
                        <?php if($name = $worker->name) { ?>(<?=$name;?>)<?php } ?>
                    </p>
                    <?php
                    if($worker->getSessions()) {
                        echo '<ul>';
                        foreach($worker->getSessions() as $session) {
                            if($session->stop_timestamp) {
                                $dateStop = date('H:i', $session->stop_timestamp);
                            } else {
                                $dateStop = '...';
                            }
                            echo Html::tag(
                                'li',
                                Html::a(
                                    date('H:i', $session->start_timestamp).' - '.$dateStop,
                                    ['/order/order/index', 'date_start' => $session->start, 'date_stop' => $session->stop]
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
            <td><strong><?=$sum['total'];?></strong></td>
            <td><strong>-</strong></td>
            <td><strong><?=$sum['earnings'];?></strong></td>
        </tr>
    </table>

</div>