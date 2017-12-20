<?php 
require_once('../../config/dbconnect.php');

if($_SESSION["role_id"]==8 && isset($_SESSION["admin_panel"]) && isset($_POST['id'])){
	
	$id = isset($_POST['id'])?$_POST['id']: '0,';
	
	$id.='0';
	
	if(mysqli_query($db,"DELETE FROM ".$db_suffix."module_in_role WHERE role_id IN (".$id.")"))
	{
		mysqli_query($db,"DELETE FROM ".$db_suffix."role WHERE role_id IN (".$id.")");
		
		$users_in_role='';
		
		$sql = "SELECT user_id, user_photo FROM ".$db_suffix."user where role_id IN (".$id.")";
		$news_query = mysqli_query($db,$sql);
		while($row = mysqli_fetch_object($news_query)){
			
			$id_value=$row->user_id;
			
			$product_img1 = $row->user_photo;
			
			if(mysqli_query($db,"DELETE FROM ".$db_suffix."user WHERE user_id != 1 AND user_id =".$id_value))
			{
				mysqli_query($db,"DELETE FROM ".$db_suffix."history WHERE user_id =".$id_value);
			
				mysqli_query($db,"DELETE FROM ".$db_suffix."message WHERE message_sender='$id_value' OR message_receiver='$id_value'");

				mysqli_query($db,"DELETE FROM ".$db_suffix."reminder WHERE user_id =".$id_value);

				mysqli_query($db,"DELETE FROM ".$db_suffix."package_completion_date WHERE user_id =".$id_value);

				$dd = mysqli_query($db, "UPDATE ".$db_suffix."indiv_codes SET user_id='0' where user_id='$id_value'");

				mysqli_query($db, "UPDATE ".$db_suffix."question SET question_wrong_hits_user=REPLACE(question_wrong_hits_user, ' $id_value ', ''), question_hits_user=REPLACE(question_hits_user, ' $id_value ', '')");

				unlink($image_dir1.$product_img1);
			}	
	}	
		
		
		$msg = "Delete successfully";
	}
	else
		
		$msg = "Failed to delete";
	
	echo $msg;
}
?>