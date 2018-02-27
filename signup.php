<?php 

require_once('config/dbconnect.php');
	
$alert_message=""; $alert_box_show="hide"; $alert_type="success";

$bookings_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
	
$err_easy="has-error";

$travelers_first_name = "";
$travelers_last_name = "";
$bookings_code = "";
$travelers_email = "";
$travelers_status = 1;

$err=0;

$messages = array(
					'travelers_first_name' => array('status' => '', 'msg' => ''),
                    'travelers_last_name' => array('status' => '', 'msg' => ''),
                    'bookings_code' => array('status' => '', 'msg' => ''),
                    'travelers_email' => array('status' => '', 'msg' => ''),
					'travelers_status' => array('status' => '', 'msg' => ''),
                    'travelers_package' => array('status' => '', 'msg' => ''),
				);

if(isset($_POST['Submit']))
{	
	extract($_POST);
    
    $sql = "select b.* from ".$db_suffix."bookings b where bookings_code = '$bookings_code' limit 1";				
    $query = mysqli_query($db, $sql);

    if(mysqli_num_rows($query) > 0){
        
        $content     = mysqli_fetch_object($query);
        $bookings_ID =  $content->bookings_ID;
    }
    else{
        $messages["bookings_code"]["status"]=$err_easy;
		$messages["bookings_code"]["msg"]="Buchungscode ist nicht gültig";
		$err++;
    }
	
	if(empty($travelers_first_name))
	{
		$messages["travelers_first_name"]["status"]=$err_easy;
		$messages["travelers_first_name"]["msg"]="Vorname ist Pflichtfeld";
		$err++;		
	}
    
    if(empty($travelers_package))
	{
		$messages["travelers_package"]["status"]=$err_easy;
		$messages["travelers_package"]["msg"]="Reisepaket ist Pflichtfeld";
		$err++;		
	}
    
    if(empty($travelers_last_name))
	{
		$messages["travelers_last_name"]["status"]=$err_easy;
		$messages["travelers_last_name"]["msg"]="Nachname ist Pflichtfeld";
		$err++;		
	}
    
    if(mysqli_num_rows(mysqli_query($db, "SELECT travelers_ID from ".$db_suffix."travelers where bookings_ID = $bookings_ID AND travelers_email='$travelers_email'"))>0)
    {
        $messages["travelers_email"]["status"]=$err_easy;
        $messages["travelers_email"]["msg"]="Email schon existiert";
        $err++;		
    }
    
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

<!DOCTYPE html>

<html>

<head>
    <?php require_once('admin/header.php'); ?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">

    <!-- BEGIN HEADER -->

    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="http://abisummer.de">
                <img src="http://rechner.webloungeonline.com/images/rsz_1abisummer-logo-internet.png" alt="logo" class="logo-default">
                </a>
                <div class="menu-toggler sidebar-toggler hide">
                </div>
            </div>
        </div>
        <!-- END HEADER INNER -->
    </div>

    <!-- END HEADER -->

    <div class="clearfix"></div>

    <!-- BEGIN CONTAINER -->

    <div class="page-container">

        <!-- BEGIN SIDEBAR -->


        <div class="page-sidebar-wrapper">
            <div class="page-sidebar navbar-collapse collapse">
            </div>
        </div>


        <!-- END SIDEBAR -->

        <!-- BEGIN PAGE -->

        <div class="page-content-wrapper">

            <div class="page-content">

                <!-- BEGIN PAGE content-->


                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

                <h3 class="page-title">
                    Reisender registrieren
                </h3>
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo SITE_URL; ?>">Startseite</a>
                        </li>
                        <li>
                            <i class="fa fa-angle-right"></i>
                            <a href="<?php echo SITE_URL.'signup.php'; ?>">Mit Buchungscode anmelden</a>
                        </li>
                        <li>
                            <i class="fa fa-angle-right"></i>
                            <a href="<?php echo SITE_URL; ?>">Preis checken</a>
                        </li>
                    </ul>
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


                                    <form action="<?php echo str_replace('?s_factor=1', '', $_SERVER['REQUEST_URI']);?>" class="form-horizontal" method="post" enctype="multipart/form-data">

                                        <div class="form-group <?php echo $messages["travelers_first_name"]["status"] ?>">
                                            <label class="control-label col-md-3" for="travelers_first_name">Vorname <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input required type="text" placeholder="" class="form-control" name="travelers_first_name" value="<?php echo $travelers_first_name;?>" />
                                                <span for="travelers_first_name" class="help-block"><?php echo $messages["travelers_first_name"]["msg"] ?></span>
                                            </div>
                                        </div>

                                        <div class="form-group <?php echo $messages["travelers_last_name"]["status"] ?>">
                                            <label class="control-label col-md-3" for="travelers_last_name">Nachname <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" required placeholder="" class="form-control" name="travelers_last_name" value="<?php echo $travelers_last_name;?>" />
                                                <span for="travelers_last_name" class="help-block"><?php echo $messages["travelers_last_name"]["msg"] ?></span>
                                            </div>
                                        </div>

                                        <div class="form-group <?php echo $messages["travelers_email"]["status"] ?>">
                                            <label class="control-label col-md-3" for="travelers_email">Email-adresse <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="email" required placeholder="" class="form-control" name="travelers_email" value="<?php echo $travelers_email;?>" />
                                                <span for="travelers_email" class="help-block"><?php echo $messages["travelers_email"]["msg"] ?></span>
                                            </div>
                                        </div>

                                        <div class="form-group <?php echo $messages["bookings_code"]["status"] ?>">
                                            <label class="control-label col-md-3" for="bookings_code">Buchungscode <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" required placeholder="" class="form-control" name="bookings_code" value="<?php echo $bookings_code;?>" />
                                                <span for="bookings_code" class="help-block"><?php echo $messages["bookings_code"]["msg"] ?></span>
                                            </div>
                                        </div>

                                        <div class="form-group <?php echo $messages["travelers_package"]["status"] ?>">
                                            <label for="parent" class="control-label col-md-3">Reisepaket <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <select required class="form-control" name="travelers_package"> 
                                                </select>
                                                <span for="travelers_package" class="help-block"><?php echo $messages["travelers_package"]["msg"] ?></span>
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

        <?php require_once('admin/footer.php'); ?>

        <!-- END FOOTER -->

        <!-- BEGIN CORE PLUGINS -->


        <?php require_once('admin/scripts.php'); ?>

        <!-- END CORE PLUGINS -->


        <!-----PAGE LEVEL SCRIPTS BEGIN--->

        <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>

        <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/ckeditor/ckeditor.js"></script>

        <script>
            $('input[name="bookings_code"]').on('blur', function() {

                var bookings_code = $(this).val();

                if (bookings_code != "" && bookings_code.length>=23) {

                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL?>get_package.php',
                        dataType: "text",
                        data: {
                            bookings_code: bookings_code
                        },
                        success: function(data) {
                            if (data) {
                                
                                $('span[for="bookings_code"]').html('');                                
                                $('select[name="travelers_package"]').val(null).trigger("change");
                                $('select[name="travelers_package"]').html(data);
                            }
                            else{
                                $('span[for="bookings_code"]').html('<b style="color:red;">Ungültig</b>');
                                $('select[name="travelers_package"] option').remove();
                            }
                        },
                    });
                }
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
	echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'?s_factor=1";</script>';
}
?>
