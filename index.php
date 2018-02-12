<?php 

require_once('config/dbconnect.php');
	
$total_cost=0; $rooms_cost=0; $meals_cost=0; $journey_cost=0; $other_cost=0;

$other_costs_list=array();

setlocale(LC_MONETARY, 'de_DE');

$colors_to_pick_array=array("blue-ebonyclay", "grey-gallery");

if(isset($_POST["Submit"])){
    
    extract($_POST);
    
    $exp_hotels_ID=explode(":::", $hotels_ID);
    $hotels_ID=$exp_hotels_ID[0];
    $hotels_name=$exp_hotels_ID[1];
    
    $exp_locations_ID=explode(":::", $locations_ID);
    $locations_ID=$exp_locations_ID[0];
    $locations_name=$exp_locations_ID[1];
    
    if(!empty($journey_ID)){
        
        $exp_journey_ID=explode(":::", $journey_ID);
        $journey_ID=$exp_journey_ID[0]; 
        $journey_title=$exp_journey_ID[1];
    }
    else
        
        $journey_title="";
    
    
    $start_date = new DateTime($date_from);
    
    $end_date = new DateTime($date_from);

    $end_date->modify('+'.($num_nights-1).' day');
    
    
    if(!empty($journey_ID)){
        
        $sql = "select journey_price from ".$db_suffix."journey where journey_ID = $journey_ID AND journey_status=1 limit 1";				
        $query = mysqli_query($db, $sql);
        if(mysqli_num_rows($query) > 0)
        {
            $content     = mysqli_fetch_object($query);
            $journey_price       = $content->journey_price; 
            
            if(!empty($num_traveler))
                
                $journey_cost=$journey_price*$num_traveler;
            
            else
                
                $journey_cost=$journey_price;
        }
    }
    
    $meals_cost_details=array();
    
    if(count($meals_ID)>0 && !empty($meals_ID[0])){
        
        foreach($meals_ID as $key => $meal_ID){           
            
            $cost_of_this_meal=0;
            
            $sql = "select meals_title from ".$db_suffix."meals where meals_ID = $meal_ID";				
            $query = mysqli_query($db, $sql);
            if(mysqli_num_rows($query) > 0)
            {
                $content     = mysqli_fetch_object($query);
                $meals_title=$content->meals_title;
            }
            
        
            $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meal_ID AND hotels_ID='$hotels_ID' AND mp_price_date_from='0000-00-00' AND mp_price_date_to='0000-00-00' AND mp_status=1";				
            $query = mysqli_query($db, $sql);
            if(mysqli_num_rows($query) > 0)
            {
                $content     = mysqli_fetch_object($query);
                $meals_regular_price = $content->mp_price;
            }        

            $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meal_ID AND hotels_ID='$hotels_ID' AND mp_price_date_from!='0000-00-00' AND mp_price_date_to!='0000-00-00' AND mp_status=1";				
            $query = mysqli_query($db, $sql);
            if(mysqli_num_rows($query) > 0)
            {            
                $start_date = new DateTime($date_from);
    
                $end_date = new DateTime($date_from);

                $end_date->modify('+'.($num_nights-1).' day');
                
                $temp_cost_this_meal=0;
                
                for($j = $start_date; $j <= $end_date; $j->modify('+1 day')){

                    $price_to_select=$meals_regular_price;

                    $trial_date = $j->format("Y-m-d");

                    $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meal_ID AND hotels_ID=$hotels_ID AND mp_price_date_from<='$trial_date' AND mp_price_date_to>='$trial_date' AND mp_status=1";

                    $query = mysqli_query($db, $sql);

                    if(mysqli_num_rows($query) > 1){

                        $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meal_ID AND hotels_ID=$hotels_ID AND mp_price_date_from='$trial_date' AND mp_price_date_to='$trial_date' AND mp_status=1 LIMIT 1";

                        $query = mysqli_query($db, $sql);

                        $content     = mysqli_fetch_object($query);

                        $price_to_select = $content->mp_price;
                    }
                    else if(mysqli_num_rows($query) == 1){

                        $content     = mysqli_fetch_object($query);

                        $price_to_select = $content->mp_price;
                    }                    
                
                    if(!empty($num_meals[$key]))

                        $meals_cost+=($price_to_select*$num_meals[$key]); 

                    else

                        $meals_cost+=$price_to_select;
                    
                    $temp_cost_this_meal+=$price_to_select;
                }
                
                if(!isset($meals_cost_details[$meals_title])){                     
					
					if(!empty($num_meals[$key]))
					
						$meals_cost_details[$meals_title]=array("costs_this_meal_the_whole_time"=>$temp_cost_this_meal*$num_meals[$key], "meals_ordered"=> $num_meals[$key]);

					else
					
						$meals_cost_details[$meals_title]=array("costs_this_meal_the_whole_time"=>$temp_cost_this_meal, "meals_ordered"=> 1);					
				}						
				else{
					
					if(!empty($num_meals[$key])){
					
						$meals_cost_details[$meals_title]["costs_this_meal_the_whole_time"]+=$temp_cost_this_meal*$num_meals[$key];
                        
                        $meals_cost_details[$meals_title]["meals_ordered"]+=$num_meals[$key];
					}
					else{
					
						$meals_cost_details[$meals_title]["costs_this_meal_the_whole_time"]+=$temp_cost_this_meal;
                        
                        $meals_cost_details[$meals_title]["meals_ordered"]++;
					}					
				}	
            }
            else{

                $cost_of_this_meal=$meals_regular_price;

                if(!empty($num_meals[$key]))

                    $cost_of_this_meal=$meals_regular_price*$num_meals[$key];

                $meals_cost += ($cost_of_this_meal * $num_nights);
                
                if(!isset($meals_cost_details[$meals_title])){                    
					
					if(!empty($num_meals[$key]))
					
						$meals_cost_details[$meals_title]=array("costs_this_meal_the_whole_time"=>$meals_regular_price * $num_nights * $num_meals[$key], "meals_ordered"=> $num_meals[$key]);
				
					else
					
						$meals_cost_details[$meals_title]=array("costs_this_meal_the_whole_time"=>$meals_regular_price * $num_nights, "meals_ordered"=> 1);					
				}	
					
				else{
					
					if(!empty($num_meals[$key])){
					
						$meals_cost_details[$meals_title]["costs_this_meal_the_whole_time"]+=$meals_regular_price * $num_nights*$num_meals[$key];
                        
                        $meals_cost_details[$meals_title]["meals_ordered"]+=$num_meals[$key];
					}
					else{
					
						$meals_cost_details[$meals_title]["costs_this_meal_the_whole_time"]+=$meals_regular_price * $num_nights;
                        
                        $meals_cost_details[$meals_title]["meals_ordered"]++;
					
					}
				}

					
            }
        }
    }	
    
    $rooms_cost_details=array();
    
    if(count($rooms_ID)>0){
        
        foreach($rooms_ID as $key => $room_ID){
            
            $cost_of_this_room=0;
            
            $sql = "select rooms_title, rooms_persons_to_fit from ".$db_suffix."rooms where rooms_ID = $room_ID";				
            $query = mysqli_query($db, $sql);
            if(mysqli_num_rows($query) > 0)
            {
                $content     = mysqli_fetch_object($query);
                $rooms_title=$content->rooms_title;
                $rooms_persons_to_fit=$content->rooms_persons_to_fit;
            }
            
        
            $sql = "select rp_price from ".$db_suffix."rooms_price where rooms_ID = $room_ID AND hotels_ID='$hotels_ID' AND rp_price_date_from='0000-00-00' AND rp_price_date_to='0000-00-00' AND rp_status=1";				
            $query = mysqli_query($db, $sql);
            if(mysqli_num_rows($query) > 0)
            {
                $content     = mysqli_fetch_object($query);
                $rooms_regular_price = $content->rp_price;
            }        

            $sql = "select rp_price from ".$db_suffix."rooms_price where rooms_ID = $room_ID AND hotels_ID='$hotels_ID' AND rp_price_date_from!='0000-00-00' AND rp_price_date_to!='0000-00-00' AND rp_status=1";				
            $query = mysqli_query($db, $sql);
            if(mysqli_num_rows($query) > 0)
            {            
                $start_date = new DateTime($date_from);
    
                $end_date = new DateTime($date_from);

                $end_date->modify('+'.($num_nights-1).' day');
                
                $temp_cost_this_room=0;
                
                for($j = $start_date; $j <= $end_date; $j->modify('+1 day')){

                    $price_to_select=$rooms_regular_price;

                    $trial_date = $j->format("Y-m-d");

                    $sql = "select rp_price from ".$db_suffix."rooms_price where rooms_ID = $room_ID AND hotels_ID=$hotels_ID AND rp_price_date_from<='$trial_date' AND rp_price_date_to>='$trial_date' AND rp_status=1";

                    $query = mysqli_query($db, $sql);

                    if(mysqli_num_rows($query) > 1){

                        $sql = "select rp_price from ".$db_suffix."rooms_price where rooms_ID = $room_ID AND hotels_ID=$hotels_ID AND rp_price_date_from='$trial_date' AND rp_price_date_to='$trial_date' AND rp_status=1 LIMIT 1";

                        $query = mysqli_query($db, $sql);

                        $content     = mysqli_fetch_object($query);

                        $price_to_select = $content->rp_price;
                    }
                    else if(mysqli_num_rows($query) == 1){

                        $content     = mysqli_fetch_object($query);

                        $price_to_select = $content->rp_price;
                    }                    
                
                    if(!empty($num_rooms[$key]))

                        $rooms_cost+=($price_to_select*$num_rooms[$key]); 

                    else

                        $rooms_cost+=$price_to_select;
                    
                    $temp_cost_this_room+=$price_to_select;
                }
                
                if(!isset($rooms_cost_details[$rooms_title])){                     
					
					if(!empty($num_rooms[$key]))
					
						$rooms_cost_details[$rooms_title]=array("costs_this_room_the_whole_time"=>$temp_cost_this_room*$num_rooms[$key], "rooms_persons_to_fit"=> $rooms_persons_to_fit*$num_rooms[$key], "rooms_ordered"=> $num_rooms[$key]);

					else
					
						$rooms_cost_details[$rooms_title]=array("costs_this_room_the_whole_time"=>$temp_cost_this_room, "rooms_persons_to_fit"=> $rooms_persons_to_fit, "rooms_ordered"=> 1);					
				}						
				else{
					
					if(!empty($num_rooms[$key])){
					
						$rooms_cost_details[$rooms_title]["costs_this_room_the_whole_time"]+=$temp_cost_this_room*$num_rooms[$key];
						
						$rooms_cost_details[$rooms_title]["rooms_persons_to_fit"]+=$rooms_persons_to_fit*$num_rooms[$key];
                        
                        $rooms_cost_details[$rooms_title]["rooms_ordered"]+=$num_rooms[$key];
					}
					else{
					
						$rooms_cost_details[$rooms_title]["costs_this_room_the_whole_time"]+=$temp_cost_this_room;
						
						$rooms_cost_details[$rooms_title]["rooms_persons_to_fit"]+=$rooms_persons_to_fit;	
                        
                        $rooms_cost_details[$rooms_title]["rooms_ordered"]++;
					}					
				}	
            }
            else{

                $cost_of_this_room=$rooms_regular_price;

                if(!empty($num_rooms[$key]))

                    $cost_of_this_room=$rooms_regular_price*$num_rooms[$key];

                $rooms_cost += ($cost_of_this_room * $num_nights);
                
                if(!isset($rooms_cost_details[$rooms_title])){                    
					
					if(!empty($num_rooms[$key]))
					
						$rooms_cost_details[$rooms_title]=array("costs_this_room_the_whole_time"=>$rooms_regular_price * $num_nights * $num_rooms[$key], "rooms_persons_to_fit"=> $rooms_persons_to_fit*$num_rooms[$key], "rooms_ordered"=> $num_rooms[$key]);
				
					else
					
						$rooms_cost_details[$rooms_title]=array("costs_this_room_the_whole_time"=>$rooms_regular_price * $num_nights, "rooms_persons_to_fit"=> $rooms_persons_to_fit, "rooms_ordered"=> 1);					
				}	
					
				else{
					
					if(!empty($num_rooms[$key])){
					
						$rooms_cost_details[$rooms_title]["costs_this_room_the_whole_time"]+=$rooms_regular_price * $num_nights*$num_rooms[$key];
						
						$rooms_cost_details[$rooms_title]["rooms_persons_to_fit"]+=$rooms_persons_to_fit*$num_rooms[$key];
                        
                        $rooms_cost_details[$rooms_title]["rooms_ordered"]+=$num_rooms[$key];
					}
					else{
					
						$rooms_cost_details[$rooms_title]["costs_this_room_the_whole_time"]+=$rooms_regular_price * $num_nights;
						
						$rooms_cost_details[$rooms_title]["rooms_persons_to_fit"]+=$rooms_persons_to_fit;
                        
                        $rooms_cost_details[$rooms_title]["rooms_ordered"]++;
					}
				}

					
            }
        }
    }
	
	/*********OTHER COSTS*************/
    
    
    $sql = "select lc_title, lc_costs from ".$db_suffix."locations_costs WHERE lc_status=1 AND locations_ID='$locations_ID' AND lc_status=1";			
    
    $query = mysqli_query($db, $sql);	
    
    while($row = mysqli_fetch_object($query)){
        
        if (strpos($row->lc_costs, "%") === false)
				
				$other_costs_list[$row->lc_title] = array ( "costs" => trim(explode("?", $row->lc_costs)[0]), "type" => "euro");
				
        else
			
				$other_costs_list[$row->lc_title] = array ( "costs" => trim(explode("%", $row->lc_costs)[0]), "type" => "percent");
    }	
    
    $total_costs = $meals_cost + $rooms_cost + $journey_cost;
    
    if($other_costs_list["Office Profit"]["type"]=="percent")
        
        $office_profit = ( $total_costs * $other_costs_list["Office Profit"]["costs"] ) / 100 ;
    
    else
        
        $office_profit = $other_costs_list["Office Profit"]["costs"]*$num_traveler;
    
    
    if($other_costs_list["MwSt"]["type"]=="percent")
        
        $MwSt = $office_profit * $other_costs_list["MwSt"]["costs"] / 100 ;
    
    else
        
        $MwSt = $other_costs_list["MwSt"]["costs"]*$num_traveler;
    
    $promoter_provision=0;    
    
    for($excel_loop=1;$excel_loop<=20;$excel_loop++){
    
    
        if($other_costs_list["Promoter Provision"]["type"]=="percent")

            $promoter_provision = ( ($promoter_provision + $total_costs + $office_profit + $MwSt) * $other_costs_list["Promoter Provision"]["costs"] ) /100 ;

        else

            $promoter_provision = $other_costs_list["Promoter Provision"]["costs"]*$num_traveler;


        if($other_costs_list["MwSt"]["type"]=="percent")

            $MwSt = ( ($promoter_provision + $office_profit) * $other_costs_list["MwSt"]["costs"] ) /100 ;

        else

            $MwSt = $other_costs_list["MwSt"]["costs"]*$num_traveler;
    }
    
    
    /*********OTHER COSTS*************/
    
    
    $actual_total_price = $total_costs + $office_profit + $promoter_provision + $MwSt;
    
    
    $start_date = new DateTime($date_from);
    
    $end_date = new DateTime($date_from);

    $end_date->modify('+'.$num_nights.' day');
}

