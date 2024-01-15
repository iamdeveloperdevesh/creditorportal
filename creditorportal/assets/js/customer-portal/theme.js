
/*****************start input js*********************/

function checkboxDropdown(el) {
  var $el = $(el)

  function updateStatus(label, result) {
    if (!result.length) {
      label.html('Arogya Sanjeevani');
    }
  };

  $el.each(function (i, element) {
    var $list = $(this).find('.dropdown-list'),
      $label = $(this).find('.dropdown-label'),
      $checkAll = $(this).find('.check-all'),
      $inputs = $(this).find('.check'),
      defaultChecked = $(this).find('input[type=checkbox]:checked'),
      result = [];

    updateStatus($label, result);
    if (defaultChecked.length) {
      defaultChecked.each(function () {
        result.push($(this).next().text());
        $label.html(result.join(", "));
      });
    }

    $label.on('click', () => {
      $(this).toggleClass('open');
    });

    $checkAll.on('change', function () {
      var checked = $(this).is(':checked');
      var checkedText = $(this).next().text();
      result = [];
      if (checked) {
        result.push(checkedText);
        $label.html(result);
        $inputs.prop('checked', false);
      } else {
        $label.html(result);
      }
      updateStatus($label, result);
    });

    $inputs.on('change', function () {
      var checked = $(this).is(':checked');
      var checkedText = $(this).next().text();
      if ($checkAll.is(':checked')) {
        result = [];
      }
      if (checked) {
        result.push(checkedText);
        $label.html(result.join(", "));
        $checkAll.prop('checked', false);
      } else {
        let index = result.indexOf(checkedText);
        if (index >= 0) {
          result.splice(index, 1);
        }
        $label.html(result.join(", "));
      }
      updateStatus($label, result);
    });

    $(document).on('click touchstart', e => {
      if (!$(e.target).closest($(this)).length) {
        $(this).removeClass('open');
      }
    });
  });
};

checkboxDropdown('.dropdown');


/**********************end input js****************/

// popovers initialization - on hover
$('[data-toggle="popover-hover"]').popover({
  html: true,
  trigger: 'hover',
  placement: 'bottom',
  content: function () { return '<img src="' + $(this).data('img') + '" />'; }
});

// popovers initialization - on click
$('[data-toggle="popover-click"]').popover({
  html: true,
  trigger: 'click',
  placement: 'bottom',
  content: function () { return '<img src="' + $(this).data('img') + '" />'; }
});


