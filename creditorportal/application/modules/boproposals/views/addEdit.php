<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Add Discrepancy - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form  id="form-validate" method="post" action="#">
				<input type="hidden" id="lead_id" name="lead_id" value="<?php if(!empty($lead_id)){echo $lead_id;}?>" />
					
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="discrepancy_type" class="col-form-label">Discrepancy Type<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="discrepancy_type" id="discrepancy_type" onchange="getDiscrepancySubType(this.value);">
									<option value="">Select</option>
									<?php 
									if(!empty($getDiscrepancyType)){
										for($i=0; $i < sizeof($getDiscrepancyType); $i++){
									?>
										<option value="<?php echo $getDiscrepancyType[$i]['discrepancy_type_id']; ?>" ><?php echo $getDiscrepancyType[$i]['discrepancy_type']; ?></option>
									<?php 
										}
									}
									?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						
						<div class="col-md-3 mb-3">
							<label for="discrepancy_subtype" class="col-form-label">Discrepancy SubType<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="discrepancy_subtype" id="discrepancy_subtype" >
									<option value="">Select</option>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						
						<div class="col-md-3 mb-3">
							<label for="discrepancy_subtype" class="col-form-label">Remark<span class="lbl-star">*</span></label>
							<div class="input-group">
								
								<textarea class="form-control" name="remark" id="remark" aria-describedby="inputGroupPrepend"></textarea>
								
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						
					</div>
					
					
					<div class="row">
						<div class="col-md-1 col-12 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-12 text-right"><a href="<?php echo base_url();?>"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
					</div>
				</form>	
			</div>
		</div>
	</div>
</div>
<!-- end: Content -->
<script type="text/javascript">

function getDiscrepancySubType(discrepancy_type_id)
{
	//alert(creditor_id);return false;
	if(discrepancy_type_id != "" )
	{
		$.ajax({
			url:"<?php echo base_url();?>boproposals/getDiscrepancySubType",
			data:{discrepancy_type_id:discrepancy_type_id},
			type:'post',
			dataType: 'json',
			success: function(res)
			{
				if(res['status']=="success")
				{
					if(res['option'] != "")
					{
						$("#discrepancy_subtype").html("<option value=''>Select</option>"+res['option']);
						// $("#subcategory_id").select2();
					}
					else
					{
						$("#discrepancy_subtype").html("<option value=''>Select</option>");
						// $("#subcategory_id").select2();
					}
				}
				else
				{	
					$("#discrepancy_subtype").html("<option value=''>Select</option>");
					// $("#subcategory_id").select2();
				}
			}
		});
	}
}
	
	$(function() {
		
	});

	var vRules = {
		discrepancy_type:{required:true},
		discrepancy_subtype:{required:true},
		remark:{required:true}
	};
	
	var vMessages = {
		discrepancy_type:{required:"Please enter type."},
		discrepancy_subtype:{required:"Please enter sub type."},
		remark:{required:"Please enter remark."}
	};

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) 
		{
			$("#form-validate").ajaxSubmit({
				url: "<?php echo base_url();?>boproposals/submitForm", 
				type: 'post',
				dataType: 'JSON',
				cache: false,
				clearForm: false, 
				beforeSubmit : function(arr, $form, options){
					$(".btn-primary").hide();
					//return false;
				},
				success: function (response) 
				{
					$(".btn-primary").show();
					if(response.success)
					{
						displayMsg("success", response.msg);
						setTimeout(function(){
							window.location = "<?php echo base_url();?>";
						},2000);
					}
					else
					{	
						displayMsg("error", response.msg);
						$(".btn-primary").show();
						return false;
					}
				}
			});
		}
	});

	document.title = "Add Discrepancy";
	
</script>
</body>
</html>