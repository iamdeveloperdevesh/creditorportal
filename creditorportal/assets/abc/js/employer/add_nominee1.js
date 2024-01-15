// onchange of memeber id show data
   
   /*  $(document).on('change', '#nominee_relation', function(){
        selectChange = $(this).closest(".row");
		var emp_id = $("#emp_id").val();
               
            $.ajax({
                url: "/home/get_family_details_from_relationship_nominee_employer",
                type: "POST",
                data: {
                	 "emp_id": emp_id,
                    "relation_id": $(this).val()
                },
                async: true,
                dataType: "json",
                success: function (response) {
               $("#nominee_fname").val("");
                $("#nominee_lname").val("");
               $("#gender").val("");
               $("input[type='text'][name='nominee_dob']").val(""); 
			   console.log(response);
                if(response.length != 0){
                    if(response[0].fr_id == "2" || response[0].fr_id == "3"){
                       $("#body_modal").html("");
                        for($i = 0; $i < response.length; $i++){
                            $("#body_modal").append('<input type="radio" name ="radio_option" value= '+response[$i]['family_id']+'> '+response[$i].family_firstname+'<br>');
                        }
                       $("#myModal").modal();
                    }
					else if(response[0].family_id == "0" ){
						$("#nominee_fname").val(response[0].emp_firstname);
                        $("#nominee_lname").val(response[0].emp_lastname);
                        $("#family_ids").val(response[0].family_id);
                       $("input[type='text'][name='nominee_dob']").val(response[0].bdate);
					}
                    else{
                      
                        $("#nominee_fname").val(response[0].family_firstname);
                        $("#nominee_lname").val(response[0].family_lastname);
                       $("#family_ids").val(response[0].family_id);
                       $("input[type='text'][name='nominee_dob']").val(response[0].family_dob);
                    }
                }
            }
        });
         }); */
		 
		   $(document).on('click', '#modal-submit', function(){
              if($("input[name='radio_option']:checked").val() == undefined){
                  swal("","please select at least one member");
                  return false;
              }  
           $.ajax({
                url: "/get_individual_family_details",
                type: "POST",
                data: {
                    "family_id": $("input[name='radio_option']:checked").val()
                },
                async: false,
                dataType: "json",
                success: function (response) {
                console.log(response);
                console.log(response.constructor);
          if(response.length != 0){
                        selectChange.find("#nominee_fname").val(response.family_firstname);
                        selectChange.find("#nominee_lname").val(response.family_lastname);
                        selectChange.find("input[type='text'][name='nominee_dob']").val(response.family_dob);
                        selectChange.find("#family_ids").val(response.family_id);
                       
                }
                 $("#myModal").modal("hide");
            }
        });   
         });
   