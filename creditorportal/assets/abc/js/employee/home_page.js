// function ecard_link(member_name,policy_no,status,tpa_id){
         // if(status == 1){
         // $.ajax({
                // url: "/get_ecard",
                // type: "POST",
                // data: {
                    // "policy_no": policy_no, "member_id": member_name,"tpa_id":tpa_id
                // },
                // async: false,
               //dataType: "json",
                // success: function (response) {
					//return false;
				// if(response!=="true"){
               //var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData[0].EcardLink.toString();
                // window.location.replace(response);
            // }
				// }
        // });  
         // }
         // else{
             // swal("","Health E-card will be available post enrolment window");
         // }
     // }
	 
	 
	
	 function ecard_link(member_name,member_id,policy_no,status,tpa_id){
    //alert(); return false;
         if(status == 1){
         $.ajax({
                url: "/get_ecard",
                type: "POST",
                data: {
                    "policy_no": policy_no, "member_name": member_name,"tpa_id":tpa_id,"member_id": member_id
                },
                async: false,
               // dataType: "json",
                success: function (response) {
                    debugger;
                    
if(response){
                               var response =  JSON.parse(response);     
if(response.tpa_id == '2' || response.tpa_id == '4' || response.tpa_id == '5'){
               //var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData[0].EcardLink.toString();
                window.location.replace(response.response);
            }else{
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

	 
// for redirect to other pageX
 function pageRedirect() {
      window.location = "/flexi_benefit";
    }  

	 
var selectChange = "";
var eValue = "";
function getgender(e){
eValue = e.value;
var family_gender = $(e).closest(".row").find("#family_gender");
if(eValue==2 || eValue == 4 || eValue == 7){
    family_gender.empty();
    family_gender.append('<option value="Male">Male</option>');
    }
    if(eValue==3 || eValue == 5 || eValue == 6){
     family_gender.empty();
     family_gender.append('<option value="Female">Female</option>');
    }
     if(eValue==1){
      family_gender.empty();
      family_gender.append('<option value="Male">Male</option>');
      family_gender.append('<option value="Female">Female</option>');
      family_gender.append('<option value="Transgender">Transgender</option>');
     }
}
function getMemberRelation(e){
			var row = $(e).closest(".row");
			var textElement = $(row).find("input");
			$.ajax({
			 url: "/employee/get_all_family_relation",
			 type: "POST",
			 data: {  
					"policy_id": e.value,
					},
				 async: false,
				 dataType: "json",
				 success: function (response) {
			if(response==''){
			$('#family_members_id').empty();
			$('#policy_id').empty();
			 }
			else {
			 var policy_id = response[0].policy_id;
			 $('#policy_id').val(policy_id);
			 $('#family_members_id').empty();
			 $('#family_members_id').append('<option value=""> Select</option>');
			for (i = 0; i < response.length; i++) { 
			 $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
			}
			}
			}			
			});
			}
    $(document).ready(function(){
	 $.ajax({
     url: "/get_all_policy_no",
     type: "POST",
     async: false,
     dataType: "json",
     success: function (response) {
     $('#policy_numbers').empty();
     $('#policy_numbers').append('<option value=""> Select Policy Type</option>');
         for (i = 0; i < response.length; i++) { 
      if(response[i].policy_sub_type_id==1){
      $('#policy_numbers').append('<option   data-id = '+response[i].policy_no +' selected value="' + response[i].policy_detail_id + '">' + response[i].policy_no + '</option>');
     }
   }
}             
}); 
	

	 var son_age = 0;
    $.ajax({
            url: "/get_smallest_date",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response) {
              son_age = response.date.split("-");
              son_age = new Date(Number(son_age[2]), Number(son_age[1])-1, Number(son_age[0]))
              son_age.setDate(son_age.getDate()+1)
              // console.log(son_age);
           }
           });
		$("#policy_numbers").trigger('change');
		
        $(document).on('click', '#modal-submit', function(){
              if($("input[name='radio_option']:checked").val() == undefined){
                  swal("please select at least one member");
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
                // console.log(response);
                // console.log(response.constructor);
          if(response.length != 0){
                        selectChange.find("#first_name").val(response.family_firstname);
                        selectChange.find("#last_name").val(response.family_lastname);
                        selectChange.find("input[type='text'][name='family_date_birth']").val(response.family_dob);
                        selectChange.find("#family_id").val(response.family_id);
                         var today = new Date();
                          var dob = new Date(response.family_dob);
                          var age_distance = (today.getFullYear() - dob.getFullYear());
                          if (age_distance >= 25) 
                          {
                             $('div .disability').removeAttr('style','display:none');
                          }   
                }
            $("#myModal").modal("hide");
            }
        });   
         });
     $(document).on('click', '#add_new', function(){
       selectChange.find("#first_name").val("");
       selectChange.find("#last_name").val("");
       selectChange.find("#family_id").val("");
       selectChange.find("input[type='text'][name='family_date_birth']").val("");
             $("#myModal").modal("hide");
         });
     
    $(document).on('change', '#family_members_id', function(){
        selectChange = $(this).closest(".row");
		var mem_id = [];
		getgender(this);
            $.ajax({
                url: "/home/get_family_details_from_relationship",
                type: "POST",
                data: {
                    "relation_id": $(this).val()
                },
                async: false,
                dataType: "json",
                success: function (response) {
                selectChange.find("#first_name").val("");
                selectChange.find("#last_name").val("");
                selectChange.find("#family_id").val("");
                selectChange.find("input[type='text'][name='family_date_birth']").val("");
               
				
				var family_detail = response.family_data;
                if(family_detail.length != 0){
                    if(family_detail[0].fr_id == "2" || family_detail[0].fr_id == "3"){
                       $("#body_modal").html("");
                        for($i = 0; $i < family_detail.length; $i++){
                            $("#body_modal").append('<input type="radio" name ="radio_option" value= '+family_detail[$i]['family_id']+'> '+family_detail[$i].family_firstname+'<br>');
                        }
                       $("#myModal").modal();
                    }
                    else{
                      
                        selectChange.find("#first_name").val(family_detail[0].family_firstname);
                        selectChange.find("#last_name").val(family_detail[0].family_lastname);
                        selectChange.find("#family_id").val(family_detail[0].family_id);
                        selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[0].family_dob);
                        selectChange.find("input[name='marriage_date']").val(family_detail[0].marriage_date);

                    }
                }
            }
        });
         });
// get date of spouse for set kids DOB
 $.ajax({
 url: "/employee/get_all_dob",
     type: "POST",
     async: false,
     dataType: "json",
     success: function (response) {
     console.log(response);
     // console.log(response.famil_date);
	 var res = response.famil_date;
	 selectChange = $(this).closest(".row");
	
	 if(res.length!=0){
		
	 $("input[name='s_date']").val(res.family_dob);
	 }
}             
});	

function get_dob(){	 
   var mdate=  $("input[name='s_date']").val().split("-");
   startDate = new Date(Number(mdate[2]), Number(mdate[1]) - 1, Number(mdate[0]));
   startDate.setMonth(startDate.getMonth() + 9);
   return startDate;
}   
		 
$(document).on('focus', '.family_date_birth',  function(e){
$(this).removeClass("hasDatepicker");
// $(this).removeAttr("id");
// console.log(this);
if(eValue==2){
   
    
$(this).datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: son_age.getYear()+":",
        minDate:   son_age,
		maxDate: new Date(),
onSelect: function (dateText, inst) {
         $(this).val(dateText);
          var today = new Date();
          var dob = $(this).datepicker("getDate");
          var age_distance = (today.getFullYear() - dob.getFullYear());
          if (age_distance >= 25) 
          {
             $('div .disability').removeAttr('style','display:none');
          }
          // console.log();
        //    if (dob.getFullYear() + 18 > today.getFullYear())
        // {
        //     alert("Under 18");
        // }
        // else
        // {
        //     alert(" Over 18");
        // }
}
    });
}
else if(eValue==3){

     // console.log(son_age);
$(this).datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: son_age.getYear()+":",
        minDate:   son_age,
		maxDate: new Date(),
onSelect: function (dateText, inst) {
         $(this).val(dateText);
}
    });
}
else {
$(this).datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:-18Y",
        maxDate: "-18Y",
        minDate: "-100Y:-18Y",
		onSelect: function (dateText, inst) {
         $(this).val(dateText);
		}
    });
}
});
$('#cancl').on("click", function() {
 $('#policy_member_form_data')[0].reset();
});

