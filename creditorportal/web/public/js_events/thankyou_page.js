
function coi_download(){
		$.post("/coi_url_call", 
			function (e) {
			var obj = JSON.parse(e);				
				if(obj.status == 'success'){
					window.open(obj.url, '_blank');
				}
			
		});		
}