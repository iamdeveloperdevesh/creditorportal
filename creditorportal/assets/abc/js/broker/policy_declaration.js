    $.ajax({
        url: "/get_all_policy_numbers1",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {

    
            $("#masPolicy").empty();
            $("#masPolicy").append("<option value = ''>Select Policy Number</option>");
            for (i = 0; i < response.length; i++) {
        
                $("#masPolicy").append("<option data-customer = '" + response[i]['customer_search_status'] + "'  data-id = '" + response[i]['policy_subtype_id'] + "' value =" + response[i]['policy_parent_id'] + ">" + response[i]['product_name'] + "</option>");


            }
        }
    });
// $(document).ready(function() {

    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            var abc='questioner_'+x;
            $(wrapper).append('<div class="row mt-4"><div class="col-md-6"><textarea name="declare['+abc+']" class="form-control" placeholder="questionnaire" /><a href="#" class="remove_field">Remove</a> </div> <div class="col-md-4"><div class="input_fields_wraps"></div><br/><button class="add_another_button lbl-btn" id='+abc+' style="border: 2px solid #DA8086;background: #fff;color: #da8085;padding: 5px 10px;border-radius: 50px;font-size: 12px;font-weight: 600;">Add Label </button></div></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text

        e.preventDefault(); $(this).closest('div').parent('div').remove(); 

       
        x--;

    
      
    })

$(document).ready(function() {

    var max_fields_prop      = 10; //maximum input boxes allowed
    var wrapper_prop         = $(".input_fields_wrap_prop"); //Fields wrapper
    var add_button_prop      = $(".add_field_button_proposal"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button_prop).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields_prop){ //max input box allowed
            x++; //text box increment
            var abc='questioner_'+x;
            $(wrapper_prop).append('<div class="row mt-4"><div class="col-md-6"><textarea name="proposal_declare['+abc+']" class="form-control" placeholder="questionnaire" /><a href="#" class="remove_field1">Remove</a> </div> <div class="col-md-4"><div class="input_fields_wrap_prop"></div><br/><button class="add_another_button1 lbl-btn" id='+abc+' style="border: 2px solid #DA8086;background: #fff;color: #da8085;padding: 5px 10px;border-radius: 50px;font-size: 12px;font-weight: 600;display:none">Add Label </button></div></div>'); //add input box
        }
    });
    
    $(wrapper_prop).on("click",".remove_field1", function(e){ //user click on remove text

        e.preventDefault(); $(this).closest('div').parent('div').remove(); 

       
        x--;

    
      
    })
});
$(document).on("click",".add_another_button",function(e) {
 
    e.preventDefault();
    var id= this.id;
    var wrapper1 = $("#"+id).siblings("div .input_fields_wraps");
    var max_fields1      = 10; //maximum input boxes allowed
    var x = 1; 

       
        if(x < max_fields1)
        { 
            x++;

            $(wrapper1).append('<div><br><input type="text" name="label['+id+'][]" class="form-control" placeholder="label" /></div>'); //add input box
        }

    });

/*    $(document).on("click",".remove_field_label",function(e) {
     e.preventDefault();
  $(this).closest('div .input_fields_wraps').remove();
  x--;
});
    /*$(wrapper1).on("click",".remove_field_label", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })*/


$(document).ready(function() {

    var max_fieldsi      = 10; //maximum input boxes allowed
    var wrapperi         = $(".input_fields_wrap_member"); //Fields wrapper
    var add_buttoni      = $(".add_field_button_member"); //Add button ID

    var x = 1; //initlal text box count
	
	
	get_first_member(x);

    $(add_buttoni).click(function(e){ //on add input button click
        e.preventDefault();

		
        if(x < max_fieldsi){ //max input box allowed
	
            x++; //text box increment
	
            var abc='questioner_'+x;
			var abci='subtype_data_'+x;

			
            $(wrapperi).append('<div class="row mt-4"><div class="col-md-6"><textarea name="declare_member['+abc+'][content]" class="form-control" placeholder="questionnaire" /><a href="#" class="remove_field"><i class="ti-trash"></i></a> </div> <div class="col-md-4"><div class="input_fields_wraps"></div><br/><div id="'+abci+'"></div></div>'); //add input box
				get_first_member(x);
			
        }
    });
    
    $(wrapperi).on("click",".remove_field", function(e){ //user click on remove text

        e.preventDefault(); $(this).closest('div').parent('div').remove(); 

       
        x--;

    
      
    })
});

$("#formDelc").validate({
        rules: {
            policyNo: {
                required: true
            },
            
           
        },
        messages: {
            policyNo: "Please specify Policy No.",
           
            
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
                url: "/broker/create_policy_declaration",
                type: "POST",
                data: $("#formDelc").serialize(),
                //async: true,
                success: function(data) {
    
                  if(data == 'true')
                  {
                        swal({
                            title: "success",
                            text: "Added Successfully",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                          },
              function(){
                location.reload();
              });
       			   }
       		
       			   }
            });
        }
    });
function get_first_member(x)
{
   var abc='questioner_'+x;
	 var radio_name='sub_type_data_'+x;
	    $.ajax({
                url: "/broker/policy_subtype_declare",
                type: "POST",
                dataType:"json",
                
                success: function(response) {

					    for (i = 0; i < response.length; i++) {

             $("#subtype_data_"+x).append('<input type="radio" name="declare_member['+abc+']['+radio_name+']" value='+response[i]['declare_subtype_id']+'>' + response[i]['sub_type_name']);

            }
			
	
		
		
       			   }
            });
}