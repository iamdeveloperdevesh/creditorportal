<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Fyntune_home extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        // checklogin();
        // $this->RolePermission = getRolePermissions();
        $this->load->model("fyntune_home/Login_abc_m", "obj_home", true);
        $this->load->model("fyntune_home/ABC_m", "abc_m", true);
        $this->load->model('Logs_m', 'Logs_m');
        $this->load->library('session');
    }


    public function refresh_captcha_abc()
    {

        $captcha_string = $this->generateRandomString();
        $this->session->set_userdata('captcha_string', $captcha_string);
        $cap = common_captcha_create($captcha_string);
        echo $cap['image'];
    }

    function getRandomUniqueNumber($length = 15)
    {
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


    public function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function getUniqueRandomNumberAbc($length = 12)
    {
        $randomNumber = $this->getRandomUniqueNumber($length);
        $this->db->where(['lead_id' => $randomNumber]);
        $employeeData = $this->db->get('lead_details')->result_array();

        if (empty($employeeData)) {
            return $randomNumber;
        } else {
            $this->getUniqueRandomNumberAbc($length);
        }
    }


    public function index()
    {
        // echo"<img src='./assets/images/ad-thankyou2.png'>";
        // exit;
        $captcha_string = $this->generateRandomString();
        $this->session->set_userdata('captcha_string', $captcha_string);
        $cap = common_captcha_create($captcha_string);
        $data['captcha_image'] = $cap;

        // print_pre($_SESSION);
        $data['title-head'] = 'Login';
        // print_r($data);exit;
        $this->load->view('template/customer_header.php');
        $this->load->view('fyntune_home/login_abc_view.php', $data);
        $this->load->view('template/customer_footer.php');
    }

    public function updateDropOffFlagValue($emp_id = 0)
    {
        if (!empty($emp_id)) {
            $seconds = 30;
            $date_now = date("Y-m-d H:i:s");
            $moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
            $request_arr_dropoff = ["dropoff_flag" => "0", 'modified_date' => $moddate];
            $this->db->where("emp_id", $emp_id);
            $this->db->update("employee_details", $request_arr_dropoff);
        }
    }


    function setCustomerSession($aD2CSession)
    {
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
        //   print_r($this->session);exit;
        $this->session->sess_regenerate();
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

    function logs_post_data_insert($lead_id, $request, $type, $response = "")
    {
        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($request), "type" => $type, "product_id" => 'ABC'];
        $logs_array['data'] = $request_arr;
        $this->Logs_m->insertLogs($logs_array);
    }

    public function generate_lead_id()
    {
        $lead_id = time();
        $EmployeeData = $this
            ->db
            ->get_where("lead_details", array(
                "lead_id" => $lead_id
            ))->row_array();
        if (!empty($EmployeeData)) {
            $this->generate_lead_id();
        }
        return $lead_id;
    }

    public function send_otp()
    {
        $otp = rand(000000, 999999);
        $mobileno = $this->input->post('mob_no');
        // print_r($_POST);exit;
        //sending code will go here.
        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/sendOtp', [
            'otp'   => $otp,
            'mobile_no' => $mobileno
        ]);

        $checkDetails = json_decode($checkDetails, true);

        if ($checkDetails['status_code'] == 200) {

            $this->session->set_userdata('lead_id', $checkDetails['data']['lead_id']);
            $this->session->set_userdata('mobile', $mobileno);
            $this->session->set_userdata('trace_id', $checkDetails['data']['trace_id']);
            $this->session->set_userdata('customer_id', $checkDetails['data']['customer_id']);
        }

        // print_r($checkDetails);exit;
        echo json_encode($checkDetails);
        exit;
    }


    public function validate_otp()
    {
        $otp = $this->input->post('otp');
        $mobileno = $this->input->post('mobile_no');
        $lead_id = $this->session->userdata('lead_id');

        //$qr = $this->db->query('select * from  employee_details where mob_no = "'.$mobileno.'"');

        $checkDetails = curlFunction(SERVICE_URL . '/customer_api/validateOtp', [
            //'otp'   => $otp,
            'mobile_no' => $mobileno,
            'lead_id' => $lead_id
        ]);
        // echo $this->db->last_query();exit;
        // print_r($checkDetails);exit;

        $checkDetails = json_decode($checkDetails, true);

        if (!empty($checkDetails['data'])) {
            $query = $checkDetails['data'];
            $this->session->set_userdata('otp_code', '1234');
            // $otp = $this->session->userdata('otp_code');
            // $res = ["status"=>1,"description"=>"Success"];
            // return $res; 
            $otp_check = $this->session->userdata('otp_code');

            //echo $otp_check ."==". $otp ;
            // die();

            if ($otp_check == $otp) {
                // $canProceed = $this->abc_m->validCheckOtpAttempt($query->emp_id);

                // if($canProceed['status'] == 0){
                //     http_response_code(401);
                //     $res = ["status"=>0,"message"=>$canProceed['message']];
                //     echo json_encode($res);exit;
                // }

                //check user last activity
                //$res = $this->db->get_where("user_activity_abc",array("emp_id" => $query['lead_id']))->row_array();
                if (!empty($checkDetails['data']['user_activity'])) {
                    $res = $checkDetails['data']['user_activity'];
                    // $policy_detail_id = $this->db->get_where("employee_product_details",array("emp_id" => $query['lead_id']))->row_array(); 
                    /*if(!empty($checkDetails['data']['plan_id'])){
                        $policy_id = $policy_detail_id['policy_id'];
                        $already_cust_id = $this->db->where(["emp_id" => $query['emp_id']])->get("api_proposal_response")->num_rows();
                        if($already_cust_id > 0){
                            if($res['type'] != 7){
                                $res['type'] = 7;
                            }
                        }
                    }else{
                        $policy_id = 2;
                    }*/

                    // echo $res['type'];
                    // exit;

                    $activityType = $res;
                    switch ($activityType) {
                        case 1:
                            $longUrl = 'comprehensive_abc/comprehensiveabc/?partner=' . encrypt_decrypt_password(PARTNER_ID);
                            break;
                        case 2:
                            //$longUrl = '/generate_quote/'.encrypt_decrypt_password($policy_id).'/?partner='.encrypt_decrypt_password(PARTNER_ID)'; //To be done later
                            break;
                        case 3:
                            $longUrl = '/member_proposer_detail/?partner=' . encrypt_decrypt_password(PARTNER_ID);
                            break;
                        case 4:
                            $longUrl = '/member_review/?partner=' . encrypt_decrypt_password(PARTNER_ID);
                            break;
                        case ($activityType == 5 || $activityType == 6):
                            $longUrl = '/payment_redirection_abc/?partner=' . encrypt_decrypt_password(PARTNER_ID);
                            break;
                        case 7:
                            $longUrl = '/success_view/' . $query['lead_id'] . '/?partner=' . encrypt_decrypt_password(PARTNER_ID);
                            break;
                    }
                } else {
                    $longUrl = 'comprehensive_abc/comprehensiveabc' . encrypt_decrypt_password(PARTNER_ID);
                }

                http_response_code(200);
                $res = ["status" => 1, "description" => "Success", "url" => $longUrl];
            } else {
                http_response_code(401);
                $res = curlFunction(SERVICE_URL . '/customer_api/invalidCheckOtpAttempt', [
                    'lead_id' => $query['lead_id']
                ]);
                session_destroy();
                // $res = ["status"=>0,"message" => "Invalid OTP", "url"=>""];
            }
        } else {
            http_response_code(401);
            $res = ["status" => 0, "message" => "Invalid OTP", "url" => ""];
            session_destroy();
        }
        //print_r($res);exit;

        echo json_encode($res);
        exit;
    }
}
