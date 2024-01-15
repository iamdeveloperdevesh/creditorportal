<style>
    .modal-footer>:not(:last-child){
        margin-right: 0px;
    }
    
</style>

<?php //echo "<pre>";print_r($roles);exit;
//echo $_GET['cid'];exit;
//$_GET['cid'] = $_GET['cid'];
?>
<script type="text/javascript" src='<?PHP echo base_url('assets/js/html2pdf.bundle.js', PROTOCOL); ?>'></script>
<!-- Center Section -->
<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Cover Balance Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Channel Partner</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <select class="form-control" id = "creditor" >
                                <option value="" selected disabled>Select Partner</option>
                                <?php
                                foreach ($creditors as $Key=>$creditorsN){
                                    $selected='';

                                    echo $option='<option '.$selected.' value="'.$creditorsN['creditor_id'].'">'.$creditorsN['creaditor_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Plan</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <select class="form-control" id= "plans" onchange="getPlanDataTable(this.value)">
                                <option value="" selected disabled>Select Plan</option>

                            </select>
                        </div>
                    </div>
                    <!-- <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">SM Name</label>
                        <div class="dataTables_filter input-group inp-frame">
                            <?php echo $sm[0]['employee_full_name'];//echo "<pre>";print_r($creditors);exit;?>

                        </div>
                    </div> -->



                    <div class="col-md-2 col-6 text-left">
                        <label style="visibility: hidden;" class="mt-1">For Space</label>
                        <a href=""><button class="btn cnl-btn">Clear Search</button></a>
                        <!-- <a href="<?php echo base_url();?>dashboarddetails?cid=<?php echo $_GET['cid'];?>&smid=<?php echo $_GET['smid'];?>"><button class="btn cnl-btn">Clear Search</button></a> -->
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
                    <table class=" table table-bordered non-bootstrap display pt-3 pb-3" id="table_lists">
                        <thead class="tbl-cre">
                        <tr>
                            <th data-bSortable="false">Partner Name</th>
                            <th data-bSortable="false">Plan Name</th>
                            <th data-bSortable="false">Policy Sub Type </th>
                            <th data-bSortable="false">Initial Cover Limit</th>
                            <th data-bSortable="false">Total Cover Limit</th>
                            <th data-bSortable="false">COI Issued</th>
                            <th data-bSortable="false">Cover Utilized</th>
                            <th data-bSortable="false">Cover Balance</th>
                            <th data-bSortable="false"> Enhance Cover Limit</th>
                            <th data-bSortable="false">Cover Statement</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


<div class="modal" id="AddCoverModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="deposit_form" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Enhance Cover Limit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">

                        <label>Date</label>
                            <input type="date" class="form-control" id="dep_date" name="dep_date" onkeydown="return false;" placeholder="DD-MM-YYYY" tabindex="51">

                        </div>
                        <div class="col-md-4">
                            <label>Amount</label>
                            <input type="number" class="form-control" required id="amount" name="amount" placeholder="Enter amount">
                        </div>


                        <input type="hidden" id="cred_id" name="cred_id">
                        <input type="hidden" id="plan_cover_id" name="plan_cover_id">
                        <input type="hidden" id="policy_cover_id" name="policy_cover_id">


                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                        <!-- <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button> -->

                    </div>
            </form>
        </div>
    </div>
</div>


</div>
<div class="modal" id="CoverstateModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="deposit_form" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">cover statement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>


                </div>
                <div class="modal-body">
                    <input type="hidden" id="credId" name="credId">
                    <input type="hidden" id="plan_state_id" name="plan_state_id">
                    <input type="hidden" id="policy_state_id" name="policy_state_id">
                    <div class="row col-md-12">
                        <label>Start Date</label>
                        <input class="form-control" type="date" id="start_date" name="start_date">
                    </div>
                    <div class="row col-md-12">
                        <label>End Date</label>
                        <input class="form-control" type="date" id="end_date" max="<?=date('Y-m-d');?>" name="end_date">
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
<!-- Center Section End-->
<script type="text/javascript">
    function ViewStatement(id){
        //     alert('1');
///dashboarddetails/OpenPdfNew?c_id='.$_POST['cid'].'
        var credId=$('#credId').val();
        var plan_state_id=$('#plan_state_id').val();
        var policy_state_id=$('#policy_state_id').val();
        var start_date=$('#start_date').val();
        var end_date=$('#end_date').val();
        if(start_date == '' || end_date == ''){
            alert('Start date and End date are mandatory');
            return;
        }
        // window.location.href = "dashboarddetails/OpenPdfNew?c_id="+credId+"&stDate="+start_date+"&endDate="+end_date;
        if(id==1){
            window.open("dashboarddetails/OpenPdfCoverNew?c_id="+credId+"&stDate="+start_date+"&endDate="+end_date+"&plan_state_id="+plan_state_id+"&policy_state_id="+policy_state_id, '_blank');
        }else{
            window.open("dashboarddetails/OpenExcelCoverNew?c_id="+credId+"&stDate="+start_date+"&endDate="+end_date+"&plan_state_id="+plan_state_id+"&policy_state_id="+policy_state_id);

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

    document.title = "Cover Limit Details";


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
    function getPlanDataTable(value) {
        /*let formData = new FormData();
        formData.set("company_name", company_name);
        formData.set("branch_id", branch_id);*/


        $.ajax({
            url: "/dashboarddetails/fetchcoverBalance",
            type: "POST",
            data: {
                'plan_id': value,
                'cid': $('#creditor').val(),
                'smid': <?php echo $sm[0]['employee_id'];?>,
            },
            dataType: 'json',
            success: function(res) {

                console.log(res);
                debugger;
                console.log(res);
// debugger;
                $("#table_lists").DataTable({

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
    function ViewModal(creditor_id,plan_id,policy_id) {
        // alert(plan_id);
        $("#AddCoverModal").modal('show');
        $("#cred_id").val(creditor_id);
        $("#plan_cover_id").val(plan_id);
        $("#policy_cover_id").val(policy_id);
    }
</script>
<script>

    var vRules1 = {
        policyNo: {
            dep_date: true
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
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>dashboarddetails/addDepositecover";

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
    function openstateModal(id,plan_id,policy_id) {
        $("#CoverstateModal").modal('show');
        $("#credId").val(id);
        $("#plan_state_id").val(plan_id);
        $("#policy_state_id").val(policy_id);
    }
    $(document).on("change","#creditor",function(){
        var creditor_id = $(this).val();
        $.ajax({
            url: "/features/fetch_plans",
            type: "POST",
            async: false,
            data: {"creditor_id":creditor_id},
            dataType: "json",
            success: function(response) {
                // debugger;
                console.log(response);
                $("#plans").html('').append(response.html);

            }
        });
    });

     // for start Date
    const startDate = document.getElementById('start_date');
    startDate.addEventListener('keydown', function (event) {
        event.preventDefault();
    });
    startDate.addEventListener('paste', function (event) {
        event.preventDefault();
    });

    // for end Date
    const endDate = document.getElementById('end_date');
    endDate.addEventListener('keydown', function (event) {
    event.preventDefault();
    });
    endDate.addEventListener('paste', function (event) {
    event.preventDefault();
    });
</script>