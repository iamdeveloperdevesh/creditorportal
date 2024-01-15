<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//require($_SERVER['DOCUMENT_ROOT'] . '/../vendor/razorpay/razorpay/Razorpay.php');

require(APPPATH.'libraries/razorpay-php/Razorpay.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
require_once'/var/www/html/fyntune-creditor-portal/vendor/autoload.php';

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
        $this->keyId = 'rzp_test_HxRHmUmojTTNs4';
        $this->keySecret = 'JEkpsKDaDPZ9RNoBpomvm2ib';
        $this->displayCurrency = 'INR';

        //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    }
    public function generate_quote_abc()
    {
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
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        // echo SERVICE_URL . '/customer_api/getCustomerDetails';exit;
        $data['post_data'] = $req_data;
//print_r($data['post_data']);die;
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/generateQuoteabc', $req_data);
        // print_r($checkDetails);
        //   exit;
        $data['quote_info'] = json_decode($checkDetails,true);
        //  print_r($data);die;
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
        //   var_dump($req_data);exit;
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/Create_quote_self', $req_data);
        //   print_r($checkDetails);die;
        $data['status'] = 'success';
        $data['data'] = $checkDetails;

        echo json_encode($data);
    }
    public function create_member_single_link()
    {
        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');

        $req_data['policy_data'] = $this->input->post('policy_details');

        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getMemebrDetails_single', $req_data);
        // print_r($checkDetails);die;
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
            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getMember_insure', [

                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'partner_id' =>$partner_id

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
            print_r($checkDetails);die;

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
            //	var_dump($proposal_data);exit;
            echo json_encode($proposal_data);

        }

    }
    //ankita
    public function generate_proposal(){

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
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

        // echo SERVICE_URL . '/customer_api/getCustomerDetails';exit;
        $get_summary_details = curlFunction(SERVICE_URL . '/customer_api/get_summary_details', $req_data);
//        print_r($get_summary_details);exit;
        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        $nominee_relation = curlFunction(SERVICE_URL . '/customer_api/getNomineeRelation', $req_data);
        $nominee_relation = json_decode($nominee_relation, TRUE);

        $data['customer_details'] = json_decode($get_customer_details, TRUE);
        $data['get_summary_details'] = json_decode($get_summary_details, TRUE);
        $data['post_data'] = $req_data;
        $data['nominee_relations'] = $nominee_relation['data'];

        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            redirect('/Customerportal/');
        }

        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/proposal_form',$data);
        $this->load->view('template/customer_portal_footer.php');
    }
    public function index()
    {

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
//        echo $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');die;
        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            // redirect('/Customerportal/');
        }

        if($this->session->userdata('partner_id_session')){
            $req_data['partner_id'] = $this->session->userdata('partner_id_session');
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
//       echo "<pre>";print_r($response);exit;

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
        // print_r($getQuotePageData['policy_data'][3]);die;
        $statusPolicy=false;
        foreach($getQuotePageData['policy_data'] as $key=>$item)
        {
            $i = 0;



//              var_dump($item);
            if($item['si_premium_basis_id'] == 5){
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
            }else{

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
            }


        }
//var_dump($sum_insured_arr);die;
        //      exit;
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

    public function indexOLD()
    {
//echo 123;die;

        $req_data = [];
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
//        echo $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');die;
        if($this->session->userdata('lead_id') && $this->session->userdata('customer_id') && $this->session->userdata('trace_id')){

        }else{
            // redirect('/Customerportal/');
        }

        if($this->session->userdata('partner_id_session')){
            $req_data['partner_id'] = $this->session->userdata('partner_id_session');
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
        // echo "<pre>";print_r($response);exit;
//       echo "<pre>";print_r($response);exit;

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
        //    print_r($getQuotePageData);exit;
        $group_by_policies = array();
        $policy_ids = array();
        $sum_insured_arr = array();
        $premium_arr = array();
        $counter = 0;
        //    echo '<pre>';
//        print_r($getQuotePageData['policy_data']);die;
        $statusPolicy=false;
        foreach($getQuotePageData['policy_data'] as $key=>$item)
        {
            $i = 0;



//              var_dump($item);
            if($item['suminsured'] != '0' && $item['suminsured'] != '' )
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


            // $sum_insured_arr[$item['policy_id']] = $item['sum_insured'];
            //  print_r($item['sum_insured']);
            //  echo '<pre>';
            //----pooja code
//            $premium=   $this->input->post('premium');
//            if($premium && $item['premium_rate'] !=0){
//
//                if(strpos($premium, '-') !== false){
//                    $premium_arr1 = explode('-', $premium);
//                    if( $item['premium_rate'] >= $premium_arr1[0] && $item['premium_rate'] <= $premium_arr1[1]){
//                        //echo $item['premium_rate'];
//                        array_push($premium_arr[$item['policy_id']], $item['premium_rate']);
//                    }
//                }else if($premium == 3000){
//                    if( $item['premium_rate'] <= $premium){
//                        array_push($premium_arr[$item['policy_id']], $item['premium_rate']);
//                    }
//                }else{
//                    array_push($premium_arr[$item['policy_id']], $item['premium_rate']);
//                }
//            }else{
//                array_push($premium_arr[$item['policy_id']], $item['premium_rate']);
//            }
            //------pooja code end

        }

//       exit;
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
//var_dump($response);die;
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
//         print_R($checkDetails);die;
        $get_summary_details = curlFunction(SERVICE_URL . '/customer_api/get_summary_details', $req_data);
        /*var_dump($get_summary_details);
        exit;*/
        $premium_details = curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data);
        $premium_details = json_decode($premium_details, TRUE);
        //var_dump($premium_details);
//        exit;

        if($is_normal_customer){
            if(array_key_exists('policy_data',$premium_details)){
                $data['premium_details'] = $premium_details['policy_data'] ;
            }else{
                $data['premium_details'] = array('') ;
            }

        }else{
            $data['premium_details'] =  $req_data ;

        }
        //echo '<pre>';
        //print_r($data);
        //  exit;
        //get policy SI type details
        if($this->session->userdata('is_normal_customer')){
            $policyId=encrypt_decrypt_password($this->session->userdata('policy_id'), 'D');
            $SItype=$this->db->query('select suminsured_type_id from master_policy_si_type_mapping where master_policy_id='.$policyId.' AND isactive')->row()->suminsured_type_id;
        }else{
            $SItype=$this->session->userdata('si_type_id');
        }




//         echo "<PRE>";print_r($premium_details);exit;
        $data['get_summary_details'] = json_decode($get_summary_details, TRUE);
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
        // print_r($req_data);exit;
        $premium_details = curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data);
        $premium_details = json_decode($premium_details, TRUE);
        print_r($premium_details);exit;
        echo json_encode($premium_details['policy_data']);exit;
    }

    public function redirect_to_pg_bk(){
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        //var_dump($req_data);exit;
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

    public function success_view($lead_id){
        // echo $lead_id;exit;
        session_destroy();
        //echo "<PRE>";print_r($_REQUEST);
        $success = true;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        // echo $lead_id;exit;

        $error = "Payment Failed";
        $data = [];
        /*if (empty($_POST['razorpay_payment_id']) === false)
        {

            $api = new Api($this->keyId, $this->keySecret);

            try
            {
                // Please note that the razorpay order ID must
                // come from a trusted source (session here, but
                // could be database or something else)
                $attributes = array(
                    'razorpay_order_id' => (array_key_exists('razorpay_order_id',$_SESSION)) ? ($_SESSION['razorpay_order_id']) : (''),
                    'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                    'razorpay_signature' => $_POST['razorpay_signature']
                );

                $api->utility->verifyPaymentSignature($attributes);
            }
            catch(SignatureVerificationError $e)
            {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }*/
        if(isset($_POST['razorpay_payment_id'])){
            // var_dump($success);exit;
            if ($success === true)
            {

                $req_data['pg_response'] = $_REQUEST;
                $req_data['lead_id'] = $lead_id;
//                print_r($req_data);exit;
                $cond = "";
                $update_payment_status = json_decode(curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data),TRUE);

                if($update_payment_status){
                    $req_data1['lead_id'] = $lead_id;
                    $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1),TRUE);
                    $coi_no = $res["data"]["certificate_number"];
                    $cond = '<p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">'.$coi_no.'</span>
                        </p>';
                }
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> '.$lead_id.'</span>
                        </p>
                        <p>Payment ID:
                            <span> '.$_POST["razorpay_payment_id"].'</span>
                        </p>
                        '.$cond.'
                    </div>';
            }
            else
            {
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment failed</p>
                <p>Lead ID:<span id="lead_view"> '.$lead_id.'</span></p>
                         <p> <span>'.$error.'</span></p>';
            }
        }else{
            //echo "in";exit;

            $req_data['lead_id'] = $lead_id;
            $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data),TRUE);
            if($res){
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> '.$lead_id.'</span>
                        </p>
                        <p>Payment ID:
                            <span>'. $res["data"]["transaction_number"].'</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">'.$res["data"]["certificate_number"].'</span>
                        </p>
                    </div>';

            }
        }


        $this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/thank_you',$data);
        $this->load->view('template/customer_portal_footer.php');
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
            redirect('/quotes/success_view/'.$this->session->userdata('lead_id'));
        }
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        if($this->session->userdata('is_normal_customer')){
            $req_data['policy_id'] = $this->session->userdata('policy_id');
        }else{
            $req_data['policy_id'] = encrypt_decrypt_password($this->session->userdata('policy_id'),'E');
        }

        //echo encrypt_decrypt_password($req_data['lead_id'], 'D');

        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        $get_customer_details = json_decode($get_customer_details, TRUE);
        $proposal_details = curlFunction(SERVICE_URL . '/customer_api/getProposalDetails', $req_data);
        $proposal_details = json_decode($proposal_details, TRUE);
        //var_dump($proposal_details['proposal_details']);exit;
        //    $premiumAmount = $proposal_details['proposal_details']['premium_amount'];
        $premiumAmount=0;
        foreach ($proposal_details['proposal_details'] as $prem){
            $premiumAmount += $prem['premium_amount'];
        }
        //exit;
        $premium_details = json_decode(curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data),TRUE);
        $leadId = $get_customer_details['customer_details']['lead_id'];
        //echo $leadId;exit;
        $customer_name = $get_customer_details['customer_details']['full_name'];
        $mobileNumber = $get_customer_details['customer_details']['mobile_no'];
        $email = $get_customer_details['customer_details']['email_id'];
        $address = trim($get_customer_details['customer_details']['address_line1'].' '.$get_customer_details['customer_details']['address_line2'].' '.$get_customer_details['customer_details']['address_line3']);
        $PaymentMode = "PO";
        $ProductInfo = $premium_details['policy_data']['creaditor_name'].' - '.$premium_details['policy_data']['plan_name'];
        $Source = 'ABC';
        $Vertical = 'ABCGRP';
        $ReturnURL = base_url("/quotes/success_view/".$leadId);
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
        $data['display_amount']    = $displayAmount;
        $data['customer_id']    = $req_data['customer_id'];
        $data['lead_id']    = encrypt_decrypt_password($leadId);

        $res['data'] = $data;

        //$json = json_encode($data);
        //$this->load->view('template/customer_portal_header.php');
        $this->load->view('quotes/pg_submit',$res);
        //$this->load->view('template/customer_portal_footer.php');
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


