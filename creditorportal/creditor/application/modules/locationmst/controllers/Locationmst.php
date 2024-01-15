<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Locationmst extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('locationmst/index');
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$dataListing = curlFunction(SERVICE_URL.'/api/locationListing',$_GET);
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
				array_push($temp, $dataListing['Data']['query_result'][$i]['state_name'] );
				array_push($temp, $dataListing['Data']['query_result'][$i]['city_name'] );
				
				
				$actionCol = "";
				if(in_array('LocationEdit',$this->RolePermission)){
					$actionCol .='<a href="locationmst/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['city_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					//if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
						//$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$dataListing['Data']['query_result'][$i]['city_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
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
		
		//Get all creditors
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getStates = curlFunction(SERVICE_URL.'/api/getStateData',$data);
		$getStates = json_decode($getStates, true);
		//echo "<pre>";print_r($getStates);exit;
		$result['states'] = $getStates['Data'];
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api/getLocationFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['getDetails'] = $checkDetails['Data'];
			
		}else{
			$result['getDetails'] = array();
		}
		
		//echo "<pre>";print_r($result);exit;
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('locationmst/addEdit',$result);
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
			$checkdata['city_name'] = $_POST['city_name'];
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			if(isset($_POST['city_id']) && $_POST['city_id'] > 0){
				$checkdata['city_id'] = $_POST['city_id'];
			}
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateLocation',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['city_id'] = (!empty($_POST['city_id'])) ? $_POST['city_id'] : '';
			$data['city_state_id'] = (!empty($_POST['city_state_id'])) ? $_POST['city_state_id'] : '';
			$data['city_name'] = (!empty($_POST['city_name'])) ? $_POST['city_name'] : '';
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditLocation',$data);
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
 
}

?>