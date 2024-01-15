<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Creditorbranches extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		//$this->load->model('permissionmodel','',TRUE); 
		checklogin();
		$this->RolePermission = getRolePermissions();
		
		//Get user access
		/*$access_data = array();
		$access_data['role_id'] = $_SESSION["webpanel"]['role_id'];
		$access_data['utoken'] = $_SESSION['webpanel']['utoken'];
		$accessRecord = curlFunction(SERVICE_URL.'/api/getLoginUserAccess',$access_data);
		$this->RolePermission = $accessRecord;*/
		//echo "<pre>";print_r($accessRecord);exit;
	}
 
	public function index()
	{
		//echo "<pre>in";print_r($this->RolePermission);exit;
		$this->load->view('template/header.php');
		$this->load->view('creditorbranches/index');
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$dataListing = curlFunction(SERVICE_URL.'/api/branchListing',$_GET);
		$dataListing = json_decode($dataListing, true);
		//echo "<pre>";print_r($dataListing);exit;
		if($dataListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $dataListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $dataListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($dataListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $dataListing['Data']['query_result'][$i]['branch_name'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['location_name'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['contact_no'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['email_id'] );
				if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				
				$actionCol = "";
				if(in_array('CreditorBranchEdit',$this->RolePermission)){
					$actionCol .='<a href="creditorbranches/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['branch_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				}
				if(in_array('CreditorBranchDelete',$this->RolePermission)){
					if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$dataListing['Data']['query_result'][$i]['branch_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
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
		
		//Get all locations
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getLocations = curlFunction(SERVICE_URL.'/api/getLocationsData',$data);
		$getLocations = json_decode($getLocations, true);
		//echo "<pre>";print_r($getLocations);exit;
		$result['locations'] = $getLocations['Data'];
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api/getBranchesFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['user_details'] = $checkDetails['Data'];
			
		}else{
			$result['user_details'] = array();
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('creditorbranches/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		/*print_r($_POST);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			//check duplicate record.
			$checkdata = array();
			$checkdata['branch_name'] = $_POST['branch_name'];
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			if(isset($_POST['branch_id']) && $_POST['branch_id'] > 0){
				$checkdata['branch_id'] = $_POST['branch_id'];
			}
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateBranch',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['branch_id'] = (!empty($_POST['branch_id'])) ? $_POST['branch_id'] : '';
			$data['branch_name'] = (!empty($_POST['branch_name'])) ? $_POST['branch_name'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : '';
			$data['contact_no'] = (!empty($_POST['contact_no'])) ? $_POST['contact_no'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditBranches',$data);
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
		$delRecord = curlFunction(SERVICE_URL.'/api/delBranch',$data);
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