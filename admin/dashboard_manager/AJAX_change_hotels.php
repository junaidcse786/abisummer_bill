<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["locations_ID"])){

	$locations_ID = $_POST["locations_ID"];

	$sql = "SELECT hotels_name, hotels_ID, hotels_star FROM ".$db_suffix."hotels WHERE locations_ID='$locations_ID' ORDER BY hotels_name";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query)){
	
		$stars="";
		
		for($i=0;$i<$row->hotels_star;$i++)
		
			$stars.="*";
		
		echo '<option value="'.$row->hotels_ID.':::'.$row->hotels_name.'">'.$row->hotels_name.' '.$stars.'</option>';
		
	}
}

?>