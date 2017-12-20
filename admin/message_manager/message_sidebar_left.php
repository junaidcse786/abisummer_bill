<?php 
 
$sql = "select message_id from ".$db_suffix."message where message_receiver = '".$_SESSION["user_id"]."' AND message_seen='0' AND receiver_delete='0'";				
$query = mysqli_query($db, $sql);
$unseen='';

if(mysqli_num_rows($query) > 0)
{
	$unseen='('.mysqli_num_rows($query).')';
}

$sql = "select dm_id from ".$db_suffix."draft_message where user_id = '".$_SESSION["user_id"]."'";				
$query = mysqli_query($db, $sql);
$drafts_num='';

if(mysqli_num_rows($query) > 0)
{
	$drafts_num='('.mysqli_num_rows($query).')';
}
 
 ?>                           
                            
                            <ul class="inbox-nav margin-bottom-10">
								<li class="compose-btn">
									<a href="<?php echo SITE_URL_ADMIN.'?mKey=sendmessage'; ?>" data-title="Compose" class="btn green">
									<i class="fa fa-edit"></i> Schreiben </a>
								</li>
								<li class="inbox <?php if($mKey=='inbox') echo 'active'; ?>">
									<a href="<?php echo SITE_URL_ADMIN.'?mKey=inbox'; ?>" class="btn" data-title="Inbox">
									Posteingang <?php echo $unseen; ?></a>
									<b></b>
								</li>
                                <li class="inbox <?php if($mKey=='drafts') echo 'active'; ?>">
									<a href="<?php echo SITE_URL_ADMIN.'?mKey=drafts'; ?>" class="btn" data-title="drafts">
									Entwürfe <?php echo $drafts_num; ?></a>
									<b></b>
								</li>
								<li class="sent <?php if($mKey=='sent') echo 'active'; ?>">
									<a class="btn" href="<?php echo SITE_URL_ADMIN.'?mKey=sent'; ?>" data-title="Sent">
									Gesendet </a>
									<b></b>
								</li>
                                
                                <?php if($_SESSION["role_id"]==15) { ?>
                                <li>
									<a class="btn" href="<?php echo SITE_URL_ADMIN; ?>" data-title="Dashboard">
									Dashboard </a>
									<b></b>
								</li>
                                
                                <?php } ?>
							</ul>