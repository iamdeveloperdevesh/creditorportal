$(document).ready(function () {



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
   debugger;
	var validNavigation = false;
	

    /* Attach the event keypress to exclude the F5 refresh (includes normal refresh)*/
    $(document).bind('keypress', function (e) {

        if (e.keyCode == 116) {
            validNavigation = true;
        }
    });


    /* Attach the event click for all links in the page*/
	$(document).on('click', 'a', function(event){
		debugger;
		if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
		  
	});
	
	$(document).on('click', 'button', function(event){
		debugger;
		if(event.currentTarget.getAttribute("id") == 'insert_proposal_button'){
			validNavigation = true;
		};
		 if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
		
		
	});
	
	$(document).on('click', 'input[type=button]', function(event){
		debugger;
		  if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
	});
	
	$(document).on('click', 'input[type=submit]', function(event){
		debugger;
		  if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
	});
	
    $("a").bind("click", function () {
debugger;
        if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
    });
	
	 /* Attach the event click for all button in the page*/
    $("button").bind("click", function () {
debugger;
if(event.currentTarget.getAttribute("id") == 'insert_proposal_button'){
			validNavigation = true;
		};
        if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
    });
	 $("input[type=button]").bind("click", function () {
		 
        if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}

    });
	
    /* Attach the event submit for all forms in the page*/
    $("form").bind("submit", function () {
		

        if(window.location.pathname != '/retail_enrollment'){
			validNavigation = true;
		}
    });
	
	
	 
	
	debugger;
	/* if(!validNavigation){
		 	 if(window.location.pathname == '/retail_enrollment'){
				 
				 if(conn){
					 alert();
				 }
				 
			var conn = new WebSocket('ws://abhiaxis.benefitz.in:8080');
conn.onopen = function(e) {
    console.log("Connection established!");conn.send($("#empIdHidden").val().trim());
};

conn.onmessage = function(e) {
    console.log(e.data);
};conn.onclose = function(e) {
   console.log(e.data);
};

		}
	 }*/

    
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