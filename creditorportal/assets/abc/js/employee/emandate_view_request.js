function emandate_request_post()
{
	var lfckv = document.getElementById("dec_final2").checked;
	if(lfckv){
		 $.post("/emandate_status_check", 
		 function (e) {
			 
		 var obj = JSON.parse(e);
		 
		 if(obj){
			alert('Thank you for your consent. We will get back to you within 2 working days');
		 }else{
			window.location.replace('/emandate_request_post');
		 }
		 
		 });
	}else{
		alert('Please agree to auto debit for this policy');
	}
}