<?php
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=protokol.xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
//header("Content-Type: application/vnd.ms-excel");
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
    <title>ИСПОЛНЕНО</title>
</head>
<body>
<!-- <h2><font color=red>Протокол передачи сведений в ФНС <?= $date ?> номер <?= $number ?> за период с <?= $period_start ?> по <?= $period_stop ?></font></h2>-->
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th>Решение</th>
        <th>№ пп</th>
        <th>Район</th>
        <th>Кадастровый№</th>
        <th>Вид объекта</th>
        <th>Описание ошибки ФЛК</th>
        <th>Местонахождение ошибки в XML файле</th>
        <th>Наименование атрибута</th>
        <th>Значение атрибута</th>
        <th>Вид сведений</th>

    </tr>
    <?php

    include_once("config.php");

    $query = "select rl.id,
substr(rl.cad_obj_num, 4, 2) rayon,
rl.cad_obj_num, rl.type_object, 
rl.error_text, rl.error_path_xml, rl.atribut_name, rl.atribut_value, rl.error_type,
case 
  WHEN  ifnull(rn.decision_type,0)=0 THEN 'Не обработано' 
  WHEN  ifnull(rn.decision_type,0)=1 THEN 'В работе' 
  WHEN  ifnull(rn.decision_type,0)=2 THEN 'Исправлена' 
  WHEN  ifnull(rn.decision_type,0)=3 THEN 'Невозможно исправить' 
  WHEN  ifnull(rn.decision_type,0)=4 THEN 'Не обнаружена' 
  ELSE  ifnull(rn.decision_type,0) 
END
 AS reshenie
from record_list rl
left join record_notes rn on rl.id=rn.record_list_id   
left join protokol_file pf on rl.file_name_id=pf.id 
left join protokol_export pe  on pf.protokol_id=pe.id
where 
rl.status!='Прошел флк'
and pe.id='$protokol_id'
order by 2, 4";
    //echo $query;

    if ($result = mysqli_query($link, $query)) {
        $k = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $k = $k + 1;
            ?>
            <tr>
                <td><?php echo $row['reshenie']; ?></td>
                <td><?= $k ?></td>
                <td><?php echo $row['rayon']; ?></td>
                <td><?php echo $row['cad_obj_num']; ?></td>
                <td><?php echo $row['type_object']; ?></td>
                <td><?php echo $row['error_text']; ?></td>
                <td><?php echo $row['error_path_xml']; ?></td>
                <td><?php echo $row['atribut_name']; ?></td>
                <td><?php echo $row['atribut_value']; ?></td>
                <td><?php echo $row['error_type']; ?></td>
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