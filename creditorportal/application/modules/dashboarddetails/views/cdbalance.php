<?php //echo "<pre>";print_r($roles);exit;
//echo $_GET['cid'];exit;
//$_GET['cid'] = $_GET['cid'];
?>
<style type="text/css">
    #deposit_form .error{position: relative;top:0}

    @media (max-width: 425px) {
        .dataTables_length label {
            position: relative;
            right: 95%;
        }
    }

    /* .dataTables_length label{
        display: none;
    }

    .dataTables_info{
        display: none;
    }

    .dataTables_paginate{
        display: none;
    } */

    .align{
        /* margin-left: auto; */
    }
</style>
<script type="text/javascript" src='<?PHP echo base_url('assets/js/html2pdf.bundle.js', PROTOCOL); ?>'></script>
<!-- Center Section -->
<div class="col-md-10 d-none">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>CD Balance Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Partner Name</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <select class="form-control" onchange="getDataTable(this.value)">
                                <option value="" selected disabled>Select Partner Name</option>
                                <?php
                                foreach ($creditors as $Key=>$creditorsN){
                                    $selected='';

                                    echo $option='<option '.$selected.' value="'.$creditorsN['creditor_id'].'">'.$creditorsN['creaditor_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                   <!--  <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">SM Name</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <?php echo $_SESSION['webpanel']['employee_fname'] . ' ' . $_SESSION['webpanel']['employee_lname'];//echo "<pre>";print_r($creditors);exit;?>

                        </div>
                    </div> -->



                    <div class="col-md-2 col-6 text-left">
                        <label style="visibility: hidden;" class="mt-1">For Space</label>
                        <a href="<?php echo base_url();?>dashboarddetails/cdBalance?cid=<?php echo $_GET['cid'];?>&smid=<?php echo $_GET['smid'];?>"><button class="btn cnl-btn">Clear Search</button></a>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-8 col-6">
                        <p>Details</p>
                    </div>
                    <div class="col-md-4 col-6">

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 table-responsive scroll-table" style="height:300px;">
                    <table data-search="false" class=" table table-bordered non-bootstrap display pt-3 pb-3" id="table_lists">
                        <thead class="tbl-cre">
                        <tr>
                            <th data-bSortable="false">Partner Name</th>
                            <th data-bSortable="false">Initial CD</th>
                            <th data-bSortable="false">Total CD</th>
                            <th data-bSortable="false">COI Issued</th>
                            <th data-bSortable="false">Total Premium</th>
                            <th data-bSortable="false">CD Utilized</th>
                            <th data-bSortable="false">CD Balance</th>
                            <th data-bSortable="false">View Transactions</th>
                            <th data-bSortable="false">Add CD</th>
                            <th data-bSortable="false">CD Statement</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="DetailsModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow-x:scroll;">
                    <table class=" table table-bordered non-bootstrap display pt-3 pb-3" id="table_lists1">
                        <thead class="tbl-cre">
                        <tr>
                            <th data-bSortable="false">Partner Name</th>
                            <th data-bSortable="false">Product Name</th>
                            <th data-bSortable="false">Policy Type</th>
                            <th data-bSortable="false">Policy Sub Type</th>
                            <th data-bSortable="false">COI Issued</th>
                            <th data-bSortable="false">Total Premium</th>
                            <th data-bSortable="false">CD Utilised</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="AddCDModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="deposit_form" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Cd</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-4">
                                <label>Deposit Date</label>
                                <input type="date" class="form-control"  id="dep_date" name="dep_date"  onkeydown = "return false;" placeholder="Enter Deposit Date">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Amount</label>
                                <input type="number" class="form-control"  id="amount" name="amount"  placeholder="Enter Amount">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Chq/DD/Trans no</label>
                                <input type="text" class="form-control"  id="trans_no" name="trans_no"  placeholder="Enter Chq/DD/Trans no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label  class="mt-3">Bank Name</label>
                                <input type="text" class="form-control"  id="bankName" name="bankName"  placeholder="Enter Bank Name">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Bank Branch</label>
                                <input type="text" class="form-control"   id="branch" name="branch"  placeholder="Enter Bank Branch">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Payment Mode</label>
                                <input type="text" class="form-control"  id="payment_mode" name="payment_mode"  placeholder="Enter Payment Mode">
                            </div>
                        </div>
                        <input type="hidden" id="cred_id" name="cred_id">

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="CDstateModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="deposit_form" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">CD Statement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="credId" name="credId">
                        <div class="row col-md-12">
                            <label>Start Date</label>
                            <input class="form-control" type="date" id="start_date" name="start_date" onkeydown = "return false;">
                        </div>
                        <div class="row col-md-12">
                            <label>End Date</label>
                            <input class="form-control" type="date" id="end_date" max="<?=date('Y-m-d');?>" name="end_date" onkeydown = "return false;">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="ViewStatement(1)">Download PDF</button>
                        <button class="btn btn-primary" type="button" onclick="ViewStatement(2)">Download EXCEL</button>
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="col-md-9 align">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>CD Balance Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Partner Name</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <select class="form-control" onchange="getDataTable(this.value)">
                                <option value="" selected disabled>Select Partner Name</option>
                                <?php
                                foreach ($creditors as $Key=>$creditorsN){
                                    $selected='';

                                    echo $option='<option '.$selected.' value="'.$creditorsN['creditor_id'].'">'.$creditorsN['creaditor_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                   <!--  <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">SM Name</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <?php echo $_SESSION['webpanel']['employee_fname'] . ' ' . $_SESSION['webpanel']['employee_lname'];//echo "<pre>";print_r($creditors);exit;?>

                        </div>
                    </div> -->



                    <div class="col-md-2 col-6 text-left">
                        <label style="visibility: hidden;" class="mt-1">For Space</label>
                        <a href="<?php echo base_url();?>dashboarddetails/cdBalance?cid=<?php echo $_GET['cid'];?>&smid=<?php echo $_GET['smid'];?>"><button class="btn cnl-btn">Clear Search</button></a>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-8 col-6">
                        <p>Details</p>
                    </div>
                    <div class="col-md-4 col-6">

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 table-responsive scroll-table" style="height:300px;">
                    <table data-search="false" class=" table table-bordered non-bootstrap display pt-3 pb-3" id="table_lists">
                        <thead class="tbl-cre">
                        <tr>
                            <th data-bSortable="false">Partner Name</th>
                            <th data-bSortable="false">Initial CD</th>
                            <th data-bSortable="false">Total CD</th>
                            <th data-bSortable="false">COI Issued</th>
                            <th data-bSortable="false">Total Premium</th>
                            <th data-bSortable="false">CD Utilized</th>
                            <th data-bSortable="false">CD Balance</th>
                            <th data-bSortable="false">View Transactions</th>
                            <th data-bSortable="false">Add CD</th>
                            <th data-bSortable="false">CD Statement</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="DetailsModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow-x:scroll;">
                    <table class=" table table-bordered non-bootstrap display pt-3 pb-3" id="table_lists1">
                        <thead class="tbl-cre">
                        <tr>
                            <th data-bSortable="false">Partner Name</th>
                            <th data-bSortable="false">Product Name</th>
                            <th data-bSortable="false">Policy Type</th>
                            <th data-bSortable="false">Policy Sub Type</th>
                            <th data-bSortable="false">COI Issued</th>
                            <th data-bSortable="false">Total Premium</th>
                            <th data-bSortable="false">CD Utilised</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="AddCDModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="deposit_form" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Cd</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-4">
                                <label>Deposit Date</label>
                                <input type="date" class="form-control"  id="dep_date" name="dep_date"  onkeydown = "return false;" placeholder="Enter Deposit Date">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Amount</label>
                                <input type="number" class="form-control"  id="amount" name="amount"  placeholder="Enter Amount">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Chq/DD/Trans no</label>
                                <input type="text" class="form-control"  id="trans_no" name="trans_no"  placeholder="Enter Chq/DD/Trans no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label  class="mt-3">Bank Name</label>
                                <input type="text" class="form-control"  id="bankName" name="bankName"  placeholder="Enter Bank Name">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Bank Branch</label>
                                <input type="text" class="form-control"   id="branch" name="branch"  placeholder="Enter Bank Branch">
                            </div>
                            <div class="col-md-4">
                                <label  class="mt-3">Payment Mode</label>
                                <input type="text" class="form-control"  id="payment_mode" name="payment_mode"  placeholder="Enter Payment Mode">
                            </div>
                        </div>
                        <input type="hidden" id="cred_id" name="cred_id">

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="CDstateModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="deposit_form" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">CD Statement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="credId" name="credId">
                        <div class="row col-md-12">
                            <label>Start Date</label>
                            <input class="form-control" type="date" id="start_date" name="start_date" onkeydown = "return false;">
                        </div>
                        <div class="row col-md-12">
                            <label>End Date</label>
                            <input class="form-control" type="date" id="end_date" max="<?=date('Y-m-d');?>" name="end_date" onkeydown = "return false;">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="ViewStatement(1)">Download PDF</button>
                        <button class="btn btn-primary" type="button" onclick="ViewStatement(2)">Download EXCEL</button>
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Center Section End-->
<script type="text/javascript">
    $('#CDstateModal').on('hidden.bs.modal', function () {
        $(':input', this).val('');
    });
    function ViewStatement(id){
   //     alert('1');
///dashboarddetails/OpenPdfNew?c_id='.$_POST['cid'].'
        var credId=$('#credId').val();
        var start_date=$('#start_date').val();
        var end_date=$('#end_date').val();
        if(start_date == '' || end_date == ''){
            alert('Start date and End date are mandatory');
            return;
        }
        // window.location.href = "dashboarddetails/OpenPdfNew?c_id="+credId+"&stDate="+start_date+"&endDate="+end_date;
        if(id==1){
            window.open("dashboarddetails/OpenPdfNew?c_id="+credId+"&stDate="+start_date+"&endDate="+end_date, '_blank');
        }else{
            window.open("dashboarddetails/OpenExcelNew?c_id="+credId+"&stDate="+start_date+"&endDate="+end_date);

        }

    }
    function goBack() {
        window.history.back();
    }

    function exportResults(){
        var creditor_id = '<?php echo $_GET['cid'];?>';
        var sm_id = '<?php echo $_GET['smid'];?>';
        var status = $("#sSearch_0").val();


        //alert("creditor_id: "+creditor_id+ "sm_id:"+sm_id+" status:"+status);
        //return false;

        $.ajax({
            url: "<?php echo base_url().$this->router->fetch_module();?>/dashboarddetails/exportexcel",
            async: false,
            type: "POST",
            dataType: 'json',
            data: {creditor_id:creditor_id, status:status, sm_id:sm_id},
            success: function (response){
                if(response.success)
                {
                    displayMsg("success",response.msg);
                    window.open(response.Data,'_blank' );
                }
                else
                {
                    displayMsg("error",response.msg);
                    return false;
                }
            }
        });


    }
    document.title = "CD Balance Details";

    function DownloadCOI(lead_id) {
        //    ajaxindicatorstart("Downloading...");
        $.ajax({
            url: "/quotes/coidownload",
            type: "POST",
            data: {
                'lead_id': lead_id,
            },
            dataType: 'html',
            success: function(response) {
                html2pdf()
                    .set({
                        filename: 'coi_' + lead_id + '.pdf'
                    })
                    .from(response)
                    .save();
                setTimeout(function() {
                    ajaxindicatorstop();
                }, 5000);
            }
        });
    }
    function getDataTable(value) {
        /*let formData = new FormData();
        formData.set("company_name", company_name);
        formData.set("branch_id", branch_id);*/


        $.ajax({
            url: "/dashboarddetails/fetchcdBalance",
            type: "POST",
            data: {
                'cid': value,
                'smid': <?php echo $sm[0]['employee_id'];?>,
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
// debugger;
                $("#table_lists").DataTable({

                    destroy: true,
                    "searching": false,
                    order: [],
                    data:res.aaData,
                    "pagingType": "simple_numbers",

                    columns:[

                        {data: 0},
                        {data: 1},
                        {data: 2},
                        {data: 3},
                        {data: 4},
                        {data: 5},
                        {data: 6},
                        {data: 7},
                        {data: 8},
                        {data: 9},

                    ]
                });
            }
        });
    }

    function ViewDetails(creditor_id='') {
        $("#DetailsModal").modal('show');
        $.ajax({
            url: "/dashboarddetails/fetchcdBalanceTrans",
            type: "POST",
            data: {
                'cid': creditor_id,
                'smid': <?php echo $sm[0]['employee_id'];?>,
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
// debugger;
                $("#table_lists1").DataTable({

                    destroy: true,
                    order: [],
                    data:res.aaData,
                    "pagingType": "simple_numbers",

                    columns:[

                        {data: 0},
                        {data: 1},
                        {data: 2},
                        {data: 3},
                        {data: 4},
                        {data: 5},
                        {data: 6}

                    ]
                });
            }
        });
    }
    function ViewModal(creditor_id) {
        $("#AddCDModal").modal('show');
        $("#cred_id").val(creditor_id);
    }
</script>
<script>

    var vRules1 = {
        dep_date: {
            required: true
        },
        amount: {
            required: true
        },
        trans_no: {
            required: true
        },
        bankName: {
            required: true
        },
        branch: {
            required: true
        },
        payment_mode: {
            required: true
        },

    };

    $("#deposit_form").validate({
        rules: vRules1,
        errorElement:"span",
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>dashboarddetails/addDepositePartner";

            $("#deposit_form").ajaxSubmit({
                url: act,
                type: 'post',
                dataType: 'json',
                cache: false,
                clearForm: false,
                success: function(response) {
                    if (response.status_code==200) {
                        alert(response.Metadata.Message);
                        $('#deposit_form').trigger("reset");
                        getDataTable($("#cred_id").val())
                    } else {
                        alert(response.Metadata.Message);
                    }

                }
            });
        }
    });
    function openModal(id) {
        $("#CDstateModal").modal('show');
        $("#credId").val(id);
    }

    
    // Function to reset the form fields
    function resetForm() {
      $('#deposit_form')[0].reset();
    }

    // Event listener for modal close button
    $('#AddCDModal').on('hidden.bs.modal', function (e) {
      resetForm();
    });
</script>