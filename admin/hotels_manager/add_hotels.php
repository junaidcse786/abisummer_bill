<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

$hotels_name = "";
$hotels_status = 1;
$hotels_star = "";
$hotels_notes = "";
$locations_ID = "";

$err=0;

$messages = array(
					'locations_ID' => array('status' => '', 'msg' => ''),						  				 
					'hotels_name' => array('status' => '', 'msg' => ''),
                    'hotels_star' => array('status' => '', 'msg' => ''),
                    'hotels_status' => array('status' => '', 'msg' => ''),
                    'hotels_notes' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	
	if(empty($hotels_name))
	{
		$messages["hotels_name"]["status"]=$err_easy;
		$messages["hotels_name"]["msg"]="Name ist Pflichtfeld";
		$err++;		
	}	
    
    if(empty($locations_ID))
	{
		$messages["locations_ID"]["status"]=$err_easy;
		$messages["locations_ID"]["msg"]="Destination ist Pflichtfeld";
		$err++;		
	} 
    
    if(empty($hotels_star))
	{
		$messages["hotels_star"]["status"]=$err_easy;
		$messages["hotels_star"]["msg"]="Sterne ist Pflichtfeld";
		$err++;		
	}
    
	
	if($err == 0)
	{
		$sql = "INSERT INTO ".$db_suffix."hotels SET locations_ID='$locations_ID',hotels_name='$hotels_name',hotels_star='$hotels_star',hotels_status='$hotels_status',hotels_notes='$hotels_notes'";
        
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Daten erfolgreich gespeichert";		
			$alert_box_show="show";
			$alert_type="success";
			
			$hotels_name = "";
			
		}else{
			$alert_box_show="show";
			$alert_type="danger";
			$alert_message="Database encountered some error while inserting";
		}
	}
	else
	{
		$alert_box_show="show";
		$alert_type="danger";
		$alert_message="Bitte korrigiere diese Felder";
		
	}
}

if(!isset($_POST["Submit"]) && isset($_GET["s_factor"]))
{
	$alert_message="Daten erfolgreich gespeichert";		
	$alert_box_show="show";
	$alert_type="success";
}


?>

<!-----PAGE LEVEL CSS BEGIN--->

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

