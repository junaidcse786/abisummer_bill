<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["hotels_ID"])){

	$hotels_ID = $_POST["hotels_ID"];

	$sql = "SELECT r.rooms_title,rp.rooms_ID, r.rooms_persons_to_fit FROM ".$db_suffix."rooms_price rp LEFT JOIN ".$db_suffix."rooms r ON r.rooms_ID=rp.rooms_ID WHERE rp.hotels_ID='$hotels_ID' AND rp_status=1 AND rooms_status=1  GROUP BY r.rooms_title ORDER BY r.rooms_title ASC";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query))
		
		echo '<option data-persons="'.$row->rooms_persons_to_fit.'" value="'.$row->rooms_ID.'">'.$row->rooms_title.'</option>';
}

?>