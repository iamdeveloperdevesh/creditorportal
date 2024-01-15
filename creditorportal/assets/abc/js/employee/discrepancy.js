$(document).ready(function () {

	 $.ajax({
        url: "/get_all_policy_numbers1",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {

            // debugger;
            $("#masPolicy").empty();
            $("#masPolicy").append("<option value = ''>Select Product Name</option>");
            for (i = 0; i < response.length; i++) {
        
                $("#masPolicy").append("<option data-customer = '" + response[i]['customer_search_status'] + "'  data-id = '" + response[i]['policy_subtype_id'] + "' value =" + response[i]['parent_policy_id'] + ">" + response[i]['product_name'] + "</option>");


            }
        }
    });
});
    var table = $('#example').DataTable({});
					  
		
    $(document).on('change', '#masPolicy', function () {

        //customer search
      //  var policy_detail_id = $('#masPolicy option:selected').val();

        // var customer = ($('#masPolicy option:selected').attr('data-customer'));
        // if (policy_detail_id && customer == 'Y') {
            // $("#customer_serach_button").show();
        // }
        // else {
            // $("#customer_serach_button").hide();
        // }
      
       // $("#policy_no").val($('#masPolicy option:selected').val());
        var policy_no = $('#masPolicy option:selected').val();
       // var view_data = $("#view_data").val();
        
        $.post("/employee/DiscrepancyView1", {
            policy_no: policy_no,
            //view:view_data
        
        }, function(e) {
			
			var status_data;
            table
                .clear()
                .draw();
                
            esi = JSON.parse(e);
	
		 e1 = esi.discList;
				var i =1;
				
            e1.forEach(function(eps) {
			
				var inc = i++;
				var name_cust = eps.emp_firstname+" "+eps.emp_lastname;
				if(eps.desc_status == 1)
				{
					
					status_data = "Discrepancy Open";

				}
				else if(eps.desc_status == 2)
				{
					status_data = "Discrepancy Closed";
					var disc_none = "disabled1";
				}
				else if(eps.desc_status == 3)
				{
					status_data = "Rejected";
					var disc_none = "disabled1";
				}
				else
				{
					status_data = "Re-submitted";
					
				}
				
			
				
				var add='';
				var edit_disc;
				var stats;
				
										
	
											if(esi.edit!='view')
                                           {
											   
											   add ="xxx";
											   edit_disc = '<i class="ti-eye" style="font-size:17px" ></i>';
											    if (eps.desc_status == "2") 
                                           {
                                               
                                                stats = "<span style='text-align:center; color: #28a745;font-weight: 600; font-size: 12px;'>Approved</span>"
											  
                                               
                                            }
											else if(eps.desc_status == "3")
											{
												stats = "<span style='text-align:center; color: #ff0000;font-weight: 600; font-size: 12px;'>Rejected</span>";
												
												
											
											}
                                            else{
                                           
                                            stats = "<a href='#' id=''  onclick='disc_approve(this);' data-id='"+eps.desc_id+"'><i class='ti-check'></i></a><a href='#' id='' onclick='disc_reject(this);' data-toggle='modal' data-target='.bd-desc-modal-lg' data-at='"+eps.proposal_id+"' data-id='"+eps.desc_id+"'><i class='ti-close' style='font-weight: 800;padding: 5px;'></i> </a><a href='#' id='' onclick='prop_reject(this);' data-at='"+ eps.proposal_id+"'  data-toggle='modal' data-target='.bd-prop-reject-modal-lg' data-id='"+eps.desc_id+"'> Reject</a>";
                                           
                                            }
											   
										   }
										   else{
											   
											   	if (eps.proposal_ids.indexOf(',') > -1)
											{
													
												add ="";
												edit_disc = '<i class="ti-pencil" style="font-size:17px" ></i>';
												
											}
											else{
									
											add ="xxx"+eps.prop_id; 
											edit_disc = '<i class="ti-pencil" style="font-size:17px" ></i>';
											}
										   }
										   if(disc_none == 'disabled1')
										   {
											    var xyz ='<a href="#">'+edit_disc+'</a>';
										   }
										   else{
											    //var xyz ='<a href="/employee_proposal/'+eps.emp_id_encrypt+'/'+ eps.product_id+add+'"  >'+edit_disc+'</a>';
												if(add == '')
												{
													var data_add ='';
												}
												else
												{
													var data_add = "data-add="+add;
												}
												 
										   var xyz ="<a href='#' "+data_add+" data-attr = "+eps.emp_id_encrypt+"  data-parent = "+eps.product_id+"  onclick = 'post_disc_data(this);'>"+edit_disc+"</a>";
											   

										   }
										  
										 

                                       
                var arr = [
				inc,
                    eps.proposal_id,
					eps.policy_no,
                    name_cust,
                    eps.created_at,
                    eps.updated_at,
                    eps.desc_type,
                    eps.desc_subtype,
                    eps.desc_remarks,
					
                    status_data,
					xyz,
					stats,
                   
                ];

        
                table.row.add(arr).draw();
            });
        });
		 
			
    });


