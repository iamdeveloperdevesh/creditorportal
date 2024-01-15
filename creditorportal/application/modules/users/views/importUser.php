<?php //echo "<pre>";print_r($user_details);exit;?>
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Import User - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2"></div>
				</div>
			</div>
			<div class="card-body">
			<form  id="form-validate" method="post" action="#">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="employee_fname" class="col-form-label">File<span class="lbl-star">*</span></label>
						<div class="input-group">
							
							<input class="form-control" placeholder="Enter ..." name="import_user" id="import_user" type="file" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
							
						</div>
					</div>
					<div class="col-md-3 col-12 mrg-2">
					<label class="col-md-12 mt-3 mb1 display-sm-lbl" style="visibility: hidden;">for space</label>
					<a class="btn-excel-d" href="../assets/import_user_template.xlsx"> Download Template <i class="ti-file"></i></a>
					</div>

					<div id="errorDiv" style="color:red;"></div>
					
				</div>
				
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left">
						<button type="submit" class="btn smt-btn btn-primary">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>users"><button type="button" class="btn cnl-btn">Cancel</button></a>
					</div>
				</div>
			</form>	
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

var vRules = {
	import_user:{required:true, extension: "xlsx"}
};
	
var vMessages = {
	import_user:{required:"Please select file.", extension:"Only .xlsx allowed."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		$("#form-validate").ajaxSubmit({
			url: "<?php echo base_url();?>users/importData", 
			type: 'post',
			dataType: 'JSON',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
				//$(".btn-primary").hide();
				//return false;
			},
			success: function (response) 
			{
				if(response.success)
				{
					displayMsg("success", response.msg);
					setTimeout(function(){
						window.location = "<?php echo base_url();?>users/importUser";
					},2000);
				}
				else
				{	
					//displayMsg("error", response.msg);
					$("#errorDiv").html(response.Errordata);
					return false;
				}
			}
		});
	}
});

document.title = "Import Users";
	
</script>
</body>
</html>


