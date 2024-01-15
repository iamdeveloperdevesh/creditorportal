<div class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6 col-lg-offset-3">
					<div class="form-group">  <label for="example-text-input" class="col-form-label">Enter OTP</label>  <input class="form-control" id="otp" name="otp" type="text" value=""><span id="otperror" lass="error"></span>  </div>
				</div>
				
				<div class="col-lg-6 col-lg-offset-3">
					<input type="hidden" name="lead_id" id="lead_id" value="<?php echo $lead_id; ?>" />
					<button type="submit" class="btn btn-success btn-lg" id="sendopt">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).on('click','#sendopt',function(){
	var opt = $("#otp").val();
	var lead_id = $("#lead_id").val();
	if(otp == ""){
		$("#otperror").html("* Please enter OTP");
		return false;
	}else{
		$("#otperror").html("");
	}
	var act = "<?php echo base_url();?>policyproposal/customerpaymentformdetails";
	$.ajax({
			url: act, 
			type: 'post',
			dataType: 'json',
			data:{otp:otp,lead_id:lead_id},
			cache: false,
			success: function (response) 
			{
				if(response.success)
				{
					window.location = act;
				}
				else
				{	
					$("#otperror").html("Incorrect OTP");
				}
			}
		});
});
</script>