$(document).ready(function () {
	
	$('input[name="dates"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
	
	$('input[name="dates_new"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
	
	
	
	
});

	  $(document).on('change', '#product_type', function () {
	 
	 var product = $('#product_type option:selected').val();
	 
	 if(product != ''){
		 
	 	  $.ajax({
                        url: "/retail/get_policy_type",
                        type: "POST",
                        async: false,
						data:{'product':product},
                        dataType: "json",
                        success: function (response) {
							debugger;

$("#masPolicy").empty();
$("#masPolicy").append("<option value=''>Select Policy Type</option>");
for(i =0; i< response.length; i++){
	$("#masPolicy").append("<option value= "+response[i].policy_detail_id+"> "+response[i].policy_type+"</option>");
}
	
}

                        
                });
		 
 }else{
	 alert('Please Select Product Type');
 }
	
	  });
$(document).on('click', '#exportExcel', function () {
	 
	 var policy_detail_id = $('#masPolicy option:selected').val();
	 var product = $('#product_type option:selected').val();
	 if(product == ''){
		 alert('Please Select Product Type'); return false;
	 }
	 
	 
	 if(policy_detail_id != ''){
		 
	 	 $('#exportExcelForm').submit();
		 
 }else{
	 alert('Please Select Policy Type');
 }
	
	  });
	  

$(document).on('click', '#exportExcel_new', function () {
	 
	 var policy_detail_id = $('#masPolicy_new option:selected').val();
	 
	 if(policy_detail_id != ''){
		 
	 	 $('#exportExcelForm_new').submit();
		 
 }else{
	 alert('Please Select Log Type');
 }
	
	  });
	
	
$(document).on('click', '#exportExcel_new1', function () {
	 
 var policy_detail_id = $('#data_type option:selected').val();
 var product = $('#product_id option:selected').val();
 if(product == ''){
	 alert('Please Select Product Type'); return false;
 }
 
 
 if(policy_detail_id != ''){
	 
	 $('#exportExcelForm_new1').submit();
		 
 }else{
	 alert('Please Select Data Type');
 }
	
});
	  