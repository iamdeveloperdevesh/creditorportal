$(document).ready(function(){
$("#start_date").datepicker({
		changeMonth: true,
	    changeYear: true,
	    showOtherMonths: true,
	    selectOtherMonths: true,
	     dateFormat: "dd-mm-yy"
	 });
$('body').on('focus',"#end_date", function(){
	var data = $("#start_date").val();
	var date = data.split("-");
	var display_date = date[1]+'-'+date[0]+'-'+date[2];
	$(this).datepicker({
	 changeMonth: true,
    changeYear: true,
    showOtherMonths: true,
    selectOtherMonths: true,
     dateFormat: "dd-mm-yy",
     minDate: new Date(display_date)
	 });
});
	var emp_id = $('#emp_id').val();
	$.ajax({
		type: 'POST',
		url: '/employee/get_family_member',
		data : {emp_id:emp_id},
		 async: false,
        success:function(res){
        	var data_res = JSON.parse(res);
        	$('#patient_name').append('<option data-rel="" value="">selected</option>');
				  $.each(data_res, function (index, value) {
				  		$('#patient_name').append('<option value="'+value.family_relation_id+'">' + value.family_firstname +'</option>');
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

	$("#save_dental").click(function(){
		 var patient_name = $("#patient_name").val();
		 var emp_flexi_benefit_id = $("#emp_flexi_benefit_id").val();
		 var start_date = $("#start_date").val();
		 var end_date = $("#end_date").val();
		 var treatment_type = $("#treatment_type").val();
		 var doctor_name = $("#doctor_name").val();
		 var location = $("#location").val();
		 var bill_amount = $("#bill_amount").val();
		 var bill_amount_in = $("#bill_amount_in").val();
		 var gst_amount = $("#gst_amount").val();
		 var comment = $("#comment").val();
		 var form_data = new FormData();
		 var $files = $("#form_data").find("input[type='file']");
		  $.each($files, function (k, v) {
		   form_data.append($(this).attr('name'),$(this).get(0).files[0]);
		  });
		   form_data.append("emp_flexi_benefit_id",emp_flexi_benefit_id);
		  form_data.append("patient_name",patient_name);
		  form_data.append("start_date",start_date);
		  form_data.append("end_date",end_date);
		  form_data.append("treatment_type",treatment_type);
		  form_data.append("doctor_name",doctor_name);
		  form_data.append("location",location);
		  form_data.append("bill_amount",bill_amount);
		  form_data.append("bill_amount_in",bill_amount_in);
		   form_data.append("gst_amount",gst_amount);
		  form_data.append("comment",comment);
		    $.ajax({
			 type:'POST',
			 url: "/employee/dental_flexi_add",
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
             	 $("#getCodeModal").modal("toggle");
                  $("#getCode").html(data.error);
             }
             else
             {
             	$("#getCodeModal").modal("toggle");
                  $("#getCode").html(data.success);
             }
          }
		 }); 
	});
	$("#data").click(function(){
      window.location.reload();
  });
	$("#back_btn").click(function(){
      window.location.href = "/flexi_benefit";
  });
})