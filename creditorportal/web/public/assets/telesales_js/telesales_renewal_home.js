function ajaxindicatorstart(text) {
  text =
    typeof text !== "undefined"
      ? text
      : "We are quickly gathering your information to get you started";

  var res = "";

  if ($("body").find("#resultLoading").attr("id") != "resultLoading") {
    res += "<div id='resultLoading' style='display: none'>";
    res += "<div id='resultcontent'>";
    res += "<div id='ajaxloader' class='txt'>";
    res +=
      '<svg class="lds-curve-bars" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(2.72337)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(64.7343)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ffffff" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(150.07)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(239.433)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(320.34)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"></animateTransform></circle></g></svg>';
    res += "<br/>";
    res += "<span id='loadingMsg'></span>";
    res += "</div>";
    res += "</div>";
    res += "</div>";

    $("body").append(res);
  }

  $("#loadingMsg").html(text);

  $("#resultLoading").find("#resultcontent > #ajaxloader").css({
    position: "absolute",
    width: "500px",
    height: "75px",
  });

  $("#resultLoading").css({
    width: "100%",
    height: "100%",
    position: "fixed",
    "z-index": "10000000",
    top: "0",
    left: "0",
    right: "0",
    bottom: "0",
    margin: "auto",
  });

  $("#resultLoading").find("#resultcontent").css({
    background: "#ffffff",
    opacity: "0.7",
    width: "100%",
    height: "100%",
    "text-align": "center",
    "vertical-align": "middle",
    position: "fixed",
    top: "0",
    left: "0",
    right: "0",
    bottom: "0",
    margin: "auto",
    "font-size": "16px",
    "z-index": "10",
    color: "#000000",
  });

  $("#resultLoading").find(".txt").css({
    position: "absolute",
    top: "-25%",
    bottom: "0",
    left: "0",
    right: "0",
    margin: "auto",
  });

  $("#resultLoading").fadeIn(300);

  $("body").css("cursor", "wait");
}

function ajaxindicatorstop() {
  $("#resultLoading").fadeOut(300);

  $("body").css("cursor", "default");
}

$(function () {
  $(".hasDatepicker1").datepicker({
    dateFormat: "mm/dd/yy",
    onSelect: function (dateText) {
      $(".policy_number_c").trigger("blur");
    },
    prevText: '<i class="fa fa-angle-left"></i>',
    nextText: '<i class="fa fa-angle-right"></i>',
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
    maxDate: "0",
  });
});

