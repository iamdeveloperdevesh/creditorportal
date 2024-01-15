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
 .btn-primary {
	background-color: #da8089;
    border-color: #da8089;   
   }
   .btn-primary:hover {
	background-color: #da8089;
    border-color: #da8089;   
   }
</style>
<?php //print_Pre($query) ; ?>
<link href="https://fonts.googleapis.com/css?family=Titillium+Web&display=swap" rel="stylesheet">
<div class="container-fluid mt-3 pad-0">
<div class="col-md-12 pad-0">
<div class="card">

<div class="card-body card-style mb-5">
<div class="col-md-12"> <h4 class="header-title title  col-md-12 header-tl-xd"> <img class="img-imv" src="/public/assets/images/new-icons/policy-del-xd.png"><span class="ml-2">Logs</span></h4> </div>
    <form action="/post_log/1" method="post" id ="lead_form">
	<div class="col-md-2 col-12">
        <select id="product_type" name="product_type" class="form-control">
							<option value="" >Select Product type</option>
							<option value="R03" <?php echo ($_POST['product_type'] == 'R03') ? 'selected' : ''?>>R03</option>
							<option value="R05" <?php echo ($_POST['product_type'] == 'R05') ? 'selected' : ''?>>R05</option>
							</select>
		</div>
	<div class="row mt-4 col-md-12 mb-5">
	<div class="col-md-2 mt-2 col-12">
<label>Select Lead</label>
        <input type="text" name="lead_id" id="lead_id"  class="form-control" value="<?= @$_POST['lead_id']; ?>">
		</div>
		
		<div class="col-md-1 mt-2">
<label style="visibility: hidden;"> label </label>
        <button type="submit" name="lead_search" id ="lead_search" class="btn btn-primary" style="float:left;">Search</button>
		</div>
		<div class="col-md-2 mt-2 col-12">
<label> Select Date</label>
	    <input type="text" name="dates" class="form-control" value="<?= $_POST['dates'] ?>"/>
		</div>
		<div class="col-md-2 mt-2 col-12">
		<label>Status</label>
		<select class="form-control" name="proposal_status">
			<option value="">Select Status</option>
			<option value="Payment Pending" <?php echo ($_POST['proposal_status'] == 'Payment Pending') ? 'selected' : ''?>>Payment Pending</option> 
			<option value="Payment Recieved" <?php echo ($_POST['proposal_status'] == 'Payment Recieved') ? 'selected' : ''?>>Payment Recieved</option>
			<option value="Success" <?php echo ($_POST['proposal_status'] == 'Success') ? 'selected' : ''?>>Success</option>
		</select>
		</div> 
		
		<div class="col-md-2 mt-2 col-12">
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
		<div class="col-md-2 mt-2 col-12">
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
		<div class="col-md-1 mt-2">
<label style="visibility: hidden;"> label </label>
		<button type="submit" name="date_search" id = "date_search" class="btn btn-primary" style="float:left;">Search</button>
		</div>
		</div>
	</form>
    <div class="table-responsive col-md-12 mt-3">
        <table class="table table-hover progress-table">
            <thead class="table-col-1">
                <tr>
                    <th>Lead id</th>
                    <th>Created at</th>
                    <th>Proposal no</th>
                    <th>Emp firstname</th>
                    <th>Emp lastname</th>
                    <th>Premium</th>
                    <th>Sum Insured</th>
                    <th>Payment_Status</th>
                    <th>Txn Date</th>
                    <th>IMDCode</th>
                    <th>Certificate Number</th>
                    <th>Cancer Status</th>
                    <th>GHI Status</th>
                    <th>API Logs</th>
					<?php if($_POST['product_type'] == 'R03'){ ?> 
                    <th>Cancer/GHI Logs</th>
					<?php } ?>
                    <th>Sms Logs</th>
					<?php if($_POST['product_type'] == 'R03'){ ?> 
                    <th>Omni Docs Logs</th>
					<?php } ?>
					
                </tr>
            </thead>
            <tbody>
                <?php foreach (@$result as $res) { ?>
                    <tr>
                        <td><?= $res->lead_id; ?></td>
                        <td><?= $res->created_at; ?></td>
                        <td><?= $res->proposal_no; ?></td>
                        <td><?= $res->emp_firstname; ?></td>
                        <td><?= $res->emp_lastname; ?></td>
                        <td><?= $res->premium; ?></td>
                        <td><?= $res->sum_insured; ?></td>
                        <td><?= $res->payment_status; ?></td>
                        <td><?= $res->txndate; ?></td>
                        <td><?= $res->IMDCode; ?></td>
                        <td><?= ($res->certificate_number) ? $res->certificate_number : $res->COI_No; ?></td>
                        <td><?= $res->acrs; ?></td>
                        <td><?= $res->aprs; ?></td>
                        <td><a href="/get_confirmation/<?= $res->lead_id ?>/<?= $_POST['product_type'] ?>" target="_blank" rel="">check logs</a></td>
						<?php if($_POST['product_type'] == 'R03'){ ?> 
                        <td><a href="/get_cancer_ghi_log/<?= $res->lead_id; ?>/<?= $res->policy_subtype_id; ?>" target="_blank" rel="">check logs</a></td>
						<?php } ?>
                        <td><a href="/get_sms_log/<?= $res->lead_id ?>/<?= $_POST['product_type'] ?>" target="_blank" rel="">check logs</a></td>
						<?php if($_POST['product_type'] == 'R03'){ ?> 
						
                        <td><a href="/get_omnidocs_log/<?= $res->lead_id; ?>" target="_blank" rel="">check logs</a></td>
						<?php } ?>
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
</div></div>
 <?php
    $js_files = [
      'public/assets/js/employee/logs.js',
    ];
    Globals::setJs(minify_resources($js_files, 'js', 'logs'));
  ?>