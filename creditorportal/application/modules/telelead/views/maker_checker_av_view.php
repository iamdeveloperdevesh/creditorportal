<style> table tr td
    { font-size: 12px; }

    .buttons-excel {
        float: right;
        background: #da8089 !important;
        border: 1px dotted #da8089 !important;
        border-radius: 100px !important;
        color: #fff !important;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 1px; }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(to bottom, #da8089 0%, #da8089 100%);
        border-radius: 100px; color:#fff !important; border-color: #da8089; }

    .wordallign {
        word-break:break-all;
    }
    .table thead th {
        border-bottom: 2px dashed #deff !important;
        text-transform: capitalize;
        width: 10%;
    }
    .table td{
        font-weight: 600!important;
        word-break: break-all;
    }

    .sweet-alert h2{
        font-size: 20px !important;
    }
    .btn-cta {
        padding: 11px 12px;
    }
    .btn-primary {
        color: #fff;
        background-color: #006400 !important;
        border-color: #006400 !important;
    }
    .btn, button {
        white-space: nowrap;
    }
    .btn-default {
        background: #efefef;
    }
</style>

<?php $telesession = $this->session->userdata('telesales_session');
$admin = $telesession['is_admin'];
?>

<?php
if($admin != 1){
    ?>
    <style>
        .dt-buttons{
            display: none !important;
        }
    </style>
    <?php
}
?>

<input type = "hidden" id = "is_admin_check" value = "<?php echo $admin; ?>">
<input type = "hidden" id = "is_region_check" value = "<?php echo $telesession['location']; ?>">
<input type = "hidden" id = "lead_id_payment" value = "">

            <div class="col-lg-10 table-responsive pad-0 mt-3">
                <div class="card">
                    <div class="card-body card-style">
                        <div class="col-md-12">
                            <h4 class="header-title title col-md-12 header-tl-xd"> <img class="img-imv" src="/public/assets/images/new-icons/policy-del-xd.png">
                                <span class="ml-2">View Lead Details</span></h4>
                        </div>
                        <?php
                        // print_r($_SESSION);
                        if ($_SESSION['telesales_session']['is_admin'] == "1" || $_SESSION['telesales_session']['is_region_admin'] == "1" || $_SESSION['telesales_session']['outbound'] == "3") {

                        $style_table = '';
                        ?>

                        <input type="hidden" id="check_to_show" name="check_to_show" value="1">
                        <div id="leadgrid" style="background: #fff; padding: 10px;display:none;">
                            <div class="row mt-2">
                                <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                    <div class="form-group">
                                        <label for="example-text-input" class="col-form-label">Enter Mobile No</label>
                                        <input class="form-control search" maxlength="10" name="mobno" type="text" placeholder="Search By Mobile No">
                                    </div>
                                </div>
                                <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                    <div class="form-group">
                                        <label for="example-text-input" class="col-form-label">Enter Lead Id</label>
                                        <input class="form-control search" name="sakshamid" type="text" placeholder="Search By Lead Id">
                                    </div>
                                </div>
                                <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                    <div class="form-group">
                                        <label for="example-text-input" class="col-form-label">Enter Policy Number</label>
                                        <input class="form-control search srchPolicyNumber" name="policyNumber" type="text" placeholder="Search By Policy Number">
                                    </div>
                                </div>
                                <!--  <div class=<?php /*echo ($admin == '1') ? "col-md-2" : "col-md-3" */?>>

                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Select Status</label>
                                            <select name="status" id="status" class='form-control' placeholder='Search By Status'>
                                                <option value="">Select Status</option>
                                                <option value="payment received">Payment Done</option>
                                                <option value="success">Policy Issued</option>
                                                <option value="payment pending">Payment Pending</option>
                                                <option value="rejected">Lead Lapsed</option>
                                                <option value="proposal not created">Proposal Pending</option>
                                                <option value="payment link not triggered">Payment Link Not Triggered</option>
                                            </select>
                                        </div>
                                    </div>-->
                                <?php if ($admin == '1') { ?>
                                    <div class="<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>">
                                        <div class="form-group">

                                            <label for="example-text-input" class="col-form-label">Enter Lead Generation Date</label>
                                            <input class="form-control search" name="moddate" type="text" placeholder="Search By Generation Date">
                                        </div>
                                    </div>
                                    <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Enter Policy Issuance Date</label>
                                            <input class="form-control search" name="issuancedate" type="text" placeholder="Search By Modified Date">
                                        </div>
                                    </div>
                                    <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Select Location</label>
                                            <select name="location" id="location" class='form-control' placeholder='Search By Status'>
                                                <option value="">Select Location</option>
                                                <option value="1">Noida</option>
                                                <option value="3">Hyderabad</option>
                                                <option value="2">Bangalore</option>
                                                <option value="5">Kolkata</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($admin == '1') { ?>
                                    <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Select Axis Process</label>
                                            <select name="axis_process_filter" id="axis_process_filter" class='form-control' placeholder='Search By Status'>
                                                <option value="">Select Axis Process</option>
                                                <option value="Inbound Phone Banking">Inbound Phone Banking</option>
                                                <option value="Outbound Call Center (OCC)">Outbound Call Center (OCC)</option>
                                            </select>
                                        </div>
                                    </div>

                                <?php } ?>

                                <div class="col-md-12 text-right"><button id = "clear_filter" type="submit" class="btn sub-btn mb-2">Clear Filter<i class="ti-arrow-right arrow-xd"></i></button></div>

                                <?php
                                } else {
                                    $style_table = 'style="margin-top:10px;display:none;"';

                                    ?>
                                    <input type="hidden" id="check_to_show" name="check_to_show" value="0">

                                    <div id="leadgrid" style="background: #fff; padding: 10px;">
                                        <div class="row mt-2">
                                            <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                                <div class="form-group">
                                                    <label for="example-text-input" class="col-form-label">Enter Mobile No</label>
                                                    <input class="form-control srchmobno" maxlength="10" name="mobno" type="text" placeholder="Search By Mobile No">
                                                </div>
                                            </div>
                                            <div class=<?php echo ($admin == '1') ? "col-md-2" : "col-md-3" ?>>
                                                <div class="form-group">
                                                    <label for="example-text-input" class="col-form-label">Enter Lead Id</label>
                                                    <input class="form-control srchLeadId" name="sakshamid" type="text" placeholder="Search By Lead Id">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-center"><button id="search_form" type="submit" class="btn btn-cta btn-xs">Search<i class="ti-arrow-right arrow-xd"></i></button></div>

                                    <?php
                                }
                                ?>

                                <div class="col-md-12 table-responsive" id="lead_table_div" style="display: none;margin-top:10px">
                                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="leadMasterTable"
                                           width="100%">
                                        <thead class="header-blue">
                                        <tr>
                                            <th data-id='0' class="export_admin export_agent blue_bg_header no-sort">Seq No</th>
                                            <th data-id='1' class="export_admin export_region export_agent blue_bg_header">Lead No</th>
                                            <th data-id='2' class="export_admin export_region export_agent blue_bg_header">Proposal No</th>
                                            <th data-id='3' class="export_admin export_region export_agent blue_bg_header">Customer Name</th>
                                            <th data-id='4' class="export_admin export_region export_agent blue_bg_header">Lead Generation date and time</th>
                                            <th data-id='5' class="export_admin export_region export_agent blue_bg_header">Last Modified Date and Time</th>
                                            <th data-id='6' class="export_admin export_region export_agent blue_bg_header">Latest mapped Do name</th>
                                            <th data-id='7' class="export_admin export_region export_agent blue_bg_header">Latest mapped AV name</th>
                                            <th data-id='8' class="export_admin export_region export_agent blue_bg_header">Product/ Plan name</th>
                                            <th data-id='9' class="export_admin  export_region export_agent export_agent blue_bg_header no-sort">Net Premium</th>
                                            <!-- <th data-id='10' class="lead_column  export_admin export_agent blue_bg_header no-sort">Axis Center</th> -->
                                            <th data-id='11' class="export_admin  export_region export_agent blue_bg_header no-sort">Axis Center/Location</th>
                                            <th data-id='11' class="export_admin  export_region export_agent blue_bg_header no-sort">LOB</th>
                                            <th data-id='12' class="export_admin export_region export_agent blue_bg_header">Disposition</th>
                                            <th data-id='13' class="export_admin export_region export_agent blue_bg_header no-sort">Sub disposition</th>
                                            <th data-id='14' class="export_admin export_region export_agent blue_bg_header">Status</th>
                                            <th data-id='15' class="export_admin export_region export_agent blue_bg_header">Issuance Date and Time</th>
                                            <th data-id='15' class="export_admin export_region export_agent blue_bg_header">Remark</th>
                                            <th data-id='16' class="export_admin  export_region export_agent export_agent blue_bg_header no-sort">Action</th>

                                            <th data-id='17' class="blue_bg_header no-sort">AUDIT</th>
                                            <th data-id='20' class="export_admin export_region export_agent blue_bg_header">RETRIGGER PG</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="8" class="dataTables_empty">Please wait.. fetching records.</td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

</div>
<!--audit trail modal -->
<div class="modal" id="auditmodal" aria-hidden="true" style="display: none;">

    <div class="modal-dialog modal-dialog-centered modal-lg" data-backdrop="static" data-keyboard="false">
        <div class="modal-content"> <div class="modal-header header-title title header-tl-xd"> </div> <div class="modal-body"> <div class="table-responsive"> <table class="table">
                        <thead>
                        <tr>
                            <th>Disposition</th>
                            <th>Sub-Disposition</th>
                            <th>Date-Time</th>
                            <th>Agent</th>
                            <th>Remarks</th>
                            <th>Agent Type</th>
                        </tr>
                        </thead>
                        <tbody id = 'payment_details_tbody_audit'>




                        </tbody>
                    </table> </div>  </div> <div class="modal-footer"> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> </div> </div> </div> </div>

<!-- ends -->

<div class="modal" id="agentModal">
    <div class="modal-dialog modal-dialog-centered modal-lg" data-backdrop="static" data-keyboard="false">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header header-title title header-tl-xd">
                <h4 class="modal-title">Select Agent</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="table-responsive">
                    <select class="form-control" name="agent_reassign" id="agent_reassign">
                        <option value="">Select Agent</option>
                        <?php foreach ($agents as $value) {?>
                            <option value=<?=trim(encrypt_decrypt_password($value['id']))?>> <?=trim($value['agent_name']);?> </option>
                        <?php }?>
                    </select>

                </div>
                <div id="agent-error" style = "display:none;" class="error">This field is required.</div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-cta" id="agentreassign-btn">Submit</button>
            </div>

        </div>
    </div>
</div>

<!--payment modal -->

<div class="modal" id="paymentModal">
    <div class="modal-dialog modal-dialog-centered modal-lg" data-backdrop="static" data-keyboard="false">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header header-title title header-tl-xd">
                <h4 class="modal-title">Select Agent</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="table-responsive">
                    <div class="form-group"> <label for="example-text-input" class="col-form-label">Enter Transaction No</label> <input class="form-control"  name="txno" id = "txno" type="text" required autocomplete="off" > </div>

                </div>
                <div id="txn-error" style = "display:none;" class="error">This field is required.</div>
                <div class="table-responsive">
                    <div class="form-group"> <label for="example-text-input" class="col-form-label">Enter Transaction Date</label> <input class="form-control "  name="txndate" id = "txndate" type="text" required autocomplete="off" > </div>

                </div>
                <div id="txndate-error" style = "display:none;" class="error">This field is required.</div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-cta" id="txnaction-btn">Submit</button>
            </div>

        </div>
    </div>
</div>

<script>


    // 03-02-2022 - SVK005
    var global_show = "show";
    var global_alert = "hide";

    function summary_page(product_id,lead_id,emp_id,picked_by,edit_status=1){

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
                    location.replace("tele_create_proposal?leadid="+lead_id+'&editstatus='+btoa(edit_status));

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
                url: 'get_datatable_maker_ajax',
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
                        alert( 'No Record Found' );
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

     /*   $('input[name="moddate"]').daterangepicker({
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
*/


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





</script>
