<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use pistol88\worksess\widgets\SessionGraph;
use nex\datepicker\DatePicker;

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
                <?= DatePicker::widget([
                    'name' => 'date',
                    'addon' => false,
                    'value' => $date,
                    'size' => 'sm',
                    'language' => 'ru',
                    'options' => [
                        'class' => 'get-sessions-by-date',
                        'href' => Url::toRoute(['/service/report/get-sessions']),
                    ],
                    'placeholder' => yii::t('order', 'Date from'),
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

                <ul>
                    <?php foreach($sessions as $sessionList) { ?>
                    <li><a <?php if($session && $sessionList->id == $sessionId) echo 'style="font-weight: bold;"'; ?> href="<?=Url::toRoute(['/service/report/index', 'sessionId' => $sessionList->id]);?>"><?=date('d.m.Y H:i:s', $sessionList->start_timestamp);?> <?=$sessionList->shiftName;?> <?php if(isset($sessionList->user)) { ?> (<?=$sessionList->user->name;?>)<?php } ?></a></li>
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
    <div id="report-to-print">
        <h1> <?php if(isset($session->user)) { ?>Администратор <?=$session->user->name;?><?php } ?> </h1>
        <a href="#" class="btn btn-submit" onclick="pistol88.service.callPrint('report-to-print'); return false;" style="float: right;"><i class="glyphicon glyphicon-print"></i></a>
        <p><strong>Смена</strong>: <?=$session->shiftName;?></p>
        <p><strong>Старт</strong>: <?=date('d.m.Y H:i:s', $session->start_timestamp);?></p>
        <p><strong>Стоп</strong>: <?php if($session->stop_timestamp) echo date('d.m.Y H:i:s', $session->stop_timestamp); else echo '-';?></p>
        <p><strong>Продолжительность</strong>: <?=$session->getDuration();?></p>
        <hr style="clear: both;" />

        <h2>Услуги</h2>
        <table class="table table-hover table-responsive">
            <tr>
                <td><strong>Сотрудник</strong></td>
                <td><strong>Время работы</strong></td>
                <td><strong>Заказов/Услуг</strong></td>
                <td><strong>Выручка</strong></td>
                <td><strong>Фикс</strong></td>
                <td><strong>Процент</strong></td>
                <td><strong>Штрафы</strong></td>
                <td><strong>Зарплата</strong></td>
                <td><strong>К выплате</strong></td>
            </tr>
            <?php
            $sum = ['earnings' => '0'];
            foreach($workers as $worker) {
                $sum['earnings'] += $workerStat[$worker->id]['earnings'];
                ?>
                <tr>
                    <td class="worker-name">
                        <p class="staffername">
                            <strong><a href="<?=Url::toRoute([$module->stafferProfileUrl, 'id' => $worker->id]);?>"><?=$worker->name;?></a></strong>
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
                                        ['/order/order/index', 'element_types' => ['pistol88\service\models\Price', 'pistol88\service\models\CustomService'], 'time_start' => $workSession->start, 'time_stop' => $workSession->stop]
                                    )
                                );
                            }
                            echo '</ul>';
                        }
                        ?>
                    </td>
                    <td class="work-time">
                        <?=$workerStat[$worker->id]['time'];?>
                    </td>
                    <td>
                        <?=$workerStat[$worker->id]['order_count'];?>/<?=$workerStat[$worker->id]['service_count'];?>
                    </td>
                    <td>
                        <?php if($workerStat[$worker->id]['service_base_total'] != $workerStat[$worker->id]['service_total']) { ?> <s title="Базовая стоимость услуг"><small><?=$workerStat[$worker->id]['service_base_total'];?></small></s><?php } ?>
                        <strong title="Фактически полученная выручка"><?=$workerStat[$worker->id]['service_total'];?></strong>
                        <?=$module->currency;?>
                    </td>
                    <td class="fix">
                        <?=$workerStat[$worker->id]['fix'];?>
                    </td>
                    <td class="persent">
                        <?=$workerStat[$worker->id]['persent'];?>%
                    </td>
                    <td class="fines">
                        <?=$workerStat[$worker->id]['fines'];?>
                    </td>
                    <td class="earnings">
                        <?=round($workerStat[$worker->id]['earnings']);?>
                        <?=$module->currency;?>
                        <?php if($bonus = $workerStat[$worker->id]['bonus']) { ?>
                            <span class="bonus" title="Бонус">+<?=$bonus;?> <?=$module->currency;?></span>
                        <?php } ?>
                        <?php if($fine = $workerStat[$worker->id]['fines']) { ?>
                            <span class="fine" title="Штраф">-<?=$fine;?> <?=$module->currency;?></span>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="<?php if($payment = $workerStat[$worker->id]['payment']) echo 'payment_yes'; else echo 'payment_no'; ?>">
                            <input
                                <?php if($payment) { ?>checked="checked"<?php } ?>
                                data-set-href="<?=Url::toRoute(['/service/payment/set']);?>"
                                data-unset-href="<?=Url::toRoute(['/service/payment/unset']);?>"
                                data-session-id="<?=$session->id;?>"
                                data-worker-id="<?=$worker->id;?>"
                                data-sum="<?=$workerStat[$worker->id]['earnings'];?>"
                                class="service-worker-payment"
                                type="checkbox"
                                id="earnings-done-<?=$worker->id;?>"
                                name="done"
                                value="1" />
                            <label for="earnings-done-<?=$worker->id;?>">Выплачено</label>
                            <?php if($payment) { ?>
                                <p><small><?=date('d.m.Y H:i:s', $payment->date_timestamp);?></small></p>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td align="right">Итого:</td>
                <td><?=$session->getDuration();?></td>
                <td><strong><?=$stat['count_order'];?>/<?=$stat['count_elements'];?></strong></td>
                <td><strong><?=$stat['total'];?> <?=$module->currency;?></strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><strong><?=$sum['earnings'];?> <?=$module->currency;?></strong></td>
                <td>
                    -
                </td>
            </tr>
        </table>

        <?php if($shopStat['total']) { ?>
            <h2>Витрина</h2>
            <ul>
                <li>Заказов: <?=$shopStat['count_order'];?></li>
                <li>Товаров: <?=$shopStat['count_elements'];?></li>
                <li>Сумма: <?=$shopStat['total'];?> <?=$module->currency;?></li>
            </ul>
        <?php } ?>

        <?php if($costs) { ?>
            <h2>Расходы</h2>
            <ul>
                <?php foreach($costs as $cost) { ?>
                    <li><?=$cost->sum;?> <?=$module->currency;?>. <?=date('d.m.Y H:i', strtotime($cost->date));?></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>


        <h2>Отчёт по кассам</h2>

        <?= \halumein\cashbox\widgets\ReportBalanceByPeriod::widget([
                'dateStart' => date('Y-m-d H:i:s', $session->start_timestamp),
                'dateStop' => $session->stop_timestamp ? date('Y-m-d H:i:s', $session->stop_timestamp) : null
                 ])
        ?>

        <h2>Рабочий день</h2>

        <?=SessionGraph::widget(['workers' => $workers, 'control' => false, 'session' => $session, 'hoursCount' => $module->shiftDuration]);?>
    <?php } ?>
</div>
