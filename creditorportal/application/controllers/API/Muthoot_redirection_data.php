<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Muthoot_redirection_data extends CI_Controller {
    
    public $secureKey;
    public $iv;
    public $algoMethod;
    public $hashMethod;
    public $productNameMatchWithAxis; //Group Active Health Plan , previous in sample post data=>ABHI Axis Freedom Plus Plan
    public $productCode;
    //all json params 
    public $aFields = array("salutation", "customer_name", "address", "city", "pin_code", "state", "dob", "gender", "mobile_number", "isnri", "nationality", "email_address", "marital_status", "occupation_type", "pan_number", "nominee_name", "nominee_dob", "relationship_of_nominee", "timestamp", "website_ref_no", "crm_lead_id", "source_id", "referral_code", "product_name", "partner_name", "utm_source", "utm_medium", "utm_campaign", "sp_code", "lg_sol_id", "producer_code", "ebcc_flag", "scheme_code", "ckyc_no", "acc_no", "acc_type");

    public $aFieldsNew = array("salutation","lead_id","first_name","last_name","gender","dob","mobile_number","email_id","address","city","state","pincode","isnri","product_name");

    public $otherFields = ["nationality"=>"IN","marital_status"=>"Y","occupation_type"=>"BUSINESS -HNI","pan_number"=>"","nominee_name"=>"","nominee_dob"=>"", "relationship_of_nominee"=>"", "timestamp"=>"4/1/2020 12:24:09 PM", "website_ref_no"=>"s4dEP1kwWhI35oOa","source_id"=>"MB_LoggedIn", "referral_code"=>"","partner_name"=>"MUTHOOT", "utm_source"=>"MB_LoggedIn", "utm_medium"=>"MB App", "utm_campaign"=>"SMS Campaign Name", "sp_code"=>"0", "lg_sol_id"=>"002", "producer_code"=>"15455043", "ebcc_flag"=>"N", "scheme_code"=>"SBEZY", "ckyc_no"=>"", "acc_no"=>"", "acc_type"=>""];

    function __construct() {
        parent::__construct();
        //echo encrypt_decrypt_password(673014);exit;
 //echo "682434".encrypt_decrypt_password(682434)."<br>";
       // echo "684961".encrypt_decrypt_password(684961)."<br>";
       // echo "685012".encrypt_decrypt_password(685012)."<br>";exit;
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
        if (isset($_SESSION)){
            session_destroy();
        }

        $checksum = 0;
        $jsonData = '';
        $encryptedJSONData = '';
        if ($this->input->post('generate_redirection_checksum') !== null) {
            $jsonString = $this->input->post('json_cust_data');
            $jsonString = trim($jsonString, "\0");
            $aJSONData = json_decode($jsonString, true);
            $aJSONData = array_map('trim', $aJSONData); //to trim the array element         
            $plainText = '';
            foreach ($aJSONData as $key => $value) {
                $plainText .= $key . "=" . $value . "|";
            }
            $aData['data'] = $plainText;
            $jsonData = json_encode($aData);
            $checksum = 1;
        }

        if ($this->input->post('enctryptcustjson') !== null) {

            $json_data = $this->input->post('json_cust_data_with_identifier');
            $postData = json_decode($json_data, true);
            $plainText = $postData['data'];
            $jsonData = $datatoEncrypt = $this->getSalt(8) . $plainText . $this->getSalt(8);
            $encryptedData = base64_encode(openssl_encrypt($datatoEncrypt, $this->algoMethod, $this->secureKey, OPENSSL_RAW_DATA, $this->iv));

            //generate sha384 hash value
            $hashedData = hash($this->hashMethod, $encryptedData, true);
            $hashedData = base64_encode($hashedData);
            $hmacInput = hash_hmac($this->hashMethod, $hashedData, $this->secureKey, true);

            $hashedDataStr = $hmacInput;
            $hashedDataStr = base64_encode($hmacInput);

            $aData['data'] = $encryptedData;
            $aData['hash'] = $hashedDataStr;
            $encryptedJSONData = json_encode($aData);
            $checksum = 2;
        }

        $this->load->view("muthoot_redirection_view", compact('checksum', 'jsonData', 'encryptedJSONData'));
    }

    public function validate_data() {

        $decryptedJSONData = '';
        $checksum = '';
        $error = '';
        if ($this->input->post('validate_encrypted_data') !== null) {
            $postData = $this->input->post();
            $aData = $postData['encrypted_cust_jsondata'];
            $aJson = json_decode($aData, true);

            $datatoDecrypt = $aJson['data'];
            $hashData = $aJson['hash'];

            $decryptedData = base64_decode($datatoDecrypt);
            $decryptedData = openssl_decrypt($decryptedData, $this->algoMethod, $this->secureKey, OPENSSL_RAW_DATA, $this->iv);
            $decryptedData = substr($decryptedData, 8); //remove first 8 char salt          
            $decryptedData = substr($decryptedData, 0, -8); //remove last 8 char salt
            $decryptedJSONData = $decryptedData;

            $aJSONDataNew = array();
            $aDecryptedData = explode('|', $decryptedData);
            foreach ($aDecryptedData as $strValue) {
                if (!empty($strValue)) {
                    $aInnerData = explode('=', $strValue);
                    $aJSONDataNew[$aInnerData[0]] = $aInnerData[1];
                }
            }

            //generate HASH value 
            //hash will be calculated on given encrypted value  
            $hashedData = hash($this->hashMethod, $datatoDecrypt, true);
            $base64EncodedHashedData = base64_encode($hashedData);
            $hmacResult = hash_hmac($this->hashMethod, $base64EncodedHashedData, $this->secureKey, true);
            $checksum = base64_encode($hmacResult);

            if ($checksum != $hashData) {
                $error = "Invalid Hash";
            }
        }

        $this->load->view("validate_view", compact('decryptedJSONData', 'checksum', 'error'));
    }

    public function create_customer() {
        //place this before any script you want to calculate time
        //$time_start = microtime(true); 

        $checksum = '';
        $message = '';
        $rawData = json_decode(file_get_contents('php://input'), true);
        $postData = $this->input->post();
        
        //print_r($rawData);//exit;
        //print_r($postData);exit;
    
        $datatoDecrypt = '';
        $hashData = '';
        $logReqData = '';

        if (!empty($rawData)) {
            $datatoDecrypt = !empty($rawData['data']) ? trim($rawData['data']) : '';
            $hashData = !empty($rawData['hash']) ? trim($rawData['hash']) : '';
            $logReqData = file_get_contents('php://input');
        } elseif (!empty($postData)) {
            $aData = $this->input->post('data');
            $logReqData = $postData;
            $aJson = json_decode($aData, true);
            if (!empty($aJson)) {
                $datatoDecrypt = !empty($aJson['data']) ? trim($aJson['data']) : '';
                $hashData = !empty($aJson['hash']) ? trim($aJson['hash']) : '';
            }
        }

        // $logReqData = json_encode($logReqData);      
        $arr = ["post_request" => $logReqData];
        $logs_type = "Muthoot_redirection_post_data_request";
        $this->logs_post_data_insert($lead_id = '', $arr, $logs_type);

        //$time_end = microtime(true);
        //dividing with 60 will give the execution time in minutes otherwise seconds
        //$execution_time = ($time_end - $time_start)/60;
        //execution time of the script
        //echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Mins';
        // if you get weird results, use number_format((float) $execution_time, 10) 
        // print_pre($datatoDecrypt);exit;
        if (empty($datatoDecrypt)) {
            $message = "No data found in request";
            $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
        } else {

            $decryptedData = base64_decode($datatoDecrypt);
            $decryptedData = openssl_decrypt($decryptedData, $this->algoMethod, $this->secureKey, OPENSSL_RAW_DATA, $this->iv);
            $decryptedData = substr($decryptedData, 8); //remove first 8 char salt          
            $decryptedData = substr($decryptedData, 0, -8); //remove last 8 char salt

            if (empty(trim($decryptedData))) {
                $message = "Invalid decrypted data";
                $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
            }

            $aJSONData = array();
            $aDecryptedData = explode('|', $decryptedData);
            // print_pre($aDecryptedData);exit;
            foreach ($aDecryptedData as $strValue) {
                if (!empty($strValue)) {
                    $aInnerData = explode('=', $strValue);
                    //$aJSONData[strtolower($aInnerData[0])]=  strtolower(trim($aInnerData[1]));    
                    $aJSONData[strtolower(trim($aInnerData[0]))] = trim($aInnerData[1]);
                    /* date 2 june 2020
                      here as per Pooja and Axis said whatever format data comes at redirection page we have check and store as it in database.
                      - so cust name - 'Demo Person' and 'demo perSon' will treat as different.
                      - so lead id - '12345ABC' and '1234abc' will treat as different. */
                }
            }
            //generate HASH value 
            //hash will be calculated on given encrypted value  
            $hashedData = hash($this->hashMethod, $datatoDecrypt, true);
            $base64EncodedHashedData = base64_encode($hashedData);
            $hmacResult = hash_hmac($this->hashMethod, $base64EncodedHashedData, $this->secureKey, true);
            $checksum = base64_encode($hmacResult);

            if ($checksum != $hashData) {
                $message = "Invalid Hash";
                $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
            } else {

                if (empty($aJSONData)) {
                    $message = "No data found to proceed further";
                    $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
                } else {
                    print_pre($aJSONData);
                    $aJSONData = array_map('trim', $aJSONData); //to trim the array element     

                    print_pre($aJSONData);exit;             
                    foreach ($aJSONData as $key => $val) {
                        $this->aFields[$key] = $val;
                    }
                    
                    //5 Jun 2020 - Getting ~ sign in the communication address.(Replace '~' with space)
                    $this->aFields['address'] = str_replace('~',' ',$this->aFields['address']);
                    /*-Nominee Name is coming with Dollar Symbol and getting repeated for Cust IDs with multiple Accounts.
                    //Select words before first '$'
                    //e.g. Anita Singh$Vihaan Singh, word to shown on UI and 
                     to be considered for further processing - Anita Singh*/
                    if (strpos($this->aFields['nominee_name'], '$') !== false) {
                        //check if words has '$'                       
                        $aNominees = explode('$',$this->aFields['nominee_name']);                      
                        $this->aFields['nominee_name'] = $aNominees[0];
                    }
                   
                    $arr = ["post_request_decrypted" => $decryptedData];
                    $logs_type = "Muthoot_redirection_post_data_decrypted";
                    $this->logs_post_data_insert($lead_id = $this->aFields['crm_lead_id'], $arr, $logs_type);
                    $isValid = $this->is_valid_data();
                    if ($isValid) {
                        //echo "in";exit;
                        /* removing that character from customer name string. And same will be displayed (without special character like Dsouza rather D'souza (No other junk values) */
                        $this->aFields['customer_name'] = str_replace('\'', '', $this->aFields['customer_name']);
    
                        /* here customer visit page  using same  mobile no, product name but different lead id
                          Product_Name,customer_name,mob_no => Combine ID to check.
                         */
                        $resultCombineCheck = $this->db->select("lead_id")
                                ->from("employee_details ed")
                                ->where("ed.product_id", $this->aFields['product_name'])
                                ->where("ed.lead_id", $this->aFields['crm_lead_id'])
                                //->where("ed.mob_no", $this->aFields['mobile_number'])
                                ->order_by("ed.created_at", "desc")
                                ->limit(1)
                                ->get()
                                ->row_array();
                       /* echo $this->db->last_query();
                        print_pre($resultCombineCheck);exit;*/
                        if (!empty($resultCombineCheck)) {
                            
                            //already lead id record found for this combine id
                            //redirect to messages for continue to existing lead ID or create new ID.
                            $existingLeadID = $resultCombineCheck['lead_id'];
                            $policy_parent_id = $this->getPolicyParentId();
                            $cust_data_json = json_encode($this->aFields);
                            
                            //if(strcmp(strtolower(trim($this->aFields['crm_lead_id'])), strtolower($existingLeadID)) !== 0){
                            /*if (strcmp($this->aFields['crm_lead_id'], $existingLeadID) !== 0) {
                                //request has new lead id.
                                //requested lead id and existing lead is different.
                                echo "in";exit;
                                $this->load->abc_portal_template('other_msg', compact('existingLeadID', 'policy_parent_id', 'cust_data_json'));
                            } else {*/
                                //echo "out";exit;
                                //request has same lead id.
                                //requested lead id and existing lead is same.
                                $data = array(
                                    "lead_id" => $existingLeadID,
                                    "policy_parent_id" => $policy_parent_id,
                                    "cust_data_json" => $cust_data_json,
                                    'create_new' => false
                                );

                                // $this->session->set_flashdata('flashdata_lead_continue',$data); 
                                // redirect('/continue_lead_data');                         
                                $this->continue_with_existing_lead_auto($data);
                            /*}*/
                        } else {
                            //no lead id record found for this combine id
                            //so insert into db and redirect to customer journey
                            
                            $this->insert_customer_data(0,[],$logged_in_axis,$cta);
                        }
                    }
                }
            }
        }
    }

    function getPolicyParentId() {
        $strSql = "select policy_parent_id from product_master_with_subtype where main_product_name='". $this->aFieldsNew['product_name']."' ";
        $aPolicy = $this->db->query($strSql)->row_array();
        return $aPolicy['policy_parent_id'];
    }

    function insert_customer_data($dropff_return_json = 0, $aJsonFields = array(),$logged_in_axis = '', $cta = ''){

        // if ((int) $dropff_return_json) {
        //     $this->aFields = json_decode($aJsonFields, true);
        // }

        $this->aFieldsNew['dob'] = date("d-m-Y", strtotime($this->aFieldsNew['dob']));
        //if not exist
        $ramdom_number = 0;
        $logReqData = array();
        $aCustUniqueID = $this->db->select("id,unique_number,c_date")->from("cust_id_unique_number")->get()->row_array();
        if (empty($aCustUniqueID)) {
            //  if not found any record
            $ramdom_number = $this->getRandomUniqueNumber(12);
            $aInsertNumber = ["unique_number" => $ramdom_number, "c_date" => date('Y-m-d')];
            $this->db->insert('cust_id_unique_number', $aInsertNumber);

            $logReqData = $aInsertNumber;
        } else {
            //already found any record
            $curr_date = date('Y-m-d');
            if (strtotime($curr_date) == strtotime($aCustUniqueID['c_date'])) {
                //if page request on same date then increment unique number
                $ramdom_number = ++$aCustUniqueID['unique_number'];
                $aUpdateNumber = ["unique_number" => $ramdom_number];
                $this->db->where('id', $aCustUniqueID['id']);
                $this->db->update('cust_id_unique_number', $aUpdateNumber);
            } else {
                //if page request but curr_date >  c_date then update date 
                $ramdom_number = $this->getRandomUniqueNumber(12);
                $aUpdateNumber = ["unique_number" => $ramdom_number, "c_date" => date('Y-m-d')];
                $this->db->where('id', $aCustUniqueID['id']);
                $this->db->update('cust_id_unique_number', $aUpdateNumber);
            }
            $logReqData = $aUpdateNumber;
        }

        $arr = ["lead_random_cust_id_generated" => $logReqData];
        $logs_type = "Muthoot_redirection_random_cust_id";
        $this->logs_post_data_insert($lead_id = $this->aFieldsNew['lead_id'], $arr, $logs_type);


        /* $i = 0;
          $ramdom_number = mt_rand(1,9);
          do {
          $ramdom_number .= mt_rand(0, 9);
          } while(++$i < 14); */

        //"ref1" => $SP_Code,
        //"ref2" => $Referral_Code (BUT previous it was: "ref2" => $LG_SOL_ID,)
        //"branch_sol_id" =>$LG_SOL_ID

        $aJsonField = $this->aFieldsNew+$this->otherFields;
        $dtformat = 'Y-m-d H:i:s';
        if (!empty($this->otherFields['timestamp'])) {
            $this->otherFields['timestamp'] = date($dtformat, $this->otherFields['timestamp']);
        }

        $emp_gender = $this->aFieldsNew['gender'];

        //$aJsonField['product_name'] = $this->productNameMatchWithAxis; //store the passed value of Product_Name in json_qote
        $aEmpData = ["ref1" => $aJsonField['sp_code'], "ref2" => $aJsonField['referral_code'], "branch_sol_id" => $aJsonField['lg_sol_id'], "lead_id" => $aJsonField['lead_id'], "salutation" => $aJsonField['salutation'], "product_id" => $this->productCode, "customer_name" => $aJsonField['customer_name'], "gender" => $emp_gender, "bdate" => $aJsonField['dob'], "mob_no" => $aJsonField['mobile_number'], "access_right_id" => "2", "module_access_rights" => "1,8", "email" => $aJsonField['email_id'], "pancard" => $aJsonField['pan_number'], "address" => $aJsonField['address'], "ISNRI" => $aJsonField['isnri'],  "emp_city" => $aJsonField['city'], "emp_state" => $aJsonField['state'], "emp_pincode" => $aJsonField['pincode'], "nationality" => $aJsonField['nationality'], "marital_status" => $aJsonField['marital_status'], "occupation" => $aJsonField['occupation_type'], "timestamp" => $aJsonField['timestamp'], "website_ref_no" => $aJsonField['website_ref_no'], "source_id" => $aJsonField['source_id'], "referral_code" => $aJsonField['referral_code'], "Product_Name" => $this->productCode, "Partner_Name" => $this->productCode, "UTM_Source" => $aJsonField['utm_source'], "UTM_Medium" => $aJsonField['utm_medium'], "UTM_Campaign" => $aJsonField['utm_campaign'], "Producer_Code" => $aJsonField['producer_code'], "cust_id" => $ramdom_number, "json_qote" => json_encode($aJsonField),"created_at" => date('Y-m-d H:i:s'), "ebcc_flag" => $aJsonField['ebcc_flag'], "scheme_code" => $aJsonField['scheme_code'], "ckyc_no" => $aJsonField['ckyc_no'], "acc_no" => $aJsonField['acc_no'], "acc_type" => $aJsonField['acc_type']];
//, "created_at" => date('Y-m-d H:i:s')
        $this->db->set($aEmpData);
        $this->db->insert("employee_details", $aEmpData);

        
        
        $emp_id = $this->db->insert_id();
        $seconds = 30;
        $date_now = date("Y-m-d H:i:s");
        $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
        $this->db->where("emp_id",$emp_id);
        $this->db->update("employee_details",['modified_date' => $moddate]);
        $this->db->insert("family_relation", ["emp_id" => $emp_id, "family_id" => 0]);

        // $sqlQuery = 'select nominee_id from master_nominee where lower(nominee_type) = "' . strtolower($this->aFields['relationship_of_nominee']) . '" ';
        // $nomineMstResult = $this->db->query($sqlQuery)->row_array();

        // $nominee_id = 0;
        // if (!empty($nomineMstResult)) {
        //     $nominee_id = $nomineMstResult['nominee_id'];
        // }

        /* if(!empty($nomineMstResult)){
          $this->aFields['nominee_dob'] = date("d-m-Y",strtotime($this->aFields['nominee_dob']));
          $aEmpNomineeData = ["emp_id"=>$emp_id,"nominee_fname"=>$this->aFields['nominee_name'],"fr_id"=>$nomineMstResult['nominee_id'],"nominee_dob"=>$this->aFields['nominee_dob']];
          $this->db->insert("member_policy_nominee", $aEmpNomineeData);
          } */

        // $isValidDt = $this->checkIsAValidDate($this->aFields['nominee_dob']);
        // if ($isValidDt)
        //     $this->aFields['nominee_dob'] = date('Y-m-d', strtotime($this->aFields['nominee_dob']));
        // else
        //     $this->aFields['nominee_dob'] = '';

        // $aEmpNomineeData = ["emp_id" => $emp_id, "nominee_fname" => $this->aFields['nominee_name'], "fr_id" => $nominee_id, "nominee_dob" => $this->aFields['nominee_dob']];
        // $this->db->insert("member_policy_nominee", $aEmpNomineeData);
        
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
        $policy_parent_id = $this->getPolicyParentId();

        //set user session then redirect
        $aD2CSession['abc_session'] = array(
            'emp_id' => $emp_id,
            'product_id' => $this->productCode,
            'lead_id' => $this->aFields['crm_lead_id'],
            'mob_no' => $this->aFields['mobile_number']
        );
        //print_pre($aD2CSession);exit;
        $this->setCustomerSession($aD2CSession);

        if ((int) $dropff_return_json) {
            $data = array(
                "status" => "1",
                "url" => base_url('comprehensive_products'),
            );
            return json_encode($data);
        } else {
            redirect(base_url('comprehensive_products'));
        }
    }

    function checkIsAValidDate($dtString) {
        return (bool) strtotime($dtString);
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

        //print_r($_POST);exit;

        //"customer_name", "dob", "mobile_number", "nationality","timestamp", "website_ref_no", "crm_lead_id", "source_id", "partner_name", "lg_sol_id", "producer_code"
        //$mandatoryCustomerFacingFields = ["customer_name", "dob", "mobile_number"];//commented on 30 april.

        // $mandatoryCustomerFacingFields = ["customer_name", "dob", "mobile_number", "pin_code", "email_address"];
        // $mandatoryOtherFacingFields = ["lg_sol_id", "partner_name", "source_id", "timestamp", "website_ref_no", "product_name"];

        $mandatoryCustomerFacingFields = ["salutation", "lead_id", "first_name", "gender", "dob", "mobile_number", "email_id", "address", "city", "state", "pincode", "isnri", "product_name"];
        // $mandatoryOtherFacingFields = ["lg_sol_id", "partner_name", "source_id", "timestamp", "website_ref_no", "product_name"];


        //removed this from mendotory=>"UTM_Source", "UTM_Medium", "UTM_Campaign", "SP_Code",
        //ref 1=>LG_SOL_ID
        //ref 2=>SP_Code            

        foreach ($mandatoryCustomerFacingFields as $field){
            if (!(isset($this->aFieldsNew[$field]) && trim($this->aFieldsNew[$field]) !== "")){
                $error_messages[] = $field . ' is mandatory';
                break;
            }
        } 
        if (count($error_messages) > 0) {
            $message = implode('<br>', $error_messages);
        }

        //Validation for Other fields (Commented as of now)
        // foreach ($mandatoryOtherFacingFields as $field) {
        //     if (!(isset($this->aFields[$field]) && trim($this->aFields[$field]) !== "")) {
        //         $otherErrMsg .= "<br/>Oops, Something went wrong! Please try again.";
        //         break;
        //     }
        // }

        if($this->aFieldsNew['product_name'] != 'MUTHOOT'){
            $otherErrMsg .= "<br/>Invalid Partner Name";
        }

        if(!$this->isValidLength($this->aFieldsNew['lead_id'],1,12)){
            $otherErrMsg .= "<br/>Invalid Lead ID length, should be between 1 to 12 characters.";
        } else if(!$this->isAlphaNumeric($this->aFieldsNew['lead_id'])){
            $otherErrMsg .= "<br/>Invalid Lead ID, only alpha numeric allowed.";
        }

        // $tempProductName = !empty($this->aFields['product_name']) ? $this->aFields['product_name'] : "";

        // if (!empty($this->aFields['product_name']) && strcmp(strtolower(trim($this->productNameMatchWithAxis)), strtolower($this->aFields['product_name'])) !== 0 && strcmp(strtolower(trim($this->productCode)), strtolower($this->aFields['product_name'])) !== 0) {
        //     //'Both strings are not equal'; 
        //     //echo "in";exit;
        //     $otherErrMsg .= "<br/>Invalid Product Name";
        // } else {

        //     $Product_Name = trim($this->productCode);
        //     $aPolicy = $this->db->query("select policy_parent_id from product_master_with_subtype where main_product_name='$Product_Name'")->row_array();
        //     // echo $this->db->last_query();exit;
        //     if (empty($aPolicy)) {
        //         $otherErrMsg .= "<br/>Invalid Product Name";
        //     }
        //     $this->aFields['product_name'] = $Product_Name;
        // }

        if (!empty($this->otherFields['lg_sol_id']) || $this->otherFields['lg_sol_id'] != '') {
            //check if branch_sol_id is exit in table 
            $lg_sol_id = $this->otherFields['lg_sol_id'];
            $sqlStr = 'SELECT id from master_imd WHERE BranchCode = ' . $this->db->escape($lg_sol_id);
            $aBranchIMD = $this->db->query($sqlStr)->row_array();
            if (empty($aBranchIMD)) {
                $otherErrMsg .= "<br/>Please enter valid Referral code.";
            }
        }
        
        
        if (empty($this->aFieldsNew['first_name']) || $this->aFieldsNew['first_name'] == '' ) {
            $aCombineErr[] = "First name";
            
        } else if (!$this->isStringValidWithLetterAndSpacesApostrophe($this->aFieldsNew['first_name'])) {
            //$message .= "<br/>Customer Name should not contain numbers and special characters other than apostrophe like D'souza";    
            $aCombineErr[] = "First name";
        } 

        if(!empty($this->aFieldsNew['last_name']) || $this->aFieldsNew['last_name'] != ''){
            if (!$this->isStringValidWithLetterAndSpacesApostrophe($this->aFieldsNew['last_name'])) {
                //$message .= "<br/>Customer Name should not contain numbers and special characters other than apostrophe like D'souza";    
                $aCombineErr[] = "Last name";
            } 
        }

        if (empty($this->aFieldsNew['mobile_number']) || $this->aFieldsNew['mobile_number'] == '' ) {
            $aCombineErr[] = "Mobile number";
        } elseif (!$this->isValidMobileNumber($this->aFieldsNew['mobile_number'])) {
            $aCombineErr[] = "Mobile number";
        }

        if (!in_array($this->aFieldsNew['isnri'],['Y','N','y','n'])){
            $aCombineErr[] = "IsNRI only accepts 'Y' or 'N'";
        }        

        if (empty($this->aFieldsNew['dob']) || $this->aFieldsNew['dob'] == '') {
            $aCombineErr[] = "Date of birth";
            
        } elseif (!empty($this->aFieldsNew['dob']) && !$this->isValidAge($this->aFieldsNew['dob'])) {
            $dobErrMsg .= "Sorry this plan covers adults from the age of 18 years to 60 years.";
            
        }

        if (!empty($this->aFieldsNew['pincode']) || $this->aFieldsNew['pincode'] != '' ) {
            if (is_numeric($this->aFieldsNew['pincode'])) {
                $aPinData = $this->db->select('state,city,state_code')->from('axis_postal_code as pc')->where('pc.pincode', $this->aFieldsNew['pincode'])->get()->row();
                if (!empty($aPinData)) {
                    $City = $this->aFieldsNew['city'] = $aPinData->city;
                    $State = $this->aFieldsNew['state'] = $aPinData->state;
                } else {
                    $aCombineErr[] = "Pin Code";
                }
            }
        } else {
            $aCombineErr[] = "Pin Code";
        }

        
        if (!empty($message) || !empty($aCombineErr) || !empty($dobErrMsg) || !empty($otherErrMsg)) {
            $is_valid = 0;
            if (!empty($aCombineErr)) {
                $custFacingErrMsg .= "Sorry, You cannot proceed further! Following information is missing in the bank's record.";
                $custFacingErrMsg .= "<ul>";
                if (count($aCombineErr) >= 3) {
                    $custFacingErrMsg .= "<li> " . implode('</li><li>', $aCombineErr) . "</li>";
                } elseif (count($aCombineErr) == 2) {
                    $custFacingErrMsg .= "<li>  " . $aCombineErr[0] . " </li> <li> " . $aCombineErr[1] . "</li>";
                } else {
                    $custFacingErrMsg .= "<li>" . $aCombineErr[0] . "</li>";
                }
                $custFacingErrMsg .= "</ul>";
                $custFacingErrMsg .= "<p>Please update the same with Axis Bank and then return again to buy the policy!</p>";
            }

            $custFacingErrMsg .= "<p>" . $dobErrMsg . "</p>";
            $message = trim($message);
            $custFacingErrMsg = trim($custFacingErrMsg);
            $otherErrMsg = trim($otherErrMsg);
            
            //## Insert Invalid Data into redirection_invalid_data Start
            
            // Extra Error Messages Capturing Start
            $tempExtraErrorMessages = array();
            if (empty($this->otherFields['lg_sol_id']) || $this->otherFields['lg_sol_id'] == '') {
                $tempExtraErrorMessages[] = "Blank Lg Sol Id Not Allowed";
                
            }
            
            if (empty($this->aFieldsNew['product_name']) || $this->aFieldsNew['product_name'] == '') {
                $tempExtraErrorMessages[] = "Blank Partner Name Not Allowed";
                
            }

            if ($this->aFieldsNew['product_name'] != 'MUTHOOT') {
                $tempExtraErrorMessages[] = "Product Name Not Allowed";
            }
            
            if (empty($this->otherFields['source_id']) || $this->otherFields['source_id'] == '') {
                $tempExtraErrorMessages[] = "Blank Source Id Not Allowed";
                
            }
            
            if (empty($this->otherFields['timestamp']) || $this->otherFields['timestamp'] == '') {
                $tempExtraErrorMessages[] = "Blank Timestamp Not Allowed";
            }

            // Extra Error Messages Capturing End

            $tempAllErrorMessage = !empty($message) ? $message. ', ' : "";
            $tempAllErrorMessage .= !empty($otherErrMsg) ? $otherErrMsg. ', ' : "";
            $tempAllErrorMessage .= !empty($dobErrMsg) ? $dobErrMsg. ', ' : "";
            $tempAllErrorMessage .= !empty($custFacingErrMsg) ? strip_tags(str_replace( '<', ' <',$custFacingErrMsg )) : "";
            $tempAllErrorMessage .= !empty($tempExtraErrorMessages) ? implode(", ",$tempExtraErrorMessages) : "";
            $tempMisAllErrorType = !empty($mis_error_message_type) ? implode(", ",$mis_error_message_type) : "";
            // Insert Invalid Data into redirection_invalid_data Start
            $aBranchIMDTemp = !empty($aBranchIMD['id']) ? $aBranchIMD['id'] : "";
            $arrInvalidData = array('product_id'=>$this->productCode, 'lead_id'=>$this->aFields['crm_lead_id'], 
                                    'client_name'=>$this->aFields['customer_name'], 'mobile_number'=>$this->aFields['mobile_number'],   
                                    'referral_code'=>$this->aFields['lg_sol_id'], 'dob'=>$this->aFields['dob'],
                                    'redirection_request' => json_encode($this->aFields), 'error_message'=>$tempAllErrorMessage,'error_message_type'=>$tempMisAllErrorType ,'salutation'=>$this->aFields['salutation']
                                    ,'address'=>$this->aFields['address'] ,'city'=>$this->aFields['city']
                                    ,'pin_code'=>$this->aFields['pin_code'] ,'state'=>$this->aFields['state']
                                    ,'gender'=>$this->aFields['gender'] ,'nationality'=>$this->aFields['nationality']
                                    ,'marital_status'=>$this->aFields['marital_status'] ,'occupation_type'=>$this->aFields['occupation_type']
                                    ,'pan_number'=>$this->aFields['pan_number'] ,'nominee_name'=>$this->aFields['nominee_name']
                                    ,'nominee_dob'=>$this->aFields['nominee_dob'] ,'relationship_of_nominee'=>$this->aFields['relationship_of_nominee']
                                    ,'timestamp'=>$this->aFields['timestamp'] ,'website_ref_no'=>$this->aFields['website_ref_no']
                                    ,'source_id'=>$this->aFields['source_id'] ,'product_name'=>$tempProductName
                                    ,'partner_name'=>$this->aFields['partner_name'] ,'utm_source'=>$this->aFields['utm_source']
                                    ,'utm_medium'=>$this->aFields['utm_medium'] ,'utm_campaign'=>$this->aFields['utm_campaign']
                                    ,'sp_code'=>$this->aFields['sp_code'] ,'lg_sol_id'=>$this->aFields['lg_sol_id']
                                    ,'producer_code'=>$this->aFields['producer_code'],'imdcode'=>$aBranchIMDTemp, 
                                    "ebcc_flag" => $this->aFields['ebcc_flag'], "scheme_code" => $this->aFields['scheme_code'], "ckyc_no" => $this->aFields['ckyc_no'], "acc_no" => $this->aFields['acc_no'], "acc_type" => $this->aFields['acc_type'],"logged_in_axis" => $this->logged_in_axis, "cta" => $this->cta
                                    );      
                                    
            // $this->Redirect_invalid_data_m->insertUpdateInvalidData($arrInvalidData);
            //## Insert Invalid Data into redirection_invalid_data End
            
            $this->load->abc_portal_template('redirection_msgs', compact('message', 'custFacingErrMsg', 'otherErrMsg'),true,$this->productCode);
        }
        //echo $is_valid;exit;
        return $is_valid;
    }

    /* continue with existing lead automatic start */

    function continue_with_existing_lead_auto($aData) {
        /*  user will come when passing values like leadid already exist or customer_name, 
          mobile_number, product_name are already exist in database.
         */

        $aCustJSON = json_decode($aData['cust_data_json'], true);
        $lead_id = $aData['lead_id'];
        $policy_id = $aData['policy_parent_id'];

        /*
          check if lead id exist in database (i.e. check if seesion value not manupulated)
          if not exist then it means we can not continue with this lead id for further.
          i.e. Proposal does not exist!!
         */
        $rsEmp = $this->db->select("emp_id, product_id")->where(["lead_id" => $lead_id])->get("employee_details");
        
        if ($rsEmp->num_rows() <= 0) {
            $message = "No lead data found";
            $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
        } else {
            //insert log
            $aRow = $rsEmp->row();
            $arr = ["emp_id" => $aRow->emp_id, "posted_new_lead_data" => $aCustJSON, "existing_leadid" => $lead_id];
            $logs_type = "cont_with_existing_journey_auto";
            $this->logs_post_data_insert($lead_id, $arr, $logs_type);

            $dropff_return_json = 0;
            $this->redirect_to_dropoff_page($lead_id, $policy_id, $dropff_return_json);
        }
    }

    /* continue with existing lead automatic end */

    /* continue lead data coed start */

   function redirect_to_dropoff_page($lead_id, $policy_id, $dropff_return_json = 0) {

        $rsEmp = $this->db->select("emp_id, product_id, lead_id, mob_no")->where(["lead_id" => $lead_id,"product_id"=>$this->productCode])->get("employee_details");
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

    function isStringValidWithLetterAndSpacesDot($string) {
        //It should not accept special characters other than dot.
        if (preg_match('/^[a-zA-Z. ]+$/', $string)) {
            return true;
        }
        return false;
    }

    function isStringValidWithLetterAndSpacesApostrophe($string) {
        //It should not accept special characters other than apostrophe.
        if (preg_match('/^[a-zA-Z.\' ]+$/', $string)) {
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

    function isAlphaNumeric($string){
        $pattern = '/^[a-zA-Z0-9]*$/';
        $result = preg_match($pattern, $string);
        return $result;
    } 

    function isValidLength($string,$minLength,$maxLength){
        if( strlen($string) > $maxLength || strlen($string) < $minLength ){
            return false;
        }
        return true;
    }

    function isValidMobileNumber($mobileNumber) {
        $mobileregex = "/^[6-9][0-9]{9}$/" ;
        return preg_match($mobileregex, $mobileNumber);
    }

    function isValidPinCodeIndia($pinCode){
        $pincodeindiaregex = "/^[1-9][0-9]{5}$/";
        return preg_match($pincodeindiaregex, $pinCode);
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

    //Purpose: To generate random salt 
    function getSalt($numberofchars = 8) {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randStringLen = $numberofchars;

        $randString = "";
        for ($i = 0; $i < $randStringLen; $i++) {
            $randString .= $charset[mt_rand(0, strlen($charset) - 1)];
        }

        return $randString;
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

    function getRandomUniqueNumber($length = 12) {
        $randNumberLen = $length;
        $numberset = time() . 11111;
        if (strlen($numberset) == $randNumberLen) {
            return $numberset;
        } else {
            if (strlen($numberset) < $randNumberLen) {
                //add if length not match
                $addRandom = $randNumberLen - strlen($numberset);
                $i = 1;
                $ramdom_number = mt_rand(1, 9);
                do {
                    $ramdom_number .= mt_rand(0, 9);
                } while (++$i < $addRandom);
                $numberset .= $ramdom_number;
            } else {
                //substract if length not match
                $substractRandom = strlen($numberset) - $randNumberLen;
                $numberset = substr($numberset, 0, -$substractRandom);
            }

            return $numberset;
        }
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

    public function submitXmlData(){
        // $xmlData = $this->input->post('REQUEST');
        $xmlData = $this->input->post('REQUESTNEW');
        
        if ($xmlData === null) {
            $message = "No data found";
            $status = "Error";
        } else {
            $dataFromXml = simplexml_load_string($xmlData);
            // $array = new SimpleXMLElement($xmlData);
            $objJsonDocument = json_encode($dataFromXml);
            $arrOutput = json_decode($objJsonDocument, TRUE);
            $aJSONData = array_change_key_case($arrOutput,CASE_LOWER);
            
            if(empty($aJSONData)){
                $message = "No valid data found to proceed further";
                $this->load->abc_portal_template('redirection_msgs', compact('message'),true,$this->productCode);
            } else {
                $aJSONData = array_map('trim', $aJSONData); //to trim the array element

                foreach ($aJSONData as $key => $val) {
                    if(isset($this->aFieldsNew[$key])){
                        $this->aFieldsNew[$key] = $val;
                    }
                    if(isset($this->otherFields[$key])){
                        $this->otherFields[$key] = $val;
                    }
                }

                // print_pre($this->aFieldsNew);print_pre($this->otherFields);exit;
                $this->aFieldsNew['address'] = str_replace('~',' ',$this->aFieldsNew['address']);

                // if (strpos($this->aFields['nominee_name'], '$') !== false) {                      
                //     $aNominees = explode('$',$this->aFields['nominee_name']);                      
                //     $this->aFields['nominee_name'] = $aNominees[0];
                // }
                
                // $combinedArr = $this->aFieldsNew+$this->otherFields;
                // print_pre($combinedArr);exit;

                $arr = ["post_request_decrypted" => json_encode($this->aFieldsNew)];
                $logs_type = "Muthoot_redirection_post_data_decrypted";
                $this->logs_post_data_insert($lead_id = $this->aFieldsNew['lead_id'], $arr, $logs_type);

                //Check Valid Data
                $isValid = $this->is_valid_data();

                if ($isValid) {
                    $this->aFieldsNew['first_name'] = str_replace('\'', '', $this->aFieldsNew['first_name']);
                    if($this->aFieldsNew['last_name'] != ''){
                        $this->aFieldsNew['customer_name'] = $this->aFieldsNew['first_name'].' '.$this->aFieldsNew['last_name'];
                    } else {
                        $this->aFieldsNew['customer_name'] = $this->aFieldsNew['first_name'];
                    }
                    $resultCombineCheck = $this->db->select("lead_id")
                            ->from("employee_details ed")
                            ->where("ed.product_id", $this->aFieldsNew['product_name'])
                            ->where("ed.lead_id", $this->aFieldsNew['lead_id'])
                            ->order_by("ed.created_at", "desc")
                            ->limit(1)
                            ->get()
                            ->row_array();

                    if (!empty($resultCombineCheck)) {
                        $existingLeadID = $resultCombineCheck['lead_id'];
                        $policy_parent_id = $this->getPolicyParentId();
                        $cust_data_json = json_encode($this->aFieldsNew+$this->otherFields);
                        $data = array(
                            "lead_id" => $existingLeadID,
                            "policy_parent_id" => $policy_parent_id,
                            "cust_data_json" => $cust_data_json,
                            'create_new' => false
                        );                       
                        $this->continue_with_existing_lead_auto($data);
                    } else {
                        $this->insert_customer_data(0,[],$logged_in_axis,$cta);
                    }
                }
            }
        }
    }

    public function xmlRedirectionView(){
        // print_pre("ghjg");exit; 
        if (isset($_SESSION)){
            session_unset();
            // session_destroy();
        }
        $this->load->view("muthoot/xml_redirection_view");
    }


}
