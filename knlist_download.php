<?php
require_once("kernel/config.php");
session_start();
require_once("functions/fn_knlist.php");
if (isset($_GET['show_kn']) && $_GET['show_kn'] == 1) 
{
	show_kn($link, $buid, $where_sel);
} 
?>

