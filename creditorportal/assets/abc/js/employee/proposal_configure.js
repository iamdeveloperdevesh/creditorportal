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
	
	$.ajax({
        url: "/proposal_config_field",
        type: "POST",
   
        success: function (response) {
	
	debugger;
	
			var abc
			es = JSON.parse(response);
      
		   var count = Object.keys(es).length;
		   var i = 0;
		   for(var k = 0; k < count; k++)
		   {
var strs = '<div class="col-md-12 mt-2"><div class="card"><div class="card-body card-style" style="background: #fff;"><h4 class="header-title title  col-md-12 header-tl-xd"> <img class="img-view" width="30" src="/public/assets/images/new-icons/cus-xd-detail.png"><span class="ml-2">'+Object.keys(es)[k]+'</span></h4><div id = "'+Object.keys(es)[k]+'_1"></div></div></div></div>';

$(".all_card").append(strs);

if(Object.keys(es)[k].length>0)
                    {
 
      var thead = '<div class="row"><div class="col-md-6"><table class="table table-bordered text-center"><thead class="text-uppercase col-da80"><tr><th scope="col" style="">Fields</th><th scope="col" style="font-weight:600 !important;">To Show</th></tr></thead><tbody id="'+Object.keys(es)[k]+'_tb"> </tbody> </table></div><div class="col-md-6"> <table class="table table-bordered text-center"><thead class="text-uppercase col-da80"><tr><th scope="col" style="">Fields</th><th scope="col" style="font-weight:600 !important;">To Show</th> </tr></thead> <tbody id="'+Object.keys(es)[k]+'_tb1"></tbody><input type="hidden" name="proposerdetails[type]" id="proposer_hidden" value=""/></table></div></div>';
	  
	  $("#"+Object.keys(es)[k]+"_1").append(thead);
						
		var get_data_all = Object.keys(es)[k];		
	
		
			
				var str;
				var get_field_number;
				var name_toggle_field ;
				var on_fields;
				var str_split_field ;
					for(var i = 0; i < Math.round(es[get_data_all].length/2);++i)
					{
						 get_field_number = es[get_data_all][i].p_config_field;
						 name_toggle_field = get_data_all+'['+get_field_number+']';
						 on_fields = 'onclick = toggleshow(this)';
						 str_split_field = es[get_data_all][i].fields.split(' ').join('_');
					
						var str = "<tr><td>"+es[get_data_all][i].fields+"</td><td><center><div class='custom-control'><label class='switch'><input type='checkbox' class='switch-input togglesw'" +on_fields+ " name='"+name_toggle_field+"' id='' data-attr = "+str_split_field+" > <span class='switch-label' data-on='On' data-off='Off'></span><span class='switch-handle'></span></label></div></center><div class='togglediv' style='padding:2px;'><div id='"+str_split_field+"' class='accordion' style = 'display:none;'><div class='card mb-0'><div class='card-header crd-bg collapsed' data-toggle='collapse' href='#collapseOne'><a class='card-title'> Item 1 </a></div><div id='collapseOne' class='card-body collapse' data-parent='#accordion' style='padding:2px;'><table width='100%;'><tr><div class='col-md-6'><td>MANDATORY</td><td width='50%;'><div class='custom-control cust-left'> <label class='ss'>One<input type='radio' checked='checked' name='radio'><span class='checkmark'></span></label><label class='ss marleft'>Two<input type='radio' name='radio'><span class='checkmark'></span></label></div></td></div><div class='col-md-6'><td><input type='text' class='form-control' placeholder='Enter Priority'></td></div></tr><tr> <div class='col-md-6'><td>Text</td></div><div class='col-md-6'><td colspan='3'><div class='col-md-7 colmleft' style=''><select class='form-control'><option name='' value=''>abc</option><option name='' value=''>abc</option><option name='' value=''>abc</option></select></div><div class='col-md-5 inputfild'><input type='text' class='form-control' value='' name=''></div></td></div> </div></tr> <tr><td colspan='3'><div class='col-md-3 inputlbl'><i class='fa fa-check-circle-o lblicon'></i>label</div><div><span>abc</span></div></td> </tr></table></div></div></div></div></div></td></tr>";
						// console.log(str);
						
						 $("#"+Object.keys(es)[k]+"_tb").append(str);
						
					}
					
					
					
					for(var i = Math.round(es[get_data_all].length/2); i < es[get_data_all].length;++i)
					{
						  get_field_number = es[get_data_all][i].p_config_field;
						 name_toggle_field = get_data_all+'['+get_field_number+']';
						 on_fields = 'onclick = toggleshow(this)';
						 str_split_field = es[get_data_all][i].fields.split(' ').join('_');
						var str = "<tr><td>"+es[get_data_all][i].fields+"</td><td><center><div class='custom-control'><label class='switch'><input type='checkbox' class='switch-input togglesw'" +on_fields+ " name='"+name_toggle_field+"' id='' data-attr = "+str_split_field+" > <span class='switch-label' data-on='On' data-off='Off'></span><span class='switch-handle'></span></label></div></center><div class='togglediv' style='padding:2px;'><div id='"+str_split_field+"' class='accordion' style = 'display:none;'><div class='card mb-0'><div class='card-header crd-bg collapsed' data-toggle='collapse' href='#collapseOne'><a class='card-title'> Item 1 </a></div><div id='collapseOne' class='card-body collapse' data-parent='#accordion' style='padding:2px;'><table width='100%;'><tr><div class='col-md-6'><td>MANDATORY</td><td width='50%;'><div class='custom-control cust-left'> <label class='ss'>One<input type='radio' checked='checked' name='radio'><span class='checkmark'></span></label><label class='ss marleft'>Two<input type='radio' name='radio'><span class='checkmark'></span></label></div></td></div><div class='col-md-6'><td><input type='text' class='form-control' placeholder='Enter Priority'></td></div></tr><tr> <div class='col-md-6'><td>Text</td></div><div class='col-md-6'><td colspan='3'><div class='col-md-7 colmleft' style=''><select class='form-control'><option name='' value=''>abc</option><option name='' value=''>abc</option><option name='' value=''>abc</option></select></div><div class='col-md-5 inputfild'><input type='text' class='form-control' value='' name=''></div></td></div> </div></tr> <tr><td colspan='3'><div class='col-md-3 inputlbl'><i class='fa fa-check-circle-o lblicon'></i>label</div><div><span>abc</span></div></td> </tr></table></div></div></div></div></div></td></tr>";
						console.log(str);
						
						 //$("#"+Object.keys(es)[k]+"_tb").append(str);
					
						
						 $("#"+Object.keys(es)[k]+"_tb1").append(str);
						
					}
					

                
                    } 
					}
		   }
		   
		   
		   
		   
		   
		   
		   
		   
		   
	
					
					
		   
        
    });
	
	  function confirm(){
	 var policy_id = $('#masPolicy').val();	 
      var data = new FormData(document.getElementById("proposer_det"));
	  data.append('policy_id', policy_id);
	  $.ajax({
        type: "POST",
        url: "/employee/save_proposer_config_data",
         data: data,
        processData: false,
        contentType: false,
        success: function (result) {
          // debugger;
          if (result.status == true) {
		
        }
      }
    });
	}

