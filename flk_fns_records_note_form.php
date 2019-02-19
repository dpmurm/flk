<?php
date_default_timezone_set("Europe/Moscow");
if (isset($_GET['record_id'])) {
	$record_id = $_GET['record_id']; // Если нет номера отдела берется 0
} else {
	$record_id = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/table.css">
	<link rel="stylesheet" href="css/input.css">
	<link rel="stylesheet" href="css/select.css">
	<title>Запись протокола ФЛК</title>
</head>

<body>

<h3><font color=red>Запись протокола ФЛК</font></h3>
<table cellspacing="0" cellpadding="0">
	<?php

	require_once("config.php");
	require_once("functions/fn_fns_records_note_form.php");

	if(isset($_GET['records_note_submit']) && $_GET['records_note_submit'] == "save")
	{flk_fns_records_note_form_insert($link);}
	elseif(isset($_GET['records_note_cancel']) && $_GET['records_note_cancel'] == "cancel")
	{flk_fns_records_note_form_cancel($link);}
	

	$query = "SELECT rlf.id AS id,
		substr(rl.cad_obj_num, 4, 2) AS rayon,
		rl.cad_obj_num,	
		rl.type_object,
		rlf.error_id, 
		rlf.error_text,
		rlf.error_value,
		ifnull(rnf.decision_type,0) AS reshenie,
		pef.file_urr_xml,
		pef.idfile_fns_xml
		from record_list_fns rlf
		left join record_notes_fns rnf on rlf.id=rnf.record_list_id   
		left join record_list rl on rlf.error_id=rl.guid_doc   
		left join protokol_export_fns pef on rlf.protokol_uid=pef.protokol_uid   
		WHERE
		rlf.id='".$record_id."'";

	if ($result = mysqli_query($link, $query)) {

		while ($row = mysqli_fetch_assoc($result)) {

			?>

				<tr><td width="30%">Кадастровый№</td><td><?php echo $row['cad_obj_num']; ?></td></tr>
				<tr><td>Вид объекта</td><td><?php echo $row['type_object']; ?></td></tr>
				<tr><td>Описание ошибки ФЛК</td><td><font color=red><?php echo $row['error_text']; ?></font></td></tr>
				<tr><td>Значение элемента</td><td><?php echo $row['error_value']; ?></td></tr>
				<tr><td>Файл выгрузки</td><td><?php echo $row['file_urr_xml']; ?></td></tr>
				<tr><td>Файл протокола ФНС</td><td><?php echo $row['idfile_fns_xml']; ?></td></tr>

			<?php
			$record_list_id=$row['id'];
		}
	}

	mysqli_free_result($result);
	?>
</table>

<h3><font color=green>Информация об исправлении ошибки ФЛК</font></h3>
<form name="records_note" action="?" method="get">
<table width="100%" border="1" cellspacing="0" cellpadding="0">
	<?php

	$query2 = "select 
	rnf.id, rnf.decision_type, rnf.reg_no, rnf.`text`
	from record_notes_fns rnf
	where 
	rnf.record_list_id=$record_list_id";
	//echo $query2;

	$result2 = mysqli_query($link, $query2);
	$row2 = mysqli_fetch_assoc($result2) ;
			?>
			<tr><td width="30%">Решение по исправлению</td>
				<td>
					<input type="radio" name="decision_type" value=0 <?php if (!isset($row2['decision_type']) or $row2['decision_type']==0)  echo 'checked'; ?> >Не обработана<br>
					<input type="radio" name="decision_type" value=1  <?php if (isset($row2['decision_type']) and $row2['decision_type']==1)  echo 'checked'; ?> >В работе<br>
					<input type="radio" name="decision_type" value=2  <?php if (isset($row2['decision_type']) and $row2['decision_type']==2)  echo 'checked'; ?> >Исправлена<br>
					<input type="radio" name="decision_type" value=3  <?php if (isset($row2['decision_type']) and $row2['decision_type']==3)  echo 'checked'; ?> >Невозможно исправить<br>
					<input type="radio" name="decision_type" value=4  <?php if (isset($row2['decision_type']) and $row2['decision_type']==4)  echo 'checked'; ?> >Не обнаружена<br>
				</td>
			</tr>
			<input type="hidden" name="record_notes_id" value="<?php //$row2[id]?>" />
			<tr><td width="30%">Номер заявки на техошибку в ЕГРН</td><td><textarea name="reg_no" type="text" rows="1" cols="100"><?php echo $row2['reg_no']; ?></textarea></td></tr>
			<tr><td width="30%">Комментарий по устранению ошибки - ФИО исполнителя</td><td><textarea name="text" type="text" rows="10" cols="100"><?= $row2['text']; ?></textarea></td></tr>
			<?php

	mysqli_free_result($result2);
	mysqli_close($link);
	?>
</table>
	<input type="hidden" name="record_list_id" value="<?=$record_list_id?>" />
	<input type="hidden" name="sel_rayon" value="<?=$sel_rayon?>" />
	<input type="hidden" name="sel_reshenie" value="<?=$sel_reshenie?>" />
	<br><button class="button" name="records_note_submit" type="submit" value="save">Сохранить</button>&#160;
	<button class="button" name="records_note_cancel" type="submit" value="cancel">Отмена</button>
</form>

</body>
</html>
