<?php
session_start();
require_once("config.php");
require_once("check_cookies.php");
require_once("functions/fn_misc.php");
require_once("matching.php");

date_default_timezone_set("Europe/Moscow");
if (isset($_GET['search_kn']) and !empty($_GET['search_kn'])) {
    $search_kn = $_SESSION['fpr']['search_kn'] = $_GET['search_kn'];
} elseif (isset($_SESSION['fpr']['search_kn'])) {
    $search_kn = $_SESSION['fpr']['search_kn'];
} else {
    $search_kn = "";
}

$title = 'Результаты поиска КН "' . $search_kn . '" в протоколах ФЛК исходящей выгрузки';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="ico/favicon_flk.ico" type="image/x-icon"/>
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
    <h1>Форма поиска сведений о выгрузке ОН по кадастровому номеру</h1>
    <br> Вернуться к <a href="index.php">протоколам ФЛК</a>
    <br>
    <h3><?= $title; ?></h3>

    <form method="GET" name="form_search_kn" id="form_search_kn" action="?">
        <label>
            <input class="date" style="width: 100%;" name="search_kn" form="form_search_kn" placeholder="Поиск по КН"/>
        </label>
        <button class="button" form="form_search_kn" type="submit">Найти</button>
    </form>
    <table class="main">
        <thead class="main">
        <tr class="main">
            <th class="main">ФЛК 1 уровня</th>
            <th class="main">Статус испр.</th>
            <th class="main">Вид сведений</th>
            <th class="main">Протокол от</th>
            <th class="main">Начало периода</th>
            <th class="main">Конец периода</th>
            <th class="main">Тип выгрузки</th>
            <th class="main">Файл выгрузки ФЛК 1 уровня</th>
            <th class="main">Ошибка ФНС (ФЛК 2 уровня)</th>
            <th class="main">Статус испр. ош. ФНС</th>
        </tr>
        </thead>
        <?php

        // Поиск по КН
        if (!empty($search_kn)) {
            $where_search_kn = " rl.cad_obj_num = '" . $search_kn . "'";
        } else {
            $where_search_kn = " rl.cad_obj_num = 'null'";
        }

        $query = "SELECT 
		rl.cad_obj_num, 
		rl.status,
		-- rn.decision_type,
		case 
          WHEN  rn.decision_type=0 THEN 'Не обработано' 
          WHEN  rn.decision_type=1 THEN 'В работе' 
          WHEN  rn.decision_type=2 THEN 'Исправлена' 
          WHEN  rn.decision_type=3 THEN 'Невозможно исправить' 
          WHEN  rn.decision_type=4 THEN 'Не обнаружена' 
          ELSE  rn.decision_type
        END
         AS decision_type,
        doc.desc ,
		pe.id AS id,
		pe.date,
		pe.period_start,
		pe.type,
		pe.period_stop,
		pf.file_name_xml,
		rlf.error_text,
		-- rnf.decision_type as fns_decision_type,
		case 
          WHEN  rnf.decision_type=0 THEN 'Не обработано' 
          WHEN  rnf.decision_type=1 THEN 'В работе' 
          WHEN  rnf.decision_type=2 THEN 'Исправлена' 
          WHEN  rnf.decision_type=3 THEN 'Невозможно исправить' 
          WHEN  rnf.decision_type=4 THEN 'Не обнаружена' 
          ELSE  rnf.decision_type
        END
         AS fns_decision_type
	FROM record_list rl
	LEFT JOIN record_notes rn on rl.id = rn.record_list_id
	LEFT JOIN protokol_file pf on rl.file_name_id=pf.id
	LEFT JOIN protokol_export pe ON pf.protokol_id=pe.id
	LEFT JOIN record_list_fns rlf on rl.guid_doc=rlf.error_id
	LEFT JOIN record_notes_fns rnf on rlf.id = rnf.record_list_id
    LEFT JOIN vid_dok doc on rl.vid_record_for_export=doc.no
	WHERE  
		" . $where_search_kn . "
	GROUP BY rl.cad_obj_num, 
	rl.status, 
	pe.date, 
	pe.period_start, 
	pe.period_stop, 
	pe.type,
	pf.file_name_xml
	ORDER BY pe.date DESC
	";

        //print_r($query);

        if ($result = mysqli_query($link, $query)) {
            $k = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $k = $k + 1;

                $id = $row['id'];

                // Базовый uid протокола
                $arr_buid = explode("-", $row['id']);
                $buid = $arr_buid[0];

                // Преобразуем формат даты для удобного отображения
                if ($formated_date = DateTime::createFromFormat('Y-m-d', $row['date'])) {
                    $insert_date = DateTime::createFromFormat('Y-m-d', $row['date'])->format('d.m.Y');
                } else {
                    $insert_date = '';
                }

                if ($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_start'])) {
                    $period_start = DateTime::createFromFormat('Y-m-d', $row['period_start'])->format('d.m.Y');
                } else {
                    $period_start = 'ERROR';
                }

                if ($formated_date = DateTime::createFromFormat('Y-m-d', $row['period_stop'])) {
                    $period_stop = DateTime::createFromFormat('Y-m-d', $row['period_stop'])->format('d.m.Y');
                } else {
                    $period_stop = 'ERROR';
                }

                // сопоставляем идентификаторы типов выгрузки читаемым именам
                foreach ($arr_type_unloading as $id_type_unloading => $name_type_unloading) {
                    if ($row['type'] == $id_type_unloading) {
                        break;
                    } else $name_type_unloading = "unknown";
                }

                echo '<tr class="hover">';
                if ($row['status'] == 'Не прошел флк') {
                    $file_name_xml = '';
                    echo '<td class="main">
					<a href="flk_protokol_records.php?protokol_id=' . $row['id'] . '&buid=' . $buid . '">' . $row['status'] . '</a>
					</td>';
                } else {
                    $file_name_xml = $row['file_name_xml'];
                    echo '<td class="main">
					' . $row['status'] . '
					</td>';
                }
                echo '
                <td class="main">' . $row['decision_type'] . '</td>
                <td class="main">' . $row['desc'] . '</td>
				<td class="main">' . $insert_date . '</td>
				<td class="main">' . $period_start . '</td>
				<td class="main">' . $period_stop . '</td>
				<td class="main">' . $name_type_unloading . '</td>
				<td class="main">' . $file_name_xml . '</td>
				<td class="main">' . $row['error_text'] . '</td>
				<td class="main">' . $row['fns_decision_type'] . '</td>
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
