<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Abml_json_redirection extends CI_Controller {
	
    public $secureKey;
    public $iv;
    public $algoMethod;
    public $hashMethod;
    public $productCode = 'ABML';
    public $aFields;
    function __construct() {
        parent::__construct();
        $this->config->set_item('csrf_protection', false);
        $this->db = $this->load->database('axis_retail', TRUE);
        $this->load->helper('url');
        $this->secureKey = "694bec96439fe598be98a0afc401c891";
        $this->iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $this->algoMethod = 'AES-256-CBC';
        $this->hashMethod = 'SHA384';
        $this->productCode = "ABML"; //Do not change this value here as this is mapped with database table
		// Added By Shardul
		$this->load->model('cron/cron_m');
		$this->load->model('Logs_m');
        $this->load->model('Retail/Dashboard_m','Dashboard_m'); 
		$this->load->model('Redirect_invalid_data_m');
		$this->load->model("API/Payment_integration_retail", "obj_api", true);
    }

    public function index() {
        if (isset($_SESSION)){
            session_unset();
            // session_destroy();
        }
        $checksum = 0;
        $jsonData = '';
        $encryptedJSONData = '';
        $this->load->view("abml_redirection", compact('checksum', 'jsonData', 'encryptedJSONData'));
    }

    public function validateMobilePan() {
        $message = '';
        $this->load->abc_portal_template('validate_mobile_pan', compact('message'),true,'ABC');
    }

    public function validate_data() {
        if (isset($_SESSION)){
            session_unset();
            // session_destroy();
        }
        $aData = $this->input->post('json_cust_data');
        $postDataArr = json_decode($aData,true);
        $result = [];
        foreach ($postDataArr as $key => $value) {
            $result[$key] = trim($value);
        }
        $this->aFields = $result;
        $isValid = $this->is_valid_data();

        if($isValid['success']){
            // Proceed with redirection
            $this->create_customer();
        } else {
            $errors = $isValid['errors'];
            $this->load->abc_portal_template('redirection_msgs', compact('errors'),true,'ABC');
        }
    }

    public function create_customer(){
        // Check customer already exists on basis of mobile number & productId
        $alreadyExistCheck = $this->db->select("emp_id,lead_id,pan_verified")
                            ->from("employee_details ed")
                            ->where("ed.product_id", $this->productCode)
                            ->where("ed.mob_no", $this->aFields['mobile'])
                            ->where("ed.pancard", $this->aFields['pan'])
                            ->limit(1)
                            ->get()
                            ->row_array();

        if (!empty($alreadyExistCheck)) {           
            //already lead id record found for this combine id
            //redirect to messages for continue to existing lead ID or create new ID.
            $existingLeadID = $alreadyExistCheck['lead_id'];
            $cust_data_json = json_encode($this->aFields);

            $data = array(
                "lead_id" => $existingLeadID,
                'emp_id'=>$alreadyExistCheck['emp_id'],
                'pan_verified'=>$alreadyExistCheck['pan_verified']
            );
            $this->load->abc_portal_template('validate_mobile_pan', compact('data'),true,'ABC');
        } else {            
            $this->insert_customer_data();
        }
    }


    public function insert_customer_data(){
        // print_pre('Inside insert customer data');exit;
        // Add record in employee_details and redirect
        $lead_id = $this->generate_lead_id();
        $aJsonField = $this->aFields;
        // Add mandatory fields which are not present, passing static as of now.
        // $aFields['salutation'] = 'Mr';
        // $aFields['gender'] = $this->getGenderFromSalutation($aFields['salutation']);
        $customerName = $this->aFields['nameonpan'];
        $aFields['customer_name'] = preg_replace('/\s+/', ' ',mb_convert_case($customerName, MB_CASE_TITLE, "UTF-8"));
        // $dtformat = 'Y-m-d H:i:s';
        // if (!empty($this->aFields['timestamp'])) {
        //     $this->aFields['timestamp'] = date($dtformat, $this->aFields['timestamp']);
        // }

        $aEmpData = ["ref1" => '', "ref2" => '', "branch_sol_id" => '', "lead_id" => $lead_id, "salutation" => null, "product_id" => $this->productCode, "customer_name" => $aFields['customer_name'], "gender" => null, "bdate" => date('d-m-Y', strtotime($this->aFields['dob'])), "mob_no" => $this->aFields['mobile'], "access_right_id" => '2', "module_access_rights" => '1,8', "email" => $this->aFields['email'], "pancard" => $this->aFields['pan'], "address" => $this->aFields['address'], "emp_city" => $this->aFields['city'], "emp_state" => $this->aFields['state'], "emp_pincode" => $this->aFields['pincode'], "nationality" => '', "marital_status" => $this->aFields['maritalstatus'], "occupation" => $this->aFields['occupation'], "timestamp" => "", "website_ref_no" => '', "source_id" => '', "referral_code" => '', "Product_Name" => $this->productCode, "Partner_Name" => $this->productCode, "UTM_Source" => '', "UTM_Medium" => '', "UTM_Campaign" => '', "Producer_Code" => $this->productCode, "cust_id" => '', "json_qote" => json_encode($aJsonField),"created_at" => date('Y-m-d H:i:s'), "ebcc_flag" => '', "scheme_code" => '', "ckyc_no" => '', "acc_no" => '', "acc_type" => ''];

        $this->db->set($aEmpData);
        $this->db->insert("employee_details", $aEmpData);

        // Inserting record in family_relation table
        $emp_id = $this->db->insert_id();

        $data = $this->db->select("emp_id,lead_id,pan_verified")
                        ->from("employee_details ed")
                        ->where("ed.emp_id", $emp_id)
                        ->limit(1)
                        ->get()
                        ->row_array();

        $seconds = 30;
        $date_now = date("Y-m-d H:i:s");
        $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
        $this->db->where("emp_id",$emp_id);
        $this->db->update("employee_details",['modified_date' => $moddate]);
        $this->db->insert("family_relation", ["emp_id" => $emp_id, "family_id" => 0]);

        /* Update lead_id in logs*/
        // $this->updateLeadIdInLogs($lead_id);

        // Checking for existing user_activity
        $check_query = $this->db->select("*")->from("user_activity_abc")->where('emp_id',$emp_id)->get()->row_array();
        if(empty($check_query)){
            $act_arr = ["emp_id" => $emp_id, "type" => "1", "updated_time" => date("Y-m-d H:i:s")];
            $this->db->insert("user_activity_abc", $act_arr);
        }else{
            $request_arr = ["type" => "1"];
            $this->db->where("emp_id",$emp_id);
            $this->db->update("user_activity_abc",$request_arr);
        }
        $emp_id = encrypt_decrypt_password($emp_id);

        $this->load->abc_portal_template('validate_mobile_pan', compact('data'),true,'ABC');
    }


    /* continue with existing lead automatic start */

    function continue_with_existing_lead_auto($aData) {
        // /*  user will come when passing values like leadid already exist or customer_name, 
        //   mobile_number, product_name are already exist in database.
        //  */
        // $aCustJSON = json_decode($aData['cust_data_json'], true);
        // $lead_id = $aData['lead_id'];
        // /*
        //   check if lead id exist in database (i.e. check if seesion value not manupulated)
        //   if not exist then it means we can not continue with this lead id for further.
        //   i.e. Proposal does not exist!!
        // */
        // $rsEmp = $this->db->select("emp_id, product_id, json_qote")->where(["lead_id" => $lead_id,'product_id'=>$this->productCode])->get("employee_details");

        // if ($rsEmp->num_rows() <= 0) {
        //     $message = "No lead data found";
        //     $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
        // } else {
        //     //insert log
        //     $aRow = $rsEmp->row();

        //     /* Update new token in employee_details json */
        //     $existingjsonQuote = $aRow->json_qote;
        //     $newArr = json_decode($existingjsonQuote,true);
        //     // $newArr['token'] = '123456789';
        //     $newArr['token'] = $aData['newToken'];


        //     $this->db->where("lead_id",$lead_id);
        //     $this->db->where("product_id",$this->productCode);
        //     $updateToken = $this->db->update('employee_details',['json_qote'=>json_encode($newArr)]);

        //     $arr = ["emp_id" => $aRow->emp_id, "posted_new_lead_data" => $aCustJSON, "existing_leadid" => $lead_id];
        //     $logs_type = "cont_with_existing_journey_auto";
        //     $this->logs_post_data_insert($lead_id, $arr, $logs_type);

        //     $this->redirect_to_dropoff_page($lead_id);
        // }
    }

    function is_valid_data() {
        $is_valid = 1;
        $aCombineErr = array();
        $custFacingErrMsg = "";
        $dobErrMsg = "";
        //$pincodeErrMsg = "";
        //$emailErrMsg = "";
        $message = "";
        $otherErrMsg = "";
        $error_messages = [];
        $mis_error_message_type = array();

        $mandatoryCustomerFacingFields = ["nameonpan", "dob", "mobile", "pan"];        

        foreach ($mandatoryCustomerFacingFields as $field){
            if (!(isset($this->aFields[$field]) && trim($this->aFields[$field]) !== "")){
                $error_messages[] = $field . ' is mandatory';
            }
        }

        // Valid Mobile Number
        if(!empty($this->aFields['mobile'])){
            if ((!$this->isValidMobileNumber($this->aFields['mobile']))) {
                $error_messages[] = "Please enter valid mobile number.";
            }
        }


        if(!empty($this->aFields['email'])){
            if (!filter_var($this->aFields['email'], FILTER_VALIDATE_EMAIL)) {
                $error_messages[] = "Please enter valid email address.";
            }
        }

        if(!$this->isStringValidWithLetterAndSpacesApostrophe($this->aFields['nameonpan'])) {
            $error_messages[] = "Customer First Name should not contain numbers and special characters other than apostrophe like D'souza";
        }

        if (!$this->isValidAge($this->aFields['dob'])) {
            $error_messages[] = "Sorry this plan covers adults from the age of 18 years to 60 years. Please visit nearest Axis Bank Branch for a suitable plan.";
        }

        if (!empty($this->aFields['pincode'])) {
            if (is_numeric($this->aFields['pincode'])) {
                $aPinData = $this->db->select('state,city,state_code')->from('axis_postal_code as pc')->where('pc.pincode', $this->aFields['pincode'])->get()->row();
                if (!empty($aPinData)) {
                    $City = $this->aFields['city'] = $aPinData->city;
                    $State = $this->aFields['state'] = $aPinData->state;
                } else {
                    $error_messages[] = "Invalid PIN Code.";
                }
            }
        }

        if(!$this->isValidPANCard($this->aFields['pan'])){
            $error_messages[] = "Invalid PAN Card Number";
        }        

        /* Pan & Mobile Combination miss match error */
        $checkMobilePanMissmatch = $this->panMobileCombinationMissmatchCheck($this->aFields['mobile'], $this->aFields['pan']);
        if(!$checkMobilePanMissmatch['status']){
            $error_messages[] = $checkMobilePanMissmatch['message'];
        }

        /* New Validation Layer End*/      
        // print_pre($otherErrMsg);

        if(count($error_messages) == 0){
            return ['success'=>true];
        } else {
            return ['success'=>false, 'errors'=>$error_messages];
        }
    }

    public function panMobileCombinationMissmatchCheck($jsonMobile, $jsonPan){
        // Get existing record with mobile 
        $employeeHavingMobile = $this->db->get_where("employee_details",array("mob_no" => $jsonMobile, "product_id"=>$this->productCode))->row_array();

        if(!empty($employeeHavingMobile) && $employeeHavingMobile['pancard'] != $jsonPan){
            return ["status" => 0, "message" => "This mobile number is already associated with a different PAN."];
        }
        // Get existing record with mobile 
        $employeeHavingPan = $this->db->get_where("employee_details",array("pancard" => $jsonPan, "product_id"=>$this->productCode))->row_array();

        if(!empty($employeeHavingPan) && $employeeHavingMobile['mob_no'] != $jsonMobile){
            return ["status" => 0, "message" => "This PAN is already associated with a different mobile number."];
        }
        return ["status" => 1, "message" => "Can proceed !!"];
    }

    function isValidMobileNumber($mobileNumber) {
        $mobileregex = "/^[6-9][0-9]{9}$/" ;
        return preg_match($mobileregex, $mobileNumber);
    }

    function isStringValidWithLetterAndSpacesApostrophe($string) {
        //It should not accept special characters other than apostrophe.
        if (preg_match('/^[a-zA-Z.\' ]+$/', $string)) {
            return true;
        }
        return false;
    }

    function isValidPANCard($string) {
        //is Pan Card valid
        if (strlen($string) != 10)
            return false;
        //PAN number should in its format.("BEDFG4563K")
        $pattern = '/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/';
        $result = preg_match($pattern, $string);
        if ($result) {
            return true;
        }
        return false;
    }

    function isValidAge($dob) {
        $dateOfBirth = date("Y-m-d", strtotime($dob));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        if ($diff->format('%y') < 18 || $diff->format('%y') > 60) {
            return false;
        }
        return true;
    }

    public function generate_lead_id()
    {
        $lead_id = time();
        $EmployeeData = $this
            ->db
            ->get_where("employee_details", array(
            "lead_id" => $lead_id
        ))->row_array();
        if (!empty($EmployeeData))
        {
            $this->generate_lead_id();
        }
        return $lead_id;
    }

    function setCustomerSession($abmlSession) {
        //unset previous user data from session.    
        if ($this->session->userdata('abc_session')) {
            $this->session->unset_userdata('abc_session');
        }
        //set new user data in session.     
        $this->session->set_userdata($abmlSession);
        /* Regenerate a new session upon successful authentication. Any session token used prior to 
          login should be discarded and only the new token should be assigned for the user till the user
          logs out.
          This session token should be properly expired when the user logs out. */
        $this->session->regenerate_id();
        $session_id = session_id();
        $abmlSession = $this->session->userdata('abc_session');
        $emp_id = encrypt_decrypt_password($abmlSession['emp_id'], 'D');
        $rsEmp = $this->db->select("id, updated_time")->where(["emp_id" => $emp_id])->get("tbl_leadid_session");
        if ($rsEmp->num_rows() > 0) {
            //update record         
            $aRow = $rsEmp->row();
            $id = $aRow->id;

            $data = array(
                'sessionid' => $session_id,
                'updated_time' => time(),
            );
            $this->db->where('id', $id);
            $this->db->update('tbl_leadid_session', $data);
        } else {
            $aLeadSession = ["emp_id" => $emp_id, "sessionid" => $session_id, "updated_time" => time()];
            $this->db->insert("tbl_leadid_session", $aLeadSession);
            
        }
        /* Added By Shardul Kulkarni on 07-08-2020 for making dropoff_flag value to 0 Start */
        $this->updateDropOffFlagValue($emp_id);
        /* Added By Shardul Kulkarni on 07-08-2020 for making dropoff_flag value to 0 End */
    }


    function retrun_journey_url($lead_id) {

        $rsEmp = $this->db->select("emp_id, product_id, lead_id, mob_no")->where(["lead_id" => $lead_id])->get("employee_details");
        $aRow = $rsEmp->row();
        $emp_id = $aRow->emp_id;
        
        $emp_id_encrypt = encrypt_decrypt_password($emp_id);
        //echo $emp_id_encrypt;exit;
        $savedData = $this->db->get_where("employee_product_details",array("emp_id" => $emp_id))->row_array();
       
        $policy_detail_id_encrypt = '';
        if(!empty($savedData)){
            $policy_detail_id_encrypt = encrypt_decrypt_password($savedData['policy_id']);
        }
        //echo $savedData['policy_id'].'------'.$policy_detail_id_encrypt;exit;
        //set user session then redirect
        $aD2CSession['abc_session'] = array(           
            'emp_id' => $emp_id_encrypt,
            'product_id' => $this->productCode,
            'lead_id' => $aRow->lead_id,
            'mob_no' => $aRow->mob_no
        );
        $this->setCustomerSession($aD2CSession);

        $sqlStr = " select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id,pmws.policy_parent_id
                    FROM employee_details as ed INNER JOIN user_activity_abc as ua
                    ON ed.emp_id = ua.emp_id AND ua.emp_id = $emp_id
                    INNER JOIN product_master_with_subtype as pmws ON ed.product_id = pmws.main_product_name";

        //echo $sqlStr;exit;
        $aResult = $this->db->query($sqlStr)->row_array();

        $logs_type = "dropoff_journey_return_page";
        $activityType = $aResult['type'];
        $arr = ["emp_id" => $emp_id, "type" => $activityType];
        switch ($activityType) {
            case 1:
                $arr['link'] = 'comprehensive_products';
                break;
            case 2:
                $arr['link'] = 'generate_quote/'.$policy_detail_id_encrypt;
                break;
            case 3:
                $arr['link'] = 'member_proposer_detail';
                break;
            case 4:
                $arr['link'] = 'member_review';
                break;
            case ($activityType == 5 || $activityType == 6):
                $arr['link'] = 'payment_redirection_abc';
                break;
            case 7:
                $arr['link'] = 'success_view/' . $emp_id;
                break;
        }

        // $this->logs_post_data_insert($lead_id, $arr, $logs_type);

        if ($dropff_return_json) {
            return '1';
        } else {
            return $arr['link'];
        }
    }
    

    /**
     * This Function is use for Updating dropoff_flag value to 0
     * @param : $emp_id
     * @author Shardul Kulkarni<shardul.kulkarni@fyntune.com>
     */ 
    public function updateDropOffFlagValue($emp_id = 0) {
        if(!empty($emp_id)) {
            $seconds = 30;
            $date_now = date("Y-m-d H:i:s");
            $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
            $request_arr_dropoff = ["dropoff_flag" => "0", 'modified_date'=>$moddate];
            $this->db->where("emp_id",$emp_id);
            $this->db->update("employee_details", $request_arr_dropoff);
        }      
    }

    public function sendOtp(){
        $mobNo = $_POST['mob_no'];
        $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));

        $this->db->where(['mob_no' => $mobNo, 'product_id' => $this->productCode]);
        $employeeDetail = $this->db->get('employee_details')->row_array();

        $otp = '1234';

        if(empty($employeeDetail)){
            http_response_code(401);
            echo json_encode(["status" => "failed", "message" => "Mobile number doesn't exist in records !!"]);
        } else {
            $otpData = ["abc_otp" => $otp, 'modified_date'=>$moddate];

            $this->db->where("emp_id",$employeeDetail['emp_id']);
            $this->db->update("employee_details", $otpData);

            $this->session->set_userdata('otp_code',$otp);   // Store OTP in session

            if($employeeDetail['pan_verified'] == 1){
                $panVerificationRequired = 0;
            } else {
                $panVerificationRequired = 1;
            }

            //Insert Send OTP Log
            $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $this->productCode, "req" => "send validation otp" , "res" => "Success" , "type" => "customer_validation_send_otp"];
            
            $dataArray['tablename'] = 'logs_docs';
            $dataArray['data'] = $request_arr;

            $this
                ->Logs_m
                ->insertLogs($dataArray);
                
            echo json_encode([
                "status" => "success", 
                "message" => "OTP sent successfully!!", 
                "pan_verification_required" => $panVerificationRequired
            ]);
        }   
    }

    public function checkOtp(){
        $mobNo = $_POST['mobile_no'];
        $otpEntered = $_POST['otp'];

        $this->db->where(['mob_no' => $mobNo, 'product_id' => $this->productCode]);
        $employeeDetail = $this->db->get('employee_details')->row_array();
        $emp_id = $employeeDetail['emp_id'];

        if(!empty($employeeDetail)){
            $otpInDb = $employeeDetail['abc_otp'];
            if($otpEntered == $this->session->userdata('otp_code')){
                $this->session->unset_userdata('otp_code');
                //Insert Send OTP Log
                $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $employeeDetail['product_id'], "req" => "otp verification" , "res" => "Success" , "type" => "customer_validation_verify_otp"];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

                $this->db->where('emp_id', $emp_id);
                $this->db->update('employee_details', ['otp_verified'=>1]);

                if($employeeDetail['pan_verified'] == 1){
                    $panVerificationRequired = 0;
                    // GET user activity data
                    $url = $this->retrun_journey_url($employeeDetail['lead_id']);
                } else {
                    $panVerificationRequired = 1;
                }

                http_response_code(200);

                echo json_encode([
                    "status"=>"success",
                    "message"=>"Success",
                    "pan_verification_required"=>$panVerificationRequired,
                    "url"=>$url
                ]);

            } else {
                $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $employeeDetail['product_id'], "req" => "otp verification" , "res" => "Failed" , "type" => "customer_validation_verify_otp"];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

                http_response_code(401);
                echo json_encode(["status"=>"invalid","message"=>"Invalid OTP"]);
            }
        } else {
            $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $employeeDetail['product_id'], "req" => "otp verification" , "res" => "Failed" , "type" => "customer_validation_verify_otp"];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

            http_response_code(401);
            echo json_encode(["status"=>"failed", "message"=>"Record not found !!"]);
        }
    }

    public function checkPan(){
        $mobNo = $_POST['mob_no'];
        $panEntered = $_POST['pan'];

        $this->db->where(['mob_no' => $mobNo, 'product_id' => $this->productCode]);
        $employeeDetail = $this->db->get('employee_details')->row_array();
        $emp_id = $employeeDetail['emp_id'];

        if(!empty($employeeDetail)){
            $panInDb = $employeeDetail['pancard'];
            if($panEntered == $panInDb){
                $this->session->unset_userdata('otp_code');
                //Insert Send OTP Log
                $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $employeeDetail['product_id'], "req" => "pan verification" , "res" => "Success" , "type" => "customer_validation_verify_pan"];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

                $this->db->where('emp_id', $emp_id);
                $this->db->update('employee_details', ['pan_verified'=>1]);

                // GET user activity data
                $url = $this->retrun_journey_url($employeeDetail['lead_id']);

                http_response_code(200);
                echo json_encode(["status"=>"success","message"=>"Success","url"=>$url]);
            } else {
                $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $employeeDetail['product_id'], "req" => "pan verification" , "res" => "Failed" , "type" => "customer_validation_verify_pan"];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

                http_response_code(401);
                echo json_encode(["status"=>"invalid","message"=>"Invalid PAN"]);
            }
        } else {
            $request_arr = ["lead_id" => $employeeDetail['lead_id'], "product_id" => $employeeDetail['product_id'], "req" => "pan verification" , "res" => "Failed" , "type" => "customer_validation_verify_pan"];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

            http_response_code(401);
            echo json_encode(["status"=>"failed", "message"=>"Record not found !!"]);
        }
    }

}
