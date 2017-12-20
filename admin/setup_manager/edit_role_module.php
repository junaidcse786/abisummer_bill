<?php

$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

require_once("function.php");
$role_id = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;

$sql = "select * from ".$db_suffix."role where role_id = $role_id AND role_id != 8 limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0){
	$usr = mysqli_fetch_object($query);
	$role_title = $usr->role_title;
	$role_desc = $usr->role_desc;
	$role_status = $usr->role_status;
}
else
	echo '<script>window.location="'.SITE_URL_ADMIN.'";</script>';


$err = 0;


$messages = array(
				  'role_title' => array('status' => '', 'msg' => ''),
				  'role_desc' => array('status' => '', 'msg' => ''),
				);
				

if(isset($_POST['Submit'])){
		
	extract($_POST);
	
	$mods = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : $mods;
	
	$role_status = isset($_REQUEST['role_status']) ? $_REQUEST['role_status'] : 0;
	
	$role_title = trim($role_title);
	
	if(!get_magic_quotes_gpc())
		{
			$role_desc = addslashes($role_desc);
		}
	
	if($role_title == ""){
		$messages["role_title"]["status"]=$err_easy;
		$messages["role_title"]["msg"]="Role Title is Required";;
		$err++;	
	}
	
	if($err == 0){
		
		$today = date('y-m-d');
				
	 $sql_roll = "UPDATE ".$db_suffix."role SET role_title = '$role_title',role_desc = '$role_desc',role_status = $role_status WHERE role_id = $role_id" ;
	 
		if(mysqli_query($db,$sql_roll))
		{
			$delete_module = "DELETE FROM ".$db_suffix."module_in_role WHERE role_id = ".$role_id ;			
			mysqli_query($db,$delete_module);
			
			foreach($mods as $md){
								
			$sql = "select * from ".$db_suffix."module where module_id = $md limit 1";
			$query =mysqli_query($db,$sql);
			if(mysqli_num_rows($query)>0){
				$row = mysqli_fetch_array($query);
				$module_key = $row['module_key'];
				mysqli_query($db,"INSERT INTO ".$db_suffix."module_in_role (module_id,role_id,module_in_role_status) VALUES ('$md','$role_id',1)");
			}
		}
			
			$alert_message="Data updated successfully";		
			$alert_box_show="show";
			$alert_type="success";	
			
		}else{
			$alert_box_show="show";
			$alert_type="danger";
			$alert_message="Database encountered some error while inserting.";
			
		}
	}
	else{
		$alert_box_show="show";
		$alert_type="danger";
		$alert_message="Please correct these errors.";
	}
}

$mods = array();

$sql = "select * from ".$db_suffix."module_in_role where role_id = $role_id";				
$query = mysqli_query($db, $sql);

$i=0;
while($row=mysqli_fetch_object($query))
{
	$mods[$i]=$row->module_id;	$i++;
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
                                                Update Role <small>Here existing User Roles can be updated</small>
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey=setup&pKey=rollmodule'; ?>">User Role List</a>
														<i class="fa fa-angle-right"></i>
                                                </li>
                                                <li>
                                                        <a  href="#">Update User Role ID: <?php echo $role_id; ?></a>
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
                               
                               
                              <h3 class="form-section">Role Info</h3> 
                             
                              <div class="form-group <?php echo $messages["role_title"]["status"] ?>">
                              		<label class="control-label col-md-3" for="role_title">Role Title <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="role_title" value="<?php echo $role_title;?>"/>
                                 		<span for="role_title" class="help-block"><?php echo $messages["role_title"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["role_desc"]["status"] ?>">
                              		<label class="control-label col-md-3" for="role_desc">Role Description</label>
                              		<div class="col-md-4">
                                 		<textarea rows="4" class="form-control" name="role_desc"><?php echo $role_desc; ?></textarea>
                                 		<span for="role_desc" class="help-block"><?php echo $messages["role_desc"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group">
                                  <label for="role_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="role_status">
                                        <option <?php if($role_status==1) echo 'selected="selected"'; ?> value="1">Active</option>
                                        <option <?php if($role_status==0) echo 'selected="selected"'; ?> value="0">InActive</option>
                                     </select>
                                  </div>
                              </div>
                              
                              <h3 class="form-section">Access to Modules</h3>
                              
                              
                              <div class="form-group">
                              <label  class="col-md-3 control-label">Modules</label>
                              <div class="col-md-9">
                                 <div class="checkbox-list">
                                 
                                 <?php
						$query = "select * from ( 
									  select um.module_id,m.module_name,um.module_in_role_status status
									  from ".$db_suffix."module_in_role um,".$db_suffix."module m 
									  where um.role_id= ".$role_id." AND um.module_id = m.module_id
									  UNION
									  select m.module_id,m.module_name,0 as status from ".$db_suffix."module m 
									  where m.module_id not in (
										  select um.module_id
										  from ".$db_suffix."module_in_role um,".$db_suffix."module m 
										  where um.role_id= ".$role_id." AND um.module_id = m.module_id
									  ) 
								  ) t1 order by t1.module_name";
								  				
						$usermodus = mysqli_query($db, $query);
							while($row = mysqli_fetch_array($usermodus)){
								$statuscheck = "";
								if(in_array($row['module_id'],$mods)){
									$statuscheck = "checked='checked'";
								}
								echo '<label><input type="checkbox" name="mod[]" value="'.$row['module_id'].'" id="txtStatus" '.$statuscheck.' />';
								echo trim($row['module_name']);
								echo '</label>';
							}
						?>
                                 
                                 </div>
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
  




<!-----------------------Here goes the rest of the page--------------------------------------------->

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