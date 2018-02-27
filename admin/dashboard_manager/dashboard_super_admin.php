<?php 

function generateRandomString($length = 20) {
    $characters = '23456789ABCDEFGHJKMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 1; $i <= $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
        if($i%5==0 && $i != $length)
            $randomString .= "-";
    }
    return $randomString;
}

$total_cost=0; $rooms_cost=0; $meals_cost=0; $journey_cost=0; $other_cost=0;

setlocale(LC_MONETARY, 'de_DE');

$colors_to_pick_array=array("blue-ebonyclay", "grey-gallery");


if(isset($_POST["Submit"])){
    
    extract($_POST);
    
    $exp_hotels_ID=explode(":::", $hotels_ID);
    $hotels_ID=$exp_hotels_ID[0];
    $hotels_name=$exp_hotels_ID[1]." ($offer_from)";
    
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
        
        $sql = "select journey_price from ".$db_suffix."journey where journey_ID = $journey_ID AND journey_status=1 limit 1";			$query = mysqli_query($db, $sql);
        if(mysqli_num_rows($query) > 0)
        {
            $content     = mysqli_fetch_object($query);
            $journey_price       = $content->journey_price; 
            $abfahrsort="";
            if(!empty($city_location)){
                
                $sql = "select jl_price, jl_city_location from ".$db_suffix."journey_location where journey_ID = $journey_ID AND jl_ID='$city_location' limit 1";			
                $query = mysqli_query($db, $sql);
                if(mysqli_num_rows($query) > 0)
                {
                    $content     = mysqli_fetch_object($query);
                    $journey_price       = $content->jl_price;
                    $abfahrsort = $content->jl_city_location;
                }
                
            }
            /*if(!empty($num_traveler))
                
                $journey_cost=$journey_price*$num_traveler;
            
            else*/
                
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
    
    $other_costs_list=array();
	
    
    $sql = "select lc_title, lc_costs from ".$db_suffix."locations_costs WHERE lc_status=1 AND locations_ID='$locations_ID' AND lc_status=1";			
    
    $query = mysqli_query($db, $sql);	
    
    while($row = mysqli_fetch_object($query)){
			
        if (strpos($row->lc_costs, "%") === false)

            $other_costs_list[$row->lc_title] = array ( "costs" => trim(explode("€", $row->lc_costs)[0]), "type" => "euro");

        else

            $other_costs_list[$row->lc_title] = array ( "costs" => trim(explode("%", $row->lc_costs)[0]), "type" => "percent");
    }
    
    $total_costs = $meals_cost + $rooms_cost + $journey_cost;
    
    if($other_costs_list["Office Profit"]["type"]=="percent")
        
        $office_profit = ( $total_costs * $other_costs_list["Office Profit"]["costs"] ) /100 ;
    
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

if(isset($_POST["Submit_booking"])){
    
    extract($_POST);
    
    do
        
        $bookings_code=generateRandomString();        
        
    while(mysqli_num_rows(mysqli_query($db, "SELECT bookings_ID from ".$db_suffix."bookings where bookings_code='$bookings_code'"))>0); 
    
    $sql_parent_menu = "INSERT into ".$db_suffix."bookings SET hotels_name='$hotels_name', locations_name='$locations_name', bookings_check_in_date='$bookings_check_in_date', bookings_check_out_date='$bookings_check_out_date', bookings_summary='".$bookings_summary."', bookings_code='$bookings_code', bookings_num_traveler='$bookings_num_traveler'";    
    
    $parent_query = mysqli_query($db, $sql_parent_menu);
    
    echo '<script>alert("Buchung erfolgreich eingefügt"); window.location="'.SITE_URL_ADMIN.'?mKey=locations&pKey=bookings";</script>';
}

?>

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

    echo 'Dashboard <small>System Stats</small>';

?>
</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="<?php echo SITE_URL_ADMIN; ?>">Home</a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->

<?php if(!isset($_POST["Submit"])): ?>

<div class="row">
    <div class="col-md-12">
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
                            <label for="offer_from" class="control-label col-md-3">Angebot von</label>
                            <div class="col-md-1">
                                <select required class="form-control input-medium" id="offer_from" name="offer_from">
								</select>
                                <span for="offer_from" class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="journey_ID" class="control-label col-md-3">Art der Anreise</label>
                            <div class="col-md-1">

                                <select class="form-control input-medium select2me" data-placeholder="Auswaehlen" tabindex="0" id="journey_ID" name="journey_ID">
                                    <option value=""></option>
									</select>
                                <span for="journey_ID" class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group abfahrsort">
                            <label for="city_location" class="control-label col-md-3">Abfahrsort</label>
                            <div class="col-md-1">
                                <select class="form-control input-medium select2me" data-placeholder="Abfahrsort Auswaehlen" tabindex="0" id="city_location" name="city_location">
                                        <option value=""></option>
									</select>
                                <span for="city_location" class="help-block"></span>
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
                                <input required type="date" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control date_from" name="date_from"> <br/>

                                <input required type="number" step="1" min="1" placeholder="Wie viele N&auml;chte?" class="form-control input-medium" name="num_nights" /><br/>

                                <input required type="number" step="1" min="1" placeholder="Wie viele Personen?" class="form-control input-medium num_traveler" name="num_traveler" />

                                <span for="lc_costs_date_from" class="help-block lc_costs_date_from"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rooms_ID" class="control-label col-md-3">Zimmertyp</label>
                            <div class="col-md-1">

                                <select class="form-control input-medium rooms_ID" name="rooms_ID[]"> 
                                    <option value=""></option>
									</select> <br/>
                                <input type="number" step="1" min="1" placeholder="Wie viele Zimmer?" class="form-control input-medium num_rooms_array" name="num_rooms[]" />
                                <span for="rooms_ID" class="help-block"></span>
                            </div>
                            <div class="col-md-offset-1 col-md-1">
                                <i style="cursor:pointer;" class="fa fa-plus clone-it"></i> &nbsp;&nbsp;&nbsp;
                                <i style="cursor:pointer;" class="fa fa-minus hide remove-it"></i>
                            </div>
                            <span for="zimmertyp" class="help-block"></span>
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
                            <div class="col-md-offset-1 col-md-1">
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
                                    <?php echo $start_date->format("d.m.Y"); ?>
                                </div>
                                <div class="desc">
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
                                    <?php echo $end_date->format("d.m.Y"); ?>
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
                                    <?php echo number_format($rooms_cost, 2, ',', '.'); ?>&euro;
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
                                            <?php echo number_format($rooms["costs_this_room_the_whole_time"], 2, ',', '.'); ?>&euro; </td>
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

                                            <?php if(!empty($abfahrsort)): ?>
                                            <br/><br/> Abfahrsort (<b><?php echo $abfahrsort; ?></b>)
                                            <?php endif; ?>
                                            <td style="text-align:right;">
                                                <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> Office Profit </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($office_profit, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> Promoter Provision </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($promoter_provision, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> MwSt </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($MwSt, 2, ',', '.'); ?>&euro; </td>
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
                                    
                                        $json_array=array(
                                                            "journey_title"=>$journey_title,
                                            
                                                            "abfahrsort"=>$abfahrsort,
                                            
                                                            "journey_cost"=>$journey_cost,
                                            
                                                            "meals_cost"=>$meals_cost,
                                            
                                                            "rooms_cost"=>$rooms_cost,
                                            
                                                            "rooms_cost_details"=>$rooms_cost_details,
                                            
                                                            "meals_cost_details"=>$meals_cost_details,
                                                            
                                                            "indiv_cost_array"=>$indiv_cost_array,
                                            
                                                            "office_profit"=>$office_profit,
                                            
                                                            "promoter_provision"=>$promoter_provision,
                                            
                                                            "MwSt"=>$MwSt,                                            
                                        );
                                    
                                        array_walk_recursive($json_array, function (&$value) {
                                            $value = htmlentities($value);
                                        });
                                    
                                    ?>
                                    <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" method="post">

                                        <input type="hidden" name="locations_name" value="<?php echo $locations_name; ?>" />

                                        <input type="hidden" name="bookings_num_traveler" value="<?php echo $num_traveler; ?>" />

                                        <input type="hidden" name="hotels_name" value="<?php echo $hotels_name; ?>" />

                                        <input type="hidden" name="bookings_check_in_date" value="<?php echo $date_from; ?>" />

                                        <input type="hidden" name="bookings_check_out_date" value="<?php echo $end_date->format(" Y-m-d "); ?>" />

                                        <input type="hidden" name="bookings_summary" value='<?php echo json_encode($json_array, JSON_UNESCAPED_UNICODE); ?>' />

                                        <button name="Submit_booking" type="submit" class="btn green-haze"><i class="fa fa-calendar"></i> Buchen</button>

                                    </form>
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
                                            <?php echo number_format($discounted_this_rooms_cost, 2, ',', '.'); ?>&euro; </td>
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

                                    <?php endforeach;
                                        
                                        
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

                                    <tr>
                                        <td> </td>
                                        <td> Anreisekosten
                                            <?php if(!empty($journey_title)) echo '(<b>'.$journey_title.'</b>)'; ?>

                                            <?php if(!empty($abfahrsort)): ?>
                                            <br/><br/> Abfahrsort (<b><?php echo $abfahrsort; ?></b>)
                                            <?php endif; ?>
                                            <td style="text-align:right;">
                                                <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> Office Profit </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($discounted_office_profit, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> Promoter Provision </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($discounted_promoter_provision, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> MwSt </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($discounted_MwSt, 2, ',', '.'); ?>&euro; </td>
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
                                    
                                        $json_array=array(
                                                            "journey_cost"=>$journey_cost,
                                            
                                                            "journey_title"=>$journey_title,
                                            
                                                            "discount_applied"=>$row->eb_discount,
                                            
                                                            "without_discount"=>$actual_total_price,
                                            
                                                            "meals_cost"=>$discounted_meals_cost,
                                            
                                                            "rooms_cost"=>$discounted_rooms_cost,
                                            
                                                            "rooms_cost_details"=>$rooms_cost_details,
                                            
                                                            "meals_cost_details"=>$meals_cost_details,
                                                            
                                                            "indiv_cost_array"=>$indiv_cost_array,
                                            
                                                            "office_profit"=>$discounted_office_profit,
                                            
                                                            "promoter_provision"=>$discounted_promoter_provision,
                                            
                                                            "MwSt"=>$discounted_MwSt,                                            
                                        );
                                    
                                        array_walk_recursive($json_array, function (&$value) {
                                            $value = htmlentities($value);
                                        });
                                            
                                    
                                    ?>

                                    <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" method="post">

                                        <input type="hidden" name="locations_name" value="<?php echo $locations_name; ?>" />

                                        <input type="hidden" name="bookings_num_traveler" value="<?php echo $num_traveler; ?>" />

                                        <input type="hidden" name="hotels_name" value="<?php echo $hotels_name; ?>" />

                                        <input type="hidden" name="bookings_check_in_date" value="<?php echo $date_from; ?>" />

                                        <input type="hidden" name="bookings_check_out_date" value='<?php echo $end_date->format(" Y-m-d "); ?>' />

                                        <input type="hidden" name="bookings_summary" value='<?php echo json_encode($json_array, JSON_UNESCAPED_UNICODE); ?>' />

                                        <button name="Submit_booking" type="submit" class="btn green-haze"><i class="fa fa-calendar"></i> Buchen</button>

                                    </form>
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



    <?php require_once('scripts.php'); ?>

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
                    url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_offers.php',
                    dataType: "text",
                    data: {
                        locations_ID: locations_ID
                    },
                    success: function(data) {

                        $("#offer_from").val(null).trigger("change");
                        $('#offer_from option').remove();
                        $('#offer_from').append('<option value=""></option>' + data);
                    },
                });

                $(".rooms_ID").val(null).trigger("change");
                $(".rooms_ID option").remove();
                $(".meals_ID").val(null).trigger("change");
                $(".meals_ID option").remove();
            } else {

                $("#offer_from").val(null).trigger("change");
                $("#offer_from option").remove();
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

        $('#offer_from').change(function() {

            var locations_ID = $('#locations_ID').val();

            var offer_from = $('#offer_from').val();

            if (locations_ID != '' && offer_from != '') {

                $.ajax({
                    type: "POST",
                    url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_hotels.php',
                    dataType: "text",
                    data: {
                        locations_ID: locations_ID,
                        offer_from: offer_from
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
                            "min": data[0],
                            "max": data[1]
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
                    "min": '<?php echo date('Y- m-d') ?>',
                    "max": '<?php echo date('Y- m-d')?>'
                });
                $(".date_from").val('<?php echo  date('Y- m-d')?>');

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


        function pax_calculate() {

            var total_booked = $('.num_traveler').val();

            if (total_booked) {

                var total_selected = 0;

                $(".num_rooms_array").each(function() {

                    total_selected += $(this).parent().find('.rooms_ID').find(':selected').data('persons') * $(this).val();

                });

                var message_for_rooms = "";

                if (total_booked == total_selected)

                    message_for_rooms = '<font style="color:green">Sie haben alle Pax verteilt!</font>';

                if (total_booked > total_selected)

                    message_for_rooms = '<font style="color:red">Sie haben noch <b>' + (total_booked - total_selected) + ' Pax </b>zu verteilen!</font>';

                if (total_booked < total_selected)

                    message_for_rooms = '<font style="color:red">die Verteilung passt NICHT!</font>';

                $(".lc_costs_date_from").html(message_for_rooms);
            } else {

                $(".lc_costs_date_from").html('<font style="color:red">Personenzahl zuerst bitte eingeben!</font>');
            }
        }

        $('.num_rooms_array').live('change', pax_calculate);
        $('.rooms_ID').live('change', pax_calculate);

        $('#journey_ID').change(function() {

            var journey_ID = $(this).val();

            if (journey_ID != '') {

                $.ajax({
                    type: "POST",
                    url: '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_city_location.php',
                    dataType: "text",
                    data: {
                        journey_ID: journey_ID
                    },
                    success: function(data) {

                        if (data) {
                            $(".abfahrsort").show();
                            $("#city_location").attr("required", true);
                            $("#city_location").val(null).trigger("change");
                            $("#city_location option").remove();
                            $("#city_location").append('<option value=""></option>' + data);
                        } else {
                            $('#city_location').removeAttr('required');
                            $(".abfahrsort").hide();
                        }
                    },
                });
            } else {
                $(".abfahrsort").hide();
            }
        });

        $(".abfahrsort").hide();

    </script>


    </body>
    <!-- END BODY -->

    </html>
