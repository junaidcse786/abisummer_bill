<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['dm_key']) && (isset($_POST['message_receiver']) || isset($_POST['message_subject']) || isset($_POST['message_text']))){

	extract($_POST);
	
	$message_receiver_text='';
	
	foreach($message_receiver as $value)
	
		$message_receiver_text.=$value.',';
	
	$message_receiver_text=substr($message_receiver_text,0,-1);	
	
	echo $message_receiver_text;
	
	$sql = "SELECT dm_key FROM ".$db_suffix."draft_message where dm_key = '$dm_key'";
	$query_res = mysqli_query($db,$sql);
	if(mysqli_num_rows($query_res)>0)
	
		mysqli_query($db, "UPDATE ".$db_suffix."draft_message SET message_receiver='$message_receiver_text', message_subject='$message_subject', message_text='$message_text' WHERE dm_key='$dm_key'");
		
	
	else{
	
		mysqli_query($db, "INSERT INTO ".$db_suffix."draft_message SET message_receiver='$message_receiver_text', message_subject='$message_subject', message_text='$message_text', dm_key='$dm_key', user_id='".$_SESSION["user_id"]."'");
	}
	
}

?>