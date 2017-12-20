<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['id'])){

	$id = isset($_POST['id'])? $_POST['id']: '0,';

	$id.='0';
	
	mysqli_query($db,"DELETE FROM ".$db_suffix."draft_message WHERE dm_id IN (".$id.")");
	
}

?>