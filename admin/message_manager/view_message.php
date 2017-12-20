<?php 
	
$id=$_REQUEST["id"];
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
}
else
	echo '<script>window.location="'.SITE_URL_ADMIN.'?mKey=inbox";</script>';


$sql = "select * from ".$db_suffix."user u
		Left Join ".$db_suffix."role r on r.role_id=u.role_id  where u.user_id = $message_sender";				
$query = mysqli_query($db, $sql);
if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);	
	
	$sender_role=$content->role_title;
	$sender_org=$content->user_org_name;
	
	if($content->role_id!='8'){
		$sender_name       = $content->user_first_name.' '.$content->user_last_name;
		$sender_email    = $content->user_email;
	}
	else{
		$sender_name       = "Administrator";
		$sender_email    = SITE_EMAIL;
	}
	if($content->user_photo!='')
		$sender_photo    = SITE_URL.'data/user/'.$content->user_photo;
	else
		$sender_photo    = SITE_URL_ADMIN."assets/admin/layout/img/avatar.png";	
}

$sql = "select * from ".$db_suffix."user where user_id = $message_receiver";				
$query = mysqli_query($db, $sql);
if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);	
	if($content->role_id!='8'){
		$receiver_name       = $content->user_first_name.' '.$content->user_last_name;
		$receiver_email    = $content->user_email;
	}
	else{
		$receiver_name       = "Administrator";
		$receiver_email    = SITE_EMAIL;
	}
	if($content->user_photo!='')
		$receiver_photo    = SITE_URL.'data/user/'.$content->user_photo;
	else
		$receiver_photo    = SITE_URL_ADMIN."assets/admin/layout/img/avatar.png";	
}

if($message_receiver==$_SESSION["user_id"])

	$query = mysqli_query($db, "UPDATE ".$db_suffix."message SET message_seen='1' WHERE message_id=$id");


?>

<!-----PAGE LEVEL CSS BEGIN--->

<link href="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/css/inbox.css" rel="stylesheet" type="text/css"/>


<!-----PAGE LEVEL CSS END--->


             <!-- BEGIN PAGE CONTENT-->
                                              
                        <div class="row inbox">
						<div class="col-md-2">
							<?php require_once("message_manager/message_sidebar_left.php"); ?>
						</div>
						<div class="col-md-10">
							<div class="inbox-content">
                            <div class="inbox-header inbox-view-header">
                            <h1 class="pull-left"><?php echo $message_subject; ?></h1>
                            
                        </div>
                        <div class="inbox-view-info">
                            <div class="row">
                                <div class="col-md-12">An: 
                                    <img src="<?php echo $receiver_photo ?>" class="img-circle" style="height: 30px; width: 30px;">
                                    <span class="bold">
                                   <?php echo $receiver_name ?></span>
                                    
                                </div>
                            </div>
                            <div class="row margin-top-10">
                                <div class="col-md-12">Von: 
                                    <img src="<?php echo $sender_photo ?>" class="img-circle" style="height: 30px; width: 30px;">
                                    <span class="bold">
                                    
                                   <?php echo $sender_name ?></span>
                                   
                                   <?php if($_SESSION["role_id"]==8) { ?>
                                    
                                    <span class="badge badge-warning"><?php echo $sender_role; ?></span>
                                     <?php if($sender_org!='') { ?>
                                     
                                    <span class="badge badge-warning"><?php echo $sender_org; ?></span>
                                    
                                   <?php } } ?> 
                                </div>
                            </div>
                            <div class="row margin-top-10">
                                <div class="col-md-7"><strong>Gesendet am:</strong> 
                                    <?php echo $message_created_time ?>
                                </div>
                                <div class="col-md-5 inbox-info-btn">
                                    <a href="<?php echo SITE_URL_ADMIN.'?mKey=sendmessage&id='.$id.'&keyword=reply'; ?>"><button class="btn blue reply-btn">
                                        <i class="fa fa-reply"></i> Antworten </button></a>
                                    <a href="<?php echo SITE_URL_ADMIN.'?mKey=sendmessage&id='.$id.'&keyword=forward'; ?>"><button class="btn blue reply-btn">
                                        <i class="fa fa-arrow-right"></i> Weiterleiten </button></a>    
                                </div>
                            </div>
                        </div>
                                <div class="inbox-view">
                                    <?php echo $message_text ?>
                                </div>
		
                                </div>
						</div>
					</div>
         
         
         
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
       
       	
    
   	 <!-----PAGE LEVEL SCRIPTS END--->    
        
        <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>