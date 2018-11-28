<?php

//use yii\helpers\Url;
//use yii\helpers\Html;
//use frontend\models\Protokolexport;

/* @var $this yii\web\View */
$title = 'Протоколы ФЛК исходящей выгрузки в ФНС';
//$params['breadcrumbs'][] = $title;


$query = 'Select * from protokol_export
        order by Year, number desc
        ';
//$model = Protokolexport::findbysql($sql)->all();
include_once("config.php");

?>
<div class="flk-export">
    <h1><?= $title; ?></h1>

    <p>На этой странице размещен список протоколов ФЛК исходящей выгрузки данных в ФНС для организации процесса
        исправления ошибок путем внесения отметок об их исправлении.<br>
        Также доступна <a href="index_stat.php">статистика по протоколам ФЛК</a>
    </p>
    <?php

    print '<table class="table table-hover">';
    echo '<thead class="thead-inverse"><tr>';
    //echo '<td>id</td>';
    echo '<th>' . ' ' . '</th>';
    echo '<th>' . '№ протокола' . '</th>';
    echo '<th>' . 'Дата формирования выгрузки' . '</th>';
    echo '<th>' . 'Год' . '</th>';
    echo '<th>' . 'Начало периода' . '</th>';
    echo '<th>' . 'Конец периода' . '</th>';

    //echo '<th>' . 'Дата обновления выгрузки' . '</th>';
    echo '<th>' . ' ' . '</th>';
    echo '</tr></thead><tbody>';


    if ($result = mysqli_query($link, $query)) {
                                                $k = 0;
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                $k = $k + 1;
                                                ?>
        <tr>

       <td><a href="flk_protokol_records.php?protokol_id=<?= $row['id']?>&number=<?= $row['number']?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">Открыть</a></td>
        <td> <?= $row['number']?></td>
        <td><?= $row['date'] ?></td>
        <td> <?= $row['Year'] ?></td>
        <td><?= $row['period_start'] ?></td>
        <td><?= $row['period_stop'] ?></td>
       <td><a href="flk_protokol_records_xls.php?protokol_id=<?= $row['id']?>&number=<?= $row['number']?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">Скачать в EXCEL</a></td>
        </tr>
         <?php
                                            }
    }
    mysqli_free_result($result);
    mysqli_close($link);

    print '</tbody></table>';

    print '</table>';

    ?>


</div>
