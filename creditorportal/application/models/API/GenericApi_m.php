<?php
/*
@author of this files - Ankita <ankita.badak@fyntune.com> and Siddhi <siddhi.yendhe@fyntune.com>
*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class GenericApi_m extends CI_Model {
    

    function __construct() {
        parent::__construct();
        define('SALT', 'Axis'); 
        define('SALT_ACCESS_TOKEN', 'Axis_access_token');
		$this->load->model("Logs_m");
    }

	//member policy validations
    public function get_unique_quote()
    {
        $quote_id = 'Q-' . time();
        $check_quote = $this->db->select("*")
            ->from("quote")
            ->where("quote_no", $quote_id)
            ->get()
            ->row_array();
        if (!empty($check_quote)) {
            $this->get_unique_quote();
        } else {
            return $quote_id;
        }

    }
    public function generate_quote($premium_arr_insert, $emp_id, $application_product_code)
    {

        $this->db->where(['emp_id' => $emp_id, 'application_product_code' => $application_product_code])
            ->delete("quote");
        $quote_id = $this->get_unique_quote();
        $total_premium = 0;
        foreach ($premium_arr_insert as $product_code => $premium_arr) {
            $total_premium += $premium_arr['premiumwithtax'];
            $quote_arr = [
                'emp_id' => $emp_id,
                'group_code' => $premium_arr['group_code'],
                'suminsured' => $premium_arr['suminsured'],
                'premium' => $premium_arr['premium'],
                'premium_with_tax' => $premium_arr['premiumwithtax'],
                'product_code' => $product_code,
                'application_product_code' => $application_product_code,
                'quote_no' => $quote_id,

            ];
            $this->db->insert('quote', $quote_arr);
            unset($premium_arr_insert[$product_code]['suminsured']);
            unset($premium_arr_insert[$product_code]['group_code']);
        }

        $this->db->where('emp_id', $emp_id);
        $this->db->where('application_product_code', $application_product_code);
        $this->db->update('quote', ['total_premium' => $total_premium]);

        $this->db->where('emp_id', $emp_id);
        $this->db->update('employee_details', ['quote_no' => $quote_id]);

        return ['status' => true, 'quote_id' => $quote_id, 'total_premium_with_tax' => $total_premium, 'premium_bifurcation' => $premium_arr_insert];
    }
	
public function product_validation($product_construct_arr,$product_code){
        $status = true;
        $return_arr = [];
        $ghi_construct = '';
        $get_all_policies = $this->db->select("mpws.plan_code,epd.policy_detail_id,epd.policy_sub_type_id,mpws.combo_flag")
                            ->from("employee_policy_detail epd,product_master_with_subtype mpws")
                            ->where("epd.product_name = mpws.id")
                            ->where("mpws.product_code",$product_code)
                             ->order_by("epd.policy_sub_type_id")
                             ->get()
                             ->result_array();
        foreach($get_all_policies as $value){
            if($value['combo_flag'] == 'Y'){
                
                if (!array_key_exists($value['plan_code'],$product_construct_arr)){
                    $status = false;
                    $return_arr[] = ['ErrorNumber' => '64','ErrorMessage' => 'product code'.$value['plan_code']. 'not present'];
                    continue;
                } else{
                    if(!$ghi_construct){
                        $ghi_construct = (explode('+',$product_construct_arr[$value['plan_code']]))[0];
                }else{
                    if($ghi_construct != $product_construct_arr[$value['plan_code']]){
                        $status = false; 
                        $return_arr[] = ['ErrorNumber' => '63','ErrorMessage' => 'family construct for '.$value['plan_code']. ' policy not as per ghi'];
                    }
                }
            }
        }
            else{
                if (array_key_exists($value['plan_code'],$product_construct_arr)){
                    if($ghi_construct != $product_construct_arr[$value['plan_code']]){
                        $status = false;
                        $return_arr[] = ['ErrorNumber' => '63','ErrorMessage' => 'family construct for '.$value['plan_code']. ' policy not as per ghi'];
                    }
                }   
            }
            
        }

        return ['status' => $status, 'errors' => $return_arr];

    }
    public function createProposal($dataArray = array())
    {
		//print_pre($dataArray);exit;

        $emp_id = !empty($dataArray['emp_id']) ? $dataArray['emp_id'] : 0;
        $plan_code = !empty($dataArray['plan_code']) ? $dataArray['plan_code'] : 0;
        $product_name = !empty($dataArray['product_name']) ? $dataArray['product_name'] : 0;
		
		$proposalReturnArray = array();
		
        if ($emp_id > 0) {

            // Get Lead Details
            $getEmployeeDetails = $this->db->query("select * from employee_details where emp_id = '$emp_id' ")->row_array();
			//print_pre($this->db->last_query());
            // Set Lead Id & Product Id values
            if ($getEmployeeDetails['lead_id'] != 0) {
                $lead_id = $getEmployeeDetails['lead_id'];
                $product_id = $getEmployeeDetails['product_id'];
            }
			
			// IMD Code & Branch Code Start
			$response_api = json_decode($getEmployeeDetails['json_qote'], true);
			$branch_code = "";
			$IMDCode = "";
			
			if(!empty($response_api)) {
				$branch_code = $response_api['branch_sol_id'];
				$IMDCode = $this
						   ->db
						   ->where('BranchCode', $branch_code)->get('master_imd')
						   ->row_array() ['IMDCode'];
			}
			// IMD Code & Branch Code End

            // Transaction Start
            //$this->db->trans_start();

            // Get Policy Details using Product Name : R03, R07 etc
            $policies = $this->db->select("*")->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
                ->where("ed.product_name = p.id")->where("p.product_code", $product_name)->order_by("ed.policy_sub_type_id")
                ->get()->result_array();
				//echo $this->db->last_query();exit;
			//print_pre($policies);
                $proposalMasterQuoteId = "M-".time();	


            foreach ($policies as $policyData) {

                // Get All Member Details Policy Wise
                $dataArrayMemberDetailsPoliceWise['emp_id'] = $emp_id;
                $dataArrayMemberDetailsPoliceWise['policyDetails'] = $policyData;
                $memberData = $this->getAllMemberDetailsPolicyWise($dataArrayMemberDetailsPoliceWise);
				//print_pre($memberData);continue;
                // Get Policy Detail Id
                $policy_details_individual = $this->db->where("policy_detail_id", $policyData['policy_detail_id'])->get("employee_policy_detail")->row_array();

                // Get Payment Status
                if ($policy_details_individual['proposal_approval'] == 'Y') {
                    $status = "Ready For Issuance";
                } else {
                    $check_payment = $this->db->select("*")->from("policy_payment_customer_mapping AS ppcm,master_payment_mode AS mpm")->where("ppcm.policy_id", $value['policy_detail_id'])->where("ppcm.mapping_id = mpm.id")
                        ->where("ppcm.type", "P")->group_by("ppcm.mapping_id")->get()->result_array();

                    if ($check_payment[0]['payment_mode_name'] != "Cheque" && $check_payment[0]['id'] != 4) {
                        $status = "Payment Pending";
                    } else {
                        $status = "Ready For Issuance";
                    }
                }

                $date = date('Y-m-d');

                // Get Proposal Unique number
                $proposal_no = $this->db->select("*")->from("proposal_unique_number")->get()->row_array();

                if (strtotime($date) == strtotime($proposal_no['date'])) {
                    $number = ++$proposal_no['number'];
                    $array = ["number" => $number];

                    $this->db->where('id', $proposal_no['id']);
                    $this->db->update('proposal_unique_number', $array);

                    $propsal_number = "P-" . $number;

                } else {
                    $number = date('Ymd') . '0000';
                    $propsal_number = "P-" . $number;
                    $array = ["number" => $number, "id" => 1, "date" => date('Y-m-d')];
                    $this->db->where('id', '1');
                    $this->db->delete('proposal_unique_number');
                    $this->db->insert('proposal_unique_number', $array);
                }

                $EasyPay_PayU_status = 0; // We will Update This Once get Response from Client

                // Member Proposal Array
                $proposal_array = ["proposal_no" => $propsal_number, "policy_detail_id" => $policyData['policy_detail_id'], "product_id" => $policyData['policy_parent_id'], "created_by" => $this->emp_id, "status" => $status, "branch_code" => $branch_code, "IMDCode" => $IMDCode, "created_date" => date('Y-m-d H:i:s'), "EasyPay_PayU_status" => $EasyPay_PayU_status, "emp_id" => $emp_id,'master_proposal_no'=>$proposalMasterQuoteId];

                // Policy Id
                $policy_details_ids = $policyData['policy_detail_id'];

                // Get Proposal Details
                $get_proposal = $this->db->query("select id,emp_id from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();

                // Assign Proposal Id
                if (count($get_proposal) <= 0) {
                    $this->db->insert("proposal", $proposal_array);
                    $proposal_id = $this->db->insert_id();
                } else {
                    $proposal_id = $get_proposal['id'];
                }

                // Insert Data Into Logs For Insert Proposal Start
                $logs_array['logsData'] = array();
                $logs_array['logsData'] = ["type" => "insert_proposal", "req" => json_encode($proposal_array), "lead_id" => $lead_id, "product_id" => $product_id];
                $this->addLogs($dataArray);
                // Insert Data Into Logs For Insert Proposal End

                // Create Proposal Id Array
                $proposal_array_ids[] = $proposal_id;
				
				$proposalPaymentDetails[$propsal_number] = array("premium"=>0,"gst"=>0,"netamount"=>0);
					
                // Insert Members In to Proposal_member table Start
                $sum = 0;
				$originalPremiumSum = 0;
				$gst = 0;
				
				foreach ($memberData as $memberValue) {
                    $member = $this->db->where("policy_member_id", $memberValue["policy_member_id"])->get("employee_policy_member")
                        ->row_array();
                    $member['proposal_id'] = $proposal_id;

                    // Insert Proposal Member
					unset($member['policy_mem_original_sum_premium']);
                    $this->db->insert("proposal_member", $member);

                    // Insert Log data for Insert proposal member Start
                    $logs_array['data'] = ["type" => "insert_proposal_member", "req" => json_encode($member), "lead_id" => $lead_id, "product_id" => $product_id];
                    
					// $this->Logs_m->insertLogs($logs_array);
                    // Insert Log data for Insert proposal member End

                    // Sum Insured Update Part Start
                    if ($memberValue['suminsured_type'] == 'memberAge') {
                        $sum += $memberValue['policy_mem_sum_premium'];
						$originalPremiumSum += $memberValue['policy_mem_original_sum_premium'];
						$gst += $memberValue['policy_mem_sum_premium'] - $memberValue['policy_mem_original_sum_premium'];
                    } else {
                        $sum = $memberValue['policy_mem_sum_premium'];
						$gst = $memberValue['policy_mem_sum_premium'] - $memberValue['policy_mem_original_sum_premium'];
						$originalPremiumSum = $memberValue['policy_mem_original_sum_premium'];
                    }
				
				$originalPremium = 0; 				
				if(!empty($originalPremiumSum) && $originalPremiumSum > 0) {
					$originalPremium = $originalPremiumSum;
				} 
				$gstAmount = 0;
				if(!empty($gst) && $gst > 0) {
					$gstAmount = $gst;
				}
				
				$netAmount = 0;
				if(!empty($sum) && $sum > 0) {
					$netAmount = $sum;
				} 
				
				$proposalPaymentDetails[$propsal_number] = array("premium"=>$originalPremium,"gst"=>$gstAmount,"netamount"=>$sum);
					
                    $this->db->where(['id' => $proposal_id])
                    ->update("proposal", ["sum_insured" => $memberValue['policy_mem_sum_insured'], "premium" => $sum]);
                    //$this->db->update('proposal', ["sum_insured" => $memberValue//['policy_mem_sum_insured'], "premium" => $sum])->where('id', $proposal_id);
                   // echo $this->db->last_query();
                    // Add Log for Update Proposal Sum Insured Premium Start
                    $logs_array['logsData'] = array();
                    $logs_array['logsData'] = ["type" => "update_proposal_suminsured_premium", "req" => json_encode($value['policy_mem_sum_insured'] . "" . $sum), "lead_id" => $lead_id, "product_id" => $product_id];
                    $this->addLogs($dataArray);
                    // Add Log for Update Proposal Sum Insured Premium End

                    // Sum Insured Update Part End
					
					// Add Proposal Payment Details Start
					$paymentDetailsArray = array('proposal_id'=>$proposal_id,"payment_status"=>"payment_pending","txnDate"=>date('Y-m-d H:i:s'));
					$update_datas = $this->db->insert('payment_details', $paymentDetailsArray);
					// Add Proposal Payment Details End

                }
                // Insert Members In to Proposal_member table End
            }
            //$this->db->trans_commit();
        }
		
		$proposalReturnArray['quotation_no'] = $proposalMasterQuoteId;
		$proposalReturnArray['proposal_payment_details'] = $proposalPaymentDetails;
		// print_r($proposalReturnArray);die();
        // return $proposalMasterQuoteId;
		return $proposalReturnArray;
    }

    public function getAllMemberDetailsPolicyWise($dataArray = array())
    {
        $subQuery1 = $this
            ->db
            ->select('epm.familyConstruct, epm.policy_mem_sum_insured,epm.policy_mem_original_sum_premium, epm.policy_mem_sum_premium, epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id, epd.suminsured_type, epd.insurer_id, epd.broker_id,epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
                            mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
            ->from('employee_details AS ed,
                            family_relation AS mf,
                            employee_policy_member AS epm,
                            employee_policy_detail AS epd,
                            master_policy_sub_type AS mpst,
                            master_policy_type AS mps,
                            master_insurance_companies AS ic')
            ->where('ed.emp_id = mf.emp_id')
            ->where('mf.family_relation_id = epm.family_relation_id')
            ->where('epm.policy_detail_id = epd.policy_detail_id')
            ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
            ->where('mpst.policy_type_id = mps.policy_type_id')
            ->where('epd.insurer_id = ic.insurer_id')
            ->where('epm.status != ', 'Inactive')
            ->where('mf.family_id', 0)
            ->where('mf.emp_id', $dataArray['emp_id'])->where('epd.policy_detail_id', $dataArray['policyDetails']['policy_detail_id'])->get_compiled_select();
        $op = $this
            ->db
            ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_original_sum_premium,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
            epd.suminsured_type,epd.insurer_id, epd.broker_id, epm.policy_detail_id,
                            epm.family_relation_id, epm.family_id, epd.policy_no,
                            mps.policy_type_id, mpst.policy_sub_type_id,
                            mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
                            fr.family_id, fr.emp_id, ic.insurer_id,
                            ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
                            fr_id, mfr.fr_name')
            ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
            ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
            ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
            ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
            ->where('ic`.`insurer_id` = `epd`.`insurer_id')
            ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
            ->where('fr`.`family_id` = `efd`.`family_id')
            ->where('epm.status!=', 'Inactive')
            ->where('efd`.`fr_id` = `mfr`.`fr_id')
            ->where('fr`.`emp_id`', $dataArray['emp_id'])->where('epd.policy_detail_id', $dataArray['policyDetails']['policy_detail_id'])->get_compiled_select();
        $response = $this
            ->db
            ->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
           // echo $this->db->last_query();
            return $response;
    }

    public function addLogs($dataArray)
    {
      //  $logsData = $dataArray['logsData'];
       // $this->Logs_m->insertLogs($logs_array);
    }
	
	public function get_premium($policy_detail_id, $sum_insure, $family_construct, $type, $age, $ew_status)
    {
		$premium_value_array = array();
        try {
            $premium1 = $this->db
                ->select('*')
                ->from('master_broker_ic_relationship as fc')
                ->where('fc.policy_id', $policy_detail_id)
                ->get()
                ->row_array();
            $family_construct1 = explode("+", $family_construct);

            if ($premium1['max_adult'] != 0 && $premium1['max_child'] == 0 && $family_construct1[1] != '') {
                $member_id = $family_construct1[0];
            } else {
                $member_id = $family_construct;
            }

            if ($type == 'family_construct') {

                $checks = $this->db->select("premium, PremiumServiceTax, sum_insured,EW_premium, EW_PremiumServiceTax")
                    ->from("family_construct_wise_si")
                    ->where("sum_insured", $sum_insure)
                    ->where("family_type", $member_id)
                    ->where("policy_detail_id", $policy_detail_id)
                    ->get()
                    ->row_array();

                if ($EW_status = 1) {
                    $premium_value = $checks['EW_PremiumServiceTax'];
					$original_premium_value = !empty($checks['EW_premium']) ? $checks['EW_premium'] : $checks['EW_PremiumServiceTax'];
                } else {
					$premium_value = $checks['PremiumServiceTax'];
                    $original_premium_value = $checks['premium'];
                }
				
				$premium_value_array['premium_value'] = $premium_value;
				$premium_value_array['original_premium_value'] = $original_premium_value;				
				
				return $premium_value_array;
                // return $premium_value;

            }

            if ($type == 'family_construct_age') {
                $check = $this->db->select("age_group, premium, PremiumServiceTax, sum_insured, EW_premium, EW_PremiumServiceTax")
                    ->from("family_construct_age_wise_si")
                    ->where("sum_insured", $sum_insure)
                    ->where("family_type", $member_id)
                    ->where("policy_detail_id", $policy_detail_id)
                    ->get()
                    ->result_array();

                foreach ($check as $values) {
                    $min_max_age = explode("-", $values['age_group']);

                    if ($age >= $min_max_age[0] && $age <= $min_max_age[1]) {

                        if ($EW_status = 1) {

                            $premium_value = $values['EW_PremiumServiceTax'];
							$original_premium_value = !empty($values['EW_premium']) ? $values['EW_premium'] : $values['EW_PremiumServiceTax'];
                        } else {

                            $premium_value = $values['PremiumServiceTax'];
							$original_premium_value = $values['premium'];
                        }
						$premium_value_array['premium_value'] = $premium_value;
						$premium_value_array['original_premium_value'] = $original_premium_value;
                        return $premium_value_array;
						// return $premium_value;

                    }
                }
            }

            if ($type == 'memberAge') {
                $check_age = $this->db->select("policy_age,premium, premium_with_tax, sum_insured,EW_premium_tax, EW_premium_with_tax")
                    ->from("policy_creation_age")
                    ->where("sum_insured", $sum_insure)
                    ->where("policy_id", $policy_detail_id)
                    ->get()
                    ->result_array();

                foreach ($check_age as $values_age) {

                    $min_max_age = explode("-", $values_age['policy_age']);

                    if ($age >= $min_max_age[0] && $age <= $min_max_age[1]) {

                        if ($EW_status = 1) {

                            $premium_value = $values_age['EW_premium_with_tax'];
							$original_premium_value = !empty($values['EW_premium']) ? $values['EW_premium'] : $values_age['EW_premium_with_tax'];
                        } else {

                            $premium_value = $values_age['premium_with_tax'];
							$original_premium_value = $values['premium'];
                        }
						$premium_value_array['premium_value'] = $premium_value;
						$premium_value_array['original_premium_value'] = $original_premium_value;
						
						return $premium_value_array;
                        // return $premium_value;

                    }
                }
            }

        } catch (Exception $e) {
            return 0;
        }

    }
	
    /*public function get_premium($policy_detail_id, $sum_insure, $family_construct, $type, $age, $ew_status)
    {

        try {
            $premium1 = $this->db
                ->select('*')
                ->from('master_broker_ic_relationship as fc')
                ->where('fc.policy_id', $policy_detail_id)
                ->get()
                ->row_array();
            $family_construct1 = explode("+", $family_construct);

            if ($premium1['max_adult'] != 0 && $premium1['max_child'] == 0 && $family_construct1[1] != '') {
                $member_id = $family_construct1[0];
            } else {
                $member_id = $family_construct;
            }

            if ($type == 'family_construct') {

                $checks = $this->db->select("PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
                    ->from("family_construct_wise_si")
                    ->where("sum_insured", $sum_insure)
                    ->where("family_type", $member_id)
                    ->where("policy_detail_id", $policy_detail_id)
                    ->get()
                    ->row_array();

                if ($EW_status = 1) {
                    $premium_value = $checks['EW_PremiumServiceTax'];
                } else {
                    $premium_value = $checks['PremiumServiceTax'];
                }

                return $premium_value;

            }

            if ($type == 'family_construct_age') {
                $check = $this->db->select("age_group,PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
                    ->from("family_construct_age_wise_si")
                    ->where("sum_insured", $sum_insure)
                    ->where("family_type", $member_id)
                    ->where("policy_detail_id", $policy_detail_id)
                    ->get()
                    ->result_array();

                foreach ($check as $values) {
                    $min_max_age = explode("-", $values['age_group']);

                    if ($age >= $min_max_age[0] && $age <= $min_max_age[1]) {

                        if ($EW_status = 1) {

                            $premium_value = $values['EW_PremiumServiceTax'];
                        } else {

                            $premium_value = $values['PremiumServiceTax'];
                        }
                        return $premium_value;

                    }
                }
            }

            if ($type == 'memberAge') {
                $check_age = $this->db->select("policy_age,premium_with_tax,sum_insured,EW_premium_with_tax")
                    ->from("policy_creation_age")
                    ->where("sum_insured", $sum_insure)
                    ->where("policy_id", $policy_detail_id)
                    ->get()
                    ->result_array();

                foreach ($check_age as $values_age) {

                    $min_max_age = explode("-", $values_age['policy_age']);

                    if ($age >= $min_max_age[0] && $age <= $min_max_age[1]) {

                        if ($EW_status = 1) {

                            $premium_value = $values_age['EW_premium_with_tax'];
                        } else {

                            $premium_value = $values_age['premium_with_tax'];
                        }

                        return $premium_value;

                    }
                }
            }

        } catch (Exception $e) {
            return 0;
        }

    }*/
    public function get_relation_from_code($relation_code)
    {
        $relationarr = $this->db->select("fr_id,fr_name")
            ->from("master_family_relation")
            ->where("relation_code", $relation_code)
            ->get()
            ->row_array();
        if (empty($relationarr)) {
            return false;

        }
        return ['relation_id' => $relationarr['fr_id'], 'relation_name' => $relationarr['fr_name']];

    }
	
    public function get_age_from_dob($dob)
    {

        $today = date("Y-m-d");
        $dateOfBirth = $dob;
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');
        $age_type = 'years';
        if ($age == 0) {
            $age = $diff->format('%a');
            $age_type = 'days';
        }

        return ['age' => $age, 'age_type' => $age_type, 'status' => true];

    }
    public function get_member_count_from_construct($family_construct)
    {

        preg_match_all('!\d+!', $family_construct, $matches);

        return (array_sum($matches[0]));

    }
    public function validateDate($date, $format = 'm/d/Y')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
    public function date_format($date)
    {
        $date = DateTime::createFromFormat('m/d/Y', $date);
        return $date->format('d-m-Y');
    }
	 function delete_all($emp_id){
        $data = $this->db->select("fr.family_relation_id")
            ->from("family_relation fr ,employee_policy_member epm")
            ->where("fr.family_relation_id = epm.family_relation_id")
            ->where("fr.emp_id",$emp_id)
			->group_by("fr.family_relation_id")
            ->get()
            
			
            ->result_array();
			//print_pre($data);
           $this->db->where('emp_id', $emp_id);
            $this->db->where('family_id !=', 0);
            $this->db->delete("family_relation");
			
			$this->db->where('emp_id', $emp_id);
            $this->db->delete("proposal");
        foreach($data as $value){
            $this->db->where('family_relation_id', $value['family_relation_id']);
            $this->db->delete("employee_policy_member");
			///print_pre($this->db->last_query());
            
			//print_pre($this->db->last_query());
            $this->db->where('family_relation_id', $value['family_relation_id']);
            $this->db->delete("proposal_member");
			//print_pre($this->db->last_query());


        }
    }
    public function member_addition($member)
    {
		//$this->db->trans_start(); 

        $relation_arr = $this->get_relation_from_code($member['relation_code']);
        $age_arr = $this->get_age_from_dob($member['dob']);
        $age = $age_arr['age'];
        $age_type = $age_arr['age_type'];

        if ($relation_arr['relation_id'] == 0) {
            $family_relation_id = $this->db->select('*')
                ->from('family_relation')
                ->where('emp_id', $member['emp_id'])
                ->where('family_id', 0)
                ->get()
                ->row_array()['family_relation_id'];

        } else {
            $family_relation_id = $this->db->select('*')
                ->from('family_relation,employee_family_details')
                ->where('emp_id', $member['emp_id'])
                ->where('family_relation.family_id = employee_family_details.family_id')
                ->where('employee_family_details.fr_id', $relation_arr['relation_id'])
                ->get()
                ->row_array()['family_relation_id'];

            if (empty($family_relation_id)) {
                $family_array = [
                    "fr_id" => $relation_arr['relation_id'],
                    "family_dob" => $this->date_format($member['dob']),
                    "family_firstname" => $member['firstname'],
                    "family_status" => 1,
                    "family_gender" => ($member['gender'] == 'M') ? 'Male' : 'Female',
                ];

                $this->db->insert('employee_family_details', $family_array);
                $this->db->insert("family_relation", ["emp_id" => $member['emp_id'], "family_id" => $this->db->insert_id()]);
                $family_relation_id = $this->db->insert_id();
            }
        }
        $get_ew_status = $this->db
            ->select('mi.EW_status')
            ->from('master_imd  as mi')
            ->where('mi.product_code', $member['product_code'])
            ->where('mi.BranchCode', $member['branch_sol_id'])
            ->get()
            ->row_array();
        $ew_status = $get_ew_status['EW_status'];
		if ($member['plan_code'] == '4216' && $member['product_code'] == 'R07') {
            if (!empty($member['max_age'])) {
                $age_premium = $member['max_age'];
            }
        }else{
            $age_premium = $age;
        }
		
		$dataArray = $this->get_premium($member['policy_detail_id'], $member['suminsured'], $member['family_construct'], $member['suminsured_type'], $age_premium, $ew_status);
		
        $member_array = [
            "policy_detail_id" => $member['policy_detail_id'],
            "family_relation_id" => $family_relation_id,
            "policy_mem_sum_insured" => $member['suminsured'],
            "policy_mem_sum_premium" => $dataArray['premium_value'],
			"policy_mem_original_sum_premium" => $dataArray['original_premium_value'],
            "policy_mem_gender" => ($member['policy_detail_id'] == 'M') ? 'Male' : 'Female',
            "policy_mem_dob" => $this->date_format($member['dob']),
            "age" => $age,
            "age_type" => $age_type,
            "member_status" => "pending",
            "fr_id" => $relation_arr['relation_id'],
            "policy_member_first_name" => $member['firstname'],
            "policy_member_middle_name" => $member['middlename'],
            "policy_member_last_name" => ($member['lastname']) ? $member['lastname'] : '.',
            "familyConstruct" => $member['family_construct'],

        ];
        $this->db->insert('employee_policy_member', $member_array);

        $nominee_relation = $this->get_relation_from_code($member['nominee_relation_code']);
        $array_nominee = [
            'emp_id' => $member['emp_id'],
            'fr_id' => $nominee_relation['relation_id'],
            'nominee_fname' => $member['nominee_firstname'],
            'nominee_lname' => $member['nominee_lastname'],
            'nominee_contact' => $member['nominee_contact'],
        ];

        $check_nominee = $this->db->select("*")
            ->from("member_policy_nominee")
            ->where("emp_id", $member['emp_id'])
            ->get()->row_array();
        if (!empty($check_nominee)) {
            $this->db->where(['nominee_id' => $check_nominee['nominee_id']])
                ->delete("member_policy_nominee");

        }
        $this->db->insert('member_policy_nominee', $array_nominee);

        foreach ($member['memberped'] as $disease) {
            if ($disease['PEDCode']) {
                $get_disease_id = $this->db->select("*")
                    ->from("master_disease")
                    ->where("sub_member_code", $disease['PEDCode'])
                    ->get()
                    ->row_array();

                $disease_array = [
                    "fr_id" => $relation_arr['relation_id'],
                    "disease_id" => $get_disease_id['id'],
                    "emp_id" => $member['emp_id'],

                ];
                $this->db->insert('employee_disease', $disease_array);
            }

        }
        if ($member['product_code'] == 'R07' && $relation_arr['relation_id'] == 0) {
            $occupation = $this->db->select("*")
                ->from("master_occupation")
                ->where("occupation_id", $member['occupation'])
                ->get()->row_array()['name'];

            $employee_update = [
                "annual_income" => $member['annual_income'],
                "occupation" => $occupation,
            ];

            $this->db->where(['emp_id' => $member['emp_id']])
                ->update("employee_details", $employee_update);

        }

    }
    public function check_member_validations($member, $check_premium = false)
    {
        $error = [];

        //check annual iincome
		if (empty($member['member_no'])) {
            $error[] = ['ErrorNumber' => '66', 'ErrorMessage' => 'Member No is required'];
        }
        if (empty($member['firstname'])) {
            $error[] = ['ErrorNumber' => '30','ErrorMessage' => 'Member first Name required'];
        } else {
            if (strlen($member['firstname']) > 30) {
                $error[] = ['ErrorNumber' => '31','ErrorMessage' => 'Member First Name cannot exceed 30 characters'];

            }
        }

        if (!empty($member['middlename'])) {
            if (strlen($member['middlename']) > 30) {
                $error[] = ['ErrorNumber' => '47','ErrorMessage' => 'Member Middle Name cannot exceed 30 characters'];

            }

        }
        if (empty($member['lastname'])) {
            $error[] = ['ErrorNumber' => '62', 'ErrorMessage' => 'Member lastname Name required'];
        } else {
            if (strlen($member['lastname']) > 30) {
                $error[] = ['ErrorNumber' => '32', 'ErrorMessage' => 'Member lastname Name cannot exceed 30 characters'];

            }
        }

        if (empty($member['gender'])) {
            $error[] = ['ErrorNumber' => '33','ErrorMessage' => 'Member Gender is required'];
        } else {
            if (!($member['gender'] == 'F' || $member['gender'] == 'M')) {
                $error[] = ['ErrorNumber' => '48','ErrorMessage' => 'Member gender is incorrect'];
            }
        }
        /*if ((empty($member['Salutation']))) {
        $error = ['009' => 'Member Salutation is required'];
        } else {
        if (!($member['Salutation'] == 'Mrs' || $member['Salutation'] == 'Mr')) {
        $error = ['009' => 'Member Salutation is incorrect'];

        }
        }*/
		 if (empty($member['salutation'])) {
            $error[] = ['ErrorNumber' => '29', 'ErrorMessage' => 'Member salutation is required'];
        }

		
		if ($member['is_payment'] == '1') {
        if (empty($member['nominee_firstname'])) {
            $error[] = ['ErrorNumber' => '21','ErrorMessage' => 'Nominee first name is  required'];
            
        } else {
            if (strlen($member['nominee_firstname']) > 30) {
                $error[] = ['ErrorNumber' => '22','ErrorMessage' => 'Nominee first name  cannot exceed 30 characters'];

            }

        }
        if (empty($member['nominee_lastname'])) {
            $error[] = ['ErrorNumber' => '49','ErrorMessage' => 'Nominee last name  is required'];
        } else {
            if (strlen($member['nominee_lastname']) > 30) {
                $error[] = ['ErrorNumber' => '23','ErrorMessage' => 'Nominee last name  cannot exceed 30 characters'];
            }

        }
        if (empty($member['nominee_contact'])) {
            $error[] = ['ErrorNumber' => '40','ErrorMessage' => 'Nominee Contact number  is required'];
        } else {
            if (strlen($member['nominee_contact']) > 10) {
                $error[] = ['ErrorNumber' => '41','ErrorMessage' => 'Nominee Contact number length should be between 10 to 20'];

            }

        }
        if (empty($member['nominee_relation_code'])) {
            $error[] = ['ErrorNumber' => '24','ErrorMessage' => 'Nominee Reationship code  is required'];
        } else {
            $nominee_relation = $this->get_relation_from_code($member['nominee_relation_code']);
            if (!($nominee_relation)) {
               
                $error[] = ['ErrorNumber' => '50','ErrorMessage' => 'Nominee Reationship code  is incorrect'];

            } else {

                $nominee_details = $this->db->select("*")
                    ->from("member_policy_nominee")
                    ->where("emp_id", $member['emp_id'])
                    ->get()->row_array();

                if (!empty($nominee_details)) {

                    if ($nominee_details['nominee_fname'] != $member['nominee_firstname'] ||
                        $nominee_details['nominee_contact'] != $member['nominee_contact'] ||
                        $nominee_details['nominee_lname'] != $member['nominee_lastname'] ||
                        $nominee_details['fr_id'] != $nominee_relation['relation_id']) {

                        $error[] = ['ErrorNumber' => '51','ErrorMessage' => 'Nominee Details mismatch'];
                    }

                }

            }

        }
		
	}
	if ($member['is_payment'] == '1') {
        if (empty($member['occupation'])) {
            if ($member['product_code'] == 'R07') {
                $error[] = ['ErrorNumber' => '36','ErrorMessage' => 'Member occupation is  required'];
            }

        } else {

            if ($member['product_code'] == 'R07') {
                $product_code = $this->db->select("*")
                    ->from("master_occupation")
                    ->where("occupation_id", $member['occupation'])
                    ->get()->row_array();
                if (empty($product_code)) {
                    $error[] = ['ErrorNumber' => '37','ErrorMessage' => 'occupation does not match with master'];
                }

            }

        }
	}

        if (empty($member['suminsured'])) {
           
            $error[] = ['ErrorNumber' => '16','ErrorMessage' => 'Member Suminsured is required'];
        }else{
            $grp_code_details = $this->db->select("*")
                ->from("master_group_code")
                ->where("EW_group_code", $member['group_code'])
                ->where("product_code", $member['product_code'])
                ->get()
                ->row_array();
                if(!empty($grp_code_details)){
                    if($member['suminsured'] != $grp_code_details['si_group']){
                     
                        $error[] = ['ErrorNumber' => '61','ErrorMessage' => 'Member sum insured incorrect as per group code'];
                    }
                }
            
        }
        if (empty($member['relation_code'])) {
            $error[] = ['ErrorNumber' => '35','ErrorMessage' => 'Member Relation Code is required'];
            $relation_arr = [];
        } else {
            $relation_arr = $this->get_relation_from_code($member['relation_code']);
            if (!($relation_arr)) {
                $error[] = ['ErrorNumber' => '52','ErrorMessage' => 'Member Relation Code is incorrect'];

            }else{

                $grp_code_details = $this->db->select("*")
                ->from("master_group_code")
                ->where("EW_group_code", $member['group_code'])
                ->where("product_code", $member['product_code'])
                ->get()
                ->row_array();
                if(!empty($grp_code_details)){
                if (!(in_array($relation_arr['relation_id'], explode(',', $grp_code_details['relation_id'])))) {
                    //$error[] = ['ErrorNumber' => '62','ErrorMessage' => 'Member relation not allowed as per group code'];

                }
            }


            }
        }
        if ($member['policy_detail_id']) {

            $check_relations = $this->db->select("*")
                ->from("master_broker_ic_relationship")
                ->where("policy_id", $member['policy_detail_id'])
                ->get()
                ->row_array();
            if (!empty($relation_arr)) {
                if (!(in_array($relation_arr['relation_id'], explode(',', $check_relations['relationship_id'])))) {
                    $error[] = ['ErrorNumber' => '53','ErrorMessage' => 'Member relation not allowed in policy'];

                }
            }

        }
		
		if ($member['is_payment'] == '1') {
			if (($relation_arr) && $member['policy_detail_id'] == 454 && $member['product_code'] == 'R07' && $member['suminsured'] > 1000000) {
            if ($relation_arr['relation_id'] == 0) {
                if (($member['annual_income'] * 8) < $member['suminsured']) {
                    $error[] = ['ErrorNumber' => '38','ErrorMessage' => 'Request to select SI lesser than opted'];
                }

            }

        }
	}

        if (empty($member['dob'])) {
            $error[] = ['ErrorNumber' => '34','ErrorMessage' => 'Member Date of Birth is required'];
        } else {
            if (!$this->validateDate($member['dob'])) {
                $error[] = ['ErrorNumber' => '55','ErrorMessage' => 'Member Date of Birth is invalid'];
            } else {
                $age_arr = $this->get_age_from_dob($member['dob']);
                $age = $age_arr['age'];
                $age_type = $age_arr['age_type'];

                if ($member['policy_detail_id'] && ($relation_arr)) {
                    $check_min_max = $this->db->select("*")
                        ->from("policy_age_limit,master_family_relation")
                        ->where("policy_detail_id", $member['policy_detail_id'])
                        ->where("policy_age_limit.relation_id", $relation_arr['relation_id'])
                        ->where("master_family_relation.fr_id = policy_age_limit.relation_id")
                        ->get()->row_array();

                    if ($age_type == 'days') {
                        if ($relation_arr['relation_id'] == 2 || $relation_arr['relation_id'] == 3) {

                            if ($age < 91) {
                                $error[] = ['ErrorNumber' => '56','ErrorMessage' => 'Invalid Age'];

                            }

                        } else {

                            $error[] = ['ErrorNumber' => '56','ErrorMessage' => 'Invalid Age'];
                        }

                    }

                    if (!($age >= $check_min_max['min_age'] && $age <= $check_min_max['max_age'])) {

                        $error[] = ['ErrorNumber' => '56','ErrorMessage' => 'Invalid Age'];
                    }
                }

            }

        }

        if ($check_premium) {

            $get_ew_status = $this->db
                ->select('mi.EW_status')
                ->from('master_imd  as mi')
                ->where('mi.product_code', $member['product_code'])
                ->where('mi.BranchCode', $member['branch_sol_id'])
                ->get()
                ->row_array();
            $ew_status = $get_ew_status['EW_status'];
            if (!empty($ew_status)) {
                if ($member['policy_detail_id'] && $member['suminsured'] && $member['family_construct'] && $member['suminsured_type'] && $age && $member['product_code'] && $member['branch_sol_id']) {
					 if ($member['plan_code'] == '4216' && $member['product_code'] == 'R07') {
                        if (!empty($member['max_age'])) {
                            $age_premium = $member['max_age'];
                        } else {
                            $error[] = ['ErrorNumber' => '57', 'ErrorMessage' => 'Max age is required'];
                        }

                    }else{
                        $age_premium = $age;
                    }

                    $premiumDataArray = $this->get_premium($member['policy_detail_id'], $member['suminsured'], $member['family_construct'], $member['suminsured_type'], $age_premium, $ew_status);
					$premium = $premiumDataArray['premium_value'];

                    if (!$premium) {
                        $error[] = ['ErrorNumber' => '57','ErrorMessage' => 'No Premium found'];

                    }
                }
            }

        }
		
	if ($member['is_payment'] == '1') {
        if (!empty($member['memberped'])) {
            foreach ($member['memberped'] as $disease) {

                if (!empty($disease['PEDCode'])) {
                    $get_disease_id = $this->db->select("*")
                        ->from("master_disease")
                        ->where("sub_member_code", $disease['PEDCode'])
                        ->get()
                        ->row_array();
                    if (empty($get_disease_id)) {
                        $error[] = ['ErrorNumber' => '58','ErrorMessage' => 'Invalid PED'];
                        continue;
                    }
                    if (!empty($relation_arr)) {

                        $check_disease = $this->db->select('*')
                            ->from('employee_disease ed')
                            ->where('disease_id', $get_disease_id['id'])
                            ->where('emp_id', $member['emp_id'])
                         ->where('fr_id != ', $relation_arr['relation_id'])
                            ->get()
                            ->row_array();
						if($relation_arr['relation_id'] == 2  ||  $relation_arr['relation_id'] == 3){
							 $family_relation_array = $this->db->select('*')
												->from('family_relation,employee_family_details')
												->where('emp_id', $member['emp_id'])
												->where('family_relation.family_id = employee_family_details.family_id')
												->where('employee_family_details.fr_id',$relation_arr['relation_id'])
												->get()
												->row_array();
								if(!empty($family_relation_array)){
								
								 $check_disease = $this->db->select('*')
                            ->from('employee_disease ed')
                            ->where('disease_id', $get_disease_id['id'])
                            ->where('emp_id', $member['emp_id'])
                         ->where('fr_id = ', $relation_arr['relation_id'])
                            ->get()
                            ->row_array();
								
								}				
						}	
							
                        // if (!empty($check_disease)) {
                        //     $error[] = ['ErrorNumber' => '59','ErrorMessage' => 'Chronic Error'];

                        // }

                    }
                }

            }

        }
	}

        if (empty($error)) {

            return ['status' => true];

        } else {
            return ['status' => false, 'error' => $error];

        }

		
    }
    public function member_validations($product_member,$common_errors,$payment_data)
    {
		
		//print_pre($product_member);exit;
        
        $product_validation = [];
        $product_code_validation;
		$premium_arr = [];
        $product_code_validation;
        $errors = [];
         if(!empty($common_errors)){
         $errors['proposal'] = $common_errors;
         }
        $i = 1;
		$is_payment;
        $emp_id;
		$quotation_no;
        $collection_amount;
        $create_proposal_arr = [];
        foreach ($product_member as $product_code => $members) {
            if (!$product_code_validation) {
                $product_code_validation = $members['product_code'];
            }
            if(!$quotation_no){
                 $quotation_no =  $members['quotation_no'];
            }
            if (!$is_payment) {
                $is_payment = $members['is_payment'];
            }
            if (!$emp_id) {
                $emp_id = $members['emp_id'];
            }
            if (!$collection_amount) {
                $collection_amount = $members['collection_amount'];
            }
            $policy_no = ['PolicyNO' => $i,'masterPolicyNo' => $members['master_policy_no'], 'productcode' => $product_code];
            $policy_errors = [];
			if (empty($members['plan_code'])) {
                $policy_errors[] = ['ErrorNumber' => '64', 'ErrorMessage' => 'Plan code is required'];
            }
            if (!empty(($members['product_code']))) {
                $check_product_code = $this->db->select('id')
                    ->from('product_master_with_subtype e')
                    ->where('e.plan_code',  $members['plan_code'])
                    ->where('e.product_code', $members['product_code'])
                    ->get()
                    ->row_array();
                if (empty($check_product_code)) {
                    $policy_errors[] = ['ErrorNumber' => '67', 'ErrorMessage' => 'Plan code is incorrect'];
                }
            }
            if (empty($members['master_policy_no'])) {
                $policy_errors[] = ['ErrorNumber' => '65', 'ErrorMessage' => 'Master Policy no is required'];
            }
            if (!empty(($members['product_code']))) {
                $check_product_code = $this->db->select('id')
                    ->from('product_master_with_subtype e')
                    ->where('e.plan_code', $product_code)
                    ->where('e.EW_master_policy_no', $members['master_policy_no'])
                    ->get()
                    ->row_array();
                if (empty($check_product_code)) {
                    $policy_errors[] = ['ErrorNumber' => '67', 'ErrorMessage' => 'Master Policy No is incorrect'];
                }
            }
            if (empty($members['scheme_code'])) {
                $policy_errors[] = ['ErrorNumber' => '66', 'ErrorMessage' => 'Scheme Code is required'];
            }
            if (empty($product_code)) {
                $policy_errors[] = ['ErrorNumber' => '44','ErrorMessage' => 'Product code is required'];
            } else {
                if(!empty(($members['product_code']))){
                $check_product_code = $this->db->select('id')
                    ->from('product_master_with_subtype e')
                    ->where('e.plan_code', $product_code)
                    ->where('e.product_code', $members['product_code'])
                    ->get()
                    ->row_array();
                if (empty($check_product_code)) {
                    $policy_errors[] = ['ErrorNumber' => '45','ErrorMessage' => 'Product code is incorrect'];
                } else {

                    if (empty($members['grpcode'])) {
                        $policy_errors[] = ['ErrorNumber' => '18','ErrorMessage' => 'family construct is required'];
                    } else {
                        if (!empty($members['product_code'])) {
                            $grp_code_details = $this->db->select('family_construct,si_per_member,si_group')
                                ->from('product_master_with_subtype e,master_group_code mgc')
                                ->where('e.product_code = mgc.product_code')
                                ->where('e.product_code', $members['product_code'])
                                ->where('mgc.EW_group_code', $members['grpcode'])
                                ->where('e.plan_code', $product_code)
                                ->get()
                                ->row_array();
                            if (empty($grp_code_details)) {
                                $policy_errors[] = ['ErrorNumber' => '46','ErrorMessage' => 'family construct is incorrect'];

                            } else {
                                // $family_construct = $grp_code_details['family_construct'];

                                // $total_member = $this->get_member_count_from_construct($family_construct);

                                // if ($total_member != count($members['members'])) {
                                    // $policy_errors[] = ['ErrorNumber' => '25','ErrorMessage' => 'Member not added as per construct'];

                                // }
								$family_construct = $grp_code_details['family_construct'];

                                    $total_member = $this->get_member_count_from_construct($family_construct);
                                    $policy_details = $this->db->select(' epd.policy_detail_id,epd.suminsured_type')
                                        ->from('product_master_with_subtype e,employee_policy_detail epd')
                                        ->where('e.id = epd.product_name')
                                        ->where('e.plan_code', $product_code)
                                        ->where('e.product_code', $members['product_code'])
                                        ->get()
                                        ->row_array();
                                    if ($is_payment == '1' || ($is_payment == 0 && $policy_details['suminsured_type'] == 'memberAge')) {
                                        if ($total_member != count($members['members'])) {
                                            $policy_errors[] = ['ErrorNumber' => '25', 'ErrorMessage' => 'Member not added as per construct'];

                                        }
                                    }
                            }

                        }
                    }

                }
            }
        }
        if(!isset($product_validation[$product_code])){
        $product_validation[$product_code] = $family_construct;
        }

            $policy_details = $this->db->select(' epd.policy_detail_id,epd.suminsured_type')
                ->from('product_master_with_subtype e,employee_policy_detail epd')
                ->where('e.id = epd.product_name')
                ->where('e.plan_code', $product_code)
                ->where('e.product_code', $members['product_code'])
                ->get()
                ->row_array();
				
				if ($product_code == '4216' && $members['product_code'] == 'R07') {
                if (!empty($policy_details)) {
                    $policy_details['suminsured_type'] = 'family_construct_age';
                }

            }

//ADD ARRAY MERGE FOR EVERY POLICY AND EVERY Member

            $sum_insured = [];
            $check_premium = true;
            $count_premium = 0;
            $sum_insured_check = true;
            $member_errors = [];
            $relation_code = [];
            $j = 1;
            foreach ($members['members'] as $value) {
                if(!isset($create_proposal_arr['emp_id'])){
                    $create_proposal_arr['emp_id'] = $value['emp_id'];
                   
                }
                if(!isset($create_proposal_arr['product_name'])){
                    $create_proposal_arr['product_name'] = $value['product_code'];
                   
                }
                
                $member_no = ['MemberNo' => $j];
                $value['policy_detail_id'] = (isset($policy_details['policy_detail_id'])) ? $policy_details['policy_detail_id'] : '';
                $value['family_construct'] = ($family_construct) ? $family_construct : '';
                $value['product_code'] = ($members['product_code']) ? $members['product_code'] : '';
                $value['group_code'] = ($members['grpcode']) ? $members['grpcode'] : '';
                $value['suminsured_type'] = (isset($policy_details['suminsured_type'])) ? $policy_details['suminsured_type'] : '';
                $value['is_payment'] = $is_payment;
                $value['plan_code'] = $product_code;


                $check_premium = ($count_premium == 0) ? true : false;
                $check = $this->check_member_validations($value, $check_premium);

                if (empty($relation_code)) {
                    $relation_code[] = $value['relation_code'];
                } else {
					 if(strtoupper($value['relation_code']) == 'R001' || strtoupper($value['relation_code']) == 'R002'){
						if (in_array($value['relation_code'], $relation_code)) {
							$check['status'] = false;
							$check['error'][] = ['ErrorNumber' => '60','ErrorMessage' => 'member already exists'];
						}
					 }

                }
                //also check common error list
                if ($check['status']) {
					
                    
                        $get_ew_status = $this->db
                            ->select('mi.EW_status')
                            ->from('master_imd  as mi')
                            ->where('mi.product_code', $value['product_code'])
                            ->where('mi.BranchCode', $value['branch_sol_id'])
                            ->get()
                            ->row_array();
                        $ew_status = $get_ew_status['EW_status'];

                        $age_arr = $this->get_age_from_dob($value['dob']);
                        $age = $age_arr['age'];
                        $age_type = $age_arr['age_type'];

                        if ($value['plan_code'] == '4216' && $value['product_code'] == 'R07') {
                            if (!empty($value['max_age'])) {
                                $age_premium = $value['max_age'];
                            }
                        }else{
                            $age_premium = $age;
                        }

                        $premium = $this->get_premium($value['policy_detail_id'], $value['suminsured'], $value['family_construct'], $value['suminsured_type'], $age_premium, $ew_status);

                        if (!isset($premium_arr[$product_code])) {

                            $premium_arr[$product_code] = ['suminsured' => $value['suminsured'], 'group_code' => $value['group_code'], 'premium' => $premium['original_premium_value'], 'premiumwithtax' => $premium['premium_value']];
                        } else {
                            if ($policy_details['suminsured_type'] == 'memberAge') {

                                $premium_arr[$product_code] = ['suminsured' => $value['suminsured'], 'group_code' => $value['group_code'], 'premium' => $premium_arr[$product_code]['premium'] + $premium['original_premium_value'], 'premiumwithtax' => $premium_arr[$product_code]['premiumwithtax'] + $premium['premium_value']];

                            }
                        }
                   
                    if (empty($sum_insured)) {

                       // $sum_insured[] = $value['suminsured'];
                    } else {
                        if (strtolower($policy_details['suminsured_type']) != 'memberAge') {

                            if (!in_array($value['suminsured'], $sum_insured)) {
                              //  if ($sum_insured_check) {
                                //    $check['error']['61'] = 'Sum Insured Mismatch';
                                }
                               // $sum_insured_check = false;
                                //$member_errors[] = ($member_no + $check['error']);
                                //continue;
                            }
                        }

                    
                    $this->member_addition($value);
                } else {
                    $member_error_append['errors'] =  $check['error'];
                    $member_errors[] = $member_no + $member_error_append;
                }
                $j++;
            }
            //and also check for policy errors also common errors
            if (!empty($policy_errors)) {
                $policy_errors_new['errors'] = $policy_errors;
            } 
            if (!empty($member_errors)) {
                $policy_errors_new['members'] = $member_errors;
            }

            if (!empty($policy_errors) || !empty($member_errors)) {
                $policy_errors_append = $policy_no +  $policy_errors_new;
                $errors['policy'][] = $policy_errors_append;
            }
            $i++;
        }
        $append_proposal = [];
		 if(empty($errors)){
        $check_product_wise_validation = $this->product_validation($product_validation,$product_code_validation);
        if(!$check_product_wise_validation['status']){
                $append_proposal['proposal'] = $errors['proposal'];
                foreach($check_product_wise_validation['errors']  as $value){
                    $append_proposal['proposal'][] = $value;
                }

        }
		 }
        $errors = $append_proposal + $errors;
        if (!empty($errors)) {

            //$this->db->trans_rollback();
			$this->delete_all($emp_id);
            $return_arr = ['status' => FALSE, 'errors' => $errors];
            header('Content-Type: application/json');
            echo json_encode($return_arr);exit;
            //ROLLBACK AND SEND ALL ERRORS
        } else {
			

			
			
            if ($is_payment == '0') {
				$this->delete_all($emp_id);
                //$this->db->trans_rollback();
                $response = $this->generate_quote($premium_arr, $emp_id, $product_code_validation);
                echo json_encode($response);exit;
            }
            //check product validation
           else{
                 
                $premium_calculated = array_sum(array_column($premium_arr,'premiumwithtax'));
                $premium_quote =  $this->db->select("total_premium")
                ->from("quote,employee_details")
                ->where("quote.quote_no = employee_details.quote_no")
                ->where("quote.quote_no",$quotation_no)
                ->get()
                ->row_array();
                if(($premium_quote['total_premium'] == $premium_calculated) && ($premium_calculated == $collection_amount)){
                $quotation_no = $this->createProposal($create_proposal_arr);
				// $this->db->trans_commit();
                //$return_arr = ['status' => true, 'quotation_no' => $quotation_no];
                //echo json_encode($return_arr);
				//siddhi change
                $policy_no = $this->generic_policy_create($quotation_no['quotation_no'],$payment_data);
				echo json_encode($policy_no);
               
            }
            else{
				$this->delete_all($emp_id);
				//$this->db->trans_rollback();
                $return_arr = ['status' => false, 'errors' => ['ErrorNumber' => '63','ErrorMessage' => 'Premium Mismatch']];
                echo json_encode($return_arr);exit;
            }
            }
		
        }
		

    }
	
