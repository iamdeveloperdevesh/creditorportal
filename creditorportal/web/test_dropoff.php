<!DOCTYPE html>
<script type="text/javascript">
var status = false;
window.onbeforeunload = function (event) {
	status = true;
	console.log("1")
	debugger;
    var message = 'Important: Please click on \'Save\' button to leave this page.';
    if (typeof event == 'undefined') {
        event = window.event;
    }
    if (event) {
        event.returnValue = message;
    }
    return message;
};

</script>
<?php 
echo RAND();
?>