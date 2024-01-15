$(document).ready(function(){
	
var relation_id = localStorage.getItem("relations");
var policy_subtype_no = localStorage.getItem("policy_subtype_no");
var type = localStorage.getItem("type");
var parental_cover = localStorage.getItem("parental_cover");
var final_amount = localStorage.getItem("final_amount");
var final_amount = localStorage.getItem("final_amount");
$(".parental_cover").val(parental_cover);
$(".policy_subtype_no").val(policy_subtype_no);
$(".types").val(type);
$(".final_amount").val(final_amount);
$(document).on('focus', '.family_date_birth',  function(e){
    $(this).removeClass("hasDatepicker");
		
		 $('.family_date_birth').datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-150Y:-40Y",
        maxDate: "-40Y",
        minDate: "-150Y:-40Y"
    });
});
// after load 
$.ajax({
	 url: "/employee/get_policy_with_subtype",
     type: "POST",
     async: false,
     dataType: "json",
	 data:{"policy_subtype_no":policy_subtype_no},
     success: function (response) {
	$('#policy_numbers').empty();
	 console.log(response.policy);
	 var policy = response.policy;
	  $('#policy_numbers').append('<option value="' + policy.policy_detail_id + '">' + policy.policy_sub_type_name + '</option>');
	 $(".policy_id").val( policy.policy_detail_id);
	}         
});

// 


	var relation_no = localStorage.getItem("relations").toString().split(",");
 var arra =[];
 var clone = $( ".form_row_data:first").clone();
if(relation_no.length > 0) {
	$( ".form_row_data:first").remove();
}
var j = 0;
 for($i=0; $i<relation_no.length; $i++){
	 console.log(relation_no[$i]);
	 //arra.push(relation_no[$i]);
	 $.post("/employee/get_policy_member_parent_details",{
		 "relation_no":relation_no[$i]
	},function (response) {
		var amt = localStorage.getItem("final_amount");
		e = JSON.parse(response);
		 console.log("asrtryrt");
		 console.log(response);
		 console.log("asrt");
		 console.log(e.response);
		 if(!e.response){
			console.log("append");
			$(".form_row").append(clone);
			clone = $( ".form_row_data:first").clone();
			document.getElementsByName("family_members_id")[j].value = e['familyRelation']['fr_id'];
			document.getElementsByName("family_members_name")[j].value = e['familyRelation']['fr_name'];
            
			j++;
		 }
		else {
			
			$(".final_amount").val((Number(amt) - Number(e.response.policy_mem_sum_premium)));
		}
	
	});         
}


// after load 
 /* $.ajax({
	 url: "/employee/getfamily_relation",
     type: "POST",
     async: false,
     dataType: "json",
	 data:{"relation_no":relation_no},
     success: function (response) {
	$('#family_members_id').empty();
	 var famil_relation = response.famil_relation;
	  var family_members_id = document.getElementsByName("family_members_id");
	  var family_members_name = document.getElementsByName("family_members_name");
	  for(i =0; i < family_members_id.length; ++i) {
		  family_members_id[i].value = famil_relation[i].fr_id;
		  family_members_name[i].value = famil_relation[i].fr_name;
	  }
	}        
}); */ 

$('#cancl').on("click", function() {
	 $('#policy_member_form_data')[0].reset();
});
 $('body').on("keyup",".first_name",function(e) {
var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z]/g, function(str) { return ''; } ) );
        }return;
   }); 
   $('body').on("keyup",".last_name", function(e) {
var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z]/g, function(str) { return ''; } ) );
        }return;
   });

