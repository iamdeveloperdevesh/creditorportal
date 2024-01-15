<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
require(APPPATH.'libraries/vendor/autoload.php');
require(APPPATH.'libraries/lib/ConvertApi/autoload.php');
use \ConvertApi\ConvertApi;
class Telelead extends CI_Controller
{
	function __construct()
	{//echo 123;die;
		parent::__construct();
		//checklogin();
	//	$this->RolePermission = getRolePermissions();
        $this->db = $this->load->database('telesales_fyntune',true);
        $this->load->model('telelead_m', 'Lead_m');
        /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        $telesession = $this->session->userdata('telesales_session');
        $this->agent_id = encrypt_decrypt_password($telesession['agent_id'], 'D');
        $this->admin = $telesession['is_admin'];
    }

	function index()
	{
		$result = array();
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];
		$this->load->view('template/header_tele.php');
		$this->load->view('telelead/index',$data);
		$this->load->view('template/footer_tele.php');
	}
    public function maker_checker_view()
    {
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];
        $this->load->view('template/header_tele.php');
        $this->load->view('telelead/maker_checker_av_view',$data);
        $this->load->view('template/footer_tele.php');

    }
    function  output ( $message )
    {
        //echo 123;die;
        if  ( php_sapi_name ( )  ==  'cli' )
            echo ( $message ) ;
        else
            echo ( nl2br ( $message ) ) ;
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
        print array_sum($final_sum);
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

            $pattern = '/total\s\d{1,9}\.\d{1,9}|total\s\d{1,9}|total\s?[^0-9]\s[A-Za-z0-9]\w+\s\d{1,9}\.\d{1,9}|(?<=total\s[^0-9]).\d{1,9}.\d{1,9}|(?<=total\s[A-Za-z0-9]\w\s)\d{1,9}[^0-9]\d{2}|(?<=grand total[^0-9]\s)\d{1,9}.\d{1,9}[^0-9]\d{2}+/i';
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
    function uploadToApi($target_file,$target_file_name){
	  //  echo 123;die;
        extract($_POST);
        $arr = [];

        $ext = pathinfo($target_file_name, PATHINFO_EXTENSION);


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
                    CURLOPT_POSTFIELDS => array('file' => new CURLFILE($target_file), 'OCREngine' => 5),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: multipart/form-data',
                        'apikey: helloworld'
                    ),
                ));

                $responses = curl_exec($curl);

                curl_close($curl);
                $response = json_decode($responses, true);
                //print_r($responses);
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
                echo json_encode($arr);

            }


    }
    function addLead()
    {
        extract($_POST);
        $excel_file_name= $_FILES["file"]["name"];
        $excel_path = APPPATH."third_party/SI_PROCESS_UPLOAD/". $excel_file_name;
        $excel_path = APPPATH."third_party/SI_PROCESS_UPLOAD/". $excel_file_name;

        if (!is_dir(APPPATH."third_party/SI_PROCESS_UPLOAD/")) {
            mkdir(APPPATH."third_party/SI_PROCESS_UPLOAD/");
        }
       // chmod(APPPATH.'third_party',777);
        if (move_uploaded_file($_FILES["file"]["tmp_name"],$excel_path)) {
           $this->uploadToApi($excel_path,$excel_file_name);

        }




die;

        $response = $this->Lead_m->insert_lead();
        echo json_encode($response);
    }
    function tls_view_lead(){
        $result = array();
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];
        $this->load->view('template/header_tele.php');
        $this->load->view('telelead/view_lead',$data);
        $this->load->view('template/footer_tele.php');
    }
    public function get_datatable_ajax()
    {
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        $_SESSION['telesales_session']['is_maker_checker']="No";
        $is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];
       // $_POST['is_maker_checker']=$_SESSION['telesales_session']['is_maker_checker'];
        $fetch_data = $this->Lead_m->make_datatables();
        //  echo $this->db->last_query();exit;
        // print_r($fetch_data);exit;

        $data = array();
        $i = 0;
        $status_array_display = [];

        $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

        //print_pre($status_array_display);exit;
        foreach ($fetch_data as $row) {
            // print_pre($row);exit;
            if (strtolower($row->status) == 'payment pending') {
                if ($row->sms_trigger_status == 0) {
                    $row->status = 'payment link not triggered';
                }
            }
            if ($row->product_id == 'T01' || $row->product_id == 'T03' || $row->product_id == 'R06') {
                $get_proposal_certificate = $this->get_proposal_certificate($row->emp_id);
                //print_pre($get_proposal_certificate);exit;
                $row->proposal_no = $get_proposal_certificate['proposal_no'];
                $row->policy_no = $get_proposal_certificate['cert_no'];
                $row->premium = $this->get_total_premium($row->emp_id)['premium'];

            }
            $agentName = $_SESSION['telesales_session']['agent_name'];

            if ($row->product_id == 'T01') {
                $product_name_display = 'Health pro';
            } else if ($row->product_id == 'T03') {
                $product_name_display = 'Tele Health Pro Infinity';
            } else {
                $product_name_display = $row->Plan;
            }


            // 03-02-2022 - SVK005
            if ($row->product_id == 'T01') {
                $product_name_display = 'Health pro';
            } else if ($row->product_id == 'T03') {
                $product_name_display = 'Health Pro Infinity';
            } else if ($row->product_id == "R06") {
                $product_name_display = 'Group Activ Health';
            }

            if ($row->is_makerchecker_journey == "yes") {
                if ($row->product_id == "R06") {
                    $product_name_display = "Group Activ Health";
                }
                if ($row->product_id == "T03") {
                    $product_name_display = "Health Pro Infinity";
                }
                if ($row->product_id == "T01") {
                    $product_name_display = "Health Pro";
                }
            }
            $attempt_connect = $this->get_attempt_connect($row->emp_id);
            $get_latest_disposition = $this->disposition($row->emp_id);
            //echo $this->db->last_query();exit;
            $sub_array = array();
            $i++;
            $sub_array[] = $i;
            $sub_array[] = $row->lead_id;
            $sub_array[] = strtoupper($row->emp_firstname . ' ' . $row->last_name);
            $sub_array[] = $row->imd_code;
            $sub_array[] = $row->axis_process;

            $sub_array[] = $row->proposal_no;
            $sub_array[] = $product_name_display;
            $sub_array[] = $row->premium;
            $net_premium = $this->Lead_m->get_net_premium_tele($row->lead_id);
            if ((int)$net_premium == $net_premium) {

            } else {
                $net_premium = round($net_premium, 2);
            }
            $sub_array[] = ($net_premium != 0) ? $net_premium : '';

            $sub_array[] = $status_array_display[strtolower($row->status)];
            // $sub_array[] = $row->modified_date;
            $sub_array[] = $row->created_at;
            $sub_array[] = $row->mob_no;
            $sub_array[] = $row->saksham_id;
            $sub_array[] = $row->policy_no;
            //ankita added this filed junk dedupe logic
            $sub_array[] = $row->policy_issuance_date;
            $sub_array[] = $row->lead_id;
            $sub_array[] = $attempt_connect['Attempt'];
            $sub_array[] = $attempt_connect['Connect'];
            $sub_array[] = $get_latest_disposition['Dispositions'];
            $sub_array[] = $get_latest_disposition['Sub-dispositions'];
            $sub_array[] = $get_latest_disposition['remarks'];
            $sub_array[] = $this->check_lead_lost($row->emp_id);
            //echo $this->check_lead_lost($row->emp_id);	exit;
            if ($this->admin == '1') {

                //upendra - maker/checker - 30-07-2021
                if ($row->is_makerchecker_journey == "yes") {
                    $sub_array[] = $this->get_maker_checker_av($row->picked_do_by, 'name');
                    $sub_array[] = $this->get_maker_checker_av($row->picked_do_by, 'id');
                    // $sub_array[] = "Maker/Checker Lead";
                } else {
                    //$sub_array[] = strtoupper($row->agent_name);
                    $sub_array[] = "";
                    $sub_array[] = $row->av_code;
                    // $sub_array[] = "";
                }

                // $sub_array[] = strtoupper($row->agent_name);
                // $sub_array[] = $row->av_code;
                // $sub_array[] = '';
                $sub_array[] = $get_latest_disposition['date'];
                $sub_array[] = $row->axis_location;
                //for xl download
                $sub_array[] = $row->emp_firstname;
                $sub_array[] = (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d H:i:s", strtotime($row->txndate)) : '';
                $sub_array[] = (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '';
                $sub_array[] = $row->sum_insured;
                $sub_array[] = $row->bdate;
                $sub_array[] = strtoupper($row->tl_name);
                $sub_array[] = strtoupper($row->am_name);
                $sub_array[] = strtoupper($row->om_name);
                $sub_array[] = $row->axis_lob;
                //$sub_array[] = $row->axis_location;
                $sub_array[] = $row->axis_vendor;
                $sub_array[] = $row->emp_state;
                $sub_array[] = $row->emp_city;
                $sub_array[] = $row->emp_pincode;
                //$sub_array[] = $row->mob_no;
                $sub_array[] = $row->businessType;
                $sub_array[] = ($row->Chronic == 'NO') ? 'NO' : 'YES';
                $sub_array[] = $row->email;
                $sub_array[] = strtoupper($row->base_caller_name);
                $sub_array[] = strtoupper($row->base_caller_id);
                $sub_array[] = $row->preferred_contact_date;
                $sub_array[] = $row->preferred_contact_time;

                //	$sub_array[] = $row->imd_code;
                $sub_array[] = $row->rec_manager_code;
                $sub_array[] = $row->quotation_no;
                $sub_array[] = '';
                $sub_array[] = '';
                $sub_array[] = ''; //$sub_array[] = $row->TxRefNo;



                if ($row->is_makerchecker_journey == "yes") {
                    //$row->status = 'payment link not triggered';
                    if ($row->status == 'Success') {

                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>';
                    } else {

                        if ((strtolower($row->status) == 'payment link not triggered')) {
                            $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>
<button type="button" onclick = BackToDo("' . ($row->emp_id) . '","' . encrypt_decrypt_password($row->lead_id) . '","' . base64_encode($agentName) . '") name="status"  class="btn btn-cta"
                        >Back To Do</button>';
                        } else {
                            $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>

';
                        }

                        /*  <button type="button" onclick = BackToDo("' . ($row->emp_id) . '","' . encrypt_decrypt_password($row->lead_id) . '","'.base64_encode($agentName).'") name="status"  class="btn btn-cta"
                          >Back To Do</button>*/

                    }

                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                } else {

                    //$sub_array[] = "testing123";
                    if (strtolower($row->status) == 'rejected') {
                        $sub_array[] = '<button type="button" disabled onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    }

                    if ($row->status == 'Success') {
                        $sub_array[] = '<button type="button" id = "' . $row->policy_no . '" onclick = call_function("' . $row->policy_no . '") class="btn btn-cta"><i class="fa fa-download"></i>Download</button>';
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>';
                    } else {
                        $sub_array[] = '';
                        /*if((strtolower($row->status) == 'payment link not triggered')){
                            $sub_array[] = '<button type="button" onclick = BackToDo("' . ($row->emp_id) . '","' . encrypt_decrypt_password($row->lead_id) . '","'.base64_encode($agentName).'") name="status"  class="btn btn-cta"
                        >Back To Do</button>';
                        }*/
                    }
                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';

                }


            } else {

                if ($row->is_makerchecker_journey == "yes") {


                    $disabled_btn = "";
                    if ($row->makerchecker == "checker") {
                        $disabled_btn = "disabled";
                    }

                    $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta" ' . $disabled_btn . '>Action</button>';

                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                } else if (strtolower($row->status) == 'proposal not created') {
                    $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Action</button>';
                    $sub_array[] = '';
                    //$sub_array[] = '';
                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';

                } else {
                    if ($row->status == 'Success'||$row->status=='Policy Issued') {

                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Action</button>';
                    }

                    //		$sub_array[] = '';
                    // $check_time = strtr($row->txndate, '/', '-');
                    // $check_time = date("Y-m-d H:i:s",strtotime($check_time));

                    // if($row->status == 'Success' && (strtotime(date("Y-m-d H:i:s")) - strtotime($check_time)) > 3600){


                    if ((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')) {
                        $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';
                        //$sub_array[] = '<button type="button" onclick = payment_call("' . encrypt_decrypt_password($row->lead_id) . '") name="status"  class="btn btn-cta">PAYMENT</button>';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else {
                        $sub_array[] = '';
                        //$sub_array[] = '';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    }

                    if ($row->status == 'Success') {
                        $sub_array[] = '<button type="button" onclick = call_function_sendmail("' . encrypt_decrypt_password($row->emp_id) . '") class="btn btn-cta">Re-trigger COI</button>';
                    } else {
                        $sub_array[] = '';
                    }

                    //			 $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';


                }


            }
            //$sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
            //if ($this->admin != '1') {
            //     $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
            //      }
            $data[] = $sub_array;
        }
        //print_pre($data);exit;
        //print_pre($data);
        if (empty($data)) {
            $data = 0;
        }

        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Lead_m->get_all_data(),
            "recordsFiltered" => $this->Lead_m->get_filtered_data(),
            "data" => $data,
        );


        //print_pre($output);exit;
        echo json_encode($output);
    }
    public function view_lead_maker_checker_all()
    {
        $result = array();
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];
        $this->load->view('template/header_tele.php');
        $this->load->view('telelead/view_lead_maker_checker_all',$data);
        $this->load->view('template/footer_tele.php');
    }
    public function get_datatable_maker_ajax()
    {
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

        //print_pre($this->input->post());
        // exit;
        $fetch_data = $this->Lead_m->makerchecker_datatables();

        // print_r($fetch_data);exit;

        $data = array();
        $i = 0;
        $status_array_display = [];

        $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

        //print_pre($status_array_display);exit;
        foreach ($fetch_data as $row) {
            // print_pre($row);exit;
            if (strtolower($row->status) == 'payment pending') {
                if ($row->sms_trigger_status == 0) {
                    $row->status = 'payment link not triggered';
                }
            }
            if ($row->product_id == 'T01' || $row->product_id == 'T03' || $row->product_id == 'R06') {
                $get_proposal_certificate = $this->get_proposal_certificate($row->emp_id);
                //print_pre($get_proposal_certificate);exit;
                $row->proposal_no = $get_proposal_certificate['proposal_no'];
                $row->policy_no = $get_proposal_certificate['cert_no'];
                $row->premium = $this->get_total_premium($row->emp_id)['premium'];

            }

            if ($row->product_id == 'T01') {
                $product_name_display = 'Health pro';
            } else if ($row->product_id == 'T03') {
                $product_name_display = 'Tele Health Pro Infinity';
            } else {
                $product_name_display = $row->Plan;
            }


            $attempt_connect = $this->get_attempt_connect($row->emp_id);
            $get_latest_disposition = $this->disposition($row->emp_id);
            $get_latest_disposition_agent = $this->disposition_agent($row->emp_id);

            $sub_array = array();
            $i++;
            $sub_array[] = $i;
            $sub_array[] = $row->lead_id;
            $sub_array[] = $row->proposal_no;

            $sub_array[] = strtoupper($row->emp_firstname . ' ' . $row->last_name);

            $sub_array[] = $row->created_at;
            $sub_array[] = $get_latest_disposition['date'];
            // $sub_array[] = $row->created_at;


            // $sub_array[] = $row->imd_code;
            // $sub_array[] = $row->axis_process;
            // $sub_array[] = $product_name_display;

            // $sub_array[] = strtoupper($row->base_caller_id);
            $sub_array[] = strtoupper($row->base_caller_name);

            $sub_array[] = $get_latest_disposition_agent['agent_name'];

            // $sub_array[] = $get_latest_disposition['agent_name'];

            if ($row->product_id == "R06") {
                $product_name_display = "Group Activ Health";
            }
            if ($row->product_id == "T03") {
                $product_name_display = "Health Pro Infinity";
            }
            if ($row->product_id == "T01") {
                $product_name_display = "Health Pro";
            }

            $sub_array[] = $product_name_display;

            $sub_array[] = $row->premium;

            $sub_array[] = $row->axis_location;
            $sub_array[] = $row->axis_lob;

            $latest_disposition = $get_latest_disposition['Dispositions'];;

            $sub_array[] = $latest_disposition;
            $sub_array[] = $get_latest_disposition['Sub-dispositions'];
            $sub_array[] = $status_array_display[strtolower($row->status)];

            $sub_array[] = $row->policy_issuance_date;
            $sub_array[] = $row->makercheckerremark;


            // $sub_array[] = $row->modified_date;
            if ($row->lead_flag == 'regenerated' || $get_latest_disposition['Dispositions'] == 'Payment done') {
                $sub_array[] = '<button type="button"  name="status"  class="btn btn-cta" disabled>View Details</button>';
            } else {
                if ((strtolower($row->status) == 'Policy Issued')){
                    $sub_array[] = '<button type="button" onclick = summary_page("' . $row->product_id . '","' . encrypt_decrypt_password($row->lead_id) . '","' . $row->emp_id . '","' . $this->agent_id . '",0) name="status"  class="btn btn-cta">View Details</button>';
                }else{
                    $sub_array[] = '<button type="button" onclick = summary_page("' . $row->product_id . '","' . encrypt_decrypt_password($row->lead_id) . '","' . $row->emp_id . '","' . $this->agent_id . '",1) name="status"  class="btn btn-cta">Action</button>';
                }

                //$sub_array[]="<a href='/tele_summary?product_id=T01&leadid=dlc3b3ZGdGloZlNockpMZUduQ0Y0Zz09'>View Details</a>";
            }

            $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
            if (((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')) && $latest_disposition == "Payment pending") {
                $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';

            } else {
                $sub_array[] = '';
            }


            // $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';


            $data[] = $sub_array;
        }
        //print_pre($data);exit;
        if (empty($data)) {
            $data = 0;
        }
        //exit;
        //print_pre($data);exit;
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Lead_m->get_all_maker_data(),
            "recordsFiltered" => $this->Lead_m->get_filtered_maker_data(),
            "data" => $data,
        );


        //print_pre($output);exit;
        echo json_encode($output);
    }
    public function get_proposal_certificate($emp_id_cert)
    {
        $lead_lost_check = $this->db->select("(GROUP_CONCAT(distinct(p.proposal_no))) as 'proposal_no',GROUP_CONCAT(api.certificate_number) as 'cert_no',api.created_date as 'policy_issuance_date'")
            ->from('proposal p')
            ->join('api_proposal_response api', 'p.id = api.proposal_id', 'left')
            //->where('ed.emp_id',$empid)
            ->where('p.emp_id', $emp_id_cert)
            ->get()
            ->row_array();
        return $lead_lost_check;
    }
    public function get_total_premium($emp_id_premium)
    {

        $lead_lost_check = $this->db->select("sum(p.premium) as 'premium'")
            ->from('proposal p')
            //->where('ed.emp_id',$empid)
            ->where('p.emp_id', $emp_id_premium)
            ->get()
            ->row_array();
        return $lead_lost_check;
    }
    public function get_maker_checker_av($av_code, $return_type)
    {

        $get_details = $this->db->select('agent_id,agent_name')->from('tls_agent_mst')
            ->where('id', $av_code)
            ->get()->row_array();

        if ($return_type == 'name') {
            return $get_details['agent_name'];
        }
        if ($return_type == 'id') {
            return $get_details['agent_id'];
        }

    }

    public function get_attempt_connect($emp_id_payment)
    {

        $data = $this->db->select('sum(dm.Attempt) as Attempt,sum(dm.Connect)as Connect')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('ed.emp_id', $emp_id_payment)
            ->group_by('ed.emp_id')
            ->get()
            ->row_array();
        return $data;

    }
    public function disposition($emp_id_disposition)
    {

        $data = $this->db->select('dm.Dispositions,dm.Sub-dispositions,ed.date,ed.agent_name,ed.remarks')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('ed.emp_id', $emp_id_disposition)
            //	->where('ed.agent_name IS NOT NULL')
            ->order_by('ed.date', 'desc')
            ->get()
            ->row_array();
        //echo $this->db->last_query();
        //print_pre($data);
        return $data;

    }
    public function check_lead_lost($empid)
    {
        $lead_lost_check = $this->db->select('*')->from('employee_disposition ed,disposition_master dm')
            ->where('ed.disposition_id = dm.id')
            //->where('ed.emp_id',$empid)
            ->where('ed.emp_id', $empid)
            ->order_by('ed.date', 'desc')
            ->get()
            ->result_array();
        //print_pre($lead_lost_check);exit;
        foreach ($lead_lost_check as $key => $value) {

            if ($value['Dispositions'] == 'Lead Lapsed') {
                $serach = $key + 1;
                return $lead_lost_check[$serach]['Dispositions'];
            }
        }
        return '';
    }

    public function get_audit_trail()
    {
        /*
        $emp_id_audit = $this->input->post('emp_id',true);
        $emp_id_audit = encrypt_decrypt_password($emp_id_audit,'D');
        $data  = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
         ->where('emp_id',$emp_id_audit)
              ->group_by('dm.Dispositions')
         ->order_by('ed.id')
        ->get()
        ->result_array();
        $lead_creation  = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
        ->where('employee_details.assigned_to = tls_agent_mst.id')
        ->where('emp_id',$emp_id_audit)
        ->get()->row_array();
        $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
        $lead_creation_merge[0]['disposition_id'] = 123;
        $lead_creation_merge[0]['Dispositions'] = 'LEAD';
        $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
        $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
        $data = array_merge($lead_creation_merge,$data);
        //echo $this->db->last_query();
        echo json_encode($data);
        */


        //upendra - maker/checker - 30-07-2021

        //print_pre($_SESSION['telesales_session']);exit;
        //print_pre($emp_id_audit);exit;

        $emp_id_audit = $this->input->post('emp_id', true);
        $emp_id_audit = encrypt_decrypt_password($emp_id_audit, 'D');

        //print_pre($emp_id_audit);exit;

        $is_maker_checker = $this->db->select('is_makerchecker_journey')->from('employee_details')->where('emp_id', $emp_id_audit)->get()->row_array();

        //print_pre($is_maker_checker);exit;


        //if(isset($_SESSION['telesales_session']['is_maker_checker'])){
        //  $is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];

        if ($is_maker_checker['is_makerchecker_journey'] == "yes") {

            $emp_id_audit = $this->input->post('emp_id', true);
            $emp_id_audit = encrypt_decrypt_password($emp_id_audit, 'D');
            $data = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id', $emp_id_audit)
                //->group_by('dm.Dispositions')
                ->order_by('ed.id')
                ->get()
                ->result_array();
            // $lead_creation  = $this->db->select('employee_details.created_at,employee_details.agent_name')->from('employee_details')
            // ->where('emp_id',$emp_id_audit)
            // ->get()->row_array();

            $lead_creation = $this->db->select('employee_details.created_at,tls_base_agent_tbl.base_agent_name')->from('employee_details,tls_base_agent_tbl')
                ->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
                ->where('emp_id', $emp_id_audit)
                ->get()->row_array();
            //	echo $this->db->last_query();exit;
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['base_agent_name'];
            $lead_creation_merge[0]['type'] = 'DO';

            $data = array_merge($lead_creation_merge, $data);
            echo json_encode($data);

            //}

        } else {
            $emp_id_audit = $this->input->post('emp_id', true);
            $emp_id_audit = encrypt_decrypt_password($emp_id_audit, 'D');
            $data = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id', $emp_id_audit)
                //->group_by('dm.Dispositions')
                ->order_by('ed.id')
                ->get()
                ->result_array();
            $lead_creation = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
                ->where('employee_details.assigned_to = tls_agent_mst.id')
                ->where('emp_id', $emp_id_audit)
                ->get()->row_array();
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
            $data = array_merge($lead_creation_merge, $data);
            //echo $this->db->last_query();
            echo json_encode($data);
        }
    }
    public function tele_get_all_agents()
    {
        $output = $this->Lead_m->get_all_agents();
        echo $output;
    }
    public function tls_reassign_agent()
    {
        $response['status'] = false;
        $response['message'] = 'Something Went Wrong';
        $emp_id = $this->input->post('emp_id', true);
        $agent_id = $this->input->post('agent_id', true);
        if (!empty($emp_id) && !empty($agent_id)) {
            $this->db->where('emp_id', encrypt_decrypt_password($emp_id, 'D'));
            $this->db->update('employee_details', ["assigned_to" => encrypt_decrypt_password($agent_id, 'D')]);
            $response['agent_name'] = $this->db->select("agent_name")->from("tls_agent_mst")->where("id", encrypt_decrypt_password($agent_id, 'D'))->get()->row_array();
            $response['status'] = true;
            $response['message'] = 'Agent Reassigned Successfully';

        }
        echo json_encode($response);

    }

    public function tele_av_upload(){
        $data['lob'] = $this->db->select('axis_lob')
            ->from('tls_axis_lob l')
            ->order_by('l.axis_lob','asc')
            ->get()
            ->result_array();

        $data['location'] = $this->db->select('axis_location')
            ->from('tls_axis_location l')
            ->order_by('l.axis_location','asc')
            ->get()
            ->result_array();

        $data['axis_process'] = $this->db->distinct()->select('axis_process')
            ->from('tls_axis_lob l')
            ->order_by('l.axis_lob','asc')
            ->get()
            ->result_array();
        $this->load->view('template/header_tele.php');
        $this->load->view('telelead/av_upload',$data);
        $this->load->view('template/footer_tele.php');
    }
    public function create_av_insert()
    {
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        //    var_dump($this->input->post(null,true));die;
            $create_agent_post = $this->input->post(null,true);
            if($create_agent_post != null || !empty($create_agent_post))
                $edit = $create_agent_post['edit'];
            $role_types = $this->input->post('role_types',true);
            if($role_types == 'Admin')
            {
                $is_admin = 1;
            }
            else
            {
                $is_admin = 0;
            }
           /* if($create_agent_post['login'] == 'Y')
            {
                $password = encrypt_decrypt_password($create_agent_post['agentCode']);
            }*/
            /*else
            {
                $password = '';
            }*/
            $agent_code = $create_agent_post['agentCode'];





            $agent_name = $create_agent_post['agentName'];
            $tl_id      = $create_agent_post['tlId'];
            $tl_name	= $create_agent_post['tlName'];
            $am_id	    = $create_agent_post['amId'];
            $am_name	= $create_agent_post['amName'];
            $om_id	    = $create_agent_post['omId'];
            $om_name	= $create_agent_post['omName'];
            array_push($create_agent_post['create_module'], 36, 37,55);
            //array_push($create_agent_post['create_module'], 34, 35);uat
            $module     = implode(',',$create_agent_post['create_module']);
            // print_r($module);exit;
            $is_login   = $create_agent_post['login'];
            $lob   = $create_agent_post['lob'];
            $axis_process = $create_agent_post['axis_process'];
            $center   = $create_agent_post['center'];
            $vendor   = $create_agent_post['vendor'];

            $license_from   = $create_agent_post['license_from'];
            $license_to   = $create_agent_post['license_to'];


            $data = array(

                'agent_id'   => $agent_code,
                'agent_name' => $agent_name,
                'tl_name'    => $tl_name,
                'tl_emp_id'  => $tl_id,
                'am_name'    => $am_name,
                'am_emp_id'  => $am_id,
                'om_name'    => $om_name,
                'om_emp_id'    => $om_id,
                'module_access_rights' => $module,
                'password' => encrypt_decrypt_password($agent_code),
                'is_admin' => $is_admin,
                'center' => $center,
                'lob'=>$lob,
                'axis_process' => $axis_process,
                'status' => 'Active',
                'license_from'=>date('Y-m-d',strtotime($license_from)),
                'license_to'=>date('Y-m-d',strtotime($license_to))

            );
            $success_data = $this->Lead_m->insert_av($data,$edit);

            print_r(json_encode($success_data));


    }
    public function get_av_datatable_ajax()
    {
        //ajax call

        $emp_id=$this->agent_id;
        $query=$this->db->query('select password_change_access from tls_agent_mst where id='.$emp_id)->row();
        $password_change_access=$query->password_change_access;
        // $fetch_data = $this->agent_m->make_datatables($table = 'table1',$select_column = "select_column1",$agent_id = "id",$agent_name = "agent_name");
        $this->db->select('*');
        $this->db->from('tls_agent_mst');
        $this->db->order_by('id','DESC');

        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))
        {

            $this->db->group_start();
            $this->db->like('agent_id', $_POST["search"]["value"]);
            $this->db->or_like('agent_name', $_POST["search"]["value"]);
            $this->db->or_like("axis_process", $_POST["search"]["value"]);
            $this->db->or_like("lob", $_POST["search"]["value"]);
            $this->db->or_like("center", $_POST["search"]["value"]);
            $this->db->group_end();
        }

        $query=$this->db->get()->result();

        $fetch_data = $query;

        $data = array();
        $i = 0;
        foreach($fetch_data as $row)
        {

            $sub_array = array();
            $i++;
            $sub_array[] = $i;
            $sub_array[] = $row->agent_id;
            $sub_array[] = $row->agent_name;
            // $sub_array[] = $row->lob;
            $sub_array[] = $row->center;
            $sub_array[] = $row->axis_process;

            $sub_array[] = $row->license_from?date('d-M-y',strtotime($row->license_from)):'';
            $sub_array[] = $row->license_to?date('d-M-y',strtotime($row->license_to)):'';
            $sub_array[] = '<button type="button" name="autit_agent_mst" class="btn btn-cta btn-primary" onclick = "aduit_agent_mst(this);" id="'.$row->agent_id.'" data-toggle="modal" data-target="#myModalaudit_mst">Audit</button>';
            $sub_array[] = $row->status;
            if($password_change_access == 1){
                $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>
 <button type="button" name="edit" class="btn btn-nope btn-xs" id="'.$row->id.'" onclick = "ResetPasswordModal(this);"><i class="ti-user"></i></button>
';

            }else{
                $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>';
            }

            $data[] = $sub_array;
        }
        $output = array(
            "draw"            => intval($_POST["draw"]),
            "recordsTotal"    => $this->Lead_m->get_all_data($table = "table1"),
            "recordsFiltered" => 0,//$this->agent_m->get_filtered_data($table = 'table1',$select_column = "select_column1",$agent_id = "id",$agent_name = "agent_name"),
            "data"            => $data
        );
        echo json_encode($output);
    }
    public function edit_av_data()
    {
        $edit_agent_post = $this->input->post(null,true);
        if($edit_agent_post != null || !empty($edit_agent_post))

            $agent_id = $edit_agent_post['edit'];
        $edit_data =  $this->db->select("id,agent_id,module_access_rights,agent_name,tl_name,tl_emp_id,am_emp_id,am_name,om_emp_id,om_name,is_admin,lob,center,axis_process,license_from,license_to")
            ->from("tls_agent_mst AS tam")
            ->where("tam.id",$agent_id)
            ->get()
            ->result_array();
        print_r(json_encode($edit_data));
    }
    public function get_role_module()
    {
        $role_access_module =  $this->db->select("role_module_id,acc_module_name")
            ->from("role_access_module AS ram")
            ->where("ram.product = 'R06'")
            ->get()
            ->result_array();


        echo json_encode($role_access_module);
    }
    public function delete_av_data()
    {
        $delete_agent_post = $this->input->post(null,true);
        if($delete_agent_post != null || !empty($delete_agent_post))
            $del_id = $delete_agent_post['delete_data'];
        $data = array(
            'status' => 'Inactive'
        );
        $this->db->where('id',$del_id);
        $result =	$this->db->update('tls_agent_mst',$data);
        if($result == 1)
        {
            $success_data = ["status" => true, "message" => "Sucessfully Deleted"];
        }
        else
        {
            $success_data = ["status" => false, "message" => "Something Went Wrong"];
        }
        print_r(json_encode($success_data));
    }
    function tele_doupload_fyntune(){
        $data=array();
        $this->load->view('template/header_tele.php');
        $this->load->view('telelead/do_upload',$data);
        $this->load->view('template/footer_tele.php');
    }
    public function get_do_datatable_ajax()
    {
        //ajax call
        $emp_id=$this->agent_id;
        $query=$this->db->query('select password_change_access from tls_agent_mst where id='.$emp_id)->row();
        $password_change_access=$query->password_change_access;
        $this->db->select('*');
        $this->db->from('tls_master_do');
        $this->db->order_by('created_at','DESC');

        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))
        {

            $this->db->group_start();
            $this->db->like('do_id', $_POST["search"]["value"]);
            $this->db->or_like('do_name', $_POST["search"]["value"]);
            $this->db->or_like("tl_id", $_POST["search"]["value"]);
            $this->db->or_like("tl_name", $_POST["search"]["value"]);
            $this->db->or_like("center", $_POST["search"]["value"]);
            $this->db->group_end();
        }

        $query=$this->db->get()->result();

        $fetch_data = $query;
        $data = array();
        $i = 0;
        foreach($fetch_data as $row)
        {

            $sub_array = array();
            $i++;
            // $sub_array[] = $i;
            $sub_array[] = $row->do_id;
            $sub_array[] = $row->do_name;
            $sub_array[] = $row->tl_id;
            $sub_array[] = $row->tl_name;
            $sub_array[] = $row->center;
            $sub_array[] = $row->status;
            //$sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>';
          /*  if($password_change_access == 1) {
                $sub_array[] = ' <button type="button" name="edit" class="btn btn-nope btn-xs" id="' . $row->id . '" onclick = "ResetPasswordModal(this);"><i class="ti-user"></i></button>';
            }else{
                $sub_array[] = ' <button type="button" name="edit" class="btn btn-nope btn-xs" disabled><i class="ti-user"></i></button>';
            }*/
            $data[] = $sub_array;
        }

        $this->db->select("*");
        $this->db->from('tls_master_do');
        $query = $this->db->get();
        $query->num_rows();
        $recordsTotal = $query->num_rows();

        $output = array(
            "draw"            => intval($_POST["draw"]),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data"            => $data
        );
        echo json_encode($output);
    }
    public function upload_av()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $file_name = $_FILES['filetoUpload']['tmp_name'];
        $file_names = $_FILES['filetoUpload']['name'];
        if($file_names != '' || $file_names != null || !empty($file_names))
            $ext = pathinfo($file_names, PATHINFO_EXTENSION);
        $allowed_types = ['xlsx','xls','csv'];

        if(!in_array($ext,$allowed_types))
        {
            $error = array('errorCode' => '1', 'msg' => 'File type not allowwed');
            echo json_encode($error);
        }

        $inputFileName = $file_name;

        $this->load->library("excel1");
        $config1   =  [
            'filename'    => $inputFileName,              // prove any custom name here
            'use_sheet_name_as_key' => false,               // this will consider every first index from an associative array as main headings to the table
            'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
        ];
        $sheetdata = [];
        $y = [];
        $vs = [];
        $sheetdatas = [];
        $data = [];
        $sheetdata = Excel1::import($inputFileName, $config1);

        if(!is_array($sheetdata))
        {
            $get_data = array('errorCode' => '1', 'msg' => $sheetdata);

            $flag = 0;
        }

        $temp = 0;
        $agentType=$this->input->post('agent_type');
        if($this->input->post('agent_type') == 1){
            // echo '234454';exit;
            if(!empty($sheetdata))
            {

                $arr = array();
                $y = array_keys($sheetdata);
                foreach($y as $value)
                {
                    foreach($sheetdata[$value] as $val)
                    {

                        if(!empty($val))
                        {
                            $sheetdatas = array_filter($val);
                            if(!empty($sheetdatas))
                            {

                                if($sheetdatas['A']== 'AV Id')
                                {
                                    continue;
                                }
                                if($sheetdatas['A']!= 'AV Id')
                                {
                                    $get_data = array('errorCode' => '1', 'msg' => "Please Upload Propoer Excel Sheet");
                                }
                                $temp = 1;
                                $flag = 1;
                                $check_agent_code = $sheetdatas['A'];
                                $agent_name = $sheetdatas['B'];

                                $check_agent_code = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['A']);
                                $agent_name = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['B']);
                                $sheetdatas['E'] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['E']);

                                $center = trim($sheetdatas['C']);
                                $center = trim(strtolower($center));
                                $center = ucfirst($center);

                                $status = $sheetdatas['E'];
                                $status = trim(strtolower($status));
                                $status = ucfirst($status);


                                $lfrom=trim($sheetdatas['F']);
                                $lto=trim($sheetdatas['G']);

                                // $lfrom="30/12/2019";

                                $lfrom=str_replace('/','-',$lfrom);

                                // echo $lfrom;exit;

                                $lto=str_replace('/','-',$lto);

                                $licensefrom=date('Y-m-d',strtotime($lfrom));
                                $licenseto=date('Y-m-d',strtotime($lto));

                                // echo $licensefrom;exit;

                                $checklob=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$center' ")->row_array();



                                if($status != "Active"){
                                    $status = "Inactive";
                                }

                                if($check_agent_code == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Agent code is mandatory, COL A-'.$get_row.' Not Inserted in database.';
                                    array_push($arr,$msg);

                                }
                                else if($agent_name == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'agent name is mandatory, COL B-'.$get_row.' Not Inserted in database.';
                                    array_push($arr,$msg);

                                }

                                else if($center == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = ' Center is mandatory,AV ID '.trim($sheetdatas['A']). ' is not inserted. ';
                                    array_push($arr,$msg);

                                }

                                else if($lfrom == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = ' License Expiry From Date,AV ID '.trim($sheetdatas['A']). ' is not inserted. ';
                                    array_push($arr,$msg);

                                }
                                else if($lto == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = ' License Expiry To Date,AV ID '.trim($sheetdatas['A']). ' is not inserted. ';
                                    array_push($arr,$msg);

                                }

                                // else if($status == '')
                                // {
                                // 	$get_row = $i+1;
                                // 	$msg  = 'Status is mandatory, COL K-'.$get_row.' Not Inserted in database.';
                                // 	array_push($arr,$msg);

                                // }

                                else if ( !in_array(trim($sheetdatas['D']), array('Inbound Phone Banking','Outbound Call Center (OCC)'), true ) ){
                                    // $get_row = $i+1;
                                    $msg  = ' AV ID '.trim($sheetdatas['A']).' is not inserted, please enter valid axis process. ';
                                    array_push($arr,$msg);
                                }

                                else if(!$checklob){
                                    $msg  = ' '.trim($sheetdatas['A']).' is not inserted, center does not exist. ';
                                    array_push($arr,$msg);
                                }

                                else
                                {


                                    $check_duplicate_record = $this->db->query("select agent_id from tls_agent_mst where agent_id = '$check_agent_code'")->row_array();
                                    if(count($check_duplicate_record)>0)
                                    {
                                        // $get_row = $i+1;
                                        // $msg  = 'Agent Code '.$check_agent_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
                                        // array_push($arr,$msg);
                                        $update_array[] = array(

                                            'agent_id' => trim($check_agent_code),
                                            'agent_name' => trim($agent_name),
                                            'center' => $center,
                                            'axis_process' => trim($sheetdatas['D']),
                                            'status' => $status,
                                            'module_access_rights' => '23,33,37,36',
                                            'password' => encrypt_decrypt_password($check_agent_code),
                                            'is_admin'=>'0',
                                            'license_from'=>$licensefrom,
                                            'license_to'=>$licenseto
                                        );
                                        // print_r($update_array);
                                        $this->db->query("INSERT INTO audit_tls_agent_mst(id,agent_id,password,module_access_rights,agent_name,tl_name,tl_emp_id,am_name,center,om_name,om_emp_id,av_code,av_name,status,is_admin,is_region_admin,is_login,am_emp_id,lob,axis_process,license_from,license_to,updated_on,agent_type) select id,agent_id,password,module_access_rights,agent_name,tl_name,tl_emp_id,am_name,center,om_name,om_emp_id,av_code,av_name,status,is_admin,is_region_admin,is_login,am_emp_id,lob,axis_process,license_from,license_to,NOW(),'".$agentType."' from tls_agent_mst where agent_id='".trim($check_agent_code)."'");
                                    }
                                    else
                                    {

                                        $data[] =  array(

                                            'agent_id' => trim($check_agent_code),
                                            'agent_name' => trim($agent_name),
                                            'center' => $center,
                                            'axis_process' => trim($sheetdatas['D']),
                                            'status' => $status,
                                            'module_access_rights' => '23,33,37,36',
                                            'password' => encrypt_decrypt_password($check_agent_code),
                                            'is_admin'=>'0',
                                            'license_from'=>$licensefrom,
                                            'license_to'=>$licenseto


                                        );




                                    }

                                }

                            }

                        }

                    }
                }

                if(!empty($data)){
                    $this->db->insert_batch('tls_agent_mst',$data);
                }
                if(!empty($update_array)){
                    $this->db->update_batch('tls_agent_mst',$update_array,'agent_id');
                }
                unset($sheetdata);
                unset($y);
                unset($vs);
                unset($sheetdatas);
                unset($data);

                if($temp == 1)
                {

                    if(count($arr) <= 0)
                    {
                        $get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');

                        echo json_encode($get_data_arr);


                    }
                    else
                    {

                        $get_data_arr = array('errorCode' => '1', 'msg' => $arr);
                        echo json_encode($get_data_arr);
                    }

                    unset($arr);
                }
                else
                {

                    echo json_encode($get_data);
                }



            }


        }else if($this->input->post('agent_type') == 2){
            if(!empty($sheetdata))
            {

                $arr = array();
                $y = array_keys($sheetdata);
                foreach($y as $value)
                {
                    foreach($sheetdata[$value] as $val)
                    {

                        if(!empty($val))
                        {
                            $sheetdatas = array_filter($val);
                            if(!empty($sheetdatas))
                            {

                                if($sheetdatas['A']== 'AV_ID')
                                {
                                    continue;
                                }
                                if($sheetdatas['A']!= 'AV_ID')
                                {
                                    $get_data = array('errorCode' => '1', 'msg' => "Please Upload Proper Excel Sheet");
                                }
                                $temp = 1;
                                $flag = 1;
                                $check_agent_code = $sheetdatas['A'];
                                $agent_name = $sheetdatas['B'];

                                // echo $check_agent_code.'<br>';
                                // echo $agent_name;exit;
                                $check_agent_code = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['A']);
                                $agent_name = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['B']);
                                $sheetdatas['E'] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['E']);

                                $center = trim($sheetdatas['D']);
                                $center = trim(strtolower($center));
                                $center = ucfirst($center);

                                $status = $sheetdatas['E'];
                                $status = trim(strtolower($status));
                                $status = ucfirst($status);

                                if($status != 'Active'){
                                    $status = 'Inactive';
                                }

                                $checklob=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$center' ")->row_array();



                                if($check_agent_code == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Agent code is mandatory, COL A-'.$get_row.' Not Inserted in database.';
                                    array_push($arr,$msg);

                                }
                                else if($agent_name == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'agent name is mandatory, COL B-'.$get_row.' Not Inserted in database.';
                                    array_push($arr,$msg);

                                }

                                else if($center == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Center is mandatory, COL B-'.$get_row.' Not Inserted in database.';
                                    array_push($arr,$msg);

                                }
                                else if(!$checklob){
                                    $msg  = ' '.trim($sheetdatas['A']).' is not inserted, center does not exist. ';
                                    array_push($arr,$msg);
                                }

                                else{



                                    $check_duplicate_record = $this->db->query("select agent_id from tls_agent_mst_outbound where agent_id = '$check_agent_code'")->row_array();
                                    if(count($check_duplicate_record)>0)
                                    {

                                        $update_data[] =  array(

                                            'agent_id' => trim($check_agent_code),
                                            'agent_name' =>  trim($agent_name),
                                            'axis_process' =>  trim($sheetdatas['C']),
                                            'center' =>  trim($sheetdatas['D']),
                                            'module_access_rights' => '38,47,48',
                                            'password' => encrypt_decrypt_password($check_agent_code),
                                            'is_admin' => '0',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'status' => $status,

                                        );

                                        $this->db->query("INSERT INTO audit_tls_agent_mst(id,agent_id,password,module_access_rights,agent_name,tl_name,tl_emp_id,am_name,center,om_name,om_emp_id,av_code,av_name,status,is_admin,is_region_admin,is_login,am_emp_id,lob,axis_process,license_from,license_to,updated_on,agent_type) select id,agent_id,password,module_access_rights,agent_name,verifier_id,verifier_name,NULL,center,NULL,NULL,NULL,agent_name,status,is_admin,NULL,NULL,NULL,NULL,axis_process,NULL,NULL,NOW(),'".$agentType."' from tls_agent_mst_outbound where agent_id='".trim($check_agent_code)."'");

                                    }
                                    else
                                    {

                                        $data[] =  array(

                                            'agent_id' => trim($check_agent_code),
                                            'agent_name' =>  trim($agent_name),
                                            'axis_process' =>  trim($sheetdatas['C']),
                                            'center' =>  trim($sheetdatas['D']),
                                            'module_access_rights' => '38,47,48',
                                            'password' => encrypt_decrypt_password($check_agent_code),
                                            'is_admin' => '0',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'status' => $status,

                                        );



                                    }
                                }
                                //here


                            }

                        }

                    }
                }

                // print_r($data);exit;

                //updated by upendra on 09-04-2021
                if(!empty($data)){
                    $this->db->insert_batch('tls_agent_mst_outbound',$data);
                }

                if(!empty($update_data)){
                    $this->db->update_batch('tls_agent_mst_outbound', $update_data, 'agent_id');
                }

                unset($sheetdata);
                unset($y);
                unset($vs);
                unset($sheetdatas);
                unset($data);

                if($temp == 1)
                {

                    if(count($arr) <= 0)
                    {
                        $get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');

                        echo json_encode($get_data_arr);


                    }
                    else
                    {

                        $get_data_arr = array('errorCode' => '1', 'msg' => $arr);
                        echo json_encode($get_data_arr);
                    }

                    unset($arr);
                }
                else
                {

                    echo json_encode($get_data);
                }



            }
        }else{
            // echo '1234';exit;
            if(!empty($sheetdata))
            {
                $arr = array();
                $y = array_keys($sheetdata);
                foreach($y as $value)
                {
                    foreach($sheetdata[$value] as $val)
                    {

                        if(!empty($val))
                        {
                            $sheetdatas = array_filter($val);
                            if(!empty($sheetdatas))
                            {
                                if($sheetdatas['A']== 'DO id')
                                {
                                    continue;
                                }
                                if($sheetdatas['A']!= 'AV Id')
                                {
                                    $get_data = array('errorCode' => '1', 'msg' => "Please Upload Proper Excel Sheet");
                                }
                                $temp = 1;
                                $flag = 1;
                                $check_do_code = $sheetdatas['A'];
                                $check_do_name = $sheetdatas['B'];
                                $check_do_tl_id = $sheetdatas['C'];
                                $check_do_tl_name = $sheetdatas['D'];
                                $check_do_center= $sheetdatas['E'];
                                $status = $sheetdatas['F'];

                                $checkCenter=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$check_do_center' ")->row_array();

                                $module_access_rights=$this->db->query("SELECT GROUP_CONCAT(role_module_id) AS ids FROM role_access_module WHERE url_link IN ('/group_renewal_do_home', '/group_renewal_phaset') ")->row_array();

                                $module_access_rights = $module_access_rights['ids'];


                                if(preg_match ('/^([a-zA-Z0-9]+)$/', $check_do_code)){
                                    $do_code_flag = 'valid';
                                }
                                else{
                                    $do_code_flag = 'invalid';
                                }


                                if(preg_match ('/^([a-zA-Z\s]+)$/', $check_do_name))
                                {
                                    $do_name_flag = 'valid';
                                }else{
                                    $do_name_flag = 'invalid';
                                }

                                if(preg_match ('/^([a-zA-Z0-9]+)$/', $check_do_tl_id)){
                                    $tl_id_flag = 'valid';
                                }
                                else{
                                    $tl_id_flag = 'invalid';
                                }

                                if(preg_match ('/^([a-zA-Z\s]+)$/', $check_do_tl_name))
                                {
                                    $tl_name_flag = 'valid';
                                }else{
                                    $tl_name_flag = 'invalid';
                                }


                                if($checkCenter){
                                    $center_flag = 'valid';
                                }else{
                                    $center_flag = 'invalid';
                                }



                                //updated by upendra on 09-04-2021
                                $status = trim(strtolower($status));
                                $status = ucfirst($status);

                                if($status != "Active"){
                                    $status = "Inactive";
                                }

                                if($check_do_code == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'DO code is mandatory, COL A-'.$get_row.' Not Inserted in database. ';
                                    array_push($arr,$msg);

                                }else if($do_code_flag == 'invalid'){
                                    $msg  = 'DO code '.$check_do_code.' is invalid. ';
                                    array_push($arr,$msg);
                                }else if($do_name_flag == 'invalid'){
                                    $msg  = 'DO name '.$check_do_name.' is invalid. ';
                                    array_push($arr,$msg);
                                }
                                else if($tl_id_flag == 'invalid'){
                                    $msg  = 'TL ID '.$check_do_tl_id.' is invalid. ';
                                    array_push($arr,$msg);
                                }else if($tl_name_flag == 'invalid'){
                                    $msg  = 'TL name '.$check_do_tl_name.' is invalid. ';
                                    array_push($arr,$msg);
                                }else if($center_flag == 'invalid'){
                                    $msg  = 'Center '.$check_do_center.' is availabel in master. ';
                                    array_push($arr,$msg);
                                }
                                else if($status == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Status is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
                                    array_push($arr,$msg);

                                }
                                else if($check_do_name == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'DO name is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
                                    array_push($arr,$msg);

                                }
                                else if($check_do_tl_id == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Tl id is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
                                    array_push($arr,$msg);

                                }
                                else if($check_do_tl_name == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Tl name is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
                                    array_push($arr,$msg);

                                }
                                else if($check_do_center == '')
                                {
                                    $get_row = $i+1;
                                    $msg  = 'Tl Center is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
                                    array_push($arr,$msg);

                                }

                                else
                                {


                                    $check_duplicate_record = $this->db->query("select do_id from tls_master_do where do_id = '$check_do_code'")->row_array();
                                    if(count($check_duplicate_record)>0)
                                    {
                                        //   $get_row = $i+1;
                                        //   $msg  = 'DO Code '.$check_do_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
                                        //   array_push($arr,$msg);
                                        $update_data[] =  array(

                                            'do_id' => trim($sheetdatas['A']),
                                            'password' => encrypt_decrypt_password(trim($sheetdatas['A'])),
                                            'module_access_rights' => $module_access_rights,
                                            'do_name' => trim($sheetdatas['B']),
                                            'tl_id' => trim($sheetdatas['C']),
                                            'tl_name' => trim($sheetdatas['D']),
                                            'center' => trim($sheetdatas['E']),
                                            'status' => $status,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')

                                        );
                                        $this->db->query("INSERT INTO audit_tls_master_do(id,do_id,password,module_access_rights,do_name,tl_id,tl_name,center,status,created_at,updated_at,password_change_date) select id,do_id,password,module_access_rights,do_name,tl_id,tl_name,center,status,NOW(),NOW(),password_change_datefrom tls_master_do where do_id='".trim($sheetdatas['A'])."'");

                                    }
                                    else
                                    {

                                        $data[] =  array(

                                            'do_id' => trim($sheetdatas['A']),
                                            'password' => encrypt_decrypt_password(trim($sheetdatas['A'])),
                                            'module_access_rights' => $module_access_rights,
                                            'do_name' => trim($sheetdatas['B']),
                                            'tl_id' => trim($sheetdatas['C']),
                                            'tl_name' => trim($sheetdatas['D']),
                                            'center' => trim($sheetdatas['E']),
                                            'status' => $status,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')

                                        );




                                    }

                                }

                            }

                        }

                    }
                }

                if(!empty($data)){
                    $this->db->insert_batch('tls_master_do',$data);
                }
                if(!empty($update_data)){
                    $this->db->update_batch('tls_master_do',$update_data,'do_id');
                }
                unset($sheetdata);
                unset($y);
                unset($vs);
                unset($sheetdatas);
                unset($data);

                if($temp == 1)
                {

                    if(count($arr) <= 0)
                    {
                        $get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');

                        echo json_encode($get_data_arr);


                    }
                    else
                    {

                        $get_data_arr = array('errorCode' => '1', 'msg' => $arr);
                        echo json_encode($get_data_arr);
                    }

                    unset($arr);
                }
                else
                {

                    echo json_encode($get_data);
                }



            }
        }

        //outbound start

    }
}

?>