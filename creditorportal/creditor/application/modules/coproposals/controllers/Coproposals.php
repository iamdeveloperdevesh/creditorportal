<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Coproposals extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		//echo "<pre>";print_r($_SESSION);exit;
		//$this->load->model('usersmodel','',TRUE);
		//checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$result = array();
		$this->load->view('template/header.php');
		$this->load->view('coproposals/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['role_id'] = $_SESSION['webpanel']['role_id'];
		$_GET['user_id'] = $_SESSION['webpanel']['employee_id'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$userListing = curlFunction(SERVICE_URL.'/api/coProposalListing',$_GET);
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
				$checkbox = "<input type='checkbox' class='check form-control' data-id='".$userListing['Data']['query_result'][$i]['trace_id']."' />";
				$temp = array();
				array_push($temp, $checkbox );
				array_push($temp, $userListing['Data']['query_result'][$i]['trace_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['mobile_no'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['status'] );
				array_push($temp, date("d-m-Y H:i:s", strtotime($userListing['Data']['query_result'][$i]['updatedon'])) );
				
				$actionCol = "";
				if(in_array('ProposalView',$this->RolePermission)){
					$actionCol .='<a href="policyproposal/viewdetails?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';
				}
				
				if($userListing['Data']['query_result'][$i]['status'] == 'CO-Approval-Awaiting'){
					$actionCol .=' | <a href="javascript:void(0);" onclick="acceptProposal(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Accept">Accept</a>';
					
					$actionCol .=' | <a href="javascript:void(0);" onclick="rejectProposal(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Reject">Reject</a>';
					
					$actionCol .=' | <a href="javascript:void(0);" onclick="moveToUW(\''.$userListing['Data']['query_result'][$i]['lead_id'] .'\');" title="Move to Underwriting">Move to Underwriting</a>';
					
					$actionCol .=' | <a href="boproposals/addEdit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Add Discrepancy</a>';
				}
				
			
				array_push($temp, $actionCol);
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}
 
	//For accept Proposal
	function acceptProposal($id)
	{
		$username = $_SESSION['webpanel']['employee_fname']." ".$_SESSION['webpanel']['employee_mname']." ".$_SESSION['webpanel']['employee_lname'];
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		$data['login_user_name'] = $username;
		$delRecord = curlFunction(SERVICE_URL.'/api/acceptProposal',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
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
	
	function uploadexcel()
	{
		$data = array();
		
		if(isset($_FILES["importdata"]["name"]))
       {
			$data['path'] = $_FILES["importdata"]["tmp_name"];
			$data = curlFunction(SERVICE_URL.'/api2/uploadcoexcel',$data);
			echo $data;
	   }else{
		   echo "data not found";
	   } 
	}
	
	
	function exportexcel(){
		$data = array();
		//$data['id'] = $id;
		$data['id'] = $_POST['id'];
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		$data['leads'] = $_POST['id'];
		$proposal_data = curlFunction(SERVICE_URL.'/api2/getproposalpolicybylead',$data);
		$mobiledata = json_decode($proposal_data, true);
		 
		if($mobiledata['status_code'] != '200'){
			echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
				exit;
		}else{
		
		$fileName = 'coworklist-'.time().'.xlsx'; 
 
        $this->load->library('excel');
        $mobiledata = $data;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Sr_No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Proposal_no');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'LAN_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Enrolment_Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Premium_Amount');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Payment_Mode');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'HB Receipt number');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Transaction_Reference_no');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Amount');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Payment Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Status');
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Remarks');
		
        // set Row
        $rowCount = 2;
        foreach ($mobiledata as $val) 
        {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $rowCount-1);          
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $val['proposal_policy_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $val['lead_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, date('Y-m-d',strtotime($val['created_at'])));
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $val['premium_amount']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, 'NEFT');
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, '');
            $rowCount++;
        }
 
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(ABSOLUTE_DOC_ROOT.'assets/coexportexcel/'.$fileName);
		$filepath = ABSOLUTE_DOC_ROOT.'assets/coexportexcel/'.$fileName;
		echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
				exit;
	}
	}	
	
}

?>