<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once PATH_VENDOR.'vendor/autoload.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/SMTP.php';
class Customerdropout extends CI_Controller
{
    public function index()
    {    
        //echo $lead_id = encrypt_decrypt_password($_REQUEST['lead_id'], 'E');exit;
        $lead_id = encrypt_decrypt_password($_REQUEST['lead_id'], 'D');
        $lead_details=$this->db->query("select l.lead_id,l.trace_id,l.proposal_step_completed,l.plan_id,m.plan_id as quote_plan_id,m.si_type_id,m.cover,m.premium,m.policy_id,l.creditor_id,journey_type,dropout_page,d.route,c.customer_id,c.full_name,m.plan_name,m.policy_details from lead_details l left join master_customer c on c.lead_id=l.lead_id left join master_plan p on p.plan_id=l.plan_id left join master_communication_events d on d.id=l.dropout_page left join quote_member_plan_details m on m.lead_id=l.lead_id  where l.lead_id=".$lead_id." group by l.lead_id")->row(); 

        //echo '<pre>';print_r($lead_details);exit;
        $journey_type = $lead_details->journey_type;
        if(!empty($lead_details)){
            $this->session->set_userdata('customer_id', encrypt_decrypt_password($lead_details->customer_id, 'E'));
            $this->session->set_userdata('lead_id', encrypt_decrypt_password($lead_details->lead_id, 'E'));
            $this->session->set_userdata('trace_id', encrypt_decrypt_password($lead_details->trace_id, 'E'));
            if(!empty( $lead_details->cover)){
                $this->session->set_userdata('cover', $lead_details->cover);
            }if(!empty( $lead_details->premium)){
                $this->session->set_userdata('premium', $lead_details->premium);
            }if(!empty( $lead_details->policy_id)){
                $this->session->set_userdata('policy_id', $lead_details->policy_id);
            }if(!empty( $lead_details->plan_id)){
                $this->session->set_userdata('plan_id', $lead_details->plan_id);
            }if(!empty( $lead_details->quote_plan_id)){
                $this->session->set_userdata('plan_id', $lead_details->quote_plan_id);
            }if(!empty( $lead_details->si_type_id)){
                $this->session->set_userdata('si_type_id', $lead_details->si_type_id);
            }if(!empty( $lead_details->plan_name)){
                $this->session->set_userdata('plan_name', $lead_details->plan_name);
            }if(!empty( $lead_details->policy_details)){
                $this->session->set_userdata('policy_details_session', json_decode($lead_details->policy_details,true));
            }  
            if($journey_type==2 || $journey_type==3){
                $this->session->set_userdata('partner_id_session', encrypt_decrypt_password($lead_details->creditor_id, 'E'));
                if($journey_type==3){
                    $this->session->set_userdata('product_id_session', encrypt_decrypt_password($lead_details->plan_id, 'E'));
                }
                $arr['partner_id'] =$lead_details->creditor_id;

                $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/fetchPartnerDetails', $arr),TRUE);
                   
                if(!empty($checkDetails)){
                    $theme_param= $checkDetails['theme_param'];
                    $theme_param_arr=explode(",",$theme_param);
                    
                    $this->session->set_userdata('primary_color', $theme_param_arr[0]);
                    $this->session->set_userdata('secondary_color', $theme_param_arr[1]);
                    $this->session->set_userdata('text_color', $theme_param_arr[2]);
                    $this->session->set_userdata('background_color', $theme_param_arr[3]);
                    $this->session->set_userdata('cta_color', $theme_param_arr[4]);

                    $qu=$this->db->query("select * from link_ui_configuaration where creditor_id=".$lead_details->creditor_id);
                    if($this->db->affected_rows() > 0){
                        $re_qu=$qu->result_array();
                        $this->session->set_userdata('linkUI_configuaration', $re_qu);
                    }
                }


                
            }

          
        }

        $url = getRedirectRoute($lead_details,$journey_type);    
       
        
        redirect($url);
        
       
    }
}