$(document).ready(function () {
  $("#dob").bind("paste", function (e) {
    e.preventDefault();
  });

  var is_a = "";

  $(document).ajaxComplete(function (event, request, settings) {
    if (request.responseText == "tele_session_timeout") {
      location.replace("/login");
    }
  });
  /*code for checking session exist or not end*/

  $.ajax({
    url: "/get_agent_details_renewal",
    type: "POST",
    data: {},
    async: false,
    dataType: "json",
    success: function (response) {
      // alert(response.is_admin);
      if (response.is_admin === "0") {
        $("#avCode").val(response.agent_id);
        $("#avCode").attr("readonly", true);
        // $("#digitalOfficer").val("test_digital_officer");
        $("#location").val(response.center);

        $("#avCode_grp").val(response.agent_id);
        $("#avCode_grp").attr("readonly", true);
        $("#location_grp").val(response.center);

      }
      if (response.is_admin === "1") {
        $("#avCode").val("");
        $("#avCode").attr("readonly", false);

        $("#avCode_grp").val("");
        $("#avCode_grp").attr("readonly", false);
      }
      is_a = response.is_admin;
    },
  });
  $("input[type=radio][name=product_type]").change(function () {
    $("#send_renewal_link").attr("disabled", true);
    $("#renewal_status").val("");
    $("#old_policy_coi_number").val("");
    $("#confirm_policy_coi_number").val("");
    $("#response_label").html("");
    if (is_a === "1") {
      $("#avCode").attr("disabled", true);
      $("#digitalOfficer").val("");
      $("#location").val("");
    }
    $("#renewal_form").validate().resetForm();
    if (this.value == "retail_product") {
      $("#renewal_form").show();
      $("#renewal_form_group").hide();
    } else if (this.value == "group_product") {
      $("#renewal_form_group").show();
      $("#renewal_form").hide();
    }
  });

  // alert("test");
  function set_session() {
    $.ajax({
      url: "/tele_set_session",
      type: "POST",
      data: { lead_id: $("#hidden_lead_id").val() },
      async: false,
      dataType: "json",
      success: function (response) {},
    });
  }

  // $("#avCode").focusout(function (e) {
  //   var avCode = $(this).val();
  //   avCode = avCode.trim();
  //   // alert(avCode);
  //   if (!$(this).is("[readonly]")) {
  //     if (avCode !== "") {
  //       $.ajax({
  //         url: "/checkValidAvCode",
  //         type: "POST",
  //         async: false,
  //         data: {
  //           avCode: avCode,
  //         },
  //         dataType: "json",
  //         beforeSend: function () {
  //           set_session();

  //         },
  //         success: function (response) {
  //           // alert(response.status);
  //           if (response.status === false) {
  //             $("#digitalOfficer").val("");
  //             $("#location").val("");
  //           }
  //           if (response.status === true) {
  //             // $("#digitalOfficer").val(response.digitalOfficer);
  //             $("#location").val(response.location);
  //           }
  //         },
  //       });
  //     }
  //   }
  // });

  $(".policy_number_c, .fc_field").keyup(function (e) {
    $("#send_renewal_link").attr("disabled", true);
    $("#renewal_status").val("");
  });

  $(".fc_field").blur(function (e) {
    var mob = $("#mobile_number").val().trim();
    if(mob !== ""){
      $(".policy_number_c").trigger("blur");
    }
  });


  $(".check_grp").blur(function (e) {
    var confirm_policy_coi_number_grp = $("#confirm_policy_coi_number_grp").val().trim();
    
    if(confirm_policy_coi_number_grp !== ""){
      $(".grp_blur").trigger("blur");
    }
  });


  



  $(".policy_number_c").blur(function () {
    // alert('123');

    $("#send_renewal_link").prop("disabled", true);
    // var confirm_policy_coi_number = $("#confirm_policy_coi_number").val().trim();
    var old_policy_coi_number = $("#confirm_policy_coi_number").val().trim();

    //to be update later
    // var DoB = $("#dob").val().trim();
    var Proposer_MobileNumber = $("#mobile_number").val().trim();

    var avCode = $("#avCode").val().trim();
    var location = $("#location").val().trim();
    var digitalOfficer = $("#digitalOfficer").val().trim();

    var check_mn = $("#renewal_form").validate().element("#mobile_number");

    var check_av =  $("#renewal_form").validate().element("#avCode");
    var check_do =  $("#renewal_form").validate().element("#digitalOfficer");
    var check_location = $("#renewal_form").validate().element("#location");
    var check_pn =  $("#renewal_form").validate().element("#confirm_policy_coi_number");

    if (confirm_policy_coi_number !== "" && old_policy_coi_number !== "" && avCode !== "" && digitalOfficer !== "" && location !== "" && (Proposer_MobileNumber !== '' && Proposer_MobileNumber.length == 10) && check_mn == true && check_av == true && check_do == true && check_location == true && check_pn == true) {
  
        
          var product_type = $(
            "input[type=radio][name=product_type]:checked"
          ).val();
          if (product_type === "retail_product") {
            var url = "/tele_renewal";
          }
          if (product_type === "group_product") {
            var url = "/tele_renewal_group";
          }
          ajaxindicatorstart();
          setTimeout(function () {
            $.ajax({
              url: url,
              type: "POST",
              async: true,
              data: {
                Policy_Number: old_policy_coi_number,
                avCode: avCode,
                checkStatus: "preCheck",
                // DoB: DoB,
                Proposer_MobileNumber: Proposer_MobileNumber,
                digitalOfficer: digitalOfficer,
                location: location,
              },
              dataType: "json",
              beforeSend: function () {
                set_session();
              },
              success: function (response) {
                var policy_lapsed_flag = response.policy_lapsed_flag;
                var renewal_status = response.renewal_status;
                var renewed_flag = response.renewed_flag;
                // alert(response.error.lead_id);
                if (
                  renewal_status === "Yes" &&
                  policy_lapsed_flag === "No" &&
                  renewed_flag === "No"
                ) {
                  
                  if(response.is_policy_exist === 'yes'){
                    $("#renewal_status").val("Renewal Link Triggered");
                  }else{
                    $("#renewal_status").val("Open for Renewal");
                  }

                  $("#lead_id_hidden").val(response.error.lead_id);
                  $("#is_policy_exist").val(response.is_policy_exist);
                  $("#send_renewal_link").attr("disabled", false);
                } else if (renewed_flag === "Yes" && renewal_status === "No") {
                  $("#send_renewal_link").attr("disabled", true);
                  $("#renewal_status").val("Policy Already Renewed");
                } else if (response.error.ErrorCode == "05") {
                  $("#send_renewal_link").attr("disabled", true);
                  swal(
                    "Alert",
                    "Please enter Valid mobile number for entered Policy",
                    "warning"
                  );
                } else if (response.error.ErrorCode == "03") {
                  $("#send_renewal_link").attr("disabled", true);
                  // swal("Alert", "Oops! Enter valid policy number", "warning");
                  swal("Alert", "Failed to fetch data from core API", "warning");

                } else if (response.error.ErrorCode == "04") {
                  $("#send_renewal_link").attr("disabled", true);
                  swal(
                    "Alert",
                    "Please enter Valid DOB for entered Policy",
                    "warning"
                  );
                }else if(policy_lapsed_flag === "Yes"){
                    $("#send_renewal_link").attr("disabled", true);
                    swal(
                      "Alert",
                      "Policy is lapsed",
                      "warning"
                    );
                }
                 else {
                  $("#send_renewal_link").attr("disabled", true);
                  $("#renewal_status").val(response.error.ErrorMessage);
                }
                ajaxindicatorstop();
              },
            });
          }, 1000);
 
    
    } else {
     

      $("#renewal_form").validate().element("#avCode");
      $("#renewal_form").validate().element("#digitalOfficer");
      $("#renewal_form").validate().element("#location");
      $("#renewal_form").validate().element("#mobile_number");
      $("#renewal_form").validate().element("#confirm_policy_coi_number");
      
      $("#renewal_status").val("");
    }
  });

  var msg_do;
  var dynamicErrorMsg_do = function () {
    return msg_do;
  };

  $.validator.addMethod(
    "do_validate",
    function (value, element) {
      var digitalofficer = value;
      digitalofficer = digitalofficer.trim();
      // alert(avCode);
      var status = false;
      if (digitalofficer !== "") {
        $.ajax({
          url: "/checkValiddigitalofficer",
          type: "POST",
          async: false,
          data: {
            digitalofficer: digitalofficer,
          },
          dataType: "json",
          beforeSend: function () {
            set_session();
          },
          success: function (response) {
            // alert(response.status);
            msg_do = response.message;
            if (response.status == true) {
              // $("#location").val(response.location);
              status = true;
            }
          },
        });
      }

      if (status == true) {
        return true;
      }
    },
    dynamicErrorMsg_do
  );

  var msg;
  var dynamicErrorMsg = function () {
    return msg;
  };
  $.validator.addMethod(
    "av_validate",
    function (value, element) {
      var avCode = value;
      avCode = avCode.trim();
      // alert(avCode);
      var status = false;
      //09-04-2021
      //  var output_msg = '';
      if (avCode !== "") {
        $.ajax({
          url: "/checkValidAvCode",
          type: "POST",
          async: false,
          data: {
            avCode: avCode,
          },
          dataType: "json",
          beforeSend: function () {
            set_session();
          },
          success: function (response) {
            //09-04-2021
            msg = response.message;

            if (response.status == true) {
              $("#location").val(response.location);
              $("#location_grp").val(response.location);
              status = true;
            }
          },
        });
      }

      if (status == true) {
        return true;
      }
    },
    dynamicErrorMsg
  );

  $.validator.addMethod(
    "valid_mobile",
    function (value, element, param) {
      var re = new RegExp("^[4-9][0-9]{9}$");
      return this.optional(element) || re.test(value);
    },
    "Enter a valid 10 digit mobile number"
  );

  $('#mobile_number').keypress(function(event){

    if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
        event.preventDefault(); //stop character from entering input
    }

});

  $("#renewal_form").validate({
    ignore: ".ignore",
    focusInvalid: true,
    rules: {
      avCode: {
        required: true,
        av_validate: true,
      },
      digitalOfficer: {
        required: true,
        do_validate: true,
      },
      location: {
        // required: true,
      },
      mobile_number: {
        required: true,
        valid_mobile: true,
      },
      dob: {},
      confirm_policy_coi_number: {
        required: true,
        // equalTo: "#old_policy_coi_number",
      },
    },
    // messages: {
    //   confirm_policy_coi_number: {
    //     equalTo:
    //       "Entered policy number does not match with above entered policy number, please check and enter correct policy number",
    //   },
    // },
    invalidHandler: function (form, validator) {
      validator.focusInvalid();
    },
    submitHandler: function (form) {
      // var confirm_policy_coi_number = $("#confirm_policy_coi_number").val();
      // confirm_policy_coi_number = confirm_policy_coi_number.trim();
      var old_policy_coi_number = $("#confirm_policy_coi_number").val();
      old_policy_coi_number = old_policy_coi_number.trim();
      var avCode = $("#avCode").val();
      avCode = avCode.trim();

      //to be update later
      // var DoB = $("#dob").val().trim();
      var Proposer_MobileNumber = $("#mobile_number").val().trim();

      var product_type = $(
        "input[type=radio][name=product_type]:checked"
      ).val();
      if (product_type === "retail_product") {
        var url = "/tele_renewal";
      }
      if (product_type === "group_product") {
        var url = "/tele_renewal_group";
      }

      var digitalOfficer = $("#digitalOfficer").val().trim();
      var is_policy_exist = $("#is_policy_exist").val().trim();
      var location = $("#location").val().trim();
      var hb_mismatch = $("#hb_mismatch").val();

      // var lead_id_hidden = $("#lead_id_hidden").val().trim();
      // alert("hi");

      
      if(is_policy_exist == 'yes'){
        swal(
          {
            title: "Are you sure?",
            text:
              "Past Reference ID with same Policy No would get lapsed and all Renewal Link associated with Lead Lapsed Reference ID would get disabled",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Send Renewal Link",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false,
          },
          function (isConfirm) {
            if (isConfirm) {
              swal.close();
              send_renewal_link(old_policy_coi_number, avCode, Proposer_MobileNumber, digitalOfficer, location, url,hb_mismatch);
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
              swal.close();
              e.preventDefault();
            }
          }
        );
      }else{
        send_renewal_link(old_policy_coi_number, avCode, Proposer_MobileNumber, digitalOfficer,location, url,hb_mismatch);
      }
 




    },
  });




  $(".grp_blur").blur(function () {
    var product_type = $("input[type=radio][name=product_type]:checked").val();

    var check_av =  $("#renewal_form_group").validate().element("#avCode_grp");
    var check_do =  $("#renewal_form_group").validate().element("#digitalOfficer_grp");
    var check_location = $("#renewal_form_group").validate().element("#location_grp");

    if (product_type === "group_product" && check_av == true && check_do == true && check_location == true) {
      // var lead_id_grp = $("#lead_id_grp").val().trim();
      
      var lead_id_grp = '';
      var confirm_policy_coi_number = $("#confirm_policy_coi_number_grp")
          .val()
          .trim();
      var send_trigger = "";

      var avCode_grp = $("#avCode_grp").val().trim();
      var digitalOfficer_grp = $("#digitalOfficer_grp").val().trim();
      var location_grp = $("#location_grp").val().trim();
      // var master_policy_number = $("#master_policy_number").val().trim();
      var master_policy_number = '';
      var dob = $("#dob").val().trim();
      var mobile_number_grp = $("#mobile_number_grp").val().trim();
      var hb_mismatch = $("#hb_mismatch").val().trim();

      tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp,hb_mismatch);

    }
    else{
      $("#renewal_form_group").validate().element("#avCode_grp");
      $("#renewal_form_group").validate().element("#digitalOfficer_grp");
      $("#renewal_form_group").validate().element("#location_grp");
    }
  });



    $("#renewal_form_group").validate({
    ignore: ".ignore",
    focusInvalid: true,
    rules: {
      avCode_grp: {
        required: true,
        av_validate: true,
      },
      digitalOfficer_grp: {
        required: true,
        do_validate: true,
      },
      location_grp: {
        required: true,
      },
    },

    invalidHandler: function (form, validator) {
      validator.focusInvalid();
    },
    submitHandler: function (form) {

      // var lead_id_grp = $("#lead_id_grp").val().trim();
      var lead_id_grp = '';
      var confirm_policy_coi_number = $("#confirm_policy_coi_number_grp")
          .val()
          .trim();
      var send_trigger = "trigger_link";

      var avCode_grp = $("#avCode_grp").val().trim();
      var digitalOfficer_grp = $("#digitalOfficer_grp").val().trim();
      var location_grp = $("#location_grp").val().trim();
      var hb_mismatch = $("#hb_mismatch").val().trim();
      // var master_policy_number = $("#master_policy_number").val().trim();
      var master_policy_number = '';
      var dob = $("#dob").val().trim();
      var mobile_number_grp = $("#mobile_number_grp").val().trim();
      var is_policy_exist = $("#is_policy_exist_grp").val().trim();

      if(is_policy_exist == 'yes'){
        swal(
          {
            title: "Are you sure?",
            text:
              "Past Reference ID with same Policy No would get lapsed and all Renewal Link associated with Lead Lapsed Reference ID would get disabled",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Send Renewal Link",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false,
          },
          function (isConfirm) {
            if (isConfirm) {
              swal.close();
              tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp,hb_mismatch);
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
              swal.close();
              e.preventDefault();
            }
          }
        );
      }else{
        tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp,hb_mismatch);
      }

      

    },
  });


  function tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp,hb_mismatch){

      // alert(confirm_policy_coi_number);
      if (lead_id_grp !== "" || confirm_policy_coi_number !== "") {
        ajaxindicatorstart();
        setTimeout(function () {
        $.ajax({
          url: "/tele_renewal_group",
          type: "POST",
          async: true,
          data: {
            lead_id_grp: lead_id_grp,
            confirm_policy_coi_number: confirm_policy_coi_number,
            send_trigger: send_trigger,
            avCode_grp: avCode_grp,
            digitalOfficer_grp: digitalOfficer_grp,
            location_grp: location_grp,
            master_policy_number: master_policy_number,
            dob: dob,
            mobile_number_grp: mobile_number_grp,
            hb_mismatch:hb_mismatch
          },
          dataType: "json",
          beforeSend: function () {
            set_session();
          },
          success: function (response) {
            console.log(response);
            ajaxindicatorstop();
            if (typeof response.policy_lapsed_flag != "undefined") {
              var policy_lapsed_flag = response.policy_lapsed_flag.toLowerCase();
              var renewal_status = response.renewal_status.toLowerCase();
              var renewed_flag = response.renewed_flag.toLowerCase();
            }else{
              var policy_lapsed_flag = '';
              var renewal_status = '';
              var renewed_flag = '';
            }
           
           
            if (response.error.ErrorCode === "00") {


              swal(
                {
                  title: "Success", 
                  text: "Renewal Premium validated successfully",
                  type: "success",
                  showCancelButton: false,
                  confirmButtonText: "Ok!",
                  closeOnConfirm: true,
                },
                  function (isConfirm) {
                    if (isConfirm) {
                      swal.close();                  
                    }
                }
              );

              if (
                renewal_status === "yes" &&
                policy_lapsed_flag === "no" &&
                renewed_flag === "no"
              ) {
              
              if(response.error.is_policy_exist === 'yes'){
                $("#renewal_status_grp").val("Renewal Link Triggered");
              }else{
                $("#renewal_status_grp").val("Open for Renewal");
              }

              $("#send_renewal_link_grp").attr("disabled", false);
              $("#is_policy_exist_grp").val(response.error.is_policy_exist);

              if(send_trigger === "trigger_link"){
                swal(
                  {
                    title: "Success", 
                    text: response.error.output_msg,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Ok!",
                    closeOnConfirm: true,
                  },
                  function () {
                    // location.reload();
                    setTimeout(function(){
                      window.location.reload();
                    });
                  }
                );
              }



            }

          }else if(response.error.ErrorCode === "05"){


            swal(
              {
                title: "Alert",
                text:response.error.output_msg,
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok!",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false,
              });
          }else if (response.error.ErrorCode=="0014"){
            $("#send_renewal_link_grp").attr("disabled", true);
            $("#renewal_status_grp").val(response.error.output_msg);
          }else if (renewed_flag === "yes" && renewal_status === "no"){
            $("#send_renewal_link_grp").attr("disabled", true);
            $("#renewal_status_grp").val("Policy Already Renewed");
          }else if (response.error.ErrorCode == "0013") {
            swal("Alert", response.error.output_msg, "warning");
            swal(
              {
                title: "Alert",
                text:response.error.output_msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok!",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false,
              },
              function (isConfirm) {
                if (isConfirm) {
                  swal.close();                  
                  $("#is_policy_exist_grp").val(response.error.is_policy_exist);

                  if(response.error.is_policy_exist === 'yes'){
                    $("#renewal_status_grp").val("Renewal Link Triggered");
                  }else{
                    $("#renewal_status_grp").val("Open for Renewal");

                  }
                  $('#hb_mismatch').val(1);
                  $("#send_renewal_link_grp").attr("disabled", false);
                  
                } else {
                  swal.close();                  
                  $("#send_renewal_link_grp").attr("disabled", true);
                  $('#hb_mismatch').val(0);
                }
              }
            );

            

          }else{
            swal(
              {
                title: "Alert",
                text:response.error.output_msg,
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok!",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false,
              });
    
          }

          },
        });
      }, 1000);
      }
  }





});



