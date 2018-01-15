<?php
 
require_once('../../config/dbconnect.php');

if(!isset($_SESSION["admin_panel"]))	

	header('Location: '.SITE_URL_ADMIN.'login.php');

if(isset($_POST["hotels_ID"])){

	$hotels_ID = $_POST["hotels_ID"];

	$sql = "SELECT m.meals_title,mp.meals_ID FROM ".$db_suffix."meals_price mp LEFT JOIN ".$db_suffix."meals m ON m.meals_ID=mp.meals_ID WHERE mp.hotels_ID='$hotels_ID' GROUP BY m.meals_title ORDER BY m.meals_title ASC";
	
	$news_query = mysqli_query($db,$sql);
    
    echo '<option value=""></option>';
	
	while($row = mysqli_fetch_object($news_query))
		
		echo '<option value="'.$row->meals_ID.':::'.$row->meals_title.'">'.$row->meals_title.'</option>';
}

?>