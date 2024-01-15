
$(window).load(function() {

	$('input[name="recondates"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
		"dateLimit": {
                "month": 1
            },
    });

	setTimeout(function(){ $('#product_type').trigger('change');}, 500);
	
});


	
$(document).ready(function(){
//Export Function Start//
	$(document).on('click', '#exportReconExcel', function(e)
	{
		$("#export_recon-applogs").val(1);	
		$("#applogs_form").submit();
		$("#export_recon-applogs").val(0);	
	});
	//Export Function End//

	//Main js for submit button(Apply Filter Button Part) --Start//
	$(document).on('click', '#recon_search', function(e)
		{
			debugger;
			e.preventDefault();
			
			var product = $('#product_type option:selected').val();
			if(product == ''){
				alert('Please Select Product Type'); return false;
		}
			  $("#applogs_form").submit();
		
	});
	//Main js for submit button(Apply Filter Button Part) --End//

	//Export Function Start//
	
	 

	 //For Dynamic Cover Selection On Product Type Change --Start//
	 $(document).on('change','#product_type',function(e)
	{
		// console.log("Changing value")
		var product_type = $(this).val();
		if(product_type != ''){
			$.ajax({
		      url: "/get_sub_product_type",
		      type: "POST",
		      async: false,
		      data: {product_type: product_type},
		      dataType: 'json',
		      success: function (response) {
		        $('#product_cover_type').html(response.html);
		      }
		
                 });
		}

	});
    var rowperpage = 10;    
        $(document).ready(function(){
			
			//$("#but_next").click(function(){
		/*	$('#but_next').click(function(e){
				
				var rowid = Number($("#txt_rowid").val());
				var allcount = Number($("#txt_allcount").val());
				var currValue = $(this).val();
				rowid += rowperpage;
				//if(rowid <= allcount){
					$("#txt_rowid").val(rowid);
					getData(currValue);
				//}

			});
			$("#but_prev").click(function(e){
				//console.log("gjhg");	
				var rowid = Number($("#txt_rowid").val());
				var allcount = Number($("#txt_allcount").val());
				var currValue = $(this).val();
				rowid -= rowperpage;
				if(rowid < 0){
					rowid = 0;
				}
				$("#txt_rowid").val(rowid);
				getData(currValue);

			});
			*/
			$('#but_next').click(function(e){
				
				var rowid = Number($("#txt_rowid").val());
				var allcount = Number($("#txt_allcount").val());
				//var currValue = $(this).val();
				var currValue = $('#but_next').attr('my-name');
				rowid += rowperpage;
				//if(rowid <= allcount){
					$("#txt_rowid").val(rowid);
					if(currValue == 'coi_faliure'||currValue == 'coi_success'){
						getDataCoi(currValue);
					}else if(currValue == 'Mismatch Policy'){
						getMismatchData(currValue);
					}else if(currValue == 'Mismatch Original'){
						getMismatchOriginalData(currValue);
					}else if(currValue == 'Payment Received'){
						getPaymentRedirectData(currValue);
					}else if(currValue == 'Success'){
						getPaymentSuccessData(currValue);
					}
					else{
						getData(currValue);
					}
					
				//}

			});
			$("#but_prev").click(function(e){
				//console.log("gjhg");	
				var rowid = Number($("#txt_rowid").val());
				var allcount = Number($("#txt_allcount").val());
				//var currValue = $(this).val();
				var currValue = $('#but_prev').attr('my-name');
				rowid -= rowperpage;
				if(rowid < 0){
					rowid = 0;
				}
				$("#txt_rowid").val(rowid);
				if(currValue == 'coi_faliure'||currValue == 'coi_success'){
					getDataCoi(currValue);
				}else if(currValue == 'Mismatch Policy'){
					getMismatchData(currValue);	
				}else if(currValue == 'Mismatch Original'){
					getMismatchOriginalData(currValue);
				}else if(currValue == 'Payment Received'){
						getPaymentRedirectData(currValue);
				}else if(currValue == 'Success'){
						getPaymentSuccessData(currValue);
				}
				else{
					getData(currValue);
				}
				//getDataCoi(currValue);

			});
			function getDataCoi(currValue){
				var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
					var res = (cover.split("_"));
					var f = res[1];
					if(f=='4112'){
						var c1 = "GPA";
					}else if(f=='4216'){
						var c1 = "GCI";
						
					}else if(f=='4211'){
						var c1 = "GHI";	
					}else{
						var c1 = "All";	
					}
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
				var rowid = $("#txt_rowid").val();
                //debugger;
                //e.preventDefault(e);
				$.ajax({
				url: "/get_coi_data",
				type: "POST",
				async: false,
				dataType: "json",
				data : {pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id,currValue: currValue,rowid:rowid,rowperpage:rowperpage},
				success: function (response) {
					var student = ''; 
					student += '<thead class="table-col-1">'
                    student += '<tr>'
                    student += '<th style="width:55px">Sr. no.</th>'
                    student += '<th style="width:110px">Lead_ID</th>'
                    student += '<th style="width:90px">Cover</th>'
                    student += '<th style="width:85px">Status</th>'
                    student += '<th style="width:100px">Lead Creation Date</th>'
                    student += '<th style="width:98px">Payment Date</th>'
                    student += '<th style="width:130px">Member ID</th>'
                    student += '<th style="width:110px">Start Date</th>'
                    student += '<th style="width:110px">End Date</th>'
                    student += '<th style="width:120px">COI Number</th>'
                    student += '<th style="width:100px">Duplicate COI</th>'
                    student += '<th style="width:100px">Failure Reason</th>'
                    student += '<th style="width:85px">Last Request Log</th>'
                    student += '<th style="width:85px">Last Response Log</th>'
                    student += '<th style="width:100px">Lead Details</th>'
                    student += '<th style="width:100px">API Type</th>'
					student += '<th style="width:100px">API Count</th>'
                    student += '</tr>'
                  student += '</thead>'
                  student += '<tbody>'
				  var i = 1;
						$.each(response, function (key, value) {
							if(value.arr_res_plan_code=='4112'){
								var c1 = "GPA";
							}else if(value.arr_res_plan_code=='4216'){
								var c1 = "GCI";
								
							}else if(value.arr_res_plan_code=='4211'){
								var c1 = "GHI";	
							}else if(value.arr_res_plan_code=='4224'){
								var c1 = "GP";	
							}
							student += '<tr>';
							student += '<td>' + i++ + '</td>';
							student += '<td>' + value.lead_id + '</td>';
							student += '<td>' + c1 + '</td>';
								
							student += '<td>' + value.payment_status + '</td>';
							student += '<td>' + value.payment_date + '</td>';
							
							student += '<td>' + value.payment_date + '</td>';
							student += '<td>' + value.cust_id + '</td>';
							
							student += '<td>' + value.start_date + '</td>';
							student += '<td>' + value.end_date + '</td>';
							
							student += '<td>' + value.COI_no + '</td>';
							student += '<td>' + value.coi_duplicate_empids + '</td>';
							student += '<td>' + value.arr_error_log + '</td>';
							
							student += '<td><button class="log" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							
							
							student += '<td> N/A </td>';
							student += '<td>' + value.create_policy_type + '</td>';
							student += '<td>' + value.api_count + '</td>';
                            
  
                            student += '</tr>';
						
						
                    });
					//console.log(student);
					//('#all_recon').append(student);
					$('#all_recon').html(student); 
				}
				});
			}
            $('.coi_b').click(function(e){
				//alert("hello");
				var currValue = $(this).val();
				//var currValue = $(this).val();
				$('#view_button').val(currValue) ;
				//$('#but_next').val(currValue) ;
				//$('#but_prev').val(currValue) ;
				$('#but_prev').attr('my-name', currValue);
				$('#but_next').attr('my-name', currValue);
				$("#txt_rowid").val(0);
                getDataCoi(currValue);
                
                
            })
			function getData(currValue){
				// alert(currValue);
				var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
					var res = (cover.split("_"));
					var f = res[1];
					//console.log(f);
					//console.log(cover);
					//console.log(res);
					if(f=='4112'){
						var c1 = "GPA";
					}else if(f=='4216'){
						var c1 = "GCI";
						
					}else if(f=='4211'){
						var c1 = "GHI";	
					}else{
						var c1 = "All";	
					}
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
				var rowid = $("#txt_rowid").val();
                //debugger;
                //e.preventDefault(e);
				$.ajax({
				url: "/get_lead_data",
				type: "POST",
				async: false,
				dataType: "json",
				data : {pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id,currValue: currValue,rowid:rowid,rowperpage:rowperpage},
				success: function (response) {
					//console.log(response);
					var student = ''; 
					student += '<thead class="table-col-1">'
                    student += '<tr>'
                    student += '<th style="width:55px">Sr. no.</th>'
                    student += '<th style="width:110px">Lead_ID</th>'
                    student += '<th style="width:90px">Cover</th>'
                    student += '<th style="width:85px">Status</th>'
                    student += '<th style="width:100px">Lead Creation Date</th>'
                    student += '<th style="width:98px">Payment Date</th>'
                    student += '<th style="width:130px">Member ID</th>'
                    student += '<th style="width:110px">Start Date</th>'
                    student += '<th style="width:110px">End Date</th>'
                    student += '<th style="width:120px">COI Number</th>'
                    student += '<th style="width:100px">Duplicate COI</th>'
                    student += '<th style="width:100px">Failure Reason</th>'
                    student += '<th style="width:85px">Last Request Log</th>'
                    student += '<th style="width:85px">Last Response Log</th>'
                    student += '<th style="width:100px">Lead Details</th>'
                    student += '<th style="width:100px">API Type</th>'
					student += '<th style="width:100px">API Count</th>'
                    student += '</tr>'
                  student += '</thead>'
                  student += '<tbody>'
				  var i = 1;
						$.each(response, function (key, value) {
							if(value.plan_code=='4112'){
								var c1 = "GPA";
							}else if(value.plan_code=='4216'){
								var c1 = "GCI";
								
							}else if(value.plan_code=='4211'){
								var c1 = "GHI";	
							}else if(value.plan_code=='4224'){
								var c1 = "GP";	
							}
							student += '<tr>';
							student += '<td>' + i++ + '</td>';
							student += '<td>' + value.lead_id + '</td>';
							student += '<td>' + c1 + '</td>';
								
							student += '<td>' + value.status + '</td>';
							student += '<td>' + value.created_at + '</td>';
							
							student += '<td>' + value.txn_res + '</td>';
							student += '<td>' + value.emp_id + '</td>';
							
							student += '<td>' + value.start_date + '</td>';
							student += '<td>' + value.end_date + '</td>';
							
							student += '<td>' + value.certificate_number + '</td>';
							student += '<td>' + value.coi_duplicate_empids + '</td>';
							
							student += '<td>'+value.arr_error_log+'</td>';
							student += '<td><button class="log" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td> N/A </td>';
							student += '<td>' + value.create_policy_type + '</td>';
							student += '<td>' + value.api_count + '</td>';
                            
  
                            student += '</tr>';
						
						
                    });
					 student += '</tbody>'
					console.log(student);
					//('#all_recon').append(student);
					$('#all_recon').html(student); 
				}
				});
				
			}
            $('.lead').click(function(e){
				//alert("hello");
				var currValue = $(this).val();
				$('#view_button').val(currValue) ;
				//$('#but_next').val(currValue) ;
				//$('#but_prev').val(currValue) ;
				
				$('#but_prev').attr('my-name', currValue);
				$('#but_next').attr('my-name', currValue);
				//var but_prev = $('#but_prev').attr('my-name');
				//console.log(but_prev);
				$("#txt_rowid").val(0);
                getData(currValue);
                
            })

			
	/***************************************************** Incomplete Data Grid ******************************************************/		
			function getMismatchData(currValue){
				var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
				var res = (cover.split("_"));
					var f = res[1];
					if(f=='4112'){
						var c1 = "GPA";
					}else if(f=='4216'){
						var c1 = "GCI";
						
					}else if(f=='4211'){
						var c1 = "GHI";	
					}else{
						var c1 = "All";	
					}
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
				var rowid = $("#txt_rowid").val();
                //debugger;
                //e.preventDefault(e);
				$.ajax({
				url: "/get_mismatch_data",
				type: "POST",
				async: false,
				dataType: "json",
				data : {pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id,currValue: currValue,rowid:rowid,rowperpage:rowperpage},
				success: function (response) {
					console.log(response);
					var student = ''; 
					student += '<thead class="table-col-1">'
                    student += '<tr>'
                    student += '<th style="width:55px">Sr. no.</th>'
                    student += '<th style="width:110px">Lead_ID</th>'
                    student += '<th style="width:90px">Cover</th>'
                    student += '<th style="width:85px">Status</th>'
                    student += '<th style="width:100px">Lead Creation Date</th>'
                    student += '<th style="width:98px">Payment Date</th>'
                    student += '<th style="width:130px">Member ID</th>'
                    student += '<th style="width:110px">Start Date</th>'
                    student += '<th style="width:110px">End Date</th>'
                    student += '<th style="width:120px">COI Number</th>'
                    student += '<th style="width:100px">Duplicate COI</th>'
                    student += '<th style="width:100px">Failure Reason</th>'
                    student += '<th style="width:85px">Last Request Log</th>'
                    student += '<th style="width:85px">Last Response Log</th>'
                    student += '<th style="width:100px">Lead Details</th>'
                    student += '<th style="width:100px">API Type</th>'
					student += '<th style="width:100px">API Count</th>'
                    student += '</tr>'
                  student += '</thead>'
                  student += '<tbody>'
				  var i = 1;
						$.each(response, function (key, value) {
							if(value.arr_res_plan_code=='4112'){
								var c1 = "GPA";
							}else if(value.arr_res_plan_code=='4216'){
								var c1 = "GCI";
								
							}else if(value.arr_res_plan_code=='4211'){
								var c1 = "GHI";	
							}else if(value.arr_res_plan_code=='4224'){
								var c1 = "GP";	
							}
							student += '<tr>';
							student += '<td>' + i++ + '</td>';
							student += '<td>' + value.lead_id + '</td>';
							student += '<td>' + c1 + '</td>';
								
							student += '<td>' + value.status + '</td>';
							student += '<td>' + value.created_date + '</td>';
							
							student += '<td>' + value.created_date + '</td>';
							student += '<td>' + value.emp_id + '</td>';
							
							student += '<td>' + value.start_date + '</td>';
							student += '<td>' + value.end_date + '</td>';
							
							student += '<td>' + value.certificate_number + '</td>';
							student += '<td>' + value.coi_duplicate_empids + '</td>';
							
							student += '<td>'+value.arr_error_log+'</td>';
							student += '<td><button class="log" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							
							student += '<td> N/A </td>';
							student += '<td>' + value.create_policy_type + '</td>';
							student += '<td>' + value.api_count + '</td>';
                            
  
                            student += '</tr>';
						
						
                    });
					//console.log(student);
					//('#all_recon').append(student);
					$('#all_recon').html(student); 
				}
				});	
			}
			
			$('.mismatch').click(function(e){
				//alert("hello");
				var currValue = $(this).val();
				$('#view_button').val(currValue) ;
                //$('#but_next').val(currValue) ;
				//$('#but_prev').val(currValue) ;
				$('#but_prev').attr('my-name', currValue);
				$('#but_next').attr('my-name', currValue);
				$("#txt_rowid").val(0);
                getMismatchData(currValue); 
               
            })
			
	/************************************************ Mismatch Data ******************************************************/
			function getMismatchOriginalData(currValue){
				var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
				var res = (cover.split("_"));
					var f = res[1];
					if(f=='4112'){
						var c1 = "GPA";
					}else if(f=='4216'){
						var c1 = "GCI";
						
					}else if(f=='4211'){
						var c1 = "GHI";	
					}else{
						var c1 = "All";	
					}
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
				var rowid = $("#txt_rowid").val();
                //debugger;
                //e.preventDefault(e);
				$.ajax({
				url: "/get_mismatch_original_data",
				type: "POST",
				async: false,
				dataType: "json",
				data : {pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id,currValue: currValue,rowid:rowid,rowperpage:rowperpage},
				success: function (response) {
					//console.log(response);
					
					
					var student = ''; 
					student += '<thead class="table-col-1">'
                    student += '<tr>'
                    student += '<th style="width:55px">Sr. no.</th>'
                    student += '<th style="width:110px">Lead_ID</th>'
                    student += '<th style="width:90px">Cover</th>'
                    student += '<th style="width:85px">Status</th>'
                    student += '<th style="width:100px">Lead Creation Date</th>'
                    student += '<th style="width:98px">Payment Date</th>'
                    student += '<th style="width:130px">Member ID</th>'
                    student += '<th style="width:110px">Start Date</th>'
                    student += '<th style="width:110px">End Date</th>'
                    student += '<th style="width:120px">COI Number</th>'
                    student += '<th style="width:100px">Duplicate Product</th>'
                    student += '<th style="width:100px">Duplicate Leads</th>'
                    student += '<th style="width:85px">Last Request Log</th>'
                    student += '<th style="width:85px">Last Response Log</th>'
                    student += '<th style="width:100px">Lead Details</th>'
                    student += '<th style="width:100px">API Type</th>'
					student += '<th style="width:100px">API Count</th>'
                    student += '</tr>'
                  student += '</thead>'
                  student += '<tbody>'
				  var i = 1;
						$.each(response, function (key, value) {
							if(value.arr_res_plan_code=='4112'){
								var c1 = "GPA";
							}else if(value.arr_res_plan_code=='4216'){
								var c1 = "GCI";
								
							}else if(value.arr_res_plan_code=='4211'){
								var c1 = "GHI";	
							}else if(value.arr_res_plan_code=='4224'){
								var c1 = "GP";	
							}
							student += '<tr>';
							student += '<td>' + i++ + '</td>';
							student += '<td>' + value.lead_id + '</td>';
							student += '<td>' + c1 + '</td>';
								
							student += '<td>' + value.status + '</td>';
							student += '<td>' + value.created_at + '</td>';
							
							student += '<td>' + value.created_at + '</td>';
							student += '<td>' + value.emp_id + '</td>';
							
							student += '<td>' + value.start_date + '</td>';
							student += '<td>' + value.end_date + '</td>';
							
							student += '<td>' + value.certificate_number + '</td>';
							student += '<td>' + value.coi_duplicate_empids + '</td>';
							
							student += '<td>'+value.duplicate_lead+'</td>';
							student += '<td><button class="log" value='+value.lead_id+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							
							student += '<td><button class="log" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							
							student += '<td> N/A </td>';
							student += '<td>' + value.create_policy_type + '</td>';
							student += '<td>' + value.api_count + '</td>';
                            
  
                            student += '</tr>';
						
						
                    });
					$('#all_recon').html(student); 
					
				 
				}
				});	
			}
			
			
			$('.mismatch_original').click(function(e){
				//alert("hello");
				var currValue = $(this).val();
				$('#view_button').val(currValue) ;
               
				$('#but_prev').attr('my-name', currValue);
				$('#but_next').attr('my-name', currValue);
				$("#txt_rowid").val(0);
                getMismatchOriginalData(currValue); 
               
            })
			
			
			/************************************************ payment Redirect  ******************************************************/
			function getPaymentRedirectData(currValue){
				var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
				var res = (cover.split("_"));
					var f = res[1];
					if(f=='4112'){
						var c1 = "GPA";
					}else if(f=='4216'){
						var c1 = "GCI";
						
					}else if(f=='4211'){
						var c1 = "GHI";	
					}else{
						var c1 = "All";	
					}
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
				var rowid = $("#txt_rowid").val();
                //debugger;
                //e.preventDefault(e);
				$.ajax({
				url: "/get_payment_redirect",
				type: "POST",
				async: false,
				dataType: "json",
				data : {pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id,currValue: currValue,rowid:rowid,rowperpage:rowperpage},
				success: function (response) {
					//console.log(response);
					
					
					var student = ''; 
					student += '<thead class="table-col-1">'
                    student += '<tr>'
                    student += '<th style="width:55px">Sr. no.</th>'
                    student += '<th style="width:110px">Lead_ID</th>'
                    student += '<th style="width:90px">Cover</th>'
                    student += '<th style="width:85px">Status</th>'
                    student += '<th style="width:100px">Lead Creation Date</th>'
                    student += '<th style="width:98px">Payment Date</th>'
                    student += '<th style="width:130px">Member ID</th>'
                    student += '<th style="width:110px">Start Date</th>'
                    student += '<th style="width:110px">End Date</th>'
                    student += '<th style="width:120px">COI Number</th>'
                    student += '<th style="width:100px">Duplicate Leads</th>'
                    student += '<th style="width:100px">Failure Reason</th>'
                    student += '<th style="width:85px">Last Request Log</th>'
                    student += '<th style="width:85px">Last Response Log</th>'
                    student += '<th style="width:100px">Lead Details</th>'
                    student += '<th style="width:100px">API Type</th>'
					student += '<th style="width:100px">API Count</th>'
                    student += '</tr>'
                  student += '</thead>'
                  student += '<tbody>'
				  var i = 1;
						$.each(response, function (key, value) {
							if(value.arr_res_plan_code=='4112'){
								var c1 = "GPA";
							}else if(value.arr_res_plan_code=='4216'){
								var c1 = "GCI";
								
							}else if(value.arr_res_plan_code=='4211'){
								var c1 = "GHI";	
							}else if(value.arr_res_plan_code=='4224'){
								var c1 = "GP";	
							}
							student += '<tr>';
							student += '<td>' + i++ + '</td>';
							student += '<td>' + value.lead_id + '</td>';
							student += '<td>' + c1 + '</td>';
								
							student += '<td>' + value.status + '</td>';
							student += '<td>' + value.created_at + '</td>';
							
							student += '<td>' + value.created_at + '</td>';
							student += '<td>' + value.emp_id + '</td>';
							
							student += '<td>' + value.start_date + '</td>';
							student += '<td>' + value.end_date + '</td>';
							
							student += '<td>' + value.certificate_number + '</td>';
							student += '<td>' + value.coi_duplicate_empids + '</td>';
							
							student += '<td>'+value.arr_error_log+'</td>';
							student += '<td><button class="log" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							
							student += '<td> N/A </td>';
							student += '<td>' + value.create_policy_type + '</td>';
							student += '<td>' + value.api_count + '</td>';
                            
  
                            student += '</tr>';
						
						
                    });
					$('#all_recon').html(student); 
					
				 
				}
				});	
			}
			
			
			$('.payment_redirect').click(function(e){
				//alert("hello");
				var currValue = $(this).val();
				$('#view_button').val(currValue) ;
               
				$('#but_prev').attr('my-name', currValue);
				$('#but_next').attr('my-name', currValue);
				$("#txt_rowid").val(0);
                getPaymentRedirectData(currValue); 
               
            })
			
			/************************************************ payment Success ******************************************************/
			function getPaymentSuccessData(currValue){
				var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
				var res = (cover.split("_"));
					var f = res[1];
					if(f=='4112'){
						var c1 = "GPA";
					}else if(f=='4216'){
						var c1 = "GCI";
						
					}else if(f=='4211'){
						var c1 = "GHI";	
					}else{
						var c1 = "All";	
					}
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
				var rowid = $("#txt_rowid").val();
                //debugger;
                //e.preventDefault(e);
				$.ajax({
				url: "/get_payment_success",
				type: "POST",
				async: false,
				dataType: "json",
				data : {pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id,currValue: currValue,rowid:rowid,rowperpage:rowperpage},
				success: function (response) {
					//console.log(response);
					
					
					var student = ''; 
					student += '<thead class="table-col-1">'
                    student += '<tr>'
                    student += '<th style="width:55px">Sr. no.</th>'
                    student += '<th style="width:110px">Lead_ID</th>'
                    student += '<th style="width:90px">Cover</th>'
                    student += '<th style="width:85px">Status</th>'
                    student += '<th style="width:100px">Lead Creation Date</th>'
                    student += '<th style="width:98px">Payment Date</th>'
                    student += '<th style="width:130px">Member ID</th>'
                    student += '<th style="width:110px">Start Date</th>'
                    student += '<th style="width:110px">End Date</th>'
                    student += '<th style="width:120px">COI Number</th>'
                    student += '<th style="width:100px">Duplicate Leads</th>'
                    student += '<th style="width:100px">Failure Reason</th>'
                    student += '<th style="width:85px">Last Request Log</th>'
                    student += '<th style="width:85px">Last Response Log</th>'
                    student += '<th style="width:100px">Lead Details</th>'
                    student += '<th style="width:100px">API Type</th>'
					student += '<th style="width:100px">API Count</th>'
                    student += '</tr>'
                  student += '</thead>'
                  student += '<tbody>'
				  var i = 1;
						$.each(response, function (key, value) {
							if(value.arr_res_plan_code=='4112'){
								var c1 = "GPA";
							}else if(value.arr_res_plan_code=='4216'){
								var c1 = "GCI";
								
							}else if(value.arr_res_plan_code=='4211'){
								var c1 = "GHI";	
							}else if(value.arr_res_plan_code=='4224'){
								var c1 = "GP";	
							}
							student += '<tr>';
							student += '<td>' + i++ + '</td>';
							student += '<td>' + value.lead_id + '</td>';
							student += '<td>' + c1 + '</td>';
								
							student += '<td>' + value.status + '</td>';
							student += '<td>' + value.created_at + '</td>';
							
							student += '<td>' + value.created_at + '</td>';
							student += '<td>' + value.emp_id + '</td>';
							
							student += '<td>' + value.start_date + '</td>';
							student += '<td>' + value.end_date + '</td>';
							
							student += '<td>' + value.certificate_number + '</td>';
							student += '<td>' + value.coi_duplicate_empids + '</td>';
							
							student += '<td>'+value.arr_error_log+'</td>';
							
							student += '<td><button class="log" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							student += '<td><button class="logres" value='+value.lead_id+' product_name='+value.product_id+' p_cover='+c1+' style="background-color: #4CAF50; color:white;width: 37px;border: none;cursor: pointer; padding: 10px 25px">View</button></td>';
							
							student += '<td> N/A </td>';
							student += '<td>' + value.create_policy_type + '</td>';
							student += '<td>' + value.api_count + '</td>';
                            
  
                            student += '</tr>';
						
						
                    });
					$('#all_recon').html(student); 
					
				 
				}
				});	
			}
			
			
			$('.payment_success').click(function(e){
				//alert("hello");
				var currValue = $(this).val();
				$('#view_button').val(currValue) ;
               
				$('#but_prev').attr('my-name', currValue);
				$('#but_next').attr('my-name', currValue);
				$("#txt_rowid").val(0);
                getPaymentSuccessData(currValue); 
               
            })
			
			
			
        });
	
	$(document).ready(function(){
            /*$('#exportReconExcel').click(function(e){
				//alert("hello");
				$('#gridHidden').val($('#view_button').val());
				$('#pTypeHidden').val($('#product_type').val());
				$('#coverHidden').val($('#product_cover_type').val());
				$('#recondatesHidden').val($('#recondates').val());
				$('#time_toHidden').val($('#time_to').val());
				$('#time_fromHidden').val($('#time_from').val());
				$('#lead_idHidden').val($('#lead_id').val());
				$( "#excel_form" ).submit();
				/*var pType = $('#product_type').val();
                var cover = $('#product_cover_type').val();
                var recondates = $('#recondates').val();
                var time_to = $('#time_to').val();
                var time_from = $('#time_from').val();
                var lead_id = $('#lead_id').val();
               
				$.ajax({
				url: "/get_grid_excel",
				type: "POST",
				async: false,
				//dataType: "json",
				data : {grid: grid,pType: pType,cover: cover,recondates: recondates,time_to: time_to,time_from: time_from,lead_id: lead_id},
				success: function (data) {
					 var $a = $("<a>");
    $a.attr("href",data.file);
    $("body").append($a);
    $a.attr("download","file.xls");
    //$a[0].click();
    //$a.remove();
				}
				});
                
            })*/
        });	
		
	
	$(document).on('click', '.log', function(e)
	{  
           var l_id = $(this).val();
		   var product_name = $(this).attr('product_name');
		   var p_cover = $(this).attr('p_cover');
		  
			//console.log(l_id);
		
			$.ajax({
				url: "/get_log_data",
				type: "POST",
				async: false,
				data : {l_id:l_id, p_cover:p_cover, product_name:product_name},
				dataType: "json",
				success: function (data) {
				console.log(data);	
				var student = ''; 
				    student += '<table style="width:100%" class="table-responsive">';
					student += '<thead class="table-col-1">';
                    student += '<tr>';
                    student += '<th style="width:55px">Sr. no.</th>';
                    student += '<th style="width:110px">Lead_ID</th>';
                    student += '</tr>';
					student += '</thead>';
					student += '<tbody>';
					
				  var i = 1;
					student += '<tr>';
					if(data.length > 0){
						student += '<td style="display:flex;width: 57px;">' + i++ + '</td>';
						student += '<td>' + data[0].req + '</td>';
					}else{
						student += '<td style="display:flex;width: 57px;">' + i++ + '</td>';
						student += '<td> No Data Found </td>';
					}
						
						student += '</tr>';
					//console.log((data[0]->req));
						
                    student += '</tbody>';
					student += '</table>';
				
                    $('#req_re').html(student);    
                    $('#Request').modal('show');  
                }  
			});  
			  
	});  
	
	$(document).on('click', '.logres', function(e)
	{  
           var l_id = $(this).val();
		   var product_name = $(this).attr('product_name');
		   var p_cover = $(this).attr('p_cover');
			//console.log(l_id);
		
			$.ajax({
				url: "/get_res_data",
				type: "POST",
				async: false,
				data : {l_id:l_id, p_cover:p_cover, product_name:product_name},
				dataType: "json",
				success: function (data) {
				//console.log(data);	
				var student = ''; 
				    student += '<table style="width:100%" class="table-responsive">';
					student += '<thead class="table-col-1">';
                    student += '<tr>';
                    student += '<th style="width:55px">Sr. no.</th>';
                    student += '<th style="width:110px">Lead_ID</th>';
                    student += '</tr>';
					student += '</thead>';
					student += '<tbody>';
				  var i = 1;
					student += '<tr>';
						//student += '<td style="display:flex;width: 57px;">' + i++ + '</td>';
						//student += '<td>' + data[0].res + '</td>';
					if(data.length > 0){
						student += '<td style="display:flex;width: 57px;">' + i++ + '</td>';
						student += '<td>' + data[0].res + '</td>';
					}else{
						student += '<td style="display:flex;width: 57px;">' + i++ + '</td>';
						student += '<td> No Data Found </td>';
					}
						student += '</tr>';
						
					//console.log((data[0]->req));
						
                    student += '</tbody>';
					student += '</table>';
				
                    $('#req_re').html(student);    
                    $('#Request').modal('show');  
                }  
			});  
			  
	});
	
	
	$(document).ready(function() {
		$('#all_recon').DataTable();
	});
		
		$(document).ready(function(){
			
			
        });
	 //For Dynamic Cover Selection On Product Type Change --End//


	//view request modal start
	$(document).on('click', '.reqs', function(e)
	{	
		 console.log("Changing value Req")
	     $('#rheader').text("Request");
	     $("#req_re").html("")
	     var did=$(this).data("id") ;
	     var leadid=$(this).data("leadid") ;
	     var cover=$(this).data("cover") ;
	     var status=$(this).data("status") ;
	      $.ajax({
	  type: 'POST',
	  url: '/reqs_recon_log',
	  data:{did:did,leadid:leadid,cover:cover,status:status},
	  success: function(data)
	  {	
	  	 $("#req_re").html(data)
	  }
	});
	  $('#Request').modal('show');
	});
	//view request modal end

	//view response modal start
	$(document).on('click', '.ress', function(e)
	{
			console.log("Changing value Res")
	      	$('#rheader2').text("Response");
	      	$("#res_re").html("")
	    	var did=$(this).data("id") ;
	     	var leadid=$(this).data("leadid") ;
	     	var cover=$(this).data("cover") ;
	     	var status=$(this).data("status") ;
	      $.ajax({
	      	type: 'POST',
		  url: '/ress_recon_log',
		  data:{did:did,leadid:leadid,cover:cover,status:status},
		  success: function(data)
		  {
	  	     $("#res_re").html(data)
		  }
	});
	  $('#Response').modal('show');
	});
	//view response modal end


	 //Serach In Dashbpard Ajax Call --Start//
 $(document).on('keyup','#keyword_search',function(e)
	{
		var keyword_search = $(this).val();
		
		// if(keyword_search != ''){
			$.ajax({
		      url: "",
		      type: "POST",
		      async: false,
		      data: {keyword_search: keyword_search},
		      dataType: 'json',
		      success: function (response) {
		        $('#all_recon').html(response.html);
		      }
		    });
		// }

	});
 	//Serach In Dashbpard Ajax Call --End//
	

});