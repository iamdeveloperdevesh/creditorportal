$(document).ready(function(){
	   $.ajax({
            url: "/get_all_submit_claim_data",
            type: "POST",
			 async: false,
            dataType: "json",
            success: function (response) 
            {
            	 $.each(response, function (index, value) {
            	 	var dateTime = value.created_at;
					var parts = dateTime.split(/[- :]/);
					var wanted = `${parts[2]}-${parts[1]}-${parts[0]}`;
            	 	 $("table #first_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">Reimbursement</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.total_claim_amount+'</span></td> <td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.total_claim_amount+'</span></td> <td><img src = "/public/assets/images/new-icons/tracking.png"  class="btn sahil_btn" data-id="'+value.claim_reimb_id+'" style = "cursor: pointer; width: 80px;"></td></tr>');
            	 });
            }
        });
		$('body').on('click', '.sahil_btn', function() {
				  	var claim_reimb_id = $(this).attr('data-id');
				  	var storage = window["localStorage"];
           			storage.setItem('claim_id',claim_reimb_id);
                    storage.setItem('shashi_set','no');
           			 window.location = "/employee/track_claim";
		});

		$.ajax({
            url: "/get_all_intimate_claim_data",
            type: "POST",
			 async: false,
            dataType: "json",
            success: function (response) 
            {
            	 $.each(response, function (index, value) {
            	 	var dateTime = value.created_date;
					var parts = dateTime.split(/[- :]/);
					var wanted = `${parts[2]}-${parts[1]}-${parts[0]}`;
            	 	if (value.claim_type == 'reimbursement') 
            	 	{
            	 		 $("table #second_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_intimate_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">'+value.claim_type+'</span></td><td><span class=" color-1 bold-13">'+value.reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.claim_Amount+'</span></td></tr>');
            	 	}
            	 	else
            	 	{
            	 		 $("table #second_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_intimate_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">'+value.claim_type+'</span></td><td><span class=" color-1 bold-13">'+value.reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>0</span></td></tr>');
            	 	}
            	 });
            }
        });

        $.ajax({
            url: "/employee/get_edit_claim_data",
            type: "POST",
			 async: false,
            dataType: "json",
            success: function (response) 
            {
                $.each(response, function (index, value) {
                    var dateTime = value.created_at;
                    var parts = dateTime.split(/[- :]/);
                    var wanted = parts[2]+'-'+parts[1]+'-'+parts[0];
                    $("#first_table_edit").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">Reimbursement</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_reason+'</span></td><td><img src = "/public/assets/images/new-icons/tracking.png"  class="btn edit_btn" id="'+value.claim_reimb_id+'" style = "cursor: pointer; width: 80px;"></td></tr>')
                });
            }
        });

       $('body').on('click', '.edit_btn', function() {
            var claim_reimb_id = $(this).attr('id');
            window.location.href = '/employee/claims?claim_reimb_id='+claim_reimb_id;
        });
})