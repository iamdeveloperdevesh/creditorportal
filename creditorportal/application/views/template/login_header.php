<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Partner Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--     <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico"> -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" 
    integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/themify-icons.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/metisMenu.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/owl.carousel.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/slicknav.min.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/typography.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/default-css.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/styles.css', PROTOCOL); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/responsive.css', PROTOCOL); ?>">
    <!-- <style type="text/css">
           @font-face {
                font-family:  'Titillium';
                src: url(assets/fonts/TitilliumWeb-Regular.ttf);
            }
    </style> -->

    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js', PROTOCOL); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.form.js', PROTOCOL); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.validate.js', PROTOCOL); ?>"></script>

</head>

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
                            <a href="index.html"><img src="assets/images/logo.png" alt="logo"></a>
                        </div>
                    </div>
                   <div class="col-md-9 col-1 display-none-sm">
                       <div class="nav-listing" id="nav-top">
                      <a href="#home" class="active">PROTECTING  <i class=" fa fa-angle-down"></i></a>
                      <a href="#news">INVESTING <i class=" fa fa-angle-down"></i></a>
                      <a href="#contact">FINANCING <i class=" fa fa-angle-down"></i></a>
                      <a href="#about">ADVISING <i class=" fa fa-angle-down"></i></a>
                    </div>
                   </div>
                   <div class="col-3 col-md-9 text-right">
                      <a href="javascript:void(0);" class="icon display-none-lg sidebar-icon" onclick="myFunction1()">
                        <i class="fa fa-bars"></i>
                      </a>
                   </div>
                </div>
            </div>
        </div> -->
        <!-- main header area end -->
        <!-- header area start -->
        <!-- header-area header-bottom -->
        <div class="col-md-12 mt-3 mb-2 bor-div">
            <div class=" pad-left-lg text-left mb-1">
                <a href="index.html">
                <?php if(!empty($data['logo_url'])) {                   
                    echo '<img src="'.$data['logo_url'].'" alt="logo" width="200"></a>';
                }else{
                    echo '<img src="/assets/images/logo.png" alt="logo" width="200"></a>';
                } ?>
                
                <!-- <span class="brand-name">Aditya Birla Health Insurance</span> -->
            </div>
        </div>
        <!-- header area end -->