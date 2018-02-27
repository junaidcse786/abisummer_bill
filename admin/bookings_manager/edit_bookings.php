<?php 

$bookings_ID = isset($_REQUEST['id']) ? $_REQUEST['id']: 0;
$sql = "select * from ".$db_suffix."bookings where bookings_ID = $bookings_ID limit 1";				
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
            <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=bookings'; ?>">Buchungsliste</a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->



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
                                    <?php echo number_format($rooms_cost, 2, ',', '.'); ?>&euro;
                                </div>
                                <div class="desc">
                                    Gesamte Zimmerkosten
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br/>

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-md-offset-4">
                        <div class="dashboard-stat green-haze">
                            <div class="visual">
                                <i class="fa fa-code"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <?php echo $bookings_code; ?>
                                </div>
                                <div class="desc">
                                    Buchungscode
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <div class="dashboard-stat">
                            <div class="visual">
                                <i class="fa fa-doc"></i>
                            </div>
                            <div class="details">
                                <div class="desc">
                                    <div class="form-group">
                                        <textarea placeholder="Notes" style="color: black;" id="bookings_notes"><?php echo $bookings_notes; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    
                                    $exact_this_rooms_cost = $rooms["costs_this_room_the_whole_time"];
                                    
                                    if(!empty($discount_applied))
                                        
                                        $exact_this_rooms_cost = $rooms["costs_this_room_the_whole_time"] - $rooms["costs_this_room_the_whole_time"]*$discount_applied/100;
                                        
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
                                        <td> Office Profit </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($office_profit, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> Promoter Provision </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($promoter_provision, 2, ',', '.'); ?>&euro; </td>
                                    </tr>

                                    <tr>
                                        <td> </td>
                                        <td> MwSt </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($MwSt, 2, ',', '.'); ?>&euro; </td>
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
                <div class="actions">
                    <div class="btn-group">

                        <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=addtravelers&id='.$bookings_ID; ?>" class="btn blue"><i class="fa fa-plus"></i> Reisender einfügen</a>

                        <a class="btn green" href="#" data-toggle="dropdown">
                           <i class="fa fa-cogs"></i> Tools
                           <i class="fa fa-angle-down"></i>
                           </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#" data-toggle="modal" data-target="#confirmation_all"><i class="fa fa-trash"></i> Löschen</a></li>
                            <li><a href="#" data-toggle="modal" data-status="1" data-target="#confirmation_status"><i class="fa fa-flag"></i> Aktivieren</a></li>
                            <li><a href="#" data-toggle="modal" data-status="0" data-target="#confirmation_status"><i class="fa fa-flag-o"></i> Deaktivieren</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="sample_2">
                    <thead>
                        <tr>
                            <th class="table-checkbox"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Reisepaket</th>
                            <th> Status </th>
                            <th>eingefügt am</th>
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
                                <a href="<?php echo '?mKey='.$mKey.'&pKey=edittravelers&id='.$row->travelers_ID;?>">
                                    <?php echo $row->travelers_first_name.' '.$row->travelers_last_name; ?>
                                </a>
                            </td>
                            <td>
                                <?php echo $row->travelers_email;?>
                            </td>
                            <td>
                                <?php echo $row->travelers_package;?>
                            </td>
                            <td>
                                <?php if($row->travelers_status)
							  
											echo '<span class="label label-md label-success">aktiv</span>'; 
									else 
											echo '<span class="label label-md label-danger">inaktiv</span>';
									?>
                            </td>
                            <td>
                                <?php echo $row->travelers_creation_time;?>
                            </td>
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


<!-----MODALS FOR THIS PAGE START ---->


<div class="modal fade" id="confirmation_all">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Bestätigung</h4>
            </div>
            <div class="modal-body">
                <span class="font-red-thunderbird"><strong>Warnung !</strong></span> Sind Sie sich sicher um diese Daten zu löschen? </div>
            <div class="modal-footer">
                <button id="delete_button" type="button" class="btn red-thunderbird">Löschen</button>
                <button type="button" class="btn default" data-dismiss="modal">Schließen</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="confirmation_status">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Bestätigung</h4>
            </div>
            <div class="modal-body">
                <span class="font-red-thunderbird"><strong>Warnung !</strong></span> Sind Sie sich sicher um diese Daten zu ändern? </div>
            <div class="modal-footer">
                <button id="delete_button" type="button" class="btn red-thunderbird">&Auml;ndern</button>
                <button type="button" class="btn default" data-dismiss="modal">Schließen</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-----MODALS FOR THIS PAGE END ---->

<input id="bookings_ID" type="hidden" name="bookings_ID" value="<?php echo $bookings_ID ?>" />

<?php require_once('footer.php'); ?>

<?php require_once('scripts.php'); ?>

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<script src="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/scripts/table-managed.js"></script>

<script>
    $(document).ready(function() {

        TableManaged.init();
        var idleState = false;
        var idleTimer = null;
        var bookings_notes_old = "";
        $('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function() {
            clearTimeout(idleTimer);
            if (idleState == true) {

                var bookings_ID = $("#bookings_ID").val();
                var bookings_notes = $("#bookings_notes").val();

                if (bookings_notes != bookings_notes_old) {

                    $.ajax({
                        type: "POST",
                        url: '<?php echo SITE_URL_ADMIN; ?>bookings_manager/save_notes.php',
                        dataType: "text",
                        data: {
                            bookings_ID: bookings_ID,
                            bookings_notes: bookings_notes
                        },
                        success: function(data) {

                            bookings_notes_old = bookings_notes;
                            //window.location.reload(true);
                        }
                    });
                }

            }
            idleState = false;
            idleTimer = setTimeout(function() {
                //$("body").css('background-color','#000');
                idleState = true;
            }, 1000);
        });
        $("body").trigger("mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick");
    });


    var table_name = 'travelers';

    var column_name = 'travelers_status';

    var column_id = 'travelers_ID';

    $('#confirmation_all').on('show.bs.modal', function(e) {

        $(this).find('#delete_button').on('click', function(e) {

            var id = '';

            $('input:checkbox[class=checkboxes]:checked').each(function() {

                id = id + $(this).val() + ',';
            })

            $.ajax({
                type: "POST",
                url: '<?php echo SITE_URL_ADMIN.'content_manager/delete_record.php ' ; ?>',
                dataType: "text",
                data: {
                    id: id,
                    table_name: table_name,
                    column_name: column_name,
                    column_id: column_id
                },
                success: function(data) {
                    window.location.reload(true);
                }
            });
        });
    });

    $('#confirmation_status').on('show.bs.modal', function(e) {

        var status = $(e.relatedTarget).data('status');

        $(this).find('#delete_button').on('click', function(e) {

            var id = '';

            $('input:checkbox[class=checkboxes]:checked').each(function() {

                id = id + $(this).val() + ',';
            })

            $.ajax({
                type: "POST",
                url: '<?php echo SITE_URL_ADMIN.'content_manager/change_status.php'; ?>',
                dataType: "text",
                data: {
                    id: id,
                    status: status,
                    table_name: table_name,
                    column_name: column_name,
                    column_id: column_id
                },
                success: function(data) {
                    window.location.reload(true);
                }
            });
        });
    });

</script>

<!-- END JAVASCRIPTS -->

</body>
<!-- END BODY -->

</html>
