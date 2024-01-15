<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Myprofile extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		//$this->load->model('myprofile_model','',TRUE);
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('myprofile/addEdit');
		$this->load->view('template/footer.php');
	}
 
	function addEdit($id=NULL)
	{
		//echo $user_id;
		$result = array();
		//echo "<pre>";print_r($_SESSION["webpanel"]);exit;
		//$result['user_details'] = $this->myprofile_model->getFormdata($_SESSION["webpanel"]['employee_id']);
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['id'] = $_SESSION["webpanel"]['employee_id'];
		$getLoginUserDetails = curlFunction(SERVICE_URL.'/api/getLoginUserDetails',$data);
		$getLoginUserDetails = json_decode($getLoginUserDetails, true);
		//echo "<pre>ddd";print_r($getLoginUserDetails);exit;
		$result['user_details'] = $getLoginUserDetails['Data']['user_data'][0];
		
		$this->load->view('template/header.php');
		$this->load->view('myprofile/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		// print_r($_POST);
		// exit;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			//check duplicate record.
			$checkdata = array();
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			$checkdata['email_id'] = $_POST['email_id'];
			$checkdata['user_name'] = $_POST['user_name'];
			$checkdata['employee_id'] = $_SESSION["webpanel"]['employee_id'];
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateUser',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'User Email/Username Already Present!'));
				exit;
			}
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['employee_id'] = $_SESSION["webpanel"]['employee_id'];
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
}

?>
