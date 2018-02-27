<?php 

require_once('config/dbconnect.php');

$bookings_code = isset($_REQUEST['bookings_code']) ? $_REQUEST['bookings_code']: 0;
$travelers_last_name = isset($_REQUEST['travelers_last_name']) ? $_REQUEST['travelers_last_name']: 0;
$sql = "select b.* from ".$db_suffix."bookings b LEFT JOIN ".$db_suffix."travelers t ON t.bookings_ID=b.bookings_ID where bookings_code = '$bookings_code' AND t.travelers_last_name='$travelers_last_name' limit 1";				
$query = mysqli_query($db, $sql);

if(mysqli_num_rows($query) > 0)
{
	$content     = mysqli_fetch_object($query);
    $bookings_code = $content->bookings_code;
    $num_traveler       = $content->bookings_num_traveler;
    $locations_name       = $content->locations_name;
	$hotels_name    = $content->hotels_name;
    $date_from    = $content->bookings_check_in_date;
    $bookings_check_out_date    = $content->bookings_check_out_date;
    $bookings_notes    = $content->bookings_notes;
    
    $bookings_ID =  $content->bookings_ID;
    
    $date1 = new DateTime($date_from);
    $date2 = new DateTime($bookings_check_out_date);
    $num_nights = $date1->diff($date2)->format("%a");
    
    $bookings_summary=json_decode($content->bookings_summary, true);	
}

if(!empty($bookings_summary["abfahrsort"]))
    
    $abfahrsort=$bookings_summary["abfahrsort"];

else
    
    $abfahrsort="";

$rooms_cost=$bookings_summary["rooms_cost"]; 

$meals_cost=$bookings_summary["meals_cost"]; 

$journey_cost=$bookings_summary["journey_cost"]; 

$promoter_provision=$bookings_summary["promoter_provision"]; 

$office_profit=$bookings_summary["office_profit"]; 

$MwSt=$bookings_summary["MwSt"]; 

$rooms_cost_details = $bookings_summary["rooms_cost_details"]; 

$meals_cost_details=$bookings_summary["meals_cost_details"];

$indiv_cost_array=$bookings_summary["indiv_cost_array"];

if(!empty($bookings_summary["discount_applied"])){
    
    $discount_applied=$bookings_summary["discount_applied"];
    
    $without_discount=$bookings_summary["without_discount"];
}

if(!empty($bookings_summary["journey_title"]))
    
    $journey_title=$bookings_summary["journey_title"];

setlocale(LC_MONETARY, 'de_DE');

$colors_to_pick_array=array("blue-ebonyclay", "grey-gallery");


    
$actual_total_price = $meals_cost + $rooms_cost + $journey_cost + $office_profit + $promoter_provision + $MwSt;


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

                <link rel="stylesheet" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />

                <style>
                    .dashboard-stat-special .dashboard-stat {
                        padding: 10px 0px 35px 0px;
                    }

                    .dashboard-stat {
                        padding: 5px 0px 15px 0px;
                    }

                    .desc {
                        padding-top: 8px;
                    }

                </style>

                <h3 class="page-title">
                    <?php
    $alert_message=""; $alert_box_show="hide"; $alert_type="success";

    echo 'Buchungsinformation <small>Buchungsinformation</small>';

