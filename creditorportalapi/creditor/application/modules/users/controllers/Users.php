<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Users extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		//$this->load->model('usersmodel','',TRUE);
		//checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$result = array();
		//$result['roles'] = $this->usersmodel->getData("role_id, role_name", "roles", "", "role_name", "asc");
		//print_r($result);exit;
		
		//Get all roles
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getRoles = curlFunction(SERVICE_URL.'/api/getRolesData',$data);
		$getRoles = json_decode($getRoles, true);
		//echo "<pre>";print_r($getRoles);exit;
		$result['roles'] = $getRoles['Data'];
		
		$this->load->view('template/header.php');
		$this->load->view('users/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$userListing = curlFunction(SERVICE_URL.'/api/userListing',$_GET);
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
				array_push($temp, $userListing['Data']['query_result'][$i]['user_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_fname'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_lname'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_code'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['mobile_number'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['role_name'] );
				if($userListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				if(in_array('UserEdit',$this->RolePermission)){
					$actionCol .='<a href="users/addEdit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['employee_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				}
				if(in_array('UserDelete',$this->RolePermission)){
					if($userListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$userListing['Data']['query_result'][$i]['employee_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
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
		//print_r($_GET);
		$user_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$user_id = $url_prams['id'];
		}
		
		//echo $user_id;
		
		//Get all roles
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getRoles = curlFunction(SERVICE_URL.'/api/getRolesData',$data);
		$getRoles = json_decode($getRoles, true);
		//echo "<pre>";print_r($getRoles);exit;
		$result['roles'] = $getRoles['Data'];
		
		//Get user details
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['id'] = $user_id;
		$getLoginUserDetails = curlFunction(SERVICE_URL.'/api/getLoginUserDetails',$data);
		$getLoginUserDetails = json_decode($getLoginUserDetails, true);
		//echo "<pre>ddd";print_r($getLoginUserDetails);exit;
		$result['user_details'] = $getLoginUserDetails['Data']['user_data'][0];
		$result['user_locations'] = $getLoginUserDetails['Data']['user_locations'];
		
		$this->load->view('template/header.php');
		$this->load->view('users/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		//echo "<pre>";print_r($_POST);exit;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			//check duplicate record.
			$checkdata = array();
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			$checkdata['email_id'] = $_POST['email_id'];
			$checkdata['user_name'] = $_POST['user_name'];
			$checkdata['employee_id'] = (!empty($_POST['employee_id'])) ? $_POST['employee_id'] : '';
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateUser',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'User Email/Username Already Present!'));
				exit;
			}
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			if(!empty($_POST['employee_id'])){
				$data['employee_id'] = $_POST['employee_id'];
			}else{
				$data['user_name'] = $_POST['user_name'];
			}
			
			$data['role_id'] = $_POST['role_id'];
			
			$data['employee_fname'] = $_POST['employee_fname'];
			
			if(!empty($_POST['employee_mname'])){
				$data['employee_mname'] = $_POST['employee_mname'];
			}
			$data['employee_lname'] = $_POST['employee_lname'];
			if(!empty($_POST['employee_code'])){
				$data['employee_code'] = $_POST['employee_code'];
			}
			if(!empty($_POST['date_of_joining'])){
				$data['date_of_joining'] = date("Y-m-d", strtotime($_POST['date_of_joining']));
			}
			$data['email_id'] = $_POST['email_id'];
			$data['mobile_number'] = $_POST['mobile_number'];
			if(!empty($_POST['password'])){
				$data['password'] = md5($_POST['password']);
			}
			
			$data['isactive'] = $_POST['isactive'];
			
			$data['zone'] = (!empty($_POST['zone'])) ? $_POST['zone'] : '';
			$data['state'] = (!empty($_POST['state'])) ? $_POST['state'] : '';
			$data['city'] = (!empty($_POST['city'])) ? $_POST['city'] : '';
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditUser',$data);
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
			echo json_encode(array('success' => false, 'msg'=>'Problem while updating record.'));
			exit;
		}
	}
 
	//For Delete
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delUser',$data);
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