<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['id']) && $_SESSION["role_id"]==8){

	$msg='Status operation unsuccessful';
	
	$id = isset($_POST['id'])? $_POST['id']: '0,';
	$status = isset($_POST['status'])? $_POST['status']: 0;
	$table_name = isset($_POST['table_name'])? $_POST['table_name']: 'state';
	$column_name = isset($_POST['column_name'])? $_POST['column_name']: 'state_status';
	$column_id = isset($_POST['column_id'])? $_POST['column_id']: 'state_id';
	
	$id.='0';
	$sql="UPDATE ".$db_suffix.$table_name." SET ".$column_name."='$status' WHERE ".$column_id." IN (".$id.")";	
	
	if(mysqli_query($db,$sql))
			
		$msg = "Status changed successfully";
	
	echo $msg;
}
?>