<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_integration_freedom_plus extends CI_Model
{
    function __construct()
    {
        parent::__construct();

        $this
            ->load
            ->model("Logs_m");
    }

    /*ACKNOWLEDEGEMENT LETTER FUNCTION
    created by - Siddhi*/

    function acknowledgement_download_m($lead_id = '')
    {

        if (empty($lead_id))
        {
            $lead_id = $this
                ->input
                ->post('lead_id');
        }

        $data = $this
            ->db
            ->query("select ed.lead_id,mps.ABHI_sub_type_name,apr.certificate_number,ed.emp_firstname,ed.emp_lastname,ed.AXISBANKACCOUNT,apr.start_date,apr.end_date,apr.ReceiptNumber,p.premium,pd.txndate,mpst.policy_subtype_id,mpst.product_code,p.multi_status,mpst.HB_policy_type from employee_policy_detail AS epd,product_master_with_subtype AS mpst,master_policy_sub_type mps,employee_details as ed,proposal as p,api_proposal_response as apr,payment_details as pd where epd.product_name = mpst.id and mpst.policy_subtype_id = epd.policy_sub_type_id and p.policy_detail_id=epd.policy_detail_id and mps.policy_sub_type_id = mpst.policy_subtype_id and ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and apr.certificate_number IS NOT NULL and ed.lead_id = '" . $lead_id . "' GROUP BY apr.certificate_number ORDER BY mpst.policy_subtype_id asc ")->result_array();

        //p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id --- replace join by  --- p.id = apr.proposal_id
        $coi_url = "https://bizpre.adityabirlahealth.com/ABHICL_TPD/Service1.svc/GetPDFURL"; // "http://esblive.adityabirlahealth.com/ABHICL_TPD/Service1.svc/GetPDFURL";
        $ClmAssistance = "";
        $Is_GCI = 0;
        $UWNotesAndImpTrmsConditns="";

        foreach ($data as $t => $v)
        {
            $product_data[$t]['certificate_number'] = $v['certificate_number'];
            $product_data[$t]['product_name'] = $v['ABHI_sub_type_name'];
            $product_data[$t]['premium'] = ($v['HB_policy_type'] == 'ProposalWise' || ($v['HB_policy_type'] == 'MemberWise' && $v['multi_status'] == '1')) ? $v['premium'] : ($v['premium'] / 2);
            $cust_name = $v['emp_firstname'] . ' ' . $v['emp_lastname'] . ',';
            $bank_name = ($v['AXISBANKACCOUNT'] == 'Y') ? 'Axis Bank Limited' : 'Other Bank';
            $start_date = $v['start_date'];
            $end_date = $v['end_date'];
            $product_id = $v['product_code'];
            $product_data[$t]['ReceiptNumber'] = $v['ReceiptNumber'];
            $no_of_product[] = $t;
            $payment_date = explode(" ", $v['txndate']);
            
            if ($v['product_code'] == 'R07')
            {
            $ClmAssistance = 'Group Health Insurance and Group Personal Accident';
             if ($v['policy_subtype_id'] == '3')
                {
                    $Is_GCI = 1;
                }
            }
        }
        
        if ($Is_GCI)
        {
            $ClmAssistance = 'Group Health Insurance , Group Personal Accident and Group Critical Insurance';
        }
        
         if ($product_id == 'R07' || $product_id == 'T01')
        { 
            $u1=isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';
            $u2=isset($product_data[1]['product_name']) ? $product_data[1]['product_name'] : '';
            $UWNotesAndImpTrmsConditns="Product Name: ".$u1.", Product UIN: ADIHLGP21134V022021 Product Name: ".$u2.", Product UIN: IRDAI/HLT/ABHI/P-H(G)/V.1/18/2016-17";
        }
        if($product_id == 'R11' ||  $product_id == 'T03' )
        {
          
            $u1=isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';
            $u2=isset($product_data[1]['product_name']) ? $product_data[1]['product_name'] : '';
            $UWNotesAndImpTrmsConditns="Product Name: ".$u1.", Product UIN: ADIHLGP21134V022021  Product Name: ".$u2.", Product UIN: IRDAI/HLT/ABHI/P-H(G)/V.1/18/2016-17";
                
               if ($v['policy_subtype_id'] == '1') {
                $u3=isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';
                   $UWNotesAndImpTrmsConditns="Product Name: ".$u3.", Product UIN: ADIHLGP21134V022021";
       
                }
        }
        if ($product_id == 'R03')
        {
            $t1 = isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';
            $t2 = isset($product_data[1]['product_name']) ? $product_data[1]['product_name'] : '';
            $UWNotesAndImpTrmsConditns= "Product Name: ".$t1.", Product UIN: ADIHLGP21134V022021 Product Name: ".$t2.", Product UIN: ADIHLGP21056V022021";
            
        }
     
        if($product_id == 'R06')
        {
            $UWNotesAndImpTrmsConditns="Product Name: ".isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : ''.", Product UIN: ADIHLGP21134V022021";
        }
        //var_dump($UWNotesAndImpTrmsConditns);exit();
        $start_date = explode(" ", $start_date);
        $start_date = date("d/m/Y", strtotime($start_date[0]));

        $end_date = explode(" ", $end_date);
        $end_date = date("d/m/Y", strtotime($end_date[0]));

        $payment_date = date("d/m/Y", strtotime($payment_date[0]));

        $coi_request = ["ClientCode" => "AXIS_ACK_LTR", "ProdCode" => "4211", "NoOfMembers" => count(array_count_values($no_of_product)) , "MPN" => isset($product_data[2]['product_name']) ? $product_data[2]['product_name'] : "", "MPHN" => isset($product_data[4]['product_name']) ? $product_data[4]['product_name'] : "", "MPStartDt" => isset($product_data[3]['product_name']) ? $product_data[3]['product_name'] : "", "MPEndDt" => "", "PHMobNo" => isset($product_data[4]['product_name']) ? $lead_id : "", "PHEmail" => isset($product_data[5]['product_name']) ? $lead_id : "", "PHContactDtls" => "", "PrmPaymntMode" => "", "InstNum" => isset($product_data[5]['product_name']) ? $product_data[5]['product_name'] : "", "InstDt" => $payment_date, "BankName" => $bank_name, "PIO" => isset($product_data[2]['product_name']) ? $lead_id : "", "PSO" => isset($product_data[3]['product_name']) ? $lead_id : "", "PHN" => "", "PN" => "", "PHAdd" => "", "PPN" => isset($product_data[1]['product_name']) ? $product_data[1]['product_name'] : "", "ProdName" => isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : "", "PlanName" => "", "PStartDt" => $start_date, "PEndDt" => $end_date, "IMDCode" => isset($product_data[0]['ReceiptNumber']) ? $product_data[0]['ReceiptNumber'] : "", "IMDName" => isset($product_data[2]['premium']) ? $product_data[2]['premium'] : "", "IMDEmail" => "", "IMDCDtls" => "", "CN" => isset($product_data[0]['certificate_number']) ? $product_data[0]['certificate_number'] : "", "PHNameAndAdd" => "", "PHCommAdd" => "", "UID" => isset($product_data[1]['certificate_number']) ? $product_data[1]['certificate_number'] : "", "CvrType" => isset($product_data[1]['ReceiptNumber']) ? $product_data[1]['ReceiptNumber'] : "", "MID1" => "", "IPName1" => $cust_name, "IPDOB1" => isset($product_data[2]['certificate_number']) ? $product_data[2]['certificate_number'] : "", "IPGender1" => "", "IPNomName1" => "", "IPNomRel1" => "", "SumInsured" => "", "IPRel1" => "", "MID2" => "", "IPName2" => isset($product_data[3]['premium']) ? $product_data[3]['premium'] : "", "IPDOB2" => isset($product_data[3]['certificate_number']) ? $product_data[3]['certificate_number'] : "", "IPGender2" => "", "IPNomName2" => "", "IPNomRel2" => isset($product_data[3]['ReceiptNumber']) ? $product_data[3]['ReceiptNumber'] : "", "MID3" => "", "IPName3" => isset($product_data[4]['premium']) ? $product_data[4]['premium'] : "", "IPDOB3" => isset($product_data[4]['certificate_number']) ? $product_data[4]['certificate_number'] : "", "IPGender3" => "", "IPNomName3" => "", "IPNomRel3" => isset($product_data[4]['ReceiptNumber']) ? $product_data[4]['ReceiptNumber'] : "", "MID4" => "", "IPName4" => isset($product_data[5]['certificate_number']) ? $product_data[5]['certificate_number'] : "", "IPDOB4" => "", "IPGender4" => "", "IPNomName4" => "", "IPNomRel4" => isset($product_data[5]['ReceiptNumber']) ? $product_data[5]['ReceiptNumber'] : "", "InitCNAndStartDate" => "", "PayoutBasis" => "", "Options" => isset($product_data[2]['ReceiptNumber']) ? $product_data[2]['ReceiptNumber'] : "", "ApplicabilityOpt1" => "", "SumInsrdLmtOpt1" => "", "PEDDtls1" => isset($product_data[0]['product_name']) ? $lead_id : "", "PEDDtls2" => isset($product_data[1]['product_name']) ? $lead_id : "", "NP" => "", "CGST" => "", "SGST" => "", "IGST" => "", "GP" => isset($product_data[0]['premium']) ? $product_data[0]['premium'] : "", "ClmAssistance" => $ClmAssistance, "UWNotesAndImpTrmsConditns" => $UWNotesAndImpTrmsConditns, "COINo1" => "", "CvrgType1" => "", "PrmAmt1" => isset($product_data[1]['premium']) ? $product_data[1]['premium'] : "", "PaymntDt1" => "", "FY1" => "", "YrWiseProportionatePrmAmt1" => "", "StampDutyAmt" => "", "StampDuty" => "", "PolIssDt" => "", "Place" => "", "SumInsured1" => "", "SumInsured2" => "", "SumInsured3" => "", "SumInsured4" => ""];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $coi_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($coi_request) ,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: " . strlen(json_encode($coi_request)) ,
                "Content-Type: application/json",
                "Host: bizpre.adityabirlahealth.com"
            ) ,
        ));

        $response = curl_exec($curl);

        $request_arr = ["lead_id" => $lead_id, "req" => json_encode($coi_request) , "res" => json_encode($response) , "type" => "coi_genarate_new_method", "product_id" => $product_id];

        $dataArray['tablename'] = 'logs_docs';
        $dataArray['data'] = $request_arr;
        $this
            ->Logs_m
            ->insertLogs($dataArray);

        if ($err)
        {
            return array(
                "status" => "error",
                "url" => $err
            );
        }
        else
        {

            $res_url = json_decode($response, true);
            $coi_url = $res_url['COIUrl'];

            $query_one = $this
                ->db
                ->query("select * from coi_genarate_url where lead_id='" . $lead_id . "'")->row_array();

            if ($query_one > 0)
            {
                $arr = ["url" => $coi_url];
                $update_where = ["lead_id" => $lead_id];
                $this
                    ->db
                    ->where($update_where);
                $this
                    ->db
                    ->update("coi_genarate_url", $arr);
            }
            else
            {
                $arr = ["lead_id" => $lead_id, "url" => $coi_url];
                $this
                    ->db
                    ->insert("coi_genarate_url", $arr);
            }

            return array(
                "status" => "success",
                "url" => $coi_url
            );
        }

    }

    function get_all_quote_call($emp_id)
    {
        $get_data = $this
            ->db
            ->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,p.id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' . $emp_id . '" ')->result_array();

        foreach ($get_data as $vall)
        {

            if ($vall['HB_policy_type'] == 'ProposalWise')
            {

                $query_check_ghi = $this
                    ->db
                    ->query("select id from ghi_quick_quote_response where policy_subtype_id = '" . $vall['policy_subtype_id'] . "' and emp_id='$emp_id' and proposal_id = '" . $vall['id'] . "' and status = 'success'")->row_array();

                if ($query_check_ghi)
                {
                    $last_array[] = 'Success';
                }
                else
                {
                    $GHI_quote = $this->get_quote_data($emp_id, $vall['policy_detail_id']);
                    $last_array[] = $GHI_quote['status'];
                }

            }
            else
            {

                $member_data = (array)$this->get_all_member_data($emp_id, $vall['policy_detail_id']);

                $query_start = $this
                    ->db
                    ->query("select count(id) as total_success from ghi_quick_quote_response where policy_subtype_id = '" . $vall['policy_subtype_id'] . "' and emp_id='$emp_id' and proposal_id = '" . $vall['id'] . "' and status = 'success'")->row_array();

                if ($member_data[0]['familyConstruct'] == '1A')
                {
                    $check_status = ($query_start['total_success'] == '1') ? 'Success' : 'error';
                }

                if ($member_data[0]['familyConstruct'] == '2A')
                {
                    $check_status = ($query_start['total_success'] == '2') ? 'Success' : 'error';
                }

                if ($check_status == 'error')
                {
                    foreach ($member_data as $key => $value)
                    {
                        /* prod uncomment */
                        // if($vall['policy_detail_id'] == 456 && empty($value['firstname'])){
                        // $value = $this->update_gci($value);
                        // }
                        $value['key_id'] = $key + 1;
                        $mem_data[0] = $value;

                        $is_data = $this
                            ->db
                            ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $value['policy_subtype_id'] . "' and proposal_id = '" . $vall['id'] . "' and fr_id = '" . $value['fr_id'] . "' ")->row_array();

                        if ($is_data)
                        {

                            $query_check = $this
                                ->db
                                ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $value['policy_subtype_id'] . "' and proposal_id = '" . $vall['id'] . "' and fr_id = '" . $value['fr_id'] . "' and status = 'error'")->row_array();

                            if ($query_check)
                            {
                                $policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
                            }

                        }
                        else
                        {

                            $arr = ["emp_id" => $emp_id, "policy_subtype_id" => $value['policy_subtype_id'], "fr_id" => $value['fr_id'], "status" => "error","proposal_id" => $vall['id']];
                            $this
                                ->db
                                ->insert("ghi_quick_quote_response", $arr);

                            $policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
                        }
                    }
                }

                $query_last = $this
                    ->db
                    ->query("select count(id) as total_success from ghi_quick_quote_response where policy_subtype_id = '" . $vall['policy_subtype_id'] . "' and emp_id='$emp_id' and proposal_id = '" . $vall['id'] . "' and status = 'success'")->row_array();

                if ($member_data[0]['familyConstruct'] == '1A')
                {
                    $check_status = ($query_last['total_success'] == '1') ? 'Success' : 'error';
                    $last_array[] = $check_status;
                }

                if ($member_data[0]['familyConstruct'] == '2A')
                {
                    $check_status = ($query_last['total_success'] == '2') ? 'Success' : 'error';
                    $last_array[] = $check_status;
                }

            }

        }

        if (in_array("error", $last_array))
        {
            $proposal_status = 'error';
        }
        else
        {
            $proposal_status = 'Success';
        }

        return $return_data = array(
            'status' => $proposal_status,
            "msg" => "testing"
        );
    }

    function Memberwise_policy_call($emp_id, $policy_detail_id, $field_array = '', $cron_policy_check = '')
    {
        $member_data = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
        //print_r($member_data);
        $query = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        $update_arr = ["emp_id" => $emp_id, "policy_detail_id" => $policy_detail_id];

        if (empty($query['multi_status']))
        {

            if ($member_data[0]['familyConstruct'] == '1A')
            {
                $arr = ["multi_status" => '0'];
            }
            else
            {
                $arr = ["multi_status" => '0,0'];
            }

            $this
                ->db
                ->where($update_arr);
            $this
                ->db
                ->update("proposal", $arr);
        }

        $query_again = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        $status_arr = explode(",", $query_again['multi_status'], 2);

        foreach ($member_data as $key => $value)
        {
            /*uncomment if GCI member name is blank*/
            // if($policy_detail_id == 456 && empty($value['firstname'])){
            // $value = $this->update_gci($value);
            // }
            $value['key_id'] = $key + 1;
            $mem_data[0] = $value;
            //print_r($value);
            if ($status_arr[$key] == 0)
            {
                $policy_data = $this->GHI_GCI_api_call($emp_id, $policy_detail_id, $mem_data, $field_array, $cron_policy_check);

                if ($policy_data['status'] == 'Success')
                {
                    $status_arr[$key] = 1;

                    $status_string = implode(",", $status_arr);

                    $arr = ["multi_status" => $status_string];

                    $this
                        ->db
                        ->where($update_arr);
                    $this
                        ->db
                        ->update("proposal", $arr);

                }

            }

        }

        $query_last = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        if ($member_data[0]['familyConstruct'] == '1A')
        {
            $proposal_status = ($query_last['multi_status'] == '1') ? 'Success' : 'error';
        }

        if ($member_data[0]['familyConstruct'] == '2A')
        {
            $proposal_status = ($query_last['multi_status'] == '1,1') ? 'Success' : 'error';
        }

        return $return_data = array(
            'status' => $proposal_status,
            "msg" => "GPA GCI GP call"
        );

    }

    function update_gci($member)
    {
        $data = $this
            ->db
            ->select('policy_member_first_name,policy_member_last_name')
            ->from('employee_policy_member')
            ->where('policy_detail_id', 454)
            ->where('family_relation_id', $member['family_relation_id'])->get()
            ->row_array();
        $update_array = ['policy_member_first_name' => $data['policy_member_first_name'], 'policy_member_last_name' => $data['policy_member_last_name']];

        $this
            ->db
            ->where('family_relation_id', $member['family_relation_id']);
        $this
            ->db
            ->where('policy_detail_id', 456);
        $this
            ->db
            ->update('employee_policy_member', $update_array);

        $this
            ->db
            ->where('family_relation_id', $member['family_relation_id']);
        $this
            ->db
            ->where('policy_detail_id', 456);
        $this
            ->db
            ->update('proposal_member', $update_array);

        $member['firstname'] = $data['policy_member_first_name'];
        $member['lastname'] = $data['policy_member_last_name'];
        return $member;

    }

    function get_quote_data($emp_id, $policy_detail_id, $mem_data = '')
    {

        $data['customer_data'] = (array)$this->get_profile($emp_id);
        $data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
        $data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
        $data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id);
        
        /*changes done by ankita - 19 march 2021*/
        $AutoRenewalFlag = '0';
        if($data['proposal_data']['si_auto_renewal']){
            if($data['proposal_data']['si_auto_renewal'] == 'Y'){
                $AutoRenewalFlag = '1';
            }else{
                $AutoRenewalFlag = '0';
            }
        }
        /*changes ends*/
        $url = trim($data['proposal_data']['api_url']);

        if ($url == '')
        {  
            return array(
                "status" => "error",
                "msg" => "Something Went Wrong"
            );
        }
		
		if ($data['proposal_data']['product_code'] == 'R03' && date('Y-m-d', strtotime($data['proposal_data']['created_date'])) < '2020-12-17')
        {
            return array(
                "status" => "error",
                "msg" => "R03 Master policy expired"
            );
        }
		
		$source_name = $data['proposal_data']['HB_source_code'];
		// $cust_id = $data['customer_data']['cust_id'];
		$unique_ref_no = $data['customer_data']['unique_ref_no'];
		$concat_string = $data['proposal_data']['HB_custid_concat_string'];
		if(!empty($concat_string)){
			// $cust_id = $data['customer_data']['cust_id'].$concat_string;
			$unique_ref_no = $data['customer_data']['unique_ref_no'].$concat_string;
		}

		$AnnualIncome=null;
		if($data['customer_data']['annual_income']>0)
		{
			$AnnualIncome=$data['customer_data']['annual_income'];
		}
		
		// $new_check_data = $this
		// ->db
		// ->query("select ed.lead_id from proposal as p,employee_details as ed  where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and p.policy_detail_id = '454' and p.created_date <= '2020-09-19' and ed.cust_id ='" . $data['customer_data']['cust_id'] . "'")->row_array();

        $new_check_data = $this
		->db
		->query("select ed.lead_id from proposal as p,employee_details as ed  where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and p.policy_detail_id = '454' and p.created_date <= '2020-09-19' and ed.unique_ref_no ='" . $data['customer_data']['unique_ref_no'] . "'")->row_array();
	
		if ($data['proposal_data']['product_code'] == 'R03' && !empty($new_check_data['lead_id']) && $data['proposal_data']['policy_sub_type_id'] == 1)
		{
			// $cust_id = $data['customer_data']['cust_id'].'GHIR03';
			$unique_ref_no = $data['customer_data']['unique_ref_no'].'GHIR03';
		}

        if ($data['proposal_data']['product_code'] == 'R07' && $data['proposal_data']['policy_sub_type_id'] == 1 && date('Y-m-d', strtotime($data['proposal_data']['created_date'])) < '2020-09-19')
        {
            // $cust_id = $data['customer_data']['cust_id'];
            $unique_ref_no = $data['customer_data']['unique_ref_no'];
        }
		
		if ($data['proposal_data']['HB_policy_type'] == 'MemberWise')
        {
            $data['member_data'] = $mem_data;
            // $cust_id = $data['customer_data']['cust_id'] . $concat_string . $data['member_data'][0]['key_id'];
            $unique_ref_no = $data['customer_data']['unique_ref_no'] . $concat_string . $data['member_data'][0]['key_id'];

        }

		if ($data['proposal_data']['product_code'] == 'R11' && $data['proposal_data']['policy_sub_type_id'] == 1)
		{
			if(!empty($data['customer_data']['deductable'])){

				$data['proposal_data']['sum_insured'] = ($data['proposal_data']['sum_insured'] - $data['customer_data']['deductable']);
				
				$GHI_supertopup_data = $this
					->db
					->query("select * from master_group_code where si_group = '" . $data['proposal_data']['sum_insured'] . "' and family_construct = '" . $data['proposal_data']['familyConstruct'] . "' and product_code = '".$data['proposal_data']['product_code']."'")->row_array();
				
				$data['proposal_data']['group_code'] = $GHI_supertopup_data['group_code'];
				$data['proposal_data']['spouse_group_code'] = $GHI_supertopup_data['spouse_group_code'];
				
			}
		}

        $EW_check = $this
            ->db
            ->query('SELECT * from master_imd where BranchCode = "' . $data['proposal_data']['branch_code'] . '" and product_code = "' . $data['proposal_data']['imd_refer_product_code'] . '"')->row_array();

        if ($EW_check['EW_status'])
        {
            $source_name = $data['proposal_data']['SourceSystemName_api'];
            $data['proposal_data']['master_policy_no'] = $data['proposal_data']['EW_master_policy_no'];
            $data['proposal_data']['group_code'] = $data['proposal_data']['EW_group_code'];
        }

        $totalMembers = count($data['member_data']);
        $member = [];
		
		$occupation = "O553";
		if ($data['proposal_data']['product_code'] == 'R07' || $data['proposal_data']['product_code'] == 'R11'){
		//if ($data['proposal_data']['product_code'] == 'R07'){
			$occupation_check = $this->db->query('SELECT occupation_id from master_occupation where id = "' . $data['customer_data']['occupation'] . '" ')->row_array();
			if (isset($occupation_check['occupation_id']))
			{
				$occupation = $occupation_check['occupation_id'];
			}
		}
		
        for ($i = 0;$i < $totalMembers;$i++)
        {
            if (($data['proposal_data']['familyConstruct'] == '1A+1K' || $data['proposal_data']['familyConstruct'] == '1A+2K') && $data['member_data'][$i]['fr_id'] == 1)
            {
                $data['proposal_data']['group_code'] = $data['proposal_data']['spouse_group_code'];
            }

            $query = $this
                ->db
                ->query('SELECT pds.sub_member_code from employee_declare_member_sub_type as edmsp JOIN policy_declaration_subtype as pds ON edmsp.declare_sub_type_id = pds.declare_subtype_id where edmsp.emp_id="' . $emp_id . '" AND edmsp.policy_member_id = "' . $data['member_data'][$i]['policy_member_id'] . '" ')->result_array();

            $abc = [];
            if (!empty($query))
            {
                foreach ($query as $key => $value)
                {

                    $abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];

                }
            }
            else
            {

                $abc[] = ["PEDCode" => null, "Remarks" => null];
            }

            $member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['firstname'], "Middle_Name" => null, "Last_Name" => !empty(trim($data['member_data'][$i]['lastname'])) ? $data['member_data'][$i]['lastname'] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']) , "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => $occupation, "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['member_data'][$i]['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data']['nominee_fname'], "Nominee_Last_Name" => !empty(trim($data['nominee_data']['nominee_lname'])) ? $data['nominee_data']['nominee_lname'] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']) , ];

        }

