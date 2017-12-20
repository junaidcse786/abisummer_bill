<?php 

function generateCode($length) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}

?>
<!-----PAGE LEVEL CSS BEGIN--->

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

<link href="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/css/inbox.css" rel="stylesheet" type="text/css"/>


<!-----PAGE LEVEL CSS END--->
<h3 class="page-title">Nachricht senden <!--<small>Send Message to Users</small>--></h3>

             <!-- BEGIN PAGE CONTENT-->
                                              
                        <div class="row inbox">
						<div class="col-md-2">
							<?php require_once("message_manager/message_sidebar_left.php"); ?>
						</div>
						<div class="col-md-10">
							<div class="inbox-content">
<?php

$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
$err_easy="has-error";

$title = "";
$description = "";
$parent=array();


if(isset($_GET["dm_key"])){

	$dm_key=$_GET["dm_key"];
	$sql = "SELECT * FROM ".$db_suffix."draft_message where dm_key = '$dm_key'";
	$query_res = mysqli_query($db,$sql);
	if(mysqli_num_rows($query_res)>0){
			
		$content=mysqli_fetch_object($query_res);
		$title = $content->message_subject;
		$description = $content->message_text;
		
		foreach(explode(',', $content->message_receiver) as $value)
		
			$parent[]=$value;		
	}
}
	
else{	
	
	while(1){
	
		$dm_key=generateCode(5).'-'.generateCode(5).'-'.generateCode(5).'-'.generateCode(5);
		
		$sql = "SELECT dm_key FROM ".$db_suffix."draft_message where dm_key = '$dm_key'";
		$query_res = mysqli_query($db,$sql);
		if(mysqli_num_rows($query_res)>0)
		
			continue;
			
		else
		
			break;	
	}
}

$id=isset($_REQUEST["id"])? $_REQUEST["id"] : 0;

if(isset($_REQUEST["parent"]))

	$parent[]=$_REQUEST["parent"];
	
$keyword=isset($_REQUEST["keyword"])? $_REQUEST["keyword"] : "";

if($id){
		
	$sql = "select * from ".$db_suffix."message where message_id = $id AND (message_sender='".$_SESSION["user_id"]."' OR message_receiver='".$_SESSION["user_id"]."')limit 1";				
	$query = mysqli_query($db, $sql);	
	if(mysqli_num_rows($query) > 0)
	{
		$content     = mysqli_fetch_object($query);
		$message_sender       = $content->message_sender;
		$message_receiver    = $content->message_receiver;
		$message_text    = $content->message_text;
		$message_subject = $content->message_subject;
		$message_created_time = date('d-m-Y H:i', strtotime($content->message_created_time));
		
		$sql = "select * from ".$db_suffix."user u
				Left Join ".$db_suffix."role r on r.role_id=u.role_id  where u.user_id = $message_sender";				
		$query = mysqli_query($db, $sql);
		if(mysqli_num_rows($query) > 0)
		{
			$content     = mysqli_fetch_object($query);	
			
			$sender_role=$content->role_title;
			
			if($content->role_id!='8'){
				$sender_name       = $content->user_first_name.' '.$content->user_last_name;
				//$sender_email    = $content->user_email;
			}
			else{
				$sender_name       = "Administrator";
				//$sender_email    = SITE_EMAIL;
			}
		}
		
		$sql = "select * from ".$db_suffix."user where user_id = $message_receiver";				
		$query = mysqli_query($db, $sql);
		if(mysqli_num_rows($query) > 0)
		{
			$content     = mysqli_fetch_object($query);	
			if($content->role_id!='8'){
				$receiver_name       = $content->user_first_name.' '.$content->user_last_name;
				//$receiver_email    = $content->user_email;
			}
			else{
				$receiver_name       = "Administrator";
				//$receiver_email    = SITE_EMAIL;
			}
		}
		
		
	
		if($keyword=='forward'){
			$title="FW: ".$message_subject;
			$description='<p></p><hr /><p>
						 <strong>Von</strong> : '.$sender_name.'<br />
						 <strong>Gesendet am</strong> : '.$message_created_time.'<br />
						 <strong>An</strong>   : '.$receiver_name.'<br />
						 <strong>Betreff</strong>   : '.$message_subject.'</p>
						 <blockquote style="font-size:13px; margin:0 0 0 .8ex; border-left:1px #ccc solid; padding-left:1ex">'
							 .$message_text.
							 '</blockquote>';
		}
		if($keyword=='reply'){
			$parent[]=$message_sender;
			$title="Re: ".$message_subject;
			$description='<p></p><hr /><p>
						 <strong>Von</strong> : '.$sender_name.'<br />
						 <strong>Gesendet am</strong> : '.$message_created_time.'<br />
						 <strong>An</strong>   : '.$receiver_name.'<br />
						 <strong>Betreff</strong>   : '.$message_subject.'</p>
						 <blockquote style="font-size:13px; margin:0 0 0 .8ex; border-left:1px #ccc solid; padding-left:1ex">'
							 .$message_text.
							 '</blockquote>';
		}
	}
}

