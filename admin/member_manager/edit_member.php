<?php 

require_once("function.php");

$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";


$user_id = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;

$sql = "select * from ".$db_suffix."user where user_id = '$user_id' limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$usr = mysqli_fetch_object($query);
	$email = $usr->user_email;
	
	$role_id= $usr->role_id;
	$user_email= $usr->user_email;
	$user_name= $usr->user_name;
	$codes_start_date= $usr->user_validity_start;
	$codes_end_date= $usr->user_validity_end;
	$codes_org_name= $usr->user_org_name;
	$user_first_name= $usr->user_first_name;
	$user_last_name= $usr->user_last_name;
	$user_password= $usr->user_password;
	$user_trackability= $usr->user_trackability;
	$cpassword= $usr->user_password;
	$user_status= $usr->user_status;
	$user_description= $usr->user_description;
	$image_old_name=$usr->user_photo;
	$voc_set_level=$usr->user_level;
	$image_name=$image_old_name;
}


$err=0;

$messages = array(
				  'user_email' => array('status' => '', 'msg' => ''),
				  'user_password' => array('status' => '', 'msg' => ''),
				  'voc_set_level' => array('status' => '', 'msg' => ''),
				  'cpassword' => array('status' => '', 'msg' => ''),
				  'codes_org_name' => array('status' => '', 'msg' => ''),
				  'codes_start_date' => array('status' => '', 'msg' => ''),
				  'codes_end_date' => array('status' => '', 'msg' => ''),
				  'user_name' => array('status' => '', 'msg' => ''),
				  'user_first_name' => array('status' => '', 'msg' => ''),
				  'user_last_name' => array('status' => '', 'msg' => '')				  
				);


