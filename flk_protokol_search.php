<?php
require_once("config.php");
session_start();
require_once("check_cookies.php");
require_once("functions/fn_misc.php");
require_once("matching.php");

date_default_timezone_set("Europe/Moscow");
if (isset($_GET['search_kn']) and !empty($_GET['search_kn'])) 
{
	$search_kn = $_SESSION['fpr']['search_kn'] = $_GET['search_kn'];
}
elseif(isset($_SESSION['fpr']['search_kn']))
{
	$search_kn = $_SESSION['fpr']['search_kn'];
} 
else
{
	$search_kn = "";
}

$title = 'Результаты поиска КН "'.$search_kn.'" в протоколах ФЛК исходящей выгрузки';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" href="ico/favicon_flk.ico" type="image/x-icon" />
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/input.css">
	<link rel="stylesheet" href="css/select.css">
	<link rel="stylesheet" href="css/table.css">
	<link rel="stylesheet" href="css/colors.css">
	<link rel="stylesheet" href="css/div.css">
	<title>Протокол ФЛК</title>
</head>

<body>
<div class="main">
<h3><?= $title; ?></h3>
<br> Вернуться к <a href="index.php">протоколам ФЛК</a>
<br>
<br>

<table class="main">
	<thead class="main">
	<tr class="main">
		<th class="main min_v1 max_v1"></th>
		<th class="main min_v1 max_v1">Протокол от</th>
		<th class="main min_v1 max_v1">Начало периода</th>
		<th class="main min_v1 max_v1">Конец периода</th>
		<th class="main min_v1 max_v1">Тип выгрузки</th>
		<th class="nobrd main min_v2 max_v1">
		<form method="GET" name="form_search_kn" id="form_search_kn" action="?"></form>
			<input class="date" style="width: 100%;" name="search_kn" form="form_search_kn" placeholder="Поиск по КН" />
		</th>
		<th class="main nobrd">
			<button class="button" form="form_search_kn" type="submit">Найти</button>
		</th>
	</tr>
	</thead>
	<?php

	// Поиск по КН
	if (!empty($search_kn)) 
	{
		$where_search_kn = " rl.cad_obj_num = '".$search_kn."'"; 
	} 
	else
	{
		$where_search_kn = " rl.cad_obj_num = 'null'"; 
	}

	$query = "SELECT 
		rl.cad_obj_num, 
		rl.status,
		pe.id AS id,
		pe.insert_date,
		pe.period_start,
		pe.type_unloading,
		pe.period_stop,
		pe.file_name_xml,
		pe.protokol_uid
	FROM record_list rl
	LEFT JOIN protokol_export pe ON rl.protokol_uid=pe.protokol_uid
	WHERE  
		".$where_search_kn."
	GROUP BY rl.cad_obj_num, 
	rl.status, 
	pe.insert_date, 
	pe.period_start, 
	pe.period_stop, 
	pe.type_unloading,
	pe.file_name_xml
	ORDER BY pe.insert_date DESC
	LIMIT 100";

	//print_r($query);
	
	if ($result = mysqli_query($link, $query)) 
	{
		$k = 0;
		while ($row = mysqli_fetch_assoc($result)) 
		{
			$k = $k + 1;

				$id = $row['id'];

				// Базовый uid протокола
				$arr_buid = explode("-", $row['protokol_uid']);
				$buid = $arr_buid[0]; 
				
				// Преобразуем формат даты для удобного отображения
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['insert_date'])){
					$insert_date = DateTime::createFromFormat('Y-m-d', $row['insert_date']) -> format('d.m.Y');
				}else{
					$insert_date = '';
				}
				
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_start'])){
					$period_start = DateTime::createFromFormat('Y-m-d', $row['period_start']) -> format('d.m.Y');
				}else{
					$period_start = 'ERROR';
				}
				
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_stop'])){
					$period_stop = DateTime::createFromFormat('Y-m-d', $row['period_stop']) -> format('d.m.Y');
				}else{
					$period_stop = 'ERROR';
				}

				// сопоставляем идентификаторы типов выгрузки читаемым именам
				foreach ($arr_type_unloading as $id_type_unloading => $name_type_unloading){
					if ($row['type_unloading'] == $id_type_unloading){break;}
					else $name_type_unloading = "unknown";
				}

				echo '<tr class="hover">';
				if($row['status'] == 'Не прошел флк')
				{
					$file_name_xml = '';
					echo '<td class="main">
					<a href="flk_protokol_records.php?id='.$id.'&buid='.$buid.'">'.$row['status'].'</a>
					</td>';
				}
				else
				{
					$file_name_xml = $row['file_name_xml'];
					echo '<td class="main">
					'.$row['status'].'
					</td>';
				}
				echo '
				<td class="main">'.$insert_date.'</td>
				<td class="main">'.$period_start.'</td>
				<td class="main">'.$period_stop.'</td>
				<td class="main">'.$name_type_unloading.'</td>
				<td class="main" colspan="2">'.$file_name_xml.'</td>
				<td class="main"></td>
			</tr>';
		}
	}

	mysqli_free_result($result);
	mysqli_close($link);
	//clear_url();
	?>
</table>
</div>
</body>
</html>
