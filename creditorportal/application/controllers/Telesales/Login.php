<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //var_dump($_SESSION);
    }

    public function index()
    {
    }
    public function logout() {
        // echo 1234;exit;
        $telSalesSession = $this->session->userdata('telesales_session');
        // print_r($telSalesSession);exit;
        if(isset($telSalesSession['is_maker_checker'])){
            if($telSalesSession['is_maker_checker'] == "yes"){
                session_unset();
                session_destroy();
                redirect(base_url().'?login_type=maker_checker');
                exit;
            }
        }
        session_unset();
        session_destroy();
        redirect("login");
    }
    public function forgotPassword()
    {

        extract($this->input->post());

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        $passEmail = implode($pass);

        //        $salt = sha1($passEmail);
        //        $pass = md5($salt . $passEmail);
        $pass = encrypt_decrypt_password($passEmail);

        $num = $this->db->where(["email" => $email])->get("employee_details")->num_rows();

        if ($num > 0) {
            $this->db->where(["email" => $email])->update("employee_details", [
                "password" => $pass,
            ]);

            $user_mail_data = [
                'to' => $email,
                'subject' => "Reset Password",
                'message' => "<p>Here is your User Name: $email</p><p>Password: $passEmail</p>",
            ];
            $user_mail_data['to'] = 'amit.matani12@gmail.com';

            // echo "here";exit;
            $this->load->library('email');
            if (Email::sendMail($user_mail_data)) {
                $insert_data = [
                    "type" => "E",
                    "to" => $email,
                    "content" => json_encode($user_mail_data),
                    "status" => "true",
                ];
                $this->db->insert('queue_log', $insert_data);
                // echo "inserted";exit;

            }

            echo "1";
        } else {
            echo "0";
        }
    }

    public function setCustomerSession($telesalesSession)
    {
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
        $this->session->regenerate_id();
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
    public function accounts_login()
    {
        $this->load->login_template('accounts_login');
    }

    public function get_account_login_details()
    {

        extract($this->input->post(null, true));
        //table_account_verify

        $check = ['username' => $employee_username, 'password' => $employee_pwd];
        $result = $this->db->where($check)
            ->get('account_verify');
        if ($result->num_rows() > 0) {
            $this->session->set_userdata('account_verify', $check);
            $success = ['success' => 1, 'mgs' => 'account_verify_encrypt'];
        } else {
            $success = ['success' => 0, 'mgs' => 'Invalid username of password'];
        }

        echo json_encode($success);
        exit;
    }


    public function get_login_details()
    {
   //   echo encrypt_decrypt_password('ZDIvdVhlZGw5czliY0lKWjhFVFhrdz09=','D');die;
        require_once APPPATH . 'libraries/encryption.php';

        // print_pre($_SESSION);
        extract($this->input->post(null, true));

        $Encryption = new Encryption();
        $nonceValue = $enckey;

        $agent_code = $Encryption->decrypt($agent_code, $nonceValue);
        $agent_pwd = str_replace('%40', '@', $Encryption->decrypt($agent_pwd, $nonceValue));
     $agent_type = $Encryption->decrypt($agent_type, $nonceValue);

        $responseEncryption = 'xLoiaASsfnjAJLFJLLjwsjlw';


        // print_r([$agent_code,$agent_pwd,$agent_type,$Encryption->decrypt($this->input->post('entered_captcha_agent'),$nonceValue)]);exit;

        // if(!isset($_SESSION['client_ip'])){
        //     $_SESSION['client_ip'] = $_SERVER['SERVER_ADDR'];
        //     $_SESSION['login_attempt_ip'] = 1;
        //     $_SESSION['first_login_time'] = date("Y-m-d H:i:s");
        // }else{
        //     $_SESSION['login_attempt_ip']++;
        // }



        // $time_now = date("Y-m-d H:i:s");

        // $to_time = strtotime($time_now);
        // $from_time = strtotime($_SESSION['first_login_time']);
        // $time_diff = round(abs($to_time - $from_time) / 60,2). " minute";


        // if($time_diff < 60 && $_SESSION['login_attempt_ip'] > 10){
        //     $data = ["show_captcha" => 0,"success" => 0, "msg" => "Too many attempts! please try again some time!"];
        //     echo json_encode($data);
        //     exit;
        // }

        // if($time_diff >= 60){
        //     unset($_SESSION['client_ip']);
        //     unset($_SESSION['login_attempt_ip']);
        //     unset($_SESSION['first_login_time']);
        // }


        if ($_SESSION['show_captcha_on_load'] == 'show' && $onloading_page == '1') {
            $aResponse['show_captcha'] = 1;
            $show_captch = 1;
            $newResponse = [
                'show_captcha' => $Encryption->encrypt($show_captch, $responseEncryption),
            ];

            echo json_encode($newResponse);
            exit;
        }




        //captcha code
        $captcha_flag = 0;
        $captcha_string = $this->session->userdata('captcha_string');
        $is_captcha_verified = $this->session->userdata('is_captcha_verified');
        // if($captcha_string != $this->input->post('entered_captcha_agent') && $this->input->post('entered_captcha_agent') != ''){
        // print_r([$captcha_string,$Encryption->decrypt($this->input->post('entered_captcha_agent'),$nonceValue)]);
        // exit;
        $captcha_flag = 1;
        // commented by pooja for testing --- need to uncomment
        if ($captcha_string != $Encryption->decrypt($this->input->post('entered_captcha_agent'), $nonceValue) && $Encryption->decrypt($this->input->post('entered_captcha_agent'), $nonceValue) != '') {

            // http_response_code(401);
            $show_captch = 1;
            $success = 0;
            $msg = "Please enter correct captcha!";

            $newResponse = [
                'show_captcha' => $Encryption->encrypt($show_captch, $responseEncryption),
                'success' => $Encryption->encrypt($success, $responseEncryption),
                'msg' => $Encryption->encrypt($msg, $responseEncryption)
            ];

            $data = ["show_captcha" => 1, "success" => 0, "msg" => "Please enter correct captcha!"];
            echo json_encode($data);
            exit;
        } else {
            $captcha_flag = 1;
        }
        // commented by pooja for testing --- need to uncomment

        //print_pre($this->input->post(null, true));exit;
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
                http_response_code(202);
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
                http_response_code(202);
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
                http_response_code(202);
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
            http_response_code(202);
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
                            $this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. Please enter captcha and try again.';
                            $_SESSION['show_captcha_on_load'] = 'show';
                            $aResponse['show_captcha'] = 1;
                        } else if ($captcha_flag == 1 && $invalid_login_count != 2) {
                            $aResponse['msg'] = 'Invalid Username or Password.';
                        } else {
                            $this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. Please enter captcha and try again.';
                            $_SESSION['show_captcha_on_load'] = 'show';
                            $aResponse['show_captcha'] = 1;
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
                            $this->captcha_verification($emp_id, 'no');
                            $aResponse['msg'] = 'You have reached the maximum logon attempts. Please enter captcha and try again.';
                            $_SESSION['show_captcha_on_load'] = 'show';
                            $aResponse['show_captcha'] = 1;
                        } else if ($captcha_flag == 1 && $invalid_login_count != 2) {
                            $aResponse['msg'] = 'Invalid Username or Password.';
                        } else {
                            $this->captcha_verification($emp_id, 'no');
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
            'success' => $Encryption->encrypt($aResponse['success'], $responseEncryption),
            'msg' => $Encryption->encrypt($aResponse['msg'], $responseEncryption),
            'dsuccess' => $aResponse['success'],
            'dmsg' => $aResponse['msg'],
        ];

        // print_pre($aResponse);exit;
        echo json_encode($aResponse);
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
    public function redirect_summary($emp_id_url)
    {
        //echo $emp_id_url;
        print_R(encrypt_decrypt_password($emp_id_url));
        die;
        //echo $emp_id_decrypt = encrypt_decrypt_password($emp_id_url,'D');die;
        if ($emp_id_url) {

            //$telSalesSession['telesales_session'] = array('emp_id' => $emp_id_decrypt);
            // $this->session->set_userdata($telesalesSession);
        }


        //redirect(base_url('tele_summary'));
    }
    public function captcha_verification($emp_id, $captcha_verified)
    {
        if ($captcha_verified == 'yes') {
            $this->db->where('emp_id', $emp_id)->update('login_verification', ['captcha_status' => $captcha_verified, 'captcha_verified' => $captcha_verified, 'invalid_login_count' => 0]);
        } else {
            $this->db->where('emp_id', $emp_id)->update('login_verification', ['captcha_status' => $captcha_verified, 'captcha_verified' => $captcha_verified]);
        }
    }

}
