$( document ).ready(function() {
	check_error();	
});

function check_error()
{
	 $.post("/tls_check_error_data", 	 
	 function (e) {
		 
	var obj = JSON.parse(e);
     //console.log(obj);
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
	 }
	 if(obj.status == 1){
		 document.getElementById("set_id").href = obj.url;
	 }else if(obj.status == 2){
		 alert("Try Again Later... URL sent on email");
	 }
	 
    });
	
}