function send_renewal_link(old_policy_coi_number, avCode, Proposer_MobileNumber, digitalOfficer,location, url,hb_mismatch){
  ajaxindicatorstart();
  setTimeout(function () {
    $.ajax({
      url: url,
      type: "POST",
      async: true,
      data: {
        Policy_Number: old_policy_coi_number,
        avCode: avCode,
        hb_mismatch:hb_mismatch,
        checkStatus: "afterCheck",
        // DoB: DoB,
        Proposer_MobileNumber: Proposer_MobileNumber,
        digitalOfficer: digitalOfficer,
        location: location,
        // lead_id_hidden:lead_id_hidden,
      },
      dataType: "json",
      beforeSend: function () {
        set_session();
      },
      success: function (response) {
        // if (response.error.ErrorMessage == "lead_lapsed") {
        //     swal(
        //       {
        //         title: "Alert",
        //         text: "Can not retrigger as lead is lapsed",
        //         type: "warning",
        //         showCancelButton: false,
        //         confirmButtonText: "Ok!",
        //         closeOnConfirm: true,
        //       },
        //       function () {
        //         location.reload();
        //       }
        //     );
        // }

         if (response.error.ErrorMessage === "Success") {
          // $("#response_label").html("Link sent on Email and Mobile");
          swal(
            {
              title: "Success",
              text: response.error.output_msg,
              type: "success",
              showCancelButton: false,
              confirmButtonText: "Ok!",
              closeOnConfirm: true,
            },
            function () {
              // location.reload();
              setTimeout(function(){
                window.location.reload();
              });
            }
          );
        } else {
          // $("#response_label").html(response.error.ErrorMessage);
          swal(
            {
              title: "Alert",
              text: response.error.ErrorMessage,
              type: "warning",
              showCancelButton: false,
              confirmButtonText: "Ok!",
              closeOnConfirm: true,
            },
            function () {
              // location.reload();
              setTimeout(function(){
                window.location.reload();
              });
            }
          );
        }
        ajaxindicatorstop();
      },
    });
  }, 1000);
}
