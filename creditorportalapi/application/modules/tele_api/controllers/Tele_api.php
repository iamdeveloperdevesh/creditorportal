<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use function PHPSTORM_META\type;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once'/var/www/html/benefitz.in/fyntune-creditor-portal/vendor/autoload.php';

require_once'/var/www/html/benefitz.in/fyntune-creditor-portal/vendor/phpmailer/phpmailer/src/Exception.php';
require_once'/var/www/html/benefitz.in/fyntune-creditor-portal/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once'/var/www/html/benefitz.in/fyntune-creditor-portal/vendor/phpmailer/phpmailer/src/SMTP.php';
// session_start(); //we need to call PHP's session object to access it through CI
class Tele_api extends CI_Controller
{
	public $algoMethod;
	public $hashMethod;
	public $hash_key;
	public $encrypt_key;

	function __construct()
	{
	  //  echo 123;exit;
		parent::__construct();
		$this->load->model('apimodel', '', TRUE);
		// Load these helper to create JWT tokens
		$this->load->helper(['core_helper', 'jwt', 'authorization_helper']);

		//$this->load->helper(['jwt', 'authorization']);

		ini_set('memory_limit', '25M');
		ini_set('upload_max_filesize', '25M');
		ini_set('post_max_size', '25M');
		ini_set('max_input_time', 3600);
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		allowCrossOrgin();
        $this->db= $this->load->database('telesales_fyntune',TRUE);
		$this->algoMethod = 'aes-128-ecb';
		$this->hashMethod = 'SHA512';
		$this->hash_key = 'razorpay';
		$this->encrypt_key = 'axisbank12345678';
	}

	function getallheaders_new()
	{
		return $response_headers = getallheaders_values();
	}

	//For generating random string
	private function generateRandomString($length = 8, $charset = "")
	{
		if ($charset == 'N') {
			$characters = '0123456789';
		} else {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	private function verify_request($token)
	{
		// Get all the headers
		//$headers = $this->input->request_headers();
		// Extract the token
		//$token = $headers['Authorization'];
		// Use try-catch
		// JWT library throws exception if the token is not valid
		try {
			// Validate the token
			// Successfull validation will return the decoded user data else returns false
			$data = AUTHORIZATION::validateToken($token);
			if ($data === false) {
				//$this->errorResponse['message'] = 'Unauthorized Access!';
				//$this->responseData($this->errorResponse);

				return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
				exit;
			} else {
				return $data;
			}
		} catch (Exception $e) {

			// Token is invalid
			// Send the unathorized access message
			//$this->errorResponse['message'] = 'Unauthorized Access!';
			//$this->responseData($this->errorResponse);

			return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
			exit;
		}
	}

	function getBankDetails()
	{

		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {

			$term = isset($_POST['data']['term']) ? $_POST['data']['term'] : '';
			$term = htmlspecialchars(strip_tags(trim($term)));
			$term = strtoupper($term);

			$bank_details_raw = $this->apimodel->getdata('master_bank_details', 'ifsc_code, bank_name, branch', "ifsc_code LIKE '%" . $term . "%' LIMIT 20");

			$bank_details = [];
			if (!empty($bank_details_raw)) {

				foreach ($bank_details_raw as $bank_info) {

					$bank_details[$bank_info['ifsc_code']] = $bank_info;
				}
			}

			$resp = ['status_code' => 200, 'Data' => $bank_details];
			echo json_encode($resp);
		} else {
			echo $checkToken;
		}

		exit;
	}

	function getAssignmentDeclaration()
	{

		$lead_declaration = $this->apimodel->getdata('lead_assignment_declaration', 'content');

		if (!empty($lead_declaration)) {

			echo json_encode($lead_declaration[0]);
			exit;
		} else {

			echo json_encode($lead_declaration);
			exit;
		}
	}

	function getAssignmentDeclarationDetails()
	{

		$lead_declaration = $this->apimodel->getdata('lead_assignment_declaration', 'content');

		if (!empty($lead_declaration)) {

			return $lead_declaration[0];
		} else {

			return $lead_declaration;
		}
	}

	function saveAssignmentDeclaration()
	{

		if (isset($_POST['lead_id']) && $_POST['lead_id'] != '') {
			if (isset($_POST['value']) && $_POST['value'] != '') {

				$update_arr = ['assignment_declaration' => $_POST['value']];
				$result = $this->apimodel->updateRecordarr('lead_details', $update_arr, 'lead_id = ' . $_POST['lead_id']);

				insert_application_log($_POST['lead_id'], "save_assignment_declaration", json_encode($update_arr), json_encode(array("result" => $result)), $_POST['user_id']);

				echo json_encode(array("result" => $result));
				exit;
			}
		}
	}

	function getpolicypremiumflat($id, $suminsured)
	{
		//$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $id))->row()->premium_type; // new added

		$ptype = $this->db->get_where("master_policy", array("policy_id" => $id))->row()->premium_type;

		if ($ptype == 1) {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $id AND sum_insured = $suminsured");
		} else {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $id");
		}

		if (!empty($rate)) {
			if ($ptype == 1) {
				$r['amount'] = $rate[0]->premium_rate;
			} else {
				$r['amount'] = ($suminsured / 1000) * $rate[0]->premium_rate;
			}

			if ($rate[0]->is_taxable) {
				$r['tax'] = $r['amount'] * $this->config->item('tax') / 100;
			}
		} else {
			$r = 0;
		}
		return $r;
	}


	function getpolicypremiumfamilyconstruct($policy, $sum_insured, $adult, $child)
	{

		//$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;

		$ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;

		if ($ptype == 1) {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured AND adult_count = $adult AND child_count = $child");
		} else {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND adult_count = $adult AND child_count = $child");
		}

		if (!empty($rate)) {
			if ($ptype == 1) {
				$r['amount'] = $rate[0]->premium_rate;
			} else {
				$r['amount'] = ($sum_insured / 1000) * $rate[0]->premium_rate;
			}
			if ($rate[0]->is_taxable) {
				$r['tax'] = $r['amount'] * $this->config->item('tax') / 100;
			}
		} else {
			$r = 0;
		}
		return $r;
	}

	function getpolicypremiumfamilyconstructage($proposal_details_id, $policy, $sum_insured, $adult, $child, $age)
	{
		//$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;

		//echo $policy.":::testing";

		$ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;

		if ($ptype == 1) {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured AND adult_count = $adult AND child_count = $child 
			        AND min_age <= $age AND max_age >= $age");
		} else {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND adult_count = $adult AND 
			child_count = $child AND min_age <= $age AND max_age >= $age");
		}

		if ($ptype == 1) {
			$pamount = $rate[0]->premium_rate;
			$r['amount'] = $pamount;

			if ($rate[0]->premium_with_tax) {
				//$r['tax'] = $pamount * $rate[0]->premium_with_tax/100;
				$r['tax'] = $rate[0]->premium_with_tax;
			} else {
				//$r['tax'] =  ($pamount  * 0.18) + $pamount;
				$r['tax'] =  $pamount  * 0.18;
			}
		} else {
			$pamount = ($sum_insured / 1000) * $rate[0]->premium_rate;

			if ($rate[0]->premium_with_tax) {
				$r['tax'] = $rate[0]->premium_with_tax;
			} else {
				$r['tax'] =  $pamount  * 0.18;
			}
		}

		# old code 
		/*
		if(!empty($rate)) {
			$trate = $this->apimodel->getdata1("proposal_policy_member","premium"," policy_id = $proposal_details_id AND lead_id = $lead_id ORDER BY premium DESC");
			if(!empty($trate) && $trate[0]->premium > $pamount){
				$r['amount'] = $trate[0]->premium;
				if($rate[0]->is_taxable){
					$r['tax'] = $trate[0]->premium*$this->config->item('tax')/100;
				}
			}else{
				$r['amount'] = $pamount;
				if($rate[0]->is_taxable){
					$r['tax'] = $pamount*$this->config->item('tax')/100;
				}
			}
		}else{
			$r = 0;
		}
		***/
		# 
		return $r;
	} // EO ()


	function getpolicypremiummemberage($policy, $sum_insured, $age)
	{
		$ptype = $this->db->get_where("master_policy", array("master_policy_id" => $policy))->row()->premium_type;
		if ($ptype == 1) {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $id AND sum_insured = $sum_insured AND min_age >= $age AND max_age <= $age");
		} else {
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $id AND min_age >= $age AND max_age <= $age");
		}
		if (!empty($rate)) {
			if ($ptype == 1) {
				$r['amount'] = $rate[0]->premium_rate;
			} else {
				$r['amount'] = ($sum_insured / 1000) * $rate[0]->premium_rate;
			}
			if ($rate[0]->is_taxable) {
				$r['tax'] = $trate[0]->premium * $this->config->item('tax') / 100;
			}
		} else {
			$r = 0;
		}
		return $r;
	}
	function gettotalpremium($id, $sibasis, $lead_id, $policy_id)
	{

		$rates = $this->apimodel->getdata1("proposal_policy_member", "premium,tax", " proposal_policy_id = $id AND lead_id = $lead_id AND policy_id = $policy_id");
		$total['amount'] = 0;
		$total['tax'] = 0;
		if (!empty($rates)) {
			if ($sibasis == 1) {
				foreach ($rates as $rate) {
					$total['amount'] = $total['amount'] + $rate->premium;
					$total['tax'] = $total['tax'] + $rate->tax;
				}
			}
			if ($sibasis == 2) {
				foreach ($rates as $rate) {
					$total['amount'] = $rate->premium;
					$total['tax'] = $rate->tax;
				}
			}
			if ($sibasis == 3) {
				$count = count($rates);
				$value = 0;
				$tax = 0;
				foreach ($rates as $rate) {
					if ($rate->premium > $value)
						$value = $rate->premium;
					$tax = $rate->tax;
				}
				$total['amount'] = $value;
				$total['tax'] = $tax;
			}
			if ($sibasis == 4) {
				foreach ($rates as $rate) {
					$total['amount'] = $total['amount'] + $rate->premium;
					$total['tax'] = $total['tax'] + $rate->tax;
				}
			}
		}
		return $total;
	}
	// Previous code
	/* function gettotalpremium($id,$sibasis,$lead_id){
		
		$rates = $this->apimodel->getdata1("proposal_policy_member","premium,tax"," proposal_policy_id = $id AND lead_id = $lead_id");
		$total['amount'] = 0;
		$total['tax'] = 0;
		if(!empty($rates)){
		if($sibasis == 1){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
		if($sibasis == 2){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
		if($sibasis == 3){  
			$count = count($rates);  
			$value = 0;
			$tax = 0;
			foreach($rates as $rate){
				if($rate->premium > $value)
				$value = $rate->premium;
				$tax = $rate->tax;
			}
			$total = $count*$value;
			$total['amount'] = $count*$value;
			$total['tax'] = $count*$tax;
		}
		if($sibasis == 4){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
	}
		return $total;
		
	} */

	function checkmemberage($id, $member_type_id, $age)
	{
		$memberage = $this->apimodel->getdata1("master_policy_family_construct", "*", " master_policy_id = $id AND member_type_id = $member_type_id AND isactive = 1");

		if (count($memberage) > 0) {
			$minage = (!empty($memberage[0]->member_min_age)) ? $memberage[0]->member_min_age : 0;
			$maxage = (!empty($memberage[0]->member_max_age)) ? $memberage[0]->member_max_age : 125;
			if ($age >= $minage && $age <= $maxage) {
				return 1;
			} else {
				return "Member age must be between $minage and $maxage";
			}
		} else {
			return 1;
		}
	}
	function addProposalMember()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$member_type_id = (!empty($_POST['family_members_id'])) ? $_POST['family_members_id'] : '';
			$age = (!empty($_POST['age'])) ? $_POST['age'] : '';
			$sitypes = (!empty($_POST['sitypes'])) ? $_POST['sitypes'] : '';
			$policy_nos = (!empty($_POST['policy_nos'])) ? $_POST['policy_nos'] : '';
			$sibasis = (!empty($_POST['sibasis'])) ? $_POST['sibasis'] : '';
			$sum_insured = (!empty($_POST['sum_insured'])) ? $_POST['sum_insured'] : '';
			$i = 0;
			$member_details = array();
			$total_premium = array();
			$key = time();
			$member_details['member_unique_id'] = $key;
			$member_details['lead_id'] = $_POST['lead_id'];
			$member_details['trace_id'] = $_POST['trace_id'];

			$member_details['policy_member_age'] = $age;
			$member_details['relation_with_proposal'] = (!empty($_POST['family_members_id'])) ? $_POST['family_members_id'] : '';
			$adult = (!empty($_POST['adultcount'])) ? $_POST['adultcount'] : '0';
			$child = (!empty($_POST['childcount'])) ? $_POST['childcount'] : '0';
			$member_details['policy_member_salutation'] = (!empty($_POST['family_salutation'])) ? $_POST['family_salutation'] : '';
			$member_details['policy_member_gender'] = (!empty($_POST['family_gender'])) ? $_POST['family_gender'] : '';
			$member_details['policy_member_first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
			$member_details['policy_member_last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
			$member_details['policy_member_pan'] = (!empty($_POST['policy_member_pan'])) ? $_POST['policy_member_pan'] : '';
			$member_details['policy_member_dob'] = (!empty($_POST['family_date_birth'])) ? date('Y-m-d', strtotime($_POST['family_date_birth'])) : '';
			$proposal_details_id = $_POST['proposal_details_id'];

			foreach ($policy_nos as $policy) {
				$pdetails = $this->apimodel->checkproposalpolicy($member_details['lead_id'], $policy, $proposal_details_id);
				if (count($pdetails) > 0) {
					$member_details['policy_id'] = $pdetails[0]->policy_id;
					if (empty($pdetails[0]->sum_insured)) {
						$ipolicy = array();
						$ipolicy['sum_insured'] = $sum_insured;
						$ipolicy['adult_count'] = $_POST['adultcount'];
						$ipolicy['child_count'] = $_POST['childcount'];
						$this->apimodel->updateRecordarr('proposal_policy', $ipolicy, array('master_policy_id' => $policy, 'proposal_details_id' => $proposal_details_id));
					}
				} else {
					$masteredetails = $this->apimodel->getPolicyDetails($policy);
					$ipolicy = array();
					$ipolicy['lead_id'] = $member_details['lead_id'];
					$ipolicy['trace_id'] = $member_details['trace_id'];
					$ipolicy['master_policy_id'] = $policy;
					$ipolicy['proposal_details_id'] = $policy;
					$ipolicy['policy_sub_type_id'] = $masteredetails[0]->policy_sub_type_id;
					$policysubtypename = $this->apimodel->getsubtypenamebyid($masteredetails[0]->policy_sub_type_id);
					$ipolicy['policy_sub_type_name'] = $policysubtypename;
					$ipolicy['insurer_id'] = $masteredetails[0]->insurer_id;
					$ipolicy['policy_number'] = $masteredetails[0]->policy_number;
					$ipolicy['policy_number'] = $masteredetails[0]->policy_number;
					$ipolicy['pdf_type'] = $masteredetails[0]->pdf_type;
					$ipolicy['is_combo'] = $masteredetails[0]->is_combo;
					$ipolicy['is_optional'] = $masteredetails[0]->is_optional;
					$ipolicy['sum_insured'] = $sum_insured;
					$ipolicy['adult_count'] = $_POST['adultcount'];
					$ipolicy['proposal_details_id'] = $proposal_details_id;
					$ipolicy['child_count'] = $_POST['childcount'];
					$insert_id = $this->apimodel->insertData('proposal_policy', $ipolicy);
					$member_details['policy_id'] = $insert_id;
				}
			}
			foreach ($policy_nos as $policy) {
				$ageresponse = $this->checkmemberage($policy, $member_type_id, $age);
				if ($ageresponse != 1) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $ageresponse)));
					exit;
				}
			}
			foreach ($policy_nos as $policy) {
				if ($sibasis[$i] == 1) {
					$rate = $this->getpolicypremiumflat($policy, $sum_insured);
					if ($rate != 0) {
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if (!empty($rate['tax'])) {
							$member_details['tax'] = $rate['tax'];
						}
						$this->apimodel->insertData('proposal_policy_member', $member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $member_details['lead_id'], $member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('proposal_details_id' => $proposal_details_id, 'master_policy_id' => $policy));
					}
				} else if ($sibasis[$i] == 2) {

					$rate = $this->getpolicypremiumfamilyconstruct($policy, $sum_insured, $adult, $child);

					if ($rate != 0) {
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if (!empty($rate['tax'])) {
							$member_details['tax'] = $rate['tax'];
						}
						$this->apimodel->insertData('proposal_policy_member', $member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $member_details['lead_id'], $member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('proposal_details_id' => $proposal_details_id, 'master_policy_id' => $policy));
					}
				} else if ($sibasis[$i] == 3) {
					$rate = $this->getpolicypremiumfamilyconstructage($member_details['policy_id'], $policy, $sum_insured, $adult, $child, $age);
					if ($rate != 0) {
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if (!empty($rate['tax'])) {
							$member_details['tax'] = $rate['tax'];
						}
						$this->apimodel->insertData('proposal_policy_member', $member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $member_details['lead_id'], $member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('proposal_details_id' => $proposal_details_id, 'master_policy_id' => $policy));
					}
				} else if ($sibasis[$i] == 4) {
					$rate = $this->getpolicypremiummemberage($policy, $sum_insured, $age);
					if ($rate != 0) {
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if (!empty($rate['tax'])) {
							$member_details['tax'] = $rate['tax'];
						}
						$this->apimodel->insertData('proposal_policy_member', $member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $member_details['lead_id'], $member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('proposal_details_id' => $proposal_details_id, 'master_policy_id' => $policy));
					}
				}
				$i++;
			}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => "Success"), "Data" => $total_premium, "Key" => $key));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function checkInsuredMembersExist()
	{
		$query = 'select count(*) as count from proposal_policy where proposal_details_id=' . $this->input->post('proposal_details_id');
		$result = $this->db->query($query)->result();

		if ($result[0]->count > 0) {
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => "Member exists")));
		} else {
			echo json_encode(array("status_code" => "204", "Metadata" => array("Message" => "No member exists")));
		}
		exit;
	}

	function deletePolicyMember()
	{

		$policy_nos = (!empty($_POST['policy_nos'])) ? $_POST['policy_nos'] : '';
		$lead_id = (!empty($_POST['lead_id'])) ? $_POST['lead_id'] : '';
		$sibasis = (!empty($_POST['sibasis'])) ? $_POST['sibasis'] : '';
		$member_unique_id = (!empty($_POST['id'])) ? $_POST['id'] : '';
		$this->apimodel->delrecord('proposal_policy_member', 'member_unique_id', $member_unique_id);
		$i = 0;
		$total_premium = array();
		foreach ($policy_nos as $policy) {
			if ($sibasis[$i] == 1) {
				$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $lead_id);
				$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('lead_id' => $lead_id, 'master_policy_id' => $policy));
			} else if ($sibasis[$i] == 2) {
				$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $lead_id);
				$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('lead_id' => $lead_id, 'master_policy_id' => $policy));
			} else if ($sibasis[$i] == 3) {
				$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $lead_id);
				$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('lead_id' => $lead_id, 'master_policy_id' => $policy));
			} else if ($sibasis[$i] == 4) {
				$total_premium[$policy] = $this->gettotalpremium($policy, $sibasis[$i], $lead_id);
				$this->apimodel->updateRecordarr('proposal_policy', array('premium_amount' => $total_premium[$policy]['amount'], 'tax_amount' => $total_premium[$policy]['tax']), array('lead_id' => $lead_id, 'master_policy_id' => $policy));
			}
			$i++;
		}
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => "Success"), "Data" => $total_premium));
		exit;
	}

	function get_tiny_url($url)
	{
		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	/*function proposalFinalSubmit()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$lead_id = $this->input->post('lead_id');
			$trace_id = $this->input->post('trace_id');
			$data['mode_of_payment'] = $this->input->post('mode_of_payment');
			$data['updated_on'] = date('Y-m-d H:i:s');

			//$data['preffered_contact_date'] = $this->input->post('preffered_contact_date');
			//$data['remark'] = $this->input->post('remark');
			//$data['preffered_contact_time'] = $this->input->post('preffered_contact_time');


			if ($data['mode_of_payment'] == "Cheque") {
				if (!file_exists(ABSOLUTE_DOC_ROOT . 'assets/leaddocuments/' . $lead_id)) {
					mkdir(ABSOLUTE_DOC_ROOT . 'assets/leaddocuments/' . $lead_id, 0777, true);
				}
				/*
				$file_ext = pathinfo($_FILES['testfile']['name'], PATHINFO_EXTENSION);
				$savename = "shani_12".'.'.$file_ext;
				$result = $this->do_upload($config,'testfile',$savename);
				***/

	/*$config = array();
				$config['upload_path']   = ABSOLUTE_DOC_ROOT . 'assets/leaddocuments/' . $lead_id . '/';
				$config['allowed_types'] = '*';
				$config['max_size']      = 2048;

				$enrollment_form = $_FILES['enrollment_form']['name'];
				$cheque_copy = $_FILES['cheque_copy']['name'];
				$itr = $_FILES['itr']['name'];
				$cam = $_FILES['cam']['name'];
				$medical = $_FILES['medical']['name'];

				if (!empty($cheque_copy)) {
					$file_ext = pathinfo($_FILES['cheque_copy']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $lead_id . '-Cheque_Copy' . '.' . $file_ext;
					$res = $this->do_upload($config, 'cheque_copy', $savename, $lead_id);
				}
				if (!empty($enrollment_form)) {
					$file_ext = pathinfo($_FILES['enrollment_form']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $lead_id . '-Enrollment_form' . '.' . $file_ext;
					$res = $this->do_upload($config, 'enrollment_form', $savename, $lead_id);
				}
				if (!empty($itr)) {
					$file_ext = pathinfo($_FILES['itr']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $lead_id . '-ITR' . '.' . $file_ext;
					$res = $this->do_upload($config, 'itr', $savename, $lead_id);
				}
				if (!empty($cam)) {
					$file_ext = pathinfo($_FILES['cam']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $lead_id . '-CAM_report' . '.' . $file_ext;
					$res = $this->do_upload($config, 'cam', $savename, $lead_id);
				}
				if (!empty($medical)) {
					$file_ext = pathinfo($_FILES['medical']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $lead_id . '-Medical_report' . '.' . $file_ext;
					$res = $this->do_upload($config, 'medical', $savename, $lead_id);
				}


				$data['bank_name'] = $this->input->post('bank_name');
				$data['bank_branch'] = $this->input->post('bank_branch');
				$data['bank_city'] = $this->input->post('bank_city');
				$data['ifsc_code'] = $this->input->post('ifsc_code');
				$data['account_number'] = $this->input->post('account_number');
				$data['cheque_number'] = $this->input->post('cheque_number');
				$data['cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
				$data['status'] = "BO-Approval-Awaiting";
			} else if ($data['mode_of_payment'] == "NEFT") {
				$data['status'] = "CO-Approval-Awaiting";
			} else if ($data['mode_of_payment'] == "Online Payment") {
				$id = rtrim(strtr(base64_encode("id=" . $lead_id), '+/', '-_'), '=');
				$url = base_url() . "policyproposal/customerpaymentform?text=$id";
				$actual_url  = base_url() . "policyproposal/customerpaymentform?text=$lead_id";


				$short_url = $this->get_tiny_url($url);
				$shorturl_data = array();
				$shorturl_data['long_url'] = $actual_url;
				$shorturl_data['short_code'] = $short_url;
				$shorturl_data['proposal_id'] = $trace_id;
				$shorturl_data['lead_id'] = $lead_id;
				$result = $this->apimodel->insertData('short_urls', $shorturl_data, 1);
				$data['status'] = "Customer-Payment-Awaiting";
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Invalid Payment Mode Selected.'), "Data" => NULL));
				exit;
			}
			if (!empty($data)) {
				$result1 = $this->apimodel->updateRecord('proposal_details', $data, "lead_id='" . $lead_id . "' ");
			}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated Successfully')));
			exit;
		} else {
			echo $checkToken;
		}
	}*/

	function proposalFinalSubmit($data = null)
	{
        if($_POST['utoken']){
            $checkToken = $this->verify_request($_POST['utoken']);
        }
        if($data != null){
            $_POST=$data;
            $checkToken->username=$_POST['created_by'];
        }else{

        }

        if (!empty($checkToken->username)) {

			$data = $proposal_payment_data = $file_upload_urls = [];
			$lead_id = $this->input->post('lead_id');
			$plan_id = $this->input->post('plan_id');

			$lead_data = $this->apimodel->getLeadByID('creditor_id,trace_id,lan_id', $lead_id);
			$master_quotes = $this->apimodel->getMasterQuotesByLeadID('master_policy_id, age, premium, premium_with_tax, sum_insured', $lead_id);
			foreach ($lead_data as $lead) {

				$data['lead_id'] = $proposal_payment_data['lead_id'] = $lead_id;
				$proposal_payment_data['creditor_id'] = $lead['creditor_id'];
				$data['trace_id'] = $proposal_payment_data['trace_id'] = $lead['trace_id'];
				$proposal_payment_data['lan_id'] = $lead['lan_id'];
			}

			/*$master_policy_arr = $this->apimodel->getdata('master_policy', 'policy_id, policy_sub_type_id', "plan_id = $plan_id AND creditor_id = ".$proposal_payment_data['creditor_id']);
			
			$policy_sub_type_id_arr = [];

			foreach($master_policy_arr as $master_policy){

				$policy_sub_type_id_arr[$master_policy['policy_id']] = $master_policy['policy_sub_type_id'];
			}*/

			$proposal_payment_data['premium'] = $proposal_payment_data['premium_with_tax'] = $proposal_payment_data['sum_insured'] = 0;
			$proposal_payment_data['go_green'] = (isset($_POST['go_green']) && $_POST['go_green'] != '') ? 'Y' : 'N';

			foreach ($master_quotes as $master_quote) {

				$proposal_payment_data['premium'] += $master_quote['premium'];
				$proposal_payment_data['premium_with_tax'] += $master_quote['premium_with_tax'];
				$proposal_payment_data['sum_insured'] += $master_quote['sum_insured'];
			}
			//$trace_id = $this->input->post('trace_id');
			$data['mode_of_payment'] = $proposal_payment_data['payment_mode'] = $this->input->post('mode_of_payment');
			$data['updated_on'] = date('Y-m-d H:i:s');

			//$data['preffered_contact_date'] = $this->input->post('preffered_contact_date');
			//$data['remark'] = $this->input->post('remark');
			//$data['preffered_contact_time'] = $this->input->post('preffered_contact_time');
			if ($data['mode_of_payment'] == 2) {

				$upload_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'leaddocuments' . DIRECTORY_SEPARATOR . $lead_id;

				if (!file_exists($upload_dir)) {

					mkdir($upload_dir, 0777, true);
				}

				$enrollment_form = isset($_FILES['enrollment_form']['name']) ? $_FILES['enrollment_form']['name'] : '';
				$cheque_copy = isset($_FILES['cheque_copy']['name']) ? $_FILES['cheque_copy']['name'] : '';
				$itr = isset($_FILES['itr']['name']) ? $_FILES['itr']['name'] : '';
				$cam = isset($_FILES['cam']['name']) ? $_FILES['cam']['name'] : '';
				$medical = isset($_FILES['medical']['name']) ? $_FILES['medical']['name'] : '';

				$docs_url = SERVICE_URL . 'uploads/leaddocuments/' . $lead_id;

				if (!empty($cheque_copy)) {
					$file_ext = pathinfo($_FILES['cheque_copy']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $proposal_payment_data['lan_id'] . '-Cheque_Copy' . '.' . $file_ext;
					$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
					if (move_uploaded_file($_FILES['cheque_copy']['tmp_name'], $path)) {

						$file_upload_urls['cheque_copy']['document_url'] = $docs_url . '/' . $savename;
					}
				}
				if (!empty($enrollment_form)) {
					$file_ext = pathinfo($_FILES['enrollment_form']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $proposal_payment_data['lan_id'] . '-Enrollment_form' . '.' . $file_ext;
					$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
					if (move_uploaded_file($_FILES['enrollment_form']['tmp_name'], $path)) {

						$file_upload_urls['enrollment_form']['document_url'] = $docs_url . '/' . $savename;
					}
				}
				if (!empty($itr)) {
					$file_ext = pathinfo($_FILES['itr']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $proposal_payment_data['lan_id'] . '-ITR' . '.' . $file_ext;
					$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
					if (move_uploaded_file($_FILES['itr']['tmp_name'], $path)) {

						$file_upload_urls['itr']['document_url'] = $docs_url . '/' . $savename;
					}
				}
				if (!empty($cam)) {
					$file_ext = pathinfo($_FILES['cam']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $proposal_payment_data['lan_id'] . '-CAM_report' . '.' . $file_ext;
					$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
					if (move_uploaded_file($_FILES['cam']['tmp_name'], $path)) {

						$file_upload_urls['cam']['document_url'] = $docs_url . '/' . $savename;
					}
				}
				if (!empty($medical)) {
					$file_ext = pathinfo($_FILES['medical']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $proposal_payment_data['lan_id'] . '-Medical_report' . '.' . $file_ext;
					$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
					if (move_uploaded_file($_FILES['medical']['tmp_name'], $path)) {

						$file_upload_urls['medical']['document_url'] = $docs_url . '/' . $savename;
					}
				}

				if (!empty($_FILES['file_type'])) {
					$file_ext = pathinfo($_FILES['file_type']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-' . $proposal_payment_data['lan_id'] . '-File_type' . '.' . $file_ext;
					$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
					if (move_uploaded_file($_FILES['file_type']['tmp_name'], $path)) {

						$file_upload_urls['file_type']['document_url'] = $docs_url . '/' . $savename;
					}
				}

				$data['bank_name'] = $proposal_payment_data['bank_name'] = $this->input->post('bank_name');
				$data['bank_branch'] = $proposal_payment_data['bank_branch'] = $this->input->post('bank_branch');
				$data['bank_city'] = $proposal_payment_data['bank_city'] = $this->input->post('bank_city');
				$data['ifsc_code'] = $proposal_payment_data['ifsc_code'] = $this->input->post('ifsc_code');
				$data['account_number'] = $proposal_payment_data['account_number'] = $this->input->post('account_number');
				$data['cheque_number'] = $proposal_payment_data['cheque_number'] = $this->input->post('cheque_number');
				$proposal_payment_data['id_document_type'] = isset($_POST['id_document_type']) ? $_POST['id_document_type'] : '';
				$data['cheque_date'] = $proposal_payment_data['cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
				//$data['status'] = "BO-Approval-Awaiting"; //"Pending Ops Verification"; //"BO-Approval-Awaiting";

			} else if ($data['mode_of_payment'] == 3) {

				//$data['status'] = "CO-Approval-Awaiting";

			} else if ($data['mode_of_payment'] == 1) {

				//$data['status'] = "Customer-Payment-Awaiting";

			}else if ($data['mode_of_payment'] == 4) {

                //$data['status'] = "Customer-Payment-Awaiting";

            } else {

				$api_response = json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Invalid Payment Mode Selected.'), "Data" => NULL));
				insert_application_log($lead_id, "proposal_final_submit", json_encode($_POST), $api_response, $_POST['created_by']);
                if($_POST['is_api'] == 1){
                    return $api_response;
                }else{
                    echo $api_response;
                    exit;
                }
			}

			if (!empty($data) && !empty($proposal_payment_data)) {
				if (!isset($data['status'])) {
 
					$tables = "plan_payment_mode ppm, payment_workflow_master pwm";
					$column = "pwm.related_status";
					$condition = "ppm.master_plan_id = $plan_id AND ppm.payment_mode_id = " . $data['mode_of_payment'] . " AND ppm.isactive = 1 AND pwm.isactive = 1 AND ppm.workflow_id = pwm.payment_workflow_master_id";

					$proposal_status = $this->apimodel->getdata($tables, $column, $condition);

					if (!empty($proposal_status)) {

						foreach ($proposal_status as $key => $status) {

							$data['status'] = $status['related_status'];
						}
					} else {

						$api_response = json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No status is assosciated with the this payment mode'), "Data" => NULL));
						insert_application_log($lead_id, "proposal_final_submit", json_encode($_POST), $api_response, $_POST['created_by']);
                        if($_POST['is_api'] == 1){
                            return $api_response;
                        }else{
                            echo $api_response;
                            exit;
                        }
					}
				}


				$proposal_payments_details = $this->apimodel->getdata('proposal_payment_details', 'proposal_payment_id', "lead_id = $lead_id");
				if (empty($proposal_payments_details)) {
					if ($data['mode_of_payment'] == 2) {
						$proposal_payment_data['transaction_number'] = $this->input->post('cheque_number');
						$proposal_payment_data['transaction_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
					}

					$proposal_payment_data['created_at'] = date('Y-m-d H:i:s');
					$proposal_payment_data['created_by'] = $this->input->post('created_by');
					$proposal_payments_id = $this->apimodel->insertData('proposal_payment_details', $proposal_payment_data, 1);
					insert_application_log($lead_id, "proposal_final_submit", json_encode($proposal_payment_data), json_encode(array("insert_id" => $proposal_payments_id)), $_POST['created_by']);
				} else {

					$proposal_payment_data['updated_at'] = date('Y-m-d H:i:s');
					$proposal_payment_data['updated_by'] = $this->input->post('created_by');
					$result1 = $this->apimodel->updateRecord('proposal_payment_details', $proposal_payment_data, "lead_id='" . $lead_id . "' ");
					/*if($result1 == false){
                        $proposal_payment_data['created_at'] = date('Y-m-d H:i:s');
                        $proposal_payment_data['created_by'] = $this->input->post('created_by');
                        $result1 = $this->apimodel->insertData('proposal_payment_details', $proposal_payment_data, 1);
                    }*/
					insert_application_log($lead_id, "proposal_final_submit", json_encode($proposal_payment_data), json_encode(array("update_result" => $result1)), $_POST['created_by']);
				}

				$result1 = $this->apimodel->updateRecord('proposal_details', $data, "lead_id='" . $lead_id . "'");
				insert_application_log($lead_id, "proposal_final_submit", json_encode($data), json_encode(array("update_result" => $result1)), $_POST['created_by']);

				$lead_data = [
					'mode_of_payment' => $proposal_payment_data['payment_mode'],
					'status' => $data['status'],
					'updatedby' => $_POST['created_by'],
					'updatedon' => date('Y-m-d H:i:s')
				];
				$result1 = $this->apimodel->updateRecord('lead_details', $lead_data, "lead_id='" . $lead_id . "' ");
				insert_application_log($lead_id, "proposal_final_submit", json_encode($data), json_encode(array("update_result" => $result1)), $_POST['created_by']);

				if (!empty($file_upload_urls)) {

					$payment_file_data = [];
					$payment_file_data['proposal_details_id'] = $this->input->post('proposal_id');

					foreach ($file_upload_urls as $doc_type => $file_upload_url) {

						$payment_file_data['lead_id'] = $lead_id;
						$payment_file_data['document_type'] = $doc_type;
						$payment_file_data['document_url'] = $file_upload_url['document_url'];
						//$payment_file_data['omnidocs_url'] = '';
						$payment_file_data['created_at'] = date('Y-m-d H:i:s');
						$payment_file_data['created_by'] = $_POST['created_by'];

						$result = $this->apimodel->insertData('proposal_payment_documents', $payment_file_data, 1);
						insert_application_log($lead_id, "proposal_final_submit", json_encode($payment_file_data), json_encode(array("insert_id" => $result)), $_POST['created_by']);
					}
				}

				if (in_array($data['mode_of_payment'], [1,3])) {

					//$id = rtrim(strtr(base64_encode("id=" . $lead_id), '+/', '-_'), '=');
					$id = encrypt_decrypt_password($lead_id);
					$url = FRONT_URL . "customerdetails/$id";
					$actual_url  = FRONT_URL . "customerdetails/$id";

					$otp = rand(100000, 999999);

					$short_url = $this->get_tiny_url($url);
					$shorturl_data = array();
					$shorturl_data['long_url'] = $actual_url;
					$shorturl_data['short_code'] = filter_var($short_url, FILTER_VALIDATE_URL) ? $short_url : $actual_url;
					$shorturl_data['proposal_id'] = $data['trace_id'];
					$shorturl_data['lead_id'] = $lead_id;
					$shorturl_data['otp'] = $otp;

					$get_short_url = $this->apimodel->getdata('short_urls', 'count(id) as short_url_count', 'lead_id = ' . $lead_id);

					if ($get_short_url[0]['short_url_count'] == 0) {

						$shorturl_data['created'] = date('Y-m-d H:i:s');
						$result = $this->apimodel->insertData('short_urls', $shorturl_data, 1);
						insert_application_log($lead_id, "proposal_final_submit", json_encode($shorturl_data), json_encode(array("insert_id" => $result)), $_POST['created_by']);
					} else {

						$shorturl_data['updated'] = date('Y-m-d H:i:s');
						$result = $this->apimodel->updateRecordarr('short_urls', $shorturl_data, 'lead_id = ' . $lead_id);
						insert_application_log($lead_id, "proposal_final_submit", json_encode($shorturl_data), json_encode(array("insert_id" => $result)), $_POST['created_by']);
					}
				}

				$api_response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated Successfully')));

				insert_application_log($lead_id, "proposal_final_submit", json_encode($_POST), $api_response, $_POST['created_by']);
				if($_POST['is_api'] == 1){
                   return $api_response;
                }else{
                    echo $api_response;
                    exit;
                }

			} else {

				$api_response = json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Something went wrong.'), "Data" => NULL));
				insert_application_log($lead_id, "proposal_final_submit", json_encode($_POST), $api_response, $_POST['created_by']);
                if($_POST['is_api'] == 1){
                    return $api_response;
                }else{
                    echo $api_response;
                    exit;
                }
			}
		} else {

			insert_application_log($_POST['lead_id'], "proposal_final_submit", json_encode($_POST), $checkToken, $_POST['created_by']);
			echo $checkToken;
			exit;
		}
	}

	public function test_upload()
	{
		$config = array();
		$config['upload_path']   = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'leaddocuments' . DIRECTORY_SEPARATOR;
		$config['allowed_types'] = '*';
		$config['max_size']      = 2048;
		//$filename = $this->input->post('testfile');
		//echo json_encode($_FILES);exit;
		$file_ext = pathinfo($_FILES['testfile']['name'], PATHINFO_EXTENSION);
		$savename = "shani_12" . '.' . $file_ext;
		$result = $this->do_upload($config, 'testfile', $savename, $this->input->post('lead_id'));
		echo json_encode($result);
	}
	public function do_upload($config, $filename, $savename, $lead_id = "")
	{
		$config['file_name'] = $savename;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload($filename)) {
			$error = array('error' => $this->upload->display_errors());
			return $error;
		} else {
			if ($lead_id != "") {
				$img = file_get_contents($config['upload_path'] . $savename);
				$fields = '{"Identifier":"ByteArray","UploadRequest":[{"CategoryID":"1003","DataClassParam":[{"Value":"' . $lead_id . '","DocSearchParamId":"22"}],"Description":"","ReferenceID":"3100","FileName":"$savename","DocumentID":"2224","ByteArray":"$filename","SharedPath":""}],"SourceSystemName":"Axis"}';
				$postField = json_decode($fields, true);
				$saveField = json_decode($fields, true);
				$postField['UploadRequest'][0]['ByteArray'] = base64_encode($img);
				$postField['UploadRequest'][0]['FileName'] = $savename;
				$this->docServiceCal($postField, $saveField);
				$data = array('upload_data' => $this->upload->data());
				return $data;
			} else {
				return true;
			}
		}
	}

	function docServiceCal($postField, $saveField)
	{

		$this->db->insert("logs_docs", [
			"req" => json_encode($saveField),
			"lead_id" => $saveField['UploadRequest'][0]['DataClassParam'][0]['Value'],
			"type" => "OmniDocs"
		]);

		//Application log entries
		$this->db->insert("application_logs", [
			"request_data" => json_encode($saveField),
			"lead_id" => $saveField['UploadRequest'][0]['DataClassParam'][0]['Value'],
			"action" => "OmniDocs_API_Call",
			"created_on" => date("Y-m-d H:i:s")
		]);

		$id = $this->db->insert_id();
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/uploadRequest",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($postField),
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"password: esb@axis@ABHI",
				"postman-token: a3f0ed2e-f9cc-f767-09ae-4c594e38d5f2",
				"username: esb_axis"
			),
		));

		$response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$this->db->where([
				"id" => $id
			])->update("logs_docs", [
				"res" => json_encode($err),
				"type" => "OmniDocs"
			]);
			// Monolog::saveLog("omniDocResError", "I", $err);
			// echo "cURL Error #:" . $err;

			//Application log entries
			$this->db->insert("application_logs", [
				"response_data" => json_encode($err),
				"lead_id" => $id,
				"action" => "OmniDocs_Error",
				"created_on" => date("Y-m-d H:i:s")
			]);
		} else {
			$this->db->where([
				"id" => $id
			])->update("logs_docs", [
				"res" => json_encode($response),
				"type" => "OmniDocs"
			]);
			// Monolog::saveLog("omniDocRes", "I", $response);
			// echo $response;

			//Application log entries
			$this->db->insert("application_logs", [
				"response_data" => json_encode($response),
				"lead_id" => $id,
				"action" => "OmniDocs_Success",
				"created_on" => date("Y-m-d H:i:s")
			]);
		}
	}

	function getPremiumSummary()
	{

		$lead_details = $this->apimodel->getLeadDetails($this->input->post('lead_id'));
		$earlierGeneratedPremiums = $this->apimodel->getGeneratedPremiums($_POST);

		// Calculate total applicants
		if ($lead_details[0]->is_coapplicant == "Y") {
			$total_applicants = $lead_details[0]->coapplicant_no + 1;
		} else {
			$total_applicants = 1;
		}

		$pan_form = '<form id="pan-form" action="' . FRONT_END_URL . 'policyproposal/capturecustomerpan" class="col-md-8">';

		$result = [];
		// Loop through All Applicants and Co-Applicants
		for ($i = 0; $i < $total_applicants; $i++) {

			// If there is no applicant details available for the current lead we will break 
			if (!current($lead_details)) {
				break;
			}

			$current_customer_id = current($lead_details)->customer_id;

			if ($i == 0) {
				$applicantName = "Applicant";
				$pan_form .= 'Applicant PAN: <input type="text" class="pan form-control" data-customer-id="' . $current_customer_id . '" name="applicant_pan">&nbsp;&nbsp;<span class="applicant_pan msg">&nbsp;&nbsp;</span><br>';
			} else {
				$applicantName = "Co-Applicant" . $i;
				$pan_form .= 'Co-Applicant ' . $i . ' PAN: <input type="text" class="pan form-control" data-customer-id="' . $current_customer_id . '" name="coapplicant_pan_' . $i . '">&nbsp;&nbsp;<span class="coapplicant_pan_' . $i . ' msg">&nbsp;&nbsp;</span><br>';
			}

			if (isset($earlierGeneratedPremiums[$current_customer_id])) {
				$result['data'][$applicantName]['policies'] = $earlierGeneratedPremiums[$current_customer_id];
			}

			next($lead_details);
		}

		$pan_form .= '<button type="submit" class="submit-btn-pan btn mr-2">Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></form>';

		$result['pan_form'] = $pan_form;

		echo json_encode($result);
		exit;
	}

	function generateQuote()
	{

		if(isset($_POST['source']) && $_POST['source']=="customer"){
			// exit('coming');
			// 	// echo $_POST['source'];exit;
			
			$_POST['customer_id']=encrypt_decrypt_password($_POST['customer_id'],'D');
			$_POST['trace_id']=encrypt_decrypt_password($_POST['trace_id'],'D');
			$_POST['plan_id']=encrypt_decrypt_password($_POST['plan_id'],'D');
			$_POST['lead_id']=encrypt_decrypt_password($_POST['lead_id'],'D');


		}else{
            $_POST['customer_id']=($_POST['customer_id']);
            $_POST['trace_id']=($_POST['trace_id']);
            $_POST['plan_id']=($_POST['plan_id']);
            $_POST['lead_id']=($_POST['lead_id']);
        }
	//	echo 123;exit;
//var_dump($_POST);die;


		$result['status'] = true;
		$result['messages'] = [];
		$hospi_cash_group_code = $group_code_type = '';
		$ghc_logic_plans = [407, 408, 409, 410, 411];


		$plan_details = $this->apimodel->getProductDetailsAll($this->input->post('plan_id'), 'mp.policy_sub_type_id', 'DESC');

		$family_construct_raw = $this->input->post('family_members_ac_count');



		if (!$family_construct_raw) {
			$result['status'] = false;
			echo json_encode($result);
			exit;
		}
		$family_construct = explode('-', $family_construct_raw);

		$adultsToCalculate = $family_construct[0];
		$childrenToCalculate = $family_construct[1];
		$earlierGeneratedPremiums = $this->apimodel->getGeneratedPremiums($_POST);
		$lead_details = $this->apimodel->getLeadDetails($this->input->post('lead_id'));



		$spouse_dob = $this->input->post('spouse_dob');

		$spouseAge = $this->input->post('spouse_age');

		if (empty($spouseAge) && !empty($spouse_dob)) {
			$spouseAge = date_diff(date_create($spouse_dob), date_create('today'))->y;
		}

		if ($adultsToCalculate == 1 && $spouseAge) {
			$member_to_calculate = 2;
		} else {
			$member_to_calculate = 1;
		}

		// Calculate total applicants
		if ($lead_details[0]->is_coapplicant == "Y") {
			$total_applicants = $lead_details[0]->coapplicant_no + 1;
		} else {
			$total_applicants = 1;
		}

		// Loop through All Applicants and Co-Applicants
		for ($i = 0; $i < $total_applicants; $i++) {

			$applicantPolicies = [];

			// If there is no applicant details available for the current lead we will break 
			if (!current($lead_details)) {
				break;
			}

			$current_customer_id = current($lead_details)->customer_id;

			if ($i == 0) {
				$applicantName = "Applicant";
			} else {
				$applicantName = "Co-Applicant" . $i;
			}

			if ($current_customer_id == $this->input->post('customer_id')) {
				// Loop through all policies in the plan and generate premiums
				// exit('12345');
				foreach ($plan_details as $policy) {
					$policy_id = $policy->policy_id;
					$policy_sub_type_code = $policy->policy_sub_type_code;
					$basis_id = $policy->basis_id;

					$members = $this->apimodel->getPolicyFamilyDetails($policy_id);


					$is_individual_cover = null;

					// Store the total children and adults allowed in the policy
					$child_count = 0;
					$adult_count = 0;

					foreach ($members as $key => $construct) {
						$adult_member_type_id = array(1, 2, 3, 4);
						if (in_array($members[$key]->member_type_id, $adult_member_type_id)) {
							$adult_count++;
						} else {
							$child_count++;
						}
					}


					if ($policy->sitype_id != 1) {
						$is_individual_cover = false;
					} else {
						$is_individual_cover = true;
					}

					// Reduce array in case of family cover and single adult
					if ($policy->sitype_id != 1 || $adultsToCalculate == 1) {
						$members = array_filter($members, function ($construct) use ($member_to_calculate) {
							return $construct->member_type_id == $member_to_calculate;
						});
					}

					$individual_adults_premium_calculated = 0;

					foreach ($members as $member) {

						$member_type = $member->member_type;
						// For Individual type if we have already calculated for required adults we will skip
						if ($individual_adults_premium_calculated == $adultsToCalculate) {
							break;
						}

						if(isset($_POST['source']) && strcmp($_POST['source'],"customer")){
							$proposersAge=$_POST['max_age'];
						}else{
							$proposersAge = $this->apimodel->getProposersAge($_POST['lead_id'], $current_customer_id);
						}															

						// If we are calculating age for individual member we have to use current memebers age;
						if ($is_individual_cover && $member->member_type_id == 1) {
							$ageToCalculate = $proposersAge;
						} else if ($is_individual_cover && $member->member_type_id == 2) {
							$ageToCalculate = $spouseAge;
						} else if (!$is_individual_cover) {
							$ageToCalculate = $proposersAge > $spouseAge ? $proposersAge : $spouseAge;
						}
                        $sum_insured= $this->getSumInSuredBasedOnPolicySubType($policy->policy_sub_type_id);
                     $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adultsToCalculate." and child_count=".$childrenToCalculate)->row()->deductable;
						$requestData = array(
							'is_individual_cover' => $is_individual_cover,
							'policy_id' => $policy_id,
							'sum_insured' => $sum_insured,
							'policy_sub_type_code' => $policy_sub_type_code,
							'member_type' => $member_type,
							'policy_sub_type_id' => $policy->policy_sub_type_id,
							'age' => $ageToCalculate,
							'adults_to_calculate'	=> $adultsToCalculate,
							'children_to_calculate'	=> $childrenToCalculate,
							'tenure'	=> trim($this->input->post('tenure')),
							'number_of_ci' => trim($this->input->post('numbers_of_ci')),
							'deductable'	=> $deductable,
							'hospi_cash_group_code' => $hospi_cash_group_code,
							'group_code_type' => $group_code_type
                        );
						// print_r($requestData);exit;

						$rater = Rater::make($basis_id, $requestData);
						//echo $rater->getPremium();die;
						$planName = $rater->getPlanName();
						$result['messages'][$applicantName][$planName] = $rater->getMessages();

						if ($rater->hasPremium()) {

							if ($policy->policy_sub_type_id == 6 && in_array($this->input->post('plan_id'), $ghc_logic_plans)) {

								$hospi_cash_group_code = $rater->getGroupCode();
								$group_code_type = $rater->getGroupCodeType();
							}

							$applicantPolicies[$planName] = $rater->getPremium();
							$is_individual_cover &&  $individual_adults_premium_calculated++;
							continue;
						}
					}
				}
				// exit;
				$result['data'][$applicantName]['policies'] = $applicantPolicies;
			} else {
				if (isset($earlierGeneratedPremiums[$current_customer_id])) {
					$result['data'][$applicantName]['policies'] = $earlierGeneratedPremiums[$current_customer_id];
				}
			}

			$total_premium = 0;

			if (isset($result['data'][$applicantName]['policies'])) {
				foreach ($result['data'][$applicantName]['policies'] as $name => $rate) {
					$total_premium += $rate;
				}
				$result['data'][$applicantName]['total_premium'] = number_format(round($total_premium, 2), 2, '.', '');
			}

			next($lead_details);
		}

		$net_premium = 0;

		foreach ($result['data'] as $applicant) {
			if (isset($applicant['total_premium'])) {
				$net_premium += $applicant['total_premium'];
			}
		}


		if (!$net_premium) {
			$result['status'] = false;
		} else {
			$result['data']['net_premium'] = number_format(round($net_premium, 2), 2, '.', '');
		}
//var_dump($result);exit;
		echo json_encode($result);
	}

	function calculateMemberAgeWisePremium($arguments)
	{
		extract($arguments);

		$data = [
			'messages' => [],
			'policy' => null,
		];

		$result = $this->apimodel->getPolicyMemberAgeWisePremium(
			[
				'policy_id' => $policy_id,
				'sum_insured' => $sum_insured,
				'age' => $age,
			]
		);
		// If cover is individual append the member type to the policy code
		if ($is_individual_cover) {
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		} else {
			$data['policy'][$policy_sub_type_code]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		}
		$data['messages'] = $result['messages'];

		return $data;
	}

	function calculateFamilyDeductablePremium($arguments)
	{
		extract($arguments);

		$data = [
			'messages' => [],
			'policy' => null,
		];

		if ($is_individual_cover) {
			$result = $this->apimodel->getPolicyFamilyDeductable(
				[
					'policy_id' => $policy_id,
					'sum_insured' => $sum_insured,
					'adult_count' => 1,
					'child_count' => 0,
					'deductable' => $deductable
				]
			);
		} else {

			$result = $this->apimodel->getPolicyFamilyDeductable(
				[
					'policy_id' => $policy_id,
					'sum_insured' => $sum_insured,
					'adult_count' => $adults_to_calculate,
					'child_count' => $children_to_calculate,
					'deductable' => $deductable
				]
			);
		}

		// If cover is individual append the member type to the policy code
		if ($is_individual_cover) {
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		} else {
			$data['policy'][$policy_sub_type_code]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		}
		$data['messages'] = $result['messages'];

		return $data;
	}

	function calculateFlatRatePremium($arguments)
	{
		extract($arguments);

		$data = [
			'messages' => [],
			'policy' => null,
		];

		if ($is_individual_cover) {
			$result = $this->apimodel->getPolicyPremiumFlat(['policy_id' => $policy_id, 'sum_insured' => $sum_insured]);
		} else {
			$result = $this->apimodel->getPolicyPremiumFlat(['policy_id' => $policy_id, 'sum_insured' => $sum_insured]);
		}

		// If cover is individual append the member type to the policy code
		if ($is_individual_cover) {
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		} else {
			$data['policy'][$policy_sub_type_code]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		}
		$data['messages'] = $result['messages'];

		return $data;
	}

	function calculateFamilyConstructPremium($arguments)
	{
		extract($arguments);

		$data = [
			'messages' => [],
			'policy' => null,
		];

		if ($is_individual_cover) {
			$result = $this->apimodel->getPolicyPremiumFamilyConstruct([
				'policy_id' => $policy_id,
				'sum_insured' => $sum_insured,
				'adult_count' => 1,
				'child_count' => 0
			]);
		} else {
			$result = $this->apimodel->getPolicyPremiumFamilyConstruct([
				'policy_id' => $policy_id,
				'sum_insured' => $sum_insured,
				'adult_count' => $adults_to_calculate,
				'child_count' => $children_to_calculate
			]);
		}

		// If cover is individual append the member type to the policy code
		if ($is_individual_cover) {
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		} else {
			$data['policy'][$policy_sub_type_code]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		}
		$data['messages'] = $result['messages'];

		return $data;
	}

	function calculateFamilyConstructAgeWisePremium($arguments)
	{
		extract($arguments);

		$data = [
			'messages' => [],
			'policy' => null,
		];

		if ($is_individual_cover) {
			$result = $this->apimodel->getFamilyConstructAgeWisePremium([
				'policy_id' => $policy_id,
				'age' => $age,
				'sum_insured' => $sum_insured,
				'adult_count' => 1,
				'child_count' => 0
			]);
		} else {
			$result = $this->apimodel->getFamilyConstructAgeWisePremium([
				'policy_id' => $policy_id,
				'age' => $age,
				'sum_insured' => $sum_insured,
				'adult_count' => $adults_to_calculate,
				'child_count' => $children_to_calculate
			]);
		}

		// If cover is individual append the member type to the policy code
		if ($is_individual_cover) {
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		} else {
			$data['policy'][$policy_sub_type_code]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		}
		$data['messages'] = $result['messages'];

		return $data;
	}

	function calculatePerMilePremium($arguments)
	{
		extract($arguments);

		$data = [
			'messages' => [],
			'policy' => null,
		];

		$request = [
			'policy_id' => $policy_id,
			'age' => $age,
			'sum_insured' => $sum_insured,
			'tenure' => $tenure,
		];

		if ($policy_sub_type_id == 3) {
			$request['number_of_ci'] = !empty($number_of_ci) ? $number_of_ci : 0;
		}

		$result = $this->apimodel->getPerMileWisePremium($request);
		// If cover is individual append the member type to the policy code
		if ($is_individual_cover) {
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code . "-" . $member_type]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		} else {
			$data['policy'][$policy_sub_type_code]['rate'] = $result['rate'] ?? null;
			$data['policy'][$policy_sub_type_code]['rate_without_tax'] = $result['rate_without_tax'] ?? null;
		}
		$data['messages'] = $result['messages'];

		return $data;
	}

	function getProposalMemberID()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$get_result = $this->apimodel->getProposalPolicyMemberDetails($_POST['lead_id'], $_POST['customer_id']);
			$result = [];

			foreach ($get_result as $arr) {

				$result[$arr['member_id']] = $arr;
			}
			echo json_encode($result);
		} else {

			echo json_encode([]);
		}

		exit;
	}

	function getGeneratedQuote()
	{
		$data = $this->apimodel->getGeneratedQuote($_POST);

		echo json_encode($data);
	}

	function getGeneratedPremiums()
	{
		$result['status'] = true;

		$lead_details = $this->apimodel->getLeadDetails($_POST['lead_id']);

		$plan_details = $this->apimodel->getProductDetailsAll($lead_details[0]->plan_id);

		$earlierGeneratedPremiums = $this->apimodel->getGeneratedPremiums($_POST);

		if ($lead_details[0]->is_coapplicant == "Y") {
			$total_applicants = $lead_details[0]->coapplicant_no + 1;
		} else {
			$total_applicants = 1;
		}

		$primary_customer_id = $lead_details[0]->primary_customer_id;

		for ($i = 0; $i < $total_applicants; $i++) {

			if (!current($lead_details)) {
				break;
			}

			$current_customer_id = current($lead_details)->customer_id;

			if ($current_customer_id == $primary_customer_id) {
				$applicantName = "Applicant";
			} else {
				$applicantName = "Co-Applicant" . $i;
			}

			foreach ($plan_details as $policy) {

				if (!isset($earlierGeneratedPremiums[$current_customer_id])) {
					continue;
				}

				if ($policy->sitype_id == 1) {
					$members = $this->apimodel->getMembers();

					foreach ($members as $member) {
						$policy_display_code = $policy->policy_sub_type_code . "-" . $member->member_type;
						if (isset($earlierGeneratedPremiums[$current_customer_id][$policy_display_code])) {
							$result['data'][$applicantName]['policies'][$policy_display_code] = $earlierGeneratedPremiums[$current_customer_id][$policy_display_code];
						}
					}
				} else {
					if (isset($earlierGeneratedPremiums[$current_customer_id][$policy->policy_sub_type_code])) {
						$result['data'][$applicantName]['policies'][$policy->policy_sub_type_code] = $earlierGeneratedPremiums[$current_customer_id][$policy->policy_sub_type_code];
					}
				}
			}

			$total_premium = 0;

			if (isset($result['data'][$applicantName]['policies'])) {
				foreach ($result['data'][$applicantName]['policies'] as $rate) {
					$total_premium += $rate;
				}
				$result['data'][$applicantName]['total_premium'] = number_format(round($total_premium, 2), 2, '.', '');
			}

			next($lead_details);
		}

		$net_premium = 0;

		if (isset($result['data'])) {
			foreach ($result['data'] as $applicant) {
				if (isset($applicant['total_premium'])) {
					$net_premium += $applicant['total_premium'];
				}
			}
		}

		if (!$net_premium) {
			$result['status'] = false;
			$result['data'] = [];
		} else {
			$result['data']['net_premium'] = number_format(round($net_premium, 2), 2, '.', '');
		}

		echo json_encode($result);
	}

	function getMasterQuoteIds()
	{
		$this->db->select('master_quote_id');
		$master_quotes = $this->db->get_where(
			'master_quotes',
			[
				'lead_id' => $this->input->post('lead_id'),
				'master_customer_id' => $this->input->post('customer_id'),
			]
		)->result_array();

		echo json_encode(array_column($master_quotes, 'master_quote_id'));
	}

	function getStateCityFromPincode()
	{
		$pincode = $this->input->post('pincode');
		$result = $this->db->get_where(
			'axis_postal_code',
			[
				'pincode' => trim($pincode),
			]
		)->result();

		echo json_encode($result[0]);
	}
	public function policyIssuance($data,$lead_id)
{

$api_data = $data;
if(!empty($api_data)){
$req_data['lead_id']= $lead_id;
$req_data['txt_date']= $api_data['PolicyCreationRequest']['TransactionRcvdDate'];
$req_data['txt_number']= $api_data['PolicyCreationRequest']['TransactionNumber'];
 $update_payment_status = $this->updateProposalStatus($req_data);
 $policyResponse = $update_payment_status;
            $policyResponse1=array();
 foreach ($policyResponse['PolicyCreationResponse'] as $row){
unset($row['gross_premium']);
     $policyResponse1[]=$row;
 }
            $lead_id = encrypt_decrypt_password($lead_id);
            $policyResponse1['COI_URL']='http://fyntunecreditoruat.benefitz.in/quotes/success_view/'.$lead_id;
           $sendMail= $this->sendMail($policyResponse1['COI_URL']);
            $policyResponse1['mail']=$sendMail;
 return $policyResponse1;
		
		
 

}

}

