<?php
// var_dump($payment_modes);exit;
?>

<div class="col-md-10 scroll" id="body">
    <div id="accordion3" class="according accordion-s2 mt-3">
        <input type="hidden" id="is_policy_created_hdn" value="<?php /*echo $is_policy_created */?>">
        <div class="card card-member">
            <div class="card-header card-vif"><a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion44" aria-expanded="false"><span class="lbl-card">Plan Details - <i class="ti-file"></i></a></div>
            <div id="accordion44" class="card-vis-mar collapse show" data-parent="#accordion2" style="">
                <form class="form-horizontal" id="form-plan" method="post" enctype="multipart/form-data" autocomplete="off">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Select Partner</label>
                                <div class="input-group">
                                    <select class="form-control" name="creditor" id="creditor">
                                        <option value="" selected disabled>Fyntune</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Type</label>
                                <div class="input-group">
                                    <select class="select2 form-control"  id='policy_type'name="policy_type[]">

                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
                                <div class="input-group">
                                    <select class="select2 form-control" id="policy_sub_type" name="policy_sub_type[]" multiple="multiple">

                                    </select>

                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Plan Name<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="plan_name" name="plan_name" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">

                                    <div class="error"><span class="planerror"></span></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Product Code</label>
                                    <input class="form-control" type="text" value="" id="product_code" name="product_code" autocomplete="off" >
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Group Codes</label>
                                    <input type="file" name="GroupCodeFile" id="GroupCodeFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" require>
                                </div>
                            </div>

                            <div class="col-md-3" style="margin-top: 20px;">
                                <a href="../assets/group_code_template.xlsx" class="btn download-btn" download="group_code_template.xlsx">Download Format </a>
                            </div>


                            <div class="control-group form-group col-md-8">
                                <label for="validationCustomUsername" class="col-form-label"><span>Payment Modes/Workflow<span class="lbl-star">*</span></span></label>
                                <div class="controls">
                                    <table id="tbl_paymentmodes" class="responsive display table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Payment Mode</th>
                                            <th>Workflow</th>
                                            <th class="text-center"><button type="button" class="addPaymentMode" style="border: none; background: none;"><i class="ti-plus"></i></button></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr class="paymentmode_tr">
                                            <td>
                                                <select class="select2 form-control" id="payment_modes" name="payment_modes[]" onchange="changeworkflow(this.value)" placeholder="Select">
                                                    <option value="">Select</option>

                                                </select>
                                            </td>
                                            <td>
                                                <select class="select2 form-control" id="payment_workflow" name="payment_workflow[]" placeholder="Select">
                                                    <!--<option value="">Select Payment Modes Applicable</option>-->
                                                    <option value="">Select</option>
                                                    <?php foreach ($payment_workflows as $workflow) { ?> <option value="<?php echo $workflow->payment_workflow_master_id; ?>"><?php  echo $workflow->workflow_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="remove" style="border: none; background: none;"><i class="fa fa-remove"></i></button>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>

                        <div class="row mt-4" id="addplanbtn" style="float: right">
                            <div class="col-md-1 col-6 mr-5">
                                <button class="btn smt-btn btn-success">Save</button>
                            </div>
                            <div class="col-md-2 col-6">
                                <!-- <button class="btn cnl-btn">Cancel</button> -->
                                <a href="<?php echo base_url(); ?>policy_configuration_fyntune" class="btn cnl-btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>


    <div id="accordion3" class="according accordion-s2 mt-3">
        <div class="card card-member">
            <div class="card-header card-vif">

                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion45" aria-expanded="false"> <span class="lbl-card">Policy Details - <i class="ti-file"></i></a>

            </div>
            <div id="accordion45" class=" card-vis-mar" data-parent="#accordion2" style="">
                <form class="form-horizontal" id="form-policy" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
                                <div class="input-group">
                                    <select id="policySubType" name="policySubType" class="form-control">

                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy #</label>
                                <div class="input-group">
                                    <input type="text" id="policyNo" name="policyNo" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                    <div class="error"><span class="policyerror"></span></div>

                                </div>
                            </div>






                                <div class="col-md-4" style="display:flex;">
                                    <div style="margin-top: 46px;"> <input type="radio" name="mandatory_optional" value="1" id="mandatory_option"> <label  for="mandatory_option"> Mandatory </label> </div>
                                    <div style="margin-top: 46px;"> <input type="radio"  name="mandatory_optional" value="0" id="optional_option"> <label  for="opttional_option"> Optional </label> </div>
                                    <div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option"> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
                                </div>


                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">PDF type</label>
                                <div class="input-group">
                                    <select id="pdf_type" name="pdf_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1">I</option>
                                        <option value="2">C1</option>
                                        <option value="3">C2</option>
                                    </select>

                                </div>
                            </div>



                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Insurer</label>
                                <div class="form-group">
                                    <select id="masterInsurance" name="masterInsurance" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1">Apollo</option>
                                        <option value="2">Tata</option>
                                        <option value="3">Religare</option>
                                        <option value="4">TATA- AIG</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Start Date</label>
                                <div class="input-group">
                                    <input class="form-control" type="date" value="" id="policyStartDate" name="policyStartDate" >

                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy end Date</label>
                                <div class="input-group">
                                    <input class="form-control" type="date" value="" id="policyEndDate" name="policyEndDate" >

                                </div>
                            </div>
                        </div>

                        <?php $this->load->view('tele_product_config/member_info' ) ?>



                        <div class="row col-md-12">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Sum Insured</label>
                                <div class="input-group">
                                    <select id="sum_insured_type" name="sum_insured_type" class="form-control valid" aria-describedby="sum_insured_type-error" aria-invalid="false">
                                        <option value=""> Select </option>
                                        <option value="individual">Individual</option>
                                        <option value="familyGroup">Family Cover</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Rater</label>
                                <div class="input-group">
                                    <select id="companySubTypePolicy" name="companySubTypePolicy" class="form-control valid" aria-invalid="false">
                                        <option value="">Select</option>
                                        <option value="flate">Flat Wise</option>
                                        <option value="family_construct">Family Construct Wise</option>
                                        <option value="family_construct_age">Family Construct and Age Wise</option>
                                        <option value="memberAge">Member Age Wise</option>
                                        <option value="permilerate" > Per Mile Rate </option>
                                        <option value="deductable"> Family Construct Deductable </option>
                                        <option value="perdaytenure"> Per Day Tenure  </option>

                                    </select>

                                </div>
                            </div>
                        </div>


                        <!-- flat -->
                        <div class="col-md-12" id="add_si_tbody" style="display: none;">
                            <div class="row lbl-body mt-1">
                                <div class="col-md-3 mb-3 col-12">
                                    <label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label>
                                    <div class="input-group">
                                        <input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off">

                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 col-12">
                                    <label for="validationCustomUsername" class="col-form-label">Enter Premium</label>
                                    <div class="input-group">
                                        <input type="number" placeholder="Premium" class="form-control" name="premium_opt[]" autocomplete="off" step="0.01">

                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 col-12">
                                    <label for="validationCustomUsername" class="col-form-label">Enter Group Code</label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off">

                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 col-12">
                                    <label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off">

                                    </div>
                                </div>
                                <div class="col-md-2 mb-3 col-12">
                                    <!-- <label style="visibility: hidden;" class="mt-3">label</label> -->
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="tax_opt[]" class="taxchk" autocomplete="off" value="1">
                                        <input type="hidden" class="tax_opt" name="tax_opt[]" value="0" />
                                        <!-- <label class="custom-control-label" for="istax">Is Taxable</label> -->
                                        <label for="istax"><b>Is Taxable</b></label>
                                    </div>
                                </div>

                                <div class="col-md-1 mb-3 col-12 text-right">
                                    <!-- <label style="visibility: hidden;" class="mt-2">label</label> -->
                                    <button type="button" class="btn add-btn" id="btn_add_si_flat" style="background: #d0f5ff;
    border: 1px dotted #107591;
    color: #107591;">Add <i class="ti-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- 2 upload  Upload Family Construct -->
                        <div class="col-md-12" id="fileUploadfamilyDiv" style="display: none;">
                            <div class=" row lbl-body">
                                <div class="col-md-4">
                                    <label class="col-form-label">Upload Family Construct</label>
                                    <br>
                                    <input type="file" name="ageFile" id="familyConstructFile">
                                </div>
                                <div class="col-md-1">
                                    <label class="mt-1" style="visibility: hidden;"></label>
                                    <button class="btn exp-button">
                                        <a href="../assets/familyExcel.xls" type="button" class="btn btn-danger btn-lg download-btn" download="familyExcel.xls">Download Format </a>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Upload family and age Construct -->
                        <div class="col-md-12" id="fileUploadfamilyageDiv" style="display: none;">
                            <div class=" row lbl-body">
                                <div class="col-md-4">
                                    <label class="col-form-label">Upload by family and age Construct</label>
                                    <br>
                                    <input type="file" name="ageFile" id="agefamilyConstructFile">
                                </div>
                                <div class="col-md-1">
                                    <label class="mt-1" style="visibility: hidden;"></label>
                                    <button class="btn exp-button">
                                        <a href="../assets/agefamilyExcel.xls" type="button" class="btn btn-danger btn-lg download-btn" download="agefamilyExcel.xls">Download Format </a>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload by Age -->
                        <div class="col-md-12" id="fileUploadAgeDiv" style="display: none;">
                            <div class=" row lbl-body">
                                <div class="col-md-4">
                                    <label class="col-form-label">Upload by Age</label>
                                    <br>
                                    <input type="file" name="ageFile" id="ageFile" class="">
                                </div>
                                <div class="col-md-1">
                                    <label class="mt-1" style="visibility: hidden;"></label>
                                    <button class="btn exp-button">
                                        <a href="../assets/ageExcel.xls"  type="button" class="btn btn-danger btn-lg download-btn" download="ageExcel.xls">Download Format </a>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 5 Upload by Age -->

                        <div id="fileUploadPerMileRate" style="<?php if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 5)) {
                            echo "display: block";
                        } else {
                            echo "display: none";
                        } ?>" class="col-md-12">
                            <div class=" row lbl-body">
                                <div class="col-md-4">
                                    <label class="col-form-label">Upload Per Mile Rate</label>
                                    <br>
                                    <input type="file" name="ageFile" id="ageFilepermile" class="form-control">
                                </div>
                                <div class="col-md-1">
                                    <label class="mt-1" style="visibility: hidden;"></label>
                                    <button class="btn add-btn">
                                        <a href="../assets/per_mile_rate.xlsx"  type="button" class="btn btn-danger btn-lg download-btn" download="per_mile_rate.xlsx">Download Format </a>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- new added -->

                        <!-- 6 Upload by Age -->
                        <div id="fileUploadFamilyDeductable" style="<?php if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 6)) {
                            echo "display: block";
                        } else {
                            echo "display: none";
                        } ?>" class="col-md-12">
                            <div class=" row lbl-body">
                                <div class="col-md-4">
                                    <label class="col-form-label">Family Construct Deductable</label>
                                    <br>
                                    <input type="file" name="ageFile" id="ageFileded" class="form-control">
                                </div>
                                <div class="col-md-1">
                                    <label class="mt-1" style="visibility: hidden;"></label>
                                    <button class="btn add-btn">
                                        <a href="../assets/familyExcelWithDeductable.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="familyExcelWithDeductable.xlsx">Download Format </a>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="perDayTenureDiv" style="<?php
                        if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 7)) {
                            echo "display: block";
                        } else {
                            echo "display: none";
                        } ?>" class="col-md-12">
                            <div class=" row lbl-body">
                                <div class="col-md-4">
                                    <label class="col-form-label">Per Day Tenure</label>
                                    <br>
                                    <input type="file" name="ageFile" id="ageFileperday" class="form-control">
                                </div>
                                <div class="col-md-1">
                                    <label class="mt-1" style="visibility: hidden;"></label>
                                    <button class="btn add-btn">
                                        <a href="../assets/per_day_tenure.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="per_day_tenure.xlsx">Download Format </a>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- new added -->

                        <div class="row col-md-12 mt-4" id="addpolicybtn">
                            <div class="col-md-1 col-12 text-left">
                                <input type="hidden " id="plan_id" name="plan_id" value="" style="display: none;" />

                                <button class="btn smt-btn btn-success" style="background: #18ba59;
    border: 3px solid #c8e6c9;
    color: #fff;">Save</button>
                            </div>
                            <div class="col-md-1 col-12 text-right">
                                <!-- <button class="btn cnl-btn">Cancel</button> -->
                                <a href="<?php echo base_url(); ?>products" class="btn cnl-btn" style="background: #f2581b; border: 3px solid #fbe9e7;  color: #fff;">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$select = "<option value=''>Select Member</option>";
