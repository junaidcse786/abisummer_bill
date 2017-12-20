<?php 

require_once('../config/dbconnect.php');	

require_once('page_config.php');


$mKey = isset($_REQUEST["mKey"])?$_REQUEST["mKey"]:"";

$pKey = isset($_REQUEST["pKey"])?$_REQUEST["pKey"]:"";


$role_id_for_modules = (isset($_SESSION["role_id"]))? $_SESSION["role_id"] : 0;

$query = "SELECT um.module_id, m.module_key FROM ".$db_suffix."module_in_role um

Left Join ".$db_suffix."module m on m.module_id=um.module_id

where um.role_id= $role_id_for_modules AND m.module_status='1'";				

$usermodules = mysqli_query($db, $query);

$modules_keys = array(); $modules_keys_for_sql="'0',";

while($row = mysqli_fetch_array($usermodules))

	array_push($modules_keys, $row['module_key']);
		
	
foreach($modules_keys as $value)

	$modules_keys_for_sql.="'$value',";
	
$modules_keys_for_sql=substr($modules_keys_for_sql,0,-1);	


require_once('authentication.php');
	
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
     <?php require_once('header.php'); ?>   
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->


<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content <?php if($_SESSION["role_id"]==15) echo 'page-sidebar-closed'; ?>">

		<!-- BEGIN HEADER --> 
		
        <?php require_once('top_nav_bar.php'); ?>
        
        <!-- END HEADER -->
        
        <div class="clearfix"></div>
        
        <!-- BEGIN CONTAINER --> 
          
        <div class="page-container">
        
                <!-- BEGIN SIDEBAR -->
                
                        
                    <?php require_once('sidebar_left.php'); ?>    
                        
                
                <!-- END SIDEBAR -->
                
                <!-- BEGIN PAGE -->
                
                <div class="page-content-wrapper">
				
						<div class="page-content">
                
                        <!-- BEGIN PAGE content-->
                        
                        <?php
	
							if($mKey)
	
							{

								if(!is_array($pages[$mKey]))
								
									require_once($pages[$mKey]);
									
								else if($pKey && isset($pages[$mKey][$pKey]))	
									
									require_once($pages[$mKey][$pKey]);
									
								else
									
									echo '<script>window.location="'.SITE_URL_ADMIN.'";</script>';		
							}
	
							else
	
							{
	
								if(isset($dashboard_pages[$_SESSION["role_id"]]))
								
									require_once($dashboard_pages[$_SESSION["role_id"]]);
									
								else
								
									require_once("welcome.php");	
	
							}
	
						?>
                        
                        