<?php 
require_once('../../config/dbconnect.php');

if(isset($_SESSION["admin_panel"]) && isset($_POST['bookings_ID'])){

	extract($_POST);
	
	mysqli_query($db, "UPDATE ".$db_suffix."bookings SET bookings_notes='$bookings_notes' WHERE bookings_ID=$bookings_ID");
	
}

?>