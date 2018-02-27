<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["locations_ID"])){

	$locations_ID = $_POST["locations_ID"];

	$sql = "SELECT distinct hotels_offer_from FROM ".$db_suffix."hotels WHERE locations_ID='$locations_ID' AND hotels_status=1 ORDER BY hotels_name";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query))	
		
		echo '<option value="'.$row->hotels_offer_from.'">'.$row->hotels_offer_from.'</option>';
}

?>