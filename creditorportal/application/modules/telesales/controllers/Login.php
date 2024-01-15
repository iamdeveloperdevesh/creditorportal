<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
             //   session_destroy();

        //var_dump($_SESSION);
	
    }

    //captcha - update - upendra - 16-10-2021
    public function refresh_captcha(){
        $captcha_string = $this->generateRandomString();
        $this->session->set_userdata('captcha_string',$captcha_string);
        $cap = common_captcha_create($captcha_string);
        echo $cap['image'];
    }
    //captcha - update - upendra - 16-10-2021
    public function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function common_captcha_create_login($captcha_string)
    {   
        $ci =& get_instance();
        $ci ->load->library('image_lib');
        $ci ->load->helper('captcha');
        $vals = array(
            'word'          => $captcha_string,
            'img_path'      => './public/assets/images/',
            'img_url'       =>  $this->config->base_url().'public/assets/images',
            'font_path'     => './path/to/fonts/texb.ttf',
            // 'font_path'     => './public/assets/images/texb.ttf',
            'img_width'     => '150',
            'img_height'    => '40',
            'expiration'    => '7200',
            'word_length'   => '6',
            'font_size'     => '24',
            'img_id'        => 'captcha_image_load',
            'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    
            // White background and border, black text and red grid
            'colors'        => array(
                    'background' => array(255, 255, 255),
                    'border' => array(255, 255, 255),
                    'text' => array(0, 0, 0),
                    'grid' => array(255, 130, 130)
            )
    );
    
    $cap = create_captcha($vals);
    return $cap;
    
    }

    public function index()
    {
		//echo 123;
		//captcha update for login
		
        $captcha_string = $this->generateRandomString();
        $this->session->set_userdata('captcha_string',$captcha_string);
        $cap = $this->common_captcha_create_login($captcha_string);
        $data['captcha_image'] = $cap;
		// echo encrypt_decrypt_password(5933);
		// exit;
		Monolog::saveLog("generateOTP", "I", "test");
        //    $this->load->login_template('login_view');
        $this->load->view('login/login_view.php',$data);
		
		

       
        
    }
