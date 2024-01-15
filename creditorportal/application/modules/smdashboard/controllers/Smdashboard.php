<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Smdashboard extends CI_Controller 
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
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['role_id'] = $_SESSION['webpanel']['role_id'];
		$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		if($_SESSION['webpanel']['role_id'] == 3){
			$getCreditors = curlFunction(SERVICE_URL.'/api/getRoleWiseCreditorsData',$data);
          //  echo "<pre>";print_r($getCreditors);exit;
		}else{
			$getCreditors = curlFunction(SERVICE_URL.'/api/getCreditorsData',$data);
		}
		$getCreditors = json_decode($getCreditors, true);
		//echo "<pre>";print_r($getCreditors);exit;
		$result['creditors'] = $getCreditors['Data'];
		
		//Get all login sm locations
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getLocations = curlFunction(SERVICE_URL.'/api/getLocationsData',$data);
		$getLocations = json_decode($getLocations, true);
		//echo "<pre>";print_r($getLocations);exit;
		$result['locations'] = $getLocations['Data'];
		
		//Get all SM
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getSMs = curlFunction(SERVICE_URL.'/api/getSMData',$data);
		$getSMs = json_decode($getSMs, true);
		//echo "<pre>";print_r($getLocations);exit;
		$result['sm'] = $getSMs['Data'];
		
		$this->load->view('template/header.php');
		$this->load->view('smdashboard/index', $result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['sm_id'] = $_SESSION['webpanel']['employee_id'];
		//echo $_GET['sSearch_4'];exit;
		$_GET['sSearch_4'] = (!empty($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : 'desc';
		$dataListing = curlFunction(SERVICE_URL.'/api/smDashBorad',$_GET);
       // echo "<pre>";print_r($dataListing);exit;
		$dataListing = json_decode($dataListing, true);
		//echo "<pre>";print_r($dataListing);exit;
		if($dataListing['status_code'] == '401'){
			redirect('login');
			exit();
		}
		
		//echo $dataListing['Data']['tot_premium'];exit;
		$tot_premium = 0;
		$tot_premium = $dataListing['Data']['tot_premium'];
		$tot_premium_withtax = 0;
		$tot_premium_withtax = $dataListing['Data']['tot_premium_withtax'];

		$weeklyNetTot = $weeklyGrossTot = $mothlyNetTot = $mothlyGrossTot = $yearlyNetTot = $yearlyGrossTot = $dateRangeNetTot = $dateRangeGrossTot = 0;

		$weeklyNetTot = $dataListing['Data']['weeklyNetTot'];
		$weeklyGrossTot = $dataListing['Data']['weeklyGrossTot'];
		$mothlyNetTot = $dataListing['Data']['mothlyNetTot'];
		$mothlyGrossTot = $dataListing['Data']['mothlyGrossTot'];
		$yearlyNetTot = $dataListing['Data']['yearlyNetTot'];
		$yearlyGrossTot = $dataListing['Data']['yearlyGrossTot'];
		$dateRangeNetTot = (!empty($dataListing['Data']['dateRangeNetTot'])) ? $dataListing['Data']['dateRangeNetTot'] : 0;
		$dateRangeGrossTot = (!empty($dataListing['Data']['dateRangeGrossTot'])) ? $dataListing['Data']['dateRangeGrossTot'] : 0;
		
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
				array_push($temp, $dataListing['Data']['query_result'][$i]['rank'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
				
				array_push($temp, round($dataListing['Data']['query_result'][$i]['weekly_tot']) );
				array_push($temp, round($dataListing['Data']['query_result'][$i]['weekly_tot_withtax']) );

				array_push($temp, round($dataListing['Data']['query_result'][$i]['monthly_tot']) );
				array_push($temp, round($dataListing['Data']['query_result'][$i]['monthly_tot_withtax']) );

				array_push($temp, round($dataListing['Data']['query_result'][$i]['yearly_tot']) );
				array_push($temp, round($dataListing['Data']['query_result'][$i]['yearly_tot_withtax']) );

				array_push($temp, round($dataListing['Data']['query_result'][$i]['range_total']) );
				array_push($temp, round($dataListing['Data']['query_result'][$i]['range_total_withtax']) );

				array_push($temp, $dataListing['Data']['query_result'][$i]['date_from'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['date_to'] );
				
				$premiumsum = $dataListing['Data']['query_result'][$i]['premiumsum'];
				
				$actionCol = "";
				$actionCol .='<a href="dashboarddetails?cid='.$dataListing['Data']['query_result'][$i]['creditor_id'].'&smid='.$dataListing['Data']['query_result'][$i]['created_by'].' " title="View"><span class="spn-9"><i class="ti-eye"></i></span></a>';
				
				array_push($temp, $actionCol);
				
				
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		$result["saleadmintotal"] = round($tot_premium);
		$result["saleadmintotalwithtax"] = round($tot_premium_withtax);

		$result["weeklyNetTot"] = round($weeklyNetTot);
		$result["weeklyGrossTot"] = round($weeklyGrossTot);
		$result["mothlyNetTot"] = round($mothlyNetTot);
		$result["mothlyGrossTot"] = round($mothlyGrossTot);
		$result["yearlyNetTot"] = round($yearlyNetTot);
		$result["yearlyGrossTot"] = round($yearlyGrossTot);
		$result["dateRangeNetTot"] = round($dateRangeNetTot);
		$result["dateRangeGrossTot"] = round($dateRangeGrossTot);

		echo json_encode($result);
		exit;
	}
	
	function exportexcel(){
		$data = array();
		//echo "<pre>";print_r($_POST);exit;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['creditor_id'] = ($_POST['creditor_id']) ? $_POST['creditor_id'] : '';
		$data['sm_id'] = $_SESSION['webpanel']['employee_id'];
		$data['status'] = ($_POST['status']) ? $_POST['status'] : '';
		$data['date_from'] = ($_POST['date_from']) ? $_POST['date_from'] : '';
		$data['date_to'] = ($_POST['date_to']) ? $_POST['date_to'] : '';
		
		//echo "<pre>";print_r($data);exit;
		$proposal_data = curlFunction(SERVICE_URL.'/api/exportSMDashBorad',$data);
		$mobiledata = json_decode($proposal_data, true);
		//echo "<pre>";print_r($mobiledata['Data']);exit;
		//echo $mobiledata['Data'][0]['trace_id'];exit;
		//echo ABSOLUTE_DOC_ROOT;exit;
		 
		if($mobiledata['status_code'] != '200'){
			echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
				exit;
		}else{
		
			$fileName = 'SmdashboardExcel-'.time().'.xls'; 
	 
			$this->load->library('excel');
			//$mobiledata = $data;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			// set Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Current Ranking');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Channel Patner Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'This Week - Net');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'This Week - Gross');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'MTD - Net');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'MTD - Gross');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'YTD - Net');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'YTD - Gross');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Date Range Total - Net');
			$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Date Range Total - Gross');
			$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Date From');
			$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Date To');
			
			// set Row
			$rowCount = 2;
			//foreach ($mobiledata as $val) 
			for($i=0;$i < sizeof($mobiledata['Data']['query_result']);$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $mobiledata['Data']['query_result'][$i]['rank']);          
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $mobiledata['Data']['query_result'][$i]['creaditor_name']);

				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data']['query_result'][$i]['weekly_tot']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $mobiledata['Data']['query_result'][$i]['weekly_tot_withtax']);

				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data']['query_result'][$i]['monthly_tot']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $mobiledata['Data']['query_result'][$i]['monthly_tot_withtax']);

				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $mobiledata['Data']['query_result'][$i]['yearly_tot']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $mobiledata['Data']['query_result'][$i]['yearly_tot_withtax']);

				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $mobiledata['Data']['query_result'][$i]['range_total']);
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $mobiledata['Data']['query_result'][$i]['range_total_withtax']);

				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $mobiledata['Data']['query_result'][$i]['date_from']);
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $mobiledata['Data']['query_result'][$i]['date_to']);
				$rowCount++;
			}
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, 'Total');
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data']['weeklyNetTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $mobiledata['Data']['weeklyGrossTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data']['mothlyNetTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $mobiledata['Data']['mothlyGrossTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $mobiledata['Data']['yearlyNetTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $mobiledata['Data']['yearlyGrossTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $mobiledata['Data']['dateRangeNetTot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $mobiledata['Data']['dateRangeGrossTot']);


			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/smdashboardexports/'.$fileName);
			$filepath = FRONT_URL.'/assets/smdashboardexports/'.$fileName;
			echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
			exit;
		}
	}	
}
?>