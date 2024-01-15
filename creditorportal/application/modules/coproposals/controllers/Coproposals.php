<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Coproposals extends CI_Controller 
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
				$checkbox = "<input type='checkbox' class='check form-control' data-id='".$userListing['Data']['query_result'][$i]['lead_id']."' />";
				$temp = array();
				array_push($temp, $checkbox );
				array_push($temp, $userListing['Data']['query_result'][$i]['trace_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['lan_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['mobile_no'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );
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
				array_push($temp, date("d-m-Y H:i:s", strtotime($userListing['Data']['query_result'][$i]['updatedon'])) );

				array_push($temp, $userListing['Data']['query_result'][$i]['premium'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['premium_with_tax'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['loan_amt'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['sum_insured'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['payment_mode_name'] );

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
				
				array_push($temp, $coi );
				
				$actionCol = "";
				if(in_array('ProposalView',$this->RolePermission)){
					$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';
				}
				
				/*
				if($userListing['Data']['query_result'][$i]['status'] == 'CO-Approval-Awaiting'){
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
		
		if(isset($_FILES["importdata"]["name"])){
			//$data['path'] = $_FILES["importdata"]["tmp_name"];
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
			
			$target_dir = DOC_ROOT.'/assets/coimportexcel/';
			$file = $_FILES['importdata']['name'];
			$path = pathinfo($file);
			$filename = $path['filename'];
			$ext = $path['extension'];
			$temp_name = $_FILES['importdata']['tmp_name'];
			$path_filename_ext = $target_dir.$filename.".".$ext;
			if (file_exists($path_filename_ext)) {
				unlink($path_filename_ext);
				move_uploaded_file($temp_name,$path_filename_ext);
			}else{
				move_uploaded_file($temp_name,$path_filename_ext);
			}
			
			//echo $path_filename_ext;exit;
			
			//echo "<pre>";print_r($_FILES);exit;
			
			//$data['path'] = $path_filename_ext;
			$data['path'] = curl_file_create($path_filename_ext, $_FILES['importdata']['type'], basename($_FILES['importdata']['name']));
			//echo $data['path'];exit;
			
			//echo $data['path'];exit;
			$uploadExcel = curlFileFunction(SERVICE_URL.'/api/uploadcoexcel',$data);
			$uploadExcel = json_decode($uploadExcel, true);
			//echo "<preeee>";print_r($uploadExcel);exit;
			
			if($uploadExcel['status_code'] == '200'){
				//unlink($path_filename_ext);
				echo json_encode(array('success'=>true, 'msg'=>$uploadExcel['Metadata']['Message'], 'data' => $uploadExcel['Data']) );
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$uploadExcel['Metadata']['Message']));
				exit;
			}
			
		}else{
		   echo "data not found";
		} 
	}
	
	function passLeadsToInsurance(){
		//echo "<pre>";print_r($_POST);exit;
		if(!empty($_POST['leadArr'])){
			$url = SERVICE_URL.'/api2/doInsurance';
			for($i=0;$i < sizeof($_POST['leadArr']);$i++){
				//$_POST['lead_id'] = $_POST['leadArr'][$i];
				$str = "lead_id=".$_POST['leadArr'][$i]."&mode_of_payment=NEFT";
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
	}
	
	
	function exportexcel(){
		$data = array();
		//echo "<pre>";print_r($_POST);exit;
		//$data['id'] = $id;
		$data['id'] = $_POST['id'];
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['login_user_id'] = $_SESSION['webpanel']['employee_id'];
		$data['leads'] = $_POST['id'];
		//echo "<pre>";print_r($data);exit;
		$proposal_data = curlFunction(SERVICE_URL.'/api/getproposalpolicybylead',$data);
		$mobiledata = json_decode($proposal_data, true);
		//echo "<pre>";print_r($mobiledata['Data']);exit;
		//echo $mobiledata['Data'][0]['trace_id'];exit;
		//echo ABSOLUTE_DOC_ROOT;exit;
		 
		if($mobiledata['status_code'] != '200'){
			echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
				exit;
		}else{
		
			$fileName = 'coworklist-'.time().'.xls'; 
	 
			$this->load->library('excel');
			//$mobiledata = $data;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			// set Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Sr_No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Trace ID');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'LAN ID');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Enrolment_Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Premium_Amount');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Payment_Mode');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'HB Receipt number');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Transaction_Reference_no');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Amount');
			$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Payment Date (d/m/Y)');
			//$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Status');
			$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Remarks');
			$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Exported On');
			
			// set Row
			$rowCount = 2;
			//foreach ($mobiledata as $val) 
			for($i=0;$i < sizeof($mobiledata['Data']);$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $rowCount - 1);          
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $mobiledata['Data'][$i]['trace_id']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data'][$i]['lan_id']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, date('d-m-Y',strtotime($mobiledata['Data'][$i]['created_at'])));
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data'][$i]['premium_with_tax']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, 'NEFT');
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, '');
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, '');
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, '');
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, '');
				//$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, '');
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, '');
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, date("d-m-Y"));
				$rowCount++;
			}
			
			
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save(DOC_ROOT.'/assets/coexportexcel/'.$fileName);
			$filepath = FRONT_URL.'/assets/coexportexcel/'.$fileName;
			echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
			exit;
		}
	}	
	
}

?>