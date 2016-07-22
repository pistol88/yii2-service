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
            <td><strong>Доля выручки</strong></td>
        </tr>
        <?php foreach($workers as $worker) { ?>
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
    </table>

</div>