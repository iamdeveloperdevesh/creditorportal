<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once PATH_VENDOR . 'vendor/autoload.php';
require_once PATH_VENDOR . 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR . 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR . 'vendor/phpmailer/phpmailer/src/SMTP.php';

class Customerquotes extends CI_Controller
{

    public function index()
    {
        //echo encrypt_decrypt_password('WitlUW5hYVlYV1JsZ2NxODV1Nm1EUT09','D');exit;
        $data['trace_id'] = $this->session->userdata('trace_id');

            $partner_id = '80';
            $creditor_logo = $this->db->query("select creditor_logo from master_ceditors where creditor_id=" . $partner_id)->row()->creditor_logo;
            $this->session->set_userdata('creditor_logo', $creditor_logo);
            // echo $partner_id;die;
            if ($partner_id) {
                $arr['partner_id'] = $partner_id;

                $checkDetails = $this->db->query("select  creditor_id, creaditor_name, creditor_code, ceditor_email, 
creditor_mobile, creditor_phone, creditor_pancard, creditor_gstn, address, creditor_logo, isactive
        , createdon, initial_cd, cd_threshold, cd_utilised, cd_balance_remain
         from master_ceditors mc
        where isactive= 1 AND creditor_id= '$partner_id'")->row_array();;

                   // var_dump($this->db->last_query());die;
                if (!empty($checkDetails)) {
$arr_det = [];
                    $plan_details = $this->db->query("select mp.plan_id,mp.plan_name from master_plan as mp where creditor_id = '$partner_id' and isactive = 1")->result_array();
foreach($plan_details as $key => $val)
                    {

                        $plan_id = $val['plan_id'];
                        switch ($plan_id) {
                            case 760:
                                $lead_id = 9790;
                                break;
                            case 761:
                                $lead_id = 9789;
                                break;
                            case 762:
                                $lead_id = 9788;
                                break;

                        }
$lead_details  = $this->db->query("select lead_id,trace_id from lead_details where lead_id = '$lead_id'")->row_array();
                       // $this->session->set_userdata('lead_id', encrypt_decrypt_password($lead_details['lead_id']));
                       // $this->session->set_userdata('customer_id', $lead_details['customer_id']);
                       // $this->session->set_userdata('trace_id', encrypt_decrypt_password($lead_details['trace_id']));



                        $member_details = $this->db->query("SELECT fc.member_type,ppmd.customer_id,ppm.policy_id FROM proposal_policy_member as ppm inner join proposal_policy_member_details as ppmd INNER join family_construct as fc WHERE  ppmd.relation_with_proposal = fc.id AND  ppm.member_id = ppmd.member_id AND   ppm.lead_id = '$lead_id'")->result_array();
                        //$this->session->set_userdata('customer_id', encrypt_decrypt_password($member_details[0]['customer_id']));
                        $arr_det[$key]['premium_details'] = $this->db->query("select sum_insured,premium_amount from proposal_policy where lead_id = '$lead_id'")->result_array();

                        $arr_det[$key]['plan_id'] = $plan_id;
                        $arr_det[$key]['plan_name'] = $val['plan_name'];
                        $arr_det[$key]['feature_config'] = $this->db->query("select long_description from features_config where plan_id = '$plan_id'")->result_array();
                        $arr_det[$key]['member_details'] = $member_details;
                        $arr_det[$key]['lead_id'] =  encrypt_decrypt_password($lead_details['lead_id']);
                        $arr_det[$key]['customer_id'] =  encrypt_decrypt_password($member_details[0]['customer_id']);
                        $arr_det[$key]['policy_id'] =  encrypt_decrypt_password($member_details[0]['policy_id']);

                        $arr_det[$key]['trace_id'] = encrypt_decrypt_password($lead_details['trace_id']);


                    }
                   $data['plan_data'] = $arr_det;
                    $qu = $this->db->query("select * from link_ui_configuaration where creditor_id=" . $partner_id);
                    if ($this->db->affected_rows() > 0) {
                        $re_qu = $qu->result_array();
                        $this->session->set_userdata('linkUI_configuaration', $re_qu);
                    }
                }


            }

        $this->load->view('template/customer_portal_header.php');
        $this->load->view('Customerquotes/index',$data);
        $this->load->view('template/customer_portal_footer.php');


    }
}