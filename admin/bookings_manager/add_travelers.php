<?php 
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";

$bookings_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
	
$err_easy="has-error";


$sql = "select bookings_summary from ".$db_suffix."bookings WHERE bookings_ID = $bookings_ID limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);
    
    $bookings_summary= json_decode($content->bookings_summary, true);
    $indiv_cost_array = $bookings_summary["indiv_cost_array"];
    $rooms_cost_details = $bookings_summary["rooms_cost_details"];
    $meals_cost_details = $bookings_summary["meals_cost_details"];
}

foreach($rooms_cost_details as $key => $rooms){
    
    $ordered_amount = $rooms["rooms_persons_to_fit"];
    
    $allocated_amount = mysqli_num_rows(mysqli_query($db, "SELECT travelers_ID from ".$db_suffix."travelers where bookings_ID = $bookings_ID AND travelers_package like '%$key%'"));
    
    $rooms_cost_details[$key]["amount_left"] = $ordered_amount - $allocated_amount;
    
}

foreach($meals_cost_details as $key => $meals){
    
    $ordered_amount = $meals["meals_ordered"];
    
    $allocated_amount = mysqli_num_rows(mysqli_query($db, "SELECT travelers_ID from ".$db_suffix."travelers where bookings_ID = $bookings_ID AND travelers_package like '%$key%'"));
    
    $meals_cost_details[$key]["amount_left"] = $ordered_amount - $allocated_amount;
    
}

$indiv_cost_array_temp = $indiv_cost_array;

foreach($indiv_cost_array_temp as $key => $value){
    
    $room_title = explode(" + ", $key)[0];
    $meal_title = explode(" + ", $key)[1];
    
    if($rooms_cost_details[$room_title]["amount_left"]<=0)
        
        unset($indiv_cost_array[$key]); 
    
    else if($meals_cost_details[$meal_title]["amount_left"]<=0)
        
        unset($indiv_cost_array[$key]); 
}

$travelers_first_name = "";
$travelers_last_name = "";
$travelers_email = "";
$travelers_status = 1;

$err=0;

$messages = array(
					'travelers_first_name' => array('status' => '', 'msg' => ''),
                    'travelers_last_name' => array('status' => '', 'msg' => ''),
                    'travelers_email' => array('status' => '', 'msg' => ''),
					'travelers_status' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
	
	if(empty($travelers_first_name))
	{
		$messages["travelers_first_name"]["status"]=$err_easy;
		$messages["travelers_first_name"]["msg"]="Vorname ist Pflichtfeld";
		$err++;		
	}
    
    if(empty($travelers_last_name))
	{
		$messages["travelers_last_name"]["status"]=$err_easy;
		$messages["travelers_last_name"]["msg"]="Nachname ist Pflichtfeld";
		$err++;		
	}
    
  /*  if(mysqli_num_rows(mysqli_query($db, "SELECT travelers_ID from ".$db_suffix."travelers where bookings_ID = $bookings_ID AND travelers_email='$travelers_email'"))>0)
    {
        $messages["travelers_email"]["status"]=$err_easy;
        $messages["travelers_email"]["msg"]="Email schon existiert";
        $err++;		
    }*/
    
    if($err == 0)
	{
		$sql = "INSERT INTO ".$db_suffix."travelers SET travelers_last_name='$travelers_last_name',travelers_first_name='$travelers_first_name',travelers_status='$travelers_status',travelers_email='$travelers_email', bookings_ID='$bookings_ID', travelers_package='$travelers_package'";
		if(mysqli_query($db,$sql))
		{		
			$alert_message="Daten erfolgreich gespeichert";		
			$alert_box_show="show";
			$alert_type="success";
			
			$travelers_first_name = "";
			
		}else{
			$alert_box_show="show";
			$alert_type="danger";
			$alert_message="Database encountered some error while inserting";
		}
	}
	else
	{
		$alert_box_show="show";
		$alert_type="danger";
		$alert_message="Bitte korrigiere diese Felder";
		
	}
}

