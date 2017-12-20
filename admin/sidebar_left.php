
<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu <?php if($_SESSION["role_id"]==15) echo 'page-sidebar-menu-closed'; ?>" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				
				<li class="start <?php if(empty($pKey) && empty($mKey)) echo "active"; ?>">
                                        <a href="<?php echo SITE_URL_ADMIN; ?>">
                                        <i class="fa fa-home"></i> 
                                        <span class="title">Dashboard</span>
                                        <?php if(empty($pKey) && empty($mKey)) echo '<span class="selected"></span>'; ?>
                                        </a>
                </li>
                                
                                
                        <?php
						
						
						$sql = "SELECT * FROM ".$db_suffix."module WHERE module_status = 1 AND module_id !='3' AND module_menu='1' AND module_key IN (".$modules_keys_for_sql.") ORDER BY module_priority";

						$module_query = mysqli_query($db,$sql);

						unset($sql);

						while($row = mysqli_fetch_object($module_query))

						{ 
						
							$active_module_icon=$row->module_image;
							
							if(!is_array($pages[$row->module_key])){
							
								$active='';
								
								if($row->module_key==$mKey){
										
									$active="active";	
									$active_module_name=$row->module_name;
									$active_module_icon=$row->module_image;						
								}
							
							
								echo'<li class="'.$active.'">
									<a href="'.SITE_URL_ADMIN.'?mKey='.$row->module_key.'">
									<i class="'.$active_module_icon.'"></i>
									<span class="title">'.$row->module_name.'</span>
									</a>
								</li>';  
							}
							else {
								
								$active="";
								
								$active1='<span class="title"></span><span class="arrow"></span>';
															
								if($mKey==$row->module_key){ 
								
									$active='active';
									
									$active1='<span class="arrow open"></span>';
									
									$active_module_name=$row->module_name;
									
									$active_module_icon=$row->module_image;	 
								}
								
								echo '<li class="'.$active.'">';
									
								echo '<a href="javascript:;">
								
								<i class="'.$active_module_icon.'"></i> <span class="title">'.$row->module_name.'</span>
								
								'.$active1.'</a>';
								
								echo '<ul class="sub-menu">';
	
								foreach($menus[$row->module_key] as $key => $value)
	
								{
									$active="";
									
									if($key==$pKey)
									
										$active='class="active"';										
									
									echo '<li '.$active.'><a href="'.SITE_URL_ADMIN.'?mKey='.$row->module_key.'&pKey='.$key.'"> '.$value.'</a></li>';
	
								}
	
								echo "</ul>";
								
							}
						 }

						if($_SESSION["role_id"]=='15'){
						
							$active=''; 
							
							if($mKey=='inbox' || $mKey=='sent' || $mKey=='sendmessage' || $mKey=='drafts')
							
								$active='class="active"'; 
							
							echo '<li '.$active.'>
									<a href="'.SITE_URL_ADMIN.'?mKey=inbox">
									<i class="fa fa-envelope-o"></i> 
											<span class="title">Messages</span>														
									</a>
								  </li>';
						}
							  
							  
						if($_SESSION["role_id"]=='8'){
							
							$active="";
								
							$active1='<span class="title"></span><span class="arrow"></span>';
														
							if($mKey=='setup'){ 
							
								$active='active'; 
								
								$active1='<span class="arrow open"></span>';
								
								$active_module_name="Setup";
									
								$active_module_icon="fa fa-cogs";
							}
							
							echo '<li class="'.$active.'">';
								
							echo '<a href="javascript:;">
							
							<i class="fa fa-cogs"></i> <span class="title">Setup</span>
							
							'.$active1.'</a>';
							
							echo '<ul class="sub-menu">';

							foreach($menus["setup"] as $key => $value)

							{
								$active="";
								
								if($key==$pKey)
								
									$active='class="active"';									
								
								echo '<li '.$active.'><a href="'.SITE_URL_ADMIN.'?mKey=setup&pKey='.$key.'"><i class="font-red-thunderbird fa fa-warning (alias)"></i> '.$value.'</a></li>';

							}

							echo "</ul>";	
							
							
						}
						
					    ?>
                                
            </ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	