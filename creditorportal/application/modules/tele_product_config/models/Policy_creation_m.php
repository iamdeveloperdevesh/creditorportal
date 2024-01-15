<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Policy_creation_m extends CI_Model {

    public function __construct() {
        //print_r($this->session->userdata('emp_code'));
        parent::__construct();

    }

    function generateRandomString($length = 4) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function create_policy() {
        // print_pre($this->input->post()); exit;

        if(isset($_SESSION['policy_conf_db'])){
            if($_SESSION['policy_conf_db'] == 'axis_retail'){
                $this->db = $this->load->database('axis_retail', true);
            }
        }

        $all = extract($this->input->post(null, true));

        if($this->db->database != "axis_retail"){
            $this->db->update('product_master_with_subtype', [
                'master_policy_no' => $master_policy_no,
                'EW_master_policy_no' => $EW_master_policy_no,
                'HB_source_code' => $HB_source_code,
                'HB_policy_type' => $HB_policy_type,
                'plan_code' => $plan_code,
                'HB_custid_concat_string' => $HB_custid_concat_string,
                'api_url' => $api_url,
                'click_pss_url' => $click_pss_url,
                'click_pss_url' => $click_pss_url,
                'imd_refer_product_code' => $imd_refer_product_code
            ], ['policy_parent_id' => $product_parent_id, 'policy_subtype_id' => $policySubType]);
        }else{
            $this->db->update('product_master_with_subtype', [
                'master_policy_no' => $master_policy_no,
                'HB_source_code' => $HB_source_code,
                'HB_policy_type' => $HB_policy_type,
                'plan_code' => $plan_code,
                'HB_custid_concat_string' => $HB_custid_concat_string,
                'api_url' => $api_url,
                'click_pss_url' => $click_pss_url,
                'payu_info_url' => $payu_info_url
            ], ['policy_parent_id' => $product_parent_id, 'policy_subtype_id' => $policySubType]);
        }




        // $GroupCodeFile = $_FILES['GroupCodeFile']['tmp_name'];
        // $objPHPExcel = PHPExcel_IOFactory::load($GroupCodeFile);

        // foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
        //     //$worksheetTitle = $worksheet->getTitle();
        //     $highestRow = $worksheet->getHighestRow(); // e.g. 10

        //     for ($col = 2; $col <= $highestRow; ++$col) {
        //         $product_code = $worksheet->getCellByColumnAndRow(0, $col);
        //         $group_code = $worksheet->getCellByColumnAndRow(1, $col);
        //         $EW_group_code = $worksheet->getCellByColumnAndRow(2, $col);
        //         $spouse_group_code = $worksheet->getCellByColumnAndRow(3, $col);
        //         $family_construct = $worksheet->getCellByColumnAndRow(4, $col);
        //         $si_per_member = $worksheet->getCellByColumnAndRow(5, $col);
        //         $si_group = $worksheet->getCellByColumnAndRow(6, $col);

        //         if($this->db->database != "axis_retail"){
        //             $this->db->insert('master_group_code', [
        //                 'product_code' => $product_code,
        //                 'group_code' => $group_code,
        //                 'EW_group_code' => $EW_group_code,
        //                 'spouse_group_code' => $spouse_group_code,
        //                 'family_construct' => $family_construct,
        //                 'si_per_member' => $si_per_member,
        //                 'si_group' => $si_group
        //             ]);
        //         }else{
        //             $this->db->insert('master_group_code', [
        //                 'product_code' => $product_code,
        //                 'group_code' => $group_code,
        //                 'spouse_group_code' => $spouse_group_code,
        //                 'family_construct' => $family_construct,
        //                 'si_per_member' => $si_per_member,
        //                 'si_group' => $si_group
        //             ]);
        //         }


        //     }
        // }
        // echo "group code inserted";exit;
        // if combo change siddhi
        if($isComboStatus == 1){
            $this->db->update('product_master_with_subtype', [
                'combo_flag' => 'Y'
            ], ['policy_parent_id' => $product_parent_id, 'policy_subtype_id' => $policySubType]);
        }
        if($isComboStatus == 1)
        {
            $combo_flag = 'Y';
        }
        else
        {
            $combo_flag = 'N';
        }
        //check if comapny is already save else exit

        $company = $this->db->where(['comapny_name' => trim($comanyName)])->get('master_company')->row_array();

        $policy_noCount = $this->db->where(['policy_no' => trim($policyNo)])->get('employee_policy_detail')->num_rows();

        $company_id = '';
        $policy_id = 0;

        if ($company) {
            $company_id = $company['company_id'];

            //check if company charges premium designation wise, age wise, grade wise
            /* if ($companySubTypePolicy != $company['company_premium_type']) {
                 return ['mgs' => "Company Premium type selected differs, charge as per " . $company['company_premium_type'] . " wise given $companySubTypePolicy wise"];
             } */
            // $this->db->where(['company_id' => $company_id])->update('master_company', [
            //     'flex_allocate' => $flex_allocate,
            //     'payroll_allocate' => $payroll_allocate,
            // ]);
        } else {
            $this->db->insert('master_company', [
                "company_premium_type" => $companySubTypePolicy,
                'comapny_name' => trim($comanyName),
                'enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                'enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
            ]);

            $company_id = $this->db->insert_id();
        }
//        $$policy_noCount == '';
        // if (empty($policy_noCount)) {
        if (1) {

            if ($policySubType == 1) {

                $ageLimit = json_decode($ageLimit);

                //    if ($companySubTypePolicy == "designation") {
                //        $designation = json_decode($designation);
                //        $designationId = json_decode($designationId);
                //        $designationId1 = implode(',', $designationId);
                //        for ($i = 0; $i < count($designationId); ++$i) {
                //        $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                //        ->from('employee_policy_detail as epd, master_designation as md')
                //        ->where('policy_sub_type_id',$policySubType)
                //       ->where('company_id',$company_id)
                //       ->where('find_in_set("'.$designationId1[$i].'",epd.applicable_for_designation_id)!=0')
                //                ->where('start_date >=',date('Y-m-d', (strtotime($policyStartDate))))
                //                 ->where('end_date <=',date('Y-m-d', (strtotime($policyEndDate))))
                //                ->get()->result_array();
                //            if ($designationId[$i] == -1) {
                //                $this->db->insert('master_designation', [
                //                    'designation_name' => ucwords(strtolower($designation[$i])),
                //                ]);
                //                $designationId[$i] = $this->db->insert_id();
                //            }
                //        }
                //        $designation = implode(',', $designation);
                //        $designationId = implode(',', $designationId);
                //    }
                // if(count($designation_diff) > 0){
                //                return ['mgs' => 'Already this policy is assigned to this Designation'];
                //    }
                //    else {
                $arrUnMaryChild = [];
                $strUnMaryChild = 0;
                $unmarried_child_contri = 0;

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                if($companySubTypePolicy == "flate"){

                    $si_id = json_decode($si_id);
                    $si_parr = json_decode($si_parr);
                    $tax_Arr = json_decode($tax_Arr);
                    $premium_taxArr = json_decode($premium_taxArr);
                    $sum_insured = implode(',', $si_id);

                    $premium_si = implode(',', $si_parr);
                    $tax = implode(',', $tax_Arr);
                    $premium_with_tax = implode(',', $premium_taxArr);
                }
                /*else {
                 $si_id = json_decode($si_id);
                 $si_parr = json_decode($si_parr);
                 $si_parr1 = json_decode($si_parr1);
                 $sum_insure = implode(',', $si_id);
                 $premium_si = implode(',', $si_parr);
                 $premium_bo = implode(',', $si_parr1);
                } */



                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($policyNo),

                    'broker_id' => $_SESSION['emp_id'],
                    'policy_type_id' => $policyType,
                    'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($premium_with_tax),
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'enrollment_status' => $isEnrollStatus,
                    'mandatory_status' => $isMatoryStatus,
                    'pdf_status' => $pdf_type,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    // 'applicable_for_designation_id' => $designationId,
                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'dbl_premium' => $premium_bo,
                    'si_parent_status' => $si_parents,
                    'both_parent_status' => $bo_parents,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    //'tpa_type' => $tpaType,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Pending',
                    'approval_id' => $emp_code,
                    'product_name'=>$product_name,
                    'parent_policy_id' => $product_parent_id,
                    'proposal_approval'=>$proposal_approval,
                    'customer_search_status'=>$customer_search,
                    'premium_type' => $companySubTypepremiumPolicy,
                    'suminsured_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ]);

                $policy_id = $this->db->insert_id();

                foreach ($ageLimit as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $key,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                    // }
                }
            }
            else if ($policySubType == 8) {

                $ageLimit = json_decode($ageLimit);

                $arrUnMaryChild = [];
                $strUnMaryChild = 0;
                $unmarried_child_contri = 0;

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                if($companySubTypePolicy == "flate"){

                    $si_id = json_decode($si_id);
                    $si_parr = json_decode($si_parr);
                    $tax_Arr = json_decode($tax_Arr);
                    $premium_taxArr = json_decode($premium_taxArr);
                    $sum_insured = implode(',', $si_id);

                    $premium_si = implode(',', $si_parr);
                    $tax = implode(',', $tax_Arr);
                    $premium_with_tax = implode(',', $premium_taxArr);
                }
                /*else {
                 $si_id = json_decode($si_id);
                 $si_parr = json_decode($si_parr);
                 $si_parr1 = json_decode($si_parr1);
                 $sum_insure = implode(',', $si_id);
                 $premium_si = implode(',', $si_parr);
                 $premium_bo = implode(',', $si_parr1);
                } */



                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    'policy_type_id' => $policyType,
                    'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($premium_with_tax),
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'enrollment_status' => $isEnrollStatus,
                    'mandatory_status' => $isMatoryStatus,
                    'pdf_status' => $pdf_type,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    // 'applicable_for_designation_id' => $designationId,
                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'dbl_premium' => $premium_bo,
                    'si_parent_status' => $si_parents,
                    'both_parent_status' => $bo_parents,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    //'tpa_type' => $tpaType,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Pending',
                    'approval_id' => $emp_code,
                    'product_name'=>$product_name,
                    'parent_policy_id' => $product_parent_id,
                    'proposal_approval'=>$proposal_approval,
                    'customer_search_status'=>$customer_search,
                    'premium_type' => $companySubTypepremiumPolicy,
                    'suminsured_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ]);

                $policy_id = $this->db->insert_id();

                foreach ($ageLimit as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $key,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                    // }
                }
            }
            else if ($policySubType == 4) {

                $ageLimit = json_decode($ageLimit);

                // if ($companySubTypePolicy == "designation") {
                //     $designation = json_decode($designation);
                //     $designationId = json_decode($designationId);
                //     $designationId1 = implode(',', $designationId);
                //     for ($i = 0; $i < count($designationId); ++$i) {
                //     $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                //      ->from('employee_policy_detail as epd')
                //      ->where('policy_sub_type_id',$policySubType)
                //     ->where('company_id',$company_id)
                //      ->where('find_in_set("'.$designationId1[$i].'",epd.applicable_for_designation_id)!=0')
                //      ->where('start_date >=',date('Y-m-d', (strtotime($policyStartDate))))
                //      ->where('end_date <=',date('Y-m-d', (strtotime($policyEndDate))))
                //      ->get()->result_array();
                //         if ($designationId[$i] == -1) {
                //             $this->db->insert('master_designation', [
                //                 'designation_name' => ucwords(strtolower($designation[$i])),
                //             ]);
                //             $designationId[$i] = $this->db->insert_id();
                //         }
                //     }
                //     $designation = implode(',', $designation);
                //     $designationId = implode(',', $designationId);
                // }

                if($companySubTypePolicy == "flate"){

                    $si_id = json_decode($si_id);
                    $si_parr = json_decode($si_parr);
                    $tax_Arr = json_decode($tax_Arr);
                    $premium_taxArr = json_decode($premium_taxArr);
                    $sum_insured = implode(',', $si_id);

                    $premium_si = implode(',', $si_parr);
                    $tax = implode(',', $tax_Arr);
                    $premium_with_tax = implode(',', $premium_taxArr);
                }

                // if(count($designation_diff) > 0){
                //             return ['mgs' => 'Already this policy is assigned to this Designation'];
                // }
                // else {

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    'policy_type_id' => $policyType,
                    'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($serviceTax),
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'enrollment_status' => $isEnrollStatus,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    'applicable_for_designation_id' => $designationId,
                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'dbl_premium' => $premium_bo,
                    'si_parent_status' => $si_parents,
                    'both_parent_status' => $bo_parents,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'mandatory_status' => $isMatoryStatus,
                    'pdf_status' => $pdf_type,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Pending',
                    'product_name'=>$product_name,
                    'parent_policy_id' => $product_parent_id,
                    'approval_id' => $emp_code,
                    'proposal_approval'=>$proposal_approval,
                    'customer_search_status'=>$customer_search,
                    'premium_type' => $companySubTypepremiumPolicy,
                    'suminsured_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ]);

                $policy_id = $this->db->insert_id();

                foreach ($ageLimit as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $key,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => 0,
                        'employee_contri' =>0,
                        'employer_contri' =>0,
                    ]);
                }
            }
            else if ($policySubType == 2 || $policySubType == 3) {

                $ageLimit = json_decode($ageLimit);


                // if ($companySubTypePolicy == "designation") {
                //     $designation = json_decode($designation);
                //     $designationId = json_decode($designationId);
                //     $designationId1 = implode(',', $designationId);
                //     for ($i = 0; $i < count($designationId); ++$i) {
                //          $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                //     ->from('employee_policy_detail as epd')
                //     ->where('policy_sub_type_id',$policySubType)
                //    ->where('company_id',$company_id)
                //     ->where('find_in_set("'.$designationId1[$i].'",epd.applicable_for_designation_id)!=0')
                //     ->where('start_date >=',date('Y-m-d', (strtotime($policyStartDate))))
                //     ->where('end_date <=',date('Y-m-d', (strtotime($policyEndDate))))
                //     ->get()->result_array();
                //         if ($designationId[$i] == -1) {
                //             $this->db->insert('master_designation', [
                //                 'designation_name' => ucwords(strtolower($designation[$i])),
                //             ]);
                //             $designationId[$i] = $this->db->insert_id();
                //         }
                //     }
                //     $designation = implode(',', $designation);
                //     $designationId = implode(',', $designationId);
                //     $si_NoOfTimes = json_decode($si_no_times);
                //     $si_parr = json_decode($si_parr);
                // }

                $sum_insure_no = $si_no_times_des;
                // if(count($designation_diff) > 0){
                //              return ['mgs' => 'Already this policy is assigned to this Designation'];
                //  } else {

                $arrUnMaryChild = [];
                $strUnMaryChild = 0;
                $unmarried_child_contri = 0;

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $premiumCalTypeValue = "";
                if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2) {
                    $premiumCalTypeValue = $premiumCalType;
                }

                if($companySubTypePolicy == "flate"){

                    $si_id = json_decode($si_id);
                    $si_parr = json_decode($si_parr);
                    $tax_Arr = json_decode($tax_Arr);
                    $premium_taxArr = json_decode($premium_taxArr);
                    $sum_insured = implode(',', $si_id);

                    $premium_si = implode(',', $si_parr);
                    $tax = implode(',', $tax_Arr);
                    $premium_with_tax = implode(',', $premium_taxArr);
                }

                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    'policy_type_id' => $policyType,
                    'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($serviceTax),
                    'premiumCalType' => trim($premiumCalTypeValue),
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'enrollment_status' => $isEnrollStatus,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    'mandatory_status' => $isMatoryStatus,
                    'pdf_status' => $pdf_type,
                    'applicable_for_designation_id' => $designationId,
                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'gpa_no_of_times' => $sum_insure_no,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Pending',
                    'product_name'=>$product_name,
                    'parent_policy_id' => $product_parent_id,
                    'approval_id' => $emp_code,
                    'proposal_approval'=>$proposal_approval,
                    'customer_search_status'=>$customer_search,
                    'premium_type' => $companySubTypepremiumPolicy,
                    'suminsured_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ]);

                $policy_id = $this->db->insert_id();

                /*if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2) {
                    $this->insertGpa_permili_calculation($policy_id);
                } */

                foreach ($ageLimit as $key => $value) {

                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $key,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                }
            }
            else if ($policySubType == 5 || $policySubType == 6) {

                $ageLimit = json_decode($ageLimit);

                // if ($companySubTypePolicy == "designation") {
                //     $designation = json_decode($designation);
                //     $designationId = json_decode($designationId);
                //     $designationId1 = implode(',', $designationId);
                //     for ($i = 0; $i < count($designationId); ++$i) {
                //     $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                //     ->from('employee_policy_detail as epd')
                //     ->where('policy_sub_type_id',$policySubType)
                //              ->where('company_id',$company_id)
                //     ->where('find_in_set("'.$designationId1[$i].'",epd.applicable_for_designation_id)!=0')
                //             ->where('start_date >=',date('Y-m-d', (strtotime($policyStartDate))))
                //              ->where('end_date <=',date('Y-m-d', (strtotime($policyEndDate))))
                //             ->get()->result_array();
                //         if ($designationId[$i] == -1) {
                //             $this->db->insert('master_designation', [
                //                 'designation_name' => ucwords(strtolower($designation[$i])),
                //             ]);
                //             $designationId[$i] = $this->db->insert_id();
                //         }
                //     }
                //     $designation = implode(',', $designation);
                //     $designationId = implode(',', $designationId);
                // }

                $si_id = json_decode($si_id);
                $si_parr = json_decode($si_parr);
                $sum_insured = implode(',', $si_id);
                $premium_si = implode(',', $si_parr);

                $arrUnMaryChild = [];
                $strUnMaryChild = 0;
                $unmarried_child_contri = 0;
                // if(count($designation_diff) > 0){
                //             return ['mgs' => 'Already this policy is assigned to this Designation'];
                // } else {

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($serviceTax),
                    'policy_type_id' => $policyType,
                    'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    //'TPA_id' => $TPA_id,
                    'mandatory_status' => $isMatoryStatus,
                    'pdf_status' => $pdf_type,
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'enrollment_status' => $isEnrollStatus,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    'applicable_for_designation_id' => $designationId,
                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Pending',
                    'product_name'=>$product_name,
                    'parent_policy_id' => $product_parent_id,
                    'approval_id' => $emp_code,
                    'proposal_approval'=>$proposal_approval,
                    'customer_search_status'=>$customer_search,
                    'premium_type' => $companySubTypepremiumPolicy,
                    'suminsured_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ]);

                $policy_id = $this->db->insert_id();

                foreach ($ageLimit as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $key,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                }
            }
            else {

                $filename = $_FILES['filename']['tmp_name'];

                if (!$filename) {
                    return ['mgs' => 'Please add 1 file to create policy'];
                }

                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    'policy_type_id' => $policyType,
                    'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sum_insured_type,
                    'policy_status' => 'Pending',
                    'product_name'=>$product_name,
                    'parent_policy_id' => $product_parent_id,
                    'approval_id' => $emp_code,
                    'premium_type' => $companySubTypepremiumPolicy,
                    'suminsured_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ]);

                $policy_id = $this->db->insert_id();
            }

            // payment mode insert in policy
            $payment = explode(",",$payment_mode);
            $customer_search_field_id = explode(",",$customer_search_field);

            for($i = 0; $i<count($payment); $i++){

                $this->db->insert('policy_payment_customer_mapping', [
                    'policy_id' => $policy_id,
                    'mapping_id' => $payment[$i],
                    'type' => 'P',
                    'status' => 'Active',

                ]);
            }
            for($i = 0; $i<count($customer_search_field_id); $i++){

                $this->db->insert('policy_payment_customer_mapping', [
                    'policy_id' => $policy_id,
                    'mapping_id' => $customer_search_field_id[$i],
                    'type' => 'C',
                    'status' => 'Active',

                ]);
            }



            // insert data in cd balance table for cd balance check
            $this->db->insert('cd_balance_transaction_log', [
                'policy_detail_id' => $policy_id,
                'amount' => $premium_cd_paid,
                'transaction_type' => 'cr',
                'created_date' => date('Y-m-d H:i:s'),
            ]);

            //after creation of policy get id
            $relationship_id = '0';

            $familyConstructArr = explode(',', $familyConstruct);
            $family_cons_relArr = explode(',', $family_cons_rel);
            if ($familyConstructArr[0] == 3) {
                $familyConstructArr[0] = 2;
            }
            if ($familyConstructArr[0] == 4 && $familyConstructArr[1] == 0) {
                $familyConstructArr[0] = 4;
            }
            if ($familyConstructArr[0] == 1) {

            } elseif ($familyConstructArr[0] > 3) {
                $relationship_id = '1,4,5,6,7';
            } else {
                $relationship_id .= ',1';
            }

            if ($familyConstructArr[1] > 0) {
                $relationship_id .= ',2,3';
            }
            $family_cons_rel = rtrim($family_cons_rel, ',');
            $this->db->insert('master_broker_ic_relationship', [
                'policy_id' => $policy_id,
                // 'relationship_id' => $family_cons_rel,
                'relationship_id' => $relationship_id,
                'max_adult' => $familyConstructArr[0],
                'max_child' => $familyConstructArr[1],
                'twins_child_limit' => $twins_child_limit,
            ]);


            $this->policyCreationSaveExcel($policy_id, $policySubType);

            // if ($fileUploadTypes == "tpaFile") {

            //     $this->policyCreationSaveExcel($policy_id, $policySubType);
            // }

            // if ($fileUploadTypegtl == "gtliTopupFile") {
            //     $this->policyCreationSaveExcel($policy_id, $policySubType);
            // }
            // if ($policySubType == 1) {

            //     $this->policyCreationSaveExcel($policy_id, $policySubType);
            // } else {

            //     $this->policyCreationSaveExcel($policy_id, $policySubType);
            // }
            return ['mgs' => 'Policy Successfully created','combo_flag' => $combo_flag,'SumInsuredType' => $companySubTypePolicy];
        } else {
            return ['mgs' => 'duplicate policy'];
        }
    }

    function insertGpa_permili_calculation($policy_id) {
        extract($this->input->post(null, true));

        $this->load->library("excel");
        if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2 && $premiumCalType == 1 && $policy_id != "") {
            $filename = $_FILES['filename']['tmp_name'];
            $objPHPExcel = PHPExcel_IOFactory::load($filename);

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                //$worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($col = 2; $col <= $highestRow; ++$col) {
                    $emp_age = $worksheet->getCellByColumnAndRow(0, $col);
                    $gpaRate = $worksheet->getCellByColumnAndRow(1, $col);

                    $this->db->insert('gpa_permili_calculation', [
                        'emp_age' => $emp_age,
                        'gtli_rate' => $gpaRate,
                        'policy_detail_id' => $policy_id
                    ]);
                }
            }
        } else {
            echo "error";
        }
    }
    function insertBatchData($tbl_name,$data_array,$sendid = NULL)
    {
        $this->db->insert_batch($tbl_name,$data_array);
        $result_id = $this->db->insert_id();

        /*echo $result_id;
        exit;*/

        if($sendid == 1)
        {
            //return id
            return $result_id;
        }
    }

    function policyCreationSaveExcel($policy_id, $policySubType) {



        extract($this->input->post(null, true));


        $this->load->library("excel");
        //    var_dump($_POST);exit;

        if ($fileUploadType) {

            $filename = $_FILES['filename']['tmp_name'];
            $premiumfilename = $_FILES['premiumfilename']['tmp_name'];



            if (!$filename) {

                return ['mgs' => 'Please add 1 file to create policy'];
            }

            $objPHPExcel = PHPExcel_IOFactory::load($filename);
            //$objPHPExcel1 = PHPExcel_IOFactory::load($premiumfilename);

            if($fileUploadType == 'byAge') {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    /* $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                      $nrColumns = ord($highestColumn) - 64;
                      $total_rows = $highestRow - 1; */

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/age_wise_xl.xls');
                            $this->db->insert('policy_creation_age', [
                                'policy_id' => $policy_id,
                                'policy_age' => $policyAge,
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'premium_tax' => $tax,
                                'premium_with_tax' => $premium_with_tax,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/age_wise_xl.xls'
                            ]);
                            // echo "here";exit;
                        } else {
                            return false;
                        }
                    }
                }
            } else if($fileUploadType == 'byMemberAge') {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10


                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/mem_age_wise_xl.xls');
                            $this->db->insert('policy_creation_age', [
                                'policy_id' => $policy_id,
                                'policy_age' => $policyAge,
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'premium_tax' => $tax,
                                'premium_with_tax' => $premium_with_tax,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/mem_age_wise_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }
            } else if($fileUploadType == 'byDesignation') {


                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    //print_pre($highestRow);

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policydesignation = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);
                        //print_pre();exit();
                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);

                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
