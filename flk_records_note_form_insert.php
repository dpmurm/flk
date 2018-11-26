<?php
//echo 'Передаваемые данные:<br>';
//print_r ($_GET);

if (!empty($_GET)) {
    $error = array();

    if (empty($_GET['record_list_id'])) {
        $error[] = "Не номер записи в протоколе ФЛК";
    };


    if (empty($error)) {

        $decision_type = 0;
        $reg_no = '';
        $text = 0;

        include_once("ConnectFlkEGRN.php");

        if (!get_magic_quotes_gpc()) {
            $record_list_id = $_GET['record_list_id'];
            $decision_type=$_GET['decision_type'];
            $protokol_id=$_GET['protokol_id'];
            //$record_notes_id=$_GET['record_notes_id'];
            $reg_no = mysqli_escape_string($link, $_GET['reg_no']);
            $text = mysqli_escape_string($link, $_GET['text']);
        };

        $query = "";
        $date=date('Y-m-d');
        $query = "insert into record_notes
	        values (null,
			        $record_list_id,
			        $decision_type,
                    '$reg_no', 
					'$text',
					now(),
					null
					 )
			ON DUPLICATE KEY UPDATE record_list_id='$record_list_id', decision_type='$decision_type', reg_no='$reg_no', text='$text', update_date=now()
					 ";
        //echo $query;
        $result = mysqli_query($link, $query);

        if (!$result) {
            $error[] = "ошибка размещения сообщения в базе данных " . mysqli_error($link);
        }


        if ($result) {
            echo '<br>Результат: <br>
	                       Запись успешно внесена. <a href="flk_protokol_records.php?protokol_id=' . $protokol_id . '#'.$record_list_id.'"> Вернуться в протокол ФЛК</a><br>
						   ';
        } else {
            echo '<br>Ошибки:<br>';
            print_r($error);
            //echo date('Y-m-d');
        };
        mysqli_close($link);
    }

}

?>


