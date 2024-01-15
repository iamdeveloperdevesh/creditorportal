function payment_call()
{
	$.ajax({
	url: "/tls_payment_redirection", 
	success: function(result){
    
	var obj = JSON.parse(result);
	
	if(obj.status == '1'){
		
		window.location = obj.data;
		
	}else if(obj.status == '2'){
	
		var data = obj.data;
		var options = data.razorpay_data;
		var propay = new Razorpay(options);
		
		propay.open();
	
	}else{
		alert(obj.data);
	}
	
	// options.handler = function (response){
	// document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
	// document.getElementById('razorpay_signature').value = response.razorpay_signature;
	//document.razorpayform.submit();
	// document.getElementById("razorpayform").submit();
	// };
	
	// options.theme.image_padding = false;

	// options.modal = {
		// ondismiss: function() {
			// console.log("This code runs when the popup is closed");
		// },
		
		// escape: true,
		
		// backdropclose: false
	// };

	}});
	
}