//                         print_pre(trim($premium));
//                         print_pre($premium_with_tax);exit();
                        // if ($premium_with_tax >= $premium) {
                        if ($sumInsured) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/designation_wise_xl.xls');
                            $designation = $this->db->where(['designation_name' => trim($policydesignation)])->get('master_designation')->row_array();
                            $designationId = $designation['master_desg_id'];

                            if ($designation == "") {
                                $this->db->insert('master_designation', [
                                    'designation_name' => ucwords(strtolower($policydesignation)),
                                ]);
                                $designationId = $this->db->insert_id();
                            }


                            $this->db->insert('policy_creation_designation', [
                                'policy_id' => $policy_id,
                                'policy_designation' => $designationId,
                                'sum_insured' => trim($sumInsured),
                                // 'premium' => $premium,
                                'policy_desig_tax' => trim($tax),
                                'policy_desig_premiumWithtax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/designation_wise_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }


            }

            else if($fileUploadType == 'byFamilyConstruct'){
                //echo "IN byFamilyConstruct";exit;
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $family_type = $worksheet->getCellByColumnAndRow(0, $col);
                        $family_types = explode("+",$family_type);

                        if(count($family_types) > 1){
                            $family_child=  $family_types[1][0];
                        }
                        else {
                            $family_child = 0;
                        }

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);
                        $premium = $worksheet->getCellByColumnAndRow(2, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);

                        if ($premium_with_tax >= $premium) {

                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/age_wise_xl.xls');
                            $this->db->insert('family_construct_wise_si', [
                                'policy_detail_id' => $policy_id,
                                'family_type' => $family_type,
                                'sum_insured' => $sumInsured,
                                'adult'=>$family_types[0][0],
                                'child'=>$family_child,
                                'premium' => $premium,
                                'policy_family_tax' => trim($tax),
                                'PremiumServiceTax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/family_construct_xl.xls'
                            ]);

                        } else {
                            return false;
                        }
                    }
                }
            }
            else if($fileUploadType == 'byFamilyAgeConstruct'){

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $family_type = $worksheet->getCellByColumnAndRow(0, $col);
                        $family_types = explode("+",$family_type);

                        if(count($family_types) > 1){
                            $family_child=  $family_types[1][0];
                        }
                        else {
                            $family_child = 0;
                        }

                        $age_group = $worksheet->getCellByColumnAndRow(1, $col);
                        $sumInsured = $worksheet->getCellByColumnAndRow(2, $col);
                        $premium = $worksheet->getCellByColumnAndRow(3, $col);
                        $tax = $worksheet->getCellByColumnAndRow(4, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(5, $col);

                        if ($premium_with_tax >= $premium) {

                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/family_age_wise_xl.xls');
                            $this->db->insert('family_construct_age_wise_si', [
                                'policy_detail_id' => $policy_id,
                                'family_type' => $family_type,
                                'age_group' => $age_group,
                                'sum_insured' => $sumInsured,
                                'adult'=>$family_types[0][0],
                                'child'=>$family_child,
                                'premium' => $premium,
                                'policy_family_tax' => trim($tax),
                                'PremiumServiceTax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/family_age_construct_xl.xls'
                            ]);

                        } else {
                            return false;
                        }
                    }
                }
            }
            else {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    /* $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                      $nrColumns = ord($highestColumn) - 64;
                      $total_rows = $highestRow - 1; */

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyGrade = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        // $relation = $worksheet->getCellByColumnAndRow(3, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $s_premium = $worksheet->getCellByColumnAndRow(3, $col);
                        $son_premium = $worksheet->getCellByColumnAndRow(4, $col);
                        $d_premium = $worksheet->getCellByColumnAndRow(5, $col);
                        $f_premium = $worksheet->getCellByColumnAndRow(6, $col);
                        $m_premium = $worksheet->getCellByColumnAndRow(7, $col);
                        $f_inlow_premium = $worksheet->getCellByColumnAndRow(8, $col);
                        $m_inlow_premium = $worksheet->getCellByColumnAndRow(9, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/grade_wise_xl.xls');

                        if ($policySubType == 2 || $policySubType == 3) {
                            $this->db->insert('grade_permilli', [
                                'policy_id' => $policy_id,
                                'grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'per_mili' => $premium,
                                'file_path' => 'application/resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        } else {
                            $this->db->insert('policy_creation_grade', [
                                'policy_id' => $policy_id,
                                'policy_grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                //'relation' => $relation,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'spouse_premium' => $s_premium,
                                'son_premium' => $son_premium,
                                'daughter_premium' => $d_premium,
                                'mother_premium' => $m_premium,
                                'father_premium' => $f_premium,
                                'mother_in_premium' => $m_inlow_premium,
                                'father_in_premium' => $f_inlow_premium,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        }
                    }
                }
            }




            if ($fileUploadTypegtl == 'gtliTopupFile') {

                // $filenamegtl = $_FILES['filenamegtl']['tmp_name'];

                // if (!$filenamegtl) {
                // return ['mgs' => 'Please add 1 file to create TPA policy'];
                // }
                // $objPHPExcel = PHPExcel_IOFactory::load($filenamegtl);


                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $tpa_arr = [];
                    for ($col = 2; $col <= $highestRow; ++$col) {

                        $emp_age = $worksheet->getCellByColumnAndRow(1, $col);
                        $gtli_rate = $worksheet->getCellByColumnAndRow(2, $col);
                        $ci_rate = $worksheet->getCellByColumnAndRow(3, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy_gtl/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy_gtl/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy_gtl/' . $policy_id . '/gtli_wise_xl.xls');

                        $this->db->insert('gtli_topup_premium_calc', [
                            'emp_age' => $$emp_age,
                            'gtli_rate' => $gtli_rate,
                            'ci_rate' => $ci_rate,
                            'policy_detail_id' => $policy_id,
                            'file_path' => '/application/resources/policy_gtl/' . $policy_id . '/grade_wise_xl.xls'
                        ]);
                    }
                }
            }
        }

        ///premium

        // if (!$premiumfilename) {
        // return ['mgs' => 'Please add 1 file to create policy'];
        // }
        if ($premiumfileUploadType != '') {

            $premiumfilename = $_FILES['premiumfilename']['tmp_name'];

            if (!$premiumfilename) {
                return ['mgs' => 'Please add 1 file to create policy'];
            }

            $objPHPExcel1 = PHPExcel_IOFactory::load($premiumfilename);

            if ($premiumfileUploadType == 'byAge') {

                foreach ($objPHPExcel1->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    /* $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                      $nrColumns = ord($highestColumn) - 64;
                      $total_rows = $highestRow - 1; */

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        /// $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(1, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(2, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(3, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy_premium/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy_premium/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy_premium/' . $policy_id . '/age_wisepremium_xl.xls');
                            $this->db->insert('policy_creation_age_bypremium', [
                                'policy_id' => $policy_id,
                                'policy_age' => $policyAge,
                                // 'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'premium_tax' => $tax,
                                'premium_with_tax' => $premium_with_tax,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'file_path' => '/application/resources/policy_premium/' . $policy_id . '/age_wisepremium_xl.xls'
                            ]);
                            // echo "here";exit;
                        } else {
                            return false;
                        }
                    }
                }
            }
            else if ($premiumfileUploadType == 'byMemberAge') {

                foreach ($objPHPExcel1->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10


                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        //$sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(1, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(2, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(3, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy_premium/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy_premium/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy_premium/' . $policy_id . '/mem_age_wisepremium_xl.xls');
                            $this->db->insert('policy_creation_age_bypremium', [
                                'policy_id' => $policy_id,
                                'policy_age' => $policyAge,
                                // 'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'premium_tax' => $tax,
                                'premium_with_tax' => $premium_with_tax,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'file_path' => '/application/resources/policy_premium/' . $policy_id . '/mem_age_wisepremium_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }
            }
            else if ($premiumfileUploadType == 'byDesignation') {

                foreach ($objPHPExcel1->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10


                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policydesignation = $worksheet->getCellByColumnAndRow(0, $col);
                        $premium = $worksheet->getCellByColumnAndRow(1, $col);
                        $tax = $worksheet->getCellByColumnAndRow(2, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(3, $col);

                        if ($premium_with_tax >= $premium) {

                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel5');

                            if (!file_exists(APPPATH . 'resources/policy_premium/' . $policy_id)) {

                                mkdir(APPPATH . 'resources/policy_premium/' . $policy_id, 0777, true);

                            }

                            $writer->save(APPPATH . 'resources/policy_premium/' . $policy_id . '/designation_wise_bypremium_xl.xls');
                            $designation = $this->db->where(['designation_name' => trim($policydesignation)])->get('master_designation')->row_array();
                            $designationId = $designation['master_desg_id'];

                            if ($designation == "") {
                                $this->db->insert('master_designation', [
                                    'designation_name' => ucwords(strtolower($policydesignation)),
                                ]);
                                $designationId = $this->db->insert_id();
                            }


                            $this->db->insert('policy_creation_designation_bypremium', [
                                'policy_id' => $policy_id,
                                'policy_designation' => $designationId,
                                'premium' => trim($premium),
                                'policy_desig_tax' => trim($tax),
                                'policy_desig_premiumWithtax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy_premium/' . $policy_id . '/designation_wise_bypremium_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }

            } else {

                foreach ($objPHPExcel1->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    /* $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                      $nrColumns = ord($highestColumn) - 64;
                      $total_rows = $highestRow - 1; */

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyGrade = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        // $relation = $worksheet->getCellByColumnAndRow(3, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $s_premium = $worksheet->getCellByColumnAndRow(3, $col);
                        $son_premium = $worksheet->getCellByColumnAndRow(4, $col);
                        $d_premium = $worksheet->getCellByColumnAndRow(5, $col);
                        $f_premium = $worksheet->getCellByColumnAndRow(6, $col);
                        $m_premium = $worksheet->getCellByColumnAndRow(7, $col);
                        $f_inlow_premium = $worksheet->getCellByColumnAndRow(8, $col);
                        $m_inlow_premium = $worksheet->getCellByColumnAndRow(9, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/grade_wisepremium_xl.xls');

                        if ($policySubType == 2 || $policySubType == 3) {
                            $this->db->insert('grade_permilli', [
                                'policy_id' => $policy_id,
                                'grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'per_mili' => $premium,
                                'file_path' => 'application/resources/policy/' . $policy_id . '/grade_wisepremium_xl.xls'
                            ]);
                        } else {
                            $this->db->insert('policy_creation_grade', [
                                'policy_id' => $policy_id,
                                'policy_grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                //'relation' => $relation,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'spouse_premium' => $s_premium,
                                'son_premium' => $son_premium,
                                'daughter_premium' => $d_premium,
                                'mother_premium' => $m_premium,
                                'father_premium' => $f_premium,
                                'mother_in_premium' => $m_inlow_premium,
                                'father_in_premium' => $f_inlow_premium,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        }
                    }
                }
            }


        }
    }

    public function get_family_relation() {
        $data = $this->db
            ->select('*')
            ->from('master_family_relation')
            ->get()
            ->result_array();
        return $data;
    }

    public function get_policy($policy_id) {
        $data = $this->db
            ->select('*')
            ->from('employee_policy_detail as epd, master_company as mc, master_broker_ic_relationship as mbir,master_insurance_companies as mic, master_policy_sub_type as mpst, master_policy_type as mpt')
            ->where('epd.company_id =  mc.company_id')
            ->where('epd.policy_detail_id =  mbir.policy_id')
            ->where('epd.insurer_id =  mic.insurer_id')
            ->where('epd.policy_sub_type_id =  mpst.policy_sub_type_id')
            ->where('epd.policy_type_id =  mpt.policy_type_id')
            ->where('epd.policy_status', 'Pending')
            ->where('epd.policy_detail_id', $policy_id)
            ->group_by('epd.policy_detail_id')
            ->get()
            ->result_array();
        if ($data[0]['premium_type'] == "age" || $data[0]['premium_type'] == "memberAge") {
            $data = $this->db
                ->select('*')
                ->from('employee_policy_detail as epd,policy_creation_age as pcg, master_company as mc, master_broker_ic_relationship as mbir,master_insurance_companies as mic, master_policy_sub_type as mpst, master_policy_type as mpt')
                ->where('epd.policy_detail_id =  pcg.policy_id')
                ->where('epd.company_id =  mc.company_id')
                ->where('epd.policy_detail_id =  mbir.policy_id')
                ->where('epd.insurer_id =  mic.insurer_id')
                ->where('epd.policy_sub_type_id =  mpst.policy_sub_type_id')
                ->where('epd.policy_type_id =  mpt.policy_type_id')
                ->where('epd.policy_status', 'Pending')
                ->where('epd.policy_detail_id', $policy_id)
                ->group_by('epd.policy_detail_id')
                ->get()
                ->result_array();
        }

        if ($data[0]['premium_type'] == "designation") {
            $data = $this->db
                ->select('*')
                ->from('employee_policy_detail as epd,  policy_creation_designation as pcg,master_company as mc, master_broker_ic_relationship as mbir,master_insurance_companies as mic, master_policy_sub_type as mpst, master_policy_type as mpt')
                ->where('epd.policy_detail_id =  pcg.policy_id')
                ->where('epd.company_id =  mc.company_id')
                ->where('epd.policy_detail_id =  mbir.policy_id')
                ->where('epd.insurer_id =  mic.insurer_id')
                ->where('epd.policy_sub_type_id =  mpst.policy_sub_type_id')
                ->where('epd.policy_type_id =  mpt.policy_type_id')
                ->where('epd.policy_status', 'Pending')
                ->where('epd.policy_detail_id', $policy_id)
                ->group_by('epd.policy_detail_id')
                ->get()
                ->result_array();
        }
        if ($data[0]['premium_type'] == "grade") {
            $data = $this->db
                ->select('*')
                ->from('employee_policy_detail as epd,  policy_creation_grade as pcg,master_company as mc, master_broker_ic_relationship as mbir,master_insurance_companies as mic, master_policy_sub_type as mpst, master_policy_type as mpt')
                ->where('epd.policy_detail_id =  pcg.policy_id')
                ->where('epd.company_id =  mc.company_id')
                ->where('epd.policy_detail_id =  mbir.policy_id')
                ->where('epd.insurer_id =  mic.insurer_id')
                ->where('epd.policy_sub_type_id =  mpst.policy_sub_type_id')
                ->where('epd.policy_type_id =  mpt.policy_type_id')
                ->where('epd.policy_status', 'Pending')
                ->where('epd.policy_detail_id', $policy_id)
                ->group_by('epd.policy_detail_id')
                ->get()
                ->result_array();
        }

//
//        if($data[0]['premium_type'] == "age"){
//            $data[0]["file_path"] =  "/application/resources/policy/".$policy_id."/age_wise.xls";
//        }
//        else if($data[0]['premium_type'] == "grade"){
//            $data[0]["file_path"] =   "/application/resources/policy/".$policy_id."/grade_wise_xl.xls";
//        }
//        else if($data[0]['premium_type'] == "memberAge"){
//            $data[0]["file_path"] =   "/application/resources/policy/".$policy_id."/mem_age_wise_xl.xls";
//        }
//        else {
//             $data[0]["file_path"] =   "/application/resources/policy/".$policy_id."/gtli_wise_xl.xls";
//        }

        return $data;
    }

    public function get_policy_additional_data($policy_id) {
        $data = $this->db
            ->select('*')
            ->from('employee_policy_detail as epd, policy_age_limit as pal')
            ->where('epd.policy_detail_id  =  pal.policy_detail_id')
            ->where('epd.policy_detail_id', $policy_id)
            ->get()
            ->result_array();

        return $data;
    }
    function save_tem_data(){

        extract($this->input->post(null, true));
        if($database_name == 'axis_retail'){
            $this->db = $this->load->database('axis_retail', true);
            $_SESSION['policy_conf_db'] = 'axis_retail';
        }else{
            $_SESSION['policy_conf_db'] = '';
        }
        $policy_subtypes_id = explode(',',$policy_subtypes_id);
        //  echo $this->db;exit;
        $data1 = $this->db->select("*")
            ->from('product_master_with_subtype')
            ->where('product_master_with_subtype.product_name', trim($product_name))
            ->get()
            ->result_array();

        if(count($data1) < 1){


            $test = $this->generateRandomString() . uniqid();
            for($i=0; $i<count($policy_subtypes_id); $i++){
                $this->db->insert('product_master_with_subtype', [
                    'product_name' => trim($product_name),
                    'policy_parent_id' => $test,
                    'policy_subtype_id' => $policy_subtypes_id[$i],
                    'product_code' => $product_code,
                    'policy_type_id'=>$policy_types_id
                ]);
            }

            $id = $this->db->insert_id();
            if($id){
                $data['subtype'] = $this->db->select("*")
                    ->from('product_master_with_subtype , master_policy_sub_type ')
                    ->where('product_master_with_subtype.policy_subtype_id = master_policy_sub_type.policy_sub_type_id')
                    ->where('product_master_with_subtype.policy_parent_id', $test)
                    ->get()
                    ->result_array();


            }



            $GroupCodeFile = $_FILES['GroupCodeFile']['tmp_name'];
            $objPHPExcel = PHPExcel_IOFactory::load($GroupCodeFile);
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                //$worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($col = 2; $col <= $highestRow; ++$col) {
                    $product_code = $worksheet->getCellByColumnAndRow(0, $col);
                    $group_code = $worksheet->getCellByColumnAndRow(1, $col);
                    $EW_group_code = $worksheet->getCellByColumnAndRow(2, $col);
                    $spouse_group_code = $worksheet->getCellByColumnAndRow(3, $col);
                    $family_construct = $worksheet->getCellByColumnAndRow(4, $col);
                    $si_per_member = $worksheet->getCellByColumnAndRow(5, $col);
                    $si_group = $worksheet->getCellByColumnAndRow(6, $col);

                    if($this->db->database != "axis_retail"){
                        $this->db->insert('master_group_code', [
                            'product_code' => $product_code,
                            'group_code' => $group_code,
                            'EW_group_code' => $EW_group_code,
                            'spouse_group_code' => $spouse_group_code,
                            'family_construct' => $family_construct,
                            'si_per_member' => $si_per_member,
                            'si_group' => $si_group
                        ]);
                    }else{
                        $this->db->insert('master_group_code', [
                            'product_code' => $product_code,
                            'group_code' => $group_code,
                            'spouse_group_code' => $spouse_group_code,
                            'family_construct' => $family_construct,
                            'si_per_member' => $si_per_member,
                            'si_group' => $si_group
                        ]);
                    }


                }
            }

            $query = 'SELECT a.id
        FROM product_master_with_subtype a
        WHERE a.policy_parent_id = "'.$test.'" '
            ;


            $query = $this->db->query($query);
            $get_data = $query->row_array();
            // echo $this->db->last_query();exit;
            // print_r($get_data);exit;
            $p_product_id = $get_data["id"];
            // $p_policy_detail_id = $get_data["policy_detail_id"];

            $PDeclaration = $_FILES['PDeclaration']['tmp_name'];
            $objPHPExcel = PHPExcel_IOFactory::load($PDeclaration);
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                //$worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($col = 2; $col <= $highestRow; ++$col) {
                    $content = $worksheet->getCellByColumnAndRow(0, $col);
                    $label = $worksheet->getCellByColumnAndRow(1, $col);
                    $proposal_continue = $worksheet->getCellByColumnAndRow(2, $col);
                    $is_remark = $worksheet->getCellByColumnAndRow(3, $col);
                    $is_answer = $worksheet->getCellByColumnAndRow(4, $col);

                    if($this->db->database != "axis_retail"){
                        $this->db->insert('policy_declaration', [
                            'product_id' => $p_product_id,
                            'parent_policy_id' => $test,
                            'policy_detail_id' => 0,
                            'content' => $content,
                            'label' => $label,
                            'proposal_continue' => $proposal_continue,
                            'created_date' => date('Y-m-d H:i:s'),
                            'modified_date' => date('Y-m-d H:i:s'),
                            'created_by' => $_SESSION['emp_id'],
                            'is_remark' => $is_remark,
                            'is_answer' => $is_answer,
                        ]);
                    }else{
                        $this->db->insert('policy_declaration', [
                            'product_id' => $p_product_id,
                            'parent_policy_id' => $test,
                            'policy_detail_id' => 0,
                            'content' => $content,
                            'label' => $label,
                            'proposal_continue' => $proposal_continue,
                            'created_date' => date('Y-m-d H:i:s'),
                            'modified_date' => date('Y-m-d H:i:s'),
                            'created_by' => $_SESSION['emp_id']
                        ]);

                    }



                }
            }

            return $data;
        }
        else {
            return false;
        }

    }
    function update_policy() {

        extract($this->input->post(null, true));
        //check if comapny is already save else exit
        $company = $this->db->where(['comapny_name' => trim($comanyName)])->get('master_company')->row_array();

        $policy_noCount = $this->db->where(['policy_no' => trim($policyNo)])->get('employee_policy_detail')->num_rows();

        $company_id = '';
        $policy_id = 0;
        $company_id = $company['company_id'];



        //check policy sub Type = group mediclaim

        if ($policySubType == 1) {

            $ageLimit = json_decode($ageLimit);

            // if ($companySubTypePolicy == "designation") {
            //     $designation = json_decode($designation);
            //     $designationId = json_decode($designationId);
            //     $designationId1 = implode(',', $designationId);
            //     for ($i = 0; $i < count($designationId); ++$i) {
            //     $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
            //     ->from('employee_policy_detail as epd, master_designation as md')
            //     ->where('policy_sub_type_id',$policySubType)
            //    ->where('company_id',$company_id)
            //    ->where('find_in_set("'.$designationId1[$i].'",epd.applicable_for_designation_id)!=0')
            //             ->where('start_date >=',date('Y-m-d', (strtotime($policyStartDate))))
            //              ->where('end_date <=',date('Y-m-d', (strtotime($policyEndDate))))
            //             ->get()->result_array();
            //         if ($designationId[$i] == -1) {
            //             $this->db->insert('master_designation', [
            //                 'designation_name' => ucwords(strtolower($designation[$i])),
            //             ]);
            //             $designationId[$i] = $this->db->insert_id();
            //         }
            //         $designation[$i] = $this->db->get_where('master_designation', array('designation_name =' => $designation[$i]))->result();
            //         }
            //         $data_des = [];
            //         foreach ($designation as $d){
            //            $designation = ((array)$d[0]);
            //            array_push($data_des, $designation['master_desg_id']);
            //         }
            //     $designation = implode(',', $designation);
            //     $designationId = implode(',', $data_des);
            // }
            // if(count($designation_diff) > 0){
            //                return ['mgs' => 'Already this policy is assigned to this Designation'];
            //    }
            //    else {
            $arrUnMaryChild = [];
            $strUnMaryChild = 0;
            $unmarried_child_contri = 0;

            if ($isunMaryChildCheck == 1) {
                $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                if ($isDaughterCheck == 1) {
                    array_push($arrUnMaryChild, 3);
                }

                if ($isSonCheck == 1) {
                    array_push($arrUnMaryChild, 2);
                }

                $strUnMaryChild = implode(',', $arrUnMaryChild);
            }

            $special_child_contri = 0;
            if ($isSpChildCheck == 1) {
                $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
            }

            $this->db->update('employee_policy_detail', [
                'policy_no' => trim($policyNo),
                'broker_id' => $_SESSION['emp_id'],
                //'policy_type_id' => $policyType,
                //'policy_sub_type_id' => $policySubType,
                'insurer_id' => $masterInsurance,
                'company_id' => $company_id,
                'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                'sales_manager_name' => trim($salesManager),
                'broker_percent' => trim($brokerPer),
                'tax' => trim($tax),
                'PremiumServiceTax' => trim($serviceTax),
                'sum_insured_type' => $sum_insured_type,
                'special_child_check' => $isSpChildCheck,
                'special_child_contri' => $special_child_contri,
                'unmarried_child_check' => $isunMaryChildCheck,
                'unmarried_child_contri' => $unmarried_child_contri,
                'unmarried_child_cover' => $strUnMaryChild,
                'applicable_for' => $appFor,
                // 'applicable_for_designation_id' => $designationId,
                'sum_insured' => $sumInsured,
                'premium' => $sumPremium,
                'flex_allocate' => $flex_allocate,
                'payroll_allocate' => $payroll_allocate,
                'addition_premium' => $additionalPremium,
                'marital_status' => $ismaritalStatus,
                'status_wise_single_si' => $singleStatus_si,
                'status_wise_single_pre' => $singleStatus_pre,
                'status_wise_married_si' => $marriedStatus_si,
                'status_wise_married_pre' => $marriedStatus_pre,
                'premium_paid' => $premium_cd_paid,
                'cd_balance_threshold' => $cd_balance_thres,
                'hr_email' => $hr_email,
                'hr_contact' => $hr_mobil_no,
                'acc_manager_email' => $account_email,
                'acc_manager_contact' => $acc_mobil_no,
                'policy_status' => 'Active',
                'approval_id' => $this->session->userdata('emp_code'),
                'premium_type' => $companySubTypePolicy,
                'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
            ], ['policy_detail_id' => $policydetailid]);



            $this->db->delete('policy_age_limit', ['policy_detail_id' => $policydetailid]);
            foreach ($ageLimit as $key => $value) {

                $this->db->insert('policy_age_limit', [
                    'policy_detail_id' => $policydetailid,
                    'relation_id' => $key,
                    'min_age' => $value->min,
                    'max_age' => $value->max,
                    'premium' => 0,
                    'employee_contri' =>0,
                    'employer_contri' => 0,
                ]);
            }
            // }
        } else if ($policySubType == 4) {

            $ageLimit = json_decode($ageLimit);

            if ($companySubTypePolicy == "designation") {
                $designation = json_decode($designation);
                $designationId = json_decode($designationId);
                $designationId1 = implode(',', $designationId);
                for ($i = 0; $i < count($designationId); ++$i) {
                    $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                        ->from('employee_policy_detail as epd')
                        ->where('policy_sub_type_id', $policySubType)
                        ->where('company_id', $company_id)
                        ->where('find_in_set("' . $designationId1[$i] . '",epd.applicable_for_designation_id)!=0')
                        ->where('start_date >=', date('Y-m-d', (strtotime($policyStartDate))))
                        ->where('end_date <=', date('Y-m-d', (strtotime($policyEndDate))))
                        ->get()->result_array();

                    if ($designationId[$i] == -1) {
                        $this->db->insert('master_designation', [
                            'designation_name' => ucwords(strtolower($designation[$i])),
                        ]);

                        $designationId[$i] = $this->db->insert_id();
                    }
                    $designation[$i] = $this->db->get_where('master_designation', array('designation_name =' => $designation[$i]))->result();
                }
            }

            $data_des = [];
            foreach ($designation as $d) {
                $designation = ((array) $d[0]);
                array_push($data_des, $designation['master_desg_id']);
            }

            $designation = implode(',', $designation);
            $designationId = implode(',', $data_des);


            $si_id = json_decode($si_id);

            $si_parr = json_decode($si_parr);
            $sum_insure = implode(',', $si_id);
            $premium_si = implode(',', $si_parr);
            /* $emp_pre = implode(',', $si_earr);
              $employer_pre = implode(',', $si_eplrarr); */

            $arrUnMaryChild = [];
            $strUnMaryChild = 0;
            $unmarried_child_contri = 0;
            if (count($designation_diff) > 0) {
                return ['mgs' => 'Already this policy is assigned to this Designation'];
            } else {

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $this->db->update('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    //'policy_type_id' => $policyType,
                    //'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    'si_parent_status' => $si_parents,
                    'both_parent_status' => $bo_parents,
                    //  'tax' => trim($tax),
                    // 'PremiumServiceTax' => trim($serviceTax),
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    'applicable_for_designation_id' => $designationId,
                    'sum_insured' => $sum_insure,
                    'premium' => $premium_si,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Active',
                    'approval_id' => $this->session->userdata('emp_code'),
                    'premium_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ], ['policy_detail_id' => $policydetailid]);



                $this->db->delete('policy_age_limit', ['policy_detail_id' => $policydetailid]);
                foreach ($ageLimit as $key => $value) {

                    $this->db->insert('policy_age_limit', [
                        'policy_detail_id' => $policydetailid,
                        'relation_id' => $key,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => $value->premium,
                        'employee_contri' => $value->premiumEmployeeContri,
                        'employer_contri' => $value->premiumEmployerContri,
                    ]);
                }
            }
        } else if ($policySubType == 2 || $policySubType == 3) {

            $ageLimit = json_decode($ageLimit);

            if ($companySubTypePolicy == "designation") {
                $designation = json_decode($designation);
                $designationId = json_decode($designationId);
                $designationId1 = implode(',', $designationId);

                for ($i = 0; $i < count($designationId); ++$i) {
                    $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                        ->from('employee_policy_detail as epd')
                        ->where('policy_sub_type_id', $policySubType)
                        ->where('company_id', $company_id)
                        ->where('find_in_set("' . $designationId1[$i] . '",epd.applicable_for_designation_id)!=0')
                        ->where('start_date >=', date('Y-m-d', (strtotime($policyStartDate))))
                        ->where('end_date <=', date('Y-m-d', (strtotime($policyEndDate))))
                        ->get()->result_array();

                    if ($designationId[$i] == -1) {
                        $this->db->insert('master_designation', [
                            'designation_name' => ucwords(strtolower($designation[$i])),
                        ]);

                        $designationId[$i] = $this->db->insert_id();
                    }
                    $designation[$i] = $this->db->get_where('master_designation', array('designation_name =' => $designation[$i]))->result();
                }

                $data_des = [];
                foreach ($designation as $d) {
                    $designation = ((array) $d[0]);
                    array_push($data_des, $designation['master_desg_id']);
                }

                $designation = implode(',', $designation);
                $designationId = implode(',', $data_des);
                $si_NoOfTimes = json_decode($si_no_times);
                $si_parr = json_decode($si_parr);
                if ($appFor == 'allEmployee') {
                    $sum_insure_no = $si_no_times_des;
                    $premium_si = $sumPremium;
                } else {
                    $sum_insure_no = implode(',', $si_NoOfTimes);
                    $premium_si = implode(',', $si_parr);
                }
            }
            if (count($designation_diff) > 0) {
                return ['mgs' => 'Already this policy is assigned to this Designation'];
            } else {

                $arrUnMaryChild = [];
                $strUnMaryChild = 0;
                $unmarried_child_contri = 0;

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $premiumCalTypeValue = "";
                if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2) {
                    $premiumCalTypeValue = $premiumCalType;
                }
                // print_pre($_SESSION['emp_id']); exit;
                $this->db->update('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    //'policy_type_id' => $policyType,
                    // 'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    // 'tax' => trim($tax),
                    //'PremiumServiceTax' => trim($serviceTax),
                    'premiumCalType' => trim($premiumCalTypeValue),
                    'approval_id' => $this->session->userdata('emp_code'),
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    'applicable_for_designation_id' => $designationId,
                    // 'sum_insured' => $sumInsured,
                    // 'premium' => $sumPremium,
                    'sum_insured' => $sumInsured,
                    'premium' => $premium_si,
                    'gpa_no_of_times' => $sum_insure_no,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    //'tpa_type' => $tpaType,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Active',
                    'approval_id' => $this->session->userdata('emp_code'),
                    'premium_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ], ['policy_detail_id' => $policydetailid]);



                /*if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2) {
                    $this->UpdateGpa_permili_calculation($policydetailid);
                } */

                $this->db->delete('policy_age_limit', ['policy_detail_id' => $policydetailid]);
                foreach ($ageLimit as $key => $value) {

                    $this->db->insert('policy_age_limit', [
                        'policy_detail_id' => $policydetailid,
                        'relation_id' => $key,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => $value->premium,
                        'employee_contri' => $value->premiumEmployeeContri,
                        'employer_contri' => $value->premiumEmployerContri,
                    ]);
                }
            }
        } else if ($policySubType == 5 || $policySubType == 6) {

            $ageLimit = json_decode($ageLimit);

            if ($companySubTypePolicy == "designation") {
                $designation = json_decode($designation);
                $designationId = json_decode($designationId);
                $designationId1 = implode(',', $designationId);
                for ($i = 0; $i < count($designationId); ++$i) {
                    $designation_diff = $this->db->select('epd.applicable_for_designation_id, epd.policy_sub_type_id,epd.start_date, epd.end_date')
                        ->from('employee_policy_detail as epd')
                        ->where('policy_sub_type_id', $policySubType)
                        ->where('company_id', $company_id)
                        ->where('find_in_set("' . $designationId1[$i] . '",epd.applicable_for_designation_id)!=0')
                        ->where('start_date >=', date('Y-m-d', (strtotime($policyStartDate))))
                        ->where('end_date <=', date('Y-m-d', (strtotime($policyEndDate))))
                        ->get()->result_array();
                    if ($designationId[$i] == -1) {
                        $this->db->insert('master_designation', [
                            'designation_name' => ucwords(strtolower($designation[$i])),
                        ]);

                        $designationId[$i] = $this->db->insert_id();
                    }
                    $designation[$i] = $this->db->get_where('master_designation', array('designation_name =' => $designation[$i]))->result();
                }

                $data_des = [];
                foreach ($designation as $d) {
                    $designation = ((array) $d[0]);
                    array_push($data_des, $designation['master_desg_id']);
                }

                $designation = implode(',', $designation);
                $designationId = implode(',', $data_des);
            }

            $si_id = json_decode($si_id);
            $si_parr = json_decode($si_parr);
            $sum_insure = implode(',', $si_id);
            $premium_si = implode(',', $si_parr);

            $arrUnMaryChild = [];
            $strUnMaryChild = 0;
            $unmarried_child_contri = 0;
            if (count($designation_diff) > 0) {
                return ['mgs' => 'Already this policy is assigned to this Designation'];
            } else {

                if ($isunMaryChildCheck == 1) {
                    $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                    if ($isDaughterCheck == 1) {
                        array_push($arrUnMaryChild, 3);
                    }

                    if ($isSonCheck == 1) {
                        array_push($arrUnMaryChild, 2);
                    }

                    $strUnMaryChild = implode(',', $arrUnMaryChild);
                }

                $special_child_contri = 0;
                if ($isSpChildCheck == 1) {
                    $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
                }

                $this->db->Update('employee_policy_detail', [
                    'policy_no' => trim($policyNo),
                    'broker_id' => $_SESSION['emp_id'],
                    //'tax' => trim($tax),
                    //'PremiumServiceTax' => trim($serviceTax),
                    // 'policy_type_id' => $policyType,
                    // 'policy_sub_type_id' => $policySubType,
                    'insurer_id' => $masterInsurance,
                    'company_id' => $company_id,
                    'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                    'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                    'sales_manager_name' => trim($salesManager),
                    'broker_percent' => trim($brokerPer),
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sum_insured_type,
                    'special_child_check' => $isSpChildCheck,
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'applicable_for' => $appFor,
                    'applicable_for_designation_id' => $designationId,
                    // 'sum_insured' => $sumInsured,
                    // 'premium' => $sumPremium,
                    'approval_id' => $this->session->userdata('emp_code'),
                    'sum_insured' => $sum_insure,
                    'premium' => $premium_si,
                    'premium_paid' => $premium_cd_paid,
                    'cd_balance_threshold' => $cd_balance_thres,
                    'flex_allocate' => $flex_allocate,
                    'payroll_allocate' => $payroll_allocate,
                    'addition_premium' => $additionalPremium,
                    'marital_status' => $ismaritalStatus,
                    'status_wise_single_si' => $singleStatus_si,
                    'status_wise_single_pre' => $singleStatus_pre,
                    'status_wise_married_si' => $marriedStatus_si,
                    'status_wise_married_pre' => $marriedStatus_pre,
                    //'tpa_type' => $tpaType,
                    'hr_email' => $hr_email,
                    'hr_contact' => $hr_mobil_no,
                    'acc_manager_email' => $account_email,
                    'acc_manager_contact' => $acc_mobil_no,
                    'policy_status' => 'Active',
                    //'approval_id'=>$emp_code,
                    'premium_type' => $companySubTypePolicy,
                    'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                    'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
                ], ['policy_detail_id' => $policydetailid]);



                foreach ($ageLimit as $key => $value) {
                    $this->db->Update('policy_age_limit', [
                        'relation_id' => $key,
                        'min_age' => $value->min,
                        'max_age' => $value->max,
                        'premium' => $value->premium,
                        'employee_contri' => $value->premiumEmployeeContri,
                        'employer_contri' => $value->premiumEmployerContri,
                    ], ['policy_detail_id' => $policydetailid]);
                }
            }
        } else {

            $filename = $_FILES['filename']['tmp_name'];

            if (!$filename) {
                return ['mgs' => 'Please add 1 file to create policy'];
            }

            $this->db->Update('employee_policy_detail', [
                'policy_no' => trim($policyNo),
                'broker_id' => $_SESSION['emp_id'],
                //'policy_type_id' => $policyType,
                //'policy_sub_type_id' => $policySubType,
                'insurer_id' => $masterInsurance,
                'company_id' => $company_id,
                'start_date' => date('Y-m-d', strtotime($policyStartDate)),
                'end_date' => date('Y-m-d', strtotime($policyEndDate)),
                'sales_manager_name' => trim($salesManager),
                'broker_percent' => trim($brokerPer),
                'TPA_id' => $TPA_id,
                'sum_insured_type' => $sum_insured_type,
                'policy_status' => 'Active',
                'approval_id' => $this->session->userdata('emp_code'),
                'premium_type' => $companySubTypePolicy,
                'policy_enrollment_start_date' => date('Y-m-d', (strtotime($enrolWindowStartDate))),
                'policy_enrollment_end_date' => date('Y-m-d', (strtotime($enrolWindowEndDate))),
            ], ['policy_detail_id' => $policydetailid]);
        }

        // insert data in cd balance table for cd balance check
        $this->db->Update('cd_balance_transaction_log', [
            'amount' => $premium_cd_paid,
            'transaction_type' => 'cr',
            'created_date' => date('Y-m-d H:i:s'),
        ], ['policy_detail_id' => $policydetailid]);

        //after creation of policy get id
        $relationship_id = '0';

        $familyConstructArr = explode(',', $familyConstruct);
        $family_cons_relArr = explode(',', $family_cons_rel);

        if ($familyConstructArr[0] == 1) {

        } elseif ($familyConstructArr[0] > 3) {
            $relationship_id = '1,4,5,6,7';
        } else {
            $relationship_id .= ',1';
        }

        if ($familyConstructArr[1] > 0) {
            $relationship_id .= ',2,3';
        }
        $family_cons_rel = rtrim($family_cons_rel, ',');
        $this->db->Update('master_broker_ic_relationship', [
            // 'relationship_id' => $family_cons_rel,
            'relationship_id' => $relationship_id,
            'max_adult' => $familyConstructArr[0],
            'max_child' => $familyConstructArr[1],
            'twins_child_limit' => $twins_child_limit,
        ], ['policy_id' => $policydetailid]);

        if ($fileUploadTypes == "tpaFile") {

            $this->policyCreationUpdateExcel($policydetailid, $policySubType);
        }

        if ($fileUploadTypegtl == "gtliTopupFile") {
            $this->policyCreationUpdateExcel($policydetailid, $policySubType);
        }
        if ($policySubType == 1) {

            $this->policyCreationUpdateExcel($policydetailid, $policySubType);
        } else {

            $this->policyCreationUpdateExcel($policydetailid, $policySubType);
        }

        return ['mgs' => 'Policy Successfully created'];
    }

    function UpdateGpa_permili_calculation($policy_id) {
        extract($this->input->post(null, true));

        $this->load->library("excel");
        if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2 && $premiumCalType == 1 && $policy_id != "") {
            $filename = $_FILES['filename']['tmp_name'];
            $objPHPExcel = PHPExcel_IOFactory::load($filename);
            $this->db->delete('gpa_permili_calculation', ['policy_detail_id' => $policy_id]);
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                //$worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($col = 2; $col <= $highestRow; ++$col) {
                    $emp_age = $worksheet->getCellByColumnAndRow(0, $col);
                    $gpaRate = $worksheet->getCellByColumnAndRow(1, $col);

                    $this->db->insert('gpa_permili_calculation', [
                        'policy_detail_id' => $policy_id,
                        'emp_age' => $emp_age,
                        'gtli_rate' => $gpaRate
                    ]);
                }
            }
        } else {
            echo "error";
        }
    }

    function policyCreationUpdateExcel($policy_id, $policySubType) {
        extract($this->input->post(null, true));

        $this->load->library("excel");
        if ($fileUploadType || $premiumfilename) {
            $filename = $_FILES['filename']['tmp_name'];


            if (!$filename) {
                return ['mgs' => 'Please add 1 file to create policy'];
            }
            $objPHPExcel = PHPExcel_IOFactory::load($filename);

            if ($fileUploadType == 'byAge') {
                $this->db->delete('policy_creation_age', ['policy_id' => $policy_id]);
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/age_wise_xl.xls');

                        $this->db->insert('policy_creation_age', [
                            'policy_id' => $policy_id,
                            'policy_age' => $policyAge,
                            'sum_insured' => $sumInsured,
                            'premium' => $premium,
                            'employee_contri_percent' => $employeeContri,
                            'employer_contri_percent' => $employerContri,
                            'premium_tax' => $tax,
                            'premium_with_tax' => $premium_with_tax,
                            'file_path' => APPPATH . 'resources/policy/' . $policy_id . '/age_wise_xl.xls'
                        ]);
                        // echo "here";exit;
                    }
                }
            } else if ($fileUploadType == 'byMemberAge') {
                $this->db->delete('policy_creation_age', ['policy_id' => $policy_id]);
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);

                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/mem_age_wise_xl.xls');
                        $this->db->insert('policy_creation_age', [
                            'policy_id' => $policy_id,
                            'policy_age' => $policyAge,
                            'sum_insured' => $sumInsured,
                            'premium' => $premium,
                            'premium_tax' => $tax,
                            'premium_with_tax' => $premium_with_tax,
                            'employee_contri_percent' => $employeeContri,
                            'employer_contri_percent' => $employerContri,
                            'file_path' => APPPATH . 'resources/policy/' . $policy_id . '/mem_age_wise_xl.xls'
                        ]);
                    }
                }
            } else if ($fileUploadType == 'byDesignation') {

                $this->db->delete('policy_creation_designation', ['policy_id' => $policy_id]);
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10


                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policydesignation = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/designation_wise_xl.xls');
                            $designation = $this->db->where(['designation_name' => trim($policydesignation)])->get('master_designation')->row_array();
                            $designationId = $designation['master_desg_id'];

                            if ($designation == "") {
                                $this->db->insert('master_designation', [
                                    'designation_name' => ucwords(strtolower($policydesignation)),
                                ]);
                                $designationId = $this->db->insert_id();
                            }


                            $this->db->insert('policy_creation_designation', [
                                'policy_id' => $policy_id,
                                'policy_designation' => $designationId,
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'policy_desig_tax' => $tax,
                                'policy_desig_premiumWithtax' => $premium_with_tax,
                                'file_path' => APPPATH . 'resources/policy/' . $policy_id . '/designation_wise_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                if ($policySubType == 2 || $policySubType == 3) {
                    $this->db->delete('grade_permilli', ['policy_id' => $policy_id]);
                } else {
                    $this->db->delete('policy_creation_grade', ['policy_id' => $policy_id]);
                }
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyGrade = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);
                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $s_premium = $worksheet->getCellByColumnAndRow(3, $col);
                        $son_premium = $worksheet->getCellByColumnAndRow(4, $col);
                        $d_premium = $worksheet->getCellByColumnAndRow(5, $col);
                        $f_premium = $worksheet->getCellByColumnAndRow(6, $col);
                        $m_premium = $worksheet->getCellByColumnAndRow(7, $col);
                        $f_inlow_premium = $worksheet->getCellByColumnAndRow(8, $col);
                        $m_inlow_premium = $worksheet->getCellByColumnAndRow(9, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }

                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/grade_wise_xl.xls');

                        if ($policySubType == 2 || $policySubType == 3) {


                            $this->db->insert('grade_permilli', [
                                'policy_id' => $policy_id,
                                'grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'per_mili' => $premium,
                                'file_path' => APPPATH . 'resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        } else {
                            $this->db->insert('policy_creation_grade', [
                                'policy_id' => $policy_id,
                                'policy_grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'spouse_premium' => $s_premium,
                                'son_premium' => $son_premium,
                                'daughter_premium' => $d_premium,
                                'mother_premium' => $m_premium,
                                'father_premium' => $f_premium,
                                'mother_in_premium' => $m_inlow_premium,
                                'father_in_premium' => $f_inlow_premium,
                                'file_path' => APPPATH . 'resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        }
                    }
                }
            }



            if ($fileUploadTypegtl == 'gtliTopupFile') {

                $filenamegtl = $_FILES['filenamegtl']['tmp_name'];

                if (!$filenamegtl) {
                    return ['mgs' => 'Please add 1 file to create TPA policy'];
                }
                $objPHPExcel = PHPExcel_IOFactory::load($filenamegtl);
                $this->db->delete('gtli_topup_premium_calc', ['policy_detail_id' => $policy_id]);

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $tpa_arr = [];
                    for ($col = 2; $col <= $highestRow; ++$col) {

                        $emp_age = $worksheet->getCellByColumnAndRow(0, $col);
                        $gtli_rate = $worksheet->getCellByColumnAndRow(1, $col);
                        $ci_rate = $worksheet->getCellByColumnAndRow(2, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy' . $policy_id . '/gtli_wise_xl.xls');

                        $this->db->insert('gtli_topup_premium_calc', [
                            'emp_age' => $$emp_age,
                            'gtli_rate' => $gtli_rate,
                            'ci_rate' => $ci_rate,
                            'policy_detail_id' => $policy_id,
                        ]);
                    }
                }
            }
        }
    }


    public function get_payment_mode_data() {
        $data = $this->db
            ->select('*')
            ->from('master_payment_mode')
            ->get()
            ->result_array();

        return $data;
    }

    public function get_customer_search_data() {
        $data = $this->db
            ->select('id,search_by')
            ->from('master_customer_search')
            ->get()
            ->result_array();

        return $data;
    }

    public function update_policy_with_product() {

        extract($this->input->post(null, true));

        $data = $this->db
            ->select('*')
            ->from('product_master_with_subtype')
            ->where('product_name',$product_name)
            ->get()
            ->result_array();


        if(count($data) > 0 ){

            for($i = 0; $i < count($data); $i++){
                $this->db->update('employee_policy_detail', [
                    'policy_status' => 'Active',
                    'parent_policy_id'=> $data[$i]['policy_parent_id'],

                ], ['product_name' => $data[$i]['id']]);

            }
            return ['mgs' => 'Policy Successfully created'];
        }


    }


}
