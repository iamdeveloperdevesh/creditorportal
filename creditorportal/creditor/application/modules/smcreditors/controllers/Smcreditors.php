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
				array_push($temp, $dataListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
				if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
					//array_push($temp, 'Active' );
				}else{
					//array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				if(in_array('SMCreditorMappingEdit',$this->RolePermission)){
					//$actionCol .='<a href="smcreditors/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['sm_creditor_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				}
				if(in_array('SMCreditorMappingDelete',$this->RolePermission)){
					if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$dataListing['Data']['query_result'][$i]['sm_creditor_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
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
 
	function submitForm()
	{
		/*print_r($_POST);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
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