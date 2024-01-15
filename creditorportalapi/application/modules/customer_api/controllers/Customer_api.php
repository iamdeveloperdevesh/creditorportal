<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//define('FCPATH', rtrim(str_replace('\\', '/', realpath('/var/www/html/benefitz.in/fyntune-creditor-portal/')), '/').'/');

require_once PATH_VENDOR.'vendor/autoload.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/SMTP.php';

// session_start(); //we need to call PHP's session object to access it through CI
class Customer_api extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('customerapimodel', '', TRUE);
        // Load these helper to create JWT tokens
        $this->load->helper(['core_helper', 'jwt', 'authorization_helper']);
        $this->load->model('api2/apimodel', '', TRUE);
        //$this->load->helper(['jwt', 'authorization']);

        ini_set('memory_limit', '25M');
        ini_set('upload_max_filesize', '25M');
        ini_set('post_max_size', '25M');
        ini_set('max_input_time', 3600);
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '-1');
        allowCrossOrgin();
      /*  ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

    }

    public function proposal_no_checker()
    {

        $proposal_no = 'P-' . hexdec(uniqid());

        $proposal_id = $this->db->select('*')
            ->from('proposal_policy')
            ->where('proposal_no', $proposal_no)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {

            $this->proposal_no_checker();
        }
        return $proposal_no;
    }
    public function secure_random_string($length) {
        $random_string = '';
        for($i = 0; $i < $length; $i++) {
            $number = random_int(0, 36);
            $character = base_convert($number, 10, 36);
            $random_string .= $character;
        }
        return $random_string;
    }
    public function getMember_insure_details_ic($lead_id)
    {


        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'");
        // print_r($this->db->last_query());exit;
        if (!empty($check_data)) {
            //fetch selected plans
            $planDetails = $this->db->query("SELECT * FROM policy_member_plan_details where lead_id = '$lead_id'")->result_array();
            /* var_dump($planDetails);
             exit;*/

            foreach ($planDetails as $val) {

                $members_data = $this->db->query("SELECT *
                    FROM proposal_policy_member_details AS pd
                    LEFT JOIN proposal_policy_member pm ON pd.member_id = pm.member_id
                    LEFT JOIN master_policy AS mp ON pm.policy_id = mp.policy_id 
                    LEFT JOIN master_policy_sub_type AS mpst ON mpst.policy_sub_type_id = mp.policy_sub_type_id
                    LEFT JOIN family_construct AS fc ON pd.relation_with_proposal = fc.id
                    WHERE pm.policy_id = '" . $val['policy_id'] . "' AND pm.lead_id = '$lead_id'")->result_array();
                //echo $this->db->last_query();exit;
                //  var_dump($members_data);exit;
                foreach ($members_data as $key => $val_p) {

                    $arr[$val_p['code']][$key]['member_type'] = $val_p['member_type'];
                    $arr[$val_p['code']][$key]['policy_member_first_name'] = $val_p['policy_member_first_name'];
                    $arr[$val_p['code']][$key]['policy_member_last_name'] = $val_p['policy_member_last_name'];
                    $arr[$val_p['code']][$key]['policy_member_dob'] = $val_p['policy_member_dob'];
                    $arr[$val_p['code']][$key]['policy_member_gender'] = $val_p['policy_member_gender'];
                    $arr[$val_p['code']][$key]['policy_member_salutation'] = $val_p['policy_member_salutation'];
                    $arr[$val_p['code']][$key]['policy_member_relation_code'] = $val_p['relation_code'];
                    $arr[$val_p['code']][$key]['policy_member_marital_status'] = $val_p['policy_member_marital_status'];
                    $arr[$val_p['code']][$key]['member_id'] = $val_p['id'];

                    $arr[$val_p['code']][$key]['cover'] = $val['cover'];
                    $arr[$val_p['code']][$key]['premium'] = $val['premium'];
                    //array_push($data,$arr);
                }
            }
//var_dump($arr);exit;

            return $arr;

        }
    }

public function generate_policy_ic_api_call()
{    $plan_id = $this->input->post('plan_id');
    $lead_id = $this->input->post('lead_id');


    $coi_type_det = $this->db->query("select coi_type,ic_api,plan_id,coi_download,creditor_id,is_RelationBased,route_id,payment_page from master_plan where plan_id=" . $plan_id)->row_array();
//print_r($coi_type_det);die;
    $master_customer = $this->db->query("select * from master_customer where lead_id=" . $lead_id)->row_array();
    $quote_data = $this->db->query("select * from master_quotes where lead_id=" . $lead_id)->row_array();
  

    $data['coi_type'] = $coi_type_det['coi_type'];
    $data['ic_api'] = $coi_type_det['ic_api'];
    $data['coi_download'] = $coi_type_det['coi_download'];
    $coi_type = $coi_type_det['coi_type'];
    $plan_id = $coi_type_det['plan_id'];
    $creditor_id = $coi_type_det['creditor_id'];
    $req_data1['lead_id'] = $lead_id;
    $creaditor_name = $this->db->query("select mc.creaditor_name from  master_ceditors as mc where mc.creditor_id = '$creditor_id'")->row_array();

    $payment_details = $this->db->query("select * from proposal_payment_details as ppd join payment_modes as pm where ppd.payment_mode = pm.payment_mode_id and ppd.lead_id= '$lead_id' and pm.isactive =1")->row_array();
    if ($payment_details['payment_mode'] == 1) {
        $collection_mode = "online Collection";
    }


            $proposal_data = $this->db->query("select pp.*,pd.nominee_relation,nominee_first_name,nominee_last_name,pd.customer_id from proposal_policy as pp join proposal_details as pd where pp.proposal_details_id = pd.proposal_details_id and pd.plan_id = '$plan_id' and pp.lead_id = '$lead_id'")->result_array();
          
       //   print_r($proposal_data);die;
            foreach ($proposal_data as $proposal_value) {

if($proposal_value['status'] !='Success'){
                $group_code = $proposal_value['group_code'];
                $adult_count = $proposal_value['adult_count'];

                $child_count = $proposal_value['child_count'];
                $sum_Insured = $proposal_value['sum_insured'];
                $master_policy = $this->db->query("select policy_number,scheme_code,payer_code,Sourcing_br_Sol_ID,payer_relation,product_code from master_policy where policy_id = " . $proposal_value['master_policy_id'] . " and isactive = 1")->row_array();
                $master_policy_premium = $this->db->query("select si_type from master_policy_premium where master_policy_id = " . $proposal_value['master_policy_id'] . " and group_code = '$group_code' and  sum_insured = '$sum_Insured' and adult_count = '$adult_count' and child_count = '$child_count' and isactive = 1")->row_array();
                if (empty($master_policy_premium)) {
                    $master_policy_premium = $this->db->query("select si_type from master_policy_premium where master_policy_id = " . $proposal_value['master_policy_id'] . " and group_code = '$group_code' and  sum_insured = '$sum_Insured' and isactive = 1")->row_array();

                }


                $policy_subtype_det = $this->db->query("select * from master_policy_sub_type where policy_sub_type_id = " . $proposal_value['policy_sub_type_id'])->row_array();
                $BA_number = $this->secure_random_string(10);
                //   $customer_data['lead_id'] = $lead_id;
                $fam_const[0] = $proposal_value['adult_count'];
                $policy_subtype_code = $policy_subtype_det['code'];
                $checkMembers = $this->getMember_insure_details_ic($lead_id);
                $checkMember = $checkMembers[$policy_subtype_code];
                $totalMembers = count($checkMember);
                //echo"<pre>";
                //  print_r($checkMember);


                for ($i = 0; $i < $totalMembers; $i++) {
                    $mem_first_name = $checkMember[$i]['policy_member_first_name'];
                    $mem_first_name = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $mem_first_name)));

                    $mem_first_name = explode(" ", $mem_first_name, 2);
                    if (empty($checkMember[$i]['policy_member_last_name'])) {
                        $checkMember[$i]['policy_member_first_name'] = $mem_first_name[0];
                        $checkMember[$i]['policy_member_last_name'] = $mem_first_name[1];


                    }
                    $member_id = $checkMember[$i]['member_id'];
                    if ($coi_type_det['is_RelationBased'] == 1) {//echo 123;die;
                        $proposal_value['member_code'] = $this->db->query("select member_code from plan_base_member_code where plan_id = " . $plan_id . " and relation_id = '$member_id'  and isactive = 1")->row()->member_code;

                    }

                    $member[] = ["Member_No" => $i + 1, "Member_Salutation" =>($checkMember[$i]['policy_member_salutation']=='Master')?'Mr':$checkMember[$i]['policy_member_salutation'], "Member_First_Nm" => $checkMember[$i]['policy_member_first_name'], "Member_Last_Nm" => $checkMember[$i]['policy_member_last_name'], "Member_Gender" => ($checkMember[$i]['policy_member_gender'] == "Male") ? "M" : "F", "Member_DOB" => $checkMember[$i]['policy_member_dob'], "Member_Relation_Cd" => $checkMember[$i]['policy_member_relation_code'], "Member_Occupation_Cd" => null, "Member_Suminsured" => $checkMember[$i]['cover'], "Member_MaritalStatus" => ($checkMember[$i]['policy_member_marital_status'] == 'Married') ? 1 : "", "Member_Height" => "", "Member_Weight" => "", "Nominee_Nm" => $proposal_value['nominee_first_name'] . " " . $proposal_value['nominee_last_name'], "Nominee_Relation_Code" => $proposal_value['nominee_relation'], "DailySmoking_Quantity" => "", "HardLiquorConsumed_Quantity" => "", "WineConsumed_Quantity" => "", "BeerConsumed_Quantity" => "", "MasalaGutka_Quantity" => "", "Details_of_Disability" => "", "Details_of_Injury" => "", "Previous_Policy_No" => "", "Previous_Insurer_Cd" => "", "Previous_Policy_StartDate" => "", "Previous_Policy_EndDate" => "", "Previous_Suminsured" => "", "Previous_IsClaim" => 0, "Monthly_Income" => "", "LoadingGCI" => "", "LoadingGPA" => "", "Plan_Cd" => $proposal_value['group_code']];

                }

                $first_name = $master_customer['first_name'];
                $first_name = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $first_name)));

                $first_name = explode(" ", $first_name, 2);
                if (empty($master_customer['last_name'])) {
                    $master_customer['first_name'] = $first_name[0];
                    $master_customer['last_name'] = $first_name[1];
                }

    $data = array(
        "status" => "error",
        "msg" => "Policy Issuance failed",
        "ErrCode" => ''
    );
                $fqrequest = ["ProposalDetailObj" => ["Quote_Number" => "", "Proposer_Salutation" => $master_customer['salutation'], "Proposer_First_Nm" => $master_customer['first_name'], "Proposer_Middle_Nm" => $master_customer['middle_name'], "Proposer_Last_Nm" => $master_customer['last_name'], "Proposer_Email" => $master_customer['email_id'], "Proposer_Mobile" => $master_customer['mobile_no'], "Proposer_DOB" => $master_customer['dob'], "Proposer_Gender" => ($master_customer['gender'] == "Male") ? "M" : "F", "Proposer_MaritalStatus" => ($master_customer['marital_status'] == 'Married') ? 1 : "", "Proposer_AnnualIncome" => null, "Proposer_Nationality" => "Indian", "Proposer_PanNumber" => "", "Proposer_Address1" => $master_customer['address_line1'], "Proposer_Address2" => $master_customer['address_line2'], "Proposer_Address3" => $master_customer['address_line3'], "Proposer_Area" => "", "Proposer_PinCode" => $master_customer['pincode'], "Proposer_LandineNumber" => "", "Bank_AccountNumber" => "", "Sourcing_br_Sol_ID" => $master_policy['Sourcing_br_Sol_ID'], "Product_Cd" => $master_policy['product_code'], "Scheme_Cd" => $master_policy['scheme_code'], "Master_Policy_No" => $master_policy['policy_number'], "Plan_Cd" => $proposal_value['group_code'], "BA_Number" => $BA_number, "SumInsured_Type" => $master_policy_premium['si_type'], "Member_Type_Cd" => trim($proposal_value['member_code']), "Policy_From" => date('Y-m-d', strtotime($payment_details['transaction_date'])), "COI_Number" => "", "Policy_Tenure" => "1", "Proposer_Suminsured" => $proposal_value['sum_insured'], "Proposer_Deductible" => (!empty($proposal_value['deductable'])) ? $proposal_value['deductable'] : ""], "MemberDetailObj" => $member, "MedicalLifeStyleObj

" => [], "PreExistingObj" => [], "OptionalCoverObj" => [], "PaymentObj" => ["Payer_Code" => $master_policy['payer_code'], "Payer_Type" => "CUSTOMER", "Payer_Name" => $master_customer['first_name'] . " " . $master_customer['last_name'], "Payer_Relation" => $master_policy['payer_relation'], "Collection_Amount" => round($payment_details['premium']), "Collection_Receive_Date" => date('Y-m-d', strtotime($payment_details['transaction_date'])), "Collection_Mode" => ($payment_details['payment_mode'] == 1) ? "Online Collection" : $payment_details['payment_mode_name'], "Collection_SubType" => "Razor Pay", "Cheque_Type" => "", "Instrument_Number" => $payment_details['transaction_number'], "Instrument_Date" => date('Y-m-d', strtotime($payment_details['transaction_date'])), "IFSC_Code" => "HDFC0002457", "Mobile_Number" => $master_customer['mobile_no'], "Email_Id" => $master_customer['email_id'], "Bank_MICRCode" => "167", "Bank_Account_Number" => "", "MerchantId" => "", "MerchantName" => ""]];
                insert_application_log($lead_id, 'full_quote_request', json_encode($fqrequest), "", 123);

                $insert_id = $this
                    ->db
                    ->insert_id();

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://healthbuzzapi-grp-3scale-apicast-production.uatwebservices.manipalcigna.com/HealthBuzzAPIUAT/api/Policy/Proposal',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($fqrequest),
                    CURLOPT_HTTPHEADER => array(
                        'app_id:  03e6283e',
                        'User_NM:  testapi',
                        'Password:  Cigna@123',
                        'app_key: 797ce918b7018effdc00ad9b417fcd40',
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                // $retcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//print_r($response);die;

                $request_arr = ["response_data" => json_encode($response)];
                $this
                    ->db
                    ->where("log_id", $insert_id);
                $this
                    ->db
                    ->update("application_logs", $request_arr);

                $err = curl_error($curl);
                // print_r($err);die;

                curl_close($curl);
                if ($err) {
                    //$data['success'] = false;
                    //insert_application_log($lead_id, 'failure_response', json_encode($errorObj['ErrorMessage']), "", 123);
                    $data = array(
                        "status" => "error",
                        "msg" => "Policy Issuance failed",
                        "ErrCode" => ''
                    );



                } else {
                    // print_r($response);die;
                    $newArr = json_decode($response, true);

                    $errorObj_det = $newArr['ErrorObj'];
                    //  echo "<pre>";
                    // print_r($errorObj);
                    // die;
                    //$premium = $newArr['premium'];
                    $return_data = [];
                    foreach ($errorObj_det as $errorObj) {

                        if ($errorObj['ErrorCode'] == 0) {

                            $newRes = $newArr['ResponseObj']['PremiumDetail'];

                            foreach ($newRes as $Response) {
                                $request_arr = array(
                                    "lead_id" => $lead_id,
                                    "certificate_number" => $Response['COI_Number'],
                                    "ProposalNumber" => $Response['Proposal_Number'],
                                    "policy_sub_type_id" => $proposal_value['policy_sub_type_id'],
                                    "proposal_policy_id" => $proposal_value['proposal_policy_id'],
                                    "gross_premium" => $Response['Gross_Premium'],
                                    "PolicyStatus" => $Response['Policy_Status'],
                                    "start_date" => $Response['PolicyIssuedDate'],
                                    "end_date" => $Response['RiskEndDate'],
                                    "created_date" => date('Y-m-d H:i:s'),
                                    //"master_policy_id" => $proposal_value['master_policy_id'],
                                    "customer_id" => $proposal_value['customer_id'],
                                    "ReceiptNumber" => $Response['Receipt_Number'],
                                    //"proposal_no" => $proposal_value['proposal_no'],
                                    "policy_no" => $Response['Policy_Number'],
                                    "status" => 'Success'

                                );


                                $cert_check = $this->db->select('*')->from('api_proposal_response')
                                    ->where('lead_id', $lead_id)
                                    ->where('certificate_number', $Response['COI_Number'])
                                    ->get()
                                    ->row_array();

                                $this->db->where('lead_id', $lead_id);
                                $this->db->where('proposal_no', $proposal_value['proposal_no']);
                                $this->db->update('api_proposal_response', $request_arr);
                                $this->db->where('lead_id', $lead_id);

                                $req_arr = array("proposal_status" => 'Success');
                                $this->db->where('lead_id', $lead_id);
                                $this->db->update('proposal_payment_details', $req_arr);


                                $req_arr1 = array("status" => 'Success');
                                $this->db->where('lead_id', $lead_id);
                                $this->db->where('proposal_no', $proposal_value['proposal_no']);
                                $this->db->update('proposal_policy', $req_arr1);
                                // $return_data['status'] = 'error';

                                $req_data1['lead_id'] = $lead_id;
                               
                               // $data['success'] = true;

                         


                                $data = array(
                                    "status" => "success",
                                    "msg" => "success",
                                    "ErrCode" => 0
                                );


                            }
                        } else {
//echo 123;die;
                            //  echo "<pre>";
                            //   print_r($errorObj);die;
                            ///------- @author : Guru --------------------------//
                            $data['success'] = false;
                            insert_application_log($lead_id, 'failure_response', json_encode($errorObj['ErrorMessage']), "", 123);
                            $data = array(
                                "status" => "error",
                                "msg" => "Policy Issuance failed",
                                "ErrCode" => 1
                            );


                        }
                    }
                }
            }
            }

     echo json_encode($data);

}
    public function generateQuoteabc()
    {
        $plan_id = $this->input->post('plan_id');
        $premium = $this->input->post('premium');
        $si_type = $this->input->post('si_type_id');
        $cover = $this->input->post('cover');
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
     
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $data['get_suminsured_type'] = $this->db->query("select suminsured_type from master_suminsured_type where suminsured_type_id = '$si_type'")->row_array();
        $quote_member_plan_details = $this->db->query("select cover from quote_member_plan_details where lead_id = '$lead_id'")->row_array();
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.member_name,ma.si_type_id,ma.dob,ma.deductable,mst.suminsured_type')
            ->from('member_ages ma')
            ->join('master_suminsured_type mst', 'mst.suminsured_type_id = ma.si_type_id')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();

        $data['lead_details'] = $this->db->query("select is_mailer_api from lead_details where lead_id = '$lead_id'")->row_array();    

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        $deductable = '';


        $sub_type = [];
        $data['get_family_construct_data_all'] = $this->get_family_construct_data_policy_wise($policy_id);
      // print_r($data['get_family_construct_data_all']);die;
    //  print_R($plan_id);die;
        $plan_details = $this->apimodel->getProductDetailsAll_diff($plan_id);
        //var_dump($plan_details);die;
       // echo $this->db->last_query();exit;
        $customer_details = $this->customerapimodel->getCustomerDetailByid($customer_id);
        $gender = $plan_details[0]['gender'];
        switch ($gender) {
            case 'F':
                $gender_con = "AND (fc.gender is NULL OR fc.gender='Female')";
                break;
            case 'M':
                $gender_con = "AND (fc.gender is NULL OR fc.gender='Male')";
                break;
            
            default:
                $gender_con =  "AND (fc.gender is NULL OR fc.gender='Female' OR fc.gender='Male')";
                break;
        }
        if(($customer_details['gender']=='Male' && $gender=='F') || ($customer_details['gender']=='Female' && $gender=='M') ){
            $gender_con .= " AND fc.member_type!='Self' ";
        }

        if(($customer_details['gender']=='Male' && $gender=='M') || ($customer_details['gender']=='Female' && $gender=='F') ){
            $gender_con .= " AND fc.member_type!='Spouse' ";
        }

        
        $fam_const_det = $this->db->query("select is_consider_adult,MAX(adult_count) as adult_count,MAX(child_count) as child_count,GROUP_CONCAT(distinct(mpf.member_type_id)) as member_type_id FROM master_policy mp INNER JOIN master_policy_family_construct mpf inner join family_construct as fc WHERE mpf.member_type_id = fc.id AND  mp.policy_id = mpf.master_policy_id and mp.plan_id = '$plan_id' $gender_con and mpf.isactive=1 GROUP BY mp.plan_id")->row_array();

       
        $data['get_family_construct_data']['child'] = $fam_const_det['child_count'];
        $get_member_type = explode(',',$fam_const_det['member_type_id']);
        $arr_fam_det = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            if ($value['member_age'] >= '18' && $fam_const_det['is_consider_adult'] == 1){
                $value['is_adult'] = 'Y';
            }
            $is_adult = $value['is_adult'];

            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            } else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            //array_push($arr_age, $value['member_age']);

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            $deductable = $value['deductable'];

        }
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];
        $data['max_age'] = max($arr_age);
        $max_age = $data['max_age'];
        foreach ($get_member_type as $key =>$valp)
        {

            $sql_t = $this->db->query("select * from family_construct where id = '$valp'")->row_array();

            $arr_fam_det[$key]['id'] = $sql_t['id'];
            $arr_fam_det[$key]['member_type'] = $sql_t['member_type'];
            $arr_fam_det[$key]['is_adult'] = $sql_t['is_adult'];

        }
        $data['get_family_construct_data']['adult'] = $arr_fam_det;
        
        $sum_premium = 0;
        $basis_id=array();
        $deductable_array=[];

        foreach ($plan_details as $key => $policy) {

           $policy_id = $policy['policy_id'];
            $policy_sub_type_id = $policy['policy_sub_type_id'];
            
            if($policy['basis_id'] == 7){
                $cover=$this->db->query("select sum_insured from master_per_day_tenure_premiums where isactive=1 AND master_policy_id=".$policy_id." order by id asc limit 1")->row()->sum_insured;
            }else{
                if(!empty($quote_member_plan_details) && !empty($quote_member_plan_details['cover'])  && count($plan_details)<2){
                    $cover=$quote_member_plan_details['cover'];
                }else{
                    $cover=$this->db->query("select sum_insured from master_policy_premium where isactive=1 AND master_policy_id=".$policy_id." order by policy_premium_id asc limit 1")->row()->sum_insured;
                }
                
            }

            if($policy['basis_id'] == 6){
                $ded = $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " group by deductable order by deductable+0 asc")->result_array();
                foreach ($ded as $d_key => $d_value) {
                    $deductable_array[]=$d_value['deductable'];
                }
                
            }

        //  echo $this->db->last_query();
            $self_data_premium = $this->getAllpremium_single($policy_id, $cover, $data['max_age'],$adult,$child,$policy_sub_type_id,$deductable,$lead_id);
         
            $policyBasisId = $this->db->query("select si_premium_basis_id as basis_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' and isactive = 1")->row()->basis_id;
            $basis_id[$policy_id]=$policyBasisId;
            //var_dump($self_data_premium);
            if($self_data_premium == 0){
                $sum_premium = $sum_premium + $self_data_premium;
            }else{
                $sum_premium = $sum_premium + $self_data_premium['amount'];
            }

//var_dump($self_data_premium);
            $sub_type [] = $policy['policy_sub_type_name'];
            if($self_data_premium == 0){
                $plan_details[$key]['self']['premium'] =  round($self_data_premium);
            }else{
                $plan_details[$key]['self']['premium'] = round($self_data_premium['amount']);
            }

            $plan_details[$key]['self']['sub_type_name'] = $policy['policy_sub_type_name'];
            $plan_details[$key]['self']['cover'] = $cover;

            $get_policy_wise_suminsured = $this->getPolicywiseSumInsured($policy_id);

            $plan_details[$key]['sum_insured'] = $get_policy_wise_suminsured;
            $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id'")->row_array();
            if (!empty($sel_policy_plan)) {
                $plan_details[$key]['already_avail'][] = 1;
            } else {
                $plan_details[$key]['already_avail'][] = 0;
            }
            //echo $policy_id;
            $data['mandatory_if_not_selected'][$policy_id] = $this->customerapimodel->getdata('master_policy_mandatory_if_not_selected_rules', 'master_policy_id, dependent_on_policy_id', 'master_policy_id = ' . $policy_id. ' AND isactive=1');

        }
//       exit;
        if (!empty($sub_type)) {
            if (count($sub_type) > 1) {

                $sub_type_name = implode('+', $sub_type);
            } else {
                $sub_type_name = $sub_type[0];
            }
        }

        $data['self_data']['sub_type_name'] = $sub_type_name;
        $data['self_data']['premium'] = round($sum_premium);
        $arr_data = [];
        $data_arr = [];

        if (!empty($plan_details)) {
            foreach ($plan_details as $get_plans) {


                //var_dump($get_plans['basis_id']);

                $data_arr['policy_sub_type_name'] = $get_plans['policy_sub_type_name'];
                $data_arr['is_combo'] = $get_plans['is_combo'];
                $data_arr['is_optional'] = $get_plans['is_optional'];
                $data_arr['creditor_name'] = $get_plans['creditor_name'];
                $data_arr['product_gender'] = $get_plans['gender'];
                $data_arr['creditor_logo'] = $get_plans['creditor_logo'];
                $data_arr['sumInsured'] = $get_plans['sum_insured'];
                $data_arr['policy_id'] = $get_plans['policy_id'];
                $data_arr['self'] = $get_plans['self'];
                $data_arr['already_avail'] = $get_plans['already_avail'];


                array_push($arr_data, $data_arr);


            }
        }

        $data['deductable_array']=$deductable_array;
        $data['deductable']=$deductable;
        $data['policy_details'] = $arr_data;
        $data['basis_details'] = $basis_id;
        $data['payment_page'] = $plan_details[0]['payment_page'];
        $data['payment_first'] = $plan_details[0]['payment_first'];
        $data['self_mandatory'] = $plan_details[0]['self_mandatory'];
        if ($data) {

            $data['policy_data'] = $data;
            $data['status'] = 200;
            echo json_encode($data);
            exit;
        } else {

            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }

    }

    function get_summary_details()
    {
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $plan_id = $this->input->post('plan_id');

        $query['insured_member'] = $this->db->query("select pmpd.policy_id,mpst.policy_sub_type_name,pmpd.cover,pmpd.premium,pmpd.total_premium,FLOOR(DATEDIFF( `mp`.`policy_end_date`, mp.policy_start_date)/364) as tenure from policy_member_plan_details as pmpd join master_policy as mp join master_policy_sub_type as mpst where pmpd.policy_id = mp.policy_id and mp.policy_sub_type_id =  mpst.policy_sub_type_id and  pmpd.lead_id = $lead_id")->result_array();
       /* var_dump($query);
        echo $this->db->last_query();
        exit;*/
        $plan_name['creditor'] = $this->db->query("select mc.creaditor_name,mp.plan_name,mc.creditor_logo, nominee_mandatory from master_plan as mp join master_ceditors as mc where mp.creditor_id = mc.creditor_id and mp.plan_id = '$plan_id' ")->row_array();
    $abc =   array_merge($query,$plan_name);

        echo json_encode($abc);
        exit;
    }

    function Create_quote_self()
    {

        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $sel_con = $this->input->post('sel_con');
        $quote_info = $this->input->post('quote_info');
        $user_id = 0;
//var_dump($quote_info);exit;
        $cover = $this->input->post('cover');
       // var_dump($cover);exit;
        $total_premium = $this->input->post('total_premium');
        $plan_id = $this->input->post('plan_id');
        $resNew=$this->db->query("select * from master_policy where plan_id=".$plan_id)->result();
        foreach ($resNew as $row){
            $qq=$this->db->query("select (si_premium_basis_id) from master_policy_premium_basis_mapping where master_policy_id = ".$row->policy_id)->row();
            $si_premium_basis_id=$qq->si_premium_basis_id;
            if($si_premium_basis_id != 5){
                $query_del = $this->db->query("delete from policy_member_plan_details where lead_id = '$lead_id' AND policy_id=".$row->policy_id);
            }
        }
       // echo 2;exit;

        //$query_del_paln = $this->db->query("delete from policy_member_plan_details where lead_id = ".$lead_id);

        $query = $this->db->query("select * from Create_quote_member_singlelink where lead_id = '$lead_id'")->row_array();
        if ($this->db->affected_rows() > 0) {

        } else {

            $sel_conArr=explode("+",$sel_con);
            $data_arrNew=array();
            $ids =[0];
            foreach ($sel_conArr as $r){
                $get_dob = $this->db->query("select id,dob from member_ages where member_name = '$r' and lead_id = '$lead_id'  and id not in(".implode(',',$ids).")")->row_array();
                $data_arr = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'member_type' => trim($r),
                    'is_adult' => 'Y',
                    'dob'=> $get_dob['dob'],

                );
                $ids[] = $get_dob['id'];
                array_push($data_arrNew,$data_arr);
            }
            $result = $this->db->insert_batch('Create_quote_member_singlelink', $data_arrNew);
            insert_application_log($lead_id, "quote_inserted", json_encode($data_arr), json_encode(array("response" => "Quote Saved")), $user_id);
        }


        $query_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id'")->row_array();
     //echo $this->db->last_query();exit;
        //   var_dump($query_plan);exit;
        if($this->db->affected_rows() > 0)
        //if (count($query_plan) > 0)
        {
          // echo 1233;exit;

        } else {
          //  echo 123;exit;
            //previous code
           /* $quote = explode(',', $quote_info);
            foreach ($quote as $val) {
                $val_imp = explode('-', $val);

                $data = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'plan_id' => $plan_id,
                    'cover' => $cover,
                    'premium' => $val_imp[1],
                    'policy_id' => $val_imp[0],
                    'total_premium' => $total_premium
                );
                $result_arr = $this->db->insert('policy_member_plan_details', $data);
            }*/
            //previous code

            $quote = explode(',', $quote_info);
            //echo $cover;
            $coverNew = explode(',', $cover);
          //   var_dump($coverNew);die;
            $covT=array();
            foreach ($coverNew as $cov){
                $coverNew1 = explode('-', $cov);
                $covT[]=$coverNew1[1];
            }
//var_dump($covT);exit;
            foreach ($quote as $key=>$val) {
                $val_imp = explode('-', $val);
//echo  $covT[$key];

                $data = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'plan_id' => $plan_id,
                    'cover' => $covT[$key],
                    'premium' => $val_imp[1],
                    'policy_id' => $val_imp[0],
                    'total_premium' => $total_premium
                );

                $result_arr = $this->db->insert('policy_member_plan_details', $data);
            }


        }
        exit;
    }

    function Create_quote_self_from_quote()
    {
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $cover = $this->input->post('cover');
        $plan_id = $this->input->post('plan_id');

        $query_plan = $this->db->query("delete from quote_member_plan_details where lead_id = '$lead_id'");

        $data = array(
            'lead_id' => $lead_id,
            'customer_id' => $customer_id,
            'plan_id' => $plan_id,
            'cover' => $cover,
            'premium' => $this->input->post('premium'),
            'si_type_id' => $this->input->post('si_type_id'),
            'plan_name' => $this->input->post('plan_name'),
            'policy_id' => $policy_id,
        );

        $result_arr = $this->db->insert('quote_member_plan_details', $data);




        exit;
    }

    public function getAllpremium_single($policy_id = 0, $sum_insured = 0, $age = 0, $adult = 0, $child = 0,$policy_sub_type_id=0,$deductable=0,$lead_id='')
    {


        if(!empty($lead_id)){
            $quote_data = $this->db->query("select cover from quote_member_plan_details where lead_id=".$lead_id)->row_array();
            if($sum_insured == 0 ) {
                $plan_data = $this->db->query("select plan_id from master_policy where  policy_id=".$policy_id)->row_array();
                if(!empty($plan_data)){
                    $poli_data = $this->db->query("select policy_id from master_policy where  plan_id=".$plan_data['plan_id'])->result_array();
                    if(!empty($quote_data) && !empty($quote_data['cover']) && !empty($poli_data) && count($poli_data)<2 ){
                        $sum_insured = $quote_data['cover'];
                    }
                }
                
            }
        }
        
        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' and isactive = 1")->row_array();
      //  print_r($policy);
     //   echo $policy['basis_id'];
        if ($policy['basis_id'] == 1) {
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
            //echo json_encode(111111);exit;
            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;
            //echo $sum_insured;exit;
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
        //    echo $sum_insured;
            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);
//           var_dump($premium);
//           echo $this->db->last_query();
//            exit();
        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;

            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
            if ($adult != 0 && $child == 0) {
                if (is_array($arr_age)) {
                    foreach ($arr_age as $member_age_cal) {

                        $age = $member_age_cal;
                        $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                        if (!empty($abc)) {
                            foreach ($abc as $rate_data) {
                                $sum = $sum + $rate_data;

                            }
                        }
                    }
                } else {
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }else if ($policy['basis_id'] == 5) {
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
          //  echo "P".$sum_insured;
            $this->age = $age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $age, "tenure" => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] == 6) {
            if($deductable<=0){
                $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adult." and child_count=".$child)->row()->deductable;
           
            }
            //echo $sum_insured;die;
            $this->tenure = 2;
            $this->age = $age;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => '',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1,
                'age'=>$age
            );

            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => $deductable]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] ==7) {
            $this->age = $age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }

        return $premium;


    }

    function getMemebrDetails_single()
    {
        //echo 123;
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        
        $policy_data=$this->db->query("select policy_details from quote_member_plan_details where lead_id=" . $lead_id )->row();

         


        if(!empty($this->input->post('policy_data'))){

            $policy_data = $this->input->post('policy_data');
        }else{
            $policy_data = json_decode($policy_data->policy_details,true);
        }

        foreach ($policy_data as $key => $value) {

            $p_id= preg_replace('/[^A-Za-z0-9\-]/', '', $value['policy_id']);
        }


        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $p_id)
            ->get()
            ->row_array();
        $consider_adult = $policy_det['is_consider_adult'];
        $deductable=$this->db->query("select deductable from member_ages where lead_id=" . $lead_id . " and customer_id=".$customer_id)->row()->deductable;
       //var_dump($policy_data);die;
