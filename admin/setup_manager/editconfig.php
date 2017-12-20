<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";


$table_name = $db_suffix.'config';

require_once("function.php");	


$config_id = isset($_REQUEST['config_id']) ? $_REQUEST['config_id']: 0;

$result_one = $db ->query(" SELECT * FROM ".$table_name." where config_id = $config_id ");

$object_page= mysqli_fetch_object($result_one);


$config_name=$object_page->config_name;

$config_value=$object_page->config_value;

$config_note=$object_page->config_note;


$err=0;

$messages = array(
					'config_name' => array('status' => '', 'msg' => ''),
					'config_value' => array('status' => '', 'msg' => ''),
					'config_note' => array('status' => '', 'msg' => ''),
);

if(isset($_POST['Submit']))
{	
	$config_name = strtoupper(($_POST['config_name']) ? $_POST['config_name'] : '');

	$config_value = isset($_POST['config_value']) ? $_POST['config_value'] : '';

	$config_note = isset($_POST['config_note']) ? $_POST['config_note'] : '';
	
	
	
	if(empty($config_name))
	{
		$messages["config_name"]["status"]=$err_easy;
		$messages["config_name"]["msg"]="Name is Required";;
		$err++;		
	}
	
	if(empty($config_value))
	{
		$messages["config_value"]["status"]=$err_easy;
		$messages["config_value"]["msg"]="Value is Required";;
		$err++;		
	}
	
	
	if($err == 0)
	{
		$today = date('y-m-d');
	
		$sql = "UPDATE ".$table_name." SET config_name = '$config_name', config_value = '$config_value', config_note = '$config_note' where config_id = $config_id";
		
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Data updated successfully";		
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
		$alert_message="Please correct these errors.";
		
	}
}

if(!isset($_POST["Submit"]) && isset($_GET["s_factor"]))
{
	$alert_message="Data updated successfully";		
	$alert_box_show="show";
	$alert_type="success";
}
?>

<!-----PAGE LEVEL CSS BEGIN--->


<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/plugins/select2/select2_metro.css" />

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />

<!-----PAGE LEVEL CSS END--->



                                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                        <h3 class="page-title">
                                                Edit Configuration <small>Here existing Configurations can be edited</small>
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey=setup&pKey=configuration'; ?>">Content List</a>
														<i class="fa fa-angle-right"></i>
                                                </li>
                                                
												<li>
                                                        <a  href="#">Update Configuration ID: <?php echo $config_id; ?></a>
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
                     <div class="caption"><i class="fa fa-reorder"></i>You have to fill the fields marked with <strong>*</strong></div>
                  </div>
                  <div class="portlet-body form">
                  
                      <div class="form-body">
                      
                          <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <?php echo $alert_message; ?>
                          </div>
                      
                               
                               <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                               
                               
                                <div class="form-group <?php echo $messages["config_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="config_name">Configuration Name<span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="config_name" value="<?php echo $config_name;?>"/>
                                 		<span for="config_name" class="help-block"><?php echo $messages["config_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["config_value"]["status"] ?>">
                              		<label class="control-label col-md-3" for="config_value">Configuration Value<span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="config_value" value="<?php echo $config_value;?>"/>
                                 		<span for="config_value" class="help-block"><?php echo $messages["config_value"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["config_note"]["status"] ?>">
                              		<label class="control-label col-md-3" for="config_note">Configuration Note</label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="config_note" value="<?php echo $config_note;?>"/>
                                 	</div>
                           	  </div>
                              
                              <div class="form-actions fluid">
                               <div class="col-md-offset-3 col-md-9">
                                  <button type="submit" name="Submit" class="btn green">Submit</button>
                                  <button type="reset" class="btn default">Cancel</button>                              
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
       
       <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/plugins/select2/select2.min.js"></script>
       
        <script src="<?php echo SITE_URL_ADMIN; ?>assets/scripts/form-samples.js"></script>
       
       <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/plugins/ckeditor/ckeditor.js"></script>

    	<script>
		  jQuery(document).ready(function() {    
			 FormSamples.init();
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