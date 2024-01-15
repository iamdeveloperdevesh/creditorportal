function ajaxindicatorstart(text) {
    text =
      typeof text !== "undefined"
        ? text
        : "Please wait...";
  
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

    $("#disposition_grp").change(function () {
     var disposition_val = $("#disposition_grp").val();

     if(disposition_val != ''){
        change_subdisposition(disposition_val);
     }
    });

    $("#subdisposition_grp").change(function () {
          enable_disable_buttons();
     });

     $("#disposition_grp").change(function () {
      $("#send_renewal_link_grp").attr('disabled', true);
      $("#proceed_for_renewal_btn").attr('disabled', true);
      $("#only_save").attr('disabled', true);
    });

});

function change_subdisposition(disposition_val){
  ajaxindicatorstart();
  $("#subdisposition_grp").html('');
  
  setTimeout(function () {
    $.ajax({
      url: "/group_cm_update_sub_dis",
      type: "POST",
      async: true,
      data: {
        disposition_val:disposition_val,
      },
      // dataType: "json",
      success: function (response) {
        ajaxindicatorstop();
        $("#subdisposition_grp").html('<option value="">Select Subdisposition</option>');
        $("#subdisposition_grp").append(response);
      },
    });
  }, 100);
}


function enable_disable_buttons(){

  $("#send_renewal_link_grp").attr('disabled', true);
  $("#proceed_for_renewal_btn").attr('disabled', true);
  $("#only_save").attr('disabled', true);

  var renewal_status = $("#renewal_status_grp").val();
  var subdisposition_grp = $("#subdisposition_grp").val();
  var disposition_grp = $("#disposition_grp").val();

  if(subdisposition_grp == null){
    subdisposition_grp = '';
  }

  if(renewal_status != '' && (subdisposition_grp != '')){

      if((renewal_status == "Open for Renewal" || renewal_status == "Renewal Link Triggered" || renewal_status == "Regenerate Lead") && subdisposition_grp == "5"){
        
        $("#only_save").hide();

        $("#send_renewal_link_grp").show();
        $("#send_renewal_link_grp").attr('disabled', false);

        $("#proceed_for_renewal_btn").show();
        $("#proceed_for_renewal_btn").attr('disabled', true);

      }else if((renewal_status == "Open for Renewal" || renewal_status == "Renewal Link Triggered"  || renewal_status == "Regenerate Lead") && (subdisposition_grp == "4" || subdisposition_grp == "6" || disposition_grp == "5"|| disposition_grp == "7"|| disposition_grp == "20"|| disposition_grp == "30"|| disposition_grp == "39"|| disposition_grp == "46")){

        $("#only_save").hide();

        $("#send_renewal_link_grp").show();
        $("#send_renewal_link_grp").attr('disabled', true);

        $("#proceed_for_renewal_btn").show();
        $("#proceed_for_renewal_btn").attr('disabled', false);

      }else{

        $("#send_renewal_link_grp").hide();
        $("#send_renewal_link_grp").attr('disabled', true);

        $("#proceed_for_renewal_btn").hide();
        $("#proceed_for_renewal_btn").attr('disabled', true);

        $("#only_save").show();
        $("#only_save").attr('disabled', false);

      }

  }

  


}

