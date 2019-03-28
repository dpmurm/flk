<?php
function flk_fns_records_note_form_insert($link){
	if (!empty($_GET)) {
		$error = array();

		if (empty($_GET['record_list_id'])) {
			$error[] = "Не номер записи в протоколе ФЛК";
		};


		if (empty($error)) {

			$decision_type = 0;
			$reg_no = '';
			$text = 0;

			if (!get_magic_quotes_gpc()) {
				$record_list_id = $_GET['record_list_id'];
				$decision_type=$_GET['decision_type'];
				//$buid=$_GET['buid'];
				$reg_no = mysqli_escape_string($link, $_GET['reg_no']);
				$text = mysqli_escape_string($link, $_GET['text']);
			};

			$query = "";
			$date=date('Y-m-d');
			$query = "insert into record_notes_fns
				values (null, $record_list_id, $decision_type, $reg_no, $text, now(), null)
				ON DUPLICATE KEY UPDATE record_list_id=$record_list_id, decision_type=$decision_type, reg_no=$reg_no, text=$text, update_date=now()";

			$result = mysqli_query($link, $query);
	
			if (!$result) {
				$error[] = "ошибка размещения сообщения в базе данных " . mysqli_error($link);
			}
	
	
			if ($result) {
				mysqli_close($link);
				echo '<script language="JavaScript"> 
 						window.location.href = "flk_fns_protokol_records.php#'.$record_list_id.'"
					</script>';
			} else {
				echo '<br>Ошибки:<br>';
				print_r($error);
			};
			mysqli_close($link);
		}
	}
}

function flk_fns_records_note_form_cancel($link){
	$record_list_id = $_GET['record_list_id'];
	//$buid=$_GET['buid'];
	mysqli_close($link);
	echo '<script language="JavaScript"> 
		window.location.href = "flk_fns_protokol_records.php#'.$record_list_id.'"
		</script>';
}
?>



