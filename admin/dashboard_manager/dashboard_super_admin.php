<?php 

$total_cost=0; $rooms_cost=0; $meals_cost=0; $journey_cost=0; $other_costs=0;

if(isset($_POST["Submit"])){
    
    extract($_POST);
    
    print_r($_POST);
    
    echo "<br/>";
    
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
            
            if(!empty($journey_num_person))
                
                $journey_cost=$journey_price*$journey_num_person;
            
            else
                
                $journey_cost=$journey_price;
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
                    
                if(!empty($meals_num_person))

                    $meals_cost+=($price_to_select*$meals_num_person); 
                
                else
                    
                    $meals_cost+=$price_to_select;
            }            
        }
        else{
            
            $meals_cost=$meals_regular_price;
        
            if(!empty($meals_num_person))

                $meals_cost=$meals_regular_price*$meals_num_person;

            $meals_cost *= $num_nights;
        }
    }
    
    if(count($rooms_ID)>0){
        
        foreach($rooms_ID as $key => $room_ID){
            
            $cost_of_this_room=0;
        
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
                }            
            }
            else{

                $cost_of_this_room=$rooms_regular_price;

                if(!empty($num_rooms[$key]))

                    $cost_of_this_room=$rooms_regular_price*$num_rooms[$key];

                $rooms_cost += ($cost_of_this_room * $num_nights);
            }
        }
    }
    
    /*********OTHER COSTS*************/
    
    $costs_title=array();
    
    $sql = "select distinct lc_title from ".$db_suffix."locations_costs WHERE lc_status=1";				
    
    $query = mysqli_query($db, $sql);
    
    while($row = mysqli_fetch_object($query))
        
        $costs_title[] = $row->lc_title;
    
    foreach($costs_title as $lc_title){
        
        $sql = "select lc_costs from ".$db_suffix."locations_costs where locations_ID = $locations_ID AND lc_title='$lc_title' AND lc_costs_date_from='0000-00-00' AND lc_costs_date_to='0000-00-00' AND lc_status=1";				
        $query = mysqli_query($db, $sql);
        if(mysqli_num_rows($query) > 0)
        {
            $content     = mysqli_fetch_object($query);
            $costs_regular_price = $content->lc_costs;
        }        
        
        $sql = "select lc_costs from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID='$hotels_ID' AND lc_costs_date_from!='0000-00-00' AND lc_costs_date_to!='0000-00-00' AND lc_status=1";				
        $query = mysqli_query($db, $sql);
        if(mysqli_num_rows($query) > 0)
        {
                
                $price_to_select=$costs_regular_price;                
                
                $sql = "select lc_costs from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID=$hotels_ID AND lc_costs_date_from<=CURDATE() AND lc_costs_date_to>=CURDATE() AND lc_status=1";
                
                $query = mysqli_query($db, $sql);
                
                if(mysqli_num_rows($query) > 1){
                    
                    $sql = "select lc_costs from ".$db_suffix."meals_price where meals_ID = $meals_ID AND hotels_ID=$hotels_ID AND lc_costs_date_from=CURDATE() AND lc_costs_date_to=CURDATE() AND lc_status=1 LIMIT 1";
                
                    $query = mysqli_query($db, $sql);
                    
                    $content     = mysqli_fetch_object($query);
                    
                    $price_to_select = $content->lc_costs;
                }
                else if(mysqli_num_rows($query) == 1){
                    
                    $content     = mysqli_fetch_object($query);
                    
                    $price_to_select = $content->lc_costs;
                }
            
            
                if (strpos($price_to_select, "€") === false){
                    
                    if (strpos($mystring, "%") === false)
                        
                        continue;
                    
                    else
                        
                        $price_to_select = trim(explode("%", $price_to_select)[0]);                     
                }
                
                else 
                    
                    $price_to_select = trim(explode("€", $price_to_select)[0]);
                        
        }
        else{
            
            $meals_cost=$costs_regular_price;
        
            if(!empty($meals_num_person))

                $meals_cost=$meals_regular_price*$meals_num_person;

            $meals_cost *= $num_nights;
        }         
    }
    
    /*********OTHER COSTS*************/

}

echo "<br/>Reise Cost: ".$journey_cost;

echo "<br/>Meals Cost: ".$meals_cost;

echo "<br/>Rooms Cost: ".$rooms_cost;


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
                                            
                                        <input required type="number" step="1" min="1" placeholder="Wie viele Nächte?" class="form-control input-medium" name="num_nights"/>
                                        
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
										 
											echo '<option value="'.$parent_obj->locations_id.'">'.$parent_obj->locations_name.'</option>';										
									
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
									</select> <br/><br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="journey_num_person"/>
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
									</select> <br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="meals_num_person"/>
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