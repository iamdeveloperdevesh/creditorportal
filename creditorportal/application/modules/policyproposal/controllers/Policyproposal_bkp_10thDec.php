<?php header('Access-Control-Allow-Origin: *'); if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Policyproposal extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
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
		$insurerListing = curlFunction(SERVICE_URL.'/api2/PolicySubTypeListing',$_GET);
		$insurerListing = json_decode($insurerListing, true);
		//echo "<pre>";print_r($insurerListing);exit;
		if($insurerListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $insurerListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $insurerListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($insurerListing['Data']['query_result']) && count($insurerListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($insurerListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $insurerListing['Data']['query_result'][$i]['policy_sub_type_name'] );
				array_push($temp, $insurerListing['Data']['query_result'][$i]['typename'] );
				
				if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
					$actionCol .='<a href="policysubtype/addEdit?text='.rtrim(strtr(base64_encode("id=".$insurerListing['Data']['query_result'][$i]['policy_sub_type_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$insurerListing['Data']['query_result'][$i]['policy_sub_type_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
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
	
	function viewdetails() {
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		$result = array();
		
		
		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL.'/api2/getLeadDetails',$data));
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/viewdetails',$result);
		$this->load->view('template/footer.php');
	}
	function addEdit()
	{
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		//print "Testing:::".$record_id;
		
		$result = array();
		
		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		
		$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL.'/api2/getLeadDetails',$data));
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	function changePaymentMode()
	{
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		$result = array();
		
		$data = array();
		$data['lead_id'] = $record_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		
		$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL.'/api2/getLeadDetails',$data));
		$result['changepaymentmodeonly'] = 1;
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policyproposal/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	 
	function submitForm()
	{
			$data = array();
		    $data['utoken'] = $_SESSION['webpanel']['utoken'];
		    $data['lead_id']  = $_POST['lead_id'];
			$data['trace_id']  = $_POST['trace_id'];
			$data['customer_id']  = $_POST['customer_id'];
			$data['address_line1'] = (!empty($_POST['address_line1'])) ? $_POST['address_line1'] : '';
			$data['address_line2'] = (!empty($_POST['address_line1'])) ? $_POST['address_line2'] : '';
			$data['address_line3'] = (!empty($_POST['address_line1'])) ? $_POST['address_line3'] : '';
			$data['mobile_no2'] = (!empty($_POST['mobile_no2'])) ? $_POST['mobile_no2'] : '';
			$data['city'] = (!empty($_POST['city'])) ? $_POST['city'] : '';
			$data['state'] = (!empty($_POST['state'])) ? $_POST['state'] : '';
			$data['pin_code'] = (!empty($_POST['pin_code'])) ? $_POST['pin_code'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['dob'] = (!empty($_POST['dob'])) ? $_POST['dob'] : '';
			//$data['updatedon'] = date("Y-m-d");
			
			if(!empty($data['lead_id'])){
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditPolicyProposalCustDetails',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			}else{
			    echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
				exit;
			}
	}
	function coapplicantsubmitForm()
	{
			$data = array();
		    $data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		    $data['lead_id']  = $_POST['lead_id'];
			$data['plan_id']  = $_POST['plan_id'];
			$data['trace_id']  = $_POST['trace_id'];
			$data['customer_id']  = $_POST['customer_id'];
			$data['first_name'] = (!empty($_POST['firstname'])) ? $_POST['firstname'] : '';
			$data['last_name'] = (!empty($_POST['lastname'])) ? $_POST['lastname'] : '';
			$data['middle_name'] = (!empty($_POST['middlename'])) ? $_POST['middlename'] : '';
			$data['dob'] = (!empty($_POST['dob'])) ? $_POST['dob'] : '';
			$data['mobile_no'] = (!empty($_POST['mob_no'])) ? $_POST['mob_no'] : '';
			$data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
			$data['gender'] = (!empty($_POST['gender1'])) ? $_POST['gender1'] : '';
			$data['address_line1'] = (!empty($_POST['address_line1'])) ? $_POST['address_line1'] : '';
			$data['address_line2'] = (!empty($_POST['address_line1'])) ? $_POST['address_line2'] : '';
			$data['address_line3'] = (!empty($_POST['address_line1'])) ? $_POST['address_line3'] : '';
			$data['mobile_no2'] = (!empty($_POST['mobile_no2'])) ? $_POST['mobile_no2'] : '';
			$data['city'] = (!empty($_POST['city'])) ? $_POST['city'] : '';
			$data['state'] = (!empty($_POST['state'])) ? $_POST['state'] : '';
			$data['pin_code'] = (!empty($_POST['pin_code'])) ? $_POST['pin_code'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			
			if(!empty($data['lead_id'])){
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditPolicyProposalCoapplicantDetails',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message'],'Data'=>$addEdit['Data']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			}else{
			    echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
				exit;
			}
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
			
			if(!empty($data['lead_id'])){
				
			$addEdit = json_decode(curlFunction(SERVICE_URL.'/api2/addProposalMember',$data),true);
			
			if($addEdit['status_code'] == '200'){
				$html = "<tr>";
				$html.= "<td>".$family_member_name."</td>";
				$html.= "<td>".$data['first_name']."</td>";
				$html.= "<td>".$data['last_name']."</td>";
				$html.= "<td>".$data['family_gender']."</td>";
				$html.= "<td>".$data['family_date_birth']."</td>";
				$html.= "<td>".$data['age']."</td>";
				$html.= "<td><a href='javascript:void(0);' class='btn btn-sm removemember' data-member='".$data['family_members_id']."' data-key='".$addEdit['Key']."'>Remove</a></td>";
				$html.= "</tr>";
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message'], 'data'=>$addEdit['Data'], 'html'=>$html));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			}else{
			    echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
				exit;
			}
	}
	
	
	function submitForm1()
	{
		
			$data = array();
		    $data['utoken'] = $_SESSION['webpanel']['utoken'];
		    $data['lead_id']  = $_POST['lead_id'];
		    $data['customer_id']  = $_POST['customer_id'];
		    $data['plan_id']  = $_POST['plan_id'];
		    $data['trace_id']  = $_POST['trace_id'];
			$data['nominee_email'] = (!empty($_POST['nominee_email'])) ? $_POST['nominee_email'] : '';
			$data['nominee_contact'] = (!empty($_POST['nominee_contact'])) ? $_POST['nominee_contact'] : '';
			$data['nominee_dob'] = (!empty($_POST['nominee_dob'])) ? $_POST['nominee_dob'] : '';
			$data['nominee_gender'] = (!empty($_POST['nominee_gender'])) ? $_POST['nominee_gender'] : '';
			$data['nominee_salutation'] = (!empty($_POST['nominee_salutation'])) ? $_POST['nominee_salutation'] : '';
			$data['nominee_last_name'] = (!empty($_POST['nominee_last_name'])) ? $_POST['nominee_last_name'] : '';
			$data['nominee_first_name'] = (!empty($_POST['nominee_first_name'])) ? $_POST['nominee_first_name'] : '';
			$data['nominee_relation'] = (!empty($_POST['nominee_relation'])) ? $_POST['nominee_relation'] : '';
			
			if(!empty($data['lead_id'])){
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditPolicyProposalNomineeDetails',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			}else{
			    echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
				exit;
			}
	}
	
	function submitfinalForm()
	{
		
			$data = array();
		    $data['utoken'] = $_SESSION['webpanel']['utoken'];
		    $data['lead_id']  = $_POST['lead_id'];
		    $data['plan_id']  = $_POST['plan_id'];
		    $data['trace_id']  = $_POST['trace_id'];
			$data['preffered_contact_date'] = $this->input->post('preffered_contact_date');
			$data['preffered_contact_time'] = $this->input->post('preffered_contact_time');
			$data['mode_of_payment'] = (!empty($_POST['mode_of_payment'])) ? $_POST['mode_of_payment'] : '';
			if($data['mode_of_payment'] == "Cheque") {
				
				if(isset($_FILES["enrollment_form"]))
				{
					$tmpfile = $_FILES['enrollment_form']['tmp_name'];
					$filename = basename($_FILES['enrollment_form']['name']);
					$data['enrollment_form'] = curl_file_create($tmpfile, $_FILES['enrollment_form']['type'], $filename);
				}
			    if(isset($_FILES["cheaque_copy"]))
				{	
					$tmpfile = $_FILES['cheaque_copy']['tmp_name'];
					$filename = basename($_FILES['cheaque_copy']['name']);
					$data['cheque_copy'] = curl_file_create($tmpfile, $_FILES['cheaque_copy']['type'], $filename);
				}
			    if(isset($_FILES["itr"]))
				{
					$tmpfile = $_FILES['itr']['tmp_name'];
					$filename = basename($_FILES['itr']['name']);
					$data['itr'] = curl_file_create($tmpfile, $_FILES['itr']['type'], $filename);
				}
				if(isset($_FILES["cam"]))
				{
					$tmpfile = $_FILES['cam']['tmp_name'];
					$filename = basename($_FILES['cam']['name']);
					$data['cam'] = curl_file_create($tmpfile, $_FILES['cam']['type'], $filename);
				}
				if(isset($_FILES["medical"]))
				{
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
			$data['remark'] = (!empty($_POST['remark'])) ? $_POST['remark'] : '';
			if(!empty($data['lead_id'])){
			$addEdit = curlFunction(SERVICE_URL.'/api2/proposalFinalSubmit',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			}else{
			    echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
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
		
		if(!empty($data['lead_id'])){
		$addEdit = curlFunction(SERVICE_URL.'/api2/deletePolicyMember',$data);
		//echo "<pre>";print_r($addEdit);exit;
		$addEdit = json_decode($addEdit, true);
		
		if($addEdit['status_code'] == '200'){
			echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message'], 'data'=>$addEdit['Data']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
			exit;
		}
		}else{
			echo json_encode(array('success'=>false, 'msg'=>"Something went wrong try after some time"));
			exit;
		}
	}	
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api2/delPolicySubType',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}
	function customerpaymentform()
	{
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		unset($_SESSION['webpanel']['customerotp']);
		unset($_SESSION['webpanel']['customerlead']);
		
		$data = array();
		$data['lead_id'] = $record_id;
		$result = json_decode(curlFunction(SERVICE_URL.'/api2/sendcustomerpaymentformotp',$data));
		if($result['status_code'] == '200'){
			$this->load->view('template/header.php');
			$this->load->view('policyproposal/otpconfirmation',$data);
			$this->load->view('template/footer.php');
		}else{
			$this->load->view('template/header.php');
			$this->load->view('policyproposal/linkexpired');
			$this->load->view('template/footer.php');
		}	
		//echo $user_id;
		
	}
	
	function test_uploadview(){
		$this->load->view('policyproposal/test');
	}
	
	function testupload(){
		$data = array();
		$tmpfile = $_FILES['testfile']['tmp_name'];
		$filename = basename($_FILES['testfile']['name']);
		$data['testfile'] = curl_file_create($tmpfile, $_FILES['testfile']['type'], $filename);
		//print_r($data);exit;
		//$data['testfile'] = '@' . realpath($_FILES['testfile']);
		$ch = curl_init(SERVICE_URL.'/api2/test_upload');   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		//$result = curlFunction(SERVICE_URL.'/api2/test_upload',$data);
		echo $response; 
	}
	
	function customerpaymentformdetails()
	{
		
		$result = array();
		$data = array();
		
		if(isset($_SESSION['webpanel']['customerotp']) && isset($_SESSION['webpanel']['customerlead'])){
			$data['lead_id'] = $_SESSION['webpanel']['customerlead'];
			$data['otp'] = $_SESSION['webpanel']['customerotp'];
			$result['leaddetails'] = json_decode(curlFunction(SERVICE_URL.'/api2/getLeadDetailsCustomer',$data));
			$this->load->view('template/header.php');
			$this->load->view('policyproposal/addEditCustomer',$result);
			$this->load->view('template/footer.php');
		}else{
			$data['lead_id'] = $_POST['lead_id'];
			$data['otp'] = $_POST['otp'];
			$addEdit = json_decode(curlFunction(SERVICE_URL.'/api2/checkLeadotpCustomer',$data));
			if($addEdit['status_code'] == '200'){
				$_SESSION['webpanel']['customerlead'] = $addEdit['Data']['lead_id'];
				$_SESSION['webpanel']['customerotp'] = $addEdit['Data']['otp'];
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
		}
	}
}

?>
