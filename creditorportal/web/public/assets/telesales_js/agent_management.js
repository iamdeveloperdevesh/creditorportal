	$(document).ready(function()
	{  
		var dataTable = $('#agentTable').DataTable({  
					   "processing":true,  
					   "serverSide":true,  
					   "order":[],  
					   "ajax":{  
							url:"/tele_agent_tbl_ajax",  
							type:"POST"  
					   },  
					   "columnDefs":[  
							{  
								 "targets":[5],  
								 "orderable":false,  
							},  
					   ],  
				  });  
				  
		$('.bd-example-modal-md').removeClass('hide');
	});  
 //myModalPassChange
	function ResetPasswordModal(data) {
		$("#agent_id").val(data.id);
	$('#myModalPassChange').modal('show');
	}
	//resetPasswordNew
	function ResetPassword() {

		var form = $("#resetPassword").serialize();
		$.ajax({
			url: "/resetPasswordNew",
			type: "POST",
			async: false,
			dataType: "json",
			data:form,
			success: function (response)
			{
				if(response.code == 200){
					swal("Success", response.msg);
					$('#myModalPassChange').modal('hide');
				}else{
					swal("Alert", response.msg);
				}
			}
		});
	}
 
	function searchTable(){	
		search("agentTable");
	}	

	function clearFilter(){

		search("agentTable");
	}
	
	$(document).on("click", ".add_agent_data", function () 
	{
		role_access_module();
		$("#agentForm").trigger("reset");
	});
	
	function role_access_module()
	{
		 $.ajax({
					url: "/tele_role_access_module",
					type: "POST",
					async: false,
					dataType: "json",
					success: function (response) 
					{
						$(".role_module").empty();	
						for (i = 0; i < response.length; i++) 
						{
							var str;
							str = '<div class="col-md-3"> <div class="form-group"><input type="checkbox" class="form-check-input role_check" value="'+response[i].role_module_id+'" name="create_module[]" ><label for="create_mode">'+response[i].acc_module_name+'</label></div></div>';
							$(".role_module").append(str);
						}
					}
				});
	}
	
	$("#agentForm").validate({
	ignore: ".ignore",
	rules: {
	agentCode: {
	required: true
	},
	agentName: {
	required: true
	},
	imd_code: {
	required: true
	},
	base_emp_id: {
	required: true
	},

	},
	messages: {
		
				'test[]': {
								required: "You must check at least 1 box",
							
						  }

			  },
	submitHandler: function (form) 
	{
		ajaxindicatorstart();
		var form = $("#agentForm").serialize();
    	$.post("/tele_create_agent", form, function (e)
		{
			var data = JSON.parse(e);
			if (data.status == false) 
			{
				ajaxindicatorstop();
				swal({
				title: "Alert",
				text: data.message,
				type: "warning",
				showCancelButton: false,
				confirmButtonText: "Ok!",
				closeOnConfirm: true,
				allowOutsideClick: false,
				closeOnClickOutside: false,
				closeOnEsc: false,
				dangerMode: true,
				allowEscapeKey: false
				},
				function () 
				{

				});
				return;
			}
			else
			{
				ajaxindicatorstop();
				swal({
				title: "Success",
				text: data.message,
				type: "success",
				showCancelButton: false,
				confirmButtonText: "Ok!",
				closeOnConfirm: true,
				allowOutsideClick: false,
				closeOnClickOutside: false,
				closeOnEsc: false,
				dangerMode: true,
				allowEscapeKey: false
				},
				function () 
				{
						location.reload();
				});
			}
			
		});			
	
	}
	});

	function editAgent(e) 
	{
		
		var edit_data = e.id;
		 $.ajax({
					url: "/tele_edit_agent_data",
					type: "POST",
					async: false,
					data:{'edit':edit_data},
					dataType: "json",
					success: function (response) 
					{
						
						if(response.length != 0)
						{
							role_access_module();
							var data = response[0];
							var is_admin;
							$("#agentCode").val(data.base_agent_id);
							$("#agentName").val(data.base_agent_name);
							$("#tl_id").val(data.tl_emp_id);
							$("#tl_name").val(data.tl_name);
							$("#am_id").val(data.am_emp_id);
							$("#am_name").val(data.am_name);
							$("#om_name").val(data.om_name);
							$("#om_id").val(data.om_emp_id);
							$("#center").val(data.center);
							$("#lob").val(data.lob);
							$("#vendor").val(data.vendor);
							$("#base_emp_id").val(data.base_emp_id);
							$("#abhi_sales_manager").val(data.abhi_sales_manager);
							$("#rec_manager_code").val(data.rec_manager_code);
							$("#abhi_area_head").val(data.abhi_area_head);
							$("#imd_code").val(data.imd_code);
							/*if(data.is_admin == '0')
							{
								is_admin = 'Agent';
							}
							else
							{
								is_admin = 'Admin';
							}
							$("input[name='role_types'][value='"+is_admin+"']").prop('checked',true);*/
							/*$("input[name='login'][value='"+data.is_login+"']").prop('checked',true);*/
							$("#edit_data").val(data.base_id);
							/*var role = data.module_access_rights.split(',');
							$.each(role,function(i){
							
							$("input[name='create_module[]'][value='"+role[i]+"']").prop('checked',true);*/
							 
							//});
	
						}
					}
				});
	}
	
	function deleteAgent(e)
	{
		var delete_data = e.id;
	
		$.ajax({
					url: "/tele_delete_agent_data",
					type: "POST",
					async: false,
					data:{'delete_data':delete_data},
					dataType: "json",
					success: function (response) 
					{
							ajaxindicatorstart();
						  
						
							if(response.status = true)
							{
								ajaxindicatorstop();
								swal({
								title: "Success",
								text: response.message,
								type: "success",
								showCancelButton: false,
								confirmButtonText: "Ok!",
								closeOnConfirm: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true,
								allowEscapeKey: false
								},
								function () 
								{
										location.reload();
								});

						    }
							else
							{
								ajaxindicatorstop();
								swal({
								title: "Alert",
								text: response.message,
								type: "warning",
								showCancelButton: false,
								confirmButtonText: "Ok!",
								closeOnConfirm: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true,
								allowEscapeKey: false
								},
								function () 
								{

								});
								return;								
							}
					}
				});
		
	}

	$("#importAgent").validate({
	ignore: ".ignore",
	rules: {
	filetoUpload: {
	required: true
	},
	
	},
	messages: {},
	submitHandler: function (form) 
	{
		ajaxindicatorstart("uploading....");
		var file_agent = $("#file_data");
 
		var file_agent_data = file_agent[0].files;

		if(file_agent_data.length != 0)
		{
			var abc = file_agent_data[0].name.substring(file_agent_data[0].name.lastIndexOf('.') + 1);
		
			var fileExtension = ['xlsx','CSV','xls','csv'];
			if ($.inArray(abc, fileExtension) == -1)
			{
				ajaxindicatorstop();
				swal("Alert", "Please check documents uploaded, Supported documents xlsx, xls,csv");
				return false;
			}
		}
		
		 var data = new FormData(form);
		  $.ajax({
            type: "POST",
         
            url: "/tele_upload_agent",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            mimeType: "multipart/form-data",
			dataType: "json",
            success: function(e) {
                
				if(e.errorCode == 1)
				{
					ajaxindicatorstop();
					var er ="" ;
					if($.isArray(e.msg))
					{
				
						$.each(e.msg,function(key,msgs)
						{
							console.log(msgs);
							er + = msgs.split("\n");
						});
					}
					else
					{
						er = e.msg;
					}
					
					     swal({
								title: "Alert",
								text: er,
								type: "warning",
								showCancelButton: false,
								confirmButtonText: "Ok!",
								closeOnConfirm: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true,
								allowEscapeKey: false
								},
								function () 
								{
										
								});
								return;
					
				}
				else
				{
					ajaxindicatorstop();
					swal({
								title: "Success",
								text: e.msg,
								type: "success",
								showCancelButton: false,
								confirmButtonText: "Ok!",
								closeOnConfirm: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true,
								allowEscapeKey: false
								},
								function () 
								{
									location.reload();	
								});
				}

            }
        });
	}
	});
    $("body").on("keyup", ".first_name", function (e) 
	{
		var $th = $(this);
		if (
		  e.keyCode != 46 &&
		  e.keyCode != 8 &&
		  e.keyCode != 37 &&
		  e.keyCode != 38 &&
		  e.keyCode != 39 &&
		  e.keyCode != 40
		) {
		  $th.val(
			$th.val().replace(/[^A-Za-z ]/g, function (str) {
			  return "";
			})
		  );
		}
		return;
  });
  $("#agentCode,#tl_id,#am_id,#om_id").keyup(function () 
  {
	 
		if ($(this).val().match(/[^\w\s]/gi)) 
		{
			$(this).val($(this).val().replace(/[^\w\s]/gi, ""));
		}
	});
	
	$("#imd_code").keyup(function () 
  {
	
		if ($(this).val().match(/[^0-9]/g)) 
		{
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		}
	});
	
	/*$('input[type=radio][name=login]').change(function() 
	{
		if (this.value == 'N') {
			$(".role_check").addClass('ignore');
		}
		
	});*/
	

 