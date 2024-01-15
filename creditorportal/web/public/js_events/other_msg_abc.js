$(document).ready(function() {
    //alert(window.location.pathname);
    var lead_id = $("#lead_id_hidden").val();
    $(document).ajaxComplete(function(event, request, settings) {
        if (request.responseText == 'abc_session_timeout') {
            location.replace('/session_timeout');
        }
    });
    var validNavigation = false;
    $(document).bind('keypress', function(e) {
        //alert(e.keyCode);
        if (e.keyCode == 116) {
            validNavigation = true;
        }
    });
    $(document).on('click', 'a', function(event) {
        // debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $(document).on('click', 'button', function(event) {
        // debugger ;
        if (event.currentTarget.getAttribute("class") == 'ClickBtn') {
            validNavigation = true;
        }
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $(document).on('click', 'input[type=button]', function(event) {
        // debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $(document).on('click', 'input[type=submit]', function(event) {
        //debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $("a").bind("click", function() {
        //debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $("button").bind("click", function() {
        //debugger ;
        if (event.currentTarget.getAttribute("class") == 'ClickBtn') {
            validNavigation = true;
        }
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $("input[type=button]").bind("click", function() {
        //debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $("form").bind("submit", function() {
        //debugger ;debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }

    });
    $("input[type=submit]").bind("click", function() {
        //debugger ;
        if (window.location.pathname != '/comprehensive_products') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_proposer_detail') {
            validNavigation = true;
        }else if (window.location.pathname != '/member_review') {
            validNavigation = true;
        }else if (!window.location.pathname.search('/generate_quote')) {
            validNavigation = true;
        }
    });
    window.onbeforeunload = function() {
        //debugger ;
        if (!validNavigation) {
            var ua = window.navigator.userAgent;
            var isIE = /MSIE|Trident/.test(ua);
            if (isIE) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "/close_browser_dropoff_action_abc",
                });
            } else {
                navigator.sendBeacon('/close_browser_dropoff_action_abc');                
            }
        }
    }; 
});
