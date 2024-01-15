<style>
   .table thead th {
   font-size:13px !important;
   }
   .table td{
   font-size:12px !important;
   }
   .navigate{
   margin-left:12px ;
   }
   .mar-0 {
   margin-top:0px;
   }
   img.export:hover {
   cursor: pointer;
   }
   .btn-primary {
	background-color: #da8089;
    border-color: #da8089;   
   }
   .btn-primary:hover {
	background-color: #da8089;
    border-color: #da8089;   
   }
</style>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<div class="container-fluid mt-3 pad-0">
   <div class="col-md-12 pad-0">
      <div class="card">
         <div class="card-body card-style mb-5">
            <div class="col-md-12">
               <h4 class="header-title title  col-md-12 header-tl-xd"> <img class="img-imv" src="/public/assets/images/new-icons/policy-del-xd.png"><span class="ml-2">Application Logs</span></h4>
            </div>
            <form action="/all_logs/1" method="get" id ="applogs_form" name ="applogs_form"  class="row col-md-12">
               <input type="hidden" name="export_excel-applogs" id="export_excel-applogs" value="0"/>
               <input type="hidden" name="clear-search-filter" id="clear-search-filter" value="0"/>
               <input type="hidden" name="search-applogs" id="search-applogs" value="1"/>
               <div class="col-md-2 col-12 mt-2">
                  <select id="product_type" name="product_type" class="form-control">
                     <option value="" <?php echo $product_type == '' ? 'selected' : ''?>>Select Product type</option>
                     <!--option value="R03" <?php echo $product_type == 'R03' ? 'selected' : '';?>>Other</option-->
					 <option value="R03" <?php echo $product_type == 'R03' ? 'selected' : '';?>>R03 (AFPP)</option>
					 <option value="R04" <?php echo $product_type == 'R04' ? 'selected' : '';?>>R04 (AGY)</option>
					 <option value="R05" <?php echo $product_type == 'R05' ? 'selected' : '';?>>R05 (Axis D2C)</option>
					 <option value="R06" <?php echo $product_type == 'R06' ? 'selected' : '';?>>R06 (Telesales)</option>
					 <option value="R13" <?php echo $product_type == 'R13' ? 'selected' : '';?>>R13 (Telesales Renewal)</option>
					 <option value="R14" <?php echo $product_type == 'R14' ? 'selected' : '';?>>R14 (Telesales Renewal With Modifications)</option>
					 <option value="R07" <?php echo $product_type == 'R07' ? 'selected' : '';?>>R07 (GHI + GCI + GPA)</option>
                <option value="R10" <?php echo $product_type == 'R10' ? 'selected' : '';?>>R10 (GPA + GCI)</option>
                <option value="ABC" <?php echo $product_type == 'ABC' ? 'selected' : '';?>>ABC</option>
                <option value="MUTHOOT" <?php echo $product_type == 'MUTHOOT' ? 'selected' : '';?>>MUTHOOT</option>
                <option value="HERO_FINCORP" <?php echo $product_type == 'HERO_FINCORP' ? 'selected' : '';?>>Hero Fin Corp</option>
                <option value="T01" <?php echo $product_type == 'T01' ? 'selected' : '';?>>Tele Healthpro (GHI + GCI + GPA)</option>
                <option value="R11" <?php echo $product_type == 'R11' ? 'selected' : '';?>>Healthpro Infinity (GHI + GPA + GHI SuperTopup)</option>
                  <option value="T03" <?php echo $product_type == 'T03' ? 'selected' : '';?>>Tele Healthpro Infinity (GHI + GPA + GHI SuperTopup)</option>
               <option value="D01" <?php echo $product_type == 'D01' ? 'selected' : '';?>>D2C Health Pro</option>
               <option value="D02" <?php echo $product_type == 'D02' ? 'selected' : '';?>>D2C Health Pro Infinity</option>
               <option value="ABML" <?php echo $product_type == 'ABML' ? 'selected' : '';?>>ABML</option>
					 <!--option value="R06" <?php echo $product_type == 'R03' ? 'selected' : '';?>>Other</option-->
                     
                  </select>
               </div>
              
				<div class="col-md-2 col-12 mt-2" id ='ro3_div'>
                  <select id="filter_type_ro3"  name="filter_type_ro3" style= "<?php echo !empty($filter_type_ro3) ? '' : 'display:none'; ?>"  class="form-control">
                     <option value="" >Select Filter</option>
                     
					 <option value="api_redirection_post_field" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'api_redirection_post_field' ? 'selected' : ''?> >Easy Pay Api redirection posted data</option>
					 
                     <option value="easy_pay_enquiry_post_field
                        " <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3  == 'easy_pay_enquiry_post_field' ? 'selected' : ''?>>Easy pay enquiry post field</option>
						
                     <option value="easy_pay_confirmation_post_field" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'easy_pay_confirmation_post_field' ? 'selected' : ''?>>Easy Pay Confirmation post field</option>
					 
                     <option value="full_quote_request1" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'full_quote_request1' ? 'selected' : ''?>> Quick Quote service</option>
					 
                     <option value="full_quote_request2" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'full_quote_request2' ? 'selected' : ''?>>  Full Quote service</option>
                     <option value="cancer_request" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'cancer_request' ? 'selected' : ''?>>Cancer_request</option>
                     <option value="bitly_url" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'bitly_url' ? 'selected' : ''?>>Pay U Bitly url</option>
                     <option value="sms_logs_redirect" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'sms_logs_redirect' ? 'selected' : ''?>>Pay U SMS Service</option>
                     <option value="payment_request_post" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 =='payment_request_post' ? 'selected' : ''?>>Pay U payment request post</option>
                     <option value="payment_response_post" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'payment_response_post' ? 'selected' : ''?>>Pay U payment response post</option>
                     <option value="payment_request_pg_post" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'payment_request_pg_post' ? 'selected' : ''?>>PG Common Source Request</option>
                     <option value="payment_response_pg_post" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'payment_response_pg_post' ? 'selected' : ''?>>PG Common Source Response</option>
                     <option value="payu_real_check" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'payu_real_check' ? 'selected' : ''?>>Payu Real Check</option>
					<option value="otp_data" <?php echo !empty($filter_type_ro3) &&  $filter_type_ro3 == 'otp_data' ? 'selected' : ''?>>OTP Data</option>
                  </select>
               </div>
			   
			   <div class="col-md-2 col-12 mt-2">
                  <select id="filter_type_ro5"  name="filter_type_ro5" style = "<?php echo !empty($filter_type_ro5) ? '' : 'display:none'; ?>"  class="form-control">
                     <option value="" >Select Filter</option>
                     <option value="Axis_redirection_post_data_request" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'Axis_redirection_post_data_request' ? 'selected' : ''?>>Axis redirection posted data</option>
                     <option value="Axis_redirection_post_data_decrypted" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 ==  'Axis_redirection_post_data_decrypted' ? 'selected' : ''?>>Axis redirection decrypted data</option>
                     <option value="full_quote_request1_retail_payment" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'full_quote_request1_retail_payment' ? 'selected' : ''?>>Quick Quote service</option>
                     <option value="payment_request_post" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'payment_request_post' ? 'selected' : ''?>>Payment Request Service</option>
                     <option value="payment_response_post" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'payment_response_post' ? 'selected' : ''?>>Payment Response Post</option>
                     <option value="full_quote_request2_retail_payment" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'full_quote_request2_retail_payment' ? 'selected' : ''?>>Full Request Service</option>
                     <option value="coi_uid_genarate" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'coi_uid_genarate' ? 'selected' : ''?>>COI UID  Member Service</option>
                     <option value="coi_genarate" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'coi_genarate' ? 'selected' : ''?>>COI UID Service</option>
                     <option value="sms_logs" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'sms_logs' ? 'selected' : ''?>>SMS Request</option>
                     <option value="payment_request_pg_post" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'payment_request_pg_post' ? 'selected' : ''?>>PG Common Source Request</option>
                     <option value="payment_response_pg_post" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'payment_response_pg_post' ? 'selected' : ''?>>PG Common Source Response</option>
                     <option value="payu_real_check" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'payu_real_check' ? 'selected' : ''?>>Payu Real Check</option>
					 <option value="invalid_redirect_data_and_error" <?php echo !empty($filter_type_ro5) &&  $filter_type_ro5 == 'invalid_redirect_data_and_error' ? 'selected' : ''?>>Invalid Redirect Data & Error</option>
                  </select>
               </div>
			   
			   <div class="row mt-4 col-md-12 mb-5">
                  <div class="col-md-2 col-12 mt-2">
                     <label>Select Date</label>
                     <input type="text" name="applogdates" class="form-control" value="<?php echo $applogdates;?>"/>
                  </div>
				  
                  <div  style = "max-width: 10.5%;" id="httime" class="col-md-2 col-12 mt-2">
                     <label>Time to</label>
                     <select class="form-control" name="time_to">
                        <option value="">In hours</option>
                        <?php for($i=1; $i<=24;$i++ ){ ?>
                        <option value="<?php echo $i; ?>" <?php echo $time_to == $i ? 'selected' : '';?>><?php echo $i; ?></option>
                        <?php } ?>
                     </select>
                  </div>
				  
                  <div  style = "max-width: 10.5%;" id="hftime" class="col-md-2 col-12 mt-2">
                     <label>Time from</label>
                     <select class="form-control" name="time_from">
                        <option value="">In hours</option>
                        <?php for($j=1; $j<=24;$j++ ){ ?>
                        <option value="<?php echo $j; ?>" <?php echo $time_from == $j ? 'selected' : ''?>><?php echo $j; ?></option>
                        <?php } ?>
                     </select>
                  </div>
				  
                  <label id="oor" class="mt-5">Or</label>
				  
                  <div class="col-md-2 mt-2 col-12 mt-2">
                     <label>Select Lead </label>
                     <input type="text" name="lead_id" id="lead_id"  class="form-control" value="<?php echo $lead_id; ?>">
                  </div>
				  
                  <label id="sor" class="mt-5">Or</label>
				  
                  <div id="hec" class="col-md-2 mt-2 col-12 mt-2"><label> Enter Certificate no </label>
                     <input type="text" name="certificate_no" id="certificate_no"  class="form-control" value="<?php echo $certificate_no; ?>">
                  </div>
				  
				  <label id="tor" class="mt-5">Or</label>
				  
                  <div id="hmn" class="col-md-2 mt-2 col-12 mt-2">
                     <label>Mobile Number </label>
                     <input type="text" name="mobile_number" id="mobile_number"  class="form-control" value="<?php echo $mobile_number; ?>">
                  </div>
				  
				  <label id="hr13" class="mt-5">Or</label>
				  
                  <div id="email_r13" class="col-md-2 mt-2 col-12 mt-2">
                     <label>Email </label>
                     <input type="text" name="email_address" id="email_address"  class="form-control" value="<?php echo $email_address; ?>">
                  </div>
				  
                  <div class="col-md-1 col-12 mt-2">
                     <label class="col-md-12" style="visibility:hidden;">L</label>
                     <button type="submit" name="applogsdate_search" id ="applogsdate_search" class="btn btn-primary">Search</button>
                  </div>
				  
                  <div class="col-md-2 col-12 mt-2 text-left">
                    <label class="col-md-12" style="visibility:hidden;">L</label>
                     <button type="button" name="applogs_clearsearch" id ="applogs_clearsearch" class="btn sub-btn">Clear Filters</button>		
                  </div>

               </div>
              
            </form>
            <div class="col-md-2 col-12 mt-2">
               <a id="exportApplogsExcel"><img class="mt-2 export" src="/public/assets/images/new-icons/export1.png" width="30" height="30"> </a>
            </div>
            <div class="table-responsive col-md-12 mt-3">
               <table style="table-layout: fixed; width: 100%"  class="table table-hover progress-table" id = "all_logs">
                  <thead class="table-col-1">
                     <tr>
                        <th style="width:100px">Lead id</th>
                        <th style="width:100px">Date</th>
                        <th style="width:160px">type</th>
                        <th style="width:400px">Request</th>
                        <th style="width:360px">Response</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach (@$result as $res) 
                        { ?>
                     <tr>
                        <td><?= $res->lead_id; ?></td>
                        <td><?= $res->created_at; ?></td>
                        <td><?= $res->type; ?></td>
                        <td><?= $res->req; ?></td>
                        <td><?= $res->res; ?></td>
                     </tr>                     
                     <?php } ?>
                  </tbody>
                  <p><?php echo $links; ?></p>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
</div>
<?php
   $js_files = [
     'public/assets/js/employee/alllogs.js',
   ];
   Globals::setJs(minify_resources($js_files, 'js', 'logs'));
   ?>