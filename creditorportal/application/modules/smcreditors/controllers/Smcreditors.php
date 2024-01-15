<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Smcreditors extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	public function index()
	{
		//$enum = get_enum_values('proposal_nominee_details', 'salutation');
		//echo "<pre>";print_r($enum);exit;
		$this->load->view('template/header.php');
		$this->load->view('smcreditors/index');
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$dataListing = curlFunction(SERVICE_URL.'/api/smCreditorMappingListing',$_GET);
		$dataListing = json_decode($dataListing, true);
		//echo "<pre>";print_r($dataListing);exit;
		if($dataListing['status_code'] == '401'){
			redirect('login');
			exit();
		}
		
		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $dataListing['Data']['totalRecords'];
		$result["iTotalDisplayRecords"]= $dataListing['Data']['totalRecords'];

		$items = array();
		
		if(!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($dataListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['employee_full_name'] );
				
				if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
					//array_push($temp, 'Active' );
				}else{
					//array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				if(in_array('SMCreditorMappingEdit',$this->RolePermission)){
					//$actionCol .='<a href="smcreditors/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['sm_creditor_id'] ), '+/', '-_'), '=').'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';
				}
				if(in_array('SMCreditorMappingDelete',$this->RolePermission)){
					if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$dataListing['Data']['query_result'][$i]['sm_creditor_id'] .'\');" title="Delete"><span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
					}
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
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		$result = array();
		
		//Get all creditors
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getCreditors = curlFunction(SERVICE_URL.'/api/getCreditorsData',$data);
		$getCreditors = json_decode($getCreditors, true);
		//echo "<pre>";print_r($getCreditors);exit;
		$result['creditors'] = $getCreditors['Data'];
		
		//Get all SM
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getSMs = curlFunction(SERVICE_URL.'/api/getSMData',$data);
		$getSMs = json_decode($getSMs, true);
		//echo "<pre>";print_r($getLocations);exit;
		$result['sm'] = $getSMs['Data'];
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api/getSMCreditorFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['user_details'] = $checkDetails['Data'];
			
		}else{
			$result['user_details'] = array();
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('smcreditors/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	
	public function getSMData() {
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['creditor_id'] = $_POST['creditor_id'];
		$getSMs = curlFunction(SERVICE_URL.'/api/getSMDataCreditorWise',$data);
		$getSMs = json_decode($getSMs, true);
		//echo "<pre>";print_r($getSMs);exit;
		$SMs = $getSMs['Data'];
		
		$option = '';
		if(!empty($SMs)){
			for ($i = 0; $i < sizeof($SMs); $i++){
				$option .= '<option value="'.$SMs[$i]['employee_id'].'" >'.$SMs[$i]['employee_full_name'].'</option>';
			}
		}
		//echo $option;exit;
		echo json_encode(array("status" => "success", "option" => $option));
		exit;
		
		
	}
 
	function submitForm()
	{
		/*print_r($_POST);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			if(empty($_POST['sm_id'])){
				echo json_encode(array("success"=>false, 'msg'=>'Please select atleast one SM.'));
				exit;
			}
			
			if(!empty($_POST['sm_creditor_id'])){
				$data = array();
				$data['utoken'] = $_SESSION['webpanel']['utoken'];
				$data['sm_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			}else{
				$data = array();
				$data['utoken'] = $_SESSION['webpanel']['utoken'];
				$data['sm_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$data['creditor_id'] = $_POST['creditor_id'];
				$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			}
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditSMCreditorMapping',$data);
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
			echo json_encode(array("success"=>false, 'msg'=>'Problem While Add/Edit Record..'));
			exit;
		}
	}

	function exportexcel(){
		$data = array();
		//echo "<pre>";print_r($_POST);exit;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['sm_name'] = ($_POST['sm_name']) ? $_POST['sm_name'] : '';
		$data['creditor_name'] = ($_POST['creditor_name']) ? $_POST['creditor_name'] : '';
		
		//echo "<pre>";print_r($data);exit;
		$proposal_data = curlFunction(SERVICE_URL.'/api/exportSMCreditors',$data);
		$mobiledata = json_decode($proposal_data, true);
		//echo "<pre>";print_r($mobiledata['Data']['query_result']);exit;
		//echo ABSOLUTE_DOC_ROOT;exit;
		 
		if($mobiledata['status_code'] != '200'){
			echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
				exit;
		}else{
			$fileName = 'SMCreditorMappingExcel-'.time().'.xls'; 
	 
			$this->load->library('excel');
			//$mobiledata = $data;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			// set Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Creditor Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'SM Name');
			
			// set Row
			$rowCount = 2;
			//foreach ($mobiledata as $val) 
			for($i=0;$i < sizeof($mobiledata['Data']['query_result']);$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $mobiledata['Data']['query_result'][$i]['creaditor_name']);          
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $mobiledata['Data']['query_result'][$i]['employee_full_name']);
				$rowCount++;
			}
			
			
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/saleadminexportexcel/'.$fileName);
			$filepath = FRONT_URL.'/assets/saleadminexportexcel/'.$fileName;
			echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
			exit;
		}
	}
	
	function delRecord($id)
	{
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delSMCreditor',$data);
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