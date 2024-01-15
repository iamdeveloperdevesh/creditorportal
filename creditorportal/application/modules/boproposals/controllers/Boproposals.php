<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Boproposals extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$result = array();
		$this->load->view('template/header.php');
		$this->load->view('boproposals/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['role_id'] = $_SESSION['webpanel']['role_id'];
		$_GET['user_id'] = $_SESSION['webpanel']['employee_id'];
		//echo "<pre>GET ";print_r($_GET);exit;
		if(empty($_GET['sSearch_0']) && $_SESSION['webpanel']['role_id'] == 9){
			$userListing['Data']['totalRecords'] = 0;
			$userListing['Data']['query_result'] = "";
		}else{
			$userListing = curlFunction(SERVICE_URL.'/api/boProposalListing',$_GET);
			$userListing = json_decode($userListing, true);
			//echo "<pre>";print_r($userListing);exit;
			if($userListing['status_code'] == '401'){
				//echo "in condition";
				redirect('login');
				exit();
			}
		}
		
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
				array_push($temp, $userListing['Data']['query_result'][$i]['lan_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['full_name'] );
				//array_push($temp, $userListing['Data']['query_result'][$i]['mobile_no'] );
				//array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );
				if($userListing['Data']['query_result'][$i]['status'] =='Pending'){
					array_push($temp, "Proposal Creation" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Client-Approval-Awaiting'){
					array_push($temp, "Pending at Customer" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Customer-Payment-Awaiting'){
					array_push($temp, "Pending For Payment" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='BO-Approval-Awaiting'){
					array_push($temp, "Pending Branch Ops Verification" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='CO-Approval-Awaiting'){
					array_push($temp, "Pending Central Ops Verification" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Discrepancy'){
					array_push($temp, "Discrepancy Raised" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Approved'){
					array_push($temp, "Issued" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Rejected'){
					array_push($temp, "Cancelled" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='UW-Approval-Awaiting'){
					array_push($temp, "Pending With Underwriting" );
				}else{
					array_push($temp, $userListing['Data']['query_result'][$i]['status']);
				}
				array_push($temp, $userListing['Data']['query_result'][$i]['payment_mode_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['remark'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['transaction_number'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['cheque_number'] );
				array_push($temp, date("d-m-Y H:i:s", strtotime($userListing['Data']['query_result'][$i]['updatedon'])) );

				//array_push($temp, $userListing['Data']['query_result'][$i]['premium'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['premium_with_tax'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['loan_amt'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['sum_insured'] );
				

				//No. of days
				$no_days = 0;
				if($userListing['Data']['query_result'][$i]['status'] !='Approved' || $userListing['Data']['query_result'][$i]['status'] !='Rejected' ){
					$now = time(); // or your date as well
					$your_date = strtotime($userListing['Data']['query_result'][$i]['createdon']);
					$datediff = $now - $your_date;

					$no_days = round($datediff / (60 * 60 * 24));
				}

				array_push($temp, $no_days );
				$coi = "";
				if($userListing['Data']['query_result'][$i]['status'] =='Approved'){
					//$actionCOI ='<a href="javascript:void(0);" onclick="getCOI(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Accept">Get COI</a>';
					$coi .='<a href="proposalcoi/downloadCOI?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="COI" target="_blank">Get COIs</a>';
				}else{
					$coi ='-';
				}
				
				//array_push($temp, $coi );
				
				$actionCol = "";
				if(in_array('ProposalView',$this->RolePermission)){
					$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';
				}
				/*
				if($userListing['Data']['query_result'][$i]['status'] == 'BO-Approval-Awaiting'){
					$actionCol .=' | <a href="javascript:void(0);" onclick="acceptProposal(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Accept">Accept</a>';
					
					$actionCol .=' | <a href="javascript:void(0);" onclick="rejectProposal(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Reject">Reject</a>';
					
					//$actionCol .=' | <a href="javascript:void(0);" onclick="moveToUW(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Move to Underwriting">Move to Underwriting</a>';
					
					$actionCol .=' | <a href="boproposals/addEdit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Add Discrepancy</a>';
				}	
				*/
			
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
		$user_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$user_id = $url_prams['id'];
		}
		
		$result = array();
		$result['lead_id'] = $user_id;
		
		//Get all descripancy types
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getDiscrepancyType = curlFunction(SERVICE_URL.'/api/getDiscrepancyTypeData',$data);
		
		$getDiscrepancyType = json_decode($getDiscrepancyType, true);
		//echo "<pre>";print_r($getDiscrepancyType);exit;
		$result['getDiscrepancyType'] = $getDiscrepancyType['Data'];
		
		$this->load->view('template/header.php');
		$this->load->view('boproposals/addEdit',$result);
		$this->load->view('template/footer.php');
	}

	function rejectProposalView($id=NULL)
	{
		//print_r($_GET);
		$user_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$user_id = $url_prams['id'];
		}
		
		$result = array();
		$result['lead_id'] = $user_id;
		
		$this->load->view('template/header.php');
		$this->load->view('boproposals/rejectProposalView',$result);
		$this->load->view('template/footer.php');
	}
	
	public function getDiscrepancySubType() {
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['discrepancy_type_id'] = $_POST['discrepancy_type_id'];
		$getDiscrepancySubType = curlFunction(SERVICE_URL.'/api/getDiscrepancySubType',$data);
		$getDiscrepancySubType = json_decode($getDiscrepancySubType, true);
		//echo "<pre>";print_r($getDiscrepancySubType);exit;
		$subtypes = $getDiscrepancySubType['Data'];
		
		$option = '';
		if(!empty($subtypes)){
			for ($i = 0; $i < sizeof($subtypes); $i++){
				$option .= '<option value="'.$subtypes[$i]['discrepancy_subtype_id'].'" >'.$subtypes[$i]['discrepancy_subtype'].'</option>';
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
			$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
			$data['lead_id'] = $_POST['lead_id'];
			$data['discrepancy_type'] = $_POST['discrepancy_type'];
			$data['discrepancy_subtype'] = $_POST['discrepancy_subtype'];
			$data['remark'] = $_POST['remark'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addDiscrepancy',$data);
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

	function rejectsubmitForm()
	{
		// print_r($_POST);
		// exit;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
			$data['role_id'] = $_SESSION['webpanel']['role_id'];
			$data['lead_id'] = $_POST['lead_id'];
			$data['reject_reason'] = $_POST['reject_reason'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/rejectLead',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			// echo "<pre>";print_r($addEdit);exit;
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
 
	//For accept Proposal
	function acceptProposal($id)
	{
		$username = $_SESSION['webpanel']['employee_fname']." ".$_SESSION['webpanel']['employee_mname']." ".$_SESSION['webpanel']['employee_lname'];
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		$data['role_id'] = $_SESSION['webpanel']['role_id'];
		$data['login_user_name'] = $username;
		$delRecord = curlFunction(SERVICE_URL.'/api/acceptProposal',$data);
		//echo "<pre>";print_r($delRecord);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			//echo "1";
			if($delRecord['Data'] == 2){
				echo json_encode(array('success'=> 1, 'msg'=>'Lead Approved Successfully and submitted for policy genration.', 'data' => $id) );
				exit;
			}else if($delRecord['Data'] == 1){
				echo json_encode(array('success'=> 3, 'msg'=>'Lead Approved Successfully and submitted for UW Approval.', 'data' => null) );
				exit;
			}
			
		}else{
			echo json_encode(array('success'=> 2, 'msg'=>'Lead Approved Error.', 'data' => $id) );
			exit;
		}	
	}
	
	function passLeadsToInsurance(){
		//echo "<pre>";print_r($_POST);exit;
		if(!empty($_POST['lead_id'])){
			$url = SERVICE_URL.'/api2/doInsurance';
			$str = "lead_id=".$_POST['lead_id']."&mode_of_payment=Cheque";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			//curl_setopt($ch, CURLOPT_UPLOAD, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
			
			// execute!
			$errors = curl_error($ch);
			$response = curl_exec($ch);
			//echo "<pre>";print_r($response);exit;

			// close the connection, release resources used
			curl_close($ch);
		}	
	}


	//For reject Proposal
	function rejectProposal($id)
	{
		$username = $_SESSION['webpanel']['employee_fname']." ".$_SESSION['webpanel']['employee_mname']." ".$_SESSION['webpanel']['employee_lname'];
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		$data['login_user_name'] = $username;
		$delRecord = curlFunction(SERVICE_URL.'/api/rejectProposal',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}
	
	//For move to UW
	function moveToUW($id)
	{
		$username = $_SESSION['webpanel']['employee_fname']." ".$_SESSION['webpanel']['employee_mname']." ".$_SESSION['webpanel']['employee_lname'];
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		$data['login_user_name'] = $username;
		$delRecord = curlFunction(SERVICE_URL.'/api/moveToUW',$data);
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