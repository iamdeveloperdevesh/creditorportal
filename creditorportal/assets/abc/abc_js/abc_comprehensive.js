function buyStup(){
	$.ajax({
		url: "/redirect_stup_retail_via_abc",
		type: "POST",
		async:false,				
		dataType: "json",
		success: function (response){
			window.location.replace(response.url);
			// console.log(response);
		}
	});
}