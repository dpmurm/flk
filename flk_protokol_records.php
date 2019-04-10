<?php
require_once("config.php");
session_start();
require_once("check_cookies.php");
require_once("functions/fn_misc.php");
require_once("functions/fn_knlist.php");
require_once("matching.php");

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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['protokol_id']))
{
    $protokol_id = $_SESSION['fpr']['protokol_id'] = $_GET['protokol_id'];
}
elseif (isset($_SESSION['fpr']['protokol_id']))
{
    $protokol_id = $_SESSION['fpr']['protokol_id'];
}
else
{
    $protokol_id = 0;
}

if (isset($_GET['id']))
{
    $id = $_SESSION['fpr']['id'] = $_GET['id'];
}
elseif (isset($_SESSION['fpr']['id']))
{
    $id = $_SESSION['fpr']['id'];
}
else
{
    $id = 0;
}

if (isset($_GET['sel_rayon'])  )
{
    $sel_rayon = $_SESSION['fpr']['sel_rayon'] = $_GET['sel_rayon'];
}
elseif (isset($_SESSION['fpr']['sel_rayon'])  && is_numeric($_SESSION['fpr']['sel_rayon']))
{
    $sel_rayon = $_SESSION['fpr']['sel_rayon'];
}
else
{
    $sel_rayon = "-1";
}

if (isset($_GET['sel_reshenie'])  && is_numeric($_GET['sel_reshenie']))
{
    $sel_reshenie = $_SESSION['fpr']['sel_reshenie'] = $_GET['sel_reshenie'];
}
elseif (isset($_SESSION['fpr']['sel_reshenie'])  && is_numeric($_SESSION['fpr']['sel_reshenie']))
{
    $sel_reshenie = $_SESSION['fpr']['sel_reshenie'];
}
else
{
    $sel_reshenie = "-1";
}

if (isset($_GET['sel_vid_object']))
{
    $sel_vid_object = $_SESSION['fpr']['sel_vid_object'] = $_GET['sel_vid_object'];
}
elseif (isset($_SESSION['fpr']['sel_vid_object']))
{
    $sel_vid_object = $_SESSION['fpr']['sel_vid_object'];
}
else
{
    $sel_vid_object = "-1";
}
//echo $sel_vid_object;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/input.css">
    <link rel="stylesheet" href="css/select.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/colors.css">
    <title>Протокол ФЛК</title>
</head>

<body>

<h2><font color=red>Протокол передачи сведений в ФНС<!-- <?= $year ?> года номер <?= $number ?> за период с <?= $period_start ?> по <?= $period_stop ?>--></font></h2>
<br> Вернуться к <a href="index.php">протоколам ФЛК</a><br>

<form method="GET" name="select_rayon" action="?">
    <select class="main" name="sel_rayon">
        <option value="-1">Кад. район не выбран</option>
        <?php
        // Список районов
        $rayon_result = mysqli_query($link, 'SELECT number,name, region FROM `kad_rayon` order by region, number');
        while($row = mysqli_fetch_assoc($rayon_result)){
            $arr_rayon[$row['region'].":".$row['number']] = $row['name'];
        }
        //asort($arr_rayon);
        foreach ($arr_rayon as $number_rayon => $name_rayon){
            if (isset($sel_rayon) && $sel_rayon == $number_rayon)
            {
                echo '<option value="'.$number_rayon.'" selected>'.$name_rayon.' ('.$number_rayon.')</option>';
            }
            else
            {
                echo '<option value="'.$number_rayon.'">'.$name_rayon.' ('.$number_rayon.')</option>';
            }
        }
        ?>
    </select>&nbsp;
    <select class="main" name="sel_reshenie">
        <option value="-1">Статус не выбран</option>';
        <?php
        // Список решений
        foreach ($arr_reshenie as $number_reshenie => $name_reshenie){
            if (isset($sel_reshenie) && $sel_reshenie == $number_reshenie)
            {
                echo '<option value="'.$number_reshenie.'" selected>'.$name_reshenie.'</option>';
            }
            else
            {
                echo '<option value="'.$number_reshenie.'">'.$name_reshenie.'</option>';
            }
        }
        ?>
    </select>
    <select class="main" name="sel_vid_object" >
        <option value="-1">Все объекты</option>

        <?php
        // Вид объекта
        foreach ($arr_vid_object as $number_vid_object => $name_vid_object){
            if (isset($sel_vid_object) && $sel_vid_object == $number_vid_object)
            {
                echo '<option value="'.$number_vid_object.'" selected>'.$name_vid_object.'</option>';
            }
            else
            {
                echo '<option value="'.$number_vid_object.'">'.$name_vid_object.'</option>';
            }
        }
        ?>
    </select>&nbsp;
    <button class="button" type="submit">Применить</button>
</form>
<br>
<?php
//Условия фильтра
$where_sel = "";

if ($sel_rayon >= 0)
{
    $where_sel .= " AND rl.cad_obj_num LIKE '".$sel_rayon.":%'";
}
if ($sel_reshenie >= 0)
{
    $where_sel .= " AND ifnull(rn.decision_type,0) = '".$sel_reshenie."'";
}
if ($sel_vid_object == 'ЗУ')
{
    $where_sel .= " AND rl.type_object = 'Земельный участок'";
}
if ($sel_vid_object == 'ОКС')
{
    $where_sel .= " AND (rl.type_object = 'Здание/Сооружение' OR rl.type_object = 'Помещение')";
}
//Конец фильтра
?>
<!-- === SHOW_KN_FOR_KORR BEGIN === -->
<?php
// показываем ссылку на список КН, если наш ip в списке разрешенных
$ipaddr = $_SERVER['REMOTE_ADDR'];
if (in_array("$ipaddr", $arr_ip_allow))
{
    echo '<a href="?show_kn=1">Список КН</a><br>';
}
if (isset($_GET['show_kn']) && $_GET['show_kn'] == 1)
{
    show_kn($link, $protokol_id, $where_sel);
}
?>
<br>
<!-- === SHOW_KN_FOR_KORR END === -->
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th></th>
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
$where_sel
order by 2, 4";
    //echo $query;
    //echo $where_sel;

    if ($result = mysqli_query($link, $query)) {
        $k = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $k = $k + 1;
            ?>
            <tr>

                <td id="<?php echo $row['id']; ?>"><a href="flk_records_note_form.php?record_id=<?= $row['id']; ?>&protokol_id=<?= $protokol_id; ?>"><?php echo $row['reshenie']; ?></a></td>
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
    clear_url();
    ?>
</table>
</body>
</html>