(function ($) {
  "use strict";


  $(document).on('ready', function () {

    $.fn.visible = function (partial) {
      var $t = $(this),
        $w = $(window),
        viewTop = $w.scrollTop(),
        viewBottom = viewTop + $w.height(),
        _top = $t.offset().top,
        _bottom = _top + $t.height(),
        compareTop = partial === true ? _bottom : _top,
        compareBottom = partial === true ? _top : _bottom;

      return ((compareBottom <= viewBottom) && (compareTop >= viewTop));
    };


    // toggleClass
    $(document).on('click', '[data-toggle-class]', function (e) {
      var $self = $(this);
      var attr = $self.attr('data-toggle-class');
      var target = $self.attr('data-toggle-class-target') || $self.attr('data-target');
      var closest = $self.attr('data-target-closest');
      var classes = (attr && attr.split(',')) || '',
        targets = (target && target.split(',')) || Array($self),
        key = 0;
      $.each(classes, function (index, value) {
        var target = closest ? $self.closest(targets[(targets.length == 1 ? 0 : key)]) : $(targets[(targets.length == 1 ? 0 : key)]),
          current = target.attr('data-class'),
          _class = classes[index];
        (current != _class) && target.removeClass(target.attr('data-class'));
        target.toggleClass(classes[index]);
        target.attr('data-class', _class);
        key++;
      });
      $self.toggleClass('active');
      $self.attr('href') == "#" ? e.preventDefault() : '';
    });


    /* scroll animate
    -------------------------------------------------------*/
    var links = $('a.scroll-target');
    links.on('click', function () {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html,body').animate({
            scrollTop: target.offset().top - 75,
          }, 1000);
          return false;
        }
      }
    });

    // ------------------------- Tooltips
    $('[data-toggle="tooltip"]').tooltip();


    // -------------------- Subscribe newsletter
    if ($('.ajax-sub-form').length > 0) {
      $('.ajax-sub-form').ajaxChimp({
        language: 'es',
        url: "https://heloshape.us19.list-manage.com/subscribe/post?u=39657911dbafbf74565c09ef2&amp;id=2e580f0cc7" //Replace this with your mailchimp post URL.
      });

      $.ajaxChimp.translations.es = {
        'submit': 'Submitting...',
        0: 'Thanks for your subscription',
        1: 'Please enter a valid email',
        2: 'An email address must contain a single @',
        3: 'The domain portion of the email address is invalid (the portion after the @: )',
        4: 'The username portion of the email address is invalid (the portion before the @: )',
        5: 'This email address looks fake or invalid. Please enter a real email address'
      };
    }





    // --------------------- SVG convert Function
    $('img.svg').each(function () {
      var $img = $(this);
      var imgID = $img.attr('id');
      var imgClass = $img.attr('class');
      var imgURL = $img.attr('src');

      $.get(imgURL, function (data) {
        // Get the SVG tag, ignore the rest
        var $svg = $(data).find('svg');

        // Add replaced image's ID to the new SVG
        if (typeof imgID !== 'undefined') {
          $svg = $svg.attr('id', imgID);
        }
        // Add replaced image's classes to the new SVG
        if (typeof imgClass !== 'undefined') {
          $svg = $svg.attr('class', imgClass + ' replaced-svg');
        }

        // Remove any invalid XML tags as per http://validator.w3.org
        $svg = $svg.removeAttr('xmlns:a');

        // Check if the viewport is set, else we gonna set it if we can.
        if (!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
          $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
        }

        // Replace image with new SVG
        $img.replaceWith($svg);

      }, 'xml');

    });

    // -------------------------- Sidebar Menu/E-commerce
    var subMenu = $(".main-menu-list ul li.dropdown-holder>a"),
      expender = $(".main-menu-list ul li.dropdown-holder .expander");

    if ($('.sidebar-menu-open').length) {
      $('.sidebar-menu-open').on('click', function () {
        $('#sidebar-menu').addClass("show-menu");
      });
    }

    if ($('.close-button').length) {
      $('.close-button').on('click', function () {
        $('#sidebar-menu').removeClass("show-menu");
      });
    }
    subMenu.on("click", function (e) {
      e.preventDefault();
    });

    subMenu.append(function () {
      return '<button type="button" class="expander"><i class="fa fa-chevron-down" aria-hidden="true"></i></button>';
    });

    subMenu.on('click', function () {
      if ($(this).parent('li').children('ul').hasClass('show')) {
        $(this).parent('li').children('ul').removeClass('show');
      } else {
        $('.sub-menu.show').removeClass('show');
        $(this).parent('li').children('ul').addClass('show');
      }
    });

    // ------------------------ Product Quantity Selector
    if ($(".product-value").length) {
      $('.value-increase').on('click', function () {
        var $qty = $(this).closest('ul').find('.product-value');
        var currentVal = parseInt($qty.val());
        if (!isNaN(currentVal)) {
          $qty.val(currentVal + 1);
        }
      });
      $('.value-decrease').on('click', function () {
        var $qty = $(this).closest('ul').find('.product-value');
        var currentVal = parseInt($qty.val());
        if (!isNaN(currentVal) && currentVal > 1) {
          $qty.val(currentVal - 1);
        }
      });
    }


    // ------------------------ Animated Bootstrap Banner
    //Function to animate slider captions
    function doAnimations(elems) {
      //Cache the animationend event in a variable
      var animEndEv = "webkitAnimationEnd animationend";

      elems.each(function () {
        var $this = $(this),
          $animationType = $this.data("animation");
        $this.addClass($animationType).one(animEndEv, function () {
          $this.removeClass($animationType);
        });
      });
    }

    //Variables on page load
    var $myCarousel = $(".carousel"),
      $firstAnimatingElems = $myCarousel
        .find(".carousel-item:first")
        .find("[data-animation ^= 'animated']");

    //Initialize carousel
    $myCarousel.carousel();

    //Animate captions in first slide on page load
    doAnimations($firstAnimatingElems);

    //Other slides to be animated on carousel slide event
    $myCarousel.on("slide.bs.carousel", function (e) {
      var $animatingElems = $(e.relatedTarget).find(
        "[data-animation ^= 'animated']"
      );
      doAnimations($animatingElems);
    });

    // scroll slides on mouse scroll 
    $('#eCommerce-carousel').bind('mousewheel DOMMouseScroll', function (e) {

      if (e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0) {
        $(this).carousel('prev');


      }
      else {
        $(this).carousel('next');

      }
    });


    // ------------------------- Price Ranger
    if ($('.price-ranger').length) {
      $('.price-ranger #slider-range').slider({
        range: true,
        min: 0,
        max: 500,
        values: [0, 335],
        slide: function (event, ui) {
          $('.price-ranger .ranger-min-max-block .min').val('$' + ui.values[0]);
          $('.price-ranger .ranger-min-max-block .max').val('$' + ui.values[1]);
        }
      });
      $('.price-ranger .ranger-min-max-block .min').val('$' + $('.price-ranger #slider-range').slider('values', 0));
      $('.price-ranger .ranger-min-max-block .max').val('$' + $('.price-ranger #slider-range').slider('values', 1));
    }


    // ------------------------ Product Fliter
    if ($(".filter-button").length) {
      $(".filter-button").on('click', function () {
        $('.large-filter-content').toggleClass('show-content');
        $('.filter-button').toggleClass('open');
      });
    }

    // ---------------------- Header Search
    // ------------------------ Product Fliter
    if ($(".toggle-search").length) {
      $(".toggle-search .icon").on('click', function () {
        $(this).parent('form').toggleClass('show');
      });
    }

    // ---------------------------- Select Dropdown
    if ($('select').length) {
      $('.theme-select-menu').selectize();
    }


    // ------------------------ Navigation Scroll
    $(window).on('scroll', function () {
      var sticky = $('.theme-main-menu'),
        scroll = $(window).scrollTop();
      if (scroll >= 100) sticky.addClass('fixed');
      else sticky.removeClass('fixed');

    });

    // -------------------- Remove Placeholder When Focus Or Click
    $("input,textarea").each(function () {
      $(this).data('holder', $(this).attr('placeholder'));
      $(this).on('focusin', function () {
        $(this).attr('placeholder', '');
      });
      $(this).on('focusout', function () {
        $(this).attr('placeholder', $(this).data('holder'));
      });
    });

    // -------------------- From Bottom to Top Button
    //Check to see if the window is top if not then display button
    $(window).on('scroll', function () {
      if ($(this).scrollTop() > 200) {
        $('.scroll-top').fadeIn();
      } else {
        $('.scroll-top').fadeOut();
      }
    });


    //---------------------- Click event to scroll to top
    $('.scroll-top').on('click', function () {
      $('html, body').animate({ scrollTop: 0 }, 1500);
      return false;
    });


    // ----------------------------- Counter Function
    var timer = $('.timer');
    if (timer.length) {
      timer.appear(function () {
        timer.countTo();
      });
    }

    // ------------------------ Hover Tilt effect
    var tiltBlock = $('.js-tilt');
    if (tiltBlock.length) {
      $('.js-tilt').tilt({
        glare: true,
        maxGlare: 0.4
      });
    }


    // ------------------------ Modal box
    if ($(".iziModal").length) {
      $(".iziModal").iziModal({
        width: 2550,
        overlayColor: 'rgba(255, 255, 255, 0.95)',
        fullscreen: true,
      });
    }


    // ----------------------- Progress Bar
    $('.progress-bar').each(function () {
      var width = $(this).data('percent');
      $(this).css({ 'transition': 'width 3s' });
      $(this).appear(function () {
        $(this).css('width', width + '%');
        $(this).find('.count').countTo({
          from: 0,
          to: width,
          speed: 3000,
          refreshInterval: 50,
        });
      });
    });



    // --------------------------- Theme Main Banner Slider One
    var banner = $(".banner-one");
    if (banner.length) {
      banner.camera({ //here I declared some settings, the height and the presence of the thumbnails 
        height: '940px',
        pagination: false,
        navigation: false,
        thumbnails: false,
        playPause: false,
        pauseOnClick: false,
        autoPlay: true,
        hover: false,
        overlayer: true,
        loader: 'none',
        minHeight: '400px',
        time: 6000000,
      });
    }



    // ------------------------------- Gallery Slider
    var tSlider = $(".gallery-slider");
    if (tSlider.length) {
      tSlider.owlCarousel({
        loop: true,
        nav: true,
        navText: ["<i class='flaticon-back'></i>", "<i class='flaticon-next'></i>"],
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        smartSpeed: 1200,
        autoplayHoverPause: true,
        lazyLoad: true,
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 2
          },
          1100: {
            items: 3
          },
          1550: {
            items: 4,
          }
        },
      });
    }


    // ------------------------------- Testimonial Slider
    var agnTslider = $(".agn-testimonial-slider");
    if (agnTslider.length) {
      agnTslider.owlCarousel({
        loop: true,
        nav: true,
        navText: ["<i class='flaticon-back'></i>", "<i class='flaticon-next'></i>"],
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        smartSpeed: 1200,
        autoplayHoverPause: true,
        lazyLoad: true,
        items: 1
      });
    }


    // ------------------------------- Home Blog Slider
    var nSlider = $(".blog-one-slider");
    if (nSlider.length) {
      nSlider.owlCarousel({
        loop: true,
        nav: false,
        dots: false,
        autoplay: true,
        margin: 30,
        autoplayTimeout: 4000,
        smartSpeed: 1200,
        autoplayHoverPause: true,
        lazyLoad: true,
        responsive: {
          0: {
            items: 1
          },
          768: {
            items: 2
          },
          992: {
            items: 3,
          }
        },
      });
    }



    // ------------------------------ Partner Logo Footer 
    var logoslider = $(".partner-slider");
    if (logoslider.length) {
      logoslider.owlCarousel({
        loop: true,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplaySpeed: 1000,
        lazyLoad: true,
        singleItem: true,
        center: true,
        responsive: {
          0: {
            items: 1
          },
          550: {
            items: 3
          },
          992: {
            items: 5
          }
        }
      });
    }



    // ------------------------------ Customer Slider 
    var csSlider = $(".customer-slider");
    if (csSlider.length) {
      csSlider.owlCarousel({
        loop: true,
        nav: true,
        navText: ["<i class='flaticon-back'></i>", "<i class='flaticon-next'></i>"],
        dots: false,
        autoplay: true,
        margin: 0,
        autoplayTimeout: 4500,
        autoplaySpeed: 1000,
        lazyLoad: true,
        singleItem: true,
        responsive: {
          0: {
            items: 1
          },
          768: {
            items: 2
          },
          992: {
            items: 3
          }
        }
      });
    }


    // ------------------------------ ARCH Client Slicer
    var arclider = $(".arch-client-slider");
    if (arclider.length) {
      arclider.owlCarousel({
        loop: true,
        nav: false,
        dots: true,
        autoplay: true,
        margin: 30,
        autoplayTimeout: 4500,
        autoplaySpeed: 1000,
        lazyLoad: true,
        singleItem: true,
        responsive: {
          0: {
            items: 1
          },
          768: {
            items: 2
          },
          1100: {
            items: 3
          }
        }
      });
    }



    // ------------------------------ Related Product Slider
    var reSlider = $(".related-product-slider");
    if (reSlider.length) {
      reSlider.owlCarousel({
        loop: true,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        dots: false,
        autoplay: true,
        margin: 30,
        autoplayTimeout: 4500,
        autoplaySpeed: 1000,
        lazyLoad: true,
        responsive: {
          0: {
            items: 1
          },
          576: {
            items: 2
          },
          992: {
            items: 3
          }
        }
      });
    }


    // ------------------------ Minimal Project Slider
    var mcpSlider = $(".project-minimal-slider");
    if (mcpSlider.length) {
      mcpSlider.owlCarousel({
        loop: true,
        nav: true,
        navText: ["<i class='flaticon-back'></i>", "<i class='flaticon-next'></i>"],
        dots: false,
        autoplay: true,
        margin: 45,
        autoplayTimeout: 4500,
        autoplaySpeed: 1000,
        lazyLoad: true,
        singleItem: true,
        center: true,
        responsive: {
          0: {
            items: 1
          },
          768: {
            items: 2
          },
          1200: {
            items: 3
          }
        }
      });
    }



    // ------------------------------ Team Classic Slider
    var tcSlider = $(".team-classic-slider");
    if (tcSlider.length) {
      tcSlider.owlCarousel({
        loop: true,
        nav: false,
        dots: false,
        autoplay: true,
        margin: 30,
        autoplayTimeout: 4500,
        autoplaySpeed: 1000,
        lazyLoad: true,
        singleItem: true,
        responsive: {
          0: {
            items: 1
          },
          550: {
            items: 2
          },
          992: {
            items: 3
          }
        }
      });
    }

    // --------------------------------- Contact Form
    // init the validator
    // validator files are included in the download package
    // otherwise download from http://1000hz.github.io/bootstrap-validator

    if ($("#contact-form").length) {
      $('#contact-form').validator();
      // when the form is submitted
      $('#contact-form').on('submit', function (e) {

        // if the validator does not prevent form submit
        if (!e.isDefaultPrevented()) {
          var url = "inc/contact.php";

          // POST values in the background the the script URL
          $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function (data) {
              // data = JSON object that contact.php returns

              // we recieve the type of the message: success x danger and apply it to the
              var messageAlert = 'alert-' + data.type;
              var messageText = data.message;

              // let's compose Bootstrap alert box HTML
              var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';

              // If we have messageAlert and messageText
              if (messageAlert && messageText) {
                // inject the alert to .messages div in our form
                $('#contact-form').find('.messages').html(alertBox);
                // empty the form
                $('#contact-form')[0].reset();
              }
            }
          });
          return false;
        }
      });
    }


    // -------------------------------- Accordion Panel
    if ($('.theme-accordion > .panel').length) {
      $('.theme-accordion > .panel').on('show.bs.collapse', function (e) {
        var heading = $(this).find('.panel-heading');
        heading.addClass("active-panel");

      });
      $('.theme-accordion > .panel').on('hidden.bs.collapse', function (e) {
        var heading = $(this).find('.panel-heading');
        heading.removeClass("active-panel");
        //setProgressBar(heading.get(0).id);
      });
      $('.panel-heading a').on('click', function (e) {
        if ($(this).parents('.panel').children('.panel-collapse').hasClass('show')) {
          e.stopPropagation();
        }
      });
    }


    // -------------------------- Partical Bg
    if ($("#particles").length) {
      particlesJS("particles", {
        "particles": {
          "number": {
            "value": 200,
            "density": {
              "enable": true,
              "value_area": 800
            }
          },
          "color": {
            "value": "#636593"
          },
          "shape": {
            "type": "circle",
            "stroke": {
              "width": 0,
              "color": "#000000"
            },
            "polygon": {
              "nb_sides": 5
            },
            "image": {
              "src": "img/github.svg",
              "width": 100,
              "height": 100
            }
          },
          "opacity": {
            "value": 0.7,
            "random": true,
            "anim": {
              "enable": true,
              "speed": 1,
              "opacity_min": 0,
              "sync": false
            }
          },
          "size": {
            "value": 3,
            "random": true,
            "anim": {
              "enable": false,
              "speed": 4,
              "size_min": 0.3,
              "sync": false
            }
          },
          "line_linked": {
            "enable": false,
            "distance": 150,
            "color": "#ffffff",
            "opacity": 0.4,
            "width": 1
          },
          "move": {
            "enable": true,
            "speed": 1,
            "direction": "none",
            "random": true,
            "straight": false,
            "out_mode": "out",
            "bounce": false,
            "attract": {
              "enable": false,
              "rotateX": 600,
              "rotateY": 600
            }
          }
        },
        "interactivity": {
          "detect_on": "canvas",
          "events": {
            "onhover": {
              "enable": true,
              "mode": "bubble"
            },
            "onclick": {
              "enable": true,
              "mode": "repulse"
            },
            "resize": true
          },
          "modes": {
            "grab": {
              "distance": 400,
              "line_linked": {
                "opacity": 1
              }
            },
            "bubble": {
              "distance": 250,
              "size": 0,
              "duration": 2,
              "opacity": 0,
              "speed": 3
            },
            "repulse": {
              "distance": 400,
              "duration": 0.4
            },
            "push": {
              "particles_nb": 4
            },
            "remove": {
              "particles_nb": 2
            }
          }
        },
        "retina_detect": true
      });
    }






  }); //End Window Ready Function


  $(window).on('load', function () { // makes sure the whole site is loaded

    // -------------------- Site Preloader
    $('#ctn-preloader').fadeOut(); // will first fade out the loading animation
    $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
    $('body').delay(350).css({ 'overflow': 'visible' });


    // ------------------------------- AOS Animation
    if ($("[data-aos]").length) {
      AOS.init({
        duration: 1000,
        mirror: true
      });
    }

    // ------------------------------- WOW Animation 
    if ($(".wow").length) {
      var wow = new WOW({
        boxClass: 'wow',      // animated element css class (default is wow)
        animateClass: 'animated', // animation css class (default is animated)
        offset: 20,          // distance to the element when triggering the animation (default is 0)
        mobile: true,       // trigger animations on mobile devices (default is true)
        live: true,       // act on asynchronously loaded content (default is true)
      });
      wow.init();
    }

    // ----------------------------- isotop gallery
    if ($("#isotop-gallery-wrapper , .masnory-blog-wrapper").length) {
      var $grid = $('#isotop-gallery-wrapper , .masnory-blog-wrapper').isotope({
        // options
        itemSelector: '.isotop-item',
        percentPosition: true,
        masonry: {
          // use element for option
          columnWidth: '.grid-sizer'
        }

      });

      // filter items on button click
      $('.isotop-menu-wrapper').on('click', 'li', function () {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
      });

      // change is-checked class on buttons
      $('.isotop-menu-wrapper').each(function (i, buttonGroup) {
        var $buttonGroup = $(buttonGroup);
        $buttonGroup.on('click', 'li', function () {
          $buttonGroup.find('.is-checked').removeClass('is-checked');
          $(this).addClass('is-checked');
        });
      });
    }


    // ------------------------------------- Fancybox
    var fancy = $(".fancybox");
    if (fancy.length) {
      fancy.fancybox({
        arrows: true,
        buttons: [
          "zoom",
          //"share",
          "slideShow",
          //"fullScreen",
          //"download",
          "thumbs",
          "close"
        ],
        animationEffect: "zoom-in-out",
        transitionEffect: "zoom-in-out",
      });
    }



  });  //End On Load Function


  $(window).on('scroll', function () { // makes sure the whole site is loaded

    // --------------------- Viewport Animation 
    $(".hide-pr").each(function (i, el) {
      var el = $(el);
      if (el.visible(true)) {
        el.addClass("show-pr");
      } else {
        el.removeClass("show-pr");
      }
    });

  });  //End On Scroll Function



})(jQuery);
/***************step wizard************************/
$(document).ready(function () {
  var navListItems = $('div.setup-panel div a'),
    allWells = $('.setup-content'),
    allNextBtn = $('.nextBtn');

  allWells.hide();

  navListItems.click(function (e) {
    e.preventDefault();
    var $target = $($(this).attr('href')),
      $item = $(this);

    if (!$item.hasClass('disabled')) {
      navListItems.removeClass('btn-primary').addClass('btn-default');
      $item.addClass('btn-primary');
      allWells.hide();
      $target.show();
      $target.find('input:eq(0)').focus();
    }
  });

  allNextBtn.click(function () {
    var curStep = $(this).closest(".setup-content"),
      curStepBtn = curStep.attr("id"),
      nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
      curInputs = curStep.find("input[type='text'],input[type='url']"),
      isValid = true;

    $(".form-group").removeClass("has-error");
    for (var i = 0; i < curInputs.length; i++) {
      if (!curInputs[i].validity.valid) {
        isValid = false;
        $(curInputs[i]).closest(".form-group").addClass("has-error");
      }
    }

    if (isValid)
      nextStepWizard.removeAttr('disabled').trigger('click');
  });

  $('div.setup-panel div a.btn-primary').trigger('click');
});
/*****************end step wizard************************/
/****************proposal step wizard********************/
$(document).ready(function () {
  var navListItems = $('div.setup-panel div a'),
    allWells = $('.setup-content'),
    allNextBtn = $('.nextBtn');

  allWells.hide();

  navListItems.click(function (e) {
    e.preventDefault();
    var $target = $($(this).attr('href')),
      $item = $(this);

    if (!$item.hasClass('disabled')) {
      navListItems.removeClass('btn-primary').addClass('btn-default');
      $item.addClass('btn-primary');
      allWells.hide();
      $target.show();
      $target.find('input:eq(0)').focus();
    }
  });

  allNextBtn.click(function () {
    var curStep = $(this).closest(".setup-content"),
      curStepBtn = curStep.attr("id"),
      nextstepwizard_p = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
      curInputs = curStep.find("input[type='text'],input[type='url']"),
      isValid = true;

    $(".form-group").removeClass("has-error");
    for (var i = 0; i < curInputs.length; i++) {
      if (!curInputs[i].validity.valid) {
        isValid = false;
        $(curInputs[i]).closest(".form-group").addClass("has-error");
      }
    }

    if (isValid)
      nextstepwizard_p.removeAttr('disabled').trigger('click');
  });

  $('div.setup-panel div a.btn-primary').trigger('click');
});
/*************************end proposal step****************/
/*****************************popover quick view hover************************/
// $(document).ready(function(){

