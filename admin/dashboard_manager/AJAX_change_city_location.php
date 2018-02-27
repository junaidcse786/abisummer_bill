<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["journey_ID"])){

	$journey_ID = $_POST["journey_ID"];

	$sql = "SELECT jl_ID, jl_city_location FROM ".$db_suffix."journey_location WHERE journey_ID='$journey_ID' AND jl_status=1 ORDER BY jl_city_location ASC";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query))
		
		echo '<option value="'.$row->jl_ID.'">'.$row->jl_city_location.'</option>';
}


?>