//echo 123;die;
        $leadId= $_GET['lead_id'];
        $req_data['lead_id'] = $leadId;
        {
            if(empty($leadId))

                $req_data['lead_id'] = $_POST['lead_id'];
        }
        //print_r($req_data['lead_id']);exit;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data),TRUE);
//var_dump($data);exit;
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
        //    var_dump($checkDetails);exit;
        $html = $this
            ->load
            ->view("quotes/coi_pdf", $data, true);
        echo $html;

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
    function PolicyIssueApiNewOLD(){
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
            CURLOPT_POSTFIELDS =>'{
    "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjQ0IiwiaWF0IjoxNjY4NTk5MTkyLCJleHAiOjE2Njg2MTcxOTJ9.tWXTLpEzInTyKO8meVUjY6dVS3v4ZKu3q9KXAAEpD4s",
  "ClientCreation": {
    "partner": "Fyntune Insurance",
	"plan": "Accident Cover",
	"salutation": "Mrs",
	"first_name": "sonal",
	"middle_name": "Abhijit",
	"last_name": "Wagh",
	"gender": "Female",
	"dob": "03-11-2004",
	"email_id": "sms@gamil.com",
	"mobile_number": "89873986726",
	"tenure": "1",
	"is_coapplicant": "No",
	"coapplicant_no": "",
    "userId":"44",
	"sm_location": "Mumbai",
    "alternateMobileNo": null,
    "homeAddressLine1": "Om hrad ntalatiof, holOmshre, talathioficeeachol, OmshreeSada9talathi",
    "homeAddressLine2": null,
    "homeAddressLine3": null
   
  },
  "QuoteRequest": {
    
    "adult_count": "1",
    "child_count": "0",
    "SumInsuredData":
   [{"PlanCode":"4211","SumInsured":"500000","Shortcode":"GHI"},
    {"PlanCode":"4212","SumInsured":"500000","Shortcode":"GPA"},
     {"PlanCode":"4213","SumInsured":"500000","Shortcode":"GCI"}
    ]
    
      },
  "MemObj": {
    "Member": [
      {
        "MemberNo": 1,
        "Salutation": "Mr",
        "First_Name": "praghghkash",
        "Middle_Name": null,
        "Last_Name": "k abhi axis",
        "Gender": "M",
        "DateOfBirth": "1991-06-15",
        "Relation_Code": "1"
      }
    ]
  },
  "ReceiptCreation": {
    "modeOfEntry": "Direct",
    "PaymentMode": "1",
    "bankName": "Axis Bank Limited",
    "branchName": "",
    "bankLocation": null,
    "chequeType": null,
    "ifscCode": null
  
  },
  "Nominee_Detail":{
      "Nominee_First_Name": "gfgh",
        "Nominee_Last_Name": "gfg",
        "Nominee_Contact_Number": "8793164535",
        "Nominee_Home_Address": null,
        "Nominee_gender": "M",
        "Nominee_Salutation": "Mr",
        "Nominee_Email": "pooja@gmail.com",
        "Nominee_Relationship_Code": "R002"
  },
 "PolicyCreationRequest": {
 
    "TransactionNumber": "Pay_kbToSSUXXtt",
    "TransactionRcvdDate": "2011-11-10",
    "PaymentMode": "cheque"
  
  }

}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: ci_session=4mcoaiqgln78o6g6todrqj2780970dbp'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
    function PolicyIssueApiNew(){
        $jsonString = $this->input->post('json_cust_data');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://betaaffinityapi.elephant.in/api2/saveProposalapi',
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
            CURLOPT_URL => 'https://betaaffinityapi.elephant.in/api/generateToken',
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
        $this->load->view("quotes/redirection_data", compact('res1'));
    }
}