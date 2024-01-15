<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}
require(APPPATH.'libraries/razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
//require_once(APPPATH."controllers/MY_TelesalesSessionCheck.php");
//class Axis_telesale extends MY_TelesalesSessionCheck

class Axis_telesale extends CI_controller
{
	public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
	
    function __construct()
    {
        parent::__construct();	
	
		     $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
        $this->keyId = 'rzp_test_HxRHmUmojTTNs4';
        $this->keySecret = 'JEkpsKDaDPZ9RNoBpomvm2ib';
        $this->displayCurrency = 'INR';
		
		$this->load->model("API/Payment_telesale_m", "obj_api", true);
		$this->load->model("API/Payment_integration_freedom_plus", "external_obj_api", true);
		$this->load->model("Logs_m");
		
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);
		
		//echo encrypt_decrypt_password(620547);
    }
public function coi_download() 
	{
		
            $certificate_no = $this
                ->input
                ->post('certificate_no');
        
		

        $data = $this
            ->db
            ->query("select ed.emp_id,ed.lead_id,ed.product_id,CONCAT(ed.emp_firstname,ed.emp_lastname) as cust_details,apr.certificate_number,apr.COI_url,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and apr.certificate_number = '$certificate_no' ")->row_array();
        $product_code = $data['product_id'];
		$emp_id = $data['emp_id'];
		$quer = $this->db->query("select GROUP_CONCAT(DISTINCT(master_policy_no)) as policy_number,product_name,policy_parent_id,id from product_master_with_subtype where product_code = '$product_code'")->row_array();
		$data['policy_number'] = $quer['policy_number'];
		$data['plan_name'] = $quer['product_name'];
		$policy_parent_id = $quer['policy_parent_id'];
		$product_id = $quer['id'];
		$policy_data = $this->db->query("select GROUP_CONCAT(DISTINCT(policy_no)) as policy_name,policy_detail_id,policy_sub_type_id from employee_policy_detail where parent_policy_id = '$policy_parent_id' and product_name = '$product_id'")->result_array();
	//	print_r($this->db->last_query());
	$nominee_det = $this->db->query("select concat(nominee_fname,nominee_lname)as nominee_name from member_policy_nominee where emp_id = '$emp_id'")->row_array();
	$data['nominee_name'] = $nominee_det['nominee_name'];

	foreach($policy_data as $value)
		{
			
			$policy_detail_id = $value['policy_detail_id'];
			$policy_sub_type_id = $value['policy_sub_type_id'];
			$policy_sub_type_name = $this->db->query("select policy_sub_type_name from master_policy_sub_type where policy_sub_type_id = '$policy_sub_type_id'")->row_array();
			
			$quer_det = $this->db->query("select epm.policy_member_first_name,epm.policy_member_last_name,epm.policy_mem_dob,epm.policy_mem_gender,epm.policy_mem_sum_insured as cover,mfr.fr_name from family_relation as fr,employee_policy_member as epm,master_family_relation as mfr where fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id' and epm.policy_detail_id = '$policy_detail_id' and epm.fr_id = mfr.fr_id")->result_array();
			$data_ref['insured_member'][$policy_sub_type_id]['member'] = $quer_det;
			$data_ref['insured_member'][$policy_sub_type_id]['policy_sub_type_name'] = $policy_sub_type_name['policy_sub_type_name'];
		}
$data['insured_details'] =$data_ref; 
		$html = $this
           ->load
           ->view("Telesales/coi_pdf", $data, true);
       echo $html;
	}
    public function update_txRefno_script(){
    	$q = "select pd.TxRefNo,pd.payment_status,pd.txndate,p.created_date,p.id,ed.product_id,ed.lead_id  
				from proposal p, payment_details pd , employee_details ed  
				where p.id = pd.proposal_id and p.emp_id=ed.emp_id and  pd.TxRefNo = 0 and p.status = 'Success' 
				and pd.payment_status = 'Payment Pending' and ed.product_id IN ('T01','T03','R06')";
		$data = $this->db->query($q)->result_array();
		print_pre($data);exit;
    }
	
	public function agent_coi_mail_trigger() 
	{
		echo json_encode($this->obj_api->agent_coi_mail_trigger_m());
	}
	
	/* cron */
	public function update_rejected()
	{
		$this->obj_api->update_rejected_m();
	}

    public function payment_url_send() 
	{
		$this->obj_api->payment_url_send_m();
		echo json_encode(['status' =>'success']);
	}
	
	public function agent_policy_create() 
	{
		echo json_encode($this->obj_api->agent_policy_create_m());
	}
	
	public function check_error_data() 
	{
		echo json_encode($this->obj_api->check_error_data_m());
	}
	
	public function payment_error_view()
	{
		$emp_id = $this->session->userdata('emp_id');
		
		$lead_arr = $this->db->query("select lead_id,email from employee_details where emp_id = '$emp_id' ")->row_array();
		$lead_id = $lead_arr['lead_id'];
		$email = $lead_arr['email'];
		
		$this->load->telesales_template('payment_error_view',compact('emp_id','lead_id','email'));
	}

	public function payment_redirect_view($emp_id_encrypt)
	{
		
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$this->session->set_userdata('emp_id', $emp_id);
		
		if($emp_id){
			
			$query = $this->db->query("SELECT ed.address,ed.emp_firstname,ed.emp_lastname,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,mpst.product_code,mpst.product_name,pd.ifscCode,pd.branch,pd.bank_name,ed.acc_no FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details as pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id=".$emp_id)->row_array();

			// 7 days = 604800 sec
			// $productname="Axis Tele Inbound Affinity Portal for ABHI";

			$productname="Axis Tele Inbound";


			if($query['product_code']=='R06'){
					$productname="Group Activ Health";
			}

			if($query['product_code']=='T01'){
					$productname="Group Activ Health and Group Activ Secure";
			}

			if($query['product_code']=='T03'){

					$productname="Group Activ Health and Group Activ Secure";
					$super_topup=$this->db->select('emp_id')->from('proposal')->where('emp_id',$emp_id)->where('policy_detail_id',475)->get()->result_array();
					if(!empty($super_topup)){
							$productname="Group Activ Health";
					}
			}



			if($query['product_code']=='R06'){
				$productname="Group Activ Health";				
			}

			if($query['product_code']=='T01'){
				$productname="Group Activ Health and Group Activ Secure";									
			}

			if($query['product_code']=='T03'){
				$productname="Group Activ Health and Group Activ Secure";													
			}

			// print_r($productname);
			// exit;
		$UniqueIdentifier = "LEADID";
						$UniqueIdentifierValue = $query['lead_id'];
						$CustomerName = $query['emp_firstname']." ".$query['emp_lastname'];
						$Email = $query['email'];
						$PhoneNo = substr(trim($query['mob_no']), -10);
						$FinalPremium = round($query['premium'],2);
						$ProductInfo = $query['product_name'];
						$address = $query['address'];
$FinalPremium = round($query['premium'],2);

			if(!empty($query))
			{
				   $api = new Api($this->keyId, $this->keySecret);
				$orderData = [
            'receipt'         => 3456,
            'amount'          => $FinalPremium*100 , // 2000 rupees in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $api->order->create($orderData);

        $razorpayOrderId = $razorpayOrder['id'];

        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

        $displayAmount = $amount = $orderData['amount'];
        $displayCurrency = $this->displayCurrency;

        if ($displayCurrency !== 'INR')
        {
            $url = 'https://api.razorpay.com/v1/orders';
            $exchange = json_decode(file_get_contents($url), true);

            $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
        }

        $checkout = 'automatic';

        if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
        {
            $checkout = $_GET['checkout'];
        }

        $data = [
            "key"               => $this->keyId,
            "amount"            => $amount,
            "name"              => 'FynTune',
            "description"       => $ProductInfo,
            "image"             => "http://fyntune.com/images/logo/logo.png",
            "prefill"           => [
            "name"              => $CustomerName,
            "email"             => $Email,
            "contact"           => $PhoneNo,
            ],
            "notes"             => [
            "address"           => $address,
            "merchant_order_id" => "12312321",
            ],
            "theme"             => [
            "color"             => "#F37254"
            ],
            "order_id"          => $razorpayOrderId,
        ];


        $data['display_currency']  = $displayCurrency;
        $data['display_amount']    = $displayAmount;
        $data['customer_id']    = $UniqueIdentifierValue;
        $data['lead_id']    = encrypt_decrypt_password($UniqueIdentifierValue);
$data['emp_id']= encrypt_decrypt_password($emp_id);
        $res['data'] = $data;

        //$json = json_encode($data);
        //$this->load->view('template/customer_portal_header.php');
						$this->load->telesales_template("pg_submit",compact('data'));
				
			}else{
				echo "Payment link has been expired, Please get in touch with your Branch RM";
			}
		}
	}
 
	
	public function payment_return_view($emp_id_encrypt){
	//	echo 123;die;
	//print_R($_POST);die;
		//$emp_id = $this->session->userdata('emp_id');
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$this->session->set_userdata('emp_id', $emp_id);
		$success = true;
		$encrypted = $this->input->post('RESPONSE');
		
		 if(isset($_POST['razorpay_payment_id'])){
           // var_dump($success);exit;
            if ($success === true)
            {
		
		
			
			extract($_POST);
				$TxRefNo = $_POST['razorpay_payment_id'];
				$TxStatus = "success";
				$TxMsg = "No Error";
			
		
			}
		
		$query = $this->db->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id='".$emp_id."' GROUP BY p.emp_id")->row_array();
		//print_pre($query);die;
		if(!empty($query['proposal_id'])){
			
			$ids = explode(',',$query['proposal_id']);
			
			if(isset($TxRefNo)){
				
				$request_arr = ["lead_id" => $query['lead_id'], "req" => '',"res"=>json_encode($_POST),"product_id"=> $query['product_code'], "type"=>"payment_response_post"];
				$txnDateTime = date('Y-m-d H:i:s');
  	            $dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);
			
				$request_arr = ["payment_status" => $TxMsg,"premium_amount" => ($amount/2),"payment_type" => 'PR',"txndate" => $txnDateTime,"TxRefNo" => $TxRefNo,"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($_POST)];
					
					$this->db->where_in('proposal_id', $ids);
					$this->db->where('TxStatus != ','success');
					$this->db->update("payment_details",$request_arr);

			}
			
		
			
		
			
			$proposal_id = $ids[0];
			
			$payment_data = $this->db->query("select payment_status,TxStatus,txndate from payment_details where proposal_id='$proposal_id'")->row_array();
			
			//print_R($payment_data);die;
			if($payment_data['TxStatus'] == 'success'){
			
								$data_res = $this->obj_api->policy_creation_call($query['lead_id']);

							//print_R($data_res);die;

				if($data_res['Status'] == 'Success'){
					
					$data_policy = $this->db->query("select GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number from api_proposal_response where emp_id='$emp_id' GROUP BY emp_id")->row_array();
					
					if($data_policy){
						
						$data['proposal_no'] = $query['proposal_no'];
						$data['lead_id'] = $query['lead_id'];
						$data['txndate'] = $payment_data['txndate'];
						
						$MandateLink_data = $this->db->query("select MandateLink,Registrationmode from emandate_data where lead_id = '".$query['lead_id']."'")->row_array();
						
						$this->load->telesales_template("thankyou",compact('data_policy','data','MandateLink_data'));
					}
					
				}else{
					
					redirect(base_url('tls_payment_error_view'));
					
				}
			
			}else{	
				 redirect(base_url('tls_payment_error_view'));
			}
			
			
		}else{
			
			echo "Payment link has been expired, Please get in touch with your Branch RM";
	
		}
		
		
	}
	}

	
	

/* cron */
public function tele_fail_policy_create($check)
{

  if($check == 2){
	// echo "8 clock cron pending";exit;
	
	$query_r = $this
	->db
	->query("SELECT ed.lead_id,ed.emp_id,ed.product_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND p.status = 'Payment Received' AND ed.product_id in('R06','T01','T03') AND date(p.created_date) = date(now())")->result_array();
	
	if($query_r)
	{
		foreach($query_r as $val_r){
			
			$where_arr = ["emp_id"=>$val_r['emp_id'],"status"=>"Payment Received"];
				$arr = ["count" => 2];
				$this->db->where($where_arr);
				$this->db->update("proposal",$arr);
				
			$check_result = $this->obj_api->policy_creation_call($val_r['lead_id'],1);
			
			$request_arr = ["lead_id" => $val_r['lead_id'], "req" => json_encode($check_result),"res" => json_encode($check_result) ,"product_id"=> $val_r['product_id'], "type"=>"8clock_cron"];
				$dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);

			//echo $check_result['Status']."hii".$val_r['lead_id'];
		}
	}

}else if($check == 1){

   /*
   // till 2020-11-17 (for old PG real pg status check)
   $query = $this
	->db
	->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url,ed.product_id,pt.txt_id,pt.pg_type,pt.id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g,payment_txt_ids as pt WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND g.`status` = 'success' AND p.status IN('Payment Pending','Rejected')  AND ed.product_id in('R06','T01','T03') AND date(p.created_date) >= '2020-10-06' AND date(p.created_date) <= '2020-11-17' AND pt.cron_count < 2  limit 15")->result_array();

	if($query)
	{
		
		foreach($query as $val1){
			
			$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);

			if($val1['pg_type'] == 'Razorpay'){
				
			// Decleration for Razor Pay Key Id, Key Secret & Currency Type
			$key_id = RAZOR_KEY_ID;
			$key_secret = RAZOR_KEY_SECRET;
			$razcurrency = PAYMENTGATEWAY_CURRENCY;
			$razcheckoutmethod = PAYMENTGATEWAY_CHECKOUT_METHOD_AUTOMATIC; 
			
			$api = new Api($key_id, $key_secret);
			$payment_obj = $api->order->payments($val1['txt_id']);
			
			$payment = (array)$payment_obj;
			
					   
			if(!empty($payment_obj['items'])){
				
				foreach ($payment_obj['items'] as $value){
					if($value['status']=='captured'){
						$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val1['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_success_cron"];
						$this->db->insert("logs_docs",$request_arr);

						$arr = ["payment_status" => "No Error","premium_amount" => ($value['amount']/100),"payment_type" => $value['method'],"pgRespCode" => "","merchantTxnId" => $value['order_id'],"SourceTxnId" => $value['order_id'],"txndate" => date('m/d/Y h:i A', $value['created_at']),"TxRefNo" => $value['id'],"TxStatus"=>"success","bank_name"=>$value['bank'],"json_quote_payment"=>json_encode($payment)];
				
						$proposal_ids = $this->db->query("select id as proposal_id from proposal where emp_id='".$val1['emp_id']."'")->row_array();
						
						$this->db->where("proposal_id",$proposal_ids['proposal_id']);
						$this->db->update("payment_details",$arr);
						
						$check_result = $this->obj_api->policy_creation_call($val1['lead_id']);
						//echo $check_result['Status']."hii".$val1['lead_id'];								

					}else{
						
						$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val1['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_fail_cron"];
						$this->db->insert("logs_docs",$request_arr);
						
					}
				}
			
			}
			
			}
				
		}
		
	}*/
	
	// after 2020-11-17 (for new PG real pg status check)
			
	$query1 = $this->db->query("SELECT ed.lead_id,pt.id FROM employee_details as ed,proposal AS p,payment_txt_ids as pt WHERE ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND p.status IN('Payment Pending','Rejected') AND ed.product_id in('R06','T01','T03') AND pt.pg_type = 'New' AND pt.cron_count < 2  limit 15")->result_array();
	
	if($query1)
	{
		
		foreach($query1 as $val1){
			
			$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
						
				$check_pg = $this->external_obj_api->real_pg_check($val1['lead_id']);
						
				if($check_pg){
					$check_result = $this->obj_api->policy_creation_call($val1['lead_id'],1);
				}
		}
		
	}else{
		
	$query_r = $this
	->db
	->query("SELECT ed.lead_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND  p.count < 5 AND p.status = 'Payment Received' AND ed.product_id in('R06','T01','T03') AND date(p.created_date) = date(now()) limit 5")->result_array();
	
	if($query_r)
	{
		foreach($query_r as $val_r){
			$check_result = $this->obj_api->policy_creation_call($val_r['lead_id'],1);
			//echo $check_result['Status']."hii".$val_r['lead_id'];
		}
	}

	//echo "cron cases finished";
	}
  }
	
}



	
}

