<?PHP
class Telelead_m extends CI_Model
{
    function __construct()
    {//echo 123;die;
        parent::__construct();
        //checklogin();
        $this->db = $this->load->database('telesales_fyntune',true);
        $telesession = $this->session->userdata('telesales_session');
        $this->agent_id = encrypt_decrypt_password($telesession['agent_id'], 'D');
        $this->admin = $telesession['is_admin'];
        $this->is_region_admin = $telesession['is_region_admin'];
        $this->load->model('telelogs/Logs_m');


    }
    public $table = "employee_details as l";
    //ankita dedeup junk changes added apr.created_date field in select query

    //upendra - maker/checker - 30-07-2021 - add l.makerchecker,.l.is_makerchecker_journey column
    public $select_column = array("l.makercheckerremark","l.makerchecker","l.deductable","l.is_makerchecker_journey","l.emp_id","l.axis_process", "l.lead_id","IFNULL(l.emp_lastname, '') as 'last_name'", "l.emp_firstname", "'Group Activ Health-Tele' as 'Plan'", "IFNULL(SUM(p.premium), 0) as 'premium'", "if(p.status is null, IF(DATEDIFF(NOW(),l.created_at) > 15 , 'rejected', 'proposal not created'),p.status) as status", "IFNULL(p.modified_date, '') as 'modified_date'","IFNULL(p.sms_trigger_status, '') as sms_trigger_status", "l.mob_no", "l.saksham_id", "IFNULL(GROUP_CONCAT(DISTINCT apr.certificate_number), '') as 'policy_no'","apr.created_date as 'policy_issuance_date'","tam.id as 'agent_id'","tam.agent_name","l.created_at","IFNULL(GROUP_CONCAT(DISTINCT p.proposal_no), '') as 'proposal_no'","IFNULL(l.av_remark, '') as 'av_remark'","IFNULL(l.preferred_contact_time, '') as 'preferred_contact_time'","l.product_id","tbat.imd_code","l.lead_flag","pd.TxRefNo","tbat.rec_manager_code","GROUP_CONCAT(apr.quotation_no) as quotation_no");
    public $order_column = array(null, "l.lead_id", "l.emp_firstname", "Plan", "p.premium", "p.status", "p.modified_date", "l.mob_no", "l.saksham_id", "apr.certificate_number");
    public function make_query()
    {
        $is_maker_checker = $_POST['is_maker_checker'];
        //upendra - maker/checker - 30-07-2021
        if (!empty($is_maker_checker)) {
            $this->select_column = array("l.makerchecker","l.is_makerchecker_journey","l.emp_id","l.axis_process", "l.lead_id","IFNULL(l.emp_lastname, '') as 'last_name'", "l.emp_firstname", "'Group Activ Health-Tele' as 'Plan'", "IFNULL(SUM(p.premium), 0) as 'premium'", "if(p.status is null, IF(DATEDIFF(NOW(),l.created_at) > 15 , 'rejected', 'proposal not created'),p.status) as status", "IFNULL(p.modified_date, '') as 'modified_date'","IFNULL(p.sms_trigger_status, '') as sms_trigger_status", "l.mob_no", "l.saksham_id", "IFNULL(GROUP_CONCAT(DISTINCT apr.certificate_number), '') as 'policy_no'","apr.created_date as 'policy_issuance_date'","tam.base_id as 'agent_id'","tam.base_agent_name","l.created_at","IFNULL(GROUP_CONCAT(DISTINCT p.proposal_no), '') as 'proposal_no'","IFNULL(l.av_remark, '') as 'av_remark'","IFNULL(l.preferred_contact_time, '') as 'preferred_contact_time'","l.product_id","tbat.imd_code","tbat.rec_manager_code","l.picked_do_by","GROUP_CONCAT(apr.quotation_no) as quotation_no");
        }


        if ($this->admin == '1') {
            $this->order_column = [];
            $this->order_column = array(null, "l.lead_id", "l.emp_firstname", "Plan", "p.premium", "p.status", "p.modified_date","p.sms_trigger_status", "l.mob_no", "l.saksham_id", "apr.certificate_number","tam.agent_name","l.created_at","l.axis_location");
            $xlfields_admin = ["tam.agent_id as 'av_code'","l.emp_firstname", "IFNULL(pd.txndate, '') as 'txndate'", "IFNULL(pd.TxStatus, '') as 'TxStatus'", "IFNULL(apr.start_date, '') as 'policystartdate'", "IFNULL(p.sum_insured, '')as 'sum_insured'", "l.bdate", "IFNULL(tam.tl_name, '') as 'tl_name'", "IFNULL(tam.am_name, '') as 'am_name'", "IFNULL(tam.om_name, '') as 'om_name'", "IFNULL(axis_location.axis_location, '') as 'axis_location'", "IFNULL(axis_lob.axis_lob, '') as 'axis_lob'","IFNULL(axis_vendor.axis_vendor, '') as 'axis_vendor'", "IFNULL(l.emp_city, '') as 'emp_city'", "IFNULL(l.emp_state, '') as 'emp_state'", "IFNULL(l.emp_pincode, '') as 'emp_pincode'", "l.mob_no", "IFNULL(l.auto_renewal, '') as 'businessType'", "IFNULL(edmst.emp_sub_type_id, 'NO') as 'Chronic'","IFNULL(l.email, '') as 'email'","IFNULL(l.preferred_contact_date, '') as 'preferred_contact_date'","IFNULL(l.agent_name, '') as 'base_caller_name'","IFNULL(l.agent_id, '') as 'base_caller_id',
          (select req from logs_docs ld where ld.lead_id=l.lead_id and type='sms_logs_redirect_summary' order by id desc limit 1 ) as sms_logs_redirect_summary "];


            //upendra - maker/checker - 30-07-2021
            if (!empty($this->input->post('is_maker_checker'))) {
                $this->order_column = array(null, "l.lead_id", "l.emp_firstname", "Plan", "p.premium", "p.status", "p.modified_date","p.sms_trigger_status", "l.mob_no", "l.saksham_id", "apr.certificate_number","tam.base_agent_name","l.created_at","l.axis_location");
                $xlfields_admin = ["tam.base_agent_id as 'av_code'","l.emp_firstname", "IFNULL(pd.txndate, '') as 'txndate'", "IFNULL(pd.TxStatus, '') as 'TxStatus'", "IFNULL(apr.start_date, '') as 'policystartdate'", "IFNULL(p.sum_insured, '')as 'sum_insured'", "l.bdate", "IFNULL(tam.tl_name, '') as 'tl_name'", "IFNULL(tam.am_name, '') as 'am_name'", "IFNULL(tam.om_name, '') as 'om_name'", "IFNULL(axis_location.axis_location, '') as 'axis_location'", "IFNULL(axis_lob.axis_lob, '') as 'axis_lob'","IFNULL(axis_vendor.axis_vendor, '') as 'axis_vendor'", "IFNULL(l.emp_city, '') as 'emp_city'", "IFNULL(l.emp_state, '') as 'emp_state'", "IFNULL(l.emp_pincode, '') as 'emp_pincode'", "l.mob_no", "IFNULL(l.auto_renewal, '') as 'businessType'", "IFNULL(edmst.emp_sub_type_id, 'NO') as 'Chronic'","IFNULL(l.email, '') as 'email'","IFNULL(l.preferred_contact_date, '') as 'preferred_contact_date'","IFNULL(l.agent_name, '') as 'base_caller_name'","IFNULL(l.agent_id, '') as 'base_caller_id'"];
            }



            $this->select_column = array_merge($this->select_column, $xlfields_admin);
        }
        $this->db->select($this->select_column);
        $this->db->from($this->table);

        //upendra - maker/checker - 30-07-2021
        if (!empty($is_maker_checker)) {
            $this->db->join('tls_base_agent_tbl tam', 'l.assigned_to = tam.base_id');
        }else{
            $this->db->join('tls_agent_mst tam', 'l.assigned_to = tam.id');
        }


        $this->db->join('proposal p', 'l.emp_id = p.emp_id', 'left');

        $this->db->join('tls_base_agent_tbl tbat', 'l.agent_id = tbat.base_agent_id', 'left');

        if ($this->admin == '1') {
            // $this->db->join('family_relation fr', 'l.emp_id = fr.emp_id');
            //$this->db->join('employee_policy_member epm', 'fr.family_relation_id = epm.family_relation_id', 'left');
            $this->db->join('tls_axis_lob  axis_lob', 'axis_lob.axis_lob_id = l.axis_lob', 'left');
            // $this->db->join('payment_details pd', 'p.id = pd.proposal_id', 'left');
            $this->db->join('employee_declare_member_sub_type edmst', 'l.emp_id = edmst.emp_id', 'left');
            $this->db->join('tls_axis_location axis_location', 'l.axis_location = axis_location.axis_loc_id', 'left');
            $this->db->join('tls_axis_vendor axis_vendor', 'l.axis_vendor = axis_vendor.axis_vendor_id', 'left');

        }

        $this->db->join('api_proposal_response apr', 'p.proposal_no = apr.proposal_no_lead', 'left');
        // $this->db->join('api_proposal_response apr', 'p.emp_id = apr.emp_id', 'left');
        $this->db->join('payment_details pd', 'p.id = pd.proposal_id', 'left');

        $where = '(l.product_id="R06" or l.product_id = "T01" or l.product_id = "T03")';

        if ($this->admin == '0') {
            $where .="AND DATE(l.created_at)>=DATE(NOW() - INTERVAL 45 DAY)";
        }
        if ($this->admin=='1'&&$this->is_region_admin=='1') {
            $where .="AND DATE(l.created_at)>=DATE(NOW() - INTERVAL 90 DAY)";
        }

        $this->db->where($where);


        if (!empty($this->input->post('mobno'))) {
            $this->db->like("l.mob_no", $this->input->post('mobno'));
        }
        if (!empty($this->input->post('sakshamid'))) {
            $this->db->like("l.lead_id", $this->input->post('sakshamid'));
        }
        if (!empty($this->input->post('status'))) {
            if (strtolower($this->input->post('status')) == 'proposal not created') {
                //$this->db->where("LOWER(p.status)", null);
                $where = '((LOWER(p.status) is null and (DATEDIFF(NOW(),l.created_at) < 15)))';
                $this->db->where($where);

            } else {
                if (strtolower($this->input->post('status') == 'payment link not triggered')) {
                    $this->db->where("LOWER(p.status)", "payment pending");
                    $this->db->where("sms_trigger_status", 0);

                } elseif (strtolower($this->input->post('status') == 'payment pending')) {
                    $this->db->where("LOWER(p.status)", "payment pending");
                    $this->db->where("sms_trigger_status", 1);

                }
                elseif (strtolower($this->input->post('status') == 'rejected')) {
                    $where = '((LOWER(p.status) = "rejected" or (LOWER(p.status) is null and DATEDIFF(NOW(),l.created_at) > 15)))';
                    $this->db->where($where);
                }


                else {
                    $this->db->where("LOWER(p.status)", strtolower($this->input->post('status')));
                    // $where = '((LOWER(p.status) = '.strtolower($this->input->post('status')).' or (LOWER(p.status) is null and DATEDIFF(NOW(),l.created_at) > 15)))';
                    //$this->db->where($where);
                }


            }
        }
        if (!empty($this->input->post('moddate'))) {
            $dates = explode('-', $this->input->post('moddate'));
            $start_date = trim(str_replace('/', '-', $dates[0])).'0:0:0';
            $end_date = trim(str_replace('/', '-', $dates[1])).'23:59:59';
            $this->db->where("l.created_at >= ", date('Y-m-d H:i:s', strtotime($start_date)));
            $this->db->where("l.created_at <= ", date('Y-m-d H:i:s', strtotime($end_date)));
        }
        //ankita new  filter filed added
        if (!empty($this->input->post('issuancedate'))) {
            $dates = explode('-', $this->input->post('issuancedate'));
            $start_date = trim(str_replace('/', '-', $dates[0])).'0:0:0';
            $end_date = trim(str_replace('/', '-', $dates[1])).'23:59:59';
            $this->db->where("apr.created_date >= ", date('Y-m-d H:i:s', strtotime($start_date)));
            $this->db->where("apr.created_date <= ", date('Y-m-d H:i:s', strtotime($end_date)));
        }
        if(!empty($this->input->post('policyNumber'))){
            $policyNumber=$this->input->post('policyNumber');
            $this->db->where("apr.certificate_number", $policyNumber);
        }
        if (!empty($this->input->post('location'))) {
            $this->db->where("LOWER(l.axis_location)", strtolower($this->input->post('location')));
        }



        if ($this->is_region_admin=='1' && empty($this->input->post('is_maker_checker'))) {
            $this->db->where("LOWER(l.axis_process)", strtolower($this->input->post('lob')));
        }

        //upendra - maker/checker - 30-07-2021
        if (!empty($this->input->post('axis_process_filter'))) {
            $this->db->where("l.axis_process", ($this->input->post('axis_process_filter')));
        }

        if ($this->admin == '0') {
            $this->db->where("l.assigned_to", $this->agent_id);
        }


        //upendra - maker/checker - 30-07-2021

        if (!empty($is_maker_checker)) {
            $this->db->where("l.is_makerchecker_journey", "yes");
            // $this->db->where("(DATE(l.created_at)=DATE(NOW()-INTERVAL 45 DAY))");
        }else{
            $this->db->where("(l.is_makerchecker_journey = 'no' OR l.is_makerchecker_journey IS NULL)");
            // ->or_where("l.is_makerchecker_journey IS NULL ");
        }


        $this->db->group_by('l.lead_id');
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('emp_id', 'DESC');
        }

    }