//     exit;
        $family_con = $policy_data[0]['family_construct'];
        //var_dump($family_con);die;
        $child=0;
        $adult=0;
        $arr_age = [];
        $data['member_ages'] = $this->db->select('fc.is_adult, ma.member_age')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();

        foreach ($data['member_ages'] as $key => $value) {

            if($value['member_age']>=18 && $consider_adult==1){
                $value['is_adult']= 'Y';
            }

            if($value['is_adult']=='Y'){
                array_push($arr_age, $value['member_age']);
            }
            




        }
        $max_age = max($arr_age);

        foreach ($family_con as $item){
            if($item['is_adult']=='Y'){
                $adult++;
            }else{
                $child++;
            }
        }
        $arr = ['adult_count' => $adult, 'child_count' => $child];
        $member_type = [];

        $arr_policy = [];
        $arr_premium = [];
        $total_premium = 0;
        $arr_all = [];
        $sub_name_imp = '';
        $i = 2;
//var_dump($policy_data);exit;
        foreach ($policy_data as $key => $value) {

            $value['policy_id']= preg_replace('/[^A-Za-z0-9\-]/', '', $value['policy_id']);
            ++$key;
            if (strpos($value['policy_id'], ',') !== false) {

                $exp_policy = explode(',', $value['policy_id']);
                // /var_dump($exp_policy);exit;
                foreach ($exp_policy as $key1 => $val) {
                    ++$key1;
                    $policy_sub_type_id=$this->db->query("select policy_sub_type_id from master_policy where policy_id=".$val)->row()->policy_sub_type_id;
                    $sub_name = $this->customerapimodel->getPolicySubTypeName($val);
                    $premium = $this->getAllpremium_single($val, $value['sum_insured'], $max_age, $arr['adult_count'], $arr['child_count'],$policy_sub_type_id,$deductable);
                    $total_premium = $total_premium + $premium['amount'];
                    array_push($arr_all, $sub_name['policy_sub_type_name']);
                    $arr_premium[$key1 + $key]['premium'] = $premium['amount'];
                    $arr_premium[$key1 + $key]['deductable'] = $deductable;
                    $arr_premium[$key1 + $key]['sub_type_name'] = $sub_name['policy_sub_type_name'];
                    $arr_premium[$key1 + $key]['policy_id'] = $val;
                    $arr_premium[$key1 + $key]['sum_insured'] = $value['sum_insured'];
                    $arr_premium[$key1 + $key]['plan_id'] = $value['plan_id'];
                    $arr_premium[$key1 + $key]['plan_flag'] = $value['plan_flag'];

                    if ($premium == 0) {


                        array_push($arr_policy, $sub_name['policy_sub_type_name']);

                    }


                }


            } else {
              //  print_r($value['sum_insured']);
            //    echo $arr['adult_count'];
                $policy_sub_type_id=$this->db->query("select policy_sub_type_id from master_policy where policy_id=".$value['policy_id'])->row()->policy_sub_type_id;
                $premium = $this->getAllpremium_single($value['policy_id'], $value['sum_insured'], $max_age, $arr['adult_count'], $arr['child_count'],$policy_sub_type_id,$deductable,$lead_id);

                $sub_name = $this->customerapimodel->getPolicySubTypeName($value['policy_id']);

                if ($premium == 0) {


                    array_push($arr_policy, $sub_name['policy_sub_type_name']);

                } else {
                    $total_premium = $total_premium + $premium['amount'];
                    array_push($arr_all, $sub_name['policy_sub_type_name']);
                    $arr_premium[$key]['premium'] = $premium['amount'];
                    $arr_premium[$key]['deductable'] = $deductable;
                    $arr_premium[$key]['sub_type_name'] = $sub_name['policy_sub_type_name'];
                    $arr_premium[$key]['policy_id'] = $value['policy_id'];
                    $arr_premium[$key]['sum_insured'] = $value['sum_insured'];
                    $arr_premium[$key]['plan_id'] = $value['plan_id'];
                    $arr_premium[$key]['plan_flag'] = $value['plan_flag'];

                }

            }


        }