if(!isset($_POST["Submit"]) && isset($_GET["s_factor"]))
{
	$alert_message="Daten erfolgreich gespeichert";		
	$alert_box_show="show";
	$alert_type="success";
}


?>

<!-----PAGE LEVEL CSS BEGIN--->

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

<!-----PAGE LEVEL CSS END--->



<!-- BEGIN PAGEtravelers_first_name& BREADCRUMB-->
<h3 class="page-travelers_first_name">
    Reisender zum Buchung einfügen
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
            <a href="#">
                <?php echo $active_module_name; ?>
            </a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=editbookings&id='.$bookings_ID; ?>">Buchungsinformation</a>
        </li>
    </ul>
    <!-- END PAGEtravelers_first_name& BREADCRUMB-->
</div>
<!-- END PAGE HEADER-->


<!--------------------------BEGIN PAGE CONTENT------------------------->

<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet box grey-cascade">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-reorder"></i>Felder mit Sterchen müssen ausgefüllt werden <strong>*</strong></div>
            </div>
            <div class="portlet-body form">

                <div class="form-body">

                    <div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <?php echo $alert_message; ?>
                    </div>


                    <form action="<?php echo str_replace('&s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">

                        <div class="form-group <?php echo $messages[" travelers_first_name "]["status "] ?>">
                            <label class="control-label col-md-3" for="travelers_first_name">Vorname <span class="required">*</span></label>
                            <div class="col-md-4">
                                <input required type="text" placeholder="" class="form-control" name="travelers_first_name" value="<?php echo $travelers_first_name;?>" />
                                <span for="travelers_first_name" class="help-block"><?php echo $messages["travelers_first_name"]["msg"] ?></span>
                            </div>
                        </div>

                        <div class="form-group <?php echo $messages[" travelers_last_name "]["status "] ?>">
                            <label class="control-label col-md-3" for="travelers_last_name">Nachname <span class="required">*</span></label>
                            <div class="col-md-4">
                                <input type="text" required placeholder="" class="form-control" name="travelers_last_name" value="<?php echo $travelers_last_name;?>" />
                                <span for="travelers_last_name" class="help-block"><?php echo $messages["travelers_last_name"]["msg"] ?></span>
                            </div>
                        </div>

                        <div class="form-group <?php echo $messages[" travelers_email "]["status "] ?>">
                            <label class="control-label col-md-3" for="travelers_email">Email-adresse <span class="required">*</span></label>
                            <div class="col-md-4">
                                <input type="email" required placeholder="" class="form-control" name="travelers_email" value="<?php echo $travelers_email;?>" />
                                <span for="travelers_email" class="help-block"><?php echo $messages["travelers_email"]["msg"] ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="parent" class="control-label col-md-3">Reisepaket <span class="required">*</span></label>
                            <div class="col-md-4">
                                <select required class="form-control select2me" data-placeholder="Auswählen" tabindex="0" name="travelers_package">                                                                              
                                       <?php
									   
										foreach($indiv_cost_array as $key => $value )
										{	
											
                                                $selected=($parent_obj->locations_ID == $locations_ID)? 'selected="selected"': '';
                                            
												echo '<option '.$selected.' value="'.$key.'">'.$key.' ()</option>';
									
										}
                                        ?>
                                       
                                    </select>
                            </div>
                        </div>

                        <div class="form-group last">
                            <label for="travelers_status" class="control-label col-md-3">Status</label>
                            <div class="col-md-2">
                                <select class="form-control" name="travelers_status">
                                        <option <?php if($travelers_status==1) echo 'selected="selected"'; ?> value="1">aktiv</option>
                                        <option <?php if($travelers_status==0) echo 'selected="selected"'; ?> value="0">inaktiv</option>
                                     </select>
                            </div>
                        </div>

                        <div class="form-actions fluid">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" name="Submit" class="btn green">Submit</button>
                                <button type="reset" class="btn default">Abbrechen</button>
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

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/ckeditor/ckeditor.js"></script>

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
