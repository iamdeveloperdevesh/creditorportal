<?php

/**
 * Dump from SI
 *
 * @author Ankita Badak <ankita.badak@fyntune.com>
 */
class SiCron extends CI_Controller {

    const FTPSERVER =  '103.144.217.49';
    const FTPUSERNAME = 'ABHI_AXIS';//'Fyntune';
    const FTPPASSWORD = 'Abhi@34!xis';//'Fyntune@123';


    public $granite_path;
    public $processed_path; 
    public $unprocessed_path;
    public $excel_path;
    public $csv_path;
    public $dwnld_reg_file_name;
    public $upld_reg_file_name;
    public $dwnld_txn_file_name;
    public $upld_txn_file_name;


    function __construct() {
        parent::__construct();

        $dwnld_reg_file_name = date("dmY")."_REG_DWNLD_GRP.xls";
        $upld_reg_file_name = date("dmY")."_REG_UPLD_GRP.xls";
        $dwnld_txn_file_name = date("dmY")."_TXN_DWNLD_GRP.xls";
        $upld_txn_file_name = date("dmY")."_TXN_UPLD_GRP.xls";      
    
        // $remote_path     = "/var/ftp/pub/tmp";       
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        $this->load->model("Logs_m");
    }
    function send_si_link_alert_48_hours()
    {
        $records = $this->db
            ->select('pd.payment_mode,
                pd.si_auto_renewal,
                ed.emp_id,
                ed.lead_id,
                GROUP_CONCAT(DISTINCT(epd.policy_sub_type_id) ORDER BY epd.policy_sub_type_id) AS policy_subtype_id,
                ed.ISNRI,
                p.created_date,
                ed.email,
                ed.mob_no,
                ed.emp_firstname,
                ed.emp_lastname,
                ed.si_link_sent_on,
                mpst.product_code,
                mpst.click_pss_url,
                p.EasyPay_PayU_status,
                mpst.product_name,
                epd.parent_policy_id')
            ->from('employee_policy_detail AS epd,
                product_master_with_subtype AS mpst,
                employee_details AS ed,
                proposal AS p,
                payment_details AS pd')
            ->where('p.emp_id = ed.emp_id')
            ->where('p.id = pd.proposal_id')
            ->where('epd.product_name = mpst.id')
            ->where('mpst.policy_subtype_id = epd.policy_sub_type_id')
            ->where('p.policy_detail_id = epd.policy_detail_id')
            ->where('ed.is_otp_verified = 0')
            ->where('ed.si_link_sent_on  > DATE_SUB(NOW(), INTERVAL 48 HOUR)')
            ->where('ed.si_link_sent_on <= DATE_SUB(NOW(), INTERVAL 24 HOUR)')
            ->where('ed.reminderone_sent = 0')
            ->group_by('ed.emp_id')
            ->get()
            ->result_array();

        $alertID = 'A1625';
        // print_pre($records);exit;
        if ($records > 0) {
            foreach ($records as $rec) {
            //     //get emp_id for current row
                $emp_id = $rec['emp_id'];

                $policy_policy_subtype_id_string = $rec['policy_subtype_id'];
                $policy_subtype_id_arr = explode(',', $policy_policy_subtype_id_string);
                $pname = [];
                foreach($policy_subtype_id_arr as $policy_subtype_id){
                    // echo $policy_subtype_id.'<br>';
                    if($policy_subtype_id == 1){
                        $pro_name = 'Group Activ Health';
                    }else if($policy_subtype_id == 8){
                        $pro_name = 'Group Protect';
                    }else{
                        $pro_name = 'Group Activ Secure';
                    }
                    
                    array_push($pname, $pro_name);
                    
                }
                

                $url = base_url("si_summary/" . $emp_id . '/' . $rec['parent_policy_id']);
                $name_data = "summary_link";

                $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url_req,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",

                    ),
                ));

                $result = curl_exec($curl);

                curl_close($curl);

                $request_arr = ["lead_id" => $rec['lead_id'], "req" => json_encode($url_req), "res" => json_encode($result), "product_id" => $rec['product_code'], "type" => "reminder_48_hr_bitly_url_" . $name_data];


                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                $data = json_decode($result, true);

                if ($data['txtly'] == '') {
                    $data['txtly'] = $url;
                }

                $senderID = 1;

                $full_name = $rec['emp_firstname'] . ' ' . $rec['emp_lastname']; //Member Full Name
                if(strlen($full_name) > 30){
                    $full_name = substr($full_name, 0, 30);
                }
                $AlertV1 = $full_name;
                $AlertV2 = $data['txtly']; //Combined Link to Both Enrollment Forms
                $AlertV3 = $rec['lead_id']; //Enrollment Form Number1

                $AlertV4 = (isset($pname[1])) ? $rec['lead_id'] : ""; //Enrollment Form Number2

                $AlertV5 = "Axis Bank"; //Bank Full Name
                $AlertV6 = $pname[0]; //Product Name1

                $AlertV7 = (isset($pname[1])) ? $pname[1] : ""; //Product Name2
                $AlertV8 =  $rec['lead_id']; //Enrollment Form for Product1
                $AlertV9 =  (isset($pname[1])) ? $rec['lead_id'] : ""; //Enrollment Form for Product2
                $AlertV10 = date('m-d-Y', strtotime($rec['created_date'])); //Date of Enrollment Form Received for Product1
                $AlertV11 =  (isset($pname[1])) ? date('m-d-Y', strtotime($rec['created_date'])) : ""; //Date of Enrollment Form for Product2

                $AlertV12 = '18002707000'; //Help Line Number

                $last_valid_date = date('Y-m-d h:i:s', strtotime($rec['si_link_sent_on'] . ' + 3 days'));

                $isNri = $rec['ISNRI'];
                $product_id = $rec['product_code'];

                $dataArray['emp_id'] = $emp_id;
                $dataArray['isNri'] = $isNri;
                $dataArray['product_id'] = $product_id;
                $alertMode = helper_validate_is_nri($dataArray);
                if ($data['txtly'] == $url)
                {
                    if(strlen($url) > 30){
                        $alertMode = 1;
                    }
                }
                $parameters = ["RTdetails" => [
                    "PolicyID" => '', "AppNo" => 'HD100017934', "alertID" => $alertID, "channel_ID" => $rec['product_name'], "Req_Id" => 1, "field1" => '', "field2" => '', "field3" => '', "Alert_Mode" => $alertMode, "Alertdata" => ["mobileno" => substr(trim($rec['mob_no']), -10), "emailId" => $rec['email'], "AlertV1" => $AlertV1, "AlertV2" => $AlertV2, "AlertV3" => $AlertV3, "AlertV4" => $AlertV4, "AlertV5" => $AlertV5, "AlertV6" => $AlertV6, "AlertV7" => $AlertV7, "AlertV8" => $AlertV8, "AlertV9" => $AlertV9, "AlertV10" => $AlertV10,  "AlertV11" => $AlertV11,  "AlertV12" => $AlertV12,  "last_valid_date" => $last_valid_date]
                ]];

                $parameters = json_encode($parameters);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $rec['click_pss_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $parameters,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",

                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $request_arr = ["lead_id" => $rec['lead_id'], "req" => json_encode($parameters), "res" => json_encode($response), "product_id" => $rec['product_code'], "type" => "reminder_48_hr_sms_logs_" . $name_data];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                //update flag reminder sent
                $this->db->where("emp_id", $emp_id);
                $this->db->update("employee_details", ["reminderone_sent" => 1]);
            }
        }
        echo "Cron executed successfully ! for ".count($records)." records.";
    }


