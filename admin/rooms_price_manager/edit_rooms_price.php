<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

$err=0;

$rp_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
$sql = "select * from ".$db_suffix."rooms_price where rp_ID = $rp_ID limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);
	$rooms_ID       = $content->rooms_ID;
    $rp_price       = $content->rp_price;
	$rp_status    = $content->rp_status;
    $rp_notes    = $content->rp_notes;
    $rp_update_time    = $content->rp_update_time;
	$hotels_ID=$content->hotels_ID;
    
    $date_from=$content->rp_price_date_from;	
	$date_to=$content->rp_price_date_to;
}

$messages = array(
					'rooms_ID' => array('status' => '', 'msg' => ''),						  				 
					'rp_price_date_from' => array('status' => '', 'msg' => ''),
                    'rp_price' => array('status' => '', 'msg' => ''),
                    'rp_status' => array('status' => '', 'msg' => ''),
                    'rp_notes' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
    
    if(empty($date_from) && empty($date_to)):
    
        if(mysqli_num_rows(mysqli_query($db, "SELECT rp_ID from ".$db_suffix."rooms_price where hotels_ID = $hotels_ID AND rooms_ID = $rooms_ID AND (rp_price_date_from='0000-00-00' AND rp_price_date_to='0000-00-00') AND rp_ID != '$rp_ID'"))>0)
        {
            $messages["rooms_ID"]["status"]=$err_easy;
            $messages["rooms_ID"]["msg"]="Regularpreis schon existiert";
            $err++;		
        }
		
	endif;
	
	if(!empty($date_from) && mysqli_num_rows(mysqli_query($db, "SELECT rp_ID from ".$db_suffix."rooms_price where hotels_ID = $hotels_ID AND rooms_ID = $rooms_ID AND (rp_price_date_from='$date_from' OR rp_price_date_to='$date_from') AND rp_ID != '$rp_ID'"))>0):		
        
		$messages["rooms_ID"]["status"]=$err_easy;
		$messages["rooms_ID"]["msg"]="Besonderpreis für diesen Datum schon existiert";
		$err++;		        
		
	endif;
	
	if(!empty($date_to) && mysqli_num_rows(mysqli_query($db, "SELECT rp_ID from ".$db_suffix."rooms_price where hotels_ID = $hotels_ID AND rooms_ID = $rooms_ID AND (rp_price_date_from='$date_to' OR rp_price_date_to='$date_to') AND rp_ID != '$rp_ID'"))>0):		
        
		$messages["rooms_ID"]["status"]=$err_easy;
		$messages["rooms_ID"]["msg"]="Besonderpreis für diesen Datum schon existiert";
		$err++;		        
		
	endif;
    
    if(empty($rp_price))
	{
		$messages["rp_price"]["status"]=$err_easy;
		$messages["rp_price"]["msg"]="Preis ist Pflichtfeld";
		$err++;		
	}    
	
	if($err == 0)
	{
		if(!empty($date_from) && empty($date_to))
            
            $date_to=$date_from;
        
        if(!empty($date_to) && empty($date_from))
            
            $date_from=$date_to;
        
        $sql = "UPDATE ".$db_suffix."rooms_price SET rooms_ID='$rooms_ID',rp_price_date_from='$date_from',rp_price_date_to='$date_to',rp_price='$rp_price',rp_status='$rp_status',rp_notes='$rp_notes' WHERE rp_ID='$rp_ID'";
        
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Daten erfolgreich aktualisiert";		
			$alert_box_show="show";
			$alert_type="success";					
		}else{
			$alert_box_show="show";
			$alert_type="danger";
			$alert_message="Database encountered some error while updating.";
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
	$alert_message="Daten erfolgreich aktualisiert";		
	$alert_box_show="show";
	$alert_type="success";
}


?>

<!-----PAGE LEVEL CSS BEGIN--->

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

<!-----PAGE LEVEL CSS END--->



                                        <!-- BEGIN PAGE rp_price_date_from & BREADCRUMB-->
                                        <h3 class="page-rp_price_date_from">
                                                Zimmerpreis für diesen Hotel aktualisieren
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=rooms_price&id='.$content->hotels_ID; ?>">Zimmerpreis Liste für diesen Hotel</a>
                                                </li>
                                        </ul>
                                        <!-- END PAGE rp_price_date_from & BREADCRUMB-->
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
                                   
                               <div class="form-group <?php echo $messages["rooms_ID"]["status"] ?>">
                                 <label for="parent" class="control-label col-md-3">Mealtyp <span class="required">*</span></label>
                                 <div class="col-md-4">
                                    <select class="form-control select2me"  data-placeholder="Auswählen" tabindex="0" name="rooms_ID">                                                                              
                                       <?php
									   $sql_parent_menu = "SELECT rooms_ID, rooms_title FROM ".$db_suffix."rooms where rooms_status='1'";	
								        $parent_query = mysqli_query($db, $sql_parent_menu);
										while($parent_obj = mysqli_fetch_object($parent_query))
										{                                            
                                                
                                            $selected= ($parent_obj->rooms_ID == $rooms_ID)? 'selected="selected"' : '';
                                            
								            echo '<option '.$selected.' value="'.$parent_obj->rooms_ID.'">'.$parent_obj->rooms_title.'</option>';											
									
										}
                                        ?>
                                       
                                    </select>
                                    <span for="rooms_ID" class="help-block"><?php echo $messages["rooms_ID"]["msg"] ?></span>
                                 </div>
                              </div>   
                                                          
                              <div class="form-group <?php echo $messages["rp_price"]["status"] ?>">
                              		<label class="control-label col-md-3" for="rp_price">Preis <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="number" min="0" step="any" placeholder="" class="form-control" name="rp_price" value="<?php echo $rp_price;?>"/>
                                 		<span for="rp_price" class="help-block"><?php echo $messages["rp_price"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group <?php echo $messages["rp_price_date_from"]["status"] ?>">
                              		<label class="control-label col-md-3" for="rp_price_date_from">Besonder preis für Datum</label>
                              		<div class="col-md-4">
                                 		<div class="input-group input-large date-picker input-daterange">
                                                            
                                                            <input value="<?php echo $date_from; ?>" type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_from">
                                            
                                                            <span class="input-group-addon"> - </span>
                                            
                                                            <input value="<?php echo $date_to; ?>" type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_to"> 
                                        </div>
                                 		<span for="rp_price_date_from" class="help-block">z.B. DD.MM.YYYY - DD.MM.YYYY oder nur den einzigen Datum z.B. DD.MM.YYYY<br/><?php echo $messages["rp_price_date_from"]["msg"] ?></span>
                              		</div>
                           	  </div>   
                                   
                              <div class="form-group  <?php echo $messages["rp_notes"]["status"] ?>">
                              		<label class="control-label col-md-3" for="rp_notes">Notes</label>
                              		<div class="col-md-9">
                                 		<textarea rows="6" class="form-control" name="rp_notes"><?php echo $rp_notes; ?></textarea>
                                 		<!--<span for="rp_notes" class="help-block"><span class="label label-danger">NOTE!</span> Use + (addition operator) to seperate answers (White space tolerable). For 2 or more correct answers, use = (equal sign) (Also White space tolerable). In case of a <strong>Text type</strong> question, just enter the text exactly how you want it (including punctuations).<br /><br /><strong><?php echo $messages["rp_notes"]["msg"] ?></strong></span>-->
                              		</div>
                           	  </div>       
                         	
                              <div class="form-group last">
                                  <label for="rp_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="rp_status">
                                        <option <?php if($rp_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($rp_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
                                     </select>
                                  </div>
                              </div>
                                   
                              <div class="form-group">
                                  <label for="locations_status" class="control-label col-md-3">Letze aktualisiert am:</label>
                                  <div class="col-md-2">
                                      <input type="text" class="form-control" disabled value="<?php echo $rp_update_time;?>" />
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
       
       <!-----PAGE LEVEL SCRIPTS END--->
 
 
        
        <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>

<?php 
if($alert_type=='success' && isset($_POST["Submit"]))
{
	//usleep(3000000);
	//echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'&s_factor=1";</script>';
}
?>