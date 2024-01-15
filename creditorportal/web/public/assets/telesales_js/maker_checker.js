
    // 03-02-2022 - SVK005
var global_show = "show";
var global_alert = "hide";

    function summary_page(product_id,lead_id,emp_id,picked_by){

        $.ajax({
            url:'/lead_picked_by',
            type:'post',
            dataType:'json',
            data:{product_id: product_id,lead_id:lead_id,emp_id:emp_id,picked_by : picked_by},
            success: function (response) {
                // console.log(response);
    //            location.replace("tele_summary?product_id="+product_id+"&leadid="+lead_id);
    //            location.replace("tele_create_proposal?leadid="+lead_id);

                if(response.status=='yes'){
                    swal('Already picked');
                }else{
                console.log('t1');
                location.replace("tele_create_proposal?leadid="+lead_id);

                }

            }
        });

    }

    
    function retrigger_pg_link(emp_id){
        emp_id = emp_id || '';
        if (emp_id) {
            $.ajax({
                url: "/tls_payment_url_send",
                type: "POST",
                async: false,
                data: { emp_id: emp_id },
                dataType: "json",
                success: function (response) {
                    
                }
            });
        }
        swal({
                        title: 'Success',
                        text: 'Link Sent Successfully',
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function () {
                            
                                    location.reload();
    
                        });
        
    }


    
    function load_data(data_search) {

        $.fn.dataTable.ext.errMode = 'none';

        data_search = data_search || {};
        var oldExportAction = function (self, e, dt, button, config) {
            if (button[0].className.indexOf('buttons-excel') >= 0) {
                if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                } else {
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                }
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
        };
    
        var newExportAction = function (e, dt, button, config) {
            var self = this;
            var oldStart = dt.settings()[0]._iDisplayStart;
    
            dt.one('preXhr', function (e, s, data) {
    
                data.start = 0;
                data.length = 2147483647;
    
                dt.one('preDraw', function (e, settings) {
    
                    oldExportAction(self, e, dt, button, config);
    
                    dt.one('preXhr', function (e, s, data) {
    
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });
    
    
                    setTimeout(dt.ajax.reload, 0);
    
    
                    return false;
                });
            });
    
    
            dt.ajax.reload();
        };


            var currentDate = new Date();
            var day = currentDate.getDate();
            var month = currentDate.getMonth() + 1;
            var year = currentDate.getFullYear();
            var hr = currentDate.getHours();
            var min = currentDate.getMinutes();
            var sec = currentDate.getSeconds();
            var get_new_dates= day + "-" + month + "-" + year + " " + hr + "-" + min + "-" + sec;

        var dataTable = $('#leadMasterTable').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            searching: false,
            buttons: [{
                extend: 'excel',
                // header: false,
                customize: function ( testing ) {

                  },  
                text: 'Export<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                title:'',
                filename: get_new_dates,
                action: newExportAction,
                
                
                
                /*exportOptions: {
                    columns :  ['.admin'], 
                    config.exportOptions = { orthogonal: 'sort', columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16,17, 18, 19, 20, 21, 22,23, 24, 25, 26,27,28,29,30,31] };
                    columns: 'th:not(:last-child)'
                },
                */
                
    
                init: function (dt, node, config) {
                    debugger;
                    if ($("#is_admin_check").val() == '1') {
                        if ($("#is_region_check").val() != '') {
                        
                            config.exportOptions = { orthogonal: 'sort', columns: ['.export_region'] };
                        }
                        else{
                            config.exportOptions = { orthogonal: 'sort', columns: ['.export_admin'] };
                        }
                        
                        
                        config.columnDefs = [{
                            "targets": [0, 34,1,2],
                            "orderable": false,
                                                                                  
                        },
                        ]
                    } else {
                        config.exportOptions = { orthogonal: 'sort', columns: ['.export_agent'] };
                        config.columnDefs = [{
                            "targets": [0, 10],
                            "orderable": false,
                            
                            
                        },
                        ]
                    }
    
                },
                initComplete : function(){
                    $(".data-grid-export tr").first().addClass("notPrintable");
                },
            // 	customizeData: function (data) {
            // 		/*for (var i = 0; i < data.body.length; i++) {
            // 			for (var j = 0; j < data.body[i].length; j++) {
            // 				data.body[i][j] = '\u200C' + data.body[i][j];
            // 			}
            // 		}*/
            // 	}
            }],
            "processing": true,
            "serverSide": true,
            "createdRow": function( row, data, dataIndex ) {
    
                
                $(row).children(':nth-child(3)').addClass('wordallign');
                 $(row).children(':nth-child(13)').addClass('wordallign');
    
                
            },
            "order": [],
            "ajax": {
                url: '/tls_maker_tbl_ajax',
                type: "POST",
                data: function (d) {
                    d.mobno = $('[name=mobno]').val();
                    d.sakshamid = $('[name=sakshamid]').val();
                    d.moddate = $('[name=moddate]').val();
                    d.issuancedate = $('[name=issuancedate]').val();
                    d.status = $('[name=status]').val();
                    d.location = $('[name=location]').val();
                    d.last_modified = $('[name=last_modified]').val();
                    d.policyNumber = $('[name=policyNumber]').val();
                },
                complete: function (response) {

                    // 03-02-2022 - SVK005
                    if ( ! dataTable.data().any() && global_alert == "show") {
                        $("#lead_table_div").hide();
                        swal( 'No Record Found' );
                        global_show = "hide";
                        return;
                    }
                    global_show = "show";

                    if ($("#is_admin_check").val() == '1') {
                dataTable.columns('.admin').visible(false);
            }
                    if(response.responseJSON !== undefined){
                    if (response.responseJSON.data.length != 0) {
                        $('#leadgrid').show();
                    }
                    }
                }
    
            },
    
        });
        if ($("#is_admin_check").val() == '1') {
            dataTable.columns('.admin').visible(false);
        }
        
        dataTable.columns('.lead_column').visible(false);
    }
    
    function searchTable() {
        search("leadMasterTable");
    }
    
    function clearFilter() {
        $('#fullName').val('');
        $('#ddlCountry').val(0);
        $('#ddlState').val(0);
        search("leadMasterTable");
    }
    function manual_search(reference) {
        $('#leadMasterTable').DataTable().destroy();
        reference = reference || '';
        var parameters = {};
        if (reference) {
            if (column_sort && column_sort['sortby'] == $(reference).text()) {
                column_sort['order'] = (column_sort['order'] == 'desc') ? 'asc' : 'desc';
            } else {
                column_sort['sortby'] = $(reference).text();
                column_sort['order'] = 'asc';
                column_sort['number'] = $(reference).data('id');
            }
            parameters['sortby'] = column_sort['sortby'];
            parameters['order'] = column_sort['order'];
            parameters['number'] = column_sort['number'];
    
        }
        $('.search').each(function (i, obj) {
            if ($(obj).val()) {
                var key = $(obj).attr('name');
                var value = $(obj).val();
                parameters[key] = value;
            }
    
        });
        var status = $('#status :selected').val();
        if (status) {
            parameters['status'] = $('#status :selected').val();
        }
    
        load_data(parameters);
    
    }
    

    $(document).ready(function () {

        $('input[name="moddate"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
      
        $('input[name="moddate"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $('input[name="moddate"]').trigger("change");
        });
      
        $('input[name="moddate"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('input[name="moddate"]').trigger("change");
        });                
        


        
        $('input[name="issuancedate"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
      
        $('input[name="issuancedate"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $('input[name="issuancedate"]').trigger("change");
        });
      
        $('input[name="issuancedate"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('input[name="issuancedate"]').trigger("change");
        });                



        $('input[name="last_modified"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
      
        $('input[name="last_modified"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $('input[name="last_modified"]').trigger("change");
        });
      
        $('input[name="last_modified"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('input[name="last_modified"]').trigger("change");
        });                
        


    });

	$('body').on("keyup change", "input[name=last_modified],input[name=issuancedate],#moddate,#status,#location", function () {
		
		if($(this).attr('name') == 'mobno' || $(this).attr('name') == 'sakshamid'){

			if($(this).val().length < 10 && $(this).val().length > 0){
				return false;
			}

		}

		global_alert = "show";

		$('#leadMasterTable').DataTable().destroy();
		load_data();
	});

    $(document).ready(function () {
        
        // 03-02-2022 - SVK005
        load_data();
        var cts = $("#check_to_show").val();
        if(cts == "1"){
        $("#lead_table_div").show();
        }
        $('body').on("click", "#search_form", function () {
            var srchmobno = $('.srchmobno').val();
            var srchLeadId = $('.srchLeadId').val();
            if(srchmobno != '' || srchLeadId != ''){
			if(/^\d{10}$/.test(srchmobno) && srchmobno != ''){
			global_alert = "show";

			$('#leadMasterTable').DataTable().destroy();
			load_data();
			setTimeout(function () {
			if(global_show == "show"){
			$("#lead_table_div").show();
			}
			}, 2500); 
			}

			else if(/^!*(\d!*){10,}$/.test(srchLeadId) && srchLeadId != ''){
			global_alert = "show";

			$('#leadMasterTable').DataTable().destroy();
			load_data();
			setTimeout(function () {
			if(global_show == "show"){
			$("#lead_table_div").show();
			}
			}, 2500); 
			}else{
			swal("Alert","Enter valid digits.","warning"); $("#lead_table_div").hide();return false;
			}
	    }else{
		    swal("Alert","Please enter LeadID or Mobile No.","warning");
	    }
        });

    
    });

    function get_audit_trail(emp_id){
        emp_id = emp_id || '';
        if (emp_id) {
            $.ajax({
                url: "/get_audit_trail",
                type: "POST",
                async: false,
                data: { emp_id: emp_id },
                dataType: "json",
                success: function (response) {
                    
                    $("#payment_details_tbody_audit").html('');
                    
                    $.each(response, function (key,val) {
                        debugger;
                        $("#payment_details_tbody_audit").append('<tr>');
                        $("#payment_details_tbody_audit").append('<td>'+val["Dispositions"]+'</td>');
                        $("#payment_details_tbody_audit").append('<td>'+val["Sub-dispositions"]+'</td>');
                        $("#payment_details_tbody_audit").append('<td>'+val["date"]+'</td>');
                        $("#payment_details_tbody_audit").append('<td>'+val["agent_name"]+'</td>');
                        $("#payment_details_tbody_audit").append('<td>'+((val["remarks"]) ? val["remarks"] : '')+'</td>');
                        //upendra - maker/checker - 30-07-2021
                        $("#payment_details_tbody_audit").append('<td>'+((val["type"]) ? val["type"] : 'AV')+'</td>');
                        $("#payment_details_tbody_audit").append('</tr>');
                    });
                    
                    $("#auditmodal").modal('show');
                }
            });
    }
    }
    


 