?>

<!DOCTYPE html>

<html>

<head>
    <?php require_once('admin/header.php'); ?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">

    <!-- BEGIN HEADER -->

    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="http://abisummer.de">
                <img src="http://rechner.webloungeonline.com/images/rsz_1abisummer-logo-internet.png" alt="logo" class="logo-default">
                </a>
                <div class="menu-toggler sidebar-toggler hide">
                </div>
            </div>
        </div>
        <!-- END HEADER INNER -->
    </div>

    <!-- END HEADER -->

    <div class="clearfix"></div>

    <!-- BEGIN CONTAINER -->

    <div class="page-container">

        <!-- BEGIN SIDEBAR -->


        <div class="page-sidebar-wrapper">
            <div class="page-sidebar navbar-collapse collapse">
            </div>
        </div>


        <!-- END SIDEBAR -->

        <!-- BEGIN PAGE -->

        <div class="page-content-wrapper">

            <div class="page-content">

                <!-- BEGIN PAGE content-->


                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

                <style>
                    .dashboard-stat-special .dashboard-stat {
                        padding: 10px 0px 35px 0px;
                    }

                    .dashboard-stat {
                        padding: 5px 0px 15px 0px;
                    }

                    .desc {
                        padding-top: 8px;
                    }

                </style>

                <h3 class="page-title">
                    <?php
                        $alert_message=""; $alert_box_show="hide"; $alert_type="success";

                        echo 'Preischecker <small>Reisekosten checken</small>';

