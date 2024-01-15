<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//require($_SERVER['DOCUMENT_ROOT'] . '/../vendor/razorpay/razorpay/Razorpay.php');

require(APPPATH.'libraries/razorpay-php/Razorpay.php');

//echo 123;die;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
require_once PATH_VENDOR.'vendor/autoload.php';


use Dompdf\Dompdf;
class Quotes extends CI_Controller
{
    public $algoMethod;
    public $hashMethod;
    public $hash_key;
    public $encrypt_key;

    public $keyId;
    public $keySecret;
    public $displayCurrency;



    function __construct()
    {
       // echo APPPATH;exit;
        parent::__construct();

        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
        $this->keyId = 'rzp_test_eOoJFKhOil7jcO';
        $this->keySecret = 'DWnJrWPIG9HPMjy10n53zWx8';
        $this->displayCurrency = 'INR';
        if(!empty($_GET['lead_id'])){
            $leadId= base64_decode($_GET['lead_id']);
            if(!is_numeric($leadId)){
                setSessionByLeadId($_GET['lead_id']);
            }
            
        }

       // ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    }
    public function generate_quote_abc()
    {
        //print_R($this->session->userdata);die;
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['si_type_id'] = $this->input->post('si_type_id');
        $req_data['plan_name'] = $this->input->post('plan_name');
        $req_data['premium'] = $this->input->post('premium');
        $req_data['cover'] = $this->input->post('cover');

        if(isset($_POST['plan_id'])){
            $this->session->set_userdata('plan_id', $_POST['plan_id']);
        }
        if(isset($_POST['cover'])){
            $this->session->set_userdata('cover', $_POST['cover']);
        }
        if(isset($_POST['policy_id'])){
            $this->session->set_userdata('policy_id', $_POST['policy_id']);
        }
        if(isset($_POST['si_type_id'])){
            $this->session->set_userdata('si_type_id', $_POST['si_type_id']);
        }
        if(isset($_POST['plan_name'])){
            $this->session->set_userdata('plan_name', $_POST['plan_name']);
        }
        if(isset($_POST['premium'])){
            $this->session->set_userdata('premium', $_POST['premium']);
        }
        $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');


        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['si_type_id'] = $this->session->userdata('si_type_id');
        $req_data['plan_name'] = $this->session->userdata('plan_name');
        $req_data['premium'] = $this->session->userdata('premium');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        // echo SERVICE_URL . '/customer_api/getCustomerDetails';exit;
        curlFunction(SERVICE_URL . '/customer_api/Create_quote_self_from_quote', $req_data);
        $policy_id = !empty(encrypt_decrypt_password($req_data['policy_id'], 'D'))?encrypt_decrypt_password($req_data['policy_id'], 'D'):$req_data['policy_id'];
        $creditor=$this->db->query("select creditor_id,max_insured_count from master_policy where policy_id in(".$policy_id.")")->row();
        $creditor_id = $creditor->creditor_id;
        $qry = "SELECT * FROM features_config where creditor_id = '" . $creditor_id . "' AND plan_id = '" . $req_data['plan_id'] . "' AND isactive = 1 ";
        $data['features'] = $this->db->query($qry)->result_array();
$data['post_data'] = $req_data;
//print_r($data['post_data']);die;
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/generateQuoteabc', $req_data);

       // print_r($checkDetails);exit;

        $quote_data = $this->db->query("select * from quote_member_plan_details where  lead_id=".encrypt_decrypt_password($req_data['lead_id'],'D'))->row_array();
        curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$req_data['lead_id'],'dropout_page'=>'5']);
   
        $data['quote_info'] = json_decode($checkDetails,true);
        $data['quote_info']['cover'] = $quote_data['cover'];
        $data['max_insured_count'] = $creditor->max_insured_count;
        $data['tc_text'] = $this->termsAndConditionText();
       // print_r($data['quote_info']);die;
        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/generate_quotes_view_abc',$data);
        $this->load->view('template/customer_portal_footer.php');
    }
    public function Create_quote_self()
    {

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');

        $req_data['sel_con'] = $this->input->post('self_con');
        $req_data['quote_info'] = $this->input->post('quote_info');
        $req_data['cover'] = $this->input->post('cover');
        $req_data['total_premium'] = $this->input->post('total_premium');
        $req_data['plan_id'] = $this->input->post('plan_id');
//print_r($this->input->post('policy_details'));die;
        $this->session->set_userdata('policy_details_session', $this->input->post('policy_details'));
        $this->db->query("update quote_member_plan_details set policy_details='".json_encode($this->input->post('policy_details'))."' where lead_id=".encrypt_decrypt_password($req_data['lead_id'],'D'));
     // $d=  $this->session->userdata('policy_details_session');
    //  print_r($d);die;
       // var_dump($req_data['quote_info']);exit;
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/Create_quote_self', $req_data);
       //  print_r($checkDetails);die;
        $data['status'] = 'success';
        $data['data'] = $checkDetails;

        echo json_encode($data);
    }
    function getPremiumNew(){
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $age = $this->input->post('age');
        $policy_data_session = $this->session->userdata('policy_details_session');
        foreach ( $policy_data_session as $res){

            $res['max_age']=$age;
            $req_data['policy_data'][]=$res;
          //  print_r($res['max_age']);
        }
//print_r($req_data['policy_data']); die;
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getMemebrDetails_single', $req_data);

        $data['status'] = 'success';
        $data['data'] = $checkDetails;

        echo json_encode($data);
    }

    public function Update_deductable(){
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');

        $deductable = $this->input->post('deductable');
        if($deductable>0){
            $this->db->query("update member_ages set deductable='".$deductable."'  where lead_id=".encrypt_decrypt_password($req_data['lead_id'],'D'));
        }


    }
    public function create_member_single_link()
    {

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');

        $req_data['policy_data'] = $this->input->post('policy_details');


        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getMemebrDetails_single', $req_data);

        $plan_data = $this->db->query("select * from quote_member_plan_details where  lead_id=".encrypt_decrypt_password($req_data['lead_id'],'D'))->row_array();
        if(!empty($plan_data)){
            $chk = json_decode($checkDetails,true);
            foreach ($chk['policy_det'] as $value) {
               $sum_Insured = $value['sum_insured'];
            }
            $this->db->query("update quote_member_plan_details set premium='".$chk['total_premium']."' , cover='".$sum_Insured."' , policy_details='".json_encode($this->input->post('policy_details'))."' where lead_id=".encrypt_decrypt_password($req_data['lead_id'],'D'));
        }
       // var_dump($checkDetails);die;
        $data['status'] = 'success';
        $data['data'] = $checkDetails;

        echo json_encode($data);
    }
    public function axis_pincode_get_state_city(){
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');

        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }

        if($this->input->is_ajax_request()){

            $req_data['pincode'] = $this->input->post('pincode');



            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/axis_pincode_get_state_city', $req_data);
            $pincodeDetails = json_decode($checkDetails, TRUE);
            //print_r($pincodeDetails);exit;
            $data = array("city"=> $pincodeDetails['pincode_data']['CITY'],
                           "state"=> $pincodeDetails['pincode_data']['STATE']);
            echo json_encode($data);exit;
        }

    }
    public function get_member_insure()
    {
        if ($this->input->is_ajax_request()) {
            $lead_id = $this->session->userdata('lead_id');
            $partner_id = $this->session->userdata('partner_id_session');
            $customer_id = $this->session->userdata('customer_id');
            $product_id = $this->session->userdata('product_id_session');
            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getMember_insure', [

                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'partner_id' =>$partner_id,
                'product_id' =>$product_id

            ]);

            echo $checkDetails;exit;
        }
    }
    public function Submitinsure_data()
    {

        if($this->input->is_ajax_request()){
            //$lead_id = $this->session->userdata('lead_id');
            //$customer_id = $this->session->userdata('customer_id');
            $req_data['lead_id'] = $this->session->userdata('lead_id');
            $req_data['customer_id'] = $this->session->userdata('customer_id');
            $policy_id = $this->session->userdata('policy_id');
            $policy_id = encrypt_decrypt_password($policy_id, 'D');
            $req_data['policy_id'] = $policy_id;
            $req_data['display'] = $this->input->post('display');
            $req_data['partner_id'] = $this->session->userdata('partner_id_session');
            $req_data['si_type_id'] = $this->session->userdata('si_type_id');




                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/Createinsure_data', $req_data);


                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$req_data['lead_id'],'dropout_page'=>'8','proposal_step_completed'=>2]);

                print_r($checkDetails);die;

        }
    }

    public function Submitmember_data()
    {

        if($this->input->is_ajax_request()){
            //$lead_id = $this->session->userdata('lead_id');
            //$customer_id = $this->session->userdata('customer_id');
            $req_data['lead_id'] = $this->session->userdata('lead_id');
            $req_data['customer_id'] = $this->session->userdata('customer_id');
            $policy_id = $this->session->userdata('policy_id');
            $policy_id = encrypt_decrypt_password($policy_id, 'D');
            $req_data['policy_id'] = $policy_id;
            $req_data['rel_name'] = $this->input->post('rel_name');
            $req_data['partner_id'] = $this->session->userdata('partner_id_session');
            $req_data['si_type_id'] = $this->session->userdata('si_type_id');




            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/Creatememberinsure_data', $req_data);


            curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$req_data['lead_id'],'dropout_page'=>'8','proposal_step_completed'=>2]);

            echo 1;

        }
    }
    public function get_member_insure_data()
    {
        if($this->input->is_ajax_request()){
            //$lead_id = $this->session->userdata('lead_id');
            //$customer_id = $this->session->userdata('customer_id');
            $req_data['lead_id'] = $this->session->userdata('lead_id');
            $req_data['customer_id'] = $this->session->userdata('customer_id');
            $req_data['display'] = $this->input->post('display');





                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/get_member_insure_data', $req_data);
                print_r($checkDetails);
                die;

        }
    }
    public function update_proposer_details(){
        $req_data = [];

        $lead_id = $this->session->userdata('lead_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = $this->session->userdata('customer_id');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $req_data['lead_id'] = $lead_id;
        $req_data['customer_id'] = $customer_id;
        $policy_id = $this->session->userdata('policy_id');
        $policy_id = encrypt_decrypt_password($policy_id, 'D');
        $req_data['policy_id'] = $policy_id;


        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }

        if($this->input->is_ajax_request()){

            $req_data['fname'] = $this->input->post('fname');
            $req_data['lname'] = $this->input->post('lname');
            $req_data['salutation'] = $this->input->post('salutation');
            $req_data['gender'] = $this->input->post('gender');
            $req_data['proposer_dob'] = $this->input->post('proposer_dob');
            $req_data['email'] = $this->input->post('email');
            $req_data['mobile_no'] = $this->input->post('mobile_no');
            $req_data['proposer_address'] = $this->input->post('proposer_address');
            $req_data['proposer_address2'] = $this->input->post('proposer_address2');
            $req_data['proposer_pincode'] = $this->input->post('proposer_pincode');
            $req_data['proposer_city'] = $this->input->post('proposer_city');
            $req_data['proposer_state'] = $this->input->post('proposer_state');
            $req_data['proposer_pan'] = $this->input->post('proposer_pan');
            $req_data['status'] = $this->input->post('status');
            $req_data['gstin'] = $this->input->post('gstin');
            $req_data['is_proposer_insured'] = ($this->input->post('is_proposer_insured') == 'Yes') ? 1 : 0;
            $req_data['partner_id'] = $this->session->userdata('partner_id_session');

            if($_POST){

                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/updateCustomerDetails', $req_data);


                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$this->session->userdata('lead_id'),'dropout_page'=>'7', 'proposal_step_completed'=>1]);

                echo $checkDetails;exit;
            }
        }
    }
