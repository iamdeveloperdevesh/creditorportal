<?php
 //https://test.payu.in/merchant/postservice?form=2-H
 $data = '<saml:Assertion xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" ID="s25cdaac9718e546e7e7c99eeee854f1c0424297d2" IssueInstant="2021-10-27T12:04:23Z" Version="2.0"> <saml:Issuer>https://idp.insurance.TATA.in</saml:Issuer>  <ds:Signature
            xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:SignedInfo>
                <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
                <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
                <ds:Reference URI="#s25cdaac9718e546e7e7c99eeee854f1c0424297d2">
                    <ds:Transforms>
                        <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                        <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
                    </ds:Transforms>
                    <ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                    <ds:DigestValue>4tCavHoGH7V6N7VkHUmfsL3ZYQM=</ds:DigestValue>
                </ds:Reference>
            </ds:SignedInfo>
            <ds:SignatureValue></ds:SignatureValue>
            <ds:KeyInfo>
                <ds:X509Data>
                    <ds:X509Certificate></ds:X509Certificate>
                </ds:X509Data>
            </ds:KeyInfo>
        </ds:Signature>
        <saml:Subject>
            <saml:NameID Format="urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified" NameQualifier="https://idp.insurance.TATA.in" SPNameQualifier="XXX">SP0125468708</saml:NameID>
            <saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
                <saml:SubjectConfirmationData NotBefore="2021-10-27T12:04:23Z" NotOnOrAfter="2021-10-27T12:34:23Z" Recipient="XXX"/>
            </saml:SubjectConfirmation>
        </saml:Subject>
        <saml:Conditions NotBefore="2021-10-27T12:04:23Z" NotOnOrAfter="2021-10-27T12:34:23Z">
            <saml:AudienceRestriction>
                <saml:Audience>XXX</saml:Audience>
            </saml:AudienceRestriction>
        </saml:Conditions>
        <saml:AuthnStatement AuthnInstant="2021-10-27T12:04:23Z">
            <saml:AuthnContext>
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
            </saml:AuthnContext>
        </saml:AuthnStatement>
        <saml:AttributeStatement>
            <saml:Attribute Name="authToken">
                <saml:AttributeValue
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string">Wa4todSk024fK7whtexrw4xoHvzVEVx/RyTYdvl/4WNCSPVFRuVOHnT9ghvWp9+juJN0LBHSqsj+h+R6aQbZFQNDDccNHxftldWQE4BR7ZLFeqPjJV4OVACuip+GrhIx8PART11XsKg5NWxj5BgwjxkeklRnfZJgrHeV1AOiNVK48lq1r0lQwRSg5r5HXWL65v9VEtJqTqd4Xcbq09javnZWMlUi2MJmLo8p2JxixC9h1crweDGH956r9HyDiCrpW0HO1yRedSS/VQ4CcoZFW4zcyMzh+g/SwChsUH6Mx8XRCIJB4ZGij/caFu8jL+5nMAQwxbCYbIrJ0BHrqAgMJLmvFzCEZvDY/dHTb4nZJ87EGg3r8UFZz/XcPZOI1tOY1CkxJ9/RnHbh4dG10XLITvf0fXJ8JMcsKHOM864/hFu62Ic5+SF3V5SX9EJkbwawyLSVahBjumiiWJDl09g7rT3Jzo63Gt77YCpIZpf2h4qhmy6+EQgZnABQuE7khGP8Oj/6BCepvHacHk6IvWIwG+MmvTjazg2S13SXb9DZE314YRlaOrwFP9RUs30+vMjI/BA8rB77HLLVmALeXnaZ8BHYXoBgRcnSamRPERByYIv6aDmU6JzFA4HM182tiUD8WHxxj5pTLh4fQYTaGXQ1CEJI9UVG5U4edP2CG9an36O4k3QsEdKqyP6H5HppBtkVA0MNxw0fF+2V1ZATgFHtksV6o+MlXg5UAK6Kn4auEjHw8BFPXVewqDk1bGPkGDCPGR6SVGd9kmCsd5XUA6I1UrjyWrWvSVDBFKDmvkddYvrm/1US0mpOp3hdxurT2Nq+dlYyVSLYwmYujynYnGLEL2HVyvB4MYf3nqv0fIOIKulbQc7XJF51JL9VDgJyhkVbjNzIzOH6D9LAKGxQfozHxdEIgkHhkaKP9xoW7yMv7mcwBDDFsJhsisnQEeuoCAwkua8XMIRm8Nj90dNvidknzsQaDevxQVnP9dw9k4jW05jUKTEn39GcduHh0bXRcshO9/R9cnwkxywoc4zzrj+EW7rYhzn5IXdXlJf0QmRvBrDItJVqEGO6aKJYkOXT2DutPcnOjrca3vtgKkhml/aHiqGbLr4RCBmcAFC4TuSEY/w6P/oEJ6m8dpweToi9YjAb4ya9ONrODZLXdJdv0NkTfXhhGVo6vAU/1FSzfT68yMj8EDysHvscstWYAt5edpnwEdhegGBFydJqZE8REHJgi/poOZTonMUDgczXza2JQPyTuIKf6vY3R2Mk/L49a4CtjeNvk1OjKQZZEA/75vs27fMftvJKAWB4O6ISJVREhwHEAad3bjk2MTPpFLHrMu/N8Mt8/EQYrTyHT14ko8ExY5Id2cNtoKYeMPfAtsFTLhSn+CfP6omp/25u9gmwuWdIC3S2hQfQAKDNcsA3P4A6TAno25JHpTi7h38g1orrBiTiJTgLNwV952HWn86CK/OaPsvHfxKTp9tGq5fLqaHaX6KaQC7ijb4yeh/JFNOSd2ZEsm+p8weeii8WGpSRC0Cy33oL3SNc8ATG+8xNW2DPwnGIuCcErRwzEGz2pAZS3iQYczlGBB/EwIRxgclOu6+O
                </saml:AttributeValue>
            </saml:Attribute>
            <saml:Attribute Name="traceId">
                <saml:AttributeValue
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string">aXRQOG9HSnpLWERFYmt0bGptVGdNT2x3VlVYeE1PM2hWT0k1SXNWK0l0NmlrNGYwN1I5eUx4ZmZ0ZTZWaUdYS0ZxQ2ZYU2JvSE1JKy9rWFVXNkRtaFhFdS9QdDdGQ2VkL2U4YkRubGFxalpsQ3IwSHc3YzBFeXJtTFBERVE5UEprKys5UlpxbFFWc0VyRHBhQ3c1N3lyZmxzK3drZzMxa2J3Y3NWdEwzQTQwbG12VGFXRk1Gak9PVElxczdXL3BkRjB0L2ZBdlB1d2dLQ2tMQzFyMnl3K1VwY3hUTEhxQWJPcC9VMmR1WjZPUXh0L3RzU1hrYURnPT0=
	</saml:AttributeValue>
            </saml:Attribute>
            <saml:Attribute Name="Dealer_ID">
                <saml:AttributeValue
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string">SP0125468708
	 </saml:AttributeValue>
            </saml:Attribute>
            <saml:Attribute Name="Created_date">
                <saml:AttributeValue
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string">2021-10-28
		</saml:AttributeValue>
            </saml:Attribute>
        </saml:AttributeStatement>
    </saml:Assertion>';