//        var_dump($family_con);
//        exit;
        if (!empty($arr_policy)) {
            if (count($arr_policy) > 1) {
                $sub_imp = implode('+', $arr_policy);
            } else {
                $sub_imp = $arr_policy[0];
            }
            $data['status'] = 400;
            $data['msg'] = 'No premium were found for selected construct for ' . $sub_imp;
            echo json_encode($data);
            exit;
        } else {


           // echo 123;die;
            
            $det = $this->db->query("delete from Create_quote_member_singlelink where lead_id  = '$lead_id'");
            
           
            $data_arrN=array();
            $ids=[0];
            foreach ($family_con as $fam) {
$mem_fc =  $fam['family_construct'];

if($fam['is_adult'] == 'Y')
{
    $get_dob = $this->db->select('ma.dob,ma.id,ma.member_type')
        ->from('member_ages ma')

        ->join('family_construct fc', 'fc.id = ma.member_type')
        ->where(['fc.member_type' => $fam['family_construct'],'ma.lead_id' => $lead_id])
        ->where_not_in('ma.id',$ids)
        ->get()
        ->row_array();
        $name =$fam['family_construct']; 
//print_r($this->db->last_query());die;
}else{
    $get_dob = $this->db->query("select id,dob,member_type from member_ages where member_name = '$mem_fc' and lead_id='$lead_id'  and id not in(".implode(',',$ids).")")->row_array();
    $member_name_array =  $this->db->query("select member_type from family_construct where id = '".$get_dob['member_type']."'")->row_array();
    $name =$member_name_array['member_type'];

}

                $ids[]=$get_dob['id'];
                $data_arr = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'member_type' => $fam['family_construct'],
                    'is_adult' => $fam['is_adult'],
                    'dob' => $get_dob['dob'],
                    'member_name'=>$name

                );
                $data_arrN[]=$data_arr;
                $result = $this->db->insert('Create_quote_member_singlelink', $data_arr);

                array_push($member_type, $fam['family_construct']);
                $is_adult = $fam['is_adult'];

                if ($is_adult == 'Y')
                    $arr['adult_count']++;

                else
                    $arr['child_count']++;
            }
            $user_id=0;
            if($result){
                insert_application_log($lead_id, "quote_inserted", json_encode($data_arrN), json_encode(array("response" => "Quote Saved")), $user_id);
            }
           
            $det_member = $this->db->query("delete from policy_member_plan_details where lead_id  = '$lead_id'");
           
            

            foreach ($arr_premium as $all_data) {
                $data_member_det = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'plan_id' => $all_data['plan_id'],
                    'policy_id' => $all_data['policy_id'],
                    'cover' => $all_data['sum_insured'],
                    'premium' => $all_data['premium'],
                    'total_premium' => $total_premium,
                    'additional_plan_flag' => $all_data['plan_flag'],


                );

                $result_det = $this->db->insert('policy_member_plan_details', $data_member_det);
            }
            if (!empty($arr_all)) {
                if (count($arr_all) > 1) {
                    $sub_name_imp = implode('+', $arr_all);
                } else {
                    $sub_name_imp = $arr_all[0];
                }
            }
            if (!empty($member_type)) {
                if (count($member_type) > 1) {
                    $member_type_det = implode('+', $member_type);
                } else {
                    $member_type_det = $member_type[0];
                }
            }


            //    array_values($arr_premium);
            $data['total_premium'] = $total_premium;
            $data['member_type'] = $member_type_det;
            $data['total_premium'] = $total_premium;
            $data['sub_name_imp'] = $sub_name_imp;

            $data['policy_det'] = array_values($arr_premium);
            $data['status'] = 200;
            $data['msg'] = 'success';
            echo json_encode($data);
            exit;

        }
    }

    function get_family_construct_data_policy_wise($policy_id)
    {
        $query = $this->db->query("SELECT a.*,b.member_min_age, b.member_min_age_days, b.member_max_age  FROM family_construct a 
        INNER JOIN master_policy_family_construct b 
        ON a.id = b.member_type_id
        WHERE b.master_policy_id = '$policy_id' AND a.isactive = 1 
        AND b.isactive = 1
        GROUP BY b.member_type_id
        ORDER BY a.id")->result_array();
//print_r($this->db->last_query());
        return $query;
    }
    public function create_proposal()
    {
        $plan_id = $this->input->post('plan_id');
        $partner_id = $this->input->post('partner_id');
        $premium = $this->input->post('premium');
        // $policy_id = encrypt_decrypt_password($this->input->post('policy_id'),'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        // echo $plan_id .'--'.$premium.'--'.$policy_id.'--'.$lead_id.'--'.$customer_id.'--'.$trace_id;exit;
        $proposal_detail = $this->db->query("select * from proposal_details where plan_id = '$plan_id' ")->row_array();

        $partner_id = encrypt_decrypt_password($this->input->post('partner_id'), 'D');
        $policy_detail_data = $this->db->query("select GROUP_CONCAT(mpfc.member_type_id) as member_type_id,mpfc.master_policy_id,mp.policy_sub_type_id,pmpd.cover,pmpd.premium,pmpd.total_premium,pmpd.plan_id from policy_member_plan_details as pmpd ,master_policy_family_construct as mpfc,master_policy as mp where pmpd.policy_id = mpfc.master_policy_id and mp.policy_id = mpfc.master_policy_id and mpfc.isactive=1 and  pmpd.lead_id = '$lead_id' group by mpfc.master_policy_id")->result_array();

        //$policy_detail = $this->db->query("select policy_id,policy_sub_type_id,mp.creditor_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->result_array();
        // echo $this->db->last_query();exit;
        //  print_r($policy_detail_data);exit;
        $this->db->query("delete from proposal_policy where lead_id = '$lead_id'");

        $this->db->query("delete from proposal_policy_member where lead_id = '$lead_id'");
        $payment_first=$this->db->query("select payment_first from master_plan where plan_id=".$plan_id)->row()->payment_first;
        if($payment_first!=1){
           $this->db->query("delete from proposal_payment_details where lead_id = '$lead_id'");
        }
        
        
        $total_premium = 0;
       // echo 123;
        foreach ($policy_detail_data as $value) {
            $arr_explode = explode(',', $value['member_type_id']);
         //    var_dump($arr_explode);exit;

            if ($partner_id) {

                $response = $this->getAllpremium_det_single($lead_id, $customer_id, $value['master_policy_id'], $value['cover'],$value['policy_sub_type_id']);
            } else {
          
//echo $value['cover'];
                $response = ($this->getAllpremium($lead_id, $customer_id, $value['master_policy_id'], $value['cover'],$value['policy_sub_type_id']));

            }
    

            $payment_mode_id=$this->db->query("select payment_mode_id from plan_payment_mode where master_plan_id=".$plan_id)->row()->payment_mode_id;

            $total_premium = $total_premium + $response['amount'];
            $policy_id = $value['master_policy_id'];
            $policy_detail = $this->db->query("select mp.policy_id,mp.policy_sub_type_id,mp.creditor_id,pd.proposal_details_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->row_array();
//echo $this->db->last_query();exit;
            // print_r($policy_detail);exit;


            $get_policy_data=$this->db->query("select start_series_number,end_series_number,(select coi_type from master_plan mpn where mpn.plan_id=mp.plan_id ) as coi_type from master_policy mp where policy_id=".$policy_id)->row();
            $start_series_number=$get_policy_data->start_series_number;
            $end_series_number=$get_policy_data->end_series_number;
            $coi_type=$get_policy_data->coi_type;
            if($coi_type == 1){
                $query_apr=$this->db->query("select group_concat(lead_id SEPARATOR ',') as all_leads  from api_proposal_response where master_policy_id=".$policy_id." order by pr_api_id desc");
                $resultTT=$query_apr->row()->all_leads;

                if(!is_null($resultTT)){
                    $all_leads=$resultTT;
                    $last_lead=$this->db->query("select lead_id from lead_details where lead_id in(".$all_leads.") and is_api_lead=0 and date(createdon)>=date('2023-04-15') order by createdon desc limit 1 ");
                    if($this->db->affected_rows() > 0){
                        $last_lead=$last_lead->row()->lead_id;
                        $getLastCOI=$this->db->query("select certificate_number from api_proposal_response where lead_id=".$last_lead)->row();
                        $cer=$getLastCOI->certificate_number;
                        $arr=explode("-",$cer);
                        $last_number=$arr[3];
                        $new_number=$last_number+1;
                        if($new_number > $end_series_number){
                            $result['messages'] = "You are not able to generate new COI as series of COI is ended.Please Contact admin!";
                            $result['status'] = false;
                            echo json_encode($result);
                            exit;
                        }
                    }
                }
            }
            $proposal_no = $this->proposal_no_checker();
            $quote_data_single = $this->db->query("select * from Create_quote_member_singlelink where lead_id = '$lead_id'")->result_array();

            if(!empty($quote_data_single))
            {
                $adult_count = 0;
                $child_count = 0;
                foreach ($quote_data_single as $quotes)
                {
                    if($quotes['is_adult'] == 'Y'){
                        $adult_count++;
                    }
                    else{
                        $child_count++;
                    }
                }
            }
            $cover_single = $value['cover'];
            $amt = $response['amount'];
       //     $member_code = $this->db->query("select member_code from master_policy_premium where adult_count = '$adult_count' and child_count = '$child_count' and sum_insured = '$cover_single' and master_policy_id = '$policy_id' and isactive = 1")->row_array();

//print_r($this->db->last_query());die;

            $quote_data_single = $this->db->query("select * from Create_quote_member_singlelink where lead_id = '$lead_id'")->result_array();
            $data = array(
                'proposal_details_id' => $policy_detail['proposal_details_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'master_policy_id' => $policy_id,
                'proposal_no' => $proposal_no,
                'premium_amount' => $response['amount'],
                'tax_amount' => $response['amount'],
                'policy_sub_type_id' => $policy_detail['policy_sub_type_id'],
                'adult_count'=> $adult_count,
                'child_count'=> $child_count,
                'sum_insured'=>$value['cover'],
                'member_code'=>$response['member_code'],
                'group_code' => $response['group_code'],
                'deductable'=>$response['deductable']

            );

            $this->db->insert("proposal_policy", $data);
            //  print_r($this->db->last_query());
            $proposal_id = $this->db->insert_id();
            if($payment_first!=1){    
                $newData = array(
                    'creditor_id' => $policy_detail['creditor_id'],
                    'lead_id' => $lead_id,
                    'trace_id' => $trace_id,
                    'premium' => $response['amount'],
                    'payment_status' => 'Pending',
                    'payment_mode' => $payment_mode_id,
                    'proposal_status' => 'PaymentPending',
                    'created_at' => date("Y-m-d H:i:s"),
                    'remark' => 'Payment Initiate',
                    'payment_date' => date("Y-m-d H:i:s"),
                );
                $this->db->insert("proposal_payment_details", $newData);
            }
            // echo $this->db->last_query();exit;
            $member_det = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id'")->result_array();
//var_dump($member_det);exit;
            foreach ($member_det as $val) {
                if (in_array($val['relation_with_proposal'], $arr_explode)) {

                    $data_det = array(
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'policy_id' => $policy_id,
                        'proposal_policy_id' => $proposal_id,
                        'premium' => $response['amount'],
                        'member_id' => $val['member_id'],
                        'premium_with_tax' => $response['amount'],
                        'created_by' => $customer_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                  //  var_dump($data_det);die;
                    $this->db->insert("proposal_policy_member", $data_det);
                    // print_r($this->db->last_query());exit;

                    $data_plan_mem_arr = array(
                        'premium' => $response['amount']

                    );
                    $this->db->where('policy_id', $value['master_policy_id']);
                    $this->db->where('lead_id', $lead_id);
                    $this->db->update('policy_member_plan_details', $data_plan_mem_arr);

                    $this->db->where('lead_id', $lead_id);
                    $this->db->update('lead_details', ['plan_id'=>$plan_id,'creditor_id'=>$partner_id]);
                }
            }

        }
       // exit;
        $data_plan_total_arr = array(
            'total_premium' => $total_premium

        );

        $this->db->where('lead_id', $lead_id);
        $this->db->update('policy_member_plan_details', $data_plan_total_arr);
        echo 1;
    }
    public function create_proposalGadget()
    {
      //  echo 123;die;
        $plan_id = $this->input->post('plan_id');
        $partner_id = $this->input->post('partner_id');
        $premium = $this->input->post('premium');
        // $policy_id = encrypt_decrypt_password($this->input->post('policy_id'),'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');


        $policy_detail_data = $this->db->query("select mp.policy_sub_type_id,pmpd.cover,mp.policy_id
,pmpd.premium,pmpd.total_premium,pmpd.plan_id from policy_member_plan_details as pmpd ,master_policy as mp 
where pmpd.plan_id= mp.plan_id and pmpd.policy_id= mp.policy_id
 and pmpd.lead_id = '$lead_id' ")->result_array();
         //echo $this->db->last_query();die;
      //     var_dump($policy_detail_data);die;
        $this->db->query("delete from proposal_policy where lead_id = '$lead_id'");

        $this->db->query("delete from proposal_policy_member where lead_id = '$lead_id'");
        $this->db->query("delete from proposal_payment_details where lead_id = '$lead_id'");


        foreach ($policy_detail_data as $value) {
            //$arr_explode = explode(',', $value['member_type_id']);

            $payment_mode_id=$this->db->query("select payment_mode_id from plan_payment_mode where master_plan_id=".$plan_id)->row()->payment_mode_id;

            $total_premium =$value['premium'];
            $premium =$value['premium'];
            $policy_id = $value['policy_id'];
            $policy_detail = $this->db->query("select mp.policy_id,mp.policy_sub_type_id,mp.creditor_id,pd.proposal_details_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->row_array();
         //   echo $this->db->last_query();die;


            $get_policy_data=$this->db->query("select start_series_number,end_series_number,(select coi_type from master_plan mpn where mpn.plan_id=mp.plan_id ) as coi_type from master_policy mp where policy_id=".$policy_id)->row();
            $start_series_number=$get_policy_data->start_series_number;
            $end_series_number=$get_policy_data->end_series_number;
            $coi_type=$get_policy_data->coi_type;
            if($coi_type == 1){
                $query_apr=$this->db->query("select group_concat(lead_id SEPARATOR ',') as all_leads  from api_proposal_response where master_policy_id=".$policy_id." order by pr_api_id desc");
                $resultTT=$query_apr->row()->all_leads;

                if(!is_null($resultTT)){
                    $all_leads=$resultTT;
                    $last_lead=$this->db->query("select lead_id from lead_details where lead_id in(".$all_leads.") and is_api_lead=0 and date(createdon)>=date('2023-04-15') order by createdon desc limit 1 ");
                    if($this->db->affected_rows() > 0){
                        $last_lead=$last_lead->row()->lead_id;
                        $getLastCOI=$this->db->query("select certificate_number from api_proposal_response where lead_id=".$last_lead)->row();
                        $cer=$getLastCOI->certificate_number;
                        $arr=explode("-",$cer);
                        $last_number=$arr[3];
                        $new_number=$last_number+1;
                        if($new_number > $end_series_number){
                            $result['messages'] = "You are not able to generate new COI as series of COI is ended.Please Contact admin!";
                            $result['status'] = false;
                            echo json_encode($result);
                            exit;
                        }
                    }
                }
            }

            $proposal_no = $this->proposal_no_checker();
            $data = array(
                'proposal_details_id' => $policy_detail['proposal_details_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'master_policy_id' => $policy_id,
                'proposal_no' => $proposal_no,
                'premium_amount' => $premium,
                'tax_amount' => $premium,
                'policy_sub_type_id' => $policy_detail['policy_sub_type_id']

            );

            $this->db->insert("proposal_policy", $data);
            $proposal_id = $this->db->insert_id();
         //   echo $proposal_id;
            $newData = array(
                'creditor_id' => $policy_detail['creditor_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'premium' => $premium,
                'payment_status' => 'Pending',
                'payment_mode' => $payment_mode_id,
                'proposal_status' => 'PaymentPending',
                'created_at' => date("Y-m-d H:i:s"),
                'remark' => 'Payment Initiate',
                'payment_date' => date("Y-m-d H:i:s"),
            );
            $this->db->insert("proposal_payment_details", $newData);
            // echo $this->db->last_query();exit;
            $member_det = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id'")->result_array();
//var_dump($member_det);exit;
            foreach ($member_det as $val) {
               // if (in_array($val['relation_with_proposal'], $arr_explode)) {

                    $data_det = array(
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'policy_id' => $policy_id,
                        'proposal_policy_id' => $proposal_id,
                        'premium' => $premium,
                        'member_id' => $val['member_id'],
                        'premium_with_tax' => $premium,
                        'created_by' => $customer_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                    //  var_dump($data_det);die;
                    $this->db->insert("proposal_policy_member", $data_det);
                    // print_r($this->db->last_query());exit;

                    $data_plan_mem_arr = array(
                        'premium' => $premium,
                        'total_premium' => $premium

                    );
                    $this->db->where('policy_id', $value['policy_id']);
                    $this->db->where('lead_id', $lead_id);
                    $this->db->update('policy_member_plan_details', $data_plan_mem_arr);

               // }
            }

        }
        // exit;
      /*  $data_plan_total_arr = array(
            'total_premium' => $total_premium

        );

        $this->db->where('lead_id', $lead_id);
     $data123=   $this->db->update('policy_member_plan_details', $data_plan_total_arr);*/
        echo json_encode('true');die;
    }
    public function create_proposalOLD()
    {
        $plan_id = $this->input->post('plan_id');
        $partner_id = $this->input->post('partner_id');
        $premium = $this->input->post('premium');
        // $policy_id = encrypt_decrypt_password($this->input->post('policy_id'),'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        // echo $plan_id .'--'.$premium.'--'.$policy_id.'--'.$lead_id.'--'.$customer_id.'--'.$trace_id;exit;
        $proposal_detail = $this->db->query("select * from proposal_details where plan_id = '$plan_id' ")->row_array();


        $policy_detail_data = $this->db->query("select GROUP_CONCAT(mpfc.member_type_id) as member_type_id,mpfc.master_policy_id,mp.policy_sub_type_id,pmpd.cover,pmpd.premium,pmpd.total_premium,pmpd.plan_id from policy_member_plan_details as pmpd ,master_policy_family_construct as mpfc,master_policy as mp where pmpd.policy_id = mpfc.master_policy_id and mp.policy_id = mpfc.master_policy_id and  pmpd.lead_id = '$lead_id' group by mpfc.master_policy_id")->result_array();
            //var_dump($policy_detail_data);exit;
        //$policy_detail = $this->db->query("select policy_id,policy_sub_type_id,mp.creditor_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->result_array();
        // echo $this->db->last_query();exit;
        // print_r($policy_detail);exit;
        $this->db->query("delete from proposal_policy where lead_id = '$lead_id'");

        $this->db->query("delete from proposal_policy_member where lead_id = '$lead_id'");
        $this->db->query("delete from proposal_payment_details where lead_id = '$lead_id'");
        $total_premium = 0;
        foreach ($policy_detail_data as $value) {
            $arr_explode = explode(',', $value['member_type_id']);
            if ($partner_id) {
                $response = $this->getAllpremium_det_single($lead_id, $customer_id, $value['master_policy_id'], $value['cover'],$value['policy_sub_type_id']);
            } else {
                $response = $this->getAllpremium($lead_id, $customer_id, $value['master_policy_id'], $value['cover']);
            }
            //var_dump($response);

            $total_premium = $total_premium + $response['amount'];
            $policy_id = $value['master_policy_id'];
            $policy_detail = $this->db->query("select mp.policy_id,mp.policy_sub_type_id,mp.creditor_id,pd.proposal_details_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->row_array();

            //  print_r($policy_detail);
            $proposal_no = $this->proposal_no_checker();
            $data = array(
                'proposal_details_id' => $policy_detail['proposal_details_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'master_policy_id' => $policy_id,
                'proposal_no' => $proposal_no,
                'premium_amount' => $premium,
                'policy_sub_type_id' => $policy_detail['policy_sub_type_id']

            );

            $this->db->insert("proposal_policy", $data);
            //  print_r($this->db->last_query());
            $proposal_id = $this->db->insert_id();
            $newData = array(
                'creditor_id' => $policy_detail['creditor_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'premium' => $premium,
                'payment_status' => 'Pending',
                'payment_mode' => '1',
                'proposal_status' => 'PaymentPending',
                'created_at' => date("Y-m-d H:i:s"),
                'remark' => 'Payment Initiate',
                'payment_date' => date("Y-m-d H:i:s"),
            );
            $this->db->insert("proposal_payment_details", $newData);
            // echo $this->db->last_query();exit;
            $member_det = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id'")->result_array();

            foreach ($member_det as $val) {
                if (in_array($val['relation_with_proposal'], $arr_explode)) {

                    $data_det = array(
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'policy_id' => $policy_id,
                        'proposal_policy_id' => $proposal_id,
                        'premium' => $response['amount'],
                        'member_id' => $val['member_id'],
                        'premium_with_tax' => $response['amount'],
                        'created_by' => $customer_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                    $this->db->insert("proposal_policy_member", $data_det);
                    // print_r($this->db->last_query());exit;
                    $data_plan_mem_arr = array(
                        'premium' => $response['amount']

                    );
               //   var_dump($data_plan_mem_arr);
                    $this->db->where('policy_id', $value['master_policy_id']);
                    $this->db->where('lead_id', $lead_id);
                    $this->db->update('policy_member_plan_details', $data_plan_mem_arr);

                }
            }

        }
        exit;
        $data_plan_total_arr = array(
            'total_premium' => $total_premium

        );

        $this->db->where('lead_id', $lead_id);
        $this->db->update('policy_member_plan_details', $data_plan_mem_arr);
        echo 1;
    }

    function updateCustomerDetails()
    {
        $partner_id = $this->input->post('partner_id');

        $dob = date("Y-m-d", strtotime($_POST['proposer_dob']));
        //$diff = date_diff(date_create(date('d-m-Y', strtotime($dob))) , date_create(date("Y-m-d")));
        //$age = $diff->format('%y');
        $ages = $this->cal_age(date('Y-m-d', strtotime($dob)));
       $age = $ages['age'];
        $age_type = $ages['age_type'];
        $validation['status'] = 'success';
        if ($partner_id) {

        } else {
            if($_POST['is_proposer_insured']){
                $validation = $this->validate_member_age($age_type, $age, $_POST['policy_id'], 1);
            }else{
                if($age < 18){
                    $data['status'] = 'error';//$memberType
                    $data['message'] = "Proposar's age should be minimum 18 Years";
                    echo json_encode($data);
                    exit;
                }

            }

        }
        //print_r($validation);exit;
        if ($validation['status'] == 'error') {
            echo json_encode($validation);
            exit;
        }
        $data = $this->db->update(
            'master_customer',
            [
                'first_name' => $_POST['fname'],
                'salutation' => $_POST['salutation'],
                'last_name' => $_POST['lname'],
                'full_name' => $_POST['fname'] . ' ' . $_POST['lname'],
                'gender' => $_POST['gender'],
                'dob' => $dob,
                'email_id' => $_POST['email'],
                'mobile_no' => $_POST['mobile_no'],
                'address_line1' => $_POST['proposer_address'],
                'address_line2' => $_POST['proposer_address2'],
                'pincode' => $_POST['proposer_pincode'],
                'city' => $_POST['proposer_city'],
                'state' => $_POST['proposer_state'],
                'pan' => $_POST['proposer_pan'],
                'gstin' => $_POST['gstin'],
                'marital_status' => $_POST['status'],
                'is_proposer_insured' => $_POST['is_proposer_insured'],
                'invoice_file' => $_POST['invoice_file'],
                'unique_number' => $_POST['unique_number'],

                'make' => $_POST['make'],
            ],
            [
                'lead_id' => $_POST['lead_id'],
                'customer_id' => $_POST['customer_id']
            ]
        );
       // print_r($data);die;
        $d=array(
            'first_name' => $_POST['fname'],
            'last_name' => $_POST['lname'],
            'salutation' => $_POST['salutation'],
            'full_name' => $_POST['fname'] . ' ' . $_POST['lname'],
            'gender' => $_POST['gender'],
            'dob' => $dob,
            'email_id' => $_POST['email'],
            'mobile_no' => $_POST['mobile_no'],
            'address_line1' => $_POST['proposer_address'],
            'address_line2' => $_POST['proposer_address2'],
            'pincode' => $_POST['proposer_pincode'],
            'city' => $_POST['proposer_city'],
            'state' => $_POST['proposer_state'],
            'pan' => $_POST['proposer_pan'],
            'gstin' => $_POST['gstin'],
            'marital_status' => $_POST['status'],
            'is_proposer_insured' => $_POST['is_proposer_insured'],
            'invoice_file' => $_POST['invoice_file'],
        );
        insert_application_log($_POST['lead_id'], "proposer_detail_submit", json_encode($d),  json_encode(array("response" => "Proposer Detail Saved")), 0);
        $data_mem = $this->db->get_where("member_ages", array('member_type' => 1, 'lead_id' => $_POST['lead_id'], 'customer_id' => $_POST['customer_id']))->row_array();
        if ($data_mem) {


            $res = $this->db->update(
                'member_ages',
                [
                    'member_age' => $age,
                ],
                [
                    'member_type' => 1,
                    'lead_id' => $_POST['lead_id'],
                    'customer_id' => $_POST['customer_id']
                ]
            );
            $res = $this->db->update(
                'proposal_policy_member_details',
                [
                    'policy_member_dob' => $dob,
                    'policy_member_age' => $age,
                ],
                [
                    'relation_with_proposal' => 1,
                    'lead_id' => $_POST['lead_id'],
                    'customer_id' => $_POST['customer_id']
                ]
            );
        }
        $data = ["status" => 'success',
            "message" => 'Proposer details updated successfully !'];
        echo json_encode($data);

    }

    function axis_pincode_get_state_city()
    {
        $pin_code = $_POST['pincode'];

        $trace_id = $this->session->userdata('trace_id');
        $trace_id = encrypt_decrypt_password($trace_id, 'D'); //"31637406003";

        $pin_code_data = $this->db->select('CITY,STATE')
            ->from('axis_postal_code')
            ->where('PINCODE', $pin_code)
            ->get()
            ->row_array();

        if ($pin_code_data) {


            $data = [
                'status_code' => '200',
                'status' => 'success',
                'pincode_data' => $pin_code_data
            ];

        } else {

            $data = [
                'status_code' => '',
                'status' => 'failed'
            ];

        }

        echo json_encode($data);
    }

    function get_family_construct_data()
    {
        $product_id = $this->input->post('product_id');
        if($product_id){
            $product_id = encrypt_decrypt_password($product_id, 'D');
        }
        
        $query = "SELECT a.*,b.member_min_age, b.member_min_age_days, b.member_max_age  FROM family_construct a 
        INNER JOIN master_policy_family_construct b 
        ON a.id = b.member_type_id
        WHERE a.isactive = 1 
        AND b.isactive = 1
        GROUP BY b.member_type_id
        ORDER BY a.id";
//print_r($product_id);exit;
        if($product_id){
            $query = "SELECT a.*,b.member_min_age, b.member_min_age_days, b.member_max_age  FROM family_construct a 
                    INNER JOIN master_policy_family_construct b 
                    ON a.id = b.member_type_id inner join master_policy p on p.policy_id=b.master_policy_id  WHERE a.isactive = 1 
                    AND b.isactive = 1 AND p.isactive = 1 and p.plan_id=".$product_id."
                    GROUP BY b.member_type_id
                    ORDER BY a.id" ;
        }
        $query = $this->db->query($query);
        $data = $result_array=$query->result_array();
        
        if($product_id){
            $result_array=[];
            foreach ($data as $key => $value) {
                if($value['member_min_age']>=18){
                    $value['is_adult']='Y';
                }
                $result_array[] = $value;
            }
        }

        echo json_encode($result_array);
    }

    //upendra - update
    function pincode_insert()
    {
        $pin_code = $_POST['pin_code'];

        $trace_id = $_POST['trace_id'];//$this->session->userdata('trace_id');
        $trace_id = encrypt_decrypt_password($trace_id, 'D'); //"31637406003";
        $customer_id = $_POST['customer_id'];
        $customer_id = encrypt_decrypt_password($customer_id, 'D'); //"31637406003";

        $pin_code_data = $this->db->select('CITY,STATE')
            ->from('axis_postal_code')
            ->where('PINCODE', $pin_code)
            ->get()
            ->row_array();

        if ($pin_code_data) {

            $data = ["pincode" => $pin_code, "city" => $pin_code_data['CITY'], "state" => $pin_code_data['STATE']];

            $this->db->where('trace_id', $trace_id);
            $this->db->update("lead_details", $data);
            if ($customer_id) {
                $this->db->where('customer_id', $customer_id);
                $this->db->update("master_customer", $data);
            }

            $data = [
                'status_code' => '200',
                'status' => 'success',
                'CITY' => $pin_code_data['CITY']
            ];

        } else {

            $data = [
                'status_code' => '',
                'status' => 'failed'
            ];

        }

        echo json_encode($data);
    }

    function insert_pop_city()
    {

        $city_name = $_POST['city_name'];

        $city_data = $this->db->select('STATE,PINCODE')
            ->from('axis_postal_code')
            ->where('LOWER(CITY)', strtolower($city_name))
            ->get()
            ->row_array();

        if ($city_data) {

            $data = [
                'status_code' => '200',
                'status' => 'success',
                'PINCODE' => $city_data['PINCODE']
            ];

        } else {

            $data = [
                'status_code' => '',
                'status' => 'failed'
            ];

        }

        echo json_encode($data);

    }

    function createLead()
    {


        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');
        $name = $this->input->post('name');
        $gender = $this->input->post('gender');
        $userId = $this->input->post('userId');
        $creditor_id = $this->input->post('creditor_id');
        $plan_id = $this->input->post('plan_id');
        $journey_type = $this->input->post('journey_type');
        $make_model = $this->input->post('make_model');
        $gadget_purchase_date = $this->input->post('gadget_purchase_date');
        $gadget_purchase_price = $this->input->post('gadget_purchase_price');
        $Address1 = $this->input->post('Address1');
        $Address2 = $this->input->post('Address2');
        $Address3 = $this->input->post('Address3');
        $pincode = $this->input->post('pincode');
        $is_api_lead = $this->input->post('is_api_lead');
        $is_mailer_api = $this->input->post('is_mailer_api');
        if(!isset($is_api_lead)){
            $is_api_lead=0;
        }
        $this->db->insert('lead_details', [
            'mobile_no' => $mobile,
            'trace_id' => time(),
            'user_activity' => 1,
            'createdon' => date('Y-m-d H:i:s'),
            'email_id' => $email,
            'gadeget_purchase_date' => $gadget_purchase_date,
            'gadget_price' => $gadget_purchase_price,
            'createdby' => $userId,
            'sales_manager_id' => $userId,
            'is_api_lead' => $is_api_lead,
            'is_mailer_api' => $is_mailer_api,
            'dropout_page' => '1',
            'dropoff_flag' => '0',
            'creditor_id' => $creditor_id,
            'plan_id' => $plan_id,
            'journey_type' => $journey_type,
        ]);


        $lead_id = $this->db->insert_id();

        $parts = explode(" ", $name);
        if (count($parts) > 1) {
            $lastname = array_pop($parts);
            $firstname = implode(" ", $parts);
        } else {
            $firstname = $name;
            $lastname = " ";
        }

        if ($gender == 'male') {

            $salutation = 'Mr';
        } else {

            $salutation = 'Ms';
        }

        $this->db->insert('master_customer', [
            'mobile_no' => $mobile,
            'lead_id' => $lead_id,
            'salutation' => $salutation,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'gender' => $gender,
            'full_name' => $name,
            'email_id' => $email,
            'unique_number' => $make_model,
            'createdon' => date('Y-m-d H:i:s'),
            'address_line1' => $Address1,
            'address_line2' => $Address2,
            'address_line3' => $Address3,
            'pincode' => $pincode,
        ]);

        $customer_id = $this->db->insert_id();
        // var_dump($customer_id);exit;

        $trace_id = $customer_id . time();
        $this->db->update('lead_details', ['trace_id' => $trace_id], "lead_id = $lead_id");

        $data = [

            'lead_id' => encrypt_decrypt_password($lead_id),
            'customer_id' => encrypt_decrypt_password($customer_id),
            'trace_id' => encrypt_decrypt_password($trace_id)
        ];

        //upendra - update
        //$the_session = array("trace_id" => $trace_id);
        //$this -> session -> set_userdata($the_session);

        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;
    }


    function updateLead()
    {


        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');
        $name = $this->input->post('name');
        $gender = $this->input->post('gender');


        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');

        $query=  $this->db->update('lead_details',[
            'mobile_no' => $mobile,
            'email_id' => $email,

        ], "lead_id = $lead_id");



        $parts = explode(" ", $name);
        if (count($parts) > 1) {
            $lastname = array_pop($parts);
            $firstname = implode(" ", $parts);
        } else {
            $firstname = $name;
            $lastname = " ";
        }

        if ($gender == 'male') {

            $salutation = 'Mr';
        } else {

            $salutation = 'Ms';
        }

        $query=  $this->db->update('master_customer', [
            'mobile_no' => $mobile,

            'salutation' => $salutation,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'gender' => $gender,
            'full_name' => $name,
            'email_id' => $email,

        ], "lead_id = $lead_id");

        $data = [

            'lead_id' => $this->input->post('lead_id'),
            'customer_id' =>$this->input->post('customer_id'),
            'trace_id' => $this->input->post('trace_id')
        ];

        //upendra - update
        //$the_session = array("trace_id" => $trace_id);
        //$this -> session -> set_userdata($the_session);

        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;
    }
    function addMarineDetails(){
        $lead_id=$this->input->post('lead_id');
        $query=$this->db->query("select lead_id from marine_customer_info where lead_id=".$lead_id)->row();
        $data_marine=array(
            'lead_id'=>$lead_id,
            'mode_of_shipment'=>$this->input->post('mode_of_shipment'),
            'from_country'=>$this->input->post('from_country'),
            'to_country'=>$this->input->post('to_country'),
            'from_city'=>$this->input->post('from_city'),
            'to_city'=>$this->input->post('to_city'),
            'currency_type'=>$this->input->post('currency_type'),
            'cargo_value'=>$this->input->post('cargo_value'),
            'rate_of_exchange'=>$this->input->post('rate_of_exchange'),
            'date_of_shipment'=>$this->input->post('date_of_shipment'),
            'Bill_number'=>$this->input->post('Bill_number'),
            'Bill_date'=>$this->input->post('Bill_date'),
            'credit_number'=>$this->input->post('credit_number'),
            'credit_description'=>$this->input->post('credit_description'),
            'place_of_issuence'=>$this->input->post('place_of_issuence'),
            'Invoice_number'=>$this->input->post('Invoice_number'),
            'Invoice_date'=>$this->input->post('Invoice_date'),
            'subject_matter_insured'=>$this->input->post('subject_matter_insured'),
            'marks_number'=>$this->input->post('marks_number'),
            'vessel_name'=>$this->input->post('vessel_name'),
            'Consignee_name'=>$this->input->post('Consignee_name'),
            'Consignee_add'=>$this->input->post('Consignee_add'),
            'Financier_name'=>$this->input->post('Financier_name'),
            'type_of_shipment'=>$this->input->post('type_of_shipment'),
            'Name_of_Transporter'=>$this->input->post('Name_of_Transporter'),
            'Number_and_kind_of_packages'=>$this->input->post('Number_and_kind_of_packages'),
            'Conveyance'=>$this->input->post('Conveyance'),
            'Packing'=>$this->input->post('Packing'),
            'Excess'=>$this->input->post('Excess'),
            'Basis_of_valuation'=>$this->input->post('Basis_of_valuation'),
            'PANCardNo'=>$this->input->post('pancard'),
            'MobileISD'=>$this->input->post('mobileisd'),
            'GSTDetails'=>$this->input->post('gstdetails'),
            'AadharNumber'=>$this->input->post('adharno'),
            'IsCollectionofform60'=>$this->input->post('collection_form'),
            'AadharEnrollmentNo'=>$this->input->post('adhar_enroll'),
            'eIA_Number'=>$this->input->post('eia_number'),
            'CustomerID'=>$this->input->post('customer_id'),
            'CKYCId'=>$this->input->post('ckycid'),
            'CorelationId'=>'',
            'EKYCid'=>$this->input->post('ekycid'),
            'PEPFlag'=>$this->input->post('pepflag'),
            'ILKYCReferenceNumber'=>$this->input->post('ilkycref_no'),
            'SkipDedupeLogic'=>$this->input->post('skipdedupelogic'),
            'DateOfIncorporation'=>$this->input->post('data_of_incoporation'),
            'SourceOfFunds'=>$this->input->post('source_of_funds'),
            'OtherFunds'=>$this->input->post('other_of_funds'),
            'CIN'=>$this->input->post('cin'),
            'BalanceCargoSIShowInCert'=>$this->input->post('BalanceCargoSIShowInCert'),
            'BlawrrlrDate'=>$this->input->post('BlawrrlrDate'),
            'BlawbrrlRNo'=>$this->input->post('BlawbrrlRNo'),
            'CargoSI'=>$this->input->post('CargoSI'),
            'CargoSICurr'=>$this->input->post('CargoSICurr'),
            'CertificateGenerateDate'=>$this->input->post('CertificateGenerateDate'),
            'CustomValueSICurr'=>$this->input->post('CustomValueSICurr'),
            'Currency'=>$this->input->post('Currency'),
            'CurrencyRate'=>$this->input->post('CurrencyRate'),
            'CustomValueSI'=>$this->input->post('CustomValueSI'),
            'ExpImpoNameAndAddress'=>$this->input->post('ExpImpoNameAndAddress'),
            'CustomValueSICurr'=>$this->input->post('CustomValueSICurr'),
            'FinancialInterest'=>$this->input->post('FinancialInterest'),
            'GoodDescription'=>$this->input->post('GoodDescription'),
            'IntermediaryStorageSI'=>$this->input->post('IntermediaryStorageSI'),
            'IntermediaryStorageSICurr'=>$this->input->post('IntermediaryStorageSICurr'),
            'LCNo'=>$this->input->post('LCNo'),
            'LoadingPremiumrate'=>$this->input->post('LoadingPremiumrate'),
            'LoadingWarSRCCrate'=>$this->input->post('LoadingWarSRCCrate'),
            'LocationFrom'=>$this->input->post('LocationFrom'),
            'LocationTo'=>$this->input->post('LocationTo'),
            'ModeOfTransitAir'=>$this->input->post('ModeOfTransitAir'),
            'ModeOfTransitCourier'=>$this->input->post('ModeOfTransitCourier'),
            'ModeOfTransitPostal'=>$this->input->post('ModeOfTransitPostal'),
            'ModeOfTransitRail'=>$this->input->post('ModeOfTransitRail'),
            'ModeOfTransitRoad'=>$this->input->post('ModeOfTransitRoad'),
            'ModeOfTransitSea'=>$this->input->post('ModeOfTransitSea'),
            'PackagingDetails'=>$this->input->post('PackagingDetails'),
            'PackagingDetailsOther'=>$this->input->post('PackagingDetailsOther'),
            'PlaceOfDischarge'=>$this->input->post('PlaceOfDischarge'),
            'PlaceOfLoading'=>$this->input->post('PlaceOfLoading'),
            'PremiumShownInCert'=>$this->input->post('PremiumShownInCert'),
            'RiskCommensementDate'=>$this->input->post('RiskCommensementDate'),
            'ServiceTaxShownInCert'=>$this->input->post('ServiceTaxShownInCert'),
            'SettlementAgentTown'=>$this->input->post('SettlementAgentTown'),
            'SettlingAgentCountry'=>$this->input->post('SettlingAgentCountry'),
            'SettlingAgentTown'=>$this->input->post('SettlingAgentTown'),
            'ShipmentRemarks'=>$this->input->post('ShipmentRemarks'),
            'TotalPackages'=>$this->input->post('TotalPackages'),
            'TypeMarksAndContainerNo'=>$this->input->post('TypeMarksAndContainerNo'),
            'VoyageNo'=>$this->input->post('VoyageNo'),
            'WeightofGoods'=>$this->input->post('WeightofGoods'),
            'limit_per_sending'=>$this->input->post('limit_per_sending'),
            'limit_per_location'=>$this->input->post('limit_per_location'),
            'PolicySubType'=>  $this->input->post('PolicySubType'),
            'PortofDischarge'=> $this->input->post('PortofDischarge'),
            'PortofLoading' => $this->input->post('portofLoading'),



        );
        if($this->db->affected_rows() > 0){
            $this->db->where(array('lead_id'=>$lead_id));
            $result=$this->db->update('marine_customer_info',$data_marine);
        }else{
            $result= $this->db->insert('marine_customer_info',$data_marine);
        }
        if($result){
            return true;
        }else{
            return false;
        }

    }
    function checkLeadExistorNot(){
        $lead_id = $this->input->post('lead_id');
        $trace_id = $this->input->post('trace_id');
        $unique_id = $this->input->post('unique_id');
        $query = "select lead_id from lead_details where lead_id=".$lead_id." AND trace_id=".$trace_id;
        if(!empty($unique_id)){
           $query .= " AND unique_id=".$unique_id;
        }
        $query=$this->db->query($query)->row();
        if($this->db->affected_rows() > 0){
            echo true;
        }else{
            echo false;
        }
    }
    function checkLeadCancelledorNot(){
        $lead_id = $this->input->post('lead_id');
        $trace_id = $this->input->post('trace_id');
        $query=$this->db->query("select status from proposal_policy where lead_id=".$lead_id." AND trace_id=".$trace_id)->row();
        if($this->db->affected_rows() > 0){
            echo $query->status;
        }else{
            echo false;
        }
    }
    function CD_credit_debit_entry(){
        $cd_data=array(
            'type'=>$this->input->post('type'),
            'amount'=>$this->input->post('amount'),
            'lead_id'=>$this->input->post('lead_id'),
            'creditor_id'=>$this->input->post('creditor_id'),
            'type_trans'=>$this->input->post('type_trans'),
        );
        $result=$this->db->insert('master_cd_credit_debit_transaction',$cd_data);
        echo $result;
    }

    function cover_credit_debit_entry(){
        $cd_data=array(
            'type'=>$this->input->post('type'),
            'amount'=>$this->input->post('amount'),
            'lead_id'=>$this->input->post('lead_id'),
            'creditor_id'=>$this->input->post('creditor_id'),
            'policy_id'=>$this->input->post('policy_id'),
            'plan_id'=>$this->input->post('plan_id'),
            'type_trans'=>$this->input->post('type_trans'),
        );
        $result=$this->db->insert('master_cover_credit_debit_transaction',$cd_data);
        echo $result;
    }
    function updatepurchaseDetails(){
        $price = $this->input->post('price');
        $daterange = $this->input->post('daterange');
        $policy_subtype_id = $this->input->post('policy_subtype_id');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $set=array('gadget_price'=>$price,
                    'gadeget_purchase_date'=>$daterange,
                    'gadget_type'=>$policy_subtype_id
            );
      $query=  $this->db->update('lead_details',$set , "lead_id = $lead_id");
      if($query){
          echo json_encode(array("status_code" => "200"));
          exit;
      }else{
          echo json_encode(array("status_code" => "201"));
          exit;
      }

    }

    function getquoteData(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $sort=$this->input->post('sort');
        $purchaseDate=$this->input->post('purchaseDate');
        $partner_id=$this->input->post('partner_id');
        $subject_matter_insured=$this->input->post('subject_matter_insured');
        $type_of_shipment=$this->input->post('type_of_shipment');
        $partner_id = encrypt_decrypt_password($partner_id, 'D');
        $where =' AND 1=1';
        if(isset($partner_id) && !empty($partner_id)){
            $where = ' AND creditor_id='.$partner_id;
        }
        $now = time(); // or your date as well
        $your_date = strtotime($purchaseDate);
        $datediff = $now - $your_date;

        $eligibilty_days= round($datediff / (60 * 60 * 24));
         $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $query=$this->db->query("select * from lead_details where lead_id=".$lead_id)->row();
        if($this->db->affected_rows() > 0){
            $query_masterData=$this->db->query(
                "select *,
(select si_premium_basis_id from master_policy_premium_basis_mapping mbs where mbs.master_policy_id=mp.policy_id and mbs.isactive=1 order by mapping_id desc limit 1) as basis_type,
(select plan_name from master_plan mpp where mpp.plan_id=mp.plan_id) as plan_name,
(select creditor_id from master_plan mpp where mpp.plan_id=mp.plan_id) as cred_id,
(select logo from master_policy_sub_type mpst where mpst.policy_sub_type_id=mp.policy_sub_type_id) as logo
 from master_policy mp where 
plan_id in(select concat(plan_id) from master_plan  where policy_type_id=3 and isactive=1) 
and mp.policy_sub_type_id = ".$query->gadget_type." and mp.gadget_eligibilty >=".$eligibilty_days." and mp.isactive=1 ".$where." and mp.policy_start_date is not null"
            )->result();
        //    echo $this->db->last_query();die;
            $finalData=array();
            foreach ($query_masterData as $key=>$row){
                //echo 1;
                $data=array();

                $data['plan_name']=$row->plan_name;
                $data['basis_type']=$row->basis_type;
                $data['plan_id']=$row->plan_id;
                $data['policy_id']=$row->policy_id;
                $data['creditor_id']=$row->cred_id;
                $data['sort_prem']=array();
                $getSumInsuredData=[];
                if($row->basis_type == 1){
                    $getSumInsuredData=$this->getSumInsuredDataGadget($row->policy_id,$row->basis_type);

                }else{
                    $getSumInsuredData[]['sum_insured']=$query->gadget_price;
                }
               // echo $row->basis_type;
               // var_dump($getSumInsuredData);
              //  (array_filter($getSumInsuredData));

                if(count($getSumInsuredData) >0 ){
                    foreach ($getSumInsuredData as $key1 => $suminsured) {
                        //print_r($suminsured);
                        if($suminsured['sum_insured'] != 0){
                            $get_policy_det = $this->getAllpremium_gadget($lead_id, $row->policy_id, $suminsured['sum_insured'],$row->basis_type,$subject_matter_insured,$row->policy_sub_type_id,$type_of_shipment);
                            $get_policy_amt = 0;
                            if (!empty($get_policy_det)) {
                                $get_policy_amt = $get_policy_det['amount'];
                                $data['premium'][$key1]['rate'] = round($get_policy_amt);


                              //  if($key1 == 0){
                                    $data['sort_prem'][]=$get_policy_amt;
                              //  }
                            } else {
                                unset($data['premium'][$key1]);
                            }
                        }
                    }
                }
              //  echo "<br>";
                $qry = "SELECT short_description FROM features_config where creditor_id = '" . $row->cred_id . "' AND plan_id = '" . $row->plan_id . "' AND isactive = 1 limit 5";
                $data['features'] = $this->db->query($qry)->result_array();
                $data['sumInsure']=$getSumInsuredData;
               // var_dump($data);die;
                array_push($finalData,$data);

            }
           // print_r($finalData);die;
         //  die;
           // print_r($finalData);die;
            echo json_encode($finalData);
            exit;
        }
    }
    public function getPremiumGadgetapi(){

        $basis_id = $this->input->post('basis_id');
        $policy_id = $this->input->post('policy_id');
        $sum_insured = $this->input->post('sum_insured');
        $subject_matter_insured = $this->input->post('subject_matter_insured');
        $policy_sub_type_id = $this->input->post('policy_sub_type_id');
        $type_of_shipment = $this->input->post('type_of_shipment');
        $lead_id='';
        $result= $this->getAllpremium_gadget($lead_id,$policy_id,$sum_insured,$basis_id,$subject_matter_insured,$policy_sub_type_id,$type_of_shipment);
    //    var_dump($result);die;
        echo json_encode($result);
        exit;
    }

    public function getAllpremium_gadget($lead_id, $policy_id, $sum_insured,$basis_type,$subject_matter_insured='',$policy_sub_type_id='',$type_of_shipment='')
    {

        if ($basis_type == 1) {
            $premium = $this->getpolicypremiumflat_gadgets($policy_id, $sum_insured);

        }else if ($basis_type == 5) {
            $premium = $this->getpolicypremiumperMili_gadgets($policy_id, $sum_insured);

        }else if ($basis_type ==8) {
            $premium = $this->getpolicypremiumpercentage_gadgets($policy_id, $sum_insured,$subject_matter_insured,$policy_sub_type_id,$type_of_shipment);
          //  var_dump($premium);die;
        }
        return $premium;
    }
    function getpolicypremiumpercentage_gadgets($policy_id,$sum_insured,$subject_matter_insured,$policy_sub_type_id,$type_of_shipment){
       // echo $policy_sub_type_id; die;
        if($policy_sub_type_id == 19){
            $premium_type_id=$this->db->query("select premium_type_id from master_policy_premium_type where premium_type_name='".$subject_matter_insured."' and policy_sub_type_id=".$policy_sub_type_id)->row();

            if(!empty($premium_type_id)){
                if( $type_of_shipment == 'Inter'){

                    $premium_type = 'inter_';
                    
                }else{
                    $premium_type = 'intra_';
                    
                }
                $premium_type = $premium_type.$premium_type_id->premium_type_id;
                $premium_rate=$this->db->query("select premium_with_tax as premium_rate from master_policy_premium where isactive=1 and premium_type='".$premium_type."' and master_policy_id=".$policy_id)->row()->premium_rate;
            }
            $amount = ($sum_insured * $premium_rate) /100;
            $response['amount'] = round($amount);
            return $response;


        }else{
            $premium_rate=$this->db->query("select premium_rate from master_policy_premium where isactive=1 and master_policy_id=".$policy_id)->row()->premium_rate;
            //    echo $premium_rate;die;
            $amount = ($sum_insured * $premium_rate) /100;
            $response['amount'] = round($amount);
            return $response;
        }

    }
    function getpolicypremiumperMili_gadgets($policy_id,$sum_insured){
        //echo $policy_id;die;
        $premium_rate=$this->db->query("select policy_rate from master_policy_premium_permile where isactive=1 and  master_policy_id=".$policy_id)->row()->policy_rate;

       $amount = ($sum_insured / 1000) * $premium_rate;
        $final_rate = $amount  *  (100 + (int) getConfigValue('tax_percent')) / 100;
        $response['amount'] = number_format(ceil($final_rate));
        return $response;
    }
    function getpolicypremiumflat_gadgets($policy_id, $sum_insured)
    {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " isactive=1 and  master_policy_id = $policy_id AND sum_insured = $sum_insured");
//echo $this->db->last_query();die;
        if (!empty($rate)) {
            if ($rate[0]->premium_rate == '') {
                $premium = $rate[0]->premium_with_tax;
            } else {
                $premium = $rate[0]->premium_rate;
            }
            $rr['amount']=$premium;


            if ($rate[0]->is_taxable) {
                $rr['tax'] = $rr['amount'] * $this->config->item('tax') / 100;
            }
            return $rr;
        } else {
            $r = 0;
            return $r;
        }
        //  print_r($r);exit;

    }
    function getSumInsuredDataGadget($policy_id,$basis_type){
        $sumInsure=array();
        if($basis_type == 1){
            $sumInsure = $this->getSumInsureDataPolicy_Gadget($policy_id, "master_policy_premium");
        }
        $arrsum = array();
        foreach ($sumInsure as $data) {
            $arrsum[] = $data;

        }

        return $arrsum;
    }
    function getSumInsureDataPolicy_Gadget($policy_id,$table)
    {
        $wherecon = "master_policy_id = $policy_id AND isactive = 1";

        $data = $this->db->query("select distinct(sum_insured) from $table where $wherecon  order by sum_insured ASC ")->result_array();

        return $data;
    }

    function getSuminsuredType()
    {
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $product_id = encrypt_decrypt_password($this->input->post('product_id'), 'D');
        if(!empty($product_id)){
            
            $data = $this->db->select('master_suminsured_type.suminsured_type_id,master_suminsured_type.suminsured_type')
            ->from('master_suminsured_type')
            ->join("master_policy_si_type_mapping mpm", "master_suminsured_type.suminsured_type_id = mpm.suminsured_type_id", 'inner')
            ->join("master_policy mpo", "mpo.policy_id = mpm.master_policy_id", 'inner')
            ->where('mpo.plan_id = ' . $product_id)
            ->where('master_suminsured_type.isactive', 1)
            ->group_by("master_suminsured_type.suminsured_type_id")
            ->get()
            ->result_array();

        }else{
            $data = $this->db->select('suminsured_type_id,suminsured_type')
            ->from('master_suminsured_type')
            ->where('isactive', 1)
            ->get()
            ->result_array();

        }
        $existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();
        $si_type_id = '';
        if(!empty($existing_members)){
            $si_type_id = $existing_members[0]['si_type_id'];  
        }
        $existing_members_count = count($existing_members);
       

        echo json_encode(array("status_code" => "200", 'data' => $data, "existing_members_count" => $existing_members_count,'si_type_id'=>$si_type_id));
        exit;

    }

    function createInsuredtype()
    {

        $SumInsuredtype = $this->input->post('SumInsuredtype');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $product_id = encrypt_decrypt_password($this->input->post('product_id'), 'D');
        $deductable = $this->input->post('deductable');
        if(!empty($product_id)){

            $data = $this->db->select('master_suminsured_type.suminsured_type_id,master_suminsured_type.suminsured_type')
                ->from('master_suminsured_type')
                ->join("master_policy_si_type_mapping mpm", "master_suminsured_type.suminsured_type_id = mpm.suminsured_type_id", 'inner')
                ->join("master_policy mpo", "mpo.policy_id = mpm.master_policy_id", 'inner')
                ->where('mpo.plan_id = ' . $product_id)
                ->where('master_suminsured_type.isactive', 1)
                ->group_by("master_suminsured_type.suminsured_type_id")
                ->get()
                ->row_array();
            $SumInsuredtype = $data['suminsured_type_id'];

        }
        if (!empty($SumInsuredtype)) {


            $data_SumInsuredtype = array(
                "si_type_id" => $SumInsuredtype,
                "deductable" => $deductable
            );

            // update Data into member_ages
            $this->db->where('lead_id', $lead_id);
            $this->db->update("member_ages", $data_SumInsuredtype);


        }
        echo json_encode(array("status_code" => "200",'lead_id'=>$this->input->post('lead_id')));
        exit;
    }

    function getMasterDisease()
    {
        $data = $this->db->query('select cd_id,disease_name from chronic_disease_master where isactive = 1')->result_array();
        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;

    }

    function createDisease()
    {

        $disease_type = $this->input->post('disease_type');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $disease_check = $this->input->post('disease_check');
        if (!empty($disease_type)) {
            $query = $this->db->query("Delete  from  chronic_diease_member_data where lead_id ='" . $lead_id . "' ");
            foreach ($disease_type as $key1 => $disease_types) {

                $data_mb_diesease = array(
                    "cd_id" => $disease_types, "lead_id" => $lead_id,
                    "customer_id" => $customer_id, "disease_check" => $disease_check,
                    "created_date" => date("Y-m-d H:i:s")
                );

                // Insert Data into chronic_diease_member_data
                $this->db->insert("chronic_diease_member_data", $data_mb_diesease);


            }
        }
        echo json_encode(array("status_code" => "200"));
        exit;
    }

    function sendOtp()
    {

        $otp = $this->input->post('otp');
        $mobileno = $this->input->post('mobile_no');
        $query = $this->db->get_where('lead_details', array('mobile_no' => $mobileno))->row_array();

        // print_r($_POST);exit;
        $data = [];

        if (!empty($query)) {

            $this->db->update('short_urls', ['otp' => $otp], 'lead_id = ' . $query['lead_id']);

            $customer_query = $this->db->get_where('master_customer', array('lead_id' => $query['lead_id']))->row_array();

            $data = [

                'lead_id' => encrypt_decrypt_password($query['lead_id']),
                'customer_id' => encrypt_decrypt_password($customer_query['customer_id']),
                'trace_id' => encrypt_decrypt_password($query['trace_id'])
            ];
        } else {

            $this->db->insert('lead_details', ['mobile_no' => $mobileno, 'user_activity' => 1]);
            $lead_id = $this->db->insert_id();

            $this->db->insert('master_customer', ['mobile_no' => $mobileno, 'lead_id' => $lead_id]);
            $customer_id = $this->db->insert_id();

            $trace_id = $customer_id . time();
            $this->db->update('lead_details', ['trace_id' => $trace_id], "lead_id = $lead_id");

            $this->db->insert('short_urls', ['otp' => $otp, 'lead_id' => $lead_id]);

            $data = [

                'lead_id' => encrypt_decrypt_password($lead_id),
                'customer_id' => encrypt_decrypt_password($customer_id),
                'trace_id' => encrypt_decrypt_password($trace_id)
            ];
        }

        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;
    }

    function validateOtp()
    {
        // print_r($_POST);
        //$otp = $this->input->post('otp');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $query = $this->db->get_where('lead_details', array('lead_id' => $lead_id))->row_array();
        // print_r($query);
        // echo $this->db->last_query();
        echo json_encode(array("status_code" => "200", "data" => $query));
        exit;
    }

    public function invalidCheckOtpAttempt()
    {

        $lead_id = $this->request->post('lead_id');

        $otpVerificationData = $this->db->query("SELECT * from short_urls where lead_id = " . $lead_id)->row_array();
        // $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+1 hour'));
        $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'] . '+10 minutes'));
        // OTP COUNT ERROR
        if ($otpVerificationData['hits'] < 5) {
            $res = ["status" => 0, "message" => "Invalid OTP", "url" => ""];
        }
        if ($otpVerificationData['hits'] >= 5 && (date('Y-m-d H:i:s') < $timestamp)) {
            $res = ["status" => 0, "message" => "Maximum attempts limit reached, generate new OTP after an hour !!", "url" => ""];
        }
        if (date('Y-m-d H:i:s') >= $timestamp) {
            $updateInvalidCount = $this->db->query("UPDATE short_urls SET hits = 1 where lead_id = " . $lead_id);
            $res = ["status" => 0, "message" => "Invalid OTP", "url" => ""];
        }
        if ($otpVerificationData['hits'] < 5) {
            $updateInvalidCount = $this->db->query("UPDATE short_urls SET hits = hits + 1 where lead_id = " . $lead_id);
        }

        echo json_encode($res);
        exit;
    }

    public function get_partner_products()
    {

        $partner_id = $_POST['partner_id'];
        $partner_id = encrypt_decrypt_password($partner_id, 'D');

        $data = $this->db->select("mp.plan_name, mpo.plan_id, mf.master_policy_id, fc.member_type, mt.code, msi.suminsured_type")
            ->from("master_plan mp")
            ->join("master_policy mpo", "mpo.plan_id = mp.plan_id AND mpo.creditor_id = $partner_id", 'inner')
            ->join("master_policy_family_construct mf", "mf.master_policy_id = mpo.policy_id", 'inner')
            ->join("family_construct fc", "mf.member_type_id = fc.id", 'inner')
            ->join("master_policy_sub_type mt", "mt.policy_sub_type_id = mpo.policy_sub_type_id", 'inner')
            ->join("master_policy_si_type_mapping mpm", "mpm.master_policy_id = mpo.policy_id", 'inner')
            ->join("master_suminsured_type msi", "msi.suminsured_type_id = mpm.suminsured_type_id", 'inner')
            ->where('mp.creditor_id = ' . $partner_id)
            ->group_by("mp.plan_name, mpo.plan_id, mf.master_policy_id, fc.member_type, mt.code, msi.suminsured_type")
            ->order_by("mp.plan_name,mpo.policy_id")
            ->get()->result_array();
        //echo $this->db->last_query();exit;

        $final_data = [];

        if (!empty($data)) {

            foreach ($data as $data_value) {

                $final_data[$data_value['plan_name']][$data_value['master_policy_id']][$data_value['member_type']] = $data_value;
            }

            $result = ['status' => 1, 'data' => $final_data];
        } else {

            $result = ['status' => 0, 'data' => $final_data];
        }

        echo json_encode($result);
        exit;
    }

    public function get_product_details()
    {

        $plan_id = $_POST['plan_id'];
        $plan_id = encrypt_decrypt_password($plan_id, 'D');

        $data = $this->db->select("mp.plan_name, mp.gender, mpo.policy_id,mpo.max_insured_count, fc.id, fc.member_type, mt.code,mpo.child_count, mpp.si_premium_basis_id, mpo.is_optional, msr.dependent_on_policy_id")
            ->from("master_plan mp")
            ->join("master_policy mpo", "mpo.plan_id = mp.plan_id", 'inner')
            ->join("master_policy_family_construct mf", "mf.master_policy_id = mpo.policy_id", 'inner')
            ->join("family_construct fc", "mf.member_type_id = fc.id", 'inner')
            ->join("master_policy_sub_type mt", "mt.policy_sub_type_id = mpo.policy_sub_type_id", 'inner')
            ->join("master_policy_si_type_mapping mpm", "mpm.master_policy_id = mpo.policy_id", 'inner')
            ->join("master_policy_mandatory_if_not_selected_rules msr", "msr.master_policy_id = mpo.policy_id AND msr.isactive = 1", 'left')
            ->join("master_policy_premium_basis_mapping mpp", "mpp.master_policy_id = mpo.policy_id", 'inner')
            ->where('mp.plan_id = ' . $plan_id)
            ->group_by("mp.plan_name, mpo.plan_id, mf.master_policy_id, fc.member_type, mt.code")
            ->order_by("mp.plan_name,mpo.policy_id")
            ->get()->result_array();

        //echo $this->db->last_query();exit;
        $query = $result = [];
        foreach ($data as $data_value) {

            //$premium_basis_id[$data_value['si_premium_basis_id']] = $data_value['si_premium_basis_id'];

            if ($data_value['si_premium_basis_id'] == 5) {

                $query[$data_value['member_type']][$data_value['policy_id']] = "master_policy_premium_permile";
            } else if ($data_value['si_premium_basis_id'] == 7) {

                $query[$data_value['member_type']][$data_value['policy_id']] = "master_per_day_tenure_premiums";
            } else {

                $query[$data_value['member_type']][$data_value['policy_id']] = "master_policy_premium";
            }

            $result['plan_name'] = $data_value['plan_name'];
            $result['gender'] = $data_value['gender'];
            $result['max_insured_count'] = $data_value['max_insured_count'];
            $result['plan_id'] = encrypt_decrypt_password($plan_id, 'E');
            $result[$data_value['policy_id']]['code'] = $data_value['code'];
            $result[$data_value['policy_id']]['is_dependent'] = $data_value['dependent_on_policy_id'];
            $result[$data_value['policy_id']]['is_optional'] = $data_value['is_optional'];
            $result[$data_value['policy_id']]['family_construct'][$data_value['id']] = $data_value['member_type'];
            $result['family_construct'][$data_value['id']] = $data_value['member_type'];
            $result['child_count'] = $data_value['child_count'];
        }

        $tenure = $deductable = [];
        foreach ($query as $member_type => $policy_table_arr) {

            foreach ($policy_table_arr as $master_policy_id => $table) {

                if (!isset($result[$master_policy_id]['sum_insured'])) {

                    $age_col = ', min_age, max_age';

                    if ($table == 'master_policy_premium') {

                        $sql = "SELECT distinct sum_insured, deductable $age_col FROM $table WHERE master_policy_id = $master_policy_id and isactive = 1 ORDER BY sum_insured";
                    } else {

                        if ($table == 'master_per_day_tenure_premiums') {

                            $age_col = '';
                        }

                        $sql = "SELECT distinct sum_insured, tenure $age_col FROM $table WHERE master_policy_id = $master_policy_id and isactive = 1 ORDER BY sum_insured";
                    }

                    $sum_insured_data = $this->db->query($sql)->result_array();

                    foreach ($sum_insured_data as $sum_insured) {

                        if (isset($sum_insured['tenure']) && !empty($sum_insured['tenure'])) {

                            $tenure[$sum_insured['tenure']] = $sum_insured['tenure'];
                        }

                        if (isset($sum_insured['deductable']) && !empty($sum_insured['deductable'])) {

                            $deductable[$sum_insured['deductable']] = $sum_insured['deductable'];
                        }
                        $result[$master_policy_id]['sum_insured'][$sum_insured['sum_insured']] = $sum_insured['sum_insured'];
                        $result[$master_policy_id]['min_age'] = $sum_insured['min_age'];
                        $result[$master_policy_id]['max_age'] = $sum_insured['max_age'];
                    }
                }
            }
        }

        $result['deductable'] = $deductable;
        $result['tenure'] = $tenure;
        //$result['deductable'] = $deductable;

        if (!empty($result)) {

            $data = ['status' => 1, 'data' => $result];
        } else {

            $data = ['status' => 0, 'data' => []];
        }

        echo json_encode($data);
        exit;
    }

    public function saveCustomerDetails()
    {

        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');

        $data = $this->db->select('mc.lead_id, mc.customer_id')
            ->from('lead_details ld')
            ->join('master_customer mc', 'mc.lead_id = ld.lead_id', 'inner')
            ->where(['ld.lead_id' => $lead_id, 'mc.customer_id' => $customer_id])->get()->result_array();

        if (!empty($data)) {

            $this->db->update('lead_details', ['plan_id' => $plan_id], ['lead_id' => $lead_id]);
            $this->db->update(
                'master_customer',
                [
                    'salutation' => $_POST['salutation'],
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'full_name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
                    'gender' => $_POST['gender'],
                    'dob' => $_POST['proposer_dob'],
                    'email_id' => $_POST['email'],
                    'mobile_no' => $_POST['mobile_no'],
                    'address_line1' => $_POST['address'],
                    'pincode' => $_POST['pincode'],
                    'city' => $_POST['city'],
                    'state' => $_POST['state']
                ],
                [
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id
                ]
            );

            $proposal_data = $this->db->select('proposal_details_id')->from('proposal_details')->where(['lead_id' => $lead_id])->get()->result_array();

            $proposal_details_id = 0;

            if (empty($proposal_data)) {

                $this->db->insert('proposal_details',
                    [
                        'plan_id' => $plan_id,
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'customer_id' => $customer_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_on' => date('Y-m-d H:i:s'),
                        'created_by' => 0
                    ]
                );

                $proposal_details_id = $this->db->insert_id();
            } else {

                $this->db->update(
                    'proposal_details',
                    [
                        'plan_id' => $plan_id,
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'customer_id' => $customer_id,
                        'updated_on' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'proposal_details_id' => $proposal_data[0]['proposal_details_id']
                    ]
                );

                $proposal_details_id = $proposal_data[0]['proposal_details_id'];
            }

            $quote_ids_arr = $this->db->select('master_quote_id')->from('master_quotes')->where(['lead_id', $lead_id])->get()->result_array();

            $quote_ids = [];

            $quote_ids = array_map(function ($arr) {

                return $arr['master_quote_id'];
            }, $quote_ids_arr);

            $response_data = [
                'proposal_details_id' => $proposal_details_id,
                'quote_ids' => !empty($quote_ids) ? join(',', $quote_ids) : ''
            ];

            $result = ['status' => 200, 'Msg' => "Record inserted successfully", 'data' => $response_data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
        exit;
    }

    function get_family_construct_details()
    {

        $lead_id = encrypt_decrypt_password($_POST['lead_id'], 'D');

        $family_construct =
            $this->db->select('family_construct')
                ->from('master_quotes')
                ->where('lead_id', $lead_id)
                ->get()->row_array();

        $total = explode('-', $family_construct['family_construct']);
        $data['family_construct'] = $total[0] + $total[1];

        $data['self_details'] =
            $this->db->select('*')
                ->from('master_customer')
                ->where('lead_id', $lead_id)
                ->get()->row_array();

        $data['all_family_construct'] =
            $this->db->select('*')
                ->from('family_construct')
                ->where('isactive', 1)
                ->get()->result_array();

        // print_r($data);
        // exit;
        echo json_encode($data);
        exit;

    }
    public function Creatememberinsure_data()
    {
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $rel_name = $this->input->post('rel_name');
        $partner_id = $this->input->post('partner_id');
        $si_type_id = $this->input->post('si_type_id');
        $deductable = $this->db->query("select * from member_ages where lead_id = '$lead_id' and deductable !='' ")->row_array();
        $deductable = $this->db->query("select deductable from member_ages where lead_id = '$lead_id' and deductable !='' ")->row_array();

        $detail_mem= $this->db->query("delete from member_ages where lead_id = '$lead_id'  ");


        foreach ($rel_name as $key => $value) {

            if($value['datebirth']!=''){

               $mem_rel = $value['rel'];
                $ages = $this->cal_age(date('Y-m-d', strtotime($value['datebirth'])));


                if($value['datebirth']!=''){
                    $mem_rel = $value['rel'];
                    $ages = $this->cal_age(date('Y-m-d', strtotime($value['datebirth'])));

                        if($value['rel']==1 && !empty($customer_id)){
                            
                            $this->db->where('customer_id', $customer_id);
                            $this->db->update("master_customer", ['dob'=>$value['datebirth']]);
                            
                        }
                        $data_member_ages = array(
                            'member_type' => $value['rel'],
                            'member_age' => $ages['age'],
                            'lead_id' => $lead_id,
                            'customer_id' => $customer_id,
                            'si_type_id' => $si_type_id,
                            'deductable'=>$deductable['deductable'],
                            'dob'=>$value['datebirth'],
                            'member_name' => $key





                        );


                        $this->db->insert('member_ages', $data_member_ages);
                     //   print_r($this->db->last_query());





                }



            }



        }
        echo 1;
    }

    public function Createinsure_data()
    {
        $arr = [];
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $display = $this->input->post('display');
        $partner_id = $this->input->post('partner_id');
        $si_type_id = $this->input->post('si_type_id');
        if ($partner_id) {
            $deductable = $this->db->query("select deductable from member_ages where lead_id = '$lead_id'")->row_array();
            $this->db->query("delete from member_ages where lead_id = '$lead_id'");
            $deductable = $deductable['deductable'];

        }

        $query = $this->db->query("delete from proposal_policy_member_details where lead_id = '$lead_id'");
        
        //fetching policy details which member selected
        $policy_detail_data = $this->db->query("select GROUP_CONCAT(mpfc.member_type_id) as member_type_id,mpfc.master_policy_id,pmpd.cover,pmpd.premium,pmpd.total_premium,pmpd.plan_id from policy_member_plan_details as pmpd ,master_policy_family_construct as mpfc where pmpd.policy_id = mpfc.master_policy_id and  pmpd.lead_id = '$lead_id' group by mpfc.master_policy_id")->result_array();
        $validation['status'] = 'success';
        $validationNew=array();
        $data_arr = [];
        $r=1;
//var_dump($display);die;
        foreach ($display as $key => $value) {

            //echo json_encode($value);exit;
            $res = $this->db->get_where("family_construct", array("id" => $value['rel']))->row_array()['member_type'];
            if ($value['rel'] == '' || $value['gender'] == '' || $value['marital_status'] == '' || $value['first_name'] == '' || $value['dob'] == '') {

                $retObj = ["status" => "error",
                    "message" => $res . " details can not be blank!"];
                echo json_encode($retObj);
                exit;
            }

            $ages = $this->cal_age(date('Y-m-d', strtotime($value['dob'])));

            $age = $ages['age'] ;
            $age_type = $ages['age_type'];
            //  var_dump(array($age_type,$age,$_POST['policy_id'],$value['rel']));
            //update member age
            $q = "update member_ages SET member_age = '" . $age . "' where member_type = '" . $value['rel'] . "' AND lead_id ='" . $lead_id . "'";
            $this->db->query($q);
            if ($partner_id) {
                $rel = $value['rel'];
//                var_dump($policy_detail_data);
                foreach($policy_detail_data as $policy_det) {
                    //echo 1;
                    $validation = $this->validate_member_age($age_type, $age, $policy_det['master_policy_id'], $rel, $res);
                    $validationNew[]=$validation;
                    //  var_dump($validation);
                }
                if($value['rel']==5 || $value['rel']==6){
                    $rel_name = 'Kid'.$r;
                    $r = $r+1;
                }else{
                   $rel_name =  $value['rel'];
                }

                $data_member_ages = array(
                    'member_type' => $value['rel'],
                    'member_age' => $age,
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'si_type_id' => $si_type_id,
                    'deductable' => $deductable,
                    'dob'       =>date('d-m-Y', strtotime($value['dob'])),
                    'member_name'=>$rel_name


                );


                $this->db->insert('member_ages', $data_member_ages);
                $value['member_ages_id'] = $this->db->insert_id();


            } else {
                $validation = $this->validate_member_age($age_type, $age, $_POST['policy_id'], $value['rel'], $res);


            }
            if ($partner_id) {
                foreach ($validationNew as $valid){
                   if($valid['status']== 'error'){
                       echo json_encode($valid);
                       exit;
                   }
                }
            }else{
                if ($validation['status'] == 'error') {
                    echo json_encode($validation);
                    exit;
                }
            }
            // print_r($validation);

            $data = array(
                'relation_with_proposal' => $value['rel'],
                'policy_member_gender' => $value['gender'],
                'policy_member_first_name' => $value['first_name'],
                'policy_member_last_name' => $value['last_name'],

                'policy_member_salutation' => $value['salutation'],

                'policy_member_dob' => date('Y-m-d', strtotime($value['dob'])),
                'policy_member_marital_status' => $value['marital_status'],
                'policy_member_age' => $age,
                'policy_member_age_in_months' => $age_type,
                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'member_ages_id' => $value['member_ages_id'],

            );

            //print_r($data);

      $result=      $this->db->insert('proposal_policy_member_details', $data);
            // print_r($this->db->last_query());die;
            insert_application_log($lead_id, "proposal_insured_member_submit", json_encode($data), json_encode($result), 0);
        }
//exit;


        $retObj = ["status" => "success",
            "message" => "Data inserted successfully !"];
        echo json_encode($retObj);


        // print_r($display);die;
    }


    public function getAllpremium_det_single($lead_id = 0, $customer_id = 0, $policy_id = 0, $cover = 0,$policy_sub_type_id='')
    {
        $quote_data = $this->db->query("select cover from quote_member_plan_details where  lead_id=".$lead_id)->row_array();
        $plan_data = $this->db->query("select plan_id from master_policy where  policy_id=".$policy_id)->row_array();
        if(!empty($plan_data)){
            $poli_data = $this->db->query("select policy_id from master_policy where  plan_id=".$plan_data['plan_id'])->result_array();
            if(!empty($quote_data) && !empty($quote_data['cover']) && !empty($poli_data) && count($poli_data)<2 ){
                $cover = $quote_data['cover'];
            }
        }


        $data['member_ages'] = $this->db->select('fc.id,ma.id as member_age_id,fc.is_adult,fc.member_type, ma.member_age,ma.deductable, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();


        $arr = ['adult_count' => 0, 'child_count' => 0];
        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $policy_id)
            ->get()
            ->row_array();
        $consider_adult = $policy_det['is_consider_adult'];
        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        $deductable='';
        foreach ($data['member_ages'] as $key => $value) {
             $age_type = $this->db->select('policy_member_age_in_months,policy_member_dob')
            ->from('proposal_policy_member_details')
            
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id,'member_ages_id'=>$value['member_age_id']])
            //->where($member_where)
            ->get()
            ->row_array();


            $deductable = $value['deductable'];
            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            $get_age = $value['member_age'];
            if(!empty($age_type['policy_member_age_in_months']) && $age_type['policy_member_age_in_months']=='days'){
                $get_age = $this->cal_age($age_type['policy_member_dob']);
                if($get_age['age_type']=='days'){
                    $get_age='';
                }         
            }else{
                $get_age = $value['member_age'];
            }

            if ($get_age >= '18' && $consider_adult == 1){
                $value['is_adult'] = 'Y';
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);


        $sum_insured = $cover;


        //$policy->basis_id = 4;
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];

        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' and isactive =1")->row_array();

        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;
            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);
            $premium['amount']= $premium['amount'];
            $premium['group_code']= $premium['group_code'];

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;
            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);
            $premium['amount']= $premium['amount'];
            $premium['group_code']= $premium['group_code'];

        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);
            $premium['amount']= $premium['amount'];
            $premium['group_code']= $premium['group_code'];


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;


            if ($arr['is_adult'] == 'Y' && $child == 0) {
                foreach ($arr_age as $member_age_cal) {

                    $age = $member_age_cal;
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }else if ($policy['basis_id'] == 5) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );



            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $max_age, "tenure" => $this->tenure]));
            //echo $this->db->last_query();
             //  var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
            $premium['group_code']= $result['group_code'];
        }else if ($policy['basis_id'] == 6) {
            if($deductable<=0){
                 $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adult." and child_count=".$child)->row()->deductable;
            }
           
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => '',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1,
                'age'=>$max_age
            );


            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => $deductable]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
            $premium['group_code']= $result['group_code'];
            $premium['member_code']= $result['member_code'];
            $premium['deductable'] = $deductable;
        }else if ($policy['basis_id'] ==7) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
            $premium['group_code']= $result['group_code'];
        }
        return $premium;

    }

    public function getAllpremium($lead_id = 0, $customer_id = 0, $policy_id = 0, $cover = 0,$policy_sub_type_id)
    {
        $quote_data = $this->db->query("select cover from quote_member_plan_details where  lead_id=".$lead_id)->row_array();

        $plan_data = $this->db->query("select plan_id from master_policy where  policy_id=".$policy_id)->row_array();
        if(!empty($plan_data)){
            $poli_data = $this->db->query("select policy_id from master_policy where  plan_id=".$plan_data['plan_id'])->result_array();
            if(!empty($quote_data) && !empty($quote_data['cover']) && !empty($poli_data) && count($poli_data)<2 ){
                $cover = $quote_data['cover'];
            }
        }
        /*if ($this->input->post('policy_id') != '') {

            $lead_id = $this->input->post('lead_id');
            $customer_id = $this->input->post('customer_id');


            $lead_id = encrypt_decrypt_password($lead_id, 'D');
            $customer_id = encrypt_decrypt_password($customer_id, 'D');
            $policy_id = $this->input->post('policy_id');
            $cover = $this->input->post('cover');
            if ($policy_id) {

                $policy_id = encrypt_decrypt_password($policy_id, 'D');
            }
        }*/

        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id,ma.deductable')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();

//print_r($data);die;
        $arr = ['adult_count' => 0, 'child_count' => 0];
        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $policy_id)
            ->get()
            ->row_array();

        $consider_adult = $policy_det['is_consider_adult'];
        $min_age = 0;
        $max_age = 0;
        $deductable = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {
            $deductable = $value['deductable'];
            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }

            if ($value['member_age'] >= '18'  && $consider_adult == 1 && strpos($value['member_age'],'months') === false){
                $value['is_adult'] = 'Y';
            }
            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);


        // print_r($plan_details);die;
        $sum_insured = $cover;


        //$policy->basis_id = 4;
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];

        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' ")->row_array();
      //  print_r($policy);
        //echo $policy['basis_id'];
        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;
            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);
        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;
            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);

        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;


            if ($arr['is_adult'] == 'Y' && $child == 0) {
                foreach ($arr_age as $member_age_cal) {

                    $age = $member_age_cal;
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }else if ($policy['basis_id'] == 5) {
            $this->age = $max_age;
            //  echo $max_age;exit;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $max_age, "tenure" => $this->tenure]));
            //echo $this->db->last_query();
            // var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] == 6) {
           
            if($deductable<=0){
                 $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adult." and child_count=".$child)->row()->deductable;
            }
           
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => '',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1,
                'age'=>$max_age
            );


            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => $deductable]));
         
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] ==7) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }
      //  var_dump($premium);exit;
       // echo $premium;
      //  echo $this->input->post('policy_id')."pooja";exit;
        if ($this->input->post('policy_id') != '') {
           // echo json_encode($premium);
            return $premium;
        } else {
            if (!empty($premium)) {
                return $premium;
            }

        }

    }

    public function getPolicywiseSumInsured_diff($policy_id, $min, $max)
    {
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where isactive=1 and master_policy_id = '$policy_id' ")->row_array();

        if ($policy['basis_id'] == 1) {
            // echo json_encode(111111);exit;
            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy_compare($policy['policy_id'], "master_policy_premium", $min, $max);


        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy_compare($policy['policy_id'], "master_policy_premium", $min, $max);

        } else if ($policy['basis_id'] == 3) {


            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy_compare($policy['policy_id'], "master_policy_premium", $min, $max);


        } else if ($policy['basis_id'] == 4) {

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium", $min, $max);
        }else if ($policy['basis_id'] == 5) {
            $whereN['master_policy_id']=$policy_id;
            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$whereN);
            // echo $this->db->last_query();
            //     var_dump($sumInsure);exit;
        }else if($policy['basis_id'] == 6){
            $sumInsure = $this->customerapimodel->getSumInsureDataP($policy['policy_id']);
        }else if($policy['basis_id'] == 7){
            $sumInsure=$this->customerapimodel->getSumInsureData($policy['policy_id'], 'master_per_day_tenure_premiums');
        }
        $arrsum = array();
        foreach ($sumInsure as $data) {
            $arrsum[] = $data;

        }

        return $arrsum;

    }

    public function getPolicywiseSumInsured($policy_id,$adult=null,$child=null,$max_age=null)
    {
        $arrsum = array();
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where isactive=1 and master_policy_id = '$policy_id' ")->row_array();
//print_r($policy);
//exit;
        $where=array();
        if(!is_null($adult)){
            $where['adult_count >=']=$adult;
        }
        if(!is_null($child)){
            $where['child_count >=']=$child;
        }
        if ($policy['basis_id'] == 1) {
            // echo json_encode(111111);exit;
            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);


        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);
            //echo $this->db->last_query();
        } else if ($policy['basis_id'] == 3) {


            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);


        } else if ($policy['basis_id'] == 4) {

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",array());
          //echo $this->db->last_query();
            //var_dump($sumInsure);exit;
        }else if ($policy['basis_id'] == 5) {
            $whereN['master_policy_id']=$policy_id;
            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$whereN);
           // echo $this->db->last_query();
       //     var_dump($sumInsure);exit;
        }else if($policy['basis_id'] == 6){
            $sumInsure = $this->customerapimodel->getSumInsureDataP($policy['policy_id']);
        }else if($policy['basis_id'] == 7){
            //$whereN['master_policy_id']=$policy_id;
            $sumInsure=$this->customerapimodel->getSumInsureDataP($policy['policy_id'], 'master_per_day_tenure_premiums');
        }

        foreach ($sumInsure as $data) {
            $arrsum[] = $data;

        }

        return $arrsum;

    }

    public function getAllpremium_det($lead_id, $customer_id, $policy_id, $cover, $max_age, $is_adult, $arr_age, $adult, $child, $partner_id, $whereArray, $min_premium, $max_premium,$policy_sub_type_id='',$deductable='')
    {

//print_r($max_age);die;
        $sum_insured = $cover;

        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where isactive=1 and  master_policy_id = '$policy_id'  and isactive=1")->row_array();

        //  print_r($policy); exit;
        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;

            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured, $whereArray, $min_premium, $max_premium, $partner_id);

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;

            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child, $partner_id, $whereArray, $min_premium, $max_premium, $partner_id);

        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age, $whereArray, $min_premium, $max_premium, $partner_id);


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;


            if ($is_adult == 'Y' && $child == 0) {
                foreach ($arr_age as $member_age_cal) {

                    $age = $member_age_cal;
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age, $whereArray, $min_premium, $max_premium);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }else if ($policy['basis_id'] == 5) {
            $this->age = $max_age;
          //  echo $max_age;exit;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $max_age, "tenure" => $this->tenure]));
           //echo $this->db->last_query(); exit;
           // var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] == 6) {
            if(empty($deductable)){
                $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adult." and child_count=".$child)->row()->deductable;
            }
            
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => '',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1,
                'age'=>$max_age
            );


            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => $deductable]));
            //echo $this->db->last_query();
              // var_dump($result);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] ==7) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }
        return $premium;
    }

    public function cal_age($dob)
    {
        $arr = [];
        $today = date("Y-m-d", strtotime(date("Y-m-d") . "-1 days"));
        $diff = date_diff(date_create(date('d-m-Y', strtotime($dob))), date_create($today));
        $age = $diff->format('%y');
        $age_type = 'years';
        if ($age == 0) {
            $age = $diff->format('%a');
            $age_type = 'days';
        }
        //print_R($age);die;
        $arr = ['age' => $age, 'age_type' => $age_type];
        return $arr;
    }
    public function getMember_insure()
    {//echo 234;die;
        //print_r($_POST);
        $lead_id = $this->input->post('lead_id');
        $partner_id = $this->input->post('partner_id');
        $customer_id = $this->input->post('customer_id');
        $product_id = $this->input->post('product_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $product_id = encrypt_decrypt_password($product_id, 'D');
        $gender_con='';
        $customer_details = $this->customerapimodel->getCustomerDetailByid($customer_id);
        $cond = '';
        $lead_details = $this->db->query("select is_mailer_api from lead_details where lead_id = '$lead_id'")->row_array();    

        // print_r($customer_details);exit;
        if($customer_details['is_proposer_insured']==0 && $customer_details['address_line1']!=''){
            $cond = ' AND (fc.member_type!="Self" OR fc.member_type IS NULL)';
        }
        if($product_id){
            $plan_details = $this->apimodel->getProductDetailsAll_diff($product_id);

            $gender = $plan_details[0]['gender'];

            switch ($gender) {
                case 'F':
                    $gender_con = "AND (fc.gender is NULL OR fc.gender='Female')";
                    break;
                case 'M':
                    $gender_con = "AND (fc.gender is NULL OR fc.gender='Male')";
                    break;

            }
        }
        $arr = array();
        $data = [];
        $details_arr = [];
        $get_single_mem = array();
        $old_kid_cnt = 0;
        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();

        if (!empty($check_data)) {

            $members_data = $this->db->query("SELECT pd.*,ma.id,cs.is_adult as member_ages_id ,fc.*,ma.lead_id,ma.customer_id,ma.dob from member_ages as ma LEFT join proposal_policy_member_details as pd ON ma.id = pd.member_ages_id left join family_construct as fc on ma.member_type = fc.id left join Create_quote_member_singlelink as cs on  cs.member_type = fc.member_type   where ma.lead_id = '$lead_id' GROUP BY member_id ")->result_array();

            $get_child = $this->db->query("select * from family_construct fc where fc.is_adult = 'N' $gender_con ")->result_array();
            $arr_kid = [];
            foreach ($get_child as $key1 => $child_det) {

                $arr_kid[$key1]['member']['construct'] = $child_det['member_type'];

                $arr_kid[$key1]['member']['member_id'] = $child_det['id'];

            }
            //print_r($this->db->last_query());die;
            foreach ($members_data as $val_p) {

                $arr['member_type'] = $val_p['member_type'];
                $arr['member_ages_id'] = $val_p['member_ages_id'];
                $arr['id'] = $val_p['id'];
                if($val_p['is_adult']!='Y'){
                    $arr['id'] = $arr_kid;
                }
                $arr['policy_member_first_name'] = $val_p['policy_member_first_name'];
                $arr['policy_member_last_name'] = $val_p['policy_member_last_name'];
                $arr['policy_member_marital_status'] = $val_p['policy_member_marital_status'];
                $arr['policy_member_salutation'] = $val_p['policy_member_salutation'];
                $arr['is_adult'] = $val_p['is_adult'];
                $arr['member_age'] = $val_p['policy_member_age'];
                $year_month = strtolower(trim($val_p['policy_member_age_in_months']));
                $year_month = ($year_month=='years')?'year':$year_month;

                $arr['member_age_month'] = $val_p['policy_member_age_in_months'];

                $arr['policy_member_dob'] = !empty($val_p['policy_member_dob'])?date('d-m-Y', strtotime($val_p['policy_member_dob'])):$val_p['dob'];
                if($val_p['policy_member_gender'] !=''){
                    $arr['policy_member_gender'] = $val_p['policy_member_gender'];
                }else{
                    $arr['policy_member_gender'] = $val_p['gender'];
                }

                array_push($get_single_mem,$arr['member_type']);
                array_push($data, $arr);
                if($val_p['is_adult'] == 'N')
                {
                    $old_kid_cnt++;
                }


            }

            $get_mem = $this->db->query("select * from Create_quote_member_singlelink where lead_id = '$lead_id'")->result_array();

            foreach($get_mem as $mem_det)
            {
                if(!in_array($mem_det['member_type'],$get_single_mem))
                {
                    if($mem_det['is_adult'] == 'Y')
                    {
                        $m_type = $mem_det['member_type'];
                        $members = $this->db->query("select ma.dob,ma.id as member_ages_id,ma.is_adult,ma.member_type,fc.gender,fc.id,ma.lead_id,ma.customer_id from Create_quote_member_singlelink as ma left join family_construct as fc on  ma.member_type = fc.member_type where ma.lead_id = '$lead_id' and ma.member_type = '$m_type'$gender_con")->result_array();

                        foreach ($members as $value) {

                            if ($value['is_adult'] == 'Y') {
                                $arr['id'] = $value['id'];
                            }
                            $arr['member_type'] = $value['member_type'];
                            $arr['member_ages_id'] = $value['member_ages_id'];
                            $arr['is_adult'] = $value['is_adult'];
                            $arr['policy_member_first_name'] = '';
                            $arr['policy_member_last_name'] = '';
                            $arr['policy_member_dob'] =  $value['dob'];
                            $arr['policy_member_salutation'] = '';

                            if ($value['gender'] != '') {
                                $arr['policy_member_gender'] = $value['gender'];
                            } else {
                                $arr['policy_member_gender'] = '';
                            }
                            array_push($data, $arr);
                        }

                    }
                    else{

                        array_push($details_arr,$mem_det);

                    }

                }
                //   array_push($get_single_mem,$mem_det);
            }
            $arr_cnt = count($details_arr);
            //kids
            for($i =$old_kid_cnt;$i<$arr_cnt;$i++ ){

                $kids = "kid".$i+1;
                $members_det = $this->db->query("select * from  family_construct as fc where  fc.is_adult = 'N' ")->result_array();
                foreach ($members_det as $key2 =>$value) {
                    $arr_kid[$key2]['member']['construct'] = $value['member_type'];
                    $arr_kid[$key2]['member']['member_id'] = $value['id'];

                }
                $arr['member_type'] = $kids;
                $arr['id'] = $arr_kid;
                $arr['is_adult'] ='N';
                $arr['policy_member_first_name'] = '';
                $arr['policy_member_last_name'] = '';
                $arr['policy_member_dob'] =  $value['dob'];
                $arr['policy_member_salutation'] = '';
                array_push($data, $arr);


                //print_r($data);
            }
            echo json_encode($data);
            exit;

        } else {

            if ($partner_id) {

                $get_child = $this->db->query("select * from family_construct fc where fc.is_adult = 'N' $gender_con ")->result_array();

                $arr_kid = [];
                foreach ($get_child as $key1 => $child_det) {

                    $arr_kid[$key1]['member']['construct'] = $child_det['member_type'];

                    $arr_kid[$key1]['member']['member_id'] = $child_det['id'];

                }

                $members = $this->db->query("select ma.id as member_ages_id,ma.is_adult,ma.member_name as member_type,fc.gender,fc.id,ma.lead_id,ma.customer_id,ma.dob from Create_quote_member_singlelink as ma left join family_construct as fc on  ma.member_type = fc.member_type where ma.lead_id = '$lead_id' $cond")->result_array();
                $ids =[0];
                foreach ($members as $value) {

                    if ($value['is_adult'] == 'Y') {
                        $arr['id'] = $value['id'];
                        if($plan_details[0]['payment_first']==1){

                            $member_age = $this->db->query("select id,member_age from member_ages where member_type='". $value['id']."' and lead_id = '$lead_id' and id not in(".implode(',',$ids).")")->row();

                            $ids[]=$member_age->id;
                        }
                        if(!empty($member_age)){
                            $arr['member_age'] = preg_replace("/[^0-9]/", '',$member_age->member_age );
                            if($arr['member_age']<18){
                                $value['is_adult'] = 'N';
                            }

                            $year_month = strtolower(trim(preg_replace('/[0-9]+/', '', $member_age->member_age)));
                            $year_month = ($year_month=='years')?'year':$year_month;

                            $arr['member_age_month'] = empty($year_month)?'year':$year_month;
                        }
                    } else {
                        $arr['id'] = $arr_kid;

                    }
                    $arr['member_type'] = $value['member_type'];
                    $arr['member_ages_id'] = $value['member_ages_id'];
                    $arr['is_adult'] = $value['is_adult'];
                    $arr['policy_member_first_name'] = '';
                    $arr['policy_member_last_name'] = '';
                    $arr['policy_member_dob'] = $value['dob'];
                    $arr['policy_member_salutation'] = '';

                    if($value['gender'] !=''){
                        $arr['policy_member_gender'] =$value['gender'];
                    }else{
                        $arr['policy_member_gender'] = '';
                    }

                    if ($value['id'] == 1) {
                        $query = $this->db->query("select * from master_customer where lead_id = '$lead_id'")->row_array();
                        $arr['policy_member_first_name'] = $query['first_name'];
                        $arr['policy_member_last_name'] = $query['last_name'];
                        $arr['policy_member_dob'] = !empty($query['dob'])?date('d-m-Y', strtotime($query['dob'])):'';
                        $arr['policy_member_gender'] = $query['gender'];
                        $arr['policy_member_salutation'] = $query['salutation'];


                    }
                    array_push($data, $arr);
//print_r($data);die;
                }


            } else {

                $members = $this->db->query("select ma.dob,ma.id as member_ages_id,fc.gender,fc.is_adult,fc.member_type,fc.id,ma.lead_id,ma.customer_id,ma.member_age from member_ages as ma join family_construct as fc where ma.member_type = fc.id and ma.lead_id = '$lead_id' $cond")->result_array();
                foreach ($members as $val) {
                    $arr['member_type'] = $val['member_type'];
                    $arr['member_ages_id'] = $val['member_ages_id'];
                    $arr['member_age'] = $val['member_age'];
                    $arr['id'] = $val['id'];
                    $arr['is_adult'] = $val['is_adult'];
                    $arr['policy_member_first_name'] = '';
                    $arr['policy_member_last_name'] = '';
                    $arr['policy_member_dob'] = $val['dob'];
                    $arr['policy_member_salutation'] = '';
                    if($val['gender'] !=''){
                        $arr['policy_member_gender'] =$val['gender'];
                    }else{
                        $arr['policy_member_gender'] = '';
                    }
                    if ($val['id'] == 1) {
                        $query = $this->db->query("select * from master_customer where lead_id = '$lead_id'")->row_array();
                        $arr['policy_member_first_name'] = $query['first_name'];
                        $arr['policy_member_last_name'] = $query['last_name'];
                        $arr['policy_member_dob'] = !empty($query['dob'])?date('d-m-Y', strtotime($query['dob'])):'';
                        $arr['policy_member_gender'] = $query['gender'];
                        $arr['policy_member_salutation'] = $query['salutation'];

                    }
                    array_push($data, $arr);


                }
            }
//             print_R($data);die;
            echo json_encode($data);
            exit;
            // print_r($data);
            //  print_r($abc);
            // echo json_encode($arr);exit;
            // echo json_encode($members);exit;
        }
        //     echo json_encode($members);exit;


    }

    public function getMember_insure25072023()
    {
        $lead_id = $this->input->post('lead_id');
        $partner_id = $this->input->post('partner_id');
        $customer_id = $this->input->post('customer_id');
        $product_id = $this->input->post('product_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $product_id = encrypt_decrypt_password($product_id, 'D');
        $gender_con='';
        $customer_details = $this->customerapimodel->getCustomerDetailByid($customer_id);
        $cond = '';
       // print_r($customer_details);exit;
        if($customer_details['is_proposer_insured']==0 && $customer_details['address_line1']!=''){
            $cond = ' AND (fc.member_type!="Self" OR fc.member_type IS NULL)';
        }
        if($product_id){
            $plan_details = $this->apimodel->getProductDetailsAll_diff($product_id);

            $gender = $plan_details[0]['gender'];
            switch ($gender) {
                case 'F':
                    $gender_con = "AND (fc.gender is NULL OR fc.gender='Female')";
                    break;
                case 'M':
                    $gender_con = "AND (fc.gender is NULL OR fc.gender='Male')";
                    break;

            }
        }
        $arr = array();
        $data = [];
        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();
        if (!empty($check_data)) {

            $members_data = $this->db->query("SELECT pd.*,ma.id as member_ages_id ,fc.*,ma.lead_id,ma.customer_id from member_ages as ma LEFT join proposal_policy_member_details as pd ON ma.id = pd.member_ages_id left join family_construct as fc on ma.member_type = fc.id  where ma.lead_id = '$lead_id'")->result_array();
            //print_r($this->db->last_query());die;
            foreach ($members_data as $val_p) {
                $arr['member_type'] = $val_p['member_type'];
                $arr['member_ages_id'] = $val_p['member_ages_id'];
                $arr['id'] = $val_p['id'];
                $arr['policy_member_first_name'] = $val_p['policy_member_first_name'];
                $arr['policy_member_last_name'] = $val_p['policy_member_last_name'];
                $arr['policy_member_marital_status'] = $val_p['policy_member_marital_status'];

                $arr['policy_member_salutation'] = $val_p['policy_member_salutation'];
                $arr['is_adult'] = $val_p['is_adult'];
                $arr['member_age'] = $val_p['policy_member_age'];
                $year_month = strtolower(trim($val_p['policy_member_age_in_months']));
                $year_month = ($year_month=='years')?'year':$year_month;
                            
                $arr['member_age_month'] = $val_p['policy_member_age_in_months'];


                $arr['policy_member_dob'] = $val_p['policy_member_dob'];
                if($val_p['policy_member_gender'] !=''){
                    $arr['policy_member_gender'] = $val_p['policy_member_gender'];
                }else{
                    $arr['policy_member_gender'] = $val_p['gender'];
                }

                array_push($data, $arr);
            }
            echo json_encode($data);
            exit;

        } else {

            if ($partner_id) {
                $get_child = $this->db->query("select * from family_construct fc where fc.is_adult = 'N' $gender_con ")->result_array();
                $arr_kid = [];
                foreach ($get_child as $key1 => $child_det) {
                    
                    $arr_kid[$key1]['member']['construct'] = $child_det['member_type'];

                    $arr_kid[$key1]['member']['member_id'] = $child_det['id'];
                    
                }

                $members = $this->db->query("select ma.id as member_ages_id,ma.is_adult,ma.member_type,fc.gender,fc.id,ma.lead_id,ma.customer_id from Create_quote_member_singlelink as ma left join family_construct as fc on  ma.member_type = fc.member_type where ma.lead_id = '$lead_id' $cond")->result_array();
                $ids =[0];
                foreach ($members as $value) {

                    if ($value['is_adult'] == 'Y' || $plan_details[0]['payment_first']==1) {
                        $arr['id'] = $value['id'];
                        if($plan_details[0]['payment_first']==1){

                            $member_age = $this->db->query("select id,member_age from member_ages where member_type='". $value['id']."' and lead_id = '$lead_id' and id not in(".implode(',',$ids).")")->row();
                           
                            $ids[]=$member_age->id;
                        }
                        if(!empty($member_age)){
                            $arr['member_age'] = preg_replace("/[^0-9]/", '',$member_age->member_age );
                            
                            $year_month = strtolower(trim(preg_replace('/[0-9]+/', '', $member_age->member_age)));
                            $year_month = ($year_month=='years')?'year':$year_month;
                            
                            $arr['member_age_month'] = empty($year_month)?'year':$year_month;
                        }
                    } else {
                        $arr['id'] = $arr_kid;
                        
                    }
                    $arr['member_type'] = $value['member_type'];
                    $arr['member_ages_id'] = $value['member_ages_id'];
                    $arr['is_adult'] = $value['is_adult'];
                    $arr['policy_member_first_name'] = '';
                    $arr['policy_member_last_name'] = '';
                    $arr['policy_member_dob'] = '';
                    $arr['policy_member_salutation'] = '';

                    if($value['gender'] !=''){
                        $arr['policy_member_gender'] =$value['gender'];
                    }else{
                        $arr['policy_member_gender'] = '';
                    }

                    if ($value['id'] == 1) {
                        $query = $this->db->query("select * from master_customer where lead_id = '$lead_id'")->row_array();
                        $arr['policy_member_first_name'] = $query['first_name'];
                        $arr['policy_member_last_name'] = $query['last_name'];
                        $arr['policy_member_dob'] = $query['dob'];
                        $arr['policy_member_gender'] = $query['gender'];
                        $arr['policy_member_salutation'] = $query['salutation'];


                    }
                    array_push($data, $arr);

                }


            } else {

                $members = $this->db->query("select ma.id as member_ages_id,fc.gender,fc.is_adult,fc.member_type,fc.id,ma.lead_id,ma.customer_id,ma.member_age from member_ages as ma join family_construct as fc where ma.member_type = fc.id and ma.lead_id = '$lead_id' $cond")->result_array();
                foreach ($members as $val) {
                    $arr['member_type'] = $val['member_type'];
                    $arr['member_ages_id'] = $val['member_ages_id'];
                    $arr['member_age'] = $val['member_age'];
                    $arr['id'] = $val['id'];
                    $arr['is_adult'] = $val['is_adult'];
                    $arr['policy_member_first_name'] = '';
                    $arr['policy_member_last_name'] = '';
                    $arr['policy_member_dob'] = '';
                    $arr['policy_member_salutation'] = '';
                    if($val['gender'] !=''){
                        $arr['policy_member_gender'] =$val['gender'];
                    }else{
                        $arr['policy_member_gender'] = '';
                    }
                    if ($val['id'] == 1) {
                        $query = $this->db->query("select * from master_customer where lead_id = '$lead_id'")->row_array();
                        $arr['policy_member_first_name'] = $query['first_name'];
                        $arr['policy_member_last_name'] = $query['last_name'];
                        $arr['policy_member_dob'] = $query['dob'];
                        $arr['policy_member_gender'] = $query['gender'];
                        $arr['policy_member_salutation'] = $query['salutation'];

                    }
                    array_push($data, $arr);


                }
            }
//             print_R($data);die;
            echo json_encode($data);
            exit;
            // print_r($data);
            //  print_r($abc);
            // echo json_encode($arr);exit;
            // echo json_encode($members);exit;
        }
        //     echo json_encode($members);exit;


    }

    public function createMembers()
    {
//echo 123;die;
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $data = $this->input->post('data');
        $deductable = $this->input->post('deductable');

        $data = json_decode($data, true);
//        print_r($data);die;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        //get adult array
        $adultNum = $this->db->query("select group_concat(id) as adultNum from family_construct where isactive = 1 and is_adult='Y'")->row()->adultNum;
        $adultNum = explode(',', $adultNum);

        $existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();
        $previous_si_type = 0;

        if (count($existing_members) > 0) {

            $previous_si_type = $existing_members[0]['si_type_id'];

            // echo $previous_si_type;exit;

            $existing_members_id = [];

            $existing_members_id = array_map(function ($value) {

                return $value['id'];

            }, $existing_members);

            $this->db->where_in('id', $existing_members_id);
            $this->db->delete('member_ages');


        }

        $array = [];

        $i = 0;
        $is_adult_exist = 0;
        foreach ($data as $key => $value) {
            if (key($value) == 'member_type') {
//                echo $value['member_type'];
                
                $array[$i]['member_type'] = $value['member_type'];
                $i++;
            }
            if (key($value) == 'age' && $value['age']>=18) {
                $is_adult_exist = 1;    
            }
        }

        if ($is_adult_exist == 0) {
            $response['msg'] = 'Atleast One Adult is Mandatory!';
            $response['status'] = 201;;
            echo json_encode($response);
            exit;
        }
        $plan = '0';
        //sum insured type off for self(type - 'Indivisual/family floater')
        if (count($array) < 2) {
            $previous_si_type = 1;
            if ($array[0]['member_type'] == 1) {
                $plan = '1';
            }

        }
        $i = 0;
        foreach ($data as $key => $value) {

            if (key($value) == 'age') {

                $array[$i]['age'] = $value['age'];
                $i++;
            }
        }
        if (count($array) > 1) {
            $previous_si_type = 2;
        }
        if (!empty($array)) {

            $data = [];

            $i = 0;
            foreach ($array as $key => $value) {

                $data[$i] = [
                    'member_type' => $value['member_type'],
                    'member_age' => $value['age'],
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'si_type_id' => $previous_si_type,
                    'created_on' => date('Y-m-d H:i:s'),
                    'updated_on' => date('Y-m-d H:i:s')
                ];
                if(!empty($deductable)){
                    $data[$i]['deductable']=$deductable;
                }


                $i++;
            }


            $this->db->insert_batch('member_ages', $data);

            $exp = [];
            $sel_p = $this->db->query("select * from member_ages where lead_id = $lead_id")->result_array();
            // print_r($sel_p);die;
            foreach ($sel_p as $val_p) {
                array_push($exp, $val_p['id']);

                $arr_p = ['member_ages_id' => $val_p['id']];
                $this->db->where('relation_with_proposal', $val_p['member_type']);
                $this->db->where('lead_id', $lead_id);

                $this->db->update('proposal_policy_member_details', $arr_p);


            }
            $imp = implode(',', $exp);
            $del_p = $this->db->query("delete from proposal_policy_member_details where member_ages_id not in($imp) and lead_id = '$lead_id'");

//die;
            echo json_encode(['status' => '200', 'message' => 'Members added successfully', 'plan' => $plan]);
            exit;
        }
    }


    public function updateLeadLastVisited()
    {

        $lead_id = encrypt_decrypt_password($_POST['lead_id'], 'D');
        $data = ['dropout_page'=>$_POST['dropout_page'],'dropoff_flag'=>0];
        if(!empty($_POST['proposal_step_completed'])){
            $data['proposal_step_completed']=$_POST['proposal_step_completed'];
        }
        $this->db->where('lead_id', $lead_id);
        $this->db->update("lead_details", $data);

        return true;

    }

    public function createMembers1()
    {
//echo 123;die;
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $data = $this->input->post('data');

        $data = json_decode($data, true);
        //  print_r($data);die;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');


        $existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();
        $previous_si_type = 0;

        if (count($existing_members) > 0) {

            $previous_si_type = $existing_members[0]['si_type_id'];

            // echo $previous_si_type;exit;

            $existing_members_id = [];

            $existing_members_id = array_map(function ($value) {

                return $value['id'];

            }, $existing_members);

            $this->db->where_in('id', $existing_members_id);
            $this->db->delete('member_ages');
        }

        $array = [];

        $i = 0;
        foreach ($data as $key => $value) {

            if (key($value) == 'member_type') {

                $array[$i]['member_type'] = $value['member_type'];
                $i++;
            }
        }
        $plan = '0';
        //sum insured type off for self(type - 'Indivisual/family floater')
        if (count($array) < 2) {
            if ($array[0]['member_type'] == 1) {
                $plan = '1';
            }

        }
        $i = 0;
        foreach ($data as $key => $value) {

            if (key($value) == 'age') {

                $array[$i]['age'] = $value['age'];
                $i++;
            }
        }

        if (!empty($array)) {

            $data = [];

            $i = 0;
            foreach ($array as $key => $value) {

                $data[$i] = [
                    'member_type' => $value['member_type'],
                    'member_age' => $value['age'],
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'si_type_id' => $previous_si_type,
                    'created_on' => date('Y-m-d H:i:s'),
                    'updated_on' => date('Y-m-d H:i:s')
                ];


                $i++;
            }


            $this->db->insert_batch('member_ages', $data);

//die;
            echo json_encode(['status' => '200', 'message' => 'Members added successfully', 'plan' => $plan]);
            exit;
        }
    }

    public function getQuotePageData()
    {

        // echo 1;exit;
        $data = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $ajaxReq = $this->input->post('ajaxReq');

        $product_id = $this->input->post('product_id');
        if($product_id){
            $product_id = encrypt_decrypt_password($product_id, 'D');
        }

        // $trace_id = "Q1BaUHFEME1wQ3JNRERQQzFjc1hJZz09";
        // $lead_id = "VDJRNnhIWEd4NFZDbVZTd01UZjBHQT09";
        // $customer_id = "N2ZaS2JRdE84RllRMUtxMzIzSENtQT09";
        // $si_type = "VjVnSUhsTHluQWtBYlg2VDNzTThNQT09";

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');


        /* data from filters via ajax calls */

        $partner_id = $this->input->post('partner_id');

        if ($partner_id) {

            $partner_id = encrypt_decrypt_password($partner_id, 'D');
        }
        $insurer_id = $this->input->post('insurer_id');
        $insurer_idArr=array();
        if (is_array($insurer_id) && count($insurer_id) > 0) {
            foreach ($insurer_id as $item1){
                $insurer_idArr[] = encrypt_decrypt_password($item1, 'D');
            }

        }
         $insurer_id=implode(',',$insurer_idArr);

        $premium = $this->input->post('premium');
        //  exit;
        $cover = $this->input->post('cover');
        $duration = $this->input->post('duration');
        $members = $this->input->post('members');
        $order = $this->input->post('order');
        $si_type = $this->input->post('si_type');

        if ($si_type) {
            $si_type = encrypt_decrypt_password($si_type, 'D');
            //$updateSIType = "UPDATE member_ages SET si_type_id = '".$si_type."' WHERE customer_id = '".$customer_id."' AND lead_id = '".$lead_id."'";
            //$this->db->query($updateSIType);
            //echo json_encode($si_type);exit;
        }
        if(!empty($product_id)){

            $data = $this->db->select('mpo.is_consider_adult,master_suminsured_type.suminsured_type_id,master_suminsured_type.suminsured_type')
                ->from('master_suminsured_type')
                ->join("master_policy_si_type_mapping mpm", "master_suminsured_type.suminsured_type_id = mpm.suminsured_type_id", 'inner')
                ->join("master_policy mpo", "mpo.policy_id = mpm.master_policy_id", 'inner')
                ->where('mpo.plan_id = ' . $product_id)
                ->where('master_suminsured_type.isactive', 1)
                ->group_by("master_suminsured_type.suminsured_type_id")
                ->get()
                ->row_array();
            $si_type = $data['suminsured_type_id'];
            $consider_adult = $data['is_consider_adult'];
        }
        // $si_type = 2;
        /* data from filters via ajax calls ends */

        /*$member_where = "customer_id = $customer_id AND lead_id = $lead_id";

        if($members){

            $member_where .= " AND fc.member_type IN (".implode(',', $members).")";
        }*/

        $mainFamConstruct=array();
        $QuerAll=$this->db->query("select id,is_adult from family_construct where isactive=1")->result();
        if(count($QuerAll) > 0){
            foreach ($QuerAll as $r){
                $mainFamConstruct[$r->id]=$r->is_adult;
            }
        }


        $data['trace_id'] = $trace_id;
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.id,fc.member_type, ma.member_age, ma.si_type_id,mst.suminsured_type,ma.deductable')
            ->from('member_ages ma')
            ->join('master_suminsured_type mst', 'mst.suminsured_type_id = ma.si_type_id')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();
      //  echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        $deductable='';
        $member_type = array();
        $selectedMembers=array();//memType
        foreach ($data['member_ages'] as $key => $value) {
            $selectedMembers[]=$value['id'];
            $deductable = $value['deductable'];
            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            if($value['member_age']>=18 && $consider_adult == 1){
               $value['is_adult']='Y';
               $mainFamConstruct[$value['id']] = 'Y';
            }
            $is_adult = $value['is_adult'];

            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            } else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            // $min_age = $value['min_age'];
            //$max_age = $value['max_age'];
            if ($members) {
                //$min_age = $value['min_age'];
                //$max_age = $value['max_age'];
                $min_age = '18';
                $max_age = '55';


            }
            $member_type[] = $value['member_type'];

        }
     //   $selectedMembers=implode(',',$selectedMembers);
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];
        if (count($arr_age) <= 0) {

            $max_age = 55;
        } else {
            $max_age = max($arr_age);
        }

        $master_policy_id_arr = $this->db->distinct()
            ->select('master_policy_id')
            ->from('master_policy_si_type_mapping')
            ->where(['suminsured_type_id' => $si_type, 'isactive' => 1])
            ->get()
            ->result_array();

   //     echo json_encode($this->db->last_query());exit;
//var_dump($master_policy_id_arr);exit;
        if (!empty($master_policy_id_arr)) {

             $master_policy_ids = $this->get_required_data_from_array($master_policy_id_arr, 'master_policy_id');
           // var_dump($master_policy_ids);exit;
            $where = "mp.policy_id IN (" . implode(',', $master_policy_ids) . ") AND mp.isactive = 1 ";
            // $where = "mp.policy_id IN (".implode(',', $master_policy_ids).") AND mp.isactive = 1 ";
            // echo $this->db->last_query();exit;
            if ($si_type == 2) {
                //  $where .= " AND  (mppr.adult_count = ".$arr['adult_count']." AND mppr.child_count = ".$arr['child_count'].")";
            }
              $where .= " AND  (mp.adult_count >= ".$arr['adult_count']." AND mp.child_count >= ".$arr['child_count'].")";

            $whereArray = array();
            $min_premium = 0;
            $max_premium = 0;
            if ($premium) {

                if (strpos($premium, '-') !== false) {

                    $premium_arr = explode('-', $premium);
                    $min_premium = $premium_arr[0];
                    $max_premium = $premium_arr[1];
                    //  $where .=  " AND ((mppr.premium_rate >=".$premium_arr[0] ." AND mppr.premium_rate <= ".$premium_arr[1].") OR (mppr.premium_with_tax >=".$premium_arr[0] ." AND mppr.premium_with_tax <= ".$premium_arr[1]."))  ";
                } else if ($premium == 3000) {
                    $min_premium = $premium;
                    $max_premium = $premium;
                    //  $where .= " AND ((mppr.premium_rate <=".$premium.") OR (mppr.premium_rate <=".$premium."))";
                } else {
                    $min_premium = 0;
                    $max_premium = $premium;
                    //     $where .= " AND ((mppr.premium_rate >=".$premium.") OR (mppr.premium_rate >=".$premium."))";
                }
            }
            $min_cover=0;
            $max_cover=0;
            if ($cover) {

                if (strpos($cover, '-') !== false) {

                    $cover_arr = explode('-', $cover);
                    $min_cover = $cover_arr[0];
                    $max_cover = $cover_arr[1];
                    // $where .=  " AND (mppr.sum_insured >=".$cover_arr[0] ." AND mppr.sum_insured <= ".$cover_arr[1].")";

                } else {
                    $max_cover = $cover;
                    $min_cover = 0;
                    //   $where .= " AND (mppr.sum_insured >=".$cover.")";
                }
            }

            if ($duration) {

                $where .= " AND TIMESTAMPDIFF(YEAR, `mp`.`policy_start_date`, `mp`.`policy_end_date`) >= " . $duration;
            }

            if ($insurer_id) {
                $where .= " AND mp.insurer_id in  (" . $insurer_id .")";
            }

            $order_by = 'mp.insurer_id ASC';
            if ($order) {

                //   $order_by = 'mppr.premium ASC';
            }

            if ($min_age && $max_age) {

                $where .= " AND (mppr.min_age >= $min_age AND mppr.max_age <= $max_age)";
            }

            if ($partner_id) {
                $where .= " AND mc.creditor_id = '" . $partner_id . "'";
//                $where .= " AND mppr.adult_count = '" . $arr['adult_count'] . "' AND mppr.child_count='" . $arr['child_count'] . "'";

            }
            if($product_id){
                $where .= " AND mp.plan_id = '" . $product_id . "'";
            }
            $adultCount=$arr['adult_count'];
            $childCount=$arr['child_count'];
           // $where .= " AND (select group_concat(mpfc.member_type_id) from master_policy_family_construct mpfc where mpfc.master_policy_id = mp.policy_id AND mpfc.isactive = 1) in ( " . $selectedMembers . ")";
            $group_by = 'mp.plan_id';

            $master_policy_arr = $this->db->select("mp.policy_id,mpbm.si_premium_basis_id,mp.plan_id,mp.policy_sub_type_id,mp.insurer_id,mp.policy_start_date,
            mp.policy_end_date,mpp.plan_name,mi.insurer_name, mpp.creditor_id, 
            TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo,
            group_concat(mpfc.member_type_id) as familyConstraint")
                ->from('master_policy mp')
                ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
                ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
                ->join('master_policy_family_construct mpfc', 'mpfc.master_policy_id = mp.policy_id AND mpfc.isactive = 1')
                ->join('master_policy_premium_basis_mapping mpbm', 'mpbm.master_policy_id = mp.policy_id AND mpbm.isactive = 1')

//                ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
                ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
                // ->join('master_policy_premium_permile mpppr', 'mpppr.master_policy_id = mp.policy_id AND mpppr.isactive = 1')
                // ->join('master_per_day_tenure_premiums mpdt', 'mpdt.master_policy_id = mp.policy_id AND mpdt.isactive = 1')
                /*->where_in('mp.policy_id', $master_policy_ids)
                ->where(['mp.policy_sub_type_id' => 1, 'mp.isactive' => 1])
                ->where(['mppr.adult_count' => $arr['adult_count'], 'mppr.child_count' => $arr['child_count']])*/
                ->where($where)
                ->order_by($order_by)
                ->group_by($group_by)
                ->get()->result_array();

            $sum = 0;

            $arr_policy = [];
             //echo $this->db->last_query();die;
          //   exit;
            // echo json_encode($master_policy_arr);exit;
            //fetch features
          //  print_r($master_policy_arr);
            $assSum = [];
            foreach ($master_policy_arr as $key => $value) {
               $familyConstraint=$value['familyConstraint'];
                $familyConstraintarr = explode(',', $familyConstraint);
                $familyConstraintarradult=array();
                foreach ($familyConstraintarr as $m){
                    if ($mainFamConstruct[$m] == 'Y'){
                        $familyConstraintarradult[]=$m;
                    }
                }
                $cnt11=count($familyConstraintarradult);
                $available=0;
                $notavial=0;
              //  var_dump($selectedMembers);
              //  var_dump($familyConstraint);
                foreach ($selectedMembers as $mem){
                    if ($mainFamConstruct[$mem] == 'Y') {
                        if (in_array($mem, $familyConstraintarradult)) {
                            $available++;
                        }else{
                            $notavial++;
                        }
                    }
                }


                if($available > 0 && $notavial ==0){
                    if ($ajaxReq) {
                        $get_policy_wise_suminsured = $this->getPolicywiseSumInsured_diff($value['policy_id'], $min_cover, $max_cover);
                        $whereArray = 1;
                    } else {
                        $whereArray = 0;
                     //  echo $value['policy_id'];
                        $get_policy_wise_suminsured = $this->getPolicywiseSumInsured($value['policy_id'],$adultCount,$childCount,$max_age);
                    //   var_dump($get_policy_wise_suminsured);exit;
                    }
                //  print_R($max_age);
                  /*  echo $value['policy_id']. "--". $value['si_premium_basis_id'];
                                 print_r($get_policy_wise_suminsured);*/
               //     print_r($get_policy_wise_suminsured);
                /*   echo 'pooja';
               print_r($get_policy_wise_suminsured);
                    exit;*/
                    $master_policy_arr[$key]['si_premium_basis_id'] = $value['si_premium_basis_id'];
                    $master_policy_arr[$key]['suminsured'] = $get_policy_wise_suminsured;
                    if (!empty($get_policy_wise_suminsured)) {

                        foreach ($get_policy_wise_suminsured as $key1 => $suminsured) {
                            $get_policy_det = $this->getAllpremium_det($lead_id, $customer_id, $value['policy_id'], $suminsured['sum_insured'], $max_age, $is_adult, $arr_age, $adult, $child, $partner_id, $whereArray, $min_premium, $max_premium,$value['policy_sub_type_id'],$deductable);
                        // var_dump('Pooja');
                          //   print_r($get_policy_det);die;
                            $get_policy_amt = 0;
                            if (!empty($get_policy_det)) {
                                $get_policy_amt = $get_policy_det['amount'];
                                $master_policy_arr[$key]['suminsured'][$key1]['rate'] = $get_policy_amt;
                            } else {
                                unset($master_policy_arr[$key]['suminsured'][$key1]);
                            }

                        }
                        $master_policy_arr[$key]['suminsured'] = array_values($master_policy_arr[$key]['suminsured']);
                        $qry = "SELECT * FROM features_config where creditor_id = '" . $value['creditor_id'] . "' AND plan_id = '" . $value['plan_id'] . "' AND isactive = 1 limit 10";
                        $master_policy_arr[$key]['features'] = $this->db->query($qry)->result_array();
                    }
                }else{
                    unset($master_policy_arr[$key]);
                }



            }
       //     exit;

         //   echo '<pre>';
      //print_r($master_policy_arr);die;
            if (!empty($master_policy_arr)) {

                $data['policy_data'] = $master_policy_arr;
                $data['status'] = 200;
                echo json_encode($data);
                exit;
            } else {

                $data['status'] = 400;
                $data['msg'] = 'No policies were found';
                echo json_encode($data);
                exit;
            }
        } else {

            $data['status'] = 401;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }
    }

    public function getAll_data_premium()
    {
        $data = [];
        $res = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $sum = 0;
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_id = $this->input->post('policy_id');
        $cover = $this->input->post('cover');
        $plan_id = encrypt_decrypt_password($this->input->post('plan_id'), 'D');
        $partner_id = $this->session->userdata('partner_id_session');
        if ($policy_id) {

            $policy_id = encrypt_decrypt_password($policy_id, 'D');
        }
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();
        //  echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];
        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $policy_id)
            ->get()
            ->row_array();

        $consider_adult = $policy_det['is_consider_adult'];
        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            if($value['member_age']>=18 && $consider_adult == 1 ){
               $value['is_adult']='Y';
               $mainFamConstruct[$value['id']] = 'Y';
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);

        $plan_details = $this->apimodel->getPolicyPlan($plan_id);

        $sum_insured = $cover;
        foreach ($plan_details as $key => $policy) {
            $policy_id = $policy['policy_id'];
            $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id'")->row_array();
            if (!empty($sel_policy_plan)) {
                $plan_details[$key]['already_avail'] = 1;
            } else {
                $plan_details[$key]['already_avail'] = 0;
            }

            //$policy->basis_id = 4;
            $adult = $arr['adult_count'];
            $child = $arr['child_count'];


            if ($policy['basis_id'] == 1) {
                //echo json_encode(111111);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured, $partner_id);

            } else if ($policy['basis_id'] == 2) {
                // echo json_encode(22222);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child, $partner_id);

            } else if ($policy['basis_id'] == 3) {
                //echo json_encode(33333);exit;
                $age = $max_age;

                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age, $partner_id);


            } else if ($policy['basis_id'] == 4) {
                // echo json_encode(4444);exit;


                if ($arr['is_adult'] == 'Y' && $child == 0) {
                    foreach ($arr_age as $member_age_cal) {

                        $age = $member_age_cal;
                        $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age, $partner_id);
                        if (!empty($abc)) {
                            foreach ($abc as $rate_data) {
                                $sum = $sum + $rate_data;

                            }
                        }
                    }
                    $plan_details[$key]['rate'][]['amount'] = $sum;
                }


            }
        }
        $arr_data = [];
        $data_arr = [];

        if (!empty($plan_details)) {
            foreach ($plan_details as $get_plans) {

                if ($get_plans['rate'][0] != 0 && $get_plans['rate'][0]['amount'] != 0) {


                    $data_arr['premium'] = $get_plans['rate'][0]['amount'];
                    $data_arr['is_combo'] = $get_plans['is_combo'];
                    $data_arr['is_optional'] = $get_plans['is_optional'];
                    $data_arr['already_avail'] = $get_plans['already_avail'];
                    $data_arr['policy_id'] = $get_plans['policy_id'];


                    array_push($arr_data, $data_arr);

                }

            }
        }
