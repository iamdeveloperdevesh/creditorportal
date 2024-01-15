$('document').ready(function () {

    $.ajax({
        type: "POST",
        url: "/broker/get_all_dashboard_employer",
        async: false,
        success: function (e) {
            // debugger;
            var data = JSON.parse(e);
            if (data.length != 0) {
                $('#employer_name').append('<option value=""> Select Employer</option>');
                for (i = 0; i < data.length; i++) {
                    $('#employer_name').append('<option   data-id = ' + data[i].company_id + '  value="' + data[i].company_id + '">' + data[i].comapny_name + '</option>');

                }
            }
        },
        error: function () {

        }
    });


    $("#employer_name").change(function () {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/get_all_policy_against_company",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function (response) {
              
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                }
            }
        });
    });


//    $("#insurer_id").change(function () {
//        var company_id = $(this).val();
//        $.ajax({
//            url: "/broker/get_policy_type",
//            type: "POST",
//            data: {company_id: company_id},
//            dataType: "json",
//            success: function (response) {
//                $('#policy_type').empty();
//                $('#policy_type').append('<option value=""> Select Policy</option>');
//                $('#policy_type').append('<option value="1">Group</option>');
//                $('#policy_type').append('<option value="2">Voluntry</option>');
//            }
//        });
//    });

//    $("#policy_type").change(function () {
//        var company_id = $('#employer_name option:selected').val();
//        var insurer_id = $('#insurer_id option:selected').val();
//        var policy_type = $('#policy_type option:selected').val();
//        $.ajax({
//            url: "/get_all_policy_against_company_and_insurer",
//            type: "POST",
//            data: {company_id: company_id, insurer_id: insurer_id, policy_type: policy_type},
//            dataType: "json",
//            success: function (response) {
//                $('#policy_no').empty();
//                $('#policy_no').append('<option value=""> Select Policy</option>');
//                for (i = 0; i < response.length; i++) {
//                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
//                }
//            }
//        });
//    });
    
    $("#policy_no").change(function () {
        var company_id = $('#employer_name option:selected').val();
        var insurer_id = $('#insurer_id option:selected').val();
        var policy_type = $('#policy_type option:selected').val();
         var policy_no = $('#policy_no option:selected').val();
        $.ajax({
            url: "/broker/get_all_employee_against_company_and_insurer",
            type: "POST",
            data: {company_id: company_id, policy_no:policy_no },
            dataType: "json",
            success: function (response) {
                $('#emp_name').empty();
                 $('#emp_code').empty();
                $('#emp_name').append('<option value=""> Select Employee</option>');
                for (i = 0; i < response.length; i++) {
                    $('#emp_name').append('<option value="' + response[i].emp_id + '">' + (response[i].emp_firstname + " " + response[i].emp_lastname) + '</option>');
                     
                }
            }
        });
    });
    
     $("#emp_name").change(function () {
        var company_id = $('#employer_name option:selected').val();
        var insurer_id = $('#insurer_id option:selected').val();
        var policy_type = $('#policy_type option:selected').val();
         var policy_no = $('#policy_no option:selected').val();
         var emp_name = $('#emp_name option:selected').val();
        $.ajax({
            url: "/broker/get_all_mem_against_employee",
            type: "POST",
            data: {company_id: company_id,  policy_no:policy_no,emp_name:emp_name },
            dataType: "json",
            success: function (response) {
                $('#member_id').empty();
               
                $('#member_id').append('<option value=""> Select Member</option>');
                for (i = 0; i < response.length; i++) {
                    $('#member_id').append('<option family-id="' + response[i].family_id + '" member-id="' + response[i].emp_member_id + '" value="' + response[i].id + '">' + response[i].relationship + '</option>');
                     
                }
            }
        });
    });
    
    $("#ecard_submit").click(function(){
        var company_id = $('#employer_name option:selected').val();
        
         var insurer_id = $('#insurer_id option:selected').val();
         var policy_type = $('#policy_type option:selected').val();
          var policy_no = $('#policy_no option:selected').val();
          var emp_name = $('#emp_name option:selected').val();
         var memberId =  $('#member_id option:selected').attr("member-id");
         var familyId =  $('#member_id option:selected').attr("family-id");
        
          if(company_id == ""){
              swal("", "Please select company_id");
              return false;
          }
          if(policy_no == ""){
              swal("", "Please select policy subtype");
              return false;
          }
          if(emp_name == ""){
              swal("", "Please select employee name");
              return false;
          }
          if(policy_type == ""){
              swal("", "Please select policy type");
              return false;
          }
          
          
           $.ajax({
                url: "/broker/get_broker_ecard_data_from_policy_no",
                type: "POST",
                async: false,
                dataType: "json",
               data : { policy_no: policy_no,
                        emp_id : emp_name,
                        company_id:company_id,
                        insurer_id:insurer_id,
                        policy_type:policy_type,
                        memberId:memberId,
                        familyId:familyId
                        
                     }, 
                success: function (response) {
				//	debugger;
                // return false;
                    $('#ecards_table').empty();
                        var status = 0;
//                        var policy_no = $('#policy_no option:selected').attr("data-id");
//						
//						policy_no = policy_no.replace(" ", "");
//						policy_no = policy_no.replace(/\//g, "-");
						
                     for (i = 0; i < response.length; i++) {
                         if(response[i].status == "Active"){
                              status = 1; 
                         }
 //var str = response[i].TPA_name+","+response[i].policy_no+","+status+","+response[i].TPA_id;
  var str = response[i].tpa_member_name+","+response[i].tpa_member_id+","+response[i].policy_no+","+status+","+response[i].TPA_id;
                        $('#ecards_table').append('<tr><td style="width: 130px;"> <div class="form-group"> <input class="form-control align-center" type="text" value='+response[i].name+' id="example-tel-input" readonly=""> </div> </td><td style="width: 130px;"> <div class="form-group"> <input class="form-control align-center" type="text" value='+response[i].relationship+' id="example-tel-input" readonly=""> </div> </td> <td style="width: 130px;"> <div class="form-group"> <input class="form-control align-center" type="text" value='+response[i].policy_no+' id="example-tel-input" readonly=""> </div> </td><td><ul class="d-flex justify-content-center"> <li style = "cursor: pointer;" class = "ecard_link_download"data-id="'+str+'"><a  class="text-secondary"> <img src="/public/assets/images/new-icons/pdf%20(1).png"></a> </li> </ul></td></tr>');
                     
                    }
                }
            }); 
  });
  
   $(document).on('click', '.ecard_link_download', function(){
   //debugger;
   var z = ($(this).attr("data-id"));
   var d = z.split(",");
  // ecard_link(d[0],d[1],d[2],d[3]);
   ecard_link(d[0],d[1],d[2],d[3],d[4]);
   
   
   });

});
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