function setModalDet(id) {
    $("#descId").val(id);
    $("#modal1").modal({backdrop: "static"});
}

$('#uploadModal').validate({
    errorElement: 'div',
    rules: {
        modalFiles: {
            required: true
        }
      },
    submitHandler: function(form) {
        var data = new FormData();
        var modalFiles = document.querySelector("input[name='modalFiles']").files[0];
        data.append('modalFiles', modalFiles);
        data.append('desc_id', $("#descId").val());

        $.ajax({
            type: "POST",
            url: "/employee/saveDiscDet",
            async: false,
            data: data,
            processData:false,
            contentType:false,
            cache:false,
			mimeType: "multipart/form-data",
            success: function(e) {
                var data = JSON.parse(e);
                if(data.errorCode == "0") {
                    location.reload();
                }
            }
        });
    }
  });

function disc_approve(e){

    var id_status =  $(e).attr("data-id");
        savediscData("2",id_status);
    }
function disc_reject(e){

    var id_status =  $(e).attr("data-id");
var prop_nos =  $(e).attr("data-at");
       $('#desc_id_no').val(id_status);
	   $('.prop_no').val(prop_nos);
    }
  
function prop_reject(e){

    var id_status =  $(e).attr("data-id");
 var prop_nos =  $(e).attr("data-at");
      $('#desc_id_no1').val(id_status);
	     $('.prop_no').val(prop_nos);
    }
    function savediscData(status,id_status) {
 
        $.post("/employee/discrepancy_status", {
            desc_id: id_status,
            status: status
        }, function(e) {
            swal({
                title: "Success",
                text: " Discrepancy Approved SuccessFully",
                type: "success",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Ok",
                closeOnConfirm: true
              },
              function(){
                location.reload();
              });
        });
    }

    $("#discrepancy_update").validate({
        rules: {
            desc_type: {
                required: true
            },
            
           
        },
        messages: {
            desc_type: "Please specify Discrepancy Type.",
           
            
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {


            $.ajax({
                url: "/employee/create_discrepancy",
                type: "POST",
                data: $("#discrepancy_update").serialize(),
                async: true,
                success: function(data) {
           
                  if(data == 'true')
                  {
                 
                  swal({
                                title: "success",
                                text: "Created Successfully",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                            function() {
                                location.reload();
                            });
                   }
            
                   }
            });
        }
    });

	    $("#proposal_reject_disc").validate({
        rules: {
            prop_remarks_disc: {
                required: true
            },
            
           
        },
        messages: {
            prop_remarks_disc: "Please specify Remark.",
           
            
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {


            $.ajax({
                url: "/employee/create_discrepancy1",
                type: "POST",
                data: $("#proposal_reject_disc").serialize(),
                async: true,
                success: function(data) {
           
                  if(data == 'true')
                  {
                 
                  swal({
                                title: "success",
                                text: "Rejected Successfully",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                            function() {
                                location.reload();
                            });
                   }
            
                   }
            });
        }
    });
	
	
	function post_disc_data(e)
	{
		var emp_id = $(e).attr('data-attr');
		var parent_id = $(e).attr('data-parent');
		var add = $(e).attr('data-add');
		 $("#emp_idDummyForms").val(emp_id);
		  $("#parent_idDummyForms").val(parent_id);
		   $("#add_idDummyForms").val(add);
		$('#discrForm').submit();
	}