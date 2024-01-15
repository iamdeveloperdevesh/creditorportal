<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Customerleads extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		//$this->load->model('usersmodel','',TRUE);
		//checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$result = array();
		$this->load->view('template/header.php');
		$this->load->view('customerleads/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['role_id'] = $_SESSION['webpanel']['role_id'];
		$_GET['user_id'] = $_SESSION['webpanel']['employee_id'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$userListing = curlFunction(SERVICE_URL.'/api/leadListing',$_GET);
		$userListing = json_decode($userListing, true);
		//echo "<pre>";print_r($userListing);exit;
		if($userListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $userListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $userListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($userListing['Data']['query_result']) && count($userListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($userListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $userListing['Data']['query_result'][$i]['trace_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['mobile_no'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );
				
				$actionCol = "";
				if(in_array('ProposalAdd',$this->RolePermission)){
					$actionCol .='<a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Add Proposal</a>';
				}
			
				array_push($temp, $actionCol);
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}
	
	function addEdit($id=NULL)
	{
		//print_r($_GET);
		$record_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['role_id'] = $_SESSION['webpanel']['role_id'];
		$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		if($_SESSION['webpanel']['role_id'] == 3){
			$getCreditors = curlFunction(SERVICE_URL.'/api/getRoleWiseCreditorsData',$data);
		}else{
			$getCreditors = curlFunction(SERVICE_URL.'/api/getCreditorsData',$data);
		}
		$getCreditors = json_decode($getCreditors, true);
		//echo "<pre>";print_r($getCreditors);exit;
		$result['creditors'] = $getCreditors['Data'];
		
		//Get all SM
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getSMs = curlFunction(SERVICE_URL.'/api/getSMData',$data);
		$getSMs = json_decode($getSMs, true);
		//echo "<pre>";print_r($getLocations);exit;
		$result['sm'] = $getSMs['Data'];
		
		$result['salutation'] = get_enum_values('master_customer','salutation');
		$result['gender'] = get_enum_values('master_customer','gender');
		
		$this->load->view('template/header.php');
		$this->load->view('customerleads/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	
	public function getPlans() {
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['creditor_id'] = $_POST['creditor_id'];
		$getPlans = curlFunction(SERVICE_URL.'/api/getCreditorsPlansData',$data);
		$getPlans = json_decode($getPlans, true);
		//echo "<pre>";print_r($getPlans);exit;
		$plans = $getPlans['Data'];
		
		$option = '';
		if(!empty($plans)){
			for ($i = 0; $i < sizeof($plans); $i++){
				$option .= '<option value="'.$plans[$i]['plan_id'].'" >'.$plans[$i]['plan_name'].'</option>';
			}
		}
		//echo $option;exit;
		echo json_encode(array("status" => "success", "option" => $option));
		exit;
		
		
	}
 
	function submitForm()
	{
		// print_r($_POST);
		// exit;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			if(!empty($_POST['sm_id'])){
				$data['sm_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
			}else{
				$data['sm_id'] = $_SESSION["webpanel"]['employee_id'];
			}
			$data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
			$data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
			$data['middle_name'] = (!empty($_POST['middle_name'])) ? $_POST['middle_name'] : '';
			$data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
			$data['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : '';
			$data['dob'] = (!empty($_POST['dob'])) ? $_POST['dob'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['mobile_number'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			
			$data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
			$data['portal_id'] = 'Creditor Portal';
			$data['vertical'] = 'Vertical';
			$data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
			$data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
			$data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : '';
			$data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addLead',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			
		}
		else
		{
			echo json_encode(array('success' => false, 'msg'=>'Problem while updating record.'));
			exit;
		}
	}
 
	//For Delete
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delUser',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}	
	
}

?>