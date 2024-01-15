<?php
//Get Login user details
$ludata = array();
$ludata['utoken'] = $_SESSION['webpanel']['utoken'];
$ludata['id'] = $_SESSION['webpanel']['employee_id'];
$luserDetails = curlFunction(SERVICE_URL . '/api/getLoginUserDetails', $ludata);
$luserDetails = json_decode($luserDetails, true);
//echo "here<pre>";print_r($luserDetails);exit;
//echo $luserDetails['Data']['user_data'][0]['employee_id'];exit;
$last_login = date("d-m-Y H:i:s A", strtotime($luserDetails['Data']['user_data'][0]['last_login']));
$logo=$_SESSION['webpanel']['creditor_logo'];
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Elephant.in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&style=outlined" rel="stylesheet">
    <!-- <style type="text/css">
        @font-face {
            font-family: 'Titillium';
            src: url(assets/fonts/TitilliumWeb-Regular.ttf);
        }
    </style> -->

    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css', PROTOCOL); ?>">


    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.ico">
    <!-- Flag icon-->
    <?php /**?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/flag-icon.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/icofont.css">
    link href="<?php echo base_url(); ?>assets/css/daterangepicker.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.v3.3.6.js"></script>
    <?php **/ ?>
    <!-- ico-font-->
    <link href="<?php echo base_url('assets/css/jquery-ui.css', PROTOCOL); ?>" rel="stylesheet">

    <link href="<?php echo base_url('assets/css/noty_theme_default.css', PROTOCOL); ?>" rel="stylesheet">

    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/themify-icons.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/metisMenu.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/owl.carousel.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/slicknav.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/typography.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/default-css.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/styles.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/responsive.css', PROTOCOL); ?>">

    <link href="<?PHP echo base_url('assets/css/jquery.dataTables.css', PROTOCOL); ?>" rel="stylesheet" type="text/css">
    <link href="<?PHP echo base_url('assets/css/jquery.noty.css', PROTOCOL); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo base_url('assets/css/select2.css', PROTOCOL); ?>" type="text/css" rel="stylesheet" />

    <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-material-datetimepicker.css', PROTOCOL); ?>" />

    <link href="<?php echo base_url('assets/css/bootstrap-datetimepicker.min.css', PROTOCOL); ?>" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-2.1.4.min.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/plugins/jquery-ui.custom.min.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/essential-plugins.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui.js', PROTOCOL); ?>"></script>

    <!-- CK Editor plugins -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/canvasjs.min.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/chart.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/ckeditor/ckeditor.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/ckeditor/adapters/jquery.js', PROTOCOL); ?>"></script>

    <script src="<?php echo base_url('assets/js/jquery.form.js', PROTOCOL); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.validate.js', PROTOCOL); ?>"></script>
    <script src="<?php echo base_url('assets/js/additional-methods.js', PROTOCOL); ?>"></script>

    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.colorbox.js', PROTOCOL); ?>"></script>

    <!-- Datatable plugin-->
    <script type="text/javascript" src='<?PHP echo base_url('assets/js/jquery.dataTables.min.js', PROTOCOL); ?>'></script>
    <script type="text/javascript" src='<?PHP echo base_url('assets/js/datatable.js', PROTOCOL); ?>'></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/jquery.noty.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/select2.min.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/moment-with-locales.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/bootstrap-material-datetimepicker.js', PROTOCOL); ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>

    <script>
        function setTabIndex() {
            var tabindex = 1;
            $('input,select,textarea,.icon-plus,.icon-minus,button,a').each(function() {
                if (this.type != "hidden") {
                    var $input = $(this);
                    $input.attr("tabindex", tabindex);
                    tabindex++;
                }
            });
        }

        $(function() {
            setTabIndex();
            $(".select2").each(function() {
                $(this).select2({
                    placeholder: "Select",
                    allowClear: true
                });
                $("#s2id_" + $(this).attr("id")).removeClass("searchInput");
            });

            $(".dataTables_filter input.hasDatepicker").change(function() {
                /* Filter on the column (the index) of this element*/
                oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $(".searchInput").index(this)));
            });

            window.scrollTo(0, 0);
        });

        function displayMsg(type, msg, timeout = 5000) {
            $.noty({
                text: msg,
                layout: "topRight",
                type: type,
                timeout: timeout
            });
        }
    </script>
