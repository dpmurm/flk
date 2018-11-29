<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Статистика по протоколам ФЛК исходящей выгрузки</title>
</head>
<body>
<?php
$title = 'Статистика по протоколам ФЛК исходящей выгрузки';

$query = 'Select * from protokol_export
        order by Year, number desc
        ';
include_once("config.php");
?>
<div class="flk-export">
    <h1><?= $title; ?></h1>
    <p>На этой странице размещен список протоколов ФЛК исходящей выгрузки данных для организации процесса
        исправления ошибок путем внесения отметок об их исправлении.<br>
        Перейти к <a href="index.php">протоколам ФЛК</a> для исправления ошибок.
    </p>

    <table class="table table-hover">
        <thead class="thead-inverse">
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>№ протокола</th>
            <th>Дата формирования выгрузки</th>
            <th>Год</th>
            <th>Начало периода</th>
            <th>Конец периода</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result = mysqli_query($link, $query)) {
            $k = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $k = $k + 1;
                ?>
                <tr>
                    <td>
                        <a href="flk_protokol_stat_procent_export.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">% прохождения</a>
                    </td>
                    <td>
                        <a href="flk_protokol_stat_procent_ispravlen.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">ход исправления</a>
                    </td>
                    <td>
                        <a href="flk_protokol_stat_vid_oshibok.php?protokol_id=<?= $row['id'] ?>&number=<?= $row['number'] ?>&year=<?= $row['Year'] ?>&period_start=<?= $row['period_start'] ?>&period_stop=<?= $row['period_stop'] ?>">виды ошибок</a>
                    </td>
                    <td> <?= $row['number'] ?> </td>
                    <td> <?= $row['date'] ?></td>
                    <td> <?= $row['Year'] ?> </td>
                    <td> <?= $row['period_start'] ?> </td>
                    <td> <?= $row['period_stop'] ?> </td>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>