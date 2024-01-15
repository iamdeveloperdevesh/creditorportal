$(document).ready(function(){
	$("#treatment_date").datepicker({
		changeMonth: true,
	    changeYear: true,
	    showOtherMonths: true,
	    selectOtherMonths: true,
	     dateFormat: "dd-mm-yy"
	 });
	var emp_id = $('#emp_id').val();
	$.ajax({
		type: 'POST',
		url: 'get_family_member',
		data : {emp_id:emp_id},
        success:function(res){
        	var data_res = JSON.parse(res);
        	$('#member_name').append('<option data-rel="" value="">selected</option>');
				  $.each(data_res, function (index, value) {
				  		$('#member_name').append('<option value="'+value.family_relation_id+'">' + value.family_firstname +' ' +value.family_lastname +'</option>');
					});
        }
	});
	var type = 'Health Check up';
	$.ajax({
		type: 'POST',
		url: 'get_employee_rel_id',
		data : {emp_id:emp_id,type:type},
        success:function(res){

        	var data_res1 = JSON.parse(res);
        	 $.each(data_res1, function (index, value) {
        	 	$("#emp_flexi_benefit_id").val(value.employee_flexi_benifit_id);
        	 });
        }
	});

	var counter = 1;
	$("#add_document_btn").click(function(){
		var newRow = $("<div class='input-group mt-2'>");
		var cols = "";
		var cols = "<div class='custom-file'><input type='file' name='additional_doc"+counter+"' id='additional_doc' required><input type='button' class='del_doc_btn' name='del_doc_btn' value='Delete'></div></div>";
		newRow.append(cols);
        $(".add_doc").append(newRow);
         counter++;
	});

	$("body").on('click',".del_doc_btn", function(){
		$(this).parent().parent().remove();
	});
	
	$("#save_visioncare").click(function(){
		 var member_name = $("#member_name").val();
		  var emp_flexi_benefit_id = $("#emp_flexi_benefit_id").val();
		 var treatement_type = $("#treatement_type").val();
		 var doctor_name = $("#doctor_name").val();
		 var centre_name = $("#centre_name").val();
		 var location = $("#location").val();
		 var treatment_date = $("#treatment_date").val();
		 var bill_amount = $("#bill_amount").val();
		  var bill_amount_exc = $("#bill_amount_exc").val();
		 var comment = $("#comment").val();
		 var form_data = new FormData();
		 var $files = $("#form_data").find("input[type='file']");
		 $.each($files, function (k, v) {
		   form_data.append($(this).attr('name'),$(this).get(0).files[0]);
		});
		  form_data.append("emp_flexi_benefit_id",emp_flexi_benefit_id);
		  form_data.append("member_name",member_name);
		  form_data.append("treatement_type",treatement_type);
		  form_data.append("doctor_name",doctor_name);
		  form_data.append("treatment_date",treatment_date);
		  form_data.append("centre_name",centre_name);
		  form_data.append("location",location);
		  form_data.append("bill_amount_exc",bill_amount_exc);
		  form_data.append("bill_amount_ingst",bill_amount);
		  form_data.append("comment",comment);
		  $.ajax({
			 type:'POST',
			 url: "visioncare_flexi_add",
             data: form_data,
             async: false,
             cache: false,
             dataType:'json',
             contentType: false,
             processData: false,
             mimeType: "multipart/form-data",
			 success : function (data) {
             if (data.error) 
             {
             	alert(data.error);
             }
             else
             {
             	alert('your data submitted');
             }
          }
		 }); 
	});
});