<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Roles extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		//$this->load->model('rolesmodel','',TRUE); 
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('roles/index');
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$dataListing = curlFunction(SERVICE_URL.'/api/roleListing',$_GET);
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
				array_push($temp, $dataListing['Data']['query_result'][$i]['role_name'] );
				
				
				$actionCol = "";
				if(in_array('RoleEdit',$this->RolePermission)){
					$actionCol .='<a href="roles/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['role_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					//if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
						//$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$dataListing['Data']['query_result'][$i]['role_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
					//}
				//}
			
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
		
		if(!empty($record_id)){
			//echo $record_id;
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$formData = curlFunction(SERVICE_URL.'/api/getRoleFormData',$data);
			$formData = json_decode($formData, true);
			//echo "<pre>dd";print_r($formData);exit;
			$result['getDetails'] = $formData['Data']['role_data'];
			if(!empty($formData['Data']['role_data'])){
				for($i=0; $i < sizeof($formData['Data']['role_perms']); $i++){
					$result['selpermissions'][] = $formData['Data']['role_perms'][$i]['perm_id'];
				}
			}
			
			//echo "<pre>dd";print_r($result);exit;
			
		}else{
			$result['getDetails'] = array();
		}
		
		//$result['role_details'] = $this->rolesmodel->getFormdata($record_id);
		//$result['permissions'] = $this->rolesmodel->getData("*","permissions","","perm_desc","asc");
		
		//Get all active permissions
		$perm = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getPermissions = curlFunction(SERVICE_URL.'/api/getPermissionsData',$data);
		$getPermissions = json_decode($getPermissions, true);
		//echo "<pre>";print_r($getPermissions);exit;
		$result['permissions'] = $getPermissions['Data'];
		
		/*if(!empty($record_id))
		{
			$selpermissions = $this->rolesmodel->getData("perm_id", "role_perm", "role_id = '".$record_id."'");
			if(!empty($selpermissions) && isset($selpermissions))
			{
				foreach($selpermissions as $key => $val)
				{
					$result['selpermissions'][] = $val->perm_id;
				}
			}
		}*/
		
		// echo "<pre>";
		// print_r($result);
		// exit;
		
		$this->load->view('template/header.php');
		$this->load->view('roles/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			//check duplicate record.
			//echo "";print_r($_POST);exit;
			$checkdata = array();
			$checkdata['role_name'] = $_POST['role_name'];
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			if(isset($_POST['role_id']) && $_POST['role_id'] > 0){
				$checkdata['role_id'] = $_POST['role_id'];
			}
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateRole',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['role_id'] = (!empty($_POST['role_id'])) ? $_POST['role_id'] : '';
			$data['role_name'] = $_POST['role_name'];
			$data['role_permissions'] = $_POST['perm_id'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditRoles',$data);
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
			echo json_encode(array("success"=>false, 'msg'=>'Problem while Add/Edit data.'));
			exit;
		}
	}
 
	//For Delete
	function delRecord($id)
	{
		$appdResult = $this->rolesmodel->delrecord("roles","role_id",$id);
		$appdResult1 = $this->rolesmodel->delrecord("role_perm","role_id",$id);
	 
		if($appdResult)
		{
			echo "1";
		}
		else
		{
			echo "2";	 
		}	
	}	
}

?>