//print_r($arr_data);exit;
        if (!empty($arr_data)) {

            // print_r($arr_data);exit;
            echo json_encode($arr_data);
            exit;


        }
    }

    public function getAll_data_card()
    {
        $data = [];
        $res = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $sum = 0;
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_id = $this->input->post('policy_id');
        $cover = $this->input->post('cover');
        if ($policy_id) {

            $policy_id = encrypt_decrypt_password($policy_id, 'D');
        }
        $mainFamConstruct=array();
        $QuerAll=$this->db->query("select id,is_adult from family_construct where isactive=1")->result();
        if(count($QuerAll) > 0){
            foreach ($QuerAll as $r){
                $mainFamConstruct[$r->id]=$r->is_adult;
            }
        }
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type,fc.id, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();
        //   echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];
        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $policy_id)
            ->get()
            ->row_array();

        $consider_adult = $policy_det['is_consider_adult'];
        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        $memberType = array();
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            if ($value['member_age'] >= '18' && strpos($value['member_age'],'months') === false && $consider_adult == 1){
                $value['is_adult'] = 'Y';
            }

            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            $memberType[] = $value['id'];
            // }
        }
        $max_age = max($arr_age);
        $plan_id = $this->input->post('plan_id');
        $plan_details = $this->apimodel->getProductDetailsAll_diff($plan_id);
