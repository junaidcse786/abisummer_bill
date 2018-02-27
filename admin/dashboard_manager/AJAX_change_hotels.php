<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["locations_ID"]) && isset($_POST["offer_from"])){

	$locations_ID = $_POST["locations_ID"];

    $offer_from = $_POST["offer_from"];
    
	$sql = "SELECT distinct h.hotels_name, h.hotels_ID, h.hotels_star FROM ".$db_suffix."rooms_price rp LEFT JOIN ".$db_suffix."hotels h ON h.hotels_ID=rp.hotels_ID WHERE h.locations_ID='$locations_ID' AND h.hotels_offer_from='$offer_from' AND h.hotels_status=1 AND rp.rp_status=1 ORDER BY h.hotels_name";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query)){
	
		$stars="";
		
		for($i=0;$i<$row->hotels_star;$i++)
		
			$stars.="*";
		
		echo '<option value="'.$row->hotels_ID.':::'.$row->hotels_name.'">'.$row->hotels_name.' '.$stars.'</option>';
		
	}
}

?>