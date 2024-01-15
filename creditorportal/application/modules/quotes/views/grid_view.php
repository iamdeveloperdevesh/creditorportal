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

  #vw-more {
    display: none;
  }

  /* New css 17-01-2023 */

  .product-head2{
      display: flex;
  }

  span.ml-2.tl-ml-2.product-title {
      position: relative;
  }

  .prodcFeature .prodcFeatureCard .greenCheck{
      position: relative;
      left:0px;

  }
  .prodcFeature .prodcFeatureCard {
      height: 227px;
      overflow: hidden;
      overflow-y: auto;
  }

  .prodcFeature .prodcFeatureCard li.mt-1 {
      display: flex;
  }
  .prodcFeature .prodcFeatureCard::-webkit-scrollbar {
      width: 4px;
      background: #96a4b5;
      border-radius: 30px;
      height: 6px;
      display: none;
  }
  /*.btn-buynow{
      background-color: #F2581B !important;
  }*/
  /* New css 17-01-2023 */

  <?php
  // error_reporting('0');
  ?>
</style>
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
  <div class="container cont-product">
    <div class="product-msg mt-4">
      <?php 
      $url = '';
          if(!empty($this->session->userdata('partner_id_session'))){
            $url .= '?partner='.$this->session->userdata('partner_id_session');
          }
          if(!empty($this->session->userdata('product_id_session'))){
            $url .= '&product='.$this->session->userdata('product_id_session');
          }

          $url .= !empty($url)?'&lead_id=':'?lead_id=';
          $url .= $_REQUEST['lead_id'];
          
      ?>
      <a href="<?php echo base_url();?>customerportal<?php echo $url;?>">
          <p class="go_back_prposal_p"><i class="fa fa-long-arrow-left" style="width: 27px;"></i> Go Back</p>
      </a>
      <div class="col-md-8 offset-md-2">
        <div class="card card-product  pdt-11">
          <div class="card-body text-center">
              <?php

              if(isset($_SESSION['linkUI_configuaration']) && (!empty($_SESSION['linkUI_configuaration'][0]['quote_header_text']))){
                  ?>
                  <span class="pro-text">
                                        <?php
                                        echo $this->session->userdata('linkUI_configuaration')[0]['quote_header_text'];?></span></span><img class="pro-img" src="/assets/abc/images/product-select.png" width="60">

                  <?php
              }else{ ?>
                  <span class="pro-text"> All Rounder Health Insurance Covers, <span class="txt-brand"> Exclusively for you!</span> </span><img class="pro-img" src="/assets/abc/images/product-select.png" width="60">
                  <?php

              }
              ?>          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container mt-3 ">
    <div class="row">
      <?php
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      //echo "<pre>";
      //print_r($group_by_policies);
      //print_r($sum_insured_arr);exit;

      function getIndianCurrency(float $number)
      {
          $decimal = round($number - ($no = floor($number)), 2) * 100;
          $hundred = null;
          $digits_length = strlen($no);
          $i = 0;
          $str = array();
          $words = array(0 => '', 1 => 'one', 2 => 'two',
              3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
              7 => 'seven', 8 => 'eight', 9 => 'nine',
              10 => 'ten', 11 => 'eleven', 12 => 'twelve',
              13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
              16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
              19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
              40 => 'forty', 50 => 'fifty', 60 => 'sixty',
              70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
          $digits = array('', 'Hundred','Thousand','Lac', 'Crore');
          while( $i < $digits_length ) {
              $divider = ($i == 2) ? 10 : 100;
              $number = floor($no % $divider);
              $no = floor($no / $divider);
              $i += $divider == 10 ? 1 : 2;
              if ($number) {
                  $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                  $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                  
                  //$str [] = ($number < 21) ? $number.' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                  $str [] = ($number < 21) ? $number.' '. $digits[$counter]. $plural.' '.$hundred:trim(floor($number / 10) * 10).' '.$digits[$counter].$plural.' '.$hundred;
              } else $str[] = null;
          }
          $Rupees = implode('', array_reverse($str));
          $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
          return ($Rupees ? $Rupees  : '') . $paise;
      }
      
      $plan_id = 0;
      ?>
      <?php
      //var_dump($group_by_policies);exit;
      foreach ($group_by_policies as $key => $value) {




          ?>
        <div class="col-lg-4 col-md-6 col-12 col-sm-6 m-77">
            <?php  if(!is_null($value['suminsured'][0]['sum_insured'])){ ?>
          <div class="card-si"> Cover <?php echo getIndianCurrency($value['suminsured'][0]['sum_insured']);?><br> <button class="btn btn-premium">Premium starting from <?php echo $value['suminsured'][0]['rate']; ?> /- </button> </div>
          <?php } ?>
          <div class="card login-card">
            <div class="card-body">
              <div class="product-head2">
                  <?php
                  if(isset($_SESSION['linkUI_configuaration'])){
                      $image= $this->session->userdata('linkUI_configuaration')[0]['quote_card_image'];
                      ?>
                      <span class="img-pro-2"><img src="<?php echo $image; ?>" width="34"></span>

                      <?php
                  }else{ ?>
                      <span class="img-pro-2"><img src="/assets/abc/images/combo.png" width="34"></span>
                  <?php  }
                  ?>

                <span class="ml-2 tl-ml-2 product-title"><?php echo $value['plan_name'] ; ?></span>
                  
              </div>
              <div class="border-pro"></div>
              <div class="product-body">
                <div style="height: auto;" class="button-ul-group">
                  <div class="mt-4">
                    <?php
                    $members_string = "";
                    foreach ($getQuotePageData['member_ages'] as $single_member) { ?>
                      <button class="btn fam-btn"><?php echo $single_member['member_type']; ?></button>
                    <?php } ?>
                  </div>
                <div class="prodcFeature">
                  <ul class="pro-list pro-height mt-2 prodcFeatureCard">
                <!--    <li class="mt-1"> <i class="fa fa-check"></i> Inpatient Hospitalization covered </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> 527-day care procedures covered </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> Organ Donor expenses covered. </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> Pre & post hospitalisation cover. </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> Ambulance charges covered. </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> Initial 30 Days waiting period except Claims related to Accident. </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> 2 years waiting periods for specified illness. </li>
                    <li class="mt-1"> <i class="fa fa-check"></i> Waiting period of 3 years for pre-existing diseases. </li>
-->

                      <?php
                      if(count($value['features']) > 0){
                      foreach ($value['features'] as $arrFeature) { ?>

                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i> <span><?php echo strip_tags($arrFeature['long_description']); ?>
                                  <?php if(!empty($arrFeature['short_description'])){
                                      echo "-".$arrFeature['short_description'];
                          } ?></span></li>


                      <?php }
                      }else{ ?>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i><span> Inpatient Hospitalization covered</span> </li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i><span> 527-day care procedures covered </span></li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i> <span> Organ Donor expenses covered. </span></li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i> <span>Pre & post hospitalisation cover.</span> </li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i><span> Ambulance charges covered. </span></li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i> <span>Initial 30 Days waiting period except Claims related to Accident. </span></li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i> <span> 2 years waiting periods for specified illness. </span></li>
                          <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i><span> Waiting period of 3 years for pre-existing diseases.</span> </li>

                     <?php }
                      ?>
                  </ul>
                </div>
                </div>
                <div class="row">
                   <div class="text-center col-md-12">
                     <?php
                  if(isset($_SESSION['linkUI_configuaration'])){
                      $know_more_pdf= $this->session->userdata('linkUI_configuaration')[0]['know_more_pdf'];
                      ?>
                      <a href="<?php echo $know_more_pdf; ?>"  target="_blank"><button class="btn btn-link" >Know More..</button></a>

                      <?php
                  }?>
                  </div>
                  <div class="text-center col-md-12">
                    <input type="hidden" name="plan_id" value="<?php echo $value['suminsured'][0]['sum_insured'];?>" id="compare_one_<?php echo encrypt_decrypt_password($value['policy_id']); ?>_cover">
                    <input type="hidden" name="creditor_id" value = "<?php echo $value['creaditor_name'] . '-' . $value['plan_name']; ?>" id="compare_one_<?php echo encrypt_decrypt_password($value['policy_id']); ?>_plan_name">
                    <input type="hidden" name="plan_id" value = "<?php echo $value['suminsured'][0]['rate']; ?>" id="select_premium_<?php echo encrypt_decrypt_password($value['policy_id']); ?>">
                    
                    <a href="javascript:void(0)" class="cardBuyNow" data-planid="<?php echo $value['plan_id']; ?>" data-policyid="<?php echo encrypt_decrypt_password($value['policy_id']); ?>">
                      <button class="btn btn-buynow ClickBtn mt-2">Buy Now <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>

                    </a>
                     <!-- <button type="button" class="btn btn-buynow  mt-2" ty onclick="PolicyIssue()">Buy Now <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>-->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php

          } ?>

    </div>
  </div>
</div>
<br>
<form action="<?php echo base_url() . "quotes/generate_quote_abc?lead_id=".$_REQUEST['lead_id'] ?>" id="hiddenCardForm" method="POST">
  <input type="hidden" name="plan_id" id="hiddenplanid">
  <input type="hidden" name="cover" id="hiddencover">
  <input type="hidden" name="premium" id="hiddenpremium">
  <input type="hidden" name="plan_name" id="hiddenplanname">
  <input type="hidden" name="policy_id" id="hiddenpolicyid">
    <input type="hidden" name="si_type_id" id="hiddensi_type_id" value = "<?php echo $si_type_id;?>">

  <input type="submit" style="display:none;">
</form>

<input type="hidden" id="plans_for_count_get" value="<?php echo count($group_by_policies); ?>">
<script type="text/javascript">
  $(document).ready(function() {
    $(document).on("click", ".cardBuyNow", function() {
      
      var policy_id = $(this).data("policyid");

      $('#hiddenplanid').val($(this).data("planid"));
      $('#hiddenpolicyid').val(policy_id);
      $('#hiddencover').val($('#compare_one_' + policy_id + '_cover').val());
      $('#hiddenplanname').val($('#compare_one_' + policy_id + '_plan_name').val());
      $('#hiddenpremium').val($('#select_premium_' + policy_id).val());
      	          var plan_id = $("#hiddenplanid").val();
var policy_id = $("#hiddenpolicyid").val();
var premium =  $("#hiddenpremium").val();
var cover  = $('#compare_one_' + policy_id + '_cover').val();
var total_premium =  $("#hiddenpremium").val();
var plan_name = $('#compare_one_' + policy_id + '_plan_name').val();


        checkCDbalance(plan_id,premium).then(e => {
            if(e !== 'true'){
                swal("Alert", e, "warning");
                return;
            }else{
                data = {};
                data.policy_id = policy_id;
                data.premium = premium;
                data.plan_id = plan_id;
                data.cover = cover;
                data.total_premium = total_premium;
                data.tenure = 1;
                data.single = 1;
                data.plan_name = plan_name;
                $("#hiddenCardForm").submit();
            }

        });
             
            
		

    });


      function checkCDbalance(plan_id,premium) {

          return new Promise(function (resolve, reject) {
              $.ajax({
                  url: "/quotes/checkCDbalanceThreshold",
                  type: "POST",
                  //async: false,
                  dataType: "json",
                  data:{plan_id,premium},
                  success: function(response) {
                      if(response.status == 200){
                          resolve(response.msg);
                      }else{
                          resolve(response.msg);

                      }
                  }


              });
          });
      }
  });

</script>
<!-- main content area end -->
<script>
  $(document).ready(function($) {
    var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
    var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

    function inWords (num) {
        if ((num = num.toString()).length > 9) return 'overflow';
        n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
        if (!n) return; var str = '';
        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
        str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
        return str;
    }


    var maxHeight = Math.max.apply(null, $(".button-ul-group").map(function() {
      return $(this).height();
    }).get());
    $(".button-ul-group").height(maxHeight);
  });
  
function PolicyIssue() {
    $.ajax({
        url: "/quotes/PolicyIssueApiNew",
        type: "POST",
        async: false,
        dataType: "json",
        success: function(response) {
            if(response.success == true){
                window.location.href =("http://fyntunecreditoruat.benefitz.in/quotes/success_view/"+response.LeadId);
            }
        }


    });
}
 document.title = 'Generate Your QuotePage'
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