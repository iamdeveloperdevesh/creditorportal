<style>
  .card-si {
    width: 81%;
  margin:0 auto;
  }

  .btn-premium {
    margin-top: 2px;
    float: none;
    margin-left: 0;
  }

  <?php
  // error_reporting('0');
  ?>
</style>
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
  <div class="container cont-product">
    <div class="product-msg mt-4">
      <div class="col-md-8 offset-md-2">
        <div class="card card-product  pdt-11">
          <div class="card-body text-center">
            <span class="pro-text">All Rounder Health Insurance Covers, <span class="txt-brand"> Exclusively for you!</span> </span><img class="pro-img" src="/assets/abc/images/product-select.png" width="60">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container mt-3 offset-md-2">
    <div class="row">
      <?php
      $plan_id = 0;
      ?>
      <?php foreach ($data as $product_name => $product_data) { ?>
        <div class="col-lg-4 mt-3 col-md-6 col-12 col-sm-6 mt-5">
          <div class="card-si"> GHI 25 Lacs - GPA 25 Lacs - GCI 5 Lacs <br> <button class="btn btn-premium">Premium starting from 13,648 /- </button> </div>
          <div class="card card1">
            <div class="card-body">
              <div class="product-head2">
                <span class="img-pro-2"><img src="/assets/abc/images/combo.png" width="34"></span>
                <span class="ml-2"><?php echo $product_name; ?></span>
              </div>
              <div class="border-pro"></div>
              <div class="product-body">
                <div style="height: auto;" class="button-ul-group">
                  <!-- <div><button class="btn fam-btn">Self</button></div> -->
                  <ul style="height: auto;" class="pro-list pro-height mt-2">
                    <?php foreach ($product_data as $policy_sub_type_id => $policy_data) {

                      $key_str = '';
                      foreach ($policy_data as $key  => $value) {

                        $plan_id = $value['plan_id'];
                        $code = $value['code'];
                        $key_str .= $key . ", ";
                      }

                    ?>
                      <li> <i class="fa fa-check"></i><?php echo $code . ":" . rtrim($key_str, ', '); ?></li>
                    <?php
                    } ?>
                  </ul>
                </div>
                <div class="row">
                  <div class="text-center col-md-12"><a class="ClickBtn" href="<?php echo base_url('generate_quote/' . encrypt_decrypt_password($plan_id)); ?>"><button class="btn btn-buynow ClickBtn">Buy Now <i class="ti-arrow-circle-right"></i></button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>
</div>
<br>
<!-- main content area end -->
<script>
  $(document).ready(function($) {
    var maxHeight = Math.max.apply(null, $(".button-ul-group").map(function() {
      return $(this).height();
    }).get());
    $(".button-ul-group").height(maxHeight);
  });
</script>
<?php
$js_files = [
  // 'public/assets/abc/js/abc_comprehensive.js',
  // 'public/js_events/other_msg_abc.js',          
];
//if($this->product_id == 'ABC') {
$js_files[] = 'public/assets/abc/js/abc_comprehensive.js';
//}
// Globals::setJs(minify_resources($js_files, 'js', 'comprehensive_page'));
?>