$('#submit').on("click", function() {
// var dataString = $("#policy_member_form_data").serialize(); 
// $.ajax({
 //            type: "POST",
 //            url: "/employee/get_family_detail_parent",
 //            //dataType: "json",
 //             data:dataString,
 //            success: function (data) {
 //               alert(data);

 //            },
 //            error: function () {
 //             alert('Error');
 //            }
 //        });
// return false;

var count = 0;
var policy_id = document.getElementById("policy_numbers").value;
var family_members_id = document.getElementsByName("family_members_id");
var first_name = document.getElementsByName("first_name");
var last_name = document.getElementsByName("last_name");
var family_date_birth = document.getElementsByName("family_date_birth");
var family_gender = document.getElementsByName("family_gender");
var policy_subtype_no = document.getElementsByName("policy_subtype_no");
var parental_cover = document.getElementsByName("parental_cover");
var types = document.getElementsByName("types");
var final_amount = document.getElementsByName("final_amount");
var family_members_name = document.getElementsByName("family_members_name");
var policy_idArr = [];
var family_members_idArr = [];
var family_members_nameArr = [];
var first_nameArr = [];
var last_nameArr = [];
var family_date_birthArr = [];
var family_genderArr = [];
var policy_subtype_noArr = [];
var parental_coverArr = [];
var typesArr = [];
var final_amountArr = [];
     //policy_idArr.push(policy_id.value); 
for(i = 0; i < family_members_id.length; ++i){

family_members_idArr.push(family_members_id[i].value);
family_members_nameArr.push(family_members_name[i].value);
first_nameArr.push(first_name[i].value);
last_nameArr.push(last_name[i].value);
family_date_birthArr.push(family_date_birth[i].value);
family_genderArr.push(family_gender[i].value);
policy_subtype_noArr.push(policy_subtype_no[i].value);
parental_coverArr.push(parental_cover[i].value);
typesArr.push(types[i].value);
final_amountArr.push(final_amount[i].value);

if(family_members_id[i].value.trim().length > 0) {
family_members_id[i].style = "border-color:black";
}
else {
family_members_id[i].style = "border-color:red";
++count;
}
if(first_name[i].value.trim().length > 0) {
first_name[i].style = "border-color:black";
}
else {
first_name[i].style = "border-color:red";
++count;
}
if(last_name[i].value.trim().length > 0) {
last_name[i].style = "border-color:black";
}
else {
last_name[i].style = "border-color:red";
++count;
}
if(family_date_birth[i].value.trim().length > 0) {
family_date_birth[i].style = "border-color:black";
}
else {
family_date_birth[i].style = "border-color:red";
++count;
}
if(family_gender[i].value.trim().length > 0) {
family_gender[i].style = "border-color:black";
}
else {
family_gender[i].style = "border-color:red";
++count;
}
}
if(count > 0) {
alert("Plase check for error");
return;
}


$.post("/employee/get_family_detail_parent", {
"policy_id":policy_id,
"family_members_idArr":JSON.stringify(family_members_idArr),
"family_members_nameArr":JSON.stringify(family_members_nameArr),
"first_nameArr":JSON.stringify(first_nameArr),
"last_nameArr":JSON.stringify(last_nameArr),
"family_date_birthArr":JSON.stringify(family_date_birthArr),
"family_genderArr":JSON.stringify(family_genderArr),
"policy_subtype_noArr":JSON.stringify(policy_subtype_noArr),
"parental_coverArr":JSON.stringify(parental_coverArr),
"typesArr":JSON.stringify(typesArr),
"final_amountArr":JSON.stringify(final_amountArr),
}, function(e) {
e = JSON.parse(e);
          console.log(e);
//var data = JSON.parse(e.family_count);
          var data = e.family_count;
if(data.msg==false ){
alert("Sorry You can not add more than parent");
location.reload();
}
else if(data[0]){
alert("Submitted Successfully");
  window.location.href = "/flexi_benefit";
}
else if(data.msg==true){
alert("Submitted Successfully");
  window.location.href = "/flexi_benefit";
}
else {
      $.each(e.messages, function(key, value){
       var element = $('#' + key);
       element.closest('div.e').find('.error').remove();
       element.after(value);
       });
   }
});
});
});

/* function test() {

	var relation_no = localStorage.getItem("relations").toString().split(",");
 var arra =[];
 var clone = $( ".form_row_data:first").clone();
if(relation_no.length > 0) {
	$( ".form_row_data:first").remove();
}
var j = 0;
 for($i=0; $i<relation_no.length; $i++){
	 console.log(relation_no[$i]);
	 //arra.push(relation_no[$i]);
	 $.post("/employee/get_policy_member_parent_details",{
		 "relation_no":relation_no[$i]
	},function (response) {
	
		e = JSON.parse(response);
		 console.log("asrtryrt");
		 console.log(response);
		 console.log("asrt");
		 console.log(e.response);
		 if(!e.response){
			  console.log("append");
			$(".form_row").append(clone);
			clone = $( ".form_row_data:first").clone();
			document.getElementsByName("family_members_id")[j].value = e['familyRelation']['fr_id'];
			document.getElementsByName("family_members_name")[j].value = e['familyRelation']['fr_name'];
			j++;
		 }
		 else {
			 
		 }
	
	});         
}

} */