$( document ).ready(function() {
	
var emp_id = $("#empIdHidden").val();

if (emp_id) {
check_error(emp_id);
}
	
});

function check_error(emp_id)
{

	 $.post("/check_error_data_axis", 
	 { "emp_id":emp_id},
	 function (e) {
		 
	 var obj = JSON.parse(e);
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
