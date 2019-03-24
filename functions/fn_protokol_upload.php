<?php
// Функция загрузки протокола 
function flk_protokol_add($link, $arr_xls_heads)
{
    if (isset($_POST['number']) && is_numeric($_POST['number'])) {
        $number = $_POST['number'];
    } else {
        $number = 0;
    }

    if (isset($_POST['date']) && ($formated_date = DateTime::createFromFormat('Y-m-d', $_POST['date']))) {
        $date = $_POST['date'];
    } else {
        echo '<b>ERROR:</b> date [' . __FILE__ . ']<br><pre>';
        print_r($_POST);
        echo '</pre>';
        return false;
    }

    if (isset($_POST['period_start']) && ($formated_date = DateTime::createFromFormat('Y-m-d', $_POST['period_start']))) {
        $period_start = $_POST['period_start'];
    } else {
        echo '<b>ERROR:</b> period_start [' . __FILE__ . ']<br><pre>';
        print_r($_POST);
        echo '</pre>';
        return false;
    }

    if (isset($_POST['period_stop']) && ($formated_date = DateTime::createFromFormat('Y-m-d', $_POST['period_stop']))) {
        $period_stop = $_POST['period_stop'];
    } else {
        echo '<b>ERROR:</b> period_stop [' . __FILE__ . ']<br><pre>';
        print_r($_POST);
        echo '</pre>';
        return false;
    }

    if (isset($_POST['visible']) && is_numeric($_POST['visible'])) {
        $visible = $_POST['visible'];
    } else {
        $visible = 0;
    }

    if (isset($_POST['type_unloading']) //&& is_numeric($_POST['type_unloading'])
        ) {
        $type_unloading = $_POST['type_unloading'];
    } else {
        $type_unloading = 0;
    }

    if (isset($_POST['vid_object']) //&& is_numeric($_POST['vid_object'])
        ) {
        $vid_object = $_POST['vid_object'];
    } else {
        $vid_object = 0;
    }

    $tmp_protokol_uid = str_replace('-', '', $date . $period_start . $period_stop);
    $rnd_protokol_uid = rand(10000, 99999);
    $protokol_uid = $tmp_protokol_uid . $type_unloading . '-' . $rnd_protokol_uid;

    $file_xls = $_FILES['filexls']['name'];
    $tmp_file_xls = $_FILES['filexls']['tmp_name'];
    $type_file_xls = $_FILES['filexls']['type'];

    $file_xml = $_FILES['filexml']['name'];
    $type_file_xml = $_FILES['filexml']['type'];

    if (file_exists($tmp_file_xls) and isset($file_xml) and $type_file_xls == 'application/vnd.ms-excel' and $type_file_xml == 'text/xml') {
        // Смотрим, есть ли xls или xml файл с таким же именем в базе, если нет, то можно загружать,
        // если есть, то выдаем ошибку
        $query_check_filename_xls = "SELECT file_name_excel FROM protokol_file WHERE `file_name_excel` = '" . $file_xls . "' LIMIT 1";
        $result_check_filename_xls = mysqli_query($link, $query_check_filename_xls);

        $query_check_filename_xml = "SELECT file_name_xml FROM protokol_file WHERE `file_name_xml` = '" . $file_xml . "' LIMIT 1";
        $result_check_filename_xml = mysqli_query($link, $query_check_filename_xml);

        if (mysqli_num_rows($result_check_filename_xls) > 0) {
            echo '<b style="color: red">Имя файла протокола "' . $file_xls . '" уже присутствует в базе, загрузка невозможна</b>';
            return false;
        } elseif (mysqli_num_rows($result_check_filename_xml) > 0) {
            echo '<b style="color: red">Имя файла выгрузки "' . $file_xml . '" уже присутствует в базе, загрузка невозможна</b>';
            return false;
        } else {
            // Переходим к обработке протокола
            flk_protokol_parsing($link, $arr_xls_heads,$number, $date, $period_start, $period_stop, $visible, $type_unloading, $vid_object, $protokol_uid, $file_xls, $tmp_file_xls, $file_xml);
        }
    } else {
        echo '<b style="color: red;">ERROR: проверьте корректность загружаемых файлов</b>
			<pre>';
        print_r($_FILES);
        echo '</pre>';
        return false;
    }

}

