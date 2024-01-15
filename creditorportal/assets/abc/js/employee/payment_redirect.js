$( document ).ready(function() {
	
var emp_id = $("#empIdHidden").val();

if (emp_id) {
redirect_url(emp_id);
}
	
});

function redirect_url(emp_id)
{
	 $.post("/redirect_url_check", 
	 { "emp_id":emp_id},
	 function (e) {
	var obj = JSON.parse(e);
     //console.log(obj);
	 if(obj.status == 1){
		 document.getElementById("set_id").href = obj.url;
	 }else if(obj.status == 2){
		 alert("Payment will recieved from Easy Pay contact your bank");
	 }else if(obj.status == 3){
		 alert("Payment is done by Easy Pay..Policy is in process");
	 }
	 
    });
	
}