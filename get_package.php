<?php 
require_once('config/dbconnect.php');

if(isset($_POST['bookings_code'])){

	extract($_POST);
	
	$sql = "select bookings_summary,bookings_ID from ".$db_suffix."bookings WHERE bookings_code = '$bookings_code' limit 1";				
    $query = mysqli_query($db, $sql);

    if(mysqli_num_rows($query) > 0)
    {
        $content     = mysqli_fetch_object($query);

        $bookings_summary= json_decode($content->bookings_summary, true);
        $indiv_cost_array = $bookings_summary["indiv_cost_array"];
        $rooms_cost_details = $bookings_summary["rooms_cost_details"];
        $meals_cost_details = $bookings_summary["meals_cost_details"];
        $bookings_ID=$content->bookings_ID;
    

        foreach($rooms_cost_details as $key => $rooms){

            $ordered_amount = $rooms["rooms_persons_to_fit"];

            $allocated_amount = mysqli_num_rows(mysqli_query($db, "SELECT travelers_ID from ".$db_suffix."travelers where bookings_ID = $bookings_ID AND travelers_package like '%$key%'"));

            $rooms_cost_details[$key]["amount_left"] = $ordered_amount - $allocated_amount;

        }

        foreach($meals_cost_details as $key => $meals){

            $ordered_amount = $meals["meals_ordered"];

            $allocated_amount = mysqli_num_rows(mysqli_query($db, "SELECT travelers_ID from ".$db_suffix."travelers where bookings_ID = $bookings_ID AND travelers_package like '%$key%'"));

            $meals_cost_details[$key]["amount_left"] = $ordered_amount - $allocated_amount;

        }

        $indiv_cost_array_temp = $indiv_cost_array;

        foreach($indiv_cost_array_temp as $key => $value){

            $exploded_array=explode(" + ", $key);
            
            if(count($exploded_array)>1){
            
                $room_title = $exploded_array[0];
                $meal_title = $exploded_array[1];
            }
            else{
                
                $room_title = $exploded_array[0];
                $meal_title = $room_title;                
            }

            if(array_key_exists($room_title, $rooms_cost_details) && $rooms_cost_details[$room_title]["amount_left"]<=0)

                unset($indiv_cost_array[$key]); 

            else if(array_key_exists($meal_title, $meals_cost_details) && $meals_cost_details[$meal_title]["amount_left"]<=0)

                unset($indiv_cost_array[$key]); 
        }

        foreach($indiv_cost_array as $key => $value )	

                echo '<option value="'.$key.'">'.$key.' ('.number_format($value, 2, ',', '.').'&euro;)</option>';
    }
	
}

?>