// 	if($cust_id == '853087692GHIR11'){
// 	$cust_id = '853087692GHIR11_1';
// }else if($cust_id == '853087692GPAR111'){
// 	$cust_id = '853087692GPAR111_1';
// }

if($unique_ref_no == '853087692GHIR11'){
	$unique_ref_no = '853087692GHIR11_1';
}else if($unique_ref_no == '853087692GPAR111'){
	$unique_ref_no = '853087692GPAR111_1';
}



                    //replaced cust_id to unique_ref_no on 28-07-2021 by upendra
                    //updated by upendra on 28-07-2021 - dedupe logic updated
                    $unique_ref_no_rows_check = $this
                    ->db
                    ->query("select ed.lead_id from employee_details as ed,proposal as p 
                    where ed.emp_id = p.emp_id and p.status in('Success','Payment Received','Cancelled','Rejected') 
                    and ed.unique_ref_no = '" . $data['customer_data']['unique_ref_no'] . "' 
                    and ed.product_id = '".$data['proposal_data']['product_code']."' 
                    and ed.lead_id != '".$data['customer_data']['lead_id']."'
                    group by p.emp_id")->num_rows();

                    if($unique_ref_no_rows_check > 0){
                        $unique_ref_no_rows_check = $unique_ref_no_rows_check + 1;
                        $unique_ref_no = $unique_ref_no."_".$unique_ref_no_rows_check;
                    }

        //print_pre($member);exit;
        $fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $unique_ref_no, "salutation" => $data['customer_data']['salutation'], "firstName" => $data['customer_data']['emp_firstname'], "middleName" => "", "lastName" => !empty(trim($data['customer_data']['emp_lastname'])) ? $data['customer_data']['emp_lastname'] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']) , -10) , "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => $AnnualIncome, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => $AutoRenewalFlag , "AutoDebit" => $AutoRenewalFlag , "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_id'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];

        Monolog::saveLog("full_quote_request1", "I", json_encode($fqrequest));

        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "product_id" => $data['proposal_data']['product_code'], "type" => "full_quote_request1_" . $data['proposal_data']['policy_sub_type_id']];
        $this
            ->db
            ->insert("logs_docs", $request_arr);
        $insert_id = $this
            ->db
            ->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: " . strlen(json_encode($fqrequest)) ,
                "Content-Type: application/json",
                "Host: bizpre.adityabirlahealth.com"
            ) ,
        ));

        $response = curl_exec($curl);

        Monolog::saveLog("full_quote_reponse1", "I", json_encode($response));

        $request_arr = ["res" => json_encode($response) ];
        $this
            ->db
            ->where("id", $insert_id);
        $this
            ->db
            ->update("logs_docs", $request_arr);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
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

            if ($errorObj['ErrorNumber'] == '00')
            {

                $policydetail = $newArr['policyDtls'];

                if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                {
                    $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'")->row_array();

                    if ($query_one > 0)
                    {

                        $arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'], "status" => "success"];

                        $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->where($update_where);
                        $this
                            ->db
                            ->update("ghi_quick_quote_response", $arr);
                    }
                    else
                    {

                        $arr = ["emp_id" => $emp_id, "status" => "success", "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "QuotationNumber" => $policydetail['QuotationNumber'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->insert("ghi_quick_quote_response", $arr);
                    }

                }
                else
                {

                    $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and fr_id = '" . $data['member_data'][0]['fr_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'")->row_array();

                    if ($query_one > 0)
                    {

                        $arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'], "status" => "success"];

                        $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->where($update_where);
                        $this
                            ->db
                            ->update("ghi_quick_quote_response", $arr);
                    }
                    else
                    {

                        $arr = ["emp_id" => $emp_id, "status" => "success", "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'], "QuotationNumber" => $policydetail['QuotationNumber'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->insert("ghi_quick_quote_response", $arr);
                    }

                }

                return array(
                    "status" => "Success",
                    "msg" => $policydetail['QuotationNumber']
                );
            }
            else
            {

                if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                {
                    $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'")->row_array();

                    if ($query_one > 0)
                    {

                        $arr = ["count" => $query_one['count'] + 1, "status" => "error"];
                        $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->where($update_where);
                        $this
                            ->db
                            ->update("ghi_quick_quote_response", $arr);
                    }
                    else
                    {

                        $arr = ["emp_id" => $emp_id, "status" => "error", "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->insert("ghi_quick_quote_response", $arr);
                    }

                }
                else
                {

                    $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and fr_id = '" . $data['member_data'][0]['fr_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'")->row_array();

                    if ($query_one > 0)
                    {

                        $arr = ["count" => $query_one['count'] + 1, "status" => "error"];
                        $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->where($update_where);
                        $this
                            ->db
                            ->update("ghi_quick_quote_response", $arr);
                    }
                    else
                    {

                        $arr = ["emp_id" => $emp_id, "status" => "error", "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->insert("ghi_quick_quote_response", $arr);
                    }

                }

                ///------- @author : Guru --------------------------//
                $request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], /*"req" => json_encode($fqrequest) ,*/
                "product_id" => $data['proposal_data']['product_code'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_" . $data['proposal_data']['policy_sub_type_id']];
                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_failure_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                return array(
                    "status" => "error",
                    "msg" => $errorObj['ErrorMessage']
                );
            }

        }

    }

    function GHI_GCI_api_call($emp_id, $policy_no, $mem_data = '', $field_array = '', $cron_policy_check = '')
    {
        /*check for payment done or not and prevent multiple policy create at same time*/
       $extra_check_data = $this
            ->db
            ->query("select ed.lead_id,ed.product_id,pd.payment_status,pd.EasyPayId,pd.transaction_no,pd.TxRefNo,pd.TxStatus,ed.is_policy_issue_initiated from proposal as p,employee_details as ed,payment_details as pd  where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.emp_id ='" . $emp_id . "'")->row_array();

	$request_arr1 = ["lead_id" => $extra_check_data['lead_id'], "req" => json_encode($field_array), "product_id" => $extra_check_data['product_id'], "type" => "filed_array_data"];
$dataArray3['tablename'] = 'logs_docs';
$dataArray3['data'] = $request_arr1;
$this->Logs_m->insertLogs($dataArray3);        

    
        /*if (($extra_check_data['payment_status'] == 'Success' && !empty($extra_check_data['EasyPayId'])) || ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success'))
        {*/
//print_r($extra_check_data);exit;
        if ((($extra_check_data['payment_status'] == 'Success' && !empty($field_array['EasyPayId'])) || ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success')) && $extra_check_data['is_policy_issue_initiated'] == 0)
        {
//echo "in";exit;
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

            $url = trim($data['proposal_data']['api_url']);

            /*changes done by ankita - 19 march 2021*/
        $AutoRenewalFlag = '0';
        if($data['proposal_data']['si_auto_renewal']){
            if($data['proposal_data']['si_auto_renewal'] == 'Y'){
                $AutoRenewalFlag = '1';
            }else{
                $AutoRenewalFlag = '0';
            }
        }
        /*changes ends*/

            if ($url == '')
            {
                return array(
                    "status" => "error",
                    "msg" => "Something Went Wrong"
                );
            }
			
			if ($data['proposal_data']['product_code'] == 'R03' && date('Y-m-d', strtotime($data['proposal_data']['created_date'])) < '2020-12-17')
			{
				return array(
					"status" => "error",
					"msg" => "R03 Master policy expired"
				);
			}

            $transaction_date = explode(" ", $data['proposal_data']['txndate']);
            $trans_date = date("Y-m-d", strtotime($transaction_date[0]));
            $collectionMode = "online";
            $micrNo = null;
            $terminal_id = "76006448";
            $mem_fr_id = '';
            $collection_amt['pay_amt'] = $data['proposal_data']['premium'];
            $query_quote = [];
			$source_name = $data['proposal_data']['HB_source_code'];
			// $cust_id = $data['customer_data']['cust_id'];
			$unique_ref_no = $data['customer_data']['unique_ref_no'];
			
			$concat_string = $data['proposal_data']['HB_custid_concat_string'];
			if(!empty($concat_string)){
				// $cust_id = $data['customer_data']['cust_id'].$concat_string;
				$unique_ref_no = $data['customer_data']['unique_ref_no'].$concat_string;
			}
			
			if ($data['proposal_data']['product_code'] == 'R07')
			{
				$terminal_id = "76012611";
			}
		
			$AnnualIncome=null;
			if($data['customer_data']['annual_income']>0)
			{
				$AnnualIncome=$data['customer_data']['annual_income'];
			}
		
            if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
            {
				// $new_check_data = $this
				// ->db
				// ->query("select ed.lead_id from proposal as p,employee_details as ed  where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and p.policy_detail_id = '454' and p.created_date <= '2020-09-19' and ed.cust_id ='" . $data['customer_data']['cust_id'] . "'")->row_array();

                $new_check_data = $this
				->db
				->query("select ed.lead_id from proposal as p,employee_details as ed  where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and p.policy_detail_id = '454' and p.created_date <= '2020-09-19' and ed.unique_ref_no ='" . $data['customer_data']['unique_ref_no'] . "'")->row_array();
			
				if ($data['proposal_data']['product_code'] == 'R03' && !empty($new_check_data['lead_id']) && $data['proposal_data']['policy_sub_type_id'] == 1)
				{
					// $cust_id = $data['customer_data']['cust_id'].'GHIR03';
					$unique_ref_no = $data['customer_data']['unique_ref_no'].'GHIR03';
				}
				
				if ($data['proposal_data']['product_code'] == 'R07' && date('Y-m-d', strtotime($data['proposal_data']['created_date'])) < '2020-09-19')
				{
					// $cust_id = $data['customer_data']['cust_id'];
					$unique_ref_no = $data['customer_data']['unique_ref_no'];
				}
			/*R11 deductable*/
				if ($data['proposal_data']['product_code'] == 'R11' && $data['proposal_data']['policy_sub_type_id'] == 1)
				{
						if(!empty($data['customer_data']['deductable'])){

							$data['proposal_data']['sum_insured'] = ($data['proposal_data']['sum_insured'] - $data['customer_data']['deductable']);
							
							$GHI_supertopup_data = $this
								->db
								->query("select * from master_group_code where si_group = '" . $data['proposal_data']['sum_insured'] . "' and family_construct = '" . $data['proposal_data']['familyConstruct'] . "' and product_code = '".$data['proposal_data']['product_code']."'")->row_array();
							
							$data['proposal_data']['group_code'] = $GHI_supertopup_data['group_code'];
							$data['proposal_data']['spouse_group_code'] = $GHI_supertopup_data['spouse_group_code'];
						}
				}
				
                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'  and status = 'success'")->row_array();

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                if ($field_array && $field_array['LeadId'])
                {
                    $collectionMode = ($data['proposal_data']['payment_type'] == 'Clearing') ? "Cheque" : "RTGS/ NEFT";
                    $data['proposal_data']['TxRefNo'] = empty($field_array['EasyPayId']) ? null : $field_array['EasyPayId'];
                    $trans_date = $field_array['RequestDateTime'];
                    $data['proposal_data']['bank_name'] = empty($field_array['BankName']) ? "Axis Bank Limited" : ucwords($field_array['BankName']);
                    $data['proposal_data']['branch'] = empty($field_array['BranchName']) ? null : $field_array['BranchName'];
                    $micrNo = $field_array['MICRNumber'];
                    $data['proposal_data']['ifscCode'] = empty($field_array['IFSCCode']) ? null : $field_array['IFSCCode'];
                    $terminal_id = "";
                    if ($data['proposal_data']['policy_sub_type_id'] == 1)
                    {
                        $terminal_id = "76010098";
                    }

                }

            }
            else
            {

                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $mem_data[0]['policy_subtype_id'] . "' and fr_id = '" . $mem_data[0]['fr_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "' and status = 'success'")->row_array();

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                $data['member_data'] = $mem_data;
                $collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];
                $mem_fr_id = $data['member_data'][0]['fr_id'];

                // $cust_id = $data['customer_data']['cust_id'] . $concat_string . $data['member_data'][0]['key_id'];
                $unique_ref_no = $data['customer_data']['unique_ref_no'] . $concat_string . $data['member_data'][0]['key_id'];

                if ($field_array && $field_array['LeadId'])
                {
                    $collectionMode = ($data['proposal_data']['payment_type'] == 'Clearing') ? "Cheque" : "RTGS/ NEFT";
                    $data['proposal_data']['TxRefNo'] = empty($field_array['EasyPayId']) ? null : $field_array['EasyPayId'];
                    $trans_date = $field_array['RequestDateTime'];
                    $data['proposal_data']['bank_name'] = empty($field_array['BankName']) ? "Axis Bank Limited" : ucwords($field_array['BankName']);
                    $data['proposal_data']['branch'] = empty($field_array['BranchName']) ? null : $field_array['BranchName'];
                    $micrNo = $field_array['MICRNumber'];
                    $data['proposal_data']['ifscCode'] = empty($field_array['IFSCCode']) ? null : $field_array['IFSCCode'];
                    $terminal_id = "";
                }

            }

            if ($query_quote['QuotationNumber'])
            {

                $EW_check = $this
                    ->db
                    ->query('SELECT * from master_imd where BranchCode = "' . $data['proposal_data']['branch_code'] . '" and product_code = "' . $data['proposal_data']['imd_refer_product_code'] . '"')->row_array();

                if ($EW_check['EW_status'])
                {
                    $source_name = $data['proposal_data']['SourceSystemName_api'];
                    $data['proposal_data']['master_policy_no'] = $data['proposal_data']['EW_master_policy_no'];
                    $data['proposal_data']['group_code'] = $data['proposal_data']['EW_group_code'];
                }

                $combi_check = $this
                    ->db
                    ->query('SELECT e.master_policy_no,e.EW_master_policy_no,e.plan_code,pm.familyConstruct FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,payment_details as pd where epd.product_name = e.id AND ed.lead_id = "' . $data['customer_data']['lead_id'] . '"  AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id')->result_array();

                $combi_count = 0;
                $combi_product_count = 0;
				$no_of_product = count($combi_check);
				$combi_flag = ($no_of_product > 1)?'1':'0';
                foreach ($combi_check as $key => $value)
                {
                    $check_adult = explode('A', $value['familyConstruct']);

                    if ($check_adult[0] == 1 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 1;
                        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_count) , "product_id" => $data['proposal_data']['product_code'], "type" => "combi_count_1A_memberwise"] ;
                $this->db->insert("logs_docs", $request_arr);
                        if ($EW_check['EW_status'] && $data['proposal_data']['master_policy_no'] == $value['EW_master_policy_no'])
                        {
                            $combi_product_count += 1;
			$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_product_count) , "product_id" => $data['proposal_data']['product_code'], "type" => "combi_product_count_1A_EW_Status"] ;
                $this->db->insert("logs_docs", $request_arr);
                        }

                        if ($EW_check['EW_status'] == 0 && $data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
			   $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_product_count) , "product_id" => $data['proposal_data']['product_code'], "type" => "combi_product_count_1A_EW_Status_0"] ;
                $this->db->insert("logs_docs", $request_arr);
                        }

                    }

                    if ($check_adult[0] == 2 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 2;
$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_count) , "product_id" => $data['proposal_data']['product_code'], "type" => "combi_count_2A_memberwise"] ;
                $this->db->insert("logs_docs", $request_arr);
                        if ($EW_check['EW_status'] && $data['proposal_data']['master_policy_no'] == $value['EW_master_policy_no'])
                        {
                            $combi_product_count += 2;
			    	$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_product_count) , "product_id" =>$data['proposal_data']['product_code'], "type" => "combi_product_count_2A_EW_Status"] ;
                $this->db->insert("logs_docs", $request_arr);
                        }

                        if ($EW_check['EW_status'] == 0 && $data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 2;
$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_product_count) , "product_id"=> $data['proposal_data']['product_code'], "type" => "combi_product_count_2A_EW_Status_0"] ;
                $this->db->insert("logs_docs", $request_arr);
                        }

                    }

                    if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                    {
                        $combi_count += 1;
$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_count) , "product_id"=> $data['proposal_data']['product_code'], "type" => "combi_count_proposalwise"] ;
                $this->db->insert("logs_docs", $request_arr);
                        if ($EW_check['EW_status'] && $data['proposal_data']['master_policy_no'] == $value['EW_master_policy_no'])
                        {
                            $combi_product_count += 1;
$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_product_count) , "product_id"=> $data['proposal_data']['product_code'], "type" => "combi_product_count_proposalwise_EW_Status"] ;
                $this->db->insert("logs_docs", $request_arr);
                        }

                        if ($EW_check['EW_status'] == 0 && $data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($combi_product_count) , "product_id"=> $data['proposal_data']['product_code'], "type" => "combi_product_count_proposalwise_EW_Status_0"] ;
                $this->db->insert("logs_docs", $request_arr);
                        }

                    }

                }

                $totalMembers = count($data['member_data']);
                $member = [];
				
				$occupation = "O553";
				if ($data['proposal_data']['product_code'] == 'R07' || $data['proposal_data']['product_code'] == 'R11'){
				//if ($data['proposal_data']['product_code'] == 'R07'){
					$occupation_check = $this->db->query('SELECT occupation_id from master_occupation where id = "' . $data['customer_data']['occupation'] . '" ')->row_array();
					if (isset($occupation_check['occupation_id']))
					{
						$occupation = $occupation_check['occupation_id'];
					}
				}

                for ($i = 0;$i < $totalMembers;$i++)
                {
                    if (($data['proposal_data']['familyConstruct'] == '1A+1K' || $data['proposal_data']['familyConstruct'] == '1A+2K') && $data['member_data'][$i]['fr_id'] == 1)
                    {
                        $data['proposal_data']['group_code'] = $data['proposal_data']['spouse_group_code'];
                    }

                    $query = $this
                        ->db
                        ->query('SELECT pds.sub_member_code from employee_declare_member_sub_type as edmsp JOIN policy_declaration_subtype as pds ON edmsp.declare_sub_type_id = pds.declare_subtype_id where edmsp.emp_id="' . $emp_id . '" AND edmsp.policy_member_id = "' . $data['member_data'][$i]['policy_member_id'] . '" ')->result_array();

                    $abc = [];
                    if (!empty($query))
                    {
                        foreach ($query as $key => $value)
                        {

                            $abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];

                        }
                    }
                    else
                    {

                        $abc[] = ["PEDCode" => null, "Remarks" => null];
                    }

                    $member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['firstname'], "Middle_Name" => null, "Last_Name" => !empty(trim($data['member_data'][$i]['lastname'])) ? $data['member_data'][$i]['lastname'] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']) , "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => $occupation, "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['member_data'][$i]['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data']['nominee_fname'], "Nominee_Last_Name" => !empty(trim($data['nominee_data']['nominee_lname'])) ? $data['nominee_data']['nominee_lname'] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']) , ];
                }
