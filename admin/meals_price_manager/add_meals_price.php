<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";

$hotels_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
	
$err_easy="has-error";

$mp_price_date_from = "";
$mp_price_date_to = "";
$mp_status = 1;
$mp_price = "";
$mp_notes = "";
$meals_ID = "";

$err=0;

$messages = array(
					'meals_ID' => array('status' => '', 'msg' => ''),						  				 
					'mp_price_date_from' => array('status' => '', 'msg' => ''),
					'mp_price_date_to' => array('status' => '', 'msg' => ''),
                    'mp_price' => array('status' => '', 'msg' => ''),
                    'mp_status' => array('status' => '', 'msg' => ''),
                    'mp_notes' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	//echo "SELECT mp_ID from ".$db_suffix."meals_price where hotels_ID = $hotels_ID AND meals_ID = $meals_ID AND mp_price_range=''";
	
    if(empty($date_from) && empty($date_to)):
    
        if(mysqli_num_rows(mysqli_query($db, "SELECT mp_ID from ".$db_suffix."meals_price where hotels_ID = $hotels_ID AND meals_ID = $meals_ID AND (mp_price_date_from='0000-00-00' AND mp_price_date_to='0000-00-00')"))>0)
        {
            $messages["meals_ID"]["status"]=$err_easy;
            $messages["meals_ID"]["msg"]="Regularpreis schon existiert";
            $err++;		
        }
		
	endif;
	
	if(!empty($date_from) && mysqli_num_rows(mysqli_query($db, "SELECT mp_ID from ".$db_suffix."meals_price where hotels_ID = $hotels_ID AND meals_ID = $meals_ID AND (mp_price_date_from='$date_from' OR mp_price_date_to='$date_from')"))>0):		
        
		$messages["meals_ID"]["status"]=$err_easy;
		$messages["meals_ID"]["msg"]="Besonderpreis für diesen Datum schon existiert";
		$err++;		        
		
	endif;
	
	if(!empty($date_to) && mysqli_num_rows(mysqli_query($db, "SELECT mp_ID from ".$db_suffix."meals_price where hotels_ID = $hotels_ID AND meals_ID = $meals_ID AND (mp_price_date_from='$date_to' OR mp_price_date_to='$date_to')"))>0):		
        
		$messages["meals_ID"]["status"]=$err_easy;
		$messages["meals_ID"]["msg"]="Besonderpreis für diesen Datum schon existiert";
		$err++;		        
		
	endif;
    
    if(empty($mp_price))
	{
		$messages["mp_price"]["status"]=$err_easy;
		$messages["mp_price"]["msg"]="Preis ist Pflichtfeld";
		$err++;		
	}    
	
	if($err == 0)
	{
		$sql = "INSERT INTO ".$db_suffix."meals_price SET hotels_ID='$hotels_ID',meals_ID='$meals_ID',mp_price_date_from='$date_from', mp_price_date_to='$date_to',mp_price='$mp_price',mp_status='$mp_status',mp_notes='$mp_notes'";
        
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Daten erfolgreich gespeichert";		
			$alert_box_show="show";
			$alert_type="success";
			
			$mp_price_date_from = "";
			
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



                                        <!-- BEGIN PAGE mp_price_date_from & BREADCRUMB-->
                                        <h3 class="page-mp_price_date_from">
                                                Mealpreis für diesen Hotel einfügen
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=meals_price&id='.$hotels_ID; ?>">Mealpreis Liste für diesen Hotel</a>
                                                </li>
                                        </ul>
                                        <!-- END PAGE mp_price_date_from & BREADCRUMB-->
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
                                   
                               <div class="form-group <?php echo $messages["meals_ID"]["status"] ?>">
                                 <label for="parent" class="control-label col-md-3">Mealtyp <span class="required">*</span></label>
                                 <div class="col-md-4">
                                    <select class="form-control select2me"  data-placeholder="Auswählen" tabindex="0" name="meals_ID">                                                                              
                                       <?php
									   $sql_parent_menu = "SELECT meals_ID, meals_title FROM ".$db_suffix."meals where meals_status='1'";	
								        $parent_query = mysqli_query($db, $sql_parent_menu);
										while($parent_obj = mysqli_fetch_object($parent_query))
										{                                            
                                                
                                            $selected= ($parent_obj->meals_ID == $meals_ID)? 'selected="selected"' : '';
                                            
								            echo '<option '.$selected.' value="'.$parent_obj->meals_ID.'">'.$parent_obj->meals_title.'</option>';											
									
										}
                                        ?>
                                       
                                    </select>
                                    <span for="meals_ID" class="help-block"><?php echo $messages["meals_ID"]["msg"] ?></span>
                                 </div>
                              </div>   
                                                          
                              <div class="form-group <?php echo $messages["mp_price"]["status"] ?>">
                              		<label class="control-label col-md-3" for="mp_price">Preis <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="number" min="0" step="any" placeholder="" class="form-control" name="mp_price" value="<?php echo $mp_price;?>"/>
                                 		<span for="mp_price" class="help-block"><?php echo $messages["mp_price"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group <?php echo $messages["mp_price_date_from"]["status"] ?>">
                              		<label class="control-label col-md-3" for="mp_price_date_from">Besonder preis für Datum</label>
                              		<div class="col-md-4">
                                 		<div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                                            <input type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_from" value="<?php echo $date_from ?>">
                                                            <span class="input-group-addon"> - </span>
                                                            <input type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_to" value="<?php echo $date_to ?>"> </div>
                                 		<span for="mp_price_date_from" class="help-block">z.B. DD.MM.YYYY - DD.MM.YYYY oder nur den einzigen Datum z.B. DD.MM.YYYY<br/><?php echo $messages["mp_price_date_from"]["msg"] ?></span>
                              		</div>
                           	  </div>   
                                   
                              <div class="form-group  <?php echo $messages["mp_notes"]["status"] ?>">
                              		<label class="control-label col-md-3" for="mp_notes">Notes</label>
                              		<div class="col-md-9">
                                 		<textarea rows="6" class="form-control" name="mp_notes"><?php echo $mp_notes; ?></textarea>
                                 		<!--<span for="mp_notes" class="help-block"><span class="label label-danger">NOTE!</span> Use + (addition operator) to seperate answers (White space tolerable). For 2 or more correct answers, use = (equal sign) (Also White space tolerable). In case of a <strong>Text type</strong> question, just enter the text exactly how you want it (including punctuations).<br /><br /><strong><?php echo $messages["mp_notes"]["msg"] ?></strong></span>-->
                              		</div>
                           	  </div>       
                         	
                              <div class="form-group last">
                                  <label for="mp_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="mp_status">
                                        <option <?php if($mp_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($mp_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
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