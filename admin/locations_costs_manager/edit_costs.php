<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

$err=0;

$lc_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
$sql = "select * from ".$db_suffix."locations_costs where lc_ID = $lc_ID limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);
    $lc_costs_date_from       = $content->lc_costs_date_from;
    $lc_title       = $content->lc_title;
    $lc_costs       = $content->lc_costs;
	$lc_status    = $content->lc_status;
    $lc_notes    = $content->lc_notes;
    $lc_update_time    = $content->lc_update_time;
    $locations_ID=$content->locations_ID;
    
    $date_from=$content->lc_costs_date_from;	
	$date_to=$content->lc_costs_date_to;
}

$messages = array(
					'lc_costs_date_from' => array('status' => '', 'msg' => ''),
                    'lc_title' => array('status' => '', 'msg' => ''),
                    'lc_costs' => array('status' => '', 'msg' => ''),
                    'lc_status' => array('status' => '', 'msg' => ''),
                    'lc_notes' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST); 
    
    if(empty($date_from) && empty($date_to)):
    
        if(mysqli_num_rows(mysqli_query($db, "SELECT lc_ID from ".$db_suffix."locations_costs where locations_ID = $locations_ID AND (lc_costs_date_from='0000-00-00' AND lc_costs_date_to='0000-00-00') AND lc_ID != '$lc_ID' AND lc_title='$lc_title'"))>0)
        {
            $messages["lc_costs_date_from"]["status"]=$err_easy;
            $messages["lc_costs_date_from"]["msg"]="Regularkost schon existiert";
            $err++;		
        }
		
	endif;
	
	if(!empty($date_from) && mysqli_num_rows(mysqli_query($db, "SELECT lc_ID from ".$db_suffix."locations_costs where locations_ID = $locations_ID AND (lc_costs_date_from='$date_from' OR lc_costs_date_to='$date_from') AND lc_ID != '$lc_ID' AND lc_title='$lc_title'"))>0):		
        
		$messages["lc_costs_date_from"]["status"]=$err_easy;
		$messages["lc_costs_date_from"]["msg"]="Besonderkost für diesen Datum schon existiert";
		$err++;		        
		
	endif;
	
	if(!empty($date_to) && mysqli_num_rows(mysqli_query($db, "SELECT lc_ID from ".$db_suffix."locations_costs where locations_ID = $locations_ID AND (lc_costs_date_from='$date_to' OR lc_costs_date_to='$date_to') AND lc_ID != '$lc_ID' AND lc_title='$lc_title'"))>0):		
        
		$messages["lc_costs_date_from"]["status"]=$err_easy;
		$messages["lc_costs_date_from"]["msg"]="Besonderkost für diesen Datum schon existiert";
		$err++;		        
		
	endif;
    
    if(empty($lc_title))
	{
		$messages["lc_title"]["status"]=$err_easy;
		$messages["lc_title"]["msg"]="Titel ist Pflichtfeld";
		$err++;		
	}
    /* else{
        
        if(mysqli_num_rows(mysqli_query($db, "SELECT lc_ID from ".$db_suffix."locations_costs where locations_ID = $locations_ID AND lc_title='$lc_title' AND lc_ID != '$lc_ID'"))>0)
        {
            $messages["lc_title"]["status"]=$err_easy;
            $messages["lc_title"]["msg"]="Titel schon existiert";
            $err++;		
        }
    } */
    
    if(empty($lc_costs))
	{
		$messages["lc_costs"]["status"]=$err_easy;
		$messages["lc_costs"]["msg"]="Kosten ist Pflichtfeld";
		$err++;		
	}    
	
	if($err == 0)
	{
		if(!empty($date_from) && empty($date_to))
            
            $date_to=$date_from;
        
        if(!empty($date_to) && empty($date_from))
            
            $date_from=$date_to;
        
        $sql = "UPDATE ".$db_suffix."locations_costs SET lc_title='$lc_title',lc_costs_date_from='$date_from',lc_costs_date_to='$date_to',lc_costs='$lc_costs',lc_status='$lc_status',lc_notes='$lc_notes' WHERE lc_ID='$lc_ID'";
        
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



                                        <!-- BEGIN PAGE lc_costs_date_from & BREADCRUMB-->
                                        <h3 class="page-lc_costs_date_from">
                                                Kosten für diese Destination aktualisieren
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=costs&id='.$locations_ID; ?>">Kosten Liste für diese Destination</a>
                                                </li>
                                        </ul>
                                        <!-- END PAGE lc_costs_date_from & BREADCRUMB-->
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
                                   
                              <div class="form-group <?php echo $messages["lc_title"]["status"] ?>">
                              		<label class="control-label col-md-3" for="lc_title">Titel <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<select class="form-control input-medium" name="lc_title">
                                            
                                            <option <?php if($lc_title=='Office Profit') echo 'selected';  ?> value="Office Profit">Office Profit</option>
                                            <option <?php if($lc_title=='MwSt') echo 'selected';  ?> value="MwSt">MwSt</option>
                                            <option <?php if($lc_title=='Promoter Provision') echo 'selected';  ?> value="Promoter Provision">Promoter Provision</option>
                                            
                                        </select>
                                            
                                 		<span for="lc_title" class="help-block"><?php echo $messages["lc_title"]["msg"] ?></span>
                                    </div>
                           	  </div>                            
                              
                              <div class="form-group <?php echo $messages["lc_costs"]["status"] ?>">
                              		<label class="control-label col-md-3" for="lc_costs">Kosten <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="100&euro; oder 7%" class="form-control" name="lc_costs" value="<?php echo $lc_costs;?>"/>
                                 		<span for="lc_costs" class="help-block"><?php echo $messages["lc_costs"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <!-- <div class="form-group <?php echo $messages["lc_costs_date_from"]["status"] ?>">
                              		<label class="control-label col-md-3" for="lc_costs_date_from">Besonder Kosten für Datum</label>
                              		<div class="col-md-4">
                                 		<div class="input-group input-large date-picker input-daterange">
                                                            
                                                            <input value="<?php echo $date_from; ?>" type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_from">
                                            
                                                            <span class="input-group-addon"> - </span>
                                            
                                                            <input value="<?php echo $date_to; ?>" type="date" min="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" name="date_to"> 
                                        </div>
                                 		<span for="lc_costs_date_from" class="help-block">z.B. DD.MM.YYYY - DD.MM.YYYY oder nur den einzigen Datum z.B. DD.MM.YYYY<br/><?php echo $messages["lc_costs_date_from"]["msg"] ?></span>
                              		</div>
                           	  </div> -->   
                                   
                              <div class="form-group  <?php echo $messages["lc_notes"]["status"] ?>">
                              		<label class="control-label col-md-3" for="lc_notes">Notes</label>
                              		<div class="col-md-9">
                                 		<textarea rows="6" class="form-control" name="lc_notes"><?php echo $lc_notes; ?></textarea>
                                 		<!--<span for="lc_notes" class="help-block"><span class="label label-danger">NOTE!</span> Use + (addition operator) to seperate answers (White space tolerable). For 2 or more correct answers, use = (equal sign) (Also White space tolerable). In case of a <strong>Text type</strong> question, just enter the text exactly how you want it (including punctuations).<br /><br /><strong><?php echo $messages["lc_notes"]["msg"] ?></strong></span>-->
                              		</div>
                           	  </div>       
                         	
                              <div class="form-group last">
                                  <label for="lc_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="lc_status">
                                        <option <?php if($lc_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($lc_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
                                     </select>
                                  </div>
                              </div>
                                   
                              <div class="form-group">
                                  <label for="locations_status" class="control-label col-md-3">Letze aktualisiert am:</label>
                                  <div class="col-md-2">
                                      <input type="text" class="form-control" disabled value="<?php echo $lc_update_time;?>" />
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
<script>
		 
$( "#lc_title_select" ).change(function() {
    $('input[name="lc_title"]').val($(this).val());
});
                
        
</script> 
 
        
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