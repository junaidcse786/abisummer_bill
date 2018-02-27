<?php 

$journey_ID = $_REQUEST['id'];
$sql = "SELECT * FROM ".$db_suffix."journey_location WHERE journey_ID = '$journey_ID' ORDER BY jl_city_location ASC";
$news_query = mysqli_query($db,$sql);


?>

<!-----PAGE LEVEL CSS BEGIN--->

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.css" />

<link rel="stylesheet" href="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />


<!-----PAGE LEVEL CSS END--->


<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
    Abfahrtsorte
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
    </ul>
    <!-- END PAGE TITLE & BREADCRUMB-->
</div>

<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->

<div class="row">
    <div class="col-md-12">



        <!-- BEGIN EXAMPLE TABLE PORTLET-->

        <div class="portlet box grey-cascade">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-table"></i>Abfahrtsortliste</div>
                <div class="actions">
                    <a href="<?php echo SITE_URL_ADMIN.'?mKey='.$mKey.'&pKey=addjourneylocation&id='.$journey_ID; ?>" class="btn blue"><i class="fa fa-plus"></i> Abfahrtsort einfügen</a>
                    <div class="btn-group">
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
                            <th>Ort</th>
                            <th>Preis</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>eingefügt am</th>
                            <!--<th >&nbsp;</th>    -->
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
		   		 while($row = mysqli_fetch_object($news_query))
			    {
				   
		   ?>

                        <tr class="odd gradeX">
                            <td><input type="checkbox" class="checkboxes" value="<?php echo $row->jl_ID;?>" /></td>
                            <td><a href="<?php echo '?mKey='.$mKey.'&pKey=editjourneylocation&id='.$row->jl_ID;?>"><?php echo $row->jl_city_location;?></a></td>

                            <td>
                                <?php echo $row->jl_price;?>
                            </td>
                            <td>
                                <?php echo $row->jl_notes;?>
                            </td>
                            <td>
                                <?php if($row->jl_status)
							  
											echo '<span class="label label-md label-success">aktiv</span>'; 
									else 
											echo '<span class="label label-md label-danger">inaktiv</span>';
									?>
                            </td>
                            <td>
                                <?php echo $row->jl_creation_time;?>
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

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>

<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<script src="<?php echo SITE_URL_ADMIN; ?>assets/admin/pages/scripts/table-managed.js"></script>

<script>
    jQuery(document).ready(function() {
        TableManaged.init();
    });

    var table_name = 'journey_location';

    var column_name = 'jl_status';

    var column_id = 'jl_ID';

    $('#confirmation_all').on('show.bs.modal', function(e) {

        $(this).find('#delete_button').on('click', function(e) {

            var id = '';

            $('input:checkbox[class=checkboxes]:checked').each(function() {

                id = id + $(this).val() + ',';
            })

            $.ajax({
                type: "POST",
                url: '<?php echo SITE_URL_ADMIN.'content_manager/delete_record.php' ; ?>',
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
                url: '<?php echo SITE_URL_ADMIN.'content_manager/change_status.php' ; ?>',
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

<!-----PAGE LEVEL SCRIPTS END--->

<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->

</html>
