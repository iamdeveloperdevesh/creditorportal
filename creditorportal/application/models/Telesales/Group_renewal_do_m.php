<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Group_renewal_do_m extends CI_Model
{

    

    function __construct()
    {
        parent::__construct();
      
        $telSalesSession = $this->session->userdata('telesales_session');

    }
	


	public function tele_renewal_group_do($confirm_policy_coi_number){

		extract($this->input->post(null, true));
        // $lead_id_grp = $lead_id_grp_table;
		$url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalCheck";

		$data = array(
            "Lead_Id" => "",
            "master_policy_number" => "",
            "certificate_number" => $confirm_policy_coi_number,
            "dob" => "",
            "proposer_mobileNumber" => "",
        );
        $data_string = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $result = curl_exec($curl);

        $info = curl_getinfo($curl);
        $response_time_renewal = $info['total_time'];
        curl_close($curl);


        $djson = json_decode($result, TRUE);

		return json_encode($djson);
	}

    public function tele_renewal_group_do_address_api($coi_number){
        $otherdataapi="https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyDetail/".$coi_number."/null";
        $othercurl = curl_init();
        curl_setopt_array($othercurl, array(
            CURLOPT_URL => $otherdataapi,
            CURLOPT_RETURNTRANSFER => true,    
        ));
        $otherresult = curl_exec($othercurl);
        curl_close($othercurl);        
        // $otherdata=json_encode($otherresult,TRUE);
        $xml_snippet = simplexml_load_string( $otherresult );
        $other_json_convert = json_encode( $xml_snippet,TRUE );
        return $other_json_convert;

    }


    public function generate_lead_id()
    {
        $lead_id = time();
        $duplicate_check = $this
            ->db
            ->get_where("group_mod_create", array(
            "lead_id" => $lead_id
        ))->row_array();
        if (!empty($duplicate_check))
        {
            $this->generate_lead_id();
        }
        return $lead_id;
    }


    public function create_short_url($lead_id_encrypt){

            // $url = base_url('check_bitly_renewal/' . $insert_id_trigger_encrypt);
            $url = base_url('group_renewal_modify_view/' . $lead_id_encrypt);
            $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url_req,
                //CURLOPT_PROXY => "185.46.212.88",
                //CURLOPT_PROXYPORT => 443,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                //CURLOPT_POSTFIELDS => $parameters,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",

                ),
            ));

            $result = curl_exec($curl);
            curl_close($curl);

            $data_txtly = json_decode($result, true);

            if ($data_txtly['txtly'] == '') {
                $data_txtly['txtly'] = $url;
            }


            if (!preg_match("@^[hf]tt?ps?://@", $data_txtly['txtly'])) {
                $data_txtly['txtly'] = "http://" . $data_txtly['txtly'];
            }



            return json_encode($data_txtly);

    }

    public function get_family_construct($sumInsured,$table,$policyNo)
	{
		
			if($table == 'family_construct')
			{
				$table = 'family_construct_wise_si';
			}
			else
			{
				$table = 'family_construct_age_wise_si';
			}
			$data = $this->db->select("*")
					->from($table)
					->where("policy_detail_id", $policyNo)
					->where("sum_insured", $sumInsured)
					->group_by("family_type")
					->get()
					->result_array();
		
		return $data;
	}

    public function  get_suminsured_data($parent_id,$product_id) 
	{
		if($product_id == 'T03'){
        	$data = $this->db
		    ->select('*')
		    ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
		    ->where('pms.id = epd.product_name')
		    ->where('pms.product_name', TELE_HEALTHPROINFINITY_PRODUCT_NAME)
		    ->group_by('pms.policy_subtype_id')
		    ->get()
		    ->result_array();
		    //echo $this->db->last_query();exit;
	    }else{
			$data = $this->db
			->select('*')
			->from('product_master_with_subtype as pms, employee_policy_detail as epd')
			->where('pms.id = epd.product_name')
			->where('pms.product_code', $product_id)
			->get()
			->result_array();
		}
		for ($i = 0; $i < count($data); $i++) {
		if ($data[$i]['suminsured_type'] == "flate" && ($data[$i]['premium_type'] == "flate1" || $data[$i]['premium_type'] == "memberage1")) {
		$data1[$i]['flate'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd')
		->join('policy_creation_age_bypremium as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
		->join('policy_creation_designation_bypremium as pcdpremium', 'epd.policy_detail_id = pcdpremium.policy_id', 'left')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->get()
		->result_array();
		}
		if ($data[$i]['suminsured_type'] == "family_construct") {
		if ($data[$i]['combo_flag'] == "Y") {
			
		$policy_ids[] = $data[$i]['policy_detail_id'];
		
		
		}
		// print_pre($data1);exit;
		$data1[$i]['family_construct'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd,family_construct_wise_si as pcapremium')
		->where('epd.policy_detail_id = pcapremium.policy_detail_id')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->group_by("pcapremium.sum_insured")
		->get()
		->result_array();
		$variable = 'family_construct';
		}
		if ($data[$i]['suminsured_type'] == "memberAge") {
			if ($data[$i]['combo_flag'] == "Y") {
			$variable = 'memberAge';
		$policy_ids[] = $data[$i]['policy_detail_id'];
		
		
		}

		$data1[$i]['memberAge'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd')
		->join('product_master_with_subtype as pms','pms.id = epd.product_name')
		->join('policy_creation_age as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
		//->join('policy_creation_age_bypremium as pcapremium1', 'epd.policy_detail_id = pcapremium1.policy_id', 'left')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->group_by("pcapremium.sum_insured")
		->get()
		->result_array();
		
		
			
		
		}
		if ($data[$i]['suminsured_type'] == "family_construct_age") {
		if ($data[$i]['combo_flag'] == "Y") {
		$policy_ids[] = $data[$i]['policy_detail_id'];

		}
		
		
		$data1[$i]['family_construct_age'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd,family_construct_age_wise_si as pcapremium')
		->where('epd.policy_detail_id = pcapremium.policy_detail_id')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->group_by("pcapremium.sum_insured")
		->get()
		->result_array();
		}
		}
		// print_r($data1);exit;
			
		if ($data[0]['combo_flag'] == "Y") {
		$y1 = implode(",", $policy_ids);


		for ($i = 0; $i < count($data1); $i++) {



		for ($j = 0; $j < count($data1[$i][$variable]); $j++) {
		
			
					
		$data1[$i][$variable][$j]['policy_sub_type_id'] = 1;
		$data1[$i][$variable][$j]['policy_detail_id'] = $y1;
		$data1[$i][$variable][$j]['combo_flag'] = "Y";
		
		
		}
		}
		}

		$key_defined = '';
		$arr_keys = [];
		$new_array = [];
		if(!empty($data1)){
		foreach($data1 as $key => $value){
		foreach($value as $key1 => $value1){
		$key_defined = $key1;
		array_push($arr_keys,$key1);
		foreach($value1 as $key2 => $value2){
		$new_array[] = $value2;
		}
		}

		}
		}
		usort($new_array, function($a, $b) {
		return $a['sum_insured'] - $b['sum_insured'];
		});
		$count_arr_keys = count(array_unique($arr_keys));
		if($count_arr_keys > 1)
		{
		$key_defined = "combo_diff_construct";
		}
		$return_array[0][$key_defined] = $new_array;
		// print_pre($return_array);exit;
		return $return_array;

     
    }

	public function group_customer_modify_issuance($lead_id){
        // echo 123;exit;
        //1-12-2021        
        $get_data =  $this->db->select("res,lead_id_grp,hr_amount_status,do_id,av_id,link_sent_by,address_api_res")
            ->from("group_mod_create")
            ->where("lead_id", $lead_id)
            ->get()
            ->row_array();

        $res = $get_data['res'];
        $hr_amount_status = $get_data['hr_amount_status'];
        $res = json_decode($res, TRUE);


        $product_array = $res['response']['policyData'];

     

        $policyData =  array();

        
        $collection_amount = 0;


        //1-12-2021
        $get_data_payment =  $this->db->select("res")
        ->from("group_mod_logs")
        ->where("lead_id", $lead_id)
        ->where("type", 'payment_response_post')
        ->order_by('id','DESC')
        ->get()
        ->row_array();
        $get_data_payment['res'] = json_decode($get_data_payment['res'], TRUE);
        $TxRefNo = $get_data_payment['res']['TxRefNo'];
        $txnDateTime = $get_data_payment['res']['txnDateTime'];
        $txnDateTime  = date('Y-m-d', strtotime($txnDateTime));


        //1-12-2021
        $get_data_do =  $this->db->select("do_id")
        ->from("tls_master_do")
        ->where("id", $get_data['do_id'])
        ->get()
        ->row_array();
        $do_id = $get_data_do['do_id'];


        //1-12-2021
        $get_data_av =  $this->db->select("agent_id")
        ->from("tls_agent_mst_outbound")
        ->where("id", $get_data['av_id'])
        ->get()
        ->row_array();

        $agent_id = "";

        if($get_data['link_sent_by'] == "AV"){
            $agent_id = $get_data_av['agent_id'];
        }

        // print_r($get_data);exit;
        $address_api_res = json_decode($get_data['address_api_res'], TRUE);
        // print_r($address_api_res);exit;
        $customer_code = $address_api_res['Response']['CustomerCode'];
        // echo $customer_code;exit;
       

        foreach($product_array as $key => $single_product){
           
            $member_details = array();


            foreach($single_product['Members'] as $member_key => $single_member_res){

                if($hr_amount_status == "yes"){
                    $member_details_single = array(
                        "member_code" => $single_product['Members'][$member_key]["Member_Code"],
                        "sum_insured" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["SumInsured"],
                        "health_return" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["Hr_Amount"],
                    );
                }
                else{
                    $member_details_single = array(
                        "member_code" => $single_product['Members'][$member_key]["Member_Code"],
                        "sum_insured" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["SumInsured"],
                        "health_return" => "0",
                       
                    );
                }
                
                array_push($member_details, $member_details_single);
            }
            

            //1-12-2021
            $policyData_single = array(
            "certificate_number" => $single_product["Certificate_number"],
            "master_policy_number" => $single_product["MaterPolicyNumber"],
            "go_green" => '1',
            "premium" => $single_product['premium']['Renewal_Gross_Premium'],
            "product_code" => $single_product['PolicyproductComponents'][0]['SchemeCode'],
            "ref_code1" => $do_id,
            "ref_code2" => '',
            "intermediary_code" => '2110705',
            "lead_Id" => $lead_id,
            "sp_Id" => $agent_id,
            "source_name" =>"AXIS_TELE",
            "member_details" => $member_details,
            );

          
            array_push($policyData, $policyData_single);
            $collection_amount += $single_product['premium']['Renewal_Gross_Premium'];

            $proposar_name = $single_product["Name_of_the_proposer"];
        }

        
        //1-12-2021
        $receiptobj[] =  array(
            "company_code"=> "",
            "system_code"=> "",
            "office_location"=> "Mumbai",
            "mode_of_entry"=> "DIRECT",
            "cd_ac_no"=> "",
            "expiry_date"=> "",
            "payer_type"=> "Customer",
            "payer_code"=> $customer_code,
            "payment_by"=> "Customer",
            "payment_by_name"=> $proposar_name,
            "payment_by_relationship"=> "Self",
            "collection_amount"=> "$collection_amount",
            "collection_rcvd_date"=> $txnDateTime,
            "collection_mode"=> "Debit/ Credit Card",
            "remarks"=> "",
            "instrument_number"=> $TxRefNo,
            "instrument_date"=> $txnDateTime,
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
            'type' => "full_quote_request",
        ];

        $this->db->insert('group_mod_logs', $fdata);


        $djson = json_decode($result, TRUE);
       
        $data_r['status'] = 'success';
        $data_r['lead_id'] = $lead_id;
        if($djson['error'][0]['ErrorCode'] != '00'){
            $data_r['status'] = 'failed';
        }

        if($djson['error'][0]['ErrorCode'] == '00'){
            $cert_arr = [];
            foreach($djson['response'] as $single_response){
            array_push($cert_arr, $single_response['new_certificate_number']);

					$fdata = [
						'lead_id' => $lead_id,
						'lead_id_grp' => $get_data['lead_id_grp'],
						'certificate_number' => $single_response['new_certificate_number']
					];
			
					$this->db->insert('group_mod_api_response', $fdata);
            }

            $certs = implode(',',$cert_arr);

			$update_policy = 
			$this->db->set('new_certificate_number', $certs);
			$this->db->set('issuance_datetime', date('Y-m-d H:i:s'));
            $this->db->set('disposition_master_id', '51');
            // 20-12-2021
            $this->db->set('new_renewal_api_req', json_encode($data));
            $this->db->set('new_renewal_api_res', $result);
            // 23-12-2021
            $this->db->set('updated_at', date("Y-m-d H:i:s"));

			$this->db->where('lead_id', $lead_id);
			$this->db->update('group_mod_create');

            $fdata = [
                'lead_id' => $lead_id,
                'disposition' => '51',
                'type' => "policy_issued",
                'agent_type' => '',
                'agent_id' => '',
            ];
            $this->db->insert('group_mod_logs', $fdata);
            $this->db->insert('group_mod_disposition', $fdata);


            return $certs;

           

       }else{
		   return 'false';
	   }

          
       

    }



    public function product($product_id){

        // SELECT pmws.product_code,epd.policy_detail_id,policy_no,sum_insured_type FROM product_master_with_subtype AS pmws JOIN employee_policy_detail AS epd ON pmws.id=epd.product_name WHERE pmws.product_code='T01';

        $product_details=$this->db->
            select('pmws.product_code,epd.policy_detail_id')
            ->from('product_master_with_subtype AS pmws')
            ->join('employee_policy_detail AS epd','pmws.id=epd.product_name')
            ->where('pmws.product_code',$product_id)
            ->get()
            ->result_array();

        return $product_details;        
    }

    public function policy_details($policy_id,$policy_details){

        // $policy_details=$this->db
        // ->select('policy_detail_id,policy_no,premium_type,suminsured_type,sum_insured_type')
        // ->from('employee_policy_detail')
        // ->where_in('policy_detail_id',$policy_id)
        // ->get()
        // ->result_array();
        // echo 'sfsdfsd';exit;
        if($policy_details=='yes'){
            $condition="AND policy_sub_type_id!=3";
        }

        
        $policy_details=$this->db->query("select policy_sub_type_id,policy_detail_id,policy_no,premium_type,suminsured_type,sum_insured_type from employee_policy_detail where policy_detail_id IN($policy_id) $condition ")->result_array();

        
        return $policy_details;
    }

    

    public function employeesalldob($lead_id){
        $dob=$this->db
                    ->select('DoB')
                    ->from('group_mod_members')
                    ->where('lead_id',$lead_id)
                    ->where_in('fr_id',[0,1])
                    ->group_by('fr_id')
                    ->order_by('fr_id','ASC')                    
                    ->get()
                    ->result_array();

        return $dob;
    }

    public function all_retail_data_group_phase2(){
        
        // print_r($this->input->post());exit;
        extract($this->input->post());
        $this->db->select('gmc.lead_id,gmc.do_location,gmc.coi_number,gmc.proposer_name,gmc.product_name,gmc.updated_at,gmc.address_api_res,gmc.res,gmc.premium,
                            DATEDIFF(NOW(),gmc.created_at) as days,gmc.new_certificate_number,gmc.hr_amount_status,gmc.issuance_datetime,
                            gmdm.disposition,gmdm.subdisposition,gmc.new_renewal_api_res,gmc.new_renewal_api_req,gmc.disposition_master_id,gmc.payment_status,gmc.wip,gmc.bitly_link,gmc.dob,gmc.mobile,gmc.claim_status,gmc.ped_status,gmc.link_sent_by,gmc.av_location,gmc.do_id,gmc.av_id,
                            tmd.do_id as do_id_display,tamo.agent_id as agent_id_display, gmc.old_premium,gmc.hr_amount_status,gmc.in_av_bucket,gmc.renewed_from_other_mode,gmc.link_for_customer');
        $this->db->from('group_mod_create as gmc');
        

        $this->db->join('group_mod_disposition_master gmdm', 'gmc.disposition_master_id = gmdm.id', 'left');
        $this->db->join('tls_master_do tmd', 'gmc.do_id = tmd.id', 'left');
        $this->db->join('tls_agent_mst_outbound tamo', 'gmc.av_id = tamo.id', 'left');
        // $this->db->join('group_mod_members gmm', 'gmc.lead_id = gmm.lead_id', 'left');
        // $this->db->join('group_mod_logs gml', 'gmc.lead_id = gml.lead_id', 'left');


        $this->db->where('gmc.status',1);
        // $this->db->where('gmc.lead_id','1630394388');
        

        if (!empty($policy)) {
            $this->db->like("gmc.coi_number", $policy);
        }

        if (!empty($epolicy)) {
            $this->db->like("gmc.new_certificate_number", $epolicy);
        }

        if (!empty($occcenter)) {
            $this->db->like("gmc.do_location", $occcenter);
        }

        if($_POST["length"] != -1)  
        {  
             $this->db->limit($_POST['length'], $_POST['start']);  
        }  

        // 1-12-2021
        if (!empty($current_status)) {
            if($current_status == '1'){
                $this->db->where("gmc.disposition_master_id NOT IN ('4','5','48','51','47','6') AND (gmc.link_for_customer != '1' OR gmc.link_for_customer IS NULL )");
                $this->db->where('gmc.renewed_from_other_mode',0);
            }else if($current_status == '5'){
                $this->db->where('gmc.disposition_master_id IN (5,6,48) AND gmc.link_for_customer = 1');
                $this->db->where('gmc.renewed_from_other_mode',0);
            }else if($current_status == '4'){
                $this->db->where('gmc.disposition_master_id IN (4)');
                $this->db->where('gmc.renewed_from_other_mode',0);
            }else if($current_status == '47'){
                $this->db->where('gmc.disposition_master_id IN (47)');
                $this->db->where('gmc.renewed_from_other_mode',0);
            }
            else if($current_status == '51'){
                $this->db->where('gmc.disposition_master_id IN (51)');
                $this->db->where('gmc.renewed_from_other_mode',0);
            }else if($current_status == 'renewed_from_other_mode'){
                $this->db->where('gmc.renewed_from_other_mode = "yes"');
            }
        }


        if (!empty($lmdf) && !empty($lmdt)) {
            // $this->db->where('last_updated_on >=', $lmdf);
            // $this->db->where('last_updated_on =', $lmdt);
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "gmc.updated_at BETWEEN '$lmdf' AND '$lmdt' ");
        }
        // $this->db->group_by('lead_id_grp');        

        $telSalesSession = $this->session->userdata('telesales_session');
        // print_pre($telSalesSession);exits;
        if($telSalesSession['is_admin'] != "1"){
            $do_id = $telSalesSession['agent_id'];
            $do_id = encrypt_decrypt_password($do_id, "D");
            if($_SESSION['telesales_session']['outbound'] == '2'){
                 $this->db->where('gmc.do_id',$do_id);
            }elseif($_SESSION['telesales_session']['outbound'] == '1'){
                $this->db->where('gmc.av_id', $do_id);
            }
        }
       
        $this->db->order_by('gmc.id','DESC');
        $this->db->group_by('gmc.lead_id');
        $all_data=$this->db->get()->result_array();
        // echo $this->db->last_query();exit;

        return $all_data;

    }

    

    public function all_retail_data_group_count(){
        
       
        extract($this->input->post());
        $this->db->select('gmc.lead_id,gmc.do_location,gmc.coi_number,gmc.proposer_name,gmc.product_name,gmc.updated_at,gmc.address_api_res,gmc.res,gmc.premium,
                            DATEDIFF(NOW(),gmc.created_at) as days,gmc.new_certificate_number,gmc.hr_amount_status,gmc.issuance_datetime,
                            gmdm.disposition,gmdm.subdisposition,gmc.new_renewal_api_res,gmc.new_renewal_api_req,gmc.disposition_master_id,gmc.payment_status,gmc.wip,gmc.bitly_link,gmc.dob,gmc.mobile,gmc.claim_status,gmc.ped_status,gmc.link_sent_by,gmc.av_location,gmc.do_id,gmc.av_id,
                            tmd.do_id as do_id_display,tamo.agent_id as agent_id_display, gmc.old_premium,gmc.hr_amount_status,gmc.in_av_bucket,gmc.renewed_from_other_mode');
        $this->db->from('group_mod_create as gmc');

        $this->db->join('group_mod_disposition_master gmdm', 'gmc.disposition_master_id = gmdm.id', 'left');
        $this->db->join('tls_master_do tmd', 'gmc.do_id = tmd.id', 'left');
        $this->db->join('tls_agent_mst_outbound tamo', 'gmc.av_id = tamo.id', 'left');
        // $this->db->join('group_mod_members gmm', 'gmc.lead_id = gmm.lead_id', 'left');
        // $this->db->join('group_mod_logs gml', 'gmc.lead_id = gml.lead_id', 'left');


        $this->db->where('gmc.status',1);
        // $this->db->where('gmc.lead_id','1630394388');
        

        if (!empty($policy)) {
            $this->db->like("gmc.coi_number", $policy);
        }

        if (!empty($epolicy)) {
            $this->db->like("gmc.new_certificate_number", $epolicy);
        }

        if (!empty($occcenter)) {
            $this->db->like("gmc.do_location", $occcenter);
        }

        if($_POST["length"] != -1)  
        {  
             $this->db->limit($_POST['length'], $_POST['start']);  
        }  

        // 1-12-2021
        if (!empty($current_status)) {
            if($current_status == '1'){
                $this->db->where("gmc.disposition_master_id NOT IN ('4','5','48','51','47','6') AND (gmc.link_for_customer != '1' OR gmc.link_for_customer IS NULL )");
                $this->db->where('gmc.renewed_from_other_mode IS NULL');
            }else if($current_status == '5'){
                $this->db->where('gmc.disposition_master_id IN (5,6,48) AND gmc.link_for_customer = 1');
                $this->db->where('gmc.renewed_from_other_mode IS NULL');
            }else if($current_status == '4'){
                $this->db->where('gmc.disposition_master_id IN (4)');
                $this->db->where('gmc.renewed_from_other_mode IS NULL');
            }else if($current_status == '47'){
                $this->db->where('gmc.disposition_master_id IN (47)');
                $this->db->where('gmc.renewed_from_other_mode IS NULL');
            }
            else if($current_status == '51'){
                $this->db->where('gmc.disposition_master_id IN (51)');
                $this->db->where('gmc.renewed_from_other_mode IS NULL');
            }else if($current_status == 'renewed_from_other_mode'){
                $this->db->where('gmc.renewed_from_other_mode = "yes"');
            }
        }


        if (!empty($lmdf) && !empty($lmdt)) {
            // $this->db->where('last_updated_on >=', $lmdf);
            // $this->db->where('last_updated_on =', $lmdt);
            $lmdt = date('Y-m-d', strtotime($lmdt . ' +1 day'));
            $this->db->where( "gmc.updated_at BETWEEN '$lmdf' AND '$lmdt' ");
        }
        // $this->db->group_by('lead_id_grp');        

        $telSalesSession = $this->session->userdata('telesales_session');
        // print_pre($telSalesSession);exits;
        if($telSalesSession['is_admin'] != "1"){
            $do_id = $telSalesSession['agent_id'];
            $do_id = encrypt_decrypt_password($do_id, "D");
            if($_SESSION['telesales_session']['outbound'] == '2'){
                 $this->db->where('gmc.do_id',$do_id);
            }elseif($_SESSION['telesales_session']['outbound'] == '1'){
                $this->db->where('gmc.av_id', $do_id);
            }
        }
       
        $this->db->order_by('gmc.id','DESC');
        $this->db->group_by('gmc.lead_id');
        $all_data=$this->db->get()->result_array();

        if($all_data){
            return count($all_data);
        }else{
            return 0;
        }
        
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


    public function get_total_attempts($lead_id, $type){

        // SELECT pmws.product_code,epd.policy_detail_id,policy_no,sum_insured_type FROM product_master_with_subtype AS pmws JOIN employee_policy_detail AS epd ON pmws.id=epd.product_name WHERE pmws.product_code='T01';

        $get_total_attempts=$this->db->
            select('DISTINCT(disposition)')
            ->from('group_mod_disposition')
            ->where('lead_id',$lead_id)
            ->get()
            ->result_array();

        if($type == "attempts"){
            $attempts = 0;

            foreach($get_total_attempts as  $single_attempt){
    
                $get_data =  $this->db->select("attempt")
                ->from("group_mod_disposition_master")
                ->where("id", $single_attempt['disposition'])
                ->get()
                ->row_array();
    
                $attempts += $get_data['attempt'];
                
            }
    
            return $attempts;       
        }

        if($type == "connects"){
            $connects = 0;

            foreach($get_total_attempts as  $single_attempt){
    
                $get_data =  $this->db->select("connect")
                ->from("group_mod_disposition_master")
                ->where("id", $single_attempt['disposition'])
                ->get()
                ->row_array();
    
                $connects += $get_data['connect'];
                
            }
    
            return $connects;       
        }

        
    }



    public function get_old_policy_premium($lead_id){

        $coi_number_arr = $policy_data['coi_number'];
        $coi_number_arr = explode(",",$coi_number_arr);
        $previous_premium = 0;
        foreach($coi_number_arr as $single_coi){
            $get_previous_data = $this->renewal_do_m->tele_renewal_group_do_address_api($single_coi);

            $get_previous_data = json_decode($get_previous_data, TRUE);
            // print_pre($get_previous_data);
            $previous_premium += $get_previous_data["Response"]["Premium"];
            
        }

        return $previous_premium;
    }



}