<!-----PAGE LEVEL CSS END--->



                                        <!-- BEGIN PAGE hotels_name & BREADCRUMB-->
                                        <h3 class="page-hotels_name">
                                                <?php echo $menus["$mKey"]["$pKey"]; ?>
                                        </h3>
                                        <div class="page-bar">         
                                        <ul class="page-breadcrumb">
                                                <li>
                                                        <i class="fa fa-home"></i>
                                                        <a href="<?php echo SITE_URL_ADMIN; ?>">Home</a>
                                                        <i class="fa fa-angle-right"></i>
                                                </li>
                                                <li>
                                                        <i class="<?php echo $active_module_icon; ?>"></i>
                                                        <a href="#"><?php echo $active_module_name; ?></a>
                                                        <i class="fa fa-angle-right"></i>
                                                </li>
                                                <li>
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey='.$pKey; ?>"><?php echo $menus["$mKey"]["$pKey"]; ?></a>
                                                </li>
                                        </ul>
                                        <!-- END PAGE hotels_name & BREADCRUMB-->
                                </div>
                        <!-- END PAGE HEADER-->
                        
                        
   <!--------------------------BEGIN PAGE CONTENT------------------------->
                                              
                        <div class="row">
            <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
               <div class="portlet box grey-cascade">
                  <div class="portlet-title">
                     <div class="caption"><i class="fa fa-reorder"></i>Felder mit Sterchen müssen ausgefüllt werden <strong>*</strong></div>
                  </div>
                  <div class="portlet-body form">
                  
                      <div class="form-body">
                      
                          <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <?php echo $alert_message; ?>
                          </div>
                          
                               
                               <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                                   
                               <div class="form-group">
                                 <label for="parent" class="control-label col-md-3">Destination <span class="required">*</span></label>
                                 <div class="col-md-4">
                                    <select class="form-control select2me"  data-placeholder="Auswählen" tabindex="0" name="locations_ID">                                                                              
                                       <?php
									   $sql_parent_menu = "SELECT locations_ID, locations_name FROM ".$db_suffix."locations where locations_status='1'";	
										$parent_query = mysqli_query($db, $sql_parent_menu);
										while($parent_obj = mysqli_fetch_object($parent_query))
										{                                            
                                                
                                            $selected= ($parent_obj->content_id == $locations_ID)? 'selected="selected"' : '';
                                            
								            echo '<option '.$selected.' value="'.$parent_obj->locations_ID.'">'.$parent_obj->locations_name.'</option>';											
									
										}
                                        ?>
                                       
                                    </select>
                                 </div>
                              </div>   
                                                          
                               <div class="form-group <?php echo $messages["hotels_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="hotels_name">Hotelname <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="hotels_name" value="<?php echo $hotels_name;?>"/>
                                 		<span for="hotels_name" class="help-block"><?php echo $messages["hotels_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group <?php echo $messages["hotels_star"]["status"] ?>">
                              		<label class="control-label col-md-3" for="hotels_star">Hotels (wie viele Sterne) <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="number" step="1" max="5" min="1" placeholder="" class="form-control" name="hotels_star" value="<?php echo $hotels_star;?>"/>
                                 		<span for="hotels_star" class="help-block"><?php echo $messages["hotels_star"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group  <?php echo $messages["hotels_notes"]["status"] ?>">
                              		<label class="control-label col-md-3" for="hotels_notes">Notes</label>
                              		<div class="col-md-9">
                                 		<textarea rows="6" class="form-control" name="hotels_notes"><?php echo $hotels_notes; ?></textarea>
                                 		<!--<span for="hotels_notes" class="help-block"><span class="label label-danger">NOTE!</span> Use + (addition operator) to seperate answers (White space tolerable). For 2 or more correct answers, use = (equal sign) (Also White space tolerable). In case of a <strong>Text type</strong> question, just enter the text exactly how you want it (including punctuations).<br /><br /><strong><?php echo $messages["hotels_notes"]["msg"] ?></strong></span>-->
                              		</div>
                           	  </div>       
                         	
                              <div class="form-group last">
                                  <label for="hotels_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="hotels_status">
                                        <option <?php if($hotels_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($hotels_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
                                     </select>
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
         
         
         <!--------------------------END PAGE CONTENT------------------------->
         
         
<!-----MODALS FOR THIS PAGE START ---->



<!-----MODALS FOR THIS PAGE END ---->
  




<!-----------------------Here goes the rest of the page --------------------------------------------->

<!-- END PAGE CONTENT-->
                </div>
                <!-- END PAGE -->    
        </div>
        <!-- END CONTAINER -->
        
        <!-- BEGIN FOOTER -->
        
        <?php require_once('footer.php'); ?>
        
        <!-- END FOOTER -->
      
        <!-- BEGIN CORE PLUGINS --> 
          
        
		<?php require_once('scripts.php'); ?>
        
        <!-- END CORE PLUGINS -->
        
        
       <!-----PAGE LEVEL SCRIPTS BEGIN--->
       
       <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>
       
       <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/ckeditor/ckeditor.js"></script>
       
       <script>
	   
	    $( "#exercise_type1" ).change(function() {
            $('input[name="content_topic"]').val($(this).val());
        });
		
		$( 'select[name="grammar"]' ).change(function() {
           
		   if($(this).val()=='1')
		   
		   		$( '#content_topic' ).show();
				
			else
			
				$( '#content_topic' ).hide();	
		   	
        });
	   
	   </script>
       
       <!-----PAGE LEVEL SCRIPTS END--->
 
 
        
        <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>

<?php 
if($alert_type=='success' && isset($_POST["Submit"]))
{
	//usleep(3000000);
	echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'&s_factor=1";</script>';
}
?>