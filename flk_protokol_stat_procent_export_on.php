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
    <title>Процент прохождения сведений в ФНС по объектам</title>
</head>

<body>

<h2><font color=red>Процент прохождения сведений в ФНС по объектам по протоколу от <?= $date ?> года
        номер <?= $number ?> за период с <?= $period_start ?> по <?= $period_stop ?> </font></h2>


<table width="60%" border="1" cellspacing="0" cellpadding="0">
    <tr>

        <th>Вид объекта</th>
        <th>Объектов для выгрузки</th>
        <th>Выгружено</th>
        <th>Не прошли ФЛК</th>
        <th>Процент прохождения</th>

    </tr>
    <?php

    include_once("config.php");

    $query = "
    select sp.type_object, (sp.pass+sp.nopass) as forpass, sp.pass, sp.nopass, (sp.pass/(sp.pass+sp.nopass))*100 as protsent
from
 (
select sp1.type_object , sum( IF (sp1.status = 'Прошел флк', 1, 0) ) AS pass, sum(IF (sp1.status = 'Не прошел флк', 1, 0)) AS nopass 
from
(
select distinct  rl.type_object ,rl.cad_obj_num,  rl.status
from record_list rl,
     protokol_file pf,
     protokol_export pe
where rl.file_name_id=pf.id    
and pf.protokol_id=pe.id
and pe.id=$protokol_id
) as sp1
group by  sp1.type_object
union
select \"Всего\" as name , sum( IF (sp2.status = 'Прошел флк', 1, 0) ) AS pass, sum(IF (sp2.status = 'Не прошел флк', 1, 0)) AS nopass
from
(
select distinct  rl.type_object ,rl.cad_obj_num,rl.status
from record_list rl,
     protokol_file pf,
     protokol_export pe
where rl.file_name_id=pf.id    
and pf.protokol_id=pe.id
and pe.id=$protokol_id
) as sp2
group by  \"Всего\"
) as sp

order by 1 desc
    ";
    //echo $query;

    if ($result = mysqli_query($link, $query)) {

        while ($row = mysqli_fetch_assoc($result)) {

            ?>
            <tr>


                <td><?php echo $row['type_object']; ?></td>
                <td><?php echo $row['forpass']; ?></td>
                <td><?php echo $row['pass']; ?></td>
                <td><?php echo $row['nopass']; ?></td>
                <td><?php echo $row['protsent']; ?></td>

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