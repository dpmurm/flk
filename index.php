<?
require_once("config.php");
require_once ('matching.php');
session_start();
?>
<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Протоколы ФЛК исходящей выгрузки</title>
</head>
<body>

<?php
$ipaddr = $_SERVER['REMOTE_ADDR'];
require_once("matching.php");
$title = 'Протоколы ФЛК исходящей выгрузки';

$query = 'Select * from protokol_export
        order by Year desc, number desc
        ';
include_once("config.php");
?>
<div class="flk-export">
    <h1><?= $title; ?></h1>

    <p>На этой странице размещен список протоколов ФЛК исходящей выгрузки данных для организации процесса
        исправления ошибок путем внесения отметок об их исправлении.<br>
        Также доступна <a href="index_stat.php">статистика по протоколам ФЛК</a><br>
        Перейти к <a href="index_fns.php">протоколам ФЛК 2 уровня</a><br>
        Перейти <a href="flk_protokol_search.php">к поиску сведений о выгрузке ОН</a> по кадастровому номеру
    </p>

    <table class="table table-hover">
        <thead class="thead-inverse">
        <tr>
            <th class="main min_v1 max_v1">
                <?php
                // показываем ссылку на загрузку протоколов, если наш ip в списке разрешенных
                if (in_array("$ipaddr", $arr_ip_allow)){
                    echo '<a href="flk_protokol_upload.php">Загрузить</a>';
                }
                ?>
            </th>
            <th>№ протокола</th>
            <th>Дата создания</th>
            <th>Тип выгрузки</th>
            <th>Начало периода</th>
            <th>Конец периода</th>
            <th>Год</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <?php
        if ($result = mysqli_query($link, $query)) {
            $k = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $k = $k + 1;
                foreach ($arr_type_unloading as $id_type_unloading => $name_type_unloading) {
                    if ($row['type'] == $id_type_unloading) {
                        break;
                    } else $name_type_unloading = "unknown";
                }
                ?>
                <tr>

                    <td>
                        <a href="flk_protokol_records.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">Открыть</a>
                    </td>
                    <td><?= $row['number'] ?></td>
                    <td><?= $row['date']?DateTime::createFromFormat('Y-m-d', $row['date']) -> format('d.m.Y'):$row['date']; ?></td>
                    <td><?= $name_type_unloading ?></td>
                    <td><?= $row['period_start']?DateTime::createFromFormat('Y-m-d', $row['period_start']) -> format('d.m.Y'):$row['period_start']; ?></td>
                    <td><?= $row['period_stop']?DateTime::createFromFormat('Y-m-d', $row['period_stop']) -> format('d.m.Y'):$row['period_stop']; ?></td>
                    <td><?= $row['Year']; ?></td>
                    <td>
                        <a href="flk_protokol_records_xls.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">Скачать
                            в EXCEL</a>
                    </td>
                </tr>
                <?php
            }
        }
        mysqli_free_result($result);
        mysqli_close($link);
        ?>
        </tbody>
    </table>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>