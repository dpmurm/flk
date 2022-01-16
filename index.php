<?
require_once("config.php");
require_once('matching.php');
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
require_once('matching.php');
require_once('config.php');
$title = 'Протоколы ФЛК исходящей выгрузки из ЕГРН';
$query = 'Select * from protokol_export
        order by date desc, number desc
        ';
?>
<div class="flk-export">
    <div class="text-center">
    <h1><?= $title; ?></h1>

    <p>На этой странице размещен список протоколов ФЛК исходящей выгрузки данных для организации процесса
        исправления ошибок путем внесения отметок об их исправлении.<br>
    <h4>
        <?php
        // показываем ссылку на загрузку протоколов, если наш ip в списке разрешенных
        if (in_array("$ipaddr", $arr_ip_allow)) {
            echo '<a href="upload_files.php">Загрузить файлы</a> /';
        }
        ?>
        <a href="index_fns.php">Протоколы ФЛК из ФНС</a> /
        <a href="flk_protokol_search.php">Поиск сведений о выгрузке объекта по кадастровому номеру</a> /
        <a href="https://fias.nalog.ru/Search"> Поиск адреса по ФИАС</a>
    </h4>
    </p>
    </div>
    <table class="table table-hover">
        <thead class="thead-inverse">
        <tr >
            <th class="text-center">№ протокола</th>
            <th>Дата создания</th>
            <th>Тип выгрузки</th>
            <th>Начало периода</th>
            <th>Конец периода</th>

            <th colspan="3" class="text-center">Статистика по протоколам форматно-логического контроля</th>
            <th>Экспорт</th>
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

                    <td class="text-center" >
                        <a href="flk_protokol_records.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&date=<?= $row['date'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>"><?= $row['number'] ?></a>
                    </td>

                    <td><?= $row['date'] ? DateTime::createFromFormat('Y-m-d', $row['date'])->format('d.m.Y') : $row['date']; ?></td>
                    <td><?= $name_type_unloading ?></td>
                    <td><?= $row['period_start'] ? DateTime::createFromFormat('Y-m-d', $row['period_start'])->format('d.m.Y') : $row['period_start']; ?></td>
                    <td><?= $row['period_stop'] ? DateTime::createFromFormat('Y-m-d', $row['period_stop'])->format('d.m.Y') : $row['period_stop']; ?></td>
                    <td>
                        <a href="flk_protokol_stat_procent_ispravlen.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&date=<?= $row['date'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">ход
                            исправления</a>
                    </td>
                    <td>
                        % прохождения <a
                                href="flk_protokol_stat_procent_export.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&date=<?= $row['date'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">по
                            документам</a> / <a
                                href="flk_protokol_stat_procent_export_on.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&date=<?= $row['date'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">по
                            объектам</a>
                    </td>
                    <td>
                        <a href="flk_protokol_stat_vid_oshibok.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&date=<?= $row['date'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">виды
                            ошибок</a>
                    </td>
                    <td>
                        <a href="flk_protokol_records_xls.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&date=<?= $row['date'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">Скачать
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