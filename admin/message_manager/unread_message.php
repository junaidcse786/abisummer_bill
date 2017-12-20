<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['id'])){

	$id = isset($_POST['id'])? $_POST['id']: '0,';
	
	$id.='0';
	
	$sql="UPDATE ".$db_suffix."message SET message_seen='0' WHERE message_id IN (".$id.")";
		
	if(mysqli_query($db,$sql))
	
		$msg = "Message read successfully";
		
	else
	
		$msg = "Failed to read message";
	
	
	echo $msg;
}
?>