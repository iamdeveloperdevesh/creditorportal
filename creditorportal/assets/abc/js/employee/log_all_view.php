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
</style>
<div class="container-fluid mt-3">
<div class="col-md-12">
<div class="card">
<?php print_Pre($query);?>
<div class="card-body card-style mb-5">
<div class="col-md-12"> <h4 class="header-title title  col-md-12 header-tl-xd"> <img class="img-imv" src="/public/assets/images/new-icons/policy-del-xd.png"><span class="ml-2">Logs</span></h4> </div>
    <form action="/all_logs/1" method="post" id ="lead_form" class="row col-md-12">
	<div class="col-md-2 col-12">
        <select id="product_type" name="product_type" class="form-control">
							<option value="" >Select Product type</option>
							<option value="R03" <?php echo ($_POST['product_type'] == 'R03') ? 'selected' : ''?>>R03</option>
							<option value="R05" <?php echo ($_POST['product_type'] == 'R05') ? 'selected' : ''?>>R05</option>
							</select>
							
		</div>
		<div class="col-md-2 col-12" id ='ro3_div'>
        <select id="filter_type_ro3"  name="filter_type_ro3" style= "<?php echo ($_POST['filter_type_ro3']) ? '' : 'display:none'; ?>"  class="form-control">
							<option value="" >Select Filter</option>
							<option value="api_redirection_post_field" <?php echo ($_POST['filter_type_ro3'] == 'api_redirection_post_field') ? 'selected' : ''?> >Easy Pay Api redirection posted data</option>
							<option value="easy_pay_enquiry_post_field
" <?php echo ($_POST['filter_type_ro3'] == 'easy_pay_enquiry_post_field
') ? 'selected' : ''?>>Easy pay enquiry post field</option>
							<option value="easy_pay_confirmation_post_field" <?php echo ($_POST['filter_type_ro3'] == 'easy_pay_confirmation_post_field') ? 'selected' : ''?>>Easy Pay Confirmation post field</option>
							<option value="full_quote_request1" <?php echo ($_POST['filter_type_ro3'] == 'full_quote_request1') ? 'selected' : ''?>> Quick Quote service</option>
							<option value="full_quote_request2" <?php echo ($_POST['filter_type_ro3'] == 'full_quote_request2') ? 'selected' : ''?>>  Full Quote service</option>
							<option value="cancer_request" <?php echo ($_POST['filter_type_ro3'] == 'cancer_request') ? 'selected' : ''?>>Cancer_request</option>
							
							<option value="bitly_url" <?php echo ($_POST['filter_type_ro3'] == 'bitly_url') ? 'selected' : ''?>>Pay U Bitly url</option>
							<option value="sms_logs_redirect" <?php echo ($_POST['filter_type_ro3'] == 'sms_logs_redirect') ? 'selected' : ''?>>Pay U SMS Service</option>
							<option value="payment_request_post" <?php echo ($_POST['filter_type_ro3'] == 'payment_request_post') ? 'selected' : ''?>>Pay U payment request post</option>
							<option value="payment_response_post" <?php echo ($_POST['filter_type_ro3'] == 'payment_response_post') ? 'selected' : ''?>>Pay U payment response post</option>
							
							</select>
		</div>
		<div class="col-md-2 col-12">
		
        <select id="filter_type_ro5"  name="filter_type_ro5" style = "<?php echo ($_POST['filter_type_ro5']) ? '' : 'display:none'; ?>"  class="form-control">
		
							<option value="" >Select Filter</option>
							<option value="Axis_redirection_post_data_request" <?php echo ($_POST['filter_type_ro5'] == 'Axis_redirection_post_data_request') ? 'selected' : ''?>>Axis redirection posted data</option>
							<option value="Axis_redirection_post_data_decrypted" <?php echo ($_POST['filter_type_ro5'] == 'Axis_redirection_post_data_decrypted') ? 'selected' : ''?>>Axis redirection decrypted data</option>
							<option value="full_quote_request1_retail_payment" <?php echo ($_POST['filter_type_ro5'] == 'full_quote_request1_retail_payment') ? 'selected' : ''?>>Quick Quote service</option>
							<option value="payment_request_post" <?php echo ($_POST['filter_type_ro5'] == 'payment_request_post') ? 'selected' : ''?>>Payment Request Service</option>
							<option value="payment_response_post" <?php echo ($_POST['filter_type_ro5'] == 'payment_response_post') ? 'selected' : ''?>>Payment Response Post</option>
							<option value="full_quote_request2_retail_payment" <?php echo ($_POST['filter_type_ro5'] == 'full_quote_request2_retail_payment') ? 'selected' : ''?>>Full Request Service</option>
							<option value="coi_uid_genarate" <?php echo ($_POST['filter_type_ro3'] == 'coi_uid_genarate') ? 'selected' : ''?>>COI UID  Member Service</option>
							<option value="coi_genarate" <?php echo ($_POST['filter_type_ro3'] == 'coi_genarate') ? 'selected' : ''?>>COI UID Service</option>
							
							<option value="sms_logs" <?php echo ($_POST['filter_type_ro5'] == 'sms_logs') ? 'selected' : ''?>>SMS Request</option>
							
							</select>
		</div>
		
		
	<div class="row mt-4 col-md-12 mb-5">
	<div class="col-md-3 mt-2 col-12 mt-2">