?>
                </h3>
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo SITE_URL; ?>">Startseite</a>
                        </li>
                        <li>
                            <i class="fa fa-angle-right"></i>
                            <a href="<?php echo SITE_URL.'signup.php'; ?>">Mit Buchungscode anmelden</a>
                        </li>
                    </ul>
                </div>
                <!-- END PAGE HEADER-->

                <?php if(!isset($_POST["Submit"])): ?>

                <div class="row">
                    <div class="col-md-8">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box grey-cascade">
                            <div class="portlet-title">
                                <div class="caption"><i class="fa fa-reorder"></i>Preis checken</div>
                            </div>
                            <div class="portlet-body form">

                                <div class="form-body">

                                    <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $alert_message; ?>
                                    </div>


                                    <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">


                                        <div class="form-group">
                                            <label for="locations_ID" class="control-label col-md-3">Destination</label>
                                            <div class="col-md-8">

                                                <select required class="form-control input-medium select2me" data-placeholder="Auswaehlen" tabindex="0" id="locations_ID" name="locations_ID">
                                                    <option value=""></option>
									
                                                    <?php 

                                                         $sql_parent_menu = "SELECT locations_id, locations_name FROM ".$db_suffix."locations where locations_status=1";	
                                                         $parent_query = mysqli_query($db, $sql_parent_menu);

                                                         while($parent_obj = mysqli_fetch_object($parent_query))

                                                            echo '<option value="'.$parent_obj->locations_id.':::'.$parent_obj->locations_name.'">'.$parent_obj->locations_name.'</option>';										

                                                    ?>
									
                                                </select>

                                                <span for="locations_ID" class="help-block"></span>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="journey_ID" class="control-label col-md-3">Art der Anreise</label>
                                            <div class="col-md-1">

                                                <select class="form-control input-medium select2me" data-placeholder="Auswaehlen" tabindex="0" id="journey_ID" name="journey_ID">
                                    <option value=""></option>
									</select> <br/>
                                                <!-- <br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="journey_num_person"/> -->
                                                <span for="journey_ID" class="help-block"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="hotels_ID" class="control-label col-md-3">Hotel</label>
                                            <div class="col-md-8">

                                                <select required class="form-control input-medium select2me" data-placeholder="Auswaehlen" tabindex="0" id="hotels_ID" name="hotels_ID">
                                    <option value=""></option>
									</select>
                                                <span for="hotels_ID" class="help-block"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="lc_costs_date_from">Reisedatum</label>
                                            <div class="col-md-3">
                                                <input required type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control date_from" name="date_from"> <br/>

                                                <input required type="number" step="1" min="1" placeholder="Wie viele N&auml;chte?" class="form-control input-medium" name="num_nights" /><br/>

                                                <input required type="number" step="1" min="1" placeholder="Wie viele Personen?" class="form-control input-medium" name="num_traveler" />

                                                <span for="lc_costs_date_from" class="help-block"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="rooms_ID" class="control-label col-md-3">Zimmertyp</label>
                                            <div class="col-md-1">

                                                <select class="form-control input-medium rooms_ID" name="rooms_ID[]"> 
                                    <option value=""></option>
									</select> <br/>
                                                <input type="number" step="1" min="1" placeholder="Wie viele Zimmer?" class="form-control input-medium" name="num_rooms[]" />
                                                <span for="rooms_ID" class="help-block"></span>
                                            </div>
                                            <div class="col-md-offset-2 col-md-1">
                                                <i style="cursor:pointer;" class="fa fa-plus clone-it"></i> &nbsp;&nbsp;&nbsp;
                                                <i style="cursor:pointer;" class="fa fa-minus hide remove-it"></i>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="meals_ID" class="control-label col-md-3">Art der Verpflegung</label>
                                            <div class="col-md-1">

                                                <select class="form-control input-medium meals_ID" name="meals_ID[]">
                                    <option value=""></option>
									</select> <br/>
                                                <input type="number" step="1" min="1" placeholder="Wie viele Person?" class="form-control input-medium" name="num_meals[]" />
                                                <span for="meals_ID" class="help-block"></span>
                                            </div>
                                            <div class="col-md-offset-2 col-md-1">
                                                <i style="cursor:pointer;" class="fa fa-plus clone-it"></i> &nbsp;&nbsp;&nbsp;
                                                <i style="cursor:pointer;" class="fa fa-minus hide remove-it"></i>
                                            </div>
                                        </div>


                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" name="Submit" class="btn green">Submit</button>
                                                <button type="reset" class="btn default">Abbrechen</button>
                                            </div>
                                        </div>

                                    </form>

                                </div>

                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                    <div class="col-md-4">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box grey-cascade">
                            <div class="portlet-title">
                                <div class="caption"><i class="fa fa-reorder"></i>Buchungsinfo checken</div>
                                <div class="actions">
                                    <a href="<?php echo SITE_URL.'signup.php'; ?>" class="btn green-haze">Mit Buchungscode anmelden</a>
                                </div>
                            </div>
                            <div class="portlet-body form">

                                <div class="form-body">

                                    <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $alert_message; ?>
                                    </div>


                                    <form action="<?php echo SITE_URL.'view_bookings.php';?>" class="form-horizontal" method="get" enctype="multipart/form-data">


                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="lc_costs_date_from">Buchungscode</label>
                                            <div class="col-md-3">

                                                <input required type="text" placeholder="XXXXX-XXXXX-XXXXX-XXXXX" class="form-control input-medium" name="bookings_code" />

                                                <span for="lc_costs_date_from" class="help-block"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="lc_costs_date_from">Nachname</label>
                                            <div class="col-md-3">

                                                <input required type="text" placeholder="" class="form-control input-medium" name="travelers_last_name" />

                                                <span for="lc_costs_date_from" class="help-block"></span>
                                            </div>
                                        </div>

                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" name="Submit_booking" class="btn green">Submit</button>
                                                <button type="reset" class="btn default">Abbrechen</button>
                                            </div>
                                        </div>

                                    </form>

                                </div>

                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>

                <?php else: ?>

                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box green-seagreen">
                            <div class="portlet-title">
                                <div class="caption"><i class="fa fa-reorder"></i>Info Box</div>
                            </div>
                            <div class="portlet-body">

                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat purple-plum">
                                            <div class="visual">
                                                <i class="fa fa-globe"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo $locations_name; ?>
                                                </div>
                                                <div class="desc">
                                                    Reisenderzahl:
                                                    <?php echo $num_traveler; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat blue">
                                            <div class="visual">
                                                <i class="fa fa-building"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo $date_from; ?>
                                                </div>
                                                <div class="desc">
                                                    Hotel:
                                                    <?php echo $hotels_name; ?> <br/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat green">
                                            <div class="visual">
                                                <i class="fa fa-bus"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo $num_nights; ?> N&auml;chte
                                                </div>
                                                <div class="desc">
                                                    Check-out:
                                                    <?php echo $end_date->format("Y-m-d"); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br/>

                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat red-thunderbird">
                                            <div class="visual">
                                                <i class="fa fa-plus"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($actual_total_price, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Reisekosten
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat green-seagreen">
                                            <div class="visual">
                                                <i class="fa fa-plane"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Anreisekosten
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat yellow-gold">
                                            <div class="visual">
                                                <i class="fa fa-cutlery"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($meals_cost, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Verpflegung
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat red-intense">
                                            <div class="visual">
                                                <i class="fa fa-bed"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($rooms_cost+$office_profit, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Zimmerkosten
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box yellow">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-euro"></i>Preis Details (<b>Ohne Early Bird Buchen</b>)
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th> # </th>
                                                        <th> Leistungsbereich </th>
                                                        <th style="text-align:right;"> Summe Gesamt </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                    
                                    $i=1;   
                                    foreach($rooms_cost_details as $key => $rooms):   
                                        
                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?> </td>
                                                        <td>
                                                            <?php echo $key.'<b>  x  '.$rooms["rooms_ordered"].'</b>'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($rooms["costs_this_room_the_whole_time"]+$office_profit/count($rooms_cost_details), 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php endforeach; 
                                        
                                    foreach($meals_cost_details as $key => $meals):   
                                        
                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?> </td>
                                                        <td>
                                                            <?php echo $key.'<b>  x  '.$meals["meals_ordered"].'</b>'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($meals["costs_this_meal_the_whole_time"], 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php endforeach; ?>

                                                    <tr>
                                                        <td> </td>
                                                        <td> Anreisekosten
                                                            <?php if(!empty($journey_title)) echo '(<b>'.$journey_title.'</b>)'; ?>
                                                            <td style="text-align:right;">
                                                                <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> MwSt + Service Charge </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($promoter_provision+$MwSt, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> <b>Gesamtpreis</b> </td>
                                                        <td style="text-align:right;"> <b><?php echo number_format($actual_total_price, 2, ',', '.'); ?>&euro;</b> </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="portlet box red-soft">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-euro"></i> Gesamtpreis f&uuml;r jede Reisendertyp
                                                </div>
                                                <div class="actions">
                                                    <?php 
                                    
                                        $indiv_journey_cost = $journey_cost / $num_traveler;

                                        $indiv_promoter_provision = $promoter_provision / $num_traveler;

                                        $indiv_office_profit = $office_profit / $num_traveler;

                                        $indiv_MwSt = $MwSt / $num_traveler;                                    

                                        $indiv_cost_array=array();

                                        $colors_picked_temp = $colors_to_pick_array[mt_rand(0, count($colors_to_pick_array) - 1)];

                                        foreach($rooms_cost_details as $key1 => $rooms){

                                            $indiv_cost_array[$key1] = ($rooms["costs_this_room_the_whole_time"] / $rooms["rooms_persons_to_fit"]) + $indiv_journey_cost + $indiv_promoter_provision + $indiv_MwSt + $indiv_office_profit;

                                            foreach($meals_cost_details as $key2 => $meals){

                                                unset($indiv_cost_array[$key1]);

                                                $indiv_cost_array[$key1." + ". $key2] = ($rooms["costs_this_room_the_whole_time"] / $rooms["rooms_persons_to_fit"] + $meals["costs_this_meal_the_whole_time"] / $meals["meals_ordered"]) + $indiv_journey_cost + $indiv_promoter_provision + $indiv_MwSt + $indiv_office_profit;
                                            }
                                        }
                                    
                                    ?>
                                                </div>
                                            </div>
                                            <div class="portlet-body">

                                                <div class="row">

                                                    <?php
                                        
                                        foreach($indiv_cost_array as $key => $indiv_total_price):
                                    
                                    ?>

                                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 dashboard-stat-special">
                                                            <div class="dashboard-stat <?php echo $colors_picked_temp; ?>">
                                                                <div class="visual">
                                                                    <i class="fa fa-euro"></i>
                                                                </div>
                                                                <div class="details">
                                                                    <div class="number">
                                                                        <?php echo number_format($indiv_total_price, 2, ',', '.');; ?>&euro;
                                                                    </div>
                                                                    <div class="desc">
                                                                        <b><?php echo $key; ?></b>
                                                                        <!--<br/> Gesamtpreis pro Reisender-->

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <?php 

             $sql_parent_menu = "SELECT eb_discount,eb_discount_date_from, eb_discount_date_to FROM ".$db_suffix."early_bird where hotels_ID='$hotels_ID' AND eb_status='1' AND eb_discount_date_from<=CURDATE() AND CURDATE()<=eb_discount_date_to AND eb_stay_from<='$date_from' AND '$date_from'<=eb_stay_to";	
             $parent_query = mysqli_query($db, $sql_parent_menu);
            $counter=1;
            while($row = mysqli_fetch_object($parent_query)):
                
                $discounted_rooms_cost = $rooms_cost - $rooms_cost*$row->eb_discount/100;
                                       
                $discounted_meals_cost = $meals_cost - $meals_cost*$row->eb_discount/100;

                $discounted_total_costs = $discounted_meals_cost + $discounted_rooms_cost + $journey_cost;

                if($other_costs_list["Office Profit"]["type"]=="percent")

                    $discounted_office_profit = ( $discounted_total_costs * $other_costs_list["Office Profit"]["costs"] ) /100 ;

                else

                    $discounted_office_profit = $other_costs_list["Office Profit"]["costs"]*$num_traveler;


                if($other_costs_list["MwSt"]["type"]=="percent")

                    $discounted_MwSt = $discounted_office_profit * $other_costs_list["MwSt"]["costs"] /100 ;

                else

                    $discounted_MwSt = $other_costs_list["MwSt"]["costs"]*$num_traveler;	

                $discounted_promoter_provision=0;

                for($excel_loop=1;$excel_loop<=20;$excel_loop++){

                    if($other_costs_list["Promoter Provision"]["type"]=="percent")

                        $discounted_promoter_provision = ($discounted_promoter_provision + $discounted_total_costs + $discounted_office_profit+ $discounted_MwSt) * $other_costs_list["Promoter Provision"]["costs"] / 100 ;

                    else

                        $discounted_promoter_provision = $other_costs_list["Promoter Provision"]["costs"]*$num_traveler;


                    if($other_costs_list["MwSt"]["type"]=="percent")

                        $discounted_MwSt = ($discounted_promoter_provision + $discounted_office_profit) * $other_costs_list["MwSt"]["costs"] / 100 ;

                    else

                        $discounted_MwSt = $other_costs_list["MwSt"]["costs"]*$num_traveler; 

                }

                $discounted_actual_total_price = $discounted_total_costs + $discounted_office_profit + $discounted_promoter_provision + $discounted_MwSt;

        ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box <?php echo $colors_to_pick_array[mt_rand(0, count($colors_to_pick_array) - 1)] ?>">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-euro"></i>Preis Details - Early Bird Buchen
                                </div>
                                <div class="actions">
                                    <a href="#" class="btn blue"><i class="fa fa-calender"></i> <?php echo $row->eb_discount_date_from.' bis '.$row->eb_discount_date_to;  ?></a>

                                    <a href="#" class="btn red"><i class="fa fa-percentage"></i> <?php echo $row->eb_discount.'%';  ?></a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th> # </th>
                                                        <th> Leistungsbereich </th>
                                                        <th style="text-align:right;"> Summe Gesamt </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    
                                                        $i=1;    
                                                        foreach($rooms_cost_details as $key => $rooms): 

                                                        $discounted_this_rooms_cost = $rooms["costs_this_room_the_whole_time"] - $rooms["costs_this_room_the_whole_time"]*$row->eb_discount/100;

                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?> </td>
                                                        <td>
                                                            <?php echo $key.'<b> x '.$rooms["rooms_ordered"].'</b>'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($discounted_this_rooms_cost+$discounted_office_profit/count($rooms_cost_details), 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php endforeach; 
                                        
                                    
                                                        foreach($meals_cost_details as $key => $meals): 

                                                        $discounted_this_meals_cost = $meals["costs_this_meal_the_whole_time"] - $meals["costs_this_meal_the_whole_time"]*$row->eb_discount/100;									

                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?> </td>
                                                        <td>
                                                            <?php echo $key.'<b> x '.$meals["meals_ordered"].'</b>'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($discounted_this_meals_cost, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php endforeach; ?>

                                                    <tr>
                                                        <td> </td>
                                                        <td> Anreisekosten
                                                            <?php if(!empty($journey_title)) echo '(<b>'.$journey_title.'</b>)'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> MwSt + Service Charge </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($discounted_MwSt + $discounted_promoter_provision, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> <b>Gesamtpreis (Ohne Rabatt)</b> </td>
                                                        <td style="text-align:right;"> <b><?php echo number_format($actual_total_price, 2, ',', '.'); ?>&euro;</b> </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> <b>Gesamtpreis</b> </td>
                                                        <td style="text-align:right;"> <b><?php echo number_format($discounted_actual_total_price, 2, ',', '.'); ?>&euro;</b> </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="portlet box red-soft">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-euro"></i> Gesamtpreis f&uuml;r jede Reisendertyp
                                                </div>
                                                <div class="actions">

                                                    <?php 
                                    
                                                        $indiv_discounted_promoter_provision = $discounted_promoter_provision / $num_traveler;

                                                        $indiv_discounted_office_profit = $discounted_office_profit / $num_traveler;

                                                        $indiv_discounted_MwSt = $discounted_MwSt / $num_traveler;

                                                        $indiv_cost_array=array();

                                                        $colors_picked_temp = $colors_to_pick_array[mt_rand(0, count($colors_to_pick_array) - 1)];

                                                        foreach($rooms_cost_details as $key1 => $rooms){

                                                            $indiv_cost_array[$key1] = ($rooms["costs_this_room_the_whole_time"] - $rooms["costs_this_room_the_whole_time"]*$row->eb_discount/100) / $rooms["rooms_persons_to_fit"] + $indiv_journey_cost + $indiv_discounted_promoter_provision + $indiv_discounted_MwSt + $indiv_discounted_office_profit;

                                                            foreach($meals_cost_details as $key2 => $meals){

                                                                unset($indiv_cost_array[$key1]);

                                                                $indiv_cost_array[$key1." + ". $key2] = ($rooms["costs_this_room_the_whole_time"] - $rooms["costs_this_room_the_whole_time"]*$row->eb_discount/100) / $rooms["rooms_persons_to_fit"] + ($meals["costs_this_meal_the_whole_time"] - $meals["costs_this_meal_the_whole_time"]*$row->eb_discount/100) / $meals["meals_ordered"] + $indiv_journey_cost + $indiv_discounted_promoter_provision + $indiv_discounted_MwSt + $indiv_discounted_office_profit;
                                                            }
                                                        } 

                                                    ?>

                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <?php

                                    foreach($indiv_cost_array as $key => $indiv_total_discounted_price):

                                    ?>
                                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 dashboard-stat-special">
                                                            <div class="dashboard-stat <?php echo $colors_picked_temp; ?>">
                                                                <div class="visual">
                                                                    <i class="fa fa-euro"></i>
                                                                </div>
                                                                <div class="details">
                                                                    <div class="number">
                                                                        <?php echo number_format($indiv_total_discounted_price, 2, ',', '.');; ?>&euro;
                                                                    </div>
                                                                    <div class="desc">
                                                                        <b><?php echo $key; ?></b>
                                                                        <!--<br/> Gesamtpreis pro Reisender-->

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php endforeach; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php

            $counter++;

            endwhile;

        ?>

                    <?php endif; ?>

            </div>
        </div>

        <!-- END PAGE CONTAINER -->

        <?php require_once('admin/footer.php'); ?>

        <?php require_once('admin/scripts.php'); ?>

        <!-- END JAVASCRIPTS -->
        <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>

        <script>
            $('#locations_ID').change(function() {

                var locations_ID = $('#locations_ID').val();

                //console.log(message);

                if (locations_ID != '') {

                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_journey.php',
                        dataType: "text",
                        data: {
                            locations_ID: locations_ID
                        },
                        success: function(data) {

                            $("#journey_ID").val(null).trigger("change");
                            $('#journey_ID option').remove();
                            $('#journey_ID').append('<option value=""></option>' + data);
                        },
                    });

                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_hotels.php',
                        dataType: "text",
                        data: {
                            locations_ID: locations_ID
                        },
                        success: function(data) {

                            $("#hotels_ID").val(null).trigger("change");
                            $('#hotels_ID option').remove();
                            $('#hotels_ID').append('<option value=""></option>' + data);
                        },
                    });

                    $(".rooms_ID").val(null).trigger("change");
                    $(".rooms_ID option").remove();
                    $(".meals_ID").val(null).trigger("change");
                    $(".meals_ID option").remove();
                } else {

                    $("#journey_ID").val(null).trigger("change");
                    $("#journey_ID option").remove();
                    $("#hotels_ID").val(null).trigger("change");
                    $("#hotels_ID option").remove();
                    $(".rooms_ID").val(null).trigger("change");
                    $(".rooms_ID option").remove();
                    $(".meals_ID").val(null).trigger("change");
                    $(".meals_ID option").remove();

                }
            });

            $('#hotels_ID').change(function() {

                var hotels_ID = $('#hotels_ID').val();

                if (hotels_ID != '') {

                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_rooms.php',
                        dataType: "text",
                        data: {
                            hotels_ID: hotels_ID
                        },
                        success: function(data) {

                            $(".rooms_ID").each(function() {
                                $(this).val(null).trigger("change");
                                $(this).append(data);
                            });
                        },
                    });

                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_meals.php',
                        dataType: "text",
                        data: {
                            hotels_ID: hotels_ID
                        },
                        success: function(data) {

                            $(".meals_ID").each(function() {
                                $(this).val(null).trigger("change");
                                $(this).append(data);
                            });
                        },
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_dates.php',
                        dataType: "json",
                        data: {
                            hotels_ID: hotels_ID
                        },
                        success: function(data) {

                            $(".date_from").attr({
                               "min" : data[0],        
                               "max" : data[1]          
                            });
                            $(".date_from").val(data[0]);
                        },
                    });

                } else {
                    $(".rooms_ID").val(null).trigger("change");
                    $(".rooms_ID option").remove();
                    $(".meals_ID").val(null).trigger("change");
                    $(".meals_ID option").remove();
                    $(".date_from").attr({
                       "min" : '<?php date('Y-m-d')?>',        
                       "max" : '<?php date('Y-m-d')?>'          
                    });
                    $(".date_from").val('<?php date('Y-m-d')?>');

                }
            });

            $('.clone-it').live('click', function() {

                var $cloned_div = $(this).parent().parent('.form-group');

                var $html_content = $cloned_div.clone();

                $cloned_div.before($html_content);

                $cloned_div.find('.remove-it').removeClass('hide');

            });

            $('.remove-it').live('click', function() {

                $(this).parent().parent('.form-group').remove();

            });

        </script>


</body>
<!-- END BODY -->

</html>
