$(document).ready(function(){
	$("#start_date").datepicker({
		changeMonth: true,
	    changeYear: true,
	    showOtherMonths: true,
	    selectOtherMonths: true,
	     dateFormat: "dd-mm-yy"
	 });
	var emp_id = $('#emp_id').val();
	$.ajax({
		type: 'POST',
		url: '/employee/get_child_name',
		data: {emp_id:emp_id},
		 async: false,
        success:function(res){
        	var data_res = JSON.parse(res);
        	$('#child_name').append('<option data-rel="" value="">selected</option>');
				  $.each(data_res, function (index, value) {
				
				  		$('#child_name').append('<option value="'+value.family_relation_id+'">' + value.family_firstname +'</option>');
						
				       	
				    });
        }
	});

	$("input[type='file']").change(function() {
		var data = $(this).attr('name');
		if (data == 'doctor_presciption') 
		{
			$("#add_document_btn").removeAttr('style','display:none');
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

	$('#bill_amount').keyup(function(e) {
        var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });

	$("#bill_amount").change(function(){
		var $bill_am = $(this).val();
		var gst = Math.ceil($bill_am * 18/100);
		$("#gst_amount").val(gst);
		var bill_in = parseInt($bill_am, 10) + parseInt(gst, 10);
		$("#bill_amount_in").val(bill_in);
	});

		$("#form_data").validate({
		  rules: {
    child_name: {
      required: true
    },
	creche_name: {
      required: true
    },
	bill_amount: {
      required: true
    },
    bill_amount_in: {
      required: true
    }
  },
  messages: {
    child_name: "Please specify member name",
	creche_name:"please enter specify care type",
	bill_amount: "Please specify bill amount",
	bill_amount_in: "Please specify bill amount",
  },
 submitHandler: function(form) {
		 var child_name = $("#child_name").val();
		  var emp_flexi_benefit_id = $("#emp_flexi_benefit_id").val();
		 var creche_name = $("#creche_name").val();
		 var location = $("#location").val();
		 var start_date = $("#start_date").val();
		 var comment = $("#comment").val();
		  var bill_amount = $("#bill_amount").val();
		  var bill_amount_in = $("#bill_amount_in").val();
		   var gst_amount = $("#gst_amount").val();
		 var form_data = new FormData();
		 var $files = $("#form_data").find("input[type='file']");
		 $.each($files, function (k, v) {
		   form_data.append($(this).attr('name'),$(this).get(0).files[0]);
		});
		  form_data.append("emp_flexi_benefit_id",emp_flexi_benefit_id);
		  form_data.append("child_name",child_name);
		  form_data.append("creche_name",creche_name);
		  form_data.append("start_date",start_date);
		  form_data.append("location",location);
		  form_data.append("comment",comment);
		   form_data.append("bill_amount_in",bill_amount_in);
		  form_data.append("bill_amount",bill_amount);
		   form_data.append("gst_amount",gst_amount);
		$.ajax({
			 type:'POST',
			 url: "/employee/childcare_add",
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
             else if(data.messages)
             {
             	$.each(data.messages, function(key, value){
					 	 var element = $('#' + key);
                            element.closest('div.data').find('.error').remove();
                            element.after(value);
					 });
             }
             else
             {
             	$("#getCodeModal").modal("toggle");
                  $("#getCode").html(data.success);
             }
          }
		 });
		 } 
	});

	$("#data").click(function(){
      window.location.reload();
  });
	$("#back_btn").click(function(){
      window.location.href = "/flexi_benefit";
  });
});