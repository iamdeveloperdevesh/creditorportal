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
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9,10,12,13,14,15,16,17,18,19,20],
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
      url: "/tele_getrenewaldata",
      type: "POST",
      data: {
        product: $("#searchp").val(),
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

$("#searchp").on("keyup change", function () {
  $("#leadMasterTable").DataTable().destroy();
  $(this).attr("disabled", false);
  load_data();
});

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

$("#importAv").validate({
  ignore: ".ignore",
  rules: {
    filetoUpload: {
      required: true,
    },
  },
  messages: {},
  submitHandler: function (form) {
    ajaxindicatorstart("uploading....");
    var file_agent = $("#file_data");

    var file_agent_data = file_agent[0].files;

    if (file_agent_data.length != 0) {
      var abc = file_agent_data[0].name.substring(
        file_agent_data[0].name.lastIndexOf(".") + 1
      );

      var fileExtension = ["xlsx", "CSV", "xls", "csv"];
      if ($.inArray(abc, fileExtension) == -1) {
        ajaxindicatorstop();
        swal(
          "Alert",
          "Please check documents uploaded, Supported documents xlsx, xls,csv"
        );
        return false;
      }
    }

    var data = new FormData(form);
    $.ajax({
      type: "POST",

      url: "/tele_groupimport",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      mimeType: "multipart/form-data",
      dataType: "json",
      success: function (e) {
        if (e.errorCode == 1) {
          ajaxindicatorstop();
          var er = "";
          if ($.isArray(e.msg)) {
            $.each(e.msg, function (key, msgs) {
              console.log(msgs);
              er += msgs.split("\n");
            });
          } else {
            er = e.msg;
          }

          swal(
            {
              title: "Alert",
              text: er,
              type: "warning",
              showCancelButton: false,
              confirmButtonText: "Ok!",
              closeOnConfirm: true,
              allowOutsideClick: false,
              closeOnClickOutside: false,
              closeOnEsc: false,
              dangerMode: true,
              allowEscapeKey: false,
            },
            function () {}
          );
          return;
        } else {
          ajaxindicatorstop();
          swal(
            {
              title: "Success",
              text: e.msg,
              type: "success",
              showCancelButton: false,
              confirmButtonText: "Ok!",
              closeOnConfirm: true,
              allowOutsideClick: false,
              closeOnClickOutside: false,
              closeOnEsc: false,
              dangerMode: true,
              allowEscapeKey: false,
            },
            function () {
              setTimeout(function () {
                window.location.reload();
              });
            }
          );
        }
      },
    });
  },
});

$(document).on("click", "#trigger", function () {
  // ajaxindicatorstart('Processing');
  var Policy_Number = $(this).attr("policy-number");
  var avCode = $(this).attr("data-av");
  var DoB = $(this).attr("dob");
  var Proposer_MobileNumber = $(this).attr("mobile-number");

  var product_type = $(this).attr("product-type");

  var digitalOfficer = $(this).attr("data-do");
  var lead_id_hidden = $(this).attr("lead-id");
  var location = $(this).attr("data-loc");
  //   alert(lead_id_hidden);
  var hb_mismatch=1;
  if (product_type === "retail") {
    var url = "/tele_renewal";
    ajaxindicatorstart();
    setTimeout(function () {
      $.ajax({
        url: url,
        method: "POST",
        async: true,
        data: {
          Policy_Number: Policy_Number,
          avCode: avCode,
          checkStatus: "afterCheck",
          DoB: DoB,
          Proposer_MobileNumber: Proposer_MobileNumber,
          digitalOfficer: digitalOfficer,
          lead_id_hidden: lead_id_hidden,
          location: location
        },
        dataType: "json",
        beforeSend: function () {
          set_session();
        },
        success: function (response) {
          if (response.error.ErrorMessage === "Success") {
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
                setTimeout(function () {
                  window.location.reload();
                });
              }
            );
          } else {
            // alert(response.error.ErrorMessage);
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
                setTimeout(function () {
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
  if (product_type === "group") {
    // var url = "/tele_renewal_group";
    var send_trigger = "trigger_link";
    var master_policy_number = "";
    // var lead_id_grp = "";
    var retrigger = "yes";
    tele_renewal_grp_api(
		lead_id_hidden,
      Policy_Number,
      send_trigger,
      avCode,
      digitalOfficer,
      location,
      master_policy_number,
      DoB,
      Proposer_MobileNumber,
	  retrigger,hb_mismatch
    );
  }
});

function tele_renewal_grp_api(lead_id,confirm_policy_coi_number,send_trigger, avCode_grp, digitalOfficer_grp, location_grp, master_policy_number, dob, mobile_number_grp,retrigger,hb_mismatch){

	// alert(confirm_policy_coi_number);
	if (lead_id !== "" || confirm_policy_coi_number !== "") {
	  ajaxindicatorstart();
	  setTimeout(function () {
	  $.ajax({
		url: "/tele_renewal_group",
		type: "POST",
		async: true,
		data: {
		  lead_id: lead_id,
		  confirm_policy_coi_number: confirm_policy_coi_number,
		  send_trigger: send_trigger,
		  avCode_grp: avCode_grp,
		  digitalOfficer_grp: digitalOfficer_grp,
		  location_grp: location_grp,
		  master_policy_number: master_policy_number,
		  dob: dob,
		  mobile_number_grp: mobile_number_grp,
		  retrigger: retrigger,
      hb_mismatch:hb_mismatch
		},
		dataType: "json",
		beforeSend: function () {
		  set_session();
		},
		success: function (response) {
		  ajaxindicatorstop();
		  if (response.error.ErrorCode !== "00") {
			swal("Alert", response.error.output_msg, "warning");
		  } else if (response.error.ErrorCode === "00") {
			
			$("#renewal_status_grp").val("Open for renewal");
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
		},
	  });
	}, 1000);
	}
}

$(document).on("click", "#audit", function () {
  $("#myModalaudit").modal("toggle");
  var lead_id = $(this).attr("lead_id");
  $.ajax({
    url: "/telesales_audit",
    type: "POST",
    data: { lead_id: lead_id },
    success: function (response) {
      var data = JSON.parse(response);
      var i;
      var json = "";
      for (i = 0; i <= data.length; i++) {
        // if(data[i].renewal_status == '0'){
        // 	$newval = "Closed";
        // }
        // if(data[i].status == '4'){
        // 	$newval = "Renewed from other mode";
        // }
        // else{
        // 	if(data[i].link_status=='1'){
        // 		$newval="Active";
        // 	}

        // 	else{
        // 		$newval="Lapsed";
        // 	}
        // }
        var new_stat = "";
        if (data[i].status == "1") {
          if (data[i].renewal_status == "0") {
            new_stat = "Closed";
          } else {
            new_stat = "Active";
          }
        } else if (data[i].status == "4") {
          // new_stat = 'Renewed from other mode';
          new_stat = "Lapsed";
        } else if (data[i].status == "0") {
          new_stat = "Lapsed";
        } else if (data[i].renewal_status == "0") {
          new_stat = "Closed";
        }

        // var created_at = data[i].last_updated_on;
        // if(created_at == null){
        // 	created_at = data[i].created_date;
        // }

        json +=
          "<tr><td>" +
          data[i].lead_id +
          "</td><td>" +
          data[i].avnacode +
          "</td><td>" +
          data[i].digital_officer +
          "</td><td>" +
          data[i].location +
          "</td><td>" +
          data[i].policy_number +
          "</td><td>" +
          new_stat +
          "</td><td>" +
          data[i].last_updated_on +
          "</td></tr>";

        $("#auditdata").html(json);
      }
    },
    error: function (response) {
      $("#auditdata").html("Sorry Check Internet Connection");
    },
  });
});