$.validator.addMethod(
    "valid_mobile",
    function (value, element, param) {
      var re = new RegExp("^[4-9][0-9]{9}$");
      return this.optional(element) || re.test(value);
    },
    "Enter a valid 10 digit mobile number."
  );


  $.validator.addMethod(
    "valid_mobile",
    function (value, element, param) {
      var re = new RegExp("^[4-9][0-9]{9}$");
      return this.optional(element) || re.test(value);
    },
    "Enter a valid 10 digit mobile number."
  );



   

   $("#confirm_policy_coi_number_grp").blur(function () {

      $("#send_renewal_link_grp").attr('disabled', true);
      $("#proceed_for_renewal_btn").attr('disabled', true);
      $("#only_save").attr('disabled', true);
      // alert('hi');
      var lead_id_grp = "";
      var confirm_policy_coi_number = $("#confirm_policy_coi_number_grp").val();
      var  send_trigger = "check";
      var avCode_grp = "";
      var  digitalOfficer_grp = $("#digitalOfficer_grp").val();
      var location_grp = $("#location_grp").val();
      var master_policy_number = "";
      var dob = $("#dob").val();
      var  mobile_number_grp =$("#mobile_number_grp").val();


     var claim_status = null;
     var ped_status = null;    
    var dontCheckAV = false;
    var reHit = 0;

      
      tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status, dontCheckAV, reHit);
   });


    function reHitConfirmPolicyCoiNumberGroup() {
      $("#send_renewal_link_grp").attr('disabled', true);
      $("#proceed_for_renewal_btn").attr('disabled', true);
      $("#only_save").attr('disabled', true);
      // alert('hi');
      var lead_id_grp = "";
      var confirm_policy_coi_number = $("#confirm_policy_coi_number_grp").val();
      var send_trigger = "check";
      var avCode_grp = "";
      var digitalOfficer_grp = $("#digitalOfficer_grp").val();
      var location_grp = $("#location_grp").val();
      var master_policy_number = "";
      var dob = $("#dob").val();
      var mobile_number_grp = $("#mobile_number_grp").val();


      var claim_status = null;
      var ped_status = null;
      var dontCheckAV = false;
      var reHit = 1;
      tele_renewal_grp_api(lead_id_grp, confirm_policy_coi_number, send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status, dontCheckAV, reHit);

    }


   function tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status, dontCheckAV, reHit, submit_btn, subdisposition_grp){
          if (lead_id_grp !== "" || confirm_policy_coi_number !== "") {

            // $("#renewal_status_grp").val("");

            var remark_group = $("#remark_group").val();

            if(remark_group == undefined){
              remark_group = '';
            }else{
              remark_group = remark_group.trim();

            }
           
            ajaxindicatorstart();
            setTimeout(function () {
            $.ajax({
              url: "/tele_renewal_group_do",
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
                claim_status: claim_status,
                ped_status: ped_status,
                submit_btn: submit_btn,
                subdisposition_grp: subdisposition_grp,
                remark_group: remark_group,
                dontCheckAV: dontCheckAV,
                reHit: reHit                
              },
              dataType: "json",
              beforeSend: function () {
                // set_session();
              },
              success: function (response) {
                ajaxindicatorstop();
                // if(response.error.ErrorCode == "10" || response.error.ErrorCode == "03"){
                //   swal(
                //     {
                //       title: "Alert",
                //       text: "Oops! Enter valid policy number",
                //       type: "warning",
                //       showCancelButton: false,
                //       confirmButtonText: "Ok!",
                //       closeOnConfirm: true,
                //     },
                //     function () {
                //       setTimeout(function(){
                //       });
                //     }
                //   );
                // }
                // else

            if (response.error.ErrorCode == '0025') {
              $("#renewal_status_grp").val(response.is_policy_exist_grp);
              swal(
                {
                  title: "Success",
                  text: response.error.output_msg,
                  type: "success",
                  showCancelButton: true,
                  confirmButtonText: "Ok!",
                  closeOnConfirm: false,
                },
                function () {
                  reHitConfirmPolicyCoiNumberGroup()
                  swal.close();
                }
              );

          } else if(response.error.ErrorCode != "00"){
                  swal(
                    {
                      title: "Alert",
                      text: response.error.output_msg,
                      type: "warning",
                      showCancelButton: false,
                      confirmButtonText: "Ok!",
                      closeOnConfirm: true,
                    },
                    function () {
                      setTimeout(function(){
                      });
                    }
                  );
                }else if(response.error.ErrorCode == "00"){

                  var is_policy_exist_grp = response.is_policy_exist_grp;

                  $("#is_policy_exist_grp").val(is_policy_exist_grp);


                  var policy_lapsed_flag = response.policy_lapsed_flag.toLowerCase();
                  var renewal_status = response.renewal_status.toLowerCase();
                  var renewed_flag = response.renewed_flag.toLowerCase();

                  

                  if (
                    renewal_status === "yes" &&
                    policy_lapsed_flag === "no" &&
                    renewed_flag === "no"
                  ) {

                    $("#renewal_status_grp").val(is_policy_exist_grp);

                    var agent_type_id = $("#agent_type_id").val();
                    if(agent_type_id == "1"){
                      // alert('here 123');
                      $("#view_proposal_agent").attr("disabled", false);
                    }
                    //$("#send_renewal_link_grp").attr("disabled", false);

                    // 31-12-2021
                    if(is_policy_exist_grp == 'Regenerate Lead'){
                        check_regenerate_lead();
                    }

                    enable_disable_buttons();
                    //update - 16-11-2021
                    // if(is_policy_exist_grp == 'Renewal Link Triggered'){
                      $("#disposition_grp").attr("disabled", false);
                    // }
                    


                  }else if(renewed_flag === "yes"){
                    $("#renewal_status_grp").val("Policy Already Renewed");
                    // 1-12-2021
                    swal(
                      {
                        title: "Success",
                        text: "Policy Already Renewed, Disposition saved successfully!",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true,
                      },
                      function () {
                        setTimeout(function(){
                          window.location.reload();
                        });
                      }
                    );
                  }else if(renewal_status === "no"){
                    $("#renewal_status_grp").val("Policy is Not Renewable");
                  }else if(policy_lapsed_flag === "yes"){
                    $("#renewal_status_grp").val("Policy is Lapsed");
                  } else{
                    $("#renewal_status_grp").val(response.error.output_msg);
                  }

                  if(send_trigger == "trigger_link"){
                        if (response.error.ErrorMessage === "Success") {

                          if(submit_btn == 'send_renewal_link_grp' || submit_btn == 'only_save'){
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
                                  setTimeout(function(){
                                    window.location.reload();
                                  });
                                }
                              );
                          }else{
                              ajaxindicatorstart();
                              location.replace(response.error.output_msg);
                          }
                         


                        } else {
                          swal(
                            {
                              title: "Alert",
                              text: response.error.output_msg,
                              type: "warning",
                              showCancelButton: false,
                              confirmButtonText: "Ok!",
                              closeOnConfirm: true,
                            },
                            function () {
                              setTimeout(function(){
                                window.location.reload();
                              });
                            }
                          );
                        }
                  }

                 

                }

              },
            });
          }, 1000);
          }
   }


  //  31-12-2021
  function check_regenerate_lead(){
    ajaxindicatorstart();
    var confirm_policy_coi_number = $("#confirm_policy_coi_number_grp").val();
    $.ajax({
      url: "/check_regenerate_lead_group_renewal",
      type: "POST",
      data: { 
        confirm_policy_coi_number: confirm_policy_coi_number,
      },
      async: true,
      dataType: "json",
      success: function (response) {
          if(response.status == 'success'){
              location.replace(response.url);
              return;
          }else{
            ajaxindicatorstop();
                   swal(
                      {
                        title: "Are you sure?",
                        text:
                          "This COI is regenerated for another DO, do you want to proceed?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Okay",
                        cancelButtonText: "Cancel",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                      },
                      function (isConfirm) {
                        if (isConfirm) {
                          swal.close();
                        } else {
                          ajaxindicatorstart();
                          swal.close();
                          location.reload();
                          return;
                        }
                      }
                    );
          }
      },
    });
  }


   jQuery.validator.addMethod("specialChars", function( value, element ) {
    var regex = new RegExp("^[a-zA-Z0-9_ ]+$");
    var key = value;

    if (!regex.test(key)) {
       return false;
    }
    return true;
}, "Special characters are not allowed.");

