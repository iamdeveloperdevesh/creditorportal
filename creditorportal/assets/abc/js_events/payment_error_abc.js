$( document ).ready(function() {
	//debugger;
	if (/payment_error_view_call_abc/.test(window.location.href)){
		//ajaxindicatorstart('Processing...');
		//debugger;
		check_error();	
	}	

	/* ------------------------- COI Download Call  --------------------------*/
	$(document).on("click",".coi_download_btn", function(){
		var certificate_no = $('#certificate_numbers').val();
		$.ajax({
			url: "/coi_download_abc",
			type: "POST",
			async: false,
			data: {certificate_no: certificate_no},
			dataType: 'json',
			success: function (response) {
				console.log(response);
				//alert(response.pdf);
				if(response.pdf != ''){
					$("#download_pdf_link").attr('href','/resources/'+response.pdf);
					$("#download_pdf_link").attr('download',response.pdf);
					 var link = document.getElementById("download_pdf_link");
	  				link.click();
	  			}
			}
		});
	})
	/* ------------------------- COI Download Call End  --------------------------*/
});

function check_error()
{
	 $.post("/check_error_data_abc", 	 
	 function (e) {
		//ajaxindicatorstop(); 
	var obj = JSON.parse(e);
     //console.log(obj);
	 if(obj.check == 1){
		$("#one").show();
		$("#two").hide();
		$("#three").hide();
	 }else if(obj.check == 2){
		$("#one").hide();
		$("#btn_id").html('<span style="padding-right:10px; font-size: 13px; font-weight: 800; letter-spacing: 1px;">Click Here</span>');
		$("#two").show();
		$("#three").hide(); 
	 }else if(obj.check == 3){
		$("#one").hide();
		$("#two").hide();
		$("#three").show();
	 }
	 if(obj.status == 1){
		 document.getElementById("set_id").href = obj.url;
	 }else if(obj.status == 2){
		 alert("Try Again Later... URL sent on email");
	 }
	 
    });
	
}