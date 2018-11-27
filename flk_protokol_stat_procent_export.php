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
    $period_start = $_GET['period_start']; // Если нет номера отдела берется 0
} else {
    $period_start = 0;
}
if (isset($_GET['period_stop'])) {
    $period_stop = $_GET['period_stop']; // Если нет номера отдела берется 0
} else {
    $period_stop = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Процент прохождения сведений в ФНС</title>
</head>

<body>

<h2><font color=red>Процент прохождения сведений в ФНС по протоколу от <?= $year ?> года номер <?= $number ?> за период с <?= $period_start ?> по <?= $period_stop ?> </font></h2>


<table width="60%" border="1" cellspacing="0" cellpadding="0">
    <tr>

        <th>Вид объекта</th>
        <th>Документов для выгрузки</th>
        <th>Выгружено</th>
        <th>Не прошли ФЛК</th>
        <th>Процент прохождения</th>

    </tr>
    <?php

    include_once("config.php");

    $query = "select sp.type_object, (sp.pass+sp.nopass) as forpass, sp.pass, sp.nopass, (sp.pass/(sp.pass+sp.nopass))*100 as protsent
from
 (
select  rl.type_object ,  sum( IF (rl.status = 'Прошел флк', 1, 0) ) AS pass, sum(IF (rl.status = 'Не прошел флк', 1, 0)) AS nopass
from record_list rl,
     protokol_file pf,
     protokol_export pe
where rl.file_name_id=pf.id    
and pf.protokol_id=pe.id
and pe.id=$protokol_id
group by  rl.type_object
union
select  'Всего' , sum( IF (rl.status = 'Прошел флк', 1, 0) ) AS pass, sum(IF (rl.status = 'Не прошел флк', 1, 0)) AS nopass
from record_list rl,
     protokol_file pf,
     protokol_export pe
where rl.file_name_id=pf.id    
and pf.protokol_id=pe.id
and pe.id=$protokol_id
group by  'Всего'
) as sp

order by 1 desc";
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