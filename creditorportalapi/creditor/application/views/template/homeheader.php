<?php 

//$CI =& get_instance();

//$CI->load->helper('core_helper'); 

// session_status_check();



?>

<?php

      $CI =& get_instance();

      $CI->load->helper('core_helper'); 

      $result   = intro_video();

      $total_cart = 0;

      $cart_session = "";

      $getCartproducts = "";

      if($this->session->userdata('cart_session')){

        $this->load->model('cart/cartmodel','',TRUE);

        $cart_session = $this->session->userdata('cart_session');

        $getCartproducts = $this->cartmodel->getOptions("tbl_shopping_cart",$cart_session,"cart_session");

        if(!empty($getCartproducts)){

          $total_cart = sizeof($getCartproducts);}else{$total_cart = 0;

        }

      }

?>

<!doctype html>

<html>

<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Qikclean - Home</title>

<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>images/favicon.ico">

<!-- Css -->

<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/font-awesome.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.bxslider.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/jcarousel.responsive.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/style.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/responsive.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/colors/default-color.css">

<link rel="stylesheet" href="<?php echo base_url();?>css/owl.carousel.css">

<link href="https://fonts.googleapis.com/css?family=Roboto:500" rel="stylesheet">

<!-- Js -->

<script src="<?php echo base_url();?>js/jquery.min.js"></script> 

<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>

<script src="<?php echo base_url();?>js/jquery.bxslider.js"></script>

<script src="<?php echo base_url();?>js/jquery.contenthover.min.js"></script>   

<script src="<?php echo base_url();?>js/script.js"></script>

<script src="<?php echo base_url();?>js/popup_view.js"></script>

<script src="<?php echo base_url();?>js/jquery.validate.js"></script>

<script src="<?php echo base_url();?>js/jquery.form.js"></script>

<script src="<?php echo base_url();?>js/jquery.mCustomScrollbar.min.js"></script>

<script src="<?php echo base_url();?>js/jquery.mousewheel.min.js"></script>

<script src="<?php echo base_url();?>js/jquery.jcarousel.min.js"></script>

<script src="<?php echo base_url();?>js/jcarousel.responsive.js"></script>

<script src="<?php echo base_url();?>js/jquery.nicescroll.min.js"></script>

<script src="<?php echo base_url();?>js/owl.carousel.min.js"></script>

<script src="https://use.fontawesome.com/7c6d04df64.js"></script>
<script src="<?php echo base_url();?>js/jquery.LoadingBox.js"></script>

</head>

<body>



