<?php 

require_once('../../config/dbconnect.php');

if($_SESSION["role_id"]==8 && isset($_SESSION["admin_panel"]) && isset($_POST['id'])){

	$table_name = $db_suffix.'config';
	
	$id = isset($_POST['id'])?$_POST['id']: '0,';
	
	$id.='0';	
	
	if(mysqli_query($db,"DELETE FROM ".$table_name." WHERE config_id IN (".$id.")"))
	
		$msg = "Delete successfully";
	
	else
	
		$msg = "Failed to delete";	
	
	
	echo $msg;
}

?>