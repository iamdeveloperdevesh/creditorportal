<style>
    .dataTables_length select{
        border-color: #da8089;
        font-size: 12px;
        border-radius: 10px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #da8089;
        border-radius: 100px;
        padding: 7px 12px;
        font-size: 12px;
        letter-spacing: 1px;
        color: grey;
    }
    .btn-cta a:hover {
        color:#fff;
    }

    #agentTable{
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: -ms-autohiding-scrollbar;
    }
    table tr td {
        font-size: 12px;
    }
    .dataTables_wrapper .dataTables_info {
        color: grey;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.4px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(to bottom, #da8089 0%, #da8089 100%);
        border-radius: 100px;
        color:#fff !important;
        border-color: #da8089;
    }
</style>

<!--<div class="horizontal-main-wrapper all_div" style="pointer-events:auto;">


    <div class="main-content-inner">
        <div class="container-fluid" style="padding:0px;">-->

            <div class="col-lg-10 mt-3 res-container">

                <div class="card">
                    <div class="card-body card-style">
                        <div class="col-md-12"> <h4 class="header-title title  col-md-12 header-tl-xd"> <img class="img-imv" src="/public/assets/images/new-icons/policy-del-xd.png"><span class="ml-2">DO Upload</span></h4> </div>
                        <div class="col-md-12 mt-2">
                            <div class="mb-4 mt-4">
                                <!--
                                                            <button type="button" name="Add" class="btn btn-cta btn-xs add_av_data" data-toggle="modal" data-target="#myModal"><span>Create AV </span><i class="ti-plus"></i></button>
                                -->
                                <button type="button" name="bulk_upload"  data-toggle="modal" data-target="#myModalexcel" class="btn btn-cta btn-xs"><span>Bulk Upload </span><i class="ti-cloud-down"></i></button>

                            </div>
                            <table cellpadding="0" cellspacing="0" border="0" class="display mt-4" id="doTable" width="100%">
                                <thead class="header-blue">
                                <tr>
                                    <th class="blue_bg_header no-sort">DO ID</th>
                                    <th class="blue_bg_header">DO Name</th>
                                    <th class="blue_bg_header">TL ID</th>
                                    <th class="blue_bg_header">TL Name</th>
                                    <th class="blue_bg_header">Center</th>
                                    <!-- <th class="blue_bg_header">OM Name</th>	 -->
                                    <th class="blue_bg_header">Status</th>
                                    
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
<!-- parent  -->


<div class=" modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-res">

        <div class="modal-content">

            <div class="modal-header header-title title  header-tl-xd">
                <h4><img src="/public/assets/images/new-icons/cus-xd-detail.png">Create AV</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>
            <form action = "#" id="avForm" method="post">
                <div class="modal-body">

                    <div class="row col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">AV Id<span style="color:#FF0000">*</span></label></label>
                                <input class="form-control" type="text" value="" id="agentCode" name="agentCode">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">AV Name</label>
                                <input class="form-control first_name" type="text" value="" id="agentName" name="agentName">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">TL Id</label>
                                <input class="form-control" type="text" value="" id="tl_id" name="tlId">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">TL Name</label>
                                <input class="form-control first_name" type="text" value="" id="tl_name" name="tlName">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-email-input" class="col-form-label">AM ID</label>
                                <input type="text" class="form-control valid" id="am_id" name="amId"  placeholder=" " >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label"> AM Name</label>
                                <input class="form-control first_name type="text" value="" id="am_name" name="amName" > </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label"> OM ID</label>
                                <input class="form-control" type="text" value="" id="om_id" name="omId"> </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label"> OM Name</label>
                                <input class="form-control first_name" type="text" value="" id="om_name" name="omName"> </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label"> Center</label>
                                <input class="form-control first_name" type="text" value="" id="center" name="center"> </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label"> LOB</label>
                                <input class="form-control first_name" type="text" value="" id="lob" name="lob"> </div>
                        </div>




                    </div>

                    <div class="row col-md-12">
                        <div class="col-md-3">
                            <h6><b>Role Type<span style="color:#FF0000">*</span></b></h6>
                        </div>
                    </div>

                    <div class="row role_type col-md-12 mt-3 ml-2">
                        <div class="col-md-3 col-6"> <div class="form-group"> <div class="custom-control custom-radio"> <input type="radio" class="form-check-input custom-control-input" name='role_types' value="Admin" id="Admin"> <label for="Admin" class="custom-control-label">Admin</label> </div> </div></div>
                        <div class="col-md-3 col-6"> <div class="form-group"> <div class="custom-control custom-radio"> <input type="radio" class="form-check-input custom-control-input" name='role_types' value="Agent" id="Agent">  <label for="Agent" class="custom-control-label">Agent</label> </div> </div></div>


                    </div>

                    <div class="row col-md-12">
                        <div class="col-md-3">
                            <h6><b>Module Access<span style="color:#FF0000">*</span></b></h6>
                        </div>
                    </div>

                    <div class="row role_module col-md-12 mt-3 ml-2">


                    </div>

                </div>
                <div class="modal-footer">

                    <input class="form-control col-md-3 col-6" type="hidden" value="" id="edit_data" name="edit" >

                    <button type="submit" class="btn sub-btn">Submit</button>
                </div>
        </div>
        </form>
    </div>

</div>



<div class=" modal fade" id="myModalexcel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-res">

        <div class="modal-content">

            <div class="modal-header header-title title  header-tl-xd">
                <h4><img src="/public/assets/images/new-icons/cus-xd-detail.png">Bulk Upload</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>
            <form action = "#" id="importAv" method="post">
                <div class="modal-body">
                    <div class="row">
                        <!--
                                        <div class="col-md-4">
                                     <div class="form-group">
                                     <label for="example-text-input" class="col-form-label"> Agent Type</label>
                                     <select class="form-control" id="agent_type" name ="agent_type">
                                              <option value = 1 selected>Inbound</option>
                                              <option value = 2>Outbound</option>
                                              <option value = 3>DO  Upload</option>
                                    </select>
                                     </div>
                                     </div>
                        -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type='hidden' id="agent_type" name ="agent_type" value="3">
                                <label for="example-text-input" class="col-form-label"> Upload DO</label><a href="../assets/telesales_file/Tele Oubound Do Upload Format.xlsx"  id ='format_file' class="btn btn-cta btn-xs mb-3" download>Sample Format Download <i class="ti-file"></i></a>
                                <br><input class="" type="file" value="" id="file_data" name="filetoUpload" > </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">


                    <button type="submit" class="btn sub-btn">Submit</button>
                </div>
            </form>
        </div>

    </div>
</div>
</div>
<div class=" modal fade" id="myModalPassChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-res">

        <div class="modal-content">

            <div class="modal-header header-title title  header-tl-xd">
                <h4><img src="/public/assets/images/new-icons/cus-xd-detail.png">Reset Password</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div> <div class="modal-body">
                <form id="resetPassword" method="post" action="">
                    <input type="hidden" id="agent_type" name="agent_type" value="3">
                    <input type="hidden" id="agent_id" name="agent_id">
                    <div class="form-group">
                        <label for="newPass">New Password:</label>
                        <input type="password" class="form-control" name="newPass" id="newPass">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword">
                    </div>

                    <button type="button" onclick="ResetPassword()" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>

    </div>
</div>
</div>

<script>
    $(document).ready(function()
    {

        $("#license_from").datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            // yearRange: "-100Y:-18Y",
            // maxDate: "+18Y",
            minDate: "0"

        });

        $("#license_to").datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            // yearRange: "-100Y:-18Y",
            // maxDate: "2Y",
            minDate: "+1D"

        });

        var dataTable = $('#avTable').DataTable({
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
                url:"/tele_av_tbl_ajax",
                type:"POST"
            },
            "columnDefs":[
                {
                    "targets":[5],
                    "orderable":false,
                },
            ],
        });


        var dataTable = $('#doTable').DataTable({
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
                url:"/telelead/get_do_datatable_ajax",
                type:"POST"
            },
            "columnDefs":[
                {
                    "targets":[5],
                    "orderable":false,
                },
            ],
        });


        var dataTable = $('#outbondTable').DataTable({
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
                url:"/tele_outbond_tbl_ajax",
                type:"POST"
            },
            "columnDefs":[
                {
                    "targets":[5],
                    "orderable":false,
                },
            ],
        });

        $('.bd-example-modal-md').removeClass('hide');
    });

    function ResetPasswordModal(data) {
        $("#agent_id").val(data.id);
        $('#myModalPassChange').modal('show');
    }
    //resetPasswordNew
    function ResetPassword() {

        var form = $("#resetPassword").serialize();
        $.ajax({
            url: "/resetPasswordNew",
            type: "POST",
            async: false,
            dataType: "json",
            data:form,
            success: function (response)
            {
                if(response.code == 200){
                    swal("Success", response.msg);
                    $('#resetPassword').trigger("reset");
                    $('#myModalPassChange').modal('hide');
                }else{
                    swal("Alert", response.msg);
                }
            }
        });
    }
    function searchTable(){
        search("avTable");
    }

    function clearFilter(){

        search("avTable");
    }
    $(document).on("click", ".add_av_data", function ()
    {
        role_access_module();
        $("#avForm").trigger("reset");
    });

    function role_access_module()
    {
        $.ajax({
            url: "/tele_role_access_module",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response)
            {
                $(".role_module").empty();
                for (i = 0; i < response.length; i++)
                {
                    var str;
                    str = '<div class="col-md-3"> <div class="form-group"><input type="checkbox" class="form-check-input role_check" value="'+response[i].role_module_id+'" name="create_module[]" ><label for="create_mode">'+response[i].acc_module_name+'</label></div></div>';
                    $(".role_module").append(str);
                }
            }
        });
    }
    $("#avForm").validate({
        ignore: ".ignore",
        rules: {
            agentCode: {
                required: true
            },
            agentName: {
                required: true
            },
            center: {
                required: true
            },
            axis_process: {
                required: true
            },


        },
        messages: {

            'test[]': {
                required: "You must check at least 1 box",

            }

        },
        submitHandler: function (form)
        {
            ajaxindicatorstart();
            var form = $("#avForm").serialize();
            $.post("/tele_create_av", form, function (e)
            {
                var data = JSON.parse(e);
                if (data.status == false)
                {
                    ajaxindicatorstop();
                    swal({
                            title: "Alert",
                            text: data.message,
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true,
                            allowOutsideClick: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true,
                            allowEscapeKey: false
                        },
                        function ()
                        {
                            location.reload();
                        });
                    return;
                }
                else
                {
                    ajaxindicatorstop();
                    swal({
                            title: "Success",
                            text: data.message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true,
                            allowOutsideClick: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true,
                            allowEscapeKey: false
                        },
                        function ()
                        {
                            location.reload();
                        });
                }

            });

        }
    });

    function editAgent(e)
    {

        var edit_data = e.id;
        $.ajax({
            url: "/tele_edit_av_data",
            type: "POST",
            async: false,
            data:{'edit':edit_data},
            dataType: "json",
            success: function (response)
            {

                if(response.length != 0)
                {
                    role_access_module();
                    var data = response[0];
                    var is_admin;
                    $("#agentCode").val(data.agent_id);
                    $("#agentName").val(data.agent_name);
                    $("#tl_id").val(data.tl_emp_id);
                    $("#tl_name").val(data.tl_name);
                    $("#am_id").val(data.am_emp_id);
                    $("#am_name").val(data.am_name);
                    $("#om_name").val(data.om_name);
                    $("#om_id").val(data.om_emp_id);
                    $("#center").val(data.center);
                    $("#lob").val(data.lob);
                    $("#axis_process").val(data.axis_process);
                    $("#axis_process").val(data.axis_process);
                    $("#license_from").val(data.license_from);
                    $("#license_to").val(data.license_to);

                    if(data.is_admin == '0')
                    {
                        is_admin = 'Agent';
                    }
                    else
                    {
                        is_admin = 'Admin';
                    }
                    $("input[name='role_types'][value='"+is_admin+"']").prop('checked',true);
                    /*$("input[name='login'][value='"+data.is_login+"']").prop('checked',true);*/
                    $("#edit_data").val(data.id);
                    var role = data.module_access_rights.split(',');
                    $.each(role,function(i){

                        $("input[name='create_module[]'][value='"+role[i]+"']").prop('checked',true);

                    });

                }
            }
        });
    }

    function deleteAgent(e)
    {
        var delete_data = e.id;

        $.ajax({
            url: "/tele_delete_av_data",
            type: "POST",
            async: false,
            data:{'delete_data':delete_data},
            dataType: "json",
            success: function (response)
            {
                ajaxindicatorstart();


                if(response.status = true)
                {
                    ajaxindicatorstop();
                    swal({
                            title: "Success",
                            text: response.message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true,
                            allowOutsideClick: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true,
                            allowEscapeKey: false
                        },
                        function ()
                        {
                            location.reload();
                        });

                }
                else
                {
                    ajaxindicatorstop();
                    swal({
                            title: "Alert",
                            text: response.message,
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true,
                            allowOutsideClick: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true,
                            allowEscapeKey: false
                        },
                        function ()
                        {

                        });
                    return;
                }
            }
        });

    }
    $("#importAv").validate({
        ignore: ".ignore",
        rules: {
            filetoUpload: {
                required: false
            },

        },
        messages: {},
        submitHandler: function (form)
        {
            var file_agent = $("#file_data");

            var file_agent_data = file_agent[0].files;

            if(file_agent_data.length != 0)
            {
                var abc = file_agent_data[0].name.substring(file_agent_data[0].name.lastIndexOf('.') + 1);

                var fileExtension = ['xlsx','CSV','xls','csv'];
                if ($.inArray(abc, fileExtension) == -1)
                {
                    displayMsg("warning", "Please check documents uploaded, Supported documents xlsx, xls,csv");
                    return false;
                }
            }

            var data = new FormData(form);
            $.ajax({
                type: "POST",
                url: "/telelead/upload_av",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                mimeType: "multipart/form-data",
                dataType: "json",
                success: function(e) {

                    if(e.errorCode == 1)
                    {
                        var er ="" ;
                        if($.isArray(e.msg))
                        {

                            $.each(e.msg,function(key,msgs)
                            {
                                console.log(msgs);
                                er += msgs.split("\n");
                            });
                        }
                        else
                        {
                            er = e.msg;
                        }



                    }
                    else
                    {
                        displayMsg("success", e.msg);
                    }

                }
            });
        }
    });
    $("body").on("keyup", ".first_name", function (e)
    {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^A-Za-z ]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
    $("#agentCode,#tl_id,#am_id,#om_id").keyup(function ()
    {

        if ($(this).val().match(/[^\w\s]/gi))
        {
            $(this).val($(this).val().replace(/[^\w\s]/gi, ""));
        }
    });


    $(document).on('change','#lob',function(){

        var lob=$(this).val();
        $.ajax({
            url:"/tele_av_lob",
            type:"POST",
            dataType:'json',
            data:{lob:lob},
            success:function(result){

                console.log(result.process);
                console.log(result);

                $('#axis_process').val(result.axis_process);
            },error:function(){
                $('#axis_process').html("<option>No Data Found</option>");
            }
        });

    });




    function aduit_agent_mst(e){

        $('#get_current_agent_aduit').html('Please wait loading data....');
        var agent_id=e.id

        $.ajax({
            url:"/get_tls_agent_audit",
            type:"POST",
            data:{agent_id:agent_id},
            success:function(result){
                $('#get_current_agent_aduit').html(result);
            },error:function(){
                $('#get_current_agent_aduit').html('No Data Found');

            }
        });


    }

    $(document).on('click','#myModalaudit_mst_close',function(){
        $('#get_current_agent_aduit').html('');
    })


</script>
