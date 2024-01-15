
    
           
 $(document).ready(function () {
     
    //show_chart(data);
    
     $("#from_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        maxDate: new Date(),
        minDate: "-100Y +1D"
    });
    	$("#to_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        maxDate: new Date(),
        minDate: "-100Y +1D"
    });
     $.ajax({
                url: "/get_all_policy_no",
                type: "POST",
                async: true,
                dataType: "json",
                success: function (response) {
                    
        
                   console.log(response);
                     console.log(response.constructor);
                     $('#policy_no').empty();
                     $('#policy_no').append('<option value=""> Select policy no</option>');
                    
                    $('#policy_no').append('<optgroup label="Active policies" id ="active_policies" class="text-center">');
                    $('#policy_no').append('<optgroup label="Inactive policies" id ="inactive_policies" class="text-center">');
                   
                        for (i = 0; i < response.length; i++) { 
                        var date = response[i].end_date.split("-");
                        var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
                        var current_date = new Date();
                        // console.log(response[i].end_date);
                        //  console.log(date);
                        //  console.log(current_date);
                        //  console.log(date > current_date);
                         
                        
                            if(date > current_date){
                         
                     $('#active_policies').append('<option value="' + response[i].policy_no + '">' + response[i].policy_no + '</option>');
                    
                            } 
                            else{
                            
                                 $('#inactive_policies').append('<option value="' + response[i].policy_no + '">' + response[i].policy_no + '</option>');
                            }
                        }
                }
                
            }); 
 
   
    
     $('#apply').on('click', function() {
             var from_date = $("#from_date").val();
             var policy_no = $("#policy_no").val();
             var to_date = $("#to_date").val();
             if(policy_no == ""){
                 swal("","please select policy no");
                 return false;
             }
             if(from_date == ""){
                 swal("","please select from date");
                 return false;
             }
              if(to_date == ""){
                 swal("","please select to date");
                 return false;
             }
        $.ajax({
                url: "/get_all_claims",
                type: "POST",
                async: true,
                data:{policy_no : $('#policy_no option:selected').val() ,employer:true, to_date:to_date, from_date:from_date},
                dataType: "json",
                success: function (response) {
                      var self = 0;
  
                }
            });
      
           
            });
 });
