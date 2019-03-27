<?php 
require_once("config.php");
session_start();
require_once("matching.php");
?>

<!doctype html>
<html lang="ru">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/table.css">
	<title>Протоколы ФЛК 2 уровня</title>
</head>
<body>

<?php
$title = 'Протоколы ФЛК 2 уровня';
$ipaddr = $_SERVER['REMOTE_ADDR'];
$query = 'SELECT pff.id AS id, 
			pff.insert_date AS insert_date, 
			pe.period_start AS period_start, 
			pe.period_stop AS period_stop, 
			pe.type, 
			pe.id AS protokol_uid 
			FROM protokol_file_fns pff 
			left join protokol_export pe on pff.protokol_id=pe.id
			GROUP BY pe.date, 
				pe.period_start, 
				pe.period_stop, 
				pe.type
			ORDER BY id DESC';
?>
<div>
	<h1><?= $title; ?></h1>

	<p>На этой странице размещен список протоколов ФЛК ФНС для организации процесса
		исправления ошибок путем внесения отметок об их исправлении.<br>
	<!--	Также доступна <a href="index_stat.php">статистика по протоколам ФЛК</a> -->
	</p>

	<table class="main">
		<thead class="main">
		<tr class="main">
			<th class="main min_v1 max_v1">
			<?php
			 //показываем ссылку на загрузку протоколов, если наш ip в списке разрешенных
			if (in_array("$ipaddr", $arr_ip_allow)){
				echo '<a href="flk_fns_protokol_upload.php">Загрузить</a>';
			}
			?>
			</th>
			<th class="main min_v1 max_v1">Дата загрузки</th>
			<th class="main min_v1 max_v1">Начало периода</th>
			<th class="main min_v1 max_v1">Конец периода</th>
			<th class="main min_v1 max_v1">Тип выгрузки</th>
			<th class="main min_v2 max_v1"></th>
			<th class="main min_v2"></th>
		</tr>
		</thead>
		<tbody>

		<?php
		if ($result = mysqli_query($link, $query)) {
			$k = 0;
			while ($row = mysqli_fetch_assoc($result)) {
				$k = $k + 1;
				
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
					$period_start = 'ERROR: date format ['.__LINE__.'] ['.__FILE__.']';
				}
				
				if($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_stop'])){
					$period_stop = DateTime::createFromFormat('Y-m-d', $row['period_stop']) -> format('d.m.Y');
				}else{
					$period_stop = 'ERROR: date format ['.__LINE__.'] ['.__FILE__.']';
				}
				
				// сопоставляем идентификаторы типов выгрузки читаемым именам
				foreach ($arr_type_unloading as $id_type_unloading => $name_type_unloading){
					if ($row['type'] == $id_type_unloading){break;}
					else $name_type_unloading = "unknown";
				}
				echo '<tr class="hover">
					<td class="main">
						<a href="flk_fns_protokol_records.php?id='.$row['id'].'&buid='.$buid.'">Открыть</a>
					</td>
					<td class="main">'.$insert_date.'</td>
					<td class="main">'.$period_start.'</td>
					<td class="main">'.$period_stop.'</td>
					<td class="main">'.$name_type_unloading.'</td>
					<td class="main">
						<a href="flk_fns_protokol_stat_procent_ispravlen.php?id='.$row['id'].'&buid='.$buid.'">ход&nbsp;исправления</a>
					</td>
					<td class="main">
						<a href="flk_fns_protokol_stat_vid_oshibok.php?id='.$row['id'].'&buid='.$buid.'">виды&nbsp;ошибок</a>
					</td>
				</tr>';
			}
		}
		mysqli_free_result($result);
		mysqli_close($link);
		?>
		</tbody>
	</table>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