//siddhi start (R03,R04,R06,R07)
public function generic_policy_create($master_proposal_no,$policy_receipt_data) 
{
		$query = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,p.premium,p.id as proposal_id,p.status,p.proposal_no,pd.TxStatus,ed.product_id FROM employee_details AS ed,proposal as p,payment_details AS pd WHERE p.emp_id = ed.emp_id AND p.id = pd.proposal_id AND p.master_proposal_no= '".$master_proposal_no."'")->result_array();
			
			if($query){
				
				if($policy_receipt_data['instrumentNumber']){
					
					$request_arr = ["lead_id" => $query[0]['lead_id'], "req" => json_encode($policy_receipt_data) ,"product_id"=> $query[0]['product_id'], "type"=>"payment_response_post"];
					
					$dataArray['tablename'] = 'logs_docs'; 
					$dataArray['data'] = $request_arr; 
					$this->Logs_m->insertLogs($dataArray);
				
					foreach ($query as $query_val)
					{
						if($query_val['TxStatus'] != 'success'){

						$request_arr = ["payment_status" => "No Error","premium_amount" => $amount,"payment_type" => "Online","pgRespCode" => "","merchantTxnId" => "","SourceTxnId" => "","txndate" => $policy_receipt_data['instrumentDate'],"TxRefNo" => $policy_receipt_data['instrumentNumber'],"TxStatus"=>"success","json_quote_payment"=>json_encode($policy_receipt_data)];
						
						$this->db->where("proposal_id",$query_val['proposal_id']);
						$this->db->update("payment_details",$request_arr);

						}
					}
				}
				
				$proposal_id = $query[0]['proposal_id'];
				
				$payment_data = $this->db->query("select payment_status,TxStatus from payment_details where proposal_id='$proposal_id'")->row_array();
				
				if($payment_data['TxStatus'] == 'success'){
					
					$check_result = $this->common_policy_creation_call($query[0]['lead_id']);
					
					$data = json_decode($check_result,true);

					if($data['Status'] == 'Success'){
					
					$data_policy = $this->db->query("SELECT GROUP_CONCAT(certificate_number) certificate_number,GROUP_CONCAT(quotation_no) quotation_no,GROUP_CONCAT(policy_no) policy_no FROM api_proposal_response m WHERE m.emp_id = '".$query[0]['emp_id']."' GROUP BY emp_id")->row_array();
					
					$all_data = $this->db->query("SELECT ed.cust_id,sum(p.premium) as total_amount,mpst.product_name,GROUP_CONCAT(mpst.plan_code) plan_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id=".$query[0]['emp_id'])->row_array();
					
						$data = [
									  'errorObj' => 
									  [ 
										[
										  'ErrorNumber' => '00',
										  'ErrorMessage' => 'Success',
										],
									  ],
									  'CombinedPremiumDetails' => 
									  [
										'TransactionReferenceNumber' => '',
										'QuotationNumber' => $data_policy['quotation_no'],
										'CertificateNumber' => $data_policy['certificate_number'],
										'LetterURL' => '',
										'ClientID' => '',
										'ProductName' => $all_data['product_name'],
										'ProductCode' => $all_data['plan_code'],
										'LineOfBusiness' => '42',
										'startDate' => '',
										'EndDate' => '',
										'PolicyNumber' => $data_policy['policy_no'],
										'ProposalNumber' => '',
										'PolicyStatus' => 'PQ1',
										'Tenure' => '1',
										'stpFlag' => '0',
										'MemberCustomerID' => $all_data['cust_id'],
										'BasePremium' => '8475',
										'NetPremium' => '8475',
										'GST' => '1525',
										'GrossPremium' => $all_data['total_amount'],
										'ReceiptNumber' => '',
										'ReceiptAmount' => '',
										'ExcessAmount' => '',
									 ],
								];
					
						
					}else{
						
						$data = [
									  'errorObj' => 
									  [ 
										[
										  'ErrorNumber' => '01',
										  'ErrorMessage' => 'Error In Policy Create',
										],
									  ],
									  'CombinedPremiumDetails' => 
									  [
										'TransactionReferenceNumber' => '',
										'QuotationNumber' => '',
										'CertificateNumber' => '',
										'LetterURL' => '',
										'ClientID' => '',
										'ProductName' => '',
										'ProductCode' => '',
										'LineOfBusiness' => '',
										'startDate' => '',
										'EndDate' => '',
										'PolicyNumber' => '',
										'ProposalNumber' => '',
										'PolicyStatus' => '',
										'Tenure' => '1',
										'stpFlag' => '0',
										'MemberCustomerID' => '',
										'BasePremium' => '',
										'NetPremium' => '',
										'GST' => '',
										'GrossPremium' => '',
										'ReceiptNumber' => '',
										'ReceiptAmount' => '',
										'ExcessAmount' => '',
									 ],
								];
								
					}
					
				}else{
					
					$data = [
									  'errorObj' => 
									  [ 
										[
										  'ErrorNumber' => '01',
										  'ErrorMessage' => 'Error In Payment Received',
										],
									  ],
									  'CombinedPremiumDetails' => 
									  [
										'TransactionReferenceNumber' => '',
										'QuotationNumber' => '',
										'CertificateNumber' => '',
										'LetterURL' => '',
										'ClientID' => '',
										'ProductName' => '',
										'ProductCode' => '',
										'LineOfBusiness' => '',
										'startDate' => '',
										'EndDate' => '',
										'PolicyNumber' => '',
										'ProposalNumber' => '',
										'PolicyStatus' => '',
										'Tenure' => '1',
										'stpFlag' => '0',
										'MemberCustomerID' => '',
										'BasePremium' => '',
										'NetPremium' => '',
										'GST' => '',
										'GrossPremium' => '',
										'ReceiptNumber' => '',
										'ReceiptAmount' => '',
										'ExcessAmount' => '',
									 ],
								];
								
				}
				
			}else{
			
			$data = [
									  'errorObj' => 
									  [ 
										[
										  'ErrorNumber' => '01',
										  'ErrorMessage' => 'Invalid DB Quote Id',
										],
									  ],
									  'CombinedPremiumDetails' => 
									  [
										'TransactionReferenceNumber' => '',
										'QuotationNumber' => '',
										'CertificateNumber' => '',
										'LetterURL' => '',
										'ClientID' => '',
										'ProductName' => '',
										'ProductCode' => '',
										'LineOfBusiness' => '',
										'startDate' => '',
										'EndDate' => '',
										'PolicyNumber' => '',
										'ProposalNumber' => '',
										'PolicyStatus' => '',
										'Tenure' => '1',
										'stpFlag' => '0',
										'MemberCustomerID' => '',
										'BasePremium' => '',
										'NetPremium' => '',
										'GST' => '',
										'GrossPremium' => '',
										'ReceiptNumber' => '',
										'ReceiptAmount' => '',
										'ExcessAmount' => '',
									 ],
								];
								

		}
		
	return $data;

} 
	