//            var_dump($plan_details);
//            exit;

        $memberType = implode(',', $memberType);
//        print_r($plan_details);die;
        $sum_insured = $cover;
        foreach ($plan_details as $key => $policy) {
           // echo 1;
            $policy_id = $policy['policy_id'];
            $policy_subtype_id=$this->db->query("select policy_sub_type_id from master_policy where policy_id=".$policy_id)->row()->policy_sub_type_id;
            $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id'")->row_array();
            if (!empty($sel_policy_plan)) {
                $plan_details[$key]['already_avail'][] = 1;
            } else {
                $plan_details[$key]['already_avail'][] = 0;
            }
            //echo $arr['adult_count'];die;
            if ($policy['basis_id'] != 7) {
                $sum_insured = $this->db->query("select sum_insured from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " order by policy_premium_id asc limit 1")->row()->sum_insured;

                $adult = $arr['adult_count'];
                $child = $arr['child_count'];
            }else{
                $sum_insured = $this->db->query("select sum_insured from master_per_day_tenure_premiums where isactive=1 AND master_policy_id=" . $policy_id . " order by id asc limit 1")->row()->sum_insured;
            }


            if ($policy['basis_id'] == 1) {
                //echo json_encode(111111);exit;
              //  echo $sum_insured;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);

            } else if ($policy['basis_id'] == 2) {
                // echo json_encode(22222);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);

            } else if ($policy['basis_id'] == 3) {
                //echo json_encode(33333);exit;
                $age = $max_age;

                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


            } else if ($policy['basis_id'] == 4) {
                // echo json_encode(4444);exit;


                if ($arr['is_adult'] == 'Y' && $child == 0) {
                    foreach ($arr_age as $member_age_cal) {

                        $age = $member_age_cal;
                        $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                        if (!empty($abc)) {
                            foreach ($abc as $rate_data) {
                                $sum = $sum + $rate_data;

                            }
                        }
                    }
                    $plan_details[$key]['rate'][]['amount'] = $sum;
                }


            }
           /* else if ($policy['basis_id'] == 5) {
                $age = $max_age;
                $abc = $this->calculatePerMilePremium($policy['policy_id'], $age, $sum_insured, $tenure);
                $plan_details[$key]['rate'][] = $abc;
                //print_r($abc);
            }*/
            else if ($policy['basis_id'] == 5) {
                if($sum_insured == NULL){
                    $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
                }
                $age = $max_age;
                //  echo "P".$sum_insured;
                $this->age = $age;
                $this->tenure = 2;
               // echo 123;
              //  echo $sum_insured;exit;
                $arr1=array(
                    'policy_id' => $policy['policy_id'],
                    'sum_insured' => $sum_insured,
                    'hospi_cash_group_code' => 'Grp001',
                    'policy_sub_type_id' => $policy_subtype_id,
                    'group_code_type' => 1
                );


                $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr1, ['number_of_ci' => 0, 'age' => $age, "tenure" => $this->tenure]));
                //echo $this->db->last_query();
                //  var_dump($result['rate']);exit;
                $plan_details[$key]['rate'][]['amount']  = $result['rate'];
            }else if ($policy['basis_id'] == 6) {

                 $deductable= $this->db->query("select deductable from master_policy_premium where isactive=1 AND master_policy_id=" . $policy_id . " and sum_insured=".$sum_insured." and adult_count=".$adult." and child_count=".$child)->row()->deductable;
                $age = $max_age;
                $this->age = $age;
                $this->tenure = 2;
                $arr2=array(
                    'policy_id' => $policy['policy_id'],
                    'sum_insured' => $sum_insured,
                    'hospi_cash_group_code' => '',
                    'policy_sub_type_id' => $policy_subtype_id,
                    'group_code_type' => 1,
                    'age'=>$age
                );


                $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr2, ['adult_count' => $adult, 'child_count' => $child, "deductable" => $deductable]));
                //echo $this->db->last_query();
                //   var_dump($result);exit;
                $plan_details[$key]['rate'][]['amount']  = $result['rate'];
            }else if ($policy['basis_id'] ==7) {
                $age = $max_age;
                $this->age = $age;
                $this->tenure = 2;
                $arr3=array(
                    'policy_id' => $policy['policy_id'],
                    'sum_insured' => $sum_insured,
                    'hospi_cash_group_code' => 'Grp001',
                    'policy_sub_type_id' => $policy_subtype_id,
                    'group_code_type' => 1
                );


                $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr3, ['tenure' => $this->tenure]));
                //echo $this->db->last_query();
                //   var_dump($result);exit;
                //   var_dump($result);exit;
                $plan_details[$key]['rate'][]['amount']  = $result['rate'];
            }

        }
      // exit;
     //   var_dump($plan_details);exit;
        //die;
        $arr_data = [];
        $data_arr = [];
