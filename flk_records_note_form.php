<?php
date_default_timezone_set("Europe/Moscow");
if (isset($_GET['record_id'])) {
    $record_id = $_GET['record_id']; // Если нет номера отдела берется 0
} else {
    $record_id = 0;
}
if (isset($_GET['protokol_id'])) {
    $protokol_id = $_GET['protokol_id']; // Если нет номера отдела берется 0
} else {
    $protokol_id = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Запись протокола ФЛК</title>
</head>

<body>

<h2><font color=red>Запись протокола ФЛК</font></h2>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <?php

    include_once("config.php");

    $query = "select 
rl.id,
substr(rl.cad_obj_num, 4, 2) rayon,
rl.cad_obj_num, rl.type_object, 
rl.error_text, rl.error_path_xml, rl.atribut_name, rl.atribut_value, rl.error_type
from record_list rl
where
rl.id='$record_id'
and rl.status!='Прошел флк' 
order by 2, 4";
    //echo $query;

    if ($result = mysqli_query($link, $query)) {

        while ($row = mysqli_fetch_assoc($result)) {

            ?>

                <tr><td width="30%">Кадастровый№</td><td><?php echo $row['cad_obj_num']; ?></td></tr>
                <tr><td>Вид объекта</td><td><?php echo $row['type_object']; ?></td></tr>
                <tr><td>Описание ошибки ФЛК</td><td><font color=red><?php echo $row['error_text']; ?></font></td></tr>
                <tr><td>Местонахождение ошибки в XML файле</td><td><?php echo $row['error_path_xml']; ?></td></tr>
                <tr><td>Наименование атрибута</td><td><?php echo $row['atribut_name']; ?></td></tr>
                <tr><td>Значение атрибута</td><td><?php echo $row['atribut_value']; ?></td></tr>
                <tr><td>Вид сведений</td><td><?php echo $row['error_type']; ?></td></tr>

            <?php
            $record_list_id=$row['id'];
        }
    }

    mysqli_free_result($result);
    //mysqli_close($link);

    ?>
</table>

<h2><font color=green>Информация об исправлении ошибки ФЛК</font></h2>
<form action="flk_records_note_form_insert.php" method="get">
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <?php

    $query2 = "select 
rn.id, rn.decision_type, rn.reg_no, rn.`text`
from record_notes rn
where 
rn.record_list_id=$record_list_id";
    //echo $query2;

    $result2 = mysqli_query($link, $query2);
    $row2 = mysqli_fetch_assoc($result2) ;
            ?>
            <tr><td width="30%">Решение по исправлению</td>
                <td>
                    <input type="radio" name="decision_type" value=0 <?php if (!isset($row2['decision_type']) or $row2['decision_type']==0)  echo 'checked'; ?> >Не обработана<br>
                    <input type="radio" name="decision_type" value=1  <?php if (isset($row2['decision_type']) and $row2['decision_type']==1)  echo 'checked'; ?> >В работе<br>
                    <input type="radio" name="decision_type" value=2  <?php if (isset($row2['decision_type']) and $row2['decision_type']==2)  echo 'checked'; ?> >Исправлена<br>
                    <input type="radio" name="decision_type" value=3  <?php if (isset($row2['decision_type']) and $row2['decision_type']==3)  echo 'checked'; ?> >Невозможно исправить<br>
                    <input type="radio" name="decision_type" value=4  <?php if (isset($row2['decision_type']) and $row2['decision_type']==4)  echo 'checked'; ?> >Не обнаружена<br>
                </td>
            </tr>
            <input type="hidden" name="record_notes_id" value="<?php //$row2[id]?>" />
            <tr><td width="30%">Номер заявки на техошибку в ЕГРН</td><td><textarea name="reg_no" type="text" rows="1" cols="100"><?php echo $row2['reg_no']; ?></textarea></td></tr>
            <tr><td width="30%">Комментарий</td><td><textarea name="text" type="text" rows="10" cols="100"><?= $row2['text']; ?></textarea></td></tr>
            <?php

    mysqli_free_result($result2);
    mysqli_close($link);

    ?>
</table>
    <input type="hidden" name="protokol_id" value="<?=$protokol_id?>" />
    <input type="hidden" name="record_list_id" value="<?=$record_list_id?>" />
    <input name="Submit" type="submit" id="Submit" value="Сохранить" />
</form>

</body>
</html>