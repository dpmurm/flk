<?php
//use yii\helpers\Url;
//use yii\helpers\Html;
//use frontend\models\Protokolexport;

/* @var $this yii\web\View */
$this->title = 'Статистика по протоколам ФЛК исходящей выгрузки в ФНС';
$this->params['breadcrumbs'][] = $this->title;


$sql = 'Select * from protokol_export
        order by Year, number desc
        ';
$model = Protokolexport::findbysql($sql)->all();

?>
<div class="flk-export">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>На этой странице размещен список протоколов ФЛК исходящей выгрузки данных в ФНС для организации процесса
        исправления ошибок путем внесения отметок об их исправлении.<br>
        Перейти к <a href="<?php echo Url::toRoute(['site/page', 'view' => 'flk_export']); ?>">протоколам ФЛК</a> для исправления ошибок.
    </p>
    <?php

    print '<table class="table table-hover">';
    echo '<thead class="thead-inverse"><tr>';
    //echo '<td>id</td>';
    echo '<th>' . ' ' . '</th>';
    echo '<th>' . ' ' . '</th>';
    echo '<th>' . ' ' . '</th>';
    echo '<th>' . '№ протокола' . '</th>';
    echo '<th>' . 'Дата формирования выгрузки' . '</th>';
    echo '<th>' . 'Год' . '</th>';
    echo '<th>' . 'Начало периода' . '</th>';
    echo '<th>' . 'Конец периода' . '</th>';

    //echo '<th>' . 'Дата обновления выгрузки' . '</th>';
   // echo '<th>' . ' ' . '</th>';
    echo '</tr></thead><tbody>';

    // while ($model=Protokolexport::findbysql($sql)->all()) {

    //$k=$k+1;
    // print '<td>'.($k).'</td>';
    foreach ($model as $item) {
        print '<tr>';
        //print '<td>'.($item->id).'</td>';
        echo '<td><a href="'.Url::base().'/scripts/flk_protokol_stat_procent_export.php?protokol_id='.$item->id.'&number='.$item->number.'&year='.$item->Year.'&period_start='.$item->period_start.'&period_stop='.$item->period_stop.'">
             % прохождения</a></td>';
        echo '<td><a href="'.Url::base().'/scripts/flk_protokol_stat_procent_ispravlen.php?protokol_id='.$item->id.'&number='.$item->number.'&year='.$item->Year.'&period_start='.$item->period_start.'&period_stop='.$item->period_stop.'">
             ход исправления</a></td>';
        echo '<td><a href="'.Url::base().'/scripts/flk_protokol_stat_vid_oshibok.php?protokol_id='.$item->id.'&number='.$item->number.'&year='.$item->Year.'&period_start='.$item->period_start.'&period_stop='.$item->period_stop.'">
             виды ошибок</a></td>';
        print '<td>' . ($item->number) . '</td>';
        print '<td>' . ($item->date) . '</td>';
        print '<td>' . ($item->Year) . '</td>';
        print '<td>' . ($item->period_start) . '</td>';
        print '<td>' . ($item->period_stop) . '</td>';

        //print '<td>' . ($item->date_update) . '</td>';
        //echo '<td><a href="'.Url::base().'/scripts/flk_protokol_records_xls.php?protokol_id='.$item->id.'&number='.$item->number.'&year='.$item->Year.'&period_start='.$item->period_start.'&period_stop='.$item->period_stop.'">
            // Скачать в EXCEL</a></td>';
        print '</tr>';

    }

    //}

    print '</tbody></table>';

    print '</table>';

    ?>


</div>
