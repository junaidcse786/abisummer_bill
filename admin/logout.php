<?php 

require_once('../config/dbconnect.php');

	session_destroy();
	
	header('Location: '.SITE_URL_ADMIN);
?>