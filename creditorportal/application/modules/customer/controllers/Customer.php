<?php

header('Access-Control-Allow-Origin: *');
if (!defined('BASEPATH')) exit('No direct script access allowed');
require(APPPATH.'libraries/razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
// session_start(); //we need to call PHP's session object to access it through CI
class Customer extends CI_Controller
{
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

    }

	function customerpaymentform()
	{

		$record_id = "";
		if (isset($_GET['text']) && !empty($_GET['text'])) {
			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$record_id = $url_prams['id'];
		}

		$data = array();
		$data['lead_id'] = $record_id;
		$data['customer'] = true;

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);

		$customers = reset($result['customer_details']);

		foreach ($customers as $customer) {

			$requestData = [
				'customer_id' => $customer['customer_id'],
				'lead_id' => $record_id
			];

			$mode = 'addEdit';
			$members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
			$questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
			$answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

			$customer_id = $customer['customer_id'];

			$html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
			$result['ghd_declaration'][$customer['customer_id']] = $html;
		}

		$result['lead_id_enc'] = $_GET['text'];
		
		$this->load->helper('form');
		$this->load->view('template/customer-header.php');
		$this->load->view('customer/addEditCustomer', $result);
		$this->load->view('template/customer-footer.php');
	}

    public function payment_redirection($lead_id_encrypt){
        $lead_id_encrypt = htmlspecialchars(strip_tags(trim($lead_id_encrypt)));
        if (isset($lead_id_encrypt) && $lead_id_encrypt != '') {
            $lead_id_encrypt = $lead_id_encrypt;
            $lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');
            $queryData = $this->db->query("SELECT mp.plan_name,ms.full_name,ms.address_line1,ms.customer_id,mp.payment_url,ed.trace_id,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.tax_amount) as premium,ppd.proposal_status,ed.plan_id FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p,proposal_payment_details as ppd WHERE mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id = ppd.lead_id AND ed.lead_id=" . $lead_id)->row_array();
           // var_dump($queryData);die;
            if (!empty($queryData)) {
                if ($queryData['proposal_status'] == 'PaymentReceived' || $queryData['proposal_status'] == 'success') {
                    //redirect(base_url("api2/payment_success_view/" . $lead_id_encrypt));
                    //echo json_encode($this->payment_success($lead_id_encrypt));

                    redirect('/quotes/success_view/'.$lead_id_encrypt);
                } else {

                    $api = new Api($this->keyId, $this->keySecret);
                    $data['lead_id'] = $lead_id;
                    $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $data),TRUE);
                   // print_r($res);die;
                    if(isset($res['data']) && $res['status'] == 200){
                        redirect('/quotes/success_view/'.$lead_id);

                    }

                    $customer_name= $queryData['full_name'];
                    $customer_id= $queryData['customer_id'];
                    $email = $queryData['email_id'];
                    $mobileNumber = $queryData['mobile_no'];
                    $address = $queryData['address_line1'];
                    $leadId= $lead_id;

                    $ProductInfo = $queryData['plan_name'];
                    $premiumAmount = $queryData['premium'];
                    $FinalPremium = $premiumAmount;

                    $orderData = [
                        'receipt'         => 3456,
                        'amount'          => $FinalPremium * 100, // 2000 rupees in paise
                        'currency'        => 'INR',
                        'payment_capture' => 1 // auto capture
                    ];
//print_r($orderData);die;
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
                            "name"              => $customer_name,
                            "email"             => $email,
                            "contact"           => $mobileNumber,
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
                    $data['customer_id']    = $customer_id;
                    $data['lead_id']    = encrypt_decrypt_password($leadId);

                    $res['data'] = $data;

                    //$json = json_encode($data);
                    //$this->load->view('template/customer_portal_header.php');
                    $this->load->view('quotes/pg_submit',$res);
                    //$this->load->view('template/customer_portal_footer.php');
                }

            }

        }

    }

	public function payment_redirection1($lead_id_encrypt){
		
		$lead_id_encrypt = htmlspecialchars(strip_tags(trim($lead_id_encrypt)));

		if ($lead_id_encrypt) {

			$result = json_decode(curlFunction(SERVICE_URL . '/api2/payment_redirection', ['lead_id_encrypt' => $lead_id_encrypt]), true);

			//print_r($result);exit;

			if($result['success'] == 1){

				$policy_data = $result['data'];
				$this->load->view('template/customer-header.php');
				$this->load->view('customer/thank-you.php', $policy_data);
				$this->load->view('template/customer-footer.php');
			}
			else if($result['success'] == 2){

				redirect($result['data']);
			}
			else if($result['faliure'] == 1){

				$lead_arr = $result['data'];

				$this->load->view('template/customer-header.php');
				$this->load->view('customer/error-page.php', $lead_arr);
				$this->load->view('template/customer-footer.php');
			}
			else if($result['faliure'] == 2){

				echo '<script>alert("'.$result['error'].'");</script>';exit;
			}
			else if($result['faliure'] == 3){

				echo '<script>alert("Thank You! your case with lead ID '.$result['data']['trace_id'].' has been assigned to the under writer");</script>';exit;
			}
		}
	}

	public function showthankyou(){

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/getpolicydata', ['lead_id' => 'S3E0c1V4VlNjLzhiQTh1enpmMlFXZz09']), true);

		$this->load->view('template/customer-header.php');
		$this->load->view('customer/thank-you.php');
		$this->load->view('template/customer-footer.php');
	}

	function customerotpform($record_id)
	{
		if (isset($_SESSION[$record_id])) {

			$data['lead_id'] = $record_id;
			$result = curlFunction(SERVICE_URL . '/api2/doSendOtp', $data);
			$this->load->helper('form');
			$this->load->view('template/customer-header.php');
			$this->load->view('customer/otpconfirmation', $data);
			$this->load->view('template/footer.php');

		} else {

			$this->load->view('template/customer-header.php');
			$this->load->view('customer/tryagain.php', ['try_again' => 0]);
			$this->load->view('template/customer-footer.php');
		}
	}

	function customdetailsform()
	{
		$data = [];
		$data['firstname'] = htmlentities(strip_tags(trim($_POST['firstname'])));
		$data['lastname'] = htmlentities(strip_tags(trim($_POST['lastname'])));
		$data['address_line1'] = htmlentities(strip_tags(trim($_POST['address_line1'])));
		$data['address_line2'] = htmlentities(strip_tags(trim($_POST['address_line2'])));
		$data['address_line3'] = htmlentities(strip_tags(trim($_POST['address_line3'])));
		$data['pincode'] = htmlentities(strip_tags(trim($_POST['pin_code'])));
		$data['city'] = htmlentities(strip_tags(trim($_POST['city'])));
		$data['state'] = htmlentities(strip_tags(trim($_POST['state'])));
		$data['lead_id'] = htmlentities(strip_tags(trim($_POST['elem_lead'])));
		$data['customer_id'] = htmlentities(strip_tags(trim($_POST['elem_customer'])));

		/*$data['lead_id'] = base64_decode(strtr($data['lead_id'], '-_', '+/'));
		$data['customer_id'] = base64_decode(strtr($data['customer_id'], '-_', '+/'));*/

		$data['lead_id'] = encrypt_decrypt_password($data['lead_id'], 'D');
		$data['customer_id'] = encrypt_decrypt_password($data['customer_id'], 'D');

		$result = curlFunction(SERVICE_URL . '/api2/savecustomerdetails', $data);

		//$_SESSION['lead_id'] = $_POST['elem_lead'];
		echo $result;
		exit;
	}

	function memberdetailsform()
	{
		$data = [];
		$data['firstname'] = htmlentities(strip_tags(trim($_POST['firstname'])));
		$data['lastname'] = htmlentities(strip_tags(trim($_POST['lastname'])));

		/*$data['lead_id'] = base64_decode(strtr($_POST['elem_lead'], '-_', '+/'));
		$data['customer_id'] = base64_decode(strtr($_POST['elem_customer'], '-_', '+/'));
		$data['member_id'] = base64_decode(strtr($_POST['elem_member'], '-_', '+/'));*/

		$data['lead_id'] = encrypt_decrypt_password($_POST['elem_lead'], 'D');
		$data['customer_id'] = encrypt_decrypt_password($_POST['elem_customer'], 'D');
		$data['member_id'] = encrypt_decrypt_password($_POST['elem_member'], 'D');		

		$result = curlFunction(SERVICE_URL . '/api2/savememberdetails', $data);
		//$_SESSION['lead_id'] = $_POST['elem_lead'];
		echo $result;
		exit;
	}

	function verifyotp(){

		$data['lead_id'] = $lead_id = htmlentities(strip_tags(trim($_POST['lead_id'])));
		$data['otp'] = htmlentities(strip_tags(trim($_POST['otp'])));
        //var_dump($data['otp']);
		$addEdit = json_decode(curlFunction(SERVICE_URL . '/api2/checkLeadotpCustomer', $data), true);
		//var_dump($addEdit);die;
		if ($addEdit['status_code'] == '200') {
			
			unset($_SESSION[$lead_id]);
			//$_SESSION['lead_id'] = $data['lead_id'];
			if($addEdit['Data']['payment_needed'] == 1){
				
				echo '1-0';
			}
			else if($addEdit['Data']['payment_needed'] == 0){

				echo '2-'.encrypt_decrypt_password($addEdit['Data']['trace_id'], 'E');
			}
		}
		else {

			echo '0-0';
		}

		exit;
	}

	function customerpaymentformdetails($lead_id)
	{

		$response = json_decode(curlFunction(SERVICE_URL . '/api2/checkIfOtpRequired', ['lead_id' => $lead_id]), true);

		if($response['response'] == 1){

			$data['lead_id'] = encrypt_decrypt_password($lead_id, 'D');
			$data['customer'] = true;

			$result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);

			$customers = reset($result['customer_details']);

			foreach ($customers as $customer) {

				$requestData = [
					'customer_id' => $customer['customer_id'],
					'lead_id' => encrypt_decrypt_password($lead_id, 'D')
				];

				$mode = 'addEdit';
				$members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
				$questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
				$answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

				$customer_id = $customer['customer_id'];

				$html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
				$result['ghd_declaration'][$customer['customer_id']] = $html;
			}

			$result['lead_id_enc'] = $lead_id;
			$_SESSION[$lead_id] = $lead_id;

			//unset($_SESSION['lead_id']);
			
			$this->load->helper('form');
			$this->load->view('template/customer-header.php');
			$this->load->view('customer/addEditCustomer', $result);
			$this->load->view('template/customer-footer.php');
		}
		else if($response['response'] == 2 || $response['response'] == 3){
			
			redirect(base_url().'paymentgatewayredirect/'.$lead_id); 
		}
		else if($response['response'] == 4){

			$result['msg'] = $response['msg'];
			$this->load->view('template/customer-header.php');
			$this->load->view('customer/link-expired', $result);
			$this->load->view('template/customer-footer.php');
		}
		else if($response['response'] == 5){

			redirect(base_url().'ghdverified/'.encrypt_decrypt_password($response['trace_id']), 'E');
		}
		else{

			$this->load->view('template/customer-header.php');
			$this->load->view('customer/tryagain.php', ['try_again' => 0]);
			$this->load->view('template/customer-footer.php');
		}

		/*if(isset($_SESSION['lead_id']) && $_SESSION['lead_id'] != ''){

			$result = array();
			$data = array();

			/*if (isset($_SESSION['webpanel']['customerotp']) && isset($_SESSION['webpanel']['customerlead'])) {
				$data['lead_id'] = $_SESSION['webpanel']['customerlead'];
				$data['otp'] = $_SESSION['webpanel']['customerotp'];
				$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL . '/api2/getLeadDetailsCustomer', $data));
				$this->load->view('template/header.php');
				$this->load->view('policyproposal/addEditCustomer', $result); //Payment form link needs to be added here
				$this->load->view('template/footer.php');
			} else {
				$data['lead_id'] = $_POST['lead_id'];
				$data['otp'] = $_POST['otp'];
				$addEdit = json_decode(curlFunction(SERVICE_URL . '/api2/checkLeadotpCustomer', $data));
				if ($addEdit['status_code'] == '200') {
					$_SESSION['webpanel']['customerlead'] = $addEdit['Data']['lead_id'];
					$_SESSION['webpanel']['customerotp'] = $addEdit['Data']['otp'];
					echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message']));
					exit;
				} else {
					echo json_encode(array('success' => false, 'msg' => $addEdit['Metadata']['Message']));
					exit;
				}
			}*/

			/*$lead_id = $_SESSION['lead_id'];

			$data['lead_id'] = encrypt_decrypt_password($lead_id, 'D');
			$data['customer'] = true;

			$result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);

			$customers = reset($result['customer_details']);

			foreach ($customers as $customer) {

				$requestData = [
					'customer_id' => $customer['customer_id'],
					'lead_id' => encrypt_decrypt_password($lead_id, 'D')
				];

				$mode = 'addEdit';
				$members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
				$questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
				$answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

				$customer_id = $customer['customer_id'];

				$html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
				$result['ghd_declaration'][$customer['customer_id']] = $html;
			}

			$result['lead_id_enc'] = $lead_id;

			//unset($_SESSION['lead_id']);
			
			$this->load->helper('form');
			$this->load->view('template/customer-header.php');
			$this->load->view('customer/addEditCustomer', $result);
			$this->load->view('template/customer-footer.php');
		}
		else {

			$prev_url = $_SESSION['prev_url'];
			redirect($prev_url);
		}*/
	}

	public function coi_download(){

		$certificate_no = $this->input->post('certificate_no');
		$certificate_no = htmlspecialchars(strip_tags(trim($certificate_no)));

		$data = ['certificate_no' => $certificate_no];
		echo curlFunction(SERVICE_URL . '/api2/coi_download', $data);
	}

	public function ghdverificationresponse($lead_id){
		
		$data['lead_id'] = $lead_id;
		$this->load->view('template/customer-header.php');
		$this->load->view('customer/ghd-verified.php', $data);
		$this->load->view('template/customer-footer.php');
	}
}
