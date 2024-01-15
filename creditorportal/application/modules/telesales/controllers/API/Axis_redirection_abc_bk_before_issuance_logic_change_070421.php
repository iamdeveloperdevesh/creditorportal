<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

require_once (APPPATH . "controllers/MY_AbcSessionCheck.php");

class Axis_redirection_abc extends MY_AbcSessionCheck
{
    public $emp_id;
    public $parent_id;

    public $algoMethod;
    public $hashMethod;
    public $hash_key;
    public $encrypt_key;

    function __construct()
    {
        parent::__construct();
        //abc_session get value in
        //d2c_session get value in
        $aD2CSession = $this
            ->session
            ->userdata('abc_session');
        //print_pre($aD2CSession);exit;
        $this->emp_id = encrypt_decrypt_password($aD2CSession['emp_id'], 'D');
        if ($this->emp_id == null)
        {
            $this->emp_id = $aD2CSession['emp_id'];
        }
        $this->lead_id = $aD2CSession['lead_id'];
        $this->mob_no = $aD2CSession['mob_no'];
        //$this->parent_id = $aD2CSession['policy_parent_id'];
        $this->db = $this
            ->load
            ->database('axis_retail', true);

        $this
            ->load
            ->model("API/Payment_integration_abc", "obj_api_new", true);
        // Added By Shardul
        $this
            ->load
            ->model("Logs_m");

        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';

        //echo encrypt_decrypt_password(620181);
        
    }

    public function show_session_timeoutpage()
    {
        $this->load->view('ABC/session_timeout_view');
        //$this->load->abc_portal_template('session_timeout_view');
    }

    public function coi_download()
    {
        echo json_encode($this
            ->obj_api_new
            ->coi_download_m());
    }
	
	/* cron */
    public function emandate_enquiry_HB_call()
    {
        $this
            ->obj_api_new
            ->emandate_enquiry_HB_call_m();
        echo json_encode(['status' => 'success']);
    }