?>
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


                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box green-seagreen">
                            <div class="portlet-title">
                                <div class="caption"><i class="fa fa-reorder"></i>Info Box</div>
                            </div>
                            <div class="portlet-body">

                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat purple-plum">
                                            <div class="visual">
                                                <i class="fa fa-globe"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo $locations_name; ?>
                                                </div>
                                                <div class="desc">
                                                    Reisenderzahl:
                                                    <?php echo $num_traveler; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat blue">
                                            <div class="visual">
                                                <i class="fa fa-building"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo $date_from; ?>
                                                </div>
                                                <div class="desc">
                                                    Hotel:
                                                    <?php echo $hotels_name; ?> <br/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat green">
                                            <div class="visual">
                                                <i class="fa fa-bus"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo $num_nights; ?> N&auml;chte
                                                </div>
                                                <div class="desc">
                                                    Check-out:
                                                    <?php echo $bookings_check_out_date; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br/>

                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat red-thunderbird">
                                            <div class="visual">
                                                <i class="fa fa-plus"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($actual_total_price, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Reisekosten
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat green-seagreen">
                                            <div class="visual">
                                                <i class="fa fa-plane"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Anreisekosten
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat yellow-gold">
                                            <div class="visual">
                                                <i class="fa fa-cutlery"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($meals_cost, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Verpflegung
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat red-intense">
                                            <div class="visual">
                                                <i class="fa fa-bed"></i>
                                            </div>
                                            <div class="details">
                                                <div class="number">
                                                    <?php echo number_format($rooms_cost+$office_profit, 2, ',', '.'); ?>&euro;
                                                </div>
                                                <div class="desc">
                                                    Gesamte Zimmerkosten
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br/>
                            </div>

                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box yellow">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-euro"></i>Preis Details
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th> # </th>
                                                        <th> Leistungsbereich </th>
                                                        <th style="text-align:right;"> Summe Gesamt </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                    
                                    $i=1;   
                                    foreach($rooms_cost_details as $key => $rooms):
                                    
                                    $exact_this_rooms_cost = $rooms["costs_this_room_the_whole_time"] + $office_profit/count($rooms_cost_details);
                                    
                                    if(!empty($discount_applied))
                                        
                                        $exact_this_rooms_cost = $rooms["costs_this_room_the_whole_time"] - $rooms["costs_this_room_the_whole_time"]*$discount_applied/100 + $office_profit/count($rooms_cost_details);
                                        
                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?> </td>
                                                        <td>
                                                            <?php echo $key.'<b>  x  '.$rooms["rooms_ordered"].'</b>'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($exact_this_rooms_cost, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php endforeach; 
                                        
                                    foreach($meals_cost_details as $key => $meals): 
                                    
                                        $exact_this_meals_cost = $meals["costs_this_meal_the_whole_time"];

                                        if(!empty($discount_applied))

                                            $exact_this_meals_cost = $meals["costs_this_meal_the_whole_time"] - $meals["costs_this_meal_the_whole_time"]*$discount_applied/100;
                                        
                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?> </td>
                                                        <td>
                                                            <?php echo $key.'<b>  x  '.$meals["meals_ordered"].'</b>'; ?> </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($exact_this_meals_cost, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php endforeach; ?>

                                                    <tr>
                                                        <td> </td>
                                                        <td> Anreisekosten
                                                            <?php if(!empty($journey_title)) echo '(<b>'.$journey_title.'</b>)'; ?>

                                                            <?php if(!empty($abfahrsort)): ?>
                                                            <br/><br/> Abfahrsort (<b><?php echo $abfahrsort; ?></b>)
                                                            <?php endif; ?>
                                                            <td style="text-align:right;">
                                                                <?php echo number_format($journey_cost, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> MwSt + Service Charge </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($promoter_provision+$MwSt, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <?php if(!empty($discount_applied)): ?>

                                                    <tr>
                                                        <td> </td>
                                                        <td> Gesamtpreis (Ohne Rabatt) </td>
                                                        <td style="text-align:right;">
                                                            <?php echo number_format($without_discount, 2, ',', '.'); ?>&euro; </td>
                                                    </tr>

                                                    <tr>
                                                        <td> </td>
                                                        <td> Frühbucher Rabatt
                                                            <!--(<?php echo $discount_applied.'%'; ?> )-->
                                                        </td>
                                                        <!--<td style="text-align:right;">
                                            <?php echo '-'.number_format($without_discount-$actual_total_price, 2, ',', '.'); ?>&euro; </td>-->
                                                        <td style="text-align:right;">
                                                            <?php echo $discount_applied.'%'; ?>
                                                        </td>
                                                    </tr>

                                                    <?php endif; ?>

                                                    <tr>
                                                        <td> </td>
                                                        <td> <b>Gesamtpreis</b> </td>
                                                        <td style="text-align:right;"> <b><?php echo number_format($actual_total_price, 2, ',', '.'); ?>&euro;</b> </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="portlet box red-soft">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-euro"></i> Gesamtpreis f&uuml;r jede Reisendertyp
                                                </div>
                                            </div>
                                            <div class="portlet-body">

                                                <div class="row">

                                                    <?php
                                        
                                        $colors_picked_temp = $colors_to_pick_array[mt_rand(0, count($colors_to_pick_array) - 1)];
                                    
                                        foreach($indiv_cost_array as $key => $indiv_total_price):
                                    
                                    ?>

                                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 dashboard-stat-special">
                                                            <div class="dashboard-stat <?php echo $colors_picked_temp; ?>">
                                                                <div class="visual">
                                                                    <i class="fa fa-euro"></i>
                                                                </div>
                                                                <div class="details">
                                                                    <div class="number">
                                                                        <?php echo number_format($indiv_total_price, 2, ',', '.');; ?>&euro;
                                                                    </div>
                                                                    <div class="desc">
                                                                        <b><?php echo $key; ?></b>
                                                                        <!--<br/> Gesamtpreis pro Reisender-->

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">



                        <!-- BEGIN EXAMPLE TABLE PORTLET-->

                        <div class="portlet box grey-cascade">
                            <div class="portlet-title">
                                <div class="caption"><i class="fa fa-table"></i>Reisenderliste</div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Reisepaket</th>
                                            <!--<th> Status </th>-->
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php 
                        
                $sql = "SELECT * FROM ".$db_suffix."travelers WHERE bookings_ID='$bookings_ID' ORDER BY travelers_ID DESC";
                $news_query = mysqli_query($db,$sql); 
                        
		   		 while($row = mysqli_fetch_object($news_query))
			    {
				   
		   ?>

                                        <tr class="odd gradeX">
                                            <td><input type="checkbox" class="checkboxes" value="<?php echo $row->travelers_ID;?>" /></td>
                                            <td>
                                                <?php echo $row->travelers_first_name.' '.$row->travelers_last_name; ?>
                                            </td>
                                            <td>
                                                <?php echo $row->travelers_email;?>
                                            </td>
                                            <td>
                                                <?php echo $row->travelers_package;?>
                                            </td>
                                            <!--<td>
                                                <?php if($row->travelers_status)
							  
											echo '<span class="label label-md label-success">aktiv</span>'; 
									else 
											echo '<span class="label label-md label-danger">inaktiv</span>';
									?>
                                            </td>-->
                                        </tr>

                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>

            </div>
        </div>


        <?php require_once('admin/footer.php'); ?>

        <?php require_once('admin/scripts.php'); ?>

        <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>

        <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>

        <script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

        <script src="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/scripts/table-managed.js"></script>

        <script>
            $(document).ready(function() {

                TableManaged.init();

            });

        </script>

        <!-- END JAVASCRIPTS -->

</body>
<!-- END BODY -->

</html>