    //Created on 8 feb 2021, by Upendra
    //updated on 9 & 10th feb 2021, by Upendra
    function send_si_link_alert_24_hours()
    {
        $records = $this->db
            ->select('pd.payment_mode,
                pd.si_auto_renewal,
                ed.emp_id,
                ed.lead_id,
                GROUP_CONCAT(DISTINCT(epd.policy_sub_type_id) ORDER BY epd.policy_sub_type_id) AS policy_subtype_id,
                ed.ISNRI,
                p.created_date,
                ed.email,
                ed.mob_no,
                ed.emp_firstname,
                ed.emp_lastname,
                ed.si_link_sent_on,
                mpst.product_code,
                mpst.click_pss_url,
                p.EasyPay_PayU_status,
                mpst.product_name,
                epd.parent_policy_id')
            ->from('employee_policy_detail AS epd,
                product_master_with_subtype AS mpst,
                employee_details AS ed,
                proposal AS p,
                payment_details AS pd')
            ->where('p.emp_id = ed.emp_id')
            ->where('p.id = pd.proposal_id')
            ->where('epd.product_name = mpst.id')
            ->where('mpst.policy_subtype_id = epd.policy_sub_type_id')
            ->where('p.policy_detail_id = epd.policy_detail_id')
            ->where('ed.is_otp_verified = 0')
            ->where('ed.si_link_sent_on  >= DATE_SUB(NOW(), INTERVAL 72 HOUR)')
            ->where('ed.si_link_sent_on <= DATE_SUB(NOW(), INTERVAL 48 HOUR)')
            ->where('ed.remindertwo_sent = 0')
            ->group_by('ed.emp_id')
            ->get()
            ->result_array();
        // print_pre($records);exit;
        $alertID = 'A1626';

        if ($records > 0) {
            foreach ($records as $rec) {
                //get emp_id for current row
                $emp_id = $rec['emp_id'];

                $policy_policy_subtype_id_string = $rec['policy_subtype_id'];
                $policy_subtype_id_arr = explode(',', $policy_policy_subtype_id_string);
                $pname = [];
                foreach($policy_subtype_id_arr as $policy_subtype_id){
                    // echo $policy_subtype_id.'<br>';
                    if($policy_subtype_id == 1){
                        $pro_name = 'Group Activ Health';
                    }else if($policy_subtype_id == 8){
                        $pro_name = 'Group Protect';
                    }else{
                        $pro_name = 'Group Activ Secure';
                    }
                    
                    array_push($pname, $pro_name);
                    
                }

                $url = base_url("si_summary/" . $emp_id . '/' . $rec['parent_policy_id']);
                $name_data = "summary_link";

                $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url_req,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",

                    ),
                ));

                $result = curl_exec($curl);

                curl_close($curl);

                $request_arr = ["lead_id" => $rec['lead_id'], "req" => json_encode($url_req), "res" => json_encode($result), "product_id" => $rec['product_code'], "type" => "reminder_24_hr_bitly_url_" . $name_data];


                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                $data = json_decode($result, true);

                if ($data['txtly'] == '') {
                    $data['txtly'] = $url;
                }

                $senderID = 1;

                $full_name = $rec['emp_firstname'] . ' ' . $rec['emp_lastname']; //Member Full Name
                if(strlen($full_name) > 30){
                    $full_name = substr($full_name, 0, 30);
                }
                $AlertV1 = $full_name;
                $AlertV2 = $data['txtly']; //Combined Link to Both Enrollment Forms
                $AlertV3 = $rec['lead_id']; //Enrollment Form Number1

                $AlertV4 = (isset($pname[1])) ? $rec['lead_id'] : ""; //Enrollment Form Number2

                $AlertV5 = "Axis Bank"; //Bank Full Name
                $AlertV6 = $pname[0]; //Product Name1

                $AlertV7 = (isset($pname[1])) ? $pname[1] : ""; //Product Name2
                $AlertV8 =  $rec['lead_id']; //Enrollment Form for Product1
                $AlertV9 =  (isset($pname[1])) ? $rec['lead_id'] : ""; //Enrollment Form for Product2
                $AlertV10 = date('m-d-Y', strtotime($rec['created_date'])); //Date of Enrollment Form Received for Product1
                $AlertV11 =  (isset($pname[1])) ? date('m-d-Y', strtotime($rec['created_date'])) : ""; //Date of Enrollment Form for Product2

                $AlertV12 = '18002707000'; //Help Line Number

                $last_valid_date = date('Y-m-d h:i:s', strtotime($rec['si_link_sent_on'] . ' + 3 days'));

                $isNri = $rec['ISNRI'];
                $product_id = $rec['product_code'];

                $dataArray['emp_id'] = $emp_id;
                $dataArray['isNri'] = $isNri;
                $dataArray['product_id'] = $product_id;
                $alertMode = helper_validate_is_nri($dataArray);
                if ($data['txtly'] == $url)
                {
                    if(strlen($url) > 30){
                        $alertMode = 1;
                    }
                }
                $parameters = ["RTdetails" => [
                    "PolicyID" => '', "AppNo" => 'HD100017934', "alertID" => $alertID, "channel_ID" => $rec['product_name'], "Req_Id" => 1, "field1" => '', "field2" => '', "field3" => '', "Alert_Mode" => $alertMode, "Alertdata" => ["mobileno" => substr(trim($rec['mob_no']), -10), "emailId" => $rec['email'], "AlertV1" => $AlertV1, "AlertV2" => $AlertV2, "AlertV3" => $AlertV3, "AlertV4" => $AlertV4, "AlertV5" => $AlertV5, "AlertV6" => $AlertV6, "AlertV7" => $AlertV7, "AlertV8" => $AlertV8, "AlertV9" => $AlertV9, "AlertV10" => $AlertV10,  "AlertV11" => $AlertV11,  "AlertV12" => $AlertV12,  "last_valid_date" => $last_valid_date]
                ]];

                $parameters = json_encode($parameters);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $rec['click_pss_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $parameters,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",

                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $request_arr = ["lead_id" => $rec['lead_id'], "req" => json_encode($parameters), "res" => json_encode($response), "product_id" => $rec['product_code'], "type" => "reminder_24_hr_sms_logs_" . $name_data];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);
                //update flag reminder sent
                $this->db->where("emp_id", $emp_id);
                $this->db->update("employee_details", ["remindertwo_sent" => 1]);
            }
        }
        echo "Cron executed successfully ! for ".count($records)." records.";
    }

    // this cron is used to call emandate HB on emandate Success
    function emandate_HB_call($lead_id)
    {   

       // $query_check = $this->db->query("select emd.micr,emd.sid_start_date,emd.sid_end_date,ed.lead_id,ed.product_id,ed.json_qote,apr.policy_no,apr.certificate_number,apr.proposal_no,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr,emandate_data as emd where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and apr.mandate_send_status = 0 and emd.lead_id = ed.lead_id and emd.status = 'Success' and emd.is_hb_call = 0 group by p.emp_id")->result_array();
       $query_check = $this->db->query("SELECT emd.micr,emd.no_of_tran,emd.customer_uid,p.premium,pd.payment_mode,pd.payment_status,pd.transaction_no,pd.EasyPayId,emd.sid_start_date,emd.sid_end_date,emd.status,ed.lead_id,ed.product_id,ed.json_qote,apr.certificate_number,apr.proposal_no,apr.pr_api_id FROM employee_details AS ed,proposal AS p,api_proposal_response AS apr,emandate_data AS emd, payment_details AS pd WHERE ed.emp_id = p.emp_id AND p.id = pd.proposal_id AND p.proposal_no = apr.proposal_no_lead AND p.emp_id = apr.emp_id AND p.status in('Success','Payment Received') AND pd.si_auto_renewal = 'Y' AND apr.mandate_send_status = 0 AND emd.lead_id = ed.lead_id AND pd.payment_status IN ('No Error','Success') AND emd.status = 'Success' AND emd.is_hb_call = 0 AND ed.lead_id ='".$lead_id."'")->result_array();
        //echo $this->db->last_query();
        
        if($query_check){ 
        
            foreach ($query_check as $val)
            {
                
                if($val['payment_status'] == 'No Error'){
                    $payment_id = $val['transaction_no'];
                }else{
                    $payment_id = $val['EasyPayId'];
                }
                //print_pre($val);
                //BIZ HB call start
                $json_data = json_decode($val['json_qote'],true);
                $curl = curl_init();
                $url = 'https://bizpre.adityabirlahealth.com/ABHICL_Generic/Service1.svc/AddEmendateDetails';
                //$url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';
                
                // echo date('m/d/Y',strtotime($end_date));exit;
                $req_arr = [
                      'EmendateDeatails' => 
                      [
                        'EmendateList' => 
                        [ 
                          [
                            'Bank_Name' => ($json_data['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other',
                            'Debit_Account_Number' => $json_data['ACCOUNTNUMBER'],                             
                            'Mandate_Start_Date' => date('m/d/Y',strtotime($val['sid_start_date'])) ,
                            'Mandate_End_Date' => date('m/d/Y',strtotime($val['sid_end_date'])),
                            'Account_Type' => 'Saving',
                            'Bank_Branch_Name' => $json_data['BRANCH_NAME'],
                            'MICR' => ($val['micr'] != '') ? $val['micr'] : '123',
                            'IFSC' => $json_data['IFSCCODE'],
                            'Frequency' => "1",
                            'Policy_Number' => $val['certificate_number'],
                            "Proposal_Number"=>$val['proposal_no'],
                            "Source"=>"AXIS_GHI",
                            "Mandate_Type"=>"MT",
                            "Payment_ID"=>$payment_id,
                            //"Account_ID"=>"845677",
                            //"Order_ID"=>"09674ODR",
                            //"Customer_ID"=>$val['customer_uid'],
                            //"Token_ID"=>"TKN0001",
                            "Lead_ID"=>$lead_id,
                            "Auto_Debit_Registration_Status"=>"Yes",
                            "Registration_Rejection_Reason"=>"N",
                            "Mandate_Category"=>"N",
                            //"Mandate_Registration_Number"=>"REG0001",
                            //"Debit_Transaction_Reference_Number"=>"DTR001",
                            "Debit_Date"=>date('m/d/Y'),
                            "Debit_Amount"=>$val['premium'],
                            "Debit_Status"=>"Active",
                            "Debit_failure_Reason"=>"N",
                            "Debit_Attempt"=>"1"
                            


                          ],
                        ],
                      ],
                    ];
                // print_pre($req_arr);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($req_arr) ,
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Length: " . strlen(json_encode($req_arr)) ,
                    "Content-Type: application/json",
                    "Host: bizpre.adityabirlahealth.com"
                ) ,
                ));

                $response = curl_exec($curl);
              // print_pre($response);exit;
                curl_close($curl);

                if($response){
                    $response = json_decode($response,true);
                    //echo '=='.$response['ErrorObj'][0]['ErrorMessage'];exit;
                    if($response['ErrorObj'][0]['ErrorMessage'] == 'Success'){
                        $update_arr = ["mandate_send_status" => 1];
                        $this->db->where("proposal_no",$val['proposal_no']);
                        $this->db->update("api_proposal_response",$update_arr);
                        //echo $this->db->last_query();
                    }
                }
                
                //  
                
                //echo $response['ErrorObj'][0]['ErrorMessage'];exit;

                $request_arr = ["lead_id" => $lead_id, "req" => json_encode($req_arr),"res" => json_encode($response),"product_id"=> $val['product_id'], "type"=>"emandate_HB_post"];

                $dataArray['tablename'] = 'logs_docs'; 
                $dataArray['data'] = $request_arr; 
                $this->Logs_m->insertLogs($dataArray);
                //BIZ HB call end                        
            }            
        }
        return true;
    }

     function si_upload_file(){
        if(isset($_FILES['upload_file'])){
            $sftp_path = '/COI/';
            $target_dir = APPPATH."resources/SI_PROCESS/";
            $upld_reg_file_name = date("dmY")."_REG_DWNLD_GRP.xls";
            $target_file = $target_dir .$upld_reg_file_name ;
            if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["upload_file"]["name"])). " has been uploaded.";
                $connectServer = $this->connectServer();
                if($connectServer['status'] == 400){

                    echo json_encode($connectServer);exit;
                    // return $connectServer;
                }
                $connection = $connectServer['connection'];
                // echo APPPATH."resources/SI_PROCESS/".$upld_reg_file_name."<--------->".$sftp_path.$upld_reg_file_name;exit;
                $uploadFile = $this->uploadFile($connection,APPPATH."resources/SI_PROCESS/".$upld_reg_file_name,$sftp_path.$upld_reg_file_name);

                // return $uploadFile;
                echo json_encode($uploadFile);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }

        }
        $this->load->view("si_upload_file",true);
      }

     function si_upload_file_bk(){
        if(isset($_FILES['upload_file'])){
            $sftp_path = '/COI/';
            $target_dir = APPPATH."resources/SI_PROCESS/";
            $upld_reg_file_name = date("dmY")."_REG_DWNLD_GRP.xls";
            $target_file = $target_dir .$upld_reg_file_name ;
            if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["upload_file"]["name"])). " has been uploaded.";
                $connectServer = $this->connectServer();
                if($connectServer['status'] == 400){

                    echo json_encode($connectServer);exit;
                    // return $connectServer;
                }
                $connection = $connectServer['connection'];
                // echo APPPATH."resources/SI_PROCESS/".$upld_reg_file_name."<--------->".$sftp_path.$upld_reg_file_name;exit;
                $uploadFile = $this->uploadFile($connection,APPPATH."resources/SI_PROCESS/".$upld_reg_file_name,$sftp_path.$upld_reg_file_name);
                if($uploadFile['status'] == 200){
                    $excel_path = APPPATH."resources/SI_PROCESS_UPLD/".$upld_reg_file_name;
                    $remote_path = "/COI/".$upld_reg_file_name;
                    // echo $remote_path;exit;
                    if (!is_dir(APPPATH."resources/SI_PROCESS_UPLD/")) {
                        mkdir(APPPATH."resources/SI_PROCESS_UPLD/");
                    }
                    //copy file from remote server
                    $connectServer = $this->connectServer();
                    if($connectServer['status'] == 400){
                        echo json_encode($connectServer);exit;
                    }
                    $connection = $connectServer['connection'];
                    $copyFile = $this->copyFile($connection,$remote_path,$excel_path);
                    //$copyFile = $this->copyFile($connection,$remote_path,$excel_path);//$this->copyFile($connection,$remote_path,$excel_path);
                    if($copyFile['status'] == 400){

                        echo json_encode($copyFile);exit;
                    }else{
                        $dwnlURL = base_url().'resources/SI_PROCESS_UPLD/'.$upld_reg_file_name;
                        echo '<a href="'.$dwnlURL.'" class = "btn">Download uploaded File</a>';
                    }
                }
                

            } else {
                echo "Sorry, there was an error uploading your file.";
            }

        }
        $this->load->view("si_upload_file",true);
      }


    public function downloadRegDoc(){       
        $dwnld_reg_file_name = date("dmY")."_REG_DWNLD_GRP.xls";
        $csv_path = APPPATH."resources/SI_PROCESS_UPLD/".date("dmY")."_REG_DWNLD_GRP.csv";
        $excel_path = APPPATH."resources/SI_PROCESS_UPLD/".date("dmY")."_REG_DWNLD_GRP.xls";
        $remote_path = "/COI/".date("dmY")."_REG_DWNLD_GRP.xls";
        // echo $remote_path;exit;
        if (!is_dir(APPPATH."resources/SI_PROCESS_UPLD/")) {
            mkdir(APPPATH."resources/SI_PROCESS_UPLD/");
        }
        //copy file from remote server
        $connectServer = $this->connectServer();
        if($connectServer['status'] == 400){
            echo json_encode($connectServer);exit;
        }
        $connection = $connectServer['connection'];
        $copyFile = $this->copyFile($connection,$remote_path,$excel_path);
        //$copyFile = $this->copyFile($connection,$remote_path,$excel_path);//$this->copyFile($connection,$remote_path,$excel_path);
        
        if($copyFile['status'] == 400){

            echo json_encode($copyFile);exit;
        }
        if(file_exists($excel_path)){
            $excelToCsv = $this->excelToCsv($excel_path,$csv_path);
            if($excelToCsv['status'] == 400){
                // return $excelToCsv;
                echo json_encode($excelToCsv);exit;
            }
            $csvToArray = $this->csvToArray($csv_path); 
            if($csvToArray['status'] == 400){
                // return $csvToArray;
                echo json_encode($csvToArray);exit;                
            }

            $data = $csvToArray['data'];
            //print_pre($data);exit;
            foreach($data as $key => $val){
                $arr = ["company_code" => $val['cmpny_code'],"company_uid" => $val['customer_uid'], "reject_reason" => $val['remark'], "sid_start_date" => $val['start_date'], "sid_end_date" => $val['end_date'],"registration_acceptane_no" => $val['1'],"si_mandate_date" => $val['2'],"account_type" => $val['3'],"micr" => $val['4']];
                if($val['status'] != ''){
                    $arr["status"] = $val['status'];
                    $arr["mandate_date"] = date("Y-m-d H:i:s");
                }
                
                $this->db->where("lead_id",$val['lead_id']);
                $this->db->where("product_id",$val['product_id']);
                $this->db->update("emandate_data",$arr);
                //si_emandate_data
                if($val['status'] != ''){
                    if(strtolower($val['status']) == 'success'){
                        $this->emandate_HB_call($val['lead_id']);
                    }
                    
                    $this->send_message($val['status'],$val['lead_id'],$val['remark'],$val['2'],$val['end_date']);
                }
                //exit;
            }
            $return = [
                'status'=>200,
                'message'=>'data updated successfully'
            ];
            echo json_encode($return);exit; 
        }else{
            $return = [
                'status'=>400,
                'message'=>'File Not found on local server'
            ];
            echo json_encode($return);exit;  
        }
    }

    //function send_message($type,$lead_id,$reject_reason,$si_mandate_date,$si_end_date){
    function send_message($type,$lead_id,$reject_reason,$si_mandate_date,$end_date){

        /*$query_check_all = $this->db->query("SELECT apr.gross_premium,apr.end_date,p.premium,p.created_date,mpst.policy_subtype_id,ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote,mpst.plan_code AS master_plan,pd.si_amount,apr.certificate_number AS certificate_number
                                        FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal AS p,payment_details AS pd,api_proposal_response AS apr
                                        WHERE p.emp_id = ed.emp_id AND epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND pd.proposal_id = p.id AND apr.proposal_no_lead = p.proposal_no AND ed.lead_id=".$lead_id)->result_array();*/
        //change in query taking time to load - 15/4/2021
//commenting below query taking time - 181021

        /*$query_check_all = $this->db->query("SELECT ed.product_id,apr.gross_premium,apr.end_date,p.premium,p.created_date,mpst.policy_subtype_id,ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote,mpst.plan_code AS master_plan,pd.si_amount,apr.certificate_number AS certificate_number
                                        FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal AS p,payment_details AS pd,api_proposal_response AS apr
                                        WHERE p.emp_id = ed.emp_id AND epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND pd.proposal_id = p.id AND apr.proposal_no_lead = p.proposal_no AND ed.lead_id=".$lead_id)->result_array();*/
 $query_check_all = $this->db->query(" SELECT ed.product_id,apr.gross_premium,apr.end_date,p.premium,p.created_date,mpst.policy_subtype_id,ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote,mpst.plan_code AS master_plan,apr.certificate_number AS certificate_number FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal AS p,api_proposal_response AS apr WHERE p.emp_id = ed.emp_id AND epd.product_name = mpst.id AND p.policy_detail_id = epd.policy_detail_id AND apr.proposal_no_lead = p.proposal_no AND ed.lead_id =".$lead_id)->result_array();


        // $query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id=".$lead_id)->row_array();
        //echo $this->db->last_query();
        // print_pre($query_check_all);exit;
        //end_date from api_proposal response date format is m/d/Y
        $plan_name = [];
        $coi_no = [];
        $premium_amt = [];
        $total_gross_premium = 0;
        $premium_GFB = 0;
        foreach ($query_check_all as $key => $value) {
            if($value['policy_subtype_id'] == 1){
                $pro_name = 'Group Activ Health';
            }else if($value['policy_subtype_id'] == 8){
                $pro_name = 'Group Protect';
            }else if($value['policy_subtype_id'] == 2){
                $pro_name = 'Group Activ Secure';
            }else if($value['policy_subtype_id'] == 3){
                $pro_name = 'Group Activ Secure';
            }
            array_push($coi_no, $value['certificate_number']);
            array_push($plan_name, $pro_name);
            array_push($premium_amt, $value['gross_premium']);
            if(is_numeric($value['gross_premium'])){
                $total_gross_premium += floatval($value['gross_premium']);
            }
            if($value['policy_subtype_id'] != 1){
                $premium_GFB += floatval($value['gross_premium']);
            }
            
        }
        if($query_check_all[0]['product_id'] == 'R10'){
            $premium_GFB = '';
        }
        /*print_pre($coi_no);
        print_pre($plan_name);
        print_pre($premium_amt);
        echo $total_gross_premium;
        exit;*/
        $query_check = $query_check_all[0];
        if($query_check){
            $si_end_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
            //echo $query_check['end_date'].'--'.$si_end_date;exit;
            $json_data = json_decode($query_check['json_qote'],true);
            //$plan_code = explode(',', $query_check['master_plan']);
            
            /*foreach ($plan_code as $key => $value) {
                if($value == '4211'){
                    array_push($plan_name, $query_check['product_name'].' - GHI');
                }else if($value == '4112'){
                    array_push($plan_name, $query_check['product_name'].' - GPA');
                }else if($value == '4216'){
                    array_push($plan_name, $query_check['product_name'].' - GCI');
                }else if($value == '4224'){
                    array_push($plan_name, $query_check['product_name'].' - Group Protect');
                }   
            }*/
            //print_pre($plan_name);exit;
            //$plan_name = implode(',', $plan_name);
            $senderID = 1;
            
            
            $si_renewal_date = '';
            $alertID = '';
            if($si_end_date != ''){
                /*$si_mandate_date_new = str_replace("/", "-", $si_end_date);                
                $si_renewal_date1 = date("d-m-Y", strtotime(date("d-m-Y", strtotime($si_mandate_date_new)) . " + 1 day"));
                $si_renewal_date = str_replace("-", "/", $si_renewal_date1);*/
                $si_renewal_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
            }
            $full_name = trim($query_check['emp_firstname'].' '.$query_check['emp_middlename'].' '.$query_check['emp_lastname']);
            if(strlen($full_name) > 30){
                $full_name = substr($full_name, 0, 30);
            }
            if(strtolower($type) == 'success'){
                
                $data['alertID'] = 'A1604';
                $data['AlertV1'] = $full_name;
                $data['AlertV2'] = floatval($total_gross_premium * 1.5);
                $data['AlertV3'] = $plan_name[0];
                $data['AlertV4'] = (isset($plan_name[1])) ? $plan_name[1] : "";//'Axis bank Ltd';
                $data['AlertV5'] = 'Axis bank Ltd';//$query_check['certificate_number'];
                $data['AlertV6'] = $plan_name[0];
                $data['AlertV7'] = $coi_no[0];
                $data['AlertV8'] = floatval($premium_amt[0] * 1.5);
                $data['AlertV9'] = $si_end_date;
                $data['AlertV10'] = date('d/m/Y');
                $data['AlertV11'] = $si_renewal_date;
                $data['AlertV12'] = (isset($plan_name[1])) ? $plan_name[1] : "";
                $data['AlertV13'] = (isset($coi_no[1])) ? $coi_no[1] : "";
                $data['AlertV14'] = (isset($premium_amt[1])) ? floatval($premium_amt[1] * 1.5) : "";                
                $data['AlertV15'] = (isset($plan_name[1])) ? $si_end_date : "";
                $data['AlertV16'] = (isset($coi_no[1])) ? date('d/m/Y') : "";
                $data['AlertV17'] = (isset($coi_no[1])) ? $si_renewal_date : "";
                $data['AlertV18'] = (isset($plan_name[2])) ? $plan_name[2] : "";
                $data['AlertV19'] = (isset($coi_no[2])) ? $coi_no[2] : "";
                $data['AlertV20'] = (isset($premium_amt[2])) ? floatval($premium_amt[2] * 1.5) : "";                
                $data['AlertV21'] = (isset($plan_name[2])) ? $si_end_date : "";
                $data['AlertV22'] = (isset($coi_no[2])) ? date('d/m/Y') : "";
                $data['AlertV23'] = (isset($coi_no[2])) ? $si_renewal_date : "";
                $data['AlertV24'] = (isset($plan_name[3])) ? $plan_name[3] : "";
                $data['AlertV25'] = (isset($coi_no[3])) ? $coi_no[3] : "";
                $data['AlertV26'] = (isset($premium_amt[3])) ? floatval($premium_amt[3] * 1.5) : "";                
                $data['AlertV27'] = (isset($plan_name[3])) ? $si_end_date : "";
                $data['AlertV28'] = (isset($coi_no[3])) ? date('d/m/Y') : "";
                $data['AlertV29'] = (isset($coi_no[3])) ? $si_renewal_date : "";
                $data['AlertV30'] = (isset($plan_name[4])) ? $plan_name[4] : "";
                $data['AlertV31'] = (isset($coi_no[4])) ? $coi_no[4] : "";
                $data['AlertV32'] = (isset($premium_amt[4])) ? floatval($premium_amt[4] * 1.5) : "";                
                $data['AlertV33'] = (isset($plan_name[4])) ? $si_end_date : "";
                $data['AlertV34'] = (isset($coi_no[4])) ? date('d/m/Y') : "";
                $data['AlertV35'] = (isset($coi_no[4])) ? $si_renewal_date : "";
                $data['AlertV36'] = floatval($premium_amt[0] * 1.5);
                $data['AlertV37'] = (isset($premium_GFB)) ? floatval($premium_GFB * 1.5) : "";         
                $data['AlertV38'] = floatval($total_gross_premium * 1.5);
                $data['AlertV39'] = $lead_id;
            }
            
            if(strtolower($type) == 'fail' || strtolower($type) == 'failure'){
               
                $data['alertID'] = 'A1606';
                $data['AlertV1'] = $full_name;
                $data['AlertV2'] = floatval($total_gross_premium * 1.5);
                $data['AlertV3'] = $plan_name[0];
                $data['AlertV4'] = (isset($plan_name[1])) ? $plan_name[1] : "";
                $data['AlertV5'] = $reject_reason;//'Failure reason';
                $data['AlertV6'] = 'klr.pw/A0Wir';//branch locator link
                $data['AlertV7'] = 'Axis bank Ltd';
                $data['AlertV8'] = $plan_name[0];
                $data['AlertV9'] = $coi_no[0];
                $data['AlertV10'] = floatval($premium_amt[0] * 1.5);
                $data['AlertV11'] = $si_renewal_date;
                $data['AlertV12'] = (isset($plan_name[1])) ? $plan_name[1] : "";
                $data['AlertV13'] = (isset($coi_no[1])) ? $coi_no[1] : "";
                $data['AlertV14'] = (isset($premium_amt[1])) ? floatval($premium_amt[1] * 1.5) : "";     
                $data['AlertV15'] = (isset($coi_no[1])) ? $si_renewal_date : "";
                $data['AlertV16'] = (isset($plan_name[2])) ? $plan_name[2] : "";
                $data['AlertV17'] = (isset($coi_no[2])) ? $coi_no[2] : "";
                $data['AlertV18'] = (isset($premium_amt[2])) ? floatval($premium_amt[2] * 1.5) : "";     
                $data['AlertV19'] = (isset($coi_no[2])) ? $si_renewal_date : "";
                $data['AlertV20'] = (isset($plan_name[3])) ? $plan_name[3] : "";
                $data['AlertV21'] = (isset($coi_no[3])) ? $coi_no[3] : "";
                $data['AlertV22'] = (isset($premium_amt[3])) ? floatval($premium_amt[3] * 1.5) : "";     
                $data['AlertV23'] = (isset($coi_no[3])) ? $si_renewal_date : "";
                $data['AlertV24'] = (isset($plan_name[4])) ? $plan_name[4] : "";
                $data['AlertV25'] = (isset($coi_no[4])) ? $coi_no[4] : "";
                $data['AlertV26'] = (isset($premium_amt[4])) ? floatval($premium_amt[4] * 1.5) : "";     
                $data['AlertV27'] = (isset($coi_no[4])) ? $si_renewal_date : "";
                $data['AlertV28'] = floatval($premium_amt[0] * 1.5);
                $data['AlertV29'] = (isset($premium_GFB)) ? floatval($premium_GFB * 1.5) : "";      
                $data['AlertV30'] = floatval($total_gross_premium * 1.5);
                $data['AlertV31'] = $lead_id;
                $data['AlertV32'] = '';
                $data['AlertV33'] = '';
                $data['AlertV34'] = '';
                $data['AlertV35'] = '';
                $data['AlertV36'] = '';
                $data['AlertV37'] = '';
                $data['AlertV38'] = '';
                $data['AlertV39'] = '';

            }
            //print_pre($data);exit;
           // foreach ($data as $key => $value) {
                //print_pre($value);exit;
                //echo "a-";
                $parameters =[
                "RTdetails" => [
               
                        "PolicyID" => '',
                        "AppNo" => 'HD100017934',
                        "alertID" => $data['alertID'],
                        "channel_ID" => $query_check['product_name'],
                        "Req_Id" => 1,
                        "field1" => '',
                        "field2" => '',
                        "field3" => '',
                        "Alert_Mode" => 3,
                        "Alertdata" => 
                            [
                                "mobileno" => substr(trim($query_check['mob_no']), -10),
                                "emailId" => $query_check['email'],
                                "AlertV1" => $data['AlertV1'],
                                "AlertV2" => $data['AlertV2'],
                                "AlertV3" => $data['AlertV3'],
                                "AlertV4" => $data['AlertV4'],
                                "AlertV5" => $data['AlertV5'],
                                "AlertV6" => $data['AlertV6'],
                                "AlertV7" => $data['AlertV7'],
                                "AlertV8" => $data['AlertV8'],
                                "AlertV9" => $data['AlertV9'],
                                "AlertV10" => $data['AlertV10'],
                                "AlertV11" => $data['AlertV11'],
                                "AlertV12" => $data['AlertV12'],
                                "AlertV13" => $data['AlertV13'],
                                "AlertV14" => $data['AlertV14'],
                                "AlertV15" => $data['AlertV15'],
                                "AlertV16" => $data['AlertV16'],
                                "AlertV17" => $data['AlertV17'],
                                "AlertV18" => $data['AlertV18'],
                                "AlertV19" => $data['AlertV19'],
                                "AlertV20" => $data['AlertV20'],
                                "AlertV21" => $data['AlertV21'],
                                "AlertV22" => $data['AlertV22'],
                                "AlertV23" => $data['AlertV23'],
                                "AlertV24" => $data['AlertV24'],
                                "AlertV25" => $data['AlertV25'],
                                "AlertV26" => $data['AlertV26'],
                                "AlertV27" => $data['AlertV27'],
                                "AlertV28" => $data['AlertV28'],
                                "AlertV29" => $data['AlertV29'],
                                "AlertV30" => $data['AlertV30'],
                                "AlertV31" => $data['AlertV31'],
                                "AlertV32" => $data['AlertV32'],
                                "AlertV33" => $data['AlertV33'],
                                "AlertV34" => $data['AlertV34'],
                                "AlertV35" => $data['AlertV35'],
                                "AlertV36" => $data['AlertV36'],
                                "AlertV37" => $data['AlertV37'],
                                "AlertV38" => $data['AlertV38'],
                                "AlertV39" => $data['AlertV39'],
                            ]

                        ]

                    ];
                     $parameters = json_encode($parameters);
                     //echo $parameters;//exit;
                     $curl = curl_init();
                    
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $query_check['click_pss_url'],
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $parameters,
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",
                       
                      ),
                    ));

                $response = curl_exec($curl);
                
                curl_close($curl);
                
                $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate_".trim($type)];
                
                $dataArray['tablename'] = 'logs_docs'; 
                $dataArray['data'] = $request_arr; 
                $this->Logs_m->insertLogs($dataArray);
            //}
           // exit;
            
    
      }
    }

    function send_communication($type,$lead_id)//send_message($lead_id,$type)
{
        $query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ee.EMandateFailureReason,ee.Registrationmode,ee.MandateLink,sum(p.premium) as total_amt FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,emandate_data as ee WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id = ee.lead_id AND ed.lead_id=".$lead_id)->row_array();
        
        if($query_check){
            
            $senderID = 1;
            $AlertV1 = $query_check['emp_firstname']." ".$query_check['emp_lastname'];
            $AlertV2 = (($query_check['total_amt'] * 1.5) + $query_check['total_amt']);
            $AlertV3 = $query_check['product_name'];
            $AlertV4 = '';
            $AlertV5 = '';
            
            $alertID = '';
            
            if(strtolower($type) == 'success'){
                
                if($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC'){
                    $alertID = 'A1407';
                }
                
                if($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI'){
                    $alertID = 'A1408';
                }
                
            }
            
            if(strtolower($type) == 'fail'){
                
                if($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC'){
                    $alertID = 'A1409';
                }
                
                if($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI'){
                    $alertID = 'A1411';
                }
                
                $AlertV4 = $query_check['EMandateFailureReason'];
                $AlertV5 = 'klr.pw/A0Wir';
            }
            
            if($type == 'SAD_EMI_one'){
                $alertID = 'A1405';
                $AlertV2 = $query_check['product_name'];
                $AlertV3 = $query_check['MandateLink'];
            }
            
            if($type == 'SAD_EMI_two'){
                $alertID = 'A1406';
                $AlertV1 = $query_check['MandateLink'];
            }
                
            
            
            $parameters =[
                "RTdetails" => [
               
                    "PolicyID" => '',
                    "AppNo" => 'HD100017934',
                    "alertID" => $alertID,
                    "channel_ID" => 'Axis Freedom Plus',
                    "Req_Id" => 1,
                    "field1" => '',
                    "field2" => '',
                    "field3" => '',
                    "Alert_Mode" => 2,
                    "Alertdata" => 
                        [
                            "mobileno" => substr(trim($query_check['mob_no']), -10),
                            "emailId" => $query_check['email'],
                            "AlertV1" => $AlertV1,
                            "AlertV2" => $AlertV2,
                            "AlertV3" => $AlertV3,
                            "AlertV4" => $AlertV4,
                            "AlertV5" => $AlertV5,
                        ]

                    ]

                ];
                 $parameters = json_encode($parameters);
                 $curl = curl_init();
                
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $query_check['click_pss_url'],
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $parameters,
                  CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                   
                  ),
                ));

            $response = curl_exec($curl);
            
            curl_close($curl);
            
            $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate".$type];
            
            $dataArray['tablename'] = 'logs_docs'; 
            $dataArray['data'] = $request_arr; 
            $this->Logs_m->insertLogs($dataArray);
    
      }
}

    function send_communication_bk($status,$lead_id) {
        

        $query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.ISNRI,p.created_date,ed.email,ed.mob_no,ed.emp_firstname,mpst.product_code,mpst.click_pss_url,p.EasyPay_PayU_status,mpst.product_name,epd.parent_policy_id FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.status = 'Payment Pending' AND ed.lead_id=".$lead_id)->row_array();
        // print_pre($query_check);exit;
        if($query_check > 0){

            $senderID = 1;

            if($status == "SUCCESS"){
                $name_data = "emandate_success";
                $AlertV1 = $query_check['emp_firstname'];
                $AlertV2 = 'Axis Freedom Plus';
                $AlertV3 = '';
                $AlertV4 = '';
                $AlertV5 = '';
                $alertID = 'A1323';
            }

            if($status == "ERROR"){
                $name_data = "emandate_failure";
                $AlertV1 = $query_check['emp_firstname'];
                $AlertV2 = '';
                $AlertV3 = date('m-d-Y', strtotime($query_check['created_date']. ' + 1 days'));
                $AlertV4 = $query_check['lead_id'];
                $AlertV5 = 'PaymentSupport.HealthInsurance@adityabirlacapital.com';
                $alertID = 'A828';
            }
            /*$AlertV1 = $query_check['emp_firstname'];
            $AlertV2 = $data['txtly'];
            $AlertV3 = date('m-d-Y', strtotime($query_check['created_date']. ' + 1 days'));
            $AlertV4 = $query_check['lead_id'];
            $AlertV5 = 'PaymentSupport.HealthInsurance@adityabirlacapital.com';
            $alertID = 'A828';*/

            $emp_id = $query_check['emp_id'];
            $isNri = $query_check['ISNRI'];
            $product_id = $query_check['product_code'];

            /**
            * Alert Mode 3 : Send OTP in SMS & Email
            * Alert Mode 2 : Send SMS Only
            * Alert Mode 1 : Send Email Only
            *
            **/
            
            // Added By Shardul For Validating ISNRI on 20-Aug-2020
            $dataArray['emp_id'] = $emp_id ;
            $dataArray['isNri'] = $isNri;
            $dataArray['product_id'] = $product_id;
            $alertMode = helper_validate_is_nri($dataArray);
            
            
            $parameters =[
                "RTdetails" => [

                    "PolicyID" => '',
                    "AppNo" => 'HD100017934',
                    "alertID" => $alertID,
                    "channel_ID" => $query_check['product_name'],
                    "Req_Id" => 1,
                    "field1" => '',
                    "field2" => '',
                    "field3" => '',
                    "Alert_Mode" => $alertMode,
                    "Alertdata" => 
                    [
                        "mobileno" => substr(trim($query_check['mob_no']), -10),
                        "emailId" => $query_check['email'],
                        "AlertV1" => $AlertV1,
                        "AlertV2" => $AlertV2,
                        "AlertV3" => $AlertV3,
                        "AlertV4" => $AlertV4,
                        "AlertV5" => $AlertV5,
                        "AlertV6" => '',

                    ]

                ]

            ];
            $parameters = json_encode($parameters);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $query_check['click_pss_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $parameters,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",

                ),
            ));

            $response = curl_exec($curl);
            
            curl_close($curl);
            
            // update employee details          
            
            $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_".$name_data];
            
            $dataArray['tablename'] = 'logs_docs'; 
            $dataArray['data'] = $request_arr; 
            $this->Logs_m->insertLogs($dataArray);
            return true;        
             
        }
    }

    public function insert_emandate_data(){
        $currdate = date('Y-m-d');
        $q = "  SELECT pd.txndate,SUM(p.premium) AS premium,e.mob_no,e.emp_city,e.emp_state,e.emp_pincode,e.address,GROUP_CONCAT(p.policy_detail_id) AS policy_detail_id,e.lead_id,e.product_id,e.emp_firstname,e.emp_middlename,e.emp_lastname,pd.account_no,pd.si_amount,pd.payment_id,e.emp_id,e.is_otp_verified FROM payment_details AS pd
                LEFT JOIN proposal AS p
                ON p.id = pd.proposal_id 
                LEFT JOIN employee_details AS e
                ON e.emp_id = p.emp_id 
                WHERE  e.is_otp_verified = 1 AND pd.si_auto_renewal = 'Y' AND pd.payment_mode = 'Easy Pay' AND txndate = '".$currdate."' GROUP BY e.lead_id";
        // echo $q;exit;  
        $res = $this->db->query($q)->result_array();
        if(!empty($res)){
            foreach ($res as $key => $value) {
                if($value['product_id'] == 'R04' || $value['product_id'] == 'R10'){
                    $qry = "SELECT GROUP_CONCAT(pms.master_policy_no) AS master_policy_no FROM product_master_with_subtype as pms, employee_policy_detail as epd where epd.product_name = pms.id AND epd.policy_detail_id IN (".$value['policy_detail_id'].")";
                }else{
                    $qry = "SELECT GROUP_CONCAT(pms.EW_master_policy_no) AS master_policy_no FROM product_master_with_subtype as pms, employee_policy_detail as epd where epd.product_name = pms.id AND epd.policy_detail_id IN (".$value['policy_detail_id'].")";
                }
                //echo $qry;exit;
                $res1 = $this->db->query($qry)->row_array();
                if(!empty($res1)){
                    $res[$key]['master_policy_no'] = $res1['master_policy_no'];
                }
            }
            //print_pre($res);exit;
            $insertArr = [];
            foreach($res as $val){          
                $start_date = date("d/m/Y",strtotime($val['txndate']));
                $txn_date_year = date("Y",strtotime($val['txndate']));
                $end_date = str_replace($txn_date_year, "2099", $start_date);
                //check if exist
                $is_exist = $this->db->get_where("emandate_data",array("lead_id" => $val['lead_id'], "product_id" => $val['product_id']))->row_array();
                if(empty($is_exist)){
                    $insertArr = array(
                        "company_uid" => $val['master_policy_no'],
                        "product_id" => $val['product_id'],
                        "lead_id" => $val['lead_id'],
                        "investor_first_name" => trim($val['emp_firstname']. ' '.$val['emp_middlename']. ' '.$val['emp_lastname'] ),    
                        "address1" => $val['address'],
                        "address2" => "NA" ,
                        "address3" => "NA",
                        "address4" => "NA",
                        "city" => ($val['emp_city'] != "") ? $val['emp_city'] : "NA",
                        "state" => ($val['emp_state'] != "") ? $val['emp_state'] : "NA",
                        "pincode" => ($val['emp_pincode'] != "") ? $val['emp_pincode'] : "NA",
                        "country" => "NA",
                        "home_phone" => "NA",
                        "office_phone" => "NA",
                        "mobile_no" => $val['mob_no'],
                        "financle_cust_id" => "NA" ,
                        "debit_account_no" => $val['account_no'] ,
                        "min_amount" => $val['premium'] ,
                        "max_amount" =>  (int)$val['premium'] * 1.5,
                        "no_of_tran" => "99999",
                        "maxium_transaction_reset" => "A" ,
                        "sid_start_date" => $start_date,
                        "sid_end_date" => $end_date
                        );
                    if(!empty($insertArr)){
                        $this->db->insert("emandate_data",$insertArr);
                    }
                }
                
            }
            return $return = [
                'status'=>200,
                'message'=>'Data inserted successfully !'
            ];
        }else{
            return $return = [
                'status'=>400,
                'message'=>'Data not found for currentdate'
            ];
        }
        
    }

    public function uploadRegDoc(){
        $sftp_path = "/COI/";
        //insert todays emandate data in table
        /*$insertData = $this->insert_emandate_data();
        if($insertData['status'] == 400){
            echo json_encode($insertData);exit;
        }*/
        $date_new = date("d/m/Y");
        $txndate = date("d/m/Y");
        if (preg_match('#^0#', $txndate) === 1) {
          $date_new = ltrim($txndate, '0'); 
        }
        //$q = "SELECT reject_reason,company_code,'' as cid,product_id,lead_id,company_uid as master_policy,investor_first_name,investor_middle_name,investor_last_name,address1,address2,address3,address4,city,pincode,state,country,home_phone,office_phone,mobile_no,financle_cust_id,debit_account_no,min_amount,max_amount,no_of_tran,maxium_transaction_reset,sid_start_date,sid_end_date,remarks1,remarks2 FROM emandate_data WHERE (si_mandate_date = '".$txndate."' OR si_mandate_date = '".$date_new."') AND status_desc = 'Registered' AND status = 'Emandate Received' ";
        //$q = "SELECT * FROM emandate_data WHERE (si_mandate_date = '".$txndate."' OR si_mandate_date = '".$date_new."') AND status_desc = 'Registered' AND status = 'Emandate Received' ";
        //query changed on 15/2/21
        $q = "SELECT ed.json_qote,emd.reject_reason,emd.company_code,emd.product_id,emd.lead_id,emd.company_uid,emd.investor_first_name,emd.investor_middle_name,emd.investor_last_name,emd.address1,emd.address2,emd.address3,emd.address4,emd.city,emd.pincode,emd.state,emd.country,emd.home_phone,emd.office_phone,emd.mobile_no,emd.financle_cust_id,emd.debit_account_no,emd.min_amount,emd.max_amount,emd.no_of_tran,emd.maxium_transaction_reset,emd.sid_start_date,emd.sid_end_date,emd.remarks1,emd.remarks2,emd.address1,emd.lead_id,pd.payment_mode
                FROM emandate_data AS emd, employee_details AS ed, proposal AS p, payment_details AS pd
                WHERE emd.lead_id = ed.lead_id AND p.emp_id = ed.emp_id AND p.id = pd.proposal_id AND pd.payment_mode = 'Easy Pay' 
                AND pd.si_auto_renewal = 'Y' AND (si_mandate_date = '".$txndate."' OR si_mandate_date = '".$date_new."') AND emd.status_desc = 'Registered' 
                AND emd.status = 'Emandate Received'
                GROUP BY emd.lead_id";
        $data = $this->db->query($q)->result_array();
        /*echo $this->db->last_query();
        print_pre($data);exit;*/
        $i = 0;
        $arraydata = [];
        foreach ($data as $key => $val) {
            //Country filed check in json if found 
            $data_json = json_decode($val['json_qote']);           
            $val['country'] = $data_json->COUNTRY;
          
            $qry = "SELECT ed.lead_id,apr.certificate_number,p.status
                    FROM employee_details AS ed,proposal AS p,api_proposal_response AS apr
                    WHERE ed.emp_id = p.emp_id AND p.proposal_no = apr.proposal_no_lead AND ed.lead_id ='".$val['lead_id']."'";
            $proData = $this->db->query($qry)->result_array();
            // echo $this->db->last_query();
            // print_pre($proData);exit;
            if(empty($proData)){
                continue;
            }
            $carr = array_column($proData, 'certificate_number');
            //print_pre($proData);
            $succArr = [];
            foreach ($proData as $key => $proVal) {
               if($proVal['status'] == 'Success'){
                    array_push($succArr, $proVal['status']);
               }
            }

            if($val['product_id'] == 'R10'){
                $val['company_uid'] = implode(',',array_unique(explode(',', $val['company_uid'])));
            }

            //print_pre($succArr);//exit;
            //echo count($succArr)." ==". count($proData);
            if(count($succArr) == count($proData)){
                $arraydata[$i]['reject_reason'] = $val['reject_reason'];
                $arraydata[$i]['company_code'] = ($val['company_code'] == 'ABHI') ? 'ABHICL1' : $val['company_code'];
                $arraydata[$i]['cid'] = implode(',', $carr);
                $arraydata[$i]['product_id'] = $val['product_id'];
                $arraydata[$i]['lead_id'] = $val['lead_id'];
                $arraydata[$i]['company_uid'] = $val['company_uid'];
                $arraydata[$i]['investor_first_name'] = $val['investor_first_name'];
                $arraydata[$i]['investor_middle_name'] = $val['investor_middle_name'];
                $arraydata[$i]['investor_last_name'] = $val['investor_last_name'];
                $arraydata[$i]['address1'] = $val['address1'];
                $arraydata[$i]['address2'] = $val['address2'];
                $arraydata[$i]['address3'] = $val['address3'];
                $arraydata[$i]['address4'] = $val['address4'];
                $arraydata[$i]['city'] = $val['city'];
                $arraydata[$i]['pincode'] = $val['pincode'];
                $arraydata[$i]['state'] = $val['state'];
                $arraydata[$i]['country'] = $val['country'];
                $arraydata[$i]['home_phone'] = $val['home_phone'];
                $arraydata[$i]['office_phone'] = $val['office_phone'];
                $arraydata[$i]['mobile_no'] = $val['mobile_no'];
                $arraydata[$i]['financle_cust_id'] = $val['financle_cust_id'];
                $arraydata[$i]['debit_account_no'] = $val['debit_account_no'];
                $arraydata[$i]['min_amount'] = $val['min_amount'];
                $arraydata[$i]['max_amount'] = $val['max_amount'];
                $arraydata[$i]['no_of_tran'] = $val['no_of_tran'];
                $arraydata[$i]['maxium_transaction_reset'] = $val['maxium_transaction_reset'];
                $arraydata[$i]['sid_start_date'] = $val['sid_start_date'];
                $arraydata[$i]['sid_end_date'] = $val['sid_end_date'];
                $arraydata[$i]['remarks1'] = $val['remarks1'];
                $arraydata[$i]['remarks2'] = $val['remarks2'];
                $i++;
            }
        }
        //exit;
         //print_pre($arraydata);exit;
        /*$this->db->select('reject_reason,company_code,company_uid,product_id,lead_id,investor_first_name,investor_middle_name,investor_last_name,address1,address2,address3,address4,city,pincode,state,country,home_phone,office_phone,mobile_no,financle_cust_id,debit_account_no,min_amount,max_amount,no_of_tran,maxium_transaction_reset,sid_start_date,sid_end_date,remarks1,remarks2,mandate_date,mandate_sent_date,batch_lot_no,mandate_image_name,account_no,micr');
        $arraydata = $this->db->get("si_emandate_data"),array("sid_start_date" => $txndate))->result_array(); */ 
        //echo $this->db->last_query();
        //print_pre($arraydata);exit;
        if(empty($arraydata)){
            $return = [
                'status'=>400,
                'message'=>'Data not found for currentdate'
            ];
            echo json_encode($return);exit;
        }
        $upld_reg_file_name = date("dmY")."_REG_UPLD_GRP.xls"; 
        //echo APPPATH;exit;
        //include_once(APPPATH   . 'third_party/PHPExcel.php');
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        //print_r($objPHPExcel);exit;
        $objPHPExcel->getProperties()->setCreator("FYNTUNE");
        $objPHPExcel->setActiveSheetIndex(0);
        $header = array('REJECT REASON','COMPANY CODE','COMPANY_UID','PRODUCT_ID','LEAD_ID','Master Policy','INVESTOR FIRST NAME','INVESTOR MIDDLE NAME','INVESTOR LAST NAME','ADDRESS1 ','ADDRESS2 ','ADDRESS3 ','ADDRESS4','CITY','PIN CODE','STATE','COUNTRY','HOME_PHONE','OFFICE_PHONE','MOBILE_NO','FINACLE_CUST_ID','DEBIT ACCOUNT NO','MIN AMOUNT','MAX AMOUNT','No_OF_TRAN','Maximum Transaction Reset(Period)','SID START DATE','SID END DATE','Remarks1','Remarks2');
        $main_header = array('ADDNL INFO1','CREDITOR ID','CUST REF NO','','','','CUSTOMER NAME','ADDN INFO 2','ADDN INFO 3','ADDN INFO 4','ADDN INFO 5','ADDN INFO 6','ADDN INFO 7','ADDN INFO 8','ADDN INFO 9','ADDN INFO 10','ADDN INFO 11','ADDN INFO 12','ADDN INFO 13','ADDN INFO 14','ADDN INFO 15','CUST ACCNT NO','ADDN INFO 16','CEILING AMT','ADDN INFO 17','FREQUENCY','SCH DATE','EXPIRY DATE','ADDN INFO 18','ADDN INFO 19','Mandate date','Mandate sent date','Batch/Lot no.','Mandate image name','Account type','MICR/IFSC');
        if (!empty($main_header)) {
            $cell_name = 'A';
            foreach ($main_header as $headerName) {
                //echo $headerName;exit;
                $prev_cell_name = $cell_name;
                $objPHPExcel->getActiveSheet()->SetCellValue($cell_name . '1', $headerName);
                $cell_name++;
            }
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $prev_cell_name . '1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $prev_cell_name . '1')->getFont()->setBold(true);
        }
        if (!empty($header)) {
            $cell_name = 'A';
            foreach ($header as $headerName) {
                //echo $headerName;exit;
                $prev_cell_name = $cell_name;
                $objPHPExcel->getActiveSheet()->SetCellValue($cell_name . '2', $headerName);
                $cell_name++;
            }
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $prev_cell_name . '2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $prev_cell_name . '2')->getFont()->setBold(true);
        }
        $rowNo = 2;
        foreach ($arraydata as $data) {
            $cell_name = 'A';
            $rowNo++;
            foreach ($data as $key => $value) {
                //echo $cell_name. $rowNo.'----'. $value.'<br>';
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell_name . $rowNo, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                $cell_name++;
            }
        }//exit;
        ob_get_clean();

        if (!is_dir(APPPATH."resources/SI_PROCESS/")) {
            mkdir(APPPATH."resources/SI_PROCESS/");
        }
            
        $objPHPExcel->setActiveSheetIndex(0);       
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace(__FILE__,APPPATH."resources/SI_PROCESS/".$upld_reg_file_name,__FILE__));
         
        $connectServer = $this->connectServer();
        if($connectServer['status'] == 400){

            echo json_encode($connectServer);exit;
            // return $connectServer;
        }
        $connection = $connectServer['connection'];
        // echo APPPATH."resources/SI_PROCESS/".$upld_reg_file_name."<--------->".$sftp_path.$upld_reg_file_name;exit;
        $uploadFile = $this->uploadFile($connection,APPPATH."resources/SI_PROCESS/".$upld_reg_file_name,$sftp_path.$upld_reg_file_name);
