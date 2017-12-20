<?php 
require_once('../config/dbconnect.php');

	//mysqli_query($db,"UPDATE ".$db_suffix."exercise SET exercise_hits='0' WHERE 1");
	
	mysqli_query($db,"UPDATE ".$db_suffix."question SET question_wrong_hits_user='', question_hits_user='' WHERE 1");
	
	//mysqli_query($db,"TRUNCATE TABLE ".$db_suffix."mandat_exe");
	
	//mysqli_query($db,"TRUNCATE TABLE ".$db_suffix."exam_date");
	
	mysqli_query($db,"TRUNCATE TABLE ".$db_suffix."history");

?>