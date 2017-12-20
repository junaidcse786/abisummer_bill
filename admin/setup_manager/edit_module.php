<?php

$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

require_once("function.php");

$module_id = isset($_REQUEST['module_id']) ? $_REQUEST['module_id']: 0;
$sql = "SELECT * FROM ".$db_suffix."module WHERE module_id = '$module_id' ";
$query_res = mysqli_query($db,$sql);
$result_obj = mysqli_fetch_object($query_res);

$module_name = 	$result_obj->module_name;
$module_title = 	$result_obj->module_title;
$module_key = 	$result_obj->module_key;
$module_image = $result_obj->module_image;
$module_priority = $result_obj->module_priority;
$module_status	 = $result_obj->module_status;
$module_menu	 = $result_obj->module_menu;

$err = 0;

$messages = array(
			'module_name' => array('status' => '', 'msg' => ''),			
			'module_title' => array('status' => '', 'msg' => ''),
			'module_key' => array('status' => '', 'msg' => ''),
			'module_priority' => array('status' => '', 'msg' => ''),
			'module_image' => array('status' => '', 'msg' => '')
			);
			
if(isset($_POST['Submit'])){
	
			extract($_POST);
			
			if(empty($module_name))
			{
				$messages["module_name"]["status"]=$err_easy;
				$messages["module_name"]["msg"]="Module Name is Required";;
				$err++;		
			}
			
			if(empty($module_title))
			{
				$messages["module_title"]["status"]=$err_easy;
				$messages["module_title"]["msg"]="Module Title is Required";;
				$err++;		
			}
			
			if(empty($module_key))
			{
				$messages["module_key"]["status"]=$err_easy;
				$messages["module_key"]["msg"]="Module Key is Required";;
				$err++;		
			}
			if(!empty($module_priority) && !is_numeric($module_priority))
			{
				$messages["module_priority"]["status"]=$err_easy;
				$messages["module_priority"]["msg"]="Only Numeric Value is allowed";;
				$err++;		
			}
			
			
			if($err == 0){	
			 
				$result = "update ".$db_suffix."module set
							       	module_name ='$module_name',
								    module_title ='$module_title',
									module_key  ='$module_key',
									module_image ='$module_image',
									module_menu ='$module_menu',
									module_priority ='$module_priority',
									module_status ='$module_status' where module_id = '$module_id'";
				
				if(mysqli_query($db,$result)){
					
					$alert_message="Data updated successfully";		
					$alert_box_show="show";
					$alert_type="success";
				}
				else
				{
					$alert_box_show="show";
					$alert_type="danger";
					$alert_message="Database encountered some error while updating.";
				}
			}
			else{
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
                                                Edit Module <small>Here existing Modules can be updated</small>
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey=setup&pKey=module'; ?>">Module List</a>
														<i class="fa fa-angle-right"></i>
                                                </li>
                                                <li>
                                                        <a  href="#">Update Module ID: <?php echo $module_id; ?></a>
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
                               
                               
                              <h3 class="form-section">Module Info</h3> 
                             
                              <div class="form-group <?php echo $messages["module_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="module_name">Module Name <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="module_name" value="<?php echo $module_name;?>"/>
                                 		<span for="module_name" class="help-block"><?php echo $messages["module_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["module_title"]["status"] ?>">
                              		<label class="control-label col-md-3" for="module_title">Module Notes <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="module_title" value="<?php echo $module_title;?>"/>
                                 		<span for="module_title" class="help-block"><?php echo $messages["module_title"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["module_key"]["status"] ?>">
                              		<label class="control-label col-md-3" for="module_key">Module Key <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="module_key" value="<?php echo $module_key;?>"/>
                                 		<span for="module_key" class="help-block"><?php echo $messages["module_key"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group">
                              
                              		<label class="control-label col-md-3" for="module_image">Module Icon</label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="module_image" value="<?php echo $module_image;?>"/>
                                 		<span for="module_image" class="help-block"><?php echo $messages["module_image"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["module_priority"]["status"] ?>">
                              
                              		<label class="control-label col-md-3" for="module_priority">Module Priority</label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="module_priority" value="<?php echo $module_priority;?>"/>
                                 		<span for="module_priority" class="help-block"><?php echo $messages["module_priority"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group">
                                  <label for="module_menu" class="control-label col-md-3">Show on Sidebar?</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="module_menu">
                                        <option <?php if($module_menu==1) echo 'selected="selected"'; ?> value="1">Yes</option>
                                        <option <?php if($module_menu==0) echo 'selected="selected"'; ?> value="0">No</option>
                                     </select>
                                  </div>
                              </div>
                              
                              <div class="form-group">
                                  <label for="module_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="module_status">
                                        <option <?php if($module_status==1) echo 'selected="selected"'; ?> value="1">Active</option>
                                        <option <?php if($module_status==0) echo 'selected="selected"'; ?> value="0">InActive</option>
                                     </select>
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