$(document).on('click','#Bank_details_44',function(){  

 if ($(this).is(':checked')) {
        switchStatus = $(this).is(':checked');
	$("#bankId_44").show();
	
	$("#bankId_44").html('<div class="col-md-6"><div class="custom-control custom-radio" style=""><input type="radio" onchange="ifscRadiofn(this);" name="Bankdetails[44][radio]" id="Default" class="custom-control-input" value="Default" checked><label class="custom-control-label" for="Default"> Default </label></div></div><div class="col-md-6"><div class="custom-control custom-radio" style=""><input type="radio" onchange="ifscRadiofn(this);" name="Bankdetails[44][radio]" class="custom-control-input radios_out " value="Custom" id="customId"><label class="custom-control-label" for="customId"> Custom </label></div></div>'); 
	 
    }
	else{
		$("#bankId_44").hide();
		$("#ifsc_file_44").hide();
	}

 });
 function ifscRadiofn(e)
 {
	 var input = $(e).val();
if(input == 'Custom')
{	
$("#ifsc_file_44").show();
	$("#ifsc_file_44").html('<input type="file" name="fileToUpload_44" class="cust_decl_forms" style="padding-left: 20px; padding-top: 0px;">');
}
else
{
	$("#ifsc_file_44").hide();
}
 }