    public function payment_redirection()
    {
        $emp_id = $this->emp_id;

        $this
            ->db
            ->where("emp_id", $emp_id);
        $this
            ->db
            ->where('type != ', 6);
        $this
            ->db
            ->update("user_activity", ['type' => 6]);

        if ($emp_id)
        {
            $query = $this
			->db
			->query("SELECT ed.customer_name,ed.lead_id,ed.emp_id,epd.policy_detail_id,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,p.id as proposal_id FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details as pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id= '".$emp_id."' GROUP BY p.emp_id")->row_array();
			//print_pre($query);exit;
			if(!empty($query))
			{
				$premiumAmount = $query['premium'];
				$leadId = $query['lead_id'];
				$email = $query['email'];
				$mobileNumber = $query['mob_no'];
				$customer_name = $query['customer_name'];
				
				
                if ($query['status'] != 'Payment Pending')
                {
                    redirect(base_url("payment_success_view_call_abc/" . $emp_id));
                }
                else
                {

                    $lead_data = $this
                        ->obj_api_new
                        ->get_all_quote_call($emp_id);
                    //print_r($lead_data);exit;
                    if ($lead_data['status'] == 'Success')
                    {
                        $Source = "ABC";
                        $Vertical = "ABCGRP";
                        $PaymentMode = "PP";
                        $ReturnURL = base_url("payment_success_view_call_abc/" . $emp_id);
                        $UniqueIdentifier = "LEADID";
                        $UniqueIdentifierValue = $leadId;
                        $CustomerName = $customer_name;
                        $Email = $email;
                        $PhoneNo = substr(trim($mobileNumber) , -10);
                        $FinalPremium = round($premiumAmount, 2);
                        $ProductInfo = "ABC_" . $leadId;

                        $CKS_data = $Source . "|" . $Vertical . "|" . $PaymentMode . "|" . $ReturnURL . "|" . $UniqueIdentifier . "|" . $UniqueIdentifierValue . "|" . $CustomerName . "|" . $Email . "|" . $PhoneNo . "|" . $FinalPremium . "|" . $ProductInfo . "|" . $this->hash_key;

                        $CKS_value = hash($this->hashMethod, $CKS_data);

                        $manDateInfo = array(
                            "ApplicationNo" => $leadId,
                            "AccountHolderName" => $customer_name,
                            "BankName" => 'Axis Bank Limited',
                            "AccountNumber" => null,
                            "AccountType" => null,
                            "BankBranchName" => null,
                            "MICRNo" => null,
                            "IFSC_Code" => null,
                            "Frequency" => "ANNUALLY"
                        );

                        $dataPost = array(
                            "signature" => $CKS_value,
                            "Source" => $Source,
                            "Vertical" => $Vertical,
                            "PaymentMode" => $PaymentMode,
                            "ReturnURL" => $ReturnURL,
                            "UniqueIdentifier" => $UniqueIdentifier,
                            "UniqueIdentifierValue" => $UniqueIdentifierValue,
                            "CustomerName" => $CustomerName,
                            "Email" => $Email,
                            "PhoneNo" => $PhoneNo,
                            "FinalPremium" => $FinalPremium,
                            "ProductInfo" => $ProductInfo,
                            //"Additionalfield1"=> "",
                            "MandateInfo" => $manDateInfo
                        );

                        $data_string = json_encode($dataPost);

                        $encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
                        $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

                        $url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
                        $data = array(
                            'REQUEST' => $encrypted
                        );

                        $c = curl_init();
                        curl_setopt($c, CURLOPT_URL, $url);
                        curl_setopt($c, CURLOPT_POST, 0);
                        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
                        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

                        $result = curl_exec($c);
                        curl_close($c);
                        $result = json_decode($result, true);

                        $request_arr = ["lead_id" => $leadId, "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result) , "product_id" => "ABC", "type" => "payment_request_post"];

                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);

                        if ($result && $result['Status'])
                        {

                            $query_check = $this
                                ->db
                                ->query("select * from payment_txt_ids where lead_id='" . $leadId . "'")->row_array();

                            if (empty($query_check))
                            {
                                $data_arr = ["lead_id" => $leadId, "txt_id" => 1, "pg_type" => "New"];
                                $this
                                    ->db
                                    ->insert("payment_txt_ids", $data_arr);
                            }
                            else
                            {
                                $update_arr = ["cron_count" => 0];
                                $this
                                    ->db
                                    ->where("lead_id", $leadId);
                                $this
                                    ->db
                                    ->update("payment_txt_ids", $update_arr);
                            }

                            //echo "WELCOME To ABHI";
                            redirect($result['PaymentLink']);
                        }
                        else
                        {
                            if ($result['ErrorList'][0]['ErrorCode'] == 'E005')
                            {
                                $check_pg = $this
                                    ->obj_api_new
                                    ->real_pg_check($leadId);
                                if ($check_pg)
                                {
                                    redirect(base_url("payment_success_view_call_abc/" . $emp_id));
                                }
                                else
                                {
                                    echo "Error in Enquiry API";
                                }
                            }
                            else
                            {
                                echo $result['ErrorList'][0]['Message'];
                            }

                        }

                    }
                    else
                    {

                        redirect(base_url("/payment_error_view_call_abc/" . $emp_id . "/1"));

                    }

                }

            }
            else
            {
                //echo "Payment link has been expired, Please get in touch with your Branch RM";
                echo "Error in proposal create";

            }

        }
    }

    public function payment_error_view($emp_id, $status)
    {
        //$emp_id = $this->emp_id;
        $lead_arr = $this
            ->db
            ->query("select lead_id,email from employee_details where emp_id = '$emp_id' ")->row_array();
        $lead_id = $lead_arr['lead_id'];
        $email = $lead_arr['email'];
        $this
            ->load
            ->abc_portal_template('payment_error_view', compact('emp_id', 'lead_id', 'email', 'status'));

    }

