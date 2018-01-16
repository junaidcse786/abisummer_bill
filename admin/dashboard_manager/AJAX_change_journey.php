<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["locations_ID"])){

	$locations_ID = $_POST["locations_ID"];

	$sql = "SELECT j.journey_title,j.journey_ID FROM ".$db_suffix."journey j WHERE j.locations_ID='$locations_ID' ORDER BY j.journey_title ASC";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query))
		
		echo '<option value="'.$row->journey_ID.':::'.$row->journey_title.'">'.$row->journey_title.'</option>';
}


?>