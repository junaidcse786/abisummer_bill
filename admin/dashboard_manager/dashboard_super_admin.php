<?php 

$total_cost=0; $rooms_cost=0; $meals_cost=0; $journey_cost=0; $other_cost=0;

$costs_array=array();

if(isset($_POST["Submit"])){
    
    extract($_POST);
    
    $exp_locations_ID=explode(":::", $locations_ID);
    $exp_meals_ID=explode(":::", $meals_ID);
    $exp_hotels_ID=explode(":::", $hotels_ID);
    
    
    $locations_ID=$exp_locations_ID[0];
    
    $locations_name=$exp_locations_ID[1];
    
    if(!empty($journey_ID)){
        
        $exp_journey_ID=explode(":::", $journey_ID);
        $journey_ID=$exp_journey_ID[0]; 
        $journey_title=$exp_journey_ID[1];
    }
    else{
        
        $journey_title="";
        $costs_array["journey"] = array("per_person" => 0, "total" => 0);        
    }
    
    
    $meals_ID=$exp_meals_ID[0];
    
    $meals_title=$exp_meals_ID[1];
    
    
    $hotels_ID=$exp_hotels_ID[0];
    
    $hotels_name=$exp_hotels_ID[1];
    
    
    /*print_r($_POST);
    
    echo "<br/>";*/
    
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
            
            $costs_array["journey"] = array("per_person" => $journey_price, "total" => $journey_cost);
        }
    }
    
    if(!empty($meals_ID)){        
        
        $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID='$hotels_ID' AND mp_price_date_from='0000-00-00' AND mp_price_date_to='0000-00-00' AND mp_status=1";				
        $query = mysqli_query($db, $sql);
        if(mysqli_num_rows($query) > 0)
        {
            $content     = mysqli_fetch_object($query);
            $meals_regular_price = $content->mp_price;
        }        
        
        $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID='$hotels_ID' AND mp_price_date_from!='0000-00-00' AND mp_price_date_to!='0000-00-00' AND mp_status=1";				
        $query = mysqli_query($db, $sql);
        if(mysqli_num_rows($query) > 0)
        {
            for($i = $start_date; $i <= $end_date; $i->modify('+1 day')){
                
                $price_to_select=$meals_regular_price;
                
                $trial_date = $i->format("Y-m-d");
                
                $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID=$hotels_ID AND mp_price_date_from<='$trial_date' AND mp_price_date_to>='$trial_date' AND mp_status=1";
                
                $query = mysqli_query($db, $sql);
                
                if(mysqli_num_rows($query) > 1){
                    
                    $sql = "select mp_price from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID=$hotels_ID AND mp_price_date_from='$trial_date' AND mp_price_date_to='$trial_date' AND mp_status=1 LIMIT 1";
                
                    $query = mysqli_query($db, $sql);
                    
                    $content     = mysqli_fetch_object($query);
                    
                    $price_to_select = $content->mp_price;
                }
                else if(mysqli_num_rows($query) == 1){
                    
                    $content     = mysqli_fetch_object($query);
                    
                    $price_to_select = $content->mp_price;
                }
                    
                if(!empty($num_traveler))

                    $meals_cost+=($price_to_select*$num_traveler); 
                
                else
                    
                    $meals_cost+=$price_to_select;                
            }
        }
        else{
            
            $meals_cost=$meals_regular_price;
        
            if(!empty($num_traveler))

                $meals_cost=$meals_regular_price*$num_traveler;

            $meals_cost *= $num_nights;
        }
        
        $costs_array["meals"] = array("per_person" => $meals_cost/$num_traveler, "total" => $meals_cost);
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
                
                if(!isset($rooms_cost_details[$rooms_title]))
               
                    $rooms_cost_details[$rooms_title]=array("costs_this_room_the_whole_time"=>$temp_cost_this_room, "rooms_persons_to_fit" => $rooms_persons_to_fit); 
            }
            else{

                $cost_of_this_room=$rooms_regular_price;

                if(!empty($num_rooms[$key]))

                    $cost_of_this_room=$rooms_regular_price*$num_rooms[$key];

                $rooms_cost += ($cost_of_this_room * $num_nights);
                
                if(!isset($rooms_cost_details[$rooms_title]))
               
                    $rooms_cost_details[$rooms_title]=array("costs_this_room_the_whole_time"=>$cost_of_this_room * $num_nights, "rooms_persons_to_fit" => $rooms_persons_to_fit);
            }
        }
        
       $costs_array["rooms"] = array("per_person" => $rooms_cost/$num_traveler, "total" => $rooms_cost); 
    }
	
	/*********OTHER COSTS*************/
    
    $other_costs_list=array();
	
    
    $sql = "select lc_title, lc_costs from ".$db_suffix."locations_costs WHERE lc_status=1 AND locations_ID='$locations_ID'";			
    
    $query = mysqli_query($db, $sql);	
    
    while($row = mysqli_fetch_object($query)){
        
        if (strpos($row->lc_costs, "€") === false){
			
			if (strpos($row->lc_costs, "%") === false)
				
				continue;
				
			else
			
				$other_costs_list[$row->lc_title] = array ( "costs" => trim(explode("%", $row->lc_costs)[0]), "type" => "percent");
							                     
		}		
		else
            
            $other_costs_list[$row->lc_title] = array ( "costs" => trim(explode("€", $row->lc_costs)[0]), "type" => "euro");
		 	
    }
    
    $total_costs = $meals_cost + $rooms_cost + $journey_cost;
    
    if($other_costs_list["Office Profit"]["type"]=="percent")
        
        $office_profit = ( $total_costs * $other_costs_list["Office Profit"]["costs"] ) /100 ;
    
    else
        
        $office_profit = $other_costs_list["Office Profit"]["costs"];
    
    
    if($other_costs_list["Promoter Provision"]["type"]=="percent")
        
        $promoter_provision = ( ($total_costs + $office_profit) * $other_costs_list["Promoter Provision"]["costs"] ) /100 ;
    
    else
        
        $promoter_provision = $other_costs_list["Promoter Provision"]["costs"];
    
    
    if($other_costs_list["MwSt"]["type"]=="percent")
        
        $MwSt = ( ($promoter_provision + $office_profit) * $other_costs_list["MwSt"]["costs"] ) /100 ;
    
    else
        
        $MwSt = $other_costs_list["MwSt"]["costs"];
    
    
    /*********OTHER COSTS*************/
    
    
    $actual_total_price = $total_costs + $office_profit + $promoter_provision + $MwSt;
    
    
    $start_date = new DateTime($date_from);
    
    $end_date = new DateTime($date_from);

    $end_date->modify('+'.$num_nights.' day');
    
   /* echo "<br/><br/>";
    
    print_r($costs_array);
    
    echo "<br/><br/>";
    
    print_r($rooms_cost_details);*/
}