foreach ($members as $member) {
    $select .= "<option value='$member->fr_id'>$member->fr_name</option>";
}
?>

<!--<script  type ="text/javascript" src="/public/assets/telesales_js/products.js?v=11"></script>-->
<script>
    var adult_members = JSON.parse('<?php echo json_encode($adult_members); ?>');
    console.log(adult_members);
    $('document').ready(function () {

        var hdnVal=$('#is_policy_created_hdn').val();
        if(hdnVal != 0){
            $("#form-plan :input").prop("disabled", true);
            getDataFromDatabase(hdnVal);
        }
        $.ajax({
            type: "POST",
            url: "tele_product_config/get_policy_creation_det",
            success: function (e) {
                e = JSON.parse(e);
                if (e.policyType) {
                    var policyType = e.policyType;
                    $("#policy_type").empty();
                    $("#policy_type").html("<option value=''> Select </option>");
                    policyType.forEach(function (e) {
                        if (e.policy_type_id != 3)
                            $("#policy_type").append("<option value='" + e.policy_type_id + "'>" + e.policy_name + "</option>");
                    });

                }
            },
            error: function () {

            }
        });
        getPaymentMode();
        document.getElementById('policyStartDate').valueAsDate = new Date(); //policyEndDate

        document.getElementById('policyEndDate').valueAsDate = fmt();


    });
    function fmt() {
        var someDate = new Date(new Date().getTime()+(364*24*60*60*1000)); //added 90 days to todays date
        return someDate;
    }
    function changeworkflow(){

    }
    function getPaymentMode(){
        $.ajax({
            type: "POST",
            url: "tele_product_config/getPaymentModes",
            success: function (e) {
                e = JSON.parse(e);
                var PayM = e;
                $("#payment_modes").empty();
                $("#payment_modes").html("<option value=''> Select </option>");
                PayM.forEach(function (e) {
                    $("#payment_modes").append("<option value='" + e.id + "'>" + e.payment_mode_name + "</option>");
                });

            },
            error: function () {

            }
        });
    }
    $("#policy_type").change(function () {

        $.post("tele_product_config/get_policy_subType_fyntune", { "policy_type_id": $("#policy_type").val() }, function (e) {

            e = JSON.parse(e);
            var policySubType = e.policySubType;
            $("#policy_sub_type").empty();
            $("#policy_sub_type").html("<option value=''>Select</option>");
            policySubType.forEach(function (e) {

                $("#policy_sub_type").append("<option value='" + e.policy_sub_type_id + "'>" + e.policy_sub_type_name + "</option>");
            });
            $("#policy_sub_type").select2( {
                    columns: 1,
                    placeholder: 'Select',
                    search: true
                }

            );
        });
    });
    $(".addPaymentMode").click(function() {
        var index = 1;
        $("#tbl_paymentmodes tbody tr.paymentmode_tr").each(function() {
            index = index + 1;
        });
        $html = '<tr class="paymentmode_tr">' +
            '<td>' +
            '<select class="select2 form-control" id="payment_modes' + index + '" name="payment_modes[]"  placeholder="Select">' +
            '<option value="">Select</option>' +
            <?php foreach ($payment_modes as $mode) { ?> '<option value="<?php echo $mode['id']; ?>"><?php echo $mode['payment_mode_name']; ?></option>' +
            <?php } ?> '</select>' +
            '</td>' +
            '<td>' +
            '<select class="select2 form-control" id="payment_workflow' + index + '" name="payment_workflow[]" placeholder="Select">' +
            '<option value="">Select</option>' +
            <?php foreach ($payment_workflows as $workflow) { ?> '<option value="<?php echo $workflow->payment_workflow_master_id; ?>"><?php  echo $workflow->workflow_name; ?></option>' +
            <?php } ?>
            '</select>' +
            '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="remove">' +
            '<i class="fa fa-remove"></i>' +
            '</button>' +
            "</td>" +
            "</tr>";
        $('#tbl_paymentmodes').find("tbody").append($html);
        $("#payment_modes" + index).select2();
        $("#payment_workflow" + index).select2();
    });
    $('#tbl_paymentmodes').on('click', '.remove', function() {
        var table_row = $('#tbl_paymentmodes tbody  tr.paymentmode_tr').length;
        if (table_row == '1') {
            alert("Atleast one mode is must. ");
        } else {
            $(this).closest('tr').remove();
        }
    });
   /* $('.membercount').on('change', function() {

        var count = $(this).val();
        var html = "";
        var select = "<?php echo $select; ?>";
        for (var i = 0; i < count; i++) {
            html += "<div class='col-sm-4 form-group'><select data-id='" + i + "'name='member[]' class='form-control memberselect'>" + select + "</select></div>";
            html += "<div class='col-sm-8 form-group'><input class='form-control agefield' type='number' placeholder='Min Age' min='1' max='100' name='minage[]'/> <input type='number' placeholder='Max Age'class='form-control agefield' min='1' max='100' name='maxage[]'/></div>";
        }
        $(".memberlist").html(html);
    });
*/
    $(document).on('change', '.memberselect', function() {
        var val = $(this).val();
        var ele = $(this).attr('data-id');
        console.log(ele);
        $('.memberselect').each(function() {
            var ele2 = $(this).attr('data-id');
            var ele3 = $(this);
            if (ele != ele2) {
                if (ele3.val() == val) {
                    ele3.val('');
                }
                ele3.find('[value="' + val + '"]').remove();
            }
        });

    });
    $("#btn_add_si_flat").click(function() {
        var newRow = "";
        newRow += '<div class="row lbl-body">';
        newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label><div class="input-group"><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"> </div></div>';
        newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Premium</label><div class="input-group"><input type="number" placeholder="Premium" class="form-control" name="premium_opt[]" autocomplete="off" step="0.01"></div></div>';
        newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Group Code</label><div class="input-group"><input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off"></div></div>';
        newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label><div class="input-group"><input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off"></div></div>';
        newRow += '<div class="col-md-2 mb-3 col-12"><div class="custom-control custom-checkbox form-check-inline"><input type="checkbox" class="taxchk" autocomplete="off"> <input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /><label for="istax">Is Taxable</label></div></div>';
        newRow += '<div class="del_btn_opt"><a>Delete<i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>';
        newRow += '</div>';

        $("#add_si_tbody").append(newRow);
    });
    $("body").on('click', ".del_btn_opt", function() {
        this.parentNode.remove();
    });
    $("#form-plan").submit(function(e) {



        e.preventDefault();
        var form = new FormData();
        form.append("policy_subtypes_id", $("#policy_sub_type").val());
        form.append("policy_subtypes_name", $("#policy_sub_type option:selected").val());
        form.append("policy_types_id", $("#policy_type").val());
        form.append("product_name", $("#plan_name").val());
        form.append("product_code", $("#product_code").val());
        form.append("payment_modes", $("#payment_modes").val());
        if($("#policy_sub_type").val()=="" || $("#policy_sub_type option:selected").val() == "" || $("#policy_type").val() == ""||
            $("#product_code").val() == "" || $("#payment_modes").val() ==""
        ){
            var title = 'Warning';
            var type = 'warning';
            var message = 'All fields are Required!';
            alert(message);

            return;
        }
        if ( document.getElementById("GroupCodeFile").files.length > 0) {
            form.append("filename", document.getElementById("GroupCodeFile").files[0]);
        } else {
            var title = 'Warning';
            var type = 'warning';
            var message = 'Please add file to upload.';
            alert(message);
            return;
        }
        $.ajax({
            url: "tele_product_config/save_tem_data",
            type: "POST",
            data: form,
            processData: false,
            contentType: false,
            cache: false,
            async: false,
            mimeType: "multipart/form-data",
            success: function (e) {
                var data_res = JSON.parse(e);

                var data = data_res.subtype;
                if(data_res == false){
                    var title = 'Warning';
                    var type = 'warning';
                    var message = 'product existing , please choose other product name.';
                    alert(message);
                }else{
                    var policyData=data_res.subtype;
                    var option='';
                    $.each(policyData, function (i) {
                        option += '<option value="'+ policyData[i].id+'">'+ policyData[i].policy_sub_type_name+'</option>';
                    });
                    var title = 'Success';
                    var type = 'success';
                    var message = 'Created Successfully.';
                    alert(message);
                    $("#policySubType").html(option);
                    $("#form-plan :input").prop("disabled", true);
                }
            }

        });
    });
    $("#companySubTypePolicy").on("change", function() {
        var str = $(this).val();
        $("#fileUploadPerMileRate").css("display", "none");
        $("#fileUploadFamilyDeductable").css("display", "none");
        $("#perDayTenureDiv").css("display", "none");
        if (str == "family_construct") {
            $("#fileUploadfamilyDiv").show();
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadfamilyageDiv").css("display", "none");

        } else if (str == "flate") {
            $("#add_si_tbody").css("display", "block");
            $("#fileUploadfamilyDiv").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadfamilyageDiv").css("display", "none");
        } else if (str == "family_construct_age") {
            $("#fileUploadfamilyDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").hide();
            $("#fileUploadfamilyageDiv").css("display", "block");
        } else if (str == "memberAge") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "permilerate") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadPerMileRate").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "deductable") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadFamilyDeductable").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "perdaytenure") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#perDayTenureDiv").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        }
    });

    $("#form-policy").submit(function(e) {
        var str = $("#companySubTypePolicy").val();
        e.preventDefault();
        var form_data = document.getElementById('form-policy');
        let formData = new FormData(form_data);
        formData.append("policy_type_id", $("#policy_type").val());
        if (str == "family_construct") {
            if ( document.getElementById("familyConstructFile").files.length > 0) {
                formData.append("filename", document.getElementById("familyConstructFile").files[0]);
                formData.append("fileUploadType", "byFamilyConstruct");
            } else {
                var title = 'Warning';
                var type = 'warning';
                var message = 'Please add file to upload.';

                alert(message);
                return;
            }

        }
        if (str == "family_construct_age") {
            if ( document.getElementById("agefamilyConstructFile").files.length > 0) {
                formData.append("filename", document.getElementById("agefamilyConstructFile").files[0]);
                formData.append("fileUploadType", "byFamilyAgeConstruct");
            } else {
                var title = 'Warning';
                var type = 'warning';
                var message = 'Please add file to upload.';
                alert(message);
                return;


            }

        }
        else if (str == "memberAge") {
            if ( document.getElementById("premiumfileUploadType").files.length > 0) {
                formData.append("premiumfileUploadType", "byMemberAge");
                formData.append("filename", document.getElementById("ageFile").files[0]);
            } else {
                var title = 'Warning';
                var type = 'warning';
                var message = 'Please add file to upload.';
                alert(message);
                return;
            }

        }  else if (str == "permilerate") {
            if ( document.getElementById("ageFilepermile").files.length > 0) {
                formData.append("ageFilepermile", "permilerate");
                formData.append("filename", document.getElementById("ageFilepermile").files[0]);
            } else {
                var title = 'Warning';
                var type = 'warning';
                var message = 'Please add file to upload.';
                alert(message);
                return;
            }

        } else if (str == "deductable") {
            if ( document.getElementById("ageFileded").files.length > 0) {
                formData.append("ageFileded", "deductable");
                formData.append("filename", document.getElementById("ageFileded").files[0]);
            } else {
                var title = 'Warning';
                var type = 'warning';
                var message = 'Please add file to upload.';
                alert(message);
                return;
            }

        }else if (str == "perdaytenure") {
            if ( document.getElementById("ageFileperday").files.length > 0) {
                formData.append("ageFileperday", "perdaytenure");
                formData.append("filename", document.getElementById("ageFileperday").files[0]);
            } else {
                var title = 'Warning';
                var type = 'warning';
                var message = 'Please add file to upload.';
                alert(message);
                return;
            }

        }
        if($("#policySubType").val()=="" ||  $("#policyNo").val() == ""||
            $("#pdf_type").val() == "" || $("#masterInsurance").val() =="" || $("#policyStartDate").val() ==""
            || $("#policyEndDate").val() ==""|| $(".membercount").val() ==""||  $("#sum_insured_type").val() ==""||
            $("#companySubTypePolicy").val() ==""
        ){
            var title = 'Warning';
            var type = 'warning';
            var message = 'All fields are Required!';
            alert(message);

            return;
        }
        $.ajax({
            url: "tele_product_config/AddPolicyNew",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            async: false,
            mimeType: "multipart/form-data",
            success: function (e) {
                var data_res = JSON.parse(e);
            
                if(data_res == false){
                    alert("product existing , please choose other product");
                }else if(data_res.status_code == 200){
                    var title = 'Success';
                    var type = 'success';
                    var message = 'Created Successfully.';
                    alert(message);

                    location.reload();
                }else{
                    alert("Something went wrong!");
                }
            }

        });
    });