$err=0;

$messages = array(
					'title' => array('status' => '', 'msg' => ''),
					'description' => array('status' => '', 'msg' => ''),
					'parent' => array('status' => '', 'msg' => ''),
					
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	
	if(empty($title))
	{
		$messages["title"]["status"]=$err_easy;
		$messages["title"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}
	
	if(count($parent)<=0)
	{
		$messages["parent"]["status"]=$err_easy;
		$messages["parent"]["msg"]="Ein Benutzer muss ausgewählt werden.";;
		$err++;		
	}
	
	if(empty($description))
	{
		$messages["description"]["status"]=$err_easy;
		$messages["description"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}	
			
	
	if($err == 0)
	{
		$today = date('y-m-d');
		
		$sql="INSERT INTO ".$db_suffix."message VALUES ";	
		
		$recipient_user_id=$parent;	
		
		if(in_array("all_teachers", $recipient_user_id)){
			
			unset($recipient_user_id[array_search("all_teachers", $recipient_user_id)]);	
		
			$user_sql="SELECT user_id FROM ".$db_suffix."user WHERE role_id='15'";
			$query_execute = mysqli_query($db, $user_sql);
			while($row=mysqli_fetch_object($query_execute))
			
				array_push($recipient_user_id, $row->user_id);		
		}
		
		if(in_array("all_studs", $recipient_user_id)){
			
			unset($recipient_user_id[array_search("all_studs", $recipient_user_id)]);	
		
			$user_sql="SELECT user_id FROM ".$db_suffix."user WHERE role_id='16'";
			
			if($_SESSION["role_id"]=='15')
			
				$user_sql.=" AND user_org_name='".$_SESSION["user_org_name"]."' AND user_level='".$_SESSION["user_level"]."'";
			
			$query_execute = mysqli_query($db, $user_sql);
			while($row=mysqli_fetch_object($query_execute))
			
				array_push($recipient_user_id, $row->user_id);		
		}
		
		$recipient_user_id = array_unique($recipient_user_id);
		
		foreach($recipient_user_id as $value)
		
			$sql.="('', '".$_SESSION["user_id"]."','$value','0','0',NOW(),'$description','$title','0','0'),";	
		
		
		$sql=substr($sql, 0, -1);
		
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Nachricht erfolgreich gesendet.";	
			$alert_box_show="show";
			$alert_type="success";
			
			if(isset($dm_key))
			
				mysqli_query($db,"DELETE FROM ".$db_suffix."draft_message WHERE dm_key = '".$dm_key."'");
			
			$title = "";
			$description = "";
			$parent = array();
			$recipient_user_id = array();			
			
		}else{
			$alert_box_show="show";
			$alert_type="danger";
			$alert_message="Database encountered some error while inserting.";
		}
	}
	else
	{
		$alert_box_show="show";
		$alert_type="danger";
		$alert_message="Bitte korrigieren Sie folgenden Fehler.";
		
	}
}

if(!isset($_POST["Submit"]) && isset($_GET["s_factor"]))
{
	$alert_message="Nachricht erfolgreich gesendet.";		
	$alert_box_show="show";
	$alert_type="success";
}

?>                            
                           
                            
                            <div class="row">
            <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
               <div class="portlet box grey-cascade">
                  <div class="portlet-title">
                     <div class="caption"><i class="fa fa-reorder"></i>Felder mit einem Sternchen <strong>*</strong> müssen ausgefüllt werden.</div>
                  </div>
                  <div class="portlet-body form">
                  
                      <div class="form-body">
                      
                          <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <?php echo $alert_message; ?>
                          </div>
                          
                              
                               <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                               
                               
                                   <input name="dm_key" type="hidden" value="<?php echo $dm_key; ?>">   
                              
                              <div class="form-group <?php echo $messages["parent"]["status"] ?>">
                                 <label for="parent" class="control-label col-md-2">Senden an <span class="required">*</span></label>
                                 <div class="col-md-9">
                                    <select class="form-control select2me"  data-placeholder="Nutzer auswählen" multiple="multiple" tabindex="0" name="parent[]">
                                      
<?php
									   
$users_in_options='';$selected='';
		   
if($_SESSION["role_id"]!=8){
   
	$selected='';
	
	if(in_array("1", $parent))
	
		$selected='selected';

	$users_in_options.='<optgroup label="Administrator"><option '.$selected.' value="1">Administrator</option></optgroup>';
	
}	

if($_SESSION["role_id"]==15){
   
	$selected='';
	
	if(in_array("all_studs", $parent))
	
		$selected='selected';

	$users_in_options.='<optgroup label="Alle Studenten"><option '.$selected.' value="all_studs">Alle Studenten meines Kurs</option></optgroup>';
	
	$users_in_options.='<optgroup label="Studenten">';
	
	$sql_parent_menu = "SELECT * FROM ".$db_suffix."user where user_id IN (".STUDS_TO_LOOK_FOR_TEACHER.")";	
	
	$parent_query = mysqli_query($db, $sql_parent_menu);
	
	while($parent_obj = mysqli_fetch_object($parent_query)){
	
		$selected='';
		
		if(in_array($parent_obj->user_id, $parent))
		
			$selected='selected';
			
		$users_in_options.='<option '.$selected.'  value="'.$parent_obj->user_id.'">'.$parent_obj->user_first_name.' '.$parent_obj->user_last_name.'</option>';
	
	}
	
	$users_in_options.='</optgroup>';
	
	
}	

if($_SESSION["role_id"]==8){
   
	$selected='';
	
	if(in_array("all_studs", $parent))
	
		$selected='selected';

	$users_in_options.='<optgroup label="Alle Studenten"><option '.$selected.' value="all_studs">Alle Studenten auf dem System</option></optgroup>';
	
	
	$selected='';
	
	if(in_array("all_teachers", $parent))
	
		$selected='selected';

	$users_in_options.='<optgroup label="Alle Lehrer"><option '.$selected.' value="all_teachers">Alle Lehrer auf dem System</option></optgroup>';
	
	
	$users_in_options.='<optgroup label="Studenten">';
	
	$sql_parent_menu = "SELECT * FROM ".$db_suffix."user where role_id='16'";	
	
	$parent_query = mysqli_query($db, $sql_parent_menu);
	
	while($parent_obj = mysqli_fetch_object($parent_query)){
	
		$selected='';
		
		if(in_array($parent_obj->user_id, $parent))
		
			$selected='selected';
			
		$users_in_options.='<option '.$selected.'  value="'.$parent_obj->user_id.'">'.$parent_obj->user_first_name.' '.$parent_obj->user_last_name.' [<strong>'.$parent_obj->user_org_name.' - '.$parent_obj->user_level.']</option>';
	
	}
	
	$users_in_options.='</optgroup>';
	
	$users_in_options.='<optgroup label="Lehrer">';
	
	$sql_parent_menu = "SELECT * FROM ".$db_suffix."user where role_id='15'";	
	
	$parent_query = mysqli_query($db, $sql_parent_menu);
	
	while($parent_obj = mysqli_fetch_object($parent_query)){
	
		$selected='';
		
		if(in_array($parent_obj->user_id, $parent))
		
			$selected='selected';
			
		$users_in_options.='<option '.$selected.'  value="'.$parent_obj->user_id.'">'.$parent_obj->user_first_name.' '.$parent_obj->user_last_name.' [<strong>'.$parent_obj->user_org_name.' - '.$parent_obj->user_level.']</option>';
	
	}
	
	$users_in_options.='</optgroup>';
	
	
}							   
									   
echo $users_in_options;
										
?>                                     
                                    </select>
                                    <span for="parent" class="help-block"><?php echo $messages["parent"]["msg"] ?></span>
                                 </div>
                              </div>                                      
                             
                                                         
                               <div class="form-group <?php echo $messages["title"]["status"] ?>">
                              		<label class="control-label col-md-2" for="title">Betreff <span class="required">*</span></label>
                              		<div class="col-md-9">
                                 		<input type="text" placeholder="" class="form-control message_subject" name="title" value="<?php echo $title;?>"/>
                                 		<span for="title" class="help-block"><?php echo $messages["title"]["msg"] ?></span>
                              		</div>
                           	  </div>
                              
                              <div class="form-body <?php echo $messages["description"]["status"] ?>">
									<div class="form-group">
										<label class="control-label col-md-2">Nachricht <span class="required">*</span></label>
										<div class="col-md-10">
											<textarea class="ckeditor form-control" id="message_text" name="description" rows="6"><?php echo str_replace('\\','',$description); ?></textarea>
                                            <span for="description" class="help-block"><?php echo $messages["description"]["msg"] ?></span>
										</div>
									</div>
							 </div>
                             
                             
                            <div class="form-actions fluid">
                               <div class="col-md-offset-2 col-md-9">
                                  <button type="submit" name="Submit" class="btn green">Senden</button>
                                  <button type="reset" class="btn default">Zurücksetzen</button>                              
                               </div>
                        	</div>
                            
                            </form>
                      
                      </div>
                      
                  </div>
               </div>
               <!-- END EXAMPLE TABLE PORTLET-->
            </div>
         </div>
                            </div>
						</div>
					</div>
         
         
         
<!-----------------------Here goes the rest of the page --------------------------------------------->

<!-- END PAGE CONTENT-->
                </div>
                <!-- END PAGE -->    
        </div>

<input type="hidden" id="dm_key" value="<?php echo $dm_key; ?>">

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
       
       <script>
                $(document).ready(function () {
					var idleState = false;
					var idleTimer = null;
					$('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
						clearTimeout(idleTimer);
						if (idleState == true) {
							
							var dm_key=$("#dm_key").val();
							var message_receiver = $(".select2me").select2("val");
							var message_subject = $(".message_subject").val();							
							var message_text = CKEDITOR.instances['message_text'].getData();
							
							if(message_receiver!='' || message_subject!='' || message_text!=''){
							
								$.ajax({
								   type: "POST",
								   url:  '<?php echo SITE_URL_ADMIN; ?>message_manager/save_drafts.php',
								   dataType: "text",
								   data: {dm_key: dm_key, message_receiver: message_receiver, message_subject: message_subject, message_text: message_text},
								   success: function(data){		
										//window.location.reload(true);
								   }								   		   		
							   });	
							}
										
						}
						idleState = false;
						idleTimer = setTimeout(function () {
							//$("body").css('background-color','#000');
							idleState = true; }, 1000);
					});
					$("body").trigger("mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick");
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
	////usleep(3000000);
	//echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'";</script>';
}
?>