</head>
<style>
    /* NEW UI START */
    .user-profile{
        margin-right: 0;
    }

    .mobileUser_option{
        display: none;
    }

    /* Loader Css Start */

    .sk-circle-wrapper {
        background: rgb(57 57 57);
        opacity: 0.7;
        width: 100%;
        height: 100%;
        text-align: center;
        vertical-align: middle;
        position: fixed;
        inset: 0px;
        margin: auto;
        font-size: 16px;
        z-index: 10;
        color: rgb(0, 0, 0);
    }

    .sk-circle {
        width: 120px;
        height: 120px;
        position: absolute;
        inset: 5% 0px 0px;
        margin: auto;
    }
    .sk-circle .sk-child {
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
    }
    .sk-circle .sk-child:before {
        content: '';
        display: block;
        margin: 0 auto;
        width: 15%;
        height: 15%;
        background-color:#2cd44a;;
        border-radius: 100%;
        -webkit-animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
        animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
    }
    .sk-circle .sk-circle2 {
        -webkit-transform: rotate(30deg);
        -ms-transform: rotate(30deg);
        transform: rotate(30deg); }
    .sk-circle .sk-circle3 {
        -webkit-transform: rotate(60deg);
        -ms-transform: rotate(60deg);
        transform: rotate(60deg); }
    .sk-circle .sk-circle4 {
        -webkit-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        transform: rotate(90deg); }
    .sk-circle .sk-circle5 {
        -webkit-transform: rotate(120deg);
        -ms-transform: rotate(120deg);
        transform: rotate(120deg); }
    .sk-circle .sk-circle6 {
        -webkit-transform: rotate(150deg);
        -ms-transform: rotate(150deg);
        transform: rotate(150deg); }
    .sk-circle .sk-circle7 {
        -webkit-transform: rotate(180deg);
        -ms-transform: rotate(180deg);
        transform: rotate(180deg); }
    .sk-circle .sk-circle8 {
        -webkit-transform: rotate(210deg);
        -ms-transform: rotate(210deg);
        transform: rotate(210deg); }
    .sk-circle .sk-circle9 {
        -webkit-transform: rotate(240deg);
        -ms-transform: rotate(240deg);
        transform: rotate(240deg); }
    .sk-circle .sk-circle10 {
        -webkit-transform: rotate(270deg);
        -ms-transform: rotate(270deg);
        transform: rotate(270deg); }
    .sk-circle .sk-circle11 {
        -webkit-transform: rotate(300deg);
        -ms-transform: rotate(300deg);
        transform: rotate(300deg); }
    .sk-circle .sk-circle12 {
        -webkit-transform: rotate(330deg);
        -ms-transform: rotate(330deg);
        transform: rotate(330deg); }
    .sk-circle .sk-circle2:before {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
        background-color:#0182ff;             }
    .sk-circle .sk-circle3:before {
        -webkit-animation-delay: -1s;
        animation-delay: -1s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle4:before {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle5:before {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
        background-color:#0182ff; }
    .sk-circle .sk-circle6:before {
        -webkit-animation-delay: -0.7s;
        animation-delay: -0.7s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle7:before {
        -webkit-animation-delay: -0.6s;
        animation-delay: -0.6s;
        background-color:#0182ff; }
    .sk-circle .sk-circle8:before {
        -webkit-animation-delay: -0.5s;
        animation-delay: -0.5s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle9:before {
        -webkit-animation-delay: -0.4s;
        animation-delay: -0.4s;
        background-color:#0182ff; }
    .sk-circle .sk-circle10:before {
        -webkit-animation-delay: -0.3s;
        animation-delay: -0.3s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle11:before {
        -webkit-animation-delay: -0.2s;
        animation-delay: -0.2s;
        background-color:#0182ff; }
    .sk-circle .sk-circle12:before {
        -webkit-animation-delay: -0.1s;
        animation-delay: -0.1s;
        background-color:#2cd44a; }

    @-webkit-keyframes sk-circleBounceDelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        } 40% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }
    }

    @keyframes sk-circleBounceDelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        } 40% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }
    }
    /* Loader CSS Start */


    .selectformflex .dropdown.singleselector_Card select.form-control{
        right: 0;
        width: 86%;
        margin: auto;
    }
    .selectformflex .dropdown.singleselector_Card .select2-container ul.select2-choices{
        border: none;
        background-image: none;
    }

    .select2-container-multi.select2-container-active .select2-choices {
        border: none;
        outline: none;
        box-shadow:none;
        background-image: none;
    }

    .select2-container .select2-choice .select2-arrow {

        border-left: none;
        background-clip: none;
        background: none;
        background-image: none;
        background-image: none;
        background-image: none;
        background-image: none;
    }
    .select2-container-multi .select2-choices .select2-search-choice {
        padding: 3px 5px 3px 5px;
        margin: 5px 0 3px 5px;
    }

    .select2-container-multi .select2-choices li.select2-search-choice div {
        font-size: 10px;
    }

    .selectformflex .dropdown.singleselector_Card .select2-container {
        top: -4px !important;
        border: none;
        right: 0;
        width: 86%;
        margin: auto;
        padding: 0;
    }
    .selectformflex .dropdown.singleselector_Card .select2-container .select2-choice > .select2-chosen {
        font-size: 11px !important;
        color: #808080 !important;
        font-weight: 600;
    }
    .selectformflex .dropdown.singleselector_Card .select2-container .select2-choice {
        border: none;
        background-image: none;
        box-shadow: none;
    }
    .selectformflex .dropdown.singleselector_Card select.form-control option{
        font-size: 14px;
    }
    .dateFltrIcon input {
        border: none;
        font-size: 11px !important;
        color: #808080 !important;
        font-weight: 600;
        font-family: inherit;
    }

    .dateFltrIcon input:focus-visible{
        outline: none;
    }



    /*  */
    .menu-lft {
        background: #fff;
        border-radius: 0px 15px 15px 0px;
        height: auto;
        box-shadow: 0px 10px 40px 0px rgb(220 220 220);
        border: 3px solid #a5a3a326;
        position: absolute;
        z-index: 3;
        top: 56px;
        transition: all .1s ease 0.3s;
        /* transition: all 0.8s ease 0s; */
    }
    .topNavbarDiv .navbar-toggler .navbar-toggler-icon {

        background-image: url("../assets/images/collapsedbtn.png");
    }


    .topNavbarDiv .navbar-toggler .navbar-toggler-icon {
        width: 36px;
        height: 31px;
    }
    .welcmpage_Wrapper .infoDiv {
        padding: 21px 21px;
        background: #025679;
    }
    .welcmpage_Wrapper .infoDiv h4 {
        color: #fff;
        font-weight: 600;
    }

    .welcmpage_Wrapper .infoDiv p {
        color: #fff;
        font-size: 14px;
        margin-top: 10px;
    }
    .welcmpage_Wrapper .absoluteimg img {
        position: absolute;
        width: 312px;
        right: 0;
        top: -27px;
    }
    .welcmpage_Wrapper .absoluteimg {
        position: relative;
    }
    /* .user-profile {
        margin-right: 0;
    } */


    .selectformflex .singleselector_Card {
        margin: 10.5px 11px 21px 1px;
        padding: 6px 30px 6px 17px;
        border-radius: 13px;
        /* box-shadow: 0 6px 51px 0 rgb(136 166 189 / 22%); */
        background-color: #fff;
        width: 19%;
        height: 72px;
        box-shadow: 1px 1px 2px 2px #e2dfdf;
        /* box-shadow: 1px 1px 2px 2px #f1f1f1; */
    }

    .singleselector_Card select {
        font-size: 11px !important;
        color: #808080 !important;
        border: none;
    }

    .singleselector_Card select.form-control{
        padding:0 !important;
    }
    .singleselector_Card select.form-control:not([size]):not([multiple]) {
        height: initial;
    }

    .singleselector_Card .form-control:focus {
        border: none;
        border-bottom: none;
    }
    .singleselector_Card .singleselect_subTitle {
        color: #9ba8ab;
        font-size: 12px;
        text-align: left;
        margin-bottom: 3px;
        font-family: "Poppins-Medium", system-ui;
        margin-left: 3px;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .selectformflex {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        /* width: 73%; */
        margin-top: 20px;
        /* padding-left: 30px; */
    }
    .cardtabsWrapper.container{
        margin-top:10px;

    }
    .performance_Wrapper.container {
        margin-top: 20px;
    }

    .zxo1{
        align-items:center;
    }

    /* .mapDiv {
        margin-top: 67px;
    } */

    .dateR_picker .date_picker input {
        border: none;
        width: 100%;
        font-size: 11px !important;
        color: #808080 !important;
        font-weight: 600;
        font-family: inherit;
    }
    .dateR_picker .date_picker.form-control {
        display: flex;
        align-items: center;
        border: none;
        padding: 0;
    }

    .cardtabsWrapper .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: #025679;
        border-radius: 30px;
        color: #fff;
        padding: 6px 18px !important;
        margin-right: 5px;
        font-size: 13px;
    }

    .cardtabsWrapper .nav-pills .nav-link {
        color: #025679;
        padding: 6px 18px !important;
        margin-right: 5px;
        font-size: 13px;
    }
    .title_performer {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        padding: 1px 20px 1px 28px;
    }

    .horizontal-main-wrapper .main-content-inner{
        padding: 0 0 0px !important;
    }
    div#chartContainer2 {
        margin-top: 81px;
        padding-top: 18px;
    }
    .dateFltrIcon img {
        width: 31px;
    }
    span.zxo3 {
        position: absolute;
        left: 26px;
    }
    span.zxo2 {
        position: relative;
        z-index: 1;
        color: #025679;
        font-weight: 700;
    }
    span.zxo3 img {
        width: 34px;
    }
    .titleTxt {
        display: flex;
        align-items: center;
    }
    .userInfo_card {
        margin: 12px 10px;
        border-radius: 20px;
        box-shadow: 0px 1px 9px 1px rgb(189 213 218 / 57%);
        /* background-color: #fff; */
        padding: 11px 11px 19px 11px;
        width: 31%;
    }
    input#daterange:focus-visible {
        border: none;
        outline: none;
    }

    .userInfo_card ul {
        margin-bottom: 1px;
        padding: 1px;
    }

    .userInfo_card ul li {
        display: flex;
        justify-content: space-between;
        line-height: inherit;
        align-items: center;
        margin-bottom: 7px;
    }

    .userInfo_card ul li span:first-child {
        /* font-size: 11px; */
        /* font-weight: 600; */
    }


    .userInfo_card ul li span:nth-child(2) {
        text-align: right !important;
        /* font-size: 11px;
        font-size: 13px; */
    }

    .userInfo_card ul li span:nth-child(2) label {
        white-space: nowrap;
        width: 82px;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0px;
    }

    /* .cardtabsWrapper .tab-content .tab-pane {
        display: flex;
    } */
    .tabconatiner_wrapper {
        display: flex;
    }
    .tabconatiner_wrapper::-webkit-scrollbar {
        display: none !important;
    }
    .tabconatiner_wrapper .cardBody::-webkit-scrollbar {
        width: 4px;
        background: #025679;
        height: 6px;
    }
    .tabconatiner_wrapper .cardBody {
        overflow: hidden;
        overflow-x: auto;
        display: -webkit-inline-box;
        width: -webkit-fill-available;
        padding-top: 7px;
    }
    .tabconatiner_wrapper .cardBody::-webkit-scrollbar-thumb {
        background: #025679;
        border-radius: 30px;
    }

    .userInfo_card  .cardHeader img {
        width: 48px;
        margin-bottom: 24px;
    }
    .userInfo_card  .cardHeader h2 {
        font-weight: 900;
        font-size: 15px;
        margin-bottom: 14px;
    }

    .cardtabsWrapper {
        /* display: flex; */
        /* align-items: center; */
    }
    .statusTable_wrapper {
        border-radius: 15px;
    }
    .userInfo_card .card_bgColor {
        background-color: #fff;
    }
    .status_tables {
        padding-bottom: 6px;
        margin-top: 8px;
        padding-top: 12px;
    }
    .status_tables .table-responsive {
        overflow-x: auto !important;
        overflow-y: auto;
        height: 310px !important;
    }
    .tableStatus_info thead {
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .status_tables .table thead th:first-child {
        border-radius: 13px 0px 0px 13px;
    }
    .status_tables .tableStatus_info thead th {
        border: none;
        border-bottom: none !important;
        text-align: center;
        white-space: nowrap;
        border-right: 1px solid #ced7e1;
    }
    .status_tables .tableStatus_info tbody tr td {
        border-top: none;
        border-bottom: 1px solid #f0f0f0;
        font-size: 12px;
        text-align: center;
        white-space: nowrap;
        color: #0e0e58;
        font-weight: 600;
        vertical-align: middle;
        border-right: 1px solid #ced7e1;
    }
    .status_tables .theadbg {
        background-color: #025679;
    }
    .status_tables .tableStatus_info thead th span {
        /* border-right: 1px solid #ced7e1; */
        display: block;
        margin: 1px 1px;
        color: #fff;
        font-size: 13px;
        padding: 1px 0px;
        font-weight:200;
    }
    .status_tables table.tableStatus_info{
        margin-bottom: 0px;
    }
    .pageFooterSingle .page-item:first-child .page-link {
        border: none;
    }

    .pageFooterSingle .pagination .page-item .page-link.active {
        margin: 3.9px 7px 3.9px 12px;
        padding: 6px 12px 5px 13px;
        box-shadow: 0 6px 9px 0 rgb(0 75 131 / 14%);
        background-color: #fff;
        border-radius: 30px;
    }
    .pageFooterSingle .pagination .page-item .page-link {
        /* border: none; */
        box-shadow: none;
    }
    .pageFooterSingle ul.pagination.justify-content-end li a {
        color: #4f6781;
        font-family: "poppins-SemiBold", sans-serif;
        font-size: 13px;
        font-weight: 600;
        margin: 3.9px 5px 3.9px 5px;
        /* padding: 6px 12px 5px 13px; */
        border-radius: 30px;
        border: 1px solid #eef0f2;
    }
    .pageFooterSingle .pagination .page-item:last-child .page-link {
        margin: 0 0 0px 9px;
        padding: 15px 10.9px 14.3px 11px;
        opacity: 0.73;
        border-radius: 14.5px;
        background-color: rgba(189, 213, 218, 0.4);
        border: none;
    }
    .pageFooterSingle {
        margin-top: 15px;
        padding-bottom: 15px;
        padding-right: 30px !important;
    }
    .status_tables .table-responsive::-webkit-scrollbar {
        width: 4px;
        background: #96a4b5;
        border-radius: 30px;
        height: 6px;
    }

    img.welcomeLogo {
        height: 40px;
    }
    .navTabContainerFlex {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .moreFilter .label1 {
        color: #2e8ded;
        font-weight: 600;
        font-size:12px;
    }
    .moreFilter img {
        width: 31px;
    }

    .filterOption .modal-header {
        flex-direction: row-reverse;
    }

    .filterOption .modal-title {
        color: #012b3d;
        font-weight: 700;
    }

    .moreFilter .dropdown .dropdown-item {
        font-size: 14px;
        padding: 0;
        text-align: center;
    }
    .moreFilter .dropdown-menu {
        min-width: 8rem;
    }
    .selectformflex .dropdown.singleselector_Card .dropdown-toggle:focus {
        outline: none;
        box-shadow: none;
        border: none;
    }
    .selectformflex .dropdown.singleselector_Card .dropdown-toggle {
        background: none;
        padding: 0;
        font-size: 11px !important;
        color: #808080 !important;
        border: none;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .selectformflex .dropdown.singleselector_Card .dropdown-menu {
        top: 13px !important;
        border-radius: 5px;
        padding-top: 0;
        padding-bottom: 0;
    }



    .selectformflex .dropdown.singleselector_Card .dropdown-menu .dropdown-item:hover {
        color: #ffffff;
        text-decoration: none;
        background-color: #025679;
    }

    .selectformflex .dropdown.singleselector_Card .dropdown-menu .dropdown-item:focus{
        color: #ffffff;
        text-decoration: none;
        background-color: #025679;
    }

    div#chartdiv {
        height: 330px !important;
        overflow: hidden !important;
        text-align: left !important;
        margin: 12px 10px;
        border-radius: 20px;
        box-shadow: 0px 1px 9px 1px rgb(189 213 218 / 57%);
        padding: 11px 11px 19px 11px;
        position: relative;
        background: #025679;
    }

    img.zoomerModalbtn {
        position: absolute;
        width: 25px;
        bottom: 17px;
        left: 31px;
        background: #ebf0f2;
        border-radius: 30px;
        z-index: 1;
    }
    div#FilterModal .selectformflex {
        width: 100%;
    }

    .mapModalBox .modal-content{
        background: #012b3d;
    }

    .mapModalBox .modal-header .modal-title{
        color: #fff;
    }

    .mapModalBox .modal-header button.close{
        color: #fff;
        opacity: 1;
    }

    /* NEW UI CSS END  */

    .dtp-buttons>button.btn {
        border: none;
        border-radius: 2px;
        position: relative;
        box-shadow: none;
        color: rgba(0, 0, 0, 0.87);
        padding: 5px 16px;
        font-size: 12px;
        margin: 10px 1px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0;
        will-change: box-shadow, transform;
        transition: box-shadow 0.2s cubic-bezier(0.4, 0, 1, 1), background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        outline: 0;
        cursor: pointer;
        text-decoration: none;
        background: transparent;
    }

    .dtp-buttons>button.btn:hover,
    .dtp-buttons>button.btn:focus {
        background-color: rgba(153, 153, 153, 0.2);
    }

    .dtp {
        overflow-y: auto !important;
    }

    .dtp>.dtp-content>.dtp-date-view>header.dtp-header,
    .dtp div.dtp-date,
    .dtp div.dtp-time,
    .dtp table.dtp-picker-days tr>td>a.selected {
        background: #6b3031 !important;
    }

    .error {
        color: red;
    }
    .desktopVisible{
        display:none;
    }
    .quickfilterMobile{
        display:none;
    }
    .mobile_cardWrapView01{
        display: none;
    }
    .singleS_dd span.zxo2 {
        position: relative;
        z-index: 1;
        color: #025679;
        font-weight: 700;
        left: -14px;
    }
    .singleS_dd.container {
        padding: 2px 31px;
        position: relative;
    }


    .selectformflex::-webkit-scrollbar {
        width: 8px;
        width: 4px;
        background: #025679;
        height: 6px;
    }
    .singleS_dd.container .title_performer{
        margin-top: 18px
    }
    .filtrtgl{
        margin-left: 8px;
        background: #025679;
        padding: 4px 6px;
        border-radius: 30px;
        color: #fff;
    }


    .singleS_dd.container .title_performer{
        display: none;
    }

    @media screen and (min-width: 1200px){

        .navbar-collapse.collapse {
            display: block;
            margin-left: -440px;

            transition: all .1s ease 0.1s;
        }
        .collapse.show {
            display: block;
            margin-left: 0;

            transition: all .1s ease 0.3s;
        }
    }


    /* Mobile Respnsive Css Sart */

    @media screen and (max-width: 576px){
        .selectformflex .singleselector_Card {
            width: 69%;
        }

        .selectformflex {
            justify-content: space-between;
            flex-wrap: nowrap;
            margin-top: 0px;
            overflow: hidden;
            overflow-x: auto;
            display: -webkit-inline-box;
            width: -webkit-fill-available;
            padding-top: 7px;
        }

        .singleS_dd.container .title_performer{
            display: block;
        }
        .mobileUser_option {
            display: block;
            margin-top: 2px;
        }
        .singleS_dd.container{
            margin-top: 22px;
        }

        .mobileUser_option a.dropdown-item {
            padding: 10px 20px;
        }

        .mobileUser_option .dropdown-menu.show{
            transform: translate3d(-87px, 38px, 0px);
        }

        img.logoutIcon {
            width: 36px;
            margin-right: 12px;
        }
        img.userProfileicon {
            width: 36px;
            margin-right: 11px;
        }
        img.welcomeLogo {
            height: 40px;
        }
        button.navbar-toggler{
            padding: 5px 1px;
        }
        .mobileUser_option .icon{
            width: 34px;
        }
        .user-profile{
            display: none;
        }
        .mobile_cardWrapView01{
            display:block;
        }
        #QuickFilterModal .filterOption .modal-header {
            padding: 3px;
        }

        /* .selectformflex{
            display: none;
        } */
        /* .selectformflex .singleselector_Card{
            width:100%;
        } */
        /* .selectformflex {
            height: 210px;
            overflow-x: hidden;
            overflow-y: auto;
            margin: 12px 10px;
            border-radius: 20px;
            box-shadow: 0px 1px 9px 1px rgb(189 213 218 / 57%);
            padding: 11px 11px 19px 11px;
        } */

        .userInfo_card {
            width: fit-content;
        }
        .Quickselectformflex {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 20px;
            flex-direction: column;
        }
        .quickWrap {
            display: flex;
            align-items: center;
        }

        .quickWrap .singleselect_subTitle{
            width:50%;
        }

        .Quickselectformflex .dropdown.singleselector_Card .select2-container .select2-choice {
            border: none;
            background-image: none;
            box-shadow: none;
        }

        .Quickselectformflex .dropdown.singleselector_Card .select2-container ul.select2-choices {
            border: none;
            background-image: none;
        }
        .Quickselectformflex .dropdown.singleselector_Card .select2-container {
            top: -4px !important;
            border: none;
            right: 0;
            width: 86%;
            margin: auto;
            padding: 0;
        }
        .QuicktitleTxt{
            display: flex;
            align-items: center;
        }
        .QuicktitleTxt span.zxo2 {
            position: relative;
            z-index: 1;
            color: #000000;
            font-weight: 700;
            font-size: 20px;
            left: 11px;
        }

        #QuickFilterModal .modal-content {
            padding: 10px 2px;
        }

        #QuickFilterModal .modal-header button.close{
            color: black;
            opacity: 1;
        }

        .QuicktitleTxt span.zxo3 {
            position: absolute;
            left: 8px;
        }

        #QuickFilterModal .modal-footer button{
            width: 100%;
            border-radius: 8px;
            background: #025679;
        }

        .QuicktitleTxt span.zxo3 img {
            width: 54px;
        }

        .Quickselectformflex .quickWrap select {
            margin: 10.5px 1px 21px 1px;
            padding: 16px 4px 16px 4px;
            border-radius: 13px;
            background-color: #fff;
            box-shadow: 1px 1px 2px 2px #e2dfdf;
            border: none;
            width: 50%;
        }

        .Quickselectformflex .singleselector_Card {
            margin: 6px 1px 11px 1px;
            padding: 6px 8px 6px 8px;
            background-color: #fff;
            height: 72px;
            width: 100%;
            box-shadow: 0px 2px 7px rgba(79, 111, 140, 0.26);
            border-radius: 10px;
        }

        .quickWrap .select2-container-multi {
            border-radius: 13px;
            background-color: #fff;
            box-shadow: 1px 1px 2px 2px #e2dfdf;
            border: none;
            width: 50%;
            margin: 10.5px 1px 21px 1px;
            padding: 9px 4px 9px 4px;
        }
        .quickWrap .select2-container-multi .select2-choices {
            height: auto !important;
            margin: 0;
            padding: 0 5px 0 0;
            position: relative;
            border: none;
            cursor: text;
            overflow: hidden;
            background-color: #fff;
            background-image: none;
        }

        span.zxo2 {
            left: -10px;
            font-size: 11px;
        }
        .quickfilterMobile {
            display: block;
            border: 1px solid #efefef;
            padding: 3px 6px;
            border-radius: 7px;
        }
        .title_performer {
            padding: 1px 12px 1px 18px;
        }
        span.zxo3 {
            left: 10px;
        }
        .cardtabsWrapper {
            margin-top: 20px;
        }
        .cardtabsWrapper .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
            padding: 4px 12px !important;
            margin-right: 2px;
            font-size: 11px;
        }
        .cardtabsWrapper .nav-pills .nav-link {
            color: #025679;
            padding: 4px 12px !important;
            margin-right: 2px;
            font-size: 11px;
        }
        .navTabContainerFlex .nav-pills {
            flex-wrap: nowrap;
        }

        .dateFltrIcon input {
            font-size: 8px !important;
            width: 90%;
            padding-left: 21px;
        }
        .quickWrap .dateFltrIcon {
            display: flex;
            justify-content: space-around;
            margin: 10.5px 1px 21px 1px;
            padding: 19px 4px 19px 4px;
            border-radius: 13px;
            background-color: #fff;
            box-shadow: 1px 1px 2px 2px #e2dfdf;
            border: none;
            width: 50%;
        }

        .dateFltrIcon {
            display: flex;
            justify-content: space-around;
        }
        label.mobile_search_data {
            font-weight: 600;
            margin-bottom: 0;
        }
        .morefilterDesktop {
            display: none;
        }
        .QuickFilter .label1 {
            color: #040404;
            /* font-weight: 600; */
            font-size: 10px;
            margin-right: 3px;
        }

        .QuickFilter img {
            width: 31px;
        }

        .performance_Wrapper .canvas_wrapper {
            margin-bottom: 35px;
            padding: 2px 10px;
            border-radius: 11px;
            margin-top: 16px;
            box-shadow: 0px 0px 10px rgb(143 172 178 / 35%);
        }
        .performance_Wrapper .table_wrapper {
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 11px;
            margin-top: 16px;
            box-shadow: 0px 0px 10px rgb(143 172 178 / 35%);
        }
        .PerformanceStatus_Mbl {
            margin-top: 20px;
        }
        .display-none-sm {
            display: block;
        }
        .desktopVisible{
            display:block;
        }
        /* .mobileVisibile{
            display:none;
        }		 */

        .performanceCard {
            column-count: 2;
            background: #E4E5E554;
            border-radius: 12px;
            padding: 18px 1px;
            margin-bottom: 10px;
            column-gap: 1px;
        }
        .performanceCard ul li {
            display: flex;
            justify-content: space-between;
            line-height: inherit;
            align-items: center;
            margin-bottom: 7px;
            padding: 2px 8px;
            font-size: 9px;
        }
        .performanceCard ul:first-child {
            border-right: 1px solid #7e7e7e;
            padding: 0px 12px;
        }
        .performanceCard ul:nth-child(2){
            padding: 0px 12px;
        }
        .viewMore {
            background: #FFFFFF;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.25);
            border-radius: 13px;
            position: absolute;
            width: 28%;
            margin: auto;
            text-align: center;
            left: 0;
            right: 0;
            bottom: -8px;
            padding: 2px 4px;
        }
        .welcmpage_Wrapper .infoDiv {
            padding: 21px 18px;
        }
    }
    /* Mobile Respnsive Css End */

