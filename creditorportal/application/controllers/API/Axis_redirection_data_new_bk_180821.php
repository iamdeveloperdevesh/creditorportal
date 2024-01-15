<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Axis_redirection_data_new extends CI_Controller {
	
    public $secureKey;
    public $iv;
    public $algoMethod;
    public $hashMethod;
    public $productNameMatchWithAxis; //Group Active Health Plan , previous in sample post data=>ABHI Axis Freedom Plus Plan
    public $productCode;
    //all json params 
    public $aFields = array("salutation", "customer_name", "address", "city", "pin_code", "state", "dob", "gender", "mobile_number", "nationality", "email_address", "marital_status", "occupation_type", "pan_number", "nominee_name", "nominee_dob", "relationship_of_nominee", "timestamp", "website_ref_no", "crm_lead_id", "source_id", "referral_code", "product_name", "partner_name", "utm_source", "utm_medium", "utm_campaign", "sp_code", "lg_sol_id", "producer_code", "ebcc_flag", "scheme_code", "ckyc_no", "acc_no", "acc_type");

    function __construct() {
        parent::__construct();
        $this->config->set_item('csrf_protection', false);
        $this->db = $this->load->database('axis_retail', TRUE);
        $this->load->helper('url');

        $this->secureKey = "694bec96439fe598be98a0afc401c891";
        $this->iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $this->algoMethod = 'AES-256-CBC';
        $this->hashMethod = 'SHA384';
        $this->productNameMatchWithAxis = "group active health plan"; //make sure lower case while comparing
        $this->productCode = "D2C2"; //Do not change this value here as this is mapped with database table
        $this->productName = "Group Active Health Plan";
        $this->aFields = array_fill_keys($this->aFields, '');
		// Added By Shardul
		$this->load->model('cron/cron_m');
		$this->load->model('Logs_m');
        $this->load->model('Retail/Dashboard_m','Dashboard_m'); 
		$this->load->model('Redirect_invalid_data_m');
		$this->load->model("API/Payment_integration_retail", "obj_api", true);
    }

    public function index() {

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

        $this->load->view("redirection_view_new_d2c", compact('checksum', 'jsonData', 'encryptedJSONData'));
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
		
			if($this->input->get('cta')){
				$logged_in_axis = 'N';
				$cta = $this->input->get('cta');
			}else{
				$logged_in_axis = 'Y';
				$cta = '';
			}
		//print_pre($rawData);
	
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

        //$logReqData = json_encode($logReqData);		
        $arr = ["post_request" => $logReqData];
        $logs_type = "Axis_redirection_post_data_request";
        $this->logs_post_data_insert($lead_id = '', $arr, $logs_type);

        //$time_end = microtime(true);
        //dividing with 60 will give the execution time in minutes otherwise seconds
        //$execution_time = ($time_end - $time_start)/60;
        //execution time of the script
        //echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Mins';
        // if you get weird results, use number_format((float) $execution_time, 10) 

        if (empty($datatoDecrypt)) {
            $message = "No data found in request";
            $this->load->retail_template('Retail/redirection_msgs', compact('message'));
        } else {

            $decryptedData = base64_decode($datatoDecrypt);
            $decryptedData = openssl_decrypt($decryptedData, $this->algoMethod, $this->secureKey, OPENSSL_RAW_DATA, $this->iv);
            $decryptedData = substr($decryptedData, 8); //remove first 8 char salt			
            $decryptedData = substr($decryptedData, 0, -8); //remove last 8 char salt

            if (empty(trim($decryptedData))) {
                $message = "Invalid decrypted data";
                $this->load->retail_template('Retail/redirection_msgs', compact('message'));
            }

            $aJSONData = array();
            $aDecryptedData = explode('|', $decryptedData);
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
                $this->load->retail_template('Retail/redirection_msgs', compact('message'));
            } else {

                if (empty($aJSONData)) {
                    $message = "No data found to proceed further";
                    $this->load->retail_template('Retail/redirection_msgs', compact('message'));
                } else {

                    $aJSONData = array_map('trim', $aJSONData); //to trim the array element						
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
                    $logs_type = "Axis_redirection_post_data_decrypted";
                    $this->logs_post_data_insert($lead_id = $this->aFields['crm_lead_id'], $arr, $logs_type);

                    $isValid = $this->is_valid_data();
                    // print_pre($isValid);exit;
                    if ($isValid) {
				        setcookie('CRM_Lead_Id', $this->aFields['crm_lead_id'], 0);
                        /* removing that character from customer name string. And same will be displayed (without special character like Dsouza rather D'souza (No other junk values) */
                        $this->aFields['customer_name'] = str_replace('\'', '', $this->aFields['customer_name']);

                        /* added new check using only lead id.
                          if customer visit page using same lead id but different customer name, mobile no, product name.
                         */
                        $resultLeadIDExistCheck = $this->db->select("lead_id")
                                ->from("employee_details ed")
                                ->where("ed.lead_id", $this->aFields['crm_lead_id'])
                                ->where_in("product_id",['D2C2','D01','D02'])
                                ->limit(1)
                                ->get()
                                ->row_array();
								
                        if (!empty($resultLeadIDExistCheck)) {							
							
                            $existingLeadID = $resultLeadIDExistCheck['lead_id'];
                            $policy_parent_id = $this->getPolicyParentId();
                            $cust_data_json = json_encode($this->aFields);

                            //request has same lead id.
                            //requested lead id and existing lead is same.
                            $data = array(
                                "lead_id" => $existingLeadID,
                                "policy_parent_id" => $policy_parent_id,
                                "cust_data_json" => $cust_data_json,
                                'create_new' => false
                            );

                            $this->session->set_flashdata('flashdata_lead_continue',$data); 
                            redirect('/continue_lead_data');
                        }
						
                        /* here customer visit page  using same customer name, mobile no, product name but different lead id
                          Product_Name,customer_name,mob_no => Combine ID to check.
                         */
                        $resultCombineCheck = $this->db->select("lead_id")
                                ->from("employee_details ed")
                                ->where("ed.Product_Name", $this->aFields['product_name'])
                                ->where("ed.customer_name", $this->aFields['customer_name'])
                                ->where("ed.mob_no", $this->aFields['mobile_number'])
                                ->where("ed.bdate", date('d-m-Y',strtotime($this->aFields['dob'])))
                                ->where("is_active_lead",1)
                                ->order_by("ed.created_at", "desc")
                                ->limit(1)
                                ->get()
                                ->row_array();

                        if (!empty($resultCombineCheck)) {
                            //already lead id record found for this combine id
                            //redirect to messages for continue to existing lead ID or create new ID.
                            $existingLeadID = $resultCombineCheck['lead_id'];
                            $policy_parent_id = $this->getPolicyParentId();
                            $cust_data_json = json_encode($this->aFields);

                            //if(strcmp(strtolower(trim($this->aFields['crm_lead_id'])), strtolower($existingLeadID)) !== 0){
                            if (strcmp($this->aFields['crm_lead_id'], $existingLeadID) !== 0) {
                                //request has new lead id.
                                //requested lead id and existing lead is different.
                                $this->load->retail_template('Retail/other_msg', compact('cta','logged_in_axis','existingLeadID', 'policy_parent_id', 'cust_data_json'));
                            } else {
								
                                //request has same lead id.
                                //requested lead id and existing lead is same.
                                $data = array(
                                    "lead_id" => $existingLeadID,
                                    "policy_parent_id" => $policy_parent_id,
                                    "cust_data_json" => $cust_data_json,
                                    'create_new' => false
                                );

                                $this->session->set_flashdata('flashdata_lead_continue',$data); 
                                redirect('/continue_lead_data');							
                                // $this->continue_with_existing_lead_auto($data);
                            }
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
        $strSql = 'select policy_parent_id from product_master_with_subtype where product_code=\'' . $this->aFields['product_name'] . '\'';
        $aPolicy = $this->db->query($strSql)->row_array();
        return $aPolicy['policy_parent_id'];
    }

    function insert_customer_data($dropff_return_json = 0, $aJsonFields = array(),$logged_in_axis = '', $cta = '') {

        if ((int) $dropff_return_json) {
            $this->aFields = json_decode($aJsonFields, true);
        }
        $this->aFields['dob'] = date("d-m-Y", strtotime($this->aFields['dob']));
        //if not exist
        $ramdom_number = 0;
        $logReqData = array();
        $aCustUniqueID = $this->db->select("id,unique_number,c_date")->from("cust_id_unique_number")->get()->row_array();
        if (empty($aCustUniqueID)) {
            //	if not found any record
            $ramdom_number = $this->getRandomUniqueNumber(15);
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
                $ramdom_number = $this->getRandomUniqueNumber(15);
                $aUpdateNumber = ["unique_number" => $ramdom_number, "c_date" => date('Y-m-d')];
                $this->db->where('id', $aCustUniqueID['id']);
                $this->db->update('cust_id_unique_number', $aUpdateNumber);
            }
            $logReqData = $aUpdateNumber;
        }

        $arr = ["lead_random_cust_id_generated" => $logReqData];
        $logs_type = "Axis_redirection_random_cust_id";
        $this->logs_post_data_insert($lead_id = $this->aFields['crm_lead_id'], $arr, $logs_type);


        /* $i = 0;
          $ramdom_number = mt_rand(1,9);
          do {
          $ramdom_number .= mt_rand(0, 9);
          } while(++$i < 14); */

        //"ref1" => $SP_Code,
        //"ref2" => $Referral_Code (BUT previous it was: "ref2" => $LG_SOL_ID,)
        //"branch_sol_id" =>$LG_SOL_ID

        $aJsonField = $this->aFields;
        $dtformat = 'Y-m-d H:i:s';
        if (!empty($this->aFields['timestamp'])) {
            $this->aFields['timestamp'] = date($dtformat, strtotime($this->aFields['timestamp']));
        }

        $emp_gender = (strtolower($this->aFields['gender']) == 'm') ? "Male" : "Female";

        $aJsonField['product_name'] = $this->productNameMatchWithAxis; //store the passed value of Product_Name in json_qote
        $aEmpData = ["ref1" => $this->aFields['sp_code'], "ref2" => $this->aFields['referral_code'], "branch_sol_id" => $this->aFields['lg_sol_id'], "lead_id" => $this->aFields['crm_lead_id'], "salutation" => $this->aFields['salutation'], "product_id" => $this->productCode, "customer_name" => $this->aFields['customer_name'], "gender" => $emp_gender, "bdate" => $this->aFields['dob'], "mob_no" => $this->aFields['mobile_number'], "access_right_id" => "2", "module_access_rights" => "1,8", "email" => $this->aFields['email_address'], "pancard" => $this->aFields['pan_number'], "address" => $this->aFields['address'], "emp_city" => $this->aFields['city'], "emp_state" => $this->aFields['state'], "emp_pincode" => $this->aFields['pin_code'], "nationality" => $this->aFields['nationality'], "marital_status" => $this->aFields['marital_status'], "occupation" => $this->aFields['occupation_type'], "timestamp" => $this->aFields['timestamp'], "website_ref_no" => $this->aFields['website_ref_no'], "source_id" => $this->aFields['source_id'], "referral_code" => $this->aFields['referral_code'], "Product_Name" => $this->aFields['product_name'], "Partner_Name" => $this->aFields['partner_name'], "UTM_Source" => $this->aFields['utm_source'], "UTM_Medium" => $this->aFields['utm_medium'], "UTM_Campaign" => $this->aFields['utm_campaign'], "Producer_Code" => $this->aFields['producer_code'], "cust_id" => $ramdom_number, "json_qote" => json_encode($aJsonField),"created_at" => date('Y-m-d H:i:s'), "ebcc_flag" => $this->aFields['ebcc_flag'], "scheme_code" => $this->aFields['scheme_code'], "ckyc_no" => $this->aFields['ckyc_no'], "acc_no" => $this->aFields['acc_no'], "acc_type" => $this->aFields['acc_type'],"logged_in_axis" => $logged_in_axis, "cta" => $cta];
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

        // Insert DOB for self in DOB table employee_family_dob
        $this->db->insert("employee_family_dob", [
            'emp_id' => $emp_id, 
            'adult_number' => 1, 
            'fr_id' => 0, 
            'dob' => $this->aFields['dob']
        ]);

        $sqlQuery = 'select nominee_id from master_nominee where lower(nominee_type) = "' . strtolower($this->aFields['relationship_of_nominee']) . '" ';
        $nomineMstResult = $this->db->query($sqlQuery)->row_array();

        $nominee_id = 0;
        if (!empty($nomineMstResult)) {
            $nominee_id = $nomineMstResult['nominee_id'];
        }

        /* if(!empty($nomineMstResult)){
          $this->aFields['nominee_dob'] = date("d-m-Y",strtotime($this->aFields['nominee_dob']));
          $aEmpNomineeData = ["emp_id"=>$emp_id,"nominee_fname"=>$this->aFields['nominee_name'],"fr_id"=>$nomineMstResult['nominee_id'],"nominee_dob"=>$this->aFields['nominee_dob']];
          $this->db->insert("member_policy_nominee", $aEmpNomineeData);
          } */

        $isValidDt = $this->checkIsAValidDate($this->aFields['nominee_dob']);
        if ($isValidDt)
            $this->aFields['nominee_dob'] = date('Y-m-d', strtotime($this->aFields['nominee_dob']));
        else
            $this->aFields['nominee_dob'] = '';

        $aEmpNomineeData = ["emp_id" => $emp_id, "nominee_fname" => $this->aFields['nominee_name'], "fr_id" => $nominee_id, "nominee_dob" => $this->aFields['nominee_dob']];
        // $this->db->insert("member_policy_nominee", $aEmpNomineeData);  // Commented by Kshitiz
		
		$check_query = $this->db->select("*")->from("user_activity")->where('emp_id',$emp_id)->get()->row_array();

		if(empty($check_query)){
			$act_arr = ["emp_id" => $emp_id, "type" => "1", "updated_time" => date("Y-m-d H:i:s")];
			$this->db->insert("user_activity", $act_arr);
		}else{
			$request_arr = ["type" => "1"];
			$this->db->where("emp_id",$emp_id);
			$this->db->update("user_activity",$request_arr);
		}
        

        $emp_id = encrypt_decrypt_password($emp_id);
        $policy_parent_id = $this->getPolicyParentId();

        //set user session then redirect
        $aD2CSession['d2c_session'] = array(
            'emp_id' => $emp_id,
            'product_id' => $this->productCode,
            'policy_parent_id' => $policy_parent_id,
        );
        $this->setCustomerSession($aD2CSession);

        if ((int) $dropff_return_json) {
            $data = array(
                "status" => "1",
                "url" => base_url('retail_dashboard'),
            );
            return json_encode($data);
        } else {
            redirect(base_url('retail_dashboard'));
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

        //"customer_name", "dob", "mobile_number", "nationality","timestamp", "website_ref_no", "crm_lead_id", "source_id", "partner_name", "lg_sol_id", "producer_code"
        //$mandatoryCustomerFacingFields = ["customer_name", "dob", "mobile_number"];//commented on 30 april.

        $mandatoryCustomerFacingFields = ["customer_name", "dob", "mobile_number", "pin_code", "email_address", "salutation"];
        $mandatoryOtherFacingFields = ["lg_sol_id", "partner_name", "source_id", "timestamp", "website_ref_no", "crm_lead_id", "product_name"];
        //removed this from mendotory=>"UTM_Source", "UTM_Medium", "UTM_Campaign", "SP_Code",
        //ref 1=>LG_SOL_ID
        //ref 2=>SP_Code			

        foreach ($mandatoryCustomerFacingFields as $field){
            if (!(isset($this->aFields[$field]) && trim($this->aFields[$field]) !== "")){
                $error_messages[] = $field . ' is mandatory';
            }
        }

        if (count($error_messages) > 0) {
            $message = implode('<br>', $error_messages);
        }

        foreach ($mandatoryOtherFacingFields as $field) {
            if (!(isset($this->aFields[$field]) && trim($this->aFields[$field]) !== "")) {
                $otherErrMsg .= "<br/>Oops, Something went wrong! Please try again.";
                break;
            }
        }
		
		// $tempProductName = !empty($this->aFields['product_name']) ? $this->aFields['product_name'] : "";
  //       if (!empty($this->aFields['product_name']) && strcmp(strtolower(trim($this->productNameMatchWithAxis)), strtolower($this->aFields['product_name'])) !== 0 && strcmp(strtolower(trim($this->productCode)), strtolower($this->aFields['product_name'])) !== 0) {
  //           //'Both strings are not equal'; 
  //           $otherErrMsg .= "<br/>Invalid Product Name";
  //       } else {
  //           $Product_Name = trim($this->productCode);
  //           $aPolicy = $this->db->query("select policy_parent_id from product_master_with_subtype where product_code='$Product_Name'")->row_array();
  //           if (empty($aPolicy)) {
  //               $otherErrMsg .= "<br/>Invalid Product Name";
  //           }
  //           $this->aFields['product_name'] = $Product_Name;
  //       }



        /* New Validation Layer Start*/

        // Valid Salutation
        if (!empty($this->aFields['salutation'])) {
            $validSalutation = ['mr','mrs','ms'];
            if (!in_array(strtolower($this->aFields['salutation']),$validSalutation)) {
                $otherErrMsg .= "<br/>Please enter valid Salutation.";
            }
        }

        // Valid Gender
        if(!empty($this->aFields['gender'])){
            $validGender = ['m','f','male','female'];
            if (!in_array(strtolower($this->aFields['gender']),$validGender)) {
                $otherErrMsg .= "<br/>Please enter valid Gender.";
            }
        }

        // Valid Mobile Number
        if(!empty($this->aFields['mobile_number'])){
            if ((!$this->isValidMobileNumber($this->aFields['mobile_number']))) {
                $otherErrMsg .= "<br/>Please enter valid mobile number.";
            }
        }


        if(!empty($this->aFields['email_address'])){
            if (!filter_var($this->aFields['email_address'], FILTER_VALIDATE_EMAIL)) {
                $otherErrMsg .= "<br/>Please enter valid email address.";
            }
        }
        
        /* New Validation Layer End*/






        if (!empty($this->aFields['lg_sol_id'])) {
            //check if branch_sol_id is exit in table 
            $lg_sol_id = $this->aFields['lg_sol_id'];
            $sqlStr = 'SELECT id from master_imd WHERE BranchCode = ' . $this->db->escape($lg_sol_id);
            $aBranchIMD = $this->db->query($sqlStr)->row_array();
            if (empty($aBranchIMD)) {
                $otherErrMsg .= "<br/>Please enter valid Referral code.";
				$mis_error_message_type[] = MIS_FAILED_SOL_ID;
            }
        }
		
		
        if (empty($this->aFields['customer_name'])) {
            $aCombineErr[] = "Full name";
			$mis_error_message_type[] = MIS_FAILED_CUSTOMER_NAME;
        } elseif (!$this->isStringValidWithLetterAndSpacesApostrophe($this->aFields['customer_name'])) {
            //$message .= "<br/>Customer Name should not contain numbers and special characters other than apostrophe like D'souza";	
            $aCombineErr[] = "Full name";
			$mis_error_message_type[] = MIS_FAILED_CUSTOMER_NAME;
        } elseif (strpos($this->aFields['customer_name'], ' ') === false) {
            //check if last is empty.
				if(strpos($this->aFields['customer_name'], '.') === false){
			$this->aFields['customer_name'] = $this->aFields['customer_name']. " .";
				}
            //$aCombineErr[] = "Full name";
        }

        if (empty($this->aFields['mobile_number'])) {
            $aCombineErr[] = "Mobile number";
			$mis_error_message_type[] = MIS_FAILED_MOBILE_NUMBER;
        } elseif (!is_numeric($this->aFields['mobile_number'])) {
            //$message .= "<br/>Mobile Number should be in digit.";
            $aCombineErr[] = "Mobile number";
			$mis_error_message_type[] = MIS_FAILED_MOBILE_NUMBER;
        }

        // if(!$this->isValidMobileNumber($this->aFields['mobile_number'])){
        // $message .= "<br/>Invalid Mobile Number, Mobile Number should be 10 digit";		
        // }
        //if(!empty($this->aFields['pan_number']) && !$this->isValidPANCard($this->aFields['pan_number'])){
        //$message .= "<br/>Invalid PAN Card Number";		
        //$aCombineErr[]="PAN Card Number";
        //}
        // if(empty($this->aFields['email_address'])){
        // $aCombineErr[]="Email Address";
        // }elseif(!empty($this->aFields['email_address']) && filter_var($this->aFields['email_address'], FILTER_VALIDATE_EMAIL) === false) {
        // //$message .= "<br/>Invalid Email Address";
        // $aCombineErr[]="Email Address";
        // }
        // if(!empty($this->aFields['nominee_name'])){
        // if(!$this->isStringValidWithLetterAndSpacesDot($this->aFields['nominee_name'])){
        // $message .= "<br/>Nominee Name should not contain numbers and special characters other than dot(.).";	
        // }
        // }
        if (empty($this->aFields['dob'])) {
            $aCombineErr[] = "Date of birth";
			$mis_error_message_type[] = MIS_FAILED_DATE_OF_BIRTH;
        } elseif (!empty($this->aFields['dob']) && !$this->isValidAge($this->aFields['dob'])) {
            $dobErrMsg .= "Sorry this plan covers adults from the age of 18 years to 55 years. Please visit nearest Axis Bank Branch for a suitable plan.";
			$mis_error_message_type[] = MIS_FAILED_AGE;
        }


        if (!empty($this->aFields['pin_code'])) {
            if (is_numeric($this->aFields['pin_code'])) {
                $aPinData = $this->db->select('state,city,state_code')->from('axis_postal_code as pc')->where('pc.pincode', $this->aFields['pin_code'])->get()->row();
                if (!empty($aPinData)) {
                    $City = $this->aFields['city'] = $aPinData->city;
                    $State = $this->aFields['state'] = $aPinData->state;
                } else {
                    $otherErrMsg .= "<br/>Invalid PIN Code.";
                }
            }
        }

        /*
          if(empty($this->aFields['pin_code']))
          {
          $aCombineErr[]="PIN Code";

          }elseif(!empty($this->aFields['pin_code']))
          {
          if(!is_numeric($this->aFields['pin_code'])){
          $aCombineErr[]="PIN Code";
          }
          if(is_numeric($this->aFields['pin_code'])){
          $aPinData = $this->db->select('state,city,state_code')->from('axis_postal_code as pc')->where('pc.pincode', $this->aFields['pin_code'])->get()->row();
          if(!empty($aPinData)){
          //$message .= "<br/>PIN Code does not exist";
          $City = $this->aFields['city'] = $aPinData->city;
          $State = $this->aFields['state'] = $aPinData->state;

          }else{
          $aCombineErr[]="PIN Code";
          }
          }

          } */
          // print_pre($otherErrMsg);
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
			if (empty($this->aFields['lg_sol_id'])) {
				$tempExtraErrorMessages[] = "Blank Lg Sol Id Not Allowed";
				$mis_error_message_type[] = MIS_FAILED_SOL_ID;
			}
			
			if (empty($this->aFields['partner_name'])) {
				$tempExtraErrorMessages[] = "Blank Partner Name Not Allowed";
				$mis_error_message_type[] = MIS_FAILED_PARTNER;
			}
			
			if (empty($this->aFields['source_id'])) {
				$tempExtraErrorMessages[] = "Blank Source Id Not Allowed";
				$mis_error_message_type[] = MIS_FAILED_SOURCE_ID;
			}
			
			if (empty($this->aFields['timestamp'])) {
				$tempExtraErrorMessages[] = "Blank Timestamp Not Allowed";
				$mis_error_message_type[] = MIS_FAILED_TIMESTAMP_WEBREF;
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
			$arrInvalidData = array('product_id'=>'D2C2', 'lead_id'=>$this->aFields['crm_lead_id'], 
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
									
			$this->Redirect_invalid_data_m->insertUpdateInvalidData($arrInvalidData);
			//## Insert Invalid Data into redirection_invalid_data End
            
			$this->load->retail_template('Retail/redirection_msgs', compact('message', 'custFacingErrMsg', 'otherErrMsg'));
        }
        return $is_valid;
    }

    /* continue with existing lead automatic start */

    function continue_with_existing_lead_auto($aData) {
        /* 	user will come when passing values like leadid already exist or customer_name, 
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
            $this->load->retail_template('Retail/redirection_msgs', compact('message'));
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

    function continue_lead_data() {
        if (!empty($this->input->post('cust_data_json')) && !empty($this->input->post('create_new'))) {

            $isCreateNew = strtolower($this->input->post('create_new'));
            $cust_data_json = $this->input->post('cust_data_json');
            $lead_id = $this->input->post('lead_id');
            $policy_id = $this->input->post('policy_id');
            $aCustData = json_decode($cust_data_json, true);
            $aJsonFields = $aCustData;
			
			 $logged_in_axis = $this->input->post('logged_in_axis');
            $cta = $this->input->post('cta');
            $this->aFields = json_decode($aJsonFields, true);
            if ($isCreateNew == 'yes') {
                /*
                  check if passed lead id exist in database (i.e.Check if not manupulated from html page.)
                  if exist then it means we can not insert into database
                  i.e. Proposal already exist!!
                */
                $rsEmp = $this->db->select("emp_id, product_id")->where(["lead_id" => $this->aFields['crm_lead_id']])->get("employee_details");
                if ($rsEmp->num_rows() > 0) {
                    $redirectLink = base_url('Axis_redirection_post_data_new');
                    $data = array(
                        "status" => "2",
                        "url" => $redirectLink,
                    );
                    echo json_encode($data);
                    exit;
                } else {
                    //insert log	
                    $arr = ["emp_id" => 0, "posted_new_lead_data" => $aCustData, "existing_leadid" => $lead_id];
                    $logs_type = "cont_with_new_journey_mannual";
                    $this->logs_post_data_insert($this->aFields['crm_lead_id'], $arr, $logs_type);

                    // GET lead & proposal with same basic detail
                    // $basicDetails = ['customer_name'=>$this->aFields['customer_name'],'bdate'=>$this->aFields['dob'],'mob_no'=>$this->aFields['mobile_number']];
                    // $detail = $this->Dashboard_m->getLeadWithProposal($basicDetails,$proposalStatus = 'Payment Pending');    
                    // exit('break');              

                    // Mark Old lead as inactive
                    $this->db->where('lead_id',$lead_id)->where('Product_Name',$this->productName);
                    $this->db->update('employee_details',['is_active_lead'=>0]);

                    $dropff_return_json = 1;
                    $result = $this->insert_customer_data($dropff_return_json, $aJsonFields,$logged_in_axis = '',$cta = ''); //call function
                    echo $result;
                    exit;
                }
            } else {
                /*
                  check if lead id exist in database (i.e.Check if not manupulated from html page.)
                  if not exist then it means we can not continue with this lead id for further.
                  i.e. Proposal does not exist!!
                 */
                $rsEmp = $this->db->select("emp_id, product_id")->where(["lead_id" => $lead_id])->get("employee_details");
                if ($rsEmp->num_rows() <= 0) {
                    $redirectLink = base_url('Axis_redirection_post_data_new');
                    $data = array(
                        "status" => "3",
                        "url" => $redirectLink,
                    );
                    echo json_encode($data);
                    exit;
                } else {
                    //continue with existing journey.
                    //insert log
                    $aRow = $rsEmp->row();
                    $arr = ["emp_id" => $aRow->emp_id, "posted_new_lead_data" => $aCustData, "existing_leadid" => $lead_id];
                    $logs_type = "cont_with_existing_journey_mannual";
                    $this->logs_post_data_insert($lead_id, $arr, $logs_type);

                    $dropff_return_json = 1;
                    $this->redirect_to_dropoff_page($lead_id, $policy_id, $dropff_return_json);
                }
            }
        } elseif(!empty($this->session->flashdata('flashdata_lead_continue'))) {//!empty($this->session->flashdata('flashdata_lead_continue'))){
            /* 	user will come when passing values like leadid already exist or customer_name, 
              mobile_number, product_name are already exist in database.
             */

            $flashData = $this->session->flashdata('flashdata_lead_continue');
            $aCustJSON = json_decode($flashData['cust_data_json'], true);
            $lead_id = $flashData['lead_id'];
            $policy_id = $flashData['policy_parent_id'];

            /*
              check if lead id exist in database (i.e. check if seesion value not manupulated)
              if not exist then it means we can not continue with this lead id for further.
              i.e. Proposal does not exist!!
             */
            $rsEmp = $this->db->select("emp_id, product_id")->where(["lead_id" => $lead_id,"product_name"=>$aCustJSON['product_name']])->get("employee_details");
            // print_pre($flashData);exit;
            if ($rsEmp->num_rows() <= 0) {
                $message = "No data found";
                $this->load->retail_template('Retail/redirection_msgs', compact('message'));
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
    }

    function redirect_to_dropoff_page($lead_id, $policy_id, $dropff_return_json = 0) {
        $rsEmp = $this->db->select("emp_id, product_id")->where(["lead_id" => $lead_id, "product_name"=>$this->productName])->get("employee_details");
        $aRow = $rsEmp->row();
        $emp_id = $aRow->emp_id;
        $emp_id_encrypt = encrypt_decrypt_password($emp_id);
        //set user session then redirect
        $aD2CSession['d2c_session'] = array(
            'emp_id' => $emp_id_encrypt,
            'product_id' => $this->productCode,
            'policy_parent_id' => $policy_id,
        );
        $this->setCustomerSession($aD2CSession);

        $sqlStr = "Select ua.type FROM user_activity as ua where ua.emp_id = $emp_id";
        $aResult = $this->db->query($sqlStr)->row_array();
        $logs_type = "dropoff_journey_return_page";

        $activityType = $aResult['type'];

        $arr = ["emp_id" => $emp_id, "type" => $activityType];
        if (!empty($activityType)) {

            switch ($activityType) {
                case 1:
                    $arr['link'] = base_url('retail_dashboard');
                    break;
                case ($activityType == 2 || $activityType == 3):
                    $arr['link'] = base_url('retail_enrollment');
                    break;
                case 4:
                    $arr['link'] = base_url('payment_redirection_retail/'.$emp_id_encrypt);
                    break;
                case ($activityType == 5 || $activityType == 6):
                    $arr['link'] = base_url('retail/payment_return_view/'.$emp_id_encrypt);
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
            $redirectLink = base_url('Axis_redirection_post_data_new');
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

    // function isValidMobileNumber($mobileNumber) {
    //     if (!is_numeric($mobileNumber) || strlen($mobileNumber) != 10)
    //         return false;
    //     //	System should allow number starting with 4,5,6,7,8 and 9 
    //     //AND It should not display mobile number starting from 0,1,2,3.
    //     /* $pattern = '/^[4-9][0-9]{9}$/';
    //       $result = 	preg_match($pattern, $mobileNumber);
    //       if ($result){
    //       return true;
    //       } */
    //     return true;
    // }

    function isValidMobileNumber($mobileNumber) {
        $mobileregex = "/^[6-9][0-9]{9}$/" ;
        return preg_match($mobileregex, $mobileNumber);
    }

    function isValidDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    function isValidAge($dob) {
        $dateOfBirth = date("Y-m-d", strtotime($dob));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        if ($diff->format('%y') < 18 || $diff->format('%y') > 55) {
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
	
	function dropoff_journey_error($emp_id_encrypt) {
        show_404();
    }


    function dropoff_journey($emp_id_encrypt,$send_type) {
        $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D'); 
		
		if($send_type == 1){
		  $utm_string = "?utm_source=abhi-dropoff&utm_medium=email&utm_campaign=abhi-dropoff-campaign";
		}else{
		  $utm_string = "?utm_source=abhi-dropoff&utm_medium=sms&utm_campaign=abhi-dropoff-campaign";
		}
		
        $val = $this->db->query("select ed.json_qote,ed.product_id,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id from employee_details as ed,user_activity as ua where ed.emp_id = ua.emp_id AND ua.status = 1 AND ua.emp_id = '$emp_id'")->row_array();

        $aD2CSession['d2c_session'] = array('emp_id' => $emp_id_encrypt, 'product_id' => $this->productCode, 'policy_parent_id' => '', 'dropoff' => true);

        $this->setCustomerSession($aD2CSession);
        setcookie('CRM_Lead_Id', $val['lead_id'], 0);
        $logs_type = "dropoff_journey_return";

        $arr = ["emp_id" => $emp_id, "type" => $val['type']];
        if ($val['type'] == 1) {
            $arr['link'] = base_url('retail_dashboard');
            $this->logs_docs_insert($val['lead_id'], $arr, $logs_type,$val['product_id']);
            redirect(base_url('retail_dashboard'.$utm_string));
        } elseif ($val['type'] == 3 || $val['type'] == 2) {
            $arr['link'] = base_url('retail_enrollment');
            $this->logs_docs_insert($val['lead_id'], $arr, $logs_type,$val['product_id']);
            redirect(base_url('retail_enrollment'.$utm_string));
        } elseif ($val['type'] == 4) {
            $arr['link'] = base_url('payment_redirection_retail/'.$emp_id_encrypt);
            $this->logs_docs_insert($val['lead_id'], $arr, $logs_type,$val['product_id']);
            redirect(base_url('payment_redirection_retail/'.$emp_id_encrypt));
        } else {
            $arr['link'] = base_url('retail/payment_return_view/'.$emp_id_encrypt);
            $this->logs_docs_insert($val['lead_id'], $arr, $logs_type,$val['product_id']);
            redirect(base_url('retail/payment_return_view/'.$emp_id_encrypt.''.$utm_string));
        }
    }

    function logs_docs_insert($lead_id, $request, $type,$productId = 'D2C2', $response = "") {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request), "type" => $type, "product_id" => $productId];
        // $this->db->insert("logs_docs", $request_arr);
		$logs_array['data'] = $request_arr;
        $logs_array['tablename'] = 'logs_docs';
		$this->Logs_m->insertLogs($logs_array);
    }

	
    function logs_post_data_insert($lead_id, $request, $type, $response = "") {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request), "type" => $type, "product_id" => 'D2C2'];
		$logs_array['data'] = $request_arr;
		$this->Logs_m->insertLogs($logs_array);
    }

    function getRandomUniqueNumber($length = 15) {
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
        if ($this->session->userdata('d2c_session')) {
            $this->session->unset_userdata('d2c_session');
        }

        //set new user data in session.		
        $this->session->set_userdata($aD2CSession);
        /* Regenerate a new session upon successful authentication. Any session token used prior to 
          login should be discarded and only the new token should be assigned for the user till the user
          logs out.
          This session token should be properly expired when the user logs out. */
        $this->session->regenerate_id();
        $session_id = session_id();
        $aD2CSession = $this->session->userdata('d2c_session');
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

    public function show_session_timeoutpage() {

        $this->load->retail_template('Retail/session_timeout_view');
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
	
	
	public function payment_success_view($emp_id_encrypt)
	{
		
		//$emp_id = $this->emp_id;	
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		
		$aD2CSession['d2c_session'] = array(
            'emp_id' => $emp_id_encrypt,
            'product_id' => $this->productCode,
            'policy_parent_id' => '',
        );

		$this->setCustomerSession($aD2CSession);

		$query = $this->db->query("SELECT  ed.source_id,ed.emp_id,ed.lead_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,p.premium,mpst.payment_url,p.id as proposal_id FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id=".$emp_id)->row_array();
		
		if($query){
			
			$encrypted = $this->input->post('RESPONSE');
		
			if($encrypted)
			{
				$decrypted = openssl_decrypt($encrypted, "aes-128-ecb", "axisbank12345678", 0);
				$post_data = json_decode($decrypted,true);
				$post_data = array_map( 'trim', $post_data );//to trim the array element	
				foreach($post_data as $key=>$val){
					$post_fields[$key] = $val;
				}
				
				$TxMsg = !empty($post_fields['TxMsg']) ? (string)$post_fields['TxMsg'] : 0;	
				$amount = !empty($post_fields['amount']) ? (float)$post_fields['amount'] : 0;	
				$paymentMode = !empty($post_fields['paymentMode']) ? (string)$post_fields['paymentMode'] : 0;	
				$txnDateTime = !empty($post_fields['txnDateTime']) ? (string)$post_fields['txnDateTime'] : 0;	
				$TxRefNo = !empty($post_fields['TxRefNo']) ? (string)$post_fields['TxRefNo'] : 0;	
				$TxStatus = !empty($post_fields['TxStatus']) ? (string)$post_fields['TxStatus'] : 0;
				$PaymentStatus = !empty($post_fields['PaymentStatus']) ? (string)$post_fields['PaymentStatus'] : 0;
				$EMandateStatus = !empty($post_fields['EMandateStatus']) ? (string)$post_fields['EMandateStatus'] : '';
				$EMandateStatusDesc = !empty($post_fields['EMandateStatusDesc']) ? (string)$post_fields['EMandateStatusDesc'] : 0;
				$EMandateRefno = !empty($post_fields['EMandateRefno']) ? (string)$post_fields['EMandateRefno'] : 0;
				$EMandateDate = !empty($post_fields['EMandateDate']) ? (string)$post_fields['EMandateDate'] : 0;
				$Registrationmode = !empty($post_fields['Registrationmode']) ? (string)$post_fields['Registrationmode'] : 0;
				$EMandateFailureReason = !empty($post_fields['EMandateFailureReason']) ? (string)$post_fields['EMandateFailureReason'] : 0;
				$MandateLink = !empty($post_fields['MandateLink']) ? (string)$post_fields['MandateLink'] : 0;
				
				if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){
					$TxStatus = "success";
					$TxMsg = "No Error";
				}
			}
			

			if($TxMsg && $amount && $paymentMode && $txnDateTime && $TxRefNo && $TxStatus){
				
			$request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted ,"res"=>$decrypted , "type"=>"payment_response_post", "product_id"=> "D2C2"];
  	        $this->db->insert("logs_docs",$request_arr);
			
			$request_arr = ["payment_status" => $TxMsg,"premium_amount" => $amount,"payment_type" => $paymentMode,"txndate" => $txnDateTime,"TxRefNo" => $TxRefNo,"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($post_fields)];
		
			$this->db->where("proposal_id",$query['proposal_id']);
			$this->db->where('TxStatus != ','success');
			$this->db->update("payment_details",$request_arr);
			}
			
			if(isset($Registrationmode))
			{
				
				$query_emandate = $this->db->query("select * from emandate_data where lead_id=".$query['lead_id'])->row_array();
				
				if($EMandateStatus == 'MS'){
					$mandate_status = 'Success';
				}elseif($EMandateStatus == 'MI'){
					$mandate_status = 'Emandate Pending';
				}elseif($EMandateStatus == 'MR'){
					$mandate_status = 'Emandate Received';
				}elseif ($EMandateStatus == '')
                {
                    $mandate_status = 'Emandate Pending';
                }else{
					$mandate_status = 'Fail';
				}
			
				if($query_emandate > 0){
					
					$arr = ["TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate)),"Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason,"MandateLink" => $MandateLink];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("emandate_data",$arr);
				}else{
					
					$arr = ["lead_id" => $query['lead_id'],"TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate)),"Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason,"MandateLink" => $MandateLink];
					
					$this->db->insert("emandate_data", $arr);
				}
				
				if($mandate_status == 'Success'){
					$this->obj_api->send_message($query['lead_id'],'success');
				}
				
				if($mandate_status == 'Fail'){
					$this->obj_api->send_message($query['lead_id'],'fail');
				}
				
				if($paymentMode == 'PP' && ($Registrationmode == 'SAD' || $Registrationmode == 'EMI' || $Registrationmode == 'UPI')){
					$this->obj_api->send_message($query['lead_id'],'SAD_EMI_one');
					$this->obj_api->send_message($query['lead_id'],'SAD_EMI_two');
				}
				
			}
			
			if(isset($PaymentStatus) && $PaymentStatus == 'PI'){
				$check_pg = $this->obj_api->real_pg_check($query['lead_id']);
				if($check_pg){
					redirect(base_url("payment_success_view_call/".$emp_id_encrypt));
				}else{
					
					$arr_update = ["is_payment_initiated" => 1];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("employee_details",$arr_update);
					
					echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
					exit;
				}
			}
			
			$proposal_id =  $query['proposal_id'];
			$payment_data = $this->db->query("select payment_status,TxStatus from payment_details where proposal_id=$proposal_id ")->row_array();
			
			if($payment_data['TxStatus'] == 'success'){
				
				$check_result = $this->obj_api->policy_creation_call($query['lead_id']);
				
				if($check_result['Status'] == 'Success'){
					
					$request_arr = ["type" => "6"];
					$this->db->where("emp_id",$emp_id);
					$this->db->update("user_activity",$request_arr);
					
					$customer_data['premium'] = $query['premium'];
					$customer_data['email'] = $query['email'];
					$customer_data['source_id'] = $query['source_id'];
					//dont remove this//
					$customer_data['lead_id'] = $query['lead_id'];
					
					$MandateLink_data = $this->db->query("select MandateLink,Registrationmode from emandate_data where lead_id = '".$query['lead_id']."'")->row_array();
					
					$data_policy = $this->db->query("select * from api_proposal_response where emp_id='$emp_id'")->row_array();
					
					if($data_policy > 0){
					
					// Shardul CRM Addition Part Start
						// Create Lead In CRM Start
						$lead_id = json_decode($this->cron_m->createCRMLeadDropOff($emp_id),true);
						// Create Lead In CRM End
						
						// Create Member In CRM Start
						if(!empty($lead_id['LeadId'])) {
							$this->cron_m->insertMemberCRMDropOff($emp_id,$lead_id['LeadId']);
						}
						// Create Member In CRM End	
					// Shardul CRM Addition Part End	
					
					$this->load->retail_template("Retail/thankyou",compact('data_policy','customer_data','MandateLink_data'));
					}
					
				}else{
					
					$request_arr = ["type" => "6"];
					$this->db->where("emp_id",$emp_id);
					$this->db->update("user_activity",$request_arr);
					
					redirect(base_url('payment_error_view_call'));
				}
			
			}else{
				
				$request_arr = ["type" => "5"];
				$this->db->where("emp_id",$emp_id);
				$this->db->update("user_activity",$request_arr);				
				redirect(base_url('payment_error_view_call'));
			}
			
			
		}
		
		
	}
	
	/* cron */
	public function emandate_enquiry_HB_call_d2c()
	{
		$this->obj_api->emandate_enquiry_HB_call_m();
		echo json_encode(['status' =>'success']);
	}

}