<label>Enter Lead </label>
        <input type="text" name="lead_id" id="lead_id"  class="form-control" value="<?= @$_POST['lead_id']; ?>">
		
		<label>or Enter Certificate no </label>
        <input type="text" name="certificate_no" id="certificate_no"  class="form-control" value="<?= @$_POST['certificate_no']; ?>">
		</div>
		
		<div class="col-md-1 mb-2 col-12 mt-2">
<label style="visibility:hidden;"> label </label>
        <button type="submit" name="lead_search" id ="lead_search" class="btn btn-primary" style="float:left;">Search</button>
		</div>
		<div class="col-md-2 col-12 mt-2">
<label>Select Date</label>
	    <input type="text" name="dates" class="form-control" value="<?= $_POST['dates'] ?>"/>
		</div>
		<!--<div class="col-md-1">
		<select class="form-control" name="proposal_status">
			<option value="Payment Pending">Select Status</option>
			<option value="Payment Pending">Payment Pending</option> 
			<option value="Payment Recieved">Payment Recieved</option>
			<option value="Success">Success</option>
		</select>
		</div> -->
		
		
		
		<div class="col-md-2 col-12 mt-2">
		<label>Time to</label>
		<select class="form-control" name="time_to">
			<option value="">Select Time to in hours</option>
			<option value="1" <?php echo ($_POST['time_to'] == '1') ? 'selected' : ''?>>1</option>
			<option value="2" <?php echo ($_POST['time_to'] == '2') ? 'selected' : ''?>>2</option>
			<option value="3" <?php echo ($_POST['time_to'] == '3') ? 'selected' : ''?>>3</option>
			<option value="4" <?php echo ($_POST['time_to'] == '4') ? 'selected' : ''?>>4</option>
			<option value="5" <?php echo ($_POST['time_to'] == '5') ? 'selected' : ''?>>5</option>
			<option value="6" <?php echo ($_POST['time_to'] == '6') ? 'selected' : ''?>>6</option>
			<option value="7" <?php echo ($_POST['time_to'] == '7') ? 'selected' : ''?>>7</option>
			<option value="8" <?php echo ($_POST['time_to'] == '8') ? 'selected' : ''?>>8</option>
			<option value="9" <?php echo ($_POST['time_to'] == '9') ? 'selected' : ''?>>9</option>
			<option value="10" <?php echo ($_POST['time_to'] == '10') ? 'selected' : ''?>>10</option>
			<option value="11" <?php echo ($_POST['time_to'] == '11') ? 'selected' : ''?>>11</option>
			<option value="12" <?php echo ($_POST['time_to'] == '12') ? 'selected' : ''?>>12</option>
			<option value="13" <?php echo ($_POST['time_to'] == '13') ? 'selected' : ''?>>13</option>
			<option value="14" <?php echo ($_POST['time_to'] == '14') ? 'selected' : ''?>>14</option>
			<option value="15" <?php echo ($_POST['time_to'] == '15') ? 'selected' : ''?>>15</option>
			<option value="16" <?php echo ($_POST['time_to'] == '16') ? 'selected' : ''?>>16</option>
			<option value="17" <?php echo ($_POST['time_to'] == '17') ? 'selected' : ''?>>17</option>
			<option value="18" <?php echo ($_POST['time_to'] == '18') ? 'selected' : ''?>>18</option>
			<option value="19" <?php echo ($_POST['time_to'] == '19') ? 'selected' : ''?>>19</option>
			<option value="20" <?php echo ($_POST['time_to'] == '20') ? 'selected' : ''?>>20</option>
			<option value="21" <?php echo ($_POST['time_to'] == '21') ? 'selected' : ''?>>21</option>
			<option value="22" <?php echo ($_POST['time_to'] == '22') ? 'selected' : ''?>>22</option>
			<option value="23" <?php echo ($_POST['time_to'] == '23') ? 'selected' : ''?>>23</option>
			<option value="24" <?php echo ($_POST['time_to'] == '24') ? 'selected' : ''?>>24</option>
			
		</select>
		</div>
		<div class="col-md-2 col-12 mt-2">
	<label>Time from</label>
		<select class="form-control" name="time_from">
			<option value="">Select Time from in hours</option>
			<option value="1" <?php echo ($_POST['time_from'] == '1') ? 'selected' : ''?>>1</option>
			<option value="2" <?php echo ($_POST['time_from'] == '2') ? 'selected' : ''?>>2</option>
			<option value="3" <?php echo ($_POST['time_from'] == '3') ? 'selected' : ''?>>3</option>
			<option value="4" <?php echo ($_POST['time_from'] == '4') ? 'selected' : ''?>>4</option>
			<option value="5" <?php echo ($_POST['time_from'] == '5') ? 'selected' : ''?>>5</option>
			<option value="6" <?php echo ($_POST['time_from'] == '6') ? 'selected' : ''?>>6</option>
			<option value="7" <?php echo ($_POST['time_from'] == '7') ? 'selected' : ''?>>7</option>
			<option value="8" <?php echo ($_POST['time_from'] == '8') ? 'selected' : ''?>>8</option>
			<option value="9" <?php echo ($_POST['time_from'] == '9') ? 'selected' : ''?>>9</option>
			<option value="10" <?php echo ($_POST['time_from'] == '10') ? 'selected' : ''?>>10</option>
			<option value="11" <?php echo ($_POST['time_from'] == '11') ? 'selected' : ''?>>11</option>
			<option value="12" <?php echo ($_POST['time_from'] == '12') ? 'selected' : ''?>>12</option>
			<option value="13" <?php echo ($_POST['time_from'] == '13') ? 'selected' : ''?>>13</option>
			<option value="14" <?php echo ($_POST['time_from'] == '14') ? 'selected' : ''?>>14</option>
			<option value="15" <?php echo ($_POST['time_from'] == '15') ? 'selected' : ''?>>15</option>
			<option value="16" <?php echo ($_POST['time_from'] == '16') ? 'selected' : ''?>>16</option>
			<option value="17" <?php echo ($_POST['time_from'] == '17') ? 'selected' : ''?>>17</option>
			<option value="18" <?php echo ($_POST['time_from'] == '18') ? 'selected' : ''?>>18</option>
			<option value="19" <?php echo ($_POST['time_from'] == '19') ? 'selected' : ''?>>19</option>
			<option value="20" <?php echo ($_POST['time_from'] == '20') ? 'selected' : ''?>>20</option>
			<option value="21" <?php echo ($_POST['time_from'] == '21') ? 'selected' : ''?>>21</option>
			<option value="22" <?php echo ($_POST['time_from'] == '22') ? 'selected' : ''?>>22</option>
			<option value="23" <?php echo ($_POST['time_from'] == '23') ? 'selected' : ''?>>23</option>
			<option value="24" <?php echo ($_POST['time_from'] == '24') ? 'selected' : ''?>>24</option>
			
		</select>
		</div>
		
		<div class="col-md-1 col-12 mt-2">
<label style="visibility:hidden;">label</label>
		<button type="submit" name="date_search" id = "date_search" class="btn btn-primary" style="float:left;">Search</button>
		</div>
		</div>
	</form>
    <div class="table-responsive col-md-12 mt-3">
        <table class="table table-hover progress-table" id = "all_logs">
            <thead class="table-col-1">
                <tr>
                    <th>Lead id</th>
					<th>type</th>
                    <th>Request</th>
                    <th>Response</th>
                    
                    
					
					
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach (@$result as $res) { ?>
                    <tr>
                        <td><?= $res->lead_id; ?></td>
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