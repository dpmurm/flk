<?php
date_default_timezone_set("Europe/Moscow");
if (isset($_GET['protokol_id'])) {
    $protokol_id = $_GET['protokol_id']; // Если нет номера отдела берется 0
} else {
    $protokol_id = 0;
}
if (isset($_GET['number'])) {
    $number = $_GET['number']; // Если нет номера отдела берется 0
} else {
    $number = 0;
}
if (isset($_GET['date'])) {
    $date = $_GET['date'] ? DateTime::createFromFormat('Y-m-d', $_GET['date'])->format('d.m.Y') : $_GET['date']; // Если нет номера отдела берется 0
} else {
    $date = 0;
}
if (isset($_GET['period_start'])) {
    $period_start = $_GET['period_start'] ? DateTime::createFromFormat('Y-m-d', $_GET['period_start'])->format('d.m.Y') : $_GET['period_start']; // Если нет номера отдела берется 0
} else {
    $period_start = 0;
}
if (isset($_GET['period_stop'])) {
    $period_stop = $_GET['period_stop'] ? DateTime::createFromFormat('Y-m-d', $_GET['period_stop'])->format('d.m.Y') : $_GET['period_stop']; // Если нет номера отдела берется 0
} else {
    $period_stop = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ход исправления протокола ФЛК</title>
</head>

<body>

<h2><font color=red>Ход исправления протокола ФЛК от <?= $date ?> номер <?= $number ?> за период с <?= $period_start ?>
        по <?= $period_stop ?> </font></h2>


<table width="60%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th>Наименование отдела</th>
        <th>Кад. район</th>
        <th>Не обработано</th>
        <th>В работе</th>
        <th>Исправлено</th>
        <th>Невозм. исправить</th>
        <th>Не обнаружено</th>
        <th>Всего</th>

    </tr>
    <?php

    include_once("config.php");

    $query = "select  
kr.otdel_name, CONCAT(kr.name, ' (', kr.region, ':',substr(rl.cad_obj_num,4,2),')') as rayon, 
sum(IF (ifnull(rn.decision_type,0) = 0, 1, 0)) as ne_obrabot,
sum(IF (ifnull(rn.decision_type,0) = 1, 1, 0)) as v_rabote,
sum(IF (ifnull(rn.decision_type,0) = 2, 1, 0)) as ispravlena,
sum(IF (ifnull(rn.decision_type,0) = 3, 1, 0)) as nevozm_isprav,
sum(IF (ifnull(rn.decision_type,0) = 4, 1, 0)) as ne_obnaruz,
count(rl.id) as vsego
-- , count(rl.id) as vsego_rec
from record_list rl
left join record_notes rn on rl.id=rn.record_list_id   
left join protokol_file pf on rl.file_name_id=pf.id 
left join protokol_export pe  on pf.protokol_id=pe.id
left join kad_rayon kr on concat(kr.region, ':',kr.number)=SUBSTR(cad_obj_num,1,5)
where    
pe.id=$protokol_id
and rl.status!='Прошел ФЛК'
group by   kr.otdel_name, CONCAT(kr.name, ' (', kr.region, ':', substr(rl.cad_obj_num,4,2),')')
order by 1";
    //echo $query;

    if ($result = mysqli_query($link, $query)) {

        while ($row = mysqli_fetch_assoc($result)) {

            ?>
            <tr>

                <td><?php echo $row['otdel_name']; ?></td>
                <td><?php echo $row['rayon']; ?></td>
                <td><?php echo $row['ne_obrabot']; ?></td>
                <td><?php echo $row['v_rabote']; ?></td>
                <td><?php echo $row['ispravlena']; ?></td>
                <td><?php echo $row['nevozm_isprav']; ?></td>
                <td><?php echo $row['ne_obnaruz']; ?></td>
                <td><?php echo $row['vsego']; ?></td>

            </tr>
            <?php
        }
    }

    mysqli_free_result($result);
    mysqli_close($link);

    ?>
</table>
</body>
</html>