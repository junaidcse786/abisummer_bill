
		<?php 
		
			$cat_sql = "SELECT * FROM ".$db_suffix."logo";
			$cat_query = mysqli_query($db,$cat_sql);
			while($row = mysqli_fetch_object($cat_query))
		   {
			$logo=$row->banner_image;
		   }
		   
		   $dd = mysqli_query($db, "select user_photo, user_first_name, user_last_name from ".$db_suffix."user where user_id='".$_SESSION["user_id"]."'");

			if(mysqli_num_rows($dd)>0){					
			
				$result1=mysqli_fetch_array($dd);
				
				$user_avatar_login = $result1["user_photo"];
				
				$user_full_name_login = $result1["user_first_name"].' '.$result1["user_last_name"];
			}
			
			if($mKey=='viewmessage'){
			
				$id=$_REQUEST["id"];
				$sql = "select message_receiver from ".$db_suffix."message where message_id = $id AND (message_sender='".$_SESSION["user_id"]."' OR message_receiver='".$_SESSION["user_id"]."')limit 1";				
				$query = mysqli_query($db, $sql);
				
				if(mysqli_num_rows($query) > 0)
				{
					$content     = mysqli_fetch_object($query);
					$message_receiver    = $content->message_receiver;
					
					if($message_receiver==$_SESSION["user_id"])

						$query = mysqli_query($db, "UPDATE ".$db_suffix."message SET message_seen='1' WHERE message_id=$id");
						
				}			
			}
			
			$sql = "select message_id from ".$db_suffix."message where message_receiver = '".$_SESSION["user_id"]."' AND message_seen='0' AND receiver_delete='0'";				
			$query = mysqli_query($db, $sql);
			$unseen='';
			
			if(mysqli_num_rows($query) > 0)
			{
				$unseen_msg_num=mysqli_num_rows($query);
				$unseen='<span class="badge badge-danger">'.mysqli_num_rows($query).'</span>';
			}
			
		?>



<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="<?php echo SITE_URL_ADMIN; ?>">
			<img src="<?php echo SITE_URL.'images/'.$logo; ?>" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
            
            	<?php if($unseen!='') { ?>
                
                	<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
					<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">

						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="icon-envelope-open"></i>
						<?php echo $unseen; ?>
						</a>
						<ul class="dropdown-menu">
							<li class="external">
								<h3>Sie haben <span class="bold"><?php echo $unseen_msg_num; ?> neue</span> Nachrichte(n)</h3>
								<!--<a href="<?php echo SITE_URL_ADMIN.'?mKey=inbox'; ?>">Alle anzeigen</a>-->
							</li>
							<li>
								<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                
                                <?php 
								
								$sql = "select * from ".$db_suffix."message m
										Left Join ".$db_suffix."user u on m.message_sender=u.user_id where m.message_receiver = '".$_SESSION["user_id"]."' AND m.receiver_delete='0' AND m.message_seen='0' ORDER BY m.message_created_time DESC, m.message_seen ASC";
								$news_query = mysqli_query($db,$sql);
								
								while($row = mysqli_fetch_object($news_query))
								{
									if($row->user_photo!='')	
									
										$sender_photo_src=SITE_URL.'data/user/'.$row->user_photo;
										
									else
									
										$sender_photo_src=SITE_URL_ADMIN.'assets/admin/layout/img/avatar.png';									
								?>
									<li>
										<a href="<?php echo SITE_URL_ADMIN.'?mKey=viewmessage&id='.$row->message_id;?>">
										<span class="photo">
										<img src="<?php echo $sender_photo_src; ?>" class="img-circle" alt="">
										</span>
										<span class="subject">
										<span class="from">
										<?php 
								  
										  if($row->role_id=='1')
										  
											echo 'Administrator';
											
										else
										
											echo $row->user_first_name.' '.$row->user_last_name;	
										  
										  ?>
                                        </span>
										<span class="time"><?php 
								  
										  if(date('d-m-Y')==date('d-m-Y', strtotime($row->message_created_time)))
										  
											echo date('H:i', strtotime($row->message_created_time));
											
										else
										
											echo date('d-m-Y H:i', strtotime($row->message_created_time));	
										  
										  ?>
                                        </span>
										</span>
										<span class="message">
										<?php 
										
										echo '<strong>'.substr($row->message_subject,0,30).' :</strong> '.substr(strip_tags($row->message_text),0,40).'...';
										
										?>
                                        </span>
										</a>
									</li>
                                    
                                <?php } ?>
								
                                </ul>
							</li>
						</ul>
					</li>
					<!-- END INBOX DROPDOWN -->
					<li class="separator hide">
					</li>
                    
                    <?php } ?>
				
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					
					<?php 
					
					if($user_avatar_login!='')

						$src=SITE_URL.'data/user/'.$user_avatar_login;
					
					else
					
						$src=SITE_URL_ADMIN.'assets/admin/layout/img/avatar.png'; 
					
					?>
					<img class="img-circle" width="29" height="29" alt="" src="<?php echo $src;?>"/>
                                       
					<span class="username username-hide-on-mobile">
					<?php echo $user_full_name_login ; ?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?php echo SITE_URL_ADMIN.'?mKey=myaccount'; ?>">
							<i class="icon-user"></i> Mein Profil </a>
						</li>
                        <!--<li>
							<a href="<?php echo SITE_URL_ADMIN.'?mKey=inbox'; ?>">
							<i class="icon-envelope-open"></i> Postfach <?php echo $unseen; ?></a>
						</li>
						<?php if($_SESSION["role_id"]==8) { ?>
						<li>
							<a target="_blank" href="<?php echo SITE_URL; ?>">
							<i class="fa fa-desktop"></i> Go to Site </a>
						</li>
						<?php } ?>-->
                        <li>
							<a href="<?php echo SITE_URL_ADMIN; ?>/logout.php">
							<i class="icon-key"></i> Abmelden </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- BEGIN QUICK SIDEBAR TOGGLER -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<!--<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="javascript:;" class="dropdown-toggle">
					<i class="icon-logout"></i>
					</a>
				</li>-->
				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>

        