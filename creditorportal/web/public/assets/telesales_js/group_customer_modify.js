  function ajaxindicatorstart(text) {
    text = typeof text !== "undefined" ? text : "Please wait...";

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

  function checkFamilyConstruct() {
    var selected_family_construct = $("#selected_family_construct").val();
    var hidden_family_construct = $("#hidden_family_construct").val();

    if (hidden_family_construct != selected_family_construct) {
      swal(
        "Alert",
        "Members are not added as per family construct selected, Please add/delete member(s) according to the family construct selected.",
        "warning"
      );
      return true;
    }else{
      return false;
    }
  }

  function load_family_construct() {
    var lead_id = $("#lead_id").val();
    var sum_insured = $("#sum_insured").val();
    var product_code = $("#product_code").val();
    var selected_family_construct = $("#selected_family_construct").val();
    ajaxindicatorstart();
    setTimeout(function () {
      $.ajax({
        url: "/group_customer_modify_get_family_construct",
        type: "POST",
        async: true,
        data: {
          lead_id: lead_id,
          sum_insured: sum_insured,
          product_code: product_code,
        },
        dataType: "json",
        success: function (response) {
          ajaxindicatorstop();
          $("#selected_family_construct").children().remove();

          $("#selected_family_construct").append(
            $("<option></option>").attr("value", "").text("Select")
          );

          $.each(response, function (key, value) {
            // alert(value);
            $("#selected_family_construct").append(
              $("<option></option>").attr("value", value).text(value)
            );
          });

          
         

          if(jQuery.inArray(selected_family_construct, response) !== -1){
            $("#selected_family_construct").val(selected_family_construct);
            $("#hidden_family_construct").val(selected_family_construct);
          }else{
            swal(
              "Alert",
              "Please select family construct as sum insured is changed",
              "warning"
            );
          }


        },
      });
    }, 1000);
  }

  $("#selected_family_construct").change(function () {
    checkFamilyConstruct();
  });

  $("#sum_insured").change(function () {

    var previous_sum_insured = $("#previous_sum_insured").val();
    var current_sum_insured = $("#current_sum_insured").val();
    var lead_id = $("#lead_id").val();
      var sum_insured = $("#sum_insured").val();

      var sum_insured_compare = parseFloat(sum_insured);
      var previous_sum_insured_compare = parseFloat(previous_sum_insured);

      if(sum_insured_compare <= previous_sum_insured_compare){
        swal("Alert","New SI should be greater or equal to previous SI!","warning");
        $("#sum_insured").val(current_sum_insured);
        return;
      }

    ajaxindicatorstart();
    setTimeout(function () {

      

      $.ajax({
        url: "/group_customer_modify_update_sum_insured",
        type: "POST",
        async: true,
        data: {
          lead_id:lead_id,
          sum_insured:sum_insured
        },
        dataType: "json",
        success: function (response) {
          ajaxindicatorstop();
          $("#current_sum_insured").val(sum_insured);
            // load_family_construct();
            get_premium();
        },
      });
    }, 100);

    
  });

  $("#nominee_form_edit").click(function(){
        $(".nominee_input").attr("disabled",false);
  });

  
  $("#member_level_edit_btn").click(function(){
    $(".member_level_input").attr("disabled",false);
});

  $("#nominee_form").validate({
    ignore: ".ignore",
    focusInvalid: true,
    rules: {
      nominee_relation: {
        required: true,
      },
      nominee_first_name: {
        required: true,
        lettersonly: true
      },
      nominee_last_name: {
        required: true,
        lettersonly: true
      },
      nominee_contact: {
        maxlength: 10,
        valid_mobile: true,
      },
   
    },

    invalidHandler: function (form, validator) {
      validator.focusInvalid();
    },
    submitHandler: function (form) {
      checkFamilyConstruct();

      if(checkFamilyConstruct()){
        return;
      }
     
      var all_data = $("#nominee_form").serialize();
      var lead_id = $("#lead_id").val();

      all_data = all_data + "&lead_id=" + lead_id;
      // console.log(all_data);
      setTimeout(function () {
        ajaxindicatorstart();
        $.ajax({
          url: "/group_customer_nominee_update",
          type: "POST",
          async: true,
          data: all_data,
          dataType: "json",
          success: function (response) {
            ajaxindicatorstop();
            if (response.status == "success") {
              swal("Success", response.description, "success");
              $(".nominee_input").attr("disabled",true);
            } else {
              swal("Alert", "Something went wrong, please try again.", "warning");
            }
          },
        });
      }, 1000);
    },
  });
  

  $("#premium_breakup_btn").click(function(){
      $("#premiumModal").modal("show");
  });

  function get_premium(){
    var lead_id = $("#lead_id").val();
    $.ajax({
      url: "/group_renewal_get_premium",
      type: "POST",
      async: true,
      data: {
        lead_id: lead_id
      },
      dataType: "json",
      success: function (response) {
        $("#premium_breakup_row").html("");
        var total_premium = 0;
        $.each(response, function(index, value) {
            // alert(value["name"]);
            // $("#premium_breakup_row")
            // .append('<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;" class=""> Name: <br></span><div style="color:#000;" class="premium_breakup_name_span">'+value["name"]+'</div></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+value["sum_insured"]+'</span></div><div><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+value["premium"]+'</span></div> </div>');

            $("#premium_breakup_row").append('<div class="col-md-4"><div class="form-group"> <label for="example-text-input" class="col-form-label"><p class="pre-ttl ">'+value["name"]+' <span class="prim-color">(SI - '+value["sum_insured"]+')</span></p></label><p class="pre-breakup" data-toggle="modal" data-target="#premium-bk"><i class="fa fa-rupee mr-1"></i> '+value["premium"]+' <span class="ml-1">/-</span><span class="txt-tax">Premium <span>(incl.Tax)</span></p></div></div>');

            total_premium = total_premium + parseFloat(value["premium"]);
        });

        // alert(total_premium);
        $("#total_premium_display").val(total_premium);

      },
    });
  }

  function updateMember(member_id) {
    ajaxindicatorstart();
    setTimeout(function () {

      $("#update_first_name").val("");
      $("#update_last_name").val("");
      $("#update_member_id").val("");

      $.ajax({
        url: "/group_customer_modify_get_mem_details",
        type: "POST",
        async: true,
        data: {
          member_id:member_id
        },
        dataType: "json",
        success: function (response) {
          ajaxindicatorstop();

          $("#update_member_modal").modal("show");
          $("#update_first_name").val(response.first_name);
          $("#update_last_name").val(response.last_name);
          $("#update_member_id").val(member_id);
        },
      });
    }, 100);
  }



  $( document ).ready(function() {

    // 31-12-2021
    var disposition_master_id = $("#disposition_master_id").val();
      if(disposition_master_id == '47'){
        var disposition_val = $("#disposition_grp").val();
        if(disposition_val == 4){
            $("#submit_to_av").show();
            $("#save_disposition").hide();
            $("#send_renewal_link").hide();
        }
      }

    $('.member_allow_form').bind('keydown', function(event) {
      var key = event.which;
      if ((key >=48 && key <= 57) || key == 32) {
        event.preventDefault();
      }
    });

    $('input[type=checkbox][name=health_return_checkbox]').change(function() {
      change_hr_amount(this.value);
    });

    $(".claim_checkbox").change(function() {
      $(".claim_checkbox").prop('checked', false);
      $(this).prop('checked', true);
  });

  $(".ped_checkbox").change(function() {
    $(".ped_checkbox").prop('checked', false);
    $(this).prop('checked', true);
});

$(".health_return_checkbox").change(function() {
  $(".health_return_checkbox").prop('checked', false);
  $(this).prop('checked', true);
});

});


function change_hr_amount(val){
    if (val == 'yes') {
        var health_returns = parseFloat($("#health_returns").val());
        var total_premium_display = parseFloat($("#total_premium_display").val());
        

        var display = total_premium_display - health_returns;
        $("#total_premium_display_to_pay").val(display);
    }
    else if (val == 'no') {
      var total_premium_display = parseFloat($("#total_premium_display").val());
      $("#total_premium_display_to_pay").val(total_premium_display);
    }
}

  function update_member_form(update_member_id){

    ajaxindicatorstart();
    var lead_id = $("#lead_id").val();
    var update_first_name = $("#update_first_name"+update_member_id).val();
    var update_last_name = $("#update_last_name"+update_member_id).val();

    
    setTimeout(function () {
      $.ajax({
        url: "/group_customer_modify_update_mem_submit",
        type: "POST",
        async: true,
        data: {
          update_member_id:update_member_id,
          lead_id:lead_id,
          update_first_name:update_first_name,
          update_last_name:update_last_name,
        },
        dataType: "json",
        success: function (response) {
          ajaxindicatorstop();
          if (response.status == "success") {
            swal("Success", "Member updated successfully!", "success");
          } else {
            swal("Alert", "Something went wrong, please try again.", "warning");
          }
        },
      });
    }, 100);

  }

  // $(".update_member_form").validate({
    
  //   ignore: ".ignore",
  //   focusInvalid: true,
  

  //   invalidHandler: function (form, validator) {
  //     validator.focusInvalid();
  //   },
  //   submitHandler: function (form) {
  //     ajaxindicatorstart();
  //     var all_data = $(this).serialize();
  //     var lead_id = $("#lead_id").val();

  //     all_data = all_data + "&lead_id=" + lead_id;
  //     console.log(all_data);
  //     return;
  //     setTimeout(function () {
  //       $.ajax({
  //         url: "/group_customer_modify_update_mem_submit",
  //         type: "POST",
  //         async: true,
  //         data: all_data,
  //         dataType: "json",
  //         success: function (response) {
  //           ajaxindicatorstop();
  //           $("#update_member_modal").modal("hide");
  //           if (response.status == "success") {
             
  //           } else {
  //             swal("Alert", "Something went wrong, please try again.", "warning");
  //           }
  //         },
  //       });
  //     }, 1000);
  //   },
  // });

  function deleteMember(member_id) {
    var lead_id = $("#lead_id").val();
    swal(
      {
        title: "Are you sure?",
        text: "You want to delete this member?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: false,
      },
      function (isConfirm) {
        if (isConfirm) {
          swal.close();
          //   alert("deleted");
          $(this).closest("tr").remove();
          $.ajax({
            url: "/group_customer_modify_mem_del",
            type: "POST",
            data: {
              member_id,
              member_id,
              lead_id,
              lead_id,
            },
            dataType: "json",
            success: function (response) {
              $("#member_tr_" + member_id).remove();
              swal(
                {
                  title: "Success",
                  text: "Member deleted successfully!",
                  type: "success",
                  showCancelButton: false,
                  confirmButtonText: "Ok!",
                  closeOnConfirm: true,
                },
                function () {
                  setTimeout(function () {
                    // alert(response.family_construct);
                    $("#hidden_family_construct").val(response.family_construct);
                    $("#selected_family_construct").val(
                      response.family_construct
                    );
                    //  checkFamilyConstruct();
                    // swal(
                    //   "Alert",
                    //   "Family construct has been changed, due to member deletion",
                    //   "warning"
                    // );

                    get_premium();
                    location.reload();

                  });
                }
              );
            },
          });
        } else {
          swal.close();
        }
      }
    );
  }


  $("#cancelled_btn").click(function () {
      cancelled_by_customer();
  });

  function cancelled_by_customer() {
    var lead_id = $("#lead_id").val();
    swal(
      {
        title: "Are you sure?",
        text: "You want to cancel renewal for policy?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: false,
      },
      function (isConfirm) {
        if (isConfirm) {
          swal.close();

          setTimeout(function () {
            ajaxindicatorstart();
            $.ajax({
              url: "/group_renewal_modify_customer_cancelled",
              type: "POST",
              async: true,
              data: {
                lead_id:lead_id
              },
              dataType: "json",
              success: function (response) {
                ajaxindicatorstop();
                window.location.replace(response.url);
              },
            });
          }, 1000);
          
        } else {
          swal.close();
        }
      }
    );
  }


  function disable_fields(lead_id){
    $(".inp_gen").hide();
    //commented code because module is non-editable

    // setTimeout(function () {
    //   ajaxindicatorstart();
    //   $.ajax({
    //     url: "/group_renewal_check_is_editable",
    //     type: "POST",
    //     async: true,
    //     data: {
    //       lead_id:lead_id
    //     },
    //     // dataType: "json",
    //     success: function (response) {
    //       ajaxindicatorstop();
    //       if(response == "no"){
    //         // $(".inp_gen").attr("disabled", true);
    //         $(".inp_gen").hide();
    //       }
    //     },
    //   });
    // }, 1000);
  }

  $(document).ready(function () {
    $("#mem_form_dob").bind("paste", function (e) {
      e.preventDefault();
    });


   
    var lead_id = $("#lead_id").val();
    // alert(lead_id);return;
    disable_fields(lead_id);


  });

  $(function () {
    $(".hasDatepicker1").datepicker({
      dateFormat: "yy-mm-dd",
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

  $("#mem_form_relation").change(function () {
    var cust_gender = $("#cust_gender").val();
    var sel_val = $(this).val();

    if (sel_val == "0") {
      $("#mem_form_gender").val(cust_gender);

      if (cust_gender == "M") {
        $("#mem_form_salutation").val("Mr");
      }

      if (cust_gender == "F") {
        $("#mem_form_salutation").val("Mrs");
      }
    }

    if (sel_val == "1") {
      if (cust_gender == "M") {
        $("#mem_form_gender").val("F");
        $("#mem_form_salutation").val("Mrs");
      }
      if (cust_gender == "F") {
        $("#mem_form_gender").val("M");
        $("#mem_form_salutation").val("Mr");
      }
    }

    if (sel_val == "2") {
      $("#mem_form_gender").val("M");
      $("#mem_form_salutation").val("Mr");
    }

    if (sel_val == "3") {
      $("#mem_form_gender").val("F");
      $("#mem_form_salutation").val("Ms");
    }
  });

  jQuery.validator.addMethod(
    "lettersonly",
    function (value, element) {
      return this.optional(element) || /^[a-z]+$/i.test(value);
    },
    "Please enter only alphabets."
  );

  jQuery.validator.addMethod(
    "letter_spaces_sonly",
    function (value, element) {
      return this.optional(element) || /^[a-z\s]+$/i.test(value);
    },
    "Please enter only alphabets."
  );

  // $("#member_addition_form").validate({
  //   ignore: ".ignore",
  //   focusInvalid: true,
  //   rules: {
  //     mem_form_salutation: {
  //       required: true,
  //     },
  //     mem_form_relation: {
  //       required: true,
  //     },
  //     mem_form_fname: {
  //       required: true,
  //       lettersonly: true,
  //     },
  //     mem_form_lname: {
  //       required: true,
  //       lettersonly: true,
  //     },
  //     mem_form_dob: {
  //       required: true,
  //     },
  //   },

  //   invalidHandler: function (form, validator) {
  //     validator.focusInvalid();
  //   },
  //   submitHandler: function (form) {
  //     ajaxindicatorstart();
  //     var all_data = $("#member_addition_form").serialize();

  //     var lead_id = $("#lead_id").val();
  //     var sum_insured = $("#sum_insured").val();
  //     var relation_desc = $("#mem_form_relation option:selected").text();

  //     all_data =
  //       all_data +
  //       "&lead_id=" +
  //       lead_id +
  //       "&sum_insured=" +
  //       sum_insured +
  //       "&relation_desc=" +
  //       relation_desc;

  //     setTimeout(function () {
  //       $.ajax({
  //         url: "/group_customer_modify_mem_add",
  //         type: "POST",
  //         async: true,
  //         data: all_data,
  //         dataType: "json",
  //         success: function (response) {
  //           ajaxindicatorstop();

  //           if (response.status != "success") {
  //             swal("Alert", response.description, "warning");
  //           }else{
  //             swal(
  //               {
  //                 title: "Success",
  //                 text: "Member added successfully",
  //                 type: "success",
  //                 showCancelButton: false,
  //                 confirmButtonText: "Ok!",
  //                 closeOnConfirm: true,
  //               },
  //               function () {
  //                 setTimeout(function(){
  //                   location.reload();
  //                 });
  //               }
  //             );
  //           }
  //         },
  //       });
  //     }, 1000);
  //   },
  // });

  $("#disposition_grp").change(function () {
    var disposition_val = $("#disposition_grp").val();
    // alert(1);
    // alert(disposition_val);

    // if(disposition_val == 4){
    //   $("#submit_to_av").show();
    //   $("#save_disposition").hide();

    // }else{
    //   $("#save_disposition").show();
    //   $("#submit_to_av").hide();
    // }
    
    // if(disposition_val != 48){
    //   $("#send_renewal_link").hide();
    //   $("#save_disposition").show();
    // }else{
    //   $("#send_renewal_link").show();
    //   $("#save_disposition").hide();
    // }

    if(disposition_val == 4){
        $("#submit_to_av").show();
        $("#save_disposition").hide();
        $("#send_renewal_link").hide();
    }else if(disposition_val == 6 || disposition_val == 48){
        $("#send_renewal_link").show();
        $("#save_disposition").hide();
        $("#submit_to_av").hide();
    }else{
        $("#save_disposition").show();
        $("#submit_to_av").hide();
        $("#send_renewal_link").hide();
    }

    if(disposition_val != ''){
       change_subdisposition(disposition_val);
    }
   });

  function change_subdisposition(disposition_val){
    // alert(2);
    ajaxindicatorstart();
    $("#subdisposition_grp").html('');
    var proposal_page = 1;
    
    setTimeout(function () {
      $.ajax({
        url: "/group_cm_update_sub_dis",
        type: "POST",
        async: true,
        data: {
          disposition_val:disposition_val,
          proposal_page: proposal_page
        },
        // dataType: "json",
        success: function (response) {
          ajaxindicatorstop();
          // $("#subdisposition_grp").html('<option value="">Select Subdisposition</option>');
          $("#subdisposition_grp").append(response);
        },
      });
    }, 100);
  }

  $.validator.addMethod(
    "valid_mobile",
    function (value, element, param) {
      var re = new RegExp("^[4-9][0-9]{9}$");
      return this.optional(element) || re.test(value);
    },
    "Enter a valid 10 digit mobile number."
  );

  $("#submit_form").validate({
    ignore: ".ignore",
    focusInvalid: true,

    rules: {
     
     
    },

    invalidHandler: function (form, validator) {
      validator.focusInvalid();
    },
    submitHandler: function (form) {
      checkFamilyConstruct();


      if(checkFamilyConstruct()){
        return;
      }

      if( (!$('input[name="claim_checkbox"]').is(':checked')) || (!$('input[name="ped_checkbox"]').is(':checked')) )
      {
        swal("Alert","Please answer all the questions for proceeding", "warning");
          return;
      }

      if(!$('input[name="ghd_1"]').is(':checked'))
      {
        swal("Alert","Please accept declaration for proceeding", "warning");
          return;
      }

    

     
      var all_data = $("#submit_form").serialize();
      var lead_id = $("#lead_id").val();
      var claim_status =   $('input[name="claim_checkbox"]:checked').val();
      var ped_status =   $('input[name="ped_checkbox"]:checked').val();
      var hr_amount_status =   $('input[name="health_return_checkbox"]:checked').val();

      if(hr_amount_status == undefined){
        hr_amount_status = "no";
      }
      // var auto_debit_status =   $('input[name="auto_debit"]:checked').val();
      var auto_debit_status =   '';

      if(auto_debit_status == undefined){
        auto_debit_status = "no";
      }

      // alert(hr_amount_status);
      // return;

      all_data = all_data + "&lead_id=" + lead_id + "&claim_status=" + claim_status + "&ped_status=" + ped_status + "&hr_amount_status=" + hr_amount_status + "&auto_debit_status=" + auto_debit_status;
      // console.log(all_data);

      var submit_btn = $(this.submitButton).attr("id");
      

      if(submit_btn == 'accept_proceed'){
        
        setTimeout(function () {
          ajaxindicatorstart();
          $.ajax({
            url: "/group_customer_modify_submit",
            type: "POST",
            async: true,
            data: all_data,
            dataType: "json",
            success: function (response) {
              ajaxindicatorstop();
              if (response.description == "success") {
                // $("#otp_modal").modal("show");
                location.replace(response.url);
                // swal('to payment');
              } else {
                swal("Alert", response.msg, "warning");
              }
            },
          });
        }, 1000);

      }

      if(submit_btn == "accept_otp"){

        setTimeout(function () {
          ajaxindicatorstart();
          $.ajax({
            url: "/group_customer_modify_submit_otp",
            type: "POST",
            async: true,
            data: all_data,
            dataType: "json",
            success: function (response) {
              console.log(response);
              ajaxindicatorstop();
              if (response.description == "success") {
                $("#otp_modal").modal("show");
              } else {
                swal("Alert", "Something went wrong, please try again.", "warning");
              }
            },
          });
        }, 1000);

      }

     
    },
  });

  $(document).ready(function () {
    $("#resend_otp").click(function () {
      $("#submit_form").submit();
    });
  });

  $(document).ready(function () {
    $("#validate_otp").click(function () {
      var pos_otp = $("#pos_otp").val();
      var lead_id = $("#lead_id").val();
      var hr_amount_status =   $('input[name="health_return_checkbox"]:checked').val();

      if(hr_amount_status == undefined){
        hr_amount_status = "no";
      }

      ajaxindicatorstart();
      setTimeout(function () {
        $.ajax({
          url: "/group_customer_modify_validate_otp",
          type: "POST",
          async: true,
          data: {
            pos_otp: pos_otp,
            lead_id: lead_id,
            hr_amount_status:hr_amount_status
          },
          dataType: "json",
          success: function (response) {
            if (response.description == "success") {
              location.replace(response.url);
            } else {
              ajaxindicatorstop();
              swal("Alert", response.msg, "warning");
            }
          },
        });
      }, 1000);
    });
  });


  $("#submit_to_av").click(function(){

    var lead_id = $("#lead_id").val();
    var remarks = $("#remarks_grp").val();
    var disposition_master_id = $("#subdisposition_grp").val();

    var claim_status =   $('input[name="claim_checkbox"]:checked').val();
    var ped_status =   $('input[name="ped_checkbox"]:checked').val();
    var hr_amount_status =   $('input[name="health_return_checkbox"]:checked').val();
  
        // 1-12-2021
        if( (!$('input[name="claim_checkbox"]').is(':checked')) || (!$('input[name="ped_checkbox"]').is(':checked')) )
        {
          swal("Alert","Please answer all the questions for proceeding", "warning");
            return;
        }
  
         /*
      if(!$('input[name="ghd_1"]').is(':checked'))
      {
        swal("Alert","Please accept declaration for proceeding", "warning");
          return;
      }
      */

    ajaxindicatorstart();
    setTimeout(function () {
      $.ajax({
        url: "/grp_renewal_sub_to_av",
        type: "POST",
        async: true,
        data: {
          lead_id: lead_id,
          remarks: remarks,
          claim_status: claim_status,
          ped_status: ped_status,
          hr_amount_status: hr_amount_status,
          disposition_master_id:disposition_master_id
        },
        dataType: "json",
        success: function (response) {
            // location.replace(response.url);
            ajaxindicatorstop();
            swal({
              title: "Success!",
              text: "Lead submitted to AV!",
              type: "success"
            }, function() {
              ajaxindicatorstart();
              window.location = response.url;
          });
        },
      });
    }, 1000);

});


$("#send_renewal_link").click(function(){

  var remarks = $("#remarks_grp").val();
  var disposition_master_id = $("#subdisposition_grp").val();

  var claim_status =   $('input[name="claim_checkbox"]:checked').val();
  var ped_status =   $('input[name="ped_checkbox"]:checked').val();
  var hr_amount_status =   $('input[name="health_return_checkbox"]:checked').val();

      // 1-12-2021
      if( (!$('input[name="claim_checkbox"]').is(':checked')) || (!$('input[name="ped_checkbox"]').is(':checked')) )
      {
        swal("Alert","Please answer all the questions for proceeding", "warning");
          return;
      }

      /*
      if(!$('input[name="ghd_1"]').is(':checked'))
      {
        swal("Alert","Please accept declaration for proceeding", "warning");
          return;
      }
      */

  var lead_id = $("#lead_id").val();
  var is_policy_exist = $("#previous_link_exist").val();
  
  // 27-12-2021
  if(is_policy_exist == 'exist'){
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
          var lead_id_grp = "";
          var confirm_policy_coi_number = $("#send_renewal_link").attr("coi_number");
          var send_trigger = "trigger_link";
          var avCode_grp = "";
          var digitalOfficer_grp = $("#send_renewal_link").attr("digital-officer");
          var location_grp = $("#send_renewal_link").attr("location");
          var master_policy_number = "";
          var dob = $("#send_renewal_link").attr("dob");
          var mobile_number_grp = $("#send_renewal_link").attr("mobile");
          var claim_status = $("#send_renewal_link").attr("claim_status");
          var ped_status = $("#send_renewal_link").attr("ped_status");
      
          var submit_btn = "send_renewal_link_grp";
      
          var subdisposition_grp = $("#send_renewal_link").attr("data-subdisposition");
      
          var previous_link_sent_by = $("#send_renewal_link").attr("data-linktrigger");
      
          var previous_av_id = $("#send_renewal_link").attr("data-avid");
          var previous_av_location = $("#send_renewal_link").attr("data-location");
      
          var in_av_bucket = $("#send_renewal_link").attr("data-avbucket");
          var hr_amount_status =  $("#send_renewal_link").attr("data-hramount");
          tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status, submit_btn, subdisposition_grp, previous_link_sent_by, previous_av_id, previous_av_location,in_av_bucket,hr_amount_status);

        } else {
          // swal("Cancelled", "Your imaginary file is safe :)", "error");
          swal.close();
          e.preventDefault();
        }
      }
    );
  }else{
    ajaxindicatorstart();
    setTimeout(function () {
      $.ajax({
        url: "/grp_renewal_av_send_pss",
        type: "POST",
        async: true,
        data: {
          lead_id: lead_id,
          remarks: remarks,
          claim_status: claim_status,
          ped_status: ped_status,
          hr_amount_status: hr_amount_status,
          disposition_master_id:disposition_master_id
        },
        dataType: "json",
        success: function (response) {
            // location.replace(response.url);
            ajaxindicatorstop();
            swal({
              title: "Success!",
              text: response.output_msg,
              type: "success"
            }, function() {
              ajaxindicatorstart();
              window.location = response.url;
          });
        },
      });
    }, 1000);
  }






});



$("#save_disposition").click(function(){

  var lead_id = $("#lead_id").val();
  var disposition_master_id = $("#subdisposition_grp").val();
  var remarks = $("#remarks_grp").val();

  var claim_status =   $('input[name="claim_checkbox"]:checked').val();
  var ped_status =   $('input[name="ped_checkbox"]:checked').val();
  var hr_amount_status =   $('input[name="health_return_checkbox"]:checked').val();

      // 1-12-2021
      if( (!$('input[name="claim_checkbox"]').is(':checked')) || (!$('input[name="ped_checkbox"]').is(':checked')) )
      {
        swal("Alert","Please answer all the questions for proceeding", "warning");
          return;
      }

       /*
      if(!$('input[name="ghd_1"]').is(':checked'))
      {
        swal("Alert","Please accept declaration for proceeding", "warning");
          return;
      }
      */

  ajaxindicatorstart();
  setTimeout(function () {
    $.ajax({
      url: "/grp_renewal_av_save",
      type: "POST",
      async: true,
      data: {
        lead_id: lead_id,
        disposition_master_id: disposition_master_id,
        claim_status: claim_status,
        ped_status: ped_status,
        hr_amount_status: hr_amount_status,
        remarks: remarks,
      },
      dataType: "json",
      success: function (response) {
          // location.replace(response.url);
          ajaxindicatorstop();
          swal({
            title: "Success!",
            text: response.output_msg,
            type: "success"
          }, function() {
            ajaxindicatorstart();
            window.location = response.url;
        });
      },
    });
  }, 1000);

});
