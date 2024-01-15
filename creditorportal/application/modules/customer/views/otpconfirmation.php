<?php

$attributes = 'id="sendotp" class="col-md-4 offset-md-4"';
echo form_open(base_url() . "customer/verifyotp", $attributes);
?>


	<div class="" style="padding-top: 100px;padding-bottom: 50px;">
    <div class="card animate-modal" style="text-align: center;border: 1px solid #deff;">
        <div class="card-body"> <i class="ti-lock ml-1" aria-hidden="true" style="font-size: 82px;margin-left: -11px;margin-bottom: 21px;color: #da8089;"></i>
		<div class="page-header">
			<div class="mt-2">
				<div class="col-lg-6 col-12 offset-md-3">
					<div class="form-group"> <label for="example-text-input" class="col-form-label">Enter OTP</label> <input class="form-control" id="otp" name="otp" type="password"><span id="otperror" lass="error"></span> </div>
				</div>

				<div class="col-lg-6 col-12 offset-md-3 mt-3">
					<input type="hidden" name="elem_lead" id="elem_lead" value="<?php echo $lead_id; ?>" />
					<button type="submit" class="btn smt-btn" id="sendotp-submit">Submit</button>
				</div>
			</div>
		</div>
        </div>
    </div>
</div>
</div>

<?php

echo form_close();
?>
<script>
	$(document).ready(function() {

		$('#sendotp').on('submit', function() {
			
			var otp = $("#otp").val();
			var lead_id = $("#elem_lead").val();
			if (otp == "") {
				$("#otperror").html("* Please enter OTP");
				return false;
			} else {
				$("#otperror").html("");
			}

			//var act = "<?php echo base_url(); ?>customer/customerpaymentformdetails";
			var act = $(this).attr('action');
			$.ajax({
				url: act,
				type: 'post',
				dataType: 'text',
				data: {
					otp: otp,
					lead_id: lead_id
				},
				cache: false,
				success: function(response) {

					response = response.split('-');
					if (response[0] == 1) {
						//window.location = act;
						//$('body .main-content-inner .row').html(response);
						alert('OTP verification successful. Please do not close or refresh the page, it might take sometime to redirect you to the payment gateway');
						//window.location = "<?php echo base_url(); ?>customerdetails";
						window.location = "<?php echo base_url(); ?>paymentgatewayredirect/<?php echo $lead_id; ?>";
					} 
					else if(response[0] == 2){

						alert('OTP verification successful. Please do not close or refresh the page, it might take sometime to redirect');
						//window.location = "<?php echo base_url(); ?>customerdetails";
						window.location = "<?php echo base_url(); ?>ghdverified/"+response[1];
					}
					else {
						$("#otperror").html("Incorrect OTP, please check the new OTP sent");
					}
				}
			});
			return false;
		});
	});
</script>