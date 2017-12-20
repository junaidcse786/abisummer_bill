<?php 

require_once('../../config/dbconnect.php');

if($_SESSION["role_id"]==8 && isset($_SESSION["admin_panel"]) && isset($_POST['id'])){

	$id = isset($_POST['id'])?$_POST['id']: '0,';
	
	$id.='0';	
	
	if(mysqli_query($db,"DELETE FROM ".$db_suffix."module_in_role WHERE module_id IN (".$id.")"))
	
	{
		mysqli_query($db,"DELETE FROM ".$db_suffix."module WHERE module_id IN (".$id.")");
		$msg = "Delete successfully";
	
	}else{
	
		$msg = "Failed to delete";
	
	}
	
	echo $msg;
}

?>