<div class="container-fluid homeHeaderNew">

	<nav id="infive_navbar"  class="navbar navbar-default navbar-fixed-top light_bg" style="background: rgba(20,20,20,0.95) !important;">

      <div class="container-fluid pr">

        <!-- <div class="navbar-header">

          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">

            <span class="sr-only">Toggle navigation</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

          </button>

          <a class="navbar-brand" href="<?php echo base_url();?>home">

          	<img src="images/logo.png" class="img-responsive" alt="Qikclean"/>

          </a>

        </div>

        <div id="navbar" class="navbar-collapse collapse">

          <ul class="nav navbar-nav">

            <li><a href="<?php echo base_url();?>courses">Courses</a></li>

            <li><a href="<?php echo base_url();?>freepage">Free Pages</a></li>

 			

                          

            <li style="position:relative;">

                <span style="position: absolute;top: -4px;display: block;background: red;color: #fff;font-size: 10px;left: 8px;padding: 1px;">Coming Soon</span>

                <a href="<?php echo base_url();?>project_listing">Projects</a>

            </li>

            <?php if($this->session->userdata('in5minutes')){ ?>

                                        <li><a href="<?php echo base_url();?>mydashboard" id="">Dashboard</a></li>

					<li><a href="<?php echo base_url();?>logout" id="">Logout</a></li>

			<?php }else{?>

					<li><a href="#" id="login_static">Login</a></li>

			<?php }?>

            <li><a href="<?php echo base_url()?>cart">My Cart</a></li>

            <li class="dropdown">

				<a href="<?php echo base_url();?>aboutus" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">About Us<b class="caret"></b></a>

                    <ul class="dropdown-menu">

                        <li><a href="<?php echo base_url();?>aboutus">Who We Are</a></li>

                        <li><a href="<?php echo base_url();?>faculty">Faculty</a></li>

                    </ul>

             </li>

          </ul>

                   

        </div> --><!--/.nav-collapse -->

        

      <div class="clearfix"></div>

        <!--********************************************** NEW HEADER BY YASH *******************************************-->

        <div class="brandSearch">

          

          <div class="brand" style="">

            <a class="navbar-brand" href="<?php echo base_url();?>home">

              <img src="images/logo.png" class="img-responsive" alt="In5Minutes"/>

            </a>

          </div>

        <!-- Header Search Bar -->

          <div class="row" id="header_search_scroll">

            <!--<div class="col-md-9 col-md-offset-3 col-sm-9 col-sm-offest-3 col-xs-10 col-xs-offset-2">-->

              <div class="col-md-12" align="center">

                  <div class="left-inner-addon">

                      <form class="form-horizontal" action="<?php echo base_url();?>home/search" method="get">

                          <i class="fa fa-search"></i>

                              <input type="text" name="search_val" id="home_header_search_input" class="search-box typeahead tt-input" autocomplete="off" spellcheck="false" dir="auto" placeholder="Search for video..">

                              <button type="submit" id="home_header_search_submit" class="btn btn-primary">Search</button>

                      </form>

                  </div>

              </div>

          </div>



          <div class="socialMediaDiv">

          <a href="https://www.facebook.com/in5minutesIndia/" target="_blank"><img src="<?php echo base_url();?>images/fb.png" class="socialMedia" data-toggle="tooltip" data-placement="left" title="Follow us on Facebook"></a>

          <a href="https://twitter.com/In5minutesIndia" target="_blank"><img src="<?php echo base_url();?>images/twi.png" class="socialMedia" data-toggle="tooltip" data-placement="left" title="Follow us on Twitter"></a>

          <a href="https://www.instagram.com/in5minutesindia/" target="_blank"><img src="<?php echo base_url();?>images/insta.png" class="socialMedia" data-toggle="tooltip" data-placement="left" title="Follow us on Instagram"></a>

          </div>

          <div class="contactNumber">

            <span>+91 98 1924 0334</span>

          </div>

          <a href="<?php echo base_url()?>cart" class="cart_infive cart_infive_home"><span id="cart_quantity"><?php echo $total_cart; ?></span><i class="fa fa-shopping-cart fa-3x" style="font-size:26px;"></i></a>

          <a href="#/" id="navbar_icon" class=""><img src="<?php echo base_url();?>images/navbar_icon.png" alt=""/></a>

          

          

        </div>

        <!-- **************************************** END NEW HEADER *********************************************** -->

        <!-- <a href="https://www.facebook.com/in5minutesIndia/" target="_blank" id="fb_share_top" class="hidden-xs"><img src="<?php echo base_url(); ?>images/fb_icon.png" alt=""/></a>

<a href="https://twitter.com/In5minutesIndia" target="_blank" id="tw_share_top" class="hidden-xs"><img src="<?php echo base_url(); ?>images/twitter.png" alt=""/></a>

<a href="https://www.youtube.com/channel/UCRTpTT8S7GU04IuVRtaj48Q" target="_blank" id="yt_share_top" class="hidden-xs"><img src="<?php echo base_url(); ?>images/youtube_icon.png" alt=""/></a> -->

      </div>

    </nav>  

    <?php $this->load->view('template/commonLoginView'); ?>

    <!-- Modal -->

<div id="myModal" class="modal fade" role="dialog">

  <div class="modal-dialog">



    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">Take a Tour - In5minutes</h4>

      </div>

       

      <div class="modal-body">
        <!-- <?php echo $result[0]['intro_video_youtube']; ?> -->
        <p align="center"><iframe width="100%" height="400" src="https://www.youtube.com/embed/nj3yiw3i7pw" frameborder="0" allowfullscreen></iframe></p>

        <p align="center"><a href="<?php echo base_url();?>images/WebsiteNavigation.pdf" target="_blank">Website Navigation Document</a></p>

      </div>

    </div>



  </div>

</div>

<!-- NEW NAVIGATION POPUP -->