    public function payment_success_view($emp_id_encrypt)
    {
        if (!is_numeric($emp_id_encrypt))
        {
            $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');
        }
        else
        {
            $emp_id = $emp_id_encrypt;
        }

        $encrypted = $this
            ->input
            ->post('RESPONSE');

        if ($encrypted)
        {
            $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
            $post_data = json_decode($decrypted, true);

            extract($post_data);

            if (isset($TxStatus) && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR')
            {
                $TxStatus = "success";
                $TxMsg = "No Error";
            }
        }

        $query = $this
            ->db
            ->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id='" . $emp_id . "' GROUP BY p.emp_id")->row_array();

        if ($query)
        {

            if (isset($TxRefNo))
            {
                $request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted, "res" => $decrypted, "product_id" => "ABC", "type" => "payment_response_post"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                $request_arr = ["payment_status" => $TxMsg, "premium_amount" => $amount, "payment_type" => $paymentMode, "txndate" => $txnDateTime, "TxRefNo" => $TxRefNo, "TxStatus" => $TxStatus, "json_quote_payment" => json_encode($post_data) ];

                $this
                    ->db
                    ->where_in('proposal_id', [$query['proposal_id']], false);
                $this
                    ->db
                    ->where('TxStatus != ', 'success');
                $this
                    ->db
                    ->update("payment_details", $request_arr);
            }

            if (isset($Registrationmode))
            {
                $query_emandate = $this
                    ->db
                    ->query("select * from emandate_data where lead_id=" . $query['lead_id'])->row_array();

                if ($EMandateStatus == 'MS')
                {
                    $mandate_status = 'Success';
                }
                elseif ($EMandateStatus == 'MI')
                {
                    $mandate_status = 'Emandate Pending';
                }
                elseif ($EMandateStatus == 'MR')
                {
                    $mandate_status = 'Emandate Received';
                }
                elseif ($EMandateStatus == '')
                {
                    $mandate_status = 'Emandate Pending';
                }
                else
                {
                    $mandate_status = 'Fail';
                }

                if ($query_emandate > 0)
                {

                    $arr = ["TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) , "Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason, "MandateLink" => $MandateLink];

                    $this
                        ->db
                        ->where("lead_id", $query['lead_id']);
                    $this
                        ->db
                        ->update("emandate_data", $arr);
                }
                else
                {

                    $arr = ["lead_id" => $query['lead_id'], "TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) , "Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason, "MandateLink" => $MandateLink];

                    $this
                        ->db
                        ->insert("emandate_data", $arr);
                }

                if ($mandate_status == 'Success')
                {
                    $this
                        ->obj_api_new
                        ->send_message($query['lead_id'], 'success');
                }

                if ($mandate_status == 'Fail')
                {
                    $this
                        ->obj_api_new
                        ->send_message($query['lead_id'], 'fail');
                }
				
				if ($paymentMode == 'PP' && ($Registrationmode == 'SAD' || $Registrationmode == 'EMI'))
                {
                    $this
                        ->obj_api_new
                        ->send_message($query['lead_id'], 'SAD_EMI_one');
                    $this
                        ->obj_api_new
                        ->send_message($query['lead_id'], 'SAD_EMI_two');
                }

            }

            $proposal_id = $query['proposal_id'];
            $extra_check = 0;
            $payment_data = $this
                ->db
                ->query("select payment_status,TxStatus from payment_details where proposal_id IN ($proposal_id)")->row_array();
            //print_pre($payment_data);exit;
            if ($payment_data['TxStatus'] == 'success')
            {
                //echo $query['lead_id'];exit;
                $check_result = $this
                    ->obj_api_new
                    ->policy_creation_call($query['lead_id']);
                //echo $this->db->last_query();
                // print_pre($check_result);exit;exit;
                if ($check_result['Status'] == 'Success')
                {

                    $data_policy[0] = $this
                        ->db
                        ->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();

                    $arr_coi = explode(',', $data_policy[0]['certificate_number']);

                    // $is_issue = $this
                    //     ->obj_api_new
                    //     ->coi_download_m($arr_coi[0]);

                    // if ($is_issue['status'] == 'error')
                    // {
                    //     $extra_check = 1;
                    // }


                    $dataTable = $this
                        ->db
                        ->query("SELECT epd.product_name,apr.certificate_number,apr.relationship_code,p.proposal_no FROM api_proposal_response AS apr
													LEFT JOIN proposal AS p
													ON apr.proposal_no_lead = p.proposal_no
													LEFT JOIN employee_policy_detail AS epd
													ON p.policy_detail_id = epd.policy_detail_id
													LEFT JOIN employee_details AS e
													ON e.emp_id = p.emp_id
													WHERE e.emp_id = $emp_id")->result_array();
                    // print_pre($this->db->last_query());exit;
                    foreach ($dataTable as $key => $value)
                    {
                        if ($value['relationship_code'] != '')
                        {
                            $temp = explode(",", $value['relationship_code']);
                            $temp = "'" . implode("', '", $temp) . "'";
                            $qry = "SELECT GROUP_CONCAT(reference_name) as members from master_family_relation WHERE relation_code IN (" . $temp . ")";
                            $dataTable[$key]['members'] = str_replace("Child", "Kid", $this
                                ->db
                                ->query($qry)->row_array() ['members']);
                        }
                    }
                    // print_pre($dataTable);exit;
                    if ($data_policy)
                    {
                        //$query_new = $this->db->query("select p.proposal_no,ed.lead_id,p.premium,GROUP_CONCAT(p.status) proposal_status from proposal as p,employee_details as ed  where p.emp_id = ed.emp_id and ed.emp_id ='$emp_id'");
                        //$data = $query_new->result_array();
                        $data = $this
                            ->db
                            ->query("select GROUP_CONCAT(p.status) proposal_status,GROUP_CONCAT(p.proposal_no) proposal_no,p.status,ed.lead_id,ed.customer_name,pd.txndate from proposal as p,employee_details as ed,payment_details as pd  where p.emp_id = ed.emp_id and p.id = pd.proposal_id and ed.emp_id ='$emp_id' GROUP BY proposal_no")->result_array();

                        $arr = explode(',', $data[0]['proposal_status']);
                        $activity_stage = $this
                            ->db
                            ->select("*")
                            ->from("user_activity")
                            ->where("emp_id", $emp_id)->get()
                            ->row_array();
                        if ($activity_stage["type"] <= 7)
                        {
                            $this
                                ->db
                                ->where("emp_id", $emp_id);
                            $this
                                ->db
                                ->update("user_activity", ['type' => 7]);
                        }
                        $this
                            ->load
                            ->abc_portal_template("thankyou_view_abc", compact('data_policy', 'data', 'extra_check', 'dataTable'));
                    }

                }
                else
                {
                    //echo "in";exit;
                    redirect(base_url("/payment_error_view_call_abc/" . $emp_id . "/0"));

                }

            }
            else
            {

                /*$query_new = $this->db->query("select p.proposal_no,ed.lead_id,p.premium,GROUP_CONCAT(p.status) proposal_status from proposal as p,employee_details as ed  where p.emp_id = ed.emp_id and ed.emp_id ='$emp_id'");
                 $data = $query_new->result_array();*/
                $data = $this
                    ->db
                    ->query("select GROUP_CONCAT(p.status) proposal_status,GROUP_CONCAT(p.proposal_no) proposal_no,p.status,ed.lead_id,ed.customer_name,pd.txndate from proposal as p,employee_details as ed,payment_details as pd  where p.emp_id = ed.emp_id and p.id = pd.proposal_id and ed.emp_id ='$emp_id' GROUP BY proposal_no")->result_array();

                $arr = explode(',', $data[0]['proposal_status']);
                $activity_stage = $this
                    ->db
                    ->select("*")
                    ->from("user_activity")
                    ->where("emp_id", $emp_id)->get()
                    ->row_array();
                if ($activity_stage["type"] <= 7)
                {
                    $this
                        ->db
                        ->where("emp_id", $emp_id);
                    $this
                        ->db
                        ->update("user_activity", ['type' => 7]);
                }
                $dataTable = $this
                    ->db
                    ->query("SELECT epd.product_name,apr.certificate_number,apr.relationship_code,p.proposal_no FROM api_proposal_response AS apr
												LEFT JOIN proposal AS p
												ON apr.proposal_no_lead = p.proposal_no
												LEFT JOIN employee_policy_detail AS epd
												ON p.policy_detail_id = epd.policy_detail_id
												LEFT JOIN employee_details AS e
												ON e.emp_id = p.emp_id
												WHERE e.emp_id = $emp_id")->result_array();
                foreach ($dataTable as $key => $value)
                {
                    if ($value['relationship_code'] != '')
                    {
                        $temp = explode(",", $value['relationship_code']);
                        $temp = "'" . implode("', '", $temp) . "'";
                        $qry = "SELECT GROUP_CONCAT(reference_name) as members from master_family_relation WHERE relation_code IN (" . $temp . ")";
                        $dataTable[$key]['members'] = str_replace("Child", "Kid", $this
                            ->db
                            ->query($qry)->row_array() ['members']);
                    }
                }
                if (in_array('Success', $arr))
                {
                    $data_policy[0] = $this
                        ->db
                        ->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();

                    $arr_coi = explode(',', $data_policy[0]['certificate_number']);

                    $is_issue = $this
                        ->obj_api_new
                        ->coi_download_m($arr_coi[0]);

                    if ($is_issue['status'] == 'error')
                    {
                        $extra_check = 1;
                    }

                    $this
                        ->load
                        ->abc_portal_template("thankyou_view_abc", compact('data_policy', 'data', 'extra_check', 'dataTable'));
                }
                else if (!in_array('Success', $arr) && $data['status'] == 'Payment Received')
                {
                    $extra_check = 2;
                    $this
                        ->load
                        ->abc_portal_template("thankyou_view_abc", compact('data', 'extra_check', 'dataTable'));
                }
                else
                {
                    $extra_check = 3;
                    $this
                        ->load
                        ->abc_portal_template("thankyou_view_abc", compact('data', 'extra_check', 'dataTable'));
                }

                //print_pre($data);	exit;
                //$this->load->abc_portal_template("thankyou_view_abc",compact('data','amount'));
                
            }

        }
        else
        {

            echo "Payment link has been expired, Please get in touch with your Branch RM";

        }

    }

    public function check_error_data()
    {
        $emp_id = $this->emp_id;
        $emp_id_encrypt = encrypt_decrypt_password($this->emp_id);

        $query_check = $this
            ->db
            ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();
        //echo $this->db->last_query();exit;
        if ($query_check > 0)
        {

            $query = $this
                ->db
                ->query("select pd.payment_status,p.status as proposal_status,p.count as p_count from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id=p.emp_id and p.id=pd.proposal_id and ed.emp_id = '$emp_id' group by p.id")->row_array();

            if ($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] < 3)
            {
                // quote genarate,payment done but policy pending
                $data = array(
                    "status" => "1",
                    "check" => "2",
                    "url" => base_url('payment_success_view_call_abc') ,
                );

            }
            else if ($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] >= 3)
            {
                // policy pending 3 count hit exceeded
                $data = array(
                    "status" => "2",
                    "check" => "3",
                    "url" => "#"
                );
            }
            else
            {
                // quote genarate but payment pending
                $data = array(
                    "status" => "1",
                    "check" => "1",
                    "url" => base_url('payment_redirection') ,
                );

            }

        }
        else
        {

            $query = $this
                ->db
                ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and count < 3 and status = 'error'")->row_array();

            if ($query > 0)
            {
                // quote pending,payment pending
                $data = array(
                    "status" => "1",
                    "check" => "1",
                    "url" => base_url('payment_redirection') ,
                );
            }
            else
            {
                // quote pending 3 count hit exceeded
                $data = array(
                    "status" => "2",
                    "check" => "3",
                    "url" => "#"
                );
            }

        }

        echo json_encode($data);

    }

    public function razorpay_returnurl()
    {
        $post_data = $this
            ->input
            ->post(NULL, true); //returns all POST items with XSS filter
        $this
            ->load
            ->view('PG_view/razor_returnpage', compact('post_data'));
    }

    public function redirect_pg_url()
    {
        $post_data = array();
        $this
            ->load
            ->view('PG_view/razor_redirect_pg_page', compact('post_data'));
    }

    function setCustomerSession($aD2CSession)
    {
        //unset previous user data from session.
        if ($this
            ->session
            ->userdata('abc_session'))
        {
            $this
                ->session
                ->unset_userdata('abc_session');
        }

        //set new user data in session.
        $this
            ->session
            ->set_userdata($aD2CSession);
        /* Regenerate a new session upon successful authentication. Any session token used prior to
          login should be discarded and only the new token should be assigned for the user till the user
          logs out.
          This session token should be properly expired when the user logs out. */
        $this
            ->session
            ->regenerate_id();
        $session_id = session_id();
        $aD2CSession = $this
            ->session
            ->userdata('abc_session');
        $emp_id = encrypt_decrypt_password($aD2CSession['emp_id'], 'D');
        $rsEmp = $this
            ->db
            ->select("id, updated_time")
            ->where(["emp_id" => $emp_id])->get("tbl_leadid_session");
        if ($rsEmp->num_rows() > 0)
        {
            //update record
            $aRow = $rsEmp->row();
            $id = $aRow->id;

            $data = array(
                'sessionid' => $session_id,
                'updated_time' => time() ,
            );
            $this
                ->db
                ->where('id', $id);
            $this
                ->db
                ->update('tbl_leadid_session', $data);

        }
        else
        {
            $aLeadSession = ["emp_id" => $emp_id, "sessionid" => $session_id, "updated_time" => time() ];
            $this
                ->db
                ->insert("tbl_leadid_session", $aLeadSession);

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
    public function updateDropOffFlagValue($emp_id = 0)
    {
        if (!empty($emp_id))
        {
            $seconds = 30;
            $date_now = date("Y-m-d H:i:s");
            $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
            $request_arr_dropoff = ["dropoff_flag" => "0", 'modified_date' => $moddate];
            $this
                ->db
                ->where("emp_id", $emp_id);
            $this
                ->db
                ->update("employee_details", $request_arr_dropoff);
        }
    }

    function logs_docs_insert($lead_id, $request, $type, $response = "")
    {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request) , "type" => $type, "product_id" => 'ABC'];
        // $this->db->insert("logs_docs", $request_arr);
        $logs_array['data'] = $request_arr;
        $this
            ->Logs_m
            ->insertLogs($logs_array);
    }

    function logs_post_data_insert($lead_id, $request, $type, $response = "")
    {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request) , "type" => $type, "product_id" => 'ABC'];
        $logs_array['data'] = $request_arr;
        $this
            ->Logs_m
            ->insertLogs($logs_array);
    }

    function dropoff_journey($emp_id_encrypt, $send_type)
    {
        $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');

        if ($send_type == 1)
        {
            $utm_string = "?utm_source=abhi-dropoff&utm_medium=sms&utm_campaign=abhi-dropoff-campaign";
        }
        else
        {
            $utm_string = "?utm_source=abhi-dropoff&utm_medium=email&utm_campaign=abhi-dropoff-campaign";
        }

        $policy_detail_id = $this
            ->db
            ->get_where("employee_product_details", array(
            "emp_id" => $emp_id
        ))->row_array();
        $policy_id = $policy_detail_id['policy_id'];

        $val = $this
            ->db
            ->query("select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id from employee_details as ed,user_activity as ua where ed.emp_id = ua.emp_id AND ua.status = 1 AND ua.emp_id = '$emp_id'")->row_array();
        /*echo $this->db->last_query();
         print_pre($val);exit;*/
        $aD2CSession['abc_session'] = array(
            'emp_id' => $emp_id_encrypt,
            'lead_id' => $val['lead_id'],
            'dropoff' => true
        );

        $this->setCustomerSession($aD2CSession);

        $logs_type = "dropoff_journey_return";

        $arr = ["emp_id" => $emp_id, "type" => $val['type']];
        //echo $val['type'];exit;
        if ($val['type'] == 1)
        {
            $arr['link'] = base_url('comprehensive_product_abc');
            $this->logs_docs_insert($lead_id, $arr, $logs_type);
            redirect(base_url('comprehensive_product_abc' . $utm_string));
        }
        elseif ($val['type'] == 2)
        {
            $arr['link'] = base_url('quotes_abc/' . $policy_id);
            $this->logs_docs_insert($lead_id, $arr, $logs_type);
            redirect(base_url('quotes_abc/' . $policy_id . $utm_string));
        }
        elseif ($val['type'] == 3)
        {
            $arr['link'] = base_url('member_proposer_detail');
            $this->logs_docs_insert($lead_id, $arr, $logs_type);
            redirect(base_url('member_proposer_detail' . $utm_string));
        }
        elseif ($val['type'] == 4)
        {
            $arr['link'] = base_url('member_detail_product_abc');
            $this->logs_docs_insert($lead_id, $arr, $logs_type);
            redirect(base_url('member_detail_product_abc' . $utm_string));
        }
        elseif ($val['type'] == 5 || $val['type'] == 6)
        {
            $arr['link'] = base_url('payment_redirection_abc');
            $this->logs_docs_insert($lead_id, $arr, $logs_type);
            redirect(base_url('payment_redirection' . $utm_string));
        }
        else
        {
            $arr['link'] = base_url('payment_success_view_call_abc/' . $emp_id);
            $this->logs_docs_insert($lead_id, $arr, $logs_type);

            redirect(base_url('payment_success_view_call_abc/' . $emp_id . $utm_string));
        }
    }

}

