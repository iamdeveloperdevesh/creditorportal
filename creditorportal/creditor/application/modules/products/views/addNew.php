<?php
/*if(isset($datalist->plan_id)){
echo "<pre>";print_r($datalist);exit;
}*/
?>
<!-- start: Content -->

<style>
    .select2-container-multi .select2-choices .select2-search-field input {
    padding: 0px!important; 
     margin: 1px 0!important; 
    font-family: sans-serif;
    font-size: 100%;
    color: #666;
    outline: 0;
    border: 0;
    -webkit-box-shadow: none;
    box-shadow: none;
    background: transparent !important;
}
.error{color:red;}
.label-primary {
    padding: 5px;
}
.collapse.in {
    display: block;
}
.collapse {
    display: none;
    padding:15px;
}
.card .card-header {
    background-color: #e6e6e6;
    padding: 5px;
    margin-top: 15px;
}
.select2-container-multi .select2-choices {
    background-image: none !important;
    border: 0!important;
}
.agefield{
    margin-left: 15px;
    width: 120px;
    float: left;
}
</style>

<div class="page-body" id="body">
	<div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <div class="page-header-left">
                        <h3>Add Product</h3>
                    </div>
                </div>
                <div class="col-lg-6">
                    <ol class="breadcrumb pull-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url();?>products/addnewview"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item active">Add Plan</li>
                    </ol>
                </div>
            </div>
        </div>
      
    <div class="card">       
        <div class="card-body">             
            <div class="box-content">
                <?php if(!isset($datalist->plan_id)){ ?>
                    <div class="card-header" id="headingOne">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Plan Details
                    </button>
                    </div>
                    <div id="collapseOne" class="collapse in" aria-labelledby="headingOne" data-parent="#accordionExample">
                   		<form class="form-horizontal" id="form-plan" method="post" enctype="multipart/form-data" autocomplete="off">
                        	<div class="row">
                        	    <div class="col-sm-4">
                        	        <div class="form-group">
            						    <label>Select Partner</label>
            							<select class="form-control" name="creditor">
        							        <?php foreach($datalist->creditors as $creditor){ ?>
            							        <option value="<?php echo $creditor->creditor_id; ?>"><?php echo $creditor->creaditor_name; ?></option>
            							    <?php } ?>
            							</select>
            						</div>
                        	    </div>
                        	    <div class="col-sm-4">
                        	        <div class="form-group">
            						    <label>Select Policy Type</label>
            							<select class="select2 form-control" name="policy_sub_type[]" multiple="multiple">
            							    <?php foreach($datalist->policysubtypes as $subtype){ ?>
            							        <option value="<?php echo $subtype->policy_sub_type_id; ?>"><?php echo $subtype->policy_sub_type_name; ?></option>
            							    <?php } ?>
            							</select>
            						</div>
                        	    </div>
                        	    <div class="col-sm-4">
                        	        <div class="form-group">
            						    <label>Plan Name</label>
            							<input class="form-control planname" type="text" name="plan_name" value="" placeholder="" autocomplete="off" />
            							<div class="error"><span class="planerror"></span></div>
            						</div>
                        	    </div>
                        	</div>
                        	<div class="row">
                        	    <div class="col-sm-4">
                        	        <div class="form-group">
            						    <label>Payment Modes Applicable</label>
            						    <select class="select2 form-control" name="payment_modes[]" multiple="multiple">
            							<?php foreach($datalist->payment_modes as $mode){ ?>
        							        <option value="<?php echo $mode->payment_mode_id; ?>"><?php echo $mode->payment_mode_name; ?></option>
        							    <?php } ?>
        							    </select>
            						</div>
                        	    </div>
                        	   
                        	</div>
    						<div class="row">
                        	    
                        	   <div class="col-sm-6">
                        	       <div class="form-group">
                        	           <button type="submit" class="btn btn-success btn-lg" id="addplanbtn">Submit</button>
                    	           </div>
                        	   </div>
                        	</div>
                        </form>
                        
                    </div>
                    <div class="clearfix"></div>
            <?php } else { ?>    
                <div class="col-sm-12">
                    <div class="card-header" id="headingOne">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Plan Details
                    </button>
                    </div>
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="row">
                        <div class="col-sm-4">
                            <h4>Plan Name</h4>
                            <label class="label label-primary"><?php echo $datalist->details[0]->plan_name; ?></label>
                        </div>
                        <div class="col-sm-5">
                            <h4>Policy Types </h4>
                            <?php foreach($datalist->details as $detail){ ?>
                            <label class="label label-primary"><?php echo $detail->policy_sub_type_name; ?> </label><br>
                            <?php } ?>
                        </div>
                        <div class="col-sm-3">
                            <h4>Partner Name </h4>
                            <label class="label label-primary"><?php echo $datalist->details[0]->creditor_name; ?></label>
                        </div>
                        </div>
                        
                    </div>
                </div>
            
                <div class="col-sm-12">
                    <div class="card-header" id="headingTwo">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        Policy Details
                    </button>
                    </div>
                    <div id="collapseTwo" class="collapse in" aria-labelledby="headingTwo" data-parent="#accordionExample">
                   		<form class="form-horizontal" id="form-policy" method="post" enctype="multipart/form-data">
                   		    
                        	<div class="row">
                                   <div class="col-md-4">
                                      <div class="form-group">
                                         <label class="col-form-label">Policy Sub Type </label> 
                                         <select id="policySubType" name="policySubType" class="form-control">
                                            
                                			<?php 
                                			$combocount = 0;$mandate = 0;$inactivecount = 0;
                                			foreach($datalist->details as $detail){
                                				if($detail->is_combo == 1){$combocount++;}
                                				if($detail->is_optional == 0){$mandate++;}
                                				if(empty($detail->policy_number)){
                                				$inactivecount++;
                                			?>
                                            <option value="<?php echo $detail->policy_id; ?>"><?php echo $detail->policy_sub_type_name; ?></option>
                                			<?php }} ?>
                                         </select>
                                      </div>
                                   </div>
                                   <div class="col-md-4">
                                      <div class="form-group"> 
                                          <label class="col-form-label">Policy # </label> 
                                          <input class="form-control policyno" type="text" value="" id="policyNo" name="policyNo"> 
                                          <div class="error"><span class="policyerror"></span></div>
                                      </div>
                                   </div>
                                	<?php if(count($datalist->details) == 1){ ?>
                                	<div class="col-md-4" style="display:flex;">
                                	<input type="checkbox" class="form-check-input custom-control-input hidden" name="mandatory" value="1" id="mandatory_option" checked>
                                	</div>
                                	<?php } else {
                                		if($inactivecount == 1 && $combocount == 1){
                                	?>
                                	<input type="checkbox" class="form-check-input custom-control-input hidden" name="combo" value="1" id="Combo_option" checked>
                                	<?php } if($inactivecount == 1 && $mandate == 0 && $combocount == 0){ ?>
                                	<input type="checkbox" class="form-check-input custom-control-input hidden" name="mandatory" value="1" id="mandatory_option" checked>
                                	<?php } if($inactivecount == 1 && $mandate > 0 && $combocount == 0){ ?>
                                	<div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option"> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
                                    <?php } if($inactivecount == 1 && $combocount > 1){ ?>
                                	<div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option"> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
                                    <div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option"> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
                                	<?php } if($inactivecount > 1){ ?>
                                	<div class="col-md-4" style="display:flex;">
                                	<div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option"> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
                                    <div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option"> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
                                	 </div>
                                	<?php } } ?>
                                     
                                   <div class="col-md-4">
                                      <div class="form-group">
                                         <label class="col-form-label">PDF type</label> 
                                         <select id="pdf_type" name="pdf_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1">I</option>
                                            <option value="2">C1</option>
                                            <option value="3">C2</option>
                                         </select>
                                      </div>
                                   </div>
								   <div class="col-md-4">
                                      <div class="form-group">
                                         <label class="col-form-label">Premium Type</label> 
                                         <select id="premium_type" name="premium_type" class="form-control">
                                            <option value="1">Absolute</option>
                                            <option value="0">Per Mile rate</option>
                                         </select>
                                      </div>
                                   </div>
                                   <div class="col-md-4">
                                      <div class="form-group">
                                         <label class="col-form-label">Insurer</label> 
                                         <select id="masterInsurance" name="masterInsurance" class="form-control">
                                            <option value="">Select</option>
                                			<?php foreach($datalist->insurers as $insurer){?>
                                            <option value="<?php echo $insurer->insurer_id; ?>"><?php echo $insurer->insurer_name; ?></option>
                                			<?php } ?>
                                         </select>
                                      </div>
                                   </div>
                                   
                                   <div class="col-md-4">
                                      <div class="form-group"> <label class="col-form-label">Policy Start Date</label> <input class="form-control imgDatepicker datepicker" type="text" value="" id="policyStartDate" name="policyStartDate" autocomplete="off"> </div>
                                   </div>
                                   <div class="col-md-4">
                                      <div class="form-group"> <label class="col-form-label">Policy End Date</label> <input class="form-control imgDatepicker datepicker" type="text" value="" id="policyEndDate" name="policyEndDate" autocomplete="off"> </div>
                                   </div>
                                   
                                </div>
    						<div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Policy Member Count</label>
                                        <input type="number" name="membercount" class="form-control membercount" />
                                    </div>
                                </div>
                            </div>
                            <div class="row memberlist">
                                
                            </div>
                            <div class="row">
                                   <div class="col-md-6">
                                      <div class="form-group">
                                         <label class="col-form-label">Sum Insured Type</label> 
                                         <select id="sum_insured_type" name="sum_insured_type" class="form-control valid" aria-describedby="sum_insured_type-error" aria-invalid="false">
                                            <option value=""> Select </option>
                                			<?php foreach($datalist->sitypes as $type){ ?>
                                            <option value="<?php echo $type->suminsured_type_id ;?>"> <?php echo $type->suminsured_type ;?> </option>
                                			<?php } ?>
                                         </select>
                                      </div>
                                   </div>
                                   <div class="col-md-6">
                                      <div class="form-group">
                                         <label class="col-form-label">SI Basis</label> 
                                         <select id="companySubTypePolicy" name="companySubTypePolicy" class="form-control valid" aria-invalid="false">
                                            <option value="">Select</option>
                                            <?php foreach($datalist->sipremiumbasis as $premium){ ?>
                                            <option value="<?php echo $premium->si_premium_basis_id ;?>"> <?php echo $premium->si_premium_basis ;?> </option>
                                			<?php } ?>
                                         </select>
                                      </div>
                                   </div>
                                   <div id="fileUploadAgeDiv" style="display: none;" class="col-md-12">
                                      <div class="row">
                                         <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group"> 
                                               <label>Upload by Age</label> 
                                               <input type="file" name="ageFile" id="ageFile" class="form-control"> 
                                            </div>
                                         </div>
                                         <div class="col-md-6" style="margin-top:30px;"> 
                                            <a href="/assets/ageExcel.xls" class="btn btn-danger btn-lg download-btn" download="ageExcel.xls">Download Format </a> 
                                         </div>
                                      </div>
                                   </div>
                                   <div id="fileUploadfamilyDiv" style="display: none;" class="col-md-12">
                                      <div class="row">
                                         <div class="col-md-6" style="margin-top: 12px;">
                                            <div class="form-group"> 
                                                <label>Upload by family Construct</label> 
                                               <input type="file" name="familyConstructFile" class="form-control" id="familyConstructFile"> 
                                            </div>
                                         </div>
                                         <div class="col-md-6" style="margin-top: 20px;"> 
                                            <a href="/assets/familyExcel.xlsx" class="btn btn-danger btn-lg download-btn" download="ageExcel.xlsx">Download Format </a> 
                                         </div>
                                      </div>
                                   </div>
                                   <div id="fileUploadfamilyageDiv" style="display: none;" class="col-md-12">
                                      <div class="row">
                                         <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group"> 
                                               <label>Upload by family and age Construct</label> 
                                               <input type="file" name="agefamilyConstructFile" class="form-control" id="agefamilyConstructFile"> 
                                            </div>
                                         </div>
                                         <div class="col-md-6" style="margin-top: 20px;">
                                            <a href="/assets/agefamilyExcel.xls" class="btn btn-danger btn-lg download-btn" download="agefamilyExcel.xls">Download Format </a> 
                                         </div>
                                      </div>
                                   </div>
                                   <div class="col-md-12">
                                      <table id="tableSiflat" class="responsive-table" style="margin: 5px 5px 5px 13px; display: none;" border="0">
                                         <tbody id="add_si_tbody">
                                            <tr id="add_tr" class="col-md-12 mt-2 mb-2">
                                               <td style="text-align: right;" class="row mt-3">
                                                  <div class="col-md-5"> <label style="font-size: 13px; float: left;">Enter Sum Insured</label>  <input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"> </div>
                                                  <div class="col-md-5"> <label style="font-size: 13px; float: left;">Enter Premium</label>  <input type="number" placeholder="Premium" class="form-control" name="premium_opt[]" autocomplete="off">  </div>
                                                  <div class="col-md-2"> <label style="font-size: 13px; float: left;">Is Taxable</label>  <input type="checkbox" class="form-control taxchk" autocomplete="off"><input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /> </div>
                                                </td>
                                               <td id="btn_add_si_flat" style="width:15%;text-align: right;"><a><i class="fa fa-plus" aria-hidden="true"></i></a>  </td>
                                            </tr>
                                         </tbody>
                                      </table>
                                   </div>
                                   
                                   <br> 
                                   <div id="fileUploadperMiliDiv" style="display: none;" class="col-md-3">
                                      <div class="form-group"> <label for="">Upload by Per Milli Rates</label> <input type="file" name="perMiliFile" id="perMiliFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"> </div>
                                      <a href="/public/assets/perMili.xlsx" class="btn download-btn" download="perMili.xlsx">Download Format </a> 
                                   </div>
                                </div>
                                <?php 
                             $select = "<option value=''>Select Member</option>";
                             
                             foreach($datalist->members as $member){ 
                                $select .= "<option value='$member->id'>$member->member_type</option>";
                                }
                            ?>
                            <script>
                            $('.membercount').on('change', function(){
                                var count = $(this).val();
                                var html = "";
                                var select = "<?php echo $select; ?>";
                                for(var i = 0; i < count; i++){
                                    html += "<div class='col-sm-4 form-group'><select data-id='"+i+"'name='member[]' class='form-control memberselect'>"+select+"</select></div>";
                                    html += "<div class='col-sm-8 form-group'><input class='form-control agefield' type='number' placeholder='Min Age' min='1' max='100' name='minage[]'/> <input type='number' placeholder='Max Age'class='form-control agefield' min='1' max='100' name='maxage[]'/></div>";
                                }
                                $(".memberlist").html(html);
                            });
                            $("#btn_add_si_flat").click(function() {
                                var cols = "";
                                var newRow = $("<tr>");
                                cols += '<td style="text-align: right;" class="row mt-4"> <div class="col-md-5"><label style="font-size: 13px; float: left;">Enter Sum Insured</label><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"></div><div class="col-md-5"><label  style="font-size: 13px; float: left;">Enter Premium</label><input type="number" placeholder="premium" class="form-control" name="premium_opt[]" autocomplete="off"> </div><div class="col-md-2"><label  style="font-size: 13px; float: left;">Is Taxable</label>  <input type="checkbox" class="form-control taxchk"><input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /> </div></td> ';
                                cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;" ><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
                                newRow.append(cols);
                                $("#add_si_tbody").append(newRow);
                            });
                            $("body").on('click', ".del_btn_opt", function() {
                                this.parentNode.remove();
                            });
                            $(document).on('change', '.memberselect', function() {
                                var val = $(this).val();
                                var ele = $(this).attr('data-id');
                                console.log(ele);
                                $('.memberselect').each(function(){
                                    var ele2 = $(this).attr('data-id');
                                    var ele3 = $(this);
                                    if(ele != ele2){
                                    if(ele3.val() == val){
                                        ele3.val('');
                                    }
                                    ele3.find('[value="'+val+'"]').remove();
                                    }
                                });
                                
                            });
                            
                                $("#companySubTypePolicy").on("change", function() {
                                    var str = $(this).val();
                                    if (str == "2") {
                                        $("#fileUploadfamilyDiv").show();
                                        $("#tableSiflat").css("display", "none");
                                        $("#fileUploadAgeDiv").css("display", "none");
                                        $("#fileUploadfamilyageDiv").css("display", "none");
                                    } else if (str == "1") {
                                        $("#tableSiflat").css("display", "block");
                                        $("#fileUploadfamilyDiv").css("display", "none");
                                        $("#fileUploadAgeDiv").css("display", "none");
                                        $("#fileUploadfamilyageDiv").css("display", "none");
                                    } else if (str == "3") {
                                        $("#fileUploadfamilyDiv").css("display", "none");
                                        $("#tableSiflat").css("display", "none");
                                        $("#fileUploadAgeDiv").hide();
                                        $("#fileUploadfamilyageDiv").css("display", "block");
                                    } else if (str == "4") {
                                        $("#fileUploadfamilyageDiv").css("display", "none");
                                        $("#tableSiflat").css("display", "none");
                                        $("#fileUploadAgeDiv").show();
                                        $("#fileUploadfamilyDiv").css("display", "none");
                                    }
                                });
                                 $(document).on('change', '.taxchk', function() {
                                     if($(this).is(':checked')){
                                         $(this).parent().find(".tax_opt").val(1);
                                     }else{
                                         $(this).parent().find(".tax_opt").val(0);
                                     }
                                 });   
                            </script>
                            <div class="row">
                        	   <div class="col-sm-6">
                        	       <div class="form-group">
                        	           <input type="hidden" id="plan_id" name="plan_id" value="<?php echo $datalist->plan_id; ?>" />
                        	           <button type="submit" class="btn btn-success btn-lg" id="addpolicybtn">Submit</button>
                    	           </div>
                        	   </div>
                        	</div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php } ?>
            </div>
        </div>              
    </div>