$method = 'AES-256-CBC';

$first_key = '12341234123412341234123412341234';

$iv = '';


$result = 	 openssl_encrypt($data,$method,$first_key, 0 ,$iv);




echo $result;exit;

 
 
 
 $fp=fopen("server.crt","r");
  $pub_key_string=fread($fp,8192);
  fclose($fp);
  
	$source = '12341234123412341234123412341234';
	
  echo openssl_public_encrypt($source,$crypttext, $pub_key_string );exit;
  
  $encryptedkey = '62676b58c9af97181be2c633a7330490d362d477be6dc13ce65d14cb0912197f8b8a1441f09bed85365d35c918b48eb1cf9039d60a9e3cba2dc85cc634476ba4';
  $key = '12341234123412341234123412341234';
  
 
 //$a = '{"type":"absolute","payuId":"403993715524585895","splitInfo":{"imAJ7I":{"aggregatorSubTxnId":"shl123","aggregatorSubAmt":"6300.00"},"qOoYIv":{"aggregatorSubTxnId":"shl90","aggregatorSubAmt":"22.00"}}}';
$a = '403993715524615258';


$hash_str = "A6lB8r|release_settlement|$a|c5KkKHlv"; 
 $hash = hash("sha512", $hash_str);
 echo $hash;exit;
