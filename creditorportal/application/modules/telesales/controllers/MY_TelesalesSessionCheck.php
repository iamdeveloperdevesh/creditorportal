<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_TelesalesSessionCheck extends CI_Controller
{
	
	function __construct()
    {
        parent::__construct();
        // do some stuff
		//$this->db= $this->load->database('axis_retail',TRUE);
		$this->checkSession();
    }
	
    public function checkSession()
    {
		
			//return true;
		if($this->session->userdata('telesales_session')) {
            //if session exist			
						
			$teleSession = $this->session->userdata('telesales_session');
			$agent_id = encrypt_decrypt_password($teleSession['agent_id'],'D');		
			$rsEmp = $this->db->select("id, sessionid,updated_time")->where(["agent_id" => $agent_id])->get("tls_agent_session");
			
			// echo $this->db->last_query();exit;
			
	
			if($rsEmp->num_rows() > 0 ){
							
				$aRow = $rsEmp->row();	
				
				$old_session_id = $aRow->sessionid;	
				$last_updated_time = $aRow->updated_time;	
				$id = $aRow->id;
				
				//check if user session is expired 
				$expiry_time = time() - $last_updated_time;      
					//900 [15-min]
				if ( $expiry_time >  900 )
				{
					//expire after - SESSION_LIFE_SEC
					//session timeout so redirect to logout page.
					$this->session->unset_userdata('telesales_session');	
					if ($this->input->is_ajax_request()) {						 					  				  
					  //exit('Session Timeout! User session expired!');
					   echo "tele_session_timeout";exit;
					}
					redirect('login','refresh');
				}else{
					
					
					//check if new session is created in other browser then redirect to login page.
					if($old_session_id !== session_id()){
						$this->session->unset_userdata('telesales_session');	
						if ($this->input->is_ajax_request()) {						 					  				  
						  //exit('Session not matched! User session expired!');
						   echo "tele_session_timeout";exit;
						}
						redirect('login','refresh');
					}	
					
					$_SESSION['regenerated'] = time();//update the session time
					
					$data = array(						
						'updated_time' => time(),					
					);
					$this->db->where('id', $id);
					$this->db->update('tls_agent_session', $data);
					
					
					
				}
				
			}else{
				if ($this->input->is_ajax_request()) {						 					  				  
				  //exit('User session is not exist!');
				   echo "tele_session_timeout";exit;
				}
				redirect('login','refresh');
			}
			
		}	
	}
}