    public function insert_lead()
    {
        $response['status'] = false;
        $response['message'] = 'Something Went Wrong';
        //check lead_id

        //


        // $sakshamidcheck = $this->db->select("saksham_id")->from("employee_details")->where("saksham_id",$saksham_id)->get()->row_array();
        //$leadcheckaxis = $this->db2->select("saksham_id")->from("employee_details")->where("saksham_id",$this->input->post('saksham_id', true))->get()->row_array();

        //cust id

        // if(!empty($sakshamidcheck)){
        // 	$response['message'] = 'Saksham id already exists';
        // 	return $response;
        // }

        //check for saem saksham id
        $ramdom_number = 0;
        $logReqData = array();
        $aCustUniqueID = $this->db->select("id,unique_number,c_date")->from("cust_id_unique_number")->get()->row_array();
        if (empty($aCustUniqueID)) {
            //	if not found any record
            $ramdom_number = $this->getRandomUniqueNumber(15);
            $aInsertNumber = ["unique_number" => $ramdom_number, "c_date" => date('Y-m-d')];
            $this->db->insert('cust_id_unique_number', $aInsertNumber);

            $logReqData = $aInsertNumber;
        } else {
            //already found any record
            $curr_date = date('Y-m-d');
            if (strtotime($curr_date) == strtotime($aCustUniqueID['c_date'])) {
                //if page request on same date then increment unique number
                $ramdom_number = ++$aCustUniqueID['unique_number'];
                $aUpdateNumber = ["unique_number" => $ramdom_number];
                $this->db->where('id', $aCustUniqueID['id']);
                $this->db->update('cust_id_unique_number', $aUpdateNumber);
            } else {
                //if page request but curr_date >  c_date then update date
                $ramdom_number = $this->getRandomUniqueNumber(15);
                $aUpdateNumber = ["unique_number" => $ramdom_number, "c_date" => date('Y-m-d')];
                $this->db->where('id', $aCustUniqueID['id']);
                $this->db->update('cust_id_unique_number', $aUpdateNumber);
            }
            $logReqData = $aUpdateNumber;
        }

        //
        $lead_id = $this->generate_lead_id();

        if($this->input->post('saksham_id')==''){
            $saksham_id='';
        }else{
            $saksham_id=$this->input->post('saksham_id');
        }

        //upendra - maker/checker - 30-07-2021
        if(isset($_SESSION['telesales_session']['is_maker_checker'])){
            $is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];
        }else{
            $is_maker_checker = 'no';
        }