?>
 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="author" content="Mahindra Insurance Brokers Ltd"/>
        <meta name="theme-color" content="#29344d">
        <title>PayBima</title>
        <link rel="canonical" href="https://miblnew.benefitz.in/motor/proposal/confirm/cholla_mandalam"/>
        <script> var nv=nv||function(){(window.nv.q=window.nv.q||[]).push(arguments)};nv.l=new Date;var notify_visitors=notify_visitors||function(){var e={initialize:!1,ab_overlay:!1,auth:{ bid_e:"32EF20BD32AF4697465758C358EFF2D4",bid:"8558",t:"420"}};return e.data={bid_e:e.auth.bid_e,bid:e.auth.bid,t:e.auth.t,iFrame:window!==window.parent,trafficSource:document.referrer,link_referrer:document.referrer,pageUrl:document.location,path:location.pathname,domain:location.origin,gmOffset:60*(new Date).getTimezoneOffset()-1,screenWidth:screen.width,screenHeight:screen.height,isPwa:window.matchMedia&&window.matchMedia("(display-mode: standalone)").matches?1:0,cookieData:document.cookie},e.options=function(t){t&&"object"==typeof t?e.ab_overlay=t.ab_overlay:console.log("Not a valid option")},e.tokens=function(t){e.data.tokens=t&&"object"==typeof t?JSON.stringify(t):""},e.ruleData=function(t){e.data.ruleData=t&&"object"==typeof t?JSON.stringify(t):""},e.getParams=function(e){url=window.location.href.toLowerCase(),e=e.replace(/[\[\]]/g,"\\${appText}").toLowerCase();var t=new RegExp("[?&]"+e+"(=([^&#])|&|#|$)").exec(url);return t&&t[2]?decodeURIComponent(t[2].replace(/\+/g," ")):""},e.init=function(){if(e.auth&&!e.initialize&&(e.data.storage=e.browserStorage(),e.js_callback="nv_json1",!e.data.iFrame&&"noapi"!==e.getParams("nvcheck"))){var t="?";if(e.ab_overlay){var o=document.createElement("style"),n="body{opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important;}",a=document.getElementsByTagName("head")[0];o.setAttribute("id","nv_hm_hidden_element"),o.setAttribute("type","text/css"),o.styleSheet?o.styleSheet.cssText=n:o.appendChild(document.createTextNode(n)),a.appendChild(o),setTimeout(function(){var e=this.document.getElementById("_nv_hm_hidden_element");if(e)try{e.parentNode.removeChild(e)}catch(t){e.remove()}},2e3)}for(var i in e.data)e.data.hasOwnProperty(i)&&(t+=encodeURIComponent(i)+"="+encodeURIComponent(e.data[i])+"&");e.load("https://www.notifyvisitors.com/ext/v1/settings"+t),e.initialize=!0}},e.browserStorage=function(){var t={session:e.storage("sessionStorage"),local:e.storage("localStorage")};return JSON.stringify(t)},e.storage=function(e){var t={};return window[e]&&window[e].length>0&&Object.keys(window[e]).forEach(function(o){-1!==o.indexOf("_nv")&&(t[o]=window[e][o])}),t},e.load=function(e){var t=document,o=t.createElement("script");o.src=e,o.type="text/javascript",t.body?t.body.appendChild(o):t.head.appendChild(o)},e}(); notify_visitors.options({ ab_overlay: false }); notify_visitors.init(); </script>
        <script> ( function (w,d,s,l,i){w[l]=w[l]||[];w[l].push({ 'gtm.start' : new Date ().getTime(),event: 'gtm.js' }); var f=d.getElementsByTagName(s)[ 0 ],j=d.createElement(s),dl=l!= 'dataLayer' ? '&l=' +l: '' ;j.async= true ;j.src='https://www.googletagmanager.com/gtm.js?id=' +i+dl;f.parentNode.insertBefore(j,f); })( window , document , 'script' , 'dataLayer' , 'GTM-NM3S5JV' ); </script>
        <script type="application/ld+json"> { "@context": "https://schema.org", "@type": "Organization", "name": "Paybima", "url": "https://www.paybima.com/", "logo": "https://www.paybima.com/public/assets_new/home_assets/images/img/logo.png", "address": { "@type": "PostalAddress", "addressCountry": "India", "addressLocality": "Worli", "addressRegion": " Mumbai ", "postalCode": "400018", "streetAddress": " Mahindra Insurance Brokers Limited (A Mahindra Group Company) Sadhana House, Ground Floor, 570 P. B. Marg, Behind Mahindra Towers"}, "email": "paybima.care@mahindra.com", "contactPoint": { "@type": "ContactPoint", "telephone": "1800 267 67 67", "contactType": "customer service", "areaServed": "IN", "availableLanguage": "en" }, "sameAs": [ "https://www.facebook.com/paybimaofficial/", "https://twitter.com/paybimaofficial", "https://www.youtube.com/channel/UCvqXg0gAeRsnjWVWNr_Um3Q", "https://www.instagram.com/paybima/" ] } </script>
        <script> var userMobile = btoa( localStorage.getItem("mobile_no") ); window .dataLayer = window .dataLayer || []; dataLayer.push({ 'event' : 'lsUserID' , 'lsUserID' : userMobile }); </script>
        <link rel="stylesheet" href="/public/assets_min/css/8d37f9fd79f4b30e3cd105035786a26a.min.css?v=1637311520"/>
        <style>.header-v1 .navbar-header {margin: 0 !important;}</style>
    </head>
    <body>
        <noscript>
            <iframe src= "https://www.googletagmanager.com/ns.html?id=GTM-NM3S5JV" height= "0" width= "0" style= "display:none;visibility:hidden" ></iframe>
        </noscript>
        <div class="main-page-wrapper" id="container">
            <div class="theme-main-menu theme-menu-two">
                <div class="logo">
                    <a href="/">
                        <img src="/public/assets_new/home_assets/images/img/logo_inner.webp" alt="Paybima by Mahindra Insurance Brokers">
                    </a>
                </div>
                <nav id="mega-menu-holder" class="navbar navbar-expand-lg">
                    <div class="ml-auto nav-container">
                        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="flaticon-setup"></i>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <p class="top_header">
                                <a href="mailto:paybima.care@mahindra.com" class="blue_m">
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i> paybima.care@mahindra.com
                                </a>
                                <a href="tel:1800 267 67 67" class="blue_m">
                                    <i class="fa fa-phone" aria-hidden="true"></i> 1800 267 67 67
                                </a>
                            </p>
                        </ul>
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown position-relative">
                                <a class="nav-link dropdown-toggle" href="/health-insurance/input" data-toggle="dropdown">Health 
                                    <i class="fa fa-angle-down hidden-xs"></i>
                                </a>
                                <ul class="dropdown-menu health_plan">
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/health-insurance/input">Individual</a>
                                    </li>
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/health-insurance/input">Family</a>
                                    </li>
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/super-top-up-health-insurance">Super Top-up</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown position-relative">
                                <a class="nav-link dropdown-toggle" href="/term-life-insurance-plans-online" data-toggle="dropdown">Life 
                                    <i class="fa fa-angle-down hidden-xs"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/term-life-insurance-plans-online">Term</a>
                                    </li>
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/saral-jeevan-bima">Saral Jeevan Bima </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown position-relative">
                                <a class="nav-link dropdown-toggle" href="/car-insurance/input" data-toggle="dropdown">Motor 
                                    <i class="fa fa-angle-down hidden-xs"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/car-insurance/input">Car</a>
                                    </li>
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/bike-insurance/input">Bike</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown position-relative">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Investment Plan 
                                    <i class="fa fa-angle-down hidden-xs"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/ulip_insurance/input">ULIP </a>
                                    </li>
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/guaranteed-return-plans">Guaranteed Return Plan </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown position-relative">
                                <a class="nav-link" href="/claims">Claims</a>
                            </li>
                            <li class="nav-item dropdown position-relative">
                                <a class="nav-link" href="/blogs">Blog</a>
                            </li>
                            <li class="nav-item dropdown simple-dropdown position-relative">
                                <a class="nav-link dropdown-toggle" href="javascript:void(0);" data-toggle="dropdown" aria-hidden="true">Login 
                                    <i class="fa fa-angle-down hidden-xs"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu dropdown">
                                        <a class="dropdown-item" href="/sign-in">Customer</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <head>
            <title>PayBima</title>
        </head>
        <div class="container-fluid ">
            <div class="container">
                <div class="row">
                    <div class="">
                        <div class="inner-wrapper pt-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <img class="img-responsive img-center" style="height:290px;" src="https://miblnew.benefitz.in/public/assets_new/home_assets/images/img/error.png"/>
                                    </div>
                                    <div class="col-md-8">
                                        <h2 class='color-blue'>Transaction Failed </h2>
                                        <p class='color-blue'>
                                            <strong>Your transaction was unsuccessful !!!</strong>
                                        </p>
                                        <h3 class='color-dark-blue padding-top-10 text-left'>Oops! Something went wrong. Process could not be completed, please ensure the information you provided is correct.</h3>
                                        <p>Any questions? Call us on our toll-free-number: 
                                            <a href="tel:1800 267 67 67">1800 267 67 67</a>.
                                        </p>
                                        <p>Email us: 
                                            <a href="mailto:paybima.care@mahindra.com">paybima.care@mahindra.com</a>
                                        </p>
                                        <br>
                                        <a href="https://miblnew.benefitz.in/car-insurance/input" class="back-button solid-button-one mar_thank_btn"> Buy another policy</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style> footer p{margin-bottom: 0;} </style>
    <footer class="theme-footer-one">
        <div class="container bottom_box_footer">
            <div class="bottom-footer " data-aos="zoom-in-right">
                <div class="clearfix">
                    <p>Get regular 
                        <b>updates, 
                            <br> offers and much more
                        </b>
                    </p>
                    <div class="signUp-page signUp-standard">
                        <div class="row" style="display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap !important;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;">
                            <div class="col-lg-12">
                                <div class="signin-form-wrapper">
                                    <form action="#" id="login-form" class="no_validate">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5">
                                                <div class="input-group">
                                                    <input type="text" name="email" id="emailid" >
                                                    <label>Your email </label>
                                                </div>
                                                <div class="invalid-email">
                                                    <span id="invalid_email"></span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <button name="subscribe_btn" id="subscribe_btn" class="solid-button-one">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="top-footer bg_blue hidden-xs">
            <div class="offset-md-1 col-md-11 mb-12">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 footer-list aos-init aos-animate" data-aos="fade-up">
                        <h5 class="title">Motor Insurance</h5>
                        <ul>
                            <li>
                                <a href="/car-insurance/input">Car Insurance</a>
                            </li>
                            <li>
                                <a href="/bike-insurance/input">Bike Insurance</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 footer-list aos-init aos-animate" data-aos="fade-up">
                        <h5 class="title">Health Insurance</h5>
                        <ul>
                            <li>
                                <a href="/medical-health-insurance-plans-online">Individual Health Insurance</a>
                            </li>
                            <li>
                                <a href="/medical-health-insurance-plans-online">Family Health Insurance</a>
                            </li>
                            <li>
                                <a href="/super-top-up-health-insurance">Super Top-up</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 footer-list aos-init aos-animate" data-aos="fade-up">
                        <h5 class="title">Life Insurance</h5>
                        <ul>
                            <li>
                                <a href="/term-life-insurance-plans-online">Term Insurance</a>
                            </li>
                            <li>
                                <a href="/saral-jeevan-bima">Saral Jeevan Bima</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 footer-list aos-init aos-animate" data-aos="fade-up">
                        <h5 class="title">Investment Plan </h5>
                        <ul>
                            <li>
                                <a href="/ulip_insurance/input">ULIP </a>
                            </li>
                            <li>
                                <a href="/guaranteed-return-plans">Guaranteed Return Plan </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 no_padding aos-init aos-animate" data-aos="fade-up">
                        <div class=" footer-list">
                            <h5 class="title">Group Companies</h5>
                            <ul>
                                <li>
                                    <a href="https://www.mahindra.com/" rel="nofollow" target="_blank">Mahindra Group</a>
                                </li>
                                <li>
                                    <a href="https://mahindrafinance.com/" rel="nofollow" target="_blank">Mahindra Finance</a>
                                </li>
                                <li>
                                    <a href="https://www.mahindrainsurance.com/" rel="nofollow" target="_blank">Mahindra Insurance Brokers</a>
                                </li>
                                <li>
                                    <a href="https://www.mahindrahomefinance.com/" rel="nofollow" target="_blank">Mahindra Home Finance</a>
                                </li>
                                <li>
                                    <a href="https://www.mahindramanulife.com/" rel="nofollow" target="_blank">Mahindra Manulife Mutual Fund </a>
                                </li>
                                <li>
                                    <a href=" https://www.mahindralifespaces.com/" rel="nofollow" target="_blank">Mahindra Lifespaces</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="top-footer hidden-lg">
            <div class="row justify-content-md-center">
                <div class="col-lg-7 col-md-9">
                    <div class="faq-tab-wrapper-three mt-25">
                        <div class="faq-panel">
                            <div class="panel-group theme-accordion" id="accordion-three">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-three" href="#collapse31"> Motor Insurance</a>
                                        </h6>
                                    </div>
                                    <div id="collapse31" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-lg-3 col-sm-6 col-12 footer-list" data-aos="fade-up">
                                                <ul>
                                                    <li>
                                                        <a href="/car-insurance/input">Car Insurance</a>
                                                    </li>
                                                    <li>
                                                        <a href="/bike-insurance/input">Bike Insurance</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-three" href="#collapse32"> Health Insurance</a>
                                        </h6>
                                    </div>
                                    <div id="collapse32" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-lg-3 col-lg-3 col-sm-6 col-12 footer-list" data-aos="fade-up">
                                                <ul>
                                                    <li>
                                                        <a href="/medical-health-insurance-plans-online">Individual Health Insurance</a>
                                                    </li>
                                                    <li>
                                                        <a href="/medical-health-insurance-plans-online">Family Health Insurance</a>
                                                    </li>
                                                    <li>
                                                        <a href="/corona-kavach-policy/input">Corona Kavach</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-three" href="#collapse323"> Life Insurance</a>
                                        </h6>
                                    </div>
                                    <div id="collapse323" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-lg-3 col-lg-3 col-sm-6 col-12 footer-list" data-aos="fade-up">
                                                <ul>
                                                    <li>
                                                        <a href="/term-life-insurance-plans-online">Term Insurance</a>
                                                    </li>
                                                    <li>
                                                        <a href="/guaranteed-return-plans">Guaranteed Return Plan</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-three" href="#collapse355"> Group Companies</a>
                                        </h6>
                                    </div>
                                    <div id="collapse355" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-lg-3 col-sm-6 col-12 footer-list" data-aos="fade-up">
                                                <ul>
                                                    <li>
                                                        <a href="https://www.mahindra.com/" rel="nofollow" target="_blank">Mahindra Group</a>
                                                    </li>
                                                    <li>
                                                        <a href="https://mahindrafinance.com/" rel="nofollow" target="_blank">Mahindra Finance</a>
                                                    </li>
                                                    <li>
                                                        <a href="https://www.mahindrainsurance.com/" rel="nofollow" target="_blank">Mahindra Insurance Brokers</a>
                                                    </li>
                                                    <li>
                                                        <a href="https://www.mahindrahomefinance.com/" rel="nofollow" target="_blank">Mahindra Home Finance</a>
                                                    </li>
                                                    <li>
                                                        <a href="https://www.mahindramanulife.com/" rel="nofollow" target="_blank">Mahindra Manulife Mutual Fund </a>
                                                    </li>
                                                    <li>
                                                        <a href=" https://www.mahindralifespaces.com/" rel="nofollow" target="_blank">Mahindra Lifespaces</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row justify-content-md-center">
            <div class="theme-footer-one footer_social hidden-lg">
                <div class="top-footer">
                    <div class="footer-information aos-init aos-animate" data-aos="fade-up">
                        <h5 class="title text-center">Follow Us</h5>
                        <ul class="text-center">
                            <li>
                                <a href="https://www.facebook.com/PayBimaofficial/" rel="nofollow" target="_blank">
                                    <i class="fa fa-facebook" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/PayBimaofficial" rel="nofollow" target="_blank">
                                    <i class="fa fa-twitter" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/PayBima/" rel="nofollow" target="_blank">
                                    <i class="fa fa-instagram" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.youtube.com/channel/UCvqXg0gAeRsnjWVWNr_Um3Q" rel="nofollow"target="_blank">
                                    <i class="fa fa-youtube-play" aria-hidden="true"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container bg_blue" style="padding: 0;">
            <p class="text-center side_border_top text-white">
                <span class="side_border text-white txtlink">
                    <a class="text-white" href="/about-us" target="_blank">About Us</a>
                </span>
                <span class="side_border text-white">
                    <a class="text-white" href="/copyright" target="_blank">Copyright</a>
                </span>
                <span class="side_border text-white">
                    <a class="text-white" href="/terms-conditions" target="_blank">Terms of use</a>
                </span>
                <span class="side_border text-white">
                    <a class="text-white" href="/privacy-policy" target="_blank">Privacy Policy</a>
                </span>
                <span class="side_border text-white">
                    <a class="text-white" href="/contact-us" target="_blank">Contact Us</a>
                </span>
                <span class="side_border text-white">
                    <a class="text-white" href="/disclaimer" target="_blank">Disclaimer</a>
                </span>
                <span class="side_border text-white">
                    <a class="text-white" href="https://www.irdai.gov.in/" target="_blank">IRDAI</a>
                </span>
                <span class="side_border_none text-white">
                    <a class="text-white" href="/blogs" target="_blank">Blog</a>
                </span>
            </p>
            <div class="row justify-content-center pt-3" style="display: flex; justify-content: center;">
                <div class="col-md-6 col-6 text-right ml-44">
                    <img src="/public/assets_new/home_assets/images/img/ssl.png" class="lazy" alt="SSL Secured" style="margin-left: auto;"> 
                </div>
                <div class="col-md-6 col-6">
                    <a href="https://www.paybima.com/public/assets_new/home_assets/files/IRDAI_Renewal_License_2019-22.pdf" target="_blank">
                        <img src="/public/assets_new/home_assets/images/img/irdai.png" class="lazy" alt="Insurance Regulatory and Development Authority: IRDAI">
                    </a>
                </div>
            </div>
            <p class="text-center text-white">Corporate Office : Mahindra Insurance Brokers Ltd ( A Mahindra Group Company ) Sadhana House, Ground Floor, 570 P. B. Marg, Behind Mahindra Towers, Worli, Mumbai 400018.</p>
            <div class="row justify-content-md-center">
                <div class="theme-footer-one footer_social hidden-xs">
                    <div class="top-footer">
                        <div class="footer-information">
                            <ul class="text-center" style="margin-bottom: 0;">
                                <li>
                                    <a href="https://www.facebook.com/PayBimaofficial/" rel="nofollow" target="_blank">
                                        <i class="fa fa-facebook" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/PayBimaofficial" rel="nofollow" target="_blank">
                                        <i class="fa fa-twitter" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/PayBima/" rel="nofollow" target="_blank">
                                        <i class="fa fa-instagram" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.youtube.com/channel/UCvqXg0gAeRsnjWVWNr_Um3Q" rel="nofollow" target="_blank">
                                        <i class="fa fa-youtube-play" aria-hidden="true"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center text-white">
                <a href="/public/assets_new/home_assets/files/IRDAI_Renewal_License_2019-22.pdf" class="text-white" target="_blank">Licenced by IRDAI License No. 261; License Validity : 17-05-2022;</a> Category : Composite Broker; CIN : U65990MH1987PLCO42609 Member of Insurance Brokers Association of India (IBAI). Insurance is the subject matter of solicitation.
            </p>
            <p class="text-center text-white">For a seamless experience, use the latest version of Chrome/Firefox/Internet Explorer. </p>
            <p class="text-center text-white">Copyright © 2020 Mahindra Insurance Brokers. All Rights Reserved. </p>
        </div>
    </footer>
    <div id="m-a-a" class="modal fade">
        <div class="modal-dialog animate" id="animate">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: none !important;">
                    <a class="modal-title" data-dismiss="modal" style=" float: right; right: 15px; position: absolute;">
                        <i class="fa fa-close"></i>
                    </a>
                </div>
                <div class="modal-body text-center p-lg">
                    <div class="row">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-4 text-center">
                            <img src="/public/assets_new/home_assets/images/img/correct.webp" width="140"> 
                        </div>
                        <div class="col-md-4">&nbsp;</div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3>Thank You!</h3>
                            <p>Your submission has been received.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> function getID(){ var currentURL=window.location.toString(); var matches = currentURL.match(/(\d+)/); if (matches) { if ( matches[0].length=13){ return matches[0] } } return ''; } window.lhnJsSdkInit = function () { lhnJsSdk.setup = { application_id: "5ca670cc-da9a-44be-8367-531d2d0cf469", application_secret: "nh+ysbzhwqdot/lamuc1lwlno2ojomm30nr10odcpabgpafzoz" }; lhnJsSdk.controls = [{ type: "hoc", id: "ca2251b5-dbf5-4f9f-c3ca-d7da21a0a16c" }]; lhnJsSdk.options = { custom1: getID() } }; (function (d, s) { var newjs, lhnjs = d.getElementsByTagName(s)[0]; newjs = d.createElement(s); newjs.src = "https://developer.livehelpnow.net/js/sdk/lhn-jssdk-current.min.js"; lhnjs.parentNode.insertBefore(newjs, lhnjs); }(document, "script")); </script>