if(isset($_POST['Submit'])){	

	extract($_POST);
	
	if(empty($voc_set_level))
	{
		$messages["voc_set_level"]["status"]=$err_easy;
		$messages["voc_set_level"]["msg"]="Batch name / level is required";
		$err++;		
	}
	
	if(empty($user_email))
	{
		$messages["user_email"]["status"]=$err_easy;
		$messages["user_email"]["msg"]="Email is Required";;
		$err++;		
	}
	else if(!isEmail($user_email)){
		$messages["user_email"]["status"]=$err_easy;
		$messages["user_email"]["msg"]="Invalid Email Address";;
		$err++;
	
	}
	/*else
	{
		$dd = mysqli_query($db, "select user_id from ".$db_suffix."user where user_email='$user_email' AND user_id!='$user_id'");

		if(mysqli_num_rows($dd)>0){
		
			$messages["user_email"]["status"]=$err_easy;
			$messages["user_email"]["msg"]="Email already exists";;
			$err++;		
		}
	}*/
	
	if(empty($user_name))
	{
		$messages["user_name"]["status"]=$err_easy;
		$messages["user_name"]["msg"]="User Name is Required";;
		$err++;		
	}
	else
	{
		$dd = mysqli_query($db, "select user_id from ".$db_suffix."user where user_name='$user_name' AND user_id!='$user_id'");

		if(mysqli_num_rows($dd)>0){
		
			$messages["user_name"]["status"]=$err_easy;
			$messages["user_name"]["msg"]="User Name already exists";;
			$err++;		
		}
	}
	
	if(empty($user_first_name))
	{
		$messages["user_first_name"]["status"]=$err_easy;
		$messages["user_first_name"]["msg"]="First Name is Required";;
		$err++;		
	}
	
	if(empty($user_last_name)){
		$messages["user_last_name"]["status"]=$err_easy;
		$messages["user_last_name"]["msg"]="Last Name is Required";;
		$err++;		
	}
	if(empty($codes_org_name))
	{
		$messages["codes_org_name"]["status"]=$err_easy;
		$messages["codes_org_name"]["msg"]="Organisation Name is Required";;
		$err++;		
	}
	
	if(empty($codes_start_date))
	{
		$messages["codes_start_date"]["status"]=$err_easy;
		$messages["codes_start_date"]["msg"]="Start Date is Required";;
		$err++;		
	}
	
	if(empty($codes_end_date))
	{
		$messages["codes_end_date"]["status"]=$err_easy;
		$messages["codes_end_date"]["msg"]="End Date is Required";;
		$err++;		
	}
	
	if($user_password!=$cpassword || empty($user_password)){
		$messages["user_password"]["status"]=$err_easy;
		$messages["cpassword"]["status"]=$err_easy;
		$messages["cpassword"]["msg"]="Passwords do not match";;
		$err++;		
	}
	
	if($err == 0){
		
		if($_FILES["user_photo"]["name"]!=''){
			$image_dir = "../data/user/";
			$image_name = date('ymdgis').$_FILES['user_photo']['name'];
		}
		else
			
			$image_name=$image_old_name;
		
		if(!get_magic_quotes_gpc())
		{
			$user_description = addslashes($user_description);
		}
		
		$sql_user = "UPDATE ".$db_suffix."user SET 
		
									role_id = '$role_id',
		
		                           	user_first_name = '$user_first_name',
									
									user_last_name = '$user_last_name',
									
									user_name = '$user_name',

									user_email = '$user_email',

									user_password = '$user_password',
									
									user_trackability  ='$user_trackability',
									
									user_photo  ='$image_name',		
									
									user_org_name  ='$codes_org_name',
									
									user_validity_start  ='$codes_start_date',
									
									user_validity_end  ='$codes_end_date',

									user_description ='$user_description', 
									
									user_level ='$voc_set_level', 
									
									user_status = '$user_status'
									
									WHERE user_id = $user_id";

		if(mysqli_query($db,$sql_user))
		{										
			if($_FILES["user_photo"]["name"]!='')
			
				{
					move_uploaded_file($_FILES['user_photo']['tmp_name'], $image_dir.$image_name);
												 
					if(is_file($image_dir.$image_old_name))
					
						unlink($image_dir.$image_old_name);
				}
			$alert_box_show="show";
			$alert_type="success";
			$alert_message="Data updated successfully";
				
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

   <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
   
   <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>

<!-----PAGE LEVEL CSS END--->



                                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                        <h3 class="page-title">
                                                Update member <small>Here existing users can be updated</small>
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
                                                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey='.$pKey; ?>">Add Member</a>
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
                     <div class="actions">
                        <a href="<?php echo SITE_URL_ADMIN.'?mKey=setup&pKey=sendmessage&parent='.$user_id; ?>" class="btn green"><i class="fa fa-envelope"></i> Send Message </a>
                        
                     </div>
                  </div>
                  <div class="portlet-body form">
                  
                      <div class="form-body">
                      
                          <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <?php echo $alert_message; ?>
                          </div>
                      
                               
                               <h3 class="form-section">Personal Info</h3>
                               
                               <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                               
                               <div class="form-group <?php echo $messages["user_first_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="user_first_name">First Name <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="user_first_name" value="<?php echo $user_first_name;?>"/>
                                 		<span for="user_first_name" class="help-block"><?php echo $messages["user_first_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["user_last_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="user_last_name">Last Name <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="user_last_name" value="<?php echo $user_last_name;?>"/>
                                 		<span for="user_last_name" class="help-block"><?php echo $messages["user_last_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["codes_org_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="codes_org_name">Organisation/school Name <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="codes_org_name" value="<?php echo $codes_org_name;?>"/>
                                 		<span for="codes_org_name" class="help-block">Make sure the school name of the students and their corresponding teachers are exactly the same and also the level.<br /><?php echo $messages["codes_org_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["voc_set_level"]["status"] ?>">
                                  <label for="voc_set_level" class="control-label col-md-3">  Batch Name / Level</label>
                                  <div class="col-md-4">
                                 		<input type="text" placeholder="e.g. A1/ Gruppe 1 - A1" class="form-control" name="voc_set_level" value="<?php echo $voc_set_level;?>"/>
                                 		<span for="voc_set_level" class="help-block"><?php echo $messages["voc_set_level"]["msg"] ?></span>
                              		</div>
                              </div>
                              
                              <div class="form-group <?php echo $messages["user_email"]["status"] ?>">
                              		<label class="control-label col-md-3" for="user_email">User Email <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="user_email" value="<?php echo $user_email;?>"/>
                                 		<span for="user_email" class="help-block"><?php echo $messages["user_email"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group">
                              		<label class="control-label col-md-3" for="user_description">User Description</label>
                              		<div class="col-md-4">
                                 		<textarea rows="4" class="form-control" name="user_description"><?php echo $user_description; ?></textarea>
                                 		<span for="user_description" class="help-block"></span>
                              		</div>
                           	  </div>
                              
                              <?php if($image_name!='' && is_file("../data/user/".$image_name))
							  
							  { ?>
                              
                              
                              <div class="form-group">
										<label class="control-label col-md-3">Existing Avatar</label>
										<div class="col-md-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-new thumbnail">
													<img width="50" height="50" src="<?php echo SITE_URL.'data/user/'.$image_name; ?>" alt=""/>
												</div>
											</div>
										</div>
							</div>
                              
                              <?php } ?>
                              
                              <div class="form-group ">
										<label class="control-label col-md-3">Avatar</label>
										<div class="col-md-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
												</div>
												<div>
													<span class="btn default btn-file">
													<span class="fileinput-new">
													Select image </span>
													<span class="fileinput-exists">
													Change </span>
													<input type="file" name="user_photo">
													</span>
													<a href="#" class="btn red fileinput-exists" data-dismiss="fileinput">
													Remove </a>
												</div>
											</div>
											<div class="clearfix margin-top-10">
												<span class="label label-danger">
												NOTE!</span> Dimensions must be 29 X 29 pixels.
											</div>
										</div>
									</div>
                                
                               
                             <h3 class="form-section">Login Info</h3> 
                             
                             <div class="form-group">
                                  <label for="role_id" class="control-label col-md-3">User Role</label>
                                  <div class="col-md-3">
                                     <select class="form-control" name="role_id">
                                        
                                        <?php
									   $sql_parent_menu = "SELECT role_id, role_title FROM ".$db_suffix."role where role_status='1' and role_id!='8'";	
										$parent_query = mysqli_query($db, $sql_parent_menu);
										while($parent_obj = mysqli_fetch_object($parent_query))
										{	
											if($parent_obj->role_id == $role_id)
											
												echo '<option selected="selected" value="'.$parent_obj->role_id.'">'.$parent_obj->role_title.'</option>';
											
											else
												
												echo '<option value="'.$parent_obj->role_id.'">'.$parent_obj->role_title.'</option>';
									
										}
                                        ?>
                                        
                                     </select>
                                  </div>
                              </div>
                             
                             
                              <div class="form-group <?php echo $messages["user_name"]["status"] ?>">
                              		<label class="control-label col-md-3" for="user_name">User Name <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="text" placeholder="" class="form-control" name="user_name" value="<?php echo $user_name;?>"/>
                                 		<span for="user_name" class="help-block"><?php echo $messages["user_name"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["user_password"]["status"] ?>">
                              		<label class="control-label col-md-3" for="user_password">Password <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="password" placeholder="" class="form-control" name="user_password" value="<?php echo $user_password;?>"/>
                                 		<span for="user_password" class="help-block"><?php echo $messages["user_password"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["cpassword"]["status"] ?>">
                              		<label class="control-label col-md-3" for="cpassword">Confirm Password <span class="required">*</span></label>
                              		<div class="col-md-4">
                                 		<input type="password" placeholder="" class="form-control" name="cpassword" value="<?php echo $cpassword;?>"/>
                                 		<span for="cpassword" class="help-block"><?php echo $messages["cpassword"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-group <?php echo $messages["codes_start_date"]["status"] ?>">
                                <label class="control-label col-md-3">Start Date <span class="required">*</span></label>
                                <div class="col-md-4">
                                    <div class="input-group input-medium date date-picker" data-date-format="yyyy-mm-dd">
                                        <input name="codes_start_date" type="text" class="form-control" value="<?php echo $codes_start_date; ?>" readonly>
                                        <span class="input-group-btn">
                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <!-- /input-group -->
                                    <span class="help-block">
                                    Date when the user is valid from<br /><?php echo $messages["codes_start_date"]["msg"] ?></span>
                                </div>
                            </div>
                            
                            <div class="form-group <?php echo $messages["codes_end_date"]["status"] ?>">
                                <label class="control-label col-md-3">End Date <span class="required">*</span></label>
                                <div class="col-md-4">
                                    <div class="input-group input-medium date date-picker" data-date-format="yyyy-mm-dd">
                                        <input name="codes_end_date" type="text" class="form-control" value="<?php echo $codes_end_date; ?>" readonly>
                                        <span class="input-group-btn">
                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <!-- /input-group -->
                                    <span class="help-block">
                                    Date till the user is valid<br /><?php echo $messages["codes_end_date"]["msg"] ?></span>
                                </div>
                            </div>
                              
                              <div class="form-group">
                                  <label for="user_status" class="control-label col-md-3">Status</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="user_status">
                                        <option <?php if($user_status==1) echo 'selected="selected"'; ?> value="1">Active</option>
                                        <option <?php if($user_status==0) echo 'selected="selected"'; ?> value="0">InActive</option>
                                        <option <?php if($user_status==2) echo 'selected="selected"'; ?> value="2">Banned</option>
                                     </select>
                                  </div>
                              </div>
							  
							  <div class="form-group <?php if($role_id==15) echo 'hide'; ?>">
                                  <label for="user_trackability" class="control-label col-md-3">Share Performance?</label>
                                  <div class="col-md-2">
                                     <select class="form-control" name="user_trackability">
                                        <option <?php if($user_trackability==1) echo 'selected="selected"'; ?> value="1">Yes</option>
                                        <option <?php if($user_trackability==0) echo 'selected="selected"'; ?> value="0">No</option> 
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
       
       <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
       
       <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/scripts/components-pickers.js"></script>
        
        <script>
        jQuery(document).ready(function() {       
			   ComponentsPickers.init();
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