<?php 

$locations_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
$sql = "select * from ".$db_suffix."locations where locations_ID = $locations_ID limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);
	$locations_name       = $content->locations_name;
	$locations_status    = $content->locations_status;
    $locations_profit    = $content->locations_profit;
    $locations_update_time    = $content->locations_update_time;
}
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

$err=0;

$messages = array(
					'locations_name' => array('status' => '', 'msg' => ''),
                    'locations_profit' => array('status' => '', 'msg' => ''),	
					'locations_status' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	
	if(empty($locations_name))
	{
		$messages["title"]["status"]=$err_easy;
		$messages["title"]["msg"]="Titel is Pflichtfeld";;
		$err++;		
	}
    
    /*if(empty($locations_profit))
	{
		$messages["locations_profit"]["status"]=$err_easy;
		$messages["locations_profit"]["msg"]="Profit ist Pflichtfeld";
		$err++;		
	}*/
	
	if($err == 0)
	{
		$sql = "UPDATE ".$db_suffix."locations SET locations_profit='$locations_profit',locations_name='$locations_name',locations_status='$locations_status' WHERE locations_ID = ".$locations_ID;
        
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
                                                Destination aktualisieren 
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey=locations&pKey=locations'; ?>">Destinations Liste</a>
														<i class="fa fa-angle-right"></i>
                                                </li>
												<li>
                                                        <a  href="#">Destinations ID: <?php echo $locations_ID; ?></a>
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
                                                          
                               <div class="form-group <?php echo $messages["locations_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="locations_name">Destination Titel <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="locations_name" value="<?php echo $locations_name;?>"/>
                                 		<span for="locations_name" class="help-block"><?php echo $messages["locations_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                                   
                              <!--<div class="form-group <?php echo $messages["locations_profit"]["status"] ?>">
                              		<label class="control-label col-md-3" for="locations_profit">Profit <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="locations_profit" value="<?php echo $locations_profit;?>"/>
                                 		<span for="locations_profit" class="help-block">In Euro oder Prozent, wenn in Prozent bitte die Symbol % einfügen.
                                            <br/><?php echo $messages["locations_profit"]["msg"] ?></span>
                              		</div>
                           	  </div>-->       
                         	
                              <div class="form-group last">
                                  <label for="locations_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="locations_status">
                                        <option <?php if($locations_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($locations_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
                                     </select>
                                  </div>
                              </div>
                                   
                              <div class="form-group">
                                  <label for="locations_status" class="control-label col-md-3">Letze aktualisiert am:</label>
                                  <div class="col-md-2">
                                      <input type="text" class="form-control" disabled value="<?php echo $locations_update_time;?>" />
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