<script src="/public/assets_min/js/24f328555b9a9e9590adda74175bdfb1.min.js?v=1637311520" type="application/javascript" defer="defer"></script>
<script src="/public/assets_min/js/9b428f3a946fbd886f35f48ad9444e09.min.js?v=1637311520" type="application/javascript" defer="defer"></script>
<script src="/public/assets_min/js/3538abebe6026430d503305dab5ea923.min.js?v=1637311520" type="application/javascript" defer="defer"></script>
<script src="/public/assets_min/js/74cbf45325613b15ca8f9b1d79435172.min.js?v=1637311520" type="application/javascript" defer="defer"></script>
</body>
</html>
<form action='https://test.payu.in/_payment'  method='post' name ="payuForm">
    <input type="hidden" name="firstname" value="SWATI" />
    <br>
    <input type="hidden" name="lastname" value="QA" />
    <br>
    <input type="hidden" name="surl" value="http://uatvahaan.fynity.in/api/payment_response_payu" />
    <br>
    <input type="hidden" name="phone" value="8055423489" />
    <br>
    <input type="hidden" name="key" value="A6lB8r" />
    <br>
    <input type="hidden" name="hash" value="fff44b2737a65f792c1b65ca9575145ed114f6c0de04aeda1be91d223c4be778f810807b9b21794e28d191628edaa279e29a345c6fc571e7168d407cbc8a327e" />
    <br>
    <input type="hidden" name="furl" value="http://uatvahaan.fynity.in/api/payment_response_payu" />
    <br>
    <input type="hidden" name="txnid" value="ae9d964a-3c27-4307-8bf0-fac3b09d62fa-hdfc-motor-19/11/2021" />
    <br>
    <input type="hidden" name="productinfo" value="PAYMENT GATEWAY API" />
    <br>
    <input type="hidden" name="amount" value="6322.00" />
    <br>
    <input type="hidden" name="email" value="swati19@gmail.com"/>
    <br>
    <input type= "submit" value="submit"  style ='visibility:hidden'> 
</form>
<script type="text/javascript">
// var payuForm = document.forms.payuForm;
//payuForm.submit();
</script>