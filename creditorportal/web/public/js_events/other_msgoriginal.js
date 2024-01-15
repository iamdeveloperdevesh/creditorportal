$(document).ready(function () {


    var conn = new WebSocket('ws://abhiaxis.benefitz.in:8080');
    conn.onopen = function(e) {
        //console.log("123");
        //alert($("#empIdHidden").val());
        conn.send($("#empIdHiddendropoff").val());
    }
    ;

    conn.onmessage = function(e) {
        console.log(e.data);
    }
    var lead_id = $("#lead_id").val();
    var policy_id = $("#policy_id").val();
    var cust_data_json = JSON.stringify($("#cust_data_json").val());

    /*code for checking session exist or not start*/
    $(document).ajaxComplete(function (event, request, settings) {
        if (request.responseText == 'd2c_session_timeout') {
            location.replace('/render_session_timeoutpage');
        }
    });
    /*code for checking session exist or not end*/

    /* drop off functionality start */
    var validNavigation = false;
    var validDropOff = false;
	var lead_idEle = "";
	
    /* Attach the event keypress to exclude the F5 refresh (includes normal refresh)*/
    /*
	$(document).bind('keypress', function (e) {

        if (e.keyCode == 116) {
            validNavigation = true;
        }
    });

	$(document).on('click', 'a', function(event){
		  validNavigation = true;
	});
	
	$(document).on('click', 'button', function(event){
		  validNavigation = true;
	});
	
	$(document).on('click', 'input[type=button]', function(event){
		  validNavigation = true;
	});
	
	$(document).on('click', 'input[type=submit]', function(event){
		  validNavigation = true;
	});
	
    $("a").bind("click", function () {

        validNavigation = true;
    });
	
    $("button").bind("click", function () {

        validNavigation = true;
    });
	 $("input[type=button]").bind("click", function () {
        validNavigation = true;

    });
	
    $("form").bind("submit", function () {

        validNavigation = true;
    });

    $("input[type=submit]").bind("click", function () {
        validNavigation = true;

    });*/
	
	// Drop Off 1
	window.onbeforeunload = function (event) {
		debugger;
		var fileName = location.href.split("/").slice(-1); 
		
		var lead_idEle = document.getElementById("lead_id_hidden");
		if (lead_idEle != null && fileName == 'retail_enrollment') {
			lead_idEle = lead_idEle.value;
			if (!validDropOff) {		
				validDropOff = true;	
				/*callDropOffEvent();*/
			}
		}

		/*setTimeout(function() {}, 1000);
		
		if (typeof event == 'undefined') {
			event = window.event;
		}
		
		if (event) {
			if (!validNavigation) {	
			   validNavigation = true;	
			   callDropOffEvent()
			}
		}*/
	};
	
	// Drop Off 2
	window.addEventListener('beforeunload', function (e) {
	  debugger;
	  var fileName = location.href.split("/").slice(-1); 
	  // Cancel the event
	  setTimeout(function() {}, 1000);
	  e.preventDefault(); // If you prevent default behavior in Mozilla Firefox prompt will always be shown
		var lead_idEle = document.getElementById("lead_id_hidden");
		if (lead_idEle != null && fileName == 'retail_enrollment') {
			lead_idEle = lead_idEle.value;
			if (!validDropOff) {		
				validDropOff = true;	
				/*callDropOffEvent();*/
			}
		}
	});
	
	// Drop Off 3
	window.addEventListener("pagehide", function (evt) {
		debugger;
		var fileName = location.href.split("/").slice(-1); 
		var lead_idEle = document.getElementById("lead_id_hidden");
		if (lead_idEle != null && fileName == 'retail_enrollment') {
			lead_idEle = lead_idEle.value;
			if (!validDropOff) {		
				validDropOff = true;	
				/*callDropOffEvent();*/
			}
		}
	}, false);
	
	// Drop Off Function
	function callDropOffEvent(){
		debugger;
		$.ajax({
			type: "POST",
			url: "/close_browser_dropoff_action",
		});
	}
	
    /*window.onbeforeunload = function () {
		debugger;
        if (!validNavigation) {		
			
		   $.ajax({
                type: "POST",
                url: "/close_browser_dropoff_action",
            });
        }
    };*/
    
	/* drop off functionality end  */
    
    if (lead_id && policy_id && cust_data_json) {
        view_popup(lead_id, policy_id, cust_data_json);
    }

});

/* popup msg */

function view_popup(lead_id, policy_id, cust_data_json)
{
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
    },
            function (isConfirm) {
                var create_new = "yes";/*continue with new */
                if (isConfirm) {
                    var create_new = "no"; /*continue with existing */
                }

                $.post("/continue_lead_data",
                        {"lead_id": lead_id, "policy_id": policy_id, "cust_data_json": cust_data_json, 'create_new': create_new},
                        function (e) {

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