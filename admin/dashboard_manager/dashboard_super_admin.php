<?php 





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
                                  <label for="locations_ID" class="control-label col-md-3">Destination</label>
                                  <div class="col-md-8">
                                  	
                                    <select class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="locations_ID" name="locations_ID">
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
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="journey_no"/>
                                     <span for="journey_ID" class="help-block"></span>                                     
                                  </div>
                              </div>
							  
							  <div class="form-group">
                                  <label for="hotels_ID" class="control-label col-md-3">Hotel</label>
                                  <div class="col-md-8">
                                  	
                                    <select class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="hotels_ID" name="hotels_ID">
                                    <option value=""></option>
									</select>
                                     <span for="hotels_ID" class="help-block"></span>                                     
                                  </div>
                              </div>
							  
							  <div class="form-group">
                                  <label for="rooms_ID" class="control-label col-md-3">Zimmertyp</label>
                                  <div class="col-md-1">
                                  	
                                    <select class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="rooms_ID" name="rooms_ID"> 
                                    <option value=""></option>
									</select> <br/><br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="rooms_no"/>
                                    <span for="rooms_ID" class="help-block"></span>                                     
                                  </div>
								  <div class="col-md-offset-1 col-md-1">
									<button type="button" class="form-control clone-it input-extra-small"/>+</button>
								  </div>
                              </div>
							  
							  <div class="form-group">
                                  <label for="meals_ID" class="control-label col-md-3">Mealtyp</label>
                                  <div class="col-md-1">
                                  	
                                    <select class="form-control input-medium select2me"  data-placeholder="Auswaehlen" tabindex="0" id="meals_ID" name="meals_ID">
                                    <option value=""></option>
									</select> <br/><br/>
									<input type="number" step="1" min="1" placeholder="Wie viel?" class="form-control" name="meals_no"/>
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
			
			$("#rooms_ID").val(null).trigger("change");
			$("#rooms_ID option").remove();
			$("#meals_ID").val(null).trigger("change");
			$("#meals_ID option").remove();
		}
		else{
			
			$("#journey_ID").val(null).trigger("change");
			$("#journey_ID option").remove();
			$("#hotels_ID").val(null).trigger("change");
			$("#hotels_ID option").remove();
			$("#rooms_ID").val(null).trigger("change");
			$("#rooms_ID option").remove();
			$("#meals_ID").val(null).trigger("change");
			$("#meals_ID option").remove();
		
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
					   
							$("#rooms_ID").val(null).trigger("change");
							$('#rooms_ID option').remove();							
							$('#rooms_ID').append('<option value=""></option>'+data); 					   
					   },
			}); 
			
			$.ajax({
					   type: "POST",
					   url:  '<?php echo SITE_URL_ADMIN?>dashboard_manager/AJAX_change_meals.php',
					   dataType: "text",
					   data: {hotels_ID: hotels_ID},
					   success: function(data){ 
					   
							$("#meals_ID").val(null).trigger("change");
							$('#meals_ID option').remove();													
							$('#meals_ID').append('<option value=""></option>'+data); 					   
					   },
			});
		}
		else{
			$("#rooms_ID").val(null).trigger("change");
			$("#rooms_ID option").remove();
			$("#meals_ID").val(null).trigger("change");
			$("#meals_ID option").remove();
		
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