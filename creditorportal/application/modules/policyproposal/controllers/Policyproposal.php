<?php header('Access-Control-Allow-Origin: *');
if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Policyproposal extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		if(!(isset($_POST['source']) && $_POST['source'] != 'customer')){

			checklogin();
			$this->RolePermission = getRolePermissions();
		}
	}

	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('policysubtype/index');
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$insurerListing = curlFunction(SERVICE_URL . '/api2/PolicySubTypeListing', $_GET);
		$insurerListing = json_decode($insurerListing, true);
		//echo "<pre>";print_r($insurerListing);exit;
		if ($insurerListing['status_code'] == '401') {
			//echo "in condition";
			redirect('login');
			exit();
		}


		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"] = $_GET['sEcho'];

		$result["iTotalRecords"] = $insurerListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"] = $insurerListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();

		if (!empty($insurerListing['Data']['query_result']) && count($insurerListing['Data']['query_result']) > 0) {
			for ($i = 0; $i < sizeof($insurerListing['Data']['query_result']); $i++) {
				$temp = array();
				array_push($temp, $insurerListing['Data']['query_result'][$i]['policy_sub_type_name']);
				array_push($temp, $insurerListing['Data']['query_result'][$i]['typename']);

				if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
					array_push($temp, 'Active');
				} else {
					array_push($temp, 'In-Active');
				}

				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
				$actionCol .= '<a href="policysubtype/addEdit?text=' . rtrim(strtr(base64_encode("id=" . $insurerListing['Data']['query_result'][$i]['policy_sub_type_id']), '+/', '-_'), '=') . '" title="Edit"><i class="fa fa-edit"></i></a>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
				if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
					$actionCol .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\'' . $insurerListing['Data']['query_result'][$i]['policy_sub_type_id'] . '\');" title="Delete"><i class="fa fa-trash"></i></a>';
				}
				//}

				array_push($temp, $actionCol);
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}

	function viewdetails()
	{
		$record_id = "";
		//print_r($_GET);
		if (!empty($_GET['text']) && isset($_GET['text'])) {
			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$record_id = $url_prams['id'];
		}
		$result = array();


		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL . '/api2/getLeadDetails', $data));

		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/viewdetails', $result);
		$this->load->view('template/footer.php');
	}

	function submitsummary()
	{
		if($this->input->is_ajax_request()){

			$lead_id = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));
			$result = [];

			if($lead_id != ''){

				$varr = base64_decode(strtr($lead_id, '-_', '+/'));
				parse_str($varr, $url_prams);
				$lead_id = $url_prams['id'];

				$data['lead_id'] = $lead_id;
				$data['utoken'] = $_SESSION['webpanel']['utoken'];
				$data['user_id'] = $_SESSION['webpanel']['employee_id'];
				//$data['alert_ids'] = ['A1655', 'A1656'];

				$resp = json_decode(curlFunction(SERVICE_URL . '/api2/checkPayMethodAndComunnicate', $data), true);
				//var_dump($resp);die;
                if (array_key_exists('payment_mode',$resp) && $resp['payment_mode'] == 4) {
                    $mainurl='/policyproposal/success_view/'.encrypt_decrypt_password($lead_id, 'E');
                    $result = ['success' => true, 'link' => $mainurl,'code'=>205];
                    echo json_encode($result);
                    exit;
                }
				if(isset($resp['status_code'])){ //to remove bypass

					if(true || $resp['status_code'] == 200){ //to remove bypass

						$result = ['success' => true, 'msg' => "Summary link sent to customer"];
					}
					else{
						
						if(isset($resp['msg'])){
							
							$result = ['success' => false, 'msg' => $resp['msg']];
						}
					}
				}
				else if(isset($resp['status']) && $resp['status'] == 'Error'){

					$result = ['success' => false, 'msg' => "Something went wrong while sending link to the customer"];
				}
				else if(isset($resp['response'])){

					$result = ['success' => true, 'msg' => "Summary Submitted"];
				}
				else{

					$result = ['success' => false, 'msg' => "Something went wrong"];
				}
			}
			else{

				$result = ['success' => false, 'msg' => "Invalid Lead ID"];
			}

			echo json_encode($result);
		}
	}

	/*function docustomervalidation(){

		$data['lead_id'] = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));

		$result = curlFunction(SERVICE_URL . '/api2/sendcustomerpaymentformotp', $data);

		echo $result;exit;
	}*/

	function addEdit()
	{
		$result = $this->createProposalViewData('addEdit');

		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/addEdit', $result);
		$this->load->view('template/footer.php');
	}

	public function preview()
	{
		$result = $this->createProposalViewData('preview');
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/addEdit', $result);
		$this->load->view('template/footer.php');
	}

	function createProposalViewData($mode = 'addedit', $lead_id = null)
	{
		if (!$lead_id) {
			$record_id = "";

			if (!empty($_GET['text']) && isset($_GET['text'])) {
				$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
				parse_str($varr, $url_prams);
				$record_id = $url_prams['id'];
			}
		} else {
			$record_id = $lead_id;
		}

		$result = array();

		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];

		$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL . '/api2/getLeadDetails', $data));
		//print_r($result['leaddetails'] );die;
	//	var_dump($result['leaddetails']->master_policy_details[0]->basis_id);exit;

		$status = $result['leaddetails']->customer_details[0]->status;

		$result['is_spouse_age_required'] = false;
		$plan_id = $result['leaddetails']->plan_details[0]->plan_id;

		$policies = $result['leaddetails']->plan_details;

		$insured_member_criterias = $family_constuct_relation_map = [];

		for ($i = 0; $i < count($result['leaddetails']->family_members); $i++) {

			$family_constuct_relation_map[$result['leaddetails']->family_members[$i]->id]['member_type'] = $result['leaddetails']->family_members[$i]->member_type;
			$family_constuct_relation_map[$result['leaddetails']->family_members[$i]->id]['is_adult'] = $result['leaddetails']->family_members[$i]->is_adult;
		}

		$result['family_constuct_relation_map'] = $family_constuct_relation_map;

		foreach ($policies as $policy) {

			if (!$result['is_spouse_age_required']) {

				if ($policy->basis_id == 3 || $policy->basis_id == 6 || $policy->basis_id == 5) {
					$result['is_spouse_age_required'] = true;
					//break;
				}
			}

			for ($i = 0; $i < count($policy->family_construct); $i++) {

				$relation = $policy->family_construct[$i]->member_type_id;
				$master_policy_id = $policy->family_construct[$i]->master_policy_id;
				$insured_member_criterias[$master_policy_id][$relation]['member_type_id'] = $relation;
				$insured_member_criterias[$master_policy_id][$relation]['member_min_age'] = $policy->family_construct[$i]->member_min_age;
				$insured_member_criterias[$master_policy_id][$relation]['member_min_age_days'] = $policy->family_construct[$i]->member_min_age_days;
				$insured_member_criterias[$master_policy_id][$relation]['member_max_age'] = $policy->family_construct[$i]->member_max_age;
			}
		}

		$result['insured_member_criterias'] = $insured_member_criterias;

		$maxSpouseAge = null;
		$minSpouseAge = null;
		$maxSelfAge = null;
		$minSelfAge = null;

		$maxSpouseAge = $this->calculateMaxAgeForMember(2, $policies);
		$minSpouseAge = $this->calculateMinAgeForMember(2, $policies);
		$maxSelfAge = $this->calculateMaxAgeForMember(1, $policies);
		$minSelfAge = $this->calculateMinAgeForMember(1, $policies);

		$result['validations']['maxSpouseAge'] = $maxSpouseAge;
		$result['validations']['minSpouseAge'] = $minSpouseAge;
		$result['validations']['maxSelfAge'] = $maxSelfAge;
		$result['validations']['minSelfAge'] = $minSelfAge;

		$result['validations']['minSelfDob'] = date('Y-m-d', strtotime('-' . $minSelfAge . ' year'));
		$result['validations']['minSpouseDob'] = date('Y-m-d', strtotime('-' . $minSpouseAge . ' year'));
		$result['validations']['maxSelfDob'] = date('Y-m-d', strtotime('-' . $maxSelfAge . ' year'));
		$result['validations']['maxSpouseDob'] = date('Y-m-d', strtotime('-' . $maxSpouseAge . ' year'));

		$coapplicant_tab_id = null;

		if (isset($_GET['coapplicant_tab_id'])) {

			$coapplicant_tab_id = $_GET['coapplicant_tab_id'];
			$result['coapplicant_tab_id'] = $coapplicant_tab_id;
		}

		$adult_count = $child_count = 0;

		if (!empty($result['leaddetails']->plan_details)) {
			foreach ($result['leaddetails']->plan_details as $key => $value) {
				if ($value->policy_sub_type_id == 1) {
					foreach ($result['leaddetails']->plan_details[0]->family_construct as $key => $value) {
						$arr_member_type_id = array(1, 2, 3, 4);

						if (in_array($value->member_type_id, $arr_member_type_id)) {
							$adult_count++;
						} else {
							$child_count++;
						}
					}
				}
			}
		}

		if ($coapplicant_tab_id) {
			if (isset($result['leaddetails']->customer_details[$coapplicant_tab_id])) {

				$result['customer'] = $result['leaddetails']->customer_details[$coapplicant_tab_id];

				$self_age = 0;

				if(isset($result['customer']->dob)){

					$from = new DateTime($result['customer']->dob);
					$to   = new DateTime('today');
					$self_age = $from->diff($to)->y;
				}

				$result['self_age'] = $self_age;

				$req_data = [
					'customer_id' => $result['customer']->customer_id,
					'lead_id' => $record_id,
					'plan_id' => $plan_id,
					'utoken' => $_SESSION['webpanel']['utoken']
				];
				$member_details = json_decode(curlFunction(SERVICE_URL . '/api2/getInsuredMemberDetails', $req_data));

				$result['co_applicant_member_details'] = $member_details;

				$member_count = ['adult_count' => $adult_count, 'child_count' => $child_count];
				$result['member_count'] = $member_count;
			} else {
				// If we dont have the coapplicant created we will create a empty customer object
				$cloned_customer = clone $result['leaddetails']->customer_details[0];

				$cloned_customer->lan_id = null;
				$cloned_customer->loan_amt = null;
				$cloned_customer->tenure  = null;
				$cloned_customer->mobile_no   = null;
				$cloned_customer->email_id   = null;
				$cloned_customer->customer_mobile_no   = null;
				$cloned_customer->customer_mobile_no2   = null;
				$cloned_customer->address_line1   = null;
				$cloned_customer->address_line2   = null;
				$cloned_customer->address_line3   = null;
				$cloned_customer->pincode   = null;
				$cloned_customer->city   = null;
				$cloned_customer->state   = null;
				$cloned_customer->salutation   = null;
				$cloned_customer->customer_id   = null;
				$cloned_customer->first_name   = null;
				$cloned_customer->middle_name   = null;
				$cloned_customer->last_name   = null;
				$cloned_customer->gender   = null;
				$cloned_customer->dob   = null;
				$cloned_customer->proposal_details_id = null;
				$result['customer'] = $cloned_customer;

				$member_count = ['adult_count' => $adult_count, 'child_count' => $child_count];
				$result['member_count'] = $member_count;
			}
		} else {
			$result['customer'] = $result['leaddetails']->customer_details[0];

			$self_age = 0;

			if(isset($result['customer']->dob)){

				$from = new DateTime($result['customer']->dob);
				$to   = new DateTime('today');
				$self_age = $from->diff($to)->y;
			}

			$result['self_age'] = $self_age;

			$req_data = [
				'customer_id' => $result['customer']->customer_id,
				'lead_id' => $record_id,
				'plan_id' => $plan_id,
				'utoken' => $_SESSION['webpanel']['utoken']
			];
			$member_details = json_decode(curlFunction(SERVICE_URL . '/api2/getInsuredMemberDetails', $req_data));

			$result['applicant_member_details'] = $member_details;

			$member_count = ['adult_count' => $adult_count, 'child_count' => $child_count];
			$result['member_count'] = $member_count;
		}

		foreach ($result['leaddetails']->proposal_details as $key => $proposalDetails) {
			if ($result['customer']->customer_id == $proposalDetails->customer_id) {
				$result['current_proposal_details'] = $proposalDetails;
				break;
			}
		}

		if (isset($result['current_proposal_details'])) {
			$result['customer']->proposal_details_id = $result['current_proposal_details']->proposal_details_id;
		}


		$result['generated_premium'] = json_decode(curlFunction(SERVICE_URL . '/api2/getGeneratedPremiums', $data));

		if ($result['generated_premium']) {
			$result['generated_premium'] = $result['generated_premium']->data;
		}

		$result['generated_quote'] = json_decode(curlFunction(SERVICE_URL . '/api2/getGeneratedQuote', [
			'customer_id' => $result['customer']->customer_id,
			'lead_id' => $record_id
		]));
		//print_r($result['generated_quote']);die;

		$result['master_quote_ids'] = json_decode(curlFunction(SERVICE_URL . '/api2/getMasterQuoteIds', [
			'customer_id' => $result['customer']->customer_id,
			'lead_id' => $record_id
		]));

		/*$result['proposal_member_id'] = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalMemberID', [
			'customer_id' => $result['customer']->customer_id,
			'lead_id' => $record_id,
			'utoken' => $_SESSION['webpanel']['utoken']
		]));*/

		$result['nominee_relations'] = json_decode(curlFunction(SERVICE_URL . '/api2/getNomineeRelations', []))->data;

		if ($mode == 'preview') {
			$result['is_only_previewable'] = true;
		} else {
			$result['is_only_previewable'] = false;
		}

		$assignment_declaration_arr = json_decode(curlFunction(SERVICE_URL . '/api2/getAssignmentDeclaration', []), true);

		$sum_insured = $result['leaddetails']->sum_insured;

		if ($sum_insured > 0) {

			$sum_insured = number_format($sum_insured, 2, '.', ',');
		}

		$assignment_declaration = '';
		if (!empty($assignment_declaration_arr)) {

			$assignment_declaration = $assignment_declaration_arr['content'];

			$customer_name = $result['customer']->first_name . ' ' . $result['customer']->middle_name . ' ' . $result['customer']->last_name;
			$assignment_declaration = str_replace('@@APPLICANT_NAME@@', $customer_name, $assignment_declaration);
			$assignment_declaration = str_replace('@@LAN_ID@@', $result['customer']->lan_id, $assignment_declaration);
			$assignment_declaration = str_replace('@@CREDITOR_NAME@@', $result['leaddetails']->plan_details[0]->creditor_name, $assignment_declaration);
			$assignment_declaration = str_replace('@@SUM_INSURED@@', "<span id=\"pp_sum_insured\">$sum_insured Rs</span>", $assignment_declaration);
		}

		$result['assigment_declaration'] = $assignment_declaration;

		$result['options']['tenure'] = $this->prepareTenureOptions($result['leaddetails']->plan_details);

		$result['family_constructs'] = $this->prepareFamilyConstruct($result['leaddetails']->plan_details);

		return $result;
	}

	function getProposalPolicySumInsured()
	{

		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalPolicySumInsured', $data), true);

		if (isset($result['status_code']) && $result['status_code'] == 200) {
			echo json_encode(array('success' => true, 'sum_insured' => $result['Metadata']['sum_insured']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'sum_insured' => 0));
			exit;
		}
	}

	protected function prepareFamilyConstruct($policies)
	{
		$constructs = [];

		foreach ($policies as $policy) {
//            var_dump($policy);
//echo ;
			$kid_count = 0;
			$adult_count = 0;

			$members = $policy->family_construct;

			foreach ($members as $key => $construct) {
				$adult_member_type_id = array(1, 2, 3, 4);

				if (in_array($members[$key]->member_type_id, $adult_member_type_id)) {
					$adult_count++;
				} else {
					$kid_count++;
				}

				$construct = $adult_count . '-' . $kid_count;

				if (!array_key_exists($construct, $constructs)) {
					$adultsDisplayName = $adult_count == 1 ? 'Adult' : 'Adults';
					$kidDisplayName = $kid_count == 1 ? 'Kid' : 'Kids';

					$constructs[$construct] = $adult_count . ' ' . $adultsDisplayName;

					if ($kid_count) {
						$constructs[$construct] .= " + " . $kid_count . ' ' . $kidDisplayName;
					}
				}
			}
		}
	//	echo $adult_count;
//		exit;

		return $constructs;
	}

	protected function calculateMaxAgeForMember($member_id, $policies)
	{
		$age = null;

		foreach ($policies as $policy) {
			$family_construct = $policy->family_construct;
			foreach ($family_construct as $construct) {
				if ($construct->member_type_id != $member_id) {
					continue;
				}
				if (is_null($age) || $age < $construct->member_max_age) {
					$age = $construct->member_max_age;
				}
			}
		}

		return $age;
	}

	protected function calculateMinAgeForMember($member_id, $policies)
	{
		$age = null;

		foreach ($policies as $policy) {
			$family_construct = $policy->family_construct;
			foreach ($family_construct as $construct) {
				if ($construct->member_type_id != $member_id) {
					continue;
				}
				if (is_null($age) || $age < $construct->member_min_age) {
					$age = $construct->member_min_age;
				}
			}
		}

		return $age;
	}


	function addPolicyProposalView($id)
	{
		$mode = isset($_GET['is_only_previewable'])  && $_GET['is_only_previewable'] ? 'preview' : 'addEdit';
		$result = $this->createProposalViewData($mode, $id);
		//print_r($result);die;
		$html = $this->load->view('policyproposal/addEdit_load', $result); // changes 
		echo $html;
	}

	function saveAssignmentDeclaration()
	{

		if ($this->input->is_ajax_request()) {

			if (isset($_POST['lead_id']) && $_POST['lead_id'] != '') {

				if (isset($_POST['value']) && $_POST['value'] != '') {

					$_POST['user_id'] = $_SESSION['webpanel']['employee_id'];
					$result = curlFunction(SERVICE_URL . '/api2/saveAssignmentDeclaration', $_POST);

					if ($result) {

						$response = ['success' => true, 'msg' => 'Record Updated'];

						echo json_encode($response);
					}
				}
			}
		}

		//exit;
	}

	function generateQuote()
	{
//		 print_r($_POST);exit;

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/generateQuote', $_POST));
//		$result = (curlFunction(SERVICE_URL . '/api2/generateQuote', $_POST));

		// var_dump($result);exit;
		if (isset($result->status) && $result->status == 'success') {

			$response = json_encode(array('success' => true, 'data' => $result->data, "messages" => $result->messages));

			if(isset($_POST['source']) && $_POST['source'] == 'customer'){

				$_SESSION['generated_quote'] = json_encode($result->data);
				$_SESSION['plan_id'] = $_POST['plan_id'];
			}

			echo $response;
			exit;
		} else {
			echo json_encode(array('success' => false, "data" => [], "messages" => $result->messages));
			exit;
		}
	}

	function getPremiumSummary()
	{

		if ($this->input->is_ajax_request()) {

			if (isset($_POST['record']) && $_POST['record'] != '') {

				/*$record = htmlspecialchars(strip_tags(trim($_POST['record'])));
				$varr = base64_decode(strtr($record, '-_', '+/'));
				parse_str($varr, $url_prams);
				$lead_id = $url_prams['id'];*/

				$lead_id = $_POST['record'];

				$premiums = json_decode(curlFunction(SERVICE_URL . '/api2/getPremiumSummary', ['lead_id' =>  $lead_id]), true);
				$this->load->view('policyproposal/premium-summary', $premiums);
			}
		}
	}

	protected function prepareTenureOptions($policies)
	{
		$perMilePolicies = [];
		foreach ($policies as $policy) {
			if ($policy->basis_id != 5) {
				continue;
			}

			$perMilePolicies[] = $policy->policy_id;
		};

		$tenures = json_decode(curlFunction(SERVICE_URL . '/api2/getTenureForPolicies', ['policies' =>  $perMilePolicies]));

		return $tenures;
	}


	function changePaymentMode()
	{
		$record_id = "";
		//print_r($_GET);
		if (!empty($_GET['text']) && isset($_GET['text'])) {
			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$record_id = $url_prams['id'];
		}

		$result = array();

		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];

		$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL . '/api2/getLeadDetails', $data));
		$result['changepaymentmodeonly'] = 1;
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/addEdit', $result);
		$this->load->view('template/footer.php');
	}

	function submitForm()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];

		if (!empty(trim($this->input->post('lead_id')))) {
			$addEdit = curlFunction(SERVICE_URL . '/api2/addEditPolicyProposalCustDetails', array_merge($data, $this->input->post()));
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);

			if ($addEdit['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'self_age' => $addEdit['Metadata']['self_age']));
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

	function coapplicantsubmitForm()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION['webpanel']['employee_id'];

		if (!empty(trim($this->input->post('lead_id')))) {
			$addEdit = curlFunction(SERVICE_URL . '/api2/addEditPolicyProposalCoapplicantDetails', array_merge($data, $this->input->post()));
			$addEdit = json_decode($addEdit, true);

			if ($addEdit['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'Data' => $addEdit['Data'], 'self_age' => $addEdit['Metadata']['self_age']));
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

	function getBankDetails()
	{

		if (isset($_POST)) {

			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['user_id'] = $_SESSION['webpanel']['employee_id'];
			$data['data'] = $_POST;

			$addEdit = curlFunction(SERVICE_URL . '/api2/getBankDetails', $data);
			$addEdit = json_decode($addEdit, true);

			if ($addEdit['status_code'] == '200') {

				echo json_encode(array('success' => true, 'msg' => $addEdit['Data']));
			} else {

				echo json_encode(array('success' => false, 'msg' => 'Something Went Wrong'));
			}
		}

		exit;
	}

	function submitmemberForm()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['trace_id']  = $_POST['trace_id'];
		$data['proposal_details_id'] = $_POST['proposal_details_id'];
		$data['sum_insured'] = (!empty($_POST['sum_insured'])) ? $_POST['sum_insured'] : '';
		$data['adultcount'] = (!empty($_POST['adultcount'])) ? $_POST['adultcount'] : '';
		$data['childcount'] = (!empty($_POST['childcount'])) ? $_POST['childcount'] : '';
		$data['family_members_id'] = (!empty($_POST['family_members_id'])) ? $_POST['family_members_id'] : '';
		$family_member_name = (!empty($_POST['family_members_name'])) ? $_POST['family_members_name'] : '';
		$data['family_salutation'] = (!empty($_POST['family_salutation'])) ? $_POST['family_salutation'] : '';
		$data['family_gender'] = (!empty($_POST['family_gender'])) ? $_POST['family_gender'] : '';
		$data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
		$data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
		$data['family_date_birth'] = (!empty($_POST['family_date_birth'])) ? $_POST['family_date_birth'] : '';
		$data['age'] = (!empty($_POST['age'])) ? $_POST['age'] : '';
		$data['sitypes'] = (!empty($_POST['sitypes'])) ? $_POST['sitypes'] : '';
		$data['policy_nos'] = (!empty($_POST['policy_nos'])) ? $_POST['policy_nos'] : '';
		$data['sibasis'] = (!empty($_POST['sibasis'])) ? $_POST['sibasis'] : '';
		$data['policy_member_pan'] = (!empty($_POST['policy_member_pan'])) ? $_POST['policy_member_pan'] : '';

		if (!empty($data['lead_id'])) {

			$addEdit = json_decode(curlFunction(SERVICE_URL . '/api2/addProposalMember', $data), true);

			if ($addEdit['status_code'] == '200') {
				$html = "<tr>";
				$html .= "<td>" . $family_member_name . "</td>";
				$html .= "<td>" . $data['first_name'] . "</td>";
				$html .= "<td>" . $data['last_name'] . "</td>";
				$html .= "<td>" . $data['family_gender'] . "</td>";
				$html .= "<td>" . $data['family_date_birth'] . "</td>";
				$html .= "<td>" . $data['age'] . "</td>";
				$html .= "<td><a href='javascript:void(0);' class='btn btn-sm removemember' data-member='" . $data['family_members_id'] . "' data-key='" . $addEdit['Key'] . "'>Remove</a></td>";
				$html .= "</tr>";
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'data' => $addEdit['Data'], 'html' => $html));
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


	function submitForm1()
	{
		$data = array();
//print_r($_SESSION);die;
		if(isset($_SESSION['webpanel']['utoken'])){

			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		}
		else{

			$data['source'] = 'customer';
			$data['login_user_id'] = 0;

			$_POST['lead_id'] = $this->session->userdata('lead_id');
			$_POST['customer_id'] = $this->session->userdata('customer_id');
			$_POST['plan_id'] = $this->session->userdata('plan_id');
			$_POST['trace_id'] = $this->session->userdata('trace_id');
			$_POST['proposal_details_id'] = $this->session->userdata('proposal_details_id');
		}
		$proposal_det_id = 0;	
	if(isset($_POST['proposal_details_id'])){
		$proposal_det_id = $_POST['proposal_details_id'];
	}
		$data['lead_id']  = $_POST['lead_id'];
		$data['customer_id']  = $_POST['customer_id'];
		$data['plan_id']  = $_POST['plan_id'];
		$data['trace_id']  = $_POST['trace_id'];
		$data['proposal_details_id']  = (!empty($_POST['proposal_details_id'])) ? $_POST['proposal_id'] : '';
		$data['nominee_email'] = (!empty($_POST['nominee_email'])) ? $_POST['nominee_email'] : '';
		$data['nominee_contact'] = (!empty($_POST['nominee_contact'])) ? $_POST['nominee_contact'] : '';
		
		$data['Nominee_dob'] = (!empty($_POST['nominee_dob'])) ? $_POST['nominee_dob'] : '';
		$data['nominee_gender'] = (!empty($_POST['nominee_gender'])) ? $_POST['nominee_gender'] : '';
		$data['nominee_salutation'] = (!empty($_POST['nominee_salutation'])) ? $_POST['nominee_salutation'] : '';
		$data['nominee_last_name'] = (!empty($_POST['nominee_last_name'])) ? $_POST['nominee_last_name'] : '';
		$data['nominee_first_name'] = (!empty($_POST['nominee_first_name'])) ? $_POST['nominee_first_name'] : '';


		if(isset($_POST['nominee_full_name'])){

			$data['nominee_first_name'] = $_POST['nominee_full_name'];
			$data['nominee_last_name'] = '';
		}

		$data['nominee_relation'] = (!empty($_POST['nominee_relation'])) ? $_POST['nominee_relation'] : '';

		if (!empty($data['lead_id'])) {
			$addEdit = curlFunction(SERVICE_URL . '/api2/addEditPolicyProposalNomineeDetails', $data);
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

	/*function submitfinalForm()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['plan_id']  = $_POST['plan_id'];
		$data['trace_id']  = $_POST['trace_id'];
		//$data['preffered_contact_date'] = $this->input->post('preffered_contact_date');
		//$data['preffered_contact_time'] = $this->input->post('preffered_contact_time');
		$data['mode_of_payment'] = (!empty($_POST['mode_of_payment'])) ? $_POST['mode_of_payment'] : '';


		if ($data['mode_of_payment'] == "Cheque") {

			if (isset($_FILES["enrollment_form"])) {
				$tmpfile = $_FILES['enrollment_form']['tmp_name'];
				$filename = basename($_FILES['enrollment_form']['name']);
				$data['enrollment_form'] = curl_file_create($tmpfile, $_FILES['enrollment_form']['type'], $filename);
			} else if ($data['mode_of_payment'] == "Online Payment") {
				$quoteData = $this->getQuickQuote($data);
				print_r($quoteData);
			}
			//exit;

			//$data['remark'] = (!empty($_POST['remark'])) ? $_POST['remark'] : '';

			if (!empty($data['lead_id'])) {
				$addEdit = curlFunction(SERVICE_URL . '/api2/proposalFinalSubmit', $data);
				echo "<pre>";
				print_r($addEdit);
				exit;
				if (isset($_FILES["cheaque_copy"])) {
					$tmpfile = $_FILES['cheaque_copy']['tmp_name'];
					$filename = basename($_FILES['cheaque_copy']['name']);
					$data['cheque_copy'] = curl_file_create($tmpfile, $_FILES['cheaque_copy']['type'], $filename);
				}
				if (isset($_FILES["itr"])) {
					$tmpfile = $_FILES['itr']['tmp_name'];
					$filename = basename($_FILES['itr']['name']);
					$data['itr'] = curl_file_create($tmpfile, $_FILES['itr']['type'], $filename);
				}
				if (isset($_FILES["cam"])) {
					$tmpfile = $_FILES['cam']['tmp_name'];
					$filename = basename($_FILES['cam']['name']);
					$data['cam'] = curl_file_create($tmpfile, $_FILES['cam']['type'], $filename);
				}
				if (isset($_FILES["medical"])) {
					$tmpfile = $_FILES['medical']['tmp_name'];
					$filename = basename($_FILES['medical']['name']);
					$data['medical'] = curl_file_create($tmpfile, $_FILES['medical']['type'], $filename);
				}
				$data['cheque_date'] = (!empty($_POST['cheque_date'])) ? $_POST['cheque_date'] : '';
				$data['cheque_number'] = (!empty($_POST['cheque_number'])) ? $_POST['cheque_number'] : '';
				$data['account_number'] = (!empty($_POST['account_number'])) ? $_POST['account_number'] : '';
				$data['ifsc_code'] = (!empty($_POST['ifsc_code'])) ? $_POST['ifsc_code'] : '';
				$data['bank_city'] = (!empty($_POST['bank_city'])) ? $_POST['bank_city'] : '';
				$data['bank_branch'] = (!empty($_POST['bank_branch'])) ? $_POST['bank_branch'] : '';
				$data['bank_name'] = (!empty($_POST['bank_name'])) ? $_POST['bank_name'] : '';
			}

			//$data['remark'] = (!empty($_POST['remark'])) ? $_POST['remark'] : '';

			if (!empty($data['lead_id'])) {
				$addEdit = curlFunction(SERVICE_URL . '/api2/proposalFinalSubmit', $data);
				//echo "<pre>";print_r($addEdit);exit;
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
	}*/

	function checkAndUploadFile($file_arr, $file_name, $allowed_formats, $max_file_size, $msg, $mandatory = 0)
	{

		if ($file_arr[$file_name]['tmp_name'] == '') {

			if ($mandatory) {

				echo json_encode(array('success' => false, 'msg' => "$msg cannot be blank"));
				exit;
			}
		} else if ($file_arr[$file_name]['tmp_name'] != "") {

			if ($file_arr[$file_name]['size'] <= $max_file_size) {

				$extension = pathinfo($file_arr[$file_name]['name'], PATHINFO_EXTENSION);

				if (in_array(strtolower($extension), $allowed_formats)) {

					$tmpfile = $file_arr[$file_name]['tmp_name'];
					$filename = basename($file_arr[$file_name]['name']);
					return curl_file_create($tmpfile, $file_arr[$file_name]['type'], $filename);
				} else {

					echo json_encode(array('success' => false, 'msg' => "$msg file extension should be either jpg, jpeg, png, pdf"));
					exit;
				}
			} else {

				echo json_encode(array('success' => false, 'msg' => "$msg file cannot be greater than 5MB"));
				exit;
			}
		}
	}

	function submitfinalForm()
	{
	  //  echo 123;die;
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['plan_id']  = $_POST['plan_id'];
		$data['trace_id']  = $_POST['trace_id'];
		$data['proposal_id']  = $_POST['proposal_id'];
		$data['go_green']  = isset($_POST['go-green']) ? $_POST['go-green'] : '';
		$data['created_by'] = $_SESSION['webpanel']['employee_id'];
		//$data['preffered_contact_date'] = $this->input->post('preffered_contact_date');
		//$data['preffered_contact_time'] = $this->input->post('preffered_contact_time');

		if (trim($data['lead_id']) != '') {

			$data['mode_of_payment'] = (!empty($_POST['mode_of_payment'])) ? strtolower($_POST['mode_of_payment']) : '';

			if (trim($data['mode_of_payment']) != '') {

				if ($data['mode_of_payment'] == 2) {

					$data['cheque_date'] = (!empty($_POST['cheque_date'])) ? $_POST['cheque_date'] : '';
					$data['cheque_number'] = (!empty($_POST['cheque_number'])) ? $_POST['cheque_number'] : '';
					$data['account_number'] = (!empty($_POST['account_number'])) ? $_POST['account_number'] : '';
					$data['ifsc_code'] = (!empty($_POST['ifsc_code'])) ? $_POST['ifsc_code'] : '';
					$data['bank_city'] = (!empty($_POST['bank_city'])) ? $_POST['bank_city'] : '';
					$data['bank_branch'] = (!empty($_POST['bank_branch'])) ? $_POST['bank_branch'] : '';
					$data['bank_name'] = (!empty($_POST['bank_name'])) ? $_POST['bank_name'] : '';

					$file_types = ['jpg', 'jpeg', 'png', 'pdf'];
					
					if(isset($_FILES['enrollment_form']['tmp_name'])){
						$data['enrollment_form'] = $this->checkAndUploadFile($_FILES, 'enrollment_form', $file_types, 5000000, "Enrollment Form", 1);
					}

					if(isset($_FILES['cheque_copy']['tmp_name'])){

						$data['cheque_copy'] = $this->checkAndUploadFile($_FILES, 'cheque_copy', $file_types, 5000000, "Cheque Copy", 1);
					}

					if(isset($_FILES['itr']['tmp_name'])){

						$data['itr'] = $this->checkAndUploadFile($_FILES, 'itr', $file_types, 5000000, "ITR Form", 0);
					}

					if(isset($_FILES['cam']['tmp_name'])){

						$data['cam'] = $this->checkAndUploadFile($_FILES, 'cam', $file_types, 5000000, "CAM Report", 0);
					}

					if(isset($_FILES['medical']['tmp_name'])){

						$data['medical'] = $this->checkAndUploadFile($_FILES, 'medical', $file_types, 5000000, "Medical Report", 0);
					}

					if (isset($_POST['id_document_type']) && trim($_POST['id_document_type']) != '') {

						if(isset($_FILES['file_type']['tmp_name'])){

							$data['id_document_type'] = $_POST['id_document_type'];
							$data['file_type'] = $this->checkAndUploadFile($_FILES, 'file_type', $file_types, 5000000, "File Type", 0);
						}
						else{

							$data['id_document_type'] = '';
						}
					}

					$data['go_green'] = '';
				}


				$addEdit = curlFileFunction(SERVICE_URL . '/api2/proposalFinalSubmit', $data);
               //print_r($addEdit);die;
                if($addEdit == "Not Sufficient CD Balance."){
                    $msg=$addEdit;
                }else{
                    $msg= $addEdit['Metadata']['Message'];
                }
				$addEdit = json_decode($addEdit, true);

				if ($addEdit['status_code'] == '200') {
                   /* if ($data['mode_of_payment'] == 4) {
                        redirect('/policyproposal/success_view/'.$_POST['lead_id']);
                    }*/
					echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message']));
					exit;
				} else {

					echo json_encode(array('success' => false, 'msg' =>$msg));
					exit;
				}
			} else {

				echo json_encode(array('success' => false, 'msg' => "Payment Mode cannot be blank"));
				exit;
			}
		} else {

			echo json_encode(array('success' => false, 'msg' => "Lead ID cannot be blank"));
			exit;
		}
	}
    public function success_view($lead_id){
        // echo $lead_id;exit;
        session_destroy();
        //echo "<PRE>";print_r($_REQUEST);
        $success = true;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        insert_application_log($lead_id, 'Payment_response', "", json_encode($_POST), 123);
        // echo $lead_id;exit;

        $error = "Payment Failed";
        $data = [];
        /*if (empty($_POST['razorpay_payment_id']) === false)
        {

            $api = new Api($this->keyId, $this->keySecret);

            try
            {
                // Please note that the razorpay order ID must
                // come from a trusted source (session here, but
                // could be database or something else)
                $attributes = array(
                    'razorpay_order_id' => (array_key_exists('razorpay_order_id',$_SESSION)) ? ($_SESSION['razorpay_order_id']) : (''),
                    'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                    'razorpay_signature' => $_POST['razorpay_signature']
                );

                $api->utility->verifyPaymentSignature($attributes);
            }
            catch(SignatureVerificationError $e)
            {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }*/
        if(isset($_POST['razorpay_payment_id'])){
            // var_dump($success);exit;
            if ($success === true)
            {

                $req_data['pg_response'] = $_REQUEST;
                $req_data['lead_id'] = $lead_id;
//                print_r($req_data);exit;
                $cond = "";
                $update_payment_status = json_decode(curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data),TRUE);

                //if($update_payment_status){
                $req_data1['lead_id'] = $lead_id;
                $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1),TRUE);
                $coi_no = $res["data"]["certificate_number"];
                $cond = '<p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">'.$coi_no.'</span>
                        </p>';
                //   }
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> '.$lead_id.'</span>
                        </p>
                        <p>Payment ID:
                            <span> '.$_POST["razorpay_payment_id"].'</span>
                        </p>
                        '.$cond.'
                    </div>';
            }
            else
            {
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment failed</p>
                <p>Lead ID:<span id="lead_view"> '.$lead_id.'</span></p>
                         <p> <span>'.$error.'</span></p>';
            }
        }else{
            $req_data['lead_id'] = $lead_id;
            //echo "in";exit;
            $update_payment_status = json_decode(curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data),TRUE);
            $req_data['lead_id'] = $lead_id;
            $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data),TRUE);
            if($res){
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> '.$lead_id.'</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">'.$res["data"]["certificate_number"].'</span>
                        </p>
                    </div>';

            }
        }


        $this->load->view('template/customer_portal_header.php');
        $this->load->view('policyproposal/thank_you',$data);
        $this->load->view('template/customer_portal_footer.php');
    }

	/*
	Author : Amol Koli
	Date : 23th Dec, 2020
	***/
	function getQuickQuote($data)
	{
		$lead_id  = (!empty($data['lead_id'])) ? $data['lead_id'] : '';
		$plan_id  = (!empty($data['plan_id'])) ? $data['plan_id'] : '';
		$trace_id = (!empty($data['trace_id'])) ? $data['trace_id'] : '';
		//print_r($data);
		//exit;
		$doQuickQuote = [];
		if (!empty($data['lead_id'])) {
			$doQuickQuote = curlFunction(SERVICE_URL . '/api2/doQuickQuote', $data);
			$doQuickQuote = json_decode($doQuickQuote, true);
		}
		return $doQuickQuote;
	}

	/*
	Author : Amol Koli
	Date : 01th Jan, 2021
	***/
	function getFullQuote($data)
	{
		$lead_id  = (!empty($data['lead_id'])) ? $data['lead_id'] : '';
		$plan_id  = (!empty($data['plan_id'])) ? $data['plan_id'] : '';
		$trace_id = (!empty($data['trace_id'])) ? $data['trace_id'] : '';

		//print_r($data);
		//exit;
		$doQuickQuote = [];
		if (!empty($data['lead_id'])) {
			//echo http_build_query($data);
			//http_build_query($data);
			//exit;
			$doFullQuote = curlFunction(SERVICE_URL . '/api2/doFullQuote', $data);
			$doFullQuote = json_decode($doFullQuote, true);
		}
		return $doQuickQuote;
	}

	/*
	Author : Amol Koli
	Date : 01th Jan, 2021
	***/
	function sendCommunication($data)
	{
		$doSendEmail = [];
		if (!empty($data['lead_id'])) {
			$doSendEmail = curlFunction(SERVICE_URL . '/api2/doSendCommunication', $data);
			$doSendEmail = json_decode($doSendEmail, true);
		}
		return $doSendEmail;
	}

	/*
	Author : Amol Koli
	Date : 01th Jan, 2021
	***/
	function sendOtp($data)
	{
		$doSendOtp = [];
		if (!empty($data['lead_id'])) {
			$doSendOtp = curlFunction(SERVICE_URL . '/api2/doSendOtp', $data);
			$doSendOtp = json_decode($doSendOtp, true);
		}
		return $doSendOtp;
	}

	/*
	Author : Amol Koli
	Date : 01th Jan, 2021
	***/
	function verifyOtp($data)
	{
		$doSendOtp = [];
		if (!empty($data['lead_id'])) {
			$verifyOtp = curlFunction(SERVICE_URL . '/api2/doVerifyOtp', $data);
			$verifyOtp = json_decode($verifyOtp, true);
		}
		return $doSendOtp;
	}
	/*
	Author : Jitendra
	Date : 16th Dec, 2020
	***/

	function submitLeadForm()
	{
		$data = $_POST;

		if(!(isset($_POST['source']) && $_POST['source'] == 'customer')){

			$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		}
		
		$result = json_decode(curlFunction(SERVICE_URL . '/api2/saveGeneratedQuote', $data));
//var_dump($result);exit;
		$response = [];

		if (isset($result->status) && $result->status) {

			if(isset($result->data->quote_ids) && !empty($result->data->quote_ids)){

				$response = array('success' => true, 'msg' => "Quote Generated", "data" => $result->data);
			}
			
			$msg = '';
			
			if(isset($result->policy_errors) && !empty($result->policy_errors)){

				foreach($result->policy_errors as $key => $value){

					if(!empty($value) && $value[0] != '' && $value[0] != 'Invalid sum_insured'){

						$msg .= $key . " : " . $value[0] . "<br> ";
					}
				}
			}
			
			if(!empty($response)){

				$response['policy_errors'] = $msg;
			}
			else{

				$response = array('success' => false, 'msg' => $result->messages, "data" => $result->data, "policy_errors" => $msg);
			}

			echo json_encode($response);

		} else {
			if(isset($result->data)){
				echo json_encode(array('success' => false, 'msg' => $result->messages, "data" => $result->data));
			} else{
				echo json_encode(array('success' => false, 'msg' => $result->messages, "data" => ""));
			}
		}

		exit;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['plan_id']  = $_POST['plan_id'];
		$data['trace_id']  = $_POST['trace_id'];
		$data['proposal_id']  = $_POST['proposal_id'];

		$data['customer_id']  = $_POST['customer_id'];

		$data['lan_id'] = $_POST['lan_id'];
		$data['loan_amt'] = $_POST['loan_amt'];

		$data['tenure'] = $_POST['tenure'];

		$data['numbers_of_ci'] = isset($_POST['numbers_of_ci']) ? $_POST['numbers_of_ci'] : '';
		$data['sum_insured5_2'] = isset($_POST['sum_insured5_2']) ? $_POST['sum_insured5_2'] : '';    // deductable

		if (isset($_POST['sum_insured1'])) {
			$data['sum_insured1'] = $_POST['sum_insured1'];
		}

		if (isset($_POST['sum_insured2'])) {
			$data['sum_insured2'] = $_POST['sum_insured2'];;
		}

		if (isset($_POST['sum_insured3'])) {
			$data['sum_insured3'] = $_POST['sum_insured3'];
		}

		if (isset($_POST['sum_insured5_1'])) {
			$data['sum_insured5_1'] = $_POST['sum_insured5_1'];
		}

		if (isset($_POST['sum_insured6'])) {
			$data['sum_insured6'] = $_POST['sum_insured6'];
		}


		if (!empty($data['lead_id'])) {
			$addEdit = curlFunction(SERVICE_URL . '/api2/proposalLeadSubmit', $data);

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
	} // submitLeadForm()

	function getMemberID()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['customer_id'] = $_POST['customer_id'];

		if (!empty($data['lead_id']) && !empty($data['customer_id'])) {

			$addEdit = curlFunction(SERVICE_URL . '/api2/get_member_id', $data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);

			if ($addEdit['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'data' => $addEdit['Data']));
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

	function checkInsuredMembersExist()
	{
		if (!empty($this->input->post('lead_id'))) {
			$data = curlFunction(SERVICE_URL . '/api2/checkInsuredMembersExist', $this->input->post());
			$data = json_decode($data, true);

			if ($data['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $data['Metadata']['Message']));
				exit;
			} else {
				echo json_encode(array('success' => false, 'msg' => $data['Metadata']['Message']));
				exit;
			}
		} else {
			echo json_encode(array('success' => false, 'msg' => "Something went wrong try after some time"));
			exit;
		}
	}

	function deletemember()
	{

		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['policy_nos']  = $_POST['policy_nos'];
		$data['sibasis']  = $_POST['sibasis'];
		$data['id']  = $_POST['id'];

		if (!empty($data['lead_id'])) {
			$addEdit = curlFunction(SERVICE_URL . '/api2/deletePolicyMember', $data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);

			if ($addEdit['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'data' => $addEdit['Data']));
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
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL . '/api2/delPolicySubType', $data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);

		if ($delRecord['status_code'] == '200') {
			echo "1";
		} else {
			echo "2";
		}
	}

	function getStateCity()
	{
		$result = json_decode(curlFunction(SERVICE_URL . '/api2/getStateCityFromPincode', $_POST));

		if ($result) {
			echo json_encode(['success' => true, 'data' => $result]);
		} else {
			echo json_encode(['success' => false, 'data' => $result]);
		}
	}

	/*function customerotpform()
	{
		if (isset($_GET['text']) && !empty($_GET['text'])) {

			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$record_id = $url_prams['id'];

			$data['lead_id'] = rtrim(strtr(base64_encode($record_id), '+/', '-_'), '=');

			$this->load->view('template/customer-header.php');
			$this->load->view('policyproposal/otpconfirmation', $data);
			$this->load->view('template/footer.php');
		}
		else{

			$this->load->view('template/customer-header.php');
			$this->load->view('policyproposal/tryagain');
			$this->load->view('template/customer-footer.php');			
		}
	}*/

	/*function customerpaymentform()
	{
		$record_id = "";
		//print_r($_GET);
		if (!empty($_GET['text']) && isset($_GET['text'])) {
			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$record_id = $url_prams['id'];
		}
		unset($_SESSION['webpanel']['customerotp']);
		unset($_SESSION['webpanel']['customerlead']);

		$data = array();
		$data['lead_id'] = $record_id;
		$result = json_decode(curlFunction(SERVICE_URL . '/api2/sendcustomerpaymentformotp', $data));
		if ($result['status_code'] == '200') {
			$this->load->view('template/header.php');
			$this->load->view('policyproposal/otpconfirmation', $data);
			$this->load->view('template/footer.php');
		} else {
			$this->load->view('template/header.php');
			$this->load->view('policyproposal/linkexpired');
			$this->load->view('template/footer.php');
		}
		//echo $user_id;

	}*/

	function test_uploadview()
	{
		$this->load->view('policyproposal/test');
	}

	function testupload()
	{
		$data = array();
		$tmpfile = $_FILES['testfile']['tmp_name'];
		$filename = basename($_FILES['testfile']['name']);
		$data['testfile'] = curl_file_create($tmpfile, $_FILES['testfile']['type'], $filename);
		//print_r($data);exit;
		//$data['testfile'] = '@' . realpath($_FILES['testfile']);
		$ch = curl_init(SERVICE_URL . '/api2/test_upload');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		//$result = curlFunction(SERVICE_URL.'/api2/test_upload',$data);
		echo $response;
	}

	/*function memberdetailsform()
	{
		$data = [];
		$data['firstname'] = htmlentities(strip_tags(trim($_POST['firstname'])));
		$data['lastname'] = htmlentities(strip_tags(trim($_POST['lastname'])));

		$data['lead_id'] = base64_decode(strtr($_POST['elem_lead'], '-_', '+/'));
		$data['customer_id'] = base64_decode(strtr($_POST['elem_customer'], '-_', '+/'));
		$data['member_id'] = base64_decode(strtr($_POST['elem_member'], '-_', '+/'));

		$result = curlFunction(SERVICE_URL . '/api2/savememberdetails', $data);

		echo $result;exit;
	}*/

	/*function customerpaymentformdetails()
	{

		$result = array();
		$data = array();

		if (isset($_SESSION['webpanel']['customerotp']) && isset($_SESSION['webpanel']['customerlead'])) {
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
		}
	}*/

	public function getmemberdetails()
	{
		$info = $_POST['info'];
		$info['utoken'] = $_SESSION['webpanel']['utoken'];
		$response = curlFunction(SERVICE_URL . '/api2/getmemberdetails', $info);

		$response = json_decode($response, true);

		$data['customer_id'] = isset($info['customer_id']) ? $info['customer_id'] : '';
		$data['trace_id'] = isset($info['trace_id']) ? $info['trace_id'] : '';
		$data['lead_id'] = isset($info['lead_id']) ? $info['lead_id'] : '';
		$data['plan_id'] = isset($info['plan_id']) ? $info['plan_id'] : '';
		$data['proposal_id'] = $info['proposal_id'];
		$data['member_id'] = isset($info['member_id']) ? $info['member_id'] : '';
		$data['member_added'] = isset($info['member_added']) ? $info['member_added'] : '';
		$data['coapplicant_tab_id'] = $info['coapplicant_tab_id'];
		$data['current_tab'] = $info['current_tab'];
		$data['data'] = (isset($response['Metadata']['data'])) ? $response['Metadata']['data'] : '';

		$this->load->view('policyproposal/insuredMemberForm', $data);
	}

	public function getquotedetails()
	{

		//$quote_ids = $_POST['quote_ids'];
		//$info['quote_ids'] = explode(',', $quote_ids['quote_ids']);
		$info['lead_id'] = $_POST['lead_id'];
		//$info['customer_id'] = $_POST['customer_id'];
		$info['utoken'] = $_SESSION['webpanel']['utoken'];
		$response = curlFunction(SERVICE_URL . '/api2/getquotedetails', $info);

		$response = json_decode($response, true);

		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Metadata']['data']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}

	public function capturecustomerpan()
	{
		$response = ['success' => '', 'msg' => ''];
		if (isset($_POST['customer_data']) && isset($_POST['lead_id'])) {

			if($_POST['customer_data'] != ''){

				$customer_data = json_decode($_POST['customer_data'], true);

				if(count($customer_data) > 0){

					$customer_pan_arr = [];
					foreach($customer_data as $key => $value){

						$val_arr = explode(':', $value);
						$customer_pan_arr[$val_arr[0]] = $val_arr[1];
					}

					$data['lead_id'] = $_POST['lead_id'];
					$data['customer_data'] = $customer_pan_arr;
					$data['utoken'] = $_SESSION['webpanel']['utoken'];
					$data['created_by'] = $_SESSION['webpanel']['employee_id'];
					$result = curlFunction(SERVICE_URL . '/api2/updatecustomerpan', $data);
					$result = json_decode($result, true);

					if ($result['status_code'] == 500) {

						$response['success'] = false;
					} else if ($result['status_code'] == 200) {

						$response['success'] = true;
					}
					$response['msg'] = $result['Metadata']['msg'];
				}
				else{

					$response['success'] = false;
					$response['msg'] = 'Error in customer pan json';
				}
			}
			else{

				$response['success'] = false;
				$response['msg'] = 'No Customer PAN Provided';
			}
		}
		else {

			$response['success'] = false;
			$response['msg'] = "Invalid Data";
		}
		echo json_encode($response);
		exit;

		/*$response = ['success' => '', 'msg' => ''];
		if (isset($_POST['proposer_pan'])) {

			if (trim($_POST['proposer_pan']) != '') {

				$data['proposer_pan'] = $_POST['proposer_pan'];
				$data['customer_id'] = $_POST['customer_id'];
				$data['lead_id'] = $_POST['lead_id'];
				$data['utoken'] = $_SESSION['webpanel']['utoken'];
				$data['created_by'] = $_SESSION['webpanel']['employee_id'];
				$result = curlFunction(SERVICE_URL . '/api2/updatecustomerpan', $data);
				$result = json_decode($result, true);

				if ($result['status_code'] == 500) {

					$response['success'] = false;
				} else if ($result['status_code'] == 200) {

					$response['success'] = true;
				}
				$response['msg'] = $result['Metadata']['msg'];
			} else {

				$response['success'] = false;
				$response['msg'] = "Blank Data";
			}
		} else {

			$response['success'] = false;
			$response['msg'] = "Invalid Data";
		}
		echo json_encode($response);
		exit;*/
	}

	function changeFamilyConstruct()
	{

		$family_construct = strip_tags(trim($_POST['member_count']));
		$co_applicant_tab_id = strip_tags(trim($_POST['co_applicant_tab_id']));
		$plan_id = strip_tags(trim($_POST['plan_id']));
		$lead_id = strip_tags(trim($_POST['lead_id']));
		$customer_id = strip_tags(trim($_POST['customer_id']));
		$proposal_details_id = strip_tags(trim($_POST['proposal_details_id']));
		$not_self = strip_tags(trim($_POST['not_self']));

		if ($customer_id != '' && $lead_id != '') {

			$member_count_arr = explode('-', $family_construct);
			$member_count = ['adult_count' => 0, 'child_count' => 0];

			if (isset($member_count_arr[0])) {

				$member_count['adult_count'] = $member_count_arr[0];
			}

			if (isset($member_count_arr[1])) {

				$member_count['child_count'] = $member_count_arr[1];
			}

			$req_data = [
				'customer_id' => $customer_id,
				'lead_id' => $lead_id,
				'plan_id' => $plan_id,
				'utoken' => $_SESSION['webpanel']['utoken']
			];

			$member_details = json_decode(curlFunction(SERVICE_URL . '/api2/getInsuredMemberDetails', $req_data), true);

			if (!empty($member_details['member_details'])) {

				$member_details_arr = array_values($member_details['member_details'][$customer_id]);
				$insured_member_form_data = [
					'coapplicant_tab_id' => $co_applicant_tab_id,
					'member_count' => $member_count,
					'current_customer_id' => $customer_id,
					'proposal_details_id' => $proposal_details_id,
					'member_details' => $member_details_arr,
					'plan_id' => $plan_id,
					'lead_id' => $lead_id,
					'criterias' => $member_details['insured_member_criterias'],
					'family_constuct_relation_map' => $member_details['family_constuct_relation_map']
				];

				$this->load->view('insuredMemberSectionFilter', $insured_member_form_data);
			} else {

				$req_data = [
					'customer_id' => $customer_id,
					'lead_id' => $lead_id,
					'plan_id' => $plan_id,
					'utoken' => $_SESSION['webpanel']['utoken']
				];

				$member_details = json_decode(curlFunction(SERVICE_URL . '/api2/getSelfDetails', $req_data), true);
				//echo "<pre>";print_r($member_details);exit;
				$insured_member_form_data = [
					'member_count' => $member_count,
					'coapplicant_tab_id' => $co_applicant_tab_id,
					'current_customer_id' => $customer_id,
					'proposal_details_id' => $proposal_details_id,
					'customer' => $member_details['member_details'],
					'not_self' => $not_self,
					'plan_id' => $plan_id,
					'lead_id' => $lead_id,
					'criterias' => $member_details['insured_member_criterias'],
					'family_constuct_relation_map' => $member_details['family_constuct_relation_map']
				];

				$this->load->view('insuredMemberSection', $insured_member_form_data);
			}
		}
	}

	function deleteInsuredMembers()
	{

		$proposal_details_id = htmlspecialchars(strip_tags(trim($_POST['proposal_details_id'])));
		$lead_id = htmlspecialchars(strip_tags(trim($_POST['lead_id'])));
		$customer_id = htmlspecialchars(strip_tags(trim($_POST['customer_id'])));

		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['created_by'] = $_SESSION['webpanel']['employee_id'];
		$data['proposal_details_id'] = $proposal_details_id;
		$data['lead_id'] = $lead_id;
		$data['customer_id'] = $customer_id;

		$member_details = json_decode(curlFunction(SERVICE_URL . '/api2/deleteInsuredMemberDetails', $data), true);

		if ($member_details['status_code'] == '200') {
			echo json_encode(array('status' => true, 'msg' => $member_details['Metadata']['msg']));
			exit;
		} else {
			echo json_encode(array('status' => false, 'msg' => $member_details['Metadata']['msg']));
			exit;
		}
	}

	/*
	Author : Jitendra
	Date : 18th Dec, 2020
	****/
	function submitInsuredMemberForm()
	{

		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['member_salutation']  = $_POST['member_salutation'];
		$data['first_name'] = $_POST['first_name'];
		$data['last_name'] = $_POST['last_name'];
		$data['gender'] = $_POST['gender'];
		$co_applicant_tab_id = $_POST['co_applicant_tab_id'];
		$tab_id = $_POST['tab_id'];
		$data['insured_member_dob'] = $_POST['insured_member_dob' . '' . $co_applicant_tab_id . '' . $tab_id];
		//$data['pan'] = $_POST['pan'];
		$data['customer_id']  = $_POST['customer_id'];
		$data['created_by'] = $_SESSION['webpanel']['employee_id'];
		$data['member_id']  = $_POST['member_id'];
		$data['lead_id']  = $_POST['lead_id'];
		$data['plan_id']  = $_POST['plan_id'];
		$data['trace_id']  = $_POST['trace_id'];
		$data['proposal_id']  = $_POST['proposal_id'];

		if($data['member_salutation'] == 'Dr'){

			$data['gender'] = $_POST['insured_member_gender'];
		}

		//$data['relation_with_proposal'] = $_POST['relation_with_proposal'];
		$data['relation_with_proposal'] = $_POST['member_type_id'];

		if (!empty($data['lead_id'])) {
			$addEdit = curlFunction(SERVICE_URL . '/api2/proposalInsuredMemberSubmit', $data);
			

			$addEdit = json_decode($addEdit, true);

			if ($addEdit['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'member_id' => $addEdit['Metadata']['member_id'], 'data_added' => $addEdit['Metadata']['data_added']));
				exit;
			} else {
				echo json_encode(array('success' => false, 'msg' => $addEdit['Metadata']['Message']));
				exit;
			}
		} else {
			echo json_encode(array('success' => false, 'msg' => "Something went wrong try after some time"));
			exit;
		}


		/*
		$data['loan_amt'] = $_POST['loan_amt'];
		
		$data['tenure'] = $_POST['tenure'];
		
		$data['numbers_of_ci'] = isset($_POST['numbers_of_ci']) ? $_POST['numbers_of_ci']:'';
		$data['sum_insured5_2'] = isset($_POST['sum_insured5_2'])? $_POST['sum_insured5_2']:'';    // deductable
		
		$total_sum_insured = 0;
		
		if (isset($_POST['sum_insured1'])) {
			$total_sum_insured += $_POST['sum_insured1'];
		}
		if (isset($_POST['sum_insured2'])) {
			$total_sum_insured += $_POST['sum_insured2'];
		}
		if (isset($_POST['sum_insured3'])) {
			$total_sum_insured += $_POST['sum_insured3'];
		}
		if (isset($_POST['sum_insured5'])) {
			$total_sum_insured += $_POST['sum_insured5'];
		}
		if (isset($_POST['sum_insured6'])) {
			$total_sum_insured += $_POST['sum_insured6'];
		}
		if (isset($_POST['sum_insured5_1'])) {
			$total_sum_insured += $_POST['sum_insured5_1'];
		}
		
		$data['total_sum_insured'] = $total_sum_insured;
		
		if(!empty($data['lead_id'])) {
			$addEdit = curlFunction(SERVICE_URL.'/api2/proposalLeadSubmit',$data);
			//echo "<pre>";print_r($addEdit);exit;
			
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200') {
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			} else {
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
		} else {
			echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
			exit;
		} 
		**/



		echo json_encode(array('success' => true, 'msg' => "call testing"));
		exit;
	} // EO submitInsuredMemberForm()

	/*
	Author : Amol Koli
	Date : 07th jan, 2020
	***/
	function quickQuote($lead_id, $mode_of_payment)
	{
		if ($mode_of_payment == "Online") {
			$doQuickQuote = $this->apimodel->doQuickQuote($lead_id, $mode_of_payment);
			//print_r($doQuickQuote);
			//exit;
			if ($doQuickQuote['Status'] == "Success") {
				redirect(base_url('payments/doPayment/' . $lead_id));
			}
		} else {
			$doQuickQuote = $this->apimodel->doQuickQuote($lead_id, $mode_of_payment);
			if ($doQuickQuote['Status'] == "Success") {
				$doFullQuote = $this->apimodel->doFullQuote($lead_id);
			}
		}
	}

	/*
	Author : Amol Koli
	Date : 07th jan, 2020
	***/
	function fullQuote($lead_id)
	{
		$doFullQuote = $this->apimodel->doFullQuote($lead_id);
		if ($doFullQuote["Status"] == "Success") {
			redirect(base_url('payments/thankyou/' . $lead_id));
		} else {
			redirect(base_url('payments/tryagain/' . $lead_id));
		}
	}

	function tryagain($lead_id)
	{
		$data = array();
		$data['lead_id'] = $lead_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];

		$this->load->view('template/header.php');
		$this->load->view('payments/tryagain', $data);
		$this->load->view('template/footer.php');
	}

	public function proposalSummary()
	{
		/*$result['generated_quote'] = json_decode(curlFunction(SERVICE_URL . '/api2/getGeneratedQuote', [
			'customer_id' => '',
			'lead_id' => ''
		]));*/

		if (!empty($_GET['text']) && isset($_GET['text'])) {
			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$record_id = $url_prams['id'];
		}

		$result = array();

		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);
// echo "<PRE>";print_r($result);exit;
		$customers = reset($result['customer_details']);

		foreach ($customers as $customer) {

			$requestData = [
				'customer_id' => $customer['customer_id'],
				'lead_id' => $record_id
			];

			$mode = 'preview';
			$members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
			$questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
			$answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

			$customer_id = $customer['customer_id'];

			$html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
			$result['ghd_declaration'][$customer['customer_id']] = $html;
		}

		$result['lead_id_enc'] = (isset($_GET['text'])) ? $_GET['text'] : '';
        
		//echo "<pre>";print_r($result);exit;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/proposalSummary', $result);
		$this->load->view('template/footer.php');
	}

    

	public function submitProposal()
	{
		$data = [];
		$data['proposal_details_id'] = $this->input->post('proposal_details_id');
		$data['health_declaration'] = $this->input->post('health_declaration');
		$data['utoken'] = $_SESSION['webpanel']['utoken'];

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/saveHealthDeclaration', $data), true);
		
		if ($result['status_code'] == '200') {

			echo json_encode(array('success' => true));
		} else {

			echo json_encode(array('success' => false));
		}
		exit;
	}

	function populateNomineeRelation()
	{
		if(isset($_POST['source']) && $_POST['source']=="customer"){

			$_POST['lead_id'] = $this->session->userdata('lead_id');
			$_POST['customer_id'] = $this->session->userdata('customer_id');
		}

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/getNomineeBasedOnRelation', $_POST));

		//print_r($result);exit;

		echo json_encode($result);exit;
	}

	function ghddeclaration()
	{
		$mode = isset($_GET['is_only_previewable'])  && $_GET['is_only_previewable'] ? 'preview' : 'addEdit';
		$members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $_POST));
		$questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
		$answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $_POST));
		$customer_id = $this->input->post('customer_id');

		$html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'));

		echo $html;
	}

	function submitGHDDeclaration()
	{
		$data = $_POST;

		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/saveGHDDeclaration', $data));

		echo json_encode([
			'status' => $result->status,
			'message' => $result->message,
		]);
	}

	function submitGHDDeclarationFromCustomerEnd()
	{
		$data = $_POST;
		$data['customer_id'] = base64_decode($data['customer_id']);
		$data['lead_id'] = base64_decode($data['lead_id']);

		$result = json_decode(curlFunction(SERVICE_URL . '/api2/saveGHDDeclaration', $data));

		echo json_encode([
			'status' => $result->status,
			'message' => $result->message,
		]);
	}
}