//print_pre($uploadFile);exit;
        // return $uploadFile;
        echo json_encode($uploadFile);exit;

        
    }

    public function uploadTxnDoc(){
        $sftp_path = "/COI/";
        $t1_date = date('d/m/Y', strtotime(date('Y-m-d'). ' +1 day'));   
        $date_new = date("d/m/Y");     
        if (preg_match('#^0#', $t1_date) === 1) {
          $date_new = ltrim($t1_date, '0'); 
        }
        $q = "SELECT 'registration_acceptane_no,company_code,company_uid,product_id,lead_id,company_uid as master_policy,'' as add_info1,sid_end_date,min_amount,'Aditya Birla Health Insurance' as rm,'' as filerefno,account_no FROM emandate_data WHERE (sid_end_date = '".$t1_date."' OR sid_end_date = '".$date_new."') ";
        $arraydata = $this->db->query($q)->result_array();
        /*$this->db->select('registration_acceptane_no,company_code,company_uid,product_id,lead_id,company_uid as master_policy,"" as add_info1,sid_end_date,min_amount,"Aditya Birla Health Insurance" as rm,"" as filerefno,account_no');
        $arraydata = $this->db->get_where("si_emandate_data", array('sid_end_date' => $date_new))->result_array();  */
        // echo $this->db->last_query();exit;
        // print_pre($arraydata);exit;
        $upld_reg_file_name = date("dmY")."_TXN_UPLD_GRP.xls"; 
        //echo APPPATH;exit;
        //include_once(APPPATH   . 'third_party/PHPExcel.php');
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        //print_r($objPHPExcel);exit;
        $objPHPExcel->getProperties()->setCreator("FYNTUNE");
        $objPHPExcel->setActiveSheetIndex(0);
        $header = array('UNDERLYING REF NO','USER NUMBER','UOMN','PRODUCT_ID','LEAD_ID','MASTER_POLICY','ADDN INFO 1','DATE','AMT','ADDRESS2 ','ADDN INFO 2 ','FILE REF NUMBER','Customer account no.');

        if (!empty($header)) {
            $cell_name = 'A';
            foreach ($header as $headerName) {
                //echo $headerName;exit;
                $prev_cell_name = $cell_name;
                $objPHPExcel->getActiveSheet()->SetCellValue($cell_name . '1', $headerName);
                $cell_name++;
            }
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $prev_cell_name . '1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $prev_cell_name . '1')->getFont()->setBold(true);
        }
        $rowNo = 1;
        foreach ($arraydata as $data) {
            $cell_name = 'A';
            $rowNo++;
            foreach ($data as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell_name . $rowNo, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                $cell_name++;
            }
        }
        ob_get_clean();
        if (!is_dir(APPPATH."resources/SI_PROCESS/")) {
            mkdir(APPPATH."resources/SI_PROCESS/");
        }
            
        $objPHPExcel->setActiveSheetIndex(0);       
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace(__FILE__,APPPATH."resources/SI_PROCESS/".$upld_reg_file_name,__FILE__));
        $connectServer = $this->connectServer();
        // print_pre($connectServer);exit;
        if($connectServer['status'] == 400){

            echo json_encode($connectServer);exit;
        }
        $connection = $connectServer['connection'];
        // echo APPPATH."resources/SI_PROCESS/".$upld_reg_file_name."<--------->".$sftp_path.$upld_reg_file_name;exit;
        $uploadFile = $this->uploadFile($connection,APPPATH."resources/SI_PROCESS/".$upld_reg_file_name,$sftp_path.$upld_reg_file_name);

        echo json_encode($uploadFile);exit;
        
    }

    public function downloadTxnDoc(){       
        $dwnld_reg_file_name = date("dmY")."_TXN_DWNLD_GRP.xls";
        $csv_path = APPPATH."resources/SI_PROCESS_UPLD/".date("dmY")."_TXN_DWNLD_GRP.csv";
        $excel_path = APPPATH."resources/SI_PROCESS_UPLD/".date("dmY")."_TXN_DWNLD_GRP.xls";
        $remote_path = "/COI/".date("dmY")."_TXN_DWNLD_GRP.xls";
        // echo $remote_path;exit;
        if (!is_dir(APPPATH."resources/SI_PROCESS_UPLD/")) {
            mkdir(APPPATH."resources/SI_PROCESS_UPLD/");
        }
        //copy file from remote server
        $connectServer = $this->connectServer();
        if($connectServer['status'] == 400){
            echo json_encode($connectServer);exit;//return $connectServer;
        }
        $connection = $connectServer['connection'];
        $copyFile = $this->copyFile($connection,$remote_path,$excel_path);
        //$copyFile = $this->copyFile($connection,$remote_path,$excel_path);//$this->copyFile($connection,$remote_path,$excel_path);
        
        if($copyFile['status'] == 400){

            echo json_encode($copyFile);exit;//return $copyFile;
        }
        if(file_exists($excel_path)){
            $excelToCsv = $this->excelToCsv($excel_path,$csv_path);
            if($excelToCsv['status'] == 400){
                echo json_encode($excelToCsv);exit;//return $excelToCsv;
            }
            $csvToArray = $this->csvToArray($csv_path); 
            if($csvToArray['status'] == 400){
                echo json_encode($csvToArray);exit;//return $csvToArray;
            }

            $data = $csvToArray['data'];
            // print_pre($data);exit;
            foreach($data as $key => $val){
                $arr = ["invoice_no" => $val['invoice_no'],"bill_debit_date" => $val['bill_debit_date'],"customer_uid" => $val['customer_uid'], "debit_bill_amount" => $val['debit_bill_amount'], "txn_status" => $val['status'], "txn_remark" => $val['remarks'], "file_ref_no" => $val['file_ref_number']];
                $this->db->where("lead_id",$val['lead_id']);
                $this->db->update("emandate_data",$arr);
                //send communication to user
                //$this->send_message($val['status'],$val['lead_id']);
                
            }
            $return = [
                'status'=>200,
                'message'=>'Data Updated successfully'
            ];
            echo json_encode($return);exit;//
        }else{
            $return = [
                'status'=>400,
                'message'=>'File Not found on local server'
            ];
            echo json_encode($return);exit;
        }
    }
   
    public function copyFromGranite() {

        //ini_set('display_errors',1);
        //error_reporting(E_ALL);

        $copyFromServer =  $this->copyFromServer();

        echo"<pre>";print_r($copyFromServer);exit();
    }

    public function copyFromServer(){
        
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 0);

        $granite_path = $this->granite_path;

        $processed_path = $this->processed_path;
        
        $unprocessed_path = $this->unprocessed_path;

        $excel_path = $this->excel_path;

        $csv_path = $this->csv_path;

        $return = [
            'status'=>200,
            'message'=>'Success File copied from server and converted to csv'
        ];

        $connectServer = $this->connectServer();

        if($connectServer['status'] == 400){

            return $connectServer;
        }

        $connection = $connectServer['connection'];

        $copyFile = $this->copyFile($connection,$granite_path,$excel_path);

        if($copyFile['status'] == 400){

            return $copyFile;
        }

        $excelToCsv = $this->excelToCsv($excel_path,$csv_path);

        if($excelToCsv['status'] == 400){

            return $excelToCsv;

        }   

        return $return;
    }

    public function finalDump(){

        //ini_set('display_errors',1);
        //error_reporting(E_ALL);
        
        $csvFinalDump =  $this->csvFinalDump();
        // send success email
        $this->sendSuccessMail();

        echo"<pre>";print_r($csvFinalDump);exit();
    }

    public function csvFinalDump(){

        $granite_path = $this->granite_path;

        $processed_path = $this->processed_path;
        
        $unprocessed_path = $this->unprocessed_path;

        $excel_path = $this->excel_path;

        $csv_path = $this->csv_path;

        $return = [
            'status'=>200,
            'message'=>'Success data dump'
        ];

        $connectServer = $this->connectServer();


        if($connectServer['status'] == 400){

            return $connectServer;
        }

        $connection = $connectServer['connection'];

        $csvToArray = $this->csvToArray($csv_path);

        if($csvToArray['status'] == 400){
                
            $sftp = ssh2_sftp($connection);

            ssh2_sftp_rename($sftp,$granite_path,$unprocessed_path);

            return $csvToArray;
        }

        $data = $csvToArray['data'];

        $deleteData = $this->deleteData();

        if($deleteData['status'] == 400){

            return $deleteData;
        }

        $data = array_chunk($data,5000);

        $l2Dump = new L2Dump();

        foreach ($data as $key => $value) {

            echo $key;

            $bulkInsert = $this->bulkInsert($l2Dump,$value);

            /*if ($bulkInsert['status'] == 400) {

                $string = substr($bulkInsert['message'],0,2000);

                echo"<pre>";print_r($string);exit();
            }*/
        }

        $sftp = ssh2_sftp($connection);

        ssh2_sftp_rename($sftp,$granite_path,$processed_path);

        return $return;
    }

    public function connectServer(){

        $return = [
            'status'=>200,
            'message'=>'connectServer Success'
        ];
        $connection =  ssh2_connect(self::FTPSERVER, 22);

        if ($connection){
            if(!ssh2_auth_password($connection, self::FTPUSERNAME, self::FTPPASSWORD)){

                $return = [
                    'status'=>400,
                    'message'=>'Authentication failed'
                ];

                return $return;

            }

        } else {

            $return = [
                'status'=>400,
                'message'=>'NOT Connected to server'
            ];

            return $return;
        }

        $return['connection'] = $connection;

        return $return;

    }

    public function uploadFile($connection,$local_path,$remote_path){

        $return = [
            'status'=>200,
            'message'=>'Success file upload'
        ];



        // Create SFTP session
        $sftp = ssh2_sftp($connection);

        $sftpStream = fopen('ssh2.sftp://'.intval($sftp).$remote_path, 'w');
        

        try {

            if (!$sftpStream) {
                throw new Exception("Could not open remote file: $remote_path");
            }
           
            $data_to_send = @file_get_contents($local_path);
           
            if ($data_to_send === false) {
                throw new Exception("Could not open local file: $local_path.");
            }
           
            if (@fwrite($sftpStream, $data_to_send) === false) {
                throw new Exception("Could not send data from file: $local_path.");
            }
           
            fclose($sftpStream);
                           
        } catch (Exception $e) {
            $return = [
                'status'=>400,
                'message'=>$e->getMessage()
            ];
            error_log('Exception: ' . $e->getMessage());
            fclose($sftpStream);
        }     

        return $return;
    }

    public function copyFile($connection,$remote_path,$excel_path){

        $return = [
            'status'=>200,
            'message'=>'Success file download'
        ];
        // Create SFTP session
        $sftp = ssh2_sftp($connection);

        $sftpStream = file_get_contents('ssh2.sftp://'.intval($sftp).$remote_path);
        

        try {

            if (!$sftpStream) {
                throw new Exception("Could not open remote file: $remote_path");

            }
           
            file_put_contents($excel_path, $sftpStream);
                           
        } catch (Exception $e) {
            error_log('Exception: ' . $e->getMessage());
            //fclose($sftpStream);
            $return = [
                'status'=> 400,
                'message'=> $e->getMessage()
            ];
        }  
       // chmod($excel_path,0777);

        return $return;
    }
    
    public function excelTocsv($excel_path,$csv_path){

        $return = [
            'status'=>200,
            'message'=>'Success excelTocsv',
        ];

        try {

            
            include_once(APPPATH   . 'third_party/PHPExcel.php');

            $inputFileName = $excel_path;

            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcelReader = $objReader->load($inputFileName);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelReader, 'CSV');
            $objWriter->save($csv_path);

            chmod($csv_path,0777);
            
        } catch (Exception $e) {

            $return = [
                'status'=>400,
                'message'=>'fail excelTocsv'
            ];
            
        }

        return $return;
    }


    public function csvToArray($filename='', $delimiter=','){

        $header = NULL;

        $data = [];

        $return = [
            'status'=>200,
            'message'=>'Success csvToArray',
            'data'=>$data
        ];

        if(!file_exists($filename) || !is_readable($filename)){

            $return = [
                'status'=>400,
                'message'=>'file_exists or is_readable issue',
                'data'=>$data
            ];

            return $return;
        }

        if (($handle = fopen($filename, 'r')) !== FALSE){
            $i = 1;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header){
                    if($i == 2){
                        $header = array_map('strtolower',array_map('trim',$row));
                        $i = 1;
                        foreach ($header as $key => $value) {
                            if($value == ''){
                                $header[$key] = $i;
                                $i++;
                            }
                        }
                        //print_pre($header);exit;
                    }                   

                }else{

                    $final = [];

                    try {
                        if(!empty($row)){
                            $final = array_combine($header,$row);
                        }                       

                    }catch (Exception $e) {

                        continue;
                    }

                    $final['created_at'] = date('Y-m-d H:i:s');
                    $final['updated_at'] = date('Y-m-d H:i:s');

                    $data[] = $final;
                }
                $i++;
            }

            fclose($handle);

            $return['data'] = $data; 
        }
        //echo $count;
        //print_pre($data);exit;
        if(empty($data)){

            $return = [
                'status'=>400,
                'message'=>'No data in csv',
                'data'=>$data
            ];
        }

        return $return;
    }


    public function sendEmail($text){

        
    }

   
}
