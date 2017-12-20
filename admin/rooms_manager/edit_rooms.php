<?php 

$rooms_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
$sql = "select * from ".$db_suffix."rooms where rooms_ID = $rooms_ID limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);
	$rooms_title    = $content->rooms_title;
    $rooms_persons_to_fit    = $content->rooms_persons_to_fit;
    $rooms_status    = $content->rooms_status;
    $rooms_notes    = $content->rooms_notes;
    $rooms_update_time    = $content->rooms_update_time;
}
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

$err=0;

$messages = array(
					'rooms_title' => array('status' => '', 'msg' => ''),
                    'rooms_persons_to_fit' => array('status' => '', 'msg' => ''),
                    'rooms_status' => array('status' => '', 'msg' => ''),
                    'rooms_notes' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	
	if(empty($rooms_title))
	{
		$messages["rooms_title"]["status"]=$err_easy;
		$messages["rooms_title"]["msg"]="Titel ist Pflichtfeld";
		$err++;		
	}	
    
    if(empty($rooms_persons_to_fit))
	{
		$messages["rooms_persons_to_fit"]["status"]=$err_easy;
		$messages["rooms_persons_to_fit"]["msg"]="Personenzahl ist Pflichtfeld";
		$err++;		
	}
	
	if($err == 0)
	{
		$sql = "UPDATE ".$db_suffix."rooms SET rooms_title='$rooms_title',rooms_persons_to_fit='$rooms_persons_to_fit',rooms_status='$rooms_status',rooms_notes='$rooms_notes' WHERE rooms_ID=$rooms_ID";
        
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



                                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                        <h3 class="page-title">
                                                Zimmertyp aktualisieren 
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey=rooms&pKey=rooms'; ?>">Zimmertyp Liste</a>
														<i class="fa fa-angle-right"></i>
                                                </li>
												<li>
                                                        <a  href="#">Zimmertyp ID: <?php echo $rooms_ID; ?></a>
                                                </li>
                                        </ul>
                                        <!-- END PAGE TITLE & BREADCRUMB-->
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
                                   
                               <div class="form-group <?php echo $messages["rooms_title"]["status"] ?>">
                              		<label class="control-label col-md-3" for="rooms_title">Zimmertyp Titel <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="rooms_title" value="<?php echo $rooms_title;?>"/>
                                 		<span for="rooms_title" class="help-block"><?php echo $messages["rooms_title"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group <?php echo $messages["rooms_persons_to_fit"]["status"] ?>">
                              		<label class="control-label col-md-3" for="rooms_persons_to_fit">Für wei viele Person <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="number" step="1" min="1" placeholder="" class="form-control" name="rooms_persons_to_fit" value="<?php echo $rooms_persons_to_fit;?>"/>
                                 		<span for="rooms_persons_to_fit" class="help-block"><?php echo $messages["rooms_persons_to_fit"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <div class="form-group  <?php echo $messages["rooms_notes"]["status"] ?>">
                              		<label class="control-label col-md-3" for="rooms_notes">Notes</label>
                              		<div class="col-md-9">
                                 		<textarea rows="6" class="form-control" name="rooms_notes"><?php echo $rooms_notes; ?></textarea>
                                 		<!--<span for="rooms_notes" class="help-block"><span class="label label-danger">NOTE!</span> Use + (addition operator) to seperate answers (White space tolerable). For 2 or more correct answers, use = (equal sign) (Also White space tolerable). In case of a <strong>Text type</strong> question, just enter the text exactly how you want it (including punctuations).<br /><br /><strong><?php echo $messages["rooms_notes"]["msg"] ?></strong></span>-->
                              		</div>
                           	  </div>       
                         	
                              <div class="form-group last">
                                  <label for="rooms_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="rooms_status">
                                        <option <?php if($rooms_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($rooms_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
                                     </select>
                                  </div>
                              </div>
                                   
                              <div class="form-group">
                                  <label for="locations_status" class="control-label col-md-3">Letze aktualisiert am:</label>
                                  <div class="col-md-2">
                                      <input type="text" class="form-control" disabled value="<?php echo $rooms_update_time;?>" />
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