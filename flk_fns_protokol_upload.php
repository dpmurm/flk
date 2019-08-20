<?php
require_once("config.php");
session_start();
require_once("matching.php");
require_once("functions/fn_fns_protokol_upload.php");
require_once("functions/fn_misc.php");
require_once("functions/fn_pagination.php");

if (isset($_GET['search_period_submit'])) 
{
	$search_period_submit = $_SESSION['ffpu']['search_period_submit'] = $_GET['search_period_submit'];
} 
elseif (isset($_SESSION['ffpu']['search_period_submit']))
{
	$search_period_submit = $_SESSION['ffpu']['search_period_submit'];
}
else
{
	$search_period_submit = "0";
}

if (isset($_GET['s_period_start']) and $search_period_submit == "1") 
{
	$s_period_start = $_SESSION['ffpu']['s_period_start'] = $_GET['s_period_start'];
} 
elseif (isset($_SESSION['ffpu']['s_period_start']) and $search_period_submit == "1")
{
	$s_period_start = $_SESSION['ffpu']['s_period_start'];
}
else
{
	$s_period_start = "";
}

if (isset($_GET['s_period_stop']) and $search_period_submit == "1") 
{
	$s_period_stop = $_SESSION['ffpu']['s_period_stop'] = $_GET['s_period_stop'];
} 
elseif (isset($_SESSION['ffpu']['s_period_stop']) and $search_period_submit == "1")
{
	$s_period_stop = $_SESSION['ffpu']['s_period_stop'];
}
else
{
	$s_period_stop = "";
}

if (isset($_GET['s_type_unloading']) and $search_period_submit == "1") 
{
	$s_type_unloading = $_SESSION['ffpu']['s_type_unloading'] = $_GET['s_type_unloading'];
} 
elseif (isset($_SESSION['ffpu']['s_type_unloading']) and $search_period_submit == "1")
{
	$s_type_unloading = $_SESSION['ffpu']['s_type_unloading'];
}
else
{
	$s_type_unloading = "-1";
}

// Преобразуем формат даты для удобного отображения
if($formated_date = DateTime::createFromFormat('Y-m-d', $s_period_start))
{
$fs_period_start = DateTime::createFromFormat('Y-m-d', $s_period_start) -> format('d.m.Y');
}
else
{
$fs_period_start = $s_period_start;
}

if($formated_date = DateTime::createFromFormat('Y-m-d', $s_period_stop))
{
$fs_period_stop = DateTime::createFromFormat('Y-m-d', $s_period_stop) -> format('d.m.Y');
}
else
{
$fs_period_stop = $s_period_stop;
}
?>

<!doctype html>
<html lang="ru">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/colors.css">
	<link rel="stylesheet" href="css/table.css">
	<link rel="stylesheet" href="css/input.css">
	<link rel="stylesheet" href="css/select.css">
	<link rel="stylesheet" href="css/div.css">
	<title>Загрузка протоколов ФЛК ФНС</title>
</head>
<body>

<?php
$title = 'Загрузка протоколов ФЛК ФНС';
$curr_date = date("Y-m-d");

/* === FILTER BEGIN === */
if($search_period_submit == "1")
{
	if(isset($s_type_unloading) and $s_type_unloading >= "0")
	{
		if(!empty($s_period_start) or !empty($s_period_stop))
		{
		$s_tu_where = " AND pe.type_unloading = '".$s_type_unloading."'";
		$f_tu_string = " по типу выгрузки: вкл.";
		}
		else
		{
		$s_tu_where = "WHERE pe.type_unloading = '".$s_type_unloading."'";
		$f_tu_string = " по типу выгрузки: вкл.";
		}
	}
	else
	{
	$s_tu_where = "";
	$f_tu_string = "";
	}

	if(!empty($s_period_start) and !empty($s_period_stop))
	{
	$s_where = "WHERE pe.period_start >= '".$s_period_start."' 
			AND pe.period_stop <= '".$s_period_stop."'".$s_tu_where;
	$f_string = "с ".$fs_period_start." по ".$fs_period_stop;
	}
	elseif(!empty($s_period_start) and empty($s_period_stop))
	{
	$s_where = "WHERE pe.period_start >= '".$s_period_start."'".$s_tu_where;
	$f_string = "с ".$fs_period_start;
	}
	elseif(empty($s_period_start) and !empty($s_period_stop))
	{
	$s_where = "WHERE pe.period_stop <= '".$s_period_stop."'".$s_tu_where;
	$f_string = "по ".$fs_period_stop;
	}
	else
	{
	$s_where = $s_tu_where;
	$f_string = "";
	}
}
else
{
	$_SESSION['ffpu']['search_period_submit'] = "0";
	unset($_SESSION['ffpu']['s_period_start']);
	unset($_SESSION['ffpu']['s_period_stop']);
	unset($_SESSION['ffpu']['s_type_unloading']);
    $s_where = "";
    $f_string = "";
    $f_tu_string = "";
}
/* === FILTER END === */