        //upendra - maker/checker - 30-07-2021 - add 'is_makerchecker_journey'

        $insert_array = [
            "lead_id" => $lead_id,
            //"saksham_id" => $saksham_id,
            "salutation" => $this->input->post('salutation', true),
            "emp_firstname" => $this->input->post('first_name', true),
            "emp_lastname" => empty($this->input->post('last_name', true)) ? '.' : $this->input->post('last_name', true),
            "gender" => $this->input->post('gender', true),
            "mob_no" => $this->input->post('mobile_number', true),
            "bdate" => $this->input->post('dob', true),
            "email" => $this->input->post('email_id', true),
            'product_id' => 'R06',
            'created_at' => date('Y-m-d H:i:s'),
            'assigned_to' => $this->agent_id,
            'assigned_by' => $this->agent_id,
            'cust_id' => $ramdom_number,
            'GCI_optional' => 'No',
            'axis_process'=> $this->input->post('axis_process', true),
            'is_makerchecker_journey' =>$is_maker_checker

        ];
    //   print_r($this->db->database);exit;

        $this->db->insert('employee_details', $insert_array);
    //    print_r($this->db->last_query());exit;
        if ($this->db->affected_rows()) {
            $family_relation_empid = $this->db->insert_id();
            $this->db->insert('family_relation', ['family_id' => 0, 'emp_id' => $family_relation_empid]);
            $parent_id = $this->db->select('p.policy_parent_id')
                ->from('employee_details l')
                ->join('product_master_with_subtype p', 'l.product_id = p.product_code')
                ->where('l.emp_id', $family_relation_empid)
                ->get()
                ->row_array();
            $_SESSION['telesales_session']['emp_id'] = $family_relation_empid;
            $_SESSION['telesales_session']['parent_id'] = $parent_id['policy_parent_id'];
            $response['lead_id'] =encrypt_decrypt_password($lead_id);
            $response['status'] = true;
            $response['message'] = 'Lead Successfully Created';
            $logs_array['data'] = ["type" => "insert_lead","req" => json_encode($insert_array), "lead_id" => $insert_array["lead_id"],"product_id" => 'R06'];
            $this->Logs_m->insertLogs($logs_array);
        }
        return $response;

    }

    public function generate_lead_id(){
        $lead_id = time();
        $leadcheck = $this->db->select("lead_id")->from("employee_details")->where("lead_id",$lead_id)->get()->row_array();
     //   $leadcheckaxis = $this->db2->select("lead_id")->from("employee_details")->where("lead_id",$lead_id)->get()->row_array();

        //cust id

        if(!empty($leadcheck) ){
            $this->generate_lead_id();
        }
        return $lead_id;
    }

    function getRandomUniqueNumber($length = 15) {
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
    public function get_net_premium_tele($lead_id){
        $empData = $this->db->select('emp_id')
            ->from('employee_details')
            ->where('lead_id', $lead_id)
            ->get()
            ->row_array();

        $emp_id = $empData['emp_id'];

        $proposal = $this
            ->db
            ->select("id,policy_detail_id,sum_insured,product_id,premium")
            ->from("proposal")
            ->where("emp_id", $emp_id)
            ->get()
            ->result_array();

        $output = 0;
        foreach($proposal as $single_proposal){

            $suminsured_type = $this->db->select('suminsured_type')
                ->from('employee_policy_detail')
                ->where('policy_detail_id', $single_proposal['policy_detail_id'])
                ->get()
                ->row_array();

            if($suminsured_type['suminsured_type'] == 'family_construct_age'){

                $net_premium = $this->db->select('net_premium_without_gst')
                    ->from('family_construct_age_wise_si')
                    ->where('policy_detail_id', $single_proposal['policy_detail_id'])
                    ->where('sum_insured', $single_proposal['sum_insured'])
                    ->where('PremiumServiceTax', $single_proposal['premium'])
                    ->get()
                    ->row_array();

                $net_premium = $net_premium['net_premium_without_gst'];

                $output += $net_premium;



            }

            if($suminsured_type['suminsured_type'] == 'family_construct'){

                $net_premium = $this->db->select('net_premium_without_gst')
                    ->from('family_construct_wise_si')
                    ->where('policy_detail_id', $single_proposal['policy_detail_id'])
                    ->where('sum_insured', $single_proposal['sum_insured'])
                    ->where('PremiumServiceTax', $single_proposal['premium'])
                    ->get()
                    ->row_array();

                $net_premium = $net_premium['net_premium_without_gst'];

                $output += $net_premium;

            }

            if($suminsured_type['suminsured_type'] == 'memberAge'){

                $family_relation_id = $this->db->select('GROUP_CONCAT(family_relation_id) as family_relation_id')
                    ->from('family_relation')
                    ->where('emp_id', $emp_id)
                    ->get()
                    ->row_array();

                // echo $this->db->last_query();exit;
                $family_relation_id = explode(',',$family_relation_id['family_relation_id']);
                // print_pre($family_relation_id);exit;

                $adult_count = $this->db->select('policy_mem_sum_premium,fr_id')
                    ->from('employee_policy_member')
                    ->where_in('family_relation_id', $family_relation_id)
                    ->where_in('fr_id', ['0','1'])
                    // ->group_by('policy_detail_id')
                    ->where('policy_detail_id',$single_proposal['policy_detail_id'])
                    // ->limit(1)
                    ->get()
                    ->result_array();

                // echo $this->db->last_query();exit;
                // echo '___adult are '.$adult_count['adult_count'];exit;

                // if($adult_count['adult_count'] == '2'){
                //     $single_proposal['premium'] = $single_proposal['premium']/2;
                // }

                foreach($adult_count as $key=>$single_adult){

                    $net_premium = $this->db->select('net_premium_without_gst')
                        ->from('policy_creation_age')
                        ->where('policy_id', $single_proposal['policy_detail_id'])
                        ->where('sum_insured', $single_proposal['sum_insured'])
                        ->where('premium_with_tax', $single_adult['policy_mem_sum_premium'])
                        ->get()
                        ->row_array();

                    $net_premium = $net_premium['net_premium_without_gst'];
                    // if($key == 1){
                    //     echo $net_premium;exit;
                    // }

                    $output += $net_premium;

                }




            }





        }

        return $output;
    }
    public function make_datatables()
    {
        $this->make_query();

        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        $query = $this->db->get();
        // $this->db->cache_on();
         //print_r($this->db->last_query());exit;
        //  echo $this->db->last_query();exit;

        return $query->result();

        // $z = $query->result();
        // $this->db->cache_off();
    }
    public function get_all_data()
    {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where('l.product_id','R06');
        $this->db->or_where('l.product_id','T01');
        $this->db->or_where('l.product_id','T03');
        return $this->db->count_all_results();
        // if ($this->admin == '0') {

        // $query = $this->db->select('count(*) as "all"')->from('employee_details,tls_agent_mst')->where('employee_details.assigned_to',$this->agent_id)->where('employee_details.assigned_to = tls_agent_mst.id')->where('employee_details.product_id','R06')->get()->row_array();



        // return $query['all'];
        //echo $this->db->last_query();exit;

        // }else{

        // $query = $this->db->select('count(*) as "all"')->from('employee_details,tls_agent_mst')->where('employee_details.product_id','R06')->where('employee_details.assigned_to = tls_agent_mst.id')->get()->row_array();

        //echo $this->db->last_query();exit;
        // return $query['all'];
        //return $query->num_rows();


        // }


    }
    public function get_filtered_data()
    {
        $this->make_query();
        $query = $this->db->get();
        $query->num_rows();
        return $query->num_rows();
        // echo $this->db->last_query();exit;
        // $this->db->where("l.assigned_to", $this->agent_id);
        // if ($this->admin == '0') {

        // $query = $this->db->select('count(*) as "all"')->from('employee_details,tls_agent_mst')
        // ->where('employee_details.assigned_to',$this->agent_id)
        // ->where('employee_details.assigned_to = tls_agent_mst.id')
        // ->where('employee_details.product_id','R06')
        // ->or_where('employee_details.product_id','T01')
        // ->get()
        // ->row_array();
        // return $query['all'];
        //echo $this->db->last_query();exit;


        // }else{


        // $query = $this->db->select('count(*) as "all"')->from('employee_details,tls_agent_mst')
        // ->where('employee_details.product_id','R06')
        // ->or_where('employee_details.product_id','T01')
        // ->where('employee_details.assigned_to = tls_agent_mst.id')
        // ->get()
        // ->row_array();
        //print_pre($query);exit;
        //echo $query['all'];exit;
        // return $query['all'];
        //return $query->num_rows();



        // }
    }
    function get_all_agents(){
        $output = $this->db->select('id,agent_name')
            ->from("tls_agent_mst")
            ->get()
            ->result_array();

        $options = "";

        foreach ($output as $value) {
            $options .= "<option value=".trim(encrypt_decrypt_password($value['id'])).">".(trim($value['agent_name']))."</option>";
        }

        return $options;
    }

    public function makerchecker_datatables(){


        $this->maker_query();

        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();

         echo $this->db->last_query();exit;

        return $query->result();

    }
    public function maker_query()
    {



        $select_column = array("l.makercheckerremark","l.makerchecker","l.is_makerchecker_journey","l.emp_id","l.axis_process", "l.lead_id","IFNULL(l.emp_lastname, '') as 'last_name'", "l.emp_firstname", "'Group Activ Health-Tele' as 'Plan'", "IFNULL(SUM(p.premium), 0) as 'premium'", "if(p.status is null, IF(DATEDIFF(NOW(),l.created_at) > 15 , 'rejected', 'proposal not created'),p.status) as status", "IFNULL(p.modified_date, '') as 'modified_date'","IFNULL(p.sms_trigger_status, '') as sms_trigger_status", "l.mob_no", "l.saksham_id", "IFNULL(GROUP_CONCAT(DISTINCT apr.certificate_number), '') as 'policy_no'","apr.created_date as 'policy_issuance_date'","tam.base_id as 'agent_id'","tam.base_agent_name","l.created_at","IFNULL(GROUP_CONCAT(DISTINCT p.proposal_no), '') as 'proposal_no'","IFNULL(l.av_remark, '') as 'av_remark'","IFNULL(l.preferred_contact_time, '') as 'preferred_contact_time'","l.product_id","tbat.imd_code","l.lead_flag");
        $order_column = array(null, "l.lead_id", "l.emp_firstname", "Plan", "p.premium", "p.status", "p.modified_date", "l.mob_no", "l.saksham_id", "apr.certificate_number");

        $this->order_column = [];
        $xlfields_admin = ["tam.base_agent_id as 'av_code'","l.emp_firstname", "IFNULL(pd.txndate, '') as 'txndate'", "IFNULL(pd.TxStatus, '') as 'TxStatus'", "IFNULL(apr.start_date, '') as 'policystartdate'", "IFNULL(p.sum_insured, '')as 'sum_insured'", "l.bdate", "IFNULL(tam.tl_name, '') as 'tl_name'", "IFNULL(tam.am_name, '') as 'am_name'", "IFNULL(tam.om_name, '') as 'om_name'", "IFNULL(axis_location.axis_location, '') as 'axis_location'", "IFNULL(axis_lob.axis_lob, '') as 'axis_lob'","IFNULL(axis_vendor.axis_vendor, '') as 'axis_vendor'", "IFNULL(l.emp_city, '') as 'emp_city'", "IFNULL(l.emp_state, '') as 'emp_state'", "IFNULL(l.emp_pincode, '') as 'emp_pincode'", "l.mob_no", "IFNULL(l.auto_renewal, '') as 'businessType'", "IFNULL(edmst.emp_sub_type_id, 'NO') as 'Chronic'","IFNULL(l.email, '') as 'email'","IFNULL(l.preferred_contact_date, '') as 'preferred_contact_date'","IFNULL(l.agent_name, '') as 'base_caller_name'","l.lead_flag","IFNULL(l.agent_id, '') as 'base_caller_id'"];
        $this->select_column = array_merge($select_column, $xlfields_admin);

        $this->db->select($this->select_column);
        $this->db->from($this->table);
        // $this->db->join('tls_agent_mst tam', 'l.assigned_to = tam.id');

        $this->db->join('tls_base_agent_tbl tam', 'l.assigned_to = tam.base_id');

        $this->db->join('proposal p', 'l.emp_id = p.emp_id', 'left');

        $this->db->join('tls_base_agent_tbl tbat', 'l.agent_id = tbat.base_agent_id', 'left');
        // $this->db->join('tls_axis_lob  axis_lob', 'axis_lob.axis_lob_id = l.axis_lob', 'left');
        // $this->db->join('family_relation fr', 'l.emp_id = fr.emp_id');
        //$this->db->join('employee_policy_member epm', 'fr.family_relation_id = epm.family_relation_id', 'left');
        $this->db->join('tls_axis_lob  axis_lob', 'axis_lob.axis_lob = tbat.lob', 'left');
        $this->db->join('payment_details pd', 'p.id = pd.proposal_id', 'left');
        $this->db->join('employee_declare_member_sub_type edmst', 'l.emp_id = edmst.emp_id', 'left');
        $this->db->join('tls_axis_location axis_location', 'l.axis_location = axis_location.axis_loc_id', 'left');
        $this->db->join('tls_axis_vendor axis_vendor', 'l.axis_vendor = axis_vendor.axis_vendor_id', 'left');

        $this->db->join('api_proposal_response apr', 'p.proposal_no = apr.proposal_no_lead', 'left');
        $this->db->join('employee_disposition edisposition ', 'edisposition.emp_id=l.emp_id', 'left');


        // $this->db->join('api_proposal_response apr', 'p.emp_id = apr.emp_id', 'left');

        $where = '(l.product_id="R06" or l.product_id = "T01" or l.product_id = "T03") AND (DATE(l.created_at)>=DATE(NOW()-INTERVAL 45 DAY)) AND (l.makerchecker="checker") AND (l.picked_do_by IS NULL OR l.picked_do_by=""  OR l.picked_do_by="'.$this->agent_id.'")';

        $this->db->where($where);


        if (!empty($this->input->post('mobno'))) {
            $this->db->like("l.mob_no", $this->input->post('mobno'));
        }
        if (!empty($this->input->post('sakshamid'))) {
            $this->db->like("l.lead_id", $this->input->post('sakshamid'));
        }
        if (!empty($this->input->post('status'))) {
            if (strtolower($this->input->post('status')) == 'proposal not created') {
                //$this->db->where("LOWER(p.status)", null);
                $where = '((LOWER(p.status) is null and (DATEDIFF(NOW(),l.created_at) < 15)))';
                $this->db->where($where);

            } else {
                if (strtolower($this->input->post('status') == 'payment link not triggered')) {
                    $this->db->where("LOWER(p.status)", "payment pending");
                    $this->db->where("sms_trigger_status", 0);

                } elseif (strtolower($this->input->post('status') == 'payment pending')) {
                    $this->db->where("LOWER(p.status)", "payment pending");
                    $this->db->where("sms_trigger_status", 1);

                }
                elseif (strtolower($this->input->post('status') == 'rejected')) {
                    $where = '((LOWER(p.status) = "rejected" or (LOWER(p.status) is null and DATEDIFF(NOW(),l.created_at) > 15)))';
                    $this->db->where($where);
                }


                else {
                    $this->db->where("LOWER(p.status)", strtolower($this->input->post('status')));
                    // $where = '((LOWER(p.status) = '.strtolower($this->input->post('status')).' or (LOWER(p.status) is null and DATEDIFF(NOW(),l.created_at) > 15)))';
                    //$this->db->where($where);
                }

            }
        }

        if (!empty($this->input->post('moddate'))) {
            $dates = explode('-', $this->input->post('moddate'));
            $start_date = trim(str_replace('/', '-', $dates[0])).'0:0:0';
            $end_date = trim(str_replace('/', '-', $dates[1])).'23:59:59';
            $this->db->where("l.created_at >= ", date('Y-m-d H:i:s', strtotime($start_date)));
            $this->db->where("l.created_at <= ", date('Y-m-d H:i:s', strtotime($end_date)));
        }

        if (!empty($this->input->post('issuancedate'))) {
            $dates = explode('-', $this->input->post('issuancedate'));
            $start_date = trim(str_replace('/', '-', $dates[0])).'0:0:0';
            $end_date = trim(str_replace('/', '-', $dates[1])).'23:59:59';
            $this->db->where("apr.created_date >= ", date('Y-m-d H:i:s', strtotime($start_date)));
            $this->db->where("apr.created_date <= ", date('Y-m-d H:i:s', strtotime($end_date)));
        }


        if (!empty($this->input->post('last_modified'))) {
            $dates = explode('-', $this->input->post('last_modified'));
            $start_date = trim(str_replace('/', '-', $dates[0])).'0:0:0';
            $end_date = trim(str_replace('/', '-', $dates[1])).'23:59:59';
            $this->db->where("edisposition.date >= ", date('Y-m-d H:i:s', strtotime($start_date)));
            $this->db->where("edisposition.date <= ", date('Y-m-d H:i:s', strtotime($end_date)));
        }



        if (!empty($this->input->post('location'))) {
            $this->db->where("LOWER(l.axis_location)", strtolower($this->input->post('location')));
        }


        // if ($this->admin == '0') {
        //     $this->db->where("l.assigned_to", $this->agent_id);
        // }
        // if ($this->admin == '1') {
        // $this->db->where("fr.family_id", 0);
        // }

        $this->db->group_by('l.lead_id');
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('emp_id', 'DESC');
        }


    }
    public function get_all_maker_data()
    {
        $this->maker_query();
        $query = $this->db->get();
        $query->num_rows();

        return $this->db->count_all_results();

    }
    public function get_filtered_maker_data()
    {
        $this->maker_query();
        $query = $this->db->get();
        $query->num_rows();
        return $query->num_rows();
    }
    public function insert_av($data,$edit)
    {
        if($edit == 0 || $edit == '')
        {
            $agent_code = $data['agent_id'];
            $agent_code_check=$this->db->query("SELECT * from tls_agent_mst where `agent_id`= '$agent_code' ")->row_array();
            if($agent_code_check){
                $validation_err= ["status" => false, "message" => "AV ID aleready exists"];
                print_r(json_encode($validation_err));
                exit;
            }
            $this->db->insert('tls_agent_mst',$data);
            $success_data = ["status" => true, "message" => "Sucessfully Created"];
        }
        else
        {
            $this->db->where('id',$edit);
            $this->db->update('tls_agent_mst',$data);
            $success_data = ["status" => true, "message" => "Sucessfully Updated"];
        }
        return $success_data;
    }
}
?>