</div>

<!-- end: Content -->			
<script>
$("#policyStartDate").datepicker({
        numberOfMonths: 1,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            $("#policyEndDate").datepicker("option", "minDate", dt);
        }
    });
    $("#policyEndDate").datepicker({
        numberOfMonths: 1,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            $("#policyStartDate").datepicker("option", "maxDate", dt);
        }
});

var vRules = {
	plan_name:{required:true, alphanumericwithspace:true}
};

var vMessages = {
	plan_name:{required:"Please enter plan name."}
};
 
$("#form-plan").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>products/AddNew";
		$("#form-plan").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
				$("#addplanbtn").hide();
			},
			success: function (response) 
			{
			   
				if(response.success)
				{
				    displayMsg("success",response.msg);
				    $("#body").load("<?php echo base_url();?>products/addpolicyview/"+response.data);
					
				}
				else
				{	
				    $("#addplanbtn").show();
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});
$(document).on('change', '.planname', function() {
    var name = $(this).val();
    console.log(name);
$.ajax({
    type: "POST",
    url: "<?php echo base_url();?>products/checkplanname",
    data: {name: name},
    dataType: "json",
       success: function(response) {
           console.log(response);
           	if(response.success)
				{
				   $('.planerror').html("");
				}
				else
				{	
				    $('.planname').val('');
				    $('.planerror').html(response.msg);
				}
       }
});
});
$(document).on('change', '.policyno', function() {
    var name = $(this).val();
   
$.ajax({
    type: "POST",
    url: "<?php echo base_url();?>products/checkpolicynumber",
    data: {name: name},
    dataType: "json",
       success: function(response) {
          
           	if(response.success)
				{
				   $('.policyerror').html("");
				}
				else
				{	
				    $('.policyno').val('');
				    $('.policyerror').html(response.msg);
				}
       }
});
});
</script>
<script>


