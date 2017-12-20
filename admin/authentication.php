<?php 
	
	if(!isset($_SESSION["admin_panel"])){
		
		$_SESSION["accessing_url"]=$_SERVER['REQUEST_URI'];
		
		header('Location: '.SITE_URL_ADMIN.'login.php');
	}
		
	else {
		
		
		array_push($modules_keys, "drafts", "inbox", "myaccount", "sent", "viewmessage", "sendmessage"); // common modules for all users
		
		if($_SESSION["role_id"]=='8')
			
			array_push($modules_keys, "setup", "member");	// push this module only for super admin
		
			
		if($mKey!='' && !in_array($mKey, $modules_keys))
	
			header('Location: '.SITE_URL_ADMIN);	
	}	
		
	
?>