</style>
<?php $this->load->view('template/custom_customer-portal-style.php');?>
<body class="body-bg">
<!-- preloader area start -->
<div id="preloader">
    <div class="loader"></div>
</div>
<!-- preloader area end -->
<!-- main wrapper start -->
<div class="horizontal-main-wrapper">
    <!-- main header area start -->
    <!-- <div class="mainheader-area">
			<div class="container con-left">
				<div class="row align-items-center">
					<div class="col-md-3 col-9">
						<div class="logo">
							<a href="<?php echo base_url('home', PROTOCOL); ?>"><img src="<?php //echo base_url('assets/images/logo.png', PROTOCOL); ?>" alt="logo"></a>
						</div>
					</div>
					<div class="col-md-9 col-1 display-none-sm">
						<div class="nav-listing" id="nav-top">
							<a href="#home" class="active">PROTECTING <i class=" fa fa-angle-down"></i></a>
							<a href="#news">INVESTING <i class=" fa fa-angle-down"></i></a>
							<a href="#contact">FINANCING <i class=" fa fa-angle-down"></i></a>
							<a href="#about">ADVISING <i class=" fa fa-angle-down"></i></a>
						</div>
					</div>
					<div class="col-3 col-md-9 text-right">
						<a href="javascript:void(0);" class="icon display-none-lg sidebar-icon" onclick="open_side_mob()">
							<i class="fa fa-bars"></i>
						</a>
					</div>
				</div>
			</div>
		</div> -->
    <!-- main header area end -->
    <!-- header area start -->
    <!-- header-area header-bottom -->
    <div class="mt-3 mb-3">
        <div class="container pad-left-lg mb-1">
            <div class="row">
                <div class="col-md-8 col-lg-8 col-9 text-left topNavbarDiv">
                    <!-- Navbar Collapsed button -->
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <!-- Navbar Collapsed button -->

                    <!-- <span class="brand-name">Fyntune</span> -->
                    <?php
                    if($logo == "" || is_null($logo)){ ?>
                        <a href="<?php echo base_url('home', PROTOCOL); ?>"><img class="welcomeLogo" src="<?php echo base_url('assets/images/logo.png', PROTOCOL); ?>" alt="logo"></a>
                    <?php  }else{ ?>
                        <a href="<?php echo base_url('home', PROTOCOL); ?>"><img class="welcomeLogo" src="<?php echo base_url($logo); ?>" alt="logo"></a>
                    <?php     }
                    ?>

                </div>
                <div class="col-md-4 col-lg-4  col-3">
                    <div class="dropdown mobileUser_option">
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <img class="icon" src="../assets/images/userNew.jpeg">
                            <!-- <span class="info_txt user_name"> Admin </span>  -->
                        </a>
                        <div class="dropdown-menu" x-placement="bottom-start" style="">
                            <a class="dropdown-item" href="#">
                                <img class="userProfileicon" src="../assets/images/userNew.jpeg">
                                <span class=""> Admin </span>
                            </a>
                            <a class="dropdown-item" href="#">
                                <img class="logoutIcon" src="../assets/images/logoutNew1.jpeg">
                                <span class=""> Logout </span>
                            </a>
                        </div>
                    </div>
                    <div class="user-profile pull-right">
                        <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><span class="display-none-sm display-ipadpro"><?php echo $_SESSION['webpanel']['employee_fname'] . ' ' . $_SESSION['webpanel']['employee_lname']; ?>(<?php echo $_SESSION['webpanel']['role_name']; ?>) <br />Last Login: (<?php echo $last_login; ?>)</span><span></span> <i class="fa fa-angle-down" style="color:#000;"></i></h4>
                        <div class="dropdown-menu">
                            <div class="display-none-lg">
                                <?php if (in_array('SaleAdminDashboard', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>saleadmindashboard">Sales Admin Dashboard</a>
                                <?php } ?>
                                <?php if (in_array('ApplicationLogs', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>applicationlogs">Application Logs</a>
                                <?php } ?>
                                <?php if (in_array('BulkUpload', $this->RolePermission)) { ?>
                                    <li><a href="<?php echo base_url(); ?>bulkUpload"><i class="ti-dashboard"></i> <span>Bulk Upload</span></a></li>
                                <?php } ?>
                                <?php if (in_array('SmDashboard', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>smdashboard">SM Dashboard</a>
                                <?php } ?>
                                <?php if (in_array('cdbalance', $this->RolePermission)) { ?>
                                    <li><a href="<?php echo base_url(); ?>dashboarddetails/cdBalance"><i class="ti-money"></i> <span>CD Balance</span></a></li>
                                <?php } ?>

                                <?php if (in_array('MyProfile', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>myprofile/addEdit">Profile</a>
                                <?php } ?>
                                <?php if (in_array('EnrollmentFormList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>enrollmentforms">Enrollment Forms</a>
                                <?php } ?>
                                <?php if (in_array('PermissionList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>permission">Permissions</a>
                                <?php } ?>
                                <?php if (in_array('RoleList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>roles">Roles</a>
                                <?php } ?>
                                <?php if (in_array('UserList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>users">Users</a>
                                <?php } ?>
                                <?php if (in_array('CreditorList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>creditors">Partners</a>
                                <?php } ?>
                                <?php if (in_array('CreditorBranchList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>creditorbranches">Partner Branches</a>
                                <?php } ?>
                                <?php if (in_array('SMCreditorMappingList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>smcreditors">SM Partner Mapping</a>
                                <?php } ?>
                                <?php if (in_array('LocationList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>locationmst">Location</a>
                                <?php } ?>
                                <?php if (in_array('DiscrepancyTypeList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>discrepancytype">Discrepancy Type</a>
                                <?php } ?>
                                <?php if (in_array('DiscrepancySubTypeList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>discrepancysubtype">Discrepancy Subtype</a>
                                <?php } ?>
                                <?php if (in_array('AssignmentDeclarationList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>assignmentdeclaration">Declaration</a>
                                <?php } ?>
                                <?php if (in_array('FamilyConstructList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>familyconstruct">Family Construct</a>
                                <?php } ?>
                                <?php if (in_array('InsurerList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>insurer">Insurer</a>
                                <?php } ?>
                                <?php if (in_array('PolicySubtypeList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>policysubtype">Policy Subtype</a>
                                <?php } ?>
                                <?php if (in_array('SumInsuredList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>suminsured">Sum Insured</a>
                                <?php } ?>
                                <?php if (in_array('ProductsList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>products">Products</a>
                                <?php } ?>

                                <?php if (in_array('LeadList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>customerleads">Leads</a>
                                <?php } ?>
                                <?php if (in_array('CustomerProposalsList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>customerproposals">Customer Proposals</a>
                                <?php } ?>
                                <?php if (in_array('DiscrepancyProposalList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>discrepancyproposals">Discrepancy Proposals</a>
                                <?php } ?>
                                <?php if (in_array('BOProposalList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>boproposals">BO Proposals</a>
                                <?php } ?>
                                <?php if (in_array('COProposalList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>coproposals">CO Proposals</a>
                                <?php } ?>
                                <?php if (in_array('UWProposalList', $this->RolePermission)) { ?>
                                    <a class="dropdown-item" href="<?php echo base_url(); ?>uwproposals">UW Proposals</a>
                                <?php } ?>

                            </div>
                            <a class="dropdown-item" href="<?php echo base_url("home/logout"); ?>">Log Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- header area end -->
    <?php if (uri_string() == "policyproposal/addedit" || uri_string() == "policyproposal/preview") : ?>
        <div class="header-pre sticky-pre" id="myHeader-pre">
            <div class="row">
                <div class="col-md-6 col-12 mb-2 text-left un-id">Unique ID : <span class="id-txt" id="unique_trace_id">6700</span></div>
                <div class="col-md-6 col-12"><span class="premium-top dropdown-toggle pre-credital" data-toggle="dropdown" aria-expanded="true">Premium<i style="font-size: 10px;"> Tax Inclusive</i> : <i class="fa fa-inr"></i><span class="total_premium" id="total_premium">0</span></span>
                    <div id="premium_calculations_data" class="dropdown-menu drop_prem" x-placement="bottom-start" style="position: absolute; transform: translate3d(580px, 32px, 0px) !important; will-change: transform; left: 7% !important; overflow-y: auto;height: auto;max-height: 350px;">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- page title area end -->
    <div class="main-content-inner" style="min-height: calc(100vh - 100px);">
        <div class="">
            <div class="row">
                <!-- Left Menu Start-->
                <div class="collapse navbar-collapse col-md-2 mt-2 pd-right menu-lft" id="collapsibleNavbar">
                    <div class="main-menu">
                        <div class="menu-inner">
                            <nav>
                                <ul class="metismenu mt-3 display-none-sm" id="menu">

                                    <?php if (in_array('SaleAdminDashboard', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>saleadmindashboard"><i class="ti-dashboard"></i> <span>Sales Admin Dashboard</span></a></li>
                                    <?php } ?>

                                    <?php if (in_array('ApplicationLogs', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>applicationlogs"><i class="ti-book"></i> <span>Application Logs</span></a></li>
                                    <?php } ?>
                                    <?php if (in_array('BulkUpload', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>bulkUpload"><i class="ti-dashboard"></i> <span>Bulk Upload</span></a></li>
                                    <?php } ?>
                                    <?php if (in_array('cdbalance', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>dashboarddetails/cdBalance"><i class="ti-money"></i> <span>CD Balance</span></a></li>
                                    <?php } ?>

                                    <?php if (in_array('CoverLimit', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>dashboarddetails/coverbalance"><i class="ti-money"></i> <span>Cover Limit</span></a></li>
                                    <?php } ?>
                                    <?php if (in_array('Claim', $this->RolePermission)) { ?>
                                        <li class="">
                                            <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-tab"></i><span>Claim</span></a>
                                            <ul class="collapse">
                                                <?php if (in_array('LodgeClaims', $this->RolePermission)) { ?>
                                                    <li class=""><a href="<?php echo base_url(); ?>Lodgeclaim"><span>Lodge Claims</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('TrackClaims', $this->RolePermission)) { ?>
                                                    <li class=""><a href="<?php echo base_url(); ?>Trackclaim"><span>Track Claims</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('RaiseBulkClaims', $this->RolePermission)) { ?>
                                                    <li class=""><a href="<?php echo base_url(); ?>Raisebulkupload"><span>Raise Bulk Claims</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('Reports', $this->RolePermission)) { ?>
                                                    <li class=""><a href="<?php echo base_url(); ?>Reports"><span>Reports</span></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>

                                    <?php } ?>
                                    <?php if (in_array('SmDashboard', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>smdashboard"><i class="ti-dashboard"></i> <span>SM Dashboard</span></a></li>
                                    <?php } ?>

                                    <?php if (in_array('MyProfile', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>myprofile/addEdit"><i class="ti-face-smile"></i> <span>Profile</span></a></li>
                                    <?php } ?>
                                    <?php if (in_array('EnrollmentFormList', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>enrollmentforms"><i class="ti-file"></i><span>Enrollment Forms</a></span></li>
                                    <?php } ?>
                                    <?php if (in_array('SingleJourney', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>singlejourney"><i class="ti-file"></i><span>Single Journey</a></span></li>
                                    <?php } ?>
                                    <?php if (in_array('CommunicationTemplate', $this->RolePermission)) { ?>
                                            <li><a href="<?php echo base_url(); ?>communicationtemplate"><i class="ti-file"></i><span>Communication Template</a></span></li>
                                        <?php } ?>
                                    <?php if (in_array('PermissionList', $this->RolePermission) || in_array('RoleList', $this->RolePermission) || in_array('UserList', $this->RolePermission)) { ?>

                                        <li class="">
                                            <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-tab"></i><span>UAC</span></a>
                                            <ul class="collapse">
                                                <?php if (in_array('PermissionList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>permission"><span>Permissions</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('RoleList', $this->RolePermission)) { ?>
                                                    <li><a href="<?php echo base_url(); ?>roles"><span>Roles</a></span></li>
                                                <?php } ?>
                                                <?php if (in_array('UserList', $this->RolePermission)) { ?>
                                                    <li><a href="<?php echo base_url(); ?>users"><span>Users</span></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>


                                    <?php if (in_array('CreditorList', $this->RolePermission) || in_array('CreditorBranchList', $this->RolePermission)) { ?>
                                        <li class="">
                                            <a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i><span>Partner Management</span></a>
                                            <ul class="collapse">
                                                <?php if (in_array('CreditorList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>creditors"><span>Partners</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('CreditorBranchList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>creditorbranches"><span>Partner Branches</span></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>

                                    <?php if (in_array('SMCreditorMappingList', $this->RolePermission)) { ?>
                                        <li><a href="<?php echo base_url(); ?>smcreditors"><i class="ti-map"></i> <span>SM Partner Mapping</span></a></li>
                                    <?php } ?>

                                    <?php if (in_array('DiscrepancyTypeList', $this->RolePermission) || in_array('DiscrepancySubTypeList', $this->RolePermission) || in_array('LocationList', $this->RolePermission) || in_array('AssignmentDeclarationList', $this->RolePermission) || in_array('PaymentWorkFlowList', $this->RolePermission)) { ?>
                                        <li class="">
                                            <a href="javascript:void(0)" aria-expanded="true"><i class="ti-harddrives"></i><span>Master's</span></a>
                                            <ul class="collapse">
                                                <?php if (in_array('PaymentWorkFlowList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>paymentworkflowmaster"><span>Payment Workflow Master</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('CompanyList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>companymst"><span>Companies</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('LocationList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>locationmst"><span>Location</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('DiscrepancyTypeList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>discrepancytype"><span>Discrepancy Type</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('DiscrepancySubTypeList', $this->RolePermission)) { ?>
                                                    <li><a href="<?php echo base_url(); ?>discrepancysubtype"><span>Discrepancy Subtype</a></span></li>
                                                <?php } ?>
                                                <?php if (in_array('AssignmentDeclarationList', $this->RolePermission)) { ?>
                                                    <li><a href="<?php echo base_url(); ?>assignmentdeclaration"><span>Declaration</a></span></li>
                                                <?php } ?>
                                                <?php if (in_array('ThemeConfiguaration', $this->RolePermission)) { ?>
                                                    <li><a href="<?php echo base_url(); ?>ThemeConfiguaration"><span>Theme Configuaration</a></span></li>
                                                <?php } ?>
                                                <?php if (in_array('Linkuiconfiguration', $this->RolePermission)) { ?>
                                                    <li><a href="<?php echo base_url(); ?>Linkuiconfiguration"><span>Link UI Configuaration</a></span></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>


                                    <!-- new added by Jiten -->
                                    <?php if (in_array('FamilyConstructList', $this->RolePermission) || in_array('InsurerList', $this->RolePermission) || in_array('PolicySubtypeList', $this->RolePermission) || in_array('SumInsuredList', $this->RolePermission) || in_array('ProductsList', $this->RolePermission) || in_array('FeatureList', $this->RolePermission)  || in_array('BranchIMDList', $this->RolePermission)) { ?>
                                        <li class="mb-3">
                                            <a href="javascript:void(0)" aria-expanded="true"><i class="ti-package"></i><span>Products</span></a>
                                            <ul class="collapse">
                                                <?php if (in_array('BranchIMDList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>branchimd"><span>Branch IMD Mapping</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('FamilyConstructList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>familyconstruct"><span>Family Construct</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('InsurerList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>insurer"><span>Insurer</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('PolicySubtypeList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>policysubtype"><span>Policy Subtype</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('SumInsuredList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>suminsured"><span>Sum Insured</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('ProductsList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>products"><span>Products</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('FeatureList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>features"><span>Config Feature</span></a></li>

                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <!-- new started -->

                                    <!-- Danish-->
                                    <?php if (in_array('LeadList', $this->RolePermission)) { ?>
                                        <!--<li><a href="<?php echo base_url(); ?>customerleads"><i class="ti-user"></i> <span>Leads</span></a></li>-->
                                    <?php } ?>

                                    <?php if (in_array('LeadList', $this->RolePermission) || in_array('CustomerProposalsList', $this->RolePermission) || in_array('DiscrepancyProposalList', $this->RolePermission) || in_array('BOProposalList', $this->RolePermission) || in_array('COProposalList', $this->RolePermission) || in_array('UWProposalList', $this->RolePermission)) { ?>
                                        <li class="mb-3">
                                            <a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i>
                                                <?php if ($_SESSION['webpanel']['role_id'] == 3) {
                                                    echo "<span>Sales Portal</span>";
                                                } else if ($_SESSION['webpanel']['role_id'] == 5) {
                                                    echo "<span>BO Proposals</span>";
                                                } else if ($_SESSION['webpanel']['role_id'] == 6) {
                                                    echo "<span>All Proposals</span>";
                                                } else if ($_SESSION['webpanel']['role_id'] == 7) {
                                                    echo "<span>UW Proposals</span>";
                                                } else {
                                                    echo "<span>Proposals</span>";
                                                } ?>

                                            </a>
                                            <ul class="collapse">
                                                <?php if (in_array('LeadList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>customerleads"><span>Leads</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('CustomerProposalsList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>customerproposals"><span>Customer Proposals</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('DiscrepancyProposalList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>discrepancyproposals"><span>Discrepancy Proposals</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('BOProposalList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>boproposals"><span>BO Proposals</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('COProposalList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>coproposals"><span>CO Proposals</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array('UWProposalList', $this->RolePermission)) { ?>
                                                    <li class="active"><a href="<?php echo base_url(); ?>uwproposals"><span>UW Proposals</span></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>

                                    <!-- end-->


                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Left Menu End-->