<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ABHI - Creditor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--     <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico"> -->
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/metisMenu.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slicknav.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/typography.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default-css.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/responsive.css">
    <link href="<?PHP echo base_url(); ?>assets/css/jquery.dataTables.css" rel="stylesheet">


    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/jquery.dataTables.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/popper.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/bootstrap.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/owl.carousel.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/metisMenu.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/jquery.slimscroll.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/jquery.slicknav.min.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/plugins.js'></script>
    <script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/scripts.js'></script>
</head>
<style type="text/css">
    @font-face {
        font-family: 'Titillium';
        src: url(<?PHP echo base_url(); ?>assets/fonts/TitilliumWeb-Regular.ttf);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        color: #fff !important;
    }
</style>

<body class="body-bg">
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- main wrapper start -->
    <div class="horizontal-main-wrapper">
        <!-- main header area start -->
        <div class="mainheader-area">
            <div class="container con-left">
                <div class="row align-items-center">
                    <div class="col-md-3 col-9">
                        <div class="logo">
                            <a href="index.html"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="logo"></a>
                        </div>
                    </div>
                    <!--   -->
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
        </div>
        <!-- main header area end -->
        <!-- header area start -->
        <div class="header-area header-bottom">
            <div class="container pad-left-lg">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <span class="brand-name">Aditya Birla Health Insurance</span>
                    </div>
                    <div class="col-md-2 col-2">
                        <div class="user-profile pull-right">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><span class="display-none-sm"></span><span></span> <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <div class="display-none-lg">
                                    <a class="dropdown-item" href="dashboard.html">Dashboard</a>
                                    <a class="dropdown-item" href="profile.html">Profile</a>
                                    <a class="dropdown-item" href="roles.html">Roles</a>
                                    <a class="dropdown-item" href="create-user.html">Create User</a>
                                    <a class="dropdown-item active" href="permissions.html">Permission</a>
                                    <a class="dropdown-item" href="create-user.html">Creditors</a>
                                    <a class="dropdown-item active" href="permissions.html">Creditors Branch</a>
                                </div>
                                <a class="dropdown-item" href="#">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- header area end -->