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
if (isset($_GET['year'])) {
    $year = $_GET['year']; // Если нет номера отдела берется 0
} else {
    $year = 0;
}
if (isset($_GET['period_start'])) {
    $period_start =$_GET['period_start']?DateTime::createFromFormat('Y-m-d', $_GET['period_start']) -> format('d.m.Y'):$_GET['period_start'] ; // Если нет номера отдела берется 0
} else {
    $period_start = 0;
}
if (isset($_GET['period_stop'])) {
    $period_stop = $_GET['period_stop']?DateTime::createFromFormat('Y-m-d', $_GET['period_stop']) -> format('d.m.Y'):$_GET['period_stop']; // Если нет номера отдела берется 0
} else {
    $period_stop = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Виды ошибок протокола ФЛК</title>
</head>

<body>

<h2><font color=red>Виды ошибок протокола ФЛК от <?= $year ?> года номер <?= $number ?> за период с <?= $period_start ?> по <?= $period_stop ?> </font></h2>


<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>

        <th>Вид сведений</th>
        <th>Описание ошибки ФЛК</th>
        <th>Всего кол-во записей</th>
        <th>Земля</th>
        <th>Здание/Сооружение</th>
        <th>Помещение</th>
        <th>Не обработано</th>
        <th>В работе</th>
        <th>Исправлено</th>
        <th>Невозможно исправить</th>
        <th>Не обнаружено</th>

    </tr>
    <?php

    include_once("config.php");

    $query = "select  
(case 
when rl.error_type='Право' then 'Право/Обременение'
when rl.error_type='Обременение' then 'Право/Обременение'
else rl.error_type
end ) as type
, rl.error_text, 
count(rl.id) as vsego,
sum(IF ( rl.type_object = 'Земельный участок', 1, 0)) as zu,
sum(IF ( rl.type_object = 'Здание/Сооружение', 1, 0)) as zd_sooruz,
sum(IF ( rl.type_object = 'Помещение', 1, 0)) as pom,
sum(IF (ifnull(rn.decision_type,0) = 0, 1, 0)) as ne_obrabot,
sum(IF (ifnull(rn.decision_type,0) = 1, 1, 0)) as v_rabote,
sum(IF (ifnull(rn.decision_type,0) = 2, 1, 0)) as ispravlena,
sum(IF (ifnull(rn.decision_type,0) = 3, 1, 0)) as nevozm_isprav,
sum(IF (ifnull(rn.decision_type,0) = 4, 1, 0)) as ne_obnaruz

-- , count(rl.id) as vsego_rec
from record_list rl
left join record_notes rn on rl.id=rn.record_list_id   
left join protokol_file pf on rl.file_name_id=pf.id 
left join protokol_export pe  on pf.protokol_id=pe.id
where    
pe.id=$protokol_id
and rl.status!='Прошел ФЛК'
group by   (case 
when rl.error_type='Право' then 'Право/Обременение'
when rl.error_type='Обременение' then 'Право/Обременение'
else rl.error_type
end ) 
, rl.error_text 
order by 1,3 desc";
    //echo $query;

    if ($result = mysqli_query($link, $query)) {

        while ($row = mysqli_fetch_assoc($result)) {

            ?>
            <tr>


                <td><?php echo $row['type']; ?></td>
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

    ?>
</table>
</body>
</html>