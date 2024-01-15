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
  
  $(document).ready(function () {
    $("#lmdf").bind("paste", function (e) {
      e.preventDefault();
    });
    $("#lmdt").bind("paste", function (e) {
      e.preventDefault();
    });

  
    $('#myModalaudit').on('hidden.bs.modal', function () {
      // do something…
      // alert('closed');
      $("#auditdata").html("");
  })
  });
  
  $(function () {
    var dateFormat = "yy-mm-dd",
      from = $("#lmdf")
        .datepicker({
          // defaultDate: "+1w",
          dateFormat: "yy-mm-dd",
          changeMonth: true,
          changeYear: true,
        })
        .on("change", function () {
          to.datepicker("option", "minDate", getDate(this));
        }),
      to = $("#lmdt")
        .datepicker({
          // defaultDate: "+1w",
          dateFormat: "yy-mm-dd",
          changeMonth: true,
          changeYear: true,
        })
        .on("change", function () {
          from.datepicker("option", "maxDate", getDate(this));
        });
  
    function getDate(element) {
      var date;
      try {
        date = $.datepicker.parseDate(dateFormat, element.value);
      } catch (error) {
        date = null;
      }
  
      return date;
    }
  });
  
  load_data();
  function ajaxindicatorstart(text) {
    text = typeof text !== "undefined" ? text : "Processing your request..";
  
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
  
  function load_data(data_search) {
    $.fn.dataTable.ext.errMode = "none";
    data_search = data_search || {};
    var oldExportAction = function (self, e, dt, button, config) {
      if (button[0].className.indexOf("buttons-excel") >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
          $.fn.dataTable.ext.buttons.excelHtml5.action.call(
            self,
            e,
            dt,
            button,
            config
          );
        } else {
          $.fn.dataTable.ext.buttons.excelFlash.action.call(
            self,
            e,
            dt,
            button,
            config
          );
        }
      } else if (button[0].className.indexOf("buttons-print") >= 0) {
        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
      }
    };
  
    var newExportAction = function (e, dt, button, config) {
      var self = this;
      var oldStart = dt.settings()[0]._iDisplayStart;
  
      dt.one("preXhr", function (e, s, data) {
        data.start = 0;
        data.length = 2147483647;
  
        dt.one("preDraw", function (e, settings) {
          oldExportAction(self, e, dt, button, config);
  
          dt.one("preXhr", function (e, s, data) {
            settings._iDisplayStart = oldStart;
            data.start = oldStart;
          });
  
          setTimeout(dt.ajax.reload, 0);
  
          return false;
        });
      });
  
      dt.ajax.reload();
    };
    debugger;
  
    function get_current_dt() {
      var currentDate = new Date();
      var day = currentDate.getDate();
      var month = currentDate.getMonth() + 1;
      var year = currentDate.getFullYear();
      var hr = currentDate.getHours();
      var min = currentDate.getMinutes();
      var sec = currentDate.getSeconds();
      return day + "-" + month + "-" + year + " " + hr + "-" + min + "-" + sec;
    }
  
    var dataTable = $("#leadMasterTable").DataTable({
      // "dom": '<f<t><"#df"<"pull-left" i><"pull-right"p><"pull-right"l>>>',
      paging: true,
      lengthChange: true,
      lengthMenu: [
        [10, 25, 100, -1],
        [10, 25, 100, "All"],
      ],
      pageLength: 10,
      processing: true,
      bServerSide: true,
      responsive: true,
      searching: false,
      columnDefs: [
        {
          data: null,
          // "defaultContent": "default content",
          // "targets": ['_all']
        },
      ],
      dom: "lBfrtip",
  
      buttons: [
        {
          extend: "excelHtml5",
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 11,12,13,15,16,17,18,19,20,21,22,23,24,25],
          },
          text: 'Export <i class="ti-export"></i>',
          title: get_current_dt(),
          init: function (dt, node, config) {
            debugger;
          },
        },
      ],
  
      order: [],
      ajax: {
        url: "/get_telesales_data_phasetwo",
        type: "POST",
        data: {
          policy: $("#searchpp").val(),
          epolicy: $("#searchep").val(),
          occcenter: $("#occcenter").val(),
          lmdf: $("#lmdf").val(),
          lmdt: $("#lmdt").val(),
          current_status: $("#current_status").val(),
        },
        complete: function (response) {
          console.log(response);
        },
      },
    });
  }
  

  $("#searchpp").on("keyup change", function () {
    $("#leadMasterTable").DataTable().destroy();
    $(this).attr("disabled", false);
    load_data();
  });
  
  $("#searchep").on("keyup change", function () {
    $("#leadMasterTable").DataTable().destroy();
    $(this).attr("disabled", false);
    load_data();
  });
  
  $("#occcenter").on("keyup change", function () {
    $("#leadMasterTable").DataTable().destroy();
    $(this).attr("disabled", false);
    load_data();
  });
  
  $("#current_status").on("keyup change", function () {
    $("#leadMasterTable").DataTable().destroy();
    $(this).attr("disabled", false);
    load_data();
  });
  
  $("#lmdf").on("keyup change", function () {
    // alert('test');
    $("#leadMasterTable").DataTable().destroy();
    $(this).attr("disabled", false);
    load_data();
  });
  
  $("#lmdt").on("keyup change", function () {
    $("#leadMasterTable").DataTable().destroy();
    $(this).attr("disabled", false);
    load_data();
  });
  
  $(document).on("click", "#clear", function () {
    $("#searchpp").val("");
    setTimeout(function () {
      window.location.reload();
    });
  });
  
  
  $(document).on("click", "#trigger", function () {

    var lead_id_grp = "";
    var confirm_policy_coi_number = $(this).attr("coi_number");
    var send_trigger = "trigger_link";
    var avCode_grp = "";
    var digitalOfficer_grp = $(this).attr("digital-officer");
    var location_grp = $(this).attr("location");
    var master_policy_number = "";
    var dob = $(this).attr("dob");
    var mobile_number_grp = $(this).attr("mobile");
    var claim_status = $(this).attr("claim_status");
    var ped_status = $(this).attr("ped_status");

    var submit_btn = "send_renewal_link_grp";

    var subdisposition_grp = $(this).attr("data-subdisposition");

    var previous_link_sent_by = $(this).attr("data-linktrigger");

    var previous_av_id = $(this).attr("data-avid");
    var previous_av_location = $(this).attr("data-location");

    var in_av_bucket =  $(this).attr("data-avbucket");
    var hr_amount_status =  $(this).attr("data-hramount");

    // 1-12-2021
    // alert(subdisposition_grp);

    // if(subdisposition_grp == '5' || subdisposition_grp == '6' || subdisposition_grp == '48'){
      tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status, submit_btn, subdisposition_grp, previous_link_sent_by, previous_av_id, previous_av_location,in_av_bucket,hr_amount_status);
    // }
    // else{
    //   swal("Retrigger not allowed");
    //   return;
    // }

   

    // tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status);
  });
  
  function tele_renewal_grp_api(lead_id_grp,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp, claim_status, ped_status, submit_btn, subdisposition_grp, previous_link_sent_by, previous_av_id, previous_av_location,in_av_bucket,hr_amount_status){
    if (lead_id_grp !== "" || confirm_policy_coi_number !== "") {
      // $("#renewal_status_grp").val("");
      // alert(confirm_policy_coi_number);return;

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
          previous_link_sent_by: previous_link_sent_by,
          previous_av_id: previous_av_id,
          previous_av_location: previous_av_location,
          in_av_bucket: in_av_bucket,
          hr_amount_status: hr_amount_status,
        },
        dataType: "json",
        beforeSend: function () {
          // set_session();
        },
        success: function (response) {
          ajaxindicatorstop();
          if(response.error.ErrorCode == "10" || response.error.ErrorCode == "03"){
            swal(
              {
                title: "Alert",
                text: "Oops! Enter valid policy number",
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
          }
          else if(response.error.ErrorCode != "00"){
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

              if(is_policy_exist_grp == "yes"){
                 $("#renewal_status_grp").val("Renewal Link Triggered");
              }else if(is_policy_exist_grp == "no"){
                $("#renewal_status_grp").val("Open for Renewal");
              }

              var agent_type_id = $("#agent_type_id").val();
              if(agent_type_id == "1"){
                // alert('here 123');
                $("#view_proposal_agent").attr("disabled", false);
              }
              //$("#send_renewal_link_grp").attr("disabled", false);
              // enable_disable_buttons();


            }else if(renewed_flag === "yes"){
              $("#renewal_status_grp").val("Policy Already Renewed");
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
                              window.location = '/group_renewal_do_home';
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


$(document).on("click", ".make_actionable", function () {
    var lead_id = $(this).attr('lead-id');
    // alert(lead_id);

    swal(
      {
        title: "Are you sure?",
        text: "to make this lead actionable?",
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

          $.ajax({
            url: "/group_renewal_wip_update",
            method: "POST",
            data: { lead_id: lead_id },
            success: function (response) {
                $('#ma_'+lead_id).attr('disabled', 'disabled');
                swal(response);
            },
            error: function (response) {
              
            },
          });
          
        } else {
          swal.close();
        }
      }
    );

});


  $(document).on("click", "#audit", function () {
    $("#myModalaudit").modal("toggle");

    // var lead_id = $(this).attr("lead_id");
    var coi_number = $(this).attr("coi_number");

    $.ajax({
      url: "/telesales_group_renewalphase2_audit",
      method: "POST",
      data: { coi_number: coi_number },
      success: function (response) {


        var data = JSON.parse(response);
        console.log(data);

        var i;
        var json = "";
        for (i = 0; i <= data.length; i++) {
            // alert(data[i].payment_status);
          var new_stat = "";
            if (data[i].status == "0" || data[i].renewed_from_other_mode == 'yes') {
              new_stat = "Lapsed";
            } else if(data[i].status == "1" && data[i].payment_status == "success") {
              new_stat = "Closed";
            } else if(data[i].status == "1"){
              new_stat = "Active";
            }

            av_id_display = data[i].av_id_display;
            if(data[i].av_id_display === undefined){
              av_id_display = '';
            }
            
            var comm_api = JSON.parse(data[i].address_api_res);
            // alert(comm_api.Response.Intermediary_x0020_Code)
  
            json +=
            "<tr><td>" +
            data[i].coi_number +
            "</td><td>" +
            data[i].proposer_name +
            "</td><td>" +
            data[i].product_name +
            "</td><td>" +
            comm_api.Response.Intermediary_x0020_Code +
            "</td><td>" +
            data[i].do_id_display +
            "</td><td>" +
            data[i].agent_id_display +
            "</td><td>" +
            data[i].do_location +
            "</td><td>" +
            data[i].lead_id +
            "</td><td>" +
            data[i].disposition +
            "</td><td>" +
            data[i].subdisposition +
            "</td><td>" +
            data[i].updated_at +
            "</td><td>" +
            data[i].SumInsured +
            "</td><td>" +
            "1 Year" +
            "</td><td>" +
            data[i].member_count +
            "</td><td>" +
            data[i].premium +
            "</td><td>" +
            new_stat +
            "</td></tr>";
  
          $("#auditdata").html(json);
        }
      },
      error: function (response) {
        $("#auditdata").html("Sorry Check Internet Connection");
      },
    });
  
  });  