public function common_policy_creation_call($CRM_Lead_Id)
{
		$this->load->model("API/Payment_integration_freedom_plus", "obj_external_afpp", true);
		$this->load->model("API/Payment_telesale_m", "obj_external_tele", true);
		
		$message = '';
		$update_data = $this
		->db
		->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,ed.product_id,e.HB_policy_type
			FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
			employee_details AS ed
			where epd.product_name = e.id
			AND p.emp_id = ed.emp_id
			AND ed.lead_id = "' .$CRM_Lead_Id. '"
			AND epd.policy_detail_id = p.policy_detail_id
			AND e.product_code in("R03","R07","R10","R11","R06","T01")');
		$update_data = $update_data->result_array();

    // payment confirmation hit count update
			foreach ($update_data as $update_payment)
			{
				$arr_new = ["count" => $update_payment['count'] + 1];
				
				$this->db->where('id', $update_payment['id']);
				$this->db->update("proposal", $arr_new);
				
			}       

    // GHI,GCS,GPA,GCI policy creation start
			foreach ($update_data as $update_payment)
			{
				if($update_payment['status']!='Success'){

      // check first hit or not
					if($update_payment['count'] < 3){
			  		
       // update proposal status - Payment Received
								
						$arr_new = ["status" => "Payment Received"];
						$this->db->where('id', $update_payment['id']);
						$this->db->update("proposal", $arr_new);
						
						
			// For GHI,GPA,GCI policy check
						$query = $this->db->query("select policy_detail_id from employee_policy_detail where policy_detail_id = '" . $update_payment['policy_detail_id'] . "' and policy_sub_type_id in(1,2,3,8)")->row_array();
						if($query)
						{
							if($update_payment['HB_policy_type'] == 'ProposalWise')
							{
								if($update_payment['product_id'] == 'R06' || $update_payment['product_id'] == 'T01')
								{
									$api_response_tbl = $this->obj_external_tele->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id']);
								}else{
									$api_response_tbl = $this->obj_external_afpp->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id']);
								}
								
							}else
							{
								if($update_payment['product_id'] == 'R06' || $update_payment['product_id'] == 'T01')
								{
									$api_response_tbl = $this->obj_external_tele->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id']);
								}else{
									$api_response_tbl = $this->obj_external_afpp->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id']);
								}
								
							}
				
							if($api_response_tbl['status']=='error'){
								
								$data = array (
									'Status' => 'error',
									'ErrorCode' => '0',
									'ErrorDescription' => $api_response_tbl['msg'],
								);

							}else{
								// update proposal status - Success 
							
									$arr = ["status" => "Success"];
									$this->db->where('id', $update_payment['id']);
									$this->db->update("proposal", $arr);
									
								$data = array(
									'Status' => "Success",
									'ErrorCode' => '1',
									'ErrorDescription' => $api_response_tbl['msg'],

								);
							}

						}
						
						

					}else{
						$data = array(
							'Status' => 'error',
							'ErrorCode' => '0',
							'ErrorDescription' => '3 times fail count exceeded',
						);
						
					}
					
				}else{
					$data = array(
						'Status' => 'Success',
						'ErrorCode' => '2',
						'ErrorDescription' => 'Already genarate',
					);
				}

			}
			
			$message = json_encode($data);
			return $message;

}
//siddhi end


