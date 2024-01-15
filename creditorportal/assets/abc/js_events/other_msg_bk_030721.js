$(document).ready(function() {
    var lead_id = $("#lead_id").val();
    var policy_id = $("#policy_id").val();
    
	
    var cust_data_json = JSON.stringify($("#cust_data_json").val());
    $(document).ajaxComplete(function(event, request, settings) {
        if (request.responseText == 'd2c_session_timeout') {
            location.replace('/render_session_timeoutpage');
        }
    });
    var validNavigation = false;
    $(document).bind('keypress', function(e) {
        if (e.keyCode == 116) {
            validNavigation = true;
        }
    });
    $(document).on('click', 'a', function(event) {
        debugger ;if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $(document).on('click', 'button', function(event) {
        debugger ;if (event.currentTarget.getAttribute("id") == 'insert_proposal_button') {
            validNavigation = true;
        }
        ;if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $(document).on('click', 'input[type=button]', function(event) {
        debugger ;if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $(document).on('click', 'input[type=submit]', function(event) {
        debugger ;if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $("a").bind("click", function() {
        debugger ;if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $("button").bind("click", function() {
        debugger ;if (event.currentTarget.getAttribute("id") == 'insert_proposal_button') {
            validNavigation = true;
        }
        ;if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $("input[type=button]").bind("click", function() {
        if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $("form").bind("submit", function() {
        if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    $("input[type=submit]").bind("click", function() {
        if (window.location.pathname != '/retail_enrollment') {
            validNavigation = true;
        }
    });
    window.onbeforeunload = function() {
		
        debugger ;if (!validNavigation) {
            var ua = window.navigator.userAgent;
            var isIE = /MSIE|Trident/.test(ua);
            if (isIE) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "/close_browser_dropoff_action",
                });
            } else {
                navigator.sendBeacon('/close_browser_dropoff_action');
            }
        }
    }
    ;
    if (lead_id && policy_id && cust_data_json) {
        view_popup(lead_id, policy_id, cust_data_json);
    }
});
function view_popup(lead_id, policy_id, cust_data_json) {
    swal({
        title: "Do you want to continue?",
        text: "Proposal already exists!!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, continue with existing!",
        cancelButtonText: "No, create new!",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        var create_new = "yes";
        if (isConfirm) {
            var create_new = "no";
        }
        $.post("/continue_lead_data", {
			
			'logged_in_axis': $("#logged_in_axis").val(),
            'cta': $("#cta").val(),
            "lead_id": lead_id,
            "policy_id": policy_id,
            "cust_data_json": cust_data_json,
            'create_new': create_new,
            
        }, function(e) {
            var obj = JSON.parse(e);
            if (obj.status == 1) {
                window.location.href = obj.url;
            } else if (obj.status == 2) {
                alert("Proposal already exists!!");
            } else if (obj.status == 3) {
                alert("Proposal does not exist!!");
            } else {
                alert("Error in fetch URL");
            }
        });
    });
}