// get family construct

//var_dump($plan_details);exit;
        if (!empty($plan_details)) {
            foreach ($plan_details as $get_plans) {
//select group_concat(member_type) as member_type from family_construct where id in(1,2,5,6);


                if (array_key_exists('rate', $get_plans) && $get_plans['rate'][0] != 0 && $get_plans['rate'][0]['amount'] != 0) {
                    if ($get_plans['basis_id'] != 7) {
                        $sum_insured=$this->db->query("select sum_insured from master_policy_premium where isactive=1 AND master_policy_id=".$get_plans['policy_id']." order by policy_premium_id asc limit 1")->row()->sum_insured;

                    }else{
                        $sum_insured = $this->db->query("select sum_insured from master_per_day_tenure_premiums where isactive=1 AND master_policy_id=" . $get_plans['policy_id'] . " order by id asc limit 1")->row()->sum_insured;
                    }

                    //var_dump($get_plans['basis_id']);
                    $data_arr['policy_sub_type_name'] = $get_plans['policy_sub_type_name'];
                    $data_arr['is_combo'] = $get_plans['is_combo'];
                    $data_arr['is_optional'] = $get_plans['is_optional'];
                    $data_arr['creditor_name'] = $get_plans['creditor_name'];
                    $data_arr['creditor_logo'] = $get_plans['creditor_logo'];
                    $data_arr['premium'] = $get_plans['rate'][0]['amount'];
                    $data_arr['policy_id'] = $get_plans['policy_id'];
                    $data_arr['already_avail'] = $get_plans['already_avail'];
                    $data_arr['family_construct'] = $get_plans['family_construct'];
                    $data_arr['cover'] = $sum_insured;
                    $familyConstraint=$get_plans['family_construct'];
                    $familyConstraintarr = explode(',', $familyConstraint);
                    $familyConstraintarradult=array();
                    foreach ($familyConstraintarr as $m){
                        if ($mainFamConstruct[$m] == 'Y'){
                            $familyConstraintarradult[]=$m;
                        }
                    }
                    $available=0;
                    $notavial=0;
                    $memberTypearr=explode(',',$memberType);
                 //   var_dump($familyConstraintarradult);
                    foreach ($memberTypearr as $mem){
                        if ($mainFamConstruct[$mem] == 'Y') {
                         //   echo $mem;
                            if (in_array($mem, $familyConstraintarradult)) {
                                $available++;
                            }
                        }
                    }


                    // get family construct
                    $member_typeName = '';
                    if ($get_plans['family_construct'] != null && !empty($get_plans['family_construct'])) {
                        $query = $this->db->query("select group_concat(member_type) as member_type from family_construct where id in(" . $get_plans['family_construct'] . ")");
                        if ($this->db->affected_rows() > 0) {
                            $member_typeName = $query->row()->member_type;
                        }
                    }
                    $data_arr['member_typeName'] = $member_typeName;
                    $data_arr['memberTypeSelected'] = $memberType;

                    if ($available > 0 ){

                        array_push($arr_data, $data_arr);
                    }


                }

            }
        }
//exit;
//print_r($arr_data);exit;
        if (!empty($arr_data)) {

            // print_r($arr_data);exit;
            echo json_encode($arr_data);
            exit;


        }
    }

    public function createPolicy_member_plan()
    {

        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_details = $this->input->post('policy_details');

        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $plan_id = $this->input->post('plan_id');
        $cover = $this->input->post('cover');
        $premium = $this->input->post('premium');
        $tenure = $this->input->post('tenure');
        $single = $this->input->post('single');
        $total_premium = $this->input->post('total_premium');
        $del = $this->db->query("delete from policy_member_plan_details where lead_id = '$lead_id'");
        if ($single) {
            $arr = ['lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'trace_id' => $trace_id,
                'plan_id' => $plan_id,
                'policy_id' => $policy_id,
                'cover' => $cover,
                'premium' => $premium,
                'total_premium' => $total_premium,


            ];
            $this->db->insert('policy_member_plan_details', $arr);
            insert_application_log($lead_id, "quote_inserted", json_encode($arr), json_encode(array("response" => "Quote Saved")), 0);
        } else {
            $arr_n=array();
            foreach ($policy_details as $value) {
                $arr = ['lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'trace_id' => $trace_id,
                    'plan_id' => $value['plan_id'],
                    'policy_id' => $value['policy_id'],
                    'cover' => $value['cover'],
                    'premium' => $value['premium'],
                    'total_premium' => $value['total_premium'],
                    'additional_plan_flag' => $value['plan_flag']

                ];
                $arr_n[]=$arr;
                $this->db->insert('policy_member_plan_details', $arr);
            }
            insert_application_log($lead_id, "quote_inserted", json_encode($arr_n), json_encode(array("response" => "Quote Saved")), 0);

            //

        }

        echo json_encode(['status' => '200', 'message' => 'Members policy plan added successfully']);
        exit;


    }
    public function createPolicy_member_plan_gadget()
    {

        $lead_id = $this->input->post('lead_id');

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $policy_details = $this->input->post('policy_details');
        $query=$this->db->query("select trace_id from lead_details where lead_id=".$lead_id)->row();
        $query2=$this->db->query("select customer_id from master_customer where lead_id=".$lead_id)->row();
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        if(is_null($policy_id) || empty($policy_id) || !isset($policy_id)){
            $policy_id=$this->input->post('policy_id');
        }
        $plan_id = $this->input->post('plan_id');
        $cover = $this->input->post('cover');
        $premium = $this->input->post('premium');
        $tenure = $this->input->post('tenure');
        $total_premium = $this->input->post('total_premium');
        $del = $this->db->query("delete from policy_member_plan_details where lead_id = '$lead_id' and policy_id=".$policy_id);
            $arr = array('lead_id' => $lead_id,
                'customer_id' => $query2->customer_id,
                'trace_id' =>$query->trace_id,
                'plan_id' => $plan_id,
                'policy_id' => $policy_id,
                'cover' => $cover,
                'premium' => $premium,
                'total_premium' => $total_premium,);
            $this->db->insert('policy_member_plan_details', $arr);
            insert_application_log($lead_id, "quote_inserted", json_encode($arr), json_encode(array("response" => "Quote Saved")), 0);


        echo json_encode(array('status' => '200', 'message' => 'Policy plan added successfully'));
        exit;


    }

    public function getQuotePageData_bknew()
    {

        // echo 1;exit;
        $data = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        // $trace_id = "Q1BaUHFEME1wQ3JNRERQQzFjc1hJZz09";
        // $lead_id = "VDJRNnhIWEd4NFZDbVZTd01UZjBHQT09";
        // $customer_id = "N2ZaS2JRdE84RllRMUtxMzIzSENtQT09";
        // $si_type = "VjVnSUhsTHluQWtBYlg2VDNzTThNQT09";

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');

        /* data from filters via ajax calls */

        $insurer_id = $this->input->post('insurer_id');

        if ($insurer_id) {

            $insurer_id = encrypt_decrypt_password($insurer_id, 'D');
        }

        $premium = $this->input->post('premium');
        $cover = $this->input->post('cover');
        $duration = $this->input->post('duration');
        $members = $this->input->post('members');
        $order = $this->input->post('order');
        $si_type = $this->input->post('si_type');

        if ($si_type) {

            $si_type = encrypt_decrypt_password($si_type, 'D');
        }
        // $si_type = 2;
        /* data from filters via ajax calls ends */

        /*$member_where = "customer_id = $customer_id AND lead_id = $lead_id";

        if($members){

            $member_where .= " AND fc.member_type IN (".implode(',', $members).")";
        }*/

        $data['trace_id'] = $trace_id;
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();
        //echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;

        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            if ($members) {
                // $min_age = $value['min_age'];
                // $max_age = $value['max_age'];
                $min_age = '18';
                $max_age = '55';
            }
        }

        $master_policy_id_arr = $this->db->distinct()
            ->select('master_policy_id')
            ->from('master_policy_si_type_mapping')
            ->where(['suminsured_type_id' => $si_type, 'isactive' => 1])
            ->get()
            ->result_array();

        //echo json_encode($this->db->last_query());exit;

        if (!empty($master_policy_id_arr)) {

            $master_policy_ids = $this->get_required_data_from_array($master_policy_id_arr, 'master_policy_id');

            // $where = "mp.policy_id IN (".implode(',', $master_policy_ids).") AND mp.policy_sub_type_id = 1 AND mp.isactive = 1 AND (mppr.adult_count = ".$arr['adult_count']." OR mppr.child_count = ".$arr['child_count'].")";
            $where = "mp.policy_id IN (" . implode(',', $master_policy_ids) . ") AND mp.isactive = 1 ";
            // echo $this->db->last_query();exit;
            if ($si_type == 2) {
                $where .= " AND mp.policy_sub_type_id = 1 AND (mppr.adult_count = " . $arr['adult_count'] . " OR mppr.child_count = " . $arr['child_count'] . ")";
            }

            if ($premium) {

                if (strpos($premium, '-') !== false) {

                    $premium_arr = explode('-', $premium);
                    $where .= " AND ((mppr.premium_rate >=" . $premium_arr[0] . " AND mppr.premium_rate <= " . $premium_arr[1] . ") OR (mppr.premium_with_tax >=" . $premium_arr[0] . " AND mppr.premium_with_tax <= " . $premium_arr[1] . "))  ";
                } else if ($premium == 3000) {

                    $where .= " AND ((mppr.premium_rate <=" . $premium . ") OR (mppr.premium_rate <=" . $premium . "))";
                } else {

                    $where .= " AND ((mppr.premium_rate >=" . $premium . ") OR (mppr.premium_rate >=" . $premium . "))";
                }
            }

            if ($cover) {

                if (strpos($cover, '-') !== false) {

                    $cover_arr = explode('-', $cover);
                    $where .= " AND (mppr.sum_insured >=" . $cover_arr[0] . " AND mppr.sum_insured <= " . $cover_arr[1] . ")";

                } else {

                    $where .= " AND (mppr.sum_insured >=" . $cover . ")";
                }
            }

            if ($duration) {

                $where .= " AND TIMESTAMPDIFF(YEAR, `mp`.`policy_start_date`, `mp`.`policy_end_date`) >= " . $duration;
            }

            if ($insurer_id) {
                $where .= " AND mp.insurer_id = " . $insurer_id;
            }

            $order_by = 'mp.insurer_id ASC';
            if ($order) {

                $order_by = 'mppr.premium ASC';
            }

            if ($min_age && $max_age) {

                $where .= " AND (mppr.min_age >= $min_age AND mppr.max_age <= $max_age)";
            }
            $group_by = 'mp.plan_id';

            $master_policy_arr = $this->db->select("mp.policy_id,mp.plan_id,mp.insurer_id,mp.policy_start_date,mp.policy_end_date,mpp.plan_name,mi.insurer_name,mppr.sum_insured, mppr.premium_rate,mppr.premium_with_tax, mpp.creditor_id, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo")
                ->from('master_policy mp')
                ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
                ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
                ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
                ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
                // ->join('master_policy_premium_permile mpppr', 'mpppr.master_policy_id = mp.policy_id AND mpppr.isactive = 1')
                // ->join('master_per_day_tenure_premiums mpdt', 'mpdt.master_policy_id = mp.policy_id AND mpdt.isactive = 1')
                /*->where_in('mp.policy_id', $master_policy_ids)
                ->where(['mp.policy_sub_type_id' => 1, 'mp.isactive' => 1])
                ->where(['mppr.adult_count' => $arr['adult_count'], 'mppr.child_count' => $arr['child_count']])*/
                ->where($where)
                ->order_by($order_by)
                ->group_by($group_by)
                ->get()->result_array();

            foreach ($master_policy_arr as $key => $value) {
                $sum_insured = $value['sum_insured'];
                $adult = $arr['adult_count'];
                $child = $arr['child_count'];
                $age = 33;
                $plan_details = $this->apimodel->getProductDetailsAll($value['plan_id']);
                foreach ($plan_details as $policy) {
                    //$policy->basis_id = 4;
                    if ($policy->basis_id == 1) {
                        //echo json_encode(111111);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiumflat($policy->policy_id, $sum_insured);

                    } else if ($policy->basis_id == 2) {
                        // echo json_encode(22222);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiumfamilyconstruct($policy->policy_id, $sum_insured, $adult, $child);

                    } else if ($policy->basis_id == 3) {
                        //echo json_encode(33333);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiumfamilyconstructage($policy->policy_id, $policy->policy_id, $sum_insured, $adult, $child, $age);


                    } else if ($policy->basis_id == 4) {
                        // echo json_encode(4444);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiummemberage($policy->policy_id, $sum_insured, $age);

                    }
                }


            }

            // echo json_encode($master_policy_arr);exit;
            //fetch features
            foreach ($master_policy_arr as $key => $value) {
                $qry = "SELECT * FROM features_config where creditor_id = '" . $value['creditor_id'] . "' AND plan_id = '" . $value['plan_id'] . "' AND isactive = 1 limit 10";
                $master_policy_arr[$key]['features'] = $this->db->query($qry)->result_array();
                print_r($this->db->query());

            }
            if (!empty($master_policy_arr)) {

                $data['policy_data'] = $master_policy_arr;
                $data['status'] = 200;
                echo json_encode($data);
                exit;
            } else {

                $data['status'] = 400;
                $data['msg'] = 'No policies were found';
                echo json_encode($data);
                exit;
            }
        } else {

            $data['status'] = 401;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }
    }

    function get_required_data_from_array($arr, $key)
    {

        return array_map(function ($inner_arr) use ($key) {

            return $inner_arr[$key];

        }, $arr);
    }

    public function getCustomerDetails()
    {
        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];
        $is_normal_customer = $_POST['is_normal_customer'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');
        $data['customer_details'] = $this->customerapimodel->getCustomerDetailByid($customer_id);
        //var_dump($data);die;

        $existing_members = $this->db->select('id,member_type,member_age,dob')->from('member_ages')->where(['lead_id' => $lead_id, 'member_type' => 1])->get()->result_array();

        $existing_all_members = $this->db->select('id,member_type,member_age,dob,deductable')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();

        if($is_normal_customer!='1'){
            
            $members = $this->db->query("select ma.dob,ma.id as member_ages_id,ma.is_adult,ma.member_type,fc.gender,fc.id,ma.lead_id,ma.customer_id from Create_quote_member_singlelink as ma left join family_construct as fc on  ma.member_type = fc.member_type where ma.lead_id = '$lead_id'")->result_array();
            $data['members'] = $members;
        }else{
           
            $data['members'] = $existing_members;
        }
        if(empty($data['customer_details']['dob']) || $data['customer_details']['dob']=='0000-00-00')
        {
            $data['customer_details']['dob'] = $existing_members[0]['dob'];
        }
        $data['member_type'] = (!empty($existing_members[0]['member_type'])) ? $existing_members[0]['member_type'] : 0;
        $data['existing_all_members'] = $existing_all_members;
        

        echo json_encode($data);
        exit;
    }

    public function getfamily_data_exist()
    {
        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $nominee_rel = $_POST['nominee_rel'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        if ($nominee_rel) {
            $query = $this->db->query("select fc.id from master_nominee_relations as mnr,family_construct as fc where mnr.name = fc.member_type and mnr.id = '$nominee_rel'")->row_array();
            $rel_id = $query['id'];
            $get_data = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id' and customer_id = '$customer_id' and relation_with_proposal = '$rel_id'")->result_array();

        } else {
            $get_data = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id' and customer_id = '$customer_id'")->result_array();

        }
        echo json_encode($get_data);
        exit;
    }

    public function getQuoteDetails()
    {

        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];
        $master_policy_id = $_POST['master_policy_id'];
        $creditor_id = $_POST['creditor_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');
        $master_policy_id = encrypt_decrypt_password($master_policy_id, 'D');
        $creditor_id = encrypt_decrypt_password($creditor_id, 'D');

        /*$existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->limit(1)->get()->result_array();

        if(!empty($existing_members)){

            $si_type = $existing_members[0]['si_type_id'];
            $where = "mp.plan_id = $plan_id AND mp.policy_sub_type_id != 1 AND mp.isactive = 1";

            $policy_data_arr = $this->db->select('mp.creditor_id, mp.policy_sub_type_id, mp.insurer_id, mp.policy_start_date, mp.policy_end_date, mp.is_optional, mp.is_combo, mp.premium_type, mp.max_member_count, mpdt.sum_insured, mpdt.premium_rate, mppr.sum_insured, mppr.premium_rate, mpppr.policy_rate, mpppr.numbers_of_ci, mpppr.sum_insured')
            ->from('master_policy mp')
            ->join('master_policy_premium_permile mpppr', 'mpppr.master_policy_id = mp.policy_id AND mpppr.isactive = 1')
            ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
            ->join('master_per_day_tenure_premiums mpdt', 'mpdt.master_policy_id = mp.policy_id AND mpdt.isactive = 1')
            ->where($where)
            ->get()
            ->result_array();
        }*/

        $data['plan_details'] = $this->customerapimodel->getProductDetailsAll($plan_id);
        $data['features'] = $this->customerapimodel->getfeaturesbyplan($plan_id, $creditor_id);

        $data['master_policy_details'] = $this->customerapimodel->getPolicySubTypePlanCreditor($plan_id, $creditor_id);
        $data['mandatory_if_not_selected'] = [];

        foreach ($data['master_policy_details'] as $policy) {
            // if ($policy->policy_sub_type_id == 1) { // GHI
            //  $data['sum_insured_type_1'] = $this->customerapimodel->getSumInsureData($policy->policy_id);
            // }

            if ($policy->policy_sub_type_id == 2) { // GPA

                $sum_insured_type_2 = $this->customerapimodel->getSumInsureData($policy->policy_id);

                /*if(empty($sum_insured_type_2)){

                    $sum_insured_type_2 = $this->customerapimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
                }*/

                $data['sum_insured_type_2'] = $sum_insured_type_2;
            }

            if ($policy->policy_sub_type_id == 3) { // Group Critical Illness, GCI

                $sum_insured_type_3 = $this->customerapimodel->getSumInsureData($policy->policy_id);
                /*if(empty($sum_insured_type_3)){

                    $sum_insured_type_3 = $this->customerapimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
                }*/

                $data['sum_insured_type_3'] = $sum_insured_type_3;

                $data['numbers_of_ci'] = $this->customerapimodel->getNoOfCI($policy->policy_id);
            }

            if ($policy->policy_sub_type_id == 5) { // Super Topup
                $data['sum_insured_type_5_1'] = $this->customerapimodel->getSumInsureData($policy->policy_id);
                $data['sum_insured_type_5_2'] = $this->customerapimodel->getSumInsureDataDeductible($policy->policy_id);
            }


            if ($policy->policy_sub_type_id == 6) { // Hospi Cash
                if ($policy->basis_id == 7) {
                    $data['sum_insured_type_6'] = $this->customerapimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
                } else {
                    $data['sum_insured_type_6'] = $this->customerapimodel->getSumInsureData($policy->policy_id);
                }
            }

            $data['mandatory_if_not_selected'][$policy->policy_id] = $this->customerapimodel->getdata('master_policy_mandatory_if_not_selected_rules', 'master_policy_id, dependent_on_policy_id', 'master_policy_id = ' . $policy->policy_id);
        }
        $policy_sub_type_id_map = [];

        foreach ($data['plan_details'] as $plandetail) {
            $plandetail->family_construct = $this->customerapimodel->getPolicyFamilyConstruct($plandetail->policy_id);
            $policy_sub_type_id_map[$plandetail->policy_id] = $plandetail->policy_sub_type_code;
        }
        foreach ($data['plan_details'] as $plandetail) {
            $plandetail->policy_premium = $this->customerapimodel->getPolicyPremium($plandetail->policy_id);
        }


        $data['status'] = 200;

        echo json_encode($data);
        exit;
    }

    public function getQuoteCompareData()
    {
        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];


        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');

        //$plan_arr = $this->input->post('plans');

        //   foreach($plan_arr as $key=>$single_plan){
        //     $plan_arr[$key] = encrypt_decrypt_password($single_plan, "D");
