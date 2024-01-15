<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

// session_start(); //we need to call PHP's session object to access it through CI
class Api extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('apimodel', '', TRUE);
$this->load->model('Api2/models/apimodel','apimodel2');

		// Load these helper to create JWT tokens
		$this->load->helper(['core_helper', 'jwt', 'authorization_helper']);

		//$this->load->helper(['jwt', 'authorization']);

		ini_set('memory_limit', '25M');
		ini_set('upload_max_filesize', '25M');
		ini_set('post_max_size', '25M');
		ini_set('max_input_time', 3600);
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		allowCrossOrgin();
	}

	function getallheaders_new()
	{
		return $response_headers = getallheaders_values();
	}

	//For generating random string
	private function generateRandomString($length = 8, $charset = "")
	{
		if ($charset == 'N') {
			$characters = '0123456789';
		} else {
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
				return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
				exit;
			} else {
				return $data;
			}
		} catch (Exception $e) {

			// Token is invalid
			// Send the unathorized access message
			
			return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
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

	function homeT()
	{
		$check = $this->verify_request($_POST['utoken']);
		//$check = $this->checkToken($_POST['utoken']);
		echo "<pre>";
		print_r($check);
		if (!empty($check->username)) {
			//kljkldsjflkdsjlkfj
		} else {
			return $check;
		}
		//echo $check->username;
		exit;
	}

	//For login all users
	function userLogin()
	{
		//echo 1;exit;
		//echo "<pre>";print_r($_POST);exit;
		if (!empty($_POST) && isset($_POST)) {

			if (empty($_POST['username'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter username."), "Data" => NULL));
				exit;
			}

			if (!empty($_POST['password'])) {
				$password = encrypt_decrypt_password($_POST['password'],"E");
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter password."), "Data" => NULL));
				exit;
			}

			//echo "<pre>";print_r($_POST);exit;

			$condition = "i.user_name='" . $_POST['username'] . "' &&  i.employee_password='" . $password . "' ";

			$result_login = $this->apimodel->login_check($condition);
			$result_data = $result_login[0];


			$utoken = $result_data['employee_id'];
			$creditor_id = $result_data['creditor_id'];
			$creditor_logo='';
			if(!empty($creditor_id)){
                $creditor_logo=$this->db->query("select creditor_logo from master_ceditors where creditor_id=".$creditor_id)->row()->creditor_logo;
            }

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
				$tokenData = array('username' => $result_data['employee_id'], 'iat' => $date->getTimestamp(), 'exp' => $date->getTimestamp() + 60 * 60 * 5);
				$token = AUTHORIZATION::generateToken($tokenData);

				//echo "<pre>";print_r($token);exit;

				$result_data['utoken'] = $token;
				$result_data['creditor_logo'] = $creditor_logo;

				//inserting last login
				$last_login = array();
				$last_login['last_login'] = date("Y-m-d H:i:s");
				$this->apimodel->updateRecord('master_employee', $last_login, "employee_id='" . $result_data['employee_id'] . "' ");

				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => $success_msg), "Data" => $result_data));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Incorrect Username or Password."), "Data" => NULL));
				exit;
			}
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Hearder section empty."), "Data" => NULL));
			exit;
		}
	}

	//Get get user details
	function getLoginUserDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLoginUserDetails($_POST['id']);
			$get_user_locations = $this->apimodel->getSortedData("location_id", "user_locations", "user_id = '" . $_POST['id'] . "'");
			$result = array();
			$result['user_data'] = $get_result;
			$result['user_locations'] = $get_user_locations;
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate creditor
	function checkDuplicateUser()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "(email_id='" . $_POST['email_id'] . "' || user_name='" . $_POST['user_name'] . "') ";
			if (!empty($_POST['employee_id'])) {
				$condition .= " && employee_id  !='" . $_POST['employee_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("master_employee", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit User
	function addEditUser()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['employee_fname'] = $_POST['employee_fname'];
			if (!empty($_POST['employee_mname'])) {
				$data['employee_mname'] = $_POST['employee_mname'];
			} else {
				$data['employee_mname'] = "";
			}

			$data['employee_lname'] = $_POST['employee_lname'];
			if (!empty($_POST['employee_code'])) {
				$data['employee_code'] = $_POST['employee_code'];
			}

			$full_name = "";
			if (!empty($_POST['employee_fname'])) {
				$full_name .= $_POST['employee_fname'];
			}
			if (!empty($_POST['employee_mname'])) {
				$full_name .= " " . $_POST['employee_mname'];
			}
			if (!empty($_POST['employee_lname'])) {
				$full_name .= " " . $_POST['employee_lname'];
			}

			$data['employee_full_name'] = $full_name;

			if (!empty($_POST['date_of_joining'])) {
				$data['date_of_joining'] = date("Y-m-d", strtotime($_POST['date_of_joining']));
			}
			$data['email_id'] = $_POST['email_id'];
			$data['mobile_number'] = $_POST['mobile_number'];
			if (!empty($_POST['user_name'])) {
				$data['user_name'] = $_POST['user_name'];
			}
			if (!empty($_POST['password'])) {
				$data['employee_password'] = $_POST['password'];
			}
			if (!empty($_POST['role_id'])) {
				$data['role_id'] = $_POST['role_id'];
			}
			if (!empty($_POST['company_id'])) {
				$data['company_id'] = $_POST['company_id'];
			}
			if (!empty($_POST['isactive'])) {
				$data['isactive'] = $_POST['isactive'];
			}
            if (!empty($_POST['logo'])) {
                $data['logo'] = $_POST['logo'];
            }

			if (!empty($_POST['employee_id'])) {
				$result = $this->apimodel->updateRecord('master_employee', $data, "employee_id='" . $_POST['employee_id'] . "' ");

				//location
				if (!empty($_POST['location_id'])) {
					$this->apimodel->delrecord("user_locations", "user_id", $_POST['employee_id']);
					for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['user_id'] = $_POST['employee_id'];
						$loc_data['location_id'] = (!empty($_POST['location_id'][$i])) ? $_POST['location_id'][$i] : '';

						$rs = $this->apimodel->insertData('user_locations', $loc_data, '1');
					}
				}

				if(!empty($_POST['creditor_id'])){

					$this->apimodel->delrecord("sm_creditor_mapping", "sm_id", $_POST['employee_id']);
					//for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['sm_id'] = $_POST['employee_id'];
						$loc_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';

						$rs = $this->apimodel->insertData('sm_creditor_mapping', $loc_data, '1');
					//}
				}
			} else {
				$result = $this->apimodel->insertData('master_employee', $data, 1);
				//location
				if (!empty($_POST['location_id'])) {
					for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['user_id'] = $result;
						$loc_data['location_id'] = (!empty($_POST['location_id'][$i])) ? $_POST['location_id'][$i] : '';

						$rs = $this->apimodel->insertData('user_locations', $loc_data, '1');
					}
				}
				if(!empty($_POST['creditor_id'])){

					//for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['sm_id'] = $result;
						$loc_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';

						$rs = $this->apimodel->insertData('sm_creditor_mapping', $loc_data, '1');
					//}
				}
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For creditor listing
	function creditorListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCreditorList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get creditor form data
	function getCreditorFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCreditorFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate creditor
	function checkDuplicateCreditor()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "creaditor_name='" . $_POST['creaditor_name'] . "' ";
			if (!empty($_POST['creditor_id'])) {
				$condition .= " && creditor_id !='" . $_POST['creditor_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("master_ceditors", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit creditor
	function addEditCreditor()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['creaditor_name'] = (!empty($_POST['creaditor_name'])) ? $_POST['creaditor_name'] : '';
			$data['creditor_code'] = (!empty($_POST['creditor_code'])) ? $_POST['creditor_code'] : '';
			$data['ceditor_email'] = (!empty($_POST['ceditor_email'])) ? $_POST['ceditor_email'] : '';
			$data['creditor_mobile'] = (!empty($_POST['creditor_mobile'])) ? $_POST['creditor_mobile'] : '';
			$data['creditor_phone'] = (!empty($_POST['creditor_phone'])) ? $_POST['creditor_phone'] : '';
			$data['creditor_pancard'] = (!empty($_POST['creditor_pancard'])) ? $_POST['creditor_pancard'] : '';
			$data['creditor_gstn'] = (!empty($_POST['creditor_gstn'])) ? $_POST['creditor_gstn'] : '';
			if(isset($_POST['creditor_logo'])){
				$data['creditor_logo'] = (!empty($_POST['creditor_logo'])) ? $_POST['creditor_logo'] : '';
			}
            $data['initial_cd'] = (!empty($_POST['cd_balance'])) ? $_POST['cd_balance'] : '';
            $data['cd_threshold'] = (!empty($_POST['threshold'])) ? $_POST['threshold'] : '';
            $data['threshold_value'] = (!empty($_POST['threshold_value'])) ? $_POST['threshold_value'] : '';
			$data['address'] = (!empty($_POST['address'])) ? $_POST['address'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;
            $data['short_code'] = (!empty($_POST['short_code'])) ? $_POST['short_code'] : 0;
            $data['term_condition'] = (!empty($_POST['tc_text'])) ? $_POST['tc_text'] : '';
            $data['allow_negative_issuance'] = (!empty($_POST['negative_issuance'])) ? $_POST['negative_issuance'] : 0;
			if (!empty($_POST['creditor_id'])) {
				$result = $this->apimodel->updateRecord('master_ceditors', $data, "creditor_id='" . $_POST['creditor_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_ceditors', $data, 1);
                $cd_data = array(
                    'type' => 1,
                    'amount' => $_POST['cd_balance'],
                    'lead_id' => ' ',
                    'creditor_id' => $this->db->insert_id(),
                    'type_trans' =>"Initial",
                );
                $result=$this->db->insert('master_cd_credit_debit_transaction',$cd_data);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete creditor
	function delCreditor()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_ceditors', $data, "creditor_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For permission listing
	function permissionListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPermissionList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get permission form data
	function getPermissionFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPermissionFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate permission
	function checkDuplicatePermission()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "perm_desc='" . $_POST['perm_desc'] . "' ";
			if (!empty($_POST['perm_id'])) {
				$condition .= " && perm_id !='" . $_POST['perm_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("permissions", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//Add Edit permission
	function addEditPermission()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['perm_desc'] = (!empty($_POST['perm_desc'])) ? $_POST['perm_desc'] : '';
			if (empty($_POST['perm_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['perm_id'])) {
				$result = $this->apimodel->updateRecord('permissions', $data, "perm_id='" . $_POST['perm_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('permissions', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//For role listing
	function roleListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getRoleList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get permissions
	function getPermissionsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("perm_id,perm_desc", "permissions", "", "perm_desc", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get roles form data
	function getRoleFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$result = array();
			$get_result = $this->apimodel->getRoleFormData($_POST['id']);
			$get_role_perms = $this->apimodel->getSortedData("perm_id", "role_perm", "role_id = '" . $_POST['id'] . "'");
			$result['role_data'] = $get_result;
			$result['role_perms'] = $get_role_perms;
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//Check duplicate permission
	function checkDuplicateRole()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "role_name='" . $_POST['role_name'] . "' ";
			if (!empty($_POST['role_id'])) {
				$condition .= " && role_id !='" . $_POST['role_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("roles", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit roles
	function addEditRoles()
	{
		//echo "<pre>post";print_r($_POST);
		//for($i=0;$i<sizeof($_POST['role_permissions']);$i++){
		//echo $_POST['role_permissions'][$i];
		//}
		//exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['role_name'] = (!empty($_POST['role_name'])) ? $_POST['role_name'] : '';

			if (!empty($_POST['role_id'])) {
				$result = $this->apimodel->updateRecord('roles', $data, "role_id='" . $_POST['role_id'] . "' ");
				if (!empty($result)) {
					if (!empty($_POST['role_permissions'])) {
						$this->apimodel->delrecord("role_perm", "role_id", $_POST['role_id']);
						for ($i = 0; $i < sizeof($_POST['role_permissions']); $i++) {
							$perm_data = array();
							$perm_data['role_id'] = $_POST['role_id'];
							$perm_data['perm_id'] = $_POST['role_permissions'][$i];
							$rs = $this->apimodel->insertData('role_perm', $perm_data, '1');
						}
					}
				}
			} else {
				$result = $this->apimodel->insertData('roles', $data, 1);
				if (!empty($result)) {
					if (!empty($_POST['role_permissions'])) {
						for ($i = 0; $i < sizeof($_POST['role_permissions']); $i++) {
							$perm_data = array();
							$perm_data['role_id'] = $result;
							$perm_data['perm_id'] = $_POST['role_permissions'][$i];
							$rs = $this->apimodel->insertData('role_perm', $perm_data, '1');
						}
					}
				}
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For user listing
	function userListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getUserList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get roles
	function getRolesData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("role_id,role_name", "roles", "", "role_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete user
	function delUser()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_employee', $data, "employee_id ='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For location listing
	function locationListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getlocationList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get location form data
	function getLocationFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLocationFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate location
	function checkDuplicateLocation()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "location_name='" . $_POST['location_name'] . "' ";
			if (!empty($_POST['location_id'])) {
				$condition .= " && location_id !='" . $_POST['location_id'] . "' ";
			}
			//echo $condition;
			//exit;
			$get_result = $this->apimodel->getdata("master_location", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//Add Edit location
	function addEditLocation()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : '';
			$data['location_name'] = (!empty($_POST['location_name'])) ? $_POST['location_name'] : '';

			if (!empty($_POST['location_id'])) {
				$result = $this->apimodel->updateRecord('master_location', $data, "location_id='" . $_POST['location_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_location', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For branches listing
	function branchListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->branchListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get creditors
	function getCreditorsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if(!empty($_POST['cid'])){
				$condition = "isactive='1' && creditor_id='".$_POST['cid']."' ";
			}else{
				$condition = "isactive='1' ";
			}
			$get_result = $this->apimodel->getSortedData("creditor_id,creaditor_name", "master_ceditors", $condition, "creaditor_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get locations
	function getLocationsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("location_id,location_name", "master_location", "", "location_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get branches form data
	function getBranchesFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getBranchesFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate branch
	function checkDuplicateBranch()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "branch_name='" . $_POST['branch_name'] . "' ";
			if (!empty($_POST['branch_id'])) {
				$condition .= " && branch_id !='" . $_POST['branch_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("creditor_branches", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit branches
	function addEditBranches()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['branch_name'] = (!empty($_POST['branch_name'])) ? $_POST['branch_name'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : '';
			$data['contact_no'] = (!empty($_POST['contact_no'])) ? $_POST['contact_no'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';

			if (empty($_POST['branch_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['branch_id'])) {
				$result = $this->apimodel->updateRecord('creditor_branches', $data, "branch_id='" . $_POST['branch_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('creditor_branches', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete branch
	function delBranch()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('creditor_branches', $data, "branch_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get login user access
	function getLoginUserAccess()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLoginUserAccess($_POST['role_id']);
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For SM and creditor mapping listing
	function smCreditorMappingListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->smCreditorMappingListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get SM
	function getSMData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if(!empty($_POST['sm_id'])){
				$condition = "employee_id='".$_POST['sm_id']."' ";
			}else{
				$condition = "isactive='1' && role_id='3'";
			}
			$get_result = $this->apimodel->getSortedData("employee_id,employee_full_name", "master_employee", $condition, "employee_full_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

    function cdDashBoradDetails()
    {

        $checkToken = $this->verify_request($_POST['utoken']);
        if (!empty($checkToken->username)) {
            // echo 'pooja';exit;
            // var_dump($_POST);exit;
            $get_result = $this->apimodel->cdDashBoradDetailsNew($_POST);
           // echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
            exit;
        } else {
            echo $checkToken;
        }
    }

    function coverDashBoradDetails()
    {

        $checkToken = $this->verify_request($_POST['utoken']);
        if (!empty($checkToken->username)) {
            // echo 'pooja';exit;
            // var_dump($_POST);exit;
            $get_result = $this->apimodel->coverDashBoradDetails($_POST);
            // echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
            exit;
        } else {
            echo $checkToken;
        }
    }
    function addDeposit(){
        $data=array(
            "partner_id"=>$_POST['cred_id'],
            "trans_date"=>$_POST['dep_date'],
            "amount"=>$_POST['amount'],
            "trans_no"=>$_POST['trans_no'],
            "bank_name"=>$_POST['bankName'],
            "branch"=>$_POST['branch'],
            "payment_mode"=>$_POST['payment_mode'],
        );
        $get_result = $this->apimodel->insertDeposit($data);
        $cd_data = array(
            'type' => 1,
            'amount' => $_POST['amount'],
            'lead_id' => ' ',
            'creditor_id' => $_POST['cred_id'],
            'type_trans' =>"Deposited",
        );
        $result=$this->db->insert('master_cd_credit_debit_transaction',$cd_data);
       // var_dump($get_result);die;
        if($get_result){
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Added Successfully!')));
            exit;
        }else{
            echo json_encode(array("status_code" => "201", "Metadata" => array("Message" => 'Something Went Wrong!')));
            exit;
        }
    }


    function addCover(){
        $data=array(
            "creditor_id"=>$_POST['cred_id'],
            "plan_id"=>$_POST['plan_cover_id'],
            "trans_date"=>$_POST['dep_date'],
            "amount"=>$_POST['amount'],
            "policy_id"=>$_POST['policy_cover_id'],

        );
        $get_result = $this->apimodel->insertDepositCover($data);
        $cd_data = array(
            'type' => 1,
            'amount' => $_POST['amount'],
            'lead_id' => ' ',
            "creditor_id"=>$_POST['cred_id'],
            "plan_id"=>$_POST['plan_cover_id'],
            'type_trans' =>"Deposited",
            "policy_id"=>$_POST['policy_cover_id'],
        );
        $result=$this->db->insert('master_cover_credit_debit_transaction',$cd_data);
        // var_dump($get_result);die;
        if($get_result){
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Added Successfully!')));
            exit;
        }else{
            echo json_encode(array("status_code" => "201", "Metadata" => array("Message" => 'Something Went Wrong!')));
            exit;
        }
    }
    function cdDashBoradDetailsTrans()
    {

        $checkToken = $this->verify_request($_POST['utoken']);
        //echo 'pooja';exit;
        //var_dump($_POST);exit;
        if (!empty($checkToken->username)) {
            //   var_dump($_POST);exit;
            $get_result = $this->apimodel->cdDashBoradDetails($_POST);

            	//echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
            exit;
        } else {
            echo $checkToken;
        }
    }
            //Get sm & creditor mapping form data
	function getSMCreditorFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSMCreditorFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get sm & creditor mapping by user id
	function getSMCreditorMappingByUserId()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSMCreditorMappingByUserId($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit SM Creditor mapping
	function addEditSMCreditorMapping()
	{
		//echo "<pre>post";print_r($_POST);
		//exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$result = 1;

			if (!empty($_POST['sm_creditor_id'])) {
			} else {
				/*if(!empty($_POST['creditor_id'])){
					$this->apimodel->delrecord_condition("sm_creditor_mapping","sm_id='".$_POST['sm_id']."' ");
					for($i=0;$i<sizeof($_POST['creditor_id']);$i++){
						$data = array();
						$data['sm_id']= $_POST['sm_id'];
						$data['creditor_id']= $_POST['creditor_id'][$i];
						$data['updated_by']= $_POST['login_user_id'];
						$rs = $this->apimodel->insertData('sm_creditor_mapping',$data,'1');
					}
				}*/

				if (!empty($_POST['sm_id'])) {
					//$this->apimodel->delrecord_condition("sm_creditor_mapping","creditor_id='".$_POST['creditor_id']."' ");
					for ($i = 0; $i < sizeof($_POST['sm_id']); $i++) {
						$chk_mapping = $this->apimodel->getSortedData("sm_id,creditor_id", "sm_creditor_mapping", "sm_id='" . $_POST['sm_id'][$i] . "' && creditor_id='" . $_POST['creditor_id'] . "' ", "sm_id", "asc");
						//echo "<pre>";print_r($get_result);exit;

						if (empty($chk_mapping)) {
							$data = array();
							$data['sm_id'] = $_POST['sm_id'][$i];
							$data['creditor_id'] = $_POST['creditor_id'];
							$data['updated_by'] = $_POST['login_user_id'];
							$rs = $this->apimodel->insertData('sm_creditor_mapping', $data, '1');
						}
					}
				}
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete SM Creditor mapping
	function delSMCreditor()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->delrecord('sm_creditor_mapping', 'sm_creditor_id', $_POST['id']);
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get states
	function getStateData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("state_id,state_name", "states", "isactive='1' ", "state_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For lead listing
	function leadListing()
	{//echo 12;die;
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
	  //  echo 123;die;
	//	echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
        //print_r($checkToken);die;
		if (!empty($checkToken->username)) {

			$get_result = $this->apimodel->leadListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Lead Export
	function exportLeads(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportLeads($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

    //For Lead Export
    function exportreport(){
        //echo "<pre>post";print_r($_POST);exit;
        $checkToken = $this->verify_request($_POST['utoken']);

        if(!empty($checkToken->username)){
            $get_result = $this->apimodel->exportreport($_POST);
            //echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
            exit;
        }else{
            echo $checkToken;
        }
    }

	function exportProposal(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportProposal($_POST);
		//	echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ),
                "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//Get creditors
	function getRoleWiseCreditorsData()
	{

		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$get_result = $this->apimodel->getRoleWiseCreditorsData($_POST['user_id']);
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get SM locations
	function getSMLocations()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {


			$get_result = $this->apimodel->getSMLocations($_POST['user_id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get creditors plans
	function getCreditorsPlansData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("plan_id,plan_name", "master_plan", "creditor_id='" . $_POST['creditor_id'] . "' ", "plan_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get All SM except already map to selected creditor
	function getSMDataCreditorWise()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo $_POST['creditor_id'];exit;
			// Get already mapped sm's
			$getAlreadySM = $this->apimodel->getSortedData("sm_id", "sm_creditor_mapping", "creditor_id='" . $_POST['creditor_id'] . "' ", "sm_creditor_id", "desc");
			//echo "<pre>";print_r($getAlreadySM);exit;
			//echo $getAlreadySM[0]->sm_id;
			//exit;

			$condition = "";
			if (!empty($getAlreadySM)) {
				$sm_id_arr = array();
				for ($i = 0; $i < sizeof($getAlreadySM); $i++) {
					$sm_id_arr[] = $getAlreadySM[$i]->sm_id;
				}
				$sm_ids = implode(",", $sm_id_arr);
				$condition = "isactive='1' && role_id='3' && employee_id NOT IN ($sm_ids) ";
				//echo $condition; exit;

			} else {
				$condition = "isactive='1' && role_id='3'";
			}

			$get_result = $this->apimodel->getSortedData("employee_id,employee_full_name", "master_employee", $condition, "employee_full_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
//Save Proposal

 function saveProposalapi()
{
	$api_data = json_decode(file_get_contents('php://input') , true);
//	print_r($api_data['ClientCreation']);die;
	$lead_id = $api_data['ClientCreation']['LeadId'];
//	print_R($api_data['ClientCreation']);die;
	//$child_cnt = 0;
	$lead_det= $this->db->query("select mc.customer_id,ld.plan_id,ld.trace_id from lead_details as ld,master_customer as mc where  ld.lead_id = mc.lead_id and ld.lead_id = '$lead_id' ")->row_array();
	//var_dump($lead_det);exit;
	$data['customer_id'] = $lead_det['customer_id'];
	$data['plan_id'] = $lead_det['plan_id'];
	$data['trace_id'] = $lead_det['trace_id'];
	$data['lead_id'] = $lead_id;
	$family_construct = explode("+",$api_data['QuoteRequest']['family_construct']);
	preg_match_all('!\d+\.*\d*!', $family_construct[0], $matches);
	$adult_cnt = $matches[0][0];
		preg_match_all('!\d+\.*\d*!', $family_construct[1], $matche1);
	$child_cnt =  (!empty($matche1[0][0])) ? $matche1[0][0] : '0'; ;
	$family_members_ac_count = $adult_cnt."-".$child_cnt;
	$data['family_members_ac_count'] = $family_members_ac_count;
        //var_dump($data);exit;

		$result = json_decode(curlFunction(SERVICE_URL . 'api2/saveGeneratedQuote', $data));

		$response = [];

		if (isset($result->status) && $result->status) {

			if(isset($result->data->quote_ids) && !empty($result->data->quote_ids)){

				$response = array('success' => true, 'msg' => "Quote Generated", "data" => $result->data);
			}

			$msg = '';

			if(isset($result->policy_errors) && !empty($result->policy_errors)){

				foreach($result->policy_errors as $key => $value){

					if(!empty($value) && $value[0] != '' && $value[0] != 'Invalid sum_insured'){

						$msg .= $key . " : " . $value[0] . "<br> ";
					}
				}
			}

			if(!empty($response)){

				$response['policy_errors'] = $msg;
			}
			else{

				$response = array('success' => false, 'msg' => $result->messages, "data" => $result->data, "policy_errors" => $msg);
			}

			echo json_encode($response);

		} else {
			if(isset($result->data)){
				echo json_encode(array('success' => false, 'msg' => $result->messages, "data" => $result->data));
			} else{
				echo json_encode(array('success' => false, 'msg' => $result->messages, "data" => ""));
			}
		}

}
	//Add Lead
public 	function addLead()
	{//echo 123;die;
	
		//echo "<pre>post";print_r($_POST);exit;
		
			
		//	$result = "";
//$api_data = json_decode(file_get_contents('php://input') , true);
//print_R($_POST);die;
if(!empty($api_data))
{
	$_POST = $api_data;
	$partner = $api_data['partner'];
	$_POST['utoken'] = $api_data['utoken'];
	$plan = $api_data['plan'];
	$creditor_id= $this->db->query("select creditor_id from master_ceditors where creaditor_name = '$partner' ")->row_array();
	if(empty($creditor_id))
	{
		echo json_encode(array("status_code" => "404", "Metadata" => array("Message" => 'Partner Name does not exist'), "Data" => ""));
				exit;
	}
	$_POST['creditor_id'] = $creditor_id['creditor_id'];
	$plan_id = $this->db->query('select plan_id from master_plan where creditor_id = '.$_POST['creditor_id']." and plan_name = '$plan'")->row_array();
	if(empty($plan_id))
	{
		echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Plan does not exist'), "Data" => ""));
				exit;
	}
	$mobile_no = $_POST['mobile_number'];
	/*$lead_det_check = $this->db->query("select lead_id from lead_details where mobile_no = '$mobile_no'")->row_array();
	if(!empty($lead_det_check))
	{
		echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Lead already created'), "Data" => ""));
				exit;
	}*/
	$_POST['plan_id'] = $plan_id['plan_id'];
	
	$emp_id = $api_data['employee_id'];
	$_POST['login_user_id'] = $api_data['employee_id'];
	$sm_location = $api_data['sm_location'];
	$_POST['lan_id'] = $api_data['lan_number'];
	$sm_loc_id = $this->db->query('select ml.location_id from master_location as ml,user_locations as ul where ml.location_id = ul.location_id and ul.user_id = '.$api_data['employee_id']." and ml.location_name = '$sm_location'")->row_array();
	$_POST['lead_location_id'] = $sm_loc_id['location_id'];

	
}else
{
	$_POST = $_POST;
}
$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

				$cust_data = array();
				$cust_data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
				$cust_data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
				$cust_data['middle_name'] = (!empty($_POST['middle_name'])) ? $_POST['middle_name'] : '';
				$cust_data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
				$full_name = '';
				if (!empty($_POST['first_name'])) {
					$full_name .= $_POST['first_name'];
				}
				if (!empty($_POST['middle_name'])) {
					$full_name .= " " . $_POST['middle_name'];
				}
				if (!empty($_POST['last_name'])) {
					$full_name .= " " . $_POST['last_name'];
				}

				$cust_data['full_name'] = $full_name;
				$cust_data['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : '';
				$cust_data['dob'] = (!empty($_POST['dob'])) ? date("Y-m-d", strtotime($_POST['dob'])) : '';
				$cust_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
				$cust_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$cust_data['isactive'] = 1;
				$cust_data['createdon'] = date("Y-m-d H:i:s");
				$cust_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$cust_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
//echo"<pre>";print_r($cust_data);die;
				$customer_id = $this->apimodel->insertData('master_customer', $cust_data, 1);
				
				//Create Lead
				$lead_data = array();
				$timestamp = time();
				$lead_data['trace_id'] = $customer_id . $timestamp;
				$lead_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$lead_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
				$lead_data['sales_manager_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$lead_data['primary_customer_id'] = $customer_id;
				$lead_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$lead_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';

				$lead_data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
				$lead_data['portal_id'] = (!empty($_POST['portal_id'])) ? $_POST['portal_id'] : 'Creditor Portal';
				$lead_data['vertical'] = (!empty($_POST['vertical'])) ? $_POST['vertical'] : 'Vertical';
				$lead_data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
				$lead_data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
				$lead_data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : 'N';
				$lead_data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
				$lead_data['lead_location_id'] = (!empty($_POST['lead_location_id'])) ? $_POST['lead_location_id'] : 0;

				$lead_data['createdon'] = date("Y-m-d H:i:s");
				$lead_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$lead_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';

//echo"<pre>";print_r($lead_data);die;

				$result = $this->apimodel->insertData('lead_details', $lead_data, 1);
				
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");

				$lead_id = $result;

           
				$this->apimodel->updateRecord('master_customer', [
					'lead_id' => $lead_id
				], "customer_id =" . $customer_id);
				
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");
				
				//Add proposal
				$proposal_data = array();
				$proposal_data['trace_id'] = $lead_data['trace_id'];
				$proposal_data['lead_id'] = $result;
				$proposal_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$proposal_data['customer_id'] = $customer_id;
				$proposal_data['status'] = 'Pending';

				$proposal_data['created_at'] = date("Y-m-d H:i:s");
				$proposal_data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$proposal_data['updated_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
//echo"<pre>";print_r($proposal_data);die;

				$proposal_details_id = $this->apimodel->insertData('proposal_details', $proposal_data, 1);

				//insert_proposal_log($proposal_details_id, $_POST['login_user_id'], $remark);
				
				
				//log entries
				$lead_id = $result;
				$created_on = date("Y-m-d H:i:s");
				$created_by = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				//customer
				$customer_action = "create_customer";
				$customer_request_data = json_encode($cust_data);
				$customer_response_data = json_encode(array("response"=>"Customer added."));
				
				//Lead
				$lead_action = "create_lead";
				$lead_request_data = json_encode($lead_data);
				$lead_response_data = json_encode(array("response"=>"Lead added."));
				
				//Proposal
				$proposal_action = "create_proposal_entry";
				$proposal_request_data = json_encode($proposal_data);
				$proposal_response_data = json_encode(array("response"=>"Proposal entery added."));
				
				if(!empty($customer_id)){
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in customer insert."));
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}
				
				if(!empty($result)){
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}
				
				if(!empty($proposal_details_id)){
					$proposallog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}

			//}

 if(!empty($api_data)) {

                $lead_det = $this->db->query("select mc.customer_id,ld.plan_id,ld.trace_id,ld.tenure from lead_details as ld,master_customer as mc where  ld.lead_id = mc.lead_id and ld.lead_id = '$lead_id' ")->row_array();

                $data['customer_id'] = $lead_det['customer_id'];
                $data['plan_id'] = $lead_det['plan_id'];
                $data['trace_id'] = $lead_det['trace_id'];
                $data['tenure'] = $lead_det['tenure'];
                $data['lead_id'] = $lead_id;
                $SumInsuredData=$api_data['QuoteRequest']['SumInsuredData'];
             // print_r($SumInsuredData);die;
                $adult_cnt =$api_data['QuoteRequest']['adult_count'];
                $child_cnt =$api_data['QuoteRequest']['child_count'];
                $family_members_ac_count = $adult_cnt . "-" . $child_cnt;
               // $data['family_members_ac_count'] = $family_members_ac_count;
				//print_r($family_members_ac_count);die;
				$data_quote['plan_id'] = $data['plan_id'];
				$data_quote['lead_id'] = $result;
				$data_quote['family_members_ac_count'] = $family_members_ac_count;
				$data_quote['customer_id'] = $customer_id;
				$data_quote['SumInsuredData'] = $SumInsuredData;
				$data_quote['tenure'] = $api_data['tenure'];
				$data_quote['trace_id'] = $lead_data['trace_id'];
				$get_premium=    $this->getPremiumApi($data_quote);

               // $result_premium = json_decode(curlFunction(SERVICE_URL . '/api2/generateQuote', $_POST));
//print_r($get_premium);exit;
            }

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Lead created successfully.'), "Data" => $result,"Quote"=>$get_premium));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
	} else {
			echo $checkToken;
		}
	}
	function fetchPremium()
    {
        $api_data = json_decode(file_get_contents('php://input') , true);
        $_POST = $api_data;
        $token=$api_data['utoken'];
        $emp_id="";
        $checkToken=$this->db->query("select emp_id,time  from token_table t where token= '".$token."' and type=1")->row();
       // print_r($this->db->last_query());die;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $time=$checkToken->time;
            $minutes = (time() - strtotime($time)) / 60;
            if($minutes > 15){
                $response = array('success' => false, 'msg' => "Token Expired!");
                echo json_encode($response);
                exit;
            }
            $emp_id=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken->emp_id."'")->row()->employee_id;
        }
        $adult_cnt =$_POST['adult_count'];
        $child_cnt =$_POST['child_count'];
        $SumInsuredData =$_POST['SumInsured'];

        $family_members_ac_count = $adult_cnt . "-" . $child_cnt;

        $data_quote['family_members_ac_count'] = $family_members_ac_count;
        $data_quote['plan_id'] = $_POST['plan_id'];
        $data_quote['MaxAge'] = $_POST['MaxAge'];

        $data_quote['SumInsuredData'] = $SumInsuredData;
        $data_quote['tenure'] = $_POST['tenure'];
        $get_premium=    $this->getPremiumFetchApi($data_quote);
        echo json_encode($get_premium);die;
    }
    function getPremiumFetchApi($data)
    {


        $_POST = $data;

        $result['status'] = true;
        $result['messages'] = [];
        $hospi_cash_group_code = $group_code_type = '';
        $ghc_logic_plans = [407, 408, 409, 410, 411];


        $plan_details = $this->apimodel->getProductDetailsAll($this->input->post('plan_id'), 'mp.policy_sub_type_id', 'DESC');

        $family_construct_raw = $this->input->post('family_members_ac_count');



        if (!$family_construct_raw) {
            $result['status'] = false;
            echo json_encode($result);
            exit;
        }
        $family_construct = explode('-', $family_construct_raw);
        $adultsToCalculate = $family_construct[0];
        $childrenToCalculate = $family_construct[1];







                foreach ($plan_details as $policy) {

                    $policy_id = $policy->policy_id;
                    $policy_sub_type_code = $policy->policy_sub_type_code;
                    $basis_id = $policy->basis_id;

                    $members = $this->apimodel->getPolicyFamilyDetails($policy_id);

                    $is_individual_cover = null;

                    // Store the total children and adults allowed in the policy
                    $child_count = 0;
                    $adult_count = 0;

                    foreach ($members as $key => $construct) {
                        $adult_member_type_id = array(1, 2, 3, 4);
                        if (in_array($members[$key]->member_type_id, $adult_member_type_id)) {
                            $adult_count++;
                        } else {
                            $child_count++;
                        }
                    }


                    if ($policy->sitype_id != 1) {
                        $is_individual_cover = false;
                    } else {
                        $is_individual_cover = true;
                    }


                    //print_r($members);
                    $individual_adults_premium_calculated = 0;

                    foreach ($members as $member) {

                        $member_type = $member->member_type;

                        $sumD['SumInsured'] = $_POST['SumInsuredData'];
                        $policy_sub_type = $policy->policy_sub_type_id;

                            $policy_sub_type_id = $this->db->query("select policy_sub_type_id from master_policy_sub_type where policy_sub_type_id='$policy_sub_type'")->row()->policy_sub_type_id;
                            //print_R($this->db->last_query());exit;
                            if($policy_sub_type_id == 1){
                                $data['ghi_cover'] = $sumD['SumInsured'];
                            }else if($policy_sub_type_id == 2){
                                $data['pa_cover'] = $sumD['SumInsured'];
                            }else if($policy_sub_type_id == 3){
                                $data['ci_cover'] = $sumD['SumInsured'];
                            }else if($policy_sub_type_id == 5){
                                $data['super_top_up_cover'] = $sumD['SumInsured'];
                            }else if($policy_sub_type_id == 6){
                                $data['hospi_cash'] = $sumD['SumInsured'];
                            }

                        $requestData = [
                            'is_individual_cover' => $is_individual_cover,
                            'policy_id' => $policy_id,
                            'sum_insured' => $this->getSumInSuredBasedOnPolicySubType($policy->policy_sub_type_id,$data,$policy_id),
                            'policy_sub_type_code' => $policy_sub_type_code,
                            'member_type' => $member_type,
                            'policy_sub_type_id' => $policy->policy_sub_type_id,
                            'age' => $_POST['MaxAge'],
                            'adults_to_calculate'	=> $adultsToCalculate,
                            'children_to_calculate'	=> $childrenToCalculate,
                            'tenure'	=> trim($_POST['tenure']),
                            'number_of_ci' => trim($this->input->post('numbers_of_ci')),
                            'deductable'	=> trim($this->input->post('deductable')),
                            'hospi_cash_group_code' => $hospi_cash_group_code,
                            'group_code_type' => $group_code_type
                        ];
                        // print_r($requestData);
                        // print_r($requestData);exit;

                        $rater = Rater::make($basis_id, $requestData);
print_R($rater);die;
                        $planName = $rater->getPlanName();
                       // print_R($planName);die;
                        $result['messages'][$planName] = $rater->getMessages();
                        //print_R($result['data']);die;
                        if ($rater->hasPremium()) {

                            if ($policy->policy_sub_type_id == 6 && in_array($this->input->post('plan_id'), $ghc_logic_plans)) {

                                $hospi_cash_group_code = $rater->getGroupCode();
                                $group_code_type = $rater->getGroupCodeType();
                            }

                            $applicantPolicies[$planName] = $rater->getPremium();
                            $is_individual_cover &&  $individual_adults_premium_calculated++;
                            continue;
                        }
                    }
                }
                // exit;
                $result['data']['policies'] = $applicantPolicies;


            $total_premium = 0;

            if (isset($result['data']['policies'])) {
                foreach ($result['data']['policies'] as $name => $rate) {
                    $total_premium += $rate;
                }
                $result['data']['total_premium'] = number_format(round($total_premium, 2), 2, '.', '');
            }


        if (! $result['data']['total_premium']) {
            $result['status'] = 'Error';
        } else {
            $result['status'] = 'Success';        }

        return $result;
          //  next($lead_details);

    }

    function getPremiumApi($data)
	{


$_POST = $data;
		$result['status'] = true;
		$result['messages'] = [];
		$hospi_cash_group_code = $group_code_type = '';
		$ghc_logic_plans = [407, 408, 409, 410, 411];


		$plan_details = $this->apimodel->getProductDetailsAll($this->input->post('plan_id'), 'mp.policy_sub_type_id', 'DESC');

		$family_construct_raw = $this->input->post('family_members_ac_count');



		if (!$family_construct_raw) {
			$result['status'] = false;
			echo json_encode($result);
			exit;
		}
		$family_construct = explode('-', $family_construct_raw);
		$adultsToCalculate = $family_construct[0];
		$childrenToCalculate = $family_construct[1];
		$earlierGeneratedPremiums = $this->apimodel->getGeneratedPremiums($_POST);
		//print_R($earlierGeneratedPremiums);
		$lead_details = $this->apimodel->getLeadDetails($_POST['lead_id']);


		$spouse_dob = $this->input->post('spouse_dob');

		$spouseAge = $this->input->post('spouse_age');

		if (empty($spouseAge) && !empty($spouse_dob)) {
			$spouseAge = date_diff(date_create($spouse_dob), date_create('today'))->y;
		}

		if ($adultsToCalculate == 1 && $spouseAge) {
			$member_to_calculate = 2;
		} else {
			$member_to_calculate = 1;
		}

		// Calculate total applicants
		if ($lead_details[0]->is_coapplicant == "Y") {
			$total_applicants = $lead_details[0]->coapplicant_no + 1;
		} else {
			$total_applicants = 1;
		}

		// Loop through All Applicants and Co-Applicants
		for ($i = 0; $i < $total_applicants; $i++) {

			$applicantPolicies = [];

			// If there is no applicant details available for the current lead we will break 
			if (!current($lead_details)) {
				break;
			}

			$current_customer_id = current($lead_details)->customer_id;

			if ($i == 0) {
				$applicantName = "Applicant";
			} else {
				$applicantName = "Co-Applicant" . $i;
			}

			if ($current_customer_id == $_POST['customer_id']) {
				// Loop through all policies in the plan and generate premiums
				// exit('12345');
				//print_R($plan_details);
				foreach ($plan_details as $policy) {
					
					$policy_id = $policy->policy_id;
					$policy_sub_type_code = $policy->policy_sub_type_code;
					$basis_id = $policy->basis_id;

					$members = $this->apimodel->getPolicyFamilyDetails($policy_id);

					$is_individual_cover = null;

					// Store the total children and adults allowed in the policy
					$child_count = 0;
					$adult_count = 0;

					foreach ($members as $key => $construct) {
						$adult_member_type_id = array(1, 2, 3, 4);
						if (in_array($members[$key]->member_type_id, $adult_member_type_id)) {
							$adult_count++;
						} else {
							$child_count++;
						}
					}


					if ($policy->sitype_id != 1) {
						$is_individual_cover = false;
					} else {
						$is_individual_cover = true;
					}

					// Reduce array in case of family cover and single adult
					if ($policy->sitype_id != 1 || $adultsToCalculate == 1) {
						$members = array_filter($members, function ($construct) use ($member_to_calculate) {
							return $construct->member_type_id == $member_to_calculate;
						});
					}

					$individual_adults_premium_calculated = 0;

					foreach ($members as $member) {

						$member_type = $member->member_type;
						// For Individual type if we have already calculated for required adults we will skip
						if ($individual_adults_premium_calculated == $adultsToCalculate) {
							break;
						}

						if(isset($_POST['source']) && strcmp($_POST['source'],"customer")){
							$proposersAge=$_POST['max_age'];
						}else{
							$proposersAge = $this->apimodel->getProposersAge($_POST['lead_id'], $current_customer_id);
						}															

						// If we are calculating age for individual member we have to use current memebers age;
						if ($is_individual_cover && $member->member_type_id == 1) {
							$ageToCalculate = $proposersAge;
						} else if ($is_individual_cover && $member->member_type_id == 2) {
							$ageToCalculate = $spouseAge;
						} else if (!$is_individual_cover) {
							$ageToCalculate = $proposersAge > $spouseAge ? $proposersAge : $spouseAge;
						}
$SumInsuredData = $_POST['SumInsuredData'];
  foreach ($SumInsuredData as $sumD){
                    if($sumD['PlanCode'] == ''){
                        $response = array('success' => false, 'msg' => "PlanCode Required!");
                        echo json_encode($response);
                    }
                    $policy_sub_type_id=$this->db->query('select policy_sub_type_id from master_policy_sub_type where plan_code='.$sumD['PlanCode'])->row()->policy_sub_type_id;
                    if(empty($policy_sub_type_id) || is_null($policy_sub_type_id)){
                        $response = array('success' => false, 'msg' => $sumD['PlanCode']." PlanCode Not Found!");
                        echo json_encode($response);
                    }
                    if($policy_sub_type_id == 1){
                        $data['ghi_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 2){
                        $data['pa_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 3){
                        $data['ci_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 5){
                        $data['super_top_up_cover'] = $sumD['SumInsured'];
                    }else if($policy_sub_type_id == 6){
                        $data['hospi_cash'] = $sumD['SumInsured'];
                    }
                }
						$requestData = [
							'is_individual_cover' => $is_individual_cover,
							'policy_id' => $policy_id,
							'sum_insured' => $this->getSumInSuredBasedOnPolicySubType($policy->policy_sub_type_id,$data,$policy_id),
							'policy_sub_type_code' => $policy_sub_type_code,
							'member_type' => $member_type,
							'policy_sub_type_id' => $policy->policy_sub_type_id,
							'age' => $ageToCalculate,
							'adults_to_calculate'	=> $adultsToCalculate,
							'children_to_calculate'	=> $childrenToCalculate,
							'tenure'	=> trim($_POST['tenure']),
							'number_of_ci' => trim($this->input->post('numbers_of_ci')),
							'deductable'	=> trim($this->input->post('deductable')),
							'hospi_cash_group_code' => $hospi_cash_group_code,
							'group_code_type' => $group_code_type
						];
						// print_r($requestData);exit;
						// print_r($requestData);exit;

						$rater = Rater::make($basis_id, $requestData);
//print_R($rater);die;
						$planName = $rater->getPlanName();
						$result['messages'][$applicantName][$planName] = $rater->getMessages();

						if ($rater->hasPremium()) {

							if ($policy->policy_sub_type_id == 6 && in_array($this->input->post('plan_id'), $ghc_logic_plans)) {

								$hospi_cash_group_code = $rater->getGroupCode();
								$group_code_type = $rater->getGroupCodeType();
							}

							$applicantPolicies[$planName] = $rater->getPremium();
							$is_individual_cover &&  $individual_adults_premium_calculated++;
							continue;
						}
					}
				}
				// exit;
				$result['data'][$applicantName]['policies'] = $applicantPolicies;
			} else {
				if (isset($earlierGeneratedPremiums[$current_customer_id])) {
					$result['data'][$applicantName]['policies'] = $earlierGeneratedPremiums[$current_customer_id];
				}
			}

			$total_premium = 0;

			if (isset($result['data'][$applicantName]['policies'])) {
				foreach ($result['data'][$applicantName]['policies'] as $name => $rate) {
					$total_premium += $rate;
				}
				$result['data'][$applicantName]['total_premium'] = number_format(round($total_premium, 2), 2, '.', '');
			}

			next($lead_details);
		}

		$net_premium = 0;

		foreach ($result['data'] as $applicant) {
			if (isset($applicant['total_premium'])) {
				$net_premium += $applicant['total_premium'];
			}
		}


		if (!$net_premium) {
			$result['status'] = false;
		} else {
			$result['data']['net_premium'] = number_format(round($net_premium, 2), 2, '.', '');
		}

		return $result;
	}
		protected function getSumInSuredBasedOnPolicySubType($policy_sub_type_id,$data_cover,$policy_id = 0, $mandatory_if_not_selected = [], $policy_sub_type_code_arr = [])
	{
$_POST = $data_cover;
		$ghi_cover = $pa_cover = $ci_cover = $hospi_cash = $super_top_up_cover = null;

		if (isset($_POST['ghi_cover']) && !empty(trim($_POST['ghi_cover']))) {
			$ghi_cover = trim($_POST['ghi_cover']);
		} else if (isset($_POST['sum_insured1']) && !empty(trim($_POST['sum_insured1']))) {
			$ghi_cover = trim($_POST['sum_insured1']);
		} else {
			$ghi_cover = null;
		}
		if (isset($_POST['pa_cover']) && !empty(trim($_POST['pa_cover']))) {
			$pa_cover = trim($_POST['pa_cover']);
		} else if (isset($_POST['sum_insured2']) && !empty(trim($_POST['sum_insured2']))) {
			$pa_cover = trim($_POST['sum_insured2']);
		} else {
			$pa_cover = null;
		}

		if (isset($_POST['ci_cover']) && !empty(trim($_POST['ci_cover']))) {
			$ci_cover = trim($_POST['ci_cover']);
		} else if (isset($_POST['sum_insured3']) && !empty(trim($_POST['sum_insured3']))) {
			$ci_cover = trim($_POST['sum_insured3']);
		} else {
			$ci_cover = null;
		}

		if (isset($_POST['hospi_cash']) && !empty(trim($_POST['hospi_cash']))) {
			$hospi_cash = trim($_POST['hospi_cash']);
		} else if (isset($_POST['sum_insured6']) && !empty(trim($_POST['sum_insured6']))) {
			$hospi_cash = trim($_POST['sum_insured6']);
		} else {
			$hospi_cash = null;
		}

		if (isset($_POST['super_top_up_cover']) && !empty(trim($_POST['super_top_up_cover']))) {
			$super_top_up_cover = trim($_POST['super_top_up_cover']);
		} else if (isset($_POST['sum_insured5_1']) && !empty(trim($_POST['sum_insured5_1']))) {
			$super_top_up_cover = trim($_POST['sum_insured5_1']);
		} else {
			$super_top_up_cover = null;
		}


		$mapping = [
			1 => $ghi_cover,
			2 => $pa_cover,
			3 => $ci_cover,
			6 => $hospi_cash,
			5 => $super_top_up_cover,
		];
		if (!empty($mandatory_if_not_selected) && $policy_id != 0) {

			if ($mapping[$policy_sub_type_id] == null) {

				if (isset($mandatory_if_not_selected[$policy_id])) {

					if ($mapping[$mandatory_if_not_selected[$policy_id]] == null) {

						$result['messages'][] = "Either " . $policy_sub_type_code_arr[$policy_sub_type_id] . " or " . $policy_sub_type_code_arr[$mandatory_if_not_selected[$policy_id]] . " is Mandatory";
						$result['status'] = false;
						echo json_encode($result);
						exit;
					}
				}
			}
		}
		return $mapping[$policy_sub_type_id];
	}

	//For customer proposal listing
	function customerProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->customerProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}


	//For discrepancy proposal listing
	function discrepancyProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->discrepancyProposalListing($_POST);
			//echo "<pre>ddd";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For bo proposal listing
	function boProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->boProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//reject proposals
	function rejectProposal()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if (empty($_POST['login_user_id'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'), "Data" => NULL));
				exit;
			}

			if (empty($_POST['login_user_name'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'), "Data" => NULL));
				exit;
			}


			$data = array();
			$data['status'] = 'Rejected';
			$data['updatedby'] = $_POST['login_user_id'];

			$pdata = array();
			$pdata['status'] = 'Rejected';
			$pdata['updated_by'] = $_POST['login_user_id'];
			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				$this->apimodel->updateRecord('proposal_details', $pdata, "lead_id='" . $_POST['id'] . "' ");
				//get login user details
				$remark = "Proposal Rejected by " . $_POST['login_user_name'];
				insert_proposal_log($_POST['id'], $_POST['login_user_id'], $remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal rejected successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//move to underwriting
	function moveToUW()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if (empty($_POST['login_user_id'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'), "Data" => NULL));
				exit;
			}

			if (empty($_POST['login_user_name'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'), "Data" => NULL));
				exit;
			}


			$data = array();
			$data['status'] = 'UW-Approval-Awaiting';
			$data['updatedby'] = $_POST['login_user_id'];

			$pdata = array();
			$pdata['status'] = 'UW-Approval-Awaiting';
			$pdata['updated_by'] = $_POST['login_user_id'];

			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				$this->apimodel->updateRecord('proposal_details', $pdata, "lead_id='" . $_POST['id'] . "' ");
				//get login user details
				$remark = "Proposal moved to underwriting by " . $_POST['login_user_name'];
				insert_proposal_log($_POST['id'], $_POST['login_user_id'], $remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal moved to underwriting successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//accept proposals
	function acceptProposal()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if (empty($_POST['login_user_id'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'), "Data" => NULL));
				exit;
			}

			if (empty($_POST['login_user_name'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'), "Data" => NULL));
				exit;
			}

			//get lead details
			$lead_details = $this->apimodel->getdata("lead_details", "lead_id, creditor_id, plan_id, status", "lead_id='" . $_POST['id'] . "' ");

			//Updating payment table
			$paymentdata = array();
			$paymentdata['payment_status'] = 'Success';
			$paymentdata['updated_at'] = date("Y-m-d H:i:s");
			$paymentdata['updated_by'] = $_POST['login_user_id'];
			$result = $this->apimodel->updateRecord('proposal_payment_details', $paymentdata, "lead_id ='" . $_POST['id'] . "' ");

			//get trigger data
			$getTriggerData = $this->db->query("select l.reject_reason, l.lan_id, l.plan_id, l.lead_id, l.trace_id, c.first_name, c.last_name, c.full_name, c.mobile_no as customer_mobile, c.email_id as customer_email, e.employee_fname, e.employee_lname, e.employee_full_name, e.mobile_number as sm_mobile, e.email_id as sm_email, p.plan_name, cr.creaditor_name from lead_details as l, master_customer as c, master_employee as e, master_plan as p, master_ceditors as cr where l.lead_id='".$_POST['id']."' and l.primary_customer_id = c.customer_id and l.createdby = e.employee_id and l.plan_id = p.plan_id and p.creditor_id = cr.creditor_id")->row_array();

			//check already in UW or UW user aproving the lead.
			if($lead_details[0]['status'] == 'UW-Approval-Awaiting'){
				//Updating Lead entry
				$leaddata = array();
				$leaddata['status'] = 'Customer-Payment-Received'; //'Approved';
				$leaddata['updatedby'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('lead_details', $leaddata, "lead_id='" . $_POST['id'] . "' ");

				//Trigger for if UW accept.
				//customer trigger
				$cus_alert_ids = ['A1668'];
				$cus_data['lead_id'] = $_POST['id'];
				$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
				$cus_data['plan_id'] = $getTriggerData['plan_id'];
				$cus_data['email_id'] = $getTriggerData['customer_email'];

				$cus_data['alerts'][] = $getTriggerData['full_name'];
				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['trace_id'];
				$cus_data['alerts'][] = "4";
				$cus_data['alerts'][] = date("d-m-Y");

				//customer trigger
				$cus_alert_ids = ['A1674'];
				$cus_data['alerts'] = [];
				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['lan_id'];
				$cus_data['alerts'][] = $getTriggerData['reject_reason'];
				$cus_data['alerts'][] = $getTriggerData['trace_id'];
				$cus_data['alerts'][] = ''; //Proposal number goes here
				

				$customer_response = triggerCommunication($cus_alert_ids, $cus_data);

				insert_application_log($_POST['id'], 'uw_accept_reject_customer', json_encode($cus_data), json_encode($customer_response), $paymentdata['updated_by']);

				//sm trigger
				$sm_alert_ids = ['A1669'];
				$sm_data['lead_id'] = $_POST['id'];
				$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
				$sm_data['plan_id'] = $getTriggerData['plan_id'];
				$sm_data['email_id'] = $getTriggerData['sm_email'];

				$sm_data['alerts'][] = $getTriggerData['full_name'];
				$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$sm_data['alerts'][] = $getTriggerData['employee_full_name'];
				$sm_data['alerts'][] = "4";
				$sm_data['alerts'][] = $getTriggerData['trace_id'];

				$sm_response = triggerCommunication($sm_alert_ids, $sm_data);

				insert_application_log($_POST['id'], 'uw_accept_reject_agent', json_encode($sm_data), json_encode($sm_response), $paymentdata['updated_by']);

				$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal accepted successfully.'), "Data" => 2));

				insert_application_log($_POST['id'], 'uw_proposal_accept', json_encode($_POST), $response, $paymentdata['updated_by']);

				echo $response;
				exit;
			}else{
				//check UW condition
				$uwCheck = checkUWCase($lead_details[0]['lead_id'], $lead_details[0]['creditor_id'], $lead_details[0]['plan_id']);
				if($uwCheck){
					//move lead to UW
					$leaddata = array();
					$leaddata['status'] = 'UW-Approval-Awaiting';
					$leaddata['updatedby'] = $_POST['login_user_id'];
					$this->apimodel->updateRecord('lead_details', $leaddata, "lead_id='" . $_POST['id'] . "' ");

					//Trigger move to UW
					//customer trigger
					$cus_alert_ids = ['A1666'];
					$cus_data['lead_id'] = $_POST['id'];
					$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
					$cus_data['plan_id'] = $getTriggerData['plan_id'];
					$cus_data['email_id'] = $getTriggerData['customer_email'];

					$cus_data['alerts'][] = $getTriggerData['full_name'];
					$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
					$cus_data['alerts'][] = "4";
					$cus_data['alerts'][] = $getTriggerData['trace_id'];

					$customer_response = triggerCommunication($cus_alert_ids, $cus_data);

					insert_application_log($_POST['id'], 'uw_bucket_movement_acceptance_customer', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);

					//sm trigger
					$sm_alert_ids = ['A1667'];
					$sm_data['lead_id'] = $_POST['id'];
					$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
					$sm_data['plan_id'] = $getTriggerData['plan_id'];
					$sm_data['email_id'] = $getTriggerData['sm_email'];

					$sm_data['alerts'][] = $getTriggerData['full_name'];
					$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
					$sm_data['alerts'][] = $getTriggerData['employee_full_name'];
					$sm_data['alerts'][] = $getTriggerData['trace_id'];
					$sm_data['alerts'][] = "4";

					$sm_response = triggerCommunication($sm_alert_ids, $sm_data);
					
					insert_application_log($_POST['id'], 'uw_bucket_movement_acceptance_agent', json_encode($sm_data), json_encode($sm_response), $_POST['login_user_id']);

					//uw_trigger

					$uw_user_data = $this->apimodel->getdata("master_employee", "employee_fname,employee_lname, employee_full_name, mobile_number, email_id", "role_id = 7");
					
					$uw_alert_ids = ['A1673'];
					$uw_data['lead_id'] = $_POST['id'];
					$uw_data['plan_id'] = $getTriggerData['plan_id'];
					$uw_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
					$uw_data['alerts'][] = $getTriggerData['creaditor_name'];
					$uw_data['alerts'][] = $getTriggerData['lan_id'];
					$uw_data['alerts'][] = date('d-m-Y');
					$uw_data['alerts'][] = ''; //remarks goes here

					foreach($uw_user_data as $uw_user){

						$uw_data['mobile_no'] = $uw_user['mobile_number'];
						$uw_data['email_id'] = $uw_user['email_id'];

						$uw_response = triggerCommunication($uw_alert_ids, $uw_data);

						insert_application_log($_POST['id'], 'uw_bucket_movement_acceptance_uw', json_encode($uw_data), json_encode($uw_response), $_POST['login_user_id']);
					}

					$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal moved to UW successfully.'), "Data" => 1));

					insert_application_log($_POST['id'], 'uw_proposal_accept', json_encode($_POST), $response, $paymentdata['updated_by']);

					echo $response;
					exit;

				}else{
					//Updating Lead entry
					$leaddata = array();
					$leaddata['status'] = 'Customer-Payment-Received'; //'Approved';
					$leaddata['updatedby'] = $_POST['login_user_id'];
					$this->apimodel->updateRecord('lead_details', $leaddata, "lead_id='" . $_POST['id'] . "' ");

					$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal accepted successfully.'), "Data" => 2));
					insert_application_log($_POST['id'], 'uw_proposal_accept', json_encode($_POST), $response, $paymentdata['updated_by']);

					echo $response;
					exit;
				}
			}
			
			
		} else {
			echo $checkToken;
		}
	}




	//For Full Quote
	/*function get_full_quote_data($policy_details, $lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured)
	{

		$count2 = 1;
		$maxcount2 = count($policy_details);
		foreach ($policy_details as $proposal) {
			$full_qoute = $this->apimodel->get_full_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $nominees, $proposal_details, $policy_sub_type_id, $sum_insured);
			//echo "<pre>";print_r($full_qoute);exit;
		}

		if ($full_qoute['status'] == 'error') {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $full_qoute['msg']), "Data" => NULL));
			exit;
		} else {

			if ($maxcount2 == $count2) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $full_qoute['msg']), "Data" => NULL));
				exit;
			}
			$count2++;
		}
	}
*/

	//dummy full code function
	/*function dummyFullQuote()
	{

		$member = array(
			0 => array(
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
					0 => array(
						"PlanCode" => "4211",
						"MemberQuestionDetails" => array(
							0 => array(
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


		$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => "1000", "salutation" => "Mr", "firstName" => "Danish", "middleName" => "", "lastName" => "Ak", "dateofBirth" => date('m/d/Y', strtotime("1986-04-11")), "gender" => "M", "educationalQualification" => null, "pinCode" => "425001", "uidNo" => null, "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => "infodanish@gmail.com", "contactMobileNo" => "8149212749", "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d'), "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => "kalyan", "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => "425001", "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "IPB100130770", "MasterPolicyNumber" => "61-20-00040-00-00", "GroupID" => "GRP001", "Product_Code" => "4211", "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => "2108233", "AutoRenewal" => 'Y', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => "1", "Source_Name" => "abc", "SPID" => "0", "TCN" => null, "CRTNO" => null, "RefCode1" => "0", "RefCode2" => "0", "Employee_Number" => "1000", "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => 1, "PaymentMode" => "online", "PolicyproductComponents" => [["PlanCode" => "4211", "SumInsured" => "300000", "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "457", "collectionRcvdDate" => "2020-10-20", "collectionMode" => "online", "remarks" => null, "instrumentNumber" => "pay_FrAaDQjzQFtQWG", "instrumentDate" => "2020-10-20", "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => null, "PaymentGatewayName" => "ABC_GFB", "TerminalID" => "EuxJCz8cZV9V63", "CardNo" => null]];

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
			CURLOPT_POSTFIELDS => json_encode($fqrequest),
			CURLOPT_HTTPHEADER => array(
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($fqrequest)),
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			),
		));

		$response = curl_exec($curl);


		$err = curl_error($curl);
		echo "<pre>";
		print_r($response);
		echo $err;
		exit;
	}
*/

	//Add Discrepancy
	function addDiscrepancy()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['created_by'] = $_POST['login_user_id'];
			$data['lead_id'] = $_POST['lead_id'];
			$data['discrepancy_type'] = $_POST['discrepancy_type'];
			$data['discrepancy_subtype'] = $_POST['discrepancy_subtype'];
			$data['remark'] = $_POST['remark'];

			$result = $this->apimodel->insertData('proposal_discrepancies', $data, 1);

			if (!empty($result)) {
				$remark = "Discrepancy Added with remark: " . $_POST['remark'];
				insert_proposal_log($result, $_POST['login_user_id'], $remark);

				$ldata = array();
				$ldata['status'] = 'Discrepancy';
				$ldata['updatedby'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('lead_details', $ldata, "lead_id='" . $_POST['lead_id'] . "' ");

				$udata = array();
				$udata['status'] = 'Discrepancy';
				$udata['updated_by'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('proposal_details', $udata, "lead_id='" . $_POST['lead_id'] . "' ");

				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Discrepancy added successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Reject Lead
	function rejectLead()
	{
		
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['updatedby'] = $_POST['login_user_id'];
			$ldata['status'] = 'Rejected';
			$data['lead_id'] = $_POST['lead_id'];
			$data['reject_reason'] = $_POST['reject_reason'];

			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='" . $_POST['lead_id'] . "' ");

			if (!empty($result)) {

				$udata = array();
				$udata['status'] = 'Rejected';
				$udata['updated_by'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('proposal_details', $udata, "lead_id='" . $_POST['lead_id'] . "' ");

				//Trigger Rejected Proposals
				//get trigger data
				$getTriggerData = $this->db->query(" select l.plan_id,l.lead_id, l.lan_id, l.trace_id, c.first_name, c.last_name, c.full_name, c.mobile_no as customer_mobile, c.email_id as customer_email, e.employee_fname, e.employee_lname, e.employee_full_name, e.mobile_number as sm_mobile, e.email_id as sm_email, p.plan_name from lead_details as l, master_customer as c, master_employee as e, master_plan as p where l.lead_id='".$_POST['lead_id']."' and l.primary_customer_id = c.customer_id and l.createdby = e.employee_id and l.plan_id = p.plan_id " )->row_array();

				//customer trigger
				$cus_alert_ids = ['A1670'];

				$cus_data['lead_id'] = $_POST['lead_id'];
				$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
				$cus_data['plan_id'] = $getTriggerData['plan_id'];
				$cus_data['email_id'] = $getTriggerData['customer_email'];

				$cus_data['alerts'][] = $getTriggerData['full_name'];
				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['trace_id'];
				$cus_data['alerts'][] = $getTriggerData['full_name'];
				$cus_data['alerts'][] = $_POST['reject_reason'];
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				
				$customer_response = triggerCommunication($cus_alert_ids, $cus_data);
				insert_application_log($_POST['lead_id'], 'alert_uw_reject_customer', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);

				$cus_alert_ids = ['A1675'];
				$cus_data['alerts'] = [];

				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['lan_id'];
				$cus_data['alerts'][] = $_POST['reject_reason'];
				$cus_data['alerts'][] = $getTriggerData['trace_id'];

				$customer_response = triggerCommunication($cus_alert_ids, $cus_data);
				insert_application_log($_POST['lead_id'], 'alert_uw_reject_customer_2', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);


				//sm trigger
				$sm_alert_ids = ['A1671'];
				$sm_data['lead_id'] = $_POST['lead_id'];
				$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
				$sm_data['plan_id'] = $getTriggerData['plan_id'];
				$sm_data['email_id'] = $getTriggerData['sm_email'];

				$sm_data['alerts'][] = $getTriggerData['full_name'];
				$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$sm_data['alerts'][] = $getTriggerData['trace_id'];
				$sm_data['alerts'][] = $getTriggerData['full_name'];
				$sm_data['alerts'][] = $_POST['reject_reason'];
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = $getTriggerData['employee_full_name'];

				$sm_response = triggerCommunication($sm_alert_ids, $sm_data);
				insert_application_log($_POST['lead_id'], 'alert_uw_reject_sm', json_encode($sm_data), json_encode($sm_response), json_encode($_POST['login_user_id']));

				$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Lead rejected successfully.'), "Data" => $result));

				insert_application_log($_POST['lead_id'], 'uw_reject', json_encode($_POST), $response, $_POST['login_user_id']);
// echo json_encode($response);exit;
				echo $response;
				exit;
			} else {

				$response = json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				insert_application_log($_POST['lead_id'], 'uw_reject', json_encode($_POST), $response, $_POST['login_user_id']);

				echo $response;
				exit;
			}
		} else {

			insert_application_log($_POST['lead_id'], 'uw_reject', json_encode($_POST), $checkToken, $_POST['login_user_id']);

			echo $checkToken;
			exit;
		}
	}

	//For CO proposal listing
	function coProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->coProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get Co Listing in excel
	function getproposalpolicybylead()
	{
		//echo "<pre>";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getproposalpaymentdetailsbylead($_POST['leads']);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For uw proposal listing
	function uwProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->uwProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For assignment declaration listing
	function assignmentDeclarationListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->assignmentDeclarationListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Add Edit Assignment Declaration
	function addEditAssignmentDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['created_at'] = date("Y-m-d H:i:s");

			if (!empty($_POST['assignment_declaration_id'])) {
				$result = $this->apimodel->updateRecord('assignment_declaration', $data, "assignment_declaration_id='" . $_POST['assignment_declaration_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('assignment_declaration', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get get assignment declaration
	function getAssignmentDeclarationDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getAssignmentDeclarationDetails($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete assignment declaration
	function delAssignmentDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['is_active'] = 0;
			$result = $this->apimodel->updateRecord('assignment_declaration', $data, "assignment_declaration_id ='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For ghd declaration listing
	function ghdDeclarationListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->ghdDeclarationListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get get GHD declaration
	function getGHDDeclarationDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getGHDDeclarationDetails($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit GHD Declaration
	function addEditGHDDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['created_at'] = date("Y-m-d H:i:s");

			if (!empty($_POST['declaration_id'])) {
				$result = $this->apimodel->updateRecord('ghd_declaration', $data, "declaration_id='" . $_POST['declaration_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('ghd_declaration', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete GHD declaration
	function delGHDDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['is_active'] = 0;
			$result = $this->apimodel->updateRecord('ghd_declaration', $data, "declaration_id ='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//multi images uploader
	function getImages()
	{

		//echo "<pre>";print_r($_FILES);exit;

	}

	//Co Excel Upload

	function uploadcoexcel()
	{
		$data = array();
		$this->load->library('excel');

		//$path = $_POST['path'];
		//echo "path: ".$path;exit;

		$path = DOC_ROOT . 'assets' . DIRECTORY_SEPARATOR . 'coimportexcel';

		if (!file_exists($path)) {

			mkdir($path, 0777, true);
		}

		$path_info = pathinfo($_FILES['path']['name']);
		$ext = $path_info['extension'];
		$temp_name = $_FILES['path']['tmp_name'];
		$path_filename_ext = $path."/".$_FILES['path']['name'].".".$ext;

		if (file_exists($path_filename_ext)) {
			unlink($path_filename_ext);
		}

		move_uploaded_file($temp_name,$path_filename_ext);
		
		//move_uploaded_file($_FILES['path']['tmp_name'], $path);

		$object = PHPExcel_IOFactory::load($path_filename_ext);
		//Get only the Cell Collection
		$sheetData = $object->getActiveSheet()->toArray(null, false, false, true);
		//echo "data: ".$sheetData[2]['E'];
		//echo "<pre>eee";print_r($sheetData);exit;

		//Check Amount should not less than premium
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			//echo $highestRow;exit;
			for ($row = 2; $row <= $highestRow; $row++) {

				$trace_id_val = $worksheet->getCellByColumnAndRow(1, $row)->getValue();

				$premium_val = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

				$ref_no_val = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$amount_val = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				$payment_date_val = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				$remark_val = $worksheet->getCellByColumnAndRow(11, $row)->getValue();

				//echo $trace_id_val;exit;
				//echo $payment_date_val;exit;


				if (empty($ref_no_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Reference No. value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if (empty($amount_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Amount value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if (empty($payment_date_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Payment date value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if (empty($remark_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Remark value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if ($amount_val < $premium_val) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Amount value should not less than premium at row no. ' . $row), "Data" => NULL));
					exit;
				}
			}
		}

		//exit;

		$lead_id = array();
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			//echo $highestRow;exit;
			$highestColumn = $worksheet->getHighestColumn();
			for ($row = 2; $row <= $highestRow; $row++) {

				//$proposal_policy_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				$trace_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				//echo $trace_id;exit;
				$hb_receipt_number = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				$reference_no = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$amount = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				$payment_date = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				//echo $payment_date;
				$payment_date = str_replace('/', '-', $payment_date);
				//$payment_date_val = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($payment_date));
				$payment_date_val = date("Y-m-d", strtotime($payment_date));
				//echo $payment_date_val;exit;
				//$status = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				$remark = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				//echo $payment_date;exit;
				if (!empty($trace_id)) {
					//array('transaction_date'=>$payment_date,'hb_receipt_number'=>$hb_receipt_number,'payment_remark'=>$remark,'transaction_number'=>$reference_no)
					$data = array();
					$data['transaction_date'] = $payment_date_val;
					$data['payment_date'] = $payment_date_val;
					//echo $data['transaction_date'];exit;
					$data['hb_receipt_no'] = $hb_receipt_number;
					$data['remark'] = $remark;
					$data['transaction_number'] = $reference_no;
					$data['payment_status'] = 'Success';
					$data['updated_at'] = date("Y-m-d H:i:s");
					$data['updated_by'] = $_POST['login_user_id'];

					$result = $this->apimodel->updateRecord('proposal_payment_details', $data, "trace_id ='" . $trace_id . "' ");

					//Get lead id from trace id.
					$getLeadID = $this->apimodel->getSortedData("lead_id, plan_id,creditor_id", "lead_details", "trace_id='" . $trace_id . "' ", "lead_id", "asc");
					//echo "<pre>";print_r($getLeadID);
					//echo $getLeadID[0]->lead_id;
					//exit;

					//get trigger data
					$getTriggerData = $this->db->query(" select l.lan_id, l.plan_id, l.lead_id, l.trace_id, c.first_name, c.last_name, c.full_name, c.mobile_no as customer_mobile, c.email_id as customer_email, e.employee_fname, e.employee_lname, e.employee_full_name, e.mobile_number as sm_mobile, e.email_id as sm_email, p.plan_name, cr.creaditor_name from lead_details as l, master_customer as c, master_employee as e, master_plan as p, master_ceditors as cr where l.lead_id='".$getLeadID[0]->lead_id."' and l.primary_customer_id = c.customer_id and l.createdby = e.employee_id and l.plan_id = p.plan_id and p.creditor_id = cr.creditor_id " )->row_array();

					//check UW condition
					$uwCheck = checkUWCase($getLeadID[0]->lead_id, $getLeadID[0]->creditor_id, $getLeadID[0]->plan_id);
					if($uwCheck){
						//move lead to UW
						$leaddata = array();
						$leaddata['status'] = 'UW-Approval-Awaiting';
						$leaddata['updatedby'] = $_POST['login_user_id'];
						$this->apimodel->updateRecord('lead_details', $leaddata, "trace_id='" . $trace_id . "' ");

						//Trigger move to UW
						//customer trigger
						$cus_alert_ids = ['A1666'];
						$cus_data['lead_id'] = $getLeadID[0]->lead_id;
						$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
						$cus_data['plan_id'] = $getTriggerData['plan_id'];
						$cus_data['email_id'] = $getTriggerData['customer_email'];

						$cus_data['alerts'][] = $getTriggerData['full_name'];
						$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
						$cus_data['alerts'][] = "4";
						$cus_data['alerts'][] = $getTriggerData['trace_id'];

						$customer_response = triggerCommunication($cus_alert_ids, $cus_data);

						insert_application_log($cus_data['lead_id'], 'uw_bucket_movement_co_upload_customer', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);

						//sm trigger
						$sm_alert_ids = ['A1667'];
						$sm_data['lead_id'] = $getLeadID[0]->lead_id;
						$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
						$sm_data['plan_id'] = $getTriggerData['plan_id'];
						$sm_data['email_id'] = $getTriggerData['sm_email'];

						$sm_data['alerts'][] = $getTriggerData['full_name'];
						$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
						$sm_data['alerts'][] = $getTriggerData['employee_full_name'];
						$sm_data['alerts'][] = $getTriggerData['trace_id'];
						$sm_data['alerts'][] = "4";
						

						$sm_response = triggerCommunication($sm_alert_ids, $sm_data);

						insert_application_log($sm_data['lead_id'], 'uw_bucket_movement_co_upload_agent', json_encode($sm_data), json_encode($sm_response), $_POST['login_user_id']);

						//uw_trigger

						$uw_user_data = $this->apimodel->getdata("master_employee", "employee_fname,employee_lname, employee_full_name, mobile_number, email_id", "role_id = 7");
						
						$uw_alert_ids = ['A1673'];
						$uw_data['lead_id'] = $getLeadID[0]->lead_id;
						$uw_data['plan_id'] = $getTriggerData['plan_id'];
						$uw_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
						$uw_data['alerts'][] = $getTriggerData['creaditor_name'];
						$uw_data['alerts'][] = $getTriggerData['lan_id'];
						$uw_data['alerts'][] = date('d-m-Y');
						$uw_data['alerts'][] = ''; //remarks goes here

						foreach($uw_user_data as $uw_user){

							$uw_data['mobile_no'] = $uw_user['mobile_number'];
							$uw_data['email_id'] = $uw_user['email_id'];

							$uw_response = triggerCommunication($uw_alert_ids, $uw_data);

							insert_application_log($getLeadID[0]->lead_id, 'uw_bucket_movement_acceptance_uw', json_encode($uw_data), json_encode($uw_response), $_POST['login_user_id']);
						}

					}else{
						//Updating Lead entry
						$leaddata = array();
						$leaddata['status'] = 'Customer-Payment-Received'; //'Approved';
						$leaddata['updatedby'] = $_POST['login_user_id'];
						$this->apimodel->updateRecord('lead_details', $leaddata, "trace_id='" . $trace_id . "' ");
						
						//echo "<pre>";print_r($getLeadID);exit;
						$lead_id[] = $getLeadID[0]->lead_id;
					}
					
				}
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Records updated successfully.'), "Data" => $lead_id));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		}
	}


	//Get Discrepancy Type
	function getDiscrepancyTypeData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("discrepancy_type_id,discrepancy_type", "discrepancy_type", "", "discrepancy_type", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get Discrepancy Subtype
	function getDiscrepancySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("discrepancy_subtype_id,discrepancy_subtype", "discrepancy_subtype", "discrepancy_type_id='" . $_POST['discrepancy_type_id'] . "' ", "discrepancy_subtype", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Discrepancy Type listing
	function discrepancyTypeListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->discrepancyTypeListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get discrepancy type form data
	function getDiscrepancyTypeFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getDiscrepancyTypeFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate discrepancy type
	function checkDuplicateDiscrepancyType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "discrepancy_type='" . $_POST['discrepancy_type'] . "' ";
			if (!empty($_POST['discrepancy_type_id'])) {
				$condition .= " && discrepancy_type_id !='" . $_POST['discrepancy_type_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("discrepancy_type", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit discrepancy type
	function addEditDiscrepancyType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['discrepancy_type'] = (!empty($_POST['discrepancy_type'])) ? $_POST['discrepancy_type'] : '';
			if (empty($_POST['discrepancy_type_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				//$data['updated_by'] = $_POST['login_user_id'];
			} else {
				//$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['discrepancy_type_id'])) {
				$result = $this->apimodel->updateRecord('discrepancy_type', $data, "discrepancy_type_id='" . $_POST['discrepancy_type_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('discrepancy_type', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Discrepancy SubType listing
	function discrepancySubTypeListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->discrepancySubTypeListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get discrepancy subtype form data
	function getDiscrepancySubTypeFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getDiscrepancySubTypeFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate discrepancy subtype
	function checkDuplicateDiscrepancySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "discrepancy_subtype='" . $_POST['discrepancy_subtype'] . "' ";
			if (!empty($_POST['discrepancy_subtype_id'])) {
				$condition .= " && discrepancy_subtype_id !='" . $_POST['discrepancy_subtype_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("discrepancy_subtype", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit discrepancy subtype
	function addEditDiscrepancySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['discrepancy_type_id'] = (!empty($_POST['discrepancy_type_id'])) ? $_POST['discrepancy_type_id'] : '';
			$data['discrepancy_subtype'] = (!empty($_POST['discrepancy_subtype'])) ? $_POST['discrepancy_subtype'] : '';
			if (empty($_POST['discrepancy_subtype_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				//$data['updated_by'] = $_POST['login_user_id'];
			} else {
				//$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['discrepancy_subtype_id'])) {
				$result = $this->apimodel->updateRecord('discrepancy_subtype', $data, "discrepancy_subtype_id='" . $_POST['discrepancy_subtype_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('discrepancy_subtype', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Sale Admin Dashboard
	function saleAdminDashBorad()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->saleAdminDashBorad($_POST);
			//echo "<pre>";print_r($get_result);
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Sale Admin Dashboard Export
	function exportSaleAdminDashBorad()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->exportSaleAdminDashBorad($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Sale Admin Dashboard Details
	function adminDashBoradDetails()
	{
		//echo "<pre>";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->adminDashBoradDetails($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Dashboard Details Export
	function exportDashBoradDetails(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportDashBoradDetails($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//For SM Dashboard
	function smDashBorad()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->smDashBorad($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	
	//For SM Dashboard Export
	function exportSMDashBorad(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportSMDashBorad($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//Get SM locations
	function getLogActions()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLogActions();
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Application Logs
	function applicationLogs()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->applicationLogs($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	
	//For Application Export
	function exportApplicationLogs(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportApplicationLogs($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//For Enrollment Form listing
	function enrollmentformListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getEnrollmentFormList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get enrollment form data
	function getEnrollmentFormsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getEnrollmentFormsData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit enrollment form
	function addEditEnrollmentForm()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['form_title'] = (!empty($_POST['form_title'])) ? $_POST['form_title'] : '';
			$data['form_file'] = (!empty($_POST['form_file'])) ? $_POST['form_file'] : '';
			if (empty($_POST['enrollmentforms_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['enrollmentforms_id'])) {
				$result = $this->apimodel->updateRecord('enrollmentforms', $data, "enrollmentforms_id='" . $_POST['enrollmentforms_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('enrollmentforms', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For company listing
	function companyListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCompanyList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get company form data
	function getCompanyFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCompanyFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate company
	function checkDuplicateCompany()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "company_name='" . $_POST['company_name'] . "' ";
			if (!empty($_POST['company_id'])) {
				$condition .= " && company_id !='" . $_POST['company_id'] . "' ";
			}
			//echo $condition;
			//exit;
			$get_result = $this->apimodel->getdata("master_company", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit company
	function addEditCompany()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['company_id'] = (!empty($_POST['company_id'])) ? $_POST['company_id'] : '';
			$data['company_name'] = (!empty($_POST['company_name'])) ? $_POST['company_name'] : '';
			$data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
			

			if (!empty($_POST['company_id'])) {
				$result = $this->apimodel->updateRecord('master_company', $data, "company_id='" . $_POST['company_id'] . "' ");
			} else {
				$data['created_on'] = date("Y-m-d H:i:s");
				$result = $this->apimodel->insertData('master_company', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get companies
	function getCompanyData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("company_id,company_name", "master_company", "", "company_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Import User
	function importUsers()
	{
		$data = array();
		$this->load->library('excel');

		//echo "<pre>";print_r($_FILES);exit;
		//Read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($_FILES['import_file']['tmp_name']);

		//Get only the Cell Collection
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,false,false,true);
		//echo "<pre>";print_r($sheetData);exit;
		if(array_key_exists(2,$sheetData)){
			$errorArr = array();
			for($i = 2; $i < count($sheetData)+1; $i++){
				//echo $sheetData[$i]['A'];
				if(empty($sheetData[$i]['A'])){
					$errorArr[] = "First name value is blank at line no.A".$i."<br/>";
				}

				if(empty($sheetData[$i]['C'])){
					$errorArr[] = "Last name value is blank at line no.C".$i."<br/>";
				}

				if(empty($sheetData[$i]['D'])){
					$errorArr[] = "Employee code value is blank at line no.D".$i."<br/>";
				}

				if(empty($sheetData[$i]['E'])){
					$errorArr[] = "Email ID value is blank at line no.E".$i."<br/>";
				}

				if(empty($sheetData[$i]['F'])){
					$errorArr[] = "Mobile NO. value is blank at line no.F".$i."<br/>";
				}

				if(empty($sheetData[$i]['G'])){
					$errorArr[] = "Username value is blank at line no.G".$i."<br/>";
				}

				//Check duplicate user from email and mobile number
				$checkUserDup = $this->apimodel->getdata("master_employee", "employee_id"," (email_id='".$sheetData[$i]['E']."' || mobile_number='".$sheetData[$i]['F']."' ) ");

				if(!empty($checkUserDup)){
					$errorArr[] = "User Email ID OR Mobile No. already present at line no.".$i."<br/>";
				}

				//Check duplicate username
				$checkUsername = $this->apimodel->getdata("master_employee", "employee_id","user_name='".$sheetData[$i]['G']."' ");

				if(!empty($checkUsername)){
					$errorArr[] = "Duplicate Username ".$sheetData[$i]['G']. " at line no.G".$i."<br/>";
				}

				if(empty($sheetData[$i]['H'])){
					$errorArr[] = "Joining Date value is blank at line no.H".$i."<br/>";
				}

				if(empty($sheetData[$i]['I'])){
					$errorArr[] = "Role value is blank at line no.I".$i."<br/>";
				}

				//check role present or not if not then create and assign to user.
				$checkRole = $this->apimodel->getdata("roles", "role_id","role_name='".$sheetData[$i]['I']."' ");
				//$role_id = "";
				if(!empty($checkRole)){
					//echo $checkRole[0]['role_id'];
					$role_id = $checkRole[0]['role_id'];
				}else{
					//Create new role
					$role_data = array();
					$role_data['role_name'] = $sheetData[$i]['I'];
					$role_id = $this->apimodel->insertData('roles', $role_data, '1');
					$role_perm = array(1,6,22);
					
					for ($r = 0; $r < sizeof($role_perm); $r++) {
						//echo $role_perm[$r];
						$perm_data = array();
						$perm_data['role_id'] = $role_id;
						$perm_data['perm_id'] = $role_perm[$r];
						$rs = $this->apimodel->insertData('role_perm', $perm_data, '1');
						
					}
				}

				//check company present or not if not then create and assign to user.
				$checkCompany = $this->apimodel->getdata("master_company", "company_id","company_name='".$sheetData[$i]['J']."' ");
				if(!empty($checkCompany)){
					//echo $checkCompany[0]['company_id'];
					$company_id = $checkCompany[0]['company_id'];
				}else{
					//Create New Company
					$company_data = array();
					$company_data['company_name'] = $sheetData[$i]['J'];
					$company_id = $this->apimodel->insertData('master_company', $company_data, '1');
					
				}

				$data = array();
				$data['employee_fname'] = (!empty($sheetData[$i]['A'])) ? $sheetData[$i]['A'] : '';
				$data['employee_mname'] = (!empty($sheetData[$i]['B'])) ? $sheetData[$i]['B'] : '';
				$data['employee_lname'] = (!empty($sheetData[$i]['C'])) ? $sheetData[$i]['C'] : '';
				$data['employee_full_name'] = $data['employee_fname']." ".$data['employee_mname']." ".$data['employee_lname'];
				$data['employee_code'] = (!empty($sheetData[$i]['D'])) ? $sheetData[$i]['D'] : '';
				$data['date_of_joining'] = (!empty($sheetData[$i]['H'])) ? date("Y-m-d",strtotime($sheetData[$i]['H'])) : '';
				$data['email_id'] = (!empty($sheetData[$i]['E'])) ? $sheetData[$i]['E'] : '';
				$data['mobile_number'] = (!empty($sheetData[$i]['F'])) ? $sheetData[$i]['F'] : '';
				$data['user_name'] = (!empty($sheetData[$i]['G'])) ? $sheetData[$i]['G'] : '';
				$data['employee_password'] = md5(123456);
				$data['role_id'] = (!empty($role_id)) ? $role_id : '';
				$data['company_id'] = (!empty($company_id)) ? $company_id : '';
				$data['isactive'] = '1';
				$data['createdon'] = date("Y-m-d H:i:s");

				//Insert New User
				$user_id = $this->apimodel->insertData('master_employee', $data, '1');

				if(!empty($user_id)){
					//check location if SM user.
					if(!empty($sheetData[$i]['K'])){
						$locations = explode(",", $sheetData[$i]['K']);
						if(!empty($locations)){
							for ($loc = 0; $loc < sizeof($locations); $loc++) {
								//check already present or not
								$checkLocation = $this->apimodel->getdata("master_location", "location_id","location_name='".$locations[$loc]."' ");
								$location_id = "";
								if(!empty($checkLocation)){
									//echo $checkLocation[0]['location_id'];
									$location_id = $checkLocation[0]['location_id'];
								}else{
									//Create new location
									$location_data = array();
									$location_data['location_name'] = $locations[$loc];
									$location_id = $this->apimodel->insertData('master_location', $location_data, '1');
									
								}

								//insert user location
								$user_locations = array();
								$user_locations['user_id'] = $user_id;
								$user_locations['location_id'] = $location_id;
								$userlocation_id = $this->apimodel->insertData('user_locations', $user_locations, '1');
								
							}
						}
					}
				}

			}

			if(!empty($errorArr)){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Error in input file. '), "Data" => $errorArr));
				exit;
			}else{
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Excel File Imported Successfully.. '), "Data" => NULL));
				exit;
			}

		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Empty file cannot be imported. '), "Data" => NULL));
			exit;
		}

		
		
	}

	//For payment work flow listing
	function paymentworkflowmasterListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getpaymentworkflowmasterList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get payment workflow master form data
	function getPaymentWorkflowFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPaymentWorkflowFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate payment workflow
	function checkDuplicatePaymentWorkflow()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "workflow_name='" . $_POST['workflow_name'] . "' ";
			if (!empty($_POST['payment_workflow_master_id'])) {
				$condition .= " && payment_workflow_master_id !='" . $_POST['payment_workflow_master_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("payment_workflow_master", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit payment workflow
	function addEditPaymentWorkFlow()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['workflow_name'] = (!empty($_POST['workflow_name'])) ? $_POST['workflow_name'] : '';
			if (empty($_POST['perm_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
				$data['updated_on'] = date("Y-m-d H:i:s");
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
				$data['updated_on'] = date("Y-m-d H:i:s");
			}

			if (!empty($_POST['payment_workflow_master_id'])) {
				$result = $this->apimodel->updateRecord('payment_workflow_master', $data, "payment_workflow_master_id='" . $_POST['payment_workflow_master_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('payment_workflow_master', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get lead COI numbers
	function getCOINumbers()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("certificate_number", "api_proposal_response", "lead_id='" . $_POST['lead_id'] . "' ", "pr_api_id", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//get COI's
	function getProposalCOI()
	{
		//Danish: get COI with other details to display on thankyou page
		$coi_data = $this->db->query("SELECT ar.certificate_number,ar.lead_id, s.code, pm.policy_member_first_name, pm.policy_member_last_name,f.member_type, l.trace_id
		FROM api_proposal_response AS ar, master_policy_sub_type AS s, proposal_policy_member_details AS pm,family_construct AS f, lead_details AS l
		WHERE ar.lead_id = '" . $_POST['lead_id'] . "' AND ar.policy_sub_type_id=s.policy_sub_type_id 
		AND (ar.customer_id = pm.customer_id AND ar.lead_id=pm.lead_id) AND pm.relation_with_proposal=f.id AND ar.lead_id = l.lead_id" )->result();

		//echo "<pre>";print_r($get_result);exit;
		if (!empty($coi_data)) {
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $coi_data));
			exit;
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
			exit;
		}
	}

	//For Sale Admin Dashboard Export
	function exportSMCreditors()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->exportSMCreditors($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get customer table enum values
	function getCustomerEnumValues()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$result = array();
			$result['get_customer_salutation'] = $this->apimodel->getEnumValues('master_customer', 'salutation');

			$result['get_customer_gender'] = $this->apimodel->getEnumValues('master_customer', 'gender');

			//echo "<pre>";print_r($result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $result));
			exit;
		} else {
			echo $checkToken;
		}
	}


	function checkUWCaseTest(){
		var_dump(checkUWCase(138,11,406));
	}

	//Test Api
	function testApi()
	{
		$send_message = send_message("8149212749", $mail_to = "", $mail_cc = "", $mail_bcc = "", $data = array(), "sendOTP");

		echo "<pre>";
		print_r($send_message);
		exit;
	}

    function lodgeclaimListing()
    {
        //echo "<pre>post";print_r($_POST);exit;
        $checkToken = $this->verify_request($_POST['utoken']);

        if (!empty($checkToken->username)) {
            $get_result = $this->apimodel->getlodgeclaimList($_POST);
           //  echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
            exit;
        } else {
            echo $checkToken;
        }
    }
    function trackclaimListing()
    {
        //echo "<pre>post";print_r($_POST);exit;
        $checkToken = $this->verify_request($_POST['utoken']);

        if (!empty($checkToken->username)) {
            $get_result = $this->apimodel->gettrackclaimList($_POST);
            //  echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
            exit;
        } else {
            echo $checkToken;
        }
    }
    function getraisebulkdata()
    {
        //echo "<pre>post";print_r($_POST);exit;
        $checkToken = $this->verify_request($_POST['utoken']);

        if (!empty($checkToken->username)) {
            $get_result = $this->apimodel->getraisebulkdata($_POST);
             // echo "<pre>";print_r($get_result);exit;
            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
            exit;
        } else {
            echo $checkToken;
        }
    }
	function singlejourneyListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getsinglejourneyList($_POST);
			// echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}


	function commstemplateListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCommstemplateList($_POST);
			// echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function getCreditorsDetails(){
	//	$res = $this->db->get_where("master_ceditors",["isactive" => 1])->result_array();
        $res=$this->db->query("select creditor_id,creaditor_name from master_ceditors where isactive=1 order by creaditor_name")->result();
		echo json_encode($res);
	}

	function addEditCommsTemplateJourney()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['id'] = (!empty($_POST['id'])) ? $_POST['id'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
			
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
			$data['type'] = (!empty($_POST['type'])) ? $_POST['type'] : '';
			$data['subject'] = (!empty($_POST['subject'])) ? $_POST['subject'] : '';
			$data['dropout_event'] = (!empty($_POST['dropout_event'])) ? $_POST['dropout_event'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			
			// echo json_encode($data);exit;
			if (!empty($_POST['id'])) {
				$result = $this->apimodel->updateRecord('master_communication_templates', $data, "id='" . $_POST['id'] . "' ");
			} else {
				// echo json_encode(1212);exit;
				//$data['created_at'] = date("Y-m-d H:i:s");
				$result = $this->apimodel->insertData('master_communication_templates', $data, 1);
				// echo json_encode($this->db->last_query());exit;
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function delTemplate(){

		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			
			$result = $this->apimodel->delrecord('master_communication_templates', 'id', $_POST['id']);
			if ($result) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record deleted successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getCommsTemplateData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCommsTemplateData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getDropoutEvents(){
		$res=$this->db->query("select id,name from master_communication_events where isactive=1")->result();
		echo json_encode($res);

	}



	function checkDuplicateSingleJourney()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "creditor_id='" . $_POST['creditor_id'] . "' ";
			if (!empty($_POST['id'])) {
				$condition .= " && id !='" . $_POST['id'] . "' ";
			}
			//echo $condition;
			//exit;
			$get_result = $this->apimodel->getdata("master_single_journey", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addEditSingleJourney()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['id'] = (!empty($_POST['id'])) ? $_POST['id'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
			$data['URL'] = (!empty($_POST['creditor_id'])) ? 'partner='.encrypt_decrypt_password($_POST['creditor_id']) : '';
			$data['is_active'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
			
			// echo json_encode($data);exit;
			if (!empty($_POST['id'])) {
				$result = $this->apimodel->updateRecord('master_single_journey', $data, "id='" . $_POST['id'] . "' ");
			} else {
				// echo json_encode(1212);exit;
				$data['created_at'] = date("Y-m-d H:i:s");
				$result = $this->apimodel->insertData('master_single_journey', $data, 1);
				// echo json_encode($this->db->last_query());exit;
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getSingleJourneyData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSingleJourneyData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}
    function generateToken(){


            $api_data = json_decode(file_get_contents('php://input') , true);
            if(!empty($api_data)){
              $_POST=$api_data;
            }else{
                //$_POST=$_POST;
                $_POST=json_decode($_POST['json_cust_data'],true);
            }
      //  print_r($_POST);die;

        if (empty($_POST['username'])) {
            echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter username."), "Data" => NULL));
            exit;
        }
        $type=1;
        if (isset($_POST['type'])) {
            $type=$_POST['type'];
        }

        if (!empty($_POST['password'])) {
            $password = encrypt_decrypt_password($_POST['password'],"E");
        } else {
            echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter password."), "Data" => NULL));
            exit;
        }
        $condition = "i.user_name='" . $_POST['username'] . "' &&  i.employee_password='" . $password . "' ";

        $result_login = $this->apimodel->login_check($condition);
        $result_data = $result_login[0];
        $token_data = array();
        if (is_array($result_login) && count($result_login) > 0) {
            $date = new DateTime();
            $tokenData = array('username' => $result_data['employee_id'], 'iat' => $date->getTimestamp(), 'exp' => $date->getTimestamp() + 60 * 60 * 5);
            $token = AUTHORIZATION::generateToken($tokenData);
            $token_data['utoken'] = $token;
            $token_data['user_id'] = $result_data['employee_id'];

            //inserting last login
            $last_login = array();
            $last_login['last_login'] = date("Y-m-d H:i:s");
            $this->apimodel->updateRecord('master_employee', $last_login, "employee_id='" . $result_data['employee_id'] . "' ");
            //token_table
            $where=array('emp_id'=>$_POST['username'],"type"=>$type);
            $this->db->delete('token_table',$where);
            $arraY_data=array(
                "emp_id"=>$_POST['username'],
                "token"=>$token,
                "type"=>$type,
            );
            $this->db->insert('token_table',$arraY_data);
            echo json_encode($token_data);
            // echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => "Token Generated!"), "Data" => $token_data));
            exit;
        } else {
            echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Incorrect Username or Password."), "Data" => NULL));
            exit;
        }
    }
    public function proposalSummaryPost()
    {
        /*$result['generated_quote'] = json_decode(curlFunction(SERVICE_URL . '/api2/getGeneratedQuote', [
            'customer_id' => '',
            'lead_id' => ''
        ]));*/
        $_POST = json_decode(file_get_contents('php://input') , true);

        if ((!empty($_POST['lead_id']) && isset($_POST['lead_id'])) ||
            (!empty($_POST['token']) && isset($_POST['token']))) {

            $record_id = $_POST['lead_id'];
            $token = $_POST['token'];
        }else{
            echo json_encode(array("status_code" => "201", "Metadata" => array("Message" => 'Lead id Or Token missing'), "Data" =>''));
            exit;
        }

        $result = array();

        $data = array();
        $data['lead_id'] = $record_id;
        $data['utoken'] = $token;

        $result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);
// echo "<PRE>";print_r($result);exit;
        $customers = reset($result['customer_details']);

        foreach ($customers as $customer) {

            $requestData = [
                'customer_id' => $customer['customer_id'],
                'lead_id' => $record_id
            ];

            $mode = 'preview';
            $members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
            $questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
            $answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

            $customer_id = $customer['customer_id'];
            $html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
            $result['ghd_declaration'][$customer['customer_id']] = $html;
        }

        $result['lead_id_enc'] = (isset($_GET['text'])) ? $_GET['text'] : '';
        //echo "<pre>";print_r($result);exit;
        $this->load->view('template/header.php');
        $this->load->view('policyproposal/proposalSummary', $result);
        $this->load->view('template/footer.php');
    }
    function getSingleLinkData(){
        $lead_id=$this->input->post('lead_id');
        $query=$this->db->query("select l.loan_amt,l.lan_id,l.loan_tenure,l.loan_disbursement_date,l.trace_id,l.lead_id,mc.pincode,mc.no_of_lives,mc.first_name,mc.last_name,mc.email_id,mc.mobile_no,
mc.address_line1,mc.address_line2,mc.address_line3,pmpd.cover as cover ,ppd.transaction_number,ppd.premium as trans_amount,
pp.master_policy_id,pp.proposal_no,pp.premium_amount,ppd.payment_status,apr.certificate_number,apr.end_date,apr.start_date,
(select plan_name from master_plan mpl where mpl.plan_id=mp.plan_id ) as plan_name,
(select policy_sub_type_name from master_policy_sub_type mpst where mpst.policy_sub_type_id=mp.policy_sub_type_id ) as policy_subtype
from lead_details l 
join master_customer mc on mc.lead_id=l.lead_id
join proposal_policy pp on pp.lead_id=l.lead_id
join master_policy mp on mp.policy_id=pp.master_policy_id
join proposal_payment_details ppd on ppd.lead_id=l.lead_id
join policy_member_plan_details pmpd on pmpd.lead_id=l.lead_id
join api_proposal_response apr on apr.lead_id=l.lead_id
where l.lead_id=".$lead_id." group by pp.master_policy_id")->result();
        if(count($query) > 0){
            echo json_encode($query);
        }else{
            echo json_encode(false);
        }

    }
}