//ends
    public function create_access_token($cust_id,$cust_secret){
        $returnArr = array();
        $curdate = date('Y-m-d H:i:s');

        //check cust_secret matches with cust_id
        $new_cust_secret = hash('sha256', $cust_id.SALT);
        //echo $new_cust_secret;exit;
        if($new_cust_secret != $cust_secret){
            $returnArr['Error']['ErrorNumber'] = '01';
            $returnArr['Error']['ErrorMessage'] = 'Authentication cust secret not matched !';
            $returnArr['Error']['Access_token'] = '';
            return $returnArr;
        }

        $data = $this->db->get_where('tbl_authenticaton',array('cust_id' => $cust_id, 'cust_secret' => $cust_secret))->row_array();
        //print_r($data);exit;
        if(empty($data)){//insert new token entry            
            $access_token = hash('sha256', $cust_id.SALT_ACCESS_TOKEN.time());
            $expiry_date = date("Y-m-d H:i:s", strtotime("+1 hours"));
            $insertArr = array('cust_id' => $cust_id,
                               'cust_secret' =>  $cust_secret,
                               'access_token' => $access_token,
                               'expiry_date' => $expiry_date,
                               'created_date' => $curdate);
            $this->db->insert('tbl_authenticaton',$insertArr);
            $returnArr['Error']['ErrorNumber'] = '00';
            $returnArr['Error']['ErrorMessage'] = 'Authenticated Sucessfully !';
            $returnArr['Error']['Access_token'] = $access_token;
        }else{
            //print_r($data);exit;
            $returnArr['Error']['ErrorNumber'] = '00';
            $returnArr['Error']['ErrorMessage'] = 'Authenticated Sucessfully !';            
            if($data['expiry_date'] >= $curdate){
                $returnArr['Error']['Access_token'] = $data['access_token'];
            }else{// token exppired create new token
                $access_token = hash('sha256', $cust_id.SALT_ACCESS_TOKEN.time());
                $expiry_date = date("Y-m-d H:i:s", strtotime("+1 hours"));
                $updateArr = array('access_token' => $access_token,
                                   'expiry_date' => $expiry_date);
                $this->db->update('tbl_authenticaton',$updateArr);
                $returnArr['Error']['Access_token'] = $access_token;
            }
        }
        return $returnArr;
    }


    public function verify_access_token($access_token){
        $returnArr = array();
        $curdate = date('Y-m-d H:i:s');
        $data = $this->db->get_where('tbl_authenticaton',array('access_token' => $access_token))->row_array();

        if(empty($data)){
            $returnArr['Error']['ErrorNumber'] = '01';
            $returnArr['Error']['ErrorMessage'] = 'Authentication Failed !';
            $returnArr['Error']['Access_token'] = '';
        }else{
                      
            if($data['expiry_date'] >= $curdate){
                $returnArr['Error']['Access_token'] = $data['access_token'];
                $returnArr['Error']['ErrorNumber'] = '00';
                $returnArr['Error']['ErrorMessage'] = 'Authenticated Sucessfully !';  
            }else{// token exppired send error message
                $returnArr['Error']['ErrorNumber'] = '01';
                $returnArr['Error']['ErrorMessage'] = 'Access Token Expired !';
                $returnArr['Error']['Access_token'] = '';
            }
        }
        //print_r($returnArr);exit;
        return $returnArr;
    }

    public function common_select_query($select,$table_name,$where,$result_type){
        if($where == ''){
            $where = '1 = 1';
        }
        $qry = "SELECT {$select} FROM {$table_name} WHERE ".$where;
        //echo $qry;exit;
        if($result_type == 'row_array'){
            return $this->db->query($qry)->row_array();
        }else{
            return $this->db->query($qry)->result_array();
        }
    }
}
