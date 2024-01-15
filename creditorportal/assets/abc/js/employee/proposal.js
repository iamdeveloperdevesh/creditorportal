
$(document).ready(function () {
    var proposal_id = 0;
    $(document).on( 'click', 'table tr', function (e) {
        if(e.target.className == "ti-eye") { //last view
		
            var row = $(this).closest('tr');
            var data = $('#example').dataTable().fnGetData(row);
            var proposal_td = $(this).closest('tr').find('td').eq(0).text();
   
            var emp_id_proposal = data[11];
			var status_prop = data[7];
			var count = data[13];
			
			if(status_prop == 'Discrepancy Open')
			{
				$(".app_proposal").attr('disabled', 'disabled');
				$(".app_discrepancy").removeAttr('disabled', 'disabled');
			}
			else if(status_prop == 'Issued' || count != 0){
				$(".app_proposal").attr('disabled', 'disabled');
				$(".app_discrepancy").attr('disabled', 'disabled');
			}
			else if(status_prop.search("<br>"))
			{
				
				var status_splits = status_prop.split("<br>");
				
				var status_split_arr = status_splits[0];
			
				if($.trim(status_split_arr) == 'Rejected')
				{
					$(".app_proposal").attr('disabled', 'disabled');
				$(".app_discrepancy").attr('disabled', 'disabled');
					
				}
				else
				{
					$(".app_discrepancy").removeAttr('disabled', 'disabled');
					$(".app_proposal").removeAttr('disabled', 'disabled');
				}
			}
			else
			{
				$(".app_discrepancy").removeAttr('disabled', 'disabled');
					$(".app_proposal").removeAttr('disabled', 'disabled');
				
			}
			
			
            $('#emp_id_proposal').val(emp_id_proposal);
            $("#proposal_ids").text(proposal_td);
            var parent_id = data[9];
            var policy_detail_id = data[10];
            proposal_id = data[11];
 
            //alert(parent_id+'  '+policy_detail_id+'  '+proposal_id);

            $.post("/employee/getMemberProposalDet", {
                parent_id: parent_id,
                policy_detail_id: policy_detail_id,
                proposal_id: proposal_id,
                proposal_no:proposal_td,
            }, function(data) {
         
                es = JSON.parse(data);

                ed = es.response

  
                if(ed.length > 0) {
					
							$("#product_subtype_name").html(es.policy_name);	
                    $("#familyModal").modal();
                    $("#familyTable").html("");
					$("#familyTables").html("");
					
				
					  var i = 0;
                    ed.forEach(function(er) {
						var str ='<table id="example" class="table table-striped table-hover" style="width:100%" style="display:inline-table;"> <thead style="background-color:#da9089;" class="text-white"><tr><th>Relation </th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Date Of Birth</th><th>AGE</th><th>Age Type (Years/Days) </th><th> Sum Insured</th></tr></thead><tbody id="familyTable_'+i+'"> </tbody></table>';
					
					$("#familyTables").append(str);
						er.forEach(function(e)
						{
					
                       var stri = "<tr><td>"+e.fr_name+"</td>";
                        stri += "<td>"+e.emp_firstname+"</td>";
                        stri += "<td>"+e.emp_lastname+"</td>";
                        stri += "<td>"+e.gender+"</td>";
                        stri += "<td>"+e.bdate+"</td>";
                        stri += "<td>"+e.age+"</td>";
                        stri += "<td>"+e.age_type+"</td>";
                        stri += "<td>"+e.policy_mem_sum_insured+"</td></tr>";
                        stri += "<td class='propsal_nos' style='display:none'>"+e.proposal_no+"</td></tr>";
                        $("#familyTable_"+i).append(stri);
					});
					i++;
					
                    }); 

                    if(es.nominee.length>0)
                    {
                   $("#familyModal").modal();
                    $("#nomineeTable").html("");
                    es.nominee.forEach(function(es) {

       
                        var str = "<tr><td>"+es.fr_name+"</td>";
                        str += "<td>"+es.nominee_fname+"</td>";
                        str += "<td>"+es.nominee_lname+"</td>";
                     
                        $("#nomineeTable").append(str);
                    });
                    } 

                       if(es.question.length>0)
                    {
                   $("#familyModal").modal();
          
                    $("#healthTable").html("");
                    es.question.forEach(function(esi) {

       
                        var str = "<tr><td>"+esi.content+"</td>";
                        str += "<td>"+esi.format+"</td>";
                   
                     
                        $("#healthTable").append(str);
                    });
                    } 
                    if(es.payment.length>0)
                    {
                   $("#familyModal").modal();
          
                    $("#paymentTable").html("");
                    es.payment.forEach(function(esip) {

       
                        var str = "<tr><td>"+esip.payment_type+"</td>";
                        str += "<td>"+esip.cheque_date +"</td>";
                        str += "<td>"+esip.cheque_no+"</td>";
                        str += "<td>"+esip.account_no+"</td>";
                        str += "<td>"+esip.bank_name+"</td>";
                        str += "<td>"+esip.branch+"</td>";
                     
                        $("#paymentTable").append(str);
                    });
                    } 
                    
                    if(es.document.length>0)
                    {
                        $("#familyModal").modal();
                
                            $("#documentTable").html("");
                            var ops;
                            es.document.forEach(function(esd) {
                            
                                 ops = esd.ops_type

                                if(esd.autorenewal == 'Y')
                                {
                                    ops = 'Autorenewal : Y';
                                }
                                if(esd.doc_type!='' && esd.doc_type !=null)
                                {
                                    ops = esd.doc_type;
                                }
                                
                                var str = "<tr><td>"+ops+"</td>";
                                str += "<td><a target='_blank' href='"+esd.path+"'><img style='width:50px;' onError=this.onerror=null;this.src='/public/images/pdf1.jpg'; src='"+esd.path+"' /></a></td>";
                            
                                $("#documentTable").append(str);
                            });
                    } 
                }else {
                    alert("No Member");
                }


                console.log(e);
            })


        }
    });


    

    // $("#proposalApprove").on("click", function() {
        // saveProposalData("Issued");
    // });

    $("#proposalReject").on("click", function() {
      //  saveProposalData("Rejected");
    });



    var table = $('#example').DataTable({
		 order:[[9,"desc"]],
        "columnDefs": [
            {
                "targets": [ 9 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 10 ],
                "visible": false
            },
            {
                "targets": [ 11 ],
				
                "visible": true
            },
			{
                "targets": [ 13 ],
				
                "visible": false
            },
            {
                "targets": [ 12 ],
                "visible": false
            }, {
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            } 
        ]
    });

    $('body').on('click', "#exportExcel", function () {
		
     var policy_detail_id = $('#masPolicy option:selected').val();
	 
	 if(!policy_detail_id)
		return;
	 
	 $("#policy_no").val(policy_detail_id);
	 
	 $('#exportExcelForm').submit();
	 
	
        // var file = new Blob(["<table>" + $('#example').html() + "</table>"], { type: "application/vnd.ms-excel" });

        // var url = URL.createObjectURL(file);

        // var data = new Date();


        // var a = $("<a />", {
            // href: url,
            // download: "proposal"
        // })
            // .appendTo("body")
            // .get(0)
            // .click();
    });

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




    $(document).on('change', '#masPolicy', function () {

        //customer search
        var policy_detail_id = $('#masPolicy option:selected').val();

        var customer = ($('#masPolicy option:selected').attr('data-customer'));
        if (policy_detail_id && customer == 'Y') {
            $("#customer_serach_button").show();
        }
        else {
            $("#customer_serach_button").hide();
        }
        // if (policy_detail_id) {
        //     $("#subtypediv").show();
        //     $("#policySubType").removeClass("ignore");
        // }
        // else {
        //     $("#subtypediv").hide();
        //     $("#policySubType").addClass("ignore");
        // }

        // $("#policySubType").val($('#masPolicy option:selected').attr('data-id'));
        $("#policy_no").val($('#masPolicy option:selected').val());
        var policy_no = $('#masPolicy option:selected').val();
        var view_data = $("#view_data").val();
        
        $.post("/employee/getProposalData", {
            policy_no: policy_no,
            view:view_data
        
        }, function(e) {
			var status_data;
            table
                .clear()
                .draw();
                
            e = JSON.parse(e);
				var st = [];
            e.forEach(function(e1) {
			
				if(e1.status == 'Rejected')
				{
					
					status_data = e1.status + " <br>Remark : "+ e1.remark_reject;

				}
				else
				{
					status_data = e1.status;
					
				
				}
                var arr = [
                    e1.proposal_no,
                    e1.policy_no,
                    e1.emp_firstname,
                    e1.emp_lastname,
                    e1.mob_no,
                    e1.sum_insured,
                    e1.premium,
                    status_data,
                    e1.created_date,
                    e1.policy_parent_id,
                    e1.policy_detail_id,
                    e1.proposal_id,
                    e1.created_by,
					e1.count,
					e1.coi_number,
                    "<i class='ti-eye' style='font-size:17px'></i>"
                ];

         
                table.row.add(arr).draw();
            });
        });
    });
});

