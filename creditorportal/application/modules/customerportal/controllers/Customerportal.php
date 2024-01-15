<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once PATH_VENDOR.'vendor/autoload.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/SMTP.php';
class Customerportal extends CI_Controller
{

    public function index()
    {
       // echo encrypt_decrypt_password(32839, 'E');die;
       // echo 123;exit;
        $data['trace_id'] = $this->session->userdata('trace_id');


        if(!empty($_REQUEST['lead_id'])){
            $customer_req_data['lead_id'] = $_REQUEST['lead_id'];
            setSessionByLeadId($_REQUEST['lead_id']);

            $customer_req_data['customer_id'] = $this->session->userdata('customer_id');
            $customer_req_data['trace_id'] = $this->session->userdata('trace_id');
            $data['get_customer_details'] = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $customer_req_data),true);
            $step=$this->db->query("select dropout_page from lead_details where lead_id=".encrypt_decrypt_password($customer_req_data['lead_id'],'D'))->row()->dropout_page;
            $data['dropout_page']=$step;
            //echo '<pre>';print_r($data);exit;
        }
        unset($_SESSION['is_normal_customer']);
        if(isset($_REQUEST['partner'])){
            $partner_id = encrypt_decrypt_password($_REQUEST['partner'], 'D');
            $creditor_logo=$this->db->query("select creditor_logo from master_ceditors where creditor_id=".$partner_id)->row()->creditor_logo;
            $this->session->set_userdata('creditor_logo', $creditor_logo);
            // echo $partner_id;die;
            if($partner_id){
                $arr['partner_id'] = $partner_id;
                //  $checkDetails = (curlFunction(SERVICE_URL . '/customer_api/fetchPartnerDetails', $arr));
                //  var_dump($checkDetails);die;
                //   print_r($arr);die;
                $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/fetchPartnerDetails', $arr),TRUE);
                //    var_dump($checkDetails['theme_param']);die;
                if(!empty($checkDetails)){
                    $theme_param= $checkDetails['theme_param'];
                    $theme_param_arr=explode(",",$theme_param);
                    $this->session->set_userdata('partner_id_session', $_REQUEST['partner']);
                    $this->session->set_userdata('primary_color', $theme_param_arr[0]);
                    $this->session->set_userdata('secondary_color', $theme_param_arr[1]);
                    $this->session->set_userdata('text_color', $theme_param_arr[2]);
                    $this->session->set_userdata('background_color', $theme_param_arr[3]);
                    $this->session->set_userdata('cta_color', $theme_param_arr[4]);

                    $qu=$this->db->query("select * from link_ui_configuaration where creditor_id=".$partner_id);
                    if($this->db->affected_rows() > 0){
                        $re_qu=$qu->result_array();
                        $this->session->set_userdata('linkUI_configuaration', $re_qu);
                    }
                }else{
                    unset($_SESSION['partner_id_session']);
                    redirect(base_url().'customerportal', 'refresh');
                }


            }else{
                unset($_SESSION['partner_id_session']);
                redirect(base_url().'customerportal', 'refresh');
            }
        }else{
            unset($_SESSION['partner_id_session']);
            $this->session->set_userdata('is_normal_customer', 1);
        }
        if(isset($_REQUEST['product'])){
            $this->session->set_userdata('product_id_session', $_REQUEST['product']);
        }else{
            unset($_SESSION['product_id_session']);
        }
        if($this->session->userdata('product_id_session')){
            $data['product_id'] = $this->session->userdata('product_id_session');
            $data['plan_id'] = $this->session->userdata('product_id_session');
            $productDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/get_product_details', $data),TRUE);
            $data['product_gender'] = $productDetails['data']['gender'];
            $data['max_insured_count'] = $productDetails['data']['max_insured_count'];
            if(isset($productDetails['data']['child_count'])){
                 $data['child_count'] =$productDetails['data']['child_count'];
            }
            
            if(!empty($productDetails['data']['deductable'])){
                asort($productDetails['data']['deductable']);
                $data['deductable'] =$productDetails['data']['deductable'];
            }
            
            $plan_id = encrypt_decrypt_password($data['plan_id'], 'D');
            $CheckCDThreshold = CheckCDThreshold($partner_id,$plan_id);
            $data['error_cd']=$CheckCDThreshold;
            $qu=$this->db->query("select * from link_ui_configuaration where plan_id=".$plan_id);
            if($this->db->affected_rows() > 0){
               $re_qu=$qu->result_array();
                $this->session->set_userdata('linkUI_configuaration', $re_qu);
            }
        }