<div id="popup_menu">

          <i class="menu__close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M24 22.568l-1.433 1.432-10.567-10.568-10.568 10.568-1.432-1.432 10.568-10.568-10.568-10.568 1.432-1.432 10.568 10.567 10.567-10.567 1.433 1.432-10.568 10.568 10.568 10.568z"></path></svg></i>

          <ul>

          <li><a href="<?php echo base_url();?>courses">Courses</a></li>

          <li><a href="<?php echo base_url();?>project_listing">Projects</a></li>

          <li><a href="<?php echo base_url();?>atzero">@zero</a></li>

          <?php if($this->session->userdata('in5minutes')){ ?>

          <li><a href="<?php echo base_url();?>mydashboard" id="">Dashboard</a></li>

          <li><a href="<?php echo base_url();?>logout" id="">Logout</a></li>

          <?php }else{?>

          <li><a href="#/" id="login_scroll">Login</a></li>

          <?php }?>

          <li class="dropdown">

              <a href="<?php echo base_url();?>aboutus" data-toggle="dropdown" role="button"

               aria-haspopup="true" aria-expanded="false">About Us <b class="caret"></b></a>

                  <ul class="dropdown-menu">

                      <li><a href="<?php echo base_url();?>aboutus">Who We Are</a></li>

                      <li><a href="<?php echo base_url();?>faculty">Faculty</a></li>

                  </ul>

           </li>

          </ul>

</div>

<!-- NEW NAVIGATION POPUP END -->

<!doctype html>



<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>



  <script>

  $(function() {

    $.extend($.ui.autocomplete.prototype, {

//                _renderMenu: function (ul, items) {

//                    //remove scroll event to prevent attaching multiple scroll events to one container element

//                    $(ul).unbind("scroll");

//

//                    var self = this;

//                    self._scrollMenu(ul, items);

//                },



                _scrollMenu: function (ul, items) {

                    var self = this;

                    var maxShow = 5;

                    var results = [];

                    var pages = Math.ceil(items.length / maxShow);

                    results = items.slice(0, maxShow);



                    if (pages > 1) {

                        $(ul).scroll(function () {

                            if (isScrollbarBottom($(ul))) {

                                ++window.pageIndex;

                                if (window.pageIndex >= pages) return;



                                results = items.slice(window.pageIndex * maxShow, window.pageIndex * maxShow + maxShow);



                                //append item to ul

                                $.each(results, function (index, item) {

                                    self._renderItem(ul, item);

                                });

                                //refresh menu

                                self.menu.deactivate();

                                self.menu.refresh();

                                // size and position menu

                                ul.show();

                                self._resizeMenu();

                                ul.position($.extend({

                                    of: self.element

                                }, self.options.position));

                                if (self.options.autoFocus) {

                                    self.menu.next(new $.Event("mouseover"));

                                }

                            }

                        });

                    }



                    $.each(results, function (index, item) {

                        self._renderItem(ul, item);

                    });

                }

            });

//            

            function isScrollbarBottom(container) {

                 var height = container.outerHeight();

                 var scrollHeight = container[0].scrollHeight;

                 var scrollTop = container.scrollTop();

                 if (scrollTop >= scrollHeight - height) {

                     return true;

                 }

                 return false;

             };

    $( "#home_header_search_input" ).autocomplete({

        //source: "<?php echo base_url(); ?>home/getautosuggest_data"

        source: function(request, response) {

                        $.ajax({

                            type: "POST",

                            url: "<?php echo base_url(); ?>home/getautosuggest_data",

                            data: {search_string: $('#home_header_search_input').val()},

                            dataType: "json",

                            success: function (data) {

                                if (data != null) {

                                    //alert(data);

                                    response(data);

                                }

                            },

                            error: function(result) {

                                //alert("Error");

                            }

                        });

                    },

                    minLength: 3,

                    delay: 0

    })

});



  



  $('.pop_up_close').click(function(){

      $('#login_popup').addClass('popup_hide');

  });



  </script>

<?php
    $temp = $this->session->userdata('in5minutes');

    if(!isset($temp['user_id']) && $temp['user_id'] == ""){
?>
    <script>
      
      $(window).load(function(){

          $('#login_popup').removeClass('popup_hide');
          
      });

    </script>
    
<?php
      // $_SESSION['popup_login'] = "LOADED";
    }

  ?>

<!-- Google analytics script -->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-80356719-1', 'auto');
  ga('send', 'pageview');

</script>    
 