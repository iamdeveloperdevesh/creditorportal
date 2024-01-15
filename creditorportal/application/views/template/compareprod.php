<?php

header('Access-Control-Allow-Origin: *');
if (!defined('BASEPATH')) exit('No direct script access allowed');
require(APPPATH . 'libraries/razorpay-php/Razorpay.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once PATH_VENDOR.'vendor/autoload.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/SMTP.php';
use Dompdf\Dompdf;
require(APPPATH.'libraries/lib/ConvertApi/autoload.php');
use \ConvertApi\ConvertApi;
use phpseclib3\Net\SFTP;
// session_start(); //we need to call PHP's session object to access it through CI
class GadgetInsurance extends CI_Controller
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
        parent::__construct();
        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
        /*  $this->keyId = 'rzp_test_HxRHmUmojTTNs4';
          $this->keySecret = 'JEkpsKDaDPZ9RNoBpomvm2ib';*/
        $this->keyId = 'rzp_live_sAyrbVkYrSAw3k';
        $this->keySecret = '0hHFxCdrWHrTIM2G9UvLBzdK';
        $this->displayCurrency = 'INR';
    }

    public function index()
    {
        $partner = $_GET['partner'];
        if(!empty($partner)){
            $cred_id = encrypt_decrypt_password($partner, 'D');
            $creditor_logo=$this->db->query("select creditor_logo from master_ceditors where creditor_id=".$cred_id)->row()->creditor_logo;
            $this->session->set_userdata('creditor_logo', $creditor_logo);
        }
        $this->session->set_userdata('partner_id', $partner);
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/index');
    }

    public function ViewMarineInsurance()
    {
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/viewMarineInsurance');
    }
    public function wordtonumber($text)
    {
        // $str = $text;

        $str = strtolower($text);
        preg_match('/(?<=\sand )\S.*+/i', $str, $match);




        $numbers = array(
            'zero' => 0,
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4,
            'five' => 5,
            'six' => 6,
            'seven' => 7,
            'eight' => 8,
            'nine' => 9,
            'ten' => 10,
            'eleven' => 11,
            'twelve' => 12,
            'thirteen' => 13,
            'fourteen' => 14,
            'fifteen' => 15,
            'sixteen' => 16,
            'seventeen' => 17,
            'eighteen' => 18,
            'nineteen' => 19,
            'twenty' => 20,
            'thirty' => 30,
            'forty' => 40,
            'fourty' => 40, // common misspelling
            'fifty' => 50,
            'sixty' => 60,
            'seventy' => 70,
            'eighty' => 80,
            'ninety' => 90,
            'hundred' => 100,
            'thousand' => 1000,
            'lakh' => 100000,
            'lakhs' => 100000,
            'crore' => 10000000,
            'crores' => 10000000,
            'million' => 1000000,
            'billion' => 1000000000);

//first we remove all unwanted characters... and keep the text
        $str = preg_replace("/[^a-zA-Z]+/", " ", $str);
        list($a, $b) = explode(' and ', $str);


//now we explode them word by word... and loop through them
        $words = explode(" ", $a);
        // print_r($words);
//i devide each thousands in groups then add them at the end
//For example 2,640,234 "two million six hundred and fourty thousand two hundred and thirty four"
//is defined into 2,000,000 + 640,000 + 234

//the $total will be the variable were we will add up to
        $total = 1;
        $point=0;

//flag to force the next operation to be an addition
        $force_addition = false;

//hold the last digit we added/multiplied
        $last_digit = null;

//the final_sum will be the array that will hold every portion "2000000,640000,234" which we will sum at the end to get the result
        $final_sum = array();

        foreach ($words as $key=>$word) {
//print_r($word);
            //if its not an and or a valid digit we skip this turn
            if (!isset($numbers[$word]) && $word != "and") {
                continue;
            }

            //all small letter to ease the comparaison
            $word = strtolower($word);
//print_r($word);


            //if it's an and .. and this is the first digit in the group we set the total = 0
            //and force the next operation to be an addition
            if ($word == "and") {
                //	print_r($word);
                if ($last_digit === null) {
                    $total = 0;
                }
                $force_addition = true;
            } else {

                //	print_r($numbers);
                //if its a digit and the force addition flag is on we sum
                if ($force_addition) {
                    //	$point += $numbers[$word];
                    //	print_r($word);
                    $total += $numbers[$word];
                    $force_addition = false;
                } else {


                    //if the last digit is bigger than the current digit we sum else we multiply
                    //example twenty one => 20+1,  twenty hundred 20 * 100
                    if ($last_digit !== null && $last_digit > $numbers[$word]) {
                        //	echo 24;
                        $total += $numbers[$word];
                    } else {
                        //	echo 23;
                        $total *= $numbers[$word];
                    }
                }
                $last_digit = $numbers[$word];

                //finally we distinguish a group by the word thousand, million, billion  >= 1000 !
                //we add the current total to the $final_sum array clear it and clear all other flags...
                if ($numbers[$word] >= 1000) {
                    $final_sum[] = $total;
                    $last_digit = null;
                    $force_addition = false;
                    $total = 1;
                }
            }



        }

        if(!empty($match))
        {
            //	print_r($match);
            $decimal = explode(" ",$match[0]);
            //	print_r($decimal);


            foreach($decimal as $value){
                if($value != 'paise' &&  $value !='only')
                {
                    $point += $numbers[$value];

                }

            }
            //	print_r($point);
            $total = $total.".".$point;
        }

// there is your final answer !
        $final_sum[] = $total;
        return array_sum($final_sum);
    }
    public function findTotal ($text_det){
        //   print_r(strtolower($text_det));
        $text_de = strtolower($text_det);
        //  echo 123;die;
        $data =  str_replace(array("\r","\n"),' ',$text_de);
        $text = preg_replace('!\s+!', ' ', $data);
        //$text = explode("\r\n", $data);
        // print_r($text);
        $text =  preg_replace('/\s/', ' ',$text);
        $total = 0;
        $pattern1 = '/(?<=words\s[^0-9]\s).*paise|(?<=words[^0-9]\s).*only+/i';
        if(preg_match_all($pattern1, $text, $matches1))
        {//echo 1;die;
            $total = $matches1[0];
//print_r($total);
            if (!is_numeric($total[0])) {
                $total = $this->wordtonumber($total[0]);
                if (is_array($total)) {
                    //  echo 123;die;
                    foreach ($total as $val) {
                        $total_value = $val;
                    }
                    return $total_value;
                    //echo "\n";
                } else {
                    // echo 123;die;
                    if(is_numeric($total))
                    {
                        return $total;
                    }else{
                        $total = explode(" ", $total[0]);
                        $total = $total[1];
                        return $total;
                    }

                    //echo "\n";

                }

            }

        }else {

            $pattern = '/total\s\d{1,9}|total\s\d{1,9}\.\d{1,9}|(?<=words[^0-9]\s).*only|total\s?[^0-9]\s[A-Za-z0-9]\w+\s\d{1,9}\.\d{1,9}|(?<=total\s[^0-9]).\d{1,9}.\d{1,9}|(?<=total\s[A-Za-z0-9]\w\s)\d{1,9}[^0-9]\d{2}|(?<=grand total[^0-9]\s)\d{1,9}.\d{1,9}[^0-9]\d{2}+/i';
            preg_match_all($pattern, $text, $matches);
            //print_r($matches[0]);

            if (count($matches[0]) >= 1) {
                //   echo "inside 1st";
                $total = $matches[0];
                if (is_array($total)) {
                    foreach ($total as $val) {
                        // print_r($val);
                        if(strpos($val, 'total') !== false) {
                            //echo 1;
                            $total = explode(" ", $val);
                            $total_value = end($total);
//print_r($total);
                        }else {
                            $total_value = $val;
                        }
                    }
                    return $total_value;
                    //echo "\n";
                } else {
                    $total = explode(" ", $total[0]);
                    //  print_r($total[1]);
                    $total = $total[1];
                    return $total;
                    //echo "\n";

                }

            } else {

                $pattern = '/Amount [A-Za-z0-9]+/i';
                if (preg_match_all($pattern, $text, $matches)) {
                    $total = $matches[0];
                    $total = explode(" ", $total[0]);
                    $total = $total[1];
                    if (is_numeric($total)) {
                        return $total;
                        //echo "\n";
                    }
                }


            }
        }



    }

    function findDate($text_det) {
        $text_de = strtolower($text_det);
        //echo 123;die;
        $data =  str_replace(array("\r","\n"),' ',$text_de);
        $text = preg_replace('!\s+!', ' ', $data);
        //print_r($text);
        // create a regular expression pattern to match the keyword and the value

        $dateP =  '/date[^0-9]\s[0-9]+\/*[0-9]+\/[0-9]\d{1,9}|date\s[0-9]+\/?-*[0-9]+\/?-*[0-9]\d{1,9}|date\s?[^0-9]?\s?[0-9]+\/?.[0-9]+\/?.[0-9]\d{1,9}|date\s?[^0-9]\s\d{2}[-\.\/][a-z]\w+[-\.\/]\d{4}|(?<=date[^0-9]\s)\d{2}\s[a-z]\w+\s\d{4}+/i';
        // use preg_match_all() function to find all matches
        preg_match_all($dateP, $text, $matches);
        //print_r($matches);die;
        // $matches[0] contains an array of all matches, so you can loop through them to extract the value
        $date = '';
        if (count($matches[0]) >= 1 ) {
//echo 123;die;
            foreach ($matches[0] as $match) {
                if(strpos($match, 'date') !== false) {
                    // echo 1;
                    //$value = preg_replace('/Date \s*/', '', $match);
                    //var_dump(explode(" ",$match));
                    $dat = explode(" ", $match);
                    //print_r($dat[0]);
                    $date = str_replace(array('\'', '"', ':', ';', '<', '>'), ' ', $dat[1]);
                    // $date = $dat[1];
                }
                else
                {
                    $date = str_replace(array('\'', '"', ':', ';', '<', '>'), ' ', $match);

                }
            }
            //  print_r($date);
            return $date;

        }


    }
    public function uploadInvoice()
    {
        extract($_POST);
        $arr = [];
        //print_r($_FILES["imageFile"]);die;
        $target_file_name= $_FILES["imageFile"]["name"];
        //$excel_path = APPPATH."third_party/SI_PROCESS_UPLOAD/". $excel_file_name;
        $target_file = APPPATH."third_party/SI_PROCESS_UPLOAD/". $target_file_name;
        $ext = pathinfo($target_file_name, PATHINFO_EXTENSION);

        if (!is_dir(APPPATH."third_party/SI_PROCESS_UPLOAD/")) {
            mkdir(APPPATH."third_party/SI_PROCESS_UPLOAD/");
        }
        // chmod(APPPATH.'third_party',777);
        if (move_uploaded_file($_FILES["imageFile"]["tmp_name"],$target_file)) {
            //$this->uploadToApi($excel_path);
            # set your api secret
            if ($ext == 'pdf') {
                ConvertApi::setApiSecret('baEI7k3cjcKl1re6');

# Example of saving Word docx to PDF and to PNG
# https://www.convertapi.com/docx-to-pdf
# https://www.convertapi.com/docx-to-png

                $dir = sys_get_temp_dir();

# Use upload IO wrapper to upload file only once to the API
                $upload = new \ConvertApi\FileUpload($target_file);

                $result = ConvertApi::convert('txt', ['File' => $upload]);
                $contents = $result->getFile()->getContents();
//print_r($savedFiles);die;
                //echo "The PDF saved to:\n";
                // print_r($contents);
                $total = $this->findTotal($contents);
                $date = $this->findDate($contents);
                if ($total != '') {
                    $arr['total'] = $total;

                }
                if ($date != '') {
                    if (strpos($date, '/') !== false || strpos($date, '.') !== false) {
                        $newDate = date("Y-m-d", strtotime(str_replace('/', '-', $date)));

                    } else {
                        $newDate = date("Y-m-d", strtotime($date));

                    }
                    $arr['daterange'] = $newDate;

                }
                // $newDate = date("Y-m-d", strtotime(str_replace('/','-',$date)));
                $arr['msg'] = 'success';
                echo json_encode($arr);

                die;
            } else {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.ocr.space/parse/image',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('file' => new CURLFILE($target_file), 'isTable' => 'true', 'OCREngine' => 5),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: multipart/form-data',
                        'apikey: helloworld'
                    ),
                ));

                $responses = curl_exec($curl);

                curl_close($curl);
                $response = json_decode($responses, true);
                print_r($response);
                foreach ($response['ParsedResults'] as $pareValue) {
                    //echo $pareValue['ParsedText'];
                    $total = $this->findTotal($pareValue['ParsedText']);
                    $date = $this->findDate($pareValue['ParsedText']);

                    if ($total != '') {
                        $arr['total'] = $total;

                    }
                    if ($date != '') {
                        if (strpos($date, '/') !== false || strpos($date, '.') !== false) {
                            $newDate = date("Y-m-d", strtotime(str_replace('/', '-', $date)));

                        } else {
                            $newDate = date("Y-m-d", strtotime($date));

                        }
                        $arr['daterange'] = $newDate;

                    }
                    // $newDate = date("Y-m-d", strtotime(str_replace('/','-',$date)));
                    $arr['msg'] = 'success';
                    //print_r($arr);
                }
            }
        }

        echo json_encode($arr);
    }
    public function compare()
    {
        //   print_r($_POST);die;
        $data['plan']['plan_id'] = $_POST['plan_id_compare'];
        $data['plan']['cover'] = $_POST['cover_compare'];
        $data['plan']['premium'] = $_POST['premium_compare'];
        $data['plan']['plan_name'] = $_POST['plan_compare'];
        $data['plan']['tenure'] = $_POST['tenure_compare'];
        $data['plan']['policy_id'] = $_POST['policy_id_compare'];
        //print_r($data);
        $exp = explode(",", $data['plan']['plan_id']);
        $policy_subtype_id = $this->session->userdata('policysubtypeid');
        $data['quoteData'] = $this->session->userdata('quoteData');
        //  print_r($data['quoteData']);die;
        $data['compare_features'] = $this->db->query("select * from compare_features where is_active=1 and policy_sub_type_id=" . $policy_subtype_id)->result();
        $features = array();
        foreach ($exp as $d) {
            $feature_id = $this->db->query("select feature_id from master_policy where plan_id=" . $d . " and policy_sub_type_id=" . $policy_subtype_id)->row()->feature_id;
            $features[$d] = $feature_id;
        }
        $data['features'] = $features;
        // var_dump($data['compare_features']);die;
        $this->session->set_userdata('CompareData', $data);
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/compare-quotes', $data);
    }

    public function select()
    {
        $data = array();
        $get_data_subtype = $this->get_data_subtype();
        if (count($get_data_subtype) > 0) {
            $data['subtype'] = $get_data_subtype;
        }
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/select', $data);
    }

    public function quote()
    {
        $lead_id = $_GET['Lead'];
        $this->session->set_userdata('lead_id', $lead_id);
        $sort = $_GET['sort'];
        $purchaseDate = $this->session->userdata('purchaseDate');
        $partner_id = $this->session->userdata('partner_id');
        $subject_matter_insured = $this->session->userdata('subject_matter_insured');
        $type_of_shipment = $this->session->userdata('type_of_shipment');
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/getquoteData', array("lead_id" => $lead_id, "sort" => $sort,
            "purchaseDate" => $purchaseDate, 'partner_id' => $partner_id,"subject_matter_insured"=>$subject_matter_insured
        ,"type_of_shipment"=>$type_of_shipment));
        //print_r($checkDetails);die;
        $data['master_policy'] = json_decode($checkDetails);
        // var_dump($data['master_policy']);die;
        //purchaseDate

        $cnt = 1;
        function cmp($a, $b)
        {
            return $b->sort_prem[0] - $a->sort_prem[0];
        }

        function cmp2($a, $b)
        {
            return $a->sort_prem[0] - $b->sort_prem[0];
        }

        if ($sort == 1) {
            usort($data['master_policy'], "cmp");
        }
        if ($sort == 2) {
            usort($data['master_policy'], "cmp2");
        }
        $this->session->set_userdata('quoteData', $data);
        // var_dump($data['master_policy']);die;
        //  print_r($data['master_policy'][1]->sort_prem[0]);die;
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/quote', $data);
    }


    public function proposal()
    {
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/proposal');
    }

    public function submitLead()
    {
        if ($this->input->is_ajax_request()) {

            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $gender = $this->input->post('gender');
            $name = $this->input->post('fullname');
            $partner_id = $this->session->userdata('partner_id');
            $parts = explode(" ", $name);
            if (count($parts) > 1) {
                $lastname = array_pop($parts);
                $firstname = implode(" ", $parts);
            } else {
                $firstname = $name;
                $lastname = "";
            }
            //echo $lastname;die;
            if (empty($lastname)) {
                $response['msg'] = 'Please enter full name';
                $response['code'] = 201;
                echo json_encode($response);
                exit;
            }

            // print_r($_POST);exit;
            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createLead', [
                'mobile' => $mobile,
                'email' => $email,
                'gender' => $gender,
                'name' => $name
            ]);
            // echo $this->db->last_query();exit;
            //print_r($checkDetails);exit;

            $checkDetails = json_decode($checkDetails, true);
            //print_r($checkDetails);exit;

            if ($checkDetails['status_code'] == 200) {

                $this->session->set_userdata('lead_id', $checkDetails['data']['lead_id']);
                $this->session->set_userdata('customer_id', $checkDetails['data']['customer_id']);
                $this->session->set_userdata('trace_id', $checkDetails['data']['trace_id']);
            }
            if (isset($partner_id)) {
                $lead_id = encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D');
                $where = array('lead_id' => $lead_id);
                $this->db->where($where);
                $update = $this->db->update('lead_details', array('creditor_id' => encrypt_decrypt_password($partner_id, 'D')));
            }
            //print_r($_SESSION);exit;

            echo json_encode($checkDetails);
            exit;
        }
    }

    public function updateDetails()
    {
        $price = $this->input->post('price');
        $daterange = $this->input->post('daterange');
        $policy_subtype_id = $this->input->post('policy_subtype_id');
        $lead_id = $this->input->post('lead_id');
        $logo = $this->input->post('logo');
        $subject_matter_insured = $this->input->post('subject_matter_insured');
        $type_of_shipment = $this->input->post('type_of_shipment');
        $this->session->set_userdata('logo', $logo);
        $this->session->set_userdata('purchaseDate', $daterange);
        $this->session->set_userdata('policysubtypeid', $policy_subtype_id);
        $this->session->set_userdata('subject_matter_insured', $subject_matter_insured);
        $this->session->set_userdata('type_of_shipment', $type_of_shipment);
        $array = array('price' => $price, 'daterange' => $daterange, 'policy_subtype_id' => $policy_subtype_id, 'lead_id' => $lead_id);
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/updatepurchaseDetails', $array);

        echo($checkDetails);
        exit;
    }

    public function get_data_subtype()
    {
        $query = $this->db->query("select * from master_policy_sub_type where policy_type_id=3 and isactive=1")->result();
        return $query;
    }

    public function getfeatures()
    {
        $plan_id = $this->input->post('plan_id');
        $creditor_id = $this->input->post('creditor_id');
        $qry = "SELECT * FROM features_config where creditor_id = '" . $creditor_id . "' AND plan_id = '" . $plan_id . "' AND isactive = 1 limit 5";
        $data['features'] = $this->db->query($qry)->result_array();
        echo json_encode($data);
    }

    public function create_policy_member_plan()
    {

        $req_data = [];
        $req_data['lead_id'] = $this->input->post('lead_id');

        $req_data['policy_id'] = $this->input->post('policy_id');
        $req_data['plan_id'] = $this->input->post('plan_id');
        $req_data['cover'] = $this->input->post('cover');
        $req_data['premium'] = $this->input->post('premium');
        $req_data['tenure'] = $this->input->post('tenure');
        $req_data['total_premium'] = $this->input->post('premium');

        $response = curlFunction(SERVICE_URL . '/customer_api/createPolicy_member_plan_gadget', $req_data);
        $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');
        $creditor_id = $this->db->query("select creditor_id from master_plan where plan_id = ".$req_data['plan_id'])->row()->creditor_id;
        $this->db->where(array('lead_id'=>$lead_id));
        $this->db->update('lead_details',array('creditor_id'=>$creditor_id));
        $data['status'] = 'success';
        // $data['data'] = $response;

        echo json_encode($data);
    }

    public function generate_proposal()
    {

        $req_data = [];
        $req_data['lead_id'] = $this->input->post('lead_id_new');
        $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');
        $query1 = $this->db->query("select trace_id from lead_details where lead_id=" . $lead_id)->row();
        $req_data['trace_id'] = encrypt_decrypt_password($query1->trace_id, 'E');
        $query2 = $this->db->query("select customer_id from master_customer where lead_id=" . $lead_id)->row();
        $req_data['customer_id'] = encrypt_decrypt_password($query2->customer_id, 'E');
//echo $_POST['cover'];die;
        if (isset($_POST['plan_id'])) {
            $this->session->set_userdata('plan_id', $_POST['plan_id']);
        }
        if (isset($_POST['cover'])) {
            $this->session->set_userdata('cover', $_POST['cover']);
        }
        if (isset($_POST['policy_id'])) {
            $this->session->set_userdata('policy_id', $_POST['policy_id']);
        }
        if (isset($_POST['premium'])) {
            $this->session->set_userdata('premium', $_POST['premium']);
        }
        if (isset($_POST['si_type_id'])) {
            $this->session->set_userdata('si_type_id', $_POST['si_type_id']);
        }
        if (isset($_POST['creditor_logo'])) {
            $this->session->set_userdata('creditor_logo', $_POST['creditor_logo']);
        }

        $req_data['si_type_id'] = $this->session->userdata('si_type_id');
        $req_data['creditor_logo'] = $this->session->userdata('creditor_logo');
        $req_data['policy_id'] = $this->session->userdata('policy_id');
        $req_data['plan_id'] = $this->session->userdata('plan_id');
        $req_data['cover'] = $this->session->userdata('cover');
        $req_data['premium'] = $this->session->userdata('premium');

        $req_data['plan_name'] = $_POST['plan_name'];
        $query = $this->db->query("select creditor_id from master_plan where plan_id=" . $req_data['plan_id'])->row();
        $creditor_id = $query->creditor_id;
        $qry = "SELECT * FROM features_config where  creditor_id=" . $creditor_id . " AND  plan_id = '" . $req_data['plan_id'] . "' AND isactive = 1 limit 5";
        $features = $this->db->query($qry)->result();
        // echo SERVICE_URL . '/customer_api/getCustomerDetails';exit;

        $get_summary_details = curlFunction(SERVICE_URL . '/customer_api/get_summary_details', $req_data);

        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        //  print_r($get_customer_details); exit;
        $nominee_relation = curlFunction(SERVICE_URL . '/customer_api/getNomineeRelation', $req_data);
        //  print_r($nominee_relation); exit;
        $nominee_relation = json_decode($nominee_relation, TRUE);
        $NomineeDetails = $this->db->query("select nominee_relation,nominee_first_name from proposal_details where lead_id=" . $lead_id . " AND customer_id=" . $query2->customer_id . " AND trace_id=" . $query1->trace_id)->row();
        if ($NomineeDetails == Null) {
            $NomineeDetails = new \stdClass();
            $NomineeDetails->nominee_relation = '';
            $NomineeDetails->nominee_first_name = '';
        }
        $data['customer_details'] = json_decode($get_customer_details, TRUE);
        $data['get_summary_details'] = json_decode($get_summary_details, TRUE);
        $data['post_data'] = $req_data;
        $data['features'] = $features;
        $data['nominee_relations'] = $nominee_relation['data'];
        $data['NomineeDetails'] = $NomineeDetails;

        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/proposal', $data);
    }

    public function create_proposal()
    {
        $req_data = [];
        $post_data = $this->input->post('post_data');
        $this->session->set_userdata('post_data', $post_data);
        $req_data['policy_id'] = $post_data['policy_id'];
        $req_data['premium'] = $post_data['premium'];
        $req_data['lead_id'] = $post_data['lead_id'];
        $req_data['customer_id'] = $post_data['customer_id'];
        $req_data['trace_id'] = $post_data['trace_id'];

        $req_data['plan_id'] = $post_data['plan_id'];
        $req_data['cover'] = $post_data['cover'];
        $req_data['premium'] = $post_data['premium'];
        // $req_data['partner_id'] = $this->session->userdata('partner_id_session');

        $proposal_data = curlFunction(SERVICE_URL . '/customer_api/create_proposalGadget', $req_data);
        //  print_r($proposal_data);exit;
        echo ($proposal_data);

    }

    function update_customer_nominee_details()
    {
        $req_data = [];
        // print_r($_POST);die;
        // $post_data=$this->input->post('post_data');
        // $data = json_decode($post_data, TRUE);
        // var_dump($data);die;
        $lead_id = $_POST['lead_id'];
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $trace_id = $_POST['trace_id'];
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $customer_id = $_POST['customer_id'];
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $plan_id = $_POST['plan_id'];
        $req_data['plan_id'] = $plan_id;
        $req_data['lead_id'] = $lead_id;
        $req_data['customer_id'] = $customer_id;
        $req_data['trace_id'] = $trace_id;

        /*$info = pathinfo($_FILES['filename']['name']);
        $ext = $info['extension']; // get the extension of the file
        $newname = "Invoice".$_POST['policy_id'].$ext;*/

        $target = '';

        //  $r= move_uploaded_file( $_FILES['filename']['tmp_name'], $target);


        /*if (isset($_FILES['newfile'])) {
//echo 1;
            $upload_dir = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'gadget';
            $file_ext = pathinfo($_FILES['newfile']['name'], PATHINFO_EXTENSION);
            $size = $_FILES['newfile']['size'];
            $savename = "Invoice" . $_POST['policy_id'] . "." . $file_ext;
            $path = $upload_dir . DIRECTORY_SEPARATOR . $savename;


            if (move_uploaded_file($_FILES['newfile']['tmp_name'], $path)) {

                $target = FRONT_URL . "/assets/gadget/$savename";
            }
        }*/
        //echo $target;die;
        //  var_dump($r);die;
        /* ini_set('display_errors', 1);
         ini_set('display_startup_errors', 1);
         error_reporting(E_ALL);*/

        $req_data['nominee_name'] = $this->input->post('first_name');
        $req_data['nominee_relation'] = 1;
        if ($_POST) {
            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/updateNomineeDetails', $req_data);

            if ($checkDetails) {
                $data['status'] = $checkDetails;
                $data['message'] = "Nominee details updated successfully !";

            }
        }
        $policy_id = $_POST['policy_id'];
        $req_data1['lead_id'] = $lead_id;
        $req_data1['customer_id'] = $customer_id;
        $req_data1['policy_id'] = $policy_id;
        $req_data1['fname'] = $this->input->post('first_name');
        $req_data1['lname'] = $this->input->post('last_name');
        $req_data1['gender'] = $this->input->post('gender');
        $req_data1['email'] = $this->input->post('email');
        $req_data1['proposer_pincode'] = $this->input->post('pincode');
        $req_data1['unique_number'] = $this->input->post('unique_number');
        $req_data1['make'] = $this->input->post('make');
        $req_data1['invoice_file'] = $target;

        if ($_POST) {

            $checkDetails = curlFunction(SERVICE_URL . '/customer_api/updateCustomerDetails', $req_data1);
            $data['customerUpdate'] = $checkDetails;
        }
        echo json_encode($data);

    }

    public function redirect_to_pg()
    {
        $post_data = $this->session->userdata('post_data');
        if ($post_data['lead_id'] && $post_data['customer_id'] && $post_data['trace_id']) {

        } else {
            redirect('/GadgetInsurance/');
        }

        $api = new Api($this->keyId, $this->keySecret);
        $data['lead_id'] = encrypt_decrypt_password($post_data['lead_id'], 'D');;
        $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $data), TRUE);

        if (isset($res['data']) && $res['status'] == 200) {
            redirect('/GadgetInsurance/success_view/' . $post_data['lead_id']);
        }
        $req_data['lead_id'] = $post_data['lead_id'];
        $req_data['customer_id'] = encrypt_decrypt_password($post_data['customer_id'], 'E');
        $req_data['trace_id'] = encrypt_decrypt_password($post_data['trace_id'], 'E');
        $req_data['plan_id'] = encrypt_decrypt_password($post_data['plan_id'], 'E');
        $req_data['cover'] = $post_data['cover'];
        $policy_id = $post_data['policy_id'];
        $req_data['policy_id'] = $policy_id;

        $get_customer_details = curlFunction(SERVICE_URL . '/customer_api/getCustomerDetails', $req_data);
        $get_customer_details = json_decode($get_customer_details, TRUE);
        $proposal_details = curlFunction(SERVICE_URL . '/customer_api/getProposalDetails', $req_data);
        $proposal_details = json_decode($proposal_details, TRUE);
        $premiumAmount = $post_data['premium'];
        /*foreach ($proposal_details['proposal_details'] as $prow){
            $premiumAmount = $prow['premium_amount'];
        }*/
        $payment_mode_id = $this->db->query("select payment_mode_id from plan_payment_mode where master_plan_id=" . $post_data['plan_id'])->row()->payment_mode_id;
        if ($payment_mode_id == 4) {
            $get_policy_detC = $this->db->query("select creditor_id,plan_id from master_policy where policy_id = '$policy_id'")->row_array();
            $creditor_id = $get_policy_detC['creditor_id'];
            $plan_id = $get_policy_detC['plan_id'];
            //$get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();

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
                $policy_cd_data['cd_utilised'] = $cd_utilised + $premiumAmount;
                $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

            } elseif ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
                echo "Not Sufficient CD Balance.";
                exit;
            } else {
                $cd_balance_remain = $balance - $premiumAmount;
                $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                $policy_cd_data['cd_utilised'] = $cd_utilised + $premiumAmount;
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
            redirect('/GadgetInsurance/success_view/' . $post_data['lead_id']);
        }
        //$premiumAmount = $proposal_details['proposal_details'][0]['premium_amount'];
        $premium_details = json_decode(curlFunction(SERVICE_URL . '/customer_api/getPremium', $req_data), TRUE);
        $leadId = $get_customer_details['customer_details']['lead_id'];
        //echo $leadId;exit;
        $customer_name = $get_customer_details['customer_details']['full_name'];
        $mobileNumber = $get_customer_details['customer_details']['mobile_no'];
        $email = $get_customer_details['customer_details']['email_id'];
        $address = trim($get_customer_details['customer_details']['address_line1'] . ' ' . $get_customer_details['customer_details']['address_line2'] . ' ' . $get_customer_details['customer_details']['address_line3']);
        $PaymentMode = "PO";
        $ProductInfo = $premium_details['policy_data']['creaditor_name'] . ' - ' . $premium_details['policy_data']['plan_name'];
        $Source = 'ABC';
        $Vertical = 'ABCGRP';
        $ReturnURL = base_url("/quotes/success_view/" . $leadId);
        $UniqueIdentifier = "LEADID";
        $UniqueIdentifierValue = $leadId;
        $CustomerName = $customer_name;
        $Email = $email;
        $PhoneNo = substr(trim($mobileNumber), -10);
        $FinalPremium = round($premiumAmount, 2);

        $orderData = [
            'receipt' => 3456,
            'amount' => $FinalPremium * 100, // 2000 rupees in paise
            'currency' => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $api->order->create($orderData);

        $razorpayOrderId = $razorpayOrder['id'];

        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

        $displayAmount = $amount = $orderData['amount'];
        $displayCurrency = $this->displayCurrency;

        if ($displayCurrency !== 'INR') {
            $url = 'https://api.razorpay.com/v1/orders';
            $exchange = json_decode(file_get_contents($url), true);

            $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
        }

        $checkout = 'automatic';

        if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true)) {
            $checkout = $_GET['checkout'];
        }

        $data = [
            "key" => $this->keyId,
            "amount" => $amount,
            "name" => 'Elephant',
            "description" => $ProductInfo,
            "image" => "/assets/images/logo.png",
            "prefill" => [
                "name" => $customer_name,
                "email" => $email,
                "contact" => $mobileNumber,
            ],
            "notes" => [
                "address" => $address,
                "merchant_order_id" => "12312321",
            ],
            "theme" => [
                "color" => "#F37254"
            ],
            "order_id" => $razorpayOrderId,
        ];


        $data['display_currency'] = $displayCurrency;
        $data['display_amount'] = $displayAmount;
        $data['customer_id'] = $req_data['customer_id'];
        $data['lead_id'] = $req_data['lead_id'];

        $res['data'] = $data;
        $lead_id = encrypt_decrypt_password($req_data['lead_id'], 'D');
        insert_application_log($lead_id, 'Payment_request', json_encode($data), "", 123);
        //$json = json_encode($data);
        //$this->load->view('template/customer_portal_header.php');
        $this->load->view('GadgetInsurance/pg_submit', $res);
        //$this->load->view('template/customer_portal_footer.php');
    }

    public function success_view($lead_id)
    {
        // echo $lead_id;exit;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        insert_application_log($lead_id, 'Payment_response', "", json_encode($_POST), 123);
        //  echo $lead_id;exit;
        session_destroy();
        //echo "<PRE>";print_r($_REQUEST);
        $success = true;

        // echo $lead_id;exit;

        $error = "Payment Failed";
        $data = [];

        if (isset($_POST['razorpay_payment_id'])) {
            // var_dump($success);exit;
            if ($success === true) {

                $req_data['pg_response'] = $_REQUEST;
                $req_data['lead_id'] = $lead_id;
                $update_payment_status = json_decode(curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data), TRUE);
                //  print_r($update_payment_status);die;
                $master_policy_id=$this->db->query("select master_policy_id from api_proposal_response where lead_id=".$lead_id)->row()->master_policy_id;

                $coi_type=$this->db->query("select coi_type from master_plan where plan_id=(select plan_id from master_policy where policy_id=".$master_policy_id.")")->row()->coi_type;
                $data['coi_type']=$coi_type;
//                print_r($req_data);exit;
                $cond = "";

                //if($update_payment_status){
                $req_data1['lead_id'] = $lead_id;
                $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data1), TRUE);

                $coi_no = $res["data"]["certificate_number"];
                //  $gadgetNames = $res["data"]["gadgetNames"];

            } else {
                $data['html'] = '<p class="g-success mt-1 text-center">Your payment failed</p>
                <p>Lead ID:<span id="lead_view"> ' . $lead_id . '</span></p>
                         <p> <span>' . $error . '</span></p>';
            }
        } else {
            $req_data['lead_id'] = $lead_id;

            //echo "in";exit;
            $update_payment_status = json_decode(curlFunction(SERVICE_URL . '/customer_api/updateProposalStatus', $req_data), TRUE);
            $req_data['lead_id'] = $lead_id;
            $master_policy_id=$this->db->query("select master_policy_id from api_proposal_response where lead_id=".$lead_id)->row()->master_policy_id;

            $coi_type=$this->db->query("select coi_type from master_plan where plan_id=(select plan_id from master_policy where policy_id=".$master_policy_id.")")->row()->coi_type;
            $data['coi_type']=$coi_type;
            $res = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOidetails', $req_data), TRUE);
            $coi_no = $res["data"]["certificate_number"];
            //      $gadgetNames = $res["data"]["gadgetNames"];
        }
        $policy_sub_type_id = $this->db->query("select (select group_concat(policy_sub_type_id) from master_policy mp where mp.plan_id=pd.plan_id) as policy_sub_type_id from proposal_details pd where lead_id=" . $lead_id)->row()->policy_sub_type_id;
        $gadgetNames = $this->db->query("select group_concat(gadget_name) as gadget_name from master_policy_sub_type where policy_sub_type_id in(" . $policy_sub_type_id . ")")->row()->gadget_name;
        // echo $gadgetNames;die;
        $master_policy_id = $this->db->query("select plan_id from lead_details where lead_id=" . $lead_id)->row()->plan_id;

        $coi_type_det = $this->db->query("select plan_id,creditor_id from master_plan where plan_id=" . $master_policy_id)->row_array();
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
        $data['coi_no'] = $coi_no;
        $data['lead_id'] = $lead_id;
        $data['gadgetNames'] = $gadgetNames;
        $this->load->view('template/gadget_header.php');
        $this->load->view('GadgetInsurance/thank_you', $data);
    }

    public function coidownload()
    {


        $leadId = $_GET['lead_id'];
        $req_data['lead_id'] = $leadId;
        {
            if (empty($leadId))

                $req_data['lead_id'] = $_POST['lead_id'];
        }
        //print_r($req_data['lead_id']);exit;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data), TRUE);
        // print_r($data);exit;
        $premiumDetails = $data['premium_details'];
        //print_r($data['premium_details']);exit;
        if (isset($data['data']) && !empty($data['data'])) {

            $data = $data['data'];
            $data['premium_details'] = $premiumDetails;
            $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                'lead_id' => encrypt_decrypt_password($_POST['lead_id']),


            ]), TRUE);
            //print_r($checkDetails);exit;

            $data['insured_member'] = $checkDetails;

        }
        $html = $this
            ->load
            ->view("GadgetInsurance/coi_pdf", $data, true);
        echo $html;

    }
    public function coidownloadNew($leadIdT='',$send_mail=0)
    {
        if(empty($leadIdT)){
        $leadId = base64_decode($_GET['lead_id']);
        }else{
            $leadId = base64_decode($leadIdT);
        }

        $req_data['lead_id'] = ($leadId);
        {
            if (empty($leadId))

                $req_data['lead_id'] = $_POST['lead_id'];
            $req_data['policy_type'] = 3;
        }
        //print_r($req_data['lead_id']);exit;

        //     $data = (curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data));

        //   print_r($data);
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data), TRUE);

        $data_marine = json_decode(curlFunction(SERVICE_URL . '/customer_api/getmarineData', $req_data), TRUE);
        $premiumDetails = $data['premium_details'];
        //print_r($data['premium_details']);exit;
        if (isset($data['data']) && !empty($data['data'])) {

            $data = $data['data'];
            $data['premium_details'] = $premiumDetails;
            $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                'lead_id' => encrypt_decrypt_password($leadId),


            ]), TRUE);
            //print_r($checkDetails);exit;

            $data['insured_member'] = $checkDetails;

        }
        //  print_r($premiumDetails);die;
        $coi_html='';
        if($premiumDetails['master_policy_id'] != null){
            $creditor_id=$this->db->query("select creditor_id from master_policy where policy_id=".$premiumDetails['master_policy_id'])->row()->creditor_id;

            $coi_html=$this->db->query("select coi_html from coi_format_html where creditor_id=".$creditor_id." AND policy_type=3")->row()->coi_html;

            if(is_null($coi_html)){
                $coi_html=$this->db->query("select coi_html from coi_format_html where is_default=1 AND policy_type=3")->row()->coi_html;
            }
        }
        //print_r($data);
        //echo $coi_html;die;
        //    $data['coi_html']=$coi_html;
        //  $coi_html=$data['coi_html'];
        //echo $coi_html;die;
        extract($data);
        $coi_html = str_replace("{{productName}}", $creaditor_name.' - '.$plan_name, $coi_html);
        $coi_html = str_replace("{{masterPolicyNumber}}", $policy_number, $coi_html);
        $coi_html = str_replace("{{premium_with_tax}}", $premium_details['premium_with_tax'], $coi_html);
        $coi_html = str_replace("{{net_premium}}", $premium_details['premium'], $coi_html);
        $coi_html = str_replace("{{tax_percent}}", round((($premium_details['premium_with_tax'] - $premium_details['premium']) / $premium_details['premium']) * 100) . "%", $coi_html);
        $coi_html = str_replace("{{gross_premium}}", $premium_details['premium_with_tax'], $coi_html);
        $coi_html = str_replace("{{addr_details}}", $addr_details, $coi_html);
        $coi_html = str_replace("{{certificateNumber}}", $certificate_number, $coi_html);
        $coi_html = str_replace("{{policyHolderName}}", $cust_details, $coi_html);
        $coi_html = str_replace("{{planName}}", $plan_name, $coi_html);
        $coi_html = str_replace("{{mobileNumber}}", $mobile_number, $coi_html);
        $coi_html = str_replace("{{startdate}}", "00:01 hrs ".date('d/m/Y',strtotime($start_date)), $coi_html);
        $coi_html = str_replace("{{enddate}}", "23:59 on ".date('d/m/Y',strtotime($end_date)), $coi_html);
        $coi_html = str_replace("{{certificate_date}}", date('d/m/Y',strtotime($created_date)), $coi_html);
        if($data_marine['status']== 200){
            extract($data_marine['data'][0]);
            //   print_r($Bill_number);die;
            $coi_html = str_replace("{{bill_number}}", $Bill_number, $coi_html);
            $coi_html = str_replace("{{bill_date}}", date('d/m/Y',strtotime($Bill_date)), $coi_html);
            $coi_html = str_replace("{{type_of_shipment}}", $type_of_shipment, $coi_html);
            $coi_html = str_replace("{{from_loc}}", $from_country."-".$from_city, $coi_html);
            $coi_html = str_replace("{{to_loc}}", $to_country."-".$to_city, $coi_html);
            $coi_html = str_replace("{{subject_matter_insured}}", $subject_matter_insured, $coi_html);
            $coi_html = str_replace("{{issuance_date}}", date('d/m/Y',strtotime($Invoice_date)), $coi_html);
            $coi_html = str_replace("{{place_of_issuance}}", $place_of_issuence, $coi_html);
            $coi_html = str_replace("{{Conveyance}}", $Conveyance, $coi_html);
            $coi_html = str_replace("{{Packing}}", $Packing, $coi_html);
            $coi_html = str_replace("{{Excess}}", $Excess, $coi_html);
            $coi_html = str_replace("{{Basis_of_valuation}}", $Basis_of_valuation, $coi_html);
        }
        //  echo $coi_html;die;
        $table='<table>
            <tr style="background-color: #ffd400">
                <th>Insured Gadget</th>
                <th>Date of Purchase</th>
                <th>Make  </th>
                <th>Model  </th>
                <th>Sum Insured</th>
                <th>Premium</th>

            </tr>

            <tr>
                <td> '.$policy_sub_type_name.'</td>
                <td>'.$gadeget_purchase_date.'</td>
                <td> '.$make.'</td>
                <td> '.$unique_number.'</td>
                <td>'.$premium_details['sum_insured'].'</td>
                <td>'. $premium_details['premium'].'</td>
            </tr>

        </table>';
        $coi_html = str_replace("{{insuredTable}}", $table, $coi_html);
