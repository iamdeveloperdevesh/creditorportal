// function ecard_link(member_id,policy_no,status,tpa_id){
	// policy_no = policy_no.trim();
	// status = status.trim();
	// policy_no = policy_no.replace(/\//g, "\/");
	// member_id = member_id.trim();
         // if(status == 1){
         // $.ajax({
                // url: "/get_ecard",
                // type: "POST",
                // data: {
                    // "policy_no": policy_no, "member_id": member_id,"tpa_id":tpa_id
                // },
                // async: false,
                // dataType: "json",
                // success: function (response) {
                // var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData[0].EcardLink;
                // window.location.href = ecard_link_url;
            // }
        // });  
         // }
         // else{
             // swal("","member is still not active");
         // }
     // }
	 
	 
	 function ecard_link(member_name,member_id,policy_no,status,tpa_id){

         if(status == 1){
         $.ajax({
                url: "/get_ecard",
                type: "POST",
                data: {
                    "policy_no": policy_no, "member_name": member_name,"tpa_id":tpa_id,"member_id": member_id,"ecard" : true
                },
                async: false,
               // dataType: "json",
                success: function (response) {
                    
                    
				if(response){
                               var response =  JSON.parse(response);     
				if(response.tpa_id == '2'){
               //var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData[0].EcardLink.toString();
                window.location.replace(response.response);
            } else if(response.tpa_id == '4'){
                window.location.replace(response.response);
            }
            else{
                //var response =  JSON.parse(response);
                 var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData.EcardLink.toString();
                window.location.replace(ecard_link_url);
            }
        }
				}
        });  
         }
         else{
             swal("","Health E-card will be available post enrolment window");
         }
     }
$(document).ready(function(){
var tpa_id = '';
$.ajax({
                url: "/employer/get_all_policy_numbers",
                type: "POST",
                async: false,
                dataType: "json",
				data : {"employer" : true},
                success: function (response) {
					
                     $('#policy_no').empty();
                     $('#policy_no').append('<option value=""> Select policy type</option>');
                        for (i = 0; i < response.length; i++) { 
                        var date = response[i].end_date.split("-");
                        var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
                        var current_date = new Date();
                            /* if(date > current_date){ */
                          $('#policy_no').append('<option data-id ="'+ response[i].policy_no+'" value="' + response[i].policy_sub_type_id + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                    /* }  */
                  }
                }
            }); 
   $('#policy_no').on('change', function() {  

  var z = $("#policy_no option:selected").attr("data-id");
  $.ajax({
                url: "/get_all_employees_from_policy_no",
                type: "POST",
                async: false,
                dataType: "json",
               data : {policy_no: this.value, policy_number: z}, 
                success: function (response) {
                  
                    $('#employee').empty();
                     $('#employee').append('<option value=""> Select Employee</option>');
                     for (i = 0; i < response.length; i++) {
                        $('#employee').append('<option value="' + response[i].id + '">' + response[i].emp_code + '</option>');  
                     
                    }
                }
            }); 
  
  
  });
   $(document).on('click', '.ecard_link_download', function(){
   var z = ($(this).attr("data-id"));
   var d = z.split(",");
   ecard_link(d[0],d[1],d[2],d[3],d[4]);
   
   
   });
  $('#employee').on('change', function() {  


  $.ajax({
                url: "/ecard_data_from_policy_no",
                type: "POST",
                async: false,
                dataType: "json",
               data : {policy_no: $('#policy_no option:selected').attr("data-id"),
                        emp_id : this.value
                        
          }, 
                success: function (response) {
                    console.log(response);
                  console.log(response.constructor);
                    $('#ecard_table').empty();
                        var status = 0;
                        var policy_no = $('#policy_no option:selected').attr("data-id");
						
						policy_no = policy_no.replace(" ", "");
						//policy_no = policy_no.replace(/\//g, "-");
						
                     for (i = 0; i < response.length; i++) {
                         if(response[i].status == "Active"){
                              status = 1; 
                         }
						
						 var str = response[i].tpa_member_name+","+response[i].tpa_member_id+","+policy_no+","+status+","+response[i].TPA_id;
                        $('#ecard_table').append('<tr><td style="width: 130px;"> <div class="form-group"> <input class="form-control align-center" type="text" value='+response[i].name+' id="example-tel-input" readonly=""> </div> </td><td style="width: 130px;"> <div class="form-group"> <input class="form-control align-center" type="text" value='+response[i].relationship+' id="example-tel-input" readonly=""> </div> </td><td><ul class="d-flex justify-content-center"> <li style = "cursor: pointer;" class = "ecard_link_download"data-id="'+str+'"><a  class="text-secondary"> <img src="/public/assets/images/new-icons/pdf%20(1).png"></a> </li> </ul></td></tr>');
                     
                    }
                }
            }); 
  
  
  });
  




});


