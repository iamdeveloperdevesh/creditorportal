<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Reject Proposal - <i class="ti-user"></i></p>
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
							<label for="discrepancy_subtype" class="col-form-label">Reason<span class="lbl-star">*</span></label>
							<div class="input-group">
								
								<textarea class="form-control" name="reject_reason" id="reject_reason" aria-describedby="inputGroupPrepend"></textarea>
								
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

	
	$(function() {
		
	});

	var vRules = {
		reject_reason:{required:true}
	};
	
	var vMessages = {
		reject_reason:{required:"Please enter reason."}
	};

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) 
		{
			$("#form-validate").ajaxSubmit({
				url: "<?php echo base_url();?>boproposals/rejectsubmitForm", 
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

	document.title = "Reject";
	
</script>
</body>
</html>