/* === PAGE COUNTING BEGIN === */
// Количество записей на странице
$on_page = "15";

$query_count_pages = 'SELECT COUNT(id) AS count FROM `protokol_file_fns` '.$s_where;
$arr_count_pages = fn_count_pages($link, $on_page, $query_count_pages);
$on_page = $arr_count_pages['on_page'];
$num_pages = $arr_count_pages['num_pages'];
$current_page = $arr_count_pages['current_page'];
$start_from = $arr_count_pages['start_from'];
/* === PAGE COUNTING END === */

$query = 'SELECT pff.id, 
			pff.insert_date,
			pff.idfile_fns_xml, 
			pff.file_urr_xml, 
			pff.protokol_id, 
			pe.date, 
			pe.period_start, 
			pe.period_stop, 
			pe.type
		FROM protokol_file_fns pff 
		LEFT JOIN protokol_export pe ON pff.protokol_id=pe.id ' .$s_where. '
		ORDER BY id DESC LIMIT ' .$on_page. ' OFFSET ' .$start_from ;

echo '
<div class="main">
	<h1>'.$title.'</h1>
	<p>
	На этой странице происходит загрузка <a href="index_fns.php">протоколов ФЛК ФНС</a> для организации процесса исправления ошибок.
	<br>
	<details>
	<summary style="text-decoration: none; cursor: pointer;">
	Фильтр '.$f_string.$f_tu_string.'
	</summary>
	<form method="GET" name="search_period" action="?">
	<table class="nobrd" style="width: 30%">
	<tr>
		<th class="main nobrd">
		Начало периода
		</th>
		<th class="main nobrd">
		Конец периода
		</th>
		<th class="main nobrd">
		Тип выгрузки
		</th>
		<th class="main nobrd"></th>
		<th class="main nobrd"></th>
	</tr>
	<tr>
		<td class="main nobrd">
		<input type="date" class="date w130" name="s_period_start" min="1998-01-01 " max="'.$curr_date.'" value="'.$s_period_start.'" />
		</td>
		<td class="main nobrd">
		<input type="date" class="date w130" name="s_period_stop" min="1998-01-01 " max="'.$curr_date.'" value="'.$s_period_stop.'" />
		</td>
		<td class="main nobrd">
			<select class="main nobrd" name="s_type_unloading">
				<option value="-1">Не выбрано</option>';
				foreach ($arr_type_unloading as $id_s_type_unloading => $name_s_type_unloading) 
				{
					if($id_s_type_unloading == $s_type_unloading)
					{
					echo '<option value="'.$id_s_type_unloading.'" selected>'.$name_s_type_unloading.'</option>';
					}
					else
					{
					echo '<option value="'.$id_s_type_unloading.'">'.$name_s_type_unloading.'</option>';
					}
				}
		echo '</select>
		</td>
		<td class="main nobrd">
		<button class="button" type="submit" name="search_period_submit" value="1">Применить</button>
		</td>
		<td class="main nobrd">
		<button class="button" type="submit" name="search_period_submit" value="0">Сбросить</button>
		</td>
	</tr>
	</table>
	</form>
	</details>
	<br>';


// Окошко дебага
/*
echo '<details>
<summary style="text-decoration: none; cursor: pointer;">
info
</summary>
<pre>';
//print_r($_SERVER);
echo '$_POST:<br>';
print_r($_POST);
echo '<br>$_GET:<br>';
print_r($_GET);
echo '<br>$_FILES:<br>';
print_r($_FILES);
echo '<br>$_SESSION:<br>';
print_r($_SESSION);
echo '<br>$_COOKIE:<br>';
print_r($_COOKIE);
echo '</pre>
<form name="rabbit_hole" method="GET" action="?">
<button class="button" type="submit" name="rabbit_hole" value="1" onclick="return confirm(\'Вы уверены?\')">
Очистить базу
</button>
</form>
</details>
<br>';
*/

