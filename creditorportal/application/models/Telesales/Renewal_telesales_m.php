<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Renewal_telesales_m extends CI_Model
{


    public function check_policy($pnumber)
    {

        $this->db->select('*');
        $this->db->from('telesales_renewal_logs');
        $this->db->where('policy_number', $pnumber);
        $this->db->where('status', '1');
        $result = $this->db->get()->result_array();

        if (count($result) > 0) {
            return true;
        }
    }

    public function check_valid_av_code($avcode)
    {
        $data = $this->db
            ->select('*')
            ->from('tls_agent_mst_outbound')
            ->where(' `agent_id` ', $avcode)
            ->limit('1')
            ->get()
            ->row_array();

        if (!empty($data)) {

            //09-04-2021
            if($data['status'] == 'Active'){

                $agent_name = $data['agent_name'];
                $response_m = ["status" => true, "message" => "Sucessfully Updated", "agent_name" => $agent_name, "digitalOfficer" => "test_digital_officer", "location" => $data['center']];

            }else{
                $response_m = ["status" => false, "message" => "Entered user is inactive."];
            }

           
        } else {
            $response_m = ["status" => false, "message" => "Entered user does not exist."];
        }

        return $response_m;
    }


    public function check_valid_do($digitalofficer)
    {
        $data = $this->db
            ->select('*')
            ->from('tls_master_do')
            ->where(' `do_id` ', $digitalofficer)
            ->limit('1')
            ->get()
            ->row_array();

        if (!empty($data)) {
            if($data['status'] == 'Active'){
            $response_m = ["status" => true, "message" => "DO Found Updated"];
            }
            else{
                $response_m = ["status" => false, "message" => "Entered DO is inactive"];
            }

        } else {
            $response_m = ["status" => false, "message" => "Entered DO does not exist."];
        }

        return $response_m;
    }
    public function check_token($avanacode, $ekey, $accesskey)
    {

        if (empty($accesskey)) {
            if (empty($avanacode) || empty($ekey)) {
                $error = ["Status" => "AVNA Code or Key Cannot be Empty"];
                echo json_encode($error);
                exit;
            }
        }


        if (!empty($accesskey)) {
            if (empty($accesskey)) {
                $error = ["Status" => "Access Key Cannot be Empty"];
                echo json_encode($error);
                exit;
            }
        }

        $this->db->select("*");
        $this->db->from("telesales_renewal_token");

        if (empty($accesskey)) {

            $this->db->where("avnacode", $avanacode);
            $this->db->where("ekey", $ekey);
        }else {
            $this->db->where("token", $accesskey);
        }

        $result = $this->db->get()->row_array();

        if (count($result) > 0) {

            $checktoken = !empty($accesskey) ? $result['token'] : "";
            $checktime = date('Y-m-d H:i:s');
            $expiry = date('Y-m-d H:i:s', strtotime($result['created_at'] . "+1 hour"));

            if (empty($accesskey)) {

                if (strtotime($checktime) < strtotime($expiry)) {
                    $error['Error'] = ["Status" => "Success", "Access_Token" => $result['token']];
                } else {

                    $newkey = hash("sha256", $avanacode . TIME());
                    $update = [
                        "token" => $newkey,
                        "created_at" => $checktime
                    ];
                    $this->db->where('avnacode', $avanacode);
                    $this->db->update('telesales_renewal_token', $update);
                    $error['Error'] = ["Status" => "Success", "Access_Token" => $newkey];
                }
            } else {

                if (strtotime($checktime) > strtotime($expiry)) {
                    $error['Error'] = ["Status" => "Token Expired"];
                }
            }
        } else {
            $error['Error'] = ["Status" => "Invaid Credentials"];
        }
        return $error;
    }

    public function check_renewal_policy($lead_id)
    {

        header('Content-Type:application/json');
        if (empty($lead_id)) {
            $error = ['Status' => "Reference ID Required"];
            echo json_encode($error);
            exit;
        } else {

            // $this->db->select('avnacode,policy_number as oldpolicy_number,newpolicy_number,mobile_no,renewal_updated');
            $this->db->select('status');
            $this->db->from('telesales_renewal_logs');
            $this->db->where('lead_id', $lead_id);
            $result = $this->db->get()->row_array();

            if(count($result)>0){

                
                if($result['status']!='1'){
                    // $result=["error"=>["errorcode"=>"01","errorMessage"=>"This Reference id is lapsed"]];

                    $this->db->select('lead_id,avnacode as authorised_verifier,digital_officer,bb_code,policy_number,dob,mobile_no,source');
                    $this->db->from('telesales_renewal_logs');
                    $this->db->where('lead_id', $lead_id);
                    $result = $this->db->get()->result_array();
                    $result[0]['status'] = 'lapsed';


                }else{
                    $this->db->select('lead_id,avnacode as authorised_verifier,digital_officer,bb_code,policy_number,dob,mobile_no,source');
                    $this->db->from('telesales_renewal_logs');
                    $this->db->where('lead_id', $lead_id);
                    $this->db->where('status', '1');
                    $result = $this->db->get()->result_array();
                    $result[0]['status'] = 'success';

                }

                $req = ['lead_id' => $lead_id];

                $fdata = [
                    'lead_id' => $lead_id,
                    'policy_number' => $result[0]['policy_number'],
                    'phone_number' => $result[0]['mobile_no'],
                    'req' => json_encode($req),
                    'res' => json_encode($result),
                    'type' => 'customer_portal_click',
                    'cron_date' => date('Y-m-d H:i:s')
                ];
        
                $this->db->insert('telesales_renewal_cron_logs', $fdata);
                print_r(json_encode($result));                

            }else{
                $error=["error"=>"No Data Found"];
                print_r(json_encode($error));
            }
        }
    }
    public function telesales_renewal_group($one, $two, $three, $all)
    {

        $this->db->select('ref_id,customer_email,customer_contact');
        $this->db->from('telesales_renewal_group');
        $this->db->where('ref_id', $one);
        $this->db->where('customer_email', $two);
        $this->db->where('customer_contact', $three);
        $result = $this->db->get()->result_array();

        if (count($result) > 0) {
        } else {

            $this->db->insert('telesales_renewal_group', $arr);
        }
    }

    function get_agent_details()
    {

        $agent_post = $this->input->post(null, true);


        $agent_id = $this->agent_id;

        $data = $this->db
            ->select('agent_id, is_admin ,center')
            ->from('tls_agent_mst_outbound')
            ->where('id', $agent_id)
            ->get();

        if (!empty($agent_post) && !empty($agent_post['agent_id'])) {

            $base_agent_id = $agent_post['agent_id'];
            $data =    $this->db
                ->select('tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name,base_agent_name,imd_code,center,lob,vendor')
                ->from('tls_base_agent_tbl')
                ->where('base_agent_id', $base_agent_id)
                ->get();
        }

        if ($data->num_rows() > 0) {

            $result = $data->row_array();

            return $result;
        }
    }


    function check_renewal_group($pnumber, $checkStatus, $avCode)
    {

        if ($checkStatus == 'preCheck') {
            $data = $this->db
                ->select('*')
                ->from('telesales_renewal_group')
                ->where('hb_certificate_no', $pnumber)
                ->get();

            if ($data->num_rows() > 0) {

                $result = $data->row_array();
                $mobile = $result['customer_contact'];

                $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success'], 'coi_number' => $pnumber, 'Mobile_Number' => $mobile, 'policy_lapsed_flag' => 'No', 'renewal_status' => 'Yes', 'renewed_flag' => 'No', 'res' => ''];
            } else {
                $output = ["error" => ["ErrorCode" => '01', "ErrorMessage" => "No Record Found"], "coi_number" => 'Not Found', "Mobile_Number" => '', "policy_lapsed_flag" => '', "renewal_status" => "", "renewed_flag" => "", "res" => ""];
            }

            $check_policy =  $this->db->select("id")
                ->from("telesales_renewal_logs")
                ->where("policy_number", $pnumber)
                ->where("status", '1')
                ->order_by("id", "DESC")
                ->limit("1")
                ->get()
                ->row_array();


            if ($check_policy) {
                $update_policy = $this->db->set('status', 0);
                $this->db->set('lapsedon', date('Y-m-d H:i:s'));
                $this->db->where('id', $check_policy['id']);
                $this->db->update('telesales_renewal_logs');
            }

            $req_arr = ['COI_Number' => $pnumber, "DoB" => '', 'Proposer_MobileNumber' => ''];
            $req = json_encode($req_arr);
            $lead_id=$this->getleadid();
            $res = json_encode($output);
            ($data->num_rows() > 0) ? $p_status = 1 : $p_status = 2;
            $fdata = [
                'lead_id' => $lead_id,
                'avnacode' => $avCode,
                'req' => $req,
                'res' => $res,
                'product_type' => 'group',
                'ref_number' => '',
                'policy_number' => $pnumber,
                'renewal_response' => '',
                'login_id' => '',
                'status' => $p_status,
                'renewstatus' => ''
            ];

            $this->db->insert('telesales_renewal_logs', $fdata);
        }

        if ($checkStatus == 'afterCheck') {
            $update_policy =
                $this->db->set('renewal_response', 'link sent');
            $this->db->where('policy_number', $pnumber);
            $this->db->where('avnacode', $avCode);
            $this->db->where('status', "1");
            $this->db->update('telesales_renewal_logs');

            $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success'], 'coi_number' => $pnumber, 'Mobile_Number' => $mobile, 'policy_lapsed_flag' => 'No', 'renewal_status' => 'Yes', 'renewed_flag' => 'No', 'res' => ''];
        }

        // $output['error'] = 'Success';
        // $output['policy_lapsed_flag'] = 'No';
        // $output['renewal_status'] = 'Yes';
        // $output['renewed_flag'] = 'No';
        return $output;
    }

    public function getleadid()
    {
        $this->db->select("lead_id");
        $this->db->from('telesales_lead_id');
        $this->db->where('id', '1');
        $lead_id = $this->db->get()->row_array();
        $unique_ref_no = (int)$lead_id['lead_id'] + 1;

        $arr = ['lead_id' => $unique_ref_no];
        $this->db->where('id', '1');
        $this->db->update('telesales_lead_id', $arr);

        $duplicate_check = $this
            ->db
            ->get_where("telesales_renewal_logs", array(
            "lead_id" => $unique_ref_no
        ))->row_array();
        if (!empty($duplicate_check))
        {
            return $this->getleadid();
        }
        else{
            return $unique_ref_no;
        }
       
    }


    public function getleadid_old()
    {

        $this->db->select("lead_id");
        $this->db->from('telesales_lead_id');
        $this->db->where('id', '1');
        $lead_id = $this->db->get()->row_array();

        $clead_id = $lead_id['lead_id'];

        $duplicate_check = $this
        ->db
        ->get_where("telesales_renewal_logs", array(
        "lead_id" => $clead_id
        ))->row_array();

        if(!empty($duplicate_check)){
            $newlead = (int)$lead_id['lead_id'] + 1;
            $arr = ['lead_id' => $newlead];

            $this->db->where('id', '1');
            $this->db->update('telesales_lead_id', $arr);

            $this->db->select("lead_id");
            $this->db->from('telesales_lead_id');
            $this->db->where('id', '1');
            $lead_id = $this->db->get()->row_array();

            $clead_id = $lead_id['lead_id'];

        }else{

            $newlead = (int)$lead_id['lead_id'] + 1;
            $arr = ['lead_id' => $newlead];

            $this->db->where('id', '1');
            $this->db->update('telesales_lead_id', $arr);
        }

        return $clead_id;

/*        
        $this->db->select("lead_id");
        $this->db->from('telesales_lead_id');
        $this->db->where('id', '1');
        $lead_id = $this->db->get()->row_array();

        $clead_id = $lead_id['lead_id'];

        $newlead = (int)$lead_id['lead_id'] + 1;
        $arr = ['lead_id' => $newlead];

        $this->db->where('id', '1');
        $this->db->update('telesales_lead_id', $arr);

        return $clead_id;
*/        
    }

    public function all_retail_data_old()
    {

        $telSalesSession = $this->session->userdata('telesales_session');
        $session_agent_id = encrypt_decrypt_password($telSalesSession['agent_id'], 'D');
        $session_admin = $telSalesSession['is_admin'];

        $get_agent_id =  $this->db->select("agent_id")
        ->from("tls_agent_mst_outbound")
        ->where("id", $session_agent_id)
        ->get()
        ->row_array();


        $get_agent_id = $get_agent_id['agent_id'];

        $type=$this->input->post('product');
        $policy=$this->input->post('policy');
        $epolicy=$this->input->post('epolicy');
        $occcenter=$this->input->post('occcenter');
        $lmdf=$this->input->post('lmdf');
        $lmdt=$this->input->post('lmdt');
        $current_status=$this->input->post('current_status');
       
        $this->db->select('lead_id,avnacode,req,res,other_details,product_type,policy_number,mobile_no,renewal_response,renewal_res,created_date,digital_officer,last_updated_on,renewed_policy_number, location,status');
        $this->db->from('telesales_renewal_logs');
        // $this->db->where('lead_id','111360');
        if (!empty($type)&&$type!='SELECT') {
            $this->db->like("product_type", $type);
        }

        if (!empty($policy)) {
            $this->db->like("policy_number", $policy);
        }

        if (!empty($epolicy)) {
            $this->db->like("renewed_policy_number", $epolicy);
        }

        if (!empty($current_status)) {
            if($current_status == '1'){
                $this->db->where( "renewed_policy_number IS NULL");
            }
            if($current_status == '2'){
                $this->db->where( "renewed_policy_number IS NOT NULL AND status = '1' ");
            }
            
        }

        if (!empty($lmdf) && !empty($lmdt)) {
            // $this->db->where('last_updated_on >=', $lmdf);
            // $this->db->where('last_updated_on =', $lmdt);
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "last_updated_on BETWEEN '$lmdf' AND '$lmdt' ");
        }

        if (!empty($occcenter)) {
            $this->db->like("location", $occcenter);
        }

       
        if (!empty($current_status)) {
            if($current_status == '3'){
                $this->db->where('status IN ("4")');
            }
            if($current_status == '1'){
                $this->db->where('status IN ("1")');
            }
        }
        else{
            $this->db->where('status IN ("1", "4")');
        }
        

        if($session_admin == '0'){
            $this->db->where('avnacode', $get_agent_id);
        }

        $this->db->order_by('id','desc');

        if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

        $result = $this->db->get()->result();
        // echo $this->db->last_query();exit; 
        
        if (count($result) > 0) {
            return $result;
        }else{
            return 0;
        }
    }

    public function all_retail_data()
    {

        $telSalesSession = $this->session->userdata('telesales_session');
        $session_agent_id = encrypt_decrypt_password($telSalesSession['agent_id'], 'D');
        $session_admin = $telSalesSession['is_admin'];

        $get_agent_id =  $this->db->select("agent_id")
            ->from("tls_agent_mst_outbound")
            ->where("id", $session_agent_id)
            ->get()
            ->row_array();


        $get_agent_id = $get_agent_id['agent_id'];

        $type=$this->input->post('product');
        $policy=$this->input->post('policy');
        $epolicy=$this->input->post('epolicy');
        $occcenter=$this->input->post('occcenter');
        $lmdf=$this->input->post('lmdf');
        $lmdt=$this->input->post('lmdt');
        $current_status=$this->input->post('current_status');

        $this->db->select('id as latest from `telesales_renewal_logs` WHERE (status = "1" OR status = "4") group by `policy_number` order by renewal_status ');
        $subquery = $this->db->get_compiled_select();
//      $this->db->_reset_select();
        // $this->db->join("($subquery)  t2","t2.id = telesales_renewal_logs.id");

        //      $this->db->select('lead_id,avnacode,product_type,policy_number,mobile_no,renewal_response,created_date,digital_officer,last_updated_on,renewed_policy_number,
      //  location,status');
$this->db->select('lead_id,avnacode,req,res,other_details,product_type,policy_number,mobile_no,renewal_response,renewal_res,created_date,digital_officer,last_updated_on
,renewed_policy_number,location,status,renewal_status');

        $this->db->from('telesales_renewal_logs');
//      $this->db->join("('select max(id) as latest from `telesales_renewal_logs` group by `policy_number`') AS TableB", 'telesales_renewal_logs.id= TableB.id');
         $this->db->join("($subquery)  t2","t2.latest = telesales_renewal_logs.id");
         if (!empty($type)&&$type!='SELECT') {
            $this->db->like("product_type", $type);
        }
        //$this->db->where( "status", 1);
        if (!empty($policy)) {
            $this->db->like("policy_number", $policy);
        }
          if (!empty($epolicy)) {
            $this->db->like("renewed_policy_number", $epolicy);
        }

        if (!empty($current_status)) {
            if($current_status == '1'){
                $this->db->where( "renewed_policy_number IS NULL");
            }
            if($current_status == '2'){
                $this->db->where( "renewed_policy_number > ''  AND status = '1'");
            }

        }

        if (!empty($lmdf) && !empty($lmdt)) {
            // $this->db->where('last_updated_on >=', $lmdf);
            // $this->db->where('last_updated_on =', $lmdt);
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "last_updated_on BETWEEN '$lmdf' AND '$lmdt' ");
        }

        if (!empty($occcenter)) {
            $this->db->like("location", $occcenter);
        }

        // $this->db->where('status', '1');
        //$this->db->where('status IN ("1", "4")');
        if (!empty($current_status)) {
            if($current_status == '3'){
//echo 1;
                $this->db->where('status = "4" OR ((renewed_policy_number IS NULL OR renewed_policy_number = "") AND renewal_status = "0") ');
            }
            if($current_status == '1'){
                $this->db->where('status IN ("1")');
            }
        }
        else{
            $this->db->where('status IN ("1", "4")');
        }

        if($session_admin == '0'){
            $this->db->where('avnacode', $get_agent_id);
        }
        // $this->db->join('select max(id) as latest from `telesales_renewal_logs` group by `policy_number`');
//      $this->db->join("('select max(id) as latest from `telesales_renewal_logs` group by `policy_number`') AS TableB", 'l.id= TableB.id');
        //$this->db->order_by('renewal_status','ASC');
        $this->db->order_by('id','desc');
        if ($_POST["length"] != -1) { $this->db->limit($_POST['length'], $_POST['start']);
        }

        $result = $this->db->get()->result();
        // echo $this->db->last_query();exit;

        if (count($result) > 0) {
            return $result;
        }else{
            return 0;
        }
    }

    public function total_retail_data()
    {


        $telSalesSession = $this->session->userdata('telesales_session');
        $session_agent_id = encrypt_decrypt_password($telSalesSession['agent_id'], 'D');
        $session_admin = $telSalesSession['is_admin'];

        $get_agent_id =  $this->db->select("agent_id")
        ->from("tls_agent_mst_outbound")
        ->where("id", $session_agent_id)
        ->get()
        ->row_array();


        $get_agent_id = $get_agent_id['agent_id'];

        $this->db->select('lead_id,avnacode,product_type,policy_number,mobile_no,renewal_response');
        $this->db->from('telesales_renewal_logs');
        $this->db->where('status IN ("1", "4")');
        if($session_admin == '0'){
            $this->db->where('avnacode', $get_agent_id);
        }
        $result = $this->db->get()->result();

        if (count($result) > 0) {
            return count($result);
        }else{
            return 0;
        }
    }    




    public function retails_update_total($product_type){
        $this->db->select('*');
        $this->db->from('telesales_renewal_logs');
        $this->db->where('product_type', $product_type);
        $this->db->where('status',1);
        $this->db->where('renewal_status',1);
        $result = $this->db->get()->result();
        if (count($result) > 0) {
            return $result;
        }else{
            return 0;
        }

    }



    public function cron_retails_update($product_type,$next,$limit){

        $this->db->select('*');
        $this->db->from('telesales_renewal_logs');
        $this->db->where('product_type', $product_type);
        $this->db->where('status',1);
        $this->db->where('renewal_status',1);        
        $this->db->limit($limit,$next);
        $this->db->order_by("id","ASC");        
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        }else{
            return 0;
        }

    }
    



    public function retails_update($product_type){

        $this->db->select('*');
        $this->db->from('telesales_renewal_logs');
        $this->db->where('product_type', $product_type);
        $this->db->where('status',1);
        $this->db->where('renewal_status',1);
        $result = $this->db->get()->result();

        if (count($result) > 0) {
            return $result;
        }else{
            return 0;
        }

    }

    public function telesales_renewal_cron($policy_number,$mobile_no,$url,$result){
        $data=[
            'policy_number'=>$policy_number,
            'phone_number'=>$mobile_no,
            'req'=>$url,
            'res'=>$result
        ];
        $this->db->insert('telesales_renewal_cron_logs',$data);        
    }
    

    public function retails_update_renewal_notfound($policy_number,$dob,$phone,$notfound,$response,$renewal_count){
        //status 0 is lapsed, 1 is active, 4 is Renewed from other mode
        $renewal_data=[
            'renewal_req'=>json_encode($notfound),
            'renewal_res'=>json_encode($response),
            'renewal_count'=>$renewal_count+1,
            'renewal_status'=>0,
            'status'=>4,
            'renewal_updated'=>date('Y-m-d H:i:s'),

        ];

        $this->db->where('policy_number',$policy_number);
        $this->db->where('status','1');        

        $get=$this->db->update('telesales_renewal_logs',$renewal_data);

        $this->db->where('policy_number',$policy_number);
        $this->db->where('status',1);
        $this->db->update('tele_renewal_triggers',['status'=>0]);

    }


    public function retails_update_renewal($policy_number,$lead_id,$res,$response,$renewalpolicynumber,$renewalppnumber,$customer_id,$receiptno,$renewalpstartdate,$renewalpexpirydate,$renewal_count){

	if(!empty($lead_id)){
		$renewal_status=1;
	}else{
		$renewal_status=4;
	}
	
        $renewal_data=[
            'renewal_req'=>$res,
            'renewal_res'=>$response,
            'renewed_policy_number'=>$renewalpolicynumber,
            'renewed_policy_proposal_number'=>$renewalppnumber,
            'customer_id'=>$customer_id,
            'receiptno'=>$receiptno,
            'renewal_count'=>$renewal_count+1,
            'renewal_status'=>0,
            'renewed_policy_start_date'=>$renewalpstartdate,
            'renewed_policy_expiry_date'=>$renewalpexpirydate,
	    'status'=>$renewal_status,
            'renewal_updated'=>date('Y-m-d H:i:s'),
        ];

        if(!empty($lead_id)){
          $this->db->where('lead_id',$lead_id);
        }else{
               $this->db->where('policy_number',$policy_number);
	       $this->db->where('status',1);
        }	    
        $get=$this->db->update('telesales_renewal_logs',$renewal_data);

        $this->db->where('policy_number',$policy_number);
        $this->db->where('status',1);
        $this->db->update('tele_renewal_triggers',['status'=>0]);

    }   
    
    public function product_type(){

        $this->db->distinct();
        $this->db->select('product_type');
        $this->db->from('telesales_renewal_logs');
        $result=$this->db->get()->result();

        return $result;

    }    
    public function all_group_retail_data(){

        $lead_id=$this->input->post('lead_id');
        $certificate=$this->input->post('certificateno');

        $this->db->select("*");
        $this->db->from("telesales_renewal_group");

        if(!empty($lead_id)){
            $this->db->where('lead_id',$lead_id);
        }

        if(!empty($certificate)){
            $this->db->where('hb_certificate_no',$certificate);
        }

        $this->db->order_by("id","DESC");
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }		

        $result=$this->db->get()->result();

 
        if(count($result)>0){
            return $result;
        }else{
            return 0;
        }
    }

    public function total_group_retail_data(){

        // $this->db->select("*");
        // $this->db->from("telesales_renewal_group");
        // $result=$this->db->order_by("id","DESC");
        // if(count($result)>0){
        //     return count($result);
        // }else{
        //     return 0;
        // }

        $check_policy =  $this->db->select("count(id) as ids")
        ->from("telesales_renewal_group")
        ->get()
        ->row_array();
        // print_pre($check_policy);
        return $check_policy['ids'];
    }

    
    public function all_audit_data($lead_id){

        $this->db->select("trl.*,trr.created_at, trr.status as link_status");
        $this->db->from("telesales_renewal_logs as trl");
        $this->db->join("tele_renewal_triggers as trr", "trl.lead_id = trr.lead_id", "left");
        $this->db->where('trl.policy_number',$lead_id);
        $this->db->order_by("trl.id","DESC");
        $result=$this->db->get()->result_array();
        if(count($result)>0){
            return $result;
        }else{
            return 0;
        }

    }






    public function renewal_group_issuance($lead_id, $cron){
       
        // echo $lead_id;exit;
        $get_data =  $this->db->select("res,lead_id_group")
            ->from("telesales_renewal_logs")
            ->where("lead_id", $lead_id)
            ->get()
            ->row_array();

        $res = $get_data['res'];
        // $res = json_encode($res);
        $res = json_decode($res, TRUE);

        // echo(json_encode($res));exit;
        // echo(json_encode($res['response']['policyData']));exit;

        $product_array = $res['response']['policyData'];

        // if (!is_array($product_array)){
        //     $product_array = (array) $product_array;
        // }

        $policyData =  array();

        
        $collection_amount = 0;
        foreach($product_array as $key => $single_product){
            // echo json_encode($single_product);exit;
            // echo $single_product['Members'];exit;
            // print_r($single_product['Members'][$key]["Member_Code"]);exit;
            $member_details = array();


            foreach($single_product['Members'] as $member_key => $single_member_res){
                $member_details_single = array(
                    "member_code" => $single_product['Members'][$member_key]["Member_Code"],
                    "sum_insured" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["SumInsured"],
                    "health_return" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["Hr_Amount"],
                );
                array_push($member_details, $member_details_single);
            }
            

            // $member_details_single = json_encode($member_details_single);
            // $member_details_single = preg_replace('/\\\"/',"\"", $member_details_single);
           


            $policyData_single = array(
            "certificate_number" => $single_product["Certificate_number"],
            "master_policy_number" => $single_product["MaterPolicyNumber"],
            "go_green" => '1',
            "premium" => $single_product['premium']['Renewal_Gross_Premium'],
            "product_code" => '',
            "ref_code1" => '',
            "ref_code2" => '',
            "intermediary_code" => '2110705',
            "lead_Id" => $get_data['lead_id_group'],
            "sp_Id" => "SP1298988781",
            "source_name" =>"Axis_Telesales",
            "member_details" => $member_details,
            );

            // $policyData_single = json_encode($policyData_single);
            // $policyData_single = preg_replace('/\\\"/',"\"", $policyData_single);
            array_push($policyData, $policyData_single);
            $collection_amount += $single_product['premium']['Renewal_Gross_Premium'];

            $proposar_name = $single_product["Name_of_the_proposer"];
        }

        // print_pre($policyData);
        // exit;
        $receiptobj[] =  array(
            "company_code"=> "",
            "system_code"=> "",
            "office_location"=> "Mumbai",
            "mode_of_entry"=> "DIRECT",
            "cd_ac_no"=> "",
            "expiry_date"=> "",
            "payer_type"=> "Customer",
            "payer_code"=> "",
            "payment_by"=> "Customer",
            "payment_by_name"=> $proposar_name,
            "payment_by_relationship"=> "Self",
            "collection_amount"=> "$collection_amount",
            "collection_rcvd_date"=> "",
            "collection_mode"=> "Debit/ Credit Card",
            "remarks"=> "",
            "instrument_number"=> "",
            "instrument_date"=> "",
            "bank_name"=> "",
            "branch_name"=> "",
            "micr_no"=> "",
            "bank_location"=> "",
            "cheque_type"=> "",
            "ifsc_code"=> "",
            "receipt_type"=> "NEW PAYMENT",
            "deposit_type"=> "",
            "deposit_bank"=> ""
        );

        $data = array(

            "policyData" => $policyData,
            "receiptobj" => $receiptobj
        );

        $data = json_encode($data);
        $data = preg_replace('/\\\"/',"\"", $data);
        // echo $data;exit;

        $url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalGeneration";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $result = curl_exec($curl);

        $info = curl_getinfo($curl);
        $response_time_renewal = $info['total_time'];
        curl_close($curl);

        $fdata = [
            'lead_id' => $lead_id,
            'req' => json_encode($data),
            'res' => $result,
            'product_id' => "R13",
            'type' => "full_quote_request",
        ];

        $this->db->insert('logs_docs_grp_renewal', $fdata);


        $djson = json_decode($result, TRUE);
        // print_r($djson);exit;
        // echo $djson['error'][0]['ErrorCode'];exit;
        $data_r['status'] = 'success';
        $data_r['lead_id'] = $lead_id;
        if($djson['error'][0]['ErrorCode'] != '00'){
            $data_r['status'] = 'failed';
        }

        if($djson['error'][0]['ErrorCode'] == '00'){
            $cert_arr = [];
            foreach($djson['response'] as $single_response){
            array_push($cert_arr, $single_response['new_certificate_number']);
            }

            $certs = implode(',',$cert_arr);


            $update_policy = $this->db->set('new_coi_number', $certs);
            $this->db->where('lead_id', $lead_id);
            $this->db->update('telesales_renewal_grp_payments');


            $update_policy = $this->db->set('renewed_policy_number', $certs);
            $this->db->set('renewal_status', 0);
            $this->db->set('renewed_policy_start_date', $single_response['policy_start_date']);
            $this->db->set('renewed_policy_expiry_date', $single_response['Policy_end_date']);
            $this->db->set('receiptno', $single_response['receipt_no']);
            $this->db->where('lead_id', $lead_id);
            $this->db->update('telesales_renewal_logs');

        
            //$data['new_coi_number'] = $get_data['new_coi_number'];
            // print_r($data);


       }

            // echo $lead_id;exit;
            $get_data =  $this->db->select("renewed_policy_number")
            ->from("telesales_renewal_logs")
            ->where("lead_id", $lead_id)
            ->get()
            ->row_array();

            $data_r['new_coi_number'] = $get_data['renewed_policy_number'];
            // print_r($data_r);exit;
        


        if($cron == "no"){
            $this->load->telesales_template("thankyou_grp_renewal.php", $data_r);
        }else if($cron == "yes"){
            //echo 'testtt';
            $fdata = [
                'lead_id' => $lead_id,
                'req' => json_encode($data),
                'res' => $result,
                'type' => "group_cron",
            ];
    
            $this->db->insert('telesales_renewal_cron_logs', $fdata);
        }

        
        


        // $djson = json_decode($result, TRUE);
        // print_r($djson);
       

    }


    public function get_grp_extract_data($sgroup){

        $lmdf=$this->input->post('lmdf');
        $lmdt=$this->input->post('lmdt');

        // $this->db->select('*');
        // $this->db->from('telesales_renewal_logs');
        // $this->db->where("product_type","group");
        // $this->db->order_by("id", "DESC");

        $this->db->select("
                            trl.lead_id,
                            trl.lead_id_group,
                            trl.policy_number as hb_certificate_no,
                            trl.status,
                            trl.renewal_status,
                            trl.req,
                            trl.res,
                            trl.avnacode,
                            trl.digital_officer,
                            trl.other_details,
                            trgp.res AS payment_res,
                            trgp.payment_status,
                            trgp.new_coi_number
                        ");

        $this->db->from("telesales_renewal_logs trl");

        $this->db->from("telesales_renewal_grp_payments trgp");

        $this->db->where("trl.product_type","group");

        $this->db->where("trgp.lead_id=trl.lead_id");

        $this->db->where("trgp.payment_status","success");

        $this->db->where("trl.policy_number!=","");

        $this->db->like("trl.policy_number","$sgroup");

        $this->db->where("trl.status",1);
       $this->db->group_by("trl.lead_id");
        $this->db->order_by("trl.id", "DESC");

        if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}
        if (!empty($lmdf) && !empty($lmdt)) {
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "trl.last_updated_on BETWEEN '$lmdf' AND '$lmdt' ");
        }

        $result=$this->db->get()->result();
        // print_r($this->db->last_query());exit;
        
            return $result;

    }

    public function get_grp_extract_data_count($sgroup){

        $lmdf=$this->input->post('lmdf');
        $lmdt=$this->input->post('lmdt');

        // $this->db->select('*');
        // $this->db->from('telesales_renewal_logs');
        // $this->db->where("product_type","group");
        // $this->db->order_by("id", "DESC");

        $this->db->select("
                            trl.lead_id,
                            trl.lead_id_group,
                            trl.policy_number as hb_certificate_no,
                            trl.status,
                            trl.renewal_status,
                            trl.req,
                            trl.res,
                            trl.avnacode,
                            trl.other_details,
                            trgp.res AS payment_res,
                            trgp.payment_status,
                            trgp.new_coi_number
                        ");

        $this->db->from("telesales_renewal_logs trl");

        $this->db->from("telesales_renewal_grp_payments trgp");

        $this->db->where("trl.product_type","group");

        $this->db->where("trgp.lead_id=trl.lead_id");

        $this->db->where("trgp.payment_status","success");

        $this->db->where("trl.policy_number!=","");

        $this->db->like("trl.policy_number","$sgroup");

        $this->db->where("trl.status",1);
       $this->db->group_by("trl.lead_id");
        $this->db->order_by("trl.id", "DESC");

        if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}
        if (!empty($lmdf) && !empty($lmdt)) {
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "trl.last_updated_on BETWEEN '$lmdf' AND '$lmdt' ");
        }

        $result=$this->db->get()->result();
        // print_r($this->db->last_query());exit;
        
            return count($result);

    }


    


    public function get_do_id($do_id){
        $data = $this->db
        ->select('do_id')
        ->from('tls_master_do')
        ->where(' `id` ', $do_id)
        ->limit('1')
        ->get()
        ->row_array();

        return $data['do_id'];
    }

    public function all_retail_data_group_phase2(){
        
        // print_r($this->input->post());exit;
        extract($this->input->post());
        $this->db->select('*,DATEDIFF(NOW(),created_at) as days');
        $this->db->from('group_mod_create');
        $this->db->where('status',1);
        

        if (!empty($policy)) {
            $this->db->like("coi_number", $policy);
        }

        if (!empty($epolicy)) {
            $this->db->like("new_certificate_number", $epolicy);
        }

        if (!empty($occcenter)) {
            $this->db->like("do_location", $occcenter);
        }

        // 1-12-2021
        if (!empty($current_status)) {
            if($current_status == '1'){
                $this->db->where_not_in('disposition_master_id', ['4','5','6','48','51']);
            }else{
                $current_status = explode(',',$current_status);
                $this->db->where_in('disposition_master_id', $current_status);

            }
        }

        if (!empty($lmdf) && !empty($lmdt)) {
            // $this->db->where('last_updated_on >=', $lmdf);
            // $this->db->where('last_updated_on =', $lmdt);
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "updated_at BETWEEN '$lmdf' AND '$lmdt' ");
        }
        // $this->db->group_by('lead_id_grp');        

        $telSalesSession = $this->session->userdata('telesales_session');
        // print_pre($telSalesSession);exits;
        if($telSalesSession['is_admin'] != "1"){
            $do_id = $telSalesSession['agent_id'];
            $do_id = encrypt_decrypt_password($do_id, "D");
            if($_SESSION['telesales_session']['outbound'] == '2'){
                 $this->db->where('do_id',$do_id);
            }elseif($_SESSION['telesales_session']['outbound'] == '1'){
                $this->db->where('av_id', $do_id);
            }
        }
        $this->db->order_by('id','DESC');
        $all_data=$this->db->get()->result_array();
        // echo $this->db->last_query();exit;

        return $all_data;

    }

    public function all_retail_data_group_count(){
        
        extract($this->input->post());
        $this->db->select('*,DATEDIFF(NOW(),created_at) as days');
        $this->db->from('group_mod_create');
        $this->db->where('status',1);
        

        if (!empty($policy)) {
            $this->db->like("coi_number", $policy);
        }

        if (!empty($epolicy)) {
            $this->db->like("new_certificate_number", $epolicy);
        }

        if (!empty($occcenter)) {
            $this->db->like("do_location", $occcenter);
        }

        // if (!empty($current_status)) {
        //     $this->db->like("new_certificate_number", $epolicy);
        // }

        if (!empty($lmdf) && !empty($lmdt)) {
            // $this->db->where('last_updated_on >=', $lmdf);
            // $this->db->where('last_updated_on =', $lmdt);
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "updated_at BETWEEN '$lmdf' AND '$lmdt' ");
        }
        // $this->db->group_by('lead_id_grp');        

        $telSalesSession = $this->session->userdata('telesales_session');
        // print_pre($telSalesSession);exits;
        if($telSalesSession['is_admin'] != "1"){
            $do_id = $telSalesSession['agent_id'];
            $do_id = encrypt_decrypt_password($do_id, "D");
            if($_SESSION['telesales_session']['outbound'] == '2'){
                 $this->db->where('do_id',$do_id);
            }elseif($_SESSION['telesales_session']['outbound'] == '1'){
                $this->db->where('av_id', $do_id);
            }
        }
        $this->db->order_by('id','DESC');
        $all_data=$this->db->get()->result_array();
        // echo $this->db->last_query();exit;
        
        if($all_data){
            return count($all_data);
        }else{
            return 0;
        }
        
    }


    public function check_group_lead_payment_status($lead){

        $this->db->select('lead_id,payment_status');
        $this->db->from('telesales_renewal_grp_payments');
        $this->db->where('lead_id',$lead);
        $this->db->where('payment_status','success');
        $result=$this->db->get()->row_array();

        return $result;
    }

    public function group2_last_dipostion($lead_id){

        $this->db->select('gmdm.disposition');
        $this->db->from('group_mod_disposition as gmd');
        $this->db->from('group_mod_disposition_master as gmdm');
        $this->db->where('gmd.disposition=gmdm.id');
        $this->db->where('gmd.lead_id',$lead_id);
        $this->db->order_by('gmd.id','DESC');
        $result=$this->db->get()->row_array();
        // print_r($result);exit;
        return $result['disposition'];
    }

    public function group2_inception_date($lead_id){

        $get_res = $this
        ->db
        ->select("res")
        ->from("group_mod_logs")
        ->where("lead_id", $lead_id)
        ->where("type", "full_quote_request")
        ->get()
        ->row_array();
        // print_r($result);exit;
        if($get_res){
            $res = $get_res['res'];
            $res = json_decode($res, TRUE);
            $policy_start_date = $res['response'][0]['policy_start_date'];
            $policy_start_date = date("Y-m-d", strtotime($policy_start_date));
            return $policy_start_date;
        }else{
            return '';
        }
        
    }

    public function group2_last_av_mapped($lead_id){

        $this->db->select('tgmo.agent_name,tgmo.center');
        $this->db->from('group_mod_create as gmc');
        $this->db->from('tls_agent_mst_outbound as tgmo');
        $this->db->where('gmc.av_id=tgmo.id');
        $this->db->where('gmc.lead_id',$lead_id);
        $this->db->order_by('gmc.id','DESC');
        $result=$this->db->get()->row_array();
        // print_r($result);exit;
        return $result;


    }

    public function group2_hr_amount($lead_id){

        //get HR Amount
        $get_total_hr_amount = $this
         ->db
         ->select("SUM(Hr_Amount) as total_hr_amount")
         ->from("group_mod_members")
         ->where("lead_id", $lead_id)
        //  ->where("Relation", "Self")
        //  ->where("coi_number LIKE '%GHI%'")
         ->group_by("NAME")
         ->get()
         ->result_array();

         $total_hr_amount = 0;

         foreach($get_total_hr_amount as $single_hr_amount){
            $total_hr_amount += $single_hr_amount['total_hr_amount'];
         }

        return $total_hr_amount;
    }

    // update - 16-11-2021
    public function group2_net_premium($lead_id){

        $get_total_hr_amount = $this
        ->db
        ->select("res")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        $res = $get_total_hr_amount['res'];
        $res = json_decode($res, TRUE);

        $product_array = $res['response']['policyData'];
        $net_premium = 0;
        foreach ($product_array as $key => $single_product) {
            $net_premium += $single_product['PolicyproductComponents'][0]['NetPremium'] . ',';
        }

        // print_pre($net_premium);exit;

        return $net_premium;
    }



}


// $route['tele_renewal'] = "Telesales/Renewal_telesales/renewal";
// $route['tele_renewalt'] = "Telesales/Renewal_telesales/renewal_token";
// $route['tele_renewald'] = "Telesales/Renewal_telesales/get_renewal_data";
// $route['tele_renewalsftp'] = "cron/Telesalesrenewal/connectServer";