public function get_api()
    {
        $data = '<?xml version="1.0"?><faml>
        <IDENTIFIER>E46B22F2EED17A93C509731298FC2B0444B41590115D8A970A22EB529A66DEF7</IDENTIFIER>
        <USERID>prakashk</USERID>
        <REQUESTUUID>ABCD1133213</REQUESTUUID>
        <REQUESTDATETIME>2019-05-30 13:58:45</REQUESTDATETIME>
        <LEADID>2019102976515</LEADID>
        <LEADOWNERID>260618000044</LEADOWNERID>
        <CUSTOMERID>260618000044</CUSTOMERID>
        <SALUTATIONID>Mr</SALUTATIONID>
        <FIRSTNAME>test</FIRSTNAME>
        <LASTNAME>BANKING</LASTNAME>
        <MOBILEPHONE>7010444138</MOBILEPHONE>
        <EMAIL>jack@gmail.com</EMAIL>
        <ADDRESS>Om hrad ntalatiof, holOmshre, talathioficeeachol, OmshreeSada9talathi</ADDRESS>
        <CITY>MUMBAI</CITY>
        <STATE>MAHARASHTRA</STATE>
        <COUNTRY>IN</COUNTRY>
        <ZIPCODE>622103</ZIPCODE>
        <PRODUCTID>R02</PRODUCTID>
        <LEADOWNERNAME>LEADOWNERSNAME</LEADOWNERNAME>
        <CURRENCYID>INR</CURRENCYID>
        <GENDERID>M</GENDERID>
        <DATEOFBIRTH>19-12-1988</DATEOFBIRTH>
        <IDTYPE>Aadhar Card</IDTYPE>
        <IDVALUE>5478-2589-3688</IDVALUE>
        <PANNUMBER>DDDDD6456A</PANNUMBER>
        <RMMOBILENO>9095583189</RMMOBILENO>
        <SPEMAIL>REGI@GMAIL.COM</SPEMAIL>
        <SPCODE>1</SPCODE>
        <LGCODE>1</LGCODE>
        <BRANCHHEADNAME>REGI</BRANCHHEADNAME>
        <BRANCHHEADEMAIL>BEG@GMAIL.COM</BRANCHHEADEMAIL>
        <BRANCHOPSHEADEMAIL>BEG@GMAIL.COM</BRANCHOPSHEADEMAIL>
        <BRANCHHEADMOBILE>9443569874</BRANCHHEADMOBILE>
        <AXISBANKACCOUNT>Y</AXISBANKACCOUNT>
        <ACCOUNTNUMBER>9170100000000329</ACCOUNTNUMBER>
        <OTHERBANKCHEQUE>N</OTHERBANKCHEQUE>
        <IFSCCODE>UTIB0000016</IFSCCODE>
        <ISNRI>N</ISNRI>
        <BRANCH_SOL_ID>051</BRANCH_SOL_ID>
        <BRANCH_NAME>chennai</BRANCH_NAME>
        <ADDITIONAL_FIELD1>asdf</ADDITIONAL_FIELD1>
        <ADDITIONAL_FIELD2>asdf</ADDITIONAL_FIELD2>
        <ADDITIONAL_FIELD3>asdf</ADDITIONAL_FIELD3>
        <ADDITIONAL_FIELD4>asdf</ADDITIONAL_FIELD4>
        <ADDITIONAL_FIELD5>asdf</ADDITIONAL_FIELD5>
        </faml>';

        $data = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data);
        $xml = new \SimpleXMLElement($data);
        $array = (array) $xml;

        extract($array);

        $alreadyExists = $this->db->where(["email" => $EMAIL])->or_where(['mob_no' => $MOBILEPHONE])->get("employee_details")->num_rows();

        if ($alreadyExists == 0) {
            $arr = [
                "ref1" => $SPCODE,
                "ref2" => $LGCODE,
                "lead_id" => $LEADID,
                "salutation" => $SALUTATIONID, 
                "product_id" => $PRODUCTID, 
                "emp_firstname" => $FIRSTNAME,
                "emp_lastname" => $LASTNAME,
                "gender" => ($GENDERID == 'M') ? "Male" : "Female",
                "bdate" => $DATEOFBIRTH,
                "mob_no" => $MOBILEPHONE,
                "access_right_id" => "2",
                "module_access_rights" => "1,8",
                "email" => $EMAIL,
                "pancard" => $PANNUMBER,
                "adhar" => $IDVALUE,
                "address" => $ADDRESS .", ".$CITY.", ".$STATE. ", ".$ZIPCODE,
                "emp_city" => $CITY,
                "emp_state" => $STATE,
                "emp_pincode" => $ZIPCODE,
                "ifsc_code" => $IFSCCODE,
                "json_qote" => json_encode($array),

                
            ];

            $this->db->insert("employee_details", $arr);
            
            $emp_id = $this->db->insert_id();
            $_SESSION['emp_id'] =  $emp_id;

            $this->db->insert("family_relation", [
                "emp_id" => $emp_id,
                "family_id" => 0
            ]);

            redirect("employee_enrollment_new/".$emp_id."/".$PRODUCTID);
            
        }else if($alreadyExists > 0) {
            $row = $this->db->select("emp_id, product_id")->where(["email" => $EMAIL])->or_where(['mob_no' => $MOBILEPHONE])->get("employee_details")->row();

            $emp_id = $row->emp_id;
            $PRODUCTID = $row->product_id;
            $_SESSION['emp_id'] =  $emp_id;
            redirect("employee_enrollment_new/".$emp_id."/".$PRODUCTID);
        }
    }
    public function about()
    {
        $this->load->about_template("about_view");
    }

    public function contact()
    {
        $this->load->about_template("contact_us_view");
    }

    /* public function pdf1() {
        $this->load->about_template("pdf1");
    }

    public function pdf2() {
        $this->load->about_template("pdf2");
    }

    public function pdf3() {
        $this->load->about_template("pdf3");
    } */

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
                "password" => $pass
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
                    "status" => "true"
                ];
                $this->db->insert('queue_log', $insert_data);
                // echo "inserted";exit;

            }

            echo "1";
        } else {
            echo "0";
        }
    }

    public function single_sign_on($emp_type = '')
    {
        $query = 0;
        if ($emp_type) {
            $query = $this->db->where(['emp_id' => $_SESSION['emp_id']])->where("find_in_set('" . $emp_type . "',access_right_id) != 0")->get('employee_details')->num_rows();
        }
        $mod =  explode(",", $this->session->userdata('module_access_rights'));

        if ($query > 0 && $emp_type != '') {

            if ($emp_type == 1) {

                $_SESSION['current_access_right'] = $emp_type;
                redirect('employee/home');
            } elseif ($emp_type == 2) {

                $_SESSION['current_access_right'] = $emp_type;
                redirect('employer/home');
            } elseif ($emp_type == 4) {
                $_SESSION['current_access_right'] = $emp_type;
                if (in_array("1", $mod)) {
                    redirect('broker/dashboard/policy_wise_enrollment');
                }
                if (in_array("2", $mod)) {
                    redirect('broker/policy_dashboard');
                }
                if (in_array("5", $mod)) {
                    redirect('broker_enrollment_report');
                }
                if (in_array("6", $mod)) {
                    redirect('broker_renewal');
                }
                if (in_array("7", $mod)) {
                    redirect('broker_network_hospital');
                }
                if (in_array("8", $mod)) {
                    redirect('broker/broker_flexi_benefit');
                }
                if (in_array("9", $mod)) {
                    redirect('broker/form_center');
                }

                //redirect('broker/policy_creation');
            } elseif ($emp_type == 5) {
                $_SESSION['current_access_right'] = $emp_type;

                if (in_array("1", $mod)) {
                    redirect('broker/dashboard/policy_wise_enrollment');
                }
                if (in_array("2", $mod)) {
                    redirect('broker/policy_dashboard');
                }
                if (in_array("5", $mod)) {
                    redirect('broker_enrollment_report');
                }
                if (in_array("6", $mod)) {
                    redirect('broker_renewal');
                }
                if (in_array("7", $mod)) {
                    redirect('broker_network_hospital');
                }
                if (in_array("8", $mod)) {
                    redirect('broker/broker_flexi_benefit');
                }
                if (in_array("9", $mod)) {
                    redirect('broker/form_center');
                }
            } elseif ($emp_type == 6) {

                $_SESSION['current_access_right'] = $emp_type;
                if (in_array("1", $mod)) {
                    redirect('broker/dashboard/policy_wise_enrollment');
                }
                if (in_array("2", $mod)) {
                    redirect('broker/policy_dashboard');
                }
                if (in_array("5", $mod)) {
                    redirect('broker_enrollment_report');
                }
                if (in_array("6", $mod)) {
                    redirect('broker_renewal');
                }
                if (in_array("7", $mod)) {
                    redirect('broker_network_hospital');
                }
                if (in_array("8", $mod)) {
                    redirect('broker/broker_flexi_benefit');
                }
                if (in_array("9", $mod)) {
                    redirect('broker/form_center');
                }
                //redirect('broker/policy_creation');
            } else {

                $_SESSION['current_access_right'] = $emp_type;
                //redirect('broker/policy_creation');
                redirect('broker/dashboard/policy_wise_enrollment');
            }
        } else {
            redirect('login');
        }

        extract($this->input->post(null, true));

        $query = $this->db
            ->where(['email' => $broker_email, 'password' => md5($broker_pwd)])
            ->get('employee_details')
            ->row_array();
        if ($query && count($query) > 0) {
            $query2 = $this->db->where_in('access_right_id', explode(',', $query['access_right_id']))->get('access_rights')->result_array();
            $currentAccessRight = $query2[0]['access_right_id'];
            if ($emp_type == 'employer') {
                $currentAccessRight = 2;
            }
            $arr = [
                'access_rights' => $query2,
                'current_access_right' => $currentAccessRight,
                'email' => $query['email'],
                'emp_id' => $query['emp_id'],
                'company_id' => $query['company_id'],
                'company_code' => $query['company_code'],
                'emp_code' => $query['emp_code'],
                'emp_name' => $query['emp_firstname'],
                'mob_no' => $query['mob_no'],
                'emp_last_name' => $query['emp_lastname'],
                'emp_full_name' => $query['emp_firstname'] . ' ' . $query['emp_lastname'],
            ];
            $this->session->set_userdata($arr);
            if ($query2[0]['access_right_id'] == 1) {
                redirect('employee/home');
            } elseif ($query2[0]['access_right_id'] == 2) {
                redirect('employer/home');
            } else {
                redirect('broker/policy_creation');
            }
        } else {
            echo json_encode(['error' => 'Invalid Credentials']);
        }
    }

    /*    public function get_login_details() {
        extract($this->input->post(null, true));

        $emp_type_id = 0;
        if ($emp_type == 'employer') {
            $emp_type_id = 2;
        } else {
            $emp_type_id = 1;
        }


         $employee_pwd = encrypt_decrypt_password($employee_pwd);

        $query = $this->db
                ->where(['email' => $employee_email, 'password' => $employee_pwd])
                ->where("find_in_set('" . $emp_type_id . "',access_right_id) != 0")
                ->get('employee_details')
                ->row_array();
       
          
        if ($query && count($query) > 0) {
            $query2 = $this->db->where_in('access_right_id', explode(',', $query['access_right_id']))->get('access_rights')->result_array();
            $currentAccessRight = $query2[0]['access_right_id'];
              
            if ($emp_type == 'employer') {
                $currentAccessRight = 2;
            }
            $arr = [
                'mobile_no' => $query['mob_no'],
                'access_rights' => $query2,
                'current_access_right' => $currentAccessRight,
                'email' => $query['email'],
                'emp_id' => $query['emp_id'],
                'company_id' => $query['company_id'],
                'company_code' => $query['company_code'],
                'emp_code' => $query['emp_code'],
                'emp_name' => $query['emp_firstname'],
                'emp_last_name' => $query['emp_lastname'],
               'module_access_rights' => $query['module_access_rights'],
                'emp_full_name' => $query['emp_firstname'] . ' ' . $query['emp_lastname'],
                'desc_id' => $query['emp_designation'],
                'grade'=> $query['emp_grade']
            ];
			
			//contact us
			$cmp_query = $this->db->where(["company_id" => $query['company_id']])->get("master_company")->row();
			
			$arr["company_address"] = $cmp_query->company_address;
			$arr["company_mob"] = $cmp_query->company_mob;
			$arr["company_email"] = $cmp_query->company_email;
			$arr["company_cont_name"] = $cmp_query->company_cont_name;
			$arr["company_head_name"] = $cmp_query->company_head_name;
			 $arr["company_logo"]  = $cmp_query->company_logo;

            $this->session->set_userdata($arr);
            echo '1';
        } else {
            echo '0';
        }
    }*/
    public function get_login_details()
    {//echo 123;die;



	//	echo encrypt_decrypt_password('enFya0Z1OUVmVWgrQmVpd1l1S1pHUT09','D');exit;
		 require_once APPPATH . 'libraries/encryption.php';
		 			  $this->load->library('encryption');
					   extract($this->input->post(null, true));
  $Encryption = new Encryption();

           // echo 123;die;
             $nonceValue=$_POST['nonceValue'];

	
/*
           

          

       
		 $employee_pwd = $_POST['employer_pwd'];
            $employer_email = $_POST['employer_email'];
            $entered_captcha = $_POST['entered_captcha'];*/
			 $employee_pwd = $Encryption->decrypt($_POST['employee_pwd'], $nonceValue);
       $employee_email = $Encryption->decrypt($_POST['employee_email'], $nonceValue);
			  $entered_captcha = $Encryption->decrypt($_POST['entered_captcha'], $nonceValue);
			 $onloading_page =  $Encryption->decrypt($onloading_page, $nonceValue);
		
  

         if($_SESSION['show_captcha_on_load_admin'] == 'show' && $onloading_page == '1'){
            $aResponse['show_captcha'] = 1;
			 $this->session->set_userdata($aResponse);
			 session_unset('msg');
			
        }

        $captcha_string = $this->session->userdata('captcha_string');
        $is_captcha_verified = $this->session->userdata('is_captcha_verified');
        $captcha_flag = 0;
		
        if($captcha_string != $entered_captcha && $entered_captcha != ''){
           // http_response_code(401);
            $data = ["show_captcha" => 1,"success" => 0, "msg" => "Please enter correct captcha!"]; 
        
		
                       echo json_encode($data);

            exit;
        }else{
            $captcha_flag = 1;
        }
         //invalid login attempt code captcha ends
        /*
        $emp_type_id = 0;
        if ($emp_type == 'employer') {
            $emp_type_id = 2;
        } else {
            $emp_type_id = 1;
        }*/


        //        $salt = sha1($employee_pwd);
        //        $employee_pwd = md5($salt . $employee_pwd);
		$login_data="logins";
		
        $employee_pwd = encrypt_decrypt_password($employee_pwd);

        $query = $this->db
            ->where(['email' => $employee_email, 'password' => $employee_pwd])
            // ->where("find_in_set('" . $emp_type_id . "',access_right_id) != 0")
            ->get('employee_details')
            ->row_array();
		//	print_r($this->db->last_query());die;


        if ($query && count($query) > 0) {

            //invalid login attempt code captcha
            $emp_id = $query['emp_id'];
                $login_attempts = $this->db
                ->where(['emp_id' => $emp_id])
                ->get('login_verification')
                ->row_array();
                if($login_attempts){
                    
                    $invalid_login_count = 0;
                    $is_blocked = 0;
                   
                    $data = array('emp_id' => $emp_id,
                          'journey_stage' => "admin_login",
                          'invalid_login_count' => $invalid_login_count,
                          'is_blocked' => $is_blocked,
						  'valid_invalid_success' => 1,
                          );
                    $this->db->where(["emp_id" => $emp_id])->update("login_verification", $data);
                }
            //invalid login attempt code captcha ends
			
			// Code Added By Shardul on 24-July-2020 for Single User Login With Credientials Start
			$emp_id = $query['emp_id'];
			$loginToken = time().RAND();
			$this->db->where(["emp_id" => $emp_id])->update("employee_details", ["login_token" => $loginToken]);
			$_SESSION[$emp_id.'_login_token'] = $loginToken;
			// Code Added By Shardul on 24-July-2020 for Single User Login With Credientials End
			
            $query2 = $this->db->where_in('access_right_id', explode(',', $query['access_right_id']))->get('access_rights')->result_array();
            $currentAccessRight = $query2[0]['access_right_id'];

            /*if ($emp_type == 'employer') {
                $currentAccessRight = 2;
            }*/
            $arr = [
                'mobile_no' => $query['mob_no'],
                'access_rights' => $query2,
                'current_access_right' => $currentAccessRight,
                'email' => $query['email'],
                'emp_id' => $query['emp_id'],
				'emp_id_admin' => $query['emp_id'],
                'company_id' => $query['company_id'],
                'company_code' => $query['company_code'],
                'emp_code' => $query['emp_code'],
                'emp_name' => $query['emp_firstname'],
                'emp_last_name' => $query['emp_lastname'],
                'module_access_rights' => $query['module_access_rights'],
                'emp_full_name' => $query['emp_firstname'] . ' ' . $query['emp_lastname'],
                'desc_id' => $query['emp_designation'],
                'grade' => $query['emp_grade'],
				'role' => $query['role'],
				'login_data'=>$login_data,
                "is_redirect_allow" => "1"
            ];

            //contact us
            $cmp_query = $this->db->where(["company_id" => $query['company_id']])->get("master_company")->row();
            if ($cmp_query->company_address != '') {
                $arr["company_address"] = $cmp_query->company_address;
            }

            if ($cmp_query->company_mob != '') {
                $arr["company_mob"] = $cmp_query->company_mob;
            }

            if ($cmp_query->company_email != '') {
                $arr["company_email"] = $cmp_query->company_email;
            }

            if ($cmp_query->company_cont_name != '') {
                $arr["company_cont_name"] = $cmp_query->company_cont_name;
            }
            if ($cmp_query->company_head_name != '') {
                $arr["company_head_name"] = $cmp_query->company_head_name;
            }
            if ($cmp_query->company_logo != '') {
                $arr["company_logo"]  = $cmp_query->company_logo;
            }


            $this->session->set_userdata($arr);
			
			//check if create proposal exist if it does then redirect to it else as it is
			$check_array = explode(",", $_SESSION['module_access_rights']);
			if(in_array("8",$check_array)){
				$indexUrl = 8;
			}
			//
			else{
				$indexUrl = explode(",", $_SESSION['module_access_rights'])[0];
			}
            

            $arr1['success'] = 1;

			
            $arr1['mgs'] = $this->db->where(["role_module_id" => $indexUrl])->get("role_access_module")->row()->url_link;
              $this->session->set_userdata($arr1);
			  
			  //redirect($arr1['mgs']);
           echo json_encode($arr1);
        } else {
            //invalid login attempts Code captcha
            $query = $this->db
            ->where(['email' => $employee_email])
            ->get('employee_details')
            ->row_array();
            $emp_id = 0;
            if($query){
                $emp_id = $query['emp_id'];
                $login_attempts = $this->db
                ->where(['emp_id' => $emp_id])
                ->get('login_verification')
                ->row_array();
                if($login_attempts){
                    $invalid_login_on = date('Y-m-d',strtotime($login_attempts['last_login_at']));
                    $current_date = date('Y-m-d');
                    if($invalid_login_on == $current_date){ //next day reset count
                       $invalid_login_count = $login_attempts['invalid_login_count']; 
                    }else{
                        $invalid_login_count = 0;
                    }
                    /*if($invalid_login_count == 5){
                        $is_blocked = 1;
                    }else{
                        $invalid_login_count = $invalid_login_count + 1;
                        $is_blocked = 0;
                    }*/
                    $arr1['show_captcha'] = 0;
                    if($invalid_login_count == 0){
                        $arr1['msg'] = 'Invalid Username or Password. 2 attempts left.';
                    }else if($invalid_login_count == 1){
                        $arr1['msg'] = 'Invalid Username or Password. 1 attempts left.';
                    }else{
                        if($captcha_flag == 1 && $invalid_login_count != 2){
                            $arr1['msg'] = 'Invalid Username or Password.';
                        }else{
                            $arr1['msg'] = 'You have reached the maximum logon attempts. Please enter captcha and try again.';
                            $_SESSION['show_captcha_on_load_admin'] = 'show';
                        }
                        $arr1['show_captcha'] = 1;
                    }
                    $invalid_login_count = $invalid_login_count + 1;
                        
                    $data = array('emp_id' => $emp_id,
                          'journey_stage' => "admin_login",
                          'invalid_login_count' => $invalid_login_count,
						  'valid_invalid_success' => 0,
                          //'is_blocked' => $is_blocked,
                          );
                    $this->db->where(["emp_id" => $emp_id])->update("login_verification", $data);
                }else{
                    $arr1['show_captcha'] = 0;
                    $data = array('emp_id' => $emp_id,
                          'journey_stage' => "admin_login",
                          'invalid_login_count' => 1,
						  'valid_invalid_success' => 0,
                          //'is_blocked' => 0,
                          );
                    $this->db->insert("login_verification", $data);
                    $arr1['msg'] = 'Invalid Username or Password. 2 attempts left.';
                }
            }else{
                  $data = array('emp_id' => $emp_id,
                          'journey_stage' => "admin_login",
                          'valid_invalid_success' => 0,
                          //'is_blocked' => 0,
                          );
                    $this->db->insert("login_verification", $data);
                $arr1['msg'] = "Invalid Username or Password.";
                $arr1['show_captcha'] = 0;
            }
            $arr1['success'] = 0;
            // invalid login attempt code captcha ends
			   //$this->session->set_userdata($arr1);
			   //redirect('login');
			   //exit;
           echo json_encode($arr1);
        }
    }
}
