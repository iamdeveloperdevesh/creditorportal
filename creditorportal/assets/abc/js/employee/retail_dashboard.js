//from url

function ajaxindicatorstart(text) {
    text = (typeof text !== "undefined") ? text : "We are quickly gathering your information to get you started";

    var res = "";

    if (($("body").find("#resultLoading").attr("id")) != "resultLoading") {
        res += "<div id='resultLoading' style='display: none'>";
        res += "<div id='resultcontent'>";
        res += "<div id='ajaxloader' class='txt'>";
        res += '<svg class="lds-curve-bars" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(2.72337)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(64.7343)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ffffff" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(150.07)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(239.433)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(320.34)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"></animateTransform></circle></g></svg>';
        res += "<br/>";
        res += "<span id='loadingMsg'></span>";
        res += "</div>";
        res += "</div>";
        res += "</div>";

        $("body").append(res);
    }

    $("#loadingMsg").html(text);

    $("#resultLoading").find("#resultcontent > #ajaxloader").css({
        "position": "absolute",
        "width": "500px",
        "height": "75px"
    });

    $("#resultLoading").css({
        "width": "100%",
        "height": "100%",
        "position": "fixed",
        "z-index": "10000000",
        "top": "0",
        "left": "0",
        "right": "0",
        "bottom": "0",
        "margin": "auto"
    });

    $("#resultLoading").find("#resultcontent").css({
        "background": "#ffffff",
        "opacity": "0.7",
        "width": "100%",
        "height": "100%",
        "text-align": "center",
        "vertical-align": "middle",
        "position": "fixed",
        "top": "0",
        "left": "0",
        "right": "0",
        "bottom": "0",
        "margin": "auto",
        "font-size": "16px",
        "z-index": "10",
        "color": "#000000"
    });

    $("#resultLoading").find(".txt").css({
        "position": "absolute",
        "top": "-25%",
        "bottom": "0",
        "left": "0",
        "right": "0",
        "margin": "auto"
    });

    $("#resultLoading").fadeIn(300);

    $("body").css("cursor", "wait");
}


function ajaxindicatorstop() {
    $("#resultLoading").fadeOut(300);

    $("body").css("cursor", "default");
}

var emp_id = $("#empIdHidden").val() || "";
var parent_id = $("#parentIdHidden").val();

$( document ).ready(function() {
	ajaxindicatorstart('We are quickly gathering your information to get you started');
	//get family_construct
if (emp_id && parent_id) {
	
$.ajax({
				url: "/get_family_construct_retail",
				type: "POST",
				data: {'parent_id':parent_id,'emp_id':emp_id},
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
					 getpremium(family_construct,response.suminsured[0].sum_insured,parent_id);	
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
					   //----------------- adult--------------------------------------------------
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
			//-------------------------child------------------------------------------------------				
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
								   console.log(child_nos);
								
								   
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
				//------------------------- Sum Insured----------------------------------------------------------
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
								   console.log(child_nos);
								
								   
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
								getpremium(family_construct,response.suminsured[0].sum_insured,parent_id);	
							}
						}
						else{
								
								family_constructs = family_construct;
								sum_insures = response.suminsured[0].sum_insured;
						}
					
					
					
					getpremium(family_constructs,sum_insures,parent_id);	
						
						
						
				
							
							
					
						
}

});
}
});


function getpremium(family_construct,sum_insured,parent_id)
{
	  $.post("/get_premium", { "family_construct":family_construct, "sum_insured": sum_insured, "parent_id": parent_id }, function (e) {
		  
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
	
	getpremium(family_const,sum_insured,parent_id);
	
	

}

function confirm()
{
	
	if($('#dec_family_check:checked').length <= 0)
	{
		// swal({
                                // title: "warning",
                                // text: "Sorry, you cannot proceed in absence of good health declaration.",
                                // type: "warning",
                                // showCancelButton: false,
                                // confirmButtonText: "Ok",
                                // closeOnConfirm: true,
								// className: "fa fa-frown-o",
								
                            // });
							$("#modal-frown").modal("show");
		return false;
	}
	var all_data = $("#know_premium").serialize()+'&parent_id='+parent_id+'&emp_id='+emp_id;
	$.ajax({
				url: "/create_know_premium",
				type: "POST",
				data: all_data,
				dataType: "json",
				success: function (response) 
				{


					if(response.statusErr == 1)
					{
						// swal({
                                // title: "success",
                                // text: "success",
                                // type: "success",
                                // showCancelButton: false,
                                // confirmButtonText: "Ok",
                                // closeOnConfirm: true
                            // });
							
							
ajaxindicatorstart('We are quickly gathering your information to get you started');
location.replace("/retail_enrollment/"+response.emp_id_encrypt+"/"+parent_id);
					}
					else
					{
							swal({
                                title: "warning",
                                text: "Please select Propoer Data",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            });
					}
				}
	});
	
	
	
}