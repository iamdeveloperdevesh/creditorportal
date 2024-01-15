$(document).ready(function(){
	$.ajax({
        url: "/get_all_employee_details",
        type: "POST",
        dataType: "json",
        success: function (response) {
        	$(response).each(function(k,v){
        		 if (v.amount) 
                 {
                    sum_allocated_amount = v.amount;
                    count = v.count;
                 }
                 else
                 {
                    sum_allocated_amount = 0;
                    count = 0;
                 }
        		
        	});
        	$("#allocated_amount").html('<i class="fa fa-inr"></i> '+sum_allocated_amount);
			 $('#total_emp').text(count);
        }
    });
    	$.ajax({
        url: "/get_all_flexi_details_salarytype",
        type: "POST",
        dataType: "json",
        success: function (response) {
        	var sum_salary_amount = 0;
        	$(response).each(function(k,v){
        		if (v.sum) {
                    sum_salary_amount = v.sum;
                }else
                {
                    sum_salary_amount = 0;
                }
        	});
        	$("#salary_deducted").html('<i class="fa fa-inr"></i> '+sum_salary_amount);
        }
    });

    	$.ajax({
        url: "/get_all_flexi_details_utilizedtype",
        type: "POST",
        dataType: "json",
        success: function (response) {
        	$(response).each(function(k,v){
        		if (v.sum) {
                    sum_utilized_amount = v.sum;
                }else
                {
                    sum_utilized_amount = 0;
                }
        	});
        	$("#utilized_amount").html('<i class="fa fa-inr"></i> '+sum_utilized_amount);
        }
    });

    $.ajax({
        url: "/get_all_flexi_transaction",
        type: "POST",
        dataType: "json",
         success: function (response) {
            var counter = 1;
           $(response).each(function(k,v){
               var newRow = $("<tr>");
                var cols = "";
                cols += '<td><img id="sodexo" class="image-responsive width-24" style="margin-right: 8px;" src="'+ v.img_name+'">'+ v.flexi_benefit_name+'</td>';
                cols += '<td class="align-center"></span><span id="sum_sodexo">'+(v.sum)+'</span></td>';
                cols += '<td class="align-center"><span id="count_sodexo">'+v.count+'</span></td>';
                 newRow.append(cols);
                $("#add_tbody").append(newRow);
                counter++;
            });
         }
	});

    $.ajax({
        url: "/get_all_flexi_transaction_salary",
        type: "POST",
        dataType: "json",
         success: function (response) {
            var counter = 1;
           $(response).each(function(k,v){
               var newRow = $("<tr>");
                var cols = "";
                cols += '<td><img id="sodexo" class="image-responsive width-24" style="margin-right: 8px;" src="'+ v.img_name+'" >'+ v.flexi_benefit_name+'</td>';
                cols += '<td class="align-center"></span><span id="sum_sodexo">'+(v.sum)+'</span></td>';
                cols += '<td class="align-center"><span id="count_sodexo">'+v.count+'</span></td>';
                 newRow.append(cols);
                $("#add_salary_tbody").append(newRow);
                counter++;
            });
         }
    });
})