// В зависимости от отправленного запроса (нажатой кнопки) дергаем нужные функции
if(isset($_POST['flk_fns_upload_submit']) && $_POST['flk_fns_upload_submit'] == "add"){
	flk_fns_protokol_add($link);
}
elseif(isset($_GET['flk_fns_del_submit']) && $_GET['flk_fns_del_submit'] == "del"){
	flk_fns_protokol_delete($link);
}
elseif(isset($_GET['rabbit_hole']) && $_GET['rabbit_hole'] == "1"){
	flk_fns_protokol_clear($link);
}
?>
	<table class="main nobrd" style="width: 30%">
	<tr>
		<th class="main nobrd">
		Файл протокола ФНС
		</th>
		<th class="main nobrd"></th>
	</tr>
	<tr>
		<form enctype="multipart/form-data" method="POST" name="flk_fns_donwload" action="">
		<td class="main nobrd">
			<input type="hidden" name="MAX_FILE_SIZE" value="104857600" />
			<input required type="file" class="button fileupload w100pt" name="filefnsxml" accept="text/xml" />
		</td>
		<td class="main nobrd">
			<button class="button" type="submit" name="flk_fns_upload_submit" value="add">Добавить</button>
		</td>
		</form>
	</tr>
	</table>

<br>	
	<table class="main">
		<thead class="main">
		<tr>
			<th class="main"></th>
			<th class="main"></th>
			<th class="main"></th>
			<th class="main"></th>
			<th class="main"></th>
			<th class="main"></th>
			<th class="main"></th>
		</tr>
		<tr>
			<th class="main" style="min-width: 110px">Дата выгрузки</th>
			<th class="main" style="min-width: 120px">Начало периода</th>
			<th class="main" style="min-width: 120px">Конец периода</th>
			<th class="main" style="min-width: 130px">Тип выгрузки</th>
			<th class="main">Файл выгрузки</th>
			<th class="main">Файл протокола ФНС</th>
			<th class="main"></th>
		</tr>
		</thead>
		<tbody>

		<?php
		if ($result = mysqli_query($link, $query)) {
			$k = 0;
			while ($row = mysqli_fetch_assoc($result)) {
				$k = $k + 1;
				
				// Преобразуем формат даты для удобного отображения
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['insert_date'])){
					$insert_date = DateTime::createFromFormat('Y-m-d', $row['insert_date']) -> format('d.m.Y');
				}else{
					$insert_date = '';
				}
				
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['date'])){
					$date = DateTime::createFromFormat('Y-m-d', $row['date']) -> format('d.m.Y');
				}else{
					$date = 'ERROR';
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
					if ($row['type'] == $id_type_unloading){break;}
					else $name_type_unloading = "unknown";
				}

				echo '<tr class="hover" title="Загружен '.$insert_date.'">
					<form name="flk_edit'.$k.'" method="GET" action="?">
					<input type="hidden" name="id" value="'.$row['id'].'" />
					<input type="hidden" name="protokol_id" value="'.$row['protokol_id'].'" />
					<td class="main">'.$date.'</td>
					<td class="main">'.$period_start.'</td>
					<td class="main">'.$period_stop.'</td>
					<td class="main">'.$name_type_unloading.'</td>
					<td class="main filelst">'.$row['file_urr_xml'].'</td>
					<td class="main filelst">'.$row['idfile_fns_xml'].'</td>
					<td class="main center">
					<button type="submit" class="button w20h20" name="flk_fns_del_submit" title="Удалить протокол"  value="del" onclick="return confirm(\'Вы уверены?\')">&#10005;</button>
					</td>
					</form>
				</tr>';
			}
		}
		mysqli_free_result($result);
		mysqli_close($link);
		clear_url();
		?>
		</tbody>
	</table>
</div>
	<div class="center">
		<?php
		// Вывод списка страниц
		fn_list_pages($current_page, $num_pages);
		?>
	</div>
</body>