//}

        $covers_arr = $this->input->post('covers');
//print_r($covers_arr);
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            //->where($member_where)
            ->get()
            ->result_array();
        // echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];
        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $policy_id)
            ->get()
            ->row_array();

        $consider_adult = $policy_det['is_consider_adult'];
        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            if ($value['member_age'] >= '18'  && strpos($value['member_age'],'months') === false && $consider_adult == 1){
                $value['is_adult'] = 'Y';
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);

        $adult_count = $arr['adult_count'];
        $child_count = $arr['child_count'];
        $response_data = [];
        $total_premium = 0;
        if (!empty($covers_arr)) {
            foreach ($covers_arr as $all_details) {

                $policy_id = encrypt_decrypt_password($all_details['policy_id'], "D");
                $get_sa_data = $this->customerapimodel->getSumInsureData($policy_id);
                foreach ($get_sa_data as $key => $single_sa_data) {
                    // json_decode($single_sa_data, TRUE);
                    $single_sa_data = (array)$single_sa_data;
                    // echo $single_sa_data['sum_insured'];
                    $get_sa_data[$key] = $single_sa_data['sum_insured'];
                }
                $plan_id = $all_details['plan_id'];
                $cover = $all_details['cover'];
                $plan_data = $this->db->select('mpst.policy_sub_type_name,mp.policy_id,mc.creditor_logo,mpl.plan_id, mi.insurer_name, mp.insurer_id, mppr.sum_insured, mppr.premium_rate, mpl.plan_name, mpl.creditor_id, mc.creaditor_name, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) As tenure, mpl.plan_name')
                    ->from('master_plan mpl')
                    ->join('master_ceditors mc', 'mc.creditor_id = mpl.creditor_id')
                    ->join('master_policy mp', 'mp.plan_id = mpl.plan_id AND mp.isactive = 1')
                    ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
                    ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
                    ->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id')
                    ->where('mpl.plan_id', $plan_id)
                    ->where('mppr.sum_insured', $cover)
                    ->get()
                    ->result_array();
                if (!empty($plan_data)) {


                    foreach ($plan_data as $key => $value) {

                        $creditor_id = $value['creditor_id'];
                        $plan_id = $value['plan_id'];
                        $response_data[$plan_id]['creditor_logo'] = $value['creditor_logo'];
                        $response_data[$plan_id]['premium'] = $value['premium_rate'];
                        $response_data[$plan_id]['sum_insured_data'] = $value['sum_insured'];
                        $response_data[$plan_id]['insurer_name'] = $value['insurer_name'];
                        $response_data[$plan_id]['tenure'] = $value['tenure'];
                        $response_data[$plan_id]['plan_name'] = $value['plan_name'];
                        $response_data[$plan_id]['creditor_name'] = $value['creaditor_name'];
                        $response_data[$plan_id]['plan_id'] = $plan_id;
                        $response_data[$plan_id]['policy_id'] = $value['policy_id'];

                        $data['master_policy_details'] = $this->customerapimodel->getPolicySubTypePlanCreditor($plan_id, $creditor_id);
                        $total_premium = 0;
                        foreach ($data['master_policy_details'] as $policy) {


                            // exit;
                            $response_data[$plan_id]['sum_insured'] = $get_sa_data;
                            $response = $this->getAllpremium($lead_id, $customer_id, $policy->policy_id, $value['sum_insured']);


                            if ($response['amount'] != '') {
                                $policy_id_set = $policy->policy_id;
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['policy_name'] = $policy->policy_sub_type_name;
                                if (($policy->is_combo == 0 && $policy->is_optional == 0) || ($policy->is_combo == 1 && $policy->is_optional == 0)) {
                                    $total_premium = $total_premium + $response['amount'];
                                }
                                $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id_set'")->row_array();
                                if (!empty($sel_policy_plan)) {
                                    if ($policy->is_optional == 1) {
                                        $total_premium = $total_premium + $response['amount'];
                                    }

                                    $response_data[$plan_id]['add_on'][$policy->policy_id]['already_avail'] = 1;
                                } else {
                                    $response_data[$plan_id]['add_on'][$policy->policy_id]['already_avail'] = 0;
                                }
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['premium'] = $response['amount'];
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['is_combo'] = $policy->is_combo;
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['is_optional'] = $policy->is_optional;
                            }


                        }
                        $response_data[$plan_id]['total_premium'] = $total_premium;
                    }
                }

            }
        }


        echo json_encode($response_data);
        exit;

    }

    public function updatePremium()
    {
        $plan_sa = $this->input->post('plan_sa');
        echo '5678';
    }

    public function getLeadDetails()
    {

        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');

        $data = $this->db->select('mc.lead_id, mc.customer_id')
            ->from('lead_details ld')
            ->join('master_customer mc', 'mc.lead_id = ld.lead_id', 'inner')
            ->where(['ld.lead_id' => $lead_id, 'mc.customer_id' => $customer_id])->get()->result_array();

        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Record inserted successfully", 'data' => $data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    public function getNomineeRelation()
    {
        $data = $this->db->get("master_nominee_relations")->result_array();

        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    public function updateNomineeDetails()
    {
        $fetchData = $this->db->get_where("proposal_details", array('lead_id' => $_POST['lead_id'], 'customer_id' => $_POST['customer_id'], 'trace_id' => $_POST['trace_id']))->row_array();
        $lname = "";
        $nominee_name = explode(' ', $_POST['nominee_name']);
        $len = count($nominee_name);
        $fname = $nominee_name[0];
        if ($len > 1) {
            $lname = $nominee_name[$len - 1];
        }
        if ($fetchData) {


            $data = $this->db->update(
                'proposal_details',
                [
                    'nominee_relation' => $_POST['nominee_relation'],
                    'nominee_first_name' => $fname,
                    'nominee_last_name' => $lname,
                    'nominee_dob' => !empty($_POST['nominee_dob'])?date("Y-m-d", strtotime($_POST['nominee_dob'])):null,
                    'nominee_contact' => $_POST['nominee_contact_number'],
                    'plan_id' => $_POST['plan_id'],
                ],
                [
                    'lead_id' => $_POST['lead_id'],
                    'customer_id' => $_POST['customer_id'],
                    'trace_id' => $_POST['trace_id'],

                ]
            );
           // echo $this->db->last_query();die;
            $dt=[
                'nominee_relation' => $_POST['nominee_relation'],
                'nominee_first_name' => $fname,
                'nominee_last_name' => $lname,
                'nominee_dob' => date("Y-m-d", strtotime($_POST['nominee_dob'])),
                'nominee_contact' => $_POST['nominee_contact_number'],
                'plan_id' => $_POST['plan_id'],
            ];
            insert_application_log($_POST['lead_id'], "nominee_saved", json_encode($dt), json_encode(array("response" => "Nominee Saved")),0 );
        } else {
            $data = [
                'plan_id' => $_POST['plan_id'],
                'lead_id' => $_POST['lead_id'],
                'customer_id' => $_POST['customer_id'],
                'trace_id' => $_POST['trace_id'],
                'nominee_relation' => $_POST['nominee_relation'],
                'nominee_first_name' => $fname,
                'nominee_last_name' => $lname,
                'nominee_dob' => date("Y-m-d", strtotime($_POST['nominee_dob'])),
                'nominee_contact' => $_POST['nominee_contact_number'],
            ];
            $insertDetails = $this->db->insert("proposal_details", $data);
            insert_application_log($_POST['lead_id'], "nominee_saved", json_encode($data), json_encode(array("response" => "Nominee Saved")),0 );
        }
        echo json_encode(true);
    }

    public function getProposalDetails()
    {
        //$trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');

        // $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        // $proposal_details = $this->db->get_where("proposal_policy",["lead_id" =>$lead_id, "trace_id"=> $trace_id ])->result_array();
        $proposal_details = $this->db->get_where("proposal_policy", ["lead_id" => $lead_id])->result_array();

        if (!empty($proposal_details)) {

            $data['proposal_details'] = $proposal_details;
            $data['status'] = 200;
            echo json_encode($data);
            exit;
        } else {

            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
            echo json_encode($proposal_details);
            exit;
        }
    }

    public function getPremium()
    {


        $data = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $policy_id = $this->input->post('policy_id');

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
       
        $cover = $this->session->userdata('cover');
        if(!empty(encrypt_decrypt_password($policy_id, 'D'))){
            $policy_id = encrypt_decrypt_password($policy_id, 'D');
        }


        $data['trace_id'] = $trace_id;
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
//echo $this->db->last_query();exit;
        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
// echo json_encode($data);exit;
        $adult_age = [];
        $policy_det = $this->db->select('mpo.is_consider_adult')
            ->from("master_policy mpo")
            ->where('mpo.policy_id = ' . $policy_id)
            ->get()
            ->row_array();

        $consider_adult = $policy_det['is_consider_adult'];
        foreach ($data['member_ages'] as $key => $value) {


            //if(!$si_type)
            $si_type = $value['si_type_id'];
            if ($value['member_age'] >= '18'  && strpos($value['member_age'],'months') === false && $consider_adult == 1){
                $value['is_adult'] = 'Y';
            }
            if ($value['is_adult'] == 'Y') {
                array_push($adult_age, $value['member_age']);
                $arr['adult_count']++;
            } else {
                $arr['child_count']++;
            }

            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            //if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';
            //}

        }

        // echo json_encode($data);exit;
        if ($data['member_ages'][0]['si_type_id'] == 2) {
            $max_age = max($adult_age);
            $where = "mpp.plan_id = '" . $_POST['plan_id'] . "' AND mp.policy_id IN (" . $policy_id . ") AND mp.policy_sub_type_id = 1 AND mp.isactive = 1 AND mppr.adult_count = " . $arr['adult_count'] . " AND mppr.child_count = " . $arr['child_count'] . " and mppr.sum_insured = '" . $this->input->post('cover') . "' ";
        } else {
            $where = "mpp.plan_id = '" . $_POST['plan_id'] . "' AND mp.policy_id IN (" . $policy_id . ") AND mp.isactive = 1 AND mppr.sum_insured = '" . $this->input->post('cover') . "' ";
        }


        $master_policy_arr = $this->db->select("mppr.min_age,mppr.max_age,mp.policy_id,mp.plan_id,mp.insurer_id,mp.policy_start_date,mp.policy_end_date,mpp.plan_name,mi.insurer_name,mppr.sum_insured, mppr.premium_rate, mpp.creditor_id, FLOOR(DATEDIFF( `mp`.`policy_end_date`, mp.policy_start_date)/364) as duration, mc.creaditor_name, mc.creditor_logo")
            ->from('master_policy mp')
            ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
            ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
            ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
            ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
            ->where($where)
            ->get()->result_array();
        //    echo json_encode($this->db->last_query());exit;
//       var_dump($master_policy_arr);exit;
        if (!empty($master_policy_arr)) {
            //check if premium type is member age
            $sibasis = $this->db->get_where("master_policy_premium_basis_mapping", ["master_policy_id" => $policy_id])->row_array()['si_premium_basis_id'];
            //echo json_encode($sibasis);exit;
            if ($sibasis == 4) {

                $premium = 0;
                foreach ($data['member_ages'] as $key => $value) {
                    $member_age = $value['member_age'];
                    $premiumData = $this->db->get_where("master_policy_premium", ["sum_insured" => $this->input->post('cover'), "master_policy_id" => $policy_id])->result_array();


                    foreach ($premiumData as $valueage) {
                        if ($member_age >= $valueage['min_age'] && $member_age <= $valueage['max_age']) {
                            $premium = $premium + $valueage['premium_rate'];
                        }
                    }
                }
                //echo json_encode($premium);exit;
                $master_policy_arr[0]['premium_rate'] = $premium;
            } else if ($sibasis == 3) {

                $premium = 0;
                foreach ($master_policy_arr as $value) {
                    // echo json_encode($max_age.'---'.$value['min_age'].'----'.$value['max_age']);//exit;
                    if ($max_age >= $value['min_age'] && $max_age <= $value['max_age']) {
                        $premium = $value['premium_rate'];
                    }
                }
                $master_policy_arr[0]['premium_rate'] = $premium;
                $master_policy_arr[0]['total_premium'] = $premium;
            }

            //fetch premium from policy_member_plan_details
            $res = $this->db->get_where('policy_member_plan_details', ["customer_id" => $customer_id])->result_array();
            //     echo json_encode($this->db->last_query());exit;
            if (!empty($res)) {
                $prem = 0;
                $total_premium = 0;
                //var_dump($res);
                foreach ($res as $key => $valprem) {
                    if ($valprem['additional_plan_flag'] == 0) {
                        $prem += $valprem['premium'];
                    }
                    $total_premium = $valprem['total_premium'];
                }
                $master_policy_arr[0]['premium_rate'] = $prem;
                $master_policy_arr[0]['total_premium'] = $total_premium;

            }
//                echo '<pre>';
            //    print_r($master_policy_arr);
//                 echo json_encode($master_policy_arr);exit;
            $data['policy_data'] = $master_policy_arr[0];
            $data['status'] = 200;
            echo json_encode($data);
            exit;
        } else {

            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }

    }

    public function getNomineeDetails()
    {
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $nq = "SELECT *,mnr.name AS nominee_relation_name
                FROM proposal_details AS pd,master_nominee_relations AS mnr
                WHERE pd.nominee_relation = mnr.id AND pd.`lead_id` = '" . $lead_id . "' AND pd.`customer_id` = '" . $customer_id . "' 
                AND pd.`trace_id` = '" . $trace_id . "' AND pd.`plan_id` = '" . $_POST['plan_id'] . "'";
        //$fetchData = $this->db->get_where("proposal_details", array('lead_id' => $lead_id, 'customer_id' => $customer_id,'trace_id' => $trace_id,'plan_id' => $_POST['plan_id']))->row_array();
        $fetchData = $this->db->query($nq)->row_array();
        // echo json_encode($this->db->last_query());exit;
        if ($fetchData) {
            $data['policy_data'] = $fetchData;
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
        }
        echo json_encode($data);
        exit;
    }

    public function validate_member_age($age_type, $age, $policy_id, $member_type_id, $memberType = '')
    {
        $get_arr = [];
        $data = array();
        $q = "SELECT * FROM master_policy_family_construct WHERE master_policy_id = '" . $policy_id . "' AND member_type_id = " . $member_type_id . " AND isactive = 1";

        $policyAge_details = $this->db->query($q)->row_array();
//            var_dump($policyAge_details);
        if ($this->db->affected_rows() > 0) {
            $q_rel = "SELECT * FROM family_construct WHERE id = '$member_type_id'";

            $policyDet = $this->db->query($q_rel)->row_array();

            if ($policyDet['is_adult'] == 'N') {

                if ((int)$age >= (int)$policyAge_details['member_min_age_days'] && $age_type=='days' ) {

                    $data['status'] = 'success';
                    $data['message'] = "success";
                } else if((int)$age <= (int)$policyAge_details['member_max_age'] && $age_type=='years' ){
                    $data['status'] = 'success';
                    $data['message'] = "success";
                }else {
                    //echo "in";exit;
                    $data['status'] = 'error';

                    if ($member_type_id == 5 || $member_type_id == 6) {
                        $data['message'] = "Sorry this plan covers kids from the age of " . $policyAge_details['member_min_age_days'] . " days to " . $policyAge_details['member_max_age'] . " years.";
                    } else {
                        $data['message'] = "Sorry this plan covers adults from the age of " . $policyAge_details['member_min_age'] . " years to " . $policyAge_details['member_max_age'] . " years.";
                    }


                }
            }

            else {
//
                if ((int)$age >= (int)$policyAge_details['member_min_age'] && (int)$age <= (int)$policyAge_details['member_max_age']) {
                    $data['status'] = 'success';
                    $data['message'] = "success";

                } else {
                    //echo "in";exit;
                    $data['status'] = 'error';

                    if ($member_type_id == 5 || $member_type_id == 6) {
                        $data['message'] = "Sorry this plan covers kids from the age of " . $policyAge_details['member_min_age'] . " year(s) to " . $policyAge_details['member_max_age'] . " years.";
                    } else {
                        $data['message'] = "Sorry this plan covers adults from the age of " . $policyAge_details['member_min_age'] . " years to " . $policyAge_details['member_max_age'] . " years.";
                       //return $data;
                    }
                  //  print_r($data);
                   // return $data;

                }
            }
        }

        return $data;
    }
public   function verify_request_customer($token)
    {
        // Get all the headers
        //$headers = $this->input->request_headers();
        // Extract the token
        //$token = $headers['Authorization'];
        // Use try-catch
        // JWT library throws exception if the token is not valid
        try {
            // Validate the token
            // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
                exit;
            } else {
                return $data;
            }
        } catch (Exception $e) {

            // Token is invalid
            // Send the unathorized access message

            return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
            exit;
        }
    }

public function policyIssuance()
{
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
$api_data = json_decode(file_get_contents('php://input') , true);
if(!empty($api_data)){
        $checkToken =$this->verify_request_customer($api_data['PolicyCreationRequest']['utoken']);
        if (!empty($checkToken->username)) {
$req_data['lead_id']= $api_data['PolicyCreationRequest']['LeadId'];
$req_data['txt_date']= $api_data['PolicyCreationRequest']['TransactionRcvdDate'];
$req_data['txt_number']= $api_data['PolicyCreationRequest']['TransactionNumber'];
 $update_payment_status = $this->updateProposalStatus($req_data);
 $policyResponse = $update_payment_status;
            $policyResponse1=array();
 foreach ($policyResponse['PolicyCreationResponse'] as $row){
unset($row['gross_premium']);
     $policyResponse1[]=$row;
 }
            $master_policy_id=$this->db->query("select master_policy_id from api_proposal_response where lead_id=".$lead_id)->row()->master_policy_id;
            $coi_type=$this->db->query("select coi_type from master_plan where plan_id=(select plan_id from master_policy where policy_id=".$master_policy_id.")")->row()->coi_type;
            if($coi_type == 1){
                $lead_id = encrypt_decrypt_password($api_data['PolicyCreationRequest']['LeadId']);
                $policyResponse1['COI_URL']='http://fyntunecreditoruat.benefitz.in/quotes/success_view/'.$lead_id;
                $sendMail= $this->sendMail($policyResponse1['COI_URL']);
                $policyResponse1['mail']=$sendMail;
            }

 print_R(json_encode($policyResponse1));die;
        }
        else{
            echo $checktoken;
        }


}


}

function sendMail($url){
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtppro.zoho.in';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->SMTPSecure = "tls";
        $mail->Username   = 'noreply@fyntune.com';                     //SMTP username
        $mail->Password   = 'Fyntune9001#';                               //SMTP password
       // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@fyntune.com', 'Mailer');
        $mail->addAddress('poojalote123@gmail.com', 'Test');     //Add a recipient

        $body="<p>Hello,</p>
<p>Please click on below given link to get your certificate of Issuance.</p>
<p>".$url."</p>
<p>Regards,<br>
Fyntune Team.
</p>
";
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Here is the subject';
        $mail->Body    = $body;
       // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
       return 'Message has been sent';
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
public  function updatePaymentgadget(){

         $lead_id=$this->input->post('lead_id');
        $TransactionNumber=$this->input->post('TransactionNumber');
        $TransactionRcvdDate=$this->input->post('TransactionRcvdDate');
    $req_data['lead_id']= encrypt_decrypt_password($lead_id, 'D');
    $req_data['txt_date']=$TransactionRcvdDate;
    $req_data['txt_number']= $TransactionNumber;
//var_dump($req_data);die;
        $result=$this->updateProposalStatus($req_data);
   // var_dump($result);die;
        echo json_encode($result);
        exit;
}

    public function updateProposalStatus($data = '')
    {

       // echo 123;die;
       
    if(!empty($data))
    {//echo 345;
        $_POST['lead_id'] = $data['lead_id'];
        $txt_date = $data['txt_date'];
        $txt_num = $data['txt_number'];
        $api = 'API';
        //echo 123;die;
    }
    else
    {//echo 244;
                $txt_date = date("Y-m-d H:i:S");
                $txt_num = $_POST['pg_response']['razorpay_payment_id'];
                $api = '';
    }
    $lead_query = $this->db->query("select l.lead_id from lead_details as l,proposal_payment_details as ppd where l.lead_id = ppd.lead_id and l.lead_id = '".$_POST['lead_id']."'")->row_array();
       
        if(!empty($lead_query))

    {
       // echo 123;die;
        $this->db->query("UPDATE proposal_policy SET status = 'Payment-Done' WHERE lead_id = '" . $_POST['lead_id'] . "'");
        $this->db->query("UPDATE lead_details SET status = 'Customer-Payment-Received' WHERE lead_id = '" . $_POST['lead_id'] . "'");
        $paymentData = $this->db->query("select * from proposal_payment_details where lead_id = '".$_POST['lead_id']."'")->row_array();
        if($paymentData['payment_status']!='Success'){
            $this->db->query("UPDATE proposal_payment_details SET payment_status = 'Success', proposal_status = 'PaymentReceived' ,payment_date = '" . $txt_date . "',transaction_date = '" . $txt_date . "',transaction_number = '" . $txt_num . "', remark = 'PaymentReceived' WHERE lead_id = '" . $_POST['lead_id'] . "'");
        }
  
        //echo json_encode("UPDATE lead_details SET status = 'Customer-Payment-Received' WHERE lead_id = '".$_POST['lead_id']."'");exit;
        $res = $this->saveApiProposalResponse($_POST['lead_id'],$api);
      
 print_r($res);exit;

        if($api = 'API')
        {
            return $res;
        }else{

        echo json_encode($res);
        }
    }
    else{

                    $response = ["StatusCode" =>400,"Status"=>"Error","Message"=>"Data Not Found","PolicyCreationResponse"=>""];
            return $response;


    }
        exit;
    }
    public function generate_coi($data)
    {
        $policy_subtype_name=$data[0];
        $short_code=$data[1];
        $lead_id=$data[2];
        $master_policy_id=$data[3];
        $is_api=$this->db->query("select is_api_lead from lead_details where lead_id=".$lead_id)->row()->is_api_lead;
      //echo $is_api;die;
       /* if($is_api == 1){
            $coi_no = $short_code."_".$policy_subtype_name . '-XL-AS-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        }else{*/
//echo $master_policy_id;die;
            $get_policy_data=$this->db->query("select start_series_number,end_series_number,mpl.plan_id,
    (select coi_start_series from master_plan mp where mp.plan_id=mpl.plan_id) as coi_start_series,
    (select series_digit_count from master_plan mp where mp.plan_id=mpl.plan_id) as series_digit_count,
    (select duplicate_coi_allow from master_plan mp where mp.plan_id=mpl.plan_id) as duplicate_coi_allow
    from master_policy mpl where policy_id=".$master_policy_id)->row();
        //    echo $this->db->last_query();die;
            $start_series_number=$get_policy_data->start_series_number;
            $end_series_number=$get_policy_data->end_series_number;
            $coi_start_series=$get_policy_data->coi_start_series;
            $series_digit_count=$get_policy_data->series_digit_count;
            $duplicate_coi_allow=$get_policy_data->duplicate_coi_allow;
            if(strlen($start_series_number) != $series_digit_count){
                $start_series_number=  str_pad( $start_series_number, $series_digit_count, "0", STR_PAD_LEFT );
            }
            $plan_id=$get_policy_data->plan_id;
            $getPolicies=$this->db->query("select group_concat(policy_id) as policies from master_policy where plan_id=".$plan_id)->row()->policies;
            $query_apr=$this->db->query("select group_concat(lead_id SEPARATOR ',') as all_leads  from api_proposal_response where certificate_number != '' and master_policy_id=".$master_policy_id." order by pr_api_id desc");
            $result=$query_apr->row()->all_leads;
            if(!is_null($result)){
                $all_leads=$result;
                $last_lead=$this->db->query("select lead_id from lead_details where lead_id in(".$all_leads.") and  date(createdon)>=date('2023-04-15') order by createdon desc limit 1 ");
                if($this->db->affected_rows() > 0){

                    $last_lead=$last_lead->row()->lead_id;

                    if(empty($coi_start_series) || is_null($coi_start_series)){
                        $getLastCOI=$this->db->query("select certificate_number from api_proposal_response where lead_id=".$last_lead)->row();
                        $cer=$getLastCOI->certificate_number;
                        $arr=explode("-",$cer);
                      echo  $last_number=$arr[3];
                       echo  $new_number=$last_number+1;
                    }else{
                        $getLastCOI=$this->db->query("select certificate_number from api_proposal_response where master_policy_id in (".$getPolicies.") order by pr_api_id desc limit 1")->row();
                        $cer=$getLastCOI->certificate_number;
                        if(empty($cer) || is_null($cer)){
                            $last_number=$start_series_number;
                            $new_number=$last_number;
                        }else{
                             $last_number = substr($cer, -($series_digit_count));
                            $new_number=$last_number+1;
                            if(strlen($new_number) != $series_digit_count){
                                $new_number=  str_pad( $new_number, $series_digit_count, "0", STR_PAD_LEFT );
                            }
                        }
                    }


                    if($new_number > $end_series_number){
                        if($duplicate_coi_allow == 1){
                            $new_number=$start_series_number;
                            $coi_no=$coi_start_series.$new_number;
                        }else{
                            return false;
                        }

                    }else{
                        if(empty($coi_start_series) || is_null($coi_start_series)){
                            $coi_no = $short_code."_".$master_policy_id."_".$policy_subtype_name . '-XL-AS-' . $new_number;
                        }else{
                            $coi_no=$coi_start_series.$new_number;
                        }


                    }
                }else{

                    if(empty($coi_start_series) || is_null($coi_start_series)){
                        $coi_no = $short_code."_".$master_policy_id."_".$policy_subtype_name . '-XL-AS-' . 1;
                    }else{
                        $coi_no=$coi_start_series.$start_series_number;
                    }
                }

            }else{
                if(empty($coi_start_series) || is_null($coi_start_series)){
                    $coi_no = $short_code."_".$master_policy_id."_".$policy_subtype_name . '-XL-AS-' . 1;
                }else{
                    $coi_no=$coi_start_series.$start_series_number;
                }
            }
    //    }
//echo $coi_no;die;



        $proposal_id = $this->db->select('*')
            ->from('api_proposal_response')
            ->where('certificate_number', $coi_no)
            ->where_in('master_policy_id', $getPolicies)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {
            if($duplicate_coi_allow == 1){

            }else{
                //$this->generate_coi($data);
            }

        }
        //print_r($coi_no);
        return $coi_no;
    }
    public function generate_coiOLD($policy_subtype_name,$short_code)
    {
        $coi_no = $short_code."_".$policy_subtype_name . '-XL-AS-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        $proposal_id = $this->db->select('*')
            ->from('api_proposal_response')
            ->where('certificate_number', $coi_no)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {

            $this->generate_coi();
        }
        return $coi_no;
    }

    function updateProposalPolicyStatus($column_name, $column_value, $status)
    {
        $proposalPolicy = $this->db->query("SELECT proposal_policy_id FROM proposal_policy WHERE `$column_name` = '$column_value'")->row_array();
        if ($proposalPolicy > 0) {
            $updateArr = ["status" => $status];
            $this->db->where($column_name, $column_value);
            $this->db->update("proposal_policy", $updateArr);
            $insert_id = $proposalPolicy['proposal_policy_id'];
        }
        return true;
    }

    function calculatePerMilePremium($policy_id, $age, $sum_insured, $tenure)
    {


        $request = [
            'policy_id' => $policy_id,
            'age' => $age,
            'sum_insured' => $sum_insured,
            'tenure' => $tenure,
        ];

        if ($policy_sub_type_id == 3) {
            $request['number_of_ci'] = !empty($number_of_ci) ? $number_of_ci : 0;
        }

        $result = $this->apimodel->getPerMileWisePremium($request);

        // If cover is individual append the member type to the policy code


        return $result['rate'];
    }

    function saveApiProposalResponse($lead_id,$api)
    {

$api_status = 0;
        $policyDataArr = $this->db->get_where("proposal_policy", ["lead_id" => $lead_id])->result_array();
        foreach ($policyDataArr as $key => $policyData) {
          //  echo 1234;die;
            $policy_subtype_name = $this->db->get_where("master_policy_sub_type", ["policy_sub_type_id" => $policyData['policy_sub_type_id']])->row_array();
            $short_code=$this->db->query("select short_code from master_ceditors where creditor_id =(select creditor_id from master_policy where policy_id=".$policyData['master_policy_id'].")")->row()->short_code;
        //  print_R($policy_subtype_name);die;
            $customer_id = $this->db->get_where("master_customer", ["lead_id" => $lead_id])->row_array()['customer_id'];
//echo $policyData['master_policy_id'];die;
          //  echo $lead_id;die;

            $data=array(
                $policy_subtype_name['code'],$short_code,$lead_id,$policyData['master_policy_id']
            );
            //$CertificateNumber = $this->generate_coi($policy_subtype_name['code'],$short_code);
        //    print_r($data);die;
            //get coi type
            $coi_type=$this->db->query("select coi_type from master_plan where plan_id=(select plan_id from master_policy where policy_id=".$policyData['master_policy_id'].")")->row()->coi_type;
           //echo $coi_type;die;
            if($coi_type == 1){
               $CertificateNumber = $this->generate_coi($data);
                // echo 1234;die;
               //  print_r($CertificateNumber);die;
               if($CertificateNumber == false){
                   //"You are not able to generate new COI as series of COI is ended."
                   $response = ["StatusCode" =>301,"Status"=>"Error","Message"=>"You are not able to generate new COI as series of COI is ended."];
                   return $response;
                   exit;
               }
               $CertificateNumberResponse=$CertificateNumber;
           }else{
               $CertificateNumber='';
               $CertificateNumberResponse='You will receive your Certificate of issuance shortly on your mail ID.';
           }

            //echo 123;die;

           // $CertificateNumber = $this->generate_coi($policy_subtype_name['code'],$short_code);
            $startDate = date('Y-m-d');
            $EndDate = date('Y-m-d', strtotime($startDate . ' + 364 days'));

            if(!empty($policyData['tax_amount'])){
                $GrossPremium = isset($policyData['tax_amount']) ? $policyData['tax_amount'] : '';
            }else{
                $GrossPremium = isset($policyData['premium_amount']) ? $policyData['premium_amount'] : '';
            }



            $lead_id = isset($policyData['lead_id']) ? $policyData['lead_id'] : '';
            $proposal_policy_id = isset($policyData['proposal_policy_id']) ? $policyData['proposal_policy_id'] : '';
            $policy_sub_type_id = isset($policyData['policy_sub_type_id']) ? $policyData['policy_sub_type_id'] : '';
            $customer_id = isset($customer_id) ? $customer_id : '';
            $master_policy_id = isset($policyData['master_policy_id']) ? $policyData['master_policy_id'] : '';
            $ProposalNumber = isset($policyData['proposal_no']) ? $policyData['proposal_no'] : '';

            $request_arr = array(
                "lead_id" => $lead_id,
                "certificate_number" => $CertificateNumber,
                "proposal_no" => $ProposalNumber,
                "policy_sub_type_id" => $policy_sub_type_id,
                "master_policy_id" => $master_policy_id,
                "gross_premium" => $GrossPremium,
                //"status" => $statusRes,
                "start_date" => date('Y-m-d H:i:s', strtotime($startDate)),
                "end_date" => date('Y-m-d H:i:s', strtotime($EndDate)),
                "created_date" => date('Y-m-d H:i:s'),
                "proposal_policy_id" => $proposal_policy_id,
                "customer_id" => $customer_id,
            );
         //   echo 123;die;
            // echo json_encode($request_arr);exit;
//echo $lead_id;die;
            $apiProposalResponse = $this->db->query("SELECT pr_api_id FROM api_proposal_response 
            WHERE lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
            AND customer_id='$customer_id' AND master_policy_id='$master_policy_id'
            AND policy_sub_type_id='$policy_sub_type_id'")->row_array();
//print_r($apiProposalResponse);die;

            if ($apiProposalResponse > 0) {
               // echo 123;die;
                $api_status = 1;
              /*  $this->db->where("lead_id", $lead_id);
                $this->db->where("proposal_policy_id", $proposal_policy_id);
                $this->db->where("customer_id", $customer_id);
                $this->db->where("master_policy_id", $master_policy_id);
                $this->db->where("policy_sub_type_id", $policy_sub_type_id);
                $this->db->update("api_proposal_response", $request_arr);
                $insert_id = $apiProposalResponse['pr_api_id'];*/

            } else {
             //   echo 123;die;
                $api_status = 0;
                //echo json_encode('1111');exit;
                $this->db->insert("api_proposal_response", $request_arr);
                insert_application_log($lead_id, "api_proposal_response", json_encode($request_arr), json_encode(array("response" => "Api proposal response Saved")), 0);
                $insert_id = $this->db->insert_id();
            }
            $policyIssuanceResponse[] =['plan_name' => $policy_subtype_name['policy_sub_type_name'],'certificate_number'=>$CertificateNumberResponse,'gross_premium'=>$GrossPremium,'policy_start_date'=> date('Y-m-d H:i:s', strtotime($startDate)),'policy_expiry_date'=>date('Y-m-d H:i:s', strtotime($startDate))];
        }
if($api = 'API'){
    if($api_status == 0)
    {
                $response = ["StatusCode" =>200,"Status"=>"Success","Message"=>"Success","PolicyCreationResponse"=>$policyIssuanceResponse];
    }else{
                        $response = ["StatusCode" =>301,"Status"=>"Error","Message"=>"Policy Already Generated","PolicyCreationResponse"=>$policyIssuanceResponse];

    }

            return $response;


}
else{
    return $CertificateNumber;
}
        //$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Full-Quote-Done");
    }

    public function getCOidetails()
    {
        /*$policy_sub_type_id=$this->db->query("select (select group_concat(policy_sub_type_id) from master_policy mp where mp.plan_id=pd.plan_id) as policy_sub_type_id from proposal_details pd where lead_id=".$_POST['lead_id'])->row()->policy_sub_type_id;
        $gadgetNames=$this->db->query("select Group_concat(gadget_name) as gadget_name from proposal_details where policy_sub_type_id in(".$policy_sub_type_id.")")->row()->gadget_name;
       echo $gadgetNames;die;*/
        //echo json_encode("SELECT api.certificate_number,pd.transaction_number FROM proposal_payment_details AS pd,api_proposal_response AS api WHERE pd.lead_id = api.lead_id and pd.lead_id = ".$_POST['lead_id']);exit;
        $data = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(api.certificate_number)) certificate_number,pd.transaction_number FROM proposal_payment_details AS pd,api_proposal_response AS api WHERE pd.lead_id = api.lead_id and pd.lead_id = " . $_POST['lead_id'] . " GROUP BY pd.transaction_number")->row_array();
       // echo $this->db->last_query();exit;
     //   $data['gadgetNames']=$gadgetNames;
        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    public function getCOiinfo()
    {
//echo 12553;die;
        // echo json_encode($_POST['lead_id']);exit;
        //  echo json_encode("SELECT api.certificate_number,pd.transaction_number FROM proposal_payment_details AS pd,api_proposal_response AS api WHERE pd.lead_id = api.lead_id and pd.lead_id = ".$_POST['lead_id']);exit;

        $lead_details = $this->db->query("select plan_id from lead_details where lead_id = " . $_POST['lead_id'])->row_array();
        $plan = $this->db->query("SELECT mp.is_single_coi,mp.coi_type from master_plan  mp  join master_policy mpi on mpi.plan_id=mp.plan_id WHERE mp.plan_id=".$lead_details['plan_id']." AND mpi.isactive=1")->result_array();

        if(!empty($_POST['coi_frontend']) && $_POST['coi_frontend']==1 && !empty($plan) && count($plan)>1 && $plan[0]['is_single_coi']!='1' && $plan[0]['coi_type']==1){
            $data = $this->db->query("SELECT pd.nominee_first_name,
(select full_name from master_customer mc where mc.customer_id=pd.customer_id) as cust_details,
(select group_concat(address_line1,'-',pincode,'-',city,'-',state) from master_customer mc where mc.customer_id=pd.customer_id) as addr_details,
(select mobile_no from master_customer mc where mc.customer_id=pd.customer_id) as mobile_number,
(select unique_number from master_customer mc where mc.customer_id=pd.customer_id) as unique_number,
(select make from master_customer mc where mc.customer_id=pd.customer_id) as make,
(select gadeget_purchase_date from lead_details ld where ld.lead_id=pd.lead_id) as gadeget_purchase_date,
pd.nominee_last_name,mpst.policy_sub_type_name,mpl.plan_name, mc.creaditor_name,mp.policy_number, mp.policy_sub_type_id,api.certificate_number,api.start_date,api.end_date,api.created_date,api.TransactionID,mp.policy_id,mpl.is_single_coi,mpl.creditor_id,api.pr_api_id FROM lead_details AS ld, api_proposal_response AS api,master_policy AS mp,master_ceditors AS mc,master_plan AS mpl,master_policy_sub_type AS mpst,proposal_details AS pd WHERE ld.lead_id = api.lead_id AND mp.policy_id= api.master_policy_id AND mp.creditor_id=mc.creditor_id AND mp.plan_id = mpl.plan_id AND mpst.policy_sub_type_id = mp.policy_sub_type_id AND pd.plan_id = mp.plan_id AND pd.lead_id = ld.lead_id AND ld.lead_id = " . $_POST['lead_id'])->result_array();
            $premium_details=$this->db->query("select premium as premium ,(premium_with_tax) as premium_with_tax,sum_insured,master_policy_id from master_quotes where lead_id=" . $_POST['lead_id'])->result();

           if($premium_details->premium != null){

           }else{
               $premium_details=$this->db->query("select pp.premium ,premium_with_tax,cover as sum_insured,pp.policy_id as master_policy_id  from proposal_policy_member pp left join policy_member_plan_details pm on pp.policy_id=pm.policy_id WHERE pm.lead_id=" . $_POST['lead_id']." and pp.lead_id=". $_POST['lead_id'])->result();
           }


        }else{

            $data = $this->db->query("SELECT pd.nominee_first_name,
            (select full_name from master_customer mc where mc.customer_id=pd.customer_id) as cust_details,
            (select group_concat(address_line1,'-',pincode,'-',city,'-',state) from master_customer mc where mc.customer_id=pd.customer_id) as addr_details,
            (select mobile_no from master_customer mc where mc.customer_id=pd.customer_id) as mobile_number,
            (select unique_number from master_customer mc where mc.customer_id=pd.customer_id) as unique_number,
            (select make from master_customer mc where mc.customer_id=pd.customer_id) as make,
            (select gadeget_purchase_date from lead_details ld where ld.lead_id=pd.lead_id) as gadeget_purchase_date,
            pd.nominee_last_name,mpst.policy_sub_type_name,mpl.plan_name, mc.creaditor_name,mp.policy_number, mp.policy_sub_type_id,GROUP_CONCAT(DISTINCT(api.certificate_number)) certificate_number,api.start_date,api.end_date,api.created_date,api.TransactionID,mp.policy_id,mpl.is_single_coi,mpl.creditor_id,api.pr_api_id FROM lead_details AS ld, api_proposal_response AS api,master_policy AS mp,master_ceditors AS mc,master_plan AS mpl,master_policy_sub_type AS mpst,proposal_details AS pd WHERE ld.lead_id = api.lead_id AND mp.policy_id= api.master_policy_id AND mp.creditor_id=mc.creditor_id AND mp.plan_id = mpl.plan_id AND mpst.policy_sub_type_id = mp.policy_sub_type_id AND pd.plan_id = mp.plan_id AND pd.lead_id = ld.lead_id AND ld.lead_id = " . $_POST['lead_id'] . " GROUP BY pd.transaction_number")->row_array();

                    
                    
            
            $premium_details=$this->db->query("select sum(premium) as premium ,sum(premium_with_tax) as premium_with_tax,sum_insured,master_policy_id from master_quotes where lead_id=" . $_POST['lead_id'])->row();

           if($premium_details->premium != null){

           }else{
               $premium_details=$this->db->query("select sum(premium) as premium ,(total_premium) as premium_with_tax,cover as sum_insured,policy_id as master_policy_id from policy_member_plan_details where lead_id=" . $_POST['lead_id'])->row();
           }
        }

        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data,'premium_details'=>$premium_details];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result,JSON_UNESCAPED_SLASHES);

    }
    function getmarineData(){
        $lead_id=$_POST['lead_id'];
        $query=$this->db->query("select * from marine_customer_info where lead_id=".$lead_id);
        if($this->db->affected_rows() > 0){
            $data=$query->result_array();
            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data];
        }else{
            $result = ['status' => 500, 'Msg' => "No Records found"];
        } echo json_encode($result);
    }

    function getpolicypremiumflat($id, $suminsured, $whereArray = null, $min_premium = null, $max_premium = null, $partner_id = null)
    {
       // var_dump($partner_id);exit;
        $ptype = $this->db->get_where("master_policy", array("policy_id" => $id))->row()->premium_type;

        if ($ptype == 1) {
            $dt = "master_policy_id =$id AND sum_insured = $suminsured";
        } else {
            $dt = "master_policy_id =$id ";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);
      // print_r($rate[0]->premium_rate);exit;
//echo $this->db->last_query();exit;
        if ($partner_id) {

            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $id AND sum_insured = $suminsured");
           // echo $this->db->last_query();exit;
        }


        if (!empty($rate)) {
            if ($rate[0]->premium_rate == '') {
                $premium = $rate[0]->premium_with_tax;
            } else {
                $premium = $rate[0]->premium_rate;
            }
            //echo $premium;exit;
          //  echo $ptype;exit;
            $rr['group_code']=$rate[0]->group_code;

            $rr['member_code']=$rate[0]->member_code;

            $rr['amount']=0;
            if ($ptype == 1) {
//echo $premium;exit;
                $rr['amount'] = (int)$premium;
              //  print_r($rr);exit;
            } else {
                $rr['amount'] = ($suminsured / 1000) * $premium;
            }
           // print_r($r);

            if ($rate[0]->is_taxable) {
                $rr['tax'] = $rr['amount'] * $this->config->item('tax') / 100;
            }
            return $rr;
        } else {
            $r = 0;
            return $r;
        }
      //  print_r($r);exit;

    }

    function getpolicypremiumfamilyconstruct($policy, $sum_insured, $adult, $child, $partner_id = null, $whereArray = null, $min_premium = null, $max_premium = null)
    {

        //$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;

        $ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;
        if ($ptype == 1) {
            $dt = "master_policy_id = $policy AND sum_insured = $sum_insured AND adult_count = $adult AND child_count = $child";
        } else {
            $dt .= "master_policy_id = $policy AND sum_insured = $sum_insured";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);

        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured");

        }
        if (!empty($rate)) {
            $r['group_code']=$rate[0]->group_code;

            $r['member_code']=$rate[0]->member_code;

            if ($rate[0]->premium_rate == '') {
                $premium = $rate[0]->premium_with_tax;
            } else {
                $premium = $rate[0]->premium_rate;
            }
            if ($ptype == 1) {
                $r['amount'] = $premium;
            } else {
                $r['amount'] = ($sum_insured / 1000) * $premium;
            }
            if ($rate[0]->is_taxable) {
                $r['tax'] = $r['amount'] * $this->config->item('tax') / 100;
            }
        } else {
            $r = 0;
        }
        return $r;
    }

    function getpolicypremiumfamilyconstructage($proposal_details_id, $policy, $sum_insured, $adult, $child, $age, $whereArray = null, $min_premium = null, $max_premium = null, $partner_id = null)
    {

        //$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;

        //echo $policy.":::testing";
        $r = [];
        $ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;
//print_r($ptype);
        $dt = '';
        if ($ptype == 1) {
            $dt = "master_policy_id = $policy AND sum_insured = $sum_insured AND (adult_count = $adult AND child_count = $child)";
        } else {
            $dt .= "master_policy_id = $policy AND (adult_count = $adult AND child_count = $child) AND min_age <= $age AND max_age >= $age";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);
        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured");

        }

        //print_r($rate[0]->policy_premium_id);
        if ($rate) {

            $r['group_code']=$rate[0]->group_code;
            $r['member_code']=$rate[0]->member_code;

            if ($ptype == 1) {
                $pamount = $rate[0]->premium_rate;
                $r['amount'] = $pamount;

                if ($rate[0]->premium_with_tax) {
                    //$r['tax'] = $pamount * $rate[0]->premium_with_tax/100;
                    $r['tax'] = $rate[0]->premium_with_tax;
                } else {
                    //$r['tax'] =  ($pamount  * 0.18) + $pamount;
                    $r['tax'] = $pamount * 0.18;
                }
            } else {
                $pamount = ($sum_insured / 1000) * $rate[0]->premium_rate;

                if ($rate[0]->premium_with_tax) {
                    $r['tax'] = $rate[0]->premium_with_tax;
                } else {
                    $r['tax'] = $pamount * 0.18;
                }
            }
        }
        if (empty($r)) {
            $r = 0;
        }


        # old code
        /*
        if(!empty($rate)) {
            $trate = $this->apimodel->getdata1("proposal_policy_member","premium"," policy_id = $proposal_details_id AND lead_id = $lead_id ORDER BY premium DESC");
            if(!empty($trate) && $trate[0]->premium > $pamount){
                $r['amount'] = $trate[0]->premium;
                if($rate[0]->is_taxable){
                    $r['tax'] = $trate[0]->premium*$this->config->item('tax')/100;
                }
            }else{
                $r['amount'] = $pamount;
                if($rate[0]->is_taxable){
                    $r['tax'] = $pamount*$this->config->item('tax')/100;
                }
            }
        }else{
            $r = 0;
        }
        ***/
        #
        return $r;
    } // EO ()


    function getpolicypremiummemberage($policy, $sum_insured, $age, $whereArray = null, $min_premium = null, $max_premium = null, $partner_id = null)
    {
        //echo json_encode(2213123);exit;
        $ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;
        // echo json_encode($ptype);exit;
        $dt = '';
        if ($ptype == 1) {
            $dt = "master_policy_id = $policy AND sum_insured = $sum_insured AND min_age <= $age AND max_age >= $age";
        } else {
            $dt .= "master_policy_id = $policy AND min_age <= $age AND max_age >= $age";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);
        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured");

        }
        if ($rate) {
            $r['group_code']=$rate[0]->group_code;
            if ($ptype == 1) {

                $r['amount'] = $rate[0]->premium_rate;
            } else {

                $r['amount'] = ($sum_insured / 1000) * $rate[0]->premium_rate;
            }
            $r['group_code']=$rate[0]->group_code;
            $r['member_code']=$rate[0]->member_code;
            //$this->config->item('tax') = 18;
            if ($rate[0]->is_taxable) {
                $r['tax'] = $rate[0]->premium_with_tax;
                $r['amount'] = $rate[0]->premium_with_tax;
            }
        } else {
            $r = 0;
        }
        return $r;
    }

    function gettotalpremium($id, $sibasis, $lead_id, $policy_id)
    {

        $rates = $this->apimodel->getdata1("proposal_policy_member", "premium,tax", " proposal_policy_id = $id AND lead_id = $lead_id AND policy_id = $policy_id");
        $total['amount'] = 0;
        $total['tax'] = 0;
        if (!empty($rates)) {
            if ($sibasis == 1) {
                foreach ($rates as $rate) {
                    $total['amount'] = $total['amount'] + $rate->premium;
                    $total['tax'] = $total['tax'] + $rate->tax;
                }
            }
            if ($sibasis == 2) {
                foreach ($rates as $rate) {
                    $total['amount'] = $rate->premium;
                    $total['tax'] = $rate->tax;
                }
            }
            if ($sibasis == 3) {
                $count = count($rates);
                $value = 0;
                $tax = 0;
                foreach ($rates as $rate) {
                    if ($rate->premium > $value)
                        $value = $rate->premium;
                    $tax = $rate->tax;
                }
                $total['amount'] = $value;
                $total['tax'] = $tax;
            }
            if ($sibasis == 4) {
                foreach ($rates as $rate) {
                    $total['amount'] = $total['amount'] + $rate->premium;
                    $total['tax'] = $total['tax'] + $rate->tax;
                }
            }
        }
        return $total;
    }

    public function fetchPartnerDetails()
    {
     //   echo 1;die;
       // echo "2".$_POST["partner_id"];die;
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
   //     $data = $this->db->get_where("master_ceditors", ["creditor_id" => $_POST["partner_id"], "isactive" => 1])->row_array();



        $data=$this->db->query("select  creditor_id, creaditor_name, creditor_code, ceditor_email, 
creditor_mobile, creditor_phone, creditor_pancard, creditor_gstn, address, creditor_logo, isactive
        , createdon, initial_cd, cd_threshold, cd_utilised, cd_balance_remain,
         (select group_concat(primary_color,',',secondary_color,',', text_color,',', background_color,',', cta_color) from theme_configuaration tc where tc.creditor_id=mc.creditor_id) as theme_param
         from master_ceditors mc
        where isactive= 1 AND creditor_id=".$_POST["partner_id"])->row_array();

      //  echo $this->db->last_query();die;
       //  var_dump( $this->db->last_query());die;
        if ($data) {
          //  echo 1;die;
            $dataa = $this->db->get_where("master_single_journey", ["creditor_id" => $_POST["partner_id"], "is_active" => 1])->row_array();
            if (empty($dataa)) {
                $data = [];
            }
        }
        echo json_encode($data);
        exit;
    }

    public function get_suminsured_type_data()
    {
        $data = $this->db->get_where("master_suminsured_type", ["isactive" => 1])->result_array();
        echo json_encode($data);
        exit;
    }

    public function fetch_additional_plans()
    {
        $where = "pmpd.additional_plan_flag = 1 AND pmpd.customer_id = '" . $_POST['customer_id'] . "' AND pmpd.lead_id = '" . $_POST['lead_id'] . "'";
        $data = $this->db->select("mpst.policy_sub_type_name,mp.policy_id,mp.plan_id,mp.insurer_id,mp.policy_start_date,mp.policy_end_date,mpp.plan_name,mi.insurer_name, mpp.creditor_id, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo")
            ->from('policy_member_plan_details pmpd')
            ->join('master_policy mp', 'mp.policy_id = pmpd.policy_id')
            ->join('master_policy_sub_type mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id')
            ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
            ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
            ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
            ->where($where)
            ->get()->result_array();
        echo json_encode($data);
        exit;
    }
    public function getMember_insure_details_coi()
    {

        $lead_id = $this->input->post('lead_id');
        //$customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();
        if (!empty($check_data)) {
            //fetch selected plans
            $planDetails = $this->db->query("SELECT * FROM policy_member_plan_details where lead_id = '$lead_id'")->result_array();
            // var_dump($this->db->last_query());
//            exit;

            foreach ($planDetails as $val) {

                $members_data = $this->db->query("SELECT *
                    FROM proposal_policy_member_details AS pd
                    LEFT JOIN proposal_policy_member pm ON pd.member_id = pm.member_id
                    LEFT JOIN master_policy AS mp ON pm.policy_id = mp.policy_id 
                    LEFT JOIN master_policy_sub_type AS mpst ON mpst.policy_sub_type_id = mp.policy_sub_type_id
                    LEFT JOIN family_construct AS fc ON pd.relation_with_proposal = fc.id
                    WHERE pm.policy_id = '" . $val['policy_id'] . "' AND pm.lead_id = '$lead_id'")->result_array();


                foreach ($members_data as $key => $val_p) {
                    $arr[$val_p['code']]['policy_sub_type_name'] = $val_p['policy_sub_type_name'];
                    $arr[$val_p['code']]['member'][$key]['member_type'] = $val_p['member_type'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_first_name'] = $val_p['policy_member_first_name'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_last_name'] = $val_p['policy_member_last_name'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_dob'] = $val_p['policy_member_dob'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_gender'] = $val_p['policy_member_gender'];
                    $arr[$val_p['code']]['member'][$key]['cover'] = $val['cover'];
                    $arr[$val_p['code']]['member'][$key]['premium'] = $val['premium'];
                    //array_push($data,$arr);
                }
            }


            echo json_encode($arr);
            exit;

        }
    }
    public function getMember_insure_details()
    {
        $lead_id = $this->input->post('lead_id');
        //$customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();
   // var_dump($check_data);exit;
        if (!empty($check_data)) {
            //fetch selected plans
            $planDetails = $this->db->query("SELECT * FROM policy_member_plan_details where lead_id = '$lead_id'")->result_array();
           /* var_dump($planDetails);
            exit;*/

            foreach ($planDetails as $val) {

                $members_data = $this->db->query("SELECT *
                    FROM proposal_policy_member_details AS pd
                    LEFT JOIN proposal_policy_member pm ON pd.member_id = pm.member_id
                    LEFT JOIN master_policy AS mp ON pm.policy_id = mp.policy_id 
                    LEFT JOIN master_policy_sub_type AS mpst ON mpst.policy_sub_type_id = mp.policy_sub_type_id
                    LEFT JOIN family_construct AS fc ON pd.relation_with_proposal = fc.id
                    WHERE pm.policy_id = '" . $val['policy_id'] . "' AND pm.lead_id = '$lead_id'")->result_array();
                //echo $this->db->last_query();exit;
              //  var_dump($members_data);exit;
                foreach ($members_data as $key => $val_p) {

                    $arr[$val_p['code']][$key]['member_type'] = $val_p['member_type'];
                    $arr[$val_p['code']][$key]['policy_member_salutation'] = $val_p['policy_member_salutation'];
                    $arr[$val_p['code']][$key]['policy_member_first_name'] = $val_p['policy_member_first_name'];
                    $arr[$val_p['code']][$key]['policy_member_last_name'] = $val_p['policy_member_last_name'];
                    $arr[$val_p['code']][$key]['policy_member_dob'] = $val_p['policy_member_dob'];
                    $arr[$val_p['code']][$key]['policy_member_gender'] = $val_p['policy_member_gender'];
                    $arr[$val_p['code']][$key]['policy_member_marital_status'] = $val_p['policy_member_marital_status'];
                    $arr[$val_p['code']][$key]['cover'] = $val['cover'];
                    $arr[$val_p['code']][$key]['premium'] = $val['premium'];
                    //array_push($data,$arr);
                }
            }
//var_dump($arr);exit;

            echo json_encode($arr);
            exit;

        }
    }

    public function checkPaymentStatus(){
        $paymentData = $this->db->query("select * from proposal_payment_details where lead_id = '".$_POST['lead_id']."'")->row_array();
        echo json_encode($paymentData);
            exit;
    }

    public function sendNotification(){
        $lead_id = encrypt_decrypt_password($_POST['lead_id'],'D');
        $sqlStr = "SELECT l.lead_id,c.full_name,p.plan_id,p.plan_name,l.creditor_id,l.email_id,l.mobile_no
        FROM lead_details l left join master_customer c on c.lead_id=l.lead_id left join master_plan p on p.plan_id=l.plan_id
        WHERE  l.lead_id =".$lead_id;
        $query=$this->db->query($sqlStr)->row_array();

        $t_query = "SELECT ct.dropout_event,ct.subject,ct.type,ct.content FROM master_communication_templates ct left join master_communication_events ce on ce.id=ct.dropout_event WHERE ce.name ='".$_POST['event']."' and ct.isactive=1 and ct.creditor_id=".$query['creditor_id'];
        $templates=$this->db->query($t_query)->result_array();
        
        if(!empty($templates)){
            $creditor_data = $this->db->query("select insurer_name from master_insurer mi left join master_policy mp on mp.insurer_id=mi.insurer_id where mp.plan_id = '".$query['plan_id']."'")->row_array();
            $paymentData = $this->db->query("select * from proposal_payment_details where lead_id = '".$lead_id."'")->row_array();
            $coiData = $this->db->query("select * from api_proposal_response where lead_id = '".$lead_id."'")->row_array();
            $cover_data = $this->db->query("select cover from quote_member_plan_details where lead_id = '".$lead_id."'")->row_array();
            if(!empty($query)){
                $amount = '';
                $certificate_number = '';
                $start_date = '';
                $end_date = '';
                $cover = '';
                $name = $query['full_name'];
                $names = explode(' ',$name);
                $first_name = $names[0];
                if(!empty($paymentData)){
                    $amount = $paymentData['premium'];
                }if(!empty($cover_data)){
                    $cover = $cover_data['cover'];
                }
                if(!empty($coiData)){
                    $certificate_number = $coiData['certificate_number'];
                    if(!empty($coiData['start_date'])){
                        $start_date = date('d-m-Y',strtotime($coiData['start_date']));
                    }
                    if(!empty($coiData['end_date'])){
                        $end_date = date('d-m-Y',strtotime($coiData['end_date']));
                    }
                }
                foreach ($templates as $template) {

                    $subject =  str_replace(array('{{name}}', '{{first_name}}','{{amount}}','{{coi_number}}','{{end_date}}','{{start_date}}','{{sum_insured}}','{{insurance_company_name}}'), array($name, $first_name, $amount, $certificate_number, $end_date, $start_date, $cover,$creditor_data['insurer_name']), $template['subject']);
                    $content =  str_replace(array('{{name}}', '{{first_name}}','{{amount}}','{{coi_number}}','{{end_date}}','{{start_date}}','{{sum_insured}}','{{insurance_company_name}}'), array($name, $first_name, $amount, $certificate_number, $end_date, $start_date, $cover,$creditor_data['insurer_name']), $template['content']);
                    
                    $log_data['lead_id']=$lead_id;
                    $log_data['subject']=$subject;
                    $log_data['content']=$content;
                    $log_data['request_body']=$content;
                    $log_data['event']=$template['dropout_event'];
                    
                    if($template['type']=='email'){
                        $log_data['to']=$query['email_id'];
                        //$log_data['response_body']= sendMail($query['email_id'],$subject,$content);
                        $log_data['response_body']= sendMail($query['email_id'],$subject,$content);
                        //$log_data['response_body']= sendSms('8003392772',$content);
                        
                        createCommunicationLog($log_data);
                    }
                    if($template['type']=='sms'){
                        $log_data['to']=$query['mobile_no'];
                        $log_data['response_body']= sendSms($query['mobile_no'],$content);
                        createCommunicationLog($log_data);
                    }

                    
                }
                   
                
            }

        }
        
    }



}