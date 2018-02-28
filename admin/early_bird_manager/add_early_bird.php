<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";

$hotels_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
	
$err_easy="has-error";

$eb_discount_date_from = "";
$eb_status = 1;
$eb_discount = "";
$eb_notes = "";
$confirmation=0;

$err=0;

$messages = array(
					'eb_discount_date_from' => array('status' => '', 'msg' => ''),
                    'eb_discount' => array('status' => '', 'msg' => ''),
                    'eb_stay_from' => array('status' => '', 'msg' => ''),
                    'eb_status' => array('status' => '', 'msg' => ''),
                    'eb_notes' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	
	if(empty($date_from) && empty($date_to)):    
        
            $messages["eb_discount_date_from"]["status"]=$err_easy;
            $messages["eb_discount_date_from"]["msg"]="Rabatt Datum ist Pflichtfeld";
            $err++;	
			
	endif;
	
	if(!empty($date_from) && mysqli_num_rows(mysqli_query($db, "SELECT eb_ID from ".$db_suffix."early_bird where hotels_ID = $hotels_ID AND (eb_discount_date_from='$date_from' OR eb_discount_date_to='$date_from')"))>0):		
        if(!$confirmation):
            $messages["eb_discount_date_from"]["status"]=$err_easy;
            $messages["eb_discount_date_from"]["msg"]="Besonderrabatt für diesen Datum schon existiert";
            $err++;		        
		endif;
	endif;
	
	if(!empty($date_to) && mysqli_num_rows(mysqli_query($db, "SELECT eb_ID from ".$db_suffix."early_bird where hotels_ID = $hotels_ID AND (eb_discount_date_from='$date_to' OR eb_discount_date_to='$date_to')"))>0):		
        if(!$confirmation):
            $messages["eb_discount_date_from"]["status"]=$err_easy;
            $messages["eb_discount_date_from"]["msg"]="Besonderrabatt für diesen Datum schon existiert";
            $err++;		        
		endif;
	endif;
    
    if($date_from1<$date_to)
    {
		$messages["eb_stay_from"]["status"]=$err_easy;
		$messages["eb_stay_from"]["msg"]="Reisedatum muss später als Buchungsdatum sein";
		$err++;		
	}    
    
    if(empty($eb_discount))
	{
		$messages["eb_discount"]["status"]=$err_easy;
		$messages["eb_discount"]["msg"]="Rabatt ist Pflichtfeld";
		$err++;		
	}    
	
	if($err == 0)
	{
		if(!empty($date_from) && empty($date_to))
            
            $date_to=$date_from;
        
        if(!empty($date_to) && empty($date_from))
            
            $date_from=$date_to;
        
        if(!empty($date_from1) && empty($date_to1))
            
            $date_to1=$date_from1;
        
        if(!empty($date_to1) && empty($date_from1))
            
            $date_from1=$date_to1;
		
		$sql = "INSERT INTO ".$db_suffix."early_bird SET hotels_ID='$hotels_ID',eb_discount_date_from='$date_from',eb_discount_date_to='$date_to',eb_discount='$eb_discount',eb_status='$eb_status',eb_notes='$eb_notes', eb_stay_from='$date_from1', eb_stay_to='$date_to1'";
        
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Daten erfolgreich gespeichert";		
			$alert_box_show="show";
			$alert_type="success";
			
			$eb_discount_date_from = "";
			
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



                                        <!-- BEGIN PAGE eb_discount_date_from & BREADCRUMB-->
                                        <h3 class="page-eb_discount_date_from">
                                                Eary Bird Rabatt für diesen Hotel einfügen
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=early_bird&id='.$hotels_ID; ?>">Eary Bird Rabatt Liste für diesen Hotel</a>
                                                </li>
                                        </ul>
                                        <!-- END PAGE eb_discount_date_from & BREADCRUMB-->
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
                                   
                               <div class="form-group <?php echo $messages["eb_discount"]["status"] ?>">
                              		<label class="control-label col-md-3" for="eb_discount">Rabatt in prozent<span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="number" min="1" step="any" placeholder="z.B. 10.5" class="form-control" name="eb_discount" value="<?php echo $eb_discount;?>"/>
                                 		<span for="eb_discount" class="help-block"><?php echo $messages["eb_discount"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group <?php echo $messages["eb_discount_date_from"]["status"] ?>">
                              		<label class="control-label col-md-3" for="eb_discount_date_from">Buchungsdatum</label>
                              		<div class="col-md-4">
                                 		<div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                                            <input type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_from" value="<?php echo $date_from; ?>">
                                                            <span class="input-group-addon"> - </span>
                                                            <input type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_to" value="<?php echo $date_to; ?>"> </div>
                                 		<span for="eb_discount_date_from" class="help-block">z.B. DD.MM.YYYY - DD.MM.YYYY oder nur den einzigen Datum z.B. DD.MM.YYYY<br/><?php echo $messages["eb_discount_date_from"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group <?php echo $messages["eb_discount_date_from"]["status"]; if(empty($messages["eb_discount_date_from"]["status"])) echo 'hide';  ?>">
                              		<label class="control-label col-md-3" for="eb_discount_date_from">Bestätigung</label>
                              		<div class="col-md-4">
                                 		<select class="form-control" name="confirmation">
                                            <option <?php if($confirmation==0) echo 'selected="selected"'; ?> value="0">Nein, das geht nicht</option>
                                            <option <?php if($confirmation==1) echo 'selected="selected"'; ?> value="1">Ja, alles klar, ich verstehe</option>                                            
                                     </select>
                                        <span for="eb_discount_date_from" class="help-block">Bitte bestätigen</span>
                              		</div>
                           	  </div>        
                                   
                              <div class="form-group <?php echo $messages["eb_stay_from"]["status"] ?>">
                              		<label class="control-label col-md-3" for="eb_stay_from">Reisedatum</label>
                              		<div class="col-md-4">
                                 		<div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                                            <input required type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_from1" value="<?php echo $date_from1; ?>">
                                                            <span class="input-group-addon"> - </span>
                                                            <input required type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_to1" value="<?php echo $date_to1; ?>"> </div>
                                 		<span for="eb_stay_from" class="help-block">z.B. DD.MM.YYYY - DD.MM.YYYY oder nur den einzigen Datum z.B. DD.MM.YYYY<br/><?php echo $messages["eb_stay_from"]["msg"] ?></span>
                              		</div>
                           	  </div>       
                                   
                              <div class="form-group  <?php echo $messages["eb_notes"]["status"] ?>">
                              		<label class="control-label col-md-3" for="eb_notes">Notes</label>
                              		<div class="col-md-9">
                                 		<textarea rows="6" class="form-control" name="eb_notes"><?php echo $eb_notes; ?></textarea>
                                 		<!--<span for="eb_notes" class="help-block"><span class="label label-danger">NOTE!</span> Use + (addition operator) to seperate answers (White space tolerable). For 2 or more correct answers, use = (equal sign) (Also White space tolerable). In case of a <strong>Text type</strong> question, just enter the text exactly how you want it (including punctuations).<br /><br /><strong><?php echo $messages["eb_notes"]["msg"] ?></strong></span>-->
                              		</div>
                           	  </div>       
                         	
                              <div class="form-group last">
                                  <label for="eb_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="eb_status">
                                        <option <?php if($eb_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($eb_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
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