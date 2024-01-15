<?PHP
class Teleproposal_m extends CI_Model
{
    function __construct()
    {//echo 123;die;
        parent::__construct();
        //checklogin();
        $this->db = $this->load->database('telesales_fyntune',true);
        $telesession = $this->session->userdata('telesales_session');
       $this->agent_id = encrypt_decrypt_password($telesession['agent_id'], 'D');
        $this->admin = $telesession['is_admin'];
       $this->emp_id = $telesession['emp_id'];
        $this->is_region_admin = $telesession['is_region_admin'];
        $this->load->model('telelogs/Logs_m');


    }
    function get_adult_child_limit($policy_no)
    {
        $polic_id = explode(",",$policy_no);
        $arr_data = [];
        foreach($polic_id as $key=>$policy_ids){
//print_r($policy_ids);
            $response = $this->db->select('*')
                ->from('master_broker_ic_relationship as `mbir')
                ->where('mbir`.`policy_id`', $policy_ids)
                ->get()->result_array();

            $arr_data = array_merge($arr_data,$response);
        }
       // print_r($arr_data);
        $arr_check = [];
        $data = [];
        foreach($arr_data as $key => $res)
        {

            if(!in_array($res['fr_id'],$arr_check))
            {

                $data[] = $res;

            }
            $arr_check[] = $res['fr_id'];
        }
        return $data;


    }
    function policy_creation_call($CRM_Lead_Id,$cron_policy_check = '')
    {
        //echo 123;die;
        //print_r($aTLSession);die;
        $update_data = $this
            ->db
            ->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,e.product_code,e.HB_policy_type
				FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
				employee_details AS ed
				where epd.product_name = e.id
				AND p.emp_id = ed.emp_id
				AND ed.lead_id = "' .$CRM_Lead_Id. '"
				AND epd.policy_detail_id = p.policy_detail_id');
        $update_data = $update_data->result_array();
      //  print_r($update_data);die;
        //update payment confirmation hit count
        $this->db->query("UPDATE proposal SET count = count + 1 WHERE emp_id ='".$update_data[0]['emp_id']."'");

        foreach ($update_data as $update_payment)
        {

            if($update_payment['status']!='Success'){

                // check first hit or not

                // update proposal status - Payment Received
                $arr_new = ["status" => "Payment Received","modified_date" => date('Y-m-d H:i:s')];
                $this->db->where('id', $update_payment['id']);
                $this->db->update("proposal", $arr_new);

                //employee disposition status add
                $aTLSession = $this->session->userdata('telesales_session');
                $agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');
                $agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];

                $this->db->insert("employee_disposition",["emp_id" => $update_payment['emp_id'],"disposition_id" => 46,"agent_name" => "","date" => date('Y-m-d H:i:s')]);

                // For GHI,GPA,GCI policy check
                $query = $this->db->query("select policy_detail_id from employee_policy_detail where policy_detail_id = '" . $update_payment['policy_detail_id'] . "' and policy_sub_type_id in(1,2,3)")->row_array();
                if($query)
                {

                    if($update_payment['HB_policy_type'] == 'ProposalWise')
                    {
                        $api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'],'',$cron_policy_check);
                    }else{

                        $api_response_tbl['status']='error';

                        $query2 = $this->db->query("SELECT p.status FROM product_master_with_subtype AS e,employee_details AS ed,proposal AS p,employee_policy_detail AS epd where ed.emp_id=p.emp_id AND epd.product_name = e.id AND epd.policy_detail_id = p.policy_detail_id AND ed.lead_id = '".$CRM_Lead_Id."' AND e.policy_subtype_id = 1")->row_array();

                        if ($query2['status']=='Success' || $update_payment['product_code'] == 'R10')
                        {

                            $api_response_tbl = $this->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id'],$cron_policy_check);

                        }

                    }

                    if($api_response_tbl['status']=='error'){

                        $return_data['check'][] = 'error';
                        $return_data['code'] = '0';
                        $return_data['msg'] = $api_response_tbl['msg'];

                    }else{
                        // update proposal status - Success
                        $arr = ["status" => "Success","modified_date" => date('Y-m-d H:i:s')];
                        $this->db->where('id', $update_payment['id']);
                        $this->db->update("proposal", $arr);

                        //employee disposition status add
                        //$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$update_payment['emp_id']."'")->row_array();
                        $aTLSession = $this->session->userdata('telesales_session');

                        $agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');
                        $agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];

                        //$this->db->insert("employee_disposition",["emp_id" => $update_payment['emp_id'],"disposition_id" => 48,"agent_name" => $agent_name,"date" => date('Y-m-d H:i:s')]);

                        //HB emandate call
                        //$this->external_obj_api->emandate_HB_call($update_payment['emp_id']);

                        $return_data['check'][] = 'Success';
                        $return_data['code'] = '1';
                        $return_data['msg'] = $api_response_tbl['msg'];
                    }

                }



            }else{
                $return_data['check'][] = 'Success';
                $return_data['code'] = '2';
                $return_data['msg'] = 'Already genarate';
            }
        }

        if (in_array("error", $return_data['check']))
        {
            $data = array(
                'Status' => 'error',
                'ErrorCode' => '0',
                'ErrorDescription' => $return_data['msg'],
            );
        }
        else
        {
            $data = array(
                'Status' => 'Success',
                'ErrorCode' => $return_data['code'],
                'ErrorDescription' => $return_data['msg'],
            );
        }

        return $data;

    }

    function get_combo_rel()
    {
        extract($this->input->post());
        $polic_id = json_decode($policy_no);
        $arr_data = [];
        foreach($polic_id as $key=>$policy_ids){
$policy_no = $policy_ids->policy_no;
            $response = $this->db->select('*')
                ->from('master_broker_ic_relationship as `mbir, master_family_relation as mfr`')
                ->where('find_in_set(mfr.fr_id, mbir.relationship_id)')
                ->where('mbir`.`policy_id`', $policy_no)
                ->get()->result_array();

            $arr_data = array_merge($arr_data,$response);
        }
//print_r($arr_data);die;

        $arr_check = [];
        $data = [];
        foreach($arr_data as $key => $res)
        {
            //print_r();

            if(!in_array($res['fr_id'],$arr_check))
            {

                $data[] = $res;

            }
            $arr_check[] = $res['fr_id'];
        }
//print_r($data);die;
        return $data;


    }
    function get_all_data_sumPremium($hiddenpolicyarr)
    {
        $sum_ins = json_decode($hiddenpolicyarr);
$policy_ids = [];
foreach($sum_ins as $value){
   // print_r($value->policy_no);
    array_push($policy_ids,$value->policy_no);
}
$policy_id = implode(',',$policy_ids);
return $policy_id;

 // $get_premium =     $this->get_premium_from_policy($policy_detail_id,$sum_insure,$family_construct,$age);
    }

    public function get_premium_from_policy($policy_detail_id,$sum_insure,$family_construct,$age,$deductable)
    {
        //print_r($policy_detail_id);
//echo 123;
        if($deductable!='')
        {
            $ded = "deductable = '$deductable'";
        }else{
            $ded = "deductable = '0' OR deductable = ''";
        }
        $ew_status  = 0;//$this->EW_status($emp_id);
        $premium_value = '';

        $premium1 = $this->db
            ->select('*')
            ->from('master_broker_ic_relationship as fc')
            ->where('fc.policy_id', $policy_detail_id)
            ->get()
            ->row_array();
      //  print_r($premium1);
        $family_construct1 = explode("+", $family_construct);

        if ($premium1['max_adult'] != 0 && 	$premium1['max_child'] == 0 && $family_construct1[1] != '')
        {
            $member_id = $family_construct1[0];
        }
        else
        {
            $member_id = $family_construct;
        }
       // print_r($member_id);
        $check_gmc = $this->db
            ->select('*')
            ->from('employee_policy_detail as epd')
            ->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
            ->where('epd.policy_detail_id', $policy_detail_id)
            ->get()
            ->row_array();
       // print_r($check_gmc);
        if($check_gmc['suminsured_type'] == 'family_construct')
        {

            $checks = $this->db->select("PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
                ->from("family_construct_wise_si")
                ->where("sum_insured", $sum_insure)
                ->where("family_type", $member_id)
                ->where("policy_detail_id", $policy_detail_id)
                ->where($ded,Null,false)

                ->get()
                ->row_array();
           // echo $this->db->last_query().'===='.$ew_status.'===';

            if($ew_status == 1)
            {
                $premium_value  = $checks['EW_PremiumServiceTax'];
            }
            else
            {
                $premium_value  = $checks['PremiumServiceTax'];
            }
            //echo $premium_value;exit;
            return $premium_value;


        }

        if($check_gmc['suminsured_type'] == 'family_construct_age'){
            $check = $this->db->select("age_group,PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
                ->from("family_construct_age_wise_si")
                ->where("sum_insured",$sum_insure)
                ->where("family_type", $member_id)
                ->where("policy_detail_id", $policy_detail_id)
                ->where($ded,Null,false)

                ->get()
                ->result_array();
//print_r($this->db->last_query());die;
            foreach($check as $values){
                $min_max_age = explode("-",$values['age_group']);


                if($age >= $min_max_age[0] && $age <= $min_max_age[1]){

                    if($EW_status == 1)
                    {


                        $premium_value  = $values['EW_PremiumServiceTax'];
                    }
                    else
                    {


                        $premium_value  = $values['PremiumServiceTax'];
                    }
                    return $premium_value;

                }
            }
        }

        if($check_gmc['suminsured_type'] == 'memberAge'){
            $check_age = $this->db->select("policy_age,premium_with_tax,sum_insured,EW_premium_with_tax")
                ->from("policy_creation_age")
                ->where("sum_insured",$sum_insure)
                ->where("policy_id", $policy_detail_id)
                ->where($ded,Null,false)


                ->get()
                ->result_array();

            foreach($check_age as $values_age){

                $min_max_age = explode("-",$values_age['policy_age']);


                if($age >= $min_max_age[0] && $age <= $min_max_age[1]){

                    if($EW_status == 1)
                    {


                        $premium_value  = $values_age['EW_premium_with_tax'];
                    }
                    else
                    {


                        $premium_value  = $values_age['premium_with_tax'];
                    }
                    //echo $premium_value;exit;
                    return $premium_value;

                }
            }
        }
        if($check_gmc['suminsured_type'] == 'permilerate')
        {
            $sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $age . " AND max_age>=" . $age . "  and master_policy_id=" . $policy_detail_id . " and isactive=1";

            $query = $this->db->query($sql);
            $rate = $query->result();
            $amount = ($sum_insure / 1000) * $rate[0]->policy_rate;

            $final_rate = $amount  *  (100 + 18) / 100;



                $premium_value  = number_format(ceil($final_rate), 2, '.', '');;

return $premium_value;
        }
        if($check_gmc['suminsured_type'] == 'flate') {
            $check_flate = $this->db->select("premium")
                ->from("policy_creation_flate")
                ->where("sum_insured", $sum_insure)
                ->where("policy_id", $policy_detail_id)
               // ->where($ded, Null, false)
                ->get()
            //print_R($this->db->last_query());
                ->row_array();
            $premium_value = $check_flate['premium'];
            return $premium_value;

        }


        }

    function getProposalData($emp_id,$policy_no)
    {
        $memberwise_check = $this
            ->db
            ->query('SELECT e.HB_policy_type from product_master_with_subtype AS e,employee_policy_detail epd where e.id = epd.product_name and e.policy_subtype_id = epd.policy_sub_type_id and  epd.policy_detail_id = "' . $policy_no . '"')->row_array() ['HB_policy_type'];

        if ($memberwise_check == 'ProposalWise')
        {
            $extra_condition = 'AND pm.familyConstruct = mgc.family_construct';
        }
        else
        {
            $extra_condition = 'AND mgc.family_construct = "1A"';
        }

        $query = $this
            ->db
            ->query('SELECT p.created_date,p.id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,epd.policy_no,mgc.group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.plan_code,e.api_url,e.product_code,pd.TxRefNo,pd.ifscCode,pd.branch,pd.bank_name,epd.start_date,e.EW_master_policy_no,p.branch_code,mgc.EW_group_code,pm.familyConstruct,mgc.spouse_group_code,epd.policy_sub_type_id,pd.payment_status,pd.transaction_no,e.SourceSystemName_api,e.imd_refer_product_code,e.HB_source_code,e.HB_policy_type,e.HB_custid_concat_string FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,master_group_code AS mgc,payment_details as pd where epd.product_name = e.id AND p.emp_id = "' . $emp_id . '" AND p.policy_detail_id = "' . $policy_no . '" AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id '.$extra_condition.' AND p.sum_insured = mgc.si_group AND e.product_code = mgc.product_code AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id');


        if ($query)
        {
            $query = $query->row_array();
        }
        else
        {
            $query = [];
        }
        //echo $this->db->last_query();exit;
        return $query;
    }

    function get_all_nominee($emp_id)
    {
        $response = $this->db->select('*,mfr.relation_code,mfr.fr_name')
            ->from('member_policy_nominee AS mpn,master_family_relation as mfr')
            ->where('mpn.emp_id', $emp_id)
            ->where('mpn.fr_id = mfr.fr_id')
            ->get()->row_array();
        if ($response) {
            return $response;
        }
    }

    function get_profile($emp_id)
    {
        return $this->db->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();
    }
    function get_all_member_data($emp_id, $policy_detail_id)
    {
        $response = $this->db
            ->query('SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"Self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_details AS ed WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = 0 AND fr.emp_id = ed.emp_id AND ed.emp_id = ' . $emp_id . '
	AND `epd`.`policy_detail_id` = '.$policy_detail_id.' UNION all SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
	epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,e.plan_code
	FROM 
	product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_family_details AS efd,
	master_family_relation AS mfr
	WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id
	AND epm.family_relation_id = fr.family_relation_id
	AND fr.family_id = efd.family_id 
	AND efd.fr_id = mfr.fr_id AND `epd`.`policy_detail_id` = '.$policy_detail_id.'
	AND fr.emp_id = ' . $emp_id)->result_array();


        return $response;
    }

    public function GHI_GCI_api_call($emp_id, $policy_no,$mem_data = '',$cron_policy_check = '')
    {//echo 23;die;

        $err = 1;
        /*check for payment done or not and prevent multiple policy create at same time*/
        $extra_check_data = $this
            ->db
            ->query("select pd.payment_status,pd.TxRefNo,pd.TxStatus,ed.is_policy_issue_initiated from proposal as p,employee_details as ed,payment_details as pd  where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.emp_id ='" . $emp_id . "'")->row_array();
        /* ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success')
        {*/


     //   print_r($extra_check_data);die;
        if (($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success') && $extra_check_data['is_policy_issue_initiated'] == 0)
        {
//echo 56;die;
            $extra_arr_update = ["is_policy_issue_initiated" => 1];
            $this
                ->db
                ->where("emp_id", $emp_id);
            $this
                ->db
                ->update("employee_details", $extra_arr_update);

            $data['customer_data'] = (array)$this->get_profile($emp_id);
            $data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_no);
            $data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
            $data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_no);
            //print_pre($data);die;
            $policy_sub_type_id =  $data['proposal_data']['policy_sub_type_id'];
            $sub_type_name = $this->db->query("select short_code from master_policy_sub_type where policy_sub_type_id = '$policy_sub_type_id'")->row_array();

            if($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
            {

                $collection_amt['pay_amt'] = $data['proposal_data']['premium'];


            }else{



                $collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];


            }

            $transaction_date = explode(" ",$data['proposal_data']['txndate']);
            $trans_date = date("Y-m-d", strtotime($transaction_date[0]));
            if(!empty($data)){
              //  echo 1;die;
                $err = 0;
                $CertificateNumber = $this->generate_coi($sub_type_name['short_code']);
                // print_R($CertificateNumber);die;
            }



            if ($err == 1)
            {
echo 56;die;
                return array(
                    "status" => "error",
                    "msg" => $err
                );
            }
            else
            {
                $new = simplexml_load_string($response);
                $con = json_encode($new);
                $newArr = json_decode($con, true);

                $errorObj = $newArr['errorObj'];
                $return_data = [];


                $return_data['status'] = 'Success';
                $return_data['msg'] = $errorObj['ErrorMessage'];

                $create_policy_type = 0;
                if($cron_policy_check){
                    $create_policy_type = 1;
                }
                $startDate = date('Y-m-d');
                $EndDate = date('Y-m-d', strtotime($startDate . ' + 364 days'));
                $api_insert = array(
                    "emp_id" => $emp_id,
                    "proposal_id" => $data['proposal_data']['id'],
                    "member_fr_id" => $mem_fr_id,
                    "certificate_number" => $CertificateNumber,
                    "gross_premium" => $collection_amt['pay_amt'],
                    "status" => "Success",
                    //"status" => $errorObj['ErrorMessage'],
                    "start_date" => $startDate,
                    "end_date" => $EndDate,
                    "created_date" => date('Y-m-d H:i:s'),
                    "proposal_no_lead"=>$data['proposal_data']['proposal_no'],


                    //"COI_url" => $newArr['policyDtls']['COIUrl'],
                );
//print_r($api_insert);die;
                $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) ,"product_id"=> $data['proposal_data']['product_code'], "type"=>"api_insert"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

                $this->db->insert('api_proposal_response', $api_insert);



                return $return_data;

            }

        }else{
//echo 567;die;
            $extra_arr_update = ["is_policy_issue_initiated" => 0];
            $this
                ->db
                ->where("emp_id", $emp_id);
            $this
                ->db
                ->update("employee_details", $extra_arr_update);

            return $return_data = array(
                'status'=>'error',
                "msg" => "Quote error"
            );
        }
    }
    public function generate_coi($policy_subtype_name)
    {
        $coi_no = $policy_subtype_name . '-XL-AS-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        $proposal_id = $this->db->select('*')
            ->from('api_proposal_response')
            ->where('certificate_number', $coi_no)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {

            $this->generate_coi();
        }
        return $coi_no;
    }

    public function policy_detail_not_combo($product_name)
    {
        $data = $this->db
            ->select('*')
            ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
            ->where('pms.id = epd.product_name')
            ->where('pms.id', $product_name)
            ->where('pms.combo_flag!=', 'Y')
            ->get()
            ->result_array();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['suminsured_type'] == "family_construct") {
                $policy_ids[] = $data[$i]['policy_detail_id'];

            }
            if ($data[$i]['suminsured_type'] == "family_construct_age") {
                $policy_ids[] = $data[$i]['policy_detail_id'];
            }
            if ($data[$i]['suminsured_type'] == "memberAge") {
                $policy_ids[] = $data[$i]['policy_detail_id'];
            }

        }
        $y1 = implode(",", $policy_ids);
        return $y1;
    }
    public function get_policy_name($policy_detail_id)
    {
        $query = $this->db->query("select * from employee_policy_detail as epd Join master_policy_sub_type as mpst ON epd.policy_sub_type_id = mpst.policy_sub_type_id where epd.policy_detail_id = '".$policy_detail_id."'");

        $result = $query->row_array();

        $new_arrays = $result['policy_sub_type_name'];

        return $new_arrays;
    }
    public function get_data($table,$col1,$result)
    {
        $data = $this->db->select("*")
            ->from($table)
            ->where($col1, $result)
            ->get()->row_array();
        return $data;
    }

    public function get_policy_data_emp($parent_id)
    {


        $arr_new = array();
        $emp_id = $this->emp_id;
        //$parent_id = $parent_id;
        //$parent_id = $this->parent_id;
        //condition for t03
        if($parent_id == 'NvpnoiwGGDQPVA23w'){
            $data = $this->db->query("SELECT pid,deductable from employee_details where emp_id = '".$emp_id."'")->row_array();
            //print_pre($data);exit;
            $query = $this->db->query("SELECT *
										FROM employee_policy_detail as epd
										JOIN product_master_with_subtype AS psw ON epd.product_name= psw.id
										JOIN master_policy_sub_type AS mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id
										WHERE epd.policy_detail_id IN (".$data['pid'].")")->result_array();
            // echo $this->db->last_query();print_r($query);exit;
            foreach ($query as $key => $value) {
                $arr_new['comboData']['plan_name'] = $value['product_name'];
                //echo $combo_data['product_name'];
                $arr_new['comboData']['product_name'] = $this
                    ->obj_home
                    ->get_sub_name($value['policy_parent_id']);

                // $arr_new['comboData']['combo_flag'] = "Y";
                $arr_new['comboData']['combo_flag'] = ($data['pid'] == TELE_HEALTHPROINFINITY_GHI_GPA) ? "Y" : "N";
                $arr_new['comboData']['policy_detail_id'] = $data['pid'];
                $arr_new['comboData']['deductable'] = $data['deductable'];
            }

        }else{
            $combo_product = $this->db->query("select * from product_master_with_subtype where policy_parent_id = '$parent_id' ")->result_array();
            //echo $this->db->last_query();exit;
            foreach ($combo_product as $combo_data)
            {

                //print_pre($combo_data);exit;
                if ($combo_data['combo_flag'] == 'Y')
                {
                    $query = $this->db->query("select * from product_master_with_subtype as psw Join master_policy_sub_type as mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id where psw.product_name = '" . $combo_data['product_name'] . "'AND psw.combo_flag ='" . Y . "'")->row_array();
                    $arr_new['comboData']['plan_name'] = $query['product_name'];
                    //echo $combo_data['product_name'];
                    $arr_new['comboData']['product_name'] = $this
                        ->obj_home
                        ->get_sub_name($combo_data['policy_parent_id']);

                    $arr_new['comboData']['combo_flag'] = $combo_data['combo_flag'];
                    $arr_new['comboData']['policy_detail_id'] = $this
                        ->obj_home
                        ->policy_detail_combo_flag($combo_data['product_name']);
                }
                else
                {
                    $query = $this
                        ->db
                        ->query("select * from product_master_with_subtype as psw Join master_policy_sub_type as mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id where psw.product_name = '" . $combo_data['product_name'] . "'AND psw.combo_flag !='" . Y . "'")->row_array();
                    $arr_new['indivisual']['product_name'] = $query['product_name'];
                    $arr_new['indivisual']['plan_name'] = $query['product_name'];
                    $arr_new['indivisual']['combo_flag'] = $combo_data['combo_flag'];
                    $arr_new['indivisual']['policy_detail_id'] = $this
                        ->obj_home
                        ->policy_detail_not_combo($combo_data['id']);
                }

            }
        }

        // print_pre($arr_new);exit;
        foreach($arr_new as $get_policy_data)
        {
            // echo $get_policy_data['combo_flag'];exit;
            $data_explode = explode(',', $get_policy_data['policy_detail_id']);
            if ($get_policy_data['combo_flag'] == 'Y')
            {
                foreach($data_explode as $get_member)
                {

                    $policy_sub_type_name = $this->get_policy_name($get_member);
                    $sub_type_id = $this->get_data('employee_policy_detail', 'policy_detail_id', $get_member);
                    $policy_sub_type_id = $sub_type_id['policy_sub_type_id'];
                    $master_policy = $this->get_data_multiple('product_master_with_subtype', 'policy_parent_id', $parent_id, 'policy_subtype_id', $policy_sub_type_id);
                    $policy_sub_type_name = $this->get_policy_name($get_member);
                    $arr_new['comboData']['policy_sub_type_name'] = $policy_sub_type_name;

                    if($get_member == TELE_HEALTHPROINFINITY_GHI_ST){
                        $member_data = $this->get_all_member_data_healthproxl($emp_id, $get_member,$get_policy_data['deductable']);
                    }else{
                        $member_data = $this
                            ->obj_home
                            ->get_all_member_data_new($emp_id, $get_member);
                    }


                    $arr_new['comboData']['customer_detail'][]= $this->get_insured_summary($member_data, $policy_sub_type_name, $parent_id, $emp_id,$get_policy_data['plan_name'],$master_policy);
                    // print_pre($policy_sub_type_name);
                    // print_pre($policy_sub_type_id);
                    // print_pre($master_policy);
                    // print_pre($policy_sub_type_name);
                    //print_pre($member_data);exit;


                }
            }
            else
            {
                foreach($data_explode as $get_member)
                {
                    $policy_sub_type_name = $this->get_policy_name($get_member);
                    $sub_type_id = $this->get_data('employee_policy_detail', 'policy_detail_id', $get_member);
                    $policy_sub_type_id = $sub_type_id['policy_sub_type_id'];

                    $master_policy = $this->get_data_multiple('product_master_with_subtype', 'policy_parent_id', $parent_id, 'policy_subtype_id', $policy_sub_type_id);



                    $policy_sub_type_name = $this->get_policy_name($get_member);
                    $arr_new['indivisual']['policy_sub_type_name'] = $policy_sub_type_name;
                    if($get_member == TELE_HEALTHPROINFINITY_GHI_ST){
                        $member_data = $this->get_all_member_data_healthproxl($emp_id, $get_member,$get_policy_data['deductable']);

                        $master_policy = $this
                            ->db
                            ->query("SELECT pmws.master_policy_no FROM employee_policy_detail AS epd
							INNER JOIN product_master_with_subtype AS pmws
							ON epd.product_name = pmws.id
							WHERE epd.policy_detail_id = ".$get_member."
							LIMIT 1")->row_array();


                    }else{
                        $member_data = $this
                            ->obj_home
                            ->get_all_member_data_new($emp_id, $get_member);
                    }
                    $arr_new['indivisual']['customer_detail'] = $this->get_insured_summary($member_data, $policy_sub_type_name, $parent_id, $emp_id,$get_policy_data['plan_name'],$master_policy);

                }
            }

        }

        // print_pre($arr_new);exit;

        return $arr_new;
    }

    public function health_declaration_emp_data($parent_id)
    {
        $emp_id = $this->emp_id;
        $data = $this->db
            ->select('employee_declare_data.format,employee_declare_data.remark,policy_declaration.policy_detail_id,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
            ->from('policy_declaration')
            ->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
            ->join('employee_declare_data ', 'employee_declare_data.p_declare_id = policy_declaration.p_declare_id', 'left')
            ->where('policy_declaration.parent_policy_id', $parent_id)
            ->where('employee_declare_data.emp_id', $emp_id)
            ->get()
            ->result_array();
        // echo $this->db->last_query();exit;
        return $data;
    }

    public function check_validations_adult_count($family_construct,$emp_id,$policy_detail_id)
    {
        if(!$family_construct){
            return false;
        }
        $data = explode("+",$family_construct);

        if($data[0])
        {

            preg_match_all('!\d+!', $data[0], $matches);

            //get total adult count

            $count = $this->get_adult_count($emp_id,$policy_detail_id);
            if($count['count'] != $matches[0][0])
            {

                return false;
            }
        }
        if($data[1])
        {
            if(empty($data[1])){
                $matches[0][0] = 0;
            }

            else{
                preg_match_all('!\d+!', $data[1], $matches);
            }

            //get total adult count
            $count = $this->get_child_count($emp_id,$policy_detail_id);
            // print_Pre($count);exit;
            if($count['count'] != $matches[0][0]){
                return false;
            }

        }


        return true;

    }
    public function get_data_multiple($table,$col1,$result,$col2,$result2)
    {
        $data = $this->db->select("*")
            ->from($table)
            ->where($col1, $result)
            ->where($col2, $result2)
            ->get()->row_array();
        return $data;
    }
    public function get_insured_summary($member_datas, $policy_sub_type_name, $parent_id, $emp_id,$plan_name,$master_policy)
    {
        //print_pre($member_datas);continue;
        $z = 1;
        foreach ($member_datas as $member_data)
        {

            $i = 1;
            $chronnic_disease = $this
                ->db
                ->query("select * from policy_declaration_member where parent_policy_id ='$parent_id'")->row_array();

            $policy_member_id = $member_data['policy_member_id'];
            $sub_type_chronic = $this
                ->db
                ->query("select group_concat(pds.sub_type_name) as chronic_subtype from employee_declare_member_sub_type as edms,policy_declaration_subtype as pds where edms.declare_sub_type_id = pds.declare_subtype_id AND edms.policy_member_id = '$policy_member_id' group by edms.policy_member_id")->row_array();
            if (!empty($sub_type_chronic))
            {
                $cronic_disease = $sub_type_chronic['chronic_subtype'];
            }
            else
            {
                $cronic_disease = 'No';
            }
            $st_text = '';
            $sum_insure = $member_data['policy_mem_sum_insured'];
            if($parent_id == 'NvpnoiwGGDQPVA23w'){
                $data = $this->db->query("SELECT pid,deductable from employee_details where emp_id = '".$emp_id."'")->row_array();
                if($data['pid'] == TELE_HEALTHPROINFINITY_GHI_ST){
                    $sum_insure = $member_data['policy_mem_sum_insured'] - $data['deductable'];
                    $st_text = ' - Super Topup';
                }
            }
            //$sub_type_chronic_ans = $this->db->query("select pds.sub_type_name,edmd.format,pdm.content from employee_declare_member_sub_type as edms,policy_declaration_subtype as pds,employee_declare_member_data as edmd,policy_declaration_member as pdm where edms.declare_sub_type_id = pds.declare_subtype_id AND edms.policy_member_id = edmd.policy_member_id AND edmd.p_member_id = pdm.p_member_id AND edms.policy_member_id = '$policy_member_id' ")->result_array();
            $subtype_id = $this->get_sub_type_data($emp_id, $policy_member_id);

            if($z == 1){
                $customer_detail .= '<div class="col-md-12"> <p class="mt-2 mb-2 text-center" style="color: #da8089;font-size: 17px;">'.$policy_sub_type_name .''.$st_text.'</p><table class="table table-bordered text-center"><thead class="text-uppercase col-da80">';
            }else{
                $customer_detail .= '<div class="col-md-12"><table class="table table-bordered text-center"><thead class="text-uppercase col-da80">';
            }

            if($z == 1){

                //maker-checker update - 30/07/2021
                $this->db->select('is_makerchecker_journey');
                $this->db->from('employee_details');
                $this->db->where('emp_id',$emp_id);
                $is_maker_checker = $this->db->get()->row_array();

                $is_maker_checker = $is_maker_checker['is_makerchecker_journey'];


                // 03-02-2022 - SVK005 - remove if
                if($plan_name == 'Group Activ Health-Tele'){
                    $plan_name = 'Group Activ Health';
                }
                if($plan_name == 'Tele - Health Pro Infinity'){
                    $plan_name = 'Health Pro Infinity';
                }


                $customer_detail .= '<tr><th scope="col" style="width:25%;">Plan Name</th>
	<th scope="col" style="font-weight:600 !important; width:25%;">' .$plan_name.'</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Master Policy No.</th>
	<th scope="col" style="font-weight:600 !important;width:25%">' . $master_policy['master_policy_no'] . '</th>
	
	</tr>
	<tr>
	<th scope="col" style="width:25%;">Sum Insured</th>
	<th scope="col" style="font-weight:600 !important;width:25%;">' . $sum_insure . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Premium </th>
	<th scope="col" style="font-weight:600 !important;width:25%;">Rs ' . $this->get_total_premium($member_datas,$parent_id) . '</th>
	</tr>
	<tr>
	<th scope="col" style="width:25%;">Family Construct </th><th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['familyConstruct'] . '</th>';
                if($parent_id == 'NvpnoiwGGDQPVA23w'){

                    if($data['pid'] == TELE_HEALTHPROINFINITY_GHI_ST){
                        $customer_detail .= '<th style="width: 1px;border: none !important;border-color: #fff;"></th><th scope="col" style="width:25%;">Deductible </th><th scope="col" style="font-weight:600 !important;width:25%;">' . $data['deductable'] . '</th>';
                    }

                }
                $customer_detail .= '</tr>';


            }

            $customer_detail .= '<tr>
	<th scope="col" colspan="5" style="background: #da8089;color: #fff;">Member '.$z.' Details</th>
	</tr>';
            $customer_detail .= '
	<tr>			
	<th scope="col" style="width:25%;">First Name</th>
	<th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['firstname']) . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Relation</th>
	<th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['relationship'] . '</th>
	</tr>
	<tr>
	<th scope="col" style="width:25%;">Last Name </th>
	<th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['lastname']) . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">DOB(DD-MM-YYYY) </th>
	<th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['dob'] . '</th>
	</tr>';
            if($parent_id == 'NvpnoiwGGDQPVA23w'){
                if($member_data['fr_id'] == 0 || $member_data['fr_id'] == 1){
                    //if($data['pid'] != TELE_HEALTHPROINFINITY_GHI_ST && ($member_data['fr_id'] == 0 || $member_data['fr_id'] == 1)){
                    $customer_detail .= '<tr>
				<th scope="col" style="width:25%;">Email ID </th>
				<th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['policy_member_email_id']) . '</th>
				<th style="width: 1px;border: none !important;border-color: #fff;"></th>
				<th scope="col" style="width:25%;">Mobile Number </th>
				<th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['policy_member_mob_no'] . '</th>
				</tr>';
                }
            }
            // if($plan_name != 'Group Activ Health-Tele'){
            // $customer_detail .= '<tr>
            // <th scope="col" style="width:25%;">Member Premium </th>
            // <th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['policy_mem_sum_premium']) . '</th>

            // <tr>';
            // }

            if (!empty($chronnic_disease))
            {
                $customer_detail .= '<th scope="col" style="">Chronic Disease </th><th scope="col" style="font-weight:600 !important;">' . $cronic_disease . '</th>';
            }
            '<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Gender </th><th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['gender'] . '</th>
	</tr>
	<tr>';
            /*********************************************** Cronic Disease **************************************************************************/
            if (!empty($subtype_id))
            {
                foreach ($subtype_id as $subid)
                {

                    $subtype_name = $this->sub_type_name($subid['declare_sub_type_id']);
                    //print_pre($subtype_name);
                    $customer_detail .= '<div id="di' . $subid['declare_sub_type_id'] . '" class="col-md-12"><table class="table table-bordered text-center">
					<thead class="text-uppercase">
					<tr>
					<th scope="col" style="text-align: left; font-weight: 600;">' . $subtype_name['sub_type_name'] . '</th>
					<th scope="col" style="font-weight: 600;">Answer</th>
					</tr>
					</thead>

					<tbody id="mydatasmembers">';
                    $policy_member_declare = $this->db->query("select md.disease,z.content,e.policy_member_id from 
																employee_declare_member_sub_type e,master_disease md,policy_declaration_member z
																where e.declare_sub_type_id = md.id
																and z.declare_subtype_id = md.id
																and e.emp_id = '".$emp_id."'
																and z.parent_policy_id = '".$parent_id."'
																and md.sub_member_code = '".$subtype_name['sub_member_code']."'
																and z.declare_subtype_id = '".$subid['declare_sub_type_id']."'
																order by md.disease
																")->result_array();//$this->edit_member_declare_data($emp_id, $policy_member_id, $subid['declare_sub_type_id']);
                    //print_pre($policy_member_declare);exit;
                    foreach ($policy_member_declare as $key => $value)
                    {
                        $customer_detail .= '<tr>
											<td style="text-align:left;"><input type="hidden" class="mycontent" value="' . $value['p_member_id'] . '"/>' . $value['content'] . '</td>
											<td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  disabled name="' . $value['p_member_id'] . '" id="' . $value['p_member_id'] . '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_member_id'] . '"> Yes123 </label> </div>
											<div class="custom-control custom-radio" style="float:right;"> <input type="radio" disabled name="' . $value['p_member_id'] . '" class="custom-control-input radios_out " value="No" id="' . $value['p_member_id'] . '_1" checked="">  <label class="custom-control-label" for="' . $value['p_member_id'] . '_1" > No </label></div>
											</td>
											</tr>';
                    }
                    $customer_detail .= '</tbody></table></div>';
                }
            }
            /*if (!empty($subtype_id))
            {
                foreach ($subtype_id as $subid)
                {
                    $subtype_name = $this
                        ->obj_home
                        ->sub_type_name($subid['declare_sub_type_id']);
                    $customer_detail .= '<div id="di' . $subid['declare_sub_type_id'] . '" class="col-md-12"><table class="table table-bordered text-center">
					<thead class="text-uppercase">
					<tr>
					<th scope="col" style="text-align: left; font-weight: 600;">' . $subtype_name['sub_type_name'] . '</th>
					<th scope="col" style="font-weight: 600;">Answer</th>
					</tr>
					</thead>

					<tbody id="mydatasmembers">';
                    $policy_member_declare = $this
                        ->obj_home
                        ->edit_member_declare_data($emp_id, $policy_member_id, $subid['declare_sub_type_id']);

                    foreach ($policy_member_declare as $key => $value)
                    {
                        $customer_detail .= '<tr>
											<td style="text-align:left;"><input type="hidden" class="mycontent" value="' . $value['p_member_id'] . '"/>' . $value['content'] . '</td>
											<td style="width: 150px;">No</td>

											</tr>';
                    }
                    $customer_detail .= '</tbody></table>';
                }
            } */
            /*********************************************** Insured Member 2nd col **************************************************************************/
            $i++;

            $z++;

            $customer_detail .= '</thead></table></div>';
        }

        return $customer_detail;
    }

    public function get_sub_type_data($emp_id, $emp_policy_mem)
    {


        $data = $this->db
            ->select('*')
            ->from('employee_declare_member_sub_type')
            ->where('emp_id', $emp_id)
            ->where('policy_member_id', $emp_policy_mem)
            ->group_by('declare_sub_type_id')
            ->get();

        if($data->num_rows()>0)
        {

            $result= $data->result_array();

            return $result;
        }

    }
    public function get_total_premium($data,$id){
        $sum = 0;

        foreach($data as $value){
            //echo $value['policy_sub_type_id'].'--'.$value['policy_mem_sum_premium'];
            if($id == 'test123' && $value['policy_sub_type_id'] != 1){
                $sum+= $value['policy_mem_sum_premium'];
            }else if($id == 'NvpnoiwGGDQPVA23w' && $value['policy_sub_type_id'] != 1){

                $sum+= $value['policy_mem_sum_premium'];
            }else{
                return $value['policy_mem_sum_premium'];
            }
        }

        return $sum;
    }

    public function check_validations_adult_count_add_member($family_construct,$emp_id,$policy_detail_id,$relation_id,$edit)
    {
        $data = explode("+",$family_construct);
        if($data[0]){
            preg_match_all('!\d+!', $data[0], $matches);
            //get total adult count
            if($relation_id != '2' && $relation_id != '3'){
                $count = $this->get_adult_count($emp_id,$policy_detail_id);
                if($edit == 0){
                    if($count['count'] == $matches[0][0]){
                        return false;
                    }
                }
            }
        }
        if($data[1]){
            preg_match_all('!\d+!', $data[1], $matches);
            //get total adult count
            if($relation_id == '2' || $relation_id == '3'){
                $count = $this->get_child_count($emp_id,$policy_detail_id);
                //print_pre($count);exit;
                if($edit == 0){
                    if($count['count'] == $matches[0][0]){
                        return false;
                    }
                }
            }
        }
        else{
            if($relation_id == '2' || $relation_id == '3'){
                return false;
            }
        }
        return true;
    }
    public function add_nominee($nominee_data)
    {
        $this->db->insert('member_policy_nominee',$nominee_data);
        // echo $this->db->last_query();exit;
        return true;
    }
    public function family_details_insert_new()
    {
       // print_r($this->input->post());exit;

 // echo 123;
        $parent_id = $this->parent_id;
        $empId = $this->emp_id;
        $fam_post = $this->input->post(null,true);
        if($fam_post != null || !empty($fam_post))
            $policy_no = $this->input->post('policyNo',true);
        $declare = $this->input->post('declare',true);
        $sub_type_check = $this->input->post('sub_type_check',true);
        $family_members_id = $this->input->post('family_members_id',true);
        $sum_insure = $this->input->post('sum_insure',true);
        $premium = $this->input->post('premium',true);
        $businessType =$this->input->post('businessType',true);
        $family_date_birth = $this->input->post('family_date_birth',true);
        $age = $this->input->post('age',true);
        $age_type = $this->input->post('age_type',true);
        $first_name = $this->input->post('first_name',true);
        $middle_name = $this->input->post('middle_name',true);
        $last_name = $this->input->post('last_name',true);
        $familyConstruct = $this->input->post('familyConstruct',true);
        $tenure = $this->input->post('tenure',true);
        $edit = $this->input->post('edit',true);
        $family_gender = $this->input->post('family_gender',true);
        $subtype_text =$this->input->post('subtype_text',true);
        //$remark = $this->input->post('remark',true);
        $family_salutation = $this->input->post('family_salutation',true);
        $edit_member_id = $this->input->post('edit_member_id',true);
        $chronic = $this->input->post('chronic',true);
        $declare_member = json_decode($declare,true);
        $chronic = json_decode($chronic,true);
        $gci_options = $this->input->post('GCI_optional',true);

        $product_id = $this->input->post('plan_name',true);
        //$hidden_policy_id = $this->input->post('hidden_policy_id',true);
      //  $hidden_deductable = $this->input->post('hidden_deductable',true);
        $mem_email_id = $this->input->post('mem_email_id',true);
        $mem_mob_no = $this->input->post('mem_mob_no',true);
        $edit = '';

        $familyConstruct = str_replace(" ", "+", trim($familyConstruct));
        // echo "GCI => ".$gci_options;exit;
        if($gci_options == 'Yes'){
            $gci_option = true;
        }else{
            $gci_option = false;
        }
        // echo $product_id;exit;
        // print_R($_POST);
        // foreach ($family_members_id as $key => $val)
        // {
        // echo $first_name[$key];
        // echo $key;
        // }
        // exit;
        $get_lead_id = $this->db->query("select lead_id,product_id,kid2_rel,kid1_rel,kid2_dob,kid1_dob,spouse_dob from employee_details where emp_id = '$empId' ")->row_array();
        if($get_lead_id['lead_id'] != 0)
        {
            $lead_id = $get_lead_id['lead_id'];
            $product_id = $get_lead_id['product_id'];
        }
        // Update Except R06
        if($product_id!='R06'){
            $update_log=['product_id'=>$product_id];
            $this->db->where('lead_id',$lead_id);
            $this->db->update('logs_post_data',$update_log);
        }


        //Add Data into logs Table
        $logs_array = ["type" => "post_insured_member","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
        $this->db->insert("logs_post_data",$logs_array);


        if($premium == 'undefined' || $premium == '')
        {
            return ["status" => false, "message" => "Invalid Premium"];
        }

        $declare_member = json_decode($declare,true);
        if($product_id == 'T03'){
            $policy_no = $hidden_policy_id;
            $deductable = $hidden_deductable;
            if($hidden_policy_id == TELE_HEALTHPROINFINITY_GHI_GPA){
                $gpa_option = true;
            }else{
                $gpa_option = false;
            }
        }
        $policy_no = explode(",", $policy_no);
       // print_r($policy_no);exit;
        foreach ($policy_no as $policy_no)
        {

            $sum_insure = $this->input->post('sum_insure_'.$policy_no,true);
            // echo $policy_no.'----';
            //print_pre($sub_type_check);
            //continue;
            if(!empty($sub_type_check))
            {
                foreach ($sub_type_check as $key1 => $sub_type_checks)
                {
                    if($edit == 0)
                    {
                        $query=$this->db->query("select count(declare_sub_type_id) as total from employee_declare_member_sub_type where emp_id='$empId' AND product_id='$parent_id' AND policy_detail_id='$policy_no' ")->result_array();

                        if($query[0]['total']>0)
                        {
                            //return ["status" => false, "message" => "As Per The Product Only One Chronic Member Is Allowed In A Policy" ,"check" => "declaration"];
                        }
                    }
                    else
                    {
                        $query=$this->db->query("select count(declare_sub_type_id) as total from employee_declare_member_sub_type where emp_id='$empId' AND product_id='$parent_id' AND policy_detail_id='$policy_no' AND policy_member_id !='$edit'")->result_array();

                        if($query[0]['total']>0)
                        {
                            //return ["status" => false, "message" => "As Per The Product Only One Chronic Member Is Allowed In A Policy" ,"check" => "declaration"];
                        }
                    }
                }
            }



                $go = false;


            //check min max age
            $premium_logic =  $this->db->select("*")->from("policy_age_limit")->where("policy_detail_id", $policy_no)->get()->row_array();

            $premium_get = $this->db->select("suminsured_type")->from("employee_policy_detail")->where("policy_detail_id", $policy_no)->get()->row_array();

            $premium_type_tbl = $premium_get['suminsured_type'];

            if($premium_type_tbl == 'memberAge')
            {
                $get_cal = $this->db->query("select premium_with_tax from policy_creation_age where policy_id = '$policy_no' AND sum_insured = '$sum_insure'")->row_array();
            }
            elseif($premium_type_tbl == "family_construct")
            {
                $get_cal = $this->db->query("select PremiumServiceTax from family_construct_wise_si where policy_detail_id = '$policy_no' AND family_type = '$familyConstruct' AND sum_insured = '$sum_insure'")->row_array();
            }
            //echo $go . '-- <br>';
            $adult_count = [];
            $child_count = [];
            $sposue_check = [];
            $tempArr = [];
            foreach ($family_members_id as $key => $val)
            {

                $family_relation_count = $this->db->select("is_adult")->from("master_family_relation")->where("fr_id", $val)->get()->row_array();

                if ($family_relation_count['is_adult'] == 'Y'){
                    array_push($adult_count, $val);
                }else{
                    array_push($child_count, $val);
                }
                //CHECK VALIDATIONS
                $check = $this->check_validations($val,$policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);
                // print_pre($check);
                if($check["message"] != "true")
                {
                    return ["status" => false, "message" => $check["message"]];
                }

                if($edit != '' && $edit != 'undefined'){
                    $action = 'update';
                }else{
                    $action = 'insert';
                }

                if(empty($tempArr)){
                    $tempArr[$key] = array("fname" => $first_name[$key],
                        "lname" => $last_name[$key],
                        "dob" => $family_date_birth[$key],
                        "policy_no" => $policy_no);
                    //array_push($tempArr,$t);
                }else{
                    // print_pre($tempArr);exit;
                    //check if in current lead any member is repeating
                    foreach ($tempArr as $key1 => $value1) {

                        if($value1['policy_no'] == $policy_no && $value1['fname'] == $first_name[$key] && $value1['lname'] == $last_name[$key] && $value1['dob'] == $family_date_birth[$key]){
                            return ["status" => false, "message" => "member already exist with same name and dob !" ];
                        }else{
                            $tempArr[$key] = array("fname" => $first_name[$key],
                                "lname" => $last_name[$key],
                                "dob" => $family_date_birth[$key],
                                "policy_no" => $policy_no);
                        }
                    }
                    //print_pre($tempArr);exit;
                }


            }
            $familyConstructArr = explode('+', $familyConstruct);
            $familyConstruct_adult = str_replace('A', '', $familyConstructArr[0]);
            $familyConstruct_child = 0;
            if(isset($familyConstructArr[1])){
                $familyConstruct_child = str_replace('K', '', $familyConstructArr[1]);
            }

            if((int)$familyConstruct_adult != count($adult_count) && (int)$familyConstruct_child != count($child_count)){
                return ["status" => false, "message" => "Member not added as per policy"];
            }

            foreach ($family_members_id as $key => $val)
            {
              // print_r($policy_no);

                $get_adult_child = $this->get_adult_child_limit($policy_no);
                //disease code
                $relationship_ids = explode(',',$get_adult_child[0]['relationship_id']);
                // if(is_array($relationship_ids)){echo 1;die;}else{echo 2;}
                if(in_array($val,$relationship_ids)) {
                    $family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", 0)->get()->row_array();

                    $member_exist_check = $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();

                    if ($edit_member_id[$key]) {

                        //$edit = $member_exist_check['policy_member_id'];
                        $edit = $edit_member_id[$key];

                    }
                    //echo $edit;exit;
                    $premium_detail = $this->get_premium_from_policy($policy_no,$sum_insure,$familyConstruct,$age[$key],$deductable = 0);
                    if(empty($premium_detail))
                    {
                        $max_age = max($age);
                        $premium_detail =  $this->get_premium_from_policy($policy_no,$sum_insure,$familyConstruct,$max_age,$deductable = 0);
                        //  print_r($premium_detail);die;
                    }
                    $member_array = [
                        "policy_detail_id" => $policy_no,
                        "family_relation_id" => $family_relation_id['family_relation_id'],
                        "policy_mem_sum_insured" => $sum_insure,
                        "policy_mem_sum_premium" => $premium_detail,
                        "policy_mem_gender" => $family_gender[$key],
                        "policy_mem_salutation" => $family_salutation[$key],
                        "policy_mem_dob" => $family_date_birth[$key],
                        "age" => $age[$key],
                        "age_type" => $age_type[$key],
                        "member_status" => "pending",
                        "fr_id" => $val,
                        "policy_member_first_name" => $first_name[$key],
                        "policy_member_middle_name" => $middle_name,
                        "policy_member_last_name" => $last_name[$key],
                        "familyConstruct" => $familyConstruct,
                        "tenure" => $tenure,
                        "businessType" => $businessType,
                        "policy_member_email_id" => $mem_email_id[$key],
                        "policy_member_mob_no" => $mem_mob_no[$key],
                        //"remark" => $remark
                    ];
                   // print_r($member_array);

                    //CHECK VALIDATIONS
                    $check = $this->check_validations($val, $policy_no, $empId, $age[$key], $age_type[$key], $familyConstruct);

                    // Return Status Message for Validtion Rules
                    if ($check["message"] != "true") {
                        return ["status" => false, "message" => $check["message"]];
                    }
                    $this->db->insert("employee_policy_member", $member_array);
                    //print_pre($this->db->last_query());
                    $id_members = $this->db->insert_id();

                    //Add Data into logs Table
                    $logs_array['data'] = ["type" => "insert_insured_member", "req" => json_encode($member_array), "lead_id" => $lead_id, "product_id" => $product_id];
                    $this->Logs_m->insertLogs($logs_array);

                    if ($this->db->affected_rows()) {
                        // echo "here==".$product_id;
$go = true;


                        //$data = $this->get_all_member_data_new($empId, $policy_no);
                        $arr[] = 1;
                    } else {
                        $arr[] = 0;
                    }
                }


                }
if($go == true)
{
    $data = $this->get_all_member_data_new($empId, $policy_no);

}

        }
        //print_R($data);die;

        $test = ["status" => true, "message" => $msg, "data" => $data];
        if(in_array(0, $arr)){
            $test = ["status" => false, "message" => "something went wrong"];
        }
        return $test;

        // exit;
        /*$test = ["status" => true, "message" => "Sucessfully Changed", "data" => $data];
                if(in_array(0, $arr)){
                    $test = ["status" => false, "message" => "something went wrong"];
                }
                return $test;*/
    }

    public function member_declare_data($parent_id)
    {
        $parent_id = 'IQDp5ebcd2b721105';
        $datas=[];
        $data=array();
        $data1 = $this->db
            ->select('*')
            ->from('policy_declaration_member')
            ->where('parent_policy_id', $parent_id)
            ->where('declare_subtype_id is not null',NULL,false)
            ->get()
            ->result_array();
        //echo $this->db->last_query();exit;
        $arr_merge = array_merge($data, $data1);
        array_push($datas, $arr_merge);
        if(!empty($arr_merge))
        {
            return $datas;
        }
        else
        {
            return $datas=[];
        }

        return $datas;
    }
    function get_family_details_from_relationship()
    {

        extract($this->input->post(null, true));
        //encrypt change
//print_r($_SESSION);
        $arr = [];
        $emp_id = $this->emp_id;

        if ($relation_id == 0) {
            $response = $this->db->select('ed.emp_id,ed.emp_code,ed.emp_firstname,,ed.emp_middlename,ed.emp_lastname,ed.fr_id,ed.company_id,ed.gender,ed.bdate,ed.mob_no,ed.email,ed.emp_grade,ed.emp_designation,ed.emp_address,ed.emp_city,ed.emp_state,ed.emp_pincode,ed.street,ed.location,ed.flex_amount,ed.total_salary,ed.gmc_grade_id,ed.emp_pay,ed.doj,fr.family_relation_id,fr.emp_id,fr.family_id')
                ->from('employee_details as ed,family_relation as fr')
                ->where('ed.emp_id = fr.emp_id')
                ->where('fr.family_id', 0)
                ->where('fr.emp_id', $emp_id)
                ->get()->result_array();
//echo $this->db->last_query();exit;
            return $response;
        } else {
            $responses = $this->db->select('efd.*')
                // ->from('employee_family_details as efd,family_relation as fr')
                ->from('employee_policy_member as efd,family_relation as fr')
                ->where('efd.family_relation_id = fr.family_relation_id')
                ->where('fr_id', $relation_id)
                ->where('fr.emp_id', $emp_id)
                ->get()->result_array();
            //echo $this->db->last_query();

            $data = $this->db->query("select dob as policy_mem_dob,rel_key,fr_id from modal_age_dob where fr_id ='$relation_id' and emp_id = '$emp_id'")->result_array();
//print_R($data);die;
            if(count($data)>0){

                if($data[0]['rel_key'] == 'A')
                {
                    $res = $this->db->select('efd.*')
                        //->from('employee_family_details as efd,family_relation as fr')
                        ->from('employee_policy_member as efd,family_relation as fr')
                        ->where('efd.family_relation_id = fr.family_relation_id')
                        ->where('fr_id', $relation_id)
                        ->where('fr.emp_id', $emp_id)
                        ->get()->result_array();
                    if(count($res)> 0){
                        $response = $res;
                    }else{
                        $response = $data;
                    }
                    //$response = $data;
                    return $response;
                }
                if($data[0]['rel_key'] == 'K')
                {
                    $i = 0 ;
                    $len = count($data);
                    foreach($data as $key=>$res){

                        $fr_id = $res['fr_id'];
                        $dob =  $res['policy_mem_dob'];
                        $res_k = $this->db->select('efd.*')
                            //->from('employee_family_details as efd,family_relation as fr')
                            ->from('employee_policy_member as efd,family_relation as fr')
                            ->where('efd.family_relation_id = fr.family_relation_id')
                            ->where('fr_id', $fr_id)
                            ->where('policy_mem_dob', $dob)
                            ->where('fr.emp_id', $emp_id)
                            ->get()->row_array();

                        if(count($res_k)<=0)
                        {

                            $response[]['policy_mem_dob'] = $res['policy_mem_dob'];
                            $response[]['fr_id'] = $res['fr_id'];
                            //array_push($response,$res['policy_mem_dob']);

                        }
                    }

                    if($nominee==''){
                        if(!empty($response))
                        {
                            return $response;
                        }
                        else{return $responses;}
                    }else{return $responses;}


                }

            }
            else{
                return $responses;
            }
        }


    }
    public function family_details_relation()
    {

        $family_details_post = $this->input->post(null,true);

        if($family_details_post != null || !empty($family_details_post))
            $relation_id = $family_details_post['relation_id'];
        $emp_id = $this->emp_id;
        if ($relation_id == 0)
        {
            $response = $this->db->select('ed.product_id,ed.GCI_optional,ed.occupation,ed.annual_income,ed.emp_id,ed.emp_code,ed.salutation,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,ed.fr_id,ed.company_id,ed.gender,ed.bdate,ed.mob_no,ed.email,ed.emp_grade,ed.emp_designation,ed.emp_address,ed.emp_city,ed.emp_state,ed.emp_pincode,ed.street,ed.location,ed.flex_amount,ed.total_salary,ed.gmc_grade_id,ed.emp_pay,ed.doj,fr.family_relation_id,fr.emp_id,fr.family_id')
                ->from('employee_details as ed,family_relation as fr')
                ->where('ed.emp_id = fr.emp_id')
                ->where('fr.family_id', 0)
                ->where('fr.emp_id', $emp_id)
                ->get()->result_array();

            return $response;
        }
        else
        {
            $response = $this->db->select('efd.*')
                ->from('employee_policy_member as efd,family_relation as fr')
                ->where('efd.family_relation_id = fr.family_relation_id')
                ->where('fr_id', $relation_id)
                ->where('fr.emp_id', $emp_id)
                ->get()->result_array();

            return $response;
        }
    }
    public function get_all_policy_data($parent_id)
    {


        $data = $this->db
            ->select('pms.master_policy_no,pms.product_code,pms.product_name,pms.policy_parent_id,epd.policy_detail_id,pms.combo_flag,mfr.fr_id,mfr.fr_name,mbir.relationship_id,mbir.max_adult,mbir.max_child,epd.policy_sub_type_id,epd.suminsured_type,mpst.policy_sub_type_name,mfr.gender_option')
            ->from('product_master_with_subtype as pms, employee_policy_detail as epd,master_broker_ic_relationship as mbir, master_family_relation as mfr,master_policy_sub_type as mpst')
            ->where('pms.id = epd.product_name')
            ->where('epd.policy_detail_id = mbir.policy_id')
            ->where('pms.policy_subtype_id = mpst.policy_sub_type_id')
            ->where('find_in_set(mfr.fr_id, mbir.relationship_id)')
            ->where('pms.policy_parent_id', $parent_id)
            //->group_by('pms.id')
            ->get()
            ->result_array();

        $check = [];

        $new_array = [];
        if ($data[0]['combo_flag'] == "Y")
        {

            for ($i = 0; $i < count($data); $i++)
            {
                $data[$i]['policy_sub_type_id'] = 1;

                $get_sub_name = $this->get_sub_name($parent_id);
                $data[$i]['policy_sub_type_name'] = $get_sub_name;

                if (empty($check))
                {

                    array_push($check, $data[$i]['fr_id']);
                    array_push($new_array, $data[$i]);
                }
                else
                {
                    if (!(in_array($data[$i]['fr_id'], $check)))
                    {
                        array_push($check, $data[$i]['fr_id']);
                        array_push($new_array, $data[$i]);
                    }
                }
            }


            return $new_array;
        }

        return $data;


    }

    function get_child_count($emp_id, $policy_detail_id)
    {//echo 56;die;

        $response = $this->db->query('SELECT epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id

            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            and mfr.fr_id  IN ("2", "3")
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();

        return ["count" => count($response)];
    }
    function get_adult_count($emp_id, $policy_detail_id)
    {

        $response = $this->db->query('SELECT epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id

            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            and mfr.fr_id NOT IN ("2", "3")
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = '.$policy_detail_id)->result_array();

        return ["count" => count($response)];
    }

    public function get_deductable_amount()
    {
        $arr =[];
//echo 123;die;
        $policy_no = $this->input->post("policy_no");
        $sum_insure = $this->input->post("sum_insure");



            $get_table = $this->db->query("select suminsured_type from employee_policy_detail where policy_detail_id = '$policy_no'")->row_array();
            if($get_table['suminsured_type'] == 'family_construct')
            {
                $table = 'family_construct_wise_si';
            }
            elseif($get_table['suminsured_type'] == 'family_construct_age')
            {
                $table = 'family_construct_age_wise_si';
            }
            else
            {
                $table = 'policy_creation_age';
            }
            if($table == 'policy_creation_age')
            {
                $data = $this->db->select("deductable")
                    ->from($table)
                    ->where("policy_id", $policy_no)
                    ->where("sum_insured", $sum_insure)
                    ->group_by("family_type")
                    ->get()
                    ->result_array();
            }else {
                $data = $this->db->select("deductable")
                    ->from($table)
                    ->where("policy_detail_id", $policy_no)
                    ->where("sum_insured", $sum_insure)
                    ->group_by("family_type")
                    ->get()
                    ->result_array();

            }
            //print_R($this->db->last_query());
           array_push($arr,$data);
            // return $data;

        // print_r($arr);
        $abc =[];
        foreach($arr as $keys => $val)
        {
            //print_r($val);
            foreach($val as $v)
            {

                array_push($abc,$v['deductable']);

            }
            //array_combine($abc,$val);
        }
        $return_arr = array_values(array_unique($abc));
//print_R($return_arr);
        return $return_arr;
    }

    public function get_family_construct()
    {
$arr =[];
//echo 123;die;
        $product_id = $this->input->post("product_id");
        $data = $this->db
            ->select('*')
            ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
            ->where('pms.id = epd.product_name')
            ->where('pms.product_code', $product_id)
            ->get()
            ->result_array();

  foreach($data as $key => $value) {
      // print_r($value);
     $parent_policy_id = $value['parent_policy_id'];
     $get_table_det = $this->db->query("select policy_detail_id,suminsured_type from employee_policy_detail where parent_policy_id = '$parent_policy_id'")->result_array();
      foreach ($get_table_det as $get_table) {
      $policy_id = $get_table['policy_detail_id'];
      if ($get_table['suminsured_type'] == 'family_construct') {
          $table = 'family_construct_wise_si';
      } elseif ($get_table['suminsured_type'] == 'family_construct_age') {
          $table = 'family_construct_age_wise_si';
      } else {
          $table = 'policy_creation_age';
      }
      if ($table == 'policy_creation_age') {
          $data = $this->db->select("family_type")
              ->from($table)
              ->where("policy_id", $policy_id)
              //->where("sum_insured", $sumInsured)
              ->group_by("family_type")
              ->get()
              ->result_array();
      } else {
          $data = $this->db->select("family_type")
              ->from($table)
              ->where("policy_detail_id", $policy_id)
              //->where("sum_insured", $sumInsured)
              ->group_by("family_type")
              ->get()
              ->result_array();
      }
      array_push($arr, $data);
      // return $data;
  }
  }
 // print_r($arr);
$abc =[];
  foreach($arr as $keys => $val)
  {
      //print_r($val);
      foreach($val as $v)
      {

          array_push($abc,$v['family_type']);

      }
      //array_combine($abc,$val);
  }
  $return_arr = array_values(array_unique($abc));
//print_R($return_arr);
  return $return_arr;
    }
    public function ghd_declined_insert($myGHD,$emp_id,$log_insert,$lead_id,$product_id)
    {

        if(@$myGHD)
            $query_ghd = $this->db->query("Delete  from  tls_ghd_employee_declare where emp_id ='".$emp_id."'");
        foreach ($myGHD as $key => $row)
        {



            $split_format = explode('_',$row['format']);
            if(!empty($row['format']))
            {
                $GHD_data = [

                    "format" => $split_format[1],
                    "remark" => $row['remark'],
                    "type" => $key,
                    "emp_id" => $emp_id
                ];
                $this->db->insert("tls_ghd_employee_declare", $GHD_data);


                //logs
                $logs_array = ["type" => "insert_member_ghd_declare","req" => json_encode($GHD_data), "lead_id" => $lead_id,"product_id" => $product_id];
                $this->db->insert("logs_post_data",$logs_array);

                if($row['format']== 'B_Yes')
                {

                    $msg = "Enrollment would be declined, Pls check the Product guidelines";

                    return ["status" => "false", "message" => $msg ];
                }
                elseif($row['format']== 'C1_Yes' || $row['format']== 'C2_Yes')
                {
                    $msg = "Enrollment would be declined, Pls check the Product guidelines";
                    return ["status" => "false", "message" => $msg ];
                }
            }



        }
    }
    function get_adult_count_new($emp_id, $policy_detail_id,$ids = 0)
    {

        $response = $this->db->query('SELECT epm.policy_mem_dob,epm.policy_mem_gender,epm.family_relation_id,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_mem_dob,epm.policy_mem_gender,epm.family_relation_id,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id

            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            and mfr.fr_id  IN ('.$ids.')
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();


        return ($response);
    }

    public function get_premium_new()
    {
        // echo 123;die;
        extract($this->input->post(null, true));
        $ew_status = 0;//$this->EW_status($emp_id);
        //sum insured
        if($product_id == 'T01'){
            if($gci_optional == 'Yes'){
                $q = "SELECT epd.policy_detail_id as policy_detail_id FROM product_master_with_subtype as pms,employee_policy_detail as epd WHERE pms.id = epd.product_name AND pms.product_code = '".$product_id."' AND pms.policy_subtype_id = 3";
                $gci_policy_detail_id = $this->db->query($q)->row_array()['policy_detail_id'];
                $policy_detail_id = $policy_detail_id.",".$gci_policy_detail_id;
            }

        }
        //family construct
        $policy_detail_ids = (explode(",", $policy_detail_id));
        //print_pre($policy_detail_ids);exit;
        for ($i = 0; $i < count($policy_detail_ids); $i++) {
            $check_gmc = $this->db
                ->select('*')
                ->from('employee_policy_detail as epd')
                ->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
                ->where('epd.policy_detail_id', $policy_detail_ids[$i])
                ->get()
                ->row_array();

            //upendra - 29-06-2021
            // if($check_gmc['policy_sub_type_id'] == '1' && $product_id == 'T01')

            //health pro infinity - premium breakup changes - akash/upendra
            if(($check_gmc['policy_sub_type_id'] == '1' && $product_id == 'T01') || (($check_gmc['policy_sub_type_id'] == '1') && $product_id == 'T03')){


                $age_array = array();

                $family_construct1 = explode("+", $family_construct);


                if($family_construct1[0] == '2A'){
                    $q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                    $bdate = $this->db->query($q1)->row_array()['bdate'];
                    $birthdate = new DateTime($bdate);
                    $today   = new DateTime('today');
                    $age_purchaser = $birthdate->diff($today)->y;
                    array_push($age_array,$age_purchaser);

                    $q1 = "SELECT spouse_dob,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                    $bdate = $this->db->query($q1)->row_array()['spouse_dob'];
                    $birthdate = new DateTime($bdate);
                    $today   = new DateTime('today');
                    $age_spouse = $birthdate->diff($today)->y;
                    array_push($age_array,$age_spouse);

                }else if($family_construct1[0] == '1A'){

                    $q1 = "SELECT policy_for,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                    $policy_for = $this->db->query($q1)->row_array()['policy_for'];

                    if($policy_for == "0"){

                        $q1 = "SELECT bdate,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                        $bdate = $this->db->query($q1)->row_array()['bdate'];
                        $birthdate = new DateTime($bdate);
                        $today   = new DateTime('today');
                        $age_purchaser = $birthdate->diff($today)->y;
                        array_push($age_array,$age_purchaser);

                    }else if($policy_for == "1"){

                        $q1 = "SELECT spouse_dob,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                        $bdate = $this->db->query($q1)->row_array()['spouse_dob'];
                        $birthdate = new DateTime($bdate);
                        $today   = new DateTime('today');
                        $age_spouse = $birthdate->diff($today)->y;
                        array_push($age_array,$age_spouse);

                    }

                }

                if($deductable!='')
                {
                    $ded = "fc.deductable = '$deductable'";
                }else{
                    $ded = "fc.deductable = '0' OR fc.deductable = ''";
                }
                $max_age = max($age_array);

                if($max_age > 45){
                    $age_group = "46-55";
                }else{
                    $age_group = "18-45";
                }

                $premium[$i] = $this->db
                    ->select('*')
                    ->from('family_construct_age_wise_si as fc')
                    ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                    ->where('fc.family_type', $family_construct)
                    ->where('fc.sum_insured', $sum_insured)
                    ->where('fc.age_group', $age_group)
                    ->where($ded,Null,false)

                    ->get()
                    ->row_array();


                // print_r($premium);exit;
                // $premium[$i]['policy_sub_type_id'] = 'Group Mediclaim';
                $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                // print_r($premium);exit;
            }

            else if ($check_gmc['policy_sub_type_id'] == '1') {
                //get gmc premium
                $premium[$i] = $this->db
                    ->select('*')
                    ->from('family_construct_wise_si as fc')
                    ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                    ->where('fc.family_type', $family_construct)
                    ->where('fc.sum_insured', $sum_insured)
                    ->get()
                    ->row_array();
                // $premium[$i]['policy_sub_type_id'] = 'Group Mediclaim';
                $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];

            }else if($check_gmc['policy_sub_type_id'] == '3'){
                // echo $check_gmc['suminsured_type'];exit;
                if($check_gmc['suminsured_type'] == 'memberAge'){

                    $q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                    $bdate = $this->db->query($q1)->row_array()['bdate'];
                    $birthdate = new DateTime($bdate);
                    $today   = new DateTime('today');
                    $age = $birthdate->diff($today)->y;

                    //upendra - 29-06-2021
                    if($product_id == "T01"){
                        $age_array = array();

                        $family_construct1 = explode("+", $family_construct);

                        if($family_construct1[0] == '2A'){
                            $q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                            $bdate = $this->db->query($q1)->row_array()['bdate'];
                            $birthdate = new DateTime($bdate);
                            $today   = new DateTime('today');
                            $age_purchaser = $birthdate->diff($today)->y;
                            array_push($age_array,$age_purchaser);

                            $q1 = "SELECT spouse_dob FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                            $bdate = $this->db->query($q1)->row_array()['spouse_dob'];
                            $birthdate = new DateTime($bdate);
                            $today   = new DateTime('today');
                            $age_spouse = $birthdate->diff($today)->y;
                            array_push($age_array,$age_spouse);

                        }else if($family_construct1[0] == '1A'){

                            $q1 = "SELECT policy_for FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                            $policy_for = $this->db->query($q1)->row_array()['policy_for'];

                            if($policy_for == "0"){

                                $q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                                $bdate = $this->db->query($q1)->row_array()['bdate'];
                                $birthdate = new DateTime($bdate);
                                $today   = new DateTime('today');
                                $age_purchaser = $birthdate->diff($today)->y;
                                array_push($age_array,$age_purchaser);

                            }else if($policy_for == "1"){

                                $q1 = "SELECT spouse_dob FROM employee_details WHERE emp_id = '".$this->emp_id."'";
                                $bdate = $this->db->query($q1)->row_array()['spouse_dob'];
                                $birthdate = new DateTime($bdate);
                                $today   = new DateTime('today');
                                $age_spouse = $birthdate->diff($today)->y;
                                array_push($age_array,$age_spouse);

                            }

                        }


                        $age = max($age_array);
                    }

                    $check_age = $this->db->select("policy_age,premium_with_tax,sum_insured,EW_premium_with_tax")
                        ->from("policy_creation_age")
                        ->where("sum_insured",$sum_insured)
                        ->where("policy_id", $policy_detail_ids[$i])
                        ->get()
                        ->result_array();
                    foreach($check_age as $values_age){
                        $min_max_age = explode("-",$values_age['policy_age']);
                        //echo $age .">=". $min_max_age[0] ."&&". $age ."<=". $min_max_age[1];exit;
                        if((int)$age >= (int)$min_max_age[0] && (int)$age <= (int)$min_max_age[1]){

                            $premium_value  = $values_age['premium_with_tax'];

                            $premium[$i] = $values_age;


                        }
                    }

                    $family_construct1 = explode("+", $family_construct);

                    if($family_construct1[0] == '2A'){
                        $premium_value = $premium_value * 2;
                    }
                    //check data already added or not
                    $fr_q = "SELECT family_relation_id FROM family_relation WHERE emp_id = '".$this->emp_id."'";
                    $frRes = $this->db->query($fr_q)->result_array();
                    if(!empty($frRes)){
                        $frRes = array_column($frRes, 'family_relation_id');
                        //print_pre($frRes);
                        $fr_ids = implode(",", $frRes);
                        $em_q = "SELECT policy_mem_sum_premium FROM employee_policy_member WHERE family_relation_id IN (".$fr_ids.") AND policy_detail_id = '".$check_gmc['policy_detail_id']."'";
                        $emRes = $this->db->query($em_q)->result_array();
                        //echo $em_q;
                        //print_pre($emRes);
                        if(!empty($emRes)){
                            $p = 0;
                            foreach ($emRes as $keyp => $valp) {
                                //echo $valp['policy_mem_sum_premium'];
                                $p += $valp['policy_mem_sum_premium'];
                            }
                            $premium_value = $p;
                        }

                    }
                    //echo $premium_value;exit;
                    $premium[$i]['policy_detail_id'] = $policy_detail_ids[$i];
                    $premium[$i]['PremiumServiceTax'] = $premium_value;
                    $premium[$i]['family_type'] = $family_construct1[0];
                    $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    // print_pre($premium);exit;
                    $ew_status = "0";

                }

            } else {
                $family_construct1 = explode("+", $family_construct);

                //get max adult count
                $premium1 = $this->db
                    ->select('*')
                    ->from('master_broker_ic_relationship as fc')
                    ->where('fc.policy_id', $policy_detail_ids[$i])
                    ->get()
                    ->row_array();

                if ($premium1['max_adult'] . "A" == $family_construct1[0] || $premium1['max_adult'] . "A" > $family_construct1[0]) {
                    $premium[$i] = $this->db
                        ->select('*')
                        ->from('family_construct_wise_si as fc')
                        ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                        ->where('fc.family_type', $family_construct1[0])
                        ->where('fc.sum_insured', $sum_insured)
                        ->get()
                        ->row_array();
                    //echo $this->db->last_query();exit;
                    if ($check_gmc['policy_sub_type_id'] == 2) {
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                        //echo "here123";exit;

                        //health pro infinity - premium breakup changes - akash/upendra
                        if($product_id == "T03"){
                            $family_construct1 = explode("+", $family_construct);


                            $premium[$i] = $this->db
                                ->select('*')
                                ->from('family_construct_age_wise_si as fc')
                                ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                                ->where('fc.family_type', $family_construct1[0])
                                ->where('fc.sum_insured', $sum_insured)
                                ->where('fc.age_group', "18-55")
                                ->get()
                                ->row_array();


                            // print_r($premium);exit;
                            // $premium[$i]['policy_sub_type_id'] = 'Group Mediclaim';
                            $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];

                        }

                    } else {
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    }
                } else {
                    if ($check_gmc['policy_sub_type_id'] == 2) {
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    } else {
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    }
                    $premium[$i] = $this->db
                        ->select('*')
                        ->from('family_construct_wise_si as fc')
                        ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                        ->where('fc.family_type', $premium1['max_adult'] . "A")
                        ->get()
                        ->row_array();
                }
            }

            $premium[$i]['ew_status'] = $ew_status;
        }
        return ($premium);

    }
    public function health_declaration($parent_id)
    {
        if($parent_id == 'test123' || $parent_id == 'NvpnoiwGGDQPVA23w'){
            $data = $this->db
                ->select('policy_declaration.label,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
                ->from('policy_declaration')
                ->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
                ->where('policy_declaration.parent_policy_id', $parent_id)
                ->where('policy_declaration.label', 'epd')
                ->get()
                ->result_array();
        }else{
            $data = $this->db
                ->select('policy_declaration.label,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
                ->from('policy_declaration')
                ->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
                ->where('policy_declaration.parent_policy_id', $parent_id)
                ->get()
                ->result_array();
        }

        // echo $this->db->last_query();exit;
        return $data;
    }

    public function axis_state_city($pincode = null)
    {
        if (isset($_POST['pincode'])) {
            $pincode = $this->input->post('pincode');
        }

        $details = $this->db
            ->select('state,city,state_code')
            ->from('axis_postal_code as pc')

            ->where('pc.pincode', $pincode)
            ->get()
            ->row();

        return $details;
    }

    public function get_common_data()
    {
        $emp_id = $this->emp_id;
//print_R($emp_id);die;
        $data = array();
        $nominee_data = $this->db->query("select mn.nominee_type,mpn.fr_id,mpn.nominee_fname,mpn.nominee_lname,mpn.nominee_salutation,mpn.nominee_gender,mpn.nominee_dob,mpn.nominee_contact,mpn.nominee_email from member_policy_nominee as mpn,master_nominee as mn where mpn.fr_id = mn.nominee_id AND  emp_id = '$emp_id'")->row_array();
        $data['nominee_data'] = $nominee_data;
        $data['base_agent_details'] = $this->db->query("select ba.*,tlob.axis_process,ed.new_remarks,ed.comm_address,ed.comm_address1,ed.emg_cno,ed.axis_lob,ed.axis_location from employee_details as ed,tls_base_agent_tbl as ba,tls_axis_lob as tlob where ba.lob=tlob.axis_lob AND ed.agent_id = ba.base_agent_id AND ed.emp_id = '$emp_id'")->row_array();
        $data['axis_details'] = $this->db->query("select tal.axis_lob,talc.axis_location,ed.axis_process from tls_axis_lob as tal,tls_axis_location as talc,employee_details as ed where ed.axis_lob = tal.axis_lob_id AND ed.axis_location = talc.axis_loc_id AND emp_id = '$emp_id'")->row_array();
        $data['ghd_proposer'] =  $this->db->query("select * from tls_ghd_employee_declare where emp_id = '$emp_id'")->result_array();
        $data['emp_declare'] = $this->db->query("select p_declare_id,format from employee_declare_data where emp_id = '$emp_id'")->result_array();
        $data['emp_details'] = $this->db->select('pid,deductable,product_id,annual_income,occupation,saksham_id,emp_middlename,ISNRI,lead_id,emp_id,emp_code,emp_firstname,emp_lastname,gender,bdate,emg_cno,mob_no,email,emp_address,emp_city,emp_state,emp_pincode,street,location,doj,pancard,adhar,address,comm_address,comm_address1,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,auto_renewal,payment_mode,preferred_contact_date,preferred_contact_time,av_remark,GCI_optional,makerchecker,is_makerchecker_journey')->where(["emp_id" => $emp_id])->get("employee_details")->row_array();

        if($data['emp_details']['occupation'] != ""){
            $data['emp_details']['occupation_name'] = $this->db->get_where("master_occupation",array("id"=>$data['emp_details']['occupation']))->row_array()['name'];

        }
        return $data;
    }

    public function add_agent_emp_details($emp_id,$emp_agent_data)
    {//echo 123;die;
       // print_r($emp_id);exit;

        // print_pre($this->input->post('email'));exit;
        $employee_policy_member_email_update=$this->input->post('email');

        $this->db->where('emp_id',$emp_id);
        $this->db->update('employee_details',$emp_agent_data);




        $data = $this->db
            ->select('*')
            ->from('employee_policy_member epm,family_relation fr,employee_family_details efd')
            ->where('efd.family_id = fr.family_id')
            ->where('fr.family_relation_id = epm.family_relation_id')
            ->where('fr.emp_id',$emp_id)
            ->where('efd.fr_id',1)
            ->get()
            ->result_array();

        if(!empty($data)){
            if($emp_agent_data['gender'] == 'Male'){
                $update_gender = 'Female';
            }else{
                $update_gender = 'Male';
            }
            foreach($data as $value){
                $this->db->where('policy_member_id', $value['policy_member_id']);
                $this->db->update('employee_policy_member',['policy_mem_gender' => $update_gender]);

                $proposal_data = $this->db->select('*')->from('proposal_member')->where('policy_member_id',$value['policy_member_id'])->get()->row_array();
                if(!empty($proposal_data)){
                    $this->db->where('policy_member_id', $value['policy_member_id']);
                    $this->db->update('proposal_member',['policy_mem_gender' => $update_gender]);

                }
            }
        }

        $get_family_relation_id=$this->db->select('family_relation_id,emp_id')->from('family_relation')->where('emp_id',$emp_id)->where('family_id',0)->get()->row_array();

        if($get_family_relation_id){
            $this->db->where('family_relation_id',$get_family_relation_id['family_relation_id'])->update('employee_policy_member',['policy_member_email_id' => $employee_policy_member_email_update]);
        }

        // print_pre($get_family_relation_id['family_relation_id']);exit;


    }
    function get_agent_details()
    {

        $agent_post = $this->input->post(null,true);


        $agent_id = $this->agent_id;

        $data = $this->db
            ->select('agent_id,agent_name,tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name,axis_process')
            ->from('tls_agent_mst')
            ->where('id', $agent_id)
            ->get();

        if(!empty($agent_post) && !empty($agent_post['agent_id']))
        {

            $base_agent_id = $agent_post['agent_id'];
            $axis_process=$agent_post['axis_process'];

            $data =    $this->db
                ->select('tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name,base_agent_name,imd_code,center,lob,vendor,axis_lob_id,axis_process')
                ->from('tls_base_agent_tbl')
                ->join('tls_axis_lob','tls_base_agent_tbl.lob=tls_axis_lob.axis_lob','left')
                ->where('base_agent_id', $base_agent_id)
                ->get();

        }
        if($data->num_rows()>0)
        {

            $result = $data->row_array();

            return $result;
        }

    }


    public function get_all_policy_data_new($parent_id)
    {

        $data = $this->db
            ->select('pms.product_code,pms.master_policy_no,pms.product_name,pms.policy_parent_id,epd.policy_detail_id,pms.combo_flag,epd.suminsured_type')
            ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
            ->where('pms.id = epd.product_name')
            ->where('pms.`is_telesales_product` ', 1)
            ->group_by('pms.product_name')
            ->get()
            ->result_array();
        if(!empty($data)){
            $planDrpdown = '<option value="">Select Product Name</option>';
            foreach($data as $val)
            {
                $cls = '';
                if($_SESSION['telesales_session']['product_code'] == $val['product_code']){
                    $cls = 'selected';
                }
                $planDrpdown .= '<option value="'.$val['product_code'].'" '.$cls.'>'.$val['product_name'].'</option>';

            }
        }
        // echo $this->db->last_query();exit;
        return $planDrpdown;

    }

    function check_validations($relation_id, $policy_detail_id, $empId, $age,$age_type,$familyConstruct,$edit = 0)
    {

        if ($relation_id == 0)
        {
            $family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", $relation_id)->get()->row_array();
        }
        else
        {
            $family_relation_id = $this->db->query('SELECT *
               FROM 
               employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
               master_family_relation AS mfr
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id

               AND fr.family_id = efd.family_id 
               AND efd.fr_id = mfr.fr_id
               AND fr.emp_id = ' . $empId . '
               and mfr.fr_id = ' . $relation_id . '
               AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->row_array();
        }
//print_r($this->db->last_query());die;
        if ($family_relation_id)
        {
            $check = $this->db->select("*")->from("employee_policy_member")->where("family_relation_id", $family_relation_id["family_relation_id"])->where("policy_detail_id", $policy_detail_id)->get()->row_array();
            if ($relation_id != '2' && $relation_id != '3' && $edit == 0)
            {
                if (count($check) > 0)
                {
                    //check if self is already present
                    return ["message" => "Member Already Exists"];
                }
            }

        }
        //now check max adult count
        $check_min_max = $this->db->select("*")->from("policy_age_limit,master_family_relation")->where("policy_detail_id", $policy_detail_id)->where("policy_age_limit.relation_id", $relation_id)
            ->where("master_family_relation.fr_id = policy_age_limit.relation_id")
            ->get()->row_array();

        if ($check_min_max['max_age'] != 0)
        {
            if($age_type == 'days' && ($relation_id == 2 || $relation_id == 3))
            {
                if($age < 91)
                {
                    return ["message" => $check_min_max['fr_name']." age should be greater than 91 days"];

                }
                else
                {

                    $age = 1;
                }
            }
            if($age_type == 'days' && ($relation_id != 2 &&  $relation_id != 3))
            {
                return ["message" => "Min age for ".$check_min_max['fr_name']. " is ".$check_min_max['min_age']." and max age
				is ".$check_min_max['max_age'] ];
            }

            //check age between max  and min
            if($age_type)
            {
                if (!($age >= $check_min_max['min_age'] && $age <= $check_min_max['max_age']))
                {
                    if($check_min_max['min_age'] == 0){
                        $check_min_max['min_age'] = "91 days";
                    }

                    return ["message" => "Min age for ".$check_min_max['fr_name']. " is ".$check_min_max['min_age']." and max age is ".$check_min_max['max_age']. " years" ];

                }
            }
        }

        //now check max adult count
        if($edit == 0)
        {
            if ($relation_id != '2' && $relation_id != '3')
            {
                $max_adult = $this->db->select("*")->from("master_broker_ic_relationship")->where("policy_id", $policy_detail_id)->get()->row_array();
                $count = ($this->get_adult_count($empId, $policy_detail_id));
                $count = $count['count'];


                if ($count >= $max_adult['max_adult'])
                {

                    return ["message" => "Adult count exceeded"];
                }


            }
            else
            {
                 $max_child = $this->db->select("*")->from("master_broker_ic_relationship")->where("policy_id", $policy_detail_id)->get()->row_array();
                if($max_child['max_child'] !=0) {
                    $count = ($this->get_child_count($empId, $policy_detail_id));

                    $count = $count['count'];

                    if ($count >= $max_child['max_child']) {

                        return ["message" => "Child count exceeded"];
                    }
                }
            }




        }

        $check1 = $this->check_validations_adult_count_add_member($familyConstruct,$empId,$policy_detail_id,$relation_id,$edit);
        if(!$check1)
        {

            return ["message" => "Cannot add member as per selection"];
        }

        return ["message" => "true"];
    }

    public function get_suminsured_data($parent_id,$product_id,$family_construct)
    {


        $family_construct1 = explode("+", $family_construct);

$permile = 'No';
            $data = $this->db
                ->select('*')
                ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
                ->where('pms.id = epd.product_name')
                ->where('pms.product_code', $product_id)
                ->get()
                ->result_array();
//print_r($data);die;
        for ($i = 0; $i < count($data); $i++) {
            //get max adult count
            $fc_relation = $this->db
                ->select('*')
                ->from('master_broker_ic_relationship as fc')
                ->where('fc.policy_id', $data[$i]['policy_detail_id'])
                ->get()
                ->row_array();
            if(($fc_relation['max_adult'] !=0 && $fc_relation['max_child'] !=0) && (!empty($family_construct1[0]) && !empty($family_construct1[1]))) {

                if ($fc_relation['max_adult'] . "A" >= $family_construct1[0] && $fc_relation['max_child'] . "K" >= $family_construct1[1]) {
                    $famConstruct = $family_construct;
                }else{
                    $famConstruct = $fc_relation['max_adult'] . "A+".$fc_relation['child_count']."K";
                }
            }
            else{

                if ($fc_relation['max_adult'] . "A" >= $family_construct1[0] ) {
                    $famConstruct = $family_construct;
                }else{
                    $famConstruct = $fc_relation['max_adult'];
                }
            }
         //   $data1[$data[$i]['policy_detail_id']]['permile'] = array();
            if ($data[$i]['suminsured_type'] == "flate") {
                $data1[$data[$i]['policy_detail_id']] = $this->db
                    ->select('pcapremium.sum_insured,mpst.policy_sub_type_name,mpst.short_code,pcapremium.deductable')
                    ->from('employee_policy_detail as epd')
                    //->join('policy_creation_age_bypremium as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
                    ->join('policy_creation_flate as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
                    ->join('master_policy_sub_type as mpst', 'epd.policy_sub_type_id = mpst.policy_sub_type_id', 'left')

                    ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                    ->group_by("pcapremium.sum_insured")


                    ->get()
               // print_r($this->db->last_query());

                   ->result_array();
              //  $data1[$data[$i]['policy_detail_id']]['permile'] = 'No';
                $permile = 'No';
                $data1[$data[$i]['policy_detail_id']][0]['permile'] = $permile;



            }
            if ($data[$i]['suminsured_type'] == "family_construct") {
                if ($data[$i]['combo_flag'] == "Y") {

                    $policy_ids[] = $data[$i]['policy_detail_id'];


                }
                //print_pre($policy_ids);exit;
                $data1[$data[$i]['policy_detail_id']] = $this->db
                    ->select('pcapremium.sum_insured,mpst.policy_sub_type_name,mpst.short_code,pcapremium.deductable')
                    ->from('employee_policy_detail as epd,family_construct_wise_si as pcapremium')
                    ->where('epd.policy_detail_id = pcapremium.policy_detail_id')
                    ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])

                    ->join('master_policy_sub_type as mpst', 'epd.policy_sub_type_id = mpst.policy_sub_type_id', 'left')
                    ->where("pcapremium.family_type",$famConstruct)

                    ->group_by("pcapremium.sum_insured")
                    ->get()
                    ->result_array();
                $variable = 'family_construct';
                $permile = 'No';
                $data1[$data[$i]['policy_detail_id']][0]['permile'] = $permile;

                //  $data1[$data[$i]['policy_detail_id']]['permile'] = 'No';

            }
            if ($data[$i]['suminsured_type'] == "memberAge") {
                if ($data[$i]['combo_flag'] == "Y") {
                    $variable = 'memberAge';
                    $policy_ids[] = $data[$i]['policy_detail_id'];


                }

                $data1[$data[$i]['policy_detail_id']] = $this->db
                    ->select('pcapremium.sum_insured,mpst.policy_sub_type_name,mpst.short_code,pcapremium.deductable')
                    ->from('employee_policy_detail as epd')
                    ->join('product_master_with_subtype as pms','pms.id = epd.product_name')
                    ->join('policy_creation_age as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
                    ->join('master_policy_sub_type as mpst', 'epd.policy_sub_type_id = mpst.policy_sub_type_id', 'left')

                    //->join('policy_creation_age_bypremium as pcapremium1', 'epd.policy_detail_id = pcapremium1.policy_id', 'left')
                    ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                   // ->where("pcapremium.family_type",'$famConstruct')

                    ->group_by("pcapremium.sum_insured")
                    ->get()
                    ->result_array();
              // $data1[$data[$i]['policy_detail_id']]['permile'] = 'No';
                $permile = 'No';
                $data1[$data[$i]['policy_detail_id']][0]['permile'] = $permile;



            }
            if ($data[$i]['suminsured_type'] == "family_construct_age") {
                if ($data[$i]['combo_flag'] == "Y") {
                    $policy_ids[] = $data[$i]['policy_detail_id'];

                }

                $data1[$data[$i]['policy_detail_id']]= $this->db
                    ->select('pcapremium.sum_insured,mpst.policy_sub_type_name,mpst.short_code,pcapremium.deductable')
                    ->from('employee_policy_detail as epd,family_construct_age_wise_si as pcapremium')
                    ->join('master_policy_sub_type as mpst', 'epd.policy_sub_type_id = mpst.policy_sub_type_id', 'left')

                    ->where('epd.policy_detail_id = pcapremium.policy_detail_id')

                    ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                    ->where("pcapremium.family_type",$famConstruct)

                    ->group_by("pcapremium.sum_insured")
                    ->get()
                    ->result_array();
                //print_r($this->db->last_query());
               // $data1[$data[$i]['policy_detail_id']]['permile'] = 'No';
                $permile = 'No';
                $data1[$data[$i]['policy_detail_id']][0]['permile'] = $permile;


            }

            if ($data[$i]['suminsured_type'] == "permilerate") {
                $famConstruct = '1A';
                $data1[$data[$i]['policy_detail_id']]= $this->db
                    ->select('pcapremium.policy_rate,pcapremium.tenure,mpst.policy_sub_type_name,mpst.short_code,pcapremium.deductable')
                    ->from('employee_policy_detail as epd,master_policy_premium_permile as pcapremium')
                    ->join('master_policy_sub_type as mpst', 'epd.policy_sub_type_id = mpst.policy_sub_type_id', 'left')

                    ->where('epd.policy_detail_id = pcapremium.master_policy_id')

                    ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                    //->where("pcapremium.family_type",$famConstruct)

                   // ->group_by("pcapremium.sum_insured")
                    ->get()
                //print_r($this->db->last_query());

                 ->result_array();
$permile = 'Yes';
                $data1[$data[$i]['policy_detail_id']][0]['permile'] = $permile;

               // $data1[$data[$i]['policy_detail_id']]['permile'] = 'Yes';
            }
          //  array_push($data1[$data[$i]['policy_detail_id']],$permile);
       //  print_r($data1[$data[$i]['policy_detail_id']][0]['permile']);
           // print_r($data1);

        }
        $arr_0 = [];
     // print_r($data1);die;

        $new_arr=[];
        $i=0;
foreach($data1 as $key => $value){
   // print_r($value[0]['permile']);
    foreach($value as $key1=>$val){
        //print_r($val['permile']);

       // $arr_0[$key]['sum_insured'][$key1] = $val['sum_insured'];
      //  $arr_0[$key]['policy_sub_type_name'] = $val['policy_sub_type_name'];
       $arr_0[$i]['short_code'] = $val['short_code'];
        $arr_0[$i]['permile'] = $value[0]['permile'];

        $arr_0[$i]['policy_sub_type_name'] = $val['policy_sub_type_name'];
        $arr_0[$i]['sum_insured'][$key1] = $val['sum_insured'];
        $arr_0[$i]['policy_id'] = $key;
        if($val['deductable'] !=0 && $val['deductable'] !='')
        {
            $arr_0[$i]['deductable'] = 'yes';

        }
        else{
            $arr_0[$i]['deductable'] = 'no';

        }


    }
    $i++;

}
        return array_values($arr_0);


    }

    function get_all_member_data_new($emp_id, $policy_detail_id,$deductable = 0)
    {

        $response = $this->db
            ->query('SELECT epm.policy_member_mob_no,epm.policy_member_email_id,epm.fr_id,epm.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,epm.policy_member_email_id,epm.policy_member_mob_no
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_member_mob_no,epm.policy_member_email_id,epm.fr_id,epm.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,epm.policy_member_email_id,epm.policy_member_mob_no
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
        //print_r($this->db->last_query());


        if($response[0]['suminsured_type'] == 'family_construct_age')
        {
            $change_premium = false;

            foreach($response as $value)
            {
                //$age[] = $value['age'];
                if($value['age_type'] == 'days'){
                    $age[] = 0;
                }else{
                    $age[] = $value['age'];
                }

            }

            $check = $this->db->select("*")
                ->from("family_construct_age_wise_si")
                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                ->where("family_type", $response[0]['familyConstruct'])
                ->where("policy_detail_id", $policy_detail_id)
                ->get()
                ->result_array();
            $max_age = max($age);

            foreach($check as $value)
            {
                $min_max_age = explode("-",$value['age_group']);
                if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1])
                {
                    $premium = $value['PremiumServiceTax'];
                }
            }
            // echo "here";exit;
            foreach($response as $value1)
            {
                //if($policy_detail_id != TELE_HEALTHPROINFINITY_GPA){
                    $this->db->where('policy_member_id', $value1['policy_member_id']);
                    $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
                    if($this->db->affected_rows())
                    {
                        $change_premium = true;
                    }
              //  }

            }

            if($change_premium)
            {

                $response[0]["message"] = "Premium has been changed as per your inputs to ".$premium;
                $response[0]["new_premium"] = $premium;
            }


        }
//print_R($response);die;
        return $response;
    }

}
?>