$(document).on('click','#Bank_details_45',function(){  

 if ($(this).is(':checked')) {
        switchStatus = $(this).is(':checked');
	$("#bankId_45").show();
	
	$("#bankId_45").html('<div class="col-md-6"><div class="custom-control custom-radio" style=""><input type="radio" onchange="BankNameRadiofn(this);"  name="Bankdetails[45][radio]" id="Defaults" class="custom-control-input" value="Default" ><label class="custom-control-label" for="Defaults"> Default </label></div></div><div class="col-md-6"><div class="custom-control custom-radio" style=""><input type="radio" onchange="BankNameRadiofn(this);" name="Bankdetails[45][radio]" class="custom-control-input radios_out " value="Custom" id="customIds"><label class="custom-control-label" for="customIds"> Custom </label></div></div>'); 
	 
    }
	else{
		$("#bankId_45").hide();
		$("#bankNames").hide();
	}

 });
 
 function BankNameRadiofn(e)
 {
	 
	 var str;
	   $("#ifsc_file_45").empty();
var input = $(e).val();
if(input == 'Default')
{	


 str ='<select name ="Bankdetails[45][bankName]" id="bankNames" class="bankName form-control"><option value="">select Bank</option>';
$.ajax({
        type: "POST",
        url: "/getBankName",
      

        success: function (response) {
          	es = JSON.parse(response);
	
		   for (i = 0; i < es.length; i++) {
        //console.log(response[i]['bank_name']);
           str +='<option value="'+es[i]['bank_name']+'">'+es[i]['bank_name']+'</option>';


            }
			
       str +='</select>';
	   $("#ifsc_file_45").show();
	   $("#ifsc_file_45").append(str);
		
        }
      
    });
	
}
else
{
	$("#ifsc_file_45").hide();
}
 }


$(document).on('click','#Upload_50',function(){  

 if ($(this).is(':checked')) {
        switchStatus = $(this).is(':checked');
	$("#uploadId_50").show();
	
	$("#uploadId_50").html('<div class="col-md-6"><div class="custom-control custom-radio" style=""><input type="radio" onchange="UploadRadiofn(this);" name="Uploaddetails[50][radio]" id="Default_upload" class="custom-control-input" value="Default" ><label class="custom-control-label" for="Default_upload"> Default Master</label></div></div><div class="col-md-6"><div class="custom-control custom-radio" style=""><input type="radio" name="Uploaddetails[50][radio]" onchange="UploadRadiofn(this);" class="custom-control-input radios_out " value="Custom" id="upload_custom"><label class="custom-control-label" for="upload_custom"> Custom Master </label></div></div>'); 
	 
    }
	else{
		$("#uploadId_50").hide();
		$("#upload_file_50").hide();
	}

 });
 
  function UploadRadiofn(e)
 {
var input = $(e).val();
if(input == 'Custom')
{	
	$("#upload_file_50").show();
	$("#upload_file_50").html('<input type="file" name="fileToUpload_50" class="cust_decl_forms" style="padding-left: 20px; padding-top: 0px;">');
}
else
{
	$("#upload_file_50").hide();
}
 }


$(document).ready(function() {

    var max_fields3      = 10; //maximum input boxes allowed
    var wrapper3        = $(".input_fields_wrap_upolad"); //Fields wrapper
    var add_button3      = $(".add_field_button_upload"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button3).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields3){ //max input box allowed
            x++; //text box increment
            var abc='upload_'+x;
			var abcx='uploadMandatory_'+x;
            $(wrapper3).append('<div class="row mt-4"><div class="col-md-7"><textarea name="upload_docs['+abc+'][input]" class="form-control" placeholder="Add Label" /> <div class="col-md-4"> <div class="row"><div class="col-md-6"><div class="custom-control"> <label class="switch"><input type="checkbox" class="switch-input" name="upload_docs['+abc+'][mandatory]" id="" > <span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span></label></div></div></div><a href="#" class="remove_field3">Remove</a> </div> <div class="col-md-4"><div class="input_fields_wrap_upolad"></div><br/></div></div>'); //add input box
        }
    });
    
    $(wrapper3).on("click",".remove_field3", function(e){ //user click on remove text

        e.preventDefault(); $(this).closest('div').parent('div').remove(); 

       
        x--;

    
      
    })
});

$(document).on('click','.masPolicy_id',function(){  
	 var policy_id = $('.masPolicy_id').val();	
$.ajax({
        type: "POST",
        url: "/edit_view_configure",
		data:{"policy_id":policy_id},

        success: function (response) {
        
		
        }
      
    });
});

$('[name="collapseGroup"]').on('change', function(){  
    if($(this).val()  === "yes"){
      $('#collapseOne').collapse('show')
    }else{
       $('#collapseOne').collapse('hide')
    }
   });
   
   
    function toggleshow(e)
	{
			 	
		var dat_attr = $(e).attr("data-attr");

		 if ($(e).is(':checked')) 
		 {
			
			$("#"+dat_attr).show();
			
		 }
		 else
		 {
			$("#"+dat_attr).hide();
		 }
      
    }
