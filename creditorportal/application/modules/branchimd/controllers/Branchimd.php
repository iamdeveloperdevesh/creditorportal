<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Branchimd extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION["webpanel"]['employee_id'];
		$branchImdData = curlFunction(SERVICE_URL.'/api2/getBranchImdData',$data);
		$branchImdData = json_decode($branchImdData, true);
		if(!is_null($branchImdData)){
            $result['data'] = $branchImdData['data'];
        }else{
            $result['data'] = '';
        }

		
		$this->load->view('template/header.php');
		$this->load->view('branchimd/index', $result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$userListing = curlFunction(SERVICE_URL.'/api2/branchImdListing',$_GET);
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
				$temp = array();
				array_push($temp, $userListing['Data']['query_result'][$i]['policy_number'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['branch_code'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['imd_code'] );

				if($userListing['Data']['query_result'][$i]['status'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				if(in_array('BranchIMDEdit',$this->RolePermission)){
					$actionCol .='<a href="branchimd/edit/'.encrypt_decrypt_password($userListing['Data']['query_result'][$i]['branch_imd_map_id']).'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';
				}
				if(in_array('BranchIMDDelete',$this->RolePermission)){
					if($userListing['Data']['query_result'][$i]['status'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.encrypt_decrypt_password($userListing['Data']['query_result'][$i]['branch_imd_map_id']).'\');" title="Delete"><span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
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

	function edit($id){

		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION["webpanel"]['employee_id'];
		$result = json_decode(curlFunction(SERVICE_URL.'/api2/getbranchimddata',$data), true);

		$this->load->view('template/header.php');
		$this->load->view('branchimd/edit-branch-imd', $result);
		$this->load->view('template/footer.php');
	}

	function addbranchimd(){

		$this->load->view('template/header.php');
		$this->load->view('branchimd/add-branch-imd');
		$this->load->view('template/footer.php');
	}

	function addsinglebranchimd(){

		$data = $_POST;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION["webpanel"]['employee_id'];
		echo curlFunction(SERVICE_URL.'/api2/addsinglebranchimd',$data);
	}

	function addbulkbranchimd(){

		$data = $_POST;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION["webpanel"]['employee_id'];

		$tmpfile = $_FILES['import_branch_imd_codes']['tmp_name'];
		$filename = basename($_FILES['import_branch_imd_codes']['name']);
		$data['import_branch_imd_codes'] = curl_file_create($tmpfile, $_FILES['import_branch_imd_codes']['type'], $filename);
		$response = curlFileFunction(SERVICE_URL.'/api2/addbulkbranchimd', $data);

		$response = json_decode($response, true);

		echo json_encode($response);
	}

	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['user_id'] = $_SESSION["webpanel"]['employee_id'];
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api2/delRecord',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}

	function updatebranchimd(){

		$data = $_POST;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION["webpanel"]['employee_id'];
		
		echo curlFileFunction(SERVICE_URL.'/api2/updatebranchimd', $data);
	}
}
?>