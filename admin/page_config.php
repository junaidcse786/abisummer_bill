<?php 

$mRole = isset($_REQUEST["mRole"])?$_REQUEST["mRole"]:"";

$config_role_sql = "SELECT role_id, role_title FROM ".$db_suffix."role WHERE role_id != 8"; 

$config_role_query = mysqli_query($db,$config_role_sql);

$config_role_pages = array();

$config_role_menu = array();

$parent_editmember = '';

while($config_role_row = mysqli_fetch_object($config_role_query))

{

	$parent_editmember .= '"editmember"=>"memberlist-'.$config_role_row->role_id.'",';	

	$config_role_pages["memberlist-".$config_role_row->role_id] = "member_manager/member.php";

	$config_role_menu["memberlist-".$config_role_row->role_id] =  $config_role_row->role_title." List";

}

$config_role_pages["addmember"] = "member_manager/add_member.php";

$config_role_pages["editmember"] = "member_manager/edit_member.php";


$dashboard_pages = array(

		"8" => "dashboard_manager/dashboard_super_admin.php", 
		
		);


$pages = array(

		//common pages START		
		"myaccount"  => "setup_manager/my_account.php",
						
		"inbox"  => "message_manager/inbox.php",
		
		"drafts"  => "message_manager/drafts.php",
			
		"sent"  => "message_manager/sent.php",

		"viewmessage"  => "message_manager/view_message.php",

		"sendmessage"  => "message_manager/send_message.php",
		//common pages END
		
        "member"        => $config_role_pages,
    
    
    
        "locations"        => array
		                (

						"locations" 	=> "locations_manager/locations.php",
						
						"addlocations"  => "locations_manager/add_locations.php",
                            
                        "editlocations"  => "locations_manager/edit_locations.php",    
						
						),     
    
        "journey"        => array
		                (

						"journey" 	=> "journey_manager/journey.php",
						
						"addjourney"  => "journey_manager/add_journey.php",
                            
                        "editjourney"  => "journey_manager/edit_journey.php",    
						
						), 
    
        "hotels"        => array
		                (

						"hotels" 	=> "hotels_manager/hotels.php",
						
						"addhotels"  => "hotels_manager/add_hotels.php",
                            
                        "edithotels"  => "hotels_manager/edit_hotels.php",
                            
                        "meals_price" 	=> "meals_price_manager/meals_price.php",
						
						"addmeals_price"  => "meals_price_manager/add_meals_price.php",
                            
                        "editmeals_price"  => "meals_price_manager/edit_meals_price.php",  
                            
                        "rooms_price" 	=> "rooms_price_manager/rooms_price.php",
						
						"addrooms_price"  => "rooms_price_manager/add_rooms_price.php",
                            
                        "editrooms_price"  => "rooms_price_manager/edit_rooms_price.php", 
                            
                        "early_bird" 	=> "early_bird_manager/early_bird.php",
						
						"addearly_bird"  => "early_bird_manager/add_early_bird.php",
                            
                        "editearly_bird"  => "early_bird_manager/edit_early_bird.php",    
						
						),
    
        "rooms"        => array
		                (

						"rooms" 	=> "rooms_manager/rooms.php",
						
						"addrooms"  => "rooms_manager/add_rooms.php",
                            
                        "editrooms"  => "rooms_manager/edit_rooms.php",    
						
						),
    
        "meals"        => array
		                (

						"meals" 	=> "meals_manager/meals.php",
						
						"addmeals"  => "meals_manager/add_meals.php",
                            
                        "editmeals"  => "meals_manager/edit_meals.php",
                            
                        ),
    

		"setup"        => array

		                (

						"logo" 	=> "content_manager/logo.php",
						
						"addconfig"  => "setup_manager/addconfiguration.php",

                        "configuration"  => "setup_manager/configurationlist.php",

                        "updateconfig"  => "setup_manager/editconfig.php",

						"addmodule"  => "setup_manager/add_module.php",

                        "module"  => "setup_manager/module_list.php",

                        "updatemodule"  => "setup_manager/edit_module.php",

						"addrollmodule"  => "setup_manager/add_role_module.php",

                        "rollmodule"  => "setup_manager/role_module_list.php",

                        "updaterolemodule"  => "setup_manager/edit_role_module.php",
						
						),
		);

	

		

$menus = array(

		"member"        =>$config_role_menu,
    
        "locations" 	  => array    

                            (
                               "locations" => "Destinations Liste",  

                               "addlocations"  => "Destination einfügen",
                               
                            ),	

	    "journey" 	  => array    

                            (
                               "journey" => "Reisetyp Liste",  

                               "addjourney"  => "Reisetyp einfügen",
                               
                            ),
    
        "hotels" 	  => array    

                            (
                               "hotels" => "Hotels Liste",  

                               "addhotels"  => "Hotels einfügen",
                               
                            ),
        
        "rooms" 	  => array    

                            (
                               "rooms" => "Zimmertyp Liste",  

                               "addrooms"  => "Zimmertyp einfügen",
                               
                            ),
    
        "meals" 	  => array    

                            (
                               "meals" => "Mealtyp Liste",  

                               "addmeals"  => "Mealtyp einfügen",
                               
                            ),
    
    
        "setup" 	  => array    

						(
						   "logo" => "Logo Manager",  

						   "configuration"  => "Configuration",

						   "module"=>"Module" ,

						   "rollmodule" =>"User Roles",

						   "addrollmodule" =>"Add User Role"  

						)	

		);

?>