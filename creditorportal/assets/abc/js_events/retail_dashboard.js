$( document ).ready(function() {
	
	/*google analytics code */
	debugger;
	if(window.location.pathname == '/retail_dashboard'){
		
		dataLayer.push({
'page': 'insurance/abhi-insurance/know-your-premium',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 1 | Abhi Insurance | Know Your Premium',
'label': 'Know Your Premium',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
		
	}
	
	ajaxindicatorstart('We are quickly gathering your information <br> to get you started');
		
	$.ajax({
				url: "/get_family_construct_retail",
				type: "POST",				
				dataType: "json",
				success: function (response) 
				{
					
				
					var edit_res = '';
					var split_data;
					
					if(typeof(response.edit_data) != "undefined" && response.edit_data !== null) {
						 if(response.edit_data!= '')
				 {
					 edit_res = response.edit_data;
					 split_data = edit_res.familyConstruct.split('+');
					 sum_insured = edit_res.policy_mem_sum_insured;
					 getpremium(family_construct,response.suminsured[0].sum_insured);	
					 $('#dec_family_check').prop('checked',true);
				 }
					}
				
					
						var arr = [];
						var child = [];
						var temp;
						var xyz;
						var str;
						var childs;
						var ad_name;
						$("#adults_no").append('');
						$("#child_no").append('');
						$("#sumInsured").append('');
						var adults = 1;
						temp = response.adult_child[0].fr_name;
						ad_name = adults+'A';
						
						
						var family_construct = ad_name;
						var obj = {};
						obj[ad_name] = temp;
						
						for(var i = 0; i < response.adult_child.length; i++)
						{
							
							 if(response.adult_child[0].max_adult >= 2 && response.adult_child[i].adult_child == 'A')
							 {
									if(response.adult_child[i].fr_id != 0)
									{
										
									
										adults = adults + 1;
										ad_name = adults+'A';
										xyz = temp+"+"+response.adult_child[i].fr_name;
										temp = xyz;
										obj[ad_name] = xyz;
										
										
										
									}						
								
							 }
							 if(response.adult_child[i].adult_child == 'C')
							 {
								 child.push(response.adult_child[i].fr_id);
							 }
							 
											
					   }
					  
					   var j = 1;
						var checked;
						
						   for(var name in obj) {
							    if(obj[name] == response.adult_child[0].fr_name)
								{
									checked = 'checked';
								}
								else
								{
									checked = '';
								}
								if(typeof(response.edit_data) != "undefined" && response.edit_data !== null) {
							   
							    if(typeof(split_data[0]) != "undefined" && split_data[0] !== null) {
								   
								   if(name == split_data[0])
								   {
									   checked = 'checked';
								   }
								   else
								   {
									   checked = '';
								   }
							   }
								}
							 
							   var id_name = name+"-"+obj[name];
							  

													
													str = '<div class="col-md-3 col-6"><div class="form-group"><div class="custom-control custom-checkbox custom-control-inline"> <input type="radio" onclick = changes(); class="custom-control-input" name ="adults" '+checked+' data-opt="" id="self_family_check_'+j+'" value="'+id_name+'"><label class="custom-control-label" for="self_family_check_'+j+'"> '+obj[name]+' </label> </div></div></div>';

													$("#adults_no").append(str);
													j++;

												}
			
						var check;			
							for(var k = 0; k <= child.length; k++)
							{
								var child_nos = k+'K';
								
									if(k == '0')
									{
										check = 'checked';
									}
									else
									{
										check = '';
									}
									if(typeof(response.edit_data) != "undefined" && response.edit_data !== null) {
								if(typeof(split_data[1]) != "undefined" && split_data[1] !== null) {
								   /*console.log(child_nos);*/
								
								   
								   if(child_nos == split_data[1])
								   {
									   
									   check = 'checked';
								   }
								   else
								   {
									   check = '';
								   }
							   }
									}
								  childs = '<div class="col-md-3 col-4"><div class="form-group"><div class="custom-control custom-checkbox custom-control-inline"> <input type="radio" onclick = changes(); name = "childs" '+check+' class="custom-control-input" id="child_family_check_'+j+'" value="'+child_nos+'"><label class="custom-control-label" for="child_family_check_'+j+'">'+k+'</label> </div></div></div>';
								  
								  $("#child_no").append(childs);
								j++;
								  
							}
				
							var sumInsured;
							
							var sums = [];
							var sort_sum;
							var checkboxs;
							var obj1 = response.suminsured;
							for(var name1 in obj1) {
								var an = obj1[name1]['sum_insured'];
								var sum_insured_word = obj1[name1]['sum_insured_word'];
								if(an == response.suminsured[0].sum_insured)
													{
														checkboxs = 'checked';
													}
													else
													{
														checkboxs = '';
													}
													
													
									if(typeof(sum_insured) != "undefined" && sum_insured !== null) {
								   /*console.log(child_nos);*/
								
								   
								   if(an == sum_insured)
								   {
									   
									   checkboxs = 'checked';
								   }
								   else
								   {
									   checkboxs = '';
								   }
							   }				
													
									sumInsured = '<div class="col-md-3 col-6"><div class="form-group"><div class="custom-control custom-checkbox custom-control-inline"> <input type="radio" '+checkboxs+' onclick = changes(); class="custom-control-input" name = "sum_insured" id="family_check_sum_'+j+'" value = "'+obj1[name1]['sum_insured']+'"><label class="custom-control-label" for="family_check_sum_'+j+'" value="'+an+'">'+sum_insured_word+'</label> </div></div></div>';
									$("#sumInsured").append(sumInsured);
									j++;
							}
						var family_constructs;
						var sum_insures;
						if(typeof(response.edit_data) != "undefined" && response.edit_data !== null) {
							if(response.edit_data != '')
							{
								family_constructs = edit_res.familyConstruct;
								sum_insures = edit_res.policy_mem_sum_insured;
						
								getpremium(family_constructs,response.suminsured[0].sum_insured);	
							}
						}
						else{
								
								family_constructs = family_construct;
								sum_insures = response.suminsured[0].sum_insured;
						}
					
					
					
					getpremium(family_constructs,sum_insures);	
							
						
						
						
				
							
							
					
						
}

});

});


function getpremium(family_construct,sum_insured)
{
	  $.post("/get_premium", { "family_construct":family_construct, "sum_insured": sum_insured }, function (e) {
		  /*console.log(e);*/
		  if(e == 111111){
			  swal({
                                title: "warning",
                                text: "Please select Propoer Data",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            });
			  
			  return false;
		  }
     $("#premium").html(e);
	 $("#premium_input").val(e);
	 ajaxindicatorstop();
    });
	
}

function changes()
{
	var adult_radios = $('input[name=adults]:checked').val(); 
	
	var adult_radioe = adult_radios.split("-");
	
	var adult_radio = adult_radioe[0];
	
	var child_radio = $('input[name=childs]:checked').val(); 
	
	var adult_child;
	if(child_radio == '0K')
	{
		adult_child = adult_radio;
	}
	else
	{
		adult_child = adult_radio+"+"+child_radio;
	}
	var sum_insured = $('input[name=sum_insured]:checked').val();
	
	var family_const = adult_child;
	
	getpremium(family_const,sum_insured);
	
	

}

function confirm()
{
	
	if($('#dec_family_check:checked').length <= 0)
	{
		
							$("#modal-frown").modal("show");
		return false;
	}
	var all_data = $("#know_premium").serialize();
	$.ajax({
				url: "/create_know_premium",
				type: "POST",
				data: all_data,
				dataType: "json",
				success: function (response) 
				{


					if(response.statusErr == 1)
					{
						
							
							
ajaxindicatorstart('We are quickly gathering your information <br> to get you started');


localStorage.clear();
location.replace("/retail_enrollment");
					}
					else
					{
							swal({
                                title: "warning",
                                text: response.message,
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            });
					}
				}
	});
	
	
	
}