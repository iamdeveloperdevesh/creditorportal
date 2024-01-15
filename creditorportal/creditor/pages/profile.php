<?php require_once('inc/header.php') ?>

<body>

<?php require_once('inc/navigation.php')?>

<!-- breadcrumb start -->
<!-- <div class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="page-title">
                    <h2>profile</h2>
                </div>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb" class="theme-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div> -->
<!-- breadcrumb End -->


<!-- personal deatail section start -->
<section class="contact-page register-page">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3>PERSONAL DETAILS</h3>
                <form class="theme-form">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="name">First Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Your name" required="">
                        </div>
                        <div class="col-md-6">
                            <label for="email">Last Name</label>
                            <input type="text" class="form-control" id="last-name" placeholder="Email" required="">
                        </div>
                        <div class="col-md-6">
                            <label for="review">Phone number</label>
                            <input type="text" class="form-control" id="review" placeholder="Enter your number" required="">
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" placeholder="Email" required="">
                        </div>                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->


<!-- address section start -->
<section class="contact-page register-page section-b-space">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3>SHIPPING ADDRESS</h3>
                <form class="theme-form">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="name">flat no. / plot no.</label>
                            <input type="text" class="form-control" id="home-ploat" placeholder="Flat no./ Plot no." required="">
                        </div>
                        <div class="col-md-6">
                            <label for="name">Address *</label>
                            <input type="text" class="form-control" id="address-two" placeholder="Address" required="">
                        </div>
                        <div class="col-md-6">
                            <label for="email">Zip Code *</label>
                            <input type="text" class="form-control" id="zip-code" placeholder="zip-code" required="">
                        </div>
                        <div class="col-md-6 select_input">
                            <label for="review">Country *</label>
                            <select class="form-control" size="1">
                                <option value="India">India</option>
                                <option value="UAE">UAE</option>
                                <option value="U.K">U.K</option>
                                <option value="US">US</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="review">City *</label>
                            <input type="text" class="form-control" id="city" placeholder="City" required="">
                        </div>
                        <div class="col-md-6">
                            <label for="review">Region/State *</label>
                            <input type="text" class="form-control" id="region-state" placeholder="Region/state" required="">
                        </div>
                        <div class="col-md-12">
                            <input type="checkbox" id="gst-required">
                            <label for="gst-required">GST Invoice Required</label>
                        </div>
                        <div class="col-md-6 gst-show">
                            <label for="company-name">Company Name *</label>
                            <input type="text" class="form-control" id="company-name" placeholder="Company Name" required="">
                        </div>                        
                        <div class="col-md-6 gst-show">
                            <label for="gst-number">GST Number *</label>
                            <input type="text" class="form-control" id="gst-number" placeholder="GST Number" required="">
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-solid" type="submit">Save settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->


<?php require_once('inc/footer.php') ?>

<?php require_once('inc/footer-scripts.php')?>

</body>

</html>