//sonal
public function create_proposal()
        {
            $req_data = [];
            if($this->input->is_ajax_request()){

                $req_data['policy_id'] = $this->input->post('policy_id');
                $req_data['premium'] = $this->input->post('premium');
                $req_data['lead_id'] = $this->session->userdata('lead_id');
                $req_data['customer_id'] = $this->session->userdata('customer_id');
                $req_data['trace_id'] = $this->session->userdata('trace_id');

                $req_data['plan_id'] = $this->session->userdata('plan_id');
                $req_data['cover'] = $this->session->userdata('cover');
                $req_data['premium'] = $this->session->userdata('premium');
                $req_data['partner_id'] = $this->session->userdata('partner_id_session');

                $proposal_data = curlFunction(SERVICE_URL . '/customer_api/create_proposal', $req_data);

                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$this->session->userdata('lead_id'),'dropout_page'=>'9','proposal_step_completed'=>3]);
                //print_r($proposal_data);exit;
                echo ($proposal_data);

            }

        }
    //ankita
    public function generate_proposal(){

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $policy_data = $this->session->userdata('policy_details_session');
       // print_r($policy_data);die;
        if(isset($_POST['plan_id'])){
            $this->session->set_userdata('plan_id', $_POST['plan_id']);
        }
        if(isset($_POST['cover'])){
            $this->session->set_userdata('cover', $_POST['cover']);
        }
        if(isset($_POST['policy_id'])){
            $this->session->set_userdata('policy_id', $_POST['policy_id']);
        }
        if(isset($_POST['premium'])){
            $this->session->set_userdata('premium', $_POST['premium']);
        }
        if(isset($_POST['si_type_id'])){
            $this->session->set_userdata('si_type_id', $_POST['si_type_id']);
        }
        if(isset($_POST['creditor_logo'])){
            $this->session->set_userdata('creditor_logo',$_POST['creditor_logo']);
        }


        $req_data['si_type_id'] = $this->session->userdata('si_type_id');
        $req_data['creditor_logo'] = $this->session->userdata('creditor_logo');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['premium'] = $this->session->userdata('premium');
        $req_data['plan_name'] = $this->session->userdata('plan_name');

        $plan_data = $this->db->query("select pan_mandatory,nominee_mandatory,gender,payment_first,payment_page from master_plan where  plan_id=".$req_data['plan_id'])->row_array();


        $get_summary_details = curlFunction(SERVICE_URL . '/customer_api/get_summary_details', $req_data);
       // print_r($get_summary_details); exit;
        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
       //print_r($get_customer_details);
        // echo SERVICE_URL . '/customer_api/getCustomerDetails';exit;
        $nominee_relation = curlFunction(SERVICE_URL . '/customer_api/getNomineeRelation', $req_data);
        $nominee_relation = json_decode($nominee_relation, TRUE);
        $lead_id = encrypt_decrypt_password( $this->session->userdata('lead_id'),'D');

        $deductable = $this->db->query("select deductable from member_ages where lead_id = '".$lead_id."'")->row_array();




        $data['customer_details'] = json_decode($get_customer_details, TRUE);
        $data['get_summary_details'] = json_decode($get_summary_details, TRUE);
        $data['post_data'] = $req_data;
        $data['nominee_relations'] = $nominee_relation['data'];
        $data['plan_data'] = $plan_data;
        $data['deductable'] = $deductable;
        $data['lead_details']  = $this->db->query("select is_mailer_api from lead_details where lead_id = '$lead_id'")->row_array();    

        //var_dump($nominee_relation['data']);exit;
        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }
        curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$req_data['lead_id'],'dropout_page'=>'6']);
       // var_dump($data);exit;
        $data['tc_text'] = $this->termsAndConditionText();

        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/proposal_form',$data);
        $this->load->view('template/customer_portal_footer.php');
    }


    public function index()
    {
       // echo 123;die;
      //  ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$req_data['lead_id'],'dropout_page'=>'4']);
//        echo $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');die;
        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
           // redirect('/Customerportal/');
        }
        if($this->session->userdata('partner_id_session')){
            $req_data['partner_id'] = $this->session->userdata('partner_id_session');
        }
        if($this->session->userdata('product_id_session')){
            $req_data['product_id'] = $this->session->userdata('product_id_session');
        }
       //print_r($req_data);exit;

        if($this->input->is_ajax_request()){

            $req_data['si_type'] = $this->input->post('si_type');
            $req_data['insurer_id'] = $this->input->post('insurer_id');
            $req_data['premium'] = $this->input->post('premium');
            $req_data['cover'] = $this->input->post('cover');
            $req_data['duration'] = $this->input->post('duration');
            $req_data['members'] = $this->input->post('members');
            $req_data['ajaxReq'] = $this->input->is_ajax_request();
          //  $insurer_id = encrypt_decrypt_password($req_data['insurer_id'], 'D');
            if($req_data['members']){

                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createMembers', $req_data);
                  //print_r($checkDetails);exit;
            }
        }
        // echo 222;exit;
//        print_r($req_data);exit;
        $response = curlFunction(SERVICE_URL . '/customer_api/getQuotePageData', $req_data);

     //  echo "<pre>";print_r($response);exit;

       // $city_name['city_name'] = $this->input->post('city_name');
        //$getQuotePageData = curlFunction(SERVICE_URL . '/customer_api/getQuotePageData', $city_name);

        $getQuotePageData = json_decode($response, TRUE);
       // var_dump($getQuotePageData['policy_data']);exit;
//        echo "p";
//         echo "<pre>";print_r($getQuotePageData);exit;
        if($getQuotePageData['status'] != '200'){
            echo $getQuotePageData['msg'];
            exit;
        }
           // print_r($getQuotePageData);exit;
        $group_by_policies = array();
        $policy_ids = array();
        $sum_insured_arr = array();
        $premium_arr = array();
        $counter = 0;
    //    echo '<pre>';
        //print_r($getQuotePageData['policy_data'][4]);die;
        $statusPolicy=false;
        foreach($getQuotePageData['policy_data'] as $key=>$item)
        {
            $i = 0;



//              var_dump($item);
          /*  if($item['si_premium_basis_id'] == 5){
//var_dump($item['suminsured'] );
                foreach($item['suminsured'] as $key=>$val) {

                        $statusPolicy=true;
                        // $item['premium_rate'] = $item['rate'][0];
                        if (!in_array($item['policy_id'], $policy_ids)) {

                            array_push($policy_ids, $item['policy_id']);
                            $group_by_policies[$counter] = $item;


                            $sum_insured_arr[$item['policy_id']] = array();
                            array_push($sum_insured_arr[$item['policy_id']], $item['suminsured']);
//echo $item['policy_id'];
                            //   $premium_arr[$item['policy_id']] = array();
                        }

                }


                // array_push($premium_arr[$item['policy_id']], $item['premium_rate']);
                $counter++;
            }else{*/

                if(($item['suminsured'] != '0' && $item['suminsured'] != '')  )
                {

                    foreach($item['suminsured'] as $key=>$val) {

                        if ($val['rate'] != '0' && $val['rate'] != '') {
                            $statusPolicy=true;
                            // $item['premium_rate'] = $item['rate'][0];
                            if (!in_array($item['policy_id'], $policy_ids)) {
                                array_push($policy_ids, $item['policy_id']);
                                $group_by_policies[$counter] = $item;


                                $sum_insured_arr[$item['policy_id']] = array();
                                array_push($sum_insured_arr[$item['policy_id']], $item['suminsured']);

                                //   $premium_arr[$item['policy_id']] = array();
                            }

                        }

                    }

                    // array_push($premium_arr[$item['policy_id']], $item['premium_rate']);
                    $counter++;

                }
//            }


        }
//var_dump($sum_insured_arr);die;
    //   exit;
       // var_dump($getQuotePageData);
        //var_dump($premium_arr[$item['policy_id']]);
        //exit;
        if(!$statusPolicy){
            echo 'No Policies were found!';
            exit;
        }

        // ksort($group_by_policies, SORT_NUMERIC);
       // print_r($group_by_policies);die;
       // $get_family_construct_plan_wise = curlFunction(SERVICE_URL . '/customer_api/get_family_construct_plan_wise', $group_by_policies);

        $get_family_construct_data = curlFunction(SERVICE_URL . '/customer_api/get_family_construct_data', $req_data);

        $get_family_construct_data = json_decode($get_family_construct_data, TRUE);

        foreach($get_family_construct_data as $single_data){
            if(strtolower($single_data['member_type']) == "son"){
                $son_id = $single_data['id'];
            }

            if(strtolower($single_data['member_type']) == "daughter"){
                $daughter_id = $single_data['id'];
            }
        }
//var_dump($premium_arr);
//        exit;
        $data['son_id'] = $son_id;
        $data['daughter_id'] = $daughter_id;
//var_dump($group_by_policies);
//exit;
       // var_dump($getQuotePageData);exit;
        $data['family_construct_arr'] = $get_family_construct_data;
        $data['getQuotePageData'] = $getQuotePageData;
        $data['group_by_policies'] = $group_by_policies;
        $data['sum_insured_arr'] = $sum_insured_arr;
        $data['premium_arr'] = $premium_arr;
        $data['premium'] = $this->input->post('premium');
        $data['si_type_id'] = $getQuotePageData['member_ages'][0]['si_type_id'];
        $data['suminsured_type_arr'] = json_decode(curlFunction(SERVICE_URL . '/customer_api/get_suminsured_type_data', $req_data),TRUE);


        // foreach($group_by_policies as $single_policy){
        //     echo $single_policy['plan_name'];
        // }
        // exit;
     // echo '<pre>';
       //  print_r($sum_insured_arr);exit;

        // $get_policy_type = curlFunction(SERVICE_URL . '/customer_api/get_policy_type');
     //   print_r($data);
       // exit;
        if($this->input->is_ajax_request()){
//            echo 1;
//            exit;
            $members_string = "";
            foreach ($getQuotePageData['member_ages'] as $single_member) {
                $members_string .= $single_member['member_type'] . ',';
            }

            $members_string = rtrim($members_string, ",");
            $data['members_string'] = $members_string;
            $this->load->view('quotes/policy_cards', $data);

        }else{
            //if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'grid'){
            if($this->session->userdata('partner_id_session')){

                $this->load->view('template/customer_portal_header.php');
                $this->load->view('quotes/grid_view', $data);
                $this->load->view('template/customer_portal_footer.php');
            }else{
                $this->load->view('template/customer_portal_header.php');
                $this->load->view('quotes/index', $data);
                $this->load->view('template/customer_portal_footer.php');
            }

        }

    }
