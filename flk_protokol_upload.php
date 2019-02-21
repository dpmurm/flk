<?php 
require_once("config.php");
session_start(); 
require_once("check_cookies.php");
require_once("matching.php");
require_once("functions/fn_protokol_upload.php");
require_once("functions/fn_misc.php");
require_once("functions/fn_pagination.php");
// подключаем библиотеку для парсинга xls
//require_once $doc_root."PHPExcel/Classes/PHPExcel.php";
require_once "PHPExcel/PHPExcel.php";

if (isset($_POST['date'])) 
{
	$date = $_SESSION['fpu']['date'] = $_POST['date'];
} 
elseif (isset($_SESSION['fpu']['date']))
{
	$date = $_SESSION['fpu']['date'];
}
else
{
	$date = "";
}

if (isset($_POST['period_start'])) 
{
	$period_start = $_SESSION['fpu']['period_start'] = $_POST['period_start'];
} 
elseif (isset($_SESSION['fpu']['period_start']))
{
	$period_start = $_SESSION['fpu']['period_start'];
}
else
{
	$period_start = "";
}

if (isset($_POST['period_stop'])) 
{
	$period_stop = $_SESSION['fpu']['period_stop'] = $_POST['period_stop'];
} 
elseif (isset($_SESSION['fpu']['period_stop']))
{
	$period_stop = $_SESSION['fpu']['period_stop'];
}
else
{
	$period_stop = "";
}

if (isset($_POST['vid_object'])) 
{
	$vid_object = $_SESSION['fpu']['vid_object'] = $_POST['vid_object'];
} 
elseif (isset($_SESSION['fpu']['vid_object']))
{
	$vid_object = $_SESSION['fpu']['vid_object'];
}
else
{
	$vid_object = "0";
}

if (isset($_POST['type_unloading'])) 
{
	$type_unloading = $_SESSION['fpu']['type_unloading'] = $_POST['type_unloading'];
} 
elseif (isset($_SESSION['fpu']['type_unloading']))
{
	$type_unloading = $_SESSION['fpu']['type_unloading'];
}
else
{
	$type_unloading = "0";
}

if (isset($_GET['search_period_submit'])) 
{
	$search_period_submit = $_SESSION['fpu']['search_period_submit'] = $_GET['search_period_submit'];
} 
elseif (isset($_SESSION['fpu']['search_period_submit']))
{
	$search_period_submit = $_SESSION['fpu']['search_period_submit'];
}
else
{
	$search_period_submit = "0";
}

if (isset($_GET['s_period_start']) and $search_period_submit == "1") 
{
	$s_period_start = $_SESSION['fpu']['s_period_start'] = $_GET['s_period_start'];
} 
elseif (isset($_SESSION['fpu']['s_period_start']) and $search_period_submit == "1")
{
	$s_period_start = $_SESSION['fpu']['s_period_start'];
}
else
{
	$s_period_start = "";
}

if (isset($_GET['s_period_stop']) and $search_period_submit == "1") 
{
	$s_period_stop = $_SESSION['fpu']['s_period_stop'] = $_GET['s_period_stop'];
} 
elseif (isset($_SESSION['fpu']['s_period_stop']) and $search_period_submit == "1")
{
	$s_period_stop = $_SESSION['fpu']['s_period_stop'];
}
else
{
	$s_period_stop = "";
}