//     $('[data-toggle="popover"]').popover({
// html: true,

//         trigger : 'hover',
//     title: function () {
//             return $(this).parent().find('.head').html();
//         },
//         content: function () {
//             return $(this).parent().find('.content').html();
//         }
//     });
// });
/*******************************************************************************/
/*****************************popover quick view click****************************/
$(function () {
  $('.trigger_m').popover({
    html: true,
    title: function () {
      return $(this).parent().find('.head').html();
    },
    content: function () {
      return $(this).parent().find('.content').html();
    }
  });

  $('body').on('click', '#vehicleChkBox', function () {
    window.alert('Is my checkbox checked: ' + $(this).prop('checked'));
  });
});
/*****************************end popover*************************/
/**********************search dropdown*********************/
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

function filterFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}
/***********************end search dropdown************/
/***********************quantity***********************/
function wcqib_refresh_quantity_increments() {
  jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function (a, b) {
    var c = jQuery(b);
    c.addClass("buttons_added"), c.children().first().before('<input type="button" value="-" class="minus" />'), c.children().last().after('<input type="button" value="+" class="plus" />')
  })
}
String.prototype.getDecimals || (String.prototype.getDecimals = function () {
  var a = this,
    b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
  return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).ready(function () {
  wcqib_refresh_quantity_increments()
}), jQuery(document).on("updated_wc_div", function () {
  wcqib_refresh_quantity_increments()
}), jQuery(document).on("click", ".plus, .minus", function () {
  var a = jQuery(this).closest(".quantity").find(".qty"),
    b = parseFloat(a.val()),
    c = parseFloat(a.attr("max")),
    d = parseFloat(a.attr("min")),
    e = a.attr("step");
  b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});
/*********************** end quantity***********************/
/**********************multi step***************************/
$(document).ready(function () {

  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  var current = 1;
  var steps = $("fieldset").length;

  setProgressBar(current);

  $(".next").click(function () {

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();

    //Add Class Active
    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

    //show the next fieldset
    next_fs.show();
    //hide the current fieldset with style
    current_fs.animate({ opacity: 0 }, {
      step: function (now) {
        // for making fielset appear animation
        opacity = 1 - now;

        current_fs.css({
          'display': 'none',
          'position': 'relative'
        });
        next_fs.css({ 'opacity': opacity });
      },
      duration: 500
    });
    setProgressBar(++current);
  });

  $(".previous").click(function () {

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();

    //Remove class active
    $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

    //show the previous fieldset
    previous_fs.show();

    //hide the current fieldset with style
    current_fs.animate({ opacity: 0 }, {
      step: function (now) {
        // for making fielset appear animation
        opacity = 1 - now;

        current_fs.css({
          'display': 'none',
          'position': 'relative'
        });
        previous_fs.css({ 'opacity': opacity });
      },
      duration: 500
    });
    setProgressBar(--current);
  });

  function setProgressBar(curStep) {
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    $(".progress-bar")
      .css("width", percent + "%")
  }

  $(".submit").click(function () {
    return false;
  })

});
/**********************end multi step****************/
/*******************horizontal bar*********/
$('.value').each(function () {
  var text = $(this).text();
  $(this).parent().css('width', text);
});

$('.block').tooltip();
/*******************end horizontal bar**************/

function isInView(elem) {
  return $(elem).offset().top - $(window).scrollTop() < $(elem).height();
}

function debounce(method, delay) {
  clearTimeout(method._tId);
  method._tId = setTimeout(function () {
    method();
  }, delay);
}