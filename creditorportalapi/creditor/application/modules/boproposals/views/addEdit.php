<?php //echo "<pre>";print_r($proposal_details_id);exit;;?>
<div class="page-body">

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Add Discrepancy</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>boproposals"><i data-feather="home"></i> </li>
						<li class="breadcrumb-item active">Proposals</li></a>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="card tab2-card">
					<div class="card-header">
						<h5>Add Discrepancy</h5>
					</div>
					<div class="card-body">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade active show"  aria-labelledby="account-tab">
								<form  id="form-validate" method="post" action="#">
									<input type="hidden" id="lead_id" name="lead_id" value="<?php if(!empty($lead_id)){echo $lead_id;}?>" />
									
									<div class="form-group row">
										<label for="discrepancy_type" class="col-xl-3 col-md-4"><span>*</span> Discrepancy Type</label>
										<input class="form-control col-xl-8 col-md-7" name="discrepancy_type" id="discrepancy_type" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="discrepancy_subtype" class="col-xl-3 col-md-4"><span>*</span> Discrepancy SubType</label>
										<input class="form-control col-xl-8 col-md-7" name="discrepancy_subtype" id="discrepancy_subtype" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="remark" class="col-xl-3 col-md-4"><span>*</span> Remark</label>
										<textarea class="form-control col-xl-8 col-md-7" name="remark" id="remark"></textarea>
									</div>
									
									<div class="pull-center">
										<button type="submit" class="btn btn-primary">Save</button>
										<a href="<?php echo base_url();?>boproposals" class="btn btn-primary">Cancel</a>
									</div>
								</form>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

</div>		
<script type="text/javascript">
	
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
							window.location = "<?php echo base_url();?>boproposals";
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