if (isset($_GET['s_type_unloading']) and $search_period_submit == "1") 
{
	$s_type_unloading = $_SESSION['fpu']['s_type_unloading'] = $_GET['s_type_unloading'];
} 
elseif (isset($_SESSION['fpu']['s_type_unloading']) and $search_period_submit == "1")
{
	$s_type_unloading = $_SESSION['fpu']['s_type_unloading'];
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
	<title>Загрузка протоколов ФЛК</title>
</head>
<body>
<script type="text/javascript" src="js/flk_protokol_upload.js"></script>

<?php
$title = 'Загрузка протоколов ФЛК исходящей выгрузки';
$curr_date = date("Y-m-d");

/* === FILTER BEGIN === */
if($search_period_submit == "1")
{
	if(isset($s_type_unloading) and $s_type_unloading >= "0")
	{
		if(!empty($s_period_start) or !empty($s_period_stop))
		{
		$s_tu_where = " AND `type_unloading` = '".$s_type_unloading."'";
		$f_tu_string = " по типу выгрузки: вкл.";
		}
		else
		{
		$s_tu_where = "WHERE `type_unloading` = '".$s_type_unloading."'";
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
	$s_where = "WHERE `period_start` >= '".$s_period_start."' 
			AND `period_stop` <= '".$s_period_stop."'".$s_tu_where;
	$f_string = "с ".$fs_period_start." по ".$fs_period_stop;
	}
	elseif(!empty($s_period_start) and empty($s_period_stop))
	{
	$s_where = "WHERE `period_start` >= '".$s_period_start."'".$s_tu_where;
	$f_string = "с ".$fs_period_start;
	}
	elseif(empty($s_period_start) and !empty($s_period_stop))
	{
	$s_where = "WHERE `period_stop` <= '".$s_period_stop."'".$s_tu_where;
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
	$_SESSION['fpu']['search_period_submit'] = "0";
	unset($_SESSION['fpu']['s_period_start']);
	unset($_SESSION['fpu']['s_period_stop']);
	unset($_SESSION['fpu']['s_type_unloading']);
}
/* === FILTER END === */

/* === PAGE COUNTING BEGIN === */
// Количество записей на странице
$on_page = "15";

$query_count_pages = 'SELECT COUNT(id) AS count FROM `protokol_export` '.$s_where; 
$arr_count_pages = fn_count_pages($link, $on_page, $query_count_pages);
$on_page = $arr_count_pages['on_page'];
$num_pages = $arr_count_pages['num_pages'];
$current_page = $arr_count_pages['current_page'];
$start_from = $arr_count_pages['start_from'];
/* === PAGE COUNTING END === */

$query = 'SELECT id,
			insert_date,
			date,
			period_start,
			period_stop,
			visible,
			type_unloading,
			vid_object,
			file_name_xls,file_name_xml,protokol_uid 
		FROM protokol_export '.$s_where.'
		ORDER BY id DESC LIMIT '.$on_page.' OFFSET '.$start_from.'';

echo '
<div class="main">
	<h1>'.$title.'</h1>
	<p>
	На этой странице происходит загрузка <a href="index.php">протоколов ФЛК</a> для организации процесса исправления ошибок.
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
	<br>
	</details>';


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
if(isset($_POST['flk_upload_submit']) && $_POST['flk_upload_submit'] == "add"){
	flk_protokol_add($link, $arr_xls_heads);
}
elseif(isset($_GET['flk_del_submit']) && $_GET['flk_del_submit'] == "del"){
	flk_protokol_delete($link);
}
elseif(isset($_GET['flk_checkbox_submit']) && $_GET['flk_checkbox_submit'] == "change"){
	flk_protokol_update($link);
}
elseif(isset($_GET['rabbit_hole']) && $_GET['rabbit_hole'] == "1"){
	flk_protokol_clear($link);
}
?>

<br>	
	<table class="main">
		<thead class="main">
		<tr>
			<!--
			<th class="main">G</th>
			-->
			<th class="main">Дата выгрузки</th>
			<th class="main">Начало периода</th>
			<th class="main">Конец периода</th>
			<th class="main">Вид</th>
			<th class="main">Тип выгрузки</th>
			<th class="main">Файл протокола</th>
			<th class="main">Файл выгрузки</th>
			<th class="main"></th>
		</tr>
		<tr>
			<form enctype="multipart/form-data" method="POST" name="flk_donwload" action="">
			<!--
			<th class="main"></th>
			-->
			<th class="main">
			<input required type="date" class="date w130" name="date" max="<?= $curr_date; ?>" value="<?= $date; ?>" />
			</th>
			<th class="main">
			<input required type="date" class="date w130" name="period_start" max="<?= $curr_date; ?>" value="<?= $period_start; ?>" />
			</th>
			<th class="main"><input required type="date" class="date w130" name="period_stop" max="<?= $curr_date; ?>" value="<?= $period_stop; ?>" />
			</th>
			<th class="main">
				<select class="main" name="vid_object">
					<?php
					foreach ($arr_vid_object as $id_vid_object => $name_vid_object) 
					{
						if($id_vid_object == $vid_object)
						{
						echo '<option value="'.$id_vid_object.'" selected>'.$name_vid_object.'</option>';
						}
						else
						{
						echo '<option value="'.$id_vid_object.'">'.$name_vid_object.'</option>';
						}
					}
					?>
				</select>
			</th>
			<th class="main">
				<select class="main" name="type_unloading">
					<?php
					foreach ($arr_type_unloading as $id_type_unloading => $name_type_unloading) 
					{
						if($id_type_unloading == $type_unloading)
						{
						echo '<option value="'.$id_type_unloading.'" selected>'.$name_type_unloading.'</option>';
						}
						else
						{
						echo '<option value="'.$id_type_unloading.'">'.$name_type_unloading.'</option>';
						}
					}
					?>
				</select>
			</th>
			<th class="main">
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
				<input required type="file" class="button fileupload w100pt" name="filexls" accept="application/vnd.ms-excel" />
			</th>
			<th class="main">
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
				<input required type="file" class="button fileupload w100pt" name="filexml" accept="text/xml" />
			</th>
			<th class="main">
				<button class="button" type="submit" name="flk_upload_submit" value="add">Добавить</button>
			</th>
			</form>
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
					$date = 'ERROR: date format ['.__LINE__.'] ['.__FILE__.']';
				}
				
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_start'])){
					$period_start = DateTime::createFromFormat('Y-m-d', $row['period_start']) -> format('d.m.Y');
				}else{
					$period_start = 'ERROR: date format ['.__LINE__.'] ['.__FILE__.']';
				}
				
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_stop'])){
					$period_stop = DateTime::createFromFormat('Y-m-d', $row['period_stop']) -> format('d.m.Y');
				}else{
					$period_stop = 'ERROR: date format ['.__LINE__.'] ['.__FILE__.']';
				}

				// сопоставляем идентификаторы типов выгрузки читаемым именам
				foreach ($arr_vid_object as $id_vid_object => $name_vid_object){
					if ($row['vid_object'] == $id_vid_object){break;}
					else $name_vid_object = "unknown";
				}
				foreach ($arr_type_unloading as $id_type_unloading => $name_type_unloading){
					if ($row['type_unloading'] == $id_type_unloading){break;}
					else $name_type_unloading = "unknown";
				}
				
				// если в базе visible == 1, то чекбокс отмечен	
				if($row['visible'] == "1")
				{
					$checked = "checked";
					$del_title = "Удалить протоколы выгрузки от ".$date." за период с ".$period_start." по ".$period_stop."";
				}
				else
				{
					$checked = "";
					$del_title = "Удалить протокол";
				}
				
					$del_confirm = "Удалить протокол?";

				echo '<tr class="hover" title="Загружен '.$insert_date.'">
					<form name="flk_edit'.$k.'" method="GET" action="?">
					<input type="hidden" name="id" value="'.$row['id'].'" />
					<input type="hidden" name="protokol_uid" value="'.$row['protokol_uid'].'" />
					<!-- 
					<td class="main"> 
					<input type="hidden" name="visible" value="0" />
					<input type="checkbox" class="checkbox" id="checkbox'.$k.'" '.$checked.' name="visible" value="1" 
					 title="Отметьте протокол, если хотите удалить все протоколы этой выгрузки"onchange="checkbox_submit(\'flk_edit'.$k.'\', \'flk_checkbox_submit\', \'change\');" />
					<label for="checkbox'.$k.'"  title="Если протокол отмечен, при его удалении будут удалены все протоколы этой выгрузки"></label>
					</td>
					-->
					<td class="main">'.$date.'</td>
					<td class="main">'.$period_start.'</td>
					<td class="main">'.$period_stop.'</td>
					<td class="main">'.$name_vid_object.'</td>
					<td class="main">'.$name_type_unloading.'</td>
					<td class="main filelst">'.$row['file_name_xls'].'</td>
					<td class="main filelst">'.$row['file_name_xml'].'</td>
					<td class="main center">
					<button type="submit" class="button w20h20" name="flk_del_submit" title="'.$del_title.'" value="del" onclick="return confirm(\''.$del_confirm.'\')">&#10005;</button>
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
