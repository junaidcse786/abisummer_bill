<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['id'])){

	$msg='Delete operation unsuccessful';
	
	$id = isset($_POST['id'])? $_POST['id']: '0,';
	
	$id.='0';
	
	$product_img1=array();
	
	$image_dir1 = "../../data/user/";
	
	$sql = "select user_photo from ".$db_suffix."user where user_id IN (".$id.")";				
	$query = mysqli_query($db, $sql);
	while($row=mysqli_fetch_object($query))
		
		array_push($row->user_photo, $product_img1);
	
	if(mysqli_query($db,"DELETE FROM ".$db_suffix."user WHERE user_id IN (".$id.")")){
	
		foreach($product_img1 as $value)
	
			unlink($image_dir1.$value);
			
		mysqli_query($db,"DELETE FROM ".$db_suffix."history WHERE user_id IN (".$id.")");
		
		mysqli_query($db,"DELETE FROM ".$db_suffix."message WHERE message_sender IN (".$id.") OR message_receiver IN (".$id.")");
		
		mysqli_query($db,"DELETE FROM ".$db_suffix."reminder WHERE user_id IN (".$id.")");
		
		mysqli_query($db,"DELETE FROM ".$db_suffix."package_completion_date WHERE user_id IN (".$id.")");
		
		mysqli_query($db, "UPDATE ".$db_suffix."indiv_codes SET user_id='0' where user_id IN (".$id.")");

		foreach(explode(',', $id) as $id_value)	
		
			$dd = mysqli_query($db, "UPDATE ".$db_suffix."question SET question_wrong_hits_user=REPLACE(question_wrong_hits_user, ' $id_value ', ''), question_hits_user=REPLACE(question_hits_user, ' $id_value ', '')");
	}
}
?>