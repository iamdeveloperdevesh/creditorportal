<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Applicationlogs extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	public function index()
	{
		$result = array();
		//Get all login sm locations
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getActions = curlFunction(SERVICE_URL.'/api/getLogActions',$data);
		$getActions = json_decode($getActions, true);
		//echo "<pre>";print_r($getActions);exit;
		$result['actions'] = $getActions['Data'];

		$this->load->view('template/header.php');
		$this->load->view('applicationlogs/index', $result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['sm_id'] = $_SESSION['webpanel']['employee_id'];
		//echo "<pre>";print_r($_GET);exit;
		$dataListing = curlFunction(SERVICE_URL.'/api/applicationLogs',$_GET);
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
				array_push($temp, $dataListing['Data']['query_result'][$i]['trace_id'] );
				array_push($temp, date("d-m-Y H:i:s", strtotime($dataListing['Data']['query_result'][$i]['created_on'])) );
				array_push($temp, $dataListing['Data']['query_result'][$i]['action'] );
				array_push($temp, str_replace('\\/',"/",$dataListing['Data']['query_result'][$i]['request_data']) );
				array_push($temp, $dataListing['Data']['query_result'][$i]['response_data'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['employee_full_name'] );
				
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}
	
	function exportexcel(){
		$data = array();
		//echo "<pre>";print_r($_POST);exit;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['trace_id'] = ($_POST['trace_id']) ? $_POST['trace_id'] : '';
		$data['customer_name'] = ($_POST['customer_name']) ? $_POST['customer_name'] : '';
		$data['customer_mob'] = ($_POST['customer_mob']) ? $_POST['customer_mob'] : '';
		$data['date_from'] = ($_POST['date_from']) ? $_POST['date_from'] : '';
		$data['date_to'] = ($_POST['date_to']) ? $_POST['date_to'] : '';
		
		//echo "<pre>";print_r($data);exit;
		$proposal_data = curlFunction(SERVICE_URL.'/api/exportApplicationLogs',$data);
		$mobiledata = json_decode($proposal_data, true);
		//echo "<pre>";print_r($mobiledata['Data']['query_result']);exit;
		//echo $mobiledata['Data'][0]['trace_id'];exit;
		//echo ABSOLUTE_DOC_ROOT;exit;
		 
		if($mobiledata['status_code'] != '200'){
			echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
				exit;
		}else{
		
			$fileName = 'ApplicationLogsExcel-'.time().'.xls'; 
	 
			$this->load->library('excel');
			//$mobiledata = $data;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			// set Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Trace ID');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Action/Type');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Request');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Response');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Created By');
			
			// set Row
			$rowCount = 2;
			for($i=0;$i < sizeof($mobiledata['Data']['query_result']);$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $mobiledata['Data']['query_result'][$i]['trace_id']);          
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $mobiledata['Data']['query_result'][$i]['created_on']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data']['query_result'][$i]['action']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $mobiledata['Data']['query_result'][$i]['request_data']);
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data']['query_result'][$i]['response_data']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $mobiledata['Data']['query_result'][$i]['employee_full_name']);
				$rowCount++;
			}
			
			
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/applicationlogexports/'.$fileName);
			$filepath = FRONT_URL.'/assets/applicationlogexports/'.$fileName;
			echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
			exit;
		}
	}
 	
}

?>