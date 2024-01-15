$(document).ready(function($) {
	getUrlTable();	
});
$('#submitBtn').on('click', function () {
    var product_id = $("#product_id").val();
    $.ajax({
        url: 'generate_generic_url_affinity',
        type: 'POST',
        dataType: 'json',
        data: {product_id : product_id},
        success: function (response) {
        	$("#generated_link").html(response.url);
			location.reload();
        }
    });
});
function updateStatus(e){
	var id = $(e).data('id');
	$.ajax({
		url: "/update_url_status_affinity",
		type: "POST",
		async: false,
		dataType: 'json',
		data:{
			id:id
		},
		success: function (response) {
			getUrlTable();
		}
	});
}
function getUrlTable(){
	$.ajax({
		url: "/get_all_urls_html_affinity",
		type: "POST",
		async: false,
		dataType: 'json',
		success: function (response) {
			$("#urlTabe").html(response.html);
		}
	});
}

$('#db_select').on('change', function() {
	var db = $(this).val();
	
	if(db == 'axis_retail'){
		// alert('hi');
		window.location.href="/url_generator_axis_retail";
	}else{
		window.location.href="/url_generator_affinity";
	}

  });