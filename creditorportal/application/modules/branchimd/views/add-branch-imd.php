<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-12">
						<p>Bulk Branch IMD Mapping</p>
					</div>
			    </div>
			</div>
			<div class="card-body">
                <form id="bulk_upload" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label for="import_branch_imd_codes" class="col-form-label">File<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                
                                <input class="form-control" placeholder="Enter ..." name="import_branch_imd_codes" id="import_branch_imd_codes" type="file" accept=".csv, .xlsx, .xls" aria-describedby="inputGroupPrepend" />
                                <input type="hidden" name="file_action" id="file_action" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                                
                            </div>
                            <!-- <span><a href="../assets/import_branch_imd_codes.xlsx"> Download Template</a></span> -->
                        </div>
                        <div class="col-md-3 col-12">
					<label class="col-md-12 mt-3 mb1 display-sm-lbl" style="visibility: hidden;">for space</label>
					<a class="btn-excel-d" href="../assets/import_branch_imd_codes.xlsx"> Download Template <i class="ti-file"></i></a>
					</div>
                        <div id="errorDiv" style="color:red;"></div>                   
                    </div>

                    <p>
                     <span class="lbl-star error-msg d-none er-lb">Required</span>
                        </p>
                       
                    
                    <div class="row mt-4">
                        <div class="col-md-1 col-6 text-left">
                            <a href="javascript:void(0);"><button class="btn btn-success bulk-add">Add <i class="ti-plus"></i></button></a>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <a href="javascript:void(0);"><button class="btn btn-warning bulk-overwrite">Overwrite <i class="ti-write"></i></button></a>
                        </div>
                    </div>
                </form>
			</div>
		</div>
	</div>
    	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Single Branch IMD Mapping</p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-3 mb-3">
						<label for="policy_number" class="col-form-label">Master Policy No.<span class="lbl-star">*</span></label>
						<div class="dataTables_filter input-group">
							<input id="policy_number" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
                        <span class="lbl-star error-msg d-none">Required</span>
					</div>
                    <div class="col-md-3 mb-3">
						<label for="branch_code" class="col-form-label">Branch Code<span class="lbl-star">*</span></label>
						<div class="dataTables_filter input-group">
							<input id="branch_code" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
                        <span class="lbl-star error-msg d-none">Required</span>
					</div>
                    <div class="col-md-3 mb-3">
						<label for="imd_code" class="col-form-label">IMD Code<span class="lbl-star">*</span></label>
						<div class="dataTables_filter input-group">
							<input id="imd_code" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
                        <span class="lbl-star error-msg d-none">Required</span>
					</div>
                    <?php /*<div class="col-md-3 mb-3">
						<label for="status" class="col-form-label">Status</label>
						<select class="form-control" name="isactive" id="isactive"> 
                            <option value="" selected>Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">In-Active</option>							
                        </select>
					</div>*/ ?>
					<?php /*<div class="col-md-2 col-12 text-center">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>users/importUser"><button class="btn cnl-btn fl-right">Import Mapping Codes</button></a>
					</div>*/ ?>

				</div>
                <div class="row mt-4">
                    <div class="col-md-1 col-6 text-left">
                        <a href="javascript:void(0);"><button class="btn btn-success single-add">Add <i class="ti-plus"></i></button></a>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <a href="javascript:void(0);"><button class="btn btn-warning single-overwrite">Overwrite <i class="ti-write"></i></button></a>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
<!-- Center Section End-->
<script type="text/javascript">
$(document).ready(function(){

    $('.bulk-overwrite').on('click', function(){

        bulkData('overwrite');
        return false;
    });

    $('.bulk-add').on('click', function(){

        bulkData('add');
        return false;
    });

    $('.single-add').on('click', function(){

        singleData('add');
        return false;
    });

    $('.single-overwrite').on('click', function(){

        singleData('overwrite');   
        return false;     
    });
});

function bulkData(action){

    $("span.error-msg").addClass('d-none');

    if($('#import_branch_imd_codes').val() != ''){

        $('#file_action').val(action);
        
        form = $("#bulk_upload")[0];
        formData = new FormData(form);

        $.ajax({

            url: "<?php echo base_url(); ?>branchimd/addbulkbranchimd",
            type: 'post',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {

                if(response.success){

                    displayMsg("success", response.msg);
                }
                else if(response.errorData){

                    $("#errorDiv").html(response.errorData);
                }
                else{

                    displayMsg("error", response.msg);
                }
            }
        });
    }
    else{

        $('#bulk_upload').closest('div').find('span.error-msg').removeClass('d-none');
    }
}

function singleData(action){

    $("span.error-msg").addClass('d-none');
    data = getData(action);
    if(data){

        $.ajax({
            url:"<?php echo base_url(); ?>branchimd/addsinglebranchimd",
            method:"POST",
            data: data,
            dataType:'JSON',
            cache:false,
            success:function(response){

                if(response.success){

                    displayMsg("success", response.msg);
                }
                else{

                    displayMsg("error", response.msg);
                }
            }
        });
    }
}

function getData(action){

    hasError = 0;

    if($.trim($("#policy_number").val()) == ''){

        $("#policy_number").closest("div").next("span.error-msg").removeClass('d-none');
        hasError = 1;
    }
    if($.trim($("#branch_code").val()) == ''){

        $("#branch_code").closest("div").next("span.error-msg").removeClass('d-none');
        hasError = 1;
    }
    if($.trim($("#imd_code").val()) == ''){

        $("#imd_code").closest("div").next("span.error-msg").removeClass('d-none');
        hasError = 1;
    }

    if(!hasError){

        data = {};
        data.policy_number = $("#policy_number").val();
        data.branch_code = $("#branch_code").val();
        data.imd_code = $("#imd_code").val();
        data.action = action;
        return data;
    }
    else{

        return false;
    }
}
</script>