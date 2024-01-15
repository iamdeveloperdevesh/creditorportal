<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class JsonRedirectionApi extends CI_Controller {
	
    public $secureKey;
    public $iv;
    public $algoMethod;
    public $hashMethod;
    public $productNameMatchWithAxis; //Group Active Health Plan , previous in sample post data=>ABHI Axis Freedom Plus Plan
    public $productCode;
    public $tokenId;
    public $urlParams = [];
    //all json params 
    public $aFields = array("salutation", "customer_name", "address", "city", "pin_code", "state", "dob", "gender", "mobile_number", "nationality", "email_address", "marital_status", "occupation_type", "pan_number", "nominee_name", "nominee_dob", "relationship_of_nominee", "timestamp", "website_ref_no", "crm_lead_id", "source_id", "referral_code", "product_name", "partner_name", "utm_source", "utm_medium", "utm_campaign", "sp_code", "lg_sol_id", "producer_code", "ebcc_flag", "scheme_code", "ckyc_no", "acc_no", "acc_type");

    public $aFieldsNew = ['customerid','first_name','middle_name','last_name','email','dob','address1','address2','address3','post_office','pin_code','district','state','mobile_number'];

    public $apiCustomerData;

    function __construct() {
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
        parent::__construct();
        $this->db = $this->load->database('axis_retail', TRUE);
        $this->load->helper('url');

        $this->secureKey = "694bec96439fe598be98a0afc401c891";
        $this->iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $this->algoMethod = 'AES-256-CBC';
        $this->hashMethod = 'SHA384';
        $this->productNameMatchWithAxis = "group active health plan"; //make sure lower case while comparing
        $this->productCode = "MUTHOOT"; //Do not change this value here as this is mapped with database table
        $this->aFields = array_fill_keys($this->aFields, '');
        $this->aFieldsNew = array_fill_keys($this->aFieldsNew, '');
		// Added By Shardul
		$this->load->model('cron/cron_m');
		$this->load->model('Logs_m');
		$this->load->model('Redirect_invalid_data_m');
		$this->load->model("API/Payment_integration_retail", "obj_api", true);
    }

    public function index() {
        $errors = [];

        $data['token'] = $_REQUEST['token'];
        $data['customerId'] = $_REQUEST['customer_id'];
        $data['created_thru'] = $_REQUEST['created_thru'];
        $dev_mode = $_REQUEST['dev_mode'];
        $data['productId'] = $this->productCode;
        $checkTokenStatus = null;
        if($dev_mode ==1){

                $mobileNumber = $_REQUEST['mbl'];
                $testJsonResponse = '{"Response":[{"ERROR_CODE":"0","ERROR_MSG":"Success"}],"ResultSet":[{"CUSTOMERID":'.$data['customerId'].',"FIRST_NAME":"ARUN","MIDDLE_NAME":"TEST","LAST_NAME":"CUSTOMER","EMAIL":"sarath@emsyne.com","DOB":"1981-03-06T00:00:00","ADDRESS1":"3/46 Ram nagar 2nd Street North Extension","ADDRESS2":"Vijayanagar 3rd main road Stores","ADDRESS3":" ","POST_OFFICE":"LOS","PIN_CODE":"600042","DISTRICT":"KANNUR","STATE":"KERALA","MOBILE_NUMBER":"'.$mobileNumber.'","WHATSAPP_NUMBER":null,"WHATSAPP_OPTIN_STATUS":1}]}';
                 //   var_dump($testJsonResponse);exit;
              //  $fetchCustomerDetails = utf8_decode($testJsonResponse);
                $fetchCustomerDetails = json_decode($testJsonResponse,true);

//var_dump($fetchCustomerDetails);exit;
//                var_dump(json_last_error());
                $customerDetailArr = $fetchCustomerDetails['ResultSet'][0];

                $this->apiCustomerData = $customerDetailArr;
               // var_dump($this->apiCustomerData);
                $this->urlParams = $data;
                $this->redirectCustomer();
                exit;

        }
        $checkTokenStatus = $this->checkTokenStatus($data);
        if($checkTokenStatus){
            $checkTokenStatus = json_decode($checkTokenStatus,true);
            if($checkTokenStatus['Response'][0]['ERROR_CODE'] == '0' && $checkTokenStatus['Response'][0]['ERROR_MSG'] == 'Success'){
                $this->tokenId = $data['token'];
                $fetchCustomerDetails = $this->fetchCustomerDetails($data);
                if($fetchCustomerDetails){
                    $fetchCustomerDetails = json_decode($fetchCustomerDetails,true);
                    if($fetchCustomerDetails['Response'][0]['ERROR_CODE'] == '0' && $fetchCustomerDetails['Response'][0]['ERROR_MSG'] == 'Success'){
                        $customerDetailArr = $fetchCustomerDetails['ResultSet'][0];
                        $this->apiCustomerData = $customerDetailArr;
                        $this->urlParams = $data;
                        $this->redirectCustomer();
                    } else {
                        $errors['fetchCustomerDetails'] = "Couldn't fetch customer details, please pass correct details in URL!!";
                    }
                }
            } else {
                $errors['checkTokenStatus'] = "Couldn't validate token & customer ID, please pass correct details in URL!!";
            }
        } else {
            $errors['checkTokenStatus'] = "Couldn't validate token!!";
        }
        if(count($errors) > 0){
            $this->load->abc_portal_template('redirection_msgs', compact('errors'),true,$this->productCode);
        }
    }

    public function checkTokenStatus($data){
        if($data['token'] && $data['customerId']){
            $url = 'http://59.145.109.140:14340/Retrieve.ashx/CheckTokenStatus';
            $checkTokenStatusReqArr = [
                "retrievalJson"=>[
                    "MethodName"=>"CheckTokenStatus",
                    "Token"=>$data['token'],
                    "CustomerId"=>$data['customerId'],
                    "ClientCode"=>"ABI",
                    "ApiVersion"=>"1"
                ]
            ];
            $checkTokenStatusCall = $this->curlCall($url,$checkTokenStatusReqArr,"POST");
            // Store Log in logs docs 
            $request_arr = ["lead_id" => $data['customerId'], "product_id" => $data['productId'], "req" => json_encode($checkTokenStatusReqArr) , "res" => json_encode($checkTokenStatusCall) , "type" => "check_token_status"];
            $dataArray['tablename'] = 'logs_docs';
            $dataArray['data'] = $request_arr;
            $this
                ->Logs_m
                ->insertLogs($dataArray);
            // STore Local log ends

            // return $checkTokenStatusCall['response'];
            
            /* Static response for test purpose */
            $testResponse = '{"Response":[{"ERROR_CODE":"0","ERROR_MSG":"Success"}]}';
            //$testResponse = '{"Response":[{"ERROR_CODE":"1","ERROR_MSG":"Failed"}]}';
            return ['response'=>json_decode($testResponse,true),'error'=>'0'];
        } else {    
            return false;
        }
    }

    public function fetchCustomerDetails($data){
        if($data['token'] && $data['customerId']){
            $url = 'http://59.145.109.140:14340/Retrieve.ashx/FetchCustomerDetails';
            $fetchCustomerDetailsArr = [
                "retrievalJson"=>[
                    "MethodName"=>"FetchCustomerDetails",
                    "Token"=>$data['token'],
                    "CustomerId"=>$data['customerId'],
                    "ClientCode"=>"ABI",
                    "ApiVersion"=>"1"
                ]
            ];
            $fetchCustomerDetailsCall = $this->curlCall($url,$fetchCustomerDetailsArr,"POST");

            // Store Log in logs docs 
            $request_arr = ["lead_id" => $data['customerId'], "product_id" => $data['productId'], "req" => json_encode($fetchCustomerDetailsArr) , "res" => $fetchCustomerDetailsCall['response'] , "type" => "fetch_customer_detail"];
            $dataArray['tablename'] = 'logs_docs';
            $dataArray['data'] = $request_arr;
            $this
                ->Logs_m
                ->insertLogs($dataArray);
            // Store Local log ends

            return $fetchCustomerDetailsCall['response'];

            /* Static response for test purpose */
            $testResponse = '{"Response":[{"ERROR_CODE":"0","ERROR_MSG":"Success"}],"ResultSet":[{"CUSTOMERID":152102,"FIRST_NAME":"Nelson","MIDDLE_NAME":"T","LAST_NAME":"Joseph","EMAIL":"nelson@emsyne.com","DOB":"1988-05-18T00:00:00","ADDRESS1":"Emsyne","ADDRESS2":"8769","ADDRESS3":"Emsyne","POST_OFFICE":"CHIDAMBARAM WEST S.O","PIN_CODE":"608001","DISTRICT":"ERODE","STATE":"TAMIL NADU","MOBILE_NUMBER":"9061834297"}]}';
            return ['response'=>json_decode($testResponse,true),'error'=>'0'];
        } else {    
            return false;
        }
    }

    public function curlCall($url,$requestArr,$methodType,$headerArr = ["Accept: */*", "Cache-Control: no-cache", "Connection: keep-alive", "Content-Type: application/json"]){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $methodType,
            CURLOPT_POSTFIELDS => json_encode($requestArr),
            CURLOPT_HTTPHEADER => $headerArr,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return ['response'=>$response,'error'=>$err];

        /* Static response for test purpose */
        // return $response;
    }

    public function redirectCustomer(){
        $apiCustomerData = $this->apiCustomerData;
        
        $aJSONData = [];
        foreach ($apiCustomerData as $key => $value) {
            $aJSONData[strtolower($key)] = $value;
        }
        $aJSONData = array_map('trim', $aJSONData);                  
        foreach ($aJSONData as $key => $val) {
            $this->aFieldsNew[$key] = $val;
        }
        // print_pre(date("d-m-Y", strtotime($customerData['dob'])));
        $validData = $this->isValidApiCustomer();

        if($validData['success']){
            // Proceed with redirection
            $this->create_customer_new();
        } else {
            $errors = $validData['errors'];
            $this->load->abc_portal_template('redirection_msgs', compact('errors'),true,$this->productCode);
        }
    }

    public function create_customer_new(){
        // Check customer already exists on basis of mobile number & productId
        $alreadyExistCheck = $this->db->select("emp_id,lead_id")
                                ->from("employee_details ed")
                                ->where("ed.product_id", $this->productCode)
                                ->where("ed.mob_no", $this->aFieldsNew['mobile_number'])
                                ->limit(1)
                                ->get()
                                ->row_array();

        if (!empty($alreadyExistCheck)) {           
            //already lead id record found for this combine id
            //redirect to messages for continue to existing lead ID or create new ID.
            $existingLeadID = $alreadyExistCheck['lead_id'];
            $cust_data_json = json_encode($this->aFieldsNew);

            $data = array(
                "lead_id" => $existingLeadID,
                'emp_id'=>$alreadyExistCheck['emp_id'],
                "cust_data_json" => $cust_data_json,
                'newToken'=>$this->urlParams['token'],
                'create_new' => false
            );
            $this->updateLeadIdInLogs($existingLeadID);  
            $this->continue_with_existing_lead_auto($data);
        } else {            
            $this->insert_customer_data_new();
        }
    }

    public function updateLeadIdInLogs($generatedLeadId){
        $this->db->where("lead_id",$this->aFieldsNew['customerid']);
        $this->db->where("product_id",$this->productCode);
        $this->db->update("logs_docs",['lead_id'=>$generatedLeadId]);
        return true;
    }

    public function insert_customer_data_new(){
        // Add record in employee_details and redirect
        $lead_id = $this->generate_lead_id();
        $aJsonField = $this->aFieldsNew;
        $aJsonField['token'] = $this->urlParams['token'];

        // Add mandatory fields which are not present, passing static as of now.
        // $aFieldsNew['salutation'] = 'Mr';
        // $aFieldsNew['gender'] = $this->getGenderFromSalutation($aFieldsNew['salutation']);

        $customerName = $this->aFieldsNew['first_name'].' '.$this->aFieldsNew['middle_name'].' '.$this->aFieldsNew['last_name'];
        $aFieldsNew['customer_name'] = preg_replace('/\s+/', ' ',mb_convert_case($customerName, MB_CASE_TITLE, "UTF-8"));

        $dtformat = 'Y-m-d H:i:s';
        if (!empty($this->aFieldsNew['timestamp'])) {
            $this->aFieldsNew['timestamp'] = date($dtformat, $this->aFieldsNew['timestamp']);
        }

        $aEmpData = ["ref1" => '', "ref2" => '', "branch_sol_id" => '002', "lead_id" => $lead_id, "salutation" => null, "product_id" => $this->productCode, "customer_name" => $aFieldsNew['customer_name'], "gender" => null, "bdate" => date('d-m-Y', strtotime($this->aFieldsNew['dob'])), "mob_no" => $this->aFieldsNew['mobile_number'], "access_right_id" => '2', "module_access_rights" => '1,8', "email" => $this->aFieldsNew['email'], "pancard" => '', "address" => $this->aFieldsNew['address1'], "emp_city" => $this->aFieldsNew['district'], "emp_state" => $this->aFieldsNew['state'], "emp_pincode" => $this->aFieldsNew['pin_code'], "nationality" => '', "marital_status" => '', "occupation" => '', "timestamp" => $this->aFieldsNew['timestamp'], "website_ref_no" => '', "source_id" => '', "referral_code" => '', "Product_Name" => $this->productCode, "Partner_Name" => $this->productCode, "UTM_Source" => '', "UTM_Medium" => '', "UTM_Campaign" => '', "Producer_Code" => $this->productCode, "cust_id" => $this->aFieldsNew['customerid'], "json_qote" => json_encode($aJsonField),"created_at" => date('Y-m-d H:i:s'), "ebcc_flag" => '', "scheme_code" => '', "ckyc_no" => '', "acc_no" => '', "acc_type" => ''];

        $this->db->set($aEmpData);
        $this->db->insert("employee_details", $aEmpData);

        // Inserting record in family_relation table
        $emp_id = $this->db->insert_id();
        $seconds = 30;
        $date_now = date("Y-m-d H:i:s");
        $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
        $this->db->where("emp_id",$emp_id);
        $this->db->update("employee_details",['modified_date' => $moddate]);
        $this->db->insert("family_relation", ["emp_id" => $emp_id, "family_id" => 0]);

        /* Update lead_id in logs*/
        $this->updateLeadIdInLogs($lead_id);

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

        //set user session then redirect
        $aMuthootSession['abc_session'] = array(
            'emp_id' => $emp_id,
            'product_id' => $this->productCode,
            'lead_id' => $lead_id,
            'mob_no' => $this->aFieldsNew['mobile_number']
        );
        $this->setCustomerSession($aMuthootSession);
        redirect(base_url('comprehensive_products'));
    }

    public function isValidApiCustomer(){
        $requiredFieldsApi = ['customerid'=>'Customer ID','first_name'=>'First Name','email'=>'Email ID','dob'=>'Date Of Birth','pin_code'=>'Pin Code','mobile_number'=>'Mobile Number'];
        $error_messages = [];
        foreach ($requiredFieldsApi as $field => $name){
            if (!(isset($this->aFieldsNew[$field]) && trim($this->aFieldsNew[$field]) !== "")){
                $error_messages[] = $name . ' is mandatory';
            }
        }
        if(count($error_messages) == 0){
            if (!$this->isValidMobileNumber($this->aFieldsNew['mobile_number'])) {
                $error_messages[] = "Mobile number is not valid!!";
            }    

            $dob = date("d-m-Y", strtotime($this->aFieldsNew['dob']));
            if (!$this->isValidAge($dob)) {
                $error_messages[] = "Sorry this plan covers adults from the age of 18 years to 60 years.";
            }
            if (!$this->isStringValidWithLetterAndSpacesApostrophe($this->aFieldsNew['first_name'])) {
                $error_messages[] = "First name doesn't allow special characters or numeric";
            }
            if (trim($this->aFieldsNew['middle_name']) != '' && !$this->isStringValidWithLetterAndSpacesApostrophe($this->aFieldsNew['middle_name'])) {
                $error_messages[] = "Middle name doesn't allow special characters or numeric";
            }
            if (trim($this->aFieldsNew['last_name']) != '' && !$this->isStringValidWithLetterAndSpacesApostrophe($this->aFieldsNew['last_name'])) {
                $error_messages[] = "Last name doesn't allow special characters or numeric";
            }
            if (is_numeric($this->aFieldsNew['pin_code'])) {
                $aPinData = $this->db->select('state,city,state_code')->from('axis_postal_code as pc')->where('pc.pincode', $this->aFieldsNew['pin_code'])->get()->row();
                if (!empty($aPinData)) {
                    $State = $this->aFieldsNew['state'] = $aPinData->state;
                } else {
                    $error_messages[] = "PIN Code does not match!!";
                }
            } else {
                $error_messages[] = "PIN Code is not valid!!";
            }
            
        }

        if(count($error_messages) == 0){
            return ['success'=>true];
        } else {
            // print_pre($error_messages);exit;
            // Log errors
            return ['success'=>false, 'errors'=>$error_messages];
        }
    }

    function checkIsAValidDate($dtString) {
        return (bool) strtotime($dtString);
    }

    

    /* continue with existing lead automatic start */

    function continue_with_existing_lead_auto($aData) {
        /* 	user will come when passing values like leadid already exist or customer_name, 
          mobile_number, product_name are already exist in database.
         */

        $aCustJSON = json_decode($aData['cust_data_json'], true);
        $lead_id = $aData['lead_id'];

        /*
          check if lead id exist in database (i.e. check if seesion value not manupulated)
          if not exist then it means we can not continue with this lead id for further.
          i.e. Proposal does not exist!!
         */
        $rsEmp = $this->db->select("emp_id, product_id, json_qote")->where(["lead_id" => $lead_id])->get("employee_details");

        if ($rsEmp->num_rows() <= 0) {
            $message = "No lead data found";
            $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
        } else {
            //insert log
            $aRow = $rsEmp->row();

            /* Update new token in employee_details json */
            $existingjsonQuote = $aRow->json_qote;
            $newArr = json_decode($existingjsonQuote,true);
            // $newArr['token'] = '123456789';
            $newArr['token'] = $aData['newToken'];


            $this->db->where("lead_id",$lead_id);
            $this->db->where("product_id",$this->productCode);
            $updateToken = $this->db->update('employee_details',['json_qote'=>json_encode($newArr)]);

            $arr = ["emp_id" => $aRow->emp_id, "posted_new_lead_data" => $aCustJSON, "existing_leadid" => $lead_id];
            $logs_type = "cont_with_existing_journey_auto";
            $this->logs_post_data_insert($lead_id, $arr, $logs_type);

            $this->redirect_to_dropoff_page($lead_id);
        }
    }

    /* continue with existing lead automatic end */

    /* continue lead data coed start */

   function redirect_to_dropoff_page($lead_id, $dropff_return_json = 0) {

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
        if (!empty($activityType)) {
    
            switch ($activityType) {
                case 1:
                    $arr['link'] = base_url('comprehensive_products');
                    break;
                case 2:
                    $arr['link'] = base_url('generate_quote/'.$policy_detail_id_encrypt);
                    break;
                case 3:
                    $arr['link'] = base_url('member_proposer_detail');
                    break;
                case 4:
                    $arr['link'] = base_url('member_review');
                    break;
                case ($activityType == 5 || $activityType == 6):
                    $arr['link'] = base_url('payment_redirection_abc');
                    break;
                case 7:
                    $arr['link'] = base_url('success_view/' . $emp_id);
                    break;
            }

            $this->logs_post_data_insert($lead_id, $arr, $logs_type);

            if ($dropff_return_json) {
                $data = array(
                    "status" => "1",
                    "url" => $arr['link'],
                );

                echo json_encode($data);
                exit;
            } else {
                redirect($arr['link']);
            }
        } else {
            $redirectLink = base_url('Muthoot_redirection_post_data');
            if ($dropff_return_json) {
                $data = array(
                    "status" => "4",
                    "url" => $redirectLink,
                );

                echo json_encode($data);
                exit;
            } else {
                redirect($redirectLink);
            }
        }
    }

    /* continue lead data code end */

    function getGenderFromSalutation($salutation){
        $salutation = strtolower($salutation);
        if($salutation == 'mr'){
            return 'Male';
        } else if (in_array($salutation,['ms','mrs'])) {
            return 'Female';
        } else {
            return false;
        }
    }

    function isStringValidWithLetterAndSpacesDot($string) {
        //It should not accept special characters other than dot.
        if (preg_match('/^[a-zA-Z. ]+$/', $string)) {
            return true;
        }
        return false;
    }

    function isStringValidWithLetterAndSpacesApostrophe($string) {
        //It should not accept special characters other than apostrophe.
        if (preg_match('/^[a-zA-Z.\'0-9 ]+$/', $string)) {
            return true;
        }
        return false;
    }

    function isStringValidWithLetterAndSpaces($string) {
        if (ctype_alpha(str_replace(' ', '', $string)) === false) {
            //if (preg_match('/^[a-zA-Z ]+$/', $string)) {
            return false; // 'Name must contain letters and spaces only' &  Not allow digts & special characters in name field
        }
        return true;
    }

    function isStringValidWithAllowSpecialChars($string) {
        //It should not accept special characters other than dot, slash,comma,& (i.e. '.' '\' '/' ',' '&' )
        if (preg_match('/^[a-zA-Z0-9.-\/\&, ]+$/', $string)) {
            return true;
        }
        return false;
    }

    function isValidMobileNumber($mobileNumber) {
        $mobileregex = "/^[6-9][0-9]{9}$/" ;
        return preg_match($mobileregex, $mobileNumber);
    }

    function isValidAge($dob) {
        $dateOfBirth = date("Y-m-d", strtotime($dob));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        if ($diff->format('%y') < 18 || $diff->format('%y') > 60) {
            //Customer age should 18 to 55.
            return false;
        }
        return true;
    }


    function logs_docs_insert($lead_id, $request, $type, $response = "") {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request), "type" => $type, "product_id" => $this->productCode];
       // $this->db->insert("logs_docs", $request_arr);
		$logs_array['data'] = $request_arr;
		$this->Logs_m->insertLogs($logs_array);
    }

	
    function logs_post_data_insert($lead_id, $request, $type, $response = "") {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request), "type" => $type, "product_id" => $this->productCode];
		$logs_array['data'] = $request_arr;
		$this->Logs_m->insertLogs($logs_array);
    }

    function setCustomerSession($aD2CSession) {
        //unset previous user data from session.	
        if ($this->session->userdata('abc_session')) {
            $this->session->unset_userdata('abc_session');
        }

        //set new user data in session.		
        $this->session->set_userdata($aD2CSession);
        /* Regenerate a new session upon successful authentication. Any session token used prior to 
          login should be discarded and only the new token should be assigned for the user till the user
          logs out.
          This session token should be properly expired when the user logs out. */
        $this->session->regenerate_id();
        $session_id = session_id();
        $aD2CSession = $this->session->userdata('abc_session');
        $emp_id = encrypt_decrypt_password($aD2CSession['emp_id'], 'D');
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

}