//print_r($_REQUEST);die;
        $get_data = curlFunction(SERVICE_URL . '/customer_api/get_family_construct_data', $data);
    //   print_r($get_data);die;
        $get_data = json_decode($get_data, TRUE);
       
        $data['family_construct_arr'] = $get_data;
        //echo "<pre>";
        //print_r($get_data);
        foreach($get_data as $single_data){
            if(strtolower($single_data['member_type']) == "son"){
                $son_id = $single_data['id'];
            }

            if(strtolower($single_data['member_type']) == "daughter"){
                $daughter_id = $single_data['id'];
            }
        }


        $data['son_id'] = $son_id;
        $data['daughter_id'] = $daughter_id;
        $this->load->view('template/customer_portal_header.php');
        $this->load->view('customerportal/index',$data);
        $this->load->view('template/customer_portal_footer.php');

       
    }

    public function check_otp_abc()
    {//echo 123;die;
        //print_r($_SESSION);die;
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
     $lead_id_enc = $this->session->userdata('lead_id');
        $lead_id =  encrypt_decrypt_password($lead_id_enc, 'D');
        $otp = $this->input->post('otp');
        $mobileno = $this->input->post('mob_no');

        //$qr = $this->db->query('select * from  employee_details where mob_no = "'.$mobileno.'"');

        $query = $this->db->query("select * from lead_details where mobile_no = '$mobileno' and lead_id = '$lead_id 'order by lead_id desc limit 1")->row_array();
//print_r($this->db->last_query());die;
        if ($query)
        {

            $qr = $this->db->query('select * from  otp_verification_new where mobile_number = "'.$mobileno.'" order by id desc limit 1')->row_array();

            $otp_check = $qr['otp'];

            if($otp_check == $otp)
            {
                //echo 123;die;
                // Submit otp validation
                /*  $canProceed = $this->abc_m->validCheckOtpAttempt($query->emp_id);
                  if($canProceed['status'] == 0){
                      $this->session->set_userdata('error', $canProceed['message']);
                      redirect($canProceed['url']);
                      // echo json_encode($res);exit;
                  }*/
                $timestamp = date('Y-m-d H:i:s', strtotime($qr['date'].'+10 minutes'));  
                if(date('Y-m-d H:i:s') > $timestamp){
                    $otpSessionData['otp_session'] = ['otp_verified' => 0];
                    $this->session->set_userdata($otpSessionData);
                    $res = ["status"=>0,"message" => "Otp expired, Try again!!", "url"=>""];
                    echo json_encode($res);exit;

                }  
                $otpSessionData['otp_session'] = ['otp_verified' => 1];
                $this->session->set_userdata($otpSessionData);

                $updateInvalidCount = $this->db->query("UPDATE otp_verification_new SET otp_verified = 1 where mobile_number = ".$mobileno);
                $res = ["status"=>1,"message" => "Success", "url"=>"/quotes/redirect_to_pg/"];

                // redirect('/quotes/redirect_to_pg/');
                echo json_encode($res);


            }
            else
            { //echo 123;die;
                 //http_response_code(401);
                 //http_response_code(401);
                 //$res = $this->abc_m->invalidCheckOtpAttempt($query->emp_id);
                 $otpSessionData['otp_session'] = ['otp_verified' => 0];
                 $this->session->set_userdata($otpSessionData);
                  $res = ["status"=>0,"message" => "Invalid OTP", "url"=>""];
                  echo json_encode($res);
            }
        }
        else
        {
            /* http_response_code(401);
             $res = ["status"=>0,"message" => "Invalid OTP", "url"=>""];
             $otpSessionData['otp_session'] = ['otp_verified' => 0];
             $this->session->set_userdata($otpSessionData);*/
        }
        //print_r($res);exit;
        //$this->session->set_userdata('error', $res['message']);
        // redirect($res["url"]);
    }

    public function refresh_captcha_abc(){
        $captcha_string = $this->generateRandomString();
        $captchaSession['abc_captcha_session'] = ['captcha_string' => $captcha_string];
        $this->session->set_userdata($captchaSession);
        $cap = common_captcha_create($captcha_string);
        echo $cap['image'];
    }
    function otp_validation($lead_id){
        //echo 123;die;
        //  $lead_id_enc = $this->session->userdata('lead_id');
        $lead_id =  encrypt_decrypt_password($lead_id, 'D');
        $get_session_data = $this->db->query("select json_session_response from application_logs where lead_id = '$lead_id' and action ='redirection_json'")->row_array();
        $sess_arr = json_decode($get_session_data['json_session_response'],true);
          //print_r($sess_arr);die;

        $this->session->set_userdata('lead_id', $sess_arr[1]);
        $this->session->set_userdata('plan_id', $sess_arr[2]);
        $this->session->set_userdata('customer_id',$sess_arr[3] );
        $this->session->set_userdata('trace_id', $sess_arr[4]);
        $this->session->set_userdata('cover',$sess_arr[0]);

        $get_data = $this->db->query("select mobile_no,email_id from lead_details where lead_id = '$lead_id'")->row_array();
        $data['mob_no'] = $get_data['mobile_no'];
        $data['email_id'] = $get_data['email_id'];

        $captcha_string = $this->generateRandomString();
        $captchaSession['abc_captcha_session'] = array('captcha_string' => $captcha_string);
        $this->session->set_userdata($captchaSession);
        $cap = common_captcha_create($captcha_string);

      //  print_r($cap);die;
        $data['captcha_image'] = $cap;
        $this->load->view('template/login_header.php');
        $this->load->view('customerportal/otp_page',$data);
         $this->load->view('template/login_footer.php');
    }
    public function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generate_otp_abc(){
        //  echo 123;die;
          //print_r($_SESSION);

       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        $lead_id = encrypt_decrypt_password($_SESSION['lead_id'],'D');
        $mobile_number=$this->input->post('mob_no');

        $email=$this->input->post('email');
        $upd = array(
            'mobile_no' => $mobile_number,
            'email_id' => $email
        );

        $this->db->where('lead_id',$lead_id);

        $this->db->update('master_customer',$upd);

        $this->db->where('lead_id',$lead_id);

        $this->db->update('lead_details',$upd);
        /*$captcha_session = $this->session->userdata('abc_captcha_session');
        $captcha_string = $captcha_session['captcha_string'];
        if($captcha_string != $this->input->post('entered_captcha')){
            $otpSessionData['otp_session'] = ['captcha_verified' => 0];
            http_response_code(401);
            $data = ["status" => "error", "message" => "Please enter correct captcha!"];
            echo json_encode($data);
            exit;
        }*/

        // echo 123;die;

        $canGenerateOtp = $this->generateOtpAttemptCheck($mobile_number);
        //  print_r($canGenerateOtp);die;
        if($canGenerateOtp['status'] == 0){
            http_response_code(401);
            echo json_encode(["status" => "failed", "message" => $canGenerateOtp['message']]);exit;
        }
        $otp = rand(1231, 7879);
        if($canGenerateOtp['status']) {

            $sms_template = $this->db->query("select * from lead_details as l join sms_template as st join master_customer as mc where l.lead_id = mc.lead_id and l.creditor_id = st.creditor_id and l.lead_id = '$lead_id'")->result_array();
              //print_r($sms_template); die;
            foreach($sms_template as $sms_id) {
                $email = $sms_id['email_id'];
                $first_name = $sms_id['first_name'];

                $sms_content = $sms_id['sms_content'];
                $sms_content = str_replace("{otp}", $otp, $sms_content);
                $sms_content = str_replace("{First_Name}", $first_name, $sms_content);

                if ($sms_id['type'] == 1) {

                    insert_application_log($lead_id, 'sms_logs_request', json_encode($sms_content), "", 123);

                    $insert_id = $this
                        ->db
                        ->insert_id();
                    $url = 'http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=366724&username=7777001974&password=Alliance@123&To=' . $mobile_number . '&Text=' . urlencode($sms_content) . '&senderid=7777001974&short=1';
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 90,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_POSTFIELDS => "",
                        CURLOPT_HTTPHEADER => array(
                            "Accept: */*",
                            "Cache-Control: no-cache",
                            "Connection: keep-alive",

                            "Content-Type: application/json",

                        ),
                    ));

                    $response = curl_exec($curl);
//print_r($response);die;

                    $err = curl_error($curl);
                    if ($err) {

                        return array(
                            "status" => "error",
                            "msg" => $err
                        );
                    } else {
                        $request_arr = ["response_data" => json_encode($otp)];
                        $this
                            ->db
                            ->where("log_id", $insert_id);
                        $this
                            ->db
                            ->update("application_logs", $request_arr);
                    }

                    curl_close($curl);
                }
                if($sms_id['type']==2)
                {
                    $this->sendMail_ic($sms_content,$email);
                }
            }


            $otpDataArr = array(
                'mobile_number' => $mobile_number,
                'generate_otp_count' => 1,
                'submit_otp_count' => 0,
                'otp' => $otp,
                'otp_verified' => 0
            );
            $insertOtpVerified = $this->db->insert('otp_verification_new', $otpDataArr);
        }
        $data = ["status" => "success", "message" => "success"];
        echo json_encode($data);

    }


    function sendMail_ic($sms_content,$email){
       // echo 123;
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Username   = 'noreply@elephant.in';                     //SMTP username
            $mail->Password   = 'dpwvzfrtjzmqlvcc';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@elephant.in', 'elephant.in');
            $mail->addAddress($email, 'Test');     //Add a recipient
//print_r($sms_content);die;
            $body="<html><body><p>$sms_content</p></body></html>";
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'OTP Verification';
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return 'Message has been sent';
            print_r($mail);
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    public function generateOtpAttemptCheck($mobile_number){
        $otpVerificationData = $this->db->query("SELECT * from otp_verification_new where mobile_number = ".$mobile_number)->row_array();
        if(!empty($otpVerificationData)){
            // $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+1 hour'));
            $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+10 minutes'));
            if($otpVerificationData['submit_otp_count'] >= 5 && (date('Y-m-d H:i:s') < $timestamp)){
                $res = ["status"=>0,"message" => "Maximum attempts limit reached, try generating OTP after sometime !!"];
            } else {
                if((date('Y-m-d H:i:s') >= $timestamp) && $otpVerificationData['submit_otp_count'] >= 5){
                    $updateInvalidCount = $this->db->query("UPDATE otp_verification_new SET submit_otp_count = 0 where mobile_number = ".$mobile_number);
                }
                $res = ["status"=>1,"message" => "OTP generated !!"];
            }
        } else {
            $res = ["status"=>2,"message" => "Generating OTP for 1st time !!"];
        }
        return $res;
    }
    public function submitLead()
    {

        if ($this->input->is_ajax_request()) {

            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $gender = $this->input->post('gender');
            $name = $this->input->post('fullname');
            $edit = $this->input->post('edit');
            $plan_id = 0;
            $creditor_id = 0;
            $journey_type = 1;
            if(!empty($this->session->userdata('partner_id_session')))
            {
                $journey_type = 2;
                $creditor_id = encrypt_decrypt_password($this->session->userdata('partner_id_session'), 'D');
            }

            if(!empty($this->session->userdata('partner_id_session')) && !empty($this->session->userdata('product_id_session')))
            {
                $journey_type = 3;
                $plan_id = encrypt_decrypt_password($this->session->userdata('product_id_session'), 'D');
            }

            
            // print_r($_POST);exit;
            if(empty($edit)){
                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createLead', [
                    'mobile' => $mobile,
                    'email' => $email,
                    'gender' => $gender,
                    'name' => $name,
                    'creditor_id'=>$creditor_id,
                    'plan_id'=>$plan_id,
                    'journey_type'=>$journey_type,
                ]);
            }
            if(!empty($edit)){
                $lead_id = $this->session->userdata('lead_id');
                $customer_id = $this->session->userdata('customer_id');
                $trace_id = $this->session->userdata('trace_id');
                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/updateLead', [
                    'mobile' => $mobile,
                    'email' => $email,
                    'gender' => $gender,
                    'name' => $name,
                    'lead_id'=>$lead_id,
                    'customer_id'=>$customer_id,
                    'trace_id'=>$trace_id,
                ]);
                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$lead_id,'dropout_page'=>1]);
            }
            // echo $this->db->last_query();exit;
            // print_r($checkDetails);exit;

            $checkDetails = json_decode($checkDetails, true);
            //print_r($checkDetails);exit;

            if ($checkDetails['status_code'] == 200) {

                $this->session->set_userdata('lead_id', $checkDetails['data']['lead_id']);
                $this->session->set_userdata('customer_id', $checkDetails['data']['customer_id']);
                $this->session->set_userdata('trace_id', $checkDetails['data']['trace_id']);
            }
            //print_r($_SESSION);exit;

            echo json_encode($checkDetails);
            exit;
        }
    }

    //upendra - update
    public function pincode_insert()
    {



        $pin_code['pin_code'] = $this->input->post('pin_code');
        $pin_code['trace_id'] = $this->session->userdata('trace_id');
        $pin_code['customer_id'] = $this->session->userdata('customer_id');
        // $pin_code['pincode'] = '1234';

        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/pincode_insert', $pin_code);
        // echo $this->db->last_query();exit;
        // print_r($checkDetails);exit;

        echo $checkDetails;
    }

    public function insert_pop_city()
    {



        $city_name['city_name'] = $this->input->post('city_name');

        // $pin_code['pincode'] = '1234';

        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/insert_pop_city', $city_name);
        // echo $this->db->last_query();exit;
        // print_r($checkDetails);exit;

        echo $checkDetails;
    }
    public function submitMembers()
    {
//echo 123;die;
        if ($this->input->is_ajax_request()) {

            $_POST['lead_id'] = $this->session->userdata('lead_id');
            $_POST['customer_id'] = $this->session->userdata('customer_id');
            $_POST['dropout_page'] = $this->input->post('dropout_page');
            $_POST['deductable'] = $this->input->post('deductable');

//             print_r($_POST);exit;

            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createMembers', $_POST);
            $checkDetails = json_decode($checkDetails, true);
//            var_dump($checkDetails);
//            exit;
            if($checkDetails['status'] == 201){
                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', $_POST);
                echo json_encode(array('status'=>201,'msg'=>$checkDetails['msg']));
                exit;
            }else{
                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', $_POST);
                echo json_encode(array('status'=>200,'msg'=>$checkDetails));
                exit;
            }
//print_r($checkDetails);die;

        }
    }
    public function getSuminsuredType(){

        $lead_id = $this->session->userdata('lead_id');
        $customer_id = $this->session->userdata('customer_id');
        $product_id = $this->session->userdata('product_id_session');
        $getSuminsuredType = curlFunction(SERVICE_URL . '/customer_api/getSuminsuredType', ['lead_id' => $lead_id,
        'customer_id' => $customer_id,'product_id'=>$product_id]);
        // echo $this->db->last_query();exit;
     //    print_r($getSuminsuredType);exit;

        //$checkDetails = json_decode($checkDetails, true);
        echo $getSuminsuredType;exit;
    
    }

    public function SubmitInsuredtype()
    {

        //if ($this->input->is_ajax_request()) {

            $SumInsuredtype = $this->input->post('SumInsuredtype');
            $deductable = $this->input->post('deductible');
            $lead_id = $this->session->userdata('lead_id');
            $customer_id = $this->session->userdata('customer_id');
            $product_id = $this->session->userdata('product_id_session');
            
            // print_r($_POST);exit;
            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createInsuredtype', [
                'SumInsuredtype' => $SumInsuredtype,
                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'deductable' => $deductable,
                'product_id' => $product_id,

            ]);
            if(!empty($deductable)){
                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$lead_id,'dropout_page'=>3]);
            }
            echo $checkDetails;
            exit;
        //}
    }
    public function getMasterDisease()
    {



        $getDisease = curlFunction(SERVICE_URL . '/customer_api/getMasterDisease', []);
        // echo $this->db->last_query();exit;
        // print_r($checkDetails);exit;

        //$checkDetails = json_decode($checkDetails, true);
        echo $getDisease;
        exit;
    }

    public function submitDisease()
    {

        if ($this->input->is_ajax_request()) {

            $disease_type = $this->input->post('disease_type');
            $lead_id = $this->session->userdata('lead_id');
            $customer_id = $this->session->userdata('customer_id');
            $disease_check = $this->input->post('disease_check');

            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createDisease', [
                'disease_type' => $disease_type,
                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'disease_check' => $disease_check

            ]);
            // echo $this->db->last_query();exit;
            // print_r($checkDetails);exit;

            //$checkDetails = json_decode($checkDetails, true);
            echo $checkDetails;
            exit;
        }
    }
    function save_Proposal_api(){
       // print_r($_POST['json_cust_data']);die;
       // echo 123;die;
        $json_cust_data=$this->input->post('json_cust_data');
        $api_data= json_decode($json_cust_data,true);
       // print_r($api_data);die;
        $sum_insured=$api_data['QuoteRequest']['SumInsuredData'][0]['SumInsured'];
        $adult_count=$api_data['QuoteRequest']['adult_count'];
        $child_count=$api_data['QuoteRequest']['child_count'];
        $plan=$api_data['ClientCreation']['plan'];
        $plan_id=$this->db->query("select plan_id from master_plan where plan_name='".$plan."'")->row()->plan_id;
        $policy_id=$this->db->query("select policy_id from master_policy where plan_id='".$plan_id."'")->row()->policy_id;
        //echo $policy_id;
        $all_dob=array();
        foreach ($api_data['MemObj']['Member'] as $member){
            $all_dob[]=$member['DateOfBirth'];
            $from = new DateTime($member['DateOfBirth']);
            $to   = new DateTime('today');
            $all_age[]= $from->diff($to)->y;
        }
        $age=max($all_age);
       // print_r($all_age);die;

        $query=$this->db->query("select * from master_policy_premium where master_policy_id=".$policy_id." AND min_age <= ".$age." AND max_age >=".$age.
            " AND  sum_insured = ".$sum_insured."  AND adult_count = " . $adult_count. " AND child_count = ".$child_count. " And isactive=1");
        if($this->db->affected_rows() > 0){

        }else{
            echo "Premium Not available.Please check age of the members, adult count and child count.";
            die;
        }
        $result1 = curlFunction(SERVICE_URL . '/api2/saveProposalapi', array('json_cust_data'=>$json_cust_data));
        //print_r($result1);die;
        $result=json_decode($result1);
        if($result->success == true){
            $this->session->set_userdata('lead_id', $result->session_arr[1]);
            $this->session->set_userdata('plan_id', $result->session_arr[2]);
            $this->session->set_userdata('customer_id',$result->session_arr[3] );
            $this->session->set_userdata('trace_id', $result->session_arr[4]);
            $this->session->set_userdata('cover', $result->session_arr[0]);
            redirect('/customerportal/otp_validation/'.$result->session_arr[1]);
        }else{
            print_r($result);die;
        }
    }

    function generate_token(){
        $json_cust_data=$this->input->post('json_cust_data');
        $api_data=json_decode($_POST['json_cust_data'],true);
        $result1 = curlFunction(SERVICE_URL . '/api/generateToken', array('json_cust_data'=>$json_cust_data));
       $result1=json_decode($result1,true);
       echo "Token : ".$result1['utoken'] . "<br> User ID: ".$result1['user_id'];
       exit;
    }
    public function updateLeadLastVisited(){
       
        $lead_id = $this->session->userdata('lead_id');
        curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$lead_id,'dropout_page'=>2]);
        
    }


    
}
