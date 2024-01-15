$(document).ready(function(){
   // all employer
	$.ajax({
		 url: "/broker/get_all_employer",
         type: "POST",
         dataType: "json",
         success: function (response) {
         	 $('#employer_name').empty();
         	 $('#employer_name').append('<option value=""> Select Employer name</option>');
         	for (i = 0; i < response.length; i++) { 
         	  	$('#employer_name').append('<option value="'+ response[i].company_id +'">' + response[i].comapny_name+ '</option>');
			}
         }
	});
        $("#employer_name").change(function(){
	 	var company_id = $(this).val();
	 	$.ajax({
                    url: "/broker/get_voluntry_benefit_data",
                    type: "POST",
                    data : {company_id:company_id},
                    dataType: "json",
                    success: function (response) {
                        
                        if(response.sodexo_data)
                        {
                            $("#sodexo_emp").val(response.sodexo_data[0]['total_employee']);
                            $("#sodexo_allocate").val(response.sodexo_data[0]['final_amount']);
                        }
                        if(response.parential_data)
                        {
                            $("#parential_emp").val(response.parential_data[0]['total_employee']);
                            $("#parential_allocate").val(response.parential_data[0]['final_amount']);
                        }
                        if(response.mediclaim_topup_data)
                        {
                            $("#mediclaim_topup_emp").val(response.mediclaim_topup_data[0]['total_employee']);
                            $("#mediclaim_topup_allocate").val(response.mediclaim_topup_data[0]['final_amount']);
                        }
                        if(response.personal_accident_topup_data)
                        {
                            $("#personal_accident_topup_emp").val(response.personal_accident_topup_data[0]['total_employee']);
                            $("#personal_accident_topup_allocate").val(response.personal_accident_topup_data[0]['final_amount']);
                        }
                        if(response.voluntary_term_life_data)
                        {
                            $("#voluntary_emp").val(response.voluntary_term_life_data[0]['total_employee']);
                            $("#voluntary_allocate").val(response.voluntary_term_life_data[0]['final_amount']);
                        }
                        
                    }
               });
               
                $.ajax({
                    url: "/broker/get_wellness_benefit_data",
                    type: "POST",
                    data : {company_id:company_id},
                    dataType: "json",
                    success: function (response) {
                        if(response.wearable_devise_smart_watch_data)
                        {
                            $("#smart_watch_emp").val(response.wearable_devise_smart_watch_data[0]['total_employee']);
                            $("#smart_watch_allocate").val(response.wearable_devise_smart_watch_data[0]['final_amount']);
                            $("#smart_watch_utilized").val(response.wearable_devise_smart_watch_data[0]['utilized_amount']);
                            $("#smart_watch_balance").val(response.wearable_devise_smart_watch_data[0]['balance_amount']);
                        }
                        if(response.gym_data)
                        {
                            $("#gym_emp").val(response.gym_data[0]['total_employee']);
                            $("#gym_allocate").val(response.gym_data[0]['final_amount']);
                            $("#gym_utilized").val(response.gym_data[0]['utilized_amount']);
                            $("#gym_balance").val(response.gym_data[0]['balance_amount']);
                        }
                        if(response.elder_care_data)
                        {
                            $("#elder_care_emp").val(response.elder_care_data[0]['total_employee']);
                            $("#elder_care_allocate").val(response.elder_care_data[0]['final_amount']);
                            $("#elder_care_utilized").val(response.elder_care_data[0]['utilized_amount']);
                            $("#elder_care_balance").val(response.elder_care_data[0]['balance_amount']);
                        }
                        if(response.vaccination_immunization_data)
                        {
                            $("#vaccination_immunization_emp").val(response.vaccination_immunization_data[0]['total_employee']);
                            $("#vaccination_immunization_allocate").val(response.vaccination_immunization_data[0]['final_amount']);
                            $("#vaccination_immunization_utilized").val(response.vaccination_immunization_data[0]['utilized_amount']);
                            $("#vaccination_immunization_balance").val(response.vaccination_immunization_data[0]['balance_amount']);
                        }
                        if(response.yoga_zumba_data)
                        {
                            $("#yoga_zumba_emp").val(response.yoga_zumba_data[0]['total_employee']);
                            $("#yoga_zumba_allocate").val(response.yoga_zumba_data[0]['final_amount']);
                            $("#yoga_zumba_utilized").val(response.yoga_zumba_data[0]['utilized_amount']);
                            $("#yoga_zumba_balance").val(response.yoga_zumba_data[0]['balance_amount']);
                        }
                        if(response.condition_mgmt_program_data)
                        {
                            $("#condition_mgmt_program_emp").val(response.condition_mgmt_program_data[0]['total_employee']);
                            $("#condition_mgmt_program_allocate").val(response.condition_mgmt_program_data[0]['final_amount']);
                            $("#condition_mgmt_program_utilized").val(response.condition_mgmt_program_data[0]['utilized_amount']);
                            $("#condition_mgmt_program_balance").val(response.condition_mgmt_program_data[0]['balance_amount']);
                        }
                        if(response.nutrition_dietician_counselling_data)
                        {
                            $("#nutrition_dietician_counselling_emp").val(response.nutrition_dietician_counselling_data[0]['total_employee']);
                            $("#nutrition_dietician_counselling_allocate").val(response.nutrition_dietician_counselling_data[0]['final_amount']);
                            $("#nutrition_dietician_counselling_utilized").val(response.nutrition_dietician_counselling_data[0]['utilized_amount']);
                            $("#nutrition_dietician_counselling_balance").val(response.nutrition_dietician_counselling_data[0]['balance_amount']);
                        }
                        if(response.home_healthcare_data)
                        {
                            $("#home_healthcare_emp").val(response.home_healthcare_data[0]['total_employee']);
                            $("#home_healthcare_allocate").val(response.home_healthcare_data[0]['final_amount']);
                            $("#home_healthcare_utilized").val(response.home_healthcare_data[0]['utilized_amount']);
                            $("#home_healthcare_balance").val(response.home_healthcare_data[0]['balance_amount']);
                        }
                        if(response.health_check_up_data)
                        {
                            $("#health_check_up_emp").val(response.health_check_up_data[0]['total_employee']);
                            $("#health_check_up_allocate").val(response.health_check_up_data[0]['final_amount']);
                            $("#health_check_up_utilized").val(response.health_check_up_data[0]['utilized_amount']);
                            $("#health_check_up_balance").val(response.health_check_up_data[0]['balance_amount']);
                        }
                    }
                });
	});
});


