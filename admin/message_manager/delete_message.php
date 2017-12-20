<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['id'])){

	$id = isset($_POST['id'])? $_POST['id']: '0,';

	$id.='0';
	
	foreach(explode(',', $id) as $value)
	{
		if($value=='0')
			
			continue;
		
		$sql = "select * from ".$db_suffix."message where message_id = $value";				
		$query = mysqli_query($db, $sql);	
		if(mysqli_num_rows($query) > 0)
		{
			$content     = mysqli_fetch_object($query);
			$message_sender       = $content->message_sender;
			$message_report       = $content->message_report;
			$message_receiver    = $content->message_receiver;
			$sender_delete    = $content->sender_delete;
			$receiver_delete    = $content->receiver_delete;
			
			if($message_sender==$_SESSION["user_id"]){
				
				if($receiver_delete==1 || $message_report=='1')
				
					mysqli_query($db,"DELETE FROM ".$db_suffix."message WHERE message_id =".$value);
				
				else

					mysqli_query($db,"UPDATE ".$db_suffix."message SET sender_delete='1' WHERE message_id =".$value);				
			}
			if($message_receiver==$_SESSION["user_id"]){
				
				if($sender_delete==1 || $message_report=='1')
				
					mysqli_query($db,"DELETE FROM ".$db_suffix."message WHERE message_id =".$value);
				
				else

					mysqli_query($db,"UPDATE ".$db_suffix."message SET receiver_delete='1' WHERE message_id =".$value);
			
			}
		}
	}
}

?>