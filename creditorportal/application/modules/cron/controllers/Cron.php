<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
include(APPPATH.'razorpay_php/Razorpay.php');
use Razorpay\Api\Api;



//use GCM\AESGCM\AESGCM;
include(APPPATH.'libraries/aes_gcm/src/AESGCM.php');
use AESGCM\AESGCM;*/


class Cron extends CI_Controller {
     

    function __construct() {
        parent::__construct();
    }
		
	

    function send_dropoff_mail(){

        $constDropOffTime = 10;  
        $sqlStr = "SELECT l.lead_id,c.full_name,p.plan_name,l.dropout_page,l.creditor_id,l.email_id,l.mobile_no
        FROM lead_details l left join master_customer c on c.lead_id=l.lead_id left join master_plan p on p.plan_id=l.plan_id
        WHERE  l.updatedon <= date_sub(now(), interval ".$constDropOffTime." minute) AND l.dropoff_flag = 0 and dropout_page is not null";

        $query=$this->db->query($sqlStr)->result_array();

       

        if($query){

            foreach ($query as $val)
            {
              $t_query = "SELECT subject,type,content FROM master_communication_templates WHERE dropout_event =".$val['dropout_page']." and isactive=1";
              if(!empty($val['creditor_id'])){
                $t_query .= ' and creditor_id='.$val['creditor_id'];
                $journey_link = base_url().'customerdropout?lead_id='.encrypt_decrypt_password($val['lead_id'],'E');
                $templates=$this->db->query($t_query)->result_array();
                if(!empty($templates)){
                  foreach ($templates as $template) {

                    $subject =  str_replace(array('{{name}}', '{{plan_name}}','{{jouney_link}}'), array($val['full_name'], $val['plan_name'], $journey_link), $template['subject']);
                    $content =  str_replace(array('{{name}}', '{{plan_name}}','{{jouney_link}}'), array($val['full_name'], $val['plan_name'], $journey_link), $template['content']);
                    $log_data['lead_id']=$val['lead_id'];
                    $log_data['subject']=$subject;
                    $log_data['content']=$content;
                    $log_data['request_body']=$content;
                    $log_data['event']=$val['dropout_page'];
                    if($template['type']=='email'){
                        $log_data['to']=$val['email_id'];
                        $log_data['response_body']= sendMail($val['email_id'],$subject,$content);
                        //$log_data['response_body']= sendSms('8003392772',$content);
                        
                        createCommunicationLog($log_data);
                    }
                    if($template['type']=='sms'){
                        $log_data['to']=$val['mobile_no'];
                        $log_data['response_body']= sendSms($val['mobile_no'],$content);
                        createCommunicationLog($log_data);
                    }
                    

                  }
                  $this->db->where("lead_id",$val['lead_id']);
                   
                  $this->db->update("lead_details",['dropoff_flag'=>1]);
                    
                }
                
              }

            }
        }
      

    }

    function send_cd_balance_mail(){

        
        $sqlStr = "SELECT * from master_ceditors WHERE  isactive=1";

        $query=$this->db->query($sqlStr)->result_array();

       

        if($query){

            foreach ($query as $val)
            {
                $response_cd= CheckCDThreshold($val['creditor_id']);
                $creditor_id=$val['creditor_id'];
                $ceditor_email=$val['ceditor_email'];
                $creaditor_name=$val['creaditor_name'];
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
                  

                
            }
          

        }
    }
}