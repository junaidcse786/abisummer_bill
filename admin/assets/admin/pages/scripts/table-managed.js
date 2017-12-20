var TableManaged = function () {

    var initTable1 = function () {

        var table = $('#sample_1');

        // begin first table
        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
					
				"sEmptyTable":      "Keine Daten in der Tabelle vorhanden",
				"sInfo":            "_START_ bis _END_ von _TOTAL_ Einträgen",
				"sInfoEmpty":       "0 bis 0 von 0 Einträgen",
				"sInfoFiltered":    "(gefiltert von _MAX_ Einträgen)",
				"sInfoPostFix":     "",
				"sInfoThousands":   ".",
				"sLengthMenu":      "_MENU_ Einträge anzeigen",
				"sLoadingRecords":  "Wird geladen...",
				"sProcessing":      "Bitte warten...",
				"sSearch":          "Suchen",
				"sZeroRecords":     "Keine Einträge vorhanden.",
				"oPaginate": {
					"sFirst":       "Erste",
					"sPrevious":    "Zurück",
					"sNext":        "Nächste",
					"sLast":        "Letzte"
				},
				"oAria": {
					"sSortAscending":  ": aktivieren, um Spalte aufsteigend zu sortieren",
					"sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
					}
				},

            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

            "columns": [{
                "orderable": false
            }, {
                "orderable": true
            }, {
                "orderable": false
            }, {
                "orderable": false
            }, {
                "orderable": true
            }, {
                "orderable": false
            }],
            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 5,            
            "pagingType": "bootstrap_full_number",
            
			"columnDefs": [{  // set default column settings
                'orderable': false,
                'targets': [0]
            }, {
                "searchable": false,
                "targets": [0]
            }],
            "order": [
                [1, "asc"]
            ] // set first column as a default sort by asc
        });

        var tableWrapper = jQuery('#sample_1_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).attr("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
		
		$('#sample_1 tr').click(function(event) {
			if (event.target.type !== 'checkbox') {
				$('.checkboxes', this).trigger('click');
			}
		});

        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    }

    var initTable2 = function () {

        var table = $('#sample_2');

        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
					
				"sEmptyTable":      "Keine Daten in der Tabelle vorhanden",
				"sInfo":            "_START_ bis _END_ von _TOTAL_ Einträgen",
				"sInfoEmpty":       "0 bis 0 von 0 Einträgen",
				"sInfoFiltered":    "(gefiltert von _MAX_ Einträgen)",
				"sInfoPostFix":     "",
				"sInfoThousands":   ".",
				"sLengthMenu":      "_MENU_ Einträge anzeigen",
				"sLoadingRecords":  "Wird geladen...",
				"sProcessing":      "Bitte warten...",
				"sSearch":          "Suchen",
				"sZeroRecords":     "Keine Einträge vorhanden.",
				"oPaginate": {
					"sFirst":       "Erste",
					"sPrevious":    "Zurück",
					"sNext":        "Nächste",
					"sLast":        "Letzte"
				},
				"oAria": {
					"sSortAscending":  ": aktivieren, um Spalte aufsteigend zu sortieren",
					"sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
					}
				},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "Alle"] // change per page values here
            ],
            // set the initial value
            "pageLength": -1,
            
            "columnDefs": [{  // set default column settings
                'orderable': false,
                'targets': [0]
            }, {
                "searchable": false,
                "targets": [0]
            }],
            "order": [
                
            ] // set first column as a default sort by asc
        });

        var tableWrapper = jQuery('#sample_2_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                } else {
                    $(this).attr("checked", false);
                }
            });
            jQuery.uniform.update(set);
        });
		
		$('#sample_2 tr').click(function(event) {
			if (event.target.type !== 'checkbox') {
				$('.checkboxes', this).trigger('click');
			}
		});

        tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown
    }

    var initTable3 = function () {

        var table = $('#sample_3');

        // begin: third table
        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
					
				"sEmptyTable":      "Keine Daten in der Tabelle vorhanden",
				"sInfo":            "_START_ bis _END_ von _TOTAL_ Einträgen",
				"sInfoEmpty":       "0 bis 0 von 0 Einträgen",
				"sInfoFiltered":    "(gefiltert von _MAX_ Einträgen)",
				"sInfoPostFix":     "",
				"sInfoThousands":   ".",
				"sLengthMenu":      "_MENU_ Einträge anzeigen",
				"sLoadingRecords":  "Wird geladen...",
				"sProcessing":      "Bitte warten...",
				"sSearch":          "Suchen",
				"sZeroRecords":     "Keine Einträge vorhanden.",
				"oPaginate": {
					"sFirst":       "Erste",
					"sPrevious":    "Zurück",
					"sNext":        "Nächste",
					"sLast":        "Letzte"
				},
				"oAria": {
					"sSortAscending":  ": aktivieren, um Spalte aufsteigend zu sortieren",
					"sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
					}
				},
            
            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
            
            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "Alle"] // change per page values here
            ],
            // set the initial value
            "pageLength": -1,
            
            "columnDefs": [{  // set default column settings
                'orderable': false,
                'targets': [0]
            }, {
                "searchable": false,
                "targets": [0]
            }],
            "order": [
                [1, "asc"]
            ] // set first column as a default sort by asc
        });

        var tableWrapper = jQuery('#sample_3_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                } else {
                    $(this).attr("checked", false);
                }
            });
            jQuery.uniform.update(set);
        });
		
		$('#sample_3 tr').click(function(event) {
			if (event.target.type !== 'checkbox') {
				$('.checkboxes', this).trigger('click');
			}
		});

        tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown
    }

    return {

        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }

            initTable1();
            initTable2();
            initTable3();
        }

    };

}();