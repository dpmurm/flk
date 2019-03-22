<?php
// Отображение списка КН из протокола для корр.выгрузки (ФЛК)
function show_kn($link, $protokol_id, $where_sel)
{
	echo '<div class="knlist">';
	$query_for_korr = "select distinct rl.cad_obj_num
from record_list rl
left join record_notes rn on rl.id=rn.record_list_id   
left join protokol_file pf on rl.file_name_id=pf.id 
left join protokol_export pe  on pf.protokol_id=pe.id
where 
rl.status!='Прошел флк'
and pe.id='$protokol_id'
$where_sel
order by 1";

	//print_r($query_for_korr);
	
	if ($result_for_korr = mysqli_query($link, $query_for_korr)) 
	{
		while ($row_for_korr = mysqli_fetch_assoc($result_for_korr)) 
		{
			echo $row_for_korr['cad_obj_num'].',';
		}
	}

	mysqli_free_result($result_for_korr);
	mysqli_close($link);
	clear_url();
	echo '</div>
	</html>
	';
	die();
}

// Отображение списка КН из протокола для корр.выгрузки (ФЛК ФНС)
function show_kn_fns($link, $buid, $where_sel)
{
	echo '<div class="knlist">';
	$query_for_korr = "SELECT DISTINCT rl.cad_obj_num
		FROM record_list_fns rlf
		LEFT JOIN record_list rl 
			ON rl.protokol_uid LIKE '".$buid."-%' 
			AND rlf.protokol_uid LIKE '".$buid."-%' 
			AND rlf.error_id=rl.guid_doc   
		LEFT JOIN record_notes_fns rnf 
			ON rlf.protokol_uid LIKE '".$buid."-%' 
			AND rlf.id=rnf.record_list_id   
		WHERE 
		rlf.protokol_uid LIKE '".$buid."-%'
		".$where_sel."
		ORDER BY rl.cad_obj_num";

	//print_r($query_for_korr);
	
	if ($result_for_korr = mysqli_query($link, $query_for_korr)) 
	{
		while ($row_for_korr = mysqli_fetch_assoc($result_for_korr)) 
		{
			echo $row_for_korr['cad_obj_num'].',';
		}
	}
	
	mysqli_free_result($result_for_korr);
	mysqli_close($link);
	clear_url();
	echo '</div>
	</html>
	';
	die();
}
?>