$("#Discrepancy").on("click", function() {

    var Proposalno = $("#proposal_ids").text();
       $('#proposal_ids').val(Proposalno);

       var d_emp_pro = $('#emp_id_proposal').val();
       $('#des_pro').val(d_emp_pro);
    });

	$("#proposalReject1").on("click", function() {

    var Proposalno = $("#proposal_ids").text();
       $('#proposal_ids_reject').val(Proposalno);

       var d_emp_pro = $('#emp_id_proposal').val();
       $('#rej_pro').val(d_emp_pro);
    });
	
$("#discrepancy_submit").validate({
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
                data: $("#discrepancy_submit").serialize(),
                async: true,
                success: function(data) {
           
                  if(data == 'true')
                  {
                 
                  swal({
                                title: "success",
                                text: "Created Discrepancy Successfully",
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
	
	$("#rejection_submit").validate({
		
		 rules: {
            rej_remarks: {
                required: true
            },
            
           
        },
        messages: {
            rej_remarks: "Please specify Remarks.",
           
            
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
                url: "/employee/proposalSave",
                type: "POST",
                data: $("#rejection_submit").serialize(),
                async: true,
                success: function(data) {
               statusMsg = JSON.parse(data);

                  swal({
                title: "success",
                text: statusMsg.message
				+" SuccessFully",
                type: "success",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Ok",
                closeOnConfirm: true
              },
              function(){
                location.reload();
              });
            
                   }
            });
        }
	});
	function myFunction()
{

	        saveProposalData("Issued");
}
	function saveProposalData(status) {
		ajaxindicatorstart();
	 var  proposal_id =  $("#emp_id_proposal").val();

        $.post("/employee/proposalSave", {
            proposal_id_post: proposal_id,
            status: status
        }, function(data) {
			ajaxindicatorstop();
			statusMsg = JSON.parse(data);
			if(statusMsg.statusErr == 'false' || statusMsg.statusErr == 'Failure' )
			{
				
				 swal({
                title: "Alert",
                text: statusMsg.message,
                type: "Alert",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Ok",
                closeOnConfirm: true
              },
              function(){
                location.reload();
              });
			}
			else{ 
				ajaxindicatorstop();
				 swal({
                title: "success",
                text: statusMsg.message,
                type: "success",
                showCancelButton: false,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Ok",
                closeOnConfirm: true
              },
              function(){
                location.reload();
              });
			}
           
        });
    }