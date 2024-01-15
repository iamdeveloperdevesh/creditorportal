<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->RolePermission = getRolePermissions();
		
		if(!empty($_SESSION["webpanel"]))
		{
			redirect('home/index', 'refresh');
		}
		
	}
 
	function index()
	{
        $themeConfig = $this->db->select("logo_url")->where(["theme_for" => 1])->get("theme_configuaration")->row_array();


		$this->load->view('template/login_header.php',['data'=>$themeConfig]);
		$this->load->view('login');
		$this->load->view('template/login_footer.php');
	}
    public function setCustomerSession($telesalesSession)
    {
        $this->load->library('session');

        //unset previous user data from session.
        if ($this->session->userdata('telesales_session')) {
            $this->session->unset_userdata('telesales_session');
        }

        //set new user data in session.
        $this->session->set_userdata($telesalesSession);
        /*Regenerate a new session upon successful authentication. Any session token used prior to
        login should be discarded and only the new token should be assigned for the user till the user
        logs out.
        This session token should be properly expired when the user logs out.*/
        $this->session->sess_regenerate();
        $session_id = session_id();
        $telesalesSession = $this->session->userdata('telesales_session');
        $agent_id = encrypt_decrypt_password($telesalesSession['agent_id'], 'D');
        $rsEmp = $this->db->select("id, updated_time")->where(["agent_id" => $agent_id])->get("tls_agent_session");
        if ($rsEmp->num_rows() > 0) {
            //update record
            $aRow = $rsEmp->row();
            $id = $aRow->id;

            $data = array(
                'sessionid' => $session_id,
                'updated_time' => time(),
            );
            $this->db->where('id', $id);
            $this->db->update('tls_agent_session', $data);
        } else {
            $aAgentSession = ["agent_id" => $agent_id, "sessionid" => $session_id, "updated_time" => time()];
            $this->db->insert("tls_agent_session", $aAgentSession);
        }
    }
    function check_accountDisabled($emp_id,$agent_type){
        $lastLogin = $this->db
            ->where(["emp_id" => $emp_id, "agent_type" => $agent_type])
            ->order_by('id','desc')
            ->get('login_verification');

        if ($lastLogin->num_rows() > 0) {
            $alastLogin= $lastLogin->row_array();
            $lastLginDate=$alastLogin['last_login_at'];
            if($agent_type == 1){
                $daysallow=30;
            }else{
                $daysallow=45;
            }

            $now = time(); // or your date as well
            $your_date = strtotime(date('Y-m-d',strtotime($lastLginDate)));
            $datediff = $now - $your_date;
            $Diffdays= round($datediff / (60 * 60 * 24));

            if($Diffdays > $daysallow){
                $aResponse['show_captcha'] = 0;
                $aResponse['msg'] ='Account has been disabled due to prolonged inactivity. Please mail Admin to activate your ID';
                return $aResponse;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function get_login_details()
    {
        $this->db = $this->load->database('telesales_fyntune',TRUE);


       //echo 123;die;
        //   echo encrypt_decrypt_password('ZDIvdVhlZGw5czliY0lKWjhFVFhrdz09=','D');die;
        //require_once APPPATH . 'libraries/encryption.php';

        // print_pre($_SESSION);
        extract($this->input->post(null, true));



        //print_r($this->input->post(null, true));exit;
        $aResponse['success'] = 0;
        $aResponse['msg'] = 'Incorrect Agent Code Or Password';
        //last login Date
        $emp_id = $agent_code;




        //updated by upendra - maker/checker - 30-07-2021
        if ($agent_type == 4) {

            $agent_pwd = encrypt_decrypt_password($agent_pwd);
            $rsAgent = $this->db
                ->where(['base_agent_id' => $agent_code, 'password' => $agent_pwd])
                ->get('tls_base_agent_tbl');

            if ($rsAgent->num_rows() > 0) {
                $aAgent = $rsAgent->row_array();
                //captcha check
                $emp_id = $agent_code;
                $login_attempts = $this->db
                    ->where(['emp_id' => $emp_id, 'agent_type' => $agent_type])
                    ->get('login_verification')
                    ->row_array();
                if ($login_attempts) {

                    $invalid_login_count = 0;
                    $is_blocked = 0;

                    $data = array(
                        'emp_id' => $emp_id,
                        'journey_stage' => "agent_login",
                        'agent_type' => $agent_type,
                        'invalid_login_count' => $invalid_login_count,
                        'is_blocked' => $is_blocked,
                        'captcha_status' => 'yes',
                        'captcha_verified' => 'yes'
                    );
                    $this->db->where(["emp_id" => $emp_id, "agent_type" => $agent_type])->update("login_verification", $data);
                }
                //captcha check ends



                $base_center_id =  $this->db->select("*")
                    ->from("tls_axis_location")
                    ->where(['axis_location' => $aAgent['center']])
                    ->get()
                    ->row_array();
                $base_center_id = $base_center_id['axis_loc_id'];
                $base_vendor_id =  $this->db->select("*")
                    ->from("tls_axis_vendor")
                    ->where(['axis_vendor' => $aAgent['vendor'], 'axis_loc_id' => $base_center_id])
                    ->get()
                    ->row_array();
                //  print_r($base_vendor_id);die;
                $base_vendor_id = $base_vendor_id['axis_vendor_id'];
                $base_lob_id  =  $this->db->select("*")
                    ->from("tls_axis_lob")
                    ->where(['axis_lob' => $aAgent['lob']])
                    ->get()
                    ->row_array();
                $base_lob_id = $base_lob_id['axis_lob_id'];


                $telSalesSession['telesales_session'] = ['agent_type' => $agent_type, 'agent_id' => encrypt_decrypt_password($aAgent['base_id']), 'agent_name' => $aAgent['base_agent_name'], 'outbound' => 3, 'is_admin' => 0, "is_maker_checker" => "yes", 'axis_process' => $aAgent['base_axis_process'], 'base_agent_id' => $aAgent['base_agent_id'], 'base_caller_name' => $aAgent['base_agent_name'], 'base_caller_location' => $base_center_id, 'base_caller_vendor' => $base_vendor_id, "base_caller_lob" => $base_lob_id, "base_tl_id" => $aAgent['tl_emp_id'], "base_tl_name" => $aAgent['tl_name'], "base_imd_code" => $aAgent['imd_code'], "is_redirect_allow" => "1"];
                $this->setCustomerSession($telSalesSession);

                // print_pre($telSalesSession);exit;

                $indexUrl = explode(",", $aAgent['module_access_rights'])[0];
                $resetPasswordUrl=55;
                $password_change_date=$aAgent['password_change_date'];
                if(empty($password_change_date) || is_null($password_change_date)){
                    $indexUrl=$resetPasswordUrl;
                }else{
                    $now = time(); // or your date as well
                    $your_date = strtotime($password_change_date);
                    $datediff = $now - $your_date;
                    $Diffdays= round($datediff / (60 * 60 * 24));
                    if($Diffdays > 30){
                        $indexUrl=$resetPasswordUrl;
                    }
                }
                // print_pre($_SESSION);exit;
                $aResponse['success'] = 1;
                $aResponse['msg'] = $this->db->where(["role_module_id" => $indexUrl])->get("role_access_module")->row()->url_link;
                http_response_code(200);
                $_SESSION['is_redirect_allow'] = '1';

                $newResponse = [
                    'success' => $Encryption->encrypt($aResponse['success'], $responseEncryption),
                    'msg' => $Encryption->encrypt($aResponse['msg'], $responseEncryption)
                ];
                $check=$this->check_accountDisabled($emp_id,$agent_type);
                if($check != false){
                    echo json_encode($check);
                    exit;
                }

                echo json_encode($aResponse);
                exit;
            }
        }

        //updated by upendra on 05-07-2021 for DO login
        if ($agent_type == 3) {

            $agent_pwd = encrypt_decrypt_password($agent_pwd);
            $rsAgent = $this->db
                ->where(['do_id' => $agent_code, 'password' => $agent_pwd, 'status' => 'Active'])
                ->get('tls_master_do');

            if ($rsAgent->num_rows() > 0) {
                $aAgent = $rsAgent->row_array();
                //captcha check
                $emp_id = $agent_code;
                $login_attempts = $this->db
                    ->where(['emp_id' => $emp_id, 'agent_type' => $agent_type])
                    ->get('login_verification')
                    ->row_array();
                if ($login_attempts) {

                    $invalid_login_count = 0;
                    $is_blocked = 0;

                    $data = array(
                        'emp_id' => $emp_id,
                        'journey_stage' => "agent_login",
                        'invalid_login_count' => $invalid_login_count,
                        'agent_type' => $agent_type,
                        'is_blocked' => $is_blocked,
                        'captcha_status' => 'yes',
                        'captcha_verified' => 'yes'

                    );
                    $this->db->where(["emp_id" => $emp_id, "agent_type" => $agent_type])->update("login_verification", $data);
                }
                //captcha check ends
                $telSalesSession['telesales_session'] = ['do_id' => $aAgent['id'], 'agent_type' => $agent_type, 'agent_id' => encrypt_decrypt_password($aAgent['id']), 'agent_name' => $aAgent['do_name'], 'outbound' => 2, 'is_admin' => 0, "is_redirect_allow" => "1"];
                $this->setCustomerSession($telSalesSession);

                // print_pre($telSalesSession);exit;

                $indexUrl = explode(",", $aAgent['module_access_rights'])[0];
                $resetPasswordUrl=55;
                $password_change_date=$aAgent['password_change_date'];
                if(empty($password_change_date) || is_null($password_change_date)){
                    $indexUrl=$resetPasswordUrl;
                }else{
                    $now = time(); // or your date as well
                    $your_date = strtotime($password_change_date);
                    $datediff = $now - $your_date;
                    $Diffdays= round($datediff / (60 * 60 * 24));
                    if($Diffdays > 30){
                        $indexUrl=$resetPasswordUrl;
                    }
                }

                // print_pre($_SESSION);exit;
                $aResponse['success'] = 1;
                $aResponse['msg'] = $this->db->where(["role_module_id" => $indexUrl])->get("role_access_module")->row()->url_link;
                http_response_code(200);
                $_SESSION['is_redirect_allow'] = '1';

                $newResponse = [
                    'success' => $Encryption->encrypt($aResponse['success'], $responseEncryption),
                    'msg' => $Encryption->encrypt($aResponse['msg'], $responseEncryption)
                ];
                $check=$this->check_accountDisabled($emp_id,$agent_type);
                if($check != false){
                    echo json_encode($check);
                    exit;
                }

                echo json_encode($aResponse);
                exit;
            }
        }
        //end - //updated by upendra on 05-07-2021 for DO login


        if ($agent_type == 2) {
//echo $agent_code;die;
            $agent_pwd = encrypt_decrypt_password($agent_pwd);
            $rsAgent = $this->db
                ->where(['agent_id' => $agent_code, 'password' => $agent_pwd, 'status' => 'Active'])
                ->get('tls_agent_mst_outbound');
            // var_dump($rsAgent->row_array());
            if ($rsAgent->num_rows() > 0) {
                $aAgent = $rsAgent->row_array();

                //captcha check
                $emp_id = $agent_code;
                $login_attempts = $this->db
                    ->where(['emp_id' => $emp_id, 'agent_type' => $agent_type])
                    ->get('login_verification')
                    ->row_array();
                if ($login_attempts) {

                    $invalid_login_count = 0;
                    $is_blocked = 0;

                    $data = array(
                        'emp_id' => $emp_id,
                        'journey_stage' => "agent_login",
                        'invalid_login_count' => $invalid_login_count,
                        'agent_type' => $agent_type,
                        'is_blocked' => $is_blocked,
                        'captcha_status' => 'yes',
                        'captcha_verified' => 'yes'
                    );
                    $this->db->where(["emp_id" => $emp_id, "agent_type" => $agent_type])->update("login_verification", $data);
                }
                //captcha check ends
                $telSalesSession['telesales_session'] = ['agent_type' => $agent_type, 'agent_id' => encrypt_decrypt_password($aAgent['id']), 'agent_name' => $aAgent['agent_name'], 'outbound' => 1, 'is_admin' => $aAgent['is_admin'], "is_redirect_allow" => "1"];
                $this->setCustomerSession($telSalesSession);


                $indexUrl = explode(",", $aAgent['module_access_rights'])[0];
                $resetPasswordUrl=55;
                $password_change_date=$aAgent['password_change_date'];
                if(empty($password_change_date) || is_null($password_change_date)){
                    $indexUrl=$resetPasswordUrl;
                }else{
                    $now = time(); // or your date as well
                    $your_date = strtotime($password_change_date);
                    $datediff = $now - $your_date;
                    $Diffdays= round($datediff / (60 * 60 * 24));
                    if($Diffdays > 30){
                        $indexUrl=$resetPasswordUrl;
                    }
                }
                // print_pre($_SESSION);exit;
                $aResponse['success'] = 1;
                $aResponse['msg'] = $this->db->where(["role_module_id" => $indexUrl])->get("role_access_module")->row()->url_link;
                http_response_code(200);
                $_SESSION['is_redirect_allow'] = '1';

                $newResponse = [
                    'success' => $Encryption->encrypt($aResponse['success'], $responseEncryption),
                    'msg' => $Encryption->encrypt($aResponse['msg'], $responseEncryption)
                ];
                $check=$this->check_accountDisabled($emp_id,$agent_type);
                if($check != false){
                    echo json_encode($check);
                    exit;
                }

                echo json_encode($aResponse);
                exit;
            }
        }
//echo $agent_pwd;die;
        $agent_pwd = encrypt_decrypt_password($agent_pwd,'D');
//        echo encrypt_decrypt_password('WUN5ek85T0tyVXpaUXJ4MUhzUDdidz09','D');die;
        $rsAgent = $this->db
            ->where(['agent_id' => $agent_code, 'password' => $agent_pwd, 'status' => 'Active'])
            ->where('DATE(license_to)>=DATE(NOW())')
            ->get('tls_agent_mst');
        //print_r($this->db->last_query());die;
        if ($rsAgent->num_rows() > 0) {
            $aAgent = $rsAgent->row_array();
            //captcha check
            $emp_id = $agent_code;
            $login_attempts = $this->db
                ->where(['emp_id' => $emp_id, 'agent_type' => $agent_type])
                ->get('login_verification')
                ->row_array();

            if ($login_attempts) {

                $invalid_login_count = 0;
                $is_blocked = 0;

                $data = array(
                    'emp_id' => $emp_id,
                    'journey_stage' => "agent_login",
                    'invalid_login_count' => $invalid_login_count,
                    'agent_type' => $agent_type,
                    'is_blocked' => $is_blocked,
                    'captcha_status' => 'yes',
                    'captcha_verified' => 'yes'

                );
                $this->db->where(["emp_id" => $emp_id, "agent_type" => $agent_type])->update("login_verification", $data);
            }
            //captcha check ends
            if ($aAgent['is_region_admin'] == '1') {
                $location = $this->db->select('axis_loc_id')->from('tls_axis_location')->where('axis_location', $aAgent['center'])->get()->row_array();
                $aAgent['location'] = $location['axis_loc_id'];
            } else {
                $aAgent['location'] = '';
            }
            $telSalesSession['telesales_session'] = ['agent_type' => $agent_type, 'agent_id' => encrypt_decrypt_password($aAgent['id']), 'agent_name' => $aAgent['agent_name'], 'is_admin' => $aAgent['is_admin'], 'is_region_admin' => $aAgent['is_region_admin'], 'location' => $aAgent['location'], 'axis_process' => $aAgent['axis_process'], "is_redirect_allow" => "1"];
            $this->setCustomerSession($telSalesSession);
            $indexUrl = explode(",", $aAgent['module_access_rights'])[0];
            $resetPasswordUrl=55;
            $password_change_date=$aAgent['password_change_date'];

            if($aAgent['is_admin'] == 1 ){
                $indexUrl = explode(",", $aAgent['module_access_rights'])[0];
            }
            // print_pre($_SESSION);exit;
            $aResponse['success'] = 1;
            $aResponse['msg'] = $this->db->where(["role_module_id" => $indexUrl])->get("role_access_module")->row()->url_link;
            http_response_code(200);
            $_SESSION['is_redirect_allow'] = '1';
        } else {

            $agent_pwd = encrypt_decrypt_password($agent_pwd);
            if ($agent_type == 4) {

                $query = $this->db
                    ->where(['base_agent_id' => $agent_code])
                    ->get('tls_base_agent_tbl')->row_array();
            } else if ($agent_type == 2) {

                $query = $this->db
                    ->where(['agent_id' => $agent_code, 'status' => 'Active'])
                    ->get('tls_agent_mst_outbound')->row_array();
            } else if ($agent_type == 3) {

                $query = $this->db
                    ->where(['do_id' => $agent_code, 'status' => 'Active'])
                    ->get('tls_master_do')->row_array();
            } else {

                $query = $this->db
                    ->where(['agent_id' => $agent_code, 'status' => 'Active'])
                    ->where('DATE(license_to)>=DATE(NOW())')
                    ->get('tls_agent_mst')->row_array();
            }
            if ($query) {

                if ($agent_type == 4) {
                    $emp_id = $query['base_agent_id'];
                } else if ($agent_type == 2) {
                    $emp_id = $query['agent_id'];
                } else if ($agent_type == 3) {
                    $emp_id = $query['do_id'];
                } else {
                    $emp_id = $query['agent_id'];
                }

                $login_attempts = $this->db
                    ->where(['emp_id' => $emp_id, 'agent_type' => $agent_type])
                    ->get('login_verification')
                    ->row_array();

                if ($login_attempts) {

                    /*if($invalid_login_count == 5){
                        $is_blocked = 1;
                    }else{
                        $invalid_login_count = $invalid_login_count + 1;
                        $is_blocked = 0;
                    }*/
                    $invalid_login_on = date('Y-m-d', strtotime($login_attempts['last_login_at']));
                    $current_date = date('Y-m-d');
                    if ($invalid_login_on == $current_date) { // next day reset count
                        $invalid_login_count = $login_attempts['invalid_login_count'];
                    } else {
                        $invalid_login_count = 0;
                        // $_SESSION['login_count_attempts'] = 0;
                    }
                    $aResponse['show_captcha'] = 0;
                    if ($invalid_login_count == 0) {
                        $aResponse['msg'] = 'Invalid Username or Password. 2 attempts left.';
                    } else if ($invalid_login_count == 1) {
                        $aResponse['msg'] = 'Invalid Username or Password. 1 attempts left.';
                    } else {
                        if ($invalid_login_count >= 3) {
                          //  $this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. ';
                           // $_SESSION['show_captcha_on_load'] = 'show';
                            $aResponse['show_captcha'] = 1;
                        } else if ($captcha_flag == 1 && $invalid_login_count != 2) {
                            $aResponse['msg'] = 'Invalid Username or Password.';
                        } else {
                            //$this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. ';
                        //    $_SESSION['show_captcha_on_load'] = 'show';
                          //  $aResponse['show_captcha'] = 1;
                        }
                    }

                    $invalid_login_count = $invalid_login_count + 1;

                    // $_SESSION['login_count_attempts'] += 1;

                    $data = array(
                        'emp_id' => $emp_id,
                        'journey_stage' => "agent_login",
                        'invalid_login_count' => $invalid_login_count,
                        'agent_type' => $agent_type,
                        'captcha_status' => 'yes',
                        'captcha_verified' => 'yes'
                        //'is_blocked' => $is_blocked,
                    );

                    $this->db->where(["emp_id" => $emp_id, "agent_type" => $agent_type])->update("login_verification", $data);

                } else {
                    $aResponse['show_captcha'] = 0;
                    $data = array(
                        'emp_id' => $emp_id,
                        'journey_stage' => "agent_login",
                        'invalid_login_count' => 1,
                        'agent_type' => $agent_type
                        //'is_blocked' => 0,
                    );
                    $this->db->insert("login_verification", $data);
                    $aResponse['msg'] = 'Invalid Username or Password. 2 attempts left.';
                }
            } else {

                $login_attempts = $this->db
                    ->where(['emp_id' => $agent_code, 'agent_type' => $agent_type])
                    ->get('login_verification')
                    ->row_array();
//               echo encrypt_decrypt_password('ZVU4QmZvTWQ2YUlLbi8yY2lTMm1jZz09','D');die;
                if ($login_attempts) {

                    $invalid_login_on = date('Y-m-d', strtotime($login_attempts['last_login_at']));
                    $current_date = date('Y-m-d');
                    if ($invalid_login_on == $current_date) { // next day reset count
                        $invalid_login_count = $login_attempts['invalid_login_count'];
                    } else {
                        $invalid_login_count = 0;
                        // $_SESSION['login_count_attempts'] = 0;
                    }

                    $aResponse['show_captcha'] = 0;
                    if ($invalid_login_count == 0) {
                        $aResponse['msg'] = 'Invalid Username or Password. 2 attempts left.';
                    } else if ($invalid_login_count == 1) {
                        $aResponse['msg'] = 'Invalid Username or Password. 1 attempts left.';
                    } else {
                        if ($invalid_login_count >= 3) {
                          //  $this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. Please enter captcha and try again.';
                            $_SESSION['show_captcha_on_load'] = 'show';
                            $aResponse['show_captcha'] = 1;
                        } else if ($captcha_flag == 1 && $invalid_login_count != 2) {
                            $aResponse['msg'] = 'Invalid Username or Password.';
                        } else {
                          //  $this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. Please enter captcha and try again.';
                            $_SESSION['show_captcha_on_load'] = 'show';
                            $aResponse['show_captcha'] = 1;
                        }
                    }

                    $invalid_login_count = $invalid_login_count + 1;

                    // $_SESSION['login_count_attempts'] += 1;

                    $data = array(
                        'emp_id' => $agent_code,
                        'journey_stage' => "agent_login",
                        'invalid_login_count' => $invalid_login_count,
                        'agent_type' => $agent_type
                        //'is_blocked' => $is_blocked,
                    );
                    $this->db->where(["emp_id" => $agent_code, "agent_type" => $agent_type])->update("login_verification", $data);
                } else {
                    // $aResponse['show_captcha'] = 1;
                    $data = array(
                        'emp_id' => $agent_code,
                        'journey_stage' => "agent_login",
                        'invalid_login_count' => 1,
                        'agent_type' => $agent_type
                        //'is_blocked' => 0,
                    );
                    $this->db->insert("login_verification", $data);
                    $aResponse['msg'] = 'Invalid Username or Password. 2 attempts left.';
                }
            }
        }
        $check=$this->check_accountDisabled($emp_id,$agent_type);
        if($check != false){
            echo json_encode($check);
            exit;
        }
        $newResponse = [
            'success' => $aResponse['success'],
            'msg' => $aResponse['msg'],
            'dsuccess' => $aResponse['success'],
            'dmsg' => $aResponse['msg'],
        ];
      //  $_SESSION["webpanel"] = $checkDetails['Data'];
        echo json_encode(array("success"=>true, "msg"=>'Authenticated Successfully'));
        exit;
        // print_pre($aResponse);exit;
    }

    function loginvalidate()
	{
       

        //echo "<pre>here";print_r($_POST);//exit;
		$data = array();
		$data['username'] = (!empty($_POST['username'])) ? $_POST['username'] : '';
		$data['password'] = (!empty($_POST['password'])) ? $_POST['password'] : '';
		//echo "yo".SERVICE_URL;exit;
		$checkDetails = curlFunction(SERVICE_URL.'/api/userLogin', $data );
	//	echo "<pre>";print_r($checkDetails);exit;
		$checkDetails = json_decode($checkDetails, true);
		//echo "<pre>";print_r($checkDetails['Data']);exit;
		if(isset($checkDetails['status_code']) && $checkDetails['status_code'] == '200')
		{
            $_SESSION["webpanel"] = $checkDetails['Data'];
		    $query=$this->db->query("select * from theme_configuaration where theme_for=1");
		    if($this->db->affected_rows() > 0){
		        $result=$query->row();
                $this->session->set_userdata('primary_color', $result->primary_color);
                $this->session->set_userdata('secondary_color', $result->secondary_color);
                $this->session->set_userdata('text_color', $result->text_color);
                $this->session->set_userdata('background_color',$result->background_color);
                $this->session->set_userdata('cta_color', $result->cta_color);
                $_SESSION["webpanel"]['creditor_logo'] =  $result->logo_url;
            }
			
            
			echo json_encode(array("success"=>true, "msg"=>'Authenticated Successfully'));
			exit;		
		}
		else
		{
			echo json_encode(array("success"=>false, "msg"=>'Username or Password incorrect.'));
			exit;
		}
	}

	/*function forgotpassword()
	{
		if(!empty($_POST['email_id']))
		{
			$result = $this->loginmodel->forgotpass($_POST['email_id']);	
			if($result)
			{
				// $result_content = $this->loginmodel->getContentForgetPass(1);
				$result_content = $this->loginmodel->getEmailContent("ADMIN_FORGOT_PASSWORD");
				
				$url = base_url()."forgetchangepassword?text=".rtrim(strtr(base64_encode("eid=".$_POST['email_id']), '+/', '-_'), '=')."/".rtrim(strtr(base64_encode("uid=".$result[0]->user_id ), '+/', '-_'), '=')."/".rtrim(strtr(base64_encode("dt=".date("Y-m-d")), '+/', '-_'), '=');
				// echo $url; exit;
				
				$message = str_replace(array('{username}','{url}','{link}'), array($result[0]->user_name,$url,base_url()), $result_content['content']);
				
				$this->email->from(FROM_EMAIL); // change it to yours
				$this->email->to($_POST['email_id']);// change it to yours
				$this->email->subject($result_content['subject']);
				$this->email->message($message);		
				$checkemail = $this->email->send();
				
				if($checkemail)
				{
					echo json_encode(array("success"=>true, "msg"=>'Mail sent successfully, Please check your mail.'));
					exit;
				}
				else
				{
					echo json_encode(array("success"=>false, "msg"=>'Problem while sending mail..'));
					exit;
				}			
			}
			else
			{		
				echo json_encode(array("success"=>false, "msg"=>'Invalid Email ID.'));
				exit;
			}
		}
		else
		{		
			echo json_encode(array("success"=>false, "msg"=>'Invalid Email ID.'));
			exit;
		}	
	}
	*/
    
    function generate_token(){
        echo $password = md5('5621be1f2352574f7a48ba5596a7c69d');
        exit;
        $data = array();
        $data['username'] = (!empty($_POST['username'])) ? $_POST['username'] : '';
        $data['password'] = (!empty($_POST['password'])) ? $_POST['password'] : '';
        if(!empty($_POST)){
            $checkDetails = curlFunction(SERVICE_URL.'/api/generateToken', $data );
            echo json_encode($checkDetails);
            exit;
        }else{
            echo json_encode(array("success"=>false, "msg"=>'No Data Found.'));
            exit;
        }

    }
    

}

?>