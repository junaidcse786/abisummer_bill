<?php 
	
$title1='Inbox';


?>

<!-----PAGE LEVEL CSS BEGIN--->

<link href="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/css/inbox.css" rel="stylesheet" type="text/css"/>

<!-----PAGE LEVEL CSS END--->


                                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                        <h3 class="page-title">
                                                Posteingang <small>Nachrichten von Benutzer</small>
                                        </h3>
                               
                     
                        <!-- END PAGE HEADER-->
                        <!-- BEGIN PAGE CONTENT-->
                                              
                        <div class="row inbox">
						<div class="col-md-2">
							<?php require_once("message_sidebar_left.php"); ?>
						</div>
						<div class="col-md-10">
							<div class="inbox-content">
                            
                            <?php 
	
$sql = "select message_id from ".$db_suffix."message m
		Left Join ".$db_suffix."user u on m.message_sender=u.user_id where m.message_receiver = '".$_SESSION["user_id"]."' AND m.receiver_delete='0' ORDER BY m.message_created_time DESC, m.message_seen ASC";
$news_query = mysqli_query($db,$sql);
$num_messages=mysqli_num_rows($news_query);

if(!isset($_SESSION["start_show"]))
	
	$start_show=1;
	
else

	$start_show=$_SESSION["start_show"];	
	
if($start_show>$num_messages)

$start_show-=PER_PAGE_MSG;
	
if($num_messages<PER_PAGE_MSG)
	
	$end_show=$num_messages;
	
else

	$end_show=$start_show+PER_PAGE_MSG-1;

$next_link=''; $prev_link=''; 


if($end_show>=$num_messages)

	{ $next_link='disabled'; $end_show=$num_messages;}
	
if($start_show<=PER_PAGE_MSG)

	$prev_link='disabled';	
	
if($num_messages==0)

	$start_show=1;		

$sql = "select * from ".$db_suffix."message m
		Left Join ".$db_suffix."user u on m.message_sender=u.user_id where m.message_receiver = '".$_SESSION["user_id"]."' AND m.receiver_delete='0' ORDER BY m.message_created_time DESC, m.message_seen ASC LIMIT ".($start_show-1).", ".PER_PAGE_MSG;
$news_query = mysqli_query($db,$sql);

if($num_messages==0)

	$start_show=0;
	
?>
                            
                            <div class="row">
                                <div class="col-md-12">
                                
                                <table class="table table-striped table-advance table-hover">
<thead>
                                <tr>
                                    <th colspan="2">
                                        <input type="checkbox" class="mail-checkbox mail-group-checkbox" />
                                        <div class="btn-group">
                                            <a class="btn btn-sm blue dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                            Optionen <i class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" data-toggle="modal" data-target="#confirmation_all1">
                                                    <i class="fa fa-pencil"></i> Als gelesen markieren </a>
                                                </li>
                                                <li>
                                                    <a href="#" data-toggle="modal" data-target="#confirmation_all_ungelesen">
                                                    <i class="fa fa-pencil"></i> Als ungelesen markieren </a>
                                                </li>
                                                <li>
                                                    <a href="#" data-toggle="modal" data-target="#confirmation_all">
                                                    <i class="fa fa-trash-o"></i> Löschen </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </th>
                                    <th class="pagination-control" colspan="2">
                                        <span class="pagination-info">
                                        <?php echo $start_show.' - '.$end_show.' von '.$num_messages; ?></span>
                                        <a <?php echo $prev_link; ?> class="btn btn-sm blue prev-page">
                                        <i class="fa fa-angle-left"></i>
                                        </a>
                                        <a <?php echo $next_link; ?> class="btn btn-sm blue next-page">
                                        <i class="fa fa-angle-right"></i>
                                        </a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                
                                	<?php 
										 while($row = mysqli_fetch_object($news_query))
										{
											$active='';
											if($row->message_seen==0)							  
												$active='class="unread" style="color:black;"'; 
								
									?>
									
									
									
									<tr <?php echo $active;?> data-href="<?php echo SITE_URL_ADMIN.'?mKey=viewmessage&id='.$row->message_id;?>">
										<td class="inbox-small-cells">
											<input value="<?php echo $row->message_id;?>" type="checkbox" class="mail-checkbox" />
										</td>
										<td class="view-message hidden-xs message-show">
											 <?php 
								  
											  if($row->role_id=='1')
											  
												echo 'Administrator';
												
											else
											
												echo $row->user_first_name.' '.$row->user_last_name;	
											  
											  ?>
										</td>
										<td class="view-message text-left message-show"><?php 
										
										$span='';
										
										if($row->message_report==1)							  
												
												$span=' <span class="badge badge-danger">Report</span>';
										
										echo substr($row->message_subject,0,30).' : '.substr(strip_tags($row->message_text),0,50).' '.$span;
										
										
										
										?></td>
										<td class="view-message text-left message-show">
											 <?php 
								  
											  if(date('d-m-Y')==date('d-m-Y', strtotime($row->message_created_time)))
											  
												echo date('H:i', strtotime($row->message_created_time));
												
											else
											
												echo date('d-m-Y H:i', strtotime($row->message_created_time));	
											  
											  ?>
										</td>
									</tr>
                                
                                <?php } ?>
                                </tbody>
                                </table>
                                   
                                </div>
                             </div>
                                                </div>
                                            </div>
                                        </div>
         
         
         