?>

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

<!-- BEGIN PAGE header-->

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
                              		<label class="control-label col-md-3" for="lc_costs_date_from">Reisedatum</label>
                              		<div class="col-md-3">
                                                            <input required type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_from"> <br/>
                                            
                                        <input required type="number" step="1" min="1" placeholder="Wie viele Nächte?" class="form-control input-medium" name="num_nights"/><br/>
                                        
                                        <input required type="number" step="1" min="1" placeholder="Wie viele Personen?" class="form-control input-medium" name="num_traveler"/>
                                        
                                 		<span for="lc_costs_date_from" class="help-block"></span>
                              		</div>
                           	  </div>
                                   
                                <div class="form-group">
                                  <label for="locations_ID" class="control-label col-md-3">Destination</label>
                                  <div class="col-md-8">
                                  	
                                    <select required class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="locations_ID" name="locations_ID">
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
                                  <label for="journey_ID" class="control-label col-md-3">Reisetyp</label>
                                  <div class="col-md-1">
                                  	
                                    <select class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="journey_ID" name="journey_ID">
                                    <option value=""></option>
									</select> <br/><!-- <br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="journey_num_person"/> -->
                                     <span for="journey_ID" class="help-block"></span>                                     
                                  </div>
                              </div>
							  
							  <div class="form-group">
                                  <label for="hotels_ID" class="control-label col-md-3">Hotel</label>
                                  <div class="col-md-8">
                                  	
                                    <select required class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="hotels_ID" name="hotels_ID">
                                    <option value=""></option>
									</select>
                                     <span for="hotels_ID" class="help-block"></span>                                     
                                  </div>
                              </div>
							  
							  <div class="form-group">
                                  <label for="rooms_ID" class="control-label col-md-3">Zimmertyp</label>
                                  <div class="col-md-1">
                                  	
                                    <select class="form-control input-medium rooms_ID" name="rooms_ID[]"> 
                                    <option value=""></option>
									</select> <br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="num_rooms[]"/>
                                    <span for="rooms_ID" class="help-block"></span>                                     
                                  </div>
								  <div class="col-md-offset-1 col-md-1">
									<i class="fa fa-plus clone-it"/></i>
								  </div>
                              </div>
							  
							  <div class="form-group">
                                  <label for="meals_ID" class="control-label col-md-3">Mealtyp</label>
                                  <div class="col-md-1">
                                  	
                                    <select class="form-control input-medium meals_ID" name="meals_ID">
                                    <option value=""></option>
									</select> <!-- <br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="meals_num_person"/> -->
                                     <span for="meals_ID" class="help-block"></span>                                     
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
                                             Reisenderzahl: <?php echo $num_traveler; ?>                                              
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
                                             Hotel: <?php echo $hotels_name; ?>  <br/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="dashboard-stat green">
                                    <div class="visual">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            <?php echo $num_nights; ?> Nächte
                                        </div>
                                        <div class="desc">
                                             Check-out: <?php echo $end_date->format("Y-m-d"); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                      </div>
                      
                      <br/>
                      
                      <div class="row">                          
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="dashboard-stat red-pink">
                                    <div class="visual">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                             <?php echo $actual_total_price; ?>&euro;
                                        </div>
                                        <div class="desc">
                                             Gesamtepreis                                              
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="dashboard-stat red-thunderbird">
                                    <div class="visual">
                                        <i class="fa fa-road"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            <?php echo ceil($actual_total_price/$num_traveler); ?>&euro;
                                        </div>
                                        <div class="desc">
                                             Pro Reisender zu zahlen
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="dashboard-stat yellow-casablanca">
                                    <div class="visual">
                                        <i class="fa fa-briefcase"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            <?php echo $MwSt; ?>&euro;
                                        </div>
                                        <div class="desc">
                                             MwSt (inkl.)
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="dashboard-stat blue-ebonyclay">
                                    <div class="visual">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            <?php echo $promoter_provision; ?>&euro;
                                        </div>
                                        <div class="desc">
                                             Promoter Provision (inkl.)
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                      </div>
                      
                      
                      <br/>
                      
                      <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 col-md-offset-1">
                                <div class="dashboard-stat green-seagreen">
                                    <div class="visual">
                                        <i class="fa fa-plane"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                             <?php echo $costs_array["journey"]["total"]; ?>&euro;
                                        </div>
                                        <div class="desc">
                                             Reisekosten <?php if($journey_title) echo '('.$journey_title.')'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 col-md-offset-1">
                                <div class="dashboard-stat yellow-gold">
                                    <div class="visual">
                                        <i class="fa fa-cutlery"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                             <?php echo $costs_array["meals"]["total"]; ?>&euro;
                                        </div>
                                        <div class="desc">
                                             Mealkosten <?php if($meals_title) echo '('.$meals_title.')'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 col-md-offset-1">
                                <div class="dashboard-stat red-intense">
                                    <div class="visual">
                                        <i class="fa fa-bed"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                             <?php echo $costs_array["rooms"]["total"]; ?>&euro;
                                        </div>
                                        <div class="desc">
                                             Zimmerkosten
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
                                <i class="fa fa-cogs"></i>Preis Details (<b>Ohne Early Bird</b>) </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th> # </th>
                                            <th> Leistungsbereich </th>
                                            <th> Personen </th>
                                            <th> Summe Gesamt </th>
                                            <th> Summer pro Person </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    
                                    $i=1;   
                                    foreach($rooms_cost_details as $key => $rooms):   
                                        
                                    ?>  
                                        <tr>
                                            <td> <?php echo $i++; ?> </td>
                                            <td> <?php echo $key; ?> </td>
                                            <td> <?php echo $rooms["rooms_persons_to_fit"]; ?> </td>
                                            <td> <?php echo $rooms["costs_this_room_the_whole_time"]; ?>&euro; </td>
                                            <td> <?php echo ceil($rooms["costs_this_room_the_whole_time"]/$rooms["rooms_persons_to_fit"]); ?>&euro; </td>
                                        </tr>
                                        
                                    <?php endforeach; ?>  
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                 
             </div>
         </div>

        <?php 

             $sql_parent_menu = "SELECT eb_discount,eb_discount_date_from, eb_discount_date_to FROM ".$db_suffix."early_bird where hotels_ID='$hotels_ID' AND eb_status='1' AND eb_discount_date_to>=CURDATE()";	
             $parent_query = mysqli_query($db, $sql_parent_menu);

            while($row = mysqli_fetch_object($parent_query)):

        ?>

        
        <div class="row">
             <div class="col-md-12">
                 <div class="portlet box grey-cascade">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cogs"></i>Preis Details - Early Bird <b><?php echo $row->eb_discount_date_from.' bis '.$row->eb_discount_date_to. ' => '.$row->eb_discount.'%'  ?></b> </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th> # </th>
                                            <th> Leistungsbereich </th>
                                            <th> Personen </th>
                                            <th> Summe Gesamt </th>
                                            <th> Summer pro Person </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $i=1;    
                                    foreach($rooms_cost_details as $key => $rooms):   
                                        
                                    ?>  
                                        <tr>
                                            <td> <?php echo $i++; ?> </td>
                                            <td> <?php echo $key; ?> </td>
                                            <td> <?php echo $rooms["rooms_persons_to_fit"]; ?> </td>
                                            <td> <?php echo $rooms["costs_this_room_the_whole_time"] -$rooms["costs_this_room_the_whole_time"]*$row->eb_discount/100; ?>&euro; </td>
                                            <td> <?php echo ceil(($rooms["costs_this_room_the_whole_time"] - - $rooms["costs_this_room_the_whole_time"]*$row->eb_discount/100)/$rooms["rooms_persons_to_fit"]); ?>&euro; </td>
                                        </tr>
                                        
                                    <?php endforeach; ?>  
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                 
             </div>
         </div>



        <?php endwhile; ?>

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

		if(locations_ID!=''){

			$.ajax({
					   type: "POST",
					   url:  '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_journey.php',
					   dataType: "text",
					   data: {locations_ID: locations_ID},
					   success: function(data){ 
					   
							$("#journey_ID").val(null).trigger("change");
							$('#journey_ID option').remove(); 
							$('#journey_ID').append('<option value=""></option>'+data); 					   
					   },
			}); 
			
			$.ajax({
					   type: "POST",
					   url:  '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_hotels.php',
					   dataType: "text",
					   data: {locations_ID: locations_ID},
					   success: function(data){ 
					   
							$("#hotels_ID").val(null).trigger("change");
							$('#hotels_ID option').remove();													
							$('#hotels_ID').append('<option value=""></option>'+data); 					   
					   },
			});
			
			$(".rooms_ID").val(null).trigger("change");
			$(".rooms_ID option").remove();
			$(".meals_ID").val(null).trigger("change");
			$(".meals_ID option").remove();
		}
		else{
			
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

		if(hotels_ID!=''){

			$.ajax({
					   type: "POST",
					   url:  '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_rooms.php',
					   dataType: "text",
					   data: {hotels_ID: hotels_ID},
					   success: function(data){ 
					   
							$( ".rooms_ID" ).each(function() {
							    $(this).val(null).trigger("change");
								$(this).append(data); 
							});				   
					   },
			}); 
			
			$.ajax({
					   type: "POST",
					   url:  '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_meals.php',
					   dataType: "text",
					   data: {hotels_ID: hotels_ID},
					   success: function(data){ 
					   
							$( ".meals_ID" ).each(function() {
							    $(this).val(null).trigger("change");
								$(this).append(data); 
							}); 					   
					   },
			});
		}
		else{
			$(".rooms_ID").val(null).trigger("change");
			$(".rooms_ID option").remove();
			$(".meals_ID").val(null).trigger("change");
			$(".meals_ID option").remove();
		
		}	
	});
	
	$('.clone-it').live('click',function() { 

		var $cloned_div = $(this).parent().parent('.form-group');
		
		var $html_content=$cloned_div.clone();
		
		$cloned_div.after($html_content);
	
	});
	
	
</script>

		
</body>
<!-- END BODY -->
</html>      