//     echo $coi_html;
        $dompdf = new Dompdf();
        $dompdf->loadHtml($coi_html);
        $customPaper = array(0,0,1100,800);
        $dompdf->set_paper($customPaper);
        //$dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        // $cert_no =  str_replace("\",'-',$certificate_number);
        $filename = str_replace("/",'-',$certificate_number)."-".base64_encode($leadId).".pdf";
        $path=FCPATH."/assets/all_coi/".$filename;
        $where=array("lead_id"=>$leadId);
        $output = $dompdf->output();
        //print_r($path);die;
        file_put_contents($path, $output);
        $this->db->where($where);
        $this->db->update('api_proposal_response',array('COI_url'=>"/assets/all_coi/".$filename));
        if($send_mail){
            $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$leadId."'")->row()->trace_id;
            $plan_id=$this->db->query("select plan_id from proposal_details where lead_id=".$leadId)->row()->plan_id;
            $coi_type=$this->db->query("select coi_type from master_plan where plan_id=".$plan_id)->row()->coi_type;
            $query = $this->db->query("select date(pd.created_at) as created_at,mc.salutation,mc.first_name,mc.last_name,
mc.email_id,pd.lead_id,pd.trace_id,ppd.premium,ppd.cover,pp.proposal_no,pp.status,
(select certificate_number from api_proposal_response apr where apr.lead_id=mc.lead_id) as certificate_number,
(select COI_url from api_proposal_response apr where apr.lead_id=mc.lead_id) as COI_url 
from master_customer mc
join proposal_details pd on pd.lead_id=mc.lead_id and pd.plan_id=".$plan_id." 
join policy_member_plan_details ppd on ppd.lead_id=mc.lead_id
join proposal_policy pp on pp.lead_id=mc.lead_id where mc.lead_id=" . $leadId . " AND pd.trace_id=" . $trace_id)->row();
            if($this->db->affected_rows() >0){
                if($coi_type == 1){
                    $COI_url=FRONT_URL.$query->COI_url;
                }else{
                    $COI_url=$query->COI_url;
                }
                if(!empty($certificate_number)) {
                    $mail_array = array($query->email_id, $query->first_name, $COI_url, $query->proposal_no);
                    $sendMail = $this->sendMail($mail_array);
                }
            }
        }
        if(empty($leadIdT)){
            $dompdf->stream($filename, array("Attachment" => false));
        }else{
            return true;
        }

    }
    function compdownload()
    {
        $CompareData = $this->session->userdata('CompareData');
        $lead_id = encrypt_decrypt_password($this->session->userdata('lead_id'), "D");
        $partner_id = encrypt_decrypt_password($this->session->userdata('partner_id', "D"));
        $CompareData['lead_id'] = $lead_id;
        $html = $this
            ->load
            ->view("GadgetInsurance/compare_pdf", $CompareData);
        echo $html;
    }

    public function saveProposalapiGadget()
    {
        $api_data = json_decode(file_get_contents('php://input'), true);
        $_POST = $api_data; // print_r($api_data);die;
        $ClientCreation = $_POST['ClientCreation'];
        //   print_r($ClientCreation);die;
        $mobile = $ClientCreation['mobile_number'];
        $email = $ClientCreation['email_id'];
        $gender = $ClientCreation['gender'];
        $name = $ClientCreation['first_name'] . $ClientCreation['last_name'];
        $plan_id = $ClientCreation['plan_id'];
        $tenure = $ClientCreation['tenure'];
        $make_model = $ClientCreation['make_model'];
        $gadget_purchase_date = $ClientCreation['gadget_purchase_date'];
        $gadget_purchase_price = $ClientCreation['gadget_purchase_price'];
        $salutation = $ClientCreation['salutation'];
        $QuoteRequest = $_POST['QuoteRequest'];
        $SumInsuredData = $_POST['QuoteRequest']['SumInsuredData'];
        $sumInsuredArray=array();
        foreach ($SumInsuredData as $p){
            $PlanCode=$p['PlanCode'];
            $sumInsuredArray[$PlanCode]=$p['SumInsured'];
        }
        //  print_r($sumInsuredArray);die;
        $ReceiptCreation = $_POST['ReceiptCreation'];
        $modeOfEntry = $ReceiptCreation['modeOfEntry'];
        $PaymentMode = $ReceiptCreation['PaymentMode'];
        $bankName = $ReceiptCreation['bankName'];
        $branchName = $ReceiptCreation['branchName'];
        $bankLocation = $ReceiptCreation['bankLocation'];
        $chequeType = $ReceiptCreation['chequeType'];
        $ifscCode = $ReceiptCreation['ifscCode'];
        $PolicyCreationRequest = $_POST['PolicyCreationRequest'];
        $TransactionNumber = $PolicyCreationRequest['TransactionNumber'];
        $TransactionRcvdDate = $PolicyCreationRequest['TransactionRcvdDate'];
        $PaymentMode1 = $PolicyCreationRequest['PaymentMode'];
        $paymentModeNew=$this->db->query("Select payment_mode_id from payment_modes where payment_mode_name='".$PaymentMode1."'");
        if($this->db->affected_rows() > 0){
            $PaymentMode=$paymentModeNew->row()->payment_mode_id;
        }else{
            echo "Given Payment Mode Not Found!";
            exit;
        }
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createLead', array(
            'mobile' => $mobile,
            'email' => $email,
            'gender' => $gender,
            'name' => $name,
            'make_model' => $make_model,
            'gadget_purchase_date' => $gadget_purchase_date,
            'gadget_purchase_price' => $gadget_purchase_price,
            'is_api_lead' => 1,
        ));
        $checkDetails = json_decode($checkDetails, true);
        $req_dataN['plan_id'] = $plan_id;
        $req_dataN['lead_id'] = encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D');
        $req_dataN['customer_id'] = encrypt_decrypt_password($checkDetails['data']['customer_id'], 'D');
        $req_dataN['trace_id'] = encrypt_decrypt_password($checkDetails['data']['trace_id'], 'D');
        $req_dataN['nominee_name'] = $name;
        $req_dataN['nominee_relation'] = 1;
        // if($_POST){
        $checkDetailsMM = curlFunction(SERVICE_URL . '/customer_api/updateNomineeDetails', $req_dataN);
        //  print_r($checkDetailsMM);die;
        if ($checkDetailsMM) {
            $data['status'] = $checkDetailsMM;
            $data['message'] = "Nominee details updated successfully !";

        }
        //   }
        $data_policy_id = $this->db->query("select policy_id,policy_sub_type_id,
(select plan_code from master_policy_sub_type mpst where mpst.policy_sub_type_id =mp.policy_sub_type_id) as plan_code from master_policy mp where plan_id=" . $plan_id)->result();
        // print_r($policy_id);die;
        foreach ($data_policy_id as $item) {
            $policy_id=$item->policy_id;
            $plan_code=$item->plan_code;
            $SumInsured=$sumInsuredArray[$plan_code];
            $basis_id = $this->db->query("select si_premium_basis_id from master_policy_premium_basis_mapping mbs where  mbs.isactive=1 and master_policy_id=" . $policy_id . " order by mapping_id desc limit 1")->row()->si_premium_basis_id;
            $req_data['policy_id'] = $policy_id;
            $req_data['lead_id'] = $checkDetails['data']['lead_id'];
            $req_data['customer_id'] = $checkDetails['data']['customer_id'];
            $req_data['trace_id'] = $checkDetails['data']['trace_id'];
            $req_data['plan_id'] = $plan_id;
            $req_data['cover'] = $SumInsured;
            $req_data['tenure'] = $tenure;
            $premium_data = curlFunction(SERVICE_URL . '/customer_api/getPremiumGadgetapi', array("basis_id" => $basis_id, "policy_id" => $policy_id, "sum_insured" => $SumInsured));
            // print_r($premium_data);
            $premium_data = json_decode($premium_data, true);
            if ($premium_data == 0) {
                echo "No premium found";
                exit;
            }
            $req_data['premium'] = $premium_data['amount'];
            $req_data['total_premium'] = $premium_data['amount'];
            //     print_r($req_data);
            $response = curlFunction(SERVICE_URL . '/customer_api/createPolicy_member_plan_gadget', $req_data);
        }
        $get_policy_detC = $this->db->query("select creditor_id from master_plan where plan_id = '$plan_id'")->row_array();
        $creditor_id = $get_policy_detC['creditor_id'];
        $this->db->where(array('lead_id'=>encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D')));
        $this->db->update('lead_details',array('creditor_id'=>$creditor_id));
//die;
        //print_r($response);die;
        $proposal_data = curlFunction(SERVICE_URL . '/customer_api/create_proposalGadget', $req_data);
        //  print_r($proposal_data);die;
        if ($proposal_data == true) {
            $req_data1['lead_id'] = $checkDetails['data']['lead_id'];
            $req_data1['PaymentMode'] = $PaymentMode1;
            $req_data1['TransactionNumber'] = $TransactionNumber;
            $req_data1['TransactionRcvdDate'] = $TransactionRcvdDate;
            //  echo $PaymentMode;die;


            if ($PaymentMode == 4) {
                $get_policy_detC = $this->db->query("select creditor_id,plan_id from master_policy where policy_id = '$policy_id'")->row_array();
                $creditor_id = $get_policy_detC['creditor_id'];
                $plan_id = $get_policy_detC['plan_id'];
                $response_cd= CheckCDThreshold($creditor_id,$plan_id,$premium_data['amount']);
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
                /*$get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
                if (count($get_policy_det) > 0) {
                    $initial_cd = $get_policy_det['initial_cd'];
                    $cd_threshold = $get_policy_det['cd_threshold'];
                    $deposit = $get_policy_det['deposit'];
                    $cd_utilised = $get_policy_det['cd_utilised'];
                    $balance = ($initial_cd + $deposit) - $cd_utilised;
                }
                if ($balance > $cd_threshold && ($balance != '' && $balance != '0')) {
                    $cd_balance_remain = $balance - $premium_data['amount'];
                    $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                    $policy_cd_data['cd_utilised'] = $cd_utilised + $premium_data['amount'];
                    $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

                } elseif ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
                    echo "Not Sufficient CD Balance.";
                    exit;
                } else {
                    $cd_balance_remain = $balance - $premium_data['amount'];
                    $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                    $policy_cd_data['cd_utilised'] = $cd_utilised + $premium_data['amount'];
                    $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");


                }*/
            }
            //   var_dump($req_data1);die;
            $update_payment_status = curlFunction(SERVICE_URL . '/customer_api/updatePaymentgadget', $req_data1);
            /*if ($update_payment_status['StatusCode'] == 200) {
                $update_payment_status['COI_URL'] = 'http://fyntunecreditoruat.benefitz.in/GadgetInsurance/success_view/' . $checkDetails['data']['lead_id'];
            }*/
            print_r($update_payment_status);
            die;
            // $makePayment=$this->policyIssuance($req_data,$ReceiptCreation,$PolicyCreationRequest);
        }

    }

    function sendmailcompareold()
    {
        $CompareData1 = $this->session->userdata('CompareData');
        $CompareData = $CompareData1['plan'];
        $CompareData['compare_features'] = $CompareData1['compare_features'];
        $CompareData['features'] = $CompareData1['features'];

        $lead_id = encrypt_decrypt_password($this->session->userdata('lead_id'), "D");
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1">
<title>Product Comparison</title>
<style type="text/css">
    table {
        width: 100%;
        margin-left:10px;
        margin-right:13px;
        margin-top:10px;
    }

    table,
    td {
        vertical-align: middle;
        border-collapse: collapse
    }

    html {
        font-family: times;
        padding: 0;
        margin: 0
    }

    body {
        line-height: 1.1;
        color: #333;
        padding: 0;
        margin: 0
    }

    tr.infoRow td {
        text-align: center;
    }

    /*  tr.infoRow {
          border-bottom: 1px solid #edeaea;
      }
    */
    tr.infoRow td {
        border: 1px solid #edeaea;
    }

    tr.infoRow img {
        width: 18px;
    }
    .t-wrap {
        float: right;
        width: 85%;
    }

    .a01 td {
        border: 1px solid #dddddd;
    }

    .a01 th {
        border: 1px solid #dddddd;
    }

    .a01 tr:nth-child(even) {
        background-color: #dddddd;
    }

    .fullwidth {
        width: 100%
    }

    img.rupee {
        width: 5px;
        position: relative;
        top: 2px;
    }

    .tataTable {
        width: 100%;
        padding: 10px;
        word-wrap: break-word;
    }

    .bdr-table {
        border: 10px solid #0072bc
    }

    .tataTable tr td {
        padding: 8px 1px 4px;
        font-size: 12px !important;

    width: {
    {
        td_width
    }
    }

    ;
    }

    .fyntune-top tr td {
        padding: 15px 4px 15px;
        font-size: 12px !important;
        width: 25%;
    }

    td.sticky_part {
        border-right: 1px solid #f6f6f6;
    }

    .borderTable tr td {
        border: 1px solid #000;
    }

    p {
        margin: 0 0 4px 0;
    }

    table.sub-table tr td {
        border: 0;
    }

    .text-center {
        text-align: center
    }

    h6.blue-hd {
        color: #0072bc;
        font-size: 10px;
        padding: 0;
        margin: 0;
    }

    .bdr-left-none {
        border-left: 0 !important
    }

    .bdr-right-none {
        border-right: 0 !important
    }

    .clr-otp {
        color: #387ef5;
    }

    td.fRIGkq {
        text-align: center;
    }

    .add_more {
        font-weight: bold;
        font-size: 7px;
    }

    .rider td {
        border: none;
    }

    .rider tr:nth-child(even) {
        background-color: transparent;
    }

    .rider tr td {
        padding: 0px;
    }

    label {
        display: block;
        padding: 3px 0px;
    }

    input {
        width: 15px;
        height: 15px;
        padding: 0;
        margin: 0;
        vertical-align: bottom;
        position: relative;
    }

    .Product_Name {
        min-height: 30px;
    }

    .productlogo_wrap {
        min-height: 60px;
    }
</style>
<table class="tataTable" cellpadding="0" cellspacing="0" border="0" style="padding-top:0px;margin-top: 0px;">
    <tbody>
    <tr>
        <td>
            <table class="bdr-table" style=" margin:auto; width:80%;border-collapse: collapse;" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="padding-top: 0px">
                        <table class="fyntune-top">
                            <tbody>
                            <tr>
                                <td class="sticky_part">
                                    <div class="product_card2" style="text-align:center;">
                                        <div class="product_wrap2" style="text-align:center">
                                            <div class="productlogo_wrap">
                                                <img style="max-width: 220px;max-height: 50px;margin-bottom: 10px;margin-top: 10px;" src="./assets/gadget/img/logo.png">
                                            </div>
                                            <p class="Product_Name" style="font-size: 16px;">Lead ID: ' . $lead_id . ' </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="sticky_part">
                                    <div class="product_card2" style="text-align:center;">
                                        <div class="product_wrap2" style="text-align:center">
                                            <div> </div>
                                            <!-- <div class="productlogo_wrap">
                                              <img style="max-width: 100px; max-height: 50px; margin-bottom: 10px; width: 46px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACIAAAAgCAYAAAB3j6rJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkE0RUVCNjQ5MUQzQTExRUNCMkYzQjlGRDQzM0M2RjMyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkE0RUVCNjRBMUQzQTExRUNCMkYzQjlGRDQzM0M2RjMyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QTRFRUI2NDcxRDNBMTFFQ0IyRjNCOUZENDMzQzZGMzIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QTRFRUI2NDgxRDNBMTFFQ0IyRjNCOUZENDMzQzZGMzIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7Z19dxAAADc0lEQVR42rSYS0iUURTHv5l8lKSN2cz0gsBC6SEUQQtJS9OWQpsWGSX0MJAeuKlNtbCgWpWtCrI2klAWRO/JSqyFK4MwK3oyDpQzNCUmpePU/8D/g9vH95zGAz+cud+9/3O+c+89946+lsOnNQ82G1wEOUpbCdgA7oJfbEuBvWDMrXCO5s1EeJtJ+yawy+KZK/Nr2bEe8BpUZSrgNSN5oAH4TJ7JdOwD88EfcAtMTFdGxEHaZtpm8XOafT1lJAzawUbwFjSDVxb9J8ENi2fbQTe45sLvCnABlIGn4IBkpAOsBEeYSknpDI+ZWs6Fet3lLOjTJj5XgcuSkXrQyDfpB4NgKbNjZuuY9mKwBKzn+tgBfrsIZBn1G5h5mdJOCSQXjCvzLJZvI9QC5rJufJS0gh8espdv8CW+czPZvns4dc/AfY9BZLWOyNzuBGdAMEt1KOOCFgf7udALpiMQvUZMuRj7DpwAlzzusimDL9NAhkElGHIp2s/d1u4hkCH6GHaamgR4AGI8aQsdhKXARcFBmz6F1IpxgSfcrJFubjFJ+2bwCMxxCOYUmAcCJs9kbIRabWAmffjsDj0RqgC14Am4zb8RFj67rXrUIoiHvLNUMXNvwGP6SqoZmWBRE/vO47yVbVGeQQFmJuBhLehjiqkRpWYrfSSVZExKIB/AGkVgKxfTVR77sqhqQJEi7CaICDNSQ408albSh25rwXsJpJP3iAV88BLUUaCLAjFOlyy6e8pxb2YFDLiIGjFqdDEzdfQhtpCn/RUJ5Cz4Sgd6pRzggGoKaEowi8FJm0Da+FK1HKNRo5qaA2wL8rwS3+f9PHzqOH89JsGMKitchI+D3XxLsxucnEXHlCB81BCtF0oQPVwf0j6ub98RptHPXRJiuwxsMty2ejlFpSaBlPJZr+FW16QEEaIPjT5HjHVEDybN7RWyOfQ0ZaeplmvoY7QQtdO8+cetClqcHVKMOqxlz8LUTBmDsKqscRavSQ7MxlEfpNYE10Tc7TUgzgExXnD/18qoVW92zjj9rklwYDbsuZNWjslv2z4WI6cxd3ji3uT3LeAcP/dxLVjZKC/dP60CkUvxaha5Lw5veQiUK9/LOdXNDuMWsc6U2AWiW4dShq2s0aTtG+8ddlbBQGwX6xgzkXQx759YnnWTz59djEuy7z//svgrwAAHvdNCkt2CgQAAAABJRU5ErkJggg==">
                                            </div> -->
                                            <p class="Product_Name" style="font-size: 16px;">Gadget Insurance</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="sticky_part" style="padding: 8px 4px 4px 24px;">
                                    <div class="product_card2" style="">
                                        <div class="product_wrap2" style="">
                                            <p class="Product_Name" style="font-weight: bold;min-height: 33px;font-size: 21px;">Contact Us</p>
                                            <p style="font-size: 16px;">
                                                <b> Email - </b>
                                                <span> fyntune@gmail.com </span>
                                            </p>
                                            <p style="font-size: 16px;">
                                                <b> Toll Free - </b>
                                                <span> +91 76567 65445 </span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>';


        $plan_names = explode(',', $CompareData['plan_name']);
        $plan_id = explode(',', $CompareData['plan_id']);
        $premiums = explode(',', $CompareData['premium']);
        $cover = explode(',', $CompareData['cover']);
        $tenure = explode(',', $CompareData['tenure']);

        $html .= '<table class="bdr-table" style=" margin:auto; width:80%;border-collapse: collapse;" cellpadding="0" cellspacing="0">
                <tbody><tr class="infoRow">
                                <td>
                                    Plan Name
                                </td>';

        foreach ($plan_names as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>
                            <tr class="infoRow">
                                <td>
                                    Sum Insured (in ₹)
                                </td>';
        foreach ($cover as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>';

        $html .= '   <tr class="infoRow">
                                <td>
                                    Tenure(Year)
                                </td> ';

        foreach ($tenure as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>';
        $features = $CompareData['features'];

        if (count($CompareData['compare_features']) > 0) {
            foreach ($CompareData['compare_features'] as $row) {
                $html .= ' <tr class="infoRow">
 <td>' . $row->feature . '</td>';
                foreach ($plan_id as $p) {
                    $feature = $features[$p];
                    $exp = explode(',', $feature);
                    $html .= '<td >';
                    if (in_array($row->id, $exp)) {
                        $html .= '<img src="./assets/gadget/img/check.png">';
                    } else {
                        $html .= '<img  src="./assets/gadget/img/cross.png">';
                    }
                    $html .= '</td >';
                }
                $html .= '</tr>';
            }
        }


        $html .= ' </tbody>
                        </table>';
        $html .= '  <table>
                            <tbody>
                            <tr>
                                <td style="padding: 21px; text-align: center;">
                                    <p style="font-size: 32px;font-weight: 500;">
                                        <span style="color: #0182ff"> Fyn</span>
                                        <span style="color: #2cd54b">tune Insurance Pvt. Ltd. </span>
                                    </p>
                                    <p style="margin: 10px 1px; font-size: 12px;"> C/o 91springboard co-work, 4th Floor, Akshar Blue Chip IT Park, Turbhe, Navi Mumbai - 400705 </p>
                                    <hr>
                                    <p style="margin: 10px 1px;font-size: 15px;"> Â© 2019 copyright all right reserved </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>';
        define("DOMPDF_ENABLE_REMOTE", true);
        $email = $this->input->post_get('email');
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        if ($email == '0') {
            $dompdf->stream("", array("Attachment" => false));
            exit;
        }
        // $dompdf->stream("",array("Attachment" => false));
        $dir = './assets/gadget/compare_pdf/';
        $filename = 'Compare_pdf' . time();
        $filenameNew = $dir . $filename;
        file_put_contents($filenameNew . '.pdf', $dompdf->output());
        $email = $this->input->post_get('email');
        $f = FRONT_URL . '/assets/gadget/compare_pdf/' . $filename . '.pdf';
        $checkDetails = (curlFunction(SERVICE_URL . '/api2/sendMailCompare', array(
            'email' => $email, 'file' => $f)));
        print_r($checkDetails);
        die;

//        Message could not be sent. Mailer Error: SMTP Error: data not accepted.SMTP server error: DATA END command failed Detail: Unusual sending activity detected. Learn more. Use the link mail.zoho.in/UnblockMe to unblock SMTP code: 550 Additional SMTP info: 5.4.6
    }

    function sendmailcompare()
    {
        $CompareData1 = $this->session->userdata('CompareData');
        $CompareData = $CompareData1['plan'];
        $CompareData['compare_features'] = $CompareData1['compare_features'];
        $CompareData['features'] = $CompareData1['features'];
        $partner_id = encrypt_decrypt_password($this->session->userdata('partner_id'), "D");
        if ($partner_id) {
            $creditor_id = $partner_id;
        } else {
            $creditor_id = 32;
        }

        $pdf_html = $this->db->query("select pdf_html from master_ceditors where creditor_id=" . $creditor_id)->row()->pdf_html;
        if ($this->db->affected_rows() > 0) {

        } else {
            $pdf_html = $this->db->query("select pdf_html from master_ceditors where creditor_id=55")->row()->pdf_html;
        }

        $plan_names = explode(',', $CompareData['plan_name']);
        $plan_id = explode(',', $CompareData['plan_id']);
        $premiums = explode(',', $CompareData['premium']);
        $cover = explode(',', $CompareData['cover']);
        $tenure = explode(',', $CompareData['tenure']);
        $lead_id = encrypt_decrypt_password($this->session->userdata('lead_id'), "D");
        $html = '';
        $html .= '<style>
 tr.infoRow td {
        text-align: center;
    }
  .newtb  table, .newtb th, .newtb td {
  border: 1px solid grey;
  border-collapse: collapse;
}
</style><table class="table newtb" style=" margin:auto; width:80%;border-collapse: collapse;" cellpadding="0" cellspacing="0">
                <tbody><tr class="infoRow">
                                <td>
                                    Plan Name
                                </td>';

        foreach ($plan_names as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>
                            <tr class="infoRow">
                                <td>
                                    Sum Insured (INR)
                                </td>';
        foreach ($cover as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>';
        $html .= '   <tr class="infoRow">
                                <td>
                                    Premium (INR)
                                </td> ';

        foreach ($premiums as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>';
        $html .= '   <tr class="infoRow">
                                <td>
                                    Tenure(Year)
                                </td> ';

        foreach ($tenure as $key => $value) {
            $html .= ' <td style="font-weight: bold;    font-size: 16px !important;">
                                ' . $value . '
                                    </td>';
        }
        $html .= ' </tr>';
        $features = $CompareData['features'];

        if (count($CompareData['compare_features']) > 0) {
            foreach ($CompareData['compare_features'] as $row) {
                $html .= ' <tr class="infoRow">
 <td>' . $row->feature . '</td>';
                foreach ($plan_id as $p) {
                    $feature = $features[$p];
                    $exp = explode(',', $feature);
                    $html .= '<td >';
                    if (in_array($row->id, $exp)) {
                        $html .= '<img src="./assets/gadget/img/check.png">';
                    } else {
                        $html .= '<img  src="./assets/gadget/img/cross.png">';
                    }
                    $html .= '</td >';
                }
                $html .= '</tr>';
            }
        }


        $html .= ' </tbody>
                        </table>';
        $pdf_html = str_replace("{{pdf_table}}", $html, $pdf_html);
        $pdf_html = str_replace("{{lead_id}}", $lead_id, $pdf_html);
        $email = $this->input->post_get('email');
        $dompdf = new Dompdf();
        $dompdf->loadHtml($pdf_html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        if ($email == '0') {
            $dompdf->stream("", array("Attachment" => false));
            exit;
        }
        $dir = './assets/gadget/compare_pdf/';
        $filename = 'Compare_pdf' . time();
        $filenameNew = $dir . $filename;
        file_put_contents($filenameNew . '.pdf', $dompdf->output());
        $email = $this->input->post_get('email');
        $f = FRONT_URL . '/assets/gadget/compare_pdf/' . $filename . '.pdf';
        $checkDetails = (curlFunction(SERVICE_URL . '/api2/sendMailCompare', array(
            'email' => $email, 'file' => $f)));
        print_r($checkDetails);
        die;
    }

    function upload()
    {
        // print_r($_FILES);
        if (isset($_FILES['file'])) {
//echo 1;
            $upload_dir = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'gadget' . DIRECTORY_SEPARATOR . 'compare_pdf';
            $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $size = $_FILES['file']['size'];
            $savename = "Compare" . date('ymdhis') . '.' . $file_ext;
            $path = $upload_dir . DIRECTORY_SEPARATOR . $savename;

            $target = 1;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {

                $target = FRONT_URL . "/assets/gadget/compare_pdf/$savename";
            }
            echo $target;
        }
    }


    function getPremiumApi()
    {

        $api_data = json_decode(file_get_contents('php://input'), true);
        $_POST = $api_data;
        $compulsory=array(
            'token', 'policy_id','sumInsure','subject_matter_insured','type_of_shipment'
        );
        foreach ($compulsory as $row){
            $value =trim($_POST[$row]);
            if(empty($value) || trim($value) == null){
                echo $row." is Compulsory!";
                exit;
            }
        }
        $token = $_POST['token'];
        $checkToken=$this->db->query("select emp_id,time  from token_table t where token= '".$token."' and type=2")->row();
        //print_r($checkToken);die;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $time=$checkToken->time;
            $minutes = (time() - strtotime($time)) / 60;
            if($minutes > 15){
                $response = array('success' => false, 'msg' => "Token Expired!");
                echo json_encode($response);
                exit;
            }
            $userId=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken->emp_id."'")->row()->employee_id;
        }
        $policy_id = $_POST['policy_id'];
        $sumInsure = $_POST['sumInsure'];
        $subject_matter_insured = $_POST['subject_matter_insured'];
        $type_of_shipment = $_POST['type_of_shipment'];
        $policy_sub_type_id=19;
        $basis_id = $this->db->query("select si_premium_basis_id from master_policy_premium_basis_mapping where master_policy_id=" . $policy_id)->row()->si_premium_basis_id;

        $premium = (curlFunction(SERVICE_URL . '/customer_api/getPremiumGadgetapi', array(
            'policy_id' => $policy_id, 'sum_insured' => $sumInsure, "basis_id" => $basis_id,'subject_matter_insured'=>$subject_matter_insured,'policy_sub_type_id'=>$policy_sub_type_id,'type_of_shipment'=>$type_of_shipment)));
        $result = json_decode($premium);
        echo "Premium Amount:- " . $premium = $result->amount;
    }
    function AddBulkFileMarine(){
        $this->load->library('excel');
        if (isset($_FILES["uploadfileMarine"]["name"])) {
            $path = $_FILES["uploadfileMarine"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            $worksheet = $object->getSheet(0);
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $rowDatafirst = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false);
            $excel_header=array('Api_type','Invoice_number','plan_id','policy_id','salutation',
                'first_name','middle_name','last_name','gender','mobile_number','pincode','Address1',
                'Address2','Address3','email_id','mode_of_shipment','from_country','to_country','from_city',
                'to_city','type_of_shipment','currency_type','cargo_value','rate_of_exchange','date_of_shipment',
                'Bill_number','Bill_date','credit_number','credit_description','place_of_issuence','Invoice_date',
                'subject_matter_insured','marks_number','vessel_name','Consignee_name','Consignee_add',
                'Financier_name','SumInsured','userId');
            $difference_array=	array_merge(array_diff($excel_header,$rowDatafirst[0]),array_diff($rowDatafirst[0],$excel_header));

            if(count($difference_array) == 0){
                for ($row = 1; $row <= $highestRow; $row++) {
                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                        null, true, false);
                    $rowData=$rowData[0];

                    if($row != 1){
                        $data=array();
                        $data['ClientCreation']['Api_type']=$rowData[0];
                        $data['ClientCreation']['Invoice_number']=$rowData[1];
                        $data['ClientCreation']['plan_id']=$rowData[2];
                        $data['ClientCreation']['policy_id']=$rowData[3];
                        $data['ClientCreation']['salutation']=$rowData[4];
                        $data['ClientCreation']['first_name']=$rowData[5];
                        $data['ClientCreation']['middle_name']=$rowData[6];
                        $data['ClientCreation']['last_name']=$rowData[7];
                        $data['ClientCreation']['gender']=$rowData[8];
                        $data['ClientCreation']['mobile_number']=$rowData[9];
                        $data['ClientCreation']['pincode']=$rowData[10];
                        $data['ClientCreation']['Address1']=$rowData[11];
                        $data['ClientCreation']['Address2']=$rowData[12];
                        $data['ClientCreation']['Address3']=$rowData[13];
                        $data['ClientCreation']['email_id']=$rowData[14];
                        $data['ClientCreation']['mode_of_shipment']=$rowData[15];
                        $data['ClientCreation']['from_country']=$rowData[16];
                        $data['ClientCreation']['to_country']=$rowData[17];
                        $data['ClientCreation']['from_city']=$rowData[18];
                        $data['ClientCreation']['to_city']=$rowData[19];
                        $data['ClientCreation']['type_of_shipment']=$rowData[20];
                        $data['ClientCreation']['currency_type']=$rowData[21];
                        $data['ClientCreation']['cargo_value']=$rowData[22];
                        $data['ClientCreation']['rate_of_exchange']=$rowData[23];
                        $data['ClientCreation']['date_of_shipment']=$rowData[24];
                        $data['ClientCreation']['Bill_number']=$rowData[25];
                        $data['ClientCreation']['Bill_date']=$rowData[26];
                        $data['ClientCreation']['credit_number']=$rowData[27];
                        $data['ClientCreation']['credit_description']=$rowData[28];
                        $data['ClientCreation']['place_of_issuence']=$rowData[29];
                        $data['ClientCreation']['Invoice_date']=$rowData[30];
                        $data['ClientCreation']['subject_matter_insured']=$rowData[31];
                        $data['ClientCreation']['marks_number']=$rowData[32];
                        $data['ClientCreation']['vessel_name']=$rowData[33];
                        $data['ClientCreation']['Consignee_name']=$rowData[34];
                        $data['ClientCreation']['Consignee_add']=$rowData[35];
                        $data['ClientCreation']['Financier_name']=$rowData[36];
                        $data['ClientCreation']['SumInsured']=$rowData[37];
                        $data['ClientCreation']['userId']=$_SESSION['webpanel']['employee_id'];
                        $request=$this->CreateProposalapi($data);
                    }

                }
                $response['msg']="Done.Check application logs for more details!";
                $response['success']=true;
                echo json_encode($response);
            }else{
                $response['msg']="Wrong file Uploaded";
                $response['success']=false;
                echo json_encode($response);
            }
        }else{
            $response['msg']="Please upload file.";
            $response['success']=false;
            echo json_encode($response);
        }

    }
    function CreateProposalapi($api_data=null)
    {

        if($api_data == null){
            $is_api=1;
        $api_data = json_decode(file_get_contents('php://input'), true);

        }else{
            $is_api=0;
            $api_data=$api_data;

        }

        //   print_r($api_data);die;
        $_POST = $api_data; // print_r($api_data);die;
        $ClientCreation = $_POST['ClientCreation'];
        $userId= $ClientCreation['userId'];
        if($is_api == 1){
        $compulsory=array(
            'token', 'Api_type','first_name','last_name','gender','mobile_number','pincode','Address1','Address2','Address3','email_id',
            'mode_of_shipment','from_country','to_country','from_city','to_city','type_of_shipment','cargo_value',
            'date_of_shipment','subject_matter_insured','Invoice_number','SumInsured'
        );
        foreach ($compulsory as $row){
            $value =trim($ClientCreation[$row]);
            if(empty($value) || trim($value) == null){
                echo $row." is Compulsory!";
                exit;
            }
        }
        $token = $ClientCreation['token'];
        $userId= $ClientCreation['userId'];
        $checkToken=$this->db->query("select emp_id,time  from token_table t where token= '".$token."' and type=2")->row();
        //print_r($checkToken);die;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $time=$checkToken->time;
            $minutes = (time() - strtotime($time)) / 60;
            if($minutes > 15){
                $response = array('success' => false, 'msg' => "Token Expired!");
                echo json_encode($response);
                exit;
            }
            $userId=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken->emp_id."'")->row()->employee_id;
        }
        }




        $Api_type = strtolower($ClientCreation['Api_type']);
        $policy_id = $ClientCreation['policy_id'];
        $Invoice_number = $ClientCreation['Invoice_number'];
        if ($Api_type != 'issuance') {
            $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
            //  echo $this->db->last_query();die;
            $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
            //    echo $this->db->last_query();die;
            $ClientCreation['lead_id']=$lead_id;
            $ClientCreation['trace_id']=$trace_id;
            //check LeadID Exist or not
            $checkLeadExistorNot = curlFunction(SERVICE_URL . '/customer_api/checkLeadExistorNot', array('lead_id' => $lead_id, 'trace_id' => $trace_id));
            //var_dump($checkLeadExistorNot);die;
            if ($checkLeadExistorNot) {

            } else {
                if($is_api == 1) {
                $response = array('success' => false, 'msg' => "Customer Not found.");
                echo json_encode($response);
                exit;
            }
            }
            //  check alredy cancelled or not
            $checkLeadCancelledorNot = curlFunction(SERVICE_URL . '/customer_api/checkLeadCancelledorNot', array('lead_id' => $lead_id, 'trace_id' => $trace_id));
            //    print_r($checkLeadCancelledorNot);die;
            if ($checkLeadCancelledorNot != false) {
                if ($checkLeadCancelledorNot == 'Cancelled') {
                    insert_application_log($lead_id, 'Cancel Requested', json_encode(array('lead_id' => $lead_id, 'trace_id' => $trace_id)), "Policy is already Cancelled.", 123);
                    if($is_api == 1) {
                    $response = array('success' => false, 'msg' => "Policy is already Cancelled");
                    echo json_encode($response);
                    exit;
                }
                }
            } else {
                if($is_api == 1) {
                $response = array('success' => false, 'msg' => "Customer Not found.");
                echo json_encode($response);
                exit;
            }
        }
        }
        $plan_id = $ClientCreation['plan_id'];
        $get_policy_detC = $this->db->query("select creditor_id,policy_sub_type_id from master_policy where policy_id = '$policy_id'")->row_array();
        $creditor_id = $get_policy_detC['creditor_id'];
        $policy_sub_type_id = $get_policy_detC['policy_sub_type_id'];
        $response_cd= CheckCDThreshold($creditor_id,$plan_id,0);
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
        /*$get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
        if (count($get_policy_det) > 0) {
            $initial_cd = $get_policy_det['initial_cd'];
            $cd_threshold = $get_policy_det['cd_threshold'];
            $deposit = $get_policy_det['deposit'];
            $cd_utilised = $get_policy_det['cd_utilised'];
            $balance = ($initial_cd + $deposit) - $cd_utilised;
        }
        if ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
            $response = array('success' => false, 'msg' => "Not Sufficient CD Balance.");
            echo json_encode($response);
            exit;
        }*/

        $mobile = $ClientCreation['mobile_number'];
        $email = $ClientCreation['email_id'];
        $gender = $ClientCreation['gender'];
        $name = $ClientCreation['first_name'] . " " . $ClientCreation['last_name'];
        $plan_id = $ClientCreation['plan_id'];
        $SumInsured = $ClientCreation['SumInsured'];
        $salutation = $ClientCreation['salutation'];
        $Premium = $ClientCreation['Premium'];
        $pincode = ($ClientCreation['pincode'] != 'NA') ? $ClientCreation['pincode'] : '';
        $Address1 = $ClientCreation['Address1'];
        $Address2 =  ($ClientCreation['Address2'] != 'NA') ? $ClientCreation['Address2'] : '';
        $Address3 =  ($ClientCreation['Address3'] != 'NA') ? $ClientCreation['Address3'] : '';
        $mode_of_shipment = $ClientCreation['mode_of_shipment'];
        $from_country = $ClientCreation['from_country'];
        $to_country = $ClientCreation['to_country'];
        $from_city = $ClientCreation['from_city'];
        $to_city = $ClientCreation['to_city'];
        $currency_type = $ClientCreation['currency_type'];
        $cargo_value = $ClientCreation['cargo_value'];
        $rate_of_exchange = $ClientCreation['rate_of_exchange'];
        $date_of_shipment = $ClientCreation['date_of_shipment'];
        $Bill_number = $ClientCreation['Bill_number'];
        $Bill_date = $ClientCreation['Bill_date'];
        $credit_number = $ClientCreation['credit_number'];
        $credit_description = $ClientCreation['credit_description'];
        $place_of_issuence = $ClientCreation['place_of_issuance'];
        $Invoice_date = $ClientCreation['Invoice_date'];
        $subject_matter_insured = $ClientCreation['subject_matter_insured'];
        $marks_number = $ClientCreation['marks_number'];
        $vessel_name = $ClientCreation['vessel_name'];
        $Consignee_name = $ClientCreation['Consignee_name'];
        $Consignee_add = $ClientCreation['Consignee_add'];
        $Financier_name = $ClientCreation['Financier_name'];
        $type_of_shipment = $ClientCreation['type_of_shipment'];
        $Name_of_Transporter = "";
        $Number_and_kind_of_packages = "";
        $Conveyance = "Land";
        $Packing = "Standard";
        $Excess = "1% of Consignment value subject to minimum of Rs. 2500";
        $Basis_of_valuation = "Market (Depreciated) Value";
        // $userId = $ClientCreation['userId'];
        // echo $Api_type;die();
        if ($Api_type == 'issuance' || $Api_type == 'modification') {
            if ($Api_type == 'issuance'){
                //  echo 1;die;
                $lead_idR= $this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
                if($this->db->affected_rows() > 0){

                    insert_application_log($lead_idR, 'Issuance Requested', json_encode(array('Invoice_number' => $Invoice_number)),
                        "This Invoice Number is already exist. Please send request for modification.", 123);
                    if($is_api == 1) {
                    $response = array('success' => false, 'msg' => "This Invoice Number is already exist. Please send request for modification.");
                    echo json_encode($response);
                    exit;
                }
                }

                $d_l_r=array(
                    'mobile' => $mobile,
                    'email' => $email,
                    'creditor_id' => $creditor_id,
                    'gender' => $gender,
                    'plan_id' => $plan_id,
                    'name' => $name,
                    'plan_id' => $plan_id,
                    'Address1' => $Address1,
                    'Address2' => $Address2,
                    'Address3' => $Address3,
                    'pincode' => $pincode,
                    'userId' => $userId,
                    'is_api_lead' => 1,
                );
                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createLead',$d_l_r );
                // print_r($checkDetails);die;
                $checkDetails = json_decode($checkDetails, true);
                if(!is_null($checkDetails['data']['lead_id'])){
                    insert_application_log(encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D'), 'Lead creation', json_encode($d_l_r),
                        json_encode($checkDetails), 123);
                }
                $req_dataN['lead_id'] = encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D');
                $req_dataN['customer_id'] = encrypt_decrypt_password($checkDetails['data']['customer_id'], 'D');
                $req_dataN['trace_id'] = encrypt_decrypt_password($checkDetails['data']['trace_id'], 'D');
            }
            else{
                //get previous premium amount
                $last_premium = $this->db->query("select premium from policy_member_plan_details where lead_id = '$lead_id'")->row()->premium;
                $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
                $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
                //$lead_id = $ClientCreation['lead_id'];
                //$trace_id = $ClientCreation['trace_id'];
                $where=array('lead_id'=>$lead_id,'trace_id'=>$trace_id);
                $where2=array('lead_id'=>$lead_id);
                $this->db->where($where);
                $result=$this->db->update('lead_details',array('mobile_no' => $mobile,
                    'trace_id' => $trace_id,
                    'user_activity' => 1,
                    'createdon' => date('Y-m-d H:i:s'),
                    'email_id' => $email,));
                insert_application_log($lead_id, 'Lead Updation', json_encode(array('mobile_no' => $mobile,
                    'trace_id' => $trace_id,
                    'user_activity' => 1,
                    'createdon' => date('Y-m-d H:i:s'),
                    'email_id' => $email,)),
                    json_encode($result), 123);
                $this->db->where($where2);
                $result2=$this->db->update('master_customer',
                    array('mobile_no' => $mobile,
                        'salutation' => $salutation,
                        'first_name' => $ClientCreation['first_name'],
                        'last_name' => $ClientCreation['last_name'],
                        'gender' => $gender,
                        'full_name' => $name,
                        'email_id' => $email,
                        'createdon' => date('Y-m-d H:i:s'),
                        'address_line1' => $Address1,
                        'address_line2' => $Address2,
                        'address_line3' => $Address3,
                        'pincode' => $pincode,));
                $req_dataN['lead_id'] = $lead_id;
                $req_dataN['customer_id'] = $this->db->query("select customer_id from master_customer where lead_id=".$lead_id)->row()->customer_id;
                $req_dataN['trace_id'] =$trace_id;
                $checkDetails['data']['customer_id']=encrypt_decrypt_password($req_dataN['customer_id'], 'E');
                $checkDetails['data']['lead_id']=encrypt_decrypt_password($req_dataN['lead_id'], 'E');
                $checkDetails['data']['trace_id']=encrypt_decrypt_password($req_dataN['trace_id'], 'E');
            }
            $req_dataN['plan_id'] = $plan_id;
            $req_dataN['nominee_name'] = $name;
            $req_dataN['nominee_relation'] = 1;
            // print_r($req_dataN['lead_id']);die;
            $checkDetailsMM = curlFunction(SERVICE_URL . '/customer_api/updateNomineeDetails', $req_dataN);
            insert_application_log($req_dataN['lead_id'], 'Proposer Details', json_encode($req_dataN),json_encode($checkDetailsMM), 123);
            //echo encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D');die;
            $data_marine = array(
                'lead_id' => encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D'),
                'mode_of_shipment' => $mode_of_shipment,
                'from_country' => $from_country,
                'to_country' => $to_country,
                'from_city' => $from_city,
                'to_city' => $to_city,
                'currency_type' => $currency_type,
                'cargo_value' => $cargo_value,
                'rate_of_exchange' => $rate_of_exchange,
                'date_of_shipment' => $date_of_shipment,
                'Bill_number' => $Bill_number,
                'Bill_date' => date('Y-m-d H:i:s',strtotime($Bill_date)),
                'credit_number' => $credit_number,
                'credit_description' => $credit_description,
                'place_of_issuence' => $place_of_issuence,
                'Invoice_number' => $Invoice_number,
                'Invoice_date' =>date('Y-m-d H:i:s',strtotime($Invoice_date)) ,
                'subject_matter_insured' => $subject_matter_insured,
                'marks_number' => $marks_number,
                'vessel_name' => $vessel_name,
                'Consignee_name' => $Consignee_name,
                'Consignee_add' => $Consignee_add,
                'Financier_name' => $Financier_name,
                'type_of_shipment' => $type_of_shipment,
                'Name_of_Transporter' => $Name_of_Transporter,
                'Number_and_kind_of_packages' => $Number_and_kind_of_packages,
                'Conveyance' => $Conveyance,
                'Packing' => $Packing,
                'Excess' => $Excess,
                'Basis_of_valuation' => $Basis_of_valuation,
            );
            $addMarineDetails = curlFunction(SERVICE_URL . '/customer_api/addMarineDetails', $data_marine);
            insert_application_log(encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D'), 'Marine Details', json_encode($data_marine),json_encode($addMarineDetails), 123);
            //   print_r($addMarineDetails);die;
            $req_data['policy_id'] = $policy_id;
            $req_data['lead_id'] = $checkDetails['data']['lead_id'];
            $req_data['customer_id'] = $checkDetails['data']['customer_id'];
            $req_data['trace_id'] = $checkDetails['data']['trace_id'];
            $req_data['plan_id'] = $plan_id;
            $req_data['cover'] = $SumInsured;
            $req_data['tenure'] = 1;
            $basis_id = $this->db->query("select si_premium_basis_id from master_policy_premium_basis_mapping where master_policy_id=" . $policy_id)->row()->si_premium_basis_id;
            //update creditor id
            $this->db->where(array('lead_id'=>encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D')));
            $this->db->update('lead_details',array('creditor_id'=>$creditor_id));
            $premium = (curlFunction(SERVICE_URL . '/customer_api/getPremiumGadgetapi', array(
                'policy_id' => $policy_id, 'sum_insured' => $SumInsured, "basis_id" => $basis_id,'subject_matter_insured'=>$subject_matter_insured,'policy_sub_type_id'=>$policy_sub_type_id,'type_of_shipment'=>$type_of_shipment)));
            $result = json_decode($premium);
            $Premium=$result->amount;

            $req_data['premium'] = $Premium;
            $req_data['total_premium'] = $Premium;
            $response = curlFunction(SERVICE_URL . '/customer_api/createPolicy_member_plan_gadget', $req_data);
            $proposal_data = curlFunction(SERVICE_URL . '/customer_api/create_proposalGadget', $req_data);
            insert_application_log(encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D'), 'Create Proposal', json_encode($req_data),json_encode($proposal_data), 123);
            if ($proposal_data == true) {
                $req_data1['lead_id'] = $checkDetails['data']['lead_id'];
                $PaymentMode = 4;
                $req_data1['PaymentMode'] = 4;
                $req_data1['TransactionNumber'] = $Invoice_number;
                $req_data1['TransactionRcvdDate'] = $Invoice_date;
                if ($PaymentMode == 4) {
                    if ($Api_type == 'modification') {
                        $cd_balance_remain = $balance + $last_premium;
                        $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                        $policy_cd_data['cd_utilised'] = $cd_utilised - $last_premium;
                        $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

                        $cd_data = array(
                            'type' => 1,
                            'amount' => $last_premium,
                            'lead_id' => $req_dataN['lead_id'],
                            'creditor_id' => $creditor_id,
                            'type_trans' =>"Policy Updated",
                        );
                        $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);

                    }
                    /* $get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
                    if (count($get_policy_det) > 0) {
                        $initial_cd = $get_policy_det['initial_cd'];
                        $cd_threshold = $get_policy_det['cd_threshold'];
                        $deposit = $get_policy_det['deposit'];
                        $cd_utilised = $get_policy_det['cd_utilised'];
                        $balance = ($initial_cd + $deposit) - $cd_utilised;
                    }
                    if ($balance > $cd_threshold && ($balance != '' && $balance != '0')) {
                        $cd_balance_remain = $balance - $Premium;
                        $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                        $policy_cd_data['cd_utilised'] = $cd_utilised + $Premium;
                        $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

                    } elseif ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
                        insert_application_log($lead_id, 'Payment Request', json_encode($policy_cd_data),json_encode("Not Sufficient CD Balance."), 123);
                         if($is_api == 1) {
                        echo "Not Sufficient CD Balance.";
                        exit;
                         }
                    } else {
                        $cd_balance_remain = $balance - $Premium;
                        $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                        $policy_cd_data['cd_utilised'] = $cd_utilised + $Premium;
                        $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

                     }*/

                    $response_cd= CheckCDThreshold($creditor_id,$plan_id,$Premium);
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
                    $cd_data = array(
                        'type' => 2,
                        'amount' => $Premium,
                        'lead_id' => $req_dataN['lead_id'],
                        'creditor_id' => $creditor_id,
                        'type_trans' =>"Policy Issuance",
                    );
                    $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);
                }
                //   var_dump($req_data1);die;
                $update_payment_status = curlFunction(SERVICE_URL . '/customer_api/updatePaymentgadget', $req_data1);
                $update_payment_status=json_decode($update_payment_status);
                insert_application_log(encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D'), 'Update Payment Details', json_encode($req_data1),json_encode($update_payment_status), 123);
                if ($update_payment_status->StatusCode == 200) {
                    $update_payment_status->COI_URL = 'http://fyntunecreditoruat.benefitz.in/GadgetInsurance/success_view/' . $checkDetails['data']['lead_id'];
                }else{

                    echo $update_payment_status->Message;
                    exit;
                }
                $get_proposal_number=$this->db->query("select group_concat(proposal_no) as proposal_no from api_proposal_response where lead_id=".$req_dataN['lead_id'])->row()->proposal_no;
                insert_application_log($req_dataN['lead_id'], 'Policy Created successfully', json_encode("Success"),json_encode("Lead ID:- " . $req_dataN['lead_id'] . "--" . "Trace ID:- " . $req_dataN['trace_id']. "--" . "Proposal Number:- " . $get_proposal_number), 123);
                if($is_api == 1) {
                $this->call_procedure_fun($req_dataN['lead_id'],$Api_type);
                    $send_mail=1;
                    $this->coidownloadNew(base64_encode($req_dataN['lead_id']),$send_mail);
                    //  echo "Lead ID:- " . $req_dataN['lead_id'] . "--" . "Trace ID:- " . $req_dataN['trace_id'] . "--" . "Proposal Number:- " . $get_proposal_number;
                    $data_json = array(
                        "Lead ID" => $req_dataN['lead_id'],
                        "Trace ID"=> $req_dataN['trace_id'],
                        "Proposal Number" => $get_proposal_number

                    );
                    echo json_encode($data_json);

                }
                // print_r($update_payment_status);die;
                // $makePayment=$this->policyIssuance($req_data,$ReceiptCreation,$PolicyCreationRequest);
            }

        }
        else if ($Api_type == 'cancellation') {
            $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
            $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
            //update policy status to Cancelled
            $where=array('lead_id'=>$lead_id,'trace_id'=>$trace_id);
            $this->db->where($where);
            $result=$this->db->update('proposal_policy',array('status'=>'Cancelled'));
            $Premium = $this->db->query("select premium from policy_member_plan_details where lead_id = '$lead_id'")->row()->premium;
            if($result){
                $cd_balance_remain = $balance + $Premium;
                $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                $policy_cd_data['cd_utilised'] = $cd_utilised - $Premium;
                $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");
                $cd_data = array(
                    'type' => 1,
                    'amount' => $Premium,
                    'lead_id' => $lead_id,
                    'creditor_id' => $creditor_id,
                    'type_trans' =>"Policy Cancelled",
                );
                $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);
                if($update_cd_data){
                    insert_application_log($lead_id, 'Cancel Requested', json_encode($cd_data),json_encode("Policy Cancelled Successfully."), 123);
                    if($is_api == 1) {
                    $this->call_procedure_fun($lead_id,'Cancelled');
                    echo "Policy Cancelled Successfully.";
                    exit;
                }
            }
            }


        }  else {
            if($is_api == 1) {
            echo "Invalid Api_type";
            exit;
        }
        }

    }
    function call_procedure_fun($lead_id,$status){
        $result=$this->db->query("call Marine_data_push(".$lead_id.",'".$status."')");
        if($result){
            //     $send_mail=$this->send_customer_data($lead_id,$status);
            $send_mail=$this->send_customer_data($lead_id,$status);
            //   print_r($send_mail);die;
        }
        return $result;
    }
    function send_customer_data($lead_id,$status){
        $query=$this->db->query("select * from marine_procedure_data where lead_id=".$lead_id." AND status='".$status."'");
        if($this->db->affected_rows() > 0){
            $row=$query->row();
            $this->load->library('excel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $array1 = array(
                "Api_type", "Invoice_number", "Proposal number", "plan_id", "policy_id", "salutation", "first_name", "middle_name",
                "last_name", "gender", "mobile_number", "pincode", "Address1", "Address2", "Address3", "email_id", "mode_of_shipment",
                "from_country","to_country","from_city","to_city","type_of_shipment","currency_type","cargo_value","cargo_value",
                "rate_of_exchange","date_of_shipment","Bill_number","Bill_date","credit_number","credit_description","place_of_issuence",
                "Invoice_date","subject_matter_insured","marks_number","vessel_name","Consignee_name","Consignee_add","Financier_name","SumInsured",
                "userId","COI number","COI url","Issuance date"
            );
            $cnt1 = 1;
            $char1 = 'A';
            foreach ($array1 as $a1) {
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
                $char1++;
            }
            $cnt = 2;
            $array2 = array(
                $row->status, $row->Invoice_number, $row->proposal_no, $row->plan_id, $row->policy_id, $row->salutation,
                $row->first_name, $row->middle_name, $row->last_name, $row->gender,$row->mobile_no,$row->pincode,$row->address_line1,
                $row->address_line1,$row->address_line2,$row->address_line3,$row->email_id,$row->mode_of_shipment,$row->from_country,
                $row->to_country, $row->from_city, $row->to_city, $row->type_of_shipment, $row->currency_type, $row->cargo_value,
                $row->rate_of_exchange,$row->date_of_shipment,$row->Bill_number,$row->Bill_date,$row->credit_number,$row->credit_description,
                $row->place_of_issuence, $row->Invoice_date, $row->subject_matter_insured, $row->marks_number, $row->vessel_name,
                $row->Consignee_name, $row->Consignee_add,$row->Financier_name,$row->cover,$row->createdby,'','',''
            );
            $char = 'A';
            foreach ($array2 as $k => $r) {
                if ($k == 2) {
                    $objPHPExcel->getActiveSheet()->getStyle($char . $cnt)
                        ->getNumberFormat()
                        ->setFormatCode('0');
                }
                $objPHPExcel->getActiveSheet()->SetCellValue($char . $cnt, $r);
                $char++;
            }

            $filename = "PolicyDetails_".$lead_id."_".date("Y-m-d-h-i-s") . ".xls";
            // echo $filename;die;
            ob_end_clean();
            header("Content-Disposition: attachment; filename=$filename");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save(str_replace(__FILE__, FCPATH."/assets/gadget/ExcelFiles/" . $filename, __FILE__));
            $f = FRONT_URL . '/assets/gadget/ExcelFiles/' . $filename;
            //chmod($f, 0777);

            $sftp = new SFTP('healthrenewalnotices.blob.core.windows.net');
            $sftp->login('healthrenewalnotices.nobrokers', 'BD//+EtWR6u2P4CeTHCzTpRgzPEq7nc5');
            $sftp->get($filename.'.filepart');
            $sftp->put('/nobrokers/ExcelSheets/'.date('dmY').'.xls',file_get_contents($f));
            /*  $mail = new PHPMailer(true);

              try {
                  //Server settings
                  //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                  $mail->isSMTP();                                            //Send using SMTP
                  $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                  $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                  $mail->SMTPSecure = "tls";
                  $mail->Username   = 'Cert.nobroker@allianceinsurance.in';                     //SMTP username
                  $mail->Password   = 'baibsegprcveftkv';                               //SMTP password
                  // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                  $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                  //Recipients
                  $mail->setFrom('Cert.nobroker@allianceinsurance.in', 'No Broker');
                  $mail->addAddress("poojalote123@gmail.com");
                  $mail->addCC("pooja.lote@fyntune.com");
                  $mail->addCC("afzal.khan@fyntune.com");
          //        $mail->addAttachment($f);


                  $body="
          Hello,<br>
  Please click on below URL to download file.<br>
  ".$f."<br>
  Thanks ,<br>
  Team No Broker<br>

  Please do not reply, this is system generated e-mail.
  ";
                  //Content
                  $mail->isHTML(true);                                  //Set email format to HTML
                  $mail->Subject = 'API data';
                  $mail->Body    = $body;
                  // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                  $mail->send();
                  insert_application_log($lead_id, 'Mail Sent', json_encode(array("file"=>$f)),json_encode("Mail Sent Successfully."), 123);
                  return 200;
              } catch (Exception $e) {
                  return $e;
              }*/
        }else{
            return 0;
        }
    }
    function CreateProposalapiBackup()
    {
        $api_data = json_decode(file_get_contents('php://input'), true);
        //   print_r($api_data);die;
        $_POST = $api_data; // print_r($api_data);die;
        $ClientCreation = $_POST['ClientCreation'];
        $compulsory=array(
            'token', 'Api_type','first_name','last_name','gender','mobile_number','pincode','Address1','Address2','Address3','email_id',
            'mode_of_shipment','from_country','to_country','from_city','to_city','type_of_shipment','cargo_value',
            'date_of_shipment','subject_matter_insured','Invoice_number','SumInsured'
        );
        foreach ($compulsory as $row){
            $value =trim($ClientCreation[$row]);
            if(empty($value) || trim($value) == null){
                echo $row." is Compulsory!";
                exit;
            }
        }
        $token = $ClientCreation['token'];
        $userId= $ClientCreation['userId'];
        $checkToken=$this->db->query("select emp_id,time  from token_table t where token= '".$token."' and type=2")->row();
        //print_r($checkToken);die;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $time=$checkToken->time;
            $minutes = (time() - strtotime($time)) / 60;
            if($minutes > 15){
                $response = array('success' => false, 'msg' => "Token Expired!");
                echo json_encode($response);
                exit;
            }
            $userId=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken->emp_id."'")->row()->employee_id;
        }

        $Api_type = strtolower($ClientCreation['Api_type']);
        $policy_id = $ClientCreation['policy_id'];
        $Invoice_number = $ClientCreation['Invoice_number'];
        if ($Api_type != 'issuance') {
            $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
            //  echo $this->db->last_query();die;
            $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
            //    echo $this->db->last_query();die;
            $ClientCreation['lead_id']=$lead_id;
            $ClientCreation['trace_id']=$trace_id;
            //check LeadID Exist or not
            $checkLeadExistorNot = curlFunction(SERVICE_URL . '/customer_api/checkLeadExistorNot', array('lead_id' => $lead_id, 'trace_id' => $trace_id));
            //var_dump($checkLeadExistorNot);die;
            if ($checkLeadExistorNot) {

            } else {
                echo "Customer Not found.";
                exit;
            }
            //  check alredy cancelled or not
            $checkLeadCancelledorNot = curlFunction(SERVICE_URL . '/customer_api/checkLeadCancelledorNot', array('lead_id' => $lead_id, 'trace_id' => $trace_id));
            //    print_r($checkLeadCancelledorNot);die;
            if ($checkLeadCancelledorNot != false) {
                if ($checkLeadCancelledorNot == 'Cancelled') {
                    echo "Policy is already Cancelled.";
                    exit;
                }
            } else {
                echo "Customer Not found.";
                exit;
            }
        }
        $get_policy_detC = $this->db->query("select creditor_id,policy_sub_type_id from master_policy where policy_id = '$policy_id'")->row_array();
        $creditor_id = $get_policy_detC['creditor_id'];
        $policy_sub_type_id = $get_policy_detC['policy_sub_type_id'];
        $get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
        if (count($get_policy_det) > 0) {
            $initial_cd = $get_policy_det['initial_cd'];
            $cd_threshold = $get_policy_det['cd_threshold'];
            $deposit = $get_policy_det['deposit'];
            $cd_utilised = $get_policy_det['cd_utilised'];
            $balance = ($initial_cd + $deposit) - $cd_utilised;
        }
        if ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
            echo "Not Sufficient CD Balance.";
            exit;
        }

        $mobile = $ClientCreation['mobile_number'];
        $email = $ClientCreation['email_id'];
        $gender = $ClientCreation['gender'];
        $name = $ClientCreation['first_name'] . " " . $ClientCreation['last_name'];
        $plan_id = $ClientCreation['plan_id'];
        $SumInsured = $ClientCreation['SumInsured'];
        $salutation = $ClientCreation['salutation'];
        $Premium = $ClientCreation['Premium'];
        $pincode = $ClientCreation['pincode'];
        $Address1 = $ClientCreation['Address1'];
        $Address2 = $ClientCreation['Address2'];
        $Address3 = $ClientCreation['Address3'];
        $mode_of_shipment = $ClientCreation['mode_of_shipment'];
        $from_country = $ClientCreation['from_country'];
        $to_country = $ClientCreation['to_country'];
        $from_city = $ClientCreation['from_city'];
        $to_city = $ClientCreation['to_city'];
        $currency_type = $ClientCreation['currency_type'];
        $cargo_value = $ClientCreation['cargo_value'];
        $rate_of_exchange = $ClientCreation['rate_of_exchange'];
        $date_of_shipment = $ClientCreation['date_of_shipment'];
        $Bill_number = $ClientCreation['Bill_number'];
        $Bill_date = $ClientCreation['Bill_date'];
        $credit_number = $ClientCreation['credit_number'];
        $credit_description = $ClientCreation['credit_description'];
        $place_of_issuence = $ClientCreation['place_of_issuence'];
        $Invoice_date = $ClientCreation['Invoice_date'];
        $subject_matter_insured = $ClientCreation['subject_matter_insured'];
        $marks_number = $ClientCreation['marks_number'];
        $vessel_name = $ClientCreation['vessel_name'];
        $Consignee_name = $ClientCreation['Consignee_name'];
        $Consignee_add = $ClientCreation['Consignee_add'];
        $Financier_name = $ClientCreation['Financier_name'];
        $type_of_shipment = $ClientCreation['type_of_shipment'];
        // $userId = $ClientCreation['userId'];
        // echo $Api_type;die();
        if ($Api_type == 'issuance' || $Api_type == 'modification') {
            if ($Api_type == 'issuance'){
                $this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
                if($this->db->affected_rows() > 0){
                    echo "This Invoice Number is already exist. Please send request for modification.";
                    exit;
                }
                $checkDetails = curlFunction(SERVICE_URL . '/customer_api/createLead', array(
                    'mobile' => $mobile,
                    'email' => $email,
                    'gender' => $gender,
                    'name' => $name,
                    'Address1' => $Address1,
                    'Address2' => $Address2,
                    'Address3' => $Address3,
                    'pincode' => $pincode,
                    'userId' => $userId,
                    'is_api_lead' => 1,
                ));
                $checkDetails = json_decode($checkDetails, true);
                $req_dataN['lead_id'] = encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D');
                $req_dataN['customer_id'] = encrypt_decrypt_password($checkDetails['data']['customer_id'], 'D');
                $req_dataN['trace_id'] = encrypt_decrypt_password($checkDetails['data']['trace_id'], 'D');
            }else{
                //get previous premium amount
                $last_premium = $this->db->query("select premium from policy_member_plan_details where lead_id = '$lead_id'")->row()->premium;
                $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
                $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
                //$lead_id = $ClientCreation['lead_id'];
                //$trace_id = $ClientCreation['trace_id'];
                $where=array('lead_id'=>$lead_id,'trace_id'=>$trace_id);
                $where2=array('lead_id'=>$lead_id);
                $this->db->where($where);
                $result=$this->db->update('lead_details',array('mobile_no' => $mobile,
                    'trace_id' => $trace_id,
                    'user_activity' => 1,
                    'createdon' => date('Y-m-d H:i:s'),
                    'email_id' => $email,));
                $this->db->where($where2);
                $result2=$this->db->update('master_customer',
                    array('mobile_no' => $mobile,
                        'salutation' => $salutation,
                        'first_name' => $ClientCreation['first_name'],
                        'last_name' => $ClientCreation['last_name'],
                        'gender' => $gender,
                        'full_name' => $name,
                        'email_id' => $email,
                        'createdon' => date('Y-m-d H:i:s'),
                        'address_line1' => $Address1,
                        'address_line2' => $Address2,
                        'address_line3' => $Address3,
                        'pincode' => $pincode,));
                $req_dataN['lead_id'] = $lead_id;
                $req_dataN['customer_id'] = $this->db->query("select customer_id from master_customer where lead_id=".$lead_id)->row()->customer_id;
                $req_dataN['trace_id'] =$trace_id;
                $checkDetails['data']['customer_id']=encrypt_decrypt_password($req_dataN['customer_id'], 'E');
                $checkDetails['data']['lead_id']=encrypt_decrypt_password($req_dataN['lead_id'], 'E');
                $checkDetails['data']['trace_id']=encrypt_decrypt_password($req_dataN['trace_id'], 'E');
            }
            $req_dataN['plan_id'] = $plan_id;
            $req_dataN['nominee_name'] = $name;
            $req_dataN['nominee_relation'] = 1;
            $checkDetailsMM = curlFunction(SERVICE_URL . '/customer_api/updateNomineeDetails', $req_dataN);
            //echo encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D');die;
            $data_marine = array(
                'lead_id' => encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D'),
                'mode_of_shipment' => $mode_of_shipment,
                'from_country' => $from_country,
                'to_country' => $to_country,
                'from_city' => $from_city,
                'to_city' => $to_city,
                'currency_type' => $currency_type,
                'cargo_value' => $cargo_value,
                'rate_of_exchange' => $rate_of_exchange,
                'date_of_shipment' => $date_of_shipment,
                'Bill_number' => $Bill_number,
                'Bill_date' => date('Y-m-d H:i:s',strtotime($Bill_date)),
                'credit_number' => $credit_number,
                'credit_description' => $credit_description,
                'place_of_issuence' => $place_of_issuence,
                'Invoice_number' => $Invoice_number,
                'Invoice_date' =>date('Y-m-d H:i:s',strtotime($Invoice_date)) ,
                'subject_matter_insured' => $subject_matter_insured,
                'marks_number' => $marks_number,
                'vessel_name' => $vessel_name,
                'Consignee_name' => $Consignee_name,
                'Consignee_add' => $Consignee_add,
                'Financier_name' => $Financier_name,
                'type_of_shipment' => $type_of_shipment,
            );
            $addMarineDetails = curlFunction(SERVICE_URL . '/customer_api/addMarineDetails', $data_marine);
            //   print_r($addMarineDetails);die;
            $req_data['policy_id'] = $policy_id;
            $req_data['lead_id'] = $checkDetails['data']['lead_id'];
            $req_data['customer_id'] = $checkDetails['data']['customer_id'];
            $req_data['trace_id'] = $checkDetails['data']['trace_id'];
            $req_data['plan_id'] = $plan_id;
            $req_data['cover'] = $SumInsured;
            $req_data['tenure'] = 1;
            $basis_id = $this->db->query("select si_premium_basis_id from master_policy_premium_basis_mapping where master_policy_id=" . $policy_id)->row()->si_premium_basis_id;
            //update creditor id
            $this->db->where(array('lead_id'=>encrypt_decrypt_password($checkDetails['data']['lead_id'], 'D')));
            $this->db->update('lead_details',array('creditor_id'=>$creditor_id));
            $premium = (curlFunction(SERVICE_URL . '/customer_api/getPremiumGadgetapi', array(
                'policy_id' => $policy_id, 'sum_insured' => $SumInsured, "basis_id" => $basis_id,'subject_matter_insured'=>$subject_matter_insured,'policy_sub_type_id'=>$policy_sub_type_id,'type_of_shipment'=>$type_of_shipment)));
            $result = json_decode($premium);
            $Premium=$result->amount;

            $req_data['premium'] = $Premium;
            $req_data['total_premium'] = $Premium;
            $response = curlFunction(SERVICE_URL . '/customer_api/createPolicy_member_plan_gadget', $req_data);
            $proposal_data = curlFunction(SERVICE_URL . '/customer_api/create_proposalGadget', $req_data);
            if ($proposal_data == true) {
                $req_data1['lead_id'] = $checkDetails['data']['lead_id'];
                $PaymentMode = 4;
                $req_data1['PaymentMode'] = 4;
                $req_data1['TransactionNumber'] = $Invoice_number;
                $req_data1['TransactionRcvdDate'] = $Invoice_date;
                if ($PaymentMode == 4) {
                    if ($Api_type == 'modification') {
                        $cd_balance_remain = $balance + $last_premium;
                        $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                        $policy_cd_data['cd_utilised'] = $cd_utilised - $last_premium;
                        $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");
                        $cd_data = array(
                            'type' => 1,
                            'amount' => $last_premium,
                            'lead_id' => $req_dataN['lead_id'],
                            'creditor_id' => $creditor_id,
                            'type_trans' =>"Policy Updated",
                        );
                        $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);

                    }
                    $get_policy_det = $this->db->query("select initial_cd,cd_threshold,cd_utilised,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
                    if (count($get_policy_det) > 0) {
                        $initial_cd = $get_policy_det['initial_cd'];
                        $cd_threshold = $get_policy_det['cd_threshold'];
                        $deposit = $get_policy_det['deposit'];
                        $cd_utilised = $get_policy_det['cd_utilised'];
                        $balance = ($initial_cd + $deposit) - $cd_utilised;
                    }
                    if ($balance > $cd_threshold && ($balance != '' && $balance != '0')) {
                        $cd_balance_remain = $balance - $Premium;
                        $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                        $policy_cd_data['cd_utilised'] = $cd_utilised + $Premium;
                        $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

                    } elseif ($balance < $cd_threshold && ($balance != '' && $balance != '0')) {
                        echo "Not Sufficient CD Balance.";
                        exit;
                    } else {
                        $cd_balance_remain = $balance - $Premium;
                        $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                        $policy_cd_data['cd_utilised'] = $cd_utilised + $Premium;
                        $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");

                    }
                    $cd_data = array(
                        'type' => 2,
                        'amount' => $Premium,
                        'lead_id' => $req_dataN['lead_id'],
                        'creditor_id' => $creditor_id,
                        'type_trans' =>"Policy Issuance",
                    );
                    $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);
                }
                //   var_dump($req_data1);die;
                $update_payment_status = curlFunction(SERVICE_URL . '/customer_api/updatePaymentgadget', $req_data1);
                /*if ($update_payment_status['StatusCode'] == 200) {
                    $update_payment_status['COI_URL'] = 'http://fyntunecreditoruat.benefitz.in/GadgetInsurance/success_view/' . $checkDetails['data']['lead_id'];
                }*/
                $get_proposal_number=$this->db->query("select group_concat(proposal_no) as proposal_no from api_proposal_response where lead_id=".$req_dataN['lead_id'])->row()->proposal_no;
                echo "Lead ID:- " . $req_dataN['lead_id'] . "--" . "Trace ID:- " . $req_dataN['trace_id']. "--" . "Proposal Number:- " . $get_proposal_number;
                // print_r($update_payment_status);die;
                // $makePayment=$this->policyIssuance($req_data,$ReceiptCreation,$PolicyCreationRequest);
            }

        }
        else if ($Api_type == 'cancellation') {
            $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
            $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
            //update policy status to Cancelled
            $where=array('lead_id'=>$lead_id,'trace_id'=>$trace_id);
            $this->db->where($where);
            $result=$this->db->update('proposal_policy',array('status'=>'Cancelled'));
            $Premium = $this->db->query("select premium from policy_member_plan_details where lead_id = '$lead_id'")->row()->premium;
            if($result){
                $cd_balance_remain = $balance + $Premium;
                $policy_cd_data['cd_balance_remain'] = $cd_balance_remain;
                $policy_cd_data['cd_utilised'] = $cd_utilised - $Premium;
                $policy_insert_data = $this->updateRecord('master_ceditors', $policy_cd_data, "creditor_id='" . $creditor_id . "'");
                $cd_data = array(
                    'type' => 1,
                    'amount' => $Premium,
                    'lead_id' => $lead_id,
                    'creditor_id' => $creditor_id,
                    'type_trans' =>"Policy Cancelled",
                );
                $update_cd_data = curlFunction(SERVICE_URL . '/customer_api/CD_credit_debit_entry', $cd_data);
                if($update_cd_data){
                    echo "Policy Cancelled Successfully.";
                    exit;
                }
            }


        }  else {
            echo "Invalid Api_type";
            exit;
        }

    }
    function updateRecord($tbl_name, $datar, $condition)
    {
        //$this -> db -> where($comp_col, $eid);
        $this->db->where("($condition)");
        $this->db->update($tbl_name, $datar);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return true;
        }
    }
    function SendFTPRequest()
    {
        /* ini_set('display_errors', 1);
         ini_set('display_startup_errors', 1);
         error_reporting(E_ALL);*/
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel2 = new PHPExcel();
        $objPHPExcel2->setActiveSheetIndex(0);
        $objPHPExcel3 = new PHPExcel();
        $objPHPExcel3->setActiveSheetIndex(0);
        $objPHPExcel4 = new PHPExcel();
        $objPHPExcel4->setActiveSheetIndex(0);
        $yesterday= date('Y-m-d 23:20:00',strtotime("-1 days"));
        $query = $this->db->query("select *,
(select policy_number from master_policy mp where mp.policy_id=mpd.policy_id) as master_policy_number ,
(select certificate_number from api_proposal_response apr where apr.lead_id=mpd.lead_id order by id desc limit 1) as certificate_number 
 from marine_procedure_data mpd where (created_at) >= '".$yesterday."' AND created_at <= now()")->result();

        $array1 = array(
            "Master Policy No.", "Type of transit(Intracity/Intercity)", "Invoice No", "Date of declaration", "Customer name",
            "Customer Email Id", "From City", "To City",
            "Cargo Value - INR", "Shipment start date", "Transporter details", "Subject Matter insured", "Plan Id", "Status","Certificate Number"
        );
        $cnt1 = 1;
        $char1 = 'A';
        foreach ($array1 as $a1) {
            $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
            $objPHPExcel2->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
            $objPHPExcel3->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
            $objPHPExcel3->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
            $char1++;
        }
        $cnt = 2;
        $cnt1 = 2;
        $cnt2 = 2;
        $cnt3 = 2;


        foreach ($query as $key => $row) {
            if($row->subject_matter_insured =='Household Goods' && $row->type_of_shipment=="Inter"){
                $arrayHouseoldInter = array(
                    $row->master_policy_number, $row->type_of_shipment, $row->Invoice_number,
                    $row->Bill_date, //"Date of declaration"
                    $row->first_name." ".$row->last_name,
                    $row->email_id, $row->from_city,
                    $row->to_city, $row->cargo_value, $row->date_of_shipment,
                    $row->vessel_name, //"Transporter details"
                    $row->subject_matter_insured,$row->plan_id,$row->status
            );
            $char = 'A';
                foreach ($arrayHouseoldInter as $k => $r) {

                $objPHPExcel->getActiveSheet()->SetCellValue($char . $cnt, $r);
                $char++;
            }
            $cnt++;
            }
            if($row->subject_matter_insured =='Household Goods' && $row->type_of_shipment=="Intra"){
                $arrayHouseoldIntra = array(
                    $row->master_policy_number, $row->type_of_shipment, $row->Invoice_number,
                    $row->Bill_date, //"Date of declaration"
                    $row->first_name." ".$row->last_name,
                    $row->email_id, $row->from_city,
                    $row->to_city, $row->cargo_value, $row->date_of_shipment,
                    $row->vessel_name, //"Transporter details"
                    $row->subject_matter_insured,$row->plan_id,$row->status
                );
                $char = 'A';
                foreach ($arrayHouseoldIntra as $k => $r) {

                    $objPHPExcel2->getActiveSheet()->SetCellValue($char . $cnt1, $r);
                    $char++;
                }
                $cnt1++;
            }
            if($row->subject_matter_insured =='Car' && $row->type_of_shipment=="Inter"){
                $arrayHouseoldIntra = array(
                    $row->master_policy_number, $row->type_of_shipment, $row->Invoice_number,
                    $row->Bill_date, //"Date of declaration"
                    $row->first_name." ".$row->last_name,
                    $row->email_id, $row->from_city,
                    $row->to_city, $row->cargo_value, $row->date_of_shipment,
                    $row->vessel_name, //"Transporter details"
                    $row->subject_matter_insured,$row->plan_id,$row->status
                );
                $char = 'A';
                foreach ($arrayHouseoldIntra as $k => $r) {

                    $objPHPExcel3->getActiveSheet()->SetCellValue($char . $cnt2, $r);
                    $char++;
                }
                $cnt2++;
            }
            if($row->subject_matter_insured =='Car' && $row->type_of_shipment=="Intra"){
                $arrayHouseoldIntra = array(
                    $row->master_policy_number, $row->type_of_shipment, $row->Invoice_number,
                    $row->Bill_date, //"Date of declaration"
                    $row->first_name." ".$row->last_name,
                    $row->email_id, $row->from_city,
                    $row->to_city, $row->cargo_value, $row->date_of_shipment,
                    $row->vessel_name, //"Transporter details"
                    $row->subject_matter_insured,$row->plan_id,$row->status,$row->certificate_number
                );
                $char = 'A';
                foreach ($arrayHouseoldIntra as $k => $r) {

                    $objPHPExcel4->getActiveSheet()->SetCellValue($char . $cnt3, $r);
                    $char++;
                }
                $cnt3++;
            }


        }
        $filename = "PolicyDetails" . date("Y-m-dH:i:s") . ".xls";
        // echo $filename;die;
        ob_end_clean();
        $today=date('Ymd');
        if (!file_exists(FCPATH."/assets/gadget/ExcelFiles/".$today)) {
            mkdir(FCPATH."/assets/gadget/ExcelFiles/".$today, 0777, true);
        }
        $fileNameArray=array('policyDetails_Inter_HouseholdGoods.xls',
            'policyDetails_Intra_HouseholdGoods.xls'
        ,'policyDetails_Inter_Car.xls'
        ,'policyDetails_Intra_Car.xls'
        );
        $objectExcel=array(
            $objPHPExcel,$objPHPExcel2,$objPHPExcel3,$objPHPExcel4
        );
        $filesindex=array(
            'HouseHoldGoods_Inter',
            'HouseHoldGoods_Intra',
            'Car_Inter',
            'Car_Intra',
        );
        $response_array=array();
        for($i=0;$i<=3;$i++){
            $filename = $fileNameArray[$i];
        header("Content-Disposition: attachment; filename=$filename");
            $objWriter = PHPExcel_IOFactory::createWriter($objectExcel[$i], 'Excel2007');
            $objWriter->save(str_replace(__FILE__, FCPATH."/assets/gadget/ExcelFiles/".$today."/" . $filename, __FILE__));
            $response_array[$filesindex[$i]]=FRONT_URL."/assets/gadget/ExcelFiles/".$today."/" . $filename;
        }
        $this->SendFTPmail($response_array);
        echo json_encode($response_array);
        exit;
    }

    function SendFTPmail($response_array){

        //chmod($f, 0777);


        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Username   = 'Cert.nobroker@allianceinsurance.in';                     //SMTP username
            $mail->Password   = 'baibsegprcveftkv';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('Cert.nobroker@allianceinsurance.in', 'No Broker');
            $mail->addAddress("marinedeclaration@icicilombard.com");
            $mail->addAddress("vijay.dwibhashi@icicilombard.com");
            $mail->addAddress("riya.ruchikar@icicilombard.com");

            $mail->addBCC("poojalote123@gmail.com");
            $mail->addBCC("afzal.khan@fyntune.com");
            //        $mail->addAttachment($f);
            $h='';
            foreach($response_array as $key=>$file){
                //     $mail->addAttachment($file,$key);
                $h .='<br><a href="'.$file.'">click here to download '.$key.'</a><br>';
            }
            $body = '<html><body>';
            $body .="
        Hello,<br>
Please click on below URL to download file.<br>
";

            $body.= $h;

            $body.= "<br>Thanks ,<br>
Team No Broker<br>

Please do not reply, this is system generated e-mail.
";

            $body.= '</body></html>';


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'API data';
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

            return 200;
        } catch (Exception $e) {
            print_r($e);die;
            return $e;
        }

    }

    function getInvoiceDetails()
    {
        $api_data = json_decode(file_get_contents('php://input'), true);
        //   print_r($api_data);die;
        $_POST = $api_data;
        $Invoice_number = $_POST['Invoice_number'];
        $token = $_POST['token'];
        $checkToken=$this->db->query("select emp_id,time  from token_table t where token= '".$token."' and type=2")->row();
        //print_r($checkToken);die;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $time=$checkToken->time;
            $minutes = (time() - strtotime($time)) / 60;
            if($minutes > 15){
                $response = array('success' => false, 'msg' => "Token Expired!");
                echo json_encode($response);
                exit;
            }
            $userId=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken->emp_id."'")->row()->employee_id;
        }
        $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
        $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
        $plan_id=$this->db->query("select plan_id from proposal_details where lead_id=".$lead_id)->row()->plan_id;
        $query = $this->db->query("select date(pd.created_at) as created_at,mc.salutation,mc.first_name,mc.last_name,
mc.email_id,pd.lead_id,pd.trace_id,ppd.premium,ppd.cover,pp.proposal_no,pp.status,
(select certificate_number from api_proposal_response apr where apr.lead_id=mc.lead_id) as certificate_number from master_customer mc
join proposal_details pd on pd.lead_id=mc.lead_id and pd.plan_id=".$plan_id." and date(pd.created_at)=date(now())
join policy_member_plan_details ppd on ppd.lead_id=mc.lead_id
join proposal_policy pp on pp.lead_id=mc.lead_id where mc.lead_id=" . $lead_id . " AND pd.trace_id=" . $trace_id)->row();
        //   echo $this->db->last_query();die;
        if ($this->db->affected_rows() > 0) {
            $certificate_number=$query->certificate_number;
            if(!empty($certificate_number)){
                $response['message']="Certificate Generated!";
                $response['certificate_number']=$certificate_number;
            }else{
                $response['message']="Certificate Not Generated!";
                $response['certificate_number']='';
            }
            echo json_encode($response);
            die;
        } else {
            echo json_encode(201);
            die;
        }

    }
    function getCoi()
    {
        $api_data = json_decode(file_get_contents('php://input'), true);
        //   print_r($api_data);die;
        $_POST = $api_data;
        $Invoice_number = $_POST['Invoice_number'];
        $Lan_number = $_POST['Lan_number'];
        $token = $_POST['token'];
        if(isset($Invoice_number)){
            $compulsory=array(
                'token', 'Invoice_number'
            );
            $type=2;
        }else if(isset($Lan_number)){
            $compulsory=array(
                'token', 'Lan_number'
            );
            $type=1;
        }


        foreach ($compulsory as $row){
            $value =trim($_POST[$row]);
            if(empty($value) || trim($value) == null){
                echo $row." is Compulsory!";
                exit;
            }
        }
        $checkToken=$this->db->query("select emp_id,time  from token_table t where token= '".$token."' and type=".$type)->row();
        //print_r($checkToken);die;
        if(empty($checkToken) || is_null($checkToken)){
            $response = array('success' => false, 'msg' => "Unauthorized Access!");
            echo json_encode($response);
            exit;
        }else{
            $time=$checkToken->time;
            $minutes = (time() - strtotime($time)) / 60;
            if($minutes > 15){
                $response = array('success' => false, 'msg' => "Token Expired!");
                echo json_encode($response);
                exit;
            }
            $userId=$this->db->query("select employee_id from master_employee e where e.user_name='".$checkToken->emp_id."'")->row()->employee_id;
        }
        if(isset($Invoice_number)){
            $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$Invoice_number."'")->row()->lead_id;
            $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
            $plan_id=$this->db->query("select plan_id from proposal_details where lead_id=".$lead_id)->row()->plan_id;
            $coi_type=$this->db->query("select coi_type from master_plan where plan_id=".$plan_id)->row()->coi_type;
            $query = $this->db->query("select date(pd.created_at) as created_at,mc.salutation,mc.first_name,mc.last_name,
mc.email_id,pd.lead_id,pd.trace_id,ppd.premium,ppd.cover,pp.proposal_no,pp.status,
(select certificate_number from api_proposal_response apr where apr.lead_id=mc.lead_id) as certificate_number,
(select COI_url from api_proposal_response apr where apr.lead_id=mc.lead_id) as COI_url 
from master_customer mc
join proposal_details pd on pd.lead_id=mc.lead_id and pd.plan_id=".$plan_id." 
join policy_member_plan_details ppd on ppd.lead_id=mc.lead_id
join proposal_policy pp on pp.lead_id=mc.lead_id where mc.lead_id=" . $lead_id . " AND pd.trace_id=" . $trace_id)->row();
            if ($this->db->affected_rows() > 0) {
                $certificate_number=$query->certificate_number;
                if($coi_type == 1){
                    $COI_url=FRONT_URL.$query->COI_url;
                }else{
                $COI_url=$query->COI_url;
                }


                if(!empty($certificate_number)){
                    $mail_array=array($query->email_id,$query->first_name,$COI_url,$query->proposal_no);
                    $sendMail=$this->sendMail($mail_array);
                    //  print_r($sendMail);die;
                    $response['message']="Certificate Generated!";
                    $response['certificate_number']=$certificate_number;
                    $response['certificate_url']=$COI_url;
                }else{
                    $response['message']="Certificate Not Generated!";
                    $response['certificate_number']='';
                }
                echo json_encode($response);
                die;
            } else {
                echo json_encode(201);
                die;
            }
        }else{
            $lead_id=$this->db->query("select lead_id from lead_details where lan_id='".$Lan_number."'")->row()->lead_id;
            if(empty($lead_id)){
                $response = array('success' => false, 'msg' => "Lan Id not exist.");
                echo json_encode($response);
                exit;
            }
            $query=$this->db->query("select certificate_number,COI_url from api_proposal_response where lead_id=".$lead_id." group by certificate_number")->result();
            $final_response=array();
            foreach ($query as $row){
                $response=array();
                if(empty($row->certificate_number)){
                    $response['Certificate Number']='';
                    $response['COI_url']='';
                    //echo "Certificate Number -  | COI_url - \n" ;
                }else{
                    $response['Certificate Number']=$row->certificate_number;
                    $response['COI_url']= FRONT_URL .$row->COI_url;
                    // echo "Certificate Number - ".$row->certificate_number." | COI_url - ".FRONT_URL .$row->COI_url."\n" ;
                }
                array_push($final_response,$response);

            }
            echo json_encode($final_response);
            exit;
        }


        //   echo $this->db->last_query();die;


    }
    function SearchDetails()
    {

        $lead_id = $this->input->post('lead_id');
        $trace_id = $this->input->post('trace_id');
        $plan_id=$this->db->query("select plan_id from proposal_details where lead_id=".$lead_id)->row()->plan_id;
        $query = $this->db->query("select date(pd.created_at) as created_at,mc.salutation,mc.first_name,mc.last_name,
mc.email_id,pd.lead_id,pd.trace_id,ppd.premium,ppd.cover,pp.proposal_no,pp.status,
(select certificate_number from api_proposal_response apr where apr.lead_id=mc.lead_id) as certificate_number from master_customer mc
join proposal_details pd on pd.lead_id=mc.lead_id and pd.plan_id=".$plan_id." and date(pd.created_at)=date(now())
join policy_member_plan_details ppd on ppd.lead_id=mc.lead_id
join proposal_policy pp on pp.lead_id=mc.lead_id where mc.lead_id=" . $lead_id . " AND pd.trace_id=" . $trace_id)->row();
        // echo $this->db->last_query();die;
        if ($this->db->affected_rows() > 0) {
            echo json_encode($query);
            die;
        } else {
            echo json_encode(201);
            die;
        }

    }
    function fetchfilesftp(){

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $sftp = new SFTP('healthrenewalnotices.blob.core.windows.net');
        $sftp->login('healthrenewalnotices.nobrokers', 'BD//+EtWR6u2P4CeTHCzTpRgzPEq7nc5');
        // echo  $sftp->get('103052023.xlsx'); die;
        $data= $sftp->nlist();
        // print_r($data);die;
        $dir='ExcelSheets';
        foreach($sftp->rawlist($dir) as $filename => $attr) {
            // print_r($attr);die;
            if ($attr['type'] == NET_SFTP_TYPE_REGULAR) {
                $filename=$attr['filename'];
                $arr=explode(".",$filename);
                $extension=$arr[1];
                $date=$arr[0];
                if($date == date('dmY') && $extension=='xlsx'){
                    $sftp->chdir($dir);
                    $sftp->get($filename, FCPATH."/assets/marine_excel/".$filename);
                    $this->load->library('excel');

                    $inputFileName = FCPATH."/assets/marine_excel/".$filename;

                    try {
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                    } catch (Exception $e) {
                        die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' .
                            $e->getMessage());
                    }
                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();
                    $result_array=array();
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                            null, true, false);
                        $rowData=$rowData[0];
                        if($row != 1){
                            $invoice_number=$rowData[0];
                            $_POST['created_by']=1;
                            $_POST['invoice_number']=$invoice_number;
                            $lead_id=$this->db->query("select lead_id from marine_customer_info where Invoice_number='".$invoice_number."'");
                            if($this->db->affected_rows() > 0){
                                $lead_id= $lead_id->row()->lead_id;
                                $trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
                                $ClientCreation['lead_id']=$lead_id;
                                $ClientCreation['trace_id']=$trace_id;
                                //check LeadID Exist or not
                                $checkLeadExistorNot = curlFunction(SERVICE_URL . '/customer_api/checkLeadExistorNot', array('lead_id' => $lead_id, 'trace_id' => $trace_id));
                                //var_dump($checkLeadExistorNot);die;
                                if ($checkLeadExistorNot) {
                                    $proposal_number=$rowData[2];
                                    $proposer_name=$rowData[3];
                                    $coi_number=$rowData[18];
                                    $coi_url=$rowData[19];
                                    $customer_mail_id=$rowData[16];
                                    $dir2='Coi Document PDF';
                                    $f_name=$coi_number.'.pdf';
                                    $sftp->chdir('..');
                                    $sftp->chdir($dir2);
                                    $sftp->get($f_name, FCPATH."/assets/marine_coi/".$f_name);
                                    $coi_url="/assets/marine_coi/".$f_name;
                                    $check_already_updated=$this->db->query("select lead_id from api_proposal_response where 
                                    lead_id=".$lead_id." AND proposal_no='".$proposal_number."' AND coi_mail_sent=1 AND certificate_number !=''");
                                    if($this->db->affected_rows() > 0){
                                        $result='Already Updated';
                                        //insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
                                    }else{
                                        $update=false;
                                        //  $mail_array=array($customer_mail_id,$proposer_name,$coi_url,$proposal_number);
                                        // $sendMail=$this->sendMail($mail_array);
                                        // if($sendMail == 200){
                                        $where=array('lead_id'=>$lead_id,'proposal_no'=>$proposal_number);
                                        $this->db->where($where);
                                        $update=$this->db->update('api_proposal_response',array('COI_url'=>$coi_url,'certificate_number'=>$coi_number,'coi_mail_sent'=>1));
                                        // }
                                        if($update == true){
                                            $result='Updated';
                                            insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
                                        }else{
                                            $result='Not Updated';
                                            insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
                                        }
                                    }
                                }
                            } else {
                                $result="Customer Not found.";
                                //  insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
                            }
                        }
                    }


                    echo "Check Application Logs.";
                }

            }else{
                echo "Something went wrong.";
                exit;
            }

        }

    }
    function sendMail($mail_array){



        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Username   = 'Cert.nobroker@allianceinsurance.in';                     //SMTP username
            $mail->Password   = 'baibsegprcveftkv';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('Cert.nobroker@allianceinsurance.in', 'No Broker');
            ///    $mail->addAddress('poojalote123@gmail.com', 'Pooja Lote');     //Add a recipient
            //    $mail->addAddress('pooja.lote@fyntune.com', 'Pooja Fyntune');     //Add a recipient
            $mail->addAddress($mail_array[0]);
            $mail->addCC("shifting@nobroker.in");
            $url=$mail_array[2];
            //Add a recipient

            /*     $body="<p>Hello,</p>
         <p>Please click on below given link to get your certificate of Issuance.</p>
         <p>".$url."</p>
         <p>Regards,<br>
         Fyntune Team.
         </p>
         ";*/
            $body = '<html><body>';

            $body .="Dear ".$mail_array[1].",

<br><br>Thank you for purchasing your online policy from us. This certificate serves as proof of your coverage and contains important information related to your policy.

<br><br>Attached to this email, you will find the insurance certificate download link here <a href='".$url."'>Link</a>. 

<br><br>Thank you once again for choosing our services. We look forward to serving you and providing continued support.";
            $body.= "<br><br>Warm Regards ,<br>
Team Elephant<br>

Please do not reply, this is system generated e-mail.
";
            $body.= '</body></html>';

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Certificate Issuance';
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return 200;
        } catch (Exception $e) {

            //  print_r($e);die;
            return 201;
        }
    }
}