$('body').on("keyup",".first_name",function(e) {
var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z]/g, function(str) { return ''; } ) );
        }return;
   }); 
     $('body').on("keyup",".last_name",function(e) { 
var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z]/g, function(str) { return ''; } ) );
        }return;
   });
/*   $('body').on("keyup",".last_name",function(e) { 
var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z\s]/g, function(str) { return ''; } ) );
        }return;
   }); */
   
$('#submit').on("click", function() {
var count = 0;
var policy_id = document.getElementsByName("policy_id");
var family_members_id = document.getElementsByName("family_members_id");
var first_name = document.getElementsByName("first_name");
var last_name = document.getElementsByName("last_name");
var family_date_birth = document.getElementsByName("family_date_birth");
var family_gender = document.getElementsByName("family_gender");
var family_id = document.getElementsByName("family_id");
var marriage_date = document.getElementsByName("marriage_date");
var s_date = document.getElementsByName("s_date");

var policy_idArr = [];
var family_members_idArr = [];
var first_nameArr = [];
var last_nameArr = [];
var family_date_birthArr = [];
var family_genderArr = [];
var family_idArr = [];
var marriage_dateArr = [];
var s_dateArr = [];

for(i = 0; i < policy_id.length; ++i){
policy_idArr.push(policy_id[i].value);
family_members_idArr.push(family_members_id[i].value);
first_nameArr.push(first_name[i].value);
last_nameArr.push(last_name[i].value);
family_date_birthArr.push(family_date_birth[i].value);
family_genderArr.push(family_gender[i].value);
family_idArr.push(family_id[i].value);
marriage_dateArr.push(marriage_date[i].value);
s_dateArr.push(s_date[i].value);


if(policy_id[i].value.trim().length > 0) {
policy_id[i].style = "border-color:black";
} 
else {
policy_id[i].style = "border-color:red";
++count;
}
if(family_members_id[i].value.trim().length > 0) {
family_members_id[i].style = "border-color:black";
}
else {
family_members_id[i].style = "border-color:red";
$(family_members_id[i]).after("<span style='color:red;'>Family member is required</span>");
++count;
}
if(first_name[i].value.trim().length > 0) {
first_name[i].style = "border-color:black";
}
else {
first_name[i].style = "border-color:red";
$(first_name[i]).after("<span style='color:red;'>First name is required</span>");
++count;
}
if(last_name[i].value.trim().length > 0) {
last_name[i].style = "border-color:black";
}
else {
last_name[i].style = "border-color:red";
$(last_name[i]).after("<span style='color:red;'>Last name is required</span>");
++count;
}

if(family_date_birth[i].value.trim().length > 0) {
family_date_birth[i].style = "border-color:black";
}
else {
family_date_birth[i].style = "border-color:red";
$(family_date_birth[i]).after("<span style='color:red;'>Date of birth is required</span>");
++count;
}
if(family_gender[i].value.trim().length > 0) {
family_gender[i].style = "border-color:black";
}
else {
family_gender[i].style = "border-color:red";
$(family_gender[i]).after("<span style='color:red;'>Gender is required</span>");
++count;
}
}
if(count > 0) {
//swal("","Please check for error");
return;
}

$.post("/employee/get_family_details", {
"policy_idArr":JSON.stringify(policy_idArr),
"family_members_idArr":JSON.stringify(family_members_idArr),
"first_nameArr":JSON.stringify(first_nameArr),
"last_nameArr":JSON.stringify(last_nameArr),
"family_date_birthArr":JSON.stringify(family_date_birthArr),
"family_idArr":JSON.stringify(family_idArr),
"family_genderArr":JSON.stringify(family_genderArr),
"marriage_dateArr":JSON.stringify(marriage_dateArr)
}, function(e) {

var data = JSON.parse(e);
// console.log(data);
try{
var data1 = data.count;
	if(data1.status =="error"){
		swal({
                         title: "Warning",
						//title: "Sorry You can not add more than 2 kids",
                        text: "Sorry You can not add more than 2 kids",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function ()
                    {
                         location.reload();
                    });
		//swal("Sorry You can not add more than 2 kids");
		//alert("Sorry You can not add more than 2 kids");
		//location.reload();
		return;
	}
}catch(e) {
	
}

try{
	var data1 = data.count;
	var msg = data1.msg;
	if(msg.status =="error"){
		swal({
                        title: "Warning",
                       text: "Sorry You can not add more",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function ()
                    {
                         location.reload();
                    });
		//alert("Sorry You can not add more ");
		//location.reload();
		return;
	}
}catch(e) {
	
}

try{
	if(data.status =="error"){
		swal({
                        title: "Warning",
                        text: "Sorry You can not add more than 2 kids",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function ()
                    {
                         location.reload();
                    });
		//alert("Sorry You can not add more than 2 kids");
		//location.reload();
		return;
	}
}catch(e) {
	
}
try{
	var errors = data.count;
	if(errors.status=="Enrollment"){
		swal("","Enrollment window closed");
		//alert("Enrollment window closed");
		return;
	}
}catch(e) {
	
}
try {
	var data1 = data.count;
     var msg = data1.msg; 
if(msg.status=="error"){
	swal({
                        title: "Warning",
                        text: "Sorry You can not add more",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function ()
                    {
                         location.reload();
                    });
	//swal("Sorry You can not add more");
	//alert("Sorry You can not add more ");
	//location.reload();
}
/* else if(data1[0]){
alert("Submitted Successfully");
location.reload();
} */
/* else if(data1.msg==true){
alert("Submitted Successfully");
location.reload();
} */
else if(data1[0] || data1.msg==true){
	 swal({
                        title: "Success",
                        text: "Submitted Successfully",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function ()
                    {
                 $.ajax({ 
                url: "/set_enrollment_mails",
                type: "POST",
                async: false,
                dataType: "json",
                data :{
                    "policy_no" : $('#policy_numbers option:selected').data("id"),
                    "policy_id" : $('#policy_numbers option:selected').val()
                },
                success: function (response) {
url = "/employee/home";
$( location ).attr("href", url);
                }
            });

                    });
}

else {
      $.each(data.messages, function(key, value){
       var element = $('#' + key);
       element.closest('div.data').find('.error').remove();
       element.after(value);
     });
    }
}catch(e){
	
}
});
});

// Start of on load append policy number in add member into policy
 /* $.ajax({
 url: "/get_all_policy_no",
     type: "POST",
     async: false,
     dataType: "json",
     success: function (response) {
     console.log(response);
     $('#policy_numbers').empty();
     $('#policy_numbers').append('<option value=""> Select Policy Type</option>');
         for (i = 0; i < response.length; i++) { 
      if(response[i].policy_sub_type_id==1){
      $('#policy_numbers').append('<option value="' + response[i].policy_detail_id + '">' + response[i].policy_sub_type_name + '</option>');
     }
   }
}             
}); */


var max_fields      = 5; //maximum input boxes allowed
var wrapper         = $(".form_row"); 
var add_button      = $(".add_more_form");
var x = 1; 
add_button.click(function(e){ 
var family_date_birth = $("input[name=family_date_birth]").val();
var first_name = $("input[name=first_name]").val();
var last_name = $("input[name=last_name]").val();
if(family_date_birth==''|| first_name=='' || last_name=='' ){
swal("","Please First fill the form");
}
else {
 e.preventDefault();
 if(x < max_fields){ 
   x++; 
  var clone = $( ".form_row_data:first").clone().find("input[type='text'] ").val("").end().find(".family_date_birth").removeAttr("id").end().append('<a  href="#" class="remove_field">Remove</a>');
  // console.log(clone)
            wrapper.append(clone); 
  }
}

});
wrapper.on("click",".remove_field", function(e){ 
    e.preventDefault(); $(this).parent('div').remove(); x--;
});
});