public function get_all_data_card()
{
    $req_data = [];
    $req_data['lead_id'] = $this->session->userdata('lead_id');
    $req_data['customer_id'] = $this->session->userdata('customer_id');
    $req_data['trace_id'] = $this->session->userdata('trace_id');

    if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

    }else{
        redirect('/Customerportal/');
    }

    if($this->input->is_ajax_request()){
            $req_data['plan_id'] = $this->input->post('plan_id');
            $req_data['policy_id'] = $this->input->post('policy_id');
            $req_data['cover'] = $this->input->post('cover');





    }

    //echo 222;exit;
    $response = curlFunction(SERVICE_URL . '/customer_api/getAll_data_card', $req_data);
//print_r($response);die;
    $data['status'] = 'success';
    $data['data'] = $response;

    echo json_encode($data);
}
public function get_all_premium()
{
    $req_data = [];
    $req_data['lead_id'] = $this->session->userdata('lead_id');
    $req_data['customer_id'] = $this->session->userdata('customer_id');
    $req_data['trace_id'] = $this->session->userdata('trace_id');

    if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

    }else{
        redirect('/Customerportal/');
    }

    if($this->input->is_ajax_request()){


            $req_data['policy_id'] = $this->input->post('policy_id');
            $req_data['plan_id'] = $this->input->post('plan_id');
            $req_data['cover'] = $this->input->post('cover');





    }
    // echo 222;exit;
    $response = curlFunction(SERVICE_URL . '/customer_api/getAll_data_premium', $req_data);
 //print_r($response);die;
    $data['status'] = 'success';
    $data['data'] = $response;

    echo json_encode($data);
}
public function create_policy_member_plan()
{

    $req_data = [];
    $req_data['lead_id'] = $this->session->userdata('lead_id');
    $req_data['customer_id'] = $this->session->userdata('customer_id');
    $req_data['trace_id'] = $this->session->userdata('trace_id');
    $is_normal_customer = $this->session->userdata('is_normal_customer');
if(isset($_POST['plan_name'])){
            $this->session->set_userdata('plan_name', $_POST['plan_name']);
        }
    if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

    }else{
        redirect('/Customerportal/');
    }
    //$req_data['policy_details'] = $this->input->post('policy_details');
 $req_data['policy_id'] = $this->input->post('policy_id');
  $req_data['plan_id'] = $this->input->post('plan_id');
   $req_data['cover'] = $this->input->post('cover');
    $req_data['premium'] = $this->input->post('premium');
     $req_data['tenure'] = $this->input->post('tenure');
      $req_data['single'] = $this->input->post('single');
      $req_data['total_premium'] = $this->input->post('total_premium');
//      var_dump($req_d ata);
//      exit;
    // echo 222;exit;
    if($is_normal_customer){
        $req_data['policy_details'] = $this->input->post('policy_details');
    }
    $response = curlFunction(SERVICE_URL . '/customer_api/createPolicy_member_plan', $req_data);
//var_dump($response);
//exit;
    $data['status'] = 'success';
    $data['data'] = $response;

    echo json_encode($data);
}
public function get_family_data_exist()
{
    $req_data = [];
    $req_data['lead_id'] = $this->session->userdata('lead_id');
    $req_data['customer_id'] = $this->session->userdata('customer_id');
    $req_data['trace_id'] = $this->session->userdata('trace_id');

    if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

    }else{
        redirect('/Customerportal/');
    }

    if($this->input->is_ajax_request()){
        if($this->input->post('nominee_rel')){
            $req_data['nominee_rel'] = $this->input->post('nominee_rel');
        }else{
            $req_data['nominee_rel'] = '';
        }




    }
    // echo 222;exit;
    $response = curlFunction(SERVICE_URL . '/customer_api/getfamily_data_exist', $req_data);

    $data['status'] = 'success';
    $data['data'] = $response;

    echo json_encode($data);
}
    public function quotecompare()
    {
        // $link = $_SERVER['PHP_SELF'];
        // $link_array = explode('/',$link);
        // $page = end($link_array);

        // // echo urldecode($page);
        // $_GET = urldecode($page);
        // print_r($_GET);
        $data['lead_id'] = $this->session->userdata('lead_id');
        $data['customer_id'] = $this->session->userdata('customer_id');
        $data['trace_id'] = $this->session->userdata('trace_id');
       // $plans = $_POST['plan_id_compare'];
        //$plans = explode(',', $plans);
        $covers =json_decode($_POST['cover_compare']) ;
        //$covers = explode(',', $covers);
       // $data['plans'] = $plans;
        $data['covers'] = $covers;
       //print_r($covers);die;
        $get_data = curlFunction(SERVICE_URL . '/customer_api/getQuoteCompareData', $data);
        //echo '<pre>';
        //print_r($get_data);exit;
        $get_data1= json_decode($get_data, TRUE);

        $data['compare_data'] = $get_data1;

      // print_r($get_data);die;

        // foreach($get_data as $key=>$single_plan){
        //     // echo '<pre>';
        //     // echo print_r($get_data[$key]);
        //     echo $single_plan['plan_name'];
        // }
        // exit;


        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/quotecompare', $data);
        $this->load->view('template/customer_portal_footer.php');
    }
    public function  rehit_fail_real_pg_check()
    {
        $query = $this->db->query("select pp.*,ppd.*,mc.creditor_id from proposal_payment_details  as ppd join proposal_policy as pp join master_ceditors as mc where ppd.lead_id = pp.lead_id and ppd.creditor_id = mc.creditor_id  and ppd.status = 'Pending' AND pp.cron_count < 2 AND  mc.api_lable_id = 'traktion_supertoup' AND date(pp.created_at) = date(now())")->result_array();


        if(!empty($query)){
            foreach($query as $value){

                $creditor_id = $value['creditor_id'];
                $plan_det = $this->db->query("select * from master_plan where creditor_id = '$creditor_id'")->row_array();
                $lead_id = $value['lead_id'];
                $policy_id = $value['master_policy_id'];
                $this->db->query("UPDATE proposal_policy SET cron_count = cron_count + 1 WHERE lead_id = '$lead_id' and master_policy_id = '$policy_id'");
                $req_details['lead_id'] = $lead_id;
                $req_details['plan_id'] = $plan_det['plan_id'];
                $response = curlFunction(SERVICE_URL . '/customer_api/generate_policy_ic_api_call', $req_details);
            }
        }
    }
    public function  rehit_fail_policy_issuance()
    {
        $query = $this->db->query("select pp.*,mc.creditor_id from proposal_policy as pp join proposal_payment_details as ppd join master_ceditors as mc where pp.lead_id = ppd.lead_id and ppd.creditor_id = mc.creditor_id  and pp.status = 'Payment-Done' AND pp.cron_count < 2 AND  mc.api_lable_id = 'traktion_supertoup' AND date(pp.created_at) = date(now())")->result_array();
        if(!empty($query)){
            foreach($query as $value){
                $creditor_id = $value['creditor_id'];
                $plan_det = $this->db->query("select * from master_plan where creditor_id = '$creditor_id'")->row_array();
                $lead_id = $value['lead_id'];
                $policy_id = $value['master_policy_id'];
                $this->db->query("UPDATE proposal_policy SET cron_count = cron_count + 1 WHERE lead_id = '$lead_id' and master_policy_id = '$policy_id'");
                $req_details['lead_id'] = $lead_id;
                $req_details['plan_id'] = $plan_det['plan_id'];
                $response = curlFunction(SERVICE_URL . '/customer_api/generate_policy_ic_api_call', $req_details);
            }
        }
    }
    public function quotedetails()
    {
        $req_data = [];
//print_r($_POST);die;
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');

        $req_data['plan_id'] = $_POST['plan_id_see'];
        $req_data['master_policy_id'] = $_POST['policy_id_see'];
        $req_data['creditor_id'] = $_POST['creditor_id_see'];

        $response = json_decode(curlFunction(SERVICE_URL . '/customer_api/getQuoteDetails', $req_data),TRUE);
   //  echo "<pre>";print_r($response);exit;

        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/quotedetails',$response);
        $this->load->view('template/customer_portal_footer.php');
    }

    public function updatePremium(){
        $plan_sa = $this->input->post('plan_sa');
        // echo $plan_sa;
        $plan_sa = explode(',', $plan_sa);
        $req_data['plan_sa'] = $plan_sa;

        echo $response = curlFunction(SERVICE_URL . '/customer_api/updatePremium', $req_data);


    }

    public function update_nominee_details(){
        $req_data = [];

        $lead_id = $this->session->userdata('lead_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $trace_id = $this->session->userdata('trace_id');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $customer_id = $this->session->userdata('customer_id');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $plan_id = $this->session->userdata('plan_id');
        $req_data['plan_id'] = $plan_id;
        $req_data['lead_id'] = $lead_id;
        $req_data['customer_id'] = $customer_id;
        $req_data['trace_id'] = $trace_id;

        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }

        if($this->input->is_ajax_request()){

            $req_data['nominee_name'] = $this->input->post('nominee_name');
            $req_data['nominee_relation'] = $this->input->post('nominee_relation');
            $req_data['nominee_dob'] = $this->input->post('nominee_dob');
            $req_data['nominee_contact_number'] = $this->input->post('nominee_contact_number');


            if($_POST){
                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/updateNomineeDetails', $req_data);
                if($checkDetails){
                    $data['status'] = $checkDetails;
                    $data['message'] = "Nominee details updated successfully !";
                    echo json_encode($data);
                }
            }
        };
    }

    public function proposal_summary(){
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');

        $payment_details = json_decode(curlFunction(SERVICE_URL . '/customer_api/checkPaymentStatus',['lead_id'=>encrypt_decrypt_password($this->session->userdata('lead_id'), 'D')]), TRUE);

        if(!empty($payment_details) && $payment_details['payment_status']=='Success' ){
            redirect('/quotes/success_view/'.$this->session->userdata('lead_id').'?lead_id='.$this->session->userdata('lead_id'));
        }
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        $req_data['premium'] = $this->session->userdata('premium');
        $req_data['plan_name'] = $this->session->userdata('plan_name');
        $req_data['creditor_logo'] = $this->session->userdata('creditor_logo');
        $is_normal_customer = $this->session->userdata('is_normal_customer');
        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        $nominee_details = curlFunction(SERVICE_URL . '/customer_api/getNomineeDetails', $req_data);
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details', [

                'lead_id' => $req_data['lead_id'],
                'customer_id' => $req_data['customer_id'],

            ]);
       //  print_R($checkDetails);die;
        $get_summary_details = curlFunction(SERVICE_URL . '/customer_api/get_summary_details', $req_data);
       // var_dump($get_summary_details);exit;
        $premium_details = curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data);
        $premium_details = json_decode($premium_details, TRUE);
       /*var_dump($premium_details);
       exit;*/

        if($is_normal_customer){
            if(array_key_exists('policy_data',$premium_details)){
                $data['premium_details'] = $premium_details['policy_data'] ;
            }else{
                $data['premium_details'] = array('') ;
            }

        }else{
            $data['premium_details'] =  $req_data ;

        }
        if(array_key_exists('policy_data',$premium_details)){
            $data['duration'] = $premium_details['policy_data']['duration'] ;
        }
