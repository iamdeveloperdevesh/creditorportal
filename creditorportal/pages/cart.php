<?php require_once('inc/header.php') ?>

<body>

<?php require_once('inc/navigation.php')?>

<!-- breadcrumb start -->
<!-- <div class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="page-title">
                    <h2>cart</h2></div>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb" class="theme-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active">cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div> -->
<!-- breadcrumb End -->


<!--section start-->
<section class="cart-section section-b-space">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <table class="table cart-table table-responsive-xs">
                    <thead>
                    <tr class="table-head">
                        <th scope="col">image</th>
                        <th scope="col">product name</th>
                        <th scope="col">price</th>
                        <th scope="col">quantity</th>
                        <th scope="col">action</th>
                        <th scope="col">total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <a href="#"><img src="../assets/images/pro3/1.jpg" alt=""></a>
                        </td>
                        <td><a href="#">Cashewnuts<br/>Weight: 1000gms, Type: Roasted</a>
                            <div class="mobile-cart-content row">
                                <div class="col-xs-3">
                                    <div class="qty-box">
                                        <div class="input-group">
                                            <input type="text" name="quantity" class="form-control input-number" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <h2 class="td-color">&#8377; 63.00</h2></div>
                                <div class="col-xs-3">
                                    <h2 class="td-color"><a href="#" class="icon"><i class="ti-close"></i></a></h2></div>
                            </div>
                        </td>
                        <td>
                            <h2>&#8377; 63.00</h2></td>
                        <td>
                            <div class="qty-box">
                                <div class="input-group">
                                    <input type="number" name="quantity" class="form-control input-number" value="1">
                                </div>
                            </div>
                        </td>
                        <td><a href="#" class="icon"><i class="ti-close"></i></a></td>
                        <td>
                            <h2 class="td-color">&#8377; 4539.00</h2></td>
                    </tr>
                    </tbody>
                    <tbody>
                    <tr>
                        <td>
                            <a href="#"><img src="../assets/images/pro3/1.jpg" alt=""></a>
                        </td>
                        <td><a href="#">Almonds<br/>Weight: 2000gms, Type: AAA</a>
                            <div class="mobile-cart-content row">
                                <div class="col-xs-3">
                                    <div class="qty-box">
                                        <div class="input-group">
                                            <input type="number" name="quantity" class="form-control input-number" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <h2 class="td-color">&#8377; 63.00</h2></div>
                                <div class="col-xs-3">
                                    <h2 class="td-color"><a href="#" class="icon"><i class="ti-close"></i></a></h2></div>
                            </div>
                        </td>
                        <td>
                            <h2>&#8377; 63.00</h2></td>
                        <td>
                            <div class="qty-box">
                                <div class="input-group">
                                    <input type="number" name="quantity" class="form-control input-number" value="1">
                                </div>
                            </div>
                        </td>
                        <td><a href="#" class="icon"><i class="ti-close"></i></a></td>
                        <td>
                            <h2 class="td-color">&#8377; 4539.00</h2></td>
                    </tr>
                    </tbody>
                    <tbody>
                    <tr>
                        <td>
                            <a href="#"><img src="../assets/images/pro3/1.jpg" alt=""></a>
                        </td>
                        <td><a href="#">Anjeer<br/>Weight: 500gms, Type: A</a>
                            <div class="mobile-cart-content row">
                                <div class="col-xs-3">
                                    <div class="qty-box">
                                        <div class="input-group">
                                            <input type="number" name="quantity" class="form-control input-number" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <h2 class="td-color">&#8377; 63.00</h2></div>
                                <div class="col-xs-3">
                                    <h2 class="td-color"><a href="#" class="icon"><i class="ti-close"></i></a></h2></div>
                            </div>
                        </td>
                        <td>
                            <h2>&#8377; 63.00</h2></td>
                        <td>
                            <div class="qty-box">
                                <div class="input-group">
                                    <input type="number" name="quantity" class="form-control input-number" value="1">
                                </div>
                            </div>
                        </td>
                        <td><a href="#" class="icon"><i class="ti-close"></i></a></td>
                        <td>
                            <h2 class="td-color">&#8377; 4539.00</h2></td>
                    </tr>
                    </tbody>
                </table>
                <table class="table cart-table table-responsive-md">
                    <tfoot>
                    <tr>
                        <td>total price :</td>
                        <td>
                            <h2>&#8377; 6935.00</h2></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row cart-buttons">
            <div class="col-6"><a href="#" class="btn btn-solid">continue shopping</a></div>
            <div class="col-6"><a href="#" class="btn btn-solid">check out</a></div>
        </div>
    </div>
</section>
<!--section end-->

<?php require_once('inc/footer.php') ?>

<?php require_once('inc/footer-scripts.php')?>

</body>

</html>