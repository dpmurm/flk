<?php
require_once("config.php");
session_start();
require_once("functions/fn_misc.php");

date_default_timezone_set("Europe/Moscow");

if (isset($_GET['protokol_id'])) {
    $protokol_id = $_SESSION['fps']['protokol_id'] = $_GET['protokol_id'];
} elseif (isset($_SESSION['fps']['protokol_id'])) {
    $protokol_id = $_SESSION['fps']['protokol_id'];
} else {
    $protokol_id = 0;
}

// Получаем период выгрузки
$query_period = "SELECT  
			pe.period_start AS period_start, 
			pe.period_stop AS period_stop 
			FROM protokol_export pe 
			WHERE pe.id = $protokol_id";
$result_period = mysqli_query($link, $query_period);
$arr_period = mysqli_fetch_assoc($result_period);
$period_start = $arr_period['period_start'];
$period_stop = $arr_period['period_stop'];

// Преобразуем формат даты для удобного отображения
if ($formated_date = DateTime::createFromFormat('Y-m-d', $period_start)) {
    $period_start = DateTime::createFromFormat('Y-m-d', $period_start)->format('d.m.Y');
} else {
    $period_start = '';
}

if ($formated_date = DateTime::createFromFormat('Y-m-d', $period_stop)) {
    $period_stop = DateTime::createFromFormat('Y-m-d', $period_stop)->format('d.m.Y');
} else {
    $period_stop = '';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/table.css">
    <title>Виды ошибок протокола ФЛК ФНС</title>
</head>

<body>

<h3>Виды ошибок протокола ФЛК ФНС за период с <?= $period_start ?> по <?= $period_stop ?></h3>
<br> Вернуться к <a href="index_fns.php">протоколам ФЛК ФНС</a><br><br>

<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th>Описание ошибки ФЛК</th>
        <th>Всего кол-во записей</th>
        <th>Земля</th>
        <th>Здание/ Сооружение</th>
        <th>Помещение</th>
        <th>Не обработано</th>
        <th>В работе</th>
        <th>Исправлено</th>
        <th>Невозможно исправить</th>
        <th>Не обнаружено</th>
    </tr>
    <?php

    require_once("config.php");

    $query = "SELECT 
		rlf.error_text AS error_text, 
	COUNT(rlf.id) as vsego,
	SUM(IF ( rl.type_object = 'Земельный участок', 1, 0)) AS zu,
	SUM(IF ( rl.type_object = 'Здание/Сооружение', 1, 0)) AS zd_sooruz,
	SUM(IF ( rl.type_object = 'Помещение', 1, 0)) AS pom,
	SUM(IF (ifnull(rnf.decision_type,0) = 0, 1, 0)) AS ne_obrabot,
	SUM(IF (ifnull(rnf.decision_type,0) = 1, 1, 0)) AS v_rabote,
	SUM(IF (ifnull(rnf.decision_type,0) = 2, 1, 0)) AS ispravlena,
	SUM(IF (ifnull(rnf.decision_type,0) = 3, 1, 0)) AS nevozm_isprav,
	SUM(IF (ifnull(rnf.decision_type,0) = 4, 1, 0)) AS ne_obnaruz
	FROM protokol_file_fns pff
	LEFT JOIN record_list_fns rlf on pff.id=rlf.protokol_file_fns_id
	LEFT JOIN record_list rl ON rlf.error_id=rl.guid_doc   
	LEFT JOIN record_notes_fns rnf ON rlf.id=rnf.record_list_id   
	WHERE	
		pff.protokol_id =$protokol_id
		and rlf.error_id is not null
        and rlf.date_correct_add is null -- Если от фнс присутствует дата исправления, то ошибка исправлена и не идет в работу
	GROUP BY error_text 
	ORDER BY 1,3 desc";
    //echo $query;

    if ($result = mysqli_query($link, $query)) {

        while ($row = mysqli_fetch_assoc($result)) {

            ?>
            <tr class="hover">
                <td><?php echo $row['error_text']; ?></td>
                <td><?php echo $row['vsego']; ?></td>
                <td><?php echo $row['zu']; ?></td>
                <td><?php echo $row['zd_sooruz']; ?></td>
                <td><?php echo $row['pom']; ?></td>
                <td><?php echo $row['ne_obrabot']; ?></td>
                <td><?php echo $row['v_rabote']; ?></td>
                <td><?php echo $row['ispravlena']; ?></td>
                <td><?php echo $row['nevozm_isprav']; ?></td>
                <td><?php echo $row['ne_obnaruz']; ?></td>
            </tr>
            <?php
        }
    }

    mysqli_free_result($result);
    mysqli_close($link);
    clear_url();
    ?>
</table>
</body>
</html>
