<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

// session_start(); //we need to call PHP's session object to access it through CI
class Api extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('apimodel','',TRUE);
		// Load these helper to create JWT tokens
		$this->load->helper(['core_helper','jwt','authorization_helper']);
		
        //$this->load->helper(['jwt', 'authorization']);
		
		ini_set( 'memory_limit', '25M' );
		ini_set('upload_max_filesize', '25M');  
		ini_set('post_max_size', '25M');  
		ini_set('max_input_time', 3600);  
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		allowCrossOrgin();
	}
	
	function getallheaders_new() {
		return $response_headers = getallheaders_values();
	}
	
	//For generating random string
	private function generateRandomString($length = 8,$charset="") {
		if($charset == 'N'){
			$characters = '0123456789';
		}else{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	private function verify_request($token)
	{
	    // Get all the headers
	    //$headers = $this->input->request_headers();
	    // Extract the token
	    //$token = $headers['Authorization'];
	    // Use try-catch
	    // JWT library throws exception if the token is not valid
	    try {
	        // Validate the token
	        // Successfull validation will return the decoded user data else returns false
	        $data = AUTHORIZATION::validateToken($token);
	        if ($data === false) {
	        	//$this->errorResponse['message'] = 'Unauthorized Access!';
				//$this->responseData($this->errorResponse);
				
				return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!" ), "Data" => NULL ));
				exit;
	        } else {
	            return $data;
	        }
	    } catch (Exception $e) {
	    	
	        // Token is invalid
	        // Send the unathorized access message
	        //$this->errorResponse['message'] = 'Unauthorized Access!';
			//$this->responseData($this->errorResponse);
			
			return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!" ), "Data" => NULL ));
			exit;
	    }
	}
	
	/*function checkToken($token) {
		$check = $this->verify_request($token);
		if($check){
			return $check;
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Unauthorized Access!" ), "Data" => NULL ));
			exit;
		}
	}*/
	
	function homeT(){
		$check = $this->verify_request($_POST['utoken']);
		//$check = $this->checkToken($_POST['utoken']);
		echo "<pre>";print_r($check);
		if(!empty($check->username)){
			//kljkldsjflkdsjlkfj
		}else{
			return $check;
		}
		//echo $check->username;
		exit;
	}
	
	
 
	//For login all users
	function userLogin() {
		
		//echo "here";exit;
		
		if (!empty($_POST) && isset($_POST)) {
		
			if (empty($_POST['username']) ) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter username." ), "Data" => NULL ));
				exit;
			}
			 
			if (!empty($_POST['password'])) {
				$password = md5($_POST['password']);
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter password." ), "Data" => NULL ));
				exit;
			}
			
			//echo "<pre>";print_r($_POST);exit;
				 
			$condition = "i.user_name='".$_POST['username']."' &&  i.employee_password='".$password."' ";
			
			$result_login = $this->apimodel->login_check($condition);
			$result_data = $result_login[0];
			
			
			$utoken = $result_data['employee_id']; 
			$success_msg = "Login Successfull. ";
      
			if (is_array($result_login) && count($result_login) > 0) {
				
				//JWT
				/*$kunci = $this->config->item('jwtkey');
				$token['id'] = $result_data['employee_id'];  //From here
				//$token['username'] = $u;
				$date = new DateTime();
				$token['iat'] = $date->getTimestamp();
				$token['exp'] = $date->getTimestamp() + 60*60*5; //To here is to generate token
				$output['token'] = JWT::encode($token,$kunci ); //This is the output token
				*/
				
				//$token = generateToken(['username' => $result_data['employee_id']]);
				$date = new DateTime();
				$tokenData = array('username' => $result_data['employee_id'], 'iat' => $date->getTimestamp(), 'exp'=> $date->getTimestamp() + 60*60*5 );
				$token = AUTHORIZATION::generateToken($tokenData);
				
				//echo "<pre>";print_r($token);exit;
				
				$result_data['utoken'] = $token;  
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => $success_msg  ), "Data" => $result_data ));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Incorrect Username or Password." ), "Data" => NULL ));
				exit;
			}
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Hearder section empty." ), "Data" => NULL ));
			exit;
		}
	}
	
	//Get get user details
	function getLoginUserDetails(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getLoginUserDetails($_POST['id']);
			$get_user_locations = $this->apimodel->getSortedData("zone, state, city", "user_locations", "user_id = '".$_POST['id']."'");
			$result = array();
			$result['user_data'] = $get_result;
			$result['user_locations'] = $get_user_locations;
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Check duplicate creditor
	function checkDuplicateUser(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$condition = "(email_id='".$_POST['email_id']."' || user_name='".$_POST['user_name']."') ";
			if(!empty($_POST['employee_id'])){
				$condition .=" && employee_id  !='".$_POST['employee_id']."' ";
			}
			$get_result = $this->apimodel->getdata("master_employee", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Add Edit User
	function addEditUser(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['employee_fname'] = $_POST['employee_fname'];
			if(!empty($_POST['employee_mname'])){
				$data['employee_mname'] = $_POST['employee_mname'];
			}
			$data['employee_lname'] = $_POST['employee_lname'];
			if(!empty($_POST['employee_code'])){
				$data['employee_code'] = $_POST['employee_code'];
			}
			
			$full_name = "";
			if(!empty($_POST['employee_fname'])){
				$full_name .= $_POST['employee_fname'];
			}
			if(!empty($_POST['employee_mname'])){
				$full_name .= " ".$_POST['employee_mname'];
			}
			if(!empty($_POST['employee_lname'])){
				$full_name .= " ".$_POST['employee_lname'];
			}
			
			$data['employee_full_name'] = $full_name;
			
			if(!empty($_POST['date_of_joining'])){
				$data['date_of_joining'] = date("Y-m-d", strtotime($_POST['date_of_joining']));
			}
			$data['email_id'] = $_POST['email_id'];
			$data['mobile_number'] = $_POST['mobile_number'];
			if(!empty($_POST['user_name'])){
				$data['user_name'] = $_POST['user_name'];
			}
			if(!empty($_POST['password'])){
				$data['employee_password'] = $_POST['password'];
			}
			if(!empty($_POST['role_id'])){
				$data['role_id'] = $_POST['role_id'];
			}
			if(!empty($_POST['isactive'])){
				$data['isactive'] = $_POST['isactive'];
			}
			
			if(!empty($_POST['employee_id'])){
				$result = $this->apimodel->updateRecord('master_employee', $data, "employee_id='".$_POST['employee_id']."' ");
				
				//location
				if(!empty($_POST['zone'])){
					$this->apimodel->delrecord("user_locations","user_id",$_POST['employee_id']);
					for($i=0;$i<sizeof($_POST['zone']);$i++){
						$loc_data = array();
						$loc_data['user_id']= $_POST['employee_id'];
						$loc_data['zone']= (!empty($_POST['zone'][$i])) ? $_POST['zone'][$i] : '';
						$loc_data['state']= (!empty($_POST['state'][$i])) ? $_POST['state'][$i] : '';
						$loc_data['city']= (!empty($_POST['city'][$i])) ? $_POST['city'][$i] : '';
						
						$rs = $this->apimodel->insertData('user_locations',$loc_data,'1');
					}
				}
				
			}else{
				$result = $this->apimodel->insertData('master_employee', $data, 1);
				//location
				if(!empty($_POST['zone'])){
					for($i=0;$i<sizeof($_POST['zone']);$i++){
						$loc_data = array();
						$loc_data['user_id']= $result;
						$loc_data['zone']= (!empty($_POST['zone'][$i])) ? $_POST['zone'][$i] : '';
						$loc_data['state']= (!empty($_POST['state'][$i])) ? $_POST['state'][$i] : '';
						$loc_data['city']= (!empty($_POST['city'][$i])) ? $_POST['city'][$i] : '';
						
						$rs = $this->apimodel->insertData('user_locations',$loc_data,'1');
					}
				}
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//For creditor listing
	function creditorListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getCreditorList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get creditor form data
	function getCreditorFormData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getCreditorFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Check duplicate creditor
	function checkDuplicateCreditor(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$condition = "creaditor_name='".$_POST['creaditor_name']."' ";
			if(!empty($_POST['creditor_id'])){
				$condition .=" && creditor_id !='".$_POST['creditor_id']."' ";
			}
			$get_result = $this->apimodel->getdata("master_ceditors", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Add Edit creditor
	function addEditCreditor(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			$data = array();
			$data['creaditor_name'] = (!empty($_POST['creaditor_name'])) ? $_POST['creaditor_name'] : '';
			$data['creditor_code'] = (!empty($_POST['creditor_code'])) ? $_POST['creditor_code'] : '';
			$data['ceditor_email'] = (!empty($_POST['ceditor_email'])) ? $_POST['ceditor_email'] : '';
			$data['creditor_mobile'] = (!empty($_POST['creditor_mobile'])) ? $_POST['creditor_mobile'] : '';
			$data['creditor_phone'] = (!empty($_POST['creditor_phone'])) ? $_POST['creditor_phone'] : '';
			$data['creditor_pancard'] = (!empty($_POST['creditor_pancard'])) ? $_POST['creditor_pancard'] : '';
			$data['creditor_gstn'] = (!empty($_POST['creditor_gstn'])) ? $_POST['creditor_gstn'] : '';
			$data['address'] = (!empty($_POST['address'])) ? $_POST['address'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;
			
			if(!empty($_POST['creditor_id'])){
				$result = $this->apimodel->updateRecord('master_ceditors', $data, "creditor_id='".$_POST['creditor_id']."' ");
			}else{
				$result = $this->apimodel->insertData('master_ceditors', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//Delete creditor
	function delCreditor(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_ceditors', $data, "creditor_id='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//For permission listing
	function permissionListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getPermissionList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get permission form data
	function getPermissionFormData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getPermissionFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Check duplicate permission
	function checkDuplicatePermission(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$condition = "perm_desc='".$_POST['perm_desc']."' ";
			if(!empty($_POST['perm_id'])){
				$condition .=" && perm_id !='".$_POST['perm_id']."' ";
			}
			$get_result = $this->apimodel->getdata("permissions", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	
	//Add Edit permission
	function addEditPermission(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			$data = array();
			$data['perm_desc'] = (!empty($_POST['perm_desc'])) ? $_POST['perm_desc'] : '';
			if(empty($_POST['perm_id'])){
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			}else{
				$data['updated_by'] = $_POST['login_user_id'];
			}
			
			if(!empty($_POST['perm_id'])){
				$result = $this->apimodel->updateRecord('permissions', $data, "perm_id='".$_POST['perm_id']."' ");
			}else{
				$result = $this->apimodel->insertData('permissions', $data, 1);
			}
			
			//echo "<pre>";print_r($result);exit;
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	
	//For role listing
	function roleListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getRoleList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}
	
	//Get permissions
	function getPermissionsData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSortedData("perm_id,perm_desc","permissions","","perm_desc","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Get roles form data
	function getRoleFormData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$result = array();
			$get_result = $this->apimodel->getRoleFormData($_POST['id']);
			$get_role_perms = $this->apimodel->getSortedData("perm_id", "role_perm", "role_id = '".$_POST['id']."'");
			$result['role_data'] = $get_result;
			$result['role_perms'] = $get_role_perms;
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	
	//Check duplicate permission
	function checkDuplicateRole(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$condition = "role_name='".$_POST['role_name']."' ";
			if(!empty($_POST['role_id'])){
				$condition .=" && role_id !='".$_POST['role_id']."' ";
			}
			$get_result = $this->apimodel->getdata("roles", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Add Edit roles
	function addEditRoles(){
		//echo "<pre>post";print_r($_POST);
		//for($i=0;$i<sizeof($_POST['role_permissions']);$i++){
			//echo $_POST['role_permissions'][$i];
		//}
		//exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			$data = array();
			$data['role_name'] = (!empty($_POST['role_name'])) ? $_POST['role_name'] : '';
			
			if(!empty($_POST['role_id'])){
				$result = $this->apimodel->updateRecord('roles', $data, "role_id='".$_POST['role_id']."' ");
				if(!empty($result)){
					if(!empty($_POST['role_permissions'])){
						$this->apimodel->delrecord("role_perm","role_id",$_POST['role_id']);
						for($i=0;$i<sizeof($_POST['role_permissions']);$i++){
							$perm_data = array();
							$perm_data['role_id']= $_POST['role_id'];
							$perm_data['perm_id']= $_POST['role_permissions'][$i];
							$rs = $this->apimodel->insertData('role_perm',$perm_data,'1');
						}
					}
				}
			}else{
				$result = $this->apimodel->insertData('roles', $data, 1);
				if(!empty($result)){
					if(!empty($_POST['role_permissions'])){
						for($i=0; $i < sizeof($_POST['role_permissions']);$i++){
							$perm_data = array();
							$perm_data['role_id']= $result;
							$perm_data['perm_id']= $_POST['role_permissions'][$i];
							$rs = $this->apimodel->insertData('role_perm',$perm_data,'1');
						}
					}
				}
			}
			
			//echo "<pre>";print_r($result);exit;
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//For user listing
	function userListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getUserList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get roles
	function getRolesData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSortedData("role_id,role_name","roles","","role_name","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Delete user
	function delUser(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_employee', $data, "employee_id ='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//For location listing
	function locationListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getlocationList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get location form data
	function getLocationFormData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getLocationFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Check duplicate location
	function checkDuplicateLocation(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$condition = "city_name='".$_POST['city_name']."' ";
			if(!empty($_POST['city_id'])){
				$condition .=" && city_id !='".$_POST['city_id']."' ";
			}
			echo $condition;exit;
			$get_result = $this->apimodel->getdata("cities", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	
	//Add Edit location
	function addEditLocation(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			$data = array();
			$data['city_country_id'] = 1;
			$data['city_state_id'] = (!empty($_POST['city_state_id'])) ? $_POST['city_state_id'] : '';
			$data['city_name'] = (!empty($_POST['city_name'])) ? $_POST['city_name'] : '';
			
			if(!empty($_POST['city_id'])){
				$result = $this->apimodel->updateRecord('cities', $data, "city_id='".$_POST['city_id']."' ");
			}else{
				$result = $this->apimodel->insertData('cities', $data, 1);
			}
			
			//echo "<pre>";print_r($result);exit;
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//For branches listing
	function branchListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->branchListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}
	
	//Get creditors
	function getCreditorsData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){	
			$get_result = $this->apimodel->getSortedData("creditor_id,creaditor_name","master_ceditors","isactive='1' ","creaditor_name","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Get locations
	function getLocationsData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSortedData("location_id,location_name","master_location","","location_name","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Get branches form data
	function getBranchesFormData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getBranchesFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Check duplicate branch
	function checkDuplicateBranch(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$condition = "branch_name='".$_POST['branch_name']."' ";
			if(!empty($_POST['branch_id'])){
				$condition .=" && branch_id !='".$_POST['branch_id']."' ";
			}
			$get_result = $this->apimodel->getdata("creditor_branches", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Add Edit branches
	function addEditBranches(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			$data = array();
			$data['branch_name'] = (!empty($_POST['branch_name'])) ? $_POST['branch_name'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : '';
			$data['contact_no'] = (!empty($_POST['contact_no'])) ? $_POST['contact_no'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
			
			if(empty($_POST['branch_id'])){
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			}else{
				$data['updated_by'] = $_POST['login_user_id'];
			}
			
			if(!empty($_POST['branch_id'])){
				$result = $this->apimodel->updateRecord('creditor_branches', $data, "branch_id='".$_POST['branch_id']."' ");
			}else{
				$result = $this->apimodel->insertData('creditor_branches', $data, 1);
			}
			
			//echo "<pre>";print_r($result);exit;
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//Delete branch
	function delBranch(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('creditor_branches', $data, "branch_id='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get login user access
	function getLoginUserAccess(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getLoginUserAccess($_POST['role_id']);
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//For SM and creditor mapping listing
	function smCreditorMappingListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->smCreditorMappingListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}
	
	//Get SM
	function getSMData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSortedData("employee_id,employee_full_name","master_employee","isactive='1' && role_id='3' ","employee_full_name","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Get sm & creditor mapping form data
	function getSMCreditorFormData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSMCreditorFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Add Edit SM Creditor mapping
	function addEditSMCreditorMapping(){
		//echo "<pre>post";print_r($_POST);
		//exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			$result = 1;
			
			if(!empty($_POST['sm_creditor_id'])){
				
			}else{
				if(!empty($_POST['creditor_id'])){
					$this->apimodel->delrecord_condition("sm_creditor_mapping","sm_id='".$_POST['sm_id']."' ");
					for($i=0;$i<sizeof($_POST['creditor_id']);$i++){
						$data = array();
						$data['sm_id']= $_POST['sm_id'];
						$data['creditor_id']= $_POST['creditor_id'][$i];
						$data['updated_by']= $_POST['login_user_id'];
						$rs = $this->apimodel->insertData('sm_creditor_mapping',$data,'1');
					}
				}
			}
			
			//echo "<pre>";print_r($result);exit;
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
	}
	
	//Delete SM Creditor mapping
	function delSMCreditor(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->delrecord('sm_creditor_mapping', 'sm_creditor_id', $_POST['id']);
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get states
	function getStateData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSortedData("state_id,state_name","states","isactive='1' ","state_name","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//For lead listing
	function leadListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->leadListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//Get creditors
	function getRoleWiseCreditorsData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			
			
			$get_result = $this->apimodel->getRoleWiseCreditorsData($_POST['user_id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Get creditors plans
	function getCreditorsPlansData(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){	
			$get_result = $this->apimodel->getSortedData("plan_id,plan_name","master_plan","creditor_id='".$_POST['creditor_id']."' ","plan_name","asc");
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	//Add Lead
	function addLead(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			//echo "<pre>";print_r($_POST);exit;
			$result = "";
			//check customer exist
			$cust_condition = "mobile_no='".$_POST['mobile_number']."' || email_id='".$_POST['email_id']."' ";
			$cust_result = $this->apimodel->getdata("master_customer", "customer_id", $cust_condition);
			//echo "<pre>";print_r($cust_result);exit;
			if(!empty($cust_result)){
				$customer_id = $cust_result[0]['customer_id'];
				//echo $customer_id;exit;
				//check lead already present
				$leadcond = "plan_id='".$_POST['plan_id']."' && customer_id='".$customer_id."' && status='Pending' ";
				$lead_result = $this->apimodel->getdata("lead_details", "lead_id", $leadcond);
				if(!empty($lead_result)){
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Lead already present and proposal on this way.'  ), "Data" => NULL ));
					exit;
				}
				
				//Create Lead
				$lead_data = array();
				$timestamp = time();
				$lead_data['trace_id'] = $customer_id.$timestamp;
				$lead_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$lead_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
				$lead_data['sales_manager_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$lead_data['customer_id'] = $customer_id;
				$lead_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$lead_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
				
				$lead_data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
				$lead_data['portal_id'] = (!empty($_POST['portal_id'])) ? $_POST['portal_id'] : 'Creditor Portal';
				$lead_data['vertical'] = (!empty($_POST['vertical'])) ? $_POST['vertical'] : 'Vertical';
				$lead_data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
				$lead_data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
				$lead_data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : 'N';
				$lead_data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
				
				$lead_data['createdon'] = date("Y-m-d H:i:s");
				$lead_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				$lead_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				$result = $this->apimodel->insertData('lead_details', $lead_data, 1);
				$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");
				
				
				//Add proposal
				$proposal_data = array();
				$proposal_data['trace_id'] = $lead_data['trace_id'];
				$proposal_data['lead_id'] = $result;
				$proposal_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$proposal_data['customer_id'] = $customer_id;
				$proposal_data['status'] = 'Pending';
				
				$proposal_data['created_at'] = date("Y-m-d H:i:s");
				$proposal_data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				$proposal_data['updated_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				$proposal_details_id = $this->apimodel->insertData('proposal_details', $proposal_data, 1);
				$remark = "Proposal added.";
				insert_proposal_log($proposal_details_id,$_POST['login_user_id'],$remark);
				
			}else{
				//add customer and lead
				//exit;
				$cust_data = array();
				$cust_data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
				$cust_data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
				$cust_data['middle_name'] = (!empty($_POST['middle_name'])) ? $_POST['middle_name'] : '';
				$cust_data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
				$full_name = '';
				if(!empty($_POST['first_name'])){
					$full_name .= $_POST['first_name'];
				}
				if(!empty($_POST['middle_name'])){
					$full_name .= " ".$_POST['middle_name'];
				}
				if(!empty($_POST['last_name'])){
					$full_name .= " ".$_POST['last_name'];
				}
				
				$cust_data['full_name'] = $full_name;
				$cust_data['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : '';
				$cust_data['dob'] = (!empty($_POST['dob'])) ? date("Y-m-d", strtotime($_POST['dob'])) : '';
				$cust_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
				$cust_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$cust_data['isactive'] = 1;
				$cust_data['createdon'] = date("Y-m-d H:i:s");
				$cust_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				$cust_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				$customer_id = $this->apimodel->insertData('master_customer', $cust_data, 1);
				
				//Create Lead
				$lead_data = array();
				$timestamp = time();
				$lead_data['trace_id'] = $customer_id.$timestamp;
				$lead_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$lead_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
				$lead_data['sales_manager_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$lead_data['customer_id'] = $customer_id;
				$lead_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$lead_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
				
				$lead_data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
				$lead_data['portal_id'] = (!empty($_POST['portal_id'])) ? $_POST['portal_id'] : 'Creditor Portal';
				$lead_data['vertical'] = (!empty($_POST['vertical'])) ? $_POST['vertical'] : 'Vertical';
				$lead_data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
				$lead_data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
				$lead_data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : 'N';
				$lead_data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
				
				$lead_data['createdon'] = date("Y-m-d H:i:s");
				$lead_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				$lead_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				
				$result = $this->apimodel->insertData('lead_details', $lead_data, 1);
				$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");
				
				//Add proposal
				$proposal_data = array();
				$proposal_data['trace_id'] = $lead_data['trace_id'];
				$proposal_data['lead_id'] = $result;
				$proposal_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$proposal_data['customer_id'] = $customer_id;
				$proposal_data['status'] = 'Pending';
				
				$proposal_data['created_at'] = date("Y-m-d H:i:s");
				$proposal_data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				$proposal_data['updated_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				$proposal_details_id = $this->apimodel->insertData('proposal_details', $proposal_data, 1);
				
				$remark = "Proposal added.";
				insert_proposal_log($proposal_details_id,$_POST['login_user_id'],$remark);
				
			}
			
			
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Lead created successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//For customer proposal listing
	function customerProposalListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->customerProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//For discrepancy proposal listing
	function discrepancyProposalListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->discrepancyProposalListing($_POST);
			//echo "<pre>ddd";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//For bo proposal listing
	function boProposalListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->boProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//reject proposals
	function rejectProposal(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			if(empty($_POST['login_user_id'])){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'  ), "Data" => NULL ));
				exit;
			}
			
			if(empty($_POST['login_user_name'])){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'  ), "Data" => NULL ));
				exit;
			}
			
			
			$data = array();
			$data['status'] = 'Rejected';
			$data['updatedby'] = $_POST['login_user_id'];
			
			$pdata = array();
			$pdata['status'] = 'Rejected';
			$pdata['updated_by'] = $_POST['login_user_id'];
			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='".$_POST['id']."' ");
			if(!empty($result)){
				$this->apimodel->updateRecord('proposal_details', $pdata, "lead_id='".$_POST['id']."' ");
				//get login user details
				$remark = "Proposal Rejected by ".$_POST['login_user_name'];
				insert_proposal_log($_POST['id'],$_POST['login_user_id'],$remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal rejected successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//move to underwriting
	function moveToUW(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			if(empty($_POST['login_user_id'])){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'  ), "Data" => NULL ));
				exit;
			}
			
			if(empty($_POST['login_user_name'])){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'  ), "Data" => NULL ));
				exit;
			}
			
			
			$data = array();
			$data['status'] = 'UW-Approval-Awaiting';
			$data['updatedby'] = $_POST['login_user_id'];
			
			$pdata = array();
			$pdata['status'] = 'UW-Approval-Awaiting';
			$pdata['updated_by'] = $_POST['login_user_id'];
			
			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='".$_POST['id']."' ");
			if(!empty($result)){
				$this->apimodel->updateRecord('proposal_details', $pdata, "lead_id='".$_POST['id']."' ");
				//get login user details
				$remark = "Proposal moved to underwriting by ".$_POST['login_user_name'];
				insert_proposal_log($_POST['id'],$_POST['login_user_id'],$remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal moved to underwriting successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	
	//accept proposals
	function acceptProposal(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			if(empty($_POST['login_user_id'])){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'  ), "Data" => NULL ));
				exit;
			}
			
			if(empty($_POST['login_user_name'])){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'  ), "Data" => NULL ));
				exit;
			}
			
			//Policy genration process
			//QUick Quote
			//get trace_id
			
			$policy_details = $this->apimodel->getdata("proposal_policy","*","lead_id='".$id."' ");
			//get primary customer
			$primary_customer = $this->db->get_where('lead_details',array('lead_id'=>$id))->row()->primary_customer_id;
			//echo "<pre>";print_r($policy_details);exit;
			
			//Pass nominee details
			
			$count = 1;
			$maxcount = count($policy_details);
			$succ = "";
			foreach($policy_details as $proposal){
				$proposal_details = $this->apimodel->getdata("proposal_details","*","proposal_details_id='".$proposal->proposal_details_id."' ");
				//echo $proposal['master_policy_id'];exit;
				$quick_qoute = $this->apimodel->get_quote_data($proposal_details[0]['lead_id'], $primary_customer, $proposal['master_policy_id'], $proposal['proposal_policy_id'], $proposal_details, $proposal['policy_sub_type_id'], $proposal['sum_insured']);
				
				
				if($quick_qoute['status'] == 'error'){
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $quick_qoute['msg']  ), "Data" => NULL ));
					exit;
				}else{
					
					if($maxcount == $count){
						
						$this->get_full_quote_data($policy_details,$proposal_details[0]['lead_id'], $primary_customer,$proposal['master_policy_id'], $proposal['proposal_policy_id'], $proposal_details, $proposal['policy_sub_type_id'], $proposal['sum_insured']);
						exit;
					}
					
					$count++;
				}
			}
			
			//Full Quote
			exit;
			
			
			$data = array();
			$data['status'] = 'Approved';
			$data['updated_by'] = $_POST['login_user_id'];
			$result = $this->apimodel->updateRecord('proposal_details', $data, "lead_id='".$_POST['id']."' ");
			if(!empty($result)){
				//get login user details
				$remark = "Proposal accepted by ".$_POST['login_user_name'];
				insert_proposal_log($_POST['id'],$_POST['login_user_id'],$remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal accepted successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	
	
	
	//For Full Quote
	function get_full_quote_data($policy_details,$lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured){
		
		$count2 = 1;
		$maxcount2 = count($policy_details);
		foreach($policy_details as $proposal){
			$full_qoute = $this->apimodel->get_full_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $nominees, $proposal_details, $policy_sub_type_id, $sum_insured);
			//echo "<pre>";print_r($full_qoute);exit;
		}
		
		if($full_qoute['status'] == 'error'){
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $full_qoute['msg']  ), "Data" => NULL ));
			exit;
		}else{
			
			if($maxcount2 == $count2){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $full_qoute['msg']  ), "Data" => NULL ));
				exit;
			}
			$count2++;
			
		}
		
		
	}
	
	
	//dummy full code function
	function dummyFullQuote(){
		
		$member = array(0 => array(
			"MemberNo" => "1",
			"Salutation" => "Mr",
			"First_Name" => "Danish Akhtar",
			"Middle_Name" => null,
			"Last_Name" => "Shaikh",
			"Gender" => "M",
			"DateOfBirth" => "04/11/1986",
			"Relation_Code" => "R001",
			"Marital_Status" => null,
			"height" => "0.00",
			"weight" => "0",
			"occupation" => "O553",
			"PrimaryMember" => "Y",
			"MemberproductComponents" => array(
					0 => array
						(
							"PlanCode" => "4211",
							"MemberQuestionDetails" => array
								(
									0 => array
										(
											"QuestionCode" => null,
											"Answer" => null,
											"Remarks" => null
										)

								)

						)

			),
			"MemberPED" => array(
					"PEDCode" => null,
					"Remarks" => null,
				),
			"exactDiagnosis" => null,
			"dateOfDiagnosis" => null,
			"lastDateConsultation" => null,
			"detailsOfTreatmentGiven" => null,
			"doctorName" => null,
			"hospitalName" => null,
			"phoneNumberHosital" => null,
			"Nominee_First_Name" => "s",
			"Nominee_Last_Name" => "d",
			"Nominee_Contact_Number" => null,
			"Nominee_Home_Address" => null,
			"Nominee_Relationship_Code" => "R001"
		)


		);
		
		//echo "<pre>";print_r($member);exit;
		
		
		$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => "1000", "salutation" => "Mr","firstName" => "Danish", "middleName" => "", "lastName" => "Ak","dateofBirth" => date('m/d/Y', strtotime("1986-04-11")) , "gender" => "M","educationalQualification" => null, "pinCode" => "425001", "uidNo" => null,"maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => "infodanish@gmail.com","contactMobileNo" => "8149212749", "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null,"annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null,"ageProof" => null, "others" => null, "homeAddressLine1" => "kalyan", "homeAddressLine2" => null, "homeAddressLine3" => null,"homePinCode" => "425001", "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null,"homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null,"mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null,"mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null,"ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null,"EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null],"PolicyCreationRequest" => ["Quotation_Number" => "IPB100130770", "MasterPolicyNumber" => "61-20-00040-00-00","GroupID" => "GRP001", "Product_Code" => "4211","SumInsured_Type"=> null,"Policy_Tanure"=> "1","Member_Type_Code"=> "M209", "intermediaryCode" => "2108233","AutoRenewal" => 'Y', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null,"Customer_Signature_Date" => null,"businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => "1", "Source_Name" => "abc", "SPID" => "0", "TCN" => null, "CRTNO" => null, "RefCode1" => "0","RefCode2" => "0", "Employee_Number" => "1000","enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => 1, "PaymentMode" => "online","PolicyproductComponents" => [["PlanCode" => "4211", "SumInsured" => "300000", "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member],"ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null,"paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "457","collectionRcvdDate" => "2020-10-20","collectionMode" => "online", "remarks" => null, "instrumentNumber" => "pay_FrAaDQjzQFtQWG","instrumentDate" => "2020-10-20", "bankName" => null, "branchName" => null,"bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => null, "PaymentGatewayName" => "ABC_GFB", "TerminalID" => "EuxJCz8cZV9V63","CardNo" => null]];
		
		$req_json = json_encode($fqrequest);
		
		//echo $req_json;exit;
		
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://bizpre.adityabirlahealth.com/ABHICL_NB/Service1.svc/GHI",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($fqrequest)) ,
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			) ,
		));

		$response = curl_exec($curl);
		
		
		$err = curl_error($curl);
		echo "<pre>";print_r($response);
		echo $err;
		exit;
		
	}
	
	
	//Add Discrepancy
	function addDiscrepancy(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['created_by'] = $_POST['login_user_id'];
			$data['lead_id'] = $_POST['lead_id'];
			$data['discrepancy_type'] = $_POST['discrepancy_type'];
			$data['discrepancy_subtype'] = $_POST['discrepancy_subtype'];
			$data['remark'] = $_POST['remark'];
			
			$result = $this->apimodel->insertData('proposal_discrepancies', $data, 1);
			
			if(!empty($result)){
				$remark = "Discrepancy Added with remark: ".$_POST['remark'];
				insert_proposal_log($result,$_POST['login_user_id'],$remark);
				
				$ldata = array();
				$ldata['status'] = 'Discrepancy';
				$ldata['updatedby'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('lead_details', $ldata, "lead_id='".$_POST['lead_id']."' ");
				
				$udata = array();
				$udata['status'] = 'Discrepancy';
				$udata['updated_by'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('proposal_details', $udata, "lead_id='".$_POST['lead_id']."' ");
				
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Discrepancy added successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//For CO proposal listing
	function coProposalListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->coProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//For uw proposal listing
	function uwProposalListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->uwProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//For assignment declaration listing
	function assignmentDeclarationListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->assignmentDeclarationListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//Add Edit Assignment Declaration
	function addEditAssignmentDeclaration(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['created_at'] = date("Y-m-d H:i:s");
			
			if(!empty($_POST['assignment_declaration_id'])){
				$result = $this->apimodel->updateRecord('assignment_declaration', $data, "assignment_declaration_id='".$_POST['assignment_declaration_id']."' ");
			}else{
				$result = $this->apimodel->insertData('assignment_declaration', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	
	//Get get assignment declaration
	function getAssignmentDeclarationDetails(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getAssignmentDeclarationDetails($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Delete assignment declaration
	function delAssignmentDeclaration(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['is_active'] = 0;
			$result = $this->apimodel->updateRecord('assignment_declaration', $data, "assignment_declaration_id ='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//For ghd declaration listing
	function ghdDeclarationListing(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->ghdDeclarationListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}	
	}
	
	//Get get GHD declaration
	function getGHDDeclarationDetails(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getGHDDeclarationDetails($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Add Edit GHD Declaration
	function addEditGHDDeclaration(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['created_at'] = date("Y-m-d H:i:s");
			
			if(!empty($_POST['declaration_id'])){
				$result = $this->apimodel->updateRecord('ghd_declaration', $data, "declaration_id='".$_POST['declaration_id']."' ");
			}else{
				$result = $this->apimodel->insertData('ghd_declaration', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
	}
	
	//Delete GHD declaration
	function delGHDDeclaration(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['is_active'] = 0;
			$result = $this->apimodel->updateRecord('ghd_declaration', $data, "declaration_id ='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//multi images uploader
	function getImages(){
		
		//echo "<pre>";print_r($_FILES);exit;
		
	}
	
	//Test Api
	function testApi(){
		$send_message = send_message("8149212749", $mail_to="", $mail_cc="", $mail_bcc="",$data=array(), "sendOTP");
		
		echo "<pre>";print_r($send_message);exit;
	}

	
}

?>