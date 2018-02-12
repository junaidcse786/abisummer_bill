<?php
 
require_once('../../config/dbconnect.php');

if(isset($_POST["hotels_ID"])){

	$hotels_ID = $_POST["hotels_ID"];
    
    $data=array();

	$sql = "SELECT MIN(rp.rp_price_date_from) AS start_with, MAX(rp.rp_price_date_to) AS end_at FROM ".$db_suffix."rooms_price rp LEFT JOIN ".$db_suffix."rooms r ON r.rooms_ID=rp.rooms_ID WHERE rp.hotels_ID='$hotels_ID' AND rp_status=1 AND rooms_status=1 AND rp.rp_price_date_to!='0000-00-00' AND rp.rp_price_date_from!='0000-00-00' AND rp.rp_price_date_from>=CURDATE()";
	
	$news_query = mysqli_query($db,$sql);
	
	while($row = mysqli_fetch_object($news_query)){
		
		if($row->start_with!=null){
        
            $data[] = $row->start_with ;
            $data[] = $row->end_at ;
        }
        else{
            
            $data[] = date('Y-m-d'); 
            $data[] = date('Y-m-d');
        }
    }
    
    echo json_encode($data);
}

?>