function sendMail($url){
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtppro.zoho.in';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->SMTPSecure = "tls";
        $mail->Username   = 'noreply@fyntune.com';                     //SMTP username
        $mail->Password   = 'Fyntune9001#';                               //SMTP password
       // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@fyntune.com', 'Mailer');
        $mail->addAddress('poojalote123@gmail.com', 'Pooja Lote');     //Add a recipient
        $mail->addAddress('pooja.lote@fyntune.com', 'Pooja Fyntune');     //Add a recipient
        $mail->addAddress('Kalpesh@elephant.in', 'Kalpesh');     //Add a recipient

        $body="<p>Hello,</p>
<p>Please click on below given link to get your certificate of Issuance.</p>
<p>".$url."</p>
<p>Regards,<br>
Fyntune Team.
</p>
";
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'COI URL';
        $mail->Body    = $body;
       // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
       return 'Message has been sent';
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

    public function updateProposalStatus($data = '')
    {
	if(!empty($data))
	{
		$_POST['lead_id'] = $data['lead_id'];
		$txt_date = $data['txt_date'];
		$txt_num = $data['txt_number'];
		$api = 'API';
	}
	else
	{
				$txt_date = date("Y-m-d H:i:S");
				$txt_num = $_POST['pg_response']['razorpay_payment_id'];
				$api = '';
	}
	$lead_query = $this->db->query("select l.lead_id from lead_details as l,proposal_policy as p,proposal_payment_details as ppd where l.lead_id = p.lead_id and l.lead_id = ppd.lead_id and l.lead_id = '".$_POST['lead_id']."'")->row_array();
	
	if(!empty($lead_query))
	{
		//print_R($lead_query);die;
        $this->db->query("UPDATE proposal_policy SET status = 'Payment-Done' WHERE lead_id = '" . $_POST['lead_id'] . "'");
        $this->db->query("UPDATE lead_details SET status = 'Customer-Payment-Received' WHERE lead_id = '" . $_POST['lead_id'] . "'");
        $this->db->query("UPDATE proposal_payment_details SET payment_status = 'Success', proposal_status = 'PaymentReceived' ,payment_date = '" . $txt_date . "',transaction_date = '" . $txt_date . "',transaction_number = '" . $txt_num . "', remark = 'PaymentReceived' WHERE lead_id = '" . $_POST['lead_id'] . "'");
        //echo json_encode("UPDATE lead_details SET status = 'Customer-Payment-Received' WHERE lead_id = '".$_POST['lead_id']."'");exit;
        $res = $this->saveApiProposalResponse($_POST['lead_id'],$api);
		if($api = 'API')
		{
			return $res;
		}else{
        echo json_encode($res);
		}
	}
	else{
					$response = ["StatusCode" =>400,"Status"=>"Error","Message"=>"Data Not Found","PolicyCreationResponse"=>""];
			return $response;

	}
        exit;
    }

    public function generate_coi($policy_subtype_name)
    {
        $coi_no = $policy_subtype_name . '-XL-AS-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        $proposal_id = $this->db->select('*')
            ->from('api_proposal_response')
            ->where('certificate_number', $coi_no)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {

            $this->generate_coi();
        }
        return $coi_no;
    }
	function saveApiProposalResponse($lead_id,$api)
    {
$api_status = 0;
        $policyDataArr = $this->db->get_where("proposal_policy", ["lead_id" => $lead_id])->result_array();
        foreach ($policyDataArr as $key => $policyData) {
            $policy_subtype_name = $this->db->get_where("master_policy_sub_type", ["policy_sub_type_id" => $policyData['policy_sub_type_id']])->row_array();
		//	print_R($policy_subtype_name);die;
            $customer_id = $this->db->get_where("master_customer", ["lead_id" => $lead_id])->row_array()['customer_id'];
            $CertificateNumber = $this->generate_coi($policy_subtype_name['code']);
            $startDate = date('Y-m-d');
            $EndDate = date('Y-m-d', strtotime($startDate . ' + 364 days'));


            $GrossPremium = isset($policyData['premium_amount']) ? $policyData['premium_amount'] : '';


            $lead_id = isset($policyData['lead_id']) ? $policyData['lead_id'] : '';
            $proposal_policy_id = isset($policyData['proposal_policy_id']) ? $policyData['proposal_policy_id'] : '';
            $policy_sub_type_id = isset($policyData['policy_sub_type_id']) ? $policyData['policy_sub_type_id'] : '';
            $customer_id = isset($customer_id) ? $customer_id : '';
            $master_policy_id = isset($policyData['master_policy_id']) ? $policyData['master_policy_id'] : '';
            $ProposalNumber = isset($policyData['proposal_no']) ? $policyData['proposal_no'] : '';

            $request_arr = array(
                "lead_id" => $lead_id,
                "certificate_number" => $CertificateNumber,
                "proposal_no" => $ProposalNumber,
                "policy_sub_type_id" => $policy_sub_type_id,
                "master_policy_id" => $master_policy_id,
                "gross_premium" => $GrossPremium,
                //"status" => $statusRes,
                "start_date" => date('Y-m-d H:i:s', strtotime($startDate)),
                "end_date" => date('Y-m-d H:i:s', strtotime($EndDate)),
                "created_date" => date('Y-m-d H:i:s'),
                "proposal_policy_id" => $proposal_policy_id,
                "customer_id" => $customer_id,
            );

            // echo json_encode($request_arr);exit;

            $apiProposalResponse = $this->db->query("SELECT pr_api_id FROM api_proposal_response 
            WHERE lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
            AND customer_id='$customer_id' AND master_policy_id='$master_policy_id'
            AND policy_sub_type_id='$policy_sub_type_id'")->row_array();


            if ($apiProposalResponse > 0) {
				$api_status = 1;
              /*  $this->db->where("lead_id", $lead_id);
                $this->db->where("proposal_policy_id", $proposal_policy_id);
                $this->db->where("customer_id", $customer_id);
                $this->db->where("master_policy_id", $master_policy_id);
                $this->db->where("policy_sub_type_id", $policy_sub_type_id);
                $this->db->update("api_proposal_response", $request_arr);
                $insert_id = $apiProposalResponse['pr_api_id'];*/

            } else {
				$api_status = 0;
                //echo json_encode('1111');exit;
                $this->db->insert("api_proposal_response", $request_arr);

                $insert_id = $this->db->insert_id();
            }
			$policyIssuanceResponse[] =['plan_name' => $policy_subtype_name['policy_sub_type_name'],'certificate_number'=>$CertificateNumber,'gross_premium'=>$GrossPremium,'policy_start_date'=> date('Y-m-d H:i:s', strtotime($startDate)),'policy_expiry_date'=>date('Y-m-d H:i:s', strtotime($startDate))];
        }
if($api = 'API'){
	if($api_status == 0)
	{
				$response = ["StatusCode" =>200,"Status"=>"Success","Message"=>"Success","PolicyCreationResponse"=>$policyIssuanceResponse];
	}else{
						$response = ["StatusCode" =>301,"Status"=>"Error","Message"=>"Policy Already Generated","PolicyCreationResponse"=>$policyIssuanceResponse];

	}

	        return $response;

	
}
else{
	return $CertificateNumber;
}
        //$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Full-Quote-Done");
    }
function addLeadapi($data)
	{
		$api_data = $data;
if(!empty($api_data))
{
	$_POST = $api_data['ClientCreation'];
	$partner = $api_data['ClientCreation']['partner'];
	$_POST['utoken'] = $api_data['token'];
	$plan = $api_data['ClientCreation']['plan'];
	$creditor_id= $this->db->query("select creditor_id from master_ceditors where creaditor_name = '$partner' ")->row_array();
	if(empty($creditor_id))
	{
		return json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Partner Name does not exist'), "Data" => ""));
				
	}
	$_POST['creditor_id'] = $creditor_id['creditor_id'];
	$plan_id = $this->db->query('select plan_id from master_plan where creditor_id = '.$_POST['creditor_id']." and plan_name = '$plan'")->row_array();
	if(empty($plan_id))
	{
		return json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Plan does not exist'), "Data" => ""));
				
	}
	$mobile_no = $_POST['ClientCreation']['mobile_number'];
	/*$lead_det_check = $this->db->query("select lead_id from lead_details where mobile_no = '$mobile_no'")->row_array();
	if(!empty($lead_det_check))
	{
		echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Lead already created'), "Data" => ""));
				exit;
	}*/
	$_POST['plan_id'] = $plan_id['plan_id'];
	
	$emp_id = $api_data['ClientCreation']['userId'];
	$_POST['sm_id'] = $api_data['ClientCreation']['userId'];
	$_POST['login_user_id'] = $api_data['ClientCreation']['employee_id'];
	$sm_location = $api_data['ClientCreation']['sm_location'];
	$sm_loc_id = $this->db->query('select ml.location_id from master_location as ml,user_locations as ul where ml.location_id = ul.location_id and ul.user_id = '.$api_data['ClientCreation']['userId']." and ml.location_name = '$sm_location'")->row_array();
	$_POST['lead_location_id'] = $sm_loc_id['location_id'];

	
}else
{
	$_POST = $_POST;
}
$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

				$cust_data = array();
				$cust_data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
				$cust_data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
				$cust_data['middle_name'] = (!empty($_POST['middle_name'])) ? $_POST['middle_name'] : '';
				$cust_data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
				$full_name = '';
				if (!empty($_POST['first_name'])) {
					$full_name .= $_POST['first_name'];
				}
				if (!empty($_POST['middle_name'])) {
					$full_name .= " " . $_POST['middle_name'];
				}
				if (!empty($_POST['last_name'])) {
					$full_name .= " " . $_POST['last_name'];
				}

				$cust_data['full_name'] = $full_name;
				$cust_data['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : '';
				$cust_data['dob'] = (!empty($_POST['dob'])) ? date("Y-m-d", strtotime($_POST['dob'])) : '';
				$cust_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
				$cust_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$cust_data['isactive'] = 1;
				$cust_data['createdon'] = date("Y-m-d H:i:s");
				$cust_data['createdby'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				//$cust_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
//echo"<pre>";print_r($cust_data);die;
				$customer_id = $this->apimodel->insertData('master_customer', $cust_data, 1);
				
				//Create Lead
				$lead_data = array();
				$timestamp = time();
				$lead_data['trace_id'] = $customer_id . $timestamp;
				$lead_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$lead_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
				$lead_data['sales_manager_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$lead_data['primary_customer_id'] = $customer_id;
				$lead_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$lead_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';

				$lead_data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
				$lead_data['portal_id'] = (!empty($_POST['portal_id'])) ? $_POST['portal_id'] : 'Creditor Portal';
				$lead_data['vertical'] = (!empty($_POST['vertical'])) ? $_POST['vertical'] : 'Vertical';
				$lead_data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
				$lead_data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
				$lead_data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : 'N';
				$lead_data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
				$lead_data['lead_location_id'] = (!empty($_POST['lead_location_id'])) ? $_POST['lead_location_id'] : 0;

				$lead_data['createdon'] = date("Y-m-d H:i:s");
				$lead_data['createdby'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				//$lead_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';

//echo"<pre>";print_r($lead_data);die;

				$result = $this->apimodel->insertData('lead_details', $lead_data, 1);
				
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");

				$lead_id = $result;

           
				$this->apimodel->updateRecord('master_customer', [
					'lead_id' => $lead_id
				], "customer_id =" . $customer_id);
				
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");
				
				//Add proposal
				$proposal_data = array();
				$proposal_data['trace_id'] = $lead_data['trace_id'];
				$proposal_data['lead_id'] = $result;
				$proposal_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$proposal_data['customer_id'] = $customer_id;
				$proposal_data['status'] = 'Pending';

				$proposal_data['created_at'] = date("Y-m-d H:i:s");
				$proposal_data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$proposal_data['updated_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
//echo"<pre>";print_r($proposal_data);die;

				$proposal_details_id = $this->apimodel->insertData('proposal_details', $proposal_data, 1);

				//insert_proposal_log($proposal_details_id, $_POST['login_user_id'], $remark);
				
				
				//log entries
				$lead_id = $result;
				$created_on = date("Y-m-d H:i:s");
				$created_by = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				//customer
				$customer_action = "create_customer";
				$customer_request_data = json_encode($cust_data);
				$customer_response_data = json_encode(array("response"=>"Customer added."));
				
				//Lead
				$lead_action = "create_lead";
				$lead_request_data = json_encode($lead_data);
				$lead_response_data = json_encode(array("response"=>"Lead added."));
				
				//Proposal
				$proposal_action = "create_proposal_entry";
				$proposal_request_data = json_encode($proposal_data);
				$proposal_response_data = json_encode(array("response"=>"Proposal entery added."));
				
				if(!empty($customer_id)){
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in customer insert."));
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}
				
				if(!empty($result)){
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}
				
				if(!empty($proposal_details_id)){
					$proposallog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}

			//}

 if(!empty($api_data)) {

                $lead_det = $this->db->query("select mc.customer_id,ld.plan_id,ld.trace_id,ld.tenure from lead_details as ld,master_customer as mc where  ld.lead_id = mc.lead_id and ld.lead_id = '$lead_id' ")->row_array();

                $data['customer_id'] = $lead_det['customer_id'];
                $data['plan_id'] = $lead_det['plan_id'];
                $data['trace_id'] = $lead_det['trace_id'];
                $data['tenure'] = $lead_det['tenure'];
                $data['lead_id'] = $lead_id;
                $SumInsuredData=$api_data['QuoteRequest']['SumInsuredData'];
             // print_r($SumInsuredData);die;
                $adult_cnt =$api_data['QuoteRequest']['adult_count'];
                $child_cnt =$api_data['QuoteRequest']['child_count'];
                $family_members_ac_count = $adult_cnt . "-" . $child_cnt;
               // $data['family_members_ac_count'] = $family_members_ac_count;
				//print_r($family_members_ac_count);die;
				$data_quote['plan_id'] = $data['plan_id'];
				$data_quote['lead_id'] = $result;
				$data_quote['family_members_ac_count'] = $family_members_ac_count;
				$data_quote['customer_id'] = $customer_id;
				$data_quote['SumInsuredData'] = $SumInsuredData;
				$data_quote['tenure'] = $api_data['ClientCreation']['tenure'];
				$data_quote['trace_id'] = $lead_data['trace_id'];
				$get_premium=    $this->getPremiumApi($data_quote);

               // $result_premium = json_decode(curlFunction(SERVICE_URL . '/api2/generateQuote', $_POST));
//print_r($get_premium);exit;
            }

			if (!empty($result)) {
				return json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Lead created successfully.'), "LeadId" => $result,"Quote"=>$get_premium));
				;
			} else {
				return json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				
			}
	} else {
			return $checkToken;
		}
	}
	function getPremiumApi($data)
	{

$_POST = $data;
		$result['status'] = true;
		$result['messages'] = [];
		$hospi_cash_group_code = $group_code_type = '';
		$ghc_logic_plans = [407, 408, 409, 410, 411];


		$plan_details = $this->apimodel->getProductDetailsAll($this->input->post('plan_id'), 'mp.policy_sub_type_id', 'DESC');
//print_R($plan_details);
		$family_construct_raw = $this->input->post('family_members_ac_count');


		if (!$family_construct_raw) {
			$result['status'] = false;
			echo json_encode($result);
			exit;
		}
		$family_construct = explode('-', $family_construct_raw);
		$adultsToCalculate = $family_construct[0];
		$childrenToCalculate = $family_construct[1];
		$earlierGeneratedPremiums = $this->apimodel->getGeneratedPremiums($_POST);
		//print_R($earlierGeneratedPremiums);
		$lead_details = $this->apimodel->getLeadDetails($_POST['lead_id']);


		$spouse_dob = $this->input->post('spouse_dob');

		$spouseAge = $this->input->post('spouse_age');

		if (empty($spouseAge) && !empty($spouse_dob)) {
			$spouseAge = date_diff(date_create($spouse_dob), date_create('today'))->y;
		}

		if ($adultsToCalculate == 1 && $spouseAge) {
			$member_to_calculate = 2;
		} else {
			$member_to_calculate = 1;
		}

		// Calculate total applicants
		if ($lead_details[0]->is_coapplicant == "Y") {
			$total_applicants = $lead_details[0]->coapplicant_no + 1;
		} else {
			$total_applicants = 1;
		}

		// Loop through All Applicants and Co-Applicants
		for ($i = 0; $i < $total_applicants; $i++) {

			$applicantPolicies = [];

			// If there is no applicant details available for the current lead we will break 
			if (!current($lead_details)) {
				break;
			}

			$current_customer_id = current($lead_details)->customer_id;

			if ($i == 0) {
				$applicantName = "Applicant";
			} else {
				$applicantName = "Co-Applicant" . $i;
			}

			if ($current_customer_id == $_POST['customer_id']) {
				// Loop through all policies in the plan and generate premiums
				// exit('12345');
				//print_R($plan_details);
				foreach ($plan_details as $policy) {
					
					$policy_id = $policy->policy_id;
					$policy_sub_type_code = $policy->policy_sub_type_code;
					$basis_id = $policy->basis_id;

					$members = $this->apimodel->getPolicyFamilyDetails($policy_id);

					$is_individual_cover = null;

					// Store the total children and adults allowed in the policy
					$child_count = 0;
					$adult_count = 0;

					foreach ($members as $key => $construct) {
						$adult_member_type_id = array(1, 2, 3, 4);
						if (in_array($members[$key]->member_type_id, $adult_member_type_id)) {
							$adult_count++;
						} else {
							$child_count++;
						}
					}


					if ($policy->sitype_id != 1) {
						$is_individual_cover = false;
					} else {
						$is_individual_cover = true;
					}

					// Reduce array in case of family cover and single adult
					if ($policy->sitype_id != 1 || $adultsToCalculate == 1) {
						$members = array_filter($members, function ($construct) use ($member_to_calculate) {
							return $construct->member_type_id == $member_to_calculate;
						});
					}
					$individual_adults_premium_calculated = 0;
					foreach ($members as $member) {

						$member_type = $member->member_type;
						// For Individual type if we have already calculated for required adults we will skip
						if ($individual_adults_premium_calculated == $adultsToCalculate) {
							break;
						}

						if(isset($_POST['source']) && strcmp($_POST['source'],"customer")){
							$proposersAge=$_POST['max_age'];
						}else{
							$proposersAge = $this->apimodel->getProposersAge($_POST['lead_id'], $current_customer_id);
						}															
						// If we are calculating age for individual member we have to use current memebers age;
						if ($is_individual_cover && $member->member_type_id == 1) {
							$ageToCalculate = $proposersAge;
						} else if ($is_individual_cover && $member->member_type_id == 2) {
							$ageToCalculate = $spouseAge;
						} else if (!$is_individual_cover) {
							$ageToCalculate = $proposersAge > $spouseAge ? $proposersAge : $spouseAge;
						}
$SumInsuredData = $_POST['SumInsuredData'];
  foreach ($SumInsuredData as $sumD){
                    if($sumD['PlanCode'] == ''){
                        $response = array('success' => false, 'msg' => "PlanCode Required!");
                        echo json_encode($response);
                    }
                    $policy_sub_type_id=$this->db->query('select policy_sub_type_id from master_policy_sub_type where plan_code='.$sumD['PlanCode'])->row()->policy_sub_type_id;
                    if(empty($policy_sub_type_id) || is_null($policy_sub_type_id)){
                        $response = array('success' => false, 'msg' => $sumD['PlanCode']." PlanCode Not Found!");
                        echo json_encode($response);
                    }
                    if($policy_sub_type_id == 1){
                        $data['ghi_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 2){
                        $data['pa_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 3){
                        $data['ci_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 5){
                        $data['super_top_up_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 6){
                        $data['hospi_cash'] = $sumD['SumInsured'];
                    }
                }
						$requestData = [
							'is_individual_cover' => $is_individual_cover,
							'policy_id' => $policy_id,
							'sum_insured' => $this->getSumInSuredBasedOnPolicySubType($policy->policy_sub_type_id,$policy_id,0,0,$data),
							'policy_sub_type_code' => $policy_sub_type_code,
							'member_type' => $member_type,
							'policy_sub_type_id' => $policy->policy_sub_type_id,
							'age' => $ageToCalculate,
							'adults_to_calculate'	=> $adultsToCalculate,
							'children_to_calculate'	=> $childrenToCalculate,
							'tenure'	=> trim($_POST['tenure']),
							'number_of_ci' => trim($this->input->post('numbers_of_ci')),
							'deductable'	=> trim($this->input->post('deductable')),
							'hospi_cash_group_code' => $hospi_cash_group_code,
							'group_code_type' => $group_code_type
						];
						// print_r($requestData);exit;
						// print_r($requestData);exit;

						$rater = Rater::make($basis_id, $requestData);
//print_R($rater);die;
						$planName = $rater->getPlanName();
						$result['messages'][$applicantName][$planName] = $rater->getMessages();

						if ($rater->hasPremium()) {

							if ($policy->policy_sub_type_id == 6 && in_array($this->input->post('plan_id'), $ghc_logic_plans)) {

								$hospi_cash_group_code = $rater->getGroupCode();
								$group_code_type = $rater->getGroupCodeType();
							}

							$applicantPolicies[$planName] = $rater->getPremium();
							$is_individual_cover &&  $individual_adults_premium_calculated++;
							continue;
						}
					}
				}
				// exit;
				$result['data'][$applicantName]['policies'] = $applicantPolicies;
			} else {
				if (isset($earlierGeneratedPremiums[$current_customer_id])) {
					$result['data'][$applicantName]['policies'] = $earlierGeneratedPremiums[$current_customer_id];
				}
			}

			$total_premium = 0;

			if (isset($result['data'][$applicantName]['policies'])) {
				foreach ($result['data'][$applicantName]['policies'] as $name => $rate) {
					$total_premium += $rate;
				}
				$result['data'][$applicantName]['total_premium'] = number_format(round($total_premium, 2), 2, '.', '');
			}

			next($lead_details);
		}

		$net_premium = 0;

		foreach ($result['data'] as $applicant) {
			if (isset($applicant['total_premium'])) {
				$net_premium += $applicant['total_premium'];
			}
		}


		if (!$net_premium) {
			$result['status'] = false;
		} else {
			$result['data']['net_premium'] = number_format(round($net_premium, 2), 2, '.', '');
		}

		return $result;
	}
    function saveProposalapi()
    {
       // echo 123;exit;
        $api_data = json_decode(file_get_contents('php://input') , true);
//	print_r($api_data['ClientCreation']);die;
//	$_POST = $api_data;
$_POST = $api_data;
       
        $add_lead = $this->addLeadapi($_POST);
$addLead_det =  json_decode($add_lead);
$lead_id = $addLead_det->LeadId;
if($addLead_det->status_code == 400)
{
	echo $add_lead;exit;
}
        $lead_det= $this->db->query("select mc.customer_id,ld.plan_id,ld.trace_id,ld.tenure from lead_details as ld,master_customer as mc where  ld.lead_id = mc.lead_id and ld.lead_id = '$lead_id' ")->row_array();

        $data['customer_id'] = $lead_det['customer_id'];
        $data['plan_id'] = $lead_det['plan_id'];
        $data['trace_id'] = $lead_det['trace_id'];
        $data['tenure'] = $lead_det['tenure'];
        $data['lead_id'] = $lead_id;
        $data['is_api'] = 1;
        $SumInsuredData=$api_data['QuoteRequest']['SumInsuredData'];
        $token=$api_data['token'];
        $emp_id="";
         $checkToken=$this->db->query("select emp_id  from token_table t where token= '".$token."'")->row()->emp_id;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $emp_id=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken."'")->row()->employee_id;
        }
        if($emp_id == ""){
            $response = array('success' => false, 'msg' => "Employee Id Not Found!");
            echo json_encode($response);
            exit;
        }
        $data['user_id']=$emp_id;
        foreach ($SumInsuredData as $sumD){
            if($sumD['PlanCode'] == ''){
                $response = array('success' => false, 'msg' => "PlanCode Required!");
                echo json_encode($response);
            }
            $policy_sub_type_id=$this->db->query('select policy_sub_type_id from master_policy_sub_type where plan_code='.$sumD['PlanCode'])->row()->policy_sub_type_id;
            if(empty($policy_sub_type_id) || is_null($policy_sub_type_id)){
                $response = array('success' => false, 'msg' => $sumD['PlanCode']." PlanCode Not Found!");
                echo json_encode($response);
            }
            if($policy_sub_type_id == 1){
                $data['ghi_cover'] = $sumD['SumInsured'];
            }else if($policy_sub_type_id == 2){
                $data['pa_cover'] = $sumD['SumInsured'];
            }else if($policy_sub_type_id == 3){
                $data['ci_cover'] = $sumD['SumInsured'];
            }else if($policy_sub_type_id == 5){
                $data['super_top_up_cover'] = $sumD['SumInsured'];
            }else if($policy_sub_type_id == 6){
                $data['hospi_cash'] = $sumD['SumInsured'];
            }
        }

        $family_construct = explode("+",$api_data['QuoteRequest']['family_construct']);
        if(empty($family_construct) || is_null($family_construct)){
            $response = array('success' => false, 'msg' => "Family Construct Required!");
            echo json_encode($response);
        }
       // preg_match_all('!\d+\.*\d*!', $family_construct[0], $matches);
        $adult_cnt = $api_data['QuoteRequest']['adult_count'];
       // preg_match_all('!\d+\.*\d*!', $family_construct[1], $matche1);
        $child_cnt =  $api_data['QuoteRequest']['child_count'];
        $family_members_ac_count = $adult_cnt."-".$child_cnt;
        $data['family_members_ac_count'] = $family_members_ac_count;
        //var_dump($data);exit;

     //   $result = json_decode(curlFunction(SERVICE_URL . 'api2/saveGeneratedQuote', $data));
        $result=  $this->saveGeneratedQuote($data);

        $response = [];

        if (isset($result['status']) && $result['status']) {

            $lead_detNew = $this->db->query("select proposal_details_id from proposal_details where lead_id = '$lead_id' ")->row_array();

            if (isset($result['data']['quote_ids']) && !empty($result['data']['quote_ids'])) {

                $response = array('success' => true, 'msg' => "Quote Generated", "data" => $result->data);
                $Member = $api_data['MemObj']['Member'];
                if(count($Member) <= 0){
                    $response = array('success' => false, 'msg' => "Members Not Found!");
                    echo json_encode($response);
                }
                $mem_resp=array();
                foreach ($Member as $mem) {
                    $mem_data = array();
                    $mem_data['member_salutation'] = $mem['Salutation'];
                    $mem_data['first_name'] = $mem['First_Name'];
                    $mem_data['last_name'] = $mem['Last_Name'];
                    $mem_data['gender'] = $mem['Gender'];
                    $mem_data['insured_member_dob'] = $mem['DateOfBirth'];
                    $mem_data['customer_id'] = $lead_det['customer_id'];
                    $mem_data['plan_id'] = $lead_det['plan_id'];
                    $mem_data['trace_id'] = $lead_det['trace_id'];
                    $mem_data['created_by'] = $emp_id;
                    $mem_data['lead_id'] = $lead_id;
                    $mem_data['proposal_id'] = $lead_detNew['proposal_details_id'];
                    $mem_data['relation_with_proposal'] = $mem['Relation_Code'];
                    $mem_data['is_api'] = 1;
                    $mem_data['user_id']=$emp_id;
                  $resss=  $this->proposalInsuredMemberSubmit($mem_data);
                    $mem_resp[]=$resss;
                //  var_dump($resss);
                }
                $response['mem_resp']=$mem_resp;


                $data['proposal_details_id'] = $lead_detNew['proposal_details_id'];
                $nomineeDetails = $api_data['Nominee_Detail'];
            
                $data['nominee_email'] = $nomineeDetails['Nominee_Email'];
                $data['nominee_contact'] = $nomineeDetails['Nominee_Contact_Number'];
                $data['nominee_gender'] = $nomineeDetails['Nominee_gender'];
                $data['nominee_salutation'] = $nomineeDetails['Nominee_Salutation'];
                $data['nominee_last_name'] = $nomineeDetails['Nominee_Last_Name'];
                $data['nominee_first_name'] = $nomineeDetails['Nominee_First_Name'];
                $data['nominee_relation'] = $nomineeDetails['Nominee_Relationship_Code'];
                $data['created_by'] = $emp_id;
                $data['user_id'] = $emp_id;
                $addEdit = $this->addEditPolicyProposalNomineeDetails($data);
                $response['Nominee']=$addEdit;
               // var_dump($addEdit);
                //Payment Details

                $Paymentdata['lead_id'] = $lead_id;
                $Paymentdata['plan_id'] = $lead_det['plan_id'];
                $Paymentdata['trace_id'] = $lead_det['trace_id'];
                $Paymentdata['proposal_id'] = $lead_detNew['proposal_details_id'];
                $Paymentdata['go_green'] = isset($_POST['go-green']) ? $_POST['go-green'] : '';
                $Paymentdata['created_by'] = $emp_id;
                $Paymentdata['is_api'] = 1;
                $ReceiptCreation = $api_data['ReceiptCreation'];
             //   var_dump($ReceiptCreation);
                $Paymentdata['mode_of_payment'] = (!empty($ReceiptCreation['PaymentMode'])) ? strtolower($ReceiptCreation['PaymentMode']) : '';
                $paymentData=$this->proposalFinalSubmit($Paymentdata);
                $response['Payment']=$paymentData;
				$response['Premium'] = $addLead_det->Quote;
				$response['LeadId'] = encrypt_decrypt_password($lead_id);
                $msg = '';

                if (isset($result->policy_errors) && !empty($result->policy_errors)) {

                    foreach ($result->policy_errors as $key => $value) {

                        if (!empty($value) && $value[0] != '' && $value[0] != 'Invalid sum_insured') {

                            $msg .= $key . " : " . $value[0] . "<br> ";
                        }
                    }
                }
if(empty($result->policy_errors))
{$data = $api_data;
	$res = $this->policyIssuance($data,$lead_id);
	                    $response['policyIssuance'] = $res;

}
                if (!empty($response)) {

                    $response['policy_errors'] = $msg;
                } else {

                    $response = array('success' => false, 'msg' => $result->messages, "data" => $result->data, "policy_errors" => $msg);
                }

                echo json_encode($response);

            } else {
                if (isset($result->data)) {
                    echo json_encode(array('success' => false, 'msg' => $result->messages, "data" => $result->data,"Premium"=>json_encode($addLead_det->Quote)));
                } else {
                    echo json_encode(array('success' => false, 'msg' => $result->messages, "data" => ""));
                }
            }
        }
    }
    function saveNomineeapi(){
        $api_data = json_decode(file_get_contents('php://input') , true);
//	print_r($api_data['ClientCreation']);die;
        $lead_id = $api_data['ClientCreation']['LeadId'];
//	print_R($api_data['ClientCreation']);die;
        //$child_cnt = 0;
        $lead_det= $this->db->query("select mc.customer_id,ld.plan_id,ld.trace_id,ld.tenure,pd.proposal_details_id from lead_details as ld,master_customer as mc,proposal_details as pd where  ld.lead_id = mc.lead_id and ld.lead_id = pd.lead_id and ld.lead_id = '$lead_id' ")->row_array();
        //var_dump($lead_det);exit;
        $data['customer_id'] = $lead_det['customer_id'];
        $data['plan_id'] = $lead_det['plan_id'];
        $data['trace_id'] = $lead_det['trace_id'];
        $data['proposal_details_id'] = $lead_det['proposal_details_id'];
        $nomineeDetails=$api_data['NomineeDetail'];
        $data['nominee_email']=$nomineeDetails['nominee_email'];
        $data['nominee_contact']=$nomineeDetails['nominee_contact'];
        $data['nominee_gender']=$nomineeDetails['nominee_gender'];
        $data['nominee_salutation']=$nomineeDetails['nominee_salutation'];
        $data['nominee_last_name']=$nomineeDetails['nominee_last_name'];
        $data['nominee_first_name']=$nomineeDetails['nominee_first_name'];
        $data['nominee_relation']=$nomineeDetails['nominee_relation'];
        $data['user_id'] = 1234;
        if (!empty($data['lead_id'])) {
            $addEdit = $this->addEditPolicyProposalNomineeDetails($data);
            // echo "<pre>";print_r($addEdit);exit;

            $addEdit = json_decode($addEdit, true);

            if ($addEdit['status_code'] == '200') {
                echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message']));
                exit;
            } else {
                echo json_encode(array('success' => false, 'msg' => $addEdit['Metadata']['Message']));
                exit;
            }
        } else {
            echo json_encode(array('success' => false, 'msg' => "Something went wrong try after some time"));
            exit;
        }
    }

	function saveGeneratedQuote($data = null)
	{

	    if($data != null){
	        $_POST=$data;
	    }
		$result['status'] = true;
		$result['messages'] = [];
		$result['data'] = [];
		$ghc_logic_plans = [407, 408, 409, 410, 411];
		$hospi_cash_group_code = $group_code_type = '';
		$has_hospi_cash = false;

		$lead_id = trim($this->input->post('lead_id'));
		$trace_id = trim($this->input->post('trace_id'));
		$customer_id = trim($this->input->post('customer_id'));
		$plan_id = trim($this->input->post('plan_id'));
		$user_id = $this->input->post('user_id');
		$is_api = $this->input->post('is_api');
		$emp_id = $this->input->post('emp_id');
		$spouse_dob = $spouseAge = '';

		if(isset($_POST['source']) && $_POST['source'] == 'customer'){
			
			$lead_id = encrypt_decrypt_password($lead_id, 'D');
			$trace_id = encrypt_decrypt_password($trace_id, 'D');
			$customer_id = encrypt_decrypt_password($customer_id, 'D');
			$plan_id = encrypt_decrypt_password($plan_id, 'D');
			$user_id = 0;
		}

		$spouse_dob = trim($this->input->post('spouse_dob')) ?? '';
		$this->apimodel->deleteEarlierQuotes($lead_id, $customer_id);
        //echo $this->db->last_query(); exit;

//echo $plan_id;exit;
		$plan_details = $this->apimodel->getProductDetailsAll($plan_id, 'mp.policy_sub_type_id', 'DESC');

		//echo $this->db->last_query(); die;
   //    var_dump($plan_details);exit;
		$family_construct_raw = trim($this->input->post('family_members_ac_count'));

		if (!$family_construct_raw) {
			$result['messages'][] = 'Please select Family Construct';
			$result['status'] = false;
			echo json_encode($result);
			exit;
		}

		if ($family_construct_raw != '1-0') {

			$spouseAge = trim($this->input->post('spouse_age'));

			if (isset($this->input->post()['spouse_dob']) && empty($spouse_dob)) {
				$result['messages'][] = 'Please select Spouse DOB';
				$result['status'] = false;
				echo json_encode($result);
				exit;
			}
		}

		 $product_type_id = $plan_details[0]->product_type_id;
		// If Bundled Product Type
		if ($product_type_id == 1 || $product_type_id == 3) {
			if (empty(trim($this->input->post('sum_insured2'))) && empty(trim($this->input->post('sum_insured3')))) {
				$result['messages'][] = 'Either GPA or GCI is mandatory';
				$result['status'] = false;
				echo json_encode($result);
				exit;
			}
		}

		$family_construct = explode('-', $family_construct_raw);

		$adultsToCalculate = $family_construct[0];
		$childrenToCalculate = $family_construct[1];

		$applicantPolicies = [];

		if (empty($spouseAge) && !empty($spouse_dob)) {
			$spouseAge = date_diff(date_create($spouse_dob), date_create('today'))->y;
		}

		$tenure = $this->input->post('tenure');
		$family_construct_string = $this->input->post('family_members_ac_count');

		if ($adultsToCalculate == 1 && $spouseAge) {
			$member_to_calculate = 2;
		} else {
			$member_to_calculate = 1;
		}

		$commonInsertions = [
			'lead_id' => $lead_id,
			'trace_id' => $trace_id,
			'master_customer_id' => $customer_id,
			'spouse_age' => $spouseAge,
			'tenure' => $tenure,
			'family_construct' => $family_construct_string,
		];

		$policy_ids = $policy_sub_type_code_arr = [];
		foreach ($plan_details as $policy) {

			$policy_ids[$policy->policy_id] = $policy->policy_sub_type_id;
			$policy_sub_type_code_arr[$policy->policy_sub_type_id] = $policy->policy_sub_type_code;
		}

		$mandatory_if_not_selected_arr = $this->apimodel->getdata('master_policy_mandatory_if_not_selected_rules', 'master_policy_id, dependent_on_policy_id', 'master_policy_id IN (' . implode(',', array_keys($policy_ids)) . ')');
        //echo $this->db->last_query();exit;
		$mandatory_if_not_selected = [];

		if (!empty($mandatory_if_not_selected_arr)) {

			foreach ($mandatory_if_not_selected_arr as $value) {

				$mandatory_if_not_selected[$value['master_policy_id']] = $policy_ids[$value['dependent_on_policy_id']] ?? '';
			}
		}
	//	var_dump($mandatory_if_not_selected);exit;

	//	var_dump($plan_details);exit;

		// Loop through all policies in the plan and generate premiums
		foreach ($plan_details as $policy) {
			$policy_id = $policy->policy_id;
			$policy_sub_type_id = $policy->policy_sub_type_id;
			$policy_sub_type_code = $policy->policy_sub_type_code;
			$basis_id = $policy->basis_id;

			$members = $this->apimodel->getPolicyFamilyDetails($policy_id);
			$is_individual_cover = null;

			// Store the total children and adults allowed in the policy
			$child_count = 0;
			$adult_count = 0;

			foreach ($members as $key => $construct) {
				$adult_member_type_id = array(1, 2, 3, 4);
				if (in_array($members[$key]->member_type_id, $adult_member_type_id)) {
					$adult_count++;
				} else {
					$child_count++;
				}
			}

			if ($policy->sitype_id != 1) {
				$is_individual_cover = false;
			} else {
				$is_individual_cover = true;
			}

			// Reduce array in case of family cover and single adult
			if ($policy->sitype_id != 1 || $adultsToCalculate == 1) {
				$members = array_filter($members, function ($construct) use ($member_to_calculate) {
					return $construct->member_type_id == $member_to_calculate;
				});
			}

			$individual_adults_premium_calculated = 0;

			foreach ($members as $member) {

				$member_type = $member->member_type;

				// For Individual type if we have already calculated for required adults we will skip
				if ($individual_adults_premium_calculated == $adultsToCalculate) {
					break;
				}

				$proposersAge = $this->apimodel->getProposersAge($lead_id, $customer_id);

				// If we are calculating age for individual member we have to use current memebers age;
				if ($is_individual_cover && $member->member_type_id == 1) {
					$ageToCalculate = $proposersAge;
				} else if ($is_individual_cover && $member->member_type_id == 2) {
					$ageToCalculate = $spouseAge;
				} else if (!$is_individual_cover) {
					$ageToCalculate = $proposersAge > $spouseAge ? $proposersAge : $spouseAge;
				}
                $sum_insured= $this->getSumInSuredBasedOnPolicySubType($policy->policy_sub_type_id);
                $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adultsToCalculate." and child_count=".$childrenToCalculate)->row()->deductable;
				$requestData = array(
					'is_individual_cover' => $is_individual_cover,
					'policy_id' => $policy_id,
					'sum_insured' => $sum_insured,
					'policy_sub_type_code' => $policy_sub_type_code,
					'member_type' => $member_type,
					'policy_sub_type_id' => $policy_sub_type_id,
					'age' => $ageToCalculate,
					'adults_to_calculate'	=> $adultsToCalculate,
					'children_to_calculate'	=> $childrenToCalculate,
					'tenure'	=> trim($this->input->post('tenure')),
					'number_of_ci' => trim($this->input->post('numbers_of_ci')),
					'deductable'	=> $deductable,
					'hospi_cash_group_code' => $hospi_cash_group_code,
					'group_code_type' => $group_code_type
                );


				if (!$policy->is_optional && empty($requestData['sum_insured'])) {
					$result['messages'][] = $policy_sub_type_code . " Is Mandatory";
					$result['status'] = false;
					echo json_encode($result);
					exit;
				}
				/*else if ($policy->is_optional && empty($requestData['sum_insured'])) {
					$result['messages'][] = $policy_sub_type_code . " Is Mandatory";
					$result['status'] = false;
					echo json_encode($result);
					exit;
				}*/

				$rater = Rater::make($basis_id, $requestData);
              //  print_r($rater);die;
				$plan_name = $rater->getPlanName();

				//$result['messages'][$plan_name] = $rater->getMessages();
				$result['policy_errors'][$plan_name] = $rater->getMessages();

				$policy_data = [
					'master_policy_id' => $policy->policy_id,
					'member_type_id' => $member->member_type_id,
					'premium_with_tax' => $rater->getPremium(),
					'sum_insured' => $requestData['sum_insured'] ?? null,
					'premium' => $rater->getPremiumWithoutTax(),
					'age' => $ageToCalculate,
					'is_age_dependent' => ($basis_id == 3 || $basis_id == 4 || $basis_id == 5) ? '1' : '0',
					'spouse_dob' => (!empty($spouse_dob) ? date('Y-m-d', strtotime($spouse_dob)) : '')
				];

				if ($basis_id == 6) {
					$policy_data['deductable'] = $requestData['deductable'];
				}

				if ($policy_sub_type_id == 3) {
					$policy_data['number_of_ci'] = $requestData['number_of_ci'];
				}

				if ($rater->hasPremium()) {
					if ($policy->policy_sub_type_id == 6 && in_array($plan_id, $ghc_logic_plans)) {

						$hospi_cash_group_code = $rater->getGroupCode();
						$group_code_type = $rater->getGroupCodeType();

						$policy_data['group_code'] = $hospi_cash_group_code;

						$has_hospi_cash = true;
					} else {

						if ($has_hospi_cash) {

							if ($policy->policy_sub_type_id == 2 || $policy->policy_sub_type_id == 3) {

								$hospi_cash_group_code = $rater->getGroupCode();
								$group_code_type = $rater->getGroupCodeType();

								$policy_data['group_code'] = $hospi_cash_group_code;
							} else {

								$policy_data['group_code'] = $rater->getGroupCode();
								$group_code_type = $rater->getGroupCodeType();
								/*$hospi_cash_group_code = $rater->getGroupCode();
								$group_code_type = $rater->getGroupCodeType();
								$policy_data['group_code'] = $hospi_cash_group_code;*/
							}
						} else {

							$policy_data['group_code'] = $rater->getGroupCode();
							$group_code_type = $rater->getGroupCodeType();
							/*$hospi_cash_group_code = $rater->getGroupCode();
							$group_code_type = $rater->getGroupCodeType();
							$policy_data['group_code'] = $hospi_cash_group_code;*/
						}
					}
					$applicantPolicies[$plan_name] = array_merge($commonInsertions, $policy_data);
				}

				$is_individual_cover &&  $individual_adults_premium_calculated++;
				continue;
			}
		}

		$master_quote_ids = [];

		foreach ($applicantPolicies as $policy) {

			$master_quote_ids[] = $this->apimodel->insertData('master_quotes', $policy, true);

			insert_application_log($lead_id, "quote_inserted", json_encode($policy), json_encode(array("response" => "Quote Saved")), $user_id);
		};

		$result['data']['quote_ids'] = $master_quote_ids;
		if($is_api == 1){
            return ($result);
        }else{
            echo json_encode($result);
        }


	}

	public function getGHDQuestions()
	{
		$questions = $this->apimodel->getGHDQuestions();
		echo json_encode($questions);
	}

	public function getGHDAnswers()
	{
		$customer_id = $this->input->post('customer_id');
		$lead_id = $this->input->post('lead_id');
		$answers = $this->apimodel->getdata1("ghd_declaration_answers", "*", " customer_id = $customer_id AND lead_id = $lead_id");
		echo json_encode($answers);
	}

	public function getPolicyAddedMembers()
	{
		$customer_id = $this->input->post('customer_id');
		$lead_id = $this->input->post('lead_id');
		$members = $this->apimodel->getPolicyAddedMembers($customer_id, $lead_id);
		echo json_encode($members);
	}

	protected function getSumInSuredBasedOnPolicySubType($policy_sub_type_id, $policy_id = 0, $mandatory_if_not_selected = [], $policy_sub_type_code_arr = [],$data)
	{
//var_dump($data);exit;
if(!empty($data)){
	$_POST = $data;
}
		$ghi_cover = $pa_cover = $ci_cover = $hospi_cash = $super_top_up_cover = null;

		if (isset($_POST['ghi_cover']) && !empty(trim($_POST['ghi_cover']))) {
			$ghi_cover = trim($_POST['ghi_cover']);
		} else if (isset($_POST['sum_insured1']) && !empty(trim($_POST['sum_insured1']))) {
			$ghi_cover = trim($_POST['sum_insured1']);
		} else {
			$ghi_cover = null;
		}

		if (isset($_POST['pa_cover']) && !empty(trim($_POST['pa_cover']))) {
			$pa_cover = trim($_POST['pa_cover']);
		} else if (isset($_POST['sum_insured2']) && !empty(trim($_POST['sum_insured2']))) {
			$pa_cover = trim($_POST['sum_insured2']);
		} else {
			$pa_cover = null;
		}

		if (isset($_POST['ci_cover']) && !empty(trim($_POST['ci_cover']))) {
			$ci_cover = trim($_POST['ci_cover']);
		} else if (isset($_POST['sum_insured3']) && !empty(trim($_POST['sum_insured3']))) {
			$ci_cover = trim($_POST['sum_insured3']);
		} else {
			$ci_cover = null;
		}

		if (isset($_POST['hospi_cash']) && !empty(trim($_POST['hospi_cash']))) {
			$hospi_cash = trim($_POST['hospi_cash']);
		} else if (isset($_POST['sum_insured6']) && !empty(trim($_POST['sum_insured6']))) {
			$hospi_cash = trim($_POST['sum_insured6']);
		} else {
			$hospi_cash = null;
		}

		if (isset($_POST['super_top_up_cover']) && !empty(trim($_POST['super_top_up_cover']))) {
			$super_top_up_cover = trim($_POST['super_top_up_cover']);
		} else if (isset($_POST['sum_insured5_1']) && !empty(trim($_POST['sum_insured5_1']))) {
			$super_top_up_cover = trim($_POST['sum_insured5_1']);
		} else {
			$super_top_up_cover = null;
		}


		$mapping = [
			1 => $ghi_cover,
			2 => $pa_cover,
			3 => $ci_cover,
			6 => $hospi_cash,
			5 => $super_top_up_cover,
		];
		if (!empty($mandatory_if_not_selected) && $policy_id != 0) {

			if ($mapping[$policy_sub_type_id] == null) {

				if (isset($mandatory_if_not_selected[$policy_id])) {

					if ($mapping[$mandatory_if_not_selected[$policy_id]] == null) {

						$result['messages'][] = "Either " . $policy_sub_type_code_arr[$policy_sub_type_id] . " or " . $policy_sub_type_code_arr[$mandatory_if_not_selected[$policy_id]] . " is Mandatory";
						$result['status'] = false;
						echo json_encode($result);
						exit;
					}
				}
			}
		}

		return $mapping[$policy_sub_type_id];
	}
    function getLeadDetails()
    {

        $data = array();
        $lead_id = $this->input->post('lead_id');

        $data['customer_details'] = $this->apimodel->getLeadDetails($lead_id);

        $data['plan_details'] = $this->apimodel->getProductDetailsAll($data['customer_details'][0]->plan_id);
        $data['payment_modes'] = $this->apimodel->getPlanPayments($data['customer_details'][0]->plan_id);
        $data['proposal_details'] = $this->apimodel->getPlanProposal($data['customer_details'][0]->plan_id, $lead_id);
        $data['proposal_member_details'] = $this->apimodel->getProposalMemberDetails($lead_id);
        $data['family_members'] = $this->apimodel->getMembers();
        $data['policy_declaration'] = $this->apimodel->getPolicyDeclaration($data['customer_details'][0]->plan_id);
        $data['proposal_payment_documents'] = $this->apimodel->getProposalPaymentDocuments($lead_id);
        //$data['assignment_declaration'] = $this->apimodel->getAssignmentDeclaration($data['customer_details'][0]->plan_id);

        $assignment_declaration = '';

        if (isset($data['customer_details'][0]->plan_id) && isset($data['customer_details'][0]->creditor_id)) {

            $assignment_declaration = $this->apimodel->getAssignmentDeclarationByPlanIDCreditorID($data['customer_details'][0]->plan_id, $data['customer_details'][0]->creditor_id);
        }

        $data['assignment_declaration'] = '';

        if (isset($assignment_declaration->content)) {
            $data['assignment_declaration'] = htmlentities($assignment_declaration->content);
        }

        //$data['assignment_declaration'] = $this->apimodel->getAssignmentDeclaration($data['customer_details'][0]->plan_id);
        // plan id
        // creditor_id

        $plan_id = $data['customer_details'][0]->plan_id;
        $creditor_id = $data['customer_details'][0]->creditor_id;

        $data['master_policy_details'] = $this->apimodel->getPolicySubTypePlanCreditor($plan_id, $creditor_id);
        $data['mandatory_if_not_selected'] = [];

        foreach ($data['master_policy_details'] as $policy) {
            // echo $policy->policy_sub_type_id;exit;
            if ($policy->policy_sub_type_id == 1) { // GHI
                if ($policy->basis_id == 7) {
                    $data['sum_insured_type_1']= $this->apimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
                } else {
                    $data['sum_insured_type_1'] = $this->apimodel->getSumInsureData($policy->policy_id);
                }
            }

            if ($policy->policy_sub_type_id == 2) { // GPA
//echo $policy->policy_id;exit;
                if ($policy->basis_id == 7) {
                    $sum_insured_type_2 = $this->apimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
                } else {
                    $sum_insured_type_2 = $this->apimodel->getSumInsureData($policy->policy_id);
                }
                //       echo $this->db->last_query();exit;
                /*if(empty($sum_insured_type_2)){

                    $sum_insured_type_2 = $this->apimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
                }*/

                $data['sum_insured_type_2'] = $sum_insured_type_2;
            }

            if ($policy->policy_sub_type_id == 3) { // Group Critical Illness, GCI
                if ($policy->basis_id == 7) {
                    $sum_insured_type_3 = $this->apimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
                } else {
                    $sum_insured_type_3 = $this->apimodel->getSumInsureData($policy->policy_id);
                }
                /*if(empty($sum_insured_type_3)){

                    $sum_insured_type_3 = $this->apimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
                }*/

                $data['sum_insured_type_3'] = $sum_insured_type_3;

                $data['numbers_of_ci'] = $this->apimodel->getNoOfCI($policy->policy_id);
            }

            if ($policy->policy_sub_type_id == 5) { // Super Topup
                $data['sum_insured_type_5_1'] = $this->apimodel->getSumInsureData($policy->policy_id);
                $data['sum_insured_type_5_2'] = $this->apimodel->getSumInsureDataDeductible($policy->policy_id);
            }


            if ($policy->policy_sub_type_id == 6) { // Hospi Cash
                if ($policy->basis_id == 7) {
                    $data['sum_insured_type_6'] = $this->apimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
                } else {
                    $data['sum_insured_type_6'] = $this->apimodel->getSumInsureData($policy->policy_id);
                }
            }

            $data['mandatory_if_not_selected'][$policy->policy_id] = $this->apimodel->getdata('master_policy_mandatory_if_not_selected_rules', 'master_policy_id, dependent_on_policy_id', 'master_policy_id = ' . $policy->policy_id);
        }

        $policy_sub_type_id_map = [];

        foreach ($data['plan_details'] as $plandetail) {
            $plandetail->family_construct = $this->apimodel->getPolicyFamilyConstruct($plandetail->policy_id);
            $policy_sub_type_id_map[$plandetail->policy_id] = $plandetail->policy_sub_type_code;
        }
        foreach ($data['plan_details'] as $plandetail) {
            $plandetail->policy_premium = $this->apimodel->getPolicyPremium($plandetail->policy_id);
        }
        $i = 0;
        $sum_insured = 0;
        foreach ($data['proposal_details'] as $proposaldetail) {
            $proposal_policy_details = $this->apimodel->getProposalPolicy1($proposaldetail->proposal_details_id);
            $proposaldetail->proposal_policy_details = $proposal_policy_details;
            foreach ($proposaldetail->proposal_policy_details as $policydetail) {
                $policydetail->policy_members = $this->apimodel->getProposalPolicyMember($policydetail->proposal_policy_id);
                $sum_insured += $policydetail->sum_insured;
            }
        }
        $data['sum_insured'] = $sum_insured;
        $data['policy_sub_type_id_map'] = $policy_sub_type_id_map;
        echo json_encode($data);
    } // EO getLeadDetails()

	function getLeadDetailsOLD()
	{

		$data = array();
		$lead_id = $this->input->post('lead_id');

		$data['customer_details'] = $this->apimodel->getLeadDetails($lead_id);

		$data['plan_details'] = $this->apimodel->getProductDetailsAll($data['customer_details'][0]->plan_id);
		$data['payment_modes'] = $this->apimodel->getPlanPayments($data['customer_details'][0]->plan_id);
		$data['proposal_details'] = $this->apimodel->getPlanProposal($data['customer_details'][0]->plan_id, $lead_id);
		$data['proposal_member_details'] = $this->apimodel->getProposalMemberDetails($lead_id);
		$data['family_members'] = $this->apimodel->getMembers();
		$data['policy_declaration'] = $this->apimodel->getPolicyDeclaration($data['customer_details'][0]->plan_id);
		$data['proposal_payment_documents'] = $this->apimodel->getProposalPaymentDocuments($lead_id);
		//$data['assignment_declaration'] = $this->apimodel->getAssignmentDeclaration($data['customer_details'][0]->plan_id);
            
		$assignment_declaration = '';

		if (isset($data['customer_details'][0]->plan_id) && isset($data['customer_details'][0]->creditor_id)) {

			$assignment_declaration = $this->apimodel->getAssignmentDeclarationByPlanIDCreditorID($data['customer_details'][0]->plan_id, $data['customer_details'][0]->creditor_id);
		}

		$data['assignment_declaration'] = '';

		if (isset($assignment_declaration->content)) {
			$data['assignment_declaration'] = htmlentities($assignment_declaration->content);
		}

		//$data['assignment_declaration'] = $this->apimodel->getAssignmentDeclaration($data['customer_details'][0]->plan_id);
		// plan id 
		// creditor_id 

		$plan_id = $data['customer_details'][0]->plan_id;
		$creditor_id = $data['customer_details'][0]->creditor_id;

		$data['master_policy_details'] = $this->apimodel->getPolicySubTypePlanCreditor($plan_id, $creditor_id);
		$data['mandatory_if_not_selected'] = [];

		foreach ($data['master_policy_details'] as $policy) {
           // echo $policy->policy_sub_type_id;exit;
			if ($policy->policy_sub_type_id == 1) { // GHI
				$data['sum_insured_type_1'] = $this->apimodel->getSumInsureData($policy->policy_id);
			}

			if ($policy->policy_sub_type_id == 2) { // GPA
//echo $policy->policy_id;exit;
				$sum_insured_type_2 = $this->apimodel->getSumInsureData($policy->policy_id);
         //       echo $this->db->last_query();exit;
				/*if(empty($sum_insured_type_2)){

					$sum_insured_type_2 = $this->apimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
				}*/

				$data['sum_insured_type_2'] = $sum_insured_type_2;
			}

			if ($policy->policy_sub_type_id == 3) { // Group Critical Illness, GCI

				$sum_insured_type_3 = $this->apimodel->getSumInsureData($policy->policy_id);
				/*if(empty($sum_insured_type_3)){

					$sum_insured_type_3 = $this->apimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
				}*/

				$data['sum_insured_type_3'] = $sum_insured_type_3;

				$data['numbers_of_ci'] = $this->apimodel->getNoOfCI($policy->policy_id);
			}

			if ($policy->policy_sub_type_id == 5) { // Super Topup
				$data['sum_insured_type_5_1'] = $this->apimodel->getSumInsureData($policy->policy_id);
				$data['sum_insured_type_5_2'] = $this->apimodel->getSumInsureDataDeductible($policy->policy_id);
			}


			if ($policy->policy_sub_type_id == 6) { // Hospi Cash
				if ($policy->basis_id == 7) {
					$data['sum_insured_type_6'] = $this->apimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
				} else {
					$data['sum_insured_type_6'] = $this->apimodel->getSumInsureData($policy->policy_id);
				}
			}

			$data['mandatory_if_not_selected'][$policy->policy_id] = $this->apimodel->getdata('master_policy_mandatory_if_not_selected_rules', 'master_policy_id, dependent_on_policy_id', 'master_policy_id = ' . $policy->policy_id);
		}

		$policy_sub_type_id_map = [];

		foreach ($data['plan_details'] as $plandetail) {
			$plandetail->family_construct = $this->apimodel->getPolicyFamilyConstruct($plandetail->policy_id);
			$policy_sub_type_id_map[$plandetail->policy_id] = $plandetail->policy_sub_type_code;
		}
		foreach ($data['plan_details'] as $plandetail) {
			$plandetail->policy_premium = $this->apimodel->getPolicyPremium($plandetail->policy_id);
		}
		$i = 0;
		$sum_insured = 0;
		foreach ($data['proposal_details'] as $proposaldetail) {
			$proposal_policy_details = $this->apimodel->getProposalPolicy1($proposaldetail->proposal_details_id);
			$proposaldetail->proposal_policy_details = $proposal_policy_details;
			foreach ($proposaldetail->proposal_policy_details as $policydetail) {
				$policydetail->policy_members = $this->apimodel->getProposalPolicyMember($policydetail->proposal_policy_id);
				$sum_insured += $policydetail->sum_insured;
			}
		}
		$data['sum_insured'] = $sum_insured;
		$data['policy_sub_type_id_map'] = $policy_sub_type_id_map;
		echo json_encode($data);
	} // EO getLeadDetails()

	function getInsuredMemberDetails()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));
			$customer_id = htmlspecialchars(strip_tags(trim($_POST['customer_id'])));
			$plan_id = htmlspecialchars(strip_tags(trim($_POST['plan_id'])));

			$member_details_raw = $this->apimodel->getProposalPolicyMemberByLeadId($lead_id, $customer_id);
			$member_details = [];

			if (!empty($member_details_raw)) {

				foreach ($member_details_raw as $member) {

					$member_details['member_details'][$member['customer_id']][$member['member_id']] = $member;
				}
			}

			$policy_ids_raw = $this->apimodel->getdata('master_policy', 'policy_id', "plan_id = $plan_id AND isactive = 1");

			$policy_id_arr = $insured_member_criterias = $family_constuct_relation_map = [];

			if (!empty($policy_ids_raw)) {

				foreach ($policy_ids_raw as $policy_id) {

					$policy_id_arr[$policy_id['policy_id']] = $policy_id['policy_id'];
				}

				$family_construct_raw = $this->apimodel->getdata('master_policy_family_construct', 'member_type_id, member_min_age, member_max_age, master_policy_id', "master_policy_id IN (" . implode(',', $policy_id_arr) . ") AND isactive = 1");

				if (!empty($family_construct_raw)) {

					foreach ($family_construct_raw as $family_construct) {

						$insured_member_criterias[$family_construct['master_policy_id']][$family_construct['member_type_id']]['member_type_id'] = $family_construct['member_type_id'];
						$insured_member_criterias[$family_construct['master_policy_id']][$family_construct['member_type_id']]['member_min_age'] = $family_construct['member_min_age'];
						$insured_member_criterias[$family_construct['master_policy_id']][$family_construct['member_type_id']]['member_max_age'] = $family_construct['member_max_age'];

						$family_member_id_arr[$family_construct['member_type_id']] = $family_construct['member_type_id'];
					}

					$family_construct_raw_arr = $this->apimodel->getdata('family_construct', 'id, member_type, is_adult', "id IN (" . implode(',', $family_member_id_arr) . ") AND isactive = 1");

					if (!empty($family_construct_raw_arr)) {

						foreach ($family_construct_raw_arr as $family_construct) {

							$family_constuct_relation_map[$family_construct['id']]['member_type'] = $family_construct['member_type'];
							$family_constuct_relation_map[$family_construct['id']]['is_adult'] = $family_construct['is_adult'];
						}
					}
				}

				$member_details['family_constuct_relation_map'] = $family_constuct_relation_map;
				$member_details['insured_member_criterias'] = $insured_member_criterias;
			}

			echo json_encode($member_details);
		}
		/*else{

			echo $checkToken;
		}	*/

		exit;
	}

	function getSelfDetails()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));
			$customer_id = htmlspecialchars(strip_tags(trim($_POST['customer_id'])));
			$plan_id = htmlspecialchars(strip_tags(trim($_POST['plan_id'])));

			$member_details_arr = $this->apimodel->getdata(
				'master_customer mc, lead_details ld',
				"mc.salutation, mc.first_name, mc.last_name, mc.gender, mc.dob, mc.lead_id, mc.customer_id, ld.trace_id, ld.plan_id",
				"mc.lead_id = $lead_id AND mc.customer_id = $customer_id AND ld.lead_id = mc.lead_id"
			);

			if (!empty($member_details_arr)) {

				//echo json_encode($member_details[0]);

				$member_details['member_details'] = $member_details_arr[0];

				$policy_ids_raw = $this->apimodel->getdata('master_policy', 'policy_id', "plan_id = $plan_id AND isactive = 1");

				$policy_id_arr = $insured_member_criterias = $family_constuct_relation_map = [];

				if (!empty($policy_ids_raw)) {

					foreach ($policy_ids_raw as $policy_id) {

						$policy_id_arr[$policy_id['policy_id']] = $policy_id['policy_id'];
					}

					$family_construct_raw = $this->apimodel->getdata('master_policy_family_construct', 'member_type_id, member_min_age, member_max_age, master_policy_id', "master_policy_id IN (" . implode(',', $policy_id_arr) . ") AND isactive = 1");

					if (!empty($family_construct_raw)) {

						foreach ($family_construct_raw as $family_construct) {

							$insured_member_criterias[$family_construct['master_policy_id']][$family_construct['member_type_id']]['member_type_id'] = $family_construct['member_type_id'];
							$insured_member_criterias[$family_construct['master_policy_id']][$family_construct['member_type_id']]['member_min_age'] = $family_construct['member_min_age'];
							$insured_member_criterias[$family_construct['master_policy_id']][$family_construct['member_type_id']]['member_max_age'] = $family_construct['member_max_age'];

							$family_member_id_arr[$family_construct['member_type_id']] = $family_construct['member_type_id'];
						}

						$family_construct_raw_arr = $this->apimodel->getdata('family_construct', 'id, member_type, is_adult', "id IN (" . implode(',', $family_member_id_arr) . ") AND isactive = 1");

						if (!empty($family_construct_raw_arr)) {

							foreach ($family_construct_raw_arr as $family_construct) {

								$family_constuct_relation_map[$family_construct['id']]['member_type'] = $family_construct['member_type'];
								$family_constuct_relation_map[$family_construct['id']]['is_adult'] = $family_construct['is_adult'];
							}
						}
					}

					$member_details['family_constuct_relation_map'] = $family_constuct_relation_map;
					$member_details['insured_member_criterias'] = $insured_member_criterias;
				}

				echo json_encode($member_details);
			}
		}
		exit;
	}

	function get_member_id()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$get_result = $this->apimodel->getMemberIdAndRelation($_POST['lead_id'], $_POST['customer_id']);
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
		} else {
			echo $checkToken;
		}

		exit;
	}

	function getLeadDetailsCustomer()
	{
		$data = array();
		$lead_id = $this->input->post('lead_id');
		$otp = $this->input->post('otp');
		$data['customer_details'] = $this->apimodel->getLeadDetails($lead_id);
		$data['plan_details'] = $this->apimodel->getProductDetailsAll($data['customer_details'][0]->plan_id);
		$data['payment_modes'] = $this->apimodel->getPlanPayments($data['customer_details'][0]->plan_id);
		$data['proposal_details'] = $this->apimodel->getPlanProposal($data['customer_details'][0]->plan_id, $lead_id);
		$data['family_members'] = $this->apimodel->getMembers();
		foreach ($data['plan_details'] as $plandetail) {
			$plandetail->family_construct = $this->apimodel->getPolicyFamilyConstruct($plandetail->policy_id);
		}
		foreach ($data['plan_details'] as $plandetail) {
			$plandetail->policy_premium = $this->apimodel->getPolicyPremium($plandetail->policy_id);
		}
		foreach ($data['proposal_details'] as $proposaldetail) {
			$proposal_policy_details = $this->apimodel->getProposalPolicy1($proposaldetail->proposal_details_id);
			$proposaldetail->proposal_policy_details = $proposal_policy_details;
			foreach ($proposal_policy_details as $policydetail) {
				$policydetail->policy_members = $this->apimodel->getProposalPolicyMember($policydetail->proposal_policy_id);
			}
		}

		echo json_encode($data);
	}

	function sendcustomerpaymentformotp()
	{
		$lead_id = $_POST['lead_id'];
		$isactive = $this->db->get_where('short_urls', array('lead_id' => $lead_id))->row();
		if ($isactive->status == 1) {
			$this->db->where('lead_id', $lead_id);
			$otp = rand(1000, 9999);
			//$this->db->insert('short_urls', array('otp' => $otp));
			$this->db->insert('short_urls', array('otp' => '1234'));
			$count = $isactive->hits + 1;
			$this->db->where('lead_id', $lead_id);
			$this->db->update('short_urls', array('hits' => $count));
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success')));
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Link Expired')));
		}
	}
	function checkLeadotpCustomer()
	{
		$lead_id = $_POST['lead_id'];
		$otp = $_POST['otp'];

		$lead_id = encrypt_decrypt_password($lead_id, 'D');
		//$isactive = $this->db->get_where('short_urls', array('lead_id' => $lead_id))->row();
		$to_send_data = $this->apimodel->getdata('lead_details ld, short_urls su, master_plan mp', 'ld.trace_id, ld.mode_of_payment, su.status, su.hits, su.otp, ld.mobile_no, ld.email_id, mp.plan_name, mp.plan_id', 'ld.lead_id = '.$lead_id.' AND ld.plan_id = mp.plan_id AND ld.lead_id = su.lead_id AND su.otp IS NOT NULL AND su.status = 1');
		/*$isactive = $this->db->get_where('short_urls', array('lead_id' => $lead_id))->row();
		
		$this->db->select('mode_of_payment,trace_id');
		$lead_details = $this->db->get_where('lead_details', array('lead_id' => $lead_id))->row();

		if (($isactive->status == 1 && $isactive->otp == $otp) || true) { // to remove the bypass*/
        $to_send_data[0]['otp']=1234;
		if (($to_send_data[0]['otp'] == $otp) || ENV_BYPASSED) { // to remove the bypass
			
			$count = $to_send_data[0]['hits'] + 1;
			$this->db->where('lead_id', $lead_id);
			$this->db->update('short_urls', array('hits' => $count, 'status' => 2)); //status set to 2 when OTP verification is successful
			/*$this->db->update('lead_details', ['status' => 'Customer-Payment-Awaiting'], 'lead_id = '.$lead_id);

			insert_application_log($lead_id, "verify_otp", json_encode($_POST), json_encode(['status' => 'Customer-Payment-Awaiting']), 0);

			$long_url = 'FRONT_END_URL'.'paymentgatewayredirect/'.$lead_id_enc;
			$payment_url = $this->get_tiny_url($long_url);

			$data['lead_id'] = $lead_id;
			$data['mobile_no'] = $to_send_data[0]['mobile_no'];
			$data['plan_id'] = $to_send_data[0]['plan_id'];
			$data['email_id'] = $to_send_data[0]['email_id'];
			$data['alerts'][] = $to_send_data[0]['plan_name'];
			$data['alerts'][] = filter_var($payment_url, FILTER_VALIDATE_URL) ? $payment_url : $long_url;

			$response = triggerCommunication(['A1660'], $data);

			insert_application_log($lead_id, "otp_success_communication", json_encode($data), json_encode($response), 0);

			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => array("lead_id" => $lead_id, "otp" => $otp)));*/


			if($to_send_data[0]['mode_of_payment'] == 1){

				$this->db->update('lead_details', ['status' => 'Customer-Payment-Awaiting'], 'lead_id = ' . $lead_id);

				insert_application_log($lead_id, "verify_otp", json_encode($_POST), json_encode(['status' => 'Customer-Payment-Awaiting']), 0);

				/*$long_url = 'FRONT_END_URL'.'paymentgatewayredirect/'.$lead_id_enc;
				$payment_url = $this->get_tiny_url($long_url);

				$data['lead_id'] = $lead_id;
				$data['mobile_no'] = $to_send_data[0]['mobile_no'];
				$data['plan_id'] = $to_send_data[0]['plan_id'];
				$data['email_id'] = $to_send_data[0]['email_id'];
				$data['alerts'][] = $to_send_data[0]['plan_name'];
				$data['alerts'][] = filter_var($payment_url, FILTER_VALIDATE_URL) ? $payment_url : $long_url;

				$response = triggerCommunication(['A1660'], $data);

				insert_application_log($lead_id, "otp_success_communication", json_encode($data), json_encode($response), 0);*/

				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => array("lead_id" => $lead_id, "otp" => $otp, "payment_needed" => 1)));
			}
			else if($to_send_data[0]['mode_of_payment'] == 3){

				$this->db->update('lead_details', ['status' => 'CO-Approval-Awaiting'], 'lead_id = ' . $lead_id);

				insert_application_log($lead_id, "verify_otp", json_encode($_POST), json_encode(['status' => 'CO-Approval-Awaiting']), 0);

				/*$long_url = 'FRONT_END_URL'.'paymentgatewayredirect/'.$lead_id_enc;
				$payment_url = $this->get_tiny_url($long_url);

				$data['lead_id'] = $lead_id;
				$data['mobile_no'] = $to_send_data[0]['mobile_no'];
				$data['plan_id'] = $to_send_data[0]['plan_id'];
				$data['email_id'] = $to_send_data[0]['email_id'];
				$data['alerts'][] = $to_send_data[0]['plan_name'];
				$data['alerts'][] = filter_var($payment_url, FILTER_VALIDATE_URL) ? $payment_url : $long_url;

				$response = triggerCommunication(['A1660'], $data);

				insert_application_log($lead_id, "otp_success_communication", json_encode($data), json_encode($response), 0);*/

				$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => array("trace_id" => $to_send_data[0]['trace_id'], "otp" => $otp, "payment_needed" => 0)));

				insert_application_log($lead_id, "verify_otp", json_encode($_POST), $response, 0);

				echo $response;
				exit;
			}
		} else {

			insert_application_log($lead_id, "verify_otp", json_encode($_POST), json_encode(array("Message" => 'Incorrect OTP')), 0);

			$shorturl_data['updated'] = date('Y-m-d H:i:s');
			$shorturl_data['otp'] = rand(100000, 999999);

			$result = $this->apimodel->updateRecordarr('short_urls', $shorturl_data, 'lead_id = '.$lead_id);
			
			insert_application_log($lead_id, "verify_otp", json_encode($shorturl_data), json_encode(array("response" => 'New OTP Generated', 'new_otp' => $shorturl_data)), 0);

			//to check as no parameters were given
			$data['lead_id'] = $lead_id;
			$data['mobile_no'] = $to_send_data[0]['mobile_no'];
			$data['plan_id'] = $to_send_data[0]['plan_id'];
			$data['email_id'] = $to_send_data[0]['email_id'];
			$data['alerts'][] = $to_send_data[0]['plan_name'];
			$data['alerts'][] = $shorturl_data['otp'];

			$response = triggerCommunication(['A1659'], $data);

			if(ENV_BYPASSED || $response['status_code'] == 200){

				$response = array_merge($response, ['success' => true, 'msg' => 'New OTP generated and sent to customer']);

				insert_application_log($lead_id, "failure_otp_retrigger", json_encode($data), json_encode($response), 0);
			}
			else{

				$response = array_merge($response, ['success' => false, 'msg' => 'Something went wrong when sending communication link']);

				insert_application_log($lead_id, "failure_otp_retrigger", json_encode($data), json_encode($response), 0);
			}

			$response = json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Incorrect OTP Please')));

			insert_application_log($lead_id, "failure_otp_retrigger", json_encode($data), $response, 0);

			echo $response;
			exit;
		}
	}
	
	function getPolicyUpdateDetails()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$id = $this->input->post('id');
			$data['plan_id'] = $id;
			$policy_id = $this->input->post('policy_id');

			if (!empty($policy_id)) {
				$data['family_construct'] = $this->apimodel->getPolicyFamilyConstruct($policy_id);
				$data['policy_premium'] = $this->apimodel->getPolicyPremium($policy_id);
				$data['si_type'] = $this->apimodel->getPolicySiType($policy_id);
				$data['premium_basis'] = $this->apimodel->getPolicyPremiumBasis($policy_id);
				$data['policydetails'] = $this->apimodel->getProductDetails($id, $policy_id);
				$data['mandatory_if_not_selections'] = $this->apimodel->getMandatoryIfNotSelections($policy_id);

				//
			}
			$data['details'] = $this->apimodel->getProductDetails($id);
			$data['sitypes'] = $this->apimodel->getSiType();
			$data['insurers'] = $this->apimodel->getInsurer();
			$data['sipremiumbasis'] = $this->apimodel->getSiPremiumBasis();
			$data['members'] = $this->apimodel->getMembers();
            $data['payment_mode_id'] = $this->db->query("select payment_mode_id from plan_payment_mode where payment_mode_id=4 AND master_plan_id=".$id)->row()->payment_mode_id;
			$data['adult_members'] = $data['child_members'] = [];
			foreach ($data['members'] as $member) {
				if ($member->is_adult == "Y") {
					$data['adult_members'][] = $member;
				} else {
					$data['child_members'][] = $member;
				}
			}

			if (isset($data['details'][0]->policy_id)) {
				//$data['master_policy_family_construct'] = $this->apimodel->getPolicyFamilyConstruct($data['details'][0]->policy_id);
				//$data['policy_premium'] = $this->apimodel->getPolicyPremium($data['details'][0]->policy_id);
				//$data['premium_basis'] = $this->apimodel->getPolicyPremiumBasis($data['details'][0]->policy_id);
				// $data['si_type'] = $this->apimodel->getPolicySiType($data['details'][0]->policy_id); // new added
				//	$data['policydetails'] = $this->apimodel->getProductDetails($id, $data['details'][0]->policy_id); // new added
			}

			echo json_encode($data);
		} else {
			echo $checkToken;
		}
	}

	function PolicySubTypeListing()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPolicySubTypeList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	function getproposalpolicybylead()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getproposalpolicybylead($_POST['leads']);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	function FamilyConstructListing()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getFamilyConstructList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function getFamilyConstructFormData()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getFamilyConstructFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function FeaturesListing(){
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getFeaturesList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function ProductsListing()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getProductsList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function getPolicySubTypeFormData()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPolicySubTypeFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getPolicyTypes()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPolicyType();
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addEditPolicySubType()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['policy_sub_type_name'] = (!empty($_POST['policy_sub_type_name'])) ? $_POST['policy_sub_type_name'] : '';
			$data['policy_type_id'] = (!empty($_POST['policy_type_id'])) ? $_POST['policy_type_id'] : '';

			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;

			if (!empty($_POST['policy_sub_type_id'])) {
				$result = $this->apimodel->updateRecord('master_policy_sub_type', $data, "policy_sub_type_id='" . $_POST['policy_sub_type_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_policy_sub_type', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	function addEditFamilyConstruct()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['member_type'] = (!empty($_POST['member_type'])) ? $_POST['member_type'] : '';

			if (!empty($_POST['family_construct_id'])) {
				$result = $this->apimodel->updateRecord('family_construct', $data, "id='" . $_POST['family_construct_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('family_construct', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	//Delete Insurer
	function delPolicySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_policy_sub_type', $data, "policy_sub_type_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	function delFamilyConstruct()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('family_construct', $data, "id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function insurerListing()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getInsurerList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	function getInsurerFormData()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getInsurerFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addEditInsurer()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['insurer_name'] = (!empty($_POST['insurer_name'])) ? $_POST['insurer_name'] : '';
			$data['insurer_code'] = (!empty($_POST['insurer_code'])) ? $_POST['insurer_code'] : '';

			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;

			if (!empty($_POST['insurer_id'])) {
				$result = $this->apimodel->updateRecord('master_insurer', $data, "insurer_id='" . $_POST['insurer_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_insurer', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete Insurer
	function delInsurer()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_insurer', $data, "insurer_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function suminsuredListing()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSuminsuredList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function getSuminsuredFormData()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSuminsuredFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addEditSuminsured()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['suminsured_type'] = (!empty($_POST['suminsured_type'])) ? $_POST['suminsured_type'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;

			if (!empty($_POST['suminsured_type_id'])) {
				$result = $this->apimodel->updateRecord('master_suminsured_type', $data, "suminsured_type_id='" . $_POST['suminsured_type_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_suminsured_type', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete Insurer
	function delSuminsured()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_suminsured_type', $data, "suminsured_type_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addMasterPlan()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['creditor_id'] = $this->input->post('creditor_id');
			$data['plan_name'] = $this->input->post('plan_name');
			$data['policy_type_id'] = $this->input->post('policy_type_id');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('plan_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_plan', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_plan', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addMasterPolicy()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['plan_id'] = $this->input->post('plan_id');
			$data['policy_sub_type_id'] = $this->input->post('policy_sub_type_id');
			$data['insurer_id'] = $this->input->post('insurer_id');
			$data['policy_number'] = $this->input->post('policy_number');
			$data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
			$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
			$data['pdf_type'] = $this->input->post('pdf_type');
			$data['is_optional'] = $this->input->post('is_optional');
			$data['max_member_count'] = $this->input->post('max_member_count');
			$data['policy_type_id'] = $this->input->post('policy_type_id');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('policy_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_policy', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_policy', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addMasterPolicyType()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['policy_type_name'] = $this->input->post('policy_type_name');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('policy_type_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_policy_type', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_policy_type', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addMasterPolicySubType()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['policy_sub_type_name'] = $this->input->post('policy_sub_type_name');
			$data['policy_type_id'] = $this->input->post('policy_type_id');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('policy_sub_type_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_policy_sub_type', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_policy_sub_type', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addFamilyConstruct()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['member_type'] = $this->input->post('member_type');
			if (!empty($id)) {
				$where = array('id' => $id);
				$data = $this->apimodel->updateRecordarr('family_construct', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('family_construct', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addMasterPolicyFamilyConstruct()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['master_policy_id'] = $this->input->post('master_policy_id');
			$data['member_type_id'] = $this->input->post('member_type_id');
			$data['member_min_age'] = $this->input->post('member_min_age');
			$data['member_max_age'] = $this->input->post('member_max_age');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('family_construct_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_policy_family_construct', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_policy_family_construct', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addSiPremiumBasis()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['si_premium_basis'] = $this->input->post('si_premium_basis');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('si_premium_basis_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_si_premium_basis', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_si_premium_basis', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function getTenureForPolicies()
	{
		$tenures = $this->apimodel->getTenureForPolicies($this->input->post('policies'));

		echo json_encode($tenures);
	}

	function uploadexcel()
	{
		$data = array();
		$records = json_decode($this->input->post('exceldata'));
		$this->apimodel->insertBatchData('master_policy_premium', $records);
	}
	function uploadcoexcel()
	{
		$data = array();
		$this->load->library('excel');

		$path = $_POST['path'];
		$object = PHPExcel_IOFactory::load($path);
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();

			for ($row = 2; $row <= $highestRow; $row++) {

				$proposal_policy_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				$lead_id = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				$hb_receipt_number = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				$reference_no = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$amount = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				$payment_date = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				$status = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				$remark = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
				if (!empty($proposal_policy_id)) {
					$policy_details_id = $this->db->get_where('proposal_policy', array('proposal_policy_id' => $proposal_policy_id))->row()->proposal_details_id;
					$this->db->where('proposal_details_id', $policy_details_id);
					$this->db->update('proposal_details', array('transaction_date' => $payment_date, 'hb_receipt_number' => $hb_receipt_number, 'payment_remark' => $remark, 'transaction_number' => $reference_no));
				}
			}
		}
	}

	function addSuminsuredType()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$id = $this->input->post('id');
			$data['suminsured_type'] = $this->input->post('suminsured_type');
			$data['isactive'] = intval($this->input->post('status'));
			if (!empty($id)) {
				$where = array('suminsured_type_id' => $id);
				$data = $this->apimodel->updateRecordarr('master_suminsured_type', $data, $where);
				return 1;
			} else {
				$this->apimodel->insertData('master_suminsured_type', $data);
				return 1;
			}
		} else {
			echo $checkToken;
		}
	}

	function addNewProduct()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
// print_r($checkToken);die;
		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$data['plan_name'] = $this->input->post('plan_name');
			$creditor_id = $this->input->post('creditor_id');
			$data['creditor_id'] = $creditor_id;
			$data['policy_type_id'] = $this->input->post('policy_type_id');
			$policy_sub_type_ids = explode(',', $this->input->post('policy_sub_type_id'));
			//$payment_modes = explode(',', $this->input->post('payment_modes'));
			$insert_id = $this->apimodel->insertData('master_plan', $data, 1);
			if (!empty($insert_id)) {
				$data2 = array();
				foreach ($policy_sub_type_ids as $subtype) {
					$data2['isactive'] = 1;
					$data2['plan_id'] = $insert_id;
					$data2['creditor_id'] = $creditor_id;
					$data2['policy_sub_type_id'] = $subtype;
					$this->apimodel->insertData('master_policy', $data2, 1);
				}

				/*foreach ($payment_modes as $modes) {
					$data3 = array();
					$data3['master_plan_id'] = $insert_id;
					$data3['payment_mode_id'] = $modes;
					$this->apimodel->insertData('plan_payment_mode', $data3);
				}*/
				if (!empty($_POST['payment_modes'])) {
					for ($i = 0; $i < sizeof($_POST['payment_modes']); $i++) {
						$payment_data = array();
						$payment_data['master_plan_id'] = $insert_id;
						$payment_data['payment_mode_id'] = $_POST['payment_modes'][$i];
						$payment_data['workflow_id'] = $_POST['payment_workflow'][$i];
						$this->apimodel->insertData('plan_payment_mode', $payment_data);
					}
				}

				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Product created successfully'), "Data" => $insert_id));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	function UpdateProduct()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$plan_id = trim($this->input->post('plan_id'));
			$data['plan_name'] = trim($this->input->post('plan_name'));
			$data['creditor_id'] = trim($this->input->post('creditor_id'));
			$data['policy_type_id'] = trim($this->input->post('policy_type_id'));
			$policy_sub_type_ids = explode(',', $this->input->post('policy_sub_type_id'));
			//$payment_modes = explode(',', $this->input->post('payment_modes'));
			$this->apimodel->updateRecordarr('master_plan', $data, array('plan_id' => $plan_id));

			$data2 = array();
			$allsubtype = $this->apimodel->getpolicysubtypeofplan($plan_id);
			$allsubtypes = array();
			foreach ($allsubtype as $subtype) {
				array_push($allsubtypes, $subtype->policy_sub_type_id);
			}
			foreach ($policy_sub_type_ids as $subtype) {
				if (!in_array($subtype, $allsubtypes)) {
					//$data2['isactive'] = 0;
					$data2['isactive'] = 1;
					$data2['plan_id'] = $plan_id;
					$data2['policy_sub_type_id'] = $subtype;
					$data2['creditor_id'] = $data['creditor_id'];
					$this->apimodel->insertData('master_policy', $data2);
				}
			}
			foreach ($allsubtypes as $subtype) {
				if (!in_array($subtype, $policy_sub_type_ids)) {
					$this->apimodel->deletemasterpolicy($subtype, $plan_id);
				}
			}
			/*$allpayment = $this->apimodel->getpaymentofplan($plan_id);
			$allpayments = array();
			foreach ($allpayment as $subtype) {
				array_push($allpayments, $subtype->payment_mode_id);
			}
			$this->apimodel->deletepolicypayment($plan_id);
			foreach ($payment_modes as $modes) {
				if (!in_array($modes, $allpayments)) {
					$data3 = array();
					$data3['master_plan_id'] = $plan_id;
					$data3['payment_mode_id'] = $modes;
					$data3['isactive'] = 1;
					$this->apimodel->insertData('plan_payment_mode', $data3);
				} else {
					$this->apimodel->updatepolicypayment($plan_id, $modes);
				}
			}*/

			if (!empty($_POST['payment_modes'])) {
				$this->apimodel->delrecord("plan_payment_mode", "master_plan_id", $plan_id);
				for ($i = 0; $i < sizeof($_POST['payment_modes']); $i++) {
					$payment_data = array();
					$payment_data['master_plan_id'] = $plan_id;
					$payment_data['payment_mode_id'] = $_POST['payment_modes'][$i];
					$payment_data['workflow_id'] = $_POST['payment_workflow'][$i];
					$this->apimodel->insertData('plan_payment_mode', $payment_data);
				}
			}

			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record updated successfully.'), "Data" => $plan_id));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function addNewPolicy()
	{
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

        $checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$plan_id = $this->input->post('plan_id');

			$data['creditor_id'] = $this->input->post('creditor_id');
			$policy_sub_type_id = $this->input->post('policy_sub_type_id');
			$data['policy_number'] = $this->input->post('policy_number');
			$data['is_optional'] = intval($this->input->post('is_optional'));
			$data['is_combo'] = intval($this->input->post('is_combo'));
			$adult_count = $this->input->post('adult_count');
			//print_r($this->input->post('adult_count'));die;
				$child_count = $this->input->post('child_count');
			//	$data['premium_type'] = $this->input->post('premium_type');
			$data['isactive'] = 1;
			$data['pdf_type'] = $this->input->post('pdf_type');
			$data['insurer_id'] = $this->input->post('insurer_id');
			$data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
			$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
			$data['plan_code'] = $this->input->post('plan_code');
			$data['product_code'] = $this->input->post('product_code');
			$data['scheme_code'] = $this->input->post('scheme_code');
			$data['source_name'] = $this->input->post('source_name');
			$data['max_member_count'] = $this->input->post('max_member_count');
			$data['adult_count'] =  $this->input->post('adult_count');
            $data['child_count'] =  $this->input->post('child_count');
            $data['cd_balance'] = $this->input->post('cd_balance');
            $data['threshold'] = $this->input->post('threshold');
			$sitype = $this->input->post('sitype');
			$sibasis = $this->input->post('sibasis');
             $default_sumInsured = $this->input->post('default_sumInsured');
			if ($sibasis != 1) {
				$exceldata = json_decode($this->input->post('exceldata'));
			} else {
				$sum_insured_opt = explode(',', $this->input->post('sum_insured_opt'));
				$premium_opt = explode(',', $this->input->post('premium_opt'));
				$group_code = explode(',', $this->input->post('group_code'));
				$group_code_spouse = explode(',', $this->input->post('group_code_spouse'));
				$tax_opt = explode(',', $this->input->post('tax_opt'));
			}

			$members = explode(',', $this->input->post('members'));
			$minages = explode(',', $this->input->post('minage'));
			$maxages = explode(',', $this->input->post('maxage'));
			$min_age_type = explode(',', $this->input->post('min_age_type'));

			$result = $this->apimodel->updateRecordarr('master_policy', $data, array('policy_id' => $policy_sub_type_id));

			$mandatory_if_not_selected = $this->input->post('mandatory_if_not_selected');

			$mandatory_insert = [];
			if (is_array($mandatory_if_not_selected)) {
				foreach ($mandatory_if_not_selected as $policy_id) {
					$mandatory_insert[] = [
						'master_policy_id' => $policy_sub_type_id,
						'dependent_on_policy_id' => $policy_id
					];
				}

				$this->apimodel->insertBatchData('master_policy_mandatory_if_not_selected_rules', $mandatory_insert);
			}

			if ($result) {
				$i = 0;
				$data2 = array();
				for ($i = 0; $i < count($members); $i++) {
					$data2[$i]['master_policy_id'] = $policy_sub_type_id;
					$data2[$i]['member_type_id'] = $members[$i];
					if ($min_age_type[$i] == 'years') {
						$data2[$i]['member_min_age'] = $minages[$i];
						$data2[$i]['member_min_age_days'] = null;
					} else {
						$data2[$i]['member_min_age'] = null;
						$data2[$i]['member_min_age_days'] = $minages[$i];
					}
					$data2[$i]['member_max_age'] = $maxages[$i];
				}
				$this->apimodel->insertBatchData('master_policy_family_construct', $data2);
				$data3 = array('master_policy_id' => $policy_sub_type_id, 'si_premium_basis_id' => $sibasis);
				$this->apimodel->insertData('master_policy_premium_basis_mapping', $data3);
				$data4 = array('master_policy_id' => $policy_sub_type_id, 'suminsured_type_id' => $sitype);
				$this->apimodel->insertData('master_policy_si_type_mapping', $data4);

				if ($sibasis == 5) {
                    $this->apimodel->insertBatchData('master_policy_premium_permile', $exceldata);
                    $data_11=array(
                        "sum_insured"=>$default_sumInsured,
                        "master_policy_id"=>$policy_sub_type_id,
                    );
                   // var_dump($data_11);die;
                    $this->db->insert('master_policy_premium', $data_11);
				} else if ($sibasis == 7) {
					$this->apimodel->insertBatchData('master_per_day_tenure_premiums', $exceldata);
				} else if ($sibasis != 1) {
					$this->apimodel->insertBatchData('master_policy_premium', $exceldata);
				} else {

					$j = 0;
					$data5 = array();
					for ($j = 0; $j < count($sum_insured_opt); $j++) {
						$is_taxable = (int) $tax_opt[$j];
						$data5[$j]['master_policy_id'] = $policy_sub_type_id;
						$data5[$j]['sum_insured'] = $sum_insured_opt[$j];
						$data5[$j]['group_code'] = $group_code[$j];
						$data5[$j]['group_code_spouse'] = $group_code_spouse[$j];
						$data5[$j]['is_taxable'] = $is_taxable;
						$data5[$j]['adult_count'] = $adult_count;
					    $data5[$j]['child_count'] = $child_count;
						if ($is_taxable == 0) {
							$data5[$j]['premium_rate'] = $premium_opt[$j];
							$data5[$j]['premium_with_tax'] = NULL;
						} else {
							$data5[$j]['premium_with_tax'] = $premium_opt[$j];
							$data5[$j]['premium_rate'] = NULL;
						}
						//$data5[$j]['is_absolute']=$data['premium_type'];
					}


					$this->apimodel->insertBatchData('master_policy_premium', $data5);
				}
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'), "Data" => $plan_id));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	function updateNewPolicy()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			
			$data = array();
			$utoken = $this->input->post('utoken');
			$plan_id = $this->input->post('plan_id');
			$policy_sub_type_id = $this->input->post('policy_sub_type_id');
        	$adult_count = $this->input->post('adult_count');
		//print_r($this->input->post('adult_count'));die;
			$child_count = $this->input->post('child_count');
			$data['creditor_id'] = $this->input->post('creditor_id'); // new added 
			$data['policy_number'] = $this->input->post('policy_number');
			$data['is_optional'] = intval($this->input->post('is_optional'));
			// $data['premium_type'] = $this->input->post('premium_type');
			$data['is_combo'] = intval($this->input->post('is_combo'));
			$data['isactive'] = 1;
			$data['pdf_type'] = $this->input->post('pdf_type');
			$data['insurer_id'] = $this->input->post('insurer_id');

			$data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
			$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
			$data['plan_code'] = $this->input->post('plan_code');
			$data['product_code'] = $this->input->post('product_code');
			$data['source_name'] = $this->input->post('source_name');
			$data['max_member_count'] = $this->input->post('max_member_count');
            $data['cd_balance'] = $this->input->post('cd_balance');
            $data['threshold'] = $this->input->post('threshold');
			$sitype = $this->input->post('sitype');
			//$sitype = $this->input->post('sum_insured_type'); // changes and new added 

			$sibasis = $this->input->post('sibasis');
			$default_sumInsured = $this->input->post('default_sumInsured');

			//echo $sibasis;exit;
			if ($sibasis != 1) {
				$exceldata = json_decode($this->input->post('exceldata'));
			} else {
				$sum_insured_opt = explode(',', $this->input->post('sum_insured_opt'));
				$premium_opt = explode(',', $this->input->post('premium_opt'));
				$group_code = explode(',', $this->input->post('group_code'));
				$group_code_spouse = explode(',', $this->input->post('group_code_spouse'));
				$tax_opt = explode(',', $this->input->post('tax_opt'));
			}
			$members = explode(',', $this->input->post('members'));
			$minages = explode(',', $this->input->post('minage'));
			$maxages = explode(',', $this->input->post('maxage'));
			$min_age_type = explode(',', $this->input->post('min_age_type'));

			$mandatory_if_not_selected = $this->input->post('mandatory_if_not_selected');

			$this->apimodel->inactivateRecord('master_policy_mandatory_if_not_selected_rules', array('master_policy_id' => $policy_sub_type_id));

			$mandatory_insert = [];
			if (is_array($mandatory_if_not_selected)) {
				foreach ($mandatory_if_not_selected as $policy_id) {
					$mandatory_insert[] = [
						'master_policy_id' => $policy_sub_type_id,
						'dependent_on_policy_id' => $policy_id
					];
				}
				$this->apimodel->insertBatchData('master_policy_mandatory_if_not_selected_rules', $mandatory_insert);
			}
			//echo "Testing...";

			$this->apimodel->updateRecordarr('master_policy', $data, array('policy_id' => $policy_sub_type_id));

			//echo "Testing2...";
			$i = 0;
			$data2 = array();

			for ($i = 0; $i < count($members); $i++) {
				$data2[$i]['master_policy_id'] = $policy_sub_type_id;
				$data2[$i]['member_type_id'] = $members[$i];
				if ($min_age_type[$i] == 'years') {
					$data2[$i]['member_min_age'] = $minages[$i];
					$data2[$i]['member_min_age_days'] = null;
				} else {
					$data2[$i]['member_min_age'] = null;
					$data2[$i]['member_min_age_days'] = $minages[$i];
				}
				$data2[$i]['member_max_age'] = $maxages[$i];
			}

			$this->apimodel->inactivateRecord('master_policy_family_construct', array('master_policy_id' => $policy_sub_type_id));
			$this->apimodel->insertBatchData('master_policy_family_construct', $data2);
			$this->apimodel->inactivateRecord('master_policy_premium_basis_mapping', array('master_policy_id' => $policy_sub_type_id));
			$data3 = array('master_policy_id' => $policy_sub_type_id, 'si_premium_basis_id' => $sibasis);
			$this->apimodel->insertData('master_policy_premium_basis_mapping', $data3);
			$this->apimodel->inactivateRecord('master_policy_si_type_mapping', array('master_policy_id' => $policy_sub_type_id));
			$data4 = array('master_policy_id' => $policy_sub_type_id, 'suminsured_type_id' => $sitype);
			$this->apimodel->insertData('master_policy_si_type_mapping', $data4);

			if ($sibasis == 5) {
				if (!empty($exceldata) && count($exceldata) > 0) {
					$this->apimodel->inactivateRecord('master_policy_premium_permile', array('master_policy_id' => $policy_sub_type_id));
					$this->apimodel->insertBatchData('master_policy_premium_permile', $exceldata);
					$query=$this->db->query("select * from master_policy_premium where master_policy_id=".$policy_sub_type_id)->row();
					if($this->db->affected_rows() >0){
                        $where=array(
                            "master_policy_id"=>$policy_sub_type_id,
                        );
                        $data_11=array(
                            "sum_insured"=>$default_sumInsured,
                        );
                        $this->db->where($where);
                        $this->db->update('master_policy_premium', $data_11);
                    }else{
                        $data_11=array(
                            "sum_insured"=>$default_sumInsured,
                            "master_policy_id"=>$policy_sub_type_id,
                        );
                        $this->db->insert('master_policy_premium', $data_11);
                    }


				}
			} else if ($sibasis == 7) {
				if (!empty($exceldata) && count($exceldata) > 0) {
					$this->apimodel->inactivateRecord('master_per_day_tenure_premiums', array('master_policy_id' => $policy_sub_type_id));
					$this->apimodel->insertBatchData('master_per_day_tenure_premiums', $exceldata);
				}
			} else if ($sibasis != 1) {
				if (!empty($exceldata) && count($exceldata) > 0) {
					$this->apimodel->inactivateRecord('master_policy_premium', array('master_policy_id' => $policy_sub_type_id));
					$this->apimodel->insertBatchData('master_policy_premium', $exceldata);
				}
			} else {
				//echo 111111;die;
				$this->apimodel->inactivateRecord('master_policy_premium', array('master_policy_id' => $policy_sub_type_id));
				$j = 0;
				$data5 = array();
				for ($j = 0; $j < count($sum_insured_opt); $j++) {
					$is_taxable = (int) $tax_opt[$j];
					$data5[$j]['master_policy_id'] = $policy_sub_type_id;
					$data5[$j]['sum_insured'] = $sum_insured_opt[$j];
					$data5[$j]['group_code'] = $group_code[$j];
					$data5[$j]['group_code_spouse'] = $group_code_spouse[$j];
					$data5[$j]['is_taxable'] = $is_taxable;
					$data5[$j]['adult_count'] = $adult_count;
					$data5[$j]['child_count'] = $child_count;
					if ($is_taxable == 0) {
						$data5[$j]['premium_rate'] = $premium_opt[$j];
						$data5[$j]['premium_with_tax'] = $premium_opt[$j];
						//$data5[$j]['premium_with_tax'] = NULL;
					} else {
						$data5[$j]['premium_with_tax'] = $premium_opt[$j];
						$data5[$j]['premium_rate'] = $premium_opt[$j];
						//$data5[$j]['premium_rate'] = NULL;
					}
					// $data5[$j]['is_absolute']=$data['premium_type'];
				
				}
				$this->apimodel->insertBatchData('master_policy_premium', $data5);
			}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'), "Data" => $plan_id));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function UpdatePolicy()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$plan_id = $this->input->post('plan_id');
			$policy_sub_type_id = $this->input->post('policy_sub_type_id');
			$data['policy_number'] = $this->input->post('policy_number');
			$data['is_optional'] = intval($this->input->post('is_optional'));
			$data['is_combo'] = intval($this->input->post('is_combo'));
			$data['isactive'] = 1;
			$data['pdf_type'] = $this->input->post('pdf_type');
			$data['insurer_id'] = $this->input->post('insurer_id');
			$data['policy_start_date'] = $this->input->post('policyStartDate');
			$data['policy_end_date'] = $this->input->post('policyEndDate');
			$data['max_member_count'] = $this->input->post('max_member_count');
			$sitype = $this->input->post('sitype');
			$sibasis = $this->input->post('sibasis');
			if ($sibasis != 1) {
				$exceldata = json_decode($this->input->post('exceldata'));
			} else {
				$sum_insured_opt = explode(',', $this->input->post('sum_insured_opt'));
				$premium_opt = explode(',', $this->input->post('premium_opt'));
				$tax_opt = explode(',', $this->input->post('tax_opt'));
			}
			$members = explode(',', $this->input->post('members'));
			$minages = explode(',', $this->input->post('minage'));
			$maxages = explode(',', $this->input->post('maxage'));

			$result = $this->apimodel->updateRecordarr('master_policy', $data, array('policy_id' => $policy_sub_type_id));

			if ($result) {
				$i = 0;
				$data2 = array();
				for ($i = 0; $i < count($members); $i++) {
					$data2[$i]['master_policy_id'] = $policy_sub_type_id;
					$data2[$i]['member_type_id'] = $members[$i];
					$data2[$i]['member_min_age'] = $minages[$i];
					$data2[$i]['member_max_age'] = $maxages[$i];
				}
				$this->apimodel->deletempfc($policy_sub_type_id);
				$this->apimodel->insertBatchData('master_policy_family_construct', $data2);
				$this->apimodel->deletemppb($policy_sub_type_id);
				$data3 = array('master_policy_id' => $policy_sub_type_id, 'si_premium_basis_id' => $sibasis);
				$this->apimodel->insertData('master_policy_premium_basis_mapping', $data3);
				$this->apimodel->deletempsi($policy_sub_type_id);
				$data4 = array('master_policy_id' => $policy_sub_type_id, 'suminsured_type_id' => $sitype);
				$this->apimodel->insertData('master_policy_si_type_mapping', $data4);
				if ($sibasis != 1) {
					$this->apimodel->deletempp($policy_sub_type_id);
					$this->apimodel->insertBatchData('master_policy_premium', $exceldata);
				} else {
					$this->apimodel->deletempp($policy_sub_type_id);
					$j = 0;
					$data5 = array();
					for ($j = 0; $j < count($sum_insured_opt); $j++) {
						$data5[$j]['master_policy_id'] = $policy_sub_type_id;
						$data5[$j]['sum_insured'] = $sum_insured_opt[$j];
						$data5[$j]['premium_rate'] = $premium_opt[$j];
						$data5[$j]['is_taxable'] = $tax_opt[$j];
					}
					$this->apimodel->insertBatchData('master_policy_premium', $data5);
				}
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record updated successfully.'), "Data" => $plan_id));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	function checkplanname()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$plan = $this->input->post('plan');
			$id = $this->input->post('id');
			if (empty($id)) {
				$name = $this->apimodel->checkplanname($plan);
			} else {
				$name = $this->apimodel->checkplanname($plan, $id);
			}
			if (count($name) > 0) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Plan name already exist.')));
				exit;
			} else {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'No Match Found.')));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	function checkpolicynumber()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$policy = $this->input->post('policy');
			$id = $this->input->post('id');
			if (empty($id)) {
				$name = $this->apimodel->checkpolicynumber($policy);
			} else {
				$name = $this->apimodel->checkpolicynumber($policy, $id);
			}
			if (count($name) > 0) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Policy Number already exist.')));
				exit;
			} else {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'No Match Found.')));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
	function getproductDetails()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$utoken = $this->input->post('utoken');
			$id = $this->input->post('id');
			$data['plan_id'] = $id;
			$data['details'] = $this->apimodel->getProductDetails($id);
			$data['sitypes'] = $this->apimodel->getSiType();
			$data['insurers'] = $this->apimodel->getInsurer();
			$data['sipremiumbasis'] = $this->apimodel->getSiPremiumBasis();
			$data['members'] = $this->apimodel->getMembers();
            $data['payment_mode_id'] = $this->db->query("select payment_mode_id from plan_payment_mode where payment_mode_id=4 AND master_plan_id=".$id)->row()->payment_mode_id;
			$data['adult_members'] = $data['child_members'] = [];
			foreach ($data['members'] as $member) {
				if ($member->is_adult == "Y") {
					$data['adult_members'][] = $member;
				} else {
					$data['child_members'][] = $member;
				}
			}
			echo json_encode($data);
		} else {
			echo $checkToken;
		}
	}

	function addNewFeatureView()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$data = array();
			$data['members'] = $this->apimodel->getMembers();
			$data['creditors'] = $this->apimodel->getCreditors();
			$data['features'] = $this->apimodel->getFeatures();
			echo json_encode($data);
		} else {
			echo $checkToken;
		}
	}

	function addNewProductView()
	{
	    //echo 123;die;
	/*	$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {*/
        ini_set('display_errors', 1);
			$data = array();
			$data['members'] = $this->apimodel->getMembers();
			//print_r($data['members']);exit;
        $data['payment_modes'] = $this->apimodel->getPaymentModes();
       // print_r($data['payment_modes']);exit;
			//$data['creditors'] = $this->apimodel->getCreditors();

        	$data['payment_workflows'] = $this->apimodel->getPaymentWorkflow();
          $data['policytypes'] = $this->apimodel->getPolicyType();
        /*   $data['policysubtypes'] = $this->apimodel->getPolicySubType();*/
			echo json_encode($data);
		/*} else {
			echo $checkToken;
		}*/
	}
	function editproduct()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$data = array();
			$data['plan_id'] = $_POST['id'];
			$data['details'] = $this->apimodel->getProductDetails($_POST['id']);
			$data['planpayments'] = $this->apimodel->getPlanPayments($_POST['id']);
			//get plan payment modes with workflow
			$data['planpayments_modes'] = $this->apimodel->getSortedData("payment_mode_id, workflow_id", "plan_payment_mode", "master_plan_id = '" . $_POST['id'] . "'");

			$data['members'] = $this->apimodel->getMembers();
			$data['creditors'] = $this->apimodel->getCreditors();
			$data['payment_modes'] = $this->apimodel->getPaymentModes();
			$data['payment_workflows'] = $this->apimodel->getPaymentWorkflow();
			$data['policytypes'] = $this->apimodel->getPolicyType();
			$data['policysubtypes'] = $this->apimodel->getPolicySubType();

			if (isset($data['details'][0]->policy_id)) {
				$data['master_policy_family_construct'] = $this->apimodel->getPolicyFamilyConstruct($data['details'][0]->policy_id);
				$data['sitypes'] = $this->apimodel->getSiType();
				// $data['policy_premium'] = $this->apimodel->getPolicyPremiumBasis($data['details'][0]->policy_id);
				//$data['policydetails'] = $this->apimodel->getProductDetails($_POST['id'], $data['details'][0]->policy_id); // new added
			}

			echo json_encode($data);
		} else {
			echo $checkToken;
		}
	}
	function addEditPolicyProposalCustDetails()
	{
		$checkToken = $this->verify_request($this->input->post('utoken'));

		if (empty($checkToken->username)) {
			echo $checkToken;
			exit;
		}

		$data = array();
		$customer_id = trim($this->input->post('customer_id'));

		$data['salutation'] = trim($this->input->post("salutation"));
		$data['first_name']  = trim($this->input->post('firstname'));
		$data['middle_name']  = trim($this->input->post('middlename'));
		$data['last_name']  = trim($this->input->post('lastname'));
		$data['dob'] = date('Y-m-d H:i:s', strtotime(trim($this->input->post('dob'))));
		$data['gender']  = trim($this->input->post('gender1'));
		$data['address_line1'] = trim($this->input->post('address_line1'));
		$data['address_line2'] = trim($this->input->post('address_line2'));
		$data['address_line3'] = trim($this->input->post('address_line3'));
		$data['mobile_no2'] = trim($this->input->post('mobile_no2'));
		$data['pincode'] = trim($this->input->post('pin_code'));
		$data['city'] = trim($this->input->post('city'));
		$data['state'] = trim($this->input->post('state'));
		$data['full_name'] = $data['first_name'] . " " . $data['middle_name'] . " " . $data['last_name'];

		if (!empty($customer_id)) {
			$data['updatedby'] = $this->input->post('login_user_id');
			$data['updatedon'] = date("Y-m-d H:i:s");
			insert_application_log($this->input->post('lead_id'), "customer_updated", json_encode($data), json_encode(array("response" => "Customer Record Updated")), $this->input->post('login_user_id'));
			$this->apimodel->updateRecordarr('master_customer', $data, array('customer_id' => $customer_id));
		} else {
			$data['lead_id'] = trim($this->input->post("lead_id"));
			$data['createdon'] = date("Y-m-d H:i:s");
			insert_application_log($this->input->post('lead_id'), "customer_created", json_encode($data), json_encode(array("response" => "Customer Record Created")), $this->input->post('login_user_id'));
			$this->apimodel->insertData('master_customer', $data);
		}

		$from = new DateTime($data['dob']);
		$to   = new DateTime('today');
		$age = $from->diff($to)->y;

		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Customer details saved.', 'self_age' => $age, 'customer_id' => $this->db->insert_id())));
		exit;
	}

	function addEditPolicyProposalCoapplicantDetails()
	{
		$checkToken = $this->verify_request($_POST['utoken']);
		if (!empty($checkToken->username)) {
			$data = array();
			$customer_id = $this->input->post('customer_id');
			$lead_id = trim($this->input->post('lead_id'));
			$trace_id = trim($this->input->post('trace_id'));
			$plan_id = trim($this->input->post('plan_id'));
			$data['salutation'] = trim($this->input->post('salutation'));
			$data['first_name'] = trim($this->input->post('firstname'));
			$data['last_name'] = trim($this->input->post('lastname'));
			$data['middle_name'] = $this->input->post('middlename');
			$data['full_name'] = $data['first_name'] . " " . $data['middle_name'] . " " . $data['last_name'];
			$data['dob'] = date('Y-m-d H:i:s', strtotime(trim($this->input->post('dob'))));
			$data['mobile_no'] = trim($this->input->post('mob_no'));
			$data['gender'] = trim($this->input->post('gender1'));
			$data['address_line1'] = trim($this->input->post('address_line1'));
			$data['address_line2'] = trim($this->input->post('address_line2'));
			$data['address_line3'] = trim($this->input->post('address_line3'));
			$data['mobile_no2'] = trim($this->input->post('mobile_no2'));
			$data['pincode'] = trim($this->input->post('pin_code'));
			$data['city'] = trim($this->input->post('city'));
			$data['state'] = trim($this->input->post('state'));
			$data['email_id'] = trim($this->input->post('email_id'));

			$from = new DateTime(trim($this->input->post('dob')));
			$to   = new DateTime('today');
			$age = $from->diff($to)->y;

			if (!empty($customer_id)) {

				$data['updatedby'] = trim($this->input->post('user_id'));
				$data['updatedon'] = date("Y-m-d H:i:s");
				insert_application_log($this->input->post('lead_id'), "coapplicant_update", json_encode($data), json_encode(array("response" => "Coapplicant Record Updated")), $this->input->post('user_id'));
				$this->apimodel->updateRecordarr('master_customer', $data, array('customer_id' => $customer_id));
				echo json_encode(array("status_code" => "200", "Data" => [], "Metadata" => array("Message" => 'Customer Details Saved.', 'self_age' => $age, "Data" => "")));
				exit;
			} else {
				$data['lead_id'] = $lead_id;
				$data['createdon'] = date("Y-m-d H:i:s");
				$data['createdby'] = trim($this->input->post('user_id'));

				insert_application_log($this->input->post('lead_id'), "coapplicant_created", json_encode($data), json_encode(array("response" => "Coapplicant Record Created")), $this->input->post('user_id'));

				$customer_insert_id = $this->apimodel->insertData('master_customer', $data, 1);

				$data2 = array('lead_id' => $lead_id, 'plan_id' => $plan_id, 'customer_id' => $customer_insert_id, 'trace_id' => $trace_id, 'created_by' => $this->input->post('user_id'));
				insert_application_log($this->input->post('lead_id'), "proposal_created", json_encode($data2), json_encode(array("response" => "Proposal Record Created")), $this->input->post('user_id'));

				$proposal_details_id = $this->apimodel->insertData('proposal_details', $data2, 1);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Customer Details Saved.', 'self_age' => $age), "Data" => array('cust_id' => $customer_insert_id, 'prop_id' => $proposal_details_id)));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}



	public function coi_download()
	{
		echo json_encode($this->apimodel->coi_download_m());
	}

	public function check_error_data()
	{
		echo json_encode($this->apimodel->check_error_data_m());
	}

	function addEditPolicyProposalNomineeDetails($data = null)
	{

        if(isset($_POST['utoken'])){

            $checkToken = $this->verify_request($_POST['utoken']);
        }

        $created_by=$this->input->post('login_user_id');
	    if($data != null){
	        $_POST=$data;
            $checkToken->username=$_POST['user_id'];
            $created_by=$_POST['created_by'];
        }


		if(isset($_POST['source']) && $_POST['source'] == 'customer'){
			
			$_POST['lead_id'] = encrypt_decrypt_password($_POST['lead_id'], 'D');
			$_POST['trace_id'] = encrypt_decrypt_password($_POST['trace_id'], 'D');
			$_POST['customer_id'] = encrypt_decrypt_password($_POST['customer_id'], 'D');
			$_POST['plan_id'] = encrypt_decrypt_password($_POST['plan_id'], 'D');
			$_POST['proposal_details_id'] = encrypt_decrypt_password($_POST['proposal_details_id'], 'D');
		}

		if ((isset($checkToken) && !empty($checkToken->username)) || (isset($_POST['source']) && $_POST['source'] == 'customer')) {

			$data = array();
			$lead_id = $_POST['lead_id'];
			$plan_id = $_POST['plan_id'];
			$customer_id = $_POST['customer_id'];
			$trace_id = $_POST['trace_id'];
			$proposal_details_id = $_POST['proposal_details_id'];

			$data['nominee_email'] = (!empty($_POST['nominee_email'])) ? $_POST['nominee_email'] : '';
			$data['nominee_contact'] = (!empty($_POST['nominee_contact'])) ? $_POST['nominee_contact'] : '';
			$data['nominee_dob'] = (!empty($_POST['nominee_dob'])) ? date('Y-m-d', strtotime($_POST['nominee_dob'])) : '';
			$data['nominee_gender'] = (!empty($_POST['nominee_gender'])) ? $_POST['nominee_gender'] : '';
			$data['nominee_salutation'] = (!empty($_POST['nominee_salutation'])) ? $_POST['nominee_salutation'] : '';
			$data['nominee_last_name'] = (!empty($_POST['nominee_last_name'])) ? $_POST['nominee_last_name'] : '';
			$data['nominee_first_name'] = (!empty($_POST['nominee_first_name'])) ? $_POST['nominee_first_name'] : '';
			$data['nominee_relation'] = (!empty($_POST['nominee_relation'])) ? $_POST['nominee_relation'] : '';
			
			$data['created_by'] =  $this->input->post('user_id'); 
			$data['updated_on'] = date("Y-m-d H:i:s");
		//	$data2 = array('lead_id' => $lead_id, 'plan_id' => $plan_id, 'customer_id' => $customer_insert_id, 'trace_id' => $trace_id, 'created_by' => $this->input->post('user_id'));
			//insert_application_log($this->input->post('lead_id'), "proposal_created", json_encode($data2), json_encode(array("response" => "Proposal Record Created")), $this->input->post('user_id'));
$sel = $this->db->query("select * from proposal_details where lead_id = '$lead_id'")->row_array();
if(!empty($sel))
{

	$this->apimodel->updateRecordarr('proposal_details', $data, array('lead_id' => $lead_id));
		insert_application_log($this->input->post('lead_id'), "nominee_saved", json_encode($data), json_encode(array("response" => "Nominee Saved")),$created_by );

}else{
	$data['lead_id'] = $lead_id; 
			$data['plan_id'] = $plan_id; 
			$data['customer_id'] = $customer_id; 
			$data['trace_id'] = $trace_id; 
	$proposal_details_id = $this->apimodel->insertData('proposal_details', $data, 1);

}
		
if($_POST['is_api'] == 1){
    return array("status_code" => "200", "Metadata" => array("Message" => 'Nominee details saved.'));
}else{
    echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Nominee details saved.')));
    exit;
}

		} else {
			echo $checkToken;
		}
	}

	public function payment_redirection()
	{

		if (isset($_POST['lead_id_encrypt']) && $_POST['lead_id_encrypt'] != '') {

			$lead_id_encrypt = $_POST['lead_id_encrypt'];
			$lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');
			$queryData = $this->db->query("SELECT mp.plan_name,ms.full_name,mp.payment_url,ed.trace_id,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.tax_amount) as premium,ppd.proposal_status,ed.plan_id FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p,proposal_payment_details as ppd WHERE mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id = ppd.lead_id AND ed.lead_id=" . $lead_id)->row_array();

			if (!empty($queryData)) {

				if ($queryData['proposal_status'] != 'PaymentPending') {
					//redirect(base_url("api2/payment_success_view/" . $lead_id_encrypt));
					echo json_encode($this->payment_success($lead_id_encrypt));
				} else {

					$premiumAmount = $queryData['premium'];
					$email = $queryData['email_id'];
					$mobileNumber = $queryData['mobile_no'];
					$customer_name = $queryData['full_name'];
					$payment_url = $queryData['payment_url'];
					$Source = "ABC";
					$Vertical = "ABCGRP";
					$PaymentMode = "PO";
					$ReturnURL = FRONT_URL . "paymentgatewayredirect/" . $lead_id_encrypt;
					$UniqueIdentifier = "LEADID";
					$UniqueIdentifierValue = $queryData['trace_id'];
					$ProductInfo = $queryData['plan_name']; //"Creditor Portal";

					$doQuickQuote = $this->apimodel->doQuickQuote($lead_id);

					if (isset($doQuickQuote['Status']) && $doQuickQuote['Status'] == "Success") {

						$CKS_data = $Source . "|" . $Vertical . "|" . $PaymentMode . "|" . $ReturnURL . "|" . $UniqueIdentifier . "|" . $UniqueIdentifierValue . "|" . $customer_name . "|" . $email . "|" . substr(trim($mobileNumber), -10) . "|" . round($premiumAmount, 2) . "|" . $ProductInfo . "|" . $this->hash_key;

						$CKS_value = hash($this->hashMethod, $CKS_data);

						$manDateInfo = array(
							//"ApplicationNo"=> $lead_data['msg'],
							"ApplicationNo" => $queryData['trace_id'],
							"AccountHolderName" => $customer_name,
							"BankName" => 'Axis Bank',
							"AccountNumber" => null,
							"AccountType" => null,
							"BankBranchName" => null,
							"MICRNo" => null,
							"IFSC_Code" => null,
							"Frequency" => "ANNUALLY"
						);

						$dataPost = array(
							"signature" => $CKS_value,
							"Source" => $Source,
							"Vertical" => $Vertical,
							"PaymentMode" => $PaymentMode,
							"ReturnURL" => $ReturnURL,
							"UniqueIdentifier" => $UniqueIdentifier,
							"UniqueIdentifierValue" => $UniqueIdentifierValue,
							"CustomerName" => $customer_name,
							"Email" => $email,
							"PhoneNo" => substr(trim($mobileNumber), -10),
							"FinalPremium" => round($premiumAmount, 2),
							"ProductInfo" => $ProductInfo,
							//"Additionalfield1"=> "",
							"MandateInfo" => $manDateInfo
						);

						$data_string = json_encode($dataPost);

						$encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
						$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

						//$url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
						$data = array('REQUEST' => $encrypted);

						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $payment_url);
						curl_setopt($c, CURLOPT_POST, 0);
						curl_setopt($c, CURLOPT_POSTFIELDS, $data);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

						$result = curl_exec($c);
						curl_close($c);
						$result = json_decode($result, true);

						$request_arr = ["lead_id" => $lead_id, "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result), "product_id" => $queryData['plan_id'], "type" => "payment_request_post"];

						$this->db->insert('logs_docs', $request_arr);

						//Application log entries
						$this->db->insert("application_logs", [
							"lead_id" => $lead_id,
							"action" => "payment_request_post",
							"request_data" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted,
							"response_data" => json_encode($result),
							"created_on" => date("Y-m-d H:i:s")
						]);

						if ($result && $result['Status']) {

							$query_check = $this
								->db
								->query("select * from proposal_payment_redirection where lead_id='" . $lead_id . "'")->row_array();

							if (empty($query_check)) {
								$data_arr = ["lead_id" => $lead_id, "txt_id" => 1, "pg_type" => "RazorPay"];
								$this
									->db
									->insert("proposal_payment_redirection", $data_arr);
							} else {
								$update_arr = ["cron_count" => 0];
								$this
									->db
									->where("lead_id", $lead_id);
								$this
									->db
									->update("proposal_payment_redirection", $update_arr);
							}

							//echo "WELCOME To ABHI";
							//redirect($result['PaymentLink']);

							echo json_encode(['success' => 2, 'faliure' => '', 'error' => '', 'data' => $result['PaymentLink']]);
						} else {
							if ($result['ErrorList'][0]['ErrorCode'] == 'E005') {
								$check_pg = $this->apimodel->real_pg_check($lead_id);
								if ($check_pg) {
									//redirect(base_url("api2/payment_success_view/" . $lead_id_encrypt));
									echo json_encode($this->payment_success($lead_id_encrypt));
								} else {
									echo json_encode(['success' => '', 'faliure' => 2, 'error' => "Error in Enquiry API", 'data' => '']);
								}
							} else if (isset($result['ErrorList'][0]['Message'])) {
								echo json_encode(['success' => '', 'faliure' => 2, 'error' => $result['ErrorList'][0]['Message'], 'data' => '']);
							} else {

								echo json_encode(['success' => '', 'faliure' => 2, 'error' => 'No response from payment gateway, please try again after sometime. In case of any queries please call our helpline 1800-270-7000 or email us at care.healthinsurance@adityabirlacapital.com', 'data' => '']);
							}
						}
					} else {

						//redirect(base_url("api2/payment_error_view/" . $lead_id_encrypt));
						echo $this->payment_error($lead_id_encrypt);
					}
				}
			} else {
				echo json_encode(['success' => '', 'faliure' => 2, 'error' => "Error in proposal data", 'data' => '']);
				//echo "Payment link has been expired, Please get in touch with your Branch RM";
			}
		}
	}


	//Payment Redirection
	/*public function payment_redirection($lead_id_encrypt)
	{
		$lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

		if ($lead_id) {

			/*to calculate premium if multiple products are selected*/
	/*$queryData = $this->db->query("SELECT ms.full_name,mp.payment_url,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.premium_amount) as premium,ppd.proposal_status,ed.plan_id FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p,proposal_payment_details as ppd WHERE mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id = ppd.lead_id AND ed.lead_id=" . $lead_id)->row_array();
			//print_pre($query);exit;
			if (!empty($queryData)) {

				if ($queryData['proposal_status'] != 'PaymentPending') {
					redirect(base_url("api2/payment_success_view/" . $lead_id_encrypt));
				} else {

					$premiumAmount = $queryData['premium'];
					$email = $queryData['email_id'];
					$mobileNumber = $queryData['mobile_no'];
					$customer_name = $queryData['full_name'];
					$payment_url = $queryData['payment_url'];
					$Source = "ABC";
					$Vertical = "ABCGRP";
					$PaymentMode = "PO";
					$ReturnURL = base_url("api2/payment_success_view/" . $lead_id_encrypt);
					$UniqueIdentifier = "LEADID";
					$UniqueIdentifierValue = $lead_id;
					$ProductInfo = "Creditor Portal";

					$doQuickQuote = $this->apimodel->doQuickQuote($lead_id);

					if (isset($doQuickQuote['Status']) && $doQuickQuote['Status'] == "Success") {

						$CKS_data = $Source . "|" . $Vertical . "|" . $PaymentMode . "|" . $ReturnURL . "|" . $UniqueIdentifier . "|" . $UniqueIdentifierValue . "|" . $customer_name . "|" . $email . "|" . substr(trim($mobileNumber), -10) . "|" . round($premiumAmount, 2) . "|" . $ProductInfo . "|" . $this->hash_key;

						$CKS_value = hash($this->hashMethod, $CKS_data);

						$manDateInfo = array(
							//"ApplicationNo"=> $lead_data['msg'],
							"ApplicationNo" => $lead_id,
							"AccountHolderName" => $customer_name,
							"BankName" => 'Axis Bank',
							"AccountNumber" => null,
							"AccountType" => null,
							"BankBranchName" => null,
							"MICRNo" => null,
							"IFSC_Code" => null,
							"Frequency" => "ANNUALLY"
						);

						$dataPost = array(
							"signature" => $CKS_value,
							"Source" => $Source,
							"Vertical" => $Vertical,
							"PaymentMode" => $PaymentMode,
							"ReturnURL" => $ReturnURL,
							"UniqueIdentifier" => $UniqueIdentifier,
							"UniqueIdentifierValue" => $UniqueIdentifierValue,
							"CustomerName" => $customer_name,
							"Email" => $email,
							"PhoneNo" => substr(trim($mobileNumber), -10),
							"FinalPremium" => round($premiumAmount, 2),
							"ProductInfo" => $ProductInfo,
							//"Additionalfield1"=> "",
							"MandateInfo" => $manDateInfo
						);

						$data_string = json_encode($dataPost);

						$encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
						$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

						//$url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
						$data = array('REQUEST' => $encrypted);

						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $payment_url);
						curl_setopt($c, CURLOPT_POST, 0);
						curl_setopt($c, CURLOPT_POSTFIELDS, $data);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

						$result = curl_exec($c);
						curl_close($c);
						$result = json_decode($result, true);

						$request_arr = ["lead_id" => $lead_id, "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result), "product_id" => $queryData['plan_id'], "type" => "payment_request_post"];

						$this->db->insert('logs_docs', $request_arr);

						if ($result && $result['Status']) {

							$query_check = $this
								->db
								->query("select * from proposal_payment_redirection where lead_id='" . $lead_id . "'")->row_array();

							if (empty($query_check)) {
								$data_arr = ["lead_id" => $lead_id, "txt_id" => 1, "pg_type" => "RazorPay"];
								$this
									->db
									->insert("proposal_payment_redirection", $data_arr);
							} else {
								$update_arr = ["cron_count" => 0];
								$this
									->db
									->where("lead_id", $lead_id);
								$this
									->db
									->update("proposal_payment_redirection", $update_arr);
							}

							//echo "WELCOME To ABHI";
							redirect($result['PaymentLink']);
						} else {
							if ($result['ErrorList'][0]['ErrorCode'] == 'E005') {
								$check_pg = $this->apimodel->real_pg_check($lead_id);
								if ($check_pg) {
									redirect(base_url("api2/payment_success_view/" . $lead_id_encrypt));
								} else {
									echo "Error in Enquiry API";
								}
							} else {
								echo $result['ErrorList'][0]['Message'];
							}
						}
					} else {

						redirect(base_url("api2/payment_error_view/" . $lead_id_encrypt));
					}
				}
			} else {
				echo "Error in proposal data";
				//echo "Payment link has been expired, Please get in touch with your Branch RM";
			}
		}
	}*/
	// public function payment_redirection()
	// {
	// 	$emp_id = $_POST['lead_id'];

	// 	if ($emp_id) {

	// 		/*to calculate premium if multiple products are selected*/
	// 		$queryData = $this->db->query("SELECT ms.full_name,mp.payment_url,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.premium_amount) as premium,epd.status FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p WHERE mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id=" . $emp_id)->row_array();

	// 		//echo "<pre>";print_r($queryData);exit;
	// 		$premiumAmount = $queryData['premium'];
	// 		$leadId = $queryData['lead_id'];
	// 		$email = $queryData['email_id'];
	// 		$mobileNumber = $queryData['mobile_no'];
	// 		$customer_name = $queryData['full_name'];
	// 		$payment_url = $queryData['payment_url'];
	// 		$status = $queryData['status'];
	// 		/*end of process

	// 		$query = $this
	// 		->db
	// 		->query("SELECT ed.acc_type,ed.acc_no,ed.customer_name,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,p.premium,mpst.payment_url,p.status,p.id as proposal_id,mpst.payu_info_url,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id=".$emp_id." order by p.policy_detail_id desc")->row_array();*/
	// 		//print_pre($query);exit;
	// 		if (!empty($query)) {

	// 			if ($status == 'Approved') {
	// 				redirect(base_url("payment_success_view_call_abc/" . $emp_id));
	// 			} else {

	// 				$lead_data = $this->get_all_quote_call($emp_id);
	// 				//print_r($lead_data);exit;

	// 				if ($lead_data) {

	// 					$CKS_data = "ABC|ABCGRP|PO|" . base_url("payment_success_view_call_abc/" . $emp_id) . "|LEADID|" . $leadId . "|" . $customer_name . "|" . $email . "|" . substr(trim($mobileNumber), -10) . "|" . round($premiumAmount, 2) . "|ABC_" . $leadId . "|" . $this->hash_key;

	// 					$CKS_value = hash($this->hashMethod, $CKS_data);

	// 					$bank_data = json_decode($query['json_qote'], true);

	// 					$manDateInfo = array(
	// 						//"ApplicationNo"=> $lead_data['msg'],
	// 						"ApplicationNo" => $leadId,
	// 						"AccountHolderName" => $customer_name,
	// 						"BankName" => ($bank_data['AXISBANKACCOUNT'] == 'Y') ? 'Axis Bank' : 'Other',
	// 						"AccountNumber" => empty($bank_data['ACCOUNTNUMBER']) ? '' : $bank_data['ACCOUNTNUMBER'],
	// 						"AccountType" => null,
	// 						"BankBranchName" => empty($bank_data['BRANCH_NAME']) ? '' : $bank_data['BRANCH_NAME'],
	// 						"MICRNo" => null,
	// 						"IFSC_Code" => empty($bank_data['IFSCCODE']) ? '' : $bank_data['IFSCCODE'],
	// 						"Frequency" => "ANNUALLY"
	// 					);

	// 					$dataPost = array(
	// 						"signature" => $CKS_value,
	// 						"Source" => "ABC",
	// 						"Vertical" => "ABCGRP",
	// 						"PaymentMode" => "PO",
	// 						"ReturnURL" => base_url("payment_success_view_call_abc/" . $emp_id),
	// 						"UniqueIdentifier" => "LEADID",
	// 						"UniqueIdentifierValue" => $leadId,
	// 						"CustomerName" => $customer_name,
	// 						"Email" => $email,
	// 						"PhoneNo" => substr(trim($mobileNumber), -10),
	// 						"FinalPremium" => round($premiumAmount, 2),
	// 						"ProductInfo" => "ABC_" . $leadId,
	// 						//"Additionalfield1"=> "",
	// 						"MandateInfo" => $manDateInfo
	// 					);

	// 					$data_string = json_encode($dataPost);

	// 					$encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
	// 					$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

	// 					$url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
	// 					$data = array('REQUEST' => $encrypted);

	// 					$c = curl_init();
	// 					curl_setopt($c, CURLOPT_URL, $url);
	// 					curl_setopt($c, CURLOPT_POST, 0);
	// 					curl_setopt($c, CURLOPT_POSTFIELDS, $data);
	// 					curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
	// 					curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	// 					curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
	// 					curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

	// 					$result = curl_exec($c);
	// 					curl_close($c);
	// 					$result = json_decode($result, true);

	// 					$request_arr = ["lead_id" => $leadId, "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result), "product_id" => "ABC", "type" => "payment_request_post"];

	// 					$this->db->insert('logs_docs', $request_arr);

	// 					if ($result && $result['Status']) {
	// 						//echo "WELCOME To ABHI";
	// 						redirect($result['PaymentLink']);
	// 					} else {
	// 						if ($result['ErrorList'][0]['ErrorCode'] == 'E005') {
	// 							$check_pg = $this->apimodel->real_pg_check($leadId);
	// 							if ($check_pg) {
	// 								redirect(base_url("payment_success_view_call_abc/" . $emp_id));
	// 							} else {
	// 								echo "Error in Enquiry API";
	// 							}
	// 						} else {
	// 							echo $result['ErrorList'][0]['Message'];
	// 						}
	// 					}
	// 				} else {

	// 					redirect(base_url("/payment_error_view_call_abc/" . $emp_id . "/1"));
	// 				}
	// 			}
	// 		} else {
	// 			echo "Payment link has been expired, Please get in touch with your Branch RM";
	// 		}
	// 	}
	// }

	public function get_all_quote_call($id)
	{
		$policy_details = $this->apimodel->getdata("proposal_policy", "*", "lead_id='" . $id . "' ");
		//get primary customer
		$primary_customer = $this->db->get_where('lead_details', array('lead_id' => $id))->row()->primary_customer_id;
		//echo "<pre>";print_r($policy_details);exit;

		//Pass nominee details

		$count = 1;
		$maxcount = count($policy_details);
		$succ = true;
		foreach ($policy_details as $proposal) {
			$proposal_details = $this->apimodel->getdata("proposal_details", "*", "proposal_details_id='" . $proposal->proposal_details_id . "' ");
			//echo $proposal['master_policy_id'];exit;
			$quick_qoute = $this->apimodel->get_quote_data($proposal_details[0]['lead_id'], $primary_customer, $proposal['master_policy_id'], $proposal['proposal_policy_id'], $proposal_details, $proposal['policy_sub_type_id'], $proposal['sum_insured']);


			if ($quick_qoute['status'] == 'error') {
				return false;
				exit;
			}
		}
		if ($succ) {
			return true;
		}
	}
	public function payment_error($lead_id_encrypt = '')
	{
		if (isset($_POST['lead_id_encrypt'])) {

			$lead_id = encrypt_decrypt_password($_POST['lead_id_encrypt'], 'D');
		} else if ($lead_id_encrypt) {

			$lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');
		} else {

			return json_encode(['success' => '', 'faliure' => 2, 'error' => "Error in lead", 'data' => '']);
			exit;
		}

		$lead_arr = $this->db->query("select lead_id,email_id,trace_id from lead_details where lead_id = '$lead_id' ")->row_array();

		$data = $this->apimodel->check_error_data_m();

		return json_encode(['success' => '', 'faliure' => 1, 'error' => "", 'data' => array_merge($lead_arr, $data)]);
		//echo "error page work pending";exit;
		/*$this->load->view('template/customer_header.php');
		$this->load->view('api2/error_view.php', $lead_arr);
		$this->load->view('template/customer_footer.php');*/
	}
	// public function payment_error_view($lead_id)
	// {
	// 	//echo "payment_error_view called";exit();
	// 	//$emp_id = $this->emp_id;

	// 	$lead_arr = $this->db->query("select lead_id,email_id from lead_details where lead_id = '$lead_id' ")->row_array();
	// 	$lead_id = $lead_arr['lead_id'];
	// 	$email = $lead_arr['email_id'];
	// 	//echo "in";exit;
	// 	$this->load->abc_portal_template('payment_error_view', compact('lead_id', 'email'));
	// }

	public function payment_success($lead_id_encrypt = '')
	{
		if (isset($_POST['lead_id_encrypt'])) {

			$lead_id = encrypt_decrypt_password($_POST['lead_id_encrypt'], 'D');
		} else if ($lead_id_encrypt) {

			$lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');
		} else {

			return ['success' => '', 'faliure' => 2, 'error' => "Error in lead", 'data' => ''];
		}

		$encrypted = $this->input->post('RESPONSE');

		if ($encrypted) {
			$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
			$post_data = json_decode($decrypted, true);

			extract($post_data);

			if ($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR') {
				$TxStatus = "Success";
			}
		}

		$queryData = $this->db->query("SELECT ed.trace_id,ms.first_name,ms.last_name,ms.full_name,mp.payment_url,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.premium_amount) as premium,ppd.proposal_status,ed.plan_id,ed.status,ed.creditor_id, mp.plan_name, ed.mobile_no as lead_mobile, ed.email_id as lead_email, ed.createdby, ed.lan_id, cr.creaditor_name FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p,proposal_payment_details as ppd, master_ceditors as cr WHERE cr.creditor_id = mp.creditor_id AND mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id = ppd.lead_id AND ed.lead_id=" . $lead_id)->row_array();

		//echo $this->db->last_query();exit;
		if ($queryData) {

			if (isset($TxRefNo)) {
				$request_arr = ["lead_id" => $lead_id, "req" => $encrypted, "res" => $decrypted, "product_id" => $query['plan_id'], "type" => "payment_response_post"];
				$this->db->insert('logs_docs', $request_arr);

				//Application log entries
				$this->db->insert("application_logs", [
					"lead_id" => $lead_id,
					"action" => "payment_response_post",
					"request_data" => $encrypted,
					"response_data" => $decrypted,
					"created_on" => date("Y-m-d H:i:s")
				]);

				$arr = ["remark" => $TxMsg, "payment_status" => $TxStatus, "premium_with_tax" => $amount, "transaction_date" => $txnDateTime, "transaction_number" => $TxRefNo];

				$this->db->where("lead_id", $lead_id);
				$this->db->update("proposal_payment_details", $arr);

				//Commented by Bilal
				/*$this->db->where("lead_id", $lead_id);
				$this->db->update("lead_details", array('status' => "Approved"));*/


				//echo $this->db->last_query();exit;
			}

			if (isset($Registrationmode)) {
				$query_emandate = $this->db->query("select * from emandate_data where lead_id=" . $lead_id)->row_array();

				if ($EMandateStatus == 'MS') {
					$mandate_status = 'Success';
				} elseif ($EMandateStatus == 'MI') {
					$mandate_status = 'Emandate Pending';
				} elseif ($EMandateStatus == 'MR') {
					$mandate_status = 'Emandate Received';
				} elseif ($EMandateStatus == '') {
					$mandate_status = 'Emandate Pending';
				} else {
					$mandate_status = 'Fail';
				}

				if ($query_emandate > 0) {
					$arr = ["TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate))];

					$this->db->where("lead_id", $lead_id);
					$this->db->update("emandate_data", $arr);
				} else {
					$arr = ["lead_id" => $lead_id, "TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate))];

					$this->db->insert("emandate_data", $arr);
				}

				/*if ($mandate_status == 'Success')
                {
                    $this->obj_api->send_message($lead_id, 'success');
                }

                if ($mandate_status == 'Fail')
                {
                    $this->obj_api->send_message($lead_id, 'fail');
                }*/
			}

			$payment_data = $this->db->query("select payment_status from proposal_payment_details where lead_id='$lead_id'")->row_array();

			if ($payment_data['payment_status'] == 'Success') {

				if ($queryData['status'] == 'UW-Approval-Awaiting') {

					return ['success' => '', 'faliure' => 3, 'error' => '', 'data' => ['trace_id' => $queryData['trace_id']]];
				} 
				else if ($queryData['status'] == 'Customer-Payment-Received') {
					
					if (checkUWCase($queryData['lead_id'], $queryData['creditor_id'], $queryData['plan_id'])) {

						if ($this->apimodel->updateRecordarr('lead_details', ['status' => 'UW-Approval-Awaiting'], 'lead_id = ' . $queryData['lead_id'])) {

							//customer communication
							$alert_ids = ['A1666'];
							$comm_data['lead_id'] = $queryData['lead_id'];
							$comm_data['mobile_no'] = $queryData['lead_email'];
							$comm_data['email_id'] = $queryData['lead_mobile'];
							$comm_data['plan_id'] = $queryData['plan_id'];
							$comm_data['alerts'][] = $queryData['first_name'] . " " . $queryData['last_name'];
							$comm_data['alerts'][] = $queryData['plan_name'];
							$comm_data['alerts'][] = 4; //'Working days will go here';
							$comm_data['alerts'][] = $queryData['trace_id'];

							$com_response = triggerCommunication($alert_ids, $comm_data);

							if (ENV_BYPASSED || $com_response['status_code'] == 200) {

								$response = ['success' => true, 'msg' => 'Status changed to UW-Approval-Awaiting sent to customer'];

								insert_application_log($queryData['lead_id'], "uw_bucket_movement_issuance_customer", json_encode($comm_data), json_encode(array_merge($com_response, $response)), 0);
							}
							else{

								$response = ['success' => false, 'msg' => 'Something went wrong when sending communication link'];

								insert_application_log($queryData['lead_id'], "uw_bucket_movement_issuance_customer", json_encode($comm_data), json_encode(array_merge($com_response, $response)), 0);
							}

							//sm_communication

							$emp_details = $this->apimodel->getdata('master_employee', 'employee_full_name, email_id, mobile_number', 'employee_id = '.$queryData['createdby']);

							$comm_data = [];
							$alert_ids = ['A1667'];
							$comm_data['lead_id'] = $queryData['lead_id'];
							$comm_data['mobile_no'] = $emp_details[0]['mobile_number'];
							$comm_data['email_id'] = $emp_details[0]['email_id'];
							$comm_data['plan_id'] = $queryData['plan_id'];
							$comm_data['alerts'][] = $queryData['first_name'] . " " . $queryData['last_name'];
							$comm_data['alerts'][] = $queryData['plan_name'];
							$comm_data['alerts'][] = $emp_details[0]['employee_full_name'];
							$comm_data['alerts'][] = $queryData['trace_id'];
							$comm_data['alerts'][] = 4; //'Working days will go here';

							$com_response = triggerCommunication($alert_ids, $comm_data);

							insert_application_log($queryData['lead_id'], "uw_bucket_movement_issuance_agent", json_encode($comm_data), json_encode($com_response), 0);


							//uw_trigger

							$uw_user_data = $this->apimodel->getdata("master_employee", "employee_fname,employee_lname, employee_full_name, mobile_number, email_id", "role_id = 7");
							
							$uw_alert_ids = ['A1673'];
							$uw_data['lead_id'] = $queryData['lead_id'];
							$uw_data['plan_id'] = $queryData['plan_id'];
							$uw_data['alerts'][] = $queryData['plan_name'] ?? '';
							$uw_data['alerts'][] = $queryData['creaditor_name'];
							$uw_data['alerts'][] = $queryData['lan_id'];
							$uw_data['alerts'][] = date('d-m-Y');
							$uw_data['alerts'][] = ''; //remarks goes here

							foreach($uw_user_data as $uw_user){

								$uw_data['mobile_no'] = $uw_user['mobile_number'];
								$uw_data['email_id'] = $uw_user['email_id'];

								$uw_response = triggerCommunication($uw_alert_ids, $uw_data);

								insert_application_log($queryData['lead_id'], 'uw_bucket_movement_acceptance_uw', json_encode($uw_data), json_encode($uw_response), 0);
							}
						}

						return ['success' => '', 'faliure' => 3, 'error' => '', 'data' => ['trace_id' => $queryData['trace_id']]];
					}
				}

				$doFullQuote = $this->apimodel->doFullQuote($lead_id);

				if (isset($doFullQuote['Status']) && $doFullQuote['Status'] == "Success") {

					//$policy_data['coi_data'] = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number,lead_id FROM api_proposal_response WHERE lead_id = '$lead_id' GROUP BY lead_id")->row_array();

					//Danish: get COI with other details to display on thankyou page
					$policy_data['coi_data'] = $this->db->query("SELECT ar.certificate_number,ar.lead_id, s.code, pm.policy_member_first_name, pm.policy_member_last_name,f.member_type,l.trace_id
					FROM api_proposal_response AS ar, master_policy_sub_type AS s, proposal_policy_member_details AS pm,family_construct AS f, lead_details AS l
					WHERE ar.lead_id = '$lead_id' AND ar.policy_sub_type_id=s.policy_sub_type_id  AND ar.lead_id = l.lead_id
					AND (ar.customer_id = pm.customer_id AND ar.lead_id=pm.lead_id) AND pm.relation_with_proposal=f.id")->result();

					//echo "thank u page work pending";exit;
					/*$this->load->view('template/customer_header.php');
					$this->load->view('api2/thanku_page.php', $policy_data);
					$this->load->view('template/customer_footer.php');*/
					return ['success' => 1, 'faliure' => '', 'error' => '', 'data' => $policy_data];
				} else {
					//redirect(base_url("/api2/payment_error_view/" . $lead_id_encrypt));
					$lead_arr = $this->db->query("select lead_id,trace_id,email_id from lead_details where lead_id = '$lead_id' ")->row_array();
					$data = $this->apimodel->check_error_data_m();

					return ['success' => '', 'faliure' => 1, 'error' => 'error_view', 'data' => array_merge($lead_arr, $data)];
				}
			} else {
				//redirect(base_url("/api2/payment_error_view/" . $lead_id_encrypt));

				$lead_arr = $this->db->query("select lead_id,trace_id,email_id from lead_details where lead_id = '$lead_id' ")->row_array();
				$data = $this->apimodel->check_error_data_m();

				return ['success' => '', 'faliure' => 1, 'error' => 'error_view', 'data' => array_merge($lead_arr, $data)];
			}
		} else {
			return ['success' => '', 'faliure' => 2, 'error' => "Error in proposal", 'data' => ''];
			//echo "Payment link has been expired, Please get in touch with your Branch RM";
		}
	}

	/*public function payment_success_view($lead_id_encrypt)
	{
		$lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

		$encrypted = $this->input->post('RESPONSE');

		if ($encrypted) {
			$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
			$post_data = json_decode($decrypted, true);

			extract($post_data);

			if ($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR') {
				$TxStatus = "Success";
			}
		}

		$queryData = $this->db->query("SELECT ms.full_name,mp.payment_url,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.premium_amount) as premium,ppd.proposal_status,ed.plan_id FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p,proposal_payment_details as ppd WHERE mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id = ppd.lead_id AND ed.lead_id=" . $lead_id)->row_array();

		//echo $this->db->last_query();exit;
		if ($queryData) {

			if (isset($TxRefNo)) {
				$request_arr = ["lead_id" => $lead_id, "req" => $encrypted, "res" => $decrypted, "product_id" => $queryData['plan_id'], "type" => "payment_response_post"];
				$this->db->insert('logs_docs', $request_arr);

				$arr = ["remark" => $TxMsg, "payment_status" => $TxStatus, "premium_with_tax" => $amount, "transaction_date" => $txnDateTime, "transaction_number" => $TxRefNo];

				$this->db->where("lead_id", $lead_id);
				$this->db->update("proposal_payment_details", $arr);

				$this->db->where("lead_id", $lead_id);
				$this->db->update("lead_details", array('status' => "Approved"));
				//echo $this->db->last_query();exit;
			}

			if (isset($Registrationmode)) {
				$query_emandate = $this->db->query("select * from emandate_data where lead_id=" . $lead_id)->row_array();

				if ($EMandateStatus == 'MS') {
					$mandate_status = 'Success';
				} elseif ($EMandateStatus == 'MI') {
					$mandate_status = 'Emandate Pending';
				} elseif ($EMandateStatus == 'MR') {
					$mandate_status = 'Emandate Received';
				} elseif ($EMandateStatus == '') {
					$mandate_status = 'Emandate Pending';
				} else {
					$mandate_status = 'Fail';
				}

				if ($query_emandate > 0) {
					$arr = ["TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate))];

					$this->db->where("lead_id", $lead_id);
					$this->db->update("emandate_data", $arr);
				} else {
					$arr = ["lead_id" => $lead_id, "TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate))];

					$this->db->insert("emandate_data", $arr);
				}

				/*if ($mandate_status == 'Success')
                {
                    $this->obj_api->send_message($lead_id, 'success');
                }

                if ($mandate_status == 'Fail')
                {
                    $this->obj_api->send_message($lead_id, 'fail');
                }*/
	/*}

			$payment_data = $this->db->query("select payment_status from proposal_payment_details where lead_id='$lead_id'")->row_array();

			if ($payment_data['payment_status'] == 'Success') {
				$doFullQuote = $this->apimodel->doFullQuote($lead_id);

				if (isset($doFullQuote['Status']) && $doFullQuote['Status'] == "Success") {

					$policy_data['coi_data'] = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number,lead_id FROM api_proposal_response WHERE lead_id = '$lead_id' GROUP BY lead_id")->row_array();
					//echo "thank u page work pending";exit;
					$this->load->view('template/customer_header.php');
					$this->load->view('api2/thanku_page.php', $policy_data);
					$this->load->view('template/customer_footer.php');
				} else {
					redirect(base_url("/api2/payment_error_view/" . $lead_id_encrypt));
				}
			} else {
				redirect(base_url("/api2/payment_error_view/" . $lead_id_encrypt));
			}
		} else {
			echo "Error in proposal";
			//echo "Payment link has been expired, Please get in touch with your Branch RM";
		}
	}*/

	// public function payment_success_view($emp_id_encrypt)
	// {
	// 	if (!is_numeric($emp_id_encrypt)) {
	// 		$emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');
	// 	} else {
	// 		$emp_id = $emp_id_encrypt;
	// 	}
	// 	//echo $emp_id;exit;

	// 	$encrypted = $this->input->post('RESPONSE');

	// 	if ($encrypted) {
	// 		$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
	// 		$post_data = json_decode($decrypted, true);

	// 		extract($post_data);
	// 		//echo $TxMsg;exit;
	// 		if ($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR') {
	// 			$TxStatus = "success";
	// 			$TxMsg = "Approved";
	// 		}
	// 	}

	// 	$query = $this->db->query("SELECT ed.primary_customer_id,ed.lead_id,mpst.plan_name FROM employee_policy_detail AS epd,master_plan AS mpst,lead_details AS ed WHERE mpst.plan_id = ed.lead_id AND ed.lead_id='" . $emp_id . "'")->row_array();
	// 	/*echo $this->db->last_query();
	// 	print_pre($query);exit;*/
	// 	if ($query) {

	// 		if (isset($TxRefNo)) {
	// 			$request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted, "res" => $decrypted, "product_id" => "ABC", "type" => "payment_response_post"];
	// 			$this->apimodel->insert('logs_docs', $request_arr);

	// 			$arr = ["payment_remark" => $TxStatus, "status" => $TxMsg, "premium_amount" => $amount, "transaction_date" => $txnDateTime, "transaction_number" => $TxRefNo];

	// 			$proposal_ids = $this->db->query("select id as proposal_id from proposal_details where lead_id='" . $query['lead_id'] . "'")->result_array();

	// 			foreach ($proposal_ids as $query_val) {
	// 				$this->db->where("proposal_details_id", $query_val['proposal_id']);
	// 				$this->db->update("proposal_details", $arr);
	// 			}
	// 			$this->db->where("lead_id", $query['lead_id']);
	// 			$this->db->update("lead_details", array('status' => "Approved"));
	// 			//echo $this->db->last_query();exit;
	// 		}
	// 		//exit;
	// 		if (isset($EMandateStatus)) {
	// 			$query_emandate = $this->db->query("select * from emandate_data where lead_id=" . $query['lead_id'])->row_array();

	// 			if ($EMandateStatus == 'MS') {
	// 				$mandate_status = 'Success';

	// 				$this->obj_api->send_message($query['lead_id'], 'success');
	// 			} elseif ($EMandateStatus == 'MI') {
	// 				$mandate_status = 'Emandate Pending';
	// 			} elseif ($EMandateStatus == 'MR') {
	// 				$mandate_status = 'Emandate Received';
	// 			} else {
	// 				$mandate_status = 'Fail';

	// 				$this->obj_api->send_message($query['lead_id'], 'fail');
	// 			}

	// 			if ($query_emandate > 0) {

	// 				$arr = ["TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate))];

	// 				$this->db->where("lead_id", $query['lead_id']);
	// 				$this->db->update("emandate_data", $arr);
	// 			} else {

	// 				$arr = ["lead_id" => $query['lead_id'], "TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate))];

	// 				$this->db->insert("emandate_data", $arr);
	// 			}
	// 		}


	// 		if (isset($TxRefNo)) {
	// 			//echo $query['lead_id'];exit;
	// 			$check_result = $this->obj_api->policy_creation_call($query['lead_id']);
	// 			//echo $this->db->last_query();
	// 			// print_pre($check_result);exit;exit;
	// 			if ($check_result['Status'] == 'Success') {

	// 				$data_policy[0] = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();

	// 				$query_new = $this->db->query("select p.proposal_policy_id,ed.lead_id,p.premium from proposal_policy as p,lead_details as ed  where p.lead_id = ed.lead_id and ed.lead_id ='$emp_id'");
	// 				$data = $query_new->result_array();

	// 				$this->load->abc_portal_template("thankyou_view_abc", compact('data_policy', 'data', 'amount'));
	// 			} else {
	// 				//echo "in";exit;
	// 				redirect(base_url("/payment_error_view_call_abc/" . $emp_id));
	// 			}
	// 		} else {

	// 			$query_new = $this->db->query("select p.proposal_policy_id,ed.lead_id,p.premium from proposal_policy as p,lead_details as ed  where p.lead_id = ed.lead_id and ed.lead_id ='$emp_id'");

	// 			$this->load->abc_portal_template("thankyou_view_abc", compact('data', 'amount'));
	// 		}
	// 	} else {

	// 		echo "Payment link has been expired, Please get in touch with your Branch RM";
	// 	}
	// }

	//For login all users
	function userLogin()
	{

		//echo "here";exit;

		if (!empty($_POST) && isset($_POST)) {

			if (empty($_POST['username'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter username."), "Data" => NULL));
				exit;
			}

			if (!empty($_POST['password'])) {
				$password = md5($_POST['password']);
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter password."), "Data" => NULL));
				exit;
			}

			//echo "<pre>";print_r($_POST);exit;

			$condition = "i.user_name='" . $_POST['username'] . "' &&  i.employee_password='" . $password . "' ";

			$result_login = $this->apimodel->login_check($condition);
			$result_data = $result_login[0];


			$utoken = $result_data['employee_id'];
			$success_msg = "Login Successfull. ";

			if (is_array($result_login) && count($result_login) > 0) {

				//JWT
				/*$kunci = $this->config->item('jwtkey');
				$token['id'] = $result_data['employee_id'];  //From here
				//$token['username'] = $u;
				$date = new DateTime();
				$token['iat'] = $date->getTimestamp();
				$token['exp'] = $date->getTimestamp() + 60*60*5; //To here is to generate token
				$output['token'] = JWT::encode($token,$kunci ); //This is the output token
				*/

				//$token = generateToken(['username' => $result_data['employee_id']]);
				$date = new DateTime();
				$tokenData = array('username' => $result_data['employee_id'], 'iat' => $date->getTimestamp(), 'exp' => $date->getTimestamp() + 60 * 60 * 5);
				$token = AUTHORIZATION::generateToken($tokenData);

				//echo "<pre>";print_r($token);exit;

				$result_data['utoken'] = $token;
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => $success_msg), "Data" => $result_data));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Incorrect Username or Password."), "Data" => NULL));
				exit;
			}
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Hearder section empty."), "Data" => NULL));
			exit;
		}
	}

	/*
	Author : Jitendra Gamit
	Date : 19th Nov, 2020
	Purpose : delete from master plan 
	**/
	function delProduct()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_plan', $data, "plan_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	} // EO delProduct()

	function delFeature(){

		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('features_config', $data, "id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	/*
	Author : Jitendra Gamit
	Date : 16th Dec, 2020
	***/
	function proposalLeadSubmit()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = $this->input->post('lead_id');
			$trace_id = $this->input->post('trace_id');
			$customer_id = $this->input->post('customer_id');

			$data_lead = array();
			$data_lead['plan_id'] = $this->input->post('plan_id');
			$data_lead['loan_amt'] = $this->input->post('loan_amt');
			$data_lead['tenure'] = $this->input->post('tenure');
			$data_lead['updatedon'] = date("Y-m-d H:i:s");

			$data_policy = array();
			$data_policy['numbers_of_ci'] = $this->input->post('numbers_of_ci');
			$data_policy['sum_insured'] = $this->input->post('total_sum_insured');

			//$data_policy['adult_count'] = $this->input->post('adult_count');
			//$data_policy['child_count'] = $this->input->post('child_count');
			$data_policy['updated_at'] = date("Y-m-d H:i:s");

			# custome age count 
			#customer_id
			$arr_customer = $this->apimodel->getCustomerDetails($customer_id);
			$from = new DateTime($arr_customer[0]->dob);
			$to   = new DateTime('today');
			$customer_age = $from->diff($to)->y;;
			##


			$data = array();
			$data['deductable'] = $this->input->post('sum_insured5_2');

			$result1 = $this->apimodel->updateRecordarr('lead_details', $data_lead, array('lead_id' => $lead_id, 'trace_id' => $trace_id));

			# new 
			$arr_plan_details = $this->apimodel->getProductDetailsAll($data_lead['plan_id']);

			$i = 0;
			foreach ($arr_plan_details as $key) {

				if ($arr_plan_details[$i]->policy_sub_type_id == 1) {
					$data_policy['sum_insured'] = $_POST['sum_insured1'];
				}

				if ($arr_plan_details[$i]->policy_sub_type_id == 2) {
					$data_policy['sum_insured'] = $_POST['sum_insured2'];
				}

				if ($arr_plan_details[$i]->policy_sub_type_id == 3) {
					$data_policy['sum_insured'] = $_POST['sum_insured3'];
				}

				if ($arr_plan_details[$i]->policy_sub_type_id == 5) {
					$data_policy['sum_insured'] = $_POST['sum_insured5_1'];
				}

				if ($arr_plan_details[$i]->policy_sub_type_id == 6) {
					$data_policy['sum_insured'] = $_POST['sum_insured6'];
				}

				##### adult child count
				$arr_family_construct = $this->apimodel->getPolicyFamilyConstruct($arr_plan_details[$i]->policy_id);
				$child_count = 0;
				$adult_count = 0;
				foreach ($arr_family_construct as $key2 => $value2) {
					$arr_member_type_id = array(1, 2, 3, 4);
					if (in_array($arr_family_construct[$key2]->member_type_id, $arr_member_type_id)) {
						$adult_count++;
					} else {
						$child_count++;
					}
				}
				####

				# for basis 1
				/*
			if ($arr_plan_details[$i]->basis_id == 1) {
				$policy_id = $arr_plan_details[$i]->policy_id;
				$rate = $this->getpolicypremiumflat($policy_id, $data_policy['sum_insured']);
				
				if($rate != 0) {
				       $premium_amount = $rate['amount']; 
					   $tax = $rate['tax'];	
				}
			}
			
			# for basis 2
			if ($arr_plan_details[$i]->basis_id == 2) {
				$policy_id = $arr_plan_details[$i]->policy_id;
				$rate = $this->getpolicypremiumfamilyconstruct($policy_id, $data_policy['sum_insured'], $adult_count, $child_count);
				
				if($rate != 0) {
				       $premium_amount = $rate['amount']; 
					   $tax = $rate['tax'];	
				}
			}
			***/

				# for basis 3

				if ($arr_plan_details[$i]->basis_id == 3) {

					$policy_id = $arr_plan_details[$i]->policy_id;
					//$age = '18';
					$rate = $this->getpolicypremiumfamilyconstructage($this->input->post('proposal_id'), $policy_id, $data_policy['sum_insured'], $adult_count, $child_count, $customer_age);

					if ($rate != 0) {
						$premium_amount = $rate['amount'];
						$tax = $rate['tax'];
					}
				}

				/*
			# for basic 4
			if ($arr_plan_details[$i]->basis_id == 4) {
				if (isset($arr_plan_details[$i]->policy_id)) {
					$policy_id = $arr_plan_details[$i]->policy_id;
					$rate = $this->getpolicypremiummemberage($policy,$sum_insured,$age);
					
					if($rate != 0) {
						   $premium_amount = $rate['amount']; 
						   $tax = $rate['tax'];	
					}
				}
			}
			***/



				#####


				if (!empty($premium_amount)) {
					$data_policy['premium_amount'] = $premium_amount;
				}
				if (!empty($tax)) {
					$data_policy['tax_amount'] = $tax;
				}
				############


				$data_policy['adult_count'] = $adult_count;
				$data_policy['child_count'] = $child_count;
				$data_policy['trace_id'] = $this->input->post('trace_id');
				$data_policy['proposal_details_id'] = $this->input->post('proposal_id');
				$data_policy['is_combo'] = $arr_plan_details[$i]->is_combo;
				$data_policy['is_optional'] = $arr_plan_details[$i]->is_optional;
				$data_policy['pdf_type'] = isset($arr_plan_details[$i]->pdf_type) ? $arr_plan_details[$i]->pdf_type : '';
				$data_policy['policy_number'] = $arr_plan_details[$i]->policy_number;
				$data_policy['policy_sub_type_name'] = $arr_plan_details[$i]->policy_sub_type_name;
				$data_policy['policy_sub_type_id'] = $arr_plan_details[$i]->policy_sub_type_id;
				$data_policy['insurer_id'] = $arr_plan_details[$i]->insurer_id;

				# sum insured 
				##



				$getProposalPolicyLead = $this->apimodel->getProposalPolicyLead($lead_id, $arr_plan_details[$i]->policy_id);

				if (!empty($getProposalPolicyLead)) {
					$result2 = $this->apimodel->updateRecordarr('proposal_policy', $data_policy, array('lead_id' => $lead_id, 'master_policy_id' => $arr_plan_details[$i]->policy_id));
				} else {
					$data_policy['lead_id'] = $this->input->post('lead_id');
					$data_policy['master_policy_id'] = $arr_plan_details[$i]->policy_id;

					$insert_id = $this->apimodel->insertData('proposal_policy', $data_policy);
				}

				$i++;
			} // end foreach()	

			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated.')));

			//$this->apimodel->updateRecordarr('proposal_details',$data,array('proposal_details_id'=>$proposal_details_id));
			//echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated.')));
			exit;
		} else {
			echo $checkToken;
		}
	} // EO proposalLeadSubmit()

	private function validateAddUpdateMemberDetails($data_member, $age_in_days, $member_id, $policy_id_member_arr, $is_adult, $master_policy_sub_type_arr, $member_age_arr)
	{
		foreach ($policy_id_member_arr as $relation => $policy_id_arr) {

			foreach ($policy_id_arr as $policy_id_key => $policy_id_value) {

				if ($data_member['relation_with_proposal'] == $relation) {

					$max_age = $member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['max_age'] ?? '';
					$min_age = $member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['min_age'] ?? '';
					$min_age_in_days = $member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['min_age_in_days'] ?? '';

					/*if (isset($member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['max_age'])) {

						$max_age = $member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['max_age'];
					}

					if (isset($member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['min_age'])) {

						$min_age = $member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['min_age'];
					}

					if (isset($member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['member_min_age_days'])) {

						$min_age_in_days = $member_age_arr[$policy_id_value][$data_member['relation_with_proposal']]['member_min_age_days'];
					}*/

					if ($is_adult[$relation] == 'N') {

						if (isset($min_age_in_days) && $min_age_in_days != '') {

							if (!($min_age_in_days <= $age_in_days && $data_member['policy_member_age'] <= $max_age)) {

								$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Age should be between ' . $min_age_in_days . ' days to ' . $max_age . ' years for kids in ' . ucwords($master_policy_sub_type_arr[$policy_id_value]) . ' policy')));

								insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), $response, $data_member['created_by']);

								return $response;
								exit;
							}
						} else if ($min_age != '') {

							if (!($data_member['policy_member_age'] >= $min_age && $data_member['policy_member_age'] <= $max_age)) {

								$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Age should be between ' . $min_age . ' to ' . $max_age . ' for kids in ' . ucwords($master_policy_sub_type_arr[$policy_id_value]) . ' policy')));

								insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), $response, $data_member['created_by']);

								return $response;
								exit;
							}
						}
					} else if ($is_adult[$relation] == 'Y') {

						if (!($data_member['policy_member_age'] >= $min_age && $data_member['policy_member_age'] <= $max_age)) {

							$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Age should be between ' . $min_age . ' to ' . $max_age . ' for adults in ' . ucwords($master_policy_sub_type_arr[$policy_id_value]) . ' policy')));

							insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), $response, $data_member['created_by']);

							return $response;
							exit;
						}
					}
				}
			}
		}


		/*if ($data_member['relation_with_proposal'] == 1 || $data_member['relation_with_proposal'] == 2) {

			$max_age = $member_age_arr[$data_member['relation_with_proposal']]['max_age'];
			$min_age = $member_age_arr[$data_member['relation_with_proposal']]['min_age'];

			if (!($data_member['policy_member_age'] >= $min_age && $data_member['policy_member_age'] <= $max_age)) {

				$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Age should be between ' . $min_age . ' to ' . $max_age . ' for adults')));

				insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), $response, $data_member['created_by']);

				return $response;
				exit;
			}
		} else if ($data_member['relation_with_proposal'] == 5 || $data_member['relation_with_proposal'] == 6) {

			if ($data_member['policy_member_age'] == 0) {

				if (!($age_in_months >= 3)) {

					$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Age should be greater than eqaul to 3 months and less than 18 years for kids')));

					insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), $response, $data_member['created_by']);

					return $response;
					exit;
				}

				$data_member['policy_member_age_in_months'] = $age_in_months;
			} else if ($data_member['policy_member_age'] > 0) {

				if (!($data_member['policy_member_age'] > 0  && $data_member['policy_member_age'] < 18)) {

					$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Age should be greater than eqaul to 3 months and less than 18 years for kids')));

					insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), $response, $data_member['created_by']);

					return $response;
					exit;
				}
			}
		}*/

		if ($member_id == 0) {

			$data_member['created_at'] = date('Y-m-d H:i:s');
			$member_id = $this->apimodel->insertData('proposal_policy_member_details', $data_member, 1);
            $data = array(
                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'plan_id' => $plan_id,
                'cover' => $cover,
                'premium' => $val_imp[1],
                'policy_id' => $val_imp[0],
                'total_premium' => $total_premium
            );
            $result_arr = $this->db->insert('policy_member_plan_details', $data);
			insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), json_encode(['response' => 'Member Saved With ID ' . $member_id]), $data_member['created_by']);
		} else {

			$data_member['updated_by'] = $data_member['created_by'];
			$data_member['updated_at'] = date('Y-m-d H:i:s');
			$this->apimodel->update_proposal_policy_member_details($data_member, $member_id);

			insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_member), json_encode(['response' => 'Member Updaed On ID ' . $member_id]), $data_member['created_by']);
		}

		return $member_id;
	}

	public function deleteInsuredMemberDetails()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		$proposal_details_id = htmlspecialchars(strip_tags(trim($_POST['proposal_details_id'])));
		$lead_id = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));
		$customer_id = htmlspecialchars(strip_tags(trim($_POST['customer_id'])));
		$created_by = htmlspecialchars(strip_tags(trim($_POST['created_by'])));

		$member_id_arr = $this->apimodel->getdata('proposal_policy_member_details', 'member_id, relation_with_proposal', 'customer_id = ' . $customer_id . ' AND lead_id = ' . $lead_id);

		$member_id = $relation = [];

		if (!empty($member_id_arr)) {

			foreach ($member_id_arr as $key => $value) {

				$member_id[$value['member_id']] = $value['member_id'];
				$relation[$value['relation_with_proposal']] = $value['relation_with_proposal'];
			}
		}

		$proposal_details_arr = [];
		if (!empty($relation)) {

			$proposal_details_arr = $this->apimodel->getdata('proposal_details', 'proposal_details_id', 'customer_id = ' . $customer_id . ' AND lead_id = ' . $lead_id . ' AND proposal_details_id = ' . $proposal_details_id . ' AND nominee_relation IN (' . implode(",", $relation) . ')');
		}

		if (!empty($checkToken->username)) {

			$result_1 = $this->apimodel->delrecord_condition('proposal_policy', 'proposal_details_id = ' . $proposal_details_id . ' AND lead_id = ' . $lead_id);
			$result_2 = $this->apimodel->delrecord_condition('proposal_policy_member_details', 'customer_id = ' . $customer_id . ' AND lead_id = ' . $lead_id);
			//$result_3 = $this->apimodel->delrecord_condition('proposal_policy_member', 'member_id IN ('.implode(",", $member_id).')');

			if ($result_1 && $result_2) {

				$msg = 'Members';

				if (count($proposal_details_arr) > 0) {

					$data['nominee_email'] = '';
					$data['nominee_contact'] = '';
					$data['nominee_dob'] = '';
					$data['nominee_gender'] = '';
					$data['nominee_salutation'] = '';
					$data['nominee_last_name'] = '';
					$data['nominee_first_name'] = '';
					$data['nominee_relation'] = '';

					$data['updated_on'] = date("Y-m-d H:i:s");

					insert_application_log($this->input->post('lead_id'), "nominee_deleted", json_encode($data), json_encode(array("response" => "Nominee Deleted")), $created_by);


					$this->apimodel->updateRecordarr('proposal_details', $data, array('proposal_details_id' => $proposal_details_arr[0]['proposal_details_id']));

					$msg .= " & Nominee";
				}

				$msg .= " Deleted";
				$response = json_encode(array("status_code" => "200", "Metadata" => array("msg" => $msg)));
				insert_application_log($lead_id, "delete_insured_member_details", json_encode($_POST), $response, $created_by);
				echo $response;
			} else {

				$response = json_encode(array("status_code" => "500", "Metadata" => array("msg" => 'Something went wrong')));
				insert_application_log($lead_id, "delete_insured_member_details", json_encode($_POST), $response, $created_by);
				echo $response;
			}
		} else {

			insert_application_log($lead_id, "delete_insured_member_details", $checkToken, json_encode(['response' => 'User Auth Failed']), $created_by);
			echo $checkToken;
		}

		exit;
	}

	private function createPolicyProposal($master_quotes_raw_arr, $proposal_id, $member_id, $lead_id, $customer_id, $master_policy_id_arr, $sum_insured_type, $relation_with_proposal, $one_adult_only = false, $created_by)
	{
		$proposal_policy_id_arr = $proposal_policy_data_raw = [];

		$member_data = $this->apimodel->getdata('proposal_policy_member ppm, proposal_policy_member_details ppmd', 'ppm.proposal_policy_member_id, ppm.premium, ppm.member_id, ppm.policy_id, ppm.proposal_policy_id, ppmd.relation_with_proposal', "ppmd.member_id = ppm.member_id AND ppmd.lead_id = $lead_id AND ppmd.customer_id = $customer_id");
		$member_arr = $member_type_id = $proposal_policy_member_arr = $member_relation = [];

		if (!empty($member_data)) {

			foreach ($member_data as $member) {

				$member_arr[$member['proposal_policy_id']] = $member['proposal_policy_id'];
				$member_type_id[$member['proposal_policy_id']][$member['relation_with_proposal']] = $member['policy_id'];
				$proposal_policy_member_arr[$member['member_id']][$member['policy_id']][$member['proposal_policy_member_id']] = $member;
			}

			$member_relation = array_values($member_type_id);

			$proposal_policy_data_raw = $this->apimodel->getdata('proposal_policy', '*', "proposal_policy_id IN (" . implode(',', $member_arr) . ")");
			foreach ($proposal_policy_data_raw as $proposal_policy_data) {

				foreach ($master_quotes_raw_arr as $master_quote) {

					if ($master_quote->master_policy_id == $proposal_policy_data['master_policy_id']) {
						$proposal_policy_id_arr[$master_quote->member_type_id][$master_quote->master_policy_id] = $proposal_policy_data['proposal_policy_id'];
					}
				}
			}
		}

		if ($member_id == 0) {

			$m = 1;
			foreach ($master_quotes_raw_arr as $master_quote) {
                //get master policyid master_policy

                $policy_sub_type_id=$this->db->query('select policy_sub_type_id from master_policy where policy_id='.$master_quote->master_policy_id)->row()->policy_sub_type_id;
            //    $plan_id=$this->db->query('select plan_id from master_policy where policy_id='.$master_quote->master_policy_id)->row()->plan_id;
				$data_proposal_policy = [];
				$proposal_policy_insert_id = null;
				$data_proposal_policy['proposal_details_id'] = $proposal_id;
				$data_proposal_policy['lead_id'] = $master_quote->lead_id;
				$data_proposal_policy['trace_id'] = $master_quote->trace_id;
				//$data_proposal_policy['proposal_no'] = $trace_id;
				$data_proposal_policy['group_code'] = $master_quote->group_code;
				$data_proposal_policy['master_policy_id'] = $master_quote->master_policy_id;
				$data_proposal_policy['sum_insured'] = $master_quote->sum_insured;
				$data_proposal_policy['premium_amount'] = $master_quote->premium;
				$data_proposal_policy['tax_amount'] = $master_quote->premium_with_tax;
				$data_proposal_policy['member_age'] = $master_quote->age;
				$data_proposal_policy['tenure'] = $master_quote->tenure;
				$data_proposal_policy['policy_sub_type_id'] = $policy_sub_type_id;

				$family_construct = $master_quote->family_construct;
				$family_construct = explode('-', $family_construct);

				$this->apimodel->lockTable("proposal_policy", "WRITE");

				try {
					$proposal_no_obj = $this->apimodel->getLastProposalNo();
					if (isset($proposal_no_obj->proposal_no) && $proposal_no_obj->proposal_no != '') {

						$proposal_no = $proposal_no_obj->proposal_no;
						$proposal_no++;
					} else {
						$proposal_no = date('dmy') . '0001';
					}
				} catch (\Throwable $th) {
					$this->apimodel->unLockTable();
				}

				$data_proposal_policy['proposal_no'] = $proposal_no; //microtime(1) . '' . $m; //To confirm with Amit Sir whether to use timestamp or not
				$data_proposal_policy['adult_count'] = $family_construct[0];
				$data_proposal_policy['child_count'] = $family_construct[1];
				$data_proposal_policy['created_at'] = date('Y-m-d H:i:s');
				$data_proposal_policy['created_by'] = $created_by;

				/** 
				 * For inserting data from master_quotes table to proposal_policy table
				 */
				if (in_array($master_quote->master_policy_id, $master_policy_id_arr)) {

					if (($master_quote->member_type_id == $relation_with_proposal)) {

						$mem_id = $relation_with_proposal;

						if ($sum_insured_type[$master_quote->master_policy_id] == 1) {

							try {
								$proposal_policy_insert_id = $this->apimodel->insertData('proposal_policy', $data_proposal_policy, 1);
							} catch (\Throwable $th) {
								$this->apimodel->unLockTable();
							}
							$this->apimodel->unLockTable();
							$proposal_policy_id_arr[$mem_id][$data_proposal_policy['master_policy_id']] = $proposal_policy_insert_id;
							$m++;

							insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($data_proposal_policy), json_encode(['response' => 'Policy Proposal Saved On ID ' . $proposal_policy_insert_id]), $created_by);
						} else if ($sum_insured_type[$master_quote->master_policy_id] == 2) {

							if ($relation_with_proposal == 1 || $one_adult_only) {

								try {
									$proposal_policy_insert_id = $this->apimodel->insertData('proposal_policy', $data_proposal_policy, 1);
								} catch (\Throwable $th) {
									$this->apimodel->unLockTable();
								}
								$this->apimodel->unLockTable();
								$proposal_policy_id_arr[$mem_id][$data_proposal_policy['master_policy_id']] = $proposal_policy_insert_id;
								$m++;

								insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($data_proposal_policy), json_encode(['response' => 'Policy Proposal Saved On ID ' . $proposal_policy_insert_id]), $created_by);
							}
						}
					}
				}
			}
		} else {

			if (!empty($proposal_policy_data_raw)) {

				foreach ($proposal_policy_data_raw as $proposal_policy_data) {

					foreach ($master_quotes_raw_arr as $master_quote) {

						if (isset($member_type_id[$proposal_policy_data['proposal_policy_id']][$master_quote->member_type_id])) {
							if ($member_type_id[$proposal_policy_data['proposal_policy_id']][$master_quote->member_type_id] == $master_quote->master_policy_id) {

								$data_proposal_policy = [];

								$data_proposal_policy['sum_insured'] = $master_quote->sum_insured;
								$data_proposal_policy['premium_amount'] = $master_quote->premium;
								$data_proposal_policy['tax_amount'] = $master_quote->premium_with_tax;
								$data_proposal_policy['member_age'] = $master_quote->age;
								$data_proposal_policy['tenure'] = $master_quote->tenure;
								$data_proposal_policy['group_code'] = $master_quote->group_code;

								$family_construct = $master_quote->family_construct;
								$family_construct = explode('-', $family_construct);

								$data_proposal_policy['adult_count'] = $family_construct[0];
								$data_proposal_policy['child_count'] = $family_construct[1];
								$data_proposal_policy['updated_at'] = date('Y-m-d H:i:s');
								$data_proposal_policy['updated_by'] = $created_by;

								$this->apimodel->updateRecord('proposal_policy', $data_proposal_policy, "proposal_policy_id = " . $proposal_policy_data['proposal_policy_id']);

								insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($data_proposal_policy), json_encode(['response' => 'Policy Proposal Updated For ID: ' . $proposal_policy_data['proposal_policy_id']]), $created_by);
							}
						}
					}
				}
			}
		}

		$proposal_policy_id_arr['member_data'] = $proposal_policy_member_arr;

		return $proposal_policy_id_arr;
	}

	/*
	Author : Jitendra
	Date : 18th dec, 2020
	***/
	function proposalInsuredMemberSubmit($data = null)
	{

      
		if(isset($_POST['utoken'])){

			$checkToken = $this->verify_request($_POST['utoken']);
		}
        if($data != null){
            $_POST=$data;
            $checkToken->username=$_POST['user_id'];
        }

		if(isset($_POST['source']) && $_POST['source'] == 'customer'){
			
			$_POST['lead_id'] = encrypt_decrypt_password($_POST['lead_id'], 'D');
			$_POST['trace_id'] = encrypt_decrypt_password($_POST['trace_id'], 'D');
			$_POST['customer_id'] = encrypt_decrypt_password($_POST['customer_id'], 'D');
			$_POST['plan_id'] = encrypt_decrypt_password($_POST['plan_id'], 'D');
			$_POST['proposal_id'] = encrypt_decrypt_password($_POST['proposal_id'], 'D');
			$_POST['quote_id'] = encrypt_decrypt_password($_POST['quote_id'], 'D');
		}

		if ((isset($checkToken) && !empty($checkToken->username)) || (isset($_POST['source']) && $_POST['source'] == 'customer')) {

			$lead_id = trim($_POST['lead_id']);
			$customer_id = trim($_POST['customer_id']);
			$proposal_id = trim($this->input->post('proposal_id'));
			$trace_id = trim($_POST['trace_id']);
			$original_member_id = (trim($this->input->post('member_id')) != '') ? trim($this->input->post('member_id')) : 0;
			$plan_id = trim($_POST['plan_id']);
			$member_salutation = trim(ucwords($this->input->post('member_salutation')));
			$member_firstname = trim(ucwords($this->input->post('first_name')));
			$member_lastname = trim(ucwords($this->input->post('last_name')));
			$member_gender = trim(ucwords($this->input->post('gender')));
			$member_dob = trim($this->input->post('insured_member_dob'));
			$relation_with_proposal = trim($this->input->post('relation_with_proposal'));
			$created_by = trim($this->input->post('created_by'));


			if (in_array($relation_with_proposal, [1, 2]) && !$original_member_id) {

				$get_exiting_data = $this->apimodel->getdata('proposal_policy_member_details', 'member_id', "lead_id=$lead_id AND customer_id=$customer_id AND relation_with_proposal=$relation_with_proposal");

				if (!empty($get_exiting_data)) {

					$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Member already added')));

					insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($_POST), $response, $created_by);

                    if($_POST['is_api'] == 1){
                        return $response;
                    }else{
                        echo $response;
                        exit;
                    }
				}
			}


			if (in_array($relation_with_proposal, [3, 4]) && !$original_member_id) {

				$get_exiting_data = $this->apimodel->getdata('proposal_policy_member_details', 'member_id', "lead_id=$lead_id AND customer_id=$customer_id AND relation_with_proposal=$relation_with_proposal AND policy_member_salutation = $member_salutation && policy_member_gender = $member_gender AND policy_member_first_name = $member_firstname AND policy_member_last_name = $member_lastname AND policy_member_dob =$member_dob");

				if (!empty($get_exiting_data)) {

					$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Member already added')));

					insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($_POST), $response, $created_by);

                    if($_POST['is_api'] == 1){
                        return $response;
                    }else{
                        echo $response;
                        exit;
                    }
				}
			}


			$master_quotes_raw_arr = $this->apimodel->select_member_quote_details_by_lead_customer_member_type_id($lead_id, $customer_id);

			if (empty($master_quotes_raw_arr)) {

				$response = json_encode(array("status_code" => "500", "Metadata" => array("Message" => 'Please submit Generate Quote section form first')));

				insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($_POST), $response, $created_by);

                if($_POST['is_api'] == 1){
                    return $response;
                }else{
                    echo $response;
                    exit;
                }
			}


			//$premium_amount = 0;
			//$tax = 0;
			foreach ($master_quotes_raw_arr as $master_quotes) {

				$premium_amount[$master_quotes->master_policy_id]['premium'] = $master_quotes->premium;
				$tax[$master_quotes->master_policy_id]['premium_with_tax'] = $master_quotes->premium_with_tax;
				$family_construct = $master_quotes->family_construct;
				$family_construct = explode('-', $family_construct);
				$policy_id_arr[$master_quotes->master_policy_id] = $master_quotes->master_policy_id;
			}

			/** 
			 * For one adult family construct other than self
			 */
			$one_adult_only = false;
			if ($family_construct['0'] == 1 && $family_construct['1'] == 0) {

				if ($relation_with_proposal > 1) {

					/*if($this->apimodel->updateRecordarr('master_quotes', ['member_type_id' => $relation_with_proposal], "lead_id = $lead_id AND master_customer_id = $customer_id")){
						
						$master_quotes_raw_arr = $this->apimodel->select_member_quote_details_by_lead_customer_member_type_id($lead_id, $customer_id);
						$one_adult_only = true;
					}*/

					$one_adult_only = true;
				}
			}

			//function to get master policy id for insured members other than self

			$master_policy_id_arr = $sum_insured_type = $policy_id_member_arr = $master_policy_sub_type_arr = [];

			//if ($relation_with_proposal > 1) {

			$result_policy_id_arr_raw = $this->apimodel->getPolicyIDForInsuredMembers($relation_with_proposal, $plan_id, $policy_id_arr);

			foreach ($result_policy_id_arr_raw as $result_policy_id_arr) {

				$master_policy_id_arr[$result_policy_id_arr['policy_id']] = $result_policy_id_arr['policy_id'];
				$sum_insured_type[$result_policy_id_arr['policy_id']] = $result_policy_id_arr['suminsured_type_id'];
				$policy_id_member_arr[$result_policy_id_arr['member_type_id']][$result_policy_id_arr['policy_id']] = $result_policy_id_arr['policy_id'];
			}
			//}

			$arr_plan_details = $this->apimodel->getProductDetailsAll($plan_id, $policy_id_arr);
			$policy_id_arr = [];

			for ($i = 0; $i < count($arr_plan_details); $i++) {

				$policy_id_arr[$arr_plan_details[$i]->policy_id] = $arr_plan_details[$i]->policy_id;
				$sum_insured_type[$arr_plan_details[$i]->policy_id] = $arr_plan_details[$i]->sitype_id;
				$master_policy_sub_type_arr[$arr_plan_details[$i]->policy_id] = $arr_plan_details[$i]->policy_sub_type_name;
			}

			$policy_family_details_raw = $this->apimodel->getPolicyFamilyDetails($policy_id_arr);
			$policy_family_details = $member_age_arr = $is_adult = $master_policy_id_arr = [];
			$min_age = $max_age = $min_age_in_days = 0;

			foreach ($policy_family_details_raw as $policy_family_detail) {

				$policy_family_details[$policy_family_detail->master_policy_id][$policy_family_detail->member_type_id] = $policy_family_detail->member_type_id;

				//if ($min_age == 0 && $max_age == 0 && $min_age_in_days == 0) {

				$min_age = $policy_family_detail->member_min_age;
				$max_age = $policy_family_detail->member_max_age;
				$min_age_in_days = $policy_family_detail->member_min_age_days;
				/*}

				if ($min_age > $policy_family_detail->member_min_age) {

					$min_age = $policy_family_detail->member_min_age;
				}

				if ($max_age < $policy_family_detail->member_max_age) {

					$max_age = $policy_family_detail->member_max_age;
				}

				if($min_age_in_days > $policy_family_detail->member_min_age_days){

					$min_age_in_days = $policy_family_detail->member_min_age_days;
				}*/

				$member_age_arr[$policy_family_detail->master_policy_id][$policy_family_detail->member_type_id]['min_age'] = $min_age;
				$member_age_arr[$policy_family_detail->master_policy_id][$policy_family_detail->member_type_id]['max_age'] = $max_age;
				$member_age_arr[$policy_family_detail->master_policy_id][$policy_family_detail->member_type_id]['min_age_in_days'] = $policy_family_detail->member_min_age_days;

				$is_adult[$policy_family_detail->member_type_id] = $policy_family_detail->is_adult;

				$master_policy_id_arr[$policy_family_detail->master_policy_id] = $policy_family_detail->master_policy_id;
			}

			/*$master_policy_sub_type_raw = $master_policy_sub_type_arr = [];

			$master_policy_sub_type_raw = $this->apimodel->getdata('master_policy_sub_type', 'policy_sub_type_id, policy_sub_type_name', 'policy_sub_type_id IN ('.implode(",", $master_policy_id_arr).') AND isactive = 1');

			if(!empty($master_policy_sub_type_raw)){

				foreach($master_policy_sub_type_raw as $master_policy_sub_type){

					$master_policy_sub_type_arr[$master_policy_sub_type['$master_policy_sub_type']] = $master_policy_sub_type['$master_policy_sub_type'];
				}
			}*/

			$data_lead = array();
			$data_lead['plan_id'] = $plan_id;

			$data_policy_member = $data_member = $data_member2=array();
			$data_member['policy_member_salutation'] = $member_salutation;
			$data_member['policy_member_first_name'] = $member_firstname;
			$data_member['policy_member_last_name'] = $member_lastname;
			$data_member['policy_member_gender'] = $member_gender;
			$data_member['policy_member_dob'] = (!empty($member_dob)) ? date('Y-m-d', strtotime($member_dob)) : '';
			$data_member['lead_id'] = $lead_id;
			$data_member['customer_id'] = $customer_id;
			$data_member['relation_with_proposal'] = $relation_with_proposal;
			$data_member['created_by'] = $created_by;


			$from = new DateTime($member_dob);
			$to   = new DateTime('today');
			$data_member['policy_member_age'] = $from->diff($to)->y;
			$age_in_days = $from->diff($to, true)->format('%a');

			//Function to validate and add/update member's age in proposal_policy_member_details table
			$member_id = $this->validateAddUpdateMemberDetails($data_member, $age_in_days, $original_member_id, $policy_id_member_arr, $is_adult, $master_policy_sub_type_arr, $member_age_arr);

			if (!is_numeric($member_id)) {

				echo $member_id;
				exit;
			}

			//function to insert into policy proposal
			try {
				$proposal_policy_id_arr = $this->createPolicyProposal($master_quotes_raw_arr, $proposal_id, $original_member_id, $lead_id, $customer_id, $master_policy_id_arr, $sum_insured_type, $relation_with_proposal, $one_adult_only, $created_by);
			} catch (\Throwable $th) {
				$this->apimodel->unLockTable();
			}
			$this->apimodel->unLockTable();

			$i = 0;
			foreach ($policy_family_details as $policy_id => $member_type_id_arr) {

				if (isset($premium_amount[$policy_id])) {
					if (in_array($relation_with_proposal, $member_type_id_arr)) {

						$data_policy_member['trace_id'] = $trace_id;
						$data_policy_member['member_id'] = $member_id;
						$data_policy_member['policy_id'] = $policy_id;
						$data_policy_member['lead_id'] = $lead_id;
						$data_policy_member['premium'] = null;
						$data_policy_member['premium_with_tax'] = null;
                        $dataNew['lead_id']=$lead_id;
                        $dataNew['customer_id']=$customer_id;
                        $dataNew['lead_id']=$lead_id;
                        $dataNew['policy_id']=$policy_id;
                        $dataNew['plan_id']=$plan_id;
                        $dataNew['premium']=null;
                        $dataNew['total_premium']=null;
                        $sumInsured=$this->db->query('select sum_insured from master_quotes where lead_id='.$lead_id.' order by master_quote_id desc limit 1')->row()->sum_insured;
                        $dataNew['cover']=$sumInsured; //sum_insured
                        if (!isset($proposal_policy_id_arr['member_data'][$member_id][$policy_id])) {

							if ($sum_insured_type[$policy_id] == 1) {

								//if ((count($policy_family_details) - 1) == $i) {

								$data_policy_member['premium'] = $premium_amount[$policy_id]['premium'];
								$data_policy_member['premium_with_tax'] = $tax[$policy_id]['premium_with_tax'];
                                $dataNew['premium']=$premium_amount[$policy_id]['premium'];
                                $dataNew['total_premium']=$tax[$policy_id]['premium_with_tax'];
								//}
							} else if ($sum_insured_type[$policy_id] == 2) {

								if ($relation_with_proposal == 1 || $one_adult_only) {

									$data_policy_member['premium'] = $premium_amount[$policy_id]['premium'];
									$data_policy_member['premium_with_tax'] = $tax[$policy_id]['premium_with_tax'];
                                    $dataNew['premium']=$premium_amount[$policy_id]['premium'];
                                    $dataNew['total_premium']=$tax[$policy_id]['premium_with_tax'];
								}
							}

							$data_policy_member['member_unique_id'] = time();
							$data_policy_member['created_at'] = date("Y-m-d H:i:s");
							$data_policy_member['created_by'] = $created_by;

							if (isset($proposal_policy_id_arr[$relation_with_proposal][$policy_id])) {

								$data_policy_member['proposal_policy_id'] = $proposal_policy_id_arr[$relation_with_proposal][$policy_id];
							} else {

								$data_policy_member['proposal_policy_id'] = $proposal_policy_id_arr[1][$policy_id];
							}
                                $this->db->insert('policy_member_plan_details',$dataNew);
							$insert_id = $this->apimodel->insertData('proposal_policy_member', $data_policy_member, 1);
							$proposal_policy_id_arr['member_data'][$member_id][$policy_id][$insert_id] = $insert_id;

							insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_policy_member), json_encode(['response' => 'Insured Member Premium Added On ID ' . $insert_id]), $created_by);
						} else {

							$policy_member_arr = $proposal_policy_id_arr['member_data'][$member_id][$policy_id];
							$data_policy_member['updated_at'] = date("Y-m-d H:i:s");
							$data_policy_member['updated_by'] = $created_by;

							foreach ($policy_member_arr as $policy_member) {

								if ($policy_member['premium']) {

									if ($sum_insured_type[$policy_id] == 1) {

										$data_policy_member['premium'] = $premium_amount[$policy_id]['premium'];
										$data_policy_member['premium_with_tax'] = $tax[$policy_id]['premium_with_tax'];
									} else if ($sum_insured_type[$policy_id] == 2) {

										if ($relation_with_proposal == 1 || $one_adult_only) {

											$data_policy_member['premium'] = $premium_amount[$policy_id]['premium'];
											$data_policy_member['premium_with_tax'] = $tax[$policy_id]['premium_with_tax'];
										}
									}

									$result2 = $this->apimodel->updateRecordarr('proposal_policy_member', $data_policy_member, ['proposal_policy_member_id' => $policy_member['proposal_policy_member_id']]);

									insert_application_log($data_member['lead_id'], "proposal_insured_member_submit", json_encode($data_policy_member), json_encode(['response' => 'Insured Member Premium Updated On ID ' . $policy_member['proposal_policy_member_id']]), $created_by);
								}
							}
						}
					}
				}

				$i++;
			}

			$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated.', "member_id" => $member_id, "data_added" => $data_member['relation_with_proposal'])));
			insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($_POST), $response, $this->input->post('created_by'));
			if($_POST['is_api'] == 1){
                return $response;
            }else{
                echo $response;
                exit;
            }

		} else {

			insert_application_log($_POST['lead_id'], "proposal_insured_member_submit", $checkToken, json_encode(['response' => 'User Auth Failed']), $this->input->post('created_by'));

			echo $checkToken;
		}
	}
	// proposalInsuredMemberSubmit()

	public function updatecustomerpan()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$customer_data = $this->input->post('customer_data');
			$lead_id = $this->input->post('lead_id');

			foreach ($customer_data as $customer_id => $proposer_pan) {

				if ($this->apimodel->updateRecordarr('master_customer', ['pan' => $proposer_pan], ['customer_id' => $customer_id, 'lead_id' => $lead_id])) {

					$response_arr[] = 200;
				} else {

					$response_arr[] = 500;
				}
			}

			if (isset($response_arr) && !in_array(500, $response_arr)) {

				$response = json_encode(array("status_code" => "200", "Metadata" => array("msg" => 'PAN Updated Successfully')));
			} else {

				$response = json_encode(array("status_code" => "500", "Metadata" => array("msg" => 'Something went wrong')));
			}

			insert_application_log($this->input->post('lead_id'), "update_customer_pan", json_encode($_POST), $response, $this->input->post('created_by'));
			echo $response;
		} else {

			insert_application_log($this->input->post('lead_id'), "update_customer_pan", $checkToken, json_encode(['response' => 'User Auth Failed']), $this->input->post('created_by'));
			echo $checkToken;
		}
		exit;
	}

	/*
	Author : Amol Koli
	Date : 02th jan, 2020
	***/
	function doInsurance()
	{
//		ini_set('display_errors', 1);
		$lead_id = $this->input->post('lead_id');
		//$mode_of_payment = $this->input->post('mode_of_payment');

		$doQuickQuote = $this->apimodel->doQuickQuote($lead_id);
		$doFullQuote = $this->apimodel->doFullQuote($lead_id);

		if (!empty($doFullQuote)) {
			if (isset($doFullQuote['Status']) && $doFullQuote['Status'] == "Success") {
				echo json_encode([
					"status_code" => "200",
					"data" => $doFullQuote
				]);
			} else {
				echo json_encode(
					array(
						"status_code" => "400",
						"data" => $doFullQuote
					)
				);
			}
		}
	}

	/*
	Author : Amol Koli
	Date : 01th jan, 2020
	***/
	function doFullQuote()
	{
		$lead_id = $this->input->post('lead_id');
		$doFullQuote = $this->apimodel->doFullQuote($lead_id);
		if (!empty($doFullQuote)) {
			if (isset($doFullQuote['Status']) && $doFullQuote['Status'] == "Success") {
				echo json_encode([
					"status_code" => "200",
					"data" => $doFullQuote
				]);
			}
		}
		echo json_encode(
			array(
				"status_code" => "400",
				"data" => $doFullQuote
			)
		);
	}

	/*
	Author : Amol Koli
	Date : 24th dec, 2020
	***/
	function doQuickQuote()
	{
		//ini_set('display_errors', 1);
		$lead_id = $this->input->post('lead_id');
		//$mode_of_payment = $this->input->post('mode_of_payment');
		$doQuickQuote = $this->apimodel->doQuickQuote($lead_id);
		if (!empty($doQuickQuote)) {
			if (isset($doQuickQuote['Status']) && $doQuickQuote['Status'] == "Success") {
				echo json_encode([
					"status_code" => "200",
					"data" => $doQuickQuote
				]);
			} else {
				echo json_encode(
					array(
						"status_code" => "400",
						"data" => $doQuickQuote
					)
				);
			}
		}
	}

	function doQuickQuoteByCustomer()
	{

		$lead_id = $this->input->post('lead_id');
		$customer_id = $this->input->post('customer_id');
		$doQuickQuote = $this->apimodel->doQuickQuote($lead_id, '', $customer_id, true);
		print_r($doQuickQuote);
		exit;
	}
	// function doQuickQuote()
	// {
	// 	//ini_set('display_errors', 1);
	// 	$lead_id = $this->input->post('lead_id');
	// 	$mode_of_payment = $this->input->post('mode_of_payment');
	// 	$doQuickQuote = $this->apimodel->doQuickQuote($lead_id, $mode_of_payment);
	// 	if (!empty($doQuickQuote)) {
	// 		if (isset($doQuickQuote['Status']) && $doQuickQuote['Status'] == "Success") {
	// 			echo json_encode([
	// 				"status_code" => "200",
	// 				"data" => $doQuickQuote
	// 			]);
	// 		}
	// 	}
	// 	echo json_encode(
	// 		array(
	// 			"status_code" => "400",
	// 			"data" => $doQuickQuote
	// 		)
	// 	);
	// 	exit;
	// }

	/*
	Author : Amol Koli
	Date : 02th jan, 2020
	***/
	function doSendCommunication()
	{
		$lead_id = $this->input->post('lead_id');
		$comm_trigger = $this->input->post('comm_trigger');
		$getLeadDetails = $this->apimodel->getLeadDetails($lead_id);
		if (!empty($comm_trigger)) {
			$getTriggers = $this->apimodel->getTriggers($comm_trigger);
		}

		// $AlertV1 = "860325";
		$AlertV1 = "";
		$AlertV2 = "";
		$AlertV3 = "";
		$AlertV4 = "";
		$AlertV5 = "";

		$doCommunicate = [];
		if (!empty($getLeadDetails)) {

			if (isset($getTriggers->alert_id) && $getTriggers->alert_id == 'A828') {

				$lead_id_encrypt = encrypt_decrypt_password($lead_id);

				$get_short_url = $this->apimodel->getdata('short_urls', 'short_code', 'lead_id = ' . $lead_id);

				if (!empty($get_short_url)) {

					$AlertV2 = $get_short_url[0]['short_code'];
				}

				$AlertV1 = $getLeadDetails[0]->first_name . " " . $getLeadDetails[0]->last_name;
				//$AlertV2 = base_url("api2/payment_redirection/" . $lead_id_encrypt);
				$AlertV3 = date("m-d-Y", time() + 86400);
				$AlertV4 = $lead_id;
				$AlertV5 = 'PaymentSupport.HealthInsurance@adityabirlacapital.com';
			}

			if (isset($getTriggers->alert_id) && $getTriggers->alert_id == 'A533') {
				//TODO: This is the OTP
				$AlertV1 = "860325";
			}

			$request = [
				"RTdetails" => [
					"PolicyID" => "",
					"AppNo" => "201901230001211",
					"alertID" => isset($getTriggers->alert_id) ? $getTriggers->alert_id : '',
					"channel_ID" => "ABHI Creditor Portal",
					"Req_Id" => "1",
					"field1" => "",
					"field2" => "",
					"field3" => "",
					"Alert_Mode" => isset($getTriggers->alert_mode) ? $getTriggers->alert_mode : '',
				],
				"Alertdata" => [
					"mobileno" => isset($getLeadDetails[0]->mobile_no) ? substr(trim($getLeadDetails[0]->mobile_no), -10) : "",
					"emailId" => isset($getLeadDetails[0]->email_id) ? $getLeadDetails[0]->email_id : "",
					"AlertV1" => $AlertV1,
					"AlertV2" => $AlertV2,
					"AlertV3" => $AlertV3,
					"AlertV4" => $AlertV4,
					"AlertV4" => $AlertV5,
				]
			];
			$doCommunicate = $this->apimodel->doCommunicate($request);

			$request_arr = [
				"lead_id" => $lead_id,
				"req" => json_encode($request),
				"res" => json_encode($doCommunicate),
				"product_id" => $getLeadDetails[0]->plan_id,
				"type" => "communication_sent",
			];

			$this->db->insert("logs_docs", $request_arr);

			//Application log entries
			$this->db->insert("application_logs", [
				"lead_id" => $lead_id,
				"action" => "communication_sent",
				"request_data" => json_encode($request),
				"response_data" => json_encode($doCommunicate),
				"created_on" => date("Y-m-d H:i:s")
			]);

			echo json_encode([
				"status_code" => "200",
				"data" => $doCommunicate
			]);
		} else {
			echo json_encode(
				array(
					"status_code" => "400",
					"data" => ["status" => "Error", "msg" => "Error in proposal"]
				)
			);
		}
	}
	// function doSendCommunication()
	// {
	// 	$lead_id = $this->input->post('lead_id');
	// 	$comm_trigger = $this->input->post('comm_trigger');
	// 	$getLeadDetails = $this->apimodel->getLeadDetails($lead_id);
	// 	if (!empty($comm_trigger)) {
	// 		$getTriggers = $this->apimodel->getTriggers($comm_trigger);
	// 	}

	// 	$doCommunicate = [];
	// 	if (!empty($getLeadDetails)) {
	// 		$request = [
	// 			"RTdetails" => [
	// 				"PolicyID" => "",
	// 				"AppNo" => "201901230001211",
	// 				"alertID" => isset($getTriggers->alert_id) ? $getTriggers->alert_id : '',
	// 				"channel_ID" => "ABHI Diamond",
	// 				"Req_Id" => "1",
	// 				"field1" => "",
	// 				"field2" => "",
	// 				"field3" => "",
	// 				"Alert_Mode" => isset($getTriggers->alert_mode) ? $getTriggers->alert_mode : '',
	// 			],
	// 			"Alertdata" => [
	// 				"mobileno" => isset($getLeadDetails[0]->mobile_no) ? $getLeadDetails[0]->mobile_no : "",
	// 				"emailId" => isset($getLeadDetails[0]->email_id) ? $getLeadDetails[0]->email_id : "",
	// 				"AlertV1" => "860325",
	// 				"AlertV2" => "",
	// 				"AlertV3" => "",
	// 				"AlertV4" => ""
	// 			]
	// 		];
	// 		$doCommunicate = $this->apimodel->doCommunicate($request);
	// 		echo json_encode([
	// 			"status_code" => "200",
	// 			"data" => $doCommunicate
	// 		]);
	// 	}
	// 	print_r($doCommunicate);
	// }

	/*
	Author : Amol Koli
	Date : 02th jan, 2020
	***/
	function doSendOtp()
	{
		$lead_id = $this->input->post('lead_id');
		$lead_id = encrypt_decrypt_password($lead_id, 'D');

		$to_send_data = $this->apimodel->getdata('lead_details ld, short_urls su, master_plan mp', 'su.otp, ld.mobile_no, ld.email_id, mp.plan_name, mp.plan_id', 'ld.lead_id = '.$lead_id.' AND ld.plan_id = mp.plan_id AND ld.lead_id = su.lead_id AND su.otp != NULL');
		
		if(!empty($to_send_data)){

			$data['lead_id'] = $lead_id;
			$data['mobile_no'] = $to_send_data[0]['mobile_no'];
			$data['plan_id'] = $to_send_data[0]['plan_id'];
			$data['email_id'] = $to_send_data[0]['email_id'];
			//$data['alerts'][6] = ; //Group Organizer Name goes here
			$data['alerts'][7] = $to_send_data[0]['otp'];

			$response = triggerCommunication(['A1659'], $data);

			insert_application_log($lead_id, "send_otp_customer", json_encode($data), json_encode($response), 0);

			if(ENV_BYPASSED || $response['status_code'] == 200){

				$response = ['success' => true, 'msg' => 'Status changed to UW-Approval-Awaiting sent to customer'];

				insert_application_log($lead_id, "send_otp_customer", json_encode($data), json_encode($response), 0);
			}
			else{

				$response = ['success' => false, 'msg' => 'Something went wrong when OTP'];

				insert_application_log($lead_id, "send_otp_customer", json_encode($data), json_encode($response), 0);
			}
		}
		else{

			insert_application_log($lead_id, "send_otp_customer", json_encode($_POST), json_encode(['response' => 'OTP Sending Failed']), 0);
		}
	}

	/*
	Author : Amol Koli
	Date : 02th jan, 2020
	***/
	function doVerifyOtp()
	{
		$lead_id = $this->input->post('lead_id');
	}

	public function getquotedetails()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = $_POST['lead_id'];
			//$customer_id = $_POST['customer_id'];
			//$quote_ids = $this->input->post('quote_ids');
			//$data = $this->apimodel->select_member_quote_details($quote_ids);
			//$data = $this->apimodel->getdata('master_quotes', 'premium_with_tax', "lead_id = $lead_id AND master_customer_id = $customer_id");
			$data = $this->apimodel->getdata('master_quotes', 'premium_with_tax', "lead_id = $lead_id");
			echo json_encode(array("status_code" => "200", "Metadata" => array("data" => $data, "Message" => "")));
			exit;
		} else {
			echo $checkToken;
		}
	}

	public function getmemberdetails()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = $this->input->post('lead_id');
			$member_id = $this->input->post('member_id');
			$customer_id = $this->input->post('customer_id');

			$data = $this->apimodel->select_member_details($member_id, $lead_id, $customer_id);

			echo json_encode(array("status_code" => "200", "Metadata" => array("data" => $data)));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function geProposalPolicyDetails()
	{
		$data = array();
		$lead_id = $this->input->post('lead_id');
		$status = $this->input->post('status');
		$geProposalPolicyDetails = $this->apimodel->geProposalPolicyDetails($lead_id, $status);
		echo json_encode(array("status_code" => "200", "Metadata" => array("data" => $geProposalPolicyDetails, "Message" => "")));
	}

	public function saveHealthDeclaration()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$condition['proposal_details_id'] = $this->input->post('proposal_details_id');
			$arr['health_declaration'] = $this->input->post('health_declaration');

			if ($this->apimodel->updateProposalPolicy($arr, $condition)) {

				echo json_encode(array("status_code" => "200"));
			} else {

				echo json_encode(array("status_code" => "500"));
			}
		} else {
			echo $checkToken;
		}

		exit;
	}

	function savecustomerdetails()
	{
		$data = [];
		$data['first_name'] = $_POST['firstname'];
		$data['last_name'] = $_POST['lastname'];
		$data['pincode'] = $_POST['pincode'];
		$data['state'] = $_POST['state'];
		$data['city'] = $_POST['city'];
		$data['address_line1'] = $_POST['address_line1'];
		$data['address_line2'] = $_POST['address_line2'];
		$data['address_line3'] = $_POST['address_line3'];

		$condition['lead_id'] = $_POST['lead_id'];
		$condition['customer_id'] = $_POST['customer_id'];

		$response['response'] = $this->apimodel->updateRecordarr('master_customer', $data, $condition);
		insert_application_log($_POST['lead_id'], "save_customer_summary_details", json_encode($data), json_encode(array("response" => $response['response'])), $condition['customer_id']);
		echo json_encode($response);
		exit;
	}

	function savememberdetails()
	{
		$data = [];
		$data['policy_member_first_name'] = $_POST['firstname'];
		$data['policy_member_last_name'] = $_POST['lastname'];

		$condition['lead_id'] = $_POST['lead_id'];
		$condition['customer_id'] = $_POST['customer_id'];
		$condition['member_id'] = $_POST['member_id'];

		$response['response'] = $this->apimodel->updateRecordarr('proposal_policy_member_details', $data, $condition);

		echo json_encode($response);
		exit;
	}

	function getProposalPolicySumInsured()
	{

		$lead_id = htmlspecialchars(strip_tags(trim($this->input->post('lead_id'))));
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$sum_insured = $this->apimodel->getProposalPolicySumInsured($lead_id);

			if (isset($sum_insured[0]['sum_insured'])) {

				echo json_encode(array("status_code" => "200", "Metadata" => array("sum_insured" => $sum_insured[0]['sum_insured'])));
			} else {

				echo json_encode(array("status_code" => "500", "Metadata" => array("sum_insured" => 0)));
			}
		} else {
			echo $checkToken;
		}

		exit;
	}

	function getProposalSummary()
	{

		$data = array();
		$lead_id = $this->input->post('lead_id');

		$checkToken = isset($_POST['utoken']) ? $this->verify_request($_POST['utoken']) : '';

		$customer_form = isset($_POST['customer']) ? $_POST['customer'] : '';

// echo "<PRE>";json_encode($lead_id);exit;
		if ($customer_form != '' || ($checkToken != '' && !empty($checkToken->username))) {

			$customer_details = $this->apimodel->getLeadDetails($lead_id);
			$propasal_details = $this->apimodel->getProposalDetails($lead_id);

			

			$customer_details = json_decode(json_encode($customer_details), true);
			$propasal_details = json_decode(json_encode($propasal_details), true);

			$creditor_id = $plan_id = 0;
// echo json_encode($propasal_details);exit;
			foreach ($customer_details as $customer_detail) {

				foreach ($propasal_details as $propasal_detail) {

					if ($customer_detail['customer_id'] == $propasal_detail['customer_id']) {

						/*$lead_id_encrypted = rtrim(strtr(base64_encode($propasal_detail['lead_id']), '+/', '-_'), '=');
						$customer_id_encrypted = rtrim(strtr(base64_encode($propasal_detail['customer_id']), '+/', '-_'), '=');*/
						$lead_id_encrypted = encrypt_decrypt_password($propasal_detail['lead_id'], 'E');
						$customer_id_encrypted = encrypt_decrypt_password($propasal_detail['customer_id'], 'E');
						$data['customer_details'][$lead_id_encrypted][$customer_id_encrypted] = array_merge($customer_detail, $propasal_detail);
					}
				}

				if (!$creditor_id && !$plan_id) {

					$creditor_id = $customer_detail['creditor_id'];
					$plan_id = $customer_detail['plan_id'];
				}
			}
// echo json_encode($data['customer_details']);exit;
			// $assignment_declaration = '';

			// if ($creditor_id && $plan_id) {

			// 	$assignment_declaration = $this->apimodel->getAssignmentDeclarationByPlanIDCreditorID($plan_id, $creditor_id);
			// }

			// $data['assignment_declaration'] = htmlentities($assignment_declaration->content);

			$member_details = $this->apimodel->getMemberDetails($lead_id);
			$proposal_policy_ids = [];

			foreach ($member_details as $member_detail) {

				//$customer_id = rtrim(strtr(base64_encode($member_detail['customer_id']), '+/', '-_'), '=');
				//$member_id = rtrim(strtr(base64_encode($member_detail['member_id']), '+/', '-_'), '=');
				$customer_id = encrypt_decrypt_password($member_detail['customer_id'], 'E');
				$member_id = encrypt_decrypt_password($member_detail['member_id'], 'E');
				$data['member_details'][$member_detail['policy_sub_type_name']][$customer_id][$member_id] = $member_detail;
			}

			$proposal_policies = $this->apimodel->getProposalPolicy($lead_id);

			$proposal_policies = json_decode(json_encode($proposal_policies), true);

			$sum_insured = 0;
			foreach ($proposal_policies as $proposal_policy) {

				$data['proposal_policy'][$proposal_policy['proposal_policy_id']] = $proposal_policy;
				$proposal_policy_ids[$proposal_policy['master_policy_id']] = $proposal_policy['master_policy_id'];
				$sum_insured += $proposal_policy['sum_insured'];
			}

			$nominee_relations_raw = $this->apimodel->getdata('master_nominee_relations', 'id, name');

			$condition = 'master_policy_id IN (' . implode(",", $proposal_policy_ids) . ') And isactive = 1';
			$si_type_mapping_raw = $this->apimodel->getdata('master_policy_si_type_mapping', 'master_policy_id, suminsured_type_id', $condition);

			foreach ($si_type_mapping_raw as $si_mapping) {

				$data['si_type_mapping'][$si_mapping['master_policy_id']] = $si_mapping['suminsured_type_id'];
			}

			foreach ($nominee_relations_raw as $nominee_relation) {

				$data['nominee_relations'][$nominee_relation['id']] = $nominee_relation['name'];
			}

			$assignment_declaration = '';

			$creditors = $this->apimodel->getdata('master_ceditors', 'creaditor_name', 'creditor_id = ' . $customer_details[0]['creditor_id']);
			$assignment_declaration_arr = $this->getAssignmentDeclarationDetails();

			if (!empty($assignment_declaration_arr)) {

				$assignment_declaration = $assignment_declaration_arr['content'];
				$customer_name = $customer_details[0]['first_name'] . ' ' . $customer_details[0]['middle_name'] . ' ' . $customer_details[0]['last_name'];
				$creditor_name = isset($creditors[0]['creaditor_name']) ? $creditors[0]['creaditor_name'] : '';

				if ($sum_insured > 0) {

					$sum_insured = number_format($sum_insured, 2, '.', ',');
				}

				$assignment_declaration = str_replace('@@APPLICANT_NAME@@', $customer_name, $assignment_declaration);
				$assignment_declaration = str_replace('@@LAN_ID@@', $customer_details[0]['lan_id'], $assignment_declaration);
				$assignment_declaration = str_replace('@@CREDITOR_NAME@@', $creditor_name, $assignment_declaration);
				$assignment_declaration = str_replace('@@SUM_INSURED@@', $sum_insured, $assignment_declaration);
			}

			$data['assignment_declaration_answer'] = $customer_details[0]['assignment_declaration'];
			$data['assigment_declaration'] = $assignment_declaration;
			$data['payment_modes'] = $this->getPaymentModeName('');

			echo json_encode($data);
		} else {

			echo $checkToken;
		}

		exit;
	}

	public function getNomineeRelations()
	{
		$query = $this->db->query("SELECT * from master_nominee_relations");
		$result = $query->result();

		echo json_encode(array("status_code" => "200", "data" => $result));
	}

	public function getNomineeBasedOnRelation()
	{
		if(isset($_POST['source']) && $_POST['source']=="customer"){
			
			$_POST['customer_id']=encrypt_decrypt_password($_POST['customer_id'],'D');
			$_POST['lead_id']=encrypt_decrypt_password($_POST['lead_id'],'D');
		}
		
		$nominee_member_mapping = [
			1 => 2,
			2 => 5,
			3 => 6
		];

		$member_id = $nominee_member_mapping[$this->input->post('nominee_relation')] ?? null;
		if ($member_id) {
			$query = $this->db->query("SELECT * from proposal_policy_member_details where customer_id="
				. $_POST['customer_id'] . " and lead_id=" . $_POST['lead_id']
				. " and relation_with_proposal=" . $member_id);

			$member = $query->result();

			if (isset($member[0])) {
				$member[0]->policy_member_dob = date('d-m-Y', strtotime($member[0]->policy_member_dob));
			}
			echo json_encode($member[0]);
		} else {
			echo json_encode([]);
		}
	}

	function review_page_details(){

		$lead_id=encrypt_decrypt_password($_POST['lead_id'],'D');

		$data['nominee_details']=$this->db->select('p.nominee_first_name,p.nominee_last_name,p.nominee_salutation,p.nominee_gender,p.nominee_dob,p.nominee_contact,n.name')
		->from('proposal_details as p')
		->from('master_nominee_relations as n')
		->where('p.nominee_relation=n.id')
		->where('p.lead_id',$lead_id)
		->get()
		->row_array();
		$data['proposal_member']=
		$this->db->select('p.policy_member_salutation,p.policy_member_gender,p.policy_member_first_name,p.policy_member_last_name,p.policy_member_last_name,p.policy_member_dob,p.policy_member_age,f.member_type')
		->from('proposal_policy_member_details as p')
		->from('family_construct as f')
		->where('p.relation_with_proposal=f.id')
		->where('p.lead_id',$lead_id)
		->get()
		->result_array();

		echo json_encode($data); 
	}

	function saveGHDDeclaration()
	{
		$rows = [];
		$answers = $this->input->post('answers');
		$customer_id = $this->input->post('customer_id');
		$lead_id = $this->input->post('lead_id');

		$anyAnswerDisagreed = false;

		foreach ($answers as $question_id => $values) {
			foreach ($values as $key => $value) {
				if (!$anyAnswerDisagreed && !$value) {
					$anyAnswerDisagreed = true;
				}
				$rows[] = ['customer_id' => $customer_id, 'lead_id' => $lead_id, 'member_id' => $key, 'question_id' => $question_id, 'answer' => $value];
			}
		}

		$this->apimodel->deleteEarlierGHDAnswers($customer_id, $lead_id);
		insert_application_log($lead_id, "ghd_declaration_saved", json_encode($rows), json_encode(array("response" => "GHD Declaration Saved")), $this->input->post('login_user_id'));
		$this->apimodel->insertBatchData('ghd_declaration_answers', $rows);

		if (!$anyAnswerDisagreed) {
			echo json_encode(['status' => 200, 'message' => 'GHD Declaration details saved']);
		} else {
			echo json_encode(['status' => 422, 'message' => 'Please remove the member who is not eligible in the criteria to proceed with the proposal']);
		}
	}


	//Get UW Workflow Data
	function getUWWorkFlowData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = array();
			$get_result['plan_details'] = $this->apimodel->getSortedData("plan_id, creditor_id, plan_name", "master_plan", "plan_id = '" . $_POST['id'] . "'");
			$get_result['uwworkflow_details'] = $this->apimodel->getSortedData("uw_case_id, sum_insured", "uw_cases", "master_plan_id = '" . $_POST['id'] . "'");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getfeaturebyid()
	{
		//echo "<pre>post";json_encode($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = array();
			$get_result['plan_details'] = $this->apimodel->getSortedData("*", "features_config", "id = '" . $_POST['id'] . "'");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addEditFeature(){
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor'])) ? $_POST['creditor'] : '';
			$data['plan_id'] = (!empty($_POST['plan_name'])) ? $_POST['plan_name'] : '';
			$data['feature_id'] = (!empty($_POST['feature'])) ? $_POST['feature'] : '';
			$data['title'] = (!empty($_POST['title'])) ? $_POST['title'] : '';
			$data['short_description'] = (!empty($_POST['short_description'])) ? $_POST['short_description'] : '';
			$data['long_description'] = (!empty($_POST['long_description'])) ? $_POST['long_description'] : '';
			$data['isactive'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['file_name'] = (!empty($_POST['image'])) ? $_POST['image'] : '';
			if (empty($_POST['id'])) {
				$data['created_at'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['id'])) {
				$result = $this->apimodel->updateRecord('features_config', $data, "id='" . $_POST['id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('features_config', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit UW workflow
	function addEditUWWorkflow()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['master_plan_id'] = (!empty($_POST['master_plan_id'])) ? $_POST['master_plan_id'] : '';
			$data['sum_insured'] = (!empty($_POST['sum_insured'])) ? $_POST['sum_insured'] : '';
			if (empty($_POST['uw_case_id'])) {
				$data['created_at'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['uw_case_id'])) {
				$result = $this->apimodel->updateRecord('uw_cases', $data, "uw_case_id='" . $_POST['uw_case_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('uw_cases', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addsinglebranchimd()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$response = [];
			$policy_number = htmlspecialchars(strip_tags(trim($_POST['policy_number'])));
			$branch_code = htmlspecialchars(strip_tags(trim($_POST['branch_code'])));
			$imd_code = htmlspecialchars(strip_tags(trim($_POST['imd_code'])));
			$action = htmlspecialchars(strip_tags(trim($_POST['action'])));

			if ($policy_number == '') {

				$response = ['success' => false, 'msg' => 'Master Policy ID cannot be blank'];
			} else if ($branch_code == '') {

				$response = ['success' => false, 'msg' => 'Branch Code cannot be blank'];
			} else if ($imd_code == '') {

				$response = ['success' => false, 'msg' => 'IMD Code cannot be blank'];
			} else {

				$branch_code = strtoupper($branch_code);
				$imd_code = strtoupper($imd_code);

				if ($action == 'overwrite') {

					$condition = "policy_number='$policy_number' AND branch_code='$branch_code' AND imd_code='$imd_code' AND status='1'";
					$existing_id = $this->apimodel->getdata('branch_imd_mapping', 'branch_imd_map_id', $condition);
					if (!empty($existing_id)) {

						foreach ($existing_id as $id) {

							$update_arr = [
								'status' => '0',
								'updated_by' => $_POST['user_id'],
								'updated_time' => date('Y-m-d H:i:s')
							];
							$this->apimodel->updateRecord('branch_imd_mapping', $update_arr, 'branch_imd_map_id = ' . $id['branch_imd_map_id']);
						}
                        $response = ['success' => true, 'msg' => 'Record Updated Successfully'];
					}
				}else{
                    $condition = "policy_number='$policy_number' AND branch_code='$branch_code' AND imd_code='$imd_code' AND status='1'";
                    $existing_id = $this->apimodel->getdata('branch_imd_mapping', 'branch_imd_map_id', $condition);
                    if (!empty($existing_id)) {

                        $response = ['success' => false, 'msg' => 'Mapping Already Exist!'];
                    }else{
                        $insert_arr = [
                            'branch_code' => $branch_code,
                            'imd_code' => $imd_code,
                            'policy_number' => $policy_number,
                            'status' => '1',
                            'created_by' => $_POST['user_id'],
                            'created_time' => date('Y-m-d H:i:s')
                        ];

                        if ($this->apimodel->insertData('branch_imd_mapping', $insert_arr, 1)) {

                            $response = ['success' => true, 'msg' => 'Record Added Successfully'];
                        }
                    }
                }


			}

			echo json_encode($response);
		} else {
			echo $checkToken;
		}
	}

	function addbulkbranchimd()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$this->load->library('excel');
			$action = htmlspecialchars(strip_tags(trim($_POST['file_action'])));
			$user_id = $_POST['user_id'];
			$upload_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'branch-imd-temp-files';

			if (!file_exists($upload_dir)) {

				mkdir($upload_dir, 0777, true);
			}

			$file_ext = pathinfo($_FILES['import_branch_imd_codes']['name'], PATHINFO_EXTENSION);

			if (!in_array($file_ext, ['xlsx', 'xls', 'csv'])) {

				$response = ['success' => false, 'msg' => 'Only .xlsx, .xls, .csv files allowed'];
			} else {

				$savename = 'branch-imd-' . $user_id . '-' . date('Y-m-d') . '-' . '.' . $file_ext;
				$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
				if (move_uploaded_file($_FILES['import_branch_imd_codes']['tmp_name'], $path)) {

					$object = PHPExcel_IOFactory::load($path);

					$add_data = $overwrite_data = $where_arr = $errorData = [];
					foreach ($object->getWorksheetIterator() as $worksheet) {

						$highestRow = $worksheet->getHighestRow();

						for ($row = 2; $row <= $highestRow; $row++) {

							$policy_number = htmlspecialchars(strip_tags(trim($worksheet->getCellByColumnAndRow(0, $row)->getValue())));
							$branch_code = htmlspecialchars(strip_tags(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue())));
							$imd_code = htmlspecialchars(strip_tags(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue())));

							$branch_code = strtoupper($branch_code);
							$imd_code = strtoupper($imd_code);

							if ($policy_number == '') {

								$errorData[] = "Master Policy Number cannot be blank at row no. $row <br/>";
							}
							if ($branch_code == '') {

								$errorData[] = "Branch Code cannot be blank at row no. $row <br/>";
							}
							if ($imd_code == '') {

								$errorData[] = "IMD Code cannot be blank at row no. $row <br/>";
							}

							if (empty($errorData)) {

								if ($action == 'add') {

									$add_data[] = array(
										'policy_number'   => $policy_number,
										'branch_code'  => $branch_code,
										'imd_code'   => $imd_code,
										'status' => '1',
										'created_by' => $user_id,
										'created_time'	=> date('Y-m-d H:i:s')
									);
								} else if ($action == 'overwrite') {

									$where_arr[] = "policy_number = $policy_number AND branch_code = '$branch_code' AND imd_code = '$imd_code' AND status = '1'";

									$overwrite_data[] = array(
										'policy_number'   => $policy_number,
										'branch_code'  => $branch_code,
										'imd_code'   => $imd_code,
										'status' => '1',
										'created_by' => $user_id,
										'created_time'	=> date('Y-m-d H:i:s')
									);
								}
							}
						}

						if (!empty($errorData)) {

							$response = ['success' => false, 'errorData' => $errorData];
						} else if (!empty($add_data)) {

							if ($this->db->insert_batch('branch_imd_mapping', $add_data)) {

								$response = ['success' => true, 'msg' => 'Records Added Successfully'];
							} else {

								$response = ['success' => false, 'msg' => 'Error Adding Records'];
							}
						} else if (!empty($overwrite_data)) {

							$this->db->select('branch_imd_map_id');
							$this->db->from('branch_imd_mapping');

							for ($i = 0; $i < count($where_arr); $i++) {

								$this->db->where($where_arr[$i]);
							}

							$results = $this->db->get()->result_array();

							if (!empty($results)) {

								$map_ids = [];
								foreach ($results as $result) {

									$map_ids[$result['branch_imd_map_id']] = $result['branch_imd_map_id'];
								}

								if (!empty($map_ids)) {

									$update_arr = [
										'status' => '0',
										'updated_by' => $user_id,
										'updated_time' => date('Y-m-d H:i:s')
									];
									$this->apimodel->updateRecord('branch_imd_mapping', $update_arr, 'branch_imd_map_id IN (' . implode(',', $map_ids) . ')');
								}
							}

							if ($this->db->insert_batch('branch_imd_mapping', $overwrite_data)) {

								$response = ['success' => true, 'msg' => 'Records Overwritten Successfully'];
							} else {

								$response = ['success' => false, 'msg' => 'Error Overwriting Records'];
							}
						}
					}
				} else {

					$response = ['success' => false, 'msg' => 'File upload failed'];
				}
			}

			echo json_encode($response);
		} else {
			echo $checkToken;
		}
	}

	function branchImdListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->branchImdList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function delRecord()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['status'] = 0;
			$data['updated_by'] = $_POST['user_id'];
			$data['updated_time'] = date('Y-m-d H:i:s');

			$id = htmlspecialchars(strip_tags(trim($_POST['id'])));
			$id = encrypt_decrypt_password($id, 'D');

			$result = $this->apimodel->updateRecord('branch_imd_mapping', $data, "branch_imd_map_id = " . $id);
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record deactivated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getbranchimddata()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$id = htmlspecialchars(strip_tags(trim($_POST['id'])));
			$id = encrypt_decrypt_password($id, 'D');

			$result = $this->apimodel->getdata('branch_imd_mapping', 'branch_imd_map_id, policy_number, branch_code, imd_code', 'branch_imd_map_id = ' . $id);

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function updatebranchimd()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$response = [];
			$policy_number = htmlspecialchars(strip_tags(trim($_POST['policy_number'])));
			$branch_code = htmlspecialchars(strip_tags(trim($_POST['branch_code'])));
			$imd_code = htmlspecialchars(strip_tags(trim($_POST['imd_code'])));
			$branch_imd_map_id = htmlspecialchars(strip_tags(trim($_POST['branch_imd_map_id'])));

			if ($policy_number != '' && $branch_code != '' && $imd_code != '' && $branch_imd_map_id != '') {

				$branch_imd_map_id = encrypt_decrypt_password($branch_imd_map_id, 'D');

				$update_arr = [
					'policy_number' => $policy_number,
					'branch_code' => $branch_code,
					'imd_code' => $imd_code
				];

				if ($this->apimodel->updateRecordarr('branch_imd_mapping', $update_arr, 'branch_imd_map_id =' . $branch_imd_map_id)) {

					$response = ['success' => true, 'msg' => 'Record Updated'];
				} else {

					$response = ['success' => false, 'msg' => 'Record Not Updated'];
				}
			} else {

				$response = ['success' => false, 'msg' => 'Invalid data'];
			}

			echo json_encode($response);
		} else {
			echo $checkToken;
		}
	}

	public function checkIfOtpRequired()
	{

		$lead_id_enc = htmlspecialchars(strip_tags(trim($this->input->post('lead_id'))));

		if ($lead_id_enc) {

			$lead_id = encrypt_decrypt_password($lead_id_enc, 'D');

			$lead_details_arr = $this->apimodel->getdata('lead_details ld, short_urls su', 'ld.trace_id, ld.mode_of_payment, ld.status AS lead_status, su.status', 'su.lead_id = ld.lead_id AND su.lead_id = ' . $lead_id);

			if (!empty($lead_details_arr) && in_array($lead_details_arr[0]['mode_of_payment'], [1,3])) {

				if ($lead_details_arr[0]['status'] == 1) { //otp not verified

					$short_url_data = $this->apimodel->getdata('short_urls', 'DATE(link_trigger_time) as link_sent_date', 'lead_id = ' . $lead_id);

					if (isset($short_url_data[0]['link_sent_date'])) {

						$link_sent_date = new DateTime($short_url_data[0]['link_sent_date']);
						$today = new DateTime('today');
						$diff_in_days = $link_sent_date->diff($today, true)->format('%a');

						if($diff_in_days > 7){ //If summary link is visited after 7 days then redirect customer to link expired page

							echo json_encode(['response' => 4, 'msg' => 'Link has expired']);
							exit;
						}
					}

					echo json_encode(['response' => 1]);
				} else if ($lead_details_arr[0]['status'] == 2) { //otp verified

					if ($lead_details_arr[0]['mode_of_payment'] == 1 && strtolower($lead_details_arr[0]['lead_status']) == 'customer-payment-awaiting') { //status in lead table is customer-payment-awaiting then redirect to customer verification page

						//redirect customer to payment gateway
						echo json_encode(['response' => 2]);
					}
					else if ($lead_details_arr[0]['mode_of_payment'] == 3 || strtolower($lead_details_arr[0]['lead_status']) == 'co-approval-awaiting') { //status in lead table is co-approval-awaiting then redirect customer to thank you page in case of neft

						//redirect customer to thank you page
						echo json_encode(['response' => 5, 'trace_id' => $lead_details_arr[0]['trace_id']]);
					}
					else { //status in lead table is other than pending then send response to redirect to thank you page

						echo json_encode(['response' => 3]);
					}
				}
			}
		} else {

			echo json_encode(['response' => 4]);
		}

		exit;
	}

	public function getPaymentModeName($payment_mode_id = '')
	{

		$payment_modes = $this->apimodel->getPaymentModes();

		$payment_mode_name = [];
		foreach ($payment_modes as $payment_mode) {

			$payment_mode_name[$payment_mode->payment_mode_id] = $payment_mode->payment_mode_name;
		}

		if (isset($payment_mode_name[$payment_mode_id])) {

			return $payment_mode_name[$payment_mode_id];
		} else {

			return $payment_mode_name;
		}
	}

	public function getpolicydata()
	{

		$lead_id = encrypt_decrypt_password($_POST['lead_id'], 'D');

		$sql = "SELECT apr.lead_id,apr.policy_sub_type_id,apr.proposal_policy_id,apr.certificate_number,apr.proposal_no,apr.status,apr.coi_url,apr.letter_url,
				mpst.code, pp.proposal_no, ppmd.relation_with_proposal
				FROM api_proposal_response apr
				JOIN proposal_policy pp ON pp.proposal_policy_id = apr.proposal_policy_id,
				JOIN  proposal_policy_member ppm ON ppm.proposal_policy_id = pp.proposal_policy_id,
				JOIN  master_policy_sub_type mpst ON mpst.policy_sub_type_id = apr.policy_sub_type_id,
				WHERE apr.lead_id = $lead_id";

		$coi_data_arr = $this->db->query($sql)->result_array();

		if (!empty($coi_data_arr)) {
		}

		/*$lead_data = $result = [];
		$lead_data = $this->db->query("SELECT trace_id, plan_id, creditor_id FROM lead_details WHERE lead_id = $lead_id")->row_array();

		if(!empty($lead_data)){

			$trace_id = $lead_data['trace_id'];
			$plan_id = $lead_data['plan_id'];
			$creditor_id = $lead_data['creditor_id'];

			$master_policy_data_arr = [];
			$master_policy_data_arr = $this->db->query("SELECT policy_id, policy_sub_type_id FROM master_policy WHERE plan_id = $plan_id AND creditor_id = $creditor_id AND isactive=1")->result_array();
			
			if(!empty($master_policy_data_arr)){
				
				$policy_sub_type_id_arr = [];
				foreach($master_policy_data_arr as $master_policy_data){

					$policy_sub_type_id_arr[$master_policy_data['policy_sub_type_id']] = $master_policy_data['policy_sub_type_id'];
				}

				$master_policy_subtype_arr = $this->db->query("SELECT policy_sub_type_id, code FROM master_policy_sub_type WHERE policy_sub_type_id IN (".implode(',', $policy_sub_type_id_arr).")")->result_array();

				$policy_sub_type_id = [];
				foreach($master_policy_subtype_arr as $master_policy_subtype){

					$policy_sub_type_id[$master_policy_subtype['policy_sub_type_id']] = $master_policy_subtype['code'];
				}		

				$family_construct_arr = $this->db->query("SELECT id, member_type FROM family_construct WHERE isactice = 1")->result_array();
				$family_construct = [];
				foreach($family_construct_arr as $family_construct_row){

					$family_construct[$family_construct_row['id']] = $family_construct_row['member_type'];
				}

				$coi_data_arr = $this->db->query("SELECT lead_id,policy_sub_type_id,proposal_policy_id,certificate_number,proposal_no,status,coi_url,letter_url FROM api_proposal_response WHERE lead_id = $lead_id")->result_array();
				
				if(!empty($coi_data_arr)){
					
					foreach($coi_data_arr as $coi_data){
						
						$proposal_policy_nos[$coi_data['proposal_policy_id']] = $coi_data['proposal_policy_id'];

					}

					$policy_proposal_arr = $this->db->query("SELECT proposal_policy_id,lead_id,trace_id,proposal_no,master_policy_idFROM proposal_policy WHERE lead_id = $lead_id");
				}
			}
		}*/
	}

	/**
	 * Cron to perform reinsurance for failed policies
	 */
	public function doReinsuranceCron()
	{

		$condition = "(transaction_date BETWEEN '" . date('Y-m-d') . " 00:00:00' AND '" . date('Y-m-d') . " 23:59:59') AND payment_status='Success' AND proposal_status != 'Success' AND issuance_count < 3 LIMIT 10";

		$lead_id_arr = $this->apimodel->getdata('proposal_payment_details', 'lead_id, issuance_count', $condition);
		//echo "<pre>";print_r($lead_id_arr);exit;
		if (!empty($lead_id_arr)) {

			foreach ($lead_id_arr as $lead_id) {

				$data_arr = ['lead_id' => $lead_id['lead_id'], 'created_at' => date('Y-m-d H:i:s')];
				$id = $this->apimodel->insertData('do_reinsurance_cron_logs', $data_arr, 1);
				$doQuickQuote = $this->apimodel->doQuickQuote($lead_id['lead_id']);
				$full_quote_status = $this->apimodel->doFullQuote($lead_id['lead_id']);

				$data_arr = ['status' => ($full_quote_status['Status'] ?? NULL), 'updated_at' => date('Y-m-d H:i:s')];

				$this->apimodel->updateRecord('proposal_payment_details', ['issuance_count'	=> $lead_id['issuance_count'] + 1], 'lead_id = ' . $lead_id['lead_id']);
				$this->apimodel->updateRecord('do_reinsurance_cron_logs', $data_arr, 'id = ' . $id);
			}
		}
	}

	public function checkPayMethodAndComunnicate()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = $this->input->post('lead_id');

			/*$tables = "lead_details ld, master_customer mc";
			$column = "mc.first_name, mc.last_name, ld.status, ld.mobile_no, ld.email_id, ld.trace_id, ld.plan_id, ld.mode_of_payment, ld.createdby";
			$condition = "ld.lead_id = $lead_id AND ld.lead_id = mc.lead_id AND ld.status = 'Pending' AND ld.mode_of_payment IN (1,3)";

			$proposal_status = $this->apimodel->getdata($tables, $column, $condition);*/

			$table = 'master_customer mc';
			$column = 'mc.first_name, mc.middle_name, mc.last_name, ld.status, ld.mobile_no, ld.email_id, ld.trace_id, ld.plan_id, ld.mode_of_payment, ld.createdby, ld.lan_id, ld.lan_id';
			$join_table = 'lead_details ld';
			$join_on = 'ld.primary_customer_id = mc.customer_id';
			$join_type = 'INNER';
			$where = "ld.lead_id = $lead_id AND ld.status = 'Pending' AND ld.mode_of_payment IN (1,3)";
			
			$proposal_status = $this->apimodel->get_data_with_join($table, $column, $join_table, $join_on, $join_type, $where);

			if (!empty($proposal_status)) {

				//$alert_ids = $this->input->post('alert_ids');

				if($proposal_status[0]['mode_of_payment'] == 1){

					$alert_ids = ['A1655', 'A1656'];
				}
				else if($proposal_status[0]['mode_of_payment'] == 3){

					$alert_ids = ['A1657', 'A1658'];
				}

				$get_short_url = $this->apimodel->getdata('short_urls', 'short_code', 'lead_id = ' . $lead_id);

				$plan_name = $this->apimodel->getdata('master_plan', 'plan_name', 'plan_id = ' . $proposal_status[0]['plan_id']);

				$employee_data = $this->apimodel->getdata('master_employee', 'Concat("employee_fname", "employee_lname") AS emp_name, email_id, mobile_number', 'employee_id = ' . $proposal_status[0]['createdby']);

				if (isset($get_short_url[0]['short_code']) && $get_short_url[0]['short_code'] != '') {

					$data['lead_id'] = $lead_id;
					$data['mobile_no'] = $proposal_status[0]['mobile_no'];
					$data['plan_id'] = $proposal_status[0]['plan_id'];
					$data['email_id'] = $proposal_status[0]['email_id'];
					$data['alerts'][] = $proposal_status[0]['first_name'] . ' ' . $proposal_status[0]['last_name'];
					$data['alerts'][] = $plan_name[0]['plan_name'] ?? '';
					$data['alerts'][] =  $get_short_url[0]['short_code'];
					$data['alerts'][] = $proposal_status[0]['trace_id'];
					$data['alerts'][] = 'Click Here';

					$response = triggerCommunication([$alert_ids[0]], $data);

					if (ENV_BYPASSED || $response['status_code'] == 200) { //to remove bypass

						$arr = ['status' => 'Client-Approval-Awaiting'];

						$this->apimodel->updateRecordarr('lead_details', $arr, 'lead_id = ' . $lead_id);

						insert_application_log($_POST['lead_id'], "send_summary_link_customer", json_encode($_POST), json_encode(array_merge($data, $arr)), $_POST['user_id']);
					}

					insert_application_log($_POST['lead_id'], "send_summary_link_customer", json_encode($_POST), json_encode($response), $_POST['user_id']);

					if(isset($employee_data[0]) && !empty($employee_data[0])){
						
						$data = [];
						$data['lead_id'] = $lead_id;
						$data['mobile_no'] = $employee_data[0]['mobile_number'];
						$data['plan_id'] = $proposal_status[0]['plan_id'];
						$data['email_id'] = $employee_data[0]['email_id'];
						$data['alerts'][] = $proposal_status[0]['first_name'] . ' ' . $proposal_status[0]['last_name'];
						$data['alerts'][] = $plan_name[0]['plan_name'] ?? '';
						$data['alerts'][] = $employee_data[0]['emp_name'];
						$data['alerts'][] = $proposal_status[0]['trace_id'];
						$data['alerts'][] = $proposal_status[0]['lan_id'];

						$agent_response = triggerCommunication([$alert_ids[1]], $data);

						insert_application_log($_POST['lead_id'], "send_summary_link_agent", json_encode($data), json_encode($agent_response), $_POST['user_id']);
					}

					echo json_encode($response);
				}
			} else {

				insert_application_log($_POST['lead_id'], "send_summary_link", json_encode($_POST), json_encode(['response' => 0]), $_POST['user_id']);
				echo json_encode(['response' => 0]);
			}
		} else {
			insert_application_log($_POST['lead_id'], "send_summary_link", json_encode($_POST), json_encode($checkToken), $_POST['user_id']);
			echo $checkToken;
		}
		exit;
	}

	public function testcommon()
	{

		var_dump(checkUWCase(100, 11, 406));
	}

	public function retriggerLink()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$lead_id = htmlspecialchars(strip_tags(trim($this->input->post('lead_id'))));
			$lead_id = encrypt_decrypt_password($lead_id, 'D');

			if ($lead_id != '') {

				$short_url_data = $this->apimodel->getdata('short_urls', 'short_code, link_trigger_count, link_trigger_time', 'lead_id = ' . $lead_id);

				if (!empty($short_url_data)) {

					$link_trigger_time = $short_url_data[0]['link_trigger_time'];

					if (strtotime($link_trigger_time) > strtotime("-15 minutes")) {

						$alert_ids = $this->input->post('alert_ids');
						//$get_short_url = $this->apimodel->getdata('short_urls', 'short_code', 'lead_id = ' . $lead_id);
						$tables = "lead_details ld, master_customer mc";
						$column = "mc.first_name, mc.last_name, ld.status, ld.mobile_no, ld.email_id, ld.trace_id, ld.plan_id, ld.mode_of_payment";
						$condition = "ld.lead_id = $lead_id AND ld.lead_id = mc.lead_id AND ld.status = 'Client-Approval-Awaiting' AND ld.mode_of_payment = 1";

						$proposal_status = $this->apimodel->getdata($tables, $column, $condition);

						$plan_name = $this->apimodel->getdata('master_plan', 'plan_name', 'plan_id = ' . $proposal_status[0]['plan_id']);

						if (!empty($proposal_status)) {

							if (isset($short_url_data[0]['short_code']) && $short_url_data[0]['short_code'] != '') {

								$data['lead_id'] = $lead_id;
								$data['mobile_no'] = $proposal_status[0]['mobile_no'];
								$data['plan_id'] = $proposal_status[0]['plan_id'];
								$data['email_id'] = $proposal_status[0]['email_id'];
								$data['alerts'][] = $proposal_status[0]['first_name'] . ' ' . $proposal_status[0]['last_name'];
								$data['alerts'][] = $plan_name[0]['plan_name'] ?? '';
								$data['alerts'][] = $short_url_data[0]['short_code'];
								$data['alerts'][] = $proposal_status[0]['trace_id'];
								$data['alerts'][] = 'Click Here';

								$com_response = triggerCommunication($alert_ids, $data);

								insert_application_log($_POST['lead_id'], "short_url_trigger_customer", json_encode($data), json_encode($com_response), $_POST['user_id']);

								if (ENV_BYPASSED ||$com_response['status_code'] == 200) {

									//$this->apimodel->updateRecordarr('lead_details', ['status' => 'Client-Approval-Awaiting'], 'lead_id = '.$lead_id);

									$arr = [
										'link_trigger_count' => $short_url_data[0]['link_trigger_count'] + 1,
										'link_trigger_time' => date('Y-m-d H:i:s'),
										'updated' => date('Y-m-d H:i:s')
									];
									$this->apimodel->updateRecordarr('short_urls', $arr, 'lead_id = ' . $lead_id);

									$response = ['success' => true, 'msg' => 'Link Sent To Customer'];

									insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($data), json_encode(array_merge($arr, $response)), $_POST['user_id']);
								}
								else{

									$response = ['success' => false, 'msg' => 'Something went wrong'];
									insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
								}
							} else {

								$response = ['success' => false, 'msg' => 'Link not generated previously'];
								insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
							}
						} else {

							$response = ['success' => false, 'msg' => 'No Leads found'];
							insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
						}
					} else {

						$response = ['success' => false, 'msg' => 'Link can be retriggered only after fifteen minutes'];
						insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
					}
				} else {

					$response = ['success' => false, 'msg' => 'Not short URL found'];
					insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
				}
			} else {

				$response = ['success' => false, 'msg' => 'Invalid Lead ID'];
				insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
			}
		} else {

			$response = $checkToken;
			insert_application_log($_POST['lead_id'], "retrigger_link", json_encode($_POST), json_encode($response), $_POST['user_id']);
		}

		echo json_encode($response);
		exit;
	}

	public function getPlanDetailsForLead()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$creditor_id = htmlspecialchars(strip_tags(trim($this->input->post('creditor_id'))));
			$plan_id = htmlspecialchars(strip_tags(trim($this->input->post('plan_id'))));

			$data = $this->apimodel->get_min_max_age($creditor_id, $plan_id);

			$min_age = $max_age = 0;
			$master_policy_id_arr = [];
			$tenure = [];
			$response = [];

			if (!empty($data)) {

				$min_age = $data[0]['member_min_age'];
				$max_age = $data[0]['member_max_age'];

				foreach ($data as $key => $value) {

					if ($min_age > $value['member_min_age']) {

						$min_age = $value['member_min_age'];
					}

					if ($max_age < $value['member_max_age']) {

						$max_age = $value['member_max_age'];
					}

					//$master_policy_id_arr[$value['master_policy_id']] = $value['master_policy_id'];
				}
			}


			/*if(!empty($master_policy_id_arr)){

				$data = $this->apimodel->getTenureForPolicies($master_policy_id_arr);

				if(!empty($data)){

					foreach($data as $tenure){

						$tenure[$tenure->tenure] = $tenure->tenure;
					}

					sort($tenure);
				}
			}*/

			$data = [
				'min_age' => $min_age,
				'max_age' => $max_age,
				//'tenure' => $tenure
			];

			$response = ['status' => 200, 'data' => $data];
		} else {

			$response = $checkToken;
		}

		echo json_encode($response);
		exit;
	}

	function doCommunicateTest()
	{
		$request = file_get_contents('php://input');
		$url = "http://10.1.226.32/ABHICL_ClickPSS/Service1.svc/click";
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 90,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $request,
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen($request),
				"Content-Type: application/json"
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		if ($response == '' || $response == NULL) {
			$response = $err;
		}
		if ($err) {
			$return = array(
				"status" => "Error",
				"msg" => $err
			);
		} else {
			//return $response;
		}

		print_r($response);
		print_r($err);
	}

	function fetchFeature(){
		$query = $this->db->query("SELECT * from master_plan where creditor_id = '".$_POST['creditor_id']."' and isactive = 1")->result_array();
		echo json_encode($query);
	}
} // EO class Api2
