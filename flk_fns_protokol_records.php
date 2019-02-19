<?php
require_once("config.php");
session_start();
require_once("check_cookies.php");
require_once("functions/fn_misc.php");
require_once("matching.php");

date_default_timezone_set("Europe/Moscow");

if (isset($_GET['buid'])) 
{
	$buid = $_SESSION['ffpr']['buid'] = $_GET['buid'];
} 
elseif (isset($_SESSION['ffpr']['buid']))
{
	$buid = $_SESSION['ffpr']['buid'];
}
else
{
	$buid = 0;
}

if (isset($_GET['id'])) 
{
	$id = $_SESSION['ffpr']['id'] = $_GET['id'];
} 
elseif (isset($_SESSION['ffpr']['id'])) 
{
	$id = $_SESSION['ffpr']['id'];
}
else 
{
	$id = 0;
}

if (isset($_GET['sel_rayon'])  && is_numeric($_GET['sel_rayon'])) 
{
	$sel_rayon = $_SESSION['ffpr']['sel_rayon'] = $_GET['sel_rayon'];
}
elseif (isset($_SESSION['ffpr']['sel_rayon'])  && is_numeric($_SESSION['ffpr']['sel_rayon'])) 
{
	$sel_rayon = $_SESSION['ffpr']['sel_rayon'];
}
else 
{
	$sel_rayon = "-1";
}

if (isset($_GET['sel_reshenie'])  && is_numeric($_GET['sel_reshenie'])) 
{
	$sel_reshenie = $_SESSION['ffpr']['sel_reshenie'] = $_GET['sel_reshenie'];
}
elseif (isset($_SESSION['ffpr']['sel_reshenie'])  && is_numeric($_SESSION['ffpr']['sel_reshenie'])) 
{
	$sel_reshenie = $_SESSION['ffpr']['sel_reshenie'];
}
else 
{
	$sel_reshenie = "-1";
}

// Получаем период выгрузки
$query_period = "SELECT pef.id AS id, 
			pe.period_start AS period_start, 
			pe.period_stop AS period_stop 
			FROM protokol_export_fns pef 
			left join protokol_export pe on pef.protokol_uid=pe.protokol_uid
			WHERE pef.id = '".$id."'";
$result_period = mysqli_query($link, $query_period);
$arr_period = mysqli_fetch_assoc($result_period);
$period_start = $arr_period['period_start'];
$period_stop = $arr_period['period_stop'];

// Преобразуем формат даты для удобного отображения
if($formated_date = DateTime::createFromFormat('Y-m-d', $period_start)){
	$period_start = DateTime::createFromFormat('Y-m-d', $period_start) -> format('d.m.Y');
}else{
	$period_start = '';
}

if($formated_date = DateTime::createFromFormat('Y-m-d', $period_stop)){
	$period_stop = DateTime::createFromFormat('Y-m-d', $period_stop) -> format('d.m.Y');
}else{
	$period_stop = '';
}
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
	<title>Протокол ФЛК ФНС</title>
</head>

<body>

<h3>Протокол ФЛК ФНС за период с <?= $period_start ?> по <?= $period_stop ?></h3>
<br> Вернуться к <a href="index_fns.php">протоколам ФЛК ФНС</a><br><br><br>

<form method="GET" name="select_rayon" action="?">
	<select class="main" name="sel_rayon">
	<option value="-1">Кад. район не выбран</option>';
<?php
	// Список районов
	$rayon_result = mysqli_query($link, 'SELECT number,name FROM `kad_rayon`');
 	while($row = mysqli_fetch_assoc($rayon_result)){
		$arr_rayon[$row['number']] = $row['name'];
	}
	asort($arr_rayon);
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
	<button class="button" type="submit">Применить</button>
</form>
<br>

<table width="100%" border="1" cellspacing="0" cellpadding="0">
	<thead>
		<th></th>
		<th>№ пп</th>
		<th>Район</th>
		<th>Кадастровый№</th>
		<th>Вид объекта</th>
		<th>Текст ошибки ФЛК</th>
		<th>Значение элемента</th>

	</thead>
	<?php

	// Условия фильтра
	if ($sel_rayon >= 0 && $sel_reshenie >= 0)
	{
		$where_sel = "
		and rl.cad_obj_num LIKE '__:".$sel_rayon.":%'
		and ifnull(rnf.decision_type,0) = ".$sel_reshenie."";
	}
	elseif ($sel_rayon >= 0)
	{
		$where_sel = "
		and rl.cad_obj_num LIKE '__:".$sel_rayon.":%'";
	}
	elseif ($sel_reshenie >= 0)
	{
		$where_sel = "
		and ifnull(rnf.decision_type,0) = ".$sel_reshenie."";
	}
	else
	{
		$where_sel = "";
	}
	
	$query = "SELECT rlf.id AS id,
		rlf.error_id, 
		rlf.error_text, 
		rlf.error_value,
		rl.cad_obj_num,	
		substr(rl.cad_obj_num, 4, 2) AS rayon,
		rl.type_object,
		ifnull(rnf.decision_type, 0) AS reshenie
		from record_list_fns rlf
		LEFT JOIN record_list rl ON rlf.error_id=rl.guid_doc   
		LEFT JOIN record_notes_fns rnf ON rlf.id=rnf.record_list_id   
		WHERE 
		rlf.protokol_uid LIKE '".$buid."-%'
		AND rl.protokol_uid LIKE '".$buid."-%'
		".$where_sel."
		ORDER BY rayon, rl.cad_obj_num, rl.type_object";
	//print_r($query);
	
	if ($result = mysqli_query($link, $query)) {
		$k = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$k = $k + 1;
			
			// сопоставляем id решения читаемым именам
			foreach ($arr_reshenie as $id_reshenie => $name_reshenie){
				if ($row['reshenie'] == $id_reshenie){break;}
				else {$name_reshenie = "UNKNOWN";}
			}
		
			echo '<tr class="hover">

				<td id="'.$row['id'].'"><a href="flk_fns_records_note_form.php?record_id='.$row['id'].'">'.$name_reshenie.'</a></td>
				<td>'.$k.'</td>
				<td>'.$row['rayon'].'</td>
				<td>'.$row['cad_obj_num'].'</td>
				<td>'.$row['type_object'].'</td>
				<td>'.$row['error_text'].'</td>
				<td>'.$row['error_value'].'</td>
			</tr>';
		}
	}
	clear_url();
	mysqli_free_result($result);
	mysqli_close($link);

	?>
</table>
</body>
</html>