/*
        echo '<pre>';
        print_r($data);
       exit;*/
        //get policy SI type details
        if($this->session->userdata('is_normal_customer')){
            if(!empty(encrypt_decrypt_password($this->session->userdata('policy_id'), 'D'))){
                $policyId = encrypt_decrypt_password($this->session->userdata('policy_id'), 'D');
            }else{
                $policyId = $this->session->userdata('policy_id');
            }
            $SItype=$this->db->query('select suminsured_type_id from master_policy_si_type_mapping where master_policy_id='.$policyId.' AND isactive')->row()->suminsured_type_id;
        }else{
            $SItype=$this->session->userdata('si_type_id');
        }




//         echo "<PRE>";print_r($premium_details);exit;
        $data['get_summary_details'] = json_decode($get_summary_details, TRUE);
        if(empty( $data['duration'] ) && !empty($data['get_summary_details']['insured_member'])){
            foreach ($data['get_summary_details']['insured_member'] as $key => $value) {
                 $data['duration'] = $value['tenure'];
                // code...
            }
        }
//        var_dump($get_summary_details);
//        exit;
        $data['member_details'] = json_decode($checkDetails, TRUE);
        //echo "<PRE>";print_r($data['member_details']);exit;
        $data['nominee_details'] = json_decode($nominee_details, TRUE);
        $data['customer_details'] = json_decode($get_customer_details, TRUE);
        $data['si_type'] = $SItype;

        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }
        $data['tc_text']='Accept Terms and conditions';
        if($this->session->userdata('partner_id_session') && $this->session->userdata('product_id_session')){
            $creditor_id = encrypt_decrypt_password($this->session->userdata('partner_id_session'),"D");
            $Q_tc_text=$this->db->query("select tc_text from link_ui_configuaration where plan_id=".$req_data['plan_id']);
            if($this->db->affected_rows() > 0){
                $data['tc_text']=$Q_tc_text->row()->tc_text;
            }else{
                $data['tc_text']='Accept Terms and conditions';
            }
        }
        else{
            $creditor_id = encrypt_decrypt_password($this->session->userdata('partner_id_session'),"D");
            $Q_tc_text=$this->db->query("select tc_text from link_ui_configuaration where plan_id =".$creditor_id);
            if($this->db->affected_rows() > 0){
                $data['tc_text']=$Q_tc_text->row()->tc_text;
            }else{
                $data['tc_text']='Accept Terms and conditions';
            }
        }

        $lead_id = encrypt_decrypt_password( $this->session->userdata('lead_id'),'D');

        $deductable = $this->db->query("select deductable from member_ages where lead_id = '".$lead_id."'")->row_array();

        $data['deductable']=$deductable;

        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/proposal_summary',$data);
        $this->load->view('template/customer_portal_footer.php');
    }

    public function fetchPremium()
    {
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['policy_id'] = $this->session->userdata('policy_id');

        $premium_details = curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data);
        $premium_details = json_decode($premium_details, TRUE);
        // print_r($premium_details);exit;
        echo json_encode($premium_details['policy_data']);exit;
    }

    public function redirect_to_pg_bk(){
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        $get_customer_details = json_decode($get_customer_details, TRUE);
        $premium_details = curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data);
        $premium_details = json_decode($premium_details, TRUE);
        $premiumAmount = $premium_details['policy_data']['premium_rate'];
        //echo "<pre>";print_r($premium_details);exit;
        $leadId = $get_customer_details['customer_details']['lead_id'];
        $customer_name = $get_customer_details['customer_details']['full_name'];
        $mobileNumber = $get_customer_details['customer_details']['mobile_no'];
        $email = $get_customer_details['customer_details']['email_id'];
        $PaymentMode = "PO";
        $ProductInfo = $premium_details['policy_data']['creaditor_name'].' - '.$premium_details['policy_data']['plan_name'];
        $Source = 'ABC';
        $Vertical = 'ABCGRP';
        $ReturnURL = base_url("/quotes/success_view/" );
        $UniqueIdentifier = "LEADID";
        $UniqueIdentifierValue = $leadId;
        $CustomerName = $customer_name;
        $Email = $email;
        $PhoneNo = substr(trim($mobileNumber) , -10);
        $FinalPremium = round($premiumAmount, 2);

        $CKS_data = $Source . "|" . $Vertical . "|" . $PaymentMode . "|" . $ReturnURL . "|" . $UniqueIdentifier . "|" . $UniqueIdentifierValue . "|" . $CustomerName . "|" . $Email . "|" . $PhoneNo . "|" . $FinalPremium . "|" . $ProductInfo . "|" . $this->hash_key;
        // print_pre($CKS_data);exit;

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

        /*$request_arr = ["lead_id" => $leadId, "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result) , "product_id" => $productId, "type" => "payment_request_post"];

        $dataArray['tablename'] = 'logs_docs';
        $dataArray['data'] = $request_arr;
        // print_pre($dataArray['data']);exit;
        $this
            ->Logs_m
            ->insertLogs($dataArray);*/

        if ($result && $result['Status'])
        {

            /*$query_check = $this
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
            }*/

            // echo "WELCOME To ABHI";
            // echo $result['PaymentLink'];exit;
            if (!preg_match("@^[hf]tt?ps?://@", $result['PaymentLink'])) {
                $result['PaymentLink'] = "http://" . $result['PaymentLink'];
            }
            redirect($result['PaymentLink']);
        }
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
    public function secure_random_string($length) {
    $random_string = '';
    for($i = 0; $i < $length; $i++) {
        $number = random_int(0, 36);
        $character = base_convert($number, 10, 36);
        $random_string .= $character;
    }
    return $random_string;
}
public function generate_coi_issuance($lead_id,$master_policy_id,$api_label_id,$dir)
{
    $req_details['lead_id'] = $lead_id;
    $req_details['plan_id'] = $master_policy_id;

    if($api_label_id == 'traktion_supertoup') {
        $coi_generate_status = curlFunction(SERVICE_URL . '/customer_api/generate_policy_ic_api_call', $req_details);
        // print_r($coi_generate_status);die;
        $response_data = json_decode($coi_generate_status, true);


        if ($response_data['status'] == 'error') {
            $data['success'] = false;
            $data['coi_download'] = 0;

            $data['error'] = 'true';
            $data['html'] = '

                <p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>
                
                <p style="font-size: 18px;
    font-weight: 600;
    text-align: center;
    color: #107591;">
                        You will receive your Certificate of issuance shortly on your mail ID.
                        </p>
                ';
            //if (!empty($status) && $status == true) {
            curlFunction(SERVICE_URL . '/customer_api/sendNotification', ['lead_id' => encrypt_decrypt_password($lead_id, 'E'), 'event' => 'Payment done but issuance failed']);
            ///}


        } else {
            //COI Issuance
            $req_data1['lead_id'] = $lead_id;

            $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1), TRUE);

            $coi_no = $res["data"]["certificate_number"];
            //coi issuance
            $fqrequest = ["Policy_number" => $coi_no, "ProposalType" => "Certificate"];

            insert_application_log($lead_id, 'coi_download_request', json_encode($fqrequest), "", 123);

            $insert_id = $this
                ->db
                ->insert_id();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://healthbuzzapi-grp-3scale-apicast-production.uatwebservices.manipalcigna.com:443/HealthBuzzAPIUAT/api/Card/PolicyCopy ",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
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


            $request_arr = ["response_data" => json_encode($response)];
            $this
                ->db
                ->where("log_id", $insert_id);
            $this
                ->db
                ->update("application_logs", $request_arr);

            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {

                return array(
                    "status" => "error",
                    "msg" => $err
                );
            } else {

                $newArr = json_decode($response, true);
                $errorObj_det = $newArr['ErrorObj'];
                foreach ($errorObj_det as $errorObj) {
                    if ($errorObj['ErrorCode'] == '0') {
                        $newRes = $newArr['ResponseObj']['PolicyCopy'];
                        foreach ($newRes as $value) {
                            $decoded = base64_decode($value['byteStrem']);
                            $file = './assets/health_coi/' . $dir . '/coi_' . str_replace('/', '_', $coi_no) . '.pdf';

                            $url = FRONT_URL . 'assets/health_coi/' . $dir . '/coi_' . str_replace('/', '_', $coi_no) . '.pdf';
                            $data_coi = array(
                                'COI_url' => $file,
                            );

                            $this->db->where('certificate_number', $coi_no);
                            $this->db->where('lead_id', $lead_id);
                            $this->db->update('api_proposal_response', $data_coi);
                            $filename = "./assets/health_coi/" . $dir . "/";
                            if (file_exists($filename)) {

                            } else {

                                mkdir('./assets/health_coi/' . $dir, 0777);
                            }
                            file_put_contents($file, $decoded);

                        }

                    }
                }
            }

            $req_data1['lead_id'] = $lead_id;

            if ($coi_no) {
                //if (!empty($status) && $status == true) {
                curlFunction(SERVICE_URL . '/customer_api/sendNotification', ['lead_id' => encrypt_decrypt_password($lead_id, 'E'), 'event' => 'Policy issuance successful']);
                // }
                $data['error'] = 'true';
                $data['html'] = '<p class="g-success mt-1 text-center">Policy Issuance Successfully</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">' . $coi_no . '</span>
                        </p>
                    </div>';
            } else {
                $data['coi_download'] = 0;

                $data['error'] = 'true';
                $data['html'] = '<p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>
                
                <p style="font-size: 18px;
    font-weight: 600;
    text-align: center;
    color: #107591;">
                        You will receive your Certificate of issuance shortly on your mail ID.
                        </p>
                ';
                //if (!empty($status) && $status == true) {
                curlFunction(SERVICE_URL . '/customer_api/sendNotification', ['lead_id' => encrypt_decrypt_password($lead_id, 'E'), 'event' => 'Payment done but issuance failed']);
                // }

            }

        }
    }

    return $data;
}
    public function success_view($lead_id)
    {//echo 123;die;
        session_destroy();
           //  print_r($_POST);
        $encryptedLeadId =  $lead_id;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        $payment_mode_id = $this->db->query("select ppm.payment_mode_id from plan_payment_mode ppm,lead_details l where ppm.master_plan_id = l.plan_id and l.lead_id = '$lead_id'")->row()->payment_mode_id;


        // $api->order->payments('pay_LuDiKHWhCmA9Hp');
        //  echo "<PRE>";
        // print_r($payment_obj);die;
        //   echo "<PRE>";print_r($_POST);
       // $payment = (array)$payment_obj;
//print_r($payment_obj);

        $req_data1['lead_id'] = $lead_id;
        $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1), TRUE);
        $payment_details = json_decode(curlFunction(SERVICE_URL . '/customer_api/checkPaymentStatus', $req_data1), TRUE);

       
        $proposal_policy  = $this->db->query("select proposal_policy_id from proposal_policy where lead_id=" . $lead_id)->row();
        $Q_customer_support_number=$this->db->query("select customer_support_number from link_ui_configuaration where creditor_id=".$payment_details['creditor_id']);
            if($this->db->affected_rows() > 0){
                $customer_support_number=$Q_customer_support_number->row()->customer_support_number;
            }else{
                $customer_support_number='1800-266-9693';
            }
            $data['customer_support_number']=$customer_support_number;
            $data['success'] = true;
         // print_r($res);
         // print_r($payment_details);die;
         // print_r($proposal_policy);die;
        if (!empty($res["data"]["certificate_number"])) {
           // echo 123;die;
            $coi_no = $res["data"]["certificate_number"];
            $master_policy_id = $this->db->query("select plan_id from lead_details where lead_id=" . $lead_id)->row()->plan_id;

            $coi_type_det = $this->db->query("select coi_type,ic_api,plan_id,coi_download from master_plan where plan_id=" . $master_policy_id)->row_array();
            $data['coi_type'] = $coi_type_det['coi_type'];

            $data['lead_id'] = $lead_id;
            $data['ic_api'] = $coi_type_det['ic_api'];
            $data['coi_download'] = $coi_type_det['coi_download'];
            $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">' . $coi_no . '</span>
                        </p>
                    </div>';
            curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$encryptedLeadId,'dropout_page'=>'10']);        
            //print_r($data);
           // die;
            $this->load->view('template/customer_portal_header.php');
            $this->load->view('quotes/thank_you', $data);
            $this->load->view('template/customer_portal_footer.php');


        } else if(!empty($payment_details)  && $payment_details['payment_status']=='Success' && empty($proposal_policy)){
            

            $master_policy_id = $this->db->query("select plan_id from lead_details where lead_id=" . $lead_id)->row()->plan_id;

            $coi_type_det = $this->db->query("select coi_type,ic_api,plan_id,coi_download,payment_page from master_plan where plan_id=" . $master_policy_id)->row_array();
        
            $data['lead_id'] = $lead_id;
            $data['payment_page'] = $coi_type_det['payment_page'];
            $data['go_proposal'] = true;
            $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>

                        <p>Payment ID:
                            <span> ' . $payment_details["transaction_number"] . '</span>
                        </p>
                       
                    </div>';
            curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$encryptedLeadId,'dropout_page'=>'12']);        
            $this->load->view('template/customer_portal_header.php');
            $this->load->view('quotes/thank_you', $data);
            $this->load->view('template/customer_portal_footer.php');

        } else {
            if($payment_mode_id == 1){
                $coi_generate=false;
                if(!empty($proposal_policy) && !empty($payment_details)  && $payment_details['payment_status']=='Success' ){
                    $coi_generate = true;
                    $paymentId =  $payment_details['transaction_number'];
                }
                $master_policy_id = $this->db->query("select plan_id from lead_details where lead_id=" . $lead_id)->row()->plan_id;
                $coi_type_det = $this->db->query("select coi_type,ic_api,plan_id,coi_download,creditor_id,is_RelationBased,payment_page from master_plan where plan_id=" . $master_policy_id)->row_array();


                $req_data2['pg_response'] = $_REQUEST;
                $req_data2['lead_id'] = $lead_id;

                if(!empty($payment_details)  && $payment_details['payment_status']!='Success' ){
                    $success = false;
                    insert_application_log($lead_id, 'Payment_response', "", json_encode($_POST), 123);
                    // echo $lead_id;exit;

                    $error = "Payment Failed";
                   
                
                    $api = new Api($this->keyId, $this->keySecret);
                    $payment_obj = $api->payment->fetch($_POST['razorpay_payment_id']);
                    if (isset($_POST['razorpay_payment_id'])) {
                        $paymentId = $_POST['razorpay_payment_id'];
                        if ($payment_obj->status === 'captured') {
                            $status = true;
                            


                            $master_customer = $this->db->query("select * from master_customer where lead_id=" . $lead_id)->row_array();
                            $quote_data = $this->db->query("select * from master_quotes where lead_id=" . $lead_id)->row_array();

                            $update_payment_status = curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data2);

                            if(!empty($proposal_policy)){
                                $coi_generate = true;
                            }else{
                                $data['lead_id'] = $lead_id;
                                $data['payment_page'] = $coi_type_det['payment_page'];
                                $data['go_proposal'] = true;
                                $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                                        <div class="color_red text-left back-thnk">
                                            <p>Lead ID:
                                                <span id="lead_view"> ' . $lead_id . '</span>
                                            </p>

                                            <p>Payment ID:
                                                <span> ' . $paymentId. '</span>
                                            </p>
                                           
                                        </div>';
                                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$encryptedLeadId,'dropout_page'=>'12']);        
                                $this->load->view('template/customer_portal_header.php');
                                $this->load->view('quotes/thank_you', $data);
                                $this->load->view('template/customer_portal_footer.php');
                                return true;

                            }
                        }
                        else {
                            $data['success'] = false; 
                            $data['html'] = '<p class="g-success mt-1 text-center">Your payment failed</p>
                    <p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>
                             <p> <span>' . $error . '</span></p>';
                        }
                    }


                }
                if($coi_generate==true){
                        
                        $update_payment_status = curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data2);

                        $data['coi_type'] = $coi_type_det['coi_type'];
                        $data['ic_api'] = $coi_type_det['ic_api'];
                        $data['coi_download'] = $coi_type_det['coi_download'];
                        $coi_type = $coi_type_det['coi_type'];
                        $plan_id = $coi_type_det['plan_id'];
                        $creditor_id = $coi_type_det['creditor_id'];
                        $req_data1['lead_id'] = $lead_id;
                        $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1), TRUE);
                        $coi_no = $res["data"]["certificate_number"];
                        $cond = '<p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">' . $coi_no . '</span>
                        </p>';
                        $payment_details = $this->db->query("select * from proposal_payment_details as ppd join payment_modes as pm where ppd.payment_mode = pm.payment_mode_id and ppd.lead_id= '$lead_id' and pm.isactive =1")->row_array();
                        if ($payment_details['payment_mode'] == 1) {
                            $collection_mode = "online Collection";
                        }
                        if ($coi_type == 2) {


                            if ($coi_type_det['ic_api'] == 1) {

                                $proposal_data = $this->db->query("select pp.*,pd.nominee_relation,nominee_first_name,nominee_last_name,pd.customer_id from proposal_policy as pp join proposal_details as pd where pp.proposal_details_id = pd.proposal_details_id and pd.plan_id = '$plan_id' and pp.lead_id = '$lead_id'")->result_array();
                                foreach ($proposal_data as $proposal_value) {

                                    $group_code  = $proposal_value['group_code'];
                                    $adult_count  = $proposal_value['adult_count'];

                                    $child_count =  $proposal_value['child_count'];
                                    $sum_Insured = $proposal_value['sum_insured'];
                                    $master_policy = $this->db->query("select policy_number,scheme_code,payer_code,Sourcing_br_Sol_ID,payer_relation,product_code from master_policy where policy_id = " . $proposal_value['master_policy_id'] ." and isactive = 1")->row_array();
                                    $master_policy_premium = $this->db->query("select si_type from master_policy_premium where master_policy_id = " . $proposal_value['master_policy_id'] ." and group_code = '$group_code' and  sum_insured = '$sum_Insured' and adult_count = '$adult_count' and child_count = '$child_count' and isactive = 1")->row_array();
                                    if(empty($master_policy_premium))
                                    {
                                        $master_policy_premium = $this->db->query("select si_type from master_policy_premium where master_policy_id = " . $proposal_value['master_policy_id'] ." and group_code = '$group_code' and  sum_insured = '$sum_Insured' and isactive = 1")->row_array();

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
                                        if($coi_type_det['is_RelationBased'] == 1)
                                        {
                                            $proposal_value['member_code'] = $this->db->query("select member_code from plan_base_member_code where plan_id = " . $plan_id ." and relation_id = '$member_id'  and isactive = 1")->row_array()->member_code;

                                        }
                                        $member[] = ["Member_No" => $i + 1, "Member_Salutation" => $checkMember[$i]['policy_member_salutation'], "Member_First_Nm" => $checkMember[$i]['policy_member_first_name'], "Member_Last_Nm" => $checkMember[$i]['policy_member_last_name'], "Member_Gender" => ($checkMember[$i]['policy_member_gender'] == "Male") ? "M" : "F", "Member_DOB" => $checkMember[$i]['policy_member_dob'], "Member_Relation_Cd" => $checkMember[$i]['policy_member_relation_code'], "Member_Occupation_Cd" => null, "Member_Suminsured" => $checkMember[$i]['cover'], "Member_MaritalStatus" => ($checkMember[$i]['policy_member_marital_status'] =='Married') ? 1 : "", "Member_Height" => "", "Member_Weight" => "", "Nominee_Nm" => $proposal_value['nominee_first_name'] . " " . $proposal_value['nominee_last_name'], "Nominee_Relation_Code" => $proposal_value['nominee_relation'], "DailySmoking_Quantity" => "", "HardLiquorConsumed_Quantity" => "", "WineConsumed_Quantity" => "", "BeerConsumed_Quantity" => "", "MasalaGutka_Quantity" => "", "Details_of_Disability" => "", "Details_of_Injury" => "", "Previous_Policy_No" => "", "Previous_Insurer_Cd" => "", "Previous_Policy_StartDate" => "", "Previous_Policy_EndDate" => "", "Previous_Suminsured" => "", "Previous_IsClaim" => 0, "Monthly_Income" => "", "LoadingGCI" => "", "LoadingGPA" => "", "Plan_Cd" =>$proposal_value['group_code']];

                                    }
                                    $first_name = $master_customer['first_name'];
                                    $first_name = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $first_name)));

                                    $first_name = explode(" ", $first_name, 2);
                                    if (empty($master_customer['last_name'])) {
                                        $master_customer['first_name'] = $first_name[0];
                                        $master_customer['last_name'] = $first_name[1];
                                    }


                                    $fqrequest = ["ProposalDetailObj" => ["Quote_Number" => "", "Proposer_Salutation" => $master_customer['salutation'], "Proposer_First_Nm" => $master_customer['first_name'], "Proposer_Middle_Nm" => $master_customer['middle_name'], "Proposer_Last_Nm" => $master_customer['last_name'], "Proposer_Email" => $master_customer['email_id'], "Proposer_Mobile" => $master_customer['mobile_no'], "Proposer_DOB" => $master_customer['dob'], "Proposer_Gender" => ($master_customer['gender'] == "Male") ? "M" : "F", "Proposer_MaritalStatus" => ($master_customer['marital_status'] == 'Married') ? 1 : "", "Proposer_AnnualIncome" => null, "Proposer_Nationality" => "Indian", "Proposer_PanNumber" => "", "Proposer_Address1" => $master_customer['address_line1'], "Proposer_Address2" => $master_customer['address_line2'], "Proposer_Address3" => $master_customer['address_line3'], "Proposer_Area" => "", "Proposer_PinCode" => $master_customer['pincode'], "Proposer_LandineNumber" => "", "Bank_AccountNumber" => "", "Sourcing_br_Sol_ID" => $master_policy['Sourcing_br_Sol_ID'], "Product_Cd" => $master_policy['product_code'], "Scheme_Cd" => $master_policy['scheme_code'], "Master_Policy_No" => $master_policy['policy_number'], "Plan_Cd" => $proposal_value['group_code'], "BA_Number" => $BA_number, "SumInsured_Type" => $master_policy_premium['si_type'], "Member_Type_Cd" => trim($proposal_value['member_code']), "Policy_From" => date('Y-m-d', strtotime($payment_details['transaction_date'])), "COI_Number" => "", "Policy_Tenure" => "1", "Proposer_Suminsured" => $proposal_value['sum_insured'], "Proposer_Deductible" => (!empty($proposal_value['deductable'])) ? $proposal_value['deductable'] : ""], "MemberDetailObj" => $member, "MedicalLifeStyleObj

" => [], "PreExistingObj" => [], "OptionalCoverObj" => [], "PaymentObj" => ["Payer_Code" => $master_policy['payer_code'], "Payer_Type" => "CUSTOMER", "Payer_Name" => $master_customer['first_name'] . " " . $master_customer['last_name'], "Payer_Relation" => $master_policy['payer_relation'], "Collection_Amount" => round($payment_details['premium']), "Collection_Receive_Date" => date('Y-m-d', strtotime($payment_details['transaction_date'])), "Collection_Mode" => ($payment_details['payment_mode'] == 1) ? "Online Collection" : $payment_details['payment_mode_name'], "Collection_SubType" => "Razor Pay", "Cheque_Type" => "", "Instrument_Number" => $payment_details['transaction_number'], "Instrument_Date" => date('Y-m-d', strtotime($payment_details['transaction_date'])), "IFSC_Code" => "HDFC0002457", "Mobile_Number" => $master_customer['mobile_no'], "Email_Id" => $master_customer['email_id'], "Bank_MICRCode" => "167", "Bank_Account_Number" => "", "MerchantId" => "", "MerchantName" => ""]];
                                    insert_application_log($lead_id, 'full_quote_request', json_encode($fqrequest), "", 123);
//echo "<pre>";
//print_r( json_encode($fqrequest));die;
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
                                        return array(
                                            "status" => "error",
                                            "msg" => $err
                                        );
                                        $data['success'] = false;
                                        $data['error'] = 'false';
                                        $data['html'] = '<p class="g-success mt-1 text-center">Policy Issuance failed</p>
                <p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>';
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
                                                        "policy_no" => $Response['Policy_Number']

                                                    );


                                                    $cert_check = $this->db->select('*')->from('api_proposal_response')
                                                        ->where('lead_id', $lead_id)
                                                        ->where('certificate_number', $Response['COI_Number'])
                                                        ->get()
                                                        ->row_array();

                                                    $this->db->where('lead_id', $lead_id);
                                                    $this->db->where('proposal_no',  $proposal_value['proposal_no']);
                                                        $this->db->update('api_proposal_response', $request_arr);

                                                        // $return_data['status'] = 'error';

                                                    $req_data1['lead_id'] = $lead_id;
                                                    $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1), TRUE);

                                                    $coi_no = $res["data"]["certificate_number"];
                                                    $data['error'] = 'true';
                                                    $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">' . $coi_no . '</span>
                        </p>
                    </div>';
                                                }
                                            } else {
                                                //  echo "<pre>";
                                                //   print_r($errorObj);die;
                                                ///------- @author : Guru --------------------------//

                                                insert_application_log($lead_id, 'failure_response', json_encode($errorObj['ErrorMessage']), "", 123);
                                                 $data['success'] = false;
                                               /* $data['html'] = '<p class="g-success mt-1 text-center">Policy Issuance failed</p>
                <p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>
                         <p> <span>' . $errorObj['ErrorMessage'] . '</span></p>';*/
                                                  $data['html'] = '<p class="g-success mt-1 text-center">Policy Issuance failed</p>
                                        <p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>';
                                                $data['error'] = 'false';

                                            }
                                        }
                                    }
                                }
                            } else {

                                $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                         <p>Payment ID:
                            <span> ' . $paymentId . '</span>
                        </p>
                        <p style="font-size: 18px;
    font-weight: 600;
    text-align: center;
    color: #107591;">
                        You will receive your Certificate of issuance shortly on your mail ID.
                        </p>
                    </div>';
                            }
                        } else {
                            $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                        <p>Payment ID:
                            <span> ' . $paymentId . '</span>
                        </p>
                        ' . $cond . '
                    </div>';
                            $data['error'] = 'true';
                        }
                    

                }
            }

             else {
                    /* ini_set('display_errors', 1);
                     ini_set('display_startup_errors', 1);
                     error_reporting(E_ALL);*/
                    $req_data['lead_id'] = $lead_id;
                    //echo "in";exit;
                    //   print_r($req_data['lead_id']);die;
                    $update_payment_status = curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data);
                    $req_data['lead_id'] = $lead_id;

                    $master_policy_id = $this->db->query("select master_policy_id from api_proposal_response where lead_id=" . $lead_id)->row()->master_policy_id;
                    $coi_type = $this->db->query("select coi_type from master_plan where plan_id=(select plan_id from master_policy where policy_id=" . $master_policy_id . ")")->row()->coi_type;
                    $data['coi_type'] = $coi_type;
                    $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data), TRUE);
                    if ($res) {
                        if ($coi_type == 2) {
                            $data['error'] = 'true';
                            $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                        <p style="font-size: 18px;
    font-weight: 600;
    text-align: center;
    color: #107591;">
                        You will receive your Certificate of issuance shortly on your mail ID.
                        </p>
                    </div>';
                        } else {
                            $data['lead_id'] = $lead_id;
                            $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> ' . $lead_id . '</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">' . $res["data"]["certificate_number"] . '</span>
                        </p>
                    </div>';
                        }


                    }
                }
                curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$encryptedLeadId,'dropout_page'=>'10']);
            //CD balance update mail
            $master_policy_id = $this->db->query("select plan_id from lead_details where lead_id=" . $lead_id)->row()->plan_id;

            $coi_type_det = $this->db->query("select plan_id,creditor_id,payment_first,payment_page from master_plan where plan_id=" . $master_policy_id)->row_array();
            $plan_id = $coi_type_det['plan_id'];
            $creditor_id = $coi_type_det['creditor_id'];
            $response_cd= CheckCDThreshold($creditor_id,$plan_id,0);
            $q_creditor_id= $this->db->query("select creditor_id,ceditor_email,creaditor_name from master_ceditors where creditor_id = '$creditor_id' ")->row_array();
            $creditor_id=$q_creditor_id['creditor_id'];
            $ceditor_email=$q_creditor_id['ceditor_email'];
            $creaditor_name=$q_creditor_id['creaditor_name'];
            $data_arr_cd=$response_cd['data'];
            $cd_threshold=$data_arr_cd['threshold_amount'];
            $balance=$data_arr_cd['balance'];
            $collection_amt=$data_arr_cd['collection_amount'];
            $data['payment_page']=$coi_type_det['payment_page'];
            $data['payment_first']=$coi_type_det['payment_first'];
            if($response_cd['status'] == 201){
                $mail=sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$response_cd['msg']);
            }else{
                if($response_cd['msg']=="NegativeAllow"){
                    $mail=sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$response_cd['msg']);
                }else if($response_cd['msg']=="LessCD"){
                    $mail=sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$response_cd['msg']);
                }else{
                    $response = array('status' => 200, 'msg' => "Success", 'data' => array());
                }
            }
            
                $this->load->view('template/customer_portal_header.php');
                $this->load->view('quotes/thank_you', $data);
                $this->load->view('template/customer_portal_footer.php');

        }

    }
    public function redirect_to_pg(){
       
        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }

        $api = new Api($this->keyId, $this->keySecret);
        $data['lead_id'] = encrypt_decrypt_password($this->session->userdata('lead_id'), 'D');;
        $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $data),TRUE);
        
        if(isset($res['data']) && $res['status'] == 200){
            redirect('/quotes/success_view/'.$this->session->userdata('lead_id').'?lead_id='.$this->session->userdata('lead_id'));
        }
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        if(is_numeric( $this->session->userdata('policy_id'))){
            $req_data['policy_id'] = encrypt_decrypt_password($this->session->userdata('policy_id'),'E');
        }
        $policy_id=encrypt_decrypt_password($req_data['policy_id'],'D');
        


        //echo encrypt_decrypt_password($req_data['lead_id'], 'D');

        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        $get_customer_details = json_decode($get_customer_details, TRUE);
        $proposal_details = curlFunction(SERVICE_URL . '/customer_api/getProposalDetails', $req_data);

        $proposal_details = json_decode($proposal_details, TRUE);
        $premiumAmount=0;
 
        foreach ($proposal_details['proposal_details'] as $prow){
            
            if(!empty($prow['tax_amount']))
            {
                $amt = $prow['tax_amount'];
            }
            else{
                $amt = $prow['premium_amount'];
            }
            $premiumAmount += $amt;
        }
        $payment_mode_id=$this->db->query("select payment_mode_id from plan_payment_mode where master_plan_id=".$req_data['plan_id'])->row()->payment_mode_id;
        $payment_first = $this->db->query("select payment_first from master_plan where plan_id = '".$req_data['plan_id']."'")->row()->payment_first;
        if($payment_first != 1){
            curlFunction(SERVICE_URL . '/customer_api/updateLeadLastVisited', ['lead_id'=>$this->session->userdata('lead_id'),'dropout_page'=>'9']);

        }

        if(empty($premiumAmount)){
            $premiumAmount = $this->session->userdata('premium');
            $get_policy_detC = $this->db->query("select creditor_id from master_plan where plan_id = '".$req_data['plan_id']."'")->row_array();
             $creditor_id = $get_policy_detC['creditor_id'];
             
             $newData = array(
                'creditor_id' => $creditor_id,
                'lead_id' => encrypt_decrypt_password($req_data['lead_id'],'D'),
                'trace_id' =>  encrypt_decrypt_password($req_data['trace_id'],'D'),
                'premium' => $premiumAmount,
                'payment_status' => 'Pending',
                'payment_mode' => $payment_mode_id,
                'proposal_status' => 'PaymentPending',
                'created_at' => date("Y-m-d H:i:s"),
                'remark' => 'Payment Initiate',
                'payment_date' => date("Y-m-d H:i:s"),
            );
            $this->db->insert("proposal_payment_details", $newData);
        }
        
        if($payment_mode_id == 4){

          //  echo $policy_id;die;
            $get_policy_detC = $this->db->query("select creditor_id,plan_id from master_policy where policy_id = '$policy_id'")->row_array();
            $creditor_id = $get_policy_detC['creditor_id'];
            $plan_id = $get_policy_detC['plan_id'];
           // $get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
            $response_cd= CheckCDThreshold($creditor_id,$plan_id,$premiumAmount);

            $q_creditor_id= $this->db->query("select creditor_id,ceditor_email,creaditor_name from master_ceditors where creditor_id = '$creditor_id' ")->row_array();
            $creditor_id=$q_creditor_id['creditor_id'];
            $ceditor_email=$q_creditor_id['ceditor_email'];
            $creaditor_name=$q_creditor_id['creaditor_name'];
            $data_arr_cd=$response_cd['data'];
            $cd_threshold=$data_arr_cd['threshold_amount'];
            $balance=$data_arr_cd['balance'];
            $collection_amt=$data_arr_cd['collection_amount'];
           
            if($response_cd['status'] == 201){
                $mail=sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$response_cd['msg']);
               echo $response_cd['msg'];
               die;
            }else{
                 
                if($response_cd['msg']=="NegativeAllow"){
                    $mail=sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$response_cd['msg']);
                   

                }else if($response_cd['msg']=="LessCD"){
                    $mail=sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$response_cd['msg']);

                }else{
                    $response = array('status' => 200, 'msg' => "Success", 'data' => array());

                }
            }
            /*if (count($get_policy_det) > 0) {
                $initial_cd = $get_policy_det['initial_cd'];
                $deposit = $get_policy_det['deposit'];
                $cd_threshold_percent = $get_policy_det['cd_threshold'];
                $total_amount=($initial_cd + $deposit);
                $cd_threshold=($total_amount) * ($cd_threshold_percent/100);
                $cd_utilised = $get_policy_det['cd_utilised'];
                $balance = ($initial_cd + $deposit) - $cd_utilised;
            }
            if ($balance > $cd_threshold && ($balance != '' && $balance != '0')) {
                $cd_balance_remain = $balance - $premiumAmount;
                $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                $policy_cd_data['cd_utilised'] = $cd_utilised+$premiumAmount;
                $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

            } elseif ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
                echo "Not Sufficient CD Balance.";exit;
            } else {
                $cd_balance_remain = $balance - $premiumAmount;
                $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                $policy_cd_data['cd_utilised'] = $cd_utilised+$premiumAmount;
                $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");


            }*/

            $cd_data = array(
                'type' => 2,
                'amount' => $premiumAmount,
                'lead_id' => $data['lead_id'],
                'creditor_id' => $creditor_id,
                'type_trans' =>"Policy Issuance",
            );
            $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);
            
            redirect('/quotes/success_view/'.$this->session->userdata('lead_id').'?lead_id='.$this->session->userdata('lead_id'));
        }
        //$premiumAmount = $proposal_details['proposal_details'][0]['premium_amount'];
        $premium_details = json_decode(curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data),true);
       
            $leadId = $get_customer_details['customer_details']['lead_id'];

            $cred_det = $this->db->query("select creditor_logo from master_ceditors where creditor_id = '$creditor_id'")->row_array();

        //echo "123".$leadId;exit;
        $customer_name = $get_customer_details['customer_details']['full_name'];
        $mobileNumber = $get_customer_details['customer_details']['mobile_no'];
        $email = $get_customer_details['customer_details']['email_id'];
        $address = trim($get_customer_details['customer_details']['address_line1'].' '.$get_customer_details['customer_details']['address_line2'].' '.$get_customer_details['customer_details']['address_line3']);
        $PaymentMode = "PO";
        $ProductInfo = $premium_details['policy_data']['creaditor_name'].' - '.$premium_details['policy_data']['plan_name'];
        $Source = 'ABC';
        $Vertical = 'ABCGRP';
        $ReturnURL = base_url("/quotes/success_view/".$leadId.'?lead_id='.$leadId);
        $UniqueIdentifier = "LEADID";
        $UniqueIdentifierValue = $leadId;
        $CustomerName = $customer_name;
        $Email = $email;
        $PhoneNo = substr(trim($mobileNumber) , -10);
        $FinalPremium = round($premiumAmount, 2);

        $orderData = [
            'receipt'         => 3456,
            'amount'          => $FinalPremium * 100, // 2000 rupees in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $api->order->create($orderData);

        $razorpayOrderId = $razorpayOrder['id'];

        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

        $displayAmount = $amount = $orderData['amount'];
        $displayCurrency = $this->displayCurrency;

        if ($displayCurrency !== 'INR')
        {
            $url = 'https://api.razorpay.com/v1/orders';
            $exchange = json_decode(file_get_contents($url), true);

            $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
        }

        $checkout = 'automatic';

        if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
        {
            $checkout = $_GET['checkout'];
        }

        $data = [
            "key"               => $this->keyId,
            "amount"            => $amount,
            "name"              => 'FynTune',
            "description"       => $ProductInfo,
            "image"             => "http://fyntune.com/images/logo/logo.png",
            "prefill"           => [
            "name"              => $customer_name,
            "email"             => $email,
            "contact"           => $mobileNumber,
            ],
            "notes"             => [
            "address"           => $address,
            "merchant_order_id" => "12312321",
            ],
            "theme"             => [
            "color"             => "#F37254"
            ],
            "order_id"          => $razorpayOrderId,
        ];


        $data['display_currency']  = $displayCurrency;
        $data['currency']  = $displayCurrency;
        $data['display_amount']    = $displayAmount/100;
        $data['customer_id']    = $req_data['customer_id'];
        $data['lead_id']    = encrypt_decrypt_password($leadId);

        $res['data'] = $data;
        $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');
        insert_application_log($lead_id, 'Payment_request', json_encode($data), "", 123);
        $res['prev_page']=$_SERVER["HTTP_REFERER"];
        //$json = json_encode($data);
        //$this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/pg_submit',$res);
        //$this->load->view('template/customer_portal_footer.php');
    }
    function updateRecord($tbl_name,$datar,$condition)
    {
        //$this -> db -> where($comp_col, $eid);
        $this -> db -> where("($condition)");
        $this -> db -> update($tbl_name,$datar);

        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return true;
        }
    }
    public function fetch_nominee_details(){
        $req_data = [];
        //echo 'inn';
        // print_r($_SESSION);exit;
        $lead_id = $this->session->userdata('lead_id');
        //$lead_id = encrypt_decrypt_password($lead_id, 'D');
        $trace_id = $this->session->userdata('trace_id');
        //$trace_id = encrypt_decrypt_password($trace_id, 'D');
        $customer_id = $this->session->userdata('customer_id');
        //$customer_id = encrypt_decrypt_password($customer_id, 'D');
        $plan_id = $this->session->userdata('plan_id');
        $req_data['plan_id'] = $plan_id;
        $req_data['lead_id'] = $lead_id;
        $req_data['customer_id'] = $customer_id;
        $req_data['trace_id'] = $trace_id;
        // print_r($req_data);exit;
        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }
        //echo 'ppp';

        if($this->input->is_ajax_request()){
        //echo 'here';


           // if($_POST){
                //echo 'fff';exit;
                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getNomineeDetails', $req_data);
                //print_r($checkDetails);exit;
                if($checkDetails){
                    $data['data'] = $checkDetails;
                    $data['status'] = 200;
                    $data['message'] = "Nominee details fetched successfully !";
                    echo json_encode($data);
                }else{
                    $data['data'] = $checkDetails;
                    $data['status'] = 400;
                    $data['message'] = "No data found !";
                }
            //}
        };
    }

    public function coidownload(){



         $leadId= $_GET['lead_id'];
        $req_data['lead_id'] = $leadId;
        {
     if(empty($leadId))

        $req_data['lead_id'] = $_POST['lead_id'];
        }
        //print_r($req_data['lead_id']);exit;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data),TRUE);
       // print_r($data);exit;
        $premiumDetails=$data['premium_details'];
         //print_r($data['premium_details']);exit;
        if(isset($data['data']) && !empty($data['data'])){

            $data = $data['data'];
            $data['premium_details'] = $premiumDetails;
            $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                'lead_id' => encrypt_decrypt_password($_POST['lead_id']),


            ]),TRUE);
            //print_r($checkDetails);exit;

            $data['insured_member'] = $checkDetails;

        }
        $html = $this
            ->load
            ->view("quotes/coi_pdf", $data, true);
        echo $html;

    }
    public function coidownloadNew(){



        $leadId= base64_decode($_REQUEST['lead_id']);
        if(!is_numeric($leadId)){
            $leadId=encrypt_decrypt_password($_REQUEST['lead_id'],'D');
        }
        $req_data['lead_id'] = $leadId;
        $req_data['policy_type'] = 1;

     if(empty($leadId))
     {
        $req_data['lead_id'] = $_POST['lead_id'];

        }
        //print_r($req_data['lead_id']);exit;
        $lead_plan = $this->db->query("select mp.api_label_id,mp.ic_api,mc.creaditor_name from lead_details as l join master_plan as mp  join master_ceditors as mc where l.plan_id = mp.plan_id and mp.creditor_id = mc.creditor_id and  l.lead_id = '$leadId'")->row_array();
//print_r($this->db->last_query());die;
        $dir = $lead_plan['creaditor_name'];
        if($lead_plan['ic_api'] == 1 && $lead_plan['api_label_id'] == 'traktion_supertoup') {
         $lead_id = $leadId;
            $api_proposal_res = $this->db->query("select policy_no,certificate_number,coi_url from api_proposal_response where lead_id = '$lead_id'")->row_array();

                         
                            $file = './assets/health_coi/' . $dir.'/coi_'.str_replace('/','_',$api_proposal_res['certificate_number']).'.pdf';
                         $url = FRONT_URL.'assets/health_coi/' . $dir.'/coi_'.str_replace('/','_',$api_proposal_res['certificate_number']).'.pdf';
                         
                       
                            //echo $file;
                        /*   if (file_exists($file)) {
                                header('Content-Description: File Transfer');
                                header('Content-Type: application/octet-stream');
                                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                                header('Expires: 0');
                                header('Cache-Control: must-revalidate');
                                header('Pragma: public');
                                header('Content-Length: ' . filesize($file));
                                readfile($file);
                                exit;
                            }*/
                            $res_url['url'] = $url;
echo json_encode($res_url);
                
        }else {
            $req_data['coi_frontend']=1;
            $coi_html1='';
            $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data), TRUE);
            
            if (isset($data['data']) && !empty($data['data'])) {
                $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                    'lead_id' => encrypt_decrypt_password($leadId),


                ]), TRUE);
                //print_r($checkDetails);exit;

                $data['insured_member'] = $checkDetails;
                if(count($data['data']) == count($data['data'], COUNT_RECURSIVE)){
                    $data['data']=[$data['data']];
                    $data['premium_details']=[$data['premium_details']];

                }

                $data['premium_details'] = array_combine(array_column($data['premium_details'],'master_policy_id'), $data['premium_details']);

                $is_single_coi = $data['data'][0]['is_single_coi'];
                $creditor_id =  $data['data'][0]['creditor_id'];
                $coi_html1=$this->db->query("select coi_html from coi_format_html where creditor_id=".$creditor_id." AND policy_type=1")->row()->coi_html;

                if(is_null($coi_html)){
                    $coi_html1=$this->db->query("select coi_html from coi_format_html where is_default=1 AND policy_type=1")->row()->coi_html;
                }
            }
            $file_to_save = $upload_dir = FCPATH.'assets'. DIRECTORY_SEPARATOR .'coipdf';
            $i=0;
            $files=[];
            foreach ($data['data'] as $key => $value_p) {
                $coi_html='';
                $i++;    
                $premium_details = $data['premium_details'][$value_p['policy_id']];
                
                extract($value_p);
                $coi_html = str_replace("{{productName}}", $creaditor_name . ' - ' . $plan_name, $coi_html1);
                $coi_html = str_replace("{{masterPolicyNumber}}", $policy_number, $coi_html);
                $coi_html = str_replace("{{premium_with_tax}}", $premium_details['premium_with_tax'], $coi_html);
                $coi_html = str_replace("{{net_premium}}", $premium_details['premium'], $coi_html);
                $coi_html = str_replace("{{tax_percent}}", round((($premium_details['premium_with_tax'] - $premium_details['premium']) / $premium_details['premium']) * 100) . "%", $coi_html);
                $coi_html = str_replace("{{gross_premium}}", $premium_details['premium_with_tax'], $coi_html);
                $coi_html = str_replace("{{certificateNumber}}", $certificate_number, $coi_html);
                $coi_html = str_replace("{{policyHolderName}}", $cust_details, $coi_html);
                $coi_html = str_replace("{{planName}}", $plan_name, $coi_html);
                $coi_html = str_replace("{{mobileNumber}}", $mobile_number, $coi_html);
                $coi_html = str_replace("{{startdate}}", "00:01 hrs " . date('d/m/Y', strtotime($start_date)), $coi_html);
                $coi_html = str_replace("{{enddate}}", "23:59 on " . date('d/m/Y', strtotime($end_date)), $coi_html);
                $coi_html = str_replace("{{addr_details}}", $cust_details . ' - ' . $addr_details, $coi_html);
                $table = '';
                $insured_member=$data['insured_member'];



                if (count($insured_member) > 0) {
                    if($is_single_coi !=1){

                        $members=array_combine(array_column($insured_member, 'policy_sub_type_name'), $insured_member);
                        $insured_member = [$members[$value_p['policy_sub_type_name']]];
                        
                    }
                    foreach ($insured_member as $key_m => $value_m) {

                        $table .= ' <p class="header_title">' . $value_m['policy_sub_type_name'] . '
                        </p><table>
                        <tr>
                            <th>Insured Person</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Nominee</th>
                            <th>Relationship</th>
                            <th>Sum Insured</th>
                           
                        </tr>';
                        foreach($value_m['member'] as $value) {
                            $str = "<tr><td>". $value['policy_member_first_name']." ".$value['policy_member_last_name']."</td>";
                            $str .=          "<td>".$value['policy_member_dob']."</td>";
                            $str .=          "<td>".$value['policy_member_gender']."</td>";
                            $str .=          "<td>".$nominee_first_name." ".$nominee_last_name."</td>";
                            $str .=          "<td>".$value['member_type']."</td>";
                            $str .=          "<td>".$value['cover']."</td>";
                            $str .=          "</tr>";
                            $table .=$str;
                        }
                        $table .='</table>';
                    }
                }

                $coi_html = str_replace("{{insuredTable}}", $table, $coi_html);
                $dompdf = new Dompdf();
                $dompdf->loadHtml($coi_html);
                $dompdf->setPaper('A4');
                $dompdf->render();
                if($is_single_coi ==1){
                    $dompdf->stream("", array("Attachment" => false));
                }else{
                    $files[]=$file_to_save.DIRECTORY_SEPARATOR."file".$value_p['pr_api_id'].".pdf";
                    file_put_contents($file_to_save.DIRECTORY_SEPARATOR."file".$value_p['pr_api_id'].".pdf", $dompdf->output());
                }
                //
                 
            }
            if(!empty($files)){
                $this->load->library('zip');
                foreach ($files as $row)
                {
                    $this->zip->read_file($row);
                }

                $this->zip->download('coi.zip');
                
            }
            
            
            
       /*
         echo "<script type='text/javascript'>";
         for($i=0;$i<2; $i++) {
              echo "window.open('/assets/coipdf/file{$i}.pdf');" ;
         }
         echo "</script>";*/
        
        }


    }
    public function coidownloadAPI(){



        $leadId= $_GET['lead_id'];
        $req_data['lead_id'] = $leadId;

        //print_r($req_data['lead_id']);exit;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data),TRUE);
         // print_r($data);exit;
        if(isset($data['data']) && !empty($data['data'])){

            $data = $data['data'];

            $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                'lead_id' => encrypt_decrypt_password($_GET['lead_id']),


            ]),TRUE);
           // echo $this->db->last_query();
          //  print_r($checkDetails);exit;

            $data['insured_member'] = $checkDetails;

        }

        $html = $this
            ->load
            ->view("quotes/coi_pdf", $data, true);
      // echo $html;exit;
        define("DOMPDF_ENABLE_REMOTE", false);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream("",array("Attachment" => false));
        echo $html;

    }

    public function fetch_additional_plans(){
        $lead_id = $this->session->userdata('lead_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = $this->session->userdata('customer_id');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $req_data['lead_id'] = $lead_id;
        $req_data['customer_id'] = $customer_id;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/fetch_additional_plans', $req_data),TRUE);
        if(!empty($data)){
            $html = '<hr><p class="addon_cover_btn_proposal_form">Additional Plans</p><br>';
            foreach($data as $key => $val){
                $html .= '<li class=""><a href="#" style="margin-top: -13px;"> <img class="contain img_right_panel_addon_add" src="'.$val['creditor_logo'].'">'.$val['creaditor_name'].' - '.$val['plan_name'].' ('.$val['policy_sub_type_name'].')</a></li><br>';
            }
            echo json_encode(["status"=>"Success", "additional_plans" => $html]);
        }else{
            echo json_encode(["status"=>"fail"]);
        }

    }

    public function test(){
        $getSuminsuredType = json_decode(curlFunction(SERVICE_URL . '/customer_api/saveApiProposalResponse/618', []),TRUE);
        echo "<pre>";print_r($getSuminsuredType);exit;
    }
    function PolicyIssueApiNew(){
 $jsonString = $this->input->post('json_cust_data');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://fyntunecreditoruatapi.benefitz.in/api2/saveProposalapi',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$jsonString,
            CURLOPT_HTTPHEADER => array(

                'Cookie: ci_session=4mcoaiqgln78o6g6todrqj2780970dbp'
            ),
        ));

       // $response = curl_exec($curl);
     //   curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

        $response=(curl_exec($curl));
       $resenc=json_encode($response,true);
         $res=json_decode($resenc,true);
        $res1=json_decode($res,true);

        curl_close($curl);
        if($res1['success'] == 'true')
        {
                        redirect('/quotes/success_view/'.$res1['LeadId']);

        }
        else{
            echo $res1;
        }

    }
    function allRiskredirectionData(){
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        $checksum = 0;
        $jsonData = '';
        $encryptedJSONData = '';
//var_dump($this->input->post('generate_redirection_checksum'));
        if ($this->input->post('generate_redirection_checksum') !== null) {
            $jsonString = $this->input->post('json_cust_data');
            $jsonString = trim($jsonString, "\0");
            $aJSONData = json_decode($jsonString, true);
            $token=$aJSONData['token'];
            $ClientCreation=$aJSONData['ClientCreation'];
            $QuoteRequest=$aJSONData['QuoteRequest'];
            $SumInsuredData=$aJSONData['QuoteRequest']['SumInsuredData'];
            $MemObj=$aJSONData['MemObj']['Member'];
            $ReceiptCreation=$aJSONData['ReceiptCreation'];
            $Nominee_Detail=$aJSONData['Nominee_Detail'];
            $PolicyCreationRequest=$aJSONData['PolicyCreationRequest'];

            //$aJSONData = array_map('trim', $aJSONData); //to trim the array element
            $plainText = '';
            foreach ($aJSONData as $key => $value) {
                if(is_array($value)){
                    foreach ($value as $k1=>$val){
                        $plainText .= $key . "=" . $value . "|";
                    }
                }else{
                    $plainText .= $key . "=" . $value . "|";
                }

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

        $this->load->view("quotes/allrisk_redirection", compact('checksum', 'jsonData', 'encryptedJSONData'));
    }
    function redirectionData(){
       // ini_set('display_errors', 1);
       // ini_set('display_startup_errors', 1);
       // error_reporting(E_ALL);
        $checksum = 0;
        $jsonData = '';
        $encryptedJSONData = '';
//var_dump($this->input->post('generate_redirection_checksum'));
        if ($this->input->post('generate_redirection_checksum') !== null) {
            $jsonString = $this->input->post('json_cust_data');
            $jsonString = trim($jsonString, "\0");
            $aJSONData = json_decode($jsonString, true);
            $token=$aJSONData['token'];
            $ClientCreation=$aJSONData['ClientCreation'];
            $QuoteRequest=$aJSONData['QuoteRequest'];
            $SumInsuredData=$aJSONData['QuoteRequest']['SumInsuredData'];
            $MemObj=$aJSONData['MemObj']['Member'];
            $ReceiptCreation=$aJSONData['ReceiptCreation'];
            $Nominee_Detail=$aJSONData['Nominee_Detail'];
            $PolicyCreationRequest=$aJSONData['PolicyCreationRequest'];

            //$aJSONData = array_map('trim', $aJSONData); //to trim the array element
            $plainText = '';
            foreach ($aJSONData as $key => $value) {
               if(is_array($value)){
                   foreach ($value as $k1=>$val){
                       $plainText .= $key . "=" . $value . "|";
                   }
               }else{
                   $plainText .= $key . "=" . $value . "|";
               }

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

        $this->load->view("quotes/redirection_data", compact('checksum', 'jsonData', 'encryptedJSONData'));
    }
    function generateToken(){
        $json_data=$this->input->post('json_data');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'betaaffinityapi.elephant.in/api/generateToken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$json_data,
            CURLOPT_HTTPHEADER => array(

                'Cookie: ci_session=4mcoaiqgln78o6g6todrqj2780970dbp'
            ),
        ));

        // $response = curl_exec($curl);
        //   curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

        $response=(curl_exec($curl));
        $resenc=json_encode($response,true);
        $res=json_decode($resenc,true);
        $res1=json_decode($res,true);
        curl_close($curl);
      //  echo $res1;
      $get_json_decode = json_decode($json_data,true);
      if($get_json_decode['type'] == 3) {
          $this->load->view("quotes/allrisk_redirection", compact('res1'));

      }
      else {
          $this->load->view("quotes/redirection_data", compact('res1'));
      }
    }
    function checkCDbalanceThreshold(){
        $plan_id=$this->input->post('plan_id');
        $premium=$this->input->post('premium');
        $creditor_id=$this->db->query('select creditor_id from master_plan where plan_id='.$plan_id)->row()->creditor_id;
        $CheckCDThreshold = CheckCDThreshold($creditor_id,$plan_id,$premium);
        if($CheckCDThreshold['status'] == 201){
            $response['status']=201;
            $response['msg']="Low CD Balance.Can't Proceed further!";
        }else{
            $response['status']=201;
            $response['msg']="true";
        }echo json_encode($response);
    }

    public function termsAndConditionText(){
        $tc_text='Accept Terms and conditions';
        if($this->session->userdata('partner_id_session') && $this->session->userdata('product_id_session')){
            $creditor_id = encrypt_decrypt_password($this->session->userdata('partner_id_session'),"D");
            $plan_id = encrypt_decrypt_password($this->session->userdata('product_id_session'),"D");
            $Q_tc_text=$this->db->query("select tc_text from link_ui_configuaration where plan_id=". $plan_id);
            if($this->db->affected_rows() > 0){
                $tc_text=$Q_tc_text->row()->tc_text;
            }else{
                $tc_text='Accept Terms and conditions';
            }
        }
        else{

            $creditor_id = encrypt_decrypt_password($this->session->userdata('partner_id_session'),"D");
            $Q_tc_text=$this->db->query("select tc_text from link_ui_configuaration where creditor_id=".$creditor_id);
            if($this->db->affected_rows() > 0){
                $tc_text=$Q_tc_text->row()->tc_text;
            }else{
                $tc_text='Accept Terms and conditions';
            }

        }
        return $tc_text;
    }
}