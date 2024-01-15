$( document ).ready(function() {
	if (/payment_error_view_call/.test(window.location.href)){
		ajaxindicatorstart('Processing...');
		check_error();	
	}	
});

function check_error()
{
	 $.post("/check_error_data", 	 
	 function (e) {
		ajaxindicatorstop(); 
	var obj = JSON.parse(e);
	 if(obj.check == 1){
		$("#one").show();
		$("#two").hide();
		$("#three").hide();
	 }else if(obj.check == 2){
		$("#one").hide();
		//$("#btn_id").html('<span style="padding-right:10px; font-size: 13px; font-weight: 800; letter-spacing: 1px;">Click Here</span>');
		$("#div_hide").hide();
		$("#two").show();
		$("#three").hide(); 
	 }else if(obj.check == 3){
		$("#one").hide();
		$("#two").hide();
		$("#three").show();
	 } else if(obj.check == 5){
	 	$("#one").hide();
		$("#two").hide();
		$("#three").hide();
		$("#four").hide();
		$("#five").show();
	 }
	 if(obj.status == 1){
		 document.getElementById("set_id").href = obj.url;
	 }else if(obj.status == 2){
		 alert("Try Again Later... URL sent on email");
	 }
	 
    });
	
}