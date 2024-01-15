$(document).ready(function(){
	// states
	$.ajax({
        	url: "/get_all_states",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response) {
            	$('#state_names').append('<option value="">select</option>');
            for (i = 0; i < response.length; i++) { 
				$('#state_names').append('<option value="' + response[i].state_id + '">' + response[i].state_name + '</option>');
				}
                }
            });
	// stae from city
			$('#state_names').on('change', function() {
			    $.ajax({
			        url: "/get_city_from_states",
			        type: "POST",
			        data: {
			                "state_names": this.value
			        },
			        async: false,
			        dataType: "json",
			        success: function (response) {		
						$('#cities').empty();
						$('#cities').append('<option>Select City</option>');
						for (i = 0; i < response.length; i++) { 
						$('#cities').append('<option value="' + response[i].city_id + '">' + response[i].city_name + '</option>');
			   		}
			}              
			}); 
		})

	var counter = 2;
	$("#btn_add").click(function(){
		var newRow = $("<tr>");
        var cols = "";
        cols += '<th scope="row">'+counter+'</th>';
        cols += '<td><input type="text" class="form-control" name="bill_no '+ counter + '"/></td>';
        cols += '<td><input type="date" class="form-control" name="name="bill_date" id="' + counter + '"/></td>';
        cols += '<td><input type="text" class="form-control" name="claim_amount" id="'+ counter +'"/></td>';
        cols += '<td><input type="text" class="form-control" name="phone" id"'+ counter +'"/></td>';
        cols += '<td><select class=form-control><option>Select Cost</option><option>Pre-Hospitalization</option><option>Post-Hospitalization</option><option>Hospitalization</option></select></td>';
        newRow.append(cols);
        $("#add_tbody").append(newRow);
        counter++;
	});

	$("#submit_add").click(function(){
		var count = 0;
		var policy_no = document.getElementsByName("policy_no");
		var family_members_id = document.getElementsByName("family_members_id");
		var member_full_name = document.getElementsByName("member_full_name");
		var family_date_birth = document.getElementsByName("family_date_birth");
		var sum_insured = document.getElementsByName("sum_insured");
		var premium_text = document.getElementsByName("premium_text");
		var family_gender = document.getElementsByName("family_gender");
	})
});