// if($cust_id == '853087692GPAR111'){
// 	$cust_id = '853087692GPAR111_1';
// }

if($unique_ref_no =='1628742391GHIR11'){
echo	$unique_ref_no = '1628742391GHIR11_1';
}

                    //replaced cust_id with uniue_ref_no on 28-07-2021 - by upendra
                    //updated by upendra on 28-07-2021 - dedupe logic updated
                    $unique_ref_no_rows_check = $this
                    ->db
                    ->query("select ed.lead_id from employee_details as ed,proposal as p 
                    where ed.emp_id = p.emp_id and p.status in('Success','Payment Received','Cancelled') 
                    and ed.unique_ref_no = '" . $data['customer_data']['unique_ref_no'] . "' 
                    and ed.product_id = '".$data['proposal_data']['product_code']."' 
                    and ed.lead_id != '".$data['customer_data']['lead_id']."'
                    group by p.emp_id")->num_rows();

                    if($unique_ref_no_rows_check > 0){
                        $unique_ref_no_rows_check = $unique_ref_no_rows_check + 1;
                        $unique_ref_no = $unique_ref_no."_".$unique_ref_no_rows_check;
                    }
//print_r($unique_ref_no);
		
                $fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $unique_ref_no, "salutation" => $data['customer_data']['salutation'], "firstName" => $data['customer_data']['emp_firstname'], "middleName" => "", "lastName" => !empty(trim($data['customer_data']['emp_lastname'])) ? $data['customer_data']['emp_lastname'] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']) , -10) , "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => $AnnualIncome, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => $AutoRenewalFlag, "AutoDebit" => $AutoRenewalFlag, "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_id'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collection_amt['pay_amt'], 2) , "collectionRcvdDate" => $trans_date, "collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $data['proposal_data']['TxRefNo'], "instrumentDate" => $trans_date, "bankName" => $data['proposal_data']['bank_name'], "branchName" => empty($data['proposal_data']['branch']) ? "" : substr(str_replace(['(', ')', '/', '*'], ' ', $data['proposal_data']['branch']) , 0, 30) , "bankLocation" => null, "micrNo" => $micrNo, "chequeType" => null, "ifscCode" => $data['proposal_data']['ifscCode'], "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id, "CardNo" => null], "CombiCreation" => ["Combi_flag" => $combi_flag, "Combi_identifier" => "leadid", "Combi_value" => $data['customer_data']['lead_id'], "Combi_code" => $source_name, "Combi_Count" => $combi_count, "Combi_product_count" => $combi_product_count]];

                Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));

                $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "product_id" => $data['proposal_data']['product_code'], "type" => "full_quote_request2_" . $data['proposal_data']['policy_sub_type_id']];
                $this
                    ->db
                    ->insert("logs_docs", $request_arr);
                $insert_id = $this
                    ->db
                    ->insert_id();

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
                    CURLOPT_HTTPHEADER => array(
                        "Accept: */*",
                        "Cache-Control: no-cache",
                        "Connection: keep-alive",
                        "Content-Length: " . strlen(json_encode($fqrequest)) ,
                        "Content-Type: application/json",
                        "Host: bizpre.adityabirlahealth.com"
                    ) ,
                ));

                $response = curl_exec($curl);

                Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));

                $request_arr = ["res" => json_encode($response) ];
                $this
                    ->db
                    ->where("id", $insert_id);
                $this
                    ->db
                    ->update("logs_docs", $request_arr);

                $err = curl_error($curl);

                curl_close($curl);
				
				$extra_arr_update = ["is_policy_issue_initiated" => 0];
				$this
					->db
					->where("emp_id", $emp_id);
				$this
					->db
					->update("employee_details", $extra_arr_update);

                if ($err)
                {

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
                    $premium = $newArr['premium'];
                    $return_data = [];

                    if ($errorObj['ErrorNumber'] == '00' || ($errorObj['ErrorNumber'] == '302' && $data['proposal_data']['master_policy_no'] == $newArr['policyDtls']['PolicyNumber']))
                    {

                        $return_data['status'] = 'Success';
                        $return_data['msg'] = $errorObj['ErrorMessage'];

                        $return_data['ClientID'] = $newArr['policyDtls']['ClientID'];
                        $return_data['CertificateNumber'] = $newArr['policyDtls']['CertificateNumber'];
                        $return_data['QuotationNumber'] = $newArr['policyDtls']['QuotationNumber'];
                        $return_data['ProposalNumber'] = $newArr['policyDtls']['ProposalNumber'];
                        $return_data['PolicyNumber'] = $newArr['policyDtls']['PolicyNumber'];
                        $return_data['GrossPremium'] = empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'];

                        $create_policy_type = 0;
                        if ($cron_policy_check)
                        {
                            $create_policy_type = 1;
                        }

                        $api_insert = array(
                            "emp_id" => $emp_id,
                            "proposal_id" => $data['proposal_data']['proposal_id'],
                            "member_fr_id" => $mem_fr_id,
                            "client_id" => $newArr['policyDtls']['ClientID'],
                            "certificate_number" => $newArr['policyDtls']['CertificateNumber'],
                            "quotation_no" => $newArr['policyDtls']['QuotationNumber'],
                            "proposal_no" => $newArr['policyDtls']['ProposalNumber'],
                            "policy_no" => $newArr['policyDtls']['PolicyNumber'],
                            "gross_premium" => empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'],
                            "status" => "Success",
                            //"status" => $errorObj['ErrorMessage'],
                            "start_date" => $newArr['policyDtls']['startDate'],
                            "end_date" => $newArr['policyDtls']['EndDate'],
                            "created_date" => date('Y-m-d H:i:s') ,
                            "proposal_no_lead" => $data['proposal_data']['proposal_no'],
                            "PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
                            "letter_url" => $newArr['policyDtls']['LetterURL'],
                            "CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                            "MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                            "create_policy_type" => $create_policy_type,
                            "ReceiptNumber" => $newArr['receiptObj']['ReceiptNumber'],
                            //"COI_url" => $newArr['policyDtls']['COIUrl'],
                            
                        );
				$cert_check = $this->db->select('*')->from('api_proposal_response')
						->where('MemberCustomerID',$newArr['policyDtls']['MemberCustomerID'])	
						->where('certificate_number',$newArr['policyDtls']['CertificateNumber'])
						->get()
						->row_array();	
						
						if(empty($cert_check)){
                        $this
                            ->db
                            ->insert('api_proposal_response', $api_insert);

                        Monolog::saveLog("api_insert", "I", json_encode($api_insert));

                        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) , "product_id" => $data['proposal_data']['product_code'], "type" => "api_insert"];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);
			}else{
			$return_data['status'] = 'error';
}

                    }
                    else
                    {

                        ///------- @author : Guru --------------------------//
                        $request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], /*"req" => json_encode($fqrequest) ,*/
                        "product_id" => $data['proposal_data']['product_code'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_" . $data['proposal_data']['policy_sub_type_id']];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_failure_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);

                        $return_data = array(
                            'status' => 'error',
                            "msg" => $errorObj['ErrorMessage']
                        );
                    }

                    return $return_data;

                }

            }
            else
            {
				$extra_arr_update = ["is_policy_issue_initiated" => 0];
				$this
					->db
					->where("emp_id", $emp_id);
				$this
					->db
					->update("employee_details", $extra_arr_update);

                return $return_data = array(
                    'status' => 'error',
                    "msg" => "Quote error"
                );
            }
			
        }
    }

    function emandate_enquiry_HB_call_m()
    {
		echo "date change as per BB payment gateway change payu to ABHI landing page";
        exit;
        $query = $this
            ->db
            ->query("select ed.lead_id,ed.product_id,emd.status from proposal as p,payment_details as pd,employee_details as ed left join emandate_data as emd on emd.lead_id = ed.lead_id where ed.emp_id = p.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and emd.status NOT IN('Success','Fail') and p.status IN('Payment Received','Success') and p.EasyPay_PayU_status = 1 and date(p.created_date) > '2020-11-17' group by p.emp_id order by ed.emp_id desc limit 20")
            ->result_array();
		//date change as per BB payment gateway change payu to ABHI landing page
        if ($query)
        {
            foreach ($query as $val)
            {
                $this->real_pg_check($val['lead_id']);
            }

        }
    }

  function real_pg_check($lead_id)
    {
        $check_pg = false;
        // echo 'here';
        $query = $this
            ->db
            ->query("SELECT ed.is_non_integrated_single_journey,ed.lead_id,ed.emp_id,ed.product_id FROM employee_details as ed where ed.lead_id ='". $lead_id."'")->row_array();
      // print_pre($query);exit;
        if ($query)
        {
            
            $vertical = 'AXBBGRP';
            $pmode = 'PP';
            
            
            if ($query['product_id'] == 'R06' || $query['product_id'] == 'T01' || $query['product_id'] == 'T03')
            {
                $vertical = 'AXATGRP';
                $pmode = 'PO';
            }

            // if ($query['product_id'] = 'R06' && $query['product_id'] != 'T03')
            // {
                // $vertical = 'AXBBGRP';
                // $pmode = 'PP';
            // }
            // else
            // {
                // $vertical = 'AXATGRP';
                // $pmode = 'PP';
            // }

            if($query['is_non_integrated_single_journey'] == 1){//ankita single link journey changes
                $pmode = "PO";
                $vertical = "AXSLGRP";
            }
        //    echo  $vertical;exit;
            $CKS_data = "AX|" . $vertical . "|LEADID|" . $query['lead_id'] . "|" . $this->hash_key;

            $CKS_value = hash($this->hashMethod, $CKS_data);

            $url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
            $fqrequest = array(
                "signature" => $CKS_value,
                "Source" => "AX",
                "Vertical" => $vertical,
                "SearchMode" => "LEADID",
                "UniqueIdentifierValue" => $query['lead_id'],
                "PaymentMode" => $pmode
            );

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Length: " . strlen(json_encode($fqrequest)) ,
                    "Content-Type: application/json"
                ) ,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $result = json_decode($response, true);
           // print_pre($result);exit;
            if ($err)
            {
                $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($err) , "product_id" => $query['product_id'], "type" => "pg_real_fail"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

            }
            else
            { //print_pre($result);exit;

                if ($result && $result['PaymentStatus'] == 'PI')
                {
                    $arr_update = ["is_payment_initiated" => 1];
                    $this
                        ->db
                        ->where("lead_id", $query['lead_id']);
                    $this
                        ->db
                        ->update("employee_details", $arr_update);
                }

                if ($result && $result['PaymentStatus'] == 'PR')
                {

                    $TxStatus = "success";
                    $TxMsg = "No Error";

                    $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($result) , "product_id" => $query['product_id'], "type" => "pg_real_success"];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this
                        ->Logs_m
                        ->insertLogs($dataArray);

                    // $date = new DateTime($result['txnDateTime']);
                    // $txt_date = $date->format('m/d/Y g:i:s A');
                    $arr = ["payment_status" => $TxMsg, "premium_amount" => $result['amount'], "payment_type" => $result['paymentMode'], "txndate" => $result['txnDateTime'], "TxRefNo" => $result['TxRefNo'], "TxStatus" => $TxStatus, "json_quote_payment" => json_encode($result) ];

                    $proposal_ids = $this
                        ->db
                        ->query("select id as proposal_id from proposal where emp_id='" . $query['emp_id'] . "'")->result_array();

                    foreach ($proposal_ids as $query_val)
                    {
                        $this
                            ->db
                            ->where("proposal_id", $query_val['proposal_id']);
                        $this
                            ->db
                            ->where('TxStatus != ', 'success');
                        $this
                            ->db
                            ->update("payment_details", $arr);
                    }

                    if ($result['Registrationmode'])
                    {

                        $query_emandate = $this
                            ->db
                            ->query("select * from emandate_data where lead_id='" . $query['lead_id']."'")->row_array();

                        if ($result['EMandateStatus'] == 'MS')
                        {
                            $mandate_status = 'Success';
                        }
                        elseif ($result['EMandateStatus'] == 'MI')
                        {
                            $mandate_status = 'Emandate Pending';
                        }
                        elseif ($result['EMandateStatus'] == 'MR')
                        {
                            $mandate_status = 'Emandate Received';
                        }
                        elseif ($result['EMandateStatus'] == '')
                        {
                            $mandate_status = 'Emandate Pending';
                        }
                        else
                        {
                            $mandate_status = 'Fail';
                        }

                        if ($query_emandate > 0)
                        {

                            $arr = ["TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])) , "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'], "MandateLink" => $result['MandateLink']];

                            $this
                                ->db
                                ->where("lead_id", $query['lead_id']);
                            $this
                                ->db
                                ->update("emandate_data", $arr);
                        }
                        else
                        {

                            $arr = ["lead_id" => $query['lead_id'], "TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])) , "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'], "MandateLink" => $result['MandateLink']];

                            $this
                                ->db
                                ->insert("emandate_data", $arr);
                        }

                        if ($mandate_status == 'Success')
                        {
                            $this->send_message($query['lead_id'], 'success');
                        }

                        if ($mandate_status == 'Fail')
                        {
                            $this->send_message($query['lead_id'], 'fail');
                        }

                        if ($result['paymentMode'] == 'PP' && ($result['Registrationmode'] == 'SAD' || $result['Registrationmode'] == 'EMI'))
                        {
                            $this->send_message($query['lead_id'], 'SAD_EMI_one');
                            $this->send_message($query['lead_id'], 'SAD_EMI_two');
                        }

                    }

                    $check_pg = true;

                }
                else
                {

                    $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($result) , "product_id" => $query['product_id'], "type" => "pg_real_fail"];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this
                        ->Logs_m
                        ->insertLogs($dataArray);

                }

            }
        }

        return $check_pg;
    }

    function send_message($lead_id, $type)
    {
        $query_check = $this
            ->db
            ->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ee.EMandateFailureReason,ee.Registrationmode,ee.MandateLink,sum(p.premium) as total_amt FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,emandate_data as ee WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id = ee.lead_id AND ed.lead_id='" . $lead_id."'")->row_array();

        if ($query_check)
        {

            $senderID = 1;
            $full_name = trim($query_check['emp_firstname'].' '.$query_check['emp_lastname']);
            if(strlen($full_name) > 30){
                $full_name = substr($full_name, 0, 30);
            }
            $AlertV1 = $full_name;//$query_check['emp_firstname'] . " " . $query_check['emp_lastname'];
            $AlertV2 = (($query_check['total_amt'] * 1.5) + $query_check['total_amt']);
            $AlertV3 = $query_check['product_name'];
            $AlertV4 = '';
            $AlertV5 = '';

            $alertID = '';

            $alertMode = 2;

            if ($type == 'success')
            {

                if ($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC')
                {
                    $alertID = 'A1407';
                }

                if ($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI')
                {
                    $alertID = 'A1408';
                }

            }

            if ($type == 'fail')
            {

                if ($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC')
                {
                    $alertID = 'A1409';
                }

                if ($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI')
                {
                    $alertID = 'A1411';
                }

                $AlertV4 = $query_check['EMandateFailureReason'];
                //$AlertV5 = 'https://www.adityabirlacapital.com/healthinsurance/#!/our-branches';
                //$alertMode = 1;
		$AlertV5 = 'klr.pw/A0Wir';//branch locator bittly
                if(strlen($AlertV5) > 30){
                    $alertMode = 1;
                }
            }

            if ($type == 'SAD_EMI_one')
            {
                $alertID = 'A1405';
                $AlertV2 = $query_check['product_name'];
                $AlertV3 = $query_check['MandateLink'];
               if(strlen($query_check['MandateLink']) > 30){
                    $alertMode = 1;
                }
                
            }

            if ($type == 'SAD_EMI_two')
            {
                $alertID = 'A1406';
                $AlertV1 = $query_check['MandateLink'];
                if(strlen($query_check['MandateLink']) > 30){
                    $alertMode = 1;
                }
            }
			//echo $alertID;exit;
			if (empty($alertID))
            {
                //exit;
            }


		if($query_check['product_code'] == 'T03'){
                $channel_id = "Tele Health Pro Infinity";
            }else{
                $channel_id = "Axis Freedom Plus";
            }

            $parameters = ["RTdetails" => [

            "PolicyID" => '', "AppNo" => 'HD100017934', "alertID" => $alertID, "channel_ID" => $channel_id, "Req_Id" => 1, "field1" => '', "field2" => '', "field3" => '', "Alert_Mode" => $alertMode, "Alertdata" => ["mobileno" => substr(trim($query_check['mob_no']) , -10) , "emailId" => $query_check['email'], "AlertV1" => $AlertV1, "AlertV2" => $AlertV2, "AlertV3" => $AlertV3, "AlertV4" => $AlertV4, "AlertV5" => $AlertV5, ]

            ]

            ];
            $parameters = json_encode($parameters);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $query_check['click_pss_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $parameters,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",

                ) ,
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters) , "res" => json_encode($response) , "product_id" => $query_check['product_code'], "type" => "sms_logs_emandate" . $type];

            $dataArray['tablename'] = 'logs_docs';
            $dataArray['data'] = $request_arr;
            $this
                ->Logs_m
                ->insertLogs($dataArray);

        }
    }

function policy_creation_call_manual($CRM_Lead_Id, $cron_policy_check = '')
    {
        $update_data = $this
            ->db
            ->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,e.product_code,e.HB_policy_type
		FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
		employee_details AS ed
		where epd.product_name = e.id
		AND p.emp_id = ed.emp_id
		AND ed.lead_id = "' . $CRM_Lead_Id . '"
		AND epd.policy_detail_id = p.policy_detail_id');
        $update_data = $update_data->result_array();
 $query_lead = $this->db->query('SELECT emp_id FROM employee_details AS ed where ed.emp_id = "' . $update_data[0]['emp_id'] . '" AND ed.lead_status = "Rejected"');
        if ($query_lead->num_rows() > 0)
        {
            echo "Lead is Rejected.Please continue journey with Fresh Lead ";
            exit;
         }

	//print_pre($update_data);exit;	
		//update payment confirmation hit count
		$this->db->query("UPDATE proposal SET count = count + 1 WHERE emp_id ='".$update_data[0]['emp_id']."'");
 
        // GHI,GCS,GPA,GCI policy creation start
        foreach ($update_data as $update_payment)
        {
            //update payment confirmation hit count
            $arr_count = ["count" => $update_payment['count'] + 1];
            $this
                ->db
                ->where('id', $update_payment['id']);
            $this
                ->db
                ->update("proposal", $arr_count);

            if ($update_payment['status'] != 'Success')
            {

                // check first hit or not
                if ($update_payment['count'] < 3)
                {
                    //echo  "1";exit;
                    // update proposal status - Payment Received
                    $arr_new = ["status" => "Payment Received"];
                    $this
                        ->db
                        ->where('id', $update_payment['id']);
                    $this
                        ->db
                        ->update("proposal", $arr_new);

                    $api_response_tbl['status'] = 'error';

                    // For GHI,GPA,GCI policy check
                    $query = $this
                        ->db
                        ->query("select policy_detail_id from employee_policy_detail where policy_detail_id = '" . $update_payment['policy_detail_id'] . "' and policy_sub_type_id in(1,2,3,8)")->row_array();
 // print_pre($update_payment);exit;          
          if ($query)
                    {
                        if ($update_payment['HB_policy_type'] == 'ProposalWise')
                        {
                                      
							if ($update_payment['policy_subtype_id'] == 8)
							{
								$query2 = $this
									->db
									->query("SELECT p.status FROM product_master_with_subtype AS e,employee_details AS ed,proposal AS p,employee_policy_detail AS epd where ed.emp_id=p.emp_id AND epd.product_name = e.id AND epd.policy_detail_id = p.policy_detail_id AND ed.lead_id = '" . $CRM_Lead_Id . "' AND e.policy_subtype_id = 1")->row_array();

								if ($query2['status'] == 'Success')
								{
									$api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'], $cron_policy_check);
								}
							}else{
								$api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'], $cron_policy_check);
							}
                            
                        }
                        else
                        {

                            $query2 = $this
                                ->db
                                ->query("SELECT p.status FROM product_master_with_subtype AS e,employee_details AS ed,proposal AS p,employee_policy_detail AS epd where ed.emp_id=p.emp_id AND epd.product_name = e.id AND epd.policy_detail_id = p.policy_detail_id AND ed.lead_id = '" . $CRM_Lead_Id . "' AND e.policy_subtype_id = 1")->row_array();
     // print_pre($query2);exit;
                            //if ($query2['status'] == 'Success' || $update_payment['product_code'] == 'R10')
                            if ($query2['status'] == 'Success')
                            {
                                $api_response_tbl = $this->Memberwise_policy_call_manual($update_payment['emp_id'], $update_payment['policy_detail_id'], $cron_policy_check);
                               
    //print_pre($api_response_tbl);exit;
 }

                        }


		$request_arr_new = ["lead_id" => $CRM_Lead_Id, "req" => json_encode($api_response_tbl) , "res" => json_encode($api_response_tbl) , "product_id" => $update_payment['product_code'], "type" => "update_proposal_status"];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr_new;
                    $this->Logs_m->insertLogs($dataArray); 


              //          if ($api_response_tbl['status'] == 'error')
                		if ($api_response_tbl['status'] == 'error' || strtolower($api_response_tbl) == 'null')
			        {
                            $return_data['check'][] = 'error';
                            $return_data['code'] = '0';
                            $return_data['msg'] = $api_response_tbl['msg'];

                        }
                        else
                        {
                            // update proposal status - Success
			if(isset($api_response_tbl['status']) && $api_response_tbl['status'] == 'Success'){	
                            $arr = ["status" => "Success"];
                            $this
                                ->db
                                ->where('id', $update_payment['id']);
                            $this
                                ->db
                                ->update("proposal", $arr);
								
                            $return_data['check'][] = 'Success';
                            $return_data['code'] = '1';
                            $return_data['msg'] = $api_response_tbl['msg'];
			}
                        }

                    }

                }
                else
                {
                    $return_data['check'][] = 'error';
                    $return_data['code'] = '0';
                    $return_data['msg'] = '3 times fail count exceeded';
                }

            }
            else
            {
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
            /*$request_arr = ["si_mandate_date" => date('d/m/Y') ];
            $this
                ->db
                ->where("lead_id", $CRM_Lead_Id);
            $this
                ->db
                ->update("emandate_data", $request_arr);*/

            $data = array(
                'Status' => 'Success',
                'ErrorCode' => $return_data['code'],
                'ErrorDescription' => $return_data['msg'],
            );
        }

        return $data;

    }

function Memberwise_policy_call_manual($emp_id, $policy_detail_id, $field_array = '', $cron_policy_check = '')
    {
        $member_data = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
        //print_pre($member_data);exit;
        $query = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        $update_arr = ["emp_id" => $emp_id, "policy_detail_id" => $policy_detail_id];

        if (empty($query['multi_status']))
        {

            if ($member_data[0]['familyConstruct'] == '1A')
            {
                $arr = ["multi_status" => '0'];
            }
            else
            {
                $arr = ["multi_status" => '0,0'];
            }

            $this
                ->db
                ->where($update_arr);
            $this
                ->db
                ->update("proposal", $arr);
        }

        $query_again = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        $status_arr = explode(",", $query_again['multi_status'], 2);
        //print_pre($status_arr);print_pre($member_data);exit;
        foreach ($member_data as $key => $value)
        {
            /*uncomment if GCI member name is blank*/
            // if($policy_detail_id == 456 && empty($value['firstname'])){
            // $value = $this->update_gci($value);
            // }
            $value['key_id'] = $key + 1;
            $mem_data[0] = $value;
            //print_r($value);
            if ($status_arr[$key] == 0)
            {
                $policy_data = $this->GHI_GCI_api_call_manual($emp_id, $policy_detail_id, $mem_data, $field_array, $cron_policy_check);
                    //print_pre($policy_data);exit;
                if ($policy_data['status'] == 'Success')
                {
                    $status_arr[$key] = 1;

                    $status_string = implode(",", $status_arr);

                    $arr = ["multi_status" => $status_string];

                    $this
                        ->db
                        ->where($update_arr);
                    $this
                        ->db
                        ->update("proposal", $arr);

                }

            }

        }

        $query_last = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        if ($member_data[0]['familyConstruct'] == '1A')
        {
            $proposal_status = ($query_last['multi_status'] == '1') ? 'Success' : 'error';
        }

        if ($member_data[0]['familyConstruct'] == '2A')
        {
            $proposal_status = ($query_last['multi_status'] == '1,1') ? 'Success' : 'error';
        }

        return $return_data = array(
            'status' => $proposal_status,
            "msg" => "GPA GCI GP call"
        );

    }
function GHI_GCI_api_call_manual($emp_id, $policy_no, $mem_data = '', $field_array = '', $cron_policy_check = '')
    {
        /*check for payment done or not and prevent multiple policy create at same time*/
       $extra_check_data = $this
            ->db
            ->query("select pd.payment_status,pd.EasyPayId,pd.transaction_no,pd.TxRefNo,pd.TxStatus,ed.is_policy_issue_initiated from proposal as p,employee_details as ed,payment_details as pd  where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.emp_id ='" . $emp_id . "'")->row_array();
       //print_pre( $extra_check_data);exit;   
        /*if (($extra_check_data['payment_status'] == 'Success' && !empty($extra_check_data['EasyPayId'])) || ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success'))
        {*/

        if ((($extra_check_data['payment_status'] == 'Success' && !empty($extra_check_data['EasyPayId'])) || ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success')) && $extra_check_data['is_policy_issue_initiated'] == 0)
        {
                  //echo 1;exit;
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
          //    print_pre($data);exit;
            /*changes done by ankita - 19 march 2021*/
            $AutoRenewalFlag = '0';
            if($data['proposal_data']['si_auto_renewal']){
                if($data['proposal_data']['si_auto_renewal'] == 'Y'){
                    $AutoRenewalFlag = '1';
                }else{
                    $AutoRenewalFlag = '0';
                }
            }
            /*changes ends*/
            $url = trim($data['proposal_data']['api_url']);

            if ($url == '')
            {
                return array(
                    "status" => "error",
                    "msg" => "Something Went Wrong"
                );
            }
			
			if ($data['proposal_data']['product_code'] == 'R03' && date('Y-m-d', strtotime($data['proposal_data']['created_date'])) < '2020-12-17')
			{
				return array(
					"status" => "error",
					"msg" => "R03 Master policy expired"
				);
			}

            $transaction_date = explode(" ", $data['proposal_data']['txndate']);
            $trans_date = date("Y-m-d", strtotime($transaction_date[0]));
            $collectionMode = "online";
            $micrNo = null;
            $terminal_id = "76006448";
            $mem_fr_id = '';
            $collection_amt['pay_amt'] = $data['proposal_data']['premium'];
            $query_quote = [];
			$source_name = $data['proposal_data']['HB_source_code'];
			// $cust_id = $data['customer_data']['cust_id'];
			$unique_ref_no = $data['customer_data']['unique_ref_no'];
			
			$concat_string = $data['proposal_data']['HB_custid_concat_string'];
			if(!empty($concat_string)){
				// $cust_id = $data['customer_data']['cust_id'].$concat_string;
				$unique_ref_no = $data['customer_data']['unique_ref_no'].$concat_string;
			}
			
			if ($data['proposal_data']['product_code'] == 'R07')
			{
				$terminal_id = "76012611";
			}
		
			$AnnualIncome=null;
			if($data['customer_data']['annual_income']>0)
			{
				$AnnualIncome=$data['customer_data']['annual_income'];
			}
		
            if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
            {
				// $new_check_data = $this
				// ->db
				// ->query("select ed.lead_id from proposal as p,employee_details as ed  where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and p.policy_detail_id = '454' and p.created_date <= '2020-09-19' and ed.cust_id ='" . $data['customer_data']['cust_id'] . "'")->row_array();

                $new_check_data = $this
				->db
				->query("select ed.lead_id from proposal as p,employee_details as ed  where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and p.policy_detail_id = '454' and p.created_date <= '2020-09-19' and ed.unique_ref_no ='" . $data['customer_data']['unique_ref_no'] . "'")->row_array();
			
				if ($data['proposal_data']['product_code'] == 'R03' && !empty($new_check_data['lead_id']) && $data['proposal_data']['policy_sub_type_id'] == 1)
				{
					// $cust_id = $data['customer_data']['cust_id'].'GHIR03';
					$unique_ref_no = $data['customer_data']['unique_ref_no'].'GHIR03';
				}
				
				if ($data['proposal_data']['product_code'] == 'R07' && date('Y-m-d', strtotime($data['proposal_data']['created_date'])) < '2020-09-19')
				{
					// $cust_id = $data['customer_data']['cust_id'];
					$unique_ref_no = $data['customer_data']['unique_ref_no'];
				}
			/*R11 deductable*/
				if ($data['proposal_data']['product_code'] == 'R11' && $data['proposal_data']['policy_sub_type_id'] == 1)
				{
						if(!empty($data['customer_data']['deductable'])){

							$data['proposal_data']['sum_insured'] = ($data['proposal_data']['sum_insured'] - $data['customer_data']['deductable']);
							
							$GHI_supertopup_data = $this
								->db
								->query("select * from master_group_code where si_group = '" . $data['proposal_data']['sum_insured'] . "' and family_construct = '" . $data['proposal_data']['familyConstruct'] . "' and product_code = '".$data['proposal_data']['product_code']."'")->row_array();
							
							$data['proposal_data']['group_code'] = $GHI_supertopup_data['group_code'];
							$data['proposal_data']['spouse_group_code'] = $GHI_supertopup_data['spouse_group_code'];
						}
				}
				
                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'  and status = 'success'")->row_array();
      //print_pre($query_quote);exit;
                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                if ($field_array && $field_array['LeadId'])
                {
                    $collectionMode = ($data['proposal_data']['payment_type'] == 'Clearing') ? "Cheque" : "RTGS/ NEFT";
                    $data['proposal_data']['TxRefNo'] = empty($field_array['EasyPayId']) ? null : $field_array['EasyPayId'];
                    $trans_date = $field_array['RequestDateTime'];
                    $data['proposal_data']['bank_name'] = empty($field_array['BankName']) ? "Axis Bank Limited" : ucwords($field_array['BankName']);
                    $data['proposal_data']['branch'] = empty($field_array['BranchName']) ? null : $field_array['BranchName'];
                    $micrNo = $field_array['MICRNumber'];
                    $data['proposal_data']['ifscCode'] = empty($field_array['IFSCCode']) ? null : $field_array['IFSCCode'];
                    $terminal_id = "";
                    if ($data['proposal_data']['policy_sub_type_id'] == 1)
                    {
                        $terminal_id = "76010098";
                    }

                }

            }
            else
            {

                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $mem_data[0]['policy_subtype_id'] . "' and fr_id = '" . $mem_data[0]['fr_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "' and status = 'success'")->row_array();
                   // print_pre($query_quote);exit;

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                $data['member_data'] = $mem_data;
                $collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];
                $mem_fr_id = $data['member_data'][0]['fr_id'];

                // $cust_id = $data['customer_data']['cust_id'] . $concat_string . $data['member_data'][0]['key_id'];
                $unique_ref_no = $data['customer_data']['unique_ref_no'] . $concat_string . $data['member_data'][0]['key_id'];

                if ($field_array && $field_array['LeadId'])
                {
                    $collectionMode = ($data['proposal_data']['payment_type'] == 'Clearing') ? "Cheque" : "RTGS/ NEFT";
                    $data['proposal_data']['TxRefNo'] = empty($field_array['EasyPayId']) ? null : $field_array['EasyPayId'];
                    $trans_date = $field_array['RequestDateTime'];
                    $data['proposal_data']['bank_name'] = empty($field_array['BankName']) ? "Axis Bank Limited" : ucwords($field_array['BankName']);
                    $data['proposal_data']['branch'] = empty($field_array['BranchName']) ? null : $field_array['BranchName'];
                    $micrNo = $field_array['MICRNumber'];
                    $data['proposal_data']['ifscCode'] = empty($field_array['IFSCCode']) ? null : $field_array['IFSCCode'];
                    $terminal_id = "";
                }

            }

            if ($query_quote['QuotationNumber'])
            {

                $EW_check = $this
                    ->db
                    ->query('SELECT * from master_imd where BranchCode = "' . $data['proposal_data']['branch_code'] . '" and product_code = "' . $data['proposal_data']['imd_refer_product_code'] . '"')->row_array();

                if ($EW_check['EW_status'])
                {
                    $source_name = $data['proposal_data']['SourceSystemName_api'];
                    $data['proposal_data']['master_policy_no'] = $data['proposal_data']['EW_master_policy_no'];
                    $data['proposal_data']['group_code'] = $data['proposal_data']['EW_group_code'];
                }

                $combi_check = $this
                    ->db
                    ->query('SELECT e.master_policy_no,e.EW_master_policy_no,e.plan_code,pm.familyConstruct FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,payment_details as pd where epd.product_name = e.id AND ed.lead_id = "' . $data['customer_data']['lead_id'] . '"  AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id')->result_array();

                $combi_count = 0;
                $combi_product_count = 0;
				$no_of_product = count($combi_check);
				$combi_flag = ($no_of_product > 1)?'1':'0';
                foreach ($combi_check as $key => $value)
                {
                    $check_adult = explode('A', $value['familyConstruct']);

                    if ($check_adult[0] == 1 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 1;

                        if ($EW_check['EW_status'] && $data['proposal_data']['master_policy_no'] == $value['EW_master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                        if ($EW_check['EW_status'] == 0 && $data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                    }

                    if ($check_adult[0] == 2 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 2;

                        if ($EW_check['EW_status'] && $data['proposal_data']['master_policy_no'] == $value['EW_master_policy_no'])
                        {
                            $combi_product_count += 2;
                        }

                        if ($EW_check['EW_status'] == 0 && $data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 2;
                        }

                    }

                    if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                    {
                        $combi_count += 1;

                        if ($EW_check['EW_status'] && $data['proposal_data']['master_policy_no'] == $value['EW_master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                        if ($EW_check['EW_status'] == 0 && $data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                    }

                }

                $totalMembers = count($data['member_data']);
                $member = [];
				
				$occupation = "O553";
				if ($data['proposal_data']['product_code'] == 'R07' || $data['proposal_data']['product_code'] == 'R11'){
				//if ($data['proposal_data']['product_code'] == 'R07'){
					$occupation_check = $this->db->query('SELECT occupation_id from master_occupation where id = "' . $data['customer_data']['occupation'] . '" ')->row_array();
					if (isset($occupation_check['occupation_id']))
					{
						$occupation = $occupation_check['occupation_id'];
					}
				}

                for ($i = 0;$i < $totalMembers;$i++)
                {
                    if (($data['proposal_data']['familyConstruct'] == '1A+1K' || $data['proposal_data']['familyConstruct'] == '1A+2K') && $data['member_data'][$i]['fr_id'] == 1)
                    {
                        $data['proposal_data']['group_code'] = $data['proposal_data']['spouse_group_code'];
                    }

                    $query = $this
                        ->db
                        ->query('SELECT pds.sub_member_code from employee_declare_member_sub_type as edmsp JOIN policy_declaration_subtype as pds ON edmsp.declare_sub_type_id = pds.declare_subtype_id where edmsp.emp_id="' . $emp_id . '" AND edmsp.policy_member_id = "' . $data['member_data'][$i]['policy_member_id'] . '" ')->result_array();

                    $abc = [];
                    if (!empty($query))
                    {
                        foreach ($query as $key => $value)
                        {

                            $abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];

                        }
                    }
                    else
                    {

                        $abc[] = ["PEDCode" => null, "Remarks" => null];
                    }

                    $member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['firstname'], "Middle_Name" => null, "Last_Name" => !empty(trim($data['member_data'][$i]['lastname'])) ? $data['member_data'][$i]['lastname'] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']) , "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => $occupation, "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['member_data'][$i]['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data']['nominee_fname'], "Nominee_Last_Name" => !empty(trim($data['nominee_data']['nominee_lname'])) ? $data['nominee_data']['nominee_lname'] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']) , ];
                }

                    //replaced cust_id to unique_ref_no on 28-07-2021 by upendra
                    //updated by upendra on 28-07-2021 - dedupe logic updated
                    $unique_ref_no_rows_check = $this
                    ->db
                    ->query("select ed.lead_id from employee_details as ed,proposal as p 
                    where ed.emp_id = p.emp_id and p.status in('Success','Payment Received','Cancelled') 
                    and ed.unique_ref_no = '" . $data['customer_data']['unique_ref_no'] . "' 
                    and ed.product_id = '".$data['proposal_data']['product_code']."' 
                    and ed.lead_id != '".$data['customer_data']['lead_id']."'
                    group by p.emp_id")->num_rows();

                    if($unique_ref_no_rows_check > 0){
                        $unique_ref_no_rows_check = $unique_ref_no_rows_check + 1;
                        $unique_ref_no = $unique_ref_no."_".$unique_ref_no_rows_check;
                    }

                $fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $unique_ref_no, "salutation" => $data['customer_data']['salutation'], "firstName" => $data['customer_data']['emp_firstname'], "middleName" => "", "lastName" => !empty(trim($data['customer_data']['emp_lastname'])) ? $data['customer_data']['emp_lastname'] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']) , -10) , "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => $AnnualIncome, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => $AutoRenewalFlag , "AutoDebit" => $AutoRenewalFlag , "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_id'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collection_amt['pay_amt'], 2) , "collectionRcvdDate" => $trans_date, "collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $data['proposal_data']['TxRefNo'], "instrumentDate" => $trans_date, "bankName" => $data['proposal_data']['bank_name'], "branchName" => empty($data['proposal_data']['branch']) ? "" : substr(str_replace(['(', ')', '/', '*'], ' ', $data['proposal_data']['branch']) , 0, 30) , "bankLocation" => null, "micrNo" => $micrNo, "chequeType" => null, "ifscCode" => $data['proposal_data']['ifscCode'], "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id, "CardNo" => null], "CombiCreation" => ["Combi_flag" => $combi_flag, "Combi_identifier" => "leadid", "Combi_value" => $data['customer_data']['lead_id'], "Combi_code" => $source_name, "Combi_Count" => $combi_count, "Combi_product_count" => $combi_product_count]];

                Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));

                $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "product_id" => $data['proposal_data']['product_code'], "type" => "full_quote_request2_" . $data['proposal_data']['policy_sub_type_id']];
                $this
                    ->db
                    ->insert("logs_docs", $request_arr);
                $insert_id = $this
                    ->db
                    ->insert_id();

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
                    CURLOPT_HTTPHEADER => array(
                        "Accept: */*",
                        "Cache-Control: no-cache",
                        "Connection: keep-alive",
                        "Content-Length: " . strlen(json_encode($fqrequest)) ,
                        "Content-Type: application/json",
                        "Host: bizpre.adityabirlahealth.com"
                    ) ,
                ));

                $response = curl_exec($curl);

                Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));

                $request_arr = ["res" => json_encode($response) ];
                $this
                    ->db
                    ->where("id", $insert_id);
                $this
                    ->db
                    ->update("logs_docs", $request_arr);

                $err = curl_error($curl);

                curl_close($curl);
				
				$extra_arr_update = ["is_policy_issue_initiated" => 0];
				$this
					->db
					->where("emp_id", $emp_id);
				$this
					->db
					->update("employee_details", $extra_arr_update);

                if ($err)
                {

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
                    $premium = $newArr['premium'];
                    $return_data = [];

                    if ($errorObj['ErrorNumber'] == '00' || ($errorObj['ErrorNumber'] == '302' && $data['proposal_data']['master_policy_no'] == $newArr['policyDtls']['PolicyNumber']))
                    {

                        $return_data['status'] = 'Success';
                        $return_data['msg'] = $errorObj['ErrorMessage'];

                        $return_data['ClientID'] = $newArr['policyDtls']['ClientID'];
                        $return_data['CertificateNumber'] = $newArr['policyDtls']['CertificateNumber'];
                        $return_data['QuotationNumber'] = $newArr['policyDtls']['QuotationNumber'];
                        $return_data['ProposalNumber'] = $newArr['policyDtls']['ProposalNumber'];
                        $return_data['PolicyNumber'] = $newArr['policyDtls']['PolicyNumber'];
                        $return_data['GrossPremium'] = empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'];

                        $create_policy_type = 0;
                        if ($cron_policy_check)
                        {
                            $create_policy_type = 1;
                        }

                        $api_insert = array(
                            "emp_id" => $emp_id,
                            "proposal_id" => $data['proposal_data']['proposal_id'],
                            "member_fr_id" => $mem_fr_id,
                            "client_id" => $newArr['policyDtls']['ClientID'],
                            "certificate_number" => $newArr['policyDtls']['CertificateNumber'],
                            "quotation_no" => $newArr['policyDtls']['QuotationNumber'],
                            "proposal_no" => $newArr['policyDtls']['ProposalNumber'],
                            "policy_no" => $newArr['policyDtls']['PolicyNumber'],
                            "gross_premium" => empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'],
                            "status" => "Success",
                            //"status" => $errorObj['ErrorMessage'],
                            "start_date" => $newArr['policyDtls']['startDate'],
                            "end_date" => $newArr['policyDtls']['EndDate'],
                            "created_date" => date('Y-m-d H:i:s') ,
                            "proposal_no_lead" => $data['proposal_data']['proposal_no'],
                            "PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
                            "letter_url" => $newArr['policyDtls']['LetterURL'],
                            "CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                            "MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                            "create_policy_type" => $create_policy_type,
                            "ReceiptNumber" => $newArr['receiptObj']['ReceiptNumber'],
                            //"COI_url" => $newArr['policyDtls']['COIUrl'],
                            
                        );
			
			$cert_check = $this->db->select('*')->from('api_proposal_response')
						->where('MemberCustomerID',$newArr['policyDtls']['MemberCustomerID'])	
						->where('certificate_number',$newArr['policyDtls']['CertificateNumber'])
						->get()
						->row_array();	
			if(empty($cert_check)){
                        $this
                            ->db
                            ->insert('api_proposal_response', $api_insert);

                        Monolog::saveLog("api_insert", "I", json_encode($api_insert));

                        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) , "product_id" => $data['proposal_data']['product_code'], "type" => "api_insert"];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);
			}else{
				$return_data['status'] = 'error';
			}
                    }
                    else
                    {

                        ///------- @author : Guru --------------------------//
                        $request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], /*"req" => json_encode($fqrequest) ,*/
                        "product_id" => $data['proposal_data']['product_code'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_" . $data['proposal_data']['policy_sub_type_id']];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_failure_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);

                        $return_data = array(
                            'status' => 'error',
                            "msg" => $errorObj['ErrorMessage']
                        );
                    }

                    return $return_data;

                }

            }
            else
            {
				$extra_arr_update = ["is_policy_issue_initiated" => 0];
				$this
					->db
					->where("emp_id", $emp_id);
				$this
					->db
					->update("employee_details", $extra_arr_update);

                return $return_data = array(
                    'status' => 'error',
                    "msg" => "Quote error"
                );
            }
			
        }
    }


    function policy_creation_call($CRM_Lead_Id, $cron_policy_check = '')
    {
        $update_data = $this
            ->db
            ->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,e.product_code,e.HB_policy_type
		FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
		employee_details AS ed
		where epd.product_name = e.id
		AND p.emp_id = ed.emp_id
		AND ed.lead_id = "' . $CRM_Lead_Id . '"
		AND epd.policy_detail_id = p.policy_detail_id');
        $update_data = $update_data->result_array();
		
		//update payment confirmation hit count
		$this->db->query("UPDATE proposal SET count = count + 1 WHERE emp_id ='".$update_data[0]['emp_id']."'");
 
        // GHI,GCS,GPA,GCI policy creation start
        foreach ($update_data as $update_payment)
        {
            //update payment confirmation hit count
            $arr_count = ["count" => $update_payment['count'] + 1];
            $this
                ->db
                ->where('id', $update_payment['id']);
            $this
                ->db
                ->update("proposal", $arr_count);

            if ($update_payment['status'] != 'Success')
            {

                // check first hit or not
                if ($update_payment['count'] < 3)
                {

                    // update proposal status - Payment Received
                    $arr_new = ["status" => "Payment Received"];
                    $this
                        ->db
                        ->where('id', $update_payment['id']);
                    $this
                        ->db
                        ->update("proposal", $arr_new);
				 // update employee_details lead status - Payment Received
 $arr_new_lead = ["lead_status" => "Payment Received"];
                    $this->db->where_in('emp_id', $update_payment['emp_id']);
                    $this->db->update("employee_details", $arr_new_lead);

                    $api_response_tbl['status'] = 'error';

                    // For GHI,GPA,GCI policy check
                    $query = $this
                        ->db
                        ->query("select policy_detail_id from employee_policy_detail where policy_detail_id = '" . $update_payment['policy_detail_id'] . "' and policy_sub_type_id in(1,2,3,8)")->row_array();
                    if ($query)
                    {
                        if ($update_payment['HB_policy_type'] == 'ProposalWise')
                        {
							if ($update_payment['policy_subtype_id'] == 8)
							{
								$query2 = $this
									->db
									->query("SELECT p.status FROM product_master_with_subtype AS e,employee_details AS ed,proposal AS p,employee_policy_detail AS epd where ed.emp_id=p.emp_id AND epd.product_name = e.id AND epd.policy_detail_id = p.policy_detail_id AND ed.lead_id = '" . $CRM_Lead_Id . "' AND e.policy_subtype_id = 1")->row_array();

								if ($query2['status'] == 'Success')
								{
									$api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'], $cron_policy_check);
								}
							}else{
								$api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'], $cron_policy_check);
							}
                            
                        }
                        else
                        {

                            $query2 = $this
                                ->db
                                ->query("SELECT p.status FROM product_master_with_subtype AS e,employee_details AS ed,proposal AS p,employee_policy_detail AS epd where ed.emp_id=p.emp_id AND epd.product_name = e.id AND epd.policy_detail_id = p.policy_detail_id AND ed.lead_id = '" . $CRM_Lead_Id . "' AND e.policy_subtype_id = 1")->row_array();

                            //if ($query2['status'] == 'Success' || $update_payment['product_code'] == 'R10')
                            if ($query2['status'] == 'Success')
                            {
                                $api_response_tbl = $this->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id'], $cron_policy_check);
                            }

                        }

			$request_arr_new = ["lead_id" => $CRM_Lead_Id, "req" => json_encode($api_response_tbl) , "res" => json_encode($api_response_tbl) , "product_id" => $update_payment['product_code'], "type" => "update_proposal_status"];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr_new;
                    $this->Logs_m->insertLogs($dataArray);

                       // if ($api_response_tbl['status'] == 'error')
                        if ($api_response_tbl['status'] == 'error' || strtolower($api_response_tbl) == 'null')
			{
                            $return_data['check'][] = 'error';
                            $return_data['code'] = '0';
                            $return_data['msg'] = $api_response_tbl['msg'];

                        }
                        else
                        {
                            // update proposal status - Success
			if(isset($api_response_tbl['status']) && $api_response_tbl['status'] == 'Success'){

                            $arr = ["status" => "Success"];
                            $this
                                ->db
                                ->where('id', $update_payment['id']);
                            $this
                                ->db
                                ->update("proposal", $arr);
					 // update employee_details lead_status - Success
$arr_lead_update = ["lead_status" => "Success"];
                            $this->db->where_in('emp_id', $update_payment['emp_id']);
                            $this->db->update("employee_details", $arr_lead_update);
								
                            $return_data['check'][] = 'Success';
                            $return_data['code'] = '1';
                            $return_data['msg'] = $api_response_tbl['msg'];
                        }
			}
                    }

                }
                else
                {
                    $return_data['check'][] = 'error';
                    $return_data['code'] = '0';
                    $return_data['msg'] = '3 times fail count exceeded';
                }

            }
            else
            {
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
            /*$request_arr = ["si_mandate_date" => date('d/m/Y') ];
            $this
                ->db
                ->where("lead_id", $CRM_Lead_Id);
            $this
                ->db
                ->update("emandate_data", $request_arr);*/

            $data = array(
                'Status' => 'Success',
                'ErrorCode' => $return_data['code'],
                'ErrorDescription' => $return_data['msg'],
            );
        }

        return $data;

    }

    function redirect_url_send_m()
    {
        $emp_id = $this
            ->input
            ->post('emp_id');
        //encrypt change
        $emp_id = encrypt_decrypt_password($emp_id, 'D');
		
        $query_check = $this
            ->db
            ->query("SELECT ed.emp_id,ed.lead_id,ed.ISNRI,p.created_date,ed.email,ed.mob_no,ed.emp_firstname,mpst.product_code,mpst.click_pss_url,p.EasyPay_PayU_status,mpst.product_name FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.status = 'Payment Pending' AND ed.emp_id=" . $emp_id)->row_array();
        // echo $query_check;exit;
        if ($query_check > 0)
        {

            $emp_id_encrypt = encrypt_decrypt_password($query_check['emp_id']);
            $send_data = [];

            if ($query_check['EasyPay_PayU_status'] == 1)
            {
                //payU link
                $send_data[] = 2;
            }

            if ($send_data)
            {
                foreach ($send_data as $val)
                {

                    if ($val == 2)
                    {
                        $url = base_url("payment_confirmation_view/" . $emp_id_encrypt);
                        $name_data = "payu";
                    }

                    $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url_req,
                        CURLOPT_PROXY => "185.46.212.88",
                        CURLOPT_PROXYPORT => 443,
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

                        ) ,
                    ));

                    $result = curl_exec($curl);

                    curl_close($curl);

                    $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($url_req) , "res" => json_encode($result) , "product_id" => $query_check['product_code'], "type" => "bitly_url_" . $name_data];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this
                        ->Logs_m
                        ->insertLogs($dataArray);

                    $data = json_decode($result, true);

                    if ($data['txtly'] == '')
                    {
                        $data['txtly'] = $url;
                    }

                    

                    $full_n = $query_check['emp_firstname'];
                    if (strlen($full_n) > 30) {
                        $full_n = substr($full_n, 0, 30);
                    }

                    $senderID = 1;

                    if ($val == 2)
                    {
                        $AlertV1 = $full_n;//$query_check['emp_firstname'];
                        $AlertV2 = $data['txtly'];
                        $AlertV3 = date('m-d-Y', strtotime($query_check['created_date'] . ' + 1 days'));
                        $AlertV4 = $query_check['lead_id'];
                        $AlertV5 = 'PaymentSupport.HealthInsurance@adityabirlacapital.com';
                        $alertID = 'A828';
                    }

                    $isNri = $query_check['ISNRI'];
                    $product_id = $query_check['product_code'];

                    /**
                     * Alert Mode 3 : Send OTP in SMS & Email
                     * Alert Mode 2 : Send SMS Only
                     * Alert Mode 1 : Send Email Only
                     *
                     *
                     */

                    // Added By Shardul For Validating ISNRI on 20-Aug-2020
                    $dataArray['emp_id'] = $emp_id;
                    $dataArray['isNri'] = $isNri;
                    $dataArray['product_id'] = $product_id;
                    $alertMode = helper_validate_is_nri($dataArray);
                    if ($data['txtly'] == $url)
                    {
                        if(strlen($url) > 30){
                            $alertMode = 1;
                        }
                    }
                    $parameters = ["RTdetails" => [

                    "PolicyID" => '', "AppNo" => 'HD100017934', "alertID" => $alertID, "channel_ID" => $query_check['product_name'], "Req_Id" => 1, "field1" => '', "field2" => '', "field3" => '', "Alert_Mode" => $alertMode, "Alertdata" => ["mobileno" => substr(trim($query_check['mob_no']) , -10) , "emailId" => $query_check['email'], "AlertV1" => $AlertV1, "AlertV2" => $AlertV2, "AlertV3" => $AlertV3, "AlertV4" => $AlertV4, "AlertV5" => $AlertV5, "AlertV6" => '',

                    ]

                    ]

                    ];
                    $parameters = json_encode($parameters);
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $query_check['click_pss_url'],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $parameters,
                        CURLOPT_HTTPHEADER => array(
                            "cache-control: no-cache",
                            "content-type: application/json",

                        ) ,
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    if ($val == 2)
                    {

                        $query = $this
                            ->db
                            ->query("select * from user_payu_activity where emp_id='" . $query_check['emp_id'] . "'")->row_array();

                        if (empty($query))
                        {
                            $this
                                ->db
                                ->insert("user_payu_activity", ["emp_id" => $query_check['emp_id']]);
                        }

                    }

                    $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters) , "res" => json_encode($response) , "product_id" => $query_check['product_code'], "type" => "sms_logs_" . $name_data];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this
                        ->Logs_m
                        ->insertLogs($dataArray);

                }
            }
        }
    }

    function redirect_url_check_m()
    {
        $emp_id = $this
            ->input
            ->post('emp_id');

        $emp_id_encrypt = encrypt_decrypt_password($emp_id);

        $query_check = $this
            ->db
            ->query("select id,status,EasyPay_PayU_status from proposal where emp_id='$emp_id'")->result_array();

        foreach ($query_check as $val)
        {
            $status = $val['status'];
        }

        if ($status == 'Payment Pending' && $query_check[0]['EasyPay_PayU_status'] == '1')
        {
            $data = array(
                "status" => "1",
                "url" => base_url("api/payment_redirection_axis/" . $emp_id_encrypt)
            );
        }
        else if ($status == 'Success')
        {
            $data = array(
                "status" => "1",
                "url" => base_url("payment_success_view_call_axis/" . $emp_id_encrypt)
            );
        }
        else if ($status == 'Payment Received')
        {
            $payment_check = $this
                ->db
                ->query("select payment_status,TxRefNo from payment_details where proposal_id = '" . $query_check[0]['id'] . "' ")->row_array();
            if ($payment_check['payment_status'] == 'No Error' && !empty($payment_check['TxRefNo']))
            {
                $data = array(
                    "status" => "1",
                    "url" => base_url("payment_success_view_call_axis/" . $emp_id_encrypt)
                );
            }
            else
            {
                $data = array(
                    "status" => "3",
                    "url" => "#"
                );
            }
        }
        else
        {
            $data = array(
                "status" => "2",
                "url" => "#"
            );

        }

        return $data;
    }

    function update_payu_rejected_m()
    {
        $query = $this
            ->db
            ->query("select ed.lead_id,ed.emp_id from employee_details as ed,user_payu_activity as ua,proposal as p where ed.emp_id = ua.emp_id AND ed.emp_id = p.emp_id AND p.status = 'Payment Pending'  AND ua.status = 0 AND p.policy_detail_id in(392,393) AND (TIMESTAMPDIFF(SECOND, ua.created_time,now()) >= 259200) group by p.emp_id")
            ->result_array();

        if ($query)
        {
            foreach ($query as $val)
            {

                $this
                    ->db
                    ->where("emp_id", $val['emp_id']);
                $this
                    ->db
                    ->update("proposal", ["status" => "Rejected"]);

                $this
                    ->db
                    ->where("emp_id", $val['emp_id']);
                $this
                    ->db
                    ->update("user_payu_activity", ["status" => "1"]);
            }

        }
    }

    function check_error_data_m_rpay()
    {
        $emp_id = $this
            ->input
            ->post('emp_id');

        $emp_id_encrypt = encrypt_decrypt_password($emp_id);

        $query_check = $this
            ->db
            ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();

        if ($query_check > 0)
        {

            $query = $this
                ->db
                ->query("select pd.payment_status,p.status as proposal_status,p.count as p_count from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id=p.emp_id and p.id=pd.proposal_id and ed.emp_id = '$emp_id'")->result_array();
            foreach ($query as $val)
            {
                $query['payment_status'] = $val['payment_status'];
                $query['proposal_status'] = $val['proposal_status'];
                $query['p_count'] = $val['p_count'];
            }

            if ($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] < 3)
            {
                // quote genarate,payment done but policy pending
                $data = array(
                    "status" => "1",
                    "check" => "2",
                    "url" => base_url() . "payment_success_view_call_axis_rpay/" . $emp_id_encrypt
                );

            }
            else if ($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] >= 3)
            {
                // policy pending 3 count hit exceeded
                $data = array(
                    "status" => "2",
                    "check" => "3",
                    "url" => "#"
                );
            }
            else
            {
                // quote genarate but payment pending
                $data = array(
                    "status" => "1",
                    "check" => "1",
                    "url" => base_url() . "payment_confirmation_view/" . $emp_id_encrypt
                );

            }

        }
        else
        {

            $query = $this
                ->db
                ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and count < 3 and status = 'error'")->row_array();

            if ($query > 0)
            {
                // quote pending,payment pending
                $data = array(
                    "status" => "1",
                    "check" => "1",
                    "url" => base_url() . "payment_confirmation_view/" . $emp_id_encrypt
                );
            }
            else
            {
                // quote pending 3 count hit exceeded
                $data = array(
                    "status" => "2",
                    "check" => "3",
                    "url" => "#"
                );
            }

        }

        return $data;
    }

    function check_error_data_m()
    {
        $emp_id = $this
            ->input
            ->post('emp_id');

        $emp_id_encrypt = encrypt_decrypt_password($emp_id);

        $query_check = $this
            ->db
            ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();

        if ($query_check > 0)
        {

            $query = $this
                ->db
                ->query("select pd.payment_status,p.status as proposal_status,p.count as p_count from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id=p.emp_id and p.id=pd.proposal_id and ed.emp_id = '$emp_id'")->result_array();
            foreach ($query as $val)
            {
                $query['payment_status'] = $val['payment_status'];
                $query['proposal_status'] = $val['proposal_status'];
                $query['p_count'] = $val['p_count'];
            }

            if ($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] < 3)
            {
                // quote genarate,payment done but policy pending
                $data = array(
                    "status" => "1",
                    "check" => "2",
                    "url" => base_url() . "payment_success_view_call_axis/" . $emp_id_encrypt
                );

            }
            else if ($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] >= 3)
            {
                // policy pending 3 count hit exceeded
                $data = array(
                    "status" => "2",
                    "check" => "3",
                    "url" => "#"
                );
            }
            else
            {
                // quote genarate but payment pending
                $data = array(
                    "status" => "1",
                    "check" => "1",
                    "url" => base_url() . "api/payment_redirection_axis/" . $emp_id_encrypt
                );

            }

        }
        else
        {

            $query = $this
                ->db
                ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and count < 3 and status = 'error'")->row_array();

            if ($query > 0)
            {
                // quote pending,payment pending
                $data = array(
                    "status" => "1",
                    "check" => "1",
                    "url" => base_url() . "api/payment_redirection_axis/" . $emp_id_encrypt
                );
            }
            else
            {
                // quote pending 3 count hit exceeded
                $data = array(
                    "status" => "2",
                    "check" => "3",
                    "url" => "#"
                );
            }

        }

        return $data;
    }

    function get_profile($emp_id)
    {

        return $this
            ->db
            ->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();
        //return $this->db->select('*')->where(["emp_id" => $emp_id])->get("employee_details")->row();
        
    }

    function getProposalData($emp_id, $policy_no)
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
            ->query('SELECT pd.si_auto_renewal,p.policy_detail_id,p.created_date,p.id as proposal_id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,epd.policy_no,mgc.group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.plan_code,e.api_url,e.product_code,pd.TxRefNo,pd.ifscCode,pd.branch,pd.bank_name,epd.start_date,e.EW_master_policy_no,p.branch_code,mgc.EW_group_code,pm.familyConstruct,mgc.spouse_group_code,epd.policy_sub_type_id,pd.payment_status,pd.transaction_no,e.SourceSystemName_api,e.imd_refer_product_code,e.HB_source_code,e.HB_policy_type,e.HB_custid_concat_string FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,master_group_code AS mgc,payment_details as pd where epd.product_name = e.id AND p.emp_id = "' . $emp_id . '" AND p.policy_detail_id = "' . $policy_no . '" AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id ' . $extra_condition . ' AND p.sum_insured = mgc.si_group AND e.product_code = mgc.product_code AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id');

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

    function get_all_member_data($emp_id, $policy_detail_id)
    {

        $response = $this
            ->db
            ->query('SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"Self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_details AS ed WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = 0 AND fr.emp_id = ed.emp_id AND ed.emp_id = ' . $emp_id . ' AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_family_details AS efd,master_family_relation AS mfr WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = efd.family_id AND efd.fr_id = mfr.fr_id AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' AND fr.emp_id = ' . $emp_id)->result_array();
               //echo $this->db->last_query();exit();
        return $response;
    }

    function get_all_nominee($emp_id)
    {
        $response = $this
            ->db
            ->select('*,mfr.relation_code,mfr.fr_name,mfr.gender_option')
            ->from('member_policy_nominee AS mpn,master_family_relation as mfr')
            ->where('mpn.emp_id', $emp_id)->where('mpn.fr_id = mfr.fr_id')
            ->get()
            ->row_array();
        if ($response)
        {
            return $response;
        }
    }

    function coi_download_m($certificate_no = '')
    {
        if (empty($certificate_no))
        {
            $certificate_no = $this
                ->input
                ->post('certificate_no');
        }

        $data = $this
            ->db
            ->query("select ed.lead_id,ed.product_id,apr.certificate_number,apr.COI_url,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and apr.certificate_number = '$certificate_no' ")->row_array();

        $result = ["status" => "error", "url" => ""];

        if ($data['COI_url'] == "false" || $data['COI_url'] == "")
        {

            //$db_url =$this->db->query("select coi_url from product_master_with_subtype where product_code = 'R05'")->row_array();
            $url = 'https://esblive.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/searchRequest';

            $req_arr = ["SearchRequest" => [["CategoryID" => "", "DocumentID" => "", "ReferenceID" => "", "FileName" => "", "Description" => "", "DataClassParam" => [["DocSearchParamId" => "23",
            //"Value"=>"GHI-TS-20-2000569",
            "Value" => $certificate_no, ]]]], "SourceSystemName" => "Axis", "SearchOperator" => "Or"];

            $request = json_encode($req_arr);

            $res = $this->curl_call($url, $request);
            $response = json_decode($res, true);

            $request_arr = ["lead_id" => $data['lead_id'], "product_id" => $data['product_id'], "req" => json_encode($request) , "res" => json_encode($response) , "type" => "coi_genarate_req1"];

            $dataArray['tablename'] = 'logs_docs';
            $dataArray['data'] = $request_arr;
            $this
                ->Logs_m
                ->insertLogs($dataArray);

            if ($response['SearchResponse'])
            {
                $ress = $response['SearchResponse'];

                if ($ress[0]['Error'][0]['Code'] == 0)
                {
                    $check_data = $ress[0]['OmniDocImageIndex'];
                }

            }

            if (!empty($check_data))
            {

                $url = 'https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/downloadRequest';

                $req_arr = ['DownloadRequest' => [['GlobalId' => "", 'OmniDocImageIndex' => $ress[0]['OmniDocImageIndex'], 'FileName' => $ress[0]['FileName'], ], ], 'Identifier' => 'ByteArray', 'SourceSystemName' => 'Axis', ];

                $request = json_encode($req_arr);

                $res = $this->curl_call($url, $request);
                $response = json_decode($res, true);

                $request_arr = ["lead_id" => $data['lead_id'], "product_id" => $data['product_id'], "req" => json_encode($request) , "res" => json_encode($response['DownloadResponse'][0]['Error'][0]['Code']) , "type" => "coi_genarate_req2"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                if ($response)
                {
                    $ress = $response['DownloadResponse'];

                    if ($ress[0]['Error'][0]['Code'] == 0)
                    {

                        $decoded = base64_decode($ress[0]['ByteArray']);
                        $file = $ress[0]['FileName'];
                        $path = APPPATH . '/resources/' . $file;
                        file_put_contents($path, $decoded);
                        // move_uploaded_file($a, $path);
                        $update_arr = ["COI_url" => '/resources/' . $file];

                        $this
                            ->db
                            ->where("pr_api_id", $data['pr_api_id']);
                        $this
                            ->db
                            ->update("api_proposal_response", $update_arr);

                        $get_data = $this
                            ->db
                            ->query("select apr.COI_url from api_proposal_response as apr where apr.certificate_number = '$certificate_no' ")->row_array();

                        $result = array(
                            "status" => "success",
                            "url" => $get_data['COI_url']
                        );

                    }
                }

            }

        }
        else
        {
            $result = array(
                "status" => "success",
                "url" => $data['COI_url']
            );
        }

        return $result;
    }

    public function curl_call($url, $request)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array(
                "username: esb_axis",
                "password: esb@axis",
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: " . strlen($request) ,
                "Content-Type: application/json",
                "Host: bizpre.adityabirlahealth.com"
            ) ,
        ));

        return $response = curl_exec($curl);

    }



    public function fetch_policy_data_m()
    {

        $JSON_data = file_get_contents("php://input");
        $request = json_decode($JSON_data, true);

        $return_data = array(
            "status" => "error",
            "response" => "Required fields are empty"
        );

        $Source = $request['Source']; // HD / AX
        $Vertical = $request['Vertical']; // AXBBGRP/AXATGRP/AXDCGRP/ABCGRP
        $PaymentMode = $request['PaymentMode']; // PP
        $UniqueIdentifier = $request['UniqueIdentifier']; // LEADID
        $UniqueIdentifierValue = $request['UniqueIdentifierValue']; // value
        $Email = $request['Email'];
        $PhoneNo = $request['PhoneNo'];
        $EMandateRefno = $request['EMandateRefno'];
        $EMandateStatus = $request['EMandateStatus'];
        $EMandateDate = $request['EMandateDate'];

        if (!empty($Source) && !empty($Vertical) && !empty($PaymentMode) && !empty($UniqueIdentifier) && !empty($UniqueIdentifierValue))
        {

            switch ($Vertical)
            {
                case "AXBBGRP":
                    $product_id = 'R03,R07,R10';
                break;
                case "AXATGRP":
                    $product_id = 'R06,T01,T03';
                break;
                case "AXDCGRP":
                    $product_id = 'R05';
                break;
                case "ABCGRP":
                    $product_id = 'ABC';
                break;
                default:
                    $product_id = '';
            }

            if (!empty($product_id) && $UniqueIdentifier == 'LEADID')
            {

                $check = 0;

                if ($product_id == 'R05' || $product_id == 'ABC')
                {

                    $this->db1 = $this
                        ->load
                        ->database('axis_retail', true);

                    $lead_data = $this
                        ->db1
                        ->query("select ed.acc_type,ed.acc_no,ed.lead_id,pd.TxRefNo,pd.txndate,ed.cust_id,sum(p.premium) as premium,ed.emp_id,imd.branch_name from employee_details as ed,proposal as p,payment_details as pd,master_imd imd where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.branch_sol_id = imd.BranchCode and p.status in('Success','Payment Received') and ed.product_id = '" . $product_id . "' and ed.lead_id = '" . $UniqueIdentifierValue . "'")->row_array();
                    //print_r($lead_data);exit;
                    if (!empty($lead_data['emp_id']))
                    {

                        $policy_data = $this
                            ->db1
                            ->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) as certificate_number,GROUP_CONCAT(DISTINCT(proposal_no)) as proposal_no FROM api_proposal_response m WHERE m.emp_id = '" . $lead_data['emp_id'] . "' GROUP BY emp_id")->row_array();

                        if (!empty($policy_data['certificate_number']))
                        {
                            $check = 1;
                        }

                        if ($check)
                        {

                            $return_data = array(
                                "Bank_Name" => "Axis Bank",
                                "Debit_Account_Number" => empty($lead_data['acc_no']) ? '' : $lead_data['acc_no'],
                                "Account_Type" => empty($lead_data['acc_type']) ? '' : $lead_data['acc_type'],
                                "Bank_Branch_Name" => empty($lead_data['branch_name']) ? '' : $lead_data['branch_name'],
                                "MICR" => null,
                                "IFSC" => null,
                                "Policy_Number" => $policy_data['certificate_number'],
                                "Proposal_Number" => $policy_data['proposal_no'],
                                "Source" => null,
                                "Payment_ID" => $lead_data['TxRefNo'],
                                "Account_ID" => null,
                                "Order_ID" => null,
                                "Customer_ID" => $lead_data['cust_id'],
                                "Lead_ID" => $lead_data['lead_id'],
                                "Debit_Date" => $lead_data['txndate'],
                                "Debit_Amount" => $lead_data['premium'],
                                "Debit_Status" => "Active",
                                "Debit_failure_Reason" => "N",
                                "Debit_Attempt" => "1",
                            );
                        }

                        if (!empty($EMandateRefno))
                        {

                            if ($EMandateStatus == 'MS')
                            {
                                $mandate_status = 'Success';
                            }
                            elseif ($EMandateStatus == 'MI')
                            {
                                $mandate_status = 'Emandate Pending';
                            }
                            elseif ($EMandateStatus == 'MR')
                            {
                                $mandate_status = 'Emandate Received';
                            }
                            else
                            {
                                $mandate_status = '';
                            }

                            if (!empty($mandate_status))
                            {
                                $arr = ["TRN" => $EMandateRefno, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) ];

                                $this
                                    ->db
                                    ->where("lead_id", $lead_data['lead_id']);
                                $this
                                    ->db
                                    ->update("emandate_data", $arr);
                            }
                        }

                    }

                }
                else
                {

                    $lead_data = $this
                        ->db
                        ->query("select ed.lead_id,ed.json_qote,pd.TxRefNo,pd.EasyPayId,pd.payment_status,pd.txndate,ed.cust_id,ed.unique_ref_no,sum(p.premium) as premium,ed.emp_id from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success','Payment Received') and ed.lead_id = '" . $UniqueIdentifierValue . "'")->row_array();

                    //print_r($this->db->last_query());exit;
                    if (!empty($lead_data['emp_id']))
                    {

                        $policy_data = $this
                            ->db
                            ->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) as certificate_number,GROUP_CONCAT(DISTINCT(proposal_no)) as proposal_no FROM api_proposal_response m WHERE m.emp_id = '" . $lead_data['emp_id'] . "' GROUP BY emp_id")->row_array();

                        if (!empty($policy_data['certificate_number']))
                        {
                            $check = 1;
                        }

                        if ($check)
                        {

                            $bank_data = json_decode($lead_data['json_qote'], true);

                            $return_data = array(
                                "Bank_Name" => (($product_id == 'R06') ? "Axis Bank" : ($bank_data['AXISBANKACCOUNT'] == 'Y') ? 'Axis Bank' : 'Other') ,
                                "Debit_Account_Number" => empty($bank_data['ACCOUNTNUMBER']) ? '' : $bank_data['ACCOUNTNUMBER'],
                                "Account_Type" => null,
                                "Bank_Branch_Name" => empty($bank_data['BRANCH_NAME']) ? '' : $bank_data['BRANCH_NAME'],
                                "MICR" => null,
                                "IFSC" => empty($bank_data['IFSCCODE']) ? '' : $bank_data['IFSCCODE'],
                                "Policy_Number" => $policy_data['certificate_number'],
                                "Proposal_Number" => $policy_data['proposal_no'],
                                "Source" => null,
                                "Payment_ID" => ($lead_data['payment_status'] == 'No Error') ? $lead_data['TxRefNo'] : $lead_data['EasyPayId'],
                                "Account_ID" => null,
                                "Order_ID" => null,
                                // "Customer_ID" => $lead_data['cust_id'],
                                "Customer_ID" => $lead_data['unique_ref_no'],
                                "Lead_ID" => $lead_data['lead_id'],
                                "Debit_Date" => $lead_data['txndate'],
                                "Debit_Amount" => $lead_data['premium'],
                                "Debit_Status" => "Active",
                                "Debit_failure_Reason" => "N",
                                "Debit_Attempt" => "1",
                            );
                        }

                        if (!empty($EMandateRefno))
                        {

                            if ($EMandateStatus == 'MS')
                            {
                                $mandate_status = 'Success';
                            }
                            elseif ($EMandateStatus == 'MI')
                            {
                                $mandate_status = 'Emandate Pending';
                            }
                            elseif ($EMandateStatus == 'MR')
                            {
                                $mandate_status = 'Emandate Received';
                            }
                            else
                            {
                                $mandate_status = '';
                            }

                            if (!empty($mandate_status))
                            {
                                $arr = ["TRN" => $EMandateRefno, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) ];

                                $this
                                    ->db
                                    ->where("lead_id", $lead_data['lead_id']);
                                $this
                                    ->db
                                    ->update("emandate_data", $arr);
                            }
                        }

                    }

                }

                if ($check == 0)
                {
                    $return_data = array(
                        "Bank_Name" => "",
                        "Debit_Account_Number" => "",
                        "Account_Type" => "",
                        "Bank_Branch_Name" => "",
                        "MICR" => "",
                        "IFSC" => "",
                        "Policy_Number" => "",
                        "Proposal_Number" => "",
                        "Source" => "",
                        "Payment_ID" => "",
                        "Account_ID" => "",
                        "Order_ID" => "",
                        "Customer_ID" => "",
                        "Lead_ID" => "",
                        "Debit_Date" => "",
                        "Debit_Amount" => "",
                        "Debit_Status" => "",
                        "Debit_failure_Reason" => "",
                        "Debit_Attempt" => "",
                    );
                }

            }
            else
            {
                $return_data = array(
                    "status" => "error",
                    "response" => "Invalid Vertical or UniqueIdentifier"
                );
            }

        }

        return $return_data;

    }

}