</script>
<script>

    $(document).on('change', '.membercount', function () {
        let member_count = $(this).val();
        let current_adult_row_count = $("#adult-members-list .adult-row").length;

        if (current_adult_row_count > member_count) {
            $("#adult-members-list .adult-row:gt(" + (member_count - 1) + ")").remove();
        }

        if (!parseInt(member_count)) {
            $("#adult-members-list").hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
            return;
        }
        $("#adult-members-list").show();
        populateOptionsForEmptyAdults();
        if (canAddMoreAdultMembers()) {
            $('#add-another-member').show();
            $('#children-members-list').show();
            enableInnerFields('children-members-list');
        } else {
            $('#add-another-member').hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    })
    $(document).on('click', '#add-another-member', function () {
        let adult_row_html = $('.adultmembers .adult-row').html();
        $('.adultmembers').append(`<div class="row adult-row">${adult_row_html}</div>`);
        $('.adult-row').last().find(':input:not([type=hidden])').removeAttr('value');
        $('.adult-row').last().find('.adult-member-select').html('');
        populateOptionsForEmptyAdults();

        if (!canAddMoreAdultMembers()) {
            $(this).hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    });

    $(document).on('click', '.delete-member-btn', function () {
        let adult_row_count = $('.adult-row').length;

        if (adult_row_count <= 1) {
            alert("Policy should have atleast one member type");

            return;
        }
        $(this).closest('.adult-row').remove();
        if (canAddMoreAdultMembers()) {
            $('#add-another-member').show();
            $('#children-members-list').show();
            enableInnerFields('children-members-list');
        } else {
            $('#add-another-member').hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    });

    function canAddMoreAdultMembers() {
        let adult_row_count = $('.adult-row').length;
        let member_count = parseInt($('[name="membercount"]').val());

        return adult_row_count < member_count;
    }

    function updateCounters() {
        let adult_rows = $('.adult-row').length;
        $('#adult-count-display').val(adult_rows);

        let member_count = parseInt($('[name="membercount"]').val());
        let child_count = member_count - adult_rows;
        $('#kids-count-display').val(child_count);
    }

    function populateOptionsForEmptyAdults() {
        let selected_members = [];

        $('.adult-row .adult-member-select').each(function (index, element) {
            let value = $(this).val();
            if (selected_members.indexOf(value) === -1) {
                selected_members.push(value);
            }
        });

        let unselected_adult_members = adult_members.filter(function (member) {
            return selected_members.includes(member.id) == false;
        });

        $('.adult-row .adult-member-select').each(function (index, element) {

            let option_length = $(this).find('option').length;

            if (option_length != 0) {
                return;
            }

            let options_html = "";

            if (unselected_adult_members.length <= 0) {
                $('.adult-row').last().remove();
                displayMsg('error', 'No more member types available');
            }
            unselected_adult_members.forEach(member => {
                options_html += `<option value="${member.fr_id}">${member.fr_name}</option>`;
            });

            $(this).html(options_html);
        });
    }

</script>