var vRules1 = {
	policyNo:{required:true, number:true},
	pdf_type:{required:true},
	masterInsurance:{required:true},
	sum_insured_type:{required:true},
	companySubTypePolicy:{required:true}
};

var vMessages1 = {
	policyNo:{required:"Please enter policy number.",number:"Please enter numbers Only"},
	pdf_type:{required:"Please select PDF type."},
	masterInsurance:{required:"Please select insurer."},
	sum_insured_type:{required:"Please select insured type."},
	companySubTypePolicy:{required:"Please select SI basis."}
};

$("#form-policy").validate({
	rules: vRules1,
	messages: vMessages1,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>products/AddPolicyNew";
		var inactivecount = "<?php echo $inactivecount; ?>";
		$("#form-policy").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
				$("#addpolicybtn").hide();
			},
			success: function (response) 
			{
			   console.log(response);
				if(response.success)
				{
				    console.log(response.data);
				    $("#addpolicybtn").show();
				    displayMsg("success",response.msg);
				    if(inactivecount == 1){
				    window.location = "<?php echo base_url();?>products";    
				    }else{
				    $("#body").load("<?php echo base_url();?>products/addpolicyview/"+response.data);}
					
				}
				else
				{	
				    $("#addpolicybtn").show();
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});

</script>
