$(document).ready(function () {
	
	  /*code for checking session exist or not start*/
    $(document).ajaxComplete(function (event, request, settings) {
        if (request.responseText == 'tele_session_timeout') {
            location.replace('/login');
        }
    });
    /*code for checking session exist or not end*/
	

});