<!-----MODALS FOR THIS PAGE START ---->


         <div class="modal fade" id="confirmation_all">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                     <h4 class="modal-title">Bestätigung</h4>
                  </div>
                  <div class="modal-body">
                        <span class="danger"><strong class="font-red">Hinweis!</strong> Möchten Sie die Nachrichte(n) wirklich löschen?</span>         			</div>
                  <div class="modal-footer">
                     <button id="delete_button" type="button" class="btn green">Bestätigen</button>
                     <button type="button" class="btn default" data-dismiss="modal">Schließen</button>
                  </div>
               </div>
               <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
         </div>
         
         
         <div class="modal fade" id="confirmation_all1">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                     <h4 class="modal-title">Bestätigung</h4>
                  </div>
                  <div class="modal-body">
                        <span class="danger"><strong class="font-red">Hinweis!</strong> Möchten Sie die Nachrichte(n) wirklich als "gelesen" markieren?</span>         			</div>
                  <div class="modal-footer">
                     <button id="delete_button" type="button" class="btn green">Bestätigen</button>
                     <button type="button" class="btn default" data-dismiss="modal">Schließen</button>
                  </div>
               </div>
               <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
         </div>         
         
         <div class="modal fade" id="confirmation_all_ungelesen">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                     <h4 class="modal-title">Bestätigung</h4>
                  </div>
                  <div class="modal-body">
                        <span class="danger"><strong class="font-red">Hinweis!</strong> Möchten Sie die Nachrichte(n) wirklich als "ungelesen" markieren?</span>         			</div>
                  <div class="modal-footer">
                     <button id="delete_button" type="button" class="btn green">Bestätigen</button>
                     <button type="button" class="btn default" data-dismiss="modal">Schließen</button>
                  </div>
               </div>
               <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
         </div>
                        <!-- /.modal -->

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
       
       <script>
	   
                $('.next-page').click(function() { 

					$.ajax({
							   type: "POST",
							   url:  '<?php echo SITE_URL?>AJAX_session_pag.php?id=<?php echo $start_show+PER_PAGE_MSG; ?>',
							   success: function(data){ window.location.reload(true); }
							  
					});
				});
				
				$('.message-show').click(function() { 

					window.location.href= $(this).closest("tr").data('href');
				});
				
				
				$('.prev-page').click(function() { 

					$.ajax({
							   type: "POST",
							   url:  '<?php echo SITE_URL?>AJAX_session_pag.php?id=<?php echo $start_show-PER_PAGE_MSG; ?>',
							   success: function(data){ window.location.reload(true); }
							  
					});
				});
				
				
				
				jQuery('body').on('change', '.mail-group-checkbox', function () {
					var set = jQuery('.mail-checkbox');
					var checked = jQuery(this).is(":checked");
					jQuery(set).each(function () {
						$(this).attr("checked", checked);
					});
					jQuery.uniform.update(set);
				});
				
				
				$('#confirmation_all').on('show.bs.modal', function(e) {
					 
					 $(this).find('#delete_button').on('click', function(e) { 
					 
					 var id='';
					 
					 $('input:checkbox[class=mail-checkbox]:checked').each(function(){
						 
						id=id+$(this).val()+',';
					 })
					 
					 	$.ajax({
								   type: "POST",
								   url:  '<?php echo SITE_URL_ADMIN.'message_manager/'; ?>delete_message.php',
								   dataType: "text",
								   data: {id: id},
								   success: function(data){		
										window.location.reload(true);
								   }								   		   		
						      });
						
					});
		        });
				
				$('#confirmation_all1').on('show.bs.modal', function(e) {
					 
					 $(this).find('#delete_button').on('click', function(e) { 
					 
					 var id='';
					 
					 $('input:checkbox[class=mail-checkbox]:checked').each(function(){
						 
						id=id+$(this).val()+',';
					 })
					 
					 	$.ajax({
								   type: "POST",
								   url:  '<?php echo SITE_URL; ?>AJAX_read_message.php',
								   dataType: "text",
								   data: {id: id},
								   success: function(data){		
										window.location.reload(true);
								   }								   		   		
						      });
						
					});
		        });
				
				$('#confirmation_all_ungelesen').on('show.bs.modal', function(e) {
					 
					 $(this).find('#delete_button').on('click', function(e) { 
					 
					 var id='';
					 
					 $('input:checkbox[class=mail-checkbox]:checked').each(function(){
						 
						id=id+$(this).val()+',';
					 })
					 
					 	$.ajax({
								   type: "POST",
								   url:  '<?php echo SITE_URL_ADMIN; ?>message_manager/unread_message.php',
								   dataType: "text",
								   data: {id: id},
								   success: function(data){		
										window.location.reload(true);
								   }								   		   		
						      });
						
					});
		        });
				
	</script>	
    
    <!-----PAGE LEVEL SCRIPTS END--->    
        
        <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>