// Функция обработки протокола
function flk_protokol_parsing($link, $arr_xls_heads,$number, $date, $period_start, $period_stop, $visible, $type_unloading, $vid_object, $protokol_uid, $file_xls, $tmp_file_xls, $file_xml)
{

    // Если все переменные определены, начинаем обработку
    if (isset($link, $arr_xls_heads, $number, $date, $period_start, $period_stop, $visible, $type_unloading, $vid_object, $protokol_uid, $file_xls, $tmp_file_xls, $file_xml)) {
        $arr_buid = explode("-", $protokol_uid);
        $buid = $arr_buid[0];

        /*
        --------------------------------------------------------------
        xls файл парсим при помощи библиотеки PHPExcel
        Пример использования PHPExcel взят с https://habr.com/post/178089/
        --------------------------------------------------------------
        */

        $PHPExcel_file = PHPExcel_IOFactory::load('' . $tmp_file_xls . '');

        // в протоколах только 1 лист, так что перебирать листы не требуется
        //$worksheet = $PHPExcel_file->setActiveSheetIndex(0);
        $worksheet = $PHPExcel_file->setActiveSheetIndex();

        // Количество столбцов на листе
        $columns_count = PHPExcel_Cell::columnIndexFromString($worksheet->getHighestColumn());

        // добавим проверку количества столбцов протокола
        if ($columns_count != 11) {
            echo '<b style="color: red;">ERROR: columns_count = ' . $columns_count . ' <br>
			Проверьте корректность загружаемого протокола.</b><br><br>
			<pre>';
            print_r($_FILES);
            echo '</pre>';
            return false;
        }

        // Проверяем заголовки, чтобы убедиться, что это файл протокола
        $columns_str = array();
        for ($column = 0; $column < $columns_count; $column++) {
            $columns_str[] = $worksheet->getCellByColumnAndRow($column, 1)->getValue();
            if ($columns_str[$column] != $arr_xls_heads[$column]) {
                echo '<b style="color: red;">ERROR: Не совпадают заголовки! 
				<br>' . $columns_str[$column] . ' != ' . $arr_xls_heads[$column] . '<br>
				Проверьте корректность загружаемого протокола.</b><br><br>';
                return false;
            }
        }

        //Проверяем наличие записи о протоколе
        //Если запись есть, то выбираем его id
        //Если записи нет, то вставляем новую запись, берем её id
        $query_ptotokol = "select id from protokol_export pe
                        where pe.Year=YEAR(CURDATE())
                        and pe.date='$date'
                        and pe.period_start='$period_start'
                        and pe.period_stop='$period_stop'
                        and pe.type='$type_unloading'
                        ";
        if ($result = mysqli_query($link, $query_ptotokol)) {
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                // Retrieve individual field value
                $protokol_id = $row["id"];
            } else {
                // Добавляем запись о протоколе в protokol_export
                $query_add_prot = "INSERT INTO protokol_export 
                                  ( number, Year ,`date`,`period_start`,`period_stop`,`visible`,`type`) 
		                          VALUES ($number ,YEAR(CURDATE()), '$date' ,  '$period_start' , '$period_stop',  $visible , '$type_unloading' )";

                echo $query_add_prot;
                if (mysqli_query($link, $query_add_prot)) {
                    // Obtain last inserted id
                    $protokol_id = mysqli_insert_id($link);
                    echo "Records inserted successfully. Last inserted ID is: " . $protokol_id;
                } else {
                    //echo "ERROR: Could not able to execute $query_add_prot. " . mysqli_error($link);
                    die ("Ошибка в запросе: " . $query_add_prot . "<br>" . mysqli_error($link));
                }
            }
        }

        // Добавляем запись о файле исходящего протокола в protokol_file
        $query_add_file = "INSERT INTO protokol_file (
            `vid_object`, `file_name_excel`,`file_name_xml`,`protokol_id`) 
		VALUES ( 
		    '$vid_object' , '$file_xls' ,  '$file_xml' , '$protokol_id' )";


        if (mysqli_query($link, $query_add_file)) {
            // Obtain last inserted id
            $file_id = mysqli_insert_id($link);
            echo "Records inserted successfully. Last inserted ID is: " . $file_id;
        } else {
            //echo "ERROR: Could not able to execute $query_add_prot. " . mysqli_error($link);
            die ("Error in query: " . $query_add_file . "<br>" . mysqli_error($link));
        }

        // Продолжаем разбирать содержимое файла

        // Количество строк на листе
        $rows_count = $worksheet->getHighestRow();

        // Перебираем строки листа Excel, начиная со второй (заголовок не нужен).
        // Строки начинаются с 1, а не с 0
        for ($row = 2; $row <= $rows_count; $row++) {
            // Если в файле строка была очищена, то она все равно выглядит не пустой и обрабатывается
            // Так что, если ячейки, содержащие № и КН, пустые, пропускаем строку.
            $cell_check_0 = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
            $cell_check_1 = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            if (empty($cell_check_0) && empty($cell_check_1)) {
                continue;
            }

            // Массив со значениями всех столбцов в строке листа Excel
            $value_str = array();

            // Перебираем столбцы листа Excel
            for ($column = 0; $column < $columns_count; $column++) {
                // Строка со значением объединенных ячеек листа Excel
                $merged_value = "";

                // Ячейка листа Excel
                $cell = $worksheet->getCellByColumnAndRow($column, $row);

                // Перебираем массив объединенных ячеек листа Excel
                foreach ($worksheet->getMergeCells() as $mergedCells) {
                    // Если текущая ячейка - объединенная,
                    if ($cell->isInRange($mergedCells)) {
                        // то вычисляем значение первой объединенной ячейки, и используем её в качестве значения
                        // текущей ячейки
                        $merged_value = $worksheet->getCell(explode(':', $mergedCells[0]))->getCalculatedValue();
                        break;
                    }
                }

                // Проверяем, что ячейка не объединенная: если нет, то берем ее значение, иначе значение первой
                // объединенной ячейки
                $value_str[] = (strlen($merged_value) == 0 ? $cell->getCalculatedValue() : $merged_value);
            }

            // Получаем необходимые данные из массива
            $number_in_file=$value_str['0'];
            $cad_obj_num = $value_str['1'];
            $type_object = $value_str['2'];
            $status = $value_str['3'];
            $guid_doc = $value_str['4'];
            $vid_record_for_export = $value_str['5'];
            $error_text = preg_replace('/([#&\']+)/', '', $value_str['6']);
            $error_path_xml = preg_replace('/([#&\']+)/', '', $value_str['7']);
            $atribut_name = $value_str['8'];
            $atribut_value = $value_str['9'];
            $error_type = $value_str['10'];

            // проверяем на наличие дублей, если запись есть, пропускаем
            $query_check_doubles_xls = "SELECT
                        number_in_file, 
                        cad_obj_num, 
						type_object, 
						status, 
						guid_doc, 
						vid_record_for_export, 
						error_text, 
						atribut_value, 
						error_type,
						file_name_id 
						 
					FROM record_list
					WHERE
					number_in_file=$number_in_file 
					and `cad_obj_num` = '$cad_obj_num' 
					AND `type_object` =  '$type_object' 
					AND `status` =  '$status' 
					AND `guid_doc` =  '$guid_doc' 
					AND `vid_record_for_export` =  $vid_record_for_export 
					AND `error_text` =  '$error_text' 
					AND `atribut_value` =  '$atribut_value' 
					AND `error_type` =  '$error_type' 
					AND file_name_id =  $file_id 
					LIMIT 1";
            $result_check_doubles_xls = mysqli_query($link, $query_check_doubles_xls);
            if (mysqli_num_rows($result_check_doubles_xls) > 0) {
                continue;
            }

            // Заносим данные в record_list
            $query_parse_xls = "INSERT INTO record_list (
                    number_in_file,
                    `cad_obj_num`,
					`type_object`,
					`status`,
					`guid_doc`,
					`vid_record_for_export`,
					`error_text`,
					`error_path_xml`,
					`atribut_name`,
					`atribut_value`,
					`error_type`,
					`file_name_id`) 
 				VALUES ( 
 				    $number_in_file,
 				    '$cad_obj_num', 
					'$type_object',
					 '$status', 
					 '$guid_doc',
					 $vid_record_for_export, 
					 '$error_text',
					 '$error_path_xml', 
					 '$atribut_name',
					 '$atribut_value', 
					 '$error_type',
					 $file_id)";

            mysqli_query($link, $query_parse_xls) or die ("Error in query: " . $query_parse_xls . "<br>" . mysqli_error($link));
        }
    } else {
        echo '<b style="color: red;">ERROR: не все переменные определены!</b>';
        return false;
    }
}

// Функция обновления чекбокса
function flk_protokol_update($link)
{
    if (isset($_GET['visible']) && is_numeric($_GET['visible'])) {
        $visible = $_GET['visible'];
    } else {
        $visible = 0;
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        echo '<b>ERROR:</b> id [' . __FILE__ . ']<br><pre>';
        print_r($_GET);
        echo '</pre>';
        return false;
    }

    $query = "UPDATE protokol_export 
				SET `visible`=" . $visible . " 
				WHERE `id`=" . $id . " 
				LIMIT 1";

    mysqli_query($link, $query) or die ("Error in query: " . $query . "<br>" . mysqli_error($link));
}

// Функция удаления протокола 
function flk_protokol_delete($link)
{
    if (isset($_GET['protokol_uid'])) {
        // uid протокола
        $protokol_uid = $_GET['protokol_uid'];
        // Базовый uid протокола
        $arr_buid = explode("-", $protokol_uid);
        $buid = $arr_buid[0];

        // Если протокол имеет статус visible=1, надо, чтобы удалялись связаные протоколы в protokol_export
        // и записи связаных протоколов в record_list.
        // В противном случае надо удалить только записи этого протокола
        if (isset($_GET['visible']) && $_GET['visible'] == 1) {
            $query = "DELETE FROM protokol_export WHERE `id` LIKE '" . $buid . "-%'";
        } else {
            $query = "DELETE FROM protokol_export WHERE `id` = '" . $_GET['protokol_uid'] . "' LIMIT 1";
        }

        mysqli_query($link, $query) or die ("Error in query: " . $query . "<br>" . mysqli_error($link));
    }
}

// Функция очистки таблиц БД 
function flk_protokol_clear($link)
{
    $protokol_export_list = array('protokol_export', 'record_list', 'record_notes');
    foreach ($protokol_export_list as $value) {
        $query = "TRUNCATE TABLE " . $value . "";
        mysqli_query($link, $query) or die ("Error in query: " . $query . "<br>" . mysqli_error($link));
    }
}

?>