$("#renewal_form_group").validate({
    ignore: ".ignore",
    focusInvalid: true,

    rules: {
      digitalOfficer_grp: {
        required: true,
      },
      location_grp: {
        required: true,
      },
      // mobile_number_grp: {
      //   valid_mobile: true,
      // },
      remark_group:{
        required: true,
        specialChars: true
      },
      disposition_grp:{
        required: true
      },
      subdisposition_grp:{
        required: true
      },
    },

    invalidHandler: function (form, validator) {
      validator.focusInvalid();
    },
    submitHandler: function (form) {
      var submit_btn = $(this.submitButton).attr("id");
      // alert(submit_btn);
      // return;
      var lead_id_grp = "";
      var confirm_policy_coi_number = $("#confirm_policy_coi_number_grp").val();
      var send_trigger = "trigger_link";
      var  avCode_grp = "";
      var  digitalOfficer_grp = $("#digitalOfficer_grp").val();
      var  location_grp = $("#location_grp").val();
      var  master_policy_number = "";
      var dob = $("#dob").val();
      var mobile_number_grp =$("#mobile_number_grp").val();

      var subdisposition_grp =$("#subdisposition_grp").val();

     
      var is_policy_exist = $("#is_policy_exist_grp").val().trim();

     
      var claim_status = null;
      var ped_status = null;
      var dontCheckAV = true;
      var reHit = 0;


      if(is_policy_exist == 'yes' && submit_btn == 'send_renewal_link_grp'){
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
              tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status,dontCheckAV,reHit, submit_btn, subdisposition_grp);
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
              swal.close();
              e.preventDefault();
            }
          }
        );
      }else{
        tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status,dontCheckAV,reHit, submit_btn, subdisposition_grp);
      }
    },
  });