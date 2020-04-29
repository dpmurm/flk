<?php
// Функция загрузки протокола 
function flk_fns_protokol_add($link, $tmp_file_fns_xml){
	 //if(!isset($tmp_file_fns_xml)){$tmp_file_fns_xml = $_FILES['filefnsxml']['tmp_name'];}
	//$type_file_fns_xml = $_FILES['filefnsxml']['type'];

	//if (file_exists($tmp_file_fns_xml) and $type_file_fns_xml == 'text/xml')
	if (file_exists("$tmp_file_fns_xml") and mime_content_type("$tmp_file_fns_xml") === 'application/xml')
	{
		$arr_fns_xml = simplexml_load_file($tmp_file_fns_xml);
		
		// Получаем значение атрибута ИдФайл из протокола ФНС
		$arr_idfile_fns_xml = $arr_fns_xml->xpath("//Файл/@ИдФайл");
		$idfile_fns_xml = $arr_idfile_fns_xml['0'];

		// Получаем имя файла выгрузки из протокола ФНС
		$arr_file_urr_xml = $arr_fns_xml->xpath("//Файл/Документ/ОбщСвПрот/@ИмяОбрабФайла"); 
		$file_urr_xml = $arr_file_urr_xml['0'];

		if(empty($file_urr_xml))
		{
		echo '<b style="color: red">ERROR: Отсутствует атрибут "ИмяОбрабФайла", проверьте корректность загружаемого файла.</b><br><br>';
		return false;
		}

		// Смотрим, есть ли запись с таким ИдФайл в базе, если нет, то загружаем, если есть, то выдаем ошибку
		// Также смотрим, есть ли запись о файле выгрузки в базе, если да, то загружаем, если нет, то выдаем ошибку
		$query_check_idfile_fns_xml = "SELECT idfile_fns_xml 
							FROM protokol_file_fns 
							WHERE `idfile_fns_xml` = '".$idfile_fns_xml."' LIMIT 1";

		$result_check_idfile_fns_xml = mysqli_query($link, $query_check_idfile_fns_xml);
	
		$query_check_file_urr_xml = "SELECT file_name_xml 
							FROM protokol_file 
							WHERE `file_name_xml` = '".$file_urr_xml."' LIMIT 1";

		$result_check_file_urr_xml = mysqli_query($link, $query_check_file_urr_xml);
	
		if (mysqli_num_rows($result_check_idfile_fns_xml) > 0)
		{	
		echo '<b style="color: red">Запись о протоколе "'.$idfile_fns_xml.'" уже присутствует в базе, загрузка невозможна</b><br><br>';
		return false;
		}
		elseif (mysqli_num_rows($result_check_file_urr_xml) < 1)
		{	
		echo '<b style="color: red">Имя файла выгрузки "'.$file_urr_xml.'" не найдено в базе протоколов ФЛК, загрузка невозможна</b><br><br>';
		return false;
		}
		else
		{
			global $good_fns;
			$good_fns=0;
			//Получаем protokol_id xml файла выгрузки
			$query_protokol_uid = "SELECT file_name_xml, protokol_id
				FROM protokol_file  
				WHERE `file_name_xml` = '".$file_urr_xml."'";
			$result_protokol_id = mysqli_query($link, $query_protokol_uid);
			$arr_protokol_id = mysqli_fetch_assoc($result_protokol_id);
			$protokol_id = $arr_protokol_id['protokol_id'];

			$arr_buid = explode("-", $protokol_id);
			$buid = $arr_buid[0]; 
			
			// Заполняем таблицу protokol_file_fns
			$query_pe_fns = "INSERT INTO protokol_file_fns (`insert_date`, `idfile_fns_xml`, `file_urr_xml`, `protokol_id`) 
					VALUES (CURDATE(), '".$idfile_fns_xml."', '".$file_urr_xml."', '".$protokol_id."')";
	
			//mysqli_query($link, $query_pe_fns) or die ("Error in query: ".$query_pe_fns."<br>".mysqli_error($link));
            if (mysqli_query($link, $query_pe_fns)) {
                // Obtain last inserted id
                $file_fns_id = mysqli_insert_id($link);
                echo "Records inserted successfully. Last inserted ID is: " . $file_fns_id;
            } else {
                //echo "ERROR: Could not able to execute $query_add_prot. " . mysqli_error($link);
                die ("Error in query: " . $query_pe_fns . "<br>" . mysqli_error($link));
            }


			// Заполняем таблицу record_list_fns
			foreach ($arr_fns_xml->xpath("//Файл/Документ/СвПоОшибке") as $segment_err_info) 
			{
				$row_error_info = $segment_err_info->attributes();
				//$error_text = preg_replace('/([#&\[\]\';]+)/', '', $row_error_info["ТекстОш"]);
				//$error_value = preg_replace('/([#&\[\]\';]+)/', '', $row_error_info["ЗнЭлем"]);
                $error_text = htmlspecialchars($row_error_info["ТекстОш"], ENT_QUOTES);
                $error_value = htmlspecialchars($row_error_info["ЗнЭлем"], ENT_QUOTES);
                $error_poz = $row_error_info["ПолОшЭл"];
				$error_code=$row_error_info["КодОшибки"];
				$error_id = $segment_err_info->ИдОш;

				// Пропускаем записи, в "ТекстОш" которых содержится "attribute is invalid", т.к. оно дублирует 
				// такую же ошибку с нормальным описанием
				$check_error_text = strpos($error_text, 'attribute is invalid');
				if ($check_error_text !== false)
				{	
					continue;
				}

				// проверяем на наличие дублей, если запись есть, пропускаем
				$query_check_doubles_fns_xml = "SELECT error_id, error_text, error_value, protokol_file_fns_id 
							FROM record_list_fns
							WHERE `error_id` = '$error_id'
							AND `error_text` = '$error_text'
							AND `error_value` = '$error_value'
							AND `protokol_file_fns_id` = $file_fns_id
							LIMIT 1";
				$result_check_doubles_fns_xml = mysqli_query($link, $query_check_doubles_fns_xml);
				if (mysqli_num_rows($result_check_doubles_fns_xml) > 0)
				{	
					continue;
				}
				$query_rl_fns = "INSERT INTO record_list_fns (`error_id`, 
									`error_text`, 
									`error_value`, 
									`protokol_file_fns_id`,
									error_poz,
									error_code
									) 
								VALUES ('$error_id', 
									'$error_text', 
									'$error_value', 
									$file_fns_id,
									'$error_poz',
									'$error_code'
									)";

				mysqli_query($link, $query_rl_fns) or die ("Error in query: ".$query_rl_fns."<br>".mysqli_error($link));

			}
			//Сигнал на окончание загрузки и началу переноса файлов в папку storage
			$good_fns=1;
			return $good_fns;
		}
	}
	else
	{
		echo '<b style="color: red;">ERROR: проверьте корректность загружаемого файла</b><pre>';
		print_r($_FILES);
		echo '</pre>';
		return false;
	}

}

// Функция удаления протокола
function flk_fns_protokol_delete($link){
	if(isset($_GET['id']))
	{
		$query = "DELETE FROM protokol_file_fns WHERE `id` = '".$_GET['id']."' LIMIT 1";
		mysqli_query($link, $query) or die ("Error in query: ".$query."<br>".mysqli_error($link));
	}
}

// Функция очистки таблиц БД
function flk_fns_protokol_clear($link){
	$list_tables = array('protokol_export_fns', 'record_list_fns', 'record_notes_fns');
	foreach ($list_tables as $table)
	{
		$query = "TRUNCATE TABLE ".$table."";
		mysqli_query($link, $query) or die ("Error in query: ".$query